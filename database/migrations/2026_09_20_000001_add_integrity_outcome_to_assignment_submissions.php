<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Stores the anti-cheat decision of an assignment attempt separately from the
 * learner's saved work (DS-02) and a one-time reward marker (DS-01).
 *
 * Additive only: plain nullable columns, no JSON/generated columns, no index,
 * no foreign key and no dependence on column order, so it runs on MySQL 5.5
 * and on a populated table without touching existing rows' grades.
 */
return new class extends Migration
{
    private const TABLE = 'assignment_submissions';

    /** @var list<string> */
    private const COLUMNS = [
        'integrity_status',
        'integrity_reason',
        'provisional_score',
        'integrity_reviewed_by',
        'integrity_reviewed_at',
        'rewards_awarded_at',
    ];

    public function up(): void
    {
        if (! $this->tableExists(self::TABLE)) {
            return;
        }

        if (! $this->columnExists(self::TABLE, 'integrity_status')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                // clear | blocked | review_required. NULL = attempt completed
                // before this column existed (treated as clear).
                $table->string('integrity_status', 30)->nullable();
            });
        }

        if (! $this->columnExists(self::TABLE, 'integrity_reason')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->string('integrity_reason', 255)->nullable();
            });
        }

        if (! $this->columnExists(self::TABLE, 'provisional_score')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->unsignedInteger('provisional_score')->nullable();
            });
        }

        if (! $this->columnExists(self::TABLE, 'integrity_reviewed_by')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->unsignedBigInteger('integrity_reviewed_by')->nullable();
            });
        }

        if (! $this->columnExists(self::TABLE, 'integrity_reviewed_at')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dateTime('integrity_reviewed_at')->nullable();
            });
        }

        if (! $this->columnExists(self::TABLE, 'rewards_awarded_at')) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->dateTime('rewards_awarded_at')->nullable();
            });

            // Attempts finished before this migration already received their
            // achievements/mission progress. Mark them so the one-time reward
            // claim can never pay them a second time. Grades are not touched.
            DB::table(self::TABLE)
                ->whereIn('status', ['graded', 'late'])
                ->whereNull('rewards_awarded_at')
                ->update(['rewards_awarded_at' => DB::raw('COALESCE(graded_at, submitted_at, updated_at, CURRENT_TIMESTAMP)')]);
        }
    }

    public function down(): void
    {
        if (! $this->tableExists(self::TABLE)) {
            return;
        }

        foreach (self::COLUMNS as $column) {
            if ($this->columnExists(self::TABLE, $column)) {
                Schema::table(self::TABLE, function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
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
