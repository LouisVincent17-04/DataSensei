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

        // Assignments were merged into assessments (DataSensei Updates 11),
        // so assessments and their submissions are the only graded work.
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->string('status', 20)->default('draft');
            $table->string('purpose', 20)->nullable();
            $table->dateTime('due_at')->nullable();
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
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_only_latest_attempt_per_activity_drives_score_and_late_count(): void
    {
        $student = $this->learner();

        // Two converted homework assessments (formerly class assignments).
        DB::table('assessments')->insert([
            ['id' => 10, 'class_id' => 5, 'status' => 'closed', 'purpose' => 'homework', 'due_at' => now()->subHour()],
            ['id' => 11, 'class_id' => 5, 'status' => 'closed', 'purpose' => 'homework', 'due_at' => now()->subHour()],
        ]);
        DB::table('assessment_submissions')->insert([
            ['id' => 100, 'assessment_id' => 10, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'late', 'score' => 20, 'total_points' => 100, 'graded_at' => now()->subDays(2)],
            ['id' => 101, 'assessment_id' => 10, 'student_id' => $student->id, 'attempt_no' => 2, 'status' => 'graded', 'score' => 90, 'total_points' => 100, 'graded_at' => now()->subDay()],
            ['id' => 110, 'assessment_id' => 11, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 40, 'total_points' => 100, 'graded_at' => now()->subDays(2)],
            ['id' => 111, 'assessment_id' => 11, 'student_id' => $student->id, 'attempt_no' => 2, 'status' => 'late', 'score' => 70, 'total_points' => 100, 'graded_at' => now()->subDay()],
        ]);

        $snapshot = (new StudentPerformanceClusteringService())->refreshForStudent($student, $this->classRoom(5));

        $this->assertSame(80.0, (float) $snapshot->average_score_percent);
        $this->assertSame(2, (int) $snapshot->completed_activities);
        $this->assertSame(0, (int) $snapshot->missing_assignments);
        $this->assertSame(1, (int) $snapshot->late_submissions);
        $this->assertDatabaseCount('student_performance_snapshots', 1);
        $this->assertDatabaseCount('student_performance_clusters', 1);
    }

    public function test_missing_assessments_fill_the_historical_missing_column_for_the_class_only(): void
    {
        $student = $this->learner();

        DB::table('assessments')->insert([
            // Missing: past due and never turned in.
            ['id' => 20, 'class_id' => 5, 'status' => 'published', 'purpose' => 'homework', 'due_at' => now()->subDay()],
            // Missing: closed, even without a due date.
            ['id' => 21, 'class_id' => 5, 'status' => 'closed', 'purpose' => 'quiz', 'due_at' => null],
            // Not missing: still open.
            ['id' => 22, 'class_id' => 5, 'status' => 'published', 'purpose' => 'examination', 'due_at' => now()->addDay()],
            // Not missing: a draft students never saw.
            ['id' => 23, 'class_id' => 5, 'status' => 'draft', 'purpose' => null, 'due_at' => now()->subDay()],
            // Missing: only an unfinished attempt exists.
            ['id' => 24, 'class_id' => 5, 'status' => 'published', 'purpose' => null, 'due_at' => now()->subDay()],
            // Not missing: graded.
            ['id' => 25, 'class_id' => 5, 'status' => 'published', 'purpose' => 'homework', 'due_at' => now()->subDay()],
            // Not missing: turned in, essay still waiting for a grade.
            ['id' => 26, 'class_id' => 5, 'status' => 'published', 'purpose' => 'homework', 'due_at' => now()->subDay()],
            // Another class: never counted here.
            ['id' => 30, 'class_id' => 6, 'status' => 'published', 'purpose' => 'homework', 'due_at' => now()->subDay()],
        ]);
        DB::table('assessment_submissions')->insert([
            ['id' => 240, 'assessment_id' => 24, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'in_progress', 'score' => 0, 'total_points' => 10, 'graded_at' => null],
            ['id' => 250, 'assessment_id' => 25, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 5, 'total_points' => 10, 'graded_at' => now()->subHours(3)],
            ['id' => 260, 'assessment_id' => 26, 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'submitted', 'score' => 0, 'total_points' => 10, 'graded_at' => null],
        ]);

        $snapshot = (new StudentPerformanceClusteringService())->refreshForStudent($student, $this->classRoom(5));

        $this->assertSame(3, (int) $snapshot->missing_assignments, 'Assessments 20, 21 and 24.');
        $this->assertSame(1, (int) $snapshot->completed_activities, 'Only the graded attempt is scored.');
        $this->assertSame(50.0, (float) $snapshot->average_score_percent);
        $this->assertSame(0, (int) $snapshot->late_submissions);
        $this->assertSame('At Risk', $snapshot->cluster_label);
        $this->assertSame('high', $snapshot->risk_level);

        $other = (new StudentPerformanceClusteringService())->refreshForStudent($student, $this->classRoom(6));
        $this->assertSame(1, (int) $other->missing_assignments);
        $this->assertSame(0, (int) $other->completed_activities);
        $this->assertSame('Not Enough Data', $other->cluster_label);
        $this->assertDatabaseCount('student_performance_snapshots', 2);
    }

    private function learner(): User
    {
        return User::create([
            'name' => 'Analytics Learner',
            'email' => 'analytics@example.test',
            'password' => 'not-used-in-this-unit-test',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    private function classRoom(int $id): ClassRoom
    {
        $class = new ClassRoom();
        $class->id = $id;

        return $class;
    }
}
