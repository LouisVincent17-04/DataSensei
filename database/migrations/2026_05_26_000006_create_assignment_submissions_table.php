<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_assignment_id')
                ->constrained('class_assignments')
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->unsignedInteger('attempt_no')->default(1);
            $table->enum('status', ['in_progress', 'submitted', 'late', 'graded'])->default('in_progress');
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['class_assignment_id', 'student_id', 'attempt_no'], 'assignment_submission_attempt_unique');
            $table->index(['student_id', 'status'], 'assignment_submissions_student_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
