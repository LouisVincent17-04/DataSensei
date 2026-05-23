<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Institution;

/*
|--------------------------------------------------------------------------
| Required routes (add to routes/web.php inside your superadmin group):
|--------------------------------------------------------------------------
|
| Route::get   ('/users',                        [UserManagementController::class, 'index'])               ->name('superadmin.users.index');
| Route::post  ('/users',                        [UserManagementController::class, 'store'])               ->name('superadmin.users.store');
| Route::put   ('/users/{user}',                 [UserManagementController::class, 'update'])              ->name('superadmin.users.update');
| Route::patch ('/users/{user}/toggle-status',   [UserManagementController::class, 'toggleStatus'])        ->name('superadmin.users.toggleStatus');
| Route::patch ('/users/{user}/promote',         [UserManagementController::class, 'promote'])             ->name('superadmin.users.promote');
| Route::patch ('/users/{user}/demote',          [UserManagementController::class, 'demote'])              ->name('superadmin.users.demote');
| Route::patch ('/users/{user}/assign-inst-admin',[UserManagementController::class, 'assignInstitutionAdmin'])->name('superadmin.users.assignInstitutionAdmin');
|
*/

class UserManagementController extends Controller
{
    // ─── INDEX ──────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = User::with('institution')->orderBy('created_at', 'desc');

        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role (accepts string label or numeric)
        if ($role = $request->input('role')) {
            $roleMap = ['student' => 1, 'admin' => 2, 'super_admin' => 3];
            if (isset($roleMap[$role])) {
                $query->where('role', $roleMap[$role]);
            }
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by institution
        if ($institutionId = $request->input('institution_id')) {
            $query->where('institution_id', $institutionId);
        }

        $users        = $query->paginate(15)->withQueryString();
        $institutions = Institution::orderBy('name')->get();
        $totalUsers   = $query->toBase()->getCountForPagination();

        return view('superadmin.users.index', compact('users', 'institutions', 'totalUsers'));
    }

    // ─── STORE (CREATE) ──────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:student,admin',   // super_admin cannot be created via this form
            'status'   => 'required|in:active,disabled',
        ]);

        $roleMap = ['student' => User::ROLE_USER, 'admin' => User::ROLE_ADMIN];

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $roleMap[$data['role']],
            'status'   => $data['status'],
            // institution_id intentionally omitted — only institution admins
            // may assign members to their institution.
        ]);

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User created successfully.');
    }

    // ─── UPDATE (EDIT — name / email / status only) ──────────────────────────────
    // institution_id is NOT updated here; super admins cannot assign regular
    // users to institutions — only institution admins may do that.
    // To promote/demote use the dedicated endpoints.
    // To assign someone as institution admin use assignInstitutionAdmin().

    public function update(Request $request, User $user)
    {
        // Prevent editing another super-admin unless you are one (extra guard)
        if ($user->isSuperAdmin() && auth()->id() !== $user->id) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'You cannot edit another Super Admin.');
        }

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'role'   => 'required|in:student,admin,super_admin',
            'status' => 'required|in:active,disabled',
        ]);

        $roleMap = [
            'student'     => User::ROLE_USER,
            'admin'       => User::ROLE_ADMIN,
            'super_admin' => User::ROLE_SUPERADMIN,
        ];

        $user->update([
            'name'   => $data['name'],
            'email'  => $data['email'],
            'role'   => $roleMap[$data['role']],
            'status' => $data['status'],
            // institution_id deliberately not touched here
        ]);

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // ─── TOGGLE STATUS ───────────────────────────────────────────────────────────

    public function toggleStatus(User $user)
    {
        // Prevent disabling yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'You cannot disable your own account.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'disabled' : 'active',
        ]);

        $label = ucfirst($user->status); // reflects new value after update

        return redirect()->route('superadmin.users.index')
                         ->with('success', "User has been {$label}.");
    }

    // ─── PROMOTE ROLE ────────────────────────────────────────────────────────────
    // Promotes a user one step up: student → admin, admin → super_admin.
    // Promoting to super_admin clears their institution_id (they become global).

    public function promote(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,super_admin',
        ]);

        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'You cannot change your own role here.');
        }

        $roleMap = [
            'admin'       => User::ROLE_ADMIN,
            'super_admin' => User::ROLE_SUPERADMIN,
        ];

        $updateData = ['role' => $roleMap[$request->role]];

        // Promoting to super_admin detaches them from any institution
        if ($request->role === 'super_admin') {
            $updateData['institution_id'] = null;
        }

        $user->update($updateData);

        $label = $request->role === 'super_admin' ? 'Super Admin' : 'Admin';

        return redirect()->route('superadmin.users.index')
                         ->with('success', "{$user->name} has been promoted to {$label}.");
    }

    // ─── DEMOTE ROLE ─────────────────────────────────────────────────────────────
    // Demotes a user one step down: super_admin → admin, admin → student.
    // Demoting from admin → student also clears their institution_id (they lose
    // institution-admin rights automatically).

    public function demote(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'You cannot change your own role.');
        }

        if ($user->isUser()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', "{$user->name} is already a Student — cannot demote further.");
        }

        $newRole     = $user->role - 1;                    // 3→2 or 2→1
        $roleLabels  = [1 => 'Student', 2 => 'Admin'];

        $updateData  = ['role' => $newRole];

        // Demoting an institution admin back to student revokes institution access
        if ($newRole === User::ROLE_USER) {
            $updateData['institution_id'] = null;
        }

        $user->update($updateData);

        return redirect()->route('superadmin.users.index')
                         ->with('success', "{$user->name} has been demoted to {$roleLabels[$newRole]}.");
    }

    // ─── ASSIGN INSTITUTION ADMIN ────────────────────────────────────────────────
    // Super admins may designate a user (student or existing admin) as the admin
    // of a specific institution. This sets their role to ROLE_ADMIN and links them
    // to the chosen institution.
    //
    // NOTE: Super admins CANNOT add regular members/students to an institution —
    //       that is exclusively the institution admin's responsibility. This action
    //       only appoints institution-level admins.

    public function assignInstitutionAdmin(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'A Super Admin cannot be assigned as an institution admin.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('superadmin.users.index')
                             ->with('error', 'You cannot change your own role.');
        }

        $data = $request->validate([
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $institution = Institution::findOrFail($data['institution_id']);

        $user->update([
            'role'           => User::ROLE_INSTITUTION_ADMIN,
            'institution_id' => $institution->id,
        ]);

        return redirect()->route('superadmin.users.index')
                         ->with('success', "{$user->name} is now the Institution Admin of {$institution->name}.");
    }
}