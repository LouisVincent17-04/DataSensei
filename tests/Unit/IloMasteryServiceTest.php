<?php

namespace Tests\Unit;

use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Services\IloMasteryService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IloMasteryServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role')->default(User::ROLE_USER);
            $table->string('status')->nullable()->default('active');
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('assignment_library_items', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('module_no');
            $table->unsignedInteger('time_limit_minutes')->default(20);
            $table->timestamps();
        });
        Schema::create('class_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('assignment_library_item_id');
            $table->timestamps();
        });
        Schema::create('assignment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_library_item_id');
            $table->string('question_type', 20)->default('mcq');
            $table->unsignedInteger('points')->default(1);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 20);
            $table->timestamps();
        });
        Schema::create('assignment_submission_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_submission_id');
            $table->unsignedBigInteger('assignment_question_id');
            $table->decimal('points_awarded', 8, 2)->default(0);
        });
        Schema::create('intended_learning_outcomes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('module_no');
            $table->string('ilo_code', 80);
            $table->string('title');
            $table->unsignedTinyInteger('mastery_threshold')->default(75);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('assessment_question_ilos', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('ilo_id');
            $table->string('assessment_source', 40);
            $table->unsignedBigInteger('question_id');
            $table->unsignedTinyInteger('weight')->default(1);
            $table->timestamps();
        });
        Schema::create('student_ilo_masteries', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('ilo_id');
            $table->decimal('mastery_percent', 6, 2)->default(0);
            $table->unsignedInteger('evidence_count')->default(0);
            $table->string('status', 30);
            $table->dateTime('last_evaluated_at')->nullable();
            $table->timestamps();
        });

        // Manual-assessment tables are queried by the combined mastery service
        // even when this test contains only assignment evidence.
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
        });
        Schema::create('assessment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 30);
            $table->dateTime('graded_at')->nullable();
        });
        Schema::create('assessment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->unsignedInteger('points')->default(1);
        });
        Schema::create('assessment_answers', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_submission_id');
            $table->unsignedBigInteger('assessment_question_id');
            $table->decimal('points_awarded', 8, 2)->default(0);
        });
    }

    protected function tearDown(): void
    {
        foreach ([
            'assessment_answers', 'assessment_questions', 'assessment_submissions', 'assessments',
            'student_ilo_masteries', 'assessment_question_ilos', 'intended_learning_outcomes',
            'assignment_submission_answers', 'assignment_submissions', 'assignment_questions',
            'class_assignments', 'assignment_library_items', 'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_only_explicit_mapping_and_latest_attempt_count_as_mastery_evidence(): void
    {
        $user = User::create([
            'name' => 'ILO Learner',
            'email' => 'ilo@example.test',
            'password' => 'not-used-in-this-unit-test',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        DB::table('assignment_library_items')->insert(['id' => 1, 'module_no' => 1]);
        DB::table('class_assignments')->insert(['id' => 5, 'class_id' => 9, 'assignment_library_item_id' => 1]);
        DB::table('assignment_questions')->insert([
            ['id' => 11, 'assignment_library_item_id' => 1, 'points' => 10, 'order_index' => 1],
            ['id' => 12, 'assignment_library_item_id' => 1, 'points' => 10, 'order_index' => 2],
        ]);
        DB::table('intended_learning_outcomes')->insert([
            ['id' => 21, 'module_no' => 1, 'ilo_code' => 'ILO-1', 'title' => 'Mapped', 'mastery_threshold' => 75, 'is_active' => 1],
            ['id' => 22, 'module_no' => 1, 'ilo_code' => 'ILO-2', 'title' => 'Unmapped', 'mastery_threshold' => 75, 'is_active' => 1],
        ]);
        DB::table('assessment_question_ilos')->insert([
            'ilo_id' => 21,
            'assessment_source' => 'assignment',
            'question_id' => 11,
            'weight' => 1,
        ]);
        DB::table('assignment_submissions')->insert([
            ['id' => 100, 'class_assignment_id' => 5, 'student_id' => $user->id, 'attempt_no' => 1, 'status' => 'graded'],
            ['id' => 101, 'class_assignment_id' => 5, 'student_id' => $user->id, 'attempt_no' => 2, 'status' => 'graded'],
        ]);
        DB::table('assignment_submission_answers')->insert([
            ['assignment_submission_id' => 100, 'assignment_question_id' => 11, 'points_awarded' => 10],
            ['assignment_submission_id' => 100, 'assignment_question_id' => 12, 'points_awarded' => 10],
            ['assignment_submission_id' => 101, 'assignment_question_id' => 11, 'points_awarded' => 0],
            ['assignment_submission_id' => 101, 'assignment_question_id' => 12, 'points_awarded' => 10],
        ]);

        $submission = AssignmentSubmission::query()->findOrFail(101);
        (new IloMasteryService())->refreshForAssignmentSubmission($submission);

        $this->assertDatabaseHas('student_ilo_masteries', [
            'student_id' => $user->id,
            'class_id' => 9,
            'ilo_id' => 21,
            'mastery_percent' => 0,
            'evidence_count' => 1,
            'status' => 'developing',
        ]);
        $this->assertDatabaseMissing('student_ilo_masteries', [
            'student_id' => $user->id,
            'class_id' => 9,
            'ilo_id' => 22,
        ]);
    }
}
