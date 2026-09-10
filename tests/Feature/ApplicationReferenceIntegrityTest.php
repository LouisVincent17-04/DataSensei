<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class ApplicationReferenceIntegrityTest extends TestCase
{
    public function test_every_controller_route_points_to_a_concrete_public_action(): void
    {
        $checked = 0;

        foreach (Route::getRoutes() as $route) {
            $controller = $route->getAction('controller');
            if (! is_string($controller) || $controller === '') {
                continue;
            }

            if (str_contains($controller, '@')) {
                [$className, $methodName] = explode('@', $controller, 2);
            } else {
                $className = $controller;
                $methodName = '__invoke';
            }

            $description = implode('|', $route->methods()).' '.$route->uri();
            $this->assertTrue(class_exists($className), "{$description} references missing {$className}.");
            $this->assertFalse((new ReflectionClass($className))->isAbstract(), "{$description} uses an abstract controller.");
            $this->assertTrue(method_exists($className, $methodName), "{$description} references missing {$className}::{$methodName}().");
            $this->assertTrue(
                (new ReflectionMethod($className, $methodName))->isPublic(),
                "{$description} must reference a public controller action."
            );
            $checked++;
        }

        $this->assertGreaterThan(0, $checked, 'The application must register controller routes.');
    }

    public function test_every_literal_view_reference_points_to_an_existing_blade_view(): void
    {
        $references = [];

        foreach ($this->phpFiles([base_path('app'), base_path('routes')]) as $file) {
            $contents = (string) file_get_contents($file);
            preg_match_all('/\bview\(\s*[\'\"]([^\'\"]+)[\'\"]/', $contents, $matches);

            foreach ($matches[1] ?? [] as $viewName) {
                $references[$viewName][] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            }
        }

        $this->assertNotSame([], $references);
        foreach ($references as $viewName => $files) {
            $this->assertTrue(
                View::exists($viewName),
                "Missing view {$viewName}; referenced by ".implode(', ', $files).'.'
            );
        }
    }

    public function test_every_literal_blade_layout_and_include_reference_exists(): void
    {
        $references = [];

        foreach ($this->phpFiles([resource_path('views')]) as $file) {
            $contents = (string) file_get_contents($file);
            preg_match_all('/@(?:extends|include|component)\(\s*[\'\"]([^\'\"]+)[\'\"]/', $contents, $matches);

            foreach ($matches[1] ?? [] as $viewName) {
                $references[$viewName][] = str_replace(resource_path('views').DIRECTORY_SEPARATOR, '', $file);
            }
        }

        foreach ($references as $viewName => $files) {
            $this->assertTrue(
                View::exists($viewName),
                "Missing Blade dependency {$viewName}; referenced by ".implode(', ', $files).'.'
            );
        }
    }

    /**
     * @param array<int, string> $directories
     * @return array<int, string>
     */
    private function phpFiles(array $directories): array
    {
        $files = [];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'php') {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return $files;
    }
}
