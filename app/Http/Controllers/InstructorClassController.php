<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorClassController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $instructor = Auth::user();
        $showArchived = $request->boolean('archived');

        // Single Eloquent query — withCount drives $class->students_count in the blade
        $query = ClassRoom::forInstructor($instructor->id)
            ->withCount('students');

        if ($showArchived) {
            $query->archived();
        } else {
            $query->active();
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhere('subject_code', 'like', "%{$search}%")
                  ->orWhere('class_code', 'like', "%{$search}%");
            });
        }

        $classes = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats strip: counts of classes (not students), scoped to this instructor
        $totalActive   = ClassRoom::forInstructor($instructor->id)->active()->count();
        $totalArchived = ClassRoom::forInstructor($instructor->id)->archived()->count();

        // Total students across the classes on the current page (single integer for the strip card)
        $studentCounts = DB::table('class_student')
            ->whereIn('class_id', $classes->pluck('id'))
            ->count();

        return view('instructor.classes.classes', compact(
            'classes', 'showArchived', 'totalActive', 'totalArchived', 'studentCounts'
        ));
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create()
    {
        return view('instructor.classes.create_classes');
    }

    // ─── Store ───────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:191'],
            'section'           => ['nullable', 'string', 'max:191'],
            'subject_code'      => ['nullable', 'string', 'max:50'],
            'term'              => ['nullable', 'string', 'max:100'],
            'academic_year'     => ['nullable', 'string', 'max:20'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'max_students'      => ['nullable', 'integer', 'min:1', 'max:1000'],
            'allow_self_enroll' => ['boolean'],
        ]);

        $validated['instructor_id']     = Auth::id();
        $validated['institution_id']    = Auth::user()->institution_id ?? null;
        $validated['allow_self_enroll'] = $request->boolean('allow_self_enroll');

        $class = ClassRoom::create($validated);

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', "Class \"{$class->name}\" created successfully! Code: {$class->class_code}");
    }

    // ─── Students ────────────────────────────────────────────────────────────

    public function students(Request $request, ClassRoom $class)
    {
        $this->authorizeClass($class);

        $search = $request->input('search');
        $tab    = $request->input('tab', 'enrolled'); // 'enrolled' | 'pending'

        // Base: only role=1 (students), belonging to this class
        $base = $class->students()
                      ->where('role', 1)
                      ->with('institution');

        if ($search) {
            $base->where(function ($q) use ($search) {
                $q->where('users.name',  'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        // When you add an `approved` boolean to class_student, split here:
        // enrolled → ->wherePivot('approved', true)
        // pending  → ->wherePivot('approved', false)

        $students      = (clone $base)->paginate(20)->withQueryString();
        $enrolledCount = $class->students()->where('role', 1)->count();
        $pendingCount  = 0; // update once pivot.approved column exists
        $avgXp         = (int) $class->students()->where('role', 1)->avg('xp');

        return view('instructor.classes.students', compact(
            'class', 'students', 'tab', 'enrolledCount', 'pendingCount', 'avgXp'
        ));
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function show(ClassRoom $class)
    {
        $this->authorizeClass($class);

        // No dedicated show blade exists yet — redirect to index.
        // When you create instructor.class_show.blade.php, replace this with:
        // return view('instructor.class_show', compact('class', ...));
        return redirect()->route('instructor.classes.index');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit(ClassRoom $class)
    {
        $this->authorizeClass($class);

        // create_classes blade handles both create & edit via isset($class)
        return view('instructor.classes.create_classes', compact('class'));
    }

    // ─── Update ──────────────────────────────────────────────────────────────

    public function update(Request $request, ClassRoom $class)
    {
        $this->authorizeClass($class);

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:191'],
            'section'           => ['nullable', 'string', 'max:191'],
            'subject_code'      => ['nullable', 'string', 'max:50'],
            'term'              => ['nullable', 'string', 'max:100'],
            'academic_year'     => ['nullable', 'string', 'max:20'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'max_students'      => ['nullable', 'integer', 'min:1', 'max:1000'],
            'allow_self_enroll' => ['boolean'],
        ]);

        $validated['allow_self_enroll'] = $request->boolean('allow_self_enroll');

        $class->update($validated);

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    // ─── Archive / Restore ───────────────────────────────────────────────────

    public function archive(ClassRoom $class)
    {
        $this->authorizeClass($class);

        $class->update(['is_archived' => true]);

        return back()->with('success', "Class \"{$class->name}\" has been archived.");
    }

    public function restore(ClassRoom $class)
    {
        $this->authorizeClass($class);

        $class->update(['is_archived' => false]);

        return back()->with('success', "Class \"{$class->name}\" has been restored.");
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(ClassRoom $class)
    {
        $this->authorizeClass($class);

        $name = $class->name;
        $class->delete();

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', "Class \"{$name}\" has been permanently deleted.");
    }

    // ─── Regenerate Code ─────────────────────────────────────────────────────

    public function regenerateCode(ClassRoom $class)
    {
        $this->authorizeClass($class);

        $class->update(['class_code' => ClassRoom::generateUniqueCode()]);

        return back()->with('success', "New class code generated: {$class->class_code}");
    }

    // ─── Remove Student ──────────────────────────────────────────────────────

    public function removeStudent(ClassRoom $class, User $student)
    {
        $this->authorizeClass($class);

        $class->students()->detach($student->id);

        return back()->with('success', "{$student->name} has been removed from the class.");
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function authorizeClass(ClassRoom $class): void
    {
        if ($class->instructor_id !== Auth::id()) {
            abort(403, 'You do not have permission to manage this class.');
        }
    }
}