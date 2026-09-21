<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentSubmission;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hand-built schema + fixtures for the TOS assessment workflow regression
 * tests (DS-01 assessment part, DS-08, DS-09). Mirrors the columns the real
 * migrations create; the default suite does not run migrations.
 */
trait BuildsAssessmentWorkflow
{
    protected User $instructor;
    protected User $student;
    protected User $otherStudent;
    protected int $classId;

    /** @var array<int, string> */
    private array $assessmentWorkflowTables = [
        'notifications',
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
    ];

    protected function bootAssessmentWorkflow(): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->createAssessmentWorkflowTables();
        $this->seedAssessmentWorkflowActors();
    }

    protected function seedAssessmentWorkflowActors(): void
    {
        DB::table('institutions')->insert([
            'id' => 1,
            'name' => 'Regression Institution',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $suffix = substr(md5(static::class), 0, 8);
        $this->instructor = User::create([
            'name' => 'Regression Instructor',
            'email' => "reg-instructor-{$suffix}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
            'institution_id' => 1,
        ]);
        $this->student = User::create([
            'name' => 'Regression Student',
            'email' => "reg-student-{$suffix}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->otherStudent = User::create([
            'name' => 'Other Student',
            'email' => "reg-other-{$suffix}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        $this->classId = DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'institution_id' => 1,
            'name' => 'Regression Class',
            'class_code' => 'REG' . strtoupper(substr($suffix, 0, 5)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ([$this->student, $this->otherStudent] as $user) {
            DB::table('class_student')->insert([
                'class_id' => $this->classId,
                'student_id' => $user->id,
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    protected function dropAssessmentWorkflowTables(): void
    {
        foreach ($this->assessmentWorkflowTables as $table) {
            Schema::dropIfExists($table);
        }
    }

    protected function instructorClient()
    {
        return $this->clientFor($this->instructor);
    }

    protected function studentClient()
    {
        return $this->clientFor($this->student);
    }

    protected function clientFor(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }

    /**
     * @param array<string, mixed> $attributes
     * @param array<int, array<string, mixed>> $questions each may carry an `options` list of [text, is_correct]
     * @return array{0: Assessment, 1: array<int, AssessmentQuestion>}
     */
    protected function makeAssessment(array $attributes, array $questions): array
    {
        $assessment = Assessment::create(array_merge([
            'class_id' => $this->classId,
            'created_by' => $this->instructor->id,
            'title' => 'Regression Assessment',
            'status' => 'published',
            'published_at' => now(),
            'total_items' => count($questions),
            'total_points' => array_sum(array_map(fn ($q) => (int) ($q['points'] ?? 1), $questions)),
            'max_attempts' => 1,
        ], $attributes));

        $created = [];
        foreach (array_values($questions) as $index => $definition) {
            $options = $definition['options'] ?? [];
            unset($definition['options']);

            $question = AssessmentQuestion::create(array_merge([
                'assessment_id' => $assessment->id,
                'item_number' => $index + 1,
                'question_text' => 'Question ' . ($index + 1),
                'points' => 1,
                'is_required' => false,
                'authoring_touched' => true,
                'topic_title' => 'Regression',
            ], $definition));

            foreach (array_values($options) as $optionIndex => [$text, $isCorrect]) {
                AssessmentQuestionOption::create([
                    'assessment_question_id' => $question->id,
                    'option_label' => chr(65 + $optionIndex),
                    'option_text' => $text,
                    'is_correct' => $isCorrect,
                    'order_index' => $optionIndex + 1,
                ]);
            }

            $created[] = $question->fresh('options');
        }

        return [$assessment, $created];
    }

    protected function startAttempt(Assessment $assessment, ?User $student = null, $startedAt = null, int $attemptNo = 1): AssessmentSubmission
    {
        return AssessmentSubmission::create([
            'assessment_id' => $assessment->id,
            'student_id' => ($student ?? $this->student)->id,
            'attempt_no' => $attemptNo,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => $assessment->total_points,
            'started_at' => $startedAt ?? now(),
        ]);
    }

    protected function createAssessmentWorkflowTables(): void
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
            $table->unsignedInteger('xp')->default(0);
            $table->unsignedInteger('streak')->default(0);
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
        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 191)->nullable();
            $table->string('dedupe_key', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->text('notification_text')->nullable();
            $table->string('action_url')->nullable();
            $table->longText('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
}
