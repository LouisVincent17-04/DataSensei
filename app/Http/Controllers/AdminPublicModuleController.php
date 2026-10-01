<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Lesson;
use App\Models\Module;
use App\Services\LessonBlockRenderer;
use App\Services\ModuleBlockConverter;
use App\Services\ModuleEditorContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * DataSensei Modules: the public curriculum at /module, open to every user
 * with or without a class and organised by year level.
 *
 * This is App\Models\Module and its lessons, not the Instructor Module Library
 * (ModuleLibraryItem) that instructors assign to classes. Creating a module
 * here also fans out one locked MCQ challenge per challenge level, so every
 * level's map has a slot for it the moment the module exists; the challenges
 * stay unavailable until an admin adds questions and publishes them.
 *
 * DataSensei Updates 5: modules are written with the visual module editor
 * (admin.partials.module-editor), the same one as the library. Each lesson is
 * one section card; a lesson written before the editor keeps its original
 * HTML and is only rewritten when its card is changed. A module is published
 * only with at least one learning outcome and one section.
 *
 * DataSensei Updates 7: the editor saves in the background. store() and
 * update() answer an AJAX request (Accept: application/json) with JSON, so
 * the admin stays on the same tab and scroll position; validation errors
 * come back as the usual 422 JSON. A normal form post still redirects.
 */
class AdminPublicModuleController extends Controller
{
    public const MAX_SECTIONS = 200;

    public const MAX_BLOCKS = 300;

    public function __construct(
        private readonly ModuleEditorContent $editorContent,
        private readonly LessonBlockRenderer $renderer,
        private readonly ModuleBlockConverter $converter,
    ) {
    }

    public function index()
    {
        $modules = Module::withCount('lessons')
            ->orderBy('order_index')
            ->orderBy('id')
            ->get(['id', 'title', 'description', 'order_index', 'year_level', 'xp_reward', 'is_boss', 'has_coding_exercises', 'is_published', 'learning_outcomes']);

        $groups = [];
        foreach (Module::YEAR_LEVELS as $level) {
            $groups[$level] = [];
        }
        foreach ($modules as $module) {
            $groups[$module->year_level][] = $module;
        }

        return view('admin.modules.index', [
            'groups' => $groups,
            'modules' => $modules,
            'levelCount' => ChallengeCategory::count(),
        ]);
    }

    public function create()
    {
        $module = new Module([
            'year_level' => Module::YEAR_LEVELS[0],
            'xp_reward' => 100,
            'is_boss' => false,
            'has_coding_exercises' => false,
        ]);
        $module->is_published = false;

        return view('admin.modules.create', [
            'module' => $module,
            'yearLevels' => Module::YEAR_LEVELS,
            'levelCount' => ChallengeCategory::count(),
            'sections' => [],
            'questions' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $cards = $this->cards($request);
        $questions = $this->questions($request);
        $outcomes = $this->editorContent->outcomes($request->input('learning_outcomes', []));
        $publish = ModuleEditorContent::targetStatus($request->input('intent'), false);

        $created = 0;

        $module = DB::transaction(function () use ($data, $cards, $questions, $outcomes, $publish, &$created): Module {
            $module = Module::create($data + [
                'order_index' => ((int) Module::max('order_index')) + 1,
                'learning_outcomes' => $outcomes,
                'review_questions' => $questions ?? [],
                'is_published' => $publish,
            ]);

            if ($cards !== null) {
                $this->syncLessons($module, $cards);
            }

            if ($publish) {
                $this->assertPublishable($module, $outcomes);
            }

            $created = $this->fanOutChallenges($module);

            return $module;
        });

        $message = 'Module '.($publish ? 'created and published' : 'saved as a draft').'. '.$created.' '.($created === 1 ? 'challenge was' : 'challenges were')
            .' created for it (one per level'.($data['has_coding_exercises'] ? ', plus one coding challenge per level' : '').').'
            .' They are unavailable to students until you add questions and publish them from Challenge Maps or Coding Challenges.';

        if ($request->expectsJson()) {
            return $this->savedResponse($module, $message, true);
        }

        return redirect()->route('admin.modules.edit', $module)->with('success', $message);
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'distinct', Rule::exists('modules', 'id')],
        ]);

        DB::transaction(function () use ($data): void {
            $position = 1;
            foreach ($data['order'] as $id) {
                Module::whereKey((int) $id)->update(['order_index' => $position++]);
            }

            // Anything not in the posted list keeps its relative order after them.
            $rest = Module::whereNotIn('id', $data['order'])->orderBy('order_index')->orderBy('id')->pluck('id');
            foreach ($rest as $id) {
                Module::whereKey($id)->update(['order_index' => $position++]);
            }
        });

        return redirect()->route('admin.modules.index')->with('success', 'Module order updated.');
    }

    public function edit(Module $module)
    {
        $module->loadCount(['lessons', 'challenges']);

        return view('admin.modules.edit', [
            'module' => $module,
            'yearLevels' => Module::YEAR_LEVELS,
            'sections' => $this->editorCards($module),
            'questions' => $module->review_questions,
        ]);
    }

    public function update(Request $request, Module $module): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);
        $cards = $this->cards($request);
        $questions = $this->questions($request);
        $intent = $request->input('intent');
        $outcomes = $request->has('learning_outcomes') || $intent !== null
            ? $this->editorContent->outcomes($request->input('learning_outcomes', []))
            : $module->learning_outcomes;
        $publish = ModuleEditorContent::targetStatus($intent, (bool) ($module->is_published ?? true));

        // Editing a module never touches the challenges fanned out for it.
        DB::transaction(function () use ($module, $data, $cards, $questions, $outcomes, $publish): void {
            $module->update($data + [
                'learning_outcomes' => $outcomes,
                'is_published' => $publish,
            ] + ($questions !== null ? ['review_questions' => $questions] : []));

            if ($cards !== null) {
                $this->syncLessons($module, $cards);
            }

            if ($publish) {
                $this->assertPublishable($module, $outcomes);
            }
        });

        $message = match ($intent) {
            'publish' => 'Module saved and published.',
            'unpublish' => 'Module saved and unpublished. Students no longer see it.',
            'draft' => 'Draft saved.',
            default => 'Module saved.',
        };

        if ($request->expectsJson()) {
            return $this->savedResponse($module, $message, false);
        }

        return redirect()->route('admin.modules.edit', $module)->with('success', $message);
    }

    /** Publish or unpublish from the list. Publishing checks the same rules. */
    public function toggleStatus(Module $module): RedirectResponse
    {
        if (! $module->is_published) {
            try {
                $this->assertPublishable($module, $module->learning_outcomes);
            } catch (ValidationException $exception) {
                return redirect()->route('admin.modules.index')
                    ->with('error', '"'.$module->title.'" cannot be published yet: '.collect($exception->errors())->flatten()->first());
            }
        }

        $module->update(['is_published' => ! $module->is_published]);

        return redirect()->route('admin.modules.index')->with('success', $module->is_published
            ? '"'.$module->title.'" is published. Students can see it.'
            : '"'.$module->title.'" is unpublished. Students no longer see it.');
    }

    /** The saved module exactly as students read it. */
    public function preview(Module $module)
    {
        $sections = $module->lessons()->get()
            ->map(fn (Lesson $lesson) => ['title' => (string) $lesson->title, 'html' => (string) $lesson->content])
            ->all();

        return $this->previewResponse($module->title, (string) $module->description, $module->learning_outcomes, $sections, $module->review_questions);
    }

    /** The editor's "Preview Module": what is in the editor now. Nothing is stored. */
    public function previewDraft(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:5000'],
            'lessons_json' => ['nullable', 'string', 'max:10000000'],
            'review_questions_json' => ['nullable', 'string', 'max:5000000'],
            'learning_outcomes' => ['nullable', 'array', 'max:'.ModuleEditorContent::MAX_OUTCOMES],
            'learning_outcomes.*' => ['nullable', 'string', 'max:'.ModuleEditorContent::MAX_OUTCOME_LENGTH],
        ]);

        $cards = array_slice($this->normalizeCards(array_values(array_filter(
            $this->editorContent->decodeList($data['lessons_json'] ?? null, 'lessons_json', 'learning content'),
            'is_array'
        ))), 0, self::MAX_SECTIONS);
        $questions = array_values(array_filter($this->editorContent->decodeList($data['review_questions_json'] ?? null, 'review_questions_json', 'review questions'), 'is_array'));

        // A lesson that was not changed is shown with its stored content, so
        // the preview is exactly what students read.
        $lessons = Lesson::whereIn('id', array_filter(array_map(fn (array $card) => (int) ($card['id'] ?? 0), $cards)))->get()->keyBy('id');
        $sections = array_map(function (array $card) use ($lessons): array {
            $lesson = $lessons->get((int) ($card['id'] ?? 0));

            return [
                'title' => $card['title'],
                'html' => $lesson && $this->lessonUnchanged($lesson, $card) ? (string) $lesson->content : $this->renderer->renderLesson($card['title'], $card['blocks']),
            ];
        }, $cards);

        return $this->previewResponse(
            trim((string) ($data['title'] ?? '')) ?: 'Untitled module',
            (string) ($data['description'] ?? ''),
            $this->editorContent->outcomes($data['learning_outcomes'] ?? []),
            $sections,
            $questions
        );
    }

    public function destroy(Module $module): RedirectResponse
    {
        if ($this->hasProgress($module)) {
            return redirect()->route('admin.modules.index')
                ->with('error', 'Cannot delete "'.$module->title.'": students already have progress in it. Edit it or move it instead.');
        }

        DB::transaction(function () use ($module): void {
            // Fanned-out challenges (and any attempts on them) stay; they just
            // stop pointing at a module.
            Challenge::where('module_id', $module->id)->update(['module_id' => null]);
            DB::table('module_user')->where('module_id', $module->id)->delete();
            DB::table('lessons')->where('module_id', $module->id)->delete();
            $module->delete();
        });

        return redirect()->route('admin.modules.index')->with('success', 'Module deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * The editor's answer to a background save: the outcome, the module's
     * publish status, and each section's lesson id in order, so sections
     * added in the editor are saved as those lessons from now on.
     */
    private function savedResponse(Module $module, string $message, bool $created): JsonResponse
    {
        $module->refresh();
        $lessons = Lesson::where('module_id', $module->id)->orderBy('order_index')->orderBy('id')->pluck('id');
        $progress = DB::table('lesson_user')
            ->whereIn('lesson_id', $lessons)
            ->select('lesson_id', DB::raw('COUNT(*) as total'))
            ->groupBy('lesson_id')
            ->pluck('total', 'lesson_id');

        return response()->json([
            'message' => $message,
            'created' => $created,
            'is_published' => (bool) ($module->is_published ?? true),
            'sections' => $lessons->map(fn ($id) => ['id' => (int) $id, 'progress' => (int) ($progress[$id] ?? 0)])->values()->all(),
            'question_count' => count($module->review_questions),
            'fields' => [
                'title' => (string) $module->title,
                'description' => (string) $module->description,
            ],
            'module_id' => $module->id,
            'edit_url' => route('admin.modules.edit', $module),
            'update_url' => route('admin.modules.update', $module),
            'cancel_url' => route('admin.modules.index'),
            'page_title' => 'Edit DataSensei Module',
        ], $created ? 201 : 200);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:5000'],
            'year_level' => ['required', Rule::in(Module::YEAR_LEVELS)],
            'xp_reward' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_boss' => ['nullable', 'boolean'],
            'has_coding_exercises' => ['nullable', 'boolean'],
            'intent' => ['nullable', Rule::in(['draft', 'save', 'publish', 'unpublish'])],
            'lessons_json' => ['nullable', 'string', 'max:10000000'],
            'review_questions_json' => ['nullable', 'string', 'max:5000000'],
            'learning_outcomes' => ['nullable', 'array', 'max:'.ModuleEditorContent::MAX_OUTCOMES],
            'learning_outcomes.*' => ['nullable', 'string', 'max:'.ModuleEditorContent::MAX_OUTCOME_LENGTH],
        ]);

        return [
            'title' => trim($data['title']),
            'description' => trim((string) ($data['description'] ?? '')),
            'year_level' => $data['year_level'],
            'xp_reward' => (int) $data['xp_reward'],
            'is_boss' => (bool) ($data['is_boss'] ?? false),
            'has_coding_exercises' => (bool) ($data['has_coding_exercises'] ?? false),
        ];
    }

    /**
     * One locked MCQ challenge per level (and one coding challenge per level
     * when the module has coding exercises). Returns how many were made.
     */
    private function fanOutChallenges(Module $module): int
    {
        $categories = ChallengeCategory::orderBy('order_index')->orderBy('id')->get();
        $created = 0;

        foreach ($categories as $category) {
            $nextOrder = ((int) Challenge::where('challenge_category_id', $category->id)->max('order_index')) + 1;

            Challenge::create([
                'challenge_category_id' => $category->id,
                'module_id' => $module->id,
                'title' => $module->title,
                // Unique by module and level, so two modules with the same
                // title never collide on the (content_code, version_code) index.
                'content_code' => 'M'.$module->id.'-'.strtoupper((string) $category->slug).'-MCQ',
                'description' => 'Practice challenge for '.$module->title.'. Add questions and publish when ready.',
                'is_coding_challenge' => 0,
                'is_active' => 0,
                'order_index' => $nextOrder,
                'visibility' => Challenge::VISIBILITY_PLATFORM,
                'created_by' => null,
            ]);
            $created++;

            if ($module->has_coding_exercises) {
                Challenge::create([
                    'challenge_category_id' => $category->id,
                    'module_id' => $module->id,
                    'title' => $module->title.' — Coding',
                    'content_code' => 'M'.$module->id.'-'.strtoupper((string) $category->slug).'-CODE',
                    'description' => 'Practice challenge for '.$module->title.'. Add questions and publish when ready.',
                    'is_coding_challenge' => 1,
                    'is_active' => 0,
                    'order_index' => $nextOrder + 1,
                    'visibility' => Challenge::VISIBILITY_PLATFORM,
                    'created_by' => null,
                ]);
                $created++;
            }
        }

        return $created;
    }

    /**
     * The section cards the editor posted, or null when the request carries
     * none (the lessons are then left as they are).
     *
     * @return list<array<string, mixed>>|null
     */
    private function cards(Request $request): ?array
    {
        if (! $request->has('lessons_json')) {
            return null;
        }

        $cards = $this->editorContent->decodeList($request->input('lessons_json'), 'lessons_json', 'learning content');

        if (count($cards) > self::MAX_SECTIONS) {
            throw ValidationException::withMessages([
                'lessons_json' => 'A module can hold at most '.self::MAX_SECTIONS.' sections.',
            ]);
        }

        foreach ($cards as $index => $card) {
            if (is_array($card) && isset($card['blocks']) && (! is_array($card['blocks']) || count($card['blocks']) > self::MAX_BLOCKS)) {
                throw ValidationException::withMessages([
                    'lessons_json' => 'Section '.($index + 1).' can hold at most '.self::MAX_BLOCKS.' content blocks.',
                ]);
            }
        }

        $this->editorContent->assertSections(
            array_map(fn ($card) => is_array($card) ? ['heading' => $card['title'] ?? $card['heading'] ?? ''] : $card, $cards),
            'lessons_json'
        );

        return $this->normalizeCards($cards);
    }

    /**
     * Section cards as {id, title, blocks}. The module builder sends content
     * blocks; a card in the earlier section-editor shape (heading, body,
     * code, lists, html) is converted to blocks.
     *
     * @param  list<array<string, mixed>>  $cards
     * @return list<array{id: int|null, title: string, blocks: list<array<string, mixed>>}>
     */
    private function normalizeCards(array $cards): array
    {
        return array_map(function (array $card): array {
            $blocks = isset($card['blocks']) && is_array($card['blocks'])
                ? $this->renderer->normalize(array_values($card['blocks']))
                : $this->converter->fromSection($card, true);

            return [
                'id' => ((int) ($card['id'] ?? 0)) ?: null,
                'title' => trim(preg_replace('/\s+/', ' ', (string) ($card['title'] ?? $card['heading'] ?? '')) ?? ''),
                'blocks' => $blocks,
            ];
        }, $cards);
    }

    /**
     * True when a card still holds exactly the lesson as it is stored: same
     * title and the same blocks the lesson converts to.
     *
     * @param  array{title: string, blocks: list<array<string, mixed>>}  $card
     */
    private function lessonUnchanged(Lesson $lesson, array $card): bool
    {
        return trim(preg_replace('/\s+/', ' ', (string) $lesson->title) ?? '') === $card['title']
            && $this->converter->fromLesson($lesson) === $card['blocks'];
    }

    /** @return list<array<string, mixed>>|null */
    private function questions(Request $request): ?array
    {
        if (! $request->has('review_questions_json')) {
            return null;
        }

        $questions = $this->editorContent->decodeList($request->input('review_questions_json'), 'review_questions_json', 'review questions');
        $this->editorContent->assertQuestions($questions, 'review_questions_json');

        return $questions;
    }

    /**
     * Makes the module's lessons match the section cards, in order.
     *
     * A card with an id is that lesson: when its title and blocks are
     * unchanged the lesson is left exactly as it is (only its position may
     * change), so a lesson written before the builder keeps its original
     * HTML byte for byte until it is edited.
     * A card without an id becomes a new lesson. A lesson with no card is
     * deleted, unless students already have progress in it.
     *
     * @param  list<array<string, mixed>>  $cards
     */
    private function syncLessons(Module $module, array $cards): void
    {
        $existing = Lesson::where('module_id', $module->id)->get()->keyBy('id');
        $kept = [];

        // Knowledge checks are checked in the sections that will be written
        // (new or changed); an untouched lesson is left as it is.
        $this->editorContent->assertKnowledgeChecks(array_map(function (array $card) use ($existing): array {
            $lesson = $card['id'] ? $existing->get($card['id']) : null;

            return $lesson && $this->lessonUnchanged($lesson, $card) ? [] : $card['blocks'];
        }, $cards), 'lessons_json');

        foreach ($cards as $index => $card) {
            $position = $index + 1;
            $id = (int) ($card['id'] ?? 0);

            if ($id > 0) {
                $lesson = $existing->get($id);

                if (! $lesson || isset($kept[$id])) {
                    throw ValidationException::withMessages([
                        'lessons_json' => 'Section '.$position.' is no longer part of this module. Reload the editor and try again.',
                    ]);
                }

                $kept[$id] = true;

                if (! $this->lessonUnchanged($lesson, $card)) {
                    $this->fillLesson($lesson, $card['title'], $card['blocks']);
                }

                $lesson->order_index = $position;

                if ($lesson->isDirty()) {
                    $lesson->save();
                }

                continue;
            }

            $lesson = new Lesson(['module_id' => $module->id, 'order_index' => $position]);
            $this->fillLesson($lesson, $card['title'], $card['blocks']);
            $lesson->save();
        }

        $removed = $existing->keys()->diff(array_keys($kept));

        if ($removed->isNotEmpty()) {
            $withProgress = DB::table('lesson_user')->whereIn('lesson_id', $removed->all())->pluck('lesson_id')->unique();

            if ($withProgress->isNotEmpty()) {
                $titles = $existing->only($withProgress->all())->pluck('title')->map(fn ($title) => '"'.$title.'"')->implode(', ');

                throw ValidationException::withMessages([
                    'lessons_json' => 'These sections cannot be deleted because students already have progress in them: '.$titles.'.',
                ]);
            }

            Lesson::whereIn('id', $removed->all())->where('module_id', $module->id)->delete();
        }
    }

    /** @param  list<array<string, mixed>>  $blocks  normalized content blocks */
    private function fillLesson(Lesson $lesson, string $title, array $blocks): void
    {
        $lesson->title = $title;
        $lesson->blocks = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $lesson->content = $this->renderer->renderLesson($title, $blocks);
    }

    /**
     * The lessons as the builder's sections: title and content blocks
     * (converted from the stored HTML or blocks), with how many students have
     * started each one (such a section cannot be deleted).
     *
     * @return list<array<string, mixed>>
     */
    private function editorCards(Module $module): array
    {
        $lessons = $module->lessons()->get();
        $progress = DB::table('lesson_user')
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->select('lesson_id', DB::raw('COUNT(*) as total'))
            ->groupBy('lesson_id')
            ->pluck('total', 'lesson_id');

        return $lessons->map(fn (Lesson $lesson) => [
            'id' => $lesson->id,
            'title' => (string) $lesson->title,
            'blocks' => $this->converter->fromLesson($lesson),
            'progress' => (int) ($progress[$lesson->id] ?? 0),
        ])->values()->all();
    }

    /**
     * Published modules need at least one learning outcome and one section.
     *
     * @param  list<string>  $outcomes
     */
    private function assertPublishable(Module $module, array $outcomes): void
    {
        $this->editorContent->assertPublishable($outcomes);

        if (! Lesson::where('module_id', $module->id)->exists()) {
            throw ValidationException::withMessages([
                'lessons_json' => 'Add at least one section under "Learning Content" before publishing this module.',
            ]);
        }
    }

    /**
     * The module preview: the learning room's reading column with every
     * section in order, the outcomes first and the review questions last.
     *
     * @param  list<string>  $outcomes
     * @param  list<array{title: string, html: string}>  $sections
     * @param  list<array<string, mixed>>  $questions
     */
    private function previewResponse(string $title, string $description, array $outcomes, array $sections, array $questions)
    {
        $html = view('admin.modules._preview_body', [
            'title' => $title,
            'description' => $description,
            'outcomes' => $outcomes,
            'sections' => $sections,
            'questions' => $questions,
        ])->render();

        return response(view('admin.modules.lessons._preview_frame', [
            'html' => $html,
            'frameTitle' => 'Module preview: '.$title,
        ])->render(), 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    private function hasProgress(Module $module): bool
    {
        $lessonIds = DB::table('lessons')->where('module_id', $module->id)->pluck('id');

        if ($lessonIds->isNotEmpty() && DB::table('lesson_user')->whereIn('lesson_id', $lessonIds)->exists()) {
            return true;
        }

        return DB::table('module_user')
            ->where('module_id', $module->id)
            ->where('is_completed', 1)
            ->exists();
    }
}
