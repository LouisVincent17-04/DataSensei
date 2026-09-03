<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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

    public function up(): void
    {
        foreach (['assignment_submissions', 'assessment_submissions'] as $tableName) {
            if (! $this->tableExists($tableName)) {
                continue;
            }

            if (! $this->columnExists($tableName, 'draft_answers')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    // LONGTEXT keeps this migration compatible with the existing
                    // MySQL deployment while Eloquent handles JSON encoding.
                    $table->longText('draft_answers')->nullable();
                });
            }

            if (! $this->columnExists($tableName, 'draft_version')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->unsignedBigInteger('draft_version')->default(0);
                });
            }

            if (! $this->columnExists($tableName, 'draft_saved_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dateTime('draft_saved_at')->nullable();
                });
            }

            if (! $this->columnExists($tableName, 'timed_out_at')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dateTime('timed_out_at')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        // Forward-only by design. These columns may have existed on a repaired
        // installation before this migration was recorded, so rollback must not
        // destroy answer snapshots or timeout audit evidence.
    }
};
