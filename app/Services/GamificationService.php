<?php

namespace App\Services;

use App\Support\SchemaInspector;
use App\Models\AchievementDefinition;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\CodingQuestion;
use App\Models\CodingSubmission;
use App\Models\MissionDefinition;
use App\Models\Rank;
use App\Models\StudentMissionProgress;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Achievements, missions, streaks and rank-up notices.
 *
 * Achievements and missions are defined by administrators on the Gamification
 * page as rules: an achievement unlocks when a measured value reaches its
 * criteria (for example challenge_passes >= 1), and a mission counts one kind
 * of activity per day or week (for example challenge_attempts). Every learning
 * event below records its activity types, advances the matching missions and
 * then re-checks every achievement rule against the learner's saved work.
 *
 * Earlier versions only unlocked achievements by fixed keys such as
 * "first_mcq_pass" and only advanced missions named "complete_mcq_challenge".
 * The default seeder (AchievementDefinitionsSeeder / MissionDefinitionsSeeder)
 * defines rules like challenge_passes and challenge_attempts instead, so on a
 * normally seeded database learners never received an achievement or mission
 * reward. Both kinds are handled now: the rule types are measured from saved
 * work, and the older keyed rules are still unlocked by their events.
 *
 * DataSensei Updates 5: XP only comes from platform content (see XpPolicy).
 * Class and instructor activity (assignments, assessments, instructor-built
 * challenges, the University Student level) records no event and is left out
 * of every measured value, so it can never lead to an achievement, a mission
 * or a streak reward.
 */
class GamificationService
{
    /** Activity types recorded for each learning event (mission target types). */
    private const EVENTS = [
        'mcq' => ['challenge_attempts', 'activity_count'],
        'mcq_passed' => ['complete_mcq_challenge', 'complete_any_assessment'],
        'coding' => ['coding_submissions', 'challenge_attempts', 'activity_count'],
        'coding_passed' => ['solve_coding_problem', 'complete_any_assessment'],
        'coding_challenge_complete' => ['complete_coding_challenge'],
        'code_run' => ['code_runs', 'activity_count'],
        'lesson' => ['lesson_completions', 'activity_count'],
    ];

    /**
     * Achievement rule types this service can measure, for the admin editor.
     * A rule with any other type is never unlocked.
     */
    public const CRITERIA_TYPES = [
        'xp_total' => 'Total XP reached',
        'streak_days' => 'Learning streak, days in a row',
        'active_days' => 'Different days with learning activity',
        'code_runs' => 'Programs run in the Python IDE',
        'challenge_attempts' => 'MCQ challenge attempts finished',
        'challenge_passes' => 'Different MCQ challenges passed (70% or more)',
        'perfect_score' => 'Different MCQ challenges with every answer correct',
        'fast_pass' => 'MCQ challenges passed in half the time limit or less',
        'coding_submissions' => 'Coding challenge submissions',
        'coding_passes' => 'Different coding problems solved',
        'coding_perfect' => 'Coding problems solved with every test passing',
        'coding_challenge_complete' => 'Coding challenges with every problem solved',
        'test_cases_passed' => 'Coding test cases passed (best run per problem)',
        'lesson_completions' => 'Lessons completed',
        'path_complete' => 'Every MCQ challenge of a path passed (key path_<path>_complete)',
        'coding_path_complete' => 'Every coding challenge of a path solved (key coding_path_<path>_complete)',
        'mcq_pass' => 'Different MCQ challenges passed (older name)',
        'coding_pass' => 'Different coding problems solved (older name)',
        'streak' => 'Learning streak, days in a row (older name)',
    ];

    /**
     * Rule and mission types that measured class work. Class work gives no
     * XP, so these are never counted; existing rules of these types were
     * turned off by the 2026_09_28 migration.
     */
    public const CLASS_ACTIVITY_TYPES = [
        'assignment_submissions',
        'assignment_perfect',
        'clean_assignment',
        'assessment_submissions',
        'assignment_submit',
        'submit_assignment',
    ];

    /** Activity types missions can count, for the admin editor. */
    public const MISSION_TYPES = [
        'activity_count' => 'Any learning activity',
        'code_runs' => 'Run code in the Python IDE or SQL Sandbox',
        'lesson_completions' => 'Complete a lesson',
        'challenge_attempts' => 'Finish an MCQ or coding challenge attempt',
        'coding_submissions' => 'Submit code in a coding challenge',
        'complete_mcq_challenge' => 'Pass an MCQ challenge',
        'solve_coding_problem' => 'Solve a coding problem',
        'complete_coding_challenge' => 'Solve every problem of a coding challenge',
        'complete_any_assessment' => 'Pass an MCQ challenge or solve a coding problem',
    ];

    /** @var array<string, bool> */
    private array $tableCache = [];

    public function awardForMcqChallenge(
        User $user,
        Challenge $challenge,
        int $correct,
        int $total,
        int $timeTakenSeconds,
        bool $passed,
        ?int $xpBefore = null
    ): array {
        // Class work (instructor-built challenges, the University Student
        // level) gives no XP, not even through missions or achievements.
        if (! $this->tablesReady() || ! XpPolicy::challengeAwardsXp($challenge)) {
            return [];
        }

        $xpBefore ??= $this->currentXp($user);

        $events = self::EVENTS['mcq'];
        if ($passed) {
            $events = array_merge($events, self::EVENTS['mcq_passed']);
        }

        $legacy = $this->legacyMcqUnlocks($user, $challenge, $correct, $total, $timeTakenSeconds, $passed);

        return array_merge(
            $this->compactUnlocks($legacy),
            $this->recordEvent($user, $events, 'mcq_challenge', (int) $challenge->id, $xpBefore)
        );
    }

    public function awardForCodingSubmission(
        User $user,
        Challenge $challenge,
        CodingQuestion $question,
        CodingSubmission $submission,
        bool $challengeComplete,
        ?int $xpBefore = null
    ): array {
        if (! $this->tablesReady() || ! XpPolicy::challengeAwardsXp($challenge)) {
            return [];
        }

        $xpBefore ??= $this->currentXp($user);

        $events = self::EVENTS['coding'];
        if ($submission->status === 'passed') {
            $events = array_merge($events, self::EVENTS['coding_passed']);
        }
        if ($challengeComplete) {
            $events = array_merge($events, self::EVENTS['coding_challenge_complete']);
        }

        $legacy = $this->legacyCodingUnlocks($user, $challenge, $question, $submission, $challengeComplete);

        return array_merge(
            $this->compactUnlocks($legacy),
            $this->recordEvent($user, $events, 'coding_question', (int) $question->id, $xpBefore)
        );
    }

    /**
     * Assessments (homework, quizzes, examinations) are class work:
     * submitting one gives no XP, mission progress, achievement or streak
     * (DataSensei Updates 5). Kept so callers keep working; it always
     * returns no rewards.
     */
    public function recordAssessmentSubmission(User $user, int $submissionId): array
    {
        return [];
    }

    public function recordCodeRun(User $user): array
    {
        return $this->recordEvent($user, self::EVENTS['code_run'], 'code_run', null);
    }

    public function recordLessonCompletion(User $user, int $lessonId): array
    {
        return $this->recordEvent($user, self::EVENTS['lesson'], 'lesson', $lessonId);
    }

    /**
     * Unlock every active achievement whose rule the learner already meets.
     * Safe to call at any time: it never unlocks the same achievement twice,
     * so pages can call it to catch up on work saved before a rule existed.
     *
     * @return array<int, UserAchievement>
     */
    public function evaluateAchievements(User $user, string $source = 'progress', ?int $sourceId = null): array
    {
        if (! $this->tablesReady()) {
            return [];
        }

        $unlocked = [];

        // An achievement's own XP reward can satisfy an xp_total rule, so the
        // rules are checked again until nothing new unlocks.
        for ($pass = 0; $pass < 3; $pass++) {
            $metrics = [];
            $newThisPass = 0;

            $earned = UserAchievement::where('user_id', $user->id)->pluck('achievement_definition_id')->all();
            $pending = AchievementDefinition::where('is_active', true)
                ->whereNotIn('id', $earned === [] ? [0] : $earned)
                ->orderBy('sort_order')
                ->get();

            foreach ($pending as $definition) {
                $value = $this->definitionValue($user, $definition, $metrics);

                if ($value === null || $value < (int) $definition->criteria_value) {
                    continue;
                }

                $record = $this->unlockDefinition($user, $definition, $source, $sourceId, (float) $value);
                if ($record) {
                    $unlocked[] = $record;
                    $newThisPass++;
                }
            }

            if ($newThisPass === 0) {
                break;
            }
        }

        return $unlocked;
    }

    /**
     * Bring every learner's achievements up to date with the active rules,
     * including work saved before a rule existed or before rewards were
     * measured from saved work. Achievements are never unlocked twice, so it
     * is safe to run again at any time.
     *
     * @param  callable(User, array<int, UserAchievement>): void|null  $onStudent
     * @return array{students: int, unlocked: int, xp: int, by_achievement: array<string, int>}
     */
    public function syncAllLearners(?string $email = null, ?callable $onStudent = null): array
    {
        $summary = ['students' => 0, 'unlocked' => 0, 'xp' => 0, 'by_achievement' => []];

        if (! $this->tablesReady()) {
            return $summary;
        }

        User::query()
            ->where('role', User::ROLE_USER)
            ->where('status', 'active')
            ->when($email !== null && $email !== '', fn ($query) => $query->where('email', strtolower(trim((string) $email))))
            ->orderBy('id')
            ->chunkById(200, function ($users) use (&$summary, $onStudent): void {
                foreach ($users as $user) {
                    $xpBefore = (int) $user->xp;
                    $unlocked = $this->evaluateAchievements($user, 'sync');
                    $this->announceRankChange($user, $xpBefore);

                    $summary['students']++;
                    foreach ($unlocked as $record) {
                        $name = (string) ($record->achievement->name ?? 'Achievement');
                        $summary['unlocked']++;
                        $summary['xp'] += (int) ($record->achievement->xp_reward ?? 0);
                        $summary['by_achievement'][$name] = ($summary['by_achievement'][$name] ?? 0) + 1;
                    }

                    if ($onStudent !== null) {
                        $onStudent($user, $unlocked);
                    }
                }
            });

        return $summary;
    }

    /**
     * Progress toward each active achievement, keyed by definition id, for the
     * achievements page: ['value' => current, 'target' => criteria].
     *
     * @return array<int, array{value:int, target:int}>
     */
    public function achievementProgress(User $user): array
    {
        if (! $this->tablesReady()) {
            return [];
        }

        $metrics = [];
        $progress = [];

        foreach (AchievementDefinition::where('is_active', true)->get() as $definition) {
            $value = $this->definitionValue($user, $definition, $metrics);
            if ($value === null) {
                continue;
            }

            $progress[(int) $definition->id] = [
                'value' => $value,
                'target' => (int) $definition->criteria_value,
            ];
        }

        return $progress;
    }

    /** Tell the learner when their XP moved them into a higher rank. */
    public function announceRankChange(User $user, int $xpBefore): void
    {
        if (! $this->hasTable('ranks')) {
            return;
        }

        $xpNow = (int) User::query()->whereKey($user->id)->value('xp');
        if ($xpNow <= $xpBefore) {
            return;
        }

        $before = Rank::currentForXp($xpBefore);
        $after = Rank::currentForXp($xpNow);

        if (! $after || ($before && (int) $after->exp_required <= (int) $before->exp_required)) {
            return;
        }

        $this->notify(
            $user,
            'rank_up_'.$after->rank_id,
            'You reached the '.$after->rank_name.' rank with '.number_format($xpNow).' XP.'
        );
    }

    public function currentMissions(User $user)
    {
        if (! $this->hasTable('mission_definitions') || ! $this->hasTable('student_mission_progress')) {
            return collect();
        }

        $today = now()->toDateString();
        $week = now()->startOfWeek()->toDateString();

        return DB::transaction(function () use ($user, $today, $week) {
            User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            return MissionDefinition::where('is_active', true)
                ->orderBy('period_type')
                ->orderBy('sort_order')
                ->get()
                ->map(function (MissionDefinition $mission) use ($user, $today, $week) {
                    $periodStart = $mission->period_type === 'weekly' ? $week : $today;
                    $progress = $this->missionProgressFor($user->id, $mission->id, $periodStart);

                    $mission->setRelation('currentProgress', $progress);

                    return $mission;
                });
        }, 3);
    }

    // ── Internals ────────────────────────────────────────────────────

    /**
     * @param  array<int, string>  $events
     * @return array<int, array{name:string, xp_reward:int}>
     */
    private function recordEvent(User $user, array $events, string $source, ?int $sourceId, ?int $xpBefore = null): array
    {
        if (! $this->tablesReady()) {
            return [];
        }

        $xpBefore ??= $this->currentXp($user);

        $this->updateStreak($user);
        $this->incrementMissions($user, array_values(array_unique($events)));
        $unlocked = $this->evaluateAchievements($user, $source, $sourceId);
        $this->announceRankChange($user, $xpBefore);

        return $this->compactUnlocks($unlocked);
    }

    /**
     * The measured value for one rule. Path rules name their path in the key
     * (path_newbie_complete) and count 1 once every challenge in it is done.
     *
     * @param  array<string, int|null>  $cache
     */
    private function definitionValue(User $user, AchievementDefinition $definition, array &$cache): ?int
    {
        $type = (string) $definition->criteria_type;

        if ($type === 'path_complete' || $type === 'coding_path_complete') {
            $coding = $type === 'coding_path_complete';
            $prefix = $coding ? 'coding_path_' : 'path_';
            $key = (string) $definition->achievement_key;

            if (! str_starts_with($key, $prefix) || ! str_ends_with($key, '_complete')) {
                return null;
            }

            $slug = str_replace('_', '-', substr($key, strlen($prefix), -strlen('_complete')));
            $cacheKey = $type.':'.$slug;

            // A level that needs a class is class work: it gives no XP.
            if (in_array($slug, XpPolicy::CLASS_ONLY_LEVELS, true)) {
                return null;
            }

            if (! array_key_exists($cacheKey, $cache)) {
                $categoryId = DB::table('challenge_categories')->where('slug', $slug)->value('id');
                $cache[$cacheKey] = $categoryId !== null && $this->completedCategoryPath($user, (int) $categoryId, $coding) ? 1 : 0;
            }

            return $cache[$cacheKey];
        }

        return $this->metric($user, $type, $cache);
    }

    /**
     * The learner's current value for one achievement criteria type, or null
     * for a type this service does not measure (it is then never unlocked).
     *
     * @param  array<string, int|null>  $cache
     */
    private function metric(User $user, string $type, array &$cache): ?int
    {
        if (array_key_exists($type, $cache)) {
            return $cache[$type];
        }

        $userId = (int) $user->id;

        // Class work never counts toward a reward: such a rule is not
        // measured at all, so it cannot unlock even with a target of 0.
        if (in_array($type, self::CLASS_ACTIVITY_TYPES, true)) {
            return $cache[$type] = null;
        }

        // Only challenges that award XP count (platform challenges outside
        // the class-only levels).
        $xpChallenges = fn ($query) => XpPolicy::selectXpChallengeIds($query);
        $xpQuestions = fn ($query) => XpPolicy::selectXpCodingQuestionIds($query);

        $value = match ($type) {
            'xp_total' => (int) User::query()->whereKey($userId)->value('xp'),
            'streak_days' => (int) User::query()->whereKey($userId)->value('streak'),
            'code_runs' => $this->hasTable('ide_execution_logs')
                ? (int) DB::table('ide_execution_logs')->where('user_id', $userId)->count()
                : 0,
            'challenge_attempts' => $this->hasTable('challenge_attempts')
                ? (int) DB::table('challenge_attempts')->where('user_id', $userId)
                    ->whereIn('challenge_id', $xpChallenges)
                    ->whereIn('status', ['submitted', 'expired'])->count()
                : 0,
            'challenge_passes' => $this->hasTable('challenge_attempts')
                ? (int) DB::table('challenge_attempts')->where('user_id', $userId)
                    ->whereIn('challenge_id', $xpChallenges)
                    ->whereIn('status', ['submitted', 'expired'])
                    ->where('total_questions', '>', 0)
                    ->whereRaw('score * 100 >= total_questions * 70')
                    ->distinct()
                    ->count('challenge_id')
                : 0,
            'coding_submissions' => $this->hasTable('coding_submissions')
                ? (int) DB::table('coding_submissions')->where('user_id', $userId)->where('voided', false)
                    ->whereIn('coding_question_id', $xpQuestions)->count()
                : 0,
            'coding_passes' => $this->hasTable('coding_submissions')
                ? (int) DB::table('coding_submissions')->where('user_id', $userId)
                    ->whereIn('coding_question_id', $xpQuestions)
                    ->where('voided', false)->where('status', 'passed')
                    ->distinct()
                    ->count('coding_question_id')
                : 0,
            'test_cases_passed' => $this->hasTable('coding_submissions')
                ? (int) DB::table('coding_submissions')->where('user_id', $userId)->where('voided', false)
                    ->whereIn('coding_question_id', $xpQuestions)
                    ->groupBy('coding_question_id')
                    ->select(DB::raw('MAX(tests_passed) as best'))
                    ->pluck('best')
                    ->sum()
                : 0,
            'lesson_completions' => $this->hasTable('lesson_user')
                ? (int) DB::table('lesson_user')->where('user_id', $userId)->where('is_completed', true)->count()
                : 0,
            'active_days' => $this->activeDays($userId),

            // Criteria types used by the older GamificationSeeder rules.
            'mcq_pass' => $this->metric($user, 'challenge_passes', $cache),
            'coding_pass' => $this->metric($user, 'coding_passes', $cache),
            'streak' => $this->metric($user, 'streak_days', $cache),
            'perfect_score' => $this->hasTable('challenge_attempts')
                ? (int) DB::table('challenge_attempts')->where('user_id', $userId)
                    ->whereIn('challenge_id', $xpChallenges)
                    ->whereIn('status', ['submitted', 'expired'])
                    ->where('total_questions', '>', 0)
                    ->whereColumn('score', '>=', 'total_questions')
                    ->distinct()
                    ->count('challenge_id')
                : 0,
            'coding_perfect' => $this->hasTable('coding_submissions')
                ? (int) DB::table('coding_submissions')->where('user_id', $userId)
                    ->whereIn('coding_question_id', $xpQuestions)
                    ->where('voided', false)->where('status', 'passed')
                    ->where('tests_total', '>', 0)
                    ->whereColumn('tests_passed', '>=', 'tests_total')
                    ->distinct()
                    ->count('coding_question_id')
                : 0,
            'fast_pass' => $this->hasTable('challenge_attempts')
                ? (int) DB::table('challenge_attempts')->where('user_id', $userId)
                    ->whereIn('challenge_id', $xpChallenges)
                    ->whereIn('status', ['submitted', 'expired'])
                    ->where('total_questions', '>', 0)
                    ->where('time_limit_seconds', '>', 0)
                    ->whereRaw('score * 100 >= total_questions * 70')
                    ->whereRaw('time_taken_seconds * 2 <= time_limit_seconds')
                    ->distinct()
                    ->count('challenge_id')
                : 0,
            'coding_challenge_complete' => $this->completedCodingChallenges($userId),
            default => null,
        };

        return $cache[$type] = $value;
    }

    /**
     * Number of different days with saved learning work on platform content.
     * Class work (assignments, assessments, class-only challenges) is not
     * counted.
     */
    private function activeDays(int $userId): int
    {
        $sources = [
            ['challenge_attempts', 'user_id', 'submitted_at', 'challenge_id', fn ($query) => XpPolicy::selectXpChallengeIds($query)],
            ['coding_submissions', 'user_id', 'created_at', 'coding_question_id', fn ($query) => XpPolicy::selectXpCodingQuestionIds($query)],
            ['ide_execution_logs', 'user_id', 'created_at', null, null],
            ['lesson_user', 'user_id', 'updated_at', null, null],
        ];

        $days = [];

        foreach ($sources as [$table, $userColumn, $dateColumn, $contentColumn, $contentFilter]) {
            if (! $this->hasTable($table)) {
                continue;
            }

            $rows = DB::table($table)
                ->where($userColumn, $userId)
                ->whereNotNull($dateColumn)
                ->when($contentColumn !== null, fn ($query) => $query->whereIn($contentColumn, $contentFilter))
                ->select(DB::raw('DISTINCT DATE('.$dateColumn.') as day'))
                ->pluck('day');

            foreach ($rows as $day) {
                $days[(string) $day] = true;
            }
        }

        return count($days);
    }

    /**
     * Event rules from the older GamificationSeeder, which unlocks achievements
     * by key when a specific attempt qualifies. A key that is not defined (or
     * not active) is simply skipped.
     *
     * @return array<int, UserAchievement|null>
     */
    private function legacyMcqUnlocks(User $user, Challenge $challenge, int $correct, int $total, int $timeTakenSeconds, bool $passed): array
    {
        $unlocked = [];
        $percent = $total > 0 ? ($correct / $total) * 100 : 0;
        $limit = (int) ($challenge->time_limit_seconds ?? 0);
        $ratio = $limit > 0 ? $timeTakenSeconds / $limit : null;

        if ($passed) {
            $unlocked[] = $this->unlockKey($user, 'first_mcq_pass', 'mcq_challenge', (int) $challenge->id, $percent);
        }

        if ($total > 0 && $correct >= $total) {
            $unlocked[] = $this->unlockKey($user, 'perfect_run', 'mcq_challenge', (int) $challenge->id, 100);
        }

        if ($passed && $ratio !== null && $ratio <= 0.50) {
            $unlocked[] = $this->unlockKey($user, 'fast_solver', 'mcq_challenge', (int) $challenge->id, round($ratio * 100, 2));
        }

        if ($passed && $this->completedChallengePath($user, $challenge, false)) {
            $slug = str_replace('-', '_', (string) optional($challenge->category)->slug);
            $unlocked[] = $this->unlockKey($user, 'path_'.$slug.'_complete', 'mcq_challenge_path', (int) $challenge->id, $percent);
        }

        return $unlocked;
    }

    /** @return array<int, UserAchievement|null> */
    private function legacyCodingUnlocks(User $user, Challenge $challenge, CodingQuestion $question, CodingSubmission $submission, bool $challengeComplete): array
    {
        $unlocked = [];
        $testsTotal = (int) $submission->tests_total;
        $testsPassed = (int) $submission->tests_passed;
        $percent = $testsTotal > 0 ? ($testsPassed / $testsTotal) * 100 : 0;

        if ($submission->status === 'passed') {
            $unlocked[] = $this->unlockKey($user, 'first_coding_pass', 'coding_question', (int) $question->id, $percent);

            if ($testsTotal > 0 && $testsPassed >= $testsTotal) {
                $unlocked[] = $this->unlockKey($user, 'coding_clean_sweep', 'coding_question', (int) $question->id, 100);
            }
        }

        if ($challengeComplete) {
            $unlocked[] = $this->unlockKey($user, 'coding_challenge_finisher', 'coding_challenge', (int) $challenge->id, 100);

            if ($this->completedChallengePath($user, $challenge, true)) {
                $slug = str_replace('-', '_', (string) optional($challenge->category)->slug);
                $unlocked[] = $this->unlockKey($user, 'coding_path_'.$slug.'_complete', 'coding_challenge_path', (int) $challenge->id, $percent);
            }
        }

        return $unlocked;
    }

    /** Number of coding challenges whose every problem this learner solved. */
    private function completedCodingChallenges(int $userId): int
    {
        if (! $this->hasTable('coding_submissions') || ! $this->hasTable('coding_questions')) {
            return 0;
        }

        $solved = DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.user_id', $userId)
            ->whereIn('coding_questions.challenge_id', fn ($query) => XpPolicy::selectXpChallengeIds($query))
            ->where('coding_submissions.status', 'passed')
            ->where('coding_submissions.voided', false)
            ->groupBy('coding_questions.challenge_id')
            ->select('coding_questions.challenge_id', DB::raw('COUNT(DISTINCT coding_submissions.coding_question_id) as solved'))
            ->pluck('solved', 'challenge_id');

        if ($solved->isEmpty()) {
            return 0;
        }

        $totals = DB::table('coding_questions')
            ->whereIn('challenge_id', $solved->keys()->all())
            ->groupBy('challenge_id')
            ->select('challenge_id', DB::raw('COUNT(*) as total'))
            ->pluck('total', 'challenge_id');

        return $solved->filter(fn ($count, $challengeId): bool => (int) $count >= (int) ($totals[$challengeId] ?? PHP_INT_MAX))->count();
    }

    /** Every active challenge in the same learning path has been passed. */
    private function completedChallengePath(User $user, Challenge $challenge, bool $coding): bool
    {
        $challenge->loadMissing('category');
        if (! $challenge->category) {
            return false;
        }

        return $this->completedCategoryPath($user, (int) $challenge->challenge_category_id, $coding);
    }

    /**
     * Every active platform challenge of one path is done. Instructor-built
     * class challenges never count toward finishing a path.
     */
    private function completedCategoryPath(User $user, int $categoryId, bool $coding): bool
    {
        $pathChallenges = Challenge::where('challenge_category_id', $categoryId)
            ->where('is_coding_challenge', $coding)
            ->where('is_active', true)
            ->platform()
            ->get();

        if ($pathChallenges->isEmpty()) {
            return false;
        }

        foreach ($pathChallenges as $pathChallenge) {
            if ($coding) {
                $questionIds = $pathChallenge->codingQuestions()->pluck('id');
                if ($questionIds->isEmpty()) {
                    return false;
                }

                $passedCount = CodingSubmission::where('user_id', $user->id)
                    ->whereIn('coding_question_id', $questionIds)
                    ->where('status', 'passed')
                    ->where('voided', false)
                    ->distinct()
                    ->count('coding_question_id');

                if ($passedCount < $questionIds->count()) {
                    return false;
                }

                continue;
            }

            $passed = ChallengeAttempt::query()
                ->where('user_id', $user->id)
                ->where('challenge_id', $pathChallenge->id)
                ->whereIn('status', ['submitted', 'expired'])
                ->where('total_questions', '>', 0)
                ->whereRaw('score * 100 >= total_questions * 70')
                ->exists();

            if (! $passed) {
                return false;
            }
        }

        return true;
    }

    private function unlockKey(User $user, string $key, string $source, ?int $sourceId, float $progress): ?UserAchievement
    {
        $definition = AchievementDefinition::where('achievement_key', $key)
            ->where('is_active', true)
            ->first();

        return $definition ? $this->unlockDefinition($user, $definition, $source, $sourceId, $progress) : null;
    }

    private function currentXp(User $user): int
    {
        return (int) User::query()->whereKey($user->id)->value('xp');
    }

    private function unlockDefinition(User $user, AchievementDefinition $definition, string $source, ?int $sourceId, float $progress): ?UserAchievement
    {
        return DB::transaction(function () use ($user, $definition, $source, $sourceId, $progress): ?UserAchievement {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            $existing = UserAchievement::where('user_id', $lockedUser->id)
                ->where('achievement_definition_id', $definition->id)
                ->exists();

            if ($existing) {
                return null;
            }

            $achievement = UserAchievement::create([
                'user_id' => $lockedUser->id,
                'achievement_definition_id' => $definition->id,
                'unlocked_at' => now(),
                'trigger_source' => $source,
                'source_id' => $sourceId,
                'progress_value' => $progress,
                'details' => [
                    'source' => $source,
                    'criteria_type' => $definition->criteria_type,
                    'criteria_value' => (int) $definition->criteria_value,
                    'progress' => $progress,
                ],
            ]);

            if ((int) $definition->xp_reward > 0) {
                $lockedUser->increment('xp', (int) $definition->xp_reward);
            }

            $this->notify(
                $lockedUser,
                'achievement_unlocked_'.$definition->achievement_key,
                'Achievement unlocked: '.$definition->name.' +'.$definition->xp_reward.' XP'
            );

            return $achievement->load('achievement');
        }, 3);
    }

    /** @param  array<int, string>  $targetTypes */
    private function incrementMissions(User $user, array $targetTypes): void
    {
        if (! $this->hasTable('mission_definitions') || ! $this->hasTable('student_mission_progress')) {
            return;
        }

        DB::transaction(function () use ($user, $targetTypes): void {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $missions = MissionDefinition::where('is_active', true)
                ->whereIn('target_type', $targetTypes)
                ->get();

            foreach ($missions as $mission) {
                $periodStart = $mission->period_type === 'weekly'
                    ? now()->startOfWeek()->toDateString()
                    : now()->toDateString();

                $progress = $this->missionProgressFor($lockedUser->id, $mission->id, $periodStart);
                $progress = StudentMissionProgress::query()
                    ->whereKey($progress->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($progress->is_completed) {
                    continue;
                }

                $progress->progress_count = min((int) $mission->target_count, (int) $progress->progress_count + 1);

                if ($progress->progress_count >= (int) $mission->target_count) {
                    $progress->is_completed = true;
                    $progress->completed_at = now();
                    $progress->xp_awarded = (int) $mission->xp_reward;
                    if ((int) $mission->xp_reward > 0) {
                        $lockedUser->increment('xp', (int) $mission->xp_reward);
                    }
                    $this->notify($lockedUser, 'mission_completed_'.$mission->mission_key.'_'.$periodStart, 'Mission complete: '.$mission->title.' +'.$mission->xp_reward.' XP');
                }

                $progress->save();
            }
        }, 3);
    }

    /**
     * Find or start a student's progress row for one mission period.
     *
     * period_start is cast to "date", so Eloquent stores it with a time part
     * ("2026-09-19 00:00:00") on drivers without a real DATE type (SQLite).
     * firstOrCreate compared that against the bare "2026-09-19" string, never
     * found the row, and tried to insert a duplicate, which failed on the
     * student_mission_period_unique index. whereDate() matches the day on
     * every driver, so the existing row is found and reused.
     */
    private function missionProgressFor(int $userId, int $missionId, string $periodStart): StudentMissionProgress
    {
        $progress = StudentMissionProgress::query()
            ->where('user_id', $userId)
            ->where('mission_definition_id', $missionId)
            ->whereDate('period_start', $periodStart)
            ->first();

        if ($progress) {
            return $progress;
        }

        return StudentMissionProgress::create([
            'user_id' => $userId,
            'mission_definition_id' => $missionId,
            'period_start' => $periodStart,
            'progress_count' => 0,
            'is_completed' => false,
            'xp_awarded' => 0,
        ]);
    }

    private function updateStreak(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $last = $lockedUser->last_activity ? Carbon::parse($lockedUser->last_activity)->startOfDay() : null;
            $today = now()->startOfDay();

            if (! $last) {
                $lockedUser->forceFill(['streak' => max(1, (int) $lockedUser->streak), 'last_activity' => now()])->save();

                return;
            }

            if ($last->equalTo($today)) {
                return;
            }

            $newStreak = $last->copy()->addDay()->equalTo($today)
                ? ((int) $lockedUser->streak + 1)
                : 1;

            $lockedUser->forceFill(['streak' => $newStreak, 'last_activity' => now()])->save();
        }, 3);
    }

    private function notify(User $user, string $type, string $text): void
    {
        $title = match (true) {
            str_starts_with($type, 'achievement_unlocked_') => 'Achievement unlocked',
            str_starts_with($type, 'mission_completed_') => 'Mission completed',
            str_starts_with($type, 'rank_up_') => 'Rank up',
            default => 'Progress update',
        };

        app(StudentNotificationService::class)->send(
            $user,
            $type,
            $title,
            $text,
            route('student.achievements.index'),
            [],
            $type
        );
    }

    /** @return array<int, array{name:string, xp_reward:int}> */
    private function compactUnlocks(array $items): array
    {
        return collect($items)
            ->filter()
            ->map(function (UserAchievement $item) {
                return [
                    'name' => $item->achievement->name,
                    'xp_reward' => (int) $item->achievement->xp_reward,
                ];
            })
            ->values()
            ->all();
    }

    private function tablesReady(): bool
    {
        return $this->hasTable('achievement_definitions') && $this->hasTable('user_achievements');
    }

    private function hasTable(string $table): bool
    {
        return $this->tableCache[$table] ??= SchemaInspector::hasTable($table);
    }
}
