<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ServiceContainerResolutionTest extends TestCase
{
    #[DataProvider('serviceProvider')]
    public function test_every_application_service_can_be_resolved_by_the_container(string $className): void
    {
        $service = $this->app->make($className);

        $this->assertInstanceOf($className, $service);
    }

    /** @return iterable<string, array{class-string}> */
    public static function serviceProvider(): iterable
    {
        $appRoot = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app';
        $serviceRoot = $appRoot.DIRECTORY_SEPARATOR.'Services';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($serviceRoot, \FilesystemIterator::SKIP_DOTS)
        );
        $classes = [];

        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($appRoot) + 1, -4);
            $classes[] = 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        }

        sort($classes);

        foreach ($classes as $className) {
            yield $className => [$className];
        }
    }
}
