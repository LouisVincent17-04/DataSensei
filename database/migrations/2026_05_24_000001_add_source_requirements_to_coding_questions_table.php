<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * OLD MYSQL / MARIADB SAFE CHECK
         *
         * Do not use Schema::hasColumn() here because Laravel 12 may query
         * information_schema.columns.generation_expression, which does not exist
         * in older MySQL/MariaDB versions.
         */
        $column = DB::selectOne("
            SELECT COUNT(*) AS column_exists
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'coding_questions'
              AND COLUMN_NAME = 'source_requirements'
        ");

        if ((int) $column->column_exists === 0) {
            /*
             * Use LONGTEXT instead of JSON for compatibility with older MySQL.
             * Laravel can still treat this as JSON using model casts.
             */
            DB::statement("
                ALTER TABLE coding_questions
                ADD COLUMN source_requirements LONGTEXT NULL
                AFTER base_xp
            ");
        }
    }

    public function down(): void
    {
        $column = DB::selectOne("
            SELECT COUNT(*) AS column_exists
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'coding_questions'
              AND COLUMN_NAME = 'source_requirements'
        ");

        if ((int) $column->column_exists > 0) {
            DB::statement("
                ALTER TABLE coding_questions
                DROP COLUMN source_requirements
            ");
        }
    }
};