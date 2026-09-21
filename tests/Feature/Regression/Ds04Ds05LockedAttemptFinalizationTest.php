<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DS-04: the locked form can still be finalized (token, identity, answers).
 * DS-05: the take page and every event response carry the server's state.
 */
class Ds04Ds05LockedAttemptFinalizationTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    /** @var array{item: int, mcq: int, correct: int, wrong: int, blank: int} */
    private array $q;
    private int $assignmentId;
    private AssignmentSubmission $attempt;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssignmentActors();
        $this->seedAssignmentRewards();
        $this->setPolicy(['max_tab_switches' => 2, 'auto_submit_mcq_on_violation' => true]);
        $this->q = $this->makeLibraryItem(30);
        $this->assignmentId = $this->makeClassAssignment($this->q['item']);
        $this->attempt = $this->makeAttempt($this->assignmentId);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** The field set the locked browser form submits (see tests/JavaScript/anti-cheat-lock.test.mjs). */
    private function autoSubmitPayload(string $token): array
    {
        return [
            '_token' => $token,
            '_anti_cheat_session_id' => $this->attempt->anti_cheat_session_id,
            '_anti_cheat_finalize' => '1',
            'answers' => [$this->q['mcq'] => (string) $this->q['correct'], $this->q['blank'] => 'pandas'],
        ];
    }

    public function test_auto_submit_payload_passes_real_csrf_verification_and_is_finalized_as_held(): void
    {
        // CSRF verification is skipped while "running unit tests". Leave that
        // mode so the real web middleware stack verifies the token.
        $this->app['env'] = 'local';
        $this->assertFalse($this->app->runningUnitTests());
        $this->recordFocusLosses($this->attempt, 3);

        $token = Str::random(40);
        $submitUrl = route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]);

        // What the old lock did: every input disabled, so no _token was sent.
        $withoutToken = $this->autoSubmitPayload($token);
        unset($withoutToken['_token']);
        $this->actAs($this->student)->withSession(['_token' => $token])
            ->post($submitUrl, $withoutToken)
            ->assertStatus(419);
        $this->assertSame('in_progress', $this->attempt->fresh()->status);

        // The repaired lock keeps _token, identity and the latest answers.
        $this->actAs($this->student)->withSession(['_token' => $token])
            ->post($submitUrl, $this->autoSubmitPayload($token))
            ->assertRedirect(route('student.assignments.result', [$this->assignmentId, $this->attempt->id]));

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertSame(0, $attempt->score);
        $this->assertSame(10, $attempt->provisional_score, 'The latest answers travelled with the auto-submit.');
        $this->assertNull($attempt->timed_out_at);
        $this->assertSame(0, (int) $this->student->fresh()->xp);
    }

    public function test_blocking_the_event_request_cannot_produce_a_clean_graded_result(): void
    {
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $url = route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]);

        // The browser locked itself, but its violation event never arrived.
        $this->actAs($this->student)->post($url, $this->autoSubmitPayload('x'))->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('review_required', $attempt->integrity_status);
        $this->assertSame(0, $attempt->score);
        $this->assertSame(10, $attempt->provisional_score);
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        $events = DB::table('anti_cheat_events')
            ->where('assignment_submission_id', $attempt->id)
            ->where('event_type', 'locked_attempt_finalized');
        $this->assertSame(1, $events->count(), 'The outcome is recorded by the server itself.');

        // Repeating the request neither changes the outcome nor adds rows.
        $this->actAs($this->student)->post($url, $this->autoSubmitPayload('x'));
        $this->assertSame(1, $events->count());
        $this->assertSame('review_required', $this->attempt->fresh()->integrity_status);
    }

    public function test_finalize_flag_is_ignored_when_no_policy_protects_the_assignment(): void
    {
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->setPolicy(['enabled' => false]);

        $this->actAs($this->student)
            ->post(route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]), $this->autoSubmitPayload('x'))
            ->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame(10, $attempt->score);
        $this->assertSame(0, DB::table('anti_cheat_events')->count());
    }

    public function test_reloading_a_blocked_attempt_renders_it_locked_with_the_finalize_action(): void
    {
        $this->recordFocusLosses($this->attempt, 3);

        $html = $this->actAs($this->student)
            ->get(route('student.assignments.take', [$this->assignmentId, $this->attempt->id]))
            ->assertOk()
            ->assertSee('Submit for review')
            ->assertDontSee('Reload Page')
            ->assertDontSee('window.location.reload()', false)
            ->getContent();

        $state = $this->renderedIntegrityState($html);
        $this->assertTrue($state['blocked']);
        $this->assertSame(3, $state['focus_loss_count']);
        $this->assertSame(0, $state['remaining_allowance']);
        $this->assertStringContainsString('focus-loss limit', $state['reason']);

        // The identity is part of the server-rendered form, not only of a script.
        $this->assertStringContainsString(
            'name="_anti_cheat_session_id" value="' . $this->attempt->anti_cheat_session_id . '"',
            $html
        );
    }

    public function test_reloading_an_unblocked_attempt_shows_the_remaining_allowance(): void
    {
        $this->recordFocusLosses($this->attempt, 1);

        $html = $this->actAs($this->student)
            ->get(route('student.assignments.take', [$this->assignmentId, $this->attempt->id]))
            ->assertOk()
            ->getContent();

        $state = $this->renderedIntegrityState($html);
        $this->assertFalse($state['blocked']);
        $this->assertNull($state['reason']);
        $this->assertSame(1, $state['focus_loss_count']);
        $this->assertSame(2, $state['max_tab_switches']);
        $this->assertSame(1, $state['remaining_allowance']);
    }

    public function test_every_event_response_carries_the_authoritative_state(): void
    {
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $expected = [[1, 1, false], [2, 0, false], [3, 0, true]];

        foreach ($expected as $index => [$count, $remaining, $blocked]) {
            Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00')->addSeconds(10 * ($index + 1)));
            $uuid = (string) Str::uuid();
            $payload = [
                'assessment_type' => 'assignment',
                'event_type' => 'focus_loss',
                'event_uuid' => $uuid,
                'attempt_session_id' => $this->attempt->anti_cheat_session_id,
                'class_assignment_id' => $this->assignmentId,
                'assignment_submission_id' => $this->attempt->id,
            ];

            $this->actAs($this->student)->postJson(route('anti-cheat.events.store'), $payload)
                ->assertOk()
                ->assertJsonPath('integrity.focus_loss_count', $count)
                ->assertJsonPath('integrity.remaining_allowance', $remaining)
                ->assertJsonPath('integrity.blocked', $blocked);

            // A retried delivery is deduplicated and does not inflate the count.
            $this->actAs($this->student)->postJson(route('anti-cheat.events.store'), $payload)
                ->assertOk()
                ->assertJsonPath('deduplicated', true)
                ->assertJsonPath('integrity.focus_loss_count', $count);
        }
    }

    public function test_guard_partial_never_disables_hidden_inputs_and_uses_the_tested_client_helpers(): void
    {
        $partial = file_get_contents(resource_path('views/student/partials/anti-cheat-guard.blade.php'));

        $this->assertStringContainsString('clientTools.snapshotAndLockForm(', $partial);
        $this->assertStringContainsString('clientTools.finalizeLockedAttempt(', $partial);
        $this->assertStringContainsString("el.type === 'hidden'", $partial);
        $this->assertStringNotContainsString('form.submit()', $partial);
    }

    /** @return array<string, mixed> */
    private function renderedIntegrityState(string $html): array
    {
        $this->assertSame(1, preg_match('/normalizeIntegrityState\((\{.*?\}|null)\);/s', $html, $matches), 'Integrity state is rendered into the page.');

        return json_decode($matches[1], true, 16, JSON_THROW_ON_ERROR);
    }
}
