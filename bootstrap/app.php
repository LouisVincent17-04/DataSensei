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
        // The audit log of admin and instructor actions (DataSensei Updates 8).
        $middleware->appendToGroup('web', \App\Http\Middleware\RecordStaffActions::class);

        // Laravel trims every incoming string by default. For code and program
        // input, whitespace is part of the value: a Python file that ends with a
        // blank line, a file that starts with one, or stdin whose first line is
        // empty all came back altered. These fields are left exactly as typed.
        $middleware->trimStrings(except: [
            'content',
            'code',
            'stdin',
            'query',
            // Coding challenge authoring: a test case's stdin and expected
            // output are compared exactly against what the program prints,
            // so leading whitespace is part of the answer, not noise. The
            // same for starter code and reference solutions.
            'questions.*.starter_code',
            'questions.*.reference_solution',
            'questions.*.test_cases.*.input',
            'questions.*.test_cases.*.expected_output',
            'reference_solution',
            'test_cases.*.input',
            'test_cases.*.expected_output',
        ]);

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
        // A page left open outlives its session, and the token it was rendered
        // with stops matching. That is ordinary, not an attack: the request is
        // still refused, but the person is sent back to a freshly tokened form
        // that says what happened, instead of Laravel's bare "Page Expired".
        // Laravel turns TokenMismatchException into a 419 HttpException before
        // render callbacks are consulted, so the status is what to match on.
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception,
            Request $request
        ) {
            if ($exception->getStatusCode() !== 419) {
                return null;
            }

            // The sign-in and registration forms: send the person back to a
            // freshly tokened form that retries once by itself.
            if ($request->routeIs('login', 'register') || $request->is('login', 'register')) {
                $message = 'Your session expired before the form was sent. Please try again.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'session_expired' => true,
                        // Lets a script retry straight away rather than dead-end.
                        'csrf_token' => $request->hasSession()
                            ? $request->session()->token()
                            : null,
                    ], 419);
                }

                return redirect()->route('login')
                    ->withInput($request->except(['password', 'password_confirmation', '_token']))
                    ->withErrors(['email' => $message])
                    // Tells the sign-in page this is a bounce, so it can put the
                    // details back and try once more with the fresh token itself.
                    ->with('session_expired_retry', true);
            }

            $signedOut = ! $request->user();
            $expiredMessage = sprintf(
                'You were signed out after %d minutes of inactivity. Sign in again to continue.',
                (int) config('session.idle_timeout', 60)
            );

            // Scripts: the assignment and assessment screens detect an expired
            // session by the 419 status and handle it themselves, so it is kept
            // for them. Signing out and the activity heartbeat are different:
            // when the session is already gone, that is simply "signed out".
            if ($request->expectsJson() || $request->ajax()) {
                if ($signedOut && $request->routeIs('logout', 'session.activity')) {
                    return response()->json([
                        'message' => $expiredMessage,
                        'session_expired' => true,
                        'login_url' => route('login', ['expired' => 1]),
                    ], 401);
                }

                return null;
            }

            // A page left open past the session: the form is still refused,
            // but the person lands on a working page instead of "Page Expired".
            if ($signedOut) {
                return redirect()->route('login', ['expired' => 1])
                    ->with('status', $expiredMessage);
            }

            return redirect()->back()
                ->withInput($request->except(['password', 'password_confirmation', 'current_password', '_token']))
                ->with('error', 'This page was open for too long, so it was not sent. Please try again.');
        });
    })->create();
