<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibraryPythonBasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary1BasicV1Seeder::class,
            ModuleLibrary1BasicV2Seeder::class,
            ModuleLibrary1BasicV3Seeder::class,
        ]);
    }
}
