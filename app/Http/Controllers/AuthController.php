<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Module;
use App\Http\Controllers\SqlSandboxController;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (isset($user->status) && $user->status === 'disabled') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors([
                    'email' => 'Your account has been disabled. Please contact the administrator.',
                ])
                ->withInput();
        }

        SqlSandboxController::provisionSandbox($user->id);

        return $this->redirectUserByRole($user);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => trim($request->name),
            'email'    => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role'     => User::ROLE_USER,
            'status'   => 'active',
        ]);

        SqlSandboxController::provisionSandbox($user->id);

        Auth::login($user);

        $firstModule = Module::orderBy('order_index', 'asc')->first();

        if ($firstModule) {
            $user->modules()->syncWithoutDetaching([
                $firstModule->id => ['is_unlocked' => true],
            ]);
        }

        return redirect('/student/dashboard')->with('success', 'Welcome to DataSensei!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function redirectUserByRole(User $user)
    {
        if ($user->role == User::ROLE_SUPERADMIN) {
            return redirect('/superadmin/dashboard');
        }

        if ($user->role == User::ROLE_ADMIN) {
            return redirect('/admin/dashboard');
        }

        if ($user->role == User::ROLE_INSTITUTION_ADMIN) {
            return redirect('/institution_admin/dashboard');
        }

        if ($user->role == User::ROLE_INSTRUCTOR) {
            return redirect('/instructor/dashboard');
        }

        if ($user->role == User::ROLE_USER) {
            return redirect('/student/dashboard');
        }

        return redirect('/');
    }
}