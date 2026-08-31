<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\User;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        if ($class->institution_id !== null) {
            abort_unless(
                (int) $class->institution_id === (int) Auth::user()->institution_id,
                403,
                'This class is not part of your active institution.'
            );
        }
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
            'enrolledCount' => $class->students()->where('role', 1)->count(),
            'avgXp' => (int) $class->students()->where('role', 1)->avg('xp'),
        ]);
    }

    /**
     * Add a student to the class by Gmail/email.
     *
     * This is now the only safe way to enroll students into an institution/class.
     * Students can no longer self-enroll by sharing an institution code.
     */
    public function addByEmail(Request $request, ClassRoom $class, StudentNotificationService $notifications)
    {
        $this->ensureCanManageClass($class);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:191'],
        ]);

        $email = strtolower(trim($validated['email']));

        $student = User::whereRaw('LOWER(email) = ?', [$email])
            ->where('role', 1)
            ->where('status', 'active')
            ->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'email' => 'No student account was found with that email. The user must register as a student first.',
            ]);
        }

        $result = DB::transaction(function () use ($class, $student): string {
            $lockedClass = ClassRoom::whereKey($class->id)->lockForUpdate()->firstOrFail();
            $lockedStudent = User::whereKey($student->id)->lockForUpdate()->firstOrFail();

            // Ownership and account eligibility are rechecked after locking so
            // a parallel role, institution, archive, or capacity change cannot
            // be bypassed between validation and enrollment.
            $this->ensureCanManageClass($lockedClass);

            if ((int) $lockedStudent->role !== User::ROLE_USER || ! $lockedStudent->is_active) {
                return 'ineligible';
            }

            $institutionId = $this->resolveInstitutionIdForEnrollment($lockedClass);
            if (! $institutionId) {
                return 'no_institution';
            }

            if ($lockedClass->is_archived) {
                return 'archived';
            }

            $alreadyEnrolled = DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->where('student_id', $lockedStudent->id)
                ->exists();

            if ($alreadyEnrolled) {
                return 'duplicate';
            }

            $studentCount = DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->count();

            if ($lockedClass->max_students !== null && $studentCount >= (int) $lockedClass->max_students) {
                return 'full';
            }

            if ($lockedStudent->institution_id !== null
                && (int) $lockedStudent->institution_id !== (int) $institutionId) {
                return 'different_institution';
            }

            if ($lockedStudent->institution_id === null) {
                $lockedStudent->update(['institution_id' => $institutionId]);
            }

            DB::table('class_student')->insert([
                'class_id' => $lockedClass->id,
                'student_id' => $lockedStudent->id,
                'enrolled_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return 'added';
        }, 3);

        if ($result !== 'added') {
            $message = match ($result) {
                'ineligible' => 'The account is no longer an active student.',
                'no_institution' => 'This instructor or class has no institution assigned.',
                'archived' => 'Students cannot be added to an archived class.',
                'duplicate' => "{$student->name} is already in this class.",
                'full' => 'This class has reached its maximum student capacity.',
                'different_institution' => 'This student belongs to a different institution.',
                default => 'The student could not be enrolled.',
            };

            throw ValidationException::withMessages(['email' => $message]);
        }

        $notifications->send(
            $student,
            'class_enrollment',
            'Added to a class',
            'You were added to “' . $class->name . '”. New modules, assignments, and assessments for this class will appear in your account.',
            route('studentDashboard'),
            ['class_id' => $class->id],
            'class-enrollment:' . $class->id . ':' . now()->format('YmdHis')
        );

        return back()->with('success', "{$student->name} ({$student->email}) has been added to {$class->name}.");
    }

    public function remove(ClassRoom $class, User $student, StudentNotificationService $notifications)
    {
        $this->ensureCanManageClass($class);

        $result = DB::transaction(function () use ($class, $student): string {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $lockedStudent = User::query()->whereKey($student->id)->lockForUpdate()->firstOrFail();
            $this->ensureCanManageClass($lockedClass);

            $enrollment = DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->where('student_id', $lockedStudent->id)
                ->lockForUpdate()
                ->first();

            if (! $enrollment) {
                return 'missing';
            }

            if ($this->hasInProgressWork($lockedClass->id, [$lockedStudent->id])) {
                return 'in_progress';
            }

            DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->where('student_id', $lockedStudent->id)
                ->delete();

            return 'removed';
        }, 3);

        if ($result === 'missing') {
            return back()->with('error', 'That student is not enrolled in this class.');
        }

        if ($result === 'in_progress') {
            return back()->with('error', 'This student has an in-progress assignment or assessment attempt. Resolve it before removing them.');
        }

        $notifications->send(
            $student,
            'class_removed',
            'Removed from a class',
            'You were removed from “' . $class->name . '”. Its active coursework is no longer available in your account.',
            route('studentDashboard'),
            ['class_id' => $class->id],
            'class-removed:' . $class->id . ':' . now()->format('YmdHis')
        );

        return back()->with('success', "{$student->name} has been removed from the class.");
    }

    public function removeBulk(Request $request, ClassRoom $class, StudentNotificationService $notifications)
    {
        $this->ensureCanManageClass($class);

        $request->validate([
            'student_ids' => ['required', 'array', 'max:100'],
            'student_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $ids = collect($request->input('student_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->toArray();

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        $result = DB::transaction(function () use ($class, $ids): array {
            $lockedClass = ClassRoom::query()->whereKey($class->id)->lockForUpdate()->firstOrFail();
            $this->ensureCanManageClass($lockedClass);

            User::query()
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            $enrolledIds = DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->whereIn('student_id', $ids)
                ->orderBy('student_id')
                ->lockForUpdate()
                ->pluck('student_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            if ($enrolledIds === []) {
                return ['status' => 'missing', 'count' => 0, 'student_ids' => []];
            }

            if ($this->hasInProgressWork($lockedClass->id, $enrolledIds)) {
                return ['status' => 'in_progress', 'count' => 0, 'student_ids' => []];
            }

            $count = DB::table('class_student')
                ->where('class_id', $lockedClass->id)
                ->whereIn('student_id', $enrolledIds)
                ->delete();

            return ['status' => 'removed', 'count' => $count, 'student_ids' => $enrolledIds];
        }, 3);

        if ($result['status'] === 'missing') {
            return back()->with('error', 'None of the selected accounts are enrolled in this class.');
        }

        if ($result['status'] === 'in_progress') {
            return back()->with('error', 'One or more selected students have in-progress assignment or assessment attempts. Resolve those attempts before removing the group.');
        }

        $notifications->sendToUsers(
            $result['student_ids'],
            'class_removed',
            'Removed from a class',
            'You were removed from “' . $class->name . '”. Its active coursework is no longer available in your account.',
            route('studentDashboard'),
            ['class_id' => $class->id],
            'class-removed:' . $class->id . ':' . now()->format('YmdHis')
        );

        return back()->with('success', $result['count'] . ' student(s) removed.');
    }

    private function hasInProgressWork(int $classId, array $studentIds): bool
    {
        $hasAssignment = DB::table('assignment_submissions')
            ->join('class_assignments', 'class_assignments.id', '=', 'assignment_submissions.class_assignment_id')
            ->where('class_assignments.class_id', $classId)
            ->whereIn('assignment_submissions.student_id', $studentIds)
            ->where('assignment_submissions.status', 'in_progress')
            ->exists();

        if ($hasAssignment) {
            return true;
        }

        return DB::table('assessment_submissions')
            ->join('assessments', 'assessments.id', '=', 'assessment_submissions.assessment_id')
            ->where('assessments.class_id', $classId)
            ->whereIn('assessment_submissions.student_id', $studentIds)
            ->where('assessment_submissions.status', 'in_progress')
            ->exists();
    }
}
