<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submission_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_submission_id')
                ->constrained('assignment_submissions')
                ->cascadeOnDelete();
            $table->foreignId('assignment_question_id')
                ->constrained('assignment_questions')
                ->cascadeOnDelete();
            $table->foreignId('selected_option_id')
                ->nullable()
                ->constrained('assignment_question_options')
                ->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamps();

            $table->unique(['assignment_submission_id', 'assignment_question_id'], 'assignment_submission_answer_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_answers');
    }
};
