<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (int) $user->role !== User::ROLE_USER) {
            abort(403, 'Only student accounts can access this page.');
        }

        return $next($request);
    }
}
