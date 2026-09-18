<?php

namespace App\Services\HybridMl;

use App\Models\ModelVersion;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use Throwable;

class HybridMlRunnerService
{
    /**
     * @param array<string,mixed> $configuration
     * @param callable(int,string):void|null $progressCallback
     * @return array{result:array<string,mixed>,output_directory:string,stdout:string,stderr:string}
     */
    public function train(string $datasetPath, array $configuration, ?callable $progressCallback = null): array
    {
        if (! is_file($datasetPath)) {
            throw new RuntimeException('The selected dataset file is no longer available.');
        }

        $temporary = storage_path('app/ml/tmp/train-'.Str::uuid());
        $handedOff = false;

        try {
            $output = $temporary.DIRECTORY_SEPARATOR.'output';
            File::ensureDirectoryExists($output, 0777, true);
            @chmod($temporary, 0777);
            @chmod($output, 0777);
            $configPath = $temporary.DIRECTORY_SEPARATOR.'config.json';
            File::put($configPath, json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
            @chmod($configPath, 0644);

            $process = $this->trainingProcess($datasetPath, $configPath, $output);
            $this->runAndMonitor(
                $process,
                $output,
                max(30, (int) config('hybrid_ml.training_timeout_seconds', 600)),
                $progressCallback
            );

            $resultPath = $output.DIRECTORY_SEPARATOR.'result.json';
            if (! is_file($resultPath)) {
                throw new RuntimeException($this->readError($output, $process));
            }

            $result = json_decode(File::get($resultPath), true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($result) || ($result['ok'] ?? false) !== true || ! is_file($output.DIRECTORY_SEPARATOR.'model.joblib')) {
                $error = is_array($result) ? (string) ($result['message'] ?? '') : '';
                throw new RuntimeException($error !== '' ? $error : 'The training runner did not produce a valid model artifact.');
            }

            // A successful run transfers ownership of this directory to the
            // queue job, which removes it after the artifacts are persisted.
            $handedOff = true;

            return [
                'result' => $result,
                'output_directory' => $output,
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
            ];
        } finally {
            // Process start, timeout, progress callback, malformed JSON, and
            // artifact-validation failures all leave through this path.
            if (! $handedOff && is_dir($temporary)) {
                File::deleteDirectory($temporary);
            }
        }
    }

    /** @param array<string,mixed> $inputValues @return array<string,mixed> */
    public function predict(ModelVersion $version, array $inputValues): array
    {
        // Artifacts are written with storage_path('app/...') by ModelStorageService and the system seeder.
        // Laravel 12's "local" disk is rooted at storage/app/private, so it must not be used here.
        $artifact = storage_path('app/'.ltrim((string) $version->artifact_path, '/\\'));
        if (! is_file($artifact)) {
            throw new RuntimeException('The selected model artifact is missing.');
        }

        $temporary = storage_path('app/ml/tmp/predict-'.Str::uuid());
        $output = $temporary.DIRECTORY_SEPARATOR.'output';
        File::ensureDirectoryExists($output, 0777, true);
        @chmod($temporary, 0777);
        @chmod($output, 0777);
        $inputPath = $temporary.DIRECTORY_SEPARATOR.'input.json';
        File::put($inputPath, json_encode(['input_values' => $inputValues], JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION | JSON_THROW_ON_ERROR));
        @chmod($inputPath, 0644);

        try {
            $process = $this->predictionProcess($artifact, $inputPath, $output);
            $this->runAndMonitor(
                $process,
                $output,
                max(5, (int) config('hybrid_ml.prediction_timeout_seconds', 30)),
                null
            );
            $resultPath = $output.DIRECTORY_SEPARATOR.'prediction.json';
            if (! is_file($resultPath)) {
                throw new RuntimeException($this->readError($output, $process));
            }
            $result = json_decode(File::get($resultPath), true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($result) || ($result['ok'] ?? false) !== true) {
                throw new RuntimeException('The prediction runner returned an invalid response.');
            }
            return $result;
        } finally {
            File::deleteDirectory($temporary);
        }
    }

    protected function trainingProcess(string $datasetPath, string $configPath, string $outputPath): Process
    {
        return $this->makeProcess(
            'train',
            $datasetPath,
            $configPath,
            $outputPath,
            max(30, (int) config('hybrid_ml.training_timeout_seconds', 600))
        );
    }

    private function predictionProcess(string $modelPath, string $inputPath, string $outputPath): Process
    {
        return $this->makeProcess(
            'predict',
            $modelPath,
            $inputPath,
            $outputPath,
            max(5, (int) config('hybrid_ml.prediction_timeout_seconds', 30))
        );
    }

    private function makeProcess(string $mode, string $firstPath, string $secondPath, string $outputPath, int $timeout): Process
    {
        $driver = strtolower((string) config('hybrid_ml.runner_driver', 'docker'));
        if ($driver === 'local') {
            $python = (string) config('hybrid_ml.local_python_binary', 'python');
            $runner = resource_path('hybrid-ml/trusted_runner.py');
            $process = new Process([$python, '-B', '-u', $runner, $mode, $firstPath, $secondPath, $outputPath]);
            $process->setTimeout($timeout);
            $process->setEnv(array_merge($_ENV, [
                'MPLBACKEND' => 'Agg',
                'MPLCONFIGDIR' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'datasensei-matplotlib',
                'OMP_NUM_THREADS' => '1',
                'OPENBLAS_NUM_THREADS' => '1',
                'MKL_NUM_THREADS' => '1',
                'NUMEXPR_NUM_THREADS' => '1',
            ]));
            return $process;
        }

        if ($driver !== 'docker') {
            throw new RuntimeException('Unknown Hybrid ML runner driver.');
        }

        $docker = (string) config('hybrid_ml.docker_binary', 'docker');
        $image = (string) config('hybrid_ml.runner_image', 'datasensei-python-runner:latest');
        $runner = resource_path('hybrid-ml/trusted_runner.py');
        if (! is_file($runner)) {
            throw new RuntimeException('The trusted Hybrid ML runner is missing.');
        }

        $firstTarget = $mode === 'train' ? '/input/dataset.csv' : '/input/model.joblib';
        $secondTarget = $mode === 'train' ? '/input/config.json' : '/input/prediction.json';
        $containerName = 'datasensei-ml-'.str_replace('-', '', (string) Str::uuid());
        $command = [
            $docker, 'run', '--rm',
            '--name', $containerName,
            '--network', 'none',
            '--memory', (string) config('hybrid_ml.memory', '768m'),
            '--memory-swap', (string) config('hybrid_ml.memory_swap', '768m'),
            '--cpus', (string) config('hybrid_ml.cpus', '1.0'),
            '--pids-limit', (string) config('hybrid_ml.pids_limit', 96),
            '--ulimit', 'nofile=128:128',
            '--ipc', 'none',
            '--read-only',
            '--cap-drop', 'ALL',
            '--security-opt', 'no-new-privileges',
            '--tmpfs', '/tmp:rw,nosuid,nodev,size=128m,mode=1777',
            '--mount', $this->mount($runner, '/opt/datasensei/trusted_ml_runner.py', true),
            '--mount', $this->mount($firstPath, $firstTarget, true),
            '--mount', $this->mount($secondPath, $secondTarget, true),
            '--mount', $this->mount($outputPath, '/output', false),
            '--entrypoint', 'python',
            '-e', 'MPLBACKEND=Agg',
            '-e', 'MPLCONFIGDIR=/tmp/matplotlib',
            '-e', 'OMP_NUM_THREADS=1',
            '-e', 'OPENBLAS_NUM_THREADS=1',
            '-e', 'MKL_NUM_THREADS=1',
            '-e', 'NUMEXPR_NUM_THREADS=1',
            $image,
            '-B', '-u', '/opt/datasensei/trusted_ml_runner.py', $mode, $firstTarget, $secondTarget, '/output',
        ];

        $process = new Process($command);
        $process->setTimeout($timeout);
        return $process;
    }

    /** @param callable(int,string):void|null $callback */
    private function runAndMonitor(Process $process, string $outputPath, int $timeout, ?callable $callback): void
    {
        $started = microtime(true);
        $lastProgress = -1;
        $lastStage = '';
        try {
            $process->start();
            while ($process->isRunning()) {
                $process->checkTimeout();
                if ($callback !== null) {
                    [$progress, $stage] = $this->progress($outputPath);
                    if ($progress !== $lastProgress || $stage !== $lastStage) {
                        $callback($progress, $stage);
                        $lastProgress = $progress;
                        $lastStage = $stage;
                    }
                }
                usleep(400000);
                if ((microtime(true) - $started) > ($timeout + 5)) {
                    $process->stop(1, 9);
                    throw new RuntimeException('The machine-learning process exceeded its time limit.');
                }
            }
        } catch (ProcessTimedOutException $exception) {
            $process->stop(1, 9);
            throw new RuntimeException('The machine-learning process exceeded its time limit.', 0, $exception);
        } catch (Throwable $exception) {
            if ($process->isRunning()) {
                $process->stop(1, 9);
            }
            throw $exception;
        }

        if (! $process->isSuccessful()) {
            throw new RuntimeException($this->readError($outputPath, $process));
        }
        if ($callback !== null) {
            [$progress, $stage] = $this->progress($outputPath);
            $callback(max(99, $progress), $stage !== '' ? $stage : 'Finalizing the model');
        }
    }

    /** @return array{0:int,1:string} */
    private function progress(string $outputPath): array
    {
        $path = $outputPath.DIRECTORY_SEPARATOR.'progress.json';
        if (! is_file($path)) {
            return [1, 'Starting the trusted training environment'];
        }
        try {
            $payload = json_decode(File::get($path), true, 32, JSON_THROW_ON_ERROR);
            return [
                max(0, min(100, (int) ($payload['progress'] ?? 1))),
                substr((string) ($payload['stage'] ?? 'Training'), 0, 160),
            ];
        } catch (\Throwable) {
            return [1, 'Starting the trusted training environment'];
        }
    }

    private function readError(string $outputPath, Process $process): string
    {
        $errorPath = $outputPath.DIRECTORY_SEPARATOR.'error.json';
        if (is_file($errorPath)) {
            try {
                $payload = json_decode(File::get($errorPath), true, 32, JSON_THROW_ON_ERROR);
                $message = trim((string) ($payload['message'] ?? ''));
                if ($message !== '') {
                    return substr($message, 0, 1200);
                }
            } catch (\Throwable) {
                // Fall through to stderr.
            }
        }
        $stderr = trim($process->getErrorOutput());
        return $stderr !== ''
            ? substr($stderr, 0, 1200)
            : 'The trusted machine-learning process failed without a usable result.';
    }

    private function mount(string $source, string $target, bool $readOnly): string
    {
        $resolved = realpath($source) ?: $source;
        if (! file_exists($resolved)) {
            throw new RuntimeException('A required Hybrid ML file or directory is missing.');
        }
        if (str_contains($resolved, ',')) {
            throw new RuntimeException('Hybrid ML storage paths may not contain commas.');
        }
        $value = 'type=bind,source='.str_replace('\\', '/', $resolved).',target='.$target;
        return $readOnly ? $value.',readonly' : $value;
    }
}
