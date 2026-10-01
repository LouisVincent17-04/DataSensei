<?php

namespace App\Support\Reports;

use App\Support\SchemaInspector;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

/**
 * Column lists for report queries that keep working on a database whose
 * table is missing an optional column (DataSensei Updates 12 fix).
 *
 * A column that does not exist is read as NULL under the same name, so a
 * page shows "no data yet" for it instead of failing with "Unknown column".
 * The migration 2026_10_02_000002 adds the missing columns; this only keeps
 * the pages up until it has run.
 */
final class Columns
{
    /**
     * @param  list<string>  $required  columns the query cannot do without
     * @param  list<string>  $optional  columns read as NULL when missing
     * @return list<string|Expression>
     */
    public static function pick(string $table, array $required, array $optional = []): array
    {
        $columns = $required;

        foreach ($optional as $column) {
            $columns[] = SchemaInspector::hasColumn($table, $column)
                ? $column
                : DB::raw('NULL as '.$column);
        }

        return $columns;
    }
}
