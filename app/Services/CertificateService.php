<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\CertificateDefinition;
use App\Models\User;
use App\Models\UserCertificate;
use App\Support\Certificates\CertificateLayouts;
use App\Support\Certificates\Placeholders;
use App\Support\CoreCurriculum;
use App\Support\SchemaInspector;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

/**
 * Certificates (DataSensei Updates 12): the three predefined system
 * certificates for Public / Common Users, issued only after a server-side
 * eligibility check on the progress DataSensei already records.
 *
 *   core_24_modules         Core 24 Module Completion: every one of the 24
 *                           Core Modules completed (module_user.is_completed,
 *                           set when all of a module's lessons are done)
 *   core_24_challenges      Core 24 Challenge Completion: the Newbie-level
 *                           MCQ challenge of each Core Module passed with 70%
 *                           or more (the challenge map's own pass rule,
 *                           ChallengePathUnlockService::PASSING_PERCENT)
 *   core_coding_challenges  Core Coding Challenge Completion: every problem
 *                           solved in the Newbie-level coding challenge of
 *                           each Core Module that has one
 *
 * Requirements are read from the core identities in the database
 * (modules.module_key, challenges.core_module_key and the challenge content
 * code shared by all versions), never from titles or ids sent by a browser.
 * Custom modules, instructor-built challenges and challenges an admin adds
 * are never part of them.
 *
 * Each certificate's requirement list is stored as a numbered version. When
 * the core content changes (for example a coding challenge is added to a
 * Core Module) after a certificate was issued under the current version, a
 * new version is written and used from then on; a version nobody was issued
 * under yet is just brought up to date. A certificate already issued keeps
 * the version and the snapshot it was issued with; it is never recalculated
 * or withdrawn. Badges (achievements) stay for small
 * events and are not touched here.
 *
 * DataSensei Updates 13: the same issuing code serves the class Certificates
 * of Completion instructors configure (ClassCertificateService); those are
 * issued by the instructor, at the end of the semester, to the students
 * they select once the server confirms every required item of the class is
 * complete, never automatically. Every certificate is
 * issued with a snapshot of what it shows (title, statement, holder, issuer,
 * signatory, layout), drawn with one of the five predefined layouts on screen
 * and as a PDF, and can be checked by anyone on the public verification page
 * by its certificate ID. Administrators can revoke or reissue a certificate
 * with a reason; both are recorded in the audit log, and so is every issue.
 * Automatic issuance never re-creates a certificate an administrator revoked.
 */
class CertificateService
{
    public const CORE_MODULES = 'core_24_modules';

    public const CORE_CHALLENGES = 'core_24_challenges';

    public const CORE_CODING = 'core_coding_challenges';

    public const DEFINITIONS = [
        self::CORE_MODULES => [
            'name' => 'Core 24 Module Completion',
            'short' => 'MOD',
            'description' => 'Completed all 24 DataSensei Core Modules, every lesson of each module.',
        ],
        self::CORE_CHALLENGES => [
            'name' => 'Core 24 Challenge Completion',
            'short' => 'CHL',
            'description' => 'Passed the official challenge of each of the 24 Core Modules with 70% or more.',
        ],
        self::CORE_CODING => [
            'name' => 'Core Coding Challenge Completion',
            'short' => 'COD',
            'description' => 'Solved every problem of the official coding challenges of the Core Modules.',
        ],
    ];

    /** The challenge level the two challenge certificates use. */
    public const LEVEL_SLUG = 'newbie';

    /** What each system certificate says and the milestone it names. */
    public const SYSTEM_CONTENT = [
        self::CORE_MODULES => [
            'statement' => 'This certifies that [Learner Name] has completed every lesson of all 24 DataSensei Core Modules.',
            'milestone' => 'All 24 Core Modules',
        ],
        self::CORE_CHALLENGES => [
            'statement' => 'This certifies that [Learner Name] has passed the official challenge of each of the 24 DataSensei Core Modules with 70% or more.',
            'milestone' => 'All 24 Core Challenges',
        ],
        self::CORE_CODING => [
            'statement' => 'This certifies that [Learner Name] has solved every official coding challenge of the DataSensei Core Modules.',
            'milestone' => 'All Core Coding Challenges',
        ],
    ];

    /**
     * Shown on certificates issued before these values were recorded, so
     * they never change with today's settings.
     */
    public const LEGACY_DEFAULTS = [
        'layout_key' => CertificateLayouts::ACADEMIC_CLASSIC,
        'issuer_name' => 'DataSensei',
        'issuer_line' => 'Data Science Learning Platform',
        'signatory_name' => 'DataSensei',
        'signatory_title' => 'Learning Platform',
    ];

    /** @var Collection<string, CertificateDefinition>|null */
    private ?Collection $definitions = null;

    /** @var array<int, object|null> current requirement set per definition id */
    private array $sets = [];

    /** The request the cached definitions and sets were read for. */
    private ?object $readFor = null;

    public function ready(): bool
    {
        return SchemaInspector::hasTable('certificate_definitions')
            && SchemaInspector::hasTable('certificate_requirement_sets')
            && SchemaInspector::hasTable('user_certificates');
    }

    /**
     * Creates the three system certificates and their current requirement
     * versions when missing. Safe to call any number of times.
     *
     * @return Collection<string, CertificateDefinition>
     */
    public function ensureDefinitions(): Collection
    {
        if (! $this->ready()) {
            return collect();
        }

        $this->forgetOnNewRequest();
        if ($this->definitions !== null) {
            return $this->definitions;
        }

        $definitions = collect();
        foreach (self::DEFINITIONS as $key => $meta) {
            $definition = CertificateDefinition::query()->where('certificate_key', $key)->first();

            if ($definition === null) {
                try {
                    $definition = CertificateDefinition::create([
                        'certificate_key' => $key,
                        'name' => $meta['name'],
                        'description' => $meta['description'],
                        'audience' => 'public',
                        'is_system' => true,
                        'is_active' => true,
                        'current_version' => 0,
                    ]);
                } catch (QueryException) {
                    $definition = CertificateDefinition::query()->where('certificate_key', $key)->firstOrFail();
                }
            }

            // System certificates are defined here, not edited by hand: keep
            // their name and description as written above.
            if ($definition->name !== $meta['name'] || $definition->description !== $meta['description']) {
                $definition->forceFill(['name' => $meta['name'], 'description' => $meta['description']])->save();
            }

            $this->currentRequirementSet($definition);
            $definitions[$key] = $definition->refresh();
        }

        return $this->definitions = $definitions;
    }

    // ── Requirements ─────────────────────────────────────────────────

    /**
     * What a certificate requires now, from the core identities.
     *
     * @return list<array{module_key: string, title: string, content_code: string|null}>
     */
    public function requirementItems(string $key): array
    {
        if ($key === self::CORE_MODULES) {
            $titles = SchemaInspector::hasColumn('modules', 'module_key')
                ? DB::table('modules')->whereIn('module_key', CoreCurriculum::keys())->pluck('title', 'module_key')
                : collect();

            return array_map(fn (array $entry) => [
                'module_key' => $entry['key'],
                'title' => (string) ($titles[$entry['key']] ?? $entry['title']),
                'content_code' => null,
            ], CoreCurriculum::MODULES);
        }

        $coding = $key === self::CORE_CODING;
        $settings = app(CertificateSettings::class);
        $levels = DB::table('challenge_categories')->orderBy('order_index')->orderBy('id')->get(['id', 'slug', 'name']);
        $level = $levels->firstWhere('slug', $settings->get('core_challenge_level')) ?? $levels->firstWhere('slug', self::LEVEL_SLUG);

        // The coding certificate can require the coding challenges of every
        // level (an admin setting); otherwise both use the one agreed level.
        $scope = $coding && $settings->get('core_coding_all_levels') === '1' ? $levels : collect($level ? [$level] : []);

        $items = [];
        foreach (CoreCurriculum::MODULES as $entry) {
            if ($scope->isEmpty() && ! $coding) {
                $items[] = ['module_key' => $entry['key'], 'title' => $entry['title'], 'content_code' => null];
                continue;
            }

            foreach ($scope as $category) {
                $code = $this->coreChallengeCode($entry, (int) $category->id, $coding);

                // Core Modules without a coding challenge are skipped for the
                // coding certificate; the MCQ certificate always lists all 24.
                if ($coding && $code === null) {
                    continue;
                }

                $items[] = [
                    'module_key' => $entry['key'],
                    'title' => $entry['title'].($scope->count() > 1 ? ' ('.$category->name.')' : ''),
                    'content_code' => $code,
                ];
            }
        }

        return $items;
    }

    /**
     * The content code of a Core Module's built-in challenge on one level,
     * from its core identity (challenges.core_module_key), or before the core
     * challenges are marked (for example right after the challenge seeders
     * ran) the seeded content code, which is the same identity.
     *
     * @param  array{key: string, title: string}  $entry
     */
    private function coreChallengeCode(array $entry, int $levelId, bool $coding): ?string
    {
        if ($levelId <= 0) {
            return null;
        }

        if (SchemaInspector::hasColumn('challenges', 'core_module_key')) {
            $marked = DB::table('challenges')
                ->where('challenge_category_id', $levelId)
                ->where('is_coding_challenge', $coding)
                ->where('core_module_key', $entry['key'])
                ->orderBy('version_no')
                ->orderBy('id')
                ->value('content_code');
            if ($marked !== null) {
                return (string) $marked;
            }
        }

        $seeded = CoreCurriculum::seededContentCode($levelId, $coding ? 'CODE' : 'MCQ', $entry['title']);
        $exists = DB::table('challenges')
            ->where('content_code', $seeded)
            ->whereNull('created_by')
            ->whereNull('module_id')
            ->exists();

        return $exists ? $seeded : null;
    }

    /**
     * The certificate's current requirement version, writing a new version
     * when what it requires has changed.
     */
    public function currentRequirementSet(CertificateDefinition $definition): ?object
    {
        $this->forgetOnNewRequest();
        if (array_key_exists((int) $definition->id, $this->sets)) {
            return $this->sets[(int) $definition->id];
        }

        if ($definition->is_system) {
            $items = $this->requirementItems((string) $definition->certificate_key);
            $hash = sha1(json_encode(array_map(fn (array $item) => [$item['module_key'], $item['content_code']], $items)));
        } else {
            $items = app(ClassCertificateService::class)->requirementItems($definition);
            $hash = sha1(json_encode(array_map(fn (array $item) => array_diff_key($item, ['title' => true]), $items)));
        }

        $latest = DB::table('certificate_requirement_sets')
            ->where('certificate_definition_id', $definition->id)
            ->orderByDesc('version')
            ->first();

        if ($latest !== null && $latest->requirements_hash === $hash) {
            return $this->sets[(int) $definition->id] = $latest;
        }

        // A version no certificate was issued under yet (for example the one
        // written by the migration before the curriculum was seeded) is
        // simply brought up to date. A version that was used is frozen, so
        // the change becomes the next version.
        if ($latest !== null && ! DB::table('user_certificates')->where('requirement_set_id', $latest->id)->exists()) {
            DB::table('certificate_requirement_sets')->where('id', $latest->id)->update([
                'requirements' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'requirements_hash' => $hash,
                'item_count' => count($items),
                'updated_at' => now(),
            ]);

            return $this->sets[(int) $definition->id] = DB::table('certificate_requirement_sets')->where('id', $latest->id)->first();
        }

        $version = ((int) ($latest->version ?? 0)) + 1;

        try {
            DB::transaction(function () use ($definition, $items, $hash, $version): void {
                DB::table('certificate_requirement_sets')->insert([
                    'certificate_definition_id' => $definition->id,
                    'version' => $version,
                    'requirements' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'requirements_hash' => $hash,
                    'item_count' => count($items),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('certificate_definitions')->where('id', $definition->id)->update([
                    'current_version' => $version,
                    'updated_at' => now(),
                ]);
            });
        } catch (QueryException) {
            // Another request wrote the same version first.
        }

        return $this->sets[(int) $definition->id] = DB::table('certificate_requirement_sets')
            ->where('certificate_definition_id', $definition->id)
            ->orderByDesc('version')
            ->first();
    }

    /**
     * Definitions and requirement versions are read once per request. A
     * long-lived instance (a controller kept between requests, a queue
     * worker) reads them again for the next request, so a change to the
     * curriculum or the settings is seen at once.
     */
    private function forgetOnNewRequest(): void
    {
        $request = app()->bound('request') ? app('request') : null;
        if ($request !== $this->readFor) {
            $this->definitions = null;
            $this->sets = [];
            $this->readFor = $request;
        }
    }

    // ── Eligibility ──────────────────────────────────────────────────

    /**
     * The learner's progress toward one certificate, checked on the server
     * against the current requirement version.
     *
     * @return array{key: string, definition: CertificateDefinition, set: object|null, items: list<array<string, mixed>>, done: int, total: int, eligible: bool, issuable: bool}|null
     */
    public function evaluate(User $user, string $key): ?array
    {
        if (! $this->ready() || ! array_key_exists($key, self::DEFINITIONS)) {
            return null;
        }

        $definition = $this->ensureDefinitions()->get($key);
        if ($definition === null) {
            return null;
        }

        $set = $this->currentRequirementSet($definition);
        $items = $set ? (array) json_decode((string) $set->requirements, true) : [];

        $done = match ($key) {
            self::CORE_MODULES => $this->completedModules($user, $items),
            self::CORE_CHALLENGES => $this->passedMcq($user, $items),
            default => $this->solvedCoding($user, $items),
        };

        $rows = array_map(fn (array $item) => $item + ['done' => isset($done[$key === self::CORE_MODULES ? $item['module_key'] : strtoupper((string) $item['content_code'])])], $items);
        $count = count(array_filter($rows, fn (array $row) => $row['done']));

        // Never issued for an empty list, or while a required challenge does
        // not exist on this install.
        $missing = array_filter($rows, fn (array $row) => $key !== self::CORE_MODULES && $row['content_code'] === null);
        $issuable = $rows !== [] && $missing === [];

        return [
            'key' => $key,
            'definition' => $definition,
            'set' => $set,
            'items' => $rows,
            'done' => $count,
            'total' => count($rows),
            'issuable' => $issuable,
            'eligible' => $issuable && $definition->issues() && $count === count($rows),
        ];
    }

    /**
     * Issues the certificate when the learner is eligible. Returns the
     * certificate (already issued or new), or null when not eligible.
     */
    public function award(User $user, string $key): ?UserCertificate
    {
        if (! $user->isLearner() || ! $this->ready()) {
            return null;
        }

        $existing = UserCertificate::query()->where('user_id', $user->id)->where('certificate_key', $key)->first();
        if ($existing) {
            return $existing;
        }

        $evaluation = $this->evaluate($user, $key);
        if ($evaluation === null || ! $evaluation['eligible']) {
            return null;
        }

        $definition = $evaluation['definition'];
        $settings = app(CertificateSettings::class);
        $content = self::SYSTEM_CONTENT[$key] ?? ['statement' => (string) $definition->description, 'milestone' => (string) $definition->name];

        return $this->issue($user, $definition, $evaluation['set'], $evaluation['items'], [
            'kind' => 'system',
            'name' => (string) $definition->name,
            'description' => (string) $definition->description,
            'statement_template' => $content['statement'],
            'module' => $content['milestone'],
            'issuer_name' => $settings->get('issuer_name'),
            'issuer_line' => $settings->get('issuer_line'),
            'logo' => $settings->logo(),
            'signatory_name' => $settings->get('signatory_name'),
            'signatory_title' => $settings->get('signatory_title'),
            'layout_key' => $settings->get('system_layout'),
            'rule' => $this->ruleText($key),
        ]);
    }

    /**
     * Creates the certificate record: a new, never reused certificate ID, the
     * statement with its placeholders filled in, and the snapshot of
     * everything the certificate shows. One active copy per learner and
     * certificate (unique index); a second request at the same moment gets
     * the copy the first one made. Writes the audit log and tells the
     * learner.
     *
     * @param  list<array<string, mixed>>  $items  the requirements met
     * @param  array<string, mixed>  $content
     */
    public function issue(User $user, CertificateDefinition $definition, object $set, array $items, array $content, ?int $classId = null, ?UserCertificate $replaces = null, ?User $issuedBy = null): ?UserCertificate
    {
        if ($issuedBy !== null && $replaces === null) {
            $content['issued_by'] = ['id' => (int) $issuedBy->id, 'name' => (string) $issuedBy->name];
        }

        $key = (string) $definition->certificate_key;
        $issuedAt = Carbon::now();
        $certificate = null;

        for ($try = 0; $try < 3 && $certificate === null; $try++) {
            $number = $this->newNumber($key, $issuedAt, $definition->is_system);
            $snapshot = $this->snapshot($user, $definition, $set, $items, $content, $number, $issuedAt, $replaces);

            try {
                $certificate = new UserCertificate([
                    'user_id' => $user->id,
                    'certificate_definition_id' => $definition->id,
                    'certificate_key' => $key,
                    'certificate_number' => $number,
                    'requirement_set_id' => $set->id,
                    'requirement_version' => (int) $set->version,
                    'snapshot' => json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'issued_at' => $issuedAt,
                ]);
                if (SchemaInspector::hasColumn('user_certificates', 'status')) {
                    $certificate->forceFill([
                        'status' => UserCertificate::STATUS_ACTIVE,
                        'active_slot' => 1,
                        'class_id' => $classId,
                        'reissued_from_id' => $replaces?->id,
                    ]);
                    if ($issuedBy !== null && SchemaInspector::hasColumn('user_certificates', 'issued_by')) {
                        $certificate->forceFill(['issued_by' => $issuedBy->id]);
                    }
                }
                $certificate->save();
            } catch (QueryException $exception) {
                $certificate = null;
                // Issued at the same moment by another request: keep that one.
                $existing = UserCertificate::query()->where('user_id', $user->id)->where('certificate_definition_id', $definition->id)->active()->first();
                if ($existing && $replaces === null) {
                    return $existing;
                }
                if ($try === 2) {
                    throw $exception;
                }
            }
        }

        $this->audit($replaces ? 'reissued' : 'issued', $certificate, $replaces ? null : ($issuedBy
            ? 'Issued by the instructor after the server confirmed every required item of the class was complete.'
            : 'Issued automatically after the requirements were met.'), $issuedBy);

        app(StudentNotificationService::class)->send(
            $user,
            'certificate_earned',
            ($replaces ? 'Certificate reissued: ' : 'Certificate earned: ').$definition->name,
            match (true) {
                $replaces !== null => 'Your '.$definition->name.' certificate was reissued with a new certificate ID. You can view it in My Certificates.',
                $issuedBy !== null => $issuedBy->name.' issued you the '.$definition->name.' certificate'.(! empty($content['module']) ? ' for '.$content['module'] : '').'. You can view and download it from My Certificates.',
                default => 'You earned the '.$definition->name.' certificate. You can view and download it from My Certificates.',
            },
            route('student.certificates.show', $certificate),
            ['certificate_id' => $certificate->id, 'certificate_key' => $key],
            'certificate-earned:'.$key.($replaces ? ':'.$certificate->id : '')
        );

        return $certificate;
    }

    /** @param list<array<string, mixed>> $items @param array<string, mixed> $content */
    private function snapshot(User $user, CertificateDefinition $definition, object $set, array $items, array $content, string $number, Carbon $issuedAt, ?UserCertificate $replaces): array
    {
        $date = $issuedAt->format('F j, Y');
        $statement = Placeholders::resolve(
            (string) ($content['statement_template'] ?? ''),
            Placeholders::values($content + ['name' => $definition->name], (string) $user->name, $date, $number)
        );

        return array_filter([
            'kind' => $content['kind'] ?? ($definition->is_system ? 'system' : 'class'),
            'certificate_key' => (string) $definition->certificate_key,
            'name' => (string) ($content['name'] ?? $definition->name),
            'description' => (string) ($content['description'] ?? $definition->description),
            'statement_template' => (string) ($content['statement_template'] ?? ''),
            'statement' => $statement,
            'module' => (string) ($content['module'] ?? ''),
            'holder' => ['id' => (int) $user->id, 'name' => (string) $user->name],
            'issuer_name' => (string) ($content['issuer_name'] ?? ''),
            'issuer_line' => (string) ($content['issuer_line'] ?? ''),
            'logo' => $content['logo'] ?? null,
            'signatory_name' => (string) ($content['signatory_name'] ?? ''),
            'signatory_title' => (string) ($content['signatory_title'] ?? ''),
            'layout_key' => CertificateLayouts::exists($content['layout_key'] ?? null) ? $content['layout_key'] : CertificateLayouts::DEFAULT,
            'class_name' => $content['class_name'] ?? null,
            'course' => $content['course'] ?? null,
            'course_code' => $content['course_code'] ?? null,
            'section' => $content['section'] ?? null,
            'semester' => $content['semester'] ?? null,
            'issued_by' => $content['issued_by'] ?? null,
            'config_version' => $content['config_version'] ?? null,
            'requirement_version' => (int) $set->version,
            'requirements' => array_map(fn (array $item) => array_diff_key($item, ['done' => true]), $items),
            'rule' => (string) ($content['rule'] ?? ''),
            'issued_at' => $issuedAt->toIso8601String(),
            'reissued_from' => $replaces?->certificate_number,
        ], fn ($value) => $value !== null);
    }

    /**
     * Checks all three certificates for a learner and issues those now
     * earned. Never throws: certificates must not block learning.
     *
     * @return list<UserCertificate>  the certificates issued by this call
     */
    public function syncUser(User $user): array
    {
        if (! $user->isLearner()) {
            return [];
        }

        try {
            if (! $this->ready()) {
                return [];
            }

            $held = UserCertificate::query()->where('user_id', $user->id)->pluck('certificate_key')->flip();
            $issued = [];
            foreach (array_keys(self::DEFINITIONS) as $key) {
                if (isset($held[$key])) {
                    continue;
                }

                try {
                    $certificate = $this->award($user, $key);
                    if ($certificate !== null && $certificate->wasRecentlyCreated) {
                        $issued[] = $certificate;
                    }
                } catch (Throwable $exception) {
                    report($exception);
                }
            }

            // Class Certificates of Completion are issued by the instructor
            // (ClassCertificateService::issueTo), never automatically.

            return $issued;
        } catch (Throwable $exception) {
            report($exception);

            return [];
        }
    }

    /**
     * Called after progress is saved (a lesson, an MCQ attempt, a coding
     * submission): checks the certificates once the surrounding transaction
     * has committed, so a certificate is only issued for saved progress.
     */
    public function afterProgress(User $user): void
    {
        if (! $user->isLearner()) {
            return;
        }

        try {
            DB::afterCommit(fn () => $this->syncUser($user));
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public function ruleText(string $key): string
    {
        $settings = app(CertificateSettings::class);
        $level = (string) (DB::table('challenge_categories')->where('slug', $settings->get('core_challenge_level'))->value('name') ?? 'Newbie');

        return match ($key) {
            self::CORE_MODULES => 'Every lesson of each of the 24 Core Modules completed.',
            self::CORE_CHALLENGES => 'The '.$level.'-level MCQ challenge of each of the 24 Core Modules passed with '.(int) ChallengePathUnlockService::PASSING_PERCENT.'% or more (any attempt, any version).',
            default => $settings->get('core_coding_all_levels') === '1'
                ? 'Every problem solved in every built-in coding challenge of the Core Modules, on every level (all problems of one version each). Core Modules without coding challenges are not required.'
                : 'Every problem solved in the '.$level.'-level coding challenge of each Core Module that has one (all problems of one version).',
        };
    }

    // ── Progress checks ──────────────────────────────────────────────

    /** @return array<string, true> module keys completed */
    private function completedModules(User $user, array $items): array
    {
        $keys = array_column($items, 'module_key');
        if ($keys === [] || ! SchemaInspector::hasColumn('modules', 'module_key')) {
            return [];
        }

        $modules = DB::table('modules')
            ->where('module_type', CoreCurriculum::TYPE_CORE)
            ->whereIn('module_key', $keys)
            ->pluck('module_key', 'id');
        if ($modules->isEmpty()) {
            return [];
        }

        $done = [];
        DB::table('module_user')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->whereIn('module_id', $modules->keys()->all())
            ->pluck('module_id')
            ->each(function ($moduleId) use ($modules, &$done): void {
                $done[(string) $modules[$moduleId]] = true;
            });

        return $done;
    }

    /** @return array<string, true> content codes of the MCQ challenges passed */
    private function passedMcq(User $user, array $items): array
    {
        $codeToKey = $this->codeMap($items);
        if ($codeToKey === []) {
            return [];
        }

        $challenges = DB::table('challenges')
            ->whereIn('content_code', array_keys($codeToKey))
            ->where('is_coding_challenge', false)
            ->get(['id', 'content_code']);
        if ($challenges->isEmpty()) {
            return [];
        }

        $codeOf = $challenges->pluck('content_code', 'id');
        $percent = (float) ChallengePathUnlockService::PASSING_PERCENT;
        $done = [];

        $attempted = DB::table('challenge_attempts')
            ->where('user_id', $user->id)
            ->whereIn('challenge_id', $codeOf->keys()->all())
            ->distinct()
            ->pluck('challenge_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        DB::table('challenge_attempts')
            ->where('user_id', $user->id)
            ->whereIn('challenge_id', $codeOf->keys()->all())
            ->whereIn('status', ['submitted', 'expired'])
            ->where('total_questions', '>', 0)
            ->whereRaw('score * 100 >= total_questions * ?', [$percent])
            ->distinct()
            ->pluck('challenge_id')
            ->each(function ($id) use ($codeOf, $codeToKey, &$done): void {
                $done[$codeToKey[strtoupper((string) $codeOf[$id])]] = true;
            });

        // Results saved before attempts were recorded (challenge_user), as
        // the challenge map counts them: only for a challenge without attempts.
        if (SchemaInspector::hasTable('challenge_user')) {
            $legacy = DB::table('challenge_user')
                ->where('user_id', $user->id)
                ->whereIn('challenge_id', $codeOf->keys()->all())
                ->get(['challenge_id', 'score']);
            if ($legacy->isNotEmpty()) {
                $questionCounts = DB::table('challenge_questions')
                    ->whereIn('challenge_id', $legacy->pluck('challenge_id')->all())
                    ->select('challenge_id', DB::raw('COUNT(*) as total'))
                    ->groupBy('challenge_id')
                    ->pluck('total', 'challenge_id');
                foreach ($legacy as $row) {
                    $total = (int) ($questionCounts[$row->challenge_id] ?? 0);
                    if ($total > 0 && ! isset($attempted[(int) $row->challenge_id])
                        && (int) $row->score >= (int) ceil($total * $percent / 100)) {
                        $done[$codeToKey[strtoupper((string) $codeOf[$row->challenge_id])]] = true;
                    }
                }
            }
        }

        return $done;
    }

    /** @return array<string, true> content codes of the coding challenges solved */
    private function solvedCoding(User $user, array $items): array
    {
        $codeToKey = $this->codeMap($items);
        if ($codeToKey === []) {
            return [];
        }

        $challenges = DB::table('challenges')
            ->whereIn('content_code', array_keys($codeToKey))
            ->where('is_coding_challenge', true)
            ->get(['id', 'content_code']);
        if ($challenges->isEmpty()) {
            return [];
        }

        $questions = DB::table('coding_questions')
            ->whereIn('challenge_id', $challenges->pluck('id')->all())
            ->get(['id', 'challenge_id'])
            ->groupBy('challenge_id');
        $questionIds = $questions->flatten()->pluck('id')->all();
        if ($questionIds === []) {
            return [];
        }

        $solved = DB::table('coding_submissions')
            ->where('user_id', $user->id)
            ->whereIn('coding_question_id', $questionIds)
            ->where('status', 'passed')
            ->where('voided', false)
            ->distinct()
            ->pluck('coding_question_id')
            ->map(fn ($id) => (int) $id)
            ->flip();

        $done = [];
        foreach ($challenges as $challenge) {
            $ids = $questions->get($challenge->id, collect())->pluck('id');
            if ($ids->isNotEmpty() && $ids->every(fn ($id) => $solved->has((int) $id))) {
                $done[$codeToKey[strtoupper((string) $challenge->content_code)]] = true;
            }
        }

        return $done;
    }

    /** @return array<string, string> content code => content code (upper case) */
    private function codeMap(array $items): array
    {
        $map = [];
        foreach ($items as $item) {
            if (! empty($item['content_code'])) {
                $code = strtoupper((string) $item['content_code']);
                $map[$code] = $code;
            }
        }

        return $map;
    }

    /**
     * A new certificate ID, e.g. DS-MOD-20261001-7KQ2M9XA. Eight random
     * letters and digits (about 2.8 trillion possibilities per day), never
     * reused: a revoked certificate keeps its ID, and the unique index
     * refuses a repeat.
     */
    private function newNumber(string $key, Carbon $issuedAt, bool $system = true): string
    {
        $code = $system ? (self::DEFINITIONS[$key]['short'] ?? 'CRT') : 'CLS';

        return 'DS-'.$code.'-'.$issuedAt->format('Ymd').'-'.strtoupper(Str::random(8));
    }

    // ── Viewing, verification, revocation ────────────────────────────

    /**
     * What a certificate shows, from its snapshot. Values missing from an
     * older snapshot use fixed defaults, never today's settings, so an issued
     * certificate never changes.
     *
     * @return array<string, mixed>
     */
    public function displayData(UserCertificate $certificate): array
    {
        $s = $certificate->snapshotData();
        $holder = (string) ($s['holder']['name'] ?? $certificate->user?->name ?? '');

        return [
            'layout_key' => CertificateLayouts::exists($s['layout_key'] ?? null) ? $s['layout_key'] : self::LEGACY_DEFAULTS['layout_key'],
            'title' => (string) ($s['name'] ?? $certificate->definition?->name ?? 'Certificate'),
            'learner' => $holder,
            'statement' => (string) ($s['statement'] ?? ('This certifies that '.$holder.' has met the requirement: '.rtrim((string) ($s['description'] ?? ''), '.').'.')),
            'module' => (string) ($s['module'] ?? self::SYSTEM_CONTENT[$certificate->certificate_key]['milestone'] ?? ''),
            'issuer_name' => (string) ($s['issuer_name'] ?? self::LEGACY_DEFAULTS['issuer_name']),
            'issuer_line' => (string) ($s['issuer_line'] ?? self::LEGACY_DEFAULTS['issuer_line']),
            'logo' => $s['logo'] ?? null,
            'signatory_name' => (string) ($s['signatory_name'] ?? self::LEGACY_DEFAULTS['signatory_name']),
            'signatory_title' => (string) ($s['signatory_title'] ?? self::LEGACY_DEFAULTS['signatory_title']),
            'date' => $certificate->issued_at?->format('F j, Y') ?? '',
            'certificate_id' => (string) $certificate->certificate_number,
            'verify_url' => $this->verifyUrl((string) $certificate->certificate_number),
            'class_name' => $s['class_name'] ?? null,
            'issued_by' => isset($s['issued_by']['name']) ? (string) $s['issued_by']['name'] : null,
            'requirements' => $s['requirements'] ?? [],
            'rule' => (string) ($s['rule'] ?? ''),
            'kind' => (string) ($s['kind'] ?? 'system'),
        ];
    }

    /** @return list<array<string, mixed>> the drawing of an issued certificate */
    public function layoutItems(UserCertificate $certificate): array
    {
        $data = $this->displayData($certificate);

        return CertificateLayouts::compose($data['layout_key'], $data);
    }

    public function verifyUrl(string $number): string
    {
        return route('certificates.verify.show', ['number' => $number]);
    }

    /** A certificate by its ID, as typed (case and spaces ignored). */
    public function findByNumber(string $number): ?UserCertificate
    {
        $number = strtoupper(trim($number));
        if ($number === '' || strlen($number) > 40 || ! preg_match('/^[A-Z0-9-]+$/', $number)) {
            return null;
        }

        return UserCertificate::query()->where('certificate_number', $number)->first();
    }

    /** Revokes an active certificate, with the administrator and the reason. */
    public function revoke(UserCertificate $certificate, User $admin, string $reason): bool
    {
        return DB::transaction(function () use ($certificate, $admin, $reason): bool {
            $locked = UserCertificate::query()->whereKey($certificate->id)->lockForUpdate()->first();
            if ($locked === null || $locked->isRevoked()) {
                return false;
            }

            $locked->forceFill([
                'status' => UserCertificate::STATUS_REVOKED,
                'active_slot' => null,
                'revoked_at' => now(),
                'revoked_by' => $admin->id,
                'revoke_reason' => Str::limit(trim($reason), 495, ''),
            ])->save();

            return true;
        }, 3);
    }

    /**
     * Reissues a certificate: the current copy is revoked (if it is not
     * already) and a new copy with a new certificate ID is issued from the
     * same snapshot, with the holder's current name. No eligibility check:
     * the learner earned it before, and an administrator decided.
     */
    public function reissue(UserCertificate $certificate, User $admin, string $reason): UserCertificate
    {
        return DB::transaction(function () use ($certificate, $admin, $reason): UserCertificate {
            $locked = UserCertificate::query()->whereKey($certificate->id)->lockForUpdate()->firstOrFail();
            $user = User::query()->findOrFail($locked->user_id);
            $definition = CertificateDefinition::query()->findOrFail($locked->certificate_definition_id);

            // Only the newest copy of a certificate can be reissued.
            $newer = UserCertificate::query()->where('reissued_from_id', $locked->id)->exists();
            abort_if($newer, 409, 'This certificate was already reissued.');

            $active = UserCertificate::query()
                ->where('user_id', $locked->user_id)
                ->where('certificate_definition_id', $locked->certificate_definition_id)
                ->active()
                ->where('id', '!=', $locked->id)
                ->exists();
            abort_if($active, 409, 'The learner already holds an active copy of this certificate.');

            if (! $locked->isRevoked()) {
                $locked->forceFill([
                    'status' => UserCertificate::STATUS_REVOKED,
                    'active_slot' => null,
                    'revoked_at' => now(),
                    'revoked_by' => $admin->id,
                    'revoke_reason' => Str::limit('Reissued: '.trim($reason), 495, ''),
                ])->save();
            }

            $old = $locked->snapshotData();
            $set = DB::table('certificate_requirement_sets')->where('id', $locked->requirement_set_id)->first()
                ?? (object) ['id' => $locked->requirement_set_id, 'version' => $locked->requirement_version];

            return $this->issue($user, $definition, $set, (array) ($old['requirements'] ?? []), [
                'kind' => $old['kind'] ?? ($definition->is_system ? 'system' : 'class'),
                'name' => $old['name'] ?? $definition->name,
                'description' => $old['description'] ?? $definition->description,
                'statement_template' => $old['statement_template'] ?? (self::SYSTEM_CONTENT[$definition->certificate_key]['statement'] ?? ''),
                'module' => $old['module'] ?? (self::SYSTEM_CONTENT[$definition->certificate_key]['milestone'] ?? ''),
                'issuer_name' => $old['issuer_name'] ?? self::LEGACY_DEFAULTS['issuer_name'],
                'issuer_line' => $old['issuer_line'] ?? self::LEGACY_DEFAULTS['issuer_line'],
                'logo' => $old['logo'] ?? null,
                'signatory_name' => $old['signatory_name'] ?? self::LEGACY_DEFAULTS['signatory_name'],
                'signatory_title' => $old['signatory_title'] ?? self::LEGACY_DEFAULTS['signatory_title'],
                'layout_key' => $old['layout_key'] ?? self::LEGACY_DEFAULTS['layout_key'],
                'class_name' => $old['class_name'] ?? null,
                'course' => $old['course'] ?? null,
                'course_code' => $old['course_code'] ?? null,
                'section' => $old['section'] ?? null,
                'semester' => $old['semester'] ?? null,
                'issued_by' => $old['issued_by'] ?? null,
                'config_version' => $old['config_version'] ?? null,
                'rule' => $old['rule'] ?? '',
            ], $locked->class_id, $locked);
        }, 3);
    }

    /**
     * An issue or reissue in the audit log (revocations and reissues done by
     * an administrator are also recorded by the request itself).
     */
    private function audit(string $action, UserCertificate $certificate, ?string $details, ?User $actor = null): void
    {
        if (! SchemaInspector::hasTable('audit_logs') || $action !== 'issued') {
            return;
        }

        try {
            AuditLog::query()->create([
                'user_id' => $actor?->id,
                'user_name' => $actor ? Str::limit((string) $actor->name, 185, '') : 'DataSensei (automatic)',
                'user_role' => $actor ? (int) $actor->role : null,
                'action' => 'issued',
                'record_type' => 'Certificate',
                'record_id' => $certificate->id,
                'record_label' => Str::limit($certificate->certificate_number.', '.$certificate->snapshotData()['name'].' for '.($certificate->snapshotData()['holder']['name'] ?? ''), 250),
                'details' => $details,
                'created_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
