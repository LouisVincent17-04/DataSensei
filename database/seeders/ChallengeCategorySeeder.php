<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChallengeCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('challenge_categories')->truncate();
        
        $categories = [
            [
                'name' => 'Newbie',
                'slug' => 'newbie',
                'target_audience' => 'Just starting, little to no background in data',
                'description' => 'Learn the absolute basics of Python and data literacy from scratch. No prior coding experience required.',
                // Seedling/sprout — represents a beginner just starting to grow
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12m0 0C12 7 7 5 3 6c0 4 2 8 9 6zm0 0c0-5 5-7 9-6-1 4-4 7-9 6z" /></svg>',
                'order_index' => 1
            ],
            [
                'name' => 'University Student',
                'slug' => 'university-student',
                'target_audience' => 'Currently studying Data Science, CS, or related fields',
                'description' => 'Bridge the gap between academic theory and practical application. Focus on algorithms, stats, and real coding.',
                // Graduation cap — directly represents a university student
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>',
                'order_index' => 2
            ],
            [
                'name' => 'Intermediate',
                'slug' => 'intermediate',
                'target_audience' => 'Has basics (Python, stats) and can do small projects',
                'description' => 'Level up your skills. Dive into data wrangling, machine learning fundamentals, and visualization techniques.',
                // Bar chart — represents growing skills and data work at a mid level
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18M7 20V10m5 10V4m5 16v-7" /></svg>',
                'order_index' => 3
            ],
            [
                'name' => 'Advanced',
                'slug' => 'advanced',
                'target_audience' => 'Strong skills, can build models and real-world systems',
                'description' => 'Tackle complex datasets, deep learning, optimization, and scalable data pipelines.',
                // Lightning bolt — represents power, speed, and high capability
'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path fill="none" d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" /></svg>',
                'order_index' => 4
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'target_audience' => 'Working in industry (Data Analyst, Data Scientist, ML Engineer)',
                'description' => 'Master MLOps, system architecture, big data frameworks, and high-level strategy for enterprise systems.',
                // Briefcase — directly represents a working professional in industry
'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect fill="none" x="2" y="7" width="20" height="14" rx="2" ry="2" /><path fill="none" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" /><line x1="12" y1="12" x2="12" y2="12" /><line x1="8" y1="12" x2="16" y2="12" /></svg>',
                'order_index' => 5
            ]
        ];

        foreach ($categories as $category) {
            ChallengeCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}