<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordWithOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $passwordRule = Password::min((int) config('password_otp.password_min_length', 8))
            ->mixedCase()
            ->numbers()
            ->symbols();

        if (config('password_otp.check_compromised_passwords', false)) {
            $passwordRule->uncompromised();
        }

        return [
            'password' => ['required', 'confirmed', $passwordRule],
        ];
    }
}
