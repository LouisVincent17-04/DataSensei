<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 11: each instructor's private Question Bank. Questions
 * are organized by module/topic, type, difficulty and ILO, and are COPIED
 * into an assessment as a snapshot, so editing or archiving a bank question
 * later never changes a published assessment, an attempt, or a grade.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('question_bank_items')) {
            Schema::create('question_bank_items', function (Blueprint $table): void {
                $table->id();
                // NULL = the shared pool every instructor can copy from
                // (seeded from the old admin assignment library).
                $table->unsignedBigInteger('instructor_id')->nullable()->index();
                $table->unsignedInteger('module_no')->nullable();
                $table->string('topic_title', 191)->nullable();
                $table->string('question_type', 40);
                $table->longText('question_text');
                $table->text('correct_answer')->nullable();
                $table->text('answer_explanation')->nullable();
                $table->longText('rubric_text')->nullable();
                $table->unsignedInteger('points')->default(1);
                $table->string('difficulty_slug', 40)->nullable();
                $table->unsignedBigInteger('ilo_id')->nullable()->index();
                $table->string('bloom_level', 80)->nullable();
                $table->boolean('is_archived')->default(false);
                $table->timestamps();
                $table->index(['instructor_id', 'is_archived']);

                $table->foreign('instructor_id')->references('id')->on('users')->cascadeOnDelete();
                // With a NULL instructor the row survives any user deletion.
                $table->foreign('ilo_id')->references('id')->on('intended_learning_outcomes')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('question_bank_options')) {
            Schema::create('question_bank_options', function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('question_bank_item_id')->index();
                $table->text('option_text');
                $table->boolean('is_correct')->default(false);
                $table->unsignedInteger('order_index')->default(0);
                $table->timestamps();

                $table->foreign('question_bank_item_id')->references('id')->on('question_bank_items')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('question_bank_options');
        Schema::dropIfExists('question_bank_items');
    }
};
