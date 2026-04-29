<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coding_question_id')->constrained('coding_questions')->cascadeOnDelete();
            $table->text('input')->nullable();          // stdin passed to the script
            $table->text('expected_output');            // exact expected stdout (trimmed)
            $table->boolean('is_hidden')->default(false); // hidden cases shown only after submit
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_cases');
    }
};