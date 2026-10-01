<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A certificate DataSensei can issue (DataSensei Updates 12 and 13).
 *
 * Two kinds share this table and the issuing code:
 *   system  the three core certificates for every learner (is_system = 1),
 *           identified by their certificate_key, never by their name
 *   class   a Certificate of Completion an instructor configures in the
 *           Certificate Builder for one of their classes (the whole class,
 *           never one module): one of the five predefined layouts, a
 *           statement and the signatory's title. The instructor issues it to
 *           the students who completed every required item of the class.
 *
 * Only an active definition issues new certificates. A class certificate
 * starts as a draft and is activated after its preview; every change to its
 * content raises config_version, and previewed_version records the version
 * last previewed. What a certificate requires is kept in versioned
 * requirement sets (certificate_requirement_sets).
 */
class CertificateDefinition extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected $fillable = [
        'certificate_key',
        'name',
        'description',
        'audience',
        'is_system',
        'is_active',
        'current_version',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'current_version' => 'integer',
        'owner_user_id' => 'integer',
        'class_id' => 'integer',
        'config_version' => 'integer',
        'previewed_version' => 'integer',
        'activated_at' => 'datetime',
    ];

    public function issued(): HasMany
    {
        return $this->hasMany(UserCertificate::class, 'certificate_definition_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function scopeClassCertificates(Builder $query): Builder
    {
        return $query->where('is_system', false);
    }

    public function isClassCertificate(): bool
    {
        return ! $this->is_system;
    }

    /** Whether it may issue new certificates now. */
    public function issues(): bool
    {
        return $this->is_system
            ? (bool) $this->is_active
            : $this->status === self::STATUS_ACTIVE;
    }

    public function statusLabel(): string
    {
        if ($this->is_system) {
            return $this->is_active ? 'Active' : 'Inactive';
        }

        return self::STATUSES[$this->status] ?? 'Draft';
    }
}
