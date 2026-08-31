<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

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
        if (! $this->tableExists('challenges')
            || ! $this->columnExists('challenges', 'is_active')
            || ! $this->columnExists('challenges', 'is_coding_challenge')
            || ! $this->columnExists('challenges', 'title')) {
            return;
        }

        $values = ['is_active' => false];

        if ($this->columnExists('challenges', 'updated_at')) {
            $values['updated_at'] = now();
        }

        DB::table('challenges')
            ->where('is_coding_challenge', true)
            ->whereIn('title', [
                'Big Data & Cloud Computing',
                'Sequential Decision Making',
            ])
            ->update($values);
    }

    public function down(): void
    {
        // Existing challenges may have been disabled manually, so rollback must
        // not reactivate them without knowing their prior state.
    }
};
