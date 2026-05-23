<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InstitutionManagementController extends Controller
{
    /**
     * List all institutions with optional search / filter.
     */
    public function index(Request $request)
    {
        $query = Institution::withCount([
            'users as student_count' => fn($q) => $q->where('role', User::ROLE_USER),
            'users as admin_count'   => fn($q) => $q->where('role', User::ROLE_ADMIN),
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
        return view('superadmin.institutions.create');
    }

    /**
     * Store a new institution.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:institutions,email',
            'address'        => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:30',
            'website'        => 'nullable|url|max:255',
            'notes'          => 'nullable|string|max:1000',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => ['required', Rule::in(['active', 'disabled'])],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('institutions/logos', 'public');
        }

        Institution::create([
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'email'          => $data['email'],
            'address'        => $data['address'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'website'        => $data['website'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'logo_path'      => $logoPath,
            'status'         => $data['status'],
        ]);

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', 'Institution created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Institution $institution)
    {
        return view('superadmin.institutions.edit', compact('institution'));
    }

    /**
     * Update an institution.
     */
    public function update(Request $request, Institution $institution)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('institutions')->ignore($institution->id)],
            'address'        => 'nullable|string|max:500',
            'contact_number' => 'nullable|string|max:30',
            'website'        => 'nullable|url|max:255',
            'notes'          => 'nullable|string|max:1000',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'         => ['required', Rule::in(['active', 'disabled'])],
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('institutions/logos', 'public');
        }

        $institution->update([
            'name'           => $data['name'],
            'slug'           => Str::slug($data['name']),
            'email'          => $data['email'],
            'address'        => $data['address'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'website'        => $data['website'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'logo_path'      => $data['logo_path'] ?? $institution->logo_path,
            'status'         => $data['status'],
        ]);

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', "Institution \"{$institution->name}\" updated successfully.");
    }

    /**
     * Toggle institution status active ↔ disabled.
     */
    public function toggleStatus(Institution $institution)
    {
        $institution->status = $institution->isActive() ? 'disabled' : 'active';
        $institution->save();

        $label = $institution->isActive() ? 'enabled' : 'disabled';
        return back()->with('success', "Institution \"{$institution->name}\" has been {$label}.");
    }

    /**
     * Permanently delete an institution and orphan its users.
     */
    public function destroy(Institution $institution)
    {
        // Detach users (nullify institution_id) before deleting
        $institution->users()->update(['institution_id' => null]);

        $name = $institution->name;
        $institution->delete();

        return redirect()->route('superadmin.institutions.index')
                         ->with('success', "Institution \"{$name}\" has been permanently deleted.");
    }
}