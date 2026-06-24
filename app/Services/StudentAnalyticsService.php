<?php

namespace App\Services;

use App\Models\AssignmentSubmission;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\CodingSubmission;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Rank;
use App\Models\StudentDataToolkitActivity;
use App\Models\StudentIloMastery;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentAnalyticsService
{
    public function build(User $student): array
    {
        $xp = (int) ($student->xp ?? 0);
        $currentRank = Rank::currentForXp($xp);
        $nextRank = Rank::nextForXp($xp);

        return [
            'summary' => $this->summary($student),
            'rank' => [
                'current' => $currentRank,
                'next' => $nextRank,
                'progress_percent' => $student->rankProgressPercent(),
                'xp_to_next' => $nextRank ? max(0, (int) $nextRank->exp_required - $xp) : 0,
            ],
            'modules' => $this->moduleAnalytics($student),
            'lessons' => $this->lessonAnalytics($student),
            'challenges' => $this->challengeAnalytics($student),
            'coding' => $this->codingAnalytics($student),
            'assignments' => $this->assignmentAnalytics($student),
            'ilo_mastery' => $this->iloMastery($student),
            'achievements' => $this->achievementAnalytics($student),
            'data_toolkit' => $this->dataToolkitAnalytics($student),
            'activity' => $this->activitySeries($student),
            'recommendations' => $this->recommendations($student),
        ];
    }

    private function summary(User $student): array
    {
        return [
            'xp' => (int) ($student->xp ?? 0),
            'streak' => (int) ($student->streak ?? 0),
            'last_activity' => $student->last_activity,
            'engagement_score' => $this->engagementScore($student),
        ];
    }

    private function moduleAnalytics(User $student): array
    {
        if (! Schema::hasTable('module_user')) {
            return ['completed' => 0, 'unlocked' => 0, 'total' => Module::count(), 'percent' => 0];
        }

        $total = max(0, Module::count());
        $completed = DB::table('module_user')->where('user_id', $student->id)->where('is_completed', true)->count();
        $unlocked = DB::table('module_user')->where('user_id', $student->id)->where('is_unlocked', true)->count();

        return [
            'completed' => $completed,
            'unlocked' => $unlocked,
            'total' => $total,
            'percent' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }

    private function lessonAnalytics(User $student): array
    {
        if (! Schema::hasTable('lesson_user')) {
            return ['completed' => 0, 'total' => Lesson::count(), 'percent' => 0];
        }

        $total = max(0, Lesson::count());
        $completed = DB::table('lesson_user')->where('user_id', $student->id)->where('is_completed', true)->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? round(($completed / $total) * 100, 1) : 0,
        ];
    }

    private function challengeAnalytics(User $student): array
    {
        $legacyAttempts = Schema::hasTable('challenge_user')
            ? DB::table('challenge_user')->where('user_id', $student->id)->get()
            : collect();

        $serverAttempts = class_exists(ChallengeAttempt::class) && Schema::hasTable('challenge_attempts')
            ? ChallengeAttempt::where('user_id', $student->id)->whereIn('status', ['submitted', 'expired'])->get()
            : collect();

        $completed = $legacyAttempts->count() + $serverAttempts->count();
        $totalScore = (float) $legacyAttempts->sum('score') + (float) $serverAttempts->sum('score');
        $averageScore = $completed > 0 ? round($totalScore / $completed, 1) : 0;
        $rankedEligible = $serverAttempts->where('is_leaderboard_eligible', true)->where('is_ranked', true)->count();
        $flagged = $serverAttempts->where('is_leaderboard_eligible', false)->count();

        return [
            'completed' => $completed,
            'average_score' => $averageScore,
            'ranked_eligible' => $rankedEligible,
            'flagged' => $flagged,
            'available' => Challenge::where('is_coding_challenge', false)->count(),
        ];
    }

    private function codingAnalytics(User $student): array
    {
        if (! Schema::hasTable('coding_submissions')) {
            return ['submissions' => 0, 'passed' => 0, 'pass_rate' => 0, 'test_case_rate' => 0];
        }

        $submissions = CodingSubmission::where('user_id', $student->id)->where('voided', false)->get();
        $total = $submissions->count();
        $passed = $submissions->where('status', 'passed')->count();
        $testsPassed = (int) $submissions->sum('tests_passed');
        $testsTotal = max(0, (int) $submissions->sum('tests_total'));

        return [
            'submissions' => $total,
            'passed' => $passed,
            'pass_rate' => $total > 0 ? round(($passed / $total) * 100, 1) : 0,
            'test_case_rate' => $testsTotal > 0 ? round(($testsPassed / $testsTotal) * 100, 1) : 0,
        ];
    }

    private function assignmentAnalytics(User $student): array
    {
        if (! Schema::hasTable('assignment_submissions')) {
            return ['submitted' => 0, 'graded' => 0, 'late' => 0, 'average_score' => 0];
        }

        $submissions = AssignmentSubmission::where('student_id', $student->id)->get();
        $graded = $submissions->where('status', 'graded');
        $scored = $graded->filter(fn ($submission) => (float) $submission->total_points > 0);

        return [
            'submitted' => $submissions->whereIn('status', ['submitted', 'late', 'graded'])->count(),
            'graded' => $graded->count(),
            'late' => $submissions->where('status', 'late')->count(),
            'average_score' => $scored->count() > 0
                ? round($scored->avg(fn ($submission) => ((float) $submission->score / max(1, (float) $submission->total_points)) * 100), 1)
                : 0,
        ];
    }

    private function iloMastery(User $student): Collection
    {
        if (! Schema::hasTable('student_ilo_masteries')) {
            return collect();
        }

        return StudentIloMastery::with('ilo')
            ->where('student_id', $student->id)
            ->orderByDesc('mastery_percent')
            ->limit(8)
            ->get();
    }

    private function achievementAnalytics(User $student): array
    {
        if (! Schema::hasTable('achievement_definitions') || ! Schema::hasTable('user_achievements')) {
            return ['unlocked' => 0, 'total' => 0, 'percent' => 0, 'latest' => collect()];
        }

        $total = DB::table('achievement_definitions')->where('is_active', true)->count();
        $unlocked = UserAchievement::with('achievement')
            ->where('user_id', $student->id)
            ->latest('unlocked_at')
            ->get();

        return [
            'unlocked' => $unlocked->count(),
            'total' => $total,
            'percent' => $total > 0 ? round(($unlocked->count() / $total) * 100, 1) : 0,
            'latest' => $unlocked->take(4),
        ];
    }

    private function dataToolkitAnalytics(User $student): array
    {
        if (! Schema::hasTable('student_data_toolkit_activities')) {
            return [
                'activities' => 0,
                'datasets_explored' => 0,
                'analyses_run' => 0,
                'reports_generated' => 0,
                'latest_dataset' => null,
                'latest_activity_at' => null,
            ];
        }

        $activities = StudentDataToolkitActivity::where('user_id', $student->id)->get();
        $latest = $activities->sortByDesc('created_at')->first();

        return [
            'activities' => $activities->count(),
            'datasets_explored' => $activities->pluck('dataset_key')->unique()->count(),
            'analyses_run' => $activities->filter(fn ($activity) => str_ends_with((string) $activity->activity_type, '_analysis'))->count(),
            'reports_generated' => $activities->where('activity_type', 'report_view')->count(),
            'latest_dataset' => $latest?->dataset_key,
            'latest_activity_at' => $latest?->created_at,
        ];
    }

    private function activitySeries(User $student): array
    {
        $days = collect(range(13, 0))->map(fn (int $daysAgo) => now()->subDays($daysAgo)->toDateString());

        $coding = Schema::hasTable('coding_submissions')
            ? CodingSubmission::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('user_id', $student->id)
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')
                ->pluck('total', 'day')
            : collect();

        $assignments = Schema::hasTable('assignment_submissions')
            ? AssignmentSubmission::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('student_id', $student->id)
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')
                ->pluck('total', 'day')
            : collect();

        $toolkit = Schema::hasTable('student_data_toolkit_activities')
            ? StudentDataToolkitActivity::selectRaw('DATE(created_at) as day, COUNT(*) as total')
                ->where('user_id', $student->id)
                ->where('created_at', '>=', now()->subDays(14))
                ->groupBy('day')
                ->pluck('total', 'day')
            : collect();

        return $days->map(fn (string $day) => [
            'day' => $day,
            'total' => (int) ($coding[$day] ?? 0) + (int) ($assignments[$day] ?? 0) + (int) ($toolkit[$day] ?? 0),
        ])->values()->all();
    }

    private function engagementScore(User $student): int
    {
        $score = min(35, ((int) ($student->streak ?? 0)) * 5);
        $score += min(35, (int) floor(((int) ($student->xp ?? 0)) / 300));

        if (Schema::hasTable('student_data_toolkit_activities')) {
            $toolkitActivities = StudentDataToolkitActivity::where('user_id', $student->id)->count();
            $score += min(15, $toolkitActivities * 3);
        }

        if ($student->last_activity && $student->last_activity >= now()->subDays(3)) {
            $score += 30;
        }

        return min(100, $score);
    }

    private function recommendations(User $student): array
    {
        $items = [];
        $coding = $this->codingAnalytics($student);
        $assignments = $this->assignmentAnalytics($student);
        $lessons = $this->lessonAnalytics($student);
        $toolkit = $this->dataToolkitAnalytics($student);

        if ($toolkit['activities'] === 0) {
            $items[] = 'Open the Data Toolkit to practice descriptive statistics, charts, correlation, and regression.';
        } elseif ($toolkit['analyses_run'] < 3) {
            $items[] = 'Run more Data Toolkit analyses to strengthen your data interpretation skills.';
        }

        if ($lessons['percent'] < 70) {
            $items[] = 'Continue lessons before attempting harder challenges.';
        }

        if ($coding['test_case_rate'] > 0 && $coding['test_case_rate'] < 75) {
            $items[] = 'Practice debugging using visible and hidden test-case patterns.';
        }

        if ($assignments['late'] > 0) {
            $items[] = 'Prioritize pending assignments to protect your class performance.';
        }

        if ($items === []) {
            $items[] = 'Maintain your streak and attempt a higher difficulty path when available.';
        }

        return $items;
    }
}
