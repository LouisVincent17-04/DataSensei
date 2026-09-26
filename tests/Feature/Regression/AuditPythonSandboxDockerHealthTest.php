<?php

namespace Tests\Feature\Regression;

use App\Services\PythonSandboxService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * A wedged Docker Desktop must not hold a Run for the whole budget.
 *
 * Before this guard, "docker run" inherited the full timeout plus the
 * start-up grace, so one broken engine froze a Run for about forty seconds.
 * php artisan serve is single-threaded, so that froze every other page in
 * the site with it. The service now probes the engine briefly first and
 * answers straight away when it is dead.
 */
class AuditPythonSandboxDockerHealthTest extends TestCase
{
    private string $scratch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->scratch = storage_path('framework/testing/docker-health-' . getmypid());
        File::ensureDirectoryExists($this->scratch);

        Cache::forget('datasensei:python-sandbox:docker-healthy');
        config()->set('code_execution.python.driver', 'docker');
        config()->set('code_execution.python.timeout_seconds', 20);
        config()->set('code_execution.python.startup_grace_seconds', 20);
        config()->set('code_execution.python.docker.health_timeout_seconds', 3);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->scratch);
        Cache::forget('datasensei:python-sandbox:docker-healthy');

        parent::tearDown();
    }

    public function test_a_hung_engine_is_reported_instead_of_blocking_the_whole_run_budget(): void
    {
        if (! $this->canRunShellStubs()) {
            $this->markTestSkipped('This check needs a POSIX shell to stand in for the docker binary.');
        }

        config()->set('code_execution.python.docker.binary', $this->stubBinary("exec sleep 600\n"));

        $service = app(PythonSandboxService::class);

        $started = microtime(true);
        $healthy = $service->dockerResponding();
        $probeSeconds = microtime(true) - $started;

        $this->assertFalse($healthy, 'A docker binary that never returns must not be reported as healthy.');
        $this->assertLessThan(
            10.0,
            $probeSeconds,
            'The health probe must give up on its own short timeout, not the run timeout.'
        );

        $workspace = $this->scratch . '/ws';
        File::ensureDirectoryExists($workspace);
        File::put($workspace . '/main.py', "print('hello')\n");

        $started = microtime(true);
        $result = $service->runWorkspace($workspace, 'main.py', "print('hello')", '', []);
        $runSeconds = microtime(true) - $started;

        $this->assertLessThan(
            5.0,
            $runSeconds,
            'With the probe result cached the run must fail fast; it used to block for the timeout plus the start-up grace.'
        );
        $this->assertStringContainsString('Docker is not responding', $result['stderr']);
        $this->assertStringContainsString(
            'Your program was not the problem',
            $result['stderr'],
            'The learner must be told plainly that their code is not at fault.'
        );
    }

    public function test_a_responding_engine_is_reported_healthy_and_the_answer_is_cached(): void
    {
        if (! $this->canRunShellStubs()) {
            $this->markTestSkipped('This check needs a POSIX shell to stand in for the docker binary.');
        }

        $marker = $this->scratch . '/calls';
        $binary = $this->stubBinary("echo call >> '{$marker}'\necho 24.0.0\n");
        config()->set('code_execution.python.docker.binary', $binary);

        $service = app(PythonSandboxService::class);

        $this->assertTrue($service->dockerResponding());
        $this->assertTrue($service->dockerResponding());

        $this->assertSame(
            1,
            count(file($marker, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)),
            'The probe result must be cached so a class pressing Run together pays for it once.'
        );
    }

    private function stubBinary(string $body): string
    {
        $path = $this->scratch . '/docker-stub-' . bin2hex(random_bytes(4));
        File::put($path, "#!/bin/sh\n" . $body);
        chmod($path, 0o755);

        return $path;
    }

    private function canRunShellStubs(): bool
    {
        return DIRECTORY_SEPARATOR === '/' && is_executable('/bin/sh');
    }
}
