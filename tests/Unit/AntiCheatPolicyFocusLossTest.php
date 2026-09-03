<?php

namespace Tests\Unit;

use App\Models\AntiCheatEvent;
use App\Services\AntiCheatPolicyService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class AntiCheatPolicyFocusLossTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        Schema::create('anti_cheat_events', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type', 80);
            $table->string('event_uuid', 36)->nullable();
            $table->timestamp('occurred_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('anti_cheat_events');

        parent::tearDown();
    }

    public function test_legacy_visibility_and_blur_rows_count_as_one_logical_focus_loss(): void
    {
        $start = Carbon::parse('2026-08-31 10:00:00');
        $this->insertEvent('visibility_hidden', null, $start);
        $this->insertEvent('window_blur', null, $start->copy()->addSecond());
        $this->insertEvent(
            'focus_loss',
            '44444444-4444-4444-8444-444444444444',
            $start->copy()->addSeconds(3)
        );
        $this->insertEvent('copy', null, $start->copy()->addSeconds(4));

        $method = new ReflectionMethod(AntiCheatPolicyService::class, 'logicalFocusLossCount');
        $method->setAccessible(true);
        $count = $method->invoke(
            new AntiCheatPolicyService(),
            AntiCheatEvent::query()
        );

        $this->assertSame(2, $count);
    }

    private function insertEvent(string $eventType, ?string $eventUuid, Carbon $occurredAt): void
    {
        DB::table('anti_cheat_events')->insert([
            'event_type' => $eventType,
            'event_uuid' => $eventUuid,
            'occurred_at' => $occurredAt,
            'created_at' => $occurredAt,
            'updated_at' => $occurredAt,
        ]);
    }
}
