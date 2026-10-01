<?php

namespace Tests\Feature\Regression;

use App\Models\AchievementDefinition;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Models\UserAchievement;
use App\Support\FinalExamAccess;
use Database\Seeders\GamificationSeeder;
use Database\Seeders\Module1LessonsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * DataSensei Updates 7, task 2: the Final Exam of a public DataSensei Module
 * is open to every learner, with or without a class, instructor, university
 * or organization. The "University / Organization Access Only" lock is gone
 * from the seeders, from the stored lessons (migration) and from what the
 * learning room shows; completing the exam completes the module as before.
 * No University/Organization-only achievement is left.
 */
class Updates7PublicFinalExamTest extends TestCase
{
    use RefreshDatabase;

    /** The final exam lesson exactly as the seeders stored it before Updates 7 (two questions kept). */
    private const LOCKED_EXAM = "<div id=\"org-lock-screen\" style=\"text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;\">\r\n"
        ."    <div style=\"font-size:3rem;margin-bottom:1rem;\">🔒</div>\r\n"
        ."    <h3 style=\"color:var(--text);margin-bottom:0.5rem;\">University / Organization Access Only</h3>\r\n"
        ."    <p style=\"color:var(--muted);\">The Final Module Exam is restricted to enrolled students and verified organization members.</p>\r\n"
        ."    <p style=\"font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;\">Please contact administration to link your account to an organization.</p>\r\n"
        ."</div>\r\n"
        ."<div id=\"final-exam-content\" style=\"display:none;\">\r\n"
        ."    <h2>Module 1: Final Examination</h2>\r\n"
        ."    <p>This comprehensive exam covers all topics. Good luck!</p>"
        ."<div class=\"quiz-wrapper\" id=\"wrap_FINAL_EXAM\"><div class=\"quiz-score-bar\"><span>Knowledge Check</span><span class=\"quiz-score-val\"><span id=\"score_FINAL_EXAM\">0</span> / 2</span></div>"
        ."<div class=\"quiz-card\" id=\"FINAL_EXAM_q1\"><div class=\"quiz-card-header\"><span class=\"quiz-q-num\">Q1</span><span class=\"quiz-q-text\">What does len([1, 2, 3]) return?</span></div><div class=\"quiz-options\">"
        ."<button class=\"quiz-option\" onclick=\"checkAnswer(this,'FINAL_EXAM_q1',true,'FINAL_EXAM')\"><span class=\"opt-key\">A</span> 3</button>"
        ."<button class=\"quiz-option\" onclick=\"checkAnswer(this,'FINAL_EXAM_q1',false,'FINAL_EXAM')\"><span class=\"opt-key\">B</span> 2</button></div>"
        ."<div class=\"quiz-explanation\" id=\"FINAL_EXAM_q1-exp\"><strong>Explanation:</strong> Three items.</div></div>"
        ."<div class=\"quiz-card\" id=\"FINAL_EXAM_q2\"><div class=\"quiz-card-header\"><span class=\"quiz-q-num\">Q2</span><span class=\"quiz-q-text\">Which keyword defines a function?</span></div><div class=\"quiz-options\">"
        ."<button class=\"quiz-option\" onclick=\"checkAnswer(this,'FINAL_EXAM_q2',false,'FINAL_EXAM')\"><span class=\"opt-key\">A</span> func</button>"
        ."<button class=\"quiz-option\" onclick=\"checkAnswer(this,'FINAL_EXAM_q2',true,'FINAL_EXAM')\"><span class=\"opt-key\">B</span> def</button></div>"
        ."<div class=\"quiz-explanation\" id=\"FINAL_EXAM_q2-exp\"><strong>Explanation:</strong> Python uses def.</div></div></div>\r\n"
        ."</div><script>\r\n"
        ."document.addEventListener('DOMContentLoaded', function() {\r\n"
        ."    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {\r\n"
        ."        document.getElementById('org-lock-screen').style.display = 'none';\r\n"
        ."        document.getElementById('final-exam-content').style.display = 'block';\r\n"
        ."    }\r\n"
        ."});\r\n"
        ."</script>\r\n";

    public function test_the_lock_is_removed_and_the_exam_is_kept_byte_for_byte(): void
    {
        $open = FinalExamAccess::open(self::LOCKED_EXAM);

        foreach (['org-lock-screen', 'final-exam-content', 'USER_ORG_ID', 'University / Organization', 'display:none;">', 'contact administration'] as $gone) {
            $this->assertStringNotContainsString($gone, $open);
        }

        // Title, introduction, both questions, answers and explanations stay.
        $this->assertStringStartsWith("    <h2>Module 1: Final Examination</h2>\r\n", $open);
        $examStart = strpos(self::LOCKED_EXAM, '    <h2>Module 1');
        $examEnd = strpos(self::LOCKED_EXAM, "</div></div></div>\r\n") + strlen("</div></div></div>\r\n");
        $this->assertSame(substr(self::LOCKED_EXAM, $examStart, $examEnd - $examStart), $open);
        $this->assertSame($open, FinalExamAccess::open($open), 'Opening twice changes nothing.');

        // Lesson HTML without the lock is returned exactly as it is.
        $plain = "<h2>Loops</h2>\n<script>console.log('display:none');</script>";
        $this->assertSame($plain, FinalExamAccess::open($plain));
        $this->assertNull(FinalExamAccess::open(null));

        // Hand-edited HTML whose lock screen is never closed still shows the exam.
        $broken = '<div id="org-lock-screen" style="text-align:center"><p>Locked</p><div id="final-exam-content" style="display:none;"><h2>Exam</h2><p>Q</p>';
        $this->assertSame('<div><h2>Exam</h2><p>Q</p>', FinalExamAccess::open($broken));
    }

    public function test_a_learner_without_class_instructor_or_institution_takes_and_submits_the_final_exam(): void
    {
        [$module, $lesson, $exam] = $this->moduleWithLockedExam();
        $learner = $this->roleUser(User::ROLE_USER, ['institution_id' => null]);
        $this->assertNull($learner->institution_id);
        $this->assertFalse(DB::table('class_student')->where('student_id', $learner->id)->exists());

        $this->authenticateAs($learner)
            ->get(route('lesson.show', ['module' => $module->id, 'lesson' => $exam->id]))
            ->assertOk()
            ->assertSee('Module 1: Final Examination')
            ->assertSee('Which keyword defines a function?')
            ->assertDontSee('University / Organization Access Only')
            ->assertDontSee('org-lock-screen', false)
            ->assertDontSee('USER_ORG_ID', false)
            ->assertDontSee('id="final-exam-content" style="display:none;"', false)
            // The result is shown once every question is answered.
            ->assertSee('page-learning-quiz-result', false)
            ->assertSee('Mark as Complete & Continue', false);

        // Submitting: every lesson done, the exam last, completes the module.
        $this->post(route('lesson.complete', $lesson))->assertRedirect(route('lesson.show', ['module' => $module->id, 'lesson' => $exam->id]));
        $this->post(route('lesson.complete', $exam))
            ->assertRedirect(route('modules.index'))
            ->assertSessionHas('success', 'Module Completed!');

        $this->assertTrue((bool) DB::table('lesson_user')->where('user_id', $learner->id)->where('lesson_id', $exam->id)->value('is_completed'));
        $this->assertTrue((bool) DB::table('module_user')->where('user_id', $learner->id)->where('module_id', $module->id)->value('is_completed'));
    }

    public function test_the_migration_opens_stored_final_exams_and_leaves_other_lessons_alone(): void
    {
        [$module, $lesson, $exam] = $this->moduleWithLockedExam();
        $blocksExam = Lesson::create(['module_id' => $module->id, 'title' => 'Edited Exam', 'order_index' => 3, 'content' => self::LOCKED_EXAM]);
        DB::table('lessons')->where('id', $blocksExam->id)->update(['blocks' => json_encode([
            ['type' => 'heading', 'text' => 'Final'],
            ['type' => 'preserved', 'label' => 'Original formatting', 'html' => self::LOCKED_EXAM],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]);
        $before = DB::table('lessons')->where('id', $lesson->id)->first();

        // The model never shows the lock, even before the migration.
        $this->assertStringNotContainsString('org-lock-screen', Lesson::find($exam->id)->content);
        $this->assertStringNotContainsString('org-lock-screen', (string) Lesson::find($blocksExam->id)->blocks);

        $migration = require database_path('migrations/2026_09_28_000003_open_module_final_exams_to_everyone.php');
        $migration->up();

        $stored = DB::table('lessons')->where('id', $exam->id)->value('content');
        $this->assertSame(FinalExamAccess::open(self::LOCKED_EXAM), $stored);
        $this->assertStringContainsString('Which keyword defines a function?', $stored);
        $blocks = json_decode(DB::table('lessons')->where('id', $blocksExam->id)->value('blocks'), true);
        $this->assertSame(FinalExamAccess::open(self::LOCKED_EXAM), $blocks[1]['html']);
        $this->assertStringNotContainsString('org-lock-screen', DB::table('lessons')->where('id', $blocksExam->id)->value('content'));

        $after = DB::table('lessons')->where('id', $lesson->id)->first();
        $this->assertSame($before->content, $after->content);
        $this->assertSame($before->updated_at, $after->updated_at);

        $snapshot = DB::table('lessons')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
        $migration->up();
        $this->assertSame($snapshot, DB::table('lessons')->orderBy('id')->get()->map(fn ($row) => (array) $row)->all(), 'Running it again changes nothing.');
    }

    public function test_the_admin_builder_shows_the_final_exam_as_editable_blocks(): void
    {
        [$module, , $exam] = $this->moduleWithLockedExam();

        $html = $this->authenticateAs($this->roleUser(User::ROLE_ADMIN))
            ->get(route('admin.modules.edit', $module))
            ->assertOk()
            ->getContent();

        $this->assertSame(1, preg_match('#<script type="application/json" id="module-editor-data">(.*?)</script>#s', $html, $match));
        $section = collect(json_decode($match[1], true)['sections'])->firstWhere('id', $exam->id);
        $this->assertSame(['heading', 'paragraph', 'quiz'], array_column($section['blocks'], 'type'));
        $this->assertCount(2, $section['blocks'][2]['questions']);
        $this->assertSame(1, $section['blocks'][2]['questions'][1]['answer']);
        $this->assertStringNotContainsString('org-lock-screen', $match[1]);
    }

    public function test_every_module_seeder_builds_its_final_exam_without_the_lock(): void
    {
        $files = glob(database_path('seeders/Module*LessonsSeeder.php'));
        $this->assertCount(24, $files);

        foreach ($files as $file) {
            $source = file_get_contents($file);
            foreach (['org-lock-screen', 'final-exam-content', 'USER_ORG_ID', 'Organization Access', 'Organization Required', 'link your account to an organi', 'Org-locked', 'Org-Locked'] as $gone) {
                $this->assertStringNotContainsString($gone, $source, basename($file).' still contains '.$gone);
            }
        }

        Module::create(['title' => 'Python Programming', 'description' => 'Python.', 'order_index' => 1, 'year_level' => 'Year 1', 'xp_reward' => 100]);
        $this->seed(Module1LessonsSeeder::class);

        $exam = Lesson::where('title', '1.11 Final Exam: Python Mastery')->firstOrFail();
        $raw = DB::table('lessons')->where('id', $exam->id)->value('content');
        $this->assertStringStartsWith('<h2>Module 1: Final Examination</h2>', $raw);
        $this->assertFalse(FinalExamAccess::isRestricted($raw));
        $this->assertStringContainsString('id="wrap_FINAL_EXAM"', $raw);
    }

    public function test_no_frontend_organization_flag_is_left(): void
    {
        foreach (['resources/views', 'public/js', 'app', 'routes'] as $path) {
            $matches = [];
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($path), \FilesystemIterator::SKIP_DOTS)) as $file) {
                if ($file->isFile() && str_contains(file_get_contents($file->getPathname()), 'USER_ORG_ID') && ! str_ends_with($file->getPathname(), 'FinalExamAccess.php')) {
                    $matches[] = $file->getPathname();
                }
            }
            $this->assertSame([], $matches, 'USER_ORG_ID is still used in '.$path);
        }
    }

    public function test_university_only_achievements_are_removed_unless_earned(): void
    {
        $unearned = AchievementDefinition::create([
            'achievement_key' => 'path_university_student_complete', 'name' => 'University Path Clear', 'description' => 'University path.',
            'icon' => 'U', 'badge_color' => 'blue', 'xp_reward' => 140, 'criteria_type' => 'path_complete', 'criteria_value' => 1, 'is_active' => false, 'sort_order' => 1,
        ]);
        $earned = AchievementDefinition::create([
            'achievement_key' => 'coding_path_university_student_complete', 'name' => 'University Coding Path Clear', 'description' => 'University coding path.',
            'icon' => 'U', 'badge_color' => 'blue', 'xp_reward' => 170, 'criteria_type' => 'coding_path_complete', 'criteria_value' => 1, 'is_active' => true, 'sort_order' => 2,
        ]);
        $normal = AchievementDefinition::create([
            'achievement_key' => 'module_finisher', 'name' => 'Module Finisher', 'description' => 'Complete five lessons.',
            'icon' => 'MF', 'badge_color' => 'blue', 'xp_reward' => 120, 'criteria_type' => 'lesson_completions', 'criteria_value' => 5, 'is_active' => true, 'sort_order' => 3,
        ]);
        $learner = $this->roleUser(User::ROLE_USER);
        UserAchievement::create(['user_id' => $learner->id, 'achievement_definition_id' => $earned->id, 'unlocked_at' => now()]);

        $migration = require database_path('migrations/2026_09_28_000004_remove_university_only_achievements.php');
        $migration->up();
        $migration->up();

        $this->assertNull(AchievementDefinition::find($unearned->id));
        $this->assertFalse((bool) $earned->fresh()->is_active, 'An earned one stays in the record, switched off.');
        $this->assertSame(1, UserAchievement::where('achievement_definition_id', $earned->id)->count());
        $this->assertTrue((bool) $normal->fresh()->is_active, 'Normal module completion rewards stay.');

        // Seeding again never brings them back.
        $this->seed(GamificationSeeder::class);
        $this->assertFalse(AchievementDefinition::where('achievement_key', 'path_university_student_complete')->exists());
        $this->assertSame(0, AchievementDefinition::where('name', 'like', '%University%')->where('is_active', true)->count());
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    /** @return array{0: Module, 1: Lesson, 2: Lesson} */
    private function moduleWithLockedExam(): array
    {
        $module = Module::create([
            'title' => 'Python Programming',
            'description' => 'Python basics.',
            'order_index' => 1,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'learning_outcomes' => ['Write short Python programs.'],
        ]);
        $lesson = Lesson::create(['module_id' => $module->id, 'title' => '1.1 Variables', 'order_index' => 1, 'content' => '<h2>Variables</h2><p>Names for values.</p>']);
        $exam = Lesson::create(['module_id' => $module->id, 'title' => '1.11 Final Exam: Python Mastery', 'order_index' => 2, 'content' => self::LOCKED_EXAM]);

        return [$module, $lesson, $exam];
    }
}
