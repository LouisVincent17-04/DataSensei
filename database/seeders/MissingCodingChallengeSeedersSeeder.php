<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Runs only relevant coding challenge seeders that were missing, empty/stub,
 * or had a class-name mismatch in the uploaded seeders.zip.
 *
 * Skips non-coding topic modules:
 * - Module 4: Mathematical Analysis I
 * - Module 5: Methods of Proof
 * - Module 10: Database Management for Data Science (the generated coding
 *   bank is generic Python, not SQLite/SQL database practice)
 * - Module 18: Privacy, Ethics & Data Governance
 * - Module 22: Big Data & Cloud Computing
 * - Module 24: Sequential Decision Making
 */
class MissingCodingChallengeSeedersSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Module2CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module2CodingChallengeSeederIntermediate::class, // empty/stub
            Module6CodingChallengeSeederAdvanced::class, // class mismatch: Module6CodingChallengeSeederIntermediate
            Module7CodingChallengeSeederProfessional::class, // empty/stub
            Module9CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module9CodingChallengeSeederIntermediate::class, // empty/stub
            Module11CodingChallengeSeederIntermediate::class, // empty/stub
            Module11CodingChallengeSeederAdvanced::class, // empty/stub
            Module11CodingChallengeSeederProfessional::class, // empty/stub
            Module12CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module12CodingChallengeSeederIntermediate::class, // empty/stub
            Module12CodingChallengeSeederAdvanced::class, // empty/stub
            Module12CodingChallengeSeederProfessional::class, // empty/stub
            Module13CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module13CodingChallengeSeederIntermediate::class, // empty/stub
            Module13CodingChallengeSeederAdvanced::class, // empty/stub
            Module13CodingChallengeSeederProfessional::class, // empty/stub
            Module14CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module14CodingChallengeSeederIntermediate::class, // empty/stub
            Module14CodingChallengeSeederAdvanced::class, // empty/stub
            Module14CodingChallengeSeederProfessional::class, // empty/stub
            Module15CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module15CodingChallengeSeederIntermediate::class, // empty/stub
            Module15CodingChallengeSeederAdvanced::class, // empty/stub
            Module15CodingChallengeSeederProfessional::class, // empty/stub
            Module16CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module16CodingChallengeSeederIntermediate::class, // empty/stub
            Module16CodingChallengeSeederAdvanced::class, // empty/stub
            Module16CodingChallengeSeederProfessional::class, // empty/stub
            Module17CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module17CodingChallengeSeederIntermediate::class, // empty/stub
            Module17CodingChallengeSeederAdvanced::class, // empty/stub
            Module17CodingChallengeSeederProfessional::class, // empty/stub
            Module19CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module19CodingChallengeSeederIntermediate::class, // empty/stub
            Module19CodingChallengeSeederAdvanced::class, // empty/stub
            Module19CodingChallengeSeederProfessional::class, // empty/stub
            Module20CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module20CodingChallengeSeederIntermediate::class, // empty/stub
            Module20CodingChallengeSeederAdvanced::class, // empty/stub
            Module20CodingChallengeSeederProfessional::class, // empty/stub
            Module21CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module21CodingChallengeSeederIntermediate::class, // empty/stub
            Module21CodingChallengeSeederAdvanced::class, // empty/stub
            Module21CodingChallengeSeederProfessional::class, // empty/stub
            Module23CodingChallengeSeederUniversityStudent::class, // empty/stub
            Module23CodingChallengeSeederIntermediate::class, // empty/stub
            Module23CodingChallengeSeederAdvanced::class, // empty/stub
            Module23CodingChallengeSeederProfessional::class, // empty/stub
        ]);
    }
}
