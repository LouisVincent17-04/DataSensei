<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\ModuleLibraryItem;
use App\Models\QuestionBankItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 9, tasks 1, 5, 10, 11 and 13: removed instructor pages
 * stay removed, internal code names never reach the screen, and the
 * profile, class and roster pages use plain text instead of pills.
 *
 * DataSensei Updates 11 merged assignments into assessments: the admin
 * assessment (assignment) library and the assignment pages are gone. The
 * library lives on as the shared Question Bank pool, and instructors build
 * assessments themselves, so the code-name and plain-text checks follow the
 * work to those pages.
 */
class Updates9CleanupTest extends TestCase
{
    use RefreshDatabase;

    private const PILL = '/class="[^"]*\b[\w-]*(pill|badge|chip|capsule)[\w-]*\b/i';

    public function test_instructor_student_model_development_is_removed_and_the_student_feature_stays(): void
    {
        $this->assertFalse(Route::has('instructor.model-development.index'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/InstructorMlDashboardController.php'));
        $this->assertDirectoryDoesNotExist(resource_path('views/instructor/model-development'));

        $instructor = $this->instructor();
        $this->authenticateAs($instructor)->get('/instructor/model-development')->assertNotFound();
        $this->authenticateAs($instructor)
            ->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertDontSee('Model Development')
            ->assertDontSee('/instructor/model-development', false);

        $this->assertTrue(Route::has('student.model-development.index'));
        $this->authenticateAs($this->roleUser(User::ROLE_USER))
            ->get(route('student.model-development.index'))
            ->assertOk();
    }

    public function test_the_separate_class_challenges_page_is_removed(): void
    {
        foreach (['index', 'store', 'update', 'destroy'] as $name) {
            $this->assertFalse(Route::has('instructor.class-challenges.'.$name), $name);
        }
        $this->assertFileDoesNotExist(app_path('Http/Controllers/InstructorClassChallengeController.php'));
        $this->assertTrue(Route::has('instructor.challenges.classes.update'));

        $this->authenticateAs($this->instructor())
            ->get('/instructor/class-challenges')
            ->assertNotFound();
    }

    public function test_the_admin_assessment_library_is_removed_and_its_successors_never_show_code_names(): void
    {
        foreach (['index', 'show', 'edit', 'create', 'store', 'update', 'destroy', 'duplicate', 'status'] as $name) {
            $this->assertFalse(Route::has('admin.assessments.'.$name), $name);
        }
        $this->assertFileDoesNotExist(app_path('Http/Controllers/AdminAssessmentContentController.php'));
        $this->assertDirectoryDoesNotExist(resource_path('views/admin/assessments'));
        $this->authenticateAs($this->roleUser(User::ROLE_ADMIN))->get('/admin/assessments')->assertNotFound();

        // The old library became the shared Question Bank pool; instructors
        // build assessments from it. None of those pages carries a code.
        $shared = $this->sharedBankItem();
        $instructor = $this->instructor();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Code Name Class', 'is_archived' => false]);
        $draft = Assessment::create([
            'class_id' => $class->id, 'created_by' => $instructor->id, 'title' => 'Draft Check', 'status' => 'draft',
            'total_items' => 0, 'total_points' => 0, 'max_attempts' => 1,
        ]);

        foreach ([
            route('instructor.question-bank.index'),
            route('instructor.assessments.new'),
            route('instructor.assessments.builder', $draft),
            route('instructor.assessments.bank', $draft),
        ] as $url) {
            $html = $this->authenticateAs($instructor)->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('name="assignment_code"', $html, $url);
            $this->assertStringNotContainsString('name="version_code"', $html, $url);
            $this->assertStringNotContainsString('name="module_code"', $html, $url);
            $this->assertDoesNotMatchRegularExpression('/>\s*V\d+\s*</', $html, $url);
        }

        // Instructors choosing shared questions see the topic and the question.
        $this->authenticateAs($instructor)
            ->get(route('instructor.assessments.bank', $draft))
            ->assertOk()
            ->assertSee('Python Fill In The Blanks')
            ->assertSee($shared->question_text)
            ->assertSee('Shared pool');
    }

    public function test_instructors_create_assessments_and_copy_shared_questions_without_typing_codes(): void
    {
        $instructor = $this->instructor();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Statistics', 'is_archived' => false]);
        $shared = $this->sharedBankItem();

        $this->authenticateAs($instructor)
            ->post(route('instructor.assessments.save'), [
                'class_id' => $class->id,
                'title' => 'Descriptive Statistics Check',
                'topic_title' => 'Descriptive Statistics',
                'purpose' => 'quiz',
                'time_limit_minutes' => 20,
                'max_attempts' => 1,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', 'Assessment saved as a draft. Add your questions below.');

        $created = Assessment::where('title', 'Descriptive Statistics Check')->firstOrFail();
        $this->assertSame('draft', $created->status);
        $this->assertSame('quiz', $created->purpose);
        foreach (['assignment_code', 'version_code', 'version_no'] as $code) {
            $this->assertArrayNotHasKey($code, $created->getAttributes(), $code);
        }

        // Copying from the shared pool (the former library) needs no code either.
        $this->authenticateAs($instructor)
            ->post(route('instructor.assessments.bank.add', $created), ['question_ids' => [$shared->id]])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', '1 question added.');

        $copy = $created->questions()->firstOrFail();
        $this->assertSame($shared->question_text, $copy->question_text);
        $this->assertSame('fill_blank', $copy->question_type);
        $this->assertSame('pandas', $copy->correct_answer);
        $this->assertSame(1, QuestionBankItem::whereNull('instructor_id')->count(), 'The shared question is copied, not moved.');
    }

    public function test_admin_module_library_shows_titles_and_versions_not_code_names(): void
    {
        ModuleLibraryItem::query()->delete();
        $module = ModuleLibraryItem::create([
            'module_no' => 1,
            'module_code' => 'PYTHON-BASIC',
            'title' => 'Programming Basics',
            'year_level' => 'Year 1',
            'version_no' => 1,
            'version_name' => 'Version 1: First Steps',
            'version_code' => 'PYTHON-BASIC-V1',
            'description' => 'Variables and loops.',
            'content_sections' => [['heading' => 'Variables', 'body' => 'A name for a value.']],
            'mcq_questions' => [],
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $admin = $this->roleUser(User::ROLE_ADMIN);

        foreach ([route('admin.module-library.index'), route('admin.module-library.show', $module), route('admin.module-library.edit', $module)] as $url) {
            $html = $this->authenticateAs($admin)->get($url)->assertOk()->assertSee('Programming Basics')->getContent();
            $this->assertStringNotContainsString('PYTHON-BASIC', $html, $url);
            $this->assertStringNotContainsString('module code', strtolower(strip_tags($html)), $url);
        }

        // A new version is made without typing any code.
        $this->authenticateAs($admin)
            ->post(route('admin.module-library.duplicate', $module), ['version_no' => 2, 'version_name' => 'Version 2', 'is_active' => 0])
            ->assertSessionHasNoErrors()
            ->assertRedirect();
        $copy = ModuleLibraryItem::where('module_no', 1)->where('version_no', 2)->firstOrFail();
        $this->assertSame('PROGRAMMING-BASICS-V2', $copy->module_code);
        $this->assertSame('V2', $copy->version_code);
        $this->assertSame('Programming Basics', $copy->title);
    }

    public function test_profile_uses_plain_text_instead_of_pills(): void
    {
        $student = $this->roleUser(User::ROLE_USER);

        $html = $this->authenticateAs($student)->get(route('profile'))->assertOk()->getContent();
        $main = substr($html, (int) strpos($html, '<main'));
        $this->assertDoesNotMatchRegularExpression(self::PILL, $main);
        $this->assertStringNotContainsString('var(--ds-gradient-brand)', $html);
    }

    public function test_class_list_and_roster_use_plain_text_instead_of_pills(): void
    {
        $instructor = $this->instructor();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Roster Class', 'is_archived' => false]);
        $student = $this->roleUser(User::ROLE_USER, ['name' => 'Roster Student']);
        DB::table('class_student')->insert([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roster = $this->authenticateAs($instructor)
            ->get(route('instructor.classes.students', $class))
            ->assertOk()
            ->assertSee('Roster Student')
            ->assertSee('Class code: '.$class->class_code)
            ->getContent();
        $this->assertDoesNotMatchRegularExpression(self::PILL, substr($roster, (int) strpos($roster, '<main')));

        $list = $this->authenticateAs($instructor)
            ->get(route('instructor.classes.index'))
            ->assertOk()
            ->assertSee('Roster Class')
            ->assertSee('Code: '.$class->class_code)
            ->getContent();
        $this->assertDoesNotMatchRegularExpression(self::PILL, substr($list, (int) strpos($list, '<main')));
    }

    public function test_pages_changed_in_this_update_use_plain_text_labels(): void
    {
        // The assignment and admin assessment views were removed in DataSensei
        // Updates 11; their successors are the assessment, Question Bank and
        // submission pages listed below.
        foreach ([
            'instructor/assignments/index', 'instructor/assignments/show', 'instructor/assignments/preview', 'instructor/assignments/create',
            'student/assignments/index', 'student/assignments/class', 'student/assignments/show', 'student/assignments/take',
            'student/assignments/result', 'admin/assessments/index', 'admin/assessments/show',
        ] as $removed) {
            $this->assertFileDoesNotExist(resource_path('views/'.$removed.'.blade.php'), $removed);
        }

        $views = [
            'instructor/dashboard', 'instructor/assessments/index', 'instructor/assessments/builder', 'instructor/assessments/create',
            'instructor/assessments/preview', 'instructor/assessments/submissions', 'instructor/assessments/submission-show',
            'instructor/assessments/analytics', 'instructor/assessments/bank', 'instructor/question-bank/index',
            'instructor/question-bank/_item_row', 'instructor/question-bank/_item_form',
            'instructor/tos/index', 'instructor/tos/show', 'instructor/tos/review', 'instructor/submissions/index',
            'instructor/classes/classes', 'instructor/classes/students', 'instructor/challenges/index', 'instructor/challenges/show',
            'instructor/modules/module_list', 'student/assessments/index', 'student/assessments/class', 'student/assessments/show',
            'student/assessments/take', 'student/assessments/result',
            'student/submissions/index', 'student/profile', 'student/challenges', 'student/coding-challenges',
            'admin/challenges/index', 'admin/challenges/show',
            'admin/gamification/index', 'superadmin/dashboard',
        ];

        foreach ($views as $view) {
            $source = (string) file_get_contents(resource_path('views/'.$view.'.blade.php'));
            $this->assertDoesNotMatchRegularExpression(self::PILL, $source, $view);
            $this->assertStringNotContainsString('gradient(', $source, $view);
        }
    }

    private function instructor(): User
    {
        $institution = Institution::create([
            'name' => 'Updates9 Cleanup '.Str::random(4),
            'email' => 'u9-cleanup-'.Str::lower(Str::random(8)).'@institution.test',
            'status' => 'active',
        ]);

        return $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
    }

    /** A question in the shared pool (seeded from the old assignment library). */
    private function sharedBankItem(): QuestionBankItem
    {
        return QuestionBankItem::create([
            'instructor_id' => null,
            'module_no' => 1,
            'topic_title' => 'Python Fill In The Blanks',
            'question_type' => 'fill_blank',
            'question_text' => 'The library for data frames is ____.',
            'correct_answer' => 'pandas',
            'points' => 1,
            'is_archived' => false,
        ]);
    }
}
