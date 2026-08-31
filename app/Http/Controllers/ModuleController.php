<?php

namespace App\Http\Controllers;

use App\Models\Module; 
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    public function showModules()
    {
        $user = Auth::user();
        $this->ensureFirstModuleUnlocked($user);

        $modules = Module::with('lessons')->orderBy('order_index', 'asc')->get();

        // Pull exact statuses from the database pivot table
        $unlockedModuleIds = $user->modules()
                                  ->wherePivot('is_unlocked', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        $completedModuleIds = $user->modules()
                                  ->wherePivot('is_completed', true)
                                  ->pluck('modules.id')
                                  ->toArray();

        return view('student.modules', compact('modules', 'unlockedModuleIds', 'completedModuleIds')); 
    }

    private function ensureFirstModuleUnlocked($user): void
    {
        if ($user->modules()->wherePivot('is_unlocked', true)->exists()) {
            return;
        }

        $firstModule = Module::orderBy('order_index')->orderBy('id')->first();
        if ($firstModule) {
            $user->modules()->syncWithoutDetaching([
                $firstModule->id => ['is_unlocked' => true],
            ]);
        }
    }
}
