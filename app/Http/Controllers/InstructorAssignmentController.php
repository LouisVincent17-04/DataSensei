<?php

namespace App\Http\Controllers;

use App\Models\AssignmentLibraryItem;
use App\Models\ClassAssignment;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InstructorAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $classIds = $classes->pluck('id');

        $query = ClassAssignment::with(['classRoom', 'libraryItem', 'submissions'])
            ->withCount('submissions')
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

    public function store(Request $request)
    {
        $validated = $this->validatedAssignmentData($request);

        $class = ClassRoom::where('id', $validated['class_id'])
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        $libraryItem = AssignmentLibraryItem::active()
            ->where('id', $validated['assignment_library_item_id'])
            ->firstOrFail();

        $status = $validated['status'] ?? 'draft';

        $assignment = ClassAssignment::create([
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

    public function update(Request $request, ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $validated = $this->validatedAssignmentData($request, $assignment);

        ClassRoom::where('id', $validated['class_id'])
            ->where('instructor_id', Auth::id())
            ->firstOrFail();

        AssignmentLibraryItem::active()
            ->where('id', $validated['assignment_library_item_id'])
            ->firstOrFail();

        $wasNotPublished = $assignment->status !== 'published';
        $newStatus = $validated['status'] ?? $assignment->status;

        $assignment->update([
            'class_id' => $validated['class_id'],
            'assignment_library_item_id' => $validated['assignment_library_item_id'],
            'title' => $validated['title'] ?: AssignmentLibraryItem::find($validated['assignment_library_item_id'])->title,
            'instructions' => $validated['instructions'] ?? null,
            'available_at' => $validated['available_at'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
            'max_attempts' => $validated['max_attempts'] ?? 1,
            'status' => $newStatus,
            'assigned_at' => ($newStatus === 'published' && $wasNotPublished) ? now() : $assignment->assigned_at,
        ]);

        return redirect()
            ->route('instructor.assignments.show', $assignment)
            ->with('success', 'Assignment updated successfully.');
    }

    public function publish(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->update([
            'status' => 'published',
            'assigned_at' => $assignment->assigned_at ?: now(),
        ]);

        return back()->with('success', 'Assignment published successfully.');
    }

    public function close(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->update(['status' => 'closed']);

        return back()->with('success', 'Assignment closed successfully.');
    }

    public function archive(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        $assignment->update(['status' => 'archived']);

        return redirect()
            ->route('instructor.assignments.index')
            ->with('success', 'Assignment archived successfully.');
    }

    public function destroy(ClassAssignment $assignment)
    {
        $this->authorizeAssignment($assignment);

        if ($assignment->submissions()->exists()) {
            return redirect()
                ->route('instructor.assignments.index')
                ->with('error', 'This assignment cannot be deleted because students have already started or submitted it. Close the assignment instead to preserve student records.');
        }

        $assignment->delete();

        return redirect()
            ->route('instructor.assignments.index')
            ->with('success', 'Assignment deleted successfully.');
    }

    private function validatedAssignmentData(Request $request, ?ClassAssignment $assignment = null): array
    {
        return $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'assignment_library_item_id' => ['required', 'integer', 'exists:assignment_library_items,id'],
            'title' => ['nullable', 'string', 'max:191'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'available_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:available_at'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'status' => ['required', Rule::in(['draft', 'published', 'closed'])],
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
}
