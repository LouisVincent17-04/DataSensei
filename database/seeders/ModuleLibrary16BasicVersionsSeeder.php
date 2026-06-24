<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary16BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary16BasicV1Seeder::class,
            ModuleLibrary16BasicV2Seeder::class,
            ModuleLibrary16BasicV3Seeder::class,
        ]);
    }
}
