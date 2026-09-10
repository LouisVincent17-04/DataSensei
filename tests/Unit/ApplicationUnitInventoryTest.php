<?php

namespace Tests\Unit;

use FilesystemIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

class ApplicationUnitInventoryTest extends TestCase
{
    #[DataProvider('applicationUnitProvider')]
    public function test_every_application_unit_is_autoloadable_from_its_expected_file(
        string $className,
        string $relativePath
    ): void {
        $this->assertTrue(
            class_exists($className),
            "{$relativePath} must declare the autoloadable class {$className}."
        );

        $reflection = new ReflectionClass($className);
        $declaredFile = $reflection->getFileName();

        $this->assertIsString($declaredFile);
        $this->assertSame(
            realpath(self::applicationRoot().DIRECTORY_SEPARATOR.$relativePath),
            realpath($declaredFile),
            "{$className} must be declared in the file that matches its PSR-4 name."
        );
        $this->assertFalse($reflection->isAnonymous());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function applicationUnitProvider(): iterable
    {
        foreach (self::applicationFiles() as $relativePath) {
            $className = 'App\\'.str_replace(
                DIRECTORY_SEPARATOR,
                '\\',
                substr($relativePath, 0, -4)
            );

            yield str_replace(DIRECTORY_SEPARATOR, '/', $relativePath) => [
                $className,
                $relativePath,
            ];
        }
    }

    /** @return array<int, string> */
    private static function applicationFiles(): array
    {
        $root = self::applicationRoot();
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
        );
        $files = [];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $files[] = substr($file->getPathname(), strlen($root) + 1);
        }

        sort($files);

        return $files;
    }

    private static function applicationRoot(): string
    {
        return dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app';
    }
}
