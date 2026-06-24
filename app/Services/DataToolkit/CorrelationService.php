<?php

namespace App\Services\DataToolkit;

class CorrelationService
{
    public function __construct(private readonly StatisticsService $statistics) {}

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @return array<string, mixed>
     */
    public function matrix(array $rows, array $numericColumns): array
    {
        $matrix = [];
        $pairs = [];

        foreach ($numericColumns as $xColumn) {
            $matrix[$xColumn] = [];

            foreach ($numericColumns as $yColumn) {
                $r = $xColumn === $yColumn ? 1.0 : $this->pearsonForRows($rows, $xColumn, $yColumn);
                $interpretation = $r === null ? null : $this->interpret($r);

                $matrix[$xColumn][$yColumn] = [
                    'value' => $r === null ? null : round($r, 3),
                    'strength' => $interpretation['strength'] ?? 'not enough data',
                    'direction' => $interpretation['direction'] ?? 'none',
                ];

                if ($xColumn !== $yColumn && $r !== null) {
                    $pairKey = collect([$xColumn, $yColumn])->sort()->implode('|');
                    $pairs[$pairKey] = [
                        'x' => $xColumn,
                        'y' => $yColumn,
                        'value' => round($r, 3),
                        'absolute' => abs($r),
                        'strength' => $interpretation['strength'],
                        'direction' => $interpretation['direction'],
                        'interpretation' => $interpretation['message'],
                    ];
                }
            }
        }

        $strongest = collect($pairs)->sortByDesc('absolute')->first();

        return [
            'columns' => $numericColumns,
            'matrix' => $matrix,
            'strongest_pair' => $strongest,
            'interpretation' => $strongest
                ? "The strongest relationship found is between {$strongest['x']} and {$strongest['y']} ({$strongest['strength']} {$strongest['direction']} correlation)."
                : 'At least two numeric columns are needed to calculate correlation.',
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function pearsonForRows(array $rows, string $xColumn, string $yColumn): ?float
    {
        $xValues = [];
        $yValues = [];

        foreach ($rows as $row) {
            $x = $row[$xColumn] ?? null;
            $y = $row[$yColumn] ?? null;

            if ($this->statistics->isMissing($x) || $this->statistics->isMissing($y) || ! is_numeric($x) || ! is_numeric($y)) {
                continue;
            }

            $xValues[] = (float) $x;
            $yValues[] = (float) $y;
        }

        return $this->pearson($xValues, $yValues);
    }

    /**
     * @param array<int, float> $xValues
     * @param array<int, float> $yValues
     */
    public function pearson(array $xValues, array $yValues): ?float
    {
        $count = min(count($xValues), count($yValues));

        if ($count < 2) {
            return null;
        }

        $xValues = array_slice($xValues, 0, $count);
        $yValues = array_slice($yValues, 0, $count);
        $xMean = array_sum($xValues) / $count;
        $yMean = array_sum($yValues) / $count;
        $numerator = 0.0;
        $xSumSquares = 0.0;
        $ySumSquares = 0.0;

        for ($i = 0; $i < $count; $i++) {
            $xDiff = $xValues[$i] - $xMean;
            $yDiff = $yValues[$i] - $yMean;
            $numerator += $xDiff * $yDiff;
            $xSumSquares += $xDiff ** 2;
            $ySumSquares += $yDiff ** 2;
        }

        $denominator = sqrt($xSumSquares * $ySumSquares);

        if ($denominator == 0.0) {
            return null;
        }

        return $numerator / $denominator;
    }

    /**
     * @return array<string, string>
     */
    public function interpret(float $r): array
    {
        $absolute = abs($r);
        $direction = $r > 0 ? 'positive' : ($r < 0 ? 'negative' : 'none');

        $strength = match (true) {
            $absolute >= 0.90 => 'very strong',
            $absolute >= 0.70 => 'strong',
            $absolute >= 0.40 => 'moderate',
            $absolute >= 0.20 => 'weak',
            default => 'very weak',
        };

        $message = $direction === 'positive'
            ? "As one value increases, the other tends to increase too. The relationship is {$strength}."
            : ($direction === 'negative'
                ? "As one value increases, the other tends to decrease. The relationship is {$strength}."
                : 'There is no clear direction between the two values.');

        return compact('strength', 'direction', 'message');
    }
}
