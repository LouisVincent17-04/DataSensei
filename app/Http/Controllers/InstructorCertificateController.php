<?php

namespace App\Http\Controllers;

use App\Models\CertificateDefinition;
use App\Models\ClassRoom;
use App\Models\User;
use App\Services\CertificateSettings;
use App\Services\ClassCertificateService;
use App\Support\Certificates\CertificateHtml;
use App\Support\Certificates\CertificateLayouts;
use App\Support\Certificates\CertificatePdf;
use App\Support\Certificates\Placeholders;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Certificate Builder: an instructor's Certificate of Completion for their
 * own classes. See App\Services\ClassCertificateService for the rules.
 *
 *   1  choose one of the five layouts; the preview updates live
 *   2  the class supplies the course information (course name and code,
 *      section, semester) through placeholders
 *   3  save (Draft), check the preview, activate
 *   4  at the end of the semester, review every student's completion and
 *      grades, select the eligible students and issue the certificate
 *
 * Authorization is checked here on every request: a configuration is found
 * only when the signed-in instructor owns it and still teaches its class,
 * otherwise 404. Class and student ids posted by the browser are checked
 * against what this instructor may use; the signatory is always the
 * signed-in instructor, and eligibility is decided on the server.
 */
class InstructorCertificateController extends Controller
{
    public function __construct(
        private readonly ClassCertificateService $builder,
        private readonly CertificateSettings $settings,
    ) {
    }

    public function index(): View
    {
        $instructor = $this->instructor();
        $definitions = CertificateDefinition::query()->classCertificates()
            ->where('owner_user_id', $instructor->id)
            ->with('classRoom:id,name,section,term,academic_year,subject_code,instructor_id,is_archived')
            ->withCount('issued')
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn (CertificateDefinition $d) => (int) $d->classRoom?->instructor_id === (int) $instructor->id);

        return view('instructor.certificates.index', [
            'definitions' => $definitions,
            'hasClasses' => $this->builder->classes($instructor)->isNotEmpty(),
        ]);
    }

    public function create(Request $request): View
    {
        $definition = new CertificateDefinition(['name' => 'Certificate of Completion']);
        $definition->forceFill([
            'statement' => ClassCertificateService::DEFAULT_STATEMENT,
            'signatory_title' => ClassCertificateService::DEFAULT_TITLE,
            // No layout yet: the instructor chooses one first.
            'layout_key' => null,
            'class_id' => $request->integer('class_id') ?: null,
        ]);

        return $this->form($definition);
    }

    public function store(Request $request): RedirectResponse
    {
        $instructor = $this->instructor();
        [$definition] = $this->builder->save($instructor, $this->builder->validated($request, $instructor));

        return redirect()->route('instructor.certificates.show', $definition)
            ->with('success', 'Saved as a draft. Check the certificate below, then activate it.');
    }

    public function edit(CertificateDefinition $certificate): View
    {
        return $this->form($this->owned((int) $certificate->getKey()));
    }

    public function update(Request $request, CertificateDefinition $certificate): RedirectResponse
    {
        $instructor = $this->instructor();
        $definition = $this->owned((int) $certificate->getKey());
        [$definition, $backToDraft] = $this->builder->save($instructor, $this->builder->validated($request, $instructor, $definition), $definition);

        return redirect()->route('instructor.certificates.show', $definition)->with('success', $backToDraft
            ? 'Saved. The certificate is back in Draft; check it below and activate it again before issuing.'
            : 'Saved. Check the certificate below.');
    }

    /**
     * The live preview of the form as it is now, unsaved: the selected
     * layout drawn with a sample learner. Returns the drawing and any unknown
     * placeholders; nothing is stored.
     */
    public function livePreview(Request $request): JsonResponse
    {
        $instructor = $this->instructor();
        $layout = (string) $request->input('layout_key', '');
        if (! CertificateLayouts::exists($layout)) {
            return response()->json(['svg' => null, 'unknown' => []]);
        }

        $class = $this->builder->classes($instructor)->firstWhere('id', $request->integer('class_id'));
        $statement = Str::limit(trim((string) $request->input('statement', '')), 600, '');
        $data = $this->builder->livePreviewData(
            $instructor,
            $class,
            Str::limit(trim((string) $request->input('name', '')), 120, ''),
            $statement,
            Str::limit(trim((string) $request->input('signatory_title', '')), 120, ''),
            $layout,
        );

        return response()->json([
            'svg' => (string) CertificateHtml::render(CertificateLayouts::compose($layout, $data), 'Live preview'),
            'unknown' => Placeholders::unknown($statement),
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * The certificate with a sample learner (previewing allows activation),
     * and, once active, every student of the class with their completion,
     * final grade and certificate, to select and issue.
     */
    public function show(CertificateDefinition $certificate): View
    {
        $definition = $this->owned((int) $certificate->getKey());
        $this->builder->markPreviewed($definition);
        $data = $this->builder->previewData($definition);

        return view('instructor.certificates.show', [
            'definition' => $definition->refresh(),
            'svg' => CertificateHtml::render(CertificateLayouts::compose($data['layout_key'], $data), 'Preview of '.$definition->name),
            'course' => $this->builder->course($definition->classRoom),
            'rule' => $this->builder->ruleText($definition),
            'problem' => $this->builder->activationProblem($definition),
            'review' => $this->builder->review($definition),
            'issuedCount' => $this->builder->issuedCount($definition),
            'layoutEnabled' => $this->settings->layoutEnabled($definition->layout_key),
        ]);
    }

    public function previewPdf(CertificateDefinition $certificate): Response
    {
        $definition = $this->owned((int) $certificate->getKey());
        $this->builder->markPreviewed($definition);
        $data = $this->builder->previewData($definition);

        return response(CertificatePdf::render(CertificateLayouts::compose($data['layout_key'], $data), 'Preview: '.$definition->name), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="certificate-preview.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function activate(CertificateDefinition $certificate): RedirectResponse
    {
        $definition = $this->owned((int) $certificate->getKey());

        try {
            $this->builder->activate($definition);
        } catch (ValidationException $exception) {
            return redirect()->route('instructor.certificates.show', $definition)
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()->route('instructor.certificates.show', $definition)
            ->with('success', 'The certificate is active. At the end of the semester, select the students who completed the class below and issue it.');
    }

    public function deactivate(CertificateDefinition $certificate): RedirectResponse
    {
        $definition = $this->owned((int) $certificate->getKey());
        if ($definition->status !== CertificateDefinition::STATUS_ACTIVE) {
            return redirect()->route('instructor.certificates.show', $definition)->with('error', 'This certificate is not active.');
        }
        $this->builder->deactivate($definition);

        return redirect()->route('instructor.certificates.show', $definition)
            ->with('success', 'The certificate is inactive. It cannot be issued; the ones already issued stay valid.');
    }

    /** Issues the certificate to the selected students who completed the class. */
    public function issue(Request $request, CertificateDefinition $certificate): RedirectResponse
    {
        $definition = $this->owned((int) $certificate->getKey());
        $selected = $request->validate([
            'students' => ['required', 'array', 'max:500'],
            'students.*' => ['integer'],
        ], [
            'students.required' => 'Select at least one student who completed the class.',
        ])['students'];

        try {
            $result = $this->builder->issueTo($definition, $this->instructor(), $selected);
        } catch (ValidationException $exception) {
            return redirect()->route('instructor.certificates.show', $definition)
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        $count = count($result['issued']);
        $message = $count === 0
            ? 'No certificate was issued.'
            : 'Issued the certificate to '.$count.' '.($count === 1 ? 'student' : 'students').'.';
        if ($result['skipped'] !== []) {
            $message .= ' Not issued: '.collect($result['skipped'])->map(fn (string $why, string $name) => $name.' ('.$why.')')->implode('; ').'.';
        }

        return redirect()->route('instructor.certificates.show', $definition)->with($count > 0 ? 'success' : 'error', $message);
    }

    public function destroy(CertificateDefinition $certificate): RedirectResponse
    {
        $definition = $this->owned((int) $certificate->getKey());
        if ($definition->status === CertificateDefinition::STATUS_ACTIVE || $this->builder->issuedCount($definition) > 0) {
            return redirect()->route('instructor.certificates.show', $definition)
                ->with('error', 'Only a certificate that is not active and was never issued can be deleted. Make it inactive instead.');
        }

        $definition->delete();

        return redirect()->route('instructor.certificates.index')->with('success', 'Certificate deleted.');
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function form(CertificateDefinition $definition): View
    {
        $instructor = $this->instructor();
        $classes = $this->builder->classes($instructor);
        [$issuerName, , $issuerLogo] = $this->builder->issuer($instructor);
        $sampleData = [
            'title' => 'Certificate of Completion',
            'learner' => ClassCertificateService::SAMPLE_LEARNER,
            'statement' => 'This certifies that Sample Learner has successfully completed the course.',
            'issuer_name' => $issuerName,
            'logo' => $issuerLogo,
            'signatory_name' => $instructor->name,
            'signatory_title' => ClassCertificateService::DEFAULT_TITLE,
            'date' => now()->format('F j, Y'),
            'certificate_id' => 'DS-CLS-SAMPLE',
        ];
        $layouts = [];
        foreach ($this->settings->layouts() as $key => $enabled) {
            if ($enabled || $definition->layout_key === $key) {
                $layouts[$key] = [
                    'name' => CertificateLayouts::name($key),
                    'description' => CertificateLayouts::LAYOUTS[$key]['description'],
                    'enabled' => $enabled,
                    'thumbnail' => CertificateHtml::render(CertificateLayouts::compose($key, $sampleData), CertificateLayouts::name($key)),
                ];
            }
        }

        $selectedClass = $classes->firstWhere('id', (int) old('class_id', $definition->class_id));
        $layout = (string) old('layout_key', (string) $definition->layout_key);
        $preview = null;
        if (CertificateLayouts::exists($layout)) {
            $data = $this->builder->livePreviewData(
                $instructor,
                $selectedClass,
                (string) old('name', (string) $definition->name),
                (string) old('statement', (string) $definition->statement),
                (string) old('signatory_title', (string) $definition->signatory_title),
                $layout,
            );
            $preview = CertificateHtml::render(CertificateLayouts::compose($layout, $data), 'Live preview');
        }

        return view('instructor.certificates.form', [
            'definition' => $definition,
            'classes' => $classes,
            'courses' => $classes->mapWithKeys(fn (ClassRoom $class) => [$class->id => $this->builder->course($class)]),
            'layouts' => $layouts,
            'issuer' => $issuerName,
            'instructor' => $instructor,
            'locked' => $definition->exists && $this->builder->issuedCount($definition) > 0,
            'preview' => $preview,
        ]);
    }

    /** A configuration the signed-in instructor owns, for a class they teach; 404 otherwise. */
    private function owned(int $id): CertificateDefinition
    {
        $instructor = $this->instructor();
        $definition = CertificateDefinition::query()->classCertificates()
            ->whereKey($id)
            ->where('owner_user_id', $instructor->id)
            ->first();

        abort_if($definition === null, 404);
        abort_unless(ClassRoom::query()->whereKey($definition->class_id)->where('instructor_id', $instructor->id)->exists(), 404);

        return $definition;
    }

    private function instructor(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }
}
