<?php

namespace Tests\Feature\Regression;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Updates 3, A0: finishing the last lesson of a module sends the student back
 * to the module page (/module) with the "Module Completed!" flash, instead of
 * the challenges page. Completing any other lesson still advances to the next
 * lesson in the module.
 */
class Updates3ModuleEndRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_the_last_lesson_returns_to_the_module_page_with_a_flash(): void
    {
        $student = $this->makeStudent();
        [$module, $lessons] = $this->makeModuleWithLessons(2);

        $this->actingAsStudent($student)
            ->post(route('lesson.complete', $lessons[0]))
            ->assertRedirect(route('lesson.show', ['module' => $module->id, 'lesson' => $lessons[1]->id]));

        $this->actingAsStudent($student)
            ->post(route('lesson.complete', $lessons[1]))
            ->assertRedirect(route('modules.index'))
            ->assertSessionHas('success', 'Module Completed!');

        $this->assertDatabaseHas('module_user', [
            'user_id' => $student->id,
            'module_id' => $module->id,
            'is_completed' => 1,
        ]);
    }

    public function test_the_module_page_shows_the_completion_flash(): void
    {
        $student = $this->makeStudent();
        $this->makeModuleWithLessons(1);

        $this->actingAsStudent($student)
            ->withSession(['success' => 'Module Completed!'])
            ->get(route('modules.index'))
            ->assertOk()
            ->assertSee('Module Completed!');
    }

    public function test_completing_a_lesson_that_is_not_last_advances_to_the_next_lesson(): void
    {
        $student = $this->makeStudent();
        [$module, $lessons] = $this->makeModuleWithLessons(3);

        $this->actingAsStudent($student)
            ->post(route('lesson.complete', $lessons[1]))
            ->assertRedirect(route('lesson.show', ['module' => $module->id, 'lesson' => $lessons[2]->id]))
            ->assertSessionMissing('success');

        $this->assertSame(0, DB::table('module_user')->where('module_id', $module->id)->where('is_completed', 1)->count());
    }

    /** @return array{0: Module, 1: list<Lesson>} */
    private function makeModuleWithLessons(int $count): array
    {
        $module = Module::create([
            'title' => 'Redirect Module',
            'description' => 'A module for the redirect test.',
            'order_index' => 1,
            'year_level' => 'Year 1',
            'xp_reward' => 100,
            'is_boss' => false,
        ]);

        $lessons = [];
        for ($i = 1; $i <= $count; $i++) {
            $lessons[] = Lesson::create([
                'module_id' => $module->id,
                'title' => 'Lesson '.$i,
                'content' => '<h2>Lesson '.$i.'</h2>',
                'order_index' => $i,
            ]);
        }

        return [$module, $lessons];
    }

    private function makeStudent(): User
    {
        return User::create([
            'name' => 'Redirect Student',
            'email' => 'redirect-student-'.Str::lower(Str::random(8)).'@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    private function actingAsStudent(User $student)
    {
        return $this->actingAs($student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student),
        ]);
    }
}
