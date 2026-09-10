<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Skills Competencies — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8aa0bd;--dim:#536985;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--violet:#8b5cf6;--radius:16px;--radius-sm:10px}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;min-width:0;padding:30px;background:radial-gradient(circle at top right,rgba(59,130,246,.13),transparent 38%),var(--bg)}
    .top{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;margin-bottom:22px}.eyebrow{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--accent)}h1{font-size:1.95rem;margin:5px 0 8px}.subtitle{color:var(--muted);line-height:1.6;max-width:820px;margin:0}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:19px;margin-bottom:17px;box-shadow:0 18px 44px rgba(0,0,0,.15)}.form-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.select,.btn{min-height:42px;border-radius:var(--radius-sm);font:inherit}.select{background:var(--bg);border:1px solid var(--border);color:var(--text);padding:9px 12px;min-width:270px}.btn{display:inline-flex;align-items:center;justify-content:center;border:0;background:var(--accent);color:white;padding:9px 14px;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}
    .hero-grid{display:grid;grid-template-columns:1.3fr repeat(3,minmax(0,1fr));gap:14px;margin-bottom:17px}.metric{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px}.metric.primary{background:linear-gradient(135deg,rgba(59,130,246,.18),rgba(139,92,246,.08)),var(--surface)}.metric .label{font-size:.75rem;color:var(--muted);font-weight:800;text-transform:uppercase;letter-spacing:.06em}.metric .value{font-size:1.7rem;font-weight:900;margin-top:7px}.metric .detail{font-size:.78rem;color:var(--muted);margin-top:5px}
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:15px}.competency{position:relative;overflow:hidden}.competency:after{content:"";position:absolute;width:120px;height:120px;border-radius:50%;right:-55px;top:-55px;background:rgba(59,130,246,.08)}.competency-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.competency h2{font-size:1.06rem;margin:0}.description{font-size:.79rem;color:var(--muted);line-height:1.5;margin-top:5px}.score-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:16px}.mini{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:11px}.mini .n{font-size:1.06rem;font-weight:900}.mini .l{font-size:.68rem;color:var(--muted);margin-top:3px;text-transform:uppercase;letter-spacing:.04em}.bar{height:9px;background:var(--surface2);border:1px solid var(--border);border-radius:999px;overflow:hidden;margin-top:13px}.fill{height:100%;background:linear-gradient(90deg,var(--accent),var(--good));border-radius:999px}.meta{display:flex;justify-content:space-between;gap:12px;color:var(--muted);font-size:.75rem;margin-top:7px}.sources{display:flex;gap:6px;flex-wrap:wrap;margin-top:13px}.source{font-size:.7rem;padding:5px 8px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);color:var(--muted)}
    .level{display:inline-flex;padding:5px 9px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);font-size:.72rem;font-weight:800;white-space:nowrap}.level.expert{color:#c4b5fd;border-color:rgba(139,92,246,.4)}.level.advanced{color:#6ee7b7;border-color:rgba(16,185,129,.4)}.level.competent{color:#93c5fd;border-color:rgba(59,130,246,.4)}.level.developing{color:#fcd34d;border-color:rgba(245,158,11,.4)}.level.beginner{color:#fca5a5;border-color:rgba(239,68,68,.4)}.level.not-assessed{color:var(--muted)}
    .trend-wrap{margin-top:14px;border-top:1px solid var(--border);padding-top:12px}.trend-title{display:flex;justify-content:space-between;font-size:.72rem;color:var(--muted)}.trend{height:52px;display:flex;align-items:flex-end;gap:5px;margin-top:8px}.trend span{flex:1;max-width:30px;min-width:8px;min-height:3px;background:linear-gradient(180deg,var(--accent),rgba(59,130,246,.3));border-radius:4px 4px 2px 2px}.trend-empty{font-size:.75rem;color:var(--dim);padding:14px 0}
    .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:var(--radius-sm)}table{width:100%;border-collapse:collapse;min-width:780px}th,td{padding:12px;border-bottom:1px solid var(--border);text-align:left}th{background:#132036;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.05em}tr:last-child td{border-bottom:0}.delta.good{color:var(--good)}.delta.bad{color:var(--bad)}.muted{color:var(--muted)}.alert{padding:14px 16px;border-radius:var(--radius-sm);border:1px solid rgba(245,158,11,.35);background:rgba(245,158,11,.08);color:#fcd34d;line-height:1.55}.empty{text-align:center;padding:42px;color:var(--muted)}
    @media(max-width:1150px){.hero-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.grid{grid-template-columns:1fr}}@media(max-width:760px){.layout{display:block}.main{padding:21px}.top{display:block}.hero-grid{grid-template-columns:1fr}.score-row{grid-template-columns:repeat(2,1fr)}.select{width:100%}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <header class="top">
      <div>
        <div class="eyebrow"></div>
        <h1 class="ds-page-title">My Skills Competency Matrix</h1>
        <p class="subtitle">See how your evidence from assessments, assignments, challenges, coding exercises, Python IDE work, and statistical activities compares with your class.</p>
      </div>
      @if($report)<a class="btn secondary" href="{{ route('student.competencies.index', ['class_id' => $selectedClass->id]) }}">Reload Saved Results</a>@endif
    </header>

    <section class="card">
      <form method="GET" class="form-row">
        <label for="class_id" class="muted"><strong>Enrolled class:</strong></label>
        <select id="class_id" name="class_id" class="select">
          @forelse($classes as $class)
            <option value="{{ $class->id }}" @selected($selectedClass && $selectedClass->id === $class->id)>{{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }}</option>
          @empty
            <option>No active class enrollment</option>
          @endforelse
        </select>
        <button class="btn" type="submit">View Competencies</button>
      </form>
    </section>

    @if($migrationRequired)
      <div class="alert"><strong>Competency monitoring has not been installed yet.</strong> Ask the administrator to run <code>php artisan migrate</code>.</div>
    @elseif(!$selectedClass)
      <section class="card empty">Your competency matrix becomes available after you are enrolled in an active class.</section>
    @elseif($report && $report['student'])
      @php
        $studentRow = $report['student'];
        $highest = $report['class_summary']['highest_performer'];
        $lowest = $report['class_summary']['lowest_performer'];
      @endphp
      <section class="hero-grid">
        <div class="metric primary"><div class="label">Overall Competency</div><div class="value">{{ number_format($studentRow['overall_percentage'], 1) }}%</div><div class="detail">{{ $studentRow['overall_level'] }} · {{ $studentRow['evidence_count'] }} total evidence records</div></div>
        <div class="metric"><div class="label">Class Average</div><div class="value">{{ number_format($report['class_summary']['class_average'], 1) }}%</div><div class="detail">{{ $report['class_summary']['assessed_students'] }} assessed classmates</div></div>
        <div class="metric"><div class="label">Highest Performer</div><div class="value">{{ $highest ? number_format($highest['overall_percentage'], 1).'%' : '—' }}</div><div class="detail">{{ $highest['student']->name ?? 'No evidence yet' }}</div></div>
        <div class="metric"><div class="label">Lowest Performer</div><div class="value">{{ $lowest ? number_format($lowest['overall_percentage'], 1).'%' : '—' }}</div><div class="detail">{{ $lowest['student']->name ?? 'No evidence yet' }}</div></div>
      </section>

      <section class="grid">
        @foreach($report['competencies'] as $competency)
          @php
            $snapshot = $studentRow['competencies'][$competency->key];
            $comparison = $report['comparisons'][$competency->key];
            $delta = round((float)$snapshot->percentage - (float)$comparison['average'], 1);
            $points = collect($report['trends'][$competency->key] ?? []);
            $levelClass = strtolower(str_replace(' ', '-', $snapshot->level));
            $sources = collect($snapshot->source_breakdown ?? []);
          @endphp
          <article class="card competency">
            <div class="competency-head">
              <div><h2>{{ $competency->name }}</h2><div class="description">{{ $competency->description }}</div></div>
              <span class="level {{ $levelClass }}">{{ $snapshot->level }}</span>
            </div>
            <div class="score-row">
              <div class="mini"><div class="n">{{ $snapshot->evidence_count > 0 ? number_format($snapshot->percentage, 1).'%' : '—' }}</div><div class="l">Your Score</div></div>
              <div class="mini"><div class="n">{{ number_format($comparison['average'], 1) }}%</div><div class="l">Class Average</div></div>
              <div class="mini"><div class="n">{{ $comparison['highest'] ? number_format($comparison['highest']->percentage, 1).'%' : '—' }}</div><div class="l">Highest</div></div>
              <div class="mini"><div class="n">{{ $comparison['lowest'] ? number_format($comparison['lowest']->percentage, 1).'%' : '—' }}</div><div class="l">Lowest</div></div>
            </div>
            <div class="bar"><div class="fill" style="width:{{ $snapshot->percentage }}%"></div></div>
            <div class="meta"><span>{{ $snapshot->evidence_count }} evidence records</span><span class="delta {{ $delta >= 0 ? 'good' : 'bad' }}">{{ $delta >= 0 ? '+' : '' }}{{ number_format($delta, 1) }} vs class</span></div>
            <div class="sources">
              @forelse($sources as $source => $details)
                <span class="source">{{ ucfirst($source) }} {{ number_format((float)($details['score'] ?? 0), 1) }}%</span>
              @empty
                <span class="source">Complete related activities to generate evidence</span>
              @endforelse
            </div>
            <div class="trend-wrap">
              <div class="trend-title"><span>Progress trend</span><span>@if($points->count() > 1){{ $points->first()['percentage'] }}% → {{ $points->last()['percentage'] }}%@else Latest snapshot @endif</span></div>
              @if($points->isNotEmpty())
                <div class="trend">@foreach($points as $point)<span title="{{ $point['date'] }}: {{ $point['percentage'] }}%" style="height:{{ max(3, $point['percentage'] * .48) }}px"></span>@endforeach</div>
              @else
                <div class="trend-empty">Your trend starts after the first competency calculation.</div>
              @endif
            </div>
          </article>
        @endforeach
      </section>

      <section class="card">
        <h2 style="font-size:1.08rem;margin:0 0 5px">Comparative Matrix</h2>
        <p class="muted" style="font-size:.82rem;margin:0 0 14px">A compact comparison of your competency level against the class average and performance range.</p>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Competency</th><th>Your Percentage</th><th>Level</th><th>Class Average</th><th>Highest</th><th>Lowest</th><th>Difference</th></tr></thead>
            <tbody>
            @foreach($report['competencies'] as $competency)
              @php
                $snapshot = $studentRow['competencies'][$competency->key];
                $comparison = $report['comparisons'][$competency->key];
                $delta = round((float)$snapshot->percentage - (float)$comparison['average'], 1);
              @endphp
              <tr>
                <td><strong>{{ $competency->name }}</strong></td>
                <td>{{ $snapshot->evidence_count > 0 ? number_format($snapshot->percentage, 1).'%' : 'Not assessed' }}</td>
                <td>{{ $snapshot->level }}</td>
                <td>{{ number_format($comparison['average'], 1) }}%</td>
                <td>{{ $comparison['highest'] ? number_format($comparison['highest']->percentage, 1).'%' : '—' }}</td>
                <td>{{ $comparison['lowest'] ? number_format($comparison['lowest']->percentage, 1).'%' : '—' }}</td>
                <td class="delta {{ $delta >= 0 ? 'good' : 'bad' }}">{{ $delta >= 0 ? '+' : '' }}{{ number_format($delta, 1) }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </section>
    @elseif($report)
      <section class="card empty">Your class enrollment is active, but no competency row could be generated. Reload the page or ask your instructor to refresh the class matrix.</section>
    @endif
  </main>
</div>
</body>
</html>
