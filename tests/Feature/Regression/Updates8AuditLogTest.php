<?php

namespace Tests\Feature\Regression;

use App\Models\AchievementDefinition;
use App\Models\AuditLog;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * DataSensei Updates 8: the audit log behind Admin Reports > Audit Logs.
 * Important admin and instructor actions are recorded once each, with who,
 * their role, the action, the affected record and when; failed requests,
 * previews and student activity are not.
 */
class Updates8AuditLogTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    private const AJAX = ['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
    }

    public function test_admin_module_actions_are_recorded_once_each(): void
    {
        $this->authenticateAs($this->admin);
        $form = fn (array $extra = []) => array_merge([
            'title' => 'Audit Module', 'description' => 'Traced.', 'year_level' => 'Year 1', 'xp_reward' => 100, 'is_boss' => 0, 'has_coding_exercises' => 0,
            'lessons_json' => json_encode([['id' => null, 'title' => 'One', 'blocks' => [['type' => 'paragraph', 'text' => 'Hi.']]]]),
            'review_questions_json' => '[]', 'learning_outcomes' => ['Explain it.'],
        ], $extra);

        $this->withHeaders(self::AJAX)->post(route('admin.modules.store'), $form(['intent' => 'draft']))->assertCreated();
        $module = Module::where('title', 'Audit Module')->firstOrFail();
        $sections = json_encode([['id' => $module->lessons()->value('id'), 'title' => 'One', 'blocks' => [['type' => 'paragraph', 'text' => 'Hi.']]]]);

        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $form(['intent' => 'publish', 'lessons_json' => $sections]))->assertOk();
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $form(['intent' => 'save', 'lessons_json' => $sections, 'description' => 'Changed.']))->assertOk();
        // A failed save and a preview write nothing.
        $this->withHeaders(self::AJAX)->put(route('admin.modules.update', $module), $form(['intent' => 'save', 'title' => '']))->assertStatus(422);
        $this->post(route('admin.modules.preview-draft'), $form())->assertOk();
        $this->patch(route('admin.modules.status', $module))->assertRedirect();

        $logs = AuditLog::orderBy('id')->get();
        $this->assertSame(['created', 'published', 'edited', 'unpublished'], $logs->pluck('action')->all());
        $this->assertTrue($logs->every(fn (AuditLog $log) => $log->user_id === $this->admin->id && $log->user_role === User::ROLE_ADMIN && $log->user_name === 'Adele Admin'));
        $this->assertTrue($logs->every(fn (AuditLog $log) => $log->record_type === 'DataSensei Module' && $log->record_id === $module->id && $log->record_label === 'Audit Module'));
        $this->assertSame('Saved as a draft', $logs[0]->details);
        $this->assertNotNull($logs[0]->created_at);

        $this->delete(route('admin.modules.destroy', $module))->assertRedirect();
        $deleted = AuditLog::latest('id')->first();
        $this->assertSame(['deleted', 'Audit Module', $module->id], [$deleted->action, $deleted->record_label, $deleted->record_id]);
    }

    public function test_gamification_changes_and_blocked_deletes(): void
    {
        $this->authenticateAs($this->admin);
        $earned = AchievementDefinition::where('achievement_key', 'first_run')->firstOrFail();
        $spare = AchievementDefinition::create(['achievement_key' => 'spare', 'name' => 'Spare Badge', 'description' => 'x', 'xp_reward' => 5, 'criteria_type' => 'code_runs', 'criteria_value' => 9, 'is_active' => true, 'sort_order' => 9]);

        // Achievements are read-only since DataSensei Updates 9; missions are still edited.
        $mission = \App\Models\MissionDefinition::where('mission_key', 'daily_run')->firstOrFail();
        $this->from(route('admin.gamification.index'))->put(route('admin.gamification.missions.update', $mission), ['title' => 'Run Code Daily', 'xp_reward' => 30, 'description' => 'Run code.', 'period_type' => 'daily'])->assertRedirect();
        $this->from(route('admin.gamification.index'))->delete(route('admin.gamification.achievements.destroy', $earned))->assertSessionHas('error');
        $this->from(route('admin.gamification.index'))->delete(route('admin.gamification.achievements.destroy', $spare))->assertSessionHas('success');

        $this->assertSame(
            [['edited', 'Mission', 'Run Code Daily', 'Previously: Run Code Today.'], ['deleted', 'Achievement', 'Spare Badge', null]],
            AuditLog::orderBy('id')->get()->map(fn (AuditLog $l) => [$l->action, $l->record_type, $l->record_label, $l->details])->all(),
            'The blocked delete of an earned achievement is not recorded.'
        );
    }

    public function test_instructor_assignments_and_class_changes_are_recorded(): void
    {
        $this->authenticateAs($this->ana);

        $this->withHeaders(self::AJAX)->post(route('modules.module-library.assign'), ['class_id' => $this->dataScience->id, 'selected_modules' => [3 => $this->ids['m3']]])->assertOk();
        $this->withHeaders(self::AJAX)->post(route('modules.module-library.unassign'), ['class_id' => $this->dataScience->id, 'remove_module_id' => $this->ids['m3']])->assertOk();
        // Nothing to remove: refused, not recorded.
        $this->withHeaders(self::AJAX)->post(route('modules.module-library.unassign'), ['class_id' => $this->dataScience->id, 'remove_module_id' => $this->ids['m3']])->assertStatus(422);
        $this->patch(route('instructor.classes.archive', $this->dataScience))->assertRedirect();

        $logs = AuditLog::orderBy('id')->get();
        $this->assertSame(['assigned', 'unassigned', 'archived'], $logs->pluck('action')->all());
        $this->assertSame('Unassigned Module (Version 1)', $logs[0]->record_label);
        $this->assertSame('Class: Data Science', $logs[0]->details);
        $this->assertSame('Removed assignment', $logs[1]->actionLabel());
        $this->assertSame(['Class', 'Data Science, DS 4A'], [$logs[2]->record_type, $logs[2]->record_label]);
        $this->assertTrue($logs->every(fn (AuditLog $l) => $l->user_role === User::ROLE_INSTRUCTOR));
    }

    public function test_students_are_never_recorded(): void
    {
        $this->authenticateAs($this->sam);
        $lesson = DB::table('lessons')->where('module_id', $this->ids['public'])->value('id');
        $this->post(route('lesson.complete', $lesson))->assertRedirect();
        $this->post(route('student.modules.complete', ['module' => $this->ids['m1'], 'class' => $this->dataScience->id]))->assertRedirect();

        $this->assertSame(0, AuditLog::count());
    }

    public function test_the_audit_report_lists_and_filters_actions(): void
    {
        AuditLog::create(['user_id' => $this->admin->id, 'user_name' => 'Adele Admin', 'user_role' => User::ROLE_ADMIN, 'action' => 'published', 'record_type' => 'DataSensei Module', 'record_id' => 1, 'record_label' => 'Intro to Python', 'created_at' => now()->subDays(2)]);
        AuditLog::create(['user_id' => $this->ana->id, 'user_name' => 'Ana Instructor', 'user_role' => User::ROLE_INSTRUCTOR, 'action' => 'assigned', 'record_type' => 'Class Module', 'record_label' => 'Python Basics (Version 1)', 'details' => 'Class: Data Science', 'created_at' => now()->subDay()]);

        $this->authenticateAs($this->admin);
        $html = $this->get(route('admin.reports.show', ['report' => 'audit']))->assertOk()->getContent();
        foreach (['Date and time', 'User', 'Role', 'Action', 'Affected record'] as $heading) {
            $this->assertStringContainsString('>'.$heading.'</th>', $html);
        }
        $this->assertMatchesRegularExpression('#Ana Instructor.*?Instructor.*?Assigned.*?Class Module: Python Basics \(Version 1\).*?Class: Data Science#s', $html);
        $this->assertMatchesRegularExpression('#Adele Admin.*?Admin.*?Published.*?DataSensei Module: Intro to Python#s', $html);

        $this->get(route('admin.reports.show', ['report' => 'audit', 'role' => 'instructor']))->assertSee('Ana Instructor')->assertDontSee('Intro to Python');
        $this->get(route('admin.reports.show', ['report' => 'audit', 'action' => 'published']))->assertSee('Intro to Python')->assertDontSee('Python Basics (Version 1)');
        $this->get(route('admin.reports.show', ['report' => 'audit', 'from' => now()->toDateString()]))->assertSee('No actions match these filters.');

        $csv = $this->get(route('admin.reports.export', ['report' => 'audit', 'format' => 'csv']))->streamedContent();
        $this->assertStringContainsString('"Ana Instructor",Instructor,Assigned', $csv);
    }
}
