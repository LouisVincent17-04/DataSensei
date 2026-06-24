<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary22BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary22BasicV1Seeder::class,
            ModuleLibrary22BasicV2Seeder::class,
            ModuleLibrary22BasicV3Seeder::class,
        ]);
    }
}
