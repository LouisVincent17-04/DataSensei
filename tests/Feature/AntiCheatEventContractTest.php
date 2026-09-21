<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AntiCheatEventContractTest extends TestCase
{
    private User $student;

    private int $classAssignmentId;

    private int $submissionId;

    private string $sessionId = 'anti-cheat-test-session';

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->withoutMiddleware();
        $this->createTables();

        $this->student = User::create([
            'name' => 'Protected Assignment Student',
            'email' => 'anti-cheat-student@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->classAssignmentId = DB::table('class_assignments')->insertGetId([
            'class_id' => 41,
            'assignment_library_item_id' => 51,
        ]);
        $this->submissionId = DB::table('assignment_submissions')->insertGetId([
            'class_assignment_id' => $this->classAssignmentId,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
            'anti_cheat_session_id' => $this->sessionId,
        ]);
        DB::table('class_student')->insert([
            'class_id' => 41,
            'student_id' => $this->student->id,
        ]);
        $this->actingAs($this->student);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        foreach ([
            'anti_cheat_events',
            'anti_cheat_settings',
            'classes',
            'assignment_questions',
            'class_student',
            'assignment_submissions',
            'class_assignments',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        parent::tearDown();
    }

    public function test_all_browser_capability_events_are_accepted_as_informational(): void
    {
        foreach ([
            'fullscreen_entered',
            'fullscreen_request_failed',
            'dual_monitor_check_unavailable',
            'dual_monitor_check_failed',
        ] as $eventType) {
            $this->postJson(route('anti-cheat.events.store'), $this->payload(
                $eventType,
                (string) Str::uuid()
            ))
                ->assertOk()
                ->assertJson([
                    'ok' => true,
                    'severity' => 'info',
                    'classification' => 'informational',
                    'deduplicated' => false,
                ]);
        }

        $this->assertDatabaseCount('anti_cheat_events', 4);
    }

    public function test_focus_events_are_deduplicated_by_uuid_and_correlation_window(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-31 09:00:00'));
        $firstUuid = (string) Str::uuid();

        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('focus_loss', $firstUuid)
        )->assertOk()->assertJson([
            'ok' => true,
            'event_uuid' => $firstUuid,
            'severity' => 'warning',
            'classification' => 'violation',
            'deduplicated' => false,
        ]);

        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('focus_loss', $firstUuid)
        )->assertOk()->assertJson([
            'event_uuid' => $firstUuid,
            'deduplicated' => true,
        ]);

        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('window_blur', (string) Str::uuid())
        )->assertOk()->assertJson([
            'event_uuid' => $firstUuid,
            'deduplicated' => true,
        ]);

        $this->assertDatabaseCount('anti_cheat_events', 1);

        Carbon::setTestNow(Carbon::parse('2026-08-31 09:00:03'));
        $secondUuid = (string) Str::uuid();
        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('focus_loss', $secondUuid)
        )->assertOk()->assertJson([
            'event_uuid' => $secondUuid,
            'deduplicated' => false,
        ]);

        $this->assertDatabaseCount('anti_cheat_events', 2);
    }

    private function payload(string $eventType, string $eventUuid): array
    {
        return [
            'assessment_type' => 'assignment',
            'event_type' => $eventType,
            'event_uuid' => $eventUuid,
            'attempt_session_id' => $this->sessionId,
            'class_assignment_id' => $this->classAssignmentId,
            'assignment_submission_id' => $this->submissionId,
            'details' => ['test' => true],
        ];
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
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('class_assignments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('assignment_library_item_id');
        });
        Schema::create('assignment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->string('status');
            $table->string('anti_cheat_session_id', 120)->nullable();
        });
        // Every event response now reports the attempt's integrity state, which
        // reads the instructor policy of the class (DS-05).
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
        });
        Schema::create('anti_cheat_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('assessment_type')->default('assignment');
            $table->boolean('enabled')->default(true);
            $table->boolean('allow_tab_switch')->default(false);
            $table->unsignedInteger('max_tab_switches')->default(2);
            $table->boolean('block_on_tab_limit')->default(true);
            $table->timestamps();
        });
        Schema::create('class_student', function (Blueprint $table): void {
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('student_id');
        });
        Schema::create('assignment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_library_item_id');
        });
        Schema::create('anti_cheat_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('class_assignment_id')->nullable();
            $table->unsignedBigInteger('assignment_submission_id')->nullable();
            $table->unsignedBigInteger('assignment_question_id')->nullable();
            $table->string('assessment_type');
            $table->string('event_type', 80);
            $table->string('severity', 30);
            $table->string('attempt_session_id', 120)->nullable();
            $table->string('event_uuid', 36)->nullable();
            $table->longText('details')->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
            $table->unique(['attempt_session_id', 'event_uuid']);
        });
    }
}
