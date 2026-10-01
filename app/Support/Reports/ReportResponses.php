<?php

namespace App\Support\Reports;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * What the admin and instructor report controllers share (DataSensei
 * Updates 8): the filter description printed on exports, and the print,
 * PDF and CSV responses.
 */
final class ReportResponses
{
    /**
     * The filters in words, for printouts and downloads.
     *
     * @param  array<string, mixed>  $choices
     * @return list<string>
     */
    public function filterLines(ReportFilters $filters, array $choices): array
    {
        $lines = [];
        if ($range = $filters->rangeText()) {
            $lines[] = 'Dates: '.$range;
        }
        if ($filters->search !== '') {
            $lines[] = 'Search: '.$filters->search;
        }

        $named = [
            'Class' => [$filters->classId, $choices['classes'] ?? []],
            'Module' => [$filters->moduleId, $choices['modules'] ?? []],
            'Student' => [$filters->studentId, $choices['students'] ?? []],
            'Role' => [$filters->role, $choices['roles'] ?? []],
            ($choices['typeLabel'] ?? 'Type') => [$filters->type, $choices['types'] ?? []],
            'Status' => [$filters->status, $choices['statuses'] ?? []],
            'Action' => [$filters->action, $choices['actions'] ?? []],
        ];
        foreach ($named as $label => [$value, $options]) {
            if ($value !== null && $value !== '' && isset($options[$value])) {
                $lines[] = $label.': '.$options[$value];
            }
        }

        return $lines;
    }

    /** @param  list<string>  $filterLines */
    public function export(string $format, string $scope, string $key, ReportResult $result, array $filterLines, string $backUrl): Response
    {
        $exporter = app(ReportExporter::class);
        $by = (string) (Auth::user()?->name ?? 'DataSensei');

        return match ($format) {
            'csv' => $exporter->csv($result, $filterLines, $by, ReportExporter::filename($scope, $key, 'csv')),
            'pdf' => $exporter->pdf($result, $filterLines, $by, ReportExporter::filename($scope, $key, 'pdf')),
            default => response()->view('reports.print', [
                'result' => $result,
                'filterLines' => $filterLines,
                'generatedBy' => $by,
                'backUrl' => $backUrl,
            ]),
        };
    }
}
