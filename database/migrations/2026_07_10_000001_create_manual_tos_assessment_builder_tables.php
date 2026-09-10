<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Legacy-MySQL-safe column check.
     *
     * This intentionally avoids Schema::hasColumn(), because older MySQL/MariaDB
     * servers do not expose information_schema.columns.generation_expression,
     * which newer Laravel versions attempt to query.
     */
    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    public function up(): void
    {
        // A previous failed run may already have added one or more of these.
        if (! $this->columnExists('table_of_specification_rows', 'subtopic_title')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table) {
                $table->string('subtopic_title', 191)->nullable()->after('topic_title');
            });
        }

        if (! $this->columnExists('table_of_specification_rows', 'learning_objective')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table) {
                $table->text('learning_objective')->nullable()->after('subtopic_title');
            });
        }

        if (! $this->columnExists('table_of_specification_rows', 'default_points')) {
            Schema::table('table_of_specification_rows', function (Blueprint $table) {
                $table->unsignedInteger('default_points')->default(1)->after('item_count');
            });
        }

        if (! $this->tableExists('assessments')) {
            Schema::create('assessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('table_of_specification_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->string('title', 191);
                $table->text('description')->nullable();
                $table->text('instructions')->nullable();
                $table->string('status', 30)->default('draft');
                $table->unsignedInteger('total_items')->default(0);
                $table->unsignedInteger('total_points')->default(0);
                $table->unsignedInteger('time_limit_minutes')->nullable();
                $table->unsignedInteger('max_attempts')->default(1);
                $table->timestamp('available_at')->nullable();
                $table->timestamp('due_at')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();

                $table->foreign('table_of_specification_id', 'assessments_tos_fk')
                    ->references('id')->on('table_of_specifications')->cascadeOnDelete();
                $table->foreign('class_id', 'assessments_class_fk')
                    ->references('id')->on('classes')->nullOnDelete();
                $table->foreign('created_by', 'assessments_creator_fk')
                    ->references('id')->on('users')->nullOnDelete();

                $table->index(['class_id', 'status'], 'assessment_class_status_idx');
                $table->index(['table_of_specification_id', 'status'], 'assessment_tos_status_idx');
            });
        }

        if (! $this->tableExists('assessment_questions')) {
            Schema::create('assessment_questions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('table_of_specification_row_id')->nullable();
                $table->unsignedBigInteger('ilo_id')->nullable();
                $table->unsignedInteger('item_number');
                $table->string('question_type', 40)->default('unconfigured');
                $table->longText('question_text')->nullable();
                $table->string('image_path', 191)->nullable();
                $table->unsignedInteger('points')->default(1);
                $table->boolean('is_required')->default(true);
                $table->boolean('authoring_touched')->default(false);
                $table->text('correct_answer')->nullable();
                $table->text('answer_explanation')->nullable();
                $table->longText('rubric_text')->nullable();
                $table->string('topic_title', 191);
                $table->string('subtopic_title', 191)->nullable();
                $table->text('learning_objective')->nullable();
                $table->string('bloom_level', 80)->nullable();
                $table->string('difficulty_slug', 40)->nullable();
                $table->timestamps();

                $table->foreign('assessment_id', 'assessment_questions_assessment_fk')
                    ->references('id')->on('assessments')->cascadeOnDelete();
                $table->foreign('table_of_specification_row_id', 'assessment_questions_tos_row_fk')
                    ->references('id')->on('table_of_specification_rows')->nullOnDelete();
                $table->foreign('ilo_id', 'assessment_questions_ilo_fk')
                    ->references('id')->on('intended_learning_outcomes')->nullOnDelete();

                $table->unique(['assessment_id', 'item_number'], 'assessment_item_number_uq');
                $table->index(['assessment_id', 'question_type'], 'assessment_question_type_idx');
                $table->index(['assessment_id', 'bloom_level'], 'assessment_bloom_idx');
                $table->index(['assessment_id', 'difficulty_slug'], 'assessment_difficulty_idx');
            });
        }

        if (! $this->tableExists('assessment_question_options')) {
            Schema::create('assessment_question_options', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_question_id');
                $table->string('option_label', 10)->nullable();
                $table->text('option_text');
                $table->boolean('is_correct')->default(false);
                $table->unsignedInteger('order_index')->default(0);
                $table->timestamps();

                $table->foreign('assessment_question_id', 'assessment_options_question_fk')
                    ->references('id')->on('assessment_questions')->cascadeOnDelete();
                $table->index(['assessment_question_id', 'order_index'], 'assessment_option_order_idx');
            });
        }

        if (! $this->tableExists('assessment_submissions')) {
            Schema::create('assessment_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedInteger('attempt_no')->default(1);
                $table->string('status', 30)->default('in_progress');
                $table->decimal('score', 8, 2)->default(0);
                $table->decimal('total_points', 8, 2)->default(0);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamp('graded_at')->nullable();
                $table->text('feedback')->nullable();
                $table->timestamps();

                $table->foreign('assessment_id', 'assessment_submissions_assessment_fk')
                    ->references('id')->on('assessments')->cascadeOnDelete();
                $table->foreign('student_id', 'assessment_submissions_student_fk')
                    ->references('id')->on('users')->cascadeOnDelete();

                $table->unique(['assessment_id', 'student_id', 'attempt_no'], 'assessment_student_attempt_uq');
                $table->index(['assessment_id', 'status'], 'assessment_submission_status_idx');
                $table->index(['student_id', 'status'], 'student_submission_status_idx');
            });
        }

        if (! $this->tableExists('assessment_answers')) {
            Schema::create('assessment_answers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('assessment_submission_id');
                $table->unsignedBigInteger('assessment_question_id');
                $table->unsignedBigInteger('selected_option_id')->nullable();
                $table->longText('answer_text')->nullable();
                $table->boolean('is_correct')->nullable();
                $table->decimal('points_awarded', 8, 2)->default(0);
                $table->text('instructor_feedback')->nullable();
                $table->timestamps();

                $table->foreign('assessment_submission_id', 'assessment_answers_submission_fk')
                    ->references('id')->on('assessment_submissions')->cascadeOnDelete();
                $table->foreign('assessment_question_id', 'assessment_answers_question_fk')
                    ->references('id')->on('assessment_questions')->cascadeOnDelete();
                $table->foreign('selected_option_id', 'assessment_answers_option_fk')
                    ->references('id')->on('assessment_question_options')->nullOnDelete();

                $table->unique(
                    ['assessment_submission_id', 'assessment_question_id'],
                    'assessment_submission_question_uq'
                );
            });
        }

        if (! $this->tableExists('student_assessment_diagnostics')) {
            Schema::create('student_assessment_diagnostics', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->unsignedBigInteger('assessment_id');
                $table->unsignedBigInteger('table_of_specification_row_id')->nullable();
                $table->unsignedBigInteger('ilo_id')->nullable();
                $table->string('topic_title', 191);
                $table->string('subtopic_title', 191)->nullable();
                $table->text('learning_objective')->nullable();
                $table->string('bloom_level', 80)->nullable();
                $table->string('difficulty_slug', 40)->nullable();
                $table->unsignedInteger('item_count')->default(0);
                $table->unsignedInteger('answered_count')->default(0);
                $table->unsignedInteger('correct_count')->default(0);
                $table->decimal('earned_points', 8, 2)->default(0);
                $table->decimal('possible_points', 8, 2)->default(0);
                $table->decimal('mastery_percent', 6, 2)->default(0);
                $table->string('proficiency_label', 40)->default('Not Assessed');
                $table->boolean('manual_review_pending')->default(false);
                $table->timestamp('calculated_at')->nullable();
                $table->timestamps();

                $table->foreign('student_id', 'sad_student_fk')
                    ->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('class_id', 'sad_class_fk')
                    ->references('id')->on('classes')->nullOnDelete();
                $table->foreign('assessment_id', 'sad_assessment_fk')
                    ->references('id')->on('assessments')->cascadeOnDelete();
                $table->foreign('table_of_specification_row_id', 'sad_tos_row_fk')
                    ->references('id')->on('table_of_specification_rows')->nullOnDelete();
                $table->foreign('ilo_id', 'sad_ilo_fk')
                    ->references('id')->on('intended_learning_outcomes')->nullOnDelete();

                $table->unique(
                    ['student_id', 'assessment_id', 'table_of_specification_row_id'],
                    'student_assessment_tos_diagnostic_uq'
                );
                $table->index(['class_id', 'proficiency_label'], 'diagnostic_class_proficiency_idx');
                $table->index(['assessment_id', 'bloom_level'], 'diagnostic_assessment_bloom_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('student_assessment_diagnostics');
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_submissions');
        Schema::dropIfExists('assessment_question_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');

        Schema::enableForeignKeyConstraints();

        foreach (['subtopic_title', 'learning_objective', 'default_points'] as $column) {
            if ($this->columnExists('table_of_specification_rows', $column)) {
                Schema::table('table_of_specification_rows', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
