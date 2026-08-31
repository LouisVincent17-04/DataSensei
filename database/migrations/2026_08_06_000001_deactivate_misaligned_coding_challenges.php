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
        foreach (['is_active', 'is_coding_challenge', 'order_index'] as $column) {
            if (! $this->tableExists('challenges') || ! $this->columnExists('challenges', $column)) {
                return;
            }
        }

        $values = ['is_active' => false];
        if ($this->columnExists('challenges', 'updated_at')) {
            $values['updated_at'] = now();
        }

        // These generated coding banks are not defensible evidence for their
        // published module topics. Preserve every row and historical attempt;
        // simply stop offering them to new learners.
        DB::table('challenges')
            ->where('is_coding_challenge', true)
            ->whereIn('order_index', [10, 18, 22, 24])
            ->update($values);
    }

    public function down(): void
    {
        // Do not blindly reactivate content: a challenge may have been disabled
        // independently by an administrator after this migration ran.
    }
};
