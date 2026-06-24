<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary24BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary24BasicV1Seeder::class,
            ModuleLibrary24BasicV2Seeder::class,
            ModuleLibrary24BasicV3Seeder::class,
        ]);
    }
}
