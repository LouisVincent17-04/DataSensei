<?php

namespace App\Http\Controllers;

use App\Models\InstructorApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitutionAdminController extends Controller
{
    /**
     * Institution Admin Dashboard.
     *
     * Shows platform stats scoped to the admin's institution and
     * a quick view of pending instructor applications.
     */
    public function dashboard()
    {
        $admin       = Auth::user();
        $institution = $admin->institution; // eager-loaded via belongsTo

        if (! $institution) {
            abort(403, 'Your account is not linked to any institution.');
        }

        // ── Stats ──────────────────────────────────────────────────────────────

        // All users whose institution_id matches (students + instructors etc.)
        $totalMembers = $institution->users()->count();

        // Approved instructors
        $totalInstructors = $institution->approvedApplications()
            ->count();

        // Pending applications waiting for review
        $pendingCount = $institution->pendingApplications()->count();

        // Rejected this month
        $rejectedCount = $institution->instructorApplications()
            ->where('status', 'rejected')
            ->whereMonth('reviewed_at', now()->month)
            ->count();

        // ── Recent pending applications (for dashboard preview) ─────────────────
        $pendingApplications = $institution->pendingApplications()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        // ── Recent approved (for "Your Instructors" preview) ───────────────────
        $recentApproved = $institution->approvedApplications()
            ->with(['user', 'reviewer'])
            ->latest('reviewed_at')
            ->take(5)
            ->get();

        return view('institution_admin.dashboard', compact(
            'institution',
            'totalMembers',
            'totalInstructors',
            'pendingCount',
            'rejectedCount',
            'pendingApplications',
            'recentApproved',
        ));
    }
}