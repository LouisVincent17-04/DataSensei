<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\IntendedLearningOutcome;
use App\Models\QuestionBankItem;
use App\Services\QuestionBankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * The instructor's Question Bank (DataSensei Updates 11): reusable questions
 * organized by module/topic, type, difficulty and ILO. Instructors manage
 * their own questions and can copy from the shared pool; nobody sees another
 * instructor's private questions, and students never reach these pages.
 */
class InstructorQuestionBankController extends Controller
{
    public function index(Request $request)
    {
        $instructorId = (int) Auth::id();
        $query = $this->filteredQuery($request, $instructorId);

        $editing = null;
        if ($request->filled('edit')) {
            $editing = QuestionBankItem::with('options')
                ->whereKey($request->integer('edit'))
                ->where('instructor_id', $instructorId)
                ->first();
        }

        return view('instructor.question-bank.index', [
            'items' => $query->with(['options', 'ilo'])->orderByDesc('id')->paginate(20)->withQueryString(),
            'editing' => $editing,
            'ilos' => IntendedLearningOutcome::orderBy('module_no')->orderBy('sort_order')->get(),
            'types' => QuestionBankItem::TYPES,
            'thinkingLevels' => QuestionBankItem::THINKING_LEVELS,
            'difficulties' => QuestionBankItem::DIFFICULTIES,
            'ownCount' => QuestionBankItem::where('instructor_id', $instructorId)->where('is_archived', false)->count(),
            'sharedCount' => QuestionBankItem::whereNull('instructor_id')->where('is_archived', false)->count(),
        ]);
    }

    public function store(Request $request, QuestionBankService $bank)
    {
        [$data, $options] = $this->validatedQuestion($request);

        $item = DB::transaction(function () use ($data, $options): QuestionBankItem {
            $item = QuestionBankItem::create($data + ['instructor_id' => Auth::id(), 'is_archived' => false]);
            $this->saveOptions($item, $options);

            return $item;
        });

        return redirect()->route('instructor.question-bank.index')
            ->with('success', 'Question added to your bank.');
    }

    public function update(Request $request, QuestionBankItem $item)
    {
        abort_unless($item->isEditableBy((int) Auth::id()), 403);
        [$data, $options] = $this->validatedQuestion($request);

        DB::transaction(function () use ($item, $data, $options): void {
            $locked = QuestionBankItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            abort_unless($locked->isEditableBy((int) Auth::id()), 403);
            $locked->update($data);
            $this->saveOptions($locked, $options);
        });

        return redirect()->route('instructor.question-bank.index')
            ->with('success', 'Question updated. Assessments that already copied it keep their own snapshot.');
    }

    /** Archive hides a question from pickers; restore brings it back. */
    public function toggleArchive(QuestionBankItem $item)
    {
        abort_unless($item->isEditableBy((int) Auth::id()), 403);

        $item->update(['is_archived' => ! $item->is_archived]);

        return back()->with('success', $item->is_archived
            ? 'Question archived. Assessments that already use it are unchanged.'
            : 'Question restored.');
    }

    /** The picker: matching bank questions for one draft assessment. */
    public function pickForAssessment(Request $request, Assessment $assessment, QuestionBankService $bank)
    {
        $this->authorizeAssessment($assessment);
        abort_unless($assessment->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

        $assessment->load('tos');
        $usesTos = $assessment->table_of_specification_id !== null;
        $slots = $assessment->questions()->orderBy('item_number')->get();
        $missing = $slots->where('question_type', 'unconfigured')->values();

        // For a TOS assessment the list starts with the questions that fit a
        // missing planned item; "All questions" shows the rest too.
        $fitFilter = $usesTos && $missing->isNotEmpty() && $request->input('fit', 'fits') !== 'all';

        $query = $this->filteredQuery($request, (int) Auth::id())->where('is_archived', false);
        if ($fitFilter) {
            $tosModuleNo = $bank->tosModuleNo($assessment);
            if ($tosModuleNo !== null) {
                $query->where(fn ($q) => $q->whereNull('module_no')->orWhere('module_no', $tosModuleNo));
            }
            $plannedLevels = $missing->map(fn ($slot) => mb_strtolower(trim((string) $slot->bloom_level)));
            if (! $plannedLevels->contains('')) {
                $query->whereIn(DB::raw('LOWER(bloom_level)'), $plannedLevels->unique()->values()->all());
            }
        }

        $items = $query->with(['options', 'ilo'])->orderByDesc('id')->paginate(20)->withQueryString();

        return view('instructor.assessments.bank', [
            'assessment' => $assessment,
            'usesTos' => $usesTos,
            'slotCount' => $slots->count(),
            'missingSlots' => $missing,
            'fitFilter' => $fitFilter,
            'fits' => $usesTos ? $bank->fittingSlots($assessment, $items->items(), $missing) : [],
            'items' => $items,
            'ilos' => IntendedLearningOutcome::orderBy('module_no')->orderBy('sort_order')->get(),
            'types' => QuestionBankItem::TYPES,
            'thinkingLevels' => QuestionBankItem::THINKING_LEVELS,
            'difficulties' => QuestionBankItem::DIFFICULTIES,
        ]);
    }

    public function addToAssessment(Request $request, Assessment $assessment, QuestionBankService $bank)
    {
        $this->authorizeAssessment($assessment);

        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1', 'max:100'],
            'question_ids.*' => ['integer'],
        ], [
            'question_ids.required' => 'Tick at least one question to add.',
        ]);

        $result = DB::transaction(function () use ($assessment, $validated, $bank): array {
            $locked = Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $this->authorizeAssessment($locked);
            abort_unless($locked->status === 'draft', 422, 'Published or closed assessments cannot be edited.');

            $items = QuestionBankItem::with('options')
                ->visibleTo((int) Auth::id())
                ->where('is_archived', false)
                ->whereIn('id', $validated['question_ids'])
                ->orderBy('id')
                ->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'question_ids' => 'None of the selected questions are available to you.',
                ]);
            }

            return $bank->copyIntoAssessment($locked, $items);
        }, 3);

        $message = $result['added'].' '.($result['added'] === 1 ? 'question' : 'questions').' added.';
        if ($result['skipped_mismatch'] > 0) {
            $message .= ' '.$result['skipped_mismatch'].' not added: '.($result['skipped_mismatch'] === 1 ? 'it does' : 'they do')
                .' not match the module or thinking level of any missing planned item.';
        }
        if ($result['skipped_full'] > 0) {
            $message .= ' '.$result['skipped_full'].' not added: every planned TOS item is already filled.';
        }

        return redirect()
            ->to(route('instructor.assessments.builder', $assessment).'#questions')
            ->with('success', $message);
    }

    /** @return array{0: array<string, mixed>, 1: array<int, array{text: string, correct: bool}>} */
    private function validatedQuestion(Request $request): array
    {
        $validated = $request->validate([
            'question_type' => ['required', Rule::in(array_keys(QuestionBankItem::TYPES))],
            'question_text' => ['required', 'string', 'max:30000'],
            'points' => ['required', 'integer', 'min:1', 'max:1000'],
            'module_no' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'topic_title' => ['nullable', 'string', 'max:189'],
            'difficulty_slug' => ['nullable', Rule::in(array_keys(QuestionBankItem::DIFFICULTIES))],
            'ilo_id' => ['nullable', 'integer', 'exists:intended_learning_outcomes,id'],
            'bloom_level' => ['nullable', Rule::in(QuestionBankItem::THINKING_LEVELS)],
            'correct_answer' => ['nullable', 'string', 'max:10000'],
            'answer_explanation' => ['nullable', 'string', 'max:10000'],
            'rubric_text' => ['nullable', 'string', 'max:30000'],
            'option_texts' => ['nullable', 'array', 'max:10'],
            'option_texts.*' => ['nullable', 'string', 'max:5000'],
            'correct_option' => ['nullable', 'integer', 'min:0', 'max:9'],
        ], [], [
            'question_type' => 'question type',
            'question_text' => 'question',
            'option_texts' => 'choices',
            'correct_option' => 'correct choice',
            'correct_answer' => 'correct answer',
            'ilo_id' => 'learning outcome',
            'bloom_level' => 'thinking level',
            'difficulty_slug' => 'difficulty',
        ]);

        $type = $validated['question_type'];
        $correctAnswer = trim((string) ($validated['correct_answer'] ?? ''));
        $correctIndex = isset($validated['correct_option']) ? (int) $validated['correct_option'] : null;
        $options = collect($validated['option_texts'] ?? [])
            ->map(fn ($text, $index) => ['source_index' => (int) $index, 'text' => trim((string) $text)])
            ->filter(fn (array $option) => $option['text'] !== '')
            ->values();

        // A bank question must be complete: half-written questions cannot be
        // reused, so the same rules apply as when publishing an assessment.
        $errors = [];
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

        $storedAnswer = match (true) {
            $type === 'true_false' => strtolower($correctAnswer),
            in_array($type, ['fill_blank', 'short_answer'], true) => $correctAnswer,
            default => null,
        };

        return [[
            'question_type' => $type,
            'question_text' => trim((string) $validated['question_text']),
            'points' => (int) $validated['points'],
            'module_no' => $validated['module_no'] ?? null,
            'topic_title' => $this->nullable($validated['topic_title'] ?? null),
            'difficulty_slug' => $this->nullable($validated['difficulty_slug'] ?? null),
            'ilo_id' => $validated['ilo_id'] ?? null,
            'bloom_level' => $this->nullable($validated['bloom_level'] ?? null),
            'correct_answer' => $storedAnswer,
            'answer_explanation' => $this->nullable($validated['answer_explanation'] ?? null),
            'rubric_text' => $type === 'essay' ? $this->nullable($validated['rubric_text'] ?? null) : null,
        ], $options->map(fn (array $option) => [
            'text' => $option['text'],
            'correct' => $correctIndex !== null && $option['source_index'] === $correctIndex,
        ])->all()];
    }

    /** @param array<int, array{text: string, correct: bool}> $options */
    private function saveOptions(QuestionBankItem $item, array $options): void
    {
        $item->options()->delete();
        if ($item->question_type !== 'multiple_choice') {
            return;
        }

        foreach (array_values($options) as $index => $option) {
            $item->options()->create([
                'option_text' => $option['text'],
                'is_correct' => $option['correct'],
                'order_index' => $index + 1,
            ]);
        }
    }

    private function filteredQuery(Request $request, int $instructorId)
    {
        return QuestionBankItem::query()
            ->visibleTo($instructorId)
            ->when($request->input('scope') === 'mine', fn ($q) => $q->where('instructor_id', $instructorId))
            ->when($request->input('scope') === 'shared', fn ($q) => $q->whereNull('instructor_id'))
            ->when(! $request->boolean('archived'), fn ($q) => $q->where('is_archived', false), fn ($q) => $q->where('is_archived', true)->where('instructor_id', $instructorId))
            ->when($request->filled('search'), fn ($q) => $q->where(function ($inner) use ($request): void {
                $term = '%'.str_replace(['%', '_'], ['\\%', '\\_'], trim((string) $request->input('search'))).'%';
                $inner->where('question_text', 'like', $term)->orWhere('topic_title', 'like', $term);
            }))
            ->when($request->filled('module_no'), fn ($q) => $q->where('module_no', $request->integer('module_no')))
            ->when($request->filled('question_type'), fn ($q) => $q->where('question_type', $request->input('question_type')))
            ->when($request->filled('difficulty'), fn ($q) => $q->where('difficulty_slug', $request->input('difficulty')))
            ->when($request->filled('bloom_level'), fn ($q) => $q->where('bloom_level', $request->input('bloom_level')))
            ->when($request->filled('ilo_id'), fn ($q) => $q->where('ilo_id', $request->integer('ilo_id')));
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function authorizeAssessment(Assessment $assessment): void
    {
        $assessment->loadMissing('classRoom');

        $ownedClass = $assessment->classRoom
            && (int) $assessment->classRoom->instructor_id === (int) Auth::id();
        $ownedOrphan = ! $assessment->class_id
            && (int) $assessment->created_by === (int) Auth::id();

        abort_unless($ownedClass || $ownedOrphan, 403);
    }
}
