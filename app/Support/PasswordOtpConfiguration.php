<?php

namespace App\Support;

use InvalidArgumentException;

final class PasswordOtpConfiguration
{
    public const MIN_LENGTH = 4;
    public const MAX_LENGTH = 9;

    public static function length(): int
    {
        $length = filter_var(
            config('password_otp.length', 6),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => self::MIN_LENGTH, 'max_range' => self::MAX_LENGTH]],
        );

        if ($length === false) {
            throw new InvalidArgumentException(
                'PASSWORD_OTP_LENGTH must be an integer from '.self::MIN_LENGTH.' through '.self::MAX_LENGTH.'.'
            );
        }

        return $length;
    }
}
