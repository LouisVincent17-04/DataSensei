<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DataSensei — Instructor Dashboard</title>
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8ca0bb;--accent:#3b82f6;--green:#10b981;--amber:#f59e0b;--red:#ef4444;--radius:10px}
    *{box-sizing:border-box}body{margin:0;min-height:100vh;display:flex;background:var(--bg);color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif}.main{flex:1;min-width:0}.topbar{height:64px;padding:0 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.topbar h1{font-size:1.05rem;margin:0}.link{color:var(--muted);text-decoration:none}.link:hover{color:var(--text)}.content{padding:28px;display:grid;gap:22px}.welcome,.card,.stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}.welcome{padding:24px 26px;border-left:4px solid var(--accent);display:flex;justify-content:space-between;gap:20px;align-items:center}.welcome h2{margin:0 0 6px;font-size:1.45rem}.welcome p,.muted{color:var(--muted)}.welcome p{margin:0;line-height:1.5}.actions{display:flex;gap:10px;flex-wrap:wrap}.btn{display:inline-flex;padding:9px 14px;border-radius:7px;border:1px solid var(--border);background:var(--surface2);color:var(--text);text-decoration:none;font-weight:650;font-size:.84rem}.btn.primary{background:var(--text);color:var(--bg)}.stats{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px}.stat{padding:18px}.stat .label{color:var(--muted);font-size:.76rem}.stat strong{display:block;font-size:1.55rem;margin-top:10px}.stat small{display:block;color:var(--muted);margin-top:5px}.grid{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(310px,.8fr);gap:22px}.stack{display:grid;gap:22px}.card-head{padding:17px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:16px;align-items:center}.card-head h3{font-size:.96rem;margin:0}.card-body{padding:18px 20px}.table-wrap{overflow:auto}table{width:100%;border-collapse:collapse;font-size:.82rem}th{text-align:left;color:var(--muted);font-weight:600;padding:10px;border-bottom:1px solid var(--border)}td{padding:12px 10px;border-bottom:1px solid var(--border)}tbody tr:last-child td{border-bottom:0}.work-list,.submission-list,.risk-list{display:grid;gap:11px}.item{border:1px solid var(--border);background:var(--bg);border-radius:8px;padding:13px 14px;display:flex;justify-content:space-between;gap:14px;align-items:center;color:inherit;text-decoration:none}.item:hover{border-color:#35517a}.item-title{font-weight:700;font-size:.86rem}.meta{font-size:.75rem;color:var(--muted);margin-top:4px;line-height:1.45}.pill{display:inline-flex;padding:4px 7px;border-radius:999px;background:var(--surface2);color:var(--muted);font-size:.68rem;font-weight:700;text-transform:uppercase}.pill.green{color:#79e6bb}.pill.amber{color:#f8ca75}.pill.red{color:#fca5a5}.empty{padding:18px;border:1px dashed var(--border);border-radius:8px;color:var(--muted);font-size:.84rem;text-align:center}@media(max-width:1250px){.stats{grid-template-columns:repeat(3,1fr)}}@media(max-width:950px){.grid{grid-template-columns:1fr}}@media(max-width:760px){.content{padding:18px}.topbar{padding:0 18px}.stats{grid-template-columns:1fr 1fr}.welcome{align-items:flex-start;flex-direction:column}}@media(max-width:480px){.stats{grid-template-columns:1fr}.item{align-items:flex-start;flex-direction:column}}
  </style>
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
                <a class="item" href="{{ $work['url'] }}"><div><div class="item-title">{{ $work['title'] }}</div><div class="meta">{{ $work['type'] }}{{ $work['class_name'] ? ' · '.$work['class_name'] : '' }} · {{ $work['at']->diffForHumans() }}</div></div><span class="pill {{ $work['status'] === 'published' ? 'green' : '' }}">{{ $work['status'] }}</span></a>
              @empty
                <div class="empty">No assignments or assessments have been created.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Recent submissions</h3><a class="link" href="{{ route('instructor.submissions.index') }}">All submissions →</a></div>
            <div class="card-body submission-list">
              @forelse ($recentSubmissions as $submission)
                <div class="item"><div><div class="item-title">{{ $submission['student'] }} · {{ $submission['title'] }}</div><div class="meta">{{ $submission['type'] }} · {{ $submission['at']->diffForHumans() }}</div></div><span class="pill {{ $submission['status'] === 'late' ? 'amber' : 'green' }}">{{ $submission['status'] }}</span></div>
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
                <div class="item"><div><div class="item-title">{{ $snapshot->student?->name ?? 'Deleted learner' }}</div><div class="meta">{{ $snapshot->classRoom?->name ?? 'Class removed' }} · score {{ number_format((float) $snapshot->average_score_percent, 1) }}% · engagement {{ number_format((float) $snapshot->engagement_score, 1) }}%</div></div><span class="pill red">High risk</span></div>
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
