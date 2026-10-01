<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Preview: {{ $item->title }} — DataSensei</title>
<style>
    /* Assignment detail: question preview and submissions. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    a{color:inherit}
    .ds-shell{display:flex;min-height:100vh}
    .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1450px;margin:0 auto}

    /* page header */
    .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top-row > div:first-child{min-width:0;flex:1 1 320px}
    .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.good{border-color:var(--ds-success-border);background:transparent;color:var(--ds-success-text)}
    .btn.good:hover{background:var(--ds-success-soft)}
    .actions{display:flex;flex-wrap:wrap;gap:8px}

    /* messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.5}
    .alert strong{font-weight:600}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .alert.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    .card{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card-pad{padding:20px}

    .grid{display:grid;gap:16px}
    .card h2{font-size:1rem;font-weight:600;line-height:1.35}
    .muted{color:var(--muted)}
    .dim{color:var(--dim)}

    /* details: plain label and value pairs */
    .facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px 24px;margin:14px 0 0}
    .facts dt{color:var(--muted);font-size:.8125rem;font-weight:500}
    .facts dd{margin:2px 0 0;color:var(--text);font-size:.9375rem;font-weight:500;overflow-wrap:anywhere}
    .instructions{margin-top:10px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.65;white-space:pre-wrap;overflow-wrap:anywhere}
    .section-note{margin-top:4px;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .state-good{color:var(--ds-success-text)}
    .state-bad{color:var(--ds-danger-text)}

    /* submissions table */
    .table{width:100%;min-width:380px;border-collapse:collapse}
    .table th{padding:10px 12px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .table td{padding:12px 12px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
      line-height:1.45;vertical-align:middle}
    .table tbody tr:last-child td{border-bottom:0}
    .table tbody tr:hover td{background:rgba(255,255,255,.02)}
    .table strong{color:var(--text);font-weight:600}
    .table td .dim{font-size:.8125rem;overflow-wrap:anywhere}
    .card .ds-table-scroll{border:1px solid var(--border);border-radius:var(--radius-sm)}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1100px){
      .facts{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:900px){
      .ds-main{padding:24px 20px 40px}
    }
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .card-pad{padding:16px}
      .top-row{align-items:stretch;flex-direction:column}
      .top-row > div:first-child{flex:0 0 auto}
      .top-row .actions > *{flex:1 1 auto}
      .top-row .actions form .btn{width:100%}
    }
    @media(max-width:420px){.facts{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'Assignment Preview', 'pageDescription' => 'The full content of an assignment before you give it to a class.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $item->title }}</h1>
            <p class="page-subtitle">Preview. This is what the assignment contains, with the answers. Nothing has been given to a class yet.</p>
          </div>
          <div class="actions">
            <a href="{{ route('instructor.assignments.create', ['item' => $item->id]) }}" class="btn primary">Use this assignment</a>
            <a href="{{ route('instructor.assignments.create') }}" class="btn secondary">Back</a>
          </div>
        </div>

        <div class="grid">
          <section class="card card-pad" aria-labelledby="details-title">
            <h2 id="details-title">Assignment details</h2>
            <dl class="facts">
              <div><dt>Topic</dt><dd>{{ $item->topic_title ?: $item->title }}</dd></div>
              <div><dt>Type</dt><dd>{{ $item->type_label === 'MCQ' ? 'Multiple choice' : $item->type_label }}</dd></div>
              <div><dt>Questions</dt><dd>{{ $item->questions->count() }}</dd></div>
              <div><dt>Total points</dt><dd>{{ (int) ($item->questions->sum('points') ?: $item->total_points) }}</dd></div>
              <div><dt>Time limit</dt><dd>{{ $item->time_limit_minutes ? $item->time_limit_minutes.' minutes' : 'None' }}</dd></div>
              <div><dt>Year level</dt><dd>{{ $item->year_level ?: 'Any' }}</dd></div>
            </dl>
          </section>

          <section class="card card-pad" aria-labelledby="instructions-title">
            <h2 id="instructions-title">Instructions</h2>
            <p class="instructions">{{ trim((string) $item->instructions) !== '' ? $item->instructions : 'No instructions. You can add class-specific instructions when you give it to a class.' }}</p>
            @if(filled($item->description))
              <p class="section-note" style="margin-top:10px">{{ $item->description }}</p>
            @endif
          </section>

          <section class="card card-pad" aria-labelledby="content-title">
            <h2 id="content-title">Questions and answers</h2>
            <p class="section-note">Correct and expected answers are shown to you only.</p>
            @include('instructor.assignments._content', ['item' => $item])
          </section>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
