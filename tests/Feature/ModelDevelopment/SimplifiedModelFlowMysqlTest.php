<?php

namespace Tests\Feature\ModelDevelopment;

use App\Jobs\ProcessMlTrainingJob;
use App\Models\MlDataset;
use App\Models\MlModel;
use App\Models\TrainingJob;
use App\Models\User;
use App\Services\HybridMl\HybridMlRunnerService;
use App\Services\HybridMl\MlWorkerSupervisor;
use App\Support\AuthSessionFingerprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

/**
 * The four-step Model Development flow through the real routes, middleware,
 * controllers and Blade views, on MySQL/MariaDB.
 *
 * Skipped unless DS_MYSQL_TEST_DB names a migrated database in which
 * `php artisan db:seed --class=HybridMlSeeder` has been run (optional:
 * DS_MYSQL_TEST_HOST, DS_MYSQL_TEST_PORT, DS_MYSQL_TEST_USER,
 * DS_MYSQL_TEST_PASSWORD; defaults 127.0.0.1/3306/ds/ds). Python is not needed:
 * the trusted runner is replaced by a double.
 */
class SimplifiedModelFlowMysqlTest extends TestCase
{
    private ?User $student = null;
    private ?MlDataset $cancer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $database = getenv('DS_MYSQL_TEST_DB') ?: '';
        if ($database === '') {
            $this->markTestSkipped('Set DS_MYSQL_TEST_DB to a migrated and ML-seeded MySQL/MariaDB database.');
        }

        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.host', getenv('DS_MYSQL_TEST_HOST') ?: '127.0.0.1');
        config()->set('database.connections.mysql.port', getenv('DS_MYSQL_TEST_PORT') ?: '3306');
        config()->set('database.connections.mysql.database', $database);
        config()->set('database.connections.mysql.username', getenv('DS_MYSQL_TEST_USER') ?: 'ds');
        config()->set('database.connections.mysql.password', getenv('DS_MYSQL_TEST_PASSWORD') ?: 'ds');
        DB::purge('mysql');

        $this->cancer = MlDataset::query()->where('slug', 'breast-cancer-wisconsin')->first();
        if (! $this->cancer) {
            $this->markTestSkipped('Run the HybridMlSeeder in the test database first.');
        }

        $this->student = User::forceCreate([
            'name' => 'Flow Student',
            'email' => 'flow-'.Str::random(10).'@example.test',
            'password' => bcrypt('secret-password'),
            'role' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

        Queue::fake();
        $supervisor = Mockery::mock(MlWorkerSupervisor::class);
        $supervisor->shouldReceive('ensureWorkerFor')->andReturn(['state' => 'ok', 'message' => null]);
        $this->app->instance(MlWorkerSupervisor::class, $supervisor);
    }

    protected function tearDown(): void
    {
        if ($this->student) {
            DB::table('prediction_logs')->where('user_id', $this->student->id)->delete();
            DB::table('training_jobs')->where('user_id', $this->student->id)->delete();
            DB::table('users')->where('id', $this->student->id)->delete();
        }
        Mockery::close();

        parent::tearDown();
    }

    public function test_the_set_up_page_preselects_everything_a_beginner_needs(): void
    {
        $response = $this->student()->get(route('student.model-development.wizard', [
            'dataset_type' => 'system',
            'dataset_id' => $this->cancer->id,
            'step' => 6, // a bookmark from the ten-stage version
        ]));

        $response->assertOk()
            ->assertSee('What should the model predict?')
            ->assertSee('Pick the best one for me')
            ->assertSee('Average cell size (radius)')
            ->assertSee('role="tooltip"', false)
            ->assertDontSee('data-workflow-step', false);
    }

    public function test_training_needs_only_a_name_an_answer_and_clues(): void
    {
        $response = $this->student()->post(route('student.model-development.training.store'), [
            'dataset_type' => 'system',
            'dataset_id' => $this->cancer->id,
            'model_name' => 'Tumour checker',
            'target_column' => 'diagnosis',
            'features' => ['mean_radius', 'mean_texture', 'worst_area'],
        ]);

        $job = TrainingJob::query()->where('user_id', $this->student->id)->latest('id')->firstOrFail();
        $response->assertRedirect(route('student.model-development.training.show', $job));
        Queue::assertPushed(ProcessMlTrainingJob::class);

        $this->assertSame('classification', $job->problem_type);
        $this->assertSame('auto_classification', $job->algorithm_key);
        $configuration = (array) $job->configuration;
        $this->assertTrue($configuration['tune']);
        $this->assertSame(0.2, (float) $configuration['test_size']);
        $this->assertSame(5, (int) $configuration['cross_validation']);
        $this->assertFalse($configuration['generate_extended_charts']);

        $this->student()->get(route('student.model-development.training.show', $job))
            ->assertOk()
            ->assertSee('Comparing algorithms')
            ->assertSee('not a simulated timer');
    }

    public function test_without_an_answer_column_the_model_finds_groups(): void
    {
        $this->student()->post(route('student.model-development.training.store'), [
            'dataset_type' => 'system',
            'dataset_id' => $this->cancer->id,
            'model_name' => 'Tumour groups',
            'target_column' => '',
            'features' => ['mean_radius', 'mean_texture'],
        ])->assertSessionHasNoErrors();

        $job = TrainingJob::query()->where('user_id', $this->student->id)->latest('id')->firstOrFail();
        $this->assertSame('clustering', $job->problem_type);
        $this->assertSame('kmeans', $job->algorithm_key);
        $this->assertFalse((bool) data_get($job->configuration, 'tune'));
    }

    public function test_validation_errors_come_back_to_the_set_up_page(): void
    {
        $this->student()
            ->from(route('student.model-development.wizard', ['dataset_type' => 'system', 'dataset_id' => $this->cancer->id]))
            ->post(route('student.model-development.training.store'), [
                'dataset_type' => 'system',
                'dataset_id' => $this->cancer->id,
                'model_name' => 'Cheater',
                'target_column' => 'diagnosis',
                'features' => ['diagnosis'],
            ])
            ->assertSessionHasErrors('features');

        $this->assertSame(0, TrainingJob::query()->where('user_id', $this->student->id)->count());
    }

    public function test_results_and_prediction_pages_speak_plain_language(): void
    {
        $model = $this->referenceModel();

        $this->student()
            ->get(route('student.model-development.models.show', ['model' => $model, 'step' => 'evaluate']))
            ->assertOk()
            ->assertSee('times out of 100')
            ->assertSee('The two kinds of mistake')
            ->assertSee('cancers the model called harmless');

        // "save" was the tenth stage; the bookmark must still open something useful.
        $this->student()
            ->get(route('student.model-development.models.show', ['model' => $model, 'step' => 'save']))
            ->assertOk()
            ->assertSee('Is this tumour breast cancer?')
            ->assertSee('Example A')
            ->assertSee('Largest cell size (radius)');
    }

    public function test_a_prediction_is_answered_in_words_on_the_same_page(): void
    {
        $model = $this->referenceModel();
        $runner = Mockery::mock(HybridMlRunnerService::class);
        $runner->shouldReceive('predict')->once()->andReturn([
            'ok' => true,
            'predicted_value' => 'malignant',
            'probabilities' => ['benign' => 3.2, 'malignant' => 96.8],
            'confidence' => 96.8,
            'top_features' => [],
        ]);
        $this->app->instance(HybridMlRunnerService::class, $runner);

        $response = $this->student()->post(route('student.model-development.models.predict', $model), [
            'input_values' => ['worst_radius' => '25.38', 'worst_area' => '2019'],
            'known_answer' => 'malignant',
        ]);

        $response->assertRedirect(route('student.model-development.models.show', ['model' => $model, 'step' => 'predict']).'#answer');
        $flashed = session('prediction_result');
        $this->assertSame('Likely breast cancer', $flashed['described']['headline']);
        $this->assertSame('Cancer (malignant)', $flashed['known_answer']);
        $this->assertTrue($flashed['agrees']);
        $this->assertSame(1, DB::table('prediction_logs')->where('user_id', $this->student->id)->count());

        $this->student()
            ->withSession([
                AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->student),
                'prediction_result' => $flashed,
            ])
            ->get(route('student.model-development.models.show', ['model' => $model, 'step' => 'predict']))
            ->assertOk()
            ->assertSee('Likely breast cancer')
            ->assertSee('Very sure, about 97 out of 100')
            ->assertSee('The model got this one right.')
            ->assertSee('not a medical test');
    }

    public function test_the_start_page_lists_questions_instead_of_dataset_jargon(): void
    {
        $this->student()->get(route('student.model-development.index'))
            ->assertOk()
            ->assertSee('Is this tumour breast cancer?')
            ->assertSee('Choose data')
            ->assertDontSee('ten short steps');
    }

    /** Signed in the way the login controller leaves a session, so the "active" middleware accepts it. */
    private function student(): static
    {
        return $this->actingAs($this->student)
            ->withSession([AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($this->student)]);
    }

    private function referenceModel(): MlModel
    {
        return MlModel::query()
            ->where('dataset_id', $this->cancer->id)
            ->where('pipeline_type', 'system')
            ->whereNotNull('current_version_id')
            ->orderBy('id')
            ->firstOrFail();
    }
}
