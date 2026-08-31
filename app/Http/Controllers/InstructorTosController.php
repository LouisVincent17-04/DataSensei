<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Module;
use App\Models\TableOfSpecification;
use App\Models\TableOfSpecificationRow;
use App\Services\TableOfSpecificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstructorTosController extends Controller
{
    public function index(Request $request, TableOfSpecificationService $service)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $tosList = TableOfSpecification::with('classRoom')
            ->withCount(['rows', 'assessments'])
            ->withSum('rows as assigned_items', 'item_count')
            ->where(function ($q) use ($classes) {
                $q->whereIn('class_id', $classes->pluck('id'))
                    ->orWhere('created_by', Auth::id());
            })
            ->latest()
            ->paginate(10);

        foreach ($tosList as $tos) {
            $target = max(1, (int) ($tos->total_items ?: $tos->assigned_items));
            $tos->setAttribute('blueprint_status', $service->statusFromCounts((int) $tos->assigned_items, $target));
        }

        return view('instructor.tos.index', compact('tosList'));
    }

    public function create()
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())
            ->where('is_archived', false)
            ->orderBy('name')
            ->get();

        $modules = Module::query()
            ->whereBetween('order_index', [1, 24])
            ->orderBy('order_index')
            ->get(['order_index', 'title']);

        return view('instructor.tos.create', compact('classes', 'modules'));
    }

    public function store(Request $request, TableOfSpecificationService $service)
    {
        $coverageOptions = Module::query()
            ->whereBetween('order_index', [1, 24])
            ->pluck('order_index')
            ->map(fn ($moduleNo) => (string) $moduleNo)
            ->push('other')
            ->all();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'module_no' => ['required', Rule::in($coverageOptions)],
            'custom_coverage' => ['nullable', 'required_if:module_no,other', 'string', 'max:191'],
            'total_items' => ['required', 'integer', 'min:1', 'max:200'],
        ], [
            'module_no.in' => 'Select an available module or choose Others.',
            'custom_coverage.required_if' => 'Enter the module or coverage you want to use.',
        ]);

        if (! empty($validated['class_id'])) {
            ClassRoom::where('id', $validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->where('is_archived', false)
                ->firstOrFail();
        }

        $usesCustomCoverage = $validated['module_no'] === 'other';

        $tos = $service->createBlueprint(
            $validated['class_id'] ?? null,
            $usesCustomCoverage ? 0 : (int) $validated['module_no'],
            $validated['title'],
            (int) $validated['total_items'],
            $usesCustomCoverage ? trim($validated['custom_coverage']) : null
        );

        return redirect()
            ->route('instructor.tos.show', $tos)
            ->with('success', 'TOS created. Review the suggested distribution or customize it.');
    }

    public function show(TableOfSpecification $tos, TableOfSpecificationService $service)
    {
        $this->authorizeTos($tos);
        $tos->load(['classRoom', 'rows.ilo'])->loadCount('assessments');

        $module = Module::query()->where('order_index', $tos->module_no)->first();
        $isModernBlueprint = $service->isModernBlueprint($tos);
        $matrix = $isModernBlueprint ? $service->matrix($tos) : null;
        $status = $service->statusSummary($tos);
        $cognitiveLevels = TableOfSpecificationService::COGNITIVE_LEVELS;
        $suggestedDistribution = array_replace(
            TableOfSpecificationService::DEFAULT_COGNITIVE_DISTRIBUTION,
            $tos->cognitive_distribution ?? []
        );

        return view('instructor.tos.show', compact(
            'tos',
            'module',
            'isModernBlueprint',
            'matrix',
            'status',
            'cognitiveLevels',
            'suggestedDistribution'
        ));
    }

    public function applySuggestedDistribution(
        Request $request,
        TableOfSpecification $tos,
        TableOfSpecificationService $service
    ) {
        $this->authorizeTos($tos);

        $validated = $request->validate([
            'remember' => ['required', 'integer', 'min:0', 'max:100'],
            'understand' => ['required', 'integer', 'min:0', 'max:100'],
            'apply' => ['required', 'integer', 'min:0', 'max:100'],
            'analyze' => ['required', 'integer', 'min:0', 'max:100'],
            'weights' => ['nullable', 'array'],
            'weights.*' => ['nullable', 'numeric', 'min:0', 'max:1000'],
        ]);

        $weights = $validated['weights'] ?? [];
        unset($validated['weights']);

        $service->applySuggestedDistribution($tos, $validated, $weights);

        return redirect()
            ->route('instructor.tos.show', $tos)
            ->with('success', 'Suggested distribution applied. You can still edit any item count below.');
    }

    public function updateDistribution(
        Request $request,
        TableOfSpecification $tos,
        TableOfSpecificationService $service
    ) {
        $this->authorizeTos($tos);

        $validated = $request->validate([
            'counts' => ['required', 'array'],
            'counts.*' => ['required', 'integer', 'min:0', 'max:500'],
        ]);

        $service->updateDistribution($tos, $validated['counts']);

        if ($request->input('next') === 'review') {
            return redirect()
                ->route('instructor.tos.review', $tos)
                ->with('success', 'TOS distribution saved.');
        }

        return back()->with('success', 'TOS distribution saved.');
    }

    public function review(TableOfSpecification $tos, TableOfSpecificationService $service)
    {
        $this->authorizeTos($tos);
        $tos->load(['classRoom', 'rows.ilo'])->loadCount('assessments');

        $module = Module::query()->where('order_index', $tos->module_no)->first();
        $isModernBlueprint = $service->isModernBlueprint($tos);
        $matrix = $isModernBlueprint ? $service->matrix($tos) : null;
        $status = $service->statusSummary($tos);
        $explanation = $isModernBlueprint
            ? $service->explain($tos)
            : 'This TOS was created with DataSensei\'s earlier difficulty-based layout. Its existing allocations are preserved for compatibility.';
        $cognitiveLevels = TableOfSpecificationService::COGNITIVE_LEVELS;

        return view('instructor.tos.review', compact(
            'tos',
            'module',
            'isModernBlueprint',
            'matrix',
            'status',
            'explanation',
            'cognitiveLevels'
        ));
    }

    /**
     * Legacy row editor retained for TOS records created before the wizard redesign.
     */
    public function updateRow(
        Request $request,
        TableOfSpecification $tos,
        TableOfSpecificationRow $row,
        TableOfSpecificationService $service
    ) {
        $this->authorizeTos($tos);
        abort_unless($row->table_of_specification_id === $tos->id, 404);
        abort_if($tos->assessments()->exists(), 422, 'This TOS is linked to an assessment and can no longer be changed.');

        $validated = $request->validate([
            'topic_title' => ['required', 'string', 'max:189'],
            'subtopic_title' => ['nullable', 'string', 'max:191'],
            'learning_objective' => ['nullable', 'string', 'max:2000'],
            'item_count' => ['required', 'integer', 'min:0', 'max:200'],
            'default_points' => ['required', 'integer', 'min:1', 'max:1000'],
            'cognitive_level' => ['nullable', 'string', 'max:80'],
        ]);

        DB::transaction(function () use ($tos, $row, $validated, $service): void {
            $lockedTos = TableOfSpecification::query()
                ->whereKey($tos->id)
                ->lockForUpdate()
                ->firstOrFail();
            $this->authorizeTos($lockedTos);

            $lockedRow = TableOfSpecificationRow::query()
                ->whereKey($row->id)
                ->where('table_of_specification_id', $lockedTos->id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedRow->update($validated);

            $assigned = (int) $lockedTos->rows()->sum('item_count');
            $target = max(1, (int) ($lockedTos->total_items ?: $assigned));
            $lockedTos->update(['status' => $service->statusFromCounts($assigned, $target)['code']]);
        }, 3);

        return back()->with('success', 'TOS row updated.');
    }

    public function destroy(TableOfSpecification $tos)
    {
        $this->authorizeTos($tos);

        $deleted = DB::transaction(function () use ($tos): bool {
            $lockedTos = TableOfSpecification::query()
                ->whereKey($tos->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->authorizeTos($lockedTos);

            if ($lockedTos->assessments()->exists()) {
                return false;
            }

            $lockedTos->delete();

            return true;
        }, 3);

        if (! $deleted) {
            return back()->with('error', 'This TOS is linked to an assessment and cannot be deleted.');
        }

        return redirect()
            ->route('instructor.tos.index')
            ->with('success', 'Table of Specification deleted.');
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
}
