<?php

namespace Tests\Feature\Regression;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * Instructor audit: enrolment is instructor-managed. Nothing in the
 * application lets a student join with a class code, so the roster must not
 * tell the instructor to share one, and an archived class must not offer an
 * "add student" form that the controller always refuses.
 */
class AuditInstructorClassRosterTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssessmentActors();
    }

    private function makeEmptyClass(bool $archived = false): int
    {
        return (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'name' => $archived ? 'Archived Roster Class' : 'Empty Roster Class',
            'class_code' => $archived ? 'ARCHIVE' : 'EMPTYCL',
            'is_archived' => $archived,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_no_route_lets_a_student_join_a_class_by_code(): void
    {
        $names = collect(app('router')->getRoutes())
            ->map(fn ($route) => (string) $route->getName())
            ->filter()
            ->values();

        $this->assertTrue(
            $names->filter(fn (string $name) => str_contains($name, 'join'))->isEmpty(),
            'A join-by-code route exists again; the roster copy needs to be revisited.'
        );
    }

    public function test_empty_roster_points_at_the_add_by_email_form_instead_of_a_class_code(): void
    {
        $classId = $this->makeEmptyClass();

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.classes.students', $classId))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('No students enrolled yet', $html);
        $this->assertStringNotContainsString('Share the class code', $html);
        $this->assertStringContainsString(
            route('instructor.classes.students.add-by-email', $classId, false),
            $html
        );
    }

    public function test_archived_class_roster_hides_the_add_student_form(): void
    {
        $archivedId = $this->makeEmptyClass(true);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.classes.students', $archivedId))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString(
            route('instructor.classes.students.add-by-email', $archivedId, false),
            $html
        );
        $this->assertStringContainsString('This class is archived', $html);
    }

    public function test_adding_a_student_to_an_archived_class_is_still_refused(): void
    {
        $archivedId = $this->makeEmptyClass(true);

        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $archivedId))
            ->post(route('instructor.classes.students.add-by-email', $archivedId), [
                'email' => $this->student->email,
            ])
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing('class_student', [
            'class_id' => $archivedId,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_removing_a_student_with_an_in_progress_assessment_attempt_is_refused(): void
    {
        $assessmentId = $this->makeAssessment()['assessment'];
        $attempt = $this->makeAttempt($assessmentId, $this->student);

        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $this->classId))
            ->delete(route('instructor.classes.students.remove', [$this->classId, $this->student->id]))
            ->assertRedirect(route('instructor.classes.students', $this->classId))
            ->assertSessionHas('error', 'This student has an in-progress assessment attempt. Resolve it before removing them.');

        $this->assertDatabaseHas('class_student', ['class_id' => $this->classId, 'student_id' => $this->student->id]);

        // Once the attempt is finished the student can be removed.
        $attempt->update(['status' => 'graded', 'submitted_at' => now(), 'graded_at' => now()]);

        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $this->classId))
            ->delete(route('instructor.classes.students.remove', [$this->classId, $this->student->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_student', ['class_id' => $this->classId, 'student_id' => $this->student->id]);
    }

    public function test_bulk_removal_is_refused_while_any_selected_student_has_an_in_progress_attempt(): void
    {
        $assessmentId = $this->makeAssessment()['assessment'];
        $this->makeAttempt($assessmentId, $this->otherStudent);

        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $this->classId))
            ->delete(route('instructor.classes.students.remove-bulk', $this->classId), [
                'student_ids' => [$this->student->id, $this->otherStudent->id],
            ])
            ->assertSessionHas('error', 'One or more selected students have in-progress assessment attempts. Resolve those attempts before removing the group.');

        // Nobody in the group is removed, not even the student without an attempt.
        $this->assertDatabaseHas('class_student', ['class_id' => $this->classId, 'student_id' => $this->student->id]);
        $this->assertDatabaseHas('class_student', ['class_id' => $this->classId, 'student_id' => $this->otherStudent->id]);

        // Without the blocked student the rest of the group is removed.
        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $this->classId))
            ->delete(route('instructor.classes.students.remove-bulk', $this->classId), [
                'student_ids' => [$this->student->id],
            ])
            ->assertSessionHas('success', '1 student(s) removed.');

        $this->assertDatabaseMissing('class_student', ['class_id' => $this->classId, 'student_id' => $this->student->id]);
        $this->assertDatabaseHas('class_student', ['class_id' => $this->classId, 'student_id' => $this->otherStudent->id]);
    }

    public function test_an_in_progress_attempt_in_another_class_does_not_block_removal(): void
    {
        $otherClassId = $this->makeEmptyClass();
        DB::table('class_student')->insert([
            'class_id' => $otherClassId,
            'student_id' => $this->student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $foreignAssessment = $this->makeAssessment(5, ['class_id' => $otherClassId])['assessment'];
        $this->makeAttempt($foreignAssessment, $this->student);

        $this->actAs($this->instructor)
            ->from(route('instructor.classes.students', $this->classId))
            ->delete(route('instructor.classes.students.remove', [$this->classId, $this->student->id]))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('class_student', ['class_id' => $this->classId, 'student_id' => $this->student->id]);
        $this->assertDatabaseHas('class_student', ['class_id' => $otherClassId, 'student_id' => $this->student->id]);
    }

    public function test_create_class_form_does_not_promise_student_self_enrolment(): void
    {
        $html = $this->actAs($this->instructor)
            ->get(route('instructor.classes.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('Students self-enrol', $html);
        $this->assertStringNotContainsString('students can join instantly', $html);
    }
}
