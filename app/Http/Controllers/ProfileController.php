<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\InstructorApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();

        $activeTab = $request->query('tab', 'general');

        if (! in_array($activeTab, ['general', 'institution', 'security'], true)) {
            $activeTab = 'general';
        }

        $institutions = Institution::where('status', 'active')
            ->orderBy('name')
            ->get();

        $instructorApplication = InstructorApplication::with('institution')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return view('student.profile', compact(
            'user',
            'activeTab',
            'institutions',
            'instructorApplication'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio'  => ['nullable', 'string', 'max:1000'],
        ]);

        $user->update([
            'name' => trim($validated['name']),
            'bio'  => $validated['bio'] ?? null,
        ]);

        return redirect()
            ->route('profile', ['tab' => 'general'])
            ->with('success', 'Profile information updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()
                ->route('profile', ['tab' => 'security'])
                ->withErrors([
                    'current_password' => 'Your current password is incorrect.',
                ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('profile', ['tab' => 'security'])
            ->with('success', 'Password updated successfully.');
    }

    public function applyAsInstructor(Request $request)
    {
        $user = Auth::user();

        if ($user->role != User::ROLE_USER) {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_application' => 'Only regular student accounts can apply as instructor.',
                ]);
        }

        if ($user->institution_id !== null) {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_application' => 'You already belong to an institution.',
                ]);
        }

        $validated = $request->validate([
            'institution_id'   => ['required', 'exists:institutions,id'],
            'institution_code' => ['required', 'string', 'size:6'],
        ]);

        $institution = Institution::where('id', $validated['institution_id'])
            ->where('status', 'active')
            ->first();

        if (! $institution) {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_application' => 'Selected institution is invalid or inactive.',
                ])
                ->withInput();
        }

        $enteredCode = strtoupper(trim($validated['institution_code']));

        if ($enteredCode !== strtoupper($institution->institution_code)) {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_code' => 'The institution code does not match the selected institution.',
                ])
                ->withInput();
        }

        $existingApplication = InstructorApplication::where('user_id', $user->id)
            ->where('institution_id', $institution->id)
            ->first();

        if ($existingApplication && $existingApplication->status === 'pending') {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_application' => 'You already have a pending instructor application for this institution.',
                ]);
        }

        if ($existingApplication && $existingApplication->status === 'approved') {
            return redirect()
                ->route('profile', ['tab' => 'institution'])
                ->withErrors([
                    'institution_application' => 'You are already approved for this institution.',
                ]);
        }

        InstructorApplication::updateOrCreate(
            [
                'user_id'        => $user->id,
                'institution_id' => $institution->id,
            ],
            [
                'entered_code' => $enteredCode,
                'status'       => 'pending',
                'reviewed_by'  => null,
                'reviewed_at'  => null,
            ]
        );

        return redirect()
            ->route('profile', ['tab' => 'institution'])
            ->with('success', "Your instructor application to {$institution->name} has been submitted.");
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'delete_password' => ['required', 'string'],
        ]);

        if (! Hash::check($request->delete_password, $user->password)) {
            return redirect()
                ->route('profile', ['tab' => 'security'])
                ->withErrors([
                    'delete_password' => 'Password is incorrect.',
                ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect('/login')->with('success', 'Your account has been deleted.');
    }
}
