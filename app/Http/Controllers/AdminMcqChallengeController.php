<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Services\PlatformContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminMcqChallengeController extends Controller
{
    public function __construct(private readonly PlatformContentService $contentService)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', 'all');
        $categoryId = $request->integer('category_id');

        $categories = ChallengeCategory::query()
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $challenges = Challenge::query()
            ->mcq()
            ->with('category')
            ->withCount(['questions', 'attempts'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('content_code', 'like', "%{$search}%")
                        ->orWhere('version_code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId > 0, fn ($query) => $query->where('challenge_category_id', $categoryId))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('challenge_category_id')
            ->orderBy('order_index')
            ->orderBy('version_no')
            ->paginate(12)
            ->withQueryString();

        return view('admin.challenges.index', compact(
            'challenges',
            'categories',
            'search',
            'status',
            'categoryId'
        ));
    }

    public function create(): View
    {
        $categories = ChallengeCategory::query()
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        return view('admin.challenges.create', [
            'challenge' => new Challenge([
                'time_limit_seconds' => 600,
                'base_xp' => 100,
                'order_index' => 0,
                'version_no' => 1,
                'version_name' => 'Version 1',
                'version_code' => 'V1',
                'is_active' => false,
            ]),
            'categories' => $categories,
            'questions' => old('questions', [$this->emptyQuestion()]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $questions] = $this->validatedData($request);

        $challenge = DB::transaction(function () use ($data, $questions): Challenge {
            $challenge = Challenge::create($this->challengePayload($data));
            $this->syncQuestions($challenge, $questions);

            if ($challenge->is_active) {
                $this->contentService->publishChallengeVersion($challenge);
            }

            return $challenge;
        });

        return redirect()
            ->route('admin.challenges.show', $challenge)
            ->with('success', 'MCQ challenge version created successfully.');
    }

    public function show(Challenge $challenge): View
    {
        $this->ensureMcq($challenge);
        $challenge->load(['category', 'questions.options'])->loadCount('attempts');

        return view('admin.challenges.show', [
            'challenge' => $challenge,
            'hasHistory' => $this->contentService->challengeHasHistory($challenge),
            'nextVersionNo' => $this->contentService->nextChallengeVersion((string) $challenge->content_code),
        ]);
    }

    public function edit(Challenge $challenge): View
    {
        $this->ensureMcq($challenge);
        $challenge->load('questions.options');

        $categories = ChallengeCategory::query()
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        return view('admin.challenges.edit', [
            'challenge' => $challenge,
            'categories' => $categories,
            'questions' => old('questions', $this->questionsForForm($challenge)),
            'hasHistory' => $this->contentService->challengeHasHistory($challenge),
        ]);
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->ensureMcq($challenge);
        [$data, $questions] = $this->validatedData($request, $challenge);

        DB::transaction(function () use ($challenge, $data, $questions): void {
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureMcq($lockedChallenge);
            $hasHistory = $this->contentService->challengeHasHistory($lockedChallenge);

            if ($hasHistory) {
                $this->assertChallengeWithHistoryIdentityUnchanged($lockedChallenge, $data);

                if ($this->contentService->challengeQuestionsChanged($lockedChallenge, $questions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'This challenge already has attempt history. Duplicate it as a new version before changing its questions or answer choices.',
                    ]);
                }
            }

            $lockedChallenge->update($this->challengePayload($data));

            if (! $hasHistory) {
                $this->syncQuestions($lockedChallenge, $questions);
            }

            if ($lockedChallenge->is_active) {
                $this->contentService->publishChallengeVersion($lockedChallenge);
            }
        }, 3);

        return redirect()
            ->route('admin.challenges.show', $challenge)
            ->with('success', 'MCQ challenge version updated successfully.');
    }

    public function duplicate(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->ensureMcq($challenge);
        $data = $request->validate([
            'version_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('challenges', 'version_no')
                    ->where(fn ($query) => $query
                        ->where('content_code', $challenge->content_code)
                        ->where('is_coding_challenge', false)),
            ],
            'version_name' => ['required', 'string', 'max:100'],
            'version_code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('challenges', 'version_code')
                    ->where(fn ($query) => $query->where('content_code', $challenge->content_code)),
            ],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $copy = DB::transaction(function () use ($challenge, $data): Challenge {
            $source = Challenge::query()
                ->whereKey($challenge->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->ensureMcq($source);
            $source->load('questions.options');

            $copy = Challenge::create([
                'challenge_category_id' => $source->challenge_category_id,
                'content_code' => $source->content_code,
                'title' => $source->title,
                'description' => $source->description,
                'time_limit_seconds' => $source->time_limit_seconds,
                'base_xp' => $source->base_xp,
                'order_index' => $source->order_index,
                'is_coding_challenge' => false,
                'version_no' => (int) $data['version_no'],
                'version_name' => trim($data['version_name']),
                'version_code' => strtoupper(trim($data['version_code'])),
                'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
            ]);

            foreach ($source->questions as $question) {
                $newQuestion = $copy->questions()->create([
                    'challenge_category_id' => $copy->challenge_category_id,
                    'question_text' => $question->question_text,
                    'order_index' => $question->order_index,
                ]);

                foreach ($question->options as $option) {
                    $newQuestion->options()->create([
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'order_index' => $option->order_index,
                    ]);
                }
            }

            if ($copy->is_active) {
                $this->contentService->publishChallengeVersion($copy);
            }

            return $copy;
        }, 3);

        return redirect()
            ->route('admin.challenges.edit', $copy)
            ->with('success', 'Challenge duplicated as a new version. Review it before publishing.');
    }

    public function toggleStatus(Challenge $challenge): RedirectResponse
    {
        $this->ensureMcq($challenge);

        $result = DB::transaction(function () use ($challenge): string {
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureMcq($lockedChallenge);

            if ($lockedChallenge->is_active
                && $lockedChallenge->attempts()->where('status', 'in_progress')->exists()) {
                return 'in_progress';
            }

            if ($lockedChallenge->is_active) {
                $lockedChallenge->update(['is_active' => false]);

                return 'deactivated';
            }

            $this->contentService->publishChallengeVersion($lockedChallenge);

            return 'published';
        }, 3);

        if ($result === 'in_progress') {
            return back()->with('error', 'This challenge has an active student attempt. Wait for the attempt to finish before deactivating it.');
        }

        return back()->with('success', $result === 'published'
            ? 'MCQ challenge version published. Other versions of this challenge were deactivated.'
            : 'MCQ challenge version deactivated.');
    }

    public function destroy(Challenge $challenge): RedirectResponse
    {
        $this->ensureMcq($challenge);

        $deleted = DB::transaction(function () use ($challenge): bool {
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureMcq($lockedChallenge);

            if ($this->contentService->challengeHasHistory($lockedChallenge)) {
                return false;
            }

            $lockedChallenge->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'This challenge has attempt history. Deactivate it instead of deleting it.');
        }

        return redirect()
            ->route('admin.challenges.index')
            ->with('success', 'MCQ challenge version deleted successfully.');
    }

    private function validatedData(Request $request, ?Challenge $challenge = null): array
    {
        $challengeId = $challenge?->id;

        $data = $request->validate([
            'challenge_category_id' => ['required', 'integer', 'exists:challenge_categories,id'],
            'content_code' => ['required', 'string', 'max:64'],
            'title' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:10000'],
            'time_limit_seconds' => ['required', 'integer', 'min:60', 'max:21600'],
            'base_xp' => ['required', 'integer', 'min:0', 'max:100000'],
            'order_index' => ['required', 'integer', 'min:0', 'max:100000'],
            'version_no' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
                Rule::unique('challenges', 'version_no')
                    ->where(fn ($query) => $query
                        ->where('content_code', strtoupper(trim((string) $request->input('content_code'))))
                        ->where('is_coding_challenge', false))
                    ->ignore($challengeId),
            ],
            'version_name' => ['required', 'string', 'max:100'],
            'version_code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('challenges', 'version_code')
                    ->where(fn ($query) => $query->where('content_code', strtoupper(trim((string) $request->input('content_code')))))
                    ->ignore($challengeId),
            ],
            'is_active' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:200'],
            'questions.*.question_text' => ['required', 'string', 'max:10000'],
            'questions.*.correct_option' => ['required', 'integer', 'min:0'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:10'],
            'questions.*.options.*.option_text' => ['required', 'string', 'max:5000'],
        ]);

        $questions = array_values($data['questions']);

        foreach ($questions as $questionIndex => &$question) {
            $question['options'] = array_values($question['options']);
            $correctIndex = (int) $question['correct_option'];

            if (!array_key_exists($correctIndex, $question['options'])) {
                throw ValidationException::withMessages([
                    "questions.{$questionIndex}.correct_option" => 'Select one valid correct answer for every question.',
                ]);
            }
        }
        unset($question);

        return [$data, $questions];
    }


    private function assertChallengeWithHistoryIdentityUnchanged(Challenge $challenge, array $data): void
    {
        $changes = [];

        if ((int) $challenge->challenge_category_id !== (int) $data['challenge_category_id']) {
            $changes['challenge_category_id'] = 'Difficulty category cannot be changed after this challenge has attempt history.';
        }
        if (strtoupper((string) $challenge->content_code) !== strtoupper(trim($data['content_code']))) {
            $changes['content_code'] = 'Content code cannot be changed after this challenge has attempt history.';
        }
        if ((int) $challenge->time_limit_seconds !== (int) $data['time_limit_seconds']) {
            $changes['time_limit_seconds'] = 'Time limit cannot be changed after this challenge has attempt history. Create a new version instead.';
        }
        if ((int) $challenge->base_xp !== (int) $data['base_xp']) {
            $changes['base_xp'] = 'Base XP cannot be changed after this challenge has attempt history. Create a new version instead.';
        }
        if ((int) $challenge->version_no !== (int) $data['version_no']) {
            $changes['version_no'] = 'Version number cannot be changed after this challenge has attempt history.';
        }
        if (strtoupper((string) $challenge->version_code) !== strtoupper(trim($data['version_code']))) {
            $changes['version_code'] = 'Version code cannot be changed after this challenge has attempt history.';
        }

        if ($changes !== []) {
            throw ValidationException::withMessages($changes);
        }
    }

    private function challengePayload(array $data): array
    {
        return [
            'challenge_category_id' => (int) $data['challenge_category_id'],
            'content_code' => strtoupper(trim($data['content_code'])),
            'title' => trim($data['title']),
            'description' => $data['description'] ?? '',
            'time_limit_seconds' => (int) $data['time_limit_seconds'],
            'base_xp' => (int) $data['base_xp'],
            'order_index' => (int) $data['order_index'],
            'is_coding_challenge' => false,
            'version_no' => (int) $data['version_no'],
            'version_name' => trim($data['version_name']),
            'version_code' => strtoupper(trim($data['version_code'])),
            'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }

    private function syncQuestions(Challenge $challenge, array $questions): void
    {
        $challenge->questions()->delete();

        foreach ($questions as $questionIndex => $questionData) {
            $question = $challenge->questions()->create([
                'challenge_category_id' => $challenge->challenge_category_id,
                'question_text' => trim($questionData['question_text']),
                'order_index' => $questionIndex + 1,
            ]);

            $correctIndex = (int) $questionData['correct_option'];

            foreach ($questionData['options'] as $optionIndex => $optionData) {
                $question->options()->create([
                    'option_text' => trim($optionData['option_text']),
                    'is_correct' => $optionIndex === $correctIndex,
                    'order_index' => $optionIndex + 1,
                ]);
            }
        }
    }

    private function questionsForForm(Challenge $challenge): array
    {
        return $challenge->questions->map(function ($question): array {
            $options = $question->options->values();
            $correctIndex = $options->search(fn ($option) => (bool) $option->is_correct);

            return [
                'question_text' => $question->question_text,
                'correct_option' => $correctIndex === false ? 0 : $correctIndex,
                'options' => $options->map(fn ($option): array => [
                    'option_text' => $option->option_text,
                ])->all(),
            ];
        })->values()->all();
    }

    private function emptyQuestion(): array
    {
        return [
            'question_text' => '',
            'correct_option' => 0,
            'options' => [
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
            ],
        ];
    }

    private function ensureMcq(Challenge $challenge): void
    {
        abort_if((bool) $challenge->is_coding_challenge, 404);
    }
}
