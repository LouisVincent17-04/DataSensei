<?php
// ═══════════════════════════════════════════════════════════════════════
//  app/Http/Controllers/ClassStudentController.php
// ═══════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;   // rename to whatever your model is called
use App\Models\User;
use Illuminate\Http\Request;

class ClassStudentController extends Controller
{
    // ── List (enrolled + pending) ────────────────────────────────────────
    public function index(Request $request, ClassRoom $class)
    {
        $this->authorize('manage', $class); // or gate check of your choice

        $tab    = $request->input('tab', 'enrolled');
        $search = $request->input('search');

        // Base query: role = 1 (student), in this class
        $base = $class->students()                  // belongsToMany via class_student
                      ->where('role', 1)
                      ->with('institution');         // eager-load if you have a belongs-to

        if ($search) {
            $base->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // If you add an `approved` boolean column to class_student later,
        // split enrolled vs pending here:
        //   enrolled: pivot.approved = true
        //   pending : pivot.approved = false / null
        //
        // For now we show all enrolled students in both tabs
        // (add ->wherePivot('approved', ...) once the column exists).

        $students = (clone $base)->paginate(20)->withQueryString();

        return view('instructor.classes.students', [
            'class'         => $class,
            'students'      => $students,
            'tab'           => $tab,
            'enrolledCount' => $class->students()->where('role', 1)->count(),
            'pendingCount'  => 0,  // update when pivot.approved column is added
            'avgXp'         => (int) $class->students()->where('role', 1)->avg('xp'),
        ]);
    }

    // ── Approve single ───────────────────────────────────────────────────
    public function approve(ClassRoom $class, User $student)
    {
        $this->authorize('manage', $class);

        // $class->students()->updateExistingPivot($student->id, ['approved' => true]);

        return back()->with('success', "{$student->name} has been approved.");
    }

    // ── Approve bulk ─────────────────────────────────────────────────────
    public function approveBulk(Request $request, ClassRoom $class)
    {
        $this->authorize('manage', $class);

        $ids = collect($request->input('student_ids', []))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->toArray();

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        // $class->students()->wherePivotIn('student_id', $ids)
        //       ->each(fn ($s) => $class->students()->updateExistingPivot($s->id, ['approved' => true]));

        return back()->with('success', count($ids) . ' student(s) approved.');
    }

    // ── Remove single ────────────────────────────────────────────────────
    public function remove(ClassRoom $class, User $student)
    {
        $this->authorize('manage', $class);

        $class->students()->detach($student->id);

        return back()->with('success', "{$student->name} has been removed from the class.");
    }

    // ── Remove bulk ──────────────────────────────────────────────────────
    public function removeBulk(Request $request, ClassRoom $class)
    {
        $this->authorize('manage', $class);

        $ids = collect($request->input('student_ids', []))
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->toArray();

        if (empty($ids)) {
            return back()->with('error', 'No students selected.');
        }

        $class->students()->detach($ids);

        return back()->with('success', count($ids) . ' student(s) removed.');
    }
}


// ═══════════════════════════════════════════════════════════════════════
//  routes/web.php  — add inside your instructor-middleware group
// ═══════════════════════════════════════════════════════════════════════

/*
Route::prefix('instructor')->name('instructor.')->middleware(['auth', 'role:instructor'])->group(function () {

    // … your existing class routes …

    // Students roster
    Route::get   ('classes/{class}/students',                [ClassStudentController::class, 'index'])       ->name('classes.students');
    Route::post  ('classes/{class}/students/{student}/approve', [ClassStudentController::class, 'approve'])  ->name('classes.students.approve');
    Route::post  ('classes/{class}/students/approve-bulk',   [ClassStudentController::class, 'approveBulk']) ->name('classes.students.approve-bulk');
    Route::delete('classes/{class}/students/{student}',      [ClassStudentController::class, 'remove'])      ->name('classes.students.remove');
    Route::delete('classes/{class}/students',                [ClassStudentController::class, 'removeBulk'])  ->name('classes.students.remove-bulk');

});
*/


// ═══════════════════════════════════════════════════════════════════════
//  app/Models/ClassRoom.php  — relationships needed by the view
// ═══════════════════════════════════════════════════════════════════════

/*
public function students(): BelongsToMany
{
    return $this->belongsToMany(
        User::class,
        'class_student',
        'class_id',
        'student_id'
    )->withPivot('enrolled_at')->withTimestamps();
}
*/