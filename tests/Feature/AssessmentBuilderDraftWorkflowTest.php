<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssessmentBuilderDraftWorkflowTest extends TestCase
{
    private User $instructor;
    private Assessment $assessment;
    private AssessmentQuestion $firstQuestion;
    private AssessmentQuestion $secondQuestion;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');

        $this->createTables();

        DB::table('institutions')->insert([
            'id' => 1,
            'name' => 'Assessment Test Institution',
            'status' => 'active',
        ]);

        $this->instructor = User::create([
            'name' => 'Assessment Instructor',
            'email' => 'assessment-instructor@example.test',
            'password' => 'TestPassword!123',
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
            'institution_id' => 1,
        ]);

        $classId = DB::table('classes')->insertGetId([
            'instructor_id' => $this->instructor->id,
            'institution_id' => 1,
            'name' => 'Data Science 101',
            'class_code' => 'DRAFT01',
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assessment = Assessment::create([
            'class_id' => $classId,
            'created_by' => $this->instructor->id,
            'title' => 'TOS Draft Quiz',
            'status' => 'draft',
            'draft_last_item' => 1,
            'draft_saved_at' => now(),
            'total_items' => 2,
            'total_points' => 2,
        ]);

        $this->firstQuestion = $this->createQuestion(1);
        $this->secondQuestion = $this->createQuestion(2);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('assessment_question_options');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('users');
        Schema::dropIfExists('institutions');

        parent::tearDown();
    }

    public function test_incomplete_question_can_be_saved_as_a_real_draft(): void
    {
        $this->instructorClient()->patch(
            route('instructor.assessments.questions.update', [$this->assessment, $this->firstQuestion]),
            [
                'intent' => 'draft',
                'question_type' => 'multiple_choice',
                'question_text' => 'A partially authored question',
                'points' => 2,
                'is_required' => 1,
                'option_texts' => ['Only one choice so far'],
            ]
        )->assertRedirect(route('instructor.assessments.builder', [
            'assessment' => $this->assessment,
            'item' => 1,
        ]));

        $question = $this->firstQuestion->fresh('options');
        $assessment = $this->assessment->fresh();

        $this->assertSame('A partially authored question', $question->question_text);
        $this->assertCount(1, $question->options);
        $this->assertFalse($question->isAuthoringComplete());
        $this->assertSame(1, $assessment->draft_last_item);
        $this->assertNotNull($assessment->draft_saved_at);
        $this->assertSame('draft', $assessment->status);
    }

    public function test_complete_item_saves_and_resumes_at_the_next_incomplete_tos_item(): void
    {
        $this->instructorClient()->patch(
            route('instructor.assessments.questions.update', [$this->assessment, $this->firstQuestion]),
            [
                'intent' => 'complete_next',
                'question_type' => 'multiple_choice',
                'question_text' => 'Which measure identifies the middle value?',
                'points' => 2,
                'is_required' => 1,
                'option_texts' => ['Mean', 'Median', 'Mode', 'Range'],
                'correct_option' => 1,
            ]
        )->assertRedirect(route('instructor.assessments.builder', [
            'assessment' => $this->assessment,
            'item' => 2,
        ]));

        $this->assertTrue($this->firstQuestion->fresh('options')->isAuthoringComplete());
        $this->assertSame(2, $this->assessment->fresh()->draft_last_item);
    }

    public function test_quick_setup_changes_only_not_started_items(): void
    {
        $this->firstQuestion->update([
            'question_text' => 'Started work',
        ]);

        $this->instructorClient()->patch(
            route('instructor.assessments.questions.quick-setup', $this->assessment),
            [
                'current_question_id' => $this->firstQuestion->id,
                'scope' => 'tos_group',
                'question_type' => 'multiple_choice',
                'points' => 3,
            ]
        )->assertRedirect(route('instructor.assessments.builder', [
            'assessment' => $this->assessment,
            'item' => 1,
        ]));

        $this->assertSame('unconfigured', $this->firstQuestion->fresh()->question_type);
        $this->assertSame('Started work', $this->firstQuestion->fresh()->question_text);
        $this->assertSame(1, $this->firstQuestion->fresh()->points);
        $this->assertSame('multiple_choice', $this->secondQuestion->fresh()->question_type);
        $this->assertSame(3, $this->secondQuestion->fresh()->points);
    }

    private function instructorClient()
    {
        return $this->actingAs($this->instructor)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->instructor),
        ]);
    }

    private function createQuestion(int $itemNumber): AssessmentQuestion
    {
        return AssessmentQuestion::create([
            'assessment_id' => $this->assessment->id,
            'table_of_specification_row_id' => 10,
            'item_number' => $itemNumber,
            'question_type' => 'unconfigured',
            'points' => 1,
            'is_required' => true,
            'topic_title' => 'Descriptive Statistics',
            'learning_objective' => 'Choose and interpret the appropriate measure of central tendency.',
            'bloom_level' => 'Apply',
            'difficulty_slug' => 'university-student',
        ]);
    }

    private function createTables(): void
    {
        Schema::create('institutions', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('status')->default('active');
        });
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->string('name');
            $table->string('class_code', 8);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('table_of_specification_id')->nullable();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('title');
            $table->string('status', 30)->default('draft');
            $table->unsignedInteger('draft_last_item')->nullable();
            $table->timestamp('draft_saved_at')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_points')->default(0);
            $table->timestamps();
        });
        Schema::create('assessment_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('table_of_specification_row_id')->nullable();
            $table->unsignedBigInteger('ilo_id')->nullable();
            $table->unsignedInteger('item_number');
            $table->string('question_type', 40)->default('unconfigured');
            $table->longText('question_text')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->boolean('is_required')->default(true);
            $table->text('correct_answer')->nullable();
            $table->text('answer_explanation')->nullable();
            $table->longText('rubric_text')->nullable();
            $table->string('topic_title');
            $table->string('subtopic_title')->nullable();
            $table->text('learning_objective')->nullable();
            $table->string('bloom_level')->nullable();
            $table->string('difficulty_slug')->nullable();
            $table->timestamps();
        });
        Schema::create('assessment_question_options', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assessment_question_id');
            $table->string('option_label', 10)->nullable();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedInteger('order_index')->default(0);
            $table->timestamps();
        });
    }
}
