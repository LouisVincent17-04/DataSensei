<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\ClassChallengeAssignment;
use App\Models\IdeExecutionLog;
use App\Models\Module;
use App\Models\User;
use App\Services\ChallengeModuleAccessService;
use App\Services\ChallengePathUnlockService;
use App\Services\GamificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function dashboard(ChallengePathUnlockService $pathUnlocks, GamificationService $gamification, ChallengeModuleAccessService $moduleAccess)
    {
        /** @var User $user */
        $user = Auth::user();

        $this->ensureFirstModuleUnlocked($user);

        // Unlock any achievement the learner already qualifies for, so the XP
        // and rank below include it. A rewards problem never blocks the page.
        try {
            $gamification->evaluateAchievements($user);
            $user->refresh();
        } catch (\Throwable $exception) {
            report($exception);
        }

        $completedLessonsByModule = DB::table('lesson_user')
            ->join('lessons', 'lessons.id', '=', 'lesson_user.lesson_id')
            ->where('lesson_user.user_id', $user->id)
            ->where('lesson_user.is_completed', true)
            ->select('lessons.module_id', DB::raw('COUNT(DISTINCT lessons.id) AS completed_count'))
            ->groupBy('lessons.module_id')
            ->pluck('completed_count', 'lessons.module_id');

        $moduleStates = DB::table('module_user')
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('module_id');

        $learningModules = Module::withCount('lessons')
            ->published()
            ->orderBy('order_index')
            ->orderBy('id')
            ->get()
            ->map(function (Module $module) use ($completedLessonsByModule, $moduleStates): array {
                $completed = (int) ($completedLessonsByModule[$module->id] ?? 0);
                $state = $moduleStates->get($module->id);
                $total = (int) $module->lessons_count;

                return [
                    'id' => $module->id,
                    'title' => $module->title,
                    'completed_lessons' => $completed,
                    'total_lessons' => $total,
                    'progress' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
                    'is_unlocked' => (bool) ($state->is_unlocked ?? false),
                    'is_completed' => (bool) ($state->is_completed ?? false),
                ];
            });

        $scorePercentages = $this->scorePercentages($user->id);
        $passedChallengeIds = ChallengeAttempt::query()
            ->where('user_id', $user->id)
            ->where('is_ranked', true)
            ->whereIn('status', ['submitted', 'expired'])
            ->where('total_questions', '>', 0)
            ->whereRaw('score * 100 >= total_questions * 70')
            ->distinct()
            ->pluck('challenge_id');

        $challengeSeconds = (int) ChallengeAttempt::query()
            ->where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'expired'])
            ->sum('time_taken_seconds');
        $executionMilliseconds = (int) IdeExecutionLog::where('user_id', $user->id)
            ->sum('execution_time_ms');

        $stats = [
            'completed_modules' => $learningModules->where('is_completed', true)->count(),
            'total_modules' => $learningModules->count(),
            'average_score' => $scorePercentages->isNotEmpty()
                ? (int) round((float) $scorePercentages->avg())
                : 0,
            'passed_challenges' => $passedChallengeIds->count(),
            'tracked_minutes' => (int) round(($challengeSeconds + ($executionMilliseconds / 1000)) / 60),
        ];

        // Build each challenge path once per request. Previously this was
        // recalculated for every challenge in the collection, causing repeated
        // enrollment and progress queries during login/dashboard navigation.
        $challengePathLocks = [
            'mcq' => $pathUnlocks->buildPathLocks($user, 'mcq'),
            'coding' => $pathUnlocks->buildPathLocks($user, 'coding'),
        ];

        // Class work first: challenges an instructor opened for one of the
        // learner's classes are available whatever the path progress. Other
        // instructor-built challenges (other classes, drafts, closed windows)
        // are never listed; they used to show here although the challenge
        // pages would not open them.
        $openClassChallengeIds = $this->openClassChallengeIds($user);

        $openChallenges = Challenge::query()
            ->active()
            ->with('category:id,slug,name')
            ->withCount(['questions', 'codingQuestions'])
            ->where(function ($query) use ($openClassChallengeIds): void {
                $query->platform();

                if ($openClassChallengeIds !== []) {
                    $query->orWhereIn('id', $openClassChallengeIds);
                }
            })
            ->whereNotIn('id', $passedChallengeIds)
            ->orderBy('is_coding_challenge')
            ->orderBy('order_index')
            ->get()
            ->filter(function (Challenge $challenge) use ($challengePathLocks, $openClassChallengeIds, $moduleAccess, $user): bool {
                $slug = $challenge->category?->slug;
                $track = $challenge->is_coding_challenge ? 'coding' : 'mcq';

                if ($slug === null) {
                    return false;
                }

                if (in_array((int) $challenge->id, $openClassChallengeIds, true)) {
                    return true;
                }

                // An open level shows only the modules that are open in it:
                // a new level starts with its first module.
                return (bool) ($challengePathLocks[$track][$slug]['unlocked'] ?? false)
                    && $moduleAccess->isOpen($user, $challenge);
            })
            ->sortBy(fn (Challenge $challenge): int => in_array((int) $challenge->id, $openClassChallengeIds, true) ? 0 : 1)
            ->take(4)
            ->values();

        $leaderboard = User::query()
            ->where('role', User::ROLE_USER)
            ->where('status', 'active')
            ->orderByDesc('xp')
            ->orderByDesc('streak')
            ->orderBy('id')
            ->limit(5)
            ->get();

        $currentRank = User::query()
            ->where('role', User::ROLE_USER)
            ->where('status', 'active')
            ->where('xp', '>', (int) $user->xp)
            ->distinct()
            ->count('xp') + 1;

        $recentActivity = $this->recentActivity($user->id);
        $upcomingDeadlines = $this->upcomingDeadlines($user);

        return view('student.dashboard', compact(
            'user',
            'stats',
            'learningModules',
            'openChallenges',
            'leaderboard',
            'currentRank',
            'recentActivity',
            'upcomingDeadlines',
        ));
    }

    private function ensureFirstModuleUnlocked(User $user): void
    {
        if ($user->modules()->wherePivot('is_unlocked', true)->exists()) {
            return;
        }

        $firstModule = Module::published()->orderBy('order_index')->orderBy('id')->first();
        if ($firstModule) {
            $user->modules()->syncWithoutDetaching([
                $firstModule->id => ['is_unlocked' => true],
            ]);
        }
    }

    private function scorePercentages(int $userId): Collection
    {
        $assessmentScores = AssessmentSubmission::query()
            ->where('student_id', $userId)
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->where('total_points', '>', 0)
            ->get(['score', 'total_points'])
            ->map(fn (AssessmentSubmission $submission): float =>
                ((float) $submission->score / (float) $submission->total_points) * 100
            );

        $challengeScores = ChallengeAttempt::query()
            ->where('user_id', $userId)
            ->where('is_ranked', true)
            ->whereIn('status', ['submitted', 'expired'])
            ->where('total_questions', '>', 0)
            ->get(['score', 'total_questions'])
            ->map(fn (ChallengeAttempt $attempt): float =>
                ((float) $attempt->score / (float) $attempt->total_questions) * 100
            );

        return $assessmentScores->concat($challengeScores);
    }

    private function recentActivity(int $userId): Collection
    {
        $assessments = AssessmentSubmission::query()
            ->with('assessment:id,title')
            ->where('student_id', $userId)
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn (AssessmentSubmission $submission): array => [
                'type' => 'Assessment',
                'title' => $submission->assessment?->title ?? 'Assessment',
                'detail' => "Score {$submission->score}/{$submission->total_points}",
                'at' => $submission->submitted_at ?? $submission->updated_at,
            ]);

        $challenges = ChallengeAttempt::query()
            ->with('challenge:id,title')
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'expired'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn (ChallengeAttempt $attempt): array => [
                'type' => 'Challenge',
                'title' => $attempt->challenge?->title ?? 'Challenge',
                'detail' => "Score {$attempt->score}/{$attempt->total_questions}",
                'at' => $attempt->submitted_at ?? $attempt->updated_at,
            ]);

        return $assessments
            ->concat($challenges)
            ->filter(fn (array $item): bool => $item['at'] !== null)
            ->sortByDesc('at')
            ->take(6)
            ->values();
    }

    private function upcomingDeadlines(User $user): Collection
    {
        $classIds = $user->classesAsStudent()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return collect();
        }

        $assessments = Assessment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->where('status', 'published')
            ->whereNotNull('due_at')
            ->where('due_at', '>=', now())
            ->where(fn ($query) => $query->whereNull('available_at')->orWhere('available_at', '<=', now()))
            ->orderBy('due_at')
            ->limit(10)
            ->get()
            ->map(fn (Assessment $assessment): array => [
                'type' => $assessment->purposeLabel() ?? 'Assessment',
                'title' => $assessment->title,
                'class_name' => $assessment->classRoom?->name,
                'due_at' => $assessment->due_at,
                'url' => route('student.assessments.show', $assessment),
            ]);

        // Challenges shared with a class are practice and have no due date
        // (DataSensei Updates 9); assessments carry every deadline now that
        // they also serve as homework (DataSensei Updates 11).
        return $assessments
            ->sortBy('due_at')
            ->take(6)
            ->values();
    }

    /**
     * Challenges (platform-pool or instructor-built) that a published class
     * assignment currently opens for one of the learner's active classes.
     *
     * @return array<int, int>
     */
    private function openClassChallengeIds(User $user): array
    {
        $classIds = $user->classesAsStudent()->active()->pluck('classes.id');

        if ($classIds->isEmpty()) {
            return [];
        }

        return ClassChallengeAssignment::query()
            ->openNow()
            ->whereIn('class_id', $classIds)
            ->pluck('challenge_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}
