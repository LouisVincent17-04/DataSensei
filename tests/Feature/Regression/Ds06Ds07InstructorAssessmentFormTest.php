<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-06/DS-07, ported to the assessment flow (assignments were merged into
 * assessments in DataSensei Updates 11). The create and settings forms only
 * reach the statuses the controller accepts: a new assessment is always a
 * draft, settings edits never change the status, and only the dedicated
 * publish/close actions move it forward. The settings validation guards the
 * documented ranges, ownership and archived classes, plus the new purpose
 * and passing-score fields.
 */
class Ds06Ds07InstructorAssessmentFormTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->seedAssessmentActors();
    }

    /** @param array<string, mixed> $overrides */
    private function settingsData(array $overrides = []): array
    {
        return array_merge([
            'class_id' => $this->classId,
            'title' => 'Form Assessment',
            'instructions' => 'Read carefully.',
            'purpose' => 'quiz',
            'topic_title' => 'Pandas',
            'time_limit_minutes' => 30,
            'max_attempts' => 2,
            'passing_score_percent' => 75,
            'available_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'due_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    private function makeArchivedClass(?int $instructorId = null): int
    {
        return (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructorId ?? $this->instructor->id,
            'name' => 'Archived Form Class',
            'class_code' => 'A' . Str::upper(Str::random(7)),
            'is_archived' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // ── DS-06: only reachable statuses ───────────────────────────────

    public function test_create_form_offers_no_status_control_and_new_assessments_are_always_drafts(): void
    {
        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assessments.new'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<select name="status"', $html);
        $this->assertStringNotContainsString('name="status"', $html);

        // Even an injected status field cannot create anything but a draft.
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData([
                'title' => 'Injected Status Assessment',
                'status' => 'published',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $assessment = Assessment::where('title', 'Injected Status Assessment')->firstOrFail();
        $this->assertSame('draft', $assessment->status);
        $this->assertNull($assessment->published_at);
        $this->assertSame('quiz', $assessment->purpose);
        $this->assertSame(75, (int) $assessment->passing_score_percent);
    }

    public function test_settings_edits_save_drafts_but_never_change_the_status(): void
    {
        $draft = $this->makeAssessment(5, ['status' => 'draft'])['assessment'];

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $draft), $this->settingsData([
                'title' => 'Edited Draft',
                'status' => 'published',
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $assessment = Assessment::findOrFail($draft);
        $this->assertSame('draft', $assessment->status);
        $this->assertSame('Edited Draft', $assessment->title);

        // Published and closed assessments cannot be edited at all.
        foreach (['published', 'closed'] as $status) {
            $locked = $this->makeAssessment(5, ['status' => $status])['assessment'];
            $this->actAs($this->instructor)
                ->patch(route('instructor.assessments.settings.update', $locked), $this->settingsData([
                    'title' => "Edited {$status}",
                ]))
                ->assertStatus(422);
            $this->assertSame($status, Assessment::findOrFail($locked)->status);
        }
    }

    public function test_dedicated_publish_and_close_actions_enforce_the_transitions(): void
    {
        $id = $this->makeAssessment(5, ['status' => 'draft'])['assessment'];

        $this->actAs($this->instructor)->patch(route('instructor.assessments.close', $id))->assertStatus(422);
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $id))->assertRedirect();
        $this->assertSame('published', Assessment::findOrFail($id)->status);
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $id))->assertStatus(422);
        $this->actAs($this->otherInstructor)->patch(route('instructor.assessments.close', $id))->assertForbidden();
        $this->actAs($this->instructor)->patch(route('instructor.assessments.close', $id))->assertRedirect();
        $this->assertSame('closed', Assessment::findOrFail($id)->status);
        $this->actAs($this->instructor)->patch(route('instructor.assessments.publish', $id))->assertStatus(422);

        // The builder page is where those actions live.
        $draft = $this->makeAssessment(5, ['status' => 'draft'])['assessment'];
        $this->actAs($this->instructor)->get(route('instructor.assessments.builder', $draft))
            ->assertOk()
            ->assertSee(route('instructor.assessments.publish', $draft), false);
    }

    public function test_an_assessment_without_complete_questions_cannot_be_published(): void
    {
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData(['title' => 'Empty Assessment']))
            ->assertSessionHasNoErrors();
        $empty = Assessment::where('title', 'Empty Assessment')->firstOrFail();

        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.publish', $empty))
            ->assertSessionHasErrors('assessment');
        $this->assertSame('draft', $empty->fresh()->status);
    }

    // ── Validation: ranges, purpose and passing score ────────────────

    /** @return iterable<string, array{array<string, mixed>, string}> */
    public static function badSettingsProvider(): iterable
    {
        yield 'time limit zero' => [['time_limit_minutes' => 0], 'time_limit_minutes'];
        yield 'time limit negative' => [['time_limit_minutes' => -5], 'time_limit_minutes'];
        yield 'time limit above a day' => [['time_limit_minutes' => 1441], 'time_limit_minutes'];
        yield 'attempts zero' => [['max_attempts' => 0], 'max_attempts'];
        yield 'attempts above ten' => [['max_attempts' => 11], 'max_attempts'];
        yield 'missing attempts' => [['max_attempts' => null], 'max_attempts'];
        yield 'passing score zero' => [['passing_score_percent' => 0], 'passing_score_percent'];
        yield 'passing score above 100' => [['passing_score_percent' => 101], 'passing_score_percent'];
        yield 'passing score not a number' => [['passing_score_percent' => 'most'], 'passing_score_percent'];
        yield 'due before available' => [
            ['available_at' => '2026-10-02 08:00:00', 'due_at' => '2026-10-01 08:00:00'],
            'due_at',
        ];
        yield 'missing title' => [['title' => null], 'title'];
        yield 'purpose junk' => [['purpose' => 'exam'], 'purpose'];
        yield 'purpose is not free text' => [['purpose' => 'Final Examination!'], 'purpose'];
    }

    /** @param array<string, mixed> $overrides */
    #[DataProvider('badSettingsProvider')]
    public function test_bad_settings_are_rejected_on_create_and_on_edit(array $overrides, string $errorKey): void
    {
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData($overrides))
            ->assertSessionHasErrors($errorKey);
        $this->assertSame(0, Assessment::count());

        $draft = $this->makeAssessment(5, ['status' => 'draft', 'title' => 'Range Draft'])['assessment'];
        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $draft), $this->settingsData($overrides))
            ->assertSessionHasErrors($errorKey);
        $this->assertSame('Range Draft', Assessment::findOrFail($draft)->title);
    }

    public function test_purpose_accepts_each_allowed_value_and_none(): void
    {
        foreach (['' => null, 'homework' => 'Homework', 'quiz' => 'Quiz', 'examination' => 'Examination'] as $purpose => $label) {
            $title = 'Purpose ' . ($purpose === '' ? 'none' : $purpose);
            $this->actAs($this->instructor)
                ->post(route('instructor.assessments.save'), $this->settingsData([
                    'title' => $title,
                    'purpose' => (string) $purpose,
                ]))
                ->assertSessionHasNoErrors()
                ->assertRedirect();

            $assessment = Assessment::where('title', $title)->firstOrFail();
            $this->assertSame($purpose === '' ? null : $purpose, $assessment->purpose);
            $this->assertSame($label, $assessment->purposeLabel());
        }
    }

    public function test_passing_score_percent_accepts_the_documented_bounds_and_none(): void
    {
        foreach ([1, 100, null] as $value) {
            $title = 'Passing ' . ($value === null ? 'none' : $value);
            $this->actAs($this->instructor)
                ->post(route('instructor.assessments.save'), $this->settingsData([
                    'title' => $title,
                    'passing_score_percent' => $value,
                ]))
                ->assertSessionHasNoErrors();
            $stored = Assessment::where('title', $title)->firstOrFail()->passing_score_percent;
            $this->assertSame($value, $stored === null ? null : (int) $stored);
        }
    }

    public function test_untimed_assessments_store_no_time_limit(): void
    {
        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData([
                'title' => 'Untimed Homework',
                'purpose' => 'homework',
                'time_limit_minutes' => null,
            ]))
            ->assertSessionHasNoErrors();

        $this->assertNull(Assessment::where('title', 'Untimed Homework')->firstOrFail()->time_limit_minutes);
    }

    // ── Ownership and archived classes ───────────────────────────────

    public function test_another_instructors_class_is_refused(): void
    {
        $foreignClassId = (int) DB::table('classes')->insertGetId([
            'instructor_id' => $this->otherInstructor->id,
            'name' => 'Foreign Class',
            'class_code' => 'F' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData(['class_id' => $foreignClassId]))
            ->assertNotFound();
        $this->assertSame(0, Assessment::count());

        $draft = $this->makeAssessment(5, ['status' => 'draft'])['assessment'];
        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $draft), $this->settingsData(['class_id' => $foreignClassId]))
            ->assertNotFound();
        $this->assertSame($this->classId, (int) Assessment::findOrFail($draft)->class_id);

        // The other instructor cannot edit this draft at all.
        $this->actAs($this->otherInstructor)
            ->patch(route('instructor.assessments.settings.update', $draft), $this->settingsData())
            ->assertForbidden();
    }

    public function test_an_archived_class_cannot_receive_new_assessments(): void
    {
        $archivedId = $this->makeArchivedClass();

        $this->actAs($this->instructor)
            ->post(route('instructor.assessments.save'), $this->settingsData(['class_id' => $archivedId]))
            ->assertNotFound();
        $this->assertSame(0, Assessment::count());

        $draft = $this->makeAssessment(5, ['status' => 'draft'])['assessment'];
        $this->actAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $draft), $this->settingsData(['class_id' => $archivedId]))
            ->assertNotFound();
        $this->assertSame($this->classId, (int) Assessment::findOrFail($draft)->class_id);
    }
}
