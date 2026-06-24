<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary11BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary11BasicV1Seeder::class,
            ModuleLibrary11BasicV2Seeder::class,
            ModuleLibrary11BasicV3Seeder::class,
        ]);
    }
}
