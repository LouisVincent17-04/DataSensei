<?php
// app/Http/Middleware/RedirectIfAuthenticated.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {

            $role = Auth::user()->role;

            return match((int) $role) {
                User::ROLE_USER => redirect('/student/dashboard'),
                User::ROLE_ADMIN => redirect('/admin/dashboard'),
                User::ROLE_SUPERADMIN => redirect('/superadmin/dashboard'),
                User::ROLE_INSTRUCTOR => redirect('/instructor/dashboard'),
                User::ROLE_INSTITUTION_ADMIN => redirect('/institution_admin/dashboard'),
                default => abort(403),
            };
        }

        return $next($request);
    }
}