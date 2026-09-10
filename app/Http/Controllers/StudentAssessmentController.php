<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Services\AssessmentDiagnosticService;
use App\Services\IloMasteryService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentAssessmentController extends Controller
{
    public function index()
    {
        $classIds = DB::table('class_student')
            ->where('student_id', Auth::id())
            ->pluck('class_id');

        $assessments = Assessment::with([
                'classRoom',
                'submissions' => fn ($q) => $q
                    ->where('student_id', Auth::id())
                    ->latest('attempt_no'),
            ])
            ->where(function ($query) use ($classIds): void {
                $query->whereIn('class_id', $classIds)
                    ->orWhereHas('submissions', fn ($submissions) => $submissions
                        ->where('student_id', Auth::id())
                        ->whereIn('status', ['submitted', 'late', 'graded']));
            })
            ->visibleToStudents()
            ->latest('published_at')
            ->paginate(12);

        return view('student.assessments.index', compact('assessments'));
    }

    public function show(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment, allowCompletedHistory: true);
        $assessment->load(['classRoom', 'questions', 'submissions' => fn ($q) => $q->where('student_id', Auth::id())->latest('attempt_no')]);
        $latestSubmission = $assessment->submissions->first();
        $completedAttempts = $assessment->submissions
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->count();
        $maxAttempts = max(1, (int) $assessment->max_attempts);
        $attemptsRemaining = max(0, $maxAttempts - $completedAttempts);
        $canContinueAttempt = $latestSubmission?->status === 'in_progress';
        $canStartNewAttempt = $assessment->status === 'published' && $attemptsRemaining > 0;

        return view('student.assessments.show', compact(
            'assessment',
            'latestSubmission',
            'completedAttempts',
            'maxAttempts',
            'attemptsRemaining',
            'canContinueAttempt',
            'canStartNewAttempt'
        ));
    }

    public function start(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment, requirePublished: true);

        $studentId = (int) Auth::id();

        $attempt = DB::transaction(function () use ($assessment, $studentId): array {
            $lockedAssessment = Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            DB::table('users')->where('id', $studentId)->lockForUpdate()->first();

            $enrollment = DB::table('class_student')
                ->where('class_id', $lockedAssessment->class_id)
                ->where('student_id', $studentId)
                ->lockForUpdate()
                ->first();

            abort_unless($enrollment, 403, 'You are not enrolled in this assessment class.');
            abort_unless($lockedAssessment->status === 'published', 403, 'This assessment is closed.');
            abort_if(
                $lockedAssessment->available_at && now()->lessThan($lockedAssessment->available_at),
                403,
                'This assessment is not available yet.'
            );

            $latest = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('student_id', $studentId)
                ->latest('attempt_no')
                ->first();

            if ($latest && $latest->status === 'in_progress') {
                return ['submission' => $latest, 'exhausted' => false];
            }

            $attempts = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('student_id', $studentId)
                ->whereIn('status', ['submitted', 'late', 'graded'])
                ->count();

            if ($attempts >= max(1, (int) $lockedAssessment->max_attempts)) {
                return ['submission' => $latest, 'exhausted' => true];
            }

            $submission = AssessmentSubmission::create([
                'assessment_id' => $lockedAssessment->id,
                'student_id' => $studentId,
                'attempt_no' => ((int) ($latest?->attempt_no ?? 0)) + 1,
                'status' => 'in_progress',
                'score' => 0,
                'total_points' => $lockedAssessment->total_points,
                'started_at' => now(),
            ]);

            return ['submission' => $submission, 'exhausted' => false];
        }, 3);

        if ($attempt['exhausted']) {
            return back()->withErrors(['assessment' => 'You have already used all allowed attempts.']);
        }

        return redirect()->route('student.assessments.take', [$assessment, $attempt['submission']]);
    }

    public function take(Assessment $assessment, AssessmentSubmission $submission)
    {
        $this->authorizeSubmission($assessment, $submission);

        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.assessments.result', [$assessment, $submission]);
        }

        $assessment->load(['classRoom', 'questions.options']);
        $remainingSeconds = $this->remainingSeconds($assessment, $submission);
        $draftAnswers = is_array($submission->draft_answers) ? $submission->draft_answers : [];
        $draftVersion = (int) ($submission->draft_version ?? 0);

        return view('student.assessments.take', compact(
            'assessment',
            'submission',
            'remainingSeconds',
            'draftAnswers',
            'draftVersion'
        ));
    }

    public function autosave(Request $request, Assessment $assessment, AssessmentSubmission $submission)
    {
        $this->authorizeSubmission($assessment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
            'client_version' => ['required', 'integer', 'min:1', 'max:9223372036854775807'],
        ]);

        $result = DB::transaction(function () use ($assessment, $submission, $validated): array {
            $lockedSubmission = AssessmentSubmission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $lockedSubmission->assessment_id === (int) $assessment->id
                && (int) $lockedSubmission->student_id === (int) Auth::id(),
                403
            );

            if ($lockedSubmission->status !== 'in_progress') {
                return [
                    'saved' => false,
                    'completed' => true,
                    'current_version' => (int) ($lockedSubmission->draft_version ?? 0),
                ];
            }

            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                in_array($lockedAssessment->status, ['published', 'closed'], true),
                403,
                'This assessment is not available.'
            );

            $lockedAssessment->load('questions');
            if ($this->remainingSeconds($lockedAssessment, $lockedSubmission) === 0) {
                return [
                    'saved' => false,
                    'expired' => true,
                    'current_version' => (int) ($lockedSubmission->draft_version ?? 0),
                ];
            }

            $clientVersion = (int) $validated['client_version'];
            $currentVersion = (int) ($lockedSubmission->draft_version ?? 0);
            if ($clientVersion <= $currentVersion) {
                return [
                    'saved' => false,
                    'stale' => true,
                    'current_version' => $currentVersion,
                    'saved_at' => optional($lockedSubmission->draft_saved_at)->toIso8601String(),
                ];
            }

            $savedAt = now();
            $lockedSubmission->update([
                'draft_answers' => $this->filterKnownAnswers(
                    $lockedAssessment,
                    $validated['answers'] ?? []
                ),
                'draft_version' => $clientVersion,
                'draft_saved_at' => $savedAt,
            ]);

            return [
                'saved' => true,
                'current_version' => $clientVersion,
                'saved_at' => $savedAt->toIso8601String(),
            ];
        }, 3);

        $status = ($result['expired'] ?? false) || ($result['completed'] ?? false)
            ? 409
            : 200;

        return response()->json($result, $status);
    }

    public function submit(Request $request, Assessment $assessment, AssessmentSubmission $submission, AssessmentDiagnosticService $diagnostics, IloMasteryService $iloMastery, StudentNotificationService $notifications)
    {
        $this->authorizeSubmission($assessment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
        ]);

        $answers = $validated['answers'] ?? [];

        $result = DB::transaction(function () use ($assessment, $submission, $answers) {
            $lockedSubmission = AssessmentSubmission::whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $lockedSubmission->assessment_id === (int) $assessment->id
                && (int) $lockedSubmission->student_id === (int) Auth::id(),
                403
            );

            if ($lockedSubmission->status !== 'in_progress') {
                return null;
            }

            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                in_array($lockedAssessment->status, ['published', 'closed'], true),
                403,
                'This assessment is not available.'
            );

            $enrollment = DB::table('class_student')
                ->where('class_id', $lockedAssessment->class_id)
                ->where('student_id', Auth::id())
                ->lockForUpdate()
                ->first();
            abort_unless($enrollment, 403, 'You are not enrolled in this assessment class.');

            $lockedAssessment->load('questions.options');
            // The saved start time and server clock are authoritative. Client
            // JavaScript cannot extend the assessment by delaying submission.
            $timedOut = $this->remainingSeconds($lockedAssessment, $lockedSubmission) === 0;

            $submittedAnswers = array_replace(
                $this->filterKnownAnswers(
                    $lockedAssessment,
                    is_array($lockedSubmission->draft_answers)
                        ? $lockedSubmission->draft_answers
                        : []
                ),
                $this->filterKnownAnswers($lockedAssessment, $answers)
            );

            if (! $timedOut) {
                $this->validateRequiredAnswers($lockedAssessment, $submittedAnswers);
            }

            $score = 0.0;
            $requiresManualReview = false;

            foreach ($lockedAssessment->questions as $question) {
                $raw = $submittedAnswers[$question->id] ?? null;
                [$selectedOptionId, $answerText, $isCorrect, $points] = $this->grade($question, $raw);

                $requiresManualReview = $requiresManualReview || $isCorrect === null;

                $score += $points;

                AssessmentAnswer::updateOrCreate(
                    [
                        'assessment_submission_id' => $lockedSubmission->id,
                        'assessment_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $points,
                    ]
                );
            }

            $isLate = $lockedAssessment->due_at && now()->greaterThan($lockedAssessment->due_at);
            $status = $isLate ? 'late' : ($requiresManualReview ? 'submitted' : 'graded');

            $completedAt = now();
            $lockedSubmission->update([
                'status' => $status,
                'score' => $score,
                'total_points' => $lockedAssessment->total_points,
                'submitted_at' => $completedAt,
                'graded_at' => $requiresManualReview ? null : $completedAt,
                'draft_answers' => $submittedAnswers,
                'draft_version' => ((int) ($lockedSubmission->draft_version ?? 0)) + 1,
                'draft_saved_at' => $completedAt,
                'timed_out_at' => $timedOut ? $completedAt : null,
            ]);

            return [
                'submission' => $lockedSubmission,
                'timed_out' => $timedOut,
            ];
        }, 3);

        if (! $result) {
            return redirect()->route('student.assessments.result', [$assessment, $submission]);
        }

        $submission = $result['submission']->fresh();
        $timedOut = $result['timed_out'];
        $diagnostics->refresh($submission);

        if ($submission->graded_at) {
            $iloMastery->refreshForAssessmentSubmission($submission);
            $percentage = $submission->total_points > 0
                ? round(((float) $submission->score / (float) $submission->total_points) * 100, 1)
                : 0;
            $notifications->send(
                Auth::user(),
                'assessment_graded',
                'Assessment result available',
                'Your result for “' . $assessment->title . '” is available: ' . $percentage . '%.',
                route('student.assessments.result', [$assessment, $submission]),
                ['assessment_id' => $assessment->id, 'submission_id' => $submission->id, 'percentage' => $percentage],
                'assessment-graded:' . $submission->id . ':' . optional($submission->graded_at)->format('YmdHis')
            );
        }

        return redirect()
            ->route('student.assessments.result', [$assessment, $submission])
            ->with('success', $timedOut
                ? 'Time expired. Your saved answers were submitted and graded.'
                : ($submission->graded_at
                    ? 'Assessment submitted and graded.'
                    : 'Assessment submitted. Essay items are waiting for instructor review.'));
    }

    public function result(Assessment $assessment, AssessmentSubmission $submission)
    {
        $this->authorizeSubmission($assessment, $submission, allowCompleted: true);
        $assessment->load(['classRoom', 'questions.options']);
        $submission->load(['answers.question.options', 'answers.selectedOption']);

        return view('student.assessments.result', compact('assessment', 'submission'));
    }

    private function grade(AssessmentQuestion $question, mixed $raw): array
    {
        if ($question->question_type === 'multiple_choice') {
            $selected = $raw ? (int) $raw : null;
            $selectedOption = $selected
                ? $question->options->firstWhere('id', $selected)
                : null;
            $isCorrect = (bool) ($selectedOption?->is_correct);

            return [$selectedOption?->id, null, $isCorrect, $isCorrect ? (float) $question->points : 0.0];
        }

        $answerText = trim((string) $raw);

        if ($question->question_type === 'essay') {
            return [null, $answerText, $answerText === '' ? false : null, 0.0];
        }

        $accepted = collect(preg_split('/\r\n|\r|\n/', (string) $question->correct_answer))
            ->map(fn ($answer) => mb_strtolower(trim($answer)))
            ->filter();

        $normalized = mb_strtolower($answerText);
        $isCorrect = $accepted->contains($normalized);

        return [null, $answerText, $isCorrect, $isCorrect ? (float) $question->points : 0.0];
    }

    private function authorizeAssessment(
        Assessment $assessment,
        bool $requirePublished = false,
        bool $allowCompletedHistory = false,
    ): void
    {
        abort_unless(in_array($assessment->status, ['published', 'closed'], true), 404);
        if ($requirePublished) {
            abort_unless($assessment->status === 'published', 403, 'This assessment is closed.');
        }

        abort_if(
            $assessment->available_at && now()->lessThan($assessment->available_at),
            403,
            'This assessment is not available yet.'
        );

        $isEnrolled = DB::table('class_student')
            ->where('class_id', $assessment->class_id)
            ->where('student_id', Auth::id())
            ->exists();

        if (! $isEnrolled && $allowCompletedHistory) {
            $hasCompletedHistory = $assessment->submissions()
                ->where('student_id', Auth::id())
                ->whereIn('status', ['submitted', 'late', 'graded'])
                ->exists();

            abort_unless($hasCompletedHistory, 403, 'You are not enrolled in this assessment class.');

            return;
        }

        abort_unless($isEnrolled, 403, 'You are not enrolled in this assessment class.');
    }

    private function authorizeSubmission(Assessment $assessment, AssessmentSubmission $submission, bool $allowCompleted = false): void
    {
        abort_unless(
            (int) $submission->assessment_id === (int) $assessment->id
            && (int) $submission->student_id === (int) Auth::id(),
            403
        );

        if ($allowCompleted) {
            abort_unless(
                in_array($submission->status, ['submitted', 'late', 'graded'], true),
                403,
                'This attempt has not been submitted yet.'
            );

            return;
        }

        $this->authorizeAssessment($assessment);

        if ($submission->status !== 'in_progress') {
            throw ValidationException::withMessages([
                'submission' => 'This assessment attempt has already been submitted.',
            ]);
        }
    }

    private function remainingSeconds(Assessment $assessment, AssessmentSubmission $submission): ?int
    {
        if (!$assessment->time_limit_minutes || !$submission->started_at) {
            return null;
        }

        $expiresAt = $submission->started_at
            ->copy()
            ->addMinutes((int) $assessment->time_limit_minutes);

        return max(0, (int) ceil(now()->diffInSeconds($expiresAt, false)));
    }

    private function validateRequiredAnswers(Assessment $assessment, array $answers): void
    {
        $errors = [];

        foreach ($assessment->questions as $question) {
            $raw = $answers[$question->id] ?? null;
            $isBlank = $raw === null || trim((string) $raw) === '';

            if ($question->is_required && $isBlank) {
                $errors['answers.' . $question->id] = 'Item ' . $question->item_number . ' is required.';
                continue;
            }

            if ($question->question_type === 'multiple_choice' && !$isBlank
                && !$question->options->contains('id', (int) $raw)) {
                $errors['answers.' . $question->id] = 'Item ' . $question->item_number . ' contains an invalid choice.';
            }

            if ($question->question_type === 'true_false' && !$isBlank
                && !in_array(mb_strtolower(trim((string) $raw)), ['true', 'false'], true)) {
                $errors['answers.' . $question->id] = 'Item ' . $question->item_number . ' contains an invalid answer.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    private function filterKnownAnswers(Assessment $assessment, array $answers): array
    {
        $allowedQuestionIds = $assessment->questions
            ->pluck('id')
            ->mapWithKeys(fn ($id) => [(string) $id => true])
            ->all();
        $filtered = [];

        foreach ($answers as $questionId => $answer) {
            $normalizedId = (string) $questionId;
            if (! isset($allowedQuestionIds[$normalizedId])) {
                continue;
            }

            $filtered[$normalizedId] = $answer === null ? '' : (string) $answer;
        }

        return $filtered;
    }
}
