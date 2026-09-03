<?php

namespace Tests\Unit\HybridMl;

use App\Jobs\ProcessMlTrainingJob;
use App\Models\DatasetVersion;
use App\Models\TrainingJob;
use App\Services\CompetencyMonitoringService;
use App\Services\HybridMl\HybridMlRunnerService;
use App\Services\HybridMl\ModelStorageService;
use Illuminate\Contracts\Queue\Job as QueueJobContract;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class ProcessMlTrainingJobStateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('dataset_versions', function (Blueprint $table): void {
            $table->id();
            $table->string('storage_path');
            $table->timestamps();
        });

        Schema::create('training_jobs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('dataset_id')->nullable();
            $table->unsignedBigInteger('user_dataset_id')->nullable();
            $table->unsignedBigInteger('dataset_version_id')->nullable();
            $table->unsignedBigInteger('ml_model_id')->nullable();
            $table->string('uuid', 36);
            $table->string('model_name');
            $table->string('problem_type');
            $table->string('algorithm_key');
            $table->string('status')->default(TrainingJob::STATUS_QUEUED);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('stage');
            $table->unsignedSmallInteger('attempt_number')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->longText('configuration');
            $table->longText('result')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('training_jobs');
        Schema::dropIfExists('dataset_versions');
        Mockery::close();

        parent::tearDown();
    }

    public function test_first_failure_is_retrying_and_has_no_terminal_timestamp(): void
    {
        $training = $this->createTraining();
        $queued = $this->queuedJob($training, 1);
        [$runner, $storage, $competencies] = $this->dependencies();
        $runner->shouldReceive('train')->once()->andThrow(new RuntimeException('Transient runner failure.'));

        try {
            $queued->handle($runner, $storage, $competencies);
            $this->fail('A runner failure must be returned to the queue worker.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Transient runner failure.', $exception->getMessage());
        }

        $training->refresh();
        $this->assertSame(TrainingJob::STATUS_RETRYING, $training->status);
        $this->assertSame(1, $training->attempt_number);
        $this->assertNull($training->finished_at);
        $this->assertNotNull($training->next_retry_at);
        $this->assertTrue($training->next_retry_at->isFuture());
        $this->assertFalse($training->isTerminal());
    }

    public function test_retry_start_clears_stale_terminal_fields_then_final_failure_is_terminal(): void
    {
        $training = $this->createTraining([
            'status' => TrainingJob::STATUS_RETRYING,
            'attempt_number' => 1,
            'next_retry_at' => now()->subSecond(),
            'finished_at' => now()->subMinute(),
            'error_message' => 'First attempt failed.',
        ]);
        $queued = $this->queuedJob($training, 2);
        [$runner, $storage, $competencies] = $this->dependencies();
        $runner->shouldReceive('train')->once()->andReturnUsing(function () use ($training): never {
            $running = $training->fresh();
            $this->assertSame(TrainingJob::STATUS_RUNNING, $running->status);
            $this->assertSame(2, $running->attempt_number);
            $this->assertNull($running->next_retry_at);
            $this->assertNull($running->finished_at);
            $this->assertNull($running->error_message);

            throw new RuntimeException('Permanent runner failure.');
        });

        try {
            $queued->handle($runner, $storage, $competencies);
            $this->fail('The final runner failure must be returned to the queue worker.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Permanent runner failure.', $exception->getMessage());
        }

        $training->refresh();
        $this->assertSame(TrainingJob::STATUS_FAILED, $training->status);
        $this->assertSame(2, $training->attempt_number);
        $this->assertNull($training->next_retry_at);
        $this->assertNotNull($training->finished_at);
        $this->assertTrue($training->isTerminal());
    }

    public function test_completed_and_cancelled_jobs_are_never_reprocessed(): void
    {
        foreach ([TrainingJob::STATUS_COMPLETED, TrainingJob::STATUS_CANCELLED] as $status) {
            $training = $this->createTraining(['status' => $status, 'finished_at' => now()]);
            [$runner, $storage, $competencies] = $this->dependencies();
            $runner->shouldNotReceive('train');

            (new ProcessMlTrainingJob($training->id))->handle($runner, $storage, $competencies);

            $this->assertSame($status, $training->fresh()->status);
        }
    }

    public function test_artifact_storage_failure_removes_the_handed_off_training_directory(): void
    {
        $training = $this->createTraining();
        $queued = $this->queuedJob($training, 1);
        [$runner, $storage, $competencies] = $this->dependencies();
        $temporary = storage_path('app/ml/tmp/train-storage-test-'.Str::uuid());
        $output = $temporary.DIRECTORY_SEPARATOR.'output';
        File::ensureDirectoryExists($output);

        $runner->shouldReceive('train')->once()->andReturn([
            'result' => ['ok' => true],
            'output_directory' => $output,
            'stdout' => '',
            'stderr' => '',
        ]);
        $storage->shouldReceive('storeCompletedTraining')
            ->once()
            ->andThrow(new RuntimeException('Artifact storage failed.'));

        try {
            $queued->handle($runner, $storage, $competencies);
            $this->fail('Artifact storage failures must be returned to the queue worker.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Artifact storage failed.', $exception->getMessage());
        } finally {
            $this->assertDirectoryDoesNotExist($temporary);
            File::deleteDirectory($temporary);
        }
    }

    private function createTraining(array $overrides = []): TrainingJob
    {
        $version = DatasetVersion::create([
            'storage_path' => 'ml/tests/dataset.csv',
        ]);

        return TrainingJob::create(array_merge([
            'user_id' => 1,
            'dataset_version_id' => $version->id,
            'uuid' => (string) Str::uuid(),
            'model_name' => 'Retry state test',
            'problem_type' => 'classification',
            'algorithm_key' => 'logistic_regression',
            'status' => TrainingJob::STATUS_QUEUED,
            'progress' => 0,
            'stage' => 'Waiting for the machine-learning worker',
            'configuration' => ['target_column' => 'target'],
        ], $overrides));
    }

    private function queuedJob(TrainingJob $training, int $attempt): ProcessMlTrainingJob
    {
        $contract = Mockery::mock(QueueJobContract::class);
        $contract->shouldReceive('attempts')->atLeast()->once()->andReturn($attempt);

        $queued = new ProcessMlTrainingJob($training->id);
        $queued->setJob($contract);
        return $queued;
    }

    private function dependencies(): array
    {
        return [
            Mockery::mock(HybridMlRunnerService::class),
            Mockery::mock(ModelStorageService::class),
            Mockery::mock(CompetencyMonitoringService::class),
        ];
    }
}
