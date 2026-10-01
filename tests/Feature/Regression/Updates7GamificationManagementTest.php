<?php

namespace Tests\Feature\Regression;

use App\Http\Controllers\AdminGamificationController;
use App\Models\AchievementDefinition;
use App\Models\Institution;
use App\Models\MissionDefinition;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 7, task 3: simpler Gamification Management.
 *
 * Achievements are predefined and, since DataSensei Updates 9, read-only:
 * admins can remove one only while nobody has earned it. Missions: name, EXP,
 * description and a daily or weekly frequency. Keys, code marks, badge
 * colours, criteria, order and on/off state are not shown or accepted.
 */
class Updates7GamificationManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_page_shows_only_user_facing_fields_and_no_way_to_create(): void
    {
        $achievement = $this->achievement();
        $retired = $this->achievement(['achievement_key' => 'assignment_ready', 'name' => 'Assignment Ready', 'is_active' => false]);
        $mission = $this->mission();

        $html = $this->authenticateAs($this->roleUser(User::ROLE_ADMIN))
            ->get(route('admin.gamification.index'))
            ->assertOk()
            ->assertDontSee('Achievement name')
            ->assertSee('Mission name')
            ->assertSee('Frequency')
            ->assertSee('First Challenge Pass')
            ->assertSee('Pass your first MCQ challenge.')
            ->assertSee('Attempt a Challenge')
            ->assertDontSee('Assignment Ready')
            ->getContent();

        foreach (['name="achievement_key"', 'name="mission_key"', 'name="icon"', 'name="badge_color"', 'name="criteria_type"', 'name="criteria_value"', 'name="target_type"', 'name="target_count"', 'name="sort_order"', 'name="is_active"'] as $field) {
            $this->assertStringNotContainsString($field, $html, $field.' is still on the page');
        }
        foreach (['Code Mark', 'Badge Color', 'Criteria Type', 'Criteria Value', 'Target Type', 'Sort Order', 'first_challenge_pass', 'daily_attempt_challenge'] as $label) {
            $this->assertStringNotContainsString($label, $html, $label.' is still on the page');
        }

        // Frequency offers daily and weekly only.
        $this->assertSame(1, preg_match('#<select id="mission-period-'.$mission->id.'"[^>]*>(.*?)</select>#s', $html, $select));
        $this->assertSame(2, substr_count($select[1], '<option'));
        $this->assertStringContainsString('value="daily"', $select[1]);
        $this->assertStringContainsString('value="weekly"', $select[1]);

        // No route or form creates an achievement or a mission.
        $this->assertFalse(Route::has('admin.gamification.achievements.store'));
        $this->assertFalse(Route::has('admin.gamification.missions.store'));
        $this->post('/admin/gamification/achievements', ['name' => 'Made up', 'xp_reward' => 10])->assertNotFound();
        $this->post('/admin/gamification/missions', ['title' => 'Made up', 'xp_reward' => 10])->assertNotFound();
        $this->assertSame(2, AchievementDefinition::count());
        $this->assertNotNull($retired->fresh());
    }

    public function test_achievements_cannot_be_edited(): void
    {
        // DataSensei Updates 9: achievements are fixed system records.
        $achievement = $this->achievement();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $this->assertFalse(Route::has('admin.gamification.achievements.update'));
        $this->put('/admin/gamification/achievements/'.$achievement->id, [
            'name' => 'Renamed', 'xp_reward' => 9999, 'description' => 'Changed.', 'criteria_value' => 50,
        ])->assertStatus(405);

        $html = $this->get(route('admin.gamification.index'))->assertOk()->getContent();
        $this->assertSame(1, preg_match('#<tr id="achievement-'.$achievement->id.'"[^>]*>(.*?)</tr>#s', $html, $row));
        $this->assertStringContainsString('First Challenge Pass', $row[1]);
        $this->assertStringContainsString('Pass your first MCQ challenge.', $row[1]);
        $this->assertStringContainsString('50', $row[1]);
        // Only the Remove form (hidden fields) is there: no field to type in.
        $this->assertSame(0, preg_match('#<input(?![^>]*type="hidden")#', $row[1]));
        $this->assertStringNotContainsString('<textarea', $row[1]);
        $this->assertStringNotContainsString('value="PUT"', $row[1]);

        $fresh = $achievement->fresh();
        $this->assertSame(['First Challenge Pass', 50, 'Pass your first MCQ challenge.', 1], [$fresh->name, (int) $fresh->xp_reward, $fresh->description, (int) $fresh->criteria_value]);
    }

    public function test_an_achievement_is_removed_only_while_nobody_has_earned_it(): void
    {
        $unearned = $this->achievement();
        $earned = $this->achievement(['achievement_key' => 'coding_starter', 'name' => 'Coding Starter']);
        $learner = $this->roleUser(User::ROLE_USER);
        UserAchievement::create(['user_id' => $learner->id, 'achievement_definition_id' => $earned->id, 'unlocked_at' => now()]);
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $html = $this->get(route('admin.gamification.index'))->assertOk()->getContent();
        $this->assertSame(1, preg_match('#id="achievement-'.$unearned->id.'".*?</tr>#s', $html, $unearnedItem));
        $this->assertSame(1, preg_match('#id="achievement-'.$earned->id.'".*?</tr>#s', $html, $earnedItem));
        $this->assertStringContainsString('value="DELETE"', $unearnedItem[0]);
        $this->assertStringContainsString('>Remove</button>', $unearnedItem[0]);
        $this->assertStringNotContainsString('value="DELETE"', $earnedItem[0]);
        $this->assertStringContainsString(AdminGamificationController::EARNED_ACHIEVEMENT_MESSAGE, $earnedItem[0]);
        $this->assertStringContainsString('1 student', $earnedItem[0]);

        $this->from(route('admin.gamification.index'))
            ->delete(route('admin.gamification.achievements.destroy', $earned))
            ->assertRedirect(route('admin.gamification.index').'#achievements')
            ->assertSessionHas('error', 'This Achievement cannot be removed because it has already been earned by one or more users.');
        $this->assertNotNull($earned->fresh());
        $this->assertSame(1, UserAchievement::where('achievement_definition_id', $earned->id)->count());

        $this->from(route('admin.gamification.index'))
            ->delete(route('admin.gamification.achievements.destroy', $unearned))
            ->assertRedirect(route('admin.gamification.index').'#achievements')
            ->assertSessionHas('success');
        $this->assertNull($unearned->fresh());
    }

    public function test_editing_a_mission_changes_name_exp_description_and_daily_or_weekly_only(): void
    {
        $mission = $this->mission();
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN));

        $this->from(route('admin.gamification.index'))
            ->put(route('admin.gamification.missions.update', $mission), [
                'form_key' => 'mission_'.$mission->id, 'title' => 'Weekly Challenge', 'xp_reward' => 90,
                'description' => 'Submit a challenge this week.', 'period_type' => 'monthly',
            ])
            ->assertSessionHasErrorsIn('mission_'.$mission->id, ['period_type']);
        $this->assertSame('daily', $mission->fresh()->period_type);

        $this->from(route('admin.gamification.index'))
            ->put(route('admin.gamification.missions.update', $mission), [
                'form_key' => 'mission_'.$mission->id, 'title' => 'Weekly Challenge', 'xp_reward' => 90,
                'description' => 'Submit a challenge this week.', 'period_type' => 'weekly',
                'mission_key' => 'hacked', 'target_type' => 'code_runs', 'target_count' => 50, 'sort_order' => 1, 'is_active' => 0,
            ])
            ->assertRedirect(route('admin.gamification.index').'#mission-'.$mission->id)
            ->assertSessionHas('success', 'Mission updated.');

        $fresh = $mission->fresh();
        $this->assertSame('Weekly Challenge', $fresh->title);
        $this->assertSame(90, $fresh->xp_reward);
        $this->assertSame('Submit a challenge this week.', $fresh->description);
        $this->assertSame('weekly', $fresh->period_type);
        $this->assertSame('daily_attempt_challenge', $fresh->mission_key);
        $this->assertSame('challenge_attempts', $fresh->target_type);
        $this->assertSame(1, $fresh->target_count);
        $this->assertTrue($fresh->is_active);
    }

    public function test_only_admins_manage_gamification(): void
    {
        $achievement = $this->achievement();
        $mission = $this->mission();
        $institution = Institution::create(['name' => 'Updates7 Institution', 'email' => 'u7-'.Str::lower(Str::random(6)).'@institution.test', 'status' => 'active']);

        foreach ([$this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]), $this->roleUser(User::ROLE_USER)] as $user) {
            $this->authenticateAs($user);
            $this->get(route('admin.gamification.index'))->assertForbidden();
            $this->delete(route('admin.gamification.achievements.destroy', $achievement))->assertForbidden();
            $this->put(route('admin.gamification.missions.update', $mission), ['title' => 'Hacked', 'xp_reward' => 1, 'period_type' => 'weekly'])->assertForbidden();
        }

        $this->assertSame('First Challenge Pass', $achievement->fresh()->name);
        $this->assertSame('Attempt a Challenge', $mission->fresh()->title);
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function achievement(array $overrides = []): AchievementDefinition
    {
        return AchievementDefinition::create(array_merge([
            'achievement_key' => 'first_challenge_pass', 'name' => 'First Challenge Pass', 'description' => 'Pass your first MCQ challenge.',
            'icon' => 'FCP', 'badge_color' => 'green', 'xp_reward' => 50, 'criteria_type' => 'challenge_passes', 'criteria_value' => 1,
            'is_active' => true, 'sort_order' => 20,
        ], $overrides));
    }

    private function mission(): MissionDefinition
    {
        return MissionDefinition::create([
            'mission_key' => 'daily_attempt_challenge', 'title' => 'Attempt a Challenge', 'description' => 'Submit one MCQ or coding challenge today.',
            'period_type' => 'daily', 'target_type' => 'challenge_attempts', 'target_count' => 1, 'xp_reward' => 35, 'is_active' => true, 'sort_order' => 30,
        ]);
    }
}
