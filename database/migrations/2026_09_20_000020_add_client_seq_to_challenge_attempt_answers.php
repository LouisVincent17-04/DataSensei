<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-question autosave sequence number sent by the quiz page. It lets the
     * server ignore a delayed autosave request that arrives after a newer edit
     * of the same question. Existing rows keep 0, which every new edit beats.
     */
    public function up(): void
    {
        if (! $this->tableExists('challenge_attempt_answers')) {
            return;
        }

        if (! $this->columnExists('challenge_attempt_answers', 'client_seq')) {
            Schema::table('challenge_attempt_answers', function (Blueprint $table): void {
                $table->unsignedInteger('client_seq')->default(0);
            });
        }
    }

    public function down(): void
    {
        if ($this->tableExists('challenge_attempt_answers')
            && $this->columnExists('challenge_attempt_answers', 'client_seq')) {
            Schema::table('challenge_attempt_answers', function (Blueprint $table): void {
                $table->dropColumn('client_seq');
            });
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
