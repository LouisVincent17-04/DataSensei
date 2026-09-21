<?php

namespace Tests\Feature\Lessons;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LessonContentSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_sync_updates_lessons_in_place_and_keeps_ids_and_learner_progress(): void
    {
        $module = Module::query()->where('order_index', 3)->first()
            ?? Module::forceCreate(['title' => 'Introduction to Data Science', 'description' => 'Test', 'order_index' => 3, 'year_level' => 'Year 1']);

        $this->artisan('lessons:sync-content', ['--module' => [3]])->assertSuccessful();

        $lessons = Lesson::where('module_id', $module->id)->orderBy('order_index')->get();
        $this->assertGreaterThanOrEqual(10, $lessons->count());

        // A learner finished the first lesson; an older copy of its text is stored.
        $student = $this->roleUser();
        $first = $lessons->first();
        DB::table('lesson_user')->insert(['user_id' => $student->id, 'lesson_id' => $first->id, 'created_at' => now(), 'updated_at' => now()]);
        $first->forceFill(['content' => '<p>old text</p>'])->save();
        $idsBefore = $lessons->pluck('id')->all();

        $this->artisan('lessons:sync-content', ['--module' => [3], '--dry-run' => true])->assertSuccessful();
        $this->assertSame('<p>old text</p>', $first->fresh()->content, 'a dry run must not write');

        $this->artisan('lessons:sync-content', ['--module' => [3]])->assertSuccessful();

        $this->assertSame($idsBefore, Lesson::where('module_id', $module->id)->orderBy('order_index')->pluck('id')->all());
        $this->assertStringContainsString('The Data Science Pipeline', $first->fresh()->content);
        $this->assertDatabaseHas('lesson_user', ['user_id' => $student->id, 'lesson_id' => $first->id]);
    }

    public function test_the_pipeline_is_a_numbered_strip_without_emoji_icons(): void
    {
        $seeder = file_get_contents(database_path('seeders/Module3LessonsSeeder.php'));
        preg_match('/<h3>The Data Science Pipeline<\/h3>(.*?)<h3>/s', $seeder, $match);

        $this->assertStringContainsString('<ol', $match[1]);
        foreach (['01', '06', 'Define', 'Communicate'] as $text) {
            $this->assertStringContainsString($text, $match[1]);
        }
        $this->assertSame(0, preg_match('/[\x{1F300}-\x{1FAFF}\x{2600}-\x{27BF}]/u', $match[1]));
    }

    public function test_examples_that_cannot_run_in_the_sandbox_offer_no_compiler_button(): void
    {
        foreach (glob(database_path('seeders/Module*LessonsSeeder.php')) as $path) {
            preg_match_all('/<div class="code-window".*?<div class="code-content"[^>]*>(.*?)<\/div>/s', file_get_contents($path), $windows, PREG_SET_ORDER);

            foreach ($windows as $window) {
                $code = html_entity_decode(strip_tags($window[1]));

                if (preg_match('/^\s*(?:import|from)\s+(tensorflow|torch|pyspark|airflow|mlflow|prophet|transformers|kafka|boto3|gymnasium|shap|plotly|pymongo|redis|sqlalchemy|fastapi)\b/m', $code, $needs) === 1) {
                    $this->assertStringNotContainsString('launchIDE', $window[0], basename($path).' offers the compiler for code that needs '.$needs[1]);
                }
            }
        }
    }
}
