<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentSubmission;
use App\Models\TableOfSpecification;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuizMakerEndToEndTest extends TestCase
{
    private User $instructor;
    private User $student;
    private int $classId;
    private TableOfSpecification $tos;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->createTables();

        DB::table('institutions')->insert([
            'id' => 1,
            'name' => 'Quiz Test Institution',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->instructor = User::create([
            'name' => 'Quiz Instructor',
            'email' => 'quiz-instructor@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
            'institution_id' => 1,
        ]);
        $this->student = User::create([
            'name' => 'Quiz Student',
            'email' => 'quiz-student@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        $this->classId = DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'institution_id' => 1,
            'name' => 'Statistics 101',
            'class_code' => 'QUIZ101',
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('class_student')->insert([
            'class_id' => $this->classId,
            'student_id' => $this->student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->tos = TableOfSpecification::create([
            'class_id' => $this->classId,
            'module_no' => 1,
            'total_items' => 5,
            'title' => 'Descriptive Statistics TOS',
            'status' => 'draft',
            'cognitive_distribution' => [],
            'created_by' => $this->instructor->id,
        ]);
        DB::table('table_of_specification_rows')->insert([
            'table_of_specification_id' => $this->tos->id,
            'ilo_id' => null,
            'topic_title' => 'Descriptive Statistics',
            'subtopic_title' => 'Summary Measures',
            'learning_objective' => 'Choose and explain appropriate summary measures.',
            'difficulty_slug' => 'university-student',
            'cognitive_level' => 'Apply',
            'item_count' => 5,
            'default_points' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        foreach ([
            'student_assessment_diagnostics',
            'assessment_answers',
            'assessment_submissions',
            'assessment_question_options',
            'assessment_questions',
            'assessments',
            'table_of_specification_rows',
            'table_of_specifications',
            'class_student',
            'classes',
            'users',
            'institutions',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_instructor_can_publish_and_grade_a_quiz_that_a_student_completes(): void
    {
        $this->instructorClient()->post(
            route('instructor.assessments.store', $this->tos),
            [
                'class_id' => $this->classId,
                'title' => 'Complete Quiz Maker Check',
                'description' => 'A complete quiz workflow test.',
                'instructions' => 'Answer every item.',
                'max_attempts' => 1,
            ]
        )->assertRedirect();

        $assessment = Assessment::query()->firstOrFail();
        $questions = $assessment->questions()->get();
        $this->assertSame('draft', $assessment->status);
        $this->assertCount(5, $questions);

        $this->instructorClient()
            ->get(route('instructor.assessments.builder', $assessment))
            ->assertOk()
            ->assertSeeText('Item 1 of 5')
            ->assertSeeText('Descriptive Statistics');

        $definitions = [
            [
                'question_type' => 'multiple_choice',
                'question_text' => 'Which measure identifies the middle value?',
                'points' => 2,
                'option_texts' => ['Mean', 'Median', 'Mode', 'Range'],
                'correct_option' => 1,
                'answer_explanation' => 'The median is the middle value after the data is ordered.',
            ],
            [
                'question_type' => 'true_false',
                'question_text' => 'The median is resistant to extreme values.',
                'points' => 1,
                'correct_answer' => 'True',
                'answer_explanation' => 'Extreme values affect the median less than the mean.',
            ],
            [
                'question_type' => 'fill_blank',
                'question_text' => 'The Python library commonly imported as pd is _____.',
                'points' => 1,
                'correct_answer' => 'pandas',
            ],
            [
                'question_type' => 'short_answer',
                'question_text' => 'Name one common measure of central tendency.',
                'points' => 1,
                'correct_answer' => "mean\naverage",
            ],
            [
                'question_type' => 'essay',
                'question_text' => 'Explain when the median is preferable to the mean.',
                'points' => 3,
                'rubric_text' => 'Award full credit for explaining skew or outliers with an example.',
            ],
        ];

        foreach ($questions as $index => $question) {
            $this->instructorClient()->patch(
                route('instructor.assessments.questions.update', [$assessment, $question]),
                array_merge($definitions[$index], [
                    'intent' => 'complete_next',
                    'is_required' => 1,
                ])
            )->assertRedirect();
        }

        $this->instructorClient()
            ->patch(route('instructor.assessments.publish', $assessment))
            ->assertSessionHasNoErrors();

        $assessment->refresh();
        $questions = $assessment->questions()->with('options')->get();
        $this->assertSame('published', $assessment->status);
        $this->assertSame(5, $assessment->total_items);
        $this->assertSame(8, $assessment->total_points);

        $this->studentClient()
            ->get(route('student.assessments.show', $assessment))
            ->assertOk()
            ->assertSeeText('Start Assessment')
            ->assertSeeText('Attempts used: 0 of 1');

        $this->studentClient()
            ->post(route('student.assessments.start', $assessment))
            ->assertRedirect();

        $submission = AssessmentSubmission::query()->firstOrFail();
        $correctOption = $questions[0]->options->firstWhere('is_correct', true);

        $this->studentClient()
            ->get(route('student.assessments.take', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('Which measure identifies the middle value?')
            ->assertSeeText('Explain when the median is preferable to the mean.');

        $this->studentClient()->postJson(
            route('student.assessments.autosave', [$assessment, $submission]),
            [
                'client_version' => 1,
                'answers' => [
                    $questions[0]->id => (string) $correctOption->id,
                ],
            ]
        )->assertOk()->assertJson([
            'saved' => true,
            'current_version' => 1,
        ]);

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessment, $submission]),
            [
                'answers' => [
                    $questions[1]->id => 'true',
                    $questions[2]->id => 'PANDAS',
                    $questions[3]->id => 'average',
                    $questions[4]->id => 'The median is better for skewed data because outliers do not pull it strongly.',
                ],
            ]
        )->assertRedirect(route('student.assessments.result', [$assessment, $submission]));

        $submission->refresh();
        $this->assertSame('submitted', $submission->status);
        $this->assertSame('5.00', $submission->score);
        $this->assertDatabaseCount('assessment_answers', 5);
        $this->assertDatabaseHas('student_assessment_diagnostics', [
            'student_id' => $this->student->id,
            'assessment_id' => $assessment->id,
            'manual_review_pending' => 1,
        ]);

        $this->instructorClient()
            ->get(route('instructor.assessments.submissions', $assessment))
            ->assertOk()
            ->assertSeeText('Quiz Student');

        $this->instructorClient()
            ->get(route('instructor.assessments.submissions.show', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('Explain when the median is preferable to the mean.')
            ->assertSeeText('Score (max 3)')
            ->assertSeeText('Award full credit for explaining skew or outliers with an example.');

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('The median is the middle value after the data is ordered.')
            ->assertSeeText('waiting for instructor grading');

        $essayAnswer = AssessmentAnswer::query()
            ->where('assessment_submission_id', $submission->id)
            ->where('assessment_question_id', $questions[4]->id)
            ->firstOrFail();

        $this->instructorClient()->patch(
            route('instructor.assessments.submissions.grade', [$assessment, $submission]),
            [
                'scores' => [$essayAnswer->id => 3],
                'feedbacks' => [
                    $essayAnswer->id => 'Clear explanation of skew and outliers.',
                ],
                'feedback' => 'Excellent work across all question types.',
            ]
        )->assertSessionHasNoErrors();

        $submission->refresh();
        $this->assertSame('graded', $submission->status);
        $this->assertSame('8.00', $submission->score);
        $this->assertSame('Excellent work across all question types.', $submission->feedback);
        $this->assertDatabaseHas('student_assessment_diagnostics', [
            'student_id' => $this->student->id,
            'assessment_id' => $assessment->id,
            'mastery_percent' => 100,
            'manual_review_pending' => 0,
        ]);

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessment, $submission]))
            ->assertOk()
            ->assertSeeText('Overall Instructor Feedback')
            ->assertSeeText('Excellent work across all question types.')
            ->assertSeeText('Clear explanation of skew and outliers.')
            ->assertSeeText('The median is the middle value after the data is ordered.')
            ->assertSeeText('100%');

        $this->studentClient()
            ->get(route('student.assessments.show', $assessment))
            ->assertOk()
            ->assertDontSeeText('Start Another Attempt')
            ->assertSeeText('All 1 allowed attempt(s) have been used.');
    }

    private function instructorClient()
    {
        return $this->actingAs($this->instructor)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->instructor),
        ]);
    }

    private function studentClient()
    {
        return $this->actingAs($this->student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->student),
        ]);
    }

    private function createTables(): void
    {
        Schema::create('institutions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
            $table->timestamps();
        });
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('name');
            $table->string('class_code', 8);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
        Schema::create('class_student', function (Blueprint $table): void {
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();
        });
        Schema::create('table_of_specifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedInteger('module_no')->nullable();
            $table->string('custom_coverage')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->string('title');
            $table->string('status')->default('draft');
            $table->longText('cognitive_distribution')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
        Schema::create('table_of_specification_rows', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id');
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->string('topic_title');
            $table->string('subtopic_title')->nullable();
            $table->text('learning_objective')->nullable();
            $table->string('difficulty_slug')->nullable();
            $table->string('cognitive_level')->nullable();
            $table->unsignedInteger('item_count')->default(0);
            $table->unsignedInteger('default_points')->default(1);
            $table->timestamps();
        });
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('draft_last_item')->nullable();
            $table->timestamp('draft_saved_at')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('max_attempts')->default(1);
            $table->timestamp('available_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
        Schema::create('assessment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('table_of_specification_row_id')->nullable();
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->unsignedInteger('item_number');
            $table->string('question_type', 40)->default('unconfigured');
            $table->longText('question_text')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->boolean('is_required')->default(true);
            $table->boolean('authoring_touched')->default(false);
            $table->text('correct_answer')->nullable();
            $table->text('answer_explanation')->nullable();
            $table->longText('rubric_text')->nullable();
            $table->string('topic_title');
            $table->string('subtopic_title')->nullable();
            $table->text('learning_objective')->nullable();
            $table->string('bloom_level')->nullable();
            $table->string('difficulty_slug')->nullable();
            $table->timestamps();
        });
        Schema::create('assessment_question_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_question_id');
            $table->string('option_label', 10)->nullable();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('assessment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no')->default(1);
            $table->string('status', 30)->default('in_progress');
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->longText('draft_answers')->nullable();
            $table->unsignedBigInteger('draft_version')->default(0);
            $table->dateTime('draft_saved_at')->nullable();
            $table->dateTime('timed_out_at')->nullable();
            $table->timestamps();
        });
        Schema::create('assessment_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_submission_id');
            $table->unsignedBigInteger('assessment_question_id');
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->longText('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_awarded', 8, 2)->default(0);
            $table->text('instructor_feedback')->nullable();
            $table->timestamps();
            $table->unique(
                ['assessment_submission_id', 'assessment_question_id'],
                'assessment_submission_question_uq'
            );
        });
        Schema::create('student_assessment_diagnostics', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('table_of_specification_row_id')->nullable();
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->string('topic_title');
            $table->string('subtopic_title')->nullable();
            $table->text('learning_objective')->nullable();
            $table->string('bloom_level')->nullable();
            $table->string('difficulty_slug')->nullable();
            $table->unsignedInteger('item_count')->default(0);
            $table->unsignedInteger('answered_count')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->decimal('earned_points', 8, 2)->default(0);
            $table->decimal('possible_points', 8, 2)->default(0);
            $table->decimal('mastery_percent', 6, 2)->default(0);
            $table->string('proficiency_label', 40)->default('Not Assessed');
            $table->boolean('manual_review_pending')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->unique(
                ['student_id', 'assessment_id', 'table_of_specification_row_id'],
                'student_assessment_tos_diagnostic_uq'
            );
        });
    }
}
