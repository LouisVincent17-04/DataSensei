<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Skills Competency Matrix — DataSensei</title>
<style>
    /* Skills competency matrix. Colours, type and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px;background:var(--bg)}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div{min-width:0;flex:1 1 320px}
    .subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* panels: header row and tables run edge to edge */
    .card{--pad:20px;margin-bottom:16px;padding:var(--pad);overflow:hidden;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card > .section-head{margin:calc(-1 * var(--pad)) calc(-1 * var(--pad)) 0;padding:14px var(--pad);border-bottom:1px solid var(--border)}
    .card > .table-wrap{width:auto;max-width:none;margin:0 calc(-1 * var(--pad));border:0;border-radius:0}
    .card > .table-wrap :is(th,td):first-child{padding-left:var(--pad)}
    .card > .table-wrap :is(th,td):last-child{padding-right:var(--pad)}
    .card > .table-wrap:last-child{margin-bottom:calc(-1 * var(--pad))}
    .card > .table-wrap + .legend{margin:0 calc(-1 * var(--pad)) calc(-1 * var(--pad));padding:12px var(--pad);border-top:1px solid var(--border)}

    /* class picker */
    .form-row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    .form-row label{color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .form-row label strong{font-weight:500}
    .select,.btn{min-height:38px;border-radius:var(--radius-sm);font-family:var(--ds-font-sans);font-size:.875rem}
    .select{min-width:260px;padding:8px 12px;border:1px solid var(--ds-input-border);background:var(--surface3);color:var(--text)}
    .select:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .btn{display:inline-flex;align-items:center;justify-content:center;padding:0 16px;border:1px solid var(--accent);
      background:var(--accent);color:#fff;font-weight:500;line-height:1.2;white-space:nowrap;cursor:pointer;text-decoration:none;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}

    /* summary tiles */
    .summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:16px}
    .metric{padding:16px 18px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .metric .label{color:var(--muted);font-size:.8125rem;font-weight:500}
    .metric .value{margin-top:4px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .metric .detail{margin-top:4px;color:var(--muted);font-size:.75rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

    /* section header */
    .section-head{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:8px 16px;margin-bottom:12px}
    .section-head > div:first-child{min-width:0;flex:1 1 320px}
    .section-head h2{margin:0;font-size:.9375rem;font-weight:600}
    .section-head p{margin:4px 0 0;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .updated{flex-shrink:0;color:var(--muted);font-size:.75rem}

    /* tables */
    .table-wrap{overflow:auto}
    table{width:100%;border-collapse:separate;border-spacing:0;min-width:1050px;font-variant-numeric:tabular-nums}
    th,td{padding:12px 14px;border-bottom:1px solid var(--border);text-align:left;vertical-align:middle}
    td{color:var(--ds-text-secondary);font-size:.875rem}
    td strong{color:var(--text);font-weight:600}
    tbody tr:last-child td{border-bottom:0}
    tbody tr:hover td{background:rgba(255,255,255,.02)}
    th{position:sticky;top:0;z-index:2;padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;white-space:nowrap}
    .student-col{position:sticky;left:0;z-index:1;min-width:190px;background:var(--surface);box-shadow:inset -1px 0 0 var(--border)}
    tbody tr:hover td.student-col{background:#131f31}
    .matrix th.student-col{z-index:3;background:var(--surface3)}
    .student-name{color:var(--text);font-weight:600}
    .student-email{margin-top:2px;color:var(--muted);font-size:.75rem;overflow-wrap:anywhere}

    .score{color:var(--text);font-size:.9375rem;font-weight:600;font-variant-numeric:tabular-nums}
    .sub{margin-top:2px;color:var(--muted);font-size:.75rem;line-height:1.45}
    .bar{height:6px;min-width:74px;margin-top:8px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .fill{height:100%;border-radius:999px;background:var(--accent)}
    .cell.na .fill{width:0!important}
    .cell.na .score{color:var(--dim)}

    /* competency levels */
    .level{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .level.expert{color:#dbeafe;border-color:var(--accent);background:rgba(59,130,246,.24)}
    .level.advanced{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .level.competent{color:var(--ds-accent-text);border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
    .level.developing{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .level.beginner{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}
    .level.not-assessed{color:var(--muted)}

    /* class trend mini chart */
    .trend{height:40px;min-width:105px;display:flex;align-items:flex-end;gap:4px}
    .trend span{display:block;width:8px;min-height:3px;border-radius:2px;background:var(--accent)}
    .trend-empty{color:var(--muted);font-size:.75rem}

    .summary-table{min-width:900px}
    .summary-table td:first-child{min-width:260px}
    .matrix td:not(.student-col){min-width:140px}
    .performer{max-width:180px}
    .muted{color:var(--muted)}
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-warning-border);border-radius:var(--radius-sm);
      background:var(--ds-warning-soft);color:#fef3c7;font-size:.875rem;line-height:1.55}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;text-align:center}
    .legend{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}

    @media(max-width:1100px){.summary{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:760px){.layout{display:block}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{--pad:16px}
      .top form,.top .btn{width:100%}
      .form-row .select,.form-row .btn{flex:1 1 100%;width:100%;min-width:0}
      .student-col{min-width:150px}
    }
    @media(max-width:480px){.summary{grid-template-columns:minmax(0,1fr)}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Skills Competency Matrix', 'pageDescription' => 'The competency matrix for your students, skill by skill.'])
</head>
<body>
<div class="layout">
  @include('partials.instructor-sidebar')
  <main class="main">
    <header class="top">
      <div>
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

    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif

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
                  <td><span class="score">{{ number_format($row['overall_percentage'], 1) }}%</span><div class="sub">{{ $row['overall_level'] }}, {{ $row['evidence_count'] }} evidence</div><div class="bar"><div class="fill" style="width:{{ $row['overall_percentage'] }}%"></div></div></td>
                  @foreach($report['competencies'] as $competency)
                    @php $snapshot = $row['competencies'][$competency->key]; @endphp
                    <td class="cell {{ $snapshot->evidence_count > 0 ? '' : 'na' }}">
                      <span class="score">{{ $snapshot->evidence_count > 0 ? number_format($snapshot->percentage, 1).'%' : '—' }}</span>
                      <div class="sub">{{ $snapshot->level }}, {{ $snapshot->evidence_count }} evidence</div>
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
