<?php

namespace Tests\Feature\Regression;

use App\Http\Middleware\EnforceIdleSessionTimeout;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * DataSensei Updates 9, tasks 3, 4 and 8: every role is signed out after
 * exactly one hour without activity, the expired screen always reaches the
 * sign-in page, signing in again works, and no 419 page is left behind.
 */
class Updates9SessionTimeoutTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{0: int, 1: string}> */
    public static function roles(): array
    {
        return [
            'public user' => [User::ROLE_USER, 'studentDashboard'],
            'instructor' => [User::ROLE_INSTRUCTOR, 'instructor.dashboard'],
            'institution admin' => [User::ROLE_INSTITUTION_ADMIN, 'institution-admin.dashboard'],
            'admin' => [User::ROLE_ADMIN, 'admin.dashboard'],
            'super admin' => [User::ROLE_SUPERADMIN, 'superadmin.dashboard'],
        ];
    }

    public function test_the_timeout_is_exactly_sixty_minutes_whatever_the_env_says(): void
    {
        $this->assertSame(60, config('session.idle_timeout'));
        $this->assertSame(65, config('session.lifetime'));

        $previous = getenv('SESSION_IDLE_TIMEOUT');
        putenv('SESSION_IDLE_TIMEOUT=240');
        putenv('SESSION_LIFETIME=240');
        $_ENV['SESSION_IDLE_TIMEOUT'] = $_SERVER['SESSION_IDLE_TIMEOUT'] = '240';
        $_ENV['SESSION_LIFETIME'] = $_SERVER['SESSION_LIFETIME'] = '240';

        try {
            $config = require config_path('session.php');
            $this->assertSame(60, $config['idle_timeout']);
            $this->assertSame(65, $config['lifetime']);
        } finally {
            putenv('SESSION_IDLE_TIMEOUT'.($previous === false ? '' : '='.$previous));
            putenv('SESSION_LIFETIME');
            unset($_ENV['SESSION_IDLE_TIMEOUT'], $_SERVER['SESSION_IDLE_TIMEOUT'], $_ENV['SESSION_LIFETIME'], $_SERVER['SESSION_LIFETIME']);
        }
    }

    #[DataProvider('roles')]
    public function test_every_role_page_counts_down_one_hour_and_redirects_itself(int $role, string $dashboard): void
    {
        $html = $this->authenticateAs($this->userWithRole($role))
            ->get(route($dashboard))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('const timeoutMs = 60 * 60 * 1000;', $html);
        $this->assertStringContainsString('signs you out after 60 minutes without activity', $html);
        // The ending screen always moves on: a sign-in link and a second try.
        $this->assertStringContainsString("window.location.replace(target);", $html);
        $this->assertStringContainsString('Go to the sign-in page', $html);
        $this->assertStringContainsString('window.location.assign(target)', $html);
    }

    #[DataProvider('roles')]
    public function test_every_role_is_signed_out_after_sixty_minutes_and_can_sign_in_again(int $role, string $dashboard): void
    {
        $user = $this->userWithRole($role);

        // 59 minutes: still signed in.
        $this->authenticateAs($user)
            ->withSession([EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY => now()->subMinutes(59)->timestamp])
            ->get(route($dashboard))
            ->assertOk();
        $this->assertAuthenticatedAs($user);

        // 60 minutes: signed out, sent to the sign-in page with the reason.
        $this->withSession([EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY => now()->subMinutes(60)->timestamp])
            ->get(route($dashboard))
            ->assertRedirect(route('login', ['expired' => 1]))
            ->assertSessionHas('status', 'You were signed out after 60 minutes of inactivity.');
        $this->assertGuest();

        // The sign-in page loads and signing in again goes straight to the dashboard.
        $this->get(route('login', ['expired' => 1]))
            ->assertOk()
            ->assertSee('You were signed out after 60 minutes of inactivity.');
        $token = session()->token();

        $this->post('/login', ['_token' => $token, 'email' => $user->email, 'password' => 'Password!2026'])
            ->assertRedirect(route($dashboard));
        $this->assertAuthenticatedAs($user);
    }

    public function test_the_heartbeat_dates_activity_from_the_last_real_input_and_never_extends_it(): void
    {
        $user = $this->userWithRole(User::ROLE_USER);
        $key = EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY;
        $this->authenticateAs($user)->withSession([$key => now()->subMinutes(10)->timestamp]);

        // Input 40 seconds ago, reported half a minute later: dated then, not now.
        $this->postJson(route('session.activity'), ['idle_ms' => 40_000])->assertNoContent();
        $this->assertEqualsWithDelta(now()->subSeconds(40)->timestamp, session($key), 1);

        // It cannot move the activity back past what the server already saw ...
        $this->postJson(route('session.activity'), ['idle_ms' => 10 ** 12])->assertNoContent();
        $this->assertEqualsWithDelta(now()->subSeconds(40)->timestamp, session($key), 1);

        // ... or forward past now.
        $this->postJson(route('session.activity'), ['idle_ms' => -60_000])->assertNoContent();
        $this->assertEqualsWithDelta(now()->timestamp, session($key), 1);

        // Without a report, a request is activity now (older pages keep working).
        $this->withSession([$key => now()->subMinutes(10)->timestamp])
            ->postJson(route('session.activity'), ['idle_ms' => 'soon'])
            ->assertNoContent();
        $this->assertEqualsWithDelta(now()->timestamp, session($key), 1);

        // The page and the server end together: 59 minutes of no input
        // reported by a heartbeat leaves one minute, not a fresh hour ...
        $this->withSession([$key => now()->subMinutes(59)->timestamp])
            ->postJson(route('session.activity'), ['idle_ms' => 59 * 60 * 1000])
            ->assertNoContent();
        $this->assertEqualsWithDelta(now()->subMinutes(59)->timestamp, session($key), 1);

        // ... and at 60 minutes the heartbeat itself is turned away.
        $this->withSession([$key => now()->subMinutes(60)->timestamp])
            ->postJson(route('session.activity'), ['idle_ms' => 0])
            ->assertStatus(401)
            ->assertJson(['session_expired' => true]);
        $this->assertGuest();
    }

    public function test_the_page_only_sends_heartbeats_after_real_input_and_retries_a_failed_sign_out(): void
    {
        $partial = (string) file_get_contents(resource_path('views/partials/session-timeout.blade.php'));

        $this->assertStringContainsString('let activityDirty = false;', $partial);
        $this->assertStringContainsString('body: JSON.stringify({ idle_ms: Math.max(0, Date.now() - effectiveLastActivity()) })', $partial);
        $this->assertMatchesRegularExpression('/if \(!signOutSettled\(response\)\) \{[\s\S]*?setTimeout\(resolve, 3000\)[\s\S]*?await signOutRequest\(\);/', $partial);
    }

    public function test_the_expired_screens_sign_out_request_never_becomes_a_419(): void
    {
        // The session is already gone (another tab signed out, or it expired):
        // the page's sign-out call gets a plain "signed out" answer.
        $this->app['env'] = 'local';
        $this->assertFalse($this->app->runningUnitTests());

        $this->withSession(['_token' => 'fresh-token'])
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest', 'X-CSRF-TOKEN' => 'stale-token'])
            ->post(route('logout'))
            ->assertStatus(401)
            ->assertJson(['session_expired' => true, 'login_url' => route('login', ['expired' => 1])]);
    }

    public function test_a_form_left_open_past_the_session_lands_on_the_sign_in_page(): void
    {
        $this->app['env'] = 'local';

        $this->withSession(['_token' => 'fresh-token'])
            ->patch(route('profile.update'), ['_token' => 'stale-token', 'name' => 'Changed'])
            ->assertRedirect(route('login', ['expired' => 1]))
            ->assertSessionHas('status');
    }

    public function test_remember_me_is_gone_and_an_old_remember_cookie_does_not_sign_anyone_in(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('name="remember"', false)
            ->assertDontSee('Remember me');

        $user = $this->userWithRole(User::ROLE_USER);
        $tokenBefore = $user->getRememberToken();
        $this->post('/login', ['email' => $user->email, 'password' => 'Password!2026', 'remember' => '1'])
            ->assertRedirect(route('studentDashboard'))
            ->assertCookieMissing(Auth::guard('web')->getRecallerName());
        $this->assertSame($tokenBefore, $user->fresh()->getRememberToken(), 'No new remember token is issued.');
        Auth::guard('web')->logout();
        Auth::forgetGuards();

        // A cookie saved before the update.
        $user->forceFill(['remember_token' => Str::random(60)])->save();
        $recaller = $user->id.'|'.$user->getRememberToken().'|'.$user->getAuthPassword();
        $this->flushSession();
        $this->withCookie(Auth::guard('web')->getRecallerName(), $recaller)
            ->get(route('studentDashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'You were signed out after 60 minutes of inactivity.']);
        $this->assertGuest();
        $this->assertNotSame(explode('|', $recaller)[1], $user->fresh()->getRememberToken(), 'The old token was rotated.');
    }

    private function userWithRole(int $role): User
    {
        $attributes = ['password' => bcrypt('Password!2026')];

        if (in_array($role, [User::ROLE_INSTRUCTOR, User::ROLE_INSTITUTION_ADMIN], true)) {
            $attributes['institution_id'] = Institution::create([
                'name' => 'Updates9 Institution '.Str::random(4),
                'email' => 'u9-'.Str::lower(Str::random(8)).'@institution.test',
                'status' => 'active',
            ])->id;
        }

        return $this->roleUser($role, $attributes);
    }
}
