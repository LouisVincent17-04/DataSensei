<?php

use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\EnforceIdleSessionTimeout::class);

        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'institution.admin' => \App\Http\Middleware\InstitutionAdmin::class,
            'student' => \App\Http\Middleware\StudentMiddleware::class,
            'instructor' => \App\Http\Middleware\InstructorMiddleware::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'active' => \App\Http\Middleware\EnsureActiveUser::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): string {
            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request): string {
            $user = $request->user();

            if (! $user) {
                return route('login');
            }

            return match ((int) $user->role) {
                User::ROLE_USER => route('studentDashboard'),
                User::ROLE_ADMIN => route('admin.dashboard'),
                User::ROLE_SUPERADMIN => route('superadmin.dashboard'),
                User::ROLE_INSTRUCTOR => route('instructor.dashboard'),
                User::ROLE_INSTITUTION_ADMIN => route('institution-admin.dashboard'),
                default => route('login'),
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
