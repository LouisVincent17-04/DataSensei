<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Analytics — DataSensei</title>
<style>
    :root{
      --bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;
      --text:#fafafa;--muted:#8aa0bd;--dim:#4b6080;
      --accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;
      /* same corner scale as the challenges page */
      --radius:12px;--radius-sm:6px;
      --num:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);
      font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;-webkit-font-smoothing:antialiased}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;background:var(--bg)}

    /* a plain page bar, not a hero block */
    .topbar{height:54px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:16px;
      background:var(--surface);border-bottom:1px solid var(--border)}
    .topbar h1{font-size:.78rem;margin:0;font-weight:650;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
    .topbar-link{color:var(--muted);text-decoration:none;font-size:.79rem;font-weight:600;padding:7px 13px;
      border:1px solid var(--border);border-radius:var(--radius-sm);
      transition:color .12s,border-color .12s,background .12s}
    .topbar-link:hover{color:var(--text);border-color:#2c4168;background:var(--surface2)}

    .content{padding:26px 32px 56px;width:100%}

    .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px}
    .label{font-size:.72rem;color:var(--muted);font-weight:600}
    .metric{font-family:var(--num);font-size:1.3rem;font-weight:650;margin-top:7px;line-height:1;letter-spacing:-.02em}
    .small{font-size:.74rem;color:var(--muted);line-height:1.5;margin:7px 0 0}

    /* flat fill, no gradient */
    .bar{height:4px;background:var(--surface2);border-radius:999px;overflow:hidden;margin-top:12px}
    .fill{height:100%;background:var(--accent);border-radius:inherit}

    .rank-card{grid-column:span 2;display:flex;flex-direction:column}
    .rank-name{font-size:1.15rem;font-weight:680;letter-spacing:-.02em;margin-top:7px;line-height:1.2}
    .rank-xp{font-family:var(--num);font-size:.78rem;color:var(--muted);margin-top:7px}
    /* the text block takes the slack so the bar sits at the bottom, but it
       keeps a real gap when the card has no room to spare */
    .rank-next{font-size:.74rem;color:var(--muted);margin-top:4px;flex:1}
    .rank-card .bar{margin-top:14px}

    .wide{grid-column:span 2}
    .full{grid-column:1/-1}

    .activity{display:grid;grid-template-columns:repeat(14,1fr);gap:6px;align-items:end;height:92px;margin-top:14px}
    .day{background:var(--accent);border-radius:2px;min-height:4px}

    .list{display:flex;flex-direction:column;gap:6px;margin-top:12px}
    .item{display:flex;justify-content:space-between;gap:12px;align-items:center;
      background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-sm);padding:11px 13px}
    .item strong{font-size:.855rem;font-weight:650;line-height:1.35}
    .item .small{margin-top:3px}
    .item-value{font-family:var(--num);font-size:.83rem;font-weight:650;white-space:nowrap}
    .item span{font-size:.83rem;line-height:1.45}

    @media(max-width:1050px){
      .grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .rank-card,.wide{grid-column:span 2}
    }
    @media(max-width:760px){
      .layout{display:block}
      .topbar{padding:0 16px}
      .content{padding:20px 16px 36px}
      .grid{grid-template-columns:1fr}
      .rank-card,.wide,.full{grid-column:span 1}
      .activity{gap:4px}
    }
    @media(prefers-reduced-motion:reduce){.topbar-link{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Student Analytics', 'pageDescription' => 'Track your progress, activity, and results across DataSensei.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <header class="topbar">
      <h1 class="ds-page-title">My Progress</h1>
      <a class="topbar-link" href="{{ route('student.leaderboard.index') }}">View leaderboard</a>
    </header>

    <div class="content">
    <div class="grid">
      <section class="card rank-card">
        <div class="label">Current rank</div>
        <div class="rank-name">{{ $analytics['rank']['current']->rank_name ?? 'Unranked' }}</div>
        <div class="rank-xp">{{ number_format($analytics['summary']['xp']) }} XP</div>
        <div class="rank-next">
          @if($analytics['rank']['next'])
            {{ number_format($analytics['rank']['xp_to_next']) }} XP to reach {{ $analytics['rank']['next']->rank_name }}
          @else
            Highest rank reached
          @endif
        </div>
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
        <div class="label">MCQ average</div>
        <div class="metric">{{ $analytics['challenges']['average_score'] }}%</div>
        <p class="small">{{ $analytics['challenges']['completed'] }} attempts completed</p>
      </section>

      <section class="card">
        <div class="label">Coding pass rate</div>
        <div class="metric">{{ $analytics['coding']['pass_rate'] }}%</div>
        <p class="small">{{ $analytics['coding']['passed'] }} of {{ $analytics['coding']['submissions'] }} passed</p>
      </section>

      <section class="card">
        <div class="label">Test case rate</div>
        <div class="metric">{{ $analytics['coding']['test_case_rate'] }}%</div>
        <p class="small">visible and hidden tests combined</p>
      </section>

      <section class="card">
        <div class="label">Assignments</div>
        <div class="metric">{{ $analytics['assignments']['average_score'] }}%</div>
        <p class="small">{{ $analytics['assignments']['submitted'] }} submitted, {{ $analytics['assignments']['late'] }} late</p>
      </section>

      <section class="card">
        <div class="label">Assessments</div>
        <div class="metric">{{ $analytics['assessments']['average_score'] }}%</div>
        <p class="small">
          {{ $analytics['assessments']['submitted'] }} submitted{{ $analytics['assessments']['pending_review'] > 0 ? ', '.$analytics['assessments']['pending_review'].' awaiting review' : '' }}
        </p>
      </section>

      <section class="card">
        <div class="label">Achievements</div>
        <div class="metric">{{ $analytics['achievements']['percent'] }}%</div>
        <p class="small">{{ $analytics['achievements']['unlocked'] }} of {{ $analytics['achievements']['total'] }} unlocked</p>
      </section>

      <section class="card">
        <div class="label">Data toolkit</div>
        <div class="metric">{{ number_format($analytics['data_toolkit']['analyses_run']) }}</div>
        <p class="small">
          {{ number_format($analytics['data_toolkit']['datasets_explored']) }} datasets explored{{ $analytics['data_toolkit']['reports_generated'] > 0 ? ', '.number_format($analytics['data_toolkit']['reports_generated']).' reports' : '' }}
        </p>
      </section>

      <section class="card full">
        <div class="label">Activity over the last 14 days</div>
        <div class="activity">
          @foreach($analytics['activity'] as $point)
            @php $height = max(8, min(100, 8 + ($point['total'] * 18))); @endphp
            <div class="day" title="{{ $point['day'] }}: {{ $point['total'] }} activities" style="height: {{ $height }}px"></div>
          @endforeach
        </div>
      </section>

      <section class="card wide">
        <div class="label">ILO mastery</div>
        <div class="list">
          @forelse($analytics['ilo_mastery'] as $mastery)
            <div class="item">
              <div>
                <strong>{{ $mastery->ilo->title ?? 'Learning Outcome' }}</strong>
                <div class="small">{{ $mastery->status }}</div>
              </div>
              <span class="item-value">{{ number_format((float) $mastery->mastery_percent, 1) }}%</span>
            </div>
          @empty
            <div class="small">No ILO mastery records yet.</div>
          @endforelse
        </div>
      </section>

      <section class="card wide">
        <div class="label">Recommended next steps</div>
        <div class="list">
          @foreach($analytics['recommendations'] as $recommendation)
            <div class="item"><span>{{ $recommendation }}</span></div>
          @endforeach
        </div>
      </section>
    </div>
    </div>
  </main>
</div>
</body>
</html>
