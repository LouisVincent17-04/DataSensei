<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Module;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admins should not have to invent a content code to create a challenge.
 *
 * The first browser run of the new coding manager stalled on "The content code
 * field is required", a field whose placeholder made it look optional. Both
 * managers now accept a blank code and generate a unique one. The module
 * fan-out likewise sets an explicit code per module and level, because the
 * model's own derivation depends on the title alone and two same-titled
 * modules would have collided on the (content_code, version_code) index.
 */
class Updates3ContentCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::create([
            'name' => 'Code Admin',
            'email' => 'code-admin-'.uniqid().'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => 2,
            'status' => 'active',
        ]);
    }

    private function actingAsAdmin()
    {
        $admin = $this->admin();

        return $this->actingAs($admin)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($admin),
        ]);
    }

    private function categories(): array
    {
        $ids = [];
        foreach (['newbie', 'intermediate'] as $index => $slug) {
            $ids[] = ChallengeCategory::create([
                'name' => ucfirst($slug), 'slug' => $slug, 'target_audience' => 'all',
                'description' => '', 'order_index' => $index + 1,
            ])->id;
        }

        return $ids;
    }

    private function codingPayload(int $categoryId, string $title, string $code = ''): array
    {
        return [
            'challenge_category_id' => $categoryId,
            'content_code' => $code,
            'title' => $title,
            'time_limit_seconds' => 1800,
            'base_xp' => 100,
            'order_index' => 0,
            'is_active' => 0,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'questions' => [[
                'problem_description' => 'Print five.',
                'language' => 'python',
                'starter_code' => '',
                'reference_solution' => 'print(5)',
                'time_limit_seconds' => 600,
                'base_xp' => 100,
                'test_cases' => [['input' => '', 'expected_output' => '5', 'is_hidden' => 0]],
            ]],
        ];
    }

    public function test_a_coding_challenge_can_be_created_without_typing_a_content_code(): void
    {
        [$category] = $this->categories();

        $this->actingAsAdmin()
            ->post(route('admin.coding-challenges.store'), $this->codingPayload($category, 'Sum Two Numbers'))
            ->assertSessionHasNoErrors();

        $challenge = Challenge::where('title', 'Sum Two Numbers')->firstOrFail();

        $this->assertNotEmpty($challenge->content_code);
        $this->assertStringStartsWith('C'.$category.'-CODE-', $challenge->content_code);
    }

    public function test_two_same_titled_challenges_on_one_level_get_different_codes(): void
    {
        [$category] = $this->categories();

        $this->actingAsAdmin()->post(route('admin.coding-challenges.store'), $this->codingPayload($category, 'Same Title'))->assertSessionHasNoErrors();
        $this->actingAsAdmin()->post(route('admin.coding-challenges.store'), $this->codingPayload($category, 'Same Title'))->assertSessionHasNoErrors();

        $codes = Challenge::where('title', 'Same Title')->pluck('content_code');

        $this->assertCount(2, $codes);
        $this->assertCount(2, $codes->unique(), 'A generated code must never repeat.');
    }

    public function test_a_typed_code_is_still_honoured(): void
    {
        [$category] = $this->categories();

        $this->actingAsAdmin()
            ->post(route('admin.coding-challenges.store'), $this->codingPayload($category, 'Typed', 'my-own-code'))
            ->assertSessionHasNoErrors();

        $this->assertSame('MY-OWN-CODE', Challenge::where('title', 'Typed')->value('content_code'));
    }

    public function test_an_mcq_challenge_can_be_created_without_a_content_code(): void
    {
        [$category] = $this->categories();

        $this->actingAsAdmin()->post(route('admin.challenges.store'), [
            'challenge_category_id' => $category,
            'content_code' => '',
            'title' => 'Quiz Without Code',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 0,
            'is_active' => 0,
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'questions' => [[
                'question_text' => 'Pick one',
                'correct_option' => 0,
                'options' => [['option_text' => 'A'], ['option_text' => 'B']],
            ]],
        ])->assertSessionHasNoErrors();

        $this->assertStringStartsWith('C'.$category.'-MCQ-', Challenge::where('title', 'Quiz Without Code')->value('content_code'));
    }

    public function test_two_modules_with_the_same_title_fan_out_without_colliding(): void
    {
        $this->categories();

        $payload = [
            'title' => 'Introduction', 'description' => 'x', 'year_level' => 'Year 1',
            'xp_reward' => 100, 'is_boss' => 0, 'has_coding_exercises' => 1,
        ];

        $this->actingAsAdmin()->post(route('admin.modules.store'), $payload)->assertSessionHasNoErrors();
        $this->actingAsAdmin()->post(route('admin.modules.store'), $payload)->assertSessionHasNoErrors();

        $modules = Module::where('title', 'Introduction')->get();
        $this->assertCount(2, $modules);

        // 2 modules x 2 levels x (MCQ + coding) = 8 challenges, all distinct codes.
        $challenges = Challenge::whereIn('module_id', $modules->pluck('id'))->get();
        $this->assertCount(8, $challenges);
        $this->assertCount(8, $challenges->pluck('content_code')->unique());
        $this->assertTrue($challenges->every(fn ($c) => ! $c->is_active), 'Fanned-out challenges start unavailable.');
        $this->assertStringStartsWith('M'.$modules[0]->id.'-NEWBIE-', $challenges->firstWhere('module_id', $modules[0]->id)->content_code);
    }
}
