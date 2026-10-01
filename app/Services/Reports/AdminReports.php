<?php

namespace App\Services\Reports;

use App\Models\Assessment;
use App\Models\AuditLog;
use App\Models\Challenge;
use App\Models\User;
use App\Support\Reports\ReportExporter;
use App\Support\Reports\ReportFilters;
use App\Support\Reports\ReportFormat as F;
use App\Support\Reports\ReportResult;
use App\Support\Reports\ReportTable;
use Carbon\CarbonImmutable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The admin reports (DataSensei Updates 8): system-wide figures read straight
 * from the saved records. No ILO mastery, rankings or technical analytics.
 *
 *   users          accounts by role and status, new registrations
 *   modules        DataSensei and class modules, who opened and finished them
 *   classes        instructors, enrolment, assigned modules, participation
 *   assessments    class assessments (assignments were merged into
 *                  assessments in DataSensei Updates 11)
 *   challenges     MCQ and coding challenges
 *   gamification   EXP given out, achievements earned, missions completed
 *   audit          important admin and instructor actions
 */
class AdminReports
{
    /** Report keys, titles and the filters each one offers. */
    public const REPORTS = [
        'users' => ['title' => 'Users', 'filters' => ['date', 'search', 'role', 'status']],
        'modules' => ['title' => 'Modules', 'filters' => ['search', 'type', 'status']],
        'classes' => ['title' => 'Classes', 'filters' => ['date', 'search', 'status']],
        'assessments' => ['title' => 'Assessments', 'filters' => ['date', 'search', 'class', 'status']],
        'challenges' => ['title' => 'Challenges & Coding Challenges', 'filters' => ['date', 'search', 'module', 'type']],
        'gamification' => ['title' => 'Gamification', 'filters' => ['date']],
        'audit' => ['title' => 'Audit Logs', 'filters' => ['date', 'search', 'role', 'action']],
    ];

    public const ROLES = [
        'learner' => User::ROLE_USER,
        'instructor' => User::ROLE_INSTRUCTOR,
        'admin' => User::ROLE_ADMIN,
        'superadmin' => User::ROLE_SUPERADMIN,
        'institution_admin' => User::ROLE_INSTITUTION_ADMIN,
    ];

    public const ROLE_NAMES = [
        User::ROLE_USER => 'Learner',
        User::ROLE_INSTRUCTOR => 'Instructor',
        User::ROLE_ADMIN => 'Admin',
        User::ROLE_SUPERADMIN => 'Super Admin',
        User::ROLE_INSTITUTION_ADMIN => 'Institution Admin',
    ];

    /** Status choices per report. */
    public const STATUSES = [
        'users' => ['active' => 'Active', 'disabled' => 'Inactive'],
        'modules' => ['published' => 'Published', 'unpublished' => 'Unpublished'],
        'classes' => ['active' => 'Active', 'archived' => 'Archived'],
        'assessments' => ['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed', 'archived' => 'Archived'],
    ];

    /** Type choices per report. */
    public const TYPES = [
        'modules' => ['datasensei' => 'DataSensei Modules', 'class' => 'Class Modules'],
        'challenges' => ['platform' => 'DataSensei challenges', 'class' => 'Instructor-built challenges'],
    ];

    public function build(string $key, ReportFilters $filters, bool $all = false): ReportResult
    {
        return match ($key) {
            'users' => $this->users($filters, $all),
            'modules' => $this->modules($filters, $all),
            'classes' => $this->classes($filters, $all),
            'assessments' => $this->assessments($filters, $all),
            'challenges' => $this->challenges($filters, $all),
            'gamification' => $this->gamification($filters, $all),
            'audit' => $this->audit($filters, $all),
        };
    }

    /** Choices for the filter menus. */
    public function options(string $key): array
    {
        return match ($key) {
            'assessments' => ['classes' => DB::table('classes')->orderBy('name')->pluck('name', 'id')->all()],
            'challenges' => ['modules' => DB::table('modules')->orderBy('order_index')->pluck('title', 'id')->all()],
            'audit' => ['actions' => AuditLog::ACTIONS],
            default => [],
        };
    }

    // ── 1. Users ─────────────────────────────────────────────────────

    private function users(ReportFilters $f, bool $all): ReportResult
    {
        $role = self::ROLES[$f->role] ?? null;
        $status = array_key_exists($f->status, self::STATUSES['users']) ? $f->status : null;

        $scoped = function () use ($role, $status, $f): Builder {
            return DB::table('users')
                ->when($role !== null, fn (Builder $q) => $q->where('role', $role))
                ->when($status !== null, fn (Builder $q) => $q->where('status', $status))
                ->when($f->search !== '', fn (Builder $q) => $q->where(fn (Builder $w) => $w->where('name', 'like', $f->like())->orWhere('email', 'like', $f->like())));
        };

        $newFrom = $f->from ?? ($f->to === null ? CarbonImmutable::now()->subDays(30)->startOfDay() : null);
        $newQuery = $scoped();
        if ($newFrom) {
            $newQuery->where('created_at', '>=', $newFrom->toDateTimeString());
        }
        if ($f->to) {
            $newQuery->where('created_at', '<=', $f->to->toDateTimeString());
        }

        $byRole = $scoped()->select('role', 'status', DB::raw('COUNT(*) as total'))->groupBy('role', 'status')->get();
        $newByRole = (clone $newQuery)->select('role', DB::raw('COUNT(*) as total'))->groupBy('role')->pluck('total', 'role');
        $active = (int) $byRole->where('status', 'active')->sum('total');
        $inactive = (int) $byRole->where('status', '!=', 'active')->sum('total');

        $roleRows = collect(self::ROLE_NAMES)
            ->map(function (string $name, int $roleId) use ($byRole, $newByRole): array {
                $rows = $byRole->where('role', $roleId);

                return [
                    'role' => $name,
                    'total' => F::number($rows->sum('total')),
                    'active' => F::number($rows->where('status', 'active')->sum('total')),
                    'inactive' => F::number($rows->where('status', '!=', 'active')->sum('total')),
                    'new' => F::number($newByRole[$roleId] ?? 0),
                ];
            })
            ->filter(fn (array $row) => $role === null || $row['role'] === self::ROLE_NAMES[$role])
            ->values();

        $newLabel = $f->hasDateRange() ? 'New registrations' : 'New in the last 30 days';

        $list = $scoped()->when($f->hasDateRange(), fn (Builder $q) => $f->applyDate($q, 'created_at'))
            ->orderByDesc('created_at')->orderByDesc('id')
            ->select('id', 'name', 'email', 'role', 'status', 'created_at', 'last_activity');

        [$rows, $truncated] = $this->pageQuery($list, 'users', $all, fn ($user) => [
            'name' => $user->name,
            'email' => $user->email,
            'role' => self::ROLE_NAMES[(int) $user->role] ?? 'Unknown',
            'status' => $user->status === 'active' ? 'Active' : 'Inactive',
            'registered' => F::date($user->created_at),
            'last_active' => F::date($user->last_activity),
            '_tone' => ['status' => $user->status === 'active' ? 'good' : 'bad'],
        ]);

        return new ReportResult(
            'users',
            'Users',
            'Accounts by role and status, and new registrations.',
            [
                ['label' => 'Total users', 'value' => F::number($active + $inactive)],
                ['label' => 'Active', 'value' => F::number($active), 'note' => 'Can sign in'],
                ['label' => 'Inactive', 'value' => F::number($inactive), 'note' => 'Disabled accounts'],
                ['label' => $newLabel, 'value' => F::number((clone $newQuery)->count()), 'note' => $f->rangeText()],
            ],
            [
                new ReportTable('roles', 'Users by role', [
                    'role' => 'Role', 'total' => 'Total', 'active' => 'Active', 'inactive' => 'Inactive', 'new' => $newLabel,
                ], $roleRows->all()),
                new ReportTable('users', $f->hasDateRange() ? 'Users registered in the selected range' : 'All users', [
                    'name' => 'Name', 'email' => 'Email', 'role' => 'Role', 'status' => 'Account status', 'registered' => 'Registered', 'last_active' => 'Last active',
                ], $rows, 'No users match these filters.', null, $truncated),
            ],
        );
    }

    // ── 2. Modules ───────────────────────────────────────────────────

    private function modules(ReportFilters $f, bool $all): ReportResult
    {
        $learners = DB::table('users')->where('role', User::ROLE_USER)->select('id');

        // DataSensei Modules: opened (Updates 8), finished a lesson, or completed.
        $opened = DB::table('module_user')
            ->whereIn('user_id', $learners)
            ->where(fn (Builder $q) => $q->whereNotNull('opened_at')->orWhere('is_completed', true))
            ->select('module_id', 'user_id');
        $studied = DB::table('lesson_user')
            ->join('lessons', 'lessons.id', '=', 'lesson_user.lesson_id')
            ->whereIn('lesson_user.user_id', $learners)
            ->select('lessons.module_id', 'lesson_user.user_id');
        $publicAccessed = DB::query()->fromSub($opened->union($studied), 'access')
            ->select('module_id', DB::raw('COUNT(DISTINCT user_id) as total'))
            ->groupBy('module_id')
            ->pluck('total', 'module_id');
        $publicCompleted = DB::table('module_user')->whereIn('user_id', $learners)->where('is_completed', true)
            ->select('module_id', DB::raw('COUNT(DISTINCT user_id) as total'))->groupBy('module_id')->pluck('total', 'module_id');

        $classAccessed = DB::table('module_library_progress')->whereIn('user_id', $learners)->whereNotNull('opened_at')
            ->select('module_library_item_id', DB::raw('COUNT(DISTINCT user_id) as total'))->groupBy('module_library_item_id')->pluck('total', 'module_library_item_id');
        $classCompleted = DB::table('module_library_progress')->whereIn('user_id', $learners)->whereNotNull('completed_at')
            ->select('module_library_item_id', DB::raw('COUNT(DISTINCT user_id) as total'))->groupBy('module_library_item_id')->pluck('total', 'module_library_item_id');
        $classCount = DB::table('class_module_assignments')->where('status', 'active')
            ->select('module_library_item_id', DB::raw('COUNT(DISTINCT class_id) as total'))->groupBy('module_library_item_id')->pluck('total', 'module_library_item_id');

        $public = DB::table('modules')->orderBy('order_index')->orderBy('id')
            ->get(['id', 'title', 'year_level', 'is_published'])
            ->map(fn ($m) => [
                'type' => 'datasensei',
                'published' => (bool) $m->is_published,
                'title' => $m->title,
                'kind' => 'DataSensei Module',
                'detail' => (string) ($m->year_level ?: F::NONE),
                'classes' => F::NONE,
                'accessed' => (int) ($publicAccessed[$m->id] ?? 0),
                'completed' => (int) ($publicCompleted[$m->id] ?? 0),
            ]);

        $library = DB::table('module_library_items')->orderBy('module_no')->orderBy('version_no')
            ->get(['id', 'title', 'module_no', 'version_name', 'is_active'])
            ->map(fn ($m) => [
                'type' => 'class',
                'published' => (bool) $m->is_active,
                'title' => $m->title,
                'kind' => 'Class Module',
                'detail' => 'Module '.$m->module_no.($m->version_name ? ', '.$m->version_name : ''),
                'classes' => F::number($classCount[$m->id] ?? 0),
                'accessed' => (int) ($classAccessed[$m->id] ?? 0),
                'completed' => (int) ($classCompleted[$m->id] ?? 0),
            ]);

        $allModules = $public->concat($library);
        $filtered = $allModules
            ->when(isset(self::TYPES['modules'][$f->type]), fn (Collection $c) => $c->where('type', $f->type))
            ->when($f->status === 'published', fn (Collection $c) => $c->where('published', true))
            ->when($f->status === 'unpublished', fn (Collection $c) => $c->where('published', false))
            ->when($f->search !== '', fn (Collection $c) => $c->filter(fn (array $m) => str_contains(mb_strtolower($m['title'].' '.$m['detail']), mb_strtolower($f->search))))
            ->values();

        [$rows, $truncated] = F::page($filtered->map(fn (array $m) => [
            'title' => $m['title'],
            'kind' => $m['kind'],
            'detail' => $m['detail'],
            'status' => $m['published'] ? 'Published' : 'Unpublished',
            'classes' => $m['classes'],
            'accessed' => F::number($m['accessed']),
            'completed' => F::number($m['completed']),
            'rate' => F::pct(F::percent($m['completed'], $m['accessed'])),
            '_tone' => ['status' => $m['published'] ? 'good' : null],
        ]), 'modules', $all);

        $count = fn (string $type, ?bool $published = null) => $allModules->where('type', $type)->when($published !== null, fn ($c) => $c->where('published', $published))->count();

        return new ReportResult(
            'modules',
            'Modules',
            'DataSensei Modules are open to every learner; Class Modules reach students only through a class. Accessed means the learner opened the module; completed means they finished it.',
            [
                ['label' => 'Total modules', 'value' => F::number($allModules->count()), 'note' => $count('datasensei').' DataSensei, '.$count('class').' class module versions'],
                ['label' => 'Published', 'value' => F::number($allModules->where('published', true)->count()), 'note' => $count('datasensei', true).' DataSensei, '.$count('class', true).' class'],
                ['label' => 'Unpublished', 'value' => F::number($allModules->where('published', false)->count()), 'note' => $count('datasensei', false).' DataSensei, '.$count('class', false).' class'],
                ['label' => 'Learners who completed a module', 'value' => F::number($this->distinctCompleters($learners))],
            ],
            [
                new ReportTable('modules', 'Modules', [
                    'title' => 'Module', 'kind' => 'Type', 'detail' => 'Year level or version', 'status' => 'Status',
                    'classes' => 'Classes assigned', 'accessed' => 'Users accessed', 'completed' => 'Users completed', 'rate' => 'Completion rate',
                ], $rows, 'No modules match these filters.', 'Completion rate is completed divided by accessed.', $truncated),
            ],
        );
    }

    private function distinctCompleters(Builder $learners): int
    {
        $public = DB::table('module_user')->whereIn('user_id', $learners)->where('is_completed', true)->select('user_id');
        $class = DB::table('module_library_progress')->whereIn('user_id', $learners)->whereNotNull('completed_at')->select('user_id');

        return (int) DB::query()->fromSub($public->union($class), 'done')->count();
    }

    // ── 3. Classes ───────────────────────────────────────────────────

    private function classes(ReportFilters $f, bool $all): ReportResult
    {
        $windowFrom = $f->from ?? ($f->to === null ? CarbonImmutable::now()->subDays(30)->startOfDay() : null);
        $window = new ReportFilters(from: $windowFrom, to: $f->to);
        $windowText = $f->hasDateRange() ? $f->rangeText() : 'Last 30 days';

        $classes = DB::table('classes')
            ->leftJoin('users as instructor', 'instructor.id', '=', 'classes.instructor_id')
            ->when($f->status === 'active', fn (Builder $q) => $q->where(fn (Builder $w) => $w->where('classes.is_archived', false)->orWhereNull('classes.is_archived')))
            ->when($f->status === 'archived', fn (Builder $q) => $q->where('classes.is_archived', true))
            ->when($f->search !== '', fn (Builder $q) => $q->where(fn (Builder $w) => $w->where('classes.name', 'like', $f->like())->orWhere('classes.section', 'like', $f->like())->orWhere('instructor.name', 'like', $f->like())))
            ->orderBy('classes.name')
            ->get(['classes.id', 'classes.name', 'classes.section', 'classes.is_archived', 'instructor.name as instructor_name']);

        $ids = $classes->pluck('id')->all();
        $enrolled = $this->countBy('class_student', 'class_id', $ids, 'student_id');
        $modules = $this->countBy('class_module_assignments', 'class_id', $ids, 'module_library_item_id', fn (Builder $q) => $q->where('status', 'active'));
        // Assignments were merged into assessments; converted rows are
        // already counted here.
        $assessments = $this->countBy('assessments', 'class_id', $ids, 'id');
        $challenges = $this->countBy('class_challenge_assignments', 'class_id', $ids, 'challenge_id');
        [$activeStudents, $submissions] = $this->classActivity($ids, $window);

        [$rows, $truncated] = F::page($classes->map(function ($class) use ($enrolled, $modules, $assessments, $challenges, $activeStudents, $submissions): array {
            $students = (int) ($enrolled[$class->id] ?? 0);
            $active = (int) ($activeStudents[$class->id] ?? 0);

            return [
                'class' => trim($class->name.($class->section ? ', '.$class->section : '')),
                'instructor' => $class->instructor_name ?: F::NONE,
                'status' => $class->is_archived ? 'Archived' : 'Active',
                'students' => F::number($students),
                'modules' => F::number($modules[$class->id] ?? 0),
                'work' => F::number(($assessments[$class->id] ?? 0) + ($challenges[$class->id] ?? 0)),
                'participation' => $students > 0 ? $active.' of '.$students.' students ('.F::pct(F::percent($active, $students)).')' : F::NONE,
                'submissions' => F::number($submissions[$class->id] ?? 0),
                '_tone' => ['participation' => $students > 0 ? F::tone(F::percent($active, $students)) : null],
            ];
        }), 'classes', $all);

        $archived = $classes->where('is_archived', true)->count();

        return new ReportResult(
            'classes',
            'Classes',
            'Every class with its instructor, enrolment, assigned modules and how many students took part in class work.',
            [
                ['label' => 'Total classes', 'value' => F::number($classes->count())],
                ['label' => 'Active classes', 'value' => F::number($classes->count() - $archived), 'note' => $archived.' archived'],
                ['label' => 'Students enrolled', 'value' => F::number(DB::table('class_student')->whereIn('class_id', $ids ?: [0])->distinct()->count('student_id'))],
                ['label' => 'Active students', 'value' => F::number(array_sum($activeStudents)), 'note' => $windowText],
            ],
            [
                new ReportTable('classes', 'Classes', [
                    'class' => 'Class', 'instructor' => 'Instructor', 'status' => 'Status', 'students' => 'Enrolled students',
                    'modules' => 'Modules assigned', 'work' => 'Assessments and challenges given',
                    'participation' => 'Participation ('.$windowText.')', 'submissions' => 'Submissions ('.$windowText.')',
                ], $rows, 'No classes match these filters.', 'Participation counts students who submitted an assessment, attempted a class challenge, or opened a class module in the period.', $truncated),
            ],
        );
    }

    /**
     * Students active in each class during the window, and the number of
     * submissions and attempts they made there.
     *
     * @param  list<int>  $classIds
     * @return array{0: array<int, int>, 1: array<int, int>}
     */
    private function classActivity(array $classIds, ReportFilters $window): array
    {
        if ($classIds === []) {
            return [[], []];
        }

        $pairs = collect();
        $counts = [];
        $add = function (Collection $rows) use (&$pairs, &$counts): void {
            foreach ($rows as $row) {
                $pairs->push($row->class_id.':'.$row->student_id);
                $counts[$row->class_id] = ($counts[$row->class_id] ?? 0) + (int) $row->total;
            }
        };

        // Assignments were merged into assessments; converted submissions are
        // already counted here.
        $add($window->applyDate(DB::table('assessment_submissions as s')
            ->join('assessments as a', 'a.id', '=', 's.assessment_id')
            ->whereIn('a.class_id', $classIds)
            ->whereIn('s.status', ['submitted', 'late', 'graded']), 's.submitted_at')
            ->groupBy('a.class_id', 's.student_id')
            ->get(['a.class_id', 's.student_id', DB::raw('COUNT(*) as total')]));

        $add($window->applyDate(DB::table('challenge_attempts as t')
            ->join('class_challenge_assignments as c', 'c.challenge_id', '=', 't.challenge_id')
            ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'c.class_id')->on('cs.student_id', '=', 't.user_id'))
            ->whereIn('c.class_id', $classIds)
            ->where('t.status', 'submitted'), 't.submitted_at')
            ->groupBy('c.class_id', 't.user_id')
            ->get(['c.class_id', 't.user_id as student_id', DB::raw('COUNT(*) as total')]));

        $add($window->applyDate(DB::table('coding_submissions as t')
            ->join('coding_questions as q', 'q.id', '=', 't.coding_question_id')
            ->join('class_challenge_assignments as c', 'c.challenge_id', '=', 'q.challenge_id')
            ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'c.class_id')->on('cs.student_id', '=', 't.user_id'))
            ->whereIn('c.class_id', $classIds)
            ->where('t.voided', false), 't.created_at')
            ->groupBy('c.class_id', 't.user_id')
            ->get(['c.class_id', 't.user_id as student_id', DB::raw('COUNT(*) as total')]));

        $add($window->applyDate(DB::table('module_library_progress as p')
            ->join('class_module_assignments as m', fn ($j) => $j->on('m.module_library_item_id', '=', 'p.module_library_item_id')->where('m.status', '=', 'active'))
            ->join('class_student as cs', fn ($j) => $j->on('cs.class_id', '=', 'm.class_id')->on('cs.student_id', '=', 'p.user_id'))
            ->whereIn('m.class_id', $classIds), 'p.last_opened_at')
            ->groupBy('m.class_id', 'p.user_id')
            ->get(['m.class_id', 'p.user_id as student_id', DB::raw('0 as total')]));

        $active = [];
        foreach ($pairs->unique() as $pair) {
            $classId = (int) strtok($pair, ':');
            $active[$classId] = ($active[$classId] ?? 0) + 1;
        }

        return [$active, $counts];
    }

    // ── 4. Assessments ───────────────────────────────────────────────

    private function assessments(ReportFilters $f, bool $all): ReportResult
    {
        $status = array_key_exists($f->status, self::STATUSES['assessments']) ? $f->status : null;
        $inRange = function (Builder $q, string $table) use ($f): Builder {
            if (! $f->hasDateRange()) {
                return $q;
            }

            return $q->where(function (Builder $w) use ($f, $table): void {
                $w->where(fn (Builder $due) => $f->applyDate($due->whereNotNull($table.'.due_at'), $table.'.due_at'))
                    ->orWhere(fn (Builder $created) => $f->applyDate($created->whereNull($table.'.due_at'), $table.'.created_at'));
            });
        };

        // Assignments were merged into assessments (DataSensei Updates 11);
        // converted rows are already counted here, under their purpose label.
        $assessments = $inRange(DB::table('assessments')
            ->leftJoin('classes', 'classes.id', '=', 'assessments.class_id')
            ->when($f->classId, fn (Builder $q) => $q->where('assessments.class_id', $f->classId))
            ->when($status, fn (Builder $q) => $q->where('assessments.status', $status))
            ->when($f->search !== '', fn (Builder $q) => $q->where('assessments.title', 'like', $f->like())), 'assessments')
            ->orderByDesc('assessments.created_at')
            ->get(['assessments.id', 'assessments.title', 'assessments.status', 'assessments.purpose', 'assessments.class_id', 'assessments.due_at', 'classes.name as class_name']);

        $classIds = $assessments->pluck('class_id')->filter()->unique()->values()->all();
        $enrolled = $this->countBy('class_student', 'class_id', $classIds, 'student_id');
        $metrics = app(ClassProgress::class);

        $assessmentStats = $metrics->assessmentStats($assessments->pluck('id')->all());

        [$assessmentRows, $cut] = F::page($assessments->map(function ($a) use ($assessmentStats, $enrolled): array {
            $s = $assessmentStats[$a->id] ?? ClassProgress::emptyAssessmentStats();
            $students = (int) ($enrolled[$a->class_id] ?? 0);
            $completion = F::percent($s['students_completed'], $students);

            return [
                'title' => $a->title,
                'kind' => Assessment::PURPOSES[$a->purpose] ?? 'Assessment',
                'class' => $a->class_name ?: F::NONE,
                'status' => ucfirst((string) $a->status),
                'attempts' => F::number($s['attempts']),
                'average' => F::pct($s['average']),
                'passed' => F::number($s['passed']),
                'failed' => F::number($s['failed']),
                'completion' => $students > 0 ? F::pct($completion).' ('.$s['students_completed'].' of '.$students.')' : F::NONE,
                '_tone' => ['average' => F::tone($s['average'])],
            ];
        }), 'assessments', $all);

        $allAttempts = array_sum(array_column($assessmentStats, 'attempts'));
        $late = DB::table('assessment_submissions as s')
            ->join('assessments as a', 'a.id', '=', 's.assessment_id')
            ->whereIn('s.assessment_id', $assessments->pluck('id')->all() ?: [0])
            ->whereIn('s.status', ['submitted', 'late', 'graded'])
            ->where(fn (Builder $q) => $q->where('s.status', 'late')
                ->orWhere(fn (Builder $w) => $w->whereNotNull('a.due_at')->whereColumn('s.submitted_at', '>', 'a.due_at')))
            ->count();

        return new ReportResult(
            'assessments',
            'Assessments',
            'Class assessments. Pass and fail use each student\'s best attempt against the '.F::PASS_PERCENT.'% pass mark.',
            [
                ['label' => 'Assessments', 'value' => F::number($assessments->count()), 'note' => F::number($allAttempts).' attempts'],
                ['label' => 'Average assessment score', 'value' => F::pct($metrics->weightedAverage($assessmentStats))],
                ['label' => 'Students passed', 'value' => F::number(array_sum(array_column($assessmentStats, 'passed'))), 'note' => F::number(array_sum(array_column($assessmentStats, 'failed'))).' failed'],
                ['label' => 'Late submissions', 'value' => F::number($late)],
            ],
            [
                new ReportTable('assessments', 'Assessments', [
                    'title' => 'Assessment', 'kind' => 'Type', 'class' => 'Class', 'status' => 'Status', 'attempts' => 'Attempts', 'average' => 'Average score',
                    'passed' => 'Passed', 'failed' => 'Failed', 'completion' => 'Completion rate',
                ], $assessmentRows, 'No assessments match these filters.', $f->hasDateRange() ? 'Assessments due (or created, when there is no due date) in the selected range.' : null, $cut),
            ],
        );
    }

    // ── 5. Challenges & Coding Challenges ───────────────────────────

    private function challenges(ReportFilters $f, bool $all): ReportResult
    {
        $base = fn (bool $coding): Builder => DB::table('challenges')
            ->leftJoin('challenge_categories', 'challenge_categories.id', '=', 'challenges.challenge_category_id')
            ->leftJoin('modules', 'modules.id', '=', 'challenges.module_id')
            ->where('challenges.is_coding_challenge', $coding ? 1 : 0)
            ->when($f->moduleId, fn (Builder $q) => $q->where('challenges.module_id', $f->moduleId))
            ->when($f->type === 'class', fn (Builder $q) => $q->where('challenges.visibility', Challenge::VISIBILITY_INSTRUCTOR))
            ->when($f->type === 'platform', fn (Builder $q) => $q->where(fn (Builder $w) => $w->whereNull('challenges.visibility')->orWhere('challenges.visibility', Challenge::VISIBILITY_PLATFORM)))
            ->when($f->search !== '', fn (Builder $q) => $q->where('challenges.title', 'like', $f->like()))
            ->select('challenges.id', 'challenges.title', 'challenges.visibility', 'challenge_categories.name as level', 'modules.title as module_title');

        // MCQ challenges.
        $mcqStats = $f->applyDate(DB::table('challenge_attempts')
            ->where('status', 'submitted')
            ->where('total_questions', '>', 0), 'submitted_at')
            ->groupBy('challenge_id')
            ->get([
                'challenge_id',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('AVG(score * 100.0 / total_questions) as average'),
                DB::raw('COUNT(DISTINCT user_id) as users'),
            ])->keyBy('challenge_id');
        $mcqPassed = $f->applyDate(DB::table('challenge_attempts')
            ->where('status', 'submitted')
            ->where('total_questions', '>', 0)
            ->whereRaw('score * 100 >= ? * total_questions', [F::PASS_PERCENT]), 'submitted_at')
            ->groupBy('challenge_id')
            ->select('challenge_id', DB::raw('COUNT(DISTINCT user_id) as total'))
            ->pluck('total', 'challenge_id');

        $mcq = $base(false)->get()->map(function ($c) use ($mcqStats, $mcqPassed): array {
            $s = $mcqStats[$c->id] ?? null;
            $average = $s ? round((float) $s->average, 1) : null;

            return [
                'title' => $c->title,
                'level' => $c->level ?: F::NONE,
                'module' => $c->module_title ?: F::NONE,
                'source' => $c->visibility === Challenge::VISIBILITY_INSTRUCTOR ? 'Instructor-built' : 'DataSensei',
                'attempts' => F::number($s->attempts ?? 0),
                'average' => F::pct($average),
                'completion' => $s ? F::pct(F::percent($mcqPassed[$c->id] ?? 0, $s->users)).' ('.($mcqPassed[$c->id] ?? 0).' of '.$s->users.' learners)' : F::NONE,
                '_attempts' => (int) ($s->attempts ?? 0),
                '_tone' => ['average' => F::tone($average)],
            ];
        })->sortByDesc('_attempts')->values();

        // Coding challenges.
        $codingStats = $f->applyDate(DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.voided', false), 'coding_submissions.created_at')
            ->groupBy('coding_questions.challenge_id')
            ->get([
                'coding_questions.challenge_id',
                DB::raw('COUNT(*) as attempts'),
                DB::raw('AVG(CASE WHEN coding_submissions.tests_total > 0 THEN coding_submissions.tests_passed * 100.0 / coding_submissions.tests_total END) as average'),
                DB::raw("SUM(CASE WHEN coding_submissions.status = 'passed' THEN 1 ELSE 0 END) as passed"),
                DB::raw("SUM(CASE WHEN coding_submissions.status IN ('failed', 'error') THEN 1 ELSE 0 END) as failed"),
                DB::raw("AVG(CASE WHEN coding_submissions.status = 'passed' AND coding_submissions.time_taken_seconds > 0 THEN coding_submissions.time_taken_seconds END) as time"),
                DB::raw('SUM(coding_submissions.tests_passed) as tests_passed'),
                DB::raw('SUM(coding_submissions.tests_total) as tests_total'),
            ])->keyBy('challenge_id');

        $coding = $base(true)->get()->map(function ($c) use ($codingStats): array {
            $s = $codingStats[$c->id] ?? null;
            $average = $s && $s->average !== null ? round((float) $s->average, 1) : null;

            return [
                'title' => $c->title,
                'level' => $c->level ?: F::NONE,
                'source' => $c->visibility === Challenge::VISIBILITY_INSTRUCTOR ? 'Instructor-built' : 'DataSensei',
                'attempts' => F::number($s->attempts ?? 0),
                'average' => F::pct($average),
                'passed' => F::number($s->passed ?? 0),
                'failed' => F::number($s->failed ?? 0),
                'time' => F::duration($s->time ?? null),
                'tests' => $s && (int) $s->tests_total > 0 ? F::pct(F::percent($s->tests_passed, $s->tests_total)).' of '.F::number($s->tests_total).' test runs' : F::NONE,
                '_attempts' => (int) ($s->attempts ?? 0),
                '_tone' => ['average' => F::tone($average)],
            ];
        })->sortByDesc('_attempts')->values();

        [$mcqRows, $cutA] = F::page($mcq, 'mcq', $all);
        [$codingRows, $cutB] = F::page($coding, 'coding', $all);

        $mcqAttempts = (int) $mcqStats->sum('attempts');
        $codingAttempts = (int) $codingStats->sum('attempts');

        return new ReportResult(
            'challenges',
            'Challenges & Coding Challenges',
            'Attempts on MCQ and coding challenges. A learner completes an MCQ challenge with '.F::PASS_PERCENT.'% or more; a coding attempt passes when every test passes.',
            [
                ['label' => 'MCQ challenge attempts', 'value' => F::number($mcqAttempts), 'note' => $f->rangeText()],
                ['label' => 'Average MCQ score', 'value' => F::pct($mcqAttempts > 0 ? round((float) $mcqStats->sum(fn ($s) => $s->average * $s->attempts) / $mcqAttempts, 1) : null)],
                ['label' => 'Coding attempts', 'value' => F::number($codingAttempts), 'note' => F::number($codingStats->sum('passed')).' passed'],
                ['label' => 'Coding tests passed', 'value' => F::pct(F::percent($codingStats->sum('tests_passed'), $codingStats->sum('tests_total')))],
            ],
            [
                new ReportTable('mcq', 'MCQ challenges', [
                    'title' => 'Challenge', 'level' => 'Level', 'module' => 'Module', 'source' => 'Made by',
                    'attempts' => 'Attempts', 'average' => 'Average score', 'completion' => 'Completion rate',
                ], $mcqRows, 'No MCQ challenges match these filters.', 'Completion rate is learners who reached '.F::PASS_PERCENT.'% divided by learners who attempted.', $cutA),
                new ReportTable('coding', 'Coding challenges', [
                    'title' => 'Coding challenge', 'level' => 'Level', 'source' => 'Made by', 'attempts' => 'Attempts', 'average' => 'Average score',
                    'passed' => 'Passed attempts', 'failed' => 'Failed attempts', 'time' => 'Average completion time', 'tests' => 'Test cases passed',
                ], $codingRows, 'No coding challenges match these filters.', 'Average completion time is the time taken on passing attempts.', $cutB),
            ],
        );
    }

    // ── 6. Gamification ──────────────────────────────────────────────

    private function gamification(ReportFilters $f, bool $all): ReportResult
    {
        $challengeXp = (int) $f->applyDate(DB::table('challenge_attempts')->where('xp_awarded', '>', 0), 'submitted_at')->sum('xp_awarded');
        $codingXp = (int) $f->applyDate(DB::table('coding_submissions')->where('xp_earned', '>', 0)->where('voided', false), 'created_at')->sum('xp_earned');

        $earned = $f->applyDate(DB::table('user_achievements')
            ->join('achievement_definitions as d', 'd.id', '=', 'user_achievements.achievement_definition_id'), 'user_achievements.unlocked_at')
            ->groupBy('d.id', 'd.name', 'd.xp_reward')
            ->orderBy('d.name')
            ->get(['d.id', 'd.name', 'd.xp_reward', DB::raw('COUNT(*) as total')]);
        $achievementXp = (int) $earned->sum(fn ($row) => (int) $row->xp_reward * (int) $row->total);

        $missions = $f->applyDate(DB::table('student_mission_progress as p')
            ->join('mission_definitions as d', 'd.id', '=', 'p.mission_definition_id')
            ->where('p.is_completed', true), 'p.completed_at')
            ->groupBy('d.id', 'd.title', 'd.period_type', 'd.xp_reward')
            ->orderBy('d.period_type')->orderBy('d.title')
            ->get(['d.id', 'd.title', 'd.period_type', 'd.xp_reward', DB::raw('COUNT(*) as total'), DB::raw('SUM(p.xp_awarded) as xp')]);
        $missionXp = (int) $missions->sum('xp');

        $total = $challengeXp + $codingXp + $achievementXp + $missionXp;
        $range = $f->rangeText() ?? 'All time';

        $achievementRows = DB::table('achievement_definitions')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'xp_reward'])
            ->map(fn ($d) => [
                'name' => $d->name,
                'xp' => F::number($d->xp_reward).' EXP',
                'earned' => F::number($earned->firstWhere('id', $d->id)->total ?? 0),
            ]);
        $missionRows = DB::table('mission_definitions')->where('is_active', true)->orderBy('period_type')->orderBy('sort_order')->get(['id', 'title', 'period_type', 'xp_reward'])
            ->map(fn ($d) => [
                'name' => $d->title,
                'frequency' => ucfirst((string) $d->period_type),
                'xp' => F::number($d->xp_reward).' EXP',
                'completed' => F::number($missions->firstWhere('id', $d->id)->total ?? 0),
            ]);

        [$achievementPage, $cutA] = F::page($achievementRows, 'achievements', $all);
        [$missionPage, $cutB] = F::page($missionRows, 'missions', $all);

        return new ReportResult(
            'gamification',
            'Gamification',
            'EXP learners received, achievements earned and missions completed. '.$range.'.',
            [
                ['label' => 'Total EXP distributed', 'value' => F::number($total), 'note' => $range],
                ['label' => 'Achievements earned', 'value' => F::number($earned->sum('total'))],
                ['label' => 'Daily mission completions', 'value' => F::number($missions->where('period_type', 'daily')->sum('total'))],
                ['label' => 'Weekly mission completions', 'value' => F::number($missions->where('period_type', 'weekly')->sum('total'))],
            ],
            [
                new ReportTable('sources', 'Where the EXP came from', ['source' => 'Source', 'xp' => 'EXP'], [
                    ['source' => 'MCQ challenges', 'xp' => F::number($challengeXp)],
                    ['source' => 'Coding challenges', 'xp' => F::number($codingXp)],
                    ['source' => 'Achievements', 'xp' => F::number($achievementXp)],
                    ['source' => 'Missions', 'xp' => F::number($missionXp)],
                ]),
                new ReportTable('achievements', 'Achievements', ['name' => 'Achievement', 'xp' => 'EXP', 'earned' => 'Times earned'], $achievementPage, 'No achievements are set up.', null, $cutA),
                new ReportTable('missions', 'Missions', ['name' => 'Mission', 'frequency' => 'Frequency', 'xp' => 'EXP', 'completed' => 'Completions'], $missionPage, 'No missions are set up.', null, $cutB),
            ],
        );
    }

    // ── 7. Audit logs ────────────────────────────────────────────────

    private function audit(ReportFilters $f, bool $all): ReportResult
    {
        $role = self::ROLES[$f->role] ?? null;

        $query = $f->applyDate(DB::table('audit_logs'), 'created_at')
            ->when($role !== null, fn (Builder $q) => $q->where('user_role', $role))
            ->when(isset(AuditLog::ACTIONS[$f->action]), fn (Builder $q) => $q->where('action', $f->action))
            ->when($f->search !== '', fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('user_name', 'like', $f->like())
                ->orWhere('record_label', 'like', $f->like())
                ->orWhere('record_type', 'like', $f->like())
                ->orWhere('details', 'like', $f->like())));

        $byRole = (clone $query)->select('user_role', DB::raw('COUNT(*) as total'))->groupBy('user_role')->pluck('total', 'user_role');

        [$rows, $truncated] = $this->pageQuery((clone $query)->orderByDesc('created_at')->orderByDesc('id'), 'audit', $all, fn ($log) => [
            'when' => F::dateTime($log->created_at),
            'user' => $log->user_name ?: F::NONE,
            'role' => self::ROLE_NAMES[(int) $log->user_role] ?? 'Unknown',
            'action' => AuditLog::ACTIONS[$log->action] ?? ucfirst(str_replace('_', ' ', (string) $log->action)),
            'record' => $log->record_type.($log->record_label ? ': '.$log->record_label : ''),
            'details' => $log->details ?: '',
        ]);

        return new ReportResult(
            'audit',
            'Audit Logs',
            'Important actions taken by admins and instructors: what was created, edited, deleted, published, unpublished, assigned or removed, and configuration changes.',
            [
                ['label' => 'Actions recorded', 'value' => F::number(array_sum($byRole->all())), 'note' => $f->rangeText()],
                ['label' => 'By admins', 'value' => F::number(($byRole[User::ROLE_ADMIN] ?? 0) + ($byRole[User::ROLE_SUPERADMIN] ?? 0) + ($byRole[User::ROLE_INSTITUTION_ADMIN] ?? 0))],
                ['label' => 'By instructors', 'value' => F::number($byRole[User::ROLE_INSTRUCTOR] ?? 0)],
            ],
            [
                new ReportTable('audit', 'Actions', [
                    'when' => 'Date and time', 'user' => 'User', 'role' => 'Role', 'action' => 'Action', 'record' => 'Affected record', 'details' => 'Details',
                ], $rows, 'No actions match these filters. Actions are recorded from DataSensei Updates 8 onward.', null, $truncated),
            ],
        );
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * @return array{0: \Illuminate\Pagination\LengthAwarePaginator|list<array<string, mixed>>, 1: bool}
     */
    private function pageQuery(Builder $query, string $name, bool $all, callable $map): array
    {
        if ($all) {
            $rows = $query->limit(ReportExporter::MAX_ROWS + 1)->get();

            return [$rows->take(ReportExporter::MAX_ROWS)->map($map)->values()->all(), $rows->count() > ReportExporter::MAX_ROWS];
        }

        return [$query->paginate(F::PER_PAGE, ['*'], $name.'_page')->withQueryString()->through($map), false];
    }

    /**
     * Counts of distinct values per group, for the given group ids.
     *
     * @param  list<int>  $ids
     * @return array<int, int>
     */
    private function countBy(string $table, string $groupColumn, array $ids, string $countColumn, ?callable $scope = null): array
    {
        if ($ids === []) {
            return [];
        }

        $query = DB::table($table)->whereIn($groupColumn, $ids);
        if ($scope) {
            $scope($query);
        }

        return $query->groupBy($groupColumn)
            ->select($groupColumn, DB::raw('COUNT(DISTINCT '.$countColumn.') as total'))
            ->pluck('total', $groupColumn)
            ->map(fn ($total) => (int) $total)
            ->all();
    }
}
