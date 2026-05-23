<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_module_assignments', function (Blueprint $table) {
            $table->id();

            /*
                If your class table is not named "classes",
                change constrained('classes') to your actual table name.
            */
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('module_library_item_id')
                ->constrained('module_library_items')
                ->cascadeOnDelete();

            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('status', ['active', 'archived'])->default('active');
            $table->timestamp('assigned_at')->nullable();

            $table->timestamps();

            $table->unique(['class_id', 'module_library_item_id'], 'class_module_assignment_unique');
            $table->index(['class_id', 'status'], 'class_module_assignment_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_module_assignments');
    }
};
