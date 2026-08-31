<?php

namespace App\Services\DataToolkit;

class CategoricalAnalysisService
{
    public function __construct(private readonly StatisticsService $statistics) {}

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, string> $categoricalColumns
     * @return array<string, array<string, mixed>>
     */
    public function summarize(array $rows, array $categoricalColumns): array
    {
        $summary = [];

        foreach ($categoricalColumns as $column) {
            $summary[$column] = $this->summarizeColumn($rows, $column);
        }

        return $summary;
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array<string, mixed>
     */
    public function summarizeColumn(array $rows, string $column): array
    {
        $frequency = [];
        $missing = 0;
        $totalValid = 0;

        foreach ($rows as $row) {
            $value = $row[$column] ?? null;

            if ($this->statistics->isMissing($value)) {
                $missing++;
                continue;
            }

            $label = (string) $value;
            $frequency[$label] = ($frequency[$label] ?? 0) + 1;
            $totalValid++;
        }

        arsort($frequency);
        $uniqueCount = count($frequency);
        $displayFrequency = array_slice($frequency, 0, 50, true);

        $distribution = [];
        foreach ($displayFrequency as $label => $count) {
            $distribution[] = [
                'label' => $label,
                'count' => $count,
                'percent' => $totalValid > 0 ? round(($count / $totalValid) * 100, 1) : 0,
            ];
        }

        $mostFrequent = $distribution[0]['label'] ?? null;

        return [
            'column' => $column,
            'unique_count' => $uniqueCount,
            'missing_count' => $missing,
            'most_frequent' => $mostFrequent,
            'distribution' => $distribution,
            'distribution_truncated' => $uniqueCount > count($displayFrequency),
            'interpretation' => $this->interpret($column, $uniqueCount, $mostFrequent),
        ];
    }

    private function interpret(string $column, int $uniqueCount, ?string $mostFrequent): string
    {
        if ($uniqueCount === 0) {
            return "The {$column} column has no usable category values.";
        }

        if ($uniqueCount === 1) {
            return "The {$column} column has only one category, so it cannot separate groups well.";
        }

        return "The most common {$column} value is {$mostFrequent}. Use the distribution to compare groups.";
    }
}
