<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * Instructor audit, ported to assessments (DataSensei Updates 11): archiving
 * a class must not orphan its assessments. The settings form keeps offering
 * the archived class the assessment already belongs to, so saving does not
 * silently move it, while moving work into an archived class, creating new
 * work on one, or taking over another instructor's assessment stays refused.
 */
class AuditInstructorAssessmentArchivedClassTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    private int $archivedClassId;
    private int $assessmentId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssessmentActors();

        $this->archivedClassId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'name' => 'Retired Class',
            'class_code' => 'R' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assessmentId = (int) Assessment::create([
            'class_id' => $this->archivedClassId,
            'created_by' => $this->instructor->id,
            'title' => 'Retired Class Assessment',
            'status' => 'draft',
            'total_items' => 0,
            'total_points' => 0,
            'max_attempts' => 1,
        ])->id;

        DB::table('classes')->where('id', $this->archivedClassId)->update(['is_archived' => true]);
    }

    /** @param array<string, mixed> $overrides */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'class_id' => $this->archivedClassId,
            'title' => 'Retired Class Assessment',
            'instructions' => 'Unchanged.',
            'max_attempts' => 1,
        ], $overrides);
    }

    public function test_settings_form_still_offers_the_archived_class_the_assessment_belongs_to(): void
    {
        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assessments.builder', $this->assessmentId))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<option value="' . $this->archivedClassId . '"[^>]*selected/',
            $html
        );
    }

    public function test_saving_keeps_the_assessment_in_its_archived_class(): void
    {
        $this->actAs($this->instructor)
            ->from(route('instructor.assessments.builder', $this->assessmentId))
            ->patch(route('instructor.assessments.settings.update', $this->assessmentId), $this->payload([
                'title' => 'Renamed While Archived',
            ]))
            ->assertSessionHasNoErrors();

        $assessment = Assessment::findOrFail($this->assessmentId);
        $this->assertSame($this->archivedClassId, (int) $assessment->class_id);
        $this->assertSame('Renamed While Archived', $assessment->title);
    }

    public function test_an_assessment_cannot_be_moved_into_an_archived_class(): void
    {
        $active = Assessment::create([
            'class_id' => $this->classId,
            'created_by' => $this->instructor->id,
            'title' => 'Active Class Assessment',
            'status' => 'draft',
            'total_items' => 0,
            'total_points' => 0,
            'max_attempts' => 1,
        ]);

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $active), $this->payload([
                'title' => 'Active Class Assessment',
            ]))
            ->assertStatus(404);

        $this->assertSame($this->classId, (int) $active->fresh()->class_id);
    }

    public function test_new_assessments_cannot_be_created_on_an_archived_class(): void
    {
        $before = Assessment::count();

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->payload([
                'title' => 'New Work On Archived Class',
            ]))
            ->assertNotFound();

        $this->assertSame($before, Assessment::count());
    }

    public function test_a_draft_cannot_be_published_to_an_archived_class(): void
    {
        // A complete draft: only the archived class stands in the way.
        $draft = $this->makeAssessment(null, ['class_id' => $this->archivedClassId, 'status' => 'draft']);

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.publish', $draft['assessment']))
            ->assertStatus(422);
        $this->assertSame('draft', Assessment::findOrFail($draft['assessment'])->status);

        // The same draft publishes once the class is active again.
        DB::table('classes')->where('id', $this->archivedClassId)->update(['is_archived' => false]);
        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.publish', $draft['assessment']))
            ->assertRedirect();
        $this->assertSame('published', Assessment::findOrFail($draft['assessment'])->status);
    }

    public function test_another_instructor_still_cannot_take_over_the_assessment(): void
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
            ->patch(route('instructor.assessments.settings.update', $this->assessmentId), $this->payload([
                'class_id' => $foreignClassId,
            ]))
            ->assertStatus(403);

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $this->assessmentId), $this->payload([
                'class_id' => $foreignClassId,
            ]))
            ->assertStatus(404);

        $this->assertSame(
            $this->archivedClassId,
            (int) Assessment::findOrFail($this->assessmentId)->class_id
        );
    }
}
