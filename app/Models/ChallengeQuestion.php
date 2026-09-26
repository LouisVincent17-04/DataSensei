<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeQuestion extends Model
{
    protected $fillable = [
        'challenge_id',
        'challenge_category_id',
        'question_text',
        'order_index',
        'image_path',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ChallengeCategory::class, 'challenge_category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ChallengeOption::class)
            ->orderBy('order_index')
            ->orderBy('id');
    }
}
