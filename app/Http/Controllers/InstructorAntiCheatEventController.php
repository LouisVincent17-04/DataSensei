<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAntiCheatEventController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassRoom::where('instructor_id', Auth::id())->orderBy('name')->get();
        $classIds = $classes->pluck('id');

        $query = AntiCheatEvent::with(['user', 'classRoom', 'classAssignment'])
            ->whereIn('class_id', $classIds)
            ->latest('occurred_at')
            ->latest();

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->integer('class_id'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        $events = $query->paginate(20)->withQueryString();

        return view('instructor.anti-cheat.events', compact('classes', 'events'));
    }
}
