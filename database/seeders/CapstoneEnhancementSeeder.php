<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CapstoneEnhancementSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            IntendedLearningOutcomeSeeder::class,
            GamificationSeeder::class,
        ]);
    }
}
