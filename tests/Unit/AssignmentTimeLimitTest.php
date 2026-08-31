<?php

namespace Tests\Unit;

use App\Http\Controllers\StudentAssignmentController;
use App\Http\Controllers\StudentAssessmentController;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\AssignmentLibraryItem;
use App\Models\AssignmentSubmission;
use App\Models\ClassAssignment;
use Carbon\Carbon;
use ReflectionMethod;
use Tests\TestCase;

class AssignmentTimeLimitTest extends TestCase
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

        $assignment = new ClassAssignment();
        $assignment->setRelation('libraryItem', new AssignmentLibraryItem([
            'time_limit_minutes' => 5,
        ]));

        $submission = new AssignmentSubmission();
        $submission->started_at = $now->copy()->subMinutes(5);

        $this->assertSame(0, $this->remainingSeconds($assignment, $submission));

        $submission->started_at = $now->copy()->subMinutes(5)->addSecond();
        $this->assertSame(1, $this->remainingSeconds($assignment, $submission));
    }

    public function test_untimed_assignment_has_no_server_deadline(): void
    {
        $assignment = new ClassAssignment();
        $assignment->setRelation('libraryItem', new AssignmentLibraryItem([
            'time_limit_minutes' => 0,
        ]));

        $submission = new AssignmentSubmission();
        $submission->started_at = now();

        $this->assertNull($this->remainingSeconds($assignment, $submission));
    }

    public function test_manual_assessment_uses_the_same_exact_server_deadline(): void
    {
        $now = Carbon::parse('2026-08-06 12:00:00');
        Carbon::setTestNow($now);

        $assessment = new Assessment(['time_limit_minutes' => 5]);
        $submission = new AssessmentSubmission();
        $submission->started_at = $now->copy()->subMinutes(5);

        $method = new ReflectionMethod(StudentAssessmentController::class, 'remainingSeconds');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke(new StudentAssessmentController(), $assessment, $submission));
    }

    private function remainingSeconds(ClassAssignment $assignment, AssignmentSubmission $submission): ?int
    {
        $method = new ReflectionMethod(StudentAssignmentController::class, 'remainingSeconds');
        $method->setAccessible(true);

        return $method->invoke(new StudentAssignmentController(), $assignment, $submission);
    }
}
