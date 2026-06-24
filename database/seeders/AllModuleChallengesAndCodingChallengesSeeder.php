<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllModuleChallengesAndCodingChallengesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Cleaning existing challenge and coding challenge data...');
        $this->cleanChallengeTables();

        $this->call([
            ChallengeCategorySeeder::class,
            ModuleChallengesSeeders::class,
            ModuleCodingChallengesSeeders::class,
        ]);

        $this->command->info('All module MCQ and coding challenge seeders completed.');
    }

    private function cleanChallengeTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'coding_question_attempts',
            'coding_challenge_retakes',
            'coding_submissions',
            'challenge_user',
            'test_cases',
            'coding_questions',
            'challenge_options',
            'challenge_questions',
            'challenges',
        ];

        foreach ($tables as $table) {
            try {
                DB::statement('TRUNCATE TABLE `' . $table . '`');
            } catch (\Throwable $e) {
                // Some older/local databases may not have every optional table yet.
                // Continue cleaning the tables that do exist.
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
