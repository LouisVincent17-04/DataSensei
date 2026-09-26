<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Signing in after a session expired failed with "We could not submit the
 * form. Please try again." and no way forward.
 *
 * The sign-in page fetches a fresh CSRF token before posting. That fetch was
 * treated as a gate: if it failed - and on "php artisan serve", which handles
 * one request at a time, another tab's request is enough to make it time out -
 * the page refused to submit at all. Right after a session expires every open
 * tab tends to fire at once, which is exactly when it was hit.
 *
 * The refresh is an optimisation now, and a stale token is answered with a
 * fresh sign-in page rather than Laravel's bare "Page Expired".
 *
 * Note on what is NOT covered here: Laravel disables CSRF verification inside
 * the test runner, so a stale token cannot be made to fail from PHPUnit. That
 * the token is still enforced was checked against a running server instead -
 * a POST to /login carrying a wrong token left the browser a guest. These
 * tests cover the handler that shapes the 419, which is the part that changed.
 */
class AuditExpiredSessionLoginTest extends TestCase
{
    use RefreshDatabase;

    private function learner(): User
    {
        return User::create([
            'name' => 'Expiry Login',
            'email' => 'expiry-login-'.Str::lower(Str::random(8)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    /**
     * CSRF verification is switched off inside the test runner, so a stale
     * token cannot be made to fail here. This stands in for it: a route that
     * raises the same 419, carrying the name the handler keys on, so the
     * behaviour being asserted is the real one.
     */
    private function registerExpiringRoute(): void
    {
        Route::middleware('web')
            ->post('/__expired-token-probe', function () {
                abort(419);
            })
            ->name('login');
    }

    public function test_an_expired_token_returns_a_usable_page_not_page_expired(): void
    {
        $this->registerExpiringRoute();

        $response = $this->from('/login')->post('/__expired-token-probe', [
            'email' => 'someone@example.test',
            'password' => 'Secret!2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'session expired',
            (string) session('errors')->first('email'),
            'The person must be told what happened, in words.'
        );
    }

    /**
     * The page retries once by itself after a bounce, restoring what was
     * typed from this tab's sessionStorage. It needs to be told the load is a
     * bounce, and it must never keep a password on an ordinary visit.
     */
    public function test_a_bounce_is_flagged_so_the_page_can_retry_by_itself(): void
    {
        $this->registerExpiringRoute();

        $response = $this->from('/login')->post('/__expired-token-probe', [
            'email' => 'someone@example.test',
            'password' => 'Secret!2026',
        ]);

        $response->assertSessionHas('session_expired_retry', true);
    }

    public function test_the_sign_in_page_restores_and_retries_but_never_loops(): void
    {
        $script = file_get_contents(resource_path('views/auth/login.blade.php'));

        $this->assertStringContainsString("session('session_expired_retry'", $script);
        $this->assertStringContainsString('window.sessionStorage', $script, 'Per-tab storage only; never localStorage.');
        $this->assertStringNotContainsString('localStorage.setItem', $script, 'A password must never be kept across tabs or sessions.');
        $this->assertStringContainsString('if (!stash.retried && stash.password)', $script, 'Exactly one automatic retry.');
        $this->assertStringContainsString('clearStash();', $script);
        $this->assertStringContainsString('STASH_TTL_MS = 2 * 60 * 1000', $script, 'Anything kept must expire quickly.');
    }

    public function test_any_signed_in_page_drops_the_kept_details(): void
    {
        $head = file_get_contents(resource_path('views/partials/page-head.blade.php'));

        $this->assertStringContainsString("sessionStorage.removeItem('datasensei.signin.retry')", $head);
        $this->assertStringContainsString("@unless (request()->routeIs('login'))", $head, 'The sign-in page itself must not wipe the retry before it runs.');
    }

    public function test_the_submitted_email_survives_so_it_need_not_be_retyped(): void
    {
        $this->registerExpiringRoute();

        $this->from('/login')->post('/__expired-token-probe', [
            'email' => 'someone@example.test',
            'password' => 'Secret!2026',
        ]);

        $this->assertSame('someone@example.test', old('email'));
    }

    public function test_the_password_is_never_flashed_back(): void
    {
        $this->registerExpiringRoute();

        $this->from('/login')->post('/__expired-token-probe', [
            'email' => 'someone@example.test',
            'password' => 'Secret!2026',
            'password_confirmation' => 'Secret!2026',
        ]);

        $this->assertNull(old('password'), 'A password must never be written into the session.');
        $this->assertNull(old('password_confirmation'));
    }

    public function test_a_json_client_is_given_a_fresh_token_to_retry_with(): void
    {
        $this->registerExpiringRoute();

        $response = $this->postJson('/__expired-token-probe', [
            'email' => 'someone@example.test',
        ]);

        $response->assertStatus(419);
        $response->assertJson(['session_expired' => true]);
        $this->assertNotEmpty($response->json('csrf_token'));
        $this->assertStringContainsString('session expired', (string) $response->json('message'));
    }

    /**
     * The POST that submits the sign-in form has no route name, so matching on
     * the name alone missed the one request this handler exists for. This
     * stands in at the same path to prove the path is matched too.
     */
    public function test_the_unnamed_sign_in_post_is_covered_by_path(): void
    {
        Route::middleware('web')->post('/login', function () {
            abort(419);
        });

        $response = $this->from('/login')->post('/login', [
            'email' => 'someone@example.test',
            'password' => 'Secret!2026',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_other_errors_are_left_alone(): void
    {
        Route::middleware('web')->get('/__not-found-probe', function () {
            abort(404);
        });

        $this->get('/__not-found-probe')->assertNotFound();
    }

    /**
     * The assignment and assessment screens watch for a 419 to notice an
     * expired session. Turning those into redirects would break them, so the
     * friendlier handling is confined to the sign-in and registration forms.
     */
    public function test_an_expired_token_elsewhere_keeps_its_status(): void
    {
        Route::middleware('web')->post('/__other-expired-probe', function () {
            abort(419);
        });

        $this->from('/login')
            ->post('/__other-expired-probe', ['anything' => 'here'])
            ->assertStatus(419);
    }

    public function test_csrf_verification_is_still_in_the_web_stack(): void
    {
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $groups = (new \ReflectionClass($kernel))->getProperty('middlewareGroups');
        $web = $groups->getValue($kernel)['web'] ?? [];

        // Laravel 11 renamed VerifyCsrfToken to ValidateCsrfToken; either name
        // means the check is in place.
        $present = (bool) array_filter(
            $web,
            fn ($middleware) => is_string($middleware)
                && str_contains($middleware, 'CsrfToken')
        );

        $this->assertTrue(
            $present,
            'Friendlier handling of an expired token must never mean dropping the check. '
            .'Web group: '.implode(', ', array_map('strval', $web))
        );
    }

    public function test_a_correct_token_still_signs_in_normally(): void
    {
        $user = $this->learner();

        $this->get('/login')->assertOk();

        $response = $this->post('/login', [
            '_token' => csrf_token(),
            'email' => $user->email,
            'password' => 'Secret!2026',
        ]);

        $this->assertAuthenticatedAs($user, 'web');
        $response->assertRedirect();
    }

    /**
     * The page must not refuse to submit when the token refresh fails. Browser
     * behaviour cannot be exercised from PHPUnit, so the guarantee is pinned on
     * the script: the refresh returns null instead of throwing, and the form is
     * sent regardless.
     */
    public function test_the_sign_in_page_submits_even_when_the_token_refresh_fails(): void
    {
        $script = file_get_contents(resource_path('views/auth/login.blade.php'));

        $this->assertStringNotContainsString(
            "throw new Error('Session refresh failed.')",
            $script,
            'A failed refresh must not abort the submission.'
        );

        $this->assertStringContainsString(
            'if (!response.ok) return null;',
            $script,
            'A refusal from the refresh must fall through to the ordinary submit.'
        );

        preg_match('/setTimeout\(\(\) => controller\.abort\(\), (\d+)\)/', $script, $matches);

        $this->assertNotEmpty($matches, 'The refresh needs a bounded budget.');
        $this->assertLessThanOrEqual(
            8000,
            (int) $matches[1],
            'Waiting out a long timeout before falling back feels as broken as failing.'
        );
    }

    public function test_the_login_route_still_answers_a_token_refresh(): void
    {
        $response = $this->getJson(route('login'));

        $response->assertOk();
        $this->assertNotEmpty($response->json('csrf_token'));
    }
}
