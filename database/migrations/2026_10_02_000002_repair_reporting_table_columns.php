<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 12 (fix): repairs the two reporting tables of
 * DataSensei Updates 8 on databases where they existed before that update.
 *
 * 2026_09_28_000005_add_reporting_tables creates module_library_progress
 * and audit_logs only when they are missing. A database that already had a
 * table with one of those names (an older draft, an imported dump, a table
 * made by hand) kept its own columns, so Class Analytics, the instructor
 * Reports, the instructor dashboard and the class module pages failed with
 * "Unknown column 'opened_at' in 'field list'".
 *
 * This adds every column those pages read that is missing, as a nullable
 * column, and fills them from what the old table already holds:
 *
 *   module_library_progress
 *     class_id         nullable
 *     opened_at        the row's created_at (a row exists once a student
 *                      opened the module), else its updated_at
 *     last_opened_at   the row's updated_at, else opened_at
 *     completed_at     only where the old table records a completion
 *                      (is_completed / completed = 1, or status completed /
 *                      complete / done), at the row's updated_at
 *   audit_logs        every column the audit log writes, nullable
 *
 * Only columns added by this migration are filled; nothing is renamed,
 * dropped or overwritten. The unique (user_id, module_library_item_id) index is added only
 * when it is missing and no student has two rows for the same module.
 *
 * Additive only, plain nullable columns, no JSON, no foreign keys, so it
 * runs on MySQL 5.5. On a database where the tables are already right (or
 * missing, when the earlier migration creates them) it changes nothing, and
 * running it again changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->repairModuleLibraryProgress();
        $this->repairAuditLogs();
    }

    public function down(): void
    {
        // Additive repair; nothing is removed.
    }

    private function repairModuleLibraryProgress(): void
    {
        $table = 'module_library_progress';

        if (! $this->tableExists($table)) {
            Schema::create($table, function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('module_library_item_id');
                $table->unsignedBigInteger('class_id')->nullable();
                $table->timestamp('opened_at')->nullable();
                $table->timestamp('last_opened_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'module_library_item_id'], 'module_library_progress_user_module_unique');
                $table->index('module_library_item_id', 'module_library_progress_module_index');
            });

            return;
        }

        $added = [];
        foreach (['user_id', 'module_library_item_id', 'class_id'] as $column) {
            if (! $this->columnExists($table, $column)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->unsignedBigInteger($column)->nullable());
                $added[$column] = true;
            }
        }
        foreach (['opened_at', 'last_opened_at', 'completed_at', 'created_at', 'updated_at'] as $column) {
            if (! $this->columnExists($table, $column)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->timestamp($column)->nullable());
                $added[$column] = true;
            }
        }

        // A module the old table named under another column.
        if (isset($added['module_library_item_id'])) {
            foreach (['module_library_id', 'library_item_id', 'module_id'] as $legacy) {
                if ($this->columnExists($table, $legacy)) {
                    DB::table($table)->whereNull('module_library_item_id')->update(['module_library_item_id' => DB::raw($legacy)]);
                    break;
                }
            }
        }
        if (isset($added['user_id']) && $this->columnExists($table, 'student_id')) {
            DB::table($table)->whereNull('user_id')->update(['user_id' => DB::raw('student_id')]);
        }

        // Dates, from what the old rows hold, only for the columns added
        // now (a table that already had them is left exactly as it is).
        if (isset($added['opened_at'])) {
            DB::table($table)->whereNull('opened_at')->update(['opened_at' => DB::raw('COALESCE(created_at, updated_at)')]);
        }
        if (isset($added['last_opened_at'])) {
            DB::table($table)->whereNull('last_opened_at')->update(['last_opened_at' => DB::raw('COALESCE(updated_at, opened_at)')]);
        }

        if (isset($added['completed_at'])) {
            $completion = null;
            foreach (['is_completed', 'completed'] as $flag) {
                if ($this->columnExists($table, $flag)) {
                    $completion = fn ($query) => $query->where($flag, 1);
                    break;
                }
            }
            if ($completion === null && $this->columnExists($table, 'status')) {
                $completion = fn ($query) => $query->whereIn('status', ['completed', 'complete', 'done']);
            }
            if ($completion !== null) {
                $completion(DB::table($table)->whereNull('completed_at'))
                    ->update(['completed_at' => DB::raw('COALESCE(updated_at, created_at, opened_at)')]);
            }
        }

        if (! $this->indexExists($table, 'module_library_progress_module_index')
            && ! $this->indexExists($table, 'module_library_progress_user_module_unique')) {
            $duplicates = DB::table($table)
                ->select('user_id', 'module_library_item_id')
                ->groupBy('user_id', 'module_library_item_id')
                ->havingRaw('COUNT(*) > 1')
                ->limit(1)
                ->get()
                ->isNotEmpty();

            Schema::table($table, function (Blueprint $blueprint) use ($duplicates): void {
                if (! $duplicates) {
                    $blueprint->unique(['user_id', 'module_library_item_id'], 'module_library_progress_user_module_unique');
                }
                $blueprint->index('module_library_item_id', 'module_library_progress_module_index');
            });
        }
    }

    private function repairAuditLogs(): void
    {
        if (! $this->tableExists('audit_logs')) {
            return; // created by 2026_09_28_000005 when missing
        }

        $columns = [
            'user_id' => fn (Blueprint $t) => $t->unsignedBigInteger('user_id')->nullable(),
            'user_name' => fn (Blueprint $t) => $t->string('user_name', 189)->nullable(),
            'user_role' => fn (Blueprint $t) => $t->unsignedTinyInteger('user_role')->nullable(),
            'action' => fn (Blueprint $t) => $t->string('action', 40)->nullable(),
            'record_type' => fn (Blueprint $t) => $t->string('record_type', 80)->nullable(),
            'record_id' => fn (Blueprint $t) => $t->unsignedBigInteger('record_id')->nullable(),
            'record_label' => fn (Blueprint $t) => $t->string('record_label', 255)->nullable(),
            'details' => fn (Blueprint $t) => $t->string('details', 500)->nullable(),
            'created_at' => fn (Blueprint $t) => $t->timestamp('created_at')->nullable(),
        ];

        foreach ($columns as $column => $define) {
            if (! $this->columnExists('audit_logs', $column)) {
                Schema::table('audit_logs', fn (Blueprint $blueprint) => $define($blueprint));
            }
        }
    }

    /**
     * Plain information_schema queries, like the earlier migrations, so they
     * also work on MySQL 5.5 (Schema::hasColumn does not there).
     */
    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
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
            'SELECT COUNT(*) AS aggregate FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
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
            'SELECT COUNT(*) AS aggregate FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $index]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
