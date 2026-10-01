<?php

namespace App\Console\Commands;

use App\Models\AchievementDefinition;
use App\Services\GamificationService;
use Illuminate\Console\Command;

/**
 * Unlocks, for every active student, each active achievement whose rule they
 * already meet from work saved earlier (challenges, coding problems, lessons,
 * assignments, runs, XP, streaks). Achievements are never unlocked twice, so
 * the command can be run again at any time. The same sync is available from
 * Admin > Gamification > Sync achievements.
 */
class SyncAchievements extends Command
{
    protected $signature = 'achievements:sync
        {--email= : Only this student (e-mail address)}';

    protected $description = 'Unlock every achievement students already qualify for, based on their saved work';

    public function handle(GamificationService $gamification): int
    {
        $rules = AchievementDefinition::query()->where('is_active', true)->orderBy('sort_order')->get();

        if ($rules->isEmpty()) {
            $this->warn('There are no active achievements. Seed them with: php artisan db:seed --class=AchievementDefinitionsSeeder');

            return self::SUCCESS;
        }

        $unmeasured = $rules->reject(fn (AchievementDefinition $rule): bool => array_key_exists((string) $rule->criteria_type, GamificationService::CRITERIA_TYPES));
        foreach ($unmeasured as $rule) {
            $this->warn("\"{$rule->name}\" uses the rule type \"{$rule->criteria_type}\", which is not measured, so it cannot unlock. Pick a listed type on the Gamification page.");
        }

        $email = $this->option('email');
        $this->info('Checking '.($email ? $email : 'every active student').' against '.$rules->count().' active achievement(s)...');

        $summary = $gamification->syncAllLearners(is_string($email) ? $email : null, function ($student, array $unlocked): void {
            if ($unlocked !== []) {
                $names = collect($unlocked)->map(fn ($record) => $record->achievement->name ?? 'Achievement')->implode(', ');
                $this->line("  {$student->email}: {$names}");
            }
        });

        $this->newLine();
        $this->info("Students checked: {$summary['students']}");
        $this->info("Achievements unlocked: {$summary['unlocked']} (+".number_format($summary['xp']).' XP in total)');

        foreach ($summary['by_achievement'] as $name => $count) {
            $this->line("  {$name}: {$count}");
        }

        return self::SUCCESS;
    }
}
