<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\CertificateDefinition;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Models\UserCertificate;
use App\Services\CertificateService;
use App\Support\Certificates\CertificateLayouts;
use App\Support\CoreCurriculum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\Feature\Regression\Concerns\BuildsCoreCurriculum;
use Tests\TestCase;

/**
 * DataSensei Additional Requirements, Part 3: the end-to-end simulation,
 * step by step, through the application's own pages, with the class
 * Certificate of Completion of the Combined Certificate and Gradebook
 * Requirements (the certificate is for a class, not one module).
 *
 *   1  Core Modules are labelled Core / System; title and delete are locked
 *   2  an admin adds "Financial Data Analytics": Custom, not one of the 24
 *   3  the instructor chooses one of the five layouts
 *   4  the instructor's class "CS 101 Python Fundamentals" supplies the
 *      course information; the signatory is the signed-in account
 *   5  preview, activate; the instructor issues it only to students the
 *      server confirms completed every required item of the class
 *   6  Core 24 Module Completion, issued once
 *   7  Core 24 Challenge Completion; extra instructor challenges never count
 *   8  Core Coding Challenge Completion when some core modules have no
 *      coding challenge
 *   9  a custom module's coding challenge never counts
 *  10  My Certificates (View, Download PDF) and public verification by ID
 *      without private data
 *  11  later edits leave the issued certificates exactly as they were
 */
class Updates13EndToEndSimulationTest extends TestCase
{
    use BuildsCoreCurriculum;
    use RefreshDatabase;

    private User $admin;

    private User $instructor;

    private User $learner;

    private ClassRoom $class;

    private int $pythonFundamentals;

    private int $quiz;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildCoreCurriculum();

        $institution = Institution::create(['name' => 'Northfield University', 'email' => 'registrar-'.Str::lower(Str::random(5)).'@northfield.test', 'status' => 'active']);
        $this->admin = $this->roleUser(User::ROLE_ADMIN, ['name' => 'Adele Admin']);
        $this->instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['name' => 'Iris Santos', 'institution_id' => $institution->id]);
        $this->learner = $this->roleUser(User::ROLE_USER, ['name' => 'Lea Cruz', 'email' => 'lea.cruz@northfield.test']);

        $this->class = ClassRoom::create(['instructor_id' => $this->instructor->id, 'institution_id' => $institution->id, 'name' => 'Python Fundamentals', 'subject_code' => 'CS 101', 'section' => 'BSDS 1A', 'term' => '1st Semester 2026-2027', 'is_archived' => false]);
        DB::table('class_student')->insert(['class_id' => $this->class->id, 'student_id' => $this->learner->id, 'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]);

        $this->pythonFundamentals = DB::table('module_library_items')->insertGetId([
            'module_no' => 1, 'module_code' => 'PYF-1', 'title' => 'Variables and Data Types', 'year_level' => 'Year 1',
            'version_no' => 1, 'version_name' => 'Version 1', 'version_code' => 'V1', 'estimated_minutes' => 45,
            'content_sections' => '[]', 'mcq_questions' => '[]', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('class_module_assignments')->insert(['class_id' => $this->class->id, 'module_library_item_id' => $this->pythonFundamentals, 'assigned_by' => $this->instructor->id, 'status' => 'active', 'assigned_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        $this->quiz = DB::table('assessments')->insertGetId([
            'class_id' => $this->class->id, 'created_by' => $this->instructor->id, 'title' => 'Python Fundamentals Quiz', 'topic_title' => 'Python', 'purpose' => 'quiz',
            'status' => 'published', 'total_items' => 1, 'total_points' => 20, 'max_attempts' => 2, 'available_at' => now()->subDay(), 'due_at' => now()->addDays(3),
            'published_at' => now()->subDay(), 'created_at' => now(), 'updated_at' => now(),
        ]);

        // Extra challenges an instructor made for practice: never required.
        $extraMcq = Challenge::create(['challenge_category_id' => $this->newbie->id, 'title' => 'Extra Loops Quiz', 'description' => 'x', 'is_coding_challenge' => false, 'order_index' => 30, 'created_by' => $this->instructor->id, 'visibility' => 'instructor']);
        $extraCoding = Challenge::create(['challenge_category_id' => $this->newbie->id, 'title' => 'Extra Python Practice', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 31, 'created_by' => $this->instructor->id, 'visibility' => 'instructor']);
        $this->codingQuestions($extraCoding, 1);
        $this->assertNotNull($extraMcq->id);
        CoreCurriculum::sync();
    }

    public function test_the_end_to_end_simulation(): void
    {
        $certificates = app(CertificateService::class);

        // ── Step 1: Core Modules are Core / System; title and delete locked.
        $list = $this->authenticateAs($this->admin)->get(route('admin.modules.index'))->assertOk()->getContent();
        $this->assertGreaterThanOrEqual(24, substr_count($list, 'Core / System'));
        $this->assertMatchesRegularExpression('#<button[^>]*disabled[^>]*title="Core Modules cannot be deleted"#', $list);
        $core = $this->coreModules[1];
        $this->delete(route('admin.modules.destroy', $core))->assertSessionHas('error');
        $this->assertNotNull($core->fresh());
        try {
            $core->fresh()->forceFill(['title' => 'Python Fundamentals'])->save();
            $this->fail('A core module title was changed.');
        } catch (ValidationException) {
            $this->assertSame('Basics of Python Programming', $core->fresh()->title);
        }
        $this->get(route('admin.modules.edit', $core))->assertOk()->assertSee('Core / System Module');

        // ── Step 2: "Financial Data Analytics" is a Custom module, not one of the 24.
        $this->post(route('admin.modules.store'), [
            'title' => 'Financial Data Analytics', 'description' => 'Elective.', 'year_level' => 'Year 3', 'xp_reward' => 100,
            'module_type' => 'core', 'module_key' => 'core-25-financial-data-analytics',
        ]);
        $custom = Module::query()->where('title', 'Financial Data Analytics')->firstOrFail();
        $this->assertSame('custom', $custom->module_type);
        $this->assertNull($custom->module_key);
        $this->assertSame('Custom', $custom->typeLabel());
        $this->assertSame(24, Module::query()->core()->count());
        $modules = $certificates->evaluate($this->learner, CertificateService::CORE_MODULES);
        $this->assertSame(24, $modules['total']);
        $this->assertNotContains('Financial Data Analytics', array_column($modules['items'], 'title'));

        // ── Step 3: the instructor sees the five predefined layouts and picks one.
        $form = $this->authenticateAs($this->instructor)->get(route('instructor.certificates.create'))->assertOk()->getContent();
        foreach (['Academic Classic', 'Institutional', 'Minimal Professional', 'Formal Border', 'Modern Academic'] as $layout) {
            $this->assertStringContainsString($layout, $form);
        }
        $this->assertStringNotContainsString('Choose a module', $form);
        foreach (['Course / class', 'Python Fundamentals', 'CS 101', '1st Semester 2026-2027', 'Iris Santos', 'Northfield University'] as $text) {
            $this->assertStringContainsString($text, $form);
        }
        $live = $this->postJson(route('instructor.certificates.live-preview'), [
            'layout_key' => CertificateLayouts::MODERN_ACADEMIC, 'class_id' => $this->class->id, 'name' => 'Python Fundamentals Completion Certificate',
            'signatory_title' => 'Assistant Professor', 'statement' => 'Completed [Course Code] [Course Name].',
        ])->assertOk()->json('svg');
        $this->assertStringContainsString('Completed CS 101 Python Fundamentals.', $live);

        // ── Step 4: the class supplies the course; the signatory is the account.
        $this->post(route('instructor.certificates.store'), [
            'name' => 'Python Fundamentals Completion Certificate',
            'class_id' => $this->class->id,
            'module' => 'library:'.$this->pythonFundamentals,
            'signatory_name' => 'Somebody Else',
            'signatory_title' => 'Assistant Professor',
            'statement' => 'This certifies that [Learner Name] has successfully completed [Course Code] [Course Name] ([Semester]) under [Instructor Name], [Instructor Title], [Issuer/Institution].',
            'layout_key' => CertificateLayouts::MODERN_ACADEMIC,
        ])->assertSessionHasNoErrors();
        $definition = CertificateDefinition::query()->classCertificates()->firstOrFail();
        $this->assertSame(CertificateDefinition::STATUS_DRAFT, $definition->status);

        // ── Step 5: preview, activate, and issue only to students who completed the class.
        $this->patch(route('instructor.certificates.activate', $definition));
        $this->assertSame(CertificateDefinition::STATUS_DRAFT, $definition->fresh()->status, 'Activation needs a preview first.');
        $preview = $this->get(route('instructor.certificates.show', $definition))->assertOk()->getContent();
        foreach (['Sample Learner', 'CS 101 Python Fundamentals', 'Iris Santos', 'Assistant Professor', 'Northfield University', 'Lea Cruz'] as $text) {
            $this->assertStringContainsString($text, $preview);
        }
        $this->assertStringNotContainsString('Somebody Else', $preview);
        $this->patch(route('instructor.certificates.activate', $definition))->assertSessionHas('success');
        $this->assertSame(CertificateDefinition::STATUS_ACTIVE, $definition->fresh()->status);
        $this->assertSame(0, UserCertificate::count(), 'Activating issues nothing.');

        // Lea completes the module: still one required item left (the quiz).
        $this->authenticateAs($this->learner)->get(route('student.modules.show', $this->pythonFundamentals))->assertOk();
        $this->post(route('student.modules.complete', $this->pythonFundamentals))->assertRedirect();
        $this->get(route('student.gradebook.index'))->assertOk()->assertSee('1 of 2 required items completed');
        $this->authenticateAs($this->instructor)->post(route('instructor.certificates.issue', $definition), ['students' => [$this->learner->id]])->assertSessionHas('error');
        $this->assertSame(0, UserCertificate::count());

        // The quiz is graded: the instructor issues it.
        DB::table('assessment_submissions')->insert([
            'assessment_id' => $this->quiz, 'student_id' => $this->learner->id, 'attempt_no' => 1, 'status' => 'graded', 'score' => 18, 'total_points' => 20,
            'started_at' => now(), 'submitted_at' => now(), 'graded_at' => now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->post(route('instructor.certificates.issue', $definition), ['students' => [$this->learner->id]])->assertSessionHas('success');
        $class = UserCertificate::query()->where('certificate_definition_id', $definition->id)->firstOrFail();
        $snapshot = $class->snapshotData();
        $this->assertSame('CS 101 Python Fundamentals, 1st Semester 2026-2027', $snapshot['module']);
        $this->assertSame('Iris Santos', $snapshot['signatory_name']);
        $this->assertSame('Assistant Professor', $snapshot['signatory_title']);
        $this->assertSame('Northfield University', $snapshot['issuer_name']);
        $this->assertSame(CertificateLayouts::MODERN_ACADEMIC, $snapshot['layout_key']);
        $this->assertSame('This certifies that Lea Cruz has successfully completed CS 101 Python Fundamentals (1st Semester 2026-2027) under Iris Santos, Assistant Professor, Northfield University.', $snapshot['statement']);
        $this->authenticateAs($this->learner);

        // ── Step 6: Core 24 Module Completion, once.
        foreach ($this->coreModules as $position => $module) {
            if ($position < 24) {
                DB::table('module_user')->insert(['user_id' => $this->learner->id, 'module_id' => $module->id, 'is_unlocked' => 1, 'is_completed' => 1, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
        DB::table('module_user')->insert(['user_id' => $this->learner->id, 'module_id' => $this->coreModules[24]->id, 'is_unlocked' => 1, 'is_completed' => 0, 'created_at' => now(), 'updated_at' => now()]);
        $this->assertNull($this->held(CertificateService::CORE_MODULES));
        $this->post(route('lesson.complete', Lesson::query()->where('module_id', $this->coreModules[24]->id)->firstOrFail()))->assertRedirect();
        $this->assertNotNull($this->held(CertificateService::CORE_MODULES));
        $this->get(route('student.certificates.index'))->assertOk();
        $certificates->syncUser($this->learner->fresh());
        $this->assertSame(1, UserCertificate::query()->where('user_id', $this->learner->id)->where('certificate_key', CertificateService::CORE_MODULES)->count());

        // ── Step 7: Core 24 Challenge Completion; the instructor's extra challenge is irrelevant.
        $challenges = $certificates->evaluate($this->learner, CertificateService::CORE_CHALLENGES);
        $this->assertSame(24, $challenges['total']);
        $this->assertNotContains('Extra Loops Quiz', array_column($challenges['items'], 'title'));
        foreach ($this->coreMcq as $position => $challenge) {
            $this->attemptChallenge($this->learner, $challenge, $position === 24 ? 6 : 8, 10);
        }
        $this->assertSame([], array_map(fn ($c) => $c->certificate_key, $certificates->syncUser($this->learner->fresh())), '60% on the last one is not a pass.');
        $this->attemptChallenge($this->learner, $this->coreMcq[24], 7, 10);
        $this->assertSame([CertificateService::CORE_CHALLENGES], array_map(fn ($c) => $c->certificate_key, $certificates->syncUser($this->learner->fresh())));

        // ── Step 8: Core Coding Challenge Completion: only core modules that have one.
        $coding = $certificates->evaluate($this->learner, CertificateService::CORE_CODING);
        $this->assertSame(2, $coding['total'], 'Core Modules 3 to 24 have no Newbie coding challenge.');
        $this->assertNotContains('Extra Python Practice', array_column($coding['items'], 'title'));
        $this->solveChallenge($this->learner, $this->coreCoding[1]);
        $this->assertSame([], $certificates->syncUser($this->learner->fresh()));
        $this->solveChallenge($this->learner, $this->coreCoding[2]);
        $this->assertSame([CertificateService::CORE_CODING], array_map(fn ($c) => $c->certificate_key, $certificates->syncUser($this->learner->fresh())));
        $this->assertCount(2, $this->held(CertificateService::CORE_CODING)->snapshotData()['requirements']);

        // ── Step 9: a coding challenge of the custom module never counts.
        $customCoding = Challenge::create(['challenge_category_id' => $this->newbie->id, 'module_id' => $custom->id, 'title' => 'Financial Data Analytics', 'content_code' => 'M'.$custom->id.'-NEWBIE-CODE', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 25]);
        $this->codingQuestions($customCoding, 2);
        CoreCurriculum::sync();
        $after = $certificates->evaluate($this->roleUser(), CertificateService::CORE_CODING);
        $this->assertSame(2, $after['total']);
        $this->assertSame((int) $coding['set']->version, (int) $after['set']->version);
        $this->assertNotContains($customCoding->content_code, array_column($after['items'], 'content_code'));
        $classmate = $this->roleUser(User::ROLE_USER, ['name' => 'Mia Reyes']);
        $this->solveChallenge($classmate, $this->coreCoding[1]);
        $this->solveChallenge($classmate, $this->coreCoding[2]);
        $this->assertSame([CertificateService::CORE_CODING], array_map(fn ($c) => $c->certificate_key, $certificates->syncUser($classmate)));

        // ── Step 10: My Certificates, PDF, and verification without private data.
        $held = UserCertificate::query()->where('user_id', $this->learner->id)->orderBy('id')->get();
        $this->assertCount(4, $held);
        $this->assertSame(4, $held->pluck('certificate_number')->unique()->count());
        $page = $this->authenticateAs($this->learner)->get(route('student.certificates.index'))->assertOk()->getContent();
        foreach ($held as $certificate) {
            $this->assertStringContainsString($certificate->certificate_number, $page);
            $this->assertStringContainsString(route('student.certificates.show', $certificate->id), $page);
            $this->assertStringContainsString(route('student.certificates.pdf', $certificate->id), $page);
            $this->get(route('student.certificates.show', $certificate->id))->assertOk()->assertSee('Lea Cruz')->assertSee('Download PDF');
            $this->assertStringStartsWith('%PDF-', $this->get(route('student.certificates.pdf', $certificate->id))->assertOk()->getContent());
        }
        foreach (['Core 24 Module Completion', 'Core 24 Challenge Completion', 'Core Coding Challenge Completion', 'Python Fundamentals Completion Certificate', 'CS 101 Python Fundamentals'] as $text) {
            $this->assertStringContainsString($text, $page);
        }
        $this->authenticateAs($classmate)->get(route('student.certificates.pdf', $class->id))->assertNotFound();

        auth()->logout();
        $verified = [];
        foreach ($held as $certificate) {
            $html = $this->get(route('certificates.verify.show', $certificate->certificate_number))->assertOk()->getContent();
            $this->assertStringContainsString('Valid certificate', $html);
            $this->assertStringContainsString('Lea Cruz', $html);
            foreach ([$this->learner->email, 'BSDS 1A', '90%', '18 / 20', 'Assistant Professor'] as $private) {
                $this->assertStringNotContainsString($private, $html);
            }
            $verified[$certificate->id] = $this->verifiedDetails($html);
        }

        // ── Step 11: later edits never change an issued certificate.
        $before = $held->mapWithKeys(fn (UserCertificate $c) => [$c->id => [$c->certificate_number, $c->snapshot, $c->issued_at->toIso8601String()]])->all();
        $views = [];
        $this->authenticateAs($this->learner);
        foreach ($held as $certificate) {
            $views[$certificate->id] = $this->get(route('student.certificates.show', $certificate->id))->getContent();
        }

        $this->authenticateAs($this->admin)->put(route('admin.certificates.settings'), [
            'issuer_name' => 'DataSensei Learning Institute', 'issuer_line' => 'Philippines', 'signatory_name' => 'Dr. Ramon Velasco', 'signatory_title' => 'Director',
            'system_layout' => CertificateLayouts::FORMAL_BORDER, 'core_challenge_level' => 'newbie', 'core_coding_all_levels' => '1',
        ])->assertSessionHasNoErrors();
        $this->patch(route('admin.certificates.layouts.status', CertificateLayouts::MODERN_ACADEMIC))->assertSessionHas('success');
        $this->put(route('admin.modules.update', $custom), ['title' => 'Financial Analytics', 'description' => 'Renamed.', 'year_level' => 'Year 3', 'xp_reward' => 100]);
        DB::table('module_library_items')->where('id', $this->pythonFundamentals)->update(['title' => 'Variables, Types and Operators']);
        $this->class->forceFill(['name' => 'Python Programming', 'term' => '2nd Semester 2026-2027'])->save();
        $this->authenticateAs($this->instructor)->put(route('instructor.certificates.update', $definition), [
            'name' => 'Certificate of Achievement', 'class_id' => $this->class->id,
            'signatory_title' => 'Professor', 'statement' => 'Awarded to [Learner Name].', 'layout_key' => CertificateLayouts::ACADEMIC_CLASSIC,
        ])->assertSessionHasNoErrors();
        $this->assertSame('Certificate of Achievement', $definition->fresh()->name);
        $this->assertSame('Financial Analytics', $custom->fresh()->title);
        $this->coreModules[2]->fresh()->forceFill(['description' => 'Updated core content.'])->save();
        $this->coreCoding[3] = $this->coreCodingChallenge($this->newbie, 3);
        CoreCurriculum::sync();
        // The official coding requirement has a new version (every level, and Core Module 3 now has one).
        $updated = $certificates->evaluate($classmate, CertificateService::CORE_CODING);
        $this->assertSame(4, $updated['total']);
        $this->assertSame((int) $coding['set']->version + 1, (int) $updated['set']->version);
        $this->authenticateAs($this->learner)->get(route('student.certificates.index'))->assertOk();
        $certificates->syncUser($this->learner->fresh());

        $this->assertSame(4, UserCertificate::query()->where('user_id', $this->learner->id)->count(), 'Nothing is issued again.');
        foreach ($held as $certificate) {
            $fresh = $certificate->fresh();
            $this->assertSame($before[$certificate->id], [$fresh->certificate_number, $fresh->snapshot, $fresh->issued_at->toIso8601String()]);
            // The same page, apart from the numbering of the help tooltips.
            $now = $this->get(route('student.certificates.show', $certificate->id))->getContent();
            $this->assertSame($this->withoutTipIds($views[$certificate->id]), $this->withoutTipIds($now));
        }
        auth()->logout();
        foreach ($held as $certificate) {
            $html = $this->get(route('certificates.verify.show', $certificate->certificate_number))->getContent();
            $this->assertSame($verified[$certificate->id], $this->verifiedDetails($html));
        }
        $this->assertSame('CS 101 Python Fundamentals, 1st Semester 2026-2027', $class->fresh()->snapshotData()['module']);
        $this->assertSame(CertificateLayouts::MODERN_ACADEMIC, $class->fresh()->snapshotData()['layout_key']);
        $this->assertSame('DataSensei', $this->held(CertificateService::CORE_MODULES)->snapshotData()['issuer_name']);
    }

    /**
     * Part 3, rule 9: nothing duplicated. One route per page, one menu entry
     * per role, one table for certificate definitions and one for issued
     * certificates.
     */
    public function test_no_duplicate_routes_menu_entries_or_tables(): void
    {
        $seen = [];
        foreach (Route::getRoutes() as $route) {
            foreach ($route->methods() as $method) {
                if ($method === 'HEAD') {
                    continue;
                }
                $key = $method.' '.$route->uri();
                $this->assertArrayNotHasKey($key, $seen, 'Duplicate route '.$key);
                $seen[$key] = true;
            }
        }
        foreach (['student.certificates.index', 'student.certificates.show', 'student.certificates.pdf', 'instructor.certificates.index', 'admin.certificates.index', 'certificates.verify.show'] as $name) {
            $this->assertTrue(Route::has($name), $name);
        }

        $menus = [
            'partials/sidebar.blade.php' => "route('student.certificates.index')",
            'partials/instructor-sidebar.blade.php' => "'instructor.certificates.index'",
            'partials/admin-sidebar.blade.php' => "route('admin.certificates.index')",
        ];
        foreach ($menus as $view => $link) {
            $this->assertSame(1, substr_count((string) file_get_contents(resource_path('views/'.$view)), $link), $view);
        }

        $tables = collect(DB::select("select name from sqlite_master where type = 'table'"))->pluck('name');
        $this->assertSame(['certificate_definitions', 'certificate_layouts', 'certificate_requirement_sets', 'certificate_settings'], $tables->filter(fn ($t) => str_starts_with($t, 'certificate'))->sort()->values()->all());
        $this->assertSame(['user_certificates'], $tables->filter(fn ($t) => str_contains($t, 'certificate') && ! str_starts_with($t, 'certificate'))->values()->all());
    }

    private function held(string $key): ?UserCertificate
    {
        return UserCertificate::query()->where('user_id', $this->learner->id)->where('certificate_key', $key)->active()->first();
    }

    private function withoutTipIds(string $html): string
    {
        return (string) preg_replace('/ds-tip-\d+/', 'ds-tip', $html);
    }

    /** The result block of the public verification page. */
    private function verifiedDetails(string $html): string
    {
        $this->assertSame(1, preg_match('#<section class="vf-result[^"]*".*?</section>#s', $html, $match));

        return $match[0];
    }
}
