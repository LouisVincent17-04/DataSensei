<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, A1: the Module Manager over the public curriculum (App\Models\Module).
 * Creating a module fans out one locked MCQ challenge per challenge level (and
 * a coding one per level when the module has coding exercises); editing never
 * touches those challenges; deleting is refused once students have progress.
 */
class Updates3ModuleManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_is_admin_only(): void
    {
        $module = $this->makeModule('Seeded Module');

        $this->actingAsUser($this->makeUser(User::ROLE_ADMIN))
            ->get(route('admin.modules.index'))
            ->assertOk()
            ->assertSee('Seeded Module')
            ->assertSee(route('admin.modules.edit', $module), false)
            ->assertSee(route('admin.modules.lessons.index', $module), false);

        $this->actingAsUser($this->makeUser(User::ROLE_USER))
            ->get(route('admin.modules.index'))
            ->assertForbidden();

        $this->actingAsUser($this->makeUser(User::ROLE_USER))
            ->get(route('admin.modules.create'))
            ->assertForbidden();

        $this->actingAsUser($this->makeUser(User::ROLE_USER))
            ->post(route('admin.modules.store'), ['title' => 'Nope'])
            ->assertForbidden();
    }

    public function test_create_and_edit_pages_render(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $module = $this->makeModule('Editable Module');

        $this->actingAsUser($admin)->get(route('admin.modules.create'))->assertOk()->assertSee('Create module');
        $this->actingAsUser($admin)->get(route('admin.modules.edit', $module))->assertOk()->assertSee('Editable Module');
    }

    public function test_store_validates_input(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAsUser($admin)
            ->from(route('admin.modules.create'))
            ->post(route('admin.modules.store'), [
                'title' => '',
                'year_level' => 'Year 9',
                'xp_reward' => 99999,
            ])
            ->assertRedirect(route('admin.modules.create'))
            ->assertSessionHasErrors(['title', 'year_level', 'xp_reward']);

        $this->assertSame(0, Module::count());
        $this->assertSame(0, Challenge::count());
    }

    public function test_store_fans_out_one_locked_mcq_challenge_per_level(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $categories = $this->makeCategories(3);
        $this->makeModule('Existing One', 1);

        // A challenge already on the first level: the new one goes after it.
        Challenge::create([
            'challenge_category_id' => $categories[0]->id,
            'title' => 'Already there',
            'description' => 'Existing challenge.',
            'order_index' => 7,
            'is_coding_challenge' => 0,
            'is_active' => 1,
        ]);

        $response = $this->actingAsUser($admin)->post(route('admin.modules.store'), [
            'title' => 'Fanned Out Module',
            'description' => 'Description here.',
            'year_level' => 'Year 2',
            'xp_reward' => 250,
            'is_boss' => '0',
            'has_coding_exercises' => '0',
        ]);

        $response->assertRedirect(route('admin.modules.index'))->assertSessionHas('success');
        $this->assertStringContainsString('3 challenges', session('success'));
        $this->assertStringContainsString('unavailable', session('success'));

        $module = Module::where('title', 'Fanned Out Module')->firstOrFail();
        $this->assertSame(2, $module->order_index);
        $this->assertSame('Year 2', $module->year_level);
        $this->assertSame(250, $module->xp_reward);
        $this->assertFalse($module->has_coding_exercises);

        $fanned = Challenge::where('module_id', $module->id)->orderBy('challenge_category_id')->get();
        $this->assertCount(3, $fanned);
        $this->assertSame(
            $categories->pluck('id')->sort()->values()->all(),
            $fanned->pluck('challenge_category_id')->sort()->values()->all()
        );

        foreach ($fanned as $challenge) {
            $this->assertFalse($challenge->is_active, 'Fanned-out challenges must be locked.');
            $this->assertFalse($challenge->is_coding_challenge);
            $this->assertSame('Fanned Out Module', $challenge->title);
            $this->assertSame('Practice challenge for Fanned Out Module. Add questions and publish when ready.', $challenge->description);
            $this->assertSame(Challenge::VISIBILITY_PLATFORM, $challenge->visibility);
            $this->assertNull($challenge->created_by);
            $this->assertNotSame('', (string) $challenge->content_code);
            $this->assertSame(1, $challenge->version_no);
        }

        $onFirstLevel = $fanned->firstWhere('challenge_category_id', $categories[0]->id);
        $this->assertSame(8, $onFirstLevel->order_index);
        $this->assertSame(1, $fanned->firstWhere('challenge_category_id', $categories[1]->id)->order_index);

        $this->assertSame(4, Challenge::count());
    }

    public function test_store_with_coding_exercises_also_fans_out_a_coding_challenge_per_level(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $this->makeCategories(3);

        $this->actingAsUser($admin)->post(route('admin.modules.store'), [
            'title' => 'Coding Module',
            'description' => '',
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'has_coding_exercises' => '1',
        ])->assertRedirect(route('admin.modules.index'));

        $this->assertStringContainsString('6 challenges', session('success'));

        $module = Module::where('title', 'Coding Module')->firstOrFail();
        $this->assertTrue($module->has_coding_exercises);

        $this->assertSame(3, Challenge::where('module_id', $module->id)->where('is_coding_challenge', 0)->where('is_active', 0)->count());
        $coding = Challenge::where('module_id', $module->id)->where('is_coding_challenge', 1)->get();
        $this->assertCount(3, $coding);
        foreach ($coding as $challenge) {
            $this->assertFalse($challenge->is_active);
            $this->assertSame('Coding Module — Coding', $challenge->title);
        }
        $this->assertSame(6, Challenge::count());
    }

    public function test_update_changes_the_module_but_creates_no_challenges(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $this->makeCategories(2);

        $this->actingAsUser($admin)->post(route('admin.modules.store'), [
            'title' => 'Before',
            'description' => 'Before description',
            'year_level' => 'Year 1',
            'xp_reward' => 100,
        ]);

        $module = Module::where('title', 'Before')->firstOrFail();
        $this->assertSame(2, Challenge::count());
        $before = Challenge::orderBy('id')->get()->map(fn ($c) => $c->only(['id', 'title', 'description', 'is_active', 'module_id']))->all();

        $this->actingAsUser($admin)->put(route('admin.modules.update', $module), [
            'title' => 'After',
            'description' => 'After description',
            'year_level' => 'Year 3',
            'xp_reward' => 400,
            'is_boss' => '1',
            'has_coding_exercises' => '1',
        ])->assertRedirect(route('admin.modules.index'))->assertSessionHas('success');

        $module->refresh();
        $this->assertSame('After', $module->title);
        $this->assertSame('After description', $module->description);
        $this->assertSame('Year 3', $module->year_level);
        $this->assertSame(400, $module->xp_reward);
        $this->assertTrue($module->is_boss);
        $this->assertTrue($module->has_coding_exercises);

        $this->assertSame(2, Challenge::count(), 'Updating a module must not fan out challenges.');
        $after = Challenge::orderBy('id')->get()->map(fn ($c) => $c->only(['id', 'title', 'description', 'is_active', 'module_id']))->all();
        $this->assertEquals($before, $after, 'Editing a module must not touch its challenges.');
    }

    public function test_reorder_rewrites_order_index(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $a = $this->makeModule('A', 1);
        $b = $this->makeModule('B', 2);
        $c = $this->makeModule('C', 3);

        $this->actingAsUser($admin)
            ->post(route('admin.modules.reorder'), ['order' => [$c->id, $a->id, $b->id]])
            ->assertRedirect(route('admin.modules.index'));

        $this->assertSame(1, $c->fresh()->order_index);
        $this->assertSame(2, $a->fresh()->order_index);
        $this->assertSame(3, $b->fresh()->order_index);

        $this->actingAsUser($admin)
            ->post(route('admin.modules.reorder'), ['order' => [999999]])
            ->assertSessionHasErrors('order.0');
    }

    public function test_destroy_is_refused_when_students_have_progress(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $student = $this->makeUser(User::ROLE_USER);
        $module = $this->makeModule('In Progress');
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => 'L1', 'content' => '<p>x</p>', 'order_index' => 1]);

        DB::table('lesson_user')->insert([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'is_completed' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAsUser($admin)
            ->delete(route('admin.modules.destroy', $module))
            ->assertRedirect(route('admin.modules.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('modules', ['id' => $module->id]);
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]);
    }

    public function test_destroy_removes_an_untouched_module_and_keeps_its_challenges(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $this->makeCategories(2);

        $this->actingAsUser($admin)->post(route('admin.modules.store'), [
            'title' => 'Disposable',
            'description' => '',
            'year_level' => 'Year 1',
            'xp_reward' => 100,
        ]);
        $module = Module::where('title', 'Disposable')->firstOrFail();
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => 'L1', 'content' => '<p>x</p>', 'order_index' => 1]);

        // Unlocked but not completed: not progress.
        $student = $this->makeUser(User::ROLE_USER);
        DB::table('module_user')->insert([
            'user_id' => $student->id,
            'module_id' => $module->id,
            'is_unlocked' => 1,
            'is_completed' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAsUser($admin)
            ->delete(route('admin.modules.destroy', $module))
            ->assertRedirect(route('admin.modules.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('modules', ['id' => $module->id]);
        $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
        $this->assertSame(2, Challenge::count());
        $this->assertSame(0, Challenge::whereNotNull('module_id')->count());
    }

    // ── Fixtures ─────────────────────────────────────────────────────

    private function makeCategories(int $count)
    {
        $categories = collect();
        for ($i = 1; $i <= $count; $i++) {
            $categories->push(ChallengeCategory::create([
                'name' => 'Level '.$i,
                'slug' => 'level-'.$i.'-'.Str::lower(Str::random(5)),
                'target_audience' => 'Year '.$i,
                'description' => 'Level '.$i.' challenges.',
                'order_index' => $i,
            ]));
        }

        return $categories;
    }

    private function makeModule(string $title, int $order = 1): Module
    {
        return Module::create([
            'title' => $title,
            'description' => 'Description of '.$title,
            'order_index' => $order,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'is_boss' => false,
        ]);
    }

    private function makeUser(int $role): User
    {
        return User::create([
            'name' => 'Updates3 User',
            'email' => 'updates3-'.Str::lower(Str::random(10)).'@example.test',
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
