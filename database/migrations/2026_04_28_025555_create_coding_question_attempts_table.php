<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * coding_question_attempts
 * ─────────────────────────────────────────────────────────────────────────────
 * Records the FIRST time a user opened each coding question.
 * The server uses started_at + time_limit_seconds to compute the true
 * remaining time on every page load — making localStorage/cookie clearing
 * completely ineffective as a cheat.
 *
 * One row per (user_id, coding_question_id). Never updated after insert.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coding_question_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('coding_question_id')
                  ->constrained('coding_questions')
                  ->cascadeOnDelete();
            $table->timestamp('started_at');          // wall-clock when first opened
            $table->boolean('expired')->default(false); // flipped true when time runs out
            $table->timestamps();

            // One attempt record per user per question
            $table->unique(['user_id', 'coding_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_question_attempts');
    }
};