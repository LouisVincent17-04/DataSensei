<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary8BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary8BasicV1Seeder::class,
            ModuleLibrary8BasicV2Seeder::class,
            ModuleLibrary8BasicV3Seeder::class,
        ]);
    }
}
