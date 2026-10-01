<?php

namespace App\Http\Controllers;

use App\Models\CertificateDefinition;
use App\Models\ClassRoom;
use App\Models\UserCertificate;
use App\Services\Reports\ClassCompletion;
use App\Services\Reports\GradebookService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class Gradebook (DataSensei Updates 12): the grades of the students
 * enrolled in one of the instructor's own classes.
 *
 * Only classes whose instructor_id is the signed-in instructor are listed or
 * opened; any other class_id answers 404, whatever the URL says. The grid
 * shows every enrolled student against every published or closed
 * assessment: score, percentage and state, with each student's final grade,
 * their completion of the class's required work, and the class averages.
 * Choosing an assessment lists each student's counted attempt with the
 * feedback given, and links to the existing submission page (which checks
 * ownership again). Choosing a student shows their whole record in the
 * class, every attempt included, to review before issuing the Certificate of
 * Completion; a student who is not enrolled in the class answers 404.
 */
class InstructorGradebookController extends Controller
{
    public function __construct(
        private readonly GradebookService $gradebook,
        private readonly ClassCompletion $completion,
    ) {
    }

    public function index(Request $request): View
    {
        $classes = ClassRoom::query()
            ->forInstructor((int) Auth::id())
            ->withCount('students')
            ->orderBy('is_archived')
            ->orderBy('name')
            ->get();

        $class = null;
        if ($request->filled('class_id')) {
            $class = $classes->firstWhere('id', (int) $request->query('class_id'));
            abort_if($class === null, 404);
        } elseif ($classes->count() === 1) {
            $class = $classes->first();
        }

        if ($class === null) {
            return view('instructor.gradebook.index', ['classes' => $classes, 'class' => null]);
        }

        // One student's record in this class: only a student enrolled in it.
        if ($request->filled('student_id')) {
            $studentId = (int) $request->query('student_id');
            abort_unless(DB::table('class_student')->where('class_id', $class->id)->where('student_id', $studentId)->exists(), 404);
            $rows = $this->gradebook->forStudent($class, $studentId);

            return view('instructor.gradebook.index', [
                'classes' => $classes,
                'class' => $class,
                'book' => null,
                'assessment' => null,
                'record' => [
                    'student' => DB::table('users')->where('id', $studentId)->first(['id', 'name', 'email']),
                    'rows' => $rows,
                    'overall' => GradebookService::overall($rows),
                    'history' => $this->gradebook->history($class, $studentId),
                    'completion' => $this->completion->forStudent($class, $studentId),
                    'certificates' => UserCertificate::query()->where('user_id', $studentId)->where('class_id', $class->id)->orderByDesc('issued_at')->get(),
                ],
            ]);
        }

        $book = $this->gradebook->forClass($class);

        $assessment = null;
        if ($request->filled('assessment_id')) {
            $assessment = $book['assessments']->firstWhere('id', (int) $request->query('assessment_id'));
            abort_if($assessment === null, 404);
        }

        return view('instructor.gradebook.index', [
            'classes' => $classes,
            'class' => $class,
            'book' => $book,
            'assessment' => $assessment,
            'record' => null,
            'completion' => $assessment === null ? $this->completion->forClass($class) : null,
            'certificate' => CertificateDefinition::query()->classCertificates()->where('class_id', $class->id)->where('owner_user_id', Auth::id())->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")->latest('id')->first(),
        ]);
    }
}
