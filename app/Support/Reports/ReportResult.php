<?php

namespace App\Support\Reports;

/**
 * A report ready to show, print or export (DataSensei Updates 8): its title,
 * the headline figures, optional simple bars, and its tables.
 */
final class ReportResult
{
    /**
     * @param  list<array{label: string, value: string, note?: string|null}>  $summary
     * @param  list<ReportTable>  $tables
     * @param  list<array{title: string, items: list<array{label: string, percent: float|int|null, text: string}>}>  $bars
     */
    public function __construct(
        public readonly string $key,
        public readonly string $title,
        public readonly string $description,
        public readonly array $summary = [],
        public readonly array $tables = [],
        public readonly array $bars = [],
        public readonly ?string $note = null,
    ) {
    }
}
