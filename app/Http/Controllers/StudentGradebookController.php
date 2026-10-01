<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Services\Reports\ClassCompletion;
use App\Services\Reports\GradebookService;
use App\Models\UserCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * My Gradebook (DataSensei Updates 12): a student's own grades, results and
 * feedback, class by class: every assessment their instructor published in
 * the class (type, score, percentage, status, due date, attempts,
 * submission date, feedback), the overall class grade, and how much of the
 * class's required work they have completed.
 *
 * Privacy is enforced here, not in the page. The only student whose grades
 * are read is the signed-in one (Auth::id()); the route takes no student id
 * and any student_id, user_id or id in the query string is ignored. A class
 * can only be opened while the student is enrolled in it, so changing
 * class_id to someone else's class answers 404 (the class is not revealed
 * to exist). Result links point to the existing result pages, which check
 * that the attempt belongs to the signed-in student.
 */
class StudentGradebookController extends Controller
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly ClassCompletion $completion,
    ) {
    }

    public function index(Request $request): View
    {
        $studentId = (int) Auth::id();

        $classIds = DB::table('class_student')
            ->where('student_id', $studentId)
            ->pluck('class_id')
            ->map(fn ($id) => (int) $id);

        $classes = ClassRoom::query()
            ->whereIn('id', $classIds->all() ?: [0])
            ->with('instructor:id,name')
            ->orderBy('is_archived')
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        $selected = null;
        if ($request->filled('class_id')) {
            $selected = $classes->firstWhere('id', (int) $request->query('class_id'));
            abort_if($selected === null, 404);
        }

        $shown = $selected ? collect([$selected]) : $classes;
        $certificates = UserCertificate::query()->where('user_id', $studentId)->whereIn('class_id', $shown->pluck('id')->all() ?: [0])->get()->groupBy('class_id');

        $books = $shown->map(function (ClassRoom $class) use ($studentId, $certificates): array {
            $rows = $this->gradebook->forStudent($class, $studentId);

            return [
                'class' => $class,
                'rows' => $rows,
                'overall' => GradebookService::overall($rows),
                // Completion of the class's required work, the same check
                // the instructor's certificate uses (own row only).
                'completion' => $this->completion->forStudent($class, $studentId),
                'certificate' => $certificates->get($class->id, collect())->first(fn (UserCertificate $c) => ! $c->isRevoked()),
            ];
        })->values()->all();

        return view('student.gradebook.index', [
            'classes' => $classes,
            'selected' => $selected,
            'books' => $books,
        ]);
    }
}
