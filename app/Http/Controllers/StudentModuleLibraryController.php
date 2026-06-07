<?php

namespace App\Http\Controllers;

use App\Models\ModuleLibraryItem;

class StudentModuleLibraryController extends Controller
{
    public function index()
    {
        $modules = ModuleLibraryItem::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('module_no')
            ->orderBy('version_no')
            ->get()
            ->groupBy('module_no');

        return view('student.modules.index', compact('modules'));
    }

    public function show(ModuleLibraryItem $module)
    {
        abort_unless($module->is_active, 404);

        $contentSections = $this->decodeLongText($module->content_sections);
        $mcqQuestions = $this->decodeLongText($module->mcq_questions);

        $relatedVersions = ModuleLibraryItem::where('module_no', $module->module_no)
            ->where('is_active', true)
            ->orderBy('version_no')
            ->get();

        return view('student.modules.module_show', compact(
            'module',
            'contentSections',
            'mcqQuestions',
            'relatedVersions'
        ));
    }

    private function decodeLongText($value): array
    {
        if (blank($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }
}
