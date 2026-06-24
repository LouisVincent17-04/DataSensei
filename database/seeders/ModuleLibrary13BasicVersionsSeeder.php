<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary13BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary13BasicV1Seeder::class,
            ModuleLibrary13BasicV2Seeder::class,
            ModuleLibrary13BasicV3Seeder::class,
        ]);
    }
}
