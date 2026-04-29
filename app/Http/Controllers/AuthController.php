<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Module;
use App\Http\Controllers\SqlSandboxController;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role == User::ROLE_SUPERADMIN)  return redirect('/superadmin/dashboard'); 
            if ($user->role == User::ROLE_ADMIN)        return redirect('/admin/dashboard'); 
            if ($user->role == User::ROLE_INSTRUCTOR)   return redirect('/instructor/dashboard'); 
            if ($user->role == User::ROLE_STUDENT)      return redirect('/student/dashboard'); 

            return redirect('/');
        }

        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Ensure the sandbox DB exists for accounts created before this fix.
            // touch() is a no-op when the file is already there, so this is safe
            // to run on every login with zero performance cost.
            SqlSandboxController::provisionSandbox($user->id);

            if ($user->role == User::ROLE_SUPERADMIN)  return redirect('/superadmin/dashboard');
            if ($user->role == User::ROLE_ADMIN)        return redirect('/admin/dashboard');
            if ($user->role == User::ROLE_INSTRUCTOR)   return redirect('/instructor/dashboard');
            if ($user->role == User::ROLE_STUDENT)      return redirect('/student/dashboard');

            return redirect('/auth/login');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.'
        ])->withInput();
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|in:student,educator',
        ]);

        $assignedRole = ($request->role === 'educator')
            ? User::ROLE_INSTRUCTOR
            : User::ROLE_STUDENT;

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => $assignedRole,
        ]);

        // ── Provision the user's private SQL Sandbox database ─────────────────
        // This creates storage/app/sandbox/user_{id}.sqlite so that the sandbox
        // is ready the first time the user visits /sql-sandbox, without them
        // having to run any query first.
        SqlSandboxController::provisionSandbox($user->id);

        Auth::login($user);

        // ── Auto-unlock the first module for students ─────────────────────────
        if ($user->role == User::ROLE_STUDENT) {
            $firstModule = Module::orderBy('order_index', 'asc')->first();
            if ($firstModule) {
                $user->modules()->attach($firstModule->id, ['is_unlocked' => true]);
            }
            return redirect('/student/dashboard')->with('success', 'Welcome to DataSensei!');
        }

        if ($user->role == User::ROLE_INSTRUCTOR) {
            return redirect('/instructor/dashboard')->with('success', 'Welcome Educator!');
        }

        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}