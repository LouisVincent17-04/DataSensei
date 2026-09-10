<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $assessment->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px}*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;padding:28px;min-width:0}.wrap{max-width:1480px;margin:0 auto}.top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.title{font-size:2rem;font-weight:900;margin:0}.subtitle{color:var(--muted);line-height:1.6;margin-top:8px}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;margin-bottom:16px}.grid{display:grid;gap:14px}.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}.btn.good{background:var(--good)}.actions{display:flex;gap:9px;flex-wrap:wrap}.muted{color:var(--muted)}.alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10);color:#a7f3d0}.alert.error{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.10);color:#fecaca}.metric{background:var(--surface2);border:1px solid var(--border);border-radius:13px;padding:14px}.metric strong{font-size:1.55rem;display:block}.availability-note{margin:14px 0 0;padding:12px 14px;border-radius:10px;background:var(--surface3);border:1px solid var(--border);color:var(--muted)}@media(max-width:1000px){.grid-2,.grid-3{grid-template-columns:1fr}.main{padding:18px}.top{flex-direction:column}}
  </style>
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
