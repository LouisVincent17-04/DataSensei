<?php

namespace App\Console\Commands;

use App\Services\PythonSandboxService;
use App\Services\PythonWarmSandbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class PythonSandboxPool extends Command
{
    protected $signature = 'python-sandbox:pool
        {action=maintain : maintain, status, clear or doctor}
        {--after=0 : Seconds to wait before maintaining (used by the background refill)}';

    protected $description = 'Warm Python sandbox: keep standby containers ready (maintain), list them (status), remove them (clear) or measure this machine (doctor)';

    public function handle(PythonWarmSandbox $warm, PythonSandboxService $sandbox): int
    {
        $action = (string) $this->argument('action');

        if ($action === 'doctor') {
            return $this->doctor($warm, $sandbox);
        }

        if (! $warm->enabled()) {
            $this->warn('The warm sandbox is off: it needs PYTHON_SANDBOX_DRIVER=docker, PYTHON_SANDBOX_WARM=true and a runner image rebuilt with "docker build -t datasensei-python-runner:latest docker/python-runner". Runs use the classic path.');

            return self::SUCCESS;
        }

        if ($action === 'clear') {
            $this->info('Removed '.$warm->clear().' container(s).');

            return self::SUCCESS;
        }

        if ($action === 'status') {
            $ready = array_flip($warm->readyNames());
            $rows = array_map(static fn (array $container): array => [
                $container['name'],
                $container['running'] ? 'running' : 'stopped',
                isset($ready[$container['name']]) ? 'ready' : '',
                (time() - $container['created']).' s',
            ], $warm->listContainers());

            $this->table(['Container', 'State', 'Pool', 'Age'], $rows);

            if ($reason = $warm->suspendedReason()) {
                $this->warn('Warm path suspended, runs use the classic runner. Reason: '.$reason);
            }

            return self::SUCCESS;
        }

        $after = max(0, min(60, (int) $this->option('after')));
        if ($after > 0) {
            sleep($after);
        }

        $summary = $warm->maintain($sandbox);
        $this->info(sprintf(
            'Standby containers: %d, ready: %d (created %d, removed %d).',
            $summary['warm'],
            $summary['ready'],
            $summary['created'],
            $summary['removed']
        ));

        return self::SUCCESS;
    }

    /** Measures, on this machine, what each way of running a program costs. */
    private function doctor(PythonWarmSandbox $warm, PythonSandboxService $sandbox): int
    {
        $docker = (string) config('code_execution.python.docker.binary', 'docker');
        $rows = [];

        $time = static function (callable $work): array {
            $start = microtime(true);
            $value = $work();

            return [(int) round((microtime(true) - $start) * 1000), $value];
        };

        [$ms, $ok] = $time(static fn () => Process::timeout(30)->run([$docker, 'version', '--format', '{{.Server.Version}}'])->successful());
        $rows[] = ['One Docker CLI call (docker version)', $ms.' ms', $ok ? 'ok' : 'FAILED: is Docker running?'];

        $program = "name = input('Name: ')\nprint('hi', name)\n";
        $run = function (bool $useWarm, string $stdin, array $session) use ($sandbox, $program): array {
            config(['code_execution.python.warm.enabled' => $useWarm]);
            $workspace = storage_path('app/python_sandbox/doctor_'.bin2hex(random_bytes(5)));
            File::ensureDirectoryExists($workspace);

            try {
                return $sandbox->runWorkspace($workspace, 'main.py', $program, $stdin, [
                    'interactive_input' => true,
                    'session' => $session,
                ]);
            } finally {
                File::deleteDirectory($workspace);
            }
        };

        $describe = static fn (array $r): string => ($r['timed_out'] ?? false)
            ? 'TIMED OUT'
            : (($r['input_required'] ?? false) ? 'asked for input' : 'exit '.($r['exit_code'] ?? '?').' '.json_encode($r['stdout'] ?? ''));

        $warmWanted = (bool) config('code_execution.python.warm.enabled', true);

        $session = ['key' => 'doctor', 'token' => 'doctor-'.bin2hex(random_bytes(6)), 'context' => 'doctor'];
        [$ms, $r] = $time(fn () => $run(false, '', $session));
        $rows[] = ['Classic runner: run until input()', $ms.' ms', $describe($r)];
        [$ms, $r] = $time(fn () => $run(false, "Louis\n", $session));
        $rows[] = ['Classic runner: answer (replays the program)', $ms.' ms', $describe($r)];

        config(['code_execution.python.warm.enabled' => $warmWanted]);

        if (! $warm->enabled()) {
            $rows[] = ['Warm sandbox', '-', 'off (needs PYTHON_SANDBOX_WARM=true and a rebuilt runner image, protocol '.PythonWarmSandbox::RUNNER_PROTOCOL.')'];
        } else {
            [$ms, $summary] = $time(fn () => $warm->maintain($sandbox));
            $rows[] = ['Fill the pool (background work, not part of a run)', $ms.' ms', $summary['ready'].' ready of '.$summary['warm']];

            $session['token'] = 'doctor-'.bin2hex(random_bytes(6));
            [$ms, $r] = $time(fn () => $run(true, '', $session));
            $rows[] = ['Warm sandbox: run until input()', $ms.' ms', $describe($r)];
            [$ms, $r] = $time(fn () => $run(true, "Louis\n", $session));
            $rows[] = ['Warm sandbox: answer (same process continues)', $ms.' ms', $describe($r)];

            if ($reason = $warm->suspendedReason()) {
                $rows[] = ['Warm sandbox suspended itself', '-', $reason];
            }
        }

        config(['code_execution.python.warm.enabled' => $warmWanted]);

        $this->table(['Step', 'Time', 'Result'], $rows);

        // A fraction of a core doubles every import and is the most common
        // reason a run "times out" on a laptop.
        $cpus = (float) config('code_execution.python.docker.cpus', 2.0);
        $this->line('CPU per program: '.config('code_execution.python.docker.cpus').' core(s), memory '.config('code_execution.python.docker.memory').'.');

        if ($cpus > 0 && $cpus < 1.0) {
            $this->warn('  PYTHON_SANDBOX_CPUS is '.$cpus.'. Under one core every import takes about twice as long.');
            $this->line('  Set PYTHON_SANDBOX_CPUS=2.0 and PYTHON_SANDBOX_MEMORY=1g in .env, then run "php artisan optimize:clear".');
        }

        $this->line("The learner's own code may run for ".config('code_execution.python.timeout_seconds').' s; the sandbox may take another '.$sandbox->startupGraceSeconds().' s to start before that clock begins.');

        // The one line that matters: is the fast path actually in use?
        $ready = count($warm->readyNames());

        if (! $warm->enabled()) {
            $this->newLine();
            $this->error('VERDICT: every run uses the CLASSIC path, which pays "docker run" and re-imports pandas each time.');
            $this->line('  Fix: set PYTHON_SANDBOX_WARM=true and rebuild the image:');
            $this->line('       docker build -t datasensei-python-runner:latest docker/python-runner');
        } elseif ($reason = $warm->suspendedReason()) {
            $this->newLine();
            $this->error('VERDICT: the warm pool switched itself off, so every run uses the SLOW classic path.');
            $this->line('  Reason: '.$reason);
            $this->line('  Fix: rebuild the image, then run "php artisan python-sandbox:pool clear" and "... maintain".');
        } elseif ($ready === 0) {
            $this->newLine();
            $this->error('VERDICT: no container is ready, so every run uses the SLOW classic path.');
            $this->line('  Fix: run "php artisan python-sandbox:pool maintain" and check Docker has free memory.');
        } else {
            $this->newLine();
            $this->info('VERDICT: the fast warm path is in use ('.$ready.' container(s) ready).');
        }

        if ($skipped = $warm->skippedPreloads()) {
            $this->warn('  The runner image is missing these preloaded libraries: '.$skipped);
            $this->line('  Runs still work and stay fast, but a lesson that imports them will fail.');
            $this->line('  Rebuild with: docker build -t datasensei-python-runner:latest docker/python-runner');
        }

        return self::SUCCESS;
    }
}
