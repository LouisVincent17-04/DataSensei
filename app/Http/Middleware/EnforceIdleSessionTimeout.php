<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceIdleSessionTimeout
{
    public const LAST_ACTIVITY_KEY = 'auth.last_user_activity_at';

    /**
     * Requests that run automatically in the background must not keep the
     * authenticated session alive. They still pass through the expiration
     * check, allowing the browser to detect an expired session promptly.
     *
     * @var array<int, string>
     */
    private const BACKGROUND_ROUTE_NAMES = [
        'student.notifications.count',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check()) {
            return $next($request);
        }

        $now = time();
        $timeoutSeconds = max(60, (int) config('session.idle_timeout', 240) * 60);
        $lastActivity = (int) $request->session()->get(self::LAST_ACTIVITY_KEY, $now);

        if (($now - $lastActivity) >= $timeoutSeconds) {
            return $this->expireSession($request);
        }

        if ($this->countsAsUserActivity($request)) {
            $request->session()->put(self::LAST_ACTIVITY_KEY, $now);
        }

        return $next($request);
    }

    private function countsAsUserActivity(Request $request): bool
    {
        $routeName = $request->route()?->getName();

        if (is_string($routeName) && in_array($routeName, self::BACKGROUND_ROUTE_NAMES, true)) {
            return false;
        }

        return $request->header('X-DataSensei-Background') !== '1';
    }

    private function expireSession(Request $request): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message = sprintf(
            'You were signed out after %d minutes of inactivity.',
            (int) config('session.idle_timeout', 240)
        );

        if ($request->expectsJson() || $request->ajax()) {
            return new JsonResponse([
                'message' => $message,
                'session_expired' => true,
                'login_url' => route('login', ['expired' => 1]),
            ], 401);
        }

        return (new RedirectResponse(route('login', ['expired' => 1])))
            ->with('status', $message);
    }
}
