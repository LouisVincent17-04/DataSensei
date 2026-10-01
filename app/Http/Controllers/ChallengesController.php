<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeAttemptAnswer;
use App\Models\ChallengeAttemptEvent;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\ClassChallengeAssignment;
use App\Services\ChallengeModuleAccessService;
use App\Services\ChallengePathUnlockService;
use App\Services\XpPolicy;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChallengesController extends Controller
{
    private const SUSPICIOUS_EVENT_LIMIT = 5;

    private const QUIZ_EVENT_SEVERITY = [
        'tab_hidden_or_app_switched' => 'low',
        'page_leave_or_refresh' => 'low',
    ];

    private function fallbackCategories(): \Illuminate\Support\Collection
    {
        return collect([
            (object)[
                'name' => 'Newbie',
                'slug' => 'newbie',
                'target_audience' => 'Just starting, little to no background in data',
                'description' => 'Learn the absolute basics of Python and data literacy from scratch. No prior coding experience required.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>',
            ],
            (object)[
                'name' => 'University Student',
                'slug' => 'university-student',
                'target_audience' => 'Currently studying Data Science, CS, or related fields',
                'description' => 'Bridge the gap between academic theory and practical application. Focus on algorithms, stats, and real coding.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>',
            ],
            (object)[
                'name' => 'Intermediate',
                'slug' => 'intermediate',
                'target_audience' => 'Has basics and can do small projects',
                'description' => 'Level up your skills. Dive into data wrangling, machine learning fundamentals, and visualization techniques.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>',
            ],
            (object)[
                'name' => 'Advanced',
                'slug' => 'advanced',
                'target_audience' => 'Strong skills, can build models and real-world systems',
                'description' => 'Tackle complex datasets, deep learning, optimization, and scalable data pipelines.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><line stroke-linecap="round" stroke-linejoin="round" x1="8.59" y1="13.51" x2="15.42" y2="17.49" /><line stroke-linecap="round" stroke-linejoin="round" x1="15.41" y1="6.51" x2="8.59" y2="10.49" /></svg>',
            ],
            (object)[
                'name' => 'Professional',
                'slug' => 'professional',
                'target_audience' => 'Working in industry or ready for enterprise-level tasks',
                'description' => 'Master MLOps, system architecture, big data frameworks, and high-level strategy for enterprise systems.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7.52a2 2 0 00-1-1.73l-6-3.46a2 2 0 00-2 0l-6 3.46a2 2 0 00-1 1.73v6.93a2 2 0 001 1.73l6 3.46a2 2 0 002 0l6-3.46a2 2 0 001-1.73V7.52z" /></svg>',
            ],
        ]);
    }

    public function index(ChallengePathUnlockService $unlockService)
    {
        $categories = ChallengeCategory::orderBy('order_index')->get();
        $hasUniversity = $this->hasActiveClassEnrollment();

        if ($categories->isEmpty()) {
            $categories = $this->fallbackCategories();
        }

        if (! $hasUniversity) {
            $categories = $categories
                ->reject(fn ($category): bool => $category->slug === 'university-student')
                ->values();
        }

        $pathLocks = $unlockService->buildPathLocks(Auth::user(), 'mcq');
        $exceptionalNotifications = Auth::check()
            ? $unlockService->notifyExceptionalUnlocks(Auth::user(), 'mcq')
            : [];

        $classChallenges = $this->classChallengeCards(false);

        return view('student.challenges', compact('categories', 'hasUniversity', 'pathLocks', 'exceptionalNotifications', 'classChallenges'));
    }

    public function codingIndex(ChallengePathUnlockService $unlockService)
    {
        $categories = ChallengeCategory::orderBy('order_index')->get();
        $hasUniversity = $this->hasActiveClassEnrollment();

        if ($categories->isEmpty()) {
            $categories = $this->fallbackCategories();
        }

        if (! $hasUniversity) {
            $categories = $categories
                ->reject(fn ($category): bool => $category->slug === 'university-student')
                ->values();
        }

        $pathLocks = $unlockService->buildPathLocks(Auth::user(), 'coding');
        $exceptionalNotifications = Auth::check()
            ? $unlockService->notifyExceptionalUnlocks(Auth::user(), 'coding')
            : [];

        $classChallenges = $this->classChallengeCards(true);

        return view('student.coding-challenges', compact('categories', 'hasUniversity', 'pathLocks', 'exceptionalNotifications', 'classChallenges'));
    }

    public function map($slug, ChallengePathUnlockService $unlockService)
    {
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService);

        $category = ChallengeCategory::where('slug', $slug)->firstOrFail();

        $challenges = Challenge::where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', 0)
            ->where('is_active', true)
            ->where(function ($query): void {
                $this->visibleToStudent($query);
            })
            ->orderByRaw("CASE WHEN visibility = 'instructor' THEN 1 ELSE 0 END")
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

        $completedChallengeIds = [];
        $bestScores = [];
        $activeAttemptIds = [];
        $latestResultAttemptIds = [];

        if (Auth::check()) {
            $finishedAttempts = ChallengeAttempt::query()
                ->where('user_id', Auth::id())
                ->whereIn('challenge_id', $challenges->pluck('id'))
                ->whereIn('status', ['submitted', 'expired', 'disqualified'])
                ->orderByDesc('attempt_no')
                ->orderByDesc('id')
                ->get()
                ->groupBy('challenge_id');

            // Any finished attempt counts toward passing a module, retakes
            // included (DataSensei Updates 4): a failed first try no longer
            // keeps the next module locked for good. XP and the leaderboard
            // still come from the first (ranked) attempt only.
            $scoredAttempts = $finishedAttempts->map(
                fn ($attempts) => $attempts
                    ->whereIn('status', ['submitted', 'expired'])
                    ->filter(fn (ChallengeAttempt $attempt): bool => (int) $attempt->total_questions > 0)
                    ->values()
            );

            $latestResultAttemptIds = $finishedAttempts
                ->mapWithKeys(fn ($attempts, $challengeId): array => [
                    (int) $challengeId => (int) $attempts->first()->id,
                ])
                ->all();

            $attemptedChallengeIds = ChallengeAttempt::query()
                ->where('user_id', Auth::id())
                ->whereIn('challenge_id', $challenges->pluck('id'))
                ->distinct()
                ->pluck('challenge_id')
                ->mapWithKeys(fn ($id): array => [(int) $id => true]);

            foreach ($challenges as $ch) {
                $attempts = $scoredAttempts->get($ch->id, collect());
                $best = $attempts->sortByDesc(fn (ChallengeAttempt $attempt): float =>
                    (float) $attempt->score / max(1, (int) $attempt->total_questions)
                )->first();

                if ($best) {
                    $bestScores[$ch->id] = ['score' => $best->score, 'xp' => (int) $attempts->max('xp_awarded')];
                    if (((int) $best->score / max(1, (int) $best->total_questions)) >= 0.70) {
                        $completedChallengeIds[] = $ch->id;
                    }
                    continue;
                }

                if ($attemptedChallengeIds->has($ch->id)) {
                    continue;
                }

                // Preserve progress saved before the attempts table existed.
                $legacy = DB::table('challenge_user')
                    ->where('user_id', Auth::id())
                    ->where('challenge_id', $ch->id)
                    ->first();
                $totalQuestions = $ch->questions()->count();

                if ($legacy && $totalQuestions > 0) {
                    $bestScores[$ch->id] = ['score' => $legacy->score, 'xp' => $legacy->xp_awarded];
                    if ((int) $legacy->score >= (int) ceil($totalQuestions * 0.70)) {
                        $completedChallengeIds[] = $ch->id;
                    }
                }
            }

            $activeAttemptIds = ChallengeAttempt::where('user_id', Auth::id())
                ->whereIn('challenge_id', $challenges->pluck('id'))
                ->where('status', 'in_progress')
                ->pluck('challenge_id')
                ->all();
        }

        $exceptionalNotifications = Auth::check()
            ? $unlockService->notifyExceptionalUnlocks(Auth::user(), 'mcq')
            : [];

        // Challenges an instructor has opened for one of the learner's classes
        // are unlocked on the map, whatever the learner's place in the path.
        $classChallengeIds = $this->openClassChallengeIdsAmong($challenges);

        return view('student.challenges-map', compact(
            'slug', 'category', 'challenges', 'completedChallengeIds', 'bestScores', 'exceptionalNotifications', 'activeAttemptIds', 'latestResultAttemptIds', 'classChallengeIds'
        ));
    }

    public function codingMap($slug, ChallengePathUnlockService $unlockService)
    {
        $this->ensurePathIsUnlocked($slug, 'coding', $unlockService);

        $category = ChallengeCategory::where('slug', $slug)->firstOrFail();

        $challenges = Challenge::where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', 1)
            ->where('is_active', true)
            ->where(function ($query): void {
                $this->visibleToStudent($query);
            })
            ->orderByRaw("CASE WHEN visibility = 'instructor' THEN 1 ELSE 0 END")
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

        $completedChallengeIds = [];
        $inProgressChallengeIds = [];
        $bestScores = [];

        if (Auth::check()) {
            foreach ($challenges as $ch) {
                $totalQuestions = $ch->codingQuestions()->count();
                if ($totalQuestions === 0) {
                    continue;
                }

                $questionIds = $ch->codingQuestions()->pluck('coding_questions.id');

                $passedCount = \App\Models\CodingSubmission::where('user_id', Auth::id())
                    ->whereIn('coding_question_id', $questionIds)
                    ->where('status', 'passed')
                    ->where('voided', false)
                    ->distinct('coding_question_id')
                    ->count('coding_question_id');

                if ($passedCount >= $totalQuestions) {
                    $completedChallengeIds[] = $ch->id;
                    continue;
                }

                $hasAttempt = \App\Models\CodingQuestionAttempt::where('user_id', Auth::id())
                    ->whereIn('coding_question_id', $questionIds)
                    ->exists();

                if ($hasAttempt) {
                    $inProgressChallengeIds[] = $ch->id;
                }
            }

            foreach ($challenges as $ch) {
                $questionIds = $ch->codingQuestions()->pluck('coding_questions.id');

                $bestData = \App\Models\CodingSubmission::where('user_id', Auth::id())
                    ->whereIn('coding_question_id', $questionIds)
                    ->where('voided', false)
                    ->select(
                        'coding_question_id',
                        DB::raw('MAX(tests_passed) as best_passed'),
                        DB::raw('MAX(tests_total) as total'),
                        DB::raw('MAX(xp_earned) as best_xp')
                    )
                    ->groupBy('coding_question_id')
                    ->get();

                if ($bestData->isNotEmpty()) {
                    $bestScores[$ch->id] = [
                        'score' => $bestData->sum('best_passed') . '/' . $bestData->sum('total'),
                        'xp' => $bestData->sum('best_xp'),
                    ];
                }
            }
        }

        $exceptionalNotifications = Auth::check()
            ? $unlockService->notifyExceptionalUnlocks(Auth::user(), 'coding')
            : [];

        $classChallengeIds = $this->openClassChallengeIdsAmong($challenges);

        return view('student.coding-challenges-map', compact(
            'slug', 'category', 'challenges',
            'completedChallengeIds', 'inProgressChallengeIds', 'bestScores', 'exceptionalNotifications', 'classChallengeIds'
        ));
    }

    public function showQuiz($slug, $challenge_id, ChallengePathUnlockService $unlockService, GamificationService $gamification)
    {
        $challenge = Challenge::with('category')->findOrFail($challenge_id);
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService, $challenge);

        // An inactive version accepts no NEW attempts, but getOrCreateMcqAttempt()
        // still resumes an in-progress attempt that was started before a newer
        // version was published, so that learner can finish on the original version.
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);
        $this->ensureModuleIsOpen($challenge, $slug);

        $attempt = $this->getOrCreateMcqAttempt($challenge);

        if (now()->greaterThanOrEqualTo($attempt->expires_at)) {
            $result = $this->finalizeMcqAttempt($attempt, 'expired', $unlockService, $gamification);
            return redirect()->route('challenges.quiz.result', [
                'slug' => $slug,
                'challenge' => $challenge->id,
                'attempt' => $result['attempt_id'],
            ])->with('success', $result['message']);
        }

        $challenge->setRelation('questions', $this->orderedQuestionsForAttempt($attempt));

        $savedAnswers = $attempt->answers()
            ->whereNotNull('selected_option_id')
            ->pluck('selected_option_id', 'challenge_question_id')
            ->map(fn ($value) => (int) $value)
            ->all();

        $autosaveSeqBase = (int) $attempt->answers()->max('client_seq');

        $serverNowMs = (int) now()->getTimestampMs();
        $expiresAtMs = ($attempt->expires_at->getTimestamp() * 1000);
        $remainingSeconds = max(0, (int) floor(($expiresAtMs - $serverNowMs) / 1000));

        return view('student.challenge-quiz', compact(
            'slug',
            'challenge',
            'attempt',
            'savedAnswers',
            'autosaveSeqBase',
            'serverNowMs',
            'expiresAtMs',
            'remainingSeconds'
        ));
    }

    public function showQuizResult(
        $slug,
        $challenge_id,
        $attempt_id,
        ChallengePathUnlockService $unlockService,
        GamificationService $gamification
    ) {
        $challenge = Challenge::with('category')->findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);

        $attempt = ChallengeAttempt::with('answers.selectedOption')
            ->where('id', (int) $attempt_id)
            ->where('challenge_id', $challenge->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($attempt->status === 'in_progress') {
            if (now()->greaterThanOrEqualTo($attempt->expires_at)) {
                $this->finalizeMcqAttempt($attempt, 'expired', $unlockService, $gamification);
                $attempt->refresh()->load('answers.selectedOption');
            } else {
                return redirect()->route('challenges.quiz', [
                    'slug' => $slug,
                    'challenge' => $challenge->id,
                ]);
            }
        }

        $answers = $attempt->answers->keyBy('challenge_question_id');
        $questionResults = $this->orderedQuestionsForAttempt($attempt)
            ->map(function (ChallengeQuestion $question) use ($answers): array {
                $answer = $answers->get($question->id);
                $selectedOption = $answer?->selectedOption;
                $correctOption = $question->options->first(fn (ChallengeOption $option): bool => (bool) $option->is_correct);

                return [
                    'question' => $question,
                    'selected_option' => $selectedOption,
                    'correct_option' => $correctOption,
                    'is_correct' => $selectedOption ? (bool) $selectedOption->is_correct : false,
                ];
            });

        $attemptHistory = ChallengeAttempt::query()
            ->where('user_id', Auth::id())
            ->where('challenge_id', $challenge->id)
            ->whereIn('status', ['submitted', 'expired', 'disqualified'])
            ->orderByDesc('attempt_no')
            ->orderByDesc('id')
            ->get();

        return view('student.challenge-result', compact(
            'slug',
            'challenge',
            'attempt',
            'questionResults',
            'attemptHistory'
        ));
    }

    public function autosaveQuiz(Request $request, $slug, $challenge_id): JsonResponse
    {
        $request->validate([
            'attempt_id' => ['required', 'integer'],
            'question_id' => ['required', 'integer'],
            'option_id' => ['nullable', 'integer'],
            'seq' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
        ]);

        $challenge = Challenge::findOrFail($challenge_id);
        // Attempt-scoped endpoint: the owned attempt row below is the gate, so an
        // attempt started before its version was deactivated keeps autosaving.
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);

        $questionId = (int) $request->input('question_id');
        $optionId = $request->filled('option_id') ? (int) $request->input('option_id') : null;
        $clientSeq = $request->filled('seq') ? (int) $request->input('seq') : null;

        return DB::transaction(function () use ($request, $challenge, $questionId, $optionId, $clientSeq) {
            $attempt = ChallengeAttempt::where('id', (int) $request->input('attempt_id'))
                ->where('challenge_id', $challenge->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== 'in_progress') {
                return response()->json([
                    'ok' => false,
                    'status' => $attempt->status,
                    'message' => 'This attempt is already finished.',
                ], 409);
            }

            if (now()->greaterThanOrEqualTo($attempt->expires_at)) {
                return response()->json([
                    'ok' => false,
                    'status' => 'expired',
                    'message' => 'Time is already up. Please submit to finalize the attempt.',
                ], 409);
            }

            $questionOrder = $this->asArray($attempt->question_order);
            abort_unless(in_array($questionId, $questionOrder, true), 422, 'Question does not belong to this attempt.');

            if ($optionId !== null) {
                $validOption = ChallengeOption::where('id', $optionId)
                    ->where('challenge_question_id', $questionId)
                    ->exists();
                abort_unless($validOption, 422, 'Selected option does not belong to this question.');
            }

            // Answers are one row per question, and the attempt row lock above
            // serializes every writer of this attempt, so concurrent saves of
            // different questions cannot overwrite each other.
            $answer = ChallengeAttemptAnswer::firstOrNew([
                'challenge_attempt_id' => $attempt->id,
                'challenge_question_id' => $questionId,
            ]);

            // A delayed request that arrives after a newer edit of the same
            // question must not roll the answer back.
            $stale = $clientSeq !== null && $answer->exists && $clientSeq < (int) $answer->client_seq;

            if (! $stale) {
                $answer->selected_option_id = $optionId;
                $answer->answered_at = $optionId ? now() : null;
                if ($clientSeq !== null) {
                    $answer->client_seq = $clientSeq;
                }
                $answer->save();
            }

            $attempt->forceFill(['last_seen_at' => now()])->save();

            return response()->json([
                'ok' => true,
                'stale' => $stale,
                'seq' => (int) $answer->client_seq,
                'answered_count' => $attempt->answers()->whereNotNull('selected_option_id')->count(),
                'remaining_seconds' => max(0, now()->diffInSeconds($attempt->expires_at, false)),
            ]);
        });
    }

    public function heartbeatQuiz(Request $request, $slug, $challenge_id): JsonResponse
    {
        $request->validate([
            'attempt_id' => ['required', 'integer'],
        ]);

        $challenge = Challenge::findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);

        $attempt = $this->currentUserAttempt($challenge, (int) $request->input('attempt_id'));
        $attempt->forceFill(['last_seen_at' => now()])->save();

        return response()->json([
            'ok' => true,
            'status' => $attempt->status,
            'server_now_ms' => (int) now()->getTimestampMs(),
            'expires_at_ms' => ($attempt->expires_at->getTimestamp() * 1000),
            'remaining_seconds' => max(0, now()->diffInSeconds($attempt->expires_at, false)),
            'should_submit' => $attempt->status === 'in_progress' && now()->greaterThanOrEqualTo($attempt->expires_at),
        ]);
    }

    public function logQuizEvent(Request $request, $slug, $challenge_id): JsonResponse
    {
        $request->validate([
            'attempt_id' => ['required', 'integer'],
            'event_type' => ['required', 'string', 'in:' . implode(',', array_keys(self::QUIZ_EVENT_SEVERITY))],
            'details' => ['nullable', 'array'],
            'details.answered' => ['nullable', 'integer', 'min:0', 'max:250'],
        ]);

        $challenge = Challenge::findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);

        return DB::transaction(function () use ($request, $challenge) {
            $attempt = ChallengeAttempt::where('id', (int) $request->input('attempt_id'))
                ->where('challenge_id', $challenge->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== 'in_progress') {
                return response()->json(['ok' => true, 'ignored' => true]);
            }

            $eventType = (string) $request->input('event_type');
            $severity = self::QUIZ_EVENT_SEVERITY[$eventType];
            $details = ['answered' => (int) $request->input('details.answered', 0)];

            ChallengeAttemptEvent::create([
                'challenge_attempt_id' => $attempt->id,
                'event_type' => $eventType,
                'severity' => $severity,
                'details' => $details,
                'occurred_at' => now(),
            ]);

            $increment = $severity === 'high' ? 3 : ($severity === 'medium' ? 2 : 1);
            $attempt->suspicious_event_count += $increment;

            if ($attempt->suspicious_event_count >= self::SUSPICIOUS_EVENT_LIMIT && $attempt->is_leaderboard_eligible) {
                $attempt->is_leaderboard_eligible = false;
                $attempt->notes = trim(($attempt->notes ?? '') . "\nLeaderboard eligibility removed because the suspicious event limit was reached.");
            }

            $attempt->save();

            return response()->json([
                'ok' => true,
                'suspicious_event_count' => $attempt->suspicious_event_count,
                'is_leaderboard_eligible' => (bool) $attempt->is_leaderboard_eligible,
            ]);
        });
    }

    public function submitQuiz(Request $request, $slug, $challenge_id, ChallengePathUnlockService $unlockService, GamificationService $gamification)
    {
        $challenge = Challenge::with('questions.options', 'category')->findOrFail($challenge_id);
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService, $challenge);

        // The owned attempt row is the gate: an attempt started before this
        // version was deactivated can still be submitted (and re-submitted
        // idempotently). New attempts on an inactive version are refused in
        // getOrCreateMcqAttempt().
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false, false);

        $request->validate([
            'attempt_id' => ['required', 'integer'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'integer'],
        ]);

        $result = DB::transaction(function () use ($request, $challenge, $unlockService, $gamification) {
            $attempt = ChallengeAttempt::where('id', (int) $request->input('attempt_id'))
                ->where('challenge_id', $challenge->id)
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->firstOrFail();

            if ($attempt->status !== 'in_progress') {
                return [
                    'message' => 'This attempt has already been finalized. Your saved result is still preserved.',
                    'attempt_id' => $attempt->id,
                ];
            }

            // Decide expiry from the server clock BEFORE touching answers. After
            // the deadline the posted answers are ignored and the attempt is
            // graded from what autosave stored while the attempt was still open.
            $expired = now()->greaterThanOrEqualTo($attempt->expires_at);

            if (! $expired) {
                $this->persistPostedAnswers($attempt, $request->input('answers', []));
            }

            return $this->finalizeMcqAttempt($attempt, $expired ? 'expired' : 'submitted', $unlockService, $gamification);
        });

        return redirect()->route('challenges.quiz.result', [
            'slug' => $slug,
            'challenge' => $challenge->id,
            'attempt' => $result['attempt_id'],
        ])->with('success', $result['message']);
    }

    public function showCodingQuiz($slug, $challenge_id)
    {
        return app(\App\Http\Controllers\CodingQuizController::class)
            ->show($slug, Challenge::findOrFail($challenge_id));
    }

    public function submitCodingQuiz(Request $request, $slug, $challenge_id)
    {
        $unlockService = app(ChallengePathUnlockService::class);
        $challenge = Challenge::with('category')->findOrFail($challenge_id);
        $this->ensurePathIsUnlocked($slug, 'coding', $unlockService, $challenge);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, true);

        $totalQuestions = $challenge->codingQuestions()->count();

        $passedCount = DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.user_id', Auth::id())
            ->where('coding_questions.challenge_id', $challenge->id)
            ->where('coding_submissions.status', 'passed')
            ->where('coding_submissions.voided', false)
            ->distinct('coding_submissions.coding_question_id')
            ->count('coding_submissions.coding_question_id');

        $totalXp = DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.user_id', Auth::id())
            ->where('coding_questions.challenge_id', $challenge->id)
            ->where('coding_submissions.voided', false)
            ->select(DB::raw('MAX(coding_submissions.xp_earned) as best_xp'))
            ->groupBy('coding_submissions.coding_question_id')
            ->get()
            ->sum('best_xp');

        $message = $passedCount >= $totalQuestions
            ? "Challenge Complete! All {$totalQuestions} problems solved. You earned {$totalXp} XP."
            : "Submitted! {$passedCount}/{$totalQuestions} problems fully passed. Keep going!";

        return redirect()->route('challenges.coding.map', $slug)->with('success', $message);
    }

    public function enrollOrganization(Request $request)
    {
        return back()->withErrors([
            'invite_code' => 'Institution code enrollment is disabled. Ask your instructor to add your registered Gmail/email to the class.',
        ]);
    }

    private function getOrCreateMcqAttempt(Challenge $challenge): ChallengeAttempt
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($challenge, $userId) {
            // Serialize attempt creation for this user. Locking an absent attempt row
            // alone cannot prevent two requests from creating attempt number one.
            DB::table('users')->where('id', $userId)->lockForUpdate()->first();

            // Coordinate with administrator edits/deactivation so a question
            // snapshot cannot be created while the challenge is being changed.
            $challenge = Challenge::query()
                ->whereKey($challenge->id)
                ->where('is_coding_challenge', false)
                ->lockForUpdate()
                ->firstOrFail();

            $activeAttempt = ChallengeAttempt::where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($activeAttempt) {
                // Continuing an existing attempt is allowed even when a newer
                // version was published in the meantime.
                return $activeAttempt;
            }

            // Only the active version accepts NEW attempts. The flag is read
            // from the row locked above, so it cannot race with publication.
            abort_unless((bool) $challenge->is_active, 404);

            $hasRankedAttempt = ChallengeAttempt::where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->where('is_ranked', true)
                ->whereIn('status', ['submitted', 'expired', 'disqualified'])
                ->exists();

            $hasLegacyRankedRecord = DB::table('challenge_user')
                ->where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->exists();

            $isRanked = ! $hasRankedAttempt && ! $hasLegacyRankedRecord;
            $attemptNo = ((int) ChallengeAttempt::where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->max('attempt_no')) + 1;

            $questionIds = ChallengeQuestion::where('challenge_id', $challenge->id)
                ->pluck('id')
                ->shuffle()
                ->values()
                ->map(fn ($id) => (int) $id)
                ->all();

            abort_if(empty($questionIds), 422, 'This challenge has no questions yet. Please contact your instructor.');

            $optionOrder = [];
            foreach ($questionIds as $questionId) {
                $optionOrder[$questionId] = ChallengeOption::where('challenge_question_id', $questionId)
                    ->pluck('id')
                    ->shuffle()
                    ->values()
                    ->map(fn ($id) => (int) $id)
                    ->all();
            }

            $startedAt = now();
            $timeLimit = max(60, (int) $challenge->time_limit_seconds);

            $attempt = ChallengeAttempt::create([
                'user_id' => $userId,
                'challenge_id' => $challenge->id,
                'attempt_no' => $attemptNo,
                'mode' => $isRanked ? 'ranked' : 'practice',
                'status' => 'in_progress',
                'started_at' => $startedAt,
                'expires_at' => $startedAt->copy()->addSeconds($timeLimit),
                'last_seen_at' => $startedAt,
                'time_limit_seconds' => $timeLimit,
                'total_questions' => count($questionIds),
                'is_ranked' => $isRanked,
                'is_leaderboard_eligible' => $isRanked,
                'question_order' => $questionIds,
                'option_order' => $optionOrder,
                'notes' => $isRanked
                    ? 'First valid attempt. Eligible for leaderboard unless suspicious activity is detected.'
                    : 'Practice attempt. Resume is allowed, but it is not leaderboard eligible and does not award leaderboard XP.',
            ]);

            foreach ($questionIds as $questionId) {
                ChallengeAttemptAnswer::create([
                    'challenge_attempt_id' => $attempt->id,
                    'challenge_question_id' => $questionId,
                ]);
            }

            return $attempt;
        });
    }

    private function orderedQuestionsForAttempt(ChallengeAttempt $attempt): \Illuminate\Support\Collection
    {
        $questionIds = $this->asArray($attempt->question_order);
        $optionOrder = $this->asArray($attempt->option_order);

        if (empty($questionIds)) {
            $questionIds = ChallengeQuestion::where('challenge_id', $attempt->challenge_id)->pluck('id')->all();
        }

        $questions = ChallengeQuestion::whereIn('id', $questionIds)->get()->keyBy('id');
        $optionsByQuestion = ChallengeOption::whereIn('challenge_question_id', $questionIds)
            ->get()
            ->groupBy('challenge_question_id');

        return collect($questionIds)
            ->map(function ($questionId) use ($questions, $optionsByQuestion, $optionOrder) {
                $question = $questions->get((int) $questionId);
                if (!$question) {
                    return null;
                }

                $availableOptions = $optionsByQuestion->get((int) $questionId, collect())->keyBy('id');
                $orderedOptionIds = $optionOrder[$questionId] ?? $optionOrder[(string) $questionId] ?? [];

                $orderedOptions = collect($orderedOptionIds)
                    ->map(fn ($optionId) => $availableOptions->get((int) $optionId))
                    ->filter()
                    ->values();

                if ($orderedOptions->isEmpty()) {
                    $orderedOptions = $availableOptions->values();
                }

                $question->setRelation('options', $orderedOptions);
                return $question;
            })
            ->filter()
            ->values();
    }

    private function persistPostedAnswers(ChallengeAttempt $attempt, array $answers): void
    {
        $questionOrder = $this->asArray($attempt->question_order);

        foreach ($answers as $questionId => $optionId) {
            $questionId = (int) $questionId;
            $optionId = $optionId !== null && $optionId !== '' ? (int) $optionId : null;

            if (!in_array($questionId, $questionOrder, true)) {
                continue;
            }

            if ($optionId !== null) {
                $validOption = ChallengeOption::where('id', $optionId)
                    ->where('challenge_question_id', $questionId)
                    ->exists();

                if (!$validOption) {
                    continue;
                }
            }

            ChallengeAttemptAnswer::updateOrCreate(
                [
                    'challenge_attempt_id' => $attempt->id,
                    'challenge_question_id' => $questionId,
                ],
                [
                    'selected_option_id' => $optionId,
                    'answered_at' => $optionId ? now() : null,
                ]
            );
        }
    }

    private function finalizeMcqAttempt(ChallengeAttempt $attempt, string $status, ChallengePathUnlockService $unlockService, GamificationService $gamification): array
    {
        return DB::transaction(function () use ($attempt, $status, $unlockService, $gamification) {
        $attempt = ChallengeAttempt::with(['challenge.category', 'answers.selectedOption'])
            ->where('id', $attempt->id)
            ->where('user_id', Auth::id())
            ->lockForUpdate()
            ->firstOrFail();

        if ($attempt->status !== 'in_progress') {
            return [
                'message' => 'This attempt was already finalized.',
                'attempt_id' => $attempt->id,
            ];
        }

        $challenge = $attempt->challenge;
        // Score against the immutable question snapshot captured when the attempt
        // started, not questions an instructor may add while it is in progress.
        $totalQuestions = max(0, (int) $attempt->total_questions);
        $correctCount = 0;

        foreach ($attempt->answers as $answer) {
            if ($answer->selectedOption && $answer->selectedOption->is_correct) {
                $correctCount++;
            }
        }

        $timeTaken = min(
            (int) $attempt->time_limit_seconds,
            max(0, $attempt->started_at->diffInSeconds(now()))
        );

        $scorePercentage = $totalQuestions > 0 ? ($correctCount / $totalQuestions) : 0;
        $passed = $scorePercentage >= 0.7;

        $leaderboardEligible = (bool) $attempt->is_ranked
            && (bool) $attempt->is_leaderboard_eligible
            && $attempt->suspicious_event_count < self::SUSPICIOUS_EVENT_LIMIT
            && $status !== 'disqualified';

        // The first (ranked) attempt always earns XP for the questions the
        // student answered correctly. Switching tabs or apps five or more times
        // used to wipe the whole reward, so a learner who alt-tabbed a few
        // times finished a challenge with no XP, no achievement and no rank
        // change. Such an attempt now keeps its score XP but loses the speed
        // bonus, stays marked as not leaderboard eligible, and its events are
        // still listed for the instructor. Practice retakes and disqualified
        // attempts earn nothing, as before.
        $earnedXp = 0;
        // Class work (an instructor-built challenge or the University Student
        // level) gives no XP: XP comes from platform content only.
        if ($attempt->is_ranked && $status !== 'disqualified' && XpPolicy::challengeAwardsXp($challenge)) {
            $earnedXp = (int) round($challenge->base_xp * $scorePercentage);

            if ($passed && $leaderboardEligible) {
                $secondsSaved = max(0, (int) $attempt->time_limit_seconds - $timeTaken);
                $earnedXp += (int) round($secondsSaved * 2);
            }
        }

        $attempt->forceFill([
            'status' => $status,
            'submitted_at' => now(),
            'time_taken_seconds' => $timeTaken,
            'score' => $correctCount,
            'total_questions' => $totalQuestions,
            'xp_awarded' => $earnedXp,
            'is_leaderboard_eligible' => $leaderboardEligible,
        ])->save();

        $this->recordLearningCompletion($attempt, $earnedXp);

        $user = Auth::user();
        $xpBefore = (int) $user->xp;

        if ($earnedXp > 0) {
            $user->increment('xp', $earnedXp);
        }

        // Any finished attempt can make this module qualify for the next
        // level (retakes included), so check after each one. The notice is
        // sent once per level.
        if ($status !== 'disqualified') {
            $unlockService->notifyExceptionalUnlocks($user, 'mcq');
        }

        // Missions, streaks, achievements and the rank-up notice follow every
        // finished attempt (practice retakes count as activity too); XP-based
        // rules only move when XP was actually earned above.
        $achievements = $status === 'disqualified'
            ? []
            : $gamification->awardForMcqChallenge(
                $user,
                $challenge,
                $correctCount,
                $totalQuestions,
                $timeTaken,
                $passed,
                $xpBefore
            );

        return [
            'message' => $this->finishedAttemptMessage($attempt, $correctCount, $totalQuestions, $earnedXp, $leaderboardEligible, $achievements, $status),
            'attempt_id' => $attempt->id,
        ];
        });
    }

    private function recordLearningCompletion(ChallengeAttempt $attempt, int $earnedXp): void
    {
        if (! $attempt->is_ranked) {
            return;
        }

        $existing = DB::table('challenge_user')
            ->where('user_id', $attempt->user_id)
            ->where('challenge_id', $attempt->challenge_id)
            ->lockForUpdate()
            ->first();

        if (!$existing) {
            DB::table('challenge_user')->insert([
                'user_id' => $attempt->user_id,
                'challenge_id' => $attempt->challenge_id,
                'score' => $attempt->score,
                'time_taken_seconds' => $attempt->time_taken_seconds ?? $attempt->time_limit_seconds,
                'xp_awarded' => $earnedXp,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return;
        }

        $newScoreIsBetter = (int) $attempt->score > (int) $existing->score;
        $sameScoreButFaster = (int) $attempt->score === (int) $existing->score
            && (int) ($attempt->time_taken_seconds ?? $attempt->time_limit_seconds) < (int) $existing->time_taken_seconds;

        if ($newScoreIsBetter || $sameScoreButFaster || $earnedXp > (int) $existing->xp_awarded) {
            DB::table('challenge_user')
                ->where('id', $existing->id)
                ->update([
                    'score' => $newScoreIsBetter || $sameScoreButFaster ? $attempt->score : $existing->score,
                    'time_taken_seconds' => $newScoreIsBetter || $sameScoreButFaster
                        ? ($attempt->time_taken_seconds ?? $attempt->time_limit_seconds)
                        : $existing->time_taken_seconds,
                    'xp_awarded' => max((int) $existing->xp_awarded, $earnedXp),
                    'updated_at' => now(),
                ]);
        }
    }

    private function finishedAttemptMessage(ChallengeAttempt $attempt, int $correct, int $total, int $xp, bool $leaderboardEligible, array $achievements, string $status): string
    {
        $modeText = $attempt->is_ranked ? 'Ranked attempt' : 'Practice attempt';
        $statusText = $status === 'expired' ? 'Time expired. Your saved answers were submitted.' : 'Challenge submitted.';
        $xpText = $xp > 0 ? " You earned {$xp} XP." : ' No leaderboard XP was awarded for this attempt.';

        if (!$leaderboardEligible && $attempt->is_ranked) {
            $xpText = $xp > 0
                ? " You earned {$xp} XP for your correct answers. The speed bonus was not added because you left the challenge page several times."
                : ' This attempt was saved, but no XP was awarded.';
        }

        if (!$attempt->is_ranked) {
            $xpText = ' Practice mode does not affect the leaderboard or award leaderboard XP.';
        }

        if (! XpPolicy::challengeAwardsXp($attempt->challenge)) {
            $xpText = ' Class challenges do not award XP.';
        }

        $message = "{$statusText} {$modeText}. Score: {$correct}/{$total}.{$xpText}";

        if (!empty($achievements)) {
            $badgeText = collect($achievements)
                ->map(fn ($badge) => ($badge['name'] ?? 'Achievement'))
                ->implode(', ');
            $message .= ' Achievements unlocked: ' . $badgeText . '.';
        }

        return $message;
    }

    private function currentUserAttempt(Challenge $challenge, int $attemptId): ChallengeAttempt
    {
        return ChallengeAttempt::where('id', $attemptId)
            ->where('user_id', Auth::id())
            ->where('challenge_id', $challenge->id)
            ->firstOrFail();
    }

    private function asArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function ensurePathIsUnlocked(string $slug, string $track, ChallengePathUnlockService $unlockService, ?Challenge $challenge = null): void
    {
        $this->ensureUniversityStudentEnrollment($slug);

        $lockInfo = $unlockService->lockInfo(Auth::user(), $slug, $track);

        if ($lockInfo['unlocked'] ?? false) {
            return;
        }

        // A challenge an instructor has opened for one of the learner's classes
        // is reachable through that class assignment, even when the learner has
        // not unlocked the difficulty path it belongs to yet. The challenge
        // must still sit in this path, so the URL cannot be used to reach any
        // other path's challenges.
        if ($challenge !== null && $this->isOpenClassChallengeInPath($challenge, $slug)) {
            return;
        }

        abort(403, $lockInfo['reason'] ?? 'This difficulty path is locked.');
    }

    /**
     * Inside an open level, modules open one at a time (DataSensei Updates 4):
     * a new level starts with its first module and the next one opens after
     * the learner passes the one before it. Modules already worked on stay
     * open, and a class assignment opens its challenge whatever the learner's
     * place in the level. Checked where an attempt is started.
     */
    private function ensureModuleIsOpen(Challenge $challenge, string $slug): void
    {
        $user = Auth::user();

        if ($user === null || app(ChallengeModuleAccessService::class)->isOpen($user, $challenge)) {
            return;
        }

        abort_unless(
            $this->isOpenClassChallengeInPath($challenge, $slug),
            403,
            'Pass the previous module in this level first. Modules open one at a time.'
        );
    }

    private function ensureChallengeBelongsToSlug(Challenge $challenge, string $slug, bool $coding, bool $requireActive = true): void
    {
        $this->ensureUniversityStudentEnrollment($slug);
        $challenge->loadMissing('category');

        abort_unless($challenge->category && $challenge->category->slug === $slug, 404);
        abort_unless((bool) $challenge->is_coding_challenge === $coding, 404);
        if ($requireActive) {
            abort_unless((bool) $challenge->is_active, 404);
        }

        // An instructor-built challenge is class work: it exists for this
        // learner only while one of their classes has it open, so a direct
        // URL cannot reach it otherwise.
        if ($challenge->isInstructorOwned()) {
            abort_unless(in_array((int) $challenge->id, $this->assignedInstructorChallengeIds(), true), 404);
        }
    }

    /**
     * Platform challenges, plus the instructor-built ones that an open class
     * assignment currently gives to this learner.
     */
    private function visibleToStudent($query): void
    {
        $query->platform();

        $assigned = $this->assignedInstructorChallengeIds();
        if ($assigned !== []) {
            $query->orWhere(function ($builder) use ($assigned): void {
                $builder->where('visibility', Challenge::VISIBILITY_INSTRUCTOR)
                    ->whereIn('id', $assigned);
            });
        }
    }

    /**
     * The challenges an instructor has opened for the learner's classes, for
     * the "From your classes" list on the challenge pages. Class work used to
     * appear only on the dashboard, and inside the path map it sat behind the
     * learner's path progress, so a student could not find or open it.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function classChallengeCards(bool $coding)
    {
        $user = Auth::user();

        if ($user === null) {
            return collect();
        }

        $classIds = $user->classesAsStudent()->active()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return collect();
        }

        $assignments = ClassChallengeAssignment::query()
            ->openNow()
            ->whereIn('class_id', $classIds)
            ->whereHas('challenge', fn ($query) => $query
                ->where('is_coding_challenge', $coding)
                ->where('is_active', true))
            ->with(['challenge.category', 'class:id,name'])
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->get()
            ->filter(fn (ClassChallengeAssignment $assignment): bool => $assignment->challenge?->category !== null)
            ->unique('challenge_id')
            ->values();

        if ($assignments->isEmpty()) {
            return collect();
        }

        $challengeIds = $assignments->pluck('challenge_id')->map(fn ($id): int => (int) $id)->all();
        $finished = $this->finishedChallengeIds((int) $user->id, $challengeIds, $coding);

        return $assignments->map(function (ClassChallengeAssignment $assignment) use ($coding, $finished): array {
            $challenge = $assignment->challenge;
            $slug = $challenge->category->slug;

            return [
                'title' => $challenge->title,
                'class_name' => $assignment->class?->name,
                'description' => $challenge->description,
                'finished' => in_array((int) $challenge->id, $finished, true),
                'url' => $coding
                    ? route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $challenge->id])
                    : route('challenges.quiz', ['slug' => $slug, 'challenge' => $challenge->id]),
            ];
        });
    }

    /**
     * Of the given challenges, the ones this learner has finished: an MCQ with
     * a submitted attempt, or a coding challenge whose problems all passed.
     *
     * @param  array<int, int>  $challengeIds
     * @return array<int, int>
     */
    private function finishedChallengeIds(int $userId, array $challengeIds, bool $coding): array
    {
        if ($challengeIds === []) {
            return [];
        }

        if (! $coding) {
            return ChallengeAttempt::query()
                ->where('user_id', $userId)
                ->whereIn('challenge_id', $challengeIds)
                ->whereIn('status', ['submitted', 'expired'])
                ->distinct()
                ->pluck('challenge_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        }

        $questionCounts = DB::table('coding_questions')
            ->whereIn('challenge_id', $challengeIds)
            ->groupBy('challenge_id')
            ->select('challenge_id', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'challenge_id');

        $passedCounts = DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.user_id', $userId)
            ->where('coding_submissions.status', 'passed')
            ->where('coding_submissions.voided', false)
            ->whereIn('coding_questions.challenge_id', $challengeIds)
            ->groupBy('coding_questions.challenge_id')
            ->select('coding_questions.challenge_id', DB::raw('COUNT(DISTINCT coding_submissions.coding_question_id) as passed'))
            ->pluck('passed', 'challenge_id');

        return collect($questionCounts)
            ->filter(fn ($total, $challengeId): bool => (int) $total > 0
                && (int) ($passedCounts[$challengeId] ?? 0) >= (int) $total)
            ->keys()
            ->map(fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    /** The challenge is in this path and one of the learner's classes has it open. */
    private function isOpenClassChallengeInPath(Challenge $challenge, string $slug): bool
    {
        $challenge->loadMissing('category');

        return $challenge->category !== null
            && $challenge->category->slug === $slug
            && in_array((int) $challenge->id, $this->assignedInstructorChallengeIds(), true);
    }

    /**
     * Ids from the given challenges that an open class assignment gives to
     * the learner (platform-pool or instructor-built).
     *
     * @return array<int, int>
     */
    private function openClassChallengeIdsAmong($challenges): array
    {
        $open = $this->assignedInstructorChallengeIds();

        if ($open === []) {
            return [];
        }

        return collect($challenges)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => in_array($id, $open, true))
            ->values()
            ->all();
    }

    /**
     * Ids of the challenges (platform-pool or instructor-built) that a
     * published, currently open class assignment gives to the signed-in
     * learner through an active class. Cached for the request.
     *
     * @return array<int, int>
     */
    private function assignedInstructorChallengeIds(): array
    {
        // Kept on the request (not the controller, which the router may reuse).
        $attributes = request()->attributes;
        $key = 'datasensei.open_class_challenge_ids.'.(int) Auth::id();

        if (! $attributes->has($key)) {
            $attributes->set($key, $this->loadOpenClassChallengeIds());
        }

        return $attributes->get($key);
    }

    private function loadOpenClassChallengeIds(): array
    {
        $user = Auth::user();

        if ($user === null) {
            return [];
        }

        $classIds = $user->classesAsStudent()->active()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return [];
        }

        return ClassChallengeAssignment::query()
            ->openNow()
            ->whereIn('class_id', $classIds)
            ->pluck('challenge_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function ensureUniversityStudentEnrollment(string $slug): void
    {
        if ($slug !== 'university-student') {
            return;
        }

        abort_unless($this->hasActiveClassEnrollment(), 404);
    }

    private function hasActiveClassEnrollment(): bool
    {
        $user = Auth::user();

        return $user !== null
            && $user->classesAsStudent()
                ->active()
                ->exists();
    }
}
