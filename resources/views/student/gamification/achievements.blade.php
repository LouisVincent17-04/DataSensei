<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Achievements — DataSensei</title>
<style>
    /* Student achievements. Colours, type and radius come from partials.design-system. */
    :root{
      --green:var(--ds-success);
      --gold:var(--ds-warning);
      --danger:var(--ds-danger);
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:var(--ds-font-sans);
      min-height:100vh;
      color:var(--text);
      background:var(--bg);
    }
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;background:var(--bg)}

    /* ── title bar ───────────────────────────────────────────── */
    .topbar{
      min-height:60px;
      padding:0 32px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:12px 16px;
      background:var(--bg);
      border-bottom:1px solid var(--border);
    }
    .topbar-link{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      min-height:32px;
      padding:0 12px;
      border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-sm);
      background:var(--surface2);
      color:var(--text);
      font-size:.8125rem;
      font-weight:500;
      line-height:1.2;
      text-decoration:none;
      white-space:nowrap;
      transition:background .12s ease;
    }
    .topbar-link:hover{background:var(--ds-surface-hover)}

    .content{padding:28px 32px 48px;width:100%}

    /* ── summary ─────────────────────────────────────────────── */
    .stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:28px}
    .stat{
      min-width:0;
      padding:16px 18px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
    }
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500;line-height:1.4}
    .stat .value{margin-top:4px;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}

    .section-title{
      margin:0 0 12px;
      color:var(--text);
      font-size:1rem;
      font-weight:600;
      line-height:1.35;
    }
    .section{margin-bottom:28px}

    /* ── missions ────────────────────────────────────────────── */
    .missions{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(280px,100%),1fr));gap:12px}
    .mission{
      min-width:0;
      display:flex;
      flex-direction:column;
      padding:16px 18px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
    }
    .mission-head{display:flex;justify-content:space-between;gap:12px;align-items:baseline}
    .mission-head strong{min-width:0;flex:1 1 auto;font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .mission-xp{flex-shrink:0;color:var(--ds-warning-text);font-size:.8125rem;font-weight:600;white-space:nowrap;font-variant-numeric:tabular-nums}
    .desc{margin-top:4px;color:var(--muted);font-size:.8125rem;line-height:1.55}
    .mission .desc,.achievement-card .desc{flex:1}
    .bar{height:6px;margin-top:14px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .bar span{display:block;height:100%;max-width:100%;border-radius:inherit;background:var(--accent)}
    .mission .meta{
      display:flex;
      justify-content:space-between;
      gap:12px;
      margin-top:8px;
      color:var(--muted);
      font-size:.75rem;
    }
    .mission .meta .count{font-variant-numeric:tabular-nums}
    .mission .meta .done{color:var(--ds-success-text);font-weight:600}

    /* ── achievements ────────────────────────────────────────── */
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(245px,100%),1fr));gap:12px}
    .achievement-card{
      min-width:0;
      display:flex;
      flex-direction:column;
      padding:16px 18px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
    }
    /* locked entries read as quieter rather than badged */
    .achievement-card.locked{background:var(--surface3)}
    .achievement-card .name{font-size:.875rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .achievement-card .reward{margin-top:12px;color:var(--ds-warning-text);font-size:.8125rem;font-weight:600;font-variant-numeric:tabular-nums}
    .achievement-card .when{margin-top:4px;color:var(--muted);font-size:.75rem}
    .achievement-card.locked .name{color:var(--ds-text-secondary)}
    .achievement-card.locked .reward,
    .achievement-card.locked .when{color:var(--dim)}

    .empty-card{
      grid-column:1/-1;
      padding:32px 20px;
      background:var(--surface);
      border:1px solid var(--border);
      border-radius:var(--radius);
      color:var(--muted);
      font-size:.875rem;
      text-align:center;
    }

    @media(max-width:1100px){.stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:900px){
      .topbar{min-height:56px;padding:0 20px}
      .content{padding:24px 20px 40px}
    }
    @media(max-width:640px){
      .topbar{padding:0 16px}
      .content{padding:20px 16px 32px}
      .stat,.mission,.achievement-card{padding:16px}
    }
    @media(max-width:360px){.stats{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.topbar-link{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Achievements', 'pageDescription' => 'Achievements, streaks, and the class leaderboard.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <header class="topbar">
      <h1 class="ds-page-title">Achievements</h1>
      <a href="{{ route('student.leaderboard.index') }}" class="topbar-link">View leaderboard</a>
    </header>

    <div class="content">
      <section class="stats">
        <div class="stat"><div class="label">Unlocked</div><div class="value">{{ $stats['unlocked_count'] }}/{{ $stats['total_count'] }}</div></div>
        <div class="stat"><div class="label">XP</div><div class="value">{{ number_format($stats['xp']) }}</div></div>
        <div class="stat"><div class="label">Streak</div><div class="value">{{ number_format($stats['streak']) }} days</div></div>
        <div class="stat"><div class="label">Completion</div><div class="value">{{ $stats['total_count'] ? round(($stats['unlocked_count'] / $stats['total_count']) * 100) : 0 }}%</div></div>
      </section>

      <section class="section">
        <h2 class="section-title">Active missions</h2>
        <div class="missions">
          @forelse($missions as $mission)
            @php
              $progress = $mission->currentProgress;
              $target = max(1, (int) $mission->target_count);
              $percent = min(100, round(((int) $progress->progress_count / $target) * 100));
            @endphp
            <div class="mission">
              <div class="mission-head">
                <strong>{{ $mission->title }}</strong>
                <span class="mission-xp">+{{ number_format($mission->xp_reward) }} XP</span>
              </div>
              <p class="desc">{{ $mission->description }}</p>
              <div class="bar"><span style="width:{{ $percent }}%"></span></div>
              <div class="meta">
                <span class="count">{{ $progress->progress_count }} of {{ $target }}</span>
                @if($progress->is_completed)
                  <span class="done">Completed</span>
                @else
                  <span>{{ ucfirst($mission->period_type) }}</span>
                @endif
              </div>
            </div>
          @empty
            <div class="empty-card">No active missions are available yet.</div>
          @endforelse
        </div>
      </section>

      <section class="section">
        <h2 class="section-title">Achievement milestones</h2>
        <div class="grid">
          @forelse($definitions as $definition)
            @php
              $record = $unlocked->get($definition->id);
            @endphp
            <article class="achievement-card {{ $record ? '' : 'locked' }}">
              <div class="name">{{ $definition->name }}</div>
              <div class="desc">{{ $definition->description }}</div>
              <div class="reward">+{{ number_format($definition->xp_reward) }} XP</div>
              <div class="when">
                @if($record)
                  Unlocked {{ optional($record->unlocked_at)->diffForHumans() }}
                @else
                  Not unlocked yet
                @endif
              </div>
            </article>
          @empty
            <div class="empty-card">No achievement milestones are available yet.</div>
          @endforelse
        </div>
      </section>
    </div>
  </main>
</div>
</body>
</html>