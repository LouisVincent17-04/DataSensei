<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One important action an admin, super admin, institution admin or
 * instructor performed (DataSensei Updates 8). Written by
 * App\Http\Middleware\RecordStaffActions; read by Admin Reports > Audit Logs.
 * The user's name and role are copied in, so the record stays readable after
 * the account changes or is removed.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;

    /** Action keys and how the report names them. */
    public const ACTIONS = [
        'created' => 'Created',
        'edited' => 'Edited',
        'deleted' => 'Deleted',
        'published' => 'Published',
        'unpublished' => 'Unpublished',
        'assigned' => 'Assigned',
        'unassigned' => 'Removed assignment',
        'duplicated' => 'Duplicated',
        'archived' => 'Archived',
        'restored' => 'Restored',
        'closed' => 'Closed',
        'graded' => 'Graded',
        'enrolled' => 'Added students',
        'removed_students' => 'Removed students',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'status_changed' => 'Changed account status',
        'role_changed' => 'Changed role',
        'reordered' => 'Reordered',
        'configured' => 'Changed configuration',
        'synced' => 'Synced',
        // Certificates (DataSensei Updates 13)
        'activated' => 'Activated',
        'deactivated' => 'Deactivated',
        'issued' => 'Issued certificate',
        'revoked' => 'Revoked certificate',
        'reissued' => 'Reissued certificate',
    ];

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'record_type',
        'record_id',
        'record_label',
        'details',
        'created_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'user_role' => 'integer',
        'record_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionLabel(): string
    {
        return self::ACTIONS[$this->action] ?? ucfirst(str_replace('_', ' ', (string) $this->action));
    }

    public function roleLabel(): string
    {
        return User::ROLE_LABELS[(int) $this->user_role] ?? 'Unknown';
    }
}
