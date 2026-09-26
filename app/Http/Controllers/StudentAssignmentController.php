<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use App\Models\AssignmentBlankAnswer;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\ClassAssignment;
use App\Models\ClassChallengeAssignment;
use App\Services\AntiCheatPolicyService;
use App\Services\GamificationService;
use App\Services\IloMasteryService;
use App\Services\StudentNotificationService;
use App\Support\AntiCheatEventContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudentAssignmentController extends Controller
{
    /**
     * Explicit network grace after the server deadline during which posted
     * answers are still accepted. Zero: the deadline is exact. The take page
     * flushes a final autosave shortly before expiry instead.
     */
    private const SUBMISSION_GRACE_SECONDS = 0;

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

        // Challenges an instructor gave to one of the student's active
        // classes: published, inside their window, and still available.
        // They are taken on the University Student challenge map.
        $challengeAssignments = ClassChallengeAssignment::with(['challenge', 'class'])
            ->whereIn('class_id', $classIds)
            ->whereHas('class', fn ($q) => $q->active())
            ->whereHas('challenge', fn ($q) => $q->where('is_active', true))
            ->openNow()
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->get();

        return view('student.assignments.index', compact('assignments', 'studentAssignmentStats', 'challengeAssignments'));
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
        $draftAnswers = is_array($submission->draft_answers) ? $submission->draft_answers : [];
        $draftVersion = (int) ($submission->draft_version ?? 0);

        $antiCheatPolicy = app(AntiCheatPolicyService::class);
        $antiCheatSettings = $antiCheatPolicy->settingsForAssignment(Auth::user(), $assignment);

        // Authoritative blocked state and logical focus-loss count, so a
        // reload can neither unlock a blocked attempt nor reset its warnings.
        $antiCheatState = $antiCheatPolicy->attemptIntegrityState(
            Auth::user(),
            $assignment,
            $submission,
            $antiCheatSettings
        );

        return view('student.assignments.take', compact(
            'assignment',
            'submission',
            'antiCheatSettings',
            'antiCheatState',
            'antiCheatSessionId',
            'remainingSeconds',
            'draftAnswers',
            'draftVersion'
        ));
    }

    public function autosave(Request $request, ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
            'client_version' => ['required', 'integer', 'min:1', 'max:9223372036854775807'],
        ]);

        $result = DB::transaction(function () use ($assignment, $submission, $validated): array {
            $lockedSubmission = AssignmentSubmission::query()
                ->whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                (int) $lockedSubmission->class_assignment_id === (int) $assignment->id
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

            $lockedAssignment = ClassAssignment::query()
                ->whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                in_array($lockedAssignment->status, ['published', 'closed'], true),
                403,
                'This assignment is not available.'
            );

            $lockedAssignment->load('libraryItem.questions');
            abort_unless(
                $lockedAssignment->libraryItem,
                422,
                'This assignment no longer has a question source.'
            );

            if ($this->remainingSeconds($lockedAssignment, $lockedSubmission) === 0) {
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
                    $lockedAssignment,
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

    public function submit(Request $request, ClassAssignment $assignment, AssignmentSubmission $submission, GamificationService $gamification, StudentNotificationService $notifications)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        $validated = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['nullable', 'string', 'max:30000'],
            '_anti_cheat_session_id' => ['nullable', 'string', 'max:120'],
            '_anti_cheat_finalize' => ['nullable', 'boolean'],
        ]);

        $answers = $validated['answers'] ?? [];
        $finalizeLockedAttempt = (bool) ($validated['_anti_cheat_finalize'] ?? false);

        $processedSubmission = DB::transaction(function () use ($assignment, $submission, $answers, $validated, $finalizeLockedAttempt) {
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

            // The server clock is authoritative. Once the deadline (plus the
            // explicit grace below) has passed, the answers posted with this
            // request are ignored completely: only the draft the server stored
            // before the deadline is graded. Autosave refuses new snapshots
            // after expiry, so that draft cannot change any more either.
            $timedOut = $this->deadlinePassed($lockedAssignment, $lockedSubmission);

            $savedDraft = $this->filterKnownAnswers(
                $lockedAssignment,
                is_array($lockedSubmission->draft_answers)
                    ? $lockedSubmission->draft_answers
                    : []
            );

            $submittedAnswers = $timedOut
                ? $savedDraft
                : array_replace($savedDraft, $this->filterKnownAnswers($lockedAssignment, $answers));

            // The integrity decision is independent of the timer: an attempt
            // that is blocked, or that cannot prove its protected identity,
            // never turns into an ordinary graded attempt by waiting.
            $integrity = $this->integrityOutcome(
                $lockedAssignment,
                $lockedSubmission,
                $validated['_anti_cheat_session_id'] ?? null,
                $timedOut,
                $finalizeLockedAttempt
            );
            $held = $integrity['status'] !== AssignmentSubmission::INTEGRITY_CLEAR;

            $totalPoints = (int) $lockedAssignment->libraryItem->questions->sum('points');
            $score = 0;

            foreach ($lockedAssignment->libraryItem->questions as $question) {
                $rawAnswer = $submittedAnswers[$question->id] ?? null;
                [$isCorrect, $selectedOptionId, $answerText] = $this->gradeQuestion($question, $rawAnswer);
                $pointsAwarded = $isCorrect ? (int) $question->points : 0;
                $score += $pointsAwarded;

                // A held attempt keeps the learner's work and its correctness
                // for the instructor, but carries no credit until released.
                AssignmentSubmissionAnswer::updateOrCreate(
                    [
                        'assignment_submission_id' => $lockedSubmission->id,
                        'assignment_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $held ? 0 : $pointsAwarded,
                    ]
                );
            }

            $isLate = $lockedAssignment->due_at && now()->greaterThan($lockedAssignment->due_at);

            $completedAt = now();
            $lockedSubmission->update([
                'status' => $held ? 'submitted' : ($isLate ? 'late' : 'graded'),
                'score' => $held ? 0 : $score,
                'provisional_score' => $held ? $score : null,
                'integrity_status' => $integrity['status'],
                'integrity_reason' => $integrity['reason'],
                'total_points' => $totalPoints,
                'submitted_at' => $completedAt,
                'graded_at' => $held ? null : $completedAt,
                'draft_answers' => $submittedAnswers,
                'draft_version' => ((int) ($lockedSubmission->draft_version ?? 0)) + 1,
                'draft_saved_at' => $completedAt,
                'timed_out_at' => $timedOut ? $completedAt : null,
            ]);

            if ($integrity['record_client_lock']) {
                $this->recordLockedAttemptFinalized($lockedAssignment, $lockedSubmission);
            }

            return [
                'submission' => $lockedSubmission,
                'timed_out' => $timedOut,
                'held' => $held,
            ];
        }, 3);

        if (!$processedSubmission) {
            return redirect()->route('student.assignments.result', [$assignment, $submission]);
        }

        $submission = $processedSubmission['submission']->fresh();
        $timedOut = (bool) $processedSubmission['timed_out'];

        if ($processedSubmission['held']) {
            // No mastery refresh, XP or "graded" notice: nothing was credited.
            $notifications->send(
                Auth::user(),
                'assignment_held_for_review',
                'Assignment attempt held for review',
                'Your attempt for “' . $assignment->title . '” was saved, but it is held for instructor review and has no credit yet.',
                route('student.assignments.result', [$assignment, $submission]),
                ['assignment_id' => $assignment->id, 'submission_id' => $submission->id],
                'assignment-held:' . $submission->id
            );

            return redirect()
                ->route('student.assignments.result', [$assignment, $submission])
                ->with('error', 'Your saved answers were submitted, but this attempt is held for instructor review and has no credit yet.');
        }

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
            ? 'Time expired. The answers saved before the deadline were submitted and graded.'
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

    /**
     * Decide whether the attempt may be credited.
     *
     * @return array{status: string, reason: ?string, record_client_lock: bool}
     */
    private function integrityOutcome(
        ClassAssignment $assignment,
        AssignmentSubmission $submission,
        ?string $postedSessionId,
        bool $timedOut,
        bool $finalizeLockedAttempt
    ): array {
        $policyService = app(AntiCheatPolicyService::class);
        $state = $policyService->attemptIntegrityState(Auth::user(), $assignment, $submission);

        if (! $state['enabled']) {
            return [
                'status' => AssignmentSubmission::INTEGRITY_CLEAR,
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
                'status' => AssignmentSubmission::INTEGRITY_BLOCKED,
                'reason' => Str::limit((string) $state['reason'], 250, ''),
                'record_client_lock' => false,
            ];
        }

        if (! $identityValid) {
            return [
                'status' => AssignmentSubmission::INTEGRITY_REVIEW_REQUIRED,
                'reason' => 'The attempt was finalized after the deadline without a valid protected attempt identity.',
                'record_client_lock' => false,
            ];
        }

        if ($finalizeLockedAttempt) {
            // The browser locked itself but the violation event never reached
            // the server (blocked or failed request). Withholding the event
            // must not produce a clean graded result.
            return [
                'status' => AssignmentSubmission::INTEGRITY_REVIEW_REQUIRED,
                'reason' => 'The browser locked this attempt, but no blocking violation event reached the server.',
                'record_client_lock' => true,
            ];
        }

        return [
            'status' => AssignmentSubmission::INTEGRITY_CLEAR,
            'reason' => null,
            'record_client_lock' => false,
        ];
    }

    private function recordLockedAttemptFinalized(ClassAssignment $assignment, AssignmentSubmission $submission): void
    {
        $sessionId = (string) $submission->anti_cheat_session_id;
        $hash = md5('locked-attempt-finalized:' . $submission->id);
        // Deterministic identifier: one row per attempt, however often asked.
        $eventUuid = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-4' . substr($hash, 13, 3)
            . '-8' . substr($hash, 17, 3) . '-' . substr($hash, 20, 12);

        $alreadyRecorded = AntiCheatEvent::query()
            ->where('attempt_session_id', $sessionId)
            ->where('event_uuid', $eventUuid)
            ->exists();
        if ($alreadyRecorded) {
            return;
        }

        AntiCheatEvent::create([
            'user_id' => (int) $submission->student_id,
            'class_id' => (int) $assignment->class_id,
            'class_assignment_id' => (int) $assignment->id,
            'assignment_submission_id' => (int) $submission->id,
            'assessment_type' => 'assignment',
            'event_type' => AntiCheatEventContract::LOCKED_ATTEMPT_FINALIZED_EVENT,
            'severity' => AntiCheatEventContract::severityFor(AntiCheatEventContract::LOCKED_ATTEMPT_FINALIZED_EVENT),
            'attempt_session_id' => $sessionId,
            'event_uuid' => $eventUuid,
            'details' => ['source' => 'server', 'reason' => 'finalize_without_recorded_violation'],
            'occurred_at' => now(),
        ]);
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

    private function deadlinePassed(ClassAssignment $assignment, AssignmentSubmission $submission): bool
    {
        $timeLimitMinutes = (int) ($assignment->libraryItem?->time_limit_minutes ?? 0);
        if ($timeLimitMinutes < 1 || ! $submission->started_at) {
            return false;
        }

        $cutoff = $submission->started_at
            ->copy()
            ->addMinutes($timeLimitMinutes)
            ->addSeconds(self::SUBMISSION_GRACE_SECONDS);

        return now()->greaterThanOrEqualTo($cutoff);
    }

    private function filterKnownAnswers(ClassAssignment $assignment, array $answers): array
    {
        $allowedQuestionIds = $assignment->libraryItem->questions
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
