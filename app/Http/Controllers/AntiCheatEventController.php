<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AntiCheatEventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'assessment_type'          => 'required|in:assignment',
            'event_type'               => 'required|in:dual_monitor_detected,blocked_paste,devtools_shortcut,fullscreen_exit,right_click,threshold_exceeded,copy,paste,cut,visibility_hidden,window_blur,tab_switch,copy_shortcut_blocked',
            'attempt_session_id'       => 'required|string|max:120',
            'class_assignment_id'      => 'required|integer|exists:class_assignments,id',
            'assignment_submission_id' => 'required|integer|exists:assignment_submissions,id',
            'assignment_question_id'   => 'nullable|integer|exists:assignment_questions,id',
            'details'                  => 'nullable|array|max:20',
        ]);

        $userId = (int) Auth::id();

        $event = DB::transaction(function () use ($data, $userId): AntiCheatEvent {
            $submission = DB::table('assignment_submissions')
                ->where('id', $data['assignment_submission_id'])
                ->lockForUpdate()
                ->first(['id', 'class_assignment_id', 'student_id', 'status', 'anti_cheat_session_id']);

            abort_unless(
                $submission
                    && (int) $submission->class_assignment_id === (int) $data['class_assignment_id']
                    && (int) $submission->student_id === $userId
                    && $submission->status === 'in_progress'
                    && is_string($submission->anti_cheat_session_id)
                    && hash_equals($submission->anti_cheat_session_id, $data['attempt_session_id']),
                403,
                'Invalid protected assignment attempt.'
            );

            $assignment = DB::table('class_assignments')
                ->where('id', $data['class_assignment_id'])
                ->lockForUpdate()
                ->first(['id', 'class_id', 'assignment_library_item_id']);
            abort_unless($assignment, 403, 'Invalid protected assignment attempt.');

            $enrollment = DB::table('class_student')
                ->where('class_id', $assignment->class_id)
                ->where('student_id', $userId)
                ->lockForUpdate()
                ->first();
            abort_unless($enrollment, 403, 'You are not enrolled in this assignment class.');

            if (! empty($data['assignment_question_id'])) {
                $questionBelongsToAssignment = DB::table('assignment_questions')
                    ->where('id', $data['assignment_question_id'])
                    ->where('assignment_library_item_id', $assignment->assignment_library_item_id)
                    ->exists();
                abort_unless($questionBelongsToAssignment, 403, 'Invalid assignment question context.');
            }

            return AntiCheatEvent::create([
                'user_id' => $userId,
                'class_id' => (int) $assignment->class_id,
                'class_assignment_id' => $data['class_assignment_id'],
                'assignment_submission_id' => $data['assignment_submission_id'],
                'assignment_question_id' => $data['assignment_question_id'] ?? null,
                'assessment_type' => 'assignment',
                'event_type' => $data['event_type'],
                'severity' => $this->severityFor($data['event_type']),
                'attempt_session_id' => $data['attempt_session_id'],
                'details' => $this->sanitizedDetails($data['details'] ?? []),
                'occurred_at' => now(),
            ]);
        }, 3);

        return response()->json([
            'ok'       => true,
            'event_id' => $event->id,
            'severity' => $event->severity,
        ]);
    }

    private function severityFor(string $eventType): string
    {
        return match ($eventType) {
            'dual_monitor_detected',
            'blocked_paste',
            'devtools_shortcut',
            'fullscreen_exit',
            'right_click',
            'threshold_exceeded' => 'critical',
            'copy',
            'paste',
            'visibility_hidden',
            'window_blur',
            'tab_switch',
            'copy_shortcut_blocked' => 'warning',
            default => 'info',
        };
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
