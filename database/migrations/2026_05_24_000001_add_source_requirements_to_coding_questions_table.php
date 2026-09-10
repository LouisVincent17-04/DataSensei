<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if ($this->columnExists('coding_questions', 'source_requirements')) {
            return;
        }

        // LONGTEXT is supported by the deployed MySQL 5.5 server. Eloquent
        // still handles JSON encoding through the model cast.
        Schema::table('coding_questions', function (Blueprint $table): void {
            $table->longText('source_requirements')->nullable()->after('base_xp');
        });
    }

    public function down(): void
    {
        if (! $this->columnExists('coding_questions', 'source_requirements')) {
            return;
        }

        Schema::table('coding_questions', function (Blueprint $table): void {
            $table->dropColumn('source_requirements');
        });
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        // Avoid Schema::hasColumn() on MySQL 5.5 because Laravel's metadata
        // query references generation_expression, which that server lacks.
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
