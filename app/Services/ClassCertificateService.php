<?php

namespace App\Services;

use App\Models\CertificateDefinition;
use App\Models\ClassRoom;
use App\Models\Institution;
use App\Models\User;
use App\Models\UserCertificate;
use App\Services\Reports\ClassCompletion;
use App\Support\Certificates\CertificateBranding;
use App\Support\Certificates\CertificateLayouts;
use App\Support\Certificates\Placeholders;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

/**
 * The instructor Certificate Builder: a Certificate of Completion for one of
 * the instructor's classes (DataSensei Updates 13, reworked by the Combined
 * Certificate and Gradebook Requirements).
 *
 * The certificate is for completing the class, the instructor-assigned
 * course of the semester, never one module and never every DataSensei
 * module. Its course information (course name and code, section, semester)
 * comes from the class record and is used through placeholders.
 *
 *   layout     one of the five predefined layouts an admin has enabled; the
 *              preview updates live while the instructor edits
 *   wording    certificate name, statement with placeholders, and the
 *              instructor's title. The signatory is always the signed-in
 *              instructor; a name typed into the request is ignored.
 *   issuer     the instructor's institution, or the DataSensei issuer set by
 *              an admin
 *
 * A configuration is a Draft until the instructor has seen its preview and
 * activates it. At the end of the semester the instructor reviews each
 * student's completion and grades and issues the certificate to the
 * students they select. The server issues it only to students enrolled in
 * the class who completed every required module, activity and assessment
 * (App\Services\Reports\ClassCompletion), and only once per student. Nothing
 * is issued automatically. After the first certificate is issued, the class
 * is locked; the wording and layout can still change for later ones.
 */
class ClassCertificateService
{
    public const DEFAULT_STATEMENT = 'This certifies that [Learner Name] has successfully completed all required modules, activities and assessments of [Course Name] ([Semester]).';

    public const DEFAULT_TITLE = 'Course Instructor';

    public const SAMPLE_LEARNER = 'Sample Learner';

    public function __construct(
        private readonly CertificateSettings $settings,
        private readonly ClassCompletion $completion,
    ) {
    }

    // ── Choices an instructor has ────────────────────────────────────

    /** @return EloquentCollection<int, ClassRoom> the instructor's classes, open ones first */
    public function classes(User $instructor): EloquentCollection
    {
        return ClassRoom::query()->forInstructor((int) $instructor->id)->orderBy('is_archived')->orderBy('name')->orderBy('section')->get();
    }

    /**
     * The course information of a class, from the class record.
     *
     * @return array{course: string, course_code: string, section: string, semester: string, title: string, milestone: string}
     */
    public function course(?ClassRoom $class): array
    {
        if ($class === null) {
            return ['course' => 'Course name', 'course_code' => 'Course code', 'section' => 'Section', 'semester' => 'Semester', 'title' => 'Course name', 'milestone' => 'Course name'];
        }

        $course = trim((string) $class->name);
        $code = trim((string) $class->subject_code);
        $semester = trim((string) ($class->term ?: $class->academic_year));
        $title = trim($code.' '.$course);

        return [
            'course' => $course,
            'course_code' => $code,
            'section' => trim((string) $class->section),
            'semester' => $semester,
            'title' => $title,
            'milestone' => $title.($semester !== '' ? ', '.$semester : ''),
        ];
    }

    // ── Saving a configuration ───────────────────────────────────────

    /**
     * Checks a posted configuration against what this instructor may use.
     * Every message is keyed by the field it belongs to.
     *
     * @return array<string, mixed>
     */
    public function validated(Request $request, User $instructor, ?CertificateDefinition $existing = null): array
    {
        $layouts = $this->settings->enabledLayouts();
        if ($existing && CertificateLayouts::exists($existing->layout_key) && ! in_array($existing->layout_key, $layouts, true)) {
            // A layout turned off later stays usable for the configuration that has it.
            $layouts[] = $existing->layout_key;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'class_id' => ['required', 'integer'],
            'signatory_title' => ['required', 'string', 'max:120'],
            'statement' => ['required', 'string', 'max:600'],
            'layout_key' => ['required', Rule::in($layouts)],
        ], [
            'name.required' => 'Give the certificate a name, for example Certificate of Completion.',
            'class_id.required' => 'Choose one of your classes.',
            'signatory_title.required' => 'Enter your title, for example Course Instructor.',
            'statement.required' => 'Write the certificate statement.',
            'layout_key.required' => 'Choose one of the five layouts first.',
            'layout_key.in' => 'Choose one of the layouts that are turned on.',
        ]);

        $errors = [];
        $class = $this->classes($instructor)->firstWhere('id', (int) $data['class_id']);
        if ($class === null) {
            $errors['class_id'] = 'Choose one of your classes.';
        }

        $unknown = Placeholders::unknown($data['statement']);
        if ($unknown !== []) {
            $errors['statement'] = 'Unknown placeholder '.implode(', ', $unknown).'. Use only: '.implode(', ', Placeholders::SHOWN).'.';
        }

        if ($existing !== null && (int) $existing->class_id !== (int) $data['class_id'] && $this->issuedCount($existing) > 0) {
            $errors['class_id'] = 'Certificates were already issued for this class, so the class is locked. Create a new certificate for another class.';
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return [
            'name' => trim($data['name']),
            'class_id' => (int) $class->id,
            'signatory_title' => trim($data['signatory_title']),
            'statement' => trim(preg_replace('/\s+/u', ' ', $data['statement']) ?? $data['statement']),
            'layout_key' => $data['layout_key'],
        ];
    }

    /**
     * Creates or updates a configuration. A change to an Active one returns
     * it to Draft. Returns the configuration and whether it went back to Draft.
     *
     * @param  array<string, mixed>  $data  from validated()
     * @return array{0: CertificateDefinition, 1: bool}
     */
    public function save(User $instructor, array $data, ?CertificateDefinition $existing = null): array
    {
        return DB::transaction(function () use ($instructor, $data, $existing): array {
            if ($existing === null) {
                $definition = new CertificateDefinition([
                    'certificate_key' => 'class-'.Str::lower((string) Str::uuid()),
                    'name' => $data['name'],
                    'description' => null,
                    'audience' => 'class',
                    'is_system' => false,
                    'is_active' => false,
                    'current_version' => 0,
                ]);
                $definition->forceFill($this->attributes($data) + [
                    'owner_user_id' => $instructor->id,
                    'status' => CertificateDefinition::STATUS_DRAFT,
                    'config_version' => 1,
                    'previewed_version' => 0,
                ])->save();

                return [$definition, false];
            }

            $definition = CertificateDefinition::query()->whereKey($existing->id)->lockForUpdate()->firstOrFail();
            $definition->forceFill($this->attributes($data) + ['name' => $data['name']]);
            if (! $definition->isDirty()) {
                return [$definition, false];
            }

            $backToDraft = $definition->status === CertificateDefinition::STATUS_ACTIVE;
            $definition->forceFill([
                'config_version' => (int) $definition->config_version + 1,
                'status' => $backToDraft ? CertificateDefinition::STATUS_DRAFT : $definition->status,
                'is_active' => $backToDraft ? false : (bool) $definition->is_active,
            ])->save();

            return [$definition, $backToDraft];
        }, 3);
    }

    /** @param array<string, mixed> $data */
    private function attributes(array $data): array
    {
        return [
            'class_id' => $data['class_id'],
            'signatory_title' => $data['signatory_title'],
            'statement' => $data['statement'],
            'layout_key' => $data['layout_key'],
        ];
    }

    /** Records that the current version was previewed (activation needs it). */
    public function markPreviewed(CertificateDefinition $definition): void
    {
        if ((int) $definition->previewed_version !== (int) $definition->config_version) {
            $definition->forceFill(['previewed_version' => (int) $definition->config_version])->save();
        }
    }

    /** Activates a configuration after its preview; nothing is issued yet. */
    public function activate(CertificateDefinition $definition): void
    {
        $problem = $this->activationProblem($definition);
        if ($problem !== null) {
            throw ValidationException::withMessages(['activate' => $problem]);
        }

        $definition->forceFill([
            'status' => CertificateDefinition::STATUS_ACTIVE,
            'is_active' => true,
            'activated_at' => now(),
        ])->save();
    }

    public function deactivate(CertificateDefinition $definition): void
    {
        $definition->forceFill([
            'status' => CertificateDefinition::STATUS_INACTIVE,
            'is_active' => false,
        ])->save();
    }

    /** Why the configuration cannot be activated now, or null. */
    public function activationProblem(CertificateDefinition $definition): ?string
    {
        if ($definition->status === CertificateDefinition::STATUS_ACTIVE) {
            return 'This certificate is already active.';
        }
        if ((int) $definition->previewed_version !== (int) $definition->config_version) {
            return 'Preview the certificate before activating it, so you see exactly what students will receive.';
        }
        if ($definition->classRoom === null) {
            return 'The class no longer exists, so this certificate cannot be activated.';
        }
        if (! $this->settings->layoutEnabled($definition->layout_key)) {
            return 'An administrator turned this layout off. Edit the certificate and choose another layout.';
        }

        return null;
    }

    // ── Requirements and completion ──────────────────────────────────

    /**
     * What the class requires now, for the certificate's versioned
     * requirement set: every module, activity and assessment assigned to it.
     *
     * @return list<array<string, mixed>>
     */
    public function requirementItems(CertificateDefinition $definition): array
    {
        $class = $definition->classRoom;
        if ($class === null) {
            return [];
        }

        $items = [];
        foreach ($this->completion->forClass($class, [])['requirements'] as $group) {
            foreach ($group as $item) {
                $items[] = ['type' => $item['type'], 'id' => $item['id'], 'title' => $item['title'], 'kind' => $item['kind'], 'class_id' => (int) $class->id];
            }
        }

        return $items;
    }

    /**
     * Every enrolled student with their completion, final grade and the
     * certificate they hold, for the instructor's review.
     *
     * @return array{requirements: array<string, list<array<string, mixed>>>, total: int, rows: list<array<string, mixed>>}
     */
    public function review(CertificateDefinition $definition): array
    {
        $class = $definition->classRoom;
        if ($class === null) {
            return ['requirements' => [], 'total' => 0, 'rows' => []];
        }

        $book = $this->completion->forClass($class);
        $held = UserCertificate::query()->where('certificate_definition_id', $definition->id)->orderByDesc('id')->get()->groupBy('user_id');

        $rows = [];
        foreach ($book['students'] as $studentId => $row) {
            $copies = $held->get($studentId, collect());
            $rows[] = $row + [
                'certificate' => $copies->first(fn (UserCertificate $c) => ! $c->isRevoked()) ?? $copies->first(),
                'holds' => $copies->isNotEmpty(),
            ];
        }

        return ['requirements' => $book['requirements'], 'total' => $book['total'], 'rows' => $rows];
    }

    /**
     * Issues the certificate to the selected students. Each one is checked
     * again on the server: enrolled in the class, every required item
     * complete, and not holding this certificate already. Ids that are not
     * students of the class are ignored.
     *
     * @param  list<int>  $studentIds
     * @return array{issued: list<UserCertificate>, skipped: array<string, string>}
     */
    public function issueTo(CertificateDefinition $definition, User $instructor, array $studentIds): array
    {
        if ($definition->status !== CertificateDefinition::STATUS_ACTIVE) {
            throw ValidationException::withMessages(['students' => 'Activate the certificate before issuing it.']);
        }

        $class = $definition->classRoom;
        $studentIds = array_values(array_unique(array_filter(array_map('intval', $studentIds), fn (int $id) => $id > 0)));
        if ($class === null || $studentIds === []) {
            throw ValidationException::withMessages(['students' => 'Select at least one student who completed the class.']);
        }

        $book = $this->completion->forClass($class, $studentIds);
        $certificates = app(CertificateService::class);
        $set = $certificates->currentRequirementSet($definition);
        if ($set === null || $book['total'] === 0) {
            throw ValidationException::withMessages(['students' => 'This class has no assigned modules, activities or assessments, so there is nothing to complete yet.']);
        }

        $items = array_map(fn (array $item) => $item + ['done' => true], $this->requirementItems($definition));
        $content = $this->content($definition);
        $issued = [];
        $skipped = [];

        foreach (User::query()->whereIn('id', array_keys($book['students']) ?: [0])->orderBy('name')->get() as $student) {
            $row = $book['students'][(int) $student->id];
            if (UserCertificate::query()->where('user_id', $student->id)->where('certificate_definition_id', $definition->id)->exists()) {
                $skipped[$student->name] = 'already holds this certificate';
                continue;
            }
            if (! $student->isLearner()) {
                $skipped[$student->name] = 'not a student account';
                continue;
            }
            if (! $row['complete']) {
                $skipped[$student->name] = 'has not completed '.($row['total'] - $row['done']).' of '.$row['total'].' required items';
                continue;
            }

            try {
                $certificate = $certificates->issue($student, $definition, $set, $items, $content, (int) $class->id, null, $instructor);
                if ($certificate !== null && $certificate->wasRecentlyCreated) {
                    $issued[] = $certificate;
                } else {
                    $skipped[$student->name] = 'already holds this certificate';
                }
            } catch (Throwable $exception) {
                report($exception);
                $skipped[$student->name] = 'could not be issued; try again';
            }
        }

        return ['issued' => $issued, 'skipped' => $skipped];
    }

    public function issuedCount(CertificateDefinition $definition): int
    {
        return UserCertificate::query()->where('certificate_definition_id', $definition->id)->count();
    }

    // ── What the certificate shows ───────────────────────────────────

    /**
     * The values a class certificate is issued with (resolved now, kept in
     * the snapshot).
     *
     * @return array<string, mixed>
     */
    public function content(CertificateDefinition $definition): array
    {
        $owner = $definition->owner;
        [$issuerName, $issuerLine, $logo] = $this->issuer($owner);
        $course = $this->course($definition->classRoom);

        return [
            'kind' => 'class',
            'name' => (string) $definition->name,
            'description' => null,
            'statement_template' => (string) $definition->statement,
            'module' => $course['milestone'],
            'course' => $course['course'],
            'course_code' => $course['course_code'],
            'section' => $course['section'],
            'semester' => $course['semester'],
            'issuer_name' => $issuerName,
            'issuer_line' => $issuerLine,
            'logo' => $logo,
            'signatory_name' => (string) ($owner?->name ?? ''),
            'signatory_title' => (string) $definition->signatory_title,
            'layout_key' => (string) $definition->layout_key,
            'class_name' => $definition->classRoom ? trim($definition->classRoom->name.($definition->classRoom->section ? ', '.$definition->classRoom->section : '')) : null,
            'config_version' => (int) $definition->config_version,
            'rule' => $this->ruleText($definition),
        ];
    }

    /**
     * The issuer: the instructor's institution (with its logo) when they
     * belong to one, otherwise the DataSensei issuer (and logo) an admin set.
     *
     * @return array{0: string, 1: string, 2: string|null}
     */
    public function issuer(?User $owner): array
    {
        $institution = $owner?->institution_id ? Institution::query()->find($owner->institution_id) : null;
        if ($institution !== null && trim((string) $institution->name) !== '') {
            return [trim((string) $institution->name), 'Issued through '.$this->settings->get('issuer_name'), CertificateBranding::logo($institution)];
        }

        return [$this->settings->get('issuer_name'), $this->settings->get('issuer_line'), $this->settings->logo()];
    }

    public function ruleText(CertificateDefinition $definition): string
    {
        $class = $definition->classRoom;
        $name = $class ? trim($class->name.($class->section ? ', '.$class->section : '')) : 'the class';

        return 'Complete every module, activity and assessment assigned to '.$name
            .' (each assessment turned in and graded), confirmed by the server when the instructor issues the certificate.';
    }

    /**
     * The preview of a saved configuration: a sample learner, today's date
     * and a sample certificate ID.
     *
     * @return array<string, mixed>
     */
    public function previewData(CertificateDefinition $definition): array
    {
        return $this->sample($this->content($definition));
    }

    /**
     * The live preview while the instructor edits, from the unsaved form.
     * Only the signed-in instructor's own class is used; anything else shows
     * the course placeholders.
     *
     * @return array<string, mixed>
     */
    public function livePreviewData(User $instructor, ?ClassRoom $class, string $name, string $statement, string $title, string $layout): array
    {
        [$issuerName, $issuerLine, $logo] = $this->issuer($instructor);
        $course = $this->course($class);

        return $this->sample([
            'name' => $name !== '' ? $name : 'Certificate of Completion',
            'statement_template' => $statement,
            'module' => $course['milestone'],
            'course' => $course['course'],
            'course_code' => $course['course_code'],
            'section' => $course['section'],
            'semester' => $course['semester'],
            'issuer_name' => $issuerName,
            'issuer_line' => $issuerLine,
            'logo' => $logo,
            'signatory_name' => (string) $instructor->name,
            'signatory_title' => $title !== '' ? $title : self::DEFAULT_TITLE,
            'layout_key' => $layout,
        ]);
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, mixed>
     */
    private function sample(array $content): array
    {
        $date = now()->format('F j, Y');
        $id = 'DS-CLS-'.now()->format('Ymd').'-SAMPLE00';

        return [
            'layout_key' => $content['layout_key'],
            'title' => $content['name'],
            'learner' => self::SAMPLE_LEARNER,
            'statement' => Placeholders::resolve((string) $content['statement_template'], Placeholders::values($content, self::SAMPLE_LEARNER, $date, $id)),
            'module' => $content['module'],
            'issuer_name' => $content['issuer_name'],
            'issuer_line' => $content['issuer_line'],
            'logo' => $content['logo'],
            'signatory_name' => $content['signatory_name'],
            'signatory_title' => $content['signatory_title'],
            'date' => $date,
            'certificate_id' => $id,
            'verify_url' => app(CertificateService::class)->verifyUrl($id),
        ];
    }
}
