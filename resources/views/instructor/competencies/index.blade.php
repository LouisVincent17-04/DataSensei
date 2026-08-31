<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Skills Competency Matrix — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8aa0bd;--dim:#536985;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--violet:#8b5cf6;--radius:15px;--radius-sm:9px}
    *{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}.main{flex:1;min-width:0;padding:30px;background:radial-gradient(circle at top right,rgba(59,130,246,.13),transparent 36%),var(--bg)}
    .top{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:22px}.eyebrow{font-size:.72rem;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--accent)}h1{font-size:1.9rem;margin:5px 0 8px}.subtitle{max-width:850px;color:var(--muted);line-height:1.6;margin:0}
    .card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:19px;margin-bottom:17px;box-shadow:0 18px 44px rgba(0,0,0,.15)}
    .form-row{display:flex;gap:10px;align-items:center;flex-wrap:wrap}.select,.btn{min-height:42px;border-radius:var(--radius-sm);font:inherit}.select{background:var(--bg);border:1px solid var(--border);color:var(--text);padding:9px 12px;min-width:260px}.btn{display:inline-flex;align-items:center;justify-content:center;border:0;background:var(--accent);color:white;padding:9px 14px;font-weight:800;cursor:pointer;text-decoration:none}.btn.secondary{background:var(--surface2);border:1px solid var(--border);color:var(--text)}
    .summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:17px}.metric{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px}.metric .label{font-size:.75rem;color:var(--muted);font-weight:800;text-transform:uppercase;letter-spacing:.06em}.metric .value{font-size:1.65rem;font-weight:900;margin-top:7px}.metric .detail{font-size:.78rem;color:var(--muted);margin-top:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .section-head{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;margin-bottom:13px}.section-head h2{font-size:1.08rem;margin:0}.section-head p{color:var(--muted);font-size:.82rem;margin:4px 0 0}.updated{font-size:.75rem;color:var(--muted)}
    .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:var(--radius-sm)}table{width:100%;border-collapse:separate;border-spacing:0;min-width:1050px}th,td{padding:12px;border-bottom:1px solid var(--border);border-right:1px solid rgba(30,47,71,.55);text-align:left;vertical-align:middle}th:last-child,td:last-child{border-right:0}tr:last-child td{border-bottom:0}th{position:sticky;top:0;background:#132036;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;z-index:2}.student-col{position:sticky;left:0;background:var(--surface);z-index:1;min-width:190px}.matrix th.student-col{z-index:3;background:#132036}.student-name{font-weight:800}.student-email{font-size:.74rem;color:var(--muted);margin-top:3px}
    .score{font-size:1rem;font-weight:900}.sub{font-size:.7rem;color:var(--muted);margin-top:3px}.bar{height:7px;background:var(--surface2);border-radius:999px;overflow:hidden;margin-top:7px;min-width:74px}.fill{height:100%;background:linear-gradient(90deg,var(--accent),var(--good));border-radius:999px}.cell.na .fill{width:0!important}.cell.na .score{color:var(--dim)}
    .level{display:inline-flex;padding:4px 8px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);font-size:.7rem;font-weight:800;white-space:nowrap}.level.expert{color:#c4b5fd;border-color:rgba(139,92,246,.4);background:rgba(139,92,246,.1)}.level.advanced{color:#6ee7b7;border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.1)}.level.competent{color:#93c5fd;border-color:rgba(59,130,246,.4);background:rgba(59,130,246,.1)}.level.developing{color:#fcd34d;border-color:rgba(245,158,11,.4);background:rgba(245,158,11,.1)}.level.beginner{color:#fca5a5;border-color:rgba(239,68,68,.4);background:rgba(239,68,68,.1)}.level.not-assessed{color:var(--muted)}
    .trend{height:40px;display:flex;align-items:flex-end;gap:3px;min-width:105px}.trend span{display:block;width:9px;min-height:3px;background:linear-gradient(180deg,var(--accent),rgba(59,130,246,.35));border-radius:3px 3px 1px 1px}.trend-empty{color:var(--dim);font-size:.74rem}
    .summary-table{min-width:900px}.summary-table td:first-child{font-weight:800}.performer{max-width:180px}.muted{color:var(--muted)}.alert{padding:14px 16px;border-radius:var(--radius-sm);border:1px solid rgba(245,158,11,.35);background:rgba(245,158,11,.08);color:#fcd34d;line-height:1.55}.empty{text-align:center;padding:40px;color:var(--muted)}.legend{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
    @media(max-width:1100px){.summary{grid-template-columns:repeat(2,minmax(0,1fr))}.main{padding:22px}}@media(max-width:760px){.layout{display:block}.summary{grid-template-columns:1fr}.top{display:block}.select{width:100%}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <header class="top">
      <div>
        <div class="eyebrow">Comparative Matrix</div>
        <h1 class="ds-page-title">Skills Competency Monitoring</h1>
        <p class="subtitle">Automatically compares mastery evidence from assessments, assignments, University Student challenges, coding exercises, Python IDE executions, and statistical Data Toolkit activities.</p>
      </div>
      @if($report && $selectedClass)
        <form method="POST" action="{{ route('instructor.competencies.refresh') }}">@csrf<input type="hidden" name="class_id" value="{{ $selectedClass->id }}"><button class="btn secondary" type="submit">Recalculate Evidence</button></form>
      @endif
    </header>

    <section class="card">
      <form method="GET" class="form-row">
        <label for="class_id" class="muted"><strong>Class:</strong></label>
        <select id="class_id" name="class_id" class="select">
          @forelse($classes as $class)
            <option value="{{ $class->id }}" @selected($selectedClass && $selectedClass->id === $class->id)>
              {{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }} ({{ $class->students_count }} students)
            </option>
          @empty
            <option>No active classes</option>
          @endforelse
        </select>
        <button type="submit" class="btn">Open Competency Matrix</button>
      </form>
    </section>

    @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

    @if($migrationRequired)
      <div class="alert"><strong>Database migration required.</strong> Run <code>php artisan migrate</code>, then reload this page.</div>
    @elseif(!$selectedClass)
      <section class="card empty">Create or restore an active class before using competency monitoring.</section>
    @elseif($report)
      @php
        $highest = $report['summary']['highest_performer'];
        $lowest = $report['summary']['lowest_performer'];
      @endphp

      <section class="summary">
        <div class="metric"><div class="label">Enrolled Students</div><div class="value">{{ $report['summary']['students'] }}</div><div class="detail">{{ $report['summary']['assessed_students'] }} with competency evidence</div></div>
        <div class="metric"><div class="label">Class Competency Average</div><div class="value">{{ number_format($report['summary']['class_average'], 1) }}%</div><div class="detail">Across assessed competencies</div></div>
        <div class="metric"><div class="label">Highest Performer</div><div class="value">{{ $highest ? number_format($highest['overall_percentage'], 1).'%' : '—' }}</div><div class="detail">{{ $highest['student']->name ?? 'No evidence yet' }}</div></div>
        <div class="metric"><div class="label">Lowest Performer</div><div class="value">{{ $lowest ? number_format($lowest['overall_percentage'], 1).'%' : '—' }}</div><div class="detail">{{ $lowest['student']->name ?? 'No evidence yet' }}</div></div>
      </section>

      <section class="card">
        <div class="section-head">
          <div><h2>Class Competency Summary</h2><p>Average, top and lowest performer, assessed students, and recent class trend for every competency.</p></div>
          <div class="updated">Calculated {{ $report['calculated_at']?->format('M d, Y h:i A') ?? 'not yet' }}</div>
        </div>
        <div class="table-wrap">
          <table class="summary-table">
            <thead><tr><th>Competency</th><th>Class Average</th><th>Level</th><th>Assessed</th><th>Highest</th><th>Lowest</th><th>Progress Trend</th></tr></thead>
            <tbody>
            @foreach($report['competencies'] as $competency)
              @php
                $stat = $report['competency_stats'][$competency->key];
                $points = collect($report['trends'][$competency->key] ?? []);
                $levelClass = strtolower(str_replace(' ', '-', $stat['level']));
              @endphp
              <tr>
                <td><strong>{{ $competency->name }}</strong><div class="sub">{{ $competency->description }}</div></td>
                <td><span class="score">{{ number_format($stat['average'], 1) }}%</span><div class="bar"><div class="fill" style="width:{{ $stat['average'] }}%"></div></div></td>
                <td><span class="level {{ $levelClass }}">{{ $stat['level'] }}</span></td>
                <td>{{ $stat['assessed_students'] }} / {{ $report['summary']['students'] }}</td>
                <td class="performer">@if($stat['highest'])<strong>{{ number_format($stat['highest']->percentage, 1) }}%</strong><div class="sub">{{ $stat['highest']->student->name ?? 'Student' }}</div>@else—@endif</td>
                <td class="performer">@if($stat['lowest'])<strong>{{ number_format($stat['lowest']->percentage, 1) }}%</strong><div class="sub">{{ $stat['lowest']->student->name ?? 'Student' }}</div>@else—@endif</td>
                <td>
                  @if($points->isNotEmpty())
                    <div class="trend">
                      @foreach($points as $point)<span title="{{ $point['date'] }}: {{ $point['percentage'] }}%" style="height:{{ max(3, $point['percentage'] * .38) }}px"></span>@endforeach
                    </div>
                    <div class="sub">{{ $points->first()['percentage'] ?? 0 }}% → {{ $points->last()['percentage'] ?? 0 }}%</div>
                  @else
                    <span class="trend-empty">Trend begins after first calculation</span>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </section>

      <section class="card">
        <div class="section-head">
          <div><h2>Student Competency Comparative Matrix</h2><p>Each percentage is computed from the evidence sources currently available for that competency.</p></div>
        </div>
        <div class="table-wrap">
          <table class="matrix">
            <thead>
              <tr>
                <th class="student-col">Student</th>
                <th>Overall</th>
                @foreach($report['competencies'] as $competency)<th>{{ $competency->name }}</th>@endforeach
              </tr>
            </thead>
            <tbody>
              @forelse($report['matrix'] as $row)
                <tr>
                  <td class="student-col"><div class="student-name">{{ $row['student']->name }}</div><div class="student-email">{{ $row['student']->email }}</div></td>
                  <td><span class="score">{{ number_format($row['overall_percentage'], 1) }}%</span><div class="sub">{{ $row['overall_level'] }} · {{ $row['evidence_count'] }} evidence</div><div class="bar"><div class="fill" style="width:{{ $row['overall_percentage'] }}%"></div></div></td>
                  @foreach($report['competencies'] as $competency)
                    @php $snapshot = $row['competencies'][$competency->key]; @endphp
                    <td class="cell {{ $snapshot->evidence_count > 0 ? '' : 'na' }}">
                      <span class="score">{{ $snapshot->evidence_count > 0 ? number_format($snapshot->percentage, 1).'%' : '—' }}</span>
                      <div class="sub">{{ $snapshot->level }} · {{ $snapshot->evidence_count }} evidence</div>
                      <div class="bar"><div class="fill" style="width:{{ $snapshot->percentage }}%"></div></div>
                    </td>
                  @endforeach
                </tr>
              @empty
                <tr><td colspan="{{ 2 + $report['competencies']->count() }}" class="empty">No enrolled students are available for comparison.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="legend">
          @foreach(['Beginner','Developing','Competent','Advanced','Expert','Not Assessed'] as $level)
            <span class="level {{ strtolower(str_replace(' ', '-', $level)) }}">{{ $level }}</span>
          @endforeach
        </div>
      </section>
    @endif
  </main>
</div>
</body>
</html>
