<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Mail\Mailable;
use Illuminate\Support\ServiceProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class ApplicationLayerContractTest extends TestCase
{
    #[DataProvider('layerUnitProvider')]
    public function test_each_application_layer_uses_its_required_framework_contract(
        string $className,
        string $expectedContract,
        ?string $requiredMethod = null
    ): void {
        $this->assertTrue(class_exists($className), "{$className} must be autoloadable.");
        $this->assertTrue(
            is_a($className, $expectedContract, true),
            "{$className} must extend or implement {$expectedContract}."
        );

        if ($requiredMethod === null) {
            return;
        }

        $this->assertTrue(method_exists($className, $requiredMethod));
        $this->assertTrue(
            (new ReflectionMethod($className, $requiredMethod))->isPublic(),
            "{$className}::{$requiredMethod}() must be public."
        );
    }

    /**
     * @return iterable<string, array{string, string, string|null}>
     */
    public static function layerUnitProvider(): iterable
    {
        foreach (self::classesIn('Models') as $className) {
            yield "model:{$className}" => [$className, Model::class, null];
        }

        foreach (self::classesIn('Http/Controllers') as $className) {
            if ($className === Controller::class) {
                continue;
            }

            yield "controller:{$className}" => [$className, Controller::class, null];
        }

        foreach (self::classesIn('Http/Middleware') as $className) {
            yield "middleware:{$className}" => [$className, $className, 'handle'];
        }

        foreach (self::classesIn('Http/Requests') as $className) {
            yield "request:{$className}" => [$className, FormRequest::class, 'rules'];
        }

        foreach (self::classesIn('Console/Commands') as $className) {
            yield "command:{$className}" => [$className, Command::class, 'handle'];
        }

        foreach (self::classesIn('Jobs') as $className) {
            yield "job:{$className}" => [$className, ShouldQueue::class, 'handle'];
        }

        foreach (self::classesIn('Mail') as $className) {
            yield "mail:{$className}" => [$className, Mailable::class, 'envelope'];
        }

        foreach (self::classesIn('Providers') as $className) {
            yield "provider:{$className}" => [$className, ServiceProvider::class, 'register'];
        }

        yield 'http-kernel' => [\App\Http\Kernel::class, HttpKernel::class, 'handle'];
    }

    /** @return array<int, class-string> */
    private static function classesIn(string $relativeDirectory): array
    {
        $root = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'app';
        $directory = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativeDirectory);
        $classes = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
                continue;
            }

            $relativePath = substr($file->getPathname(), strlen($root) + 1, -4);
            $classes[] = 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath);
        }

        sort($classes);

        return $classes;
    }
}
