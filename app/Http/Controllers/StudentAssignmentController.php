<?php

namespace App\Http\Controllers;

use App\Models\AssignmentBlankAnswer;
use App\Models\AssignmentQuestion;
use App\Models\AssignmentQuestionOption;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentSubmissionAnswer;
use App\Models\ClassAssignment;
use App\Services\AntiCheatPolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user();

        // IMPORTANT: Use the actual pivot table directly.
        // This avoids the page becoming empty just because User::classesAsStudent()
        // was not copied/added yet.
        $classIds = $this->studentClassIds((int) $student->id);

        $baseForStudentClasses = ClassAssignment::whereIn('class_id', $classIds);

        $studentAssignmentStats = [
            'enrolled_classes' => $classIds->count(),
            'all_class_assignments' => (clone $baseForStudentClasses)->count(),
            'published_visible' => (clone $baseForStudentClasses)->visibleToStudents()->count(),
            'draft' => (clone $baseForStudentClasses)->where('status', 'draft')->count(),
            'future' => (clone $baseForStudentClasses)
                ->where('status', 'published')
                ->whereNotNull('available_at')
                ->where('available_at', '>', now())
                ->count(),
        ];

        $query = ClassAssignment::with([
                'classRoom',
                'libraryItem',
                'submissions' => fn ($q) => $q
                    ->where('student_id', $student->id)
                    ->orderByDesc('attempt_no')
                    ->orderByDesc('created_at'),
            ])
            ->whereIn('class_id', $classIds)
            ->visibleToStudents()
            ->orderByRaw('due_at IS NULL')
            ->orderBy('due_at')
            ->latest('created_at');

        if ($request->filled('status')) {
            $status = $request->input('status');

            if ($status === 'pending') {
                $query->whereDoesntHave('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['submitted', 'late', 'graded']);
                });
            }

            if ($status === 'submitted') {
                $query->whereHas('submissions', function ($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->whereIn('status', ['submitted', 'late', 'graded']);
                });
            }
        }

        $assignments = $query->paginate(10)->withQueryString();

        return view('student.assignments.index', compact('assignments', 'studentAssignmentStats'));
    }

    public function show(ClassAssignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);

        $assignment->load([
            'classRoom',
            'libraryItem.questions.options',
            'libraryItem.questions.blankAnswers',
            'submissions' => fn ($q) => $q->where('student_id', Auth::id())->orderByDesc('attempt_no'),
        ]);

        $latestSubmission = $assignment->submissions->first();

        return view('student.assignments.show', compact('assignment', 'latestSubmission'));
    }

    public function start(ClassAssignment $assignment)
    {
        $this->authorizeStudentAssignment($assignment);

        abort_unless($assignment->status === 'published', 403, 'This assignment is already closed.');

        $studentId = Auth::id();
        $assignment->load('libraryItem.questions');

        $latestSubmission = AssignmentSubmission::where('class_assignment_id', $assignment->id)
            ->where('student_id', $studentId)
            ->orderByDesc('attempt_no')
            ->first();

        if ($latestSubmission && $latestSubmission->status === 'in_progress') {
            return redirect()->route('student.assignments.take', [$assignment, $latestSubmission]);
        }

        $submittedAttempts = AssignmentSubmission::where('class_assignment_id', $assignment->id)
            ->where('student_id', $studentId)
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->count();

        if ($submittedAttempts >= $assignment->max_attempts) {
            if ($latestSubmission) {
                return redirect()
                    ->route('student.assignments.result', [$assignment, $latestSubmission])
                    ->with('error', 'You have already used all attempts for this assignment.');
            }

            return redirect()
                ->route('student.assignments.show', $assignment)
                ->with('error', 'You have already used all attempts for this assignment.');
        }

        $submission = AssignmentSubmission::create([
            'class_assignment_id' => $assignment->id,
            'student_id' => $studentId,
            'attempt_no' => $submittedAttempts + 1,
            'status' => 'in_progress',
            'score' => 0,
            'total_points' => (int) $assignment->libraryItem->questions->sum('points'),
            'started_at' => now(),
        ]);

        return redirect()->route('student.assignments.take', [$assignment, $submission]);
    }

    public function take(ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.assignments.result', [$assignment, $submission]);
        }

        $assignment->load(['classRoom', 'libraryItem.questions.options']);

        $antiCheatSettings = app(AntiCheatPolicyService::class)
            ->settingsForAssignment(Auth::user(), $assignment);

        return view('student.assignments.take', compact('assignment', 'submission', 'antiCheatSettings'));
    }

    public function submit(Request $request, ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission);

        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.assignments.result', [$assignment, $submission]);
        }

        $blockedReason = app(AntiCheatPolicyService::class)->assignmentSubmissionBlocked(
            Auth::user(),
            $assignment,
            $submission,
            $request->input('_anti_cheat_session_id')
        );

        if ($blockedReason) {
            return redirect()
                ->route('student.assignments.take', [$assignment, $submission])
                ->withErrors(['anti_cheat' => $blockedReason]);
        }

        $assignment->load(['libraryItem.questions.options', 'libraryItem.questions.blankAnswers']);

        $answers = $request->input('answers', []);
        $score = 0;
        $totalPoints = (int) $assignment->libraryItem->questions->sum('points');

        DB::transaction(function () use ($assignment, $submission, $answers, &$score, $totalPoints) {
            foreach ($assignment->libraryItem->questions as $question) {
                $rawAnswer = $answers[$question->id] ?? null;
                [$isCorrect, $selectedOptionId, $answerText] = $this->gradeQuestion($question, $rawAnswer);
                $pointsAwarded = $isCorrect ? (int) $question->points : 0;
                $score += $pointsAwarded;

                AssignmentSubmissionAnswer::updateOrCreate(
                    [
                        'assignment_submission_id' => $submission->id,
                        'assignment_question_id' => $question->id,
                    ],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'answer_text' => $answerText,
                        'is_correct' => $isCorrect,
                        'points_awarded' => $pointsAwarded,
                    ]
                );
            }

            $isLate = $assignment->due_at && now()->greaterThan($assignment->due_at);

            $submission->update([
                'status' => $isLate ? 'late' : 'graded',
                'score' => $score,
                'total_points' => $totalPoints,
                'submitted_at' => now(),
                'graded_at' => now(),
            ]);
        });

        return redirect()
            ->route('student.assignments.result', [$assignment, $submission])
            ->with('success', 'Assignment submitted successfully.');
    }

    public function result(ClassAssignment $assignment, AssignmentSubmission $submission)
    {
        $this->authorizeStudentSubmission($assignment, $submission, allowSubmitted: true);

        $submission->load([
            'answers.question.options',
            'answers.question.blankAnswers',
            'answers.selectedOption',
        ]);

        $assignment->load(['classRoom', 'libraryItem.questions.options']);

        return view('student.assignments.result', compact('assignment', 'submission'));
    }

    private function authorizeStudentAssignment(ClassAssignment $assignment): void
    {
        abort_unless(in_array($assignment->status, ['published', 'closed'], true), 404);

        abort_if(
            $assignment->available_at && now()->lessThan($assignment->available_at),
            403,
            'This assignment is not available yet.'
        );

        $isEnrolled = DB::table('class_student')
            ->where('class_id', $assignment->class_id)
            ->where('student_id', Auth::id())
            ->exists();

        abort_unless($isEnrolled, 403, 'You are not enrolled in this assignment class.');
    }

    private function authorizeStudentSubmission(ClassAssignment $assignment, AssignmentSubmission $submission, bool $allowSubmitted = false): void
    {
        $this->authorizeStudentAssignment($assignment);

        abort_unless(
            (int) $submission->class_assignment_id === (int) $assignment->id &&
            (int) $submission->student_id === (int) Auth::id(),
            403,
            'You are not allowed to access this submission.'
        );

        if (!$allowSubmitted && $submission->status !== 'in_progress') {
            throw ValidationException::withMessages([
                'submission' => 'This assignment attempt has already been submitted.',
            ]);
        }
    }

    private function gradeQuestion(AssignmentQuestion $question, mixed $rawAnswer): array
    {
        if ($question->question_type === 'mcq') {
            $selectedOptionId = $rawAnswer ? (int) $rawAnswer : null;

            $isCorrect = AssignmentQuestionOption::where('id', $selectedOptionId)
                ->where('assignment_question_id', $question->id)
                ->where('is_correct', true)
                ->exists();

            return [$isCorrect, $selectedOptionId, null];
        }

        $answerText = trim((string) $rawAnswer);

        $acceptedAnswers = AssignmentBlankAnswer::where('assignment_question_id', $question->id)->get();

        $isCorrect = $acceptedAnswers->contains(function (AssignmentBlankAnswer $accepted) use ($answerText) {
            $expected = trim((string) $accepted->answer_text);

            if ($accepted->is_case_sensitive) {
                return $answerText === $expected;
            }

            return mb_strtolower($answerText) === mb_strtolower($expected);
        });

        return [$isCorrect, null, $answerText];
    }

    private function studentClassIds(int $studentId)
    {
        return DB::table('class_student')
            ->where('student_id', $studentId)
            ->pluck('class_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();
    }
}
