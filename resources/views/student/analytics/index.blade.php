<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Analytics — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8aa0bd;--dim:#4b6080;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px;--radius-sm:10px}*{box-sizing:border-box}body{margin:0;background:radial-gradient(circle at top right,rgba(59,130,246,.13),transparent 38%),var(--bg);color:var(--text);font-family:Inter,Arial,sans-serif}.layout{display:flex;min-height:100vh}.content{flex:1;padding:32px;overflow:auto}.hero{display:flex;justify-content:space-between;gap:18px;align-items:flex-start;margin-bottom:22px}.eyebrow{font-size:.75rem;font-weight:800;text-transform:uppercase;letter-spacing:.1em;color:var(--accent)}h1{font-size:2rem;margin:5px 0 8px}.muted{color:var(--muted);line-height:1.6}.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}.card{background:linear-gradient(180deg,rgba(255,255,255,.025),transparent),var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;box-shadow:0 20px 45px rgba(0,0,0,.18)}.metric{font-size:1.9rem;font-weight:900;margin-top:4px}.label{font-size:.78rem;color:var(--muted);font-weight:700;text-transform:uppercase;letter-spacing:.06em}.bar{height:9px;background:var(--surface2);border:1px solid var(--border);border-radius:999px;overflow:hidden;margin-top:12px}.fill{height:100%;background:linear-gradient(90deg,var(--accent),var(--good));border-radius:999px}.rank-card{grid-column:span 2;background:linear-gradient(135deg,rgba(59,130,246,.18),rgba(16,185,129,.08)),var(--surface);position:relative;overflow:hidden}.rank-name{font-size:1.8rem;font-weight:900}.pill{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--border);background:var(--surface2);border-radius:999px;padding:6px 10px;color:var(--muted);font-size:.78rem;font-weight:800}.section{margin-top:18px}.wide{grid-column:span 2}.full{grid-column:1/-1}.activity{display:grid;grid-template-columns:repeat(14,1fr);gap:6px;align-items:end;height:110px;margin-top:16px}.day{background:linear-gradient(180deg,var(--accent),rgba(59,130,246,.25));border-radius:8px 8px 3px 3px;min-height:8px}.list{display:flex;flex-direction:column;gap:10px;margin-top:12px}.item{display:flex;justify-content:space-between;gap:12px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:12px}.item strong{font-size:.9rem}.small{font-size:.8rem;color:var(--muted)}@media(max-width:1050px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}.rank-card,.wide{grid-column:span 2}}@media(max-width:760px){.layout{display:block}.content{padding:22px}.grid{grid-template-columns:1fr}.rank-card,.wide,.full{grid-column:span 1}.hero{display:block}}
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="content">
    <div class="hero">
      <div>
        <div class="eyebrow">Learning Analytics</div>
        <h1>Your Progress Overview</h1>
        <p class="muted">A focused summary of your rank, learning progress, challenge performance, assignments, statistical toolkit usage, and mastery signals.</p>
      </div>
      <a class="pill" href="{{ route('student.leaderboard.index') }}">View Leaderboard</a>
    </div>

    <div class="grid">
      <section class="card rank-card">
        <div class="label">Current Rank</div>
        <div class="rank-name">{{ $analytics['rank']['current']->rank_name ?? 'Unranked' }}</div>
        <p class="muted">
          {{ number_format($analytics['summary']['xp']) }} XP
          @if($analytics['rank']['next'])
            · {{ number_format($analytics['rank']['xp_to_next']) }} XP to {{ $analytics['rank']['next']->rank_name }}
          @else
            · Highest rank reached
          @endif
        </p>
        <div class="bar"><div class="fill" style="width: {{ $analytics['rank']['progress_percent'] }}%"></div></div>
      </section>

      <section class="card">
        <div class="label">Engagement</div>
        <div class="metric">{{ $analytics['summary']['engagement_score'] }}%</div>
        <div class="bar"><div class="fill" style="width: {{ $analytics['summary']['engagement_score'] }}%"></div></div>
      </section>

      <section class="card">
        <div class="label">Streak</div>
        <div class="metric">{{ number_format($analytics['summary']['streak']) }}</div>
        <p class="small">active learning streak</p>
      </section>

      <section class="card">
        <div class="label">Modules</div>
        <div class="metric">{{ $analytics['modules']['percent'] }}%</div>
        <p class="small">{{ $analytics['modules']['completed'] }} of {{ $analytics['modules']['total'] }} completed</p>
      </section>

      <section class="card">
        <div class="label">Lessons</div>
        <div class="metric">{{ $analytics['lessons']['percent'] }}%</div>
        <p class="small">{{ $analytics['lessons']['completed'] }} of {{ $analytics['lessons']['total'] }} completed</p>
      </section>

      <section class="card">
        <div class="label">MCQ Average</div>
        <div class="metric">{{ $analytics['challenges']['average_score'] }}</div>
        <p class="small">{{ $analytics['challenges']['completed'] }} attempts completed</p>
      </section>

      <section class="card">
        <div class="label">Coding Pass Rate</div>
        <div class="metric">{{ $analytics['coding']['pass_rate'] }}%</div>
        <p class="small">{{ $analytics['coding']['passed'] }} of {{ $analytics['coding']['submissions'] }} passed</p>
      </section>

      <section class="card">
        <div class="label">Test Case Rate</div>
        <div class="metric">{{ $analytics['coding']['test_case_rate'] }}%</div>
        <p class="small">visible and hidden tests combined</p>
      </section>

      <section class="card">
        <div class="label">Assignments</div>
        <div class="metric">{{ $analytics['assignments']['average_score'] }}%</div>
        <p class="small">{{ $analytics['assignments']['submitted'] }} submitted · {{ $analytics['assignments']['late'] }} late</p>
      </section>

      <section class="card">
        <div class="label">Achievements</div>
        <div class="metric">{{ $analytics['achievements']['percent'] }}%</div>
        <p class="small">{{ $analytics['achievements']['unlocked'] }} of {{ $analytics['achievements']['total'] }} unlocked</p>
      </section>

      <section class="card">
        <div class="label">Data Toolkit</div>
        <div class="metric">{{ number_format($analytics['data_toolkit']['analyses_run']) }}</div>
        <p class="small">
          {{ number_format($analytics['data_toolkit']['datasets_explored']) }} datasets explored
          @if($analytics['data_toolkit']['reports_generated'] > 0)
            · {{ number_format($analytics['data_toolkit']['reports_generated']) }} reports
          @endif
        </p>
      </section>

      <section class="card full">
        <div class="label">14-Day Activity</div>
        <div class="activity">
          @foreach($analytics['activity'] as $point)
            @php $height = max(8, min(100, 8 + ($point['total'] * 18))); @endphp
            <div class="day" title="{{ $point['day'] }}: {{ $point['total'] }} activities" style="height: {{ $height }}px"></div>
          @endforeach
        </div>
      </section>

      <section class="card wide">
        <div class="label">ILO Mastery</div>
        <div class="list">
          @forelse($analytics['ilo_mastery'] as $mastery)
            <div class="item">
              <div>
                <strong>{{ $mastery->ilo->title ?? 'Learning Outcome' }}</strong>
                <div class="small">{{ $mastery->status }}</div>
              </div>
              <strong>{{ number_format((float) $mastery->mastery_percent, 1) }}%</strong>
            </div>
          @empty
            <div class="small">No ILO mastery records yet.</div>
          @endforelse
        </div>
      </section>

      <section class="card wide">
        <div class="label">Recommended Next Steps</div>
        <div class="list">
          @foreach($analytics['recommendations'] as $recommendation)
            <div class="item"><span>{{ $recommendation }}</span></div>
          @endforeach
        </div>
      </section>
    </div>
  </main>
</div>
</body>
</html>
