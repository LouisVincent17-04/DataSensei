<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\CodingSubmission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChallengePathUnlockService
{
    private const PASSING_PERCENT = 70.0;
    private const EXCEPTIONAL_PERCENT = 90.0;
    private const EXCEPTIONAL_TIME_RATIO = 0.70;
    private const EXCEPTIONAL_AVG_ATTEMPTS = 2.50;

    private const PATHS = [
        ['slug' => 'newbie', 'name' => 'Newbie'],
        ['slug' => 'university-student', 'name' => 'University Student'],
        ['slug' => 'intermediate', 'name' => 'Intermediate'],
        ['slug' => 'advanced', 'name' => 'Advanced'],
        ['slug' => 'professional', 'name' => 'Professional'],
    ];

    private array $summaryCache = [];
    private array $locksCache = [];
    private array $activeEnrollmentCache = [];

    public function buildPathLocks(?User $user, string $track = 'mcq'): array
    {
        $track = $this->normalizeTrack($track);
        $cacheKey = ($user?->id ?? 0).':'.$track;

        if (array_key_exists($cacheKey, $this->locksCache)) {
            return $this->locksCache[$cacheKey];
        }

        $locks = [];

        foreach (self::PATHS as $index => $path) {
            $slug = $path['slug'];
            $name = $path['name'];

            if (!$user) {
                $locks[$slug] = $index === 0
                    ? $this->unlocked('Newbie is always available.', 'starter')
                    : $this->locked('Log in first to unlock this path.');
                continue;
            }

            if ($index === 0) {
                $locks[$slug] = $this->unlocked('Newbie is always available.', 'starter');
                continue;
            }

            if ($slug === 'university-student') {
                if ($this->hasActiveClassEnrollment($user)) {
                    $locks[$slug] = $this->unlocked(
                        'Available through your active class enrollment.',
                        'class_enrollment'
                    );
                } else {
                    $locks[$slug] = $this->locked(
                        'Enroll in an active class to access University Student challenges.'
                    );
                }

                continue;
            }

            $previous = self::PATHS[$index - 1];
            $previousSummary = $this->performanceSummary($user, $previous['slug'], $track);

            if ($previousSummary['completed']) {
                $locks[$slug] = $this->unlocked('Unlocked by completing ' . $previous['name'] . '.', 'progression');
                continue;
            }

            if ($index >= 2) {
                $twoStepsBack = self::PATHS[$index - 2];
                $twoStepsBackSummary = $this->performanceSummary($user, $twoStepsBack['slug'], $track);

                if ($twoStepsBackSummary['exceptional']) {
                    $locks[$slug] = $this->unlocked(
                        'Exceptional performance in ' . $twoStepsBack['name'] . ' unlocked this early.',
                        'exceptional_bonus',
                        true,
                        $twoStepsBack['slug'],
                        $twoStepsBack['name'],
                        $twoStepsBackSummary
                    );
                    continue;
                }
            }

            $reason = 'Complete ' . $previous['name'] . ' to unlock ' . $name . '.';
            if ($index >= 2) {
                $twoStepsBack = self::PATHS[$index - 2];
                $reason .= ' Exceptional performance in ' . $twoStepsBack['name'] . ' can unlock it early.';
            }

            $locks[$slug] = $this->locked($reason);
        }

        return $this->locksCache[$cacheKey] = $locks;
    }

    public function canAccess(?User $user, string $slug, string $track = 'mcq'): bool
    {
        $locks = $this->buildPathLocks($user, $track);
        return (bool) ($locks[$slug]['unlocked'] ?? false);
    }

    public function lockInfo(?User $user, string $slug, string $track = 'mcq'): array
    {
        $locks = $this->buildPathLocks($user, $track);
        return $locks[$slug] ?? $this->locked('This path is locked.');
    }

    public function notifyExceptionalUnlocks(User $user, string $track = 'mcq'): array
    {
        // A submission may have been saved earlier in the same request. Clear only
        // this user's request cache so unlock checks use the newly persisted result.
        $this->forgetUserCache($user);

        $track = $this->normalizeTrack($track);
        $locks = $this->buildPathLocks($user, $track);
        $messages = [];

        if (!Schema::hasTable('notifications')) {
            return $messages;
        }

        foreach ($locks as $slug => $lock) {
            if (!($lock['bonus_unlocked'] ?? false)) {
                continue;
            }

            $sourceName = $lock['source_name'] ?? 'a previous path';
            $sourceSummary = $lock['source_summary'] ?? [];
            $pathName = $this->pathName($slug);
            $trackLabel = $track === 'coding' ? 'Coding Challenge' : 'MCQ Challenge';
            $score = isset($sourceSummary['average_score_percent'])
                ? round((float) $sourceSummary['average_score_percent']) . '%'
                : 'high';
            $time = isset($sourceSummary['average_time_ratio']) && $sourceSummary['average_time_ratio'] !== null
                ? round((float) $sourceSummary['average_time_ratio'] * 100) . '% of the time limit'
                : 'efficient completion time';

            $type = 'exceptional_unlock_' . $track . '_' . $slug;
            $text = "Exceptional {$trackLabel} performance detected! You completed {$sourceName} with {$score} average performance and used about {$time}. {$pathName} is now unlocked early.";

            $actionUrl = $track === 'coding'
                ? route('challenges.coding.map', ['slug' => $slug])
                : route('challenges.map', ['slug' => $slug]);

            $notification = app(StudentNotificationService::class)->send(
                $user,
                $type,
                'Challenge path unlocked',
                $text,
                $actionUrl,
                ['track' => $track, 'path' => $slug],
                $type
            );

            if ($notification?->wasRecentlyCreated) {
                $messages[] = $text;
            }
        }

        return $messages;
    }

    public function performanceSummary(User $user, string $slug, string $track = 'mcq'): array
    {
        $track = $this->normalizeTrack($track);
        $cacheKey = $user->id . ':' . $track . ':' . $slug;

        if (array_key_exists($cacheKey, $this->summaryCache)) {
            return $this->summaryCache[$cacheKey];
        }

        return $this->summaryCache[$cacheKey] = $track === 'coding'
            ? $this->codingSummary($user, $slug)
            : $this->mcqSummary($user, $slug);
    }

    private function forgetUserCache(User $user): void
    {
        $prefix = $user->id . ':';

        foreach (array_keys($this->summaryCache) as $key) {
            if (str_starts_with($key, $prefix)) {
                unset($this->summaryCache[$key]);
            }
        }

        foreach (array_keys($this->locksCache) as $key) {
            if (str_starts_with($key, $prefix)) {
                unset($this->locksCache[$key]);
            }
        }

        unset($this->activeEnrollmentCache[$user->id]);
    }

    private function hasActiveClassEnrollment(User $user): bool
    {
        if (array_key_exists($user->id, $this->activeEnrollmentCache)) {
            return $this->activeEnrollmentCache[$user->id];
        }

        return $this->activeEnrollmentCache[$user->id] = $user->classesAsStudent()
            ->active()
            ->exists();
    }

    private function mcqSummary(User $user, string $slug): array
    {
        $category = ChallengeCategory::where('slug', $slug)->first();
        if (! $category) {
            return $this->emptySummary();
        }

        $challenges = Challenge::withCount('questions')
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', false)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get();

        if ($challenges->isEmpty()) {
            return $this->emptySummary();
        }

        $challengeIds = $challenges->pluck('id');
        $attemptsByChallenge = ChallengeAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('challenge_id', $challengeIds)
            ->get([
                'challenge_id',
                'status',
                'is_ranked',
                'score',
                'total_questions',
                'time_limit_seconds',
                'time_taken_seconds',
            ])
            ->groupBy('challenge_id');

        $legacyChallengeIds = $challengeIds
            ->reject(fn ($challengeId): bool => $attemptsByChallenge->has($challengeId))
            ->values();

        $legacyProgress = $legacyChallengeIds->isEmpty()
            ? collect()
            : DB::table('challenge_user')
                ->where('user_id', $user->id)
                ->whereIn('challenge_id', $legacyChallengeIds)
                ->get()
                ->keyBy('challenge_id');

        $completed = 0;
        $scorePercents = [];
        $timeRatios = [];

        foreach ($challenges as $challenge) {
            if ((int) $challenge->questions_count <= 0) {
                continue;
            }

            $attempts = $attemptsByChallenge->get($challenge->id, collect());
            $best = $attempts
                ->filter(fn (ChallengeAttempt $attempt): bool =>
                    (bool) $attempt->is_ranked
                    && in_array($attempt->status, ['submitted', 'expired'], true)
                    && (int) $attempt->total_questions > 0
                )
                ->sort(function (ChallengeAttempt $left, ChallengeAttempt $right): int {
                    $leftRatio = (int) $left->score / max(1, (int) $left->total_questions);
                    $rightRatio = (int) $right->score / max(1, (int) $right->total_questions);

                    if ($leftRatio !== $rightRatio) {
                        return $leftRatio < $rightRatio ? 1 : -1;
                    }

                    return (int) $left->time_taken_seconds <=> (int) $right->time_taken_seconds;
                })
                ->first();

            if (! $best && $attempts->isEmpty()) {
                $best = $legacyProgress->get($challenge->id);
            }

            if (! $best) {
                continue;
            }

            $denominator = isset($best->total_questions)
                ? (int) $best->total_questions
                : (int) $challenge->questions_count;
            $percent = ((int) $best->score / max(1, $denominator)) * 100;
            $scorePercents[] = $percent;

            $timeLimit = isset($best->time_limit_seconds)
                ? (int) $best->time_limit_seconds
                : (int) $challenge->time_limit_seconds;
            if ($timeLimit > 0 && (int) $best->time_taken_seconds > 0) {
                $timeRatios[] = min(1, (int) $best->time_taken_seconds / $timeLimit);
            }

            if ($percent >= self::PASSING_PERCENT) {
                $completed++;
            }
        }

        $total = $challenges->filter(fn ($challenge) => (int) $challenge->questions_count > 0)->count();
        $completedAll = $total > 0 && $completed >= $total;
        $averageScore = $this->average($scorePercents);
        $averageTimeRatio = $this->nullableAverage($timeRatios);
        $timeGood = $averageTimeRatio === null || $averageTimeRatio <= self::EXCEPTIONAL_TIME_RATIO;

        return [
            'track' => 'mcq',
            'total_items' => $total,
            'completed_items' => $completed,
            'completed' => $completedAll,
            'average_score_percent' => $averageScore,
            'average_time_ratio' => $averageTimeRatio,
            'average_attempts' => null,
            'exceptional' => $completedAll
                && $averageScore >= self::EXCEPTIONAL_PERCENT
                && $timeGood,
        ];
    }

    private function codingSummary(User $user, string $slug): array
    {
        $category = ChallengeCategory::where('slug', $slug)->first();
        if (! $category) {
            return $this->emptySummary('coding');
        }

        $challenges = Challenge::with('codingQuestions')
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', true)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->get();

        if ($challenges->isEmpty()) {
            return $this->emptySummary('coding');
        }

        $questionIds = $challenges
            ->flatMap(fn (Challenge $challenge) => $challenge->codingQuestions->pluck('id'))
            ->unique()
            ->values();

        $submissionsByQuestion = $questionIds->isEmpty()
            ? collect()
            : CodingSubmission::query()
                ->where('user_id', $user->id)
                ->whereIn('coding_question_id', $questionIds)
                ->where('voided', false)
                ->get([
                    'coding_question_id',
                    'status',
                    'tests_passed',
                    'tests_total',
                    'time_taken_seconds',
                ])
                ->groupBy('coding_question_id');

        $completedChallenges = 0;
        $scoredQuestionPercents = [];
        $timeRatios = [];
        $attemptCounts = [];

        foreach ($challenges as $challenge) {
            $questions = $challenge->codingQuestions;
            if ($questions->isEmpty()) {
                continue;
            }

            $passedQuestions = 0;

            foreach ($questions as $question) {
                $submissions = $submissionsByQuestion->get($question->id, collect());
                $attemptCount = $submissions->count();

                if ($attemptCount > 0) {
                    $attemptCounts[] = $attemptCount;
                }

                $best = $submissions
                    ->sort(function (CodingSubmission $left, CodingSubmission $right): int {
                        if ((int) $left->tests_passed !== (int) $right->tests_passed) {
                            return (int) $right->tests_passed <=> (int) $left->tests_passed;
                        }

                        $leftPassed = $left->status === 'passed' ? 1 : 0;
                        $rightPassed = $right->status === 'passed' ? 1 : 0;
                        if ($leftPassed !== $rightPassed) {
                            return $rightPassed <=> $leftPassed;
                        }

                        return (int) $left->time_taken_seconds <=> (int) $right->time_taken_seconds;
                    })
                    ->first();

                if (! $best || (int) $best->tests_total <= 0) {
                    continue;
                }

                $percent = ((int) $best->tests_passed / max(1, (int) $best->tests_total)) * 100;
                $scoredQuestionPercents[] = $percent;

                if ((int) $question->time_limit_seconds > 0 && (int) $best->time_taken_seconds > 0) {
                    $timeRatios[] = min(1, (int) $best->time_taken_seconds / (int) $question->time_limit_seconds);
                }

                if ($best->status === 'passed' || $percent >= 100) {
                    $passedQuestions++;
                }
            }

            if ($passedQuestions >= $questions->count()) {
                $completedChallenges++;
            }
        }

        $totalChallenges = $challenges->filter(fn ($challenge) => $challenge->codingQuestions->isNotEmpty())->count();
        $completedAll = $totalChallenges > 0 && $completedChallenges >= $totalChallenges;
        $averageScore = $this->average($scoredQuestionPercents);
        $averageTimeRatio = $this->nullableAverage($timeRatios);
        $averageAttempts = $this->nullableAverage($attemptCounts);
        $timeGood = $averageTimeRatio === null || $averageTimeRatio <= self::EXCEPTIONAL_TIME_RATIO;
        $attemptsGood = $averageAttempts === null || $averageAttempts <= self::EXCEPTIONAL_AVG_ATTEMPTS;

        return [
            'track' => 'coding',
            'total_items' => $totalChallenges,
            'completed_items' => $completedChallenges,
            'completed' => $completedAll,
            'average_score_percent' => $averageScore,
            'average_time_ratio' => $averageTimeRatio,
            'average_attempts' => $averageAttempts,
            'exceptional' => $completedAll
                && $averageScore >= self::EXCEPTIONAL_PERCENT
                && $timeGood
                && $attemptsGood,
        ];
    }

    private function unlocked(
        string $reason,
        string $unlockType = 'progression',
        bool $bonus = false,
        ?string $sourceSlug = null,
        ?string $sourceName = null,
        ?array $sourceSummary = null
    ): array {
        return [
            'unlocked' => true,
            'reason' => $reason,
            'unlock_type' => $unlockType,
            'bonus_unlocked' => $bonus,
            'source_slug' => $sourceSlug,
            'source_name' => $sourceName,
            'source_summary' => $sourceSummary,
        ];
    }

    private function locked(string $reason): array
    {
        return [
            'unlocked' => false,
            'reason' => $reason,
            'unlock_type' => 'locked',
            'bonus_unlocked' => false,
            'source_slug' => null,
            'source_name' => null,
            'source_summary' => null,
        ];
    }

    private function emptySummary(string $track = 'mcq'): array
    {
        return [
            'track' => $track,
            'total_items' => 0,
            'completed_items' => 0,
            'completed' => false,
            'average_score_percent' => 0.0,
            'average_time_ratio' => null,
            'average_attempts' => null,
            'exceptional' => false,
        ];
    }

    private function normalizeTrack(string $track): string
    {
        return $track === 'coding' ? 'coding' : 'mcq';
    }

    private function pathName(string $slug): string
    {
        foreach (self::PATHS as $path) {
            if ($path['slug'] === $slug) {
                return $path['name'];
            }
        }

        return ucwords(str_replace('-', ' ', $slug));
    }

    private function average(array $values): float
    {
        $values = array_values(array_filter($values, fn ($value) => is_numeric($value)));
        if (count($values) === 0) {
            return 0.0;
        }

        return array_sum($values) / count($values);
    }

    private function nullableAverage(array $values): ?float
    {
        $values = array_values(array_filter($values, fn ($value) => is_numeric($value)));
        if (count($values) === 0) {
            return null;
        }

        return array_sum($values) / count($values);
    }
}
