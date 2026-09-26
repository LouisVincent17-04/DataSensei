<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A challenge (MCQ or coding) an instructor has given to one of their classes.
 *
 * The challenge itself lives in the challenges table, so students take it with
 * the same quiz and coding screens as platform challenges; this row decides
 * which class can see it and when.
 */
class ClassChallengeAssignment extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [self::STATUS_DRAFT, self::STATUS_PUBLISHED, self::STATUS_CLOSED];

    protected $fillable = [
        'class_id',
        'challenge_id',
        'assigned_by',
        'title',
        'instructions',
        'available_at',
        'due_at',
        'status',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class, 'challenge_id');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /** Published and inside its window, if one was set. */
    public function scopeOpenNow(Builder $query): Builder
    {
        $now = now();

        return $query->published()
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('available_at')->orWhere('available_at', '<=', $now);
            })
            ->where(function (Builder $builder) use ($now): void {
                $builder->whereNull('due_at')->orWhere('due_at', '>=', $now);
            });
    }

    public function isOpenNow(): bool
    {
        if ($this->status !== self::STATUS_PUBLISHED) {
            return false;
        }

        $now = now();

        if ($this->available_at && $this->available_at->gt($now)) {
            return false;
        }

        return ! ($this->due_at && $this->due_at->lt($now));
    }
}
