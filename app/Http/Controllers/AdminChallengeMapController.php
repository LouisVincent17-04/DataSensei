<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Challenge Maps: one page listing every challenge level (challenge_categories)
 * with its MCQ and coding challenges, where the level's text and order can be
 * edited inline and challenges reordered within their level and type.
 */
class AdminChallengeMapController extends Controller
{
    public function index(): View
    {
        $categories = ChallengeCategory::query()
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        $challenges = Challenge::query()
            ->with('module:id,title')
            ->withCount(['questions', 'codingQuestions'])
            ->orderBy('order_index')
            ->orderBy('version_no')
            ->orderBy('id')
            ->get()
            ->groupBy('challenge_category_id');

        $maps = $categories->map(function (ChallengeCategory $category) use ($challenges): array {
            $own = $challenges->get($category->id, collect());

            return [
                'category' => $category,
                'mcq' => $own->filter(fn (Challenge $challenge): bool => ! $challenge->is_coding_challenge)->values(),
                'coding' => $own->filter(fn (Challenge $challenge): bool => (bool) $challenge->is_coding_challenge)->values(),
            ];
        });

        return view('admin.challenge-maps.index', [
            'maps' => $maps,
        ]);
    }

    public function update(Request $request, ChallengeCategory $category): RedirectResponse
    {
        // The slug is deliberately not validated or written: it is part of the
        // student-facing URLs (/challenges/map/{slug}) and stays fixed.
        $data = $request->validate([
            'name' => ['required', 'string', 'max:189'],
            'description' => ['nullable', 'string', 'max:5000'],
            'target_audience' => ['nullable', 'string', 'max:189'],
            'order_index' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);

        $category->update([
            'name' => trim($data['name']),
            'description' => trim((string) ($data['description'] ?? '')),
            'target_audience' => trim((string) ($data['target_audience'] ?? '')),
            'order_index' => (int) $data['order_index'],
        ]);

        return redirect()
            ->to(route('admin.challenge-maps.index') . '#map-' . $category->id)
            ->with('success', 'Level "' . $category->name . '" updated.');
    }

    public function reorder(Request $request, ChallengeCategory $category): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['mcq', 'coding'])],
            'order' => ['required', 'array', 'min:1', 'max:1000'],
            'order.*' => ['required', 'integer', 'distinct'],
        ]);

        $isCoding = $data['type'] === 'coding';

        DB::transaction(function () use ($category, $data, $isCoding): void {
            $ids = collect($data['order'])->map(fn ($id): int => (int) $id)->values();

            // Only challenges of this level and this type may be touched; ids from
            // elsewhere are ignored rather than pulled into the level.
            $known = Challenge::query()
                ->where('challenge_category_id', $category->id)
                ->where('is_coding_challenge', $isCoding)
                ->whereIn('id', $ids->all())
                ->lockForUpdate()
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->flip();

            $position = 0;
            foreach ($ids as $id) {
                if (! $known->has($id)) {
                    continue;
                }

                $position++;
                Challenge::query()->whereKey($id)->update(['order_index' => $position]);
            }
        }, 3);

        return redirect()
            ->to(route('admin.challenge-maps.index') . '#map-' . $category->id)
            ->with('success', ($isCoding ? 'Coding' : 'MCQ') . ' challenge order saved for "' . $category->name . '".');
    }
}
