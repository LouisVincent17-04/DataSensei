<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — Assignment Result</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#1e2f47;--border-hover:#2c4168;--accent:#3b82f6;--accent-hover:#2563eb;--accent2:#8b5cf6;--accent3:#10b981;--warn:#ef4444;--warn2:#f59e0b;--text:#fafafa;--muted:#7f93b0;--dim:#3d5272;--radius:14px;--radius-sm:10px;--sidebar-w:270px}*{box-sizing:border-box;margin:0;padding:0}html,body{min-height:100%;font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.12),transparent 34rem),radial-gradient(circle at top right,rgba(139,92,246,.10),transparent 28rem),var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}a{color:inherit}.ds-shell{display:flex;min-height:100vh}.ds-main{flex:1;min-width:0;padding:28px}.wrap{max-width:1450px;margin:0 auto}.top-row{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:22px}.page-kicker{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px}.page-kicker:before{content:"";width:8px;height:8px;background:var(--accent3);border-radius:50%;box-shadow:0 0 18px rgba(16,185,129,.8)}.page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;letter-spacing:-.06em;line-height:1.05}.page-subtitle{color:var(--muted);max-width:780px;line-height:1.65;margin-top:10px;font-size:.95rem}.card{border:1px solid var(--border);background:rgba(17,28,45,.92);border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.22);backdrop-filter:blur(12px);overflow:hidden}.card-pad{padding:18px}.grid{display:grid;gap:14px}.stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:16px}.stat{border:1px solid var(--border);background:rgba(255,255,255,.035);border-radius:18px;padding:16px}.stat .num{font-size:1.6rem;font-weight:900}.stat .label{color:var(--muted);font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}.toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 190px 190px auto;gap:12px;align-items:end;padding:18px;background:linear-gradient(135deg,rgba(255,255,255,.045),rgba(255,255,255,.015)),var(--surface);border-bottom:1px solid var(--border)}.field label{display:block;color:var(--dim);font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px}.input,.select,textarea{width:100%;min-height:42px;border:1px solid var(--border);border-radius:12px;background:var(--surface3);color:var(--text);padding:10px 12px;font:inherit;font-size:.88rem;outline:none}.input:focus,.select:focus,textarea:focus{border-color:rgba(59,130,246,.65);box-shadow:0 0 0 4px rgba(59,130,246,.10)}textarea{min-height:110px;resize:vertical}.btn{min-height:42px;border-radius:12px;border:1px solid transparent;padding:0 16px;font:inherit;font-size:.82rem;font-weight:900;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.15s;white-space:nowrap}.btn:hover{transform:translateY(-1px)}.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 12px 26px rgba(59,130,246,.24)}.btn.primary:hover{background:var(--accent-hover)}.btn.secondary{background:rgba(255,255,255,.035);color:var(--text);border-color:var(--border)}.btn.danger{background:rgba(239,68,68,.10);color:#fecaca;border-color:rgba(239,68,68,.35)}.btn.good{background:rgba(16,185,129,.12);color:#a7f3d0;border-color:rgba(16,185,129,.35)}.table{width:100%;border-collapse:collapse}.table th,.table td{text-align:left;padding:14px 16px;border-bottom:1px solid var(--border);vertical-align:top}.table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:var(--dim);background:rgba(255,255,255,.025)}.table td{font-size:.88rem;color:var(--muted)}.table strong{color:var(--text)}.muted{color:var(--muted)}.dim{color:var(--dim)}.badge-pill{display:inline-flex;align-items:center;gap:7px;border:1px solid var(--border);background:rgba(255,255,255,.04);border-radius:999px;padding:5px 9px;font-size:.72rem;font-weight:900;color:var(--muted);white-space:nowrap}.badge-pill.good{color:#a7f3d0;border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.09)}.badge-pill.warn{color:#fde68a;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.09)}.badge-pill.danger{color:#fecaca;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.09)}.actions{display:flex;gap:8px;flex-wrap:wrap}.alert{border-radius:16px;padding:14px 16px;font-weight:800;line-height:1.5;margin-bottom:16px;border:1px solid transparent}.alert.success{background:rgba(16,185,129,.10);border-color:rgba(16,185,129,.30);color:#a7f3d0}.alert.danger{background:rgba(239,68,68,.10);border-color:rgba(239,68,68,.30);color:#fecaca}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}.question-card{border:1px solid var(--border);background:rgba(255,255,255,.025);border-radius:18px;padding:16px;margin-bottom:12px}.option-row{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--border);background:rgba(15,25,40,.75);border-radius:12px;padding:11px;margin-top:8px}.score-big{font-size:3rem;font-weight:900;letter-spacing:-.08em}.pagination{padding:16px}.empty{text-align:center;padding:46px 22px;color:var(--muted)}@media(max-width:900px){.ds-main{padding:18px}.top-row{flex-direction:column}.stats,.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}.toolbar{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
  </style>

</head>
<body>
  <div class="ds-shell">
    @include('partials.sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <div class="page-kicker">Result</div>
            <h1 class="page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">Attempt #{{ $submission->attempt_no }} · Submitted {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}</p>
          </div>
          <a href="{{ route('student.assignments.index') }}" class="btn secondary">Back to Assignments</a>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert danger">{{ session('error') }}</div> @endif

        <section class="card card-pad" style="margin-bottom:16px">
          <div class="score-big">{{ $submission->score }}/{{ $submission->total_points }}</div>
          <div class="muted">{{ $submission->percentage }}% · <span class="badge-pill {{ $submission->status === 'late' ? 'danger' : 'good' }}">{{ ucfirst($submission->status) }}</span></div>
        </section>

        <section class="card card-pad">
          <h2 style="margin-bottom:12px">Answer Review</h2>
          @foreach($submission->answers->sortBy('question.order_index') as $answer)
            <div class="question-card">
              <div class="badge-pill {{ $answer->is_correct ? 'good' : 'danger' }}">{{ $answer->is_correct ? 'Correct' : 'Incorrect' }} · {{ $answer->points_awarded }}/{{ $answer->question->points }} pt</div>
              <p style="margin-top:10px;line-height:1.6"><strong>{{ $answer->question->question_text }}</strong></p>
              @if($answer->question->question_type === 'mcq')
                <p class="muted" style="margin-top:8px">Your answer: <strong>{{ $answer->selectedOption?->option_text ?? 'No answer' }}</strong></p>
              @else
                <p class="muted" style="margin-top:8px">Your answer: <strong>{{ $answer->answer_text ?: 'No answer' }}</strong></p>
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
