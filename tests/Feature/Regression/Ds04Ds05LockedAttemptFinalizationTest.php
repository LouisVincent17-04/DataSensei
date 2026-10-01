<?php

namespace Tests\Feature\Regression;

use App\Models\AssessmentSubmission;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Feature\Regression\Concerns\BuildsClassAssessmentWorkflow;
use Tests\TestCase;

/**
 * DS-04: the locked form can still be finalized (token, identity, answers).
 * DS-05: the take page and every event response carry the server's state.
 * Assessments carry the anti-cheat duty since Updates 11.
 */
class Ds04Ds05LockedAttemptFinalizationTest extends TestCase
{
    use BuildsClassAssessmentWorkflow;
    use RefreshDatabase;

    /** @var array{assessment: int, mcq: int, correct: int, wrong: int, blank: int} */
    private array $q;
    private int $assessmentId;
    private AssessmentSubmission $attempt;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssessmentActors();
        $this->seedAssessmentRewards();
        $this->setPolicy(['max_tab_switches' => 2, 'auto_submit_mcq_on_violation' => true]);
        $this->q = $this->makeAssessment(30);
        $this->assessmentId = $this->q['assessment'];
        $this->attempt = $this->makeAttempt($this->assessmentId);
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

    /** The server writes exactly one deterministic event per finalized locked attempt. */
    private function expectedLockedAttemptFinalizedUuid(): string
    {
        $hash = md5('locked-attempt-finalized:assessment:' . $this->attempt->id);

        return substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-4' . substr($hash, 13, 3)
            . '-8' . substr($hash, 17, 3) . '-' . substr($hash, 20, 12);
    }

    public function test_auto_submit_payload_passes_real_csrf_verification_and_is_finalized_as_held(): void
    {
        // CSRF verification is skipped while "running unit tests". Leave that
        // mode so the real web middleware stack verifies the token.
        $this->app['env'] = 'local';
        $this->assertFalse($this->app->runningUnitTests());
        $this->recordFocusLosses($this->attempt, 3);

        $token = Str::random(40);
        $submitUrl = route('student.assessments.submit', [$this->assessmentId, $this->attempt->id]);

        // What the old lock did: every input disabled, so no _token was sent.
        $withoutToken = $this->autoSubmitPayload($token);
        unset($withoutToken['_token']);
        // Still refused by CSRF verification; since DataSensei Updates 9 the
        // student is sent back to the page instead of a "Page Expired" error.
        $this->actAs($this->student)->withSession(['_token' => $token])
            ->from(route('student.assessments.take', [$this->assessmentId, $this->attempt->id]))
            ->post($submitUrl, $withoutToken)
            ->assertRedirect(route('student.assessments.take', [$this->assessmentId, $this->attempt->id]))
            ->assertSessionHas('error');
        $this->assertSame('in_progress', $this->attempt->fresh()->status);

        // The repaired lock keeps _token, identity and the latest answers.
        $this->actAs($this->student)->withSession(['_token' => $token])
            ->post($submitUrl, $this->autoSubmitPayload($token))
            ->assertRedirect(route('student.assessments.result', [$this->assessmentId, $this->attempt->id]));

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('blocked', $attempt->integrity_status);
        $this->assertSame('0.00', $attempt->score);
        $this->assertSame('10.00', $attempt->provisional_score, 'The latest answers travelled with the auto-submit.');
        $this->assertNull($attempt->graded_at);
        $this->assertNull($attempt->timed_out_at);
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        // The work and its correctness are stored, but no answer carries credit.
        $answers = DB::table('assessment_answers')->where('assessment_submission_id', $attempt->id)->get();
        $this->assertCount(2, $answers);
        $this->assertSame(0.0, (float) $answers->sum('points_awarded'));
        $this->assertTrue($answers->every(fn ($answer) => (int) $answer->is_correct === 1));

        $this->assertSame(1, DB::table('notifications')
            ->where('user_id', $this->student->id)
            ->where('type', 'assessment_held_for_review')
            ->count());
    }

    public function test_blocking_the_event_request_cannot_produce_a_clean_graded_result(): void
    {
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $url = route('student.assessments.submit', [$this->assessmentId, $this->attempt->id]);

        // The browser locked itself, but its violation event never arrived.
        $this->actAs($this->student)->post($url, $this->autoSubmitPayload('x'))->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('submitted', $attempt->status);
        $this->assertSame('review_required', $attempt->integrity_status);
        $this->assertSame('0.00', $attempt->score);
        $this->assertSame('10.00', $attempt->provisional_score);
        $this->assertSame(0, (int) $this->student->fresh()->xp);

        $events = DB::table('anti_cheat_events')
            ->where('assessment_submission_id', $attempt->id)
            ->where('event_type', 'locked_attempt_finalized');
        $this->assertSame(1, $events->count(), 'The outcome is recorded by the server itself.');
        $this->assertSame(
            $this->expectedLockedAttemptFinalizedUuid(),
            $events->clone()->value('event_uuid'),
            'The server event carries the deterministic per-attempt identifier.'
        );

        // Repeating the request neither changes the outcome nor adds rows.
        $this->actAs($this->student)->post($url, $this->autoSubmitPayload('x'));
        $this->assertSame(1, $events->count());
        $this->assertSame('review_required', $this->attempt->fresh()->integrity_status);
    }

    public function test_finalize_flag_is_ignored_when_no_policy_protects_the_assessment(): void
    {
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        $this->setPolicy(['enabled' => false]);

        $this->actAs($this->student)
            ->post(route('student.assessments.submit', [$this->assessmentId, $this->attempt->id]), $this->autoSubmitPayload('x'))
            ->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('10.00', $attempt->score);
        $this->assertSame(0, DB::table('anti_cheat_events')->count());
    }

    public function test_reloading_a_blocked_attempt_renders_it_locked_with_the_finalize_action(): void
    {
        $this->recordFocusLosses($this->attempt, 3);

        $html = $this->actAs($this->student)
            ->get(route('student.assessments.take', [$this->assessmentId, $this->attempt->id]))
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
            ->get(route('student.assessments.take', [$this->assessmentId, $this->attempt->id]))
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
                'assessment_type' => 'assessment',
                'event_type' => 'focus_loss',
                'event_uuid' => $uuid,
                'attempt_session_id' => $this->attempt->anti_cheat_session_id,
                'assessment_id' => $this->assessmentId,
                'assessment_submission_id' => $this->attempt->id,
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
