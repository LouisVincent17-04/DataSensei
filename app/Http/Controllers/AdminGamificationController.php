<?php

namespace App\Http\Controllers;

use App\Models\AchievementDefinition;
use App\Models\MissionDefinition;
use App\Models\Rank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminGamificationController extends Controller
{
    public function index(): View
    {
        $achievements = AchievementDefinition::query()
            ->withCount('unlocks')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'achievements_page')
            ->withQueryString();

        $missions = MissionDefinition::query()
            ->withCount('progress')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(10, ['*'], 'missions_page')
            ->withQueryString();

        $ranks = Rank::query()->orderBy('exp_required')->get();

        return view('admin.gamification.index', compact('achievements', 'missions', 'ranks'));
    }

    public function updateAchievement(Request $request, AchievementDefinition $achievement): RedirectResponse
    {
        $data = $request->validate([
            'achievement_key' => ['required', 'string', 'max:100', Rule::unique('achievement_definitions', 'achievement_key')->ignore($achievement->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge_color' => ['nullable', 'string', 'max:50'],
            'xp_reward' => ['required', 'integer', 'min:0', 'max:100000'],
            'criteria_type' => ['required', 'string', 'max:80'],
            'criteria_value' => ['required', 'integer', 'min:0', 'max:1000000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $achievement->update([
            'achievement_key' => $data['achievement_key'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'icon' => strtoupper(trim($data['icon'] ?? 'ACH')),
            'badge_color' => $data['badge_color'] ?? 'blue',
            'xp_reward' => (int) $data['xp_reward'],
            'criteria_type' => $data['criteria_type'],
            'criteria_value' => (int) $data['criteria_value'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.gamification.index')->with('success', 'Achievement updated.');
    }

    public function updateMission(Request $request, MissionDefinition $mission): RedirectResponse
    {
        $data = $request->validate([
            'mission_key' => ['required', 'string', 'max:100', Rule::unique('mission_definitions', 'mission_key')->ignore($mission->id)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'period_type' => ['required', Rule::in(['daily', 'weekly'])],
            'target_type' => ['required', 'string', 'max:80'],
            'target_count' => ['required', 'integer', 'min:1', 'max:1000000'],
            'xp_reward' => ['required', 'integer', 'min:0', 'max:100000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $mission->update([
            'mission_key' => $data['mission_key'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'period_type' => $data['period_type'],
            'target_type' => $data['target_type'],
            'target_count' => (int) $data['target_count'],
            'xp_reward' => (int) $data['xp_reward'],
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.gamification.index')->with('success', 'Mission updated.');
    }
}
