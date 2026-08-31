<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\ClassAssignment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class StudentNotificationService
{
    public function send(
        User|int $recipient,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $data = [],
        ?string $dedupeKey = null
    ): ?Notification {
        if (! Schema::hasTable('notifications')) {
            return null;
        }

        $userId = $recipient instanceof User ? (int) $recipient->id : (int) $recipient;
        if ($userId <= 0) {
            return null;
        }

        $type = Str::limit(trim($type), 191, '');
        $title = Str::limit(trim($title), 191, '');
        $message = trim($message);
        $normalizedUrl = $this->normalizeActionUrl($actionUrl);
        $userDedupeKey = $dedupeKey ? Str::limit($dedupeKey . ':user:' . $userId, 191, '') : null;

        try {
            return DB::transaction(function () use (
                $userId,
                $type,
                $title,
                $message,
                $normalizedUrl,
                $data,
                $userDedupeKey
            ): Notification {
                DB::table('users')->where('id', $userId)->lockForUpdate()->first();

                if ($userDedupeKey) {
                    $existing = Notification::query()
                        ->where('user_id', $userId)
                        ->where('dedupe_key', $userDedupeKey)
                        ->lockForUpdate()
                        ->first();

                    if ($existing) {
                        return $existing;
                    }
                }

                return Notification::create([
                    'user_id' => $userId,
                    'type' => $type,
                    'dedupe_key' => $userDedupeKey,
                    'title' => $title !== '' ? $title : 'Notification',
                    'notification_text' => $message,
                    'action_url' => $normalizedUrl,
                    'data' => $data !== [] ? $data : null,
                    'is_read' => false,
                    'read_at' => null,
                ]);
            }, 3);
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function sendToUsers(
        iterable $userIds,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $data = [],
        ?string $dedupeKey = null
    ): int {
        $sent = 0;

        foreach (collect($userIds)->map(fn ($id) => (int) $id)->filter()->unique() as $userId) {
            if ($this->send($userId, $type, $title, $message, $actionUrl, $data, $dedupeKey)) {
                $sent++;
            }
        }

        return $sent;
    }

    public function sendToClass(
        int $classId,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        array $data = [],
        ?string $dedupeKey = null
    ): int {
        $studentIds = DB::table('class_student')
            ->where('class_id', $classId)
            ->pluck('student_id');

        return $this->sendToUsers($studentIds, $type, $title, $message, $actionUrl, $data, $dedupeKey);
    }

    public function assignmentPublished(ClassAssignment $assignment): int
    {
        $assignment->loadMissing('classRoom');
        $dueText = $assignment->due_at
            ? ' Due ' . $assignment->due_at->format('M j, Y g:i A') . '.'
            : '';

        return $this->sendToClass(
            (int) $assignment->class_id,
            'assignment_published',
            'New assignment',
            '“' . $assignment->title . '” was posted in ' . ($assignment->classRoom?->name ?? 'your class') . '.' . $dueText,
            route('student.assignments.show', $assignment),
            ['assignment_id' => $assignment->id, 'class_id' => $assignment->class_id],
            'assignment-published:' . $assignment->id
        );
    }

    public function assignmentUpdated(ClassAssignment $assignment): int
    {
        $assignment->loadMissing('classRoom');
        $dueText = $assignment->due_at
            ? ' The due date is ' . $assignment->due_at->format('M j, Y g:i A') . '.'
            : '';

        return $this->sendToClass(
            (int) $assignment->class_id,
            'assignment_updated',
            'Assignment updated',
            '“' . $assignment->title . '” in ' . ($assignment->classRoom?->name ?? 'your class') . ' was updated.' . $dueText,
            route('student.assignments.show', $assignment),
            ['assignment_id' => $assignment->id, 'class_id' => $assignment->class_id],
            'assignment-updated:' . $assignment->id . ':' . optional($assignment->updated_at)->format('YmdHis')
        );
    }

    public function assignmentClosed(ClassAssignment $assignment): int
    {
        $assignment->loadMissing('classRoom');

        return $this->sendToClass(
            (int) $assignment->class_id,
            'assignment_closed',
            'Assignment closed',
            '“' . $assignment->title . '” in ' . ($assignment->classRoom?->name ?? 'your class') . ' is now closed.',
            route('student.assignments.show', $assignment),
            ['assignment_id' => $assignment->id, 'class_id' => $assignment->class_id],
            'assignment-closed:' . $assignment->id
        );
    }

    public function assessmentPublished(Assessment $assessment): int
    {
        $assessment->loadMissing('classRoom');
        $dueText = $assessment->due_at
            ? ' Due ' . $assessment->due_at->format('M j, Y g:i A') . '.'
            : '';

        return $this->sendToClass(
            (int) $assessment->class_id,
            'assessment_published',
            'New assessment',
            '“' . $assessment->title . '” was published in ' . ($assessment->classRoom?->name ?? 'your class') . '.' . $dueText,
            route('student.assessments.show', $assessment),
            ['assessment_id' => $assessment->id, 'class_id' => $assessment->class_id],
            'assessment-published:' . $assessment->id
        );
    }

    public function assessmentClosed(Assessment $assessment): int
    {
        $assessment->loadMissing('classRoom');

        return $this->sendToClass(
            (int) $assessment->class_id,
            'assessment_closed',
            'Assessment closed',
            '“' . $assessment->title . '” in ' . ($assessment->classRoom?->name ?? 'your class') . ' is now closed.',
            route('student.assessments.show', $assessment),
            ['assessment_id' => $assessment->id, 'class_id' => $assessment->class_id],
            'assessment-closed:' . $assessment->id
        );
    }

    public function syncDeadlineNotifications(User $user): void
    {
        if (! $user->isLearner() || ! Schema::hasTable('class_student')) {
            return;
        }

        $classIds = DB::table('class_student')
            ->where('student_id', $user->id)
            ->pluck('class_id');

        if ($classIds->isEmpty()) {
            return;
        }

        $now = now();
        $soon = $now->copy()->addHours(24);

        $assignments = ClassAssignment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->where('status', 'published')
            ->where(function ($query) use ($now): void {
                $query->whereNull('available_at')->orWhere('available_at', '<=', $now);
            })
            ->whereNotNull('due_at')
            ->where('due_at', '>=', $now->copy()->subDays(7))
            ->where('due_at', '<=', $soon)
            ->whereDoesntHave('submissions', fn ($query) => $query
                ->where('student_id', $user->id)
                ->whereIn('status', ['submitted', 'late', 'graded']))
            ->get();

        foreach ($assignments as $assignment) {
            $this->sendDeadlineNotification(
                $user,
                'assignment',
                (int) $assignment->id,
                (string) $assignment->title,
                $assignment->due_at,
                route('student.assignments.show', $assignment),
                $assignment->classRoom?->name
            );
        }

        $assessments = Assessment::query()
            ->with('classRoom:id,name')
            ->whereIn('class_id', $classIds)
            ->where('status', 'published')
            ->where(function ($query) use ($now): void {
                $query->whereNull('available_at')->orWhere('available_at', '<=', $now);
            })
            ->whereNotNull('due_at')
            ->where('due_at', '>=', $now->copy()->subDays(7))
            ->where('due_at', '<=', $soon)
            ->whereDoesntHave('submissions', fn ($query) => $query
                ->where('student_id', $user->id)
                ->whereIn('status', ['submitted', 'late', 'graded']))
            ->get();

        foreach ($assessments as $assessment) {
            $this->sendDeadlineNotification(
                $user,
                'assessment',
                (int) $assessment->id,
                (string) $assessment->title,
                $assessment->due_at,
                route('student.assessments.show', $assessment),
                $assessment->classRoom?->name
            );
        }
    }

    private function sendDeadlineNotification(
        User $user,
        string $kind,
        int $entityId,
        string $title,
        Carbon $dueAt,
        string $actionUrl,
        ?string $className
    ): void {
        $overdue = now()->greaterThan($dueAt);
        $type = $kind . ($overdue ? '_overdue' : '_due_soon');
        $heading = ucfirst($kind) . ($overdue ? ' overdue' : ' due soon');
        $timing = $overdue
            ? 'was due ' . $dueAt->diffForHumans()
            : 'is due ' . $dueAt->diffForHumans();
        $classText = $className ? ' in ' . $className : '';

        $this->send(
            $user,
            $type,
            $heading,
            '“' . $title . '”' . $classText . ' ' . $timing . '.',
            $actionUrl,
            [$kind . '_id' => $entityId, 'due_at' => $dueAt->toDateTimeString()],
            $type . ':' . $entityId . ':' . $dueAt->format('YmdHi')
        );
    }

    private function normalizeActionUrl(?string $actionUrl): ?string
    {
        if (! $actionUrl) {
            return null;
        }

        $actionUrl = trim($actionUrl);
        if ($actionUrl === '') {
            return null;
        }

        if (Str::startsWith($actionUrl, '/') && ! Str::startsWith($actionUrl, '//')) {
            return $actionUrl;
        }

        $parts = parse_url($actionUrl);
        if ($parts === false) {
            return null;
        }

        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        return Str::startsWith($path, '/') ? $path . $query . $fragment : null;
    }
}
