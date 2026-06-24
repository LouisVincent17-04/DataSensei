<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $primaryKey = 'rank_id';

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'rank_id',
        'rank_name',
        'exp_required',
    ];

    protected $casts = [
        'rank_id' => 'integer',
        'exp_required' => 'integer',
    ];

    public function scopeUnlockedForXp(Builder $query, int $xp): Builder
    {
        return $query
            ->where('exp_required', '<=', max(0, $xp))
            ->orderByDesc('exp_required');
    }

    public function scopeLockedForXp(Builder $query, int $xp): Builder
    {
        return $query
            ->where('exp_required', '>', max(0, $xp))
            ->orderBy('exp_required');
    }

    public static function currentForXp(int $xp): ?self
    {
        return static::query()
            ->unlockedForXp($xp)
            ->first()
            ?: static::query()->orderBy('exp_required')->first();
    }

    public static function nextForXp(int $xp): ?self
    {
        return static::query()
            ->lockedForXp($xp)
            ->first();
    }
}
