<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Add as nullable first because existing rows must be backfilled before
        // MySQL can enforce NOT NULL and uniqueness.
        if (! $this->columnExists('institutions', 'institution_code')) {
            Schema::table('institutions', function (Blueprint $table): void {
                $table->string('institution_code', 6)->nullable()->after('slug');
            });
        }

        DB::table('institutions')
            ->whereNull('institution_code')
            ->orWhere('institution_code', '')
            ->get()
            ->each(function ($institution): void {
                do {
                    $code = strtoupper(Str::random(6));
                } while (DB::table('institutions')->where('institution_code', $code)->exists());

                DB::table('institutions')
                    ->where('id', $institution->id)
                    ->update(['institution_code' => $code]);
            });

        if (DB::connection()->getDriverName() === 'mysql') {
            // Raw MODIFY keeps this compatible with the deployed MySQL 5.5
            // server and avoids Laravel's newer metadata query.
            DB::statement(
                'ALTER TABLE institutions MODIFY institution_code VARCHAR(6) NOT NULL'
            );
        }

        if (! $this->indexExists('institutions', 'institutions_institution_code_unique')) {
            Schema::table('institutions', function (Blueprint $table): void {
                $table->unique('institution_code', 'institutions_institution_code_unique');
            });
        }
    }

    public function down(): void
    {
        if (! $this->columnExists('institutions', 'institution_code')) {
            return;
        }

        $dropUnique = $this->indexExists(
            'institutions',
            'institutions_institution_code_unique'
        );

        Schema::table('institutions', function (Blueprint $table) use ($dropUnique): void {
            if ($dropUnique) {
                $table->dropUnique('institutions_institution_code_unique');
            }
            $table->dropColumn('institution_code');
        });
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

    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasIndex($table, $index);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND INDEX_NAME = ?',
            [$table, $index]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
