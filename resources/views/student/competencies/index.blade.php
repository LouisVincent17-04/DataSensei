<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Skills Competencies — DataSensei</title>
<style>
    /* Student competency matrix. Colours, type and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger);--violet:var(--ds-accent)}
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px;background:var(--bg)}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}

    .card{margin-bottom:16px;padding:20px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .card > h2{color:var(--text);font-weight:600;line-height:1.35}

    /* class picker */
    .form-row{display:flex;gap:8px 12px;align-items:center;flex-wrap:wrap}
    .form-row label{color:var(--ds-text-secondary);font-size:.8125rem}
    .form-row label strong{font-weight:500}
    .select{min-height:38px;min-width:270px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font-family:var(--ds-font-sans);font-size:.875rem;outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .select:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font-family:var(--ds-font-sans);font-size:.875rem;font-weight:500;line-height:1.2;white-space:nowrap;cursor:pointer;text-decoration:none;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover);border-color:var(--ds-border-strong)}

    /* summary figures */
    .hero-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:16px}
    .metric{min-width:0;padding:16px 18px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .metric .label{color:var(--muted);font-size:.8125rem;font-weight:500;line-height:1.4}
    .metric .value{margin-top:4px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .metric .detail{margin-top:4px;color:var(--muted);font-size:.75rem;line-height:1.45;overflow-wrap:anywhere}

    /* one panel per competency */
    .grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;margin-bottom:16px}
    .grid > .card{min-width:0;margin-bottom:0}
    .competency-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
    .competency-head > div{min-width:0;flex:1 1 auto}
    .competency h2{margin:0;font-size:.9375rem;font-weight:600;line-height:1.4}
    .description{margin-top:4px;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .score-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-top:16px}
    .mini{min-width:0;padding:10px 12px;background:var(--surface3);border-radius:var(--radius-sm)}
    .mini .n{font-size:1rem;font-weight:600;line-height:1.3;font-variant-numeric:tabular-nums}
    .mini .l{margin-top:2px;color:var(--muted);font-size:.75rem;line-height:1.35}
    .bar{height:6px;margin-top:14px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .fill{height:100%;max-width:100%;border-radius:inherit;background:var(--accent)}
    .meta{display:flex;justify-content:space-between;flex-wrap:wrap;gap:4px 12px;margin-top:8px;color:var(--muted);font-size:.75rem;font-variant-numeric:tabular-nums}
    .sources{display:flex;gap:6px;flex-wrap:wrap;margin-top:12px}
    .source{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:500;line-height:1.4;font-variant-numeric:tabular-nums}

    /* level labels */
    .level{flex-shrink:0;display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .level.expert{color:#dbeafe;border-color:var(--ds-accent);background:var(--ds-accent-soft)}
    .level.advanced{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .level.competent{color:var(--ds-accent-text);border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
    .level.developing{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .level.beginner{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}
    .level.not-assessed{color:var(--muted)}

    .trend-wrap{margin-top:16px;padding-top:12px;border-top:1px solid var(--border)}
    .trend-title{display:flex;justify-content:space-between;gap:12px;color:var(--muted);font-size:.75rem;font-variant-numeric:tabular-nums}
    .trend{height:52px;display:flex;align-items:flex-end;gap:4px;margin-top:8px}
    .trend span{flex:1;max-width:30px;min-width:8px;min-height:3px;border-radius:2px 2px 0 0;background:var(--accent)}
    .trend-empty{padding:12px 0 0;color:var(--muted);font-size:.75rem}

    /* comparative matrix */
    .table-wrap{overflow:auto;border:1px solid var(--border);border-radius:var(--radius-sm)}
    .table-wrap table{width:100%;min-width:780px;border-collapse:collapse}
    .table-wrap th,.table-wrap td{text-align:left;border-bottom:1px solid var(--border);vertical-align:middle}
    .table-wrap th{padding:10px 14px;background:var(--surface3);color:var(--muted);font-size:.75rem;font-weight:600;white-space:nowrap}
    .table-wrap td{padding:12px 14px;color:var(--ds-text-secondary);font-size:.875rem;font-variant-numeric:tabular-nums}
    .table-wrap td strong{color:var(--text);font-weight:600}
    .table-wrap tbody tr:last-child td{border-bottom:0}
    .table-wrap tbody tr:hover td{background:rgba(255,255,255,.02)}
    .delta.good{color:var(--ds-success-text)}
    .delta.bad{color:var(--ds-danger-text)}
    .muted{color:var(--muted)}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-warning-border);border-radius:var(--radius-sm);
      background:var(--ds-warning-soft);color:#fef3c7;font-size:.875rem;line-height:1.55}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.55;text-align:center}

    @media(max-width:1100px){
      .hero-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .top > .btn{width:100%}
      .card{padding:16px}
      .form-row .select{min-width:0;width:100%}
      .form-row .btn{width:100%}
      .score-row{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:360px){.hero-grid{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,.select{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'My Skills Competencies', 'pageDescription' => 'See which data science skills you have shown and what to practise next.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <header class="top">
      <div>
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
        <div class="metric primary"><div class="label">Overall Competency</div><div class="value">{{ number_format($studentRow['overall_percentage'], 1) }}%</div><div class="detail">{{ $studentRow['overall_level'] }}, {{ $studentRow['evidence_count'] }} total evidence records</div></div>
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
        <h2 style="font-size:1rem;margin:0 0 4px">Comparative Matrix</h2>
        <p class="muted" style="font-size:.8125rem;margin:0 0 16px">A compact comparison of your competency level against the class average and performance range.</p>
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
