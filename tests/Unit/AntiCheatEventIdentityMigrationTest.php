<?php

namespace Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AntiCheatEventIdentityMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('anti_cheat_events', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('assignment_submission_id')->nullable();
            $table->string('attempt_session_id', 120)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('anti_cheat_events');

        parent::tearDown();
    }

    public function test_event_identity_migration_is_idempotent_and_preserves_integrity_data(): void
    {
        $migration = require database_path(
            'migrations/2026_08_31_000001_add_anti_cheat_event_identity.php'
        );

        $migration->up();
        $migration->up();

        $this->assertTrue(Schema::hasColumn('anti_cheat_events', 'event_uuid'));
        $indexNames = array_column(Schema::getIndexes('anti_cheat_events'), 'name');
        $this->assertContains('anti_cheat_event_session_uuid_uq', $indexNames);
        $this->assertContains('anti_cheat_event_focus_lookup_idx', $indexNames);

        $migration->down();

        $this->assertTrue(Schema::hasColumn('anti_cheat_events', 'event_uuid'));
    }
}
