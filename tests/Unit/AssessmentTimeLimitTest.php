<?php

namespace Tests\Unit;

use App\Http\Controllers\StudentAssessmentController;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionMethod;
use Tests\TestCase;

/**
 * The server-side attempt deadline (DataSensei Updates 11: assignments were
 * merged into assessments, so the one assessment rule now covers homework,
 * quizzes and examinations). A timed attempt ends when its time limit runs
 * out, or at the due date if that comes first; untimed work has no hard end.
 */
class AssessmentTimeLimitTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_server_timer_expires_exactly_at_configured_deadline(): void
    {
        $now = Carbon::parse('2026-08-06 12:00:00');
        Carbon::setTestNow($now);

        $assessment = new Assessment(['time_limit_minutes' => 5]);
        $submission = new AssessmentSubmission();
        $submission->started_at = $now->copy()->subMinutes(5);

        $this->assertSame(0, $this->remainingSeconds($assessment, $submission));

        $submission->started_at = $now->copy()->subMinutes(5)->addSecond();
        $this->assertSame(1, $this->remainingSeconds($assessment, $submission));

        $submission->started_at = $now->copy()->subMinutes(5)->subSecond();
        $this->assertSame(0, $this->remainingSeconds($assessment, $submission));

        [$endsAt, $reason] = $this->attemptEnd($assessment, $submission);
        $this->assertSame('2026-08-06 11:59:59', $endsAt->format('Y-m-d H:i:s'));
        $this->assertSame('time_limit', $reason);
    }

    /** @return iterable<string, array{?int}> */
    public static function untimedProvider(): iterable
    {
        yield 'zero minutes' => [0];
        yield 'no time limit' => [null];
    }

    #[DataProvider('untimedProvider')]
    public function test_untimed_assessment_has_no_server_deadline(?int $timeLimit): void
    {
        Carbon::setTestNow('2026-08-06 12:00:00');

        $assessment = new Assessment(['time_limit_minutes' => $timeLimit]);
        $submission = new AssessmentSubmission();
        $submission->started_at = now();

        $this->assertNull($this->remainingSeconds($assessment, $submission));
        $this->assertSame([null, null], $this->attemptEnd($assessment, $submission));

        // A due date does not add a hard end to untimed work (homework can
        // still be turned in late).
        $assessment->due_at = now()->subHour();
        $this->assertNull($this->remainingSeconds($assessment, $submission));
        $this->assertSame([null, null], $this->attemptEnd($assessment, $submission));
    }

    public function test_an_attempt_that_never_started_has_no_server_deadline(): void
    {
        $assessment = new Assessment(['time_limit_minutes' => 5, 'due_at' => '2026-08-06 12:00:00']);
        $submission = new AssessmentSubmission();

        $this->assertNull($this->remainingSeconds($assessment, $submission));
        $this->assertSame([null, null], $this->attemptEnd($assessment, $submission));
    }

    public function test_a_due_date_before_the_time_limit_ends_the_attempt_at_the_due_date(): void
    {
        $now = Carbon::parse('2026-08-06 12:00:00');
        Carbon::setTestNow($now);

        // 30 minutes allowed, started 1 minute ago, but due in 2 minutes.
        $assessment = new Assessment([
            'time_limit_minutes' => 30,
            'due_at' => $now->copy()->addMinutes(2)->format('Y-m-d H:i:s'),
        ]);
        $submission = new AssessmentSubmission();
        $submission->started_at = $now->copy()->subMinute();

        [$endsAt, $reason] = $this->attemptEnd($assessment, $submission);
        $this->assertSame('2026-08-06 12:02:00', $endsAt->format('Y-m-d H:i:s'));
        $this->assertSame('due_date', $reason);
        $this->assertSame(120, $this->remainingSeconds($assessment, $submission));

        // Exactly at the due date the attempt is over, and it stays over.
        Carbon::setTestNow($now->copy()->addMinutes(2));
        $this->assertSame(0, $this->remainingSeconds($assessment, $submission));
        Carbon::setTestNow($now->copy()->addMinutes(2)->addSecond());
        $this->assertSame(0, $this->remainingSeconds($assessment, $submission));
    }

    public function test_a_due_date_after_the_time_limit_leaves_the_time_limit_in_charge(): void
    {
        $now = Carbon::parse('2026-08-06 12:00:00');
        Carbon::setTestNow($now);

        $assessment = new Assessment([
            'time_limit_minutes' => 5,
            'due_at' => $now->copy()->addHour()->format('Y-m-d H:i:s'),
        ]);
        $submission = new AssessmentSubmission();
        $submission->started_at = $now->copy();

        [$endsAt, $reason] = $this->attemptEnd($assessment, $submission);
        $this->assertSame('2026-08-06 12:05:00', $endsAt->format('Y-m-d H:i:s'));
        $this->assertSame('time_limit', $reason);
        $this->assertSame(300, $this->remainingSeconds($assessment, $submission));
    }

    public function test_a_due_date_equal_to_the_time_limit_end_reports_the_time_limit(): void
    {
        $now = Carbon::parse('2026-08-06 12:00:00');
        Carbon::setTestNow($now);

        $assessment = new Assessment([
            'time_limit_minutes' => 5,
            'due_at' => $now->copy()->addMinutes(5)->format('Y-m-d H:i:s'),
        ]);
        $submission = new AssessmentSubmission();
        $submission->started_at = $now->copy();

        [$endsAt, $reason] = $this->attemptEnd($assessment, $submission);
        $this->assertSame('2026-08-06 12:05:00', $endsAt->format('Y-m-d H:i:s'));
        $this->assertSame('time_limit', $reason, 'Only a strictly earlier due date takes over.');
    }

    private function remainingSeconds(Assessment $assessment, AssessmentSubmission $submission): ?int
    {
        $method = new ReflectionMethod(StudentAssessmentController::class, 'remainingSeconds');
        $method->setAccessible(true);

        return $method->invoke(new StudentAssessmentController(), $assessment, $submission);
    }

    /** @return array{0: ?\Illuminate\Support\Carbon, 1: ?string} */
    private function attemptEnd(Assessment $assessment, AssessmentSubmission $submission): array
    {
        $method = new ReflectionMethod(StudentAssessmentController::class, 'attemptEnd');
        $method->setAccessible(true);

        return $method->invoke(new StudentAssessmentController(), $assessment, $submission);
    }
}
