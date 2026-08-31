<?php

namespace App\Support;

use App\Models\User;

final class AuthSessionFingerprint
{
    public const SESSION_KEY = 'auth.password_fingerprint';

    public static function for(User $user): string
    {
        $securityState = implode('|', [
            (string) $user->getAuthPassword(),
            (string) $user->getRememberToken(),
            strtolower((string) $user->email),
            (string) $user->role,
            (string) ($user->status ?? 'active'),
            (string) ($user->institution_id ?? ''),
        ]);

        return hash_hmac(
            'sha256',
            $securityState,
            (string) config('app.key')
        );
    }
}
