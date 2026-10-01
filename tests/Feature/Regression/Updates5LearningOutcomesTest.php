<?php

namespace Tests\Feature\Regression;

use App\Models\Assessment;
use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\IntendedLearningOutcome;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\User;
use App\Services\TableOfSpecificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 5, task 4: intended learning outcomes only describe a
 * module. Learners see them as "What You Will Learn" in every module view;
 * nothing measures, weights or links work to them (no ILO mastery page, no
 * ILO fields on assessments, Tables of Specification built from topics).
 */
class Updates5LearningOutcomesTest extends TestCase
{
    use RefreshDatabase;

    public function test_what_you_will_learn_is_shown_in_every_module_view(): void
    {
        $institution = Institution::create(['name' => 'U5 ILO Institution', 'email' => 'u5-ilo@institution.test', 'status' => 'active']);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
        $student = $this->roleUser();
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Data Science', 'section' => 'IT 4A', 'is_archived' => false]);
        $class->students()->attach($student->id, ['enrolled_at' => now()]);

        $library = ModuleLibraryItem::create([
            'module_no' => 3, 'module_code' => 'MOD-003-'.Str::upper(Str::random(4)), 'title' => 'Introduction to Data Science',
            'year_level' => 'Year 1', 'version_no' => 1, 'version_name' => 'Version 1', 'version_code' => 'V1',
            'description' => 'Foundations.', 'estimated_minutes' => 30, 'sort_order' => 3, 'is_active' => true,
            'content_sections' => [
                ['heading' => 'Intended Learning Outcomes', 'key_points' => ['Old section outcome.']],
                ['heading' => 'The Data Lifecycle', 'body' => 'Collect, clean, analyse.'],
            ],
            'mcq_questions' => [],
            'learning_outcomes' => ['Describe the data lifecycle.', 'Name common data roles.'],
        ]);
        ClassModuleAssignment::create(['class_id' => $class->id, 'module_library_item_id' => $library->id, 'status' => 'active', 'assigned_at' => now()]);

        $public = Module::create(['title' => 'Basics of Statistics', 'description' => 'Stats.', 'order_index' => 1, 'year_level' => 'Year 1', 'learning_outcomes' => ['Compute a mean.']]);
        Lesson::create(['module_id' => $public->id, 'title' => 'Means', 'content' => '<p>Average.</p>', 'order_index' => 1]);

        // Instructor library view.
        $this->authenticateAs($instructor)
            ->get(route('modules.module-library.show', $library))
            ->assertOk()
            ->assertSee('What You Will Learn')
            ->assertSee('Describe the data lifecycle.')
            ->assertSee('The Data Lifecycle')
            ->assertDontSee('Old section outcome.');

        // Student class module view.
        $this->authenticateAs($student)
            ->get(route('student.modules.show', ['module' => $library->id, 'class' => $class->id]))
            ->assertOk()
            ->assertSee('What You Will Learn')
            ->assertSee('Name common data roles.');

        // Student Modules page: the DataSensei module card lists its outcomes.
        $this->get(route('modules.index'))
            ->assertOk()
            ->assertSee('What you will learn')
            ->assertSee('Compute a mean.');

        // Student DataSensei module, in the learning room (open on the first lesson).
        $this->get(route('lesson.show', $public))
            ->assertOk()
            ->assertSee('What you will learn')
            ->assertSee('Compute a mean.');
    }

    public function test_there_is_no_ilo_mastery_tracking_left(): void
    {
        $institution = Institution::create(['name' => 'U5 ILO Institution', 'email' => 'u5-ilo2@institution.test', 'status' => 'active']);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);

        $this->assertFalse(Route::has('instructor.mastery.index'));
        $this->assertFileDoesNotExist(app_path('Services/IloMasteryService.php'));
        $this->assertFileDoesNotExist(app_path('Http/Controllers/InstructorMasteryController.php'));

        $this->authenticateAs($instructor)->get('/instructor/mastery')->assertNotFound();
        $this->get(route('instructor.dashboard'))
            ->assertOk()
            ->assertDontSee('ILO Mastery')
            ->assertDontSee('ILO mastery')
            ->assertSee('Class modules');

        $this->authenticateAs($this->roleUser())
            ->get(route('student.analytics.index'))
            ->assertOk()
            ->assertDontSee('ILO mastery')
            ->assertDontSee('No ILO mastery records yet.');

        // Assessment questions are written without ILO links or weights. The
        // admin assessment library that carried this check was removed when
        // assignments were merged into assessments (DataSensei Updates 11);
        // instructors write assessment questions in the assessment builder.
        $this->assertFalse(Route::has('admin.assessments.create'));
        $class = ClassRoom::create(['instructor_id' => $instructor->id, 'name' => 'Outcome Check Class', 'is_archived' => false]);
        $draft = Assessment::create([
            'class_id' => $class->id, 'created_by' => $instructor->id, 'title' => 'Outcome Check', 'status' => 'draft',
            'total_items' => 0, 'total_points' => 0, 'max_attempts' => 1,
        ]);
        foreach ([route('instructor.assessments.new'), route('instructor.assessments.builder', $draft)] as $url) {
            $this->authenticateAs($instructor)
                ->get($url)
                ->assertOk()
                ->assertDontSee('ilo_ids', false)
                ->assertDontSee('name="ilo_id"', false)
                ->assertDontSee('ILO');
        }
    }

    public function test_a_table_of_specification_is_built_from_topics_not_ilos(): void
    {
        $institution = Institution::create(['name' => 'U5 ILO Institution', 'email' => 'u5-ilo3@institution.test', 'status' => 'active']);
        $instructor = $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $institution->id]);
        $module = Module::create(['title' => 'Basics of Python Programming', 'description' => 'Python.', 'order_index' => 1, 'year_level' => 'Year 1']);
        Lesson::create(['module_id' => $module->id, 'title' => 'Variables', 'content' => '<p>x</p>', 'order_index' => 1]);
        Lesson::create(['module_id' => $module->id, 'title' => 'Loops', 'content' => '<p>y</p>', 'order_index' => 2]);
        IntendedLearningOutcome::create(['module_no' => 1, 'ilo_code' => 'M1-ILO1', 'title' => 'An ILO that must not drive the blueprint', 'description' => 'x', 'sort_order' => 1, 'is_active' => true]);

        Auth::login($instructor);
        $tos = app(TableOfSpecificationService::class)->createBlueprint(null, 1, 'Python quiz', 30);

        $this->assertNotEmpty($tos->rows);
        $this->assertTrue($tos->rows->every(fn ($row) => $row->ilo_id === null), 'No row is linked to an ILO.');
        $this->assertSame(['Loops', 'Variables'], $tos->rows->pluck('topic_title')->unique()->sort()->values()->all());
        $this->assertSame(30, (int) $tos->rows->sum('item_count'));
        $this->assertStringNotContainsString('An ILO that must not drive the blueprint', $tos->rows->pluck('topic_title')->implode(' '));
    }

    public function test_the_migration_fills_outcomes_from_existing_content_once(): void
    {
        $make = fn (int $no, string $title, array $sections) => ModuleLibraryItem::create([
            'module_no' => $no, 'module_code' => 'MIG-'.$no.'-'.Str::upper(Str::random(4)), 'title' => $title,
            'year_level' => 'Year 1', 'version_no' => 1, 'version_name' => 'V1', 'version_code' => 'V1',
            'estimated_minutes' => 30, 'sort_order' => $no, 'is_active' => true,
            'content_sections' => $sections, 'mcq_questions' => [],
        ]);
        $ilo = $make(1, 'Python', [['heading' => 'Intended Learning Outcomes', 'key_points' => ['Trace code.', ' ', 'Fix errors.']], ['heading' => 'Basics', 'body' => 'x']]);
        $objectives = $make(2, 'Finance', [['heading' => 'Learning Objectives', 'key_points' => ['Compute returns.']]]);
        $table = $make(3, 'Stats', [['heading' => 'Means', 'body' => 'x']]);
        IntendedLearningOutcome::create(['module_no' => 3, 'ilo_code' => 'M3-1', 'title' => 'Describe data', 'description' => 'Describe a data set with summary statistics.', 'sort_order' => 1, 'is_active' => true]);
        $public = Module::create(['title' => 'Python', 'description' => 'x', 'order_index' => 1, 'year_level' => 'Year 1']);
        $kept = Module::create(['title' => 'Other', 'description' => 'x', 'order_index' => 9, 'year_level' => 'Year 1', 'learning_outcomes' => ['Already written.']]);

        \Illuminate\Support\Facades\DB::table('module_library_items')->update(['learning_outcomes' => null]);
        \Illuminate\Support\Facades\DB::table('modules')->where('id', $public->id)->update(['learning_outcomes' => null]);

        $migration = require database_path('migrations/2026_09_28_000002_add_learning_outcomes_and_publishing_to_modules.php');
        $migration->up();
        $migration->up();

        $this->assertSame(['Trace code.', 'Fix errors.'], $ilo->fresh()->learning_outcomes);
        $this->assertSame(['Compute returns.'], $objectives->fresh()->learning_outcomes);
        $this->assertSame(['Describe a data set with summary statistics.'], $table->fresh()->learning_outcomes);
        $this->assertSame(['Trace code.', 'Fix errors.'], $public->fresh()->learning_outcomes, 'A public module takes the outcomes of the library module with its number and title.');
        $this->assertSame(['Already written.'], $kept->fresh()->learning_outcomes, 'Outcomes already written are left alone.');
        $this->assertTrue($public->fresh()->is_published, 'Existing modules stay published.');
    }
}
