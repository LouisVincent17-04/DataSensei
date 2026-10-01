<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The anti-cheat event-ingest API contract. Since DataSensei Updates 11 the
 * payload names the protected assessment attempt (assessment_id,
 * assessment_submission_id, assessment_question_id) and the only accepted
 * assessment_type is 'assessment'.
 */
class AntiCheatEventContractTest extends TestCase
{
    private User $student;

    private int $classId;

    private int $assessmentId;

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
            'name' => 'Protected Assessment Student',
            'email' => 'anti-cheat-student@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->classId = DB::table('classes')->insertGetId([
            'instructor_id' => null,
        ]);
        $this->assessmentId = DB::table('assessments')->insertGetId([
            'class_id' => $this->classId,
        ]);
        $this->submissionId = DB::table('assessment_submissions')->insertGetId([
            'assessment_id' => $this->assessmentId,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
            'anti_cheat_session_id' => $this->sessionId,
        ]);
        DB::table('class_student')->insert([
            'class_id' => $this->classId,
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
            'assessment_questions',
            'class_student',
            'assessment_submissions',
            'assessments',
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

        // The legacy focus event names stay accepted and correlate with the
        // canonical focus_loss event.
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

    public function test_severity_mapping_matches_the_published_contract(): void
    {
        foreach ([
            'devtools_shortcut' => ['critical', 'violation'],
            'blocked_paste' => ['critical', 'violation'],
            'tab_switch' => ['warning', 'violation'],
            'copy' => ['warning', 'activity'],
            'cut' => ['info', 'activity'],
        ] as $eventType => [$severity, $classification]) {
            // Spread the events beyond the focus correlation window.
            Carbon::setTestNow(now()->addSeconds(5));

            $this->postJson(route('anti-cheat.events.store'), $this->payload(
                $eventType,
                (string) Str::uuid()
            ))
                ->assertOk()
                ->assertJson([
                    'ok' => true,
                    'severity' => $severity,
                    'classification' => $classification,
                    'deduplicated' => false,
                ]);
        }

        $this->assertDatabaseCount('anti_cheat_events', 5);
    }

    public function test_rejected_payloads_never_store_an_event(): void
    {
        // The retired 'assignment' type is no longer a valid payload.
        $legacyType = $this->payload('focus_loss', (string) Str::uuid());
        $legacyType['assessment_type'] = 'assignment';
        $this->postJson(route('anti-cheat.events.store'), $legacyType)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['assessment_type']);

        // Unknown event types are refused by the shared contract allow-list.
        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('made_up_event', (string) Str::uuid())
        )->assertStatus(422)->assertJsonValidationErrors(['event_type']);

        // The attempt identity must match the stored per-attempt secret.
        $forgedSession = $this->payload('focus_loss', (string) Str::uuid());
        $forgedSession['attempt_session_id'] = 'some-other-session';
        $this->postJson(route('anti-cheat.events.store'), $forgedSession)
            ->assertStatus(403);

        // The submission must belong to the assessment named in the payload.
        $otherAssessmentId = DB::table('assessments')->insertGetId(['class_id' => $this->classId]);
        $wrongAssessment = $this->payload('focus_loss', (string) Str::uuid());
        $wrongAssessment['assessment_id'] = $otherAssessmentId;
        $this->postJson(route('anti-cheat.events.store'), $wrongAssessment)
            ->assertStatus(403);

        // A question context from another assessment is refused.
        $foreignQuestionId = DB::table('assessment_questions')->insertGetId([
            'assessment_id' => $otherAssessmentId,
        ]);
        $foreignQuestion = $this->payload('focus_loss', (string) Str::uuid());
        $foreignQuestion['assessment_question_id'] = $foreignQuestionId;
        $this->postJson(route('anti-cheat.events.store'), $foreignQuestion)
            ->assertStatus(403);

        // A finished attempt accepts no further events.
        DB::table('assessment_submissions')->where('id', $this->submissionId)->update(['status' => 'submitted']);
        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('focus_loss', (string) Str::uuid())
        )->assertStatus(403);
        DB::table('assessment_submissions')->where('id', $this->submissionId)->update(['status' => 'in_progress']);

        $this->assertDatabaseCount('anti_cheat_events', 0);
    }

    public function test_reusing_an_event_uuid_for_a_different_event_type_conflicts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-31 09:00:00'));
        $uuid = (string) Str::uuid();

        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('focus_loss', $uuid)
        )->assertOk()->assertJson(['deduplicated' => false]);

        $this->postJson(
            route('anti-cheat.events.store'),
            $this->payload('devtools_shortcut', $uuid)
        )->assertStatus(409);

        $this->assertDatabaseCount('anti_cheat_events', 1);
    }

    private function payload(string $eventType, string $eventUuid): array
    {
        return [
            'assessment_type' => 'assessment',
            'event_type' => $eventType,
            'event_uuid' => $eventUuid,
            'attempt_session_id' => $this->sessionId,
            'assessment_id' => $this->assessmentId,
            'assessment_submission_id' => $this->submissionId,
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
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('class_id');
        });
        Schema::create('assessment_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->string('status');
            $table->string('anti_cheat_session_id', 120)->nullable();
        });
        // Every event response reports the attempt's integrity state, which
        // reads the instructor policy of the class (DS-05).
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id')->nullable();
        });
        Schema::create('anti_cheat_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('assessment_type')->default('assessment');
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
        Schema::create('assessment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
        });
        Schema::create('anti_cheat_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->unsignedBigInteger('assessment_submission_id')->nullable();
            $table->unsignedBigInteger('assessment_question_id')->nullable();
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
