<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->unsignedBigInteger('rank_id')->primary();
            $table->string('rank_name', 80)->unique();
            $table->unsignedInteger('exp_required')->unique();
            $table->timestamps();
        });

        DB::table('ranks')->insert([
            [
                'rank_id' => 1,
                'rank_name' => 'Apprentice',
                'exp_required' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 2,
                'rank_name' => 'Practitioner',
                'exp_required' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 3,
                'rank_name' => 'Specialist',
                'exp_required' => 1500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 4,
                'rank_name' => 'Strategist',
                'exp_required' => 3500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 5,
                'rank_name' => 'Expert',
                'exp_required' => 7000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 6,
                'rank_name' => 'Master',
                'exp_required' => 12000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 7,
                'rank_name' => 'Authority',
                'exp_required' => 20000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'rank_id' => 8,
                'rank_name' => 'Luminary',
                'exp_required' => 32000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
