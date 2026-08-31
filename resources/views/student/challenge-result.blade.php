<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $challenge->title }} Result — DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg:#0d1320;
      --surface:#111c2d;
      --surface2:#1a2638;
      --surface3:#0f1928;
      --border:#1e2f47;
      --border-hover:#2c4168;
      --accent:#3b82f6;
      --accent-hover:#2563eb;
      --good:#10b981;
      --warning:#f59e0b;
      --danger:#ef4444;
      --text:#fafafa;
      --muted:#7f93b0;
      --dim:#3d5272;
      --radius:8px;
      --radius-sm:6px;
    }

    *, *::before, *::after { box-sizing:border-box; }
    body { margin:0; min-height:100vh; background:var(--bg); color:var(--text); font-family:Inter,system-ui,sans-serif; }
    a { color:inherit; text-decoration:none; }
    .result-layout { display:flex; min-height:100vh; }
    .result-main { flex:1; min-width:0; padding:32px; overflow:auto; }
    .result-wrap { width:100%; max-width:1180px; margin:0 auto; }
    .result-header { display:flex; align-items:flex-start; justify-content:space-between; gap:20px; margin-bottom:24px; }
    .result-kicker { margin-bottom:6px; color:var(--accent); font-size:.7rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; }
    .result-title { margin:0; font-size:1.6rem; line-height:1.25; letter-spacing:-.025em; }
    .result-subtitle { margin:7px 0 0; color:var(--muted); font-size:.875rem; line-height:1.55; }
    .result-actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:10px; }
    .result-btn { min-height:38px; display:inline-flex; align-items:center; justify-content:center; padding:8px 14px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--text); font-size:.84rem; font-weight:600; }
    .result-btn:hover { border-color:var(--border-hover); }
    .result-btn.primary { border-color:var(--accent); background:var(--accent); color:white; }
    .result-btn.primary:hover { background:var(--accent-hover); }
    .result-notice { margin-bottom:16px; padding:12px 14px; border:1px solid rgba(16,185,129,.30); border-radius:var(--radius); background:rgba(16,185,129,.08); color:#a7f3d0; font-size:.875rem; line-height:1.55; }
    .result-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; margin-bottom:24px; }
    .result-stat { min-height:112px; padding:18px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface); }
    .result-stat-label { color:var(--muted); font-size:.75rem; font-weight:600; }
    .result-stat-value { margin-top:10px; font-size:1.55rem; font-weight:700; letter-spacing:-.025em; font-variant-numeric:tabular-nums; }
    .result-stat-note { margin-top:5px; color:var(--dim); font-size:.72rem; line-height:1.45; }
    .result-stat.pass { border-left:3px solid var(--good); }
    .result-stat.fail { border-left:3px solid var(--danger); }
    .result-panel { margin-bottom:24px; overflow:hidden; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface); }
    .result-panel-head { padding:18px 20px; border-bottom:1px solid var(--border); }
    .result-panel-head h2 { margin:0; font-size:1rem; font-weight:600; }
    .result-panel-head p { margin:5px 0 0; color:var(--muted); font-size:.78rem; line-height:1.5; }
    .result-table-wrap { overflow-x:auto; }
    .result-table { width:100%; border-collapse:collapse; }
    .result-table th, .result-table td { padding:13px 16px; border-bottom:1px solid var(--border); text-align:left; vertical-align:middle; }
    .result-table th { background:rgba(255,255,255,.018); color:var(--dim); font-size:.68rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; white-space:nowrap; }
    .result-table td { color:var(--muted); font-size:.82rem; line-height:1.5; }
    .result-table tbody tr:last-child td { border-bottom:0; }
    .result-table tbody tr.current { background:rgba(59,130,246,.06); }
    .result-table strong { color:var(--text); }
    .result-link { color:#bfdbfe; font-weight:600; }
    .result-badge { display:inline-flex; align-items:center; padding:4px 8px; border:1px solid var(--border); border-radius:999px; background:var(--surface2); color:var(--muted); font-size:.68rem; font-weight:600; }
    .result-badge.good { border-color:rgba(16,185,129,.35); background:rgba(16,185,129,.08); color:#a7f3d0; }
    .result-badge.bad { border-color:rgba(239,68,68,.35); background:rgba(239,68,68,.08); color:#fecaca; }
    .result-questions { display:grid; gap:12px; padding:16px; }
    .result-question { padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface3); }
    .result-question-head { display:flex; align-items:flex-start; justify-content:space-between; gap:14px; }
    .result-question h3 { margin:0; font-size:.9rem; font-weight:600; line-height:1.5; }
    .result-answer { margin-top:12px; color:var(--muted); font-size:.82rem; line-height:1.55; }
    .result-answer strong { color:var(--text); }
    .result-answer.correct { color:#a7f3d0; }
    .result-answer.expected { color:#bfdbfe; }

    @media (max-width:950px) { .result-summary { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:700px) {
      .result-layout { display:block; }
      .result-main { padding:18px; }
      .result-header { flex-direction:column; }
      .result-actions { width:100%; justify-content:flex-start; }
      .result-summary { grid-template-columns:1fr; }
    }
  </style>
  @include('partials.admin-inspired-page-style')
</head>
<body class="ds-admin-inspired">
@php
  $percentage = $attempt->total_questions > 0
      ? (int) round(($attempt->score / $attempt->total_questions) * 100)
      : 0;
  $passed = $percentage >= 70;
  $minutes = intdiv((int) ($attempt->time_taken_seconds ?? 0), 60);
  $seconds = (int) ($attempt->time_taken_seconds ?? 0) % 60;
@endphp

<div class="result-layout">
  @include('partials.sidebar')

  <main class="result-main">
    <div class="result-wrap">
      <header class="result-header">
        <div>
          <div class="result-kicker">Challenge Result</div>
          <h1 class="result-title ds-page-title">{{ $challenge->title }}</h1>
          <p class="result-subtitle">
            Attempt #{{ $attempt->attempt_no }} · {{ $attempt->is_ranked ? 'Ranked attempt' : 'Practice attempt' }}
          </p>
        </div>

        <div class="result-actions">
          <a class="result-btn" href="{{ route('challenges.map', $slug) }}">Back to Challenge Map</a>
          <a class="result-btn primary" href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $challenge->id]) }}">Retake Challenge</a>
        </div>
      </header>

      @if(session('success'))
        <div class="result-notice" role="status">{{ session('success') }}</div>
      @endif

      <section class="result-summary" aria-label="Attempt result summary">
        <article class="result-stat {{ $passed ? 'pass' : 'fail' }}">
          <div class="result-stat-label">Score</div>
          <div class="result-stat-value">{{ $attempt->score }}/{{ $attempt->total_questions }}</div>
          <div class="result-stat-note">{{ $passed ? 'Passed' : 'Needs 70% to pass' }}</div>
        </article>
        <article class="result-stat">
          <div class="result-stat-label">Percentage</div>
          <div class="result-stat-value">{{ $percentage }}%</div>
          <div class="result-stat-note">Correct answers in this attempt</div>
        </article>
        <article class="result-stat">
          <div class="result-stat-label">Time Used</div>
          <div class="result-stat-value">{{ sprintf('%02d:%02d', $minutes, $seconds) }}</div>
          <div class="result-stat-note">{{ $attempt->status === 'expired' ? 'Submitted when time ended' : 'Submitted before the deadline' }}</div>
        </article>
        <article class="result-stat">
          <div class="result-stat-label">XP Awarded</div>
          <div class="result-stat-value">{{ number_format($attempt->xp_awarded) }}</div>
          <div class="result-stat-note">{{ $attempt->is_ranked ? 'Leaderboard XP from this attempt' : 'Practice attempts do not award XP' }}</div>
        </article>
      </section>

      <section class="result-panel">
        <div class="result-panel-head">
          <h2>Attempt History</h2>
          <p>All completed attempts for this challenge. Select any attempt to review it.</p>
        </div>
        <div class="result-table-wrap">
          <table class="result-table">
            <thead>
              <tr>
                <th>Attempt</th>
                <th>Mode</th>
                <th>Score</th>
                <th>Time</th>
                <th>XP</th>
                <th>Finished</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @foreach($attemptHistory as $historyAttempt)
                @php
                  $historyPercent = $historyAttempt->total_questions > 0
                      ? (int) round(($historyAttempt->score / $historyAttempt->total_questions) * 100)
                      : 0;
                  $historyDuration = (int) ($historyAttempt->time_taken_seconds ?? 0);
                @endphp
                <tr class="{{ $historyAttempt->id === $attempt->id ? 'current' : '' }}">
                  <td><strong>#{{ $historyAttempt->attempt_no }}</strong></td>
                  <td>{{ $historyAttempt->is_ranked ? 'Ranked' : 'Practice' }}</td>
                  <td><span class="result-badge {{ $historyPercent >= 70 ? 'good' : 'bad' }}">{{ $historyAttempt->score }}/{{ $historyAttempt->total_questions }} · {{ $historyPercent }}%</span></td>
                  <td>{{ sprintf('%02d:%02d', intdiv($historyDuration, 60), $historyDuration % 60) }}</td>
                  <td>{{ number_format($historyAttempt->xp_awarded) }}</td>
                  <td>{{ optional($historyAttempt->submitted_at)->format('M d, Y h:i A') ?? 'Saved' }}</td>
                  <td>
                    @if($historyAttempt->id === $attempt->id)
                      <strong>Viewing</strong>
                    @else
                      <a class="result-link" href="{{ route('challenges.quiz.result', ['slug' => $slug, 'challenge' => $challenge->id, 'attempt' => $historyAttempt->id]) }}">View</a>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>

      <section class="result-panel">
        <div class="result-panel-head">
          <h2>Answer Review</h2>
          <p>Compare your saved answers with the correct answers before trying again.</p>
        </div>

        <div class="result-questions">
          @forelse($questionResults as $index => $row)
            <article class="result-question">
              <div class="result-question-head">
                <h3>{{ $index + 1 }}. {{ $row['question']->question_text }}</h3>
                <span class="result-badge {{ $row['is_correct'] ? 'good' : 'bad' }}">{{ $row['is_correct'] ? 'Correct' : 'Incorrect' }}</span>
              </div>
              <div class="result-answer {{ $row['is_correct'] ? 'correct' : '' }}">
                <strong>Your answer:</strong> {{ $row['selected_option']?->option_text ?? 'No answer submitted' }}
              </div>
              @if(! $row['is_correct'])
                <div class="result-answer expected">
                  <strong>Correct answer:</strong> {{ $row['correct_option']?->option_text ?? 'The correct option is no longer available.' }}
                </div>
              @endif
            </article>
          @empty
            <div class="result-question">Question details are no longer available, but the saved score and attempt history are preserved.</div>
          @endforelse
        </div>
      </section>
    </div>
  </main>
</div>
</body>
</html>
