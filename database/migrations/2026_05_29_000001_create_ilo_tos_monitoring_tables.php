<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intended_learning_outcomes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('module_no');
            $table->string('ilo_code', 80)->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('mastery_threshold')->default(75);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['module_no', 'is_active']);
        });

        Schema::create('assessment_question_ilos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilo_id')->constrained('intended_learning_outcomes')->cascadeOnDelete();
            $table->string('assessment_source', 40); // assignment, mcq_challenge, coding_challenge
            $table->unsignedBigInteger('question_id');
            $table->unsignedTinyInteger('weight')->default(1);
            $table->timestamps();

            $table->unique(['ilo_id', 'assessment_source', 'question_id'], 'question_ilo_unique');
            $table->index(['assessment_source', 'question_id'], 'question_ilo_source_index');
        });

        Schema::create('student_ilo_masteries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->foreignId('ilo_id')->constrained('intended_learning_outcomes')->cascadeOnDelete();
            $table->decimal('mastery_percent', 6, 2)->default(0);
            $table->unsignedInteger('evidence_count')->default(0);
            $table->string('status', 30)->default('not_started'); // not_started, developing, mastered
            $table->timestamp('last_evaluated_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'class_id', 'ilo_id'], 'student_class_ilo_unique');
            $table->index(['class_id', 'status']);
        });

        Schema::create('table_of_specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedInteger('module_no');
            $table->string('title');
            $table->string('status', 30)->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'module_no']);
            $table->index('created_by');
        });

        Schema::create('table_of_specification_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_of_specification_id')->constrained('table_of_specifications')->cascadeOnDelete();
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->string('topic_title');
            $table->string('difficulty_slug', 40); // newbie, university-student, intermediate, advanced, professional
            $table->unsignedInteger('item_count')->default(0);
            $table->string('cognitive_level', 80)->nullable();
            $table->timestamps();

            $table->index(['table_of_specification_id', 'difficulty_slug'], 'tos_row_difficulty_index');
            $table->index('ilo_id');
        });

        Schema::create('student_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->decimal('average_score_percent', 6, 2)->default(0);
            $table->decimal('average_time_ratio', 6, 2)->nullable();
            $table->unsignedInteger('completed_activities')->default(0);
            $table->unsignedInteger('missing_assignments')->default(0);
            $table->unsignedInteger('late_submissions')->default(0);
            $table->unsignedInteger('anti_cheat_warnings')->default(0);
            $table->decimal('engagement_score', 6, 2)->default(0);
            $table->string('cluster_label', 80)->default('Not Enough Data');
            $table->string('risk_level', 30)->default('unknown');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'risk_level']);
            $table->index(['student_id', 'class_id']);
        });

        Schema::create('student_performance_clusters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('cluster_label', 80);
            $table->text('cluster_description')->nullable();
            $table->decimal('average_score_percent', 6, 2)->default(0);
            $table->decimal('engagement_score', 6, 2)->default(0);
            $table->string('risk_level', 30)->default('unknown');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->index(['class_id', 'cluster_label']);
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_performance_clusters');
        Schema::dropIfExists('student_performance_snapshots');
        Schema::dropIfExists('table_of_specification_rows');
        Schema::dropIfExists('table_of_specifications');
        Schema::dropIfExists('student_ilo_masteries');
        Schema::dropIfExists('assessment_question_ilos');
        Schema::dropIfExists('intended_learning_outcomes');
    }
};
