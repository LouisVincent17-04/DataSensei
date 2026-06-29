# DataSensei OTP Password Reset Integration

This package adds a secure six-digit OTP password-reset flow to the existing DataSensei Laravel 12 application without replacing the current authentication system.

## Codebase integration summary

The implementation was integrated with the existing:

- `AuthController` login and registration flow
- `User` model and role system
- guest and authenticated route groups in `routes/web.php`
- profile password-change flow in `ProfileController`
- Laravel mail configuration in `config/mail.php`
- database queue configuration and existing `jobs` migration
- DataSensei authentication branding and dark UI design
- MySQL-backed application database

No existing route names, role middleware, authentication guard, registration flow, or login logic were removed.

## Security controls

- OTP is generated with `random_int` and is always six digits.
- OTP is stored only as a Laravel password hash.
- OTP expires after five minutes by default.
- OTP becomes unusable after successful verification.
- A successful verification creates a separate 256-bit reset authorization token; only its SHA-256 hash is stored.
- New OTP generation invalidates all previous active OTPs for the user.
- Successful password reset invalidates all OTP records for the user.
- Request cooldown is enforced through Laravel's rate limiter.
- HTTP request, verification, and reset endpoints have named route rate limiters.
- Each OTP has a database-backed verification-attempt limit.
- Responses do not reveal whether an email address exists.
- Queue payloads containing OTP mail data are encrypted using Laravel's `ShouldBeEncrypted` contract.
- Passwords require mixed case, numbers, and symbols.
- Remember tokens are rotated after reset.
- Existing database sessions are revoked after reset when `SESSION_DRIVER=database`.
- Security events are logged using fingerprints rather than full email addresses, OTPs, or reset tokens.

## New files

- `config/password_otp.php`
- `database/migrations/2026_06_29_000001_create_password_reset_otps_table.php`
- `app/Models/PasswordResetOtp.php`
- `app/Services/PasswordResetOtpService.php`
- `app/Mail/PasswordResetOtpMail.php`
- `app/Http/Controllers/PasswordResetOtpController.php`
- `app/Http/Requests/Auth/SendPasswordOtpRequest.php`
- `app/Http/Requests/Auth/VerifyPasswordOtpRequest.php`
- `app/Http/Requests/Auth/ResetPasswordWithOtpRequest.php`
- `resources/views/layouts/auth-reset.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/verify-password-otp.blade.php`
- `resources/views/auth/reset-password-otp.blade.php`
- `resources/views/emails/auth/password-reset-otp.blade.php`
- `tests/Feature/PasswordResetOtpTest.php`

## Modified files

- `routes/web.php`
- `routes/console.php`
- `app/Providers/AppServiceProvider.php`
- `app/Models/User.php`
- `app/Http/Controllers/ProfileController.php`
- `resources/views/student/profile.blade.php`
- `.env.example`

## Installation

Copy the package files into the root of the DataSensei project, preserving the directory structure. Then run:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan queue:work
```

For production, keep a queue worker running continuously and configure the Laravel scheduler:

```text
* * * * * cd /path/to/datasensei && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler removes old consumed or expired OTP records.

## Email configuration

The implementation reuses Laravel's existing mail configuration. Configure your own provider in `.env`. Example placeholders:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Do not commit the real `.env` file or SMTP credentials.

When `MAIL_MAILER=log`, the email content is written to the Laravel log and is suitable only for local testing. Use SMTP or another production mail transport for deployment.

## OTP settings

```env
PASSWORD_OTP_EXPIRES_MINUTES=5
PASSWORD_OTP_COOLDOWN_SECONDS=60
PASSWORD_OTP_MAX_VERIFY_ATTEMPTS=5
PASSWORD_OTP_RESET_WINDOW_MINUTES=10
PASSWORD_OTP_DELETE_AFTER_HOURS=24
PASSWORD_OTP_REQUESTS_PER_MINUTE=3
PASSWORD_OTP_REQUESTS_PER_HOUR=10
PASSWORD_OTP_VERIFY_PER_MINUTE=10
PASSWORD_OTP_RESET_PER_MINUTE=5
PASSWORD_RESET_MIN_LENGTH=8
PASSWORD_RESET_CHECK_COMPROMISED=false
```

Set `PASSWORD_RESET_CHECK_COMPROMISED=true` only when the production server can reach the external compromised-password service used by Laravel's `uncompromised` password rule.

## Session security

Database sessions are recommended because they allow DataSensei to revoke active sessions after a forgotten-password reset:

```env
SESSION_DRIVER=database
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Use `SESSION_SECURE_COOKIE=true` only when the application is served through HTTPS.

## Routes added

| Method | URI | Route name |
|---|---|---|
| GET | `/forgot-password` | `password.request` |
| POST | `/forgot-password` | `password.otp.send` |
| GET | `/forgot-password/verify` | `password.otp.verify.form` |
| POST | `/forgot-password/verify` | `password.otp.verify` |
| GET | `/reset-password` | `password.reset.form` |
| POST | `/reset-password` | `password.otp.reset` |

## Flow

1. User submits the registered email address.
2. DataSensei always returns a generic response.
3. For an active account, prior OTP records are invalidated and a new hashed OTP record is created.
4. An encrypted queued mailable sends the six-digit OTP.
5. The user verifies the OTP within five minutes.
6. DataSensei marks the OTP as verified and creates a short-lived reset authorization token.
7. The user submits and confirms a strong new password.
8. DataSensei updates the password, rotates the remember token, invalidates all OTPs, and revokes database sessions when supported.

## Validation performed in the build environment

- PHP syntax lint passed for all application, configuration, migration, and route PHP files.
- All OTP Blade templates compiled successfully with Laravel's Blade compiler.
- All named routes referenced by application and Blade files were found.
- Password routes were confirmed with the expected `guest`, `auth`, and named throttle middleware.
- The queued OTP mailable was confirmed to use encrypted payloads and after-commit dispatch.

The full PHPUnit suite could not be executed in the build container because its PHP runtime does not include the DOM, mbstring, XML, XMLWriter, or PDO database-driver extensions. The feature test file is included for execution in the normal DataSensei development environment.
