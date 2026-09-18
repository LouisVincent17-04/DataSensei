<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use JsonException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Throwable;

class DataSenseiPreflight extends Command
{
    protected $signature = 'datasensei:preflight';
    protected $description = 'Check defense-critical DataSensei dependencies without displaying secrets';

    private int $failures = 0;
    private int $warnings = 0;

    public function handle(): int
    {
        $this->newLine();
        $this->info('DataSensei defense preflight');
        $this->line('This check does not print passwords, API keys, tokens, or dataset contents.');
        $this->newLine();

        $this->checkApplication();
        $databaseReady = $this->checkDatabase();
        if ($databaseReady) {
            $this->checkMigrations();
            $this->checkQuizMakerSchema();
            $this->checkHybridMlReferentialIntegrity();
            $this->checkQueues();
        }
        $this->checkStorage();
        $this->checkHybridMlAssets();
        $this->checkDocker();
        $this->checkMail();
        $this->checkOptionalOllama();

        $this->newLine();
        if ($this->failures > 0) {
            $this->error("Preflight failed: {$this->failures} required check(s) failed; {$this->warnings} warning(s).");
            return self::FAILURE;
        }

        $this->info("Preflight passed with {$this->warnings} warning(s).");
        $this->line('The defense launcher starts the web server, default queue worker, ML queue worker, and scheduler in separate windows.');
        return self::SUCCESS;
    }

    private function checkApplication(): void
    {
        if (PHP_VERSION_ID >= 80200) {
            $this->pass('PHP ' . PHP_VERSION . ' satisfies the application requirement.');
        } else {
            $this->recordFailure('PHP 8.2 or newer is required by this Laravel application.');
        }

        if (filled((string) config('app.key'))) {
            $this->pass('Application key is configured.');
        } else {
            $this->recordFailure('Application key is missing. Run: php artisan key:generate');
        }

        if ((bool) config('app.debug')) {
            $this->recordWarning('APP_DEBUG is enabled. Set APP_DEBUG=false before a defense or shared demonstration.');
        } else {
            $this->pass('Debug output is disabled.');
        }
    }

    private function checkDatabase(): bool
    {
        try {
            $driver = (string) DB::connection()->getDriverName();
            $row = DB::selectOne('SELECT VERSION() AS version');
            $version = trim((string) ($row->version ?? 'unknown'));

            if ($driver !== 'mysql') {
                $this->recordWarning("Database connection is {$driver}, not the defense MySQL connection.");
            } else {
                $this->pass("MySQL connection succeeded (server {$version}).");
            }

            if ($driver === 'mysql' && preg_match('/^(\d+)\.(\d+)\.(\d+)/', $version, $match)) {
                $numeric = $match[1] . '.' . $match[2] . '.' . $match[3];
                if (version_compare($numeric, '5.5.3', '>=')) {
                    $this->pass('Server supports utf8mb4; the schema default is 189 characters so composite indexes remain under the older InnoDB 767-byte key limit.');
                } else {
                    $this->recordFailure('The configured utf8mb4 schema requires MySQL 5.5.3 or newer.');
                }
            }

            return true;
        } catch (Throwable $exception) {
            $this->recordFailure('Database connection failed: ' . $this->safeMessage($exception));
            return false;
        }
    }

    private function checkMigrations(): void
    {
        try {
            Artisan::call('migrate:status', ['--no-interaction' => true]);
            $output = Artisan::output();
            if (preg_match('/\bPending\b/i', $output)) {
                $this->recordFailure('Pending database migrations were detected. Run: php artisan migrate');
            } else {
                $this->pass('No pending migrations were reported.');
            }
        } catch (Throwable $exception) {
            $this->recordFailure('Migration status could not be read: ' . $this->safeMessage($exception));
        }
    }

    private function checkQuizMakerSchema(): void
    {
        $requiredSchema = [
            'table_of_specifications' => [
                'id',
                'total_items',
            ],
            'table_of_specification_rows' => [
                'table_of_specification_id',
                'item_count',
                'default_points',
            ],
            'assessments' => [
                'table_of_specification_id',
                'class_id',
                'status',
                'draft_last_item',
                'draft_saved_at',
                'total_items',
                'total_points',
                'max_attempts',
            ],
            'assessment_questions' => [
                'assessment_id',
                'item_number',
                'question_type',
                'points',
                'is_required',
                'authoring_touched',
                'correct_answer',
                'answer_explanation',
                'rubric_text',
            ],
            'assessment_question_options' => [
                'assessment_question_id',
                'option_text',
                'is_correct',
                'order_index',
            ],
            'assessment_submissions' => [
                'assessment_id',
                'student_id',
                'attempt_no',
                'status',
                'score',
                'total_points',
                'draft_answers',
                'draft_version',
                'draft_saved_at',
                'timed_out_at',
            ],
            'assessment_answers' => [
                'assessment_submission_id',
                'assessment_question_id',
                'selected_option_id',
                'answer_text',
                'is_correct',
                'points_awarded',
                'instructor_feedback',
            ],
            'student_assessment_diagnostics' => [
                'student_id',
                'assessment_id',
                'mastery_percent',
                'manual_review_pending',
            ],
        ];

        $missing = [];
        try {
            foreach ($requiredSchema as $table => $columns) {
                if (! Schema::hasTable($table)) {
                    $missing[] = $table;
                    continue;
                }

                foreach ($columns as $column) {
                    if (! $this->columnExists($table, $column)) {
                        $missing[] = $table.'.'.$column;
                    }
                }
            }
        } catch (Throwable $exception) {
            $this->recordFailure(
                'Quiz Maker schema could not be verified: '.$this->safeMessage($exception)
            );
            return;
        }

        if ($missing !== []) {
            $this->recordFailure(
                'Quiz Maker schema is incomplete: '.implode(', ', $missing)
                .'. Run: php artisan migrate'
            );
            return;
        }

        $this->pass('Quiz Maker database schema is complete.');
    }

    private function checkQueues(): void
    {
        if (! Schema::hasTable('jobs')) {
            $this->recordFailure('The jobs table is missing. Run: php artisan migrate');
            return;
        }

        $defaultQueue = (string) config('queue.connections.database.queue', 'default');
        $mlQueue = (string) config('hybrid_ml.queue_name', 'machine-learning');
        $defaultBacklog = DB::table('jobs')->where('queue', $defaultQueue)->count();
        $mlBacklog = DB::table('jobs')->where('queue', $mlQueue)->count();
        $this->pass("Queue storage is ready (default backlog {$defaultBacklog}; ML backlog {$mlBacklog}).");

        $retryAfter = max(60, (int) config('queue.connections.machine_learning.retry_after', 1200));
        $staleMl = DB::table('jobs')
            ->where('queue', $mlQueue)
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<', now()->timestamp - $retryAfter)
            ->count();
        if ($staleMl > 0) {
            $this->recordWarning("{$staleMl} ML queue job(s) appear stale. Check the ML worker before the defense.");
        }

        if (Schema::hasTable('failed_jobs')) {
            $failed = DB::table('failed_jobs')->count();
            if ($failed > 0) {
                $this->recordWarning("{$failed} failed queue job(s) are recorded. Review them before presenting background features.");
            }
        }
    }

    private function checkHybridMlReferentialIntegrity(): void
    {
        if (! Schema::hasTable('ml_models') || ! Schema::hasTable('model_versions')) {
            return;
        }
        if (! $this->columnExists('ml_models', 'current_version_id') || ! $this->columnExists('model_versions', 'ml_model_id')) {
            $this->recordFailure('Hybrid ML model-version relationship columns are missing. Run: php artisan migrate');
            return;
        }

        try {
            $invalidReferences = DB::table('ml_models as model')
                ->leftJoin('model_versions as version', 'version.id', '=', 'model.current_version_id')
                ->whereNotNull('model.current_version_id')
                ->where(function ($query): void {
                    $query->whereNull('version.id')
                        ->orWhereColumn('version.ml_model_id', '!=', 'model.id');
                })
                ->count();

            if ($invalidReferences > 0) {
                $this->recordFailure("Hybrid ML contains {$invalidReferences} invalid current-version reference(s).");
                return;
            }

            if (DB::connection()->getDriverName() === 'mysql') {
                $constraint = DB::selectOne(
                    <<<'SQL'
                        SELECT 1 AS constraint_exists
                        FROM information_schema.TABLE_CONSTRAINTS
                        WHERE CONSTRAINT_SCHEMA = DATABASE()
                          AND TABLE_NAME = ?
                          AND CONSTRAINT_NAME = ?
                          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                        LIMIT 1
                    SQL,
                    ['ml_models', 'mm_current_version_fk'],
                );
                if ($constraint === null) {
                    $this->recordFailure('Hybrid ML current-version foreign key mm_current_version_fk is missing.');
                    return;
                }
            }

            $this->pass('Hybrid ML current-version references and foreign-key constraint are valid.');
        } catch (Throwable $exception) {
            $this->recordFailure('Hybrid ML referential integrity could not be verified: '.$this->safeMessage($exception));
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            <<<'SQL'
                SELECT COUNT(*) AS aggregate
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?
                  AND COLUMN_NAME = ?
            SQL,
            [$table, $column],
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function checkStorage(): void
    {
        foreach ([storage_path(), storage_path('framework'), base_path('bootstrap/cache')] as $path) {
            if (is_dir($path) && is_writable($path)) {
                $this->pass('Writable runtime directory: ' . basename($path));
            } else {
                $this->recordFailure('Runtime directory is missing or not writable: ' . $path);
            }
        }

        $publicTarget = storage_path('app/public');
        $publicLink = public_path('storage');
        $resolvedTarget = realpath($publicTarget);
        $resolvedLink = realpath($publicLink);

        if ($resolvedTarget === false || ! is_dir($publicTarget)) {
            $this->recordFailure('Public storage target is missing: storage/app/public');
            return;
        }

        if ($resolvedLink === false || $this->normalizedPath($resolvedLink) !== $this->normalizedPath($resolvedTarget)) {
            $this->recordFailure('public/storage is not linked to storage/app/public. Remove any ordinary public/storage directory, then run: php artisan storage:link');
            return;
        }

        $targetProbe = null;
        try {
            $probeName = '.datasensei-storage-probe-'.bin2hex(random_bytes(8));
            $targetProbe = $publicTarget.DIRECTORY_SEPARATOR.$probeName;
            $linkedProbe = $publicLink.DIRECTORY_SEPARATOR.$probeName;
            $probeContents = bin2hex(random_bytes(16));

            if (file_put_contents($targetProbe, $probeContents, LOCK_EX) === false) {
                $this->recordFailure('The public storage target is not writable.');
                return;
            }
            if (! is_readable($linkedProbe) || ! hash_equals($probeContents, (string) file_get_contents($linkedProbe))) {
                $this->recordFailure('The public storage link exists but files cannot be read through it.');
                return;
            }

            $this->pass('public/storage resolves to storage/app/public and passed a write/read probe.');
        } catch (Throwable $exception) {
            $this->recordFailure('The public storage link could not be tested: '.$this->safeMessage($exception));
        } finally {
            if ($targetProbe !== null && is_file($targetProbe)) {
                @unlink($targetProbe);
            }
        }
    }

    private function normalizedPath(string $path): string
    {
        return strtolower(rtrim(str_replace('\\', '/', $path), '/'));
    }

    private function checkHybridMlAssets(): void
    {
        $relativeManifest = (string) config('hybrid_ml.system_manifest_path', 'ml/system/manifest.json');
        $manifestPath = storage_path('app/' . $relativeManifest);
        if (! is_file($manifestPath)) {
            $this->recordFailure('Hybrid ML system manifest is missing. Run: php artisan ml:build-system-assets');
            return;
        }

        try {
            $manifest = json_decode((string) file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->recordFailure('Hybrid ML system manifest is invalid JSON.');
            return;
        }

        $missing = 0;
        $datasets = (array) ($manifest['datasets'] ?? []);
        foreach ($datasets as $dataset) {
            $datasetPath = (string) ($dataset['storage_path'] ?? '');
            if ($datasetPath === '' || ! is_file(storage_path('app/' . $datasetPath))) {
                $missing++;
            }
            foreach ((array) ($dataset['models'] ?? []) as $model) {
                $artifact = (string) ($model['artifact_path'] ?? '');
                if ($artifact === '' || ! is_file(storage_path('app/' . $artifact))) {
                    $missing++;
                }
            }
        }

        if ($missing > 0) {
            $this->recordFailure("Hybrid ML manifest references {$missing} missing dataset/model artifact(s).");
        } else {
            $this->pass('Hybrid ML manifest and bundled dataset/model artifacts are present.');
        }
    }

    private function checkDocker(): void
    {
        $mlNeedsDocker = (string) config('hybrid_ml.runner_driver', 'docker') === 'docker';
        $pythonNeedsDocker = (string) config('code_execution.python.driver', 'docker') === 'docker';
        $needsDocker = $mlNeedsDocker || $pythonNeedsDocker;
        if (! $needsDocker) {
            $this->recordWarning('Docker checks skipped because the configured runners are not using Docker.');
            return;
        }

        $binary = $mlNeedsDocker
            ? (string) config('hybrid_ml.docker_binary', 'docker')
            : (string) config('code_execution.python.docker.binary', 'docker');
        $executable = (new ExecutableFinder())->find($binary);
        if (! $executable) {
            $this->recordFailure('Docker is required by the configured code/ML runner but the docker command was not found.');
            return;
        }

        try {
            $process = new Process([$executable, 'version', '--format', '{{.Server.Version}}']);
            $process->setTimeout(6)->run();
            if (! $process->isSuccessful()) {
                $this->recordFailure('Docker command exists, but the Docker engine is not responding. Start Docker Desktop.');
                return;
            }
            $this->pass('Docker engine is responding.');
        } catch (Throwable) {
            $this->recordFailure('Docker engine could not be checked. Start Docker Desktop and retry.');
        }
    }

    private function checkMail(): void
    {
        $mailer = (string) config('mail.default', 'log');
        $from = (string) config('mail.from.address', '');
        $host = (string) config("mail.mailers.{$mailer}.host", '');
        if ($mailer === 'smtp' && ($host === '' || str_contains($host, 'example.com'))) {
            $this->recordWarning('SMTP still uses placeholder settings. Password-reset email will not be defense-ready until real mail settings are configured.');
            return;
        }
        if ($from === '' || str_contains($from, 'example.com')) {
            $this->recordWarning('Mail sender address is still a placeholder.');
            return;
        }
        $this->pass("Mail metadata is configured for the {$mailer} mailer.");
    }

    private function checkOptionalOllama(): void
    {
        $generateUrl = (string) config('code_execution.ollama.url', '');
        if ($generateUrl === '') {
            $this->recordWarning('Optional Ollama code reviewer is not configured.');
            return;
        }

        $tagsUrl = preg_replace('#/api/generate/?$#', '/api/tags', $generateUrl) ?: $generateUrl;
        try {
            $response = Http::timeout(2)->acceptJson()->get($tagsUrl);
            if ($response->successful()) {
                $configuredModel = trim((string) config('code_execution.ollama.model', ''));
                $installedModels = collect($response->json('models', []))
                    ->flatMap(fn (mixed $model): array => is_array($model)
                        ? array_filter([
                            trim((string) ($model['name'] ?? '')),
                            trim((string) ($model['model'] ?? '')),
                        ])
                        : [])
                    ->unique()
                    ->values();

                $modelCandidates = collect([$configuredModel])
                    ->when(
                        $configuredModel !== '' && ! str_contains($configuredModel, ':'),
                        fn ($models) => $models->push($configuredModel.':latest')
                    );
                $modelIsInstalled = $modelCandidates
                    ->contains(fn (string $model): bool => $installedModels->contains($model));

                if ($configuredModel !== '' && ! $modelIsInstalled) {
                    $this->recordWarning("Ollama is responding, but the configured model '{$configuredModel}' is not installed.");
                    return;
                }

                $this->pass($configuredModel !== ''
                    ? "Ollama is responding and model '{$configuredModel}' is installed."
                    : 'Optional Ollama service is responding.');

                // Load the model now so the first student review does not wait for it.
                $warmup = app(\App\Services\CodeReview\OllamaWarmupService::class)->warm(120);
                $seconds = number_format($warmup['elapsed_ms'] / 1000, 1);
                if (in_array($warmup['status'], ['ready', 'loaded'], true)) {
                    $this->pass("AI reviewer model is loaded in memory ({$seconds}s).");
                } elseif ($warmup['status'] !== 'disabled') {
                    $this->recordWarning($warmup['message']);
                }
            } else {
                $this->recordWarning('Optional Ollama service did not return a successful health response.');
            }
        } catch (Throwable) {
            $this->recordWarning('Optional Ollama service is not responding; only AI code review is affected.');
        }
    }

    private function pass(string $message): void
    {
        $this->line('[PASS] ' . $message);
    }

    private function recordWarning(string $message): void
    {
        $this->warnings++;
        $this->line('[WARN] ' . $message);
    }

    private function recordFailure(string $message): void
    {
        $this->failures++;
        $this->line('[FAIL] ' . $message);
    }

    private function safeMessage(Throwable $exception): string
    {
        $message = trim($exception->getMessage());
        if ($message === '') {
            return $exception::class;
        }

        // Database exception messages can contain DSNs or SQL values. Keep the
        // console useful without echoing potentially sensitive connection data.
        return str_contains($exception::class, 'QueryException')
            ? 'database query failed (details are available in the application log)'
            : mb_strimwidth($message, 0, 180, '...');
    }
}
