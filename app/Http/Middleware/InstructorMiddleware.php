<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $allowed = [
            User::ROLE_INSTRUCTOR,
            User::ROLE_INSTITUTION_ADMIN,
            User::ROLE_SUPERADMIN,
        ];

        if (!in_array((int) $user->role, $allowed, true)) {
            abort(403, 'Only instructor accounts can access this page.');
        }

        return $next($request);
    }
}
