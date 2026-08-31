<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Module;
use App\Support\AuthSessionFingerprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function home(Request $request)
    {
        if ($request->user()) {
            return $this->redirectUserByRole($request->user());
        }

        return redirect()->route('login');
    }

    public function showLogin(Request $request)
    {
        if ($request->user()) {
            return $this->redirectUserByRole($request->user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['email'] = strtolower(trim($credentials['email']));

        if (! Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        $user = $request->user();

        $accessFailure = $user ? $this->accessFailureReason($user) : 'Your account cannot be accessed.';

        if ($accessFailure !== null) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors([
                    'email' => $accessFailure,
                ])
                ->withInput($request->only('email'));
        }

        $request->session()->put(
            AuthSessionFingerprint::SESSION_KEY,
            AuthSessionFingerprint::for($user)
        );

        return $this->redirectUserByRole($user);
    }

    public function register(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:189'],
            'email'    => ['required', 'email', 'max:189', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name'     => trim($validated['name']),
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => User::ROLE_USER,
                'status'   => 'active',
            ]);

            $firstModule = Module::orderBy('order_index', 'asc')->first();

            if ($firstModule) {
                $user->modules()->syncWithoutDetaching([
                    $firstModule->id => ['is_unlocked' => true],
                ]);
            }

            return $user;
        }, 3);

        Auth::guard('web')->login($user);
        $request->session()->regenerate();
        $request->session()->put(
            AuthSessionFingerprint::SESSION_KEY,
            AuthSessionFingerprint::for($user)
        );

        return $this->redirectUserByRole($user)->with('success', 'Welcome to DataSensei!');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectUserByRole(User $user)
    {
        return match ((int) $user->role) {
            User::ROLE_SUPERADMIN => redirect()->route('superadmin.dashboard'),
            User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            User::ROLE_INSTITUTION_ADMIN => redirect()->route('institution-admin.dashboard'),
            User::ROLE_INSTRUCTOR => redirect()->route('instructor.dashboard'),
            User::ROLE_USER => redirect()->route('studentDashboard'),
            default => redirect()->route('login'),
        };
    }

    private function accessFailureReason(User $user): ?string
    {
        if (! $user->is_active) {
            return 'Your account has been disabled. Please contact the administrator.';
        }

        if (! in_array((int) $user->role, [User::ROLE_INSTRUCTOR, User::ROLE_INSTITUTION_ADMIN], true)) {
            return null;
        }

        if (! $user->institution_id || ! $user->institution || ! $user->institution->isActive()) {
            return 'Your institution access is inactive. Please contact the administrator.';
        }

        return null;
    }
}
