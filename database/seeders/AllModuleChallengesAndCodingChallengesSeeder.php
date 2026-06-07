<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-command full reset + seed for ALL DataSensei challenge content.
 *
 * This removes existing MCQ challenges, coding challenges, coding attempts,
 * coding submissions, test cases, and challenge completion records first.
 * Then it reseeds MCQ challenges and coding challenges.
 *
 * Recommended command:
 * php artisan db:seed --class=AllModuleChallengesAndCodingChallengesSeeder
 */
class AllModuleChallengesAndCodingChallengesSeeder extends Seeder
{
    public function run(): void
    {
        $this->cleanupAllChallengeData();
        $this->seedChallengeCategoriesSafely();

        $this->call([
            Module1ChallengeSeederNewbie::class,
            Module1ChallengeSeederUniversityStudent::class,
            Module1ChallengeSeederIntermediate::class,
            Module1ChallengeSeederAdvanced::class,
            Module1ChallengeSeederProfessional::class,
            Module2ChallengeSeederNewbie::class,
            Module2ChallengeSeederUniversityStudent::class,
            Module2ChallengeSeederIntermediate::class,
            Module2ChallengeSeederAdvanced::class,
            Module2ChallengeSeederProfessional::class,
            Module3ChallengeSeederNewbie::class,
            Module3ChallengeSeederUniversityStudent::class,
            Module3ChallengeSeederIntermediate::class,
            Module3ChallengeSeederAdvanced::class,
            Module3ChallengeSeederProfessional::class,
            Module4ChallengeSeederNewbie::class,
            Module4ChallengeSeederUniversityStudent::class,
            Module4ChallengeSeederIntermediate::class,
            Module4ChallengeSeederAdvanced::class,
            Module4ChallengeSeederProfessional::class,
            Module5ChallengeSeederNewbie::class,
            Module5ChallengeSeederUniversityStudent::class,
            Module5ChallengeSeederIntermediate::class,
            Module5ChallengeSeederAdvanced::class,
            Module5ChallengeSeederProfessional::class,
            Module6ChallengeSeederNewbie::class,
            Module6ChallengeSeederUniversityStudent::class,
            Module6ChallengeSeederIntermediate::class,
            Module6ChallengeSeederAdvanced::class,
            Module6ChallengeSeederProfessional::class,
            Module7ChallengeSeederNewbie::class,
            Module7ChallengeSeederUniversityStudent::class,
            Module7ChallengeSeederIntermediate::class,
            Module7ChallengeSeederAdvanced::class,
            Module7ChallengeSeederProfessional::class,
            Module8ChallengeSeederNewbie::class,
            Module8ChallengeSeederUniversityStudent::class,
            Module8ChallengeSeederIntermediate::class,
            Module8ChallengeSeederAdvanced::class,
            Module8ChallengeSeederProfessional::class,
            Module9ChallengeSeederNewbie::class,
            Module9ChallengeSeederUniversityStudent::class,
            Module9ChallengeSeederIntermediate::class,
            Module9ChallengeSeederAdvanced::class,
            Module9ChallengeSeederProfessional::class,
            Module10ChallengeSeederNewbie::class,
            Module10ChallengeSeederUniversityStudent::class,
            Module10ChallengeSeederIntermediate::class,
            Module10ChallengeSeederAdvanced::class,
            Module10ChallengeSeederProfessional::class,
            Module11ChallengeSeederNewbie::class,
            Module11ChallengeSeederUniversityStudent::class,
            Module11ChallengeSeederIntermediate::class,
            Module11ChallengeSeederAdvanced::class,
            Module11ChallengeSeederProfessional::class,
            Module12ChallengeSeederNewbie::class,
            Module12ChallengeSeederUniversityStudent::class,
            Module12ChallengeSeederIntermediate::class,
            Module12ChallengeSeederAdvanced::class,
            Module12ChallengeSeederProfessional::class,
            Module13ChallengeSeederNewbie::class,
            Module13ChallengeSeederUniversityStudent::class,
            Module13ChallengeSeederIntermediate::class,
            Module13ChallengeSeederAdvanced::class,
            Module13ChallengeSeederProfessional::class,
            Module14ChallengeSeederNewbie::class,
            Module14ChallengeSeederUniversityStudent::class,
            Module14ChallengeSeederIntermediate::class,
            Module14ChallengeSeederAdvanced::class,
            Module14ChallengeSeederProfessional::class,
            Module15ChallengeSeederNewbie::class,
            Module15ChallengeSeederUniversityStudent::class,
            Module15ChallengeSeederIntermediate::class,
            Module15ChallengeSeederAdvanced::class,
            Module15ChallengeSeederProfessional::class,
            Module16ChallengeSeederNewbie::class,
            Module16ChallengeSeederUniversityStudent::class,
            Module16ChallengeSeederIntermediate::class,
            Module16ChallengeSeederAdvanced::class,
            Module16ChallengeSeederProfessional::class,
            Module17ChallengeSeederNewbie::class,
            Module17ChallengeSeederUniversityStudent::class,
            Module17ChallengeSeederIntermediate::class,
            Module17ChallengeSeederAdvanced::class,
            Module17ChallengeSeederProfessional::class,
            Module18ChallengeSeederNewbie::class,
            Module18ChallengeSeederUniversityStudent::class,
            Module18ChallengeSeederIntermediate::class,
            Module18ChallengeSeederAdvanced::class,
            Module18ChallengeSeederProfessional::class,
            Module19ChallengeSeederNewbie::class,
            Module19ChallengeSeederUniversityStudent::class,
            Module19ChallengeSeederIntermediate::class,
            Module19ChallengeSeederAdvanced::class,
            Module19ChallengeSeederProfessional::class,
            Module20ChallengeSeederNewbie::class,
            Module20ChallengeSeederUniversityStudent::class,
            Module20ChallengeSeederIntermediate::class,
            Module20ChallengeSeederAdvanced::class,
            Module20ChallengeSeederProfessional::class,
            Module21ChallengeSeederNewbie::class,
            Module21ChallengeSeederUniversityStudent::class,
            Module21ChallengeSeederIntermediate::class,
            Module21ChallengeSeederAdvanced::class,
            Module21ChallengeSeederProfessional::class,
            Module22ChallengeSeederNewbie::class,
            Module22ChallengeSeederUniversityStudent::class,
            Module22ChallengeSeederIntermediate::class,
            Module22ChallengeSeederAdvanced::class,
            Module22ChallengeSeederProfessional::class,
            Module23ChallengeSeederNewbie::class,
            Module23ChallengeSeederUniversityStudent::class,
            Module23ChallengeSeederIntermediate::class,
            Module23ChallengeSeederAdvanced::class,
            Module23ChallengeSeederProfessional::class,
            Module24ChallengeSeederNewbie::class,
            Module24ChallengeSeederUniversityStudent::class,
            Module24ChallengeSeederIntermediate::class,
            Module24ChallengeSeederAdvanced::class,
            Module24ChallengeSeederProfessional::class,
            Module1CodingChallengeSeederNewbie::class,
            Module1CodingChallengeSeederUniversityStudent::class,
            Module1CodingChallengeSeederIntermediate::class,
            Module1CodingChallengeSeederAdvanced::class,
            Module1CodingChallengeSeederProfessional::class,
            Module2CodingChallengeSeederNewbie::class,
            Module2CodingChallengeSeederUniversityStudent::class,
            Module2CodingChallengeSeederIntermediate::class,
            Module2CodingChallengeSeederAdvanced::class,
            Module2CodingChallengeSeederProfessional::class,
            Module3CodingChallengeSeederNewbie::class,
            Module3CodingChallengeSeederUniversityStudent::class,
            Module3CodingChallengeSeederIntermediate::class,
            Module3CodingChallengeSeederAdvanced::class,
            Module3CodingChallengeSeederProfessional::class,
            Module6CodingChallengeSeederNewbie::class,
            Module6CodingChallengeSeederUniversityStudent::class,
            Module6CodingChallengeSeederIntermediate::class,
            Module6CodingChallengeSeederAdvanced::class,
            Module6CodingChallengeSeederProfessional::class,
            Module7CodingChallengeSeederNewbie::class,
            Module7CodingChallengeSeederUniversityStudent::class,
            Module7CodingChallengeSeederIntermediate::class,
            Module7CodingChallengeSeederAdvanced::class,
            Module7CodingChallengeSeederProfessional::class,
            Module8CodingChallengeSeederNewbie::class,
            Module8CodingChallengeSeederUniversityStudent::class,
            Module8CodingChallengeSeederIntermediate::class,
            Module8CodingChallengeSeederAdvanced::class,
            Module8CodingChallengeSeederProfessional::class,
            Module9CodingChallengeSeederNewbie::class,
            Module9CodingChallengeSeederUniversityStudent::class,
            Module9CodingChallengeSeederIntermediate::class,
            Module9CodingChallengeSeederAdvanced::class,
            Module9CodingChallengeSeederProfessional::class,
            Module10CodingChallengeSeederNewbie::class,
            Module10CodingChallengeSeederUniversityStudent::class,
            Module10CodingChallengeSeederIntermediate::class,
            Module10CodingChallengeSeederAdvanced::class,
            Module10CodingChallengeSeederProfessional::class,
            Module11CodingChallengeSeederNewbie::class,
            Module11CodingChallengeSeederUniversityStudent::class,
            Module11CodingChallengeSeederIntermediate::class,
            Module11CodingChallengeSeederAdvanced::class,
            Module11CodingChallengeSeederProfessional::class,
            Module12CodingChallengeSeederNewbie::class,
            Module12CodingChallengeSeederUniversityStudent::class,
            Module12CodingChallengeSeederIntermediate::class,
            Module12CodingChallengeSeederAdvanced::class,
            Module12CodingChallengeSeederProfessional::class,
            Module13CodingChallengeSeederNewbie::class,
            Module13CodingChallengeSeederUniversityStudent::class,
            Module13CodingChallengeSeederIntermediate::class,
            Module13CodingChallengeSeederAdvanced::class,
            Module13CodingChallengeSeederProfessional::class,
            Module14CodingChallengeSeederNewbie::class,
            Module14CodingChallengeSeederUniversityStudent::class,
            Module14CodingChallengeSeederIntermediate::class,
            Module14CodingChallengeSeederAdvanced::class,
            Module14CodingChallengeSeederProfessional::class,
            Module15CodingChallengeSeederNewbie::class,
            Module15CodingChallengeSeederUniversityStudent::class,
            Module15CodingChallengeSeederIntermediate::class,
            Module15CodingChallengeSeederAdvanced::class,
            Module15CodingChallengeSeederProfessional::class,
            Module16CodingChallengeSeederNewbie::class,
            Module16CodingChallengeSeederUniversityStudent::class,
            Module16CodingChallengeSeederIntermediate::class,
            Module16CodingChallengeSeederAdvanced::class,
            Module16CodingChallengeSeederProfessional::class,
            Module17CodingChallengeSeederNewbie::class,
            Module17CodingChallengeSeederUniversityStudent::class,
            Module17CodingChallengeSeederIntermediate::class,
            Module17CodingChallengeSeederAdvanced::class,
            Module17CodingChallengeSeederProfessional::class,
            Module19CodingChallengeSeederNewbie::class,
            Module19CodingChallengeSeederUniversityStudent::class,
            Module19CodingChallengeSeederIntermediate::class,
            Module19CodingChallengeSeederAdvanced::class,
            Module19CodingChallengeSeederProfessional::class,
            Module20CodingChallengeSeederNewbie::class,
            Module20CodingChallengeSeederUniversityStudent::class,
            Module20CodingChallengeSeederIntermediate::class,
            Module20CodingChallengeSeederAdvanced::class,
            Module20CodingChallengeSeederProfessional::class,
            Module21CodingChallengeSeederNewbie::class,
            Module21CodingChallengeSeederUniversityStudent::class,
            Module21CodingChallengeSeederIntermediate::class,
            Module21CodingChallengeSeederAdvanced::class,
            Module21CodingChallengeSeederProfessional::class,
            Module22CodingChallengeSeederNewbie::class,
            Module22CodingChallengeSeederUniversityStudent::class,
            Module22CodingChallengeSeederIntermediate::class,
            Module22CodingChallengeSeederAdvanced::class,
            Module22CodingChallengeSeederProfessional::class,
            Module23CodingChallengeSeederNewbie::class,
            Module23CodingChallengeSeederUniversityStudent::class,
            Module23CodingChallengeSeederIntermediate::class,
            Module23CodingChallengeSeederAdvanced::class,
            Module23CodingChallengeSeederProfessional::class,
            Module24CodingChallengeSeederNewbie::class,
            Module24CodingChallengeSeederUniversityStudent::class,
            Module24CodingChallengeSeederIntermediate::class,
            Module24CodingChallengeSeederAdvanced::class,
            Module24CodingChallengeSeederProfessional::class,
        ]);
    }

    private function cleanupAllChallengeData(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('coding_challenge_retakes')->truncate();
        DB::table('coding_question_attempts')->truncate();
        DB::table('coding_submissions')->truncate();
        DB::table('test_cases')->truncate();
        DB::table('coding_questions')->truncate();

        DB::table('challenge_user')->truncate();
        DB::table('challenge_options')->truncate();
        DB::table('challenge_questions')->truncate();
        DB::table('challenges')->truncate();

        Schema::enableForeignKeyConstraints();
    }

    private function seedChallengeCategoriesSafely(): void
    {
        $now = now();

        $categories = [
            [
                'name' => 'Newbie',
                'slug' => 'newbie',
                'target_audience' => 'Just starting, little to no background in data',
                'description' => 'Learn the absolute basics of Python and data literacy from scratch. No prior coding experience required.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22V12m0 0C12 7 7 5 3 6c0 4 2 8 9 6zm0 0c0-5 5-7 9-6-1 4-4 7-9 6z" /></svg>',
                'order_index' => 1,
            ],
            [
                'name' => 'University Student',
                'slug' => 'university-student',
                'target_audience' => 'Currently studying Data Science, CS, or related fields',
                'description' => 'Bridge the gap between academic theory and practical application. Focus on algorithms, stats, and real coding.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>',
                'order_index' => 2,
            ],
            [
                'name' => 'Intermediate',
                'slug' => 'intermediate',
                'target_audience' => 'Has basics (Python, stats) and can do small projects',
                'description' => 'Level up your skills. Dive into data wrangling, machine learning fundamentals, and visualization techniques.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 20h18M7 20V10m5 10V4m5 16v-7" /></svg>',
                'order_index' => 3,
            ],
            [
                'name' => 'Advanced',
                'slug' => 'advanced',
                'target_audience' => 'Strong skills, can build models and real-world systems',
                'description' => 'Tackle complex datasets, deep learning, optimization, and scalable data pipelines.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path fill="none" d="M13 2L3 14h9l-1 8 10-12h-9l1-8z" /></svg>',
                'order_index' => 4,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'target_audience' => 'Working in industry (Data Analyst, Data Scientist, ML Engineer)',
                'description' => 'Master MLOps, system architecture, big data frameworks, and high-level strategy for enterprise systems.',
                'icon_svg' => '<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect fill="none" x="2" y="7" width="20" height="14" rx="2" ry="2" /><path fill="none" d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" /><line x1="12" y1="12" x2="12" y2="12" /><line x1="8" y1="12" x2="16" y2="12" /></svg>',
                'order_index' => 5,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('challenge_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                array_merge($category, [
                    'updated_at' => $now,
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ])
            );
        }
    }

}
