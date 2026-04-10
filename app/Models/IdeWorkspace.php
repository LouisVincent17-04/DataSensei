<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdeWorkspace extends Model
{
    protected $fillable = ['user_id', 'name', 'description'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function nodes(): HasMany
    {
        return $this->hasMany(IdeNode::class, 'workspace_id');
    }

    public function rootNodes(): HasMany
    {
        return $this->hasMany(IdeNode::class, 'workspace_id')
                    ->whereNull('parent_id')
                    ->orderBy('type', 'desc') // folders first
                    ->orderBy('name');
    }
}