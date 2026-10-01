<?php

namespace App\Support\Reports;

/**
 * Low / Moderate / High performance groups and the at-risk numbers of Class
 * Analytics, read from config/class_analytics.php (DataSensei Updates 12).
 *
 * The values come from configuration, never from the page. A value that is
 * missing, not a number or out of range falls back to the agreed default, so
 * a typo in .env cannot hide every student or flag the whole class.
 */
final class PerformanceBands
{
    public const LOW = 'low';

    public const MODERATE = 'moderate';

    public const HIGH = 'high';

    public const NOT_GRADED = 'not_graded';

    public const DEFAULT_LOW_BELOW = 70;

    public const DEFAULT_HIGH_FROM = 90;

    public const LABELS = [
        self::LOW => 'Low',
        self::MODERATE => 'Moderate',
        self::HIGH => 'High',
        self::NOT_GRADED => 'Not yet graded',
    ];

    /** Colour of each group in the bar graph (report bar tones). */
    public const TONES = [
        self::LOW => 'bad',
        self::MODERATE => 'warn',
        self::HIGH => 'good',
        self::NOT_GRADED => null,
    ];

    private const AT_RISK_DEFAULTS = [
        'missing_assessments' => [2, 1, 100],
        'challenge_average_below' => [70, 1, 100],
        'failed_attempts' => [3, 2, 100],
        'module_completion_below' => [25, 1, 100],
        'module_grace_days' => [7, 0, 365],
    ];

    /** Scores below this are Low. */
    public static function lowBelow(): float
    {
        return self::thresholds()[0];
    }

    /** Scores at or above this are High. */
    public static function highFrom(): float
    {
        return self::thresholds()[1];
    }

    /** The group of an assessment average; NOT_GRADED when there is none. */
    public static function groupOf(?float $average): string
    {
        if ($average === null) {
            return self::NOT_GRADED;
        }

        [$low, $high] = self::thresholds();

        return match (true) {
            $average < $low => self::LOW,
            $average >= $high => self::HIGH,
            default => self::MODERATE,
        };
    }

    public static function label(string $group): string
    {
        return self::LABELS[$group] ?? self::LABELS[self::NOT_GRADED];
    }

    /** "Below 70%", "70% to 89.9%", "90% or more". */
    public static function rangeText(string $group): string
    {
        [$low, $high] = self::thresholds();

        return match ($group) {
            self::LOW => 'Below '.self::number($low).'%',
            self::MODERATE => self::number($low).'% to under '.self::number($high).'%',
            self::HIGH => self::number($high).'% or more',
            default => 'No graded assessment yet',
        };
    }

    /** One at-risk number from config, checked against its allowed range. */
    public static function atRisk(string $key): int
    {
        [$default, $min, $max] = self::AT_RISK_DEFAULTS[$key] ?? [0, 0, PHP_INT_MAX];
        $value = config('class_analytics.at_risk.'.$key);

        if (! is_numeric($value)) {
            return $default;
        }

        $value = (int) $value;

        return $value < $min || $value > $max ? $default : $value;
    }

    /** @return array{0: float, 1: float} */
    private static function thresholds(): array
    {
        $low = config('class_analytics.performance.low_below', self::DEFAULT_LOW_BELOW);
        $high = config('class_analytics.performance.high_from', self::DEFAULT_HIGH_FROM);

        if (! is_numeric($low) || ! is_numeric($high)) {
            return [(float) self::DEFAULT_LOW_BELOW, (float) self::DEFAULT_HIGH_FROM];
        }

        $low = (float) $low;
        $high = (float) $high;

        if ($low <= 0 || $high > 100 || $low >= $high) {
            return [(float) self::DEFAULT_LOW_BELOW, (float) self::DEFAULT_HIGH_FROM];
        }

        return [$low, $high];
    }

    private static function number(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }
}
