<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $challenge->title }} Result — DataSensei</title>
<style>
    /* Result of a multiple-choice challenge attempt. Colours, type and radius
       come from partials.design-system. */
    :root {
      --good:    var(--ds-success);
      --warning: var(--ds-warning);
      --danger:  var(--ds-danger);
    }

    *, *::before, *::after { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    a { color: inherit; text-decoration: none; }
    .result-layout { display: flex; min-height: 100vh; }
    .result-main { flex: 1; min-width: 0; padding: 28px 32px 48px; }
    .result-wrap { width: 100%; max-width: 1180px; margin: 0 auto; }

    /* ── page header ──────────────────────────────────────────────── */
    .result-header { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px; margin-bottom: 24px; }
    .result-header > div:first-child { min-width: 0; flex: 1 1 320px; }
    .result-title { margin: 0; overflow-wrap: anywhere; }
    .result-subtitle { margin: 4px 0 0; max-width: 72ch; color: var(--muted); font-size: .875rem; line-height: 1.5; }
    .result-actions { display: flex; flex-wrap: wrap; gap: 8px; }
    .result-btn {
      min-height: 38px; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2); color: var(--text);
      font-size: .875rem; font-weight: 500; line-height: 1.2; white-space: nowrap;
      transition: background .12s ease, border-color .12s ease;
    }
    .result-btn:hover { background: var(--ds-surface-hover); }
    .result-btn.primary { border-color: var(--accent); background: var(--accent); color: #fff; }
    .result-btn.primary:hover { border-color: var(--accent-hover); background: var(--accent-hover); }

    .result-notice {
      margin-bottom: 16px; padding: 12px 16px;
      border: 1px solid var(--ds-success-border); border-radius: var(--radius-sm); background: var(--ds-success-soft);
      color: #d1fae5; font-size: .875rem; line-height: 1.55;
    }

    /* ── summary figures ──────────────────────────────────────────── */
    .result-summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 24px; }
    .result-stat { padding: 16px 18px; display: flex; flex-direction: column; gap: 4px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
    .result-stat-label { color: var(--muted); font-size: .8125rem; font-weight: 500; }
    .result-stat-value { margin-top: 2px; font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .result-stat-note { color: var(--muted); font-size: .75rem; line-height: 1.4; }
    .result-stat.pass .result-stat-value { color: var(--ds-success-text); }
    .result-stat.fail .result-stat-value { color: var(--ds-danger-text); }

    /* ── panels ───────────────────────────────────────────────────── */
    .result-panel { margin-bottom: 20px; overflow: hidden; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface); }
    .result-panel-head { padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .result-panel-head h2 { margin: 0; font-size: .9375rem; font-weight: 600; line-height: 1.35; }
    .result-panel-head p { margin: 2px 0 0; color: var(--muted); font-size: .8125rem; line-height: 1.5; }

    .result-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .result-table { width: 100%; min-width: 680px; border-collapse: collapse; }
    .result-table th, .result-table td { border-bottom: 1px solid var(--border); text-align: left; vertical-align: middle; }
    .result-table th { padding: 10px 14px; background: var(--surface3); color: var(--muted); font-size: .75rem; font-weight: 600; white-space: nowrap; }
    .result-table td { padding: 12px 14px; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.5; font-variant-numeric: tabular-nums; }
    .result-table tbody tr:last-child td { border-bottom: 0; }
    .result-table tbody tr:hover td { background: rgba(255, 255, 255, .02); }
    .result-table tbody tr.current td { background: var(--ds-accent-soft); }
    .result-table tbody tr.current td:first-child { box-shadow: inset 3px 0 0 var(--accent); }
    .result-table strong { color: var(--text); font-weight: 600; }
    .result-link { color: var(--ds-accent-text); font-weight: 500; }
    .result-link:hover { color: var(--text); text-decoration: underline; }

    .result-badge {
      display: inline-flex; align-items: center; padding: 2px 8px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2);
      color: var(--ds-text-secondary); font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; font-variant-numeric: tabular-nums;
    }
    .result-badge.good { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .result-badge.bad { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

    /* ── answer review: rows inside the panel ─────────────────────── */
    .result-questions { display: grid; }
    .result-question { padding: 16px 20px; border-top: 1px solid var(--border); color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .result-question:first-child { border-top: 0; }
    .result-question-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
    .result-question-head .result-badge { flex-shrink: 0; }
    .result-question h3 { min-width: 0; flex: 1 1 auto; margin: 0; color: var(--text); font-size: .875rem; font-weight: 600; line-height: 1.5; overflow-wrap: anywhere; }
    .result-answer { margin-top: 8px; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; overflow-wrap: anywhere; }
    .result-answer strong { color: var(--muted); font-weight: 500; }
    .result-answer.correct { color: var(--ds-success-text); }
    .result-answer.expected { color: var(--ds-accent-text); }

    @media (max-width: 1100px) { .result-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 900px) { .result-main { padding: 24px 20px 40px; } }
    @media (max-width: 640px) {
      .result-main { padding: 20px 16px 32px; }
      .result-header { align-items: flex-start; flex-direction: column; }
      .result-header > div:first-child { flex-basis: auto; }
      .result-actions { width: 100%; }
      .result-actions .result-btn { flex: 1 1 auto; }
      .result-panel-head, .result-question { padding-left: 16px; padding-right: 16px; }
      .result-question-head { flex-wrap: wrap; gap: 8px; }
    }
    @media (max-width: 420px) { .result-summary { grid-template-columns: minmax(0, 1fr); } }
    @media (prefers-reduced-motion: reduce) { .result-btn { transition: none; } }
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageDescription' => 'Practise data science challenges and track your mastery.'])
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
          <h1 class="result-title ds-page-title">{{ $challenge->title }}</h1>
          <p class="result-subtitle">
            Attempt #{{ $attempt->attempt_no }}, {{ $attempt->is_ranked ? 'Ranked attempt' : 'Practice attempt' }}
          </p>
        </div>

        <div class="result-actions">
          <a class="result-btn" href="{{ route('challenges.map', $slug) }}">Back to Challenge Map</a>
          @if($challenge->is_active)
          <a class="result-btn primary" href="{{ route('challenges.quiz', ['slug' => $slug, 'challenge' => $challenge->id]) }}">Retake Challenge</a>
          @endif
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
                  <td><span class="result-badge {{ $historyPercent >= 70 ? 'good' : 'bad' }}">{{ $historyAttempt->score }}/{{ $historyAttempt->total_questions }}, {{ $historyPercent }}%</span></td>
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
