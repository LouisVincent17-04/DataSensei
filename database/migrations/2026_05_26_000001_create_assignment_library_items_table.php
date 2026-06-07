<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_library_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_no');
            $table->string('assignment_code')->unique();
            $table->string('title');
            $table->string('topic_title');
            $table->string('year_level');
            $table->enum('assignment_type', ['mcq', 'fill_blank', 'mixed'])->default('mcq');
            $table->unsignedInteger('version_no')->default(1);
            $table->string('version_name')->default('Version 1');
            $table->string('version_code');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('time_limit_minutes')->default(20);
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['module_no', 'version_code'], 'assignment_library_module_version_unique');
            $table->index(['year_level', 'module_no'], 'assignment_library_year_module_index');
            $table->index(['assignment_type', 'is_active'], 'assignment_library_type_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_library_items');
    }
};
