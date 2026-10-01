<?php

namespace Tests\Feature\Regression;

use App\Models\AssignmentLibraryItem;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\ModuleLibraryItem;
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

    public function test_admin_assessment_pages_never_show_code_names(): void
    {
        $item = $this->libraryItem('ASN-001-PYTHON-FIB', 'PYTHON-FIB-V1');
        $admin = $this->roleUser(User::ROLE_ADMIN);

        foreach ([
            route('admin.assessments.index'),
            route('admin.assessments.show', $item),
            route('admin.assessments.edit', $item),
            route('admin.assessments.create'),
        ] as $url) {
            $html = $this->authenticateAs($admin)->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('ASN-001-PYTHON-FIB', $html, $url);
            $this->assertStringNotContainsString('PYTHON-FIB-V1', $html, $url);
            $this->assertStringNotContainsString('name="assignment_code"', $html, $url);
            $this->assertStringNotContainsString('name="version_code"', $html, $url);
            $this->assertDoesNotMatchRegularExpression('/>\s*V\d+\s*</', $html, $url);
        }

        // Instructors choosing an assessment see titles only.
        $html = $this->authenticateAs($this->instructor())
            ->get(route('instructor.assignments.create'))
            ->assertOk()
            ->assertSee('Python Fill In The Blanks')
            ->getContent();
        $this->assertStringNotContainsString('ASN-001-PYTHON-FIB', $html);
        $this->assertStringNotContainsString('PYTHON-FIB-V1', $html);
    }

    public function test_admin_creates_and_copies_assessments_without_typing_codes(): void
    {
        $admin = $this->roleUser(User::ROLE_ADMIN);

        $this->authenticateAs($admin)
            ->post(route('admin.assessments.store'), [
                'module_no' => 3,
                'title' => 'Descriptive Statistics Check',
                'topic_title' => 'Descriptive Statistics',
                'year_level' => 'First Year',
                'assignment_type' => 'mcq',
                'time_limit_minutes' => 20,
                'questions' => [[
                    'question_type' => 'mcq',
                    'question_text' => 'Which value is the middle one?',
                    'points' => 2,
                    'correct_option' => 1,
                    'options' => [['option_text' => 'Mean'], ['option_text' => 'Median']],
                ]],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', 'Assessment created.');

        $created = AssignmentLibraryItem::where('title', 'Descriptive Statistics Check')->firstOrFail();
        $this->assertMatchesRegularExpression('/^ASN-003-[A-Z0-9]{6}$/', $created->assignment_code);
        $this->assertSame(1, (int) $created->version_no);
        $this->assertSame('V1', $created->version_code);

        $this->authenticateAs($admin)
            ->post(route('admin.assessments.duplicate', $created))
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', 'A copy was made. It is inactive until you publish it.');

        $copy = AssignmentLibraryItem::where('title', 'Descriptive Statistics Check (copy)')->firstOrFail();
        $this->assertNotSame($created->assignment_code, $copy->assignment_code);
        $this->assertSame(2, (int) $copy->version_no);
        $this->assertFalse((bool) $copy->is_active);
        $this->assertSame(1, $copy->questions()->count());
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
        $views = [
            'instructor/dashboard', 'instructor/assessments/index', 'instructor/assessments/builder', 'instructor/assessments/create',
            'instructor/assessments/preview', 'instructor/assessments/submissions', 'instructor/assessments/analytics',
            'instructor/assignments/index', 'instructor/assignments/show', 'instructor/assignments/preview', 'instructor/assignments/create',
            'instructor/tos/index', 'instructor/tos/show', 'instructor/tos/review', 'instructor/submissions/index',
            'instructor/classes/classes', 'instructor/classes/students', 'instructor/challenges/index', 'instructor/challenges/show',
            'instructor/modules/module_list', 'student/assessments/index', 'student/assessments/take', 'student/assignments/index',
            'student/assignments/class', 'student/assignments/show', 'student/assignments/take', 'student/assignments/result',
            'student/submissions/index', 'student/profile', 'student/challenges', 'student/coding-challenges',
            'admin/assessments/index', 'admin/assessments/show', 'admin/challenges/index', 'admin/challenges/show',
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

    private function libraryItem(string $code, string $versionCode): AssignmentLibraryItem
    {
        $item = AssignmentLibraryItem::create([
            'module_no' => 1,
            'assignment_code' => $code,
            'title' => 'Python Fill In The Blanks',
            'topic_title' => 'Python Basics',
            'year_level' => 'First Year',
            'assignment_type' => 'fill_blank',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => $versionCode,
            'time_limit_minutes' => 15,
            'total_points' => 1,
            'is_active' => true,
        ]);
        $question = $item->questions()->create([
            'question_type' => 'fill_blank',
            'question_text' => 'The library for data frames is ____.',
            'points' => 1,
            'order_index' => 1,
        ]);
        $question->blankAnswers()->create(['answer_text' => 'pandas', 'is_case_sensitive' => false]);

        return $item;
    }
}
