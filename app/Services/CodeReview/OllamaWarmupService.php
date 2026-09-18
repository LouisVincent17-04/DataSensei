<?php

namespace App\Services\CodeReview;

use App\Support\BackgroundArtisanLauncher;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Keeps the AI reviewer model loaded in Ollama.
 *
 * The performance log showed that almost every slow review was the first one
 * after the model had been unloaded (keep_alive expired, Ollama or the PC
 * restarted): loading the model took 4–12 seconds before the review itself
 * started. Warm reviews finished in 1–2.5 seconds. Loading the model before a
 * student presses Run removes that delay.
 */
class OllamaWarmupService
{
    private const THROTTLE_KEY = 'datasensei:code-review:warm-throttle';

    public function __construct(private readonly BackgroundArtisanLauncher $launcher)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('code_execution.ollama.warmup', true)
            && trim((string) config('code_execution.ollama.url', '')) !== ''
            && trim((string) config('code_execution.ollama.model', '')) !== '';
    }

    /**
     * Ollama accepts keep_alive as a number of seconds (-1 = keep loaded
     * forever) or as a duration such as "30m". A plain "-1" string is rejected
     * by Ollama, so numeric values are sent as integers.
     */
    public static function keepAlive(): int|string
    {
        $value = trim((string) config('code_execution.ollama.keep_alive', '-1'));

        if ($value === '') {
            return -1;
        }

        return preg_match('/^-?\d+$/', $value) === 1 ? (int) $value : $value;
    }

    /** Asks for a warm-up without making the page wait. At most once a minute. */
    public function warmInBackground(): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        try {
            if (! Cache::lock(self::THROTTLE_KEY, 60)->get()) {
                return true;
            }
        } catch (Throwable) {
            // Without the throttle the command still only loads the model when needed.
        }

        return $this->launcher->launch(['code-review:warm']);
    }

    /**
     * Loads the model if it is not in memory, or refreshes its keep-alive timer.
     *
     * @return array{status: string, message: string, elapsed_ms: float}
     */
    public function warm(int $timeoutSeconds = 180): array
    {
        $startedAt = hrtime(true);
        $result = fn (string $status, string $message): array => [
            'status' => $status,
            'message' => $message,
            'elapsed_ms' => round((hrtime(true) - $startedAt) / 1_000_000, 2),
        ];

        if (! $this->enabled()) {
            return $result('disabled', 'The AI reviewer warm-up is disabled.');
        }

        $model = (string) config('code_execution.ollama.model');
        $loaded = $this->isLoaded();

        if ($loaded === null) {
            return $result('unavailable', 'Ollama is not responding, so the AI reviewer model could not be loaded.');
        }

        try {
            // An empty prompt only loads the model (or resets its keep-alive timer).
            // num_ctx must match the review requests, or Ollama reloads the model
            // on the first real review.
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout((int) config('code_execution.ollama.connect_timeout_seconds', 3))
                ->timeout(max(10, $timeoutSeconds))
                ->post((string) config('code_execution.ollama.url'), [
                    'model' => $model,
                    'prompt' => '',
                    'keep_alive' => self::keepAlive(),
                    'options' => [
                        'num_ctx' => (int) config('code_execution.ollama.num_ctx', 4096),
                    ],
                ]);
        } catch (Throwable $exception) {
            return $this->logged($result('failed', 'Loading the AI reviewer model failed: '.$exception->getMessage()));
        }

        if ($response->failed()) {
            $error = strtolower((string) ($response->json('error') ?? $response->body()));
            $missing = str_contains($error, 'not found') || str_contains($error, 'pull');

            return $this->logged($result(
                $missing ? 'missing_model' : 'failed',
                $missing
                    ? "The AI reviewer model '{$model}' is not installed. Run: ollama pull {$model}"
                    : 'Ollama could not load the AI reviewer model (HTTP '.$response->status().').'
            ));
        }

        return $this->logged($loaded
            ? $result('ready', "The AI reviewer model '{$model}' is already loaded.")
            : $result('loaded', "The AI reviewer model '{$model}' was loaded and is ready."));
    }

    /** true = loaded, false = not loaded, null = Ollama not reachable. */
    public function isLoaded(): ?bool
    {
        $model = (string) config('code_execution.ollama.model');
        $psUrl = $this->baseUrl().'/api/ps';

        try {
            $response = Http::acceptJson()->connectTimeout(2)->timeout(4)->get($psUrl);
        } catch (Throwable) {
            return null;
        }

        if ($response->failed()) {
            return null;
        }

        $wanted = str_contains($model, ':') ? [$model] : [$model, $model.':latest'];

        foreach ((array) $response->json('models', []) as $running) {
            if (! is_array($running)) {
                continue;
            }

            foreach (['name', 'model'] as $field) {
                if (in_array((string) ($running[$field] ?? ''), $wanted, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function baseUrl(): string
    {
        $url = rtrim((string) config('code_execution.ollama.url', ''), '/');

        return (string) preg_replace('#/api/(generate|chat)$#', '', $url);
    }

    /**
     * @param array{status: string, message: string, elapsed_ms: float} $result
     * @return array{status: string, message: string, elapsed_ms: float}
     */
    private function logged(array $result): array
    {
        try {
            Log::channel('ollama_performance')->info('IDE AI model warm-up.', [
                'model' => (string) config('code_execution.ollama.model'),
                'status' => $result['status'],
                'elapsed_ms' => $result['elapsed_ms'],
                'keep_alive' => self::keepAlive(),
            ]);
        } catch (Throwable) {
            // Logging must never break the warm-up.
        }

        return $result;
    }
}
