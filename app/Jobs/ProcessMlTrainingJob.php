<?php

namespace App\Jobs;

use App\Models\ClassRoom;
use App\Models\TrainingJob;
use App\Services\CompetencyMonitoringService;
use App\Services\HybridMl\HybridMlRunnerService;
use App\Services\HybridMl\ModelStorageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

class ProcessMlTrainingJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 900;
    public int $uniqueFor = 1200;

    public function __construct(
        public readonly int $trainingJobId,
        public readonly ?string $trainingUuid = null,
    ) {
        $this->onConnection((string) config('hybrid_ml.queue_connection', 'database'));
        $this->onQueue((string) config('hybrid_ml.queue_name', 'machine-learning'));
    }

    public function uniqueId(): string
    {
        // The UUID keeps the lock unique even after the database is reset and
        // ids start again from 1; an id-only lock could silently skip a dispatch.
        $uuid = $this->trainingUuid
            ?? (string) TrainingJob::query()->whereKey($this->trainingJobId)->value('uuid');

        return 'hybrid-ml-training-'.$this->trainingJobId.'-'.$uuid;
    }

    public function backoff(): array
    {
        return [15, 60];
    }

    public function handle(HybridMlRunnerService $runner, ModelStorageService $storage, CompetencyMonitoringService $competencies): void
    {
        $training = TrainingJob::query()->with('datasetVersion')->find($this->trainingJobId);
        if (! $training || $training->isTerminal()) {
            return;
        }

        $attempt = max(1, $this->attempts());
        $training->update([
            'status' => TrainingJob::STATUS_RUNNING,
            'progress' => max(1, (int) $training->progress),
            'stage' => 'Starting the trusted training environment',
            'attempt_number' => $attempt,
            'next_retry_at' => null,
            'started_at' => $training->started_at ?: now(),
            'finished_at' => null,
            'error_message' => null,
        ]);

        $temporaryDirectory = null;
        $started = microtime(true);
        try {
            if (! $training->datasetVersion) {
                throw new RuntimeException('The dataset version linked to this training job is missing.');
            }

            $datasetPath = storage_path('app/'.$training->datasetVersion->storage_path);
            $execution = $runner->train(
                $datasetPath,
                (array) $training->configuration,
                function (int $progress, string $stage) use ($training): void {
                    TrainingJob::query()->whereKey($training->id)->update([
                        'progress' => max(1, min(99, $progress)),
                        'stage' => substr($stage, 0, 160),
                        'updated_at' => now(),
                    ]);
                }
            );
            $temporaryDirectory = dirname($execution['output_directory']);
            $training->update(['duration_ms' => (int) round((microtime(true) - $started) * 1000)]);
            $storage->storeCompletedTraining(
                $training->fresh(['dataset', 'userDataset', 'datasetVersion']),
                $execution['result'],
                $execution['output_directory']
            );

            // Training completion must stay successful even if a concurrent
            // dashboard refresh temporarily prevents the derived competency
            // snapshot from updating. The instructor can recalculate later.
            if ($training->class_id) {
                try {
                    $class = ClassRoom::query()->find($training->class_id);
                    if ($class) {
                        $competencies->refreshClass($class);
                    }
                } catch (Throwable $refreshException) {
                    report($refreshException);
                }
            }
        } catch (Throwable $exception) {
            $message = trim($exception->getMessage());
            $willRetry = $attempt < $this->tries;
            TrainingJob::query()->whereKey($training->id)->update([
                'status' => $willRetry ? TrainingJob::STATUS_RETRYING : TrainingJob::STATUS_FAILED,
                'stage' => $willRetry ? 'Training will retry automatically' : 'Training failed',
                'attempt_number' => $attempt,
                'next_retry_at' => $willRetry ? now()->addSeconds($this->retryDelaySeconds($attempt)) : null,
                'error_message' => substr($message !== '' ? $message : 'The training worker failed.', 0, 1200),
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'finished_at' => $willRetry ? null : now(),
                'updated_at' => now(),
            ]);
            throw $exception;
        } finally {
            if ($temporaryDirectory && is_dir($temporaryDirectory)) {
                File::deleteDirectory($temporaryDirectory);
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        $message = trim((string) ($exception?->getMessage() ?? ''));
        TrainingJob::query()
            ->whereKey($this->trainingJobId)
            ->whereNotIn('status', [TrainingJob::STATUS_COMPLETED, TrainingJob::STATUS_CANCELLED])
            ->update([
                'status' => TrainingJob::STATUS_FAILED,
                'stage' => 'Training failed',
                'attempt_number' => max(1, $this->attempts()),
                'next_retry_at' => null,
                'error_message' => substr($message !== '' ? $message : 'The queue worker could not complete the training job.', 0, 1200),
                'finished_at' => now(),
                'updated_at' => now(),
            ]);
    }

    private function retryDelaySeconds(int $attempt): int
    {
        $delays = $this->backoff();
        $index = max(0, $attempt - 1);
        $lastDelay = $delays === [] ? 1 : $delays[array_key_last($delays)];

        return max(1, (int) ($delays[$index] ?? $lastDelay));
    }
}
