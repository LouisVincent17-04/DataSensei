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
.table td.wrap{min-width:280px;line-height:1.5}
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

    @php $n = fn ($value) => rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.'); @endphp

    <div class="top">
      <div>
        <h1 class="title ds-page-title">Advanced Topic Recommendations</h1>
        <div class="subtitle">A level opens when you have 2 different qualifying modules in the level below it. A module qualifies when one attempt reaches the score and stays within the time shown below. Your best attempt at each module counts, and each module counts once. For coding challenges, the score and time are the averages of the items in that challenge.</div>
      </div>
    </div>

    @if($recommendations->isEmpty())
      <div class="card">
        <h2>No new level unlocked yet</h2>
        <p class="muted">Newbie modules need at least 80% score with 50% time consumed or less, Intermediate modules 75% with 70% or less, and Advanced modules 70% with 80% or less. Two qualifying modules open the next level, starting with its first module.</p>
      </div>
    @else
      <div class="grid-2">
        @foreach($recommendations as $item)
          <div class="card">
            <h2>{{ $item['target_name'] }} is unlocked for {{ $item['track'] === 'coding' ? 'coding challenges' : 'MCQ challenges' }}</h2>
            <p class="muted">{{ $item['message'] }}</p>
            @foreach(array_slice($item['qualifying_modules'], 0, $item['required']) as $module)
              <p>{{ $module['title'] }}: <strong>{{ $n($module['score']) }}%</strong> score, <strong>{{ $n($module['time']) }}%</strong> time consumed</p>
            @endforeach
            @if(count($item['qualifying_modules']) > $item['required'])
              @php $more = count($item['qualifying_modules']) - $item['required']; @endphp
              <p>{{ $more }} more qualifying {{ $more === 1 ? 'module is' : 'modules are' }} listed in your module results below.</p>
            @endif
            <a class="btn" href="{{ $item['url'] }}">Open {{ $item['target_name'] }}</a>
          </div>
        @endforeach
      </div>
    @endif

    <div class="card table-wrap">
      <h2>Your progress toward each level</h2>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Type</th>
            <th scope="col">Level</th>
            <th scope="col">Qualifying modules</th>
            <th scope="col">Each module needs</th>
            <th scope="col">Opens</th>
            <th scope="col">Status</th>
            <th scope="col">What is left</th>
          </tr>
        </thead>
        <tbody>
          @foreach($progress as $row)
            <tr>
              <td>{{ $row['track'] === 'coding' ? 'Coding' : 'Multiple choice' }}</td>
              <td>{{ $row['source_name'] }}</td>
              <td>{{ min($row['qualifying'], $row['required']) }} of {{ $row['required'] }}@if($row['qualifying'] > $row['required']) <span class="muted">({{ $row['qualifying'] }} in all)</span>@endif</td>
              <td>{{ $n($row['required_score']) }}% score, {{ $n($row['required_time']) }}% time or less</td>
              <td>{{ $row['target_name'] }}</td>
              <td>{{ $row['status'] === 'Not yet' && $row['total_modules'] === 0 ? 'No modules yet' : $row['status'] }}</td>
              <td class="muted wrap">{{ $row['message'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="card table-wrap">
      <h2>Your module results</h2>
      <table class="table">
        <thead>
          <tr>
            <th scope="col">Type</th>
            <th scope="col">Level</th>
            <th scope="col">Module</th>
            <th scope="col">Score</th>
            <th scope="col">Time consumed</th>
            <th scope="col">Counts toward the next level</th>
          </tr>
        </thead>
        <tbody>
          @forelse($moduleResults as $result)
            <tr>
              <td>{{ $result['track'] === 'coding' ? 'Coding' : 'Multiple choice' }}</td>
              <td>{{ $result['level'] }}</td>
              <td>{{ $result['module'] }}</td>
              <td>{{ $result['score'] === null ? '—' : $n($result['score']) . '%' }} <span class="muted">(needs {{ $n($result['required_score']) }}%)</span></td>
              <td>{{ $result['time'] === null ? '—' : $n($result['time']) . '%' }} <span class="muted">(needs {{ $n($result['required_time']) }}% or less)</span></td>
              <td>{{ $result['qualifies'] ? 'Yes' : 'No: ' . lcfirst($result['note']) }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="muted">No module results yet. Finish a Newbie module to see how it counts.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </main>
</div>
</body>
</html>
