<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeAttemptAnswer;
use App\Models\ChallengeAttemptEvent;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Services\ChallengePathUnlockService;
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

        return view('student.challenges', compact('categories', 'hasUniversity', 'pathLocks', 'exceptionalNotifications'));
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

        return view('student.coding-challenges', compact('categories', 'hasUniversity', 'pathLocks', 'exceptionalNotifications'));
    }

    public function map($slug, ChallengePathUnlockService $unlockService)
    {
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService);

        $category = ChallengeCategory::where('slug', $slug)->firstOrFail();

        $challenges = Challenge::where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', 0)
            ->where('is_active', true)
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

            $rankedAttempts = $finishedAttempts->map(
                fn ($attempts) => $attempts
                    ->where('is_ranked', true)
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
                $attempts = $rankedAttempts->get($ch->id, collect());
                $best = $attempts->sortByDesc(fn (ChallengeAttempt $attempt): float =>
                    (float) $attempt->score / max(1, (int) $attempt->total_questions)
                )->first();

                if ($best) {
                    $bestScores[$ch->id] = ['score' => $best->score, 'xp' => $best->xp_awarded];
                    if (((int) $best->score / max(1, (int) $best->total_questions)) >= 0.70) {
                        $completedChallengeIds[] = $ch->id;
                    }
                    continue;
                }

                if ($attemptedChallengeIds->has($ch->id)) {
                    continue;
                }

                // Preserve pre-attempt-table progress without allowing newer
                // practice attempts to count as ranked completion.
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

        return view('student.challenges-map', compact(
            'slug', 'category', 'challenges', 'completedChallengeIds', 'bestScores', 'exceptionalNotifications', 'activeAttemptIds', 'latestResultAttemptIds'
        ));
    }

    public function codingMap($slug, ChallengePathUnlockService $unlockService)
    {
        $this->ensurePathIsUnlocked($slug, 'coding', $unlockService);

        $category = ChallengeCategory::where('slug', $slug)->firstOrFail();

        $challenges = Challenge::where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', 1)
            ->where('is_active', true)
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

        return view('student.coding-challenges-map', compact(
            'slug', 'category', 'challenges',
            'completedChallengeIds', 'inProgressChallengeIds', 'bestScores', 'exceptionalNotifications'
        ));
    }

    public function showQuiz($slug, $challenge_id, ChallengePathUnlockService $unlockService, GamificationService $gamification)
    {
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService);

        $challenge = Challenge::with('category')->findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false);

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

        $serverNowMs = (now()->getTimestamp() * 1000);
        $expiresAtMs = ($attempt->expires_at->getTimestamp() * 1000);
        $remainingSeconds = max(0, (int) floor(($expiresAtMs - $serverNowMs) / 1000));

        return view('student.challenge-quiz', compact(
            'slug',
            'challenge',
            'attempt',
            'savedAnswers',
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
        ]);

        $challenge = Challenge::findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false);

        $questionId = (int) $request->input('question_id');
        $optionId = $request->filled('option_id') ? (int) $request->input('option_id') : null;

        return DB::transaction(function () use ($request, $challenge, $questionId, $optionId) {
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

            $attempt->forceFill(['last_seen_at' => now()])->save();

            return response()->json([
                'ok' => true,
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
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false);

        $attempt = $this->currentUserAttempt($challenge, (int) $request->input('attempt_id'));
        $attempt->forceFill(['last_seen_at' => now()])->save();

        return response()->json([
            'ok' => true,
            'status' => $attempt->status,
            'server_now_ms' => (now()->getTimestamp() * 1000),
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
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false);

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
        $this->ensurePathIsUnlocked($slug, 'mcq', $unlockService);

        $challenge = Challenge::with('questions.options', 'category')->findOrFail($challenge_id);
        $this->ensureChallengeBelongsToSlug($challenge, $slug, false);

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

            $this->persistPostedAnswers($attempt, $request->input('answers', []));

            $status = now()->greaterThanOrEqualTo($attempt->expires_at) ? 'expired' : 'submitted';

            return $this->finalizeMcqAttempt($attempt, $status, $unlockService, $gamification);
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
        $this->ensurePathIsUnlocked($slug, 'coding', $unlockService);

        $challenge = Challenge::with('category')->findOrFail($challenge_id);
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
                ->where('is_active', true)
                ->lockForUpdate()
                ->firstOrFail();

            $activeAttempt = ChallengeAttempt::where('user_id', $userId)
                ->where('challenge_id', $challenge->id)
                ->where('status', 'in_progress')
                ->lockForUpdate()
                ->latest('id')
                ->first();

            if ($activeAttempt) {
                return $activeAttempt;
            }

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

        $earnedXp = 0;
        if ($leaderboardEligible) {
            $earnedXp = (int) round($challenge->base_xp * $scorePercentage);

            if ($passed) {
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

        if ($earnedXp > 0) {
            $user->increment('xp', $earnedXp);
        }

        $achievements = [];
        if ($leaderboardEligible) {
            if ($passed) {
                $unlockService->notifyExceptionalUnlocks($user, 'mcq');
            }

            $achievements = $gamification->awardForMcqChallenge(
                $user,
                $challenge,
                $correctCount,
                $totalQuestions,
                $timeTaken,
                $passed
            );
        }

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
            $xpText = ' This attempt was saved, but it is not leaderboard eligible because suspicious activity was detected.';
        }

        if (!$attempt->is_ranked) {
            $xpText = ' Practice mode does not affect the leaderboard or award leaderboard XP.';
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

    private function ensurePathIsUnlocked(string $slug, string $track, ChallengePathUnlockService $unlockService): void
    {
        $this->ensureUniversityStudentEnrollment($slug);

        $lockInfo = $unlockService->lockInfo(Auth::user(), $slug, $track);

        if (!($lockInfo['unlocked'] ?? false)) {
            abort(403, $lockInfo['reason'] ?? 'This difficulty path is locked.');
        }
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
