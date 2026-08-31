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
        if (! $this->columnExists('table_of_specifications', 'custom_coverage')) {
            Schema::table('table_of_specifications', function (Blueprint $table): void {
                $table->string('custom_coverage', 191)->nullable()->after('module_no');
            });
        }
    }

    public function down(): void
    {
        if ($this->columnExists('table_of_specifications', 'custom_coverage')) {
            Schema::table('table_of_specifications', function (Blueprint $table): void {
                $table->dropColumn('custom_coverage');
            });
        }
    }
};
