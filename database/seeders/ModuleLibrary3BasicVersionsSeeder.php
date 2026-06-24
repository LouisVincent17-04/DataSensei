<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary3BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary3BasicV1Seeder::class,
            ModuleLibrary3BasicV2Seeder::class,
            ModuleLibrary3BasicV3Seeder::class,
        ]);
    }
}
