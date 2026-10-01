<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 5, tasks 4 and 5.
 *
 *   module_library_items.learning_outcomes  the module's intended learning
 *   modules.learning_outcomes               outcomes: plain sentences stored
 *                                           as a JSON list in a TEXT column,
 *                                           shown as "What You Will Learn"
 *   modules.review_questions                embedded review questions of a
 *                                           public module (same shape as the
 *                                           library's mcq_questions), not
 *                                           scored
 *   modules.is_published                    whether a public module is shown
 *                                           to learners; existing modules
 *                                           stay published
 *
 * Existing modules get outcomes from the content they already have: a
 * library version from its "Intended Learning Outcomes" (or "Learning
 * Outcomes" / "Learning Objectives") section, the key points listed there, a public module from the library module with the same
 * number and title, and otherwise from the intended_learning_outcomes table.
 * Nothing is removed or rewritten: only empty learning_outcomes are filled.
 *
 * Additive only: nullable TEXT columns and a boolean with a default, no JSON
 * column type, no foreign keys, so it runs on MySQL 5.5. Running it again
 * changes nothing.
 */
return new class extends Migration
{
    public function up(): void
    {
        if ($this->tableExists('module_library_items') && ! $this->columnExists('module_library_items', 'learning_outcomes')) {
            Schema::table('module_library_items', function (Blueprint $table): void {
                $table->text('learning_outcomes')->nullable();
            });
        }

        if ($this->tableExists('modules')) {
            if (! $this->columnExists('modules', 'learning_outcomes')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->text('learning_outcomes')->nullable();
                });
            }

            if (! $this->columnExists('modules', 'review_questions')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->longText('review_questions')->nullable();
                });
            }

            if (! $this->columnExists('modules', 'is_published')) {
                Schema::table('modules', function (Blueprint $table): void {
                    $table->boolean('is_published')->default(true);
                });
            }
        }

        $this->fillLibraryOutcomes();
        $this->fillPublicModuleOutcomes();
    }

    public function down(): void
    {
        // Additive; the columns are left in place.
    }

    private function fillLibraryOutcomes(): void
    {
        if (! $this->tableExists('module_library_items') || ! $this->columnExists('module_library_items', 'learning_outcomes')) {
            return;
        }

        DB::table('module_library_items')
            ->whereNull('learning_outcomes')
            ->orderBy('id')
            ->select(['id', 'module_no', 'content_sections'])
            ->chunkById(10, function ($items): void {
                foreach ($items as $item) {
                    $outcomes = $this->outcomesFromSections($this->decodeList($item->content_sections));

                    if ($outcomes === []) {
                        $outcomes = $this->outcomesFromTable((int) $item->module_no);
                    }

                    DB::table('module_library_items')
                        ->where('id', $item->id)
                        ->update(['learning_outcomes' => $this->encode($outcomes)]);
                }
            });
    }

    private function fillPublicModuleOutcomes(): void
    {
        if (! $this->tableExists('modules') || ! $this->columnExists('modules', 'learning_outcomes')) {
            return;
        }

        $modules = DB::table('modules')->whereNull('learning_outcomes')->get(['id', 'title', 'order_index']);

        foreach ($modules as $module) {
            $outcomes = [];

            if ($this->tableExists('module_library_items') && $this->columnExists('module_library_items', 'learning_outcomes')) {
                $library = DB::table('module_library_items')
                    ->where('module_no', (int) $module->order_index)
                    ->where('title', (string) $module->title)
                    ->orderBy('version_no')
                    ->value('learning_outcomes');

                $outcomes = $this->decodeList($library);
            }

            if ($outcomes === []) {
                $outcomes = $this->outcomesFromTable((int) $module->order_index);
            }

            DB::table('modules')
                ->where('id', $module->id)
                ->update(['learning_outcomes' => $this->encode($outcomes)]);
        }
    }

    /**
     * The key points of a section headed "Intended Learning Outcomes"
     * (or "Learning Outcomes" / "Learning Objectives").
     */
    private function outcomesFromSections(array $sections): array
    {
        foreach ($sections as $section) {
            if (! is_array($section) || ! preg_match('/^\s*(intended\s+)?learning\s+(outcomes?|objectives?)\s*$/i', (string) ($section['heading'] ?? ''))) {
                continue;
            }

            return $this->cleanList($section['key_points'] ?? []);
        }

        return [];
    }

    private function outcomesFromTable(int $moduleNo): array
    {
        if ($moduleNo <= 0 || ! $this->tableExists('intended_learning_outcomes')) {
            return [];
        }

        return $this->cleanList(
            DB::table('intended_learning_outcomes')
                ->where('module_no', $moduleNo)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['title', 'description'])
                ->map(fn ($row) => trim((string) ($row->description ?: $row->title)))
                ->all()
        );
    }

    private function cleanList(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return array_values(array_filter(
            array_map(fn ($value) => is_string($value) ? trim($value) : '', $values),
            fn (string $value) => $value !== ''
        ));
    }

    /** Decodes a JSON list, also when it was stored JSON-encoded twice. */
    private function decodeList(mixed $value): array
    {
        $decoded = $value;

        for ($attempt = 0; $attempt < 3 && is_string($decoded); $attempt++) {
            $decoded = json_decode(trim($decoded), true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function encode(array $outcomes): string
    {
        return json_encode(array_values($outcomes), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
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
};
