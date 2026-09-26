<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ClassChallengeAssignment;
use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * "Give a challenge to a class": a class_challenge_assignments row makes one
 * challenge (the instructor's own, or an active platform challenge from the
 * University Student pool) visible to one class while it is published and
 * inside its window. Students take it on the University Student map.
 */
class InstructorClassChallengeController extends Controller
{
    public const CATEGORY_SLUG = InstructorChallengeBuilderController::CATEGORY_SLUG;

    public function index(Request $request): View
    {
        $classes = ClassRoom::query()
            ->where('instructor_id', (int) Auth::id())
            ->active()
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        // ClassRoom has no relation to these rows, so they are loaded once and
        // grouped by class here.
        $assignmentsByClass = ClassChallengeAssignment::query()
            ->whereIn('class_id', $classes->pluck('id'))
            ->with('challenge')
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('class_id');

        return view('instructor.class-challenges.index', [
            'classes' => $classes,
            'assignmentsByClass' => $assignmentsByClass,
            'ownChallenges' => $this->ownChallenges(),
            'poolChallenges' => $this->poolChallenges(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        $assignment = DB::transaction(function () use ($data): ClassChallengeAssignment {
            $class = ClassRoom::query()
                ->whereKey((int) $data['class_id'])
                ->where('instructor_id', (int) Auth::id())
                ->active()
                ->lockForUpdate()
                ->firstOrFail();

            $challenge = $this->assignableChallenge((int) $data['challenge_id']);

            if ($challenge === null) {
                throw ValidationException::withMessages([
                    'challenge_id' => 'Choose one of your own challenges or an available University Student challenge from the pool.',
                ]);
            }

            $duplicate = ClassChallengeAssignment::query()
                ->where('class_id', $class->id)
                ->where('challenge_id', $challenge->id)
                ->exists();

            if ($duplicate) {
                throw ValidationException::withMessages([
                    'challenge_id' => 'This challenge is already given to that class. Edit the existing entry instead.',
                ]);
            }

            return ClassChallengeAssignment::create([
                'class_id' => $class->id,
                'challenge_id' => $challenge->id,
                'assigned_by' => (int) Auth::id(),
                'title' => $this->nullableText($data['title'] ?? null) ?? $challenge->title,
                'instructions' => $this->nullableText($data['instructions'] ?? null),
                'available_at' => $data['available_at'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                'status' => $data['status'],
            ]);
        }, 3);

        return redirect()
            ->route('instructor.class-challenges.index')
            ->with('success', '"' . $assignment->title . '" given to the class'
                . ($assignment->status === ClassChallengeAssignment::STATUS_PUBLISHED ? '.' : ' as a ' . $assignment->status . ' entry; students see it once it is published.'));
    }

    public function update(Request $request, ClassChallengeAssignment $assignment): RedirectResponse
    {
        $this->ensureOwned($assignment);
        $data = $this->validatedData($request, $assignment);

        DB::transaction(function () use ($assignment, $data): void {
            $locked = ClassChallengeAssignment::query()->whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            $this->ensureOwned($locked);

            $locked->update([
                'title' => $this->nullableText($data['title'] ?? null) ?? $locked->title ?? $locked->challenge?->title,
                'instructions' => $this->nullableText($data['instructions'] ?? null),
                'available_at' => $data['available_at'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                'status' => $data['status'],
            ]);
        }, 3);

        return redirect()
            ->route('instructor.class-challenges.index')
            ->with('success', 'Class challenge updated.');
    }

    public function destroy(Request $request, ClassChallengeAssignment $assignment): RedirectResponse
    {
        $this->ensureOwned($assignment);

        DB::transaction(function () use ($assignment): void {
            $locked = ClassChallengeAssignment::query()->whereKey($assignment->id)->lockForUpdate()->firstOrFail();
            $this->ensureOwned($locked);
            $locked->delete();
        }, 3);

        return redirect()
            ->route('instructor.class-challenges.index')
            ->with('success', 'The challenge was removed from the class. Attempts students already made are kept.');
    }

    // ─────────────────────────────────────────────────────────────────────

    private function validatedData(Request $request, ?ClassChallengeAssignment $assignment = null): array
    {
        $rules = [
            'title' => ['nullable', 'string', 'max:189'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'available_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:available_at'],
            'status' => ['required', Rule::in(ClassChallengeAssignment::STATUSES)],
        ];

        if ($assignment === null) {
            $rules['class_id'] = ['required', 'integer', 'exists:classes,id'];
            $rules['challenge_id'] = ['required', 'integer', 'exists:challenges,id'];
        }

        return $request->validate($rules, [
            'due_at.after_or_equal' => 'The due date must be on or after the available date.',
        ]);
    }

    /** The assignment's class must belong to the signed-in instructor. */
    private function ensureOwned(ClassChallengeAssignment $assignment): void
    {
        $assignment->loadMissing('class');

        abort_unless(
            $assignment->class && (int) $assignment->class->instructor_id === (int) Auth::id(),
            404
        );
    }

    /**
     * A challenge this instructor may give to a class: one they built
     * themselves (any availability), or an available platform challenge on
     * the University Student level. Nothing else, whatever its id.
     */
    private function assignableChallenge(int $challengeId): ?Challenge
    {
        $challenge = Challenge::query()->with('category')->find($challengeId);

        if ($challenge === null) {
            return null;
        }

        if ($challenge->isInstructorOwned()) {
            return (int) $challenge->created_by === (int) Auth::id() ? $challenge : null;
        }

        $isPool = (bool) $challenge->is_active
            && $challenge->category
            && $challenge->category->slug === self::CATEGORY_SLUG;

        return $isPool ? $challenge : null;
    }

    private function ownChallenges()
    {
        return Challenge::query()
            ->where('visibility', Challenge::VISIBILITY_INSTRUCTOR)
            ->where('created_by', (int) Auth::id())
            ->orderBy('is_coding_challenge')
            ->orderBy('title')
            ->get();
    }

    private function poolChallenges()
    {
        $category = ChallengeCategory::query()->where('slug', self::CATEGORY_SLUG)->first();

        if ($category === null) {
            return collect();
        }

        return Challenge::query()
            ->platform()
            ->where('challenge_category_id', $category->id)
            ->where('is_active', true)
            ->orderBy('is_coding_challenge')
            ->orderBy('order_index')
            ->orderBy('title')
            ->get();
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
