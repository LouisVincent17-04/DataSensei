<?php

use App\Support\AssignmentToAssessmentMerge;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 11: the separate Assignment feature is retired.
 *
 * Every class assignment is first converted into an assessment (questions,
 * submissions, answers, grades, feedback and anti-cheat records included; see
 * App\Support\AssignmentToAssessmentMerge), the active admin assignment
 * library is preserved as the shared Question Bank pool, and only then are
 * the assignment tables removed.
 *
 * Archival form for anything that cannot be converted: the table
 * legacy_assignment_archive. A class assignment can only be unconvertible
 * when its library item is missing (possible only if rows were imported with
 * foreign-key checks off), since every old question type (mcq, fill_blank)
 * has an assessment equivalent. Such an assignment is stored there verbatim,
 * one row per record (the class_assignments row, each submission, each
 * answer), as a JSON-encoded copy of the original columns in `payload`,
 * with `reason` explaining why it was not converted. Its anti-cheat events
 * stay in anti_cheat_events with their original ids. If the archive table
 * cannot be written, the assignment tables are kept instead of dropped.
 *
 * Other tables may hold foreign keys to the assignment tables (an older
 * schema, an imported dump, a table added outside these migrations). Those
 * constraints are removed first; their columns and values stay, as a record
 * of the original ids. Without this, MySQL refuses to drop a referenced
 * table (errors 1217 / 1451).
 *
 * Rerunning is safe: converted rows are remembered by their legacy ids, and
 * once the tables are gone the migration does nothing. A run that stopped
 * part-way through the drops (for example on such a foreign key) is resumed:
 * nothing is converted twice and the remaining tables are dropped.
 */
return new class extends Migration
{
    /** Children first, parents last. */
    private const LEGACY_TABLES = [
        'assignment_submission_answers',
        'assignment_submissions',
        'assignment_blank_answers',
        'assignment_question_options',
        'assignment_questions',
        'class_assignments',
        'assignment_library_items',
    ];

    public function up(): void
    {
        // Created on every install so the schema is the same everywhere.
        // Plain columns only (longText payload, no JSON type): MySQL 5.5.
        if (! Schema::hasTable('legacy_assignment_archive')) {
            Schema::create('legacy_assignment_archive', function (Blueprint $table): void {
                $table->id();
                $table->string('source_table', 64);
                $table->unsignedBigInteger('source_id');
                $table->unsignedBigInteger('class_assignment_id')->nullable()->index();
                $table->string('reason', 255);
                $table->longText('payload');
                $table->timestamp('archived_at')->nullable();
                $table->timestamps();
                $table->unique(['source_table', 'source_id'], 'legacy_assignment_archive_source_unique');
            });
        }

        if (! Schema::hasTable('class_assignments')) {
            return;
        }

        $merge = new AssignmentToAssessmentMerge();
        $merge->convert();

        if (! $merge->fullyConverted()) {
            // Something was neither converted nor archived: keep the original
            // tables rather than drop them; the next run picks it up.
            return;
        }

        $dropTables = function (): void {
            foreach (self::LEGACY_TABLES as $table) {
                Schema::dropIfExists($table);
            }
        };

        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            $this->dropForeignKeysToLegacyTables();
            $dropTables();
        } else {
            // SQLite cannot drop a single constraint; the tables go with
            // checks paused instead.
            Schema::withoutForeignKeyConstraints($dropTables);
        }
    }

    /**
     * Remove every foreign key, in any table, that points at one of the
     * assignment tables. Only the constraint goes: the column and its values
     * stay. Uses information_schema, which MySQL 5.5 already provides.
     */
    private function dropForeignKeysToLegacyTables(): void
    {
        $placeholders = implode(',', array_fill(0, count(self::LEGACY_TABLES), '?'));
        $keys = DB::select(
            "SELECT DISTINCT TABLE_NAME AS table_name, CONSTRAINT_NAME AS constraint_name, REFERENCED_TABLE_NAME AS referenced_table
               FROM information_schema.KEY_COLUMN_USAGE
              WHERE TABLE_SCHEMA = DATABASE()
                AND REFERENCED_TABLE_NAME IN ({$placeholders})",
            self::LEGACY_TABLES
        );

        foreach ($keys as $key) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                str_replace('`', '``', $key->table_name),
                str_replace('`', '``', $key->constraint_name)
            ));

            if (! in_array($key->table_name, self::LEGACY_TABLES, true)) {
                Log::info('Updates 11 merge: removed a foreign key that pointed at a retired assignment table; the column and its values are kept.', [
                    'table' => $key->table_name,
                    'constraint' => $key->constraint_name,
                    'referenced_table' => $key->referenced_table,
                ]);
            }
        }
    }

    public function down(): void
    {
        // The converted records live on as assessments; the original tables
        // are not recreated.
    }
};
