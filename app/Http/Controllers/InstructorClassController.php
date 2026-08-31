<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            'name'              => ['required', 'string', 'max:189'],
            'section'           => ['nullable', 'string', 'max:189'],
            'subject_code'      => ['nullable', 'string', 'max:50'],
            'term'              => ['nullable', 'string', 'max:100'],
            'academic_year'     => ['nullable', 'string', 'max:20'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'max_students'      => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $class = DB::transaction(function () use ($validated): ClassRoom {
            $instructor = User::query()
                ->whereKey(Auth::id())
                ->where('role', User::ROLE_INSTRUCTOR)
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            $institution = Institution::query()
                ->whereKey($instructor->institution_id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            return ClassRoom::create([
                ...$validated,
                'instructor_id' => $instructor->id,
                'institution_id' => $institution->id,
                'allow_self_enroll' => false,
            ]);
        }, 3);

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', "Class \"{$class->name}\" created successfully! Code: {$class->class_code}");
    }

    // ─── Show ────────────────────────────────────────────────────────────────

    public function show(ClassRoom $class)
    {
        $this->authorizeClass($class);

        // No dedicated show blade exists yet, so this safely redirects to the class list.
        // Add a dedicated class show blade later if you need a separate class detail page.
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
            'name'              => ['required', 'string', 'max:189'],
            'section'           => ['nullable', 'string', 'max:189'],
            'subject_code'      => ['nullable', 'string', 'max:50'],
            'term'              => ['nullable', 'string', 'max:100'],
            'academic_year'     => ['nullable', 'string', 'max:20'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'max_students'      => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($class, $validated): array {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->authorizeClass($lockedClass);
            $enrolledCount = $lockedClass->students()->count();

            if (($validated['max_students'] ?? null) !== null
                && (int) $validated['max_students'] < $enrolledCount) {
                return ['updated' => false, 'enrolled_count' => $enrolledCount];
            }

            $payload = $validated;
            $payload['allow_self_enroll'] = false;
            $lockedClass->update($payload);

            return ['updated' => true, 'enrolled_count' => $enrolledCount];
        }, 3);

        if (! $result['updated']) {
            return back()
                ->withErrors(['max_students' => "Capacity cannot be lower than the {$result['enrolled_count']} currently enrolled students."])
                ->withInput();
        }

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    // ─── Archive / Restore ───────────────────────────────────────────────────

    public function archive(ClassRoom $class)
    {
        $this->authorizeClass($class);

        DB::transaction(function () use ($class): void {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->authorizeClass($lockedClass);
            $lockedClass->update(['is_archived' => true]);

            // Archiving stops new starts but keeps current attempts reviewable
            // and submittable through their existing closed records.
            $lockedClass->assignmentPosts()->where('status', 'published')->update(['status' => 'closed']);

            if (Schema::hasTable('assessments')) {
                DB::table('assessments')
                    ->where('class_id', $lockedClass->id)
                    ->where('status', 'published')
                    ->update(['status' => 'closed', 'updated_at' => now()]);
            }
        }, 3);

        return back()->with('success', "Class \"{$class->name}\" has been archived.");
    }

    public function restore(ClassRoom $class)
    {
        $this->authorizeClass($class);

        DB::transaction(function () use ($class): void {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->authorizeClass($lockedClass);
            $lockedClass->update(['is_archived' => false]);
        }, 3);

        return back()->with('success', "Class \"{$class->name}\" has been restored.");
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(ClassRoom $class)
    {
        $this->authorizeClass($class);
        $result = DB::transaction(function () use ($class): array {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->authorizeClass($lockedClass);
            $blockingReasons = [];

            if ($lockedClass->students()->exists()) {
                $blockingReasons[] = 'it still has enrolled students';
            }
            if ($lockedClass->assignedModules()->exists()) {
                $blockingReasons[] = 'it still has assigned modules';
            }
            if ($lockedClass->assignmentPosts()->exists()) {
                $blockingReasons[] = 'it still has class assignments';
            }

            $hasAssignmentSubmissions = DB::table('assignment_submissions')
                ->join('class_assignments', 'class_assignments.id', '=', 'assignment_submissions.class_assignment_id')
                ->where('class_assignments.class_id', $lockedClass->id)
                ->exists();
            if ($hasAssignmentSubmissions) {
                $blockingReasons[] = 'students already have assignment submissions';
            }

            if (DB::table('anti_cheat_events')->where('class_id', $lockedClass->id)->exists()) {
                $blockingReasons[] = 'it has anti-cheat event records';
            }

            foreach ([
                'table_of_specifications' => 'tables of specification',
                'assessments' => 'assessments',
                'student_ilo_masteries' => 'ILO mastery records',
                'student_performance_snapshots' => 'performance snapshots',
                'student_performance_clusters' => 'performance cluster records',
            ] as $table => $label) {
                if (Schema::hasTable($table) && DB::table($table)->where('class_id', $lockedClass->id)->exists()) {
                    $blockingReasons[] = "it has {$label}";
                }
            }

            if ($blockingReasons !== []) {
                return ['deleted' => false, 'reasons' => $blockingReasons, 'name' => $lockedClass->name];
            }

            $name = $lockedClass->name;
            $lockedClass->delete();

            return ['deleted' => true, 'reasons' => [], 'name' => $name];
        }, 3);

        if (! $result['deleted']) {
            return redirect()
                ->route('instructor.classes.index')
                ->with('error', 'This class cannot be permanently deleted because ' . implode(', ', $result['reasons']) . '. Archive the class instead to preserve records.');
        }

        return redirect()
            ->route('instructor.classes.index')
            ->with('success', "Class \"{$result['name']}\" has been permanently deleted.");
    }

    // ─── Regenerate Code ─────────────────────────────────────────────────────

    public function regenerateCode(ClassRoom $class)
    {
        $this->authorizeClass($class);

        $newCode = DB::transaction(function () use ($class): string {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->authorizeClass($lockedClass);
            $lockedClass->update(['class_code' => ClassRoom::generateUniqueCode()]);

            return $lockedClass->class_code;
        }, 3);

        return back()->with('success', "New class code generated: {$newCode}");
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function authorizeClass(ClassRoom $class): void
    {
        if ($class->instructor_id !== Auth::id()) {
            abort(403, 'You do not have permission to manage this class.');
        }
    }
}
