<?php

namespace Tests\Feature\Regression\Concerns;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\TestCase as CodingTestCase;
use App\Models\User;
use App\Services\ChallengePathUnlockService;
use App\Services\GamificationService;
use App\Services\PythonSandboxService;
use App\Support\AuthSessionFingerprint;
use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;

/**
 * Hand-built schema + fixtures for the coding-challenge regression tests
 * (DS-12 .. DS-15). Mirrors the columns the real migrations create; the
 * default suite does not run migrations.
 */
trait BuildsCodingChallengeWorkflow
{
    protected User $student;
    protected User $otherStudent;
    protected ChallengeCategory $category;
    protected Challenge $challenge;
    protected string $slug = 'beginner';

    /** @var array<int, string> */
    private array $codingWorkflowTables = [
        'coding_question_attempt_archives',
        'coding_challenge_retakes',
        'coding_question_attempts',
        'coding_submissions',
        'test_cases',
        'coding_questions',
        'challenges',
        'challenge_categories',
        'users',
    ];

    protected function bootCodingWorkflow(): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        config()->set('session.driver', 'array');
        $this->createCodingWorkflowTables();
        $this->seedCodingWorkflowActors();
        $this->stubCodingCollaborators();
    }

    protected function dropCodingWorkflowTables(): void
    {
        foreach ($this->codingWorkflowTables as $table) {
            Schema::dropIfExists($table);
        }
    }

    protected function seedCodingWorkflowActors(): void
    {
        $suffix = substr(md5(static::class . microtime()), 0, 8);
        $this->student = User::create([
            'name' => 'Coding Student',
            'email' => "coding-student-{$suffix}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
        $this->otherStudent = User::create([
            'name' => 'Other Coding Student',
            'email' => "coding-other-{$suffix}@example.test",
            'password' => 'TestPassword!123',
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);

        $this->category = ChallengeCategory::firstOrCreate(
            ['slug' => $this->slug],
            ['name' => 'Beginner', 'target_audience' => 'Everyone', 'description' => 'Regression path', 'order_index' => 1]
        );
    }

    /**
     * Path unlocking and achievements are other workflows with their own
     * tables; they are stubbed at the service boundary.
     */
    protected function stubCodingCollaborators(): void
    {
        $unlock = Mockery::mock(ChallengePathUnlockService::class);
        $unlock->shouldReceive('lockInfo')->andReturn(['unlocked' => true]);
        $unlock->shouldReceive('notifyExceptionalUnlocks')->andReturn([]);
        $this->app->instance(ChallengePathUnlockService::class, $unlock);

        $gamification = Mockery::mock(GamificationService::class);
        $gamification->shouldReceive('awardForCodingSubmission')->andReturn([]);
        $this->app->instance(GamificationService::class, $gamification);
    }

    protected function fakeSandbox(Closure $handler): FakePythonSandbox
    {
        $fake = new FakePythonSandbox($handler);
        $this->app->instance(PythonSandboxService::class, $fake);

        return $fake;
    }

    protected function studentClient()
    {
        return $this->clientFor($this->student);
    }

    protected function clientFor(User $user)
    {
        return $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $questions each may carry `tests`: list of [input, expected, is_hidden]
     * @return array<int, CodingQuestion>
     */
    protected function makeCodingChallenge(array $questions, array $attributes = []): array
    {
        $this->challenge = Challenge::create(array_merge([
            'challenge_category_id' => $this->category->id,
            'title' => 'Regression Coding Challenge ' . substr(md5(microtime()), 0, 6),
            'description' => 'Regression',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'order_index' => 1,
            'is_coding_challenge' => true,
            'is_active' => true,
        ], $attributes));

        $created = [];
        foreach (array_values($questions) as $index => $definition) {
            $tests = $definition['tests'] ?? [['', '42', false]];
            unset($definition['tests']);

            $question = CodingQuestion::create(array_merge([
                'challenge_id' => $this->challenge->id,
                'problem_description' => 'Problem ' . ($index + 1),
                'language' => 'python',
                'starter_code' => '# starter ' . ($index + 1),
                'order_index' => $index + 1,
                'time_limit_seconds' => 600,
                'base_xp' => 100,
            ], $definition));

            foreach (array_values($tests) as $testIndex => [$input, $expected, $hidden]) {
                CodingTestCase::create([
                    'coding_question_id' => $question->id,
                    'input' => $input,
                    'expected_output' => $expected,
                    'is_hidden' => $hidden,
                    'order_index' => $testIndex + 1,
                ]);
            }

            $created[] = $question;
        }

        return $created;
    }

    protected function codingUrl(string $name, ?CodingQuestion $question = null): string
    {
        $parameters = ['slug' => $this->slug, 'challenge' => $this->challenge->id];
        if ($question) {
            $parameters['question'] = $question->id;
        }

        return route('challenges.coding.' . $name, $parameters);
    }

    private function createCodingWorkflowTables(): void
    {
        $this->dropCodingWorkflowTables();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->unsignedInteger('xp')->default(0);
            $table->unsignedInteger('streak')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::create('challenge_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('target_audience');
            $table->text('description');
            $table->text('icon_svg')->nullable();
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('challenges', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('challenge_category_id');
            $table->string('content_code', 64)->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('time_limit_seconds')->default(600);
            $table->integer('base_xp')->default(100);
            $table->integer('order_index');
            $table->tinyInteger('is_coding_challenge')->default(0);
            $table->unsignedInteger('version_no')->default(1);
            $table->string('version_name')->nullable();
            $table->string('version_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('coding_questions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->text('problem_description');
            $table->string('language', 20)->default('python');
            $table->text('starter_code')->nullable();
            $table->integer('order_index')->default(0);
            $table->integer('time_limit_seconds')->default(1800);
            $table->integer('base_xp')->default(100);
            $table->longText('source_requirements')->nullable();
            $table->timestamps();
        });
        Schema::create('test_cases', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('coding_question_id');
            $table->text('input')->nullable();
            $table->text('expected_output');
            $table->boolean('is_hidden')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
        Schema::create('coding_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coding_question_id');
            $table->text('code');
            $table->string('language', 20)->default('python');
            $table->string('status')->default('pending');
            $table->integer('tests_passed')->default(0);
            $table->integer('tests_total')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->integer('time_taken_seconds')->default(0);
            $table->longText('test_results')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('voided')->default(false);
            $table->string('attempt_token', 64)->nullable();
            $table->unsignedInteger('attempt_generation')->nullable();
            $table->string('void_reason', 40)->nullable();
            $table->longText('grader_diagnostics')->nullable();
            $table->timestamps();
        });
        Schema::create('coding_question_attempts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coding_question_id');
            $table->dateTime('started_at');
            $table->boolean('expired')->default(false);
            $table->string('attempt_token', 64)->nullable();
            $table->unsignedInteger('generation')->default(1);
            $table->timestamps();
            $table->unique(['user_id', 'coding_question_id']);
        });
        Schema::create('coding_challenge_retakes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedTinyInteger('retake_count')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'challenge_id']);
        });
        Schema::create('coding_question_attempt_archives', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('coding_question_id');
            $table->string('attempt_token', 64)->nullable();
            $table->unsignedInteger('generation')->default(1);
            $table->dateTime('started_at')->nullable();
            $table->boolean('expired')->default(false);
            $table->dateTime('retaken_at')->nullable();
            $table->timestamps();
        });
    }
}
