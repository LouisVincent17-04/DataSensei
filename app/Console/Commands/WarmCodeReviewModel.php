<?php

namespace App\Console\Commands;

use App\Services\CodeReview\OllamaWarmupService;
use Illuminate\Console\Command;

class WarmCodeReviewModel extends Command
{
    protected $signature = 'code-review:warm {--timeout=180 : Seconds to wait for Ollama to load the model}';

    protected $description = 'Load the AI code reviewer model into Ollama so the first review is fast.';

    public function handle(OllamaWarmupService $warmup): int
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $result = $warmup->warm((int) $this->option('timeout'));
        $seconds = number_format($result['elapsed_ms'] / 1000, 1);

        $line = "[{$result['status']}] {$result['message']} ({$seconds}s)";
        in_array($result['status'], ['ready', 'loaded', 'disabled'], true)
            ? $this->info($line)
            : $this->warn($line);

        // A missing or stopped Ollama only affects AI review, so this never fails a start script.
        return self::SUCCESS;
    }
}
