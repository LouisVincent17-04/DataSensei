<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DatasetVersion extends Model
{
    protected $hidden = ['storage_path', 'checksum_sha256'];

    protected $fillable = [
        'dataset_id', 'user_dataset_id', 'created_by', 'version_number', 'version_label',
        'checksum_sha256', 'storage_path', 'row_count', 'column_count', 'schema_profile', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'dataset_id' => 'integer',
            'user_dataset_id' => 'integer',
            'created_by' => 'integer',
            'version_number' => 'integer',
            'row_count' => 'integer',
            'column_count' => 'integer',
            'schema_profile' => 'array',
            'metadata' => 'array',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(MlDataset::class, 'dataset_id');
    }

    public function userDataset(): BelongsTo
    {
        return $this->belongsTo(UserDataset::class, 'user_dataset_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function modelVersions(): HasMany
    {
        return $this->hasMany(ModelVersion::class, 'dataset_version_id');
    }
}
