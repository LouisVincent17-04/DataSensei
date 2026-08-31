<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * MySQL 5.5/MariaDB-compatible migration.
     *
     * Foreign-key names are unique throughout an InnoDB database. The previous
     * names could collide with constraints already stored in the database and
     * produce errno 121. These short, project-specific names avoid that issue,
     * while the information_schema checks make retries safe after a partial run.
     */
    public function up(): void
    {
        if ($this->tableExists('student_ilo_masteries') && $this->tableExists('classes')) {
            DB::table('student_ilo_masteries')
                ->whereNotNull('class_id')
                ->whereNotIn('class_id', DB::table('classes')->select('id'))
                ->update(['class_id' => null]);

            $globalDuplicates = DB::table('student_ilo_masteries')
                ->whereNull('class_id')
                ->select('student_id', 'ilo_id')
                ->groupBy('student_id', 'ilo_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($globalDuplicates as $duplicate) {
                $ids = DB::table('student_ilo_masteries')
                    ->where('student_id', $duplicate->student_id)
                    ->where('ilo_id', $duplicate->ilo_id)
                    ->whereNull('class_id')
                    ->orderByDesc('id')
                    ->pluck('id');

                DB::table('student_ilo_masteries')
                    ->whereIn('id', $ids->slice(1)->values())
                    ->delete();
            }

            $this->addForeignKeyIfMissing(
                'student_ilo_masteries',
                'class_id',
                'classes',
                'id',
                'ds_sim_class_fk'
            );
        }

        if ($this->tableExists('table_of_specifications')) {
            if ($this->tableExists('classes')) {
                DB::table('table_of_specifications')
                    ->whereNotNull('class_id')
                    ->whereNotIn('class_id', DB::table('classes')->select('id'))
                    ->update(['class_id' => null]);

                $this->addForeignKeyIfMissing(
                    'table_of_specifications',
                    'class_id',
                    'classes',
                    'id',
                    'ds_tos_class_fk'
                );
            }

            if ($this->tableExists('users')) {
                DB::table('table_of_specifications')
                    ->whereNotNull('created_by')
                    ->whereNotIn('created_by', DB::table('users')->select('id'))
                    ->update(['created_by' => null]);

                $this->addForeignKeyIfMissing(
                    'table_of_specifications',
                    'created_by',
                    'users',
                    'id',
                    'ds_tos_creator_fk'
                );
            }
        }

        if ($this->tableExists('table_of_specification_rows')
            && $this->tableExists('intended_learning_outcomes')) {
            DB::table('table_of_specification_rows')
                ->whereNotNull('ilo_id')
                ->whereNotIn('ilo_id', DB::table('intended_learning_outcomes')->select('id'))
                ->update(['ilo_id' => null]);

            $this->addForeignKeyIfMissing(
                'table_of_specification_rows',
                'ilo_id',
                'intended_learning_outcomes',
                'id',
                'ds_tos_row_ilo_fk'
            );
        }
    }

    public function down(): void
    {
        $this->dropNamedForeignKeyIfPresent(
            'table_of_specification_rows',
            ['ds_tos_row_ilo_fk', 'table_of_specification_rows_ilo_fk']
        );

        $this->dropNamedForeignKeyIfPresent(
            'table_of_specifications',
            ['ds_tos_creator_fk', 'table_of_specifications_creator_fk']
        );

        $this->dropNamedForeignKeyIfPresent(
            'table_of_specifications',
            ['ds_tos_class_fk', 'table_of_specifications_class_fk']
        );

        $this->dropNamedForeignKeyIfPresent(
            'student_ilo_masteries',
            ['ds_sim_class_fk', 'student_ilo_masteries_class_fk']
        );
    }

    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return DB::getSchemaBuilder()->hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function addForeignKeyIfMissing(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn,
        string $constraintName
    ): void {
        if ($this->matchingForeignKeyExists($table, $column, $referencedTable, $referencedColumn)) {
            return;
        }

        // InnoDB requires foreign-key constraint names to be unique per schema.
        // Use a deterministic fallback if this name is already used elsewhere.
        if ($this->constraintNameExists($constraintName)) {
            $constraintName = substr(
                'ds_'.md5($table.'.'.$column.'.'.$referencedTable.'.'.$referencedColumn),
                0,
                60
            );
        }

        DB::statement(sprintf(
            'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE SET NULL',
            $table,
            $constraintName,
            $column,
            $referencedTable,
            $referencedColumn
        ));
    }

    private function matchingForeignKeyExists(
        string $table,
        string $column,
        string $referencedTable,
        string $referencedColumn
    ): bool {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.key_column_usage
             WHERE constraint_schema = DATABASE()
               AND table_name = ?
               AND column_name = ?
               AND referenced_table_name = ?
               AND referenced_column_name = ?',
            [$table, $column, $referencedTable, $referencedColumn]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function constraintNameExists(string $constraintName): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.referential_constraints
             WHERE constraint_schema = DATABASE()
               AND constraint_name = ?',
            [$constraintName]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function dropNamedForeignKeyIfPresent(string $table, array $constraintNames): void
    {
        if (! $this->tableExists($table)) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            foreach ($constraintNames as $constraintName) {
                try {
                    DB::getSchemaBuilder()->table(
                        $table,
                        fn ($blueprint) => $blueprint->dropForeign($constraintName)
                    );
                } catch (\Throwable) {
                    // The constraint may not have been created by this migration.
                }
            }

            return;
        }

        foreach ($constraintNames as $constraintName) {
            $result = DB::selectOne(
                'SELECT COUNT(*) AS aggregate
                 FROM information_schema.referential_constraints
                 WHERE constraint_schema = DATABASE()
                   AND table_name = ?
                   AND constraint_name = ?',
                [$table, $constraintName]
            );

            if ((int) ($result->aggregate ?? 0) > 0) {
                DB::statement(sprintf(
                    'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                    $table,
                    $constraintName
                ));
            }
        }
    }
};
