<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Take Assessment — DataSensei</title>
<style>
    /* Student assessment attempt. Colours, type and radius come from partials.design-system. */
    :root{--good:var(--ds-success);--warn:var(--ds-warning);--bad:var(--ds-danger)}
    *{box-sizing:border-box}
    body{margin:0;font-family:var(--ds-font-sans);background:var(--bg);color:var(--text)}
    .layout{display:flex;min-height:100vh}
    .main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1480px;margin:0 auto}

    /* page header with the timer on the right */
    .top{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top > div:first-child{min-width:0;flex:1 1 320px}
    .top .ds-page-title{overflow-wrap:anywhere}
    .subtitle{margin:4px 0 0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}
    .muted{color:var(--muted)}

    .metric{flex-shrink:0;min-width:150px;display:flex;flex-direction:column;gap:2px;padding:10px 16px;
      background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .metric .muted{font-size:.8125rem;font-weight:500}
    .metric strong{display:block;font-size:1.375rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}

    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-success-border);border-radius:var(--radius-sm);
      background:var(--ds-success-soft);color:#d1fae5;font-size:.875rem;line-height:1.55}
    .alert.error{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    /* one panel per question */
    .question-card{margin-bottom:16px;overflow:hidden;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .question-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;padding:14px 20px;border-bottom:1px solid var(--border)}
    .question-head strong{min-width:0;flex:1 1 auto;font-size:.9375rem;font-weight:600;line-height:1.45;overflow-wrap:anywhere}
    .question-body{padding:16px 20px 20px}
    .question-head .badge{flex-shrink:0;display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}

    .option-row{display:grid;grid-template-columns:18px minmax(0,1fr);gap:10px;align-items:center;min-height:40px;margin-top:8px;padding:8px 12px;
      border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);background:var(--surface3);
      color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5;cursor:pointer;transition:border-color .12s ease,background .12s ease}
    .question-body > .option-row:first-child,.image-preview + .option-row{margin-top:0}
    .option-row:hover{border-color:var(--ds-border-strong);background:var(--surface2)}
    .option-row:has(input:checked){border-color:var(--ds-accent-border);background:var(--ds-accent-soft);color:var(--text)}
    .option-row input{width:16px;height:16px;margin:0}
    .option-row span{overflow-wrap:anywhere}

    .input,.textarea{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font-family:var(--ds-font-sans);font-size:.875rem;line-height:1.5;
      transition:border-color .12s ease,box-shadow .12s ease}
    .textarea{display:block;min-height:120px;resize:vertical}
    .input::placeholder,.textarea::placeholder{color:var(--dim)}
    .input:focus,.textarea:focus{outline:none;border-color:var(--accent);box-shadow:var(--ds-focus-ring)}
    .image-preview{display:block;width:auto;max-width:min(520px,100%);height:auto;max-height:320px;margin:0 0 12px;
      border:1px solid var(--border);border-radius:var(--radius-sm)}

    .card{margin-bottom:16px;padding:16px 20px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--accent);border-radius:var(--radius-sm);background:var(--accent);color:#fff;
      font-size:.875rem;font-weight:500;line-height:1.2;text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover,.btn.good:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .hidden{display:none!important}

    @media(max-width:900px){.main{padding:24px 20px 40px}}
    @media(max-width:640px){
      .main{padding:20px 16px 32px}
      .top{align-items:stretch;flex-direction:column}
      .top > div:first-child{flex:0 0 auto}
      .metric{min-width:0}
      .question-head{padding:12px 16px}
      .question-body{padding:14px 16px 16px}
      .card{padding:16px}
      .actions .btn{flex:1 1 auto}
    }
    @media(prefers-reduced-motion:reduce){.btn,.option-row,.input,.textarea{transition:none}}
  </style>
    @include('partials.page-head', ['pageTitle' => 'Take Assessment', 'pageDescription' => 'Take assessments set by your instructor and review your results.'])
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
