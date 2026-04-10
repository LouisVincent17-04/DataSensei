<?php
// app/Http/Middleware/RedirectIfAuthenticated.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (Auth::check()) {

            $role = Auth::user()->role;

            return match($role) {
                1 => redirect('/student/dashboard'),
                2 => redirect('/instructor/dashboard'),
                3 => redirect('/admin/dashboard'),
                4 => redirect('/superadmin/dashboard'),
                default => abort(403),
            };
        }

        return $next($request);
    }
}