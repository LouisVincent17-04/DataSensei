<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestionIlo;
use App\Models\AssignmentLibraryItem;
use App\Models\IntendedLearningOutcome;
use App\Services\PlatformContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAssessmentContentController extends Controller
{
    public function __construct(private readonly PlatformContentService $contentService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', 'all');
        $type = (string) $request->input('type', 'all');

        $assessments = AssignmentLibraryItem::query()
            ->withCount(['questions', 'classAssignments'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('topic_title', 'like', "%{$search}%")
                        ->orWhere('assignment_code', 'like', "%{$search}%")
                        ->orWhere('version_code', 'like', "%{$search}%")
                        ->orWhere('module_no', 'like', "%{$search}%");
                });
            })
            ->when($type !== 'all', fn ($query) => $query->where('assignment_type', $type))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->paginate(12)
            ->withQueryString();

        return view('admin.assessments.index', compact('assessments', 'search', 'status', 'type'));
    }

    public function create(Request $request): View
    {
        $moduleNo = max(1, (int) $request->integer('module_no', 1));
        $versionNo = $this->contentService->nextAssessmentVersion($moduleNo);

        return view('admin.assessments.create', [
            'assessment' => new AssignmentLibraryItem([
                'module_no' => $moduleNo,
                'assignment_type' => 'mcq',
                'version_no' => $versionNo,
                'version_name' => 'Version ' . $versionNo,
                'version_code' => 'V' . $versionNo,
                'time_limit_minutes' => 20,
                'sort_order' => $moduleNo,
                'is_active' => false,
            ]),
            'questions' => old('questions', [$this->emptyMcqQuestion()]),
            'ilos' => $this->availableIlos(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $questions] = $this->validatedData($request);

        $assessment = DB::transaction(function () use ($data, $questions): AssignmentLibraryItem {
            $assessment = AssignmentLibraryItem::create($this->assessmentPayload($data, $questions));
            $this->syncQuestions($assessment, $questions);

            return $assessment;
        });

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('success', 'Assessment content version created successfully.');
    }

    public function show(AssignmentLibraryItem $assessment): View
    {
        $assessment->load(['questions.options', 'questions.blankAnswers', 'questions.iloMappings.ilo'])
            ->loadCount('classAssignments');

        return view('admin.assessments.show', [
            'assessment' => $assessment,
            'hasReferences' => $this->contentService->assessmentHasReferences($assessment),
            'nextVersionNo' => $this->contentService->nextAssessmentVersion((int) $assessment->module_no),
        ]);
    }

    public function edit(AssignmentLibraryItem $assessment): View
    {
        $assessment->load(['questions.options', 'questions.blankAnswers', 'questions.iloMappings']);

        return view('admin.assessments.edit', [
            'assessment' => $assessment,
            'questions' => old('questions', $this->questionsForForm($assessment)),
            'hasReferences' => $this->contentService->assessmentHasReferences($assessment),
            'ilos' => $this->availableIlos(),
        ]);
    }

    public function update(Request $request, AssignmentLibraryItem $assessment): RedirectResponse
    {
        [$data, $questions] = $this->validatedData($request, $assessment);
        DB::transaction(function () use ($assessment, $data, $questions): void {
            $lockedAssessment = AssignmentLibraryItem::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $hasReferences = $this->contentService->assessmentHasReferences($lockedAssessment);

            if ($hasReferences) {
                $this->assertReferencedAssessmentIdentityUnchanged($lockedAssessment, $data);

                if ($this->contentService->assessmentQuestionsChanged($lockedAssessment, $questions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'This assessment version is already used by a class assignment. Duplicate it as a new version before changing its questions or answers.',
                    ]);
                }
            }

            $lockedAssessment->update($this->assessmentPayload($data, $questions));

            if (! $hasReferences) {
                $this->syncQuestions($lockedAssessment, $questions);
            }
        }, 3);

        return redirect()
            ->route('admin.assessments.show', $assessment)
            ->with('success', 'Assessment content version updated successfully.');
    }

    public function duplicate(Request $request, AssignmentLibraryItem $assessment): RedirectResponse
    {
        $data = $request->validate([
            'assignment_code' => ['required', 'string', 'max:189', 'unique:assignment_library_items,assignment_code'],
            'version_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('assignment_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $assessment->module_no)),
            ],
            'version_name' => ['required', 'string', 'max:189'],
            'version_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assignment_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $assessment->module_no)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $copy = DB::transaction(function () use ($assessment, $data): AssignmentLibraryItem {
            $source = AssignmentLibraryItem::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $source->load(['questions.options', 'questions.blankAnswers', 'questions.iloMappings']);

            $copy = AssignmentLibraryItem::create([
                'module_no' => $source->module_no,
                'assignment_code' => strtoupper(trim($data['assignment_code'])),
                'title' => $source->title,
                'topic_title' => $source->topic_title,
                'year_level' => $source->year_level,
                'assignment_type' => $source->assignment_type,
                'version_no' => (int) $data['version_no'],
                'version_name' => trim($data['version_name']),
                'version_code' => strtoupper(trim($data['version_code'])),
                'description' => $source->description,
                'instructions' => $source->instructions,
                'time_limit_minutes' => $source->time_limit_minutes,
                'total_points' => $source->total_points,
                'sort_order' => $source->sort_order,
                'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
            ]);

            foreach ($source->questions as $question) {
                $newQuestion = $copy->questions()->create([
                    'question_type' => $question->question_type,
                    'question_text' => $question->question_text,
                    'points' => $question->points,
                    'order_index' => $question->order_index,
                    'explanation' => $question->explanation,
                ]);

                foreach ($question->options as $option) {
                    $newQuestion->options()->create([
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'order_index' => $option->order_index,
                    ]);
                }

                foreach ($question->blankAnswers as $answer) {
                    $newQuestion->blankAnswers()->create([
                        'answer_text' => $answer->answer_text,
                        'is_case_sensitive' => $answer->is_case_sensitive,
                    ]);
                }

                foreach ($question->iloMappings as $mapping) {
                    AssessmentQuestionIlo::create([
                        'ilo_id' => $mapping->ilo_id,
                        'assessment_source' => 'assignment',
                        'question_id' => $newQuestion->id,
                        'weight' => max(1, (int) $mapping->weight),
                    ]);
                }
            }

            return $copy;
        }, 3);

        return redirect()
            ->route('admin.assessments.edit', $copy)
            ->with('success', 'Assessment duplicated as a new version. Review it before publishing.');
    }

    public function toggleStatus(AssignmentLibraryItem $assessment): RedirectResponse
    {
        $isActive = DB::transaction(function () use ($assessment): bool {
            $lockedAssessment = AssignmentLibraryItem::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedAssessment->update(['is_active' => ! $lockedAssessment->is_active]);

            return (bool) $lockedAssessment->is_active;
        }, 3);

        return back()->with('success', $isActive
            ? 'Assessment content version published.'
            : 'Assessment content version deactivated.');
    }

    public function destroy(AssignmentLibraryItem $assessment): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($assessment): bool {
            $lockedAssessment = AssignmentLibraryItem::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($this->contentService->assessmentHasReferences($lockedAssessment)) {
                return false;
            }

            $lockedAssessment->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'This assessment version is already used by one or more class assignments. Deactivate it instead of deleting it.');
        }

        return redirect()
            ->route('admin.assessments.index')
            ->with('success', 'Assessment content version deleted successfully.');
    }

    private function validatedData(Request $request, ?AssignmentLibraryItem $assessment = null): array
    {
        $assessmentId = $assessment?->id;

        $data = $request->validate([
            'module_no' => ['required', 'integer', 'min:1', 'max:9999'],
            'assignment_code' => [
                'required',
                'string',
                'max:189',
                Rule::unique('assignment_library_items', 'assignment_code')->ignore($assessmentId),
            ],
            'title' => ['required', 'string', 'max:189'],
            'topic_title' => ['required', 'string', 'max:189'],
            'year_level' => ['required', 'string', 'max:50'],
            'assignment_type' => ['required', Rule::in(['mcq', 'fill_blank', 'mixed'])],
            'version_no' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
                Rule::unique('assignment_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($assessmentId),
            ],
            'version_name' => ['required', 'string', 'max:189'],
            'version_code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('assignment_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($assessmentId),
            ],
            'description' => ['nullable', 'string', 'max:10000'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'time_limit_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:200'],
            'questions.*.question_type' => ['required', Rule::in(['mcq', 'fill_blank'])],
            'questions.*.question_text' => ['required', 'string', 'max:10000'],
            'questions.*.points' => ['required', 'integer', 'min:1', 'max:1000'],
            'questions.*.explanation' => ['nullable', 'string', 'max:10000'],
            'questions.*.correct_option' => ['nullable', 'integer', 'min:0'],
            'questions.*.options' => ['nullable', 'array', 'max:10'],
            'questions.*.options.*.option_text' => ['nullable', 'string', 'max:5000'],
            'questions.*.blank_answers' => ['nullable', 'array', 'max:20'],
            'questions.*.blank_answers.*.answer_text' => ['nullable', 'string', 'max:189'],
            'questions.*.blank_answers.*.is_case_sensitive' => ['nullable', 'boolean'],
            'questions.*.ilo_ids' => ['nullable', 'array', 'max:5'],
            'questions.*.ilo_ids.*' => [
                'integer',
                Rule::exists('intended_learning_outcomes', 'id')
                    ->where(fn ($query) => $query->where('is_active', true)),
            ],
        ]);

        $questions = array_values($data['questions']);
        $types = [];

        foreach ($questions as $questionIndex => &$question) {
            $type = $question['question_type'];
            $types[] = $type;
            $submittedCorrectIndex = isset($question['correct_option'])
                ? (int) $question['correct_option']
                : -1;
            $normalizedOptions = [];
            $normalizedCorrectIndex = null;

            foreach (($question['options'] ?? []) as $originalIndex => $option) {
                if (trim((string) ($option['option_text'] ?? '')) === '') {
                    continue;
                }

                if ((int) $originalIndex === $submittedCorrectIndex) {
                    $normalizedCorrectIndex = count($normalizedOptions);
                }

                $normalizedOptions[] = $option;
            }

            $question['options'] = $normalizedOptions;
            $question['blank_answers'] = array_values(array_filter(
                $question['blank_answers'] ?? [],
                fn ($answer) => trim((string) ($answer['answer_text'] ?? '')) !== ''
            ));
            $question['ilo_ids'] = collect($question['ilo_ids'] ?? [])
                ->map(fn ($id): int => (int) $id)
                ->filter(fn (int $id): bool => $id > 0)
                ->unique()
                ->values()
                ->all();

            if ($type === 'mcq') {
                if (count($question['options']) < 2) {
                    throw ValidationException::withMessages([
                        "questions.{$questionIndex}.options" => 'Each MCQ question must contain at least two answer choices.',
                    ]);
                }

                if ($normalizedCorrectIndex === null) {
                    throw ValidationException::withMessages([
                        "questions.{$questionIndex}.correct_option" => 'Select one valid correct answer for every MCQ question.',
                    ]);
                }

                $question['correct_option'] = $normalizedCorrectIndex;
            } elseif (count($question['blank_answers']) < 1) {
                throw ValidationException::withMessages([
                    "questions.{$questionIndex}.blank_answers" => 'Each fill-in-the-blank question must have at least one accepted answer.',
                ]);
            }
        }
        unset($question);

        $selectedIloIds = collect($questions)->pluck('ilo_ids')->flatten()->unique()->values();
        if ($selectedIloIds->isNotEmpty()) {
            $wrongModuleExists = IntendedLearningOutcome::query()
                ->whereIn('id', $selectedIloIds)
                ->where('module_no', '!=', (int) $data['module_no'])
                ->exists();

            if ($wrongModuleExists) {
                throw ValidationException::withMessages([
                    'questions' => 'Every selected ILO must belong to this assessment module. Remove ILOs from other modules and try again.',
                ]);
            }
        }

        $assignmentType = $data['assignment_type'];
        if ($assignmentType === 'mcq' && collect($types)->contains('fill_blank')) {
            throw ValidationException::withMessages(['assignment_type' => 'An MCQ assessment may contain only MCQ questions.']);
        }
        if ($assignmentType === 'fill_blank' && collect($types)->contains('mcq')) {
            throw ValidationException::withMessages(['assignment_type' => 'A fill-in-the-blank assessment may contain only fill-in-the-blank questions.']);
        }
        if ($assignmentType === 'mixed' && count(array_unique($types)) < 2) {
            throw ValidationException::withMessages(['assignment_type' => 'A mixed assessment must contain at least one MCQ and one fill-in-the-blank question.']);
        }

        return [$data, $questions];
    }


    private function assertReferencedAssessmentIdentityUnchanged(AssignmentLibraryItem $assessment, array $data): void
    {
        $changes = [];

        if ((int) $assessment->module_no !== (int) $data['module_no']) {
            $changes['module_no'] = 'Module number cannot be changed after this assessment version has been assigned to a class.';
        }
        if (strtoupper((string) $assessment->assignment_code) !== strtoupper(trim($data['assignment_code']))) {
            $changes['assignment_code'] = 'Assessment code cannot be changed after this version has been assigned to a class.';
        }
        if ((string) $assessment->assignment_type !== (string) $data['assignment_type']) {
            $changes['assignment_type'] = 'Assessment type cannot be changed after this version has been assigned to a class.';
        }
        if ((int) $assessment->version_no !== (int) $data['version_no']) {
            $changes['version_no'] = 'Version number cannot be changed after this assessment version has been assigned to a class.';
        }
        if (strtoupper((string) $assessment->version_code) !== strtoupper(trim($data['version_code']))) {
            $changes['version_code'] = 'Version code cannot be changed after this assessment version has been assigned to a class.';
        }
        if ((int) $assessment->time_limit_minutes !== (int) $data['time_limit_minutes']) {
            $changes['time_limit_minutes'] = 'Time limit cannot be changed after this assessment version has been assigned to a class. Duplicate it as a new version instead.';
        }

        if ($changes !== []) {
            throw ValidationException::withMessages($changes);
        }
    }

    private function assessmentPayload(array $data, array $questions): array
    {
        return [
            'module_no' => (int) $data['module_no'],
            'assignment_code' => strtoupper(trim($data['assignment_code'])),
            'title' => trim($data['title']),
            'topic_title' => trim($data['topic_title']),
            'year_level' => trim($data['year_level']),
            'assignment_type' => $data['assignment_type'],
            'version_no' => (int) $data['version_no'],
            'version_name' => trim($data['version_name']),
            'version_code' => strtoupper(trim($data['version_code'])),
            'description' => $data['description'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'time_limit_minutes' => (int) $data['time_limit_minutes'],
            'total_points' => collect($questions)->sum(fn ($question) => (int) $question['points']),
            'sort_order' => (int) $data['sort_order'],
            'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }

    private function syncQuestions(AssignmentLibraryItem $assessment, array $questions): void
    {
        $existingQuestionIds = $assessment->questions()->pluck('id');
        if ($existingQuestionIds->isNotEmpty()) {
            AssessmentQuestionIlo::query()
                ->where('assessment_source', 'assignment')
                ->whereIn('question_id', $existingQuestionIds)
                ->delete();
        }

        $assessment->questions()->delete();

        foreach ($questions as $questionIndex => $questionData) {
            $question = $assessment->questions()->create([
                'question_type' => $questionData['question_type'],
                'question_text' => trim($questionData['question_text']),
                'points' => (int) $questionData['points'],
                'order_index' => $questionIndex + 1,
                'explanation' => $questionData['explanation'] ?? null,
            ]);

            if ($questionData['question_type'] === 'mcq') {
                $correctIndex = (int) $questionData['correct_option'];
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    $question->options()->create([
                        'option_text' => trim($optionData['option_text']),
                        'is_correct' => $optionIndex === $correctIndex,
                        'order_index' => $optionIndex + 1,
                    ]);
                }
            } else {
                foreach ($questionData['blank_answers'] as $answerData) {
                    $question->blankAnswers()->create([
                        'answer_text' => trim($answerData['answer_text']),
                        'is_case_sensitive' => filter_var($answerData['is_case_sensitive'] ?? false, FILTER_VALIDATE_BOOL),
                    ]);
                }
            }

            foreach ($questionData['ilo_ids'] ?? [] as $iloId) {
                AssessmentQuestionIlo::create([
                    'ilo_id' => (int) $iloId,
                    'assessment_source' => 'assignment',
                    'question_id' => $question->id,
                    'weight' => 1,
                ]);
            }
        }
    }

    private function questionsForForm(AssignmentLibraryItem $assessment): array
    {
        return $assessment->questions->map(function ($question): array {
            $options = $question->options->values();
            $correctIndex = $options->search(fn ($option) => (bool) $option->is_correct);

            return [
                'question_type' => $question->question_type,
                'question_text' => $question->question_text,
                'points' => $question->points,
                'explanation' => $question->explanation,
                'correct_option' => $correctIndex === false ? 0 : $correctIndex,
                'options' => $options->map(fn ($option): array => [
                    'option_text' => $option->option_text,
                ])->all(),
                'blank_answers' => $question->blankAnswers->map(fn ($answer): array => [
                    'answer_text' => $answer->answer_text,
                    'is_case_sensitive' => (bool) $answer->is_case_sensitive,
                ])->values()->all(),
                'ilo_ids' => $question->iloMappings->pluck('ilo_id')->map(fn ($id): int => (int) $id)->values()->all(),
            ];
        })->values()->all();
    }

    private function emptyMcqQuestion(): array
    {
        return [
            'question_type' => 'mcq',
            'question_text' => '',
            'points' => 1,
            'explanation' => '',
            'correct_option' => 0,
            'options' => [
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
            ],
            'blank_answers' => [
                ['answer_text' => '', 'is_case_sensitive' => false],
            ],
            'ilo_ids' => [],
        ];
    }

    private function availableIlos()
    {
        return IntendedLearningOutcome::active()
            ->orderBy('module_no')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
