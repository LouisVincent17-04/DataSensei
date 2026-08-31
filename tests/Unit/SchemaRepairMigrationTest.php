<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaRepairMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('classes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
        });

        Schema::create('table_of_specifications', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('module_no');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('table_of_specifications');
        Schema::dropIfExists('classes');

        parent::tearDown();
    }

    public function test_schema_repairs_add_missing_columns_and_are_safe_to_run_again(): void
    {
        $coverageMigration = require database_path(
            'migrations/2026_08_27_000001_add_custom_coverage_to_table_of_specifications.php'
        );
        $classMigration = require database_path(
            'migrations/2026_08_27_000002_repair_allow_self_enroll_on_classes.php'
        );

        $coverageMigration->up();
        $classMigration->up();
        $coverageMigration->up();
        $classMigration->up();

        $this->assertTrue(Schema::hasColumn('table_of_specifications', 'custom_coverage'));
        $this->assertTrue(Schema::hasColumn('classes', 'allow_self_enroll'));

        $classId = DB::table('classes')->insertGetId(['name' => 'Migration Test Class']);
        $this->assertSame(0, (int) DB::table('classes')->where('id', $classId)->value('allow_self_enroll'));
    }
}
