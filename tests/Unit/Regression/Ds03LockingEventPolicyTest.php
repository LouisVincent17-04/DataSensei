<?php

namespace Tests\Unit\Regression;

use App\Support\AntiCheatEventContract;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * DS-03: which recorded event types may lock an attempt under a policy.
 */
class Ds03LockingEventPolicyTest extends TestCase
{
    private const BASE = [
        'enabled' => true,
        'lock_screen_on_violation' => true,
        'allow_devtools_shortcuts' => false,
        'allow_paste' => false,
        'block_external_paste' => true,
        'require_fullscreen' => false,
        'detect_dual_monitor' => true,
        'block_dual_monitor' => true,
        'allow_right_click' => false,
    ];

    public static function policies(): array
    {
        return [
            'default policy' => [[], ['devtools_shortcut', 'blocked_paste']],
            'fullscreen required' => [['require_fullscreen' => true], ['devtools_shortcut', 'blocked_paste', 'fullscreen_exit']],
            'devtools allowed' => [['allow_devtools_shortcuts' => true], ['blocked_paste']],
            'paste fully allowed' => [['allow_paste' => true, 'block_external_paste' => false], ['devtools_shortcut']],
            'only external paste blocked' => [['allow_paste' => true], ['devtools_shortcut', 'blocked_paste']],
            'lock screen off' => [['lock_screen_on_violation' => false, 'require_fullscreen' => true], []],
            'policy disabled' => [['enabled' => false], []],
        ];
    }

    #[DataProvider('policies')]
    public function test_each_event_type_follows_its_own_setting(array $overrides, array $expected): void
    {
        $this->assertSame($expected, AntiCheatEventContract::lockingEventTypesFor(array_merge(self::BASE, $overrides)));
    }

    public function test_warning_only_and_reported_outcome_events_never_lock_through_the_general_branch(): void
    {
        $everythingStrict = array_merge(self::BASE, ['require_fullscreen' => true]);
        $types = AntiCheatEventContract::lockingEventTypesFor($everythingStrict);

        foreach (['dual_monitor_detected', 'right_click', 'threshold_exceeded', 'locked_attempt_finalized', 'focus_loss', 'copy'] as $eventType) {
            $this->assertNotContains($eventType, $types);
        }
    }
}
