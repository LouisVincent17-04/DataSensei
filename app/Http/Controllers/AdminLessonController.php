<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Services\LessonBlockRenderer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Lesson content helpers for the DataSensei Modules editor.
 *
 * Lessons store their blocks (lessons.blocks, JSON) and the HTML the renderer
 * produced from them (lessons.content), so the learning room keeps reading
 * lessons.content unchanged. Since DataSensei Updates 5 the lessons are
 * written as section cards in the module editor (AdminPublicModuleController);
 * this controller keeps the rendered preview of one section, the image
 * upload, and sends the old lesson pages to the module editor.
 */
class AdminLessonController extends Controller
{
    public const MAX_BLOCKS = 200;

    public function __construct(private readonly LessonBlockRenderer $renderer)
    {
    }

    /**
     * The lessons of a public module are its sections in the module editor
     * (DataSensei Updates 5); the old lesson pages open that editor.
     */
    public function index(Module $module): RedirectResponse
    {
        return redirect()->to(route('admin.modules.edit', $module).'#content');
    }

    public function create(Module $module): RedirectResponse
    {
        return redirect()->to(route('admin.modules.edit', $module).'#content');
    }

    public function edit(Module $module, Lesson $lesson): RedirectResponse
    {
        abort_unless((int) $lesson->module_id === (int) $module->id, 404);

        return redirect()->to(route('admin.modules.edit', $module).'#content');
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
        $title = trim((string) $request->input('title', ''));

        // With a title this is one section of the module builder, shown as
        // the lesson will be (the title is its heading unless it has one).
        $html = view('admin.modules.lessons._preview_frame', [
            'html' => $title !== '' ? $this->renderer->renderLesson($title, $blocks) : $this->renderer->render($blocks),
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
}
