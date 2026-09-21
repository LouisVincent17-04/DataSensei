<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * Warm Docker sandbox for Python runs.
 *
 * Design rule: a step of a run costs exactly ONE Docker CLI call, the same
 * blocking Process::run() primitive the classic runner uses, and that call is
 * a "docker exec" into a container that already exists, which is cheaper than
 * the "docker run" the classic runner pays for every step. Anything slower or
 * less certain than the classic path is not used on the request path at all:
 *
 *  - Which standby containers are ready is read from the cache. It is written
 *    by the background "python-sandbox:pool maintain" process, which is the
 *    only place that lists containers, waits for them and creates new ones.
 *  - A container is claimed inside the container (O_EXCL file), so no rename
 *    or listing is needed and two requests can never share one.
 *  - Program output is read from files in the container's own /tmp by the
 *    attach helper, not from "docker logs".
 *  - When a program waits in input(), the container stays alive and the next
 *    request feeds the answer to the same process (no replay).
 *  - After any failure the warm path switches itself off for a while and the
 *    classic runner takes every run, so a broken pool can never slow runs
 *    down or fail them repeatedly.
 *
 * Containers are created by PythonSandboxService::dockerRunCommand(), the same
 * function as a classic run (no network, read-only root, dropped capabilities,
 * memory/CPU/pid limits, unprivileged user), and have no host mount at all.
 * Each one runs exactly one program and is then removed.
 */
class PythonWarmSandbox
{
    public const RUNNER_PROTOCOL = 4;

    private const WARM_PREFIX = 'dswarm-';

    private const RUN_PREFIX = 'dsrun-';

    private const INPUT_REQUIRED_MARKER = '__DATASENSEI_INPUT_REQUIRED__:';

    private const INPUTS_CONSUMED_MARKER = '__DATASENSEI_INPUTS_CONSUMED__:';

    private const READY_MARKER = '__DATASENSEI_READY__:';

    private const READY_LIST = 'datasensei:python-warm:ready-list';

    private const CLAIMED_LIST = 'datasensei:python-warm:claimed-list';

    private const BREAKER = 'datasensei:python-warm:breaker';

    /** Preloads the image does not have. Harmless, but worth showing. */
    private const SKIPPED_PRELOADS = 'datasensei:python-warm:skipped-preloads';

    public function enabled(): bool
    {
        if (! (bool) config('code_execution.python.warm.enabled', true)) {
            return false;
        }

        if (strtolower((string) config('code_execution.python.driver', 'docker')) !== 'docker') {
            return false;
        }

        return $this->imageSupportsProtocol();
    }

    /** True while the warm path has switched itself off after a failure. */
    public function suspendedReason(): ?string
    {
        $reason = Cache::get(self::BREAKER);

        return is_string($reason) ? $reason : null;
    }

    /**
     * Run one program, or continue the learner's waiting program.
     *
     * @param  array{key?:string, token?:?string, context?:string}|null  $session
     * @return array{stdout:string, stderr:string, exit_code:int, failed:bool, timed_out:bool}|null
     */
    public function run(
        PythonSandboxService $sandbox,
        string $workspacePath,
        string $entryRelativePath,
        string $stdin,
        int $timeout,
        bool $interactive,
        ?array $session
    ): ?array {
        if (! $this->enabled()) {
            return null;
        }

        $sessionKey = $interactive && ! empty($session['key']) && ! empty($session['token'])
            ? 'datasensei:python-session:'.$session['key']
            : null;

        try {
            if ($sessionKey !== null) {
                $continued = $this->continueSession($sessionKey, $session, $stdin, $timeout);
                if ($continued !== null) {
                    return $continued;
                }
            }

            if ($this->suspendedReason() !== null) {
                return null;
            }

            $job = $this->buildJob($workspacePath, $entryRelativePath, $stdin, $timeout, $interactive);
            if ($job === null) {
                return null;
            }

            $started = $this->startInReadyContainer($job, $timeout);

            if ($started === null && $interactive && $this->entryAsksForInput($workspacePath, $entryRelativePath)) {
                // No standby container is ready. A program that reads input is
                // still better off in a container that stays alive: one extra
                // call now saves a full replay for every answer.
                $started = $this->startInNewContainer($sandbox, $job, $timeout);
            }

            if ($started === null) {
                return null;
            }

            [$container, $reply] = $started;

            if ($reply['state'] === 'waiting' && $sessionKey !== null) {
                Cache::put($sessionKey, [
                    'container' => $container,
                    'token' => (string) $session['token'],
                    'context' => (string) ($session['context'] ?? ''),
                    'fed' => $this->lines($stdin),
                    'seq' => (int) ($reply['seq'] ?? 0),
                ], $this->idleSeconds() + 60);
            } else {
                $this->removeInBackground($container);
            }

            return $this->toResult($reply);
        } catch (\Throwable $exception) {
            $this->suspend('exception: '.$exception->getMessage());

            return null;
        }
    }

    /**
     * Background upkeep: sweep leftovers, create standby containers one at a
     * time, wait until each has finished importing, publish the ready list.
     *
     * @return array{created:int, removed:int, warm:int, ready:int}
     */
    public function maintain(PythonSandboxService $sandbox): array
    {
        $summary = ['created' => 0, 'removed' => 0, 'warm' => 0, 'ready' => 0];

        if (! $this->enabled()) {
            Cache::forget(self::READY_LIST);

            return $summary;
        }

        $lock = Cache::lock('datasensei:python-warm:maintain', 240);

        if (! $lock->get()) {
            return $summary;
        }

        try {
            $now = time();
            $standbyLife = $this->standbySeconds();
            $runLife = $this->idleSeconds() + (int) config('code_execution.python.timeout_seconds', 10) * 45 + 120;
            $claimed = $this->claimedNames();
            $ready = [];

            foreach ($this->listContainers() as $container) {
                $age = $now - $container['created'];
                $isClaimed = isset($claimed[$container['name']]);
                $expired = $isClaimed ? $age > $standbyLife + $runLife : $age > $standbyLife - 60;

                // A container that is not running yet may still be starting,
                // and one that just exited is removed by the request that owns
                // it. Only leftovers are swept.
                $leftover = ! $container['running'] && $age > 45;

                if ($leftover || $expired || ! $container['current']) {
                    $this->remove($container['name']);
                    $summary['removed']++;

                    continue;
                }

                if ($isClaimed || ! $container['running']) {
                    continue;
                }

                $summary['warm']++;

                if ($this->waitUntilReady($container['name'], 0)) {
                    $ready[] = $container['name'];
                }
            }

            $this->publishReady($ready);
            $target = max(0, min(16, (int) config('code_execution.python.warm.pool_size', 2)));

            // One at a time, each only after the previous one has finished its
            // imports: refilling must never compete with itself, and as little
            // as possible with a learner's running program.
            while ($summary['warm'] < $target) {
                $name = $this->createStandby($sandbox, true);

                if ($name === null) {
                    break;
                }

                $summary['warm']++;
                $summary['created']++;

                if ($this->waitUntilReady($name, 120)) {
                    $ready[] = $name;
                    $this->publishReady($ready);
                    $this->recordSkippedPreloads($name);

                    continue;
                }

                // Never ready: most likely a library failed to import. Keep
                // the reason, stop creating more, let the classic runner work.
                $logs = Process::timeout(20)->run([$this->docker(), 'logs', $name]);
                $this->remove($name);
                $summary['warm']--;

                if (preg_match('/__DATASENSEI_PRELOAD_FAILED__:([^\r\n]*)/', $logs->errorOutput().$logs->output(), $match) === 1) {
                    $this->suspend('preloading failed in the runner image: '.Str::limit(trim($match[1]), 200));
                }

                break;
            }

            $summary['ready'] = count($ready);
        } finally {
            $lock->release();
        }

        return $summary;
    }

    /** Remove every pool and session container (used by the artisan command). */
    public function clear(): int
    {
        $removed = 0;

        foreach ($this->listContainers() as $container) {
            $this->remove($container['name']);
            $removed++;
        }

        Cache::forget(self::READY_LIST);
        Cache::forget(self::CLAIMED_LIST);
        Cache::forget(self::BREAKER);

        return $removed;
    }

    /** @return list<array{name:string, running:bool, created:int, current:bool}> */
    public function listContainers(): array
    {
        $process = Process::timeout(20)->run([
            $this->docker(), 'ps', '-a',
            '--filter', 'label=datasensei.sandbox=warm',
            '--format', '{{.Names}}|{{.State}}|{{.Label "datasensei.protocol"}}',
        ]);

        if (! $process->successful()) {
            return [];
        }

        $containers = [];

        foreach (preg_split('/\R/', trim($process->output())) ?: [] as $line) {
            [$name, $state, $protocol] = array_pad(explode('|', trim($line), 3), 3, '');

            if (preg_match('/^(?:'.self::WARM_PREFIX.'|'.self::RUN_PREFIX.')(\d{9,11})-/', $name, $match) !== 1) {
                continue;
            }

            $containers[] = [
                'name' => $name,
                'running' => $state === 'running',
                'created' => (int) $match[1],
                // Containers started by an older build speak an older protocol.
                'current' => (int) $protocol === self::RUNNER_PROTOCOL,
            ];
        }

        usort($containers, static fn (array $a, array $b): int => $a['created'] <=> $b['created']);

        return $containers;
    }

    /** @return list<string> */
    public function readyNames(): array
    {
        $names = Cache::get(self::READY_LIST);

        return is_array($names) ? array_values(array_filter($names, 'is_string')) : [];
    }

    /**
     * Preloaded libraries this image does not contain. They are skipped so one
     * optional package cannot empty the pool, but an instructor should still
     * know the image is behind the Dockerfile.
     */
    public function skippedPreloads(): ?string
    {
        $value = Cache::get(self::SKIPPED_PRELOADS);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function recordSkippedPreloads(string $container): void
    {
        try {
            $logs = Process::timeout(10)->run([$this->docker(), 'logs', $container]);
            $text = $logs->errorOutput().$logs->output();

            if (preg_match('/__DATASENSEI_PRELOAD_SKIPPED__:([^\r\n]*)/', $text, $match) === 1) {
                Cache::put(self::SKIPPED_PRELOADS, Str::limit(trim($match[1]), 300), 3600);

                return;
            }

            Cache::forget(self::SKIPPED_PRELOADS);
        } catch (\Throwable) {
            // Diagnostics only.
        }
    }

    /**
     * Ask a background PHP process to refill the pool, so the learner's
     * response never waits for "docker run".
     */
    public function requestMaintenance(): void
    {
        try {
            if (! Cache::add('datasensei:python-warm:maintain-requested', 1, 5)) {
                return;
            }

            $php = (string) config('code_execution.python.warm.php_binary', '');
            if ($php === '') {
                $php = str_starts_with(strtolower(basename(PHP_BINARY)), 'php') ? PHP_BINARY : 'php';
            }

            $artisan = base_path('artisan');

            // --after: let the run that triggered this finish first.
            $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? 'start /B "" "'.$php.'" "'.$artisan.'" python-sandbox:pool maintain --after=4 > NUL 2>&1'
                : 'nohup '.escapeshellarg($php).' '.escapeshellarg($artisan).' python-sandbox:pool maintain --after=4 > /dev/null 2>&1 &';

            $handle = @popen($command, 'r');

            if (is_resource($handle)) {
                pclose($handle);
            }
        } catch (\Throwable) {
            // The scheduler refills the pool every minute as well.
        }
    }

    private function continueSession(string $sessionKey, array $session, string $stdin, int $timeout): ?array
    {
        $state = Cache::get($sessionKey);

        if (! is_array($state) || empty($state['container'])) {
            return null;
        }

        $container = (string) $state['container'];
        $lines = $this->lines($stdin);
        $fed = is_array($state['fed'] ?? null) ? $state['fed'] : [];
        $sameRun = hash_equals((string) ($state['token'] ?? ''), (string) $session['token'])
            && (string) ($state['context'] ?? '') === (string) ($session['context'] ?? '')
            && count($lines) > count($fed)
            && array_slice($lines, 0, count($fed)) === $fed;

        if (! $sameRun) {
            // A new Run (or edited code): the waiting program is abandoned.
            Cache::forget($sessionKey);
            $this->removeInBackground($container);

            return null;
        }

        $reply = $this->attach($container, [
            'op' => 'feed',
            'lines' => implode("\n", array_slice($lines, count($fed)))."\n",
            'seq' => (int) ($state['seq'] ?? 0),
        ], $timeout);

        if ($reply === null || ! in_array($reply['state'], ['waiting', 'exited', 'timeout'], true)) {
            // The container expired while the learner was thinking. Start
            // again with every answer, exactly like the classic replay.
            Cache::forget($sessionKey);
            $this->removeInBackground($container);

            return null;
        }

        if ($reply['state'] === 'waiting') {
            $state['fed'] = $lines;
            $state['seq'] = (int) ($reply['seq'] ?? 0);
            Cache::put($sessionKey, $state, $this->idleSeconds() + 60);
        } else {
            Cache::forget($sessionKey);
            $this->removeInBackground($container);
        }

        return $this->toResult($reply);
    }

    /** @return array{0:string, 1:array}|null */
    private function startInReadyContainer(array $job, int $timeout): ?array
    {
        $tried = 0;

        foreach ($this->readyNames() as $container) {
            if (++$tried > 2) {
                break;
            }

            $this->markClaimed($container);
            $reply = $this->attach($container, ['op' => 'start', 'job' => $job], $timeout);
            $this->requestMaintenance();

            if ($reply !== null && in_array($reply['state'], ['waiting', 'exited', 'timeout'], true)) {
                return [$container, $reply];
            }

            if ($reply !== null && $reply['state'] === 'taken') {
                // Another request won this one; it stays theirs.
                continue;
            }

            // Gone, stuck or never picked the job up. Nothing ran in it.
            $this->removeInBackground($container);

            if ($reply !== null && $reply['state'] === 'not_started') {
                $this->suspend('a ready container did not start the job in time');

                return null;
            }
        }

        if ($tried === 0) {
            $this->requestMaintenance();
        }

        return null;
    }

    /** @return array{0:string, 1:array}|null */
    private function startInNewContainer(PythonSandboxService $sandbox, array $job, int $timeout): ?array
    {
        $container = $this->createStandby($sandbox, false, self::RUN_PREFIX.time().'-'.Str::lower(Str::random(12)));

        if ($container === null) {
            $this->suspend('docker run for a standby container failed');

            return null;
        }

        $this->markClaimed($container);
        $reply = $this->attach($container, ['op' => 'start', 'job' => $job], $timeout);

        if ($reply !== null && in_array($reply['state'], ['waiting', 'exited', 'timeout'], true)) {
            return [$container, $reply];
        }

        $this->removeInBackground($container);
        $this->suspend('a new container did not start the job in time');

        return null;
    }

    /**
     * The single Docker call of a step: hand the request to the attach helper
     * inside the container and wait for its one JSON reply.
     */
    private function attach(string $container, array $request, int $timeout): ?array
    {
        $pickup = max(2, (int) config('code_execution.python.warm.pickup_seconds', 6));
        $request += [
            'wait' => $timeout + 4,
            'pickup' => $pickup,
            'max_bytes' => (int) config('code_execution.python.warm.max_log_bytes', 16 * 1024 * 1024),
        ];

        try {
            $process = Process::timeout($timeout + $pickup + 10)
                ->input(json_encode($request, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE))
                ->run([
                    $this->docker(), 'exec', '-i', $container,
                    'python', '-S', '-B', '/opt/datasensei/datasensei_runner.py', '--attach',
                ]);
        } catch (\Throwable) {
            return null;
        }

        $reply = json_decode(trim($process->output()), true);

        if (is_array($reply) && is_string($reply['state'] ?? null)) {
            if ($reply['state'] === 'timeout') {
                $this->remove($container);
            }

            return $reply;
        }

        // No reply: the program was killed under the helper (memory or CPU
        // limit, os._exit) and took the container down with it.
        if (($request['op'] ?? '') !== '' && $this->containerExited($container, $exitCode)) {
            return ['state' => 'exited', 'exit_code' => $exitCode, 'stdout' => '', 'stderr' => ''];
        }

        return null;
    }

    private function containerExited(string $container, ?int &$exitCode): bool
    {
        $inspect = Process::timeout(15)->run([
            $this->docker(), 'inspect', '-f', '{{.State.Running}}|{{.State.ExitCode}}', $container,
        ]);

        [$running, $code] = array_pad(explode('|', trim($inspect->output()), 2), 2, '');
        $exitCode = is_numeric($code) ? (int) $code : 1;

        return $inspect->successful() && $running === 'false';
    }

    /** Shape the helper's reply like the classic runner's raw process result. */
    private function toResult(array $reply): array
    {
        $stdout = (string) ($reply['stdout'] ?? '');
        $stderr = rtrim((string) ($reply['stderr'] ?? ''));

        if ($reply['state'] === 'timeout') {
            return ['stdout' => $stdout, 'stderr' => $stderr, 'exit_code' => 124, 'failed' => true, 'timed_out' => true];
        }

        if ($reply['state'] === 'waiting') {
            // The same two lines the classic runner prints for a pending input().
            $stderr .= "\n".self::INPUT_REQUIRED_MARKER.base64_encode((string) ($reply['prompt'] ?? ''))
                ."\n".self::INPUTS_CONSUMED_MARKER.(int) ($reply['consumed'] ?? 0);

            return ['stdout' => $stdout, 'stderr' => $stderr, 'exit_code' => 75, 'failed' => true, 'timed_out' => false];
        }

        $exitCode = (int) ($reply['exit_code'] ?? 1);

        return ['stdout' => $stdout, 'stderr' => $stderr, 'exit_code' => $exitCode, 'failed' => $exitCode !== 0, 'timed_out' => false];
    }

    private function entryAsksForInput(string $workspacePath, string $entryRelativePath): bool
    {
        $path = rtrim($workspacePath, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $entryRelativePath);

        return is_file($path) && str_contains((string) File::get($path), 'input(');
    }

    private function waitUntilReady(string $container, int $seconds): bool
    {
        $deadline = microtime(true) + $seconds;

        do {
            $logs = Process::timeout(20)->run([$this->docker(), 'logs', $container]);

            if (! $logs->successful()) {
                return false;
            }

            $text = $logs->errorOutput().$logs->output();

            if (str_contains($text, self::READY_MARKER)) {
                return true;
            }

            if (str_contains($text, '__DATASENSEI_PRELOAD_FAILED__')) {
                return false;
            }

            if ($seconds > 0) {
                usleep(750000);
            }
        } while (microtime(true) < $deadline);

        return false;
    }

    private function createStandby(PythonSandboxService $sandbox, bool $preload, ?string $name = null): ?string
    {
        $name ??= self::WARM_PREFIX.time().'-'.Str::lower(Str::random(10));

        $environment = $sandbox->containerEnvironment();
        $environment['DS_SESSION_DIR'] = '/tmp';
        $environment['DS_STANDBY_SECONDS'] = (string) $this->standbySeconds();
        $environment['DS_PRELOAD'] = $preload ? (string) config('code_execution.python.warm.preload', 'numpy,pandas,matplotlib.pyplot') : '';

        $command = $sandbox->dockerRunCommand($name, $environment, null, true);
        $command[] = (string) config('code_execution.python.docker.image', 'datasensei-python-runner:latest');
        $command[] = '--standby';

        $process = Process::timeout(90)->run($command);

        if (! $process->successful()) {
            Log::warning('Could not start a warm Python sandbox container.', [
                'error' => Str::limit(trim($process->errorOutput()), 400),
            ]);
            $this->remove($name);

            return null;
        }

        return $name;
    }

    private function buildJob(string $workspacePath, string $entryRelativePath, string $stdin, int $timeout, bool $interactive): ?array
    {
        $base = rtrim(str_replace('\\', '/', realpath($workspacePath) ?: $workspacePath), '/');
        $maxBytes = (int) config('code_execution.python.warm.max_job_bytes', 6 * 1024 * 1024);
        $files = [];
        $dirs = [];
        $total = 0;

        foreach (File::directories($workspacePath) as $directory) {
            $dirs[] = ltrim(substr(str_replace('\\', '/', $directory), strlen($base)), '/');
        }

        foreach (File::allFiles($workspacePath, true) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());
            $relative = ltrim(substr($path, strlen($base)), '/');
            $total += $file->getSize();

            if ($relative === '' || $total > $maxBytes) {
                // Too large for one job: the classic bind-mount path handles it.
                return null;
            }

            $dirs[] = trim(dirname($relative), './');
            $files[] = ['path' => $relative, 'b64' => base64_encode((string) File::get($file->getPathname()))];
        }

        return [
            'entry' => ltrim(str_replace('\\', '/', $entryRelativePath), '/'),
            'dirs' => array_values(array_unique(array_filter($dirs, static fn ($dir) => $dir !== ''))),
            'files' => $files,
            'stdin' => $stdin,
            'interactive' => $interactive,
            'cpu_seconds' => max(1, $timeout),
            // Same meaning as DS_WALL_SECONDS on the classic path: the clock
            // starts at the learner's first statement, not at container start.
            'wall_seconds' => max(1, $timeout),
            'idle_seconds' => $this->idleSeconds(),
        ];
    }

    private function imageSupportsProtocol(): bool
    {
        $image = (string) config('code_execution.python.docker.image', 'datasensei-python-runner:latest');

        // Asked rarely (and never again until it expires or the cache is
        // cleared): it is one more Docker call on the request path otherwise.
        return (bool) Cache::remember('datasensei:python-warm:protocol:'.self::RUNNER_PROTOCOL.':'.md5($image), 1800, function () use ($image): bool {
            try {
                $process = Process::timeout(15)->run([
                    $this->docker(), 'image', 'inspect', '-f',
                    '{{ index .Config.Labels "datasensei.runner.protocol" }}', $image,
                ]);

                return $process->successful() && (int) trim($process->output()) >= self::RUNNER_PROTOCOL;
            } catch (\Throwable) {
                return false;
            }
        });
    }

    /** Switch the warm path off for a while; the classic runner takes over. */
    private function suspend(string $reason): void
    {
        $minutes = max(1, (int) config('code_execution.python.warm.suspend_minutes', 10));
        Cache::put(self::BREAKER, $reason, $minutes * 60);
        Cache::forget(self::READY_LIST);

        Log::warning('Warm Python sandbox suspended; runs use the classic runner.', [
            'reason' => $reason,
            'minutes' => $minutes,
        ]);
    }

    /** @param  list<string>  $names */
    private function publishReady(array $names): void
    {
        $claimed = $this->claimedNames();
        $names = array_values(array_filter(array_unique($names), static fn (string $name): bool => ! isset($claimed[$name])));

        Cache::put(self::READY_LIST, $names, $this->standbySeconds());
    }

    private function markClaimed(string $container): void
    {
        $claimed = $this->claimedNames();
        $claimed[$container] = time();
        Cache::put(self::CLAIMED_LIST, $claimed, $this->standbySeconds() * 2);

        Cache::put(
            self::READY_LIST,
            array_values(array_filter($this->readyNames(), static fn (string $name): bool => $name !== $container)),
            $this->standbySeconds()
        );
    }

    /** @return array<string, int> */
    private function claimedNames(): array
    {
        $claimed = Cache::get(self::CLAIMED_LIST);
        $cutoff = time() - $this->standbySeconds() * 2;

        return is_array($claimed)
            ? array_filter($claimed, static fn ($at): bool => is_int($at) && $at > $cutoff)
            : [];
    }

    /**
     * Remove a finished container without making the learner wait for it.
     * Falls back to the blocking removal when no background shell is available.
     */
    private function removeInBackground(string $container): void
    {
        if (preg_match('/^[A-Za-z0-9_.-]+$/', $container) !== 1) {
            return;
        }

        try {
            $docker = $this->docker();
            $command = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? 'start /B "" "'.$docker.'" rm -f '.$container.' > NUL 2>&1'
                : 'nohup '.escapeshellarg($docker).' rm -f '.escapeshellarg($container).' > /dev/null 2>&1 &';
            $handle = @popen($command, 'r');

            if (is_resource($handle)) {
                pclose($handle);

                return;
            }
        } catch (\Throwable) {
            // Blocking removal below.
        }

        $this->remove($container);
    }

    private function remove(string $container): void
    {
        try {
            Process::timeout(20)->run([$this->docker(), 'rm', '-f', $container]);
        } catch (\Throwable) {
            // The maintenance sweep removes leftovers.
        }
    }

    /** @return list<string> */
    private function lines(string $stdin): array
    {
        if ($stdin === '') {
            return [];
        }

        $stdin = str_replace(["\r\n", "\r"], "\n", $stdin);

        return explode("\n", str_ends_with($stdin, "\n") ? substr($stdin, 0, -1) : $stdin);
    }

    private function idleSeconds(): int
    {
        return max(30, min(1800, (int) config('code_execution.python.warm.input_idle_seconds', 300)));
    }

    private function standbySeconds(): int
    {
        return max(120, min(86400, (int) config('code_execution.python.warm.standby_seconds', 1800)));
    }

    private function docker(): string
    {
        return (string) config('code_execution.python.docker.binary', 'docker');
    }
}
