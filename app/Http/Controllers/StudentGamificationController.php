<?php

namespace App\Http\Controllers;

use App\Models\AchievementDefinition;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StudentGamificationController extends Controller
{
    public function achievements(GamificationService $gamification)
    {
        $user = Auth::user();

        $definitions = Schema::hasTable('achievement_definitions')
            ? AchievementDefinition::where('is_active', true)->orderBy('sort_order')->get()
            : collect();

        $unlocked = Schema::hasTable('user_achievements')
            ? UserAchievement::with('achievement')
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('achievement_definition_id')
            : collect();

        $missions = $gamification->currentMissions($user);

        $stats = [
            'unlocked_count' => $unlocked->count(),
            'total_count' => $definitions->count(),
            'xp' => (int) $user->xp,
            'streak' => (int) $user->streak,
        ];

        return view('student.gamification.achievements', compact('definitions', 'unlocked', 'missions', 'stats'));
    }

    public function leaderboard(Request $request)
    {
        $period = 'all';

        $achievementCounts = Schema::hasTable('user_achievements')
            ? DB::table('user_achievements')
                ->select('user_id', DB::raw('COUNT(*) as achievements_count'))
                ->groupBy('user_id')
            : null;

        $users = User::query()
            ->where('role', User::ROLE_USER)
            ->where('status', 'active')
            ->when($achievementCounts, function ($query) use ($achievementCounts) {
                $query->leftJoinSub($achievementCounts, 'ua', 'ua.user_id', '=', 'users.id')
                    ->select('users.*', DB::raw('COALESCE(ua.achievements_count, 0) as achievements_count'));
            }, function ($query) {
                $query->select('users.*', DB::raw('0 as achievements_count'));
            })
            ->orderByDesc('xp')
            ->orderByDesc('streak')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $currentRank = User::where('role', User::ROLE_USER)
            ->where('status', 'active')
            ->where('xp', '>', Auth::user()->xp ?? 0)
            ->distinct()
            ->count('xp') + 1;

        return view('student.gamification.leaderboard', compact('users', 'period', 'currentRank'));
    }
}
