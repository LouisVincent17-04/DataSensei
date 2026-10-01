<?php

namespace App\Services\Reports;

use App\Models\ClassRoom;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportFormat as F;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * How a class is doing, read from the saved records (DataSensei Updates 8).
 *
 * One place computes class progress for the instructor's Class Analytics and
 * Reports (and the admin assessment and assignment figures), so they always
 * agree. Everything is rule based and uses only what students did: modules
 * opened and completed, assignments and assessments submitted, and attempts on
 * the challenges given to the class. No ILO mastery, no rankings.
 *
 * Rules used throughout:
 *   - an assessment, assignment or MCQ challenge is passed at 70% or more
 *     (ReportFormat::PASS_PERCENT), using the student's best attempt;
 *   - an assignment is missing when it was not submitted after its due date
 *     (or after it was closed); it is late when submitted after the due date;
 *   - a coding challenge is completed when every problem in it is solved;
 *   - a student is active with any class activity in the last 14 days.
 */
class ClassProgress
{
    public const ACTIVE_DAYS = 14;

    /** Students are flagged for attention by these rules, nothing else. */
    public const ATTENTION_RULES = [
        'missing' => 'Missing 2 or more assignments',
        'assessments' => 'Assessment average below 70%',
        'challenges' => 'Challenge average below 70%',
        'coding' => '3 or more failed attempts on an unsolved coding problem',
        'modules' => 'Completed under 25% of modules assigned a week or more ago',
    ];

    private const DONE = ['submitted', 'late', 'graded'];

    /** @var array<int, array<string, mixed>> */
    private array $cache = [];

    // ── Shared figures (admin report too) ────────────────────────────

    public static function emptyAssessmentStats(): array
    {
        return ['attempts' => 0, 'average' => null, 'passed' => 0, 'failed' => 0, 'students_completed' => 0];
    }

    public static function emptyAssignmentStats(): array
    {
        return ['submitted' => 0, 'late' => 0, 'on_time' => 0, 'average' => null];
    }

    /** Past its due date, or closed or archived. */
    public static function isPastDue(object|array $assignment): bool
    {
        $assignment = (object) $assignment;
        $status = (string) ($assignment->status ?? '');

        return in_array($status, ['closed', 'archived'], true)
            || (! empty($assignment->due_at) && CarbonImmutable::parse($assignment->due_at)->isPast());
    }

    /**
     * Attempts, average score, pass and fail counts per assessment (each
     * student's best attempt decides pass or fail).
     *
     * @param  list<int>  $assessmentIds
     * @return array<int, array<string, mixed>>
     */
    public function assessmentStats(array $assessmentIds): array
    {
        if ($assessmentIds === []) {
            return [];
        }

        $rows = DB::table('assessment_submissions')
            ->whereIn('assessment_id', $assessmentIds)
            ->whereIn('status', self::DONE)
            ->get(['assessment_id', 'student_id', 'score', 'total_points']);

        $stats = [];
        foreach ($rows->groupBy('assessment_id') as $assessmentId => $submissions) {
            $scores = $submissions->map(fn ($s) => self::scorePercent($s->score, $s->total_points))->filter(fn ($p) => $p !== null);
            $best = $submissions->groupBy('student_id')->map(fn ($own) => $own->map(fn ($s) => self::scorePercent($s->score, $s->total_points))->filter(fn ($p) => $p !== null)->max());
            $scored = $best->filter(fn ($p) => $p !== null);

            $stats[(int) $assessmentId] = [
                'attempts' => $submissions->count(),
                'average' => $scores->isEmpty() ? null : round($scores->avg(), 1),
                'passed' => $scored->filter(fn ($p) => $p >= F::PASS_PERCENT)->count(),
                'failed' => $scored->filter(fn ($p) => $p < F::PASS_PERCENT)->count(),
                'students_completed' => $best->count(),
            ];
        }

        return $stats;
    }

    /**
     * Submitted, late, on-time counts and the average grade per assignment,
     * from each student's latest submission.
     *
     * @param  Collection<int, object>  $assignments  rows with id and due_at
     * @return array<int, array<string, mixed>>
     */
    public function assignmentStats(Collection $assignments): array
    {
        if ($assignments->isEmpty()) {
            return [];
        }

        $dueDates = $assignments->pluck('due_at', 'id');
        $rows = DB::table('assignment_submissions')
            ->whereIn('class_assignment_id', $assignments->pluck('id')->all())
            ->whereIn('status', self::DONE)
            ->orderBy('attempt_no')
            ->get(['class_assignment_id', 'student_id', 'status', 'score', 'total_points', 'submitted_at']);

        $stats = [];
        foreach ($rows->groupBy('class_assignment_id') as $assignmentId => $submissions) {
            $latest = $submissions->groupBy('student_id')->map(fn ($own) => $own->last());
            $late = $latest->filter(fn ($s) => self::isLate($s, $dueDates[$assignmentId] ?? null))->count();
            $grades = $latest->map(fn ($s) => self::scorePercent($s->score, $s->total_points))->filter(fn ($p) => $p !== null);

            $stats[(int) $assignmentId] = [
                'submitted' => $latest->count(),
                'late' => $late,
                'on_time' => $latest->count() - $late,
                'average' => $grades->isEmpty() ? null : round($grades->avg(), 1),
            ];
        }

        return $stats;
    }

    /** @param  array<int, array<string, mixed>>  $stats */
    public function weightedAverage(array $stats): ?float
    {
        $weight = 0;
        $sum = 0.0;
        foreach ($stats as $row) {
            if ($row['average'] !== null) {
                $weight += $row['attempts'];
                $sum += $row['average'] * $row['attempts'];
            }
        }

        return $weight > 0 ? round($sum / $weight, 1) : null;
    }

    // ── One class ────────────────────────────────────────────────────

    /**
     * Everything the analytics and reports show about one class. With a
     * window, only the class work due (or created, when it has no due date)
     * in that range is included.
     *
     * @return array<string, mixed>
     */
    public function forClass(ClassRoom $class, ?ReportFilters $window = null): array
    {
        $cacheKey = $class->id.'|'.($window?->from?->toDateTimeString() ?? '').'|'.($window?->to?->toDateTimeString() ?? '');
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $inWindow = fn (object $item): bool => $window === null || ! $window->hasDateRange()
            || $window->inRange(($item->due_at ?? null) ?: ($item->created_at ?? null));

        $students = DB::table('class_student')
            ->join('users', 'users.id', '=', 'class_student.student_id')
            ->where('class_student.class_id', $class->id)
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'users.email', 'class_student.enrolled_at'])
            ->keyBy('id');
        $studentIds = $students->keys()->map(fn ($id) => (int) $id)->all();
        $ids = $studentIds === [] ? [0] : $studentIds;

        // Modules given to the class (removed ones are kept for the record).
        $moduleAssignments = DB::table('class_module_assignments as a')
            ->join('module_library_items as m', 'm.id', '=', 'a.module_library_item_id')
            ->where('a.class_id', $class->id)
            ->orderBy('m.module_no')->orderBy('m.version_no')
            ->get(['a.id as assignment_id', 'a.status', 'a.assigned_at', 'a.created_at', 'm.id', 'm.title', 'm.version_name', 'm.module_no']);
        $modules = $moduleAssignments->where('status', 'active')->keyBy('id');
        $moduleProgress = DB::table('module_library_progress')
            ->whereIn('user_id', $ids)
            ->whereIn('module_library_item_id', $moduleAssignments->pluck('id')->all() ?: [0])
            ->get(['user_id', 'module_library_item_id', 'opened_at', 'last_opened_at', 'completed_at'])
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->keyBy('module_library_item_id'));

        // Assignments and assessments students can see.
        $assignments = DB::table('class_assignments')
            ->where('class_id', $class->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('due_at')->orderBy('id')
            ->get(['id', 'title', 'status', 'due_at', 'available_at', 'created_at'])
            ->filter($inWindow)->keyBy('id');
        $assignmentSubs = DB::table('assignment_submissions')
            ->whereIn('class_assignment_id', $assignments->keys()->all() ?: [0])
            ->whereIn('student_id', $ids)
            ->orderBy('attempt_no')
            ->get(['id', 'class_assignment_id', 'student_id', 'attempt_no', 'status', 'score', 'total_points', 'submitted_at', 'started_at'])
            ->groupBy('student_id');

        $assessments = DB::table('assessments')
            ->where('class_id', $class->id)
            ->whereIn('status', ['published', 'closed'])
            ->orderBy('due_at')->orderBy('id')
            ->get(['id', 'title', 'status', 'due_at', 'created_at', 'max_attempts'])
            ->filter($inWindow)->keyBy('id');
        $assessmentSubs = DB::table('assessment_submissions')
            ->whereIn('assessment_id', $assessments->keys()->all() ?: [0])
            ->whereIn('student_id', $ids)
            ->orderBy('attempt_no')
            ->get(['id', 'assessment_id', 'student_id', 'attempt_no', 'status', 'score', 'total_points', 'submitted_at', 'started_at'])
            ->groupBy('student_id');

        // Challenges given to the class.
        $given = DB::table('class_challenge_assignments as a')
            ->join('challenges as c', 'c.id', '=', 'a.challenge_id')
            ->where('a.class_id', $class->id)
            ->whereIn('a.status', ['published', 'closed'])
            ->orderBy('a.due_at')->orderBy('a.id')
            ->get(['a.id as assignment_id', 'a.title as given_title', 'a.due_at', 'a.created_at', 'a.status', 'c.id', 'c.title', 'c.is_coding_challenge'])
            ->filter($inWindow)
            ->unique('id');
        $mcq = $given->where('is_coding_challenge', 0)->keyBy('id');
        $coding = $given->where('is_coding_challenge', '!=', 0)->keyBy('id');

        $attempts = DB::table('challenge_attempts')
            ->whereIn('challenge_id', $mcq->keys()->all() ?: [0])
            ->whereIn('user_id', $ids)
            ->where('status', 'submitted')
            ->get(['id', 'user_id', 'challenge_id', 'score', 'total_questions', 'submitted_at', 'time_taken_seconds'])
            ->groupBy('user_id');

        $problems = DB::table('coding_questions')
            ->whereIn('challenge_id', $coding->keys()->all() ?: [0])
            ->orderBy('order_index')
            ->get(['id', 'challenge_id', 'title']);
        $problemCounts = $problems->countBy('challenge_id');
        $codingSubs = DB::table('coding_submissions')
            ->whereIn('coding_question_id', $problems->pluck('id')->all() ?: [0])
            ->whereIn('user_id', $ids)
            ->where('voided', false)
            ->orderBy('id')
            ->get(['id', 'user_id', 'coding_question_id', 'status', 'tests_passed', 'tests_total', 'time_taken_seconds', 'created_at'])
            ->groupBy('user_id');
        $problemChallenge = $problems->pluck('challenge_id', 'id');

        $now = CarbonImmutable::now();
        $weekAgo = $now->subDays(7);
        $activeSince = $now->subDays(self::ACTIVE_DAYS);

        $perStudent = [];
        foreach ($students as $studentId => $student) {
            $studentId = (int) $studentId;
            $lastActivity = null;
            $touch = function ($date) use (&$lastActivity): void {
                if ($date && ($lastActivity === null || (string) $date > (string) $lastActivity)) {
                    $lastActivity = (string) $date;
                }
            };

            // Modules.
            $progress = $moduleProgress->get($studentId, collect());
            $moduleRows = [];
            foreach ($modules as $moduleId => $module) {
                $p = $progress->get($moduleId);
                $moduleRows[$moduleId] = [
                    'state' => $p?->completed_at ? 'completed' : ($p?->opened_at ? 'started' : 'not_started'),
                    'opened_at' => $p?->opened_at,
                    'completed_at' => $p?->completed_at,
                ];
                $touch($p?->last_opened_at);
                $touch($p?->completed_at);
            }
            $modulesDone = collect($moduleRows)->where('state', 'completed')->count();
            $olderModules = $modules->filter(fn ($m) => CarbonImmutable::parse($m->assigned_at ?? $m->created_at ?? $now)->lessThanOrEqualTo($weekAgo));
            $olderDone = $olderModules->keys()->filter(fn ($id) => ($moduleRows[$id]['state'] ?? '') === 'completed')->count();

            // Assignments.
            $own = $assignmentSubs->get($studentId, collect())->groupBy('class_assignment_id');
            $assignmentRows = [];
            foreach ($assignments as $assignmentId => $assignment) {
                $subs = $own->get($assignmentId, collect());
                $done = $subs->whereIn('status', self::DONE);
                $latest = $done->last();
                $late = $latest ? self::isLate($latest, $assignment->due_at) : false;
                $state = match (true) {
                    $latest !== null => $late ? 'late' : 'submitted',
                    self::isPastDue($assignment) => 'missing',
                    $subs->isNotEmpty() => 'in_progress',
                    default => 'pending',
                };
                $assignmentRows[$assignmentId] = [
                    'state' => $state,
                    'submitted_at' => $latest?->submitted_at,
                    'late' => $late,
                    'score' => $latest ? self::scorePercent($latest->score, $latest->total_points) : null,
                    'score_text' => $latest ? self::scoreText($latest->score, $latest->total_points, $latest->status) : F::NONE,
                    'attempts' => $done->count(),
                    'graded' => $latest?->status === 'graded',
                ];
                foreach ($subs as $sub) {
                    $touch($sub->submitted_at);
                }
            }
            $assignmentStates = collect($assignmentRows)->pluck('state');

            // Assessments.
            $own = $assessmentSubs->get($studentId, collect())->groupBy('assessment_id');
            $assessmentRows = [];
            foreach ($assessments as $assessmentId => $assessment) {
                $subs = $own->get($assessmentId, collect());
                $done = $subs->whereIn('status', self::DONE);
                $scores = $done->map(fn ($s) => self::scorePercent($s->score, $s->total_points))->filter(fn ($p) => $p !== null);
                $best = $scores->isEmpty() ? null : round($scores->max(), 1);
                $bestSub = $done->sortByDesc(fn ($s) => self::scorePercent($s->score, $s->total_points) ?? -1)->first();
                $assessmentRows[$assessmentId] = [
                    'state' => $done->isNotEmpty() ? 'completed' : ($subs->isNotEmpty() ? 'in_progress' : 'not_started'),
                    'attempts' => $done->count(),
                    'best' => $best,
                    'score_text' => $bestSub ? self::scoreText($bestSub->score, $bestSub->total_points, $bestSub->status) : F::NONE,
                    'passed' => $best !== null ? $best >= F::PASS_PERCENT : null,
                    'submitted_at' => $done->max('submitted_at'),
                    'awaiting_review' => $done->isNotEmpty() && $done->every(fn ($s) => $s->status === 'submitted' && $s->score === null),
                ];
                foreach ($subs as $sub) {
                    $touch($sub->submitted_at);
                }
            }
            $assessmentBests = collect($assessmentRows)->pluck('best')->filter(fn ($p) => $p !== null);

            // MCQ challenges.
            $own = $attempts->get($studentId, collect())->groupBy('challenge_id');
            $challengeRows = [];
            foreach ($mcq as $challengeId => $challenge) {
                $tries = $own->get($challengeId, collect());
                $scores = $tries->map(fn ($t) => self::scorePercent($t->score, $t->total_questions))->filter(fn ($p) => $p !== null);
                $best = $scores->isEmpty() ? null : round($scores->max(), 1);
                $bestTry = $tries->sortByDesc(fn ($t) => self::scorePercent($t->score, $t->total_questions) ?? -1)->first();
                $challengeRows[$challengeId] = [
                    'state' => $best === null ? 'not_started' : ($best >= F::PASS_PERCENT ? 'passed' : 'attempted'),
                    'attempts' => $tries->count(),
                    'best' => $best,
                    'score_text' => $bestTry ? $bestTry->score.' / '.$bestTry->total_questions : F::NONE,
                    'last_at' => $tries->max('submitted_at'),
                ];
                $touch($tries->max('submitted_at'));
            }
            $challengeBests = collect($challengeRows)->pluck('best')->filter(fn ($p) => $p !== null);

            // Coding challenges.
            $own = $codingSubs->get($studentId, collect())->groupBy(fn ($s) => (int) ($problemChallenge[$s->coding_question_id] ?? 0));
            $codingRows = [];
            $repeatedFailures = 0;
            foreach ($coding as $challengeId => $challenge) {
                $subs = $own->get((int) $challengeId, collect());
                $byProblem = $subs->groupBy('coding_question_id');
                $total = (int) ($problemCounts[$challengeId] ?? 0);
                $solved = 0;
                $time = 0;
                $bestPercents = [];
                $testsPassed = 0;
                $testsFailed = 0;
                foreach ($byProblem as $problemSubs) {
                    $pass = $problemSubs->firstWhere('status', 'passed');
                    if ($pass) {
                        $solved++;
                        $time += (int) $pass->time_taken_seconds;
                    } elseif ($problemSubs->whereIn('status', ['failed', 'error'])->count() >= 3) {
                        $repeatedFailures++;
                    }
                    $best = $problemSubs->sortByDesc(fn ($s) => self::scorePercent($s->tests_passed, $s->tests_total) ?? -1)->first();
                    $bestPercents[] = self::scorePercent($best->tests_passed, $best->tests_total) ?? 0.0;
                    $testsPassed += (int) $best->tests_passed;
                    $testsFailed += max(0, (int) $best->tests_total - (int) $best->tests_passed);
                }
                $score = $subs->isEmpty() || $total === 0 ? null : round(array_sum($bestPercents) / max($total, count($bestPercents)), 1);
                $codingRows[$challengeId] = [
                    'state' => $subs->isEmpty() ? 'not_started' : ($total > 0 && $solved >= $total ? 'completed' : 'in_progress'),
                    'submissions' => $subs->count(),
                    'solved' => $solved,
                    'problems' => $total,
                    'score' => $score,
                    'tests_passed' => $testsPassed,
                    'tests_failed' => $testsFailed,
                    'failed' => $subs->whereIn('status', ['failed', 'error'])->count(),
                    'time' => $total > 0 && $solved >= $total ? $time : null,
                    'last_at' => $subs->max('created_at'),
                ];
                $touch($subs->max('created_at'));
            }

            $components = array_filter([
                $modules->isNotEmpty() ? F::percent($modulesDone, $modules->count()) : null,
                $assignments->isNotEmpty() ? F::percent($assignmentStates->filter(fn ($s) => in_array($s, ['submitted', 'late'], true))->count(), $assignments->count()) : null,
                $assessments->isNotEmpty() ? F::percent(collect($assessmentRows)->where('state', 'completed')->count(), $assessments->count()) : null,
                $mcq->isNotEmpty() ? F::percent(collect($challengeRows)->where('state', 'passed')->count(), $mcq->count()) : null,
                $coding->isNotEmpty() ? F::percent(collect($codingRows)->where('state', 'completed')->count(), $coding->count()) : null,
            ], fn ($value) => $value !== null);

            $summary = [
                'modules_total' => $modules->count(),
                'modules_completed' => $modulesDone,
                'modules_started' => collect($moduleRows)->whereIn('state', ['started', 'completed'])->count(),
                'module_percent' => $modules->isNotEmpty() ? F::percent($modulesDone, $modules->count()) : null,
                'assignments_total' => $assignments->count(),
                'assignments_submitted' => $assignmentStates->filter(fn ($s) => in_array($s, ['submitted', 'late'], true))->count(),
                'assignments_missing' => $assignmentStates->filter(fn ($s) => $s === 'missing')->count(),
                'assignments_late' => $assignmentStates->filter(fn ($s) => $s === 'late')->count(),
                'assessments_total' => $assessments->count(),
                'assessments_completed' => collect($assessmentRows)->where('state', 'completed')->count(),
                'assessment_average' => $assessmentBests->isEmpty() ? null : round($assessmentBests->avg(), 1),
                'challenges_total' => $mcq->count(),
                'challenges_attempted' => collect($challengeRows)->where('state', '!=', 'not_started')->count(),
                'challenges_passed' => collect($challengeRows)->where('state', 'passed')->count(),
                'challenge_attempts' => (int) collect($challengeRows)->sum('attempts'),
                'challenge_average' => $challengeBests->isEmpty() ? null : round($challengeBests->avg(), 1),
                'coding_total' => $coding->count(),
                'coding_completed' => collect($codingRows)->where('state', 'completed')->count(),
                'coding_started' => collect($codingRows)->where('state', '!=', 'not_started')->count(),
                'coding_submissions' => (int) collect($codingRows)->sum('submissions'),
                'coding_score' => collect($codingRows)->pluck('score')->filter(fn ($p) => $p !== null)->avg(),
                'repeated_failures' => $repeatedFailures,
                'overall' => $components === [] ? null : round(array_sum($components) / count($components), 1),
                'last_activity' => $lastActivity,
                'active' => $lastActivity !== null && CarbonImmutable::parse($lastActivity)->greaterThanOrEqualTo($activeSince),
            ];
            $summary['coding_score'] = $summary['coding_score'] === null ? null : round((float) $summary['coding_score'], 1);

            $attention = [];
            if ($summary['assignments_missing'] >= 2) {
                $attention['missing'] = 'Missing '.$summary['assignments_missing'].' assignments';
            }
            if ($summary['assessment_average'] !== null && $summary['assessment_average'] < F::PASS_PERCENT) {
                $attention['assessments'] = 'Assessment average '.F::pct($summary['assessment_average']);
            }
            if ($summary['challenge_average'] !== null && $summary['challenge_average'] < F::PASS_PERCENT) {
                $attention['challenges'] = 'Challenge average '.F::pct($summary['challenge_average']);
            }
            if ($repeatedFailures > 0) {
                $attention['coding'] = 'Repeated failures on '.$repeatedFailures.' coding '.($repeatedFailures === 1 ? 'problem' : 'problems');
            }
            if ($olderModules->isNotEmpty() && F::percent($olderDone, $olderModules->count()) < 25) {
                $attention['modules'] = 'Completed '.$olderDone.' of '.$olderModules->count().' modules';
            }
            $summary['attention'] = $attention;

            $perStudent[$studentId] = [
                'student' => $student,
                'summary' => $summary,
                'modules' => $moduleRows,
                'assignments' => $assignmentRows,
                'assessments' => $assessmentRows,
                'challenges' => $challengeRows,
                'coding' => $codingRows,
            ];
        }

        $snapshot = [
            'class' => $class,
            'students' => $students,
            'moduleAssignments' => $moduleAssignments,
            'modules' => $modules,
            'assignments' => $assignments,
            'assessments' => $assessments,
            'challenges' => $mcq,
            'coding' => $coding,
            'problems' => $problems,
            'perStudent' => $perStudent,
            'overview' => $this->overview($students->count(), $modules->count(), $assignments->count(), $assessments->count(), $mcq->count(), $coding->count(), $perStudent),
        ];

        return $this->cache[$cacheKey] = $snapshot;
    }

    /**
     * Recent learning activity of one student in the class, newest first.
     *
     * @param  array<string, mixed>  $snapshot
     * @return list<array{when: string, type: string, title: string, detail: string}>
     */
    public function recentActivity(array $snapshot, int $studentId, ?ReportFilters $filters = null, int $limit = 25): array
    {
        $row = $snapshot['perStudent'][$studentId] ?? null;
        if ($row === null) {
            return [];
        }

        $items = collect();
        foreach ($row['modules'] as $moduleId => $module) {
            $title = $snapshot['modules'][$moduleId]->title ?? 'Module';
            if ($module['completed_at']) {
                $items->push(['when' => $module['completed_at'], 'type' => 'module', 'title' => $title, 'detail' => 'Marked complete']);
            }
            if ($module['opened_at']) {
                $items->push(['when' => $module['opened_at'], 'type' => 'module', 'title' => $title, 'detail' => 'First opened']);
            }
        }
        foreach ($row['assignments'] as $id => $a) {
            if ($a['submitted_at']) {
                $items->push(['when' => $a['submitted_at'], 'type' => 'assignment', 'title' => $snapshot['assignments'][$id]->title, 'detail' => ($a['late'] ? 'Submitted late' : 'Submitted').($a['score'] !== null ? ', '.F::pct($a['score']) : '')]);
            }
        }
        foreach ($row['assessments'] as $id => $a) {
            if ($a['submitted_at']) {
                $items->push(['when' => $a['submitted_at'], 'type' => 'assessment', 'title' => $snapshot['assessments'][$id]->title, 'detail' => $a['attempts'].' '.($a['attempts'] === 1 ? 'attempt' : 'attempts').', best '.F::pct($a['best'])]);
            }
        }
        foreach ($row['challenges'] as $id => $c) {
            if ($c['last_at']) {
                $items->push(['when' => $c['last_at'], 'type' => 'challenge', 'title' => $snapshot['challenges'][$id]->title, 'detail' => $c['attempts'].' '.($c['attempts'] === 1 ? 'attempt' : 'attempts').', best '.$c['score_text']]);
            }
        }
        foreach ($row['coding'] as $id => $c) {
            if ($c['last_at']) {
                $items->push(['when' => $c['last_at'], 'type' => 'coding', 'title' => $snapshot['coding'][$id]->title, 'detail' => $c['solved'].' of '.$c['problems'].' problems solved, '.$c['submissions'].' '.($c['submissions'] === 1 ? 'submission' : 'submissions')]);
            }
        }

        return $items
            ->when($filters && $filters->type !== '', fn (Collection $c) => $c->where('type', $filters->type))
            ->filter(fn (array $item) => $filters === null || $filters->inRange($item['when']))
            ->sortByDesc('when')
            ->take($limit)
            ->values()
            ->all();
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * @param  array<int, array<string, mixed>>  $perStudent
     * @return array<string, mixed>
     */
    private function overview(int $students, int $modules, int $assignments, int $assessments, int $challenges, int $coding, array $perStudent): array
    {
        $summaries = collect($perStudent)->pluck('summary');
        $sum = fn (string $key) => (int) $summaries->sum($key);
        $avg = function (string $key) use ($summaries): ?float {
            $values = $summaries->pluck($key)->filter(fn ($v) => $v !== null);

            return $values->isEmpty() ? null : round($values->avg(), 1);
        };

        return [
            'students' => $students,
            'active' => $summaries->where('active', true)->count(),
            'modules' => $modules,
            'module_completion' => F::percent($sum('modules_completed'), $students * $modules),
            'assignments' => $assignments,
            'assignment_rate' => F::percent($sum('assignments_submitted'), $students * $assignments),
            'assignments_missing' => $sum('assignments_missing'),
            'assignments_late' => $sum('assignments_late'),
            'assessments' => $assessments,
            'assessment_completion' => F::percent($sum('assessments_completed'), $students * $assessments),
            'assessment_average' => $avg('assessment_average'),
            'challenges' => $challenges,
            'challenge_completion' => F::percent($sum('challenges_passed'), $students * $challenges),
            'challenge_average' => $avg('challenge_average'),
            'coding' => $coding,
            'coding_completion' => F::percent($sum('coding_completed'), $students * $coding),
            'coding_score' => $avg('coding_score'),
            'attention' => $summaries->filter(fn ($s) => $s['attention'] !== [])->count(),
        ];
    }

    public static function scorePercent(mixed $score, mixed $total): ?float
    {
        if ($score === null || $total === null || (float) $total <= 0) {
            return null;
        }

        return round(((float) $score / (float) $total) * 100, 1);
    }

    private static function scoreText(mixed $score, mixed $total, string $status): string
    {
        if ($score === null) {
            return $status === 'submitted' ? 'Awaiting grade' : F::NONE;
        }

        $number = fn ($v) => rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');

        return $number($score).' / '.$number($total).' ('.F::pct(self::scorePercent($score, $total)).')';
    }

    public static function isLate(object $submission, mixed $dueAt): bool
    {
        if (($submission->status ?? '') === 'late') {
            return true;
        }

        return $dueAt && ! empty($submission->submitted_at)
            && CarbonImmutable::parse($submission->submitted_at)->greaterThan(CarbonImmutable::parse($dueAt));
    }
}
