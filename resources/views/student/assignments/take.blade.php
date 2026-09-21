<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Take Assignment — DataSensei</title>
<style>
    /* Student assignment attempt. Colours, type and radius come from partials.design-system. */
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

    .timer-box{flex-shrink:0;min-width:150px;padding:10px 16px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .timer-box span{display:block;color:var(--muted);font-size:.8125rem;font-weight:500}
    .timer-box strong{display:block;margin-top:2px;font-size:1.375rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}

    /* questions: rows separated by rules inside the form panel */
    .question-card{padding:18px 0;border-bottom:1px solid var(--border)}
    .question-card:first-of-type{padding-top:0}
    .question-card p{font-size:.9375rem;line-height:1.55;overflow-wrap:anywhere}
    .question-card p strong{color:var(--text);font-weight:600}
    .option-row{display:flex;gap:10px;align-items:flex-start;margin-top:8px;padding:10px 12px;
      border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);background:var(--surface3);
      color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5;cursor:pointer;transition:border-color .12s ease,background .12s ease}
    .option-row:hover{border-color:var(--ds-border-strong);background:var(--surface2)}
    .option-row:has(input:checked){border-color:var(--ds-accent-border);background:var(--ds-accent-soft);color:var(--text)}
    .option-row input{flex:0 0 16px;width:16px;height:16px;margin-top:2px}
    .option-row span{min-width:0;overflow-wrap:anywhere}
    .input{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font-family:var(--ds-font-sans);font-size:.875rem;line-height:1.4;outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input::placeholder{color:var(--dim)}
    .input:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    @media(max-width:640px){.top-row{flex-direction:column;align-items:stretch}.top-row > div:first-child{flex:0 0 auto}.timer-box{min-width:0}}

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
    @include('partials.page-head', ['pageTitle' => 'Take Assignment', 'pageDescription' => 'See the assignments your instructor set, submit work, and review feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">Attempt #{{ $submission->attempt_no }}. Answer every item. The timer is enforced by the server and does not reset if you refresh the page. When time runs out, only the answers already saved before the deadline are graded.</p>
          </div>
          @if($remainingSeconds !== null)
            <div class="timer-box" role="timer" aria-live="polite"><span>Time remaining</span><strong id="assignment-timer" data-remaining="{{ $remainingSeconds }}">—</strong></div>
          @endif
        </div>

        @if($errors->any()) <div class="alert danger">{{ $errors->first() }}</div> @endif

        <form id="assignmentForm" data-protected-assessment="1" class="card card-pad" method="POST" action="{{ route('student.assignments.submit', [$assignment, $submission]) }}">
          @csrf
          @if(!empty($antiCheatSettings['enabled']))
            <input type="hidden" name="_anti_cheat_session_id" value="{{ $antiCheatSessionId }}">
          @endif
          @foreach($assignment->libraryItem->questions as $question)
            <div class="question-card" data-assignment-question-id="{{ $question->id }}">
              <div class="badge-pill">{{ $question->type_label }}, {{ $question->points }} pt</div>
              <p style="margin-top:8px;line-height:1.6"><strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong></p>
              @if($question->question_type === 'mcq')
                @foreach($question->options as $option)
                  <label class="option-row">
                    <input
                      type="radio"
                      name="answers[{{ $question->id }}]"
                      value="{{ $option->id }}"
                      @checked((string) old('answers.'.$question->id, $draftAnswers[(string) $question->id] ?? '') === (string) $option->id)
                    >
                    <span>{{ $option->option_text }}</span>
                  </label>
                @endforeach
              @else
                <input
                  class="input"
                  style="margin-top:8px"
                  type="text"
                  name="answers[{{ $question->id }}]"
                  value="{{ old('answers.'.$question->id, $draftAnswers[(string) $question->id] ?? '') }}"
                  placeholder="Type your answer..."
                >
              @endif
            </div>
          @endforeach
          <div class="actions" style="justify-content:flex-end;margin-top:16px">
            <a href="{{ route('student.assignments.show', $assignment) }}" class="btn secondary">Cancel</a>
            <button class="btn primary" type="submit" onclick="return confirm('Submit this assignment now?')">Submit Assignment</button>
          </div>
        </form>
      </div>

    </main>
  </div>

  @include('student.partials.anti-cheat-guard', [
      'assessmentType' => 'assignment',
      'classAssignmentId' => $assignment->id,
      'assignmentSubmissionId' => $submission->id,
      'antiCheatSettings' => $antiCheatSettings ?? [],
      'antiCheatSessionId' => $antiCheatSessionId,
      'antiCheatState' => $antiCheatState ?? null,
  ])

  @include('student.partials.timed-answer-autosave', [
      'formId' => 'assignmentForm',
      'autosaveUrl' => route('student.assignments.autosave', [$assignment, $submission]),
      'draftVersion' => $draftVersion,
  ])

  @if($remainingSeconds !== null)
    <script>
      (() => {
        const timer = document.getElementById('assignment-timer');
        const form = document.getElementById('assignmentForm');
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
        // Answers posted after the server deadline are ignored, so the last
        // edits are pushed into the saved draft just before time runs out.
        const flushDraft = () => form.dispatchEvent(new Event('datasensei:flush-autosave'));
        const interval = window.setInterval(() => {
          remaining -= 1;
          render();
          if (remaining === 3 || remaining === 1) flushDraft();
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
