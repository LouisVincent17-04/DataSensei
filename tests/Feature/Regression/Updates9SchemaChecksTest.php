<?php

namespace Tests\Feature\Regression;

use App\Models\User;
use App\Support\SchemaInspector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * DataSensei Updates 9, task 8: signing in after a quiet period could run past
 * PHP's 60-second limit. The dashboards made dozens of information_schema
 * lookups per page (65 on the admin dashboard), and on MySQL 5.5 each one
 * makes InnoDB recalculate index statistics from disk. Table and column
 * checks now come from one listing per request.
 */
class Updates9SchemaChecksTest extends TestCase
{
    use RefreshDatabase;

    public function test_table_and_column_checks_are_answered_from_one_listing_per_request(): void
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $check = function (int $i): void {
            $this->assertTrue(SchemaInspector::hasTable('users'));
            $this->assertTrue(SchemaInspector::hasTable('classes'));
            $this->assertFalse(SchemaInspector::hasTable('no_such_table_'.($i % 2)));
            $this->assertTrue(SchemaInspector::hasColumn('users', 'email'));
            $this->assertTrue(SchemaInspector::hasColumn('users', 'EMAIL'));
            $this->assertFalse(SchemaInspector::hasColumn('users', 'no_such_column'));
            $this->assertFalse(SchemaInspector::hasColumn('no_such_table', 'id'));
        };

        $check(0);
        $afterFirst = count($queries);
        foreach (range(1, 20) as $i) {
            $check($i);
        }

        $this->assertGreaterThan(0, $afterFirst);
        $this->assertSame($afterFirst, count($queries), 'Repeated checks ask the database nothing new: '.implode(' | ', $queries));
    }

    public function test_it_never_outlives_the_request(): void
    {
        $first = app(SchemaInspector::class);
        $this->assertSame($first, app(SchemaInspector::class));

        $this->app->forgetScopedInstances();
        $this->assertNotSame($first, app(SchemaInspector::class));
    }

    public function test_no_page_code_queries_information_schema_or_the_schema_builder_directly(): void
    {
        $skip = [
            // A diagnostic command, run by hand.
            'Console/Commands/DataSenseiPreflight.php',
            'Support/SchemaInspector.php',
            // Blocks students from reading INFORMATION_SCHEMA in the SQL sandbox.
            'Http/Controllers/SqlSandboxController.php',
        ];
        $offenders = [];
        foreach (File::allFiles(app_path()) as $file) {
            $path = str_replace('\\', '/', $file->getRelativePathname());
            if (in_array($path, $skip, true)) {
                continue;
            }

            // Code only: comments may explain the history.
            $code = '';
            foreach (token_get_all(File::get($file->getPathname())) as $token) {
                if (is_array($token) && in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                    continue;
                }
                $code .= is_array($token) ? $token[1] : $token;
            }

            if (preg_match('/information_schema|Schema::has(Table|Column)\(/i', $code)) {
                $offenders[] = $path;
            }
        }

        $this->assertSame([], $offenders);
    }

    public function test_dashboards_still_render_for_each_role(): void
    {
        foreach ([[User::ROLE_ADMIN, 'admin.dashboard'], [User::ROLE_SUPERADMIN, 'superadmin.dashboard'], [User::ROLE_USER, 'studentDashboard']] as [$role, $route]) {
            $this->authenticateAs($this->roleUser($role))->get(route($route))->assertOk();
        }
    }
}
