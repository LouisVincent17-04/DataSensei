<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Which of an instructor's classes may practice a challenge (DataSensei
 * Updates 9).
 *
 * Class Challenges used to be a second assignment system: a title, an
 * instructions box, an "available from" date, a due date and a draft /
 * published / closed status, listed next to real assignments. Assignments
 * are the graded class work; challenges are practice. So a challenge is now
 * simply shared with classes, or not. Nothing is due and nothing is graded.
 *
 * The class_challenge_assignments table is kept: a "published" row means the
 * class can practice the challenge, a "closed" row means it used to and the
 * students' attempts still count in the class reports.
 */
class ClassChallengePractice
{
    public const CATEGORY_SLUG = 'university-student';

    public function __construct(private readonly StudentNotificationService $notifications)
    {
    }

    /** The instructor's active classes, in the order they are listed. */
    public function classesFor(int $instructorId): Collection
    {
        return ClassRoom::query()
            ->where('instructor_id', $instructorId)
            ->active()
            ->orderBy('name')
            ->orderBy('section')
            ->get();
    }

    /**
     * A challenge this instructor may share: one they built (any
     * availability), or an available platform challenge on the University
     * Student level.
     */
    public function canShare(Challenge $challenge, int $instructorId): bool
    {
        if ($challenge->isInstructorOwned()) {
            return (int) $challenge->created_by === $instructorId;
        }

        $challenge->loadMissing('category');

        return (bool) $challenge->is_active
            && $challenge->category !== null
            && $challenge->category->slug === self::CATEGORY_SLUG;
    }

    /**
     * Ids of the instructor's classes that can practice the challenge now.
     *
     * @return array<int, int>
     */
    public function sharedClassIds(Challenge $challenge, int $instructorId): array
    {
        return ClassChallengeAssignment::query()
            ->where('challenge_id', $challenge->id)
            ->where('status', ClassChallengeAssignment::STATUS_PUBLISHED)
            ->whereIn('class_id', $this->classesFor($instructorId)->pluck('id'))
            ->pluck('class_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Share the challenge with exactly these classes of the instructor.
     * Classes that are not the instructor's, or archived, are ignored.
     *
     * @param  array<int, int|string>  $classIds
     * @return array{added: array<int, string>, removed: array<int, string>}
     */
    public function sync(Challenge $challenge, array $classIds, int $instructorId): array
    {
        $classes = $this->classesFor($instructorId)->keyBy('id');
        $wanted = collect($classIds)
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $classes->has($id))
            ->unique()
            ->values();

        $changes = DB::transaction(function () use ($challenge, $classes, $wanted, $instructorId): array {
            $rows = ClassChallengeAssignment::query()
                ->where('challenge_id', $challenge->id)
                ->whereIn('class_id', $classes->keys())
                ->lockForUpdate()
                ->get()
                ->groupBy('class_id');

            $added = [];
            $removed = [];

            foreach ($classes as $classId => $class) {
                $existing = $rows->get($classId, collect());
                $published = $existing->firstWhere('status', ClassChallengeAssignment::STATUS_PUBLISHED);

                if ($wanted->contains((int) $classId)) {
                    if ($published !== null) {
                        continue;
                    }

                    $row = $existing->first() ?? new ClassChallengeAssignment([
                        'class_id' => (int) $classId,
                        'challenge_id' => $challenge->id,
                    ]);
                    $row->fill([
                        'assigned_by' => $instructorId,
                        'title' => null,
                        'instructions' => null,
                        'available_at' => null,
                        'due_at' => null,
                        'status' => ClassChallengeAssignment::STATUS_PUBLISHED,
                    ])->save();
                    $added[(int) $classId] = $row;
                } elseif ($published !== null) {
                    // Kept as "closed": the class's attempts stay in its reports.
                    $existing->where('status', ClassChallengeAssignment::STATUS_PUBLISHED)
                        ->each(fn (ClassChallengeAssignment $row) => $row->update(['status' => ClassChallengeAssignment::STATUS_CLOSED]));
                    $removed[] = (int) $classId;
                }
            }

            return [$added, $removed];
        }, 3);

        [$added, $removed] = $changes;

        foreach ($added as $classId => $row) {
            $this->notifyClass($challenge, $classes->get($classId));
        }

        $name = fn (int $id): string => (string) $classes->get($id)?->name;

        return [
            'added' => array_map($name, array_keys($added)),
            'removed' => array_map($name, $removed),
        ];
    }

    /** Tell the class, with a link straight to the challenge. */
    private function notifyClass(Challenge $challenge, ?ClassRoom $class): void
    {
        $challenge->loadMissing('category');
        $slug = $challenge->category?->slug;

        if ($class === null || ! $slug) {
            return;
        }

        $url = $challenge->is_coding_challenge
            ? route('challenges.coding.quiz', ['slug' => $slug, 'challenge' => $challenge->id])
            : route('challenges.quiz', ['slug' => $slug, 'challenge' => $challenge->id]);

        try {
            $this->notifications->sendToClass(
                (int) $class->id,
                'class_challenge_published',
                $challenge->is_coding_challenge ? 'New coding challenge to practice' : 'New challenge to practice',
                '“'.$challenge->title.'” was shared with '.$class->name.' for practice.',
                $url,
                ['class_id' => (int) $class->id, 'challenge_id' => (int) $challenge->id],
                'class-challenge-shared:'.$class->id.':'.$challenge->id.':'.now()->format('YmdHi')
            );
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
