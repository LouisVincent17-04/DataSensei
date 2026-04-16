<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema  ::disableForeignKeyConstraints();
        DB::table('challenge_options')->truncate();
        DB::table('challenge_questions')->truncate();
        Schema  ::enableForeignKeyConstraints();

        $this->call(Module1ChallengeSeederNewbie::class);
        $this->call(Module1ChallengeSeederProfessional::class);
        $this->call(Module2ChallengeSeederNewbie::class);
    }
}
