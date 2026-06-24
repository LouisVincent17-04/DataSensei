<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary20BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary20BasicV1Seeder::class,
            ModuleLibrary20BasicV2Seeder::class,
            ModuleLibrary20BasicV3Seeder::class,
        ]);
    }
}
