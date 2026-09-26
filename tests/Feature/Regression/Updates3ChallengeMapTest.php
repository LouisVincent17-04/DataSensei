<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, B1: the Challenge Map manager (/admin/challenge-maps). Every
 * level is editable inline (never its slug), and the MCQ and coding challenges
 * of a level can be reordered independently of each other.
 */
class Updates3ChallengeMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_every_level_with_its_mcq_and_coding_challenges(): void
    {
        $newbie = $this->makeCategory('Newbie', 'newbie', 1);
        $advanced = $this->makeCategory('Advanced', 'advanced', 2);

        $mcq = $this->makeChallenge($newbie, 'Python Basics MCQ', false, 1);
        $coding = $this->makeChallenge($newbie, 'Python Basics Coding', true, 1);
        $otherMcq = $this->makeChallenge($advanced, 'Advanced Pandas', false, 1, false);

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->get(route('admin.challenge-maps.index'))
            ->assertOk()
            ->assertSee('Challenge Maps')
            ->assertSee('Newbie')
            ->assertSee('Advanced')
            ->assertSee('Python Basics MCQ')
            ->assertSee('Python Basics Coding')
            ->assertSee('Advanced Pandas')
            ->assertSee('Available')
            ->assertSee('Unavailable')
            ->assertSee(route('admin.challenges.edit', $mcq), false)
            ->assertSee(route('admin.coding-challenges.edit', $coding), false)
            ->assertSee(route('admin.challenges.status', $mcq), false)
            ->assertSee(route('admin.coding-challenges.status', $coding), false)
            ->assertSee(route('admin.challenges.edit', $otherMcq), false)
            ->assertSee(route('admin.challenge-maps.update', $newbie), false)
            ->assertSee(route('admin.challenge-maps.reorder', $newbie), false);
    }

    public function test_students_cannot_reach_the_manager(): void
    {
        $category = $this->makeCategory('Newbie', 'newbie', 1);
        $this->get(route('admin.challenge-maps.index'))->assertRedirect(route('login'));

        $user = $this->makeUser(User::ROLE_USER);

        $this->actingAsUser($user)->get(route('admin.challenge-maps.index'))->assertForbidden();
        $this->actingAsUser($user)->put(route('admin.challenge-maps.update', $category), ['name' => 'Hacked', 'order_index' => 5])->assertForbidden();
        $this->actingAsUser($user)->post(route('admin.challenge-maps.reorder', $category), ['type' => 'mcq', 'order' => [1]])->assertForbidden();

        $this->assertSame('Newbie', $category->fresh()->name);
    }

    public function test_update_changes_name_description_audience_and_order_but_never_the_slug(): void
    {
        $category = $this->makeCategory('Newbie', 'newbie', 1);

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->put(route('admin.challenge-maps.update', $category), [
                'name' => 'Beginner',
                'description' => 'Start here.',
                'target_audience' => 'First-year students',
                'order_index' => 4,
                'slug' => 'beginner-hacked',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $category->refresh();
        $this->assertSame('Beginner', $category->name);
        $this->assertSame('Start here.', $category->description);
        $this->assertSame('First-year students', $category->target_audience);
        $this->assertSame(4, (int) $category->order_index);
        $this->assertSame('newbie', $category->slug, 'The slug is part of learner URLs and must never change.');
    }

    public function test_update_validates_input(): void
    {
        $category = $this->makeCategory('Newbie', 'newbie', 1);
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->from(route('admin.challenge-maps.index'))
            ->put(route('admin.challenge-maps.update', $category), [
                'name' => '',
                'order_index' => 1001,
            ])
            ->assertRedirect(route('admin.challenge-maps.index'))
            ->assertSessionHasErrors(['name', 'order_index']);

        $this->actingAsUser($admin)
            ->from(route('admin.challenge-maps.index'))
            ->put(route('admin.challenge-maps.update', $category), [
                'name' => str_repeat('x', 190),
                'order_index' => -1,
            ])
            ->assertSessionHasErrors(['name', 'order_index']);

        $category->refresh();
        $this->assertSame('Newbie', $category->name);
        $this->assertSame(1, (int) $category->order_index);
    }

    public function test_reorder_rewrites_order_index_for_the_requested_type_only(): void
    {
        $category = $this->makeCategory('Newbie', 'newbie', 1);
        $other = $this->makeCategory('Advanced', 'advanced', 2);

        $mcqA = $this->makeChallenge($category, 'MCQ A', false, 1);
        $mcqB = $this->makeChallenge($category, 'MCQ B', false, 2);
        $mcqC = $this->makeChallenge($category, 'MCQ C', false, 3);
        $codingA = $this->makeChallenge($category, 'Coding A', true, 1);
        $codingB = $this->makeChallenge($category, 'Coding B', true, 2);
        $foreign = $this->makeChallenge($other, 'Foreign MCQ', false, 7);

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->post(route('admin.challenge-maps.reorder', $category), [
                'type' => 'mcq',
                'order' => [$mcqC->id, $mcqA->id, $foreign->id, $codingA->id, $mcqB->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(1, (int) $mcqC->fresh()->order_index);
        $this->assertSame(2, (int) $mcqA->fresh()->order_index);
        $this->assertSame(3, (int) $mcqB->fresh()->order_index);

        // Coding challenges of the same level and challenges of other levels are untouched.
        $this->assertSame(1, (int) $codingA->fresh()->order_index);
        $this->assertSame(2, (int) $codingB->fresh()->order_index);
        $this->assertSame(7, (int) $foreign->fresh()->order_index);
        $this->assertSame($other->id, (int) $foreign->fresh()->challenge_category_id);

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->post(route('admin.challenge-maps.reorder', $category), [
                'type' => 'coding',
                'order' => [$codingB->id, $codingA->id],
            ])
            ->assertRedirect();

        $this->assertSame(1, (int) $codingB->fresh()->order_index);
        $this->assertSame(2, (int) $codingA->fresh()->order_index);
        $this->assertSame(1, (int) $mcqC->fresh()->order_index);
    }

    public function test_reorder_validates_type_and_order(): void
    {
        $category = $this->makeCategory('Newbie', 'newbie', 1);
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->from(route('admin.challenge-maps.index'))
            ->post(route('admin.challenge-maps.reorder', $category), ['type' => 'essay', 'order' => []])
            ->assertRedirect(route('admin.challenge-maps.index'))
            ->assertSessionHasErrors(['type', 'order']);

        $this->actingAsUser($admin)
            ->from(route('admin.challenge-maps.index'))
            ->post(route('admin.challenge-maps.reorder', $category), ['type' => 'mcq', 'order' => ['abc']])
            ->assertSessionHasErrors(['order.0']);
    }

    private function makeCategory(string $name, string $slug, int $order): ChallengeCategory
    {
        return ChallengeCategory::create([
            'name' => $name,
            'slug' => $slug,
            'target_audience' => 'Learners',
            'description' => $name . ' challenges.',
            'order_index' => $order,
        ]);
    }

    private function makeChallenge(ChallengeCategory $category, string $title, bool $coding, int $order, bool $active = true): Challenge
    {
        return Challenge::create([
            'challenge_category_id' => $category->id,
            'title' => $title,
            'description' => $title . ' description',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => $order,
            'is_coding_challenge' => $coding,
            'version_no' => 1,
            'is_active' => $active,
        ]);
    }

    private function makeUser(int $role): User
    {
        return User::create([
            'name' => 'Updates3 User',
            'email' => 'updates3-' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function actingAsUser(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }
}
