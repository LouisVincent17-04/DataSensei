<?php

namespace App\Services\DataToolkit;

class EdaAnalysisService
{
    public function __construct(private readonly StatisticsService $statistics) {}

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @param array<string, mixed> $correlation
     * @param array<string, mixed> $validationRules
     * @return array<string, mixed>
     */
    public function profile(
        array $rows,
        array $columns,
        array $numericColumns,
        array $categoricalColumns,
        array $correlation,
        array $validationRules = []
    ): array {
        $dataTypes = $this->dataTypes($rows, $columns, $numericColumns);
        $missing = $this->missingSummary($rows, $columns);
        $duplicates = $this->duplicateSummary($rows, $columns);
        $outliers = $this->outlierSummary($rows, $numericColumns);
        $invalidValues = $this->invalidValueSummary($rows, $validationRules);
        $categoryIssues = $this->categoryConsistency($rows, array_values(array_filter(
            $categoricalColumns,
            fn (string $column): bool => in_array(
                $dataTypes[$column]['type'] ?? '',
                ['Categorical', 'Boolean'],
                true
            )
        )));
        $typeIssues = array_values(array_filter(array_map(
            fn (array $profile): ?array => ($profile['invalid_count'] ?? 0) > 0
                ? [
                    'column' => $profile['column'],
                    'invalid_count' => $profile['invalid_count'],
                    'message' => "{$profile['invalid_count']} value(s) do not match the detected {$profile['type']} type.",
                ]
                : null,
            $dataTypes
        )));
        $histograms = [];
        $boxPlots = [];

        foreach ($numericColumns as $column) {
            $values = $this->statistics->numericValues($rows, $column);
            $histograms[$column] = $this->histogram($values);
            $boxPlots[$column] = $outliers['columns'][$column]['box_plot'] ?? null;
        }

        $rowCount = count($rows);
        $cellCount = max(1, $rowCount * max(1, count($columns)));
        $missingRate = $missing['total_missing'] / $cellCount;
        $duplicateRate = $rowCount > 0 ? $duplicates['duplicate_records'] / $rowCount : 0.0;
        $numericCellCount = max(1, $rowCount * max(1, count($numericColumns)));
        $categoricalCellCount = max(1, $rowCount * max(1, count($categoricalColumns)));
        $outlierRate = $outliers['total_outliers'] / $numericCellCount;
        $invalidCount = max(
            (int) $invalidValues['total_invalid'],
            (int) array_sum(array_column($typeIssues, 'invalid_count'))
        );
        $invalidRate = $invalidCount / $numericCellCount;
        $categoryRate = $categoryIssues['total_inconsistent'] / $categoricalCellCount;
        $qualityScore = max(0, min(100, 100 - (($missingRate * 0.30) + ($duplicateRate * 0.20) + ($outlierRate * 0.15) + ($invalidRate * 0.20) + ($categoryRate * 0.15)) * 100));
        $summaryInvalidValues = $invalidValues;
        $summaryInvalidValues['total_invalid'] = $invalidCount;

        return [
            'data_types' => $dataTypes,
            'numeric_columns' => $numericColumns,
            'categorical_columns' => $categoricalColumns,
            'missing' => $missing,
            'duplicates' => $duplicates,
            'type_issues' => $typeIssues,
            'invalid_values' => $invalidValues,
            'category_issues' => $categoryIssues,
            'outliers' => $outliers,
            'histograms' => $histograms,
            'box_plots' => array_filter($boxPlots),
            'correlation_heatmap' => $correlation,
            'quality_score' => round($qualityScore, 1),
            'summary' => $this->summary($missing, $duplicates, $outliers, $summaryInvalidValues, $categoryIssues, $qualityScore),
            'explanations' => $this->explanations(),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @param array<int, string> $numericColumns
     * @return array<string, array<string, mixed>>
     */
    public function dataTypes(array $rows, array $columns, array $numericColumns): array
    {
        $types = [];
        $rowCount = count($rows);

        foreach ($columns as $column) {
            $values = [];
            $missingCount = 0;

            foreach ($rows as $row) {
                $value = $row[$column] ?? null;

                if ($this->statistics->isMissing($value)) {
                    $missingCount++;
                    continue;
                }

                $values[] = $value;
            }

            $uniqueValues = [];
            foreach ($values as $value) {
                $uniqueValues[$this->normaliseValue($value)] = true;
            }

            $isNumeric = in_array($column, $numericColumns, true);
            $type = $this->inferType($column, $values, $isNumeric);
            $nonMissing = count($values);
            $invalidCount = $isNumeric
                ? count(array_filter($values, fn (mixed $value): bool => ! is_numeric($value)))
                : 0;

            $types[$column] = [
                'column' => $column,
                'type' => $type,
                'non_missing_count' => $nonMissing,
                'missing_count' => $missingCount,
                'missing_percent' => $rowCount > 0 ? round(($missingCount / $rowCount) * 100, 2) : 0,
                'unique_count' => count($uniqueValues),
                'unique_percent' => $nonMissing > 0 ? round((count($uniqueValues) / $nonMissing) * 100, 2) : 0,
                'invalid_count' => $invalidCount,
                'example' => $values[0] ?? null,
            ];
        }

        return $types;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<string, mixed>
     */
    public function missingSummary(array $rows, array $columns): array
    {
        $byColumn = [];
        $totalMissing = 0;
        $rowCount = count($rows);
        $rowsWithMissing = 0;

        foreach ($columns as $column) {
            $count = $this->statistics->missingCount($rows, $column);
            $totalMissing += $count;
            $byColumn[$column] = [
                'count' => $count,
                'percent' => $rowCount > 0 ? round(($count / $rowCount) * 100, 2) : 0,
            ];
        }

        foreach ($rows as $row) {
            foreach ($columns as $column) {
                if ($this->statistics->isMissing($row[$column] ?? null)) {
                    $rowsWithMissing++;
                    break;
                }
            }
        }

        return [
            'total_missing' => $totalMissing,
            'rows_with_missing' => $rowsWithMissing,
            'complete_rows' => max(0, $rowCount - $rowsWithMissing),
            'by_column' => $byColumn,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<string, mixed>
     */
    public function duplicateSummary(array $rows, array $columns): array
    {
        $seen = [];
        $counts = [];
        $duplicateRecords = 0;
        $samples = [];

        foreach ($rows as $index => $row) {
            $ordered = [];
            foreach ($columns as $column) {
                $ordered[$column] = $row[$column] ?? null;
            }

            $hash = hash('sha256', json_encode($ordered, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) ?: '');
            $counts[$hash] = ($counts[$hash] ?? 0) + 1;

            if (! isset($seen[$hash])) {
                $seen[$hash] = $index;
                continue;
            }

            $duplicateRecords++;

            if (count($samples) < 10) {
                $samples[] = [
                    'row_number' => $index + 1,
                    'first_row_number' => $seen[$hash] + 1,
                    'row' => $ordered,
                ];
            }
        }

        $duplicateGroups = count(array_filter($counts, fn (int $count): bool => $count > 1));

        return [
            'duplicate_records' => $duplicateRecords,
            'duplicate_groups' => $duplicateGroups,
            'unique_records' => max(0, count($rows) - $duplicateRecords),
            'samples' => $samples,
        ];
    }

    /**
     * Find values that conflict with dataset-specific numeric rules.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param array<string, mixed> $validationRules
     * @return array<string, mixed>
     */
    public function invalidValueSummary(array $rows, array $validationRules): array
    {
        $ranges = (array) ($validationRules['numeric_ranges'] ?? []);
        $byColumn = [];
        $totalInvalid = 0;

        foreach ($ranges as $column => $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $samples = [];
            $invalidCount = 0;
            foreach ($rows as $index => $row) {
                $value = $row[$column] ?? null;
                if ($this->statistics->isMissing($value)) {
                    continue;
                }

                $reason = null;
                if (! is_numeric($value)) {
                    $reason = 'Expected a number';
                } elseif (array_key_exists('min', $rule) && (float) $value < (float) $rule['min']) {
                    $reason = 'Below the minimum of ' . $rule['min'];
                } elseif (array_key_exists('max', $rule) && (float) $value > (float) $rule['max']) {
                    $reason = 'Above the maximum of ' . $rule['max'];
                }

                if ($reason === null) {
                    continue;
                }

                $invalidCount++;
                if (count($samples) < 10) {
                    $samples[] = [
                        'row_number' => $index + 1,
                        'value' => $value,
                        'reason' => $reason,
                    ];
                }
            }

            if ($invalidCount < 1) {
                continue;
            }

            $limits = [];
            if (array_key_exists('min', $rule)) {
                $limits[] = 'minimum ' . $rule['min'];
            }
            if (array_key_exists('max', $rule)) {
                $limits[] = 'maximum ' . $rule['max'];
            }

            $byColumn[(string) $column] = [
                'column' => (string) $column,
                'invalid_count' => $invalidCount,
                'sample_values' => $samples,
                'rule' => $rule,
                'explanation' => $invalidCount . ' value(s) conflict with the expected numeric range (' . implode(', ', $limits) . ').',
            ];
            $totalInvalid += $invalidCount;
        }

        return [
            'total_invalid' => $totalInvalid,
            'columns_with_invalid' => count($byColumn),
            'by_column' => $byColumn,
        ];
    }

    /**
     * Find categories that differ only by capitalization or extra spacing.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $categoricalColumns
     * @return array<string, mixed>
     */
    public function categoryConsistency(array $rows, array $categoricalColumns): array
    {
        $columns = [];
        $totalInconsistent = 0;

        foreach ($categoricalColumns as $column) {
            if ($this->isIdentifierColumn($column)) {
                continue;
            }

            $groups = [];
            foreach ($rows as $row) {
                $value = $row[$column] ?? null;
                if ($this->statistics->isMissing($value)) {
                    continue;
                }

                $display = preg_replace('/\s+/u', ' ', (string) $value) ?? (string) $value;
                $normalized = $this->normaliseCategory($display);
                $groups[$normalized][$display] = ($groups[$normalized][$display] ?? 0) + 1;
            }

            $issueGroups = [];
            $columnCount = 0;
            foreach ($groups as $variants) {
                if (count($variants) < 2) {
                    continue;
                }

                arsort($variants);
                $canonicalVariant = (string) array_key_first($variants);
                $canonical = trim($canonicalVariant);
                $affected = array_sum($variants) - (int) $variants[$canonicalVariant];
                $columnCount += $affected;
                $issueGroups[] = [
                    'canonical' => $canonical,
                    'variants' => array_map(
                        fn (string $variant, int $count): array => ['value' => $variant, 'count' => $count],
                        array_keys($variants),
                        array_values($variants)
                    ),
                    'affected_count' => $affected,
                ];
            }

            if ($columnCount < 1) {
                continue;
            }

            $columns[$column] = [
                'column' => $column,
                'inconsistent_count' => $columnCount,
                'groups' => $issueGroups,
            ];
            $totalInconsistent += $columnCount;
        }

        return [
            'total_inconsistent' => $totalInconsistent,
            'columns_with_issues' => count($columns),
            'columns' => $columns,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @return array<string, mixed>
     */
    public function outlierSummary(array $rows, array $numericColumns, ?string $selectedColumn = null): array
    {
        $columns = $selectedColumn ? array_values(array_intersect($numericColumns, [$selectedColumn])) : $numericColumns;
        $result = [];
        $totalOutliers = 0;

        foreach ($columns as $column) {
            $indexedValues = [];

            foreach ($rows as $index => $row) {
                $value = $row[$column] ?? null;
                if ($this->statistics->isMissing($value) || ! is_numeric($value)) {
                    continue;
                }

                $indexedValues[] = ['row_number' => $index + 1, 'value' => (float) $value];
            }

            $values = array_column($indexedValues, 'value');
            sort($values);
            $q1 = $this->statistics->percentile($values, 25);
            $median = $this->statistics->median($values);
            $q3 = $this->statistics->percentile($values, 75);
            $iqr = ($q1 !== null && $q3 !== null) ? $q3 - $q1 : null;
            $lowerFence = $iqr !== null ? $q1 - (1.5 * $iqr) : null;
            $upperFence = $iqr !== null ? $q3 + (1.5 * $iqr) : null;
            $outlierRows = [];
            $nonOutliers = [];

            foreach ($indexedValues as $item) {
                $isOutlier = $iqr !== null && $iqr > 0 && ($item['value'] < $lowerFence || $item['value'] > $upperFence);

                if ($isOutlier) {
                    $outlierRows[] = $item;
                } else {
                    $nonOutliers[] = $item['value'];
                }
            }

            $totalOutliers += count($outlierRows);
            $count = count($values);
            $minimum = $count > 0 ? min($values) : null;
            $maximum = $count > 0 ? max($values) : null;
            $whiskerMinimum = $nonOutliers !== [] ? min($nonOutliers) : $minimum;
            $whiskerMaximum = $nonOutliers !== [] ? max($nonOutliers) : $maximum;

            $result[$column] = [
                'column' => $column,
                'count' => $count,
                'outlier_count' => count($outlierRows),
                'outlier_percent' => $count > 0 ? round((count($outlierRows) / $count) * 100, 2) : 0,
                'q1' => $this->statistics->roundNumber($q1),
                'median' => $this->statistics->roundNumber($median),
                'q3' => $this->statistics->roundNumber($q3),
                'iqr' => $this->statistics->roundNumber($iqr),
                'lower_fence' => $this->statistics->roundNumber($lowerFence),
                'upper_fence' => $this->statistics->roundNumber($upperFence),
                'sample_outliers' => array_slice(array_map(fn (array $item): array => [
                    'row_number' => $item['row_number'],
                    'value' => $this->statistics->roundNumber($item['value']),
                ], $outlierRows), 0, 12),
                'interpretation' => $this->outlierInterpretation($column, count($outlierRows), $count),
                'box_plot' => [
                    'minimum' => $this->statistics->roundNumber($minimum),
                    'whisker_minimum' => $this->statistics->roundNumber($whiskerMinimum),
                    'q1' => $this->statistics->roundNumber($q1),
                    'median' => $this->statistics->roundNumber($median),
                    'q3' => $this->statistics->roundNumber($q3),
                    'whisker_maximum' => $this->statistics->roundNumber($whiskerMaximum),
                    'maximum' => $this->statistics->roundNumber($maximum),
                    'outliers' => array_slice(array_map(
                        fn (array $item): float|int|null => $this->statistics->roundNumber($item['value']),
                        $outlierRows
                    ), 0, 20),
                ],
            ];
        }

        return [
            'total_outliers' => $totalOutliers,
            'columns_with_outliers' => count(array_filter($result, fn (array $item): bool => $item['outlier_count'] > 0)),
            'columns' => $result,
        ];
    }

    /**
     * @param array<int, float> $values
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    public function histogram(array $values): array
    {
        if ($values === []) {
            return ['labels' => [], 'values' => []];
        }

        $minimum = min($values);
        $maximum = max($values);

        if ($minimum === $maximum) {
            return [
                'labels' => [(string) $this->statistics->roundNumber($minimum)],
                'values' => [count($values)],
            ];
        }

        $bins = max(5, min(10, (int) ceil(sqrt(count($values)))));
        $width = ($maximum - $minimum) / $bins;
        $counts = array_fill(0, $bins, 0);
        $labels = [];

        foreach ($values as $value) {
            $index = min($bins - 1, (int) floor(($value - $minimum) / $width));
            $counts[$index]++;
        }

        for ($i = 0; $i < $bins; $i++) {
            $start = $minimum + ($width * $i);
            $end = $start + $width;
            $labels[] = $this->statistics->roundNumber($start) . '–' . $this->statistics->roundNumber($end);
        }

        return ['labels' => $labels, 'values' => $counts];
    }

    /**
     * @param array<string, mixed> $missing
     * @param array<string, mixed> $duplicates
     * @param array<string, mixed> $outliers
     */
    private function summary(
        array $missing,
        array $duplicates,
        array $outliers,
        array $invalidValues,
        array $categoryIssues,
        float $qualityScore
    ): string
    {
        $issues = [];

        if ($missing['total_missing'] > 0) {
            $issues[] = number_format($missing['total_missing']) . ' missing cells';
        }

        if ($duplicates['duplicate_records'] > 0) {
            $issues[] = number_format($duplicates['duplicate_records']) . ' duplicate records';
        }

        if ($categoryIssues['total_inconsistent'] > 0) {
            $issues[] = number_format($categoryIssues['total_inconsistent']) . ' inconsistent category values';
        }

        if ($invalidValues['total_invalid'] > 0) {
            $issues[] = number_format($invalidValues['total_invalid']) . ' invalid values';
        }

        if ($outliers['total_outliers'] > 0) {
            $issues[] = number_format($outliers['total_outliers']) . ' possible outliers';
        }

        if ($issues === []) {
            return 'No missing values, duplicates, inconsistent categories, invalid values, or IQR-based outliers were detected. The dataset is ready for exploratory analysis, but domain validation is still recommended.';
        }

        return 'The automated profile detected ' . implode(', ', $issues) . '. The estimated data-quality score is ' . round($qualityScore, 1) . '%. Review these findings before drawing conclusions.';
    }

    /**
     * @param array<int, mixed> $values
     */
    private function inferType(string $column, array $values, bool $numeric): string
    {
        if ($values === []) {
            return 'Unknown';
        }

        $normalisedColumn = strtolower($column);
        if (str_ends_with($normalisedColumn, '_id') || str_ends_with($normalisedColumn, ' id') || $normalisedColumn === 'id') {
            return 'Identifier';
        }

        if ($numeric) {
            $allInteger = true;
            foreach ($values as $value) {
                if (! is_numeric($value) || (float) $value !== floor((float) $value)) {
                    $allInteger = false;
                    break;
                }
            }

            return $allInteger ? 'Integer' : 'Decimal';
        }

        $dateMatches = 0;
        $booleanMatches = 0;
        $booleanValues = ['yes', 'no', 'true', 'false', '0', '1'];

        foreach ($values as $value) {
            $string = trim((string) $value);
            if (preg_match('/^\d{4}-\d{2}(?:-\d{2})?$/', $string)) {
                $dateMatches++;
            }
            if (in_array(strtolower($string), $booleanValues, true)) {
                $booleanMatches++;
            }
        }

        if ($dateMatches / count($values) >= 0.80) {
            return 'Date';
        }

        if ($booleanMatches === count($values)) {
            return 'Boolean';
        }

        $unique = count(array_unique(array_map(fn ($value): string => $this->normaliseValue($value), $values)));

        return $unique <= max(20, (int) floor(count($values) * 0.20)) ? 'Categorical' : 'Text';
    }

    private function normaliseValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_float($value)) {
            return number_format($value, 10, '.', '');
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION) ?: '';
        }

        return trim((string) $value);
    }

    private function normaliseCategory(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);

        return function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);
    }

    private function isIdentifierColumn(string $column): bool
    {
        $normalized = strtolower(trim($column));

        return $normalized === 'id'
            || str_ends_with($normalized, '_id')
            || str_ends_with($normalized, ' id');
    }

    private function outlierInterpretation(string $column, int $outlierCount, int $count): string
    {
        if ($count === 0) {
            return "The {$column} column has no usable numeric values.";
        }

        if ($outlierCount === 0) {
            return "No IQR-based outliers were detected in {$column}.";
        }

        $percent = round(($outlierCount / $count) * 100, 1);

        return "{$outlierCount} value(s), or {$percent}% of usable records, fall outside the 1.5×IQR fences for {$column}. They may be valid extremes or data-quality issues.";
    }

    /**
     * @return array<string, string>
     */
    private function explanations(): array
    {
        return [
            'data_type' => 'A data type describes how a column should be interpreted, such as integer, decimal, date, category, identifier, or text.',
            'missing' => 'A missing value is blank or unavailable. Missingness can reduce sample size and may introduce bias when it follows a pattern.',
            'duplicates' => 'A duplicate record exactly repeats another row across all columns. Duplicates can inflate counts and distort averages.',
            'category_consistency' => 'Category labels that differ only by capitalization or extra spaces can split one real group into several artificial groups.',
            'invalid_values' => 'An invalid value conflicts with an expected numeric type or a reasonable range defined for this learning dataset.',
            'outliers' => 'An outlier is an unusually low or high value. This module flags values below Q1 − 1.5×IQR or above Q3 + 1.5×IQR.',
            'histogram' => 'A histogram groups numeric values into ranges so you can see the distribution, skewness, gaps, and clusters.',
            'box_plot' => 'A box plot shows the median, quartiles, typical range, and possible outliers of a numeric column.',
            'scatter' => 'A scatter plot compares two numeric variables. Direction and clustering can suggest a relationship, but not causation.',
            'correlation_heatmap' => 'A correlation heatmap uses color intensity to summarize positive and negative relationships among numeric columns.',
            'quality_score' => 'The data-quality score is a learning aid based on missing cells, duplicates, category consistency, invalid values, and IQR outliers. It is not a substitute for domain expertise.',
        ];
    }
}
