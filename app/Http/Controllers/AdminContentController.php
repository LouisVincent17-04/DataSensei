<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\ModuleLibraryItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminContentController extends Controller
{
    public function index(Request $request): View
    {
        $moduleSearch = trim((string) $request->input('module_search', ''));
        $challengeSearch = trim((string) $request->input('challenge_search', ''));

        $modules = ModuleLibraryItem::query()
            ->when($moduleSearch !== '', function ($query) use ($moduleSearch): void {
                $query->where(function ($q) use ($moduleSearch): void {
                    $q->where('title', 'like', '%' . $moduleSearch . '%')
                        ->orWhere('module_code', 'like', '%' . $moduleSearch . '%')
                        ->orWhere('version_code', 'like', '%' . $moduleSearch . '%');
                });
            })
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->paginate(10, ['*'], 'modules_page')
            ->withQueryString();

        $categories = ChallengeCategory::query()->orderBy('order_index')->orderBy('name')->get();

        $challenges = Challenge::query()
            ->with('category')
            ->when($challengeSearch !== '', function ($query) use ($challengeSearch): void {
                $query->where(function ($q) use ($challengeSearch): void {
                    $q->where('title', 'like', '%' . $challengeSearch . '%')
                        ->orWhere('description', 'like', '%' . $challengeSearch . '%');
                });
            })
            ->orderBy('challenge_category_id')
            ->orderBy('order_index')
            ->paginate(10, ['*'], 'challenges_page')
            ->withQueryString();

        return view('admin.content.index', compact('modules', 'categories', 'challenges'));
    }

    public function updateModule(Request $request, ModuleLibraryItem $module): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year_level' => ['nullable', 'string', 'max:50'],
            'version_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'estimated_minutes' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $module->update([
            'title' => $data['title'],
            'year_level' => $data['year_level'] ?? $module->year_level,
            'version_name' => $data['version_name'] ?? $module->version_name,
            'description' => $data['description'] ?? null,
            'estimated_minutes' => $data['estimated_minutes'] ?? 0,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Module library item updated.');
    }

    public function updateCategory(Request $request, ChallengeCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('challenge_categories', 'slug')->ignore($category->id)],
            'target_audience' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'order_index' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'target_audience' => $data['target_audience'] ?? null,
            'description' => $data['description'] ?? null,
            'order_index' => $data['order_index'] ?? 0,
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Challenge category updated.');
    }

    public function updateChallenge(Request $request, Challenge $challenge): RedirectResponse
    {
        $data = $request->validate([
            'challenge_category_id' => ['required', 'integer', 'exists:challenge_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:3000'],
            'time_limit_seconds' => ['required', 'integer', 'min:60', 'max:21600'],
            'base_xp' => ['required', 'integer', 'min:0', 'max:100000'],
            'order_index' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_coding_challenge' => ['nullable', 'boolean'],
        ]);

        $challenge->update([
            'challenge_category_id' => (int) $data['challenge_category_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'time_limit_seconds' => (int) $data['time_limit_seconds'],
            'base_xp' => (int) $data['base_xp'],
            'order_index' => $data['order_index'] ?? 0,
            'is_coding_challenge' => $request->boolean('is_coding_challenge'),
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Challenge updated.');
    }
}
