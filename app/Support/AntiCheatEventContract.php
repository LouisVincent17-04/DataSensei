<?php

namespace App\Support;

final class AntiCheatEventContract
{
    public const FOCUS_LOSS_EVENT = 'focus_loss';

    public const LOCKED_ATTEMPT_FINALIZED_EVENT = 'locked_attempt_finalized';

    public const FOCUS_CORRELATION_WINDOW_SECONDS = 2;

    /** @var list<int> */
    public const TRANSIENT_HTTP_STATUSES = [408, 425, 429, 500, 502, 503, 504];

    /**
     * The single source of truth for anti-cheat events accepted by the API and
     * exposed to the browser. Legacy focus event names remain accepted so that
     * an already-open assignment page can finish safely after deployment.
     *
     * @var array<string, array{severity: string, classification: string}>
     */
    private const DEFINITIONS = [
        'dual_monitor_detected' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        'blocked_paste' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        'devtools_shortcut' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        'fullscreen_exit' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        'right_click' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        'threshold_exceeded' => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        // Written by the server when a locked browser finalizes an attempt.
        self::LOCKED_ATTEMPT_FINALIZED_EVENT => [
            'severity' => 'critical',
            'classification' => 'violation',
        ],
        self::FOCUS_LOSS_EVENT => [
            'severity' => 'warning',
            'classification' => 'violation',
        ],
        'visibility_hidden' => [
            'severity' => 'warning',
            'classification' => 'violation',
        ],
        'window_blur' => [
            'severity' => 'warning',
            'classification' => 'violation',
        ],
        'tab_switch' => [
            'severity' => 'warning',
            'classification' => 'violation',
        ],
        'copy_shortcut_blocked' => [
            'severity' => 'warning',
            'classification' => 'violation',
        ],
        'copy' => [
            'severity' => 'warning',
            'classification' => 'activity',
        ],
        'paste' => [
            'severity' => 'warning',
            'classification' => 'activity',
        ],
        'cut' => [
            'severity' => 'info',
            'classification' => 'activity',
        ],
        'fullscreen_entered' => [
            'severity' => 'info',
            'classification' => 'informational',
        ],
        'fullscreen_request_failed' => [
            'severity' => 'info',
            'classification' => 'informational',
        ],
        'dual_monitor_check_unavailable' => [
            'severity' => 'info',
            'classification' => 'informational',
        ],
        'dual_monitor_check_failed' => [
            'severity' => 'info',
            'classification' => 'informational',
        ],
    ];

    /** @return list<string> */
    public static function eventTypes(): array
    {
        return array_keys(self::DEFINITIONS);
    }

    /** @return array{severity: string, classification: string} */
    public static function definitionFor(string $eventType): array
    {
        return self::DEFINITIONS[$eventType] ?? [
            'severity' => 'info',
            'classification' => 'informational',
        ];
    }

    public static function severityFor(string $eventType): string
    {
        return self::definitionFor($eventType)['severity'];
    }

    public static function classificationFor(string $eventType): string
    {
        return self::definitionFor($eventType)['classification'];
    }

    /** @return list<string> */
    public static function focusEventTypes(): array
    {
        return [
            self::FOCUS_LOSS_EVENT,
            'visibility_hidden',
            'window_blur',
            'tab_switch',
        ];
    }

    /**
     * Event types that lock an attempt under the given policy. Each type is
     * tied to the setting that makes it a restricted action, and all of them
     * require "Lock screen on critical violation".
     *
     * Deliberately absent:
     *  - dual_monitor_detected: governed only by detect + block dual monitor.
     *  - right_click: the setting promises "blocked and logged", never a lock.
     *  - threshold_exceeded / locked_attempt_finalized: outcomes reported by
     *    the browser; the server derives the focus limit from the focus events
     *    themselves and handles finalization explicitly.
     *
     * @param  array<string, mixed>  $policy
     * @return list<string>
     */
    public static function lockingEventTypesFor(array $policy): array
    {
        if (empty($policy['enabled']) || ! ($policy['lock_screen_on_violation'] ?? true)) {
            return [];
        }

        $types = [];

        if (! ($policy['allow_devtools_shortcuts'] ?? false)) {
            $types[] = 'devtools_shortcut';
        }

        if (! ($policy['allow_paste'] ?? false) || ($policy['block_external_paste'] ?? true)) {
            $types[] = 'blocked_paste';
        }

        if ($policy['require_fullscreen'] ?? false) {
            $types[] = 'fullscreen_exit';
        }

        return $types;
    }

    public static function isFocusEvent(string $eventType): bool
    {
        return in_array($eventType, self::focusEventTypes(), true);
    }

    /**
     * This payload is JSON-encoded directly into the Blade partial, so browser
     * and controller validation cannot drift into separate allow-lists.
     *
     * @return array{
     *     version: int,
     *     events: array<string, array{severity: string, classification: string}>,
     *     focus_loss_event: string,
     *     focus_correlation_window_ms: int,
     *     transient_http_statuses: list<int>
     * }
     */
    public static function clientContract(): array
    {
        return [
            'version' => 1,
            'events' => self::DEFINITIONS,
            'focus_loss_event' => self::FOCUS_LOSS_EVENT,
            'focus_correlation_window_ms' => self::FOCUS_CORRELATION_WINDOW_SECONDS * 1000,
            'transient_http_statuses' => self::TRANSIENT_HTTP_STATUSES,
        ];
    }
}
