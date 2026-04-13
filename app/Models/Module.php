<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['title', 'description', 'order_index']; 

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order_index', 'asc');
    }
}
