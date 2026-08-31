<?php

namespace App\Http\Controllers;

use App\Models\AssignmentLibraryItem;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ModuleLibraryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminContentController extends Controller
{
    public function index(): View
    {
        $categories = ChallengeCategory::query()
            ->withCount([
                'challenges as mcq_challenges_count' => fn ($query) => $query->where('is_coding_challenge', false),
            ])
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $summary = [
            'modules' => ModuleLibraryItem::count(),
            'active_modules' => ModuleLibraryItem::where('is_active', true)->count(),
            'mcq_challenges' => Challenge::mcq()->count(),
            'active_mcq_challenges' => Challenge::mcq()->active()->count(),
            'assessments' => AssignmentLibraryItem::count(),
            'active_assessments' => AssignmentLibraryItem::active()->count(),
        ];

        return view('admin.content.index', compact('categories', 'summary'));
    }

    public function updateCategory(Request $request, ChallengeCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:189'],
            'slug' => [
                'required',
                'string',
                'max:189',
                Rule::unique('challenge_categories', 'slug')->ignore($category->id),
            ],
            'target_audience' => ['required', 'string', 'max:189'],
            'description' => ['required', 'string', 'max:5000'],
            'order_index' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $category->update([
            'name' => trim($data['name']),
            'slug' => trim($data['slug']),
            'target_audience' => trim($data['target_audience']),
            'description' => trim($data['description']),
            'order_index' => (int) $data['order_index'],
        ]);

        return redirect()
            ->to(route('admin.content.index') . '#challenge-categories')
            ->with('success', 'Challenge category updated successfully.');
    }
}
