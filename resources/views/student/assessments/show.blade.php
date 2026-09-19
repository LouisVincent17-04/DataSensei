<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $assessment->title }}</title>
<style>
    /* Student assessment overview. */
    /* Colours, type and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1480px;margin:0 auto}

    /* page header */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div{min-width:0;flex:1 1 320px}
    .top .ds-page-title{overflow-wrap:anywhere}
    .subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55;overflow-wrap:anywhere}

    .card{min-width:0;margin-bottom:16px;padding:20px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .card > p{margin:0 0 12px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.6;overflow-wrap:anywhere}
    .card > :last-child{margin-bottom:0}
    .card h3{margin:0 0 8px;font-size:.9375rem;font-weight:600;line-height:1.4;overflow-wrap:anywhere}
    .muted{color:var(--muted)}
    .card > p.muted{color:var(--muted);font-size:.8125rem}

    /* summary figures: label above value */
    .grid{display:grid;gap:12px}
    .grid-3{grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:16px}
    .metric{min-width:0;display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:16px 18px;
      background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .metric strong{display:block;font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
    .metric .muted{font-size:.8125rem;font-weight:500}

    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover);border-color:var(--ds-border-strong)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .actions form{margin:0}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.55}
    .alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}
    .btn.good{background:var(--accent);border-color:var(--accent)}
    .btn.good:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .availability-note{margin:16px 0 0;padding:12px 16px;border-radius:var(--radius-sm);background:var(--surface3);color:var(--muted);font-size:.875rem;line-height:1.55}

    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .card{padding:16px}
      .top > .btn{width:100%}
      .actions > .btn,.actions > form,.actions > form .btn{flex:1 1 auto;width:100%}
    }
    @media(max-width:560px){.grid-3{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn{transition:none}}
  </style>
    @include('partials.page-head', ['pageDescription' => 'Take assessments set by your instructor and review your results.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">{{ $assessment->title }}</h1>
          <p class="subtitle">{{ $assessment->instructions }}</p>
        </div>
        <a class="btn secondary" href="{{ route('student.assessments.index') }}">Back</a>
      </div>

      @if($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
      @endif

      <div class="grid grid-3">
        <div class="metric"><strong>{{ $assessment->total_items }}</strong><span class="muted">Items</span></div>
        <div class="metric"><strong>{{ $assessment->total_points }}</strong><span class="muted">Points</span></div>
        <div class="metric"><strong>{{ $assessment->time_limit_minutes ?: '—' }}</strong><span class="muted">Minutes</span></div>
      </div>

      <div class="card">
        <p>{{ $assessment->description }}</p>
        <p class="muted">Attempts used: {{ $completedAttempts }} of {{ $maxAttempts }}</p>

        <div class="actions">
          @if($latestSubmission && $latestSubmission->status !== 'in_progress')
            <a class="btn secondary" href="{{ route('student.assessments.result', [$assessment, $latestSubmission]) }}">View Latest Result</a>
          @endif

          @if($canContinueAttempt)
            <a class="btn good" href="{{ route('student.assessments.take', [$assessment, $latestSubmission]) }}">Continue Assessment</a>
          @elseif($canStartNewAttempt)
            <form method="POST" action="{{ route('student.assessments.start', $assessment) }}">
              @csrf
              <button class="btn {{ $latestSubmission ? '' : 'good' }}" type="submit">
                {{ $latestSubmission ? 'Start Another Attempt' : 'Start Assessment' }}
              </button>
            </form>
          @endif
        </div>

        @unless($canContinueAttempt || $canStartNewAttempt)
          <div class="availability-note">
            @if($assessment->status === 'closed')
              This assessment is closed. No new attempts can be started.
            @elseif($attemptsRemaining === 0)
              All {{ $maxAttempts }} allowed attempt(s) have been used.
            @else
              A new attempt is not available.
            @endif
          </div>
        @endunless
      </div>
    </div>
  </main>
</div>
</body>
</html>
