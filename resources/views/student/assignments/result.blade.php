<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Result — DataSensei</title>
<style>
    /* Student assignment result. Colours, type and radius come from partials.design-system. */
    :root{--accent2:var(--ds-accent);--accent3:var(--ds-success);--warn:var(--ds-danger);--warn2:var(--ds-warning)}
    *{box-sizing:border-box;margin:0;padding:0}
    html,body{min-height:100%;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    a{color:inherit}
    .ds-shell{display:flex;min-height:100vh}
    .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1200px;margin:0 auto}

    /* page header */
    .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top-row > div:first-child{min-width:0;flex:1 1 320px}
    .top-row .ds-page-title{overflow-wrap:anywhere}
    .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55;overflow-wrap:anywhere}

    .card{border:1px solid var(--border);background:var(--surface);border-radius:var(--radius);overflow:hidden}
    .card-pad{padding:20px}
    .card h2{color:var(--text);font-size:1rem;font-weight:600;line-height:1.35}
    .muted{color:var(--muted)}
    .dim{color:var(--muted)}

    .btn{min-height:38px;display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font-family:var(--ds-font-sans);font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff}
    .btn.primary:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn.secondary{background:var(--surface2);border-color:var(--ds-border-strong);color:var(--text)}
    .btn.secondary:hover{background:var(--ds-surface-hover)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .actions form{display:contents}

    /* status labels */
    .badge-pill{display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;font-variant-numeric:tabular-nums}
    .badge-pill.good{color:var(--ds-success-text);border-color:var(--ds-success-border);background:var(--ds-success-soft)}
    .badge-pill.warn{color:var(--ds-warning-text);border-color:var(--ds-warning-border);background:var(--ds-warning-soft)}
    .badge-pill.danger{color:var(--ds-danger-text);border-color:var(--ds-danger-border);background:var(--ds-danger-soft)}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.55}
    .alert.success{background:var(--ds-success-soft);border-color:var(--ds-success-border);color:#d1fae5}
    .alert.danger{background:var(--ds-danger-soft);border-color:var(--ds-danger-border);color:#fee2e2}

    .score-big{font-size:1.75rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .score-big + .muted{display:flex;align-items:center;flex-wrap:wrap;gap:4px 8px;margin-top:6px;font-size:.875rem;font-variant-numeric:tabular-nums}

    /* answer review: rows separated by rules, not nested cards */
    .question-card{padding:16px 0;border-bottom:1px solid var(--border)}
    .question-card:first-of-type{padding-top:4px}
    .question-card:last-child{padding-bottom:0;border-bottom:0}
    .question-card p{font-size:.875rem;line-height:1.6;overflow-wrap:anywhere}
    .question-card p > strong{color:var(--text);font-weight:600}
    .question-card p.muted strong{color:var(--ds-text-secondary);font-weight:600}

    @media(max-width:900px){.ds-main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .card-pad{padding:16px}
      .top-row > .btn{width:100%}
      .actions > .btn,.actions > form > .btn{flex:1 1 auto}
    }
    @media(prefers-reduced-motion:reduce){.btn,.input,.option-row{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Assignment Result', 'pageDescription' => 'See the assignments your instructor set, submit work, and review feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">Attempt #{{ $submission->attempt_no }}. Submitted {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}</p>
          </div>
          <a href="{{ $resultBackRoute ?? route('student.assignments.index') }}" class="btn secondary">
            {{ $resultBackLabel ?? 'Back to Assignments' }}
          </a>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert danger">{{ session('error') }}</div> @endif

        <section class="card card-pad" style="margin-bottom:16px">
          <div class="score-big">{{ $submission->score }}/{{ $submission->total_points }}</div>
          @if($submission->isHeldForIntegrityReview())
            <div class="muted">{{ $submission->percentage }}%. <strong>Held for instructor review.</strong></div>
            <p class="muted" style="margin-top:8px">Your saved answers are stored below, but this attempt has no credit until your instructor reviews it. {{ $submission->integrity_reason }}</p>
          @else
            <div class="muted">{{ $submission->percentage }}% <span class="badge-pill {{ $submission->status === 'late' ? 'danger' : 'good' }}">{{ ucfirst($submission->status) }}</span></div>
            @if($submission->integrity_status === 'blocked' && $submission->integrity_reviewed_at)
              <p class="muted" style="margin-top:8px">Your instructor reviewed this attempt and kept it blocked, so it has no credit.</p>
            @endif
          @endif
        </section>

        <section class="card card-pad">
          <h2 style="margin-bottom:12px">Answer Review</h2>
          @foreach($submission->answers->sortBy('question.order_index') as $answer)
            <div class="question-card">
              <div class="badge-pill {{ $answer->is_correct ? 'good' : 'danger' }}">{{ $answer->is_correct ? 'Correct' : 'Incorrect' }}, {{ $answer->points_awarded }}/{{ $answer->question->points }} pt</div>
              <p style="margin-top:8px;line-height:1.6"><strong>{{ $answer->question->question_text }}</strong></p>
              @if($answer->question->question_type === 'mcq')
                <p class="muted" style="margin-top:8px">Your answer: <strong>{{ $answer->selectedOption?->option_text ?? 'No answer' }}</strong></p>
              @else
                <p class="muted" style="margin-top:8px">Your answer: <strong>{{ trim((string) $answer->answer_text) !== '' ? $answer->answer_text : 'No answer' }}</strong></p>
                <p class="dim" style="margin-top:4px">Accepted: {{ $answer->question->blankAnswers->pluck('answer_text')->join(', ') }}</p>
              @endif
              @if($answer->question->explanation)<p class="muted" style="margin-top:8px">Explanation: {{ $answer->question->explanation }}</p>@endif
            </div>
          @endforeach
        </section>
      </div>

    </main>
  </div>
</body>
</html>
