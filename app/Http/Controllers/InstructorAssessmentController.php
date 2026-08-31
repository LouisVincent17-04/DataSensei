<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentQuestionOption;
use App\Models\AssessmentSubmission;
use App\Models\ClassRoom;
use App\Models\StudentAssessmentDiagnostic;
use App\Models\TableOfSpecification;
use App\Models\TableOfSpecificationRow;
use App\Services\AssessmentDiagnosticService;
use App\Services\IloMasteryService;
use App\Services\StudentNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstructorAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $classIds = ClassRoom::where('instructor_id', Auth::id())->pluck('id');

        $query = Assessment::with(['classRoom', 'tos'])
            ->withCount([
                'questions',
                'submissions' => fn ($submissions) => $submissions
                    ->whereIn('status', ['submitted', 'late', 'graded']),
            ])
            ->where(function ($q) use ($classIds) {
                $q->whereIn('class_id', $classIds)
                    ->orWhere(function ($orphaned) {
                        $orphaned->whereNull('class_id')
                            ->where('created_by', Auth::id());
                    });
            })
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $assessments = $query->paginate(12)->withQueryString();

        return view('instructor.assessments.index', compact('assessments'));
    }

    public function create(TableOfSpecification $tos)
    {
        $this->authorizeTos($tos);
        $tos->load(['rows.ilo', 'classRoom']);

        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $totalItems = (int) $tos->rows->sum('item_count');
        $targetItems = max(1, (int) ($tos->total_items ?: $totalItems));
        abort_if($totalItems < 1, 422, 'The selected TOS has no assessment items. Add item allocations first.');
        abort_if($totalItems !== $targetItems, 422, "The selected TOS is not complete. Assign exactly {$targetItems} items before generating an assessment.");

        return view('instructor.assessments.create', compact('tos', 'classes', 'totalItems'));
    }

    public function store(Request $request, TableOfSpecification $tos)
    {
        $this->authorizeTos($tos);

        $validated = $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:191'],
            'description' => ['nullable', 'string', 'max:5000'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'available_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:available_at'],
        ]);

        $assessment = DB::transaction(function () use ($validated, $tos): Assessment {
            $lockedTos = TableOfSpecification::query()
                ->whereKey($tos->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeTos($lockedTos);
            $lockedTos->load('rows.ilo');

            $class = ClassRoom::query()
                ->whereKey($validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where('is_archived', false)
                ->lockForUpdate()
                ->firstOrFail();

            $totalItems = (int) $lockedTos->rows->sum('item_count');
            $targetItems = max(1, (int) ($lockedTos->total_items ?: $totalItems));
            abort_if($totalItems < 1, 422, 'The selected TOS has no assessment items.');
            abort_if($totalItems !== $targetItems, 422, "The selected TOS is not complete. Assign exactly {$targetItems} items before generating an assessment.");

            $totalPoints = (int) $lockedTos->rows
                ->sum(fn ($row) => $row->item_count * max(1, (int) $row->default_points));

            $assessment = Assessment::create([
                'table_of_specification_id' => $lockedTos->id,
                'class_id' => $class->id,
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'status' => 'draft',
                'draft_last_item' => 1,
                'draft_saved_at' => now(),
                'total_items' => $totalItems,
                'total_points' => $totalPoints,
                'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
                'max_attempts' => $validated['max_attempts'],
                'available_at' => $validated['available_at'] ?? null,
                'due_at' => $validated['due_at'] ?? null,
            ]);

            $itemNumber = 1;
            foreach ($lockedTos->rows->sortBy('id') as $row) {
                for ($i = 0; $i < (int) $row->item_count; $i++) {
                    AssessmentQuestion::create([
                        'assessment_id' => $assessment->id,
                        'table_of_specification_row_id' => $row->id,
                        'ilo_id' => $row->ilo_id,
                        'item_number' => $itemNumber++,
                        'question_type' => 'unconfigured',
                        'points' => max(1, (int) $row->default_points),
                        'is_required' => true,
                        'topic_title' => $row->topic_title,
                        'subtopic_title' => $row->subtopic_title,
                        'learning_objective' => $row->learning_objective ?: ($row->ilo->description ?? $row->ilo->title ?? null),
                        'bloom_level' => $row->cognitive_level,
                        'difficulty_slug' => $row->difficulty_slug,
                    ]);
                }
            }

            return $assessment;
        }, 3);

        return redirect()
            ->route('instructor.assessments.builder', $assessment)
            ->with('success', 'Assessment draft created. Items 1–' . $assessment->total_items . ' are ready for TOS-guided authoring.');
    }

    public function builder(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->load(['tos', 'classRoom']);

        $questions = $assessment->questions()
            ->with('options')
            ->orderBy('item_number')
            ->get();

        abort_if($questions->isEmpty(), 422, 'This assessment has no TOS items to configure.');

        $assessment->setRelation('questions', $questions);

        $requestedItem = max(0, (int) $request->integer('item'));
        $resumeItem = $requestedItem ?: max(0, (int) $assessment->draft_last_item);
        $currentQuestion = $questions->firstWhere('item_number', $resumeItem)
            ?? $questions->first(fn (AssessmentQuestion $question) => ! $question->isAuthoringComplete())
            ?? $questions->first();

        $complete = $questions->filter(
            fn (AssessmentQuestion $question) => $question->isAuthoringComplete()
        )->count();
        $inProgress = $questions->filter(
            fn (AssessmentQuestion $question) => $question->authoring_status === 'in_progress'
        )->count();
        $total = $questions->count();

        $progress = [
            'complete' => $complete,
            'in_progress' => $inProgress,
            'not_started' => max(0, $total - $complete - $inProgress),
            'total' => $total,
            'percent' => $total > 0 ? (int) round(($complete / $total) * 100) : 0,
        ];

        $tosGroups = $questions
            ->groupBy(function (AssessmentQuestion $question): string {
                if ($question->table_of_specification_row_id !== null) {
                    return 'row:' . $question->table_of_specification_row_id;
                }

                return 'coverage:' . implode('|', [
                    $question->topic_title,
                    $question->subtopic_title,
                    $question->bloom_level,
                    $question->difficulty_slug,
                ]);
            })
            ->map(function ($group): array {
                $first = $group->first();

                return [
                    'topic' => $first->topic_title,
                    'subtopic' => $first->subtopic_title,
                    'objective' => $first->learning_objective,
                    'bloom' => $first->bloom_level,
                    'difficulty' => $first->difficulty_slug,
                    'target' => $group->count(),
                    'complete' => $group->filter(
                        fn (AssessmentQuestion $question) => $question->isAuthoringComplete()
                    )->count(),
                    'items' => $group->values(),
                ];
            })
            ->values();

        $previousQuestion = $questions
            ->filter(fn (AssessmentQuestion $question) => $question->item_number < $currentQuestion->item_number)
            ->last();
        $nextQuestion = $questions
            ->first(fn (AssessmentQuestion $question) => $question->item_number > $currentQuestion->item_number);
        $readyToPublish = $complete === $total;

        return view('instructor.assessments.builder', compact(
            'assessment',
            'currentQuestion',
            'previousQuestion',
            'nextQuestion',
            'progress',
            'tosGroups',
            'readyToPublish'
        ));
    }

    public function applyQuickSetup(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        abort_unless($assessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

        $validated = $request->validate([
            'current_question_id' => ['required', 'integer', 'exists:assessment_questions,id'],
            'scope' => ['required', Rule::in(['tos_group', 'all_unstarted'])],
            'question_type' => ['required', Rule::in(array_keys(AssessmentQuestion::TYPES))],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $result = DB::transaction(function () use ($assessment, $validated): array {
            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeAssessment($lockedAssessment);
            abort_unless($lockedAssessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

            $currentQuestion = AssessmentQuestion::query()
                ->whereKey($validated['current_question_id'])
                ->where('assessment_id', $lockedAssessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $questions = AssessmentQuestion::query()
                ->where('assessment_id', $lockedAssessment->id)
                ->where('question_type', 'unconfigured')
                ->where(function ($query) {
                    $query->whereNull('question_text')->orWhere('question_text', '');
                })
                ->where(function ($query) {
                    $query->whereNull('correct_answer')->orWhere('correct_answer', '');
                })
                ->where(function ($query) {
                    $query->whereNull('answer_explanation')->orWhere('answer_explanation', '');
                })
                ->where(function ($query) {
                    $query->whereNull('rubric_text')->orWhere('rubric_text', '');
                })
                ->where(function ($query) {
                    $query->whereNull('image_path')->orWhere('image_path', '');
                })
                ->whereDoesntHave('options');

            if ($validated['scope'] === 'tos_group') {
                if ($currentQuestion->table_of_specification_row_id === null) {
                    $questions
                        ->whereNull('table_of_specification_row_id')
                        ->where('topic_title', $currentQuestion->topic_title)
                        ->where('bloom_level', $currentQuestion->bloom_level)
                        ->where('difficulty_slug', $currentQuestion->difficulty_slug);
                } else {
                    $questions->where(
                        'table_of_specification_row_id',
                        $currentQuestion->table_of_specification_row_id
                    );
                }
            }

            $updated = $questions->update([
                'question_type' => $validated['question_type'],
                'points' => (int) $validated['points'],
                'updated_at' => now(),
            ]);

            $lockedAssessment->update([
                'total_points' => (int) $lockedAssessment->questions()->sum('points'),
                'draft_last_item' => $currentQuestion->item_number,
                'draft_saved_at' => now(),
            ]);

            return [
                'updated' => $updated,
                'item_number' => $currentQuestion->item_number,
            ];
        }, 3);

        $scopeLabel = $validated['scope'] === 'tos_group'
            ? 'this TOS section'
            : 'all not-started items';

        return redirect()
            ->route('instructor.assessments.builder', [
                'assessment' => $assessment,
                'item' => $result['item_number'],
            ])
            ->with('success', $result['updated'] > 0
                ? "Quick setup applied to {$result['updated']} item(s) in {$scopeLabel}."
                : 'No not-started items needed the quick setup.');
    }

    public function updateQuestion(Request $request, Assessment $assessment, AssessmentQuestion $question)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);
        abort_if($assessment->status !== 'draft', 422, 'Published or closed assessments cannot be edited.');

        $validated = $request->validate([
            'intent' => ['nullable', Rule::in(['draft', 'draft_exit', 'complete_next'])],
            'question_type' => [
                'required',
                Rule::in(array_merge(['unconfigured'], array_keys(AssessmentQuestion::TYPES))),
            ],
            'question_text' => ['nullable', 'string', 'max:30000'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_required' => ['nullable', 'boolean'],
            'correct_answer' => ['nullable', 'string', 'max:10000'],
            'answer_explanation' => ['nullable', 'string', 'max:10000'],
            'rubric_text' => ['nullable', 'string', 'max:30000'],
            'question_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'option_texts' => ['nullable', 'array', 'max:10'],
            'option_texts.*' => ['nullable', 'string', 'max:5000'],
            'correct_option' => ['nullable', 'integer', 'min:0', 'max:9'],
        ]);

        $type = $validated['question_type'];
        $intent = $validated['intent'] ?? 'draft';
        $requiresCompleteQuestion = $intent === 'complete_next';
        $questionText = trim((string) ($validated['question_text'] ?? ''));
        $options = collect();
        $correctIndex = $validated['correct_option'] ?? null;

        if ($type === 'multiple_choice') {
            $options = collect($validated['option_texts'] ?? [])
                ->map(fn ($text, $index) => [
                    'source_index' => (int) $index,
                    'text' => trim((string) $text),
                ])
                ->filter(fn (array $option) => $option['text'] !== '')
                ->values();

            if ($requiresCompleteQuestion && $options->count() < 2) {
                throw ValidationException::withMessages([
                    'option_texts' => 'A multiple-choice question needs at least two choices.',
                ]);
            }

            if ($requiresCompleteQuestion
                && ($correctIndex === null || ! $options->contains('source_index', (int) $correctIndex))) {
                throw ValidationException::withMessages([
                    'correct_option' => 'Select exactly one correct choice.',
                ]);
            }
        }

        if ($requiresCompleteQuestion && $type === 'unconfigured') {
            throw ValidationException::withMessages([
                'question_type' => 'Choose a question type before marking this item complete.',
            ]);
        }

        if ($requiresCompleteQuestion && $questionText === '') {
            throw ValidationException::withMessages([
                'question_text' => 'Enter the question before moving to the next item.',
            ]);
        }

        if ($requiresCompleteQuestion
            && in_array($type, ['fill_blank', 'short_answer'], true)
            && trim((string) ($validated['correct_answer'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'correct_answer' => 'Enter the correct or accepted answer for this question type.',
            ]);
        }

        if ($requiresCompleteQuestion
            && $type === 'true_false'
            && ! in_array(strtolower(trim((string) ($validated['correct_answer'] ?? ''))), ['true', 'false'], true)) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Select True or False as the correct answer.',
            ]);
        }

        if ($requiresCompleteQuestion
            && $type === 'essay'
            && trim((string) ($validated['rubric_text'] ?? '')) === '') {
            throw ValidationException::withMessages([
                'rubric_text' => 'Essay questions require a scoring rubric.',
            ]);
        }

        $newImagePath = $request->hasFile('question_image')
            ? $request->file('question_image')->store('assessment-images', 'public')
            : null;

        if ($newImagePath === false) {
            throw ValidationException::withMessages([
                'question_image' => 'The image could not be stored. Try again.',
            ]);
        }

        try {
            $result = DB::transaction(function () use (
                $request,
                $validated,
                $question,
                $assessment,
                $type,
                $intent,
                $questionText,
                $options,
                $correctIndex,
                $newImagePath
            ): array {
                $lockedAssessment = Assessment::query()
                    ->whereKey($assessment->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $this->authorizeAssessment($lockedAssessment);
                abort_unless($lockedAssessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

                $lockedQuestion = AssessmentQuestion::query()
                    ->whereKey($question->id)
                    ->where('assessment_id', $lockedAssessment->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldImagePath = $lockedQuestion->image_path;
                $imagePath = $newImagePath
                    ?? ($request->boolean('remove_image') ? null : $oldImagePath);

                $lockedQuestion->update([
                    'question_type' => $type,
                    'question_text' => $questionText !== '' ? $questionText : null,
                    'image_path' => $imagePath,
                    'points' => $validated['points'],
                    'is_required' => $request->boolean('is_required'),
                    'correct_answer' => in_array($type, ['true_false', 'fill_blank', 'short_answer'], true)
                        ? (trim((string) ($validated['correct_answer'] ?? '')) ?: null)
                        : null,
                    'answer_explanation' => $validated['answer_explanation'] ?? null,
                    'rubric_text' => $type === 'essay' ? ($validated['rubric_text'] ?? null) : null,
                ]);

                $lockedQuestion->options()->delete();

                if ($type === 'multiple_choice') {
                    foreach ($options as $index => $option) {
                        AssessmentQuestionOption::create([
                            'assessment_question_id' => $lockedQuestion->id,
                            'option_label' => chr(65 + $index),
                            'option_text' => $option['text'],
                            'is_correct' => $correctIndex !== null
                                && $option['source_index'] === (int) $correctIndex,
                            'order_index' => $index + 1,
                        ]);
                    }
                }

                $allQuestions = $lockedAssessment->questions()
                    ->with('options')
                    ->orderBy('item_number')
                    ->get();
                $nextIncomplete = $allQuestions->first(
                    fn (AssessmentQuestion $candidate) => $candidate->item_number > $lockedQuestion->item_number
                        && ! $candidate->isAuthoringComplete()
                ) ?? $allQuestions->first(
                    fn (AssessmentQuestion $candidate) => ! $candidate->isAuthoringComplete()
                );
                $nextSequential = $allQuestions->first(
                    fn (AssessmentQuestion $candidate) => $candidate->item_number > $lockedQuestion->item_number
                );
                $destinationItem = $intent === 'complete_next'
                    ? (int) ($nextIncomplete?->item_number ?? $nextSequential?->item_number ?? $lockedQuestion->item_number)
                    : (int) $lockedQuestion->item_number;

                $lockedAssessment->update([
                    'total_points' => (int) $allQuestions->sum('points'),
                    'draft_last_item' => $destinationItem,
                    'draft_saved_at' => now(),
                ]);

                return [
                    'item_number' => $lockedQuestion->item_number,
                    'destination_item' => $destinationItem,
                    'old_image_path' => $oldImagePath && $oldImagePath !== $imagePath
                        ? $oldImagePath
                        : null,
                ];
            }, 3);
        } catch (\Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($result['old_image_path']) {
            Storage::disk('public')->delete($result['old_image_path']);
        }

        if ($intent === 'draft_exit') {
            return redirect()
                ->route('instructor.assessments.index')
                ->with('success', 'Assessment saved as a draft. You can continue from item ' . $result['item_number'] . ' later.');
        }

        return redirect()
            ->route('instructor.assessments.builder', [
                'assessment' => $assessment,
                'item' => $result['destination_item'],
            ])
            ->with('success', $intent === 'complete_next'
                ? 'Item ' . $result['item_number'] . ' completed and saved.'
                : 'Draft for item ' . $result['item_number'] . ' saved.');
    }

    public function publish(Assessment $assessment, StudentNotificationService $notifications)
    {
        $this->authorizeAssessment($assessment);

        DB::transaction(function () use ($assessment): void {
            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeAssessment($lockedAssessment);
            abort_unless($lockedAssessment->status === 'draft', 422, 'Only draft assessments can be published.');
            $lockedAssessment->load('questions.options');

            $errors = [];
            if ($lockedAssessment->questions->isEmpty()) {
                $errors[] = 'The assessment has no questions.';
            }

            foreach ($lockedAssessment->questions as $question) {
                foreach ($question->authoringErrors() as $error) {
                    $errors[] = 'Item ' . $question->item_number . ': ' . $error;
                }
            }

            if ($errors !== []) {
                throw ValidationException::withMessages(['assessment' => $errors]);
            }

            $lockedAssessment->update([
                'status' => 'published',
                'published_at' => now(),
                'total_items' => $lockedAssessment->questions->count(),
                'total_points' => (int) $lockedAssessment->questions->sum('points'),
            ]);
        }, 3);

        $notifications->assessmentPublished($assessment->fresh());

        return back()->with('success', 'Assessment published to students.');
    }

    public function close(Assessment $assessment, StudentNotificationService $notifications)
    {
        $this->authorizeAssessment($assessment);

        DB::transaction(function () use ($assessment): void {
            $lockedAssessment = Assessment::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeAssessment($lockedAssessment);
            abort_unless($lockedAssessment->status === 'published', 422, 'Only a published assessment can be closed.');
            $lockedAssessment->update(['status' => 'closed']);
        }, 3);

        $notifications->assessmentClosed($assessment->fresh());

        return back()->with('success', 'Assessment closed.');
    }

    public function submissions(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->load(['classRoom']);

        $submissions = $assessment->submissions()
            ->with('student')
            ->whereIn('status', ['submitted', 'late', 'graded'])
            ->latest('submitted_at')
            ->paginate(30);

        return view('instructor.assessments.submissions', compact('assessment', 'submissions'));
    }

    public function showSubmission(Assessment $assessment, AssessmentSubmission $submission)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $submission->assessment_id === (int) $assessment->id, 404);
        abort_unless(
            in_array($submission->status, ['submitted', 'late', 'graded'], true),
            422,
            'An in-progress assessment cannot be graded.'
        );

        $submission->load([
            'student',
            'answers.question.options',
            'answers.selectedOption',
        ]);
        $assessment->load('questions');

        return view('instructor.assessments.submission-show', compact('assessment', 'submission'));
    }

    public function gradeSubmission(Request $request, Assessment $assessment, AssessmentSubmission $submission, AssessmentDiagnosticService $diagnostics, IloMasteryService $iloMastery, StudentNotificationService $notifications)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $submission->assessment_id === (int) $assessment->id, 404);

        $submission->load('answers.question');

        $validated = $request->validate([
            'scores' => ['nullable', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0'],
            'feedbacks' => ['nullable', 'array'],
            'feedbacks.*' => ['nullable', 'string', 'max:10000'],
            'feedback' => ['nullable', 'string', 'max:10000'],
        ]);

        DB::transaction(function () use ($assessment, $submission, $validated) {
            $lockedSubmission = AssessmentSubmission::whereKey($submission->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless(
                (int) $lockedSubmission->assessment_id === (int) $assessment->id,
                404
            );
            $lockedSubmission->load('answers.question');

            abort_unless(
                in_array($lockedSubmission->status, ['submitted', 'late', 'graded'], true),
                422,
                'An in-progress assessment cannot be graded.'
            );

            foreach ($lockedSubmission->answers as $answer) {
                if ($answer->question->question_type !== 'essay') {
                    continue;
                }

                $score = (float) ($validated['scores'][$answer->id] ?? 0);
                $score = min($score, (float) $answer->question->points);

                $answer->update([
                    'points_awarded' => $score,
                    'is_correct' => $score >= (float) $answer->question->points,
                    'instructor_feedback' => $validated['feedbacks'][$answer->id] ?? null,
                ]);
            }

            $score = (float) $lockedSubmission->answers()->sum('points_awarded');
            $lockedSubmission->update([
                'score' => $score,
                'status' => $lockedSubmission->status === 'late' ? 'late' : 'graded',
                'graded_at' => now(),
                'feedback' => $validated['feedback'] ?? null,
            ]);
        }, 3);

        $gradedSubmission = $submission->fresh();
        $diagnostics->refresh($gradedSubmission);
        $iloMastery->refreshForAssessmentSubmission($gradedSubmission);

        $percentage = $gradedSubmission->total_points > 0
            ? round(((float) $gradedSubmission->score / (float) $gradedSubmission->total_points) * 100, 1)
            : 0;
        $notifications->send(
            (int) $gradedSubmission->student_id,
            'assessment_graded',
            'Assessment result available',
            'Your result for “' . $assessment->title . '” is available: ' . $percentage . '%.',
            route('student.assessments.result', [$assessment, $gradedSubmission]),
            ['assessment_id' => $assessment->id, 'submission_id' => $gradedSubmission->id, 'percentage' => $percentage],
            'assessment-graded:' . $gradedSubmission->id . ':' . optional($gradedSubmission->graded_at)->format('YmdHis')
        );

        return back()->with('success', 'Submission graded and diagnostics recalculated.');
    }

    public function analytics(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->load(['classRoom', 'tos']);

        $diagnostics = StudentAssessmentDiagnostic::with('student')
            ->where('assessment_id', $assessment->id)
            ->orderBy('student_id')
            ->get();

        $byTopic = $this->aggregateDiagnostics($diagnostics, 'topic_title');
        $byObjective = $this->aggregateDiagnostics($diagnostics, 'learning_objective');
        $byBloom = $this->aggregateDiagnostics($diagnostics, 'bloom_level');

        return view('instructor.assessments.analytics', compact(
            'assessment',
            'diagnostics',
            'byTopic',
            'byObjective',
            'byBloom'
        ));
    }

    private function aggregateDiagnostics($diagnostics, string $field)
    {
        return $diagnostics
            ->groupBy(fn ($row) => trim((string) $row->{$field}) ?: 'Not specified')
            ->map(function ($rows, $label) {
                return [
                    'label' => $label,
                    'students' => $rows->pluck('student_id')->unique()->count(),
                    'average_mastery' => round((float) $rows->avg('mastery_percent'), 2),
                    'needs_support' => $rows->where('proficiency_label', 'Needs Support')->count(),
                    'pending_review' => $rows->where('manual_review_pending', true)->count(),
                ];
            })
            ->sortBy('average_mastery')
            ->values();
    }

    private function authorizeTos(TableOfSpecification $tos): void
    {
        if ($tos->class_id) {
            $owned = ClassRoom::where('id', $tos->class_id)
                ->where('instructor_id', Auth::id())
                ->exists();
            abort_unless($owned, 403);
        } else {
            abort_unless((int) $tos->created_by === (int) Auth::id(), 403);
        }
    }

    private function authorizeAssessment(Assessment $assessment): void
    {
        $assessment->loadMissing('classRoom');

        $ownedClass = $assessment->classRoom
            && (int) $assessment->classRoom->instructor_id === (int) Auth::id();
        $ownedOrphan = !$assessment->class_id
            && (int) $assessment->created_by === (int) Auth::id();

        abort_unless($ownedClass || $ownedOrphan, 403);
    }
}
