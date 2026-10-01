<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
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
        'api.code-review.warm',
        'api.code-review.status',
    ];

    /** The page's keep-alive call (routes/web.php). */
    private const HEARTBEAT_ROUTE_NAME = 'session.activity';

    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('web')->check()) {
            return $next($request);
        }

        $now = time();
        $timeoutSeconds = max(60, (int) config('session.idle_timeout', 60) * 60);
        $lastActivity = (int) $request->session()->get(self::LAST_ACTIVITY_KEY, $now);

        if (($now - $lastActivity) >= $timeoutSeconds) {
            return $this->expireSession($request);
        }

        if ($this->countsAsUserActivity($request)) {
            $request->session()->put(self::LAST_ACTIVITY_KEY, $this->activityTime($request, $now, $lastActivity, $timeoutSeconds));
        }

        return $next($request);
    }

    /**
     * When the activity happened. A page request is activity now. The
     * page's heartbeat is sent up to half a minute after the last real
     * input and says how long ago that was, so the server dates it from
     * then and signs the session out at the same moment the page does
     * (DataSensei Updates 9). A heartbeat can only move the time forward,
     * never past now, so it can shorten an idle session but never extend it.
     */
    private function activityTime(Request $request, int $now, int $lastActivity, int $timeoutSeconds): int
    {
        if ($request->route()?->getName() !== self::HEARTBEAT_ROUTE_NAME) {
            return $now;
        }

        $idleMs = $request->input('idle_ms');
        if (! is_numeric($idleMs)) {
            return $now;
        }

        $idleSeconds = (int) ceil(min(max(0.0, (float) $idleMs), $timeoutSeconds * 1000.0) / 1000);

        return max(min($lastActivity, $now), $now - $idleSeconds);
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
            (int) config('session.idle_timeout', 60)
        );

        if ($request->expectsJson() || $request->ajax()) {
            return new JsonResponse([
                'message' => $message,
                'session_expired' => true,
                'login_url' => route('login', ['expired' => 1]),
            ], 401);
        }

        return redirect()->route('login', ['expired' => 1])
            ->with('status', $message);
    }
}
