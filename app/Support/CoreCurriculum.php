<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The 24 Core Modules of DataSensei (DataSensei Updates 12).
 *
 * The core curriculum is marked in the database, never recognised by title:
 *
 *   modules.module_type       'core' or 'custom' (new modules are custom)
 *   modules.module_key        the module's permanent identity, for example
 *                             core-01-basics-of-python-programming; it is
 *                             set once and cannot be changed
 *   challenges.core_module_key the core module a built-in challenge belongs
 *                             to (every version of it), NULL for custom-module,
 *                             admin-added and instructor challenges
 *
 * The list below is the agreed curriculum. The titles are used only once, by
 * sync(), to find which existing rows are the core ones on an install made
 * before these columns existed (or after the curriculum seeders ran). After
 * that, everything uses the keys, so renaming a title (which the admin pages
 * no longer allow for core modules) changes nothing.
 */
final class CoreCurriculum
{
    public const TYPE_CORE = 'core';

    public const TYPE_CUSTOM = 'custom';

    /** @var list<array{key: string, title: string, year: string}> */
    public const MODULES = [
        ['key' => 'core-01-basics-of-python-programming', 'title' => 'Basics of Python Programming', 'year' => 'Year 1'],
        ['key' => 'core-02-basics-of-statistics', 'title' => 'Basics of Statistics', 'year' => 'Year 1'],
        ['key' => 'core-03-introduction-to-data-science', 'title' => 'Introduction to Data Science', 'year' => 'Year 1'],
        ['key' => 'core-04-mathematical-analysis-i', 'title' => 'Mathematical Analysis I', 'year' => 'Year 1'],
        ['key' => 'core-05-methods-of-proof', 'title' => 'Methods of Proof', 'year' => 'Year 1'],
        ['key' => 'core-06-modeling-and-simulation', 'title' => 'Modeling and Simulation', 'year' => 'Year 1'],
        ['key' => 'core-07-algorithms-data-structures', 'title' => 'Algorithms & Data Structures for Data Scientists', 'year' => 'Year 2'],
        ['key' => 'core-08-statistical-methods-experimental-design', 'title' => 'Statistical Methods & Experimental Design', 'year' => 'Year 2'],
        ['key' => 'core-09-applied-matrix-analysis', 'title' => 'Applied Matrix Analysis', 'year' => 'Year 2'],
        ['key' => 'core-10-database-management', 'title' => 'Database Management for Data Science', 'year' => 'Year 2'],
        ['key' => 'core-11-bayesian-data-analysis', 'title' => 'Introduction to Bayesian Data Analysis', 'year' => 'Year 2'],
        ['key' => 'core-12-introductory-forecasting', 'title' => 'Introductory Forecasting', 'year' => 'Year 2'],
        ['key' => 'core-13-optimization-techniques', 'title' => 'Introduction to Optimization Techniques', 'year' => 'Year 3'],
        ['key' => 'core-14-machine-learning-1', 'title' => 'Machine Learning 1: Supervised Learning', 'year' => 'Year 3'],
        ['key' => 'core-15-data-visualization', 'title' => 'Data Visualization', 'year' => 'Year 3'],
        ['key' => 'core-16-multivariate-analysis', 'title' => 'Multivariate Analysis', 'year' => 'Year 3'],
        ['key' => 'core-17-deep-learning', 'title' => 'Deep Learning', 'year' => 'Year 3'],
        ['key' => 'core-18-privacy-ethics-data-governance', 'title' => 'Privacy, Ethics & Data Governance', 'year' => 'Year 3'],
        ['key' => 'core-19-artificial-intelligence', 'title' => 'Introduction to Artificial Intelligence', 'year' => 'Year 4'],
        ['key' => 'core-20-unstructured-data', 'title' => 'Analysis of Unstructured Data', 'year' => 'Year 4'],
        ['key' => 'core-21-machine-learning-2', 'title' => 'Machine Learning 2: Unsupervised Learning', 'year' => 'Year 4'],
        ['key' => 'core-22-big-data-cloud-computing', 'title' => 'Big Data & Cloud Computing', 'year' => 'Year 4'],
        ['key' => 'core-23-data-warehousing', 'title' => 'Data Warehousing', 'year' => 'Year 4'],
        ['key' => 'core-24-sequential-decision-making', 'title' => 'Sequential Decision Making', 'year' => 'Year 4'],
    ];

    /** @return list<string> */
    public static function keys(): array
    {
        return array_column(self::MODULES, 'key');
    }

    public static function isCoreKey(?string $key): bool
    {
        return $key !== null && in_array($key, self::keys(), true);
    }

    /** The agreed title of a core module key (for messages and snapshots). */
    public static function titleOf(string $key): ?string
    {
        foreach (self::MODULES as $module) {
            if ($module['key'] === $key) {
                return $module['title'];
            }
        }

        return null;
    }

    public static function ready(): bool
    {
        return SchemaInspector::hasColumn('modules', 'module_type')
            && SchemaInspector::hasColumn('modules', 'module_key');
    }

    /**
     * Marks the existing core modules and their built-in challenges. Safe to
     * run any number of times: rows that already carry a key keep it, and a
     * key is never given to two modules. Returns what was marked. Run by the
     * migration, the curriculum seeders and "php artisan datasensei:core-sync",
     * never by a page.
     *
     * @return array{modules: int, challenges: int}
     */
    public static function sync(): array
    {
        $marked = ['modules' => 0, 'challenges' => 0];

        if (! SchemaInspector::hasTable('modules') || ! SchemaInspector::hasColumn('modules', 'module_key')) {
            return $marked;
        }

        $marked['modules'] = self::markModules();

        if (SchemaInspector::hasTable('challenges') && SchemaInspector::hasColumn('challenges', 'core_module_key')) {
            $marked['challenges'] = self::markChallenges();
        }

        return $marked;
    }

    private static function markModules(): int
    {
        $marked = 0;
        $modules = DB::table('modules')->orderBy('id')->get(['id', 'title', 'order_index', 'module_key', 'module_type']);
        $taken = $modules->pluck('module_key')->filter()->flip();
        $usedIds = [];

        // A module already holding a core key is core.
        foreach ($modules as $module) {
            if (self::isCoreKey($module->module_key)) {
                $usedIds[(int) $module->id] = true;
                if ($module->module_type !== self::TYPE_CORE) {
                    DB::table('modules')->where('id', $module->id)->update(['module_type' => self::TYPE_CORE]);
                    $marked++;
                }
            }
        }

        $normalize = fn ($title) => mb_strtolower(trim(preg_replace('/\s+/', ' ', (string) $title) ?? ''));
        $byTitle = [];
        foreach ($modules as $module) {
            if (! isset($usedIds[(int) $module->id]) && $module->module_key === null) {
                $byTitle[$normalize($module->title)][] = $module;
            }
        }

        $matchedIds = [];
        $assign = function (object $module, string $key) use (&$marked, &$usedIds, &$taken, &$matchedIds): void {
            DB::table('modules')->where('id', $module->id)->update([
                'module_key' => $key,
                'module_type' => self::TYPE_CORE,
            ]);
            $usedIds[(int) $module->id] = true;
            $matchedIds[] = (int) $module->id;
            $taken[$key] = true;
            $marked++;
        };

        // 1. The agreed title (the original seeded curriculum).
        $unmatched = [];
        foreach (self::MODULES as $position => $entry) {
            if (isset($taken[$entry['key']])) {
                continue;
            }

            $candidate = collect($byTitle[$normalize($entry['title'])] ?? [])
                ->first(fn ($module) => ! isset($usedIds[(int) $module->id]));

            if ($candidate) {
                $assign($candidate, $entry['key']);
            } else {
                $unmatched[$position + 1] = $entry['key'];
            }
        }

        // 2. A core module renamed before this update: the seeded module still
        //    at the same curriculum position. Only rows created with the
        //    seeded curriculum qualify (an id no higher than the last matched
        //    core module), never a custom module added later.
        if ($unmatched !== [] && $matchedIds !== []) {
            $lastSeededId = max($matchedIds);
            foreach ($unmatched as $position => $key) {
                $candidate = $modules->first(fn ($module) => (int) $module->order_index === $position
                    && (int) $module->id <= $lastSeededId
                    && $module->module_key === null
                    && ! isset($usedIds[(int) $module->id]));

                if ($candidate) {
                    $assign($candidate, $key);
                }
            }
        }

        return $marked;
    }

    /**
     * Built-in platform challenges of a core module: matched by the content
     * code the seeders give them (category, type and the agreed title, see
     * Challenge::booted), or by the core module they were fanned out from.
     * Every version shares the content code, so every version is marked.
     * Instructor-built challenges, challenges of custom modules and
     * challenges an admin added by hand are never marked.
     */
    private static function markChallenges(): int
    {
        $categories = DB::table('challenge_categories')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $byCode = [];

        foreach (self::MODULES as $entry) {
            foreach ($categories as $categoryId) {
                foreach (['MCQ' => false, 'CODE' => true] as $type => $coding) {
                    $byCode[self::seededContentCode($categoryId, $type, $entry['title'])] = $entry['key'];
                }
            }
        }

        $coreModuleIds = DB::table('modules')
            ->whereNotNull('module_key')
            ->where('module_type', self::TYPE_CORE)
            ->pluck('module_key', 'id');

        $marked = 0;
        DB::table('challenges')
            ->whereNull('core_module_key')
            ->where(fn ($q) => $q->whereNull('visibility')->orWhere('visibility', 'platform'))
            ->whereNull('created_by')
            ->orderBy('id')
            ->select(['id', 'content_code', 'title', 'module_id', 'is_coding_challenge', 'challenge_category_id'])
            ->chunkById(200, function ($challenges) use ($byCode, $coreModuleIds, &$marked): void {
                foreach ($challenges as $challenge) {
                    // A challenge fanned out from a module belongs to that
                    // module: core only when the module is core.
                    $key = $challenge->module_id
                        ? ($coreModuleIds[(int) $challenge->module_id] ?? null)
                        : ($byCode[strtoupper((string) $challenge->content_code)] ?? null);

                    if ($key !== null) {
                        DB::table('challenges')->where('id', $challenge->id)->update(['core_module_key' => $key]);
                        $marked++;
                    }
                }
            });

        // Other versions of a marked challenge share its content code.
        $codes = DB::table('challenges')->whereNotNull('core_module_key')->pluck('core_module_key', 'content_code');
        foreach ($codes as $code => $key) {
            $marked += DB::table('challenges')
                ->where('content_code', $code)
                ->whereNull('core_module_key')
                ->whereNull('created_by')
                ->update(['core_module_key' => $key]);
        }

        return $marked;
    }

    /** The content code Challenge::booted() gives a seeded challenge. */
    public static function seededContentCode(int $categoryId, string $type, string $title): string
    {
        $identity = implode('|', [(string) $categoryId, $type, $title]);
        $slug = strtoupper(Str::slug($title, '-')) ?: 'CHALLENGE';
        $prefix = 'C'.$categoryId.'-'.$type.'-';
        $suffix = '-'.strtoupper(substr(sha1($identity), 0, 8));
        $available = max(1, 64 - strlen($prefix) - strlen($suffix));

        return $prefix.substr($slug, 0, $available).$suffix;
    }
}
