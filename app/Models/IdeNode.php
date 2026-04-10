<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IdeNode extends Model
{
    protected $fillable = [
        'workspace_id',
        'parent_id',
        'user_id',
        'type',
        'name',
        'content',
        'language',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(IdeWorkspace::class, 'workspace_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(IdeNode::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(IdeNode::class, 'parent_id')
                    ->orderBy('type', 'desc') // folders first
                    ->orderBy('name');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function executionLogs(): HasMany
    {
        return $this->hasMany(IdeExecutionLog::class, 'node_id');
    }

    public function isFile(): bool
    {
        return $this->type === 'file';
    }

    public function isFolder(): bool
    {
        return $this->type === 'folder';
    }

    /**
     * Recursively load all children as a nested tree.
     */
    public function toTree(): array
    {
        $data = $this->toArray();
        $data['children'] = $this->children->map(fn($child) => $child->toTree())->toArray();
        return $data;
    }
}