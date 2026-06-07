<?php

namespace Database\Seeders;

use App\Models\ChallengeOption;
use App\Models\ChallengeQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Schema::disableForeignKeyConstraints();
        // ChallengeQuestion::truncate();
        // ChallengeOption::truncate();
        

         $this->call([
            AssignmentLibrarySeeder::class,
            // Module1LessonsSeeder::class,
            // Module2LessonsSeeder::class,
            // Module3LessonsSeeder::class,
            // Module4LessonsSeeder::class,
            // Module5LessonsSeeder::class,
            // Module6LessonsSeeder::class,
            // Module7LessonsSeeder::class,
            // Module8LessonsSeeder::class,    
            // Module9LessonsSeeder::class,
            // Module10LessonsSeeder::class,
            // Module11LessonsSeeder::class,
            // Module12LessonsSeeder::class,
            // Module13LessonsSeeder::class,
            // Module14LessonsSeeder::class,
            // Module15LessonsSeeder::class,
            // Module16LessonsSeeder::class,
            // Module17LessonsSeeder::class,
            // Module18LessonsSeeder::class,
            // Module19LessonsSeeder::class,
            // Module20LessonsSeeder::class,
            // Module21LessonsSeeder::class,
            // Module22LessonsSeeder::class,
            // Module23LessonsSeeder::class,
                // Module24LessonsSeeder::class,
            // ChallengeTitlesSeeder::class,
            // CodingChallengeTitlesSeeder::class,
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
        ]);
        // Schema::enableForeignKeyConstraints();
    }

    
}
