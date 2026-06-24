<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardService
{
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

    private function cards(): array
    {
        return [
            ['label' => 'Learner Accounts', 'value' => $this->countWhere('users', ['role' => 1]), 'sub' => $this->countWhere('users', ['role' => 1, 'status' => 'active']) . ' active', 'tone' => 'blue'],
            ['label' => 'Instructors', 'value' => $this->countWhere('users', ['role' => 4]), 'sub' => $this->countWhere('users', ['role' => 4, 'status' => 'active']) . ' active', 'tone' => 'green'],
            ['label' => 'Institution Admins', 'value' => $this->countWhere('users', ['role' => 5]), 'sub' => 'School-level managers', 'tone' => 'purple'],
            ['label' => 'Institutions', 'value' => $this->count('institutions'), 'sub' => $this->countWhere('institutions', ['status' => 'active']) . ' active', 'tone' => 'orange'],
            ['label' => 'Module Library', 'value' => $this->count('module_library_items'), 'sub' => $this->countWhere('module_library_items', ['is_active' => 1]) . ' active', 'tone' => 'blue'],
            ['label' => 'Challenges', 'value' => $this->count('challenges'), 'sub' => $this->countWhere('challenges', ['is_coding_challenge' => 1]) . ' coding', 'tone' => 'green'],
            ['label' => 'Achievements', 'value' => $this->count('achievement_definitions'), 'sub' => $this->countWhere('achievement_definitions', ['is_active' => 1]) . ' active', 'tone' => 'purple'],
            ['label' => 'Missions', 'value' => $this->count('mission_definitions'), 'sub' => $this->countWhere('mission_definitions', ['is_active' => 1]) . ' active', 'tone' => 'orange'],
            ['label' => 'Anti-Cheat Events', 'value' => $this->count('anti_cheat_events') + $this->count('challenge_attempt_events'), 'sub' => 'Assignment and challenge flags', 'tone' => 'red'],
            ['label' => 'Submissions', 'value' => $this->count('assignment_submissions') + $this->count('coding_submissions') + $this->count('challenge_user'), 'sub' => 'All learner attempts', 'tone' => 'blue'],
        ];
    }

    private function roleDistribution(): array
    {
        if (! Schema::hasTable('users')) {
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
                'label' => $labels[(int) $row->role] ?? ('Role ' . $row->role),
                'total' => (int) $row->total,
            ])
            ->all();
    }

    private function activityTrend(): array
    {
        $days = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->toDateString();
            $days[$date] = [
                'date' => $date,
                'users' => 0,
                'mcq' => 0,
                'coding' => 0,
                'assignments' => 0,
                'total' => 0,
            ];
        }

        foreach ([
            'users' => 'users',
            'challenge_user' => 'mcq',
            'coding_submissions' => 'coding',
            'assignment_submissions' => 'assignments',
        ] as $table => $key) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'created_at')) {
                continue;
            }

            $rows = DB::table($table)
                ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
                ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
                ->groupBy('activity_date')
                ->get();

            foreach ($rows as $row) {
                if (isset($days[$row->activity_date])) {
                    $days[$row->activity_date][$key] = (int) $row->total;
                }
            }
        }

        foreach ($days as &$row) {
            $row['total'] = $row['users'] + $row['mcq'] + $row['coding'] + $row['assignments'];
        }

        return array_values($days);
    }

    private function recentUsers(int $limit = 8): array
    {
        if (! Schema::hasTable('users')) {
            return [];
        }

        return DB::table('users')
            ->leftJoin('institutions', 'institutions.id', '=', 'users.institution_id')
            ->select('users.id', 'users.name', 'users.email', 'users.role', 'users.status', 'users.created_at', 'institutions.name as institution_name')
            ->whereNotIn('users.role', [3])
            ->orderByDesc('users.created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function recentAntiCheatEvents(int $limit = 8): array
    {
        if (! Schema::hasTable('anti_cheat_events')) {
            return [];
        }

        return DB::table('anti_cheat_events as e')
            ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
            ->leftJoin('class_assignments as a', 'a.id', '=', 'e.class_assignment_id')
            ->select('e.id', 'e.event_type', 'e.severity', 'e.occurred_at', 'e.created_at', 'u.name as user_name', 'u.email as user_email', 'a.title as assignment_title')
            ->orderByDesc(DB::raw('COALESCE(e.occurred_at, e.created_at)'))
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function recentChallengeFlags(int $limit = 8): array
    {
        if (! Schema::hasTable('challenge_attempt_events')) {
            return [];
        }

        return DB::table('challenge_attempt_events as e')
            ->leftJoin('challenge_attempts as a', 'a.id', '=', 'e.challenge_attempt_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->leftJoin('challenges as c', 'c.id', '=', 'a.challenge_id')
            ->select('e.id', 'e.event_type', 'e.severity', 'e.occurred_at', 'e.created_at', 'u.name as user_name', 'u.email as user_email', 'c.title as challenge_title')
            ->orderByDesc(DB::raw('COALESCE(e.occurred_at, e.created_at)'))
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function recentCodingSubmissions(int $limit = 20): array
    {
        if (! Schema::hasTable('coding_submissions')) {
            return [];
        }

        return DB::table('coding_submissions as s')
            ->leftJoin('users as u', 'u.id', '=', 's.user_id')
            ->leftJoin('coding_questions as q', 'q.id', '=', 's.coding_question_id')
            ->leftJoin('challenges as c', 'c.id', '=', 'q.challenge_id')
            ->select('s.id', 's.status', 's.tests_passed', 's.tests_total', 's.xp_earned', 's.created_at', 'u.name as user_name', 'u.email as user_email', 'c.title as challenge_title')
            ->orderByDesc('s.created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function recentAssignmentSubmissions(int $limit = 20): array
    {
        if (! Schema::hasTable('assignment_submissions')) {
            return [];
        }

        return DB::table('assignment_submissions as s')
            ->leftJoin('users as u', 'u.id', '=', 's.student_id')
            ->leftJoin('class_assignments as a', 'a.id', '=', 's.class_assignment_id')
            ->select('s.id', 's.status', 's.score', 's.total_points', 's.submitted_at', 's.created_at', 'u.name as user_name', 'u.email as user_email', 'a.title as assignment_title')
            ->orderByDesc(DB::raw('COALESCE(s.submitted_at, s.created_at)'))
            ->limit($limit)
            ->get()
            ->map(fn ($row) => (array) $row)
            ->all();
    }

    private function systemHealth(): array
    {
        return [
            ['label' => 'Application Environment', 'value' => config('app.env'), 'status' => config('app.debug') ? 'warning' : 'ok', 'note' => config('app.debug') ? 'APP_DEBUG is enabled' : 'Debug mode is off'],
            ['label' => 'Python Sandbox', 'value' => config('code_execution.python.driver', env('PYTHON_SANDBOX_DRIVER', 'local')), 'status' => config('app.env') === 'production' && env('PYTHON_SANDBOX_DRIVER', 'local') !== 'docker' ? 'danger' : 'ok', 'note' => 'Compiler isolation mode'],
            ['label' => 'Queue Driver', 'value' => config('queue.default'), 'status' => config('queue.default') === 'sync' ? 'warning' : 'ok', 'note' => 'Use database/redis for production'],
            ['label' => 'Cache Store', 'value' => config('cache.default'), 'status' => 'ok', 'note' => 'Current cache backend'],
        ];
    }

    private function count(string $table): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        return (int) DB::table($table)->count();
    }

    private function countWhere(string $table, array $conditions): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);

        foreach ($conditions as $column => $value) {
            if (! Schema::hasColumn($table, $column)) {
                return 0;
            }
            $query->where($column, $value);
        }

        return (int) $query->count();
    }
}
