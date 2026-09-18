<?php

namespace App\Services\HybridMl;

use App\Jobs\ProcessMlTrainingJob;
use App\Models\TrainingJob;
use App\Support\BackgroundArtisanLauncher;
use Illuminate\Bus\UniqueLock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Makes sure a queued training job is actually picked up.
 *
 * Training runs on the "machine_learning" queue. When nobody started
 * start-ml-worker.bat, jobs used to sit at 0% forever. The supervisor notices
 * that no worker is alive and starts a short-lived one that exits when the
 * queue is empty. It also re-queues a training job whose queue row vanished.
 */
class MlWorkerSupervisor
{
    public const HEARTBEAT_KEY = 'datasensei:ml-worker:heartbeat';
    private const LAUNCH_THROTTLE_KEY = 'datasensei:ml-worker:launch-throttle';
    private const LAUNCH_RESULT_KEY = 'datasensei:ml-worker:launch-result';
    private const REQUEUE_THROTTLE_PREFIX = 'datasensei:ml-worker:requeue:';

    private static int $lastHeartbeatWrite = 0;

    public function __construct(private readonly BackgroundArtisanLauncher $launcher)
    {
    }

    /** Called from the queue worker loop (Queue::looping). */
    public function recordHeartbeat(): void
    {
        $now = time();
        if ($now - self::$lastHeartbeatWrite < 5) {
            return;
        }

        self::$lastHeartbeatWrite = $now;

        try {
            Cache::put(self::HEARTBEAT_KEY, $now, 120);
        } catch (Throwable) {
            // A heartbeat is advisory only.
        }
    }

    public function workerIsAlive(): bool
    {
        try {
            $beat = Cache::get(self::HEARTBEAT_KEY);
            if (is_numeric($beat) && time() - (int) $beat <= 20) {
                return true;
            }
        } catch (Throwable) {
            // Fall through to the database signal.
        }

        // A worker that is busy training does not loop, but it keeps updating its job.
        return TrainingJob::query()
            ->where('status', TrainingJob::STATUS_RUNNING)
            ->where('updated_at', '>=', now()->subSeconds(90))
            ->exists();
    }

    /**
     * @return array{state: string, message: ?string}
     */
    public function ensureWorkerFor(TrainingJob $training): array
    {
        if ($training->status !== TrainingJob::STATUS_QUEUED) {
            return ['state' => 'ok', 'message' => null];
        }

        $connection = (string) config('hybrid_ml.queue_connection', 'machine_learning');
        if ((string) config("queue.connections.{$connection}.driver") === 'sync') {
            return ['state' => 'ok', 'message' => null];
        }

        if ($this->workerIsAlive()) {
            return ['state' => 'worker_running', 'message' => null];
        }

        if (! (bool) config('hybrid_ml.auto_start_worker', true)) {
            return [
                'state' => 'worker_missing',
                'message' => 'The machine-learning worker is not running. Ask your instructor or administrator to start start-ml-worker.bat.',
            ];
        }

        $this->requeueIfOrphaned($training, $connection);

        try {
            // At most one automatic worker start every 45 seconds; in between,
            // report what the last attempt did.
            $launched = (bool) Cache::get(self::LAUNCH_RESULT_KEY, true);
            if (Cache::lock(self::LAUNCH_THROTTLE_KEY, 45)->get()) {
                $launched = $this->launcher->launch([
                    'queue:work',
                    $connection,
                    '--queue='.(string) config('hybrid_ml.queue_name', 'machine-learning'),
                    '--stop-when-empty',
                    '--sleep=1',
                    '--tries=2',
                    '--timeout=900',
                ]);
                Cache::put(self::LAUNCH_RESULT_KEY, $launched, 60);
            }
        } catch (Throwable $exception) {
            report($exception);
            $launched = false;
        }

        if (! $launched) {
            return [
                'state' => 'worker_missing',
                'message' => 'The machine-learning worker is not running and could not be started automatically. Ask your instructor or administrator to start start-ml-worker.bat.',
            ];
        }

        return [
            'state' => 'worker_starting',
            'message' => 'Starting the machine-learning worker. Training begins in a few seconds.',
        ];
    }

    /**
     * A training job can stay "queued" while its queue row is gone, for example
     * after the jobs table was cleared or a stale uniqueness lock skipped the
     * dispatch. Put it back on the queue once.
     */
    private function requeueIfOrphaned(TrainingJob $training, string $connection): void
    {
        if ((string) config("queue.connections.{$connection}.driver") !== 'database') {
            return;
        }

        if ($training->created_at === null || $training->created_at->gt(now()->subSeconds(10))) {
            return;
        }

        try {
            $queueConnection = config("queue.connections.{$connection}.connection");
            $table = (string) config("queue.connections.{$connection}.table", 'jobs');
            $queue = (string) config('hybrid_ml.queue_name', 'machine-learning');
            // Serialized commands look like: trainingJobId\";i:12; (quotes escaped inside JSON).
            $pattern = '/trainingJobId\\\\*";i:'.(int) $training->id.';/';

            $hasQueueRow = DB::connection($queueConnection ?: null)
                ->table($table)
                ->where('queue', $queue)
                ->get(['payload'])
                ->contains(static fn ($row): bool => preg_match($pattern, (string) $row->payload) === 1);

            if ($hasQueueRow || ! Cache::lock(self::REQUEUE_THROTTLE_PREFIX.$training->id, 60)->get()) {
                return;
            }

            $job = new ProcessMlTrainingJob((int) $training->id, (string) $training->uuid);
            (new UniqueLock(Cache::store()))->release($job);
            dispatch($job);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
