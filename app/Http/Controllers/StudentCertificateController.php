<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserCertificate;
use App\Services\CertificateService;
use App\Support\Certificates\CertificateHtml;
use App\Support\Certificates\CertificatePdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * My Certificates (DataSensei Updates 12 and 13): every certificate the
 * learner holds (the three core certificates and the class certificates of
 * their instructors) with View and Download PDF, and their progress toward
 * the core certificates.
 *
 * Opening the page checks eligibility on the server and issues any
 * certificate the learner has earned. A certificate is only ever shown to its
 * holder: a certificate id that belongs to someone else answers 404. Anyone
 * can confirm a certificate on the public verification page instead.
 */
class StudentCertificateController extends Controller
{
    public function __construct(private readonly CertificateService $certificates)
    {
    }

    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        $this->certificates->syncUser($user);

        $all = UserCertificate::query()
            ->where('user_id', $user->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->get();
        $earned = $all->reject(fn (UserCertificate $c) => $c->isRevoked())->keyBy('certificate_key');

        $progress = [];
        foreach (array_keys(CertificateService::DEFINITIONS) as $key) {
            $held = $all->contains('certificate_key', $key);
            $progress[$key] = $held ? null : $this->certificates->evaluate($user, $key);
        }

        return view('student.certificates.index', [
            'definitions' => $this->certificates->ensureDefinitions(),
            'all' => $all,
            'earned' => $earned,
            'progress' => $progress,
            'rows' => $all->map(fn (UserCertificate $c) => ['certificate' => $c] + $this->certificates->displayData($c)),
            'rules' => collect(array_keys(CertificateService::DEFINITIONS))->mapWithKeys(fn ($key) => [$key => $this->certificates->ruleText($key)])->all(),
        ]);
    }

    public function show(int $certificate): View
    {
        $record = $this->own($certificate);
        $data = $this->certificates->displayData($record);

        return view('student.certificates.show', [
            'certificate' => $record,
            'data' => $data,
            'svg' => CertificateHtml::render($this->certificates->layoutItems($record), $data['title'].' for '.$data['learner']),
            // Kept for views written before Updates 13.
            'snapshot' => $record->snapshotData(),
        ]);
    }

    public function pdf(int $certificate): Response
    {
        $record = $this->own($certificate);
        abort_if($record->isRevoked(), 404);
        $data = $this->certificates->displayData($record);

        return response(CertificatePdf::render($this->certificates->layoutItems($record), $data['title']), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.Str::slug($data['title']).'-'.$record->certificate_number.'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private function own(int $certificate): UserCertificate
    {
        return UserCertificate::query()
            ->whereKey($certificate)
            ->where('user_id', (int) Auth::id())
            ->firstOrFail();
    }
}
