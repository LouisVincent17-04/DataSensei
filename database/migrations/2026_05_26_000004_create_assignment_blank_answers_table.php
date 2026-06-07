<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_blank_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_question_id')
                ->constrained('assignment_questions')
                ->cascadeOnDelete();
            $table->string('answer_text');
            $table->boolean('is_case_sensitive')->default(false);
            $table->timestamps();

            $table->unique(['assignment_question_id', 'answer_text'], 'assignment_blank_answer_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_blank_answers');
    }
};
