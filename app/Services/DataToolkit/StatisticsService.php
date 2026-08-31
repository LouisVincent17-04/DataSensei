<?php

namespace App\Services\DataToolkit;

class StatisticsService
{
    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $numericColumns
     * @return array<string, array<string, mixed>>
     */
    public function describe(array $rows, array $numericColumns): array
    {
        $summary = [];

        foreach ($numericColumns as $column) {
            $summary[$column] = $this->describeColumn($rows, $column);
        }

        return $summary;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function describeColumn(array $rows, string $column): array
    {
        $values = $this->numericValues($rows, $column);
        $missing = $this->missingCount($rows, $column);
        $count = count($values);

        if ($count === 0) {
            return [
                'column' => $column,
                'count' => 0,
                'missing_count' => $missing,
                'mean' => null,
                'median' => null,
                'mode' => null,
                'minimum' => null,
                'maximum' => null,
                'range' => null,
                'variance' => null,
                'standard_deviation' => null,
                'q1' => null,
                'q3' => null,
                'interquartile_range' => null,
                'interpretation' => 'This column has no usable numeric values for statistics.',
            ];
        }

        sort($values);

        $minimum = min($values);
        $maximum = max($values);
        $mean = array_sum($values) / $count;
        $variance = $this->variance($values, $mean);
        $standardDeviation = sqrt($variance);
        $q1 = $this->percentile($values, 25);
        $q3 = $this->percentile($values, 75);

        return [
            'column' => $column,
            'count' => $count,
            'missing_count' => $missing,
            'mean' => $this->roundNumber($mean),
            'median' => $this->roundNumber($this->median($values)),
            'mode' => $this->mode($values),
            'minimum' => $this->roundNumber($minimum),
            'maximum' => $this->roundNumber($maximum),
            'range' => $this->roundNumber($maximum - $minimum),
            'variance' => $this->roundNumber($variance),
            'standard_deviation' => $this->roundNumber($standardDeviation),
            'q1' => $this->roundNumber($q1),
            'q3' => $this->roundNumber($q3),
            'interquartile_range' => $this->roundNumber($q3 - $q1),
            'interpretation' => $this->interpretSpread($column, $mean, $standardDeviation),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, float>
     */
    public function numericValues(array $rows, string $column): array
    {
        $values = [];

        foreach ($rows as $row) {
            $value = $row[$column] ?? null;

            if ($this->isMissing($value) || ! is_numeric($value)) {
                continue;
            }

            $values[] = (float) $value;
        }

        return $values;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function missingCount(array $rows, string $column): int
    {
        $count = 0;

        foreach ($rows as $row) {
            if ($this->isMissing($row[$column] ?? null)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @param array<int, float> $values
     */
    public function median(array $values): ?float
    {
        $count = count($values);

        if ($count === 0) {
            return null;
        }

        sort($values);
        $middle = intdiv($count, 2);

        if ($count % 2 === 1) {
            return $values[$middle];
        }

        return ($values[$middle - 1] + $values[$middle]) / 2;
    }

    /**
     * @param array<int, float> $values
     */
    public function percentile(array $values, float $percentile): ?float
    {
        $count = count($values);

        if ($count === 0) {
            return null;
        }

        sort($values);
        $index = ($percentile / 100) * ($count - 1);
        $lower = (int) floor($index);
        $upper = (int) ceil($index);

        if ($lower === $upper) {
            return $values[$lower];
        }

        $weight = $index - $lower;

        return $values[$lower] * (1 - $weight) + $values[$upper] * $weight;
    }

    /**
     * @param array<int, float> $values
     */
    private function variance(array $values, float $mean): float
    {
        $count = count($values);

        if ($count === 0) {
            return 0.0;
        }

        $sum = 0.0;

        foreach ($values as $value) {
            $sum += ($value - $mean) ** 2;
        }

        return $sum / $count;
    }

    /**
     * @param array<int, float> $values
     */
    private function mode(array $values): float|int|string|null
    {
        if ($values === []) {
            return null;
        }

        $counts = [];

        foreach ($values as $value) {
            $key = (string) $value;
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        $highestFrequency = max($counts);

        // A value occurring only once is not a meaningful mode. Returning the
        // first value in an all-unique dataset made the UI report a false mode.
        if ($highestFrequency <= 1) {
            return null;
        }

        $modes = [];
        foreach ($counts as $value => $frequency) {
            if ($frequency === $highestFrequency) {
                $modes[] = $this->roundNumber((float) $value);
            }
        }

        return count($modes) === 1 ? $modes[0] : implode(', ', $modes);
    }

    public function isMissing(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    public function roundNumber(float|int|null $value, int $decimals = 2): float|int|null
    {
        if ($value === null) {
            return null;
        }

        $rounded = round((float) $value, $decimals);

        return floor($rounded) == $rounded ? (int) $rounded : $rounded;
    }

    private function interpretSpread(string $column, float $mean, float $standardDeviation): string
    {
        if ($mean == 0.0) {
            return "The {$column} values are centered near zero, so compare the raw values carefully.";
        }

        $ratio = abs($standardDeviation / $mean);

        if ($ratio < 0.10) {
            return "The {$column} values are tightly grouped around the average.";
        }

        if ($ratio < 0.25) {
            return "The {$column} values have a moderate amount of variation.";
        }

        return "The {$column} values vary widely, so individual records may differ a lot from the average.";
    }
}
