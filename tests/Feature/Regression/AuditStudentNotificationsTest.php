<?php

namespace Tests\Feature\Regression;

use App\Models\Notification;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Student notification centre.
 *
 * "Mark all as read" and the per-row "Mark as read" button posted plain POST
 * to routes registered as PATCH, so both answered 405 and nothing was ever
 * marked read from the page. "Mark as unread", right next to it, spoofed the
 * method correctly.
 */
class AuditStudentNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
    }

    public function test_notification_forms_spoof_the_method_the_route_expects(): void
    {
        $student = $this->makeStudent();
        $unread = $this->makeNotification($student);
        $read = $this->makeNotification($student);
        $read->forceFill(['is_read' => true, 'read_at' => now()])->save();

        $html = $this->actingAsStudent($student)
            ->get(route('student.notifications.index'))
            ->assertOk()
            ->getContent();

        foreach ([
            route('student.notifications.read-all'),
            route('student.notifications.read', $unread),
            route('student.notifications.unread', $read),
        ] as $action) {
            $form = $this->formFor($html, $action);
            $this->assertNotNull($form, 'No form posts to ' . $action);
            $this->assertStringContainsString(
                'name="_method" value="PATCH"',
                $form,
                'The form for ' . $action . ' does not spoof PATCH, so the route answers 405.'
            );
        }
    }

    public function test_mark_all_as_read_works_the_way_the_page_submits_it(): void
    {
        $student = $this->makeStudent();
        $this->makeNotification($student);
        $this->makeNotification($student);

        $this->actingAsStudent($student)
            ->from(route('student.notifications.index'))
            ->post(route('student.notifications.read-all'), ['_method' => 'PATCH'])
            ->assertRedirect(route('student.notifications.index'));

        $this->assertSame(0, Notification::where('user_id', $student->id)->where('is_read', false)->count());
    }

    public function test_mark_one_as_read_works_the_way_the_page_submits_it(): void
    {
        $student = $this->makeStudent();
        $notification = $this->makeNotification($student);

        $this->actingAsStudent($student)
            ->from(route('student.notifications.index'))
            ->post(route('student.notifications.read', $notification), ['_method' => 'PATCH'])
            ->assertRedirect(route('student.notifications.index'));

        $this->assertTrue((bool) $notification->fresh()->is_read);
    }

    public function test_another_students_notification_stays_out_of_reach(): void
    {
        $student = $this->makeStudent();
        $other = $this->makeStudent();
        $theirs = $this->makeNotification($other);

        $this->actingAsStudent($student)
            ->post(route('student.notifications.read', $theirs), ['_method' => 'PATCH'])
            ->assertNotFound();

        $this->actingAsStudent($student)
            ->post(route('student.notifications.destroy', $theirs), ['_method' => 'DELETE'])
            ->assertNotFound();

        $this->assertFalse((bool) $theirs->fresh()->is_read);
    }

    public function test_empty_and_filtered_notification_pages_render(): void
    {
        $student = $this->makeStudent();

        $this->actingAsStudent($student)->get(route('student.notifications.index'))->assertOk();
        $this->actingAsStudent($student)->get(route('student.notifications.index', ['filter' => 'unread']))->assertOk();
        $this->actingAsStudent($student)->get(route('student.notifications.index', ['filter' => 'nonsense']))->assertOk();
        $this->actingAsStudent($student)->get(route('student.notifications.feed'))->assertOk();
        $this->actingAsStudent($student)->get(route('student.notifications.count'))->assertOk()
            ->assertJson(['unread_count' => 0]);
    }

    private function formFor(string $html, string $action): ?string
    {
        preg_match_all('/<form\b[^>]*action="([^"]*)"[^>]*>(.*?)<\/form>/s', $html, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (html_entity_decode($match[1]) === $action) {
                return $match[0];
            }
        }

        return null;
    }

    private function makeStudent(): User
    {
        return User::create([
            'name' => 'Audit Student',
            'email' => 'audit-notify-' . Str::lower(Str::random(8)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    private function makeNotification(User $student): Notification
    {
        return Notification::create([
            'user_id' => $student->id,
            'type' => 'general',
            'title' => 'Audit notice',
            'notification_text' => 'Something happened in your class.',
            'is_read' => false,
        ]);
    }

    private function actingAsStudent(User $student)
    {
        return $this->actingAs($student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student),
        ]);
    }
}
