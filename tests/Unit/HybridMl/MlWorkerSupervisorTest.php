<?php

namespace Tests\Unit\HybridMl;

use App\Models\DatasetVersion;
use App\Models\MlDataset;
use App\Models\TrainingJob;
use App\Models\User;
use App\Services\HybridMl\MlWorkerSupervisor;
use App\Support\BackgroundArtisanLauncher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class MlWorkerSupervisorTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, array<int, string>> */
    private array $launches = [];

    private bool $launchResult = true;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config([
            'cache.default' => 'array',
            'hybrid_ml.queue_connection' => 'machine_learning',
            'hybrid_ml.queue_name' => 'machine-learning',
            'queue.connections.machine_learning.driver' => 'database',
            'hybrid_ml.auto_start_worker' => true,
        ]);

        $test = $this;
        $this->app->instance(BackgroundArtisanLauncher::class, new class($test) extends BackgroundArtisanLauncher {
            public function __construct(private MlWorkerSupervisorTest $test)
            {
            }

            public function launch(array $arguments): bool
            {
                return $this->test->recordLaunch($arguments);
            }
        });
    }

    public function recordLaunch(array $arguments): bool
    {
        $this->launches[] = $arguments;

        return $this->launchResult;
    }

    public function test_it_starts_a_short_lived_worker_when_none_is_running(): void
    {
        $training = $this->training();

        $result = app(MlWorkerSupervisor::class)->ensureWorkerFor($training);

        $this->assertSame('worker_starting', $result['state']);
        $this->assertCount(1, $this->launches);
        $this->assertSame(['queue:work', 'machine_learning', '--queue=machine-learning', '--stop-when-empty'], array_slice($this->launches[0], 0, 4));

        // Polling again does not start a second worker right away.
        app(MlWorkerSupervisor::class)->ensureWorkerFor($training);
        $this->assertCount(1, $this->launches);
    }

    public function test_it_does_nothing_when_a_worker_heartbeat_is_fresh(): void
    {
        app(MlWorkerSupervisor::class)->recordHeartbeat();

        $result = app(MlWorkerSupervisor::class)->ensureWorkerFor($this->training());

        $this->assertSame('worker_running', $result['state']);
        $this->assertSame([], $this->launches);
    }

    public function test_it_reports_a_missing_worker_when_it_cannot_start_one(): void
    {
        $this->launchResult = false;

        $result = app(MlWorkerSupervisor::class)->ensureWorkerFor($this->training());

        $this->assertSame('worker_missing', $result['state']);
        $this->assertStringContainsString('start-ml-worker.bat', (string) $result['message']);

        // The next poll is throttled but still reports the failed start.
        $this->assertSame('worker_missing', app(MlWorkerSupervisor::class)->ensureWorkerFor($this->training())['state']);
        $this->assertCount(1, $this->launches);
    }

    public function test_finished_jobs_and_sync_queues_are_left_alone(): void
    {
        $this->assertSame('ok', app(MlWorkerSupervisor::class)->ensureWorkerFor($this->training(['status' => TrainingJob::STATUS_COMPLETED]))['state']);

        config(['queue.connections.machine_learning.driver' => 'sync']);
        $this->assertSame('ok', app(MlWorkerSupervisor::class)->ensureWorkerFor($this->training())['state']);
        $this->assertSame([], $this->launches);
    }

    public function test_an_orphaned_queued_job_is_put_back_on_the_queue(): void
    {
        $training = $this->training();
        $training->forceFill(['created_at' => now()->subMinutes(2)])->save();
        $this->assertSame(0, DB::table('jobs')->count());

        app(MlWorkerSupervisor::class)->ensureWorkerFor($training->fresh());

        $rows = DB::table('jobs')->where('queue', 'machine-learning')->get();
        $this->assertCount(1, $rows);
        $this->assertStringContainsString('ProcessMlTrainingJob', (string) $rows[0]->payload);

        // A second check sees the queue row and does not add another one.
        Cache::flush();
        app(MlWorkerSupervisor::class)->ensureWorkerFor($training->fresh());
        $this->assertSame(1, DB::table('jobs')->where('queue', 'machine-learning')->count());
    }

    private function training(array $overrides = []): TrainingJob
    {
        $user = User::query()->firstOrCreate(['email' => 'supervisor@example.test'], [
            'name' => 'Supervisor Student', 'password' => 'Password!123', 'role' => User::ROLE_USER, 'status' => 'active',
        ]);
        $dataset = MlDataset::query()->create([
            'slug' => 'supervisor-'.Str::random(6),
            'name' => 'Supervisor dataset',
            'storage_path' => 'ml/system/datasets/iris/v1/dataset.csv',
            'row_count' => 20,
            'column_count' => 2,
            'target_column' => 'target',
            'problem_type' => 'classification',
            'feature_list' => ['a'],
            'metadata' => [],
            'version_label' => 'v1',
            'checksum_sha256' => str_repeat('a', 64),
            'is_active' => true,
        ]);
        $version = DatasetVersion::query()->create([
            'dataset_id' => $dataset->id,
            'version_number' => 1,
            'version_label' => 'v1',
            'storage_path' => 'ml/system/datasets/iris/v1/dataset.csv',
            'checksum_sha256' => str_repeat('a', 64),
            'row_count' => 20,
            'column_count' => 2,
            'schema_profile' => [],
        ]);

        return TrainingJob::create(array_merge([
            'user_id' => $user->id,
            'dataset_id' => $dataset->id,
            'dataset_version_id' => $version->id,
            'uuid' => (string) Str::uuid(),
            'model_name' => 'Supervisor test',
            'problem_type' => 'classification',
            'algorithm_key' => 'logistic_regression',
            'status' => TrainingJob::STATUS_QUEUED,
            'progress' => 0,
            'stage' => 'Waiting for the machine-learning worker',
            'configuration' => ['target_column' => 'target'],
        ], $overrides));
    }
}
