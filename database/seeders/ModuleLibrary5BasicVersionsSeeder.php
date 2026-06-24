<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary5BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary5BasicV1Seeder::class,
            ModuleLibrary5BasicV2Seeder::class,
            ModuleLibrary5BasicV3Seeder::class,
        ]);
    }
}
