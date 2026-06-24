<?php

namespace Database\Seeders;

use App\Models\AchievementDefinition;
use App\Models\MissionDefinition;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['first_mcq_pass', 'First Challenge Pass', 'Pass your first MCQ challenge.', '✅', 'green', 25, 'mcq_pass', 1, 10],
            ['perfect_run', 'Perfect Run', 'Get a perfect score in an MCQ challenge.', '💯', 'gold', 75, 'perfect_score', 1, 20],
            ['fast_solver', 'Fast Solver', 'Pass a challenge using half or less of the time limit.', '⚡', 'yellow', 60, 'fast_pass', 1, 30],
            ['first_coding_pass', 'First Code Accepted', 'Pass your first coding problem.', '🧑‍💻', 'blue', 35, 'coding_pass', 1, 40],
            ['coding_clean_sweep', 'Clean Sweep', 'Pass all visible and hidden tests for a coding problem.', '🧹', 'cyan', 70, 'coding_perfect', 1, 50],
            ['coding_challenge_finisher', 'Coding Challenge Finisher', 'Complete every coding problem in a challenge.', '🚀', 'purple', 100, 'coding_challenge_complete', 1, 60],
            ['assignment_finisher', 'Assignment Finisher', 'Submit your first instructor-given assignment.', '📘', 'blue', 30, 'assignment_submit', 1, 70],
            ['perfect_assignment', 'Perfect Assignment', 'Get full points in an instructor-given assignment.', '🏅', 'gold', 85, 'assignment_perfect', 1, 80],
            ['clean_attempt', 'Clean Attempt', 'Submit an assignment without anti-cheat violation records.', '🛡️', 'green', 40, 'clean_assignment', 1, 90],
            ['seven_day_streak', 'Consistency Master', 'Keep learning for 7 days in a row.', '🔥', 'orange', 150, 'streak', 7, 100],
            ['path_newbie_complete', 'Newbie Path Clear', 'Complete all MCQ challenges in the Newbie path.', '🌱', 'green', 120, 'path_complete', 1, 110],
            ['path_university_student_complete', 'University Path Clear', 'Complete all MCQ challenges in the University Student path.', '🎓', 'blue', 140, 'path_complete', 1, 120],
            ['path_intermediate_complete', 'Intermediate Path Clear', 'Complete all MCQ challenges in the Intermediate path.', '📈', 'purple', 160, 'path_complete', 1, 130],
            ['path_advanced_complete', 'Advanced Path Clear', 'Complete all MCQ challenges in the Advanced path.', '🧠', 'gold', 200, 'path_complete', 1, 140],
            ['path_professional_complete', 'Professional Path Clear', 'Complete all MCQ challenges in the Professional path.', '👑', 'red', 250, 'path_complete', 1, 150],
            ['coding_path_newbie_complete', 'Newbie Coding Path Clear', 'Complete all coding challenges in the Newbie path.', '🐍', 'green', 150, 'coding_path_complete', 1, 160],
            ['coding_path_university_student_complete', 'University Coding Path Clear', 'Complete all coding challenges in the University Student path.', '💻', 'blue', 170, 'coding_path_complete', 1, 170],
            ['coding_path_intermediate_complete', 'Intermediate Coding Path Clear', 'Complete all coding challenges in the Intermediate path.', '🧩', 'purple', 190, 'coding_path_complete', 1, 180],
            ['coding_path_advanced_complete', 'Advanced Coding Path Clear', 'Complete all coding challenges in the Advanced path.', '⚙️', 'gold', 230, 'coding_path_complete', 1, 190],
            ['coding_path_professional_complete', 'Professional Coding Path Clear', 'Complete all coding challenges in the Professional path.', '🏆', 'red', 280, 'coding_path_complete', 1, 200],
        ];

        foreach ($achievements as [$key, $name, $description, $icon, $color, $xp, $type, $value, $sort]) {
            AchievementDefinition::updateOrCreate(
                ['achievement_key' => $key],
                [
                    'name' => $name,
                    'description' => $description,
                    'icon' => $icon,
                    'badge_color' => $color,
                    'xp_reward' => $xp,
                    'criteria_type' => $type,
                    'criteria_value' => $value,
                    'is_active' => true,
                    'sort_order' => $sort,
                ]
            );
        }

        $missions = [
            ['daily_complete_one', 'Daily Focus', 'Complete any one assessment today.', 'daily', 'complete_any_assessment', 1, 25, 10],
            ['daily_solve_code', 'Daily Code Spark', 'Solve one coding problem today.', 'daily', 'solve_coding_problem', 1, 35, 20],
            ['weekly_three_assessments', 'Weekly Momentum', 'Complete three assessments this week.', 'weekly', 'complete_any_assessment', 3, 100, 30],
            ['weekly_assignment', 'Classwork Ready', 'Submit one instructor assignment this week.', 'weekly', 'submit_assignment', 1, 80, 40],
            ['weekly_coding_challenge', 'Coding Sprint', 'Finish one full coding challenge this week.', 'weekly', 'complete_coding_challenge', 1, 120, 50],
        ];

        foreach ($missions as [$key, $title, $description, $period, $target, $count, $xp, $sort]) {
            MissionDefinition::updateOrCreate(
                ['mission_key' => $key],
                [
                    'title' => $title,
                    'description' => $description,
                    'period_type' => $period,
                    'target_type' => $target,
                    'target_count' => $count,
                    'xp_reward' => $xp,
                    'is_active' => true,
                    'sort_order' => $sort,
                ]
            );
        }
    }
}
