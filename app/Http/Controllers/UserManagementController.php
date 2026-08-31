<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Institution;
use App\Models\ClassRoom;
use App\Support\AuthSessionFingerprint;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

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
        if (($role = $request->input('role')) !== null && $role !== '') {
            $roleMap = [
                'student' => User::ROLE_USER,
                'admin' => User::ROLE_ADMIN,
                'super_admin' => User::ROLE_SUPERADMIN,
                'instructor' => User::ROLE_INSTRUCTOR,
                'institution_admin' => User::ROLE_INSTITUTION_ADMIN,
            ];
            $roleValue = is_numeric($role) ? (int) $role : ($roleMap[$role] ?? null);

            if (in_array($roleValue, array_keys(User::ROLE_LABELS), true)) {
                $query->where('role', $roleValue);
            }
        }

        // Filter by status
        if (($status = $request->input('status')) && in_array($status, ['active', 'disabled'], true)) {
            $query->where('status', $status);
        }

        // Filter by institution
        if ($institutionId = $request->input('institution_id')) {
            $query->where('institution_id', $institutionId);
        }

        $totalUsers   = (clone $query)->count();
        $users        = $query->paginate(15)->withQueryString();
        $institutions = Institution::orderBy('name')->get();
        $activeInstitutions = $institutions->where('status', 'active');

        return view('superadmin.users.index', compact('users', 'institutions', 'activeInstitutions', 'totalUsers'));
    }

    // ─── STORE (CREATE) ──────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:189',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'role'     => 'required|in:student,admin',   // super_admin cannot be created via this form
            'status'   => 'required|in:active,disabled',
        ]);

        $roleMap = ['student' => User::ROLE_USER, 'admin' => User::ROLE_ADMIN];

        User::create([
            'name'     => trim($data['name']),
            'email'    => strtolower(trim($data['email'])),
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
        $data = $request->validate([
            'name'   => 'required|string|max:189',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'status' => 'required|in:active,disabled',
        ]);

        $result = DB::transaction(function () use ($user, $data): string {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->isSuperAdmin() && auth()->id() !== $lockedUser->id) {
                return 'protected';
            }

            if ($lockedUser->id === auth()->id() && $data['status'] !== 'active') {
                return 'self_disable';
            }

            $lockedUser->update([
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'status' => $data['status'],
            ]);

            return 'updated';
        }, 3);

        if ($result === 'protected') {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'You cannot edit another Super Admin.');
        }

        if ($result === 'self_disable') {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'You cannot disable your own account.');
        }

        if ((int) $user->id === (int) auth()->id()) {
            $currentUser = User::query()->findOrFail($user->id);
            $request->session()->put(
                AuthSessionFingerprint::SESSION_KEY,
                AuthSessionFingerprint::for($currentUser)
            );
        }

        return redirect()->route('superadmin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    // ─── TOGGLE STATUS ───────────────────────────────────────────────────────────

    public function toggleStatus(User $user)
    {
        $result = DB::transaction(function () use ($user): array {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->id === auth()->id()) {
                return ['status' => 'self', 'label' => null];
            }

            if ($lockedUser->isSuperAdmin()) {
                return ['status' => 'protected', 'label' => null];
            }

            $lockedUser->update([
                'status' => $lockedUser->status === 'active' ? 'disabled' : 'active',
            ]);

            return ['status' => 'updated', 'label' => ucfirst($lockedUser->status)];
        }, 3);

        if ($result['status'] === 'self') {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'You cannot disable your own account.');
        }

        if ($result['status'] === 'protected') {
            return redirect()->route('superadmin.users.index')
                ->with('error', 'Another Super Admin account cannot be disabled here.');
        }

        return redirect()->route('superadmin.users.index')
                         ->with('success', "User has been {$result['label']}.");
    }

    // ─── PROMOTE ROLE ────────────────────────────────────────────────────────────
    // Promotes a user one step up: student → admin, admin → super_admin.
    // Promoting to super_admin clears their institution_id (they become global).

    public function promote(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,super_admin',
        ]);

        $roleMap = [
            'admin'       => User::ROLE_ADMIN,
            'super_admin' => User::ROLE_SUPERADMIN,
        ];

        $result = DB::transaction(function () use ($user, $request, $roleMap): string {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->id === auth()->id()) {
                return 'self';
            }

            if ($lockedUser->isUser() && $lockedUser->classesAsStudent()->exists()) {
                return 'enrolled';
            }

            $expectedRole = match ((int) $lockedUser->role) {
                User::ROLE_USER => User::ROLE_ADMIN,
                User::ROLE_ADMIN => User::ROLE_SUPERADMIN,
                default => null,
            };

            if ($expectedRole === null || $roleMap[$request->role] !== $expectedRole) {
                return 'invalid';
            }

            $lockedUser->update([
                'role' => $roleMap[$request->role],
                'institution_id' => null,
            ]);

            return 'promoted';
        }, 3);

        if ($result === 'self') {
            return redirect()->route('superadmin.users.index')->with('error', 'You cannot change your own role here.');
        }
        if ($result === 'enrolled') {
            return redirect()->route('superadmin.users.index')->with('error', 'Remove this learner from all classes before promoting the account.');
        }
        if ($result === 'invalid') {
            return redirect()->route('superadmin.users.index')->with('error', 'That promotion is not a valid one-step role change.');
        }

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
        $result = DB::transaction(function () use ($user): array {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->id === auth()->id()) {
                return ['status' => 'self', 'role' => null, 'name' => $lockedUser->name];
            }
            if ($lockedUser->isInstructor() && ClassRoom::where('instructor_id', $lockedUser->id)->exists()) {
                return ['status' => 'owns_classes', 'role' => null, 'name' => $lockedUser->name];
            }
            if ($lockedUser->isUser()) {
                return ['status' => 'lowest', 'role' => null, 'name' => $lockedUser->name];
            }

            $newRole = match ((int) $lockedUser->role) {
                User::ROLE_SUPERADMIN => User::ROLE_ADMIN,
                User::ROLE_ADMIN, User::ROLE_INSTRUCTOR, User::ROLE_INSTITUTION_ADMIN => User::ROLE_USER,
                default => null,
            };

            if ($newRole === null) {
                return ['status' => 'invalid', 'role' => null, 'name' => $lockedUser->name];
            }

            $lockedUser->update(['role' => $newRole, 'institution_id' => null]);

            return ['status' => 'demoted', 'role' => $newRole, 'name' => $lockedUser->name];
        }, 3);

        if ($result['status'] === 'self') {
            return redirect()->route('superadmin.users.index')->with('error', 'You cannot change your own role.');
        }
        if ($result['status'] === 'owns_classes') {
            return redirect()->route('superadmin.users.index')->with('error', 'Transfer or permanently remove this instructor\'s classes before demoting the account.');
        }
        if ($result['status'] === 'lowest') {
            return redirect()->route('superadmin.users.index')->with('error', "{$result['name']} is already a Student — cannot demote further.");
        }
        if ($result['status'] === 'invalid') {
            return redirect()->route('superadmin.users.index')->with('error', 'This account cannot be demoted from its current role.');
        }

        return redirect()->route('superadmin.users.index')
                         ->with('success', "{$result['name']} has been demoted to ".User::ROLE_LABELS[$result['role']].'.');
    }

    // ─── ASSIGN INSTITUTION ADMIN ────────────────────────────────────────────────
    // Super admins may designate a user (student or existing admin) as the admin
    // of a specific institution. This sets their role to ROLE_INSTITUTION_ADMIN and links them
    // to the chosen institution.
    //
    // NOTE: Super admins CANNOT add regular members/students to an institution —
    //       that is exclusively the institution admin's responsibility. This action
    //       only appoints institution-level admins.

    public function assignInstitutionAdmin(Request $request, User $user)
    {
        $data = $request->validate([
            'institution_id' => [
                'required',
                Rule::exists('institutions', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ]);

        $result = DB::transaction(function () use ($user, $data): array {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->isSuperAdmin()) {
                return ['status' => 'protected', 'name' => $lockedUser->name, 'institution' => null];
            }
            if ($lockedUser->id === auth()->id()) {
                return ['status' => 'self', 'name' => $lockedUser->name, 'institution' => null];
            }
            if ($lockedUser->isInstructor() && ClassRoom::where('instructor_id', $lockedUser->id)->exists()) {
                return ['status' => 'owns_classes', 'name' => $lockedUser->name, 'institution' => null];
            }
            if ($lockedUser->isUser() && $lockedUser->classesAsStudent()->exists()) {
                return ['status' => 'enrolled', 'name' => $lockedUser->name, 'institution' => null];
            }

            $institution = Institution::query()
                ->whereKey($data['institution_id'])
                ->where('status', 'active')
                ->lockForUpdate()
                ->firstOrFail();

            $lockedUser->update([
                'role' => User::ROLE_INSTITUTION_ADMIN,
                'institution_id' => $institution->id,
            ]);

            return ['status' => 'assigned', 'name' => $lockedUser->name, 'institution' => $institution->name];
        }, 3);

        if ($result['status'] === 'protected') {
            return redirect()->route('superadmin.users.index')->with('error', 'A Super Admin cannot be assigned as an institution admin.');
        }
        if ($result['status'] === 'self') {
            return redirect()->route('superadmin.users.index')->with('error', 'You cannot change your own role.');
        }
        if ($result['status'] === 'owns_classes') {
            return redirect()->route('superadmin.users.index')->with('error', 'Transfer or permanently remove this instructor\'s classes before changing the account role.');
        }
        if ($result['status'] === 'enrolled') {
            return redirect()->route('superadmin.users.index')->with('error', 'Remove this learner from all classes before assigning institution-admin access.');
        }

        return redirect()->route('superadmin.users.index')
                         ->with('success', "{$result['name']} is now the Institution Admin of {$result['institution']}.");
    }
}
