<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $reason = $this->accessFailureReason($user);

        if ($reason === null) {
            $storedFingerprint = $request->session()->get(AuthSessionFingerprint::SESSION_KEY);
            $currentFingerprint = AuthSessionFingerprint::for($user);

            if (is_string($storedFingerprint) && ! hash_equals($storedFingerprint, $currentFingerprint)) {
                $reason = 'Your account access changed. Please sign in again.';
            } elseif (Auth::viaRemember()) {
                // A valid remember-me cookie was already checked against the
                // rotated remember token, so it is safe to initialise here.
                $request->session()->put(AuthSessionFingerprint::SESSION_KEY, $currentFingerprint);
            } elseif (! is_string($storedFingerprint)) {
                $reason = 'Your session needs to be refreshed. Please sign in again.';
            }
        }

        if ($reason === null) {
            return $next($request);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return new JsonResponse(['message' => $reason], 403);
        }

        return (new RedirectResponse(route('login')))
            ->withErrors(['email' => $reason]);
    }

    private function accessFailureReason(User $user): ?string
    {
        if (! $user->is_active) {
            return 'Your account has been disabled. Please contact the administrator.';
        }

        if (! in_array((int) $user->role, [User::ROLE_INSTRUCTOR, User::ROLE_INSTITUTION_ADMIN], true)) {
            return null;
        }

        if (! $user->institution_id || ! $user->institution || ! $user->institution->isActive()) {
            return 'Your institution access is inactive. Please contact the administrator.';
        }

        return null;
    }
}
