<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced Topic Recommendations — DataSensei</title>
<style>
/* Advanced topic recommendations. Colours, type and radius come from partials.design-system. */
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

/* recommendation cards */
.card h2{margin:0 0 4px;font-size:1rem;font-weight:600;line-height:1.35}
.card > .badge + h2{margin-top:10px}
.card p{margin:0;font-size:.875rem;line-height:1.55}
.card p.muted{margin-bottom:12px}
.card p.muted strong{color:var(--ds-text-secondary);font-weight:600}
.grid-2 > .card{display:flex;flex-direction:column;align-items:flex-start}
.grid-2 > .card > .badge{border-color:var(--ds-border-strong);background:var(--surface2);color:var(--ds-text-secondary)}
.grid-2 > .card p:not(.muted){width:100%;padding:8px 0;border-top:1px solid var(--border);color:var(--muted);font-size:.8125rem}
.grid-2 > .card p:not(.muted) strong{color:var(--text);font-weight:600;font-variant-numeric:tabular-nums}
.grid-2 > .card > .btn{margin-top:12px}

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

    @include('partials.page-head', ['pageTitle' => 'Advanced Topic Recommendations', 'pageDescription' => 'Topics DataSensei suggests next, based on what you have completed.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">

    <div class="top">
      <div>
        <h1 class="title ds-page-title">Advanced Topic Recommendations</h1>
        <div class="subtitle">DataSensei recommends higher difficulty paths when your score, time efficiency, and completion evidence show exceptional mastery.</div>
      </div>
    </div>

    @if($recommendations->isEmpty())
      <div class="card">
        <h2>No advanced recommendation yet</h2>
        <p class="muted">Complete a full difficulty path with excellent accuracy and efficient time use. When you show exceptional performance, the next-next difficulty can unlock early.</p>
      </div>
    @else
      <div class="grid-2">
        @foreach($recommendations as $item)
          <div class="card">
            <span class="badge good">{{ $item['track_label'] }}</span>
            <h2>{{ $item['target_name'] }}</h2>
            <p class="muted">Recommended because of exceptional performance in <strong>{{ $item['source_name'] }}</strong>.</p>
            <p>Average Score: <strong>{{ $item['score'] }}%</strong></p>
            @if($item['time_ratio'] !== null)
              <p>Average Time Used: <strong>{{ round($item['time_ratio'] * 100) }}% of limit</strong></p>
            @endif
            @if($item['attempts'] !== null)
              <p>Average Attempts: <strong>{{ round($item['attempts'], 2) }}</strong></p>
            @endif
            <a class="btn" href="{{ $item['url'] }}">Open Recommended Path</a>
          </div>
        @endforeach
      </div>
    @endif

  </main>
</div>
</body>
</html>
