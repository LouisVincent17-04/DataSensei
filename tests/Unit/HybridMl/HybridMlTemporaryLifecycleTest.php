<?php

namespace Tests\Unit\HybridMl;

use App\Services\HybridMl\HybridMlRunnerService;
use App\Services\HybridMl\TabularDatasetReader;
use App\Services\HybridMl\TemporaryFileCleanupService;
use Closure;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class HybridMlTemporaryLifecycleTest extends TestCase
{
    private array $temporaryPaths = [];

    protected function tearDown(): void
    {
        foreach (array_reverse($this->temporaryPaths) as $path) {
            is_dir($path) ? File::deleteDirectory($path) : @unlink($path);
        }

        parent::tearDown();
    }

    public function test_training_timeout_removes_the_owned_working_directory(): void
    {
        $before = $this->newTrainingDirectories();
        $runner = $this->runner(function (): Process {
            $process = new Process([PHP_BINARY, '-r', 'usleep(2000000);']);
            $process->setTimeout(0.05);
            return $process;
        });

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('exceeded its time limit');

        try {
            $runner->train($this->dataset(), []);
        } finally {
            $this->assertSame($before, $this->newTrainingDirectories());
        }
    }

    public function test_malformed_runner_output_removes_the_owned_working_directory(): void
    {
        $before = $this->newTrainingDirectories();
        $runner = $this->runner(static function (string $output): Process {
            $code = 'file_put_contents($argv[1].DIRECTORY_SEPARATOR."result.json", "{");';
            return new Process([PHP_BINARY, '-r', $code, $output]);
        });

        try {
            $runner->train($this->dataset(), []);
            $this->fail('Malformed result JSON should fail training.');
        } catch (\JsonException) {
            $this->assertSame($before, $this->newTrainingDirectories());
        }
    }

    public function test_progress_callback_failure_stops_the_process_and_removes_the_directory(): void
    {
        $before = $this->newTrainingDirectories();
        $runner = $this->runner(static function (): Process {
            return new Process([PHP_BINARY, '-r', 'usleep(2000000);']);
        });

        try {
            $runner->train($this->dataset(), [], static function (): void {
                throw new RuntimeException('Progress persistence failed.');
            });
            $this->fail('The callback failure should leave training.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Progress persistence failed.', $exception->getMessage());
            $this->assertSame($before, $this->newTrainingDirectories());
        }
    }

    public function test_converted_csv_is_removed_when_parsing_fails(): void
    {
        $csv = $this->temporaryFile('dataset-', "name,age\nJos\xE9,20,unexpected\n");
        $before = glob(sys_get_temp_dir().DIRECTORY_SEPARATOR.'datasensei_csv_*') ?: [];

        try {
            (new TabularDatasetReader())->readCsv($csv);
            $this->fail('The malformed row should fail CSV validation.');
        } catch (ValidationException) {
            $after = glob(sys_get_temp_dir().DIRECTORY_SEPARATOR.'datasensei_csv_*') ?: [];
            $this->assertSame(array_values($before), array_values($after));
        }
    }

    public function test_stale_pruner_removes_only_expired_datasensei_entries(): void
    {
        $mlRoot = $this->temporaryDirectory('ml-temp-');
        $systemRoot = $this->temporaryDirectory('system-temp-');
        $staleDirectory = $mlRoot.DIRECTORY_SEPARATOR.'train-stale';
        $freshDirectory = $mlRoot.DIRECTORY_SEPARATOR.'train-fresh';
        File::ensureDirectoryExists($staleDirectory);
        File::ensureDirectoryExists($freshDirectory);
        file_put_contents($staleDirectory.DIRECTORY_SEPARATOR.'result.json', '{}');
        file_put_contents($freshDirectory.DIRECTORY_SEPARATOR.'result.json', '{}');
        $staleCsv = $systemRoot.DIRECTORY_SEPARATOR.'datasensei_csv_stale';
        $freshCsv = $systemRoot.DIRECTORY_SEPARATOR.'datasensei_csv_fresh';
        $unrelated = $systemRoot.DIRECTORY_SEPARATOR.'unrelated.tmp';
        file_put_contents($staleCsv, 'old');
        file_put_contents($freshCsv, 'new');
        file_put_contents($unrelated, 'old');

        $old = now()->subHours(3)->timestamp;
        touch($staleDirectory.DIRECTORY_SEPARATOR.'result.json', $old);
        touch($staleDirectory, $old);
        touch($staleCsv, $old);
        touch($unrelated, $old);

        $deleted = (new TemporaryFileCleanupService($mlRoot, $systemRoot))
            ->prune(now()->subHour());

        $this->assertSame(['directories' => 1, 'files' => 1], $deleted);
        $this->assertDirectoryDoesNotExist($staleDirectory);
        $this->assertDirectoryExists($freshDirectory);
        $this->assertFileDoesNotExist($staleCsv);
        $this->assertFileExists($freshCsv);
        $this->assertFileExists($unrelated);
    }

    private function runner(callable $factory): HybridMlRunnerService
    {
        return new class($factory) extends HybridMlRunnerService
        {
            private Closure $factory;

            public function __construct(callable $factory)
            {
                $this->factory = Closure::fromCallable($factory);
            }

            protected function trainingProcess(string $datasetPath, string $configPath, string $outputPath): Process
            {
                return ($this->factory)($outputPath);
            }
        };
    }

    private function dataset(): string
    {
        return $this->temporaryFile('dataset-', "feature,target\n1,2\n");
    }

    private function newTrainingDirectories(): array
    {
        return glob(storage_path('app/ml/tmp/train-*'), GLOB_ONLYDIR) ?: [];
    }

    private function temporaryFile(string $prefix, string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), $prefix);
        $this->assertNotFalse($path);
        file_put_contents($path, $contents);
        $this->temporaryPaths[] = $path;
        return $path;
    }

    private function temporaryDirectory(string $prefix): string
    {
        $base = tempnam(sys_get_temp_dir(), $prefix);
        $this->assertNotFalse($base);
        @unlink($base);
        $this->assertTrue(mkdir($base, 0700));
        $this->temporaryPaths[] = $base;
        return $base;
    }
}
