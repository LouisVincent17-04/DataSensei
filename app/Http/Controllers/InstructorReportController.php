<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\Reports\InstructorReports;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Instructor Reports (DataSensei Updates 8): Class Performance, Student
 * Progress, Assignments & Assessments, Challenges & Coding Challenges, Module
 * Assignments and Submissions.
 *
 * Only the instructor's own classes and the students enrolled in them are
 * ever read: the class list comes from classes.instructor_id, and asking for
 * someone else's class is refused (404) rather than ignored.
 */
class InstructorReportController extends Controller
{
    public function __construct(
        private readonly InstructorReports $reports,
        private readonly ReportResponses $responses,
    ) {
    }

    public function index(Request $request): View
    {
        return $this->show($request, 'classes');
    }

    public function show(Request $request, string $report): View
    {
        abort_unless(isset(InstructorReports::REPORTS[$report]), 404);
        $filters = ReportFilters::fromRequest($request);
        [$classes, $scoped] = $this->classes($filters);

        return view('instructor.reports.show', [
            'catalog' => InstructorReports::REPORTS,
            'current' => $report,
            'result' => $this->reports->build($report, $scoped, $filters),
            'filters' => $filters,
            'choices' => $this->choices($report, $classes),
            'searchHint' => $report === 'classes' ? 'Class name' : 'Student or activity',
            'noClasses' => $classes->isEmpty(),
        ]);
    }

    public function export(Request $request, string $report, string $format): Response
    {
        abort_unless(isset(InstructorReports::REPORTS[$report]) && in_array($format, ['csv', 'pdf', 'print'], true), 404);
        $filters = ReportFilters::fromRequest($request);
        [$classes, $scoped] = $this->classes($filters);
        $result = $this->reports->build($report, $scoped, $filters, true);

        return $this->responses->export(
            $format,
            'instructor',
            $report,
            $result,
            $this->responses->filterLines($filters, $this->choices($report, $classes)),
            route('instructor.reports.show', ['report' => $report] + $filters->query()),
        );
    }

    /**
     * The instructor's classes, and the ones the class filter keeps.
     *
     * @return array{0: Collection<int, ClassRoom>, 1: Collection<int, ClassRoom>}
     */
    private function classes(ReportFilters $filters): array
    {
        $classes = ClassRoom::query()
            ->forInstructor((int) Auth::id())
            ->orderBy('name')
            ->get();

        if ($filters->classId === null) {
            return [$classes, $classes];
        }

        $class = $classes->firstWhere('id', $filters->classId);
        abort_if($class === null, 404);

        return [$classes, collect([$class])];
    }

    /** @return array<string, mixed> */
    private function choices(string $report, Collection $classes): array
    {
        $choices = [
            'classes' => $classes->mapWithKeys(fn (ClassRoom $class) => [$class->id => $class->name.($class->section ? ', '.$class->section : '').($class->is_archived ? ' (archived)' : '')])->all(),
            'statuses' => InstructorReports::STATUSES[$report] ?? [],
            'types' => InstructorReports::TYPES[$report] ?? [],
            'typeLabel' => 'Activity type',
        ];

        if ($report === 'modules') {
            $choices['modules'] = DB::table('class_module_assignments as a')
                ->join('module_library_items as m', 'm.id', '=', 'a.module_library_item_id')
                ->whereIn('a.class_id', $classes->pluck('id')->all() ?: [0])
                ->orderBy('m.module_no')->orderBy('m.version_no')
                ->get(['m.id', 'm.title', 'm.version_name'])
                ->unique('id')
                ->mapWithKeys(fn ($m) => [$m->id => $m->title.($m->version_name ? ' ('.$m->version_name.')' : '')])
                ->all();
        }

        return $choices;
    }
}
