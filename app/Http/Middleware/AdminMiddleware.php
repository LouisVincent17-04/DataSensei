<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $role = (int) Auth::user()->role;

        if (! in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPERADMIN], true)) {
            abort(403, 'Only administrator accounts can access this page.');
        }

        return $next($request);
    }
}
