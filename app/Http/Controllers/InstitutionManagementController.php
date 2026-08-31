<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class InstitutionManagementController extends Controller
{
    /**
     * List all institutions with optional search / filter.
     */
    public function index(Request $request)
    {
        $query = Institution::withCount([
            'users',
            'classes',
            'instructorApplications',
            'users as student_count' => fn($q) => $q->where('role', User::ROLE_USER),
            'users as admin_count'   => fn($q) => $q->where('role', User::ROLE_INSTITUTION_ADMIN),
        ]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $institutions = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('superadmin.institutions.index', compact('institutions'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return redirect()
            ->route('superadmin.institutions.index')
            ->with('info', 'Use the Create Institution form on this page.');
    }

    /**
     * Store a new institution.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:189', Rule::unique('institutions', 'name')],
            'email'          => 'required|email|max:189|unique:institutions,email',
            'address'        => 'nullable|string|max:189',
            'contact_number' => 'nullable|string|max:30',
            'website'        => 'nullable|url|max:189',
            'notes'          => 'nullable|string|max:1000',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => ['required', Rule::in(['active', 'disabled'])],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('institutions/logos', 'public');
        }

        if ($logoPath === false) {
            throw ValidationException::withMessages(['logo' => 'The logo could not be stored. Try again.']);
        }

        try {
            DB::transaction(function () use ($data, $logoPath): void {
                Institution::create([
                    'name' => trim($data['name']),
                    'slug' => Institution::generateUniqueSlug(trim($data['name'])),
                    'email' => strtolower(trim($data['email'])),
                    'address' => $data['address'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'website' => $data['website'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'logo_path' => $logoPath,
                    'status' => $data['status'],
                ]);
            }, 3);
        } catch (\Throwable $exception) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            throw $exception;
        }

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Institution created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Institution $institution)
    {
        return redirect()
            ->route('superadmin.institutions.index')
            ->with('info', 'Use the Edit action on the institutions page.');
    }

    /**
     * Update an institution.
     */
    public function update(Request $request, Institution $institution)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:189', Rule::unique('institutions', 'name')->ignore($institution->id)],
            'email'          => ['required', 'email', 'max:189', Rule::unique('institutions')->ignore($institution->id)],
            'address'        => 'nullable|string|max:189',
            'contact_number' => 'nullable|string|max:30',
            'website'        => 'nullable|url|max:189',
            'notes'          => 'nullable|string|max:1000',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => ['required', Rule::in(['active', 'disabled'])],
        ]);

        $newLogoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('institutions/logos', 'public')
            : null;

        if ($newLogoPath === false) {
            throw ValidationException::withMessages(['logo' => 'The logo could not be stored. Try again.']);
        }

        try {
            $oldLogoPath = DB::transaction(function () use ($institution, $data, $newLogoPath): ?string {
                $lockedInstitution = Institution::query()
                    ->whereKey($institution->id)
                    ->lockForUpdate()
                    ->firstOrFail();
                $oldLogoPath = $lockedInstitution->logo_path;

                $lockedInstitution->update([
                    'name' => trim($data['name']),
                    'slug' => Institution::generateUniqueSlug(trim($data['name']), $lockedInstitution->id),
                    'email' => strtolower(trim($data['email'])),
                    'address' => $data['address'] ?? null,
                    'contact_number' => $data['contact_number'] ?? null,
                    'website' => $data['website'] ?? null,
                    'notes' => $data['notes'] ?? null,
                    'logo_path' => $newLogoPath ?? $oldLogoPath,
                    'status' => $data['status'],
                ]);

                return $newLogoPath && $oldLogoPath && $oldLogoPath !== $newLogoPath
                    ? $oldLogoPath
                    : null;
            }, 3);
        } catch (\Throwable $exception) {
            if ($newLogoPath) {
                Storage::disk('public')->delete($newLogoPath);
            }

            throw $exception;
        }

        if ($oldLogoPath) {
            Storage::disk('public')->delete($oldLogoPath);
        }

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Institution "'.trim($data['name']).'" updated successfully.');
    }

    /**
     * Toggle institution status active ↔ disabled.
     */
    public function toggleStatus(Institution $institution)
    {
        $result = DB::transaction(function () use ($institution): array {
            $lockedInstitution = Institution::query()
                ->whereKey($institution->id)
                ->lockForUpdate()
                ->firstOrFail();
            $lockedInstitution->status = $lockedInstitution->isActive() ? 'disabled' : 'active';
            $lockedInstitution->save();

            return [
                'name' => $lockedInstitution->name,
                'active' => $lockedInstitution->isActive(),
            ];
        }, 3);

        $label = $result['active'] ? 'enabled' : 'disabled';
        return back()->with('success', "Institution \"{$result['name']}\" has been {$label}.");
    }

    /** Permanently delete an institution only after its dependent records are cleared. */
    public function destroy(Institution $institution)
    {
        $result = DB::transaction(function () use ($institution): array {
            $lockedInstitution = Institution::query()
                ->whereKey($institution->id)
                ->lockForUpdate()
                ->firstOrFail();

            $hasDependencies = $lockedInstitution->users()->exists()
                || ClassRoom::query()->where('institution_id', $lockedInstitution->id)->exists()
                || $lockedInstitution->instructorApplications()->exists();

            if ($hasDependencies) {
                return ['deleted' => false, 'name' => $lockedInstitution->name, 'logo_path' => null];
            }

            $result = [
                'deleted' => true,
                'name' => $lockedInstitution->name,
                'logo_path' => $lockedInstitution->logo_path,
            ];
            $lockedInstitution->delete();

            return $result;
        }, 3);

        if (! $result['deleted']) {
            return redirect()->route('superadmin.institutions.index')
                ->with('error', 'This institution still has members, classes, or instructor applications. Reassign or remove those records before deleting it.');
        }

        if ($result['logo_path']) {
            Storage::disk('public')->delete($result['logo_path']);
        }

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', "Institution \"{$result['name']}\" has been permanently deleted.");
    }
}
