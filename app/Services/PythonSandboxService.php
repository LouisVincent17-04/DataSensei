<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class PythonSandboxService
{
    public function __construct(private readonly PythonCodePolicyService $policy)
    {
    }

    public function runInline(string $code, string $stdin = '', array $options = []): array
    {
        $workspacePath = storage_path('app/python_sandbox/tmp_' . Str::uuid());
        File::ensureDirectoryExists($workspacePath, 0755, true);

        try {
            $this->policy->assertAllowed($code);
            File::put($workspacePath . DIRECTORY_SEPARATOR . 'main.py', $code);
            @chmod($workspacePath . DIRECTORY_SEPARATOR . 'main.py', 0644);

            return $this->runWorkspace($workspacePath, 'main.py', $code, $stdin, $options);
        } catch (\DomainException $exception) {
            return $this->policyFailure($exception->getMessage());
        } finally {
            File::deleteDirectory($workspacePath);
        }
    }

    public function runWorkspace(
        string $workspacePath,
        string $entryRelativePath,
        string $entryCode,
        string $stdin = '',
        array $options = []
    ): array {
        File::ensureDirectoryExists($workspacePath, 0755, true);

        try {
            $this->validateWorkspaceSources($workspacePath, $entryCode);
        } catch (\DomainException $exception) {
            return $this->policyFailure($exception->getMessage());
        }

        $safeEntryPath = $this->safeJoin($workspacePath, $entryRelativePath);
        File::ensureDirectoryExists(dirname($safeEntryPath), 0755, true);
        File::put($safeEntryPath, $entryCode);
        @chmod($safeEntryPath, 0644);

        return $this->runPreparedWorkspace($workspacePath, $entryRelativePath, $stdin, $options);
    }

    private function validateWorkspaceSources(string $workspacePath, string $entryCode): void
    {
        if (strlen($entryCode) > (int) config('code_execution.python.max_code_bytes', 50000)) {
            throw new \DomainException('The Python entry file exceeds the source-code size limit.');
        }

        // Validate only the file the student is currently running. Scanning every
        // saved .py file caused an old, unrelated test file to block all future runs.
        // Imported local modules are still checked at runtime by datasensei_runner.py.
        $this->policy->assertAllowed($entryCode);
    }

    private function runPreparedWorkspace(string $workspacePath, string $entryRelativePath, string $stdin, array $options): array
    {
        $start = microtime(true);
        $driver = strtolower((string) config('code_execution.python.driver', 'docker'));
        $timeout = max(1, min(60, (int) ($options['timeout'] ?? config('code_execution.python.timeout_seconds', 10))));
        $entryPath = $this->safeJoin($workspacePath, $entryRelativePath);

        if (strlen(File::get($entryPath)) > (int) config('code_execution.python.max_code_bytes', 50000) + 20000) {
            return $this->failure('Code is too large for the sandbox limit.', $start);
        }

        if (strlen($stdin) > (int) config('code_execution.python.max_stdin_bytes', 10000)) {
            return $this->failure('Standard input is too large for the sandbox limit.', $start);
        }

        if ($driver !== 'docker' && $driver !== 'local') {
            return $this->failure('Unknown Python sandbox driver.', $start);
        }

        if ($driver === 'local' && ! (bool) config('code_execution.python.local.allow_unsafe', false)) {
            return $this->failure(
                'Local Python execution is disabled because it cannot isolate student code from the host. Build the Docker runner or explicitly enable unsafe local mode only on an isolated development machine.',
                $start
            );
        }

        if ($driver === 'docker') {
            $this->makeWorkspaceDockerReadable($workspacePath);
        }

        $result = $driver === 'docker'
            ? $this->runWithDocker($workspacePath, $entryRelativePath, $stdin, $timeout)
            : $this->runLocally($workspacePath, $entryRelativePath, $stdin, $timeout);

        $plots = [];
        $stdout = (string) ($result['stdout'] ?? '');
        $stdout = preg_replace_callback(
            '/__PLOT_BASE64__:(.*?):__END_PLOT__/s',
            function (array $match) use (&$plots): string {
                if (count($plots) >= (int) config('code_execution.python.max_plots', 4)) {
                    return "\n[Additional plot omitted by sandbox limit]\n";
                }

                $payload = trim((string) ($match[1] ?? ''));
                $maxBytes = (int) config('code_execution.python.max_plot_bytes', 1500000);
                $decoded = base64_decode($payload, true);

                if ($decoded === false || strlen($decoded) > $maxBytes || ! str_starts_with($decoded, "\x89PNG\r\n\x1a\n")) {
                    return "\n[Invalid or oversized plot omitted]\n";
                }

                $plots[] = $payload;
                return '';
            },
            $stdout
        ) ?? $stdout;

        $stdout = $this->truncate($stdout, (int) config('code_execution.python.max_stdout_bytes', 60000));
        $stderr = $this->truncate((string) ($result['stderr'] ?? ''), (int) config('code_execution.python.max_stderr_bytes', 60000));

        if (($result['timed_out'] ?? false) === true) {
            $stderr = trim($stderr . "\nExecution stopped after {$timeout} seconds. The program may contain an infinite loop or a task that is too expensive for the learning sandbox.");
        }

        return [
            'stdout' => trim($stdout),
            'stderr' => trim($stderr),
            'exit_code' => (int) ($result['exit_code'] ?? 1),
            'failed' => (bool) ($result['failed'] ?? true),
            'timed_out' => (bool) ($result['timed_out'] ?? false),
            'execution_time_ms' => (int) round((microtime(true) - $start) * 1000),
            'plots' => $plots,
        ];
    }

    private function runLocally(string $workspacePath, string $entryRelativePath, string $stdin, int $timeout): array
    {
        $python = $this->resolveLocalPython();
        $runner = (string) config('code_execution.python.local.runner');

        if ($python === null || ! is_file($runner)) {
            return [
                'stdout' => '',
                'stderr' => 'Python or the DataSensei runner was not found.',
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }

        $environment = $this->runnerEnvironment($workspacePath, $workspacePath, $timeout);
        $environment['DS_USE_RLIMIT_AS'] = '1';
        $entry = $this->safeJoin($workspacePath, $entryRelativePath);

        try {
            $process = Process::timeout($timeout + 2)
                ->path($workspacePath)
                ->env($environment)
                ->input($stdin)
                ->run([$python, '-B', '-u', $runner, $entry]);

            return [
                'stdout' => $process->output(),
                'stderr' => $process->errorOutput(),
                'exit_code' => $process->exitCode(),
                'failed' => $process->failed(),
                'timed_out' => false,
            ];
        } catch (\Illuminate\Process\Exceptions\ProcessTimedOutException) {
            return $this->timedOutResult();
        } catch (\Throwable) {
            return [
                'stdout' => '',
                'stderr' => 'The local development runner failed. No server details were exposed.',
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }
    }

    private function runWithDocker(string $workspacePath, string $entryRelativePath, string $stdin, int $timeout): array
    {
        $docker = (string) config('code_execution.python.docker.binary', 'docker');
        $image = (string) config('code_execution.python.docker.image', 'datasensei-python-runner:latest');

        try {
            $bindSource = $this->dockerBindSource($workspacePath);
        } catch (\Throwable) {
            return [
                'stdout' => '',
                'stderr' => 'The private Python workspace could not be prepared for Docker.',
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }

        $mount = 'type=bind,source=' . $bindSource . ',target=/input,readonly';

        $command = [
            $docker, 'run', '--rm',
            '--network', (string) config('code_execution.python.docker.network', 'none'),
            '--memory', (string) config('code_execution.python.docker.memory', '512m'),
            '--memory-swap', (string) config('code_execution.python.docker.memory_swap', '512m'),
            '--cpus', (string) config('code_execution.python.docker.cpus', '0.50'),
            '--pids-limit', (string) config('code_execution.python.docker.pids_limit', 64),
            '--ulimit', 'nofile=64:64',
            '--ipc', 'none',
            '--stop-timeout', '1',
            '--tmpfs', '/tmp:rw,nosuid,nodev,noexec,size=' . (string) config('code_execution.python.docker.tmpfs_size', '64m') . ',mode=1777',
            '--tmpfs', '/workspace:rw,nosuid,nodev,noexec,size=' . (string) config('code_execution.python.docker.workspace_tmpfs_size', '32m') . ',mode=1777',
            '--mount', $mount,
            '-w', '/workspace',
        ];

        if ((bool) config('code_execution.python.docker.cap_drop_all', true)) {
            array_push($command, '--cap-drop', 'ALL');
        }

        if ((bool) config('code_execution.python.docker.no_new_privileges', true)) {
            array_push($command, '--security-opt', 'no-new-privileges');
        }

        $runAsUser = trim((string) config('code_execution.python.docker.run_as_user', '1000:1000'));
        if ($runAsUser !== '') {
            array_push($command, '--user', $runAsUser);
        }

        if ((bool) config('code_execution.python.docker.read_only_root', true)) {
            $command[] = '--read-only';
        }

        foreach ($this->runnerEnvironment('/workspace', '/input', $timeout) as $key => $value) {
            array_push($command, '-e', $key . '=' . $value);
        }

        $command[] = $image;
        $command[] = '/workspace/' . $this->normalizeRelativePath($entryRelativePath);

        try {
            $process = Process::timeout($timeout + 4)
                ->input($stdin)
                ->run($command);

            return [
                'stdout' => $process->output(),
                'stderr' => $process->errorOutput(),
                'exit_code' => $process->exitCode(),
                'failed' => $process->failed(),
                'timed_out' => false,
            ];
        } catch (\Illuminate\Process\Exceptions\ProcessTimedOutException) {
            return $this->timedOutResult();
        } catch (\Throwable) {
            return [
                'stdout' => '',
                'stderr' => 'Docker sandbox execution failed. Verify that Docker is running and the DataSensei runner image is built.',
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }
    }

    /** @return array<string, string> */
    private function runnerEnvironment(string $workspacePath, string $inputPath, int $timeout): array
    {
        $maxOutput = (int) config('code_execution.python.max_stdout_bytes', 60000)
            + (int) config('code_execution.python.max_stderr_bytes', 60000)
            + ((int) config('code_execution.python.max_plot_bytes', 1500000) * (int) config('code_execution.python.max_plots', 4) * 2);

        return [
            'DS_WORKSPACE' => $workspacePath,
            'DS_INPUT' => $inputPath,
            'DS_CPU_SECONDS' => (string) max(1, $timeout),
            'DS_MAX_OUTPUT_BYTES' => (string) max(60000, $maxOutput),
            'DS_MAX_FILE_BYTES' => (string) config('code_execution.python.max_generated_file_bytes', 8 * 1024 * 1024),
            'DS_MAX_PLOT_BYTES' => (string) config('code_execution.python.max_plot_bytes', 1500000),
            'DS_MAX_PLOTS' => (string) config('code_execution.python.max_plots', 4),
            'DS_MEMORY_BYTES' => (string) $this->memoryStringToBytes((string) config('code_execution.python.docker.memory', '512m')),
        ];
    }

    private function resolveLocalPython(): ?string
    {
        $configured = (string) config('code_execution.python.local.binary', 'auto');
        if ($configured !== '' && $configured !== 'auto') {
            return $configured;
        }

        $candidates = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
            ? ['py', 'python']
            : ['/usr/bin/python3', '/usr/local/bin/python3', 'python3', 'python'];

        foreach ($candidates as $candidate) {
            try {
                $probe = Process::timeout(3)->run([$candidate, '--version']);
                if ($probe->successful() && str_contains(strtolower($probe->output() . $probe->errorOutput()), 'python')) {
                    return $candidate;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function safeJoin(string $basePath, string $relativePath): string
    {
        $relativePath = $this->normalizeRelativePath($relativePath);
        $fullPath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $baseReal = realpath($basePath) ?: $basePath;
        $dirReal = realpath(dirname($fullPath)) ?: dirname($fullPath);
        $baseNormal = rtrim(str_replace('\\', '/', $baseReal), '/');
        $dirNormal = str_replace('\\', '/', $dirReal);

        if ($dirNormal !== $baseNormal && ! Str::startsWith($dirNormal, $baseNormal . '/')) {
            throw new \InvalidArgumentException('Invalid workspace path.');
        }

        return $fullPath;
    }

    private function normalizeRelativePath(string $relativePath): string
    {
        $relativePath = ltrim(trim(str_replace('\\', '/', $relativePath)), '/');
        $parts = [];

        foreach (explode('/', $relativePath) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }
            if ($part === '..') {
                throw new \InvalidArgumentException('Parent-directory traversal is not allowed.');
            }
            $parts[] = $part;
        }

        return implode('/', $parts) ?: 'main.py';
    }

    private function makeWorkspaceDockerReadable(string $workspacePath): void
    {
        @chmod($workspacePath, 0755);

        foreach (File::allFiles($workspacePath) as $file) {
            @chmod($file->getPath(), 0755);
            @chmod($file->getPathname(), 0644);
        }
    }

    private function dockerBindSource(string $path): string
    {
        $resolved = realpath($path) ?: $path;

        if (! is_dir($resolved)) {
            throw new \RuntimeException('Python workspace directory does not exist.');
        }

        if (str_contains($resolved, ',')) {
            throw new \RuntimeException('Python workspace path contains an unsupported comma.');
        }

        return str_replace('\\', '/', $resolved);
    }

    private function truncate(string $value, int $maxBytes): string
    {
        if ($maxBytes <= 0 || strlen($value) <= $maxBytes) {
            return $value;
        }

        return substr($value, 0, $maxBytes) . "\n[Output truncated by DataSensei sandbox]";
    }

    private function failure(string $message, float $start): array
    {
        return [
            'stdout' => '',
            'stderr' => $message,
            'exit_code' => 1,
            'failed' => true,
            'timed_out' => false,
            'execution_time_ms' => (int) round((microtime(true) - $start) * 1000),
            'plots' => [],
        ];
    }

    private function policyFailure(string $message): array
    {
        return [
            'stdout' => '',
            'stderr' => 'Sandbox policy: ' . $message,
            'exit_code' => 126,
            'failed' => true,
            'timed_out' => false,
            'execution_time_ms' => 0,
            'plots' => [],
            'policy_blocked' => true,
        ];
    }

    private function timedOutResult(): array
    {
        return [
            'stdout' => '',
            'stderr' => '',
            'exit_code' => 124,
            'failed' => true,
            'timed_out' => true,
        ];
    }

    private function memoryStringToBytes(string $value): int
    {
        $value = strtolower(trim($value));
        if (preg_match('/^(\d+)([kmgt]?)b?$/', $value, $matches) !== 1) {
            return 256 * 1024 * 1024;
        }

        $number = (int) $matches[1];
        $power = match ($matches[2]) {
            'k' => 1,
            'm' => 2,
            'g' => 3,
            't' => 4,
            default => 0,
        };

        return $number * (1024 ** $power);
    }
}
