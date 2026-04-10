<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return view('auth.login');
        }

        if (Auth::user()->role !== $role) {
           abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
