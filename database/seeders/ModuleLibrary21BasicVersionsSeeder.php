<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary21BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary21BasicV1Seeder::class,
            ModuleLibrary21BasicV2Seeder::class,
            ModuleLibrary21BasicV3Seeder::class,
        ]);
    }
}
