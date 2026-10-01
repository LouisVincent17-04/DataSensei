<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 10, task 5: every dashboard is titled for its role in
 * plain words, and no internal role name (institution_admin, super_admin,
 * superadmin) appears as the title.
 */
class Updates10DashboardTitlesTest extends TestCase
{
    use RefreshDatabase;

    private int $institutionId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->institutionId = (int) DB::table('institutions')->insertGetId([
            'name' => 'Title Institution',
            'slug' => 'title-institution-' . Str::lower(Str::random(6)),
            'email' => 'titles-' . Str::lower(Str::random(6)) . '@institution.test',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_each_role_sees_its_own_dashboard_title(): void
    {
        $instructor = $this->user(User::ROLE_INSTRUCTOR, $this->institutionId);
        $student = $this->user(User::ROLE_USER);
        $classId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructor->id, 'name' => 'Title Class', 'class_code' => 'T' . Str::upper(Str::random(7)),
            'is_archived' => false, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('class_student')->insert(['class_id' => $classId, 'student_id' => $student->id, 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $cases = [
            [$this->user(User::ROLE_USER), route('studentDashboard'), 'Public User Dashboard'],
            [$student, route('studentDashboard'), 'Student Dashboard'],
            [$instructor, route('instructor.dashboard'), 'Instructor Dashboard'],
            [$this->user(User::ROLE_INSTITUTION_ADMIN, $this->institutionId), route('institution-admin.dashboard'), 'Institution Admin Dashboard'],
            [$this->user(User::ROLE_ADMIN), route('admin.dashboard'), 'Admin Dashboard'],
            [$this->user(User::ROLE_SUPERADMIN), route('superadmin.dashboard'), 'Super Admin Dashboard'],
        ];

        foreach ($cases as [$user, $url, $title]) {
            $html = $this->actingAs($user)
                ->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user)])
                ->get($url)
                ->assertOk()
                ->getContent();

            $this->assertMatchesRegularExpression('#<h1 class="ds-page-title">\s*' . preg_quote($title, '#') . '\s*</h1>#', $html, $title);
            $this->assertStringContainsString('<title>' . $title . ' — DataSensei</title>', $html, $title);
            foreach (['institution_admin', 'super_admin', 'superadmin'] as $internal) {
                $this->assertDoesNotMatchRegularExpression('#<h1[^>]*>[^<]*' . $internal . '#i', $html, "{$title} shows no internal role name");
            }
        }
    }

    private function user(int $role, ?int $institutionId = null): User
    {
        return User::create([
            'name' => 'Title User ' . Str::random(4),
            'email' => 'title-' . Str::lower(Str::random(8)) . '@example.test',
            'password' => 'TestPassword!123',
            'role' => $role,
            'status' => 'active',
            'institution_id' => $institutionId,
        ]);
    }
}
