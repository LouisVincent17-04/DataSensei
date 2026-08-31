<?php

namespace App\Http\Controllers;

use App\Models\AssignmentBlankAnswer;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\ClassAssignment;
use App\Services\AntiCheatPolicyService;
use App\Services\GamificationService;
use App\Services\IloMasteryService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        // IMPORTANT: Use the actual pivot table directly.
        // This avoids the page becoming empty just because User::classesAsStudent()
        // was not copied/added yet.
        $classIds = $this->studentClassIds((int) $student->id);

        $baseForStudentClasses = ClassAssignment::whereIn('class_id', $classIds);

        $studentAssignmentStats = [
            'enrolled_classes' => $classIds->count(),
            'all_class_assignments' => (clone $baseForStudentClasses)->count(),
            'published_visible' => (clone $baseForStudentClasses)->visibleToStudents()->count(),
            'draft' => (clone $baseForStudentClasses)->where('status', 'draft')->count(),
            'future' => (clone $baseForStudentClasses)
                ->where('status', 'published')
                ->whereNotNull('available_at')
                ->where('available_at', '>', now())
                ->count(),
        ];

        $query = ClassAssignment::with([
                'classRoom',
                'libraryItem',
                'submissions' => fn ($q) => $q
                    ->where('student_id', $student->id)
                    ->orderByDesc('attempt_no')
                    ->orderByDesc('created_at'),
            ])
            ->whereIn('class_id', $classIds)
            ->visibleToStudents()
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->latest('created_at');

        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'pending') {
                $query->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['submitted', 'late', 'graded']);
                });
            }

            if ($status === 'submitted') {
                $query->whereHas('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['submitted', 'late', 'graded']);
                });
            }
        }

        $assignments = $query->paginate(10)->withQueryString();

        return view('student.assignments.index', compact('assignments', 'studentAssignmentStats'));
    }

    /**
     * Display the authenticated student's assignment-attempt history.
     *
     * This is intentionally separate from the assignments page. The assignments
     * page lists work that the student can start or continue, while this page
     * lists every saved attempt and its result/status.
     */
    public function submissions(Request $request)
    {
        $studentId = (int) Auth::id();
        $completedStatuses = ['submitted', 'late', 'graded'];

        $baseQuery = AssignmentSubmission::query()
            ->where('student_id', $studentId);

        $completedQuery = (clone $baseQuery)
            ->whereIn('status', $completedStatuses);

        $earnedPoints = (int) (clone $completedQuery)->sum('score');
        $possiblePoints = (int) (clone $completedQuery)->sum('total_points');

        $submissionStats = [
            'all_attempts' => (clone $baseQuery)->count(),
            'completed' => (clone $completedQuery)->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'graded' => (clone $baseQuery)->where('status', 'graded')->count(),
            'late' => (clone $baseQuery)->where('status', 'late')->count(),
            'average_percentage' => $possiblePoints > 0
                ? (int) round(($earnedPoints / $possiblePoints) * 100)
                : 0,
        ];

        $query = AssignmentSubmission::with([
                'classAssignment.classRoom',
                'classAssignment.libraryItem',
            ])
            ->where('student_id', $studentId);

        $status = (string) $request->input('status', '');

        if ($status === 'completed') {
            $query->whereIn('status', $completedStatuses);
        } elseif (in_array($status, ['in_progress', 'submitted', 'late', 'graded'], true)) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->whereHas('classAssignment', function ($assignmentQuery) use ($search) {
                $assignmentQuery
                    ->where('title', 'like', '%' . $search . '%')
                    ->orWhereHas('classRoom', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('libraryItem', function ($libraryQuery) use ($search) {
                        $libraryQuery
                            ->where('title', 'like', '%' . $search . '%')
                            ->orWhere('topic_title', 'like', '%' . $search . '%')
                            ->orWhere('assignment_code', 'like', '%' . $search . '%');
                    });
            });
        }

        $submissions = $query
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('student.submissions.index', compact('submissions', 'submissionStats'));
    }

    public function show(ClassAssignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);

        $assignment->load([
            'classRoom',
            'libraryItem.questions.options',
            'libraryItem.questions.blankAnswers',
            'submissions' => fn ($q) => $q->where('student_id', Auth::id())->orderByDesc('attempt_no'),
        ]);

        $latestSubmission = $assignment->submissions->first();

        return view('student.assignments.show', compact('assignment', 'latestSubmission'));
    }

    public function start(ClassAssignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);

        abort_unless($assignment->status === 'published', 403, 'This assignment is already closed.');

        $studentId = (int) Auth::id();

        // Lock the learner row so double-clicks and parallel requests cannot
        // create two attempts with the same attempt number.
        $attempt = DB::transaction(function () use ($assignment, $studentId): array {
            $lockedAssignment = ClassAssignment::whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            DB::table('users')->where('id', $studentId)->lockForUpdate()->first();

            $enrollment = DB::table('class_student')
                ->where('class_id', $lockedAssignment->class_id)
                ->where('student_id', $studentId)
                ->lockForUpdate()
                ->first();

            abort_unless($enrollment, 403, 'You are not enrolled in this assignment class.');
            abort_unless($lockedAssignment->status === 'published', 403, 'This assignment is closed.');
            abort_if(
                $lockedAssignment->available_at && now()->lessThan($lockedAssignment->available_at),
                403,
                'This assignment is not available yet.'
            );

            $lockedAssignment->load('libraryItem.questions');
            abort_unless($lockedAssignment->libraryItem, 422, 'This assignment no longer has a question source.');

            $latestSubmission = AssignmentSubmission::where('class_assignment_id', $assignment->id)
                ->where('student_id', $studentId)
                ->orderByDesc('attempt_no')
                ->first();

            if ($latestSubmission && $latestSubmission->status === 'in_progress') {
                return ['submission' => $latestSubmission, 'exhausted' => false];
            }

            $submittedAttempts = AssignmentSubmission::where('class_assignment_id', $assignment->id)
                ->where('student_id', $studentId)
                ->whereIn('status', ['submitted', 'late', 'graded'])
                ->count();

            if ($submittedAttempts >= max(1, (int) $lockedAssignment->max_attempts)) {
                return ['submission' => $latestSubmission, 'exhausted' => true];
            }

            $submission = AssignmentSubmission::create([
                'class_assignment_id' => $lockedAssignment->id,
                'student_id' => $studentId,
                'attempt_no' => ((int) ($latestSubmission?->attempt_no ?? 0)) + 1,
                'status' => 'in_progress',
                'score' => 0,
                'total_points' => (int) $lockedAssignment->libraryItem->questions->sum('points'),
                'anti_cheat_session_id' => Str::random(64),
                'started_at' => now(),
            ]);

            return ['submission' => $submission, 'exhausted' => false];
        }, 3);

        if ($attempt['exhausted']) {
            if ($attempt['submission']) {
                return redirect()
                    ->route('student.assignments.result', [$assignment, $attempt['submission']])
                    ->with('error', 'You have already used all attempts for this assignment.');
            }

            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'You have already used all attempts for this assignment.');
        }

        return redirect()->route('student.assignments.take', [$assignment, $attempt['submission']]);
    }

    public function take(ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        // Older in-progress rows may pre-date the protection-token migration.
        // Repair the row in place instead of exposing a technical migration error
        // or forcing the learner to lose the attempt.
        $antiCheatSessionId = DB::transaction(function () use ($assignment, $submission): string {
            $lockedSubmission = AssignmentSubmission::query()
                ->whereKey($submission->id)
                ->where('class_assignment_id', $assignment->id)
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

        $assignment->load(['classRoom', 'libraryItem.questions.options']);
        abort_unless($assignment->libraryItem, 422, 'This assignment no longer has a question source.');
        $remainingSeconds = $this->remainingSeconds($assignment, $submission);

        $antiCheatSettings = app(AntiCheatPolicyService::class)
            ->settingsForAssignment(Auth::user(), $assignment);

        return view('student.assignments.take', compact(
            'assignment',
            'submission',
            'antiCheatSettings',
            'antiCheatSessionId',
            'remainingSeconds'
        ));
    }

    public function submit(Request $request, ClassAssignment $assignment, AssignmentSubmission $submission, GamificationService $gamification, StudentNotificationService $notifications)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
            '_anti_cheat_session_id' => ['nullable', 'string', 'max:120'],
        ]);

        $answers = $validated['answers'] ?? [];

        $processedSubmission = DB::transaction(function () use ($assignment, $submission, $answers, $validated) {
            $lockedSubmission = AssignmentSubmission::whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $lockedSubmission->class_assignment_id === (int) $assignment->id
                && (int) $lockedSubmission->student_id === (int) Auth::id(),
                403
            );

            // A concurrent submit request may already have completed this row.
            if ($lockedSubmission->status !== 'in_progress') {
                return null;
            }

            $lockedAssignment = ClassAssignment::query()
                ->whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                in_array($lockedAssignment->status, ['published', 'closed'], true),
                403,
                'This assignment is not available.'
            );

            $enrollment = DB::table('class_student')
                ->where('class_id', $lockedAssignment->class_id)
                ->where('student_id', Auth::id())
                ->lockForUpdate()
                ->first();
            abort_unless($enrollment, 403, 'You are not enrolled in this assignment class.');

            $lockedAssignment->load([
                'libraryItem.questions.options',
                'libraryItem.questions.blankAnswers',
            ]);
            abort_unless($lockedAssignment->libraryItem, 422, 'This assignment no longer has a question source.');

            // The server clock is authoritative. Do not accept answers after
            // the configured deadline, even if a client-side timer is paused,
            // edited, or prevented from submitting automatically.
            $timedOut = $this->remainingSeconds($lockedAssignment, $lockedSubmission) === 0;

            if (! $timedOut) {
                $blockedReason = app(AntiCheatPolicyService::class)->assignmentSubmissionBlocked(
                    Auth::user(),
                    $lockedAssignment,
                    $lockedSubmission,
                    $validated['_anti_cheat_session_id'] ?? null
                );

                if ($blockedReason) {
                    throw ValidationException::withMessages(['anti_cheat' => $blockedReason]);
                }
            }

            $totalPoints = (int) $lockedAssignment->libraryItem->questions->sum('points');
            $submittedAnswers = $timedOut ? [] : $answers;
            $score = 0;

            foreach ($lockedAssignment->libraryItem->questions as $question) {
                $rawAnswer = $submittedAnswers[$question->id] ?? null;
                [$isCorrect, $selectedOptionId, $answerText] = $this->gradeQuestion($question, $rawAnswer);
                $pointsAwarded = $isCorrect ? (int) $question->points : 0;
                $score += $pointsAwarded;

                AssignmentSubmissionAnswer::updateOrCreate(
                    [
                        'assignment_submission_id' => $lockedSubmission->id,
                        'assignment_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $pointsAwarded,
                    ]
                );
            }

            $isLate = $lockedAssignment->due_at && now()->greaterThan($lockedAssignment->due_at);

            $lockedSubmission->update([
                'status' => $isLate ? 'late' : 'graded',
                'score' => $score,
                'total_points' => $totalPoints,
                'submitted_at' => now(),
                'graded_at' => now(),
            ]);

            return [
                'submission' => $lockedSubmission,
                'timed_out' => $timedOut,
            ];
        }, 3);

        if (!$processedSubmission) {
            return redirect()->route('student.assignments.result', [$assignment, $submission]);
        }

        $submission = $processedSubmission['submission']->fresh();
        $timedOut = (bool) $processedSubmission['timed_out'];

        app(IloMasteryService::class)->refreshForAssignmentSubmission($submission);

        $achievements = $gamification->awardForAssignmentSubmission(Auth::user(), $submission);

        $percentage = $submission->total_points > 0
            ? round(((float) $submission->score / (float) $submission->total_points) * 100, 1)
            : 0;
        $notifications->send(
            Auth::user(),
            'assignment_graded',
            'Assignment result available',
            'Your result for “' . $assignment->title . '” is available: ' . $percentage . '%.',
            route('student.assignments.result', [$assignment, $submission]),
            ['assignment_id' => $assignment->id, 'submission_id' => $submission->id, 'percentage' => $percentage],
            'assignment-graded:' . $submission->id . ':' . optional($submission->graded_at)->format('YmdHis')
        );

        $message = $timedOut
            ? 'Time expired. The assignment was submitted automatically.'
            : 'Assignment submitted successfully.';

        if (!empty($achievements)) {
            $badgeText = collect($achievements)
                ->map(fn ($badge) => $badge['name'] ?? 'Achievement')
                ->implode(', ');
            $message .= ' Achievements unlocked: ' . $badgeText . '.';
        }

        return redirect()
            ->route('student.assignments.result', [$assignment, $submission])
            ->with('success', $message);
    }

    public function result(ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission, allowSubmitted: true);

        $submission->load([
            'answers.question.options',
            'answers.question.blankAnswers',
            'answers.selectedOption',
        ]);

        $assignment->load(['classRoom', 'libraryItem.questions.options']);

        $resultBackRoute = route('student.assignments.index');
        $resultBackLabel = 'Back to Assignments';

        return view('student.assignments.result', compact(
            'assignment',
            'submission',
            'resultBackRoute',
            'resultBackLabel'
        ));
    }

    /**
     * Show a saved submission while preserving the Submissions navigation state.
     */
    public function submissionResult(AssignmentSubmission $submission)
    {
        abort_unless((int) $submission->student_id === (int) Auth::id(), 403);

        $submission->load([
            'classAssignment.classRoom',
            'classAssignment.libraryItem.questions.options',
            'answers.question.options',
            'answers.question.blankAnswers',
            'answers.selectedOption',
        ]);

        $assignment = $submission->classAssignment;
        abort_unless($assignment, 404);

        $this->authorizeStudentSubmission($assignment, $submission, allowSubmitted: true);

        $resultBackRoute = route('student.submissions.index');
        $resultBackLabel = 'Back to Submissions';

        return view('student.assignments.result', compact(
            'assignment',
            'submission',
            'resultBackRoute',
            'resultBackLabel'
        ));
    }

    private function authorizeStudentAssignment(ClassAssignment $assignment): void
    {
        abort_unless(in_array($assignment->status, ['published', 'closed'], true), 404);

        abort_if(
            $assignment->available_at && now()->lessThan($assignment->available_at),
            403,
            'This assignment is not available yet.'
        );

        $isEnrolled = DB::table('class_student')
            ->where('class_id', $assignment->class_id)
            ->where('student_id', Auth::id())
            ->exists();

        abort_unless($isEnrolled, 403, 'You are not enrolled in this assignment class.');
    }

    private function authorizeStudentSubmission(ClassAssignment $assignment, AssignmentSubmission $submission, bool $allowSubmitted = false): void
    {
        abort_unless(
            (int) $submission->class_assignment_id === (int) $assignment->id &&
            (int) $submission->student_id === (int) Auth::id(),
            403,
            'You are not allowed to access this submission.'
        );

        if ($allowSubmitted) {
            abort_unless(
                in_array($submission->status, ['submitted', 'late', 'graded'], true),
                403,
                'This attempt has not been submitted yet.'
            );

            // Ownership of a completed record is sufficient. A learner who is
            // later removed from a class must still be able to review their own
            // submission history.
            return;
        }

        $this->authorizeStudentAssignment($assignment);

        if ($submission->status !== 'in_progress') {
            throw ValidationException::withMessages([
                'submission' => 'This assignment attempt has already been submitted.',
            ]);
        }
    }

    private function gradeQuestion(AssignmentQuestion $question, mixed $rawAnswer): array
    {
        if ($question->question_type === 'mcq') {
            $selectedOptionId = $rawAnswer ? (int) $rawAnswer : null;
            $selectedOption = $selectedOptionId
                ? $question->options->firstWhere('id', $selectedOptionId)
                : null;

            return [
                (bool) ($selectedOption?->is_correct),
                $selectedOption?->id,
                null,
            ];
        }

        $answerText = trim((string) $rawAnswer);

        $acceptedAnswers = $question->relationLoaded('blankAnswers')
            ? $question->blankAnswers
            : AssignmentBlankAnswer::where('assignment_question_id', $question->id)->get();

        $isCorrect = $acceptedAnswers->contains(function (AssignmentBlankAnswer $accepted) use ($answerText) {
            $expected = trim((string) $accepted->answer_text);

            if ($accepted->is_case_sensitive) {
                return $answerText === $expected;
            }

            return mb_strtolower($answerText) === mb_strtolower($expected);
        });

        return [$isCorrect, null, $answerText];
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

    private function remainingSeconds(ClassAssignment $assignment, AssignmentSubmission $submission): ?int
    {
        $timeLimitMinutes = (int) ($assignment->libraryItem?->time_limit_minutes ?? 0);
        if ($timeLimitMinutes < 1 || ! $submission->started_at) {
            return null;
        }

        $expiresAt = $submission->started_at
            ->copy()
            ->addMinutes($timeLimitMinutes);

        return max(0, (int) ceil(now()->diffInSeconds($expiresAt, false)));
    }
}
