<?php

namespace App\Support\Reports;

use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

/**
 * Shared helpers for the reports and Class Analytics (DataSensei Updates 8):
 * how percentages, dates and durations are written, and paging of rows that
 * were put together in PHP.
 */
final class ReportFormat
{
    /** Assessments, assignments and challenges count as passed from here. */
    public const PASS_PERCENT = 70;

    public const PER_PAGE = 15;

    public const NONE = '—';

    public static function percent(float|int|null $part, float|int|null $whole): ?float
    {
        if ($part === null || $whole === null || (float) $whole <= 0) {
            return null;
        }

        return round(((float) $part / (float) $whole) * 100, 1);
    }

    public static function pct(float|int|null $value): string
    {
        if ($value === null) {
            return self::NONE;
        }

        $rounded = round((float) $value, 1);

        return (fmod($rounded, 1.0) === 0.0 ? number_format($rounded) : number_format($rounded, 1)).'%';
    }

    public static function number(float|int|null $value): string
    {
        return $value === null ? self::NONE : number_format((float) $value);
    }

    public static function date(mixed $value): string
    {
        return $value ? CarbonImmutable::parse($value)->format('M j, Y') : self::NONE;
    }

    public static function dateTime(mixed $value): string
    {
        return $value ? CarbonImmutable::parse($value)->format('M j, Y g:i A') : self::NONE;
    }

    public static function duration(float|int|null $seconds): string
    {
        if ($seconds === null || $seconds <= 0) {
            return self::NONE;
        }

        $seconds = (int) round($seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $rest = $seconds % 60;

        return $hours > 0 ? sprintf('%dh %02dm', $hours, $minutes) : ($minutes > 0 ? sprintf('%dm %02ds', $minutes, $rest) : $rest.'s');
    }

    /** Plain tone of a percentage against the pass mark. */
    public static function tone(?float $percent): ?string
    {
        if ($percent === null) {
            return null;
        }

        return $percent >= self::PASS_PERCENT ? 'good' : ($percent >= 50 ? 'warn' : 'bad');
    }

    /**
     * Rows put together in PHP, as one page (or all of them for a download).
     *
     * @param  Collection<int, array<string, mixed>>|array<int, array<string, mixed>>  $rows
     * @return array{0: LengthAwarePaginator|list<array<string, mixed>>, 1: bool}  rows and whether a download was cut short
     */
    public static function page(Collection|array $rows, string $name, bool $all): array
    {
        $rows = collect($rows)->values();

        if ($all) {
            return [$rows->take(ReportExporter::MAX_ROWS)->all(), $rows->count() > ReportExporter::MAX_ROWS];
        }

        $pageName = $name.'_page';
        $page = max(1, (int) Paginator::resolveCurrentPage($pageName));
        $lastPage = max(1, (int) ceil($rows->count() / self::PER_PAGE));
        $page = min($page, $lastPage);

        $paginator = new LengthAwarePaginator(
            $rows->forPage($page, self::PER_PAGE)->values()->all(),
            $rows->count(),
            self::PER_PAGE,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => $pageName]
        );

        return [$paginator->withQueryString(), false];
    }
}
