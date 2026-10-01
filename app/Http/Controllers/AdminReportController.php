<?php

namespace App\Http\Controllers;

use App\Services\Reports\AdminReports;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportResponses;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Reports (DataSensei Updates 8): Users, Modules, Classes, Assessments,
 * Challenges & Coding Challenges, Gamification and Audit Logs, each with its
 * own filters, print, PDF and CSV. System-wide; the admin middleware on the
 * route group keeps everyone else out.
 */
class AdminReportController extends Controller
{
    public function __construct(
        private readonly AdminReports $reports,
        private readonly ReportResponses $responses,
    ) {
    }

    public function index(Request $request): View
    {
        return $this->show($request, 'users');
    }

    public function show(Request $request, string $report): View
    {
        abort_unless(isset(AdminReports::REPORTS[$report]), 404);
        $filters = ReportFilters::fromRequest($request);

        return view('admin.reports.show', [
            'catalog' => AdminReports::REPORTS,
            'current' => $report,
            'result' => $this->reports->build($report, $filters),
            'filters' => $filters,
            'choices' => $this->choices($report),
            'searchHint' => $this->searchHint($report),
        ]);
    }

    public function export(Request $request, string $report, string $format): Response
    {
        abort_unless(isset(AdminReports::REPORTS[$report]) && in_array($format, ['csv', 'pdf', 'print'], true), 404);
        $filters = ReportFilters::fromRequest($request);
        $result = $this->reports->build($report, $filters, true);

        return $this->responses->export(
            $format,
            'admin',
            $report,
            $result,
            $this->responses->filterLines($filters, $this->choices($report)),
            route('admin.reports.show', ['report' => $report] + $filters->query()),
        );
    }

    /** @return array<string, mixed> */
    private function choices(string $report): array
    {
        $options = $this->reports->options($report);
        $roleNames = array_combine(array_keys(AdminReports::ROLES), array_map(fn (int $role) => AdminReports::ROLE_NAMES[$role], AdminReports::ROLES));

        return match ($report) {
            'users' => ['roles' => $roleNames, 'statuses' => AdminReports::STATUSES['users']],
            'modules' => ['types' => AdminReports::TYPES['modules'], 'statuses' => AdminReports::STATUSES['modules']],
            'classes' => ['statuses' => AdminReports::STATUSES['classes']],
            'assessments' => ['classes' => $options['classes'], 'statuses' => AdminReports::STATUSES['assessments']],
            'challenges' => ['modules' => $options['modules'], 'types' => AdminReports::TYPES['challenges'], 'typeLabel' => 'Made by'],
            'audit' => ['roles' => array_diff_key($roleNames, ['learner' => true]), 'actions' => $options['actions']],
            default => [],
        };
    }

    private function searchHint(string $report): string
    {
        return match ($report) {
            'users' => 'Name or email',
            'modules' => 'Module title',
            'classes' => 'Class or instructor',
            'assessments' => 'Assessment title',
            'challenges' => 'Challenge title',
            'audit' => 'User, record or details',
            default => 'Search',
        };
    }
}
