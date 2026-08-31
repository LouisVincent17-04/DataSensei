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

        $code = strtoupper(trim($request->institution_code));

        return DB::transaction(function () use ($code) {
            $user = User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();

            if ((int) $user->role !== User::ROLE_USER || $user->institution_id !== null) {
                return back()->withErrors([
                    'institution_code' => 'Only an unassigned learner account can submit an instructor application.',
                ]);
            }

            // Lock the institution so it cannot be disabled between validation
            // and creation of the pending application.
            $institution = Institution::where('institution_code', $code)
                ->where('status', 'active')
                ->lockForUpdate()
                ->first();

            if (! $institution) {
                return back()
                    ->withInput()
                    ->withErrors(['institution_code' => 'Invalid or inactive institution code. Please double-check and try again.']);
            }

            // 2. Prevent duplicate applications
            $otherActiveApplication = InstructorApplication::query()
                ->where('user_id', $user->id)
                ->where('institution_id', '!=', $institution->id)
                ->whereIn('status', ['pending', 'approved'])
                ->with('institution')
                ->first();

            if ($otherActiveApplication) {
                return back()->withErrors([
                    'institution_code' => "You already have an active application with {$otherActiveApplication->institution->name}.",
                ]);
            }

            $existing = InstructorApplication::where('user_id', $user->id)
                ->where('institution_id', $institution->id)
                ->first();

            if ($existing) {
                if ($existing->isPending()) {
                    return back()->withErrors([
                        'institution_code' => "You already have a pending application for {$institution->name}.",
                    ]);
                }

                // Re-open a rejected or legacy approved record after the account has
                // been returned to an unassigned learner role.
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
        }, 3);
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

        $status = (string) $request->query('status', 'pending');
        if (! in_array($status, ['pending', 'approved', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

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

        $result = DB::transaction(function () use ($application): string {
            $lockedApplication = InstructorApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedApplication->isPending()) {
                return 'reviewed';
            }

            $reviewer = User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            if (! $reviewer->is_active
                || ! $reviewer->isInstitutionAdmin()
                || (int) $reviewer->institution_id !== (int) $lockedApplication->institution_id) {
                return 'unauthorized';
            }

            $applicant = User::query()
                ->whereKey($lockedApplication->user_id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $applicant->is_active || (int) $applicant->role !== User::ROLE_USER || $applicant->institution_id !== null) {
                return 'ineligible';
            }

            $institution = Institution::query()
                ->whereKey($lockedApplication->institution_id)
                ->lockForUpdate()
                ->first();
            if (! $institution || $institution->status !== 'active') {
                return 'inactive_institution';
            }

            $lockedApplication->update([
                'status'      => 'approved',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $applicant->update([
                'institution_id' => $lockedApplication->institution_id,
                'role'           => User::ROLE_INSTRUCTOR,
            ]);

            return 'approved';
        }, 3);

        if ($result === 'reviewed') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        if ($result === 'ineligible') {
            return back()->with('error', 'This applicant is disabled, already assigned, or no longer eligible for approval.');
        }

        if ($result === 'inactive_institution') {
            return back()->with('error', 'This institution is inactive and cannot approve new instructors.');
        }

        abort_if($result === 'unauthorized', 403, 'Your access to this institution changed before the review completed.');

        return back()->with('success', "{$application->user->name} has been approved as an instructor.");
    }

    /**
     * Reject an instructor application.
     * PATCH /institution-admin/applications/{application}/reject
     */
    public function reject(InstructorApplication $application)
    {
        $this->authorizeAdminAccess($application);

        $rejected = DB::transaction(function () use ($application): string {
            $lockedApplication = InstructorApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $lockedApplication->isPending()) {
                return 'reviewed';
            }

            $reviewer = User::query()->whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            if (! $reviewer->is_active
                || ! $reviewer->isInstitutionAdmin()
                || (int) $reviewer->institution_id !== (int) $lockedApplication->institution_id) {
                return 'unauthorized';
            }

            $lockedApplication->update([
                'status'      => 'rejected',
                'reviewed_by' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            return 'rejected';
        }, 3);

        if ($rejected === 'reviewed') {
            return back()->with('error', 'This application has already been reviewed.');
        }

        abort_if($rejected === 'unauthorized', 403, 'Your access to this institution changed before the review completed.');

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
