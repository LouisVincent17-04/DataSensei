<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Module Manager: the public curriculum students work through at /module.
 *
 * This is App\Models\Module (the 24 seeded modules and their lessons), not the
 * module library instructors assign to classes. Creating a module here also
 * fans out one locked MCQ challenge per challenge level, so every level's map
 * has a slot for it the moment the module exists; the challenges stay
 * unavailable until an admin adds questions and publishes them.
 */
class AdminPublicModuleController extends Controller
{
    public function index()
    {
        $modules = Module::withCount('lessons')
            ->orderBy('order_index')
            ->orderBy('id')
            ->get();

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
        return view('admin.modules.create', [
            'module' => new Module([
                'year_level' => Module::YEAR_LEVELS[0],
                'xp_reward' => 100,
                'is_boss' => false,
                'has_coding_exercises' => false,
            ]),
            'yearLevels' => Module::YEAR_LEVELS,
            'levelCount' => ChallengeCategory::count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $created = 0;

        DB::transaction(function () use ($data, &$created): void {
            $data['order_index'] = ((int) Module::max('order_index')) + 1;

            $module = Module::create($data);

            $created = $this->fanOutChallenges($module);
        });

        $message = 'Module created. '.$created.' '.($created === 1 ? 'challenge was' : 'challenges were')
            .' created for it (one per level'.($data['has_coding_exercises'] ? ', plus one coding challenge per level' : '').').'
            .' They are unavailable to students until you add questions and publish them from Challenge Maps or Coding Challenges.';

        return redirect()->route('admin.modules.index')->with('success', $message);
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
        ]);
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $data = $this->validated($request);

        // Editing a module never touches the challenges fanned out for it.
        $module->update($data);

        return redirect()->route('admin.modules.index')->with('success', 'Module updated.');
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
