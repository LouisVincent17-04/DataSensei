<?php

namespace App\Console\Commands;

use App\Models\Lesson;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Brings the lesson text of existing modules up to date with the lesson
 * seeders WITHOUT re-seeding.
 *
 * "php artisan db:seed --class=Module3LessonsSeeder" deletes the module's
 * lessons and creates them again. lesson_user rows cascade with the lessons,
 * so re-seeding wipes every learner's progress in that module. This command
 * runs the seeder inside a transaction only to read what it would create,
 * rolls everything back, and then updates the existing lessons in place
 * (matched by module and order), so lesson ids and progress stay untouched.
 */
class SyncLessonContent extends Command
{
    protected $signature = 'lessons:sync-content
        {--module=* : Module numbers to sync (default: every ModuleNLessonsSeeder)}
        {--dry-run : Show what would change without writing}';

    protected $description = 'Update existing lessons from the lesson seeders in place, keeping lesson ids and learner progress';

    public function handle(): int
    {
        $numbers = array_map('intval', (array) $this->option('module'));

        if ($numbers === []) {
            foreach (glob(database_path('seeders/Module*LessonsSeeder.php')) ?: [] as $file) {
                if (preg_match('/Module(\d+)LessonsSeeder\.php$/', $file, $match) === 1) {
                    $numbers[] = (int) $match[1];
                }
            }
        }

        sort($numbers);
        $dryRun = (bool) $this->option('dry-run');
        $rows = [];

        foreach (array_unique($numbers) as $number) {
            $class = 'Database\\Seeders\\Module'.$number.'LessonsSeeder';

            if (! class_exists($class)) {
                $rows[] = [$number, 'no seeder', '-', '-', '-'];

                continue;
            }

            try {
                $fresh = $this->lessonsTheSeederWouldCreate($class);
            } catch (\Throwable $exception) {
                $rows[] = [$number, 'seeder failed: '.mb_substr($exception->getMessage(), 0, 60), '-', '-', '-'];

                continue;
            }

            [$updated, $created, $unchanged] = [0, 0, 0];

            DB::transaction(function () use ($fresh, $dryRun, &$updated, &$created, &$unchanged): void {
                foreach ($fresh as $lesson) {
                    $existing = Lesson::query()
                        ->where('module_id', $lesson['module_id'])
                        ->where('order_index', $lesson['order_index'])
                        ->orderBy('id')
                        ->first();

                    if (! $existing) {
                        $created++;
                        if (! $dryRun) {
                            (new Lesson())->forceFill($lesson)->save();
                        }

                        continue;
                    }

                    $changes = array_filter(
                        $lesson,
                        static fn ($value, string $key): bool => (string) $existing->getAttribute($key) !== (string) $value,
                        ARRAY_FILTER_USE_BOTH
                    );

                    if ($changes === []) {
                        $unchanged++;

                        continue;
                    }

                    $updated++;
                    if (! $dryRun) {
                        $existing->forceFill($changes)->save();
                    }
                }
            });

            $rows[] = [$number, 'ok', $updated, $created, $unchanged];
        }

        $this->table(['Module', 'Seeder', 'Updated', 'Created', 'Unchanged'], $rows);
        $this->line($dryRun ? 'Dry run: nothing was written.' : 'Lesson ids and learner progress were not touched.');

        return self::SUCCESS;
    }

    /** @return list<array<string, mixed>> */
    private function lessonsTheSeederWouldCreate(string $class): array
    {
        $before = (int) Lesson::max('id');

        DB::beginTransaction();

        try {
            app($class)->run();

            return Lesson::query()
                ->where('id', '>', $before)
                ->orderBy('module_id')
                ->orderBy('order_index')
                ->get()
                ->map(static fn (Lesson $lesson): array => collect($lesson->getAttributes())
                    ->except(['id', 'created_at', 'updated_at'])
                    ->all())
                ->all();
        } finally {
            // The delete-and-recreate the seeder just did never reaches the database.
            DB::rollBack();
        }
    }
}
