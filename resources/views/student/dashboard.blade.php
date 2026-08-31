<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DataSensei — Student Dashboard</title>
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--border:#1e2f47;--text:#fafafa;--muted:#8ca0bb;--accent:#3b82f6;--green:#10b981;--amber:#f59e0b;--red:#ef4444;--radius:10px}
    *{box-sizing:border-box}body{margin:0;min-height:100vh;display:flex;background:var(--bg);color:var(--text);font-family:Inter,ui-sans-serif,system-ui,-apple-system,sans-serif}.main{flex:1;min-width:0}.topbar{height:64px;padding:0 28px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}.topbar h1{font-size:1.05rem;margin:0}.profile-link,.link{color:var(--muted);text-decoration:none}.profile-link:hover,.link:hover{color:var(--text)}.content{padding:28px;display:grid;gap:22px}.welcome,.card,.stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}.welcome{padding:24px 26px;border-left:4px solid var(--accent);display:flex;justify-content:space-between;gap:20px;align-items:center}.welcome h2{margin:0 0 6px;font-size:1.45rem}.welcome p,.muted{color:var(--muted)}.welcome p{margin:0;line-height:1.5}.actions{display:flex;gap:10px;flex-wrap:wrap}.btn{display:inline-flex;align-items:center;justify-content:center;padding:9px 14px;border-radius:7px;border:1px solid var(--border);background:var(--surface2);color:var(--text);text-decoration:none;font-weight:650;font-size:.84rem}.btn.primary{background:var(--text);color:var(--bg)}.stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px}.stat{padding:18px}.stat .label{color:var(--muted);font-size:.78rem}.stat strong{display:block;font-size:1.65rem;margin-top:10px}.stat small{display:block;color:var(--muted);margin-top:5px}.grid{display:grid;grid-template-columns:minmax(0,1.45fr) minmax(300px,.8fr);gap:22px}.stack{display:grid;gap:22px}.card-head{padding:17px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;gap:16px;align-items:center}.card-head h3{font-size:.96rem;margin:0}.card-body{padding:18px 20px}.module-list,.activity-list,.challenge-list,.rank-list,.deadline-list{display:grid;gap:11px}.module,.challenge,.rank,.deadline,.activity{border:1px solid var(--border);background:var(--bg);border-radius:8px;padding:13px 14px}.module{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;align-items:center}.module-title,.item-title{font-weight:700;font-size:.88rem}.meta{font-size:.76rem;color:var(--muted);margin-top:4px;line-height:1.45}.progress{height:6px;width:110px;background:var(--surface2);border-radius:99px;overflow:hidden;margin-top:7px}.progress span{display:block;height:100%;background:var(--accent)}.challenge,.deadline{display:flex;justify-content:space-between;align-items:center;gap:14px;color:inherit;text-decoration:none}.challenge:hover,.deadline:hover{border-color:#35517a}.pill{display:inline-flex;padding:4px 7px;border-radius:999px;background:var(--surface2);color:var(--muted);font-size:.69rem;font-weight:700;text-transform:uppercase}.pill.green{color:#79e6bb}.pill.amber{color:#f8ca75}.rank{display:grid;grid-template-columns:26px 1fr auto;gap:10px;align-items:center}.rank.you{border-color:var(--accent)}.rank-no{font-weight:800;color:var(--muted)}.rank-xp{font-size:.78rem;color:var(--muted)}.activity{display:grid;grid-template-columns:auto 1fr;gap:11px}.dot{width:9px;height:9px;border-radius:50%;background:var(--accent);margin-top:5px}.empty{padding:18px;border:1px dashed var(--border);border-radius:8px;color:var(--muted);font-size:.84rem;text-align:center}.current-rank{margin-top:12px;color:var(--muted);font-size:.8rem}.flash{padding:12px 14px;border-radius:8px;background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.35);color:#a7f3d0}@media(max-width:1200px){.stats{grid-template-columns:repeat(3,1fr)}.grid{grid-template-columns:1fr}}@media(max-width:760px){.content{padding:18px}.topbar{padding:0 18px}.stats{grid-template-columns:1fr 1fr}.welcome{align-items:flex-start;flex-direction:column}}@media(max-width:480px){.stats{grid-template-columns:1fr}.challenge,.deadline{align-items:flex-start;flex-direction:column}.module{grid-template-columns:1fr}.progress{width:100%}}
  </style>
</head>
<body>
  @include('partials.sidebar')
  <div class="main">
    <header class="topbar">
      <h1 class="ds-page-title">Student Dashboard</h1>
      <a class="profile-link" href="{{ route('profile') }}">{{ $user->name }}</a>
    </header>

    <main class="content">
      @if (session('success'))
        <div class="flash" role="status">{{ session('success') }}</div>
      @endif

      <section class="welcome">
        <div>
          <h2>Welcome back, {{ $user->name }}.</h2>
          <p>Your dashboard now reflects saved coursework, challenge attempts, and tracked activity.</p>
        </div>
        <div class="actions">
          <a class="btn primary" href="{{ route('modules.index') }}">Continue learning</a>
          <a class="btn" href="{{ route('ide.index') }}">Open Python IDE</a>
          <a class="btn" href="{{ route('student.competencies.index') }}">Skills Competencies</a>
        </div>
      </section>

      <section class="stats" aria-label="Learning summary">
        <div class="stat"><span class="label">Modules completed</span><strong>{{ $stats['completed_modules'] }}</strong><small>of {{ $stats['total_modules'] }}</small></div>
        <div class="stat"><span class="label">Average score</span><strong>{{ $stats['average_score'] }}%</strong><small>graded and ranked work</small></div>
        <div class="stat"><span class="label">Challenges passed</span><strong>{{ $stats['passed_challenges'] }}</strong><small>ranked, at least 70%</small></div>
        <div class="stat"><span class="label">Tracked activity</span><strong>{{ $stats['tracked_minutes'] }}</strong><small>minutes in challenges and runs</small></div>
        <div class="stat"><span class="label">Experience</span><strong>{{ number_format((int) $user->xp) }}</strong><small>{{ (int) $user->streak }} day streak</small></div>
      </section>

      <section class="grid">
        <div class="stack">
          <article class="card">
            <div class="card-head"><h3>Learning modules</h3><a class="link" href="{{ route('modules.index') }}">View all →</a></div>
            <div class="card-body module-list">
              @forelse ($learningModules as $module)
                <div class="module">
                  <div>
                    <div class="module-title">{{ $module['title'] }}</div>
                    <div class="meta">{{ $module['completed_lessons'] }} of {{ $module['total_lessons'] }} lessons · {{ $module['is_completed'] ? 'Completed' : ($module['is_unlocked'] ? 'Available' : 'Locked') }}</div>
                    <div class="progress" aria-label="{{ $module['progress'] }} percent complete"><span style="width:{{ $module['progress'] }}%"></span></div>
                  </div>
                  @if ($module['is_unlocked'])
                    <a class="btn" href="{{ route('lesson.show', ['module' => $module['id']]) }}">Open</a>
                  @else
                    <span class="pill">Locked</span>
                  @endif
                </div>
              @empty
                <div class="empty">No curriculum modules are available yet.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Open challenges</h3><a class="link" href="{{ route('challenges') }}">All challenges →</a></div>
            <div class="card-body challenge-list">
              @forelse ($openChallenges as $challenge)
                @php
                  $challengeUrl = $challenge->is_coding_challenge
                    ? route('challenges.coding.quiz', ['slug' => $challenge->category->slug, 'challenge' => $challenge])
                    : route('challenges.quiz', ['slug' => $challenge->category->slug, 'challenge' => $challenge]);
                  $questionCount = $challenge->is_coding_challenge ? $challenge->coding_questions_count : $challenge->questions_count;
                @endphp
                <a class="challenge" href="{{ $challengeUrl }}">
                  <div><div class="item-title">{{ $challenge->title }}</div><div class="meta">{{ $challenge->category->name }} · {{ $questionCount }} {{ Str::plural('question', $questionCount) }}</div></div>
                  <span class="pill {{ $challenge->is_coding_challenge ? 'amber' : 'green' }}">{{ $challenge->is_coding_challenge ? 'Coding' : 'MCQ' }} · {{ $challenge->base_xp }} XP</span>
                </a>
              @empty
                <div class="empty">No unlocked challenges are pending.</div>
              @endforelse
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Recent activity</h3><a class="link" href="{{ route('student.analytics.index') }}">Analytics →</a></div>
            <div class="card-body activity-list">
              @forelse ($recentActivity as $activity)
                <div class="activity"><span class="dot"></span><div><div class="item-title">{{ $activity['type'] }} · {{ $activity['title'] }}</div><div class="meta">{{ $activity['detail'] }} · {{ $activity['at']->diffForHumans() }}</div></div></div>
              @empty
                <div class="empty">Completed work will appear here.</div>
              @endforelse
            </div>
          </article>
        </div>

        <div class="stack">
          <article class="card">
            <div class="card-head"><h3>Leaderboard</h3><a class="link" href="{{ route('student.leaderboard.index') }}">Full board →</a></div>
            <div class="card-body rank-list">
              @forelse ($leaderboard as $leader)
                <div class="rank {{ $leader->id === $user->id ? 'you' : '' }}">
                  <span class="rank-no">{{ $loop->iteration }}</span>
                  <span>{{ $leader->name }} @if ($leader->id === $user->id)<span class="pill green">You</span>@endif</span>
                  <span class="rank-xp">{{ number_format((int) $leader->xp) }} XP</span>
                </div>
              @empty
                <div class="empty">No ranked learners yet.</div>
              @endforelse
              <div class="current-rank">Your current XP rank: #{{ $currentRank }}</div>
            </div>
          </article>

          <article class="card">
            <div class="card-head"><h3>Upcoming deadlines</h3><a class="link" href="{{ route('student.assignments.index') }}">Coursework →</a></div>
            <div class="card-body deadline-list">
              @forelse ($upcomingDeadlines as $deadline)
                <a class="deadline" href="{{ $deadline['url'] }}">
                  <div><div class="item-title">{{ $deadline['title'] }}</div><div class="meta">{{ $deadline['type'] }}{{ $deadline['class_name'] ? ' · '.$deadline['class_name'] : '' }}</div></div>
                  <span class="pill amber">{{ $deadline['due_at']->format('M j, g:i A') }}</span>
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
