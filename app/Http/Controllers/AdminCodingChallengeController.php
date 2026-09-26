<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\TestCase;
use App\Services\CodingChallengeTestRunner;
use App\Services\PlatformContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Platform coding challenges: a challenge (is_coding_challenge = 1) holds one
 * or more coding questions, each with its own test cases. Mirrors the MCQ
 * manager's versioning and history rules.
 */
class AdminCodingChallengeController extends Controller
{
    public const LANGUAGES = ['python'];

    public const MAX_TEST_CASES_PER_QUESTION = CodingChallengeTestRunner::MAX_TEST_CASES;

    public function __construct(
        private readonly PlatformContentService $contentService,
        private readonly CodingChallengeTestRunner $testRunner,
    ) {
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
            ->coding()
            ->with(['category', 'module'])
            ->withCount('codingQuestions')
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

        return view('admin.coding-challenges.index', compact(
            'challenges',
            'categories',
            'search',
            'status',
            'categoryId'
        ));
    }

    public function create(): View
    {
        return view('admin.coding-challenges.create', [
            'challenge' => new Challenge([
                'time_limit_seconds' => 1800,
                'base_xp' => 100,
                'order_index' => 0,
                'version_no' => 1,
                'version_name' => 'Version 1',
                'version_code' => 'V1',
                'is_active' => false,
            ]),
            'categories' => $this->categories(),
            'questions' => old('questions', [$this->emptyQuestion()]),
            'hasHistory' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        [$data, $questions] = $this->validatedData($request);

        $continuingAttempts = 0;

        $challenge = DB::transaction(function () use ($data, $questions, &$continuingAttempts): Challenge {
            $challenge = Challenge::create($this->challengePayload($data));
            $this->syncQuestions($challenge, $questions);

            if ($challenge->is_active) {
                $continuingAttempts = $this->contentService->publishChallengeVersion($challenge);
            }

            return $challenge;
        }, 3);

        return redirect()
            ->route('admin.coding-challenges.show', $challenge)
            ->with('success', 'Coding challenge version created successfully.' . $this->continuingAttemptsNotice($continuingAttempts));
    }

    /**
     * Runs a reference solution against the submitted test cases and reports
     * the result. Nothing is persisted: the form calls this before saving.
     */
    public function checkTests(Request $request): JsonResponse
    {
        $data = $request->validate([
            'reference_solution' => ['required', 'string', 'max:50000'],
            'time_limit_seconds' => ['nullable', 'integer', 'min:1', 'max:7200'],
            'test_cases' => ['required', 'array', 'min:1', 'max:100'],
            'test_cases.*' => ['array'],
            'test_cases.*.input' => ['nullable', 'string', 'max:10000'],
            'test_cases.*.expected_output' => ['nullable', 'string', 'max:10000'],
            'test_cases.*.is_hidden' => ['nullable', 'boolean'],
        ]);

        // The same normalisation the store applies, so the check grades what
        // would actually be saved.
        $testCases = array_map(fn (array $case): array => [
            'input' => $this->nullableCode($case['input'] ?? null) ?? '',
            'expected_output' => $this->normalizeNewlines((string) ($case['expected_output'] ?? '')),
            'is_hidden' => filter_var($case['is_hidden'] ?? false, FILTER_VALIDATE_BOOL),
        ], array_values($data['test_cases']));

        $report = $this->testRunner->check(
            $this->normalizeNewlines((string) $data['reference_solution']),
            $testCases,
            (int) ($data['time_limit_seconds'] ?? CodingChallengeTestRunner::MAX_SECONDS_PER_CASE)
        );

        return response()->json($report);
    }

    public function show(Challenge $challenge): View
    {
        $this->ensureCoding($challenge);
        $challenge->load(['category', 'module', 'codingQuestions.testCases']);

        return view('admin.coding-challenges.show', [
            'challenge' => $challenge,
            'hasHistory' => $this->hasCodingHistory($challenge),
            'submissionCount' => CodingSubmission::query()
                ->whereIn('coding_question_id', $challenge->codingQuestions->pluck('id'))
                ->count(),
        ]);
    }

    public function edit(Challenge $challenge): View
    {
        $this->ensureCoding($challenge);
        $challenge->load('codingQuestions.testCases');

        return view('admin.coding-challenges.edit', [
            'challenge' => $challenge,
            'categories' => $this->categories(),
            'questions' => old('questions', $this->questionsForForm($challenge)),
            'hasHistory' => $this->hasCodingHistory($challenge),
        ]);
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->ensureCoding($challenge);
        [$data, $questions] = $this->validatedData($request, $challenge);

        $continuingAttempts = 0;

        DB::transaction(function () use ($challenge, $data, $questions, &$continuingAttempts): void {
            $continuingAttempts = 0;
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureCoding($lockedChallenge);
            $hasHistory = $this->hasCodingHistory($lockedChallenge);

            if ($hasHistory) {
                $this->assertChallengeWithHistoryIdentityUnchanged($lockedChallenge, $data);

                if ($this->gradedContentChanged($lockedChallenge, $questions)) {
                    throw ValidationException::withMessages([
                        'questions' => 'This challenge already has submission history. Only problem titles and reference solutions may change; problem descriptions, starter code, limits and test cases are frozen.',
                    ]);
                }
            }

            $lockedChallenge->update($this->challengePayload($data, $lockedChallenge));

            if (! $hasHistory) {
                $this->syncQuestions($lockedChallenge, $questions);
            } else {
                // Graded content is frozen once learners have attempted it, but
                // the title and the author's reference solution never affect
                // grading and may still be corrected in place.
                $this->syncUngradedFields($lockedChallenge, $questions);
            }

            if ($lockedChallenge->is_active) {
                $continuingAttempts = $this->contentService->publishChallengeVersion($lockedChallenge);
            }
        }, 3);

        return redirect()
            ->route('admin.coding-challenges.show', $challenge)
            ->with('success', 'Coding challenge version updated successfully.' . $this->continuingAttemptsNotice($continuingAttempts));
    }

    public function toggleStatus(Challenge $challenge): RedirectResponse
    {
        $this->ensureCoding($challenge);

        $continuingAttempts = 0;

        $result = DB::transaction(function () use ($challenge, &$continuingAttempts): string {
            $continuingAttempts = 0;
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureCoding($lockedChallenge);

            if ($lockedChallenge->is_active && $this->hasInProgressAttempts($lockedChallenge)) {
                return 'in_progress';
            }

            if ($lockedChallenge->is_active) {
                $lockedChallenge->update(['is_active' => false]);

                return 'deactivated';
            }

            $continuingAttempts = $this->contentService->publishChallengeVersion($lockedChallenge);

            return 'published';
        }, 3);

        if ($result === 'in_progress') {
            return back()->with('error', 'This coding challenge has a student attempt in progress. Wait for the attempt to finish before deactivating it.');
        }

        return back()->with('success', $result === 'published'
            ? 'Coding challenge version published. Other versions of this challenge were deactivated.' . $this->continuingAttemptsNotice($continuingAttempts)
            : 'Coding challenge version deactivated.');
    }

    public function destroy(Challenge $challenge): RedirectResponse
    {
        $this->ensureCoding($challenge);

        $deleted = DB::transaction(function () use ($challenge): bool {
            $lockedChallenge = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureCoding($lockedChallenge);

            if ($this->hasCodingHistory($lockedChallenge)) {
                return false;
            }

            $this->deleteQuestions($lockedChallenge);
            $lockedChallenge->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'This coding challenge has submission history. Deactivate it instead of deleting it.');
        }

        return redirect()
            ->route('admin.coding-challenges.index')
            ->with('success', 'Coding challenge version deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Validation and payloads
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return array{0: array, 1: array<int, array>}
     */
    private function validatedData(Request $request, ?Challenge $challenge = null): array
    {
        $challengeId = $challenge?->id;
        $contentCode = strtoupper(trim((string) $request->input('content_code')));

        $data = $request->validate([
            'challenge_category_id' => ['required', 'integer', 'exists:challenge_categories,id'],
            'content_code' => ['nullable', 'string', 'max:64'],
            'title' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:10000'],
            'time_limit_seconds' => ['required', 'integer', 'min:60', 'max:7200'],
            'base_xp' => ['required', 'integer', 'min:0', 'max:10000'],
            'order_index' => ['required', 'integer', 'min:0', 'max:100000'],
            'version_no' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
                Rule::unique('challenges', 'version_no')
                    ->where(fn ($query) => $query
                        ->where('content_code', $contentCode)
                        ->where('is_coding_challenge', true))
                    ->ignore($challengeId),
            ],
            'version_name' => ['required', 'string', 'max:100'],
            'version_code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('challenges', 'version_code')
                    ->where(fn ($query) => $query
                        ->where('content_code', $contentCode)
                        ->where('is_coding_challenge', true))
                    ->ignore($challengeId),
            ],
            'is_active' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:50'],
            'questions.*' => ['array'],
            'questions.*.title' => ['nullable', 'string', 'max:189'],
            'questions.*.problem_description' => ['required', 'string', 'max:20000'],
            'questions.*.language' => ['required', 'string', Rule::in(self::LANGUAGES)],
            'questions.*.starter_code' => ['nullable', 'string', 'max:50000'],
            'questions.*.reference_solution' => ['nullable', 'string', 'max:50000'],
            'questions.*.time_limit_seconds' => ['required', 'integer', 'min:60', 'max:7200'],
            'questions.*.base_xp' => ['required', 'integer', 'min:0', 'max:10000'],
            'questions.*.test_cases' => ['required', 'array', 'min:1', 'max:' . self::MAX_TEST_CASES_PER_QUESTION],
            'questions.*.test_cases.*' => ['array'],
            'questions.*.test_cases.*.input' => ['nullable', 'string', 'max:10000'],
            'questions.*.test_cases.*.expected_output' => ['required', 'string', 'max:10000'],
            'questions.*.test_cases.*.is_hidden' => ['nullable', 'boolean'],
        ], [
            'questions.required' => 'Add at least one coding problem.',
            'questions.*.problem_description.required' => 'Every problem needs a description.',
            'questions.*.test_cases.required' => 'Every problem needs at least one test case.',
            'questions.*.test_cases.min' => 'Every problem needs at least one test case.',
            'questions.*.test_cases.*.expected_output.required' => 'Every test case needs an expected output.',
        ]);

        $questions = array_values($data['questions']);

        foreach ($questions as &$question) {
            $question['test_cases'] = array_values($question['test_cases']);
        }
        unset($question);

        return [$data, $questions];
    }

    /**
     * The typed content code, or a generated unique one when the field was
     * left blank. Admins should not have to invent codes to create a challenge.
     */
    private function resolvedContentCode(array $data, ?Challenge $existing = null): string
    {
        $typed = strtoupper(trim((string) ($data['content_code'] ?? '')));

        if ($typed !== '') {
            return $typed;
        }

        if ($existing !== null && filled($existing->content_code)) {
            return (string) $existing->content_code;
        }

        return $this->contentService->uniqueChallengeContentCode(
            (int) $data['challenge_category_id'],
            true,
            (string) $data['title']
        );
    }

    private function challengePayload(array $data, ?Challenge $existing = null): array
    {
        return [
            'challenge_category_id' => (int) $data['challenge_category_id'],
            'content_code' => $this->resolvedContentCode($data, $existing),
            'title' => trim($data['title']),
            'description' => $data['description'] ?? '',
            'time_limit_seconds' => (int) $data['time_limit_seconds'],
            'base_xp' => (int) $data['base_xp'],
            'order_index' => (int) $data['order_index'],
            'is_coding_challenge' => true,
            'version_no' => (int) $data['version_no'],
            'version_name' => trim($data['version_name']),
            'version_code' => strtoupper(trim($data['version_code'])),
            'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }

    /**
     * Identity fields that group versions together may not change once the
     * challenge has history. Titles, descriptions, order and limits may.
     */
    private function assertChallengeWithHistoryIdentityUnchanged(Challenge $challenge, array $data): void
    {
        $changes = [];

        if ((int) $challenge->challenge_category_id !== (int) $data['challenge_category_id']) {
            $changes['challenge_category_id'] = 'Difficulty category cannot be changed after this challenge has submission history.';
        }
        $typedCode = strtoupper(trim((string) ($data['content_code'] ?? '')));

        // A blank field means "keep the current code", not "change it to nothing".
        if ($typedCode !== '' && strtoupper((string) $challenge->content_code) !== $typedCode) {
            $changes['content_code'] = 'Content code cannot be changed after this challenge has submission history.';
        }
        if ((int) $challenge->version_no !== (int) $data['version_no']) {
            $changes['version_no'] = 'Version number cannot be changed after this challenge has submission history.';
        }
        if (strtoupper((string) $challenge->version_code) !== strtoupper(trim($data['version_code']))) {
            $changes['version_code'] = 'Version code cannot be changed after this challenge has submission history.';
        }

        if ($changes !== []) {
            throw ValidationException::withMessages($changes);
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Persistence
    // ─────────────────────────────────────────────────────────────────────

    private function syncQuestions(Challenge $challenge, array $questions): void
    {
        $this->deleteQuestions($challenge);

        foreach ($questions as $questionIndex => $questionData) {
            $question = $challenge->codingQuestions()->create([
                'title' => $this->nullableText($questionData['title'] ?? null, 189),
                'problem_description' => trim($this->normalizeNewlines((string) $questionData['problem_description'])),
                'language' => (string) $questionData['language'],
                'starter_code' => $this->nullableCode($questionData['starter_code'] ?? null),
                'reference_solution' => $this->nullableCode($questionData['reference_solution'] ?? null),
                'time_limit_seconds' => (int) $questionData['time_limit_seconds'],
                'base_xp' => (int) $questionData['base_xp'],
                'order_index' => $questionIndex + 1,
            ]);

            foreach ($questionData['test_cases'] as $caseIndex => $caseData) {
                $question->testCases()->create([
                    'input' => $this->nullableCode($caseData['input'] ?? null),
                    'expected_output' => $this->normalizeNewlines((string) $caseData['expected_output']),
                    'is_hidden' => filter_var($caseData['is_hidden'] ?? false, FILTER_VALIDATE_BOOL),
                    'order_index' => $caseIndex + 1,
                ]);
            }
        }
    }

    /**
     * Test cases are removed explicitly so the result does not depend on the
     * database enforcing ON DELETE CASCADE.
     */
    private function deleteQuestions(Challenge $challenge): void
    {
        $questionIds = $challenge->codingQuestions()->pluck('coding_questions.id');

        if ($questionIds->isNotEmpty()) {
            TestCase::query()->whereIn('coding_question_id', $questionIds)->delete();
        }

        $challenge->codingQuestions()->delete();
    }

    /**
     * With history: re-applies only the title and reference solution of each
     * existing question, matched by position.
     */
    private function syncUngradedFields(Challenge $challenge, array $questions): void
    {
        $existing = $challenge->codingQuestions()->get()->values();

        foreach ($existing as $index => $question) {
            if (! array_key_exists($index, $questions)) {
                continue;
            }

            $title = $this->nullableText($questions[$index]['title'] ?? null, 189);
            $reference = $this->nullableCode($questions[$index]['reference_solution'] ?? null);

            if ($question->title !== $title || $question->reference_solution !== $reference) {
                $question->update([
                    'title' => $title,
                    'reference_solution' => $reference,
                ]);
            }
        }
    }

    /**
     * Anything that affects grading or what a learner sees during a timed
     * attempt: problem text, language, starter code, per-question limits and
     * the test cases. Titles and reference solutions are excluded.
     */
    private function gradedContentChanged(Challenge $challenge, array $questions): bool
    {
        $current = $challenge->codingQuestions()
            ->with('testCases')
            ->get()
            ->map(fn (CodingQuestion $question): array => [
                'problem_description' => trim((string) $question->problem_description),
                'language' => (string) $question->language,
                'starter_code' => $this->nullableCode($question->starter_code),
                'time_limit_seconds' => (int) $question->time_limit_seconds,
                'base_xp' => (int) $question->base_xp,
                'test_cases' => $question->testCases->map(fn (TestCase $case): array => [
                    'input' => $this->nullableCode($case->input),
                    'expected_output' => (string) $case->expected_output,
                    'is_hidden' => (bool) $case->is_hidden,
                ])->values()->all(),
            ])->values()->all();

        $submitted = collect($questions)->map(fn (array $question): array => [
            'problem_description' => trim($this->normalizeNewlines((string) ($question['problem_description'] ?? ''))),
            'language' => (string) ($question['language'] ?? ''),
            'starter_code' => $this->nullableCode($question['starter_code'] ?? null),
            'time_limit_seconds' => (int) ($question['time_limit_seconds'] ?? 0),
            'base_xp' => (int) ($question['base_xp'] ?? 0),
            'test_cases' => collect($question['test_cases'] ?? [])->values()->map(fn ($case): array => [
                'input' => $this->nullableCode($case['input'] ?? null),
                'expected_output' => $this->normalizeNewlines((string) ($case['expected_output'] ?? '')),
                'is_hidden' => filter_var($case['is_hidden'] ?? false, FILTER_VALIDATE_BOOL),
            ])->all(),
        ])->values()->all();

        return json_encode($current, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            !== json_encode($submitted, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // ─────────────────────────────────────────────────────────────────────
    // History and attempts
    // ─────────────────────────────────────────────────────────────────────

    /**
     * A coding challenge has history once any learner has started or
     * submitted one of its questions, in addition to the shared challenge
     * history the platform service tracks.
     */
    private function hasCodingHistory(Challenge $challenge): bool
    {
        if ($this->contentService->challengeHasHistory($challenge)) {
            return true;
        }

        $questionIds = $challenge->codingQuestions()->pluck('coding_questions.id');

        if ($questionIds->isEmpty()) {
            return false;
        }

        return CodingSubmission::query()->whereIn('coding_question_id', $questionIds)->exists()
            || CodingQuestionAttempt::query()->whereIn('coding_question_id', $questionIds)->exists();
    }

    /**
     * An attempt is in progress while its clock is still running and the
     * learner has not yet passed that question.
     */
    private function hasInProgressAttempts(Challenge $challenge): bool
    {
        $questions = $challenge->codingQuestions()->get(['id', 'time_limit_seconds'])->keyBy('id');

        if ($questions->isEmpty()) {
            return false;
        }

        $attempts = CodingQuestionAttempt::query()
            ->whereIn('coding_question_id', $questions->keys())
            ->where('expired', false)
            ->get(['id', 'user_id', 'coding_question_id', 'started_at']);

        $now = now()->timestamp;

        foreach ($attempts as $attempt) {
            $question = $questions->get($attempt->coding_question_id);
            if (! $question || ! $attempt->started_at) {
                continue;
            }

            $remaining = (int) $question->time_limit_seconds - max(0, $now - $attempt->started_at->timestamp);
            if ($remaining <= 0) {
                continue;
            }

            $alreadyPassed = CodingSubmission::query()
                ->where('user_id', $attempt->user_id)
                ->where('coding_question_id', $attempt->coding_question_id)
                ->where('status', 'passed')
                ->where('voided', false)
                ->exists();

            if (! $alreadyPassed) {
                return true;
            }
        }

        return false;
    }

    private function continuingAttemptsNotice(int $continuingAttempts): string
    {
        if ($continuingAttempts < 1) {
            return '';
        }

        $learners = $continuingAttempts === 1
            ? '1 learner has an attempt in progress on the previous version and will finish it there'
            : $continuingAttempts . ' learners have attempts in progress on the previous version and will finish them there';

        return ' ' . $learners . '. New attempts start on the published version.';
    }

    // ─────────────────────────────────────────────────────────────────────
    // Form helpers
    // ─────────────────────────────────────────────────────────────────────

    private function categories()
    {
        return ChallengeCategory::query()
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();
    }

    private function questionsForForm(Challenge $challenge): array
    {
        return $challenge->codingQuestions->map(fn (CodingQuestion $question): array => [
            'title' => $question->title,
            'problem_description' => $question->problem_description,
            'language' => $question->language ?: 'python',
            'starter_code' => $question->starter_code,
            'reference_solution' => $question->reference_solution,
            'time_limit_seconds' => $question->time_limit_seconds,
            'base_xp' => $question->base_xp,
            'test_cases' => $question->testCases->map(fn (TestCase $case): array => [
                'input' => $case->input,
                'expected_output' => $case->expected_output,
                'is_hidden' => (bool) $case->is_hidden,
            ])->values()->all(),
        ])->values()->all();
    }

    private function emptyQuestion(): array
    {
        return [
            'title' => '',
            'problem_description' => '',
            'language' => 'python',
            'starter_code' => '',
            'reference_solution' => '',
            'time_limit_seconds' => 600,
            'base_xp' => 100,
            'test_cases' => [
                ['input' => '', 'expected_output' => '', 'is_hidden' => false],
            ],
        ];
    }

    private function ensureCoding(Challenge $challenge): void
    {
        abort_unless((bool) $challenge->is_coding_challenge, 404);
    }

    private function nullableText(mixed $value, int $max): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }

    /**
     * Code and stdin keep their internal whitespace; only a value that is
     * entirely blank becomes NULL. Line endings are normalised so a form
     * submitted from Windows compares equal to what was stored.
     */
    private function nullableCode(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = $this->normalizeNewlines((string) $value);

        return trim($value) === '' ? null : $value;
    }

    private function normalizeNewlines(string $value): string
    {
        return str_replace(["\r\n", "\r"], "\n", $value);
    }
}
