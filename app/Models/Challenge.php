<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Challenge extends Model
{
    protected $fillable = [
        'challenge_category_id',
        'content_code',
        'title',
        'description',
        'time_limit_seconds',
        'base_xp',
        'order_index',
        'is_coding_challenge',
        'version_no',
        'version_name',
        'version_code',
        'is_active',
    ];

    protected $casts = [
        'time_limit_seconds' => 'integer',
        'base_xp' => 'integer',
        'order_index' => 'integer',
        'is_coding_challenge' => 'boolean',
        'version_no' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Challenge $challenge): void {
            $challenge->version_no = max(1, (int) ($challenge->version_no ?: 1));
            $challenge->version_name = filled($challenge->version_name)
                ? trim((string) $challenge->version_name)
                : 'Version ' . $challenge->version_no;
            $challenge->version_code = filled($challenge->version_code)
                ? strtoupper(trim((string) $challenge->version_code))
                : 'V' . $challenge->version_no;
            $challenge->is_active = $challenge->is_active ?? true;

            if (blank($challenge->content_code)) {
                $type = $challenge->is_coding_challenge ? 'CODE' : 'MCQ';
                $identity = implode('|', [
                    (string) $challenge->challenge_category_id,
                    $type,
                    (string) $challenge->title,
                ]);
                $slug = strtoupper(Str::slug((string) $challenge->title, '-')) ?: 'CHALLENGE';
                $prefix = 'C' . (int) $challenge->challenge_category_id . '-' . $type . '-';
                $suffix = '-' . strtoupper(substr(sha1($identity), 0, 8));
                $available = max(1, 64 - strlen($prefix) - strlen($suffix));

                $challenge->content_code = $prefix . substr($slug, 0, $available) . $suffix;
            } else {
                $challenge->content_code = strtoupper(trim((string) $challenge->content_code));
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChallengeCategory::class, 'challenge_category_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ChallengeQuestion::class)
            ->orderBy('order_index')
            ->orderBy('id');
    }

    public function codingQuestions(): HasMany
    {
        return $this->hasMany(CodingQuestion::class)->orderBy('order_index');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ChallengeAttempt::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeMcq(Builder $query): Builder
    {
        return $query->where('is_coding_challenge', false);
    }

    public function scopeCoding(Builder $query): Builder
    {
        return $query->where('is_coding_challenge', true);
    }
}
