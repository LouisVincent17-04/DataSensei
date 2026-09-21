<?php

namespace Tests\Feature;

use App\Models\AssessmentSubmission;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Services\AssessmentDiagnosticService;
use App\Services\GamificationService;
use App\Services\IloMasteryService;
use App\Services\StudentNotificationService;
use App\Support\AuthSessionFingerprint;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class TimedSubmissionAnswerPreservationTest extends TestCase
{
    private User $student;
    private int $classId;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->createTables();

        $this->student = User::create([
            'name' => 'Timed Learner',
            'email' => 'timed-learner@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->classId = DB::table('classes')->insertGetId([
            'name' => 'Timed Activities',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('class_student')->insert([
            'class_id' => $this->classId,
            'student_id' => $this->student->id,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        foreach ([
            'anti_cheat_settings',
            'assignment_submission_answers',
            'assignment_submissions',
            'assignment_blank_answers',
            'assignment_question_options',
            'assignment_questions',
            'class_assignments',
            'assignment_library_items',
            'assessment_answers',
            'assessment_submissions',
            'assessment_question_options',
            'assessment_questions',
            'assessments',
            'class_student',
            'classes',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    /**
     * DS-01 (assessment): rewritten. The previous version of this test posted the
     * correct answer AFTER the server deadline with no saved draft and expected
     * full marks, which encoded the bug. Answers now freeze at the deadline.
     */
    public function test_timed_out_assessment_grades_the_draft_saved_before_the_deadline_and_ignores_late_answers(): void
    {
        $deadline = Carbon::parse('2026-08-30 12:00:00');
        Carbon::setTestNow($deadline->copy()->subMinute());
        [$assessmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssessmentAttempt($deadline->copy()->subMinutes(5));
        $wrongOptionId = (int) DB::table('assessment_question_options')
            ->where('assessment_question_id', $questionId)
            ->where('is_correct', false)
            ->value('id');

        $diagnostics = Mockery::mock(AssessmentDiagnosticService::class);
        $diagnostics->shouldReceive('refresh')->once();
        $mastery = Mockery::mock(IloMasteryService::class);
        $mastery->shouldReceive('refreshForAssessmentSubmission')->once();
        $notifications = Mockery::mock(StudentNotificationService::class);
        $notifications->shouldReceive('send')->once()->andReturnNull();
        $this->app->instance(AssessmentDiagnosticService::class, $diagnostics);
        $this->app->instance(IloMasteryService::class, $mastery);
        $this->app->instance(StudentNotificationService::class, $notifications);

        // A wrong answer reaches the server one minute before the deadline.
        $this->studentClient()->post(
            route('student.assessments.autosave', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $wrongOptionId], 'client_version' => 1]
        )->assertOk()->assertJson(['saved' => true]);

        // After the deadline the student posts the correct answer instead.
        Carbon::setTestNow($deadline->copy()->addSeconds(30));
        $this->studentClient()->post(
            route('student.assessments.submit', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId]]
        )->assertRedirect(route('student.assessments.result', [$assessmentId, $submissionId]));

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $submissionId,
            'assessment_question_id' => $questionId,
            'selected_option_id' => $wrongOptionId,
            'is_correct' => 0,
            'points_awarded' => 0,
        ]);
        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame('graded', $submission->status);
        $this->assertSame('0.00', $submission->score);
        $this->assertNotNull($submission->timed_out_at);
        $this->assertSame((string) $wrongOptionId, $submission->draft_answers[(string) $questionId]);

        // Repeating the request is a no-op: the mocks above allow exactly one
        // diagnostics refresh, mastery refresh and notification.
        $this->studentClient()->post(
            route('student.assessments.submit', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId]]
        )->assertRedirect();

        $this->assertSame(1, DB::table('assessment_answers')->count());
        $this->assertSame('0.00', AssessmentSubmission::findOrFail($submissionId)->score);
    }

    public function test_timed_out_assessment_keeps_the_score_of_a_correct_draft_when_a_late_replacement_arrives(): void
    {
        $deadline = Carbon::parse('2026-08-30 12:00:00');
        Carbon::setTestNow($deadline->copy()->subMinute());
        [$assessmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssessmentAttempt($deadline->copy()->subMinutes(5));
        $wrongOptionId = (int) DB::table('assessment_question_options')
            ->where('assessment_question_id', $questionId)
            ->where('is_correct', false)
            ->value('id');
        $this->bindQuietAssessmentServices();

        $this->studentClient()->post(
            route('student.assessments.autosave', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId], 'client_version' => 1]
        )->assertOk()->assertJson(['saved' => true]);

        Carbon::setTestNow($deadline);
        $this->studentClient()->post(
            route('student.assessments.submit', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $wrongOptionId]]
        )->assertRedirect(route('student.assessments.result', [$assessmentId, $submissionId]));

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $submissionId,
            'selected_option_id' => $correctOptionId,
            'is_correct' => 1,
            'points_awarded' => 5,
        ]);
        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame('5.00', $submission->score);
        $this->assertNotNull($submission->timed_out_at);
        $this->assertSame((string) $correctOptionId, $submission->draft_answers[(string) $questionId]);
    }

    public function test_timed_out_assessment_without_a_saved_draft_is_finalized_as_unanswered(): void
    {
        $now = Carbon::parse('2026-08-30 12:00:00');
        Carbon::setTestNow($now);
        [$assessmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssessmentAttempt($now->copy()->subMinutes(5));
        $this->bindQuietAssessmentServices();

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId]]
        )
            ->assertRedirect(route('student.assessments.result', [$assessmentId, $submissionId]))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_submission_id' => $submissionId,
            'assessment_question_id' => $questionId,
            'selected_option_id' => null,
            'is_correct' => 0,
            'points_awarded' => 0,
        ]);
        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame('graded', $submission->status);
        $this->assertSame('0.00', $submission->score);
        $this->assertNotNull($submission->timed_out_at);
        $this->assertSame([], $submission->draft_answers);

        $this->studentClient()
            ->get(route('student.assessments.result', [$assessmentId, $submissionId]))
            ->assertOk()
            ->assertSeeText('No answer');
    }

    public function test_assessment_answers_posted_before_the_deadline_still_override_the_draft(): void
    {
        $deadline = Carbon::parse('2026-08-30 12:00:00');
        Carbon::setTestNow($deadline->copy()->subSeconds(2));
        [$assessmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssessmentAttempt($deadline->copy()->subMinutes(5));
        $this->bindQuietAssessmentServices();

        $this->studentClient()->post(
            route('student.assessments.submit', [$assessmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId]]
        )->assertRedirect(route('student.assessments.result', [$assessmentId, $submissionId]));

        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame('5.00', $submission->score);
        $this->assertNull($submission->timed_out_at);
    }

    /**
     * DS-01 (assignment): rewritten. The previous version,
     * test_timed_out_assignment_grades_received_answers_instead_of_emptying_them,
     * posted the correct answer AFTER the server deadline with no saved draft
     * and expected full marks, which encoded the bug. Answers now freeze at the
     * deadline: the draft saved before expiry is graded and keeps its score.
     */
    public function test_timed_out_assignment_grades_the_draft_saved_before_the_deadline_and_ignores_late_answers(): void
    {
        $deadline = Carbon::parse('2026-08-30 13:00:00');
        Carbon::setTestNow($deadline->copy()->subMinute());
        [$assignmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssignmentAttempt($deadline->copy()->subMinutes(5));
        $wrongOptionId = (int) DB::table('assignment_question_options')
            ->where('assignment_question_id', $questionId)
            ->where('is_correct', false)
            ->value('id');

        $mastery = Mockery::mock(IloMasteryService::class);
        $mastery->shouldReceive('refreshForAssignmentSubmission')->once();
        $gamification = Mockery::mock(GamificationService::class);
        $gamification->shouldReceive('awardForAssignmentSubmission')->once()->andReturn([]);
        $notifications = Mockery::mock(StudentNotificationService::class);
        $notifications->shouldReceive('send')->once()->andReturnNull();
        $this->app->instance(IloMasteryService::class, $mastery);
        $this->app->instance(GamificationService::class, $gamification);
        $this->app->instance(StudentNotificationService::class, $notifications);

        // The correct answer reaches the server one minute before the deadline.
        $this->studentClient()->post(
            route('student.assignments.autosave', [$assignmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId], 'client_version' => 1]
        )->assertOk()->assertJson(['saved' => true]);

        // After the deadline a replacement arrives; it must change nothing.
        Carbon::setTestNow($deadline->copy()->addSeconds(30));
        $this->studentClient()->post(
            route('student.assignments.submit', [$assignmentId, $submissionId]),
            ['answers' => [$questionId => (string) $wrongOptionId]]
        )->assertRedirect(route('student.assignments.result', [$assignmentId, $submissionId]));

        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $submissionId,
            'assignment_question_id' => $questionId,
            'selected_option_id' => $correctOptionId,
            'is_correct' => 1,
            'points_awarded' => 5,
        ]);
        $submission = AssignmentSubmission::findOrFail($submissionId);
        $this->assertSame(5, $submission->score);
        $this->assertSame('graded', $submission->status);
        $this->assertNotNull($submission->timed_out_at);
        $this->assertSame((string) $correctOptionId, $submission->draft_answers[(string) $questionId]);

        // Repeating the request is inert: the mocks above allow exactly one
        // mastery refresh, reward call and notification.
        $this->studentClient()->post(
            route('student.assignments.submit', [$assignmentId, $submissionId]),
            ['answers' => [$questionId => (string) $wrongOptionId]]
        );
        $this->assertSame(1, DB::table('assignment_submission_answers')->count());
        $this->assertSame(5, AssignmentSubmission::findOrFail($submissionId)->score);
    }

    public function test_timed_out_assignment_with_late_answers_and_no_draft_is_unanswered_not_an_error(): void
    {
        $now = Carbon::parse('2026-08-30 13:00:00');
        Carbon::setTestNow($now);
        [$assignmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssignmentAttempt($now->copy()->subMinutes(5));

        $mastery = Mockery::mock(IloMasteryService::class);
        $mastery->shouldReceive('refreshForAssignmentSubmission')->once();
        $gamification = Mockery::mock(GamificationService::class);
        $gamification->shouldReceive('awardForAssignmentSubmission')->once()->andReturn([]);
        $notifications = Mockery::mock(StudentNotificationService::class);
        $notifications->shouldReceive('send')->once()->andReturnNull();
        $this->app->instance(IloMasteryService::class, $mastery);
        $this->app->instance(GamificationService::class, $gamification);
        $this->app->instance(StudentNotificationService::class, $notifications);

        $this->studentClient()->post(
            route('student.assignments.submit', [$assignmentId, $submissionId]),
            ['answers' => [$questionId => (string) $correctOptionId]]
        )->assertRedirect(route('student.assignments.result', [$assignmentId, $submissionId]));

        $this->assertDatabaseHas('assignment_submission_answers', [
            'assignment_submission_id' => $submissionId,
            'assignment_question_id' => $questionId,
            'selected_option_id' => null,
            'is_correct' => 0,
            'points_awarded' => 0,
        ]);
        $submission = AssignmentSubmission::findOrFail($submissionId);
        $this->assertSame(0, $submission->score);
        $this->assertSame('graded', $submission->status);
        $this->assertNotNull($submission->timed_out_at);
    }

    public function test_autosave_is_monotonic_and_rejects_new_snapshots_at_the_deadline(): void
    {
        $deadline = Carbon::parse('2026-08-30 14:00:00');
        Carbon::setTestNow($deadline->copy()->subSecond());
        [$assessmentId, $questionId, $correctOptionId, $submissionId] =
            $this->createAssessmentAttempt($deadline->copy()->subMinutes(5));

        $route = route('student.assessments.autosave', [$assessmentId, $submissionId]);
        $this->studentClient()->post($route, [
            'answers' => [$questionId => (string) $correctOptionId],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => true, 'current_version' => 1]);

        $this->studentClient()->post($route, [
            'answers' => [$questionId => '999999'],
            'client_version' => 1,
        ])->assertOk()->assertJson(['saved' => false, 'stale' => true]);

        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame((string) $correctOptionId, $submission->draft_answers[(string) $questionId]);

        Carbon::setTestNow($deadline);
        $this->studentClient()->post($route, [
            'answers' => [$questionId => '999999'],
            'client_version' => 2,
        ])->assertStatus(409)->assertJson(['saved' => false, 'expired' => true]);

        $submission = AssessmentSubmission::findOrFail($submissionId);
        $this->assertSame(1, $submission->draft_version);
        $this->assertSame((string) $correctOptionId, $submission->draft_answers[(string) $questionId]);
    }

    private function bindQuietAssessmentServices(): void
    {
        $diagnostics = Mockery::mock(AssessmentDiagnosticService::class);
        $diagnostics->shouldReceive('refresh');
        $mastery = Mockery::mock(IloMasteryService::class);
        $mastery->shouldReceive('refreshForAssessmentSubmission');
        $notifications = Mockery::mock(StudentNotificationService::class);
        $notifications->shouldReceive('send')->andReturnNull();
        $this->app->instance(AssessmentDiagnosticService::class, $diagnostics);
        $this->app->instance(IloMasteryService::class, $mastery);
        $this->app->instance(StudentNotificationService::class, $notifications);
    }

    private function studentClient()
    {
        return $this->actingAs($this->student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->student),
        ]);
    }

    private function createAssessmentAttempt(Carbon $startedAt): array
    {
        $assessmentId = DB::table('assessments')->insertGetId([
            'class_id' => $this->classId,
            'title' => 'Timed TOS Quiz',
            'status' => 'published',
            'total_items' => 1,
            'total_points' => 5,
            'time_limit_minutes' => 5,
            'max_attempts' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $questionId = DB::table('assessment_questions')->insertGetId([
            'assessment_id' => $assessmentId,
            'item_number' => 1,
            'question_type' => 'multiple_choice',
            'question_text' => 'Which answer is correct?',
            'points' => 5,
            'is_required' => true,
            'topic_title' => 'Testing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $correctOptionId = DB::table('assessment_question_options')->insertGetId([
            'assessment_question_id' => $questionId,
            'option_label' => 'A',
            'option_text' => 'Correct',
            'is_correct' => true,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('assessment_question_options')->insert([
            'assessment_question_id' => $questionId,
            'option_label' => 'B',
            'option_text' => 'Incorrect',
            'is_correct' => false,
            'order_index' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $submissionId = DB::table('assessment_submissions')->insertGetId([
            'assessment_id' => $assessmentId,
            'student_id' => $this->student->id,
            'attempt_no' => 1,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 5,
            'started_at' => $startedAt,
            'draft_version' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$assessmentId, $questionId, $correctOptionId, $submissionId];
    }

    private function createAssignmentAttempt(Carbon $startedAt): array
    {
        $libraryItemId = DB::table('assignment_library_items')->insertGetId([
            'title' => 'Timed Assignment',
            'time_limit_minutes' => 5,
            'total_points' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $assignmentId = DB::table('class_assignments')->insertGetId([
            'class_id' => $this->classId,
            'assignment_library_item_id' => $libraryItemId,
            'title' => 'Timed Assignment',
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $questionId = DB::table('assignment_questions')->insertGetId([
            'assignment_library_item_id' => $libraryItemId,
            'question_type' => 'mcq',
            'question_text' => 'Which answer is correct?',
            'points' => 5,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $correctOptionId = DB::table('assignment_question_options')->insertGetId([
            'assignment_question_id' => $questionId,
            'option_text' => 'Correct',
            'is_correct' => true,
            'order_index' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('assignment_question_options')->insert([
            'assignment_question_id' => $questionId,
            'option_text' => 'Incorrect',
            'is_correct' => false,
            'order_index' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $submissionId = DB::table('assignment_submissions')->insertGetId([
            'class_assignment_id' => $assignmentId,
            'student_id' => $this->student->id,
            'attempt_no' => 1,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => 5,
            'started_at' => $startedAt,
            'anti_cheat_session_id' => 'test-session',
            'draft_version' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$assignmentId, $questionId, $correctOptionId, $submissionId];
    }

    private function createTables(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->string('status')->default('active');
            $table->unsignedInteger('xp')->default(0);
            $table->unsignedInteger('streak')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('class_student', function (Blueprint $table): void {
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
        });
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->string('title');
            $table->string('status', 30);
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('max_attempts')->default(1);
            $table->dateTime('available_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });
        Schema::create('assessment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedInteger('item_number');
            $table->string('question_type', 40);
            $table->longText('question_text');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('points');
            $table->boolean('is_required')->default(true);
            $table->text('correct_answer')->nullable();
            $table->string('topic_title');
            $table->timestamps();
        });
        Schema::create('assessment_question_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_question_id');
            $table->string('option_label', 10)->nullable();
            $table->text('option_text');
            $table->boolean('is_correct');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('assessment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 30);
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('graded_at')->nullable();
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
            $table->unique(['assessment_submission_id', 'assessment_question_id']);
        });
        Schema::create('assignment_library_items', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('time_limit_minutes')->nullable();
            $table->unsignedInteger('total_points')->default(0);
            $table->timestamps();
        });
        Schema::create('class_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('assignment_library_item_id');
            $table->string('title');
            $table->string('status', 30);
            $table->dateTime('available_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->timestamps();
        });
        Schema::create('assignment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_library_item_id');
            $table->string('question_type', 40);
            $table->longText('question_text');
            $table->unsignedInteger('points');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('assignment_question_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_question_id');
            $table->text('option_text');
            $table->boolean('is_correct');
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('assignment_blank_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_question_id');
            $table->text('answer_text');
            $table->boolean('is_case_sensitive')->default(false);
            $table->timestamps();
        });
        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 30);
            $table->unsignedInteger('score')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->text('feedback')->nullable();
            $table->string('anti_cheat_session_id', 120)->nullable();
            $table->longText('draft_answers')->nullable();
            $table->unsignedBigInteger('draft_version')->default(0);
            $table->dateTime('draft_saved_at')->nullable();
            $table->dateTime('timed_out_at')->nullable();
            // 2026_09_20_000001: integrity outcome + one-time reward marker.
            $table->string('integrity_status', 30)->nullable();
            $table->string('integrity_reason', 255)->nullable();
            $table->unsignedInteger('provisional_score')->nullable();
            $table->unsignedBigInteger('integrity_reviewed_by')->nullable();
            $table->dateTime('integrity_reviewed_at')->nullable();
            $table->dateTime('rewards_awarded_at')->nullable();
            $table->timestamps();
        });
        // The integrity decision is evaluated on every assignment submit, also
        // after expiry (DS-02), so the policy table must exist. It stays empty:
        // no instructor policy protects these attempts.
        Schema::create('anti_cheat_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('assessment_type')->default('assignment');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('assignment_submission_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_submission_id');
            $table->unsignedBigInteger('assignment_question_id');
            $table->unsignedBigInteger('selected_option_id')->nullable();
            $table->longText('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->unsignedInteger('points_awarded')->default(0);
            $table->timestamps();
            $table->unique(['assignment_submission_id', 'assignment_question_id']);
        });
    }
}
