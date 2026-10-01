<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A certificate issued to one learner (DataSensei Updates 12 and 13).
 *
 * Issued only by App\Services\CertificateService after a server-side
 * eligibility check. It records the requirement version it was issued under
 * and a snapshot of the certificate as issued (title, statement, holder,
 * issuer, signatory, layout, requirements), so later changes to the
 * certificate's settings or requirements never change it.
 *
 * A certificate is active or revoked. Only an administrator revokes or
 * reissues one, with a reason; a reissue revokes the old copy and issues a
 * new one with a new certificate ID. A learner holds at most one active copy
 * of a certificate (unique user / certificate / active_slot, with
 * active_slot NULL once revoked). Certificate IDs are never reused.
 */
class UserCertificate extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'user_id',
        'certificate_definition_id',
        'certificate_key',
        'certificate_number',
        'requirement_set_id',
        'requirement_version',
        'snapshot',
        'issued_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'certificate_definition_id' => 'integer',
        'requirement_set_id' => 'integer',
        'requirement_version' => 'integer',
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
        'revoked_by' => 'integer',
        'reissued_from_id' => 'integer',
        'class_id' => 'integer',
        'issued_by' => 'integer',
        'active_slot' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(CertificateDefinition::class, 'certificate_definition_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(fn (Builder $q) => $q->whereNull('status')->orWhere('status', self::STATUS_ACTIVE));
    }

    public function isRevoked(): bool
    {
        return $this->getAttribute('status') === self::STATUS_REVOKED;
    }

    /** The certificate exactly as it was issued. */
    public function snapshotData(): array
    {
        $decoded = json_decode((string) $this->getAttribute('snapshot'), true);

        return is_array($decoded) ? $decoded : [];
    }
}
