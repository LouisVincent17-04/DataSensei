<?php

namespace App\Services;

final class PythonCodePolicyService
{
    /**
     * Defence-in-depth policy for code submitted to the educational runner.
     * Container isolation remains the primary security boundary: the sandbox has
     * no network, a read-only root filesystem, dropped capabilities, a non-root
     * user, and memory, CPU, process and output limits.
     *
     * Because that boundary does the real work, this layer blocks only what has
     * no place in a lesson — process spawning, raw sockets, interpreter
     * internals — and deliberately allows the standard library a Python course
     * actually teaches: os.path, pathlib, file handling, csv, json, pickle,
     * datetime, collections, itertools, typing, dataclasses, asyncio, threading,
     * unittest, and the installed scientific stack.
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
            /*
             * Modules whose entire purpose is leaving the sandbox. "os" is NOT
             * here: os.path, os.listdir and os.makedirs are ordinary lesson
             * material, and the dangerous os calls are blocked by name below.
             */
            '/^\s*(?:from|import)\s+(?:subprocess|socket|ssl|ctypes|cffi|multiprocessing|pty|fcntl|termios|winreg|msvcrt|importlib|webbrowser|http|urllib|requests|httpx|ftplib|smtplib|poplib|imaplib|telnetlib|xmlrpc|venv|ensurepip|pip|setuptools|distutils)\b/im'
                => 'Imports for process control, networking, dynamic loading, or package installation are blocked in the learning sandbox.',

            '/\b(?:os\s*\.\s*(?:system|popen|spawn\w*|exec\w*|fork\w*|kill|killpg|abort|setuid|setgid|chroot|chown|chmod)|subprocess\s*\.)/i'
                => 'Operating-system process execution and ownership changes are blocked.',

            '/\b(?:socket\s*\.\s*socket|requests\s*\.\s*(?:get|post|put|delete|patch|head|request|Session)|urllib\s*\.\s*request|httpx\s*\.\s*(?:get|post|Client))/i'
                => 'Outbound network access is blocked; the sandbox runs with no network.',

            '/\b(?:sys\s*\.\s*(?:modules|path|settrace|setrecursionlimit|_getframe|exit_hook)|gc\s*\.\s*get_objects)\b/i'
                => 'Interpreter internals are not available to student code.',

            /*
             * The runner strips the server's variables before a program starts,
             * so nothing sensitive is there to read. Saying so plainly is still
             * better than letting a lesson build on an empty dictionary.
             */
            '/\b(?:os\s*\.\s*(?:environ|getenv|putenv|unsetenv)|posix\s*\.\s*environ)\b/i'
                => 'Environment variables are not available to student code.',

            '/\b__(?:subclasses|globals|builtins|loader|spec|code|mro|bases|reduce|reduce_ex)__\b/i'
                => 'Unsafe Python introspection is blocked.',

            '/\b__import__\s*\(/i'
                => 'Dynamic module loading through __import__ is blocked. Use a normal import statement.',
        ];

        if (! $this->allowsDynamicExecution()) {
            $checks['/\b(?:eval|exec|compile)\s*\(/i']
                = 'eval, exec and compile are disabled by default. An administrator can enable them with PYTHON_ALLOW_DYNAMIC_EXECUTION=true.';
        }

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

    private function allowsDynamicExecution(): bool
    {
        return (bool) config('code_execution.python.policy.allow_dynamic_execution', false);
    }

    private function isUtf8(string $value): bool
    {
        if (function_exists('mb_check_encoding')) {
            return mb_check_encoding($value, 'UTF-8');
        }

        return preg_match('//u', $value) === 1;
    }
}
