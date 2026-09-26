<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Services\LessonBlockRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Module Content Manager: the lessons inside one public module, written with
 * the block editor and previewed live exactly as the learning room shows them.
 *
 * Lessons store both the editor's blocks (lessons.blocks, JSON) and the HTML
 * the renderer produced from them (lessons.content), so the learning room
 * keeps reading lessons.content unchanged. A lesson written before the
 * editor existed has no blocks; the editor opens it as one "html" block and
 * saving it untouched leaves its content byte for byte the same.
 */
class AdminLessonController extends Controller
{
    public const MAX_BLOCKS = 200;

    public function __construct(private readonly LessonBlockRenderer $renderer)
    {
    }

    public function index(Module $module)
    {
        $lessons = $module->lessons()->get();
        $progressCounts = DB::table('lesson_user')
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->select('lesson_id', DB::raw('COUNT(*) as total'))
            ->groupBy('lesson_id')
            ->pluck('total', 'lesson_id');

        $rows = $lessons->map(function (Lesson $lesson) use ($progressCounts): array {
            return [
                'lesson' => $lesson,
                'legacy' => $lesson->usesLegacyHtml(),
                'blockCount' => count($lesson->editorBlocks()),
                'progress' => (int) ($progressCounts[$lesson->id] ?? 0),
            ];
        });

        return view('admin.modules.lessons.index', [
            'module' => $module,
            'rows' => $rows,
        ]);
    }

    public function create(Module $module)
    {
        $lesson = new Lesson(['module_id' => $module->id, 'title' => '']);

        return view('admin.modules.lessons.edit', [
            'module' => $module,
            'lesson' => $lesson,
            'blocks' => [],
            'isLegacy' => false,
            'formAction' => route('admin.modules.lessons.store', $module),
            'formMethod' => 'POST',
        ]);
    }

    public function store(Request $request, Module $module): RedirectResponse
    {
        [$title, $blocks] = $this->validated($request);

        $lesson = new Lesson([
            'module_id' => $module->id,
            'title' => $title,
            'order_index' => ((int) $module->lessons()->max('order_index')) + 1,
        ]);
        $this->applyBlocks($lesson, $blocks);
        $lesson->save();

        return redirect()->route('admin.modules.lessons.index', $module)->with('success', 'Lesson "'.$lesson->title.'" added.');
    }

    public function reorder(Request $request, Module $module): RedirectResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'distinct', Rule::exists('lessons', 'id')->where('module_id', $module->id)],
        ]);

        DB::transaction(function () use ($data, $module): void {
            $position = 1;
            foreach ($data['order'] as $id) {
                Lesson::whereKey((int) $id)->where('module_id', $module->id)->update(['order_index' => $position++]);
            }

            $rest = Lesson::where('module_id', $module->id)
                ->whereNotIn('id', $data['order'])
                ->orderBy('order_index')
                ->orderBy('id')
                ->pluck('id');
            foreach ($rest as $id) {
                Lesson::whereKey($id)->update(['order_index' => $position++]);
            }
        });

        return redirect()->route('admin.modules.lessons.index', $module)->with('success', 'Lesson order updated.');
    }

    public function edit(Module $module, Lesson $lesson)
    {
        $this->ensureBelongs($module, $lesson);

        return view('admin.modules.lessons.edit', [
            'module' => $module,
            'lesson' => $lesson,
            'blocks' => $lesson->editorBlocks(),
            'isLegacy' => $lesson->usesLegacyHtml(),
            'formAction' => route('admin.modules.lessons.update', [$module, $lesson]),
            'formMethod' => 'PUT',
        ]);
    }

    public function update(Request $request, Module $module, Lesson $lesson): RedirectResponse
    {
        $this->ensureBelongs($module, $lesson);

        [$title, $blocks] = $this->validated($request);

        $lesson->title = $title;
        $this->applyBlocks($lesson, $blocks);
        $lesson->save();

        return redirect()->route('admin.modules.lessons.index', $module)->with('success', 'Lesson "'.$lesson->title.'" saved.');
    }

    public function destroy(Module $module, Lesson $lesson): RedirectResponse
    {
        $this->ensureBelongs($module, $lesson);

        if (DB::table('lesson_user')->where('lesson_id', $lesson->id)->exists()) {
            return redirect()->route('admin.modules.lessons.index', $module)
                ->with('error', 'Cannot delete "'.$lesson->title.'": students already have progress in it.');
        }

        $lesson->delete();

        return redirect()->route('admin.modules.lessons.index', $module)->with('success', 'Lesson deleted.');
    }

    /**
     * The editor's live preview: the same renderer and the same lesson-body
     * styles as the learning room, so what the admin sees is what students get.
     */
    public function preview(Request $request): Response
    {
        $raw = $request->input('blocks_json', $request->input('blocks'));
        $blocks = $this->renderer->normalize($raw);
        $blocks = array_slice($blocks, 0, self::MAX_BLOCKS);

        $html = view('admin.modules.lessons._preview_frame', [
            'html' => $this->renderer->render($blocks),
        ])->render();

        return response($html, 200)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'module_id' => ['nullable', 'integer'],
        ]);

        $file = $request->file('image');
        $moduleId = (int) $request->input('module_id', 0);
        $folder = $moduleId > 0 && Module::whereKey($moduleId)->exists() ? (string) $moduleId : 'shared';

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        if (! in_array($extension, ['jpg', 'png', 'webp', 'gif'], true)) {
            $extension = 'png';
        }

        $directory = public_path('uploads/lessons/'.$folder);
        File::ensureDirectoryExists($directory);

        $name = Str::lower(Str::random(24)).'.'.$extension;
        $file->move($directory, $name);

        return response()->json(['url' => '/uploads/lessons/'.$folder.'/'.$name]);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /**
     * @return array{0: string, 1: list<array<string, mixed>>}
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:189'],
            'blocks_json' => ['required', 'string', 'json'],
        ]);

        $decoded = json_decode($data['blocks_json'], true);

        if (! is_array($decoded)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'blocks_json' => 'The lesson blocks could not be read. Reload the editor and try again.',
            ]);
        }

        if (count($decoded) > self::MAX_BLOCKS) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'blocks_json' => 'A lesson can hold at most '.self::MAX_BLOCKS.' blocks.',
            ]);
        }

        return [trim($data['title']), $this->renderer->normalize($decoded)];
    }

    /** @param  list<array<string, mixed>>  $blocks */
    private function applyBlocks(Lesson $lesson, array $blocks): void
    {
        $lesson->blocks = json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $lesson->content = $this->renderer->render($blocks);
    }

    private function ensureBelongs(Module $module, Lesson $lesson): void
    {
        abort_unless((int) $lesson->module_id === (int) $module->id, 404);
    }
}
