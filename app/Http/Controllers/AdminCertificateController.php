<?php

namespace App\Http\Controllers;

use App\Models\CertificateDefinition;
use App\Models\User;
use App\Models\UserCertificate;
use App\Services\CertificateService;
use App\Services\CertificateSettings;
use App\Support\Certificates\CertificateBranding;
use App\Support\Certificates\CertificateHtml;
use App\Support\Certificates\CertificateLayouts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Certificates for administrators (DataSensei Updates 13).
 *
 *   Settings            issuer name and line, global signatory, the layout
 *                       of the three core certificates, and their official
 *                       requirement set (challenge level; coding challenges
 *                       of one level or of every level)
 *   Core certificates   turn each of the three system certificates on or off
 *                       and read its current requirement version
 *   Layouts             turn any of the five predefined layouts on or off
 *   Issued              every certificate issued, with revoke and reissue
 *
 * Changes apply to certificates issued afterwards. A certificate already
 * issued keeps its snapshot; it is never changed retroactively, only revoked
 * or reissued, each with a reason that the audit log records.
 */
class AdminCertificateController extends Controller
{
    public const TABS = [
        'issued' => 'Issued certificates',
        'core' => 'Core certificates',
        'settings' => 'Issuer and signatory',
        'layouts' => 'Layouts',
    ];

    public function __construct(
        private readonly CertificateService $certificates,
        private readonly CertificateSettings $settings,
    ) {
    }

    public function index(Request $request): View
    {
        $tab = array_key_exists((string) $request->query('tab'), self::TABS) ? (string) $request->query('tab') : 'issued';
        $definitions = $this->certificates->ensureDefinitions();

        $data = [
            'tab' => $tab,
            'tabs' => self::TABS,
            'settings' => $this->settings->all(),
            'layouts' => $this->settings->layouts(),
            'levels' => DB::table('challenge_categories')->orderBy('order_index')->orderBy('id')->get(['slug', 'name']),
        ];

        if ($tab === 'core') {
            $data['core'] = $definitions->map(function (CertificateDefinition $definition) {
                $set = $this->certificates->currentRequirementSet($definition);

                return [
                    'definition' => $definition,
                    'set' => $set,
                    'items' => $set ? (array) json_decode((string) $set->requirements, true) : [],
                    'rule' => $this->certificates->ruleText((string) $definition->certificate_key),
                    'issued' => UserCertificate::query()->where('certificate_definition_id', $definition->id)->active()->count(),
                    'versions' => DB::table('certificate_requirement_sets')->where('certificate_definition_id', $definition->id)->orderByDesc('version')->get(['version', 'item_count', 'created_at']),
                ];
            })->values();
        }

        if ($tab === 'issued') {
            $search = trim((string) $request->query('q', ''));
            $status = (string) $request->query('status', '');
            $definitionId = (int) $request->query('certificate', 0);
            $data['filters'] = ['q' => $search, 'status' => $status, 'certificate' => $definitionId];
            $data['definitionChoices'] = CertificateDefinition::query()->orderByDesc('is_system')->orderBy('name')->get(['id', 'name', 'is_system']);
            $data['issued'] = UserCertificate::query()
                ->with(['user:id,name', 'definition:id,name,is_system'])
                ->when($search !== '', fn ($q) => $q->where(function ($inner) use ($search): void {
                    $inner->where('certificate_number', 'like', '%'.strtoupper($search).'%')
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$search.'%'));
                }))
                ->when($status === 'active', fn ($q) => $q->active())
                ->when($status === 'revoked', fn ($q) => $q->where('status', UserCertificate::STATUS_REVOKED))
                ->when($definitionId > 0, fn ($q) => $q->where('certificate_definition_id', $definitionId))
                ->orderByDesc('issued_at')
                ->orderByDesc('id')
                ->paginate(20)
                ->withQueryString();
        }

        return view('admin.certificates.index', $data);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $enabled = $this->settings->enabledLayouts();
        $data = $request->validate([
            'issuer_name' => ['required', 'string', 'max:'.CertificateSettings::LIMITS['issuer_name']],
            'issuer_line' => ['nullable', 'string', 'max:'.CertificateSettings::LIMITS['issuer_line']],
            'signatory_name' => ['required', 'string', 'max:'.CertificateSettings::LIMITS['signatory_name']],
            'signatory_title' => ['required', 'string', 'max:'.CertificateSettings::LIMITS['signatory_title']],
            'system_layout' => ['required', Rule::in($enabled)],
            'core_challenge_level' => ['required', Rule::exists('challenge_categories', 'slug')],
            'core_coding_all_levels' => ['nullable', 'boolean'],
            'issuer_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [
            'issuer_name.required' => 'Enter the issuer name shown on the core certificates.',
            'signatory_name.required' => 'Enter the signatory shown on the core certificates.',
            'signatory_title.required' => 'Enter the signatory\'s title.',
            'system_layout.in' => 'Choose a layout that is turned on.',
            'core_challenge_level.exists' => 'Choose one of the challenge levels.',
            'issuer_logo.mimes' => 'Upload the logo as a JPG, PNG, GIF or WebP image.',
            'issuer_logo.max' => 'The logo can be at most 2 MB.',
        ]);

        // The logo is kept as a small JPEG, so certificates carry it in their
        // snapshot; an uploaded file is never stored or served as it is.
        $upload = $request->file('issuer_logo');
        $removeLogo = ! empty($data['remove_logo']);
        unset($data['issuer_logo'], $data['remove_logo']);
        if ($upload !== null) {
            $logo = CertificateBranding::jpeg((string) file_get_contents($upload->getRealPath()));
            if ($logo === null) {
                return redirect()->route('admin.certificates.index', ['tab' => 'settings'])->withInput($request->except('issuer_logo'))
                    ->withErrors(['issuer_logo' => 'This image could not be read. Upload a JPG, PNG, GIF or WebP logo.']);
            }
            $data['issuer_logo'] = $logo;
        } elseif ($removeLogo) {
            $data['issuer_logo'] = '';
        }

        $data['core_coding_all_levels'] = ! empty($data['core_coding_all_levels']) ? '1' : '0';
        $data['issuer_line'] = (string) ($data['issuer_line'] ?? '');
        $this->settings->save($data, (int) Auth::id());

        return redirect()->route('admin.certificates.index', ['tab' => 'settings'])
            ->with('success', 'Certificate settings saved. They apply to certificates issued from now on; issued certificates keep what they show.');
    }

    /** Turns one of the three core certificates on or off. */
    public function toggleDefinition(CertificateDefinition $definition): RedirectResponse
    {
        abort_unless($definition->is_system, 404);

        $active = ! $definition->is_active;
        $definition->forceFill([
            'is_active' => $active,
            'status' => $active ? CertificateDefinition::STATUS_ACTIVE : CertificateDefinition::STATUS_INACTIVE,
        ])->save();

        return redirect()->route('admin.certificates.index', ['tab' => 'core'])->with('success', $active
            ? '"'.$definition->name.'" is active: learners who meet the requirement receive it.'
            : '"'.$definition->name.'" is inactive: no new copies are issued; issued ones stay valid.');
    }

    public function toggleLayout(string $layout): RedirectResponse
    {
        abort_unless(CertificateLayouts::exists($layout), 404);
        $enabled = $this->settings->layoutEnabled($layout);

        if ($enabled) {
            if ($this->settings->get('system_layout') === $layout) {
                return redirect()->route('admin.certificates.index', ['tab' => 'layouts'])
                    ->with('error', 'The core certificates use this layout. Choose another layout for them under Issuer and signatory first.');
            }
            if (count($this->settings->enabledLayouts()) <= 1) {
                return redirect()->route('admin.certificates.index', ['tab' => 'layouts'])
                    ->with('error', 'At least one layout must stay on.');
            }
        }

        $this->settings->setLayoutEnabled($layout, ! $enabled);

        return redirect()->route('admin.certificates.index', ['tab' => 'layouts'])->with('success', $enabled
            ? CertificateLayouts::name($layout).' is turned off. Instructors cannot choose it for new or edited certificates; certificates already issued keep their layout.'
            : CertificateLayouts::name($layout).' is turned on.');
    }

    public function layoutPreview(string $layout): View
    {
        abort_unless(CertificateLayouts::exists($layout), 404);
        $data = [
            'title' => 'Certificate of Completion',
            'learner' => 'Sample Learner',
            'statement' => 'This certifies that Sample Learner has successfully completed Basics of Python Programming.',
            'issuer_name' => $this->settings->get('issuer_name'),
            'issuer_line' => $this->settings->get('issuer_line'),
            'signatory_name' => $this->settings->get('signatory_name'),
            'signatory_title' => $this->settings->get('signatory_title'),
            'logo' => $this->settings->logo(),
            'date' => now()->format('F j, Y'),
            'certificate_id' => 'DS-MOD-'.now()->format('Ymd').'-SAMPLE00',
            'verify_url' => $this->certificates->verifyUrl('DS-MOD-'.now()->format('Ymd').'-SAMPLE00'),
        ];

        return view('admin.certificates.layout', [
            'layout' => $layout,
            'svg' => CertificateHtml::render(CertificateLayouts::compose($layout, $data), CertificateLayouts::name($layout).' sample'),
            'enabled' => $this->settings->layoutEnabled($layout),
        ]);
    }

    public function show(UserCertificate $certificate): View
    {
        $data = $this->certificates->displayData($certificate);
        $certificate->loadMissing(['user:id,name,email', 'definition:id,name,is_system']);

        return view('admin.certificates.show', [
            'certificate' => $certificate,
            'data' => $data,
            'svg' => CertificateHtml::render($this->certificates->layoutItems($certificate), $data['title']),
            'revokedBy' => $certificate->revoked_by ? User::query()->whereKey($certificate->revoked_by)->value('name') : null,
            'replaces' => $certificate->reissued_from_id ? UserCertificate::query()->find($certificate->reissued_from_id) : null,
            'replacedBy' => UserCertificate::query()->where('reissued_from_id', $certificate->id)->first(),
        ]);
    }

    public function revoke(Request $request, UserCertificate $certificate): RedirectResponse
    {
        $reason = $this->reason($request);
        if ($certificate->isRevoked()) {
            return redirect()->route('admin.certificates.show', $certificate)->with('error', 'This certificate is already revoked.');
        }

        /** @var User $admin */
        $admin = Auth::user();
        $this->certificates->revoke($certificate, $admin, $reason);

        return redirect()->route('admin.certificates.show', $certificate)
            ->with('success', 'Certificate '.$certificate->certificate_number.' is revoked. The verification page now shows it as revoked.');
    }

    public function reissue(Request $request, UserCertificate $certificate): RedirectResponse
    {
        $reason = $this->reason($request);

        /** @var User $admin */
        $admin = Auth::user();

        try {
            $new = $this->certificates->reissue($certificate, $admin, $reason);
        } catch (HttpException $exception) {
            return redirect()->route('admin.certificates.show', $certificate)->with('error', $exception->getMessage());
        }

        return redirect()->route('admin.certificates.show', $new)
            ->with('success', 'Reissued as '.$new->certificate_number.'. The previous copy '.$certificate->certificate_number.' is revoked.');
    }

    private function reason(Request $request): string
    {
        return trim((string) $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:400'],
        ], [
            'reason.required' => 'Give the reason; it is kept with the certificate and in the audit log.',
            'reason.min' => 'Give a short reason of at least 5 characters.',
        ])['reason']);
    }
}
