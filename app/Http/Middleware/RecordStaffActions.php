<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditActions;
use App\Support\AuditTrail;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Writes the audit log (DataSensei Updates 8).
 *
 * After an admin, super admin, institution admin or instructor request that
 * AuditActions lists has succeeded, one row is written: who (name and role),
 * the action (created, edited, deleted, published, unpublished, assigned,
 * removed assignment, ...), the affected record and when. A request that
 * failed (an error status, validation errors or an error message sent back)
 * writes nothing. Recording never changes or breaks the request itself.
 */
class RecordStaffActions
{
    private const STAFF_ROLES = [
        User::ROLE_ADMIN,
        User::ROLE_SUPERADMIN,
        User::ROLE_INSTRUCTOR,
        User::ROLE_INSTITUTION_ADMIN,
    ];

    public function __construct(private readonly AuditTrail $trail)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethodSafe()) {
            return $next($request);
        }

        $route = $request->route();
        $definition = $route instanceof Route ? AuditActions::definition((string) $route->getName()) : null;
        $user = $request->user();

        if ($definition === null || ! $user instanceof User || ! in_array((int) $user->role, self::STAFF_ROLES, true)) {
            return $next($request);
        }

        // The record as it was before the request: its name survives a
        // delete, and a status toggle is compared with it afterwards.
        $record = isset($definition['param']) ? $route->parameter($definition['param']) : null;
        $record = $record instanceof Model ? $record : null;
        $label = $record ? AuditActions::labelFor($record) : null;
        $toggleBefore = $record && isset($definition['toggle']) ? $record->getAttribute($definition['toggle'][0]) : null;

        $this->trail->begin();

        try {
            $response = $next($request);
        } finally {
            $created = $this->trail->end();
        }

        try {
            if ($this->succeeded($request, $response)) {
                $this->write($request, $user, $definition, $record, $label, $toggleBefore, $created);
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return $response;
    }

    private function succeeded(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() >= 400) {
            return false;
        }

        if ($request->hasSession()) {
            $flashed = (array) $request->session()->get('_flash.new', []);
            if (in_array('errors', $flashed, true) || in_array('error', $flashed, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  list<Model>  $created
     */
    private function write(Request $request, User $user, array $definition, ?Model $record, ?string $label, mixed $toggleBefore, array $created): void
    {
        $action = (string) ($definition['action'] ?? 'edited');
        $details = [];

        $newRecord = null;
        if (isset($definition['creates'])) {
            foreach ($created as $model) {
                if ($model instanceof $definition['creates']) {
                    $newRecord = $model;
                    break;
                }
            }
        }

        if (($definition['intent'] ?? false) === true) {
            $intent = (string) $request->input('intent', '');
            if ($action === 'created') {
                $details[] = match ($intent) {
                    'publish' => 'Published',
                    'draft' => 'Saved as a draft',
                    default => null,
                };
            } else {
                $action = match ($intent) {
                    'publish' => 'published',
                    'unpublish' => 'unpublished',
                    default => $action,
                };
            }
        }

        if (isset($definition['toggle']) && $record) {
            [$attribute, $onAction, $offAction, $onValue] = $definition['toggle'];
            $after = $record->fresh()?->getAttribute($attribute);
            if ($after === null || $this->same($after, $toggleBefore)) {
                return;
            }
            $action = $this->same($after, $onValue) ? $onAction : $offAction;
        }

        $subject = $record ?? $newRecord;

        if ($action === 'duplicated' && $newRecord) {
            $details[] = 'New copy: '.AuditActions::labelFor($newRecord).' (#'.$newRecord->getKey().')';
            $subject = $record ?? $newRecord;
        }

        if (isset($definition['label'])) {
            $label = (string) ($definition['label'])($request, $subject);
        } elseif ($record !== null && $action !== 'deleted') {
            // The record's name after the change; a rename keeps the old one.
            $fresh = $record->fresh();
            $current = $fresh ? AuditActions::labelFor($fresh) : (string) $label;
            if ($label !== null && $label !== '' && $current !== $label) {
                $details[] = 'Previously: '.$label.'.';
            }
            $label = $current;
        } elseif ($label === null || $label === '') {
            $label = AuditActions::labelFor($subject);
        }

        if (isset($definition['details'])) {
            $details[] = ($definition['details'])($request, $subject);
        }

        $details = trim(implode(' ', array_filter(array_map(fn ($d) => is_string($d) ? trim($d) : null, $details))));

        AuditLog::query()->create([
            'user_id' => $user->id,
            'user_name' => Str::limit((string) $user->name, 185, ''),
            'user_role' => (int) $user->role,
            'action' => $action,
            'record_type' => (string) $definition['type'],
            'record_id' => $subject?->getKey(),
            'record_label' => $label !== '' ? Str::limit($label, 250) : null,
            'details' => $details !== '' ? Str::limit($details, 495) : null,
            'created_at' => now(),
        ]);
    }

    /** Compares stored values: true, 1 and "1" are the same. */
    private function same(mixed $a, mixed $b): bool
    {
        $normalize = fn (mixed $value): string => is_bool($value) ? (string) (int) $value : (string) $value;

        return $normalize($a) === $normalize($b);
    }
}
