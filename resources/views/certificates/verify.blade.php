<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex">
  <title>Verify a certificate — DataSensei</title>
  @include('partials.page-head', ['pageTitle' => 'Verify a certificate', 'pageDescription' => 'Confirm that a DataSensei certificate is genuine using its certificate ID.'])
  @include('certificates._styles')
  <style>
    body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .vf-wrap { max-width: 720px; margin: 0 auto; padding: 40px 20px 56px; }
    .vf-brand { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 28px; }
    .vf-brand a { color: var(--text); font-weight: 700; text-decoration: none; }
    .vf-title { margin: 0 0 6px; font-size: 1.375rem; font-weight: 700; }
    .vf-sub { margin: 0 0 20px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .vf-form { display: flex; gap: 8px; margin-bottom: 20px; }
    .vf-form .ct-input { flex: 1 1 auto; font-family: var(--ds-font-mono); text-transform: uppercase; }
    .vf-result { padding: 18px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
    .vf-result.is-valid { border-color: var(--ds-success-border); }
    .vf-result.is-revoked, .vf-result.is-missing { border-color: var(--ds-danger-border); }
    .vf-status { margin: 0 0 14px; font-size: 1rem; font-weight: 600; }
    .vf-note { margin: 16px 0 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }
    @media (max-width: 560px) { .vf-form { flex-direction: column; } .ct-dl { grid-template-columns: minmax(0, 1fr); } }
  </style>
</head>
<body>
  <main class="vf-wrap">
    <div class="vf-brand">
      <a href="{{ url('/') }}">DataSensei</a>
      @include('partials.theme-toggle')
    </div>

    <h1 class="vf-title">Verify a certificate</h1>
    <p class="vf-sub">Enter the {!! \App\Support\Glossary::help('certificate_id', 'certificate ID') !!} printed on the certificate, for example DS-MOD-20261001-7KQ2M9XA. Only the details needed to confirm it are shown.</p>

    <form method="GET" action="{{ route('certificates.verify.form') }}" class="vf-form" role="search">
      <label class="sr-only" for="vf-id" style="position:absolute;left:-9999px">Certificate ID</label>
      <input id="vf-id" class="ct-input" name="id" value="{{ $number }}" maxlength="60" autocomplete="off" spellcheck="false" placeholder="Certificate ID" required>
      <button class="ct-btn" type="submit">Verify</button>
    </form>

    @if($searched)
      @if($result === null)
        <section class="vf-result is-missing" aria-live="polite">
          <p class="vf-status ct-bad">No certificate has this ID.</p>
          <p class="vf-note">Check the ID for typing mistakes. Certificate IDs are not case sensitive.</p>
        </section>
      @else
        <section class="vf-result {{ $result['status'] === 'valid' ? 'is-valid' : 'is-revoked' }}" aria-live="polite">
          @if($result['status'] === 'valid')
            <p class="vf-status ct-good">Valid certificate</p>
          @else
            <p class="vf-status ct-bad">This certificate was revoked{{ $result['revoked'] ? ' on '.$result['revoked'] : '' }} and is no longer valid.</p>
          @endif
          <dl class="ct-dl">
            <dt>Certificate</dt><dd>{{ $result['title'] }}</dd>
            <dt>Awarded to</dt><dd>{{ $result['holder'] }}</dd>
            <dt>Course or milestone</dt><dd>{{ $result['module'] }}</dd>
            <dt>Issued by</dt><dd>{{ $result['issuer'] }}</dd>
            <dt>Issue date</dt><dd>{{ $result['issued'] }}</dd>
            <dt>Certificate ID</dt><dd><code>{{ $result['certificate_id'] }}</code></dd>
          </dl>
          <p class="vf-note">DataSensei keeps grades, contact details and class information private; they are not part of verification.</p>
        </section>
      @endif
    @endif
  </main>
</body>
</html>
