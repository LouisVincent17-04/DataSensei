<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary2BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary2BasicV1Seeder::class,
            ModuleLibrary2BasicV2Seeder::class,
            ModuleLibrary2BasicV3Seeder::class,
        ]);
    }
}
