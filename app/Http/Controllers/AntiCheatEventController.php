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
            'event_type'               => 'required|string|max:80',
            'attempt_session_id'       => 'nullable|string|max:120',
            'class_assignment_id'      => 'required|integer|exists:class_assignments,id',
            'assignment_submission_id' => 'required|integer|exists:assignment_submissions,id',
            'assignment_question_id'   => 'nullable|integer|exists:assignment_questions,id',
            'details'                  => 'nullable|array',
            'occurred_at'              => 'nullable|date',
        ]);

        $userId = (int) Auth::id();

        $assignment = DB::table('class_assignments')
            ->where('id', $data['class_assignment_id'])
            ->first(['id', 'class_id']);

        $submission = DB::table('assignment_submissions')
            ->where('id', $data['assignment_submission_id'])
            ->first(['id', 'class_assignment_id', 'student_id']);

        if (!$assignment || !$submission ||
            (int) $submission->class_assignment_id !== (int) $assignment->id ||
            (int) $submission->student_id !== $userId) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid protected assignment attempt.',
            ], 403);
        }

        $isEnrolled = DB::table('class_student')
            ->where('class_id', $assignment->class_id)
            ->where('student_id', $userId)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'ok' => false,
                'message' => 'You are not enrolled in this assignment class.',
            ], 403);
        }

        $event = AntiCheatEvent::create([
            'user_id'                  => $userId,
            'class_id'                 => (int) $assignment->class_id,
            'class_assignment_id'      => $data['class_assignment_id'],
            'assignment_submission_id' => $data['assignment_submission_id'],
            'assignment_question_id'   => $data['assignment_question_id'] ?? null,
            'assessment_type'          => 'assignment',
            'event_type'               => $data['event_type'],
            'severity'                 => $this->severityFor($data['event_type']),
            'attempt_session_id'       => $data['attempt_session_id'] ?? null,
            'details'                  => $data['details'] ?? [],
            'occurred_at'              => $data['occurred_at'] ?? now(),
        ]);

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
}
