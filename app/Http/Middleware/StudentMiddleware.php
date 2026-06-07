<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        /*
         * Adjust this if your database uses another role value.
         * Examples accepted here:
         * - student
         * - Student
         * - 1
         */
        $studentRoles = ['student', 'Student', 'STUDENT', 1, '1'];

        if (!in_array($user->role, $studentRoles, true)) {
            abort(403, 'Only student accounts can access this page.');
        }

        return $next($request);
    }
}