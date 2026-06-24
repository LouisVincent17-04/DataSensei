<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (int) $user->role !== User::ROLE_SUPERADMIN) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}
