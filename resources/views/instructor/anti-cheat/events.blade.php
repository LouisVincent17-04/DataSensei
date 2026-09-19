<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Anti-Cheat Events — DataSensei</title>
<style>
/* Anti-cheat event log. Colours, type and radius come from partials.design-system. */
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
.layout{display:flex;min-height:100vh}
.main{flex:1;min-width:0;padding:28px 32px 48px}

/* page header */
.top{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px}
.top > div:first-child{min-width:0;flex:1 1 320px}
.subtitle{max-width:72ch;margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.55}

.card{margin-bottom:16px;padding:16px 20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}

/* filter row */
.form-row{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.input,select{min-width:180px;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
  background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
  transition:border-color .12s ease,box-shadow .12s ease}
.input:focus,select:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
  border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
  font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
  transition:background .12s ease,border-color .12s ease}
.btn:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
.muted{color:var(--muted)}

/* table card: rows run edge to edge */
.card.table-wrap{padding:0;overflow-x:auto}
.table{width:100%;border-collapse:collapse}
.table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
  font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
.table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
  line-height:1.45;vertical-align:middle}
.table tbody tr:last-child td{border-bottom:0}
.table tbody tr:hover td{background:rgba(255,255,255,.02)}
.table td.muted[colspan]{padding:32px 20px;color:var(--muted);text-align:center}
.table tbody tr:hover td[colspan]{background:none}
.badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
  background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
.badge.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
.badge.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
.badge.bad{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}
.pagination{padding:14px 20px;border-top:1px solid var(--border)}
.pagination:not(:has(*)){display:none}
.card .table{min-width:900px}
.table td:nth-child(6){white-space:nowrap;font-variant-numeric:tabular-nums}
/* raw event details are shown as data */
.table td.muted:not([colspan]){min-width:220px;max-width:360px;font-family:var(--ds-font-mono);font-size:.75rem;line-height:1.5;overflow-wrap:anywhere}

@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .card{padding:16px}
  .card.table-wrap{padding:0}
  .pagination{padding:12px 16px}
  .form-row > *{width:100%}
  .input,select{min-width:0}
  .table td.muted[colspan]{text-align:left}
}
@media(prefers-reduced-motion:reduce){.btn,.input,select{transition:none}}
</style>

    @include('partials.page-head', ['pageTitle' => 'Anti-Cheat Events', 'pageDescription' => 'Anti-cheat settings and the events recorded during assessments.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">

    <div class="top">
      <div>
        <h1 class="title ds-page-title">Anti-Cheat Events</h1>
        <p class="subtitle">Review violations recorded during instructor-given assignments only.</p>
      </div>
    </div>

    <div class="card">
      <form method="GET" class="form-row">
        <select name="class_id">
          <option value="">All classes</option>
          @foreach($classes as $class)
            <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}</option>
          @endforeach
        </select>
        <select name="severity">
          <option value="">All severity</option>
          @foreach(['info','warning','critical'] as $severity)
            <option value="{{ $severity }}" @selected(request('severity') === $severity)>{{ ucfirst($severity) }}</option>
          @endforeach
        </select>
        <button class="btn" type="submit">Filter</button>
      </form>
    </div>

    <div class="card table-wrap">
      <table class="table">
        <thead><tr><th>Student</th><th>Class</th><th>Assignment</th><th>Event</th><th>Severity</th><th>Time</th><th>Details</th></tr></thead>
        <tbody>
        @forelse($events as $event)
          @php $severityClass = $event->severity === 'critical' ? 'bad' : ($event->severity === 'warning' ? 'warn' : 'good'); @endphp
          <tr>
            <td>{{ $event->user->name ?? 'Student' }}</td>
            <td>{{ $event->classRoom->name ?? '—' }}</td>
            <td>{{ $event->classAssignment->title ?? '—' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $event->event_type)) }}</td>
            <td><span class="badge {{ $severityClass }}">{{ ucfirst($event->severity) }}</span></td>
            <td>{{ optional($event->occurred_at)->format('M d, Y h:i A') ?? optional($event->created_at)->format('M d, Y h:i A') }}</td>
            <td class="muted">{{ is_array($event->details) ? json_encode($event->details) : $event->details }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="muted">No anti-cheat events recorded yet.</td></tr>
        @endforelse
        </tbody>
      </table>
      <div class="pagination">{{ $events->links() }}</div>
    </div>

  </main>
</div>
</body>
</html>
