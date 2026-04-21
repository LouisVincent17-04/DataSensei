<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {

        // The main challenge
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->integer('time_limit_seconds')->default(600); // e.g., 10 mins
            $table->integer('base_xp')->default(100);
            $table->integer('order_index'); // For ordering challenges in the map
            $table->tinyInteger('is_coding_challenge')->default(0); // 0 = quiz, 1 = coding challenge
            $table->timestamps();
        });

        // The questions [ModuleNChallengeSeederNewbie.php will populate this with multiple choice questions for now]
        Schema::create('challenge_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_category_id')
                ->constrained('challenge_categories')
                ->cascadeOnDelete();
            $table->text('question_text');
            $table->timestamps();
        });

        // The multiple choice options [ModuleNChallengeSeederNewbie.php will populate this with multiple choice options for now]
        Schema::create('challenge_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challenge_question_id')->constrained()->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // Tracking user completion, score, and time
        Schema::create('challenge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challenge_id')->constrained()->cascadeOnDelete();
            $table->integer('score');
            $table->integer('time_taken_seconds');
            $table->integer('xp_awarded');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('challenge_options');
        Schema::dropIfExists('challenge_questions');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('challenge_user');

        Schema::enableForeignKeyConstraints();
    }
};