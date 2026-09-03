<?php

namespace App\Console\Commands;

use App\Services\HybridMl\TemporaryFileCleanupService;
use Illuminate\Console\Command;

class PruneMlTemporaryFiles extends Command
{
    protected $signature = 'ml:prune-temporary-files {--hours= : Delete temporary entries older than this many hours}';

    protected $description = 'Remove stale Hybrid ML working directories and converted CSV files';

    public function handle(TemporaryFileCleanupService $cleanup): int
    {
        $hours = $this->option('hours');
        $hours = $hours === null
            ? (int) config('hybrid_ml.temporary_file_retention_hours', 24)
            : filter_var($hours, FILTER_VALIDATE_INT);

        if (! is_int($hours) || $hours < 1 || $hours > 720) {
            $this->error('The retention period must be an integer from 1 through 720 hours.');
            return self::INVALID;
        }

        $deleted = $cleanup->prune(now()->subHours($hours));
        $this->info("Removed {$deleted['directories']} stale ML working directory entries and {$deleted['files']} stale temporary files.");

        return self::SUCCESS;
    }
}
