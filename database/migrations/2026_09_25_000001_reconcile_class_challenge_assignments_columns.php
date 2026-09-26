<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Brings an older class_challenge_assignments table up to the columns the
 * instructor class-challenge feature reads.
 *
 * The previous migration creates the table only when it is missing. On a
 * long-lived database a table of that name already existed with a different
 * shape, so the create was skipped and the assignments page failed with
 * "Unknown column 'available_at'". This adds each missing column on its own,
 * additively: no JSON, no foreign keys, nothing that touches existing rows,
 * so it runs on MySQL 5.5. On a database where the table was created by the
 * previous migration every check is a no-op.
 */
return new class extends Migration
{
    private const TABLE = 'class_challenge_assignments';

    public function up(): void
    {
        if (! $this->tableExists(self::TABLE)) {
            return;
        }

        $additions = [
            'class_id' => fn (Blueprint $table) => $table->unsignedBigInteger('class_id')->nullable(),
            'challenge_id' => fn (Blueprint $table) => $table->unsignedBigInteger('challenge_id')->nullable(),
            'assigned_by' => fn (Blueprint $table) => $table->unsignedBigInteger('assigned_by')->nullable(),
            'title' => fn (Blueprint $table) => $table->string('title', 189)->nullable(),
            'instructions' => fn (Blueprint $table) => $table->text('instructions')->nullable(),
            'available_at' => fn (Blueprint $table) => $table->dateTime('available_at')->nullable(),
            'due_at' => fn (Blueprint $table) => $table->dateTime('due_at')->nullable(),
            'status' => fn (Blueprint $table) => $table->string('status', 20)->default('draft'),
            'created_at' => fn (Blueprint $table) => $table->timestamp('created_at')->nullable(),
            'updated_at' => fn (Blueprint $table) => $table->timestamp('updated_at')->nullable(),
        ];

        foreach ($additions as $column => $definition) {
            if ($this->columnExists(self::TABLE, $column)) {
                continue;
            }

            Schema::table(self::TABLE, function (Blueprint $table) use ($definition): void {
                $definition($table);
            });
        }

        // One challenge per class. Added only when both columns exist and no
        // index of that name does, and never when existing rows would violate it.
        if (
            $this->columnExists(self::TABLE, 'class_id')
            && $this->columnExists(self::TABLE, 'challenge_id')
            && ! $this->indexExists(self::TABLE, 'class_challenge_assignments_unique')
            && ! $this->hasDuplicatePairs()
        ) {
            Schema::table(self::TABLE, function (Blueprint $table): void {
                $table->unique(['class_id', 'challenge_id'], 'class_challenge_assignments_unique');
            });
        }
    }

    public function down(): void
    {
        // Additive reconciliation; the columns are left in place.
    }

    private function hasDuplicatePairs(): bool
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM (
                SELECT class_id, challenge_id
                FROM '.self::TABLE.'
                WHERE class_id IS NOT NULL AND challenge_id IS NOT NULL
                GROUP BY class_id, challenge_id
                HAVING COUNT(*) > 1
            ) AS duplicates'
        );

        return (int) ($row->aggregate ?? 0) > 0;
    }

    private function indexExists(string $table, string $index): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            try {
                return in_array($index, array_keys(Schema::getIndexes($table) ? array_column(Schema::getIndexes($table), null, 'name') : []), true);
            } catch (\Throwable) {
                return false;
            }
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

    /**
     * Schema::hasColumn() and Schema::hasTable() read information_schema
     * columns (generation_expression) that MySQL 5.5 does not have, so they
     * fail there with "Unknown column". These plain queries work on every
     * MySQL/MariaDB version, like the helpers of the earlier migrations.
     */
    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

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
};
