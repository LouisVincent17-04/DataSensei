<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ChallengeOption;
use App\Models\CodingQuestion;
use App\Models\CodingQuestionAttempt;
use App\Models\CodingSubmission;
use App\Models\TestCase;
use App\Services\CodingChallengeTestRunner;
use App\Services\PlatformContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Instructor-built MCQ and coding challenges.
 *
 * Every challenge made here is a row in `challenges` with
 * visibility = instructor, created_by = the instructor, always in the
 * University Student level. Students never see it on the public path: it
 * reaches them only through a class_challenge_assignments row for a class
 * they are enrolled in (InstructorClassChallengeController), and they take
 * it with the ordinary quiz and coding screens.
 *
 * Instructors do not manage versions: each challenge is a single version
 * with its own content code, so publishing one never touches another.
 */
class InstructorChallengeBuilderController extends Controller
{
    public const CATEGORY_SLUG = 'university-student';

    public const TYPES = ['mcq', 'coding'];

    public const LANGUAGES = ['python'];

    public const MAX_TEST_CASES_PER_QUESTION = CodingChallengeTestRunner::MAX_TEST_CASES;

    /**
     * Only pictures uploaded through uploadImage() may be attached to a question:
     * a path under /uploads/challenges with an image extension and no ".." segment.
     * Identical to AdminMcqChallengeController::IMAGE_PATH_PATTERN.
     */
    public const IMAGE_PATH_PATTERN = '/^\/uploads\/challenges\/(?!.*\.\.)[A-Za-z0-9_\-.\/]+\.(png|jpe?g|gif|webp)$/i';

    private const IMAGE_DIRECTORY = 'uploads/challenges';

    public function __construct(
        private readonly PlatformContentService $contentService,
        private readonly CodingChallengeTestRunner $testRunner,
    ) {
    }

    public function index(Request $request): View
    {
        $challenges = $this->ownedQuery()
            ->withCount(['questions', 'codingQuestions', 'classAssignments'])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('instructor.challenge-builder.index', [
            'challenges' => $challenges,
            'category' => $this->category(),
        ]);
    }

    public function create(Request $request): View
    {
        $type = $this->requestedType($request);
        $this->categoryOrFail();

        if ($type === 'coding') {
            return view('instructor.challenge-builder.create', [
                'type' => 'coding',
                'challenge' => new Challenge([
                    'time_limit_seconds' => 1800,
                    'base_xp' => 100,
                    'is_active' => false,
                ]),
                'questions' => old('questions', [$this->emptyCodingQuestion()]),
                'hasHistory' => false,
            ]);
        }

        return view('instructor.challenge-builder.create', [
            'type' => 'mcq',
            'challenge' => new Challenge([
                'time_limit_seconds' => 600,
                'base_xp' => 100,
                'is_active' => false,
            ]),
            'questions' => old('questions', [$this->emptyMcqQuestion()]),
            'hasHistory' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $this->requestedType($request);
        $category = $this->categoryOrFail();

        if ($type === 'coding') {
            [$data, $questions] = $this->validatedCodingData($request);
        } else {
            [$data, $questions] = $this->validatedMcqData($request);
        }

        $challenge = DB::transaction(function () use ($type, $category, $data, $questions): Challenge {
            $challenge = Challenge::create($this->challengePayload($data, $type === 'coding', $category));

            if ($type === 'coding') {
                $this->syncCodingQuestions($challenge, $questions);
            } else {
                $this->syncMcqQuestions($challenge, $questions);
            }

            return $challenge;
        }, 3);

        return redirect()
            ->route('instructor.challenge-builder.index')
            ->with('success', ($type === 'coding' ? 'Coding challenge' : 'Quiz challenge') . ' "' . $challenge->title . '" saved.'
                . ($challenge->is_active ? ' Give it to a class from Class Challenges so students can take it.' : ' It stays unavailable to students until you publish it.'));
    }

    /**
     * Runs a reference solution against the submitted test cases and reports
     * the result. Nothing is persisted: the form calls this before saving.
     * Same contract as the admin coding challenge manager.
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

    /**
     * Stores a picture for an MCQ question under public/uploads/challenges and
     * returns its public URL path for the form's hidden image_path field.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $file = $request->file('image');
        $extension = strtolower((string) $file->guessExtension() ?: $file->getClientOriginalExtension());
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            throw ValidationException::withMessages(['image' => 'The picture must be a jpg, jpeg, png, webp or gif file.']);
        }

        $directory = public_path(self::IMAGE_DIRECTORY);
        File::ensureDirectoryExists($directory);

        $filename = Str::lower(Str::random(40)) . '.' . $extension;
        $file->move($directory, $filename);

        return response()->json([
            'url' => '/' . self::IMAGE_DIRECTORY . '/' . $filename,
        ]);
    }

    public function edit(Request $request, Challenge $challenge): View
    {
        $this->ensureOwned($challenge);

        if ($challenge->is_coding_challenge) {
            $challenge->load('codingQuestions.testCases');

            return view('instructor.challenge-builder.edit', [
                'type' => 'coding',
                'challenge' => $challenge,
                'questions' => old('questions', $this->codingQuestionsForForm($challenge)),
                'hasHistory' => $this->hasCodingHistory($challenge),
            ]);
        }

        $challenge->load('questions.options');

        return view('instructor.challenge-builder.edit', [
            'type' => 'mcq',
            'challenge' => $challenge,
            'questions' => old('questions', $this->mcqQuestionsForForm($challenge)),
            'hasHistory' => $this->contentService->challengeHasHistory($challenge),
        ]);
    }

    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->ensureOwned($challenge);
        $isCoding = (bool) $challenge->is_coding_challenge;

        if ($isCoding) {
            [$data, $questions] = $this->validatedCodingData($request);
        } else {
            [$data, $questions] = $this->validatedMcqData($request);
        }

        DB::transaction(function () use ($challenge, $isCoding, $data, $questions): void {
            $locked = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureOwned($locked);

            $hasHistory = $isCoding
                ? $this->hasCodingHistory($locked)
                : $this->contentService->challengeHasHistory($locked);

            if ($hasHistory) {
                if ($isCoding) {
                    if ($this->codingGradedContentChanged($locked, $questions)) {
                        throw ValidationException::withMessages([
                            'questions' => 'Students have already worked on this challenge. Only the title, description, availability, problem titles and reference solutions may change; problem descriptions, starter code, limits and test cases are frozen.',
                        ]);
                    }
                } else {
                    $this->assertMcqWithHistoryUnchanged($locked, $data);

                    if ($this->contentService->challengeQuestionsChanged($locked, $questions)) {
                        throw ValidationException::withMessages([
                            'questions' => 'Students have already attempted this challenge. Only the title, description, availability and question pictures may change; questions and answer choices are frozen. Build a new challenge to change them.',
                        ]);
                    }
                }
            }

            // The level, owner and visibility never change after creation.
            $locked->update([
                'title' => trim($data['title']),
                'description' => $data['description'] ?? '',
                'time_limit_seconds' => (int) $data['time_limit_seconds'],
                'base_xp' => (int) $data['base_xp'],
                'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
            ]);

            if (! $hasHistory) {
                if ($isCoding) {
                    $this->syncCodingQuestions($locked, $questions);
                } else {
                    $this->syncMcqQuestions($locked, $questions);
                }
            } elseif ($isCoding) {
                $this->syncCodingUngradedFields($locked, $questions);
            } else {
                $this->syncMcqQuestionImages($locked, $questions);
            }
        }, 3);

        return redirect()
            ->route('instructor.challenge-builder.index')
            ->with('success', ($isCoding ? 'Coding challenge' : 'Quiz challenge') . ' updated.');
    }

    public function destroy(Request $request, Challenge $challenge): RedirectResponse
    {
        $this->ensureOwned($challenge);

        $deleted = DB::transaction(function () use ($challenge): bool {
            $locked = Challenge::query()->whereKey($challenge->id)->lockForUpdate()->firstOrFail();
            $this->ensureOwned($locked);

            $hasHistory = $locked->is_coding_challenge
                ? $this->hasCodingHistory($locked)
                : $this->contentService->challengeHasHistory($locked);

            if ($hasHistory) {
                return false;
            }

            $locked->classAssignments()->delete();

            if ($locked->is_coding_challenge) {
                $this->deleteCodingQuestions($locked);
            } else {
                $this->deleteMcqQuestions($locked);
            }

            $locked->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'Students have already worked on this challenge, so it cannot be deleted. Mark it unavailable instead.');
        }

        return redirect()
            ->route('instructor.challenge-builder.index')
            ->with('success', 'Challenge deleted.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // Ownership and level
    // ─────────────────────────────────────────────────────────────────────

    /** Only the challenges this instructor built; platform content is never reachable here. */
    private function ownedQuery()
    {
        return Challenge::query()
            ->where('visibility', Challenge::VISIBILITY_INSTRUCTOR)
            ->where('created_by', (int) Auth::id());
    }

    private function ensureOwned(Challenge $challenge): void
    {
        abort_unless(
            $challenge->isInstructorOwned() && (int) $challenge->created_by === (int) Auth::id(),
            404
        );
    }

    private function category(): ?ChallengeCategory
    {
        return ChallengeCategory::query()->where('slug', self::CATEGORY_SLUG)->first();
    }

    private function categoryOrFail(): ChallengeCategory
    {
        $category = $this->category();

        abort_if($category === null, 404, 'The University Student challenge level has not been set up yet. Ask an administrator to add it.');

        return $category;
    }

    private function requestedType(Request $request): string
    {
        $type = strtolower(trim((string) $request->input('type', 'mcq')));

        abort_unless(in_array($type, self::TYPES, true), 404);

        return $type;
    }

    /**
     * Every instructor challenge is its own single version, so its content
     * code carries the owner and a random suffix: two instructors (or one
     * instructor twice) may use the same title without colliding on the
     * challenges (content_code, version_code) unique index.
     */
    private function newContentCode(bool $coding, string $title): string
    {
        $prefix = 'I' . (int) Auth::id() . '-' . ($coding ? 'CODE' : 'MCQ') . '-';
        $slug = strtoupper(Str::slug($title, '-')) ?: 'CHALLENGE';
        $available = max(1, 64 - strlen($prefix) - 9);

        do {
            $code = $prefix . substr($slug, 0, $available) . '-' . strtoupper(Str::random(8));
        } while (Challenge::query()->where('content_code', $code)->exists());

        return $code;
    }

    private function challengePayload(array $data, bool $coding, ChallengeCategory $category): array
    {
        return [
            'challenge_category_id' => (int) $category->id,
            'content_code' => $this->newContentCode($coding, (string) $data['title']),
            'title' => trim($data['title']),
            'description' => $data['description'] ?? '',
            'time_limit_seconds' => (int) $data['time_limit_seconds'],
            'base_xp' => (int) $data['base_xp'],
            'order_index' => 0,
            'is_coding_challenge' => $coding,
            'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
            'visibility' => Challenge::VISIBILITY_INSTRUCTOR,
            'created_by' => (int) Auth::id(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────
    // MCQ
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return array{0: array, 1: array<int, array>}
     */
    private function validatedMcqData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:10000'],
            'time_limit_seconds' => ['required', 'integer', 'min:60', 'max:21600'],
            'base_xp' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1', 'max:200'],
            'questions.*.question_text' => ['required', 'string', 'max:10000'],
            'questions.*.image_path' => ['nullable', 'string', 'max:255', 'regex:' . self::IMAGE_PATH_PATTERN],
            'questions.*.correct_option' => ['required', 'integer', 'min:0'],
            'questions.*.options' => ['required', 'array', 'min:2', 'max:10'],
            'questions.*.options.*.option_text' => ['required', 'string', 'max:5000'],
        ], [
            'questions.required' => 'Add at least one question.',
            'questions.*.question_text.required' => 'Every question needs its text.',
            'questions.*.options.min' => 'Every question needs at least two answer choices.',
            'questions.*.options.*.option_text.required' => 'Every answer choice needs its text.',
        ]);

        $questions = array_values($data['questions']);

        foreach ($questions as $questionIndex => &$question) {
            $question['options'] = array_values($question['options']);
            $correctIndex = (int) $question['correct_option'];

            if (! array_key_exists($correctIndex, $question['options'])) {
                throw ValidationException::withMessages([
                    "questions.{$questionIndex}.correct_option" => 'Select one valid correct answer for every question.',
                ]);
            }
        }
        unset($question);

        return [$data, $questions];
    }

    /** With attempt history the timer and XP are frozen, exactly like the admin MCQ manager. */
    private function assertMcqWithHistoryUnchanged(Challenge $challenge, array $data): void
    {
        $changes = [];

        if ((int) $challenge->time_limit_seconds !== (int) $data['time_limit_seconds']) {
            $changes['time_limit_seconds'] = 'The time limit cannot change after students have attempted this challenge.';
        }
        if ((int) $challenge->base_xp !== (int) $data['base_xp']) {
            $changes['base_xp'] = 'The XP cannot change after students have attempted this challenge.';
        }

        if ($changes !== []) {
            throw ValidationException::withMessages($changes);
        }
    }

    private function syncMcqQuestions(Challenge $challenge, array $questions): void
    {
        $this->deleteMcqQuestions($challenge);

        foreach ($questions as $questionIndex => $questionData) {
            $question = $challenge->questions()->create([
                'challenge_category_id' => $challenge->challenge_category_id,
                'question_text' => trim($questionData['question_text']),
                'order_index' => $questionIndex + 1,
                'image_path' => $this->normalizeImagePath($questionData['image_path'] ?? null),
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

    /**
     * Options are removed explicitly so the result does not depend on the
     * database enforcing ON DELETE CASCADE.
     */
    private function deleteMcqQuestions(Challenge $challenge): void
    {
        $questionIds = $challenge->questions()->pluck('challenge_questions.id');

        if ($questionIds->isNotEmpty()) {
            ChallengeOption::query()->whereIn('challenge_question_id', $questionIds)->delete();
        }

        $challenge->questions()->delete();
    }

    /**
     * Re-attaches pictures to the existing questions (matched by position)
     * without touching the question text or answer choices.
     */
    private function syncMcqQuestionImages(Challenge $challenge, array $questions): void
    {
        $existing = $challenge->questions()->get()->values();

        foreach ($existing as $index => $question) {
            if (! array_key_exists($index, $questions)) {
                continue;
            }

            $imagePath = $this->normalizeImagePath($questions[$index]['image_path'] ?? null);

            if ($question->image_path !== $imagePath) {
                $question->update(['image_path' => $imagePath]);
            }
        }
    }

    private function mcqQuestionsForForm(Challenge $challenge): array
    {
        return $challenge->questions->map(function ($question): array {
            $options = $question->options->values();
            $correctIndex = $options->search(fn ($option) => (bool) $option->is_correct);

            return [
                'question_text' => $question->question_text,
                'image_path' => $question->image_path,
                'correct_option' => $correctIndex === false ? 0 : $correctIndex,
                'options' => $options->map(fn ($option): array => [
                    'option_text' => $option->option_text,
                ])->all(),
            ];
        })->values()->all();
    }

    private function emptyMcqQuestion(): array
    {
        return [
            'question_text' => '',
            'image_path' => null,
            'correct_option' => 0,
            'options' => [
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
                ['option_text' => ''],
            ],
        ];
    }

    private function normalizeImagePath(mixed $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '' || preg_match(self::IMAGE_PATH_PATTERN, $path) !== 1) {
            return null;
        }

        return $path;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Coding
    // ─────────────────────────────────────────────────────────────────────

    /**
     * @return array{0: array, 1: array<int, array>}
     */
    private function validatedCodingData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:10000'],
            'time_limit_seconds' => ['required', 'integer', 'min:60', 'max:7200'],
            'base_xp' => ['required', 'integer', 'min:0', 'max:10000'],
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

    private function syncCodingQuestions(Challenge $challenge, array $questions): void
    {
        $this->deleteCodingQuestions($challenge);

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

    private function deleteCodingQuestions(Challenge $challenge): void
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
    private function syncCodingUngradedFields(Challenge $challenge, array $questions): void
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
    private function codingGradedContentChanged(Challenge $challenge, array $questions): bool
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

    private function codingQuestionsForForm(Challenge $challenge): array
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

    private function emptyCodingQuestion(): array
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

    // ─────────────────────────────────────────────────────────────────────
    // Text helpers
    // ─────────────────────────────────────────────────────────────────────

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
