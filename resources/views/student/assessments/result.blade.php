<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Assessment Result — DataSensei</title>
<style>
    /* Student assessment result. */
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
    .alert.pending{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:#fef3c7}
    .card.feedback{border-color:var(--ds-accent-border);background:var(--ds-accent-soft)}
    .card.feedback > p{color:#dbeafe}
    .card > p strong{color:var(--text);font-weight:600}
    .explanation{margin-top:12px;padding:12px 16px;border-radius:var(--radius-sm);background:var(--surface3);font-size:.875rem;line-height:1.6}
    .explanation strong{display:block;color:var(--text);font-size:.8125rem;font-weight:600}
    .explanation p{margin:4px 0 0;color:var(--ds-text-secondary);overflow-wrap:anywhere}
    .explanation.feedback{border:1px solid var(--ds-accent-border);background:var(--ds-accent-soft)}
    .explanation.feedback p{color:#dbeafe}

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
    @include('partials.page-head', ['pageTitle' => 'Assessment Result', 'pageDescription' => 'Take assessments set by your instructor and review your results.'])
</head>
<body>
<div class="layout">
  @include('partials.sidebar')
  <main class="main">
    <div class="wrap">
      <div class="top">
        <div>
          <h1 class="title ds-page-title">Assessment result</h1>
          <p class="subtitle">{{ $assessment->title }}</p>
        </div>
        <a class="btn secondary" href="{{ route('student.assessments.index') }}">Back</a>
      </div>

      @if(session('success'))
        <div class="alert">{{ session('success') }}</div>
      @endif

      <div class="grid grid-3">
        <div class="metric"><strong>{{ $submission->score }}/{{ $submission->total_points }}</strong><span class="muted">Score</span></div>
        <div class="metric"><strong>{{ $submission->percentage }}%</strong><span class="muted">Percentage</span></div>
        <div class="metric"><strong>{{ ucfirst($submission->status) }}</strong><span class="muted">Status</span></div>
      </div>

      @if(!$submission->graded_at)
        <div class="alert error pending">Essay responses are waiting for instructor grading. The displayed score is preliminary.</div>
      @endif

      @if($submission->feedback)
        <div class="card feedback">
          <h3>Overall Instructor Feedback</h3>
          <p>{{ $submission->feedback }}</p>
        </div>
      @endif

      @foreach($submission->answers->sortBy(fn($answer) => $answer->question->item_number) as $answer)
        <div class="card">
          <h3>{{ $answer->question->item_number }}. {{ $answer->question->question_text }}</h3>
          <p><strong>Your answer:</strong> {{ $answer->selectedOption->option_text ?? (trim((string) $answer->answer_text) !== '' ? $answer->answer_text : 'No answer') }}</p>

          @if($answer->is_correct !== null)
            <p class="muted">{{ $answer->is_correct ? 'Correct' : 'Incorrect' }}, {{ $answer->points_awarded }}/{{ $answer->question->points }}</p>
          @else
            <p class="muted">Pending instructor review, {{ $answer->points_awarded }}/{{ $answer->question->points }}</p>
          @endif

          @if($answer->question->answer_explanation && $answer->is_correct !== null)
            <div class="explanation">
              <strong>Answer Explanation</strong>
              <p>{{ $answer->question->answer_explanation }}</p>
            </div>
          @endif

          @if($answer->instructor_feedback)
            <div class="explanation feedback">
              <strong>Instructor Feedback</strong>
              <p>{{ $answer->instructor_feedback }}</p>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </main>
</div>
</body>
</html>
