<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstructorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (int) $user->role !== User::ROLE_INSTRUCTOR) {
            abort(403, 'Only instructor accounts can access this page.');
        }

        return $next($request);
    }
}
