<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function columnExists(string $table, string $column): bool
    {
        if (DB::getDriverName() === 'sqlite') {
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
        if (! $this->columnExists('assessments', 'draft_last_item')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedInteger('draft_last_item')->nullable()->after('status');
            });
        }

        if (! $this->columnExists('assessments', 'draft_saved_at')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->timestamp('draft_saved_at')->nullable()->after('draft_last_item');
            });
        }
    }

    public function down(): void
    {
        foreach (['draft_saved_at', 'draft_last_item'] as $column) {
            if ($this->columnExists('assessments', $column)) {
                Schema::table('assessments', function (Blueprint $table) use ($column): void {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
