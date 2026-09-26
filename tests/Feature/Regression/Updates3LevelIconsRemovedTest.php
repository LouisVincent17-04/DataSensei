<?php

namespace Tests\Feature\Regression;

use App\Models\ChallengeCategory;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, B0: the level cards on /challenges and /challenges/coding no
 * longer carry the per-level icon. The category names, audience and
 * descriptions still render; only the icon box and its SVG are gone.
 */
class Updates3LevelIconsRemovedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Distinctive path data from resources/views/student/partials/challenge-category-icon.blade.php.
     * The partial itself is kept for other pages; these pages must not include it.
     */
    private const ICON_PATH_MARKERS = [
        'M12 22V12m0 0C12 7 7 5 3 6c0 4 2 8 9 6zm0 0c0-5 5-7 9-6-1 4-4 7-9 6z', // newbie
        'M12 14l9-5-9-5-9 5 9 5z', // university-student
        'M4 5v7c0 2 3.6 3 8 3s8-1 8-3V5M4 12v7c0 2 3.6 3 8 3s8-1 8-3v-7', // intermediate
        'M8.6 13.5l6.8 4M15.4 6.5l-6.8 4', // advanced
        'M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18', // professional
        'M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z', // default
    ];

    public function test_icon_partial_still_exists_and_carries_the_markers_this_test_asserts_against(): void
    {
        $partial = file_get_contents(resource_path('views/student/partials/challenge-category-icon.blade.php'));

        foreach (self::ICON_PATH_MARKERS as $marker) {
            $this->assertStringContainsString($marker, $partial);
        }
    }

    public function test_mcq_challenge_index_renders_levels_without_icons(): void
    {
        $this->makeLevels();

        $response = $this->actingAsStudent()->get(route('challenges'))->assertOk();

        $this->assertLevelsWithoutIcons($response->getContent());
    }

    public function test_coding_challenge_index_renders_levels_without_icons(): void
    {
        $this->makeLevels();

        $response = $this->actingAsStudent()->get(route('challenges.coding'))->assertOk();

        $this->assertLevelsWithoutIcons($response->getContent());
    }

    private function assertLevelsWithoutIcons(string $html): void
    {
        foreach (['Newbie', 'Intermediate', 'Advanced', 'Professional'] as $name) {
            $this->assertStringContainsString($name, $html);
        }
        $this->assertStringContainsString('Beginners', $html);
        $this->assertStringContainsString('page-challenges-card-title', $html);

        $this->assertStringNotContainsString('page-challenges-card-icon', $html);
        $this->assertStringNotContainsString('challenge-category-icon', $html);
        foreach (self::ICON_PATH_MARKERS as $marker) {
            $this->assertStringNotContainsString($marker, $html);
        }
    }

    private function makeLevels(): void
    {
        foreach (['newbie' => 'Newbie', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'professional' => 'Professional'] as $slug => $name) {
            ChallengeCategory::create([
                'name' => $name,
                'slug' => $slug,
                'target_audience' => $slug === 'newbie' ? 'Beginners' : 'Learners',
                'description' => $name . ' challenges.',
                'order_index' => count(ChallengeCategory::pluck('id')->all()) + 1,
            ]);
        }
    }

    private function actingAsStudent()
    {
        $student = User::create([
            'name' => 'Updates3 Student',
            'email' => 'updates3-' . Str::lower(Str::random(10)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        return $this->actingAs($student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student),
        ]);
    }
}
