<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A student's progress in one class module (Instructor Module Library
 * version), DataSensei Updates 8: when they first opened it and when they
 * marked it complete. Class reports and Class Analytics read it.
 */
class ModuleLibraryProgress extends Model
{
    protected $table = 'module_library_progress';

    protected $fillable = [
        'user_id',
        'module_library_item_id',
        'class_id',
        'opened_at',
        'last_opened_at',
        'completed_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'module_library_item_id' => 'integer',
        'class_id' => 'integer',
        'opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(ModuleLibraryItem::class, 'module_library_item_id');
    }
}
