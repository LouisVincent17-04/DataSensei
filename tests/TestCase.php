<?php

namespace Tests;

use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates a saved user with the given role.
     *
     * Feature tests need a real row (workspaces, nodes and applications all
     * carry a user_id foreign key), so this always persists.
     *
     * @param  array<string, mixed>  $attributes
     */
    protected function roleUser(int $role = User::ROLE_USER, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
        ], $attributes));
    }

    /**
     * Signs the user in the way the application does.
     *
     * Logging in alone is not enough: EnsureActiveUser compares a fingerprint
     * of the account's security state against the session on every request and
     * signs the user out when it is missing, so the fingerprint is seeded here.
     */
    protected function authenticateAs(User $user): static
    {
        $this->actingAs($user)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($user),
        ]);

        return $this;
    }
}
