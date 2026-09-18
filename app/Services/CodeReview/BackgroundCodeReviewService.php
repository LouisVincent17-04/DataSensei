<?php

namespace App\Services\CodeReview;

use App\Support\BackgroundArtisanLauncher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * Keeps an AI review going after the web request's time limit.
 *
 * The request hands the prepared Ollama payload to a detached
 * "code-review:process" command and returns a review id at once. The browser
 * polls the status endpoint until the command stores the finished message.
 * State lives in the cache so the web server and the command share it.
 */
class BackgroundCodeReviewService
{
    private const PREFIX = 'datasensei:code-review:background:';

    public function __construct(private readonly BackgroundArtisanLauncher $launcher)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('code_execution.ollama.background_continuation', true);
    }

    /**
     * @param array<string, mixed> $payload The exact Ollama payload the request prepared.
     */
    public function start(string $ownerKey, string $mode, array $payload, string $language, string $runOutput): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        $id = (string) Str::uuid();
        $payload['stream'] = false;

        Cache::put($this->key($id), [
            'id' => $id,
            'owner' => $ownerKey,
            'mode' => $mode === 'chat' ? 'chat' : 'review',
            'payload' => $payload,
            'language' => $language,
            'run_output' => $runOutput,
            'status' => 'pending',
            'message' => null,
            'fallback' => false,
            'outcome' => null,
            'created_at' => now()->getTimestamp(),
            'started_at' => null,
            'finished_at' => null,
            'launches' => 1,
            'last_launch_at' => now()->getTimestamp(),
        ], $this->ttl());
        Cache::put($this->latestKey($ownerKey), $id, $this->ttl());

        if (! $this->launcher->launch(['code-review:process', $id])) {
            Cache::forget($this->key($id));

            return null;
        }

        return $id;
    }

    /** @return array<string, mixed>|null */
    public function find(string $id): ?array
    {
        if (! Str::isUuid($id)) {
            return null;
        }

        $job = Cache::get($this->key($id));

        return is_array($job) ? $job : null;
    }

    /**
     * Status for the browser. Only the student who started the review can read it.
     *
     * @return array{status: string, message: ?string, fallback: bool, elapsed_seconds: int}|null
     */
    public function statusFor(string $id, string $ownerKey): ?array
    {
        $job = $this->find($id);
        if ($job === null || ! hash_equals((string) $job['owner'], $ownerKey)) {
            return null;
        }

        $now = now()->getTimestamp();
        $elapsed = max(0, $now - (int) $job['created_at']);

        if ($job['status'] === 'pending') {
            $job = $this->recoverIfStalled($job, $now);
        }

        return [
            'status' => (string) $job['status'],
            'message' => $job['message'] !== null ? (string) $job['message'] : null,
            'fallback' => (bool) $job['fallback'],
            'elapsed_seconds' => $elapsed,
        ];
    }

    public function isSuperseded(array $job): bool
    {
        $latest = Cache::get($this->latestKey((string) $job['owner']));

        return is_string($latest) && $latest !== (string) $job['id'];
    }

    /** Only one process may work on a review. */
    public function claim(string $id): bool
    {
        try {
            return Cache::lock($this->key($id).':claim', $this->ttl())->get();
        } catch (Throwable) {
            return true;
        }
    }

    public function markStarted(string $id): void
    {
        $this->update($id, ['started_at' => now()->getTimestamp()]);
    }

    public function complete(string $id, string $message, string $outcome, bool $fallback): void
    {
        $this->update($id, [
            'status' => 'done',
            'message' => $message,
            'outcome' => $outcome,
            'fallback' => $fallback,
            'finished_at' => now()->getTimestamp(),
            // The payload is no longer needed once the answer exists.
            'payload' => [],
        ]);
    }

    /**
     * @param array<string, mixed> $job
     * @return array<string, mixed>
     */
    private function recoverIfStalled(array $job, int $now): array
    {
        $startedAt = $job['started_at'];

        // The process never started (for example, the PC was busy): try again a few times.
        $lastLaunch = (int) ($job['last_launch_at'] ?? $job['created_at']);
        if ($startedAt === null && $now - $lastLaunch >= 15 && (int) $job['launches'] < 3) {
            $this->update((string) $job['id'], [
                'launches' => (int) $job['launches'] + 1,
                'last_launch_at' => $now,
            ]);
            // The command claims the review atomically, so a late first process
            // and this retry can never both call the model.
            $this->launcher->launch(['code-review:process', (string) $job['id']]);

            return $this->find((string) $job['id']) ?? $job;
        }

        $staleAfter = max(60, (int) config('code_execution.ollama.background_stale_after_seconds', 1800));
        $reference = (int) ($startedAt ?? $job['created_at']);
        $neverStarted = $startedAt === null && (int) $job['launches'] >= 3 && $now - $lastLaunch >= 20;

        if ($neverStarted || $now - $reference >= $staleAfter) {
            $message = $neverStarted
                ? "Status: Not Reviewed\nFeedback: The background reviewer could not be started on this computer, so this run was not reviewed. Nothing is wrong with your run. Run the program again to retry."
                : "Status: Not Reviewed\nFeedback: The AI reviewer stopped responding before it finished. Nothing is wrong with your run. Run the program again to retry.";
            $this->complete((string) $job['id'], $message, $neverStarted ? 'background_not_started' : 'background_stalled', true);

            return $this->find((string) $job['id']) ?? $job;
        }

        return $job;
    }

    /** @param array<string, mixed> $changes */
    private function update(string $id, array $changes): void
    {
        $write = function () use ($id, $changes): void {
            $job = $this->find($id);
            if ($job === null) {
                return;
            }

            Cache::put($this->key($id), array_merge($job, $changes), $this->ttl());
        };

        try {
            Cache::lock($this->key($id).':write', 10)->block(5, $write);
        } catch (Throwable) {
            // A lock problem must never lose a finished review.
            $write();
        }
    }

    private function ttl(): int
    {
        return max(600, (int) config('code_execution.ollama.background_retention_seconds', 7200));
    }

    private function key(string $id): string
    {
        return self::PREFIX.'job:'.$id;
    }

    private function latestKey(string $ownerKey): string
    {
        return self::PREFIX.'latest:'.$ownerKey;
    }
}
