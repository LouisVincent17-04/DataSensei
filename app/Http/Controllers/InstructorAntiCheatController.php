<?php

namespace App\Http\Controllers;

use App\Models\AntiCheatEvent;
use App\Models\AntiCheatSetting;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InstructorAntiCheatController extends Controller
{
    public function index(Request $request)
    {
        $instructorId = Auth::id();

        $classes = ClassRoom::forInstructor($instructorId)
            ->active()
            ->orderBy('name')
            ->get();

        $classIds = $classes->pluck('id');

        $settings = AntiCheatSetting::with('classRoom')
            ->where('instructor_id', $instructorId)
            ->where('assessment_type', 'assignment')
            ->orderByRaw('class_id IS NOT NULL')
            ->orderBy('class_id')
            ->get();

        // Only a configuration this instructor already owns can be loaded back
        // into the form, because $settings is scoped to the signed-in user.
        $editingSetting = $request->filled('setting')
            ? $settings->firstWhere('id', (int) $request->integer('setting'))
            : null;

        // A configuration written for a class that was archived afterwards must
        // keep that class in the picker, or saving the edit would quietly move
        // the rules to the all-classes default.
        if ($editingSetting?->classRoom
            && (int) $editingSetting->classRoom->instructor_id === (int) $instructorId
            && ! $classes->contains('id', $editingSetting->class_id)) {
            $classes = $classes->push($editingSetting->classRoom)->sortBy('name')->values();
        }

        $recentEvents = AntiCheatEvent::with(['user', 'classRoom', 'classAssignment', 'assignmentSubmission', 'assignmentQuestion'])
            ->where('assessment_type', 'assignment')
            ->whereIn('class_id', $classIds)
            ->latest()
            ->limit(40)
            ->get();

        $stats = [
            'settings' => $settings->count(),
            'events_today' => AntiCheatEvent::where('assessment_type', 'assignment')
                ->whereIn('class_id', $classIds)
                ->whereDate('created_at', today())
                ->count(),
            'critical_today' => AntiCheatEvent::where('assessment_type', 'assignment')
                ->whereIn('class_id', $classIds)
                ->where('severity', 'critical')
                ->whereDate('created_at', today())
                ->count(),
        ];

        return view('instructor.anti-cheat.index', compact('classes', 'settings', 'recentEvents', 'stats', 'editingSetting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['instructor_id'] = Auth::id();
        $data['class_id'] = $data['class_id'] ?: null;
        $data['assessment_type'] = 'assignment';

        DB::transaction(function () use ($data): void {
            User::query()->whereKey($data['instructor_id'])->lockForUpdate()->firstOrFail();

            $query = AntiCheatSetting::query()
                ->where('instructor_id', $data['instructor_id'])
                ->where('assessment_type', 'assignment');

            is_null($data['class_id'])
                ? $query->whereNull('class_id')
                : $query->where('class_id', $data['class_id']);

            $setting = $query->lockForUpdate()->first();

            if ($setting) {
                $setting->update($data);
            } else {
                AntiCheatSetting::create($data);
            }
        }, 3);

        return back()->with('success', 'Assignment anti-cheat configuration saved.');
    }

    public function update(Request $request, AntiCheatSetting $setting): RedirectResponse
    {
        abort_unless((int) $setting->instructor_id === (int) Auth::id(), 403);

        $data = $this->validated($request);
        $data['class_id'] = $data['class_id'] ?: null;
        $data['assessment_type'] = 'assignment';

        DB::transaction(function () use ($setting, $data): void {
            User::query()->whereKey($setting->instructor_id)->lockForUpdate()->firstOrFail();
            $lockedSetting = AntiCheatSetting::query()->whereKey($setting->id)->lockForUpdate()->firstOrFail();
            abort_unless((int) $lockedSetting->instructor_id === (int) Auth::id(), 403);

            $conflict = AntiCheatSetting::query()
                ->where('instructor_id', $lockedSetting->instructor_id)
                ->where('assessment_type', 'assignment')
                ->where('id', '<>', $lockedSetting->id)
                ->when(
                    is_null($data['class_id']),
                    fn ($query) => $query->whereNull('class_id'),
                    fn ($query) => $query->where('class_id', $data['class_id']),
                )
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'class_id' => 'A configuration already exists for that class scope. Edit the existing configuration instead.',
                ]);
            }

            $lockedSetting->update($data);
        }, 3);

        return back()->with('success', 'Assignment anti-cheat configuration updated.');
    }

    public function destroy(AntiCheatSetting $setting): RedirectResponse
    {
        abort_unless((int) $setting->instructor_id === (int) Auth::id(), 403);

        DB::transaction(function () use ($setting): void {
            User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $lockedSetting = AntiCheatSetting::query()
                ->whereKey($setting->id)
                ->lockForUpdate()
                ->firstOrFail();
            abort_unless((int) $lockedSetting->instructor_id === (int) Auth::id(), 403);
            $lockedSetting->delete();
        }, 3);

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

        if (! empty($data['class_id'])) {
            $ownsClass = ClassRoom::query()
                ->whereKey($data['class_id'])
                ->where('instructor_id', Auth::id())
                ->exists();

            abort_unless($ownsClass, 403, 'You can only configure anti-cheat settings for your own classes.');
        }

        return $data;
    }
}
