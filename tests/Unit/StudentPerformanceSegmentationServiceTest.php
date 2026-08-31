<?php

namespace Tests\Unit;

use App\Models\ClassRoom;
use App\Models\User;
use App\Services\StudentPerformanceClusteringService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentPerformanceSegmentationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('cache.default', 'array');

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

        Schema::create('class_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->string('status', 20);
            $table->dateTime('due_at')->nullable();
        });

        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 20);
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
        });

        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
        });

        Schema::create('assessment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedInteger('attempt_no');
            $table->string('status', 20);
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('total_points', 8, 2)->default(0);
            $table->dateTime('graded_at')->nullable();
        });

        Schema::create('student_performance_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->decimal('average_score_percent', 6, 2)->default(0);
            $table->decimal('average_time_ratio', 6, 2)->nullable();
            $table->unsignedInteger('completed_activities')->default(0);
            $table->unsignedInteger('missing_assignments')->default(0);
            $table->unsignedInteger('late_submissions')->default(0);
            $table->unsignedInteger('anti_cheat_warnings')->default(0);
            $table->decimal('engagement_score', 6, 2)->default(0);
            $table->string('cluster_label', 80);
            $table->string('risk_level', 30);
            $table->dateTime('generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('student_performance_clusters', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('cluster_label', 80);
            $table->text('cluster_description')->nullable();
            $table->decimal('average_score_percent', 6, 2)->default(0);
            $table->decimal('engagement_score', 6, 2)->default(0);
            $table->string('risk_level', 30);
            $table->dateTime('assigned_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        foreach ([
            'student_performance_clusters',
            'student_performance_snapshots',
            'assessment_submissions',
            'assessments',
            'assignment_submissions',
            'class_assignments',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_only_latest_attempt_per_activity_drives_score_and_late_count(): void
    {
        $student = User::create([
            'name' => 'Analytics Learner',
            'email' => 'analytics@example.test',
            'password' => 'not-used-in-this-unit-test',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        DB::table('class_assignments')->insert([
            ['id' => 10, 'class_id' => 5, 'status' => 'closed', 'due_at' => now()->subHour()],
            ['id' => 11, 'class_id' => 5, 'status' => 'closed', 'due_at' => now()->subHour()],
        ]);
        DB::table('assignment_submissions')->insert([
            ['id' => 100, 'class_assignment_id' => 10, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'late', 'score' => 20, 'total_points' => 100],
            ['id' => 101, 'class_assignment_id' => 10, 'student_id' => $student->id, 'attempt_no' => 2, 'status' => 'graded', 'score' => 90, 'total_points' => 100],
            ['id' => 110, 'class_assignment_id' => 11, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 40, 'total_points' => 100],
            ['id' => 111, 'class_assignment_id' => 11, 'student_id' => $student->id, 'attempt_no' => 2, 'status' => 'late', 'score' => 70, 'total_points' => 100],
        ]);

        $class = new ClassRoom();
        $class->id = 5;

        $snapshot = (new StudentPerformanceClusteringService())->refreshForStudent($student, $class);

        $this->assertSame(80.0, (float) $snapshot->average_score_percent);
        $this->assertSame(2, (int) $snapshot->completed_activities);
        $this->assertSame(0, (int) $snapshot->missing_assignments);
        $this->assertSame(1, (int) $snapshot->late_submissions);
        $this->assertDatabaseCount('student_performance_snapshots', 1);
        $this->assertDatabaseCount('student_performance_clusters', 1);
    }
}
