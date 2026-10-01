<?php

namespace Tests\Unit;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSubmission;
use App\Models\ChallengeAttempt;
use App\Models\CodingChallengeRetake;
use App\Models\CodingSubmission;
use App\Models\IdeExecutionLog;
use App\Models\IdeNode;
use App\Models\Institution;
use App\Models\InstructorApplication;
use App\Models\MlModel;
use App\Models\ModelDevelopmentRun;
use App\Models\ModuleLibraryItem;
use App\Models\Notification;
use App\Models\QualityReport;
use App\Models\QuestionBankItem;
use App\Models\TableOfSpecification;
use App\Models\TrainingJob;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CoreModelBehaviorTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_user_role_helpers_are_mutually_consistent(): void
    {
        $roles = [
            User::ROLE_USER => ['isUser', 'Learner / Common User', false],
            User::ROLE_ADMIN => ['isAdmin', 'Admin', true],
            User::ROLE_SUPERADMIN => ['isSuperAdmin', 'Superadmin', true],
            User::ROLE_INSTRUCTOR => ['isInstructor', 'Instructor', false],
            User::ROLE_INSTITUTION_ADMIN => ['isInstitutionAdmin', 'Institution Admin', false],
        ];
        $roleMethods = ['isUser', 'isAdmin', 'isSuperAdmin', 'isInstructor', 'isInstitutionAdmin'];

        foreach ($roles as $role => [$expectedMethod, $label, $platformStaff]) {
            $user = new User(['role' => $role, 'status' => 'active']);

            foreach ($roleMethods as $method) {
                $this->assertSame($method === $expectedMethod, $user->{$method}());
            }

            $this->assertSame($role === User::ROLE_USER, $user->isLearner());
            $this->assertSame($label, $user->roleName());
            $this->assertSame($label, $user->role_name);
            $this->assertSame($platformStaff, $user->isPlatformStaff());
            $this->assertTrue($user->is_active);
        }

        $unknown = new User(['role' => 999, 'status' => 'disabled']);
        $this->assertSame('Unknown Role', $unknown->roleName());
        $this->assertFalse($unknown->is_active);
    }

    public function test_submission_percentage_and_coding_score_accessors_handle_zero_totals(): void
    {
        $assessment = new AssessmentSubmission(['score' => 7.5, 'total_points' => 10]);
        // Former assignment submissions are assessment submissions since
        // Updates 11; the same rounding applies (7 of 8 is 87.5%, shown 88).
        $converted = new AssessmentSubmission(['score' => 7, 'total_points' => 8]);
        $coding = new CodingSubmission(['tests_passed' => 3, 'tests_total' => 4]);

        $this->assertSame(75, $assessment->percentage);
        $this->assertSame(88, $converted->percentage);
        $this->assertSame(75.0, $coding->score_percent);
        $this->assertFalse($coding->isPerfect());

        $this->assertSame(0, (new AssessmentSubmission(['score' => 1, 'total_points' => 0]))->percentage);
        $this->assertSame(0, (new AssessmentSubmission(['score' => 0, 'total_points' => null]))->percentage);
        $this->assertSame(0.0, (new CodingSubmission(['tests_passed' => 0, 'tests_total' => 0]))->score_percent);
        $this->assertSame(0.0, (new CodingSubmission(['tests_passed' => '0', 'tests_total' => '0']))->score_percent);
        $this->assertTrue((new CodingSubmission(['tests_passed' => 4, 'tests_total' => 4]))->isPerfect());
    }

    public function test_state_helpers_recognize_only_their_documented_terminal_states(): void
    {
        foreach (TrainingJob::TERMINAL_STATUSES as $status) {
            $this->assertTrue((new TrainingJob(['status' => $status]))->isTerminal());
        }
        foreach ([TrainingJob::STATUS_QUEUED, TrainingJob::STATUS_RUNNING, TrainingJob::STATUS_RETRYING] as $status) {
            $this->assertFalse((new TrainingJob(['status' => $status]))->isTerminal());
        }

        foreach (['submitted', 'expired', 'voided', 'disqualified'] as $status) {
            $this->assertTrue((new ChallengeAttempt(['status' => $status]))->isFinished());
        }
        foreach (['in_progress', 'started', null] as $status) {
            $this->assertFalse((new ChallengeAttempt(['status' => $status]))->isFinished());
        }

        $this->assertTrue((new MlModel(['pipeline_type' => 'system']))->isSystemModel());
        $this->assertFalse((new MlModel(['pipeline_type' => 'user']))->isSystemModel());
        $this->assertTrue((new ModelDevelopmentRun(['status' => 'completed']))->isCompleted());
        $this->assertFalse((new ModelDevelopmentRun(['status' => 'failed']))->isCompleted());
    }

    #[DataProvider('qualityGradeProvider')]
    public function test_quality_report_grades_use_the_documented_boundaries(float $score, string $grade): void
    {
        $this->assertSame($grade, (new QualityReport(['quality_score' => $score]))->grade);
    }

    public function test_coding_retake_limits_never_become_negative(): void
    {
        $this->assertTrue((new CodingChallengeRetake(['retake_count' => 0]))->hasRetakesRemaining());
        $this->assertSame(1, (new CodingChallengeRetake(['retake_count' => 2]))->retakesRemaining());
        $this->assertFalse((new CodingChallengeRetake(['retake_count' => 3]))->hasRetakesRemaining());
        $this->assertSame(0, (new CodingChallengeRetake(['retake_count' => 20]))->retakesRemaining());
    }

    public function test_tos_coverage_and_class_binding_helpers_support_global_and_custom_blueprints(): void
    {
        $global = new TableOfSpecification(['class_id' => null, 'module_no' => 5]);
        $classSpecific = new TableOfSpecification(['class_id' => 12, 'module_no' => 0, 'custom_coverage' => 'Data Ethics']);

        $this->assertSame('Module 5', $global->coverage_label);
        $this->assertTrue($global->canBeUsedForClass(99));
        $this->assertSame('Data Ethics', $classSpecific->coverage_label);
        $this->assertTrue($classSpecific->canBeUsedForClass(12));
        $this->assertFalse($classSpecific->canBeUsedForClass(13));
    }

    public function test_assessment_purposes_are_labels_of_one_feature(): void
    {
        $this->assertSame(
            ['homework' => 'Homework', 'quiz' => 'Quiz', 'examination' => 'Examination'],
            Assessment::PURPOSES
        );

        foreach (Assessment::PURPOSES as $purpose => $label) {
            $this->assertSame($label, (new Assessment(['purpose' => $purpose]))->purposeLabel());
        }

        $this->assertNull((new Assessment(['purpose' => null]))->purposeLabel());
        $this->assertNull((new Assessment(['purpose' => 'assignment']))->purposeLabel());
        $this->assertNull((new Assessment())->purposeLabel());
    }

    #[DataProvider('integrityHoldProvider')]
    public function test_only_a_submitted_attempt_with_a_blocking_decision_is_held_for_review(
        string $status,
        ?string $integrity,
        bool $held
    ): void {
        $submission = new AssessmentSubmission(['status' => $status, 'integrity_status' => $integrity]);

        $this->assertSame($held, $submission->isHeldForIntegrityReview());
    }

    public function test_question_bank_types_are_exactly_the_assessment_question_types(): void
    {
        $this->assertSame(AssessmentQuestion::TYPES, QuestionBankItem::TYPES);
        $this->assertSame(
            ['multiple_choice', 'true_false', 'fill_blank', 'short_answer', 'essay'],
            array_keys(QuestionBankItem::TYPES)
        );

        $this->assertSame('Fill in the Blank', (new QuestionBankItem(['question_type' => 'fill_blank']))->typeLabel());
        $this->assertSame('Legacy type', (new QuestionBankItem(['question_type' => 'legacy_type']))->typeLabel());
    }

    public function test_question_bank_ownership_helpers_separate_shared_and_own_questions(): void
    {
        $shared = new QuestionBankItem(['instructor_id' => null]);
        $own = new QuestionBankItem(['instructor_id' => 7]);
        $foreign = new QuestionBankItem(['instructor_id' => 8]);

        $this->assertTrue($shared->isShared());
        $this->assertFalse($own->isShared());
        $this->assertFalse($shared->isEditableBy(7), 'Nobody edits the shared pool from an instructor account.');
        $this->assertTrue($own->isEditableBy(7));
        $this->assertFalse($foreign->isEditableBy(7));

        $query = QuestionBankItem::query()->visibleTo(7);
        $sql = strtolower($query->toSql());
        $this->assertStringContainsString('"instructor_id" = ?', $sql);
        $this->assertStringContainsString('"instructor_id" is null', $sql);
        $this->assertStringContainsString(' or ', $sql);
        $this->assertSame([7], $query->getBindings());
    }

    public function test_ide_state_helpers_and_tree_conversion_keep_nested_children(): void
    {
        $child = new IdeNode(['type' => 'file', 'name' => 'analysis.py']);
        $child->setRelation('children', collect());
        $parent = new IdeNode(['type' => 'folder', 'name' => 'project']);
        $parent->setRelation('children', collect([$child]));

        $this->assertTrue($parent->isFolder());
        $this->assertFalse($parent->isFile());
        $this->assertTrue($child->isFile());
        $this->assertSame('analysis.py', $parent->toTree()['children'][0]['name']);
        $this->assertTrue((new IdeExecutionLog(['exit_code' => 0]))->wasSuccessful());
        $this->assertFalse((new IdeExecutionLog(['exit_code' => 1]))->wasSuccessful());
    }

    public function test_institution_and_application_status_helpers_are_exact(): void
    {
        $this->assertTrue((new Institution(['status' => 'active']))->isActive());
        $this->assertFalse((new Institution(['status' => 'disabled']))->isActive());

        $pending = new InstructorApplication(['status' => 'pending']);
        $approved = new InstructorApplication(['status' => 'approved']);
        $rejected = new InstructorApplication(['status' => 'rejected']);

        $this->assertTrue($pending->isPending());
        $this->assertFalse($pending->isApproved());
        $this->assertTrue($approved->isApproved());
        $this->assertFalse($approved->isRejected());
        $this->assertTrue($rejected->isRejected());
    }

    #[DataProvider('notificationProvider')]
    public function test_notification_fallback_titles_and_categories(
        string $type,
        string $expectedTitle,
        string $expectedCategory
    ): void {
        $notification = new Notification(['type' => $type]);

        $this->assertSame($expectedTitle, $notification->display_title);
        $this->assertSame($expectedCategory, $notification->category);
    }

    public function test_module_structured_content_handles_arrays_and_legacy_double_encoded_json(): void
    {
        $sections = [['heading' => 'Intro', 'body' => 'Start here.']];
        $module = new ModuleLibraryItem();

        $module->content_sections = $sections;
        $this->assertSame($sections, $module->content_sections);

        $module->setRawAttributes([
            'content_sections' => json_encode(json_encode($sections, JSON_THROW_ON_ERROR), JSON_THROW_ON_ERROR),
            'mcq_questions' => '{invalid json',
        ]);
        $this->assertSame($sections, $module->content_sections);
        $this->assertSame([], $module->mcq_questions);
    }

    #[DataProvider('defaultLearningUnitProvider')]
    public function test_each_default_learning_unit_has_complete_version_metadata(
        int $expectedModuleNumber,
        array $module
    ): void {
        $this->assertSame($expectedModuleNumber, $module['module_no']);
        $this->assertNotSame('', trim($module['title']));
        $this->assertMatchesRegularExpression('/^Year [1-4]$/', $module['year_level']);
        $this->assertNotSame([], $module['versions']);

        foreach ($module['versions'] as $version) {
            $this->assertGreaterThanOrEqual(1, (int) $version['version_no']);
            $this->assertNotSame('', trim($version['version_name']));
            $this->assertNotSame('', trim($version['version_code']));
            $this->assertNotSame('', trim($version['description']));
        }
    }

    public function test_default_module_library_numbers_and_version_codes_are_unique(): void
    {
        $library = ModuleLibraryItem::defaultLibrary();
        $versionCodes = [];

        $this->assertCount(24, $library);
        $this->assertSame(range(1, 24), array_column($library, 'module_no'));

        foreach ($library as $module) {
            foreach ($module['versions'] as $version) {
                $versionCodes[] = $version['version_code'];
            }
        }

        $this->assertSame($versionCodes, array_values(array_unique($versionCodes)));
    }

    /** @return iterable<string, array{float, string}> */
    public static function qualityGradeProvider(): iterable
    {
        yield 'excellent' => [90, 'Excellent'];
        yield 'good' => [80, 'Good'];
        yield 'acceptable' => [70, 'Acceptable'];
        yield 'needs cleaning' => [55, 'Needs cleaning'];
        yield 'poor' => [54.99, 'Poor'];
    }

    /** @return iterable<string, array{string, string, bool}> */
    public static function integrityHoldProvider(): iterable
    {
        yield 'submitted and blocked' => ['submitted', AssessmentSubmission::INTEGRITY_BLOCKED, true];
        yield 'submitted and needs review' => ['submitted', AssessmentSubmission::INTEGRITY_REVIEW_REQUIRED, true];
        yield 'submitted and clear' => ['submitted', AssessmentSubmission::INTEGRITY_CLEAR, false];
        yield 'submitted with no decision' => ['submitted', null, false];
        yield 'kept blocked after review (graded)' => ['graded', AssessmentSubmission::INTEGRITY_BLOCKED, false];
        yield 'late and needs review' => ['late', AssessmentSubmission::INTEGRITY_REVIEW_REQUIRED, false];
        yield 'still in progress' => ['in_progress', AssessmentSubmission::INTEGRITY_BLOCKED, false];
    }

    /** @return iterable<string, array{string, string, string}> */
    public static function notificationProvider(): iterable
    {
        yield 'achievement' => ['achievement_unlocked', 'Achievement unlocked', 'achievement'];
        yield 'mission' => ['mission_completed', 'Mission completed', 'achievement'];
        // Historical assignment_* rows stored before Updates 11 keep their label.
        yield 'historical assignment' => ['assignment_published', 'Assignment update', 'assignment'];
        yield 'assessment' => ['assessment_closed', 'Assessment update', 'assessment'];
        yield 'assessment graded' => ['assessment_graded', 'Assessment update', 'assessment'];
        yield 'assessment held for review' => ['assessment_held_for_review', 'Assessment update', 'assessment'];
        yield 'challenge' => ['exceptional_unlock', 'Notification', 'challenge'];
        yield 'class' => ['class_enrollment', 'Class update', 'class'];
        yield 'module' => ['module_assigned', 'Module update', 'module'];
        yield 'general' => ['account_notice', 'Notification', 'general'];
    }

    /** @return iterable<string, array{int, array<string, mixed>}> */
    public static function defaultLearningUnitProvider(): iterable
    {
        foreach (ModuleLibraryItem::defaultLibrary() as $index => $module) {
            $moduleNumber = $index + 1;

            yield "unit {$moduleNumber}" => [$moduleNumber, $module];
        }
    }
}
