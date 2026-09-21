<?php

namespace Tests\Feature\Regression;

use App\Models\ClassAssignment;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * Instructor audit: archiving a class left its assignments editable, but the
 * edit form dropped the archived class from the class list. The form requires
 * a class, so every save silently moved the assignment to another class.
 * Keeping the current class must work; moving one into an archived class must
 * still be refused.
 */
class AuditInstructorAssignmentArchivedClassTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    private int $libraryItemId;
    private int $archivedClassId;
    private int $assignmentId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssignmentActors();

        $this->libraryItemId = $this->makeLibraryItem()['item'];

        $this->archivedClassId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'name' => 'Retired Class',
            'class_code' => 'R' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assignmentId = (int) ClassAssignment::create([
            'class_id' => $this->archivedClassId,
            'assignment_library_item_id' => $this->libraryItemId,
            'assigned_by' => $this->instructor->id,
            'title' => 'Retired Class Assignment',
            'max_attempts' => 1,
            'status' => 'draft',
        ])->id;

        DB::table('classes')->where('id', $this->archivedClassId)->update(['is_archived' => true]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'class_id' => $this->archivedClassId,
            'assignment_library_item_id' => $this->libraryItemId,
            'title' => 'Retired Class Assignment',
            'instructions' => 'Unchanged.',
            'max_attempts' => 1,
            'status' => 'draft',
        ], $overrides);
    }

    public function test_edit_form_still_offers_the_archived_class_the_assignment_belongs_to(): void
    {
        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assignments.edit', $this->assignmentId))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<option value="' . $this->archivedClassId . '"[^>]*selected/',
            $html
        );
    }

    public function test_saving_keeps_the_assignment_in_its_archived_class(): void
    {
        $this->actAs($this->instructor)
            ->from(route('instructor.assignments.edit', $this->assignmentId))
            ->put(route('instructor.assignments.update', $this->assignmentId), $this->payload([
                'title' => 'Renamed While Archived',
            ]))
            ->assertSessionHasNoErrors();

        $assignment = ClassAssignment::findOrFail($this->assignmentId);
        $this->assertSame($this->archivedClassId, (int) $assignment->class_id);
        $this->assertSame('Renamed While Archived', $assignment->title);
    }

    public function test_an_assignment_cannot_be_moved_into_an_archived_class(): void
    {
        $active = ClassAssignment::create([
            'class_id' => $this->classId,
            'assignment_library_item_id' => $this->libraryItemId,
            'assigned_by' => $this->instructor->id,
            'title' => 'Active Class Assignment',
            'max_attempts' => 1,
            'status' => 'draft',
        ]);

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $active), $this->payload([
                'title' => 'Active Class Assignment',
            ]))
            ->assertStatus(404);

        $this->assertSame($this->classId, (int) $active->fresh()->class_id);
    }

    public function test_another_instructor_still_cannot_take_over_the_assignment(): void
    {
        $foreignClassId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->otherInstructor->id,
            'name' => 'Foreign Class',
            'class_code' => 'F' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actAs($this->otherInstructor)
            ->put(route('instructor.assignments.update', $this->assignmentId), $this->payload([
                'class_id' => $foreignClassId,
            ]))
            ->assertStatus(403);

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $this->assignmentId), $this->payload([
                'class_id' => $foreignClassId,
            ]))
            ->assertStatus(404);

        $this->assertSame(
            $this->archivedClassId,
            (int) ClassAssignment::findOrFail($this->assignmentId)->class_id
        );
    }
}
