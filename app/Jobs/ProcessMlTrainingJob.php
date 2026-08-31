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

    public function __construct(public readonly int $trainingJobId)
    {
        $this->onConnection((string) config('hybrid_ml.queue_connection', 'database'));
        $this->onQueue((string) config('hybrid_ml.queue_name', 'machine-learning'));
    }

    public function uniqueId(): string
    {
        return 'hybrid-ml-training-'.$this->trainingJobId;
    }

    public function backoff(): array
    {
        return [15, 60];
    }

    public function handle(HybridMlRunnerService $runner, ModelStorageService $storage, CompetencyMonitoringService $competencies): void
    {
        $training = TrainingJob::query()->with('datasetVersion')->find($this->trainingJobId);
        if (! $training || $training->status === 'completed' || $training->status === 'cancelled') {
            return;
        }
        if (! $training->datasetVersion) {
            throw new RuntimeException('The dataset version linked to this training job is missing.');
        }

        $datasetPath = storage_path('app/'.$training->datasetVersion->storage_path);
        $training->update([
            'status' => 'running',
            'progress' => max(1, (int) $training->progress),
            'stage' => 'Starting the trusted training environment',
            'started_at' => $training->started_at ?: now(),
            'error_message' => null,
        ]);

        $temporaryDirectory = null;
        $started = microtime(true);
        try {
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
            TrainingJob::query()->whereKey($training->id)->update([
                'status' => 'failed',
                'stage' => $this->attempts() < $this->tries ? 'Training failed; a retry may be attempted' : 'Training failed',
                'error_message' => substr($message !== '' ? $message : 'The training worker failed.', 0, 1200),
                'duration_ms' => (int) round((microtime(true) - $started) * 1000),
                'finished_at' => now(),
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
        TrainingJob::query()->whereKey($this->trainingJobId)->update([
            'status' => 'failed',
            'stage' => 'Training failed',
            'error_message' => substr($message !== '' ? $message : 'The queue worker could not complete the training job.', 0, 1200),
            'finished_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
