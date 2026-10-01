<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 8: what the new Reports and Class Analytics read.
 *
 *   audit_logs               one row per important action an admin, super
 *                            admin, institution admin or instructor performed
 *                            (created, edited, deleted, published, assigned,
 *                            ...): who, their role, the action, the affected
 *                            record and when (Admin Reports > Audit Logs)
 *   module_library_progress  when a student first opened a class module and
 *                            when they marked it complete, so class module
 *                            progress can be reported
 *   module_user.opened_at    when a learner first opened a DataSensei Module,
 *                            so "users who accessed each module" is known
 *
 * Additive only: new tables, one nullable column, plain indexes, no JSON
 * columns and no foreign keys, so it runs on MySQL 5.5. Running it again
 * changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! $this->tableExists('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name', 189)->nullable();
                $table->unsignedTinyInteger('user_role')->nullable();
                $table->string('action', 40);
                $table->string('record_type', 80);
                $table->unsignedBigInteger('record_id')->nullable();
                $table->string('record_label', 255)->nullable();
                $table->string('details', 500)->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index('created_at', 'audit_logs_created_at_index');
                $table->index('user_id', 'audit_logs_user_id_index');
                $table->index('action', 'audit_logs_action_index');
            });
        }

        if (! $this->tableExists('module_library_progress')) {
            Schema::create('module_library_progress', function (Blueprint $table): void {
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
        }

        if ($this->tableExists('module_user') && ! $this->columnExists('module_user', 'opened_at')) {
            Schema::table('module_user', function (Blueprint $table): void {
                $table->timestamp('opened_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Reports keep their history; nothing is dropped.
    }

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
};
