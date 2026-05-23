<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures the authenticated user is an institution admin:
 *   - role === 2  (Admin)
 *   - institution_id is not null  (linked to an institution)
 *
 * Register this alias in bootstrap/app.php (Laravel 11):
 *
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias([
 *           'superadmin'       => \App\Http\Middleware\SuperAdmin::class,
 *           'institution.admin'=> \App\Http\Middleware\InstitutionAdmin::class,
 *       ]);
 *   })
 *
 * Or in app/Http/Kernel.php (Laravel 10) under $routeMiddleware.
 */
class InstitutionAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 5 || ! $user->institution_id) {
            abort(403, 'Access denied. Institution admin only.');
        }

        return $next($request);
    }
}