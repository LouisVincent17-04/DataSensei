<?php

namespace App\Http\Controllers;

use App\Models\AchievementDefinition;
use App\Models\MissionDefinition;
use App\Models\Rank;
use App\Models\UserAchievement;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gamification Management (DataSensei Updates 7, task 3).
 *
 * Achievements and missions are predefined by the system.
 *
 *   achievements  fixed system records (DataSensei Updates 9): name,
 *                 description, rule and EXP cannot be edited. One can be
 *                 removed only while nobody has earned it, so earned
 *                 achievements stay in learners' records.
 *   missions      name, EXP, description, and whether it is daily or weekly
 *
 * The rule behind each one (what is measured and how much of it), its key,
 * code mark, badge colour, order and on/off state are system settings and
 * are neither shown nor accepted here. Achievements and missions that are
 * switched off (class work gives no XP) are not listed, because learners
 * never see them. Admins cannot create achievements or missions.
 */
class AdminGamificationController extends Controller
{
    public const EARNED_ACHIEVEMENT_MESSAGE = 'This Achievement cannot be removed because it has already been earned by one or more users.';

    public const FREQUENCIES = ['daily' => 'Daily', 'weekly' => 'Weekly'];

    public function index(): View
    {
        $achievements = AchievementDefinition::query()
            ->where('is_active', true)
            ->withCount('unlocks')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'achievements_page')
            ->withQueryString();

        $missions = MissionDefinition::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(10, ['*'], 'missions_page')
            ->withQueryString();

        $ranks = Rank::query()->orderBy('exp_required')->get();
        $frequencies = self::FREQUENCIES;

        return view('admin.gamification.index', compact('achievements', 'missions', 'ranks', 'frequencies'));
    }

    /**
     * Unlock, for every active student, each achievement they already qualify
     * for from saved work. Safe to repeat; nothing is unlocked twice.
     */
    public function syncAchievements(GamificationService $gamification): RedirectResponse
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $summary = $gamification->syncAllLearners();

        $message = 'Achievements synced for '.number_format($summary['students']).' student(s): '
            .number_format($summary['unlocked']).' newly unlocked'
            .($summary['unlocked'] > 0 ? ' (+'.number_format($summary['xp']).' XP in total).' : '. Everyone was already up to date.');

        return redirect()->route('admin.gamification.index')->with('success', $message);
    }

    /**
     * Removes an achievement nobody has earned yet. One that has been earned
     * stays, so learners keep it in their records.
     */
    public function destroyAchievement(AchievementDefinition $achievement): RedirectResponse
    {
        $result = DB::transaction(function () use ($achievement): string {
            $locked = AchievementDefinition::query()->whereKey($achievement->id)->lockForUpdate()->first();

            if ($locked === null) {
                return 'missing';
            }

            if (UserAchievement::query()->where('achievement_definition_id', $locked->id)->exists()) {
                return 'earned';
            }

            $locked->delete();

            return 'deleted';
        }, 3);

        if ($result === 'earned') {
            return $this->backToList('achievements', self::EARNED_ACHIEVEMENT_MESSAGE, 'error');
        }

        return $this->backToList('achievements', $result === 'deleted' ? '"'.$achievement->name.'" was removed.' : 'That achievement was already removed.');
    }

    /** Name, EXP, description and frequency (daily or weekly) only. */
    public function updateMission(Request $request, MissionDefinition $mission): RedirectResponse
    {
        $data = $request->validateWithBag('mission_'.$mission->id, [
            'title' => ['required', 'string', 'max:189'],
            'xp_reward' => ['required', 'integer', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'period_type' => ['required', Rule::in(array_keys(self::FREQUENCIES))],
        ], [], [
            'title' => 'mission name',
            'xp_reward' => 'EXP',
            'period_type' => 'frequency',
        ]);

        $mission->update([
            'title' => trim($data['title']),
            'xp_reward' => (int) $data['xp_reward'],
            'description' => trim((string) ($data['description'] ?? '')) ?: null,
            'period_type' => $data['period_type'],
        ]);

        return $this->backToList('mission-'.$mission->id, 'Mission updated.');
    }

    /** Back to the same page of the list, at the item or section that was edited. */
    private function backToList(string $section, string $message, string $flash = 'success'): RedirectResponse
    {
        $previous = (string) url()->previous();
        $index = route('admin.gamification.index');
        $target = str_starts_with($previous, $index) ? strtok($previous, '#') : $index;

        return redirect()->to($target.'#'.$section)->with($flash, $message)->with('saved_item', $flash === 'success' ? $section : null);
    }
}
