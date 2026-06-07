<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();
            $table->foreignId('assignment_library_item_id')
                ->constrained('assignment_library_items')
                ->cascadeOnDelete();
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');
            $table->text('instructions')->nullable();
            $table->timestamp('available_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->unsignedInteger('max_attempts')->default(1);
            $table->enum('status', ['draft', 'published', 'closed', 'archived'])->default('draft');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'status'], 'class_assignments_class_status_index');
            $table->index(['assignment_library_item_id'], 'class_assignments_library_item_index');
            $table->index(['due_at'], 'class_assignments_due_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_assignments');
    }
};
