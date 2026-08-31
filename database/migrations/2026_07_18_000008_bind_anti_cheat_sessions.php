<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            || $this->columnExists('assignment_submissions', 'anti_cheat_session_id')) {
            return;
        }

        Schema::table('assignment_submissions', function (Blueprint $table): void {
            $table->string('anti_cheat_session_id', 120)
                ->nullable()
                ->after('feedback');
            $table->unique('anti_cheat_session_id', 'assignment_submission_anti_cheat_session_uq');
        });
    }

    public function down(): void
    {
        if (! $this->tableExists('assignment_submissions')
            || ! $this->columnExists('assignment_submissions', 'anti_cheat_session_id')) {
            return;
        }

        Schema::table('assignment_submissions', function (Blueprint $table): void {
            $table->dropUnique('assignment_submission_anti_cheat_session_uq');
            $table->dropColumn('anti_cheat_session_id');
        });
    }
};
