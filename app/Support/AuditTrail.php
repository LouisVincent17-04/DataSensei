<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Remembers the records created while one staff action runs (DataSensei
 * Updates 8), so the audit log can name what "Created" created. The
 * RecordStaffActions middleware starts and ends it around the request; an
 * Eloquent "created" listener (AppServiceProvider) reports each new model.
 * Outside a staff action it records nothing.
 */
final class AuditTrail
{
    private bool $active = false;

    /** @var list<Model> */
    private array $created = [];

    public function begin(): void
    {
        $this->active = true;
        $this->created = [];
    }

    /** @return list<Model> the models created since begin() */
    public function end(): array
    {
        $created = $this->created;
        $this->active = false;
        $this->created = [];

        return $created;
    }

    public function noteCreated(mixed $model): void
    {
        if ($this->active && $model instanceof Model && count($this->created) < 200) {
            $this->created[] = $model;
        }
    }
}
