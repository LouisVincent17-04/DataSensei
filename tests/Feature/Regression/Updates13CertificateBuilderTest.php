<?php

namespace Tests\Feature\Regression;

use App\Http\Controllers\AdminCertificateController;
use App\Models\AuditLog;
use App\Models\CertificateDefinition;
use App\Models\User;
use App\Models\UserCertificate;
use App\Services\CertificateService;
use App\Services\CertificateSettings;
use App\Services\ClassCertificateService;
use App\Services\Reports\ClassCompletion;
use App\Support\Certificates\CertificateLayouts;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\Feature\Regression\Concerns\BuildsReportData;
use Tests\TestCase;

/**
 * The instructor Certificate Builder for a class Certificate of Completion
 * (DataSensei Updates 13, as changed by the Combined Certificate and
 * Gradebook Requirements), issuance by the instructor, My Certificates with
 * PDF, public verification, administrator settings, layouts, revoke and
 * reissue.
 *
 * Fixture (BuildsReportData): Ana teaches Data Science (Sam, Lia, Tom). The
 * class requires 2 modules (Python Basics, Pandas Basics), 2 activities (the
 * Loops quiz and coding challenges) and 4 assessments (Loops, Functions,
 * Upcoming worksheets and the Midterm). Sam has done everything except the
 * Upcoming Worksheet; Lia and Tom are far from complete. Ben teaches
 * Statistics (Zoe). Ana's institution is "Report School".
 */
class Updates13CertificateBuilderTest extends TestCase
{
    use BuildsReportData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildReportData();
        $this->dataScience->forceFill(['subject_code' => 'DS 101', 'term' => '1st Semester 2026-2027'])->save();
    }

    public function test_the_builder_has_no_module_choice_and_a_live_preview(): void
    {
        $form = $this->authenticateAs($this->ana)->get(route('instructor.certificates.create'))->assertOk()->getContent();
        $this->assertStringNotContainsString('Choose a module', $form);
        $this->assertStringNotContainsString('name="module"', $form);
        $this->assertStringContainsString('Course / class', $form);
        foreach (CertificateLayouts::LAYOUTS as $layout) {
            $this->assertStringContainsString($layout['name'], $form);
        }
        foreach (['[Learner Name]', '[Course Name]', '[Course Code]', '[Section]', '[Semester]', '[Certificate ID]', '[Issue Date]'] as $placeholder) {
            $this->assertStringContainsString($placeholder, $form);
        }
        // The layout is chosen first: none is selected yet, so no preview.
        $this->assertDoesNotMatchRegularExpression('#name="layout_key"[^>]*checked#', $form);
        $this->assertStringContainsString('Choose a layout in step 1', $form);
        $this->assertStringContainsString('DS 101', $form);
        $this->assertStringContainsString('1st Semester 2026-2027', $form);

        // The live preview follows the unsaved form.
        $preview = $this->postJson(route('instructor.certificates.live-preview'), [
            'layout_key' => CertificateLayouts::FORMAL_BORDER,
            'class_id' => $this->dataScience->id,
            'name' => 'Data Science Completion',
            'signatory_title' => 'Data Science Instructor',
            'signatory_name' => 'Someone Else',
            'statement' => '[Learner Name] completed [Course Code] [Course Name], [Section], [Semester]. [Grade]',
        ])->assertOk()->json();
        $this->assertStringContainsString('<svg', $preview['svg']);
        foreach (['Data Science Completion', 'Sample Learner completed DS 101 Data Science, DS 4A, 1st Semester 2026-2027.', 'Ana Instructor', 'Data Science Instructor', 'REPORT SCHOOL'] as $text) {
            $this->assertStringContainsString($text, html_entity_decode($preview['svg']), $text);
        }
        $this->assertStringNotContainsString('Someone Else', $preview['svg']);
        $this->assertSame(['[Grade]'], $preview['unknown']);

        // Another instructor's class is never read; no layout means no preview.
        $other = $this->postJson(route('instructor.certificates.live-preview'), ['layout_key' => CertificateLayouts::INSTITUTIONAL, 'class_id' => $this->statistics->id, 'statement' => '[Course Name]'])->json();
        $this->assertStringNotContainsString('Statistics', $other['svg']);
        $this->assertStringContainsString('Course name', $other['svg']);
        $this->assertNull($this->postJson(route('instructor.certificates.live-preview'), ['statement' => 'x'])->json('svg'));

        // Nothing was saved.
        $this->assertSame(0, CertificateDefinition::query()->classCertificates()->count());
        $this->authenticateAs($this->sam)->postJson(route('instructor.certificates.live-preview'), ['layout_key' => CertificateLayouts::FORMAL_BORDER])->assertForbidden();
    }

    public function test_the_instructor_issues_the_certificate_to_students_who_completed_the_class(): void
    {
        $this->authenticateAs($this->ana)->post(route('instructor.certificates.store'), $this->config([
            'signatory_name' => 'Someone Else',
            'module' => 'library:'.$this->ids['m1'],
        ]))->assertRedirect();
        $definition = CertificateDefinition::query()->classCertificates()->firstOrFail();
        $this->assertSame(CertificateDefinition::STATUS_DRAFT, $definition->status);
        $this->assertSame((int) $this->dataScience->id, (int) $definition->class_id);

        // Activation needs the preview; activating issues nothing.
        $this->patch(route('instructor.certificates.activate', $definition));
        $this->assertSame(CertificateDefinition::STATUS_DRAFT, $definition->fresh()->status);
        $page = $this->get(route('instructor.certificates.show', $definition))->assertOk()->getContent();
        foreach (['Sample Learner', 'Ana Instructor', 'Data Science Instructor', 'Sam Student', 'Lia Student', 'Tom Student', '7 of 8 done', 'Upcoming Worksheet'] as $text) {
            $this->assertStringContainsString($text, $page, $text);
        }
        $this->assertStringNotContainsString('Someone Else', $page);
        $this->patch(route('instructor.certificates.activate', $definition))->assertSessionHas('success');
        $this->assertSame(CertificateDefinition::STATUS_ACTIVE, $definition->fresh()->status);
        $this->assertSame(0, UserCertificate::count());

        // Sam has not turned in the Upcoming Worksheet: refused on the server.
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id]]);
        $this->assertSame(0, UserCertificate::count());

        // Sam completes the last item; Lia and Zoe (another class) are refused or ignored.
        $this->gradeUpcomingWorksheet($this->sam);
        $page = $this->get(route('instructor.certificates.show', $definition))->getContent();
        $this->assertMatchesRegularExpression('#name="students\[\]" value="'.$this->sam->id.'"[^>]*data-eligible#', $page);
        $this->assertMatchesRegularExpression('#name="students\[\]" value="'.$this->lia->id.'"[^>]*disabled#', $page);
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id, $this->lia->id, $this->zoe->id]])->assertSessionHas('success');

        $this->assertSame([(int) $this->sam->id], UserCertificate::query()->pluck('user_id')->map(fn ($id) => (int) $id)->all());
        $certificate = UserCertificate::query()->firstOrFail();
        $this->assertStringStartsWith('DS-CLS-', $certificate->certificate_number);
        $this->assertSame((int) $this->dataScience->id, (int) $certificate->class_id);
        $this->assertSame((int) $this->ana->id, (int) $certificate->issued_by);
        $snapshot = $certificate->snapshotData();
        $this->assertSame('Certificate of Completion', $snapshot['name']);
        $this->assertSame('DS 101 Data Science, 1st Semester 2026-2027', $snapshot['module']);
        $this->assertSame('This certifies that Sam Student has successfully completed all required modules, activities and assessments of Data Science (1st Semester 2026-2027).', $snapshot['statement']);
        $this->assertSame('Ana Instructor', $snapshot['signatory_name']);
        $this->assertSame('Report School', $snapshot['issuer_name']);
        $this->assertSame('Ana Instructor', $snapshot['issued_by']['name']);
        $this->assertCount(8, $snapshot['requirements']);
        $this->assertDatabaseHas('notifications', ['user_id' => $this->sam->id, 'type' => 'certificate_earned']);
        $log = AuditLog::query()->where('action', 'issued')->firstOrFail();
        $this->assertSame([(int) $this->ana->id, 'Certificate'], [(int) $log->user_id, $log->record_type]);

        // Once per student.
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id]])->assertSessionHas('error');
        $this->assertSame(1, UserCertificate::count());

        // Progress never issues a class certificate by itself.
        DB::table('module_library_progress')->where('user_id', $this->lia->id)->update(['completed_at' => now()]);
        $this->authenticateAs($this->lia)->post(route('student.modules.complete', $this->ids['m2']));
        $this->get(route('student.certificates.index'))->assertOk();
        $this->assertSame(0, UserCertificate::query()->where('user_id', $this->lia->id)->count());
    }

    public function test_the_builder_checks_every_choice_on_the_server(): void
    {
        $this->authenticateAs($this->ana);

        $cases = [
            'class_id' => ['class_id' => $this->statistics->id],
            'statement' => ['statement' => 'Awarded to [Learner Name] with [Final Grade].'],
            'name' => ['name' => ''],
            'signatory_title' => ['signatory_title' => ''],
            'layout_key' => ['layout_key' => ''],
        ];
        foreach ($cases as $field => $override) {
            $this->post(route('instructor.certificates.store'), $this->config($override))->assertSessionHasErrors($field);
        }
        $this->post(route('instructor.certificates.store'), $this->config(['layout_key' => 'my_own_html']))->assertSessionHasErrors('layout_key');

        app(CertificateSettings::class)->setLayoutEnabled(CertificateLayouts::MODERN_ACADEMIC, false);
        $this->post(route('instructor.certificates.store'), $this->config(['layout_key' => CertificateLayouts::MODERN_ACADEMIC]))->assertSessionHasErrors('layout_key');
        $this->assertStringNotContainsString('Modern Academic', $this->get(route('instructor.certificates.create'))->assertOk()->getContent());

        $this->assertSame(0, CertificateDefinition::query()->classCertificates()->count());
    }

    public function test_an_instructor_only_manages_their_own_configurations(): void
    {
        $definition = $this->activeCertificate();
        $this->gradeUpcomingWorksheet($this->sam);

        $this->authenticateAs($this->ben);
        $this->get(route('instructor.certificates.show', $definition))->assertNotFound();
        $this->get(route('instructor.certificates.edit', $definition))->assertNotFound();
        $this->get(route('instructor.certificates.preview-pdf', $definition))->assertNotFound();
        $this->put(route('instructor.certificates.update', $definition), $this->config(['name' => 'Taken over']))->assertNotFound();
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id]])->assertNotFound();
        $this->patch(route('instructor.certificates.deactivate', $definition))->assertNotFound();
        $this->delete(route('instructor.certificates.destroy', $definition))->assertNotFound();
        $this->get(route('instructor.certificates.index'))->assertOk()->assertDontSee('Certificate of Completion</td>', false);
        $this->assertSame(0, UserCertificate::count());
        $this->assertSame(CertificateDefinition::STATUS_ACTIVE, $definition->fresh()->status);

        // A configuration of a class Ana no longer teaches is closed to her too.
        $this->dataScience->forceFill(['instructor_id' => $this->ben->id])->save();
        $this->authenticateAs($this->ana)->get(route('instructor.certificates.show', $definition))->assertNotFound();

        $this->authenticateAs($this->sam)->get(route('instructor.certificates.index'))->assertForbidden();
        $this->authenticateAs($this->ana)->get(route('admin.certificates.index'))->assertForbidden();
    }

    public function test_editing_returns_to_draft_and_never_changes_issued_certificates(): void
    {
        $definition = $this->issuedToSam();
        $issued = UserCertificate::query()->where('user_id', $this->sam->id)->firstOrFail();
        $before = $issued->snapshot;

        $this->authenticateAs($this->ana);
        $this->put(route('instructor.certificates.update', $definition), $this->config([
            'name' => 'Certificate of Achievement',
            'statement' => 'Awarded to [Learner Name] for [Course Code] [Course Name].',
            'layout_key' => CertificateLayouts::MINIMAL_PROFESSIONAL,
        ]))->assertRedirect(route('instructor.certificates.show', $definition));
        $definition->refresh();
        $this->assertSame(CertificateDefinition::STATUS_DRAFT, $definition->status);
        $this->assertSame(2, (int) $definition->config_version);

        // Issued: the class is locked. A draft cannot be issued.
        $this->put(route('instructor.certificates.update', $definition), $this->config(['class_id' => $this->dataScience->id + 999]))->assertSessionHasErrors('class_id');
        $this->completeEverything($this->lia);
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->lia->id]])->assertSessionHas('error');
        $this->assertSame(0, UserCertificate::query()->where('user_id', $this->lia->id)->count());

        // The class is renamed later; Lia's certificate uses the new wording and name.
        $this->dataScience->forceFill(['name' => 'Applied Data Science'])->save();
        $this->get(route('instructor.certificates.show', $definition))->assertOk()->assertSee('Applied Data Science');
        $this->patch(route('instructor.certificates.activate', $definition))->assertSessionHas('success');
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->lia->id]])->assertSessionHas('success');
        $lias = UserCertificate::query()->where('user_id', $this->lia->id)->firstOrFail()->snapshotData();
        $this->assertSame('Certificate of Achievement', $lias['name']);
        $this->assertSame('Awarded to Lia Student for DS 101 Applied Data Science.', $lias['statement']);
        $this->assertSame(CertificateLayouts::MINIMAL_PROFESSIONAL, $lias['layout_key']);

        // Sam's certificate is exactly as issued.
        $this->assertSame($before, $issued->fresh()->snapshot);
        $this->authenticateAs($this->sam)->get(route('student.certificates.show', $issued->id))->assertOk()
            ->assertSee('Certificate of Completion')->assertDontSee('Applied Data Science');

        // Inactive: cannot be issued; with certificates issued it cannot be deleted.
        $this->authenticateAs($this->ana)->patch(route('instructor.certificates.deactivate', $definition))->assertSessionHas('success');
        $this->completeEverything($this->tom);
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->tom->id]])->assertSessionHas('error');
        $this->assertSame(0, UserCertificate::query()->where('user_id', $this->tom->id)->count());
        $this->delete(route('instructor.certificates.destroy', $definition))->assertSessionHas('error');
        $this->assertNotNull($definition->fresh());

        // A draft that never issued anything can be deleted.
        $this->post(route('instructor.certificates.store'), $this->config(['name' => 'Spare draft']));
        $draft = CertificateDefinition::query()->where('name', 'Spare draft')->firstOrFail();
        $this->delete(route('instructor.certificates.destroy', $draft))->assertRedirect(route('instructor.certificates.index'));
        $this->assertNull($draft->fresh());
    }

    public function test_completion_counts_every_item_the_instructor_assigned_and_nothing_else(): void
    {
        $completion = app(ClassCompletion::class)->forClass($this->dataScience);
        $this->assertSame(['modules' => 2, 'activities' => 2, 'assessments' => 4], array_map('count', $completion['requirements']));
        $this->assertSame(8, $completion['total']);
        $sam = $completion['students'][$this->sam->id];
        $this->assertSame([7, false], [$sam['done'], $sam['complete']]);
        $this->assertSame('Upcoming Worksheet', $sam['missing'][0]['title']);
        $this->assertSame(83.3, $sam['final_grade']);

        // An attempt held for an integrity review is not complete yet.
        DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->ids['a3'], 'student_id' => $this->sam->id, 'attempt_no' => 1, 'status' => 'submitted',
            'score' => 0, 'provisional_score' => 10, 'total_points' => 10, 'integrity_status' => 'review_required',
            'started_at' => now(), 'submitted_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->assertFalse(app(ClassCompletion::class)->forClass($this->dataScience)['students'][$this->sam->id]['complete']);
        DB::table('assessment_submissions')->where('assessment_id', $this->ids['a3'])->delete();

        // A public challenge passed outside the class changes nothing; a module
        // removed from the class is no longer required.
        $public = DB::table('challenges')->insertGetId(['challenge_category_id' => DB::table('challenge_categories')->value('id'), 'title' => 'Public Quiz', 'description' => 'x', 'order_index' => 9, 'is_coding_challenge' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('challenge_attempts')->insert(['user_id' => $this->sam->id, 'challenge_id' => $public, 'attempt_no' => 1, 'mode' => 'practice', 'status' => 'submitted', 'started_at' => now(), 'submitted_at' => now(), 'time_taken_seconds' => 5, 'score' => 10, 'total_questions' => 10, 'xp_awarded' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('class_module_assignments')->where('class_id', $this->dataScience->id)->where('module_library_item_id', $this->ids['m2'])->update(['status' => 'archived']);
        $after = app(ClassCompletion::class)->forClass($this->dataScience);
        $this->assertSame(7, $after['total']);
        $this->assertSame(6, $after['students'][$this->sam->id]['done']);
    }

    public function test_my_certificates_pdf_and_public_verification(): void
    {
        $this->issuedToSam();
        $certificate = UserCertificate::query()->where('user_id', $this->sam->id)->firstOrFail();

        $page = $this->authenticateAs($this->sam)->get(route('student.certificates.index'))->assertOk()->getContent();
        foreach (['My Certificates', 'Certificate of Completion', 'Course or milestone', 'DS 101 Data Science, 1st Semester 2026-2027', $certificate->certificate_number, 'View', 'Download PDF'] as $text) {
            $this->assertStringContainsString($text, $page, $text);
        }
        $pdf = $this->get(route('student.certificates.pdf', $certificate->id))->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());

        $this->authenticateAs($this->lia)->get(route('student.certificates.pdf', $certificate->id))->assertNotFound();
        $this->get(route('student.certificates.show', $certificate->id))->assertNotFound();

        auth()->logout();
        $verify = $this->get(route('certificates.verify.show', strtolower($certificate->certificate_number)))->assertOk()->getContent();
        foreach (['Valid certificate', 'Sam Student', 'Certificate of Completion', 'DS 101 Data Science', 'Report School', $certificate->issued_at->format('F j, Y'), $certificate->certificate_number] as $text) {
            $this->assertStringContainsString($text, $verify, $text);
        }
        foreach ([$this->sam->email, 'DS 4A', 'Ana Instructor', '83.3', 'user_id'] as $private) {
            $this->assertStringNotContainsString($private, $verify, $private);
        }
        $this->get(route('certificates.verify.form', ['id' => ' '.$certificate->certificate_number]))
            ->assertRedirect(route('certificates.verify.show', ['number' => $certificate->certificate_number]));
        $this->get(route('certificates.verify.show', 'DS-CLS-20260101-NOTREAL1'))->assertOk()->assertSee('No certificate has this ID.');
        $this->get('/certificates/verify/'.rawurlencode('<script>'))->assertNotFound();
    }

    public function test_an_admin_revokes_and_reissues_with_a_reason_and_an_audit_trail(): void
    {
        $this->issuedToSam();
        $certificate = UserCertificate::query()->where('user_id', $this->sam->id)->firstOrFail();

        $this->authenticateAs($this->admin);
        $this->get(route('admin.certificates.index'))->assertOk()->assertSee($certificate->certificate_number)->assertSee('Sam Student');
        $this->get(route('admin.certificates.show', $certificate))->assertOk()->assertSee('by Ana Instructor');

        $this->patch(route('admin.certificates.revoke', $certificate), ['reason' => ''])->assertSessionHasErrors('reason');
        $this->assertFalse($certificate->fresh()->isRevoked());

        $this->patch(route('admin.certificates.revoke', $certificate), ['reason' => 'Issued to the wrong account.'])->assertRedirect();
        $certificate->refresh();
        $this->assertTrue($certificate->isRevoked());
        $this->assertNull($certificate->active_slot);
        $this->assertDatabaseHas('audit_logs', ['action' => 'revoked', 'record_type' => 'Certificate', 'record_id' => $certificate->id, 'details' => 'Reason: Issued to the wrong account.']);

        $this->get(route('certificates.verify.show', $certificate->certificate_number))->assertOk()->assertSee('was revoked')->assertDontSee('Valid certificate');
        $this->authenticateAs($this->sam)->get(route('student.certificates.pdf', $certificate->id))->assertNotFound();

        // The instructor cannot simply issue it again; only a reissue can.
        $definition = CertificateDefinition::query()->classCertificates()->firstOrFail();
        $this->authenticateAs($this->ana)->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id]]);
        $this->assertSame(1, UserCertificate::query()->where('user_id', $this->sam->id)->count());

        $this->authenticateAs($this->admin)->post(route('admin.certificates.reissue', $certificate), ['reason' => 'Name corrected on the account.'])->assertRedirect();
        $new = UserCertificate::query()->where('reissued_from_id', $certificate->id)->firstOrFail();
        $this->assertNotSame($certificate->certificate_number, $new->certificate_number);
        $this->assertSame($certificate->snapshotData()['module'], $new->snapshotData()['module']);
        $this->assertSame(1, UserCertificate::query()->where('user_id', $this->sam->id)->active()->count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'reissued', 'record_type' => 'Certificate', 'record_id' => $certificate->id]);
        $this->post(route('admin.certificates.reissue', $certificate), ['reason' => 'Trying again.'])->assertSessionHas('error');

        $this->expectException(QueryException::class);
        UserCertificate::query()->create([
            'user_id' => $this->sam->id, 'certificate_definition_id' => $new->certificate_definition_id, 'certificate_key' => $new->certificate_key,
            'certificate_number' => 'DS-CLS-20260101-DUPLICAT', 'requirement_set_id' => $new->requirement_set_id, 'requirement_version' => $new->requirement_version,
            'snapshot' => '{}', 'issued_at' => now(), 'status' => UserCertificate::STATUS_ACTIVE, 'active_slot' => 1,
        ]);
    }

    public function test_admin_settings_layouts_and_core_certificates(): void
    {
        $this->authenticateAs($this->admin);
        foreach (array_keys(AdminCertificateController::TABS) as $tab) {
            $this->get(route('admin.certificates.index', ['tab' => $tab]))->assertOk();
        }

        $this->put(route('admin.certificates.settings'), $this->settingsForm())->assertRedirect();
        $settings = app(CertificateSettings::class);
        $this->assertSame('DataSensei Academy', $settings->get('issuer_name'));
        $this->assertSame('1', $settings->get('core_coding_all_levels'));
        $this->assertDatabaseHas('audit_logs', ['action' => 'configured', 'record_type' => 'Certificate Settings']);

        $this->put(route('admin.certificates.settings'), $this->settingsForm(['issuer_logo' => UploadedFile::fake()->image('logo.png', 600, 300)]))->assertSessionHasNoErrors();
        $logo = app(CertificateSettings::class)->logo();
        $this->assertStringStartsWith("\xFF\xD8", (string) base64_decode((string) $logo));
        $this->put(route('admin.certificates.settings'), $this->settingsForm(['issuer_logo' => UploadedFile::fake()->createWithContent('logo.png', 'not an image')]))->assertSessionHasErrors('issuer_logo');
        $this->put(route('admin.certificates.settings'), $this->settingsForm(['remove_logo' => '1']))->assertSessionHasNoErrors();
        $this->assertNull(app(CertificateSettings::class)->logo());

        $this->patch(route('admin.certificates.layouts.status', CertificateLayouts::INSTITUTIONAL))->assertSessionHas('error');
        $this->patch(route('admin.certificates.layouts.status', CertificateLayouts::ACADEMIC_CLASSIC))->assertSessionHas('success');
        $this->assertFalse(app(CertificateSettings::class)->layoutEnabled(CertificateLayouts::ACADEMIC_CLASSIC));
        $this->get(route('admin.certificates.layouts.preview', CertificateLayouts::ACADEMIC_CLASSIC))->assertOk()->assertSee('<svg', false);

        // Turning a layout off keeps certificates already issued with it.
        $this->issuedToSam(); // Formal Border
        $this->authenticateAs($this->admin)->patch(route('admin.certificates.layouts.status', CertificateLayouts::FORMAL_BORDER))->assertSessionHas('success');
        $issued = UserCertificate::query()->where('user_id', $this->sam->id)->firstOrFail();
        $this->assertSame(CertificateLayouts::FORMAL_BORDER, app(CertificateService::class)->displayData($issued)['layout_key']);
        $this->authenticateAs($this->sam)->get(route('student.certificates.pdf', $issued->id))->assertOk();

        $definition = CertificateDefinition::query()->where('certificate_key', CertificateService::CORE_CODING)->firstOrFail();
        $this->authenticateAs($this->admin)->patch(route('admin.certificates.definitions.status', $definition))->assertSessionHas('success');
        $this->assertFalse((bool) $definition->fresh()->is_active);
        $this->patch(route('admin.certificates.definitions.status', CertificateDefinition::query()->classCertificates()->firstOrFail()))->assertNotFound();
    }

    public function test_dark_and_light_mode_and_a_plain_look(): void
    {
        $definition = $this->issuedToSam();
        $pages = [
            [$this->ana, route('instructor.certificates.index')],
            [$this->ana, route('instructor.certificates.create')],
            [$this->ana, route('instructor.certificates.show', $definition)],
            [$this->sam, route('student.certificates.index')],
            [$this->admin, route('admin.certificates.index')],
        ];
        foreach ($pages as [$user, $url]) {
            $html = $this->authenticateAs($user)->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('data-ds-theme-toggle', $html, $url);
            $this->assertStringContainsString(':root[data-theme="light"]', $html, $url);
        }
        auth()->logout();
        $this->get(route('certificates.verify.form'))->assertOk()->assertSee('data-ds-theme-toggle', false);

        $views = array_merge(
            glob(resource_path('views/certificates/*.blade.php')),
            glob(resource_path('views/instructor/certificates/*.blade.php')),
            glob(resource_path('views/admin/certificates/*.blade.php')),
            glob(resource_path('views/student/certificates/*.blade.php')),
            glob(resource_path('views/*/gradebook/*.blade.php')),
        );
        $this->assertGreaterThanOrEqual(11, count($views));
        foreach ($views as $view) {
            $this->assertDoesNotMatchRegularExpression('/gradient\(|class="[^"]*\b[\w-]*(pill|badge|chip|hero)\b/i', (string) file_get_contents($view), $view);
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────

    /** @param array<string, mixed> $override */
    private function config(array $override = []): array
    {
        return array_merge([
            'name' => 'Certificate of Completion',
            'class_id' => $this->dataScience->id,
            'signatory_title' => 'Data Science Instructor',
            'statement' => ClassCertificateService::DEFAULT_STATEMENT,
            'layout_key' => CertificateLayouts::FORMAL_BORDER,
        ], $override);
    }

    /** @param array<string, mixed> $override */
    private function settingsForm(array $override = []): array
    {
        return array_merge([
            'issuer_name' => 'DataSensei Academy', 'issuer_line' => 'Learning Platform', 'signatory_name' => 'Dr. Reyes', 'signatory_title' => 'Program Director',
            'system_layout' => CertificateLayouts::INSTITUTIONAL, 'core_challenge_level' => 'university-student', 'core_coding_all_levels' => '1',
        ], $override);
    }

    /** Ana's certificate for Data Science, saved, previewed and activated through the pages. */
    private function activeCertificate(array $override = []): CertificateDefinition
    {
        $this->authenticateAs($this->ana)->post(route('instructor.certificates.store'), $this->config($override))->assertSessionHasNoErrors();
        $definition = CertificateDefinition::query()->classCertificates()->latest('id')->firstOrFail();
        $this->get(route('instructor.certificates.show', $definition))->assertOk();
        $this->patch(route('instructor.certificates.activate', $definition))->assertRedirect();
        $this->assertSame(CertificateDefinition::STATUS_ACTIVE, $definition->fresh()->status);

        return $definition->fresh();
    }

    /** The active certificate, issued by Ana to Sam once he completed the class. */
    private function issuedToSam(): CertificateDefinition
    {
        $definition = $this->activeCertificate();
        $this->gradeUpcomingWorksheet($this->sam);
        $this->authenticateAs($this->ana)->post(route('instructor.certificates.issue', $definition), ['students' => [$this->sam->id]])->assertSessionHas('success');
        $this->assertSame(1, UserCertificate::query()->where('user_id', $this->sam->id)->count());

        return $definition;
    }

    private function gradeUpcomingWorksheet(User $student): void
    {
        DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->ids['a3'], 'student_id' => $student->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 9, 'total_points' => 10,
            'started_at' => now(), 'submitted_at' => now(), 'graded_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** Every required item of Data Science, done and graded. */
    private function completeEverything(User $student): void
    {
        foreach (['m1', 'm2'] as $module) {
            DB::table('module_library_progress')->updateOrInsert(
                ['user_id' => $student->id, 'module_library_item_id' => $this->ids[$module]],
                ['class_id' => $this->dataScience->id, 'opened_at' => now(), 'last_opened_at' => now(), 'completed_at' => now(), 'created_at' => now(), 'updated_at' => now()]
            );
        }
        foreach (['a1', 'a2', 'a3', 'x1'] as $assessment) {
            $next = 1 + (int) DB::table('assessment_submissions')->where('assessment_id', $this->ids[$assessment])->where('student_id', $student->id)->max('attempt_no');
            DB::table('assessment_submissions')->insert([
                'assessment_id' => $this->ids[$assessment], 'student_id' => $student->id, 'attempt_no' => $next, 'status' => 'graded', 'score' => 8, 'total_points' => 10,
                'started_at' => now(), 'submitted_at' => now(), 'graded_at' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        DB::table('challenge_attempts')->insert([
            'user_id' => $student->id, 'challenge_id' => $this->ids['c1'], 'attempt_no' => 1 + (int) DB::table('challenge_attempts')->where('user_id', $student->id)->where('challenge_id', $this->ids['c1'])->max('attempt_no'),
            'mode' => 'practice', 'status' => 'submitted', 'started_at' => now(), 'submitted_at' => now(), 'time_taken_seconds' => 60, 'score' => 9, 'total_questions' => 10, 'xp_awarded' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        foreach (['q1', 'q2'] as $question) {
            DB::table('coding_submissions')->insert([
                'user_id' => $student->id, 'coding_question_id' => $this->ids[$question], 'code' => 'print(1)', 'language' => 'python', 'status' => 'passed',
                'tests_passed' => 4, 'tests_total' => 4, 'xp_earned' => 0, 'time_taken_seconds' => 30, 'voided' => false, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
