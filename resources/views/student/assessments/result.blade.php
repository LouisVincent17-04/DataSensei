<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Assessment Result</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px}*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;padding:28px;min-width:0}.wrap{max-width:1480px;margin:0 auto}.top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.title{font-size:2rem;font-weight:900;margin:0}.subtitle{color:var(--muted);line-height:1.6;margin-top:8px}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;margin-bottom:16px}.grid{display:grid;gap:14px}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}.muted{color:var(--muted)}.alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10);color:#a7f3d0}.alert.error{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.10);color:#fecaca}.metric{background:var(--surface2);border:1px solid var(--border);border-radius:13px;padding:14px}.metric strong{font-size:1.55rem;display:block}.explanation{margin-top:12px;padding:12px 14px;border-radius:10px;background:var(--surface3);border:1px solid var(--border);line-height:1.6}.feedback{border-color:rgba(59,130,246,.45);background:rgba(59,130,246,.08)}@media(max-width:1000px){.grid-3{grid-template-columns:1fr}.main{padding:18px}.top{flex-direction:column}}
  </style>
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
        <div class="alert error">Essay responses are waiting for instructor grading. The displayed score is preliminary.</div>
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
          <p><strong>Your answer:</strong> {{ $answer->selectedOption->option_text ?? $answer->answer_text ?: 'No answer' }}</p>

          @if($answer->is_correct !== null)
            <p class="muted">{{ $answer->is_correct ? 'Correct' : 'Incorrect' }} · {{ $answer->points_awarded }}/{{ $answer->question->points }}</p>
          @else
            <p class="muted">Pending instructor review · {{ $answer->points_awarded }}/{{ $answer->question->points }}</p>
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
