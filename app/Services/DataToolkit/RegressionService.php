<?php

namespace App\Services\DataToolkit;

class RegressionService
{
    public function __construct(private readonly StatisticsService $statistics) {}

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function simpleLinear(array $rows, string $xColumn, string $yColumn): array
    {
        $points = $this->pairedValues($rows, $xColumn, $yColumn);
        $count = count($points);

        if ($count < 2) {
            return [
                'x_column' => $xColumn,
                'y_column' => $yColumn,
                'count' => $count,
                'slope' => null,
                'intercept' => null,
                'r_squared' => null,
                'equation' => 'Not enough paired numeric data.',
                'sample_prediction' => null,
                'points' => $points,
                'interpretation' => 'Linear regression needs at least two rows where both selected columns are numeric.',
            ];
        }

        $xValues = array_column($points, 'x');
        $yValues = array_column($points, 'y');
        $xMean = array_sum($xValues) / $count;
        $yMean = array_sum($yValues) / $count;
        $numerator = 0.0;
        $denominator = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $xDiff = $xValues[$i] - $xMean;
            $numerator += $xDiff * ($yValues[$i] - $yMean);
            $denominator += $xDiff ** 2;
        }

        if ($denominator == 0.0) {
            return [
                'x_column' => $xColumn,
                'y_column' => $yColumn,
                'count' => $count,
                'slope' => null,
                'intercept' => null,
                'r_squared' => null,
                'equation' => 'Regression cannot be calculated because the X values do not vary.',
                'sample_prediction' => null,
                'points' => $points,
                'interpretation' => 'Choose an X column with changing numeric values.',
            ];
        }

        $slope = $numerator / $denominator;
        $intercept = $yMean - ($slope * $xMean);
        $ssTotal = 0.0;
        $ssResidual = 0.0;

        foreach ($points as $point) {
            $predicted = $this->predict((float) $point['x'], $slope, $intercept);
            $ssTotal += (((float) $point['y']) - $yMean) ** 2;
            $ssResidual += (((float) $point['y']) - $predicted) ** 2;
        }

        $rSquared = $ssTotal == 0.0 ? null : 1 - ($ssResidual / $ssTotal);
        $sampleX = $xMean;
        $sampleY = $this->predict($sampleX, $slope, $intercept);
        $roundedSlope = $this->statistics->roundNumber($slope);
        $roundedIntercept = $this->statistics->roundNumber($intercept);

        return [
            'x_column' => $xColumn,
            'y_column' => $yColumn,
            'count' => $count,
            'slope' => $roundedSlope,
            'intercept' => $roundedIntercept,
            'r_squared' => $rSquared === null ? null : $this->statistics->roundNumber(max(0, min(1, $rSquared)), 3),
            'equation' => "{$yColumn} = {$roundedIntercept} + ({$roundedSlope} × {$xColumn})",
            'sample_prediction' => [
                'x' => $this->statistics->roundNumber($sampleX),
                'y' => $this->statistics->roundNumber($sampleY),
            ],
            'points' => $points,
            'interpretation' => $this->interpret($xColumn, $yColumn, $slope, $rSquared),
        ];
    }

    public function predict(float $x, float $slope, float $intercept): float
    {
        return $intercept + ($slope * $x);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array{x: float, y: float}>
     */
    private function pairedValues(array $rows, string $xColumn, string $yColumn): array
    {
        $points = [];

        foreach ($rows as $row) {
            $x = $row[$xColumn] ?? null;
            $y = $row[$yColumn] ?? null;

            if ($this->statistics->isMissing($x) || $this->statistics->isMissing($y) || ! is_numeric($x) || ! is_numeric($y)) {
                continue;
            }

            $points[] = ['x' => (float) $x, 'y' => (float) $y];
        }

        return $points;
    }

    private function interpret(string $xColumn, string $yColumn, float $slope, ?float $rSquared): string
    {
        $direction = $slope >= 0 ? 'increases' : 'decreases';
        $fit = match (true) {
            $rSquared === null => 'The model fit cannot be judged because the Y values do not vary.',
            $rSquared >= 0.75 => 'The line explains a large part of the pattern.',
            $rSquared >= 0.40 => 'The line explains a moderate part of the pattern.',
            default => 'The line explains only a small part of the pattern.',
        };

        return "For every 1-unit increase in {$xColumn}, {$yColumn} usually {$direction} by about " . abs(round($slope, 2)) . ". {$fit}";
    }
}
