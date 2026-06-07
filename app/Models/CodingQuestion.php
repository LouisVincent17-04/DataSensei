<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CodingQuestion extends Model
{
    protected $fillable = [
        'challenge_id',
        'problem_description',
        'language',
        'starter_code',
        'order_index',
        'time_limit_seconds',
        'base_xp',
        'source_requirements',
    ];

    protected $casts = [
        'source_requirements' => 'array',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function testCases(): HasMany
    {
        return $this->hasMany(TestCase::class)->orderBy('order_index');
    }

    public function visibleTestCases(): HasMany
    {
        return $this->hasMany(TestCase::class)
            ->where('is_hidden', false)
            ->orderBy('order_index');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(CodingSubmission::class);
    }
}