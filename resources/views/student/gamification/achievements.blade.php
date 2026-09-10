<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Achievements — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{
      --bg:#080f1d;
      --bg2:#0b1425;
      --surface:#101b2e;
      --surface2:#142238;
      --surface3:#0d1728;
      --border:#1d314d;
      --border2:#284263;
      --text:#f8fafc;
      --muted:#8aa0bd;
      --dim:#50647f;
      --accent:#4f8cff;
      --cyan:#22d3ee;
      --green:#2dd4bf;
      --gold:#f5b84b;
      --violet:#a78bfa;
      --danger:#fb7185;
      --radius:18px;
      --radius-sm:12px;
      --shadow:0 24px 60px rgba(0,0,0,.30);
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
      min-height:100vh;
      color:var(--text);
      background:
        radial-gradient(circle at top left,rgba(79,140,255,.18),transparent 34%),
        radial-gradient(circle at top right,rgba(45,212,191,.10),transparent 30%),
        linear-gradient(180deg,var(--bg2),var(--bg));
    }
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:34px;overflow:auto}

    .header{
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:20px;
      flex-wrap:wrap;
      margin-bottom:24px;
    }
    .eyebrow{
      display:inline-flex;
      align-items:center;
      gap:10px;
      color:#9fc2ff;
      font-size:.76rem;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.14em;
    }
    .eyebrow::before{
      content:"";
      width:34px;
      height:2px;
      border-radius:999px;
      background:linear-gradient(90deg,var(--accent),var(--green));
      box-shadow:0 0 18px rgba(79,140,255,.45);
    }
    .title{font-size:2.1rem;font-weight:900;letter-spacing:-.045em;margin-top:7px}
    .subtitle{max-width:820px;color:var(--muted);margin-top:8px;line-height:1.65;font-size:.95rem}
    .leaderboard-link{
      color:white;
      text-decoration:none;
      background:linear-gradient(135deg,#2563eb,#0f766e);
      border:1px solid rgba(255,255,255,.12);
      padding:12px 16px;
      border-radius:13px;
      font-weight:900;
      box-shadow:0 16px 34px rgba(37,99,235,.20);
      transition:.2s ease;
    }
    .leaderboard-link:hover{transform:translateY(-1px);box-shadow:0 20px 44px rgba(37,99,235,.30)}

    .stats{display:grid;grid-template-columns:repeat(4,minmax(140px,1fr));gap:14px;margin-bottom:26px}
    .stat{
      position:relative;
      overflow:hidden;
      padding:17px 18px;
      background:linear-gradient(180deg,rgba(20,34,56,.92),rgba(13,23,40,.92));
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      box-shadow:var(--shadow);
    }
    .stat::after{
      content:"";
      position:absolute;
      inset:0 auto auto 0;
      width:100%;
      height:2px;
      background:linear-gradient(90deg,var(--accent),transparent);
      opacity:.75;
    }
    .stat .label{color:var(--muted);font-size:.78rem;font-weight:750;text-transform:uppercase;letter-spacing:.06em}
    .stat .value{font-size:1.55rem;font-weight:900;margin-top:7px;letter-spacing:-.03em}

    .section-title{
      display:flex;
      align-items:center;
      gap:12px;
      font-size:1.08rem;
      font-weight:900;
      margin:28px 0 14px;
      letter-spacing:-.02em;
    }
    .section-title::before{
      content:"";
      width:7px;
      height:24px;
      border-radius:999px;
      background:linear-gradient(180deg,var(--accent),var(--green));
    }

    .missions{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
    .mission{
      position:relative;
      overflow:hidden;
      padding:17px;
      background:rgba(16,27,46,.86);
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      box-shadow:0 16px 40px rgba(0,0,0,.18);
    }
    .mission::before{
      content:"";
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:2px;
      background:linear-gradient(90deg,var(--green),transparent);
      opacity:.8;
    }
    .mission-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
    .mission strong{font-size:.96rem;line-height:1.35}
    .mission-xp{color:var(--gold);font-weight:900;font-size:.82rem;white-space:nowrap}
    .desc{font-size:.86rem;color:var(--muted);line-height:1.58;margin-top:8px}
    .bar{height:8px;background:#07101e;border:1px solid rgba(255,255,255,.04);border-radius:999px;overflow:hidden;margin-top:13px}
    .bar span{display:block;height:100%;background:linear-gradient(90deg,var(--accent),var(--green));box-shadow:0 0 18px rgba(34,211,238,.35)}
    .meta{font-size:.76rem;color:var(--muted);margin-top:9px;font-weight:650}

    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(245px,1fr));gap:16px}
    .achievement-card{
      --card-accent:var(--accent);
      position:relative;
      min-height:172px;
      overflow:hidden;
      padding:18px;
      background:
        linear-gradient(180deg,rgba(20,34,56,.94),rgba(11,20,35,.94)),
        radial-gradient(circle at 20% 0%,rgba(255,255,255,.08),transparent 28%);
      border:1px solid var(--border);
      border-radius:var(--radius);
      box-shadow:0 18px 42px rgba(0,0,0,.22);
      transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease;
      isolation:isolate;
    }
    .achievement-card:hover{
      transform:translateY(-3px);
      border-color:color-mix(in srgb,var(--card-accent) 44%,var(--border));
      box-shadow:0 26px 64px rgba(0,0,0,.34);
    }
    .achievement-card::before{
      content:"";
      position:absolute;
      top:0;
      left:0;
      right:0;
      height:3px;
      background:linear-gradient(90deg,var(--card-accent),transparent 72%);
      opacity:.9;
    }
    .achievement-card::after{
      content:"";
      position:absolute;
      right:-45px;
      bottom:-45px;
      width:135px;
      height:135px;
      border-radius:38px;
      border:1px solid color-mix(in srgb,var(--card-accent) 36%,transparent);
      transform:rotate(28deg);
      opacity:.16;
      z-index:-1;
    }
    .achievement-card.locked{
      opacity:.72;
      filter:saturate(.78);
    }
    .achievement-card.locked::before{background:linear-gradient(90deg,#334861,transparent)}

    .tier-1{--card-accent:#60a5fa}
    .tier-2{--card-accent:#22d3ee}
    .tier-3{--card-accent:#2dd4bf}
    .tier-4{--card-accent:#a78bfa}
    .tier-5{--card-accent:#f5b84b}
    .tier-6{--card-accent:#fb7185}

    .achievement-top{display:flex;align-items:flex-start;justify-content:flex-end;gap:12px;margin-bottom:12px}
    .status-pill{
      padding:5px 9px;
      border-radius:999px;
      font-size:.66rem;
      font-weight:950;
      letter-spacing:.04em;
      text-transform:uppercase;
      border:1px solid rgba(255,255,255,.09);
    }
    .status-pill.unlocked{background:rgba(45,212,191,.13);color:#5eead4;border-color:rgba(45,212,191,.32)}
    .status-pill.locked{background:rgba(138,160,189,.08);color:#8aa0bd;border-color:#263a57}
    .name{font-weight:900;letter-spacing:-.02em;line-height:1.3}
    .reward{margin-top:14px;color:var(--gold);font-weight:950;font-size:.84rem;letter-spacing:.01em}
    .empty-card{
      grid-column:1/-1;
      padding:18px;
      background:rgba(16,27,46,.86);
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      color:var(--muted);
    }
    code{background:#07101e;border:1px solid var(--border);border-radius:7px;padding:2px 6px;color:#bfdbfe}

    @media(max-width:900px){.stats{grid-template-columns:repeat(2,1fr)}.main{padding:22px}}
    @media(max-width:700px){.layout{display:block}.stats{grid-template-columns:1fr}.title{font-size:1.72rem}.grid{grid-template-columns:1fr}}
  </style>
  @include('partials.admin-inspired-page-style')
</head>
<body class="ds-admin-inspired">
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <div class="header">
      <div>
        <div class="eyebrow"></div>
        <h1 class="title ds-page-title">Achievements & Missions</h1>
        <p class="subtitle">See what you have unlocked, check active missions, and follow your XP progress.</p>
      </div>
      <a href="{{ route('student.leaderboard.index') }}" class="leaderboard-link">View Leaderboard</a>
    </div>

    <section class="stats">
      <div class="stat"><div class="label">Unlocked</div><div class="value">{{ $stats['unlocked_count'] }}/{{ $stats['total_count'] }}</div></div>
      <div class="stat"><div class="label">XP</div><div class="value">{{ number_format($stats['xp']) }}</div></div>
      <div class="stat"><div class="label">Streak</div><div class="value">{{ number_format($stats['streak']) }} days</div></div>
      <div class="stat"><div class="label">Completion</div><div class="value">{{ $stats['total_count'] ? round(($stats['unlocked_count'] / $stats['total_count']) * 100) : 0 }}%</div></div>
    </section>

    <h2 class="section-title">Active Missions</h2>
    <section class="missions">
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
          <div class="meta">{{ $progress->progress_count }}/{{ $target }} · {{ ucfirst($mission->period_type) }} {{ $progress->is_completed ? '· Completed' : '' }}</div>
        </div>
      @empty
        <div class="mission">No active missions are available yet.</div>
      @endforelse
    </section>

    <h2 class="section-title">Achievement Milestones</h2>
    <section class="grid">
      @forelse($definitions as $definition)
        @php
          $record = $unlocked->get($definition->id);
          $tierClass = 'tier-' . ((($loop->iteration - 1) % 6) + 1);
        @endphp
        <article class="achievement-card {{ $tierClass }} {{ $record ? '' : 'locked' }}">
          <div class="achievement-top">
            <span class="status-pill {{ $record ? 'unlocked' : 'locked' }}">{{ $record ? 'Unlocked' : 'Locked' }}</span>
          </div>
          <div class="name">{{ $definition->name }}</div>
          <div class="desc">{{ $definition->description }}</div>
          <div class="reward">+{{ number_format($definition->xp_reward) }} XP</div>
          @if($record)
            <div class="meta">Unlocked {{ optional($record->unlocked_at)->diffForHumans() }}</div>
          @endif
        </article>
      @empty
        <div class="empty-card">No achievement milestones are available yet.</div>
      @endforelse
    </section>
  </main>
</div>
</body>
</html>
