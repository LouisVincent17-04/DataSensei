<?php

namespace App\Support\Reports;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * One table of a report (DataSensei Updates 8).
 *
 * Rows are plain display values keyed like the columns. A row may carry
 * "_url" (the first cell links there) and "_tone" per column ("good",
 * "warn", "bad") for the plain coloured text some cells use.
 */
final class ReportTable
{
    /**
     * @param  array<string, string>  $columns  key => heading
     * @param  LengthAwarePaginator|list<array<string, mixed>>  $rows
     */
    public function __construct(
        public readonly string $key,
        public readonly string $title,
        public readonly array $columns,
        public readonly LengthAwarePaginator|array $rows,
        public readonly string $empty = 'Nothing matches these filters.',
        public readonly ?string $note = null,
        public readonly bool $truncated = false,
    ) {
    }

    /** @return list<array<string, mixed>> */
    public function rowList(): array
    {
        return $this->rows instanceof LengthAwarePaginator ? array_values($this->rows->items()) : array_values($this->rows);
    }

    public function paginator(): ?LengthAwarePaginator
    {
        return $this->rows instanceof LengthAwarePaginator ? $this->rows : null;
    }

    public function total(): int
    {
        return $this->rows instanceof LengthAwarePaginator ? $this->rows->total() : count($this->rows);
    }

    /** @return list<list<string>> the rows as text, in column order */
    public function textRows(): array
    {
        return array_map(
            fn (array $row) => array_map(fn (string $key) => (string) ($row[$key] ?? ''), array_keys($this->columns)),
            $this->rowList()
        );
    }
}
