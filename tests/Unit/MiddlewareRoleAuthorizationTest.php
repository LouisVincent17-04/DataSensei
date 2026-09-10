<?php

namespace Tests\Unit;

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\InstitutionAdmin;
use App\Http\Middleware\InstructorMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\StudentMiddleware;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class MiddlewareRoleAuthorizationTest extends TestCase
{
    public function test_each_role_middleware_allows_its_intended_account(): void
    {
        $cases = [
            [new StudentMiddleware(), User::ROLE_USER, null],
            [new InstructorMiddleware(), User::ROLE_INSTRUCTOR, 1],
            [new SuperAdminMiddleware(), User::ROLE_SUPERADMIN, null],
            [new InstitutionAdmin(), User::ROLE_INSTITUTION_ADMIN, 1],
            [new AdminMiddleware(), User::ROLE_ADMIN, null],
            [new AdminMiddleware(), User::ROLE_SUPERADMIN, null],
        ];

        foreach ($cases as [$middleware, $role, $institutionId]) {
            $user = new User(['role' => $role, 'status' => 'active', 'institution_id' => $institutionId]);
            $request = $this->authenticatedRequest($user);
            $response = $middleware->handle($request, fn (): Response => new Response('allowed'));

            $this->assertSame(200, $response->getStatusCode());
            $this->assertSame('allowed', $response->getContent());
        }
    }

    public function test_each_role_middleware_rejects_the_wrong_account(): void
    {
        $cases = [
            [new StudentMiddleware(), User::ROLE_INSTRUCTOR, 1],
            [new InstructorMiddleware(), User::ROLE_USER, null],
            [new SuperAdminMiddleware(), User::ROLE_ADMIN, null],
            [new InstitutionAdmin(), User::ROLE_INSTITUTION_ADMIN, null],
            [new AdminMiddleware(), User::ROLE_USER, null],
        ];

        foreach ($cases as [$middleware, $role, $institutionId]) {
            $user = new User(['role' => $role, 'status' => 'active', 'institution_id' => $institutionId]);
            $request = $this->authenticatedRequest($user);

            try {
                $middleware->handle($request, fn (): Response => new Response('not allowed'));
                $this->fail($middleware::class.' must reject the supplied role.');
            } catch (HttpException $exception) {
                $this->assertSame(403, $exception->getStatusCode());
            }
        }
    }

    public function test_generic_role_middleware_uses_exact_role_matching(): void
    {
        $student = new User(['role' => User::ROLE_USER, 'status' => 'active']);
        $request = $this->authenticatedRequest($student);
        $middleware = new RoleMiddleware();

        $allowed = $middleware->handle(
            $request,
            fn (): Response => new Response('allowed'),
            (string) User::ROLE_USER
        );
        $this->assertSame(200, $allowed->getStatusCode());

        try {
            $middleware->handle(
                $request,
                fn (): Response => new Response('not allowed'),
                User::ROLE_ADMIN
            );
            $this->fail('A different role must be rejected.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }

    public function test_admin_middleware_redirects_an_unauthenticated_request_to_login(): void
    {
        Auth::forgetGuards();
        $request = Request::create('/admin', 'GET');
        $response = (new AdminMiddleware())->handle(
            $request,
            fn (): Response => new Response('not allowed')
        );

        $this->assertTrue($response->isRedirect(route('login')));
    }

    private function authenticatedRequest(User $user): Request
    {
        Auth::guard('web')->setUser($user);
        $request = Request::create('/protected', 'GET');
        $request->setUserResolver(static fn (): User => $user);

        return $request;
    }
}
