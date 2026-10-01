<?php

namespace App\Services\Reports;

use App\Models\ClassRoom;
use App\Support\Reports\ReportExporter;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportFormat as F;
use App\Support\Reports\ReportResult;
use App\Support\Reports\ReportTable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The instructor reports (DataSensei Updates 8). Every figure comes from the
 * classes the instructor teaches and the students enrolled in them; the
 * controller only passes classes the instructor owns. Class progress comes
 * from ClassProgress, the same source as Class Analytics.
 *
 *   classes      Class Performance
 *   students     Student Progress
 *   assignments  Assignments & Assessments, per student
 *   challenges   Challenges & Coding Challenges given to the class, per student
 *   modules      Module Assignments (modules are made by admins; instructors
 *                assign them)
 *   submissions  every submission and attempt in one list
 */
class InstructorReports
{
    public const REPORTS = [
        'classes' => ['title' => 'Class Performance', 'filters' => ['search', 'status']],
        'students' => ['title' => 'Student Progress', 'filters' => ['class', 'search']],
        'assignments' => ['title' => 'Assignments & Assessments', 'filters' => ['date', 'class', 'status', 'search']],
        'challenges' => ['title' => 'Challenges & Coding Challenges', 'filters' => ['class', 'status', 'search']],
        'modules' => ['title' => 'Module Assignments', 'filters' => ['class', 'module', 'status']],
        'submissions' => ['title' => 'Submissions', 'filters' => ['date', 'class', 'type', 'status', 'search']],
    ];

    public const STATUSES = [
        'classes' => ['active' => 'Active', 'archived' => 'Archived'],
        'assignments' => ['completed' => 'Completed', 'not_completed' => 'Not completed', 'late' => 'Late', 'missing' => 'Missing', 'passed' => 'Passed', 'failed' => 'Failed'],
        'challenges' => ['completed' => 'Completed', 'in_progress' => 'Attempted, not completed', 'not_started' => 'Not started'],
        'modules' => ['active' => 'Assigned', 'archived' => 'Removed'],
        'submissions' => ['on_time' => 'On time', 'late' => 'Late', 'passed' => 'Passed', 'failed' => 'Failed', 'awaiting' => 'Awaiting grade'],
    ];

    public const TYPES = [
        'submissions' => ['assignment' => 'Assignment', 'assessment' => 'Assessment', 'challenge' => 'Challenge', 'coding' => 'Coding challenge'],
    ];

    public function __construct(private readonly ClassProgress $progress)
    {
    }

    /** @param  Collection<int, ClassRoom>  $classes  the instructor's classes (already filtered by class_id) */
    public function build(string $key, Collection $classes, ReportFilters $filters, bool $all = false): ReportResult
    {
        return match ($key) {
            'classes' => $this->classes($classes, $filters, $all),
            'students' => $this->students($classes, $filters, $all),
            'assignments' => $this->assignments($classes, $filters, $all),
            'challenges' => $this->challenges($classes, $filters, $all),
            'modules' => $this->modules($classes, $filters, $all),
            'submissions' => $this->submissions($classes, $filters, $all),
        };
    }

    // ── 1. Class Performance ─────────────────────────────────────────

    private function classes(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $classes = $classes
            ->when($f->status === 'active', fn ($c) => $c->filter(fn (ClassRoom $class) => ! $class->is_archived))
            ->when($f->status === 'archived', fn ($c) => $c->filter(fn (ClassRoom $class) => (bool) $class->is_archived))
            ->when($f->search !== '', fn ($c) => $c->filter(fn (ClassRoom $class) => str_contains(mb_strtolower($class->name.' '.$class->section), mb_strtolower($f->search))));

        $rows = $classes->map(function (ClassRoom $class): array {
            $o = $this->progress->forClass($class)['overview'];

            return [
                'class' => trim($class->name.($class->section ? ', '.$class->section : '')),
                'status' => $class->is_archived ? 'Archived' : 'Active',
                'students' => F::number($o['students']),
                'modules' => F::number($o['modules']),
                'module_completion' => F::pct($o['module_completion']),
                'assessment_average' => F::pct($o['assessment_average']),
                'assignment_rate' => F::pct($o['assignment_rate']),
                'activity' => $o['students'] > 0 ? $o['active'].' of '.$o['students'].' active in the last '.ClassProgress::ACTIVE_DAYS.' days' : F::NONE,
                '_url' => route('instructor.analytics.index', ['class_id' => $class->id]),
                '_tone' => ['module_completion' => F::tone($o['module_completion']), 'assessment_average' => F::tone($o['assessment_average'])],
                '_o' => $o,
            ];
        })->values();

        [$page, $cut] = F::page($rows, 'classes', $all);
        $overviews = $rows->pluck('_o');
        $avg = fn (string $key) => ($values = $overviews->pluck($key)->filter(fn ($v) => $v !== null))->isEmpty() ? null : round($values->avg(), 1);

        return new ReportResult(
            'classes',
            'Class Performance',
            'How each of your classes is doing. Open a class to see it in Class Analytics.',
            [
                ['label' => 'Classes', 'value' => F::number($classes->count())],
                ['label' => 'Students enrolled', 'value' => F::number($overviews->sum('students'))],
                ['label' => 'Module completion', 'value' => F::pct($avg('module_completion')), 'note' => 'Average across classes'],
                ['label' => 'Average assessment score', 'value' => F::pct($avg('assessment_average')), 'note' => 'Average across classes'],
            ],
            [new ReportTable('classes', 'Classes', [
                'class' => 'Class', 'status' => 'Status', 'students' => 'Enrolled students', 'modules' => 'Assigned modules',
                'module_completion' => 'Module completion', 'assessment_average' => 'Average assessment score',
                'assignment_rate' => 'Assignments submitted', 'activity' => 'Class activity',
            ], $page, 'You have no classes that match these filters.', 'Module completion is modules completed divided by students times assigned modules. Assessment scores use each student\'s best attempt.', $cut)],
        );
    }

    // ── 2. Student Progress ──────────────────────────────────────────

    private function students(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $rows = collect();
        foreach ($classes as $class) {
            $snapshot = $this->progress->forClass($class);
            foreach ($snapshot['perStudent'] as $studentId => $data) {
                $student = $data['student'];
                if ($f->search !== '' && ! str_contains(mb_strtolower($student->name.' '.$student->email), mb_strtolower($f->search))) {
                    continue;
                }
                $s = $data['summary'];
                $rows->push([
                    'student' => $student->name,
                    'class' => $class->name,
                    'modules' => $s['modules_total'] > 0 ? $s['modules_completed'].' of '.$s['modules_total'].' completed, '.$s['modules_started'].' started' : 'No modules assigned',
                    'module_completion' => F::pct($s['module_percent']),
                    'assignments' => $s['assignments_total'] > 0 ? $s['assignments_submitted'].' of '.$s['assignments_total'].' submitted'.($s['assignments_missing'] > 0 ? ', '.$s['assignments_missing'].' missing' : '') : 'None given',
                    'assessments' => $s['assessments_total'] > 0 ? $s['assessments_completed'].' of '.$s['assessments_total'].' completed'.($s['assessment_average'] !== null ? ', average '.F::pct($s['assessment_average']) : '') : 'None given',
                    'challenges' => $s['challenges_total'] > 0 ? $s['challenge_attempts'].' '.($s['challenge_attempts'] === 1 ? 'attempt' : 'attempts').', '.$s['challenges_passed'].' of '.$s['challenges_total'].' passed' : 'None given',
                    'coding' => $s['coding_total'] > 0 ? $s['coding_submissions'].' '.($s['coding_submissions'] === 1 ? 'submission' : 'submissions').', '.$s['coding_completed'].' of '.$s['coding_total'].' completed' : 'None given',
                    'overall' => F::pct($s['overall']),
                    '_overall' => $s['overall'],
                    '_url' => route('instructor.analytics.student', ['class' => $class->id, 'student' => $studentId]),
                    '_tone' => ['overall' => F::tone($s['overall']), 'assignments' => $s['assignments_missing'] > 0 ? 'warn' : null],
                ]);
            }
        }

        [$page, $cut] = F::page($rows, 'students', $all);

        return new ReportResult(
            'students',
            'Student Progress',
            'Each student\'s progress in your class: modules, assignments, assessments and the challenges given to the class. Open a student to see the details.',
            [
                ['label' => 'Students', 'value' => F::number($rows->count())],
                ['label' => 'Average overall progress', 'value' => F::pct($this->averageOf($rows, '_overall'))],
            ],
            [new ReportTable('students', 'Students', [
                'student' => 'Student', 'class' => 'Class', 'modules' => 'Assigned module progress', 'module_completion' => 'Module completion',
                'assignments' => 'Assignments', 'assessments' => 'Assessments', 'challenges' => 'Challenge activity',
                'coding' => 'Coding challenge activity', 'overall' => 'Overall class progress',
            ], $page, 'No students match these filters.', 'Overall class progress averages the completion of modules, assignments, assessments, challenges and coding challenges given to the class.', $cut)],
        );
    }

    // ── 3. Assignments & Assessments ─────────────────────────────────

    private function assignments(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $window = $f->hasDateRange() ? new ReportFilters(from: $f->from, to: $f->to) : null;
        $assignmentRows = collect();
        $assessmentRows = collect();
        $search = mb_strtolower($f->search);

        foreach ($classes as $class) {
            $snapshot = $this->progress->forClass($class, $window);
            foreach ($snapshot['perStudent'] as $data) {
                $student = $data['student'];
                foreach ($data['assignments'] as $id => $a) {
                    $item = $snapshot['assignments'][$id];
                    if ($search !== '' && ! str_contains(mb_strtolower($student->name.' '.$item->title), $search)) {
                        continue;
                    }
                    if (! $this->assignmentMatches($f->status, $a)) {
                        continue;
                    }
                    $assignmentRows->push([
                        'student' => $student->name,
                        'class' => $class->name,
                        'assignment' => $item->title,
                        'status' => $this->assignmentState($a['state']),
                        'submitted' => F::dateTime($a['submitted_at']),
                        'timing' => match ($a['state']) {
                            'late' => 'Late',
                            'submitted' => $item->due_at ? 'On time' : F::NONE,
                            default => F::NONE,
                        },
                        'grade' => $a['score_text'],
                        '_tone' => ['status' => match ($a['state']) { 'missing' => 'bad', 'late' => 'warn', 'submitted' => 'good', default => null }],
                    ]);
                }
                foreach ($data['assessments'] as $id => $a) {
                    $item = $snapshot['assessments'][$id];
                    if ($search !== '' && ! str_contains(mb_strtolower($student->name.' '.$item->title), $search)) {
                        continue;
                    }
                    if (! $this->assessmentMatches($f->status, $a, $item)) {
                        continue;
                    }
                    $assessmentRows->push([
                        'student' => $student->name,
                        'class' => $class->name,
                        'assessment' => $item->title,
                        'attempts' => F::number($a['attempts']),
                        'score' => $a['score_text'],
                        'percent' => F::pct($a['best']),
                        'result' => $a['passed'] === null ? ($a['awaiting_review'] ? 'Awaiting grade' : F::NONE) : ($a['passed'] ? 'Passed' : 'Failed'),
                        'status' => match ($a['state']) { 'completed' => 'Completed', 'in_progress' => 'In progress', default => 'Not started' },
                        '_tone' => ['result' => $a['passed'] === null ? null : ($a['passed'] ? 'good' : 'bad')],
                    ]);
                }
            }
        }

        [$assignmentPage, $cutA] = F::page($assignmentRows, 'assignments', $all);
        [$assessmentPage, $cutB] = F::page($assessmentRows, 'assessments', $all);

        return new ReportResult(
            'assignments',
            'Assignments & Assessments',
            'Every student\'s result on each assignment and assessment in your classes. Pass is '.F::PASS_PERCENT.'% or more on the best attempt.',
            [
                ['label' => 'Assignment results', 'value' => F::number($assignmentRows->count())],
                ['label' => 'Missing assignments', 'value' => F::number($assignmentRows->where('status', 'Missing')->count())],
                ['label' => 'Late submissions', 'value' => F::number($assignmentRows->where('status', 'Submitted late')->count())],
                ['label' => 'Assessments passed', 'value' => F::number($assessmentRows->where('result', 'Passed')->count()).' of '.F::number($assessmentRows->where('status', 'Completed')->count()), 'note' => 'Completed assessments'],
            ],
            [
                new ReportTable('assignments', 'Assignments', [
                    'student' => 'Student', 'class' => 'Class', 'assignment' => 'Assignment', 'status' => 'Submission status',
                    'submitted' => 'Submitted', 'timing' => 'Late or on time', 'grade' => 'Grade or score',
                ], $assignmentPage, 'No assignment results match these filters.', $window ? 'Assignments due (or created, when there is no due date) in the selected range.' : null, $cutA),
                new ReportTable('assessments', 'Assessments', [
                    'student' => 'Student', 'class' => 'Class', 'assessment' => 'Assessment', 'attempts' => 'Attempts', 'score' => 'Score',
                    'percent' => 'Percentage', 'result' => 'Pass or fail', 'status' => 'Completion status',
                ], $assessmentPage, 'No assessment results match these filters.', null, $cutB),
            ],
        );
    }

    private function assignmentState(string $state): string
    {
        return match ($state) {
            'submitted' => 'Submitted',
            'late' => 'Submitted late',
            'missing' => 'Missing',
            'in_progress' => 'Started, not submitted',
            default => 'Not submitted yet',
        };
    }

    /** @param  array<string, mixed>  $a */
    private function assignmentMatches(string $status, array $a): bool
    {
        return match ($status) {
            'completed' => in_array($a['state'], ['submitted', 'late'], true),
            'not_completed' => ! in_array($a['state'], ['submitted', 'late'], true),
            'late' => $a['state'] === 'late',
            'missing' => $a['state'] === 'missing',
            'passed' => $a['score'] !== null && $a['score'] >= F::PASS_PERCENT,
            'failed' => $a['score'] !== null && $a['score'] < F::PASS_PERCENT,
            default => true,
        };
    }

    /** @param  array<string, mixed>  $a */
    private function assessmentMatches(string $status, array $a, object $item): bool
    {
        $pastDue = ClassProgress::isPastDue($item);

        return match ($status) {
            'completed' => $a['state'] === 'completed',
            'not_completed' => $a['state'] !== 'completed',
            'late' => false,
            'missing' => $a['state'] !== 'completed' && $pastDue,
            'passed' => $a['passed'] === true,
            'failed' => $a['passed'] === false,
            default => true,
        };
    }

    // ── 4. Challenges & Coding Challenges ────────────────────────────

    private function challenges(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $mcqRows = collect();
        $codingRows = collect();
        $search = mb_strtolower($f->search);
        $matches = fn (string $state) => match ($f->status) {
            'completed' => in_array($state, ['passed', 'completed'], true),
            'in_progress' => in_array($state, ['attempted', 'in_progress'], true),
            'not_started' => $state === 'not_started',
            default => true,
        };

        foreach ($classes as $class) {
            $snapshot = $this->progress->forClass($class);
            foreach ($snapshot['perStudent'] as $data) {
                $student = $data['student'];
                foreach ($data['challenges'] as $id => $c) {
                    $title = $snapshot['challenges'][$id]->title;
                    if (($search !== '' && ! str_contains(mb_strtolower($student->name.' '.$title), $search)) || ! $matches($c['state'])) {
                        continue;
                    }
                    $mcqRows->push([
                        'student' => $student->name,
                        'class' => $class->name,
                        'challenge' => $title,
                        'attempts' => F::number($c['attempts']),
                        'score' => $c['best'] === null ? F::NONE : $c['score_text'].' ('.F::pct($c['best']).')',
                        'status' => match ($c['state']) { 'passed' => 'Completed', 'attempted' => 'Attempted, below '.F::PASS_PERCENT.'%', default => 'Not started' },
                        '_tone' => ['status' => match ($c['state']) { 'passed' => 'good', 'attempted' => 'warn', default => null }],
                    ]);
                }
                foreach ($data['coding'] as $id => $c) {
                    $title = $snapshot['coding'][$id]->title;
                    if (($search !== '' && ! str_contains(mb_strtolower($student->name.' '.$title), $search)) || ! $matches($c['state'])) {
                        continue;
                    }
                    $codingRows->push([
                        'student' => $student->name,
                        'class' => $class->name,
                        'challenge' => $title,
                        'attempts' => F::number($c['submissions']),
                        'score' => F::pct($c['score']),
                        'tests' => $c['submissions'] > 0 ? $c['tests_passed'].' passed, '.$c['tests_failed'].' failed' : F::NONE,
                        'status' => match ($c['state']) { 'completed' => 'Completed', 'in_progress' => $c['solved'].' of '.$c['problems'].' problems solved', default => 'Not started' },
                        'time' => F::duration($c['time']),
                        '_tone' => ['status' => match ($c['state']) { 'completed' => 'good', 'in_progress' => 'warn', default => null }],
                    ]);
                }
            }
        }

        [$mcqPage, $cutA] = F::page($mcqRows, 'mcq', $all);
        [$codingPage, $cutB] = F::page($codingRows, 'coding', $all);

        return new ReportResult(
            'challenges',
            'Challenges & Coding Challenges',
            'Results on the challenges you gave to your classes. An MCQ challenge is completed at '.F::PASS_PERCENT.'% or more; a coding challenge when every problem is solved.',
            [
                ['label' => 'Challenge results', 'value' => F::number($mcqRows->count()), 'note' => $mcqRows->where('status', 'Completed')->count().' completed'],
                ['label' => 'Coding challenge results', 'value' => F::number($codingRows->count()), 'note' => $codingRows->where('status', 'Completed')->count().' completed'],
            ],
            [
                new ReportTable('mcq', 'Challenges', [
                    'student' => 'Student', 'class' => 'Class', 'challenge' => 'Challenge', 'attempts' => 'Attempts', 'score' => 'Best score', 'status' => 'Completion status',
                ], $mcqPage, 'No challenge results match these filters.', null, $cutA),
                new ReportTable('coding', 'Coding challenges', [
                    'student' => 'Student', 'class' => 'Class', 'challenge' => 'Coding challenge', 'attempts' => 'Attempts', 'score' => 'Score',
                    'tests' => 'Test cases (best runs)', 'status' => 'Completion status', 'time' => 'Completion time',
                ], $codingPage, 'No coding challenge results match these filters.', 'Score is the share of tests passed on each problem\'s best run. Completion time adds up the time of each problem\'s first passing run.', $cutB),
            ],
        );
    }

    // ── 5. Module Assignments ────────────────────────────────────────

    private function modules(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $rows = collect();
        foreach ($classes as $class) {
            $snapshot = $this->progress->forClass($class);
            $studentIds = array_keys($snapshot['perStudent']);
            $progress = DB::table('module_library_progress')
                ->whereIn('user_id', $studentIds ?: [0])
                ->whereIn('module_library_item_id', $snapshot['moduleAssignments']->pluck('id')->all() ?: [0])
                ->get(['user_id', 'module_library_item_id', 'opened_at', 'completed_at'])
                ->groupBy('module_library_item_id');

            foreach ($snapshot['moduleAssignments'] as $assignment) {
                if (($f->moduleId && (int) $assignment->id !== $f->moduleId) || ($f->status !== '' && $assignment->status !== $f->status)) {
                    continue;
                }
                $own = $progress->get($assignment->id, collect());
                $started = $own->whereNotNull('opened_at')->count();
                $completed = $own->whereNotNull('completed_at')->count();
                $rows->push([
                    'class' => $class->name,
                    'module' => $assignment->title.($assignment->version_name ? ' ('.$assignment->version_name.')' : ''),
                    'assigned' => F::date($assignment->assigned_at ?? $assignment->created_at),
                    'status' => $assignment->status === 'active' ? 'Assigned' : 'Removed',
                    'started' => F::number($started).' of '.F::number(count($studentIds)),
                    'completed' => F::number($completed).' of '.F::number(count($studentIds)),
                    'rate' => F::pct(F::percent($completed, count($studentIds))),
                    '_rate' => F::percent($completed, count($studentIds)),
                    '_tone' => ['rate' => F::tone(F::percent($completed, count($studentIds)))],
                ]);
            }
        }

        [$page, $cut] = F::page($rows, 'modules', $all);

        return new ReportResult(
            'modules',
            'Module Assignments',
            'The modules you assigned to your classes (modules are created by admins) and how many students started and completed each.',
            [
                ['label' => 'Module assignments', 'value' => F::number($rows->count()), 'note' => $rows->where('status', 'Assigned')->count().' still assigned'],
                ['label' => 'Average completion rate', 'value' => F::pct($this->averageOf($rows, '_rate'))],
            ],
            [new ReportTable('modules', 'Assigned modules', [
                'class' => 'Class', 'module' => 'Assigned module', 'assigned' => 'Date assigned', 'status' => 'Status',
                'started' => 'Students who started', 'completed' => 'Students who completed', 'rate' => 'Completion rate',
            ], $page, 'No module assignments match these filters.', 'A student starts a module by opening it and completes it with "Mark module as complete". Counts use the students enrolled now.', $cut)],
        );
    }

    // ── 6. Submissions ───────────────────────────────────────────────

    private function submissions(Collection $classes, ReportFilters $f, bool $all): ReportResult
    {
        $rows = collect();
        $classIds = $classes->pluck('id')->all() ?: [0];
        $classNames = $classes->pluck('name', 'id');
        $type = isset(self::TYPES['submissions'][$f->type]) ? $f->type : null;

        if ($type === null || $type === 'assignment') {
            $f->applyDate(DB::table('assignment_submissions as s')
                ->join('class_assignments as a', 'a.id', '=', 's.class_assignment_id')
                ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'a.class_id')->on('cs.student_id', '=', 's.student_id'))
                ->join('users as u', 'u.id', '=', 's.student_id')
                ->whereIn('a.class_id', $classIds)
                ->whereIn('s.status', ['submitted', 'late', 'graded']), 's.submitted_at')
                ->get(['u.name as student', 'a.class_id', 'a.title', 'a.due_at', 's.status', 's.score', 's.total_points', 's.submitted_at'])
                ->each(function ($s) use ($rows): void {
                    $late = ClassProgress::isLate($s, $s->due_at);
                    $rows->push($this->submissionRow($s, 'assignment', 'Assignment', $late, $s->due_at !== null));
                });
        }

        if ($type === null || $type === 'assessment') {
            $f->applyDate(DB::table('assessment_submissions as s')
                ->join('assessments as a', 'a.id', '=', 's.assessment_id')
                ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'a.class_id')->on('cs.student_id', '=', 's.student_id'))
                ->join('users as u', 'u.id', '=', 's.student_id')
                ->whereIn('a.class_id', $classIds)
                ->whereIn('s.status', ['submitted', 'late', 'graded']), 's.submitted_at')
                ->get(['u.name as student', 'a.class_id', 'a.title', 'a.due_at', 's.status', 's.score', 's.total_points', 's.submitted_at'])
                ->each(function ($s) use ($rows): void {
                    $late = ClassProgress::isLate($s, $s->due_at);
                    $rows->push($this->submissionRow($s, 'assessment', 'Assessment', $late, $s->due_at !== null));
                });
        }

        if ($type === null || $type === 'challenge') {
            $f->applyDate(DB::table('challenge_attempts as t')
                ->join('class_challenge_assignments as c', 'c.challenge_id', '=', 't.challenge_id')
                ->join('challenges as ch', 'ch.id', '=', 't.challenge_id')
                ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'c.class_id')->on('cs.student_id', '=', 't.user_id'))
                ->join('users as u', 'u.id', '=', 't.user_id')
                ->whereIn('c.class_id', $classIds)
                ->where('ch.is_coding_challenge', 0)
                ->where('t.status', 'submitted'), 't.submitted_at')
                ->get(['u.name as student', 'c.class_id', 'ch.title', 'c.due_at', 't.score', 't.total_questions', 't.submitted_at'])
                ->each(function ($t) use ($rows): void {
                    $percent = ClassProgress::scorePercent($t->score, $t->total_questions);
                    $late = $t->due_at && $t->submitted_at && $t->submitted_at > $t->due_at;
                    $rows->push([
                        'student' => $t->student,
                        '_class_id' => (int) $t->class_id,
                        'type' => 'Challenge',
                        '_type' => 'challenge',
                        'title' => $t->title,
                        'date' => F::dateTime($t->submitted_at),
                        '_date' => (string) $t->submitted_at,
                        'status' => $percent !== null && $percent >= F::PASS_PERCENT ? 'Passed' : 'Below '.F::PASS_PERCENT.'%',
                        '_status' => [$percent !== null && $percent >= F::PASS_PERCENT ? 'passed' : 'failed', $late ? 'late' : 'on_time'],
                        'score' => $t->score.' / '.$t->total_questions.' ('.F::pct($percent).')',
                        'timing' => $t->due_at ? ($late ? 'Late' : 'On time') : F::NONE,
                    ]);
                });
        }

        if ($type === null || $type === 'coding') {
            $f->applyDate(DB::table('coding_submissions as s')
                ->join('coding_questions as q', 'q.id', '=', 's.coding_question_id')
                ->join('class_challenge_assignments as c', 'c.challenge_id', '=', 'q.challenge_id')
                ->join('challenges as ch', 'ch.id', '=', 'q.challenge_id')
                ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'c.class_id')->on('cs.student_id', '=', 's.user_id'))
                ->join('users as u', 'u.id', '=', 's.user_id')
                ->whereIn('c.class_id', $classIds)
                ->where('s.voided', false), 's.created_at')
                ->get(['u.name as student', 'c.class_id', 'ch.title', 'q.title as problem', 'c.due_at', 's.status', 's.tests_passed', 's.tests_total', 's.created_at'])
                ->each(function ($s) use ($rows): void {
                    $late = $s->due_at && $s->created_at > $s->due_at;
                    $passed = $s->status === 'passed';
                    $rows->push([
                        'student' => $s->student,
                        '_class_id' => (int) $s->class_id,
                        'type' => 'Coding challenge',
                        '_type' => 'coding',
                        'title' => $s->title.($s->problem ? ': '.$s->problem : ''),
                        'date' => F::dateTime($s->created_at),
                        '_date' => (string) $s->created_at,
                        'status' => $passed ? 'Passed' : ucfirst((string) $s->status),
                        '_status' => [$passed ? 'passed' : 'failed', $late ? 'late' : 'on_time'],
                        'score' => (int) $s->tests_total > 0 ? $s->tests_passed.' of '.$s->tests_total.' tests' : F::NONE,
                        'timing' => $s->due_at ? ($late ? 'Late' : 'On time') : F::NONE,
                    ]);
                });
        }

        $search = mb_strtolower($f->search);
        $rows = $rows
            ->filter(fn (array $row) => $search === '' || str_contains(mb_strtolower($row['student'].' '.$row['title']), $search))
            ->filter(fn (array $row) => $f->status === '' || in_array($f->status, $row['_status'], true))
            ->map(fn (array $row) => $row + ['class' => (string) ($classNames[$row['_class_id']] ?? '')])
            ->sortByDesc('_date')
            ->values();

        [$page, $cut] = F::page($rows, 'submissions', $all);

        return new ReportResult(
            'submissions',
            'Submissions',
            'Every assignment and assessment submission and every attempt on the challenges given to your classes, newest first.',
            [
                ['label' => 'Submissions', 'value' => F::number($rows->count()), 'note' => $f->rangeText()],
                ['label' => 'Late', 'value' => F::number($rows->filter(fn ($r) => in_array('late', $r['_status'], true))->count())],
                ['label' => 'Awaiting grade', 'value' => F::number($rows->filter(fn ($r) => in_array('awaiting', $r['_status'], true))->count())],
            ],
            [new ReportTable('submissions', 'Submissions', [
                'student' => 'Student', 'class' => 'Class', 'type' => 'Activity type', 'title' => 'Activity', 'date' => 'Submitted',
                'status' => 'Status', 'score' => 'Score or grade', 'timing' => 'Late or on time',
            ], $page, 'No submissions match these filters.', null, $cut)],
        );
    }

    private function submissionRow(object $s, string $type, string $label, bool $late, bool $hasDue): array
    {
        $percent = ClassProgress::scorePercent($s->score, $s->total_points);
        $awaiting = $s->score === null;

        $statuses = [$late ? 'late' : 'on_time'];
        if ($awaiting) {
            $statuses[] = 'awaiting';
        } elseif ($percent !== null) {
            $statuses[] = $percent >= F::PASS_PERCENT ? 'passed' : 'failed';
        }

        return [
            'student' => $s->student,
            '_class_id' => (int) $s->class_id,
            'type' => $label,
            '_type' => $type,
            'title' => $s->title,
            'date' => F::dateTime($s->submitted_at),
            '_date' => (string) $s->submitted_at,
            'status' => match (true) {
                $awaiting => 'Awaiting grade',
                $s->status === 'graded' => 'Graded',
                $late => 'Submitted late',
                default => 'Submitted',
            },
            '_status' => $statuses,
            'score' => $awaiting ? F::NONE : rtrim(rtrim(number_format((float) $s->score, 2, '.', ''), '0'), '.').' / '.rtrim(rtrim(number_format((float) $s->total_points, 2, '.', ''), '0'), '.').' ('.F::pct($percent).')',
            'timing' => $hasDue ? ($late ? 'Late' : 'On time') : F::NONE,
            '_tone' => ['timing' => $late ? 'warn' : null],
        ];
    }

    /** Average of a numeric column across the rows that have a value. */
    private function averageOf(Collection $rows, string $column): ?float
    {
        $values = $rows->pluck($column)->filter(fn ($value) => $value !== null);

        return $values->isEmpty() ? null : round((float) $values->avg(), 1);
    }
}
