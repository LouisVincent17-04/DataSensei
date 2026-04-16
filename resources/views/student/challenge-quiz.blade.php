<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — {{ $challenge->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6;
      --accent-hover: #2563eb;
      --accent3:      #10b981;
      --warn:         #ef4444;
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      --radius:       12px;
      --radius-sm:    6px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* Standardized Layout */
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── NAMESPACED QUIZ COMPONENTS ── */
    .page-quiz-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    /* Sticky Header */
    .page-quiz-header {
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(13, 19, 32, 0.96);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
      padding: 14px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }
    .page-quiz-breadcrumb { font-size: 0.7rem; color: var(--dim); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; margin-bottom: 3px; }
    .page-quiz-breadcrumb a { color: var(--muted); text-decoration: none; transition: color 0.15s; }
    .page-quiz-breadcrumb a:hover { color: var(--text); }
    .page-quiz-title { font-size: 1.125rem; font-weight: 700; color: var(--text); }

    .page-quiz-header-right { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
    
    /* Progress */
    .page-quiz-progress-wrap { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
    .page-quiz-progress-label { font-size: 0.7rem; color: var(--muted); font-weight: 600; white-space: nowrap; }
    .page-quiz-progress-track { width: 120px; height: 5px; background: var(--surface2); border-radius: 10px; overflow: hidden; }
    .page-quiz-progress-fill { height: 100%; background: var(--accent); border-radius: 10px; transition: width 0.3s ease; }

    /* Timer */
    .page-quiz-timer {
      font-size: 1.375rem; font-weight: 700; font-variant-numeric: tabular-nums;
      display: flex; align-items: center; gap: 8px;
      color: var(--accent); background: var(--surface);
      padding: 8px 18px; border-radius: 8px; border: 1px solid var(--border);
      white-space: nowrap;
    }
    .page-quiz-timer.urgent { color: var(--warn); border-color: rgba(239,68,68,0.4); animation: blink 1s step-end infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.45} }

    /* Quiz Body */
    .page-quiz-body {
      max-width: 760px;
      width: 100%;
      margin: 36px auto;
      padding: 0 32px 80px;
    }

    .page-quiz-info-bar {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 18px 24px; margin-bottom: 32px;
      display: flex; justify-content: space-between; align-items: center;
      gap: 16px; flex-wrap: wrap;
    }
    .page-quiz-desc { font-size: 0.9rem; color: var(--muted); line-height: 1.6; flex: 1; }
    .page-quiz-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; flex-shrink: 0; }
    .page-quiz-meta-pill { font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .page-quiz-meta-pill.xp   { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
    .page-quiz-meta-pill.time { background: rgba(59,130,246,0.12); color: var(--accent); border: 1px solid rgba(59,130,246,0.25); }

    /* Questions */
    .page-quiz-question-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 28px; margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: border-color 0.2s;
    }
    .page-quiz-q-header { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 20px; }
    .page-quiz-q-number {
      width: 28px; height: 28px; border-radius: 6px;
      background: var(--surface2); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 0.75rem; font-weight: 700; color: var(--muted);
      flex-shrink: 0; margin-top: 2px;
    }
    .page-quiz-question-text { font-size: 1rem; font-weight: 600; line-height: 1.55; color: var(--text); white-space: pre-wrap; }

    /* Options */
    .page-quiz-option-label {
      display: flex; align-items: center; gap: 14px;
      padding: 14px 18px; border: 1px solid var(--border);
      border-radius: 8px; cursor: pointer;
      transition: all 0.18s; margin-bottom: 10px; background: var(--bg);
    }
    .page-quiz-option-label:last-of-type { margin-bottom: 0; }
    .page-quiz-option-label:hover { border-color: rgba(59,130,246,0.45); background: rgba(59,130,246,0.04); }
    .page-quiz-option-label.selected { border-color: var(--accent); background: rgba(59,130,246,0.1); }
    .page-quiz-option-label input[type="radio"] { width: 18px; height: 18px; accent-color: var(--accent); cursor: pointer; flex-shrink: 0; }
    .page-quiz-option-label span { font-size: 0.9375rem; color: var(--muted); line-height: 1.5; transition: color 0.18s; }
    .page-quiz-option-label.selected span { color: var(--text); font-weight: 500; }

    /* Submit Bar */
    .page-quiz-submit-bar {
      background: var(--surface); padding: 20px 28px;
      border-radius: var(--radius); border: 1px solid var(--border);
      display: flex; justify-content: space-between; align-items: center;
      gap: 16px; margin-top: 36px; box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
    .page-quiz-submit-info strong { font-size: 0.875rem; color: var(--text); display: block; margin-bottom: 2px; }
    .page-quiz-submit-info small  { font-size: 0.8rem; color: var(--muted); }
    .page-quiz-submit-btn {
      background: var(--accent); color: #fff; border: none;
      padding: 13px 28px; font-size: 0.9375rem; font-weight: 700;
      border-radius: var(--radius-sm); cursor: pointer;
      transition: all 0.2s; box-shadow: 0 4px 14px rgba(59,130,246,0.35);
      white-space: nowrap; font-family: inherit;
      display: flex; align-items: center; gap: 8px;
    }
    .page-quiz-submit-btn:hover { background: var(--accent-hover); transform: translateY(-1px); }
    .page-quiz-submit-btn:active { transform: translateY(0); }

    @media (max-width: 768px) {
      .page-quiz-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 12px 16px; }
      .page-quiz-body { padding: 0 16px 60px; }
      .page-quiz-submit-bar { flex-direction: column; align-items: stretch; }
      .page-quiz-progress-track { width: 80px; }
    }
  </style>
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-quiz-main">
    <form id="quizForm"
          action="{{ route('challenges.quiz.submit', ['slug' => $slug, 'challenge' => $challenge->id]) }}"
          method="POST">
      @csrf
      <input type="hidden" name="time_taken_seconds" id="time_taken" value="0">

      <header class="page-quiz-header">
        <div>
          <div class="page-quiz-breadcrumb">
            <a href="{{ route('challenges') }}">Challenges</a>
            &nbsp;›&nbsp;
            <a href="{{ route('challenges.map', $slug) }}">{{ ucwords(str_replace('-', ' ', $slug)) }}</a>
            &nbsp;› Quiz
          </div>
          <div class="page-quiz-title">{{ $challenge->title }}</div>
        </div>
        <div class="page-quiz-header-right">
          <div class="page-quiz-progress-wrap">
            <div class="page-quiz-progress-label">
              <span id="answered-count">0</span> / {{ $challenge->questions->count() }} answered
            </div>
            <div class="page-quiz-progress-track">
              <div class="page-quiz-progress-fill" id="progressFill" style="width:0%"></div>
            </div>
          </div>
          <div class="page-quiz-timer" id="timerDisplay">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span id="timeText">--:--</span>
          </div>
        </div>
      </header>

      <div class="page-quiz-body">

        <div class="page-quiz-info-bar">
          <div class="page-quiz-desc">{{ $challenge->description }}</div>
          <div class="page-quiz-meta">
            <span class="page-quiz-meta-pill xp">⚡ {{ $challenge->base_xp }} Base XP</span>
            <span class="page-quiz-meta-pill time">⏱ {{ intval($challenge->time_limit_seconds / 60) }} min</span>
          </div>
        </div>

        @foreach($challenge->questions as $index => $question)
          <div class="page-quiz-question-card" id="qcard-{{ $question->id }}">
            <div class="page-quiz-q-header">
              <div class="page-quiz-q-number">{{ $index + 1 }}</div>
              <div class="page-quiz-question-text">{{ $question->question_text }}</div>
            </div>
            @foreach($question->options as $option)
              <label class="page-quiz-option-label" onclick="selectOption(this, {{ $question->id }})">
                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required>
                <span>{{ $option->option_text }}</span>
              </label>
            @endforeach
          </div>
        @endforeach

        <div class="page-quiz-submit-bar">
          <div class="page-quiz-submit-info">
            <strong>Ready to submit?</strong>
            <small>Answer all {{ $challenge->questions->count() }} questions. Time bonus applied if you score 70%+.</small>
          </div>
          <button type="submit" class="page-quiz-submit-btn" id="submitBtn">
            Submit Challenge
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
          </button>
        </div>

      </div>{{-- .page-quiz-body --}}
    </form>
  </div>{{-- .page-quiz-main --}}
</div>

  <script>
    const totalQuestions    = {{ $challenge->questions->count() }};
    const timeLimit         = {{ $challenge->time_limit_seconds }};
    let   timeRemaining     = timeLimit;
    const answeredQuestions = new Set();

    const timerDisplay   = document.getElementById('timerDisplay');
    const timeText       = document.getElementById('timeText');
    const timeTakenInput = document.getElementById('time_taken');
    const progressFill   = document.getElementById('progressFill');
    const answeredLabel  = document.getElementById('answered-count');
    const form           = document.getElementById('quizForm');

    function selectOption(label, questionId) {
      // 🚀 JS updated to use the new namespaced classes!
      label.closest('.page-quiz-question-card')
           .querySelectorAll('.page-quiz-option-label')
           .forEach(l => l.classList.remove('selected'));
      label.classList.add('selected');

      answeredQuestions.add(questionId);
      const pct = (answeredQuestions.size / totalQuestions) * 100;
      progressFill.style.width = pct + '%';
      answeredLabel.textContent = answeredQuestions.size;
    }

    function pad(n) { return String(n).padStart(2, '0'); }

    function updateTimer() {
      const m = Math.floor(timeRemaining / 60);
      const s = timeRemaining % 60;
      timeText.textContent = pad(m) + ':' + pad(s);
      timeTakenInput.value = timeLimit - timeRemaining;

      if (timeRemaining <= 60) timerDisplay.classList.add('urgent');

      if (timeRemaining <= 0) {
        clearInterval(timerInterval);
        timeText.textContent = '00:00';
        form.submit();
        return;
      }
      timeRemaining--;
    }

    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);

    let submitting = false;
    form.addEventListener('submit', () => { submitting = true; });
    window.addEventListener('beforeunload', (e) => {
      if (!submitting && answeredQuestions.size > 0) {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  </script>

</body>
</html>