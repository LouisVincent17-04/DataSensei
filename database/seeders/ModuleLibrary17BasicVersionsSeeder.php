<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary17BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary17BasicV1Seeder::class,
            ModuleLibrary17BasicV2Seeder::class,
            ModuleLibrary17BasicV3Seeder::class,
        ]);
    }
}
