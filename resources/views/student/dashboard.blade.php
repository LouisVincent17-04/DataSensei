<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Dashboard — DataSensei</title>
  <style>
    :root{
      --bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;
      --text:#fafafa;--muted:#8ca0bb;--accent:#3b82f6;--green:#10b981;--amber:#f59e0b;--red:#ef4444;
      --radius:8px;--radius-sm:6px;--radius-xs:4px;
      --num:'JetBrains Mono',ui-monospace,SFMono-Regular,Menlo,monospace;
    }
    *{box-sizing:border-box}
    /* Borderless: separation comes from tone and space, not lines. Corners use
       the same scale as the module library — 8px panels, 6px controls. */
    .main *,.main *::before,.main *::after{border:0}
    body{margin:0;min-height:100vh;display:flex;background:var(--bg);color:var(--text);
      font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif;-webkit-font-smoothing:antialiased}
    .main{flex:1;min-width:0;background:var(--bg)}

    /* ── top bar ───────────────────────────────────────────────── */
    .topbar{height:54px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;
      background:var(--surface)}
    .topbar h1{font-size:.78rem;margin:0;font-weight:650;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
    .whoami{display:inline-flex;align-items:center;gap:9px;color:var(--muted);text-decoration:none;
      font-size:.84rem;font-weight:600;padding:5px 10px 5px 5px;border-radius:var(--radius-sm)}
    .whoami:hover{color:var(--text);background:var(--surface2)}
    .whoami i{width:24px;height:24px;display:grid;place-items:center;font-style:normal;border-radius:var(--radius-xs);
      font-size:.72rem;font-weight:800;color:var(--text);background:var(--surface2)}

    /* fills the window: no max-width holding the layout back */
    .content{padding:28px 32px 56px;width:100%}
    .link{color:var(--muted);text-decoration:none;font-size:.79rem;font-weight:600}
    .link:hover{color:var(--text)}

    .flash{padding:12px 16px;background:rgba(16,185,129,.12);color:#a7f3d0;font-size:.86rem;margin-bottom:24px;
      border-radius:var(--radius-sm);box-shadow:inset 2px 0 0 var(--green)}

    /* ── greeting ──────────────────────────────────────────────── */
    .greet{display:flex;align-items:flex-end;justify-content:space-between;gap:28px;flex-wrap:wrap;margin-bottom:24px}
    .greet h2{margin:0;font-size:1.7rem;font-weight:680;letter-spacing:-.028em;line-height:1.15}
    .greet h2 b{font-weight:680;color:var(--accent)}
    .greet p{margin:8px 0 0;color:var(--muted);font-size:.87rem;line-height:1.5}
    .actions{display:flex;gap:8px;flex-wrap:wrap;padding-bottom:3px}
    .btn{display:inline-flex;align-items:center;justify-content:center;padding:9px 16px;
      background:var(--surface2);color:var(--text);text-decoration:none;font-weight:650;font-size:.82rem;
      border-radius:var(--radius-sm);transition:background .12s,color .12s}
    .btn:hover{background:#243350}
    .btn.primary{background:var(--text);color:var(--bg)}
    .btn.primary:hover{background:#e6ebf2}
    .btn.small{padding:7px 13px;font-size:.78rem}

    /* ── summary rail ──────────────────────────────────────────── */
    .readout{display:grid;grid-template-columns:minmax(190px,.8fr) repeat(4,minmax(0,1fr));gap:8px;
      margin-bottom:24px;background:var(--bg)}
    /* margin:0 matters — a <dl> carries a default 1em block margin, which would
       otherwise make these cells shorter than the row they sit in. */
    .cell{margin:0;padding:18px 20px;display:flex;flex-direction:column;justify-content:center;
      gap:6px;background:var(--surface);border-radius:var(--radius)}
    .cell dt{font-size:.72rem;color:var(--muted);font-weight:600;margin:0}
    .cell dd{margin:0;font-family:var(--num);font-size:1.3rem;font-weight:650;line-height:1;letter-spacing:-.02em}
    .cell small{color:var(--muted);font-size:.71rem;line-height:1.35}

    /* ── two columns; the rail on the right follows the scroll ─── */
    .grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(320px,26%);gap:20px;align-items:start}
    .stack{display:grid;gap:20px;min-width:0}
    .rail{position:sticky;top:20px}
    .card{background:var(--surface);border-radius:var(--radius)}
    .card-head{padding:16px 20px 12px;display:flex;justify-content:space-between;gap:16px;align-items:baseline}
    .card-head h3{font-size:.88rem;margin:0;font-weight:680}
    .card-body{padding:0 20px 20px;display:grid;gap:6px}

    /* rows read as bands, told apart by tone */
    .row{background:var(--bg);padding:13px 16px;color:inherit;text-decoration:none;display:block;
      border-radius:var(--radius-sm)}
    a.row{transition:background .12s}
    a.row:hover{background:#0a0f19}
    .item-title{font-weight:650;font-size:.865rem;line-height:1.35}
    .meta{font-size:.755rem;color:var(--muted);margin-top:3px;line-height:1.45}
    .split{display:flex;justify-content:space-between;align-items:center;gap:14px}

    .tag{font-size:.755rem;font-weight:650;color:var(--muted);white-space:nowrap}
    .tag.green{color:#79e6bb}
    .tag.amber{color:#f8ca75}
    .tag .num{font-family:var(--num);font-weight:600}

    .module{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:16px;align-items:center}
    .progress{height:3px;width:170px;background:var(--surface2);overflow:hidden;margin-top:9px;border-radius:999px}
    .progress span{display:block;height:100%;background:var(--accent);border-radius:inherit}

    .board{display:grid;gap:4px}
    .rank{display:grid;grid-template-columns:20px 1fr auto;gap:10px;align-items:center;
      padding:10px 16px;font-size:.85rem;background:var(--bg);border-radius:var(--radius-sm)}
    .rank-no{font-family:var(--num);font-weight:650;color:var(--muted);font-size:.8rem}
    .rank-xp{font-family:var(--num);font-size:.78rem;color:var(--muted)}
    .rank.you{background:rgba(59,130,246,.13);box-shadow:inset 2px 0 0 var(--accent)}
    .rank.you .rank-xp{color:var(--text)}
    .you-mark{color:var(--accent);font-size:.72rem;font-weight:650;margin-left:6px}
    .current-rank{margin-top:10px;color:var(--muted);font-size:.78rem;padding:0 16px}

    .feed{display:grid;position:relative;padding:4px 0 4px 18px}
    .feed::before{content:'';position:absolute;left:4px;top:12px;bottom:12px;width:1px;background:var(--surface2)}
    .event{padding:9px 0;position:relative}
    .event-top{display:flex;justify-content:space-between;align-items:baseline;gap:14px}
    .when{font-size:.72rem;color:var(--muted);white-space:nowrap;font-family:var(--num)}
    .event::before{content:'';position:absolute;left:-17px;top:15px;width:5px;height:5px;
      background:var(--accent);border-radius:50%}

    .empty{padding:26px 16px;background:var(--bg);color:var(--muted);font-size:.83rem;text-align:center;
      line-height:1.5;border-radius:var(--radius-sm)}

    @media(max-width:1180px){
      .grid{grid-template-columns:1fr}
      .rail{position:static}
      .readout{grid-template-columns:repeat(2,1fr)}
      .xp{grid-column:1 / -1}
    }
    @media(max-width:760px){
      .content{padding:22px 16px 40px}.topbar{padding:0 16px}
      .greet{align-items:flex-start;flex-direction:column;gap:16px}
      .greet h2{font-size:1.42rem}
      .module{grid-template-columns:1fr}.progress{width:100%}
      .module .tag{justify-self:start}
      .split{align-items:flex-start;flex-direction:column;gap:9px}
    }
    @media(max-width:480px){.readout{grid-template-columns:1fr}}
    @media(prefers-reduced-motion:reduce){.btn,a.row{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Student Dashboard', 'pageDescription' => 'Your learning dashboard: current modules, assignments, and progress.'])
</head>
<body>
  @include('partials.sidebar')
  <div class="main">
    <header class="topbar">
      <h1 class="ds-page-title">Student Dashboard</h1>
      <a class="whoami" href="{{ route('profile') }}"><i>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</i>{{ $user->name }}</a>
    </header>

    <main class="content">
      @if (session('success'))
        <div class="flash" role="status">{{ session('success') }}</div>
      @endif

      <section class="greet">
        <div>
          <h2>Welcome back, <b>{{ $user->name }}</b>.</h2>
          <p>Pick up where you left off, or start something new.</p>
        </div>
        <div class="actions">
          <a class="btn primary" href="{{ route('modules.index') }}">Continue learning</a>
          <a class="btn" href="{{ route('ide.index') }}">Open Python IDE</a>
          <a class="btn" href="{{ route('student.competencies.index') }}">Skills Competencies</a>
        </div>
      </section>

      <section class="readout" aria-label="Learning summary">
        <dl class="cell xp">
          <dt>Experience</dt>
          <dd>{{ number_format((int) $user->xp) }}</dd>
          <small>{{ (int) $user->streak }} {{ Str::plural('day', (int) $user->streak) }} in a row</small>
        </dl>
        <dl class="cell">
          <dt>Modules completed</dt>
          <dd>{{ $stats['completed_modules'] }}</dd>
          <small>of {{ $stats['total_modules'] }}</small>
        </dl>
        <dl class="cell">
          <dt>Average score</dt>
          <dd>{{ $stats['average_score'] }}%</dd>
          <small>graded and ranked work</small>
        </dl>
        <dl class="cell">
          <dt>Challenges passed</dt>
          <dd>{{ $stats['passed_challenges'] }}</dd>
          <small>ranked, at least 70%</small>
        </dl>
        <dl class="cell">
          <dt>Tracked activity</dt>
          <dd>{{ $stats['tracked_minutes'] }}</dd>
          <small>minutes in challenges and runs</small>
        </dl>
      </section>

      <section class="grid">
        <div class="stack">
          <article class="card">
            <div class="card-head"><h3>Learning modules</h3><a class="link" href="{{ route('modules.index') }}">View all</a></div>
            <div class="card-body">
              @forelse ($learningModules as $module)
                <div class="row module">
                  <div>
                    <div class="item-title">{{ $module['title'] }}</div>
                    <div class="meta">
                      @if ($module['is_completed'])
                        All {{ $module['total_lessons'] }} {{ Str::plural('lesson', $module['total_lessons']) }} completed
                      @elseif ($module['is_unlocked'])
                        {{ $module['completed_lessons'] }} of {{ $module['total_lessons'] }} {{ Str::plural('lesson', $module['total_lessons']) }} completed
                      @else
                        {{ $module['total_lessons'] }} {{ Str::plural('lesson', $module['total_lessons']) }} to unlock
                      @endif
                    </div>
                    @if ($module['is_unlocked'])
                      <div class="progress" aria-label="{{ $module['progress'] }} percent complete"><span style="width:{{ $module['progress'] }}%"></span></div>
                    @endif
                  </div>
                  @if ($module['is_unlocked'])
                    <a class="btn small" href="{{ route('lesson.show', ['module' => $module['id']]) }}">Open</a>
                  @else
                    <span class="tag">Locked</span>
                  @endif
                </div>
              @empty
                <div class="empty">No curriculum modules are available yet.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Open challenges</h3><a class="link" href="{{ route('challenges') }}">All challenges</a></div>
            <div class="card-body">
              @forelse ($openChallenges as $challenge)
                @php
                  $challengeUrl = $challenge->is_coding_challenge
                    ? route('challenges.coding.quiz', ['slug' => $challenge->category->slug, 'challenge' => $challenge])
                    : route('challenges.quiz', ['slug' => $challenge->category->slug, 'challenge' => $challenge]);
                  $questionCount = $challenge->is_coding_challenge ? $challenge->coding_questions_count : $challenge->questions_count;
                @endphp
                <a class="row split" href="{{ $challengeUrl }}">
                  <div>
                    <div class="item-title">{{ $challenge->title }}</div>
                    <div class="meta">{{ $challenge->is_coding_challenge ? 'Coding' : 'Multiple choice' }} challenge in {{ $challenge->category->name }}, {{ $questionCount }} {{ Str::plural('question', $questionCount) }}</div>
                  </div>
                  <span class="tag {{ $challenge->is_coding_challenge ? 'amber' : 'green' }}"><span class="num">{{ $challenge->base_xp }}</span> XP</span>
                </a>
              @empty
                <div class="empty">No unlocked challenges are pending.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Recent activity</h3><a class="link" href="{{ route('student.analytics.index') }}">Analytics</a></div>
            <div class="card-body">
              @if ($recentActivity->isEmpty())
                <div class="empty">Completed work will appear here.</div>
              @else
                <div class="feed">
                  @foreach ($recentActivity as $activity)
                    <div class="event">
                      <div class="event-top">
                        <span class="item-title">{{ $activity['title'] }}</span>
                        <span class="when">{{ $activity['at']->diffForHumans() }}</span>
                      </div>
                      <div class="meta">{{ $activity['type'] }}, {{ lcfirst($activity['detail']) }}</div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          </article>
        </div>

        <div class="stack rail">
          <article class="card">
            <div class="card-head"><h3>Leaderboard</h3><a class="link" href="{{ route('student.leaderboard.index') }}">Full board</a></div>
            <div class="card-body">
              @if ($leaderboard->isEmpty())
                <div class="empty">No ranked learners yet.</div>
              @else
                <div class="board">
                  @foreach ($leaderboard as $leader)
                    <div class="rank {{ $leader->id === $user->id ? 'you' : '' }}">
                      <span class="rank-no">{{ $loop->iteration }}</span>
                      <span>{{ $leader->name }} @if ($leader->id === $user->id)<span class="you-mark">you</span>@endif</span>
                      <span class="rank-xp">{{ number_format((int) $leader->xp) }} XP</span>
                    </div>
                  @endforeach
                </div>
              @endif
              <div class="current-rank">Your current XP rank: #{{ $currentRank }}</div>
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Upcoming deadlines</h3><a class="link" href="{{ route('student.assignments.index') }}">Coursework</a></div>
            <div class="card-body">
              @forelse ($upcomingDeadlines as $deadline)
                <a class="row split" href="{{ $deadline['url'] }}">
                  <div><div class="item-title">{{ $deadline['title'] }}</div><div class="meta">{{ $deadline['type'] }}{{ $deadline['class_name'] ? ' for '.$deadline['class_name'] : '' }}</div></div>
                  <span class="tag amber">{{ $deadline['due_at']->format('M j, g:i A') }}</span>
                </a>
              @empty
                <div class="empty">No published deadlines are upcoming.</div>
              @endforelse
            </div>
          </article>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
