<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class PythonSandboxService
{
    /** Printed by the runner when it stopped the learner's own code. */
    public const TIME_LIMIT_MARKER = '__DATASENSEI_TIME_LIMIT__';

    /**
     * The only variables a locally-run student program inherits.
     *
     * Symfony merges the parent environment into a child process, so without
     * this the web server's own variables — APP_KEY, the database password, the
     * mail credentials — would be readable from student code through
     * os.environ or by opening /proc/self/environ. Everything outside this list
     * and the DS_* runner settings is removed before the program starts.
     */
    private const RUNNER_ENV_ALLOWLIST = [
        'PATH', 'HOME', 'LANG', 'LC_ALL', 'LC_CTYPE', 'TMPDIR', 'TMP', 'TEMP',
        'SYSTEMROOT', 'WINDIR', 'COMSPEC', 'PATHEXT', 'USERPROFILE', 'APPDATA',
        'LOCALAPPDATA', 'PROGRAMDATA', 'PROGRAMFILES', 'PROGRAMFILES(X86)',
        'COMMONPROGRAMFILES', 'NUMBER_OF_PROCESSORS', 'PROCESSOR_ARCHITECTURE', 'OS',
        'PYTHONHOME', 'PYTHONPATH', 'PYTHONIOENCODING', 'PYTHONUTF8',
        // The rest of the standard Windows system block. Winsock initialises
        // when asyncio is imported, joblib imports asyncio and scikit-learn
        // imports joblib, so a thinned environment made "import sklearn" fail
        // with WinError 10106 (WSAEPROVIDERFAILEDINIT) on the local driver.
        // None of these name a secret, and student code cannot read them in
        // any case: "os" is a blocked import inside the sandbox.
        'SYSTEMDRIVE', 'ALLUSERSPROFILE', 'PUBLIC', 'HOMEDRIVE', 'HOMEPATH',
        'COMPUTERNAME', 'USERNAME', 'USERDOMAIN', 'USERDOMAIN_ROAMINGPROFILE',
        'LOGONSERVER', 'SESSIONNAME', 'DRIVERDATA',
        'PROGRAMW6432', 'COMMONPROGRAMW6432', 'COMMONPROGRAMFILES(X86)',
        'PROCESSOR_IDENTIFIER', 'PROCESSOR_LEVEL', 'PROCESSOR_REVISION',
    ];

    private const INPUT_REQUIRED_MARKER = '__DATASENSEI_INPUT_REQUIRED__:';

    private const INPUTS_CONSUMED_MARKER = '__DATASENSEI_INPUTS_CONSUMED__:';

    public function __construct(
        private readonly PythonCodePolicyService $policy,
        private readonly ?PythonWarmSandbox $warmSandbox = null,
    ) {
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
        $interactiveInput = (bool) ($options['interactive_input'] ?? false);
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

        // A hung Docker Desktop used to hold the request for the entire
        // timeout + start-up budget. On a single-threaded dev server that
        // freezes the whole site, so the engine is probed briefly first and
        // the answer is cached for a few seconds.
        if ($driver === 'docker' && ! $this->dockerResponding()) {
            return $this->failure(
                'Docker is not responding, so code cannot run right now. Your program was not the problem. Ask your instructor to check that Docker Desktop is running; "php artisan python-sandbox:pool doctor" reports the details.',
                $start
            );
        }

        $executionSlot = $this->acquireExecutionSlot($timeout + $this->startupGraceSeconds() + 5);

        if (!$executionSlot) {
            return $this->failure(
                'The Python sandbox is currently at capacity. Please wait a moment and run your code again.',
                $start
            );
        }

        try {
            if ($driver === 'docker') {
                $this->makeWorkspaceDockerReadable($workspacePath);
            }

            // Warm sandbox first: a pre-started container (and, in the IDE, the
            // still-running program waiting in input()). It returns null when
            // it cannot serve this run, and the classic path below takes over.
            $result = $driver === 'docker'
                ? ($this->warmSandbox ?? app(PythonWarmSandbox::class))->run(
                    $this,
                    $workspacePath,
                    $entryRelativePath,
                    $stdin,
                    $timeout,
                    $interactiveInput,
                    is_array($options['session'] ?? null) ? $options['session'] : null
                )
                : null;

            $result ??= $driver === 'docker'
                ? $this->runWithDocker($workspacePath, $entryRelativePath, $stdin, $timeout, $interactiveInput)
                : $this->runLocally($workspacePath, $entryRelativePath, $stdin, $timeout, $interactiveInput);
        } finally {
            try {
                $executionSlot->release();
            } catch (\Throwable) {
                // The short lock TTL is the final cleanup fallback.
            }
        }

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
        $inputRequest = $this->extractInputRequest((string) ($result['stderr'] ?? ''), (int) ($result['exit_code'] ?? 1));
        $stderr = $this->truncate($inputRequest['stderr'], (int) config('code_execution.python.max_stderr_bytes', 60000));

        $hitTimeLimit = $this->programHitTimeLimit((string) ($result['stderr'] ?? ''))
            || $this->programHitTimeLimit($stdout);

        if ($hitTimeLimit) {
            // The runner already printed a full explanation; drop the marker.
            $stderr = trim(str_replace(self::TIME_LIMIT_MARKER, '', $stderr));
            $result['timed_out'] = true;
        } elseif (($result['timed_out'] ?? false) === true) {
            // The program never reached its own limit, so the sandbox itself
            // was too slow to start. Saying "infinite loop" here sends the
            // learner hunting for a bug that is not in their code.
            $stderr = trim($stderr."\nThe sandbox did not finish starting in time, so this run was cancelled. Your code was not the problem. Press Run again; if it keeps happening, ask your instructor to check that Docker is running and that the warm sandbox pool is filled (php artisan python-sandbox:pool doctor).");
        }

        $stdout = str_replace(self::TIME_LIMIT_MARKER, '', $stdout);

        return [
            // Only trailing newlines are dropped. Leading indentation and a
            // prompt's trailing space are part of what the program printed.
            'stdout' => preg_replace('/\R+$/', '', $stdout) ?? $stdout,
            'stderr' => trim($stderr),
            'exit_code' => (int) ($result['exit_code'] ?? 1),
            'failed' => (bool) ($result['failed'] ?? true),
            'timed_out' => (bool) ($result['timed_out'] ?? false),
            'execution_time_ms' => (int) round((microtime(true) - $start) * 1000),
            'plots' => $plots,
            'input_required' => $inputRequest['required'],
            'input_prompt' => $inputRequest['prompt'],
            'inputs_consumed' => $inputRequest['consumed'],
        ];
    }

    private function runLocally(string $workspacePath, string $entryRelativePath, string $stdin, int $timeout, bool $interactiveInput): array
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

        $environment = $this->runnerEnvironment($workspacePath, $workspacePath, $timeout, $interactiveInput);
        $environment['DS_USE_RLIMIT_AS'] = '1';
        $entry = $this->safeJoin($workspacePath, $entryRelativePath);

        try {
            $process = Process::timeout($timeout + 2)
                ->path($workspacePath)
                ->env($this->isolatedEnvironment($environment))
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

    /**
     * How long the sandbox may take to start before the learner's code runs.
     * Docker Desktop on Windows is the slow case: a cold "docker run" of this
     * image is routinely 3-8 seconds, and the warm pool exists to avoid it.
     */
    public function startupGraceSeconds(): int
    {
        return max(4, min(60, (int) config('code_execution.python.startup_grace_seconds', 20)));
    }

    /**
     * Is the Docker engine answering at all?
     *
     * "docker version" against a healthy engine is a few hundred milliseconds;
     * against a broken Docker Desktop it can hang indefinitely. The result is
     * cached briefly so a class pressing Run together pays the probe once.
     */
    public function dockerResponding(): bool
    {
        $cacheKey = 'datasensei:python-sandbox:docker-healthy';
        $cached = Cache::get($cacheKey);

        if (is_bool($cached)) {
            return $cached;
        }

        $seconds = max(2, min(15, (int) config('code_execution.python.docker.health_timeout_seconds', 6)));

        try {
            $healthy = Process::timeout($seconds)
                ->run([$this->dockerBinary(), 'version', '--format', '{{.Server.Version}}'])
                ->successful();
        } catch (\Throwable) {
            // A timeout means the engine is wedged, which is exactly the case
            // this probe exists to catch.
            $healthy = false;
        }

        // Remember "up" a little longer than "down", so recovery is noticed
        // quickly but a class does not re-probe a dead engine on every click.
        Cache::put($cacheKey, $healthy, $healthy ? 15 : 5);

        return $healthy;
    }

    private function dockerBinary(): string
    {
        return (string) config('code_execution.python.docker.binary', 'docker');
    }

    /**
     * The runner prints a marker when it stopped the learner's own code. Its
     * absence on a timeout means the sandbox never got that far, which is an
     * environment problem and must not be reported as an infinite loop.
     */
    public function programHitTimeLimit(string $stderr): bool
    {
        return str_contains($stderr, self::TIME_LIMIT_MARKER);
    }

    private function runWithDocker(string $workspacePath, string $entryRelativePath, string $stdin, int $timeout, bool $interactiveInput): array
    {
        $docker = (string) config('code_execution.python.docker.binary', 'docker');
        $image = (string) config('code_execution.python.docker.image', 'datasensei-python-runner:latest');
        $containerName = 'datasensei-python-'.str_replace('-', '', (string) Str::uuid());

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

        // Student files are exposed read-only at /input. The trusted runner copies
        // them into a fresh tmpfs at /workspace before execution, so generated
        // files work without allowing code to alter Laravel's persisted workspace.
        $inputMount = 'type=bind,source='.$bindSource.',target=/input,readonly';

        $command = $this->dockerRunCommand(
            $containerName,
            $this->runnerEnvironment('/workspace', '/input', $timeout, $interactiveInput),
            $inputMount,
            false
        );

        $command[] = $image;
        $command[] = '/workspace/' . $this->normalizeRelativePath($entryRelativePath);

        try {
            // The container has to boot before the learner's clock starts, and
            // on a busy laptop "docker run" alone can take several seconds. The
            // runner stops the program itself at DS_WALL_SECONDS, so this outer
            // limit only needs to be generous enough that a slow start is never
            // mistaken for the learner's infinite loop.
            $process = Process::timeout($timeout + $this->startupGraceSeconds())
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
            $this->forceRemoveDockerContainer($docker, $containerName);
            return $this->timedOutResult();
        } catch (\Throwable) {
            $this->forceRemoveDockerContainer($docker, $containerName);
            return [
                'stdout' => '',
                'stderr' => 'Docker sandbox execution failed. Verify that Docker is running and the DataSensei runner image is built.',
                'exit_code' => 1,
                'failed' => true,
                'timed_out' => false,
            ];
        }
    }

    /**
     * The one place that defines how a sandbox container is confined. The
     * classic run and the warm pool both build their command here, so a warm
     * container can never be less restricted than a classic one.
     *
     * @param  array<string, string>  $environment
     * @return list<string>
     */
    public function dockerRunCommand(string $containerName, array $environment, ?string $inputMount, bool $detached): array
    {
        $docker = (string) config('code_execution.python.docker.binary', 'docker');

        $command = $detached
            // Standby container: kept after exit so its log can still be read,
            // removed explicitly by the warm sandbox, never given a host mount.
            ? [
                $docker, 'run', '--detach',
                '--label', 'datasensei.sandbox=warm',
                '--label', 'datasensei.protocol='.PythonWarmSandbox::RUNNER_PROTOCOL,
                // Below the default of 1024: while a standby container imports
                // its libraries it yields the CPU to classic runs and to the
                // rest of the machine. It changes nothing when the CPU is idle.
                '--cpu-shares', (string) max(2, (int) config('code_execution.python.warm.cpu_shares', 512)),
            ]
            // Without --interactive the container's stdin is /dev/null, so every
            // input() call reached EOF no matter what the learner typed and the
            // IDE asked for the same value forever. No --tty: the runner needs a
            // plain pipe, not a terminal.
            : [$docker, 'run', '--rm', '--interactive'];

        array_push(
            $command,
            '--name', $containerName,
            '--network', (string) config('code_execution.python.docker.network', 'none'),
            '--memory', (string) config('code_execution.python.docker.memory', '512m'),
            '--memory-swap', (string) config('code_execution.python.docker.memory_swap', '512m'),
            '--cpus', (string) config('code_execution.python.docker.cpus', '0.50'),
            '--pids-limit', (string) config('code_execution.python.docker.pids_limit', 64),
            '--ulimit', 'nofile=64:64',
            '--ipc', 'none',
            '--stop-timeout', '1',
            '--tmpfs', '/tmp:rw,nosuid,nodev,noexec,size=' . (string) config('code_execution.python.docker.tmpfs_size', '64m') . ',mode=1777',
            '--tmpfs', '/workspace:rw,nosuid,nodev,noexec,size=' . (string) config('code_execution.python.docker.workspace_tmpfs_size', '32m') . ',mode=0770,uid=1000,gid=1000',
            '-w', '/workspace'
        );

        if ($inputMount !== null) {
            array_push($command, '--mount', $inputMount);
        }

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

        foreach ($environment as $key => $value) {
            array_push($command, '-e', $key . '=' . $value);
        }

        return $command;
    }

    /**
     * Limits shared by every job of a standby container. The per-run values
     * (CPU seconds, interactive input) travel inside the job instead.
     *
     * @return array<string, string>
     */
    public function containerEnvironment(): array
    {
        $environment = $this->runnerEnvironment(
            '/workspace',
            '/input',
            (int) config('code_execution.python.timeout_seconds', 10)
        );
        unset($environment['DS_CPU_SECONDS']);

        return $environment;
    }

    private function forceRemoveDockerContainer(string $docker, string $containerName): void
    {
        try {
            Process::timeout(3)->run([$docker, 'rm', '-f', $containerName]);
        } catch (\Throwable) {
            // Docker may already have removed the --rm container. Nothing else
            // should delay the learner response during timeout cleanup.
        }
    }

    /** @return array{stderr:string, required:bool, prompt:?string, consumed:?int} */
    private function extractInputRequest(string $stderr, int $exitCode): array
    {
        $prompt = null;
        $consumed = null;
        $remainingLines = [];
        $lines = preg_split('/\R/u', $stderr) ?: [];

        foreach ($lines as $line) {
            if (str_starts_with($line, self::INPUT_REQUIRED_MARKER)) {
                $encodedPrompt = substr($line, strlen(self::INPUT_REQUIRED_MARKER));
                $decodedPrompt = base64_decode($encodedPrompt, true);

                if ($decodedPrompt !== false) {
                    $prompt = $decodedPrompt;
                    continue;
                }
            }

            if (str_starts_with($line, self::INPUTS_CONSUMED_MARKER)) {
                $consumed = (int) substr($line, strlen(self::INPUTS_CONSUMED_MARKER));
                continue;
            }

            $remainingLines[] = $line;
        }

        $required = $exitCode === 75 && $prompt !== null;

        return [
            'stderr' => trim(implode("\n", $remainingLines)),
            'required' => $required,
            'prompt' => $required ? $prompt : null,
            'consumed' => $consumed === null ? null : max(0, $consumed),
        ];
    }

    /** @return array<string, string> */
    /**
     * Drops every inherited variable that is not on the allowlist.
     *
     * Symfony removes a variable from the child process when its value is
     * false, so the web server's secrets never reach the student's program.
     * The Docker driver does not need this: a container starts with a clean
     * environment and only receives the DS_* values passed with -e.
     *
     * @param  array<string, string>  $environment
     * @return array<string, string|false>
     */
    private function isolatedEnvironment(array $environment): array
    {
        $inherited = getenv();

        if (! is_array($inherited)) {
            return $environment;
        }

        foreach ($inherited as $name => $value) {
            $upper = strtoupper((string) $name);

            if (array_key_exists($upper, $environment)) {
                continue;
            }

            if (in_array($upper, self::RUNNER_ENV_ALLOWLIST, true)) {
                // Passed by value, not left to inheritance. Symfony builds the
                // child's default environment from getenv() intersected with
                // $_SERVER (Process::getDefaultEnv), and under "php artisan
                // serve" $_SERVER holds request variables rather than the
                // system block. SystemRoot was therefore dropped on the way to
                // the child, Winsock could not expand the %SystemRoot% paths in
                // its provider catalogue, and every import of asyncio - so
                // joblib, so scikit-learn - died with WinError 10106.
                // Keeping a name on the allowlist only stops us deleting it; it
                // cannot put back something the default environment never had.
                $environment[(string) $name] = (string) $value;

                continue;
            }

            $environment[(string) $name] = false;
        }

        return $environment;
    }

    private function runnerEnvironment(string $workspacePath, string $inputPath, int $timeout, bool $interactiveInput = false): array
    {
        $maxOutput = (int) config('code_execution.python.max_stdout_bytes', 60000)
            + (int) config('code_execution.python.max_stderr_bytes', 60000)
            + ((int) config('code_execution.python.max_plot_bytes', 1500000) * (int) config('code_execution.python.max_plots', 4) * 2);

        $environment = [
            'DS_WORKSPACE' => $workspacePath,
            'DS_INPUT' => $inputPath,
            // RLIMIT_CPU, kept as the hard backstop against a busy loop.
            'DS_CPU_SECONDS' => (string) max(1, $timeout),
            // The learner's wall-clock budget, measured by the runner from
            // their first statement so "docker run" and "import pandas" are
            // not billed to them.
            'DS_WALL_SECONDS' => (string) max(1, $timeout),
            'DS_MAX_OUTPUT_BYTES' => (string) max(60000, $maxOutput),
            'DS_MAX_FILE_BYTES' => (string) config('code_execution.python.max_generated_file_bytes', 8 * 1024 * 1024),
            'DS_MAX_PLOT_BYTES' => (string) config('code_execution.python.max_plot_bytes', 1500000),
            'DS_MAX_PLOTS' => (string) config('code_execution.python.max_plots', 4),
            'DS_MEMORY_BYTES' => (string) $this->memoryStringToBytes((string) config('code_execution.python.docker.memory', '512m')),
        ];

        if ($interactiveInput) {
            $environment['DS_INTERACTIVE_INPUT'] = '1';
        }

        return $environment;
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

    private function acquireExecutionSlot(int $seconds): ?Lock
    {
        $slotCount = max(1, min(64, (int) config('code_execution.python.max_concurrent_executions', 8)));
        $start = random_int(0, $slotCount - 1);

        try {
            for ($offset = 0; $offset < $slotCount; $offset++) {
                $slot = ($start + $offset) % $slotCount;
                $lock = Cache::lock('datasensei:python-execution:slot:'.$slot, max(5, $seconds));

                if ($lock->get()) {
                    return $lock;
                }
            }
        } catch (\Throwable) {
            // Fail closed: running unbounded work is less safe than asking the
            // learner to retry when the lock backend is unavailable.
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
        // The container receives this directory as a read-only bind mount. Keep
        // permissions narrow while still allowing its unprivileged UID to read it.
        @chmod($workspacePath, 0755);

        foreach (File::directories($workspacePath) as $directory) {
            @chmod($directory, 0755);
        }

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
