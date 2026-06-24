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
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function overview(string $datasetKey): array
    {
        $dataset = $this->requireDataset($datasetKey);
        $rows = $dataset['rows'];
        $columns = $dataset['columns'];
        $numericColumns = $this->numericColumns($rows, $columns);
        $categoricalColumns = array_values(array_diff($columns, $numericColumns));
        $missingValues = $this->missingValues($rows, $columns);
        $descriptive = $this->statistics->describe($rows, $numericColumns);
        $categoricalSummary = $this->categorical->summarize($rows, $categoricalColumns);
        $correlation = $this->correlation->matrix($rows, $numericColumns);
        $regression = $this->defaultRegression($dataset, $rows, $numericColumns);

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
        $overview = $this->overview($datasetKey);
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
            default => throw new InvalidArgumentException('Unsupported analysis type.'),
        };
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function report(string $datasetKey, array $options = []): array
    {
        $overview = $this->overview($datasetKey);
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
        ];
    }
}
