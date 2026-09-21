<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentSubmission;
use App\Models\ClassAssignment;
use App\Services\AntiCheatPolicyService;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Feature\Regression\Concerns\BuildsAssignmentWorkflow;
use Tests\TestCase;

/**
 * DS-03: server enforcement matches the configured policy.
 */
class Ds03AntiCheatPolicyMatrixTest extends TestCase
{
    use BuildsAssignmentWorkflow;
    use RefreshDatabase;

    private int $assignmentId;
    private AssignmentSubmission $attempt;
    /** @var array{item: int, mcq: int, correct: int, wrong: int, blank: int} */
    private array $q;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ValidateCsrfToken::class]);
        Carbon::setTestNow(Carbon::parse('2026-09-20 09:00:00'));
        $this->seedAssignmentActors();
        $this->q = $this->makeLibraryItem(30);
        $this->assignmentId = $this->makeClassAssignment($this->q['item']);
        $this->attempt = $this->makeAttempt($this->assignmentId);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function blockedReason(): ?string
    {
        return app(AntiCheatPolicyService::class)->assignmentSubmissionBlocked(
            $this->student,
            ClassAssignment::findOrFail($this->assignmentId),
            $this->attempt->fresh(),
            $this->attempt->anti_cheat_session_id
        );
    }

    public static function dualMonitorMatrix(): array
    {
        $rows = [];
        foreach ([true, false] as $detect) {
            foreach ([true, false] as $block) {
                foreach ([true, false] as $lock) {
                    $rows[sprintf('detect=%d block=%d lock=%d', $detect, $block, $lock)] = [
                        $detect, $block, $lock,
                        // Only "detect AND block" locks. The lock-screen switch is
                        // about other critical events and must not matter here.
                        $detect && $block,
                    ];
                }
            }
        }

        return $rows;
    }

    #[DataProvider('dualMonitorMatrix')]
    public function test_dual_monitor_event_blocks_only_when_detection_and_blocking_are_both_on(bool $detect, bool $block, bool $lock, bool $expectBlocked): void
    {
        $this->setPolicy([
            'detect_dual_monitor' => $detect,
            'block_dual_monitor' => $block,
            'lock_screen_on_violation' => $lock,
        ]);
        $this->recordEvent($this->attempt, 'dual_monitor_detected');

        $this->assertSame($expectBlocked, $this->blockedReason() !== null);
    }

    public function test_detect_only_dual_monitor_records_the_event_and_accepts_the_submission(): void
    {
        $this->setPolicy(['detect_dual_monitor' => true, 'block_dual_monitor' => false, 'lock_screen_on_violation' => true]);

        $this->actAs($this->student)->postJson(route('anti-cheat.events.store'), [
            'assessment_type' => 'assignment',
            'event_type' => 'dual_monitor_detected',
            'attempt_session_id' => $this->attempt->anti_cheat_session_id,
            'class_assignment_id' => $this->assignmentId,
            'assignment_submission_id' => $this->attempt->id,
            'details' => ['screens' => 2],
        ])->assertOk()->assertJsonPath('integrity.blocked', false);

        $this->assertSame(1, DB::table('anti_cheat_events')->where('event_type', 'dual_monitor_detected')->count());

        $this->actAs($this->student)->post(route('student.assignments.submit', [$this->assignmentId, $this->attempt->id]), [
            '_anti_cheat_session_id' => $this->attempt->anti_cheat_session_id,
            'answers' => [$this->q['mcq'] => (string) $this->q['correct'], $this->q['blank'] => 'pandas'],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $attempt = $this->attempt->fresh();
        $this->assertSame('graded', $attempt->status);
        $this->assertSame('clear', $attempt->integrity_status);
        $this->assertSame(10, $attempt->score);
    }

    public static function eventPolicyMatrix(): array
    {
        return [
            // event, policy overrides, expected blocked
            'fullscreen exit, fullscreen required' => ['fullscreen_exit', ['require_fullscreen' => true], true],
            'fullscreen exit, fullscreen optional' => ['fullscreen_exit', ['require_fullscreen' => false], false],
            'fullscreen exit, lock screen off' => ['fullscreen_exit', ['require_fullscreen' => true, 'lock_screen_on_violation' => false], false],
            'blocked paste, paste disabled' => ['blocked_paste', ['allow_paste' => false], true],
            'blocked paste, external paste blocked' => ['blocked_paste', ['allow_paste' => true, 'block_external_paste' => true], true],
            'blocked paste, paste fully allowed' => ['blocked_paste', ['allow_paste' => true, 'block_external_paste' => false], false],
            'blocked paste, lock screen off' => ['blocked_paste', ['allow_paste' => false, 'lock_screen_on_violation' => false], false],
            'devtools, shortcuts disabled' => ['devtools_shortcut', ['allow_devtools_shortcuts' => false], true],
            'devtools, shortcuts allowed' => ['devtools_shortcut', ['allow_devtools_shortcuts' => true], false],
            'devtools, lock screen off' => ['devtools_shortcut', ['lock_screen_on_violation' => false], false],
            // The browser only warns and logs a right click; it never locks.
            'right click, not allowed' => ['right_click', ['allow_right_click' => false], false],
            'right click, allowed' => ['right_click', ['allow_right_click' => true], false],
            // Reported by the browser, not evidence on its own.
            'forged threshold event' => ['threshold_exceeded', [], false],
            'copy activity' => ['copy', ['allow_copy' => false], false],
        ];
    }

    #[DataProvider('eventPolicyMatrix')]
    public function test_each_event_is_evaluated_against_its_own_policy_flag(string $eventType, array $policy, bool $expectBlocked): void
    {
        $this->setPolicy($policy);
        $this->recordEvent($this->attempt, $eventType);

        $this->assertSame($expectBlocked, $this->blockedReason() !== null);
    }

    public static function focusMatrix(): array
    {
        return [
            'over limit, blocking on' => [3, [], true],
            'at limit' => [2, [], false],
            'over limit, blocking off' => [3, ['block_on_tab_limit' => false], false],
            'over limit, tab switch allowed' => [3, ['allow_tab_switch' => true], false],
            'over limit, lock screen off still blocks (own flag)' => [3, ['lock_screen_on_violation' => false], true],
            'zero allowance' => [1, ['max_tab_switches' => 0], true],
        ];
    }

    #[DataProvider('focusMatrix')]
    public function test_focus_limit_follows_its_own_flags(int $losses, array $policy, bool $expectBlocked): void
    {
        $this->setPolicy(array_merge(['max_tab_switches' => 2], $policy));
        $this->recordFocusLosses($this->attempt, $losses);

        $this->assertSame($expectBlocked, $this->blockedReason() !== null);
    }

    public function test_disabled_policy_never_blocks_and_events_of_other_attempts_are_ignored(): void
    {
        $this->setPolicy(['enabled' => false]);
        $this->recordEvent($this->attempt, 'devtools_shortcut');
        $this->assertNull($this->blockedReason());

        $this->setPolicy();
        DB::table('anti_cheat_events')->delete();
        $otherAttempt = $this->makeAttempt($this->assignmentId, $this->otherStudent);
        $this->recordEvent($otherAttempt, 'devtools_shortcut');
        $this->assertNull($this->blockedReason());
    }

    public function test_practice_workflows_stay_outside_the_instructor_policy(): void
    {
        $this->setPolicy();
        $service = app(AntiCheatPolicyService::class);

        foreach (['mcq', 'coding', 'assessment', 'tos_assessment'] as $type) {
            $this->assertFalse($service->settingsForUser($this->student, $type)['enabled'], $type);
        }
        $this->assertTrue($service->settingsForUser($this->student, 'assignment')['enabled']);
    }
}
