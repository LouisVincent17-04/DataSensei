<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    protected $fillable = [
        'challenge_category_id',
        'title',
        'description',
        'time_limit_seconds',
        'base_xp',
        'order_index',
        'is_coding_challenge',
    ];

    protected $casts = [
        'is_coding_challenge' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChallengeCategory::class, 'challenge_category_id');
    }

    /** MCQ questions (existing system) */
    public function questions(): HasMany
    {
        return $this->hasMany(ChallengeQuestion::class);
    }

    /** Coding questions (new system) */
    public function codingQuestions(): HasMany
    {
        return $this->hasMany(CodingQuestion::class)->orderBy('order_index');
    }
}