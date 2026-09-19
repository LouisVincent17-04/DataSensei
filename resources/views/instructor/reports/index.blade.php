<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports & Exports — DataSensei</title>
<style>
/* Reports and exports. Colours, type and radius come from partials.design-system. */
:root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
*{box-sizing:border-box}
body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
.layout{display:flex;min-height:100vh}
.main{flex:1;min-width:0;padding:28px 32px 48px;background:var(--bg)}

/* page header */
.top{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:24px}
.top > div{min-width:0;flex:1 1 320px}
.subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}

/* panels */
.card{margin-bottom:16px;padding:20px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.card > p{margin:0}
.muted{color:var(--muted)}

/* summary tiles */
.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:16px}
.grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:16px}
.grid-2 > .card{margin-bottom:0}
.metric{display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:16px 18px;
  border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
.metric .num{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
.metric .lbl{color:var(--muted);font-size:.8125rem;font-weight:500}

/* table panel */
.card.table-wrap{padding:0;overflow-x:auto}
.card.table-wrap > h2{position:sticky;left:0;margin:0;padding:14px 20px;border-bottom:1px solid var(--border);
  font-size:.9375rem;font-weight:600;line-height:1.35}
.table{width:100%;min-width:640px;border-collapse:collapse;font-variant-numeric:tabular-nums}
.table th,.table td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;vertical-align:middle}
.table th{padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;white-space:nowrap}
.table td{color:var(--ds-text-secondary);font-size:.875rem}
.table td:first-child{color:var(--text);font-weight:500}
.table td.muted{color:var(--muted);font-size:.8125rem}
.table :is(th,td):first-child{padding-left:20px}
.table :is(th,td):last-child{padding-right:20px}
.table tbody tr:last-child td{border-bottom:0}
.table tbody tr:hover td{background:rgba(255,255,255,.02)}

/* status badges */
.badge{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
  background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
.badge.good{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
.badge.warn{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
.badge.bad{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}

/* buttons */
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
  border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
  font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
  transition:background .12s ease,border-color .12s ease}
.btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
.btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
.btn.secondary:hover{background:var(--ds-surface-hover)}
.btn.good{background:transparent;border-color:var(--ds-success-border);color:var(--ds-success-text)}
.btn.good:hover{background:var(--ds-success-soft)}

/* filter form */
.form-row{display:flex;gap:8px;flex-wrap:wrap;align-items:center}
.form-row .muted{font-size:.8125rem}
.input,.form-row select{min-height:38px;max-width:100%;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
  background:var(--surface3);color:var(--text);font-size:.875rem;font-family:var(--ds-font-sans)}
.form-row select:first-of-type{min-width:220px}
.input:focus,.form-row select:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

/* flash */
.alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
  background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem}
.alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

/* pagination sits under the table */
.pagination{position:sticky;left:0}
.pagination nav{padding:12px 20px;border-top:1px solid var(--border)}

@media(max-width:1100px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:1000px){.grid-2{grid-template-columns:minmax(0,1fr)}}
@media(max-width:900px){.main{padding:24px 20px 40px}}
@media(max-width:700px){.layout{display:block}}
@media(max-width:640px){
  .main{padding:20px 16px 32px}
  .card{padding:16px}
  .card.table-wrap{padding:0}
  .card.table-wrap > h2{padding:12px 16px}
  .table :is(th,td):first-child{padding-left:16px}
  .pagination nav{padding:12px 16px}
  .top > .btn{width:100%}
  .form-row select,.form-row .btn{flex:1 1 100%;width:100%;min-width:0}
}
@media(max-width:420px){.grid{grid-template-columns:minmax(0,1fr)}}
</style>

    @include('partials.page-head', ['pageTitle' => 'Reports & Exports', 'pageDescription' => 'Export class results and progress reports.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">

    <div class="top">
      <div>
        <h1 class="title ds-page-title">Reports & Exports</h1>
        <p class="subtitle">Generate a quick performance report from the latest student analytics snapshots.</p>
      </div>
      <button class="btn secondary" type="button" onclick="window.print()">Print</button>
    </div>

    <div class="card">
      <form method="GET" class="form-row">
        <select name="class_id">
          @foreach($classes as $class)
            <option value="{{ $class->id }}" @selected($selectedClass && $selectedClass->id === $class->id)>{{ $class->name }}</option>
          @endforeach
        </select>
        <button class="btn" type="submit">Load Report</button>
      </form>
    </div>

    <div class="card table-wrap">
      <h2>{{ $selectedClass->name ?? 'Class' }} Performance Report</h2>
      <table class="table">
        <thead><tr><th>Student</th><th>Performance Segment</th><th>Risk</th><th>Score</th><th>Engagement</th><th>Generated</th></tr></thead>
        <tbody>
        @forelse($snapshots as $snapshot)
          <tr>
            <td>{{ $snapshot->student->name ?? 'Student' }}</td>
            <td>{{ $snapshot->cluster_label }}</td>
            <td>{{ ucfirst($snapshot->risk_level) }}</td>
            <td>{{ $snapshot->average_score_percent }}%</td>
            <td>{{ $snapshot->engagement_score }}%</td>
            <td>{{ optional($snapshot->generated_at)->format('M d, Y h:i A') }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No report data yet.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

  </main>
</div>
</body>
</html>
