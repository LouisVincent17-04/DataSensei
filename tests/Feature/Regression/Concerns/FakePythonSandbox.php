<?php

namespace Tests\Feature\Regression\Concerns;

use App\Services\PythonCodePolicyService;
use App\Services\PythonSandboxService;
use Closure;

/**
 * Test-only seam for the coding-challenge grader: replaces real Python
 * execution with a scripted callback. Bound into the container in place of
 * PythonSandboxService; production code is unchanged.
 */
class FakePythonSandbox extends PythonSandboxService
{
    /** @var array<int, array{code:string, stdin:string}> */
    public array $calls = [];

    public function __construct(private readonly Closure $handler)
    {
        parent::__construct(new PythonCodePolicyService());
    }

    public function runInline(string $code, string $stdin = '', array $options = []): array
    {
        $this->calls[] = ['code' => $code, 'stdin' => $stdin];

        return array_merge([
            'stdout' => '',
            'stderr' => '',
            'exit_code' => 0,
            'failed' => false,
            'timed_out' => false,
            'execution_time_ms' => 1,
            'plots' => [],
        ], ($this->handler)($code, $stdin));
    }

    public static function ok(string $stdout): array
    {
        return ['stdout' => $stdout];
    }

    public static function crash(string $stdout, string $stderr, int $exitCode = 1): array
    {
        return ['stdout' => $stdout, 'stderr' => $stderr, 'exit_code' => $exitCode, 'failed' => true];
    }

    public static function timeout(string $stdout = ''): array
    {
        return ['stdout' => $stdout, 'stderr' => 'Execution stopped after 10 seconds.', 'exit_code' => 124, 'failed' => true, 'timed_out' => true];
    }
}
