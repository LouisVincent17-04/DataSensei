<?php

namespace App\Services;

use App\Support\SchemaInspector;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Which difficulty levels (challenge paths) a learner can open.
 *
 * DataSensei Updates 4, revised progression rule: a level opens only when the
 * learner has at least two different qualifying modules in the level below
 * it. A module qualifies when one finished attempt at that module reaches the
 * level's score and stays within its share of the time limit:
 *
 *   Newbie        80% score, 50% of the time or less  ->  Intermediate
 *   Intermediate  75% score, 70% of the time or less  ->  Advanced
 *   Advanced      70% score, 80% of the time or less  ->  Professional
 *
 * Every module is judged on its own (student + level + module); results are
 * never averaged across a level. For MCQ the module is one challenge and each
 * finished attempt is judged separately: the best one counts, so a retake can
 * make a module qualify, but the module is still counted once. For coding the
 * module is one coding challenge: every item of a run must be submitted, and
 * the run's average item score and average share of each item's time limit
 * are compared with the rule, using only that challenge's items.
 *
 * Moving to the next module inside a level is a separate rule (see
 * ChallengeModuleAccessService); a newly opened level starts at its first
 * module. University Student stays open through class enrollment only.
 *
 * Levels a learner had already started before this rule keep working: work
 * in a level (outside class assignments) keeps that level, and the levels
 * below it, open.
 */
class ChallengePathUnlockService
{
    /** Passing a module (and opening the next one) needs this score. */
    public const PASSING_PERCENT = 70.0;

    /** Distinct qualifying modules needed to open the next level. */
    public const REQUIRED_QUALIFYING_MODULES = 2;

    public const ADVANCEMENT_RULES = [
        'newbie' => ['target' => 'intermediate', 'score' => 80.0, 'time' => 50.0],
        'intermediate' => ['target' => 'advanced', 'score' => 75.0, 'time' => 70.0],
        'advanced' => ['target' => 'professional', 'score' => 70.0, 'time' => 80.0],
    ];

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
    private array $progressCache = [];
    private array $classChallengeCache = [];

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

            if (! $user) {
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
                $locks[$slug] = $this->hasActiveClassEnrollment($user)
                    ? $this->unlocked('Available through your active class enrollment.', 'class_enrollment')
                    : $this->locked('Enroll in an active class to access University Student challenges.');
                continue;
            }

            $source = $this->advancementSourceFor($slug);
            if ($source === null) {
                $locks[$slug] = $this->locked('This path is locked.');
                continue;
            }

            $sourceOpen = (bool) ($locks[$source]['unlocked'] ?? false);
            $summary = $this->performanceSummary($user, $source, $track);

            if ($sourceOpen && $summary['eligible']) {
                $locks[$slug] = $this->unlocked(
                    'Unlocked with '.$summary['qualifying_modules'].' qualifying '.$this->pathName($source).' modules.',
                    'qualifying_modules',
                    false,
                    $source,
                    $this->pathName($source),
                    $summary
                );
                continue;
            }

            if ($this->hasExistingProgressFrom($user, $slug, $track)) {
                $locks[$slug] = $this->unlocked('Open because you already started this level.', 'existing_progress');
                continue;
            }

            $locks[$slug] = $this->locked($sourceOpen
                ? $this->progressMessage($source, (int) $summary['qualifying_modules'])
                : 'Unlock '.$this->pathName($source).' first. '.$this->ruleSentence($source));
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

    /**
     * Tells the learner, once per level and track, that the second qualifying
     * module opened the next level. Running the check again never sends the
     * notice twice: the dedupe key is the level, so a third qualifying module
     * or a later visit finds the existing notice.
     *
     * @return array<int, string> notices created by this call
     */
    public function notifyExceptionalUnlocks(User $user, string $track = 'mcq'): array
    {
        // A submission may have been saved earlier in the same request. Clear only
        // this user's request cache so unlock checks use the newly persisted result.
        $this->forgetUserCache($user);

        $track = $this->normalizeTrack($track);
        $locks = $this->buildPathLocks($user, $track);
        $messages = [];

        if (! SchemaInspector::hasTable('notifications')) {
            return $messages;
        }

        foreach ($locks as $slug => $lock) {
            if (($lock['unlock_type'] ?? null) !== 'qualifying_modules') {
                continue;
            }

            $sourceName = $lock['source_name'] ?? 'the previous level';
            $count = (int) ($lock['source_summary']['qualifying_modules'] ?? self::REQUIRED_QUALIFYING_MODULES);
            $pathName = $this->pathName($slug);
            $trackLabel = $track === 'coding' ? 'coding challenges' : 'MCQ challenges';

            // Same key as the earlier early-unlock notice, so a level that was
            // already announced is not announced again.
            $type = 'exceptional_unlock_'.$track.'_'.$slug;
            $text = "You have {$count} qualifying {$sourceName} modules in {$trackLabel}, so you are now eligible for {$pathName}. {$pathName} is unlocked: start with its first module.";

            $actionUrl = $track === 'coding'
                ? route('challenges.coding.map', ['slug' => $slug])
                : route('challenges.map', ['slug' => $slug]);

            $notification = app(StudentNotificationService::class)->send(
                $user,
                $type,
                'Challenge level unlocked',
                $text,
                $actionUrl,
                ['track' => $track, 'path' => $slug, 'source' => $lock['source_slug'] ?? null],
                $type
            );

            if ($notification?->wasRecentlyCreated) {
                $messages[] = $text;
            }
        }

        return $messages;
    }

    /**
     * One level's module results for one track.
     *
     * Besides the per-module rows ('modules'), it keeps the older summary keys
     * (total_items, completed_items, completed, averages) used by the pages.
     */
    public function performanceSummary(User $user, string $slug, string $track = 'mcq'): array
    {
        $track = $this->normalizeTrack($track);
        $cacheKey = $user->id.':'.$track.':'.$slug;

        if (array_key_exists($cacheKey, $this->summaryCache)) {
            return $this->summaryCache[$cacheKey];
        }

        return $this->summaryCache[$cacheKey] = $track === 'coding'
            ? $this->codingSummary($user, $slug)
            : $this->mcqSummary($user, $slug);
    }

    /**
     * "You have 1 of 2 qualifying Newbie modules. Complete one more Newbie
     * module with at least 80% score and 50% time consumed or less to unlock
     * Intermediate."
     */
    public function progressMessage(string $sourceSlug, int $qualifying): string
    {
        $rule = self::ADVANCEMENT_RULES[$sourceSlug] ?? null;
        if ($rule === null) {
            return '';
        }

        $source = $this->pathName($sourceSlug);
        $target = $this->pathName($rule['target']);
        $required = self::REQUIRED_QUALIFYING_MODULES;

        if ($qualifying >= $required) {
            return "You have {$qualifying} qualifying {$source} modules. {$target} is unlocked.";
        }

        $remaining = $required - $qualifying;
        $what = $remaining === 1
            ? "Complete one more {$source} module"
            : "Complete {$remaining} {$source} modules";

        return "You have {$qualifying} of {$required} qualifying {$source} modules. {$what} with at least "
            .$this->percent($rule['score']).' score and '.$this->percent($rule['time'])
            ." time consumed or less to unlock {$target}.";
    }

    /** "Complete 2 Newbie modules with at least 80% score and 50% time consumed or less to unlock Intermediate." */
    public function ruleSentence(string $sourceSlug): string
    {
        $rule = self::ADVANCEMENT_RULES[$sourceSlug] ?? null;
        if ($rule === null) {
            return '';
        }

        return 'Complete '.self::REQUIRED_QUALIFYING_MODULES.' '.$this->pathName($sourceSlug)
            .' modules with at least '.$this->percent($rule['score']).' score and '
            .$this->percent($rule['time']).' time consumed or less to unlock '
            .$this->pathName($rule['target']).'.';
    }

    /** The level whose rule opens this one, if any. */
    public function advancementSourceFor(string $targetSlug): ?string
    {
        foreach (self::ADVANCEMENT_RULES as $source => $rule) {
            if ($rule['target'] === $targetSlug) {
                return $source;
            }
        }

        return null;
    }

    /** Whether one result (score and time in percent) meets a level's rule. */
    public function resultQualifies(string $slug, ?float $scorePercent, ?float $timePercent): bool
    {
        $rule = self::ADVANCEMENT_RULES[$slug] ?? null;

        return $rule !== null
            && $scorePercent !== null
            && $timePercent !== null
            && $scorePercent >= $rule['score']
            && $timePercent <= $rule['time'];
    }

    public function pathName(string $slug): string
    {
        foreach (self::PATHS as $path) {
            if ($path['slug'] === $slug) {
                return $path['name'];
            }
        }

        return ucwords(str_replace('-', ' ', $slug));
    }

    public function percent(float|int|null $value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.').'%';
    }

    private function forgetUserCache(User $user): void
    {
        $prefix = $user->id.':';

        foreach (['summaryCache', 'locksCache', 'progressCache'] as $cache) {
            foreach (array_keys($this->{$cache}) as $key) {
                if (str_starts_with((string) $key, $prefix)) {
                    unset($this->{$cache}[$key]);
                }
            }
        }

        unset($this->activeEnrollmentCache[$user->id], $this->classChallengeCache[$user->id]);
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
            return $this->summarize('mcq', $slug, []);
        }

        // Instructor-built challenges are class work, never part of the
        // public path, so they are not modules of the level.
        $challenges = Challenge::withCount('questions')
            ->platform()
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', false)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get()
            ->filter(fn (Challenge $challenge): bool => (int) $challenge->questions_count > 0)
            ->values();

        if ($challenges->isEmpty()) {
            return $this->summarize('mcq', $slug, []);
        }

        $challengeIds = $challenges->pluck('id');
        $attemptsByChallenge = ChallengeAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('challenge_id', $challengeIds)
            ->get([
                'challenge_id',
                'status',
                'score',
                'total_questions',
                'time_limit_seconds',
                'time_taken_seconds',
            ])
            ->groupBy('challenge_id');

        $legacyChallengeIds = $challengeIds
            ->reject(fn ($challengeId): bool => $attemptsByChallenge->has($challengeId))
            ->values();

        $legacyProgress = $legacyChallengeIds->isEmpty() || ! SchemaInspector::hasTable('challenge_user')
            ? collect()
            : DB::table('challenge_user')
                ->where('user_id', $user->id)
                ->whereIn('challenge_id', $legacyChallengeIds)
                ->get()
                ->keyBy('challenge_id');

        $modules = [];

        foreach ($challenges as $challenge) {
            $key = $this->moduleKey($challenge);
            $modules[$key] ??= ['title' => $challenge->title, 'results' => [], 'passed' => false];

            $attempts = $attemptsByChallenge->get($challenge->id, collect());

            // Every finished attempt, first or retake, is judged on its own:
            // score and time always come from the same attempt.
            foreach ($attempts as $attempt) {
                if (! in_array($attempt->status, ['submitted', 'expired'], true) || (int) $attempt->total_questions <= 0) {
                    continue;
                }

                $timeLimit = (int) $attempt->time_limit_seconds ?: (int) $challenge->time_limit_seconds;
                $modules[$key]['results'][] = [
                    'score' => $this->ratioPercent((int) $attempt->score, (int) $attempt->total_questions),
                    'time' => $attempt->time_taken_seconds === null
                        ? null
                        : $this->ratioPercent((int) $attempt->time_taken_seconds, $timeLimit, true),
                ];
            }

            // Progress saved before the attempts table existed.
            $legacy = $attempts->isEmpty() ? $legacyProgress->get($challenge->id) : null;
            if ($legacy) {
                $modules[$key]['results'][] = [
                    'score' => $this->ratioPercent((int) $legacy->score, (int) $challenge->questions_count),
                    'time' => (int) ($legacy->time_taken_seconds ?? 0) > 0
                        ? $this->ratioPercent((int) $legacy->time_taken_seconds, (int) $challenge->time_limit_seconds, true)
                        : null,
                ];
            }
        }

        $rows = [];
        foreach ($modules as $key => $module) {
            $best = collect($module['results'])->max('score');
            $rows[] = $this->moduleRow($slug, (string) $key, $module['title'], $module['results'], [
                'passed' => $best !== null && $best >= self::PASSING_PERCENT,
            ]);
        }

        return $this->summarize('mcq', $slug, $rows);
    }

    private function codingSummary(User $user, string $slug): array
    {
        $category = ChallengeCategory::where('slug', $slug)->first();
        if (! $category) {
            return $this->summarize('coding', $slug, []);
        }

        $challenges = Challenge::with('codingQuestions')
            ->platform()
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', true)
            ->where('is_active', true)
            ->orderBy('order_index')
            ->orderBy('id')
            ->get()
            ->filter(fn (Challenge $challenge): bool => $challenge->codingQuestions->isNotEmpty())
            ->values();

        if ($challenges->isEmpty()) {
            return $this->summarize('coding', $slug, []);
        }

        $questionIds = $challenges
            ->flatMap(fn (Challenge $challenge) => $challenge->codingQuestions->pluck('id'))
            ->unique()
            ->values();

        // The current run, plus earlier runs that a retake replaced. Results
        // that arrived after a retake (stale) never counted and still do not.
        $submissionsByQuestion = CodingSubmission::query()
            ->where('user_id', $user->id)
            ->whereIn('coding_question_id', $questionIds)
            ->where(function ($query): void {
                $query->where('voided', false)
                    ->orWhere(function ($query): void {
                        $query->where('voided', true)
                            ->where(function ($query): void {
                                $query->whereNull('void_reason')->orWhere('void_reason', 'retake');
                            });
                    });
            })
            ->get([
                'coding_question_id',
                'status',
                'tests_passed',
                'tests_total',
                'time_taken_seconds',
                'voided',
                'attempt_generation',
            ])
            ->groupBy('coding_question_id');

        $modules = [];
        $totalQuestions = 0;

        foreach ($challenges as $challenge) {
            $items = $challenge->codingQuestions;
            $totalQuestions += $items->count();
            $key = $this->moduleKey($challenge);

            // Group this challenge's submissions into runs.
            $runs = [];
            foreach ($items as $item) {
                foreach ($submissionsByQuestion->get($item->id, collect()) as $submission) {
                    $run = $submission->voided
                        ? 'earlier:'.(int) ($submission->attempt_generation ?? 0)
                        : 'current';
                    $runs[$run][$item->id][] = $submission;
                }
            }

            $results = [];
            foreach ($runs as $byItem) {
                // A run counts once every item of this challenge was submitted.
                if (count($byItem) < $items->count()) {
                    continue;
                }

                $scores = [];
                $times = [];
                foreach ($items as $item) {
                    $best = $this->bestSubmission($byItem[$item->id]);
                    $scores[] = $this->ratioPercent((int) $best->tests_passed, (int) $best->tests_total);
                    $time = $this->ratioPercent((int) $best->time_taken_seconds, (int) $item->time_limit_seconds, true);
                    if ($time !== null) {
                        $times[] = $time;
                    }
                }

                $results[] = [
                    'score' => round(array_sum($scores) / count($scores), 4),
                    // Average share of the time limit used on each item.
                    'time' => count($times) === count($scores) ? round(array_sum($times) / count($times), 4) : null,
                ];
            }

            // Solving every item of the current run completes the challenge,
            // as on the coding map.
            $current = $runs['current'] ?? [];
            $solved = $items->filter(fn ($item): bool => collect($current[$item->id] ?? [])
                ->contains(fn (CodingSubmission $submission): bool => $submission->status === 'passed'))
                ->count();

            $modules[$key] = [
                'title' => $challenge->title,
                'results' => array_merge($modules[$key]['results'] ?? [], $results),
                'passed' => ($modules[$key]['passed'] ?? false) || $solved >= $items->count(),
                'items_total' => ($modules[$key]['items_total'] ?? 0) + $items->count(),
                'items_finished' => ($modules[$key]['items_finished'] ?? 0) + count($current),
            ];
        }

        $rows = [];
        foreach ($modules as $key => $module) {
            $rows[] = $this->moduleRow($slug, (string) $key, $module['title'], $module['results'], [
                'passed' => $module['passed'],
                'items_total' => $module['items_total'],
                'items_finished' => $module['items_finished'],
            ]);
        }

        return $this->summarize('coding', $slug, $rows) + ['total_questions' => $totalQuestions];
    }

    /**
     * One module's row: its best result and whether it qualifies. When some
     * result qualifies, the best qualifying one is shown.
     *
     * @param  array<int, array{score: float, time: ?float}>  $results
     */
    private function moduleRow(string $slug, string $key, string $title, array $results, array $extra): array
    {
        $rule = self::ADVANCEMENT_RULES[$slug] ?? null;
        $results = collect($results);
        $qualifying = $results->filter(fn (array $result): bool =>
            $this->resultQualifies($slug, $result['score'], $result['time'])
        );

        $best = ($qualifying->isNotEmpty() ? $qualifying : $results)
            ->sort(function (array $left, array $right): int {
                if ($left['score'] !== $right['score']) {
                    return $right['score'] <=> $left['score'];
                }

                return ($left['time'] ?? INF) <=> ($right['time'] ?? INF);
            })
            ->first();

        $note = 'Not finished yet';
        if ($qualifying->isNotEmpty()) {
            $note = 'Qualifies';
        } elseif ($best !== null && $rule !== null) {
            $problems = [];
            if ($best['score'] < $rule['score']) {
                $problems[] = 'score below '.$this->percent($rule['score']);
            }
            if ($best['time'] === null) {
                $problems[] = 'time not recorded';
            } elseif ($best['time'] > $rule['time']) {
                $problems[] = 'time above '.$this->percent($rule['time']);
            }
            $note = ucfirst(implode(' and ', $problems));
        } elseif ($best !== null) {
            $note = 'Finished';
        }

        return [
            'key' => $key,
            'title' => $title,
            'finished' => $results->isNotEmpty(),
            'attempts' => $results->count(),
            'passed' => (bool) ($extra['passed'] ?? false),
            'qualifies' => $qualifying->isNotEmpty(),
            'score' => $best['score'] ?? null,
            'time' => $best['time'] ?? null,
            'items_total' => $extra['items_total'] ?? null,
            'items_finished' => $extra['items_finished'] ?? null,
            'note' => $note,
        ];
    }

    private function summarize(string $track, string $slug, array $modules): array
    {
        $modules = collect($modules)->values();
        $finished = $modules->where('finished', true);
        $qualifying = $modules->where('qualifies', true)->count();
        $completed = $modules->where('passed', true)->count();
        $times = $finished->pluck('time')->filter(fn ($time): bool => $time !== null);

        return [
            'track' => $track,
            'slug' => $slug,
            'total_items' => $modules->count(),
            'completed_items' => $completed,
            'finished_items' => $finished->count(),
            'completed' => $modules->isNotEmpty() && $completed >= $modules->count(),
            'average_score_percent' => (float) ($finished->avg('score') ?? 0.0),
            'average_time_ratio' => $times->isEmpty() ? null : (float) $times->avg() / 100,
            'average_attempts' => null,
            'modules' => $modules->all(),
            'qualifying_modules' => $qualifying,
            'required_modules' => self::REQUIRED_QUALIFYING_MODULES,
            'eligible' => isset(self::ADVANCEMENT_RULES[$slug]) && $qualifying >= self::REQUIRED_QUALIFYING_MODULES,
        ];
    }

    /** Best submission of one item: most tests passed, then solved, then fastest. */
    private function bestSubmission(array $submissions): CodingSubmission
    {
        return collect($submissions)
            ->sort(function (CodingSubmission $left, CodingSubmission $right): int {
                $leftRatio = (int) $left->tests_passed / max(1, (int) $left->tests_total);
                $rightRatio = (int) $right->tests_passed / max(1, (int) $right->tests_total);
                if ($leftRatio !== $rightRatio) {
                    return $rightRatio <=> $leftRatio;
                }

                $leftPassed = $left->status === 'passed' ? 1 : 0;
                $rightPassed = $right->status === 'passed' ? 1 : 0;
                if ($leftPassed !== $rightPassed) {
                    return $rightPassed <=> $leftPassed;
                }

                return (int) $left->time_taken_seconds <=> (int) $right->time_taken_seconds;
            })
            ->first();
    }

    /**
     * A module is counted once: challenges fanned out from the same public
     * module on one level share it, every other challenge is its own module.
     */
    private function moduleKey(Challenge $challenge): string
    {
        return $challenge->module_id
            ? 'module:'.(int) $challenge->module_id
            : 'challenge:'.(int) $challenge->id;
    }

    /** $part / $whole in percent (capped at 100 for time), or null without a whole. */
    private function ratioPercent(int $part, int $whole, bool $cap = false): ?float
    {
        if ($whole <= 0) {
            return $cap ? null : 0.0;
        }

        $percent = round(max(0, $part) / $whole * 100, 4);

        return $cap ? min(100.0, $percent) : $percent;
    }

    /**
     * Work already done in this level or a higher one keeps the level open,
     * so learners who opened levels under the earlier rules keep them.
     */
    private function hasExistingProgressFrom(User $user, string $slug, string $track): bool
    {
        $chain = array_column(self::ADVANCEMENT_RULES, 'target');
        $position = array_search($slug, $chain, true);

        if ($position === false) {
            return false;
        }

        foreach (array_slice($chain, $position) as $level) {
            if ($this->hasExistingProgress($user, $level, $track)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attempts or submissions on this level's platform challenges. Challenges
     * given to one of the learner's classes are left out: a class assignment
     * opens that one challenge, never the level.
     */
    private function hasExistingProgress(User $user, string $slug, string $track): bool
    {
        $cacheKey = $user->id.':'.$track.':'.$slug;
        if (array_key_exists($cacheKey, $this->progressCache)) {
            return $this->progressCache[$cacheKey];
        }

        $category = ChallengeCategory::where('slug', $slug)->first();
        if (! $category) {
            return $this->progressCache[$cacheKey] = false;
        }

        $challengeIds = Challenge::query()
            ->platform()
            ->where('challenge_category_id', $category->id)
            ->where('is_coding_challenge', $track === 'coding')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->diff($this->classChallengeIds($user))
            ->values();

        if ($challengeIds->isEmpty()) {
            return $this->progressCache[$cacheKey] = false;
        }

        if ($track === 'coding') {
            $questionIds = CodingQuestion::whereIn('challenge_id', $challengeIds)->pluck('id');

            return $this->progressCache[$cacheKey] = $questionIds->isNotEmpty() && (
                CodingSubmission::where('user_id', $user->id)->whereIn('coding_question_id', $questionIds)->exists()
                || CodingQuestionAttempt::where('user_id', $user->id)->whereIn('coding_question_id', $questionIds)->exists()
            );
        }

        return $this->progressCache[$cacheKey] =
            ChallengeAttempt::where('user_id', $user->id)->whereIn('challenge_id', $challengeIds)->exists()
            || (SchemaInspector::hasTable('challenge_user')
                && DB::table('challenge_user')->where('user_id', $user->id)->whereIn('challenge_id', $challengeIds)->exists());
    }

    /** @return array<int, int> */
    private function classChallengeIds(User $user): array
    {
        if (array_key_exists($user->id, $this->classChallengeCache)) {
            return $this->classChallengeCache[$user->id];
        }

        if (! SchemaInspector::hasTable('class_challenge_assignments')) {
            return $this->classChallengeCache[$user->id] = [];
        }

        $classIds = $user->classesAsStudent()->pluck('classes.id');

        return $this->classChallengeCache[$user->id] = $classIds->isEmpty()
            ? []
            : ClassChallengeAssignment::query()
                ->whereIn('class_id', $classIds)
                ->pluck('challenge_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values()
                ->all();
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

    private function normalizeTrack(string $track): string
    {
        return $track === 'coding' ? 'coding' : 'mcq';
    }
}
