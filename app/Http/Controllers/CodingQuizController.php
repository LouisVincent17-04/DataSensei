<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ClassChallengeAssignment;
use App\Models\CodingChallengeRetake;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\TestCase;
use App\Services\ChallengePathUnlockService;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PythonSandboxService;

class CodingQuizController extends Controller
{
    public function __construct(private readonly PythonSandboxService $pythonSandbox)
    {
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPER — always use this to compute elapsed/remaining.
    //
    // FIX: Carbon::diffInSeconds() is UNSIGNED (absolute). When APP_TIMEZONE
    // or the DB connection timezone differs from UTC, started_at can appear
    // to be in the future relative to now(), making diffInSeconds return e.g.
    // 28 800 with the wrong sign context — and then:
    //   remaining = 600 - (-28800) = 29 400 s ≈ 490 minutes   ← the bug
    //
    // Raw Unix timestamps are always UTC-based integers, so subtraction is
    // correctly signed and timezone-safe everywhere.
    // ─────────────────────────────────────────────────────────────────────────
    private function elapsedSeconds(CodingQuestionAttempt $attempt): int
    {
        return max(0, now()->timestamp - $attempt->started_at->timestamp);
    }

    private function remainingSeconds(CodingQuestionAttempt $attempt, CodingQuestion $question): int
    {
        return max(0, $question->time_limit_seconds - $this->elapsedSeconds($attempt));
    }


    private function ensureCodingPathIsUnlocked(string $slug): void
    {
        if ($slug === 'university-student') {
            $user = Auth::user();

            abort_unless(
                $user !== null
                && $user->classesAsStudent()->active()->exists(),
                404
            );
        }

        $service = app(ChallengePathUnlockService::class);
        $lockInfo = $service->lockInfo(Auth::user(), $slug, 'coding');

        if (!($lockInfo['unlocked'] ?? false)) {
            abort(403, $lockInfo['reason'] ?? 'This coding difficulty path is locked.');
        }
    }

    private function ensureCodingChallengeBelongsToSlug(Challenge $challenge, string $slug): void
    {
        $challenge->loadMissing('category');

        abort_unless($challenge->category && $challenge->category->slug === $slug, 404);
        abort_unless((bool) $challenge->is_coding_challenge === true, 404);
        abort_unless((bool) $challenge->is_active, 404);

        // An instructor-built challenge is class work: it is reachable only
        // while a published, open class assignment gives it to one of the
        // learner's active classes. Same rule as ChallengesController.
        if ($challenge->isInstructorOwned()) {
            abort_unless($this->hasOpenClassAssignment($challenge), 404);
        }
    }

    private function hasOpenClassAssignment(Challenge $challenge): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        $classIds = $user->classesAsStudent()->active()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return false;
        }

        return ClassChallengeAssignment::query()
            ->openNow()
            ->where('challenge_id', $challenge->id)
            ->whereIn('class_id', $classIds)
            ->exists();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW QUIZ
    // GET /challenges/coding/{slug}/challenge/{challenge}
    // ─────────────────────────────────────────────────────────────────────────
    public function show(string $slug, Challenge|int $challenge)
    {
        if (is_int($challenge)) {
            $challenge = Challenge::findOrFail($challenge);
        }

        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);

        // DS-14: visible test cases are loaded further down, and only for the
        // questions whose timed content this learner is already entitled to.
        $challenge->load(['codingQuestions']);

        abort_if($challenge->codingQuestions->isEmpty(), 404, 'No coding questions found.');

        $userId = Auth::id();

        // Best prior submission per question (voided submissions from retakes are excluded).
        // keyBy() keeps the LAST item per key, so we order ascending by quality
        // (worst → best) so the best submission ends up as the survivor:
        //   1. tests_passed ASC  — fewer passing tests first
        //   2. passed status last — 'passed' rows overwrite 'failed'/'error' rows
        $priorSubmissions = CodingSubmission::where('user_id', $userId)
            ->whereIn('coding_question_id', $challenge->codingQuestions->pluck('id'))
            ->where('voided', false)
            ->orderBy('tests_passed')
            ->orderByRaw("CASE WHEN status = 'passed' THEN 1 ELSE 0 END")
            ->get()
            ->keyBy('coding_question_id');

        // ── Build attempt data ────────────────────────────────────────────
        // Rule:
        //   • Already passed      → state='done',   no attempt needed, no timer
        //   • Existing DB attempt → state='active',  restore it (clock was already running)
        //   • First unsolved      → state='active',  has_attempt=false. The page ships NO
        //                           problem content for it; JavaScript calls the CSRF-protected
        //                           start() POST, which stamps started_at and only then
        //                           returns the content (DS-14).
        //   • Later unsolved      → state='locked',  no content, clock starts lazily via start()
        $attempts            = [];
        $createdFirstAttempt = false;

        foreach ($challenge->codingQuestions as $question) {
            $alreadyPassed = isset($priorSubmissions[$question->id])
                && $priorSubmissions[$question->id]->status === 'passed';

            if ($alreadyPassed) {
                $attempts[$question->id] = [
                    'state'             => 'done',
                    'remaining_seconds' => $question->time_limit_seconds,
                    'expired'           => false,
                    'started_at'        => null,
                    'has_attempt'       => false,
                ];
                continue;
            }

            $existing = CodingQuestionAttempt::where('user_id', $userId)
                ->where('coding_question_id', $question->id)
                ->first();

            if ($existing) {
                // Clock already running — FIX: use timestamp arithmetic
                $remaining = $this->remainingSeconds($existing, $question);

                // Expired questions are treated as finished-without-XP, not active.
                // This lets the next unsolved question start instead of trapping the user
                // forever on a 00:00 timer.
                if ($existing->expired || $remaining <= 0) {
                    $attempts[$question->id] = [
                        'state'             => 'expired',
                        'remaining_seconds' => 0,
                        'expired'           => true,
                        'started_at'        => $existing->started_at->toIso8601String(),
                        'has_attempt'       => true,
                    ];
                    continue;
                }

                $attempts[$question->id] = [
                    'state'             => 'active',
                    'remaining_seconds' => $remaining,
                    'expired'           => false,
                    'started_at'        => $existing->started_at->toIso8601String(),
                    'has_attempt'       => true,
                ];
                $createdFirstAttempt = true;

            } elseif (!$createdFirstAttempt) {
                // The GET page remains read-only. JavaScript starts this first
                // unsolved question through the CSRF-protected POST endpoint.
                $attempts[$question->id] = [
                    'state'             => 'active',
                    'remaining_seconds' => $question->time_limit_seconds,
                    'expired'           => false,
                    'started_at'        => null,
                    'has_attempt'       => false,
                ];

                $createdFirstAttempt = true;

            } else {
                // Not yet visited — show full limit in UI but DO NOT start the DB clock
                $attempts[$question->id] = [
                    'state'             => 'locked',
                    'remaining_seconds' => $question->time_limit_seconds,
                    'expired'           => false,
                    'started_at'        => null,
                    'has_attempt'       => false,
                ];
            }
        }

        // DS-14: timed content (description, starter code, sample cases) is rendered
        // only when the question is solved or its server attempt already exists.
        // Everything else receives the content from start() after started_at is stamped.
        $questionContent = [];
        foreach ($challenge->codingQuestions as $question) {
            $state = $attempts[$question->id];
            $entitled = $state['state'] === 'done' || $state['has_attempt'] === true;
            $questionContent[$question->id] = $entitled ? $this->questionContent($question) : null;
        }

        // Compute the index of the first 'active' question for the blade's ACTIVE_IDX
        $activeIdx = null;
        foreach ($challenge->codingQuestions as $i => $question) {
            if (($attempts[$question->id]['state'] ?? 'locked') === 'active') {
                $activeIdx = $i;
                break;
            }
        }

        // Add no-store headers so the browser never caches this page in bfcache.
        // The pageshow JS handler is the primary bfcache fix, but no-store is a
        // belt-and-suspenders defence: it prevents the quiz page from being stored
        // at all, so Back/Forward always fetches fresh server-rendered state.
        return response()
            ->view('student.coding-challenge-quiz', compact(
                'slug', 'challenge', 'priorSubmissions', 'attempts', 'activeIdx', 'questionContent'
            ))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // START — lazy clock start when user first navigates to a question
    // POST /challenges/coding/{slug}/challenge/{challenge}/start/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function start(string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);
        $this->ensureQuestionBelongsToChallenge($question, $challenge);
        $this->ensureQuestionIsAvailable($question, $challenge);

        $userId = Auth::id();

        // Already passed — no timer needed
        $alreadyPassed = CodingSubmission::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->where('status', 'passed')
            ->where('voided', false)
            ->exists();

        if ($alreadyPassed) {
            return response()->json([
                'remaining_seconds' => $question->time_limit_seconds,
                'expired'           => false,
                'question'          => $this->questionContent($question),
            ]);
        }

        // firstOrCreate is safe against race conditions / double-clicks: the
        // (user_id, coding_question_id) unique key makes a concurrent second
        // insert fall back to the row that won. A repeated start (refresh,
        // double click, retry) therefore RESUMES the attempt — started_at and
        // the attempt identity are never rewritten.
        $generation = 1 + (int) CodingChallengeRetake::where('user_id', $userId)
            ->where('challenge_id', $challenge->id)
            ->value('retake_count');

        $attempt = CodingQuestionAttempt::firstOrCreate(
            [
                'user_id'            => $userId,
                'coding_question_id' => $question->id,
            ],
            [
                'started_at'    => now(),
                'expired'       => false,
                'attempt_token' => $this->newAttemptToken(),
                'generation'    => $generation,
            ]
        );

        // FIX: use timestamp arithmetic, never diffInSeconds
        $remaining = $this->remainingSeconds($attempt, $question);

        // DS-14: the content leaves the server only here, after the attempt row
        // (and therefore started_at) is committed.
        return response()->json([
            'remaining_seconds' => $remaining,
            'expired'           => $remaining <= 0,
            'started_at'        => $attempt->started_at->toIso8601String(),
            'question'          => $this->questionContent($question),
        ]);
    }

    /**
     * Student-visible content of one question. Hidden test cases never appear.
     *
     * @return array{id:int, problem_description:string, starter_code:string, test_cases:array<int, array{id:int, input:?string, expected_output:string}>}
     */
    private function questionContent(CodingQuestion $question): array
    {
        $question->loadMissing('visibleTestCases');

        return [
            'id'                  => (int) $question->id,
            'title'               => (string) ($question->title ?? ''),
            'problem_description' => (string) $question->problem_description,
            'starter_code'        => (string) ($question->starter_code ?? ''),
            'test_cases'          => $question->visibleTestCases
                ->map(fn (TestCase $tc) => [
                    'id'              => (int) $tc->id,
                    'input'           => $tc->input,
                    'expected_output' => (string) $tc->expected_output,
                ])
                ->values()
                ->all(),
        ];
    }

    private function newAttemptToken(): string
    {
        return Str::random(40);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PING — client calls this every 30 s to get server-authoritative remaining
    // GET /challenges/coding/{slug}/challenge/{challenge}/ping/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function ping(string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);
        $this->ensureQuestionBelongsToChallenge($question, $challenge);
        $this->ensureQuestionIsAvailable($question, $challenge);

        $attempt = CodingQuestionAttempt::where('user_id', Auth::id())
            ->where('coding_question_id', $question->id)
            ->first();

        if (!$attempt) {
            return response()->json(['remaining_seconds' => 0, 'expired' => true]);
        }

        // FIX: use timestamp arithmetic, never diffInSeconds
        $remaining = $this->remainingSeconds($attempt, $question);

        return response()->json([
            'remaining_seconds' => $remaining,
            'expired'           => $remaining <= 0,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RUN ONLY — free execution, no submission, no XP
    // POST /challenges/coding/{slug}/challenge/{challenge}/run/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function run(Request $request, string $slug, Challenge $challenge, CodingQuestion $question)
    {
        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);
        $this->ensureQuestionBelongsToChallenge($question, $challenge);
        $this->ensureQuestionIsAvailable($question, $challenge);

        $request->validate([
            'code'  => 'required|string|max:20000',
            'input' => 'nullable|string|max:5000',
        ]);

        if ($expiredResponse = $this->rejectIfQuestionExpired($question)) {
            return $expiredResponse;
        }

        $result = $this->execute($request->input('code'), $request->input('input') ?? '');

        return response()->json([
            'output' => $result['stdout'],
            'stderr' => $result['stderr'],
            'image'  => $result['image'],
            'status' => $result['failed'] ? 'error' : 'ok',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SUBMIT — enforces server-side timer, runs test cases, awards XP
    // POST /challenges/coding/{slug}/challenge/{challenge}/submit/{question}
    // ─────────────────────────────────────────────────────────────────────────
    public function submit(Request $request, string $slug, Challenge $challenge, CodingQuestion $question, GamificationService $gamification)
    {
        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);
        $this->ensureQuestionBelongsToChallenge($question, $challenge);
        $this->ensureQuestionIsAvailable($question, $challenge);

        $request->validate([
            'code' => 'required|string|max:20000',
        ]);

        $userId = Auth::id();
        $submissionLock = Cache::lock(
            'datasensei:coding-submit:'.$userId.':'.$question->id,
            600
        );

        if (!$submissionLock->get()) {
            return response()->json([
                'error' => 'This question is already being submitted. Please wait for the current result.',
            ], 409);
        }

        try {

        // ── Anti-cheat: verify server-side timer ──────────────────────────
        $attempt = CodingQuestionAttempt::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->first();

        if (!$attempt) {
            return response()->json([
                'error'   => 'No attempt record found. Please reload the page.',
                'expired' => true,
            ], 403);
        }

        // DS-15: capture the immutable identity of the attempt this request is
        // graded for. It is re-validated under row locks before anything is
        // committed, so a retake that lands while the sandbox is running cannot
        // turn this result into an active result of the NEW run.
        $identity = $this->attemptIdentity($attempt);

        // FIX: use timestamp arithmetic, never diffInSeconds
        $elapsedSeconds = $this->elapsedSeconds($attempt);
        $timeTaken      = $elapsedSeconds;
        $timeExpired    = $elapsedSeconds >= $question->time_limit_seconds;

        if ($timeExpired && !$attempt->expired) {
            CodingQuestionAttempt::whereKey($attempt->id)
                ->where('attempt_token', $identity['token'])
                ->update(['expired' => true]);
        }

        $alreadyPassed = CodingSubmission::where('user_id', $userId)
            ->where('coding_question_id', $question->id)
            ->where('status', 'passed')
            ->where('voided', false)
            ->exists();

        if ($alreadyPassed) {
            return response()->json(['error' => 'Already solved.'], 422);
        }

        $question->load('testCases');

        if ($question->testCases->isEmpty()) {
            return response()->json([
                'error' => 'This coding question has no test cases and cannot be graded.',
            ], 422);
        }

        if ($timeExpired) {
            $commit = $this->commitSubmission($userId, $question, $identity, [
                'code'               => $request->input('code'),
                'language'           => $question->language,
                'status'             => 'failed',
                'tests_passed'       => 0,
                'tests_total'        => $question->testCases->count(),
                'xp_earned'          => 0,
                'time_taken_seconds' => $timeTaken,
                'test_results'       => [],
                'error_message'      => 'Time limit exceeded.',
            ]);

            if ($commit['outcome'] !== 'committed') {
                return $this->rejectedCommitResponse($commit['outcome']);
            }

            return response()->json([
                'status'             => 'expired',
                'tests_passed'       => 0,
                'tests_total'        => $question->testCases->count(),
                'xp_earned'          => 0,
                'results'            => [],
                'expired'            => true,
                'unlock_next'        => true,
                'message'            => 'Time limit exceeded. No XP earned for this question. You may proceed to the next question.',
                'challenge_complete' => false,
            ]);
        }

        // ── Source-code / instruction validation ─────────────────────────
        // Output-only tests are not enough for tasks like:
        //   Create greeting = "Hello" and print greeting.
        // Without this, print("Hello") passes even though the required variable was never created.
        $sourceErrors = $this->validateSourceRequirements(
            $request->input('code'),
            $question->source_requirements ?? null
        );

        if (!empty($sourceErrors)) {
            $commit = $this->commitSubmission($userId, $question, $identity, [
                'code'               => $request->input('code'),
                'language'           => $question->language,
                'status'             => 'failed',
                'tests_passed'       => 0,
                'tests_total'        => $question->testCases->count(),
                'xp_earned'          => 0,
                'time_taken_seconds' => $timeTaken,
                'test_results'       => [],
                'error_message'      => 'Instruction check failed: ' . implode(' ', $sourceErrors),
            ]);

            if ($commit['outcome'] !== 'committed') {
                return $this->rejectedCommitResponse($commit['outcome']);
            }

            $submission = $commit['submission'];

            return response()->json([
                'submission_id'       => $submission->id,
                'status'              => 'failed',
                'tests_passed'        => 0,
                'tests_total'         => $question->testCases->count(),
                'xp_earned'           => 0,
                'results'             => [],
                'expired'             => false,
                'source_failed'       => true,
                'instruction_errors'  => $sourceErrors,
                'message'             => 'Your output may be correct, but the required coding instructions were not followed.',
                'challenge_complete'  => false,
            ], 422);
        }

        // ── Grade ─────────────────────────────────────────────────────────
        // Runs OUTSIDE any database transaction: the sandbox can take many
        // seconds and must not hold row locks while it does.
        $graded = $question->testCases->map(
            fn(TestCase $tc) => $this->runSingle($request->input('code'), $tc)
        );

        // DS-13: $results is the ONLY per-test data that reaches the student or
        // the student-visible test_results column. Detailed diagnostics of hidden
        // cases go to the restricted grader_diagnostics column.
        $results     = $graded->pluck('public')->values()->all();
        $diagnostics = $graded->pluck('restricted')->filter()->values()->all();

        // DS-12: a test counts only when it executed successfully AND matched.
        $passed = collect($results)->where('passed', true)->count();
        $total  = $question->testCases->count();
        $hasExecutionError = collect($results)->contains('status', 'error');

        $status = match(true) {
            $total > 0 && $passed === $total && !$hasExecutionError => 'passed',
            $hasExecutionError && $passed === 0                     => 'error',
            default                                                 => 'failed',
        };

        $xp = 0;
        if ($passed > 0) {
            $rawXp  = (int) round($question->base_xp * ($passed / $total));
            $bonus  = ($status === 'passed' && $timeTaken < $question->time_limit_seconds * 0.5) ? 1.2 : 1.0;
            $xp     = (int) round($rawXp * $bonus);
        }

        $commit = $this->commitSubmission($userId, $question, $identity, [
            'code'               => $request->input('code'),
            'language'           => $question->language,
            'status'             => $status,
            'tests_passed'       => $passed,
            'tests_total'        => $total,
            'xp_earned'          => $xp,
            'time_taken_seconds' => $timeTaken,
            'test_results'       => $results,
            'error_message'      => $this->publicErrorMessage($results),
            'grader_diagnostics' => $diagnostics ?: null,
        ], awardXp: true);

        if ($commit['outcome'] !== 'committed') {
            return $this->rejectedCommitResponse($commit['outcome']);
        }

        $submission = $commit['submission'];

        $questionIds = $challenge->codingQuestions()->pluck('id');

        $passedCount = CodingSubmission::where('user_id', $userId)
            ->whereIn('coding_question_id', $questionIds)
            ->where('status', 'passed')
            ->where('voided', false)
            ->distinct('coding_question_id')
            ->count('coding_question_id');

        $challengeComplete = $passedCount >= $questionIds->count();

        if ($challengeComplete) {
            app(ChallengePathUnlockService::class)->notifyExceptionalUnlocks(Auth::user(), 'coding');
        }

        $achievementMessages = $gamification->awardForCodingSubmission(
            Auth::user(),
            $challenge,
            $question,
            $submission,
            $challengeComplete
        );

        return response()->json([
            'submission_id'      => $submission->id,
            'status'             => $status,
            'tests_passed'       => $passed,
            'tests_total'        => $total,
            'xp_earned'          => $xp,
            'achievements'       => $achievementMessages,
            'results'            => $results,
            'expired'            => false,
            'remaining_seconds'  => max(0, $question->time_limit_seconds - $elapsedSeconds),
            'challenge_complete' => $challengeComplete,
            'redirect_url'       => $challengeComplete ? route('challenges.coding.map', $slug) : null,
        ]);
        } finally {
            try {
                $submissionLock->release();
            } catch (\Throwable) {
                // The lock has a finite TTL if its backend becomes unavailable.
            }
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DS-15 — attempt identity + guarded commit
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Immutable identity of an attempt. Rows created before the identity columns
     * existed have no token yet; one is stamped once (compare-and-set on NULL so
     * two concurrent requests agree on the same value) and never changes again.
     * retake() removes the row, so a new run always gets a different token — the
     * auto-increment id alone is not enough because InnoDB before MySQL 8.0 can
     * hand a deleted id out again after a server restart.
     *
     * @return array{token:string, generation:int}
     */
    private function attemptIdentity(CodingQuestionAttempt $attempt): array
    {
        if (blank($attempt->attempt_token)) {
            CodingQuestionAttempt::whereKey($attempt->id)
                ->whereNull('attempt_token')
                ->update(['attempt_token' => $this->newAttemptToken()]);

            $attempt->refresh();
        }

        return [
            'token'      => (string) $attempt->attempt_token,
            'generation' => max(1, (int) ($attempt->generation ?? 1)),
        ];
    }

    /**
     * Persist a graded submission only if the attempt it was graded for is still
     * the learner's current attempt.
     *
     * Lock order is users row → attempt row, the same order retake() uses
     * (users row → retake counter → attempt rows), so the two can never
     * deadlock and always serialize per learner in the DATABASE. The cache lock
     * in submit() is only a courtesy "already submitting" guard: the default
     * file/array cache stores are not shared between servers, so correctness
     * must not depend on it.
     *
     * Outcomes:
     *   committed — active result (XP delta applied when $awardXp)
     *   stale     — a retake replaced the attempt: the row is kept for history,
     *               voided, bound to the OLD attempt, with no XP and no progress
     *   duplicate — another request already recorded a pass for this attempt
     *
     * @return array{outcome:string, submission:?CodingSubmission}
     */
    private function commitSubmission(int $userId, CodingQuestion $question, array $identity, array $attributes, bool $awardXp = false): array
    {
        return DB::transaction(function () use ($userId, $question, $identity, $attributes, $awardXp): array {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            $current = CodingQuestionAttempt::where('user_id', $userId)
                ->where('coding_question_id', $question->id)
                ->lockForUpdate()
                ->first();

            $base = [
                'user_id'            => $userId,
                'coding_question_id' => $question->id,
                'attempt_token'      => $identity['token'],
                'attempt_generation' => $identity['generation'],
            ];

            if (!$current || !hash_equals((string) $current->attempt_token, $identity['token'])) {
                $submission = CodingSubmission::create(array_merge($attributes, $base, [
                    'xp_earned'   => 0,
                    'voided'      => true,
                    'void_reason' => 'stale_attempt',
                ]));

                return ['outcome' => 'stale', 'submission' => $submission];
            }

            $alreadyPassed = CodingSubmission::where('user_id', $userId)
                ->where('coding_question_id', $question->id)
                ->where('status', 'passed')
                ->where('voided', false)
                ->exists();

            if ($alreadyPassed) {
                return ['outcome' => 'duplicate', 'submission' => null];
            }

            $submission = CodingSubmission::create(array_merge($attributes, $base, ['voided' => false]));

            if ($awardXp) {
                $xp = (int) $submission->xp_earned;
                $previousBest = CodingSubmission::where('user_id', $userId)
                    ->where('coding_question_id', $question->id)
                    ->where('id', '!=', $submission->id)
                    ->max('xp_earned') ?? 0;

                if ($xp > $previousBest) {
                    Auth::user()->increment('xp', $xp - $previousBest);
                }
            }

            return ['outcome' => 'committed', 'submission' => $submission];
        }, 3);
    }

    private function rejectedCommitResponse(string $outcome): \Illuminate\Http\JsonResponse
    {
        if ($outcome === 'duplicate') {
            return response()->json(['error' => 'Already solved.'], 422);
        }

        return response()->json([
            'status'        => 'stale',
            'stale_attempt' => true,
            'error'         => 'This challenge was restarted while your code was being graded. The result was saved to your history but does not count for the new run. Please reload the page.',
        ], 409);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DS-13 — what a student may see about failing tests
    // ─────────────────────────────────────────────────────────────────────────
    private const PUBLIC_TEXT_LIMIT = 4000;

    private function boundText(?string $text, int $limit = self::PUBLIC_TEXT_LIMIT): ?string
    {
        if ($text === null || $text === '') {
            return null;
        }

        return strlen($text) > $limit
            ? substr($text, 0, $limit) . "\n[truncated]"
            : $text;
    }

    /** Top-level message: never derived from a hidden case's output. */
    private function publicErrorMessage(array $results): ?string
    {
        foreach ($results as $result) {
            if (($result['status'] ?? null) === 'error' && !($result['is_hidden'] ?? false) && !empty($result['stderr'])) {
                return $this->boundText((string) $result['stderr'], 2000);
            }
        }

        foreach ($results as $result) {
            if (($result['status'] ?? null) === 'error') {
                return $result['message'] ?? 'A test case ended with an error.';
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SOURCE REQUIREMENTS — validates that the student's SOURCE CODE follows
    // the task instructions, not only the final output.
    // ─────────────────────────────────────────────────────────────────────────
    private function validateSourceRequirements(string $code, mixed $requirements): array
    {
        if (empty($requirements)) {
            return [];
        }

        if (is_string($requirements)) {
            $decoded = json_decode($requirements, true);
            $requirements = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($requirements)) {
            return [];
        }

        $errors = [];

        foreach (($requirements['required_variables'] ?? []) as $variable) {
            $variable = (string) $variable;
            if (!preg_match('/\b' . preg_quote($variable, '/') . '\b/', $code)) {
                $errors[] = "Required variable `{$variable}` was not found.";
            }
        }

        foreach (($requirements['required_assignments'] ?? []) as $assignment) {
            $variable = (string) ($assignment['variable'] ?? '');
            if ($variable === '') continue;

            $value = $assignment['value'] ?? null;
            $type  = $assignment['value_type'] ?? 'string';
            $pattern = $this->assignmentPattern($variable, $value, $type);

            if (!preg_match($pattern, $code)) {
                $display = is_bool($value) ? ($value ? 'True' : 'False') : (string) $value;
                $errors[] = "Required assignment missing: `{$variable} = {$display}`.";
            }
        }

        foreach (($requirements['required_print_variables'] ?? []) as $variable) {
            $variable = (string) $variable;
            $pattern = '/\bprint\s*\(\s*' . preg_quote($variable, '/') . '\s*\)/';
            if (!preg_match($pattern, $code)) {
                $errors[] = "You must print the variable using `print({$variable})`.";
            }
        }

        foreach (($requirements['forbidden_print_literals'] ?? []) as $literal) {
            $literal = (string) $literal;
            $pattern = '/\bprint\s*\(\s*([\'\"])' . preg_quote($literal, '/') . '\\1\s*\)/';
            if (preg_match($pattern, $code)) {
                $errors[] = "Do not directly print `{$literal}`. Store it in the required variable and print the variable.";
            }
        }

        foreach (($requirements['required_regex'] ?? []) as $rule) {
            $pattern = is_array($rule) ? ($rule['pattern'] ?? null) : $rule;
            $message = is_array($rule) ? ($rule['message'] ?? 'A required code pattern is missing.') : 'A required code pattern is missing.';
            if ($pattern && @preg_match($pattern, '') !== false && !preg_match($pattern, $code)) {
                $errors[] = $message;
            }
        }

        foreach (($requirements['forbidden_regex'] ?? []) as $rule) {
            $pattern = is_array($rule) ? ($rule['pattern'] ?? null) : $rule;
            $message = is_array($rule) ? ($rule['message'] ?? 'A forbidden code pattern was found.') : 'A forbidden code pattern was found.';
            if ($pattern && @preg_match($pattern, '') !== false && preg_match($pattern, $code)) {
                $errors[] = $message;
            }
        }

        if (!empty($errors) && !empty($requirements['message'])) {
            array_unshift($errors, (string) $requirements['message']);
        }

        return array_values(array_unique($errors));
    }

    private function assignmentPattern(string $variable, mixed $value, string $type): string
    {
        $var = preg_quote($variable, '/');

        return match ($type) {
            'string' => '/^\s*' . $var . '\s*=\s*([\'\"])' . preg_quote((string) $value, '/') . '\\1\s*(?:#.*)?$/m',
            'bool'   => '/^\s*' . $var . '\s*=\s*' . ($value ? 'True' : 'False') . '\s*(?:#.*)?$/m',
            'none'   => '/^\s*' . $var . '\s*=\s*None\s*(?:#.*)?$/m',
            default  => '/^\s*' . $var . '\s*=\s*' . preg_quote((string) $value, '/') . '\s*(?:#.*)?$/m',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TIMEOUT GUARD — Run and Submit must not allow coding after time is out.
    // ─────────────────────────────────────────────────────────────────────────
    private function rejectIfQuestionExpired(CodingQuestion $question): ?\Illuminate\Http\JsonResponse
    {
        $attempt = CodingQuestionAttempt::where('user_id', Auth::id())
            ->where('coding_question_id', $question->id)
            ->first();

        if (!$attempt) {
            return response()->json([
                'status'  => 'expired',
                'expired' => true,
                'error'   => 'No active attempt was found. Please reload the challenge.',
            ], 403);
        }

        $remaining = $this->remainingSeconds($attempt, $question);

        if ($remaining <= 0 || $attempt->expired) {
            if (!$attempt->expired) {
                $attempt->update(['expired' => true]);
            }

            return response()->json([
                'status'       => 'expired',
                'expired'      => true,
                'unlock_next'  => true,
                'error'        => 'Time limit exceeded. No XP can be earned for this question.',
            ], 403);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RETAKE — wipes attempt + voids submissions, then redirects to fresh quiz
    // POST /challenges/coding/{slug}/challenge/{challenge}/retake
    //
    // Rules:
    //   • Max 3 retakes per user per challenge (enforced here + in the map blade).
    //   • Old CodingQuestionAttempt rows are archived to coding_question_attempt_archives
    //     and then removed, so the server timer resets and every new run gets a new
    //     attempt_token (DS-15). A submission still being graded for the old token is
    //     stored voided and never counts for the new run.
    //   • Old CodingSubmission rows are marked voided = true (NOT deleted) so
    //     XP history / analytics are preserved, but all game-logic queries
    //     (alreadyPassed, previousBest, priorSubmissions) skip voided rows.
    //   • XP already credited to the user's account is intentionally kept.
    // ─────────────────────────────────────────────────────────────────────────
    public function retake(string $slug, Challenge $challenge)
    {
        $this->ensureCodingPathIsUnlocked($slug);
        $this->ensureCodingChallengeBelongsToSlug($challenge, $slug);

        $userId      = Auth::id();
        $questionIds = $challenge->codingQuestions()->pluck('id');

        $retakeAllowed = DB::transaction(function () use ($userId, $challenge, $questionIds): bool {
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            $retakeRecord = CodingChallengeRetake::firstOrCreate(
                ['user_id' => $userId, 'challenge_id' => $challenge->id],
                ['retake_count' => 0],
            );
            $retakeRecord = CodingChallengeRetake::whereKey($retakeRecord->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($retakeRecord->retake_count >= CodingChallengeRetake::MAX_RETAKES) {
                return false;
            }

            // DS-15: lock the attempt rows in the same order submit() does
            // (users row first). An in-flight submission either committed before
            // this point — and is voided below with the rest of the old run — or
            // commits afterwards, finds its attempt token gone, and stores its
            // result as voided history of the OLD run.
            $oldAttempts = CodingQuestionAttempt::where('user_id', $userId)
                ->whereIn('coding_question_id', $questionIds)
                ->lockForUpdate()
                ->get();

            $hasActiveResults = CodingSubmission::where('user_id', $userId)
                ->whereIn('coding_question_id', $questionIds)
                ->where('voided', false)
                ->exists();

            // Retry safety: a double click / replayed POST right after a retake
            // finds nothing left to reset. It must not burn another retake.
            if ($oldAttempts->isEmpty() && !$hasActiveResults) {
                return true;
            }

            // History is preserved: attempts are archived before the unique
            // (user_id, coding_question_id) slot is freed for the new run.
            foreach ($oldAttempts as $oldAttempt) {
                DB::table('coding_question_attempt_archives')->insert([
                    'user_id'            => $oldAttempt->user_id,
                    'coding_question_id' => $oldAttempt->coding_question_id,
                    'attempt_token'      => $oldAttempt->attempt_token,
                    'generation'         => max(1, (int) ($oldAttempt->generation ?? 1)),
                    'started_at'         => $oldAttempt->started_at,
                    'expired'            => (bool) $oldAttempt->expired,
                    'retaken_at'         => now(),
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);
            }

            CodingQuestionAttempt::whereIn('id', $oldAttempts->pluck('id'))->delete();

            CodingSubmission::where('user_id', $userId)
                ->whereIn('coding_question_id', $questionIds)
                ->where('voided', false)
                ->update(['voided' => true, 'void_reason' => 'retake']);

            $retakeRecord->increment('retake_count');

            return true;
        }, 3);

        if (!$retakeAllowed) {
            return redirect()
                ->route('challenges.coding.map', $slug)
                ->with('error', 'You have used all 3 retakes for this challenge.');
        }

        // ── Go straight into the quiz (show() will create a fresh attempt) ─
        return redirect()->route(
            'challenges.coding.quiz',
            ['slug' => $slug, 'challenge' => $challenge->id]
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Run code against one TestCase
    // ─────────────────────────────────────────────────────────────────────────
    /**
     * @return array{public: array<string, mixed>, restricted: ?array<string, mixed>}
     *
     * `public` is safe for the student (response + test_results column).
     * `restricted` exists only for a non-passing HIDDEN case and is stored in
     * the grader_diagnostics column, which no student route serializes.
     */
    private function runSingle(string $code, TestCase $tc): array
    {
        $isHidden = (bool) $tc->is_hidden;

        $result   = $this->execute($code, $tc->input ?? '');
        $actual   = rtrim($result['stdout']);
        $expected = rtrim($tc->expected_output);

        // DS-12: output equality alone is not a pass. A program that prints the
        // expected answer and then raises, exits non-zero, is blocked by the
        // sandbox policy or is killed by the time limit has FAILED this test.
        $timedOut = (bool) $result['timed_out'];
        $failed   = (bool) $result['failed'] || $timedOut;
        $passed   = !$failed && $actual === $expected;

        $category = match (true) {
            $timedOut => 'time_limit',
            $failed   => 'runtime_error',
            !$passed  => 'wrong_answer',
            default   => null,
        };
        $status = $failed ? 'error' : ($passed ? 'passed' : 'failed');

        if ($isHidden) {
            // DS-13: nothing the student's program wrote (stdout, stderr,
            // traceback, exception text) and nothing from the test definition
            // leaves the server for a hidden case — only pass/fail + category.
            $public = [
                'test_case_id' => $tc->id,
                'input'        => null,
                'expected'     => null,
                'actual'       => null,
                'passed'       => $passed,
                'status'       => $status,
                'category'     => $category,
                'message'      => match ($category) {
                    'time_limit'    => 'A hidden test case exceeded the time limit.',
                    'runtime_error' => 'A hidden test case ended with a runtime error.',
                    'wrong_answer'  => 'A hidden test case produced the wrong output.',
                    default         => null,
                },
                'stderr'       => null,
                'is_hidden'    => true,
            ];

            $restricted = $passed ? null : [
                'test_case_id' => $tc->id,
                'category'     => $category,
                'exit_code'    => $result['exit_code'],
                'stderr'       => $this->boundText($result['stderr']),
                'stdout'       => $this->boundText($actual, 1000),
            ];

            return ['public' => $public, 'restricted' => $restricted];
        }

        return [
            'public' => [
                'test_case_id' => $tc->id,
                'input'        => $tc->input,
                'expected'     => $tc->expected_output,
                'actual'       => $this->boundText($actual) ?? '',
                'passed'       => $passed,
                'status'       => $status,
                'category'     => $category,
                'message'      => null,
                'stderr'       => $this->boundText($result['stderr']),
                'is_hidden'    => false,
            ],
            'restricted' => null,
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Execute Python through the centralized sandbox service
    // ─────────────────────────────────────────────────────────────────────────
    private function execute(string $code, ?string $stdin = ''): array
    {
        // Grading compares output exactly. Prompts from input("Enter a: ") are not
        // printed, so a program written the way the lessons teach is not
        // failed for them. The learner's own Run here uses the same setting,
        // so what they see matches what is graded.
        $result = $this->pythonSandbox->runInline($code, $stdin ?? '', ['quiet_input_prompts' => true]);

        return [
            'stdout' => $result['stdout'] ?? '',
            'stderr' => ($result['stderr'] ?? '') !== '' ? $result['stderr'] : null,
            'image' => null,
            'plots' => $result['plots'] ?? [],
            // Fail closed: a result without an explicit success flag is a failure.
            'failed' => (bool) ($result['failed'] ?? true),
            'timed_out' => (bool) ($result['timed_out'] ?? false),
            'exit_code' => isset($result['exit_code']) ? (int) $result['exit_code'] : null,
        ];
    }

    private function ensureQuestionBelongsToChallenge(CodingQuestion $question, Challenge $challenge): void
    {
        abort_unless((int) $question->challenge_id === (int) $challenge->id, 404);
    }

    private function ensureQuestionIsAvailable(CodingQuestion $question, Challenge $challenge): void
    {
        $orderedQuestions = $challenge->codingQuestions()
            ->orderBy('order_index')
            ->orderBy('id')
            ->get(['id', 'time_limit_seconds']);
        $targetIndex = $orderedQuestions->search(
            fn (CodingQuestion $candidate) => (int) $candidate->id === (int) $question->id
        );

        abort_if($targetIndex === false, 404);

        $preceding = $orderedQuestions->take((int) $targetIndex);
        if ($preceding->isEmpty()) {
            return;
        }

        $questionIds = $preceding->pluck('id');
        $passedIds = CodingSubmission::where('user_id', Auth::id())
            ->whereIn('coding_question_id', $questionIds)
            ->where('status', 'passed')
            ->where('voided', false)
            ->pluck('coding_question_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $attempts = CodingQuestionAttempt::where('user_id', Auth::id())
            ->whereIn('coding_question_id', $questionIds)
            ->get()
            ->keyBy('coding_question_id');

        foreach ($preceding as $previousQuestion) {
            if (in_array((int) $previousQuestion->id, $passedIds, true)) {
                continue;
            }

            $attempt = $attempts->get($previousQuestion->id);
            if ($attempt && ($attempt->expired || $this->remainingSeconds($attempt, $previousQuestion) <= 0)) {
                continue;
            }

            abort(403, 'Complete or time out the preceding coding question before opening this one.');
        }
    }

}
