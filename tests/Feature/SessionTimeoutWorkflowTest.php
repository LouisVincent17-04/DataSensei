<?php

namespace Tests\Feature;

use App\Http\Middleware\EnforceIdleSessionTimeout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionTimeoutWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_browser_session_redirects_cleanly_to_sign_in(): void
    {
        config(['session.idle_timeout' => 5]);
        $user = $this->roleUser(User::ROLE_USER);

        $response = $this->authenticateAs($user)
            ->withSession([EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY => now()->subMinutes(6)->timestamp])
            ->get(route('profile'));

        $response
            ->assertRedirect(route('login', ['expired' => 1]))
            ->assertSessionHas('status', 'You were signed out after 5 minutes of inactivity.');
        $this->assertGuest();
    }

    public function test_expired_json_session_returns_the_login_destination(): void
    {
        config(['session.idle_timeout' => 5]);
        $user = $this->roleUser(User::ROLE_USER);

        $response = $this->authenticateAs($user)
            ->withSession([EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY => now()->subMinutes(6)->timestamp])
            ->getJson(route('profile'));

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'You were signed out after 5 minutes of inactivity.',
                'session_expired' => true,
                'login_url' => route('login', ['expired' => 1]),
            ]);
        $this->assertGuest();
    }

    public function test_active_browser_session_continues_normally(): void
    {
        config(['session.idle_timeout' => 5]);
        $user = $this->roleUser(User::ROLE_USER);

        $this->authenticateAs($user)
            ->withSession([EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY => now()->subMinute()->timestamp])
            ->get(route('profile'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);
        $this->assertGreaterThan(
            now()->subSeconds(5)->timestamp,
            (int) session(EnforceIdleSessionTimeout::LAST_ACTIVITY_KEY)
        );
    }
}
