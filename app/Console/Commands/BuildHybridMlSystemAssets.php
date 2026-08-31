<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class BuildHybridMlSystemAssets extends Command
{
    protected $signature = 'ml:build-system-assets {--python=python : Python executable}';
    protected $description = 'Rebuild the curated CSV files and read-only benchmark model artifacts';

    public function handle(): int
    {
        $process = new Process([
            (string) $this->option('python'),
            base_path('resources/hybrid-ml/build_system_assets.py'),
        ], base_path(), null, null, 3600);
        $process->setTty(Process::isTtySupported());
        $process->run(fn ($type, $buffer) => $this->output->write($buffer));
        if (! $process->isSuccessful()) {
            $this->error(trim($process->getErrorOutput()) ?: 'System asset generation failed.');
            return self::FAILURE;
        }
        $this->info('System assets rebuilt. Run php artisan db:seed --class=Database\\Seeders\\HybridMlSeeder.');
        return self::SUCCESS;
    }
}
