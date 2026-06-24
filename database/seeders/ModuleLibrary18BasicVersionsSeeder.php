<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary18BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary18BasicV1Seeder::class,
            ModuleLibrary18BasicV2Seeder::class,
            ModuleLibrary18BasicV3Seeder::class,
        ]);
    }
}
