<?php

namespace Tests\Feature\Regression;

use App\Models\AchievementDefinition;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Student > Achievements returned a 500 on every visit: the controller
 * rendered student.gamification.achievements and that template did not exist.
 * It is linked from the student sidebar, so it was one click away from every
 * student page.
 *
 * The suite had 1,156 passing tests at the time and none of them caught it,
 * because SidebarRoutesTest checks that the route NAME resolves — which it
 * did. Nothing rendered the page. This test closes that gap for every
 * controller in the application rather than for this one route.
 */
class AuditControllerViewsExistTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_view_a_controller_renders_exists(): void
    {
        $missing = [];
        $checked = [];
        $references = 0;

        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure' || ! str_contains($action, '@')) {
                continue;
            }

            [$class, $method] = explode('@', $action, 2);

            $this->assertTrue(class_exists($class), $route->uri().' points at a missing controller: '.$class);
            $this->assertTrue(method_exists($class, $method), $route->uri().' points at a missing action: '.$action);

            if (isset($checked[$action])) {
                continue;
            }

            $checked[$action] = true;

            $reflection = new \ReflectionMethod($class, $method);
            $body = implode('', array_slice(
                file($reflection->getFileName()),
                $reflection->getStartLine() - 1,
                $reflection->getEndLine() - $reflection->getStartLine() + 1
            ));

            if (! preg_match_all('/view\(\s*[\'"]([a-zA-Z0-9_.\-]+)[\'"]/', $body, $matches)) {
                continue;
            }

            foreach (array_unique($matches[1]) as $view) {
                $references++;

                if (! View::exists($view)) {
                    $missing[] = $view.'  rendered by '.$action.'  ('.$route->uri().')';
                }
            }
        }

        $this->assertGreaterThan(50, $references, 'The sweep found suspiciously few view references.');
        $this->assertSame([], $missing, "Controllers render views that do not exist:\n".implode("\n", $missing));
    }

    public function test_the_student_achievements_page_renders(): void
    {
        if (! Schema::hasTable('achievement_definitions')) {
            $this->markTestSkipped('The achievements tables are not part of this schema.');
        }

        AchievementDefinition::create([
            'achievement_key' => 'audit_first_run',
            'name' => 'Audit First Run',
            'description' => 'Run your first program.',
            'icon' => 'ACH',
            'badge_color' => 'blue',
            'xp_reward' => 50,
            'criteria_type' => 'coding_submissions',
            'criteria_value' => 1,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $student = User::create([
            'name' => 'Achievements Audit',
            'email' => 'achievements-audit-'.Str::lower(Str::random(8)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        $this->actingAs($student)
            ->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student)])
            ->get(route('student.achievements.index'))
            ->assertOk()
            ->assertSee('Audit First Run');
    }
}
