<?php

use App\Support\FinalExamAccess;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DataSensei Updates 7, task 2: every learner can take the Final Exam of a
 * public DataSensei Module.
 *
 * The seeded final exam lessons showed a "University / Organization Access
 * Only" lock screen and kept the exam hidden until window.USER_ORG_ID was
 * set, which never happened. This rewrites those lessons once: the lock
 * screen and its script are removed and the exam is shown (see
 * App\Support\FinalExamAccess). The exam questions, their answers and every
 * other lesson are left exactly as they are, and so is learner progress.
 *
 * Only rows that still contain the lock are touched, so running it again
 * changes nothing. Plain SELECT/UPDATE statements, so it runs on MySQL 5.5.
 */
return new class extends Migration
{
    private const MARKERS = ['%org-lock-screen%', '%final-exam-content%', '%USER_ORG_ID%'];

    public function up(): void
    {
        if (! $this->tableExists('lessons')) {
            return;
        }

        $columns = ['content'];
        if ($this->columnExists('lessons', 'blocks')) {
            $columns[] = 'blocks';
        }

        DB::table('lessons')
            ->where(function ($query) use ($columns): void {
                foreach ($columns as $column) {
                    foreach (self::MARKERS as $marker) {
                        $query->orWhere($column, 'like', $marker);
                    }
                }
            })
            ->select(array_merge(['id'], $columns))
            ->orderBy('id')
            ->chunkById(25, function ($lessons) use ($columns): void {
                foreach ($lessons as $lesson) {
                    $changes = [];

                    $content = FinalExamAccess::open($lesson->content);
                    if ($content !== $lesson->content) {
                        $changes['content'] = $content;
                    }

                    if (in_array('blocks', $columns, true)) {
                        $blocks = $this->openBlocks($lesson->blocks);
                        if ($blocks !== $lesson->blocks) {
                            $changes['blocks'] = $blocks;
                        }
                    }

                    if ($changes !== []) {
                        DB::table('lessons')->where('id', $lesson->id)->update($changes + ['updated_at' => now()]);
                    }
                }
            });
    }

    public function down(): void
    {
        // The final exams stay open to everyone.
    }

    /**
     * The block editor's copy of a lesson (JSON), with the lock taken out of
     * any block that kept the original HTML. Returned unchanged when there is
     * nothing to open.
     */
    private function openBlocks(?string $raw): ?string
    {
        if (! FinalExamAccess::isRestricted($raw)) {
            return $raw;
        }

        $blocks = json_decode((string) $raw, true);
        if (! is_array($blocks)) {
            return $raw;
        }

        $changed = false;
        foreach ($blocks as $index => $block) {
            if (! is_array($block)) {
                continue;
            }

            foreach (['html', 'content'] as $field) {
                if (isset($block[$field]) && is_string($block[$field]) && FinalExamAccess::isRestricted($block[$field])) {
                    $blocks[$index][$field] = FinalExamAccess::open($block[$field]);
                    $changed = true;
                }
            }
        }

        return $changed ? json_encode($blocks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $raw;
    }

    private function tableExists(string $table): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasTable($table);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?',
            [$table]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        $result = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        return (int) ($result->aggregate ?? 0) > 0;
    }
};
