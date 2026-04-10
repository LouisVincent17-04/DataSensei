<?php

namespace App\Http\Controllers;

use App\Models\Module; // <-- CRITICAL: You must include this line at the top!
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function showModules()
    {
        // 1. Fetch all modules and eagerly load their lessons from the database
        $modules = Module::with('lessons')->orderBy('order_index', 'asc')->get();

        // 2. Pass the $modules variable into your view
        return view('student.modules', compact('modules')); 
        
        // Note: If your view is inside a folder (e.g., resources/views/student/modules.blade.php), 
        // change it to return view('student.modules', compact('modules'));
    }
}