<?php

namespace Tests\Feature\Regression;

use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin / superadmin audit: every institution picker must offer exactly the
 * institutions the controller accepts, and a refused account change must come
 * back as a readable message instead of the generic error page.
 */
class AuditAdminInstitutionChoicesTest extends TestCase
{
    use RefreshDatabase;

    private Institution $active;

    private Institution $disabled;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);

        $this->active = Institution::create([
            'name' => 'Active Audit University',
            'email' => 'active-audit@institution.test',
            'status' => 'active',
        ]);

        $this->disabled = Institution::create([
            'name' => 'Disabled Audit College',
            'email' => 'disabled-audit@institution.test',
            'status' => 'disabled',
        ]);
    }

    public function test_admin_user_forms_offer_only_institutions_the_controller_accepts(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $this->roleUser(User::ROLE_USER, ['name' => 'Listed Learner']);

        $html = $this->authenticateAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->getContent();

        // Once for the filter dropdown, which legitimately covers every
        // institution, and never inside a create or edit form.
        $this->assertSame(1, substr_count($html, $this->disabled->name));
        $this->assertGreaterThan(1, substr_count($html, $this->active->name));
    }

    public function test_admin_cannot_attach_an_account_to_a_disabled_institution(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Blocked Institution Admin',
                'email' => 'blocked-institution-admin@audit.test',
                'role' => User::ROLE_INSTITUTION_ADMIN,
                'status' => 'active',
                'institution_id' => $this->disabled->id,
                'password' => 'Audit!2026Secure',
                'password_confirmation' => 'Audit!2026Secure',
            ])
            ->assertSessionHasErrors('institution_id');

        $this->assertDatabaseMissing('users', ['email' => 'blocked-institution-admin@audit.test']);
    }

    public function test_superadmin_assign_institution_admin_picker_lists_only_active_institutions(): void
    {
        $superadmin = $this->roleUser(User::ROLE_SUPERADMIN);

        $html = $this->authenticateAs($superadmin)
            ->get(route('superadmin.users.index'))
            ->assertOk()
            ->getContent();

        $modal = substr($html, (int) strpos($html, 'assign_inst_select'));
        $modal = substr($modal, 0, (int) strpos($modal, '</select>'));

        $this->assertStringContainsString($this->active->name, $modal);
        $this->assertStringNotContainsString($this->disabled->name, $modal);
    }

    public function test_refused_role_change_returns_a_readable_message_instead_of_an_error_page(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $this->active->id]);

        ClassRoom::create([
            'instructor_id' => $instructor->id,
            'institution_id' => $this->active->id,
            'name' => 'Audit Owned Class',
            'class_code' => 'AUDOWN1',
            'is_archived' => false,
        ]);

        $this->authenticateAs($admin)
            ->from(route('admin.users.index'))
            ->put(route('admin.users.update', $instructor), [
                'name' => $instructor->name,
                'email' => $instructor->email,
                'role' => User::ROLE_USER,
                'status' => 'active',
                'institution_id' => $this->active->id,
            ])
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHasErrors([
                'role' => 'Transfer or archive this instructor\'s classes before changing their role.',
            ]);

        $this->assertSame(User::ROLE_INSTRUCTOR, (int) $instructor->fresh()->role);
    }

    public function test_profile_institution_tab_names_the_institution_instead_of_its_id(): void
    {
        $admin = $this->roleUser(User::ROLE_INSTITUTION_ADMIN, ['institution_id' => $this->active->id]);

        $html = $this->authenticateAs($admin)
            ->get(route('profile', ['tab' => 'institution']))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString($this->active->name, $html);
        $this->assertStringNotContainsString('Institution ID', $html);
    }
}
