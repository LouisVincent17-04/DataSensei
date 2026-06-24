<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstitutionAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (int) $user->role !== User::ROLE_INSTITUTION_ADMIN || ! $user->institution_id) {
            abort(403, 'Access denied. Institution admin only.');
        }

        return $next($request);
    }
}
