<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DataSensei Updates 9, tasks 9 and 12.
 *
 * Students open their assignments class by class (My Classes, then one
 * class's available, upcoming, missing, late and submitted work), only for
 * classes they are enrolled in. Instructors can read the full content of an
 * assignment, with the correct answers, before and after giving it.
 */
class Updates9AssignmentsTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAssignmentActors();
    }

    public function test_my_classes_lists_only_enrolled_classes_with_a_summary(): void
    {
        $item = $this->makeLibraryItem();
        $this->assignment($item['item'], 'Open Work', ['due_at' => now()->addDays(2)]);
        $this->assignment($item['item'], 'Overdue Work', ['due_at' => now()->subDay()]);

        $notMine = $this->otherClass('Someone Else Class');

        $this->actAs($this->student)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('My Classes')
            ->assertSee('Assignment Workflow Class')
            ->assertSee('Workflow Instructor')
            ->assertSee(route('student.assignments.class', $this->classId), false)
            ->assertSee('1 available')
            ->assertSee('1 missing')
            ->assertDontSee('Someone Else Class')
            ->assertDontSee(route('student.assignments.class', $notMine), false);
    }

    public function test_a_class_page_groups_work_like_an_lms(): void
    {
        $item = $this->makeLibraryItem();
        $this->assignment($item['item'], 'Open Work', ['due_at' => now()->addDays(2)]);
        $this->assignment($item['item'], 'Opens Next Week', ['available_at' => now()->addWeek(), 'due_at' => now()->addWeeks(2)]);
        $this->assignment($item['item'], 'Never Turned In', ['due_at' => now()->subDay()]);
        $late = $this->assignment($item['item'], 'Turned In Late', ['due_at' => now()->subDays(2)]);
        $onTime = $this->assignment($item['item'], 'Turned In On Time', ['due_at' => now()->addDay()]);
        // Drafts are the instructor's; they never reach students.
        $this->assignment($item['item'], 'Hidden Draft', ['status' => 'draft']);

        $this->finished($late, 'late', now()->subDay());
        $this->finished($onTime, 'submitted', now()->subHour());

        $html = $this->actAs($this->student)
            ->get(route('student.assignments.class', $this->classId))
            ->assertOk()
            ->assertSee('Assignment Workflow Class')
            ->assertSeeInOrder(['Available (1)', 'Open Work', 'Upcoming (1)', 'Opens Next Week', 'Missing (1)', 'Never Turned In', 'Late (1)', 'Turned In Late', 'Submitted (1)', 'Turned In On Time'])
            ->assertSee('1 available, 1 upcoming, 1 missing, 1 late, 1 submitted.')
            ->assertDontSee('Hidden Draft')
            ->assertDontSee('No assignments yet')
            ->getContent();

        // The page body (the shared sidebar keeps its unread counter).
        $main = substr($html, (int) strpos($html, '<main'));
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b[\w-]*(pill|badge|chip|capsule)[\w-]*\b/i', $main);
    }

    public function test_a_class_without_assignments_says_so(): void
    {
        $this->actAs($this->student)
            ->get(route('student.assignments.class', $this->classId))
            ->assertOk()
            ->assertSee('No assignments yet');

        $this->actAs($this->student)
            ->get(route('student.assignments.index'))
            ->assertOk()
            ->assertSee('No assignments yet');
    }

    public function test_a_class_the_student_is_not_enrolled_in_cannot_be_opened(): void
    {
        $item = $this->makeLibraryItem();
        $notMine = $this->otherClass('Someone Else Class');
        DB::table('class_assignments')->insert([
            'class_id' => $notMine,
            'assignment_library_item_id' => $item['item'],
            'assigned_by' => $this->otherInstructor->id,
            'title' => 'Not For You',
            'max_attempts' => 1,
            'status' => 'published',
            'assigned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actAs($this->student)
            ->get(route('student.assignments.class', $notMine))
            ->assertNotFound();
    }

    public function test_the_assignment_page_leads_back_to_its_class(): void
    {
        $item = $this->makeLibraryItem();
        $id = $this->assignment($item['item'], 'Open Work', ['due_at' => now()->addDays(2)]);

        $this->actAs($this->student)
            ->get(route('student.assignments.show', $id))
            ->assertOk()
            ->assertSee(route('student.assignments.class', $this->classId), false);
    }

    public function test_instructors_read_the_full_content_before_giving_it(): void
    {
        $item = $this->makeLibraryItem();
        DB::table('assignment_library_items')->where('id', $item['item'])->update(['title' => 'Descriptive Statistics Check']);

        $this->actAs($this->instructor)
            ->get(route('instructor.assignments.create'))
            ->assertOk()
            ->assertSee('Preview');

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assignments.library-preview', $item['item']))
            ->assertOk()
            ->assertSee('Questions and answers')
            ->assertSee('Which option is correct?')
            ->assertSee('Correct option')
            ->assertSee('Wrong option')
            ->assertSee('Correct answer')
            ->assertSee('Which library provides DataFrame?')
            ->assertSee('Expected answer')
            ->assertSee('pandas')
            ->assertSeeInOrder(['Total points', '10'])
            ->getContent();

        $this->assertNoCodeNames($html);
    }

    public function test_instructors_read_the_full_content_after_giving_it(): void
    {
        $item = $this->makeLibraryItem();
        DB::table('assignment_library_items')->where('id', $item['item'])->update(['title' => 'Descriptive Statistics Check']);
        $id = $this->assignment($item['item'], 'Given Work', ['due_at' => now()->addDays(2)]);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assignments.show', $id))
            ->assertOk()
            ->assertSee('Assignment details')
            ->assertSee('Questions and answers')
            ->assertSee('Correct answer')
            ->assertSee('Expected answer')
            ->assertSee('pandas')
            ->assertSee('Submissions')
            ->getContent();

        $this->assertNoCodeNames($html);

        // Another instructor cannot read it.
        $this->actAs($this->otherInstructor)
            ->get(route('instructor.assignments.show', $id))
            ->assertForbidden();
    }

    public function test_students_never_see_the_answer_key(): void
    {
        $item = $this->makeLibraryItem();
        $id = $this->assignment($item['item'], 'Given Work', ['due_at' => now()->addDays(2)]);

        $this->actAs($this->student)->get(route('instructor.assignments.library-preview', $item['item']))->assertForbidden();
        $this->actAs($this->student)->get(route('instructor.assignments.show', $id))->assertForbidden();

        $this->actAs($this->student)
            ->get(route('student.assignments.show', $id))
            ->assertOk()
            ->assertDontSee('Correct answer')
            ->assertDontSee('Expected answer');
    }

    public function test_an_inactive_library_item_is_previewable_only_by_instructors_who_use_it(): void
    {
        $item = $this->makeLibraryItem(active: false);

        $this->actAs($this->instructor)
            ->get(route('instructor.assignments.library-preview', $item['item']))
            ->assertNotFound();

        $this->assignment($item['item'], 'Old Work');
        $this->actAs($this->instructor)
            ->get(route('instructor.assignments.library-preview', $item['item']))
            ->assertOk();
        $this->actAs($this->otherInstructor)
            ->get(route('instructor.assignments.library-preview', $item['item']))
            ->assertNotFound();
    }

    /** @param array<string, mixed> $overrides */
    private function assignment(int $itemId, string $title, array $overrides = []): int
    {
        $status = $overrides['status'] ?? 'published';

        return (int) DB::table('class_assignments')->insertGetId(array_merge([
            'class_id' => $this->classId,
            'assignment_library_item_id' => $itemId,
            'assigned_by' => $this->instructor->id,
            'title' => $title,
            'max_attempts' => 1,
            'status' => $status,
            'assigned_at' => $status === 'published' ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides));
    }

    private function finished(int $assignmentId, string $status, $submittedAt): void
    {
        AssignmentSubmission::create([
            'class_assignment_id' => $assignmentId,
            'student_id' => $this->student->id,
            'attempt_no' => 1,
            'status' => $status,
            'score' => 5,
            'total_points' => 10,
            'anti_cheat_session_id' => Str::random(64),
            'started_at' => $submittedAt->copy()->subMinutes(5),
            'submitted_at' => $submittedAt,
        ]);
    }

    private function otherClass(string $name): int
    {
        return (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->otherInstructor->id,
            'name' => $name,
            'class_code' => 'X'.Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function assertNoCodeNames(string $html): void
    {
        // makeLibraryItem codes look like WF-XXXXXXXX and WF-XXXXXXXX-V1
        // (the fixture title carries the code too, so it is renamed first).
        $this->assertDoesNotMatchRegularExpression('/WF-[A-Z0-9]{8}/', strip_tags($html));
        $this->assertDoesNotMatchRegularExpression('/>\s*V\d+\s*</', $html);
    }
}
