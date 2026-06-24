<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\TableOfSpecification;
use App\Models\TableOfSpecificationRow;
use App\Services\TableOfSpecificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorTosController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();

        $tosList = TableOfSpecification::with(['classRoom', 'rows'])
            ->where(function ($q) use ($classes) {
                $q->whereIn('class_id', $classes->pluck('id'))
                  ->orWhere('created_by', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('instructor.tos.index', compact('classes', 'tosList'));
    }

    public function store(Request $request, TableOfSpecificationService $service)
    {
        $validated = $request->validate([
            'class_id' => ['nullable', 'integer', 'exists:classes,id'],
            'module_no' => ['required', 'integer', 'min:1', 'max:24'],
        ]);

        if (!empty($validated['class_id'])) {
            ClassRoom::where('id', $validated['class_id'])
                ->where('instructor_id', Auth::id())
                ->firstOrFail();
        }

        $tos = $service->createDefault($validated['class_id'] ?? null, (int) $validated['module_no']);

        return redirect()->route('instructor.tos.show', $tos)->with('success', 'Table of Specification generated.');
    }

    public function show(TableOfSpecification $tos)
    {
        $this->authorizeTos($tos);
        $tos->load(['classRoom', 'rows.ilo']);

        return view('instructor.tos.show', compact('tos'));
    }

    public function updateRow(Request $request, TableOfSpecification $tos, TableOfSpecificationRow $row)
    {
        $this->authorizeTos($tos);
        abort_unless($row->table_of_specification_id === $tos->id, 404);

        $validated = $request->validate([
            'item_count' => ['required', 'integer', 'min:0', 'max:200'],
            'cognitive_level' => ['nullable', 'string', 'max:80'],
        ]);

        $row->update($validated);

        return back()->with('success', 'TOS row updated.');
    }

    private function authorizeTos(TableOfSpecification $tos): void
    {
        if ($tos->class_id) {
            $owned = ClassRoom::where('id', $tos->class_id)->where('instructor_id', Auth::id())->exists();
            abort_unless($owned, 403);
        } else {
            abort_unless((int) $tos->created_by === (int) Auth::id(), 403);
        }
    }
}
