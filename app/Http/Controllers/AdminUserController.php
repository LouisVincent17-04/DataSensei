<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    private const MANAGEABLE_ROLES = [
        User::ROLE_USER,
        User::ROLE_INSTRUCTOR,
        User::ROLE_INSTITUTION_ADMIN,
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));
        $role = $request->input('role');
        $status = $request->input('status');
        $institutionId = $request->input('institution_id');

        $query = User::query()
            ->with('institution')
            ->withCount(['classesAsStudent', 'assignmentSubmissions', 'userAchievements'])
            ->whereIn('role', self::MANAGEABLE_ROLES)
            ->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($role && in_array((int) $role, self::MANAGEABLE_ROLES, true)) {
            $query->where('role', (int) $role);
        }

        if ($status && in_array($status, ['active', 'disabled'], true)) {
            $query->where('status', $status);
        }

        if ($institutionId) {
            $query->where('institution_id', (int) $institutionId);
        }

        $users = $query->paginate(15)->withQueryString();
        $institutions = Institution::query()->orderBy('name')->get(['id', 'name', 'status']);
        $roleOptions = $this->roleOptions();

        return view('admin.users.index', compact('users', 'institutions', 'roleOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        User::create([
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'role' => (int) $data['role'],
            'status' => $data['status'],
            'institution_id' => $this->institutionValue((int) $data['role'], $data['institution_id'] ?? null),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Account created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManageableUser($user);

        $data = $this->validatedData($request, $user);

        $payload = [
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'role' => (int) $data['role'],
            'status' => $data['status'],
            'institution_id' => $this->institutionValue((int) $data['role'], $data['institution_id'] ?? null),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')->with('success', 'Account updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $this->authorizeManageableUser($user);

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot disable your own account.');
        }

        $user->update([
            'status' => $user->status === 'active' ? 'disabled' : 'active',
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Account status updated.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
            'status' => ['required', Rule::in(['active', 'disabled'])],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
        ];

        if ($user) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        return $request->validate($rules);
    }

    private function institutionValue(int $role, mixed $institutionId): ?int
    {
        if ($role === User::ROLE_INSTITUTION_ADMIN || $role === User::ROLE_INSTRUCTOR) {
            return $institutionId ? (int) $institutionId : null;
        }

        return null;
    }

    private function authorizeManageableUser(User $user): void
    {
        abort_unless(in_array((int) $user->role, self::MANAGEABLE_ROLES, true), 403, 'Admins cannot modify admin or superadmin accounts.');
    }

    private function roleOptions(): array
    {
        return [
            User::ROLE_USER => 'Learner / Common User',
            User::ROLE_INSTRUCTOR => 'Instructor',
            User::ROLE_INSTITUTION_ADMIN => 'Institution Admin',
        ];
    }
}
