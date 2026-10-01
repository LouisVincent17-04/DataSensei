<?php

namespace Tests\Feature\Regression;

use App\Services\Reports\GradebookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * Combined Certificate and Gradebook Requirements: the instructor class
 * gradebook (students in rows, the class's assessments in columns, a final
 * grade, class averages, completion, and each student's record before
 * certificates are issued) and the student gradebook (type, score,
 * percentage, status, due date, attempts, submission date, feedback and the
 * overall class grade).
 *
 * Fixture (BuildsReportData): in Data Science, Sam has Loops 8/10, Functions
 * 8/10 and Midterm 9/10 (final grade 83.3%); Lia has Loops 6/10 (late) and
 * Midterm 4/10 then 5/10 (final grade 55%); Tom has nothing. The Loops quiz
 * and coding challenges are practice given to the class: activities, never
 * part of the grade.
 */
class Updates13GradebookTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_the_instructor_grid_has_final_grades_averages_and_completion(): void
    {
        $page = $this->authenticateAs($this->ana)->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id]))->assertOk()->getContent();

        foreach (['Final grade', 'Completion', 'Average / Total', 'Loops Worksheet', 'Midterm Quiz', '83.3%', '55%', '7 of 8', '0 of 3 completed'] as $text) {
            $this->assertStringContainsString($text, $page, $text);
        }
        // Practice challenges are not grade columns.
        $this->assertStringNotContainsString('Loops Quiz Challenge</a>', $page);
        // The class average of the final grades: (83.3 + 55) / 2.
        $this->assertStringContainsString('69.2%', $page);
        $this->assertStringContainsString(route('instructor.certificates.create', ['class_id' => $this->dataScience->id]), $page);
        $this->assertStringNotContainsString('Zoe Student', $page);
    }

    public function test_the_instructor_reviews_one_students_whole_record(): void
    {
        $url = route('instructor.gradebook.index', ['class_id' => $this->dataScience->id, 'student_id' => $this->lia->id]);
        $page = $this->authenticateAs($this->ana)->get($url)->assertOk()->getContent();
        foreach (['Lia Student', 'Final grade', '55%', 'Attempt 1', 'Attempt 2', 'Graded (late)', 'Class completion', 'Not issued', 'Upcoming Worksheet', 'Python Basics (Version 1)'] as $text) {
            $this->assertStringContainsString($text, $page, $text);
        }
        $this->assertStringNotContainsString('Sam Student', $page);

        // Only students of the instructor's own class.
        $this->get(route('instructor.gradebook.index', ['class_id' => $this->dataScience->id, 'student_id' => $this->zoe->id]))->assertNotFound();
        $this->authenticateAs($this->ben)->get($url)->assertNotFound();
        $this->authenticateAs($this->lia)->get($url)->assertForbidden();
    }

    public function test_the_student_gradebook_shows_every_column_and_the_overall_grade(): void
    {
        DB::table('assessment_submissions')->where('assessment_id', $this->ids['a1'])->where('student_id', $this->sam->id)->update(['feedback' => 'Neat loops, Sam.']);

        $page = $this->authenticateAs($this->sam)->get(route('student.gradebook.index'))->assertOk()->getContent();
        foreach (['Type', 'Score', 'Percentage', 'Status', 'Due date', 'Attempts', 'Submitted', 'Feedback', 'Homework', '8 / 10', 'View feedback', 'Neat loops, Sam.', 'Overall grade', '25 / 30', '83.3%', 'In progress', 'Class completion', '7 of 8 required items completed', 'Upcoming Worksheet'] as $text) {
            $this->assertStringContainsString($text, $page, $text);
        }
        // Only Sam's own results, and only the class's assessments.
        $this->assertStringNotContainsString('Lia Student', $page);
        $this->assertStringNotContainsString('Other Class Worksheet', $page);
        $this->assertStringNotContainsString('Loops Quiz Challenge</strong>', $page);

        // Every assessment graded: the overall grade is final.
        DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->ids['a3'], 'student_id' => $this->sam->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 10, 'total_points' => 10,
            'started_at' => now(), 'submitted_at' => now(), 'graded_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $page = $this->get(route('student.gradebook.index'))->getContent();
        $this->assertStringContainsString('35 / 40', $page);
        $this->assertStringContainsString('87.5%', $page);
        $this->assertStringContainsString('all required work completed', $page);
        $this->assertMatchesRegularExpression('#Overall grade.*?Final#s', $page);
    }

    public function test_the_overall_grade_rule(): void
    {
        $rows = [
            ['state' => 'graded', 'score' => '18', 'total' => '20', 'percent' => 90.0],
            ['state' => 'graded', 'score' => '17', 'total' => '20', 'percent' => 85.0],
            ['state' => 'missing', 'score' => null, 'total' => '25', 'percent' => null],
        ];
        $overall = GradebookService::overall($rows);
        $this->assertSame(87.5, $overall['percent']);
        $this->assertSame(['35', '40', 2, 3, false, 'In progress'], [$overall['earned'], $overall['possible'], $overall['graded'], $overall['total'], $overall['final'], $overall['status']]);
        $this->assertNull(GradebookService::overall([])['percent']);
    }
}
