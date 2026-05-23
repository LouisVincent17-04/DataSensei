<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\InstructorApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class InstructorApplicationController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    //  INSTRUCTOR SIDE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Show the "Enter Institution Code" form for instructors.
     * GET /instructor/apply
     */
    public function showApplyForm()
    {
        $user = Auth::user();

        // If the instructor already has an active/pending application, show its status
        $existingApplication = InstructorApplication::where('user_id', $user->id)
            ->with('institution')
            ->latest()
            ->first();

        return view('instructor.apply', compact('existingApplication'));
    }

    /**
     * Handle the instructor's code submission.
     * POST /instructor/apply
     */
    public function apply(Request $request)
    {
        $request->validate([
            'institution_code' => ['required', 'string', 'size:6'],
        ]);

        $user = Auth::user();
        $code = strtoupper(trim($request->institution_code));

        // 1. Find the institution by code
        $institution = Institution::where('institution_code', $code)
            ->where('status', 'active')
            ->first();

        if (! $institution) {
            return back()
                ->withInput()
                ->withErrors(['institution_code' => 'Invalid or inactive institution code. Please double-check and try again.']);
        }

        // 2. Prevent duplicate applications
        $existing = InstructorApplication::where('user_id', $user->id)
            ->where('institution_id', $institution->id)
            ->first();

        if ($existing) {
            if ($existing->isPending()) {
                return back()->withErrors([
                    'institution_code' => "You already have a pending application for {$institution->name}.",
                ]);
            }

            if ($existing->isApproved()) {
                return back()->withErrors([
                    'institution_code' => "You are already an approved instructor at {$institution->name}.",
                ]);
            }

            // If previously rejected, allow re-application by updating the record
            $existing->update([
                'entered_code' => $code,
                'status'       => 'pending',
                'reviewed_by'  => null,
                'reviewed_at'  => null,
            ]);

            return redirect()->route('instructor.apply')
                ->with('success', "Your re-application to {$institution->name} has been submitted and is pending review.");
        }

        // 3. Create the application
        InstructorApplication::create([
            'user_id'        => $user->id,
            'institution_id' => $institution->id,
            'entered_code'   => $code,
            'status'         => 'pending',
        ]);

        return redirect()->route('instructor.apply')
            ->with('success', "Application submitted to {$institution->name}! You'll be notified once an admin reviews it.");
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  INSTITUTION ADMIN SIDE
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Full applications list with filtering.
     * GET /institution-admin/applications
     */
    public function index(Request $request)
    {
        $admin       = Auth::user();
        $institution = $admin->institution;

        $status = $request->query('status', 'pending'); // default to pending tab

        $applications = InstructorApplication::where('institution_id', $institution->id)
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->with(['user', 'reviewer'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending'  => $institution->instructorApplications()->where('status', 'pending')->count(),
            'approved' => $institution->instructorApplications()->where('status', 'approved')->count(),
            'rejected' => $institution->instructorApplications()->where('status', 'rejected')->count(),
        ];

        return view('institution_admin.applications', compact(
            'institution',
            'applications',
            'counts',
            'status',
        ));
    }

    /**
     * Approve an instructor application.
     * PATCH /institution-admin/applications/{application}/approve
     */
    public function approve(InstructorApplication $application)
    {
        $this->authorizeAdminAccess($application);

        if (! $application->isPending()) {
            return back()->with('error', 'This application has already been reviewed.');
        }

        DB::transaction(function () use ($application) {
            $application->update([
                'status'      => 'approved',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
            ]);

            // Link the instructor to the institution in the users table
            $application->user->update([
                'institution_id' => $application->institution_id,
                'role'           => User::ROLE_INSTRUCTOR,
            ]);
        });

        return back()->with('success', "{$application->user->name} has been approved as an instructor.");
    }

    /**
     * Reject an instructor application.
     * PATCH /institution-admin/applications/{application}/reject
     */
    public function reject(InstructorApplication $application)
    {
        $this->authorizeAdminAccess($application);

        if (! $application->isPending()) {
            return back()->with('error', 'This application has already been reviewed.');
        }

        $application->update([
            'status'      => 'rejected',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', "{$application->user->name}'s application has been rejected.");
    }

    // ── Private helpers ────────────────────────────────────────────────────────

    /**
     * Ensure the logged-in admin can only act on applications
     * that belong to their institution.
     */
    private function authorizeAdminAccess(InstructorApplication $application): void
    {
        $adminInstitutionId = Auth::user()->institution_id;

        if ($application->institution_id !== $adminInstitutionId) {
            abort(403, 'You do not have permission to review this application.');
        }
    }
}