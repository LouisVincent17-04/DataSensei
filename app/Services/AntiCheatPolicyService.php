<?php

namespace App\Services;

use App\Models\AntiCheatEvent;
use App\Models\AntiCheatSetting;
use App\Models\AssignmentSubmission;
use App\Models\Challenge;
use App\Models\ClassAssignment;
use App\Models\User;
use App\Support\AntiCheatEventContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AntiCheatPolicyService
{
    public const INVALID_IDENTITY_MESSAGE = 'The protected attempt identity is invalid. Reload the assignment and try again.';

    /**
     * Backward-compatible entry point. Public MCQ and coding challenges intentionally return disabled settings.
     */
    public function settingsForUser(?User $user, string $assessmentType = 'assignment', ?ClassAssignment $assignment = null): array
    {
        if (!$user || $assessmentType !== 'assignment') {
            return $this->disabledSettings();
        }

        return $this->settingsForAssignment($user, $assignment);
    }

    public function settingsForAssignment(User $user, ?ClassAssignment $assignment = null): array
    {
        $settings = $this->assignmentSettingsQuery($user, $assignment)->get();

        if ($settings->isEmpty()) {
            return $this->disabledSettings();
        }

        $policy = $this->defaultActiveSettings();

        foreach ($settings as $setting) {
            $policy = $this->mergeOverride($policy, $setting->toArray());
        }

        if ($assignment) {
            $policy['class_assignment_id'] = (int) $assignment->id;
            $policy['class_id'] = (int) $assignment->class_id;
        }

        return $policy;
    }

    public function defaultActiveSettings(): array
    {
        return [
            'enabled'                      => true,
            'allow_tab_switch'             => false,
            'max_tab_switches'             => 2,
            'block_on_tab_limit'           => true,
            'require_fullscreen'           => false,
            'detect_dual_monitor'          => true,
            'block_dual_monitor'           => false,
            'allow_copy'                   => true,
            'allow_paste'                  => false,
            'block_external_paste'         => true,
            'allow_right_click'            => false,
            'allow_devtools_shortcuts'     => false,
            'show_warnings'                => true,
            'auto_submit_mcq_on_violation' => false,
            'lock_screen_on_violation'     => true,
        ];
    }

    public function disabledSettings(): array
    {
        return array_merge($this->defaultActiveSettings(), [
            'enabled' => false,
        ]);
    }

    /**
     * Kept so old public challenge controller code will not break, but it never blocks public practice challenges.
     */
    public function submissionBlocked(User $user, Challenge $challenge, string $assessmentType, ?string $sessionId = null): ?string
    {
        return null;
    }

    public function assignmentSubmissionBlocked(User $user, ClassAssignment $assignment, AssignmentSubmission $submission, ?string $sessionId = null): ?string
    {
        $policy = $this->settingsForAssignment($user, $assignment);

        if (empty($policy['enabled'])) {
            return null;
        }

        if (! $this->attemptIdentityMatches($submission, $sessionId)) {
            return self::INVALID_IDENTITY_MESSAGE;
        }

        return $this->blockedReason($this->attemptEventQuery($user, $assignment, $submission), $policy);
    }

    /**
     * The protected-attempt identity is a per-attempt secret rendered into the
     * take page. It never becomes optional, including after the timer expires.
     */
    public function attemptIdentityMatches(AssignmentSubmission $submission, ?string $sessionId): bool
    {
        $expectedSessionId = (string) ($submission->anti_cheat_session_id ?? '');

        return $expectedSessionId !== ''
            && is_string($sessionId)
            && hash_equals($expectedSessionId, $sessionId);
    }

    /**
     * Single authoritative integrity state of one assignment attempt. Used to
     * render the take page, to answer every recorded event and to decide the
     * submission outcome, so browser and server cannot drift apart.
     *
     * @return array{
     *     enabled: bool,
     *     blocked: bool,
     *     reason: ?string,
     *     focus_loss_count: int,
     *     max_tab_switches: int,
     *     remaining_allowance: ?int
     * }
     */
    public function attemptIntegrityState(User $user, ClassAssignment $assignment, AssignmentSubmission $submission, ?array $policy = null): array
    {
        $policy = $policy ?? $this->settingsForAssignment($user, $assignment);
        $maxTabSwitches = max(0, (int) ($policy['max_tab_switches'] ?? 0));

        if (empty($policy['enabled'])) {
            return [
                'enabled' => false,
                'blocked' => false,
                'reason' => null,
                'focus_loss_count' => 0,
                'max_tab_switches' => $maxTabSwitches,
                'remaining_allowance' => null,
            ];
        }

        $query = $this->attemptEventQuery($user, $assignment, $submission);
        $focusLossCount = $this->logicalFocusLossCount(clone $query);
        $reason = $this->blockedReason($query, $policy, $focusLossCount);
        $focusLimitApplies = ! ($policy['allow_tab_switch'] ?? true) && ($policy['block_on_tab_limit'] ?? true);

        return [
            'enabled' => true,
            'blocked' => $reason !== null,
            'reason' => $reason,
            'focus_loss_count' => $focusLossCount,
            'max_tab_switches' => $maxTabSwitches,
            // Focus losses that may still happen without locking the attempt.
            // null means the policy does not lock on focus loss at all.
            'remaining_allowance' => $focusLimitApplies
                ? max(0, $maxTabSwitches - $focusLossCount)
                : null,
        ];
    }

    private function attemptEventQuery(User $user, ClassAssignment $assignment, AssignmentSubmission $submission): Builder
    {
        $query = AntiCheatEvent::where('user_id', $user->id)
            ->where('assessment_type', 'assignment')
            ->where('class_assignment_id', $assignment->id)
            ->where('assignment_submission_id', $submission->id);

        $this->applySessionScope($query, (string) ($submission->anti_cheat_session_id ?? ''));

        return $query;
    }

    private function assignmentSettingsQuery(User $user, ?ClassAssignment $assignment): Builder
    {
        $query = AntiCheatSetting::query()->where('assessment_type', 'assignment');

        if ($assignment) {
            $classId = (int) $assignment->class_id;
            $instructorId = DB::table('classes')->where('id', $classId)->value('instructor_id');

            $query->where('instructor_id', $instructorId)
                ->where(function ($q) use ($classId) {
                    $q->whereNull('class_id')->orWhere('class_id', $classId);
                })
                ->orderByRaw('CASE WHEN class_id IS NULL THEN 0 ELSE 1 END');

            return $query;
        }

        $classIds = DB::table('class_student')
            ->where('student_id', $user->id)
            ->pluck('class_id')
            ->filter()
            ->values();

        if ($classIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        $instructorIds = DB::table('classes')
            ->whereIn('id', $classIds)
            ->pluck('instructor_id')
            ->filter()
            ->unique()
            ->values();

        return $query
            ->where(function ($q) use ($classIds, $instructorIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhere(function ($inner) use ($instructorIds) {
                        $inner->whereNull('class_id')
                            ->whereIn('instructor_id', $instructorIds);
                    });
            })
            ->orderByRaw('CASE WHEN class_id IS NULL THEN 0 ELSE 1 END');
    }

    private function applySessionScope(Builder $query, string $sessionId): void
    {
        $query->where('attempt_session_id', $sessionId);
    }

    /**
     * Every recorded event is judged against the policy flag that governs it.
     * An event the configured policy only logs (warning-only dual monitor,
     * right click) or does not restrict at all (fullscreen exit while
     * fullscreen is optional, a forged event) never blocks the attempt.
     */
    private function blockedReason(Builder $query, array $policy, ?int $focusLossCount = null): ?string
    {
        if (! ($policy['allow_tab_switch'] ?? true) && ($policy['block_on_tab_limit'] ?? true)) {
            $tabEvents = $focusLossCount ?? $this->logicalFocusLossCount(clone $query);

            if ($tabEvents > (int) ($policy['max_tab_switches'] ?? 0)) {
                return 'Your assignment attempt was locked because it exceeded the allowed tab-switch/focus-loss limit.';
            }
        }

        if (($policy['detect_dual_monitor'] ?? false) && ($policy['block_dual_monitor'] ?? false)) {
            $dualMonitorDetected = (clone $query)
                ->where('event_type', 'dual_monitor_detected')
                ->exists();

            if ($dualMonitorDetected) {
                return 'Your assignment attempt was locked because multiple screens were detected under the current anti-cheat settings.';
            }
        }

        $lockingEventTypes = AntiCheatEventContract::lockingEventTypesFor($policy);

        if ($lockingEventTypes !== []) {
            $critical = (clone $query)
                ->whereIn('event_type', $lockingEventTypes)
                ->exists();

            if ($critical) {
                return 'Your assignment attempt was locked because a restricted action was detected.';
            }
        }

        return null;
    }

    private function logicalFocusLossCount(Builder $query): int
    {
        $events = $query
            ->whereIn('event_type', AntiCheatEventContract::focusEventTypes())
            ->orderByRaw('COALESCE(occurred_at, created_at)')
            ->orderBy('id')
            ->get(['id', 'event_uuid', 'occurred_at', 'created_at']);

        $count = 0;
        $lastTimestamp = null;
        $seenEventUuids = [];

        foreach ($events as $event) {
            $eventUuid = trim((string) $event->event_uuid);
            if ($eventUuid !== '') {
                if (isset($seenEventUuids[$eventUuid])) {
                    continue;
                }

                $seenEventUuids[$eventUuid] = true;
            }

            $occurredAt = $event->occurred_at ?? $event->created_at;
            $timestamp = $occurredAt?->getTimestamp();
            if ($lastTimestamp !== null
                && $timestamp !== null
                && $timestamp >= $lastTimestamp
                && $timestamp - $lastTimestamp < AntiCheatEventContract::FOCUS_CORRELATION_WINDOW_SECONDS) {
                continue;
            }

            $count++;
            $lastTimestamp = $timestamp;
        }

        return $count;
    }

    private function mergeOverride(array $base, array $override): array
    {
        foreach ([
            'enabled',
            'allow_tab_switch',
            'block_on_tab_limit',
            'require_fullscreen',
            'detect_dual_monitor',
            'block_dual_monitor',
            'allow_copy',
            'allow_paste',
            'block_external_paste',
            'allow_right_click',
            'allow_devtools_shortcuts',
            'show_warnings',
            'auto_submit_mcq_on_violation',
            'lock_screen_on_violation',
        ] as $key) {
            if (array_key_exists($key, $override)) {
                $base[$key] = (bool) $override[$key];
            }
        }

        if (array_key_exists('max_tab_switches', $override)) {
            $base['max_tab_switches'] = max(0, (int) $override['max_tab_switches']);
        }

        return $base;
    }
}
