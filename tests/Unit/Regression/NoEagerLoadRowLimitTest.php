<?php

namespace Tests\Unit\Regression;

use App\Models\ModelVersion;
use Tests\TestCase;

/**
 * MySQL 5.5 cannot run Laravel's "limit per parent" eager-load query: on
 * MySQL older than 8 it filters the @laravel_row user variable in HAVING,
 * which fails with error 1463 under the strict SQL mode of this application
 * (the model result page returned HTTP 500). Newer databases use window
 * functions and never show the problem, so it is guarded here instead.
 */
class NoEagerLoadRowLimitTest extends TestCase
{
    public function test_no_eager_load_in_the_application_limits_rows_per_parent(): void
    {
        $offenders = [];
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path(), \FilesystemIterator::SKIP_DOTS));

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($file->getPathname());

            // 'relation' => fn ($query) => $query ... ->limit(n) / ->take(n)
            if (preg_match_all(
                "/['\"][\\w.]+(?: as \\w+)?['\"]\\s*=>\\s*(?:static\\s+)?(?:fn|function)\\s*\\([^)]*\\)[^;\\]]*?->(?:limit|take)\\s*\\(/s",
                $source,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                foreach ($matches[0] as [, $offset]) {
                    $offenders[] = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname())
                        .':'.(substr_count($source, "\n", 0, $offset) + 1);
                }
            }
        }

        $this->assertSame([], $offenders, 'Eager loads with a per-parent limit break on MySQL 5.5; query the relation separately.');
    }

    public function test_recent_predictions_use_a_plain_limit_query(): void
    {
        $version = (new ModelVersion())->forceFill(['id' => 33]);
        $version->exists = true;

        $sql = $version->predictions()->where('user_id', 5)->latest()->limit(10)->toSql();

        $this->assertStringNotContainsString('laravel_row', $sql);
        $this->assertStringNotContainsStringIgnoringCase('row_number', $sql);
        $this->assertMatchesRegularExpression('/limit 10$/i', $sql);
    }
}
