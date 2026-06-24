<?php

namespace Database\Seeders;

use App\Models\Rank;
use Illuminate\Database\Seeder;

class RanksSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            [1, 'Apprentice', 0],
            [2, 'Practitioner', 500],
            [3, 'Specialist', 1500],
            [4, 'Strategist', 3500],
            [5, 'Expert', 7000],
            [6, 'Master', 12000],
            [7, 'Authority', 20000],
            [8, 'Luminary', 32000],
        ];

        foreach ($ranks as [$rankId, $rankName, $expRequired]) {
            Rank::updateOrCreate(
                ['rank_id' => $rankId],
                ['rank_name' => $rankName, 'exp_required' => $expRequired]
            );
        }
    }
}
