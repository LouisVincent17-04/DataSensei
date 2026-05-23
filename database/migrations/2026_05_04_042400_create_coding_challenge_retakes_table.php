<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coding_challenge_retakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();

            // Counts how many times this user has retaken this challenge.
            // Starts at 0 (first ever attempt). After clicking Retake once → 1, etc.
            // Hard cap enforced in PHP: retake_count < 3.
            $table->unsignedTinyInteger('retake_count')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'challenge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_challenge_retakes');
    }
};