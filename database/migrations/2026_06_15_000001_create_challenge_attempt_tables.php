<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('challenge_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('attempt_no')->default(1);
            $table->enum('mode', ['ranked', 'practice'])->default('ranked');
            $table->enum('status', ['in_progress', 'submitted', 'expired', 'voided', 'disqualified'])->default('in_progress');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('last_seen_at')->nullable();
            $table->unsignedInteger('time_limit_seconds')->default(600);
            $table->unsignedInteger('time_taken_seconds')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_questions')->default(0);
            $table->unsignedInteger('xp_awarded')->default(0);
            $table->boolean('is_ranked')->default(true);
            $table->boolean('is_leaderboard_eligible')->default(true);
            $table->unsignedInteger('suspicious_event_count')->default(0);
            $table->longText('question_order')->nullable();
            $table->longText('option_order')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'challenge_id', 'attempt_no'], 'challenge_attempt_user_challenge_no_unique');
            $table->index(['user_id', 'challenge_id', 'status'], 'challenge_attempt_user_challenge_status_idx');
            $table->index(['challenge_id', 'is_ranked', 'is_leaderboard_eligible'], 'challenge_attempt_leaderboard_idx');
        });

        Schema::create('challenge_attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_attempt_id')->constrained('challenge_attempts')->cascadeOnDelete();
            $table->foreignId('challenge_question_id')->constrained('challenge_questions')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('challenge_options')->nullOnDelete();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['challenge_attempt_id', 'challenge_question_id'], 'challenge_attempt_answer_unique');
            $table->index(['challenge_question_id', 'selected_option_id'], 'challenge_attempt_answer_question_option_idx');
        });

        Schema::create('challenge_attempt_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_attempt_id')->constrained('challenge_attempts')->cascadeOnDelete();
            $table->string('event_type', 80);
            $table->enum('severity', ['low', 'medium', 'high'])->default('low');
            $table->longText('details')->nullable();
            $table->dateTime('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['challenge_attempt_id', 'event_type'], 'challenge_attempt_event_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('challenge_attempt_events');
        Schema::dropIfExists('challenge_attempt_answers');
        Schema::dropIfExists('challenge_attempts');
    }
};
