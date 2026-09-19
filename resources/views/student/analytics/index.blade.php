<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Analytics — DataSensei</title>
<style>
    /* Student analytics. Colours, type and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;background:var(--bg)}

    /* ── title bar ─────────────────────────────────────────────── */
    .topbar{min-height:60px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px 16px;
      background:var(--bg);border-bottom:1px solid var(--border)}
    .topbar-link{display:inline-flex;align-items:center;justify-content:center;min-height:32px;padding:0 12px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-size:.8125rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;
      transition:background .12s ease}
    .topbar-link:hover{background:var(--ds-surface-hover)}

    .content{padding:28px 32px 48px;width:100%}

    /* ── summary tiles and panels ──────────────────────────────── */
    .grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
    .card{min-width:0;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px 18px}
    .label{color:var(--muted);font-size:.8125rem;font-weight:500;line-height:1.4}
    .metric{margin-top:4px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .small{margin:4px 0 0;color:var(--muted);font-size:.75rem;line-height:1.45}

    .bar{height:6px;margin-top:12px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .fill{height:100%;max-width:100%;border-radius:inherit;background:var(--accent)}

    .rank-card{grid-column:span 2;display:flex;flex-direction:column}
    .rank-name{margin-top:4px;font-size:1.25rem;font-weight:700;line-height:1.25;letter-spacing:-.02em}
    .rank-xp{margin-top:4px;color:var(--ds-text-secondary);font-size:.8125rem;font-variant-numeric:tabular-nums}
    /* the text block takes the slack so the bar sits at the bottom */
    .rank-next{flex:1;margin-top:2px;color:var(--muted);font-size:.75rem;line-height:1.45}

    .wide{grid-column:span 2}
    .full{grid-column:1/-1}

    /* Panels that hold a chart or a list carry a real title. */
    .full > .label,.wide > .label{color:var(--text);font-size:.9375rem;font-weight:600}
    .wide{padding:16px 0 4px}
    .wide > .label{padding:0 18px 12px;border-bottom:1px solid var(--border)}

    .activity{display:grid;grid-template-columns:repeat(14,minmax(0,1fr));gap:6px;align-items:end;height:100px;margin-top:16px}
    .day{min-height:4px;border-radius:2px 2px 0 0;background:var(--accent)}

    /* list rows separated by rules, not nested cards */
    .list{display:flex;flex-direction:column}
    .list > .small{padding:14px 18px;font-size:.875rem;margin:0}
    .item{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid var(--border)}
    .list > .item:first-child{border-top:0}
    .item > div{min-width:0;flex:1 1 auto}
    .item strong{display:block;font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .item .small{margin-top:2px}
    .item-value{flex-shrink:0;font-size:.875rem;font-weight:600;white-space:nowrap;font-variant-numeric:tabular-nums}
    .item > span:not(.item-value){min-width:0;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}

    @media(max-width:1100px){
      .grid{grid-template-columns:repeat(2,minmax(0,1fr))}
      .rank-card,.wide{grid-column:span 2}
    }
    @media(max-width:900px){
      .topbar{min-height:56px;padding:0 20px}
      .content{padding:24px 20px 40px}
    }
    @media(max-width:640px){
      .topbar{padding:0 16px}
      .content{padding:20px 16px 32px}
      .card{padding:16px}
      .wide{padding:16px 0 4px}
      .wide > .label{padding:0 16px 12px}
      .item,.list > .small{padding-left:16px;padding-right:16px}
      .activity{gap:4px}
    }
    @media(max-width:520px){
      .grid{grid-template-columns:minmax(0,1fr)}
      .rank-card,.wide,.full{grid-column:span 1}
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
      <h1 class="ds-page-title">Analytics</h1>
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