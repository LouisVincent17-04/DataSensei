<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\User;
use App\Services\Reports\ClassProgress;
use App\Support\Reports\PerformanceBands;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportFormat as F;
use App\Support\Reports\ReportTable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class Analytics (DataSensei Updates 8): the instructor's one place to
 * follow a class. Pick a class, then read it in tabs (Overview, Students,
 * Modules, Assessments, Challenges, Coding Challenges) and open any student
 * for their details. Assignments were merged into assessments (DataSensei
 * Updates 11); converted rows are already counted with the assessments.
 *
 * Everything is read live from the class's saved work through
 * ClassProgress, the same source as the instructor Reports, so the two always
 * agree. The at-risk list uses fixed rules on that data
 * (ClassProgress::attentionRules()); there is no ILO mastery, no ranking and
 * nothing is guessed. Only the instructor's own classes can be opened.
 *
 * DataSensei Updates 12: Class Analytics and the former At-Risk Alerts page
 * are one instructor area. The Overview shows a Low / Moderate / High bar
 * graph of the class (by assessment average, thresholds from
 * config/class_analytics.php) and, in the same place, the at-risk students
 * with the reasons that flagged them. /instructor/risk now opens this page.
 */
class InstructorAnalyticsController extends Controller
{
    public const TABS = [
        'overview' => 'Overview',
        'students' => 'Students',
        'modules' => 'Modules',
        'assessments' => 'Assessments',
        'challenges' => 'Challenges',
        'coding' => 'Coding Challenges',
    ];

    /** Tabs whose lists can be limited to work due in a date range. */
    private const DATED_TABS = ['assessments', 'challenges', 'coding'];

    public function __construct(private readonly ClassProgress $progress)
    {
    }

    public function index(Request $request): View
    {
        $filters = ReportFilters::fromRequest($request);
        $tab = array_key_exists((string) $request->query('tab'), self::TABS) ? (string) $request->query('tab') : 'overview';

        $classes = ClassRoom::query()
            ->forInstructor((int) Auth::id())
            ->withCount('students')
            ->orderBy('is_archived')
            ->orderBy('name')
            ->get();

        $class = null;
        if ($filters->classId !== null) {
            $class = $classes->firstWhere('id', $filters->classId);
            abort_if($class === null, 404);
        } elseif ($classes->count() === 1) {
            $class = $classes->first();
        }

        if ($class === null) {
            return view('instructor.analytics.index', [
                'classes' => $classes,
                'class' => null,
                'overviews' => $classes->mapWithKeys(fn (ClassRoom $c) => [$c->id => $this->progress->forClass($c)['overview']]),
            ]);
        }

        $window = in_array($tab, self::DATED_TABS, true) && $filters->hasDateRange() ? new ReportFilters(from: $filters->from, to: $filters->to) : null;
        $snapshot = $this->progress->forClass($class, $window);

        return view('instructor.analytics.index', [
            'classes' => $classes,
            'class' => $class,
            'tab' => $tab,
            'tabs' => self::TABS,
            'filters' => $filters,
            'snapshot' => $snapshot,
            'overview' => $snapshot['overview'],
            'content' => $this->tab($tab, $class, $snapshot, $filters),
        ]);
    }

    public function student(Request $request, ClassRoom $class, User $student): View
    {
        abort_unless((int) $class->instructor_id === (int) Auth::id(), 404);
        $filters = ReportFilters::fromRequest($request);

        $snapshot = $this->progress->forClass($class);
        $data = $snapshot['perStudent'][$student->id] ?? null;
        abort_if($data === null, 404);

        return view('instructor.analytics.student', [
            'class' => $class,
            'student' => $student,
            'data' => $data,
            'summary' => $data['summary'],
            'snapshot' => $snapshot,
            'filters' => $filters,
            'tables' => $this->studentTables($snapshot, $data),
            'activity' => $this->progress->recentActivity($snapshot, $student->id, $filters),
            'activityTypes' => ['module' => 'Modules', 'assessment' => 'Assessments', 'challenge' => 'Challenges', 'coding' => 'Coding challenges'],
        ]);
    }

    // ── Tabs ─────────────────────────────────────────────────────────

    /**
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>
     */
    private function tab(string $tab, ClassRoom $class, array $snapshot, ReportFilters $filters): array
    {
        $count = count($snapshot['perStudent']);

        return match ($tab) {
            'students' => $this->studentsTab($class, $snapshot, $filters),
            'modules' => $this->modulesTab($snapshot, $filters, $count),
            'assessments' => $this->assessmentsTab($snapshot, $filters, $count),
            'challenges' => $this->challengesTab($snapshot, $count),
            'coding' => $this->codingTab($snapshot, $count),
            default => $this->overviewTab($class, $snapshot),
        };
    }

    private function overviewTab(ClassRoom $class, array $snapshot): array
    {
        $o = $snapshot['overview'];
        $completion = array_values(array_filter([
            $o['modules'] > 0 ? ['label' => 'Modules completed', 'percent' => $o['module_completion'], 'text' => F::pct($o['module_completion'])] : null,
            $o['assessments'] > 0 ? ['label' => 'Assessments completed', 'percent' => $o['assessment_completion'], 'text' => F::pct($o['assessment_completion'])] : null,
            $o['challenges'] > 0 ? ['label' => 'Challenges completed', 'percent' => $o['challenge_completion'], 'text' => F::pct($o['challenge_completion'])] : null,
            $o['coding'] > 0 ? ['label' => 'Coding challenges completed', 'percent' => $o['coding_completion'], 'text' => F::pct($o['coding_completion'])] : null,
        ]));
        $scores = array_values(array_filter([
            $o['assessment_average'] !== null ? ['label' => 'Assessment average', 'percent' => $o['assessment_average'], 'text' => F::pct($o['assessment_average'])] : null,
            $o['challenge_average'] !== null ? ['label' => 'Challenge average', 'percent' => $o['challenge_average'], 'text' => F::pct($o['challenge_average'])] : null,
            $o['coding_score'] !== null ? ['label' => 'Coding tests passed', 'percent' => $o['coding_score'], 'text' => F::pct($o['coding_score'])] : null,
        ]));

        $attention = collect($snapshot['perStudent'])
            ->filter(fn (array $row) => $row['summary']['attention'] !== [])
            ->map(fn (array $row, int $id) => [
                'name' => $row['student']->name,
                'url' => route('instructor.analytics.student', ['class' => $class->id, 'student' => $id]),
                'reasons' => array_values($row['summary']['attention']),
                'group' => PerformanceBands::label($row['summary']['performance_group']),
                'average' => $row['summary']['assessment_average'],
            ])
            ->values()
            ->all();

        return [
            'performance' => $this->performanceGraph($o),
            'bars' => array_values(array_filter([
                $completion !== [] ? ['title' => 'Completion', 'items' => $completion] : null,
                $scores !== [] ? ['title' => 'Scores', 'items' => $scores] : null,
            ])),
            'attention' => $attention,
        ];
    }

    /**
     * Low / Moderate / High: how many students are in each group, as a share
     * of the students who have a graded assessment.
     *
     * @param  array<string, mixed>  $overview
     * @return array<string, mixed>
     */
    private function performanceGraph(array $overview): array
    {
        $counts = $overview['performance'];
        $graded = $counts[PerformanceBands::LOW] + $counts[PerformanceBands::MODERATE] + $counts[PerformanceBands::HIGH];
        $notGraded = $counts[PerformanceBands::NOT_GRADED];

        $items = [];
        foreach ([PerformanceBands::LOW, PerformanceBands::MODERATE, PerformanceBands::HIGH] as $group) {
            $share = F::percent($counts[$group], $graded);
            $items[] = [
                'label' => PerformanceBands::label($group).' ('.PerformanceBands::rangeText($group).')',
                'percent' => $share ?? 0,
                'text' => $counts[$group].' '.($counts[$group] === 1 ? 'student' : 'students').($graded > 0 ? ', '.F::pct($share) : ''),
                'tone' => PerformanceBands::TONES[$group],
            ];
        }

        return [
            'id' => 'performance',
            'title' => 'Class performance',
            'items' => $items,
            'note' => 'Grouped by each student\'s assessment average (best graded attempt on each assessment). '
                .($notGraded > 0 ? $notGraded.' '.($notGraded === 1 ? 'student has' : 'students have').' no graded assessment yet and '.($notGraded === 1 ? 'is' : 'are').' shown as Not yet graded.' : 'Every student has at least one graded assessment.'),
        ];
    }

    private function studentsTab(ClassRoom $class, array $snapshot, ReportFilters $f): array
    {
        $search = mb_strtolower($f->search);
        $rows = collect($snapshot['perStudent'])
            ->filter(fn (array $row) => $search === '' || str_contains(mb_strtolower($row['student']->name.' '.$row['student']->email), $search))
            ->filter(fn (array $row) => match ($f->status) {
                'attention' => $row['summary']['attention'] !== [],
                PerformanceBands::LOW, PerformanceBands::MODERATE, PerformanceBands::HIGH, PerformanceBands::NOT_GRADED => $row['summary']['performance_group'] === $f->status,
                default => true,
            })
            ->map(function (array $row, int $id) use ($class): array {
                $s = $row['summary'];

                return [
                    'student' => $row['student']->name,
                    'modules' => $s['modules_total'] > 0 ? $s['modules_completed'].' of '.$s['modules_total'].' ('.F::pct($s['module_percent']).')' : F::NONE,
                    // Assignments were merged into assessments; converted rows
                    // are already counted here.
                    'assessments' => $s['assessments_total'] > 0 ? $s['assessments_completed'].' of '.$s['assessments_total'].' completed'.($s['assessments_missing'] > 0 ? ', '.$s['assessments_missing'].' missing' : '').($s['assessment_average'] !== null ? ', average '.F::pct($s['assessment_average']) : '') : F::NONE,
                    'performance' => PerformanceBands::label($s['performance_group']),
                    'challenges' => F::pct($s['challenge_average']),
                    'coding' => $s['coding_total'] > 0 ? $s['coding_completed'].' of '.$s['coding_total'].' completed'.($s['coding_score'] !== null ? ', '.F::pct($s['coding_score']).' of tests' : '') : F::NONE,
                    'overall' => F::pct($s['overall']),
                    'activity' => $s['last_activity'] ? F::date($s['last_activity']) : 'No class activity yet',
                    'attention' => $s['attention'] === [] ? '' : implode('; ', $s['attention']),
                    '_url' => route('instructor.analytics.student', ['class' => $class->id, 'student' => $id]),
                    '_tone' => [
                        'performance' => PerformanceBands::TONES[$s['performance_group']] ?? null,
                        'assessments' => $s['assessments_missing'] > 0 ? 'warn' : F::tone($s['assessment_average']),
                        'challenges' => F::tone($s['challenge_average']),
                        'overall' => F::tone($s['overall']),
                        'attention' => 'warn',
                    ],
                ];
            })->values();

        [$page] = F::page($rows, 'students', false);

        return ['table' => new ReportTable('students', 'Students', [
            'student' => 'Student', 'modules' => 'Module progress', 'assessments' => 'Assessments', 'performance' => 'Performance',
            'challenges' => 'Challenge average', 'coding' => 'Coding challenges', 'overall' => 'Overall progress',
            'activity' => 'Last class activity', 'attention' => 'At risk because',
        ], $page, 'No students match these filters.', 'Averages use each student\'s best graded attempt. Performance: Low is '.mb_strtolower(PerformanceBands::rangeText(PerformanceBands::LOW)).', Moderate '.PerformanceBands::rangeText(PerformanceBands::MODERATE).', High '.PerformanceBands::rangeText(PerformanceBands::HIGH).'. Open a student to see everything they did.')];
    }

    private function modulesTab(array $snapshot, ReportFilters $f, int $students): array
    {
        $bars = [];
        $rows = collect($snapshot['modules'])->map(function (object $module) use ($snapshot, $students, &$bars): array {
            $states = collect($snapshot['perStudent'])->map(fn (array $row) => $row['modules'][$module->id]['state'] ?? 'not_started');
            $started = $states->filter(fn ($s) => $s !== 'not_started')->count();
            $completed = $states->filter(fn ($s) => $s === 'completed')->count();
            $rate = F::percent($completed, $students);
            $bars[] = ['label' => $module->title, 'percent' => $rate, 'text' => $completed.' of '.$students];

            return [
                'module' => $module->title.($module->version_name ? ' ('.$module->version_name.')' : ''),
                'assigned' => F::date($module->assigned_at ?? $module->created_at),
                'started' => $started.' of '.$students,
                'completed' => $completed.' of '.$students,
                'rate' => F::pct($rate),
                '_tone' => ['rate' => F::tone($rate)],
            ];
        })->values();

        $detail = null;
        if ($f->moduleId !== null && isset($snapshot['modules'][$f->moduleId])) {
            $module = $snapshot['modules'][$f->moduleId];
            $detail = new ReportTable('module-students', $module->title.': students', [
                'student' => 'Student', 'status' => 'Status', 'opened' => 'First opened', 'completed' => 'Marked complete',
            ], collect($snapshot['perStudent'])->map(function (array $row) use ($module): array {
                $p = $row['modules'][$module->id] ?? ['state' => 'not_started', 'opened_at' => null, 'completed_at' => null];

                return [
                    'student' => $row['student']->name,
                    'status' => ['completed' => 'Completed', 'started' => 'Started'][$p['state']] ?? 'Not started',
                    'opened' => F::date($p['opened_at']),
                    'completed' => F::date($p['completed_at']),
                    '_tone' => ['status' => $p['state'] === 'completed' ? 'good' : null],
                ];
            })->values()->all(), 'No students in this class yet.');
        }

        return [
            'bars' => $bars === [] ? [] : [['title' => 'Students who completed each module', 'items' => $bars]],
            'table' => new ReportTable('modules', 'Assigned modules', [
                'module' => 'Module', 'assigned' => 'Assigned', 'started' => 'Started', 'completed' => 'Completed', 'rate' => 'Completion rate',
            ], $rows->all(), 'No modules are assigned to this class. Assign modules from the Module Library.', 'A student starts a module by opening it and completes it with "Mark module as complete".'),
            'detail' => $detail,
            'moduleChoices' => collect($snapshot['modules'])->mapWithKeys(fn ($m) => [$m->id => $m->title.($m->version_name ? ' ('.$m->version_name.')' : '')])->all(),
        ];
    }

    private function assessmentsTab(array $snapshot, ReportFilters $f, int $students): array
    {
        // Assignments were merged into assessments (DataSensei Updates 11);
        // converted rows are already counted here, under their purpose label.
        $rows = collect($snapshot['assessments'])
            ->filter(fn (object $a) => $f->status === '' || ($f->status === 'open' ? $a->status === 'published' : $a->status === 'closed'))
            ->map(function (object $a) use ($snapshot, $students): array {
                $results = collect($snapshot['perStudent'])->map(fn (array $row) => $row['assessments'][$a->id] ?? null)->filter();
                $completed = $results->where('state', 'completed')->count();
                $bests = $results->pluck('best')->filter(fn ($p) => $p !== null);
                $average = $bests->isEmpty() ? null : round($bests->avg(), 1);

                return [
                    'assessment' => $a->title,
                    'kind' => Assessment::PURPOSES[$a->purpose] ?? 'Assessment',
                    'due' => F::dateTime($a->due_at),
                    'status' => $a->status === 'closed' ? 'Closed' : 'Open',
                    'attempts' => F::number($results->sum('attempts')),
                    'completed' => $completed.' of '.$students.' ('.F::pct(F::percent($completed, $students)).')',
                    'average' => F::pct($average),
                    'passed' => F::number($results->filter(fn ($r) => ($r['passed'] ?? null) === true)->count()),
                    // Strict: a student with no attempt has passed === null,
                    // which a loose where() would count as failed.
                    'failed' => F::number($results->filter(fn ($r) => ($r['passed'] ?? null) === false)->count()),
                    '_tone' => ['average' => F::tone($average)],
                ];
            })->values();

        return ['table' => new ReportTable('assessments', 'Assessments', [
            'assessment' => 'Assessment', 'kind' => 'Type', 'due' => 'Due', 'status' => 'Status', 'attempts' => 'Attempts', 'completed' => 'Completed',
            'average' => 'Average score', 'passed' => 'Passed', 'failed' => 'Failed',
        ], $rows->all(), 'No published assessments match these filters.', 'Average score, pass and fail use each student\'s best attempt; the pass mark is '.F::PASS_PERCENT.'%.')];
    }

    private function challengesTab(array $snapshot, int $students): array
    {
        $rows = collect($snapshot['challenges'])->map(function (object $c) use ($snapshot, $students): array {
            $results = collect($snapshot['perStudent'])->map(fn (array $row) => $row['challenges'][$c->id] ?? null)->filter();
            $attempted = $results->where('state', '!=', 'not_started')->count();
            $passed = $results->where('state', 'passed')->count();
            $bests = $results->pluck('best')->filter(fn ($p) => $p !== null);
            $average = $bests->isEmpty() ? null : round($bests->avg(), 1);

            return [
                'challenge' => $c->given_title ?: $c->title,
                'due' => F::dateTime($c->due_at),
                'attempted' => $attempted.' of '.$students,
                'completed' => $passed.' of '.$students.' ('.F::pct(F::percent($passed, $students)).')',
                'average' => F::pct($average),
                'attempts' => F::number($results->sum('attempts')),
                '_tone' => ['average' => F::tone($average)],
            ];
        })->values();

        return ['table' => new ReportTable('challenges', 'Challenges', [
            'challenge' => 'Challenge', 'due' => 'Due', 'attempted' => 'Attempted', 'completed' => 'Completed ('.F::PASS_PERCENT.'% or more)',
            'average' => 'Average best score', 'attempts' => 'Attempts',
        ], $rows->all(), 'No quiz challenges are shared with this class. Share one from the Challenge Builder or the Challenge Pool.')];
    }

    private function codingTab(array $snapshot, int $students): array
    {
        $rows = collect($snapshot['coding'])->map(function (object $c) use ($snapshot, $students): array {
            $results = collect($snapshot['perStudent'])->map(fn (array $row) => $row['coding'][$c->id] ?? null)->filter();
            $started = $results->where('state', '!=', 'not_started')->count();
            $completed = $results->where('state', 'completed')->count();
            $scores = $results->pluck('score')->filter(fn ($p) => $p !== null);
            $failing = $results->filter(fn ($r) => $r['state'] !== 'completed' && $r['failed'] >= 3)->count();
            $average = $scores->isEmpty() ? null : round($scores->avg(), 1);

            return [
                'challenge' => $c->given_title ?: $c->title,
                'problems' => F::number($results->first()['problems'] ?? 0),
                'started' => $started.' of '.$students,
                'completed' => $completed.' of '.$students.' ('.F::pct(F::percent($completed, $students)).')',
                'score' => F::pct($average),
                'submissions' => F::number($results->sum('submissions')),
                'failing' => F::number($failing),
                '_tone' => ['score' => F::tone($average), 'failing' => $failing > 0 ? 'warn' : null],
            ];
        })->values();

        return ['table' => new ReportTable('coding', 'Coding challenges', [
            'challenge' => 'Coding challenge', 'problems' => 'Problems', 'started' => 'Started', 'completed' => 'Completed',
            'score' => 'Average tests passed', 'submissions' => 'Submissions', 'failing' => 'Students with 3 or more failed runs',
        ], $rows->all(), 'No coding challenges are shared with this class. Share one from the Challenge Builder or the Challenge Pool.', 'A coding challenge is completed when every problem in it is solved.')];
    }

    // ── One student ──────────────────────────────────────────────────

    /**
     * @param  array<string, mixed>  $snapshot
     * @param  array<string, mixed>  $data
     * @return list<ReportTable>
     */
    private function studentTables(array $snapshot, array $data): array
    {
        $state = fn (array $labels, string $key) => $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));

        return [
            new ReportTable('modules', 'Modules', ['module' => 'Module', 'status' => 'Status', 'opened' => 'First opened', 'completed' => 'Marked complete'],
                collect($data['modules'])->map(fn (array $m, int $id) => [
                    'module' => $snapshot['modules'][$id]->title,
                    'status' => $state(['completed' => 'Completed', 'started' => 'Started', 'not_started' => 'Not started'], $m['state']),
                    'opened' => F::date($m['opened_at']),
                    'completed' => F::date($m['completed_at']),
                    '_tone' => ['status' => $m['state'] === 'completed' ? 'good' : null],
                ])->values()->all(), 'No modules are assigned to this class.'),
            // Assignments were merged into assessments; converted rows are
            // already counted here, under their purpose label.
            new ReportTable('assessments', 'Assessments', ['assessment' => 'Assessment', 'kind' => 'Type', 'attempts' => 'Attempts', 'score' => 'Best score', 'result' => 'Pass or fail', 'status' => 'Status'],
                collect($data['assessments'])->map(fn (array $a, int $id) => [
                    'assessment' => $snapshot['assessments'][$id]->title,
                    'kind' => Assessment::PURPOSES[$snapshot['assessments'][$id]->purpose] ?? 'Assessment',
                    'attempts' => F::number($a['attempts']),
                    'score' => $a['score_text'],
                    'result' => $a['passed'] === null ? ($a['awaiting_review'] ? 'Awaiting grade' : F::NONE) : ($a['passed'] ? 'Passed' : 'Failed'),
                    'status' => $state(['completed' => 'Completed', 'in_progress' => 'In progress', 'not_started' => 'Not started'], $a['state']),
                    '_tone' => ['result' => $a['passed'] === null ? null : ($a['passed'] ? 'good' : 'bad')],
                ])->values()->all(), 'No assessments are published for this class.'),
            new ReportTable('challenges', 'Challenges', ['challenge' => 'Challenge', 'attempts' => 'Attempts', 'score' => 'Best score', 'status' => 'Status', 'last' => 'Last attempt'],
                collect($data['challenges'])->map(fn (array $c, int $id) => [
                    'challenge' => $snapshot['challenges'][$id]->given_title ?: $snapshot['challenges'][$id]->title,
                    'attempts' => F::number($c['attempts']),
                    'score' => $c['best'] === null ? F::NONE : $c['score_text'].' ('.F::pct($c['best']).')',
                    'status' => $state(['passed' => 'Completed', 'attempted' => 'Below '.F::PASS_PERCENT.'%', 'not_started' => 'Not started'], $c['state']),
                    'last' => F::dateTime($c['last_at']),
                    '_tone' => ['status' => match ($c['state']) { 'passed' => 'good', 'attempted' => 'warn', default => null }],
                ])->values()->all(), 'No MCQ challenges are given to this class.'),
            new ReportTable('coding', 'Coding challenges', ['challenge' => 'Coding challenge', 'attempts' => 'Submissions', 'solved' => 'Problems solved', 'score' => 'Tests passed', 'tests' => 'Test cases (best runs)', 'time' => 'Completion time', 'status' => 'Status'],
                collect($data['coding'])->map(fn (array $c, int $id) => [
                    'challenge' => $snapshot['coding'][$id]->given_title ?: $snapshot['coding'][$id]->title,
                    'attempts' => F::number($c['submissions']),
                    'solved' => $c['solved'].' of '.$c['problems'],
                    'score' => F::pct($c['score']),
                    'tests' => $c['submissions'] > 0 ? $c['tests_passed'].' passed, '.$c['tests_failed'].' failed' : F::NONE,
                    'time' => F::duration($c['time']),
                    'status' => $state(['completed' => 'Completed', 'in_progress' => 'In progress', 'not_started' => 'Not started'], $c['state']),
                    '_tone' => ['status' => match ($c['state']) { 'completed' => 'good', 'in_progress' => 'warn', default => null }],
                ])->values()->all(), 'No coding challenges are given to this class.'),
        ];
    }
}
