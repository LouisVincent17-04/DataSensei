<?php

namespace App\Http\Controllers;

use App\Models\UserCertificate;
use App\Services\CertificateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public certificate verification (DataSensei Updates 13).
 *
 * Anyone with a certificate ID can confirm it is genuine. The page shows only
 * what verification needs: whether it is valid or revoked, the certificate,
 * the holder's name, the course or milestone, the issuer and the issue date.
 * Never grades, e-mail addresses, internal user ids or class details. The
 * route is rate limited so IDs cannot be guessed in bulk, and an unknown ID
 * gets the same "not found" answer whatever it looks like.
 */
class CertificateVerificationController extends Controller
{
    public function __construct(private readonly CertificateService $certificates)
    {
    }

    public function form(Request $request): View|RedirectResponse
    {
        $id = trim((string) $request->query('id', ''));
        if ($id !== '') {
            return redirect()->route('certificates.verify.show', ['number' => strtoupper(substr($id, 0, 60))]);
        }

        return view('certificates.verify', ['number' => '', 'result' => null, 'searched' => false]);
    }

    public function show(string $number): View
    {
        $certificate = $this->certificates->findByNumber($number);

        return view('certificates.verify', [
            'number' => strtoupper(substr($number, 0, 60)),
            'result' => $certificate ? $this->publicData($certificate) : null,
            'searched' => true,
        ]);
    }

    /** @return array<string, string|null> the minimum needed to verify */
    private function publicData(UserCertificate $certificate): array
    {
        $data = $this->certificates->displayData($certificate);

        return [
            'status' => $certificate->isRevoked() ? 'revoked' : 'valid',
            'certificate_id' => (string) $certificate->certificate_number,
            'title' => $data['title'],
            'holder' => $data['learner'],
            'module' => $data['module'],
            'issuer' => $data['issuer_name'],
            'issued' => $data['date'],
            'revoked' => $certificate->revoked_at?->format('F j, Y'),
        ];
    }
}
