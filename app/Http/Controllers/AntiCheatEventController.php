<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Services\AntiCheatPolicyService;
use App\Support\AntiCheatEventContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Records anti-cheat events for protected assessment attempts (DataSensei
 * Updates 11: assessments carry the anti-cheat duty that assignments held).
 */
class AntiCheatEventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'assessment_type' => ['required', 'in:assessment'],
            'event_type' => ['required', 'string', Rule::in(AntiCheatEventContract::eventTypes())],
            'event_uuid' => ['nullable', 'uuid'],
            'attempt_session_id' => ['required', 'string', 'max:120'],
            'assessment_id' => ['required', 'integer', 'exists:assessments,id'],
            'assessment_submission_id' => ['required', 'integer', 'exists:assessment_submissions,id'],
            'assessment_question_id' => ['nullable', 'integer', 'exists:assessment_questions,id'],
            'details' => ['nullable', 'array', 'max:20'],
        ]);

        $userId = (int) Auth::id();
        $eventUuid = (string) ($data['event_uuid'] ?? Str::uuid());

        $result = DB::transaction(function () use ($data, $eventUuid, $userId): array {
            $submission = DB::table('assessment_submissions')
                ->where('id', $data['assessment_submission_id'])
                ->lockForUpdate()
                ->first(['id', 'assessment_id', 'student_id', 'status', 'anti_cheat_session_id']);

            abort_unless(
                $submission
                    && (int) $submission->assessment_id === (int) $data['assessment_id']
                    && (int) $submission->student_id === $userId
                    && $submission->status === 'in_progress'
                    && is_string($submission->anti_cheat_session_id)
                    && hash_equals($submission->anti_cheat_session_id, $data['attempt_session_id']),
                403,
                'Invalid protected assessment attempt.'
            );

            $assessment = DB::table('assessments')
                ->where('id', $data['assessment_id'])
                ->lockForUpdate()
                ->first(['id', 'class_id']);
            abort_unless($assessment, 403, 'Invalid protected assessment attempt.');

            $enrollment = DB::table('class_student')
                ->where('class_id', $assessment->class_id)
                ->where('student_id', $userId)
                ->lockForUpdate()
                ->first();
            abort_unless($enrollment, 403, 'You are not enrolled in this assessment class.');

            if (! empty($data['assessment_question_id'])) {
                $questionBelongsToAssessment = DB::table('assessment_questions')
                    ->where('id', $data['assessment_question_id'])
                    ->where('assessment_id', $assessment->id)
                    ->exists();
                abort_unless($questionBelongsToAssessment, 403, 'Invalid assessment question context.');
            }

            $duplicate = $this->duplicateEvent($data, $eventUuid, $userId);
            if ($duplicate) {
                abort_if(
                    $duplicate->event_uuid === $eventUuid
                        && $duplicate->event_type !== $data['event_type'],
                    409,
                    'The anti-cheat event identifier was already used for a different event type.'
                );

                return ['event' => $duplicate, 'deduplicated' => true];
            }

            $event = AntiCheatEvent::create([
                'user_id' => $userId,
                'class_id' => (int) $assessment->class_id,
                'assessment_id' => $data['assessment_id'],
                'assessment_submission_id' => $data['assessment_submission_id'],
                'assessment_question_id' => $data['assessment_question_id'] ?? null,
                'assessment_type' => 'assessment',
                'event_type' => $data['event_type'],
                'severity' => AntiCheatEventContract::severityFor($data['event_type']),
                'attempt_session_id' => $data['attempt_session_id'],
                'event_uuid' => $eventUuid,
                'details' => $this->sanitizedDetails($data['details'] ?? []),
                'occurred_at' => now(),
            ]);

            return ['event' => $event, 'deduplicated' => false];
        }, 3);

        /** @var AntiCheatEvent $event */
        $event = $result['event'];

        return response()->json([
            // The browser locks exactly when the server considers the attempt
            // blocked and shows the server's deduplicated focus-loss count.
            'integrity' => $this->integrityState((int) $data['assessment_id'], (int) $data['assessment_submission_id']),
            'ok' => true,
            'event_id' => $event->id,
            'event_uuid' => $event->event_uuid,
            'severity' => $event->severity,
            'classification' => AntiCheatEventContract::classificationFor($event->event_type),
            'deduplicated' => $result['deduplicated'],
        ]);
    }

    /**
     * @return array{blocked: bool, reason: ?string, focus_loss_count: int, max_tab_switches: int, remaining_allowance: ?int}|null
     */
    private function integrityState(int $assessmentId, int $submissionId): ?array
    {
        $assessment = Assessment::find($assessmentId);
        $submission = AssessmentSubmission::find($submissionId);
        $user = Auth::user();

        if (! $assessment || ! $submission || ! $user) {
            return null;
        }

        $state = app(AntiCheatPolicyService::class)->attemptIntegrityState($user, $assessment, $submission);

        return [
            'blocked' => $state['blocked'],
            'reason' => $state['reason'],
            'focus_loss_count' => $state['focus_loss_count'],
            'max_tab_switches' => $state['max_tab_switches'],
            'remaining_allowance' => $state['remaining_allowance'],
        ];
    }

    private function duplicateEvent(array $data, string $eventUuid, int $userId): ?AntiCheatEvent
    {
        $attemptEvents = AntiCheatEvent::query()
            ->where('user_id', $userId)
            ->where('assessment_type', 'assessment')
            ->where('assessment_id', $data['assessment_id'])
            ->where('assessment_submission_id', $data['assessment_submission_id'])
            ->where('attempt_session_id', $data['attempt_session_id']);

        $uuidDuplicate = (clone $attemptEvents)
            ->where('event_uuid', $eventUuid)
            ->first();
        if ($uuidDuplicate) {
            return $uuidDuplicate;
        }

        if (! AntiCheatEventContract::isFocusEvent($data['event_type'])) {
            return null;
        }

        return (clone $attemptEvents)
            ->whereIn('event_type', AntiCheatEventContract::focusEventTypes())
            ->where('occurred_at', '>=', now()->subSeconds(
                AntiCheatEventContract::FOCUS_CORRELATION_WINDOW_SECONDS
            ))
            ->latest('occurred_at')
            ->first();
    }

    private function sanitizedDetails(array $details): array
    {
        $sanitized = [];

        foreach (array_slice($details, 0, 20, true) as $key => $value) {
            if (! is_scalar($value) && $value !== null) {
                continue;
            }

            $safeKey = mb_substr((string) $key, 0, 80);
            if ($safeKey === '') {
                continue;
            }

            $sanitized[$safeKey] = is_string($value)
                ? mb_substr($value, 0, 1000)
                : $value;
        }

        return $sanitized;
    }
}
