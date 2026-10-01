<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $data['title'] }} — DataSensei</title>
  @include('student.assessments._list_styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => $data['title'], 'pageDescription' => 'A DataSensei certificate.'])
  @include('certificates._styles')
  <style>
    .cert-wrap { max-width: 980px; }
    .cert-actions { display: flex; flex-wrap: wrap; gap: 8px; margin: 14px 0 18px; }
    .cert-meta { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px 18px; margin: 0 0 18px; padding: 0; list-style: none; font-size: .875rem; }
    .cert-meta span { display: block; color: var(--muted); font-size: .75rem; }
    .cert-meta strong { color: var(--text); font-weight: 500; overflow-wrap: anywhere; }
  </style>
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <span class="mobile-title">Certificate</span>
  </header>
  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap cert-wrap">
        <a class="crumb" href="{{ route('student.certificates.index') }}">&larr; My Certificates</a>
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $data['title'] }}</h1>
            <p class="page-subtitle">{{ $data['module'] }}</p>
          </div>
        </div>

        @if($certificate->isRevoked())
          <div class="alert danger" role="alert">This certificate was revoked{{ $certificate->revoked_at ? ' on '.$certificate->revoked_at->format('F j, Y') : '' }} and is no longer valid. The verification page shows it as revoked.</div>
        @endif

        <ul class="cert-meta">
          <li><span>{!! \App\Support\Glossary::help('certificate_id') !!}</span><strong>{{ $certificate->certificate_number }}</strong></li>
          <li><span>Issued</span><strong>{{ $data['date'] }}</strong></li>
          <li><span>Issuer</span><strong>{{ $data['issuer_name'] }}</strong></li>
          <li><span>Status</span><strong class="{{ $certificate->isRevoked() ? 'state-bad' : 'state-good' }}">{{ $certificate->isRevoked() ? 'Revoked' : 'Valid' }}</strong></li>
        </ul>

        <div class="ct-frame ct-printable">{{ $svg }}</div>

        <div class="cert-actions">
          @unless($certificate->isRevoked())
            <a class="btn primary" href="{{ route('student.certificates.pdf', $certificate->id) }}">Download PDF</a>
            <button class="btn" type="button" onclick="window.print()">Print</button>
          @endunless
          <a class="btn" href="{{ route('certificates.verify.show', ['number' => $certificate->certificate_number]) }}" target="_blank" rel="noopener">Open the verification page</a>
        </div>

        @if(! empty($data['requirements']))
          <section class="card" aria-labelledby="cert-req">
            <div class="card-head"><h2 id="cert-req">What this certificate was issued for</h2><span>{{ $data['rule'] }}</span></div>
            <table class="table">
              <thead><tr><th scope="col">#</th><th scope="col">Requirement</th></tr></thead>
              <tbody>
                @foreach($data['requirements'] as $index => $item)
                  <tr><td data-label="#">{{ $index + 1 }}</td><td data-label="Requirement">{{ $item['title'] ?? '' }}</td></tr>
                @endforeach
              </tbody>
            </table>
          </section>
        @endif
      </div>
    </main>
  </div>

  @include('student.assessments._menu_script')
</body>
</html>
