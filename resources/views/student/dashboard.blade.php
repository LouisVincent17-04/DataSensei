<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Dashboard — DataSensei</title>
  <style>
    /* Layout and components for the student dashboard. Colours, type and
       radius come from partials.design-system. */
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;display:flex;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    .main{flex:1;min-width:0}

    /* ── title bar ─────────────────────────────────────────────── */
    .topbar{min-height:60px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:16px;
      background:var(--bg);border-bottom:1px solid var(--border)}
    .whoami{display:inline-flex;align-items:center;gap:8px;min-width:0;padding:4px 10px 4px 4px;border-radius:var(--radius-sm);
      color:var(--muted);font-size:.875rem;font-weight:500;text-decoration:none}
    .whoami:hover{color:var(--text);background:var(--surface2)}
    .whoami i{width:28px;height:28px;flex:0 0 28px;display:grid;place-items:center;font-style:normal;border-radius:var(--radius-sm);
      border:1px solid var(--ds-border-strong);background:var(--surface2);color:var(--text);font-size:.75rem;font-weight:600}

    .content{padding:28px 32px 48px;width:100%}
    .link{color:var(--ds-accent-text);text-decoration:none;font-size:.8125rem;font-weight:500;white-space:nowrap}
    .link:hover{text-decoration:underline}

    .flash{margin-bottom:20px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem}

    /* ── lead-in ───────────────────────────────────────────────── */
    .greet{display:flex;align-items:flex-end;justify-content:space-between;gap:16px 24px;flex-wrap:wrap;margin-bottom:24px}
    .greet h2{margin:0;font-size:1.125rem;font-weight:600;line-height:1.35;letter-spacing:-.01em}
    .greet h2 b{font-weight:600;color:inherit}
    .greet p{margin:4px 0 0;color:var(--muted);font-size:.875rem;line-height:1.5}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.small{min-height:32px;padding:0 12px;font-size:.8125rem}

    /* ── summary figures ───────────────────────────────────────── */
    .readout{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:24px}
    /* margin:0 matters: a <dl> carries a default block margin. */
    .cell{margin:0;padding:16px 18px;display:flex;flex-direction:column;gap:4px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .cell dt{margin:0;color:var(--muted);font-size:.8125rem;font-weight:500}
    .cell dd{margin:2px 0 0;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .cell small{color:var(--muted);font-size:.75rem;line-height:1.4}

    /* ── two columns; the side column follows the scroll ─────── */
    .grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(300px,30%);gap:20px;align-items:start}
    .stack{display:grid;gap:20px;min-width:0}
    .rail{position:sticky;top:20px}
    .card{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);overflow:hidden}
    .card-head{padding:14px 20px;display:flex;justify-content:space-between;align-items:center;gap:16px;border-bottom:1px solid var(--border)}
    .card-head h3{margin:0;font-size:.9375rem;font-weight:600}
    .card-body{padding:0;display:grid}

    /* list rows separated by rules */
    .row{display:block;padding:12px 20px;border-top:1px solid var(--border);color:inherit;text-decoration:none}
    .card-body > .row:first-child{border-top:0}
    a.row{transition:background .12s ease}
    a.row:hover{background:var(--surface2)}
    .item-title{font-size:.875rem;font-weight:600;line-height:1.4}
    .meta{margin-top:2px;color:var(--muted);font-size:.8125rem;line-height:1.45}
    .split{display:flex;justify-content:space-between;align-items:center;gap:16px}
    .split > :first-child{min-width:0;flex:1 1 auto}

    .tag{flex-shrink:0;color:var(--muted);font-size:.8125rem;font-weight:500;white-space:nowrap}
    .tag.green{color:var(--ds-success-text)}
    .tag.amber{color:var(--ds-warning-text)}
    .tag .num{font-variant-numeric:tabular-nums}

    .module{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:16px;align-items:center}
    .progress{height:6px;width:180px;max-width:100%;margin-top:8px;overflow:hidden;border-radius:999px;background:var(--surface2)}
    .progress span{display:block;height:100%;border-radius:inherit;background:var(--accent)}

    .board{display:grid}
    .rank{display:grid;grid-template-columns:24px minmax(0,1fr) auto;gap:10px;align-items:center;
      padding:10px 20px;border-top:1px solid var(--border);font-size:.875rem}
    .board .rank:first-child{border-top:0}
    .rank-no{color:var(--muted);font-size:.8125rem;font-weight:600;font-variant-numeric:tabular-nums}
    .rank-xp{color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
    .rank.you{background:var(--ds-accent-soft);box-shadow:inset 3px 0 0 var(--accent)}
    .rank.you .rank-xp{color:var(--text)}
    .you-mark{margin-left:6px;color:var(--ds-accent-text);font-size:.75rem;font-weight:500}
    .current-rank{padding:12px 20px;border-top:1px solid var(--border);color:var(--muted);font-size:.8125rem}

    .feed{position:relative;display:grid;padding:8px 20px 12px 38px}
    .feed::before{content:'';position:absolute;left:24px;top:20px;bottom:20px;width:1px;background:var(--border)}
    .event{position:relative;padding:8px 0}
    .event-top{display:flex;justify-content:space-between;align-items:baseline;gap:16px}
    .when{color:var(--muted);font-size:.75rem;white-space:nowrap}
    .event::before{content:'';position:absolute;left:-17px;top:15px;width:7px;height:7px;border-radius:50%;background:var(--accent)}

    .empty{padding:28px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1280px){
      .readout{grid-template-columns:repeat(3,minmax(0,1fr))}
    }
    @media(max-width:1100px){
      .grid{grid-template-columns:minmax(0,1fr)}
      .rail{position:static}
    }
    @media(max-width:900px){
      .topbar{min-height:56px;padding:0 20px}
      .content{padding:24px 20px 40px}
      .readout{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:640px){
      .content{padding:20px 16px 32px}
      .topbar{padding:0 16px}
      .greet{align-items:flex-start;flex-direction:column}
      .actions{width:100%}
      .actions .btn{flex:1 1 auto}
      .module{grid-template-columns:minmax(0,1fr)}
      .progress{width:100%}
      .module .btn,.module .tag{justify-self:start}
      .split{align-items:flex-start;flex-direction:column;gap:6px}
      .row,.rank,.current-rank{padding-left:16px;padding-right:16px}
      .card-head{padding:12px 16px}
    }
    @media(max-width:420px){.readout{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,a.row{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Student Dashboard', 'pageDescription' => 'Your learning dashboard: current modules, assignments, and progress.'])
</head>
<body>
  @include('partials.sidebar')
  <div class="main">
    <header class="topbar">
      <h1 class="ds-page-title">Dashboard</h1>
      <a class="whoami" href="{{ route('profile') }}"><i>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</i>{{ $user->name }}</a>
    </header>

    <main class="content">
      @if (session('success'))
        <div class="flash" role="status">{{ session('success') }}</div>
      @endif

      <section class="greet">
        <div>
          <h2>Welcome back, <b>{{ $user->name }}</b></h2>
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