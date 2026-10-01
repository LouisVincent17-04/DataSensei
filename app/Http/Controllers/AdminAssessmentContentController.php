<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestionIlo;
use App\Models\AssignmentLibraryItem;
use App\Services\PlatformContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
            ->with('success', 'Assessment created.');
    }

    public function show(AssignmentLibraryItem $assessment): View
    {
        $assessment->load(['questions.options', 'questions.blankAnswers'])
            ->loadCount('classAssignments');

        return view('admin.assessments.show', [
            'assessment' => $assessment,
            'hasReferences' => $this->contentService->assessmentHasReferences($assessment),
            'nextVersionNo' => $this->contentService->nextAssessmentVersion((int) $assessment->module_no),
        ]);
    }

    public function edit(AssignmentLibraryItem $assessment): View
    {
        $assessment->load(['questions.options', 'questions.blankAnswers']);

        return view('admin.assessments.edit', [
            'assessment' => $assessment,
            'questions' => old('questions', $this->questionsForForm($assessment)),
            'hasReferences' => $this->contentService->assessmentHasReferences($assessment),
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
            ->with('success', 'Assessment saved.');
    }

    /**
     * Copies an assessment, with every question and answer, into a new
     * inactive version that can be edited freely. The code and version
     * fields are generated; older forms that still send them are honoured.
     */
    public function duplicate(Request $request, AssignmentLibraryItem $assessment): RedirectResponse
    {
        $data = $request->validate([
            'assignment_code' => ['nullable', 'string', 'max:189', 'unique:assignment_library_items,assignment_code'],
            'version_no' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('assignment_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $assessment->module_no)),
            ],
            'version_name' => ['nullable', 'string', 'max:189'],
            'version_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('assignment_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $assessment->module_no)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $generated = ! isset($data['assignment_code']);
        $nextVersion = (int) ($data['version_no'] ?? $this->contentService->nextAssessmentVersion((int) $assessment->module_no));
        $data['version_no'] = $nextVersion;
        $data['version_name'] = $data['version_name'] ?? 'Version '.$nextVersion;
        $data['version_code'] = $data['version_code'] ?? 'V'.$nextVersion;
        $data['assignment_code'] = $data['assignment_code'] ?? $this->generatedAssignmentCode((int) $assessment->module_no);
        $data['copy_title'] = $generated;

        $copy = DB::transaction(function () use ($assessment, $data): AssignmentLibraryItem {
            $source = AssignmentLibraryItem::query()
                ->whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();
            $source->load(['questions.options', 'questions.blankAnswers']);

            $copy = AssignmentLibraryItem::create([
                'module_no' => $source->module_no,
                'assignment_code' => strtoupper(trim($data['assignment_code'])),
                'title' => ($data['copy_title'] ?? false) ? mb_substr($source->title.' (copy)', 0, 189) : $source->title,
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

            }

            return $copy;
        }, 3);

        return redirect()
            ->route('admin.assessments.edit', $copy)
            ->with('success', 'A copy was made. It is inactive until you publish it.');
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
            ? 'Assessment published. Instructors can now give it to their classes.'
            : 'Assessment deactivated. Instructors can no longer choose it.');
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
            ->with('success', 'Assessment deleted.');
    }

    private function validatedData(Request $request, ?AssignmentLibraryItem $assessment = null): array
    {
        $assessmentId = $assessment?->id;

        $data = $request->validate([
            'module_no' => ['required', 'integer', 'min:1', 'max:9999'],
            // The code and version fields are internal (DataSensei Updates 9):
            // the form no longer asks for them and they are generated below.
            // Values sent by older forms or scripts are still checked.
            'assignment_code' => [
                'nullable',
                'string',
                'max:189',
                Rule::unique('assignment_library_items', 'assignment_code')->ignore($assessmentId),
            ],
            'title' => ['required', 'string', 'max:189'],
            'topic_title' => ['required', 'string', 'max:189'],
            'year_level' => ['required', 'string', 'max:50'],
            'assignment_type' => ['required', Rule::in(['mcq', 'fill_blank', 'mixed'])],
            'version_no' => [
                'nullable',
                'integer',
                'min:1',
                'max:9999',
                Rule::unique('assignment_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($assessmentId),
            ],
            'version_name' => ['nullable', 'string', 'max:189'],
            'version_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('assignment_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($assessmentId),
            ],
            'description' => ['nullable', 'string', 'max:10000'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'time_limit_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
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
        ]);

        $data = $this->withGeneratedIdentity($data, $assessment);

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


    /**
     * Fills the internal code and version fields the admin no longer types:
     * an existing assessment keeps its own, a new one gets the next version
     * number of its module and a generated code.
     */
    private function withGeneratedIdentity(array $data, ?AssignmentLibraryItem $existing): array
    {
        $moduleNo = (int) $data['module_no'];
        $sameModule = $existing !== null && (int) $existing->module_no === $moduleNo;

        $versionNo = $data['version_no'] ?? null;
        if ($versionNo === null) {
            $versionNo = $sameModule ? (int) $existing->version_no : $this->contentService->nextAssessmentVersion($moduleNo);
        }

        $data['version_no'] = (int) $versionNo;
        $data['version_name'] = trim((string) ($data['version_name'] ?? '')) !== ''
            ? $data['version_name']
            : ($sameModule && $existing->version_name ? $existing->version_name : 'Version '.$versionNo);
        $data['version_code'] = trim((string) ($data['version_code'] ?? '')) !== ''
            ? $data['version_code']
            : ($sameModule && $existing->version_code ? $existing->version_code : 'V'.$versionNo);
        $data['assignment_code'] = trim((string) ($data['assignment_code'] ?? '')) !== ''
            ? $data['assignment_code']
            : ($existing?->assignment_code ?: $this->generatedAssignmentCode($moduleNo));
        $data['sort_order'] = $data['sort_order'] ?? ($existing?->sort_order ?? $moduleNo);

        return $data;
    }

    private function generatedAssignmentCode(int $moduleNo): string
    {
        do {
            $code = 'ASN-'.str_pad((string) $moduleNo, 3, '0', STR_PAD_LEFT).'-'.strtoupper(Str::random(6));
        } while (AssignmentLibraryItem::query()->where('assignment_code', $code)->exists());

        return $code;
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
        // Older ILO evidence rows of the replaced questions are removed with
        // them. Assessments are not linked to ILOs any more (DataSensei
        // Updates 5): ILOs only describe a module.
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
        ];
    }
}
