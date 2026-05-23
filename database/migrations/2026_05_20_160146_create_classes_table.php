<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('name');
            $table->string('section')->nullable();
            $table->string('term')->nullable();          // e.g. "1st Semester 2025-2026"
            $table->string('academic_year')->nullable(); // e.g. "2025-2026"
            $table->text('description')->nullable();
            $table->string('class_code', 8)->unique();  // Unique join code for students
            $table->string('subject_code')->nullable(); // e.g. "IT 301"
            $table->integer('max_students')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('allow_self_enroll')->default(true);
            $table->timestamps();
        });

        // Pivot table: students enrolled in classes
        Schema::create('class_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();

            $table->unique(['class_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_student');
        Schema::dropIfExists('classes');
    }
};