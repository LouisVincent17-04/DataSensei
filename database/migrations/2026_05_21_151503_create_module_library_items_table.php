<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_library_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('module_no');
            $table->string('module_code')->unique();

            $table->string('title');
            $table->string('year_level');

            $table->unsignedInteger('version_no')->default(1);
            $table->string('version_name')->default('Version 1');
            $table->string('version_code');

            $table->text('description')->nullable();
            $table->unsignedInteger('estimated_minutes')->default(45);

            /*
                Use longText instead of json because your MySQL version
                does not support JSON columns.
            */
            $table->longText('content_sections')->nullable();
            $table->longText('mcq_questions')->nullable();

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['module_no', 'version_code'], 'module_library_module_version_unique');
            $table->index(['year_level', 'module_no'], 'module_library_year_module_index');
            $table->index(['is_active', 'sort_order'], 'module_library_active_sort_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_library_items');
    }
};
