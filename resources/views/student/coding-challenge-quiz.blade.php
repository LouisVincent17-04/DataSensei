<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — {{ $challenge->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet" />
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
      --warn-bg:      rgba(239,68,68,0.1);
      --pass-bg:      rgba(16,185,129,0.1);
      --pass-border:  rgba(16,185,129,0.25);
      --warn-border:  rgba(239,68,68,0.25);
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      --radius:       12px;
      --radius-sm:    6px;
      --mono:         'JetBrains Mono', 'Fira Code', monospace;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── MAIN ── */
    .page-coding-main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    /* ── HEADER ── */
    .page-coding-header {
      position: sticky; top: 0; z-index: 100;
      background: rgba(13,19,32,0.97); backdrop-filter: blur(10px);
      border-bottom: 1px solid var(--border);
      padding: 12px 28px;
      display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .page-coding-breadcrumb { font-size: 0.7rem; color: var(--dim); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600; margin-bottom: 3px; }
    .page-coding-breadcrumb a { color: var(--muted); text-decoration: none; transition: color 0.15s; }
    .page-coding-breadcrumb a:hover { color: var(--text); }
    .page-coding-title { font-size: 1.05rem; font-weight: 700; }

    .page-coding-header-right { display: flex; align-items: center; gap: 12px; flex-shrink: 0; }

    /* Progress dots */
    .page-coding-dots { display: flex; gap: 6px; align-items: center; }
    .page-coding-dot {
      width: 10px; height: 10px; border-radius: 50%;
      background: var(--surface2); border: 1px solid var(--border);
      cursor: pointer; transition: all 0.2s;
    }
    .page-coding-dot.is-active { background: var(--accent); border-color: var(--accent); box-shadow: 0 0 8px rgba(59,130,246,0.5); }
    .page-coding-dot.is-passed { background: var(--accent3); border-color: var(--accent3); }
    .page-coding-dot.is-failed { background: var(--warn); border-color: var(--warn); }

    /* Timer */
    .page-coding-timer {
      font-family: var(--mono); font-size: 1.25rem; font-weight: 700;
      display: flex; align-items: center; gap: 8px;
      color: var(--accent); background: var(--surface);
      padding: 7px 16px; border-radius: 8px; border: 1px solid var(--border);
      white-space: nowrap;
    }
    .page-coding-timer.urgent { color: var(--warn); border-color: var(--warn-border); animation: pulse-warn 1s ease-in-out infinite; }
    @keyframes pulse-warn { 0%,100%{opacity:1} 50%{opacity:0.5} }

    /* ── QUESTION PANEL (accordion-style, one at a time) ── */
    .page-coding-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .page-coding-question { display: none; flex: 1; flex-direction: row; min-height: 0; }
    .page-coding-question.is-active { display: flex; }

    /* Left: problem description */
    .page-coding-problem {
      width: 380px; min-width: 280px; flex-shrink: 0;
      display: flex; flex-direction: column;
      border-right: 1px solid var(--border);
      overflow-y: auto;
    }
    .page-coding-problem-inner { padding: 28px 24px; flex: 1; }
    .page-coding-problem-num { font-size: 0.7rem; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
    .page-coding-problem-title { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 16px; line-height: 1.4; }
    .page-coding-problem-desc { font-size: 0.875rem; color: var(--muted); line-height: 1.75; white-space: pre-wrap; }

    /* Test-case preview (visible only) */
    .page-coding-testcases { margin-top: 24px; }
    .page-coding-testcases-label { font-size: 0.7rem; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
    .page-coding-tc {
      background: var(--surface2); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 12px 14px; margin-bottom: 8px;
      font-family: var(--mono); font-size: 0.8rem;
    }
    .page-coding-tc-row { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 4px; }
    .page-coding-tc-row:last-child { margin-bottom: 0; }
    .page-coding-tc-key { color: var(--dim); font-weight: 700; min-width: 68px; flex-shrink: 0; }
    .page-coding-tc-val { color: var(--muted); word-break: break-all; }

    /* Result overlay on test cases after submit */
    .page-coding-tc.tc-pass { border-color: var(--pass-border); background: var(--pass-bg); }
    .page-coding-tc.tc-fail { border-color: var(--warn-border); background: var(--warn-bg); }
    .page-coding-tc.tc-error { border-color: rgba(245,158,11,0.3); background: rgba(245,158,11,0.07); }
    .page-coding-tc-badge { font-size: 0.65rem; font-weight: 700; padding: 2px 7px; border-radius: 4px; text-transform: uppercase; }
    .tc-badge-pass { background: rgba(16,185,129,0.2); color: var(--accent3); }
    .tc-badge-fail { background: rgba(239,68,68,0.2); color: var(--warn); }
    .tc-badge-error{ background: rgba(245,158,11,0.2); color: #f59e0b; }

    /* Meta pills */
    .page-coding-meta { padding: 0 24px 20px; display: flex; flex-wrap: wrap; gap: 8px; }
    .page-coding-pill { font-size: 0.72rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; }
    .pill-xp   { background: rgba(245,158,11,0.12); color: #f59e0b; border: 1px solid rgba(245,158,11,0.25); }
    .pill-lang { background: rgba(59,130,246,0.10); color: var(--accent); border: 1px solid rgba(59,130,246,0.25); }
    .pill-time { background: rgba(16,185,129,0.10); color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }

    /* Right: editor + actions */
    .page-coding-editor-panel {
      flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden;
    }

    /* Editor toolbar */
    .page-coding-toolbar {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 16px; border-bottom: 1px solid var(--border);
      background: var(--surface); flex-shrink: 0;
    }
    .page-coding-toolbar-lang { font-family: var(--mono); font-size: 0.78rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }
    .page-coding-toolbar-dot { width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; }
    .page-coding-toolbar-spacer { flex: 1; }
    .page-coding-btn {
      font-family: 'Inter', sans-serif; font-size: 0.8125rem; font-weight: 600;
      padding: 7px 16px; border-radius: var(--radius-sm); border: none; cursor: pointer;
      display: flex; align-items: center; gap: 6px; transition: all 0.18s; white-space: nowrap;
    }
    .page-coding-btn-run  { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .page-coding-btn-run:hover  { border-color: var(--border-hover); color: var(--text); }
    .page-coding-btn-submit { background: var(--accent); color: #fff; box-shadow: 0 3px 12px rgba(59,130,246,0.3); }
    .page-coding-btn-submit:hover { background: var(--accent-hover); transform: translateY(-1px); }
    .page-coding-btn-submit:disabled { opacity: 0.5; cursor: default; transform: none; }
    .page-coding-btn-next { background: var(--accent3); color: #fff; display: none; }
    .page-coding-btn-next:hover { background: #0da271; }

    /* Textarea editor */
    .page-coding-editor-wrap { flex: 1; display: flex; flex-direction: column; position: relative; min-height: 0; }
    .page-coding-textarea {
      flex: 1; width: 100%; resize: none; outline: none; border: none;
      background: #0a1020; color: #e2eaf7;
      font-family: var(--mono); font-size: 0.875rem; line-height: 1.7;
      padding: 20px 24px; tab-size: 4;
      caret-color: var(--accent);
    }
    .page-coding-textarea::selection { background: rgba(59,130,246,0.3); }

    /* Result panel (slides up after submit) */
    .page-coding-result {
      flex-shrink: 0; border-top: 1px solid var(--border);
      background: var(--surface); max-height: 0; overflow: hidden;
      transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    .page-coding-result.is-open { max-height: 340px; overflow-y: auto; }
    .page-coding-result-inner { padding: 16px 20px; }
    .page-coding-result-header { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
    .page-coding-result-title { font-size: 0.875rem; font-weight: 700; }
    .page-coding-result-score { font-size: 0.8rem; color: var(--muted); }
    .page-coding-result-xp { font-size: 0.8rem; font-weight: 700; color: #f59e0b; }
    .page-coding-result-cases { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 8px; }

    /* Verdict banner */
    .page-coding-verdict {
      font-size: 0.78rem; font-weight: 700; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.06em;
    }
    .verdict-pass  { background: var(--pass-bg);  color: var(--accent3); border: 1px solid var(--pass-border); }
    .verdict-fail  { background: var(--warn-bg);  color: var(--warn);    border: 1px solid var(--warn-border); }
    .verdict-error { background: rgba(245,158,11,0.1); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }

    /* Spinner */
    .page-coding-spinner { display: none; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Stderr panel */
    .page-coding-stderr {
      margin-top: 10px; padding: 12px 14px; border-radius: var(--radius-sm);
      background: var(--warn-bg); border: 1px solid var(--warn-border);
      font-family: var(--mono); font-size: 0.78rem; color: #f87171;
      white-space: pre-wrap; word-break: break-all;
    }

    /* Navigation footer */
    .page-coding-footer {
      position: sticky; bottom: 0; z-index: 50;
      background: rgba(13,19,32,0.97); border-top: 1px solid var(--border);
      padding: 14px 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px;
    }
    .page-coding-footer-info { font-size: 0.8rem; color: var(--muted); }
    .page-coding-footer-info strong { color: var(--text); }
    .page-coding-footer-nav { display: flex; gap: 10px; }
    .page-coding-btn-nav { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .page-coding-btn-nav:hover:not(:disabled) { color: var(--text); border-color: var(--border-hover); }
    .page-coding-btn-nav:disabled { opacity: 0.35; cursor: default; }

    /* Resize handle */
    .page-coding-resizer {
      width: 4px; cursor: col-resize; background: var(--border); flex-shrink: 0;
      transition: background 0.15s;
    }
    .page-coding-resizer:hover { background: var(--accent); }

    @media (max-width: 900px) {
      .page-coding-question { flex-direction: column; }
      .page-coding-problem { width: 100%; border-right: none; border-bottom: 1px solid var(--border); max-height: 260px; }
      .page-coding-resizer { display: none; }
    }
    @media (max-width: 600px) {
      .page-coding-header { padding: 10px 14px; }
      .page-coding-footer { padding: 10px 14px; }
    }
  </style>
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-coding-main">

    {{-- ── HEADER ── --}}
    <header class="page-coding-header">
      <div>
        <div class="page-coding-breadcrumb">
          <a href="{{ route('challenges') }}">Challenges</a>
          &nbsp;›&nbsp;
          <a href="{{ route('challenges.map', $slug) }}">{{ ucwords(str_replace('-', ' ', $slug)) }}</a>
          &nbsp;› Coding Challenge
        </div>
        <div class="page-coding-title">{{ $challenge->title }}</div>
      </div>

      <div class="page-coding-header-right">
        {{-- Question nav dots --}}
        <div class="page-coding-dots" id="navDots">
          @foreach($challenge->codingQuestions as $i => $q)
            <div class="page-coding-dot {{ $i === 0 ? 'is-active' : '' }}"
                 data-index="{{ $i }}"
                 title="Question {{ $i + 1 }}"
                 onclick="gotoQuestion({{ $i }})"></div>
          @endforeach
        </div>

        <div class="page-coding-timer" id="timerDisplay">
          <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
          </svg>
          <span id="timerText">--:--</span>
        </div>
      </div>
    </header>

    {{-- ── QUESTION PANELS ── --}}
    <div class="page-coding-body">
      @foreach($challenge->codingQuestions as $i => $question)
        @php $prior = $priorSubmissions[$question->id] ?? null; @endphp

        <div class="page-coding-question {{ $i === 0 ? 'is-active' : '' }}"
             id="question-panel-{{ $i }}"
             data-index="{{ $i }}"
             data-question-id="{{ $question->id }}"
             data-timelimit="{{ $question->time_limit_seconds }}"
             data-submit-url="{{ route('challenges.coding.submit', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]) }}">

          {{-- LEFT: Problem --}}
          <div class="page-coding-problem">
            <div class="page-coding-problem-inner">
              <div class="page-coding-problem-num">Question {{ $i + 1 }} of {{ $challenge->codingQuestions->count() }}</div>
              <h2 class="page-coding-problem-title">{{ $question->problem_description }}</h2>

              {{-- Visible test cases --}}
              @if($question->visibleTestCases->isNotEmpty())
                <div class="page-coding-testcases">
                  <div class="page-coding-testcases-label">Sample Test Cases</div>
                  @foreach($question->visibleTestCases as $tc)
                    <div class="page-coding-tc" id="tc-{{ $tc->id }}">
                      @if($tc->input !== null)
                        <div class="page-coding-tc-row">
                          <span class="page-coding-tc-key">Input:</span>
                          <span class="page-coding-tc-val">{{ $tc->input }}</span>
                        </div>
                      @endif
                      <div class="page-coding-tc-row">
                        <span class="page-coding-tc-key">Expected:</span>
                        <span class="page-coding-tc-val" id="tc-expected-{{ $tc->id }}">{{ $tc->expected_output }}</span>
                      </div>
                      <div class="page-coding-tc-row" id="tc-actual-row-{{ $tc->id }}" style="display:none;">
                        <span class="page-coding-tc-key">Got:</span>
                        <span class="page-coding-tc-val" id="tc-actual-{{ $tc->id }}"></span>
                        <span class="page-coding-tc-badge" id="tc-badge-{{ $tc->id }}"></span>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>

            <div class="page-coding-meta">
              <span class="page-coding-pill pill-xp">⚡ {{ $question->base_xp }} XP</span>
              <span class="page-coding-pill pill-lang">🐍 Python</span>
              <span class="page-coding-pill pill-time">⏱ {{ intval($question->time_limit_seconds / 60) }}m</span>
            </div>
          </div>

          {{-- Resize handle --}}
          <div class="page-coding-resizer" data-panel="{{ $i }}"></div>

          {{-- RIGHT: Editor --}}
          <div class="page-coding-editor-panel">

            <div class="page-coding-toolbar">
              <div class="page-coding-toolbar-lang">
                <div class="page-coding-toolbar-dot"></div>
                Python 3
              </div>
              <div class="page-coding-toolbar-spacer"></div>
              <button class="page-coding-btn page-coding-btn-next" id="btn-next-{{ $i }}" onclick="gotoQuestion({{ $i + 1 }})">
                Next Question →
              </button>
              <button class="page-coding-btn page-coding-btn-submit" id="btn-submit-{{ $i }}"
                      onclick="submitCode({{ $i }})">
                <div class="page-coding-spinner" id="spinner-{{ $i }}"></div>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" id="submit-icon-{{ $i }}"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Run & Submit
              </button>
            </div>

            <div class="page-coding-editor-wrap">
              <textarea class="page-coding-editor page-coding-textarea"
                        id="editor-{{ $i }}"
                        spellcheck="false"
                        placeholder="# Write your Python solution here..."
                        onkeydown="handleTab(event)">{{ $prior ? $prior->code : ($question->starter_code ?? '') }}</textarea>
            </div>

            {{-- Result panel --}}
            <div class="page-coding-result" id="result-{{ $i }}">
              <div class="page-coding-result-inner">
                <div class="page-coding-result-header">
                  <span class="page-coding-result-title">Results</span>
                  <span class="page-coding-verdict" id="verdict-{{ $i }}"></span>
                  <span class="page-coding-result-score" id="score-{{ $i }}"></span>
                  <span class="page-coding-result-xp" id="xp-label-{{ $i }}"></span>
                </div>
                <div class="page-coding-result-cases" id="result-cases-{{ $i }}"></div>
                <div class="page-coding-stderr" id="stderr-{{ $i }}" style="display:none;"></div>
              </div>
            </div>

          </div>{{-- editor-panel --}}
        </div>{{-- question --}}
      @endforeach
    </div>{{-- body --}}

    {{-- ── FOOTER NAV ── --}}
    <footer class="page-coding-footer">
      <div class="page-coding-footer-info" id="footerInfo">
        Question <strong id="footerCurrent">1</strong> of <strong>{{ $challenge->codingQuestions->count() }}</strong>
      </div>
      <div class="page-coding-footer-nav">
        <button class="page-coding-btn page-coding-btn-nav" id="btnPrev" onclick="gotoQuestion(currentIndex - 1)" disabled>← Prev</button>
        <button class="page-coding-btn page-coding-btn-nav" id="btnNext" onclick="gotoQuestion(currentIndex + 1)" {{ $challenge->codingQuestions->count() <= 1 ? 'disabled' : '' }}>Next →</button>
      </div>
    </footer>

  </div>{{-- page-coding-main --}}
</div>

<script>
  /* ──────────────────────────────────────────────────────────
     STATE
  ─────────────────────────────────────────────────────────── */
  const TOTAL          = {{ $challenge->codingQuestions->count() }};
  let   currentIndex   = 0;
  const submittedFor   = new Set(); // question indices already submitted
  const questionStates = {}; // { index: { status, passed, total, xp } }

  /* Global timer (counts up) */
  let globalSeconds = 0;
  setInterval(() => { globalSeconds++; }, 1000);

  /* Per-question countdown timers */
  const timers = {}; // { index: { remaining, interval } }

  /* ──────────────────────────────────────────────────────────
     NAVIGATION
  ─────────────────────────────────────────────────────────── */
  function gotoQuestion(index) {
    if (index < 0 || index >= TOTAL) return;

    // hide current
    document.getElementById(`question-panel-${currentIndex}`)?.classList.remove('is-active');
    updateDot(currentIndex);

    currentIndex = index;

    // show next
    document.getElementById(`question-panel-${currentIndex}`).classList.add('is-active');
    updateDot(currentIndex);

    // update footer
    document.getElementById('footerCurrent').textContent = currentIndex + 1;
    document.getElementById('btnPrev').disabled = currentIndex === 0;
    document.getElementById('btnNext').disabled = currentIndex === TOTAL - 1;

    // start per-question timer if not started
    startQuestionTimer(currentIndex);
  }

  function updateDot(index) {
    const dot = document.querySelector(`.page-coding-dot[data-index="${index}"]`);
    if (!dot) return;
    dot.classList.remove('is-active', 'is-passed', 'is-failed');
    const state = questionStates[index];
    if (index === currentIndex) dot.classList.add('is-active');
    else if (state?.status === 'passed') dot.classList.add('is-passed');
    else if (state?.status === 'failed' || state?.status === 'error') dot.classList.add('is-failed');
  }

  /* ──────────────────────────────────────────────────────────
     PER-QUESTION TIMER
  ─────────────────────────────────────────────────────────── */
  function startQuestionTimer(index) {
    if (timers[index]) return; // already running

    const panel     = document.getElementById(`question-panel-${index}`);
    const limitSecs = parseInt(panel.dataset.timelimit, 10);
    timers[index]   = { remaining: limitSecs };

    timers[index].interval = setInterval(() => {
      timers[index].remaining--;
      if (index === currentIndex) renderTimer(timers[index].remaining);
      if (timers[index].remaining <= 0) {
        clearInterval(timers[index].interval);
        // Auto-submit if not already submitted
        if (!submittedFor.has(index)) submitCode(index, true);
      }
    }, 1000);

    renderTimer(limitSecs);
  }

  function renderTimer(secs) {
    const m   = Math.floor(secs / 60);
    const s   = secs % 60;
    const pad = n => String(n).padStart(2, '0');
    document.getElementById('timerText').textContent = pad(m) + ':' + pad(s);
    document.getElementById('timerDisplay').classList.toggle('urgent', secs <= 60);
  }

  // kick off first timer
  startQuestionTimer(0);

  /* ──────────────────────────────────────────────────────────
     SUBMIT
  ─────────────────────────────────────────────────────────── */
  async function submitCode(index, autoSubmit = false) {
    if (submittedFor.has(index)) return;

    const panel    = document.getElementById(`question-panel-${index}`);
    const qId      = panel.dataset.questionId;
    const url      = panel.dataset.submitUrl;
    const code     = document.getElementById(`editor-${index}`).value.trim();
    const timeUsed = timers[index] ? parseInt(panel.dataset.timelimit) - timers[index].remaining : globalSeconds;

    if (!code && !autoSubmit) {
      shakeEditor(index);
      return;
    }

    // UI: loading state
    const btn      = document.getElementById(`btn-submit-${index}`);
    const spinner  = document.getElementById(`spinner-${index}`);
    const icon     = document.getElementById(`submit-icon-${index}`);
    btn.disabled   = true;
    spinner.style.display = 'block';
    icon.style.display    = 'none';

    // Stop the question timer
    if (timers[index]?.interval) clearInterval(timers[index].interval);

    try {
      const resp = await fetch(url, {
        method:  'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                          || '{{ csrf_token() }}',
          'Accept':       'application/json',
        },
        body: JSON.stringify({ code, time_taken_seconds: timeUsed }),
      });

      const data = await resp.json();
      submittedFor.add(index);
      questionStates[index] = { status: data.status, passed: data.tests_passed, total: data.tests_total, xp: data.xp_earned };

      renderResults(index, data);
      updateDot(index);

      // Show "Next" button if not last
      if (index < TOTAL - 1) {
        document.getElementById(`btn-next-${index}`).style.display = 'flex';
      }

    } catch (err) {
      console.error(err);
      showGenericError(index, 'Network error. Please try again.');
      btn.disabled = false;
    } finally {
      spinner.style.display = 'none';
      icon.style.display    = 'block';
    }
  }

  /* ──────────────────────────────────────────────────────────
     RENDER RESULTS
  ─────────────────────────────────────────────────────────── */
  function renderResults(index, data) {
    const resultEl = document.getElementById(`result-${index}`);
    const casesEl  = document.getElementById(`result-cases-${index}`);
    const verdictEl= document.getElementById(`verdict-${index}`);
    const scoreEl  = document.getElementById(`score-${index}`);
    const xpEl     = document.getElementById(`xp-label-${index}`);
    const stderrEl = document.getElementById(`stderr-${index}`);

    // Verdict
    const verdictMap = {
      passed: ['verdict-pass',  '✓ All Passed'],
      failed: ['verdict-fail',  '✗ Some Failed'],
      error:  ['verdict-error', '⚠ Runtime Error'],
    };
    const [cls, label] = verdictMap[data.status] ?? ['verdict-fail', 'Unknown'];
    verdictEl.className = `page-coding-verdict ${cls}`;
    verdictEl.textContent = label;

    scoreEl.textContent = `${data.tests_passed} / ${data.tests_total} tests passed`;
    xpEl.textContent    = data.xp_earned > 0 ? `+${data.xp_earned} XP` : '';

    // Inline test case decorations
    data.results.forEach(r => {
      const tcEl  = document.getElementById(`tc-${r.test_case_id}`);
      const rowEl = document.getElementById(`tc-actual-row-${r.test_case_id}`);
      const actEl = document.getElementById(`tc-actual-${r.test_case_id}`);
      const badEl = document.getElementById(`tc-badge-${r.test_case_id}`);

      if (!tcEl) return; // hidden test case

      tcEl.classList.remove('tc-pass','tc-fail','tc-error');
      tcEl.classList.add(r.status === 'passed' ? 'tc-pass' : r.status === 'error' ? 'tc-error' : 'tc-fail');

      if (r.actual !== null) {
        rowEl.style.display = 'flex';
        actEl.textContent   = r.actual;
      }
      badEl.className   = `page-coding-tc-badge ${r.status === 'passed' ? 'tc-badge-pass' : r.status === 'error' ? 'tc-badge-error' : 'tc-badge-fail'}`;
      badEl.textContent = r.status.toUpperCase();
    });

    // Result cards (hidden test cases)
    casesEl.innerHTML = '';
    data.results.filter(r => r.is_hidden).forEach((r, i) => {
      const card = document.createElement('div');
      card.className = `page-coding-tc ${r.status === 'passed' ? 'tc-pass' : r.status === 'error' ? 'tc-error' : 'tc-fail'}`;
      card.innerHTML = `
        <div class="page-coding-tc-row">
          <span class="page-coding-tc-key">Hidden #${i + 1}</span>
          <span class="page-coding-tc-badge ${r.status === 'passed' ? 'tc-badge-pass' : r.status === 'error' ? 'tc-badge-error' : 'tc-badge-fail'}">${r.status.toUpperCase()}</span>
        </div>`;
      casesEl.appendChild(card);
    });

    // Stderr
    const stderr = data.results.find(r => r.stderr)?.stderr;
    if (stderr) {
      stderrEl.style.display = 'block';
      stderrEl.textContent   = stderr;
    } else {
      stderrEl.style.display = 'none';
    }

    // Open panel
    resultEl.classList.add('is-open');
  }

  /* ──────────────────────────────────────────────────────────
     HELPERS
  ─────────────────────────────────────────────────────────── */
  function shakeEditor(index) {
    const el = document.getElementById(`editor-${index}`);
    el.style.outline = '2px solid var(--warn)';
    setTimeout(() => { el.style.outline = ''; }, 800);
  }

  function showGenericError(index, msg) {
    const resultEl = document.getElementById(`result-${index}`);
    const verdictEl= document.getElementById(`verdict-${index}`);
    const stderrEl = document.getElementById(`stderr-${index}`);
    document.getElementById(`score-${index}`).textContent = '';
    verdictEl.className   = 'page-coding-verdict verdict-error';
    verdictEl.textContent = '⚠ Error';
    stderrEl.style.display = 'block';
    stderrEl.textContent   = msg;
    resultEl.classList.add('is-open');
  }

  /* Tab key inside textarea */
  function handleTab(e) {
    if (e.key !== 'Tab') return;
    e.preventDefault();
    const ta    = e.target;
    const start = ta.selectionStart;
    const end   = ta.selectionEnd;
    ta.value    = ta.value.substring(0, start) + '    ' + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + 4;
  }

  /* Drag-to-resize problem pane */
  document.querySelectorAll('.page-coding-resizer').forEach(handle => {
    handle.addEventListener('mousedown', e => {
      e.preventDefault();
      const panel   = handle.previousElementSibling; // .page-coding-problem
      const startX  = e.clientX;
      const startW  = panel.offsetWidth;

      const onMove = ev => {
        const newW = Math.max(200, Math.min(600, startW + ev.clientX - startX));
        panel.style.width = newW + 'px';
      };
      const onUp = () => {
        window.removeEventListener('mousemove', onMove);
        window.removeEventListener('mouseup',  onUp);
      };
      window.addEventListener('mousemove', onMove);
      window.addEventListener('mouseup',   onUp);
    });
  });

  /* Warn before leaving if unsaved */
  window.addEventListener('beforeunload', e => {
    if (submittedFor.size < TOTAL) {
      e.preventDefault();
      e.returnValue = '';
    }
  });
</script>

</body>
</html>