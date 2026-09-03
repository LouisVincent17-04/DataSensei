<?php

namespace Tests\Unit;

use App\Support\AntiCheatEventContract;
use PHPUnit\Framework\TestCase;

class AntiCheatEventContractTest extends TestCase
{
    public function test_every_event_emitted_by_the_browser_exists_in_the_shared_contract(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $partial = file_get_contents(
            $projectRoot . '/resources/views/student/partials/anti-cheat-guard.blade.php'
        );
        $this->assertIsString($partial);

        preg_match_all("/logEvent\(\s*['\"]([^'\"]+)['\"]/", $partial, $matches);
        $emittedTypes = array_values(array_unique(array_merge(
            $matches[1],
            [AntiCheatEventContract::FOCUS_LOSS_EVENT, 'threshold_exceeded']
        )));
        sort($emittedTypes);
        $acceptedTypes = AntiCheatEventContract::eventTypes();
        sort($acceptedTypes);

        $this->assertSame([], array_values(array_diff($emittedTypes, $acceptedTypes)));
        $this->assertStringContainsString(
            'AntiCheatEventContract::clientContract()',
            $partial
        );
    }

    public function test_browser_capability_results_are_centrally_classified_as_informational(): void
    {
        foreach ([
            'fullscreen_entered',
            'fullscreen_request_failed',
            'dual_monitor_check_unavailable',
            'dual_monitor_check_failed',
        ] as $eventType) {
            $this->assertContains($eventType, AntiCheatEventContract::eventTypes());
            $this->assertSame('info', AntiCheatEventContract::severityFor($eventType));
            $this->assertSame(
                'informational',
                AntiCheatEventContract::classificationFor($eventType)
            );
        }
    }

    public function test_controller_validation_uses_the_shared_contract_instead_of_an_inline_allow_list(): void
    {
        $controller = file_get_contents(
            dirname(__DIR__, 2) . '/app/Http/Controllers/AntiCheatEventController.php'
        );

        $this->assertIsString($controller);
        $this->assertStringContainsString(
            'Rule::in(AntiCheatEventContract::eventTypes())',
            $controller
        );
        $this->assertStringNotContainsString("'event_type' => 'required|in:", $controller);
    }
}
