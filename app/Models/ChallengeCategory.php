<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallengeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'target_audience',
        'description',
        'icon_svg',
        'order_index'
    ];
}