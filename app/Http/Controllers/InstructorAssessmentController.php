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

            $class = ClassRoom::query()
                ->whereKey($validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where('is_archived', false)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedTos->canBeUsedForClass((int) $class->id)) {
                throw ValidationException::withMessages([
                    'class_id' => 'This class-specific TOS can only be used with the class it was created for.',
                ]);
            }

            $lockedTos->load('rows.ilo');

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
                        // Assessments stay separate from module ILOs
                        // (DataSensei Updates 5): no ILO link.
                        'ilo_id' => null,
                        'item_number' => $itemNumber++,
                        'question_type' => 'unconfigured',
                        'points' => max(1, (int) $row->default_points),
                        'is_required' => true,
                        'topic_title' => $row->topic_title,
                        'subtopic_title' => $row->subtopic_title,
                        'learning_objective' => $row->learning_objective ?: null,
                        'bloom_level' => $row->cognitive_level,
                        'difficulty_slug' => $row->difficulty_slug,
                    ]);
                }
            }

            return $assessment;
        }, 3);

        return redirect()
            ->route('instructor.assessments.builder', $assessment)
            ->with('success', 'Assessment saved as a draft with ' . $assessment->total_items . ' planned questions from your Table of Specifications. Write each one below.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Simple assessment builder (DataSensei Updates 9)
    //
    // An assessment is created with one short form (no Table of
    // Specifications needed), and every question is written on one page:
    // type, question, choices, the correct answer and points. Everything is
    // saved as a draft as you go; Publish checks that every question is
    // complete. A TOS can still be used to plan the questions: it creates
    // the planned items, which are then written on the same page.
    // ─────────────────────────────────────────────────────────────────────

    public const QUESTION_TYPES = [
        'multiple_choice' => 'Multiple choice',
        'true_false' => 'True or false',
        'short_answer' => 'Short answer',
        'essay' => 'Essay (checked by you)',
    ];

    public function newAssessment()
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        return view('instructor.assessments.create', [
            'tos' => null,
            'classes' => $classes,
            'totalItems' => null,
        ]);
    }

    public function saveNew(Request $request)
    {
        $validated = $this->validatedSettings($request);

        $assessment = DB::transaction(function () use ($validated): Assessment {
            $class = ClassRoom::query()
                ->whereKey($validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where('is_archived', false)
                ->lockForUpdate()
                ->firstOrFail();

            return Assessment::create([
                'table_of_specification_id' => null,
                'class_id' => $class->id,
                'created_by' => Auth::id(),
                'title' => $validated['title'],
                'description' => null,
                'instructions' => $validated['instructions'] ?? null,
                'status' => 'draft',
                'draft_saved_at' => now(),
                'total_items' => 0,
                'total_points' => 0,
                'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
                'max_attempts' => $validated['max_attempts'],
                'available_at' => $validated['available_at'] ?? null,
                'due_at' => $validated['due_at'] ?? null,
            ]);
        }, 3);

        return redirect()
            ->to(route('instructor.assessments.builder', $assessment).'#add-question')
            ->with('success', 'Assessment saved as a draft. Add your questions below.');
    }

    public function builder(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->load(['tos', 'classRoom']);

        $questions = $assessment->questions()
            ->with('options')
            ->orderBy('item_number')
            ->get();
        $assessment->setRelation('questions', $questions);

        $incomplete = $questions->reject(fn (AssessmentQuestion $question) => $question->isAuthoringComplete());
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where(fn ($query) => $query->where('is_archived', false)->orWhere('id', $assessment->class_id))
            ->orderBy('name')
            ->get();

        return view('instructor.assessments.builder', [
            'assessment' => $assessment,
            'questions' => $questions,
            'incomplete' => $incomplete,
            'readyToPublish' => $questions->isNotEmpty() && $incomplete->isEmpty(),
            'editable' => $assessment->status === 'draft',
            'classes' => $classes,
            'types' => self::QUESTION_TYPES,
            'openItem' => max(0, (int) $request->integer('item')),
        ]);
    }

    /** Title, class, instructions, time limit, attempts and dates. Drafts only. */
    public function updateSettings(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        abort_unless($assessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');
        $validated = $this->validatedSettings($request);

        DB::transaction(function () use ($assessment, $validated): void {
            $locked = Assessment::query()->whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $this->authorizeAssessment($locked);
            abort_unless($locked->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

            $class = ClassRoom::query()
                ->whereKey($validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where(fn ($query) => $query->where('is_archived', false)->orWhere('id', $locked->class_id))
                ->firstOrFail();

            if ($locked->table_of_specification_id) {
                $tos = TableOfSpecification::find($locked->table_of_specification_id);
                if ($tos && ! $tos->canBeUsedForClass((int) $class->id)) {
                    throw ValidationException::withMessages([
                        'class_id' => 'This assessment was planned with a class-specific Table of Specifications, so it stays with that class.',
                    ]);
                }
            }

            $locked->update([
                'class_id' => $class->id,
                'title' => $validated['title'],
                'instructions' => $validated['instructions'] ?? null,
                'time_limit_minutes' => $validated['time_limit_minutes'] ?? null,
                'max_attempts' => $validated['max_attempts'],
                'available_at' => $validated['available_at'] ?? null,
                'due_at' => $validated['due_at'] ?? null,
                'draft_saved_at' => now(),
            ]);
        }, 3);

        return redirect()
            ->to(route('instructor.assessments.builder', $assessment).'#settings')
            ->with('success', 'Settings saved.');
    }

    /** Adds a question at the end. Saved as written; Publish checks it is complete. */
    public function storeQuestion(Request $request, Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        abort_unless($assessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

        [$validated, $type, $questionText, $correctAnswer, $options, $correctIndex] = $this->validatedQuestion($request, false);
        $newImagePath = $this->storeQuestionImage($request);

        try {
            $question = DB::transaction(function () use ($request, $assessment, $validated, $type, $questionText, $correctAnswer, $options, $correctIndex, $newImagePath): AssessmentQuestion {
                $locked = Assessment::query()->whereKey($assessment->id)->lockForUpdate()->firstOrFail();
                $this->authorizeAssessment($locked);
                abort_unless($locked->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

                $question = AssessmentQuestion::create([
                    'assessment_id' => $locked->id,
                    'table_of_specification_row_id' => null,
                    'ilo_id' => null,
                    'item_number' => (int) $locked->questions()->max('item_number') + 1,
                    'question_type' => $type,
                    'question_text' => $questionText !== '' ? $questionText : null,
                    'image_path' => $newImagePath,
                    'points' => (int) $validated['points'],
                    'is_required' => true,
                    'authoring_touched' => true,
                    'correct_answer' => $this->storedCorrectAnswer($type, $correctAnswer),
                    'answer_explanation' => null,
                    'rubric_text' => $type === 'essay' ? ($this->nullableText($validated['rubric_text'] ?? null)) : null,
                    'topic_title' => mb_substr((string) $locked->title, 0, 189),
                ]);

                $this->saveOptions($question, $type, $options, $correctIndex);
                $this->refreshTotals($locked);

                return $question;
            }, 3);
        } catch (\Throwable $exception) {
            if ($newImagePath) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        return redirect()
            ->to(route('instructor.assessments.builder', $assessment).'#question-'.$question->id)
            ->with('success', 'Question '.$question->item_number.' added.');
    }

    public function updateQuestion(Request $request, Assessment $assessment, AssessmentQuestion $question)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);
        abort_if($assessment->status !== 'draft', 422, 'Published or closed assessments cannot be edited.');

        $intent = (string) $request->input('intent', 'save');
        $request->validate(['intent' => ['nullable', Rule::in(['save', 'draft', 'draft_exit', 'complete_next'])]]);
        [$validated, $type, $questionText, $correctAnswer, $options, $correctIndex] = $this->validatedQuestion($request, $intent === 'complete_next');
        $newImagePath = $this->storeQuestionImage($request);

        try {
            $result = DB::transaction(function () use (
                $request, $validated, $question, $assessment, $type, $intent,
                $questionText, $correctAnswer, $options, $correctIndex, $newImagePath
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
                    'points' => (int) $validated['points'],
                    // Every question is answered; the field stays for older forms.
                    'is_required' => $request->has('is_required') ? $request->boolean('is_required') : true,
                    'authoring_touched' => true,
                    'correct_answer' => $this->storedCorrectAnswer($type, $correctAnswer),
                    // The explanation is no longer asked for; one written
                    // earlier is kept unless a form sends a new one.
                    'answer_explanation' => $request->has('answer_explanation')
                        ? $this->nullableText($validated['answer_explanation'] ?? null)
                        : $lockedQuestion->answer_explanation,
                    'rubric_text' => $type === 'essay' ? $this->nullableText($validated['rubric_text'] ?? null) : null,
                ]);

                $this->saveOptions($lockedQuestion, $type, $options, $correctIndex);
                $allQuestions = $this->refreshTotals($lockedAssessment);

                $nextIncomplete = $allQuestions->first(
                    fn (AssessmentQuestion $candidate) => $candidate->item_number > $lockedQuestion->item_number
                        && ! $candidate->isAuthoringComplete()
                ) ?? $allQuestions->first(fn (AssessmentQuestion $candidate) => ! $candidate->isAuthoringComplete());
                $nextSequential = $allQuestions->first(
                    fn (AssessmentQuestion $candidate) => $candidate->item_number > $lockedQuestion->item_number
                );
                $destinationItem = $intent === 'complete_next'
                    ? (int) ($nextIncomplete?->item_number ?? $nextSequential?->item_number ?? $lockedQuestion->item_number)
                    : (int) $lockedQuestion->item_number;
                $lockedAssessment->update(['draft_last_item' => $destinationItem]);

                return [
                    'id' => $lockedQuestion->id,
                    'item_number' => $lockedQuestion->item_number,
                    'destination_item' => $destinationItem,
                    'old_image_path' => $oldImagePath && $oldImagePath !== $imagePath ? $oldImagePath : null,
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

        if ($intent === 'save') {
            return redirect()
                ->to(route('instructor.assessments.builder', $assessment).'#question-'.$result['id'])
                ->with('success', 'Question '.$result['item_number'].' saved.');
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

    /** Removes a question from a draft and numbers the rest again. */
    public function destroyQuestion(Assessment $assessment, AssessmentQuestion $question)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $question->assessment_id === (int) $assessment->id, 404);
        abort_if($assessment->status !== 'draft', 422, 'Published or closed assessments cannot be edited.');

        $imagePath = DB::transaction(function () use ($assessment, $question): ?string {
            $locked = Assessment::query()->whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->status === 'draft', 422, 'Published or closed assessments cannot be edited.');
            $lockedQuestion = AssessmentQuestion::query()
                ->whereKey($question->id)
                ->where('assessment_id', $locked->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_if($lockedQuestion->answers()->exists(), 422, 'Students have answered this question, so it cannot be removed.');

            $image = $lockedQuestion->image_path;
            $lockedQuestion->options()->delete();
            $lockedQuestion->delete();

            $number = 1;
            foreach ($locked->questions()->orderBy('item_number')->get() as $remaining) {
                if ((int) $remaining->item_number !== $number) {
                    $remaining->update(['item_number' => $number]);
                }
                $number++;
            }

            $this->refreshTotals($locked);

            return $image;
        }, 3);

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()
            ->to(route('instructor.assessments.builder', $assessment).'#questions')
            ->with('success', 'Question removed.');
    }

    /** The assessment as students see it, with an optional answer key. */
    public function preview(Assessment $assessment)
    {
        $this->authorizeAssessment($assessment);
        $assessment->load(['classRoom', 'questions' => fn ($query) => $query->orderBy('item_number'), 'questions.options']);

        return view('instructor.assessments.preview', ['assessment' => $assessment]);
    }

    /** @return array<string, mixed> */
    private function validatedSettings(Request $request): array
    {
        return $request->validate([
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'title' => ['required', 'string', 'max:191'],
            'instructions' => ['nullable', 'string', 'max:10000'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'max_attempts' => ['required', 'integer', 'min:1', 'max:10'],
            'available_at' => ['nullable', 'date'],
            'due_at' => ['nullable', 'date', 'after_or_equal:available_at'],
        ], [
            'due_at.after_or_equal' => 'The due date must be on or after the date the assessment opens.',
        ], [
            'time_limit_minutes' => 'time limit',
            'max_attempts' => 'attempts allowed',
            'available_at' => 'available from',
            'due_at' => 'due date',
        ]);
    }

    /**
     * @return array{0: array<string, mixed>, 1: string, 2: string, 3: string, 4: \Illuminate\Support\Collection, 5: ?int}
     */
    private function validatedQuestion(Request $request, bool $requireComplete): array
    {
        $validated = $request->validate([
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
        ], [], [
            'question_type' => 'question type',
            'question_text' => 'question',
            'option_texts' => 'choices',
            'correct_option' => 'correct choice',
            'correct_answer' => 'correct answer',
        ]);

        $type = $validated['question_type'];
        $questionText = trim((string) ($validated['question_text'] ?? ''));
        // Compared with === '' everywhere: "0" is a real answer, not a blank.
        $correctAnswer = trim((string) ($validated['correct_answer'] ?? ''));
        $correctIndex = isset($validated['correct_option']) ? (int) $validated['correct_option'] : null;
        $options = collect();

        if ($type === 'multiple_choice') {
            $options = collect($validated['option_texts'] ?? [])
                ->map(fn ($text, $index) => ['source_index' => (int) $index, 'text' => trim((string) $text)])
                ->filter(fn (array $option) => $option['text'] !== '')
                ->values();
        }

        if ($requireComplete) {
            $errors = [];
            if ($type === 'unconfigured') {
                $errors['question_type'] = 'Choose a question type before marking this item complete.';
            }
            if ($questionText === '') {
                $errors['question_text'] = 'Enter the question before moving to the next item.';
            }
            if ($type === 'multiple_choice' && $options->count() < 2) {
                $errors['option_texts'] = 'A multiple-choice question needs at least two choices.';
            }
            if ($type === 'multiple_choice' && ($correctIndex === null || ! $options->contains('source_index', $correctIndex))) {
                $errors['correct_option'] = 'Select exactly one correct choice.';
            }
            if (in_array($type, ['fill_blank', 'short_answer'], true) && $correctAnswer === '') {
                $errors['correct_answer'] = 'Enter the correct or accepted answer for this question type.';
            }
            if ($type === 'true_false' && ! in_array(strtolower($correctAnswer), ['true', 'false'], true)) {
                $errors['correct_answer'] = 'Select True or False as the correct answer.';
            }
            if ($errors !== []) {
                throw ValidationException::withMessages($errors);
            }
        }

        return [$validated, $type, $questionText, $correctAnswer, $options, $correctIndex];
    }

    private function storeQuestionImage(Request $request): ?string
    {
        if (! $request->hasFile('question_image')) {
            return null;
        }

        $path = $request->file('question_image')->store('assessment-images', 'public');
        if ($path === false) {
            throw ValidationException::withMessages([
                'question_image' => 'The image could not be stored. Try again.',
            ]);
        }

        return $path;
    }

    private function storedCorrectAnswer(string $type, string $correctAnswer): ?string
    {
        if ($type === 'true_false') {
            $normalized = strtolower($correctAnswer);

            return in_array($normalized, ['true', 'false'], true) ? $normalized : null;
        }

        return in_array($type, ['fill_blank', 'short_answer'], true) && $correctAnswer !== ''
            ? $correctAnswer
            : null;
    }

    private function saveOptions(AssessmentQuestion $question, string $type, $options, ?int $correctIndex): void
    {
        $question->options()->delete();

        if ($type !== 'multiple_choice') {
            return;
        }

        foreach ($options->values() as $index => $option) {
            AssessmentQuestionOption::create([
                'assessment_question_id' => $question->id,
                'option_label' => chr(65 + $index),
                'option_text' => $option['text'],
                'is_correct' => $correctIndex !== null && $option['source_index'] === $correctIndex,
                'order_index' => $index + 1,
            ]);
        }
    }

    /** Recounts items and points after a change; returns the questions in order. */
    private function refreshTotals(Assessment $assessment)
    {
        $questions = $assessment->questions()->with('options')->orderBy('item_number')->get();
        $assessment->update([
            'total_items' => $questions->count(),
            'total_points' => (int) $questions->sum('points'),
            'draft_saved_at' => now(),
        ]);

        return $questions;
    }

    private function nullableText(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
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

    public function gradeSubmission(Request $request, Assessment $assessment, AssessmentSubmission $submission, AssessmentDiagnosticService $diagnostics, StudentNotificationService $notifications)
    {
        $this->authorizeAssessment($assessment);
        abort_unless((int) $submission->assessment_id === (int) $assessment->id, 404);

        $submission->load('answers.question');

        $rules = [
            'scores' => ['nullable', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0'],
            'feedbacks' => ['nullable', 'array'],
            'feedbacks.*' => ['nullable', 'string', 'max:10000'],
            'feedback' => ['nullable', 'string', 'max:10000'],
        ];
        $attributes = [];
        foreach ($submission->answers as $answer) {
            if ($answer->question?->question_type !== 'essay') {
                continue;
            }

            $rules['scores.' . $answer->id] = ['nullable', 'numeric', 'min:0', 'max:' . (int) $answer->question->points];
            $attributes['scores.' . $answer->id] = 'score for Item ' . $answer->question->item_number;
        }

        $validated = $request->validate($rules, [], $attributes);

        // Partial-update semantics: a score or feedback key that is absent means
        // "leave the stored value alone". A blank score also means "no change",
        // so only an explicit number (including 0) ever sets an essay score.
        $postedScores = is_array($validated['scores'] ?? null) ? $validated['scores'] : [];
        $postedFeedbacks = is_array($validated['feedbacks'] ?? null) ? $validated['feedbacks'] : [];
        $hasOverallFeedback = array_key_exists('feedback', $validated);

        $outcome = DB::transaction(function () use ($assessment, $submission, $validated, $postedScores, $postedFeedbacks, $hasOverallFeedback): array {
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

            $scoreChanged = false;
            $unscoredItems = [];

            foreach ($lockedSubmission->answers as $answer) {
                if ($answer->question->question_type !== 'essay') {
                    continue;
                }

                $changes = [];
                $postedScore = $postedScores[$answer->id] ?? null;

                if ($postedScore !== null && $postedScore !== '') {
                    $maxPoints = (float) $answer->question->points;
                    $score = min((float) $postedScore, $maxPoints);

                    // is_correct === null is the "never scored" marker for essays,
                    // so an explicit 0 is recorded as scored (false), not pending.
                    if ($answer->is_correct === null
                        || abs((float) $answer->points_awarded - $score) > 0.00001) {
                        $scoreChanged = true;
                    }

                    $changes['points_awarded'] = $score;
                    $changes['is_correct'] = $score >= $maxPoints;
                }

                if (array_key_exists($answer->id, $postedFeedbacks)) {
                    $changes['instructor_feedback'] = $postedFeedbacks[$answer->id];
                }

                if ($changes !== []) {
                    $answer->update($changes);
                }

                if ($answer->is_correct === null) {
                    $unscoredItems[] = (int) $answer->question->item_number;
                }
            }

            // Always recomputed from what is stored, never from substituted zeros.
            $updates = [
                'score' => (float) $lockedSubmission->answers()->sum('points_awarded'),
            ];
            if ($hasOverallFeedback) {
                $updates['feedback'] = $validated['feedback'];
            }

            $fullyScored = $unscoredItems === [];
            if ($fullyScored) {
                $updates['status'] = $lockedSubmission->status === 'late' ? 'late' : 'graded';
                if ($scoreChanged || ! $lockedSubmission->graded_at) {
                    $updates['graded_at'] = now();
                }
            }

            $lockedSubmission->update($updates);
            sort($unscoredItems);

            return [
                'fully_scored' => $fullyScored,
                'unscored_items' => $unscoredItems,
            ];
        }, 3);

        $gradedSubmission = $submission->fresh();
        $diagnostics->refresh($gradedSubmission);

        if (! $outcome['fully_scored']) {
            $items = collect($outcome['unscored_items'])->map(fn (int $item): string => 'Item ' . $item)->implode(', ');

            return back()->with(
                'success',
                'Scores and feedback saved. This submission is still pending review because no score has been entered for: ' . $items . '.'
            );
        }

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
