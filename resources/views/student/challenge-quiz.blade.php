<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DataSensei — {{ $challenge->title }}</title>
<style>
    /* MCQ challenge attempt. Colours, type and radius come from partials.design-system. */
    :root {
      --accent3: var(--ds-success);
      --warn:    var(--ds-danger);
      --warning: var(--ds-warning);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { margin: 0; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .page-layout-wrapper { display: flex; min-height: 100vh; }
    .page-quiz-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    /* ── title bar: stays in view with the timer ─────────────────── */
    .page-quiz-header {
      position: sticky; top: var(--ds-sticky-top, 0px); z-index: 50;
      min-height: 60px; padding: 10px 32px;
      display: flex; align-items: center; justify-content: space-between; gap: 12px 24px;
      background: var(--bg); border-bottom: 1px solid var(--border);
    }
    .page-quiz-header > div:first-child { min-width: 0; }
    .page-quiz-breadcrumb { margin-bottom: 2px; color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; overflow-wrap: anywhere; }
    .page-quiz-breadcrumb a { color: var(--muted); text-decoration: none; transition: color .12s ease; }
    .page-quiz-breadcrumb a:hover { color: var(--text); }
    .page-quiz-title { overflow-wrap: anywhere; }
    .page-quiz-header-right { display: flex; align-items: center; gap: 16px; flex-shrink: 0; }
    .page-quiz-progress-wrap { display: flex; flex-direction: column; align-items: flex-end; gap: 6px; }
    .page-quiz-progress-label { color: var(--muted); font-size: .75rem; font-weight: 500; white-space: nowrap; font-variant-numeric: tabular-nums; }
    .page-quiz-progress-track { width: 120px; height: 6px; overflow: hidden; border-radius: 999px; background: var(--surface2); }
    .page-quiz-progress-fill { height: 100%; border-radius: inherit; background: var(--accent); transition: width .2s ease; }
    .page-quiz-timer {
      min-height: 38px; padding: 0 12px; display: flex; align-items: center; gap: 8px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface);
      color: var(--text); font-size: 1.125rem; font-weight: 600; line-height: 1; font-variant-numeric: tabular-nums;
      white-space: nowrap; transition: color .16s ease, background .16s ease, border-color .16s ease;
    }
    .page-quiz-timer svg { width: 16px; height: 16px; color: var(--muted); }
    /* Last minute: a colour change, no flashing. */
    .page-quiz-timer.urgent { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }
    .page-quiz-timer.urgent svg { color: inherit; }

    /* ── body ─────────────────────────────────────────────────────── */
    .page-quiz-body { width: 100%; max-width: 820px; margin: 0 auto; padding: 28px 32px 48px; }

    .attempt-notice {
      margin-bottom: 16px; padding: 16px 20px; display: flex; gap: 12px; align-items: flex-start;
      border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface);
    }
    .attempt-notice > div:last-child { min-width: 0; }
    .attempt-code {
      flex-shrink: 0; margin-top: 1px; padding: 2px 8px; display: inline-flex; align-items: center;
      border: 1px solid var(--ds-accent-border); border-radius: var(--radius-xs); background: var(--ds-accent-soft);
      color: var(--ds-accent-text); font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; font-variant-numeric: tabular-nums;
    }
    .attempt-notice.practice .attempt-code { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }
    .attempt-notice strong { display: block; margin-bottom: 4px; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; }
    .attempt-notice p { color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .save-state { margin-top: 8px; color: var(--muted); font-size: .75rem; line-height: 1.4; }
    .save-state.saved { color: var(--ds-success-text); }
    .save-state.error { color: var(--ds-warning-text); }

    .page-quiz-info-bar {
      margin-bottom: 24px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: flex-start; gap: 12px 24px;
      border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface);
    }
    .page-quiz-desc { flex: 1 1 auto; min-width: 0; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.6; }
    .page-quiz-meta { flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
    .page-quiz-meta-pill { color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; white-space: nowrap; font-variant-numeric: tabular-nums; }

    /* ── questions ────────────────────────────────────────────────── */
    .page-quiz-question-card {
      margin-bottom: 16px; padding: 20px;
      border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface);
      transition: border-color .16s ease;
    }
    .page-quiz-question-card.answered { border-color: var(--ds-accent-border); }
    .page-quiz-q-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
    .page-quiz-q-number {
      width: 28px; height: 28px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2);
      color: var(--muted); font-size: .75rem; font-weight: 600; font-variant-numeric: tabular-nums;
    }
    .page-quiz-question-text { min-width: 0; padding-top: 3px; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.55; white-space: pre-wrap; overflow-wrap: anywhere; }
    .page-quiz-option-label {
      min-height: 44px; margin-bottom: 8px; padding: 10px 14px; display: flex; align-items: center; gap: 12px;
      border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface3); cursor: pointer;
      transition: background .12s ease, border-color .12s ease;
    }
    .page-quiz-option-label:last-of-type { margin-bottom: 0; }
    .page-quiz-option-label:hover { border-color: var(--border-hover); background: var(--surface2); }
    .page-quiz-option-label.selected { border-color: var(--accent); background: var(--ds-accent-soft); }
    .page-quiz-option-label input[type="radio"] { width: 16px; height: 16px; flex-shrink: 0; accent-color: var(--accent); cursor: pointer; }
    .page-quiz-option-label span { min-width: 0; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.5; overflow-wrap: anywhere; transition: color .12s ease; }
    .page-quiz-option-label.selected span { color: var(--text); font-weight: 500; }

    /* ── submit ───────────────────────────────────────────────────── */
    .page-quiz-submit-bar {
      margin-top: 24px; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px;
      border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface);
    }
    .page-quiz-submit-info { min-width: 0; }
    .page-quiz-submit-info strong { display: block; margin-bottom: 2px; color: var(--text); font-size: .9375rem; font-weight: 600; }
    .page-quiz-submit-info small { color: var(--muted); font-size: .8125rem; line-height: 1.5; }
    .page-quiz-submit-btn {
      flex-shrink: 0; min-height: 38px; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff;
      font-family: inherit; font-size: .875rem; font-weight: 500; line-height: 1.2; white-space: nowrap; cursor: pointer;
      transition: background .12s ease, border-color .12s ease;
    }
    .page-quiz-submit-btn:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
    .page-quiz-submit-btn:disabled { opacity: .55; cursor: not-allowed; }

    @media (max-width: 900px) {
      .page-quiz-header { min-height: 56px; padding: 8px 20px; }
      .page-quiz-body { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      /* Title and timer share the first row; progress spans the second. */
      .page-quiz-header {
        display: grid; grid-template-columns: minmax(0, 1fr) auto; grid-template-areas: "head timer" "progress progress";
        align-items: center; gap: 8px 12px; padding: 8px 16px 10px;
      }
      .page-quiz-header > div:first-child { grid-area: head; }
      .page-quiz-header-right { display: contents; }
      .page-quiz-progress-wrap { grid-area: progress; flex-direction: row; align-items: center; gap: 10px; }
      .page-quiz-progress-track { order: 2; flex: 1 1 auto; width: auto; }
      .page-quiz-timer { grid-area: timer; font-size: 1rem; }
      .page-quiz-breadcrumb { font-size: .75rem; }
      .page-quiz-body { padding: 20px 16px 32px; }
      .attempt-notice, .page-quiz-info-bar, .page-quiz-question-card { padding: 16px; }
      .page-quiz-info-bar { flex-direction: column; }
      .page-quiz-meta { flex-direction: row; flex-wrap: wrap; align-items: center; gap: 4px 16px; }
      .page-quiz-submit-bar { flex-direction: column; align-items: stretch; padding: 16px; }
      .page-quiz-submit-btn { width: 100%; }
    }
    @media (prefers-reduced-motion: reduce) {
      .page-quiz-progress-fill, .page-quiz-option-label, .page-quiz-question-card { transition: none; }
    }
  </style>
    @include('partials.page-head', ['pageDescription' => 'Practise data science challenges and track your mastery.'])
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-quiz-main">
    <form id="quizForm" action="{{ route('challenges.quiz.submit', ['slug' => $slug, 'challenge' => $challenge->id]) }}" method="POST">
      @csrf
      <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
      <input type="hidden" name="time_taken_seconds" id="time_taken" value="0">

      <header class="page-quiz-header">
        <div>
          <div class="page-quiz-breadcrumb">
            <a href="{{ route('challenges') }}">Challenges</a>
            &nbsp;›&nbsp;
            <a href="{{ route('challenges.map', $slug) }}">{{ ucwords(str_replace('-', ' ', $slug)) }}</a>
            &nbsp;› Quiz
          </div>
          <h1 class="page-quiz-title ds-page-title">{{ $challenge->title }}</h1>
        </div>
        <div class="page-quiz-header-right">
          <div class="page-quiz-progress-wrap">
            <div class="page-quiz-progress-label">
              <span id="answered-count">{{ count($savedAnswers ?? []) }}</span> / {{ $challenge->questions->count() }} answered
            </div>
            <div class="page-quiz-progress-track">
              <div class="page-quiz-progress-fill" id="progressFill" style="width:{{ $challenge->questions->count() > 0 ? (count($savedAnswers ?? []) / $challenge->questions->count()) * 100 : 0 }}%"></div>
            </div>
          </div>
          <div class="page-quiz-timer" id="timerDisplay">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span id="timeText">--:--</span>
          </div>
        </div>
      </header>

      <div class="page-quiz-body">
        <div class="attempt-notice {{ $attempt->is_ranked ? '' : 'practice' }}">
          <div class="attempt-code">{{ $attempt->is_ranked ? 'R' : 'P' }}{{ $attempt->attempt_no }}</div>
          <div>
            <strong>{{ $attempt->is_ranked ? 'Ranked attempt' : 'Practice attempt' }}</strong>
            <p>
              Your timer and answers are saved on the server. Going back, refreshing, or reopening this challenge will resume this same attempt.
              {{ $attempt->is_ranked ? 'This first attempt can affect XP and leaderboard eligibility.' : 'This retake is for learning only and will not affect the leaderboard.' }}
            </p>
            <div class="save-state saved" id="saveState">Answers loaded. Autosave is active.</div>
          </div>
        </div>

        <div class="page-quiz-info-bar">
          <div class="page-quiz-desc">{{ $challenge->description }}</div>
          <div class="page-quiz-meta">
            <span class="page-quiz-meta-pill xp">{{ $challenge->base_xp }} Base XP</span>
            <span class="page-quiz-meta-pill time">{{ intval($challenge->time_limit_seconds / 60) }} min</span>
          </div>
        </div>

        @foreach($challenge->questions as $index => $question)
          @php $selectedOption = $savedAnswers[$question->id] ?? null; @endphp
          <div class="page-quiz-question-card {{ $selectedOption ? 'answered' : '' }}" id="qcard-{{ $question->id }}">
            <div class="page-quiz-q-header">
              <div class="page-quiz-q-number">{{ $index + 1 }}</div>
              <div class="page-quiz-question-text">{{ $question->question_text }}</div>
            </div>
            @foreach($question->options as $option)
              <label class="page-quiz-option-label {{ (int) $selectedOption === (int) $option->id ? 'selected' : '' }}" onclick="selectOption(this, {{ $question->id }}, {{ $option->id }})">
                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" {{ (int) $selectedOption === (int) $option->id ? 'checked' : '' }}>
                <span>{{ $option->option_text }}</span>
              </label>
            @endforeach
          </div>
        @endforeach

        <div class="page-quiz-submit-bar">
          <div class="page-quiz-submit-info">
            <strong>Ready to submit?</strong>
            <small>Unanswered questions are allowed but counted as incorrect. Saved answers remain even after refresh or connection loss.</small>
          </div>
          <button type="submit" class="page-quiz-submit-btn" id="submitBtn">
            Submit Challenge
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="{{ asset('js/challenge-quiz-client.js') }}"></script>
<script>
  const CSRF = document.querySelector('meta[name="csrf-token"]').content;
  const totalQuestions = {{ $challenge->questions->count() }};
  const timeLimit = {{ (int) $attempt->time_limit_seconds }};
  const attemptId = {{ (int) $attempt->id }};
  const autosaveUrl = @json(route('challenges.quiz.autosave', ['slug' => $slug, 'challenge' => $challenge->id]));
  const heartbeatUrl = @json(route('challenges.quiz.heartbeat', ['slug' => $slug, 'challenge' => $challenge->id]));
  const eventUrl = @json(route('challenges.quiz.events', ['slug' => $slug, 'challenge' => $challenge->id]));
  const answeredQuestions = new Set(@json(array_map('intval', array_keys($savedAnswers ?? []))));

  // The server time reference and its local anchor live together inside the
  // clock and are only replaced as a pair (see public/js/challenge-quiz-client.js).
  const quizClock = DataSenseiChallengeQuizClient.createQuizClock({
    serverNowMs: {{ (int) $serverNowMs }},
    expiresAtMs: {{ (int) $expiresAtMs }},
  });

  const timerDisplay = document.getElementById('timerDisplay');
  const timeText = document.getElementById('timeText');
  const timeTakenInput = document.getElementById('time_taken');
  const progressFill = document.getElementById('progressFill');
  const answeredLabel = document.getElementById('answered-count');
  const form = document.getElementById('quizForm');
  const saveState = document.getElementById('saveState');
  const submitBtn = document.getElementById('submitBtn');

  function pad(n) { return String(Math.max(0, n)).padStart(2, '0'); }

  function remainingSeconds() {
    return quizClock.remainingSeconds();
  }

  function updateProgress() {
    const pct = totalQuestions > 0 ? (answeredQuestions.size / totalQuestions) * 100 : 0;
    progressFill.style.width = pct + '%';
    answeredLabel.textContent = answeredQuestions.size;
  }

  function selectOption(label, questionId, optionId) {
    label.closest('.page-quiz-question-card')
      .querySelectorAll('.page-quiz-option-label')
      .forEach(l => l.classList.remove('selected'));
    label.classList.add('selected');
    label.closest('.page-quiz-question-card').classList.add('answered');
    answeredQuestions.add(questionId);
    updateProgress();
    autosaveAnswer(questionId, optionId);
  }

  async function postJson(url, payload) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
      keepalive: true,
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
      const error = new Error(data.message || 'Request failed.');
      error.status = res.status;
      error.body = data;
      throw error;
    }
    return data;
  }

  function setSaveState(text, cls) {
    saveState.textContent = text;
    saveState.className = 'save-state ' + (cls || '');
  }

  let submitting = false;

  const autosaveQueue = DataSenseiChallengeQuizClient.createAutosaveQueue({
    seqBase: {{ (int) ($autosaveSeqBase ?? 0) }},
    send: payload => postJson(autosaveUrl, Object.assign({ attempt_id: attemptId }, payload)),
    onState: state => {
      if (submitting) return;
      if (state === 'saving') {
        setSaveState('Saving answer...', '');
      } else if (state === 'saved') {
        setSaveState('Saved. You can safely refresh or return later.', 'saved');
      } else if (state === 'error') {
        setSaveState('Connection issue. Your answer is not saved yet. Retrying automatically, keep this page open.', 'error');
      }
    },
    onClosed: reason => {
      if (reason === 'expired') {
        submitDueToTime();
      } else if (!submitting) {
        setSaveState('This attempt is already finished. New changes were not saved.', 'error');
      }
    },
  });

  function autosaveAnswer(questionId, optionId) {
    autosaveQueue.set(questionId, optionId);
  }

  async function logAttemptEvent(eventType, details = {}) {
    try {
      await postJson(eventUrl, { attempt_id: attemptId, event_type: eventType, details });
    } catch (error) {
      // Event logging should never disturb the student taking the quiz.
    }
  }

  async function heartbeat() {
    const ticket = quizClock.beginSync();
    try {
      const data = await postJson(heartbeatUrl, { attempt_id: attemptId });
      quizClock.completeSync(ticket, data);
      if (data.should_submit || (data.status && data.status !== 'in_progress')) {
        submitDueToTime();
        return;
      }
      updateTimer();
    } catch (error) {
      // If offline, the client timer keeps counting down using the latest known server expiry.
    }
  }

  function updateTimer() {
    if (quizClock.detectSuspension()) {
      // The device slept or the tab was frozen: ask the server instead of guessing.
      heartbeat();
    }

    const left = remainingSeconds();
    const m = Math.floor(left / 60);
    const s = left % 60;
    timeText.textContent = pad(m) + ':' + pad(s);
    timeTakenInput.value = Math.min(timeLimit, Math.max(0, timeLimit - left));

    if (left <= 60) timerDisplay.classList.add('urgent');

    if (left <= 0) {
      submitDueToTime();
    }
  }

  function submitDueToTime() {
    if (submitting) return;
    submitting = true;
    submitBtn.disabled = true;
    setSaveState('Time is up. Submitting saved answers...', 'error');
    HTMLFormElement.prototype.submit.call(form);
  }

  form.addEventListener('submit', () => {
    submitting = true;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
  });

  document.addEventListener('visibilitychange', () => {
    if (document.hidden && !submitting) {
      autosaveQueue.flush();
      logAttemptEvent('tab_hidden_or_app_switched', { answered: answeredQuestions.size });
    } else if (!document.hidden && !submitting) {
      heartbeat();
      autosaveQueue.flush();
    }
  });

  window.addEventListener('pageshow', (e) => {
    if (e.persisted && !submitting) heartbeat();
  });

  window.addEventListener('pagehide', () => {
    if (!submitting) autosaveQueue.flush();
  });

  window.addEventListener('online', () => {
    if (!submitting) {
      autosaveQueue.flush();
      heartbeat();
    }
  });

  window.addEventListener('beforeunload', (e) => {
    if (!submitting) {
      autosaveQueue.flush();
      logAttemptEvent('page_leave_or_refresh', { answered: answeredQuestions.size });
      e.preventDefault();
      e.returnValue = '';
    }
  });

  updateProgress();
  updateTimer();
  setInterval(updateTimer, 1000);
  setInterval(heartbeat, 30000);
  heartbeat();
</script>
</body>
</html>
