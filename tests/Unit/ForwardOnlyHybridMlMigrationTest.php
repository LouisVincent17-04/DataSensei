<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ForwardOnlyHybridMlMigrationTest extends TestCase
{
    #[DataProvider('conditionallyOwnedMigrationProvider')]
    public function test_conditionally_owned_tables_are_never_dropped_on_rollback(string $relativePath): void
    {
        $source = file_get_contents(database_path($relativePath));
        $this->assertNotFalse($source);
        $this->assertStringContainsString('Forward-only by design', $source);

        preg_match(
            '/public function down\(\): void\s*\{(?<body>.*?)\n\s*\}/s',
            $source,
            $match
        );

        $this->assertArrayHasKey('body', $match, 'The migration must define down().');
        $this->assertDoesNotMatchRegularExpression('/Schema::drop/i', $match['body']);
        $this->assertStringNotContainsString('disableForeignKeyConstraints', $match['body']);
    }

    public static function conditionallyOwnedMigrationProvider(): array
    {
        return [
            'hybrid ML module tables' => [
                'migrations/2026_08_01_000002_create_hybrid_ml_module_tables.php',
            ],
            'queue tables' => [
                'migrations/2026_08_01_000003_create_queue_tables_for_ml_training.php',
            ],
        ];
    }

    public function test_current_version_foreign_key_only_ignores_a_confirmed_duplicate(): void
    {
        $source = file_get_contents(database_path('migrations/2026_08_01_000002_create_hybrid_ml_module_tables.php'));
        $this->assertNotFalse($source);

        $this->assertStringContainsString('catch (QueryException $exception)', $source);
        $this->assertStringNotContainsString('catch (Throwable)', $source);
        $this->assertStringContainsString('currentVersionForeignKeyExists()', $source);
        $this->assertStringContainsString('isDuplicateConstraintFailure($exception)', $source);
        $this->assertStringContainsString('throw $exception;', $source);
        $this->assertStringContainsString("CONSTRAINT_NAME = ?", $source);
        $this->assertStringContainsString("CONSTRAINT_TYPE = 'FOREIGN KEY'", $source);
    }
}
