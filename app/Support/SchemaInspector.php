<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * "Does this table / column exist?" for code that runs on every page.
 *
 * Laravel's Schema::hasTable() and hand-written information_schema lookups
 * ask MySQL once per call. On MySQL 5.5 every information_schema.TABLES
 * lookup also makes InnoDB recalculate that table's index statistics
 * (innodb_stats_on_metadata is ON there), which reads from disk. The admin
 * dashboard made 65 such lookups per page and the student dashboard 15;
 * after a quiet period, with cold caches, the page after signing in could
 * run past PHP's 60-second limit, and PHP reported the limit wherever it
 * happened to be, usually inside Composer's class loader.
 *
 * Here the table list is read once per request with SHOW TABLES (a plain
 * directory listing, no statistics) and each table's columns at most once
 * with SHOW COLUMNS. Registered as a scoped binding, so it never outlives a
 * request, a queued job or a test.
 */
final class SchemaInspector
{
    /** @var array<string, true>|null */
    private ?array $tables = null;

    private bool $listingFailed = false;

    /** @var array<string, array<string, true>> */
    private array $columns = [];

    public static function hasTable(string $table): bool
    {
        return app(self::class)->tableExists($table);
    }

    public static function hasColumn(string $table, string $column): bool
    {
        return app(self::class)->columnExists($table, $column);
    }

    public function tableExists(string $table): bool
    {
        if ($this->tables === null && ! $this->listingFailed) {
            $this->tables = $this->loadTables();
            $this->listingFailed = $this->tables === null;
        }

        if ($this->tables === null) {
            // The listing itself failed; ask the portable way instead.
            try {
                return Schema::hasTable($table);
            } catch (Throwable) {
                return false;
            }
        }

        return isset($this->tables[strtolower($this->prefixed($table))]);
    }

    public function columnExists(string $table, string $column): bool
    {
        if (! $this->tableExists($table)) {
            return false;
        }

        $key = strtolower($table);
        if (! array_key_exists($key, $this->columns)) {
            $this->columns[$key] = $this->loadColumns($table);
        }

        return isset($this->columns[$key][strtolower($column)]);
    }

    /** @return array<string, true>|null */
    private function loadTables(): ?array
    {
        try {
            $connection = DB::connection();

            if ($this->isMySql()) {
                $names = array_map(
                    static fn ($row): string => (string) array_values((array) $row)[0],
                    $connection->select('SHOW TABLES')
                );
            } else {
                $names = array_map(
                    static fn (array $table): string => (string) $table['name'],
                    Schema::getTables()
                );
            }
        } catch (Throwable) {
            return null;
        }

        $tables = [];
        foreach ($names as $name) {
            $tables[strtolower($name)] = true;
        }

        return $tables;
    }

    /** @return array<string, true> */
    private function loadColumns(string $table): array
    {
        if (preg_match('/^[A-Za-z0-9_]+$/', $table) !== 1) {
            return [];
        }

        try {
            if ($this->isMySql()) {
                $names = array_map(
                    static fn ($row): string => (string) ($row->Field ?? array_values((array) $row)[0]),
                    DB::connection()->select('SHOW COLUMNS FROM `'.$this->prefixed($table).'`')
                );
            } else {
                $names = Schema::getColumnListing($table);
            }
        } catch (Throwable) {
            return [];
        }

        $columns = [];
        foreach ($names as $name) {
            $columns[strtolower((string) $name)] = true;
        }

        return $columns;
    }

    private function isMySql(): bool
    {
        try {
            return in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true);
        } catch (Throwable) {
            return false;
        }
    }

    private function prefixed(string $table): string
    {
        try {
            return DB::connection()->getTablePrefix().$table;
        } catch (Throwable) {
            return $table;
        }
    }
}
