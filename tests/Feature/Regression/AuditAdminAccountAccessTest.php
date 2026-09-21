<?php

namespace Tests\Feature\Regression;

use App\Models\Institution;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin/superadmin audit: an account that loses access mid-session must be
 * returned to the sign-in page with the reason. EnsureActiveUser built a bare
 * RedirectResponse, which carries no session, so withErrors() fataled and the
 * signed-out user got a 500 page instead.
 *
 * The same file also pins the admin workspace's role boundaries: an admin may
 * not reach an admin or superadmin account, and may not hand out either role.
 */
class AuditAdminAccountAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    private function institution(string $status = 'active'): Institution
    {
        return Institution::create([
            'name' => 'Audit Institution '.$status,
            'email' => $status.'-audit@institution.test',
            'status' => $status,
        ]);
    }

    public function test_account_disabled_mid_session_is_redirected_to_login_with_the_reason(): void
    {
        $user = $this->roleUser(User::ROLE_USER);
        $this->authenticateAs($user);

        $user->forceFill(['status' => 'disabled'])->save();

        $this->get(route('studentDashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Your account has been disabled. Please contact the administrator.']);

        $this->assertGuest();
    }

    public function test_institution_admin_is_redirected_to_login_when_the_institution_is_deactivated(): void
    {
        $institution = $this->institution();
        $admin = $this->roleUser(User::ROLE_INSTITUTION_ADMIN, ['institution_id' => $institution->id]);
        $this->authenticateAs($admin);

        $this->get(route('institution-admin.dashboard'))->assertOk();

        $institution->forceFill(['status' => 'disabled'])->save();

        // actingAs keeps the very same model instance for every request in a
        // test, so the relation is re-read the way a real request would.
        $this->authenticateAs($admin->fresh());

        $this->get(route('institution-admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Your institution access is inactive. Please contact the administrator.']);

        $this->assertGuest();
    }

    public function test_changed_account_security_state_is_redirected_to_login_with_the_reason(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $this->authenticateAs($admin);

        $admin->forceFill(['email' => 'rotated-'.$admin->email])->save();

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Your account access changed. Please sign in again.']);

        $this->assertGuest();
    }

    public function test_session_without_a_fingerprint_is_redirected_to_login_instead_of_erroring(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Your session needs to be refreshed. Please sign in again.']);
    }

    public function test_admin_cannot_promote_a_learner_to_admin_or_superadmin(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $learner = $this->roleUser(User::ROLE_USER);

        foreach ([User::ROLE_ADMIN, User::ROLE_SUPERADMIN] as $role) {
            $this->authenticateAs($admin)
                ->put(route('admin.users.update', $learner), [
                    'name' => $learner->name,
                    'email' => $learner->email,
                    'role' => $role,
                    'status' => 'active',
                ])
                ->assertSessionHasErrors('role');

            $this->assertSame(User::ROLE_USER, (int) $learner->fresh()->role);
        }
    }

    public function test_admin_cannot_edit_or_disable_an_admin_or_superadmin_account(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);

        foreach ([User::ROLE_ADMIN, User::ROLE_SUPERADMIN] as $role) {
            $target = $this->roleUser($role);

            $this->authenticateAs($admin)
                ->put(route('admin.users.update', $target), [
                    'name' => 'Renamed',
                    'email' => $target->email,
                    'role' => User::ROLE_USER,
                    'status' => 'disabled',
                ])
                ->assertForbidden();

            $this->authenticateAs($admin)
                ->patch(route('admin.users.status', $target))
                ->assertForbidden();

            $fresh = $target->fresh();
            $this->assertSame($role, (int) $fresh->role);
            $this->assertSame('active', $fresh->status);
        }
    }

    public function test_admin_cannot_disable_their_own_account(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin)
            ->patch(route('admin.users.status', $admin))
            ->assertForbidden();

        $this->assertSame('active', $admin->fresh()->status);
    }

    public function test_superadmin_list_offers_no_action_it_refuses_on_another_superadmin(): void
    {
        $superadmin = $this->roleUser(User::ROLE_SUPERADMIN, ['name' => 'Acting Superadmin']);
        $peer = $this->roleUser(User::ROLE_SUPERADMIN, ['name' => 'Peer Superadmin']);

        $html = $this->authenticateAs($superadmin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString(route('superadmin.users.toggleStatus', $peer, false), $html);
        $this->assertStringNotContainsString('openEditModal('.$peer->id.',', str_replace([' ', "\n"], '', $html));
        $this->assertStringContainsString('openEditModal('.$superadmin->id.',', str_replace([' ', "\n"], '', $html));

        // The server side is unchanged: a crafted request is still refused.
        $this->authenticateAs($superadmin)
            ->patch(route('superadmin.users.toggleStatus', $peer))
            ->assertRedirect(route('superadmin.users.index'))
            ->assertSessionHas('error', 'Another Super Admin account cannot be disabled here.');

        $this->assertSame('active', $peer->fresh()->status);
    }

    public function test_institution_admin_cannot_act_on_another_institutions_application(): void
    {
        $mine = $this->institution();
        $theirs = Institution::create([
            'name' => 'Other Audit Institution',
            'email' => 'other-audit@institution.test',
            'status' => 'active',
        ]);

        $admin = $this->roleUser(User::ROLE_INSTITUTION_ADMIN, ['institution_id' => $mine->id]);
        $applicant = $this->roleUser(User::ROLE_USER);

        $application = \App\Models\InstructorApplication::create([
            'user_id' => $applicant->id,
            'institution_id' => $theirs->id,
            'entered_code' => 'OTHER1',
            'status' => 'pending',
        ]);

        $this->authenticateAs($admin)
            ->patch(route('institution-admin.applications.approve', $application))
            ->assertForbidden();

        $this->authenticateAs($admin)
            ->patch(route('institution-admin.applications.reject', $application))
            ->assertForbidden();

        $this->assertSame('pending', $application->fresh()->status);
        $this->assertSame(User::ROLE_USER, (int) $applicant->fresh()->role);
    }

    public function test_institution_admin_without_an_institution_cannot_reach_the_workspace(): void
    {
        $admin = $this->roleUser(User::ROLE_INSTITUTION_ADMIN, ['institution_id' => null]);
        $this->authenticateAs($admin);

        $this->get(route('institution-admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
    }
}
