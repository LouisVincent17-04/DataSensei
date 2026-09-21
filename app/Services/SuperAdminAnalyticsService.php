<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SuperAdminAnalyticsService
{
    private const MAX_RANGE_DAYS = 366;

    /** @var array<string, bool> */
    private array $tableExistsCache = [];

    /** @var array<string, bool> */
    private array $columnExistsCache = [];

    private const ROLE_LABELS = [
        1 => 'Student',
        2 => 'Admin',
        3 => 'Super Admin',
        4 => 'Instructor',
        5 => 'Institution Admin',
    ];

    public function build(?string $from = null, ?string $to = null): array
    {
        $range = $this->range($from, $to);

        return [
            'range' => $range,
            'summary' => $this->summary($range),
            'roleDistribution' => $this->roleDistribution(),
            'registrationTrend' => $this->monthlyTrend('users', $range),
            'activityTrend' => $this->activityTrend($range),
            'students' => $this->studentAnalytics($range),
            'instructors' => $this->instructorAnalytics($range),
            'institutions' => $this->institutionAnalytics($range),
            'learning' => $this->learningAnalytics($range),
            'antiCheat' => $this->antiCheatAnalytics($range),
            'content' => $this->contentAnalytics($range),
            'insights' => $this->insights($range),
        ];
    }

    public function exportRows(string $section, array $analytics): array
    {
        return match ($section) {
            'students' => $this->rows($analytics['students']['topXp'] ?? [], [
                'name', 'email', 'xp', 'streak', 'last_activity',
            ]),
            'instructors' => $this->rows($analytics['instructors']['topInstructors'] ?? [], [
                'name', 'email', 'classes_count', 'assignments_count', 'students_count', 'submissions_count',
            ]),
            'institutions' => $this->rows($analytics['institutions']['topInstitutions'] ?? [], [
                'name', 'status', 'users_count', 'classes_count', 'students_count', 'assignments_count',
            ]),
            'learning' => $this->rows($analytics['learning']['hardestMcq'] ?? [], [
                'title', 'level', 'attempts_count', 'avg_score',
            ]),
            'anticheat' => $this->rows($analytics['antiCheat']['recentEvents'] ?? [], [
                'student_name', 'student_email', 'assignment_title', 'event_type', 'severity', 'occurred_at',
            ]),
            default => $this->rows($analytics['summary'] ?? [], ['label', 'value']),
        };
    }

    public function csvFilename(string $section): string
    {
        return 'datasensei_' . Str::slug($section) . '_analytics_' . now()->format('Ymd_His') . '.csv';
    }

    private function range(?string $from, ?string $to): array
    {
        $end = $this->safeDate($to, Carbon::now())->endOfDay();
        $start = $this->safeDate($from, Carbon::now()->subDays(29))->startOfDay();

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        if ($start->diffInDays($end) + 1 > self::MAX_RANGE_DAYS) {
            $start = $end->copy()->subDays(self::MAX_RANGE_DAYS - 1)->startOfDay();
        }

        return [
            'from' => $start,
            'to' => $end,
            'from_date' => $start->toDateString(),
            'to_date' => $end->toDateString(),
            'days' => max(1, $start->diffInDays($end) + 1),
        ];
    }

    private function safeDate(?string $value, Carbon $fallback): Carbon
    {
        if (! $value || preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value) !== 1) {
            return $fallback->copy();
        }

        try {
            $date = Carbon::createFromFormat('!Y-m-d', $value);

            return $date && $date->toDateString() === $value
                ? $date
                : $fallback->copy();
        } catch (\Throwable $e) {
            return $fallback->copy();
        }
    }

    private function summary(array $range): array
    {
        $students = $this->countWhere('users', ['role' => 1]);
        $instructors = $this->countWhere('users', ['role' => 4]);
        $admins = $this->countWhere('users', ['role' => 2]);
        $institutionAdmins = $this->countWhere('users', ['role' => 5]);
        $activeUsers = $this->countWhere('users', ['status' => 'active']);
        $disabledUsers = $this->countWhere('users', ['status' => 'disabled']);
        $newUsers = $this->countDateRange('users', 'created_at', $range);

        return [
            ['label' => 'Total Students', 'value' => $students, 'sub' => 'Registered learner accounts', 'tone' => 'blue'],
            ['label' => 'Instructors', 'value' => $instructors, 'sub' => 'Teacher / class manager accounts', 'tone' => 'green'],
            ['label' => 'Institutions', 'value' => $this->count('institutions'), 'sub' => $this->countWhere('institutions', ['status' => 'active']) . ' active', 'tone' => 'purple'],
            ['label' => 'Classes', 'value' => $this->count('classes'), 'sub' => $this->count('class_student') . ' enrollments', 'tone' => 'orange'],
            ['label' => 'Module Library', 'value' => $this->count('module_library_items'), 'sub' => 'Reusable learning items', 'tone' => 'blue'],
            ['label' => 'Assignments', 'value' => $this->count('class_assignments'), 'sub' => $this->count('assignment_submissions') . ' student submissions', 'tone' => 'green'],
            ['label' => 'Assessments', 'value' => $this->count('assessments'), 'sub' => $this->count('assessment_submissions') . ' student attempts', 'tone' => 'blue'],
            ['label' => 'MCQ Challenges', 'value' => $this->countWhere('challenges', ['is_coding_challenge' => 0, 'is_active' => 1]), 'sub' => $this->count('challenge_attempts') . ' attempts', 'tone' => 'purple'],
            ['label' => 'Coding Challenges', 'value' => $this->countWhere('challenges', ['is_coding_challenge' => 1, 'is_active' => 1]), 'sub' => $this->count('coding_submissions') . ' submissions', 'tone' => 'orange'],
            ['label' => 'Anti-Cheat Events', 'value' => $this->count('anti_cheat_events'), 'sub' => 'Assignment protection logs', 'tone' => 'red'],
            ['label' => 'New Users', 'value' => $newUsers, 'sub' => $range['from_date'] . ' to ' . $range['to_date'], 'tone' => 'blue'],
            ['label' => 'Active Accounts', 'value' => $activeUsers, 'sub' => $disabledUsers . ' disabled', 'tone' => 'green'],
            ['label' => 'Admins', 'value' => $admins + $institutionAdmins, 'sub' => $admins . ' platform · ' . $institutionAdmins . ' institution', 'tone' => 'purple'],
        ];
    }

    private function roleDistribution(): array
    {
        if (! $this->tableExists('users')) {
            return [];
        }

        return DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderBy('role')
            ->get()
            ->map(function ($row) {
                return [
                    'role' => (int) $row->role,
                    'label' => self::ROLE_LABELS[(int) $row->role] ?? ('Role ' . $row->role),
                    'total' => (int) $row->total,
                ];
            })
            ->values()
            ->all();
    }

    private function activityTrend(array $range): array
    {
        $dates = [];
        $cursor = $range['from']->copy();
        while ($cursor->lte($range['to'])) {
            $dates[$cursor->toDateString()] = [
                'date' => $cursor->toDateString(),
                'mcq' => 0,
                'coding' => 0,
                'assignments' => 0,
                'assessments' => 0,
                'anti_cheat' => 0,
                'total' => 0,
            ];
            $cursor->addDay();
        }

        foreach ([
            'challenge_attempts' => 'mcq',
            'coding_submissions' => 'coding',
            'assignment_submissions' => 'assignments',
            'assessment_submissions' => 'assessments',
            'anti_cheat_events' => 'anti_cheat',
        ] as $table => $key) {
            foreach ($this->dailyCounts($table, $range) as $row) {
                if (isset($dates[$row['date']])) {
                    $dates[$row['date']][$key] = (int) $row['total'];
                }
            }
        }

        foreach ($dates as &$row) {
            $row['total'] = $row['mcq'] + $row['coding'] + $row['assignments'] + $row['assessments'] + $row['anti_cheat'];
        }

        return array_values($dates);
    }

    private function studentAnalytics(array $range): array
    {
        return [
            'topXp' => $this->topXpStudents(),
            'topMcq' => $this->topMcqStudents($range),
            'topCoding' => $this->topCodingStudents($range),
            'topAssignments' => $this->topAssignmentStudents($range),
            'atRisk' => $this->atRiskStudents($range),
        ];
    }

    private function instructorAnalytics(array $range): array
    {
        if (! $this->tableExists('users')) {
            return ['topInstructors' => []];
        }

        $rows = DB::table('users as u')
            ->where('u.role', 4)
            ->leftJoin('classes as c', 'c.instructor_id', '=', 'u.id')
            ->leftJoin('class_student as cs', 'cs.class_id', '=', 'c.id')
            ->leftJoin('class_assignments as ca', 'ca.class_id', '=', 'c.id')
            ->leftJoin('assignment_submissions as s', 's.class_assignment_id', '=', 'ca.id')
            ->select(
                'u.id',
                'u.name',
                'u.email',
                DB::raw('COUNT(DISTINCT c.id) as classes_count'),
                DB::raw('COUNT(DISTINCT cs.student_id) as students_count'),
                DB::raw('COUNT(DISTINCT ca.id) as assignments_count'),
                DB::raw('COUNT(DISTINCT s.id) as submissions_count')
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('submissions_count')
            ->orderByDesc('assignments_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return ['topInstructors' => $rows];
    }

    private function institutionAnalytics(array $range): array
    {
        if (! $this->tableExists('institutions')) {
            return ['topInstitutions' => []];
        }

        $rows = DB::table('institutions as i')
            ->leftJoin('users as u', 'u.institution_id', '=', 'i.id')
            ->leftJoin('classes as c', 'c.institution_id', '=', 'i.id')
            ->leftJoin('class_student as cs', 'cs.class_id', '=', 'c.id')
            ->leftJoin('class_assignments as ca', 'ca.class_id', '=', 'c.id')
            ->select(
                'i.id',
                'i.name',
                'i.status',
                DB::raw('COUNT(DISTINCT u.id) as users_count'),
                DB::raw('COUNT(DISTINCT c.id) as classes_count'),
                DB::raw('COUNT(DISTINCT cs.student_id) as students_count'),
                DB::raw('COUNT(DISTINCT ca.id) as assignments_count')
            )
            ->groupBy('i.id', 'i.name', 'i.status')
            ->orderByDesc('students_count')
            ->orderByDesc('users_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();

        return ['topInstitutions' => $rows];
    }

    private function learningAnalytics(array $range): array
    {
        return [
            'mostAttemptedMcq' => $this->mcqPerformance($range, 'attempts_desc'),
            'hardestMcq' => $this->mcqPerformance($range, 'avg_score_asc'),
            'codingPerformance' => $this->codingPerformance($range),
            'hardestCoding' => $this->hardestCodingQuestions($range),
            'assignmentPerformance' => $this->assignmentPerformance($range),
            'assessmentPerformance' => $this->assessmentPerformance($range),
            'categoryBreakdown' => $this->categoryBreakdown(),
        ];
    }

    private function antiCheatAnalytics(array $range): array
    {
        return [
            'eventTypes' => $this->groupCount('anti_cheat_events', 'event_type', $range),
            'severity' => $this->groupCount('anti_cheat_events', 'severity', $range),
            'recentEvents' => $this->recentAntiCheatEvents($range),
        ];
    }

    private function contentAnalytics(array $range): array
    {
        return [
            'moduleLibraryByYear' => $this->groupCount('module_library_items', 'year_level', null),
            'assignmentLibraryTypes' => $this->groupCount('assignment_library_items', 'assignment_type', null),
            'classAssignmentStatus' => $this->groupCount('class_assignments', 'status', null),
            'assignmentSubmissionStatus' => $this->groupCount('assignment_submissions', 'status', $range),
        ];
    }

    private function insights(array $range): array
    {
        $summary = $this->summary($range);
        $antiCheat = $this->countDateRange('anti_cheat_events', 'created_at', $range);
        $submissions = $this->countDateRange('assignment_submissions', 'created_at', $range);
        $assessments = $this->countDateRange('assessment_submissions', 'created_at', $range);
        $coding = $this->countDateRange('coding_submissions', 'created_at', $range);
        $mcq = $this->countDateRange('challenge_attempts', 'created_at', $range);
        $students = $this->countWhere('users', ['role' => 1]);
        $atRisk = count($this->atRiskStudents($range));

        $items = [];
        $items[] = [
            'title' => 'Platform activity in selected range',
            'body' => 'There are ' . number_format($mcq + $coding + $submissions + $assessments) . ' learning actions in the selected period: ' . number_format($mcq) . ' MCQ attempts, ' . number_format($coding) . ' coding submissions, ' . number_format($submissions) . ' assignment submissions, and ' . number_format($assessments) . ' assessment attempts.',
            'tone' => 'blue',
        ];

        $items[] = [
            'title' => 'Academic risk signal',
            'body' => number_format($atRisk) . ' student' . ($atRisk === 1 ? '' : 's') . ' appear in the at-risk list based on low activity, low XP, or weak recent assessment evidence.',
            'tone' => $atRisk > 0 ? 'red' : 'green',
        ];

        $items[] = [
            'title' => 'Anti-cheat visibility',
            'body' => number_format($antiCheat) . ' anti-cheat event' . ($antiCheat === 1 ? '' : 's') . ' were logged in the selected period. Use the event report table to review severe or repeated violations.',
            'tone' => $antiCheat > 0 ? 'orange' : 'green',
        ];

        $studentsText = number_format($students) . ' registered student' . ($students === 1 ? '' : 's');
        $items[] = [
            'title' => 'Learner base',
            'body' => 'DataSensei currently has ' . $studentsText . '. Compare this with active classes and submissions to check whether students are only registered or truly engaged.',
            'tone' => 'purple',
        ];

        return $items;
    }

    private function topXpStudents(): array
    {
        if (! $this->tableExists('users')) {
            return [];
        }

        return DB::table('users')
            ->where('role', 1)
            ->where('status', 'active')
            ->select('id', 'name', 'email', 'xp', 'streak', 'last_activity')
            ->orderByDesc('xp')
            ->orderByDesc('streak')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function topMcqStudents(array $range): array
    {
        if (! $this->tableExists('challenge_attempts')) {
            return [];
        }

        return DB::table('challenge_attempts as ca')
            ->join('users as u', 'u.id', '=', 'ca.user_id')
            ->where('u.role', 1)
            ->where('u.status', 'active')
            ->where('ca.is_ranked', true)
            ->where('ca.is_leaderboard_eligible', true)
            ->whereIn('ca.status', ['submitted', 'expired'])
            ->whereBetween('ca.created_at', [$range['from'], $range['to']])
            ->select(
                'u.name',
                'u.email',
                DB::raw('COUNT(*) as attempts_count'),
                DB::raw('ROUND(AVG(CASE WHEN ca.total_questions > 0 THEN (ca.score * 100.0 / ca.total_questions) ELSE 0 END), 2) as avg_score'),
                DB::raw('SUM(ca.xp_awarded) as total_xp'),
                DB::raw('ROUND(AVG(ca.time_taken_seconds), 0) as avg_time_seconds')
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('avg_score')
            ->orderByDesc('attempts_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function topCodingStudents(array $range): array
    {
        if (! $this->tableExists('coding_submissions')) {
            return [];
        }

        return DB::table('coding_submissions as cs')
            ->join('users as u', 'u.id', '=', 'cs.user_id')
            ->where('u.role', 1)
            ->where('u.status', 'active')
            ->where('cs.voided', false)
            ->whereBetween('cs.created_at', [$range['from'], $range['to']])
            ->select(
                'u.name',
                'u.email',
                DB::raw('COUNT(*) as submissions_count'),
                DB::raw("SUM(CASE WHEN cs.status = 'passed' THEN 1 ELSE 0 END) as passed_count"),
                DB::raw('ROUND(AVG(CASE WHEN cs.tests_total > 0 THEN (cs.tests_passed * 100.0 / cs.tests_total) ELSE 0 END), 2) as avg_test_score'),
                DB::raw('SUM(cs.xp_earned) as total_xp'),
                DB::raw('ROUND(AVG(cs.time_taken_seconds), 0) as avg_time_seconds')
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('avg_test_score')
            ->orderByDesc('passed_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function topAssignmentStudents(array $range): array
    {
        if (! $this->tableExists('assignment_submissions')) {
            return [];
        }

        return DB::table('assignment_submissions as s')
            ->join('users as u', 'u.id', '=', 's.student_id')
            ->where('u.role', 1)
            ->where('u.status', 'active')
            ->whereIn('s.status', ['submitted', 'late', 'graded'])
            ->whereBetween('s.created_at', [$range['from'], $range['to']])
            ->select(
                'u.name',
                'u.email',
                DB::raw('COUNT(*) as submissions_count'),
                DB::raw('ROUND(AVG(CASE WHEN s.graded_at IS NOT NULL AND s.total_points > 0 THEN (s.score * 100.0 / s.total_points) ELSE NULL END), 2) as avg_score'),
                DB::raw("SUM(CASE WHEN s.status = 'late' THEN 1 ELSE 0 END) as late_count")
            )
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('avg_score')
            ->orderByDesc('submissions_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function atRiskStudents(array $range): array
    {
        if (! $this->tableExists('users')) {
            return [];
        }

        $cutoff = Carbon::now()->subDays(14)->toDateTimeString();

        $rows = DB::table('users as u')
            ->where('u.role', 1)
            ->where('u.status', 'active')
            ->leftJoin('assignment_submissions as s', function ($join) use ($range) {
                $join->on('s.student_id', '=', 'u.id')
                    ->whereIn('s.status', ['submitted', 'late', 'graded'])
                    ->whereBetween('s.created_at', [$range['from'], $range['to']]);
            })
            ->leftJoin('challenge_attempts as cu', function ($join) use ($range) {
                $join->on('cu.user_id', '=', 'u.id')
                    ->whereIn('cu.status', ['submitted', 'expired'])
                    ->where('cu.is_ranked', true)
                    ->whereBetween('cu.created_at', [$range['from'], $range['to']]);
            })
            ->leftJoin('coding_submissions as cs', function ($join) use ($range) {
                $join->on('cs.user_id', '=', 'u.id')
                    ->where('cs.voided', false)
                    ->whereBetween('cs.created_at', [$range['from'], $range['to']]);
            })
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'u.xp',
                'u.streak',
                'u.last_activity',
                DB::raw('COUNT(DISTINCT s.id) as assignment_submissions_count'),
                DB::raw('COUNT(DISTINCT cu.id) as mcq_attempts_count'),
                DB::raw('COUNT(DISTINCT cs.id) as coding_submissions_count'),
                DB::raw('ROUND(AVG(CASE WHEN s.graded_at IS NOT NULL AND s.total_points > 0 THEN (s.score * 100.0 / s.total_points) ELSE NULL END), 2) as assignment_avg')
            )
            ->groupBy('u.id', 'u.name', 'u.email', 'u.xp', 'u.streak', 'u.last_activity')
            ->havingRaw('(MAX(COALESCE(u.xp, 0)) < 50) OR (MAX(u.last_activity) IS NULL OR MAX(u.last_activity) < ?) OR assignment_avg < 70', [$cutoff])
            ->orderBy('u.last_activity')
            ->limit(20)
            ->get();

        return $rows->map(function ($row) use ($cutoff) {
            $reasons = [];
            if ((int) ($row->xp ?? 0) < 50) {
                $reasons[] = 'Low XP';
            }
            if (empty($row->last_activity) || $row->last_activity < $cutoff) {
                $reasons[] = 'Inactive 14+ days';
            }
            if ($row->assignment_avg !== null && (float) $row->assignment_avg < 70) {
                $reasons[] = 'Low assignment average';
            }

            $data = (array) $row;
            $data['risk_reasons'] = implode(', ', $reasons) ?: 'Needs review';
            return $data;
        })->all();
    }

    private function mcqPerformance(array $range, string $sort): array
    {
        if (! $this->tableExists('challenge_attempts')) {
            return [];
        }

        $query = DB::table('challenges as ch')
            ->join('challenge_attempts as ca', 'ca.challenge_id', '=', 'ch.id')
            ->leftJoin('challenge_categories as cc', 'cc.id', '=', 'ch.challenge_category_id')
            ->where('ch.is_coding_challenge', 0)
            ->where('ch.is_active', true)
            ->where('ca.is_ranked', true)
            ->where('ca.is_leaderboard_eligible', true)
            ->whereIn('ca.status', ['submitted', 'expired'])
            ->whereBetween('ca.created_at', [$range['from'], $range['to']])
            ->select(
                'ch.id',
                'ch.title',
                DB::raw("COALESCE(cc.name, cc.slug, 'Uncategorized') as level"),
                DB::raw('COUNT(ca.id) as attempts_count'),
                DB::raw('ROUND(AVG(CASE WHEN ca.total_questions > 0 THEN (ca.score * 100.0 / ca.total_questions) ELSE 0 END), 2) as avg_score'),
                DB::raw('ROUND(AVG(ca.time_taken_seconds), 0) as avg_time_seconds')
            )
            ->groupBy('ch.id', 'ch.title', 'cc.name', 'cc.slug');

        if ($sort === 'avg_score_asc') {
            $query->orderBy('avg_score')->orderByDesc('attempts_count');
        } else {
            $query->orderByDesc('attempts_count')->orderBy('avg_score');
        }

        return $query->limit(15)->get()->map(fn ($row) => (array) $row)->all();
    }

    private function codingPerformance(array $range): array
    {
        if (! $this->tableExists('coding_submissions')) {
            return [];
        }

        return DB::table('challenges as ch')
            ->join('coding_questions as cq', 'cq.challenge_id', '=', 'ch.id')
            ->join('coding_submissions as cs', 'cs.coding_question_id', '=', 'cq.id')
            ->leftJoin('challenge_categories as cc', 'cc.id', '=', 'ch.challenge_category_id')
            ->where('ch.is_active', true)
            ->where('cs.voided', false)
            ->whereBetween('cs.created_at', [$range['from'], $range['to']])
            ->select(
                'ch.id',
                'ch.title',
                DB::raw("COALESCE(cc.name, cc.slug, 'Uncategorized') as level"),
                DB::raw('COUNT(cs.id) as submissions_count'),
                DB::raw("SUM(CASE WHEN cs.status = 'passed' THEN 1 ELSE 0 END) as passed_count"),
                DB::raw('ROUND(AVG(CASE WHEN cs.tests_total > 0 THEN (cs.tests_passed * 100.0 / cs.tests_total) ELSE 0 END), 2) as avg_test_score')
            )
            ->groupBy('ch.id', 'ch.title', 'cc.name', 'cc.slug')
            ->orderByDesc('submissions_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function hardestCodingQuestions(array $range): array
    {
        if (! $this->tableExists('coding_submissions')) {
            return [];
        }

        return DB::table('coding_questions as cq')
            ->join('challenges as ch', 'ch.id', '=', 'cq.challenge_id')
            ->join('coding_submissions as cs', 'cs.coding_question_id', '=', 'cq.id')
            ->where('ch.is_active', true)
            ->where('cs.voided', false)
            ->whereBetween('cs.created_at', [$range['from'], $range['to']])
            ->select(
                'cq.id',
                'ch.title as challenge_title',
                DB::raw('LEFT(cq.problem_description, 120) as problem_preview'),
                DB::raw('COUNT(cs.id) as submissions_count'),
                DB::raw("SUM(CASE WHEN cs.status = 'passed' THEN 1 ELSE 0 END) as passed_count"),
                DB::raw('ROUND(AVG(CASE WHEN cs.tests_total > 0 THEN (cs.tests_passed * 100.0 / cs.tests_total) ELSE 0 END), 2) as avg_test_score')
            )
            ->groupBy('cq.id', 'ch.title', 'cq.problem_description')
            ->orderBy('avg_test_score')
            ->orderByDesc('submissions_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function assignmentPerformance(array $range): array
    {
        if (! $this->tableExists('assignment_submissions')) {
            return [];
        }

        return DB::table('class_assignments as ca')
            ->leftJoin('classes as c', 'c.id', '=', 'ca.class_id')
            ->join('assignment_submissions as s', 's.class_assignment_id', '=', 'ca.id')
            ->whereIn('s.status', ['submitted', 'late', 'graded'])
            ->whereBetween('s.created_at', [$range['from'], $range['to']])
            ->select(
                'ca.id',
                'ca.title',
                'ca.status',
                'c.name as class_name',
                DB::raw('COUNT(s.id) as submissions_count'),
                DB::raw("SUM(CASE WHEN s.status = 'late' THEN 1 ELSE 0 END) as late_count"),
                DB::raw('ROUND(AVG(CASE WHEN s.graded_at IS NOT NULL AND s.total_points > 0 THEN (s.score * 100.0 / s.total_points) ELSE NULL END), 2) as avg_score')
            )
            ->groupBy('ca.id', 'ca.title', 'ca.status', 'c.name')
            ->orderByDesc('submissions_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function assessmentPerformance(array $range): array
    {
        if (! $this->tableExists('assessment_submissions') || ! $this->tableExists('assessments')) {
            return [];
        }

        return DB::table('assessments as a')
            ->leftJoin('classes as c', 'c.id', '=', 'a.class_id')
            ->join('assessment_submissions as s', 's.assessment_id', '=', 'a.id')
            ->whereIn('s.status', ['submitted', 'late', 'graded'])
            ->whereBetween('s.created_at', [$range['from'], $range['to']])
            ->select(
                'a.id',
                'a.title',
                'a.status',
                'c.name as class_name',
                DB::raw('COUNT(s.id) as submissions_count'),
                DB::raw("SUM(CASE WHEN s.status = 'late' THEN 1 ELSE 0 END) as late_count"),
                DB::raw('SUM(CASE WHEN s.graded_at IS NULL THEN 1 ELSE 0 END) as pending_review_count'),
                DB::raw('ROUND(AVG(CASE WHEN s.graded_at IS NOT NULL AND s.total_points > 0 THEN (s.score * 100.0 / s.total_points) ELSE NULL END), 2) as avg_score')
            )
            ->groupBy('a.id', 'a.title', 'a.status', 'c.name')
            ->orderByDesc('submissions_count')
            ->limit(15)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function categoryBreakdown(): array
    {
        if (! $this->tableExists('challenge_categories')) {
            return [];
        }

        return DB::table('challenge_categories as cc')
            ->leftJoin('challenges as ch', 'ch.challenge_category_id', '=', 'cc.id')
            ->select(
                'cc.id',
                DB::raw("COALESCE(cc.name, cc.slug, 'Uncategorized') as level"),
                DB::raw('COUNT(DISTINCT CASE WHEN ch.is_active = 1 AND ch.is_coding_challenge = 0 THEN ch.id ELSE NULL END) as mcq_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN ch.is_active = 1 AND ch.is_coding_challenge = 1 THEN ch.id ELSE NULL END) as coding_count')
            )
            ->groupBy('cc.id', 'cc.name', 'cc.slug')
            ->orderBy('cc.id')
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function recentAntiCheatEvents(array $range): array
    {
        if (! $this->tableExists('anti_cheat_events')) {
            return [];
        }

        return DB::table('anti_cheat_events as e')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->leftJoin('class_assignments as ca', 'ca.id', '=', 'e.class_assignment_id')
            ->whereBetween('e.created_at', [$range['from'], $range['to']])
            ->select(
                'e.id',
                'u.name as student_name',
                'u.email as student_email',
                'ca.title as assignment_title',
                'e.event_type',
                'e.severity',
                'e.occurred_at',
                'e.created_at'
            )
            ->orderByDesc('e.created_at')
            ->limit(20)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function monthlyTrend(string $table, array $range): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        return DB::table($table)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as period"), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('period')
            ->get()
            ->map(fn ($row) => ['period' => $row->period, 'total' => (int) $row->total])
            ->all();
    }

    private function dailyCounts(string $table, array $range): array
    {
        if (! $this->tableExists($table)) {
            return [];
        }

        return DB::table($table)
            ->whereBetween('created_at', [$range['from'], $range['to']])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => ['date' => $row->date, 'total' => (int) $row->total])
            ->all();
    }

    private function groupCount(string $table, string $column, ?array $range): array
    {
        if (! $this->tableExists($table) || ! $this->columnExists($table, $column)) {
            return [];
        }

        $query = DB::table($table)
            ->select(DB::raw("COALESCE(" . $this->quoteColumn($column) . ", 'Unknown') as label"), DB::raw('COUNT(*) as total'))
            ->groupBy($column)
            ->orderByDesc('total');

        if ($range && $this->columnExists($table, 'created_at')) {
            $query->whereBetween('created_at', [$range['from'], $range['to']]);
        }

        return $query->limit(15)->get()->map(fn ($row) => ['label' => (string) $row->label, 'total' => (int) $row->total])->all();
    }

    private function count(string $table): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    private function countWhere(string $table, array $conditions): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);
        foreach ($conditions as $column => $value) {
            if ($this->columnExists($table, $column)) {
                $query->where($column, $value);
            }
        }

        return (int) $query->count();
    }

    private function countDateRange(string $table, string $column, array $range): int
    {
        if (! $this->tableExists($table) || ! $this->columnExists($table, $column)) {
            return 0;
        }

        return (int) DB::table($table)->whereBetween($column, [$range['from'], $range['to']])->count();
    }

    private function rows(array $items, array $preferredColumns): array
    {
        if (empty($items)) {
            return [];
        }

        if (array_is_list($items) && isset($items[0]) && is_array($items[0])) {
            $columns = array_values(array_filter($preferredColumns, fn ($column) => array_key_exists($column, $items[0])));
            if (empty($columns)) {
                $columns = array_keys($items[0]);
            }

            $rows = [$columns];
            foreach ($items as $item) {
                $rows[] = array_map(
                    fn ($column) => $this->safeCsvCell($item[$column] ?? ''),
                    $columns
                );
            }
            return $rows;
        }

        return [];
    }

    /**
     * MySQL cannot prepare "SHOW TABLES LIKE ?" or "SHOW COLUMNS ... LIKE ?":
     * the placeholder is a syntax error, the exception was swallowed, and every
     * existence check answered "no", so the whole analytics page and all of its
     * exports reported zero on a populated database. information_schema accepts
     * bound parameters and exposes only the basic columns the MySQL 5.5
     * deployment has. Every query in this service is MySQL SQL, so another
     * driver still reports nothing rather than failing halfway down the page.
     */
    private function tableExists(string $table): bool
    {
        if (array_key_exists($table, $this->tableExistsCache)) {
            return $this->tableExistsCache[$table];
        }

        return $this->tableExistsCache[$table] = $this->schemaObjectExists(
            'SELECT COUNT(*) AS aggregate
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?',
            [$table]
        );
    }

    private function columnExists(string $table, string $column): bool
    {
        $cacheKey = $table . '.' . $column;

        if (array_key_exists($cacheKey, $this->columnExistsCache)) {
            return $this->columnExistsCache[$cacheKey];
        }

        return $this->columnExistsCache[$cacheKey] = $this->tableExists($table)
            && $this->schemaObjectExists(
                'SELECT COUNT(*) AS aggregate
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?',
                [$table, $column]
            );
    }

    /**
     * @param  array<int, string>  $bindings
     */
    private function schemaObjectExists(string $sql, array $bindings): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return false;
        }

        try {
            $result = DB::selectOne($sql, $bindings);

            return (int) ($result->aggregate ?? 0) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function quoteColumn(string $column): string
    {
        return '`' . str_replace('`', '``', $column) . '`';
    }

    private function safeCsvCell(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        return preg_match('/^\s*[=+\-@]/u', $value) === 1 || str_starts_with($value, "\t") || str_starts_with($value, "\r")
            ? "'" . $value
            : $value;
    }
}
