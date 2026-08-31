<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ILO Mastery — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <style>
:root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#7f93b0;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:14px;--radius-sm:8px}*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;padding:32px;min-width:0;background:radial-gradient(circle at top right,rgba(59,130,246,.10),transparent 40%),var(--bg)}.top{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;margin-bottom:24px}.title{font-size:1.8rem;font-weight:800;margin:0}.subtitle{color:var(--muted);margin-top:8px;line-height:1.6}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:18px}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.metric{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:18px}.metric .num{font-size:1.6rem;font-weight:800}.metric .lbl{color:var(--muted);font-size:.85rem;margin-top:4px}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:12px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top}.table th{color:var(--muted);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em}.badge{display:inline-flex;align-items:center;padding:4px 8px;border-radius:999px;font-size:.75rem;font-weight:700;border:1px solid var(--border);background:var(--surface2);color:var(--text)}.badge.good{color:var(--good);border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.08)}.badge.warn{color:var(--warn);border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.08)}.badge.bad{color:var(--bad);border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.08)}.btn{display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:var(--radius-sm);background:var(--accent);color:white;text-decoration:none;border:0;font-weight:700;cursor:pointer}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}.btn.good{background:var(--good)}.form-row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}.input,select{background:var(--bg);border:1px solid var(--border);color:var(--text);padding:10px 12px;border-radius:var(--radius-sm)}.muted{color:var(--muted)}.alert{padding:12px 14px;border-radius:var(--radius-sm);margin-bottom:16px;border:1px solid rgba(16,185,129,.25);background:rgba(16,185,129,.08);color:var(--good)}.alert.error{border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:var(--bad)}.pagination{margin-top:16px}.pagination nav{display:flex;gap:8px;flex-wrap:wrap}@media(max-width:1000px){.grid,.grid-2{grid-template-columns:1fr}.main{padding:20px}}@media(max-width:700px){.layout{display:block}}
</style>

</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">

    <div class="top">
      <div>
        <h1 class="title ds-page-title">ILO Mastery</h1>
        <p class="subtitle">Track student mastery per intended learning outcome and module.</p>
      </div>
    </div>

    <div class="card">
      <form method="GET" class="form-row">
        <select name="class_id">
          @foreach($classes as $class)
            <option value="{{ $class->id }}" @selected($selectedClass && $selectedClass->id === $class->id)>{{ $class->name }}</option>
          @endforeach
        </select>
        <select name="module_no">
          @for($i = 1; $i <= 24; $i++)
            <option value="{{ $i }}" @selected($moduleNo === $i)>Module {{ $i }}</option>
          @endfor
        </select>
        <button class="btn" type="submit">View Mastery</button>
      </form>
    </div>

    <div class="grid-2">
      @forelse($ilos as $ilo)
        <div class="card">
          <span class="badge good">{{ $ilo->ilo_code }}</span>
          <h3>{{ $ilo->title }}</h3>
          <p class="muted">{{ $ilo->description }}</p>
          <p>Mastery threshold: <strong>{{ $ilo->mastery_threshold }}%</strong></p>
        </div>
      @empty
        <div class="card muted">No ILO records found for this module.</div>
      @endforelse
    </div>

    <div class="card table-wrap">
      <table class="table">
        <thead><tr><th>Student</th><th>ILO</th><th>Mastery</th><th>Status</th><th>Evidence</th><th>Last Evaluated</th></tr></thead>
        <tbody>
        @forelse($masteries as $mastery)
          @php $statusClass = $mastery->status === 'mastered' ? 'good' : ($mastery->status === 'developing' ? 'warn' : 'bad'); @endphp
          <tr>
            <td>{{ $mastery->student->name ?? 'Student' }}</td>
            <td>{{ $mastery->ilo->ilo_code ?? 'ILO' }} — {{ $mastery->ilo->title ?? '' }}</td>
            <td>
              @if((int) $mastery->evidence_count === 0)
                <span class="muted">No mapped evidence yet</span>
              @else
                {{ $mastery->mastery_percent }}%
              @endif
            </td>
            <td><span class="badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $mastery->status)) }}</span></td>
            <td>{{ $mastery->evidence_count }} mapped item{{ (int) $mastery->evidence_count === 1 ? '' : 's' }}</td>
            <td>{{ optional($mastery->last_evaluated_at)->format('M d, Y h:i A') ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="6" class="muted">No mapped mastery evidence yet for this class and module. Grade an assessment or assignment question that is explicitly mapped to an ILO.</td></tr>
        @endforelse
        </tbody>
      </table>
      <div class="pagination">{{ $masteries->links() }}</div>
    </div>

  </main>
</div>
</body>
</html>
