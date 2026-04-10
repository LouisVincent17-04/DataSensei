<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IdeExecutionLog extends Model
{
    protected $fillable = [
        'node_id',
        'user_id',
        'output',
        'error',
        'exit_code',
        'execution_time_ms',
    ];

    protected $casts = [
        'exit_code'          => 'integer',
        'execution_time_ms'  => 'integer',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(IdeNode::class, 'node_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wasSuccessful(): bool
    {
        return $this->exit_code === 0;
    }
}