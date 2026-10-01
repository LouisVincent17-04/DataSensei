<?php

namespace Tests\Feature\Regression;

use App\Models\AssessmentSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DataSensei Updates 9, tasks 9 and 12, on the pages that replaced the
 * assignment pages when assignments were merged into assessments (DataSensei
 * Updates 11).
 *
 * Students open their assessments class by class (My Classes, then one
 * class's available, upcoming, missing, late and submitted work), only for
 * classes they are enrolled in, with plain-text state labels instead of
 * pills. Instructors can read the full content of an assessment, with the
 * correct answers, before and after giving it; students never see the key.
 * The instructor Submissions page lists every attempt with plain-text states.
 */
class Updates9AssessmentsPagesTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    private const PILL = '/class="[^"]*\b[\w-]*(pill|badge|chip|capsule)[\w-]*\b/i';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedAssessmentActors();
    }

    public function test_my_classes_lists_only_enrolled_classes_with_a_summary(): void
    {
        $this->assessment('Open Work', ['due_at' => now()->addDays(2)]);
        $this->assessment('Overdue Work', ['due_at' => now()->subDay()]);

        $notMine = $this->otherClass('Someone Else Class');

        $html = $this->actAs($this->student)
            ->get(route('student.assessments.index'))
            ->assertOk()
            ->assertSee('<title>Assessments — DataSensei</title>', false)
            ->assertSee('My Classes')
            ->assertSee('Assessment Workflow Class')
            ->assertSee('Workflow Instructor')
            ->assertSee(route('student.assessments.class', $this->classId), false)
            ->assertSee('1 available, 1 missing')
            ->assertDontSee('Someone Else Class')
            ->assertDontSee(route('student.assessments.class', $notMine), false)
            ->getContent();

        $this->assertDoesNotMatchRegularExpression(self::PILL, $this->main($html));
    }

    public function test_a_class_page_groups_work_like_an_lms(): void
    {
        $this->assessment('Open Work', ['due_at' => now()->addDays(2)]);
        $this->assessment('Opens Next Week', ['available_at' => now()->addWeek(), 'due_at' => now()->addWeeks(2)]);
        $this->assessment('Never Turned In', ['due_at' => now()->subDay()]);
        $late = $this->assessment('Turned In Late', ['due_at' => now()->subDays(2)]);
        $onTime = $this->assessment('Turned In On Time', ['due_at' => now()->addDay()]);
        // Drafts are the instructor's; they never reach students.
        $this->assessment('Hidden Draft', ['status' => 'draft']);

        $this->finished($late, 'late', now()->subDay());
        $this->finished($onTime, 'submitted', now()->subHour());

        $html = $this->actAs($this->student)
            ->get(route('student.assessments.class', $this->classId))
            ->assertOk()
            ->assertSee('<title>Assessment Workflow Class Assessments — DataSensei</title>', false)
            ->assertSee('Assessment Workflow Class')
            ->assertSeeInOrder(['Available (1)', 'Open Work', 'Upcoming (1)', 'Opens Next Week', 'Missing (1)', 'Never Turned In', 'Late (1)', 'Turned In Late', 'Submitted (1)', 'Turned In On Time'])
            ->assertSee('1 available, 1 upcoming, 1 missing, 1 late, 1 submitted.')
            ->assertDontSee('Hidden Draft')
            ->assertDontSee('No assessments yet')
            ->getContent();

        // Each state is a plain-text label, not a pill.
        $main = $this->main($html);
        foreach (['Not started', 'Not open yet', 'Missing', 'Late', 'Submitted'] as $label) {
            $this->assertStringContainsString($label, strip_tags($main), $label);
        }
        $this->assertMatchesRegularExpression('#<span class="state-bad">\s*Missing\s*</span>#', $main);
        $this->assertMatchesRegularExpression('#<span class="state-warn">\s*Late\s*</span>#', $main);
        // The page body (the shared sidebar keeps its unread counter).
        $this->assertDoesNotMatchRegularExpression(self::PILL, $main);
    }

    public function test_a_class_without_assessments_says_so(): void
    {
        $this->actAs($this->student)
            ->get(route('student.assessments.class', $this->classId))
            ->assertOk()
            ->assertSee('No assessments yet');

        $this->actAs($this->student)
            ->get(route('student.assessments.index'))
            ->assertOk()
            ->assertSee('No assessments yet');
    }

    public function test_a_class_the_student_is_not_enrolled_in_cannot_be_opened(): void
    {
        $notMine = $this->otherClass('Someone Else Class');
        DB::table('assessments')->insert([
            'class_id' => $notMine,
            'created_by' => $this->otherInstructor->id,
            'title' => 'Not For You',
            'status' => 'published',
            'total_items' => 0,
            'total_points' => 0,
            'max_attempts' => 1,
            'published_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actAs($this->student)
            ->get(route('student.assessments.class', $notMine))
            ->assertNotFound();
    }

    public function test_the_assessment_page_leads_back_to_its_class(): void
    {
        $id = $this->assessment('Open Work', ['due_at' => now()->addDays(2)]);
        $classId = (int) DB::table('assessments')->where('id', $id)->value('class_id');

        // Back returns to the class page the student opened it from, as the
        // Updates 9 assignment page did, not to the My Classes overview.
        $this->actAs($this->student)
            ->get(route('student.assessments.show', $id))
            ->assertOk()
            ->assertSee('Open Work')
            ->assertSee('href="'.route('student.assessments.class', $classId).'"', false);
    }

    public function test_instructors_read_the_full_content_before_giving_it(): void
    {
        $id = $this->assessment('Descriptive Statistics Check', ['status' => 'draft']);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.assessments.preview', $id))
            ->assertOk()
            ->assertSee('Descriptive Statistics Check')
            ->assertSee('Show the answers')
            ->assertSee('Which option is correct?')
            ->assertSee('Correct option')
            ->assertSee('Wrong option')
            ->assertSee('Correct answer')
            ->assertSee('Which library provides DataFrame?')
            ->assertSee('Accepted: pandas')
            ->assertSeeInOrder(['Questions', '2, 10 points'])
            ->getContent();

        $this->assertDoesNotMatchRegularExpression(self::PILL, $this->main($html));
    }

    public function test_instructors_read_the_full_content_after_giving_it(): void
    {
        $id = $this->assessment('Given Work', ['due_at' => now()->addDays(2)]);

        $this->actAs($this->instructor)
            ->get(route('instructor.assessments.preview', $id))
            ->assertOk()
            ->assertSee('Given Work')
            ->assertSee('Correct answer')
            ->assertSee('Accepted: pandas');

        $this->actAs($this->instructor)
            ->get(route('instructor.assessments.submissions', $id))
            ->assertOk()
            ->assertSee('Given Work');

        // Another instructor cannot read it.
        $this->actAs($this->otherInstructor)
            ->get(route('instructor.assessments.preview', $id))
            ->assertForbidden();
    }

    public function test_students_never_see_the_answer_key(): void
    {
        $id = $this->assessment('Given Work', ['due_at' => now()->addDays(2)]);

        $this->actAs($this->student)->get(route('instructor.assessments.preview', $id))->assertForbidden();
        $this->actAs($this->student)->get(route('instructor.assessments.builder', $id))->assertForbidden();

        $this->actAs($this->student)
            ->get(route('student.assessments.show', $id))
            ->assertOk()
            ->assertDontSee('Correct answer')
            ->assertDontSee('Accepted:')
            ->assertDontSee('pandas');
    }

    public function test_the_instructor_submissions_page_uses_plain_text_states(): void
    {
        $homework = $this->assessment('Week 2 Homework', ['purpose' => 'homework', 'due_at' => now()->subDay()]);
        $quiz = $this->assessment('Week 3 Quiz', ['purpose' => 'quiz', 'due_at' => now()->addDay()]);
        $exam = $this->assessment('Midterm Examination', ['purpose' => 'examination', 'due_at' => now()->addDays(2)]);

        $this->finished($homework, 'late', now()->subHours(2));
        $this->finished($quiz, 'graded', now()->subHour());
        AssessmentSubmission::create([
            'assessment_id' => $exam,
            'student_id' => $this->student->id,
            'attempt_no' => 1,
            'status' => 'submitted',
            'score' => 0,
            'provisional_score' => 5,
            'total_points' => 10,
            'integrity_status' => AssessmentSubmission::INTEGRITY_BLOCKED,
            'anti_cheat_session_id' => Str::random(64),
            'started_at' => now()->subMinutes(20),
            'submitted_at' => now()->subMinutes(10),
        ]);
        $this->makeAttempt($homework, $this->otherStudent);

        $html = $this->actAs($this->instructor)
            ->get(route('instructor.submissions.index'))
            ->assertOk()
            ->assertSee('<title>Submissions — DataSensei</title>', false)
            ->assertSee('Week 2 Homework (Homework)')
            ->assertSee('Week 3 Quiz (Quiz)')
            ->assertSee('Midterm Examination (Examination)')
            ->getContent();

        $main = $this->main($html);
        $this->assertMatchesRegularExpression('#<span class="state warn">\s*Held for review\s*</span>#', $main);
        $this->assertMatchesRegularExpression('#<span class="state bad">\s*Late\s*</span>#', $main);
        $this->assertMatchesRegularExpression('#<span class="state good">\s*Graded\s*</span>#', $main);
        $this->assertMatchesRegularExpression('#<span class="state good">\s*In Progress\s*</span>#', $main);
        $this->assertDoesNotMatchRegularExpression(self::PILL, $main);

        // Held attempts can be listed on their own.
        $this->actAs($this->instructor)
            ->get(route('instructor.submissions.index', ['status' => 'held']))
            ->assertOk()
            ->assertSee('Midterm Examination (Examination)')
            ->assertDontSee('Week 2 Homework (Homework)')
            ->assertDontSee('Week 3 Quiz (Quiz)');

        // Only the instructor's own classes, and never students.
        $this->actAs($this->otherInstructor)
            ->get(route('instructor.submissions.index'))
            ->assertOk()
            ->assertSee('No submissions found.')
            ->assertDontSee('Week 2 Homework');
        $this->actAs($this->student)->get(route('instructor.submissions.index'))->assertForbidden();
    }

    /** @param array<string, mixed> $overrides */
    private function assessment(string $title, array $overrides = []): int
    {
        return $this->makeAssessment(null, array_merge(['title' => $title], $overrides))['assessment'];
    }

    private function finished(int $assessmentId, string $status, Carbon $submittedAt): void
    {
        AssessmentSubmission::create([
            'assessment_id' => $assessmentId,
            'student_id' => $this->student->id,
            'attempt_no' => 1,
            'status' => $status,
            'score' => 5,
            'total_points' => 10,
            'anti_cheat_session_id' => Str::random(64),
            'started_at' => $submittedAt->copy()->subMinutes(5),
            'submitted_at' => $submittedAt,
            'graded_at' => $status === 'graded' ? $submittedAt : null,
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

    /** The page body (the shared sidebar keeps its unread counter). */
    private function main(string $html): string
    {
        return substr($html, (int) strpos($html, '<main'));
    }
}
