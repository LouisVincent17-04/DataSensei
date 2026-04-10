<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // or 'auth.login' if using named routes
        }

        return $next($request);
    }
}