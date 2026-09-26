<?php

namespace Tests\Feature\Regression;

use App\Services\PythonSandboxService;
use Tests\TestCase;

/**
 * The runner environment is thinned to an allowlist so the web server's own
 * secrets never reach student code. It was thinned too far for Windows: the
 * local driver lost most of the system block, and Winsock then failed to
 * initialise with
 *
 *   OSError: [WinError 10106] The requested service provider could not be
 *   loaded or initialized
 *
 * asyncio touches Winsock on import, joblib imports asyncio and scikit-learn
 * imports joblib, so every scikit-learn lesson died on a networking error.
 *
 * The allowlist is what this test guards, in both directions: the system
 * block survives and the secrets do not.
 */
class AuditPythonSandboxEnvironmentTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $restore = [];

    protected function tearDown(): void
    {
        foreach ($this->restore as $name => $value) {
            if ($value === false) {
                putenv($name);
            } else {
                putenv($name.'='.$value);
            }
        }

        parent::tearDown();
    }

    private function fakeEnv(string $name, string $value): void
    {
        $this->restore[$name] = getenv($name);
        putenv($name.'='.$value);
    }

    /** @return array<string, mixed> */
    private function isolated(): array
    {
        $service = app(PythonSandboxService::class);
        $method = (new \ReflectionClass($service))->getMethod('isolatedEnvironment');

        return $method->invoke($service, ['DS_WORKSPACE' => '/tmp/ws']);
    }

    public function test_the_windows_system_block_survives_isolation(): void
    {
        $system = [
            'SystemRoot' => 'C:\\Windows',
            'SystemDrive' => 'C:',
            'windir' => 'C:\\Windows',
            'ALLUSERSPROFILE' => 'C:\\ProgramData',
            'COMPUTERNAME' => 'CAPSTONE-PC',
            'HOMEDRIVE' => 'C:',
            'HOMEPATH' => '\\Users\\louis',
            'PROCESSOR_IDENTIFIER' => 'Intel64 Family 6',
            'ProgramW6432' => 'C:\\Program Files',
        ];

        foreach ($system as $name => $value) {
            $this->fakeEnv($name, $value);
        }

        $environment = $this->isolated();

        foreach (array_keys($system) as $name) {
            $this->assertNotSame(
                false,
                $environment[$name] ?? null,
                $name.' was stripped; Winsock fails to initialise without the system block.'
            );
        }
    }

    public function test_application_secrets_are_still_removed(): void
    {
        $secrets = [
            'APP_KEY' => 'base64:not-a-real-key',
            'DB_PASSWORD' => 'not-a-real-password',
            'MAIL_PASSWORD' => 'not-a-real-mail-password',
            'AWS_SECRET_ACCESS_KEY' => 'not-a-real-aws-key',
            'OLLAMA_URL' => 'http://127.0.0.1:11434/api/generate',
        ];

        foreach ($secrets as $name => $value) {
            $this->fakeEnv($name, $value);
        }

        $environment = $this->isolated();

        foreach (array_keys($secrets) as $name) {
            $this->assertSame(
                false,
                $environment[$name] ?? null,
                $name.' must be removed before student code starts.'
            );
        }
    }

    /**
     * The container gets SEABORN_DATA from the image. The local driver has no
     * image, so it must point at the same CSVs in the project, or
     * sns.load_dataset() tries to download and the visualisation lessons fail.
     */
    public function test_the_local_driver_points_seaborn_at_the_bundled_datasets(): void
    {
        $datasets = base_path('docker/python-runner/seaborn-data');

        $this->assertDirectoryExists($datasets, 'The bundled datasets are missing from the project.');

        foreach (['iris', 'titanic', 'tips', 'penguins', 'flights'] as $name) {
            $this->assertFileExists($datasets.'/'.$name.'.csv');
        }

        $service = app(PythonSandboxService::class);
        $method = (new \ReflectionClass($service))->getMethod('runLocally');
        $source = implode('', array_slice(
            file($method->getFileName()),
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        $this->assertStringContainsString(
            "SEABORN_DATA",
            $source,
            'The local driver must pass SEABORN_DATA; the container gets it from the image.'
        );
    }

    /**
     * The local driver used to cap the process at the learner's limit plus two
     * seconds, with none of the start-up allowance the Docker path gets. A
     * first run that had to build matplotlib's font index was killed part-way,
     * so the index was never finished and every later run repeated it.
     */
    public function test_the_local_driver_allows_for_sandbox_start_up(): void
    {
        $service = app(PythonSandboxService::class);
        $reflection = new \ReflectionClass($service);

        $method = $reflection->getMethod('runLocally');
        $source = implode('', array_slice(
            file($method->getFileName()),
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        $this->assertStringContainsString(
            'startupGraceSeconds()',
            $source,
            'The local process timeout must include the start-up allowance, not just the learner limit.'
        );

        $grace = $reflection->getMethod('startupGraceSeconds');

        $this->assertGreaterThanOrEqual(
            4,
            (int) $grace->invoke($service),
            'A start-up allowance of nothing leaves no room to build a font index.'
        );
    }

    /**
     * A lesson printed "±" and the whole Run failed with "Malformed UTF-8
     * characters": on Windows the pipe was opened in cp1252, the character
     * left as the single byte 0xB1, and json_encode refused the response.
     */
    public function test_python_is_told_to_write_utf8(): void
    {
        $service = app(PythonSandboxService::class);
        $method = (new \ReflectionClass($service))->getMethod('runnerEnvironment');
        $environment = $method->invoke($service, '/tmp/ws', '/tmp/in', 10, false);

        $this->assertSame('utf-8', $environment['PYTHONIOENCODING'] ?? null);
        $this->assertSame('1', $environment['PYTHONUTF8'] ?? null);
    }

    public function test_output_that_is_not_utf8_is_scrubbed_rather_than_fatal(): void
    {
        $service = app(PythonSandboxService::class);
        $method = (new \ReflectionClass($service))->getMethod('utf8');

        $cp1252 = "Mean: 0.850 \xB1 0.050";
        $clean = $method->invoke($service, $cp1252);

        $this->assertTrue(mb_check_encoding($clean, 'UTF-8'));
        $this->assertStringStartsWith('Mean: 0.850 ', $clean);
        $this->assertStringEndsWith(' 0.050', $clean);
        $this->assertNotNull(json_encode(['stdout' => $clean]), 'The scrubbed text must encode.');

        // Already-valid text, including the real character, is untouched.
        $valid = 'Mean: 0.850 ± 0.050';
        $this->assertSame($valid, $method->invoke($service, $valid));
    }

    public function test_the_runner_settings_are_kept(): void
    {
        $environment = $this->isolated();

        $this->assertSame('/tmp/ws', $environment['DS_WORKSPACE']);
    }

    /**
     * The part that actually bit: a child process must really receive the
     * allowlisted variables.
     *
     * Symfony composes the child's default environment from getenv()
     * intersected with $_SERVER. Under "php artisan serve" $_SERVER carries
     * request variables, so the system block was excluded and never reached
     * the runner, however the allowlist was written. The intersection is by
     * key on Linux and case-insensitively by key on Windows, so the fault
     * reproduces here by shrinking $_SERVER the same way a request does.
     */
    public function test_allowlisted_variables_reach_the_child_process(): void
    {
        $this->fakeEnv('SYSTEMROOT', 'C:\\Windows');
        $this->fakeEnv('PROGRAMDATA', 'C:\\ProgramData');

        $originalServer = $_SERVER;

        // A request's $_SERVER: request variables plus the handful of
        // environment names a web SAPI does republish. PATH matters — the
        // intersection has to be non-empty, or Symfony falls back to the whole
        // environment and the defect hides.
        $_SERVER = [
            'REQUEST_URI' => '/ide/run',
            'REQUEST_METHOD' => 'POST',
            'SCRIPT_NAME' => '/index.php',
            'SERVER_NAME' => '127.0.0.1',
            'PATH' => getenv('PATH') ?: '/usr/bin:/bin',
        ];

        try {
            $environment = $this->isolated();

            $result = \Illuminate\Support\Facades\Process::env($environment)
                ->run([
                    PHP_BINARY,
                    '-r',
                    'echo getenv("SYSTEMROOT") ?: "MISSING", "|", getenv("PROGRAMDATA") ?: "MISSING", "|", getenv("APP_KEY") ?: "ABSENT";',
                ]);

            [$systemRoot, $programData, $appKey] = explode('|', trim($result->output()));

            $this->assertSame(
                'C:\\Windows',
                $systemRoot,
                'SystemRoot never reached the runner: Winsock cannot initialise without it.'
            );
            $this->assertSame('C:\\ProgramData', $programData);
            $this->assertSame('ABSENT', $appKey, 'The application key must not reach student code.');
        } finally {
            $_SERVER = $originalServer;
        }
    }

    public function test_the_allowlist_names_are_upper_case_so_matching_works(): void
    {
        // isolatedEnvironment() upper-cases each inherited name before the
        // comparison. A lower-case entry here would silently never match, and
        // Windows reports its variables in mixed case ("SystemRoot").
        $allowlist = (new \ReflectionClass(PythonSandboxService::class))
            ->getConstant('RUNNER_ENV_ALLOWLIST');

        foreach ($allowlist as $name) {
            $this->assertSame(strtoupper($name), $name, $name.' must be upper case.');
        }
    }
}
