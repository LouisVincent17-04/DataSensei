<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary12BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary12BasicV1Seeder::class,
            ModuleLibrary12BasicV2Seeder::class,
            ModuleLibrary12BasicV3Seeder::class,
        ]);
    }
}
