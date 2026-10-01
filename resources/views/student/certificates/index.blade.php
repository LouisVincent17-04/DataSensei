<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificates — DataSensei</title>
  @include('student.assessments._list_styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'My Certificates', 'pageDescription' => 'Your certificates, with View and Download PDF, and your progress toward the DataSensei core certificates.'])
  <style>
    /* Certificates (DataSensei Updates 12). Plain text states, no badges. */
    .ct-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 16px; }
    .ct-card { display: grid; gap: 10px; align-content: start; padding: 16px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface); }
    .ct-card h2 { color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; }
    .ct-card p { color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
    .ct-meter { height: 8px; border-radius: 4px; background: var(--surface3); overflow: hidden; }
    .ct-meter span { display: block; height: 100%; border-radius: 4px; background: var(--accent); }
    .ct-meter span.done { background: var(--ds-success); }
    .ct-count { color: var(--text); font-size: .875rem; font-variant-numeric: tabular-nums; }
    .ct-items { margin: 0; padding: 0; list-style: none; border-top: 1px solid var(--border); }
    .ct-items li { display: flex; justify-content: space-between; gap: 12px; padding: 7px 0; border-bottom: 1px solid var(--border); color: var(--ds-text-secondary); font-size: .8125rem; }
    .ct-items li:last-child { border-bottom: 0; }
    details summary { cursor: pointer; color: var(--ds-accent-text); font-size: .8125rem; }
    .ct-h2 { margin: 8px 0 12px; color: var(--text); font-size: 1rem; font-weight: 600; }
    .ct-mono { font-family: var(--ds-font-mono); font-size: .8125rem; }
  </style>
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <span class="mobile-title">My Certificates</span>
  </header>
  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">My Certificates</h1>
            <p class="page-subtitle">The DataSensei core certificates are checked on the server from the progress you have saved and issued as soon as you qualify. A class Certificate of Completion is issued by your instructor at the end of the semester when you have completed all the class's required work. Each has a unique {!! \App\Support\Glossary::help('certificate_id', 'certificate ID') !!} that anyone can confirm on the <a class="crumb" style="display:inline;margin:0" href="{{ route('certificates.verify.form') }}">verification page</a>. Badges for smaller wins stay under Achievements.</p>
          </div>
        </div>

        <section class="card" aria-labelledby="ct-mine">
          <div class="card-head"><h2 id="ct-mine">Earned certificates ({{ $earned->count() }})</h2><span>Your own certificates only</span></div>
          @if($rows->isEmpty())
            <p class="card-empty">You have not earned a certificate yet. Complete the core curriculum below, or all the required work of your classes; your Gradebook shows how much of each class you have completed.</p>
          @else
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">Certificate</th>
                  <th scope="col">Course or milestone</th>
                  <th scope="col">Issued</th>
                  <th scope="col">Certificate ID</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  @php $c = $row['certificate']; @endphp
                  <tr>
                    <td>
                      <strong>{{ $row['title'] }}</strong>
                      <span class="sub">{{ $row['kind'] === 'class' ? 'From '.$row['signatory_name'].($row['class_name'] ? ', '.$row['class_name'] : '') : 'DataSensei core certificate' }}</span>
                      @if($c->isRevoked())<span class="sub state-bad">Revoked{{ $c->revoked_at ? ' on '.$c->revoked_at->format('M d, Y') : '' }}</span>@endif
                    </td>
                    <td data-label="Course or milestone">{{ $row['module'] }}</td>
                    <td data-label="Issued">{{ $c->issued_at?->format('M d, Y') }}</td>
                    <td data-label="Certificate ID"><span class="ct-mono">{{ $c->certificate_number }}</span></td>
                    <td class="action">
                      <a class="btn" href="{{ route('student.certificates.show', $c->id) }}">View</a>
                      @unless($c->isRevoked())
                        <a class="btn" href="{{ route('student.certificates.pdf', $c->id) }}">Download PDF</a>
                      @endunless
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </section>

        <h2 class="ct-h2">DataSensei core certificates</h2>
        @if($definitions->isEmpty())
          <section class="card"><p class="card-empty">Certificates are not set up on this server yet.</p></section>
        @else
          <div class="ct-grid">
            @foreach($definitions as $key => $definition)
              @php
                $certificate = $earned->get($key);
                $state = $progress[$key] ?? null;
                $percent = $state && $state['total'] > 0 ? round($state['done'] / $state['total'] * 100) : 0;
              @endphp
              <section class="ct-card" aria-labelledby="ct-{{ $key }}">
                <h2 id="ct-{{ $key }}">{{ $definition->name }}</h2>
                <p>{{ $definition->description }}</p>

                @if($certificate)
                  <div class="ct-meter" aria-hidden="true"><span class="done" style="width:100%"></span></div>
                  <p><span class="state-good">Earned</span> on {{ $certificate->issued_at?->format('M d, Y') }}. Certificate ID {{ $certificate->certificate_number }}.</p>
                  <div><a class="btn primary" href="{{ route('student.certificates.show', $certificate->id) }}">View certificate</a></div>
                @elseif($all->contains('certificate_key', $key))
                  <p class="state-warn">This certificate was revoked by an administrator.</p>
                @elseif(! $definition->issues())
                  <p class="state-warn">Not being issued at the moment.</p>
                @elseif($state === null || ! $state['issuable'])
                  <p class="state-warn">Not available yet: the core content this certificate needs is not complete on this server.</p>
                @else
                  <div class="ct-meter" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $state['total'] }}" aria-valuenow="{{ $state['done'] }}" aria-label="{{ $definition->name }} progress"><span style="width:{{ $percent }}%"></span></div>
                  <p class="ct-count">{{ $state['done'] }} of {{ $state['total'] }} done</p>
                  <p>{{ $rules[$key] }}</p>
                  <details>
                    <summary>Show each requirement</summary>
                    <ul class="ct-items">
                      @foreach($state['items'] as $item)
                        <li><span>{{ $item['title'] }}</span><span class="{{ $item['done'] ? 'state-good' : '' }}">{{ $item['done'] ? 'Done' : 'Not yet' }}</span></li>
                      @endforeach
                    </ul>
                  </details>
                @endif
              </section>
            @endforeach
          </div>
        @endif
      </div>
    </main>
  </div>

  @include('student.assessments._menu_script')
</body>
</html>
