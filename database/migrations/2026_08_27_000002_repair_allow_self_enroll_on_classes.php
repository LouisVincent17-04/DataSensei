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
        if (! $this->columnExists('classes', 'allow_self_enroll')) {
            Schema::table('classes', function (Blueprint $table): void {
                $table->boolean('allow_self_enroll')->default(false);
            });
        }
    }

    public function down(): void
    {
        // This repair migration may run after the column was already supplied by
        // the original classes migration. Removing it during a one-step rollback
        // would break those databases, so the repair is intentionally permanent.
    }
};
