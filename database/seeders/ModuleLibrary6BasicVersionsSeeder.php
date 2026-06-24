<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary6BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary6BasicV1Seeder::class,
            ModuleLibrary6BasicV2Seeder::class,
            ModuleLibrary6BasicV3Seeder::class,
        ]);
    }
}
