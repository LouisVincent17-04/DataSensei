<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    private function tableExists(string $table): bool
    {
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
        if (! $this->tableExists('assignment_submissions')
            || ! $this->columnExists('assignment_submissions', 'anti_cheat_session_id')) {
            return;
        }

        DB::table('assignment_submissions')
            ->where('status', 'in_progress')
            ->whereNull('anti_cheat_session_id')
            ->orderBy('id')
            ->chunkById(100, function ($submissions): void {
                foreach ($submissions as $submission) {
                    DB::table('assignment_submissions')
                        ->where('id', $submission->id)
                        ->whereNull('anti_cheat_session_id')
                        ->update(['anti_cheat_session_id' => Str::random(64)]);
                }
            });
    }

    public function down(): void
    {
        // Tokens are audit/security data and cannot be meaningfully restored to
        // their pre-migration null values, so rollback intentionally keeps them.
    }
};
