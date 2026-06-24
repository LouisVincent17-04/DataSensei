<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableOfSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'module_no',
        'title',
        'status',
        'created_by',
    ];

    protected $casts = [
        'class_id' => 'integer',
        'module_no' => 'integer',
        'created_by' => 'integer',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rows(): HasMany
    {
        return $this->hasMany(TableOfSpecificationRow::class, 'table_of_specification_id');
    }
}
