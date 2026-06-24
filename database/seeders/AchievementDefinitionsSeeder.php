<?php

namespace Database\Seeders;

use App\Models\AchievementDefinition;
use Illuminate\Database\Seeder;

class AchievementDefinitionsSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['first_code_run', 'First Code Run', 'Run your first Python program in the IDE.', 'FCR', 'blue', 25, 'code_runs', 1, 10],
            ['first_challenge_pass', 'First Challenge Pass', 'Complete your first MCQ challenge.', 'FCP', 'green', 50, 'challenge_passes', 1, 20],
            ['coding_starter', 'Coding Starter', 'Pass your first coding challenge test set.', 'CS', 'green', 75, 'coding_passes', 1, 30],
            ['practice_rhythm', 'Practice Rhythm', 'Maintain a 3-day learning streak.', 'PR', 'amber', 100, 'streak_days', 3, 40],
            ['module_finisher', 'Module Finisher', 'Complete five lessons or learning activities.', 'MF', 'blue', 120, 'lesson_completions', 5, 50],
            ['assignment_ready', 'Assignment Ready', 'Submit your first instructor assignment.', 'AR', 'purple', 100, 'assignment_submissions', 1, 60],
            ['test_case_climber', 'Test Case Climber', 'Accumulate 50 passed coding test cases.', 'TCC', 'green', 180, 'test_cases_passed', 50, 70],
            ['focused_learner', 'Focused Learner', 'Complete learning activities on seven different days.', 'FL', 'teal', 220, 'active_days', 7, 80],
            ['rank_advancer', 'Rank Advancer', 'Reach at least Practitioner rank.', 'RA', 'indigo', 250, 'xp_total', 500, 90],
            ['data_sensei_elite', 'DataSensei Elite', 'Reach Expert rank through sustained performance.', 'DSE', 'gold', 500, 'xp_total', 7000, 100],
        ];

        foreach ($achievements as [$key, $name, $description, $icon, $color, $xp, $criteriaType, $criteriaValue, $sortOrder]) {
            AchievementDefinition::updateOrCreate(
                ['achievement_key' => $key],
                [
                    'name' => $name,
                    'description' => $description,
                    'icon' => $icon,
                    'badge_color' => $color,
                    'xp_reward' => $xp,
                    'criteria_type' => $criteriaType,
                    'criteria_value' => $criteriaValue,
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ]
            );
        }
    }
}
