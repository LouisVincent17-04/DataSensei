<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChallengeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'target_audience',
        'description',
        'icon_svg',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
    ];

    public function challenges(): HasMany
    {
        return $this->hasMany(Challenge::class)
            ->orderBy('order_index')
            ->orderBy('version_no');
    }
}
