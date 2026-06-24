<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary19BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary19BasicV1Seeder::class,
            ModuleLibrary19BasicV2Seeder::class,
            ModuleLibrary19BasicV3Seeder::class,
        ]);
    }
}
