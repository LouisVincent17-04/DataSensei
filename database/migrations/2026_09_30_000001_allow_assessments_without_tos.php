<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 9: an instructor can create an assessment with one short
 * form, without planning it with a Table of Specifications first, so
 * assessments.table_of_specification_id may now be empty. Assessments made
 * from a TOS keep their link (and its foreign key).
 *
 * MySQL 5.5 safe: a plain MODIFY that keeps the column type, run only while
 * the column is still NOT NULL, so running it twice changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            $column = DB::selectOne(
                'SELECT IS_NULLABLE AS nullable, COLUMN_TYPE AS type FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                ['assessments', 'table_of_specification_id']
            );

            if ($column !== null && strtoupper((string) $column->nullable) === 'NO') {
                DB::statement('ALTER TABLE `assessments` MODIFY `table_of_specification_id` '.$column->type.' NULL');
            }

            return;
        }

        if (Schema::hasTable('assessments') && Schema::hasColumn('assessments', 'table_of_specification_id')) {
            Schema::table('assessments', function (Blueprint $table): void {
                $table->unsignedBigInteger('table_of_specification_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Assessments created without a TOS would block making it required
        // again; nothing is changed back.
    }
};
