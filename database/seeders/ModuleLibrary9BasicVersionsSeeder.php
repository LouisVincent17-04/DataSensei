<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary9BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary9BasicV1Seeder::class,
            ModuleLibrary9BasicV2Seeder::class,
            ModuleLibrary9BasicV3Seeder::class,
        ]);
    }
}
