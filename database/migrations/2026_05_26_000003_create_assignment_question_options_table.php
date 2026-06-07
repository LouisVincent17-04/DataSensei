<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_question_id')
                ->constrained('assignment_questions')
                ->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();

            $table->index(['assignment_question_id', 'order_index'], 'assignment_options_question_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_question_options');
    }
};
