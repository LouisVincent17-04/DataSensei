<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AssessmentDraftResumeMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('assessments', function (Blueprint $table): void {
            $table->id();
            $table->string('status', 30)->default('draft');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('assessments');
        parent::tearDown();
    }

    public function test_draft_resume_columns_are_added_idempotently(): void
    {
        $migration = require database_path(
            'migrations/2026_08_27_000003_add_draft_resume_fields_to_assessments.php'
        );

        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasColumn('assessments', 'draft_last_item'));
        $this->assertTrue(Schema::hasColumn('assessments', 'draft_saved_at'));
    }
}
