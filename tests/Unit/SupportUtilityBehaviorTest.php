<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\PythonCodePolicyService;
use App\Support\AuthSessionFingerprint;
use App\Support\PasswordOtpConfiguration;
use DomainException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SupportUtilityBehaviorTest extends TestCase
{
    public function test_authentication_fingerprint_is_stable_and_covers_every_security_field(): void
    {
        config()->set('app.key', 'base64:test-application-key');

        $base = $this->fingerprintUser();
        $fingerprint = AuthSessionFingerprint::for($base);

        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $fingerprint);
        $this->assertSame($fingerprint, AuthSessionFingerprint::for($this->fingerprintUser()));
        $this->assertSame(
            $fingerprint,
            AuthSessionFingerprint::for($this->fingerprintUser(['email' => 'STUDENT@EXAMPLE.TEST']))
        );

        foreach ([
            ['password' => 'different-password-hash'],
            ['remember_token' => 'different-remember-token'],
            ['email' => 'another.student@example.test'],
            ['role' => User::ROLE_INSTRUCTOR],
            ['status' => 'disabled'],
            ['institution_id' => 99],
        ] as $change) {
            $this->assertNotSame(
                $fingerprint,
                AuthSessionFingerprint::for($this->fingerprintUser($change)),
                'Changing a security-sensitive user field must invalidate the stored session fingerprint.'
            );
        }
    }

    #[DataProvider('validOtpLengthProvider')]
    public function test_password_otp_configuration_accepts_supported_lengths(int|string $configured, int $expected): void
    {
        config()->set('password_otp.length', $configured);

        $this->assertSame($expected, PasswordOtpConfiguration::length());
    }

    #[DataProvider('invalidOtpLengthProvider')]
    public function test_password_otp_configuration_rejects_unsupported_values(mixed $configured): void
    {
        config()->set('password_otp.length', $configured);

        $this->expectException(InvalidArgumentException::class);
        PasswordOtpConfiguration::length();
    }

    #[DataProvider('allowedPythonProvider')]
    public function test_python_policy_allows_normal_learning_code(string $code): void
    {
        $policy = new PythonCodePolicyService();

        $this->assertSame([], $policy->violations($code));
        $policy->assertAllowed($code);
        $this->addToAssertionCount(1);
    }

    #[DataProvider('blockedPythonProvider')]
    public function test_python_policy_rejects_dangerous_or_malformed_code(string $code): void
    {
        $policy = new PythonCodePolicyService();

        $this->assertNotSame([], $policy->violations($code));
        $this->expectException(DomainException::class);
        $policy->assertAllowed($code);
    }

    /** @return iterable<string, array{int|string, int}> */
    public static function validOtpLengthProvider(): iterable
    {
        yield 'minimum' => [PasswordOtpConfiguration::MIN_LENGTH, PasswordOtpConfiguration::MIN_LENGTH];
        yield 'default' => [6, 6];
        yield 'numeric string maximum' => [(string) PasswordOtpConfiguration::MAX_LENGTH, PasswordOtpConfiguration::MAX_LENGTH];
    }

    /** @return iterable<string, array{mixed}> */
    public static function invalidOtpLengthProvider(): iterable
    {
        yield 'below minimum' => [PasswordOtpConfiguration::MIN_LENGTH - 1];
        yield 'above maximum' => [PasswordOtpConfiguration::MAX_LENGTH + 1];
        yield 'word' => ['six'];
        yield 'decimal' => [6.5];
        yield 'null' => [null];
    }

    /** @return iterable<string, array{string}> */
    public static function allowedPythonProvider(): iterable
    {
        yield 'basic arithmetic' => ['numbers = [1, 2, 3]' . "\n" . 'print(sum(numbers))'];
        yield 'safe standard library' => ['import math' . "\n" . 'print(math.sqrt(81))'];
        yield 'function definition' => ['def double(value):' . "\n" . '    return value * 2'];
        // os itself is lesson material (os.path, os.listdir). Only the calls
        // that leave the sandbox are blocked, one by one, below.
        yield 'operating system paths' => ['import os' . "\n" . 'print(os.getcwd())'];
    }

    /** @return iterable<string, array{string}> */
    public static function blockedPythonProvider(): iterable
    {
        yield 'process spawning import' => ['import subprocess' . "\n" . 'subprocess.run(["ls"])'];
        yield 'network import' => ['from socket import socket'];
        yield 'dynamic evaluation' => ['result = eval("2 + 2")'];
        yield 'network client call' => ['requests.get("https://example.test")'];
        yield 'environment access' => ['value = os.getenv("APP_KEY")'];
        yield 'unsafe introspection' => ['classes = object.__subclasses__()'];
        yield 'null byte' => ["print('ok')\0"];
        yield 'invalid utf8' => ["\xC3\x28"];
    }

    /** @param array<string, mixed> $overrides */
    private function fingerprintUser(array $overrides = []): User
    {
        $user = new User();
        $user->setRawAttributes(array_merge([
            'password' => 'stored-password-hash',
            'remember_token' => 'stored-remember-token',
            'email' => 'student@example.test',
            'role' => User::ROLE_USER,
            'status' => 'active',
            'institution_id' => 7,
        ], $overrides));

        return $user;
    }
}
