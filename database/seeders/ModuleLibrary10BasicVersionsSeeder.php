<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary10BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary10BasicV1Seeder::class,
            ModuleLibrary10BasicV2Seeder::class,
            ModuleLibrary10BasicV3Seeder::class,
        ]);
    }
}
