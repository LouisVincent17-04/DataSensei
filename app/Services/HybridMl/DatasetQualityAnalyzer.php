<?php

namespace App\Services\HybridMl;

class DatasetQualityAnalyzer
{
    /**
     * @param array<int,array<string,mixed>> $rows
     * @param array<string,mixed> $profile
     * @return array<string,mixed>
     */
    public function analyze(array $rows, array $profile, ?string $targetColumn = null, ?string $problemType = null): array
    {
        $rowCount = count($rows);
        $columns = (array) ($profile['columns'] ?? []);
        $targetColumn ??= $profile['suggested_target'] ?? null;
        $problemType ??= $profile['detected_problem_type'] ?? 'clustering';

        $missingTotal = 0;
        $emptyColumns = [];
        $constantColumns = [];
        $highCardinalityColumns = [];
        $mixedTypeColumns = [];
        $outlierCounts = [];
        $skewness = [];
        $columnAnalysis = [];

        foreach ($columns as $name => $column) {
            $missing = (int) ($column['missing_count'] ?? 0);
            $unique = (int) ($column['unique_count'] ?? 0);
            $type = (string) ($column['type'] ?? 'text');
            $missingTotal += $missing;

            if ($unique === 0) {
                $emptyColumns[] = $name;
            } elseif ($unique === 1) {
                $constantColumns[] = $name;
            }

            if (in_array($type, ['categorical', 'text'], true)
                && $unique > 50
                && $rowCount > 0
                && ($unique / $rowCount) > 0.25) {
                $highCardinalityColumns[] = $name;
            }

            if ((bool) ($column['has_mixed_types'] ?? false)) {
                $mixedTypeColumns[] = [
                    'column' => $name,
                    'detected_type' => $type,
                    'inconsistent_count' => (int) ($column['inconsistent_type_count'] ?? 0),
                    'consistency_percent' => (float) ($column['type_consistency_percent'] ?? 0),
                ];
            }

            $outliers = 0;
            $skew = null;
            if (in_array($type, ['integer', 'decimal'], true)) {
                $values = array_values(array_filter(array_column($rows, $name), 'is_numeric'));
                $numeric = array_map('floatval', $values);
                if (count($numeric) >= 8) {
                    sort($numeric);
                    $q1 = $this->percentile($numeric, 0.25);
                    $q3 = $this->percentile($numeric, 0.75);
                    $iqr = $q3 - $q1;
                    if ($iqr > 0) {
                        $lower = $q1 - (1.5 * $iqr);
                        $upper = $q3 + (1.5 * $iqr);
                        $outliers = count(array_filter($numeric, static fn (float $value): bool => $value < $lower || $value > $upper));
                    }
                    $skew = $this->skewness($numeric);
                }
            }

            $outlierCounts[$name] = $outliers;
            $skewness[$name] = $skew;
            $columnAnalysis[$name] = array_merge($column, [
                'outlier_count' => $outliers,
                'outlier_percent' => $rowCount > 0 ? round(($outliers / $rowCount) * 100, 2) : 0,
                'skewness' => $skew,
            ]);
        }

        $duplicates = $this->duplicateCount($rows);
        $totalCells = max(1, $rowCount * max(1, count($columns)));
        $missingPercent = round(($missingTotal / $totalCells) * 100, 2);
        $duplicatePercent = $rowCount > 0 ? round(($duplicates / $rowCount) * 100, 2) : 0;
        $totalOutliers = array_sum($outlierCounts);
        $numericCellCount = max(1, $rowCount * max(1, count((array) ($profile['numeric_columns'] ?? []))));
        $outlierPercent = round(($totalOutliers / $numericCellCount) * 100, 2);
        $correlations = $this->correlations($rows, (array) ($profile['numeric_columns'] ?? []));
        $classBalance = $this->classBalance($rows, $targetColumn, $problemType);

        $score = 100.0;
        $score -= min(30, $missingPercent * 0.75);
        $score -= min(15, $duplicatePercent * 0.60);
        $score -= min(20, count($emptyColumns) * 8);
        $score -= min(15, count($constantColumns) * 4);
        $score -= min(10, count($highCardinalityColumns) * 2.5);
        $score -= min(12, count($mixedTypeColumns) * 3);
        $score -= min(15, $outlierPercent * 0.45);
        if (($classBalance['imbalance_ratio'] ?? null) !== null) {
            $ratio = (float) $classBalance['imbalance_ratio'];
            if ($ratio < 0.50) {
                $score -= min(15, (0.50 - $ratio) * 30);
            }
        }
        $score = round(max(0, min(100, $score)), 2);

        $recommendations = $this->recommendations(
            $missingPercent,
            $duplicates,
            $emptyColumns,
            $constantColumns,
            $highCardinalityColumns,
            $mixedTypeColumns,
            $outlierPercent,
            $classBalance,
            $correlations,
            $skewness,
            $rowCount,
            $problemType
        );

        return [
            'quality_score' => $score,
            'grade' => $this->grade($score),
            'summary' => [
                'rows' => $rowCount,
                'columns' => count($columns),
                'missing_values' => $missingTotal,
                'missing_percent' => $missingPercent,
                'duplicate_rows' => $duplicates,
                'duplicate_percent' => $duplicatePercent,
                'empty_columns' => $emptyColumns,
                'constant_columns' => $constantColumns,
                'high_cardinality_columns' => $highCardinalityColumns,
                'mixed_type_columns' => $mixedTypeColumns,
                'outliers' => $totalOutliers,
                'outlier_percent' => $outlierPercent,
                'target_column' => $targetColumn,
                'problem_type' => $problemType,
                'class_balance' => $classBalance,
                'high_correlations' => $correlations,
                'recommended_algorithms' => $this->recommendedAlgorithms($problemType, $rowCount, $classBalance, $correlations),
            ],
            'column_analysis' => $columnAnalysis,
            'recommendations' => $recommendations,
        ];
    }

    /** @param array<int,array<string,mixed>> $rows */
    private function duplicateCount(array $rows): int
    {
        $seen = [];
        $duplicates = 0;
        foreach ($rows as $row) {
            $key = hash('sha256', json_encode($row, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION));
            if (isset($seen[$key])) {
                $duplicates++;
            } else {
                $seen[$key] = true;
            }
        }
        return $duplicates;
    }

    /** @param array<int,float> $values */
    private function percentile(array $values, float $percentile): float
    {
        $count = count($values);
        if ($count === 0) {
            return 0.0;
        }
        $position = ($count - 1) * $percentile;
        $lower = (int) floor($position);
        $upper = (int) ceil($position);
        if ($lower === $upper) {
            return (float) $values[$lower];
        }
        $weight = $position - $lower;
        return (float) (($values[$lower] * (1 - $weight)) + ($values[$upper] * $weight));
    }

    /** @param array<int,float> $values */
    private function skewness(array $values): ?float
    {
        $count = count($values);
        if ($count < 3) {
            return null;
        }
        $mean = array_sum($values) / $count;
        $variance = array_sum(array_map(static fn (float $value): float => ($value - $mean) ** 2, $values)) / $count;
        if ($variance <= 0) {
            return 0.0;
        }
        $std = sqrt($variance);
        $third = array_sum(array_map(static fn (float $value): float => (($value - $mean) / $std) ** 3, $values)) / $count;
        return round($third, 4);
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @param array<int,string> $numericColumns
     * @return array<int,array<string,mixed>>
     */
    private function correlations(array $rows, array $numericColumns): array
    {
        $result = [];
        $numericColumns = array_slice($numericColumns, 0, 30);
        for ($i = 0; $i < count($numericColumns); $i++) {
            for ($j = $i + 1; $j < count($numericColumns); $j++) {
                $a = $numericColumns[$i];
                $b = $numericColumns[$j];
                $pairs = [];
                foreach ($rows as $row) {
                    if (is_numeric($row[$a] ?? null) && is_numeric($row[$b] ?? null)) {
                        $pairs[] = [(float) $row[$a], (float) $row[$b]];
                    }
                }
                if (count($pairs) < 5) {
                    continue;
                }
                $correlation = $this->pearson($pairs);
                if ($correlation !== null && abs($correlation) >= 0.85) {
                    $result[] = ['column_a' => $a, 'column_b' => $b, 'correlation' => round($correlation, 4)];
                }
            }
        }
        usort($result, static fn (array $left, array $right): int => abs($right['correlation']) <=> abs($left['correlation']));
        return array_slice($result, 0, 20);
    }

    /** @param array<int,array{0:float,1:float}> $pairs */
    private function pearson(array $pairs): ?float
    {
        $count = count($pairs);
        $meanA = array_sum(array_column($pairs, 0)) / $count;
        $meanB = array_sum(array_column($pairs, 1)) / $count;
        $numerator = 0.0;
        $denominatorA = 0.0;
        $denominatorB = 0.0;
        foreach ($pairs as [$a, $b]) {
            $da = $a - $meanA;
            $db = $b - $meanB;
            $numerator += $da * $db;
            $denominatorA += $da ** 2;
            $denominatorB += $db ** 2;
        }
        $denominator = sqrt($denominatorA * $denominatorB);
        return $denominator > 0 ? $numerator / $denominator : null;
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return array<string,mixed>
     */
    private function classBalance(array $rows, ?string $target, string $problemType): array
    {
        if ($problemType !== 'classification' || $target === null) {
            return ['applicable' => false, 'label' => 'Not applicable', 'counts' => []];
        }

        $counts = [];
        foreach ($rows as $row) {
            $value = $row[$target] ?? null;
            if ($value !== null && $value !== '') {
                $key = (string) $value;
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }
        }
        arsort($counts);
        if (count($counts) < 2) {
            return ['applicable' => true, 'label' => 'Invalid target', 'counts' => $counts, 'imbalance_ratio' => 0.0];
        }

        $largest = max($counts);
        $smallest = min($counts);
        $ratio = $largest > 0 ? $smallest / $largest : 0.0;
        $label = match (true) {
            $ratio >= 0.75 => 'Excellent',
            $ratio >= 0.50 => 'Good',
            $ratio >= 0.25 => 'Moderately imbalanced',
            default => 'Severely imbalanced',
        };

        return [
            'applicable' => true,
            'label' => $label,
            'counts' => $counts,
            'imbalance_ratio' => round($ratio, 4),
            'minority_count' => $smallest,
            'majority_count' => $largest,
        ];
    }

    /** @return array<int,string> */
    private function recommendations(
        float $missingPercent,
        int $duplicates,
        array $emptyColumns,
        array $constantColumns,
        array $highCardinalityColumns,
        array $mixedTypeColumns,
        float $outlierPercent,
        array $classBalance,
        array $correlations,
        array $skewness,
        int $rows,
        string $problemType,
    ): array {
        $items = [];
        if ($missingPercent > 0) {
            $items[] = $missingPercent > 10
                ? 'Missing values are substantial. Compare imputation methods and consider removing columns with excessive missingness.'
                : 'Use median or mean imputation for numeric columns and most-frequent imputation for categorical columns.';
        }
        if ($duplicates > 0) {
            $items[] = "Remove or justify the {$duplicates} duplicate row(s) before final model evaluation.";
        }
        if ($emptyColumns !== []) {
            $items[] = 'Remove empty columns: '.implode(', ', array_slice($emptyColumns, 0, 8)).'.';
        }
        if ($constantColumns !== []) {
            $items[] = 'Remove constant columns because they contain no predictive variation: '.implode(', ', array_slice($constantColumns, 0, 8)).'.';
        }
        if ($highCardinalityColumns !== []) {
            $items[] = 'Review high-cardinality categorical columns and consider grouping rare values or excluding identifier-like fields.';
        }
        if ($mixedTypeColumns !== []) {
            $names = array_map(static fn (array $item): string => (string) ($item['column'] ?? ''), array_slice($mixedTypeColumns, 0, 8));
            $items[] = 'Standardize inconsistent data types before training in: '.implode(', ', array_filter($names)).'. Values that do not match the dominant type may be treated as missing.';
        }
        if ($outlierPercent > 2) {
            $items[] = 'Inspect outliers with box plots. Do not remove them automatically unless they are confirmed errors or outside the study scope.';
        }
        if (($classBalance['applicable'] ?? false) && (float) ($classBalance['imbalance_ratio'] ?? 1) < 0.50) {
            $items[] = 'Use stratified splitting and evaluate precision, recall, F1, ROC AUC, and precision-recall curves instead of accuracy alone.';
        }
        if ($correlations !== []) {
            $items[] = 'Highly correlated features were found. Linear models may benefit from removing redundant columns or applying regularization.';
        }
        if (count(array_filter($skewness, static fn ($value): bool => $value !== null && abs((float) $value) > 1.0)) > 0) {
            $items[] = 'Strongly skewed numeric columns may benefit from log or power transformations when their values permit it.';
        }
        if ($rows < 120) {
            $items[] = 'The dataset is small, so use cross-validation and keep model complexity low to reduce overfitting risk.';
        }
        if ($problemType === 'regression') {
            $items[] = 'Inspect residual plots after training. Patterns in residuals suggest missing nonlinear relationships or preprocessing issues.';
        }
        if ($items === []) {
            $items[] = 'The dataset has no major structural quality warning. Continue with a baseline model and cross-validation before tuning.';
        }
        return array_values(array_unique($items));
    }

    /** @return array<int,string> */
    private function recommendedAlgorithms(string $problemType, int $rows, array $classBalance, array $correlations): array
    {
        if ($problemType === 'classification') {
            $algorithms = ['logistic_regression', 'random_forest', 'decision_tree'];
            if ($rows <= 5000) {
                $algorithms[] = 'svm';
                $algorithms[] = 'knn';
            }
            if ((float) ($classBalance['imbalance_ratio'] ?? 1) < 0.50) {
                $algorithms = array_values(array_diff($algorithms, ['knn']));
            }
            return $algorithms;
        }
        if ($problemType === 'regression') {
            return $correlations !== []
                ? ['linear_regression', 'random_forest_regressor', 'decision_tree_regressor']
                : ['random_forest_regressor', 'linear_regression', 'decision_tree_regressor'];
        }
        return ['kmeans'];
    }

    private function grade(float $score): string
    {
        return match (true) {
            $score >= 90 => 'Excellent',
            $score >= 80 => 'Good',
            $score >= 70 => 'Acceptable',
            $score >= 55 => 'Needs cleaning',
            default => 'Poor',
        };
    }
}
