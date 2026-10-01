<?php

namespace App\Support;

use App\Models\AchievementDefinition;
use App\Models\AntiCheatSetting;
use App\Models\Assessment;
use App\Models\AssignmentLibraryItem;
use App\Models\Challenge;
use App\Models\ClassAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\TableOfSpecification;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Which admin and instructor requests go into the audit log, and how
 * (DataSensei Updates 8).
 *
 * Keyed by route name. Only these routes are recorded, and only when the
 * request succeeded. Previews, image uploads, test-case checks and
 * recalculations change nothing anyone needs to trace and are left out.
 *
 * A definition says:
 *   action   the action key (App\Models\AuditLog::ACTIONS)
 *   type     what kind of record it affects, as the report shows it
 *   param    the route parameter holding the affected record
 *   creates  the model class a "Created" request creates
 *   intent   the editor's intent (publish / unpublish) decides the action
 *   toggle   [attribute, on-action, off-action, on-value]: the action follows
 *            the attribute's new value, and nothing is logged if it did not
 *            change
 *   label    fn (Request, ?Model): string, when the record's name is not on
 *            the model (for example modules assigned to a class)
 *   details  fn (Request, ?Model): ?string, a short note
 */
final class AuditActions
{
    /** @return array<string, mixed>|null */
    public static function definition(string $routeName): ?array
    {
        return self::all()[$routeName] ?? null;
    }

    /** @return array<string, array<string, mixed>> */
    public static function all(): array
    {
        static $definitions = null;

        return $definitions ??= self::build();
    }

    /** @return array<string, array<string, mixed>> */
    private static function build(): array
    {
        $publishToggle = ['is_published', 'published', 'unpublished', true];
        $activeToggle = ['is_active', 'published', 'unpublished', true];

        $crud = function (string $prefix, string $type, string $param, string $model, array $extra = []) use ($activeToggle): array {
            return array_filter([
                $prefix.'.store' => ['action' => 'created', 'type' => $type, 'creates' => $model],
                $prefix.'.update' => ['action' => 'edited', 'type' => $type, 'param' => $param],
                $prefix.'.destroy' => ['action' => 'deleted', 'type' => $type, 'param' => $param],
                $prefix.'.status' => in_array('status', $extra, true) ? ['type' => $type, 'param' => $param, 'toggle' => $activeToggle] : null,
                $prefix.'.duplicate' => in_array('duplicate', $extra, true) ? ['action' => 'duplicated', 'type' => $type, 'param' => $param, 'creates' => $model] : null,
            ]);
        };

        $className = fn (Request $request) => (string) (ClassRoom::query()->whereKey((int) $request->input('class_id'))->value('name') ?? 'Class');

        return array_merge(
            // ── Admin: platform content ─────────────────────────────────
            [
                'admin.modules.store' => ['action' => 'created', 'type' => 'DataSensei Module', 'creates' => Module::class, 'intent' => true],
                'admin.modules.update' => ['action' => 'edited', 'type' => 'DataSensei Module', 'param' => 'module', 'intent' => true],
                'admin.modules.status' => ['type' => 'DataSensei Module', 'param' => 'module', 'toggle' => $publishToggle],
                'admin.modules.destroy' => ['action' => 'deleted', 'type' => 'DataSensei Module', 'param' => 'module'],
                'admin.modules.reorder' => ['action' => 'reordered', 'type' => 'DataSensei Module', 'label' => fn () => 'Curriculum order'],
                'admin.module-library.store' => ['action' => 'created', 'type' => 'Class Module', 'creates' => ModuleLibraryItem::class, 'intent' => true],
                'admin.module-library.update' => ['action' => 'edited', 'type' => 'Class Module', 'param' => 'module', 'intent' => true],
                'admin.module-library.status' => ['type' => 'Class Module', 'param' => 'module', 'toggle' => $activeToggle],
                'admin.module-library.destroy' => ['action' => 'deleted', 'type' => 'Class Module', 'param' => 'module'],
                'admin.module-library.duplicate' => ['action' => 'duplicated', 'type' => 'Class Module', 'param' => 'module', 'creates' => ModuleLibraryItem::class],
            ],
            $crud('admin.assessments', 'Assessment Content', 'assessment', AssignmentLibraryItem::class, ['status', 'duplicate']),
            $crud('admin.challenges', 'MCQ Challenge', 'challenge', Challenge::class, ['status', 'duplicate']),
            $crud('admin.coding-challenges', 'Coding Challenge', 'challenge', Challenge::class, ['status']),
            [
                'admin.challenge-maps.update' => ['action' => 'configured', 'type' => 'Challenge Level', 'param' => 'category'],
                'admin.challenge-maps.reorder' => ['action' => 'reordered', 'type' => 'Challenge Level', 'param' => 'category', 'details' => fn () => 'Challenge order on the map'],
                'admin.content.categories.update' => ['action' => 'configured', 'type' => 'Challenge Level', 'param' => 'category'],

                // ── Admin: gamification ────────────────────────────────
                'admin.gamification.achievements.destroy' => ['action' => 'deleted', 'type' => 'Achievement', 'param' => 'achievement'],
                'admin.gamification.achievements.sync' => ['action' => 'synced', 'type' => 'Achievement', 'label' => fn () => 'All students'],
                'admin.gamification.missions.update' => ['action' => 'edited', 'type' => 'Mission', 'param' => 'mission'],

                // ── Admin and super admin: accounts ────────────────────
                'admin.users.store' => ['action' => 'created', 'type' => 'User Account', 'creates' => User::class],
                'admin.users.update' => ['action' => 'edited', 'type' => 'User Account', 'param' => 'user'],
                'admin.users.status' => ['type' => 'User Account', 'param' => 'user', 'toggle' => ['status', 'status_changed', 'status_changed', 'active'], 'details' => fn (Request $r, ?Model $m) => self::accountStatus($m)],
                'superadmin.users.store' => ['action' => 'created', 'type' => 'User Account', 'creates' => User::class],
                'superadmin.users.update' => ['action' => 'edited', 'type' => 'User Account', 'param' => 'user'],
                'superadmin.users.toggleStatus' => ['type' => 'User Account', 'param' => 'user', 'toggle' => ['status', 'status_changed', 'status_changed', 'active'], 'details' => fn (Request $r, ?Model $m) => self::accountStatus($m)],
                'superadmin.users.promote' => ['action' => 'role_changed', 'type' => 'User Account', 'param' => 'user', 'details' => fn (Request $r, ?Model $m) => self::roleNote($m)],
                'superadmin.users.demote' => ['action' => 'role_changed', 'type' => 'User Account', 'param' => 'user', 'details' => fn (Request $r, ?Model $m) => self::roleNote($m)],
                'superadmin.users.assignInstitutionAdmin' => ['action' => 'role_changed', 'type' => 'User Account', 'param' => 'user', 'details' => fn (Request $r, ?Model $m) => self::roleNote($m)],
                'superadmin.institutions.store' => ['action' => 'created', 'type' => 'Institution', 'creates' => Institution::class],
                'superadmin.institutions.update' => ['action' => 'edited', 'type' => 'Institution', 'param' => 'institution'],
                'superadmin.institutions.destroy' => ['action' => 'deleted', 'type' => 'Institution', 'param' => 'institution'],
                'superadmin.institutions.toggleStatus' => ['action' => 'configured', 'type' => 'Institution', 'param' => 'institution', 'details' => fn (Request $r, ?Model $m) => $m ? 'Status: '.ucfirst((string) $m->fresh()?->status) : null],
                'institution-admin.applications.approve' => ['action' => 'approved', 'type' => 'Instructor Application', 'param' => 'application', 'label' => fn (Request $r, ?Model $m) => self::applicant($m)],
                'institution-admin.applications.reject' => ['action' => 'rejected', 'type' => 'Instructor Application', 'param' => 'application', 'label' => fn (Request $r, ?Model $m) => self::applicant($m)],

                // ── Instructor: classes ────────────────────────────────
                'instructor.classes.store' => ['action' => 'created', 'type' => 'Class', 'creates' => ClassRoom::class],
                'instructor.classes.update' => ['action' => 'edited', 'type' => 'Class', 'param' => 'class'],
                'instructor.classes.destroy' => ['action' => 'deleted', 'type' => 'Class', 'param' => 'class'],
                'instructor.classes.archive' => ['action' => 'archived', 'type' => 'Class', 'param' => 'class'],
                'instructor.classes.restore' => ['action' => 'restored', 'type' => 'Class', 'param' => 'class'],
                'instructor.classes.regenerate-code' => ['action' => 'configured', 'type' => 'Class', 'param' => 'class', 'details' => fn () => 'New class code'],
                'instructor.classes.students.add-by-email' => ['action' => 'enrolled', 'type' => 'Class', 'param' => 'class', 'details' => fn (Request $r) => 'Student: '.strtolower(trim((string) $r->input('email')))],
                'instructor.classes.students.remove' => ['action' => 'removed_students', 'type' => 'Class', 'param' => 'class', 'details' => fn (Request $r) => self::studentNote($r->route()?->parameter('student'))],
                'instructor.classes.students.remove-bulk' => ['action' => 'removed_students', 'type' => 'Class', 'param' => 'class', 'details' => fn (Request $r) => count((array) $r->input('student_ids', [])).' student(s)'],

                // ── Instructor: modules given to classes ───────────────
                'modules.module-library.assign' => [
                    'action' => 'assigned', 'type' => 'Class Module',
                    'label' => fn (Request $r) => self::moduleTitles(array_filter((array) $r->input('selected_modules', []))),
                    'details' => fn (Request $r) => 'Class: '.$className($r),
                ],
                'modules.module-library.unassign' => [
                    'action' => 'unassigned', 'type' => 'Class Module',
                    'label' => fn (Request $r) => self::moduleTitles([(int) $r->input('remove_module_id')]),
                    'details' => fn (Request $r) => 'Class: '.$className($r),
                ],

                // ── Instructor: assignments and assessments ────────────
                'instructor.assignments.store' => ['action' => 'created', 'type' => 'Assignment', 'creates' => ClassAssignment::class],
                'instructor.assignments.update' => ['action' => 'edited', 'type' => 'Assignment', 'param' => 'assignment'],
                'instructor.assignments.publish' => ['action' => 'published', 'type' => 'Assignment', 'param' => 'assignment'],
                'instructor.assignments.close' => ['action' => 'closed', 'type' => 'Assignment', 'param' => 'assignment'],
                'instructor.assignments.archive' => ['action' => 'archived', 'type' => 'Assignment', 'param' => 'assignment'],
                'instructor.assignments.destroy' => ['action' => 'deleted', 'type' => 'Assignment', 'param' => 'assignment'],
                'instructor.assignments.submissions.release' => ['action' => 'graded', 'type' => 'Assignment', 'param' => 'assignment', 'details' => fn (Request $r) => 'Released a held submission. '.self::studentNote($r->route()?->parameter('submission')?->student ?? null)],
                'instructor.assignments.submissions.keep-blocked' => ['action' => 'graded', 'type' => 'Assignment', 'param' => 'assignment', 'details' => fn (Request $r) => 'Kept a submission blocked. '.self::studentNote($r->route()?->parameter('submission')?->student ?? null)],
                'instructor.assessments.store' => ['action' => 'created', 'type' => 'Assessment', 'creates' => Assessment::class],
                'instructor.assessments.publish' => ['action' => 'published', 'type' => 'Assessment', 'param' => 'assessment'],
                'instructor.assessments.close' => ['action' => 'closed', 'type' => 'Assessment', 'param' => 'assessment'],
                'instructor.assessments.questions.update' => ['action' => 'edited', 'type' => 'Assessment', 'param' => 'assessment', 'details' => fn () => 'Edited a question'],
                'instructor.assessments.save' => ['action' => 'created', 'type' => 'Assessment', 'creates' => Assessment::class],
                'instructor.assessments.settings.update' => ['action' => 'edited', 'type' => 'Assessment', 'param' => 'assessment', 'details' => fn () => 'Changed the settings'],
                'instructor.assessments.questions.store' => ['action' => 'edited', 'type' => 'Assessment', 'param' => 'assessment', 'details' => fn () => 'Added a question'],
                'instructor.assessments.questions.destroy' => ['action' => 'edited', 'type' => 'Assessment', 'param' => 'assessment', 'details' => fn () => 'Removed a question'],
                'instructor.assessments.submissions.grade' => ['action' => 'graded', 'type' => 'Assessment', 'param' => 'assessment', 'details' => fn (Request $r) => self::studentNote($r->route()?->parameter('submission')?->student ?? null)],

                // ── Instructor: challenges ─────────────────────────────
                'instructor.challenge-builder.store' => ['action' => 'created', 'type' => 'Challenge', 'creates' => Challenge::class],
                'instructor.challenge-builder.update' => ['action' => 'edited', 'type' => 'Challenge', 'param' => 'challenge'],
                'instructor.challenge-builder.destroy' => ['action' => 'deleted', 'type' => 'Challenge', 'param' => 'challenge'],
                'instructor.challenges.classes.update' => ['action' => 'assigned', 'type' => 'Challenge', 'param' => 'challenge', 'details' => fn () => 'Changed the classes that can practice it'],

                // ── Instructor: settings and planning ──────────────────
                'instructor.anti-cheat.store' => ['action' => 'configured', 'type' => 'Anti-Cheat Setting', 'creates' => AntiCheatSetting::class],
                'instructor.anti-cheat.update' => ['action' => 'configured', 'type' => 'Anti-Cheat Setting', 'param' => 'setting'],
                'instructor.anti-cheat.destroy' => ['action' => 'deleted', 'type' => 'Anti-Cheat Setting', 'param' => 'setting'],
                'instructor.tos.store' => ['action' => 'created', 'type' => 'Table of Specifications', 'creates' => TableOfSpecification::class],
                'instructor.tos.destroy' => ['action' => 'deleted', 'type' => 'Table of Specifications', 'param' => 'tos'],
                'instructor.tos.distribution.update' => ['action' => 'edited', 'type' => 'Table of Specifications', 'param' => 'tos'],
                'instructor.tos.rows.update' => ['action' => 'edited', 'type' => 'Table of Specifications', 'param' => 'tos'],
                'instructor.tos.suggested-distribution' => ['action' => 'edited', 'type' => 'Table of Specifications', 'param' => 'tos'],
            ],
        );
    }

    /** The readable name of a record for the log. */
    public static function labelFor(?Model $model): string
    {
        if ($model === null) {
            return '';
        }

        if ($model instanceof User) {
            return trim($model->name.' ('.$model->email.')');
        }

        if ($model instanceof ModuleLibraryItem) {
            return trim($model->title.($model->version_name ? ', '.$model->version_name : ''));
        }

        if ($model instanceof ClassRoom) {
            return trim($model->name.($model->section ? ', '.$model->section : ''));
        }

        if ($model instanceof AntiCheatSetting) {
            return 'Anti-cheat for '.str_replace('_', ' ', (string) ($model->assessment_type ?? 'class work'));
        }

        foreach (['title', 'name', 'rank_name'] as $attribute) {
            $value = trim((string) $model->getAttribute($attribute));
            if ($value !== '') {
                return $value;
            }
        }

        return class_basename($model).' #'.$model->getKey();
    }

    /** @param  array<int, mixed>  $ids */
    private static function moduleTitles(array $ids): string
    {
        $titles = ModuleLibraryItem::query()
            ->whereIn('id', array_map('intval', $ids))
            ->orderBy('module_no')
            ->get(['title', 'version_name'])
            ->map(fn (ModuleLibraryItem $module) => $module->title.($module->version_name ? ' ('.$module->version_name.')' : ''))
            ->all();

        return $titles === [] ? 'Class module' : implode(', ', $titles);
    }

    private static function studentNote(mixed $student): string
    {
        return $student instanceof User ? 'Student: '.$student->name : '';
    }

    private static function accountStatus(?Model $user): ?string
    {
        $status = $user?->fresh()?->status;

        return $status === null ? null : ($status === 'active' ? 'Activated' : 'Deactivated');
    }

    private static function roleNote(?Model $user): ?string
    {
        $fresh = $user?->fresh();

        return $fresh instanceof User ? 'New role: '.$fresh->roleName() : null;
    }

    private static function applicant(?Model $application): string
    {
        $user = $application ? User::query()->find($application->getAttribute('user_id')) : null;

        return $user ? $user->name.' ('.$user->email.')' : 'Instructor application';
    }
}
