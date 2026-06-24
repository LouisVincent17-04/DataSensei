<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleLibrary15BasicVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ModuleLibrary15BasicV1Seeder::class,
            ModuleLibrary15BasicV2Seeder::class,
            ModuleLibrary15BasicV3Seeder::class,
        ]);
    }
}
