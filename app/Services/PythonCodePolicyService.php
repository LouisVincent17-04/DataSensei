<?php

namespace App\Services;

final class PythonCodePolicyService
{
    /**
     * Defence-in-depth policy for code submitted to the educational runner.
     * Container isolation remains the primary security boundary.
     *
     * @return array<int, string>
     */
    public function violations(string $code): array
    {
        $violations = [];

        if (str_contains($code, "\0")) {
            $violations[] = 'Null bytes are not allowed in Python source code.';
        }

        if (! $this->isUtf8($code)) {
            $violations[] = 'Python source code must be valid UTF-8 text.';
        }

        $checks = [
            '/^\s*(?:from|import)\s+(?:os|subprocess|socket|ctypes|multiprocessing|resource|signal|pty|fcntl|winreg|importlib|builtins|pickle|marshal|shelve|webbrowser|http|urllib|ftplib|telnetlib|venv|ensurepip)\b/im'
                => 'Imports that can access the operating system, processes, network, dynamic loaders, or unsafe serialization are blocked.',
            '/\b(?:__import__|eval|exec|compile)\s*\(/i'
                => 'Dynamic code execution functions are blocked in the Python IDE.',
            '/\b(?:os\s*\.\s*(?:system|popen|spawn\w*|exec\w*|fork|kill)|subprocess\s*\.)/i'
                => 'Operating-system process execution is blocked.',
            '/\b(?:socket\s*\.|requests\s*\.|urllib\s*\.|httpx\s*\.)/i'
                => 'Outbound network access is blocked.',
            '/\b(?:pip|pip3|ensurepip)\b/i'
                => 'Package installation is disabled inside the sandbox.',
            '/\b(?:sys\s*\.\s*modules|sys\s*\.\s*path|os\s*\.\s*environ|os\s*\.\s*getenv)\b/i'
                => 'Runtime internals and environment variables are not available to student code.',
            '/\b__(?:subclasses|globals|builtins|loader|spec|code|mro|bases)__\b/i'
                => 'Unsafe Python introspection is blocked.',
        ];

        foreach ($checks as $pattern => $message) {
            if (preg_match($pattern, $code) === 1) {
                $violations[] = $message;
            }
        }

        return array_values(array_unique($violations));
    }

    public function assertAllowed(string $code): void
    {
        $violations = $this->violations($code);

        if ($violations !== []) {
            throw new \DomainException(implode(' ', $violations));
        }
    }

    private function isUtf8(string $value): bool
    {
        if (function_exists('mb_check_encoding')) {
            return mb_check_encoding($value, 'UTF-8');
        }

        return preg_match('//u', $value) === 1;
    }
}
