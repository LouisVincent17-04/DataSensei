<?php

namespace App\Http\Controllers;

use App\Models\Module; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function showModules()
    {
        $modules = Module::with('lessons')->orderBy('order_index', 'asc')->get();

        // Pull exact statuses from the database pivot table
        $unlockedModuleIds = Auth::user()->modules()
                                  ->wherePivot('is_unlocked', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        $completedModuleIds = Auth::user()->modules()
                                  ->wherePivot('is_completed', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        return view('student.modules', compact('modules', 'unlockedModuleIds', 'completedModuleIds')); 
    }
}