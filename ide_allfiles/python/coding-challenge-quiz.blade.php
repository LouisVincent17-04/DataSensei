<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DataSensei — {{ $challenge->title }}</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
      --border:      #1e2f47;
      --border-h:    #2c4168;
      --accent:      #3b82f6;
      --accent-h:    #2563eb;
      --green:       #10b981;
      --green-bg:    rgba(16,185,129,0.10);
      --green-bd:    rgba(16,185,129,0.25);
      --red:         #ef4444;
      --red-bg:      rgba(239,68,68,0.10);
      --red-bd:      rgba(239,68,68,0.25);
      --amber:       #f59e0b;
      --amber-bg:    rgba(245,158,11,0.10);
      --amber-bd:    rgba(245,158,11,0.25);
      --text:        #fafafa;
      --muted:       #7f93b0;
      --dim:         #3d5272;
      --radius:      10px;
      --mono:        'JetBrains Mono', monospace;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }
    .layout { display: flex; min-height: 100vh; }

    /* ── MAIN SHELL ── */
    .coding-main { flex: 1; display: flex; flex-direction: column; min-width: 0; height: 100vh; overflow: hidden; }

    /* ── HEADER ── */
    .coding-header {
      flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; gap: 16px;
      padding: 10px 24px; border-bottom: 1px solid var(--border);
      background: rgba(13,19,32,0.98); backdrop-filter: blur(8px); z-index: 50;
    }
    .coding-breadcrumb { font-size: 0.68rem; color: var(--dim); text-transform: uppercase; letter-spacing: .08em; font-weight: 600; margin-bottom: 2px; }
    .coding-breadcrumb a { color: var(--muted); text-decoration: none; }
    .coding-breadcrumb a:hover { color: var(--text); }
    .coding-title { font-size: 1rem; font-weight: 700; }

    .coding-header-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }

    /* Nav dots */
    .q-dots { display: flex; gap: 6px; align-items: center; }
    .q-dot {
      width: 11px; height: 11px; border-radius: 50%;
      background: var(--surface2); border: 1.5px solid var(--border);
      cursor: pointer; transition: all .2s; position: relative;
    }
    .q-dot:hover { border-color: var(--border-h); }
    .q-dot.active { background: var(--accent); border-color: var(--accent); box-shadow: 0 0 8px rgba(59,130,246,.5); }
    .q-dot.done   { background: var(--green); border-color: var(--green); }
    .q-dot.failed { background: var(--red); border-color: var(--red); }
    .q-dot.locked { opacity: .35; cursor: not-allowed; }

    /* Timer */
    .coding-timer {
      font-family: var(--mono); font-size: 1.15rem; font-weight: 700;
      display: flex; align-items: center; gap: 7px;
      color: var(--accent); background: var(--surface);
      padding: 6px 14px; border-radius: 8px; border: 1px solid var(--border);
    }
    .coding-timer.urgent    { color: var(--red);   border-color: var(--red-bd);   animation: blink 1s step-end infinite; }
    .coding-timer.hidden    { visibility: hidden; }
    .coding-timer.finished  { color: var(--green); border-color: var(--green-bd); animation: none; background: var(--green-bg); }
    .timer-label { font-size: .6rem; text-transform: uppercase; letter-spacing: .08em; opacity: .75; display: none; margin-right: 2px; }
    .coding-timer.finished .timer-label { display: inline; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }

    /* ── BODY: SPLIT PANE ── */
    .coding-body { flex: 1; display: flex; overflow: hidden; }

    /* ── QUESTION SWITCHER ── */
    .q-panel { display: none; flex: 1; overflow: hidden; }
    .q-panel.active { display: flex; }

    /* ── LEFT: PROBLEM ── */
    .problem-pane {
      width: 360px; min-width: 240px; max-width: 560px; flex-shrink: 0;
      display: flex; flex-direction: column;
      border-right: 1px solid var(--border); overflow-y: auto;
    }
    .problem-body { padding: 22px 20px; flex: 1; }
    .problem-qnum { font-size: .68rem; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
    .problem-title { font-size: .95rem; font-weight: 700; color: var(--text); line-height: 1.5; margin-bottom: 14px; white-space: pre-wrap; }

    /* Sample test cases */
    .tc-list { margin-top: 18px; }
    .tc-list-label { font-size: .68rem; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px; }
    .tc-card {
      font-family: var(--mono); font-size: .78rem;
      background: var(--surface2); border: 1px solid var(--border);
      border-radius: 6px; padding: 10px 12px; margin-bottom: 7px;
      transition: border-color .2s, background .2s;
    }
    .tc-card.tc-pass  { border-color: var(--green-bd); background: var(--green-bg); }
    .tc-card.tc-fail  { border-color: var(--red-bd);   background: var(--red-bg); }
    .tc-card.tc-error { border-color: var(--amber-bd); background: var(--amber-bg); }
    .tc-row { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 3px; }
    .tc-row:last-child { margin-bottom: 0; }
    .tc-key { color: var(--dim); font-weight: 700; min-width: 62px; flex-shrink: 0; }
    .tc-val { color: var(--muted); word-break: break-all; white-space: pre-wrap; }
    .tc-badge { font-size: .6rem; font-weight: 700; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; flex-shrink: 0; }
    .badge-pass  { background: rgba(16,185,129,.2); color: var(--green); }
    .badge-fail  { background: rgba(239,68,68,.2);  color: var(--red); }
    .badge-error { background: rgba(245,158,11,.2); color: var(--amber); }

    /* Meta pills */
    .problem-meta { padding: 0 20px 16px; display: flex; flex-wrap: wrap; gap: 7px; }
    .pill { font-size: .7rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
    .pill-xp   { background: var(--amber-bg); color: var(--amber); border: 1px solid var(--amber-bd); }
    .pill-lang { background: rgba(59,130,246,.1); color: var(--accent); border: 1px solid rgba(59,130,246,.25); }
    .pill-time { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-bd); }
    .pill-done { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-bd); }

    /* Locked overlay */
    .locked-overlay {
      position: absolute; inset: 0; background: rgba(13,19,32,.85);
      display: flex; flex-direction: column; align-items: center; justify-content: center;
      gap: 12px; z-index: 10; backdrop-filter: blur(2px);
    }
    .locked-overlay .lock-icon { font-size: 2rem; }
    .locked-overlay .lock-msg  { font-size: .9rem; color: var(--muted); font-weight: 600; }

    /* ── RESIZE HANDLE ── */
    .resizer { width: 4px; flex-shrink: 0; cursor: col-resize; background: var(--border); transition: background .15s; }
    .resizer:hover { background: var(--accent); }

    /* ── RIGHT: EDITOR PANE ── */
    .editor-pane { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; position: relative; }

    /* Toolbar */
    .editor-toolbar {
      flex-shrink: 0; display: flex; align-items: center; gap: 8px;
      padding: 8px 14px; background: var(--surface); border-bottom: 1px solid var(--border);
    }
    .toolbar-lang { font-family: var(--mono); font-size: .75rem; color: var(--muted); display: flex; align-items: center; gap: 5px; }
    .lang-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); }
    .toolbar-spacer { flex: 1; }

    /* Buttons */
    .btn {
      font-family: 'Inter', sans-serif; font-size: .8rem; font-weight: 600;
      padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer;
      display: inline-flex; align-items: center; gap: 6px; transition: all .18s; white-space: nowrap;
    }
    .btn:disabled { opacity: .45; cursor: default; }
    .btn-run    { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .btn-run:hover:not(:disabled) { color: var(--text); border-color: var(--border-h); }
    .btn-submit { background: var(--accent); color: #fff; box-shadow: 0 2px 10px rgba(59,130,246,.3); }
    .btn-submit:hover:not(:disabled) { background: var(--accent-h); transform: translateY(-1px); }
    .btn-next   { background: var(--green); color: #fff; display: none; }
    .btn-next:hover { background: #0da271; }
    .btn-finish { background: var(--green); color: #fff; display: none; }
    .btn-finish:hover { background: #0da271; }
    .btn-retry  { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); display: none; }
    .btn-retry:hover { color: var(--text); border-color: var(--border-h); }

    /* Spinner */
    .spinner { display: none; width: 13px; height: 13px; border: 2px solid rgba(255,255,255,.25); border-top-color: #fff; border-radius: 50%; animation: spin .65s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── EDITOR AREA ── */
    .editor-wrap { flex: 1; display: flex; min-height: 0; position: relative; }
    .code-editor {
      flex: 1; resize: none; outline: none; border: none;
      background: #080f1c; color: #dce7f7;
      font-family: var(--mono); font-size: .85rem; line-height: 1.7;
      padding: 18px 20px; tab-size: 4; caret-color: var(--accent);
    }
    .code-editor::selection { background: rgba(59,130,246,.25); }
    .code-editor:disabled { opacity: .6; cursor: not-allowed; }

    /* ── STDIN / CUSTOM INPUT ── */
    .stdin-wrap {
      flex-shrink: 0; border-top: 1px solid var(--border);
      background: var(--surface); max-height: 0; overflow: hidden;
      transition: max-height .3s ease;
    }
    .stdin-wrap.open { max-height: 120px; }
    .stdin-inner { padding: 8px 14px; }
    .stdin-label { font-size: .68rem; font-weight: 700; color: var(--dim); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 5px; }
    .stdin-textarea {
      width: 100%; resize: none; outline: none; border: 1px solid var(--border);
      background: var(--bg); color: var(--muted);
      font-family: var(--mono); font-size: .78rem; line-height: 1.5;
      padding: 6px 10px; border-radius: 4px; height: 64px;
    }
    .stdin-textarea:focus { border-color: var(--accent); color: var(--text); }

    /* ── OUTPUT PANEL ── */
    .output-wrap {
      flex-shrink: 0; border-top: 1px solid var(--border);
      background: var(--surface); max-height: 0; overflow: hidden;
      transition: max-height .35s cubic-bezier(.4,0,.2,1);
    }
    .output-wrap.open { max-height: 320px; overflow-y: auto; }
    .output-inner { padding: 12px 16px; }

    /* Output tabs */
    .output-tabs { display: flex; gap: 4px; margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 6px; }
    .output-tab {
      font-size: .72rem; font-weight: 600; padding: 3px 10px; border-radius: 4px 4px 0 0; cursor: pointer;
      color: var(--muted); background: none; border: none; transition: all .15s;
    }
    .output-tab.active { color: var(--text); background: var(--surface2); }

    /* Output content areas */
    .output-section { display: none; }
    .output-section.active { display: block; }

    /* Run output */
    .run-output-box {
      font-family: var(--mono); font-size: .8rem; color: #c9d8ef;
      background: var(--bg); border: 1px solid var(--border);
      border-radius: 5px; padding: 10px 12px; white-space: pre-wrap;
      word-break: break-all; min-height: 36px; max-height: 180px; overflow-y: auto;
    }
    .run-output-box.is-error { color: #f87171; border-color: var(--red-bd); background: var(--red-bg); }

    /* Matplotlib image */
    .plot-img { max-width: 100%; border-radius: 6px; margin-top: 8px; border: 1px solid var(--border); display: none; }

    /* Submit results */
    .result-header { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
    .result-title { font-size: .85rem; font-weight: 700; }
    .verdict {
      font-size: .7rem; font-weight: 700; padding: 3px 10px; border-radius: 20px;
      text-transform: uppercase; letter-spacing: .05em;
    }
    .verdict-pass  { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-bd); }
    .verdict-fail  { background: var(--red-bg);   color: var(--red);   border: 1px solid var(--red-bd); }
    .verdict-error { background: var(--amber-bg); color: var(--amber); border: 1px solid var(--amber-bd); }
    .result-score  { font-size: .78rem; color: var(--muted); }
    .result-xp     { font-size: .78rem; font-weight: 700; color: var(--amber); }
    .hidden-cases  { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 6px; }

    /* Stderr */
    .stderr-box {
      margin-top: 8px; padding: 10px 12px; border-radius: 5px;
      background: var(--red-bg); border: 1px solid var(--red-bd);
      font-family: var(--mono); font-size: .75rem; color: #f87171;
      white-space: pre-wrap; word-break: break-all;
    }

    /* ── COMPLETION MODAL ── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,.75); z-index: 9999;
      display: flex; align-items: center; justify-content: center;
      backdrop-filter: blur(4px); opacity: 0; pointer-events: none; transition: opacity .25s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 36px 32px; max-width: 420px; width: 90%;
      text-align: center; transform: scale(.92); transition: transform .25s;
      box-shadow: 0 24px 60px rgba(0,0,0,.6);
    }
    .modal-overlay.open .modal-card { transform: scale(1); }
    .modal-trophy { font-size: 3rem; margin-bottom: 12px; }
    .modal-title { font-size: 1.4rem; font-weight: 700; margin-bottom: 8px; }
    .modal-sub   { font-size: .9rem; color: var(--muted); margin-bottom: 24px; line-height: 1.6; }
    .modal-xp    { font-size: 1.1rem; font-weight: 700; color: var(--amber); margin-bottom: 24px; }
    .modal-btn   { display: inline-block; padding: 12px 28px; background: var(--accent); color: #fff; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: .95rem; transition: background .18s; }
    .modal-btn:hover { background: var(--accent-h); }

    /* ── FOOTER ── */
    .coding-footer {
      flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; gap: 12px;
      padding: 10px 24px; border-top: 1px solid var(--border);
      background: rgba(13,19,32,.98);
    }
    .footer-info { font-size: .78rem; color: var(--muted); }
    .footer-info strong { color: var(--text); }
    .footer-nav { display: flex; gap: 8px; }
    .btn-nav { background: var(--surface2); color: var(--muted); border: 1px solid var(--border); }
    .btn-nav:hover:not(:disabled) { color: var(--text); border-color: var(--border-h); }
    .btn-nav:disabled { opacity: .3; cursor: default; }
    .btn-finish-footer {
      background: var(--green); color: #fff; display: none;
      box-shadow: 0 2px 12px rgba(16,185,129,.35); font-size: .88rem;
    }
    .btn-finish-footer:hover { background: #0da271; transform: translateY(-1px); }

    @media (max-width: 900px) {
      .q-panel { flex-direction: column; }
      .problem-pane { width: 100%; max-width: 100%; border-right: none; border-bottom: 1px solid var(--border); max-height: 250px; }
      .resizer { display: none; }
    }
  </style>
</head>
<body>
<div class="layout">
  @include('partials.sidebar')

  <div class="coding-main">

    {{-- ── HEADER ── --}}
    <header class="coding-header">
      <div>
        <div class="coding-breadcrumb">
          <a href="{{ route('challenges.coding') }}">Coding Challenges</a>
          &nbsp;›&nbsp;
          <a href="{{ route('challenges.coding.map', $slug) }}">{{ ucwords(str_replace('-', ' ', $slug)) }}</a>
          &nbsp;› Quiz
        </div>
        <div class="coding-title">{{ $challenge->title }}</div>
      </div>
      <div class="coding-header-right">
        <div class="q-dots" id="navDots">
          @foreach($challenge->codingQuestions as $i => $q)
            @php
              $qState  = $attempts[$q->id]['state'] ?? 'locked';
              $dotCls  = match($qState) {
                'done'   => 'done',
                'active' => ($i === 0 ? 'active' : ''),
                default  => 'locked',
              };
            @endphp
            <div class="q-dot {{ $dotCls }}"
                 data-index="{{ $i }}"
                 data-state="{{ $qState }}"
                 title="Q{{ $i + 1 }}{{ $qState === 'locked' ? ' (locked)' : '' }}"
                 onclick="gotoQ({{ $i }})"></div>
          @endforeach
        </div>
        <div class="coding-timer" id="timerEl">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span class="timer-label" id="timerLabel">Elapsed</span>
          <span id="timerTxt">--:--</span>
        </div>
      </div>
    </header>

    {{-- ── QUESTION PANELS ── --}}
    <div class="coding-body">
      @foreach($challenge->codingQuestions as $i => $question)
        @php
          $prior      = $priorSubmissions[$question->id] ?? null;
          $qState     = $attempts[$question->id]['state'] ?? 'locked';
          $isDone     = $qState === 'done';
          $isLocked   = $qState === 'locked';
          $isActive   = $i === ($activeIdx ?? 0) && !$isDone;
          $runUrl     = route('challenges.coding.run',    ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]);
          $subUrl     = route('challenges.coding.submit', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]);
        @endphp

        <div class="q-panel {{ $i === 0 ? 'active' : '' }}"
             id="qpanel-{{ $i }}"
             data-index="{{ $i }}"
             data-qid="{{ $question->id }}"
             data-timelimit="{{ $question->time_limit_seconds }}"
             data-run-url="{{ $runUrl }}"
             data-sub-url="{{ $subUrl }}"
             data-state="{{ $qState }}"
             data-done="{{ $isDone ? '1' : '0' }}">

          {{-- ── LEFT: PROBLEM ── --}}
          <div class="problem-pane" id="problemPane-{{ $i }}">
            <div class="problem-body">
              <div class="problem-qnum">Question {{ $i + 1 }} of {{ $challenge->codingQuestions->count() }}</div>
              <div class="problem-title">{{ $question->problem_description }}</div>

              @if($question->visibleTestCases->isNotEmpty())
                <div class="tc-list">
                  <div class="tc-list-label">Sample Test Cases</div>
                  @foreach($question->visibleTestCases as $tc)
                    <div class="tc-card" id="tc-{{ $tc->id }}">
                      @if($tc->input !== null)
                        <div class="tc-row">
                          <span class="tc-key">Input:</span>
                          <span class="tc-val">{{ $tc->input }}</span>
                        </div>
                      @endif
                      <div class="tc-row">
                        <span class="tc-key">Expected:</span>
                        <span class="tc-val">{{ $tc->expected_output }}</span>
                      </div>
                      <div class="tc-row" id="tc-got-row-{{ $tc->id }}" style="display:none">
                        <span class="tc-key">Got:</span>
                        <span class="tc-val" id="tc-got-{{ $tc->id }}"></span>
                        <span class="tc-badge" id="tc-badge-{{ $tc->id }}"></span>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
            </div>

            <div class="problem-meta">
              <span class="pill pill-xp">⚡ {{ $question->base_xp }} XP</span>
              <span class="pill pill-lang">🐍 Python</span>
              <span class="pill pill-time">⏱ {{ intval($question->time_limit_seconds / 60) }}m</span>
              @if($isDone)
                <span class="pill pill-done">✓ Solved</span>
              @endif
            </div>
          </div>

          <div class="resizer" data-pane="{{ $i }}"></div>

          {{-- ── RIGHT: EDITOR ── --}}
          <div class="editor-pane">

            {{-- Locked overlay for future questions --}}
            @if($isLocked)
              <div class="locked-overlay">
                <div class="lock-icon">🔒</div>
                <div class="lock-msg">Complete Question {{ $i }} first to unlock this question.</div>
              </div>
            @endif

            {{-- Toolbar --}}
            <div class="editor-toolbar">
              <div class="toolbar-lang"><div class="lang-dot"></div> Python 3</div>
              <div class="toolbar-spacer"></div>

              {{-- Toggle stdin (hidden for done/locked) --}}
              @if(!$isDone && !$isLocked)
                <button class="btn btn-run" onclick="toggleStdin({{ $i }})" title="Custom input for Run">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  stdin
                </button>
              @endif

              {{-- Run button --}}
              <button class="btn btn-run" id="btn-run-{{ $i }}" onclick="runCode({{ $i }})" {{ ($isDone || $isLocked) ? 'disabled' : '' }}>
                <div class="spinner" id="run-spinner-{{ $i }}"></div>
                <svg id="run-icon-{{ $i }}" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3" fill="currentColor"/></svg>
                Run
              </button>

              {{-- Retry (shown after failed submit) --}}
              <button class="btn btn-retry" id="btn-retry-{{ $i }}" onclick="resetSubmit({{ $i }})">
                ↺ Retry
              </button>

              {{-- Next Question (shown after passing, if not last) --}}
              <button class="btn btn-next" id="btn-next-{{ $i }}" onclick="gotoQ({{ $i + 1 }})">
                Next →
              </button>

              {{-- Finish (shown after passing the last question) --}}
              <button class="btn btn-finish" id="btn-finish-{{ $i }}" onclick="showCompletion()">
                🎉 Finish
              </button>

              {{-- Submit button --}}
              <button class="btn btn-submit" id="btn-submit-{{ $i }}" onclick="submitCode({{ $i }})" {{ ($isDone || $isLocked) ? 'disabled' : '' }}>
                <div class="spinner" id="sub-spinner-{{ $i }}"></div>
                <svg id="sub-icon-{{ $i }}" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ $isDone ? '✓ Solved' : 'Run & Submit' }}
              </button>
            </div>

            {{-- Code editor --}}
            <div class="editor-wrap">
              <textarea class="code-editor" id="editor-{{ $i }}"
                        spellcheck="false"
                        placeholder="# Write your Python solution here..."
                        onkeydown="handleTab(event)"
                        {{ ($isDone || $isLocked) ? 'disabled' : '' }}>{{ $prior ? $prior->code : ($question->starter_code ?? '') }}</textarea>
            </div>

            {{-- stdin panel --}}
            <div class="stdin-wrap" id="stdin-wrap-{{ $i }}">
              <div class="stdin-inner">
                <div class="stdin-label">Custom stdin (used by Run only)</div>
                <textarea class="stdin-textarea" id="stdin-{{ $i }}" placeholder="e.g. 5&#10;hello world"></textarea>
              </div>
            </div>

            {{-- Output panel --}}
            <div class="output-wrap" id="output-wrap-{{ $i }}">
              <div class="output-inner">
                <div class="output-tabs">
                  <button class="output-tab active" id="tab-run-{{ $i }}"    onclick="switchTab({{ $i }}, 'run')">▶ Run Output</button>
                  <button class="output-tab"         id="tab-submit-{{ $i }}" onclick="switchTab({{ $i }}, 'submit')">✓ Test Results</button>
                </div>

                <div class="output-section active" id="sec-run-{{ $i }}">
                  <div class="run-output-box" id="run-out-{{ $i }}"></div>
                  <img class="plot-img" id="plot-img-{{ $i }}" alt="Matplotlib output"/>
                </div>

                <div class="output-section" id="sec-submit-{{ $i }}">
                  <div class="result-header">
                    <span class="result-title">Test Results</span>
                    <span class="verdict" id="verdict-{{ $i }}"></span>
                    <span class="result-score" id="result-score-{{ $i }}"></span>
                    <span class="result-xp" id="result-xp-{{ $i }}"></span>
                  </div>
                  <div class="hidden-cases" id="hidden-cases-{{ $i }}"></div>
                  <div class="stderr-box" id="stderr-{{ $i }}" style="display:none"></div>
                </div>
              </div>
            </div>

          </div>{{-- editor-pane --}}
        </div>{{-- q-panel --}}
      @endforeach
    </div>{{-- coding-body --}}

    {{-- ── FOOTER ── --}}
    <footer class="coding-footer">
      <div class="footer-info">
        Question <strong id="footerQ">1</strong> of <strong>{{ $challenge->codingQuestions->count() }}</strong>
      </div>
      <div class="footer-nav">
        <button class="btn btn-nav" id="btnPrev" onclick="gotoQ(currentQ - 1)" disabled>← Prev</button>
        <button class="btn btn-nav" id="btnNext" onclick="gotoQ(currentQ + 1)" {{ $challenge->codingQuestions->count() <= 1 ? 'disabled' : '' }}>Next →</button>
        <button class="btn btn-finish-footer" id="btnFinishFooter" onclick="showCompletion()">🎉 Finish Challenge</button>
      </div>
    </footer>

  </div>{{-- coding-main --}}
</div>

{{-- ── COMPLETION MODAL ── --}}
<div class="modal-overlay" id="completionModal">
  <div class="modal-card">
    <div class="modal-trophy">🏆</div>
    <div class="modal-title">Challenge Complete!</div>
    <div class="modal-sub" id="modalSub">You've solved all problems in this challenge.</div>
    <div class="modal-xp" id="modalXp"></div>
    <a href="{{ route('challenges.coding.map', $slug) }}" class="modal-btn">Back to Map →</a>
  </div>
</div>

<script>
/* ══════════════════════════════════════════════════════════════════════════
   STATE
   ══════════════════════════════════════════════════════════════════════════ */
const TOTAL    = {{ $challenge->codingQuestions->count() }};
let   currentQ = 0;

// ── Question states from server ──────────────────────────────────────────
// state: 'done' | 'active' | 'locked'
// Only one question can be 'active' at a time.
// The active index is the first unsolved question.
const ACTIVE_IDX = {{ $activeIdx ?? 'null' }};

// Per-question metadata from server render
const Q_STATE = {
  @foreach($challenge->codingQuestions as $i => $question)
    {{ $i }}: '{{ $attempts[$question->id]['state'] ?? 'locked' }}',
  @endforeach
};

// Which questions have a live DB clock already (true = don't call start)
const hasAttempt = {
  @foreach($challenge->codingQuestions as $i => $question)
    {{ $i }}: {{ $attempts[$question->id]['has_attempt'] ? 'true' : 'false' }},
  @endforeach
};

// Server-rendered remaining seconds (only meaningful when has_attempt is true)
const serverRemaining = {
  @foreach($challenge->codingQuestions as $i => $question)
    {{ $i }}: {{ $attempts[$question->id]['remaining_seconds'] }},
  @endforeach
};

// Per-question prior submission states (for dot colours and qStates init)
const qStates = {};
@foreach($challenge->codingQuestions as $i => $question)
  @if(isset($priorSubmissions[$question->id]) && $priorSubmissions[$question->id]->status === 'passed')
    qStates[{{ $i }}] = {
      status: 'passed',
      passed: {{ $priorSubmissions[$question->id]->tests_passed }},
      total:  {{ $priorSubmissions[$question->id]->tests_total }},
      xp:     {{ $priorSubmissions[$question->id]->xp_earned }},
    };
  @endif
@endforeach

const submitted = new Set(
  {{ json_encode($priorSubmissions->where('status', 'passed')->keys()->toArray()) }}
);

/* ══════════════════════════════════════════════════════════════════════════
   TIMER
   ══════════════════════════════════════════════════════════════════════════

   Design rules:
     1. Only ONE timer is ever active: the current active question.
     2. Done questions have NO timer. Locked questions have NO timer.
     3. The timer is seeded ONCE from the server value — never re-seeded by
        API responses (Run, Submit, Ping). Those can only push it DOWN.
     4. The timer lives in memory as `activeTimer.remaining`.
        It is never stored in localStorage so there is no stale-seed risk.
     5. Ping syncs only downward (Math.min) — it can never inflate the timer.
     6. Submit and Run responses do NOT touch the timer at all.

   activeTimer = { remaining: N, questionIdx: N }  or  null
   ══════════════════════════════════════════════════════════════════════════ */

let   activeTimer    = null;   // the one live timer object
let   pingCounter    = 0;
let   challengeStartTime = null;  // wall-clock ms when the first question timer seeded
const PING_INTERVAL  = 30;     // seconds between server pings

const pingUrls = {
  @foreach($challenge->codingQuestions as $i => $question)
    {{ $i }}: '{{ route('challenges.coding.ping', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]) }}',
  @endforeach
};

const startUrls = {
  @foreach($challenge->codingQuestions as $i => $question)
    {{ $i }}: '{{ route('challenges.coding.start', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]) }}',
  @endforeach
};

/**
 * Seed the ONE active timer for question at idx.
 * Only called when we know the DB clock is already running (hasAttempt=true).
 * Will refuse to re-seed if already seeded for this question.
 */
function seedTimer(idx, remaining) {
  if (activeTimer !== null && activeTimer.questionIdx === idx) return; // already seeded — never re-seed
  if (Q_STATE[idx] !== 'active') return; // done/locked questions get no timer

  activeTimer  = { remaining: Math.max(0, remaining), questionIdx: idx };
  pingCounter  = 0;

  // Record wall-clock start for elapsed time calculation.
  // Back-calculate so that elapsed = timeLimit - remaining at this moment.
  if (challengeStartTime === null) {
    const panel    = document.querySelector(`[data-index="${idx}"]`);
    const limit    = parseInt(panel?.dataset.timelimit || 0);
    const elapsed  = Math.max(0, limit - remaining);
    challengeStartTime = Date.now() - (elapsed * 1000);
  }
}

/**
 * Clear the active timer (called when a question is completed).
 */
function clearTimer() {
  activeTimer = null;
  pingCounter = 0;
  // Hide/blank the display
  document.getElementById('timerTxt').textContent = '--:--';
  document.getElementById('timerEl').classList.remove('urgent');
  document.getElementById('timerEl').classList.add('hidden');
}

// Master tick — runs every second, drives the single active timer
setInterval(() => {
  if (activeTimer === null) return;
  if (activeTimer.remaining <= 0) return;

  activeTimer.remaining--;
  pingCounter++;

  // Update display only when this question is visible
  if (activeTimer.questionIdx === currentQ) {
    syncTimerDisplay();
  }

  // Periodic server re-sync
  if (pingCounter >= PING_INTERVAL) {
    pingCounter = 0;
    pingServer(activeTimer.questionIdx);
  }

  // Auto-submit on expiry
  if (activeTimer.remaining <= 0) {
    const idx   = activeTimer.questionIdx;
    const panel = document.getElementById(`qpanel-${idx}`);
    if (panel && panel.dataset.done !== '1') {
      submitCode(idx, true);
    }
  }
}, 1000);

async function pingServer(idx) {
  try {
    const resp = await fetch(pingUrls[idx], {
      headers: {
        'Accept':       'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    });
    if (!resp.ok) return;
    const data = await resp.json();

    // RULE: ping can only push the timer DOWN — never inflate it.
    // If the server returns a larger value (timezone bug / clock skew),
    // we trust the locally-counted value which is always correctly decreasing.
    if (activeTimer !== null && activeTimer.questionIdx === idx) {
      const serverVal = Math.max(0, data.remaining_seconds);
      activeTimer.remaining = Math.min(activeTimer.remaining, serverVal);
    }

    if (data.expired) {
      const panel = document.getElementById(`qpanel-${idx}`);
      if (panel && panel.dataset.done !== '1') {
        if (activeTimer) activeTimer.remaining = 0;
        submitCode(idx, true);
      }
    }
  } catch (_) { /* network blip — next ping will correct */ }
}

function syncTimerDisplay() {
  if (activeTimer === null || Q_STATE[activeTimer.questionIdx] !== 'active') {
    document.getElementById('timerTxt').textContent = '--:--';
    document.getElementById('timerEl').classList.remove('urgent');
    document.getElementById('timerEl').classList.add('hidden');
    return;
  }

  const s   = Math.max(0, activeTimer.remaining);
  const pad = n => String(n).padStart(2, '0');
  document.getElementById('timerTxt').textContent = pad(Math.floor(s / 60)) + ':' + pad(s % 60);
  document.getElementById('timerEl').classList.remove('hidden');
  document.getElementById('timerEl').classList.toggle('urgent', s <= 60 && s > 0);
}

function handleExpired(idx) {
  const panel = document.getElementById(`qpanel-${idx}`);
  if (!panel) return;
  panel.dataset.done = '1';
  document.getElementById(`btn-submit-${idx}`).disabled = true;
  const verdictEl = document.getElementById(`verdict-${idx}`);
  if (verdictEl) { verdictEl.className = 'verdict verdict-error'; verdictEl.textContent = '⏱ Time Expired'; }
  if (activeTimer && activeTimer.questionIdx === idx) activeTimer.remaining = 0;
  openOutput(idx);
  switchTab(idx, 'submit');
  syncTimerDisplay();
}

// ── Initialise timer on page load ────────────────────────────────────────
// Only the active question gets a timer.
// If it already has an attempt (DB clock running), seed from server value.
// Locked and done questions are silently skipped.
(function initTimer() {
  if (ACTIVE_IDX === null) {
    // All questions done — show elapsed time instead of countdown
    // challengeStartTime is unknown at this point so show '--:--' as finished
    const timerEl = document.getElementById('timerEl');
    timerEl.classList.remove('hidden', 'urgent');
    timerEl.classList.add('finished');
    document.getElementById('timerTxt').textContent = '--:--';
    document.getElementById('btnFinishFooter').style.display = 'inline-flex';
    return;
  }

  const idx = ACTIVE_IDX;
  if (hasAttempt[idx]) {
    seedTimer(idx, serverRemaining[idx]);
  }
  // If no attempt yet (edge case), seedTimer is called after the first gotoQ AJAX
  syncTimerDisplay();
})();

/* ══════════════════════════════════════════════════════════════════════════
   NAVIGATION
   ══════════════════════════════════════════════════════════════════════════

   Rules:
     • Done questions (state='done')    → allowed, read-only view
     • Active question (state='active') → allowed, editor live
     • Locked questions (state='locked')→ BLOCKED — show a notice, don't switch

   The timer is NOT re-seeded on navigation. It keeps counting.
   ══════════════════════════════════════════════════════════════════════════ */

async function gotoQ(idx) {
  if (idx < 0 || idx >= TOTAL) return;

  // Block navigation to locked questions
  if (Q_STATE[idx] === 'locked') {
    // The locked overlay already makes this obvious visually,
    // but prevent the panel switch entirely
    return;
  }

  // Deactivate current
  document.getElementById(`qpanel-${currentQ}`)?.classList.remove('active');
  dotUpdate(currentQ);

  currentQ = idx;

  document.getElementById(`qpanel-${currentQ}`).classList.add('active');
  dotUpdate(currentQ);
  document.getElementById('footerQ').textContent = currentQ + 1;
  document.getElementById('btnPrev').disabled    = currentQ === 0;
  // "Next" is disabled if: last question, OR the next question is locked
  document.getElementById('btnNext').disabled    =
    currentQ === TOTAL - 1 || Q_STATE[currentQ + 1] === 'locked';

  // If this is the active question and it has no DB clock yet, start it now
  if (Q_STATE[idx] === 'active' && !hasAttempt[idx] && activeTimer === null) {
    hasAttempt[idx] = true; // optimistic flag — prevent double-call on rapid clicks
    try {
      const resp = await fetch(startUrls[idx], {
        method:  'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept':       'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({}),
      });
      if (resp.ok) {
        const data = await resp.json();
        seedTimer(idx, data.remaining_seconds);
        if (data.expired) handleExpired(idx);
      }
    } catch (_) {
      // Network error — seed from display value as fallback
      seedTimer(idx, serverRemaining[idx]);
    }
  }

  // Show/hide the timer depending on which question we're on
  if (Q_STATE[idx] === 'active' && activeTimer !== null) {
    document.getElementById('timerEl').classList.remove('hidden');
  } else {
    document.getElementById('timerEl').classList.add('hidden');
  }

  syncTimerDisplay();
}

function dotUpdate(idx) {
  const dot = document.querySelector(`.q-dot[data-index="${idx}"]`);
  if (!dot) return;
  dot.classList.remove('active', 'done', 'failed');
  if (Q_STATE[idx] === 'done')   { dot.classList.add('done');   return; }
  if (Q_STATE[idx] === 'locked') { dot.classList.add('locked'); return; }
  // active question
  const st = qStates[idx];
  if (idx === currentQ)             dot.classList.add('active');
  else if (st?.status === 'passed') dot.classList.add('done');
  else if (st?.status === 'failed' || st?.status === 'error') dot.classList.add('failed');
}

// Initialise footer nav state
document.getElementById('btnNext').disabled =
  TOTAL <= 1 || Q_STATE[1] === 'locked';

/* ══════════════════════════════════════════════════════════════════════════
   STDIN TOGGLE
   ══════════════════════════════════════════════════════════════════════════ */
function toggleStdin(idx) {
  const wrap = document.getElementById(`stdin-wrap-${idx}`);
  wrap.classList.toggle('open');
}

/* ══════════════════════════════════════════════════════════════════════════
   OUTPUT TABS
   ══════════════════════════════════════════════════════════════════════════ */
function switchTab(idx, tab) {
  ['run', 'submit'].forEach(t => {
    document.getElementById(`tab-${t}-${idx}`)?.classList.toggle('active', t === tab);
    document.getElementById(`sec-${t}-${idx}`)?.classList.toggle('active', t === tab);
  });
}

function openOutput(idx) {
  document.getElementById(`output-wrap-${idx}`).classList.add('open');
}

/* ══════════════════════════════════════════════════════════════════════════
   RUN ONLY — does NOT affect timer or progress
   ══════════════════════════════════════════════════════════════════════════ */
async function runCode(idx) {
  const panel  = document.getElementById(`qpanel-${idx}`);
  const code   = document.getElementById(`editor-${idx}`).value;
  const stdin  = document.getElementById(`stdin-${idx}`)?.value ?? '';
  const url    = panel.dataset.runUrl;

  if (!code.trim()) { flashEditor(idx); return; }

  setRunLoading(idx, true);
  switchTab(idx, 'run');
  openOutput(idx);

  const outBox  = document.getElementById(`run-out-${idx}`);
  const plotImg = document.getElementById(`plot-img-${idx}`);
  outBox.textContent    = 'Running…';
  outBox.className      = 'run-output-box';
  plotImg.style.display = 'none';

  // Timer is intentionally NOT touched here — Run has zero effect on it

  try {
    const resp = await apiFetch(url, { code, input: stdin });
    if (!resp.ok) {
      const text = await resp.text();
      outBox.textContent = `Server error ${resp.status}: ${text.slice(0, 300)}`;
      outBox.classList.add('is-error');
      return;
    }
    const data = await resp.json();

    if (data.status === 'error') {
      outBox.textContent = data.stderr || data.output || '(runtime error — no output)';
      outBox.classList.add('is-error');
    } else {
      const out = (data.output ?? '').trimEnd();
      outBox.textContent = out.length > 0 ? out : '(no output)';
      if (data.image) {
        plotImg.src           = data.image;
        plotImg.style.display = 'block';
      }
    }
  } catch (e) {
    outBox.textContent = 'Network error: ' + e.message;
    outBox.classList.add('is-error');
  } finally {
    setRunLoading(idx, false);
    // Timer is NOT touched in finally either
  }
}

function setRunLoading(idx, on) {
  document.getElementById(`btn-run-${idx}`).disabled           = on;
  document.getElementById(`run-spinner-${idx}`).style.display  = on ? 'block' : 'none';
  document.getElementById(`run-icon-${idx}`).style.display     = on ? 'none'  : 'block';
}

/* ══════════════════════════════════════════════════════════════════════════
   SUBMIT
   ══════════════════════════════════════════════════════════════════════════

   Timer rules:
     • Timer NEVER stops, pauses, or resets during or after submit.
     • If the server response contains remaining_seconds, we IGNORE it.
       (The controller no longer sends it, but even if it did: ignored.)
     • The ONLY things that stop the timer are:
         (a) handleExpired() — time ran out
         (b) clearTimer()    — question was passed (question is now 'done')
   ══════════════════════════════════════════════════════════════════════════ */
async function submitCode(idx, auto = false) {
  const panel = document.getElementById(`qpanel-${idx}`);
  if (panel.dataset.done === '1') return;

  const code = document.getElementById(`editor-${idx}`).value;
  if (!code.trim() && !auto) { flashEditor(idx); return; }

  const url = panel.dataset.subUrl;

  setSubLoading(idx, true);
  switchTab(idx, 'submit');
  openOutput(idx);

  // ── Timer is NOT touched here ────────────────────────────────────────
  // The setInterval tick above continues counting regardless.

  try {
    const resp = await apiFetch(url, { code });
    const data = await resp.json();

    // Time expired — server confirms
    if (data.status === 'expired' || data.expired) {
      handleExpired(idx);
      return;
    }

    if (resp.status === 403 || resp.status === 422) {
      showOutputError(idx, data.error || 'Submission rejected by server.');
      return;
    }

    qStates[idx] = { status: data.status, passed: data.tests_passed, total: data.tests_total, xp: data.xp_earned };
    dotUpdate(idx);
    renderSubmitResults(idx, data);

    if (data.status === 'passed') {
      // ── Mark question done ──────────────────────────────────────────
      panel.dataset.done  = '1';
      panel.dataset.state = 'done';
      Q_STATE[idx]        = 'done';

      document.getElementById(`btn-submit-${idx}`).disabled    = true;
      document.getElementById(`btn-run-${idx}`).disabled       = true;
      document.getElementById(`editor-${idx}`).disabled        = true;
      document.getElementById(`sub-icon-${idx}`).style.display = 'block';

      submitted.add(parseInt(panel.dataset.qid));

      const meta = panel.querySelector('.problem-meta');
      if (meta && !meta.querySelector('.pill-done')) {
        const pill = document.createElement('span');
        pill.className   = 'pill pill-done';
        pill.textContent = '✓ Solved';
        meta.appendChild(pill);
      }

      // ── Stop this question's timer — it's done ──────────────────────
      clearTimer();

      // ── Unlock the next question ────────────────────────────────────
      const nextIdx = idx + 1;
      if (nextIdx < TOTAL) {
        Q_STATE[nextIdx] = 'active';

        // Update dot
        const nextDot = document.querySelector(`.q-dot[data-index="${nextIdx}"]`);
        if (nextDot) { nextDot.classList.remove('locked'); nextDot.dataset.state = 'active'; }

        // Remove locked overlay from the next panel
        const nextPanel = document.getElementById(`qpanel-${nextIdx}`);
        if (nextPanel) {
          nextPanel.dataset.state = 'active';
          const overlay = nextPanel.querySelector('.locked-overlay');
          if (overlay) overlay.remove();

          // Re-enable its editor and buttons
          const nextEditor = document.getElementById(`editor-${nextIdx}`);
          if (nextEditor) nextEditor.disabled = false;
          const nextRun    = document.getElementById(`btn-run-${nextIdx}`);
          if (nextRun)    nextRun.disabled = false;
          const nextSub    = document.getElementById(`btn-submit-${nextIdx}`);
          if (nextSub)    nextSub.disabled = false;
        }

        // "Next" footer button can now be used
        if (currentQ === idx) {
          document.getElementById('btnNext').disabled = false;
        }

        // Show "Next →" in toolbar
        document.getElementById(`btn-next-${idx}`).style.display = 'inline-flex';
      } else {
        // Last question passed
        document.getElementById(`btn-finish-${idx}`).style.display = 'inline-flex';
      }

      if (data.challenge_complete) {
        setTimeout(() => showCompletion(data), 900);
      }

    } else {
      // ── Failed/error — stay on this question, timer keeps running ───
      // Retry button appears so user can dismiss results and try again.
      document.getElementById(`btn-retry-${idx}`).style.display = 'inline-flex';

      // ── Timer is deliberately NOT touched here ──────────────────────
    }

  } catch (e) {
    showOutputError(idx, 'Network error: ' + e.message);
  } finally {
    // Re-enable submit only if still not passed
    const panel2 = document.getElementById(`qpanel-${idx}`);
    if (panel2.dataset.done !== '1') {
      setSubLoading(idx, false);
    } else {
      // Keep it disabled — question is solved
      document.getElementById(`sub-spinner-${idx}`).style.display = 'none';
    }
  }
}

function setSubLoading(idx, on) {
  document.getElementById(`btn-submit-${idx}`).disabled         = on;
  document.getElementById(`sub-spinner-${idx}`).style.display   = on ? 'block' : 'none';
  document.getElementById(`sub-icon-${idx}`).style.display      = on ? 'none'  : 'block';
}

function resetSubmit(idx) {
  // Re-enables the editor after a failed submit so user can retry.
  // Timer is deliberately NOT touched.
  const panel = document.getElementById(`qpanel-${idx}`);
  panel.dataset.done = '0';
  document.getElementById(`btn-submit-${idx}`).disabled     = false;
  document.getElementById(`btn-retry-${idx}`).style.display = 'none';
  document.getElementById(`output-wrap-${idx}`).classList.remove('open');
  panel.querySelectorAll('.tc-card').forEach(c => c.classList.remove('tc-pass', 'tc-fail', 'tc-error'));
  panel.querySelectorAll('[id^="tc-got-row-"]').forEach(r => r.style.display = 'none');
  // Timer intentionally left alone
}

/* ══════════════════════════════════════════════════════════════════════════
   RENDER SUBMIT RESULTS
   ══════════════════════════════════════════════════════════════════════════ */
function renderSubmitResults(idx, data) {
  const verdictEl = document.getElementById(`verdict-${idx}`);
  const scoreEl   = document.getElementById(`result-score-${idx}`);
  const xpEl      = document.getElementById(`result-xp-${idx}`);
  const hiddenEl  = document.getElementById(`hidden-cases-${idx}`);
  const stderrEl  = document.getElementById(`stderr-${idx}`);

  const verdictMap = {
    passed: ['verdict-pass',  '✓ All Passed'],
    failed: ['verdict-fail',  '✗ Some Failed'],
    error:  ['verdict-error', '⚠ Runtime Error'],
  };
  const [cls, label] = verdictMap[data.status] ?? ['verdict-fail', 'Unknown'];
  verdictEl.className   = `verdict ${cls}`;
  verdictEl.textContent = label;
  scoreEl.textContent   = `${data.tests_passed} / ${data.tests_total} tests`;
  xpEl.textContent      = data.xp_earned > 0 ? `+${data.xp_earned} XP` : '';

  (data.results || []).forEach(r => {
    const card   = document.getElementById(`tc-${r.test_case_id}`);
    const gotRow = document.getElementById(`tc-got-row-${r.test_case_id}`);
    const gotVal = document.getElementById(`tc-got-${r.test_case_id}`);
    const badge  = document.getElementById(`tc-badge-${r.test_case_id}`);
    if (!card) return;

    card.classList.remove('tc-pass', 'tc-fail', 'tc-error');
    card.classList.add(r.status === 'passed' ? 'tc-pass' : r.status === 'error' ? 'tc-error' : 'tc-fail');

    if (r.actual !== null && gotRow) {
      gotRow.style.display = 'flex';
      gotVal.textContent   = r.actual;
    }
    if (badge) {
      badge.className   = `tc-badge ${r.status === 'passed' ? 'badge-pass' : r.status === 'error' ? 'badge-error' : 'badge-fail'}`;
      badge.textContent = r.status.toUpperCase();
    }
  });

  hiddenEl.innerHTML = '';
  (data.results || []).filter(r => r.is_hidden).forEach((r, i) => {
    const d = document.createElement('div');
    d.className = `tc-card ${r.status === 'passed' ? 'tc-pass' : r.status === 'error' ? 'tc-error' : 'tc-fail'}`;
    d.innerHTML = `<div class="tc-row">
      <span class="tc-key">Hidden #${i + 1}</span>
      <span class="tc-badge ${r.status === 'passed' ? 'badge-pass' : r.status === 'error' ? 'badge-error' : 'badge-fail'}">${r.status.toUpperCase()}</span>
    </div>`;
    hiddenEl.appendChild(d);
  });

  const stderr = (data.results || []).find(r => r.stderr)?.stderr;
  if (stderr) { stderrEl.style.display = 'block'; stderrEl.textContent = stderr; }
  else          { stderrEl.style.display = 'none'; }
}

function showOutputError(idx, msg) {
  switchTab(idx, 'submit');
  openOutput(idx);
  const verdictEl = document.getElementById(`verdict-${idx}`);
  const stderrEl  = document.getElementById(`stderr-${idx}`);
  verdictEl.className   = 'verdict verdict-error';
  verdictEl.textContent = '⚠ Error';
  document.getElementById(`result-score-${idx}`).textContent = '';
  stderrEl.style.display = 'block';
  stderrEl.textContent   = msg;
}

/* ══════════════════════════════════════════════════════════════════════════
   COMPLETION MODAL
   ══════════════════════════════════════════════════════════════════════════ */
function showCompletion() {
  const totalXp = Object.values(qStates).reduce((s, v) => s + (v?.xp || 0), 0);
  document.getElementById('modalXp').textContent = `+${totalXp} XP earned`;
  document.getElementById('completionModal').classList.add('open');

  // Switch the header timer to elapsed display
  showElapsedTimer();
}

/**
 * Replace the countdown timer in the header with a green "Elapsed" display.
 * Called once when the challenge is fully complete.
 */
function showElapsedTimer() {
  const elapsedMs   = challengeStartTime !== null ? (Date.now() - challengeStartTime) : 0;
  const elapsedSecs = Math.max(0, Math.floor(elapsedMs / 1000));
  const pad = n => String(n).padStart(2, '0');

  document.getElementById('timerTxt').textContent =
    pad(Math.floor(elapsedSecs / 60)) + ':' + pad(elapsedSecs % 60);

  const timerEl = document.getElementById('timerEl');
  timerEl.classList.remove('hidden', 'urgent');
  timerEl.classList.add('finished');   // green style + shows "Elapsed" label

  // Show footer Finish button
  document.getElementById('btnFinishFooter').style.display = 'inline-flex';
}

/* ══════════════════════════════════════════════════════════════════════════
   UTILITIES
   ══════════════════════════════════════════════════════════════════════════ */
async function apiFetch(url, body) {
  return fetch(url, {
    method:  'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept':       'application/json',
    },
    body: JSON.stringify(body),
  });
}

function flashEditor(idx) {
  const el = document.getElementById(`editor-${idx}`);
  el.style.outline = '2px solid var(--red)';
  setTimeout(() => el.style.outline = '', 700);
}

function handleTab(e) {
  if (e.key !== 'Tab') return;
  e.preventDefault();
  const ta = e.target, s = ta.selectionStart, en = ta.selectionEnd;
  ta.value = ta.value.slice(0, s) + '    ' + ta.value.slice(en);
  ta.selectionStart = ta.selectionEnd = s + 4;
}

// Drag-to-resize problem pane
document.querySelectorAll('.resizer').forEach(handle => {
  handle.addEventListener('mousedown', e => {
    e.preventDefault();
    const pane = handle.previousElementSibling;
    const startX = e.clientX, startW = pane.offsetWidth;
    const onMove = ev => { pane.style.width = Math.max(200, Math.min(600, startW + ev.clientX - startX)) + 'px'; };
    const onUp   = () => { window.removeEventListener('mousemove', onMove); window.removeEventListener('mouseup', onUp); };
    window.addEventListener('mousemove', onMove);
    window.addEventListener('mouseup', onUp);
  });
});

// Warn on leave if not all done
window.addEventListener('beforeunload', e => {
  const allDone = TOTAL === Object.values(qStates).filter(s => s?.status === 'passed').length;
  if (!allDone) { e.preventDefault(); e.returnValue = ''; }
});

// ── Initialise nav state ─────────────────────────────────────────────────
// Dots are rendered by PHP; just make sure the initial Q=0 panel is shown
// and the active question dot is marked correctly.
for (let i = 0; i < TOTAL; i++) dotUpdate(i);
syncTimerDisplay();
</script>
</body>
</html>