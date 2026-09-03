<?php

namespace App\Http\Requests\Auth;

use App\Support\PasswordOtpConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'otp' => preg_replace('/\D+/', '', (string) $this->input('otp')),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'max:191'],
            'otp' => ['required', 'digits:'.PasswordOtpConfiguration::length()],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.digits' => 'Enter the complete '.PasswordOtpConfiguration::length().'-digit verification code.',
        ];
    }
}
