<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->tableExists('training_jobs')) {
            return;
        }

        $addAttemptNumber = ! $this->columnExists('training_jobs', 'attempt_number');
        $addNextRetryAt = ! $this->columnExists('training_jobs', 'next_retry_at');
        if (! $addAttemptNumber && ! $addNextRetryAt) {
            return;
        }

        Schema::table('training_jobs', function (Blueprint $table) use ($addAttemptNumber, $addNextRetryAt): void {
            if ($addAttemptNumber) {
                $table->unsignedSmallInteger('attempt_number')->default(0)->after('stage');
            }
            if ($addNextRetryAt) {
                $table->timestamp('next_retry_at')->nullable()->after('attempt_number');
            }
        });
    }

    public function down(): void
    {
        if (! $this->tableExists('training_jobs')) {
            return;
        }

        $dropNextRetryAt = $this->columnExists('training_jobs', 'next_retry_at');
        $dropAttemptNumber = $this->columnExists('training_jobs', 'attempt_number');
        if (! $dropNextRetryAt && ! $dropAttemptNumber) {
            return;
        }

        Schema::table('training_jobs', function (Blueprint $table) use ($dropNextRetryAt, $dropAttemptNumber): void {
            if ($dropNextRetryAt) {
                $table->dropColumn('next_retry_at');
            }
            if ($dropAttemptNumber) {
                $table->dropColumn('attempt_number');
            }
        });
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
