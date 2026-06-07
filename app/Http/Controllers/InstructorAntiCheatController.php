<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use App\Models\AntiCheatSetting;
use App\Models\ClassRoom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstructorAntiCheatController extends Controller
{
    public function index(Request $request)
    {
        $instructorId = Auth::id();

        $classes = ClassRoom::forInstructor($instructorId)
            ->active()
            ->orderBy('name')
            ->get();

        $settings = AntiCheatSetting::with('classRoom')
            ->where('instructor_id', $instructorId)
            ->where('assessment_type', 'assignment')
            ->orderByRaw('class_id IS NOT NULL')
            ->orderBy('class_id')
            ->get();

        $classIds = $classes->pluck('id');

        $recentEvents = AntiCheatEvent::with(['user', 'classRoom', 'classAssignment', 'assignmentSubmission', 'assignmentQuestion'])
            ->where('assessment_type', 'assignment')
            ->when($classIds->isNotEmpty(), fn ($query) => $query->whereIn('class_id', $classIds))
            ->latest()
            ->limit(40)
            ->get();

        $stats = [
            'settings' => $settings->count(),
            'events_today' => AntiCheatEvent::where('assessment_type', 'assignment')
                ->when($classIds->isNotEmpty(), fn ($query) => $query->whereIn('class_id', $classIds))
                ->whereDate('created_at', today())
                ->count(),
            'critical_today' => AntiCheatEvent::where('assessment_type', 'assignment')
                ->when($classIds->isNotEmpty(), fn ($query) => $query->whereIn('class_id', $classIds))
                ->where('severity', 'critical')
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('instructor.anti-cheat.index', compact('classes', 'settings', 'recentEvents', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['instructor_id'] = Auth::id();
        $data['class_id'] = $data['class_id'] ?: null;
        $data['assessment_type'] = 'assignment';

        AntiCheatSetting::updateOrCreate(
            [
                'instructor_id'   => $data['instructor_id'],
                'class_id'        => $data['class_id'],
                'assessment_type' => 'assignment',
            ],
            $data
        );

        return back()->with('success', 'Assignment anti-cheat configuration saved.');
    }

    public function update(Request $request, AntiCheatSetting $setting): RedirectResponse
    {
        abort_unless((int) $setting->instructor_id === (int) Auth::id(), 403);

        $data = $this->validated($request);
        $data['class_id'] = $data['class_id'] ?: null;
        $data['assessment_type'] = 'assignment';

        $setting->update($data);

        return back()->with('success', 'Assignment anti-cheat configuration updated.');
    }

    public function destroy(AntiCheatSetting $setting): RedirectResponse
    {
        abort_unless((int) $setting->instructor_id === (int) Auth::id(), 403);
        $setting->delete();

        return back()->with('success', 'Assignment anti-cheat configuration removed.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'class_id'         => 'nullable|integer|exists:classes,id',
            'max_tab_switches' => 'required|integer|min:0|max:20',
        ]);

        foreach ([
            'enabled',
            'allow_tab_switch',
            'block_on_tab_limit',
            'require_fullscreen',
            'detect_dual_monitor',
            'block_dual_monitor',
            'allow_copy',
            'allow_paste',
            'block_external_paste',
            'allow_right_click',
            'allow_devtools_shortcuts',
            'show_warnings',
            'auto_submit_mcq_on_violation',
            'lock_screen_on_violation',
        ] as $key) {
            $data[$key] = $request->boolean($key);
        }

        return $data;
    }
}
