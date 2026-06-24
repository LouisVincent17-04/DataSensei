<?php

namespace Database\Seeders;

use App\Models\MissionDefinition;
use Illuminate\Database\Seeder;

class MissionDefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        $missions = [
            ['daily_run_code', 'Run Code Today', 'Run at least one Python or SQL activity today.', 'daily', 'code_runs', 1, 20, 10],
            ['daily_complete_lesson', 'Complete a Lesson', 'Finish one lesson or module activity today.', 'daily', 'lesson_completions', 1, 30, 20],
            ['daily_attempt_challenge', 'Attempt a Challenge', 'Submit one MCQ or coding challenge today.', 'daily', 'challenge_attempts', 1, 35, 30],
            ['weekly_coding_practice', 'Weekly Coding Practice', 'Submit five coding challenge attempts this week.', 'weekly', 'coding_submissions', 5, 120, 40],
            ['weekly_assignment_progress', 'Weekly Assignment Progress', 'Submit two class assignment attempts this week.', 'weekly', 'assignment_submissions', 2, 150, 50],
            ['weekly_mastery_builder', 'Weekly Mastery Builder', 'Complete ten learning activities this week.', 'weekly', 'activity_count', 10, 180, 60],
        ];

        foreach ($missions as [$key, $title, $description, $periodType, $targetType, $targetCount, $xpReward, $sortOrder]) {
            MissionDefinition::updateOrCreate(
                ['mission_key' => $key],
                [
                    'title' => $title,
                    'description' => $description,
                    'period_type' => $periodType,
                    'target_type' => $targetType,
                    'target_count' => $targetCount,
                    'xp_reward' => $xpReward,
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ]
            );
        }
    }
}
