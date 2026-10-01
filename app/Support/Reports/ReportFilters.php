<?php

namespace App\Support\Reports;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * The filters one report page was opened with (DataSensei Updates 8).
 *
 * Every report reads the same query-string names: from, to (dates), q
 * (search), class_id, module_id, student_id, status, role, type and action.
 * A report only shows, and only applies, the ones that make sense for it.
 */
final class ReportFilters
{
    public function __construct(
        public readonly ?CarbonImmutable $from = null,
        public readonly ?CarbonImmutable $to = null,
        public readonly string $search = '',
        public readonly ?int $classId = null,
        public readonly ?int $moduleId = null,
        public readonly ?int $studentId = null,
        public readonly string $status = '',
        public readonly string $role = '',
        public readonly string $type = '',
        public readonly string $action = '',
    ) {
    }

    /** Validation rules for the report filters. */
    public static function rules(): array
    {
        return [
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'q' => ['nullable', 'string', 'max:100'],
            'class_id' => ['nullable', 'integer', 'min:1'],
            'module_id' => ['nullable', 'integer', 'min:1'],
            'student_id' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'max:40'],
            'role' => ['nullable', 'string', 'max:40'],
            'type' => ['nullable', 'string', 'max:40'],
            'action' => ['nullable', 'string', 'max:40'],
        ];
    }

    public static function fromRequest(Request $request): self
    {
        $data = $request->validate(self::rules());

        return new self(
            from: isset($data['from']) ? CarbonImmutable::createFromFormat('Y-m-d', $data['from'])->startOfDay() : null,
            to: isset($data['to']) ? CarbonImmutable::createFromFormat('Y-m-d', $data['to'])->endOfDay() : null,
            search: trim((string) ($data['q'] ?? '')),
            classId: isset($data['class_id']) ? (int) $data['class_id'] : null,
            moduleId: isset($data['module_id']) ? (int) $data['module_id'] : null,
            studentId: isset($data['student_id']) ? (int) $data['student_id'] : null,
            status: trim((string) ($data['status'] ?? '')),
            role: trim((string) ($data['role'] ?? '')),
            type: trim((string) ($data['type'] ?? '')),
            action: trim((string) ($data['action'] ?? '')),
        );
    }

    public function hasDateRange(): bool
    {
        return $this->from !== null || $this->to !== null;
    }

    /** True when the date is inside the chosen range (or no range is set). */
    public function inRange(mixed $date): bool
    {
        if (! $this->hasDateRange()) {
            return true;
        }

        if ($date === null || $date === '') {
            return false;
        }

        $moment = CarbonImmutable::parse($date);

        return ($this->from === null || $moment->greaterThanOrEqualTo($this->from))
            && ($this->to === null || $moment->lessThanOrEqualTo($this->to));
    }

    /**
     * Limits a query to the date range on one column.
     *
     * @template T of \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     *
     * @param  T  $query
     * @return T
     */
    public function applyDate($query, string $column)
    {
        if ($this->from !== null) {
            $query->where($column, '>=', $this->from->toDateTimeString());
        }

        if ($this->to !== null) {
            $query->where($column, '<=', $this->to->toDateTimeString());
        }

        return $query;
    }

    /** The filters as query-string values (for links and exports). */
    public function query(): array
    {
        return array_filter([
            'from' => $this->from?->format('Y-m-d'),
            'to' => $this->to?->format('Y-m-d'),
            'q' => $this->search,
            'class_id' => $this->classId,
            'module_id' => $this->moduleId,
            'student_id' => $this->studentId,
            'status' => $this->status,
            'role' => $this->role,
            'type' => $this->type,
            'action' => $this->action,
        ], fn ($value) => $value !== null && $value !== '');
    }

    /** "From Sep 1, 2026 to Sep 28, 2026", or null without a range. */
    public function rangeText(): ?string
    {
        if (! $this->hasDateRange()) {
            return null;
        }

        return match (true) {
            $this->from !== null && $this->to !== null => 'From '.$this->from->format('M j, Y').' to '.$this->to->format('M j, Y'),
            $this->from !== null => 'From '.$this->from->format('M j, Y'),
            default => 'Up to '.$this->to->format('M j, Y'),
        };
    }

    /** A LIKE pattern for the search text. */
    public function like(): string
    {
        return '%'.$this->search.'%';
    }
}
