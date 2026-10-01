<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 11: assessments take over homework, quizzes and
 * examinations from the removed Assignment feature.
 *
 *  - assessments gain an optional purpose label, an optional passing score,
 *    an optional topic, and a legacy id that remembers which class
 *    assignment a converted assessment came from (rerunning the conversion
 *    can then never duplicate it).
 *  - assessment_submissions gain the anti-cheat and integrity columns that
 *    assignment submissions had, plus their own legacy id.
 *  - the anti-cheat tables accept 'assessment' rows: the enum columns become
 *    plain varchars and the events table can point at assessment records.
 *
 * Everything here is additive and idempotent (safe to run twice), and uses
 * nothing newer than MySQL 5.5 understands.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table): void {
            if (! $this->columnExists('assessments', 'purpose')) {
                // Homework, quiz or examination. Purely a label: every purpose
                // uses the same assessment feature.
                $table->string('purpose', 20)->nullable()->after('status');
            }
            if (! $this->columnExists('assessments', 'passing_score_percent')) {
                $table->unsignedTinyInteger('passing_score_percent')->nullable()->after('total_points');
            }
            if (! $this->columnExists('assessments', 'topic_title')) {
                $table->string('topic_title', 191)->nullable()->after('instructions');
            }
            if (! $this->columnExists('assessments', 'legacy_class_assignment_id')) {
                $table->unsignedBigInteger('legacy_class_assignment_id')->nullable()->unique();
            }
        });

        Schema::table('assessment_submissions', function (Blueprint $table): void {
            if (! $this->columnExists('assessment_submissions', 'anti_cheat_session_id')) {
                $table->string('anti_cheat_session_id', 120)->nullable()->unique();
            }
            if (! $this->columnExists('assessment_submissions', 'integrity_status')) {
                $table->string('integrity_status', 30)->nullable();
                $table->string('integrity_reason', 255)->nullable();
                $table->decimal('provisional_score', 8, 2)->nullable();
                $table->unsignedBigInteger('integrity_reviewed_by')->nullable();
                $table->dateTime('integrity_reviewed_at')->nullable();
            }
            if (! $this->columnExists('assessment_submissions', 'legacy_assignment_submission_id')) {
                $table->unsignedBigInteger('legacy_assignment_submission_id')->nullable()->unique();
            }
        });

        // The anti-cheat tables were created with enum('assignment'). Widen
        // the type column so 'assessment' rows fit as well.
        if (DB::connection()->getDriverName() === 'mysql') {
            foreach (['anti_cheat_events', 'anti_cheat_settings'] as $tableName) {
                if ($this->mysqlColumnIsEnum($tableName, 'assessment_type')) {
                    DB::statement("ALTER TABLE `{$tableName}` MODIFY `assessment_type` VARCHAR(30) NOT NULL DEFAULT 'assignment'");
                }
            }
        } else {
            foreach (['anti_cheat_events', 'anti_cheat_settings'] as $tableName) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->string('assessment_type', 30)->default('assignment')->change();
                });
            }
        }

        Schema::table('anti_cheat_events', function (Blueprint $table): void {
            if (! $this->columnExists('anti_cheat_events', 'assessment_id')) {
                $table->unsignedBigInteger('assessment_id')->nullable()->index();
                $table->unsignedBigInteger('assessment_submission_id')->nullable()->index();
                $table->unsignedBigInteger('assessment_question_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Converted data depends on these columns; nothing is changed back.
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            return DB::selectOne(
                'SELECT COUNT(*) AS n FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                [$table, $column]
            )->n > 0;
        }

        return Schema::hasColumn($table, $column);
    }

    private function mysqlColumnIsEnum(string $table, string $column): bool
    {
        $row = DB::selectOne(
            'SELECT DATA_TYPE AS t FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return $row !== null && strtolower((string) $row->t) === 'enum';
    }
};
