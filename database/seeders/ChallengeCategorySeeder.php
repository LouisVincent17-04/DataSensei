<?php

namespace Database\Seeders;

use App\Models\ChallengeCategory;
use Illuminate\Database\Seeder;

class ChallengeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Newbie',
                'slug' => 'newbie',
                'target_audience' => 'Beginners',
                'description' => 'Introductory questions for first-time learners building basic confidence.',
                'order_index' => 1,
            ],
            [
                'name' => 'University Student',
                'slug' => 'university-student',
                'target_audience' => 'College learners',
                'description' => 'Foundational academic exercises aligned with early programming and data science coursework.',
                'order_index' => 2,
            ],
            [
                'name' => 'Intermediate',
                'slug' => 'intermediate',
                'target_audience' => 'Developing practitioners',
                'description' => 'Applied problem-solving challenges for learners who already understand the basics.',
                'order_index' => 3,
            ],
            [
                'name' => 'Advanced',
                'slug' => 'advanced',
                'target_audience' => 'Advanced learners',
                'description' => 'Higher-level analytical and coding challenges for strong performers.',
                'order_index' => 4,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'target_audience' => 'Career-ready learners',
                'description' => 'Professional-level tasks focused on readiness for data-driven technical work.',
                'order_index' => 5,
            ],
        ];

        foreach ($categories as $category) {
            ChallengeCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['icon_svg' => null]
            );
        }
    }
}
