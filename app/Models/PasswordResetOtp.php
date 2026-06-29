<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\MassPrunable;

class PasswordResetOtp extends Model
{
    use HasFactory, MassPrunable;

    public const PURPOSE_PASSWORD_RESET = 'password_reset';

    protected $fillable = [
        'user_id',
        'purpose',
        'otp_hash',
        'attempts',
        'max_attempts',
        'expires_at',
        'sent_at',
        'verified_at',
        'consumed_at',
        'reset_token_hash',
        'reset_token_expires_at',
        'request_ip_hash',
        'user_agent_hash',
    ];

    protected $hidden = [
        'otp_hash',
        'reset_token_hash',
        'request_ip_hash',
        'user_agent_hash',
    ];

    protected function casts(): array
    {
        return [
            'attempts' => 'integer',
            'max_attempts' => 'integer',
            'expires_at' => 'datetime',
            'sent_at' => 'datetime',
            'verified_at' => 'datetime',
            'consumed_at' => 'datetime',
            'reset_token_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function prunable(): Builder
    {
        $cutoff = now()->subHours((int) config('password_otp.delete_after_hours', 24));

        return static::query()->where(function (Builder $query) use ($cutoff): void {
            $query->where('expires_at', '<', $cutoff)
                ->orWhere('consumed_at', '<', $cutoff);
        });
    }
}
