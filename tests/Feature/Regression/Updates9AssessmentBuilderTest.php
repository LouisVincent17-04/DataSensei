<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 9, task 2: an instructor creates an assessment with one
 * short form, adds each question on the same page, previews it with the
 * answers, and publishes it. Planning with a Table of Specifications is
 * optional.
 */
class Updates9AssessmentBuilderTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $student;
    private ClassRoom $class;

    protected function setUp(): void
    {
        parent::setUp();

        $institution = Institution::create([
            'name' => 'Updates9 Builder Institution',
            'email' => 'u9-builder-'.Str::lower(Str::random(6)).'@institution.test',
            'status' => 'active',
        ]);
        $this->instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
        $this->student = $this->roleUser(User::ROLE_USER);
        $this->class = ClassRoom::create([
            'instructor_id' => $this->instructor->id,
            'name' => 'Statistics 101',
            'is_archived' => false,
        ]);
        DB::table('class_student')->insert([
            'class_id' => $this->class->id,
            'student_id' => $this->student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_the_create_form_is_one_short_form_without_a_table_of_specifications(): void
    {
        $this->authenticateAs($this->instructor)
            ->get(route('instructor.assessments.index'))
            ->assertOk()
            ->assertSee(route('instructor.assessments.new'), false);

        $html = $this->authenticateAs($this->instructor)
            ->get(route('instructor.assessments.new'))
            ->assertOk()
            ->assertSee('Statistics 101')
            ->assertSee('name="title"', false)
            ->assertSee('name="due_at"', false)
            ->assertSee('name="available_at"', false)
            ->assertSee('name="time_limit_minutes"', false)
            ->getContent();

        $this->assertNoDecorations($html);
    }

    public function test_full_flow_create_add_each_type_preview_publish_and_the_student_sees_it(): void
    {
        $assessment = $this->createDraft();

        // Every question type, added one at a time on the builder page.
        $this->addQuestion($assessment, [
            'question_type' => 'multiple_choice',
            'question_text' => 'Which measure is the middle value?',
            'points' => 2,
            'option_texts' => ['Mean', 'Median', 'Mode'],
            'correct_option' => 1,
        ]);
        $this->addQuestion($assessment, [
            'question_type' => 'true_false',
            'question_text' => 'The mean is affected by outliers.',
            'points' => 1,
            'correct_answer' => 'true',
        ]);
        $this->addQuestion($assessment, [
            'question_type' => 'short_answer',
            'question_text' => 'Name the Python library used for data frames.',
            'points' => 1,
            'correct_answer' => "pandas\nPandas",
        ]);
        $this->addQuestion($assessment, [
            'question_type' => 'fill_blank',
            'question_text' => 'The ____ is the most frequent value.',
            'points' => 1,
            'correct_answer' => 'mode',
        ]);
        // Essay grading notes are optional.
        $this->addQuestion($assessment, [
            'question_type' => 'essay',
            'question_text' => 'Explain when the median is better than the mean.',
            'points' => 5,
        ]);

        $assessment->refresh();
        $this->assertSame(5, (int) $assessment->total_items);
        $this->assertSame(10, (int) $assessment->total_points);
        $this->assertNull($assessment->table_of_specification_id);
        $this->assertSame([1, 2, 3, 4, 5], $assessment->questions()->orderBy('item_number')->pluck('item_number')->map(fn ($n) => (int) $n)->all());

        $builder = $this->authenticateAs($this->instructor)
            ->get(route('instructor.assessments.builder', $assessment))
            ->assertOk()
            ->assertSee('Questions (5)')
            ->assertSee('Add a question')
            ->getContent();
        $this->assertNoDecorations($builder);

        // Preview shows the questions with every correct answer.
        $preview = $this->authenticateAs($this->instructor)
            ->get(route('instructor.assessments.preview', $assessment))
            ->assertOk()
            ->assertSee('Which measure is the middle value?')
            ->assertSee('Median')
            ->assertSee('Correct answer')
            ->assertSee('Accepted: pandas, Pandas')
            ->assertSee('Accepted: mode')
            ->assertSee('Show the answers')
            ->getContent();
        $this->assertNoDecorations($preview);

        $this->authenticateAs($this->instructor)
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertRedirect()
            ->assertSessionHasNoErrors();
        $this->assertSame('published', $assessment->fresh()->status);

        // Published: read-only.
        $this->authenticateAs($this->instructor)
            ->post(route('instructor.assessments.questions.store', $assessment), [
                'question_type' => 'true_false',
                'question_text' => 'Late addition',
                'points' => 1,
                'correct_answer' => 'false',
            ])
            ->assertStatus(422);
        $this->assertSame(5, $assessment->questions()->count());

        $this->authenticateAs($this->student)
            ->get(route('student.assessments.index'))
            ->assertOk()
            ->assertSee('Midterm Quiz');
        $this->authenticateAs($this->student)
            ->get(route('student.assessments.show', $assessment))
            ->assertOk();
    }

    public function test_an_incomplete_question_blocks_publishing_and_says_why(): void
    {
        $assessment = $this->createDraft();
        $this->addQuestion($assessment, [
            'question_type' => 'multiple_choice',
            'question_text' => 'Pick one',
            'points' => 1,
            'option_texts' => ['Only one choice'],
        ]);

        $this->authenticateAs($this->instructor)
            ->from(route('instructor.assessments.builder', $assessment))
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertRedirect(route('instructor.assessments.builder', $assessment))
            ->assertSessionHasErrors('assessment');
        $this->assertSame('draft', $assessment->fresh()->status);

        // An empty assessment cannot be published either.
        $empty = $this->createDraft('Empty Quiz');
        $this->authenticateAs($this->instructor)
            ->from(route('instructor.assessments.builder', $empty))
            ->patch(route('instructor.assessments.publish', $empty))
            ->assertSessionHasErrors('assessment');
    }

    public function test_removing_a_question_renumbers_the_rest_and_updates_the_totals(): void
    {
        $assessment = $this->createDraft();
        foreach (['First', 'Second', 'Third'] as $i => $text) {
            $this->addQuestion($assessment, [
                'question_type' => 'true_false',
                'question_text' => $text,
                'points' => $i + 1,
                'correct_answer' => 'true',
            ]);
        }

        $second = $assessment->questions()->where('question_text', 'Second')->firstOrFail();
        $this->authenticateAs($this->instructor)
            ->delete(route('instructor.assessments.questions.destroy', [$assessment, $second]))
            ->assertRedirect();

        $this->assertSame(
            ['First' => 1, 'Third' => 2],
            $assessment->questions()->orderBy('item_number')->pluck('item_number', 'question_text')->map(fn ($n) => (int) $n)->all()
        );
        $this->assertSame(4, (int) $assessment->fresh()->total_points);
        $this->assertSame(2, (int) $assessment->fresh()->total_items);
    }

    public function test_settings_are_edited_in_place_while_a_draft(): void
    {
        $assessment = $this->createDraft();

        $this->authenticateAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $assessment), [
                'class_id' => $this->class->id,
                'title' => 'Renamed Quiz',
                'instructions' => 'Answer every question.',
                'time_limit_minutes' => 45,
                'max_attempts' => 2,
                'available_at' => now()->addDay()->format('Y-m-d H:i'),
                'due_at' => now()->addDays(3)->format('Y-m-d H:i'),
            ])
            ->assertRedirect(route('instructor.assessments.builder', $assessment).'#settings');

        $fresh = $assessment->fresh();
        $this->assertSame('Renamed Quiz', $fresh->title);
        $this->assertSame(45, (int) $fresh->time_limit_minutes);
        $this->assertSame(2, (int) $fresh->max_attempts);
        $this->assertNotNull($fresh->due_at);

        // The due date cannot come before the opening date.
        $this->authenticateAs($this->instructor)
            ->patch(route('instructor.assessments.settings.update', $assessment), [
                'class_id' => $this->class->id,
                'title' => 'Renamed Quiz',
                'max_attempts' => 1,
                'available_at' => now()->addDays(3)->format('Y-m-d H:i'),
                'due_at' => now()->addDay()->format('Y-m-d H:i'),
            ])
            ->assertSessionHasErrors('due_at');
    }

    public function test_another_instructor_and_students_cannot_open_or_change_it(): void
    {
        $assessment = $this->createDraft();
        $other = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $this->instructor->institution_id]);

        foreach (['builder', 'preview'] as $page) {
            $this->authenticateAs($other)->get(route('instructor.assessments.'.$page, $assessment))->assertForbidden();
        }
        $this->authenticateAs($other)
            ->post(route('instructor.assessments.questions.store', $assessment), [
                'question_type' => 'true_false', 'question_text' => 'x', 'points' => 1, 'correct_answer' => 'true',
            ])
            ->assertForbidden();

        // A draft is never shown to students.
        $this->authenticateAs($this->student)->get(route('student.assessments.index'))->assertOk()->assertDontSee('Midterm Quiz');
        $this->authenticateAs($this->student)->get(route('instructor.assessments.preview', $assessment))->assertStatus(403);

        // An instructor cannot create one for someone else's class.
        $otherClass = ClassRoom::create(['instructor_id' => $other->id, 'name' => 'Not Mine', 'is_archived' => false]);
        $this->authenticateAs($this->instructor)
            ->post(route('instructor.assessments.save'), ['class_id' => $otherClass->id, 'title' => 'Sneaky', 'max_attempts' => 1])
            ->assertNotFound();
        $this->assertSame(0, Assessment::where('title', 'Sneaky')->count());
    }

    private function createDraft(string $title = 'Midterm Quiz'): Assessment
    {
        $this->authenticateAs($this->instructor)
            ->post(route('instructor.assessments.save'), [
                'class_id' => $this->class->id,
                'title' => $title,
                'max_attempts' => 1,
                'time_limit_minutes' => 30,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $assessment = Assessment::where('title', $title)->latest('id')->firstOrFail();
        $this->assertSame('draft', $assessment->status);

        return $assessment;
    }

    /** @param array<string, mixed> $fields */
    private function addQuestion(Assessment $assessment, array $fields): void
    {
        $this->authenticateAs($this->instructor)
            ->post(route('instructor.assessments.questions.store', $assessment), $fields)
            ->assertSessionHasNoErrors()
            ->assertRedirect();
    }

    private function assertNoDecorations(string $html): void
    {
        // Flat academic layout: no pills, badges, chips or gradients.
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*\b[\w-]*(pill|badge|chip|capsule)[\w-]*\b/i', $html);
        // The shared design system defines one brand gradient; these pages never use it.
        $this->assertStringNotContainsString('var(--ds-gradient-brand)', $html);
        foreach (glob(resource_path('views/instructor/assessments/*.blade.php')) as $view) {
            $source = str_replace(' ', '', (string) file_get_contents($view));
            $this->assertStringNotContainsString('gradient(', $source, basename($view));
            $this->assertStringNotContainsString('border-radius:999px', $source, basename($view));
        }
    }
}
