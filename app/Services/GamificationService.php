<?php

namespace App\Services;

use App\Models\AchievementDefinition;
use App\Models\AssignmentSubmission;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\CodingQuestion;
use App\Models\CodingSubmission;
use App\Models\MissionDefinition;
use App\Models\StudentMissionProgress;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GamificationService
{
    public function awardForMcqChallenge(User $user, Challenge $challenge, int $correct, int $total, int $timeTakenSeconds, bool $passed): array
    {
        if (!$this->tablesReady()) {
            return [];
        }

        $unlocked = [];
        $percent = $total > 0 ? ($correct / $total) * 100 : 0;
        $ratio = $challenge->time_limit_seconds > 0 ? $timeTakenSeconds / $challenge->time_limit_seconds : null;

        if ($passed) {
            $unlocked[] = $this->unlock($user, 'first_mcq_pass', 'mcq_challenge', $challenge->id, $percent);
            $this->incrementMissions($user, ['complete_any_assessment', 'complete_mcq_challenge']);
        }

        if ($total > 0 && $correct >= $total) {
            $unlocked[] = $this->unlock($user, 'perfect_run', 'mcq_challenge', $challenge->id, 100);
        }

        if ($passed && $ratio !== null && $ratio <= 0.50) {
            $unlocked[] = $this->unlock($user, 'fast_solver', 'mcq_challenge', $challenge->id, round($ratio * 100, 2));
        }

        if ($this->completedChallengePath($user, $challenge, false)) {
            $slug = optional($challenge->category)->slug;
            $unlocked[] = $this->unlock($user, 'path_' . str_replace('-', '_', (string) $slug) . '_complete', 'mcq_challenge_path', $challenge->id, $percent);
        }

        $this->updateStreak($user);

        return $this->compactUnlocks($unlocked);
    }

    public function awardForCodingSubmission(User $user, Challenge $challenge, CodingQuestion $question, CodingSubmission $submission, bool $challengeComplete): array
    {
        if (!$this->tablesReady()) {
            return [];
        }

        $unlocked = [];
        $percent = $submission->tests_total > 0 ? ($submission->tests_passed / $submission->tests_total) * 100 : 0;

        if ($submission->status === 'passed') {
            $unlocked[] = $this->unlock($user, 'first_coding_pass', 'coding_question', $question->id, $percent);
            $this->incrementMissions($user, ['complete_any_assessment', 'solve_coding_problem']);
        }

        if ($submission->status === 'passed' && $submission->tests_total > 0 && $submission->tests_passed >= $submission->tests_total) {
            $unlocked[] = $this->unlock($user, 'coding_clean_sweep', 'coding_question', $question->id, 100);
        }

        if ($challengeComplete) {
            $unlocked[] = $this->unlock($user, 'coding_challenge_finisher', 'coding_challenge', $challenge->id, 100);
            $this->incrementMissions($user, ['complete_coding_challenge']);
        }

        if ($this->completedChallengePath($user, $challenge, true)) {
            $slug = optional($challenge->category)->slug;
            $unlocked[] = $this->unlock($user, 'coding_path_' . str_replace('-', '_', (string) $slug) . '_complete', 'coding_challenge_path', $challenge->id, $percent);
        }

        $this->updateStreak($user);

        return $this->compactUnlocks($unlocked);
    }

    public function awardForAssignmentSubmission(User $user, AssignmentSubmission $submission): array
    {
        if (!$this->tablesReady()) {
            return [];
        }

        $unlocked = [];
        $percent = $submission->total_points > 0 ? ($submission->score / $submission->total_points) * 100 : 0;

        $unlocked[] = $this->unlock($user, 'assignment_finisher', 'assignment_submission', $submission->id, $percent);
        $this->incrementMissions($user, ['complete_any_assessment', 'submit_assignment']);

        if ($percent >= 100) {
            $unlocked[] = $this->unlock($user, 'perfect_assignment', 'assignment_submission', $submission->id, 100);
        }

        $hasAntiCheatEvents = Schema::hasTable('anti_cheat_events') && DB::table('anti_cheat_events')
            ->where('assignment_submission_id', $submission->id)
            ->exists();

        if (!$hasAntiCheatEvents) {
            $unlocked[] = $this->unlock($user, 'clean_attempt', 'assignment_submission', $submission->id, $percent);
        }

        $this->updateStreak($user);

        return $this->compactUnlocks($unlocked);
    }

    public function currentMissions(User $user)
    {
        if (!Schema::hasTable('mission_definitions') || !Schema::hasTable('student_mission_progress')) {
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

    private function unlock(User $user, string $key, string $source, ?int $sourceId, float $progress): ?UserAchievement
    {
        return DB::transaction(function () use ($user, $key, $source, $sourceId, $progress): ?UserAchievement {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $definition = AchievementDefinition::where('achievement_key', $key)
                ->where('is_active', true)
                ->first();

            if (!$definition) {
                return null;
            }

            $existing = UserAchievement::where('user_id', $lockedUser->id)
                ->where('achievement_definition_id', $definition->id)
                ->first();

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
                    'progress' => $progress,
                ],
            ]);

            if ((int) $definition->xp_reward > 0) {
                $lockedUser->increment('xp', (int) $definition->xp_reward);
            }

            $this->notify($lockedUser, 'achievement_unlocked_' . $definition->achievement_key, 'Achievement unlocked: ' . $definition->name . ' +' . $definition->xp_reward . ' XP');

            return $achievement->load('achievement');
        }, 3);
    }

    private function incrementMissions(User $user, array $targetTypes): void
    {
        if (!Schema::hasTable('mission_definitions') || !Schema::hasTable('student_mission_progress')) {
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
                    $this->notify($lockedUser, 'mission_completed_' . $mission->mission_key, 'Mission complete: ' . $mission->title . ' +' . $mission->xp_reward . ' XP');
                }

                $progress->save();
            }
        }, 3);
    }

    private function completedChallengePath(User $user, Challenge $challenge, bool $coding): bool
    {
        $challenge->loadMissing('category');
        if (!$challenge->category) {
            return false;
        }

        $pathChallenges = Challenge::where('challenge_category_id', $challenge->challenge_category_id)
            ->where('is_coding_challenge', $coding)
            ->where('is_active', true)
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
                    ->distinct('coding_question_id')
                    ->count('coding_question_id');

                if ($passedCount < $questionIds->count()) {
                    return false;
                }
            } else {
                $total = $pathChallenge->questions()->count();
                if ($total <= 0) {
                    return false;
                }

                $passed = ChallengeAttempt::query()
                    ->where('user_id', $user->id)
                    ->where('challenge_id', $pathChallenge->id)
                    ->where('is_ranked', true)
                    ->whereIn('status', ['submitted', 'expired'])
                    ->where('total_questions', '>', 0)
                    ->whereRaw('score * 100 >= total_questions * 70')
                    ->exists();

                if (! $passed && ! ChallengeAttempt::where('user_id', $user->id)
                    ->where('challenge_id', $pathChallenge->id)->exists()) {
                    $passed = DB::table('challenge_user')
                        ->where('user_id', $user->id)
                        ->where('challenge_id', $pathChallenge->id)
                        ->where('score', '>=', (int) ceil($total * 0.70))
                        ->exists();
                }

                if (!$passed) {
                    return false;
                }
            }
        }

        return true;
    }

    private function updateStreak(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $last = $lockedUser->last_activity ? Carbon::parse($lockedUser->last_activity)->startOfDay() : null;
            $today = now()->startOfDay();

            if (!$last) {
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

            if ($newStreak >= 7) {
                $this->unlock($lockedUser, 'seven_day_streak', 'streak', null, $newStreak);
            }
        }, 3);
    }

    private function notify(User $user, string $type, string $text): void
    {
        $isAchievement = str_starts_with($type, 'achievement_unlocked_');
        $isMission = str_starts_with($type, 'mission_completed_');

        app(StudentNotificationService::class)->send(
            $user,
            $type,
            $isAchievement ? 'Achievement unlocked' : ($isMission ? 'Mission completed' : 'Progress update'),
            $text,
            route('student.achievements.index'),
            [],
            $type
        );
    }

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
        return Schema::hasTable('achievement_definitions') && Schema::hasTable('user_achievements');
    }
}
