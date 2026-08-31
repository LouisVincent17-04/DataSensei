<?php

namespace App\Services\HybridMl;

use DateTimeImmutable;

class DatasetProfiler
{
    /**
     * @param array<int,string> $headers
     * @param array<int,array<string,mixed>> $rows
     * @return array<string,mixed>
     */
    public function profile(array $headers, array $rows): array
    {
        $columns = [];
        foreach ($headers as $header) {
            $values = array_column($rows, $header);
            $nonNull = array_values(array_filter($values, static fn ($value): bool => $value !== null && $value !== ''));
            $unique = array_values(array_unique(array_map(static fn ($value): string => is_scalar($value) ? (string) $value : json_encode($value), $nonNull)));
            $type = $this->detectType($nonNull);
            $numeric = array_values(array_filter($nonNull, 'is_numeric'));
            $consistency = $this->typeConsistency($nonNull, $type);

            $columns[$header] = [
                'name' => $header,
                'type' => $type,
                'nullable' => count($nonNull) < count($rows),
                'missing_count' => count($rows) - count($nonNull),
                'missing_percent' => count($rows) > 0 ? round(((count($rows) - count($nonNull)) / count($rows)) * 100, 2) : 0,
                'unique_count' => count($unique),
                'unique_percent' => count($rows) > 0 ? round((count($unique) / count($rows)) * 100, 2) : 0,
                'minimum' => $numeric !== [] ? min(array_map('floatval', $numeric)) : null,
                'maximum' => $numeric !== [] ? max(array_map('floatval', $numeric)) : null,
                'sample_values' => array_slice($unique, 0, 8),
                'type_consistency_percent' => $consistency['percent'],
                'inconsistent_type_count' => $consistency['inconsistent_count'],
                'has_mixed_types' => $consistency['has_mixed_types'],
                'is_identifier_like' => $this->looksLikeIdentifier($header, count($unique), count($rows)),
            ];
        }

        $target = $this->detectTarget($headers, $columns);
        $problemType = $this->detectProblemType($target, $columns, $rows);

        return [
            'headers' => $headers,
            'row_count' => count($rows),
            'column_count' => count($headers),
            'columns' => $columns,
            'numeric_columns' => array_keys(array_filter($columns, static fn (array $column): bool => in_array($column['type'], ['integer', 'decimal'], true))),
            'categorical_columns' => array_keys(array_filter($columns, static fn (array $column): bool => in_array($column['type'], ['boolean', 'categorical'], true))),
            'date_columns' => array_keys(array_filter($columns, static fn (array $column): bool => $column['type'] === 'date')),
            'text_columns' => array_keys(array_filter($columns, static fn (array $column): bool => $column['type'] === 'text')),
            'mixed_type_columns' => array_keys(array_filter($columns, static fn (array $column): bool => (bool) ($column['has_mixed_types'] ?? false))),
            'suggested_target' => $target,
            'detected_problem_type' => $problemType,
            'preview' => array_slice($rows, 0, max(5, (int) config('hybrid_ml.preview_rows', 20))),
        ];
    }

    /** @param array<int,mixed> $values */
    private function detectType(array $values): string
    {
        if ($values === []) {
            return 'empty';
        }

        $sample = array_slice($values, 0, 500);
        $integer = 0;
        $numeric = 0;
        $boolean = 0;
        $date = 0;

        foreach ($sample as $value) {
            $string = trim((string) $value);
            if (preg_match('/^-?\d+$/', $string) === 1) {
                $integer++;
                $numeric++;
            } elseif (is_numeric($value)) {
                $numeric++;
            }
            if (in_array(strtolower($string), ['true', 'false', 'yes', 'no', 'y', 'n', '0', '1'], true)) {
                $boolean++;
            }
            if ($this->looksLikeDate($string)) {
                $date++;
            }
        }

        $threshold = max(1, (int) floor(count($sample) * 0.90));
        if ($boolean >= $threshold) {
            return 'boolean';
        }
        if ($integer >= $threshold) {
            return 'integer';
        }
        if ($numeric >= $threshold) {
            return 'decimal';
        }
        if ($date >= $threshold) {
            return 'date';
        }

        $unique = count(array_unique(array_map('strval', $sample)));
        return $unique <= min(50, max(20, (int) floor(count($sample) * 0.30))) ? 'categorical' : 'text';
    }

    /**
     * @param array<int,mixed> $values
     * @return array{percent:float,inconsistent_count:int,has_mixed_types:bool}
     */
    private function typeConsistency(array $values, string $detectedType): array
    {
        if ($values === []) {
            return ['percent' => 100.0, 'inconsistent_count' => 0, 'has_mixed_types' => false];
        }

        $total = count($values);
        $matches = 0;
        $numericCount = 0;
        foreach ($values as $value) {
            $string = trim((string) $value);
            if (is_numeric($value)) {
                $numericCount++;
            }

            $matches += match ($detectedType) {
                'integer' => preg_match('/^-?\d+$/', $string) === 1 ? 1 : 0,
                'decimal' => is_numeric($value) ? 1 : 0,
                'boolean' => in_array(strtolower($string), ['true', 'false', 'yes', 'no', 'y', 'n', '0', '1'], true) ? 1 : 0,
                'date' => $this->looksLikeDate($string) ? 1 : 0,
                default => 1,
            };
        }

        // A categorical/text column containing a meaningful mixture of numeric and
        // non-numeric scalar values is not rejected, but is reported for review.
        if (in_array($detectedType, ['categorical', 'text'], true)) {
            $numericRatio = $numericCount / max(1, $total);
            if ($numericRatio >= 0.10 && $numericRatio <= 0.90) {
                $matches = max($numericCount, $total - $numericCount);
            }
        }

        $inconsistent = max(0, $total - $matches);
        $percent = round(($matches / max(1, $total)) * 100, 2);

        return [
            'percent' => $percent,
            'inconsistent_count' => $inconsistent,
            'has_mixed_types' => $inconsistent > 0,
        ];
    }

    private function looksLikeDate(string $value): bool
    {
        if ($value === '' || is_numeric($value)) {
            return false;
        }
        if (preg_match('/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}(?:[ T].*)?$/', $value) !== 1
            && preg_match('/^\d{1,2}[-\/]\d{1,2}[-\/]\d{2,4}(?:[ T].*)?$/', $value) !== 1) {
            return false;
        }

        try {
            new DateTimeImmutable($value);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @param array<int,string> $headers
     * @param array<string,array<string,mixed>> $columns
     */
    private function detectTarget(array $headers, array $columns): ?string
    {
        $exact = [
            'target', 'label', 'class', 'outcome', 'result', 'prediction', 'survived',
            'churn', 'churned', 'diagnosis', 'quality', 'quality_label', 'final_score',
            'final_grade', 'price', 'sales', 'revenue', 'progression', 'has_disease',
        ];

        foreach ($headers as $header) {
            $normalized = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $header) ?? $header);
            if (in_array(trim($normalized, '_'), $exact, true)) {
                return $header;
            }
        }

        for ($index = count($headers) - 1; $index >= 0; $index--) {
            $header = $headers[$index];
            $column = $columns[$header] ?? [];
            if (($column['type'] ?? 'empty') !== 'empty' && ! ($column['is_identifier_like'] ?? false)) {
                return $header;
            }
        }

        return null;
    }

    /**
     * @param array<string,array<string,mixed>> $columns
     * @param array<int,array<string,mixed>> $rows
     */
    private function detectProblemType(?string $target, array $columns, array $rows): string
    {
        if ($target === null || ! isset($columns[$target])) {
            return 'clustering';
        }

        $column = $columns[$target];
        $type = (string) ($column['type'] ?? 'text');
        $unique = (int) ($column['unique_count'] ?? 0);
        $rowCount = max(1, count($rows));

        if (in_array($type, ['boolean', 'categorical', 'text'], true)) {
            return 'classification';
        }
        if ($unique >= 2 && $unique <= min(20, max(2, (int) floor($rowCount * 0.10)))) {
            return 'classification';
        }

        return 'regression';
    }

    private function looksLikeIdentifier(string $header, int $uniqueCount, int $rowCount): bool
    {
        $nameLooksLikeId = preg_match('/(^id$|_id$|^id_|identifier|uuid|record_number|student_number)/i', $header) === 1;
        return $nameLooksLikeId && $rowCount > 0 && $uniqueCount >= (int) floor($rowCount * 0.90);
    }
}
