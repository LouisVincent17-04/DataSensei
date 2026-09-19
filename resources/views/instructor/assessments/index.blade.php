<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Assessments — DataSensei</title>
<style>
    /* Assessment list. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1480px;margin:0 auto}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}

    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.secondary{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.5}

    /* assessment cards */
    .grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
    .card{display:flex;flex-direction:column;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card h3{margin:12px 0 4px;font-size:.9375rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .card > p.muted{margin:0;font-size:.8125rem;line-height:1.5}
    .card-actions{margin-top:auto;padding-top:16px}
    .actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}
    .between{justify-content:space-between}
    .muted{color:var(--muted)}
    .small{font-size:.8125rem;font-variant-numeric:tabular-nums}

    /* status badge; the status arrives upper-case, shown in sentence case */
    .badge{display:inline-block;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;
      text-transform:lowercase}
    .badge::first-letter{text-transform:capitalize}
    .badge.draft{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .badge.published{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}

    .draft-note{margin-top:12px;padding:10px 12px;border:1px solid var(--ds-warning-border);border-radius:var(--radius-sm);
      background:var(--ds-warning-soft);color:#fef3c7;font-size:.8125rem;line-height:1.5}
    .draft-note strong{font-weight:600}

    /* empty state spans the grid */
    .grid > div.card{grid-column:1/-1;padding:32px 20px;text-align:center}
    .grid > div.card p{margin:0;font-size:.875rem;line-height:1.5}

    .pagination{margin-top:16px}

    @media(max-width:1100px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:700px){.grid{grid-template-columns:minmax(0,1fr)}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .top{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .top > .btn{width:100%}
    }
    @media(prefers-reduced-motion:reduce){.btn{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Assessments', 'pageDescription' => 'Create, publish, and grade assessments for your classes.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Assessments</h1>
          <p class="subtitle">Create TOS-guided assessments, save unfinished quizzes as drafts, and resume from the last item at any time.</p>
        </div>
        <a class="btn secondary" href="{{ route('instructor.tos.index') }}">Choose a TOS</a>
      </div>

      @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

      <div class="grid">
        @forelse($assessments as $assessment)
          <article class="card">
            <div class="actions between">
              <span class="badge {{ $assessment->status }}">{{ strtoupper($assessment->status) }}</span>
              <span class="muted small">{{ $assessment->questions_count }} items</span>
            </div>
            <h3>{{ $assessment->title }}</h3>
            <p class="muted">{{ $assessment->classRoom->name ?? 'No class' }}, {{ $assessment->total_points }} points, {{ $assessment->submissions_count }} submissions</p>

            @if($assessment->status === 'draft')
              <div class="draft-note">
                <strong>Saved draft</strong><br>
                @if($assessment->draft_saved_at)
                  Last saved {{ $assessment->draft_saved_at->diffForHumans() }}
                  @if($assessment->draft_last_item)<br>Resume at item {{ $assessment->draft_last_item }} @endif
                @else
                  Ready to continue authoring.
                @endif
              </div>
            @endif

            <div class="actions card-actions">
              <a class="btn" href="{{ route('instructor.assessments.builder', ['assessment' => $assessment, 'item' => $assessment->status === 'draft' ? $assessment->draft_last_item : null]) }}">
                {{ $assessment->status === 'draft' ? 'Continue Draft' : 'View Builder' }}
              </a>
              <a class="btn secondary" href="{{ route('instructor.assessments.analytics', $assessment) }}">Diagnostics</a>
            </div>
          </article>
        @empty
          <div class="card">
            <p class="muted">No assessments yet. Open a TOS and click Create Assessment Form.</p>
          </div>
        @endforelse
      </div>
      <div class="pagination">{{ $assessments->links() }}</div>
    </div>
  </main>
</div>
</body>
</html>
