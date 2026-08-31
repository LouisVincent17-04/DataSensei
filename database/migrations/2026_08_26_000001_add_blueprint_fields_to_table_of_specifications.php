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
        if (! $this->columnExists('table_of_specifications', 'total_items')) {
            Schema::table('table_of_specifications', function (Blueprint $table) {
                $table->unsignedInteger('total_items')->default(0)->after('module_no');
            });
        }

        if (! $this->columnExists('table_of_specifications', 'cognitive_distribution')) {
            // Text is intentionally used instead of a native JSON column for compatibility
            // with the older MySQL/MariaDB environments already supported by DataSensei.
            Schema::table('table_of_specifications', function (Blueprint $table) {
                $table->text('cognitive_distribution')->nullable()->after('status');
            });
        }

        DB::table('table_of_specification_rows')
            ->select('table_of_specification_id', DB::raw('SUM(item_count) AS assigned_items'))
            ->groupBy('table_of_specification_id')
            ->orderBy('table_of_specification_id')
            ->get()
            ->each(function ($row): void {
                DB::table('table_of_specifications')
                    ->where('id', $row->table_of_specification_id)
                    ->where('total_items', 0)
                    ->update(['total_items' => (int) $row->assigned_items]);
            });
    }

    public function down(): void
    {
        if ($this->columnExists('table_of_specifications', 'cognitive_distribution')) {
            Schema::table('table_of_specifications', function (Blueprint $table) {
                $table->dropColumn('cognitive_distribution');
            });
        }

        if ($this->columnExists('table_of_specifications', 'total_items')) {
            Schema::table('table_of_specifications', function (Blueprint $table) {
                $table->dropColumn('total_items');
            });
        }
    }
};
