<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Services\AntiCheatPolicyService;
use App\Services\AssessmentDiagnosticService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Services\GamificationService;

class StudentAssessmentController extends Controller
{
    private const DONE_STATUSES = ['submitted', 'late', 'graded'];

    /**
     * My Classes (DataSensei Updates 11): the classes the student is enrolled
     * in, each with a short summary of its assessments. Homework, quizzes,
     * and examinations are opened class by class.
     */
    public function index()
    {
        $studentId = (int) Auth::id();
        $classIds = $this->studentClassIds($studentId);

        $classes = \App\Models\ClassRoom::query()
            ->whereIn('id', $classIds)
            ->with('instructor:id,name')
            ->orderBy('is_archived')
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        $assessments = Assessment::query()
            ->with(['submissions' => fn ($q) => $q->where('student_id', $studentId)])
            ->whereIn('class_id', $classes->pluck('id'))
            ->whereIn('status', ['published', 'closed'])
            ->get()
            ->groupBy('class_id');

        $summaries = $classes->mapWithKeys(fn (\App\Models\ClassRoom $class) => [
            $class->id => collect($assessments->get($class->id, collect()))
                ->map(fn (Assessment $assessment) => $this->assessmentState($assessment, $assessment->submissions))
                ->countBy()
                ->all(),
        ])->all();

        return view('student.assessments.index', compact('classes', 'summaries'));
    }

    /**
     * One class's assessment area: available, upcoming, missing, late and
     * submitted work. Only a class the student is enrolled in can be opened.
     */
    public function classAssessments(\App\Models\ClassRoom $class)
    {
        $studentId = (int) Auth::id();
        abort_unless($this->studentClassIds($studentId)->contains((int) $class->id), 404);
        $class->loadMissing('instructor:id,name');

        $groups = ['available' => [], 'upcoming' => [], 'missing' => [], 'late' => [], 'submitted' => []];

        Assessment::query()
            ->with(['submissions' => fn ($q) => $q
                ->where('student_id', $studentId)
                ->orderByDesc('attempt_no')
                ->orderByDesc('created_at'),
            ])
            ->where('class_id', $class->id)
            ->whereIn('status', ['published', 'closed'])
            ->get()
            ->each(function (Assessment $assessment) use (&$groups): void {
                $submissions = $assessment->submissions;
                $state = $this->assessmentState($assessment, $submissions);
                $groups[$state][] = [
                    'assessment' => $assessment,
                    'done' => $submissions->first(fn (AssessmentSubmission $s) => in_array($s->status, self::DONE_STATUSES, true)),
                    'in_progress' => $submissions->firstWhere('status', 'in_progress'),
                ];
            });

        $byDue = fn (array $row) => $row['assessment']->due_at?->timestamp ?? PHP_INT_MAX;
        $groups['available'] = collect($groups['available'])->sortBy($byDue)->values()->all();
        $groups['upcoming'] = collect($groups['upcoming'])->sortBy(fn (array $row) => $row['assessment']->available_at?->timestamp ?? 0)->values()->all();
        $groups['missing'] = collect($groups['missing'])->sortBy($byDue)->values()->all();
        foreach (['late', 'submitted'] as $key) {
            $groups[$key] = collect($groups[$key])->sortByDesc(fn (array $row) => $row['done']?->submitted_at?->timestamp ?? 0)->values()->all();
        }

        return view('student.assessments.class', ['class' => $class, 'groups' => $groups]);
    }

    /**
     * Where an assessment sits for this student:
     *   upcoming   published, but it has not opened yet
     *   submitted  turned in on time
     *   late       turned in after the due date
     *   missing    not turned in and the due date has passed, or it closed
     *   available  open to start or continue
     */
    private function assessmentState(Assessment $assessment, $submissions): string
    {
        $done = collect($submissions)
            ->filter(fn (AssessmentSubmission $s) => in_array($s->status, self::DONE_STATUSES, true))
            ->sortByDesc(fn (AssessmentSubmission $s) => $s->submitted_at?->timestamp ?? 0)
            ->first();

        if ($done !== null) {
            $late = $done->status === 'late'
                || ($assessment->due_at !== null && $done->submitted_at !== null && $done->submitted_at->greaterThan($assessment->due_at));

            return $late ? 'late' : 'submitted';
        }

        if ($assessment->status === 'published' && $assessment->available_at !== null && $assessment->available_at->isFuture()) {
            return 'upcoming';
        }

        if ($assessment->status === 'closed' || ($assessment->due_at !== null && $assessment->due_at->isPast())) {
            return 'missing';
        }

        return 'available';
    }

    private function studentClassIds(int $studentId)
    {
        return DB::table('class_student')
            ->where('student_id', $studentId)
            ->pluck('class_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
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
            // A timed attempt ends at the due date at the latest, so starting
            // one after the due date would end it immediately.
            abort_if(
                $lockedAssessment->time_limit_minutes
                    && $lockedAssessment->due_at
                    && now()->greaterThanOrEqualTo($lockedAssessment->due_at),
                403,
                'The due date for this timed assessment has passed.'
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
                // Per-attempt secret for the anti-cheat contract.
                'anti_cheat_session_id' => Str::random(64),
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

        // Attempts that pre-date the protected-identity column are repaired
        // in place instead of failing.
        $antiCheatSessionId = DB::transaction(function () use ($assessment, $submission): string {
            $lockedSubmission = AssessmentSubmission::query()
                ->whereKey($submission->id)
                ->where('assessment_id', $assessment->id)
                ->where('student_id', Auth::id())
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->firstOrFail();

            if (trim((string) $lockedSubmission->anti_cheat_session_id) === '') {
                $lockedSubmission->anti_cheat_session_id = Str::random(64);
                $lockedSubmission->save();
            }

            return (string) $lockedSubmission->anti_cheat_session_id;
        }, 3);

        $assessment->load(['classRoom', 'questions.options']);
        $remainingSeconds = $this->remainingSeconds($assessment, $submission);
        [$attemptEndsAt, $attemptEndReason] = $this->attemptEnd($assessment, $submission);
        $draftAnswers = is_array($submission->draft_answers) ? $submission->draft_answers : [];
        $draftVersion = (int) ($submission->draft_version ?? 0);

        $antiCheatPolicy = app(AntiCheatPolicyService::class);
        $antiCheatSettings = $antiCheatPolicy->settingsForAssessment(Auth::user(), $assessment);
        // Authoritative blocked state and logical focus-loss count, so a
        // reload can neither unlock a blocked attempt nor reset its warnings.
        $antiCheatState = $antiCheatPolicy->attemptIntegrityState(
            Auth::user(),
            $assessment,
            $submission,
            $antiCheatSettings
        );

        return view('student.assessments.take', compact(
            'assessment',
            'submission',
            'remainingSeconds',
            'attemptEndsAt',
            'attemptEndReason',
            'draftAnswers',
            'draftVersion',
            'antiCheatSettings',
            'antiCheatState',
            'antiCheatSessionId'
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

    public function submit(Request $request, Assessment $assessment, AssessmentSubmission $submission, AssessmentDiagnosticService $diagnostics, StudentNotificationService $notifications)
    {
        $this->authorizeSubmission($assessment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
            '_anti_cheat_session_id' => ['nullable', 'string', 'max:120'],
            '_anti_cheat_finalize' => ['nullable', 'boolean'],
        ]);

        $answers = $validated['answers'] ?? [];
        $finalizeLockedAttempt = (bool) ($validated['_anti_cheat_finalize'] ?? false);

        $result = DB::transaction(function () use ($assessment, $submission, $answers, $validated, $finalizeLockedAttempt) {
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

            $savedAnswers = $this->filterKnownAnswers(
                $lockedAssessment,
                is_array($lockedSubmission->draft_answers)
                    ? $lockedSubmission->draft_answers
                    : []
            );

            // Answers freeze at the server deadline. After expiry only the last
            // draft the server accepted before the deadline is graded; anything
            // posted with this late request is ignored (autosave refuses late
            // snapshots as well), so a late change can never alter the score.
            $submittedAnswers = $timedOut
                ? $savedAnswers
                : array_replace(
                    $savedAnswers,
                    $this->filterKnownAnswers($lockedAssessment, $answers)
                );

            if (! $timedOut) {
                $this->validateRequiredAnswers($lockedAssessment, $submittedAnswers);
            }

            // The integrity decision is independent of the timer: an attempt
            // that is blocked, or that cannot prove its protected identity,
            // never turns into an ordinary graded attempt by waiting.
            $integrity = $this->integrityOutcome(
                $lockedAssessment,
                $lockedSubmission,
                $validated['_anti_cheat_session_id'] ?? null,
                $timedOut,
                $finalizeLockedAttempt
            );
            $held = $integrity['status'] !== AssessmentSubmission::INTEGRITY_CLEAR;

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
                        // A held attempt keeps the learner's work and its
                        // correctness for the instructor, but carries no
                        // credit until it is released.
                        'points_awarded' => $held ? 0 : $points,
                    ]
                );
            }

            $isLate = $lockedAssessment->due_at && now()->greaterThan($lockedAssessment->due_at);
            $status = $held ? 'submitted' : ($isLate ? 'late' : ($requiresManualReview ? 'submitted' : 'graded'));

            $completedAt = now();
            $lockedSubmission->update([
                'status' => $status,
                'score' => $held ? 0 : $score,
                'provisional_score' => $held ? $score : null,
                'integrity_status' => $integrity['status'],
                'integrity_reason' => $integrity['reason'],
                'total_points' => $lockedAssessment->total_points,
                'submitted_at' => $completedAt,
                'graded_at' => ($held || $requiresManualReview) ? null : $completedAt,
                'draft_answers' => $submittedAnswers,
                'draft_version' => ((int) ($lockedSubmission->draft_version ?? 0)) + 1,
                'draft_saved_at' => $completedAt,
                'timed_out_at' => $timedOut ? $completedAt : null,
            ]);

            if ($integrity['record_client_lock']) {
                $this->recordLockedAttemptFinalized($lockedAssessment, $lockedSubmission);
            }

            return [
                'submission' => $lockedSubmission,
                'timed_out' => $timedOut,
                'held' => $held,
            ];
        }, 3);

        if (! $result) {
            return redirect()->route('student.assessments.result', [$assessment, $submission]);
        }

        $submission = $result['submission']->fresh();

        // A graded attempt may complete a class certificate's requirement
        // (DataSensei Updates 13); checked on the server after the commit.
        app(\App\Services\CertificateService::class)->afterProgress(Auth::user());
        $timedOut = $result['timed_out'];

        if ($result['held']) {
            // No diagnostics refresh, XP or "graded" notice: nothing was credited.
            $notifications->send(
                Auth::user(),
                'assessment_held_for_review',
                'Assessment attempt held for review',
                'Your attempt for “' . $assessment->title . '” was saved, but it is held for instructor review and has no credit yet.',
                route('student.assessments.result', [$assessment, $submission]),
                ['assessment_id' => $assessment->id, 'submission_id' => $submission->id],
                'assessment-held:' . $submission->id
            );

            return redirect()
                ->route('student.assessments.result', [$assessment, $submission])
                ->with('error', 'Your saved answers were submitted, but this attempt is held for instructor review and has no credit yet.');
        }

        $diagnostics->refresh($submission);

        // This request finalized the attempt, so it counts once toward
        // missions, streaks and achievements.
        try {
            app(GamificationService::class)->recordAssessmentSubmission(Auth::user(), (int) $submission->id);
        } catch (\Throwable $exception) {
            report($exception);
        }

        if ($submission->graded_at) {
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
            $selected = ($raw === null || trim((string) $raw) === '') ? null : (int) $raw;
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
            // Only blank lines are dropped. A bare filter() would also drop "0".
            ->filter(fn ($answer) => $answer !== '');

        $normalized = mb_strtolower($answerText);
        $isCorrect = $normalized !== '' && $accepted->contains($normalized);

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
        [$endsAt] = $this->attemptEnd($assessment, $submission);
        if ($endsAt === null) {
            return null;
        }

        return max(0, (int) ceil(now()->diffInSeconds($endsAt, false)));
    }

    /**
     * When this attempt hard-ends, and why (DataSensei Updates 11).
     *
     * With a time limit, the attempt ends when the limit runs out, or at the
     * due date if that arrives first; the earlier moment wins and the server
     * enforces it. Untimed work (homework) has no hard end: it can still be
     * turned in after the due date and is then marked late.
     *
     * @return array{0: ?\Illuminate\Support\Carbon, 1: ?string} [endsAt, 'time_limit'|'due_date'|null]
     */
    private function attemptEnd(Assessment $assessment, AssessmentSubmission $submission): array
    {
        if (!$assessment->time_limit_minutes || !$submission->started_at) {
            return [null, null];
        }

        $limitEndsAt = $submission->started_at
            ->copy()
            ->addMinutes((int) $assessment->time_limit_minutes);

        if ($assessment->due_at && $assessment->due_at->lessThan($limitEndsAt)) {
            return [$assessment->due_at->copy(), 'due_date'];
        }

        return [$limitEndsAt, 'time_limit'];
    }

    /**
     * Decide whether the attempt may be credited (ported from the retired
     * assignment flow; DataSensei Updates 11).
     *
     * @return array{status: string, reason: ?string, record_client_lock: bool}
     */
    private function integrityOutcome(
        Assessment $assessment,
        AssessmentSubmission $submission,
        ?string $postedSessionId,
        bool $timedOut,
        bool $finalizeLockedAttempt
    ): array {
        $policyService = app(AntiCheatPolicyService::class);
        $state = $policyService->attemptIntegrityState(Auth::user(), $assessment, $submission);

        if (! $state['enabled']) {
            return [
                'status' => AssessmentSubmission::INTEGRITY_CLEAR,
                'reason' => null,
                'record_client_lock' => false,
            ];
        }

        $identityValid = $policyService->attemptIdentityMatches($submission, $postedSessionId);

        // Before the deadline the learner can still reload the page and send
        // the correct identity, so the request is simply rejected.
        if (! $identityValid && ! $timedOut) {
            throw ValidationException::withMessages([
                'anti_cheat' => AntiCheatPolicyService::INVALID_IDENTITY_MESSAGE,
            ]);
        }

        if ($state['blocked']) {
            // An ordinary manual submit of a blocked attempt is still refused.
            // It is finalized only by the deadline or by the explicit
            // "Submit for review" action of the locked page.
            if (! $timedOut && ! $finalizeLockedAttempt) {
                throw ValidationException::withMessages(['anti_cheat' => $state['reason']]);
            }

            return [
                'status' => AssessmentSubmission::INTEGRITY_BLOCKED,
                'reason' => Str::limit((string) $state['reason'], 250, ''),
                'record_client_lock' => false,
            ];
        }

        if (! $identityValid) {
            return [
                'status' => AssessmentSubmission::INTEGRITY_REVIEW_REQUIRED,
                'reason' => 'The attempt was finalized after the deadline without a valid protected attempt identity.',
                'record_client_lock' => false,
            ];
        }

        if ($finalizeLockedAttempt) {
            // The browser locked itself but the violation event never reached
            // the server. Withholding the event must not produce a clean
            // graded result.
            return [
                'status' => AssessmentSubmission::INTEGRITY_REVIEW_REQUIRED,
                'reason' => 'The browser locked this attempt, but no blocking violation event reached the server.',
                'record_client_lock' => true,
            ];
        }

        return [
            'status' => AssessmentSubmission::INTEGRITY_CLEAR,
            'reason' => null,
            'record_client_lock' => false,
        ];
    }

    private function recordLockedAttemptFinalized(Assessment $assessment, AssessmentSubmission $submission): void
    {
        $sessionId = (string) $submission->anti_cheat_session_id;
        $hash = md5('locked-attempt-finalized:assessment:' . $submission->id);
        // Deterministic identifier: one row per attempt, however often asked.
        $eventUuid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-4' . substr($hash, 13, 3)
            . '-8' . substr($hash, 17, 3) . '-' . substr($hash, 20, 12);

        $alreadyRecorded = \App\Models\AntiCheatEvent::query()
            ->where('attempt_session_id', $sessionId)
            ->where('event_uuid', $eventUuid)
            ->exists();
        if ($alreadyRecorded) {
            return;
        }

        \App\Models\AntiCheatEvent::create([
            'user_id' => (int) $submission->student_id,
            'class_id' => (int) $assessment->class_id,
            'assessment_id' => (int) $assessment->id,
            'assessment_submission_id' => (int) $submission->id,
            'assessment_type' => 'assessment',
            'event_type' => \App\Support\AntiCheatEventContract::LOCKED_ATTEMPT_FINALIZED_EVENT,
            'severity' => \App\Support\AntiCheatEventContract::severityFor(\App\Support\AntiCheatEventContract::LOCKED_ATTEMPT_FINALIZED_EVENT),
            'attempt_session_id' => $sessionId,
            'event_uuid' => $eventUuid,
            'details' => ['source' => 'server', 'reason' => 'finalize_without_recorded_violation'],
            'occurred_at' => now(),
        ]);
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
