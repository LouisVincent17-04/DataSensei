<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DS-13 / DS-15.
 *
 * Additive only. Existing rows are never rewritten or removed:
 *   - legacy attempts keep attempt_token = NULL until their next submit/start,
 *     when the application stamps one;
 *   - legacy submissions keep attempt_token = NULL (unknown attempt).
 *
 * MySQL 5.5 compatible: no JSON type, no generated columns, indexed string
 * columns are 64 chars (< 191), no fractional datetimes, no ->after().
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->tableExists('coding_question_attempts')) {
            if (!$this->columnExists('coding_question_attempts', 'attempt_token')) {
                Schema::table('coding_question_attempts', function (Blueprint $table) {
                    $table->string('attempt_token', 64)->nullable();
                });
            }
            if (!$this->columnExists('coding_question_attempts', 'generation')) {
                Schema::table('coding_question_attempts', function (Blueprint $table) {
                    $table->unsignedInteger('generation')->default(1);
                });
            }
        }

        if ($this->tableExists('coding_submissions')) {
            if (!$this->columnExists('coding_submissions', 'attempt_token')) {
                Schema::table('coding_submissions', function (Blueprint $table) {
                    $table->string('attempt_token', 64)->nullable();
                    $table->index('attempt_token', 'coding_submissions_attempt_token_idx');
                });
            }
            if (!$this->columnExists('coding_submissions', 'attempt_generation')) {
                Schema::table('coding_submissions', function (Blueprint $table) {
                    $table->unsignedInteger('attempt_generation')->nullable();
                });
            }
            if (!$this->columnExists('coding_submissions', 'void_reason')) {
                Schema::table('coding_submissions', function (Blueprint $table) {
                    $table->string('void_reason', 40)->nullable();
                });
            }
            if (!$this->columnExists('coding_submissions', 'grader_diagnostics')) {
                Schema::table('coding_submissions', function (Blueprint $table) {
                    // Restricted: hidden-test diagnostics. Never serialized to students.
                    $table->longText('grader_diagnostics')->nullable();
                });
            }
        }

        if (!$this->tableExists('coding_question_attempt_archives')) {
            Schema::create('coding_question_attempt_archives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('coding_question_id');
                $table->string('attempt_token', 64)->nullable();
                $table->unsignedInteger('generation')->default(1);
                $table->dateTime('started_at')->nullable();
                $table->boolean('expired')->default(false);
                $table->dateTime('retaken_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'coding_question_id'], 'coding_attempt_archives_user_question_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('coding_question_attempt_archives');

        if ($this->tableExists('coding_submissions')) {
            if ($this->columnExists('coding_submissions', 'attempt_token')) {
                Schema::table('coding_submissions', function (Blueprint $table) {
                    $table->dropIndex('coding_submissions_attempt_token_idx');
                });
                Schema::table('coding_submissions', function (Blueprint $table) {
                    $table->dropColumn('attempt_token');
                });
            }
            foreach (['attempt_generation', 'void_reason', 'grader_diagnostics'] as $column) {
                if ($this->columnExists('coding_submissions', $column)) {
                    Schema::table('coding_submissions', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }

        if ($this->tableExists('coding_question_attempts')) {
            foreach (['attempt_token', 'generation'] as $column) {
                if ($this->columnExists('coding_question_attempts', $column)) {
                    Schema::table('coding_question_attempts', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }

    /**
     * Schema::hasColumn() and Schema::hasTable() read information_schema
     * columns (generation_expression) that MySQL 5.5 does not have, so they
     * fail there with "Unknown column". These plain queries work on every
     * MySQL/MariaDB version, like the helpers of the earlier migrations.
     */
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
};
