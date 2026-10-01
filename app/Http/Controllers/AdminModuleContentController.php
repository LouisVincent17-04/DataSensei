<?php

namespace App\Http\Controllers;

use App\Models\ModuleLibraryItem;
use App\Services\ModuleBlockConverter;
use App\Services\ModuleEditorContent;
use App\Services\PlatformContentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Admin: the Instructor Module Library (ModuleLibraryItem).
 *
 * These module versions are only for classes: an instructor assigns one to a
 * class and its students open it from the Modules page under that class. They
 * are a different library from the DataSensei Modules (App\Models\Module,
 * AdminPublicModuleController), which are open to everyone by year level.
 *
 * DataSensei Updates 5: the raw JSON fields are replaced by the visual
 * module editor (admin.partials.module-editor). It still posts the sections
 * and review questions as JSON, built from its fields, and keeps every key it
 * does not show. A version is published only with at least one learning
 * outcome ("What Students Will Learn").
 *
 * DataSensei Updates 7: the editor saves in the background. store() and
 * update() answer an AJAX request (Accept: application/json) with JSON and
 * validation errors come back as the usual 422 JSON; a normal form post still
 * redirects. Validation and the checks for versions assigned to classes are
 * the same either way.
 */
class AdminModuleContentController extends Controller
{
    public function __construct(
        private readonly PlatformContentService $contentService,
        private readonly ModuleEditorContent $editorContent,
        private readonly ModuleBlockConverter $converter,
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', 'all');

        $modules = ModuleLibraryItem::query()
            ->withCount('classAssignments')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('module_code', 'like', "%{$search}%")
                        ->orWhere('version_code', 'like', "%{$search}%")
                        ->orWhere('version_name', 'like', "%{$search}%")
                        ->orWhere('module_no', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->paginate(12)
            ->withQueryString();

        return view('admin.module-library.index', compact('modules', 'search', 'status'));
    }

    public function create(Request $request): View
    {
        $moduleNo = max(1, (int) $request->integer('module_no', ((int) ModuleLibraryItem::max('module_no')) + 1));
        $versionNo = $this->contentService->nextModuleVersion($moduleNo);

        return view('admin.module-library.create', [
            'module' => new ModuleLibraryItem([
                'module_no' => $moduleNo,
                'version_no' => $versionNo,
                'version_name' => 'Version ' . $versionNo,
                'version_code' => 'V' . $versionNo,
                'estimated_minutes' => 45,
                'sort_order' => $moduleNo,
                'is_active' => false,
            ]),
            'sections' => [],
            'questions' => [],
            'hasReferences' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request);
        [$sections, $questions] = $this->editorContent($request, $data);
        $outcomes = $this->editorContent->outcomes($request->input('learning_outcomes', []));
        $publish = ModuleEditorContent::targetStatus($data['intent'] ?? null, filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL));

        if ($publish) {
            $this->editorContent->assertPublishable($outcomes);
        }

        $module = DB::transaction(function () use ($data, $sections, $questions, $outcomes, $publish): ModuleLibraryItem {
            return ModuleLibraryItem::create($this->modulePayload($data, $sections, $questions, $outcomes, $publish));
        });

        $message = $publish ? 'Module version created and published.' : 'Module version saved as a draft.';

        if ($request->expectsJson()) {
            return $this->savedResponse($module, $message, true);
        }

        return redirect()
            ->route('admin.module-library.edit', $module)
            ->with('success', $message);
    }

    public function show(ModuleLibraryItem $module): View
    {
        $module->loadCount('classAssignments');

        return view('admin.module-library.show', [
            'module' => $module,
            'hasReferences' => $this->contentService->moduleHasReferences($module),
            'nextVersionNo' => $this->contentService->nextModuleVersion((int) $module->module_no),
        ]);
    }

    public function edit(ModuleLibraryItem $module): View
    {
        return view('admin.module-library.edit', [
            'module' => $module,
            'sections' => $this->builderSections($module->content_sections),
            'questions' => $module->mcq_questions,
            'hasReferences' => $this->contentService->moduleHasReferences($module),
        ]);
    }

    /** The saved version as students see it. */
    public function preview(ModuleLibraryItem $module): View
    {
        return $this->previewView($module, $module->content_sections, $module->mcq_questions, $module->learning_outcomes);
    }

    /**
     * The editor's "Preview Module": what is in the editor now, saved or not,
     * in the student module viewer. Nothing is stored.
     */
    public function previewDraft(Request $request): View
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:10000'],
            'module_no' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'version_name' => ['nullable', 'string', 'max:189'],
            'estimated_minutes' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'content_sections_json' => ['nullable', 'string', 'max:5000000'],
            'mcq_questions_json' => ['nullable', 'string', 'max:5000000'],
            'learning_outcomes' => ['nullable', 'array', 'max:'.ModuleEditorContent::MAX_OUTCOMES],
            'learning_outcomes.*' => ['nullable', 'string', 'max:'.ModuleEditorContent::MAX_OUTCOME_LENGTH],
            'section_only' => ['nullable', 'boolean'],
        ]);

        $module = new ModuleLibraryItem([
            'title' => trim((string) ($data['title'] ?? '')) ?: 'Untitled module',
            'description' => $data['description'] ?? '',
            'module_no' => (int) ($data['module_no'] ?? 0),
            'version_name' => $data['version_name'] ?? '',
            'estimated_minutes' => (int) ($data['estimated_minutes'] ?? 0),
        ]);

        $sections = $this->resolveSections(
            array_values(array_filter($this->editorContent->decodeList($data['content_sections_json'] ?? null, 'content_sections_json', 'learning content'), 'is_array')),
            [],
            false
        );
        $questions = array_values(array_filter($this->editorContent->decodeList($data['mcq_questions_json'] ?? null, 'mcq_questions_json', 'review questions'), 'is_array'));

        return $this->previewView(
            $module,
            $sections,
            (bool) ($data['section_only'] ?? false) ? [] : $questions,
            (bool) ($data['section_only'] ?? false) ? [] : $this->editorContent->outcomes($data['learning_outcomes'] ?? []),
            (bool) ($data['section_only'] ?? false)
        );
    }

    public function update(Request $request, ModuleLibraryItem $module): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request, $module);
        [$sections, $questions] = $this->editorContent($request, $data, $module);
        $outcomes = $request->has('learning_outcomes') || $request->has('intent')
            ? $this->editorContent->outcomes($request->input('learning_outcomes', []))
            : $module->learning_outcomes;
        $current = array_key_exists('is_active', $data) && ! array_key_exists('intent', $data)
            ? filter_var($data['is_active'], FILTER_VALIDATE_BOOL)
            : (bool) $module->is_active;
        $publish = ModuleEditorContent::targetStatus($data['intent'] ?? null, $current);

        if ($publish) {
            $this->editorContent->assertPublishable($outcomes);
        }

        DB::transaction(function () use ($module, $data, $sections, $questions, $outcomes, $publish): void {
            $lockedModule = ModuleLibraryItem::query()->whereKey($module->id)->lockForUpdate()->firstOrFail();

            if ($this->contentService->moduleHasReferences($lockedModule)) {
                $this->assertReferencedModuleIdentityUnchanged($lockedModule, $data);

                if ($this->contentService->moduleContentChanged($lockedModule, $sections, $questions)) {
                    throw ValidationException::withMessages([
                        'content_sections_json' => 'This module version is already assigned to a class. Duplicate it as a new version before changing its learning content.',
                    ]);
                }
            }

            $lockedModule->update($this->modulePayload($data, $sections, $questions, $outcomes, $publish));
        }, 3);

        $message = match ($data['intent'] ?? null) {
            'publish' => 'Module version saved and published.',
            'unpublish' => 'Module version saved and unpublished. Instructors can no longer assign it.',
            'draft' => 'Draft saved.',
            default => 'Changes saved.',
        };

        if ($request->expectsJson()) {
            return $this->savedResponse($module, $message, false);
        }

        return redirect()
            ->route('admin.module-library.edit', $module)
            ->with('success', $message);
    }

    public function duplicate(Request $request, ModuleLibraryItem $module): RedirectResponse
    {
        $data = $request->validate([
            'version_no' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('module_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $module->module_no)),
            ],
            'version_name' => ['required', 'string', 'max:189'],
            'version_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('module_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $module->module_no)),
            ],
            // Internal codes are generated when the form leaves them out
            // (DataSensei Updates 9); older forms that send them still work.
            'module_code' => ['nullable', 'string', 'max:189', 'unique:module_library_items,module_code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (trim((string) ($data['version_code'] ?? '')) === '') {
            $data['version_code'] = 'V'.(int) $data['version_no'];
        }
        if (trim((string) ($data['module_code'] ?? '')) === '') {
            $data['module_code'] = $this->generatedModuleCode((string) $module->title, (int) $data['version_no']);
        }

        if (filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL)) {
            $this->editorContent->assertPublishable($module->learning_outcomes);
        }

        $copy = DB::transaction(function () use ($module, $data): ModuleLibraryItem {
            $source = ModuleLibraryItem::query()
                ->whereKey($module->id)
                ->lockForUpdate()
                ->firstOrFail();

            return ModuleLibraryItem::create([
                'module_no' => $source->module_no,
                'module_code' => strtoupper(trim($data['module_code'])),
                'title' => $source->title,
                'year_level' => $source->year_level,
                'version_no' => (int) $data['version_no'],
                'version_name' => $data['version_name'],
                'version_code' => strtoupper(trim($data['version_code'])),
                'description' => $source->description,
                'estimated_minutes' => $source->estimated_minutes,
                'content_sections' => $source->content_sections,
                'mcq_questions' => $source->mcq_questions,
                'learning_outcomes' => $source->learning_outcomes,
                'sort_order' => $source->sort_order,
                'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
            ]);
        }, 3);

        return redirect()
            ->route('admin.module-library.edit', $copy)
            ->with('success', "Module duplicated as version {$data['version_no']}. Review the copied content before publishing.");
    }

    public function toggleStatus(ModuleLibraryItem $module): RedirectResponse
    {
        $isActive = DB::transaction(function () use ($module): ?bool {
            $lockedModule = ModuleLibraryItem::query()->whereKey($module->id)->lockForUpdate()->firstOrFail();

            // Publishing needs at least one learning outcome.
            if (! $lockedModule->is_active && $lockedModule->learning_outcomes === []) {
                return null;
            }

            $lockedModule->update(['is_active' => ! $lockedModule->is_active]);

            return (bool) $lockedModule->is_active;
        }, 3);

        if ($isActive === null) {
            return back()->with('error', '"'.$module->title.'" has no learning outcomes. Add at least one under "What Students Will Learn" before publishing it.');
        }

        return back()->with('success', $isActive
            ? 'Module version published.'
            : 'Module version unpublished.');
    }

    public function destroy(ModuleLibraryItem $module): RedirectResponse
    {
        $deleted = DB::transaction(function () use ($module): bool {
            $lockedModule = ModuleLibraryItem::query()->whereKey($module->id)->lockForUpdate()->firstOrFail();

            if ($this->contentService->moduleHasReferences($lockedModule)) {
                return false;
            }

            $lockedModule->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'This module version is assigned to one or more classes. Deactivate it instead of deleting it.');
        }

        return redirect()
            ->route('admin.module-library.index')
            ->with('success', 'Module version deleted successfully.');
    }

    private function validatedData(Request $request, ?ModuleLibraryItem $module = null): array
    {
        $moduleId = $module?->id;

        $data = $request->validate([
            'module_no' => ['required', 'integer', 'min:1', 'max:9999'],
            // Internal codes (DataSensei Updates 9): the form no longer shows
            // them; a missing one is generated below.
            'module_code' => [
                'nullable',
                'string',
                'max:189',
                Rule::unique('module_library_items', 'module_code')->ignore($moduleId),
            ],
            'title' => ['required', 'string', 'max:189'],
            // Class modules are not grouped by year; kept for reference only.
            'year_level' => ['nullable', 'string', 'max:50'],
            'version_no' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
                Rule::unique('module_library_items', 'version_no')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($moduleId),
            ],
            'version_name' => ['required', 'string', 'max:189'],
            // Generated below when the form leaves it out (Updates 9).
            'version_code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('module_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $request->integer('module_no')))
                    ->ignore($moduleId),
            ],
            'description' => ['nullable', 'string', 'max:10000'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:100000'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
            'intent' => ['nullable', Rule::in(['draft', 'save', 'publish', 'unpublish'])],
            'content_sections_json' => ['nullable', 'string', 'max:5000000'],
            'mcq_questions_json' => ['nullable', 'string', 'max:5000000'],
            'learning_outcomes' => ['nullable', 'array', 'max:'.ModuleEditorContent::MAX_OUTCOMES],
            'learning_outcomes.*' => ['nullable', 'string', 'max:'.ModuleEditorContent::MAX_OUTCOME_LENGTH],
        ]);

        if (trim((string) ($data['version_code'] ?? '')) === '') {
            $data['version_code'] = $module && (int) $module->module_no === (int) $data['module_no'] && (int) $module->version_no === (int) $data['version_no']
                ? (string) $module->version_code
                : 'V'.(int) $data['version_no'];
        }

        if (trim((string) ($data['module_code'] ?? '')) === '') {
            $data['module_code'] = $module?->module_code ?: $this->generatedModuleCode((string) $data['title'], (int) $data['version_no']);
        }

        return $data;
    }

    /**
     * The sections and review questions from the editor. A field the request
     * does not carry keeps what the version already has (the editor only
     * sends its content once it has loaded it).
     *
     * @return array{0: list<mixed>, 1: list<mixed>}
     */
    private function editorContent(Request $request, array $data, ?ModuleLibraryItem $module = null): array
    {
        $sections = $request->has('content_sections_json')
            ? $this->resolveSections(
                $this->editorContent->decodeList($data['content_sections_json'] ?? null, 'content_sections_json', 'learning content'),
                $module?->content_sections ?? []
            )
            : ($module?->content_sections ?? []);
        $questions = $request->has('mcq_questions_json')
            ? $this->editorContent->decodeList($data['mcq_questions_json'] ?? null, 'mcq_questions_json', 'review questions')
            : ($module?->mcq_questions ?? []);

        if ($request->has('content_sections_json')) {
            $this->editorContent->assertSections($sections, 'content_sections_json');
        }
        if ($request->has('mcq_questions_json')) {
            $this->editorContent->assertQuestions($questions, 'mcq_questions_json');
        }

        return [$sections, $questions];
    }

    /**
     * @param  list<mixed>  $sections
     * @param  list<mixed>  $questions
     * @param  list<string>  $outcomes
     */
    private function previewView(ModuleLibraryItem $module, array $sections, array $questions, array $outcomes, bool $sectionOnly = false): View
    {
        return view('student.modules.module_show', [
            'module' => $module,
            'contentSections' => $sections,
            'mcqQuestions' => $questions,
            'relatedVersions' => collect(),
            'learningOutcomes' => $outcomes,
            'backRoute' => $module->exists ? route('admin.module-library.edit', $module) : route('admin.module-library.index'),
            'versionClassId' => null,
            'sectionOnly' => $sectionOnly,
        ]);
    }

    /**
     * The sections as the module builder shows them: the title, the content
     * blocks (converted from the stored fields) and where the section came
     * from, so an untouched section can be kept exactly as stored.
     *
     * @param  list<mixed>  $sections
     * @return list<array{source: int, title: string, blocks: list<array<string, mixed>>}>
     */
    private function builderSections(array $sections): array
    {
        $out = [];

        foreach (array_values($sections) as $index => $section) {
            $section = is_array($section) ? $section : [];
            $out[] = [
                'source' => $index,
                'title' => (string) ($section['heading'] ?? ''),
                'blocks' => $this->converter->fromLibrarySection($section),
            ];
        }

        return $out;
    }

    /**
     * What the editor posted, as stored sections. A builder section
     * ({source, title, blocks}) whose title and blocks still equal the stored
     * section it came from is kept exactly as stored, keys and all; an edited
     * one keeps its other keys (lesson_no, ilo_codes, ...) and stores its
     * content as blocks. A plain section object is taken as it is.
     *
     * @param  list<mixed>  $posted
     * @param  list<mixed>  $stored
     * @return list<mixed>
     */
    private function resolveSections(array $posted, array $stored, bool $validate = true): array
    {
        $stored = array_values($stored);
        $out = [];

        foreach ($posted as $index => $item) {
            if (! is_array($item) || ! array_key_exists('blocks', $item) || array_key_exists('heading', $item) && ! array_key_exists('title', $item)) {
                // A whole section object: kept as sent, its blocks (if any) cleaned.
                if (is_array($item) && isset($item['blocks']) && is_array($item['blocks'])) {
                    $item['blocks'] = $this->renderer()->normalize(array_values($item['blocks']));
                }
                $out[] = $item;

                continue;
            }

            if (! is_array($item['blocks']) || count($item['blocks']) > 300) {
                throw ValidationException::withMessages([
                    'content_sections_json' => 'Section '.($index + 1).' can hold at most 300 content blocks.',
                ]);
            }

            $title = trim(preg_replace('/\s+/', ' ', (string) ($item['title'] ?? '')) ?? '');
            $blocks = $this->renderer()->normalize(array_values($item['blocks']));
            $source = isset($item['source']) && is_numeric($item['source']) ? (int) $item['source'] : null;
            $original = $source !== null && isset($stored[$source]) && is_array($stored[$source]) ? $stored[$source] : null;

            if ($original !== null
                && trim(preg_replace('/\s+/', ' ', (string) ($original['heading'] ?? '')) ?? '') === $title
                && $this->converter->fromLibrarySection($original) === $blocks) {
                $out[] = $original;

                continue;
            }

            if ($validate) {
                $sectionBlocks = array_fill(0, $index, []);
                $sectionBlocks[] = $blocks;
                $this->editorContent->assertKnowledgeChecks($sectionBlocks, 'content_sections_json');
            }

            $out[] = $this->converter->librarySectionFromBlocks($original ?? [], $title, $blocks);
        }

        return $out;
    }

    private function renderer(): \App\Services\LessonBlockRenderer
    {
        return app(\App\Services\LessonBlockRenderer::class);
    }


    private function assertReferencedModuleIdentityUnchanged(ModuleLibraryItem $module, array $data): void
    {
        $changes = [];

        if ((int) $module->module_no !== (int) $data['module_no']) {
            $changes['module_no'] = 'Module number cannot be changed after this version has been assigned to a class.';
        }
        if (strtoupper((string) $module->module_code) !== strtoupper(trim($data['module_code']))) {
            $changes['module_code'] = 'Module code cannot be changed after this version has been assigned to a class.';
        }
        if ((int) $module->version_no !== (int) $data['version_no']) {
            $changes['version_no'] = 'Version number cannot be changed after this module version has been assigned to a class.';
        }
        if (strtoupper((string) $module->version_code) !== strtoupper(trim($data['version_code']))) {
            $changes['version_code'] = 'Version code cannot be changed after this module version has been assigned to a class.';
        }

        if ($changes !== []) {
            throw ValidationException::withMessages($changes);
        }
    }

    /**
     * The editor's answer to a background save: the outcome, whether the
     * version is published, the stored values of the details fields (codes
     * are saved in capitals), and each section's stored position, so the
     * editor keeps matching its sections to what is stored.
     */
    private function savedResponse(ModuleLibraryItem $module, string $message, bool $created): JsonResponse
    {
        $module->refresh();
        $sections = array_values((array) $module->content_sections);

        return response()->json([
            'message' => $message,
            'created' => $created,
            'is_published' => (bool) $module->is_active,
            'sections' => array_map(fn (int $index) => ['source' => $index], array_keys($sections)),
            'question_count' => count((array) $module->mcq_questions),
            'fields' => [
                'title' => (string) $module->title,
                'module_code' => (string) $module->module_code,
                'version_code' => (string) $module->version_code,
                'version_name' => (string) $module->version_name,
                'year_level' => (string) $module->year_level,
            ],
            'module_id' => $module->id,
            'edit_url' => route('admin.module-library.edit', $module),
            'update_url' => route('admin.module-library.update', $module),
            'cancel_url' => route('admin.module-library.show', $module),
            'page_title' => 'Edit Library Module',
        ], $created ? 201 : 200);
    }

    private function modulePayload(array $data, array $sections, array $questions, array $outcomes, bool $publish): array
    {
        return [
            'module_no' => (int) $data['module_no'],
            'module_code' => strtoupper(trim($data['module_code'])),
            'title' => trim($data['title']),
            'year_level' => trim((string) ($data['year_level'] ?? '')),
            'version_no' => (int) $data['version_no'],
            'version_name' => trim($data['version_name']),
            'version_code' => strtoupper(trim($data['version_code'])),
            'description' => $data['description'] ?? null,
            'estimated_minutes' => (int) $data['estimated_minutes'],
            'content_sections' => $sections,
            'mcq_questions' => $questions,
            'learning_outcomes' => $outcomes,
            'sort_order' => (int) $data['sort_order'],
            'is_active' => $publish,
        ];
    }

    /** A unique internal code for a new module version, from its title. */
    private function generatedModuleCode(string $title, int $versionNo): string
    {
        $base = strtoupper(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $title), '-')) ?: 'MODULE';
        $base = substr($base, 0, 150);
        $code = $base.'-V'.$versionNo;
        $suffix = 2;
        while (ModuleLibraryItem::query()->where('module_code', $code)->exists()) {
            $code = $base.'-V'.$versionNo.'-'.$suffix++;
        }

        return $code;
    }
}
