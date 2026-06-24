<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary4BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary4BasicV1Seeder::class,
            ModuleLibrary4BasicV2Seeder::class,
            ModuleLibrary4BasicV3Seeder::class,
        ]);
    }
}
