{{-- Failed reference-solution cases from the server-side check (ReferenceSolutionVerifier). --}}
@php
  $referenceFailures = session(\App\Services\ReferenceSolutionVerifier::SESSION_KEY, []);
  $clip = fn ($text) => \Illuminate\Support\Str::limit((string) $text, 2000);
@endphp
@if(! empty($referenceFailures))
  <style>
    .reference-check{margin:0 0 16px;padding:16px 18px;border:1px solid var(--ds-danger-border, #7f1d1d);border-radius:12px;background:var(--ds-danger-soft, rgba(239,68,68,.08));color:var(--ds-text, #f8fafc)}
    .reference-check h2{margin:0 0 4px;font-size:.9375rem;font-weight:600}
    .reference-check > p{margin:0 0 12px;color:var(--ds-text-secondary, #cbd5e1);font-size:.875rem;line-height:1.5}
    .reference-check-case{padding:12px 0;border-top:1px solid var(--ds-danger-border, #7f1d1d)}
    .reference-check-case strong{display:block;margin-bottom:8px;font-size:.875rem}
    .reference-check-case dl{display:grid;grid-template-columns:140px minmax(0,1fr);gap:6px 12px;margin:0}
    .reference-check-case dt{color:var(--ds-text-muted, #94a3b8);font-size:.8125rem}
    .reference-check-case dd{margin:0;min-width:0}
    .reference-check-case pre{margin:0;padding:6px 8px;border-radius:6px;background:var(--surface3, rgba(0,0,0,.25));font-family:var(--ds-font-mono, ui-monospace, monospace);font-size:.8125rem;white-space:pre-wrap;word-break:break-word}
    .reference-check-case p{margin:0;font-size:.875rem}
    @media (max-width:640px){.reference-check-case dl{grid-template-columns:minmax(0,1fr)}}
  </style>
  <section class="reference-check" role="alert" aria-labelledby="reference-check-title">
    <h2 id="reference-check-title">The reference solution did not pass every test case</h2>
    <p>Nothing was saved. Each problem's reference solution was run against its test cases with the same Python checker that grades students. Fix the solution or the expected output below, then save again.</p>
    @foreach($referenceFailures as $failure)
      <div class="reference-check-case">
        <strong>
          {{ $failure['problem_title'] }}@if($failure['case']), test case {{ $failure['case'] }}{{ $failure['is_hidden'] ? ' (hidden)' : '' }}@endif
        </strong>
        @if($failure['case'])
          <dl>
            <dt>Input</dt>
            <dd><pre>{{ $failure['input'] === '' ? '(no input)' : $clip($failure['input']) }}</pre></dd>
            <dt>Expected output</dt>
            <dd><pre>{{ $failure['expected'] === '' ? '(empty)' : $clip($failure['expected']) }}</pre></dd>
            <dt>Actual output</dt>
            <dd><pre>{{ $failure['actual'] === '' ? '(nothing printed)' : $clip($failure['actual']) }}</pre></dd>
            @if($failure['error'] !== '')
              <dt>Error</dt>
              <dd><pre>{{ $clip($failure['error']) }}</pre></dd>
            @endif
          </dl>
        @else
          <p>{{ $failure['error'] }}</p>
        @endif
      </div>
    @endforeach
  </section>
@endif
