<?php

namespace Tests\Feature\Regression;

use App\Models\ClassModuleAssignment;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\Module;
use App\Models\ModuleLibraryItem;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * DataSensei Updates 5, task 3: a module an instructor assigns to a class
 * shows on the student's Modules page, under "Module source" = that class.
 * The notification, the list and the module page all use the same
 * class_module_assignments rows. DataSensei Modules stay the default and
 * keep their year levels; class modules have none.
 */
class Updates5ClassModulesTest extends TestCase
{
    use RefreshDatabase;

    private ?Institution $institution = null;

    public function test_an_assigned_module_appears_where_the_notification_points(): void
    {
        $instructor = $this->instructor();
        $class = $this->classFor($instructor, 'Data Science', 'IT 4A');
        $student = $this->enrolledStudent($class);
        Module::create(['title' => 'Basics of Python Programming', 'description' => 'Public', 'order_index' => 1, 'year_level' => 'Year 1']);
        $library = $this->libraryModule('Financial Data Analytics', ['Read a balance sheet.', 'Compute simple returns.']);

        $this->authenticateAs($instructor)
            ->post(route('modules.module-library.assign'), [
                'class_id' => $class->id,
                'selected_modules' => [$library->module_no => $library->id],
            ])
            ->assertSessionHas('success');

        $notification = Notification::where('user_id', $student->id)->where('type', 'modules_assigned')->firstOrFail();
        $classUrl = route('modules.index', ['source' => 'class-'.$class->id]);
        $this->assertSame('/module?source=class-'.$class->id, $notification->action_url, 'Stored as a relative link to the class modules.');

        // The page the notification opens lists the module, without a year.
        $this->authenticateAs($student)
            ->get($classUrl)
            ->assertOk()
            ->assertSee('IT 4A – Data Science')
            ->assertSee('Financial Data Analytics')
            ->assertSee('What you will learn')
            ->assertSee('Read a balance sheet.')
            ->assertDontSee('First Year')
            ->assertDontSee('Basics of Python Programming')
            ->assertSee(route('student.modules.show', ['module' => $library->id, 'class' => $class->id]), false);

        // Opening it shows the content and What You Will Learn.
        $this->get(route('student.modules.show', ['module' => $library->id, 'class' => $class->id]))
            ->assertOk()
            ->assertSee('What You Will Learn')
            ->assertSee('Compute simple returns.')
            ->assertSee('Balance sheets');
    }

    public function test_datasensei_modules_are_the_default_with_year_levels(): void
    {
        $class = $this->classFor($this->instructor(), 'Data Science', 'IT 4A');
        $student = $this->enrolledStudent($class);
        Module::create(['title' => 'Basics of Python Programming', 'description' => 'Public', 'order_index' => 1, 'year_level' => 'Year 1']);
        $library = $this->libraryModule('Financial Data Analytics');
        $this->assign($class, $library);

        $this->authenticateAs($student)
            ->get(route('modules.index'))
            ->assertOk()
            ->assertSee('Module source')
            ->assertSee('<option value="datasensei" selected>DataSensei Modules</option>', false)
            ->assertSee('IT 4A – Data Science')
            ->assertSee('First Year')
            ->assertSee('Basics of Python Programming')
            ->assertDontSee('Financial Data Analytics');

        // A learner with no class sees the DataSensei modules only.
        $this->authenticateAs($this->roleUser())
            ->get(route('modules.index'))
            ->assertOk()
            ->assertSee('Basics of Python Programming')
            ->assertDontSee('Module source');
    }

    public function test_students_only_see_modules_of_their_own_current_classes(): void
    {
        $instructor = $this->instructor();
        $mine = $this->classFor($instructor, 'Data Science', 'IT 4A');
        $other = $this->classFor($instructor, 'Advanced Analytics', 'IT 4B');
        $archived = $this->classFor($instructor, 'Old Class', 'IT 3A');
        $student = $this->enrolledStudent($mine);
        $archived->students()->attach($student->id, ['enrolled_at' => now()]);
        $archived->update(['is_archived' => true]);

        $ours = $this->libraryModule('Our Module');
        $theirs = $this->libraryModule('Their Module');
        $old = $this->libraryModule('Archived Class Module');
        $this->assign($mine, $ours);
        $this->assign($other, $theirs);
        $this->assign($archived, $old);

        $this->authenticateAs($student);

        $this->get(route('modules.index', ['source' => 'class-'.$other->id]))
            ->assertOk()
            ->assertDontSee('Their Module')
            ->assertDontSee('IT 4B – Advanced Analytics');
        $this->get(route('modules.index', ['source' => 'class-'.$archived->id]))
            ->assertOk()
            ->assertDontSee('Archived Class Module');

        $this->get(route('student.modules.show', $theirs))->assertNotFound();
        $this->get(route('student.modules.show', ['module' => $theirs->id, 'class' => $other->id]))->assertNotFound();
        $this->get(route('student.modules.show', $old))->assertNotFound();
        $this->get(route('student.modules.show', $ours))->assertOk();
    }

    public function test_removing_a_module_from_a_class_removes_it_from_the_list(): void
    {
        $instructor = $this->instructor();
        $class = $this->classFor($instructor, 'Data Science', 'IT 4A');
        $student = $this->enrolledStudent($class);
        $library = $this->libraryModule('Financial Data Analytics');
        $this->assign($class, $library);

        $this->authenticateAs($student)->get(route('modules.index', ['source' => 'class-'.$class->id]))->assertSee('Financial Data Analytics');

        // Another instructor cannot remove it.
        $this->authenticateAs($this->instructor())
            ->post(route('modules.module-library.unassign'), ['class_id' => $class->id, 'remove_module_id' => $library->id])
            ->assertNotFound();

        $this->authenticateAs($instructor)
            ->post(route('modules.module-library.unassign'), ['class_id' => $class->id, 'remove_module_id' => $library->id])
            ->assertSessionHas('success');

        $this->assertSame('archived', ClassModuleAssignment::where('class_id', $class->id)->value('status'));
        // (The instructor's "removed" message is still in this test session,
        // so the check looks for the module card rather than its title.)
        $this->authenticateAs($student)
            ->get(route('modules.index', ['source' => 'class-'.$class->id]))
            ->assertOk()
            ->assertDontSee('Financial Data Analytics for classes.')
            ->assertDontSee(route('student.modules.show', ['module' => $library->id, 'class' => $class->id]), false)
            ->assertSee('Your instructor has not assigned any modules to this class yet.');
        $this->get(route('student.modules.show', $library))->assertNotFound();
    }

    // ── Fixtures ──────────────────────────────────────────────────────

    private function instructor(): User
    {
        $this->institution ??= Institution::create([
            'name' => 'Updates5 Institution',
            'email' => 'updates5-'.Str::lower(Str::random(6)).'@institution.test',
            'status' => 'active',
        ]);

        return $this->roleUser(User::ROLE_INSTRUCTOR, ['institution_id' => $this->institution->id]);
    }

    private function classFor(User $instructor, string $name, string $section): ClassRoom
    {
        return ClassRoom::create(['instructor_id' => $instructor->id, 'name' => $name, 'section' => $section, 'is_archived' => false]);
    }

    private function enrolledStudent(ClassRoom $class): User
    {
        $student = $this->roleUser();
        $class->students()->attach($student->id, ['enrolled_at' => now()]);

        return $student;
    }

    private function libraryModule(string $title, array $outcomes = ['Explain the idea.']): ModuleLibraryItem
    {
        $number = (int) ModuleLibraryItem::max('module_no') + 1;

        return ModuleLibraryItem::create([
            'module_no' => $number,
            'module_code' => 'U5-'.$number.'-'.Str::upper(Str::random(4)),
            'title' => $title,
            'year_level' => 'Year 3',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1',
            'description' => $title.' for classes.',
            'estimated_minutes' => 40,
            'content_sections' => [['heading' => 'Balance sheets', 'body' => 'Assets equal liabilities plus equity.']],
            'mcq_questions' => [],
            'learning_outcomes' => $outcomes,
            'sort_order' => $number,
            'is_active' => true,
        ]);
    }

    private function assign(ClassRoom $class, ModuleLibraryItem $module): void
    {
        ClassModuleAssignment::create([
            'class_id' => $class->id,
            'module_library_item_id' => $module->id,
            'status' => 'active',
            'assigned_at' => now(),
        ]);
    }
}
