<?php

namespace Tests\Feature\Sandbox;

use App\Services\PythonSandboxService;
use App\Services\PythonWarmSandbox;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Drives the warm sandbox against a real Docker daemon and a runner image
 * built from docker/python-runner. Skipped unless DS_DOCKER_TEST=1, so the
 * default suite needs no Docker:
 *
 *   DS_DOCKER_TEST=1 php vendor/bin/phpunit --filter PythonWarmSandboxDockerTest
 */
class PythonWarmSandboxDockerTest extends TestCase
{
    private PythonSandboxService $sandbox;

    private PythonWarmSandbox $warm;

    /** @var list<string> */
    private array $workspaces = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (getenv('DS_DOCKER_TEST') !== '1') {
            $this->markTestSkipped('Set DS_DOCKER_TEST=1 to run the Docker sandbox tests.');
        }

        config([
            'cache.default' => 'file',
            'code_execution.python.driver' => 'docker',
            'code_execution.python.warm.enabled' => true,
            'code_execution.python.warm.pool_size' => 2,
            'code_execution.python.timeout_seconds' => 4,
        ]);

        $this->warm = app(PythonWarmSandbox::class);
        $this->sandbox = app(PythonSandboxService::class);

        if (! $this->warm->enabled()) {
            $this->markTestSkipped('The runner image does not carry datasensei.runner.protocol=4. Rebuild it first.');
        }

        $this->warm->clear();
    }

    protected function tearDown(): void
    {
        foreach ($this->workspaces as $workspace) {
            is_dir($workspace) ? File::deleteDirectory($workspace) : File::delete($workspace);
        }

        if (isset($this->warm)) {
            $this->warm->clear();
        }

        parent::tearDown();
    }

    public function test_input_answers_continue_the_same_process_instead_of_replaying_it(): void
    {
        // The random number is drawn once. A replay would draw a new one for
        // every answer, so an identical echo proves the process kept running.
        $code = "import random\nsecret = random.randint(100000, 999999)\nprint('secret', secret)\n"
            ."name = input('Name: ')\nprint('again', secret)\nage = int(input('Age: '))\nprint(name, age + 1, secret)\n";
        $session = $this->sessionFor($code);

        $first = $this->execute($code, '', $session);
        $this->assertTrue($first['input_required']);
        $this->assertSame('Name: ', $first['input_prompt']);
        $this->assertSame(0, $first['inputs_consumed']);
        $this->assertMatchesRegularExpression('/^secret (\d{6})\nName: $/', $first['stdout']);
        preg_match('/secret (\d{6})/', $first['stdout'], $match);

        $second = $this->execute($code, "Louis\n", $session);
        $this->assertTrue($second['input_required']);
        $this->assertSame('Age: ', $second['input_prompt']);
        $this->assertSame(1, $second['inputs_consumed']);
        $this->assertStringStartsWith($first['stdout'], $second['stdout']);
        $this->assertStringContainsString('again '.$match[1], $second['stdout']);

        $third = $this->execute($code, "Louis\n20\n", $session);
        $this->assertFalse($third['input_required']);
        $this->assertSame(0, $third['exit_code']);
        $this->assertStringStartsWith($second['stdout'], $third['stdout']);
        $this->assertStringEndsWith('Louis 21 '.$match[1], $third['stdout']);
        $this->assertSame('', $third['stderr']);
    }

    public function test_a_standby_container_that_is_still_preloading_is_never_handed_a_run(): void
    {
        // A slow machine: importing the preloaded libraries takes far longer
        // than the run timeout. The pool was created a moment ago, so every
        // standby container is still importing. The run used to be handed to
        // one of them, waited, and was reported as "Execution stopped after
        // N seconds" although the program was only waiting in input().
        config(['code_execution.python.docker.cpus' => '0.08']);
        exec('docker run -d --label datasensei.sandbox=warm --label datasensei.protocol='.PythonWarmSandbox::RUNNER_PROTOCOL
            .' --cpus 0.08 --network none -e DS_PRELOAD=numpy,pandas,matplotlib.pyplot --name dswarm-'.time().'-stillloading'
            .' datasensei-python-runner:latest --standby 2>&1');
        $this->assertSame([], $this->warm->readyNames());

        $code = "name = input('Name: ')\nprint('hi', name)\n";
        $session = $this->sessionFor($code);
        $started = microtime(true);
        $first = $this->execute($code, '', $session);

        $this->assertFalse($first['timed_out'], $first['stderr']);
        $this->assertTrue($first['input_required']);
        $this->assertSame('Name: ', $first['stdout']);
        $this->assertLessThan(8.0, microtime(true) - $started);

        $second = $this->execute($code, "Louis\n", $session);
        $this->assertFalse($second['timed_out'], $second['stderr']);
        $this->assertSame('Name: hi Louis', $second['stdout']);
        $this->assertSame(0, $second['exit_code']);
    }

    public function test_time_spent_waiting_for_the_learner_is_not_execution_time(): void
    {
        $code = "value = input('Value: ')\nprint('got', value)\n";
        $session = $this->sessionFor($code);
        $this->assertTrue($this->execute($code, '', $session)['input_required']);

        // Longer than the 4 second run timeout configured for these tests.
        sleep(6);

        $done = $this->execute($code, "7\n", $session);
        $this->assertFalse($done['timed_out'], $done['stderr']);
        $this->assertSame('Value: got 7', $done['stdout']);
    }

    public function test_a_new_run_token_abandons_the_waiting_program(): void
    {
        $code = "value = input('Value: ')\nprint('got', value)\n";
        $first = $this->execute($code, '', $this->sessionFor($code, 'token-aaaaaaaa'));
        $this->assertTrue($first['input_required']);
        $waiting = $this->runContainers();
        $this->assertCount(1, $waiting);

        // Same learner presses Run again: different token, and the answer
        // arrives with it. The program starts fresh and the old one is removed.
        $second = $this->execute($code, "7\n", $this->sessionFor($code, 'token-bbbbbbbb'));
        $this->assertFalse($second['input_required']);
        $this->assertSame("Value: got 7", $second['stdout']);

        usleep(1500000);
        $this->assertSame([], array_intersect($waiting, $this->runContainers()));
    }

    public function test_an_expired_container_falls_back_to_a_full_replay(): void
    {
        $code = "a = input('A: ')\nb = input('B: ')\nprint(a + b)\n";
        $session = $this->sessionFor($code);
        $this->assertTrue($this->execute($code, '', $session)['input_required']);

        foreach ($this->runContainers() as $container) {
            exec('docker rm -f '.escapeshellarg($container).' 2>&1');
        }

        $next = $this->execute($code, "x\n", $session);
        $this->assertTrue($next['input_required']);
        $this->assertSame('B: ', $next['input_prompt']);
        $this->assertSame('A: B: ', $next['stdout']);

        $done = $this->execute($code, "x\ny\n", $session);
        $this->assertSame('A: B: xy', $done['stdout']);
    }

    public function test_errors_timeouts_and_exit_codes_match_the_classic_runner(): void
    {
        $error = $this->sandbox->runInline("print('before')\nraise ValueError('boom')\n");
        $this->assertSame(1, $error['exit_code']);
        $this->assertTrue($error['failed']);
        $this->assertSame('before', $error['stdout']);
        $this->assertStringContainsString('ValueError: boom', $error['stderr']);
        $this->assertStringNotContainsString('__DATASENSEI_', $error['stderr']);

        $exit = $this->sandbox->runInline("import sys\nprint('bye')\nsys.exit(3)\n");
        $this->assertSame(3, $exit['exit_code']);

        $loop = $this->sandbox->runInline("import time\nprint('start', flush=True)\nwhile True:\n    time.sleep(0.05)\n");
        $this->assertTrue($loop['timed_out']);
        $this->assertSame(124, $loop['exit_code']);
        $this->assertStringContainsString('Execution stopped after 4 seconds', $loop['stderr']);

        $blocked = $this->sandbox->runInline("import subprocess\n");
        $this->assertSame(126, $blocked['exit_code']);
    }

    public function test_inline_runs_receive_stdin_and_reach_eof_without_prompting(): void
    {
        $result = $this->sandbox->runInline("print(int(input()) * 2)\nprint(input())\n", "21\n");
        $this->assertSame('42', $result['stdout']);
        $this->assertStringContainsString('EOFError', $result['stderr']);
        $this->assertFalse($result['input_required']);
    }

    public function test_plots_and_workspace_files_work_in_a_warm_container(): void
    {
        $code = "import matplotlib.pyplot as plt\nfrom helper import twice\nplt.plot([1, twice(2), 3])\nplt.show()\nprint(open('data/n.csv').read().strip())\n";
        $result = $this->execute($code, '', null, ['helper.py' => "def twice(v):\n    return v * 2\n", 'data/n.csv' => "a,b\n"]);

        $this->assertSame(0, $result['exit_code'], $result['stderr']);
        $this->assertSame('a,b', $result['stdout']);
        $this->assertCount(1, $result['plots']);
    }

    public function test_warm_containers_carry_the_same_restrictions_as_a_classic_run(): void
    {
        $this->warm->maintain($this->sandbox);
        $warm = array_values(array_filter($this->warm->listContainers(), static fn ($c) => str_starts_with($c['name'], 'dswarm-')));
        $this->assertGreaterThanOrEqual(2, count($warm));

        $inspect = json_decode((string) shell_exec('docker inspect '.escapeshellarg($warm[0]['name'])), true)[0];
        $host = $inspect['HostConfig'];

        $this->assertSame('none', $host['NetworkMode']);
        $this->assertTrue($host['ReadonlyRootfs']);
        $this->assertSame(['ALL'], $host['CapDrop']);
        $this->assertContains('no-new-privileges', $host['SecurityOpt']);
        $this->assertSame(512 * 1024 * 1024, $host['Memory']);
        $this->assertSame(64, $host['PidsLimit']);
        $this->assertSame('1000:1000', $inspect['Config']['User']);
        // No host directory is visible inside a warm container.
        $this->assertSame([], $inspect['Mounts'] === null ? [] : array_values(array_filter(
            $inspect['Mounts'],
            static fn (array $mount): bool => $mount['Type'] === 'bind'
        )));
    }

    public function test_a_run_costs_exactly_one_docker_call_when_the_pool_is_ready(): void
    {
        $this->warm->maintain($this->sandbox);
        $this->assertNotSame([], $this->warm->readyNames());

        $log = storage_path('app/python_sandbox/phpunit_docker_calls.log');
        $wrapper = storage_path('app/python_sandbox/phpunit_docker_wrapper.sh');
        File::put($wrapper, "#!/bin/sh\necho \"\$1\" >> ".escapeshellarg($log)."\nexec docker \"\$@\"\n");
        chmod($wrapper, 0755);
        $this->workspaces[] = $wrapper;
        $this->workspaces[] = $log;
        config(['code_execution.python.docker.binary' => $wrapper]);

        $code = "name = input('Name: ')\nprint('hi', name)\n";
        $session = $this->sessionFor($code);

        File::put($log, '');
        $this->assertTrue($this->execute($code, '', $session)['input_required']);
        $this->assertSame(['exec'], preg_split('/\R/', trim(File::get($log))));

        File::put($log, '');
        $this->assertSame('Name: hi Louis', $this->execute($code, "Louis\n", $session)['stdout']);
        $this->assertSame(['exec'], preg_split('/\R/', trim(File::get($log))));
    }

    public function test_a_vanished_ready_container_costs_one_call_and_the_run_still_succeeds(): void
    {
        \Illuminate\Support\Facades\Cache::put('datasensei:python-warm:ready-list', ['dswarm-'.time().'-doesnotexist'], 600);

        $result = $this->sandbox->runInline("print(6 * 7)\n");

        $this->assertSame('42', $result['stdout']);
        $this->assertSame(0, $result['exit_code']);
    }

    public function test_concurrent_claims_never_share_a_container(): void
    {
        $this->warm->maintain($this->sandbox);
        $script = base_path('tests/Feature/Sandbox/Support/warm_claim_worker.php');
        $handles = [];

        foreach (range(1, 4) as $index) {
            $handles[] = popen(escapeshellarg(PHP_BINARY).' '.escapeshellarg($script).' '.$index, 'r');
        }

        $outputs = array_map(static function ($handle): string {
            $output = stream_get_contents($handle);
            pclose($handle);

            return trim((string) $output);
        }, $handles);

        sort($outputs);
        $this->assertSame(['worker-1', 'worker-2', 'worker-3', 'worker-4'], $outputs);
    }

    private function sessionFor(string $code, string $token = 'token-12345678'): array
    {
        return ['key' => 'phpunit-user', 'token' => $token, 'context' => '1:'.sha1($code)];
    }

    private function execute(string $code, string $stdin, ?array $session, array $extraFiles = []): array
    {
        $workspace = storage_path('app/python_sandbox/phpunit_'.bin2hex(random_bytes(6)));
        $this->workspaces[] = $workspace;
        File::ensureDirectoryExists($workspace);

        foreach ($extraFiles as $path => $content) {
            File::ensureDirectoryExists(dirname($workspace.'/'.$path));
            File::put($workspace.'/'.$path, $content);
        }

        return $this->sandbox->runWorkspace($workspace, 'main.py', $code, $stdin, [
            'interactive_input' => $session !== null,
            'session' => $session,
        ]);
    }

    /** Running containers that hold a learner's program (claimed, so no longer in the ready list). @return list<string> */
    private function runContainers(): array
    {
        $ready = $this->warm->readyNames();

        return array_values(array_map(
            static fn (array $container): string => $container['name'],
            array_filter(
                $this->warm->listContainers(),
                static fn ($c) => $c['running'] && ! in_array($c['name'], $ready, true) && ! str_contains($c['name'], 'stillloading')
            )
        ));
    }
}
