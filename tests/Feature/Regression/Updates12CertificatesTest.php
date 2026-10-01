<?php

namespace Tests\Feature\Regression;

use App\Models\Challenge;
use App\Models\ChallengeCategory;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Models\UserCertificate;
use App\Services\CertificateService;
use App\Support\CoreCurriculum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * DataSensei Updates 12, part 5: the three predefined core certificates.
 * Issued only after a server-side eligibility check on existing progress,
 * defined by core identities, never by titles or browser ids, and stable
 * once issued.
 *
 * Fixture: the 24 Core Modules (one lesson each), a custom module, the
 * Newbie MCQ challenge of every core module, Newbie coding challenges for
 * Core Modules 1 and 2 only, the same challenges on another level, an
 * instructor's coding challenge and a custom module's challenge.
 */
class Updates12CertificatesTest extends TestCase
{
    use RefreshDatabase;

    private ChallengeCategory $newbie;

    private ChallengeCategory $university;

    /** @var array<int, Module> position => module */
    private array $modules = [];

    /** @var array<int, Challenge> position => Newbie MCQ */
    private array $mcq = [];

    /** @var array<int, Challenge> position => Newbie coding challenge */
    private array $coding = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildCurriculum();
    }

    public function test_the_three_certificates_require_only_core_content(): void
    {
        $service = app(CertificateService::class);
        $learner = $this->roleUser();

        $modules = $service->evaluate($learner, CertificateService::CORE_MODULES);
        $challenges = $service->evaluate($learner, CertificateService::CORE_CHALLENGES);
        $coding = $service->evaluate($learner, CertificateService::CORE_CODING);

        $this->assertSame(24, $modules['total']);
        $this->assertSame(CoreCurriculum::keys(), array_column($modules['items'], 'module_key'));
        $this->assertSame(24, $challenges['total']);
        $this->assertSame(collect($this->mcq)->pluck('content_code')->values()->all(), array_column($challenges['items'], 'content_code'));
        // Only the core modules that have a Newbie coding challenge.
        $this->assertSame(2, $coding['total']);
        $this->assertSame([$this->coding[1]->content_code, $this->coding[2]->content_code], array_column($coding['items'], 'content_code'));

        foreach ([$modules, $challenges, $coding] as $evaluation) {
            $this->assertTrue($evaluation['issuable']);
            $this->assertFalse($evaluation['eligible']);
            $this->assertSame(1, (int) $evaluation['set']->version);
        }

        $this->assertSame([], $service->syncUser($learner));
        $this->assertSame(0, UserCertificate::count());
    }

    public function test_the_module_certificate_is_issued_when_the_last_core_module_is_completed(): void
    {
        $learner = $this->roleUser();
        foreach ($this->modules as $position => $module) {
            DB::table('module_user')->insert([
                'user_id' => $learner->id, 'module_id' => $module->id, 'is_unlocked' => 1,
                'is_completed' => $position < 24 ? 1 : 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->assertSame(0, UserCertificate::count());

        // Finishing the last lesson of Core Module 24 completes it and the certificate.
        $lesson = Lesson::where('module_id', $this->modules[24]->id)->firstOrFail();
        $this->authenticateAs($learner)->post(route('lesson.complete', $lesson))->assertRedirect();

        $certificate = UserCertificate::where('user_id', $learner->id)->firstOrFail();
        $this->assertSame(CertificateService::CORE_MODULES, $certificate->certificate_key);
        $this->assertSame(1, $certificate->requirement_version);
        $snapshot = $certificate->snapshotData();
        $this->assertSame('Core 24 Module Completion', $snapshot['name']);
        $this->assertCount(24, $snapshot['requirements']);
        $this->assertSame($learner->name, $snapshot['holder']['name']);
        $this->assertDatabaseHas('notifications', ['user_id' => $learner->id, 'type' => 'certificate_earned']);

        // Checking again never issues a second one.
        app(CertificateService::class)->syncUser($learner->fresh());
        $this->assertSame(1, UserCertificate::where('user_id', $learner->id)->count());
    }

    public function test_completing_a_custom_module_never_counts(): void
    {
        $learner = $this->roleUser();
        $custom = Module::where('module_type', 'custom')->firstOrFail();
        foreach ($this->modules as $position => $module) {
            if ($position === 24) {
                continue;
            }
            DB::table('module_user')->insert(['user_id' => $learner->id, 'module_id' => $module->id, 'is_unlocked' => 1, 'is_completed' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }
        DB::table('module_user')->insert(['user_id' => $learner->id, 'module_id' => $custom->id, 'is_unlocked' => 1, 'is_completed' => 1, 'created_at' => now(), 'updated_at' => now()]);

        $evaluation = app(CertificateService::class)->evaluate($learner, CertificateService::CORE_MODULES);
        $this->assertSame(23, $evaluation['done']);
        $this->assertFalse($evaluation['eligible']);
    }

    public function test_the_challenge_certificate_needs_a_pass_on_every_core_newbie_mcq(): void
    {
        $learner = $this->roleUser();
        foreach ($this->mcq as $position => $challenge) {
            if ($position < 24) {
                $this->attempt($learner, $challenge, 7, 10);
            }
        }
        // 69% on the last one is not a pass; a pass on another level does not count.
        $this->attempt($learner, $this->mcq[24], 69, 100);
        $other = Challenge::where('challenge_category_id', $this->university->id)->where('title', 'Sequential Decision Making')->firstOrFail();
        $this->attempt($learner, $other, 10, 10);

        $service = app(CertificateService::class);
        $this->assertSame([], $service->syncUser($learner));
        $this->assertSame(23, $service->evaluate($learner, CertificateService::CORE_CHALLENGES)['done']);

        // A pass on a newer version of the same challenge counts.
        $v2 = Challenge::create([
            'challenge_category_id' => $this->newbie->id, 'content_code' => $this->mcq[24]->content_code, 'title' => 'Sequential Decision Making',
            'description' => 'v2', 'version_no' => 2, 'version_code' => 'V2', 'is_coding_challenge' => false, 'order_index' => 24,
        ]);
        $this->attempt($learner, $v2, 8, 10);

        $issued = app(CertificateService::class)->syncUser($learner);
        $this->assertCount(1, $issued);
        $this->assertSame(CertificateService::CORE_CHALLENGES, $issued[0]->certificate_key);
    }

    public function test_the_coding_certificate_covers_only_built_in_core_coding_challenges_and_stays_stable(): void
    {
        $first = $this->roleUser();
        $second = $this->roleUser();

        // The instructor's coding challenge is never required.
        $this->solve($first, $this->coding[1]);
        $this->assertSame([], app(CertificateService::class)->syncUser($first));
        $this->solve($first, $this->coding[2]);
        $issued = app(CertificateService::class)->syncUser($first);
        $this->assertCount(1, $issued);
        $certificate = $issued[0];
        $this->assertSame(CertificateService::CORE_CODING, $certificate->certificate_key);
        $this->assertSame(1, $certificate->requirement_version);
        $this->assertCount(2, $certificate->snapshotData()['requirements']);

        // Later a coding challenge is added to Core Module 3: a new requirement version.
        $this->coding[3] = $this->codingChallenge($this->newbie, 3);
        CoreCurriculum::sync();
        $service = app(CertificateService::class);
        $evaluation = $service->evaluate($second, CertificateService::CORE_CODING);
        $this->assertSame(2, (int) $evaluation['set']->version);
        $this->assertSame(3, $evaluation['total']);

        // The certificate already issued is unchanged.
        $certificate->refresh();
        $this->assertSame(1, $certificate->requirement_version);
        $this->assertCount(2, $certificate->snapshotData()['requirements']);
        $this->assertSame(1, UserCertificate::where('user_id', $first->id)->count());

        // A learner who solved only the first two now needs the third as well.
        $this->solve($second, $this->coding[1]);
        $this->solve($second, $this->coding[2]);
        $this->assertSame([], app(CertificateService::class)->syncUser($second));
    }

    public function test_certificate_pages_show_progress_and_only_the_holders_own_certificate(): void
    {
        $holder = $this->roleUser();
        $other = $this->roleUser();
        $this->solve($holder, $this->coding[1]);
        $this->solve($holder, $this->coding[2]);

        // Opening the page checks eligibility on the server and issues it.
        $page = $this->authenticateAs($holder)->get(route('student.certificates.index'))->assertOk()->getContent();
        $this->assertStringContainsString('Core 24 Module Completion', $page);
        $this->assertStringContainsString('0 of 24 done', $page);
        $this->assertStringContainsString('Core Coding Challenge Completion', $page);
        $certificate = UserCertificate::where('user_id', $holder->id)->firstOrFail();
        $this->assertStringContainsString(route('student.certificates.show', $certificate->id), $page);

        $this->get(route('student.certificates.show', $certificate->id))->assertOk()
            ->assertSee($holder->name)->assertSee($certificate->certificate_number)->assertSee('Download PDF');

        // Someone else's certificate id: not found.
        $this->authenticateAs($other)->get(route('student.certificates.show', $certificate->id))->assertNotFound();

        // Certificates are for learners: an instructor gets none.
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $this->solve($instructor, $this->coding[1]);
        $this->solve($instructor, $this->coding[2]);
        $this->assertSame([], app(CertificateService::class)->syncUser($instructor));
        $this->assertSame(0, UserCertificate::where('user_id', $instructor->id)->count());
    }

    // ── Fixtures ─────────────────────────────────────────────────────

    private function buildCurriculum(): void
    {
        $this->newbie = ChallengeCategory::create(['name' => 'Newbie', 'slug' => 'newbie', 'target_audience' => 'Beginners', 'description' => 'Newbie', 'order_index' => 1]);
        $this->university = ChallengeCategory::create(['name' => 'University Student', 'slug' => 'university-student', 'target_audience' => 'College', 'description' => 'University', 'order_index' => 2]);

        foreach (CoreCurriculum::MODULES as $index => $entry) {
            $position = $index + 1;
            $module = Module::create([
                'title' => $entry['title'], 'description' => 'Core.', 'order_index' => $position,
                'year_level' => $entry['year'], 'xp_reward' => 100, 'is_published' => true,
            ]);
            Lesson::create(['module_id' => $module->id, 'title' => 'Lesson '.$position, 'content' => '<p>x</p>', 'order_index' => 1]);
            $this->modules[$position] = $module;

            foreach ([$this->newbie, $this->university] as $category) {
                $challenge = Challenge::create([
                    'challenge_category_id' => $category->id, 'title' => $entry['title'], 'description' => 'MCQ',
                    'is_coding_challenge' => false, 'order_index' => $position,
                ]);
                if ($category->is($this->newbie)) {
                    $this->mcq[$position] = $challenge;
                }
            }
        }

        $this->coding[1] = $this->codingChallenge($this->newbie, 1);
        $this->coding[2] = $this->codingChallenge($this->newbie, 2);
        $this->codingChallenge($this->university, 1);

        // Never part of a certificate: a custom module and its challenge, and
        // an instructor's own coding challenge on the Newbie level.
        $custom = Module::create(['title' => 'Workshop', 'description' => 'Custom.', 'order_index' => 25, 'year_level' => 'Year 4', 'xp_reward' => 100, 'is_published' => true]);
        Challenge::create(['challenge_category_id' => $this->newbie->id, 'module_id' => $custom->id, 'title' => 'Workshop', 'content_code' => 'M'.$custom->id.'-NEWBIE-CODE', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 25]);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR);
        $own = Challenge::create(['challenge_category_id' => $this->newbie->id, 'title' => 'Extra Python Practice', 'description' => 'x', 'is_coding_challenge' => true, 'order_index' => 26, 'created_by' => $instructor->id, 'visibility' => 'instructor']);
        DB::table('coding_questions')->insert(['challenge_id' => $own->id, 'title' => 'Extra', 'problem_description' => 'Extra', 'language' => 'python', 'order_index' => 1, 'created_at' => now(), 'updated_at' => now()]);

        CoreCurriculum::sync();
    }

    private function codingChallenge(ChallengeCategory $category, int $position): Challenge
    {
        $challenge = Challenge::create([
            'challenge_category_id' => $category->id, 'title' => CoreCurriculum::MODULES[$position - 1]['title'], 'description' => 'Coding',
            'is_coding_challenge' => true, 'order_index' => $position,
        ]);
        foreach ([1, 2] as $order) {
            DB::table('coding_questions')->insert(['challenge_id' => $challenge->id, 'title' => 'Problem '.$order, 'problem_description' => 'Solve it.', 'language' => 'python', 'order_index' => $order, 'created_at' => now(), 'updated_at' => now()]);
        }

        return $challenge;
    }

    private function attempt(User $learner, Challenge $challenge, int $score, int $total): void
    {
        DB::table('challenge_attempts')->insert([
            'user_id' => $learner->id, 'challenge_id' => $challenge->id, 'attempt_no' => 1, 'mode' => 'practice', 'status' => 'submitted',
            'started_at' => now(), 'submitted_at' => now(), 'time_taken_seconds' => 60, 'score' => $score, 'total_questions' => $total, 'xp_awarded' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function solve(User $learner, Challenge $challenge): void
    {
        foreach (DB::table('coding_questions')->where('challenge_id', $challenge->id)->pluck('id') as $questionId) {
            DB::table('coding_submissions')->insert([
                'user_id' => $learner->id, 'coding_question_id' => $questionId, 'code' => 'print(1)', 'language' => 'python', 'status' => 'passed',
                'tests_passed' => 3, 'tests_total' => 3, 'xp_earned' => 0, 'time_taken_seconds' => 30, 'voided' => false, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }
}
