<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
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
        // Only an active institution passes validation, so the create and edit
        // forms offer that list; the filter still covers every institution.
        $activeInstitutions = $institutions->where('status', 'active');
        $roleOptions = $this->roleOptions();

        return view('admin.users.index', compact('users', 'institutions', 'activeInstitutions', 'roleOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data): void {
            $institutionId = $this->lockInstitutionValue($data['institution_id'] ?? null);

            User::create([
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'password' => Hash::make($data['password']),
                'role' => (int) $data['role'],
                'status' => $data['status'],
                'institution_id' => $institutionId,
            ]);
        }, 3);

        return redirect()->route('admin.users.index')->with('success', 'Account created successfully.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManageableUser($user);

        $data = $this->validatedData($request, $user);
        DB::transaction(function () use ($user, $data): void {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorizeManageableUser($lockedUser);
            $this->validateAccountTransition($lockedUser, (int) $data['role'], $data['institution_id'] ?? null);

            $payload = [
                'name' => trim($data['name']),
                'email' => strtolower(trim($data['email'])),
                'role' => (int) $data['role'],
                'status' => $data['status'],
                'institution_id' => $this->lockInstitutionValue($data['institution_id'] ?? null),
            ];

            if (! empty($data['password'])) {
                $payload['password'] = Hash::make($data['password']);
                $payload['remember_token'] = Str::random(60);
            }

            $lockedUser->update($payload);
        }, 3);

        return redirect()->route('admin.users.index')->with('success', 'Account updated successfully.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $updated = DB::transaction(function () use ($user): bool {
            $lockedUser = User::query()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorizeManageableUser($lockedUser);

            if ($lockedUser->id === auth()->id()) {
                return false;
            }

            $lockedUser->update([
                'status' => $lockedUser->status === 'active' ? 'disabled' : 'active',
            ]);

            return true;
        }, 3);

        if (! $updated) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot disable your own account.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Account status updated.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:189'],
            'email' => ['required', 'email', 'max:189', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', Rule::in(self::MANAGEABLE_ROLES)],
            'status' => ['required', Rule::in(['active', 'disabled'])],
            'institution_id' => [
                Rule::requiredIf(fn () => in_array((int) $request->input('role'), [User::ROLE_INSTRUCTOR, User::ROLE_INSTITUTION_ADMIN], true)),
                'nullable',
                'integer',
                Rule::exists('institutions', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
        ];

        $passwordRule = Password::min(8)->mixedCase()->numbers()->symbols();

        if ($user) {
            $rules['password'] = ['nullable', 'confirmed', $passwordRule];
        } else {
            $rules['password'] = ['required', 'confirmed', $passwordRule];
        }

        return $request->validate($rules);
    }

    private function lockInstitutionValue(mixed $institutionId): ?int
    {
        if (! $institutionId) {
            return null;
        }

        return (int) Institution::query()
            ->whereKey((int) $institutionId)
            ->where('status', 'active')
            ->lockForUpdate()
            ->firstOrFail()
            ->id;
    }

    /**
     * These refusals are things the administrator can act on, so they are
     * raised as validation messages. abort(422) rendered the generic
     * "Something is broken" error page and threw the reason away.
     */
    private function validateAccountTransition(User $user, int $newRole, mixed $institutionId): void
    {
        $newInstitutionId = $institutionId ? (int) $institutionId : null;

        if ((int) $user->role === User::ROLE_INSTRUCTOR && $newRole !== User::ROLE_INSTRUCTOR) {
            $ownsClasses = ClassRoom::where('instructor_id', $user->id)->exists();
            $this->refuseTransition($ownsClasses, 'role', 'Transfer or archive this instructor\'s classes before changing their role.');
        }

        if ((int) $user->role === User::ROLE_USER && $newRole !== User::ROLE_USER
            && $user->classesAsStudent()->exists()) {
            $this->refuseTransition(true, 'role', 'Remove this learner from all classes before changing their role.');
        }

        if ($newRole === User::ROLE_INSTRUCTOR) {
            $mismatchedClass = ClassRoom::where('instructor_id', $user->id)
                ->where(function ($query) use ($newInstitutionId) {
                    if ($newInstitutionId === null) {
                        $query->whereNotNull('institution_id');
                    } else {
                        $query->where('institution_id', '!=', $newInstitutionId)
                            ->orWhereNull('institution_id');
                    }
                })
                ->exists();
            $this->refuseTransition($mismatchedClass, 'institution_id', 'The selected institution does not match this instructor\'s classes.');
        }

        if ($newRole === User::ROLE_USER && $user->classesAsStudent()->exists()) {
            $classInstitutionIds = $user->classesAsStudent()
                ->whereNotNull('classes.institution_id')
                ->pluck('classes.institution_id')
                ->unique();

            $this->refuseTransition(
                $newInstitutionId === null
                || $classInstitutionIds->contains(fn ($id) => (int) $id !== $newInstitutionId),
                'institution_id',
                'The learner institution must match every class in which they are enrolled.'
            );
        }
    }

    private function refuseTransition(bool $condition, string $field, string $message): void
    {
        if ($condition) {
            throw ValidationException::withMessages([$field => $message]);
        }
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
