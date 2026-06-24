<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary7BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary7BasicV1Seeder::class,
            ModuleLibrary7BasicV2Seeder::class,
            ModuleLibrary7BasicV3Seeder::class,
        ]);
    }
}
