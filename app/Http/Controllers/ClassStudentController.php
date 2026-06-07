<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClassStudentController extends Controller
{
    /**
     * Check if the logged-in instructor owns/manages this class.
     */
    private function ensureCanManageClass(ClassRoom $class): void
    {
        abort_unless(Auth::check(), 403, 'You must be logged in.');

        abort_unless(
            (int) $class->instructor_id === (int) Auth::id(),
            403,
            'You are not allowed to manage this class.'
        );
    }

    /**
     * Get the institution that should own the student enrollment.
     *
     * Priority:
     * 1. class.institution_id, if your classes table has it
     * 2. instructor.institution_id
     */
    private function resolveInstitutionIdForEnrollment(ClassRoom $class): ?int
    {
        $classInstitutionId = $class->institution_id ?? null;
        $instructorInstitutionId = Auth::user()->institution_id ?? null;

        return $classInstitutionId ?: $instructorInstitutionId;
    }

    public function index(Request $request, ClassRoom $class)
    {
        $this->ensureCanManageClass($class);

        $tab = $request->input('tab', 'enrolled');
        $search = $request->input('search');

        $base = $class->students()
            ->where('role', 1)
            ->with('institution');

        if ($search) {
            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = (clone $base)
            ->paginate(20)
            ->withQueryString();

        return view('instructor.classes.students', [
            'class' => $class,
            'students' => $students,
            'tab' => $tab,
            'enrolledCount' => $class->students()->where('role', 1)->count(),
            'pendingCount' => 0,
            'avgXp' => (int) $class->students()->where('role', 1)->avg('xp'),
        ]);
    }

    /**
     * Add a student to the class by Gmail/email.
     *
     * This is now the only safe way to enroll students into an institution/class.
     * Students can no longer self-enroll by sharing an institution code.
     */
    public function addByEmail(Request $request, ClassRoom $class)
    {
        $this->ensureCanManageClass($class);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = strtolower(trim($validated['email']));

        $student = User::whereRaw('LOWER(email) = ?', [$email])
            ->where('role', 1)
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'email' => 'No student account was found with that email. The user must register as a student first.',
            ]);
        }

        $institutionId = $this->resolveInstitutionIdForEnrollment($class);

        if (!$institutionId) {
            throw ValidationException::withMessages([
                'email' => 'Cannot add this student because this instructor/class has no institution assigned.',
            ]);
        }

        /*
         * Do not lock/block a student just because they already have institution_id.
         * If empty, assign the instructor/class institution.
         * If already set, keep it as-is. This prevents unwanted overwrites while still allowing class enrollment.
         */
        if (empty($student->institution_id)) {
            $student->institution_id = $institutionId;
            $student->save();
        }

        $alreadyEnrolled = $class->students()
            ->where('users.id', $student->id)
            ->exists();

        if ($alreadyEnrolled) {
            return back()->with('error', "{$student->name} is already in this class.");
        }

        $pivotData = [];

        /*
         * If your class_student pivot table has enrolled_at, keep this.
         * If it does not, remove this line.
         */
        $pivotData['enrolled_at'] = now();

        $class->students()->attach($student->id, $pivotData);

        return back()->with('success', "{$student->name} ({$student->email}) has been added to {$class->name}.");
    }

    public function approve(ClassRoom $class, User $student)
    {
        $this->ensureCanManageClass($class);

        return back()->with('success', "{$student->name} has been approved.");
    }

    public function approveBulk(Request $request, ClassRoom $class)
    {
        $this->ensureCanManageClass($class);

        $ids = collect($request->input('student_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->toArray();

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        return back()->with('success', count($ids) . ' student(s) approved.');
    }

    public function remove(ClassRoom $class, User $student)
    {
        $this->ensureCanManageClass($class);

        $class->students()->detach($student->id);

        return back()->with('success', "{$student->name} has been removed from the class.");
    }

    public function removeBulk(Request $request, ClassRoom $class)
    {
        $this->ensureCanManageClass($class);

        $ids = collect($request->input('student_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->toArray();

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        $class->students()->detach($ids);

        return back()->with('success', count($ids) . ' student(s) removed.');
    }
}
