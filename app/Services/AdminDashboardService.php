<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Return the information displayed on the administrator dashboard.
     */
    public function overview(): array
    {
        return [
            'cards' => $this->cards(),
            'roleDistribution' => $this->roleDistribution(),
            'activityTrend' => $this->activityTrend(),
            'recentUsers' => $this->recentUsers(),
            'recentAntiCheat' => $this->recentAntiCheatEvents(),
            'recentChallengeFlags' => $this->recentChallengeFlags(),
            'systemHealth' => $this->systemHealth(),
        ];
    }

    /**
     * Return the information displayed on the administrator reports page.
     */
    public function reports(): array
    {
        return [
            'cards' => $this->cards(),
            'recentUsers' => $this->recentUsers(12),
            'recentAntiCheat' => $this->recentAntiCheatEvents(20),
            'recentChallengeFlags' => $this->recentChallengeFlags(20),
            'recentCodingSubmissions' => $this->recentCodingSubmissions(20),
            'recentAssignmentSubmissions' => $this->recentAssignmentSubmissions(20),
            'systemHealth' => $this->systemHealth(),
        ];
    }

    /**
     * Build the dashboard summary cards.
     */
    private function cards(): array
    {
        return [
            [
                'label' => 'Learner Accounts',
                'value' => $this->countWhere('users', ['role' => 1]),
                'sub' => $this->countWhere('users', [
                    'role' => 1,
                    'status' => 'active',
                ]) . ' active',
                'tone' => 'blue',
            ],
            [
                'label' => 'Instructors',
                'value' => $this->countWhere('users', ['role' => 4]),
                'sub' => $this->countWhere('users', [
                    'role' => 4,
                    'status' => 'active',
                ]) . ' active',
                'tone' => 'green',
            ],
            [
                'label' => 'Institution Admins',
                'value' => $this->countWhere('users', ['role' => 5]),
                'sub' => 'School-level managers',
                'tone' => 'purple',
            ],
            [
                'label' => 'Institutions',
                'value' => $this->count('institutions'),
                'sub' => $this->countWhere('institutions', [
                    'status' => 'active',
                ]) . ' active',
                'tone' => 'orange',
            ],
            [
                'label' => 'Module Library',
                'value' => $this->count('module_library_items'),
                'sub' => $this->countWhere('module_library_items', [
                    'is_active' => 1,
                ]) . ' active',
                'tone' => 'blue',
            ],
            [
                'label' => 'Challenges',
                'value' => $this->count('challenges'),
                'sub' => $this->countWhere('challenges', [
                    'is_coding_challenge' => 1,
                ]) . ' coding',
                'tone' => 'green',
            ],
            [
                'label' => 'Achievements',
                'value' => $this->count('achievement_definitions'),
                'sub' => $this->countWhere('achievement_definitions', [
                    'is_active' => 1,
                ]) . ' active',
                'tone' => 'purple',
            ],
            [
                'label' => 'Missions',
                'value' => $this->count('mission_definitions'),
                'sub' => $this->countWhere('mission_definitions', [
                    'is_active' => 1,
                ]) . ' active',
                'tone' => 'orange',
            ],
            [
                'label' => 'Anti-Cheat Events',
                'value' => $this->count('anti_cheat_events')
                    + $this->count('challenge_attempt_events'),
                'sub' => 'Assignment and challenge flags',
                'tone' => 'red',
            ],
            [
                'label' => 'Submissions',
                'value' => $this->count('assignment_submissions')
                    + $this->count('coding_submissions')
                    + $this->count('challenge_user'),
                'sub' => 'All learner attempts',
                'tone' => 'blue',
            ],
        ];
    }

    /**
     * Return the number of users belonging to each role.
     */
    private function roleDistribution(): array
    {
        if (
            ! $this->tableExists('users')
            || ! $this->columnExists('users', 'role')
        ) {
            return [];
        }

        $labels = [
            1 => 'Learner/Common User',
            2 => 'Admin',
            3 => 'Superadmin',
            4 => 'Instructor',
            5 => 'Institution Admin',
        ];

        return DB::table('users')
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->orderBy('role')
            ->get()
            ->map(fn ($row) => [
                'role' => (int) $row->role,
                'label' => $labels[(int) $row->role]
                    ?? ('Role ' . $row->role),
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * Return activity totals for the latest fourteen days.
     */
    private function activityTrend(): array
    {
        $days = [];
        $now = Carbon::now();

        for ($i = 13; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->toDateString();

            $days[$date] = [
                'date' => $date,
                'users' => 0,
                'mcq' => 0,
                'coding' => 0,
                'assignments' => 0,
                'total' => 0,
            ];
        }

        $activityTables = [
            'users' => 'users',
            'challenge_user' => 'mcq',
            'coding_submissions' => 'coding',
            'assignment_submissions' => 'assignments',
        ];

        foreach ($activityTables as $table => $key) {
            if (
                ! $this->tableExists($table)
                || ! $this->columnExists($table, 'created_at')
            ) {
                continue;
            }

            $rows = DB::table($table)
                ->selectRaw(
                    'DATE(created_at) as activity_date, COUNT(*) as total'
                )
                ->where(
                    'created_at',
                    '>=',
                    $now->copy()->subDays(13)->startOfDay()
                )
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy(DB::raw('DATE(created_at)'))
                ->get();

            foreach ($rows as $row) {
                $activityDate = (string) $row->activity_date;

                if (isset($days[$activityDate])) {
                    $days[$activityDate][$key] = (int) $row->total;
                }
            }
        }

        foreach ($days as &$row) {
            $row['total'] = $row['users']
                + $row['mcq']
                + $row['coding']
                + $row['assignments'];
        }

        unset($row);

        return array_values($days);
    }

    /**
     * Return the most recently created non-superadministrator users.
     */
    private function recentUsers(int $limit = 8): array
    {
        if (! $this->tableExists('users')) {
            return [];
        }

        $query = DB::table('users')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.role',
                'users.status',
                'users.created_at'
            );

        if (
            $this->tableExists('institutions')
            && $this->columnExists('users', 'institution_id')
            && $this->columnExists('institutions', 'id')
            && $this->columnExists('institutions', 'name')
        ) {
            $query
                ->leftJoin(
                    'institutions',
                    'institutions.id',
                    '=',
                    'users.institution_id'
                )
                ->addSelect(
                    'institutions.name as institution_name'
                );
        } else {
            $query->addSelect(
                DB::raw('NULL as institution_name')
            );
        }

        return $query
            ->whereNotIn('users.role', [3])
            ->orderByDesc('users.created_at')
            ->limit($this->normalizeLimit($limit))
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /**
     * Return recent assignment anti-cheat events.
     */
    private function recentAntiCheatEvents(int $limit = 8): array
    {
        if (! $this->tableExists('anti_cheat_events')) {
            return [];
        }

        $query = DB::table('anti_cheat_events as e')
            ->select(
                'e.id',
                'e.event_type',
                'e.severity',
                'e.occurred_at',
                'e.created_at'
            );

        if (
            $this->tableExists('users')
            && $this->columnExists('anti_cheat_events', 'user_id')
            && $this->columnExists('users', 'id')
        ) {
            $query
                ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
                ->addSelect(
                    'u.name as user_name',
                    'u.email as user_email'
                );
        } else {
            $query->addSelect(
                DB::raw('NULL as user_name'),
                DB::raw('NULL as user_email')
            );
        }

        if (
            $this->tableExists('class_assignments')
            && $this->columnExists(
                'anti_cheat_events',
                'class_assignment_id'
            )
            && $this->columnExists('class_assignments', 'id')
        ) {
            $query
                ->leftJoin(
                    'class_assignments as a',
                    'a.id',
                    '=',
                    'e.class_assignment_id'
                )
                ->addSelect(
                    'a.title as assignment_title'
                );
        } else {
            $query->addSelect(
                DB::raw('NULL as assignment_title')
            );
        }

        return $query
            ->orderByDesc(
                DB::raw('COALESCE(e.occurred_at, e.created_at)')
            )
            ->limit($this->normalizeLimit($limit))
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /**
     * Return recent suspicious challenge-attempt events.
     */
    private function recentChallengeFlags(int $limit = 8): array
    {
        if (! $this->tableExists('challenge_attempt_events')) {
            return [];
        }

        $requiredTables = [
            'challenge_attempts',
            'users',
            'challenges',
        ];

        foreach ($requiredTables as $table) {
            if (! $this->tableExists($table)) {
                return [];
            }
        }

        return DB::table('challenge_attempt_events as e')
            ->leftJoin(
                'challenge_attempts as a',
                'a.id',
                '=',
                'e.challenge_attempt_id'
            )
            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                'a.user_id'
            )
            ->leftJoin(
                'challenges as c',
                'c.id',
                '=',
                'a.challenge_id'
            )
            ->select(
                'e.id',
                'e.event_type',
                'e.severity',
                'e.occurred_at',
                'e.created_at',
                'u.name as user_name',
                'u.email as user_email',
                'c.title as challenge_title'
            )
            ->orderByDesc(
                DB::raw('COALESCE(e.occurred_at, e.created_at)')
            )
            ->limit($this->normalizeLimit($limit))
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /**
     * Return recent coding-challenge submissions.
     */
    private function recentCodingSubmissions(int $limit = 20): array
    {
        if (! $this->tableExists('coding_submissions')) {
            return [];
        }

        $requiredTables = [
            'users',
            'coding_questions',
            'challenges',
        ];

        foreach ($requiredTables as $table) {
            if (! $this->tableExists($table)) {
                return [];
            }
        }

        return DB::table('coding_submissions as s')
            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                's.user_id'
            )
            ->leftJoin(
                'coding_questions as q',
                'q.id',
                '=',
                's.coding_question_id'
            )
            ->leftJoin(
                'challenges as c',
                'c.id',
                '=',
                'q.challenge_id'
            )
            ->select(
                's.id',
                's.status',
                's.tests_passed',
                's.tests_total',
                's.xp_earned',
                's.created_at',
                'u.name as user_name',
                'u.email as user_email',
                'c.title as challenge_title'
            )
            ->orderByDesc('s.created_at')
            ->limit($this->normalizeLimit($limit))
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /**
     * Return recent class-assignment submissions.
     */
    private function recentAssignmentSubmissions(
        int $limit = 20
    ): array {
        if (! $this->tableExists('assignment_submissions')) {
            return [];
        }

        $requiredTables = [
            'users',
            'class_assignments',
        ];

        foreach ($requiredTables as $table) {
            if (! $this->tableExists($table)) {
                return [];
            }
        }

        return DB::table('assignment_submissions as s')
            ->leftJoin(
                'users as u',
                'u.id',
                '=',
                's.student_id'
            )
            ->leftJoin(
                'class_assignments as a',
                'a.id',
                '=',
                's.class_assignment_id'
            )
            ->select(
                's.id',
                's.status',
                's.score',
                's.total_points',
                's.submitted_at',
                's.created_at',
                'u.name as user_name',
                'u.email as user_email',
                'a.title as assignment_title'
            )
            ->orderByDesc(
                DB::raw('COALESCE(s.submitted_at, s.created_at)')
            )
            ->limit($this->normalizeLimit($limit))
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    /**
     * Return a summary of the application's current configuration.
     */
    private function systemHealth(): array
    {
        $environment = (string) config('app.env');
        $debugEnabled = (bool) config('app.debug');

        $pythonDriver = (string) config(
            'code_execution.python.driver',
            env('PYTHON_SANDBOX_DRIVER', 'local')
        );

        $queueDriver = (string) config('queue.default');
        $cacheStore = (string) config('cache.default');

        return [
            [
                'label' => 'Application Environment',
                'value' => $environment,
                'status' => $debugEnabled ? 'warning' : 'ok',
                'note' => $debugEnabled
                    ? 'APP_DEBUG is enabled'
                    : 'Debug mode is off',
            ],
            [
                'label' => 'Python Sandbox',
                'value' => $pythonDriver,
                'status' => $environment === 'production'
                    && $pythonDriver !== 'docker'
                        ? 'danger'
                        : 'ok',
                'note' => 'Compiler isolation mode',
            ],
            [
                'label' => 'Queue Driver',
                'value' => $queueDriver,
                'status' => $queueDriver === 'sync'
                    ? 'warning'
                    : 'ok',
                'note' => 'Use database or Redis for production',
            ],
            [
                'label' => 'Cache Store',
                'value' => $cacheStore,
                'status' => 'ok',
                'note' => 'Current cache backend',
            ],
        ];
    }

    /**
     * Count all rows from a table.
     */
    private function count(string $table): int
    {
        if (! $this->tableExists($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    /**
     * Count rows matching the provided conditions.
     */
    private function countWhere(
        string $table,
        array $conditions
    ): int {
        if (! $this->tableExists($table)) {
            return 0;
        }

        $query = DB::table($table);

        foreach ($conditions as $column => $value) {
            if (! $this->columnExists($table, (string) $column)) {
                return 0;
            }

            $query->where($column, $value);
        }

        return (int) $query->count();
    }

    /**
     * Check whether a table exists.
     *
     * This avoids Laravel's newer schema-inspection query and remains
     * compatible with MySQL 5.5.
     */
    private function tableExists(string $table): bool
    {
        if (! $this->isSafeIdentifier($table)) {
            return false;
        }

        $result = DB::selectOne(
            '
                SELECT COUNT(*) AS aggregate_count
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
            ',
            [$table]
        );

        return (int) ($result->aggregate_count ?? 0) > 0;
    }

    /**
     * Check whether a column exists.
     *
     * This intentionally does not request the GENERATION_EXPRESSION
     * metadata field, which is unavailable in MySQL 5.5.
     */
    private function columnExists(
        string $table,
        string $column
    ): bool {
        if (
            ! $this->isSafeIdentifier($table)
            || ! $this->isSafeIdentifier($column)
        ) {
            return false;
        }

        $result = DB::selectOne(
            '
                SELECT COUNT(*) AS aggregate_count
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = ?
            ',
            [$table, $column]
        );

        return (int) ($result->aggregate_count ?? 0) > 0;
    }

    /**
     * Restrict dynamic identifiers to letters, digits, and underscores.
     */
    private function isSafeIdentifier(string $identifier): bool
    {
        return preg_match(
            '/^[A-Za-z_][A-Za-z0-9_]*$/',
            $identifier
        ) === 1;
    }

    /**
     * Prevent invalid or excessively large query limits.
     */
    private function normalizeLimit(int $limit): int
    {
        return max(1, min($limit, 100));
    }
}

