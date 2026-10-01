<?php

namespace App\Http\Controllers;

use App\Models\AssessmentSubmission;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * The student's attempt history (DataSensei Updates 11): every saved
 * assessment attempt (homework, quizzes, examinations) plus finished
 * challenge activity. The assessments page lists work to start or continue;
 * this page lists what was attempted and how it went.
 */
class StudentSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $studentId = (int) Auth::id();
        $completedStatuses = ['submitted', 'late', 'graded'];

        $baseQuery = AssessmentSubmission::query()->where('student_id', $studentId);
        $completedQuery = (clone $baseQuery)->whereIn('status', $completedStatuses);

        $earnedPoints = (float) (clone $completedQuery)->sum('score');
        $possiblePoints = (float) (clone $completedQuery)->sum('total_points');

        $submissionStats = [
            'all_attempts' => (clone $baseQuery)->count(),
            'completed' => (clone $completedQuery)->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'graded' => (clone $baseQuery)->where('status', 'graded')->count(),
            'late' => (clone $baseQuery)->where('status', 'late')->count(),
            'average_percentage' => $possiblePoints > 0
                ? (int) round(($earnedPoints / $possiblePoints) * 100)
                : 0,
        ];

        $query = AssessmentSubmission::with(['assessment.classRoom'])
            ->where('student_id', $studentId);

        $status = (string) $request->input('status', '');

        if ($status === 'completed') {
            $query->whereIn('status', $completedStatuses);
        } elseif (in_array($status, ['in_progress', 'submitted', 'late', 'graded'], true)) {
            $query->where('status', $status);
        }

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->whereHas('assessment', function ($assessmentQuery) use ($search) {
                $assessmentQuery
                    ->where('title', 'like', '%'.$search.'%')
                    ->orWhereHas('classRoom', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', '%'.$search.'%');
                    });
            });
        }

        $submissions = $query
            ->latest('created_at')
            ->paginate(12, ['*'], 'page')
            ->withQueryString();

        [$challengeActivity, $challengeLookup] = $this->challengeActivity($studentId, $search);

        return view('student.submissions.index', compact(
            'submissions',
            'submissionStats',
            'challengeActivity',
            'challengeLookup'
        ));
    }

    public function show(AssessmentSubmission $submission)
    {
        abort_unless((int) $submission->student_id === (int) Auth::id(), 403);
        abort_unless(in_array($submission->status, ['submitted', 'late', 'graded'], true), 404);

        $assessment = $submission->assessment;
        abort_unless($assessment, 404);

        return redirect()->route('student.assessments.result', [$assessment, $submission]);
    }

    /**
     * Finished multiple-choice challenge attempts and graded coding challenge
     * submissions, newest first. They live in their own tables, so without
     * this the page would show only assessment work.
     *
     * @return array{0: \Illuminate\Contracts\Pagination\LengthAwarePaginator, 1: \Illuminate\Support\Collection}
     */
    private function challengeActivity(int $studentId, string $search): array
    {
        $matchingChallengeIds = null;
        if ($search !== '') {
            $matchingChallengeIds = DB::table('challenges')
                ->where('title', 'like', '%'.$search.'%')
                ->pluck('id')
                ->all();
        }

        $mcq = DB::table('challenge_attempts')
            ->where('user_id', $studentId)
            ->whereIn('status', ['submitted', 'expired', 'disqualified'])
            ->select([
                DB::raw("'mcq' as kind"),
                'id as record_id',
                'challenge_id',
                'attempt_no',
                'status',
                'score as earned',
                'total_questions as possible',
                'xp_awarded as xp',
                'is_ranked as ranked',
                'submitted_at as activity_at',
                DB::raw('NULL as question_title'),
                DB::raw('NULL as question_order'),
            ]);

        $coding = DB::table('coding_submissions')
            ->join('coding_questions', 'coding_questions.id', '=', 'coding_submissions.coding_question_id')
            ->where('coding_submissions.user_id', $studentId)
            ->where('coding_submissions.voided', false)
            ->whereIn('coding_submissions.status', ['passed', 'failed', 'error'])
            ->select([
                DB::raw("'coding' as kind"),
                'coding_submissions.id as record_id',
                'coding_questions.challenge_id',
                DB::raw('NULL as attempt_no'),
                'coding_submissions.status',
                'coding_submissions.tests_passed as earned',
                'coding_submissions.tests_total as possible',
                'coding_submissions.xp_earned as xp',
                DB::raw('1 as ranked'),
                'coding_submissions.created_at as activity_at',
                'coding_questions.title as question_title',
                'coding_questions.order_index as question_order',
            ]);

        if ($matchingChallengeIds !== null) {
            $ids = $matchingChallengeIds === [] ? [0] : $matchingChallengeIds;
            $mcq->whereIn('challenge_id', $ids);
            $coding->whereIn('coding_questions.challenge_id', $ids);
        }

        $activity = $mcq->unionAll($coding)
            ->orderByDesc('activity_at')
            ->orderByDesc('record_id')
            ->paginate(12, ['*'], 'challenge_page')
            ->withQueryString();

        $challengeLookup = Challenge::with('category')
            ->whereIn('id', collect($activity->items())->pluck('challenge_id')->filter()->unique()->all())
            ->get()
            ->keyBy('id');

        return [$activity, $challengeLookup];
    }
}
