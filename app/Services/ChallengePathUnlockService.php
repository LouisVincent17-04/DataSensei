<?php

namespace App\Services;

use App\Models\Challenge;
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

    public function buildPathLocks(?User $user, string $track = 'mcq'): array
    {
        $track = $this->normalizeTrack($track);
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

            $previous = self::PATHS[$index - 1];
            $previousSummary = $this->performanceSummary($user, $previous['slug'], $track);

            if ($slug === 'university-student' && !empty($user->institution_id)) {
                $locks[$slug] = $this->unlocked('Unlocked through your institution/class enrollment.', 'institution');
                continue;
            }

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

        return $locks;
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

            $exists = DB::table('notifications')
                ->where('user_id', $user->id)
                ->where('type', $type)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => $type,
                'notification_text' => $text,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $messages[] = $text;
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

    private function mcqSummary(User $user, string $slug): array
    {
        $category = ChallengeCategory::where('slug', $slug)->first();
        if (!$category) {
            return $this->emptySummary();
        }

        $challenges = Challenge::withCount('questions')
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', false)
            ->orderBy('order_index')
            ->get();

        if ($challenges->isEmpty()) {
            return $this->emptySummary();
        }

        $completed = 0;
        $scorePercents = [];
        $timeRatios = [];

        foreach ($challenges as $challenge) {
            if ((int) $challenge->questions_count <= 0) {
                continue;
            }

            $best = DB::table('challenge_user')
                ->where('user_id', $user->id)
                ->where('challenge_id', $challenge->id)
                ->orderByDesc('score')
                ->orderBy('time_taken_seconds')
                ->first();

            if (!$best) {
                continue;
            }

            $percent = ((int) $best->score / max(1, (int) $challenge->questions_count)) * 100;
            $scorePercents[] = $percent;

            if ((int) $challenge->time_limit_seconds > 0 && (int) $best->time_taken_seconds > 0) {
                $timeRatios[] = min(1, (int) $best->time_taken_seconds / (int) $challenge->time_limit_seconds);
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
        if (!$category) {
            return $this->emptySummary('coding');
        }

        $challenges = Challenge::with('codingQuestions')
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', true)
            ->orderBy('order_index')
            ->get();

        if ($challenges->isEmpty()) {
            return $this->emptySummary('coding');
        }

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
                $attemptCount = CodingSubmission::where('user_id', $user->id)
                    ->where('coding_question_id', $question->id)
                    ->where('voided', false)
                    ->count();

                if ($attemptCount > 0) {
                    $attemptCounts[] = $attemptCount;
                }

                $best = CodingSubmission::where('user_id', $user->id)
                    ->where('coding_question_id', $question->id)
                    ->where('voided', false)
                    ->orderByDesc('tests_passed')
                    ->orderByRaw("CASE WHEN status = 'passed' THEN 1 ELSE 0 END DESC")
                    ->orderBy('time_taken_seconds')
                    ->first();

                if (!$best || (int) $best->tests_total <= 0) {
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
