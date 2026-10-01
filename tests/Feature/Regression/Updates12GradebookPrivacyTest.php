<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 12, part 1 and 2: the student's own gradebook and the
 * instructor's class gradebook. Privacy is enforced by the server: a student
 * only ever gets their own grades, whatever the URL says, and an instructor
 * only their own classes.
 *
 * Fixture (BuildsReportData): Ana teaches Data Science (Sam, Lia, Tom), Ben
 * teaches Statistics (Zoe). Sam: Loops 8/10, Functions 8/10, Midterm 9/10.
 * Lia: Loops 6/10 late, Midterm 4/10 then 5/10.
 */
class Updates12GradebookPrivacyTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_a_student_sees_only_their_own_grades(): void
    {
        $this->feedback($this->sam, $this->ids['a1'], 'Neat loops, Sam.');
        $this->feedback($this->lia, $this->ids['a1'], 'Lia, review range().');

        $html = $this->authenticateAs($this->sam)->get(route('student.gradebook.index'))->assertOk()->getContent();
        $main = substr($html, (int) strpos($html, '<main'));

        $this->assertStringContainsString('My Gradebook', $html);
        $this->assertStringContainsString('Loops Worksheet', $main);
        $this->assertStringContainsString('8 / 10', $main);
        $this->assertStringContainsString('9 / 10', $main);
        $this->assertStringContainsString('Neat loops, Sam.', $main);
        // Lia's grades, feedback and name never appear, nor Ben's class.
        $this->assertStringNotContainsString('6 / 10', $main);
        $this->assertStringNotContainsString('Lia, review range().', $main);
        $this->assertStringNotContainsString('Lia Student', $main);
        $this->assertStringNotContainsString('Other Class Worksheet', $main);
        $this->assertStringNotContainsString('Statistics', $main);
    }

    public function test_changing_the_url_never_reveals_another_students_grades(): void
    {
        $this->authenticateAs($this->sam);

        // Someone else's class: not found, not even a hint it exists.
        $this->get(route('student.gradebook.index', ['class_id' => $this->statistics->id]))->assertNotFound();

        // A student id in the URL is ignored: still Sam's own gradebook.
        foreach (['student_id', 'user_id', 'id', 'student'] as $parameter) {
            $main = $this->get(route('student.gradebook.index', ['class_id' => $this->dataScience->id, $parameter => $this->lia->id]))
                ->assertOk()->getContent();
            $this->assertStringContainsString('8 / 10', $main, $parameter);
            $this->assertStringNotContainsString('6 / 10', substr($main, (int) strpos($main, '<main')), $parameter);
        }

        // Every result link on the page is one of Sam's own attempts.
        $html = $this->get(route('student.gradebook.index'))->getContent();
        preg_match_all('#/student/assessments/(\d+)/attempt/(\d+)/result#', $html, $links, PREG_SET_ORDER);
        $this->assertNotEmpty($links);
        foreach ($links as [, , $submissionId]) {
            $this->assertSame($this->sam->id, (int) DB::table('assessment_submissions')->where('id', $submissionId)->value('student_id'));
        }

        // Opening Lia's result directly is refused by the result page.
        $lias = DB::table('assessment_submissions')->where('student_id', $this->lia->id)->where('assessment_id', $this->ids['a1'])->first();
        $this->get(route('student.assessments.result', [$this->ids['a1'], $lias->id]))->assertForbidden();
        $this->get(route('student.submissions.show', $lias->id))->assertForbidden();

        // An instructor cannot open a submission of another instructor's class.
        $zoes = DB::table('assessment_submissions')->where('student_id', $this->zoe->id)->first();
        $status = $this->authenticateAs($this->ana)->get(route('instructor.assessments.submissions.show', [$this->ids['a_other'], $zoes->id]))->status();
        $this->assertContains($status, [403, 404]);
    }

    public function test_held_and_ungraded_attempts_are_shown_but_not_counted(): void
    {
        // Lia turns in Functions, but the attempt is held for an integrity review.
        DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->ids['a2'], 'student_id' => $this->lia->id, 'attempt_no' => 1, 'status' => 'submitted',
            'score' => 0, 'provisional_score' => 10, 'total_points' => 10, 'integrity_status' => 'review_required',
            'started_at' => now(), 'submitted_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $main = $this->authenticateAs($this->lia)->get(route('student.gradebook.index'))->assertOk()->getContent();
        $this->assertStringContainsString('Held for integrity review', $main);
        // The overall grade averages graded work only: Loops 60%, Midterm 50%.
        $this->assertMatchesRegularExpression('#Overall grade.*?<strong>55%</strong>#s', $main);
    }

    public function test_an_instructor_sees_only_the_students_of_their_own_classes(): void
    {
        $this->feedback($this->sam, $this->ids['a1'], 'Neat loops, Sam.');

        $this->authenticateAs($this->ana);
        $grid = $this->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id]))->assertOk()->getContent();
        foreach (['Sam Student', 'Lia Student', 'Tom Student', 'Loops Worksheet', 'Midterm Quiz', '80%', '60%', 'Missing'] as $text) {
            $this->assertStringContainsString($text, $grid, $text);
        }
        $this->assertStringNotContainsString('Zoe', $grid);
        $this->assertStringNotContainsString('Other Class Worksheet', $grid);

        // One assessment: score, percentage, state and feedback per student.
        $detail = $this->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id, 'assessment_id' => $this->ids['a1']]))->assertOk()->getContent();
        $this->assertStringContainsString('Neat loops, Sam.', $detail);
        $this->assertStringContainsString('Graded (late)', $detail);
        $this->assertStringContainsString('Not passed', $detail);

        // Other classes, by any id: not found.
        $this->get(route('instructor.gradebook.index', ['class_id' => $this->statistics->id]))->assertNotFound();
        $this->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id, 'assessment_id' => $this->ids['a_other']]))->assertNotFound();

        $this->authenticateAs($this->ben)
            ->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id]))
            ->assertNotFound();
    }

    public function test_each_role_only_reaches_its_own_gradebook(): void
    {
        $this->assertContains($this->authenticateAs($this->sam)->get(route('instructor.gradebook.index'))->status(), [302, 403]);
        $this->assertContains($this->authenticateAs($this->ana)->get(route('student.gradebook.index'))->status(), [302, 403]);

        $sidebar = $this->authenticateAs($this->ana)->get(route('instructor.gradebook.index'))->getContent();
        $this->assertStringContainsString(route('instructor.gradebook.index'), $sidebar);
        $student = $this->authenticateAs($this->sam)->get(route('student.gradebook.index'))->getContent();
        $this->assertStringContainsString(route('student.gradebook.index'), $student);
    }

    private function feedback(User $student, int $assessmentId, string $text): void
    {
        DB::table('assessment_submissions')
            ->where('student_id', $student->id)
            ->where('assessment_id', $assessmentId)
            ->update(['feedback' => $text]);
    }
}
