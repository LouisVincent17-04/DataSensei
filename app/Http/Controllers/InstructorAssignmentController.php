<?php

namespace App\Http\Controllers;

use App\Models\AssignmentLibraryItem;
use App\Models\ClassAssignment;
use App\Models\ClassRoom;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstructorAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->orderBy('name')
            ->get();

        $classIds = $classes->pluck('id');

        $query = ClassAssignment::with([
                'classRoom' => fn ($q) => $q->withCount('students'),
                'libraryItem',
            ])
            ->withCount([
                'submissions',
                'submissions as submitted_students_count' => fn ($q) => $q
                    ->whereIn('status', ['submitted', 'late', 'graded'])
                    ->select(DB::raw('COUNT(DISTINCT student_id)')),
            ])
            ->whereIn('class_id', $classIds)
            ->latest();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('libraryItem', function ($itemQuery) use ($search) {
                      $itemQuery->where('topic_title', 'like', "%{$search}%")
                          ->orWhere('version_name', 'like', "%{$search}%")
                          ->orWhere('assignment_code', 'like', "%{$search}%");
                  });
            });
        }

        $assignments = $query->paginate(10)->withQueryString();

        $allAssignments = ClassAssignment::whereIn('class_id', $classIds);
        $stats = [
            'total' => (clone $allAssignments)->count(),
            'published' => (clone $allAssignments)->where('status', 'published')->count(),
            'draft' => (clone $allAssignments)->where('status', 'draft')->count(),
            'closed' => (clone $allAssignments)->where('status', 'closed')->count(),
        ];

        return view('instructor.assignments.index', compact('assignments', 'classes', 'stats'));
    }

    public function create()
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $libraryItems = AssignmentLibraryItem::active()
            ->withCount('questions')
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get();

        return view('instructor.assignments.create', compact('classes', 'libraryItems'));
    }

    public function store(Request $request, StudentNotificationService $notifications)
    {
        $validated = $this->validatedAssignmentData($request);
        $status = $validated['status'] ?? 'draft';

        $assignment = DB::transaction(function () use ($validated, $status): ClassAssignment {
            $class = ClassRoom::query()
                ->whereKey($validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where('is_archived', false)
                ->lockForUpdate()
                ->firstOrFail();

            $libraryItem = AssignmentLibraryItem::active()
                ->whereKey($validated['assignment_library_item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($status === 'published' && ! $libraryItem->questions()->exists()) {
                throw ValidationException::withMessages([
                    'assignment_library_item_id' => 'This assignment has no questions and cannot be published.',
                ]);
            }

            return ClassAssignment::create([
                'class_id' => $class->id,
                'assignment_library_item_id' => $libraryItem->id,
                'assigned_by' => Auth::id(),
                'title' => $validated['title'] ?: $libraryItem->title,
                'instructions' => $validated['instructions'] ?? $libraryItem->instructions,
                'available_at' => $validated['available_at'] ?? null,
                'due_at' => $validated['due_at'] ?? null,
                'max_attempts' => $validated['max_attempts'] ?? 1,
                'status' => $status,
                'assigned_at' => $status === 'published' ? now() : null,
            ]);
        }, 3);

        if ($assignment->status === 'published') {
            $notifications->assignmentPublished($assignment->fresh());
        }

        return redirect()
            ->route('instructor.assignments.show', $assignment)
            ->with('success', 'Assignment created successfully.');
    }

    public function show(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->load([
            'classRoom.students',
            'libraryItem.questions.options',
            'libraryItem.questions.blankAnswers',
            'submissions.student',
            'submissions.answers',
        ]);

        $studentCount = $assignment->classRoom->students->count();
        $submittedCount = $assignment->submissions
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->pluck('student_id')
            ->unique()
            ->count();

        return view('instructor.assignments.show', compact('assignment', 'studentCount', 'submittedCount'));
    }

    public function edit(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);
        abort_if($assignment->status === 'archived', 422, 'Archived assignments are read-only.');

        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $libraryItems = AssignmentLibraryItem::active()
            ->withCount('questions')
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get();

        $classAssignment = $assignment;

        return view('instructor.assignments.create', compact('classes', 'libraryItems', 'classAssignment'));
    }

    public function update(Request $request, ClassAssignment $assignment, StudentNotificationService $notifications)
    {
        $this->authorizeAssignment($assignment);

        $validated = $this->validatedAssignmentData($request, $assignment);

        DB::transaction(function () use ($assignment, $validated): void {
            $lockedAssignment = ClassAssignment::query()
                ->whereKey($assignment->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_if($lockedAssignment->status === 'archived', 422, 'Archived assignments are read-only.');

            $classIds = collect([$lockedAssignment->class_id, $validated['class_id']])
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values();

            $classes = ClassRoom::query()
                ->whereIn('id', $classIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $currentClass = $classes->get((int) $lockedAssignment->class_id);
            abort_unless(
                $currentClass && (int) $currentClass->instructor_id === (int) Auth::id(),
                403,
                'You are not allowed to manage this assignment.'
            );

            $targetClass = $classes->get((int) $validated['class_id']);
            abort_unless(
                $targetClass
                    && (int) $targetClass->instructor_id === (int) Auth::id()
                    && ! $targetClass->is_archived,
                404
            );

            $libraryItem = AssignmentLibraryItem::active()
                ->whereKey($validated['assignment_library_item_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedAssignment->submissions()->exists()) {
                if ((int) $validated['class_id'] !== (int) $lockedAssignment->class_id
                    || (int) $validated['assignment_library_item_id'] !== (int) $lockedAssignment->assignment_library_item_id) {
                    throw ValidationException::withMessages([
                        'assignment_library_item_id' => 'The class and question source cannot change after a student starts an attempt.',
                    ]);
                }

                $highestAttempt = (int) $lockedAssignment->submissions()->max('attempt_no');
                if ((int) $validated['max_attempts'] < $highestAttempt) {
                    throw ValidationException::withMessages([
                        'max_attempts' => "Maximum attempts cannot be lower than existing attempt {$highestAttempt}.",
                    ]);
                }
            }

            $lockedAssignment->update([
                'class_id' => $targetClass->id,
                'assignment_library_item_id' => $libraryItem->id,
                'title' => $validated['title'] ?: $libraryItem->title,
                'instructions' => $validated['instructions'] ?? null,
                'available_at' => $validated['available_at'] ?? null,
                'due_at' => $validated['due_at'] ?? null,
                'max_attempts' => $validated['max_attempts'] ?? 1,
                'status' => $lockedAssignment->status,
                'assigned_at' => $lockedAssignment->assigned_at,
            ]);
        }, 3);

        $updatedAssignment = $assignment->fresh();
        if ($updatedAssignment?->status === 'published') {
            $notifications->assignmentUpdated($updatedAssignment);
        }

        return redirect()
            ->route('instructor.assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    public function publish(ClassAssignment $assignment, StudentNotificationService $notifications)
    {
        $this->authorizeAssignment($assignment);

        DB::transaction(function () use ($assignment): void {
            $lockedAssignment = $this->lockOwnedAssignment($assignment);
            abort_unless($lockedAssignment->status === 'draft', 422, 'Only a draft assignment can be published.');
            abort_if($lockedAssignment->classRoom->is_archived, 422, 'Assignments cannot be published to an archived class.');

            $libraryItem = AssignmentLibraryItem::active()
                ->whereKey($lockedAssignment->assignment_library_item_id)
                ->lockForUpdate()
                ->first();
            abort_unless($libraryItem?->questions()->exists(), 422, 'This assignment has no active question source or questions.');

            $lockedAssignment->update([
                'status' => 'published',
                'assigned_at' => $lockedAssignment->assigned_at ?: now(),
            ]);
        }, 3);

        $notifications->assignmentPublished($assignment->fresh());

        return back()->with('success', 'Assignment published successfully.');
    }

    public function close(ClassAssignment $assignment, StudentNotificationService $notifications)
    {
        $this->authorizeAssignment($assignment);

        DB::transaction(function () use ($assignment): void {
            $lockedAssignment = $this->lockOwnedAssignment($assignment);
            abort_unless($lockedAssignment->status === 'published', 422, 'Only a published assignment can be closed.');
            $lockedAssignment->update(['status' => 'closed']);
        }, 3);

        $notifications->assignmentClosed($assignment->fresh());

        return back()->with('success', 'Assignment closed successfully.');
    }

    public function archive(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        DB::transaction(function () use ($assignment): void {
            $lockedAssignment = $this->lockOwnedAssignment($assignment);
            abort_unless(in_array($lockedAssignment->status, ['draft', 'closed'], true), 422, 'Close a published assignment before archiving it.');
            abort_if(
                $lockedAssignment->submissions()->where('status', 'in_progress')->exists(),
                422,
                'This assignment still has in-progress attempts and cannot be archived.'
            );

            $lockedAssignment->update(['status' => 'archived']);
        }, 3);

        return redirect()
            ->route('instructor.assignments.index')
            ->with('success', 'Assignment archived successfully.');
    }

    public function destroy(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $deleted = DB::transaction(function () use ($assignment): bool {
            $lockedAssignment = $this->lockOwnedAssignment($assignment);

            if ($lockedAssignment->submissions()->exists()) {
                return false;
            }

            $lockedAssignment->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return redirect()
                ->route('instructor.assignments.index')
                ->with('error', 'This assignment cannot be deleted because students have already started or submitted it. Close the assignment instead to preserve student records.');
        }

        return redirect()
            ->route('instructor.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    private function validatedAssignmentData(Request $request, ?ClassAssignment $assignment = null): array
    {
        $allowedStatuses = $assignment ? [$assignment->status] : ['draft', 'published'];

        return $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'assignment_library_item_id' => ['required', 'integer', 'exists:assignment_library_items,id'],
            'title' => ['nullable', 'string', 'max:189'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'available_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:available_at'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'status' => ['required', Rule::in($allowedStatuses)],
        ]);
    }

    private function authorizeAssignment(ClassAssignment $assignment): void
    {
        $assignment->loadMissing('classRoom');

        abort_unless(
            $assignment->classRoom && (int) $assignment->classRoom->instructor_id === (int) Auth::id(),
            403,
            'You are not allowed to manage this assignment.'
        );
    }

    private function lockOwnedAssignment(ClassAssignment $assignment): ClassAssignment
    {
        $lockedAssignment = ClassAssignment::query()
            ->whereKey($assignment->id)
            ->lockForUpdate()
            ->firstOrFail();

        $class = ClassRoom::query()
            ->whereKey($lockedAssignment->class_id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            (int) $class->instructor_id === (int) Auth::id(),
            403,
            'You are not allowed to manage this assignment.'
        );

        return $lockedAssignment->setRelation('classRoom', $class);
    }
}
