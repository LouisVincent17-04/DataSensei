<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    public const YEAR_LEVELS = ['Year 1', 'Year 2', 'Year 3', 'Year 4'];

    protected $fillable = [
        'title',
        'description',
        'order_index',
        'year_level',
        'xp_reward',
        'is_boss',
        'has_coding_exercises',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'xp_reward' => 'integer',
        'is_boss' => 'boolean',
        'has_coding_exercises' => 'boolean',
    ];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index', 'asc');
    }

    /** The challenges fanned out from this module, one per level. */
    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class, 'module_id');
    }
}
