<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Take Assessment</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#263854;--text:#f8fafc;--muted:#91a4bf;--dim:#68809f;--accent:#3b82f6;--good:#10b981;--warn:#f59e0b;--bad:#ef4444;--radius:16px}*{box-sizing:border-box}body{margin:0;font-family:Inter,Arial,sans-serif;background:var(--bg);color:var(--text)}.layout{display:flex;min-height:100vh}.main{flex:1;padding:28px;min-width:0}.wrap{max-width:1480px;margin:0 auto}.top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:22px}.title{font-size:2rem;font-weight:900;margin:0}.subtitle{color:var(--muted);line-height:1.6;margin-top:8px}.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px;margin-bottom:16px}.grid{display:grid;gap:14px}.grid-2{grid-template-columns:repeat(2,minmax(0,1fr))}.grid-3{grid-template-columns:repeat(3,minmax(0,1fr))}.field label{display:block;color:var(--dim);font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:7px}.input,.select,.textarea{width:100%;background:var(--surface3);border:1px solid var(--border);color:var(--text);border-radius:10px;padding:10px 12px;font:inherit}.textarea{min-height:100px;resize:vertical}.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:10px;padding:10px 14px;font-weight:800;text-decoration:none;cursor:pointer;background:var(--accent);color:#fff}.btn.secondary{background:var(--surface2);border-color:var(--border);color:var(--text)}.btn.good{background:var(--good)}.btn.warn{background:var(--warn);color:#111827}.btn.bad{background:var(--bad)}.actions{display:flex;gap:9px;flex-wrap:wrap}.badge{display:inline-flex;padding:4px 9px;border-radius:999px;border:1px solid var(--border);background:var(--surface2);font-size:.74rem;font-weight:800}.muted{color:var(--muted)}.alert{padding:13px 15px;border-radius:12px;margin-bottom:16px;border:1px solid rgba(16,185,129,.35);background:rgba(16,185,129,.10);color:#a7f3d0}.alert.error{border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.10);color:#fecaca}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:11px 12px;border-bottom:1px solid var(--border);text-align:left;vertical-align:top}.table th{color:var(--dim);font-size:.72rem;text-transform:uppercase}.question-card{background:var(--surface);border:1px solid var(--border);border-radius:18px;margin-bottom:18px;overflow:hidden}.question-head{padding:14px 18px;background:var(--surface2);display:flex;justify-content:space-between;gap:14px;align-items:center}.question-body{padding:18px}.meta-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;margin-bottom:14px}.meta{background:var(--surface3);border:1px solid var(--border);border-radius:10px;padding:9px}.meta b{display:block;font-size:.66rem;text-transform:uppercase;color:var(--dim);margin-bottom:4px}.option-row{display:grid;grid-template-columns:34px 1fr;gap:9px;align-items:center;margin-top:8px}.item-nav{display:flex;flex-wrap:wrap;gap:6px}.item-nav a{width:38px;height:38px;border-radius:8px;display:flex;align-items:center;justify-content:center;text-decoration:none;background:var(--surface2);border:1px solid var(--border);color:var(--muted);font-weight:800}.item-nav a.done{color:#a7f3d0;border-color:rgba(16,185,129,.45)}.metric{background:var(--surface2);border:1px solid var(--border);border-radius:13px;padding:14px}.metric strong{font-size:1.55rem;display:block}.image-preview{max-width:520px;max-height:320px;border-radius:12px;border:1px solid var(--border);margin-top:10px}.hidden{display:none!important}@media(max-width:1000px){.grid-2,.grid-3,.meta-grid{grid-template-columns:1fr}.main{padding:18px}.top{flex-direction:column}}
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
            <p class="subtitle">Answer all required items before the server-enforced deadline. Refreshing this page does not reset the timer.</p>
          </div>
          @if($remainingSeconds !== null)
            <div class="metric" role="timer" aria-live="polite">
              <span class="muted">Time remaining</span>
              <strong id="assessment-timer" data-remaining="{{ $remainingSeconds }}">—</strong>
            </div>
          @endif
        </div>

        @if($errors->any())
          <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form id="assessment-form" method="POST" action="{{ route('student.assessments.submit', [$assessment, $submission]) }}">
          @csrf
          @foreach($assessment->questions as $question)
            @php($savedAnswer = old('answers.'.$question->id, $draftAnswers[(string) $question->id] ?? ''))
            <div class="question-card">
              <div class="question-head">
                <strong>{{ $question->item_number }}. {{ $question->question_text }}</strong>
                <span class="badge">{{ $question->points }} pt</span>
              </div>
              <div class="question-body">
                @if($question->image_path)
                  <img class="image-preview" src="{{ asset('storage/'.$question->image_path) }}" alt="Illustration for item {{ $question->item_number }}">
                @endif

                @if($question->question_type === 'multiple_choice')
                  @foreach($question->options as $option)
                    <label class="option-row">
                      <input
                        type="radio"
                        name="answers[{{ $question->id }}]"
                        value="{{ $option->id }}"
                        @required($question->is_required)
                        @checked((string) $savedAnswer === (string) $option->id)
                      >
                      <span>{{ $option->option_label }}. {{ $option->option_text }}</span>
                    </label>
                  @endforeach
                @elseif($question->question_type === 'true_false')
                  <label class="option-row">
                    <input
                      type="radio"
                      name="answers[{{ $question->id }}]"
                      value="True"
                      @required($question->is_required)
                      @checked(mb_strtolower((string) $savedAnswer) === 'true')
                    >
                    True
                  </label>
                  <label class="option-row">
                    <input
                      type="radio"
                      name="answers[{{ $question->id }}]"
                      value="False"
                      @required($question->is_required)
                      @checked(mb_strtolower((string) $savedAnswer) === 'false')
                    >
                    False
                  </label>
                @elseif($question->question_type === 'essay')
                  <textarea class="textarea" name="answers[{{ $question->id }}]" maxlength="30000" @required($question->is_required) placeholder="Write your answer here...">{{ $savedAnswer }}</textarea>
                @else
                  <input class="input" name="answers[{{ $question->id }}]" value="{{ $savedAnswer }}" maxlength="30000" @required($question->is_required) placeholder="Enter your answer">
                @endif
              </div>
            </div>
          @endforeach

          <div class="card">
            <div class="actions" style="justify-content:flex-end">
              <button class="btn good" type="submit" onclick="return confirm('Submit this assessment now?')">Submit Assessment</button>
            </div>
          </div>
        </form>
      </div>
    </main>
  </div>

  @include('student.partials.timed-answer-autosave', [
      'formId' => 'assessment-form',
      'autosaveUrl' => route('student.assessments.autosave', [$assessment, $submission]),
      'draftVersion' => $draftVersion,
  ])

  @if($remainingSeconds !== null)
    <script>
      (() => {
        const timer = document.getElementById('assessment-timer');
        const form = document.getElementById('assessment-form');
        let remaining = Math.max(0, Number(timer?.dataset.remaining || 0));
        let submitted = false;

        const render = () => {
          const hours = Math.floor(remaining / 3600);
          const minutes = Math.floor((remaining % 3600) / 60);
          const seconds = remaining % 60;
          timer.textContent = (hours ? String(hours).padStart(2, '0') + ':' : '')
            + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        };

        const finish = () => {
          if (submitted) return;
          submitted = true;
          timer.textContent = '00:00';
          form.dispatchEvent(new Event('datasensei:final-submit'));
          HTMLFormElement.prototype.submit.call(form);
        };

        render();
        if (remaining === 0) {
          finish();
          return;
        }

        const interval = window.setInterval(() => {
          remaining -= 1;
          render();
          if (remaining <= 0) {
            window.clearInterval(interval);
            finish();
          }
        }, 1000);

        form.addEventListener('submit', () => {
          submitted = true;
          window.clearInterval(interval);
        });
      })();
    </script>
  @endif
</body>
</html>
