<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary14BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary14BasicV1Seeder::class,
            ModuleLibrary14BasicV2Seeder::class,
            ModuleLibrary14BasicV3Seeder::class,
        ]);
    }
}
