<?php

namespace App\Support;

use App\Models\User;

/**
 * A signed summary of the account details that decide what a session may do.
 * EnsureActiveUser signs a session out when it no longer matches: after a
 * password change, an e-mail or role change, a disabled account, or a staff
 * account moved to another institution.
 *
 * Two values were removed because they signed people out for no security
 * reason, which students reported as "the system logs me out quickly":
 *
 *  - The remember-me token. Laravel replaces it on every sign-out, so signing
 *    out on a phone or a lab PC ended the same account's session everywhere
 *    else. Every place that deliberately rotates it (password change or
 *    reset, disabling an account) also changes the password or status,
 *    which are still part of the fingerprint, and remember-me cookies are
 *    still checked against the token by Laravel itself.
 *  - A student's institution. It is filled in automatically the first time
 *    an instructor enrolls the student in a class, which signed the student
 *    out mid-session. A student's access comes from class enrollment, which
 *    is checked on every request. For instructors and institution admins the
 *    institution decides what they can manage, so it is kept for them.
 */
final class AuthSessionFingerprint
{
    public const SESSION_KEY = 'auth.password_fingerprint';

    public static function for(User $user): string
    {
        $institutionScoped = in_array((int) $user->role, [
            User::ROLE_INSTRUCTOR,
            User::ROLE_INSTITUTION_ADMIN,
        ], true);

        $securityState = implode('|', [
            (string) $user->getAuthPassword(),
            strtolower((string) $user->email),
            (string) $user->role,
            (string) ($user->status ?? 'active'),
            $institutionScoped ? (string) ($user->institution_id ?? '') : '',
        ]);

        return hash_hmac(
            'sha256',
            $securityState,
            (string) config('app.key')
        );
    }
}
