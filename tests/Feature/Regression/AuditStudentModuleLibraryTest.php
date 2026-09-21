<?php

namespace Tests\Feature\Regression;

use App\Models\ModuleLibraryItem;
use App\Models\User;
use App\Support\AuthSessionFingerprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Student module library (/student/modules and /student/modules/{module}).
 *
 * The lesson viewer used to open its "is this a SQL example?" check with the
 * inline @php(...) form. Blade extracts raw PHP blocks first with a lazy
 * "@php(.*?)@endphp" match, so that inline directive was paired with the next
 * @endphp further down the file and everything in between was emitted as
 * literal PHP. Every module page died with a PHP parse error.
 */
class AuditStudentModuleLibraryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Every Blade template in the project, not just the module viewers: the
     * inline @php(...) collision is invisible until the page is requested, and
     * four different views carried it. A whole-tree sweep is cheap insurance.
     */
    public function test_every_blade_template_compiles(): void
    {
        $views = [];
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($directory as $file) {
            if (str_ends_with($file->getPathname(), '.blade.php')) {
                $views[] = $file->getPathname();
            }
        }

        $this->assertGreaterThan(100, count($views), 'The view sweep found suspiciously few templates.');

        $broken = [];
        foreach ($views as $view) {
            $compiled = Blade::compileString(file_get_contents($view));

            set_error_handler(static fn (): bool => true);

            try {
                @token_get_all('<?php ?>' . $compiled, TOKEN_PARSE);
            } catch (\ParseError $parseError) {
                $broken[] = str_replace(base_path() . '/', '', $view) . ': ' . $parseError->getMessage();
            } finally {
                restore_error_handler();
            }
        }

        $this->assertSame([], $broken, "Blade templates that do not compile:\n" . implode("\n", $broken));
    }

    public function test_shared_module_viewers_compile(): void
    {
        foreach ([
            'resources/views/student/shared/module_lesson_viewer.blade.php',
            'resources/views/student/shared/module_netacad_viewer.blade.php',
            'resources/views/instructor/shared/module_lesson_viewer.blade.php',
            'resources/views/instructor/shared/module_netacad_viewer.blade.php',
        ] as $view) {
            $compiled = Blade::compileString(file_get_contents(base_path($view)));

            $error = null;
            set_error_handler(static function (): bool {
                return true;
            });

            try {
                // Parsing only: the closing tag keeps the compiled template's
                // own inline HTML from being echoed while it is checked.
                $parsed = @token_get_all('<?php ?>' . $compiled, TOKEN_PARSE);
                $this->assertNotEmpty($parsed);
            } catch (\ParseError $parseError) {
                $error = $parseError->getMessage();
            } finally {
                restore_error_handler();
            }

            $this->assertNull($error, $view . ' does not compile to valid PHP: ' . (string) $error);
        }
    }

    public function test_student_can_open_an_assigned_module(): void
    {
        $student = $this->makeStudent();
        $classId = $this->makeClass();
        DB::table('class_student')->insert([
            'class_id' => $classId,
            'student_id' => $student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $module = $this->makeModule('MOD-SQL', 'Querying data', [
            [
                'heading' => 'Reading rows',
                'body' => 'Select the columns you need.',
                'code' => "SELECT * FROM student_scores;",
                'key_points' => ['Name the columns'],
            ],
            [
                'heading' => 'Counting rows',
                'body' => 'Python can do it too.',
                'code' => "print(len(rows))",
            ],
        ]);

        DB::table('class_module_assignments')->insert([
            'class_id' => $classId,
            'module_library_item_id' => $module->id,
            'status' => 'active',
            'assigned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAsStudent($student)
            ->get(route('student.modules.index'))
            ->assertOk()
            ->assertSee('Querying data');

        $this->actingAsStudent($student)
            ->get(route('student.modules.show', $module))
            ->assertOk()
            ->assertSee('SQL Example')
            ->assertSee('Python Example');
    }

    public function test_student_cannot_open_a_module_assigned_to_another_class(): void
    {
        $student = $this->makeStudent();
        $myClass = $this->makeClass();
        $otherClass = $this->makeClass();
        DB::table('class_student')->insert([
            'class_id' => $myClass->id ?? $myClass,
            'student_id' => $student->id,
            'enrolled_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $mine = $this->makeModule('MOD-MINE', 'My module');
        $theirs = $this->makeModule('MOD-THEIRS', 'Their module');

        foreach ([[$myClass, $mine], [$otherClass, $theirs]] as [$class, $module]) {
            DB::table('class_module_assignments')->insert([
                'class_id' => $class,
                'module_library_item_id' => $module->id,
                'status' => 'active',
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->actingAsStudent($student)
            ->get(route('student.modules.show', $theirs))
            ->assertNotFound();

        $this->actingAsStudent($student)
            ->get(route('student.modules.show', $mine))
            ->assertOk();
    }

    public function test_student_without_a_class_sees_the_active_library(): void
    {
        $student = $this->makeStudent();
        $module = $this->makeModule('MOD-OPEN', 'Open module');

        $this->actingAsStudent($student)
            ->get(route('student.modules.index'))
            ->assertOk()
            ->assertSee('Open module');

        $this->actingAsStudent($student)
            ->get(route('student.modules.show', $module))
            ->assertOk();
    }

    private function makeStudent(): User
    {
        return User::create([
            'name' => 'Audit Student',
            'email' => 'audit-student-' . Str::lower(Str::random(8)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_USER,
            'status' => 'active',
        ]);
    }

    private function makeClass(): int
    {
        $instructor = User::create([
            'name' => 'Audit Instructor',
            'email' => 'audit-instructor-' . Str::lower(Str::random(8)) . '@example.test',
            'password' => bcrypt('Secret!2026'),
            'role' => User::ROLE_INSTRUCTOR,
            'status' => 'active',
        ]);

        return (int) DB::table('classes')->insertGetId([
            'instructor_id' => $instructor->id,
            'name' => 'Audit Class',
            'class_code' => 'A' . Str::upper(Str::random(7)),
            'is_archived' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeModule(string $code, string $title, array $sections = []): ModuleLibraryItem
    {
        return ModuleLibraryItem::create([
            'module_no' => random_int(1, 9999),
            'module_code' => $code . '-' . Str::upper(Str::random(5)),
            'title' => $title,
            'year_level' => '1',
            'version_no' => 1,
            'version_name' => 'Version 1',
            'version_code' => 'V1-' . Str::upper(Str::random(5)),
            'is_active' => true,
            'content_sections' => $sections,
            'mcq_questions' => [],
        ]);
    }

    private function actingAsStudent(User $student)
    {
        return $this->actingAs($student)->withSession([
            AuthSessionFingerprint::SESSION_KEY => AuthSessionFingerprint::for($student),
        ]);
    }
}
