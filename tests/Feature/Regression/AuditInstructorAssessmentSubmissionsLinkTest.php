<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\User;
use Tests\Feature\Regression\Concerns\BuildsAssessmentWorkflow;
use Tests\TestCase;

/**
 * Instructor audit: the assessment submission list (and therefore the essay
 * grading screen behind it) had no entry point anywhere in the interface.
 * Both the assessment list and the item builder must link to it, and the page
 * itself must stay closed to other instructors.
 */
class AuditInstructorAssessmentSubmissionsLinkTest extends TestCase
{
    use BuildsAssessmentWorkflow;

    private Assessment $assessment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootAssessmentWorkflow();

        [$this->assessment] = $this->makeAssessment([], [
            ['question_type' => 'essay', 'points' => 5, 'rubric_text' => 'Rubric.'],
        ]);
    }

    protected function tearDown(): void
    {
        $this->dropAssessmentWorkflowTables();

        parent::tearDown();
    }

    public function test_assessment_list_links_to_the_submission_list(): void
    {
        $html = $this->instructorClient()
            ->get(route('instructor.assessments.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            route('instructor.assessments.submissions', $this->assessment, false),
            $html
        );
    }

    public function test_item_builder_links_to_the_submission_list(): void
    {
        $draft = Assessment::query()->whereKey($this->assessment->id)->firstOrFail();
        $draft->update(['status' => 'draft']);

        $html = $this->instructorClient()
            ->get(route('instructor.assessments.builder', $draft))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            route('instructor.assessments.submissions', $draft, false),
            $html
        );
    }

    public function test_submission_list_reaches_the_grading_screen_for_the_owner_only(): void
    {
        $submission = $this->startAttempt($this->assessment);
        $submission->update(['status' => 'submitted', 'submitted_at' => now()]);

        $html = $this->instructorClient()
            ->get(route('instructor.assessments.submissions', $this->assessment))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString(
            route('instructor.assessments.submissions.show', [$this->assessment, $submission], false),
            $html
        );

        $other = User::create([
            'name' => 'Other Instructor',
            'email' => 'audit-other-instructor@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
            'institution_id' => 1,
        ]);

        $this->clientFor($other)
            ->get(route('instructor.assessments.submissions', $this->assessment))
            ->assertStatus(403);
    }

    public function test_submission_list_renders_when_there_are_no_submissions(): void
    {
        $this->assertSame(0, AssessmentSubmission::query()->count());

        $this->instructorClient()
            ->get(route('instructor.assessments.submissions', $this->assessment))
            ->assertOk()
            ->assertSee('No submissions yet');
    }
}
