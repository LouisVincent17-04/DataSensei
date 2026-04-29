<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestCase extends Model
{
    protected $fillable = [
        'coding_question_id',
        'input',
        'expected_output',
        'is_hidden',
        'order_index',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function codingQuestion(): BelongsTo
    {
        return $this->belongsTo(CodingQuestion::class);
    }
}