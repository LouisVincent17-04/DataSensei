<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SidebarRoutesTest extends TestCase
{
    #[Test]
    public function every_named_route_used_by_sidebars_exists(): void
    {
        $availableRoutes = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values()
            ->all();

        $missing = [];

        foreach ($this->sidebarFiles() as $file) {
            $contents = file_get_contents($file);
            preg_match_all('/route\([\'\"]([^\'\"]+)[\'\"]/', $contents, $matches);

            foreach ($matches[1] ?? [] as $routeName) {
                if (! in_array($routeName, $availableRoutes, true)) {
                    $missing[] = $routeName . ' referenced in ' . basename($file);
                }
            }
        }

        $this->assertSame([], array_values(array_unique($missing)));
    }

    #[Test]
    public function sidebars_do_not_use_placeholder_links_for_real_features(): void
    {
        foreach ($this->sidebarFiles() as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString('href="#', $contents, basename($file) . ' still contains a placeholder link.');
        }

        $studentSidebar = file_get_contents(resource_path('views/partials/sidebar.blade.php'));
        $this->assertStringContainsString("route('student.analytics.index')", $studentSidebar);
        $this->assertStringContainsString("route('student.achievements.index')", $studentSidebar);
        $this->assertStringContainsString("route('student.leaderboard.index')", $studentSidebar);

        $adminSidebar = file_get_contents(resource_path('views/partials/admin-sidebar.blade.php'));
        $this->assertStringContainsString("route('admin.users.index')", $adminSidebar);
        $this->assertStringContainsString("route('admin.content.index')", $adminSidebar);
        $this->assertStringContainsString("route('admin.gamification.index')", $adminSidebar);
        $this->assertStringContainsString("route('admin.reports.index')", $adminSidebar);
    }

    /**
     * @return array<int, string>
     */
    private function sidebarFiles(): array
    {
        return array_values(array_filter([
            resource_path('views/partials/sidebar.blade.php'),
            resource_path('views/partials/instructor-sidebar.blade.php'),
            resource_path('views/partials/superadmin-sidebar.blade.php'),
            resource_path('views/partials/admin-sidebar.blade.php'),
        ], static fn (string $file) => file_exists($file)));
    }
}
