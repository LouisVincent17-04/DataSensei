<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDataToolkitActivity extends Model
{
    protected $fillable = [
        'user_id',
        'dataset_key',
        'activity_type',
        'selected_columns',
        'result_summary',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'selected_columns' => 'array',
        'result_summary' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
