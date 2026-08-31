<?php

namespace App\Http\Controllers;

use App\Models\ModuleLibraryItem;
use App\Services\PlatformContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminModuleContentController extends Controller
{
    public function __construct(private readonly PlatformContentService $contentService)
    {
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
            'contentSectionsJson' => "[]",
            'mcqQuestionsJson' => "[]",
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $sections = $this->contentService->decodeJsonArray($data['content_sections_json'] ?? null, 'content_sections_json');
        $questions = $this->contentService->decodeJsonArray($data['mcq_questions_json'] ?? null, 'mcq_questions_json');

        $module = DB::transaction(function () use ($data, $sections, $questions): ModuleLibraryItem {
            return ModuleLibraryItem::create($this->modulePayload($data, $sections, $questions));
        });

        return redirect()
            ->route('admin.module-library.show', $module)
            ->with('success', 'Learning module version created successfully.');
    }

    public function show(ModuleLibraryItem $module): View
    {
        $module->loadCount('classAssignments');

        return view('admin.module-library.show', [
            'module' => $module,
            'contentSectionsJson' => $this->contentService->prettyJson($module->content_sections),
            'mcqQuestionsJson' => $this->contentService->prettyJson($module->mcq_questions),
            'hasReferences' => $this->contentService->moduleHasReferences($module),
            'nextVersionNo' => $this->contentService->nextModuleVersion((int) $module->module_no),
        ]);
    }

    public function edit(ModuleLibraryItem $module): View
    {
        return view('admin.module-library.edit', [
            'module' => $module,
            'contentSectionsJson' => $this->contentService->prettyJson($module->content_sections),
            'mcqQuestionsJson' => $this->contentService->prettyJson($module->mcq_questions),
            'hasReferences' => $this->contentService->moduleHasReferences($module),
        ]);
    }

    public function update(Request $request, ModuleLibraryItem $module): RedirectResponse
    {
        $data = $this->validatedData($request, $module);
        $sections = $this->contentService->decodeJsonArray($data['content_sections_json'] ?? null, 'content_sections_json');
        $questions = $this->contentService->decodeJsonArray($data['mcq_questions_json'] ?? null, 'mcq_questions_json');

        DB::transaction(function () use ($module, $data, $sections, $questions): void {
            $lockedModule = ModuleLibraryItem::query()->whereKey($module->id)->lockForUpdate()->firstOrFail();

            if ($this->contentService->moduleHasReferences($lockedModule)) {
                $this->assertReferencedModuleIdentityUnchanged($lockedModule, $data);

                if ($this->contentService->moduleContentChanged($lockedModule, $sections, $questions)) {
                    throw ValidationException::withMessages([
                        'content_sections_json' => 'This module version is already assigned to a class. Duplicate it as a new version before changing its learning content.',
                    ]);
                }
            }

            $lockedModule->update($this->modulePayload($data, $sections, $questions));
        }, 3);

        return redirect()
            ->route('admin.module-library.show', $module)
            ->with('success', 'Learning module version updated successfully.');
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
                'required',
                'string',
                'max:100',
                Rule::unique('module_library_items', 'version_code')
                    ->where(fn ($query) => $query->where('module_no', $module->module_no)),
            ],
            'module_code' => ['required', 'string', 'max:189', 'unique:module_library_items,module_code'],
            'is_active' => ['nullable', 'boolean'],
        ]);

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
        $isActive = DB::transaction(function () use ($module): bool {
            $lockedModule = ModuleLibraryItem::query()->whereKey($module->id)->lockForUpdate()->firstOrFail();
            $lockedModule->update(['is_active' => ! $lockedModule->is_active]);

            return (bool) $lockedModule->is_active;
        }, 3);

        return back()->with('success', $isActive
            ? 'Module version published.'
            : 'Module version deactivated.');
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

        return $request->validate([
            'module_no' => ['required', 'integer', 'min:1', 'max:9999'],
            'module_code' => [
                'required',
                'string',
                'max:189',
                Rule::unique('module_library_items', 'module_code')->ignore($moduleId),
            ],
            'title' => ['required', 'string', 'max:189'],
            'year_level' => ['required', 'string', 'max:50'],
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
            'version_code' => [
                'required',
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
            'content_sections_json' => ['nullable', 'string', 'max:5000000'],
            'mcq_questions_json' => ['nullable', 'string', 'max:5000000'],
        ]);
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

    private function modulePayload(array $data, array $sections, array $questions): array
    {
        return [
            'module_no' => (int) $data['module_no'],
            'module_code' => strtoupper(trim($data['module_code'])),
            'title' => trim($data['title']),
            'year_level' => trim($data['year_level']),
            'version_no' => (int) $data['version_no'],
            'version_name' => trim($data['version_name']),
            'version_code' => strtoupper(trim($data['version_code'])),
            'description' => $data['description'] ?? null,
            'estimated_minutes' => (int) $data['estimated_minutes'],
            'content_sections' => $sections,
            'mcq_questions' => $questions,
            'sort_order' => (int) $data['sort_order'],
            'is_active' => filter_var($data['is_active'] ?? false, FILTER_VALIDATE_BOOL),
        ];
    }
}
