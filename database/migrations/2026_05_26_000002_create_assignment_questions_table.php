<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_library_item_id')
                ->constrained('assignment_library_items')
                ->cascadeOnDelete();
            $table->enum('question_type', ['mcq', 'fill_blank']);
            $table->longText('question_text');
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('order_index')->default(0);
            $table->text('explanation')->nullable();
            $table->timestamps();

            $table->index(['assignment_library_item_id', 'order_index'], 'assignment_questions_item_order_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_questions');
    }
};
