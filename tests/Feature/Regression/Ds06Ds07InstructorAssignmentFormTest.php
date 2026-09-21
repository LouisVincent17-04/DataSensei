<?php

namespace Tests\Feature\Regression;

use App\Models\ClassAssignment;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DS-06: the create/edit form offers only statuses the controller accepts.
 * DS-07: a deactivated library version keeps existing assignments editable.
 */
class Ds06Ds07InstructorAssignmentFormTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssignmentActors();
    }

    private function formData(int $libraryItemId, array $overrides = []): array
    {
        return array_merge([
            'class_id' => $this->classId,
            'assignment_library_item_id' => $libraryItemId,
            'title' => 'Form Assignment',
            'instructions' => 'Read carefully.',
            'max_attempts' => 2,
            'status' => 'draft',
        ], $overrides);
    }

    /** @return list<string> values of the status control(s) rendered by the form */
    private function offeredStatuses(string $html): array
    {
        if (preg_match('/<select name="status".*?<\/select>/s', $html, $select)) {
            preg_match_all('/<option value="([^"]*)"/', $select[0], $options);

            return $options[1];
        }

        preg_match_all('/<input type="hidden" name="status" value="([^"]*)"/', $html, $hidden);

        return $hidden[1];
    }

    public function test_create_form_offers_only_statuses_that_store_accepts(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $html = $this->actAs($this->instructor)->get(route('instructor.assignments.create'))->assertOk()->getContent();

        $offered = $this->offeredStatuses($html);
        $this->assertSame(['draft', 'published'], $offered);

        foreach ($offered as $status) {
            $this->actAs($this->instructor)
                ->post(route('instructor.assignments.store'), $this->formData($item, ['status' => $status, 'title' => "Created {$status}"]))
                ->assertSessionHasNoErrors()
                ->assertRedirect();
            $this->assertDatabaseHas('class_assignments', ['title' => "Created {$status}", 'status' => $status]);
        }
    }

    public function test_closed_is_still_rejected_on_create(): void
    {
        $item = $this->makeLibraryItem()['item'];

        $this->actAs($this->instructor)
            ->post(route('instructor.assignments.store'), $this->formData($item, ['status' => 'closed']))
            ->assertSessionHasErrors('status');
        $this->assertSame(0, ClassAssignment::count());
    }

    public function test_edit_form_offers_only_the_current_status_and_saving_it_is_accepted(): void
    {
        $item = $this->makeLibraryItem()['item'];

        foreach (['draft', 'published', 'closed'] as $status) {
            $assignmentId = $this->makeClassAssignment($item, $status);
            $html = $this->actAs($this->instructor)
                ->get(route('instructor.assignments.edit', $assignmentId))
                ->assertOk()
                ->getContent();

            $offered = $this->offeredStatuses($html);
            $this->assertSame([$status], $offered, "edit form of a {$status} assignment");
            $this->assertStringNotContainsString('<select name="status"', $html);

            $this->actAs($this->instructor)
                ->put(route('instructor.assignments.update', $assignmentId), $this->formData($item, ['status' => $offered[0], 'title' => "Edited {$status}"]))
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('instructor.assignments.show', $assignmentId));
            $this->assertDatabaseHas('class_assignments', ['id' => $assignmentId, 'title' => "Edited {$status}", 'status' => $status]);
        }
    }

    public function test_status_changes_through_the_edit_request_stay_forbidden(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $draft = $this->makeClassAssignment($item, 'draft');
        $closed = $this->makeClassAssignment($item, 'closed');

        foreach ([[$draft, 'published'], [$draft, 'closed'], [$closed, 'published'], [$closed, 'draft']] as [$id, $target]) {
            $this->actAs($this->instructor)
                ->put(route('instructor.assignments.update', $id), $this->formData($item, ['status' => $target]))
                ->assertSessionHasErrors('status');
        }
        $this->assertSame('draft', ClassAssignment::find($draft)->status);
        $this->assertSame('closed', ClassAssignment::find($closed)->status);
    }

    public function test_dedicated_publish_and_close_actions_enforce_the_transitions(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $id = $this->makeClassAssignment($item, 'draft');

        $this->actAs($this->instructor)->patch(route('instructor.assignments.close', $id))->assertStatus(422);
        $this->actAs($this->instructor)->patch(route('instructor.assignments.publish', $id))->assertRedirect();
        $this->assertSame('published', ClassAssignment::find($id)->status);
        $this->actAs($this->instructor)->patch(route('instructor.assignments.publish', $id))->assertStatus(422);
        $this->actAs($this->otherInstructor)->patch(route('instructor.assignments.close', $id))->assertForbidden();
        $this->actAs($this->instructor)->patch(route('instructor.assignments.close', $id))->assertRedirect();
        $this->assertSame('closed', ClassAssignment::find($id)->status);
        $this->actAs($this->instructor)->patch(route('instructor.assignments.publish', $id))->assertStatus(422);

        // The assignment page is where those actions live.
        $draft = $this->makeClassAssignment($item, 'draft');
        $this->actAs($this->instructor)->get(route('instructor.assignments.show', $draft))
            ->assertOk()
            ->assertSee(route('instructor.assignments.publish', $draft), false);
    }

    public function test_deactivated_current_version_stays_available_for_metadata_edits(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $otherInactive = $this->makeLibraryItem(5, false)['item'];
        $assignmentId = $this->makeClassAssignment($item, 'published', 2);
        $this->makeAttempt($assignmentId);
        DB::table('assignment_library_items')->where('id', $item)->update(['is_active' => false]);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assignments.edit', $assignmentId))
            ->assertOk()
            ->assertSee('(inactive version, kept for this assignment)')
            ->getContent();
        $this->assertMatchesRegularExpression('/<option value="' . $item . '" data-title[^>]*selected/', $html);
        $this->assertStringNotContainsString('<option value="' . $otherInactive . '" data-title', $html, 'Unrelated inactive versions are not offered.');

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $assignmentId), $this->formData($item, [
                'status' => 'published',
                'title' => 'Renamed after deactivation',
                'instructions' => 'New instructions.',
                'due_at' => '2026-10-01 17:00:00',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('instructor.assignments.show', $assignmentId));

        $assignment = ClassAssignment::find($assignmentId);
        $this->assertSame('Renamed after deactivation', $assignment->title);
        $this->assertSame('New instructions.', $assignment->instructions);
        $this->assertSame('2026-10-01 17:00:00', $assignment->due_at->format('Y-m-d H:i:s'));
        $this->assertSame($item, (int) $assignment->assignment_library_item_id);
    }

    public function test_new_assignments_still_reject_inactive_content(): void
    {
        $inactive = $this->makeLibraryItem(5, false)['item'];

        $this->actAs($this->instructor)
            ->post(route('instructor.assignments.store'), $this->formData($inactive))
            ->assertNotFound();
        $this->assertSame(0, ClassAssignment::count());

        $html = $this->actAs($this->instructor)->get(route('instructor.assignments.create'))->getContent();
        $this->assertStringNotContainsString('<option value="' . $inactive . '" data-title', $html);
    }

    public function test_source_cannot_change_after_attempts_exist(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $activeReplacement = $this->makeLibraryItem()['item'];
        $assignmentId = $this->makeClassAssignment($item, 'published');
        $this->makeAttempt($assignmentId);

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $assignmentId), $this->formData($activeReplacement, ['status' => 'published']))
            ->assertSessionHasErrors('assignment_library_item_id');
        $this->assertSame($item, (int) ClassAssignment::find($assignmentId)->assignment_library_item_id);
    }

    public function test_source_change_without_attempts_requires_an_active_version(): void
    {
        $item = $this->makeLibraryItem()['item'];
        $activeReplacement = $this->makeLibraryItem()['item'];
        $inactiveReplacement = $this->makeLibraryItem(5, false)['item'];
        $assignmentId = $this->makeClassAssignment($item, 'draft');

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $assignmentId), $this->formData($inactiveReplacement))
            ->assertSessionHasErrors('assignment_library_item_id');
        $this->assertSame($item, (int) ClassAssignment::find($assignmentId)->assignment_library_item_id);

        $this->actAs($this->instructor)
            ->put(route('instructor.assignments.update', $assignmentId), $this->formData($activeReplacement))
            ->assertSessionHasNoErrors();
        $this->assertSame($activeReplacement, (int) ClassAssignment::find($assignmentId)->assignment_library_item_id);
    }
}
