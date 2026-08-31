<?php

namespace App\Console\Commands;

use Database\Seeders\HybridMlSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallHybridMlModule extends Command
{
    protected $signature = 'ml:install {--force : Continue in production}';
    protected $description = 'Create Hybrid ML tables and register the bundled datasets and benchmark models';

    public function handle(): int
    {
        if (! is_file(storage_path('app/'.config('hybrid_ml.system_manifest_path', 'ml/system/manifest.json')))) {
            $this->error('The bundled Hybrid ML manifest is missing.');
            return self::FAILURE;
        }

        $this->info('Running database migrations...');
        $arguments = ['--force' => (bool) $this->option('force')];
        if (Artisan::call('migrate', $arguments) !== self::SUCCESS) {
            $this->error(Artisan::output());
            return self::FAILURE;
        }
        $this->line(Artisan::output());

        $this->info('Registering built-in datasets and read-only benchmark models...');
        $this->callSilent('db:seed', [
            '--class' => HybridMlSeeder::class,
            '--force' => (bool) $this->option('force'),
        ]);
        $this->callSilent('optimize:clear');
        $this->info('Hybrid Machine Learning is ready. Start the queue worker with start-ml-worker.bat.');
        return self::SUCCESS;
    }
}
