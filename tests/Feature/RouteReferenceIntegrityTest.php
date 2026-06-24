<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RouteReferenceIntegrityTest extends TestCase
{
    #[Test]
    public function all_named_routes_referenced_by_php_and_blade_files_exist(): void
    {
        $availableRoutes = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values()
            ->all();

        $missing = [];

        foreach ($this->applicationFiles() as $file) {
            $contents = file_get_contents($file);
            preg_match_all('/route\([\'\"]([^\'\"]+)[\'\"]/', $contents, $matches);

            foreach ($matches[1] ?? [] as $routeName) {
                if (! in_array($routeName, $availableRoutes, true)) {
                    $missing[] = $routeName . ' referenced in ' . str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file);
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)));
    }

    /**
     * @return array<int, string>
     */
    private function applicationFiles(): array
    {
        $directories = [
            base_path('app'),
            base_path('routes'),
            resource_path('views'),
        ];

        $files = [];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));

            foreach ($iterator as $file) {
                if (! $file->isFile()) {
                    continue;
                }

                if (in_array($file->getExtension(), ['php'], true)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        sort($files);

        return $files;
    }
}
