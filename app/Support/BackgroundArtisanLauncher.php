<?php

namespace App\Support;

use Symfony\Component\Process\PhpExecutableFinder;
use Throwable;

/**
 * Starts an Artisan command as a detached process and returns immediately.
 *
 * Used where a web request must not wait: the built-in "php artisan serve"
 * server handles one request at a time, so a long synchronous call would
 * freeze every other page for the same student.
 */
class BackgroundArtisanLauncher
{
    /** @param array<int, string> $arguments Artisan command name followed by its arguments. */
    public function launch(array $arguments): bool
    {
        if (! (bool) config('background.enabled', true) || $arguments === []) {
            return false;
        }

        $php = $this->phpBinary();
        if ($php === null) {
            return false;
        }

        $parts = array_map(
            static fn (string $part): string => escapeshellarg($part),
            array_merge([$php, base_path('artisan')], array_map('strval', $arguments))
        );

        try {
            if (PHP_OS_FAMILY === 'Windows') {
                if (! function_exists('popen') || ! function_exists('pclose')) {
                    return false;
                }

                // "start /B" returns at once; the redirection gives the child NUL
                // handles so pclose() never waits for the child to finish.
                $handle = @popen('start "" /B '.implode(' ', $parts).' < NUL > NUL 2>&1', 'r');
                if ($handle === false) {
                    return false;
                }
                pclose($handle);

                return true;
            }

            if (! function_exists('exec')) {
                return false;
            }

            $output = [];
            $exitCode = 1;
            @exec('nohup '.implode(' ', $parts).' < /dev/null > /dev/null 2>&1 &', $output, $exitCode);

            return $exitCode === 0;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    public function phpBinary(): ?string
    {
        $configured = trim((string) config('background.php_binary', ''));
        if ($configured !== '') {
            return is_file($configured) ? $configured : null;
        }

        $found = (new PhpExecutableFinder())->find(false);
        if (! is_string($found) || $found === '') {
            return null;
        }

        // Under Apache, PHP_BINARY can point at the web server itself. Only a PHP
        // executable can run Artisan.
        $name = strtolower(pathinfo($found, PATHINFO_FILENAME));
        if (! str_starts_with($name, 'php')) {
            return null;
        }

        return $found;
    }
}
