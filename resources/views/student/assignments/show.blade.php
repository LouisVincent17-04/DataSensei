<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Overview — DataSensei</title>
<style>
    /* Student assignment overview. Colours, type and radius come from partials.design-system. */
    :root{--accent2:var(--ds-accent);--accent3:var(--ds-success);--warn:var(--ds-danger);--warn2:var(--ds-warning)}
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{min-height:100%;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    a{color:inherit}
    .ds-shell{display:flex;min-height:100vh}
    .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1200px;margin:0 auto}

    /* page header */
    .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top-row > div:first-child{min-width:0;flex:1 1 320px}
    .top-row .ds-page-title{overflow-wrap:anywhere}
    .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55;overflow-wrap:anywhere}

    .card{border:1px solid var(--border);background:var(--surface);border-radius:var(--radius);overflow:hidden}
    .card-pad{padding:20px}
    .card h2{color:var(--text);font-size:1rem;font-weight:600;line-height:1.35}
    .muted{color:var(--muted)}
    .dim{color:var(--muted)}

    .btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-family:var(--ds-font-sans);font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff}
    .btn.primary:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .actions form{display:contents}

    /* status labels */
    .badge-pill{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;font-variant-numeric:tabular-nums}
    .badge-pill.good{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .badge-pill.warn{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .badge-pill.danger{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.55}
    .alert.success{background:var(--ds-success-soft);border-color:var(--ds-success-border);color:#d1fae5}
    .alert.danger{background:var(--ds-danger-soft);border-color:var(--ds-danger-border);color:#fee2e2}

    /* summary figures */
    .grid{display:grid;gap:12px}
    .stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:20px}
    .stat{min-width:0;display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:14px 16px;background:var(--surface3);border-radius:var(--radius-sm)}
    .stat .num{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500}
    .card-pad > p{font-size:.875rem;overflow-wrap:anywhere}
    .card-pad > p strong{color:var(--text);font-weight:600}

    @media(max-width:900px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}

    @media(max-width:900px){.ds-main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .card-pad{padding:16px}
      .top-row > .btn{width:100%}
      .actions > .btn,.actions > form > .btn{flex:1 1 auto}
    }
    @media(prefers-reduced-motion:reduce){.btn,.input,.option-row{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Assignment Overview', 'pageDescription' => 'See the assignments your instructor set, submit work, and review feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">{{ $assignment->classRoom->name }}, {{ $assignment->libraryItem->topic_title }}, {{ $assignment->libraryItem->type_label }}</p>
          </div>
          <a href="{{ route('student.assignments.index') }}" class="btn secondary">Back</a>
        </div>

        <section class="card card-pad">
          <div class="grid stats">
            <div class="stat"><div class="num">{{ $assignment->libraryItem->questions->count() }}</div><div class="label">Questions</div></div>
            <div class="stat"><div class="num">{{ $assignment->libraryItem->total_points }}</div><div class="label">Points</div></div>
            <div class="stat"><div class="num">{{ $assignment->max_attempts }}</div><div class="label">Max Attempts</div></div>
            <div class="stat"><div class="num">{{ $assignment->libraryItem->time_limit_minutes }}</div><div class="label">Minutes</div></div>
          </div>

          <h2 style="margin-bottom:8px">Instructions</h2>
          <p class="muted" style="line-height:1.7;margin-bottom:16px">{{ $assignment->instructions ?: $assignment->libraryItem->instructions ?: 'Answer all items before submitting.' }}</p>
          <p class="muted" style="margin-bottom:16px">Due: <strong>{{ $assignment->due_at ? $assignment->due_at->format('M d, Y h:i A') : 'No due date' }}</strong></p>

          <div class="actions">
            @if($latestSubmission && $latestSubmission->status === 'in_progress')
              <a class="btn primary" href="{{ route('student.assignments.take', [$assignment, $latestSubmission]) }}">Continue Attempt</a>
            @elseif($latestSubmission && $latestSubmission->status !== 'in_progress')
              <a class="btn secondary" href="{{ route('student.assignments.result', [$assignment, $latestSubmission]) }}">View Result</a>
              <form method="POST" action="{{ route('student.assignments.start', $assignment) }}">@csrf<button class="btn primary" type="submit">Retake</button></form>
            @else
              <form method="POST" action="{{ route('student.assignments.start', $assignment) }}">@csrf<button class="btn primary" type="submit">Start Assignment</button></form>
            @endif
          </div>
        </section>
      </div>

    </main>
  </div>
</body>
</html>
