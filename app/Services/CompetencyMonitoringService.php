<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Competency;
use App\Models\StudentCompetencySnapshot;
use App\Models\StudentCompetencyTrend;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CompetencyMonitoringService
{
    private const SOURCE_WEIGHTS = [
        'assessment' => 30,
        'assignment' => 20,
        'coding' => 20,
        'challenge' => 15,
        'toolkit' => 10,
        'model' => 20,
        'ide' => 5,
    ];

    /** @var array<int, array<int, string>> */
    private const MODULE_COMPETENCIES = [
        1 => ['python_programming'],
        2 => ['statistics', 'eda'],
        3 => ['data_analysis', 'eda'],
        4 => ['statistics'],
        6 => ['statistics', 'data_analysis'],
        7 => ['python_programming', 'data_analysis'],
        8 => ['statistics', 'data_analysis'],
        9 => ['statistics', 'machine_learning'],
        10 => ['sql'],
        11 => ['statistics', 'data_analysis'],
        12 => ['statistics', 'data_analysis'],
        13 => ['machine_learning'],
        14 => ['machine_learning', 'data_analysis'],
        15 => ['data_visualization', 'data_analysis', 'eda'],
        16 => ['statistics', 'data_analysis'],
        17 => ['machine_learning'],
        19 => ['machine_learning'],
        20 => ['data_analysis', 'eda'],
        21 => ['machine_learning', 'data_analysis', 'eda'],
        22 => ['data_analysis'],
        23 => ['sql', 'data_analysis'],
        24 => ['machine_learning'],
    ];

    /** @var array<string, array<int, string>> */
    private const KEYWORDS = [
        'python_programming' => [
            'python', 'pandas', 'numpy', 'programming', 'algorithm', 'data structure',
            'debugging', 'function', 'loop', 'dictionary', 'list comprehension',
        ],
        'statistics' => [
            'statistics', 'statistical', 'probability', 'hypothesis', 'correlation',
            'regression', 'bayesian', 'experimental design', 'forecasting', 'multivariate',
            'variance', 'standard deviation', 'confidence interval',
        ],
        'sql' => [
            'sql', 'database', 'dbms', 'relational', 'query', 'join', 'normalization',
            'data warehouse', 'warehousing', 'mysql', 'sqlite', 'postgresql',
        ],
        'data_visualization' => [
            'visualization', 'visualisation', 'chart', 'plot', 'dashboard', 'matplotlib',
            'seaborn', 'histogram', 'scatter plot', 'bar graph', 'line graph',
        ],
        'data_analysis' => [
            'data analysis', 'analytics', 'data science', 'analysis of', 'analytical',
            'forecasting', 'multivariate', 'unstructured data', 'decision making',
        ],
        'eda' => [
            'exploratory data analysis', 'eda', 'data exploration', 'data cleaning',
            'preprocessing', 'missing values', 'outlier', 'descriptive analysis',
            'dataset exploration',
        ],
        'machine_learning' => [
            'machine learning', 'supervised learning', 'unsupervised learning',
            'deep learning', 'artificial intelligence', 'classification', 'clustering',
            'model evaluation', 'neural network', 'gradient descent', 'prediction model',
        ],
    ];

    public function classReport(ClassRoom $class): array
    {
        $competencies = Competency::active()->orderBy('sort_order')->orderBy('id')->get();
        $students = $class->students()
            ->where(function ($query): void {
                $query->where('users.status', 'active')->orWhereNull('users.status');
            })
            ->orderBy('users.name')
            ->get(['users.id', 'users.name', 'users.email']);

        $snapshots = StudentCompetencySnapshot::query()
            ->with(['competency', 'student:id,name,email'])
            ->where('class_id', $class->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get();

        $byStudent = $snapshots->groupBy('student_id');
        $matrix = $students->map(function (User $student) use ($byStudent, $competencies): array {
            $studentSnapshots = $byStudent->get($student->id, collect())->keyBy('competency_id');
            $cells = $competencies->mapWithKeys(function (Competency $competency) use ($studentSnapshots): array {
                $snapshot = $studentSnapshots->get($competency->id);

                return [$competency->key => $snapshot ?: $this->emptySnapshot($competency)];
            });
            $assessed = $cells->filter(fn ($snapshot) => (int) $snapshot->evidence_count > 0);
            $overall = $assessed->isNotEmpty() ? round((float) $assessed->avg('percentage'), 1) : 0.0;

            return [
                'student' => $student,
                'competencies' => $cells,
                'overall_percentage' => $overall,
                'overall_level' => $this->levelFor($overall, (int) $assessed->sum('evidence_count')),
                'evidence_count' => (int) $cells->sum('evidence_count'),
            ];
        })->values();

        $competencyStats = $competencies->mapWithKeys(function (Competency $competency) use ($snapshots): array {
            $rows = $snapshots
                ->where('competency_id', $competency->id)
                ->filter(fn (StudentCompetencySnapshot $snapshot) => $snapshot->evidence_count > 0)
                ->sortByDesc('percentage')
                ->values();
            $highest = $rows->first();
            $lowest = $rows->last();
            $average = $rows->isNotEmpty() ? round((float) $rows->avg('percentage'), 1) : 0.0;

            return [$competency->key => [
                'competency' => $competency,
                'average' => $average,
                'level' => $this->levelFor($average, (int) $rows->sum('evidence_count')),
                'assessed_students' => $rows->count(),
                'highest' => $highest,
                'lowest' => $lowest,
                'distribution' => collect(['Beginner', 'Developing', 'Competent', 'Advanced', 'Expert'])
                    ->mapWithKeys(fn (string $level): array => [$level => $rows->where('level', $level)->count()])
                    ->all(),
            ]];
        });

        $ranked = $matrix->filter(fn (array $row): bool => $row['evidence_count'] > 0)
            ->sortByDesc('overall_percentage')
            ->values();
        $calculatedAt = $snapshots->sortByDesc('calculated_at')->first()?->calculated_at;

        return [
            'class' => $class,
            'competencies' => $competencies,
            'matrix' => $matrix,
            'competency_stats' => $competencyStats,
            'summary' => [
                'students' => $students->count(),
                'assessed_students' => $ranked->count(),
                'class_average' => $ranked->isNotEmpty()
                    ? round((float) $ranked->avg('overall_percentage'), 1)
                    : 0.0,
                'highest_performer' => $ranked->first(),
                'lowest_performer' => $ranked->last(),
            ],
            'trends' => $this->classTrends($class, $competencies),
            'calculated_at' => $calculatedAt,
        ];
    }

    public function studentReport(User $student, ClassRoom $class): array
    {
        $report = $this->classReport($class);
        $studentRow = $report['matrix']->first(
            fn (array $row): bool => (int) $row['student']->id === (int) $student->id
        );

        return [
            'class' => $class,
            'competencies' => $report['competencies'],
            'student' => $studentRow,
            'class_summary' => $report['summary'],
            'comparisons' => $report['competency_stats'],
            'trends' => $this->studentTrends($student, $class, $report['competencies']),
            'calculated_at' => $report['calculated_at'],
        ];
    }

    public function refreshClass(ClassRoom $class): void
    {
        $lock = Cache::lock('competency-monitoring:class:' . $class->id, 600);
        if (! $lock->get()) {
            throw ValidationException::withMessages([
                'competencies' => 'Competencies are already being recalculated for this class. Wait a moment and try again.',
            ]);
        }

        try {
            $this->performRefreshClass($class);
        } finally {
            $lock->release();
        }
    }

    private function performRefreshClass(ClassRoom $class): void
    {
        if (! Schema::hasTable('competencies') || ! Schema::hasTable('student_competency_snapshots')) {
            return;
        }

        $competencies = Competency::active()->orderBy('sort_order')->get();
        if ($competencies->isEmpty()) {
            return;
        }

        $students = $class->students()
            ->where(function ($query): void {
                $query->where('users.status', 'active')->orWhereNull('users.status');
            })
            ->get(['users.id']);
        $studentIds = $students->pluck('id')->map(fn ($id): int => (int) $id)->values();

        if ($studentIds->isEmpty()) {
            return;
        }

        /** @var array<int, array<string, array<int, array<string, mixed>>>> $evidence */
        $evidence = [];
        $this->collectAssignmentEvidence($studentIds, (int) $class->id, $evidence);
        $this->collectAssessmentEvidence($studentIds, (int) $class->id, $evidence);
        $this->collectChallengeEvidence($studentIds, $evidence);
        $this->collectCodingEvidence($studentIds, $evidence);
        $this->collectToolkitEvidence($studentIds, $evidence);
        $this->collectModelDevelopmentEvidence($studentIds, (int) $class->id, $evidence);
        $this->collectIdeEvidence($studentIds, $evidence);

        DB::transaction(function () use ($students, $class, $competencies, $evidence): void {
            foreach ($students as $student) {
                foreach ($competencies as $competency) {
                    $items = collect($evidence[(int) $student->id][$competency->key] ?? []);
                    $calculated = $this->calculate($items);

                    $snapshot = StudentCompetencySnapshot::updateOrCreate([
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'competency_id' => $competency->id,
                    ], [
                        'percentage' => $calculated['percentage'],
                        'evidence_count' => $calculated['evidence_count'],
                        'level' => $calculated['level'],
                        'source_breakdown' => $calculated['source_breakdown'],
                        'last_evidence_at' => $calculated['last_evidence_at'],
                        'calculated_at' => now(),
                    ]);

                    if (Schema::hasTable('student_competency_trends')) {
                        StudentCompetencyTrend::updateOrCreate([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'competency_id' => $competency->id,
                            'recorded_on' => today()->toDateString(),
                        ], [
                            'percentage' => $snapshot->percentage,
                            'evidence_count' => $snapshot->evidence_count,
                        ]);
                    }
                }
            }
        }, 3);
    }

    public function levelFor(float $percentage, int $evidenceCount = 1): string
    {
        if ($evidenceCount <= 0) {
            return 'Not Assessed';
        }

        return match (true) {
            $percentage >= 85 => 'Expert',
            $percentage >= 70 => 'Advanced',
            $percentage >= 55 => 'Competent',
            $percentage >= 40 => 'Developing',
            default => 'Beginner',
        };
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectAssignmentEvidence(Collection $studentIds, int $classId, array &$evidence): void
    {
        if (! Schema::hasTable('assignment_submissions')) {
            return;
        }

        $rows = DB::table('assignment_submissions as submission')
            ->join('class_assignments as class_assignment', 'class_assignment.id', '=', 'submission.class_assignment_id')
            ->join('assignment_library_items as item', 'item.id', '=', 'class_assignment.assignment_library_item_id')
            ->whereIn('submission.student_id', $studentIds)
            ->where('class_assignment.class_id', $classId)
            ->whereIn('submission.status', ['submitted', 'late', 'graded'])
            ->where('submission.total_points', '>', 0)
            ->get([
                'submission.student_id',
                'submission.id',
                'submission.class_assignment_id',
                'submission.attempt_no',
                'submission.score',
                'submission.total_points',
                'submission.submitted_at',
                'submission.updated_at',
                'class_assignment.title as assigned_title',
                'item.module_no',
                'item.title as library_title',
                'item.topic_title',
                'item.description',
            ])
            ->groupBy(fn ($row): string => $row->student_id . ':' . $row->class_assignment_id)
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first());

        foreach ($rows as $row) {
            $score = $this->percent((float) $row->score, (float) $row->total_points);
            $text = implode(' ', array_filter([
                $row->assigned_title,
                $row->library_title,
                $row->topic_title,
                $row->description,
            ]));

            foreach ($this->matchingCompetencies($text, (int) $row->module_no) as $key) {
                $this->addEvidence($evidence, (int) $row->student_id, $key, [
                    'source' => 'assignment',
                    'score' => $score,
                    'weight' => 1.0,
                    'count' => 1,
                    'label' => (string) ($row->assigned_title ?: $row->library_title),
                    'at' => $row->submitted_at ?: $row->updated_at,
                ]);
            }
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectAssessmentEvidence(Collection $studentIds, int $classId, array &$evidence): void
    {
        if (! Schema::hasTable('assessment_submissions')) {
            return;
        }

        $diagnosticPairs = collect();

        // Diagnostics are stored as one current row-set per student/assessment,
        // not per attempt. Only trust those rows when the learner's latest
        // completed attempt has actually finished grading. If a newer essay
        // attempt is waiting for review, keep the previous fully graded result
        // as competency evidence until the instructor finishes that review.
        $latestAttemptStates = DB::table('assessment_submissions as submission')
            ->join('assessments as assessment', 'assessment.id', '=', 'submission.assessment_id')
            ->whereIn('submission.student_id', $studentIds)
            ->where('assessment.class_id', $classId)
            ->whereIn('submission.status', ['submitted', 'late', 'graded'])
            ->get([
                'submission.id',
                'submission.student_id',
                'submission.assessment_id',
                'submission.attempt_no',
                'submission.graded_at',
            ])
            ->groupBy(fn ($row): string => $row->student_id . ':' . $row->assessment_id)
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first());

        $fullyGradedLatestPairs = $latestAttemptStates
            ->filter(fn ($row): bool => $row->graded_at !== null)
            ->keys()
            ->flip();

        if (Schema::hasTable('student_assessment_diagnostics')) {
            $diagnostics = DB::table('student_assessment_diagnostics as diagnostic')
                ->join('assessments as assessment', 'assessment.id', '=', 'diagnostic.assessment_id')
                ->leftJoin('table_of_specifications as tos', 'tos.id', '=', 'assessment.table_of_specification_id')
                ->whereIn('diagnostic.student_id', $studentIds)
                ->where('diagnostic.class_id', $classId)
                ->where('diagnostic.manual_review_pending', false)
                ->where('diagnostic.possible_points', '>', 0)
                ->get([
                    'diagnostic.student_id',
                    'diagnostic.assessment_id',
                    'diagnostic.mastery_percent',
                    'diagnostic.possible_points',
                    'diagnostic.topic_title',
                    'diagnostic.subtopic_title',
                    'diagnostic.learning_objective',
                    'diagnostic.calculated_at',
                    'assessment.title as assessment_title',
                    'assessment.description as assessment_description',
                    'tos.module_no',
                ])
                ->filter(fn ($row): bool => $fullyGradedLatestPairs->has(
                    $row->student_id . ':' . $row->assessment_id
                ));

            $diagnosticPairs = $diagnostics
                ->map(fn ($row): string => $row->student_id . ':' . $row->assessment_id)
                ->unique()
                ->flip();

            foreach ($diagnostics as $row) {
                $text = implode(' ', array_filter([
                    $row->assessment_title,
                    $row->assessment_description,
                    $row->topic_title,
                    $row->subtopic_title,
                    $row->learning_objective,
                ]));

                foreach ($this->matchingCompetencies($text, $row->module_no ? (int) $row->module_no : null) as $key) {
                    $this->addEvidence($evidence, (int) $row->student_id, $key, [
                        'source' => 'assessment',
                        'score' => min(100.0, max(0.0, (float) $row->mastery_percent)),
                        'weight' => max(1.0, min(10.0, (float) $row->possible_points)),
                        'count' => 1,
                        'label' => (string) ($row->topic_title ?: $row->assessment_title),
                        'at' => $row->calculated_at,
                    ]);
                }
            }
        }

        $submissions = DB::table('assessment_submissions as submission')
            ->join('assessments as assessment', 'assessment.id', '=', 'submission.assessment_id')
            ->leftJoin('table_of_specifications as tos', 'tos.id', '=', 'assessment.table_of_specification_id')
            ->whereIn('submission.student_id', $studentIds)
            ->where('assessment.class_id', $classId)
            ->whereIn('submission.status', ['submitted', 'late', 'graded'])
            ->whereNotNull('submission.graded_at')
            ->where('submission.total_points', '>', 0)
            ->get([
                'submission.student_id',
                'submission.id',
                'submission.assessment_id',
                'submission.attempt_no',
                'submission.score',
                'submission.total_points',
                'submission.submitted_at',
                'submission.updated_at',
                'assessment.title',
                'assessment.description',
                'tos.module_no',
            ])
            ->groupBy(fn ($row): string => $row->student_id . ':' . $row->assessment_id)
            ->map(fn (Collection $attempts) => $attempts
                ->sortByDesc(fn ($row): string => $this->attemptSortKey($row))
                ->first());

        foreach ($submissions as $pair => $row) {
            if ($diagnosticPairs->has((string) $pair)) {
                continue;
            }

            $score = $this->percent((float) $row->score, (float) $row->total_points);
            foreach ($this->matchingCompetencies(
                trim((string) $row->title . ' ' . (string) $row->description),
                $row->module_no ? (int) $row->module_no : null,
            ) as $key) {
                $this->addEvidence($evidence, (int) $row->student_id, $key, [
                    'source' => 'assessment',
                    'score' => $score,
                    'weight' => 1.0,
                    'count' => 1,
                    'label' => (string) $row->title,
                    'at' => $row->submitted_at ?: $row->updated_at,
                ]);
            }
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectChallengeEvidence(Collection $studentIds, array &$evidence): void
    {
        if (! Schema::hasTable('challenge_attempts')) {
            return;
        }

        $rows = DB::table('challenge_attempts as attempt')
            ->join('challenges as challenge', 'challenge.id', '=', 'attempt.challenge_id')
            ->join('challenge_categories as category', 'category.id', '=', 'challenge.challenge_category_id')
            ->whereIn('attempt.user_id', $studentIds)
            ->where('category.slug', 'university-student')
            ->where('challenge.is_coding_challenge', false)
            ->where('attempt.is_ranked', true)
            ->whereIn('attempt.status', ['submitted', 'expired'])
            ->where('attempt.total_questions', '>', 0)
            ->get([
                'attempt.user_id',
                'attempt.challenge_id',
                'attempt.score',
                'attempt.total_questions',
                'attempt.submitted_at',
                'attempt.updated_at',
                'challenge.title',
                'challenge.description',
                'challenge.order_index',
            ])
            ->groupBy(fn ($row): string => $row->user_id . ':' . $row->challenge_id)
            ->map(fn (Collection $attempts) => $attempts->sortByDesc(
                fn ($row): float => ((float) $row->score / max(1.0, (float) $row->total_questions))
            )->first());

        foreach ($rows as $row) {
            $score = $this->percent((float) $row->score, (float) $row->total_questions);
            foreach ($this->matchingCompetencies(
                trim((string) $row->title . ' ' . (string) $row->description),
                (int) $row->order_index,
            ) as $key) {
                $this->addEvidence($evidence, (int) $row->user_id, $key, [
                    'source' => 'challenge',
                    'score' => $score,
                    'weight' => 1.0,
                    'count' => 1,
                    'label' => (string) $row->title,
                    'at' => $row->submitted_at ?: $row->updated_at,
                ]);
            }
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectCodingEvidence(Collection $studentIds, array &$evidence): void
    {
        if (! Schema::hasTable('coding_submissions')) {
            return;
        }

        $rows = DB::table('coding_submissions as submission')
            ->join('coding_questions as question', 'question.id', '=', 'submission.coding_question_id')
            ->join('challenges as challenge', 'challenge.id', '=', 'question.challenge_id')
            ->join('challenge_categories as category', 'category.id', '=', 'challenge.challenge_category_id')
            ->whereIn('submission.user_id', $studentIds)
            ->where('category.slug', 'university-student')
            ->where('submission.voided', false)
            ->where('submission.tests_total', '>', 0)
            ->get([
                'submission.user_id',
                'submission.coding_question_id',
                'submission.tests_passed',
                'submission.tests_total',
                'submission.language',
                'submission.created_at',
                'question.problem_description',
                'challenge.title',
                'challenge.description',
                'challenge.order_index',
            ])
            ->groupBy(fn ($row): string => $row->user_id . ':' . $row->coding_question_id)
            ->map(fn (Collection $attempts) => $attempts->sortByDesc(
                fn ($row): float => ((float) $row->tests_passed / max(1.0, (float) $row->tests_total))
            )->first());

        foreach ($rows as $row) {
            $score = $this->percent((float) $row->tests_passed, (float) $row->tests_total);
            $text = implode(' ', array_filter([$row->title, $row->description, $row->problem_description]));
            foreach ($this->matchingCompetencies($text, (int) $row->order_index, (string) $row->language) as $key) {
                $this->addEvidence($evidence, (int) $row->user_id, $key, [
                    'source' => 'coding',
                    'score' => $score,
                    'weight' => 1.0,
                    'count' => 1,
                    'label' => (string) $row->title,
                    'at' => $row->created_at,
                ]);
            }
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectToolkitEvidence(Collection $studentIds, array &$evidence): void
    {
        if (! Schema::hasTable('student_data_toolkit_activities')) {
            return;
        }

        $activities = DB::table('student_data_toolkit_activities')
            ->whereIn('user_id', $studentIds)
            ->orderBy('created_at')
            ->get(['user_id', 'dataset_key', 'activity_type', 'created_at'])
            ->groupBy('user_id');

        $relevantTypes = [
            'statistics' => ['descriptive_analysis', 'categorical_analysis', 'correlation_analysis', 'regression_analysis', 'outliers_analysis', 'report_view'],
            'data_analysis' => ['descriptive_analysis', 'categorical_analysis', 'correlation_analysis', 'regression_analysis', 'data_quality_analysis', 'outliers_analysis', 'report_view'],
            'eda' => ['view_dataset', 'descriptive_analysis', 'categorical_analysis', 'correlation_analysis', 'data_quality_analysis', 'outliers_analysis', 'report_view'],
            'data_visualization' => ['descriptive_analysis', 'categorical_analysis', 'correlation_analysis', 'regression_analysis', 'data_quality_analysis', 'outliers_analysis', 'report_view'],
        ];

        foreach ($activities as $studentId => $studentActivities) {
            foreach ($relevantTypes as $key => $types) {
                $matched = $studentActivities->whereIn('activity_type', $types);
                if ($matched->isEmpty()) {
                    continue;
                }

                $typeCount = $matched->pluck('activity_type')->unique()->count();
                $datasetCount = $matched->pluck('dataset_key')->unique()->count();
                $reportCount = $matched->where('activity_type', 'report_view')->count();
                $score = min(100, 35 + ($typeCount * 10) + ($datasetCount * 7) + (min(3, $reportCount) * 5));

                $this->addEvidence($evidence, (int) $studentId, $key, [
                    'source' => 'toolkit',
                    'score' => (float) $score,
                    'weight' => 1.0,
                    'count' => $matched->count(),
                    'label' => 'Statistical Data Toolkit',
                    'at' => $matched->max('created_at'),
                ]);
            }
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectModelDevelopmentEvidence(Collection $studentIds, int $classId, array &$evidence): void
    {
        $studentsWithCurrentEvidence = collect();

        // The active Hybrid ML pipeline is the primary source. A model counts
        // only for the exact class selected by the learner; private experiments
        // (class_id NULL) must never appear in every instructor's class report.
        if (Schema::hasTable('training_jobs') && Schema::hasTable('model_versions') && Schema::hasTable('ml_models')) {
            $currentRuns = DB::table('training_jobs as job')
                ->join('model_versions as version', 'version.training_job_id', '=', 'job.id')
                ->join('ml_models as model', 'model.id', '=', 'version.ml_model_id')
                ->whereIn('job.user_id', $studentIds)
                ->where('job.class_id', $classId)
                ->where('model.class_id', $classId)
                ->where('job.status', 'completed')
                ->where('version.status', 'ready')
                ->where('version.is_active', true)
                ->where('model.status', 'ready')
                ->where('model.pipeline_type', 'user')
                ->whereNull('model.deleted_at')
                ->orderBy('job.finished_at')
                ->get([
                    'job.user_id',
                    'job.problem_type',
                    'job.algorithm_key',
                    'job.model_name',
                    'job.finished_at',
                    'job.created_at',
                    'version.metrics',
                ]);

            foreach ($currentRuns as $run) {
                $metrics = json_decode((string) $run->metrics, true) ?: [];
                $score = $this->modelMetricScore((string) $run->problem_type, $metrics);
                $label = trim((string) $run->model_name) !== ''
                    ? (string) $run->model_name
                    : Str::headline((string) $run->algorithm_key);
                $this->addModelEvidence(
                    $evidence,
                    (int) $run->user_id,
                    (string) $run->problem_type,
                    $score,
                    $label,
                    $run->finished_at ?: $run->created_at,
                );
                $studentsWithCurrentEvidence->push((int) $run->user_id);
            }
        }

        // Preserve historical evidence from the retired implementation only for
        // learners who have no current Hybrid ML evidence in this exact class.
        if (! Schema::hasTable('model_development_runs')) {
            return;
        }

        $legacyRuns = DB::table('model_development_runs')
            ->whereIn('user_id', $studentIds)
            ->whereNotIn('user_id', $studentsWithCurrentEvidence->unique()->values())
            ->where('class_id', $classId)
            ->where('status', 'completed')
            ->orderBy('trained_at')
            ->get([
                'user_id', 'dataset_key', 'task_type', 'algorithm',
                'metrics', 'trained_at', 'created_at',
            ]);

        foreach ($legacyRuns as $run) {
            $metrics = json_decode((string) $run->metrics, true) ?: [];
            $score = $this->modelMetricScore((string) $run->task_type, $metrics);
            $label = Str::headline((string) $run->algorithm) . ' — ' . Str::headline((string) $run->dataset_key);
            $this->addModelEvidence(
                $evidence,
                (int) $run->user_id,
                (string) $run->task_type,
                $score,
                $label,
                $run->trained_at ?: $run->created_at,
            );
        }
    }

    /** @param array<string, mixed> $metrics */
    private function modelMetricScore(string $problemType, array $metrics): float
    {
        return match ($problemType) {
            // The trusted Hybrid ML runner stores classification percentages on
            // a 0–100 scale. R2 and silhouette remain unit-scale statistics.
            'classification' => max(0, min(100,
                ((float) ($metrics['accuracy'] ?? 0) + (float) ($metrics['f1'] ?? $metrics['accuracy'] ?? 0)) / 2
            )),
            'regression' => max(0, min(100, (float) ($metrics['r2'] ?? 0) * 100)),
            'clustering' => max(0, min(100, (float) ($metrics['silhouette'] ?? 0) * 100)),
            default => 0.0,
        };
    }

    /**
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function addModelEvidence(
        array &$evidence,
        int $studentId,
        string $problemType,
        float $score,
        string $label,
        mixed $at,
    ): void {
        foreach (['machine_learning', 'data_analysis'] as $key) {
            $this->addEvidence($evidence, $studentId, $key, [
                'source' => 'model',
                'score' => $score,
                'weight' => 1.0,
                'count' => 1,
                'label' => $label,
                'at' => $at,
            ]);
        }

        $this->addEvidence($evidence, $studentId, 'data_visualization', [
            'source' => 'model',
            'score' => min(100, max(40, $score)),
            'weight' => 0.5,
            'count' => 1,
            'label' => $label . ' evaluation charts',
            'at' => $at,
        ]);

        if ($problemType === 'clustering') {
            $this->addEvidence($evidence, $studentId, 'eda', [
                'source' => 'model',
                'score' => min(100, max(40, $score)),
                'weight' => 0.5,
                'count' => 1,
                'label' => $label,
                'at' => $at,
            ]);
        }
    }

    /**
     * @param Collection<int, int> $studentIds
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     */
    private function collectIdeEvidence(Collection $studentIds, array &$evidence): void
    {
        if (! Schema::hasTable('ide_execution_logs') || ! Schema::hasTable('ide_nodes')) {
            return;
        }

        $logs = DB::table('ide_execution_logs as execution')
            ->join('ide_nodes as node', 'node.id', '=', 'execution.node_id')
            ->whereIn('execution.user_id', $studentIds)
            ->where(function ($query): void {
                $query->where('node.language', 'python')
                    ->orWhere('node.name', 'like', '%.py');
            })
            ->get(['execution.user_id', 'execution.exit_code', 'execution.created_at'])
            ->groupBy('user_id');

        foreach ($logs as $studentId => $studentLogs) {
            $total = $studentLogs->count();
            if ($total <= 0) {
                continue;
            }

            $successful = $studentLogs->where('exit_code', 0)->count();
            $this->addEvidence($evidence, (int) $studentId, 'python_programming', [
                'source' => 'ide',
                'score' => round(($successful / $total) * 100, 2),
                'weight' => 1.0,
                'count' => $total,
                'label' => 'Python IDE executions',
                'at' => $studentLogs->max('created_at'),
            ]);
        }
    }

    /**
     * @param Collection<int, array<string, mixed>> $items
     * @return array<string, mixed>
     */
    private function calculate(Collection $items): array
    {
        if ($items->isEmpty()) {
            return [
                'percentage' => 0.0,
                'evidence_count' => 0,
                'level' => 'Not Assessed',
                'source_breakdown' => [],
                'last_evidence_at' => null,
            ];
        }

        $sourceBreakdown = [];
        $weightedScore = 0.0;
        $availableSourceWeight = 0.0;
        $evidenceCount = 0;
        $lastEvidenceAt = null;

        foreach ($items->groupBy('source') as $source => $sourceItems) {
            $itemWeight = max(1.0, (float) $sourceItems->sum('weight'));
            $score = round((float) $sourceItems->sum(
                fn (array $item): float => (float) $item['score'] * max(0.01, (float) $item['weight'])
            ) / $itemWeight, 2);
            $count = (int) $sourceItems->sum('count');
            $sourceWeight = (float) (self::SOURCE_WEIGHTS[$source] ?? 10);
            $latest = $sourceItems->pluck('at')->filter()->sortDesc()->first();

            $weightedScore += $score * $sourceWeight;
            $availableSourceWeight += $sourceWeight;
            $evidenceCount += $count;
            $lastEvidenceAt = $this->latestDate($lastEvidenceAt, $latest);

            $sourceBreakdown[$source] = [
                'score' => $score,
                'evidence_count' => $count,
                'weight' => $sourceWeight,
                'last_evidence_at' => $latest,
            ];
        }

        $percentage = $availableSourceWeight > 0
            ? round(min(100, max(0, $weightedScore / $availableSourceWeight)), 2)
            : 0.0;

        return [
            'percentage' => $percentage,
            'evidence_count' => $evidenceCount,
            'level' => $this->levelFor($percentage, $evidenceCount),
            'source_breakdown' => $sourceBreakdown,
            'last_evidence_at' => $lastEvidenceAt,
        ];
    }

    /**
     * @return array<int, string>
     */
    private function matchingCompetencies(
        string $text,
        ?int $moduleNo = null,
        ?string $language = null,
    ): array {
        $keys = collect($moduleNo ? (self::MODULE_COMPETENCIES[$moduleNo] ?? []) : []);
        $haystack = Str::lower(strip_tags($text));
        $language = Str::lower(trim((string) $language));

        if (in_array($language, ['python', 'py'], true)) {
            $keys->push('python_programming');
        }
        if (in_array($language, ['sql', 'mysql', 'sqlite', 'postgresql'], true)) {
            $keys->push('sql');
        }

        foreach (self::KEYWORDS as $key => $keywords) {
            foreach ($keywords as $keyword) {
                if ($this->containsKeyword($haystack, $keyword)) {
                    $keys->push($key);
                    break;
                }
            }
        }

        return $keys->unique()->values()->all();
    }

    private function containsKeyword(string $haystack, string $keyword): bool
    {
        if ($keyword === 'sql' || $keyword === 'eda') {
            return preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $haystack) === 1;
        }

        return str_contains($haystack, Str::lower($keyword));
    }

    /**
     * @param array<int, array<string, array<int, array<string, mixed>>>> $evidence
     * @param array<string, mixed> $item
     */
    private function addEvidence(array &$evidence, int $studentId, string $competencyKey, array $item): void
    {
        $evidence[$studentId][$competencyKey][] = $item;
    }

    private function percent(float $earned, float $possible): float
    {
        return $possible > 0
            ? round(min(100, max(0, ($earned / $possible) * 100)), 2)
            : 0.0;
    }

    private function attemptSortKey(object $row): string
    {
        return str_pad((string) ((int) $row->attempt_no), 10, '0', STR_PAD_LEFT)
            . ':' . str_pad((string) ((int) $row->id), 20, '0', STR_PAD_LEFT);
    }

    private function latestDate(mixed $current, mixed $candidate): mixed
    {
        if (! $candidate) {
            return $current;
        }
        if (! $current) {
            return $candidate;
        }

        return strtotime((string) $candidate) > strtotime((string) $current) ? $candidate : $current;
    }

    /**
     * @return object
     */
    private function emptySnapshot(Competency $competency): object
    {
        return (object) [
            'competency_id' => $competency->id,
            'percentage' => 0.0,
            'evidence_count' => 0,
            'level' => 'Not Assessed',
            'source_breakdown' => [],
            'last_evidence_at' => null,
            'calculated_at' => null,
        ];
    }

    /**
     * @param Collection<int, Competency> $competencies
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function classTrends(ClassRoom $class, Collection $competencies): array
    {
        if (! Schema::hasTable('student_competency_trends')) {
            return [];
        }

        $dates = DB::table('student_competency_trends')
            ->where('class_id', $class->id)
            ->orderByDesc('recorded_on')
            ->distinct()
            ->limit(8)
            ->pluck('recorded_on')
            ->sort()
            ->values();

        if ($dates->isEmpty()) {
            return [];
        }

        $rows = DB::table('student_competency_trends')
            ->where('class_id', $class->id)
            ->whereIn('recorded_on', $dates)
            ->where('evidence_count', '>', 0)
            ->selectRaw('competency_id, recorded_on, AVG(percentage) as average_percentage')
            ->groupBy('competency_id', 'recorded_on')
            ->get()
            ->groupBy('competency_id');

        return $competencies->mapWithKeys(function (Competency $competency) use ($rows, $dates): array {
            $points = $rows->get($competency->id, collect())->keyBy('recorded_on');

            return [$competency->key => $dates->map(fn ($date): array => [
                'date' => (string) $date,
                'percentage' => round((float) ($points->get($date)->average_percentage ?? 0), 1),
            ])->all()];
        })->all();
    }

    /**
     * @param Collection<int, Competency> $competencies
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function studentTrends(User $student, ClassRoom $class, Collection $competencies): array
    {
        if (! Schema::hasTable('student_competency_trends')) {
            return [];
        }

        $rows = StudentCompetencyTrend::query()
            ->where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->whereIn('competency_id', $competencies->pluck('id'))
            ->orderByDesc('recorded_on')
            ->get()
            ->groupBy('competency_id');

        return $competencies->mapWithKeys(function (Competency $competency) use ($rows): array {
            return [$competency->key => $rows->get($competency->id, collect())
                ->take(8)
                ->sortBy('recorded_on')
                ->map(fn (StudentCompetencyTrend $trend): array => [
                    'date' => $trend->recorded_on->toDateString(),
                    'percentage' => round((float) $trend->percentage, 1),
                ])
                ->values()
                ->all()];
        })->all();
    }
}
