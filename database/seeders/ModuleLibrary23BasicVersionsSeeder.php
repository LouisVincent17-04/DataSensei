<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary23BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary23BasicV1Seeder::class,
            ModuleLibrary23BasicV2Seeder::class,
            ModuleLibrary23BasicV3Seeder::class,
        ]);
    }
}
