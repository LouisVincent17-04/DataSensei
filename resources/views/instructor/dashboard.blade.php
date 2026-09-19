<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Instructor Dashboard — DataSensei</title>
  <style>
    /* Instructor dashboard layout. Colours, type and radius come from
       partials.design-system. */
    :root{--green:var(--ds-success);--amber:var(--ds-warning);--red:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;display:flex;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .main{flex:1;min-width:0}

    /* ── title bar ─────────────────────────────────────────────── */
    .topbar{min-height:60px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px 16px;
      background:var(--bg);border-bottom:1px solid var(--border)}
    .topbar .link{color:var(--muted);font-size:.875rem;font-weight:500;overflow-wrap:anywhere}
    .topbar .link:hover{color:var(--text);text-decoration:none}

    .content{padding:28px 32px 48px;display:grid;grid-template-columns:minmax(0,1fr);gap:24px}
    .link{color:var(--ds-accent-text);text-decoration:none;font-size:.8125rem;font-weight:500;white-space:nowrap}
    .link:hover{color:var(--text);text-decoration:underline}
    .muted{color:var(--muted)}

    /* ── lead-in ───────────────────────────────────────────────── */
    .welcome{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px 24px}
    .welcome > div:first-child{min-width:0;flex:1 1 320px}
    .welcome h2{margin:0;font-size:1.125rem;font-weight:600;line-height:1.35;letter-spacing:-.01em}
    .welcome p{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.5}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}

    /* ── summary figures ───────────────────────────────────────── */
    .stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
    .stat{padding:16px 18px;display:flex;flex-direction:column;gap:4px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500;line-height:1.4}
    .stat strong{display:block;margin-top:2px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat small{display:block;color:var(--muted);font-size:.75rem;line-height:1.4}

    /* ── two columns ───────────────────────────────────────────── */
    .grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,34%);gap:20px;align-items:start}
    .stack{display:grid;grid-template-columns:minmax(0,1fr);gap:20px;min-width:0}
    .card{min-width:0;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);overflow:hidden}
    .card-head{padding:14px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px 16px;border-bottom:1px solid var(--border)}
    .card-head h3{margin:0;font-size:.9375rem;font-weight:600;line-height:1.35}
    .card-body{padding:16px 20px}
    .card-body.actions{padding:16px 20px}

    /* table */
    .card-body.table-wrap{padding:0;overflow-x:auto}
    table{width:100%;min-width:640px;border-collapse:collapse;font-variant-numeric:tabular-nums}
    th{padding:10px 14px;background:var(--surface3);border-bottom:1px solid var(--border);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;vertical-align:middle}
    td strong{color:var(--text);font-weight:600}
    th:first-child,td:first-child{padding-left:20px}
    th:last-child,td:last-child{padding-right:20px;text-align:right}
    tbody tr:last-child td{border-bottom:0}
    tbody tr:hover td{background:rgba(255,255,255,.02)}

    /* list rows separated by rules */
    .work-list,.submission-list,.risk-list{padding:0;display:grid}
    .item{padding:12px 20px;display:flex;justify-content:space-between;align-items:center;gap:16px;
      border-top:1px solid var(--border);color:inherit;text-decoration:none}
    .card-body > .item:first-child{border-top:0}
    .item > div:first-child{min-width:0;flex:1 1 auto}
    a.item{transition:background .12s ease}
    a.item:hover{background:var(--surface2)}
    .item-title{font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .meta{margin-top:2px;color:var(--muted);font-size:.8125rem;line-height:1.45}
    td .meta{font-size:.75rem}

    /* status labels */
    .pill{flex-shrink:0;display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-xs);background:var(--surface2);color:var(--ds-text-secondary);
      font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .pill.status{text-transform:capitalize}
    .pill.green{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .pill.amber{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .pill.red{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}

    .empty{padding:28px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1100px){
      .grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:900px){
      .topbar{min-height:56px;padding:0 20px}
      .content{padding:24px 20px 40px}
      .stats{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:640px){
      .content{padding:20px 16px 32px}
      .welcome{align-items:flex-start;flex-direction:column}
      .welcome > div:first-child{flex-basis:auto}
      .welcome .actions{width:100%}
      .welcome .actions .btn{flex:1 1 auto}
      .topbar{padding:10px 16px}
      .card-head{padding:12px 16px}
      .card-body,.card-body.actions{padding:16px}
      .card-body.work-list,.card-body.submission-list,.card-body.risk-list,.card-body.table-wrap{padding:0}
      .item{padding:12px 16px}
      .stat{padding:14px 16px}
      th:first-child,td:first-child{padding-left:16px}
      th:last-child,td:last-child{padding-right:16px}
    }
    @media(max-width:480px){
      .item{align-items:flex-start;flex-direction:column;gap:8px}
    }
    @media(max-width:420px){.stats{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,a.item{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Instructor Dashboard', 'pageDescription' => 'Your teaching dashboard: classes, submissions, and alerts.'])
</head>
<body>
  @include('partials.instructor-sidebar')
  <div class="main">
    <header class="topbar"><h1 class="ds-page-title">Instructor Dashboard</h1><a class="link" href="{{ route('profile') }}">{{ $instructor->name }}</a></header>
    <main class="content">
      <section class="welcome">
        <div><h2>Welcome back, {{ $instructor->name }}.</h2><p>Review live class, coursework, mastery, and risk data from the classes assigned to you.</p></div>
        <div class="actions"><a class="btn primary" href="{{ route('instructor.classes.create') }}">Create class</a><a class="btn" href="{{ route('instructor.assignments.create') }}">Create assignment</a><a class="btn" href="{{ route('instructor.analytics.index') }}">Open analytics</a><a class="btn" href="{{ route('instructor.competencies.index') }}">Competency matrix</a></div>
      </section>

      <section class="stats" aria-label="Instructor summary">
        <div class="stat"><span class="label">Active classes</span><strong>{{ $stats['active_classes'] }}</strong><small>owned by you</small></div>
        <div class="stat"><span class="label">Unique learners</span><strong>{{ $stats['total_students'] }}</strong><small>across active classes</small></div>
        <div class="stat"><span class="label">Published work</span><strong>{{ $stats['published_work'] }}</strong><small>assignments and assessments</small></div>
        <div class="stat"><span class="label">Average score</span><strong>{{ $stats['average_score'] }}%</strong><small>latest performance snapshots</small></div>
        <div class="stat"><span class="label">ILO mastery</span><strong>{{ $stats['mastery_rate'] }}%</strong><small>mastered evidence rows</small></div>
        <div class="stat"><span class="label">High risk</span><strong>{{ $stats['at_risk'] }}</strong><small>unique flagged learners</small></div>
      </section>

      <section class="grid">
        <div class="stack">
          <article class="card">
            <div class="card-head"><h3>Class performance</h3><a class="link" href="{{ route('instructor.analytics.index') }}">Detailed analytics →</a></div>
            <div class="card-body table-wrap">
              @if ($classMetrics->isEmpty())
                <div class="empty">No active classes. Create a class to begin.</div>
              @else
                <table>
                  <thead><tr><th>Class</th><th>Learners</th><th>Avg. score</th><th>Engagement</th><th>High risk</th><th></th></tr></thead>
                  <tbody>
                    @foreach ($classMetrics as $metric)
                      <tr>
                        <td><strong>{{ $metric['class']->name }}</strong><div class="meta">{{ $metric['class']->section ?: 'No section' }}</div></td>
                        <td>{{ $metric['class']->students_count }}</td>
                        <td>{{ $metric['average_score'] === null ? '—' : $metric['average_score'].'%' }}</td>
                        <td>{{ $metric['engagement'] === null ? '—' : $metric['engagement'].'%' }}</td>
                        <td>{{ $metric['at_risk'] }}</td>
                        <td><a class="link" href="{{ route('instructor.classes.show', $metric['class']) }}">Open</a></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @endif
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Recently updated work</h3><a class="link" href="{{ route('instructor.assignments.index') }}">Manage work →</a></div>
            <div class="card-body work-list">
              @forelse ($recentWork as $work)
                <a class="item" href="{{ $work['url'] }}"><div><div class="item-title">{{ $work['title'] }}</div><div class="meta">{{ $work['type'] }}{{ $work['class_name'] ? ', '.$work['class_name'] : '' }}, {{ $work['at']->diffForHumans() }}</div></div><span class="pill status {{ $work['status'] === 'published' ? 'green' : '' }}">{{ $work['status'] }}</span></a>
              @empty
                <div class="empty">No assignments or assessments have been created.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Recent submissions</h3><a class="link" href="{{ route('instructor.submissions.index') }}">All submissions →</a></div>
            <div class="card-body submission-list">
              @forelse ($recentSubmissions as $submission)
                <div class="item"><div><div class="item-title">{{ $submission['student'] }}, {{ $submission['title'] }}</div><div class="meta">{{ $submission['type'] }}, {{ $submission['at']->diffForHumans() }}</div></div><span class="pill status {{ $submission['status'] === 'late' ? 'amber' : 'green' }}">{{ $submission['status'] }}</span></div>
              @empty
                <div class="empty">No completed submissions are available.</div>
              @endforelse
            </div>
          </article>
        </div>

        <div class="stack">
          <article class="card">
            <div class="card-head"><h3>High-risk learners</h3><a class="link" href="{{ route('instructor.risk.index') }}">Risk report →</a></div>
            <div class="card-body risk-list">
              @forelse ($atRiskStudents->take(6) as $snapshot)
                <div class="item"><div><div class="item-title">{{ $snapshot->student?->name ?? 'Deleted learner' }}</div><div class="meta">{{ $snapshot->classRoom?->name ?? 'Class removed' }}, score {{ number_format((float) $snapshot->average_score_percent, 1) }}%, engagement {{ number_format((float) $snapshot->engagement_score, 1) }}%</div></div><span class="pill red">High risk</span></div>
              @empty
                <div class="empty">No learners are currently marked high risk.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Quick actions</h3></div>
            <div class="card-body actions">
              <a class="btn" href="{{ route('instructor.classes.index') }}">Manage classes</a>
              <a class="btn" href="{{ route('instructor.assessments.index') }}">Assessments</a>
              <a class="btn" href="{{ route('instructor.tos.index') }}">TOS builder</a>
              <a class="btn" href="{{ route('instructor.reports.index') }}">Reports</a>
            </div>
          </article>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
