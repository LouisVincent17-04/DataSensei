<?php

namespace App\Services\DataToolkit;

use InvalidArgumentException;

class DataToolkitAnalysisService
{
    public function __construct(
        private readonly BuiltInDatasetService $datasets,
        private readonly StatisticsService $statistics,
        private readonly CategoricalAnalysisService $categorical,
        private readonly CorrelationService $correlation,
        private readonly RegressionService $regression,
        private readonly EdaAnalysisService $eda,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(string $datasetKey): array
    {
        return $this->overviewDataset($this->requireDataset($datasetKey));
    }

    /**
     * Build the same EDA profile for a predefined dataset or a validated,
     * user-scoped CSV working copy.
     *
     * @param array<string, mixed> $dataset
     * @return array<string, mixed>
     */
    public function overviewDataset(array $dataset): array
    {
        $rows = array_values((array) ($dataset['rows'] ?? []));
        $columns = array_values(array_map('strval', (array) ($dataset['columns'] ?? [])));
        $numericColumns = $this->numericColumns($rows, $columns);
        $categoricalColumns = array_values(array_diff($columns, $numericColumns));
        $missingValues = $this->missingValues($rows, $columns);
        $descriptive = $this->statistics->describe($rows, $numericColumns);
        $categoricalSummary = $this->categorical->summarize($rows, $categoricalColumns);
        // Pairwise correlation grows quadratically. Twenty-five variables still
        // provide a useful beginner view while keeping a 10,000-row upload responsive.
        $correlation = $this->correlation->matrix($rows, array_slice($numericColumns, 0, 25));
        $regression = $this->defaultRegression($dataset, $rows, $numericColumns);
        $eda = $this->eda->profile(
            $rows,
            $columns,
            $numericColumns,
            $categoricalColumns,
            $correlation,
            (array) ($dataset['validation_rules'] ?? [])
        );
        $currentSnapshot = $this->qualitySnapshot(count($rows), count($columns), $eda);
        $originalSnapshot = is_array($dataset['original_snapshot'] ?? null)
            ? $dataset['original_snapshot']
            : $currentSnapshot;
        $relationships = $this->topRelationships($correlation);
        $groupComparison = $this->groupComparison($dataset, $rows, $numericColumns, $categoricalColumns, $categoricalSummary);
        $featureSuggestions = $this->featureSuggestions($dataset, $columns, $numericColumns);
        $objective = trim((string) ($dataset['analysis_objective'] ?? $dataset['learning_objective'] ?? ''));
        $researchQuestions = $this->researchQuestions($dataset, $numericColumns, $categoricalColumns);

        return [
            'dataset' => $dataset,
            'row_count' => count($rows),
            'column_count' => count($columns),
            'columns' => $columns,
            'numeric_columns' => $numericColumns,
            'categorical_columns' => $categoricalColumns,
            'missing_values' => $missingValues,
            'preview' => array_slice($rows, 0, 10),
            'descriptive' => $descriptive,
            'categorical' => $categoricalSummary,
            'correlation' => $correlation,
            'regression' => $regression,
            'eda' => $eda,
            'objective' => $objective,
            'research_questions' => $researchQuestions,
            'relationships' => $relationships,
            'group_comparison' => $groupComparison,
            'feature_suggestions' => $featureSuggestions,
            'created_features' => array_values((array) ($dataset['created_features'] ?? [])),
            'eda_actions' => array_values((array) ($dataset['eda_actions'] ?? [])),
            'cleaning_history' => array_values((array) ($dataset['cleaning_history'] ?? [])),
            'original_snapshot' => $originalSnapshot,
            'current_snapshot' => $currentSnapshot,
            'key_findings' => $this->keyFindings(
                $dataset,
                $descriptive,
                $categoricalSummary,
                $eda,
                $relationships
            ),
            'charts' => $this->chartPayload($dataset, $rows, $numericColumns, $categoricalColumns, $categoricalSummary, $regression),
            'explanations' => $this->explanations(),
        ];
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function analyze(string $datasetKey, array $options): array
    {
        return $this->analyzeOverview($this->overview($datasetKey), $options);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function analyzeDataset(array $dataset, array $options): array
    {
        return $this->analyzeOverview($this->overviewDataset($dataset), $options);
    }

    /**
     * @param array<string, mixed> $overview
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function analyzeOverview(array $overview, array $options): array
    {
        $rows = $overview['dataset']['rows'];
        $type = (string) ($options['analysis_type'] ?? 'descriptive');

        return match ($type) {
            'descriptive' => [
                'analysis_type' => 'descriptive',
                'result' => isset($options['numeric_column']) && $options['numeric_column']
                    ? [$options['numeric_column'] => $this->statistics->describeColumn($rows, (string) $options['numeric_column'])]
                    : $overview['descriptive'],
            ],
            'categorical' => [
                'analysis_type' => 'categorical',
                'result' => isset($options['categorical_column']) && $options['categorical_column']
                    ? [$options['categorical_column'] => $this->categorical->summarizeColumn($rows, (string) $options['categorical_column'])]
                    : $overview['categorical'],
            ],
            'correlation' => [
                'analysis_type' => 'correlation',
                'result' => $overview['correlation'],
            ],
            'regression' => [
                'analysis_type' => 'regression',
                'result' => $this->regression->simpleLinear($rows, (string) $options['x_column'], (string) $options['y_column']),
            ],
            'data_quality' => [
                'analysis_type' => 'data_quality',
                'result' => [
                    'quality_score' => $overview['eda']['quality_score'],
                    'summary' => $overview['eda']['summary'],
                    'data_types' => $overview['eda']['data_types'],
                    'missing' => $overview['eda']['missing'],
                    'duplicates' => $overview['eda']['duplicates'],
                    'category_issues' => $overview['eda']['category_issues'],
                    'invalid_values' => $overview['eda']['invalid_values'],
                ],
            ],
            'outliers' => [
                'analysis_type' => 'outliers',
                'result' => $this->eda->outlierSummary(
                    $rows,
                    $overview['numeric_columns'],
                    isset($options['numeric_column']) && $options['numeric_column'] ? (string) $options['numeric_column'] : null
                ),
            ],
            default => throw new InvalidArgumentException('Unsupported analysis type.'),
        };
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function report(string $datasetKey, array $options = []): array
    {
        return $this->reportOverview($this->overview($datasetKey), $options);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function reportDataset(array $dataset, array $options = []): array
    {
        return $this->reportOverview($this->overviewDataset($dataset), $options);
    }

    /**
     * @param array<string, mixed> $overview
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function reportOverview(array $overview, array $options = []): array
    {
        $rows = $overview['dataset']['rows'];
        $numericColumns = $overview['numeric_columns'];
        $xColumn = (string) ($options['x_column'] ?? ($overview['dataset']['suggested_x_column'] ?? ($numericColumns[0] ?? '')));
        $yColumn = (string) ($options['y_column'] ?? ($overview['dataset']['suggested_y_column'] ?? ($numericColumns[1] ?? $xColumn)));

        if (! in_array($xColumn, $numericColumns, true) || ! in_array($yColumn, $numericColumns, true) || $xColumn === $yColumn) {
            $xColumn = $numericColumns[0] ?? '';
            $yColumn = $numericColumns[1] ?? $xColumn;
        }

        $overview['report_regression'] = ($xColumn && $yColumn && $xColumn !== $yColumn)
            ? $this->regression->simpleLinear($rows, $xColumn, $yColumn)
            : null;
        $overview['generated_at'] = now();

        return $overview;
    }

    /**
     * @return array<string, mixed>
     */
    private function requireDataset(string $datasetKey): array
    {
        $dataset = $this->datasets->find($datasetKey);

        if (! $dataset) {
            abort(404, 'Dataset not found.');
        }

        return $dataset;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<int, string>
     */
    public function numericColumns(array $rows, array $columns): array
    {
        $numericColumns = [];

        foreach ($columns as $column) {
            if ($this->isIdentifierColumn($column)) {
                continue;
            }

            $valid = 0;
            $numeric = 0;

            foreach ($rows as $row) {
                $value = $row[$column] ?? null;

                if ($this->statistics->isMissing($value)) {
                    continue;
                }

                $valid++;

                if (is_numeric($value)) {
                    $numeric++;
                }
            }

            if ($valid > 0 && ($numeric / $valid) >= 0.80) {
                $numericColumns[] = $column;
            }
        }

        return $numericColumns;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $columns
     * @return array<string, int>
     */
    private function missingValues(array $rows, array $columns): array
    {
        $missing = [];

        foreach ($columns as $column) {
            $missing[$column] = $this->statistics->missingCount($rows, $column);
        }

        return $missing;
    }

    private function isIdentifierColumn(string $column): bool
    {
        $normalized = strtolower(trim($column));

        return $normalized === 'id'
            || str_ends_with($normalized, '_id')
            || str_ends_with($normalized, ' id');
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @return array<int, string>
     */
    private function researchQuestions(array $dataset, array $numericColumns, array $categoricalColumns): array
    {
        $provided = array_values(array_filter(array_map('strval', (array) ($dataset['research_questions'] ?? []))));
        if ($provided !== []) {
            return array_slice($provided, 0, 4);
        }

        $questions = [];
        $primaryNumeric = (string) ($dataset['suggested_numeric_column'] ?? ($numericColumns[0] ?? ''));
        $xColumn = (string) ($dataset['suggested_x_column'] ?? ($numericColumns[0] ?? ''));
        $yColumn = (string) ($dataset['suggested_y_column'] ?? ($numericColumns[1] ?? ''));
        $category = (string) ($dataset['suggested_category_column'] ?? ($categoricalColumns[0] ?? ''));

        if ($primaryNumeric !== '') {
            $questions[] = "What is the typical {$primaryNumeric} value, and how widely does it vary?";
        }
        if ($category !== '' && $primaryNumeric !== '') {
            $questions[] = "How does {$primaryNumeric} differ across {$category} groups?";
        }
        if ($xColumn !== '' && $yColumn !== '' && $xColumn !== $yColumn) {
            $questions[] = "Is {$xColumn} related to {$yColumn}?";
        }
        $questions[] = 'Are there missing, duplicate, or unusual records that need attention?';

        return array_slice(array_values(array_unique($questions)), 0, 4);
    }

    /**
     * @param array<string, mixed> $correlation
     * @return array<int, array<string, mixed>>
     */
    private function topRelationships(array $correlation): array
    {
        $columns = array_values((array) ($correlation['columns'] ?? []));
        $relationships = [];

        for ($x = 0; $x < count($columns); $x++) {
            for ($y = $x + 1; $y < count($columns); $y++) {
                $xColumn = (string) $columns[$x];
                $yColumn = (string) $columns[$y];
                $cell = $correlation['matrix'][$xColumn][$yColumn] ?? null;
                $value = is_array($cell) ? ($cell['value'] ?? null) : null;
                if (! is_numeric($value)) {
                    continue;
                }

                $numericValue = (float) $value;
                $relationships[] = [
                    'x' => $xColumn,
                    'y' => $yColumn,
                    'value' => $numericValue,
                    'absolute' => abs($numericValue),
                    'strength' => (string) ($cell['strength'] ?? ''),
                    'direction' => (string) ($cell['direction'] ?? ''),
                    'finding' => $numericValue >= 0
                        ? "{$xColumn} and {$yColumn} tend to increase together."
                        : "As {$xColumn} increases, {$yColumn} tends to decrease.",
                ];
            }
        }

        usort($relationships, fn (array $a, array $b): int => $b['absolute'] <=> $a['absolute']);

        return array_slice($relationships, 0, 4);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @param array<string, array<string, mixed>> $categoricalSummary
     * @return array<string, mixed>|null
     */
    private function groupComparison(
        array $dataset,
        array $rows,
        array $numericColumns,
        array $categoricalColumns,
        array $categoricalSummary
    ): ?array {
        $category = (string) ($dataset['suggested_category_column'] ?? '');
        if (! in_array($category, $categoricalColumns, true)
            || ($categoricalSummary[$category]['unique_count'] ?? 0) > 10
            || ($categoricalSummary[$category]['unique_count'] ?? 0) < 2) {
            $category = '';
            foreach ($categoricalColumns as $candidate) {
                $unique = (int) ($categoricalSummary[$candidate]['unique_count'] ?? 0);
                if ($unique >= 2 && $unique <= 10 && ! $this->isIdentifierColumn($candidate)) {
                    $category = $candidate;
                    break;
                }
            }
        }

        $numeric = (string) ($dataset['suggested_numeric_column'] ?? ($numericColumns[0] ?? ''));
        if (! in_array($numeric, $numericColumns, true)) {
            $numeric = $numericColumns[0] ?? '';
        }
        if ($category === '' || $numeric === '') {
            return null;
        }

        $groups = [];
        foreach ($rows as $row) {
            $label = $row[$category] ?? null;
            $value = $row[$numeric] ?? null;
            if ($this->statistics->isMissing($label) || ! is_numeric($value)) {
                continue;
            }
            $label = (string) $label;
            $groups[$label][] = (float) $value;
        }

        $values = [];
        foreach ($groups as $label => $groupValues) {
            $values[] = [
                'label' => $label,
                'mean' => $this->statistics->roundNumber(array_sum($groupValues) / count($groupValues)),
                'count' => count($groupValues),
            ];
        }
        usort($values, fn (array $a, array $b): int => $b['mean'] <=> $a['mean']);

        return $values === [] ? null : [
            'category_column' => $category,
            'numeric_column' => $numeric,
            'groups' => $values,
            'finding' => "Compare the average {$numeric} across {$category} groups. Group differences describe this dataset but do not prove what caused them.",
        ];
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, string> $columns
     * @param array<int, string> $numericColumns
     * @return array<int, array<string, mixed>>
     */
    private function featureSuggestions(array $dataset, array $columns, array $numericColumns): array
    {
        $suggestions = array_values((array) ($dataset['feature_suggestions'] ?? []));
        $lowerColumns = [];
        foreach ($columns as $column) {
            $lowerColumns[strtolower($column)] = $column;
        }

        $scoreColumns = array_values(array_filter(
            $numericColumns,
            fn (string $column): bool => str_contains(strtolower($column), 'score')
        ));
        if (count($scoreColumns) >= 2 && ! in_array('average_score', $columns, true)) {
            $suggestions[] = [
                'key' => 'average-score',
                'name' => 'average_score',
                'formula' => 'average_score = mean(' . implode(', ', array_slice($scoreColumns, 0, 4)) . ')',
                'explanation' => 'Combines related score columns into one easy-to-compare average.',
                'operation' => 'mean',
                'columns' => array_slice($scoreColumns, 0, 4),
            ];
        }

        $quantity = $lowerColumns['units_sold'] ?? $lowerColumns['quantity'] ?? null;
        $price = $lowerColumns['unit_price'] ?? $lowerColumns['price'] ?? null;
        if ($quantity && $price && ! in_array('calculated_total', $columns, true)) {
            $suggestions[] = [
                'key' => 'calculated-total',
                'name' => 'calculated_total',
                'formula' => "calculated_total = {$quantity} × {$price}",
                'explanation' => 'Combines quantity and price into an estimated total value for each row.',
                'operation' => 'product',
                'columns' => [$quantity, $price],
            ];
        }

        $monthlySpend = $lowerColumns['monthly_spend'] ?? null;
        if ($monthlySpend && ! in_array('estimated_annual_spend', $columns, true)) {
            $suggestions[] = [
                'key' => 'estimated-annual-spend',
                'name' => 'estimated_annual_spend',
                'formula' => "estimated_annual_spend = {$monthlySpend} × 12",
                'explanation' => 'Converts monthly spending into a simple annual estimate for easier interpretation.',
                'operation' => 'scale',
                'columns' => [$monthlySpend],
                'factor' => 12,
            ];
        }

        $unique = [];
        foreach ($suggestions as $suggestion) {
            $key = (string) ($suggestion['key'] ?? '');
            $name = (string) ($suggestion['name'] ?? '');
            $sourceColumns = array_values((array) ($suggestion['columns'] ?? []));
            if ($key === '' || $name === '' || in_array($name, $columns, true) || array_diff($sourceColumns, $columns) !== []) {
                continue;
            }
            $unique[$key] = $suggestion;
        }

        return array_slice(array_values($unique), 0, 3);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<string, array<string, mixed>> $descriptive
     * @param array<string, array<string, mixed>> $categoricalSummary
     * @param array<string, mixed> $eda
     * @param array<int, array<string, mixed>> $relationships
     * @return array<int, string>
     */
    private function keyFindings(
        array $dataset,
        array $descriptive,
        array $categoricalSummary,
        array $eda,
        array $relationships
    ): array {
        $findings = [];
        $missing = (int) ($eda['missing']['total_missing'] ?? 0);
        $duplicates = (int) ($eda['duplicates']['duplicate_records'] ?? 0);
        $outliers = (int) ($eda['outliers']['total_outliers'] ?? 0);
        $categories = (int) ($eda['category_issues']['total_inconsistent'] ?? 0);
        $invalid = max(
            (int) ($eda['invalid_values']['total_invalid'] ?? 0),
            (int) array_sum(array_column((array) ($eda['type_issues'] ?? []), 'invalid_count'))
        );
        $findings[] = $missing === 0 && $duplicates === 0
            ? 'No missing values or duplicate rows are currently present.'
            : "The current data contains {$missing} missing value(s) and {$duplicates} duplicate row(s).";
        $findings[] = $outliers > 0
            ? "The automated IQR check found {$outliers} potential outlier value(s)."
            : 'No significant potential outliers were detected by the IQR check.';
        $findings[] = $categories === 0 && $invalid === 0
            ? 'Category labels and expected value ranges are currently consistent.'
            : "The current data contains {$categories} inconsistent category label(s) and {$invalid} invalid value(s).";

        $firstNumeric = collect($descriptive)->first(fn (array $item): bool => ($item['mean'] ?? null) !== null);
        if (is_array($firstNumeric)) {
            $findings[] = "The average {$firstNumeric['column']} is {$firstNumeric['mean']}, with a median of {$firstNumeric['median']}.";
        }

        $firstCategory = collect($categoricalSummary)->first(
            fn (array $item): bool => ($item['unique_count'] ?? 0) >= 2 && ($item['unique_count'] ?? 0) <= 20
        );
        if (is_array($firstCategory) && ($firstCategory['most_frequent'] ?? null) !== null) {
            $findings[] = "The most common {$firstCategory['column']} category is {$firstCategory['most_frequent']}.";
        }

        if (isset($relationships[0])) {
            $relationship = $relationships[0];
            $findings[] = "The strongest numeric relationship is {$relationship['strength']} and {$relationship['direction']} between {$relationship['x']} and {$relationship['y']} (r = {$relationship['value']}).";
        }

        $created = count((array) ($dataset['created_features'] ?? []));
        if ($created > 0) {
            $findings[] = "{$created} new feature(s) were created during this EDA.";
        }

        return array_slice($findings, 0, 6);
    }

    /**
     * @param array<string, mixed> $eda
     * @return array<string, int>
     */
    private function qualitySnapshot(int $rows, int $columns, array $eda): array
    {
        return [
            'rows' => $rows,
            'columns' => $columns,
            'missing_values' => (int) ($eda['missing']['total_missing'] ?? 0),
            'duplicate_rows' => (int) ($eda['duplicates']['duplicate_records'] ?? 0),
            'inconsistent_categories' => (int) ($eda['category_issues']['total_inconsistent'] ?? 0),
            'invalid_values' => max(
                (int) ($eda['invalid_values']['total_invalid'] ?? 0),
                (int) array_sum(array_column((array) ($eda['type_issues'] ?? []), 'invalid_count'))
            ),
            'potential_outliers' => (int) ($eda['outliers']['total_outliers'] ?? 0),
        ];
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @return array<string, mixed>|null
     */
    private function defaultRegression(array $dataset, array $rows, array $numericColumns): ?array
    {
        $xColumn = (string) ($dataset['suggested_x_column'] ?? ($numericColumns[0] ?? ''));
        $yColumn = (string) ($dataset['suggested_y_column'] ?? ($numericColumns[1] ?? ''));

        if (! in_array($xColumn, $numericColumns, true) || ! in_array($yColumn, $numericColumns, true) || $xColumn === $yColumn) {
            if (count($numericColumns) < 2) {
                return null;
            }

            $xColumn = $numericColumns[0];
            $yColumn = $numericColumns[1];
        }

        return $this->regression->simpleLinear($rows, $xColumn, $yColumn);
    }

    /**
     * @param array<string, mixed> $dataset
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @param array<int, string> $categoricalColumns
     * @param array<string, array<string, mixed>> $categoricalSummary
     * @param array<string, mixed>|null $regression
     * @return array<string, mixed>
     */
    private function chartPayload(array $dataset, array $rows, array $numericColumns, array $categoricalColumns, array $categoricalSummary, ?array $regression): array
    {
        $numericColumn = (string) ($dataset['suggested_numeric_column'] ?? ($numericColumns[0] ?? ''));
        $categoryColumn = (string) ($dataset['suggested_category_column'] ?? ($categoricalColumns[0] ?? ''));
        $numericValues = $numericColumn ? $this->statistics->numericValues($rows, $numericColumn) : [];
        $categoryDistribution = $categoryColumn && isset($categoricalSummary[$categoryColumn])
            ? $categoricalSummary[$categoryColumn]['distribution']
            : [];

        return [
            'numeric_column' => $numericColumn,
            'category_column' => $categoryColumn,
            'bar' => [
                'labels' => array_column($categoryDistribution, 'label'),
                'values' => array_column($categoryDistribution, 'count'),
            ],
            'line' => [
                'labels' => array_map(fn (int $i) => (string) ($i + 1), array_keys($numericValues)),
                'values' => $numericValues,
            ],
            'histogram' => $this->histogram($numericValues),
            'scatter' => [
                'x_column' => $regression['x_column'] ?? null,
                'y_column' => $regression['y_column'] ?? null,
                'points' => $regression['points'] ?? [],
            ],
        ];
    }

    /**
     * @param array<int, float> $values
     * @return array{labels: array<int, string>, values: array<int, int>}
     */
    private function histogram(array $values, int $bins = 5): array
    {
        if ($values === []) {
            return ['labels' => [], 'values' => []];
        }

        $minimum = min($values);
        $maximum = max($values);

        if ($minimum === $maximum) {
            return ['labels' => [(string) $this->statistics->roundNumber($minimum)], 'values' => [count($values)]];
        }

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
     * @return array<string, string>
     */
    private function explanations(): array
    {
        return [
            'mean' => 'Mean is the average value. It gives a quick center point for a numeric column.',
            'standard_deviation' => 'Standard deviation shows how spread out the values are from the average.',
            'correlation' => 'Correlation describes how two numeric columns move together. It does not automatically prove cause and effect.',
            'r_squared' => 'R-squared estimates how much of the Y pattern is explained by the X column in a simple regression line.',
            'missing_values' => 'Missing values are blank or unavailable entries. Too many missing values can weaken an analysis.',
            'chart' => 'Charts make patterns easier to see than raw rows alone.',
            'duplicates' => 'Duplicate records repeat an existing row and can inflate counts or distort results.',
            'outliers' => 'Outliers are unusually low or high observations. They should be investigated rather than automatically deleted.',
            'box_plot' => 'A box plot summarizes the median, quartiles, typical range, and possible outliers.',
            'heatmap' => 'A correlation heatmap uses color intensity to make relationships among numeric variables easier to compare.',
        ];
    }
}
