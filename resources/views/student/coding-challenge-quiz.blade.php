<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>DataSensei — {{ $challenge->title }}</title>
<style>
    /* Coding challenge workspace: problem, editor and test output side by side.
       Colours, type and radius come from partials.design-system. The status
       names below are used by the page (and --red by the empty-editor flash). */
    :root {
      --border-h: var(--ds-border-strong);
      --accent-h: var(--ds-accent-strong);
      --green:    var(--ds-success);
      --green-bg: var(--ds-success-soft);
      --green-bd: var(--ds-success-border);
      --red:      var(--ds-danger);
      --red-bg:   var(--ds-danger-soft);
      --red-bd:   var(--ds-danger-border);
      --amber:    var(--ds-warning);
      --amber-bg: var(--ds-warning-soft);
      --amber-bd: var(--ds-warning-border);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .layout { display: flex; min-height: 100vh; }

    /* ── main shell: fills the viewport on desktop ──────────────── */
    .coding-main { flex: 1; display: flex; flex-direction: column; min-width: 0; height: 100vh; height: 100dvh; overflow: hidden; }

    /* ── header: title and timer, question strip below ──────────── */
    .coding-header {
      flex-shrink: 0; z-index: 50;
      display: grid; grid-template-columns: minmax(0, 1fr) auto; grid-template-areas: "title timer" "dots dots";
      align-items: center; gap: 6px 16px;
      padding: 10px 24px 4px; background: var(--bg); border-bottom: 1px solid var(--border);
    }
    .coding-header > div:first-child { grid-area: title; min-width: 0; }
    .coding-breadcrumb { margin-bottom: 2px; color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.4; overflow-wrap: anywhere; }
    .coding-breadcrumb a { color: var(--muted); text-decoration: none; transition: color .12s ease; }
    .coding-breadcrumb a:hover { color: var(--text); }
    .coding-title { overflow-wrap: anywhere; }

    .coding-header-right { display: contents; }

    /* Question strip: one dot per question; each dot has a 32px tall hit area. */
    .q-dots {
      grid-area: dots; min-width: 0; display: flex; align-items: center; gap: 0;
      margin: 0 -5px; overflow-x: auto; overscroll-behavior-x: contain; scrollbar-width: thin;
    }
    .q-dot {
      flex: 0 0 20px; width: 20px; height: 32px; display: grid; place-items: center;
      border-radius: var(--radius-xs); cursor: pointer;
    }
    .q-dot::before {
      content: ""; width: 10px; height: 10px; border-radius: 50%;
      background: var(--surface2); border: 1px solid var(--ds-border-strong);
      transition: background .12s ease, border-color .12s ease;
    }
    .q-dot:hover::before { border-color: var(--muted); }
    .q-dot.active::before { background: var(--accent); border-color: var(--accent); outline: 1px solid var(--accent); outline-offset: 2px; }
    .q-dot.done::before   { background: var(--ds-success); border-color: var(--ds-success); }
    .q-dot.failed::before { background: var(--ds-danger); border-color: var(--ds-danger); }
    .q-dot.locked { opacity: .4; cursor: not-allowed; }
    .q-dot.locked:hover::before { border-color: var(--ds-border-strong); }

    /* Timer: Inter with tabular figures; the warning is a colour change only. */
    .coding-timer {
      grid-area: timer; min-height: 36px; padding: 0 12px; display: flex; align-items: center; gap: 8px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface);
      color: var(--text); font-size: 1rem; font-weight: 600; line-height: 1; font-variant-numeric: tabular-nums; white-space: nowrap;
      transition: color .16s ease, background .16s ease, border-color .16s ease;
    }
    .coding-timer svg { width: 14px; height: 14px; color: var(--muted); }
    .coding-timer.urgent    { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }
    .coding-timer.hidden    { visibility: hidden; }
    .coding-timer.finished  { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .coding-timer.urgent svg, .coding-timer.finished svg { color: inherit; }
    .timer-label { display: none; margin-right: 2px; font-size: .75rem; font-weight: 500; }
    .coding-timer.finished .timer-label { display: inline; }

    /* ── body: split pane ───────────────────────────────────────── */
    .coding-body { flex: 1; min-height: 0; display: flex; overflow: hidden; }

    .q-panel { display: none; flex: 1; min-width: 0; overflow: hidden; }
    .q-panel.active { display: flex; }

    /* left: problem */
    .problem-pane {
      width: 360px; min-width: 240px; max-width: 560px; flex-shrink: 0;
      display: flex; flex-direction: column; overflow-y: auto;
      background: var(--bg); border-right: 1px solid var(--border);
    }
    .problem-body { flex: 1; padding: 20px; }
    .problem-qnum { margin-bottom: 8px; color: var(--muted); font-size: .8125rem; font-weight: 500; font-variant-numeric: tabular-nums; }
    .problem-name { margin: 0 0 8px; color: var(--text); font-size: 1rem; font-weight: 600; line-height: 1.4; overflow-wrap: anywhere; }
    .problem-name[hidden] { display: none; }
    .problem-title { margin-bottom: 16px; color: var(--ds-text-secondary); font-size: .875rem; font-weight: 400; line-height: 1.6; white-space: pre-wrap; overflow-wrap: anywhere; }

    /* sample test cases */
    .tc-list { margin-top: 16px; }
    .tc-list-label { margin-bottom: 8px; color: var(--text); font-size: .8125rem; font-weight: 600; }
    .tc-card {
      margin-bottom: 8px; padding: 8px 12px;
      border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--surface3);
      font-family: var(--ds-font-mono); font-size: .75rem; line-height: 1.5;
      transition: border-color .16s ease, background .16s ease;
    }
    .tc-card.tc-pass  { border-color: var(--ds-success-border); background: var(--ds-success-soft); }
    .tc-card.tc-fail  { border-color: var(--ds-danger-border);  background: var(--ds-danger-soft); }
    .tc-card.tc-error { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); }
    .tc-row { display: flex; gap: 8px; align-items: flex-start; margin-bottom: 4px; }
    .tc-row:last-child { margin-bottom: 0; }
    .tc-key { min-width: 72px; flex-shrink: 0; color: var(--muted); font-weight: 500; }
    .tc-val { min-width: 0; color: var(--ds-text-secondary); white-space: pre-wrap; overflow-wrap: anywhere; }
    .tc-badge {
      flex-shrink: 0; margin-left: auto; padding: 1px 6px;
      border: 1px solid transparent; border-radius: var(--radius-xs);
      font-family: var(--ds-font-sans); font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .tc-badge:empty { display: none; }
    .badge-pass  { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .badge-fail  { border-color: var(--ds-danger-border);  background: var(--ds-danger-soft);  color: var(--ds-danger-text); }
    .badge-error { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }

    /* question facts */
    .problem-meta { padding: 0 20px 16px; display: flex; flex-wrap: wrap; gap: 6px; }
    .pill {
      display: inline-flex; align-items: center; padding: 2px 8px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2);
      color: var(--ds-text-secondary); font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .pill-done { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }

    /* locked question */
    .locked-overlay {
      position: absolute; inset: 0; z-index: 10; padding: 20px;
      display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;
      background: rgba(13, 19, 32, .9); text-align: center;
    }
    .locked-overlay .lock-icon { color: var(--muted); }
    .locked-overlay .lock-msg  { max-width: 36ch; color: var(--ds-text-secondary); font-size: .875rem; font-weight: 500; line-height: 1.5; }

    /* resize handle: takes no layout width; a 7px grab area over the pane border */
    .resizer { position: relative; z-index: 5; flex: 0 0 7px; width: 7px; margin: 0 -4px 0 -3px; cursor: col-resize; background: transparent; }
    .resizer::after { content: ""; position: absolute; top: 0; bottom: 0; left: 2px; width: 1px; background: transparent; transition: background .12s ease; }
    .resizer:hover::after { left: 2px; width: 2px; background: var(--accent); }

    /* right: editor */
    .editor-pane { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; position: relative; }

    .editor-toolbar {
      flex-shrink: 0; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;
      min-height: 48px; padding: 8px 12px 8px 16px; background: var(--surface); border-bottom: 1px solid var(--border);
    }
    .toolbar-lang { display: flex; align-items: center; gap: 6px; color: var(--muted); font-size: .8125rem; font-weight: 500; white-space: nowrap; }
    .lang-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); }
    .toolbar-spacer { flex: 1; }

    /* buttons (compact, 32px) */
    .btn {
      min-height: 32px; padding: 0 12px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2); color: var(--text);
      font-family: var(--ds-font-sans); font-size: .8125rem; font-weight: 500; line-height: 1.2; white-space: nowrap; cursor: pointer;
      transition: background .12s ease, border-color .12s ease, color .12s ease;
    }
    .btn svg { width: 13px; height: 13px; }
    .btn:disabled { opacity: .5; cursor: not-allowed; }
    .btn-run:hover:not(:disabled) { background: var(--ds-surface-hover); }
    .btn-submit { border-color: var(--accent); background: var(--accent); color: #fff; }
    .btn-submit:hover:not(:disabled) { border-color: var(--accent-hover); background: var(--accent-hover); }
    .btn-next, .btn-finish { display: none; border-color: var(--ds-success-border); background: transparent; color: var(--ds-success-text); }
    .btn-next:hover, .btn-finish:hover { background: var(--ds-success-soft); }
    .btn-retry { display: none; }
    .btn-retry:hover { background: var(--ds-surface-hover); }

    /* spinner (shown by the page while a request runs) */
    .spinner { display: none; width: 13px; height: 13px; border: 2px solid rgba(255, 255, 255, .25); border-top-color: #fff; border-radius: 50%; animation: spin .65s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* editor */
    .editor-wrap { flex: 1; display: flex; min-height: 0; position: relative; }
    .code-editor {
      flex: 1; min-width: 0; resize: none; outline: none; border: none;
      padding: 16px 20px; background: var(--bg); color: var(--ds-text-secondary);
      font-family: var(--ds-font-mono); font-size: .8125rem; line-height: 1.7; tab-size: 4; caret-color: var(--accent);
    }
    .code-editor::placeholder { color: var(--dim); }
    .code-editor::selection { background: rgba(59, 130, 246, .25); }
    .code-editor:disabled { opacity: .6; cursor: not-allowed; }

    /* custom stdin */
    .stdin-wrap {
      flex-shrink: 0; max-height: 0; overflow: hidden;
      background: var(--surface); border-top: 1px solid transparent;
      transition: max-height .2s ease;
    }
    .stdin-wrap.open { max-height: 120px; border-top-color: var(--border); }
    .stdin-inner { padding: 10px 16px 12px; }
    .stdin-label { margin-bottom: 6px; color: var(--ds-text-secondary); font-size: .75rem; font-weight: 500; }
    .stdin-textarea {
      width: 100%; height: 64px; padding: 8px 10px; resize: none; outline: none;
      border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--ds-text-secondary);
      font-family: var(--ds-font-mono); font-size: .75rem; line-height: 1.5;
      transition: border-color .12s ease, box-shadow .12s ease;
    }
    .stdin-textarea::placeholder { color: var(--dim); }
    .stdin-textarea:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); color: var(--text); }

    /* output */
    .output-wrap {
      flex-shrink: 0; max-height: 0; overflow: hidden;
      background: var(--surface); border-top: 1px solid transparent;
      transition: max-height .3s ease;
    }
    .output-wrap.open { max-height: 320px; overflow-y: auto; border-top-color: var(--border); }
    .output-inner { padding: 0 16px 14px; }

    /* output tabs: underline style */
    .output-tabs { display: flex; gap: 16px; margin: 0 0 12px; border-bottom: 1px solid var(--border); }
    .output-tab {
      min-height: 36px; margin-bottom: -1px; padding: 0 2px;
      border: 0; border-bottom: 2px solid transparent; background: none;
      color: var(--muted); font-family: var(--ds-font-sans); font-size: .8125rem; font-weight: 500; cursor: pointer;
      transition: color .12s ease, border-color .12s ease;
    }
    .output-tab:hover { color: var(--text); }
    .output-tab.active { color: var(--text); border-bottom-color: var(--accent); }

    .output-section { display: none; }
    .output-section.active { display: block; }

    .run-output-box {
      min-height: 36px; max-height: 180px; overflow-y: auto; padding: 8px 12px;
      border: 1px solid var(--border); border-radius: var(--radius-sm); background: var(--bg);
      color: var(--ds-text-secondary); font-family: var(--ds-font-mono); font-size: .8125rem; line-height: 1.55;
      white-space: pre-wrap; overflow-wrap: anywhere;
    }
    .run-output-box.is-error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

    .plot-img { display: none; max-width: 100%; margin-top: 8px; border: 1px solid var(--border); border-radius: var(--radius-sm); }

    /* test results */
    .result-header { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 12px; margin-bottom: 10px; }
    .result-title { color: var(--text); font-size: .875rem; font-weight: 600; }
    .verdict {
      display: inline-flex; align-items: center; padding: 2px 8px;
      border: 1px solid transparent; border-radius: var(--radius-xs);
      font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .verdict:empty { display: none; }
    .verdict-pass  { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .verdict-fail  { border-color: var(--ds-danger-border);  background: var(--ds-danger-soft);  color: var(--ds-danger-text); }
    .verdict-error { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }
    .result-score  { color: var(--muted); font-size: .8125rem; font-variant-numeric: tabular-nums; }
    .result-xp     { color: var(--ds-warning-text); font-size: .8125rem; font-weight: 600; font-variant-numeric: tabular-nums; }
    .hidden-cases  { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 8px; }
    .hidden-cases .tc-card { margin-bottom: 0; }

    .stderr-box {
      margin-top: 8px; padding: 8px 12px;
      border: 1px solid var(--ds-danger-border); border-radius: var(--radius-sm); background: var(--ds-danger-soft);
      color: var(--ds-danger-text); font-family: var(--ds-font-mono); font-size: .75rem; line-height: 1.55;
      white-space: pre-wrap; overflow-wrap: anywhere;
    }

    /* ── completion dialog ──────────────────────────────────────── */
    .modal-overlay {
      position: fixed; inset: 0; z-index: 9999; padding: 16px; overflow-y: auto;
      display: flex; align-items: center; justify-content: center;
      background: var(--ds-overlay); opacity: 0; pointer-events: none; transition: opacity .2s ease;
    }
    .modal-overlay.open { opacity: 1; pointer-events: auto; }
    .modal-card {
      width: min(420px, 100%); max-height: calc(100vh - 32px); max-height: calc(100dvh - 32px); overflow-y: auto;
      padding: 20px; display: flex; flex-direction: column;
      background: var(--surface); border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg);
      box-shadow: var(--ds-shadow-lg); transform: translateY(8px); transition: transform .2s ease;
    }
    .modal-overlay.open .modal-card { transform: none; }
    .modal-title { margin-bottom: 6px; color: var(--text); font-size: 1rem; font-weight: 600; line-height: 1.35; }
    .modal-sub   { margin-bottom: 12px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .modal-xp    { margin-bottom: 20px; color: var(--ds-warning-text); font-size: .9375rem; font-weight: 600; font-variant-numeric: tabular-nums; }
    .modal-xp:empty { display: none; }
    .modal-btn {
      align-self: flex-end; min-height: 38px; padding: 0 16px; display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff;
      font-size: .875rem; font-weight: 500; text-decoration: none; white-space: nowrap; transition: background .12s ease, border-color .12s ease;
    }
    .modal-btn:hover { border-color: var(--accent-hover); background: var(--accent-hover); }

    /* ── footer ─────────────────────────────────────────────────── */
    .coding-footer {
      flex-shrink: 0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px 12px;
      min-height: 52px; padding: 8px 24px; background: var(--bg); border-top: 1px solid var(--border);
    }
    .footer-info { color: var(--muted); font-size: .8125rem; font-variant-numeric: tabular-nums; }
    .footer-info strong { color: var(--text); font-weight: 600; }
    .footer-nav { display: flex; flex-wrap: wrap; gap: 8px; }
    .btn-nav:hover:not(:disabled) { background: var(--ds-surface-hover); }
    .btn-finish-footer { display: none; border-color: var(--ds-success-border); background: transparent; color: var(--ds-success-text); }
    .btn-finish-footer:hover { background: var(--ds-success-soft); }

    @media (max-width: 1200px) {
      .problem-pane { width: 320px; }
    }

    /* ── ≤900px: panes stack and the page scrolls normally ─────── */
    @media (max-width: 900px) {
      .coding-main { height: auto; min-height: calc(100vh - var(--ds-sticky-top, 0px)); min-height: calc(100dvh - var(--ds-sticky-top, 0px)); overflow: visible; }
      .coding-header { position: sticky; top: var(--ds-sticky-top, 0px); padding: 8px 20px 2px; }
      .coding-body { display: block; flex: 1 0 auto; overflow: visible; }
      .q-panel { overflow: visible; }
      .q-panel.active { flex-direction: column; }
      /* !important: the desktop drag handle writes an inline width. */
      .problem-pane { width: 100% !important; min-width: 0; max-width: none; overflow: visible; border-right: 0; border-bottom: 1px solid var(--border); }
      .problem-body { padding: 20px 20px 4px; }
      .resizer { display: none; }
      .editor-pane { overflow: visible; }
      .editor-wrap { flex: 0 0 auto; min-height: 55vh; min-height: 55dvh; }
      .output-wrap.open { max-height: none; overflow: visible; }
      .coding-footer { padding: 8px 20px; }
    }
    @media (max-width: 640px) {
      .coding-header { padding: 8px 16px 2px; }
      .coding-breadcrumb { font-size: .75rem; }
      .coding-timer { padding: 0 10px; }
      .problem-body { padding: 16px 16px 4px; }
      .problem-meta { padding: 0 16px 16px; }
      .editor-toolbar { padding: 8px 12px; }
      .code-editor { padding: 14px 16px; }
      .output-inner { padding: 0 12px 12px; }
      .coding-footer { padding: 8px 16px; }
    }
    @media (prefers-reduced-motion: reduce) {
      .stdin-wrap, .output-wrap, .modal-overlay, .modal-card { transition: none; }
    }
  </style>
    @include('partials.page-head', ['pageDescription' => 'Practise Python coding challenges and see where your skills stand.'])
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
        <h1 class="coding-title ds-page-title">{{ $challenge->title }}</h1>
      </div>
      <div class="coding-header-right">
        <div class="q-dots" id="navDots">
          @foreach($challenge->codingQuestions as $i => $q)
            @php
              $qState  = $attempts[$q->id]['state'] ?? 'locked';
              $dotCls  = match($qState) {
                'done'    => 'done',
                'expired' => 'failed',
                'active'  => ($i === ($activeIdx ?? 0) ? 'active' : ''),
                default   => 'locked',
              };
            @endphp
            <div class="q-dot {{ $dotCls }}"
                 data-index="{{ $i }}"
                 data-state="{{ $qState }}"
                 title="Q{{ $i + 1 }}{{ $qState === 'locked' ? ' (locked)' : ($qState === 'expired' ? ' (time expired)' : '') }}"
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
          $isExpired  = $qState === 'expired';
          $isFinished = $isDone || $isExpired;
          $isLocked   = $qState === 'locked';
          $isActive   = $i === ($activeIdx ?? 0) && !$isFinished;
          // DS-14: null until the server attempt exists (or the question is solved).
          // The start POST returns the content after started_at is stamped.
          $content    = $questionContent[$question->id] ?? null;
          $runUrl     = route('challenges.coding.run',    ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]);
          $subUrl     = route('challenges.coding.submit', ['slug' => $slug, 'challenge' => $challenge->id, 'question' => $question->id]);
        @endphp

        <div class="q-panel {{ $i === ($activeIdx ?? 0) ? 'active' : '' }}"
             id="qpanel-{{ $i }}"
             data-index="{{ $i }}"
             data-qid="{{ $question->id }}"
             data-timelimit="{{ $question->time_limit_seconds }}"
             data-run-url="{{ $runUrl }}"
             data-sub-url="{{ $subUrl }}"
             data-state="{{ $qState }}"
             data-content="{{ $content !== null ? '1' : '0' }}"
             data-done="{{ $isDone ? '1' : '0' }}">

          {{-- ── LEFT: PROBLEM ── --}}
          <div class="problem-pane" id="problemPane-{{ $i }}">
            <div class="problem-body">
              <div class="problem-qnum">Question {{ $i + 1 }} of {{ $challenge->codingQuestions->count() }}</div>
              <h2 class="problem-name" id="problem-name-{{ $i }}" @if(blank($content['title'] ?? null)) hidden @endif>{{ $content['title'] ?? '' }}</h2>
              <div class="problem-title" id="problem-title-{{ $i }}">{{ $content['problem_description'] ?? '' }}</div>

              <div id="tc-holder-{{ $i }}">
              @if($content !== null && count($content['test_cases']) > 0)
                <div class="tc-list">
                  <div class="tc-list-label">Sample Test Cases</div>
                  @foreach($content['test_cases'] as $tc)
                    <div class="tc-card" id="tc-{{ $tc['id'] }}">
                      @if($tc['input'] !== null)
                        <div class="tc-row">
                          <span class="tc-key">Input:</span>
                          <span class="tc-val">{{ $tc['input'] }}</span>
                        </div>
                      @endif
                      <div class="tc-row">
                        <span class="tc-key">Expected:</span>
                        <span class="tc-val">{{ $tc['expected_output'] }}</span>
                      </div>
                      <div class="tc-row" id="tc-got-row-{{ $tc['id'] }}" style="display:none">
                        <span class="tc-key">Got:</span>
                        <span class="tc-val" id="tc-got-{{ $tc['id'] }}"></span>
                        <span class="tc-badge" id="tc-badge-{{ $tc['id'] }}"></span>
                      </div>
                    </div>
                  @endforeach
                </div>
              @endif
              </div>
            </div>

            <div class="problem-meta">
              <span class="pill pill-xp">{{ $question->base_xp }} XP</span>
              <span class="pill pill-lang">Python</span>
              <span class="pill pill-time">{{ intval($question->time_limit_seconds / 60) }} min limit</span>
              @if($isDone)
                <span class="pill pill-done">✓ Solved</span>
              @elseif($isExpired)
                <span class="pill pill-time">Time expired</span>
              @endif
            </div>
          </div>

          <div class="resizer" data-pane="{{ $i }}"></div>

          {{-- ── RIGHT: EDITOR ── --}}
          <div class="editor-pane">

            {{-- Locked overlay for future questions --}}
            @if($isLocked)
              <div class="locked-overlay">
                <svg class="lock-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                <div class="lock-msg">Complete Question {{ $i }} first to unlock this question.</div>
              </div>
            @endif

            {{-- Toolbar --}}
            <div class="editor-toolbar">
              <div class="toolbar-lang"><div class="lang-dot"></div> Python 3</div>
              <div class="toolbar-spacer"></div>

              {{-- Toggle stdin (hidden for done/locked) --}}
              @if(!$isDone && !$isLocked)
                <button type="button" class="btn btn-run" onclick="toggleStdin({{ $i }})" title="Custom input for Run">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  stdin
                </button>
              @endif

              {{-- Run button --}}
              <button type="button" class="btn btn-run" id="btn-run-{{ $i }}" onclick="runCode({{ $i }})" {{ ($isFinished || $isLocked) ? 'disabled' : '' }}>
                <div class="spinner" id="run-spinner-{{ $i }}"></div>
                <svg id="run-icon-{{ $i }}" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3" fill="currentColor"/></svg>
                Run
              </button>

              {{-- Retry (shown after failed submit) --}}
              <button type="button" class="btn btn-retry" id="btn-retry-{{ $i }}" onclick="resetSubmit({{ $i }})">
                ↺ Retry
              </button>

              {{-- Next Question (shown after passing, if not last) --}}
              <button type="button" class="btn btn-next" id="btn-next-{{ $i }}" onclick="gotoQ({{ $i + 1 }})">
                Next →
              </button>

              {{-- Finish (shown after passing the last question) --}}
              <button type="button" class="btn btn-finish" id="btn-finish-{{ $i }}" onclick="showCompletion()">
                Finish
              </button>

              {{-- Submit button --}}
              <button type="button" class="btn btn-submit" id="btn-submit-{{ $i }}" onclick="submitCode({{ $i }})" {{ ($isFinished || $isLocked) ? 'disabled' : '' }}>
                <div class="spinner" id="sub-spinner-{{ $i }}"></div>
                <svg id="sub-icon-{{ $i }}" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ $isDone ? '✓ Solved' : ($isExpired ? 'Expired' : 'Run & Submit') }}
              </button>
            </div>

            {{-- Code editor --}}
            <div class="editor-wrap">
              <textarea class="code-editor" id="editor-{{ $i }}"
                        spellcheck="false"
                        placeholder="# Write your Python solution here..."
                        onkeydown="handleTab(event)"
                        {{ ($isFinished || $isLocked) ? 'disabled' : '' }}>{{ $prior ? $prior->code : ($content['starter_code'] ?? '') }}</textarea>
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
                  <button type="button" class="output-tab active" id="tab-run-{{ $i }}"    onclick="switchTab({{ $i }}, 'run')">▶ Run Output</button>
                  <button type="button" class="output-tab"         id="tab-submit-{{ $i }}" onclick="switchTab({{ $i }}, 'submit')">✓ Test Results</button>
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
        <button type="button" class="btn btn-nav" id="btnPrev" onclick="gotoQ(currentQ - 1)" disabled>← Prev</button>
        <button type="button" class="btn btn-nav" id="btnNext" onclick="gotoQ(currentQ + 1)" {{ $challenge->codingQuestions->count() <= 1 ? 'disabled' : '' }}>Next →</button>
        <button type="button" class="btn btn-finish-footer" id="btnFinishFooter" onclick="showCompletion()">Finish challenge</button>
      </div>
    </footer>

  </div>{{-- coding-main --}}
</div>

{{-- ── COMPLETION MODAL ── --}}
<div class="modal-overlay" id="completionModal">
  <div class="modal-card">
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
// Start on the first unsolved question, not always Q0.
// If ACTIVE_IDX is null (all done), default to 0 so the blade has a valid panel.
let   currentQ = {{ $activeIdx ?? 0 }};

// ── Question states from server ──────────────────────────────────────────
// state: 'done' | 'expired' | 'active' | 'locked'
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
 * Stop counting for the current question.
 * The timer stays VISIBLE with '--:--' (neutral) — it must NOT disappear
 * between individual questions. It only turns green when the full challenge
 * is complete (showElapsedTimer). Hiding it per-question was the source of
 * Issue 3 (timer disappears after a single level success).
 */
function clearTimer() {
  activeTimer = null;
  pingCounter = 0;
  document.getElementById('timerTxt').textContent = '--:--';
  document.getElementById('timerEl').classList.remove('urgent', 'hidden', 'finished');
}

// Master tick — runs every second, drives the single active timer
setInterval(() => {
  if (activeTimer === null) return;
  if (activeTimer.remaining <= 0) return;

  activeTimer.remaining--;
  pingCounter++;

  // Always update — the header timer is global, not per-panel.
  // The user sees it regardless of which question panel is active.
  syncTimerDisplay();

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
  const timerEl = document.getElementById('timerEl');

  // If the challenge is already fully complete, leave the green "Done in" display alone.
  if (timerEl.classList.contains('finished')) return;

  if (activeTimer === null) {
    // Between questions (clearTimer was called but next has not started yet).
    // Stay visible with a neutral '--:--' — Issue 3 fix: do NOT hide the timer here.
    document.getElementById('timerTxt').textContent = '--:--';
    timerEl.classList.remove('urgent', 'hidden');
    return;
  }

  if (Q_STATE[activeTimer.questionIdx] !== 'active') {
    // Safety guard — should not occur in normal flow.
    timerEl.classList.add('hidden');
    return;
  }

  const s   = Math.max(0, activeTimer.remaining);
  const pad = n => String(n).padStart(2, '0');
  document.getElementById('timerTxt').textContent = pad(Math.floor(s / 60)) + ':' + pad(s % 60);
  timerEl.classList.remove('hidden', 'finished');
  timerEl.classList.toggle('urgent', s <= 60 && s > 0);
}

function handleExpired(idx, data = {}) {
  const panel = document.getElementById(`qpanel-${idx}`);
  if (!panel) return;

  panel.dataset.done = '1';
  panel.dataset.state = 'expired';
  Q_STATE[idx] = 'expired';
  qStates[idx] = {
    status: 'expired',
    passed: 0,
    total: data.tests_total || 0,
    xp: 0,
  };

  if (activeTimer && activeTimer.questionIdx === idx) {
    activeTimer.remaining = 0;
  }
  activeTimer = null;
  pingCounter = 0;

  const timerEl = document.getElementById('timerEl');
  timerEl.classList.remove('hidden', 'finished');
  timerEl.classList.add('urgent');
  document.getElementById('timerLabel').textContent = 'Elapsed';
  document.getElementById('timerTxt').textContent = '00:00';

  const editor = document.getElementById(`editor-${idx}`);
  const runBtn = document.getElementById(`btn-run-${idx}`);
  const subBtn = document.getElementById(`btn-submit-${idx}`);
  const retryBtn = document.getElementById(`btn-retry-${idx}`);
  if (editor) editor.disabled = true;
  if (runBtn) runBtn.disabled = true;
  if (subBtn) subBtn.disabled = true;
  if (retryBtn) retryBtn.style.display = 'none';

  const verdictEl = document.getElementById(`verdict-${idx}`);
  if (verdictEl) {
    verdictEl.className = 'verdict verdict-error';
    verdictEl.textContent = 'Time expired';
  }

  const scoreEl = document.getElementById(`result-score-${idx}`);
  const xpEl = document.getElementById(`result-xp-${idx}`);
  const stderrEl = document.getElementById(`stderr-${idx}`);
  if (scoreEl) scoreEl.textContent = '0 / ' + (data.tests_total || 0) + ' tests';
  if (xpEl) xpEl.textContent = '+0 XP';
  if (stderrEl) {
    stderrEl.style.display = 'block';
    stderrEl.textContent = data.message || data.error || 'Time limit exceeded. No XP earned. You may proceed to the next question.';
  }

  openOutput(idx);
  switchTab(idx, 'submit');
  dotUpdate(idx);
  unlockNextAfterTimeout(idx);
}

function unlockNextAfterTimeout(idx) {
  const nextIdx = idx + 1;

  if (nextIdx < TOTAL) {
    Q_STATE[nextIdx] = 'active';

    const nextDot = document.querySelector(`.q-dot[data-index="${nextIdx}"]`);
    if (nextDot) {
      nextDot.classList.remove('locked');
      nextDot.dataset.state = 'active';
    }

    const nextPanel = document.getElementById(`qpanel-${nextIdx}`);
    if (nextPanel) {
      nextPanel.dataset.state = 'active';
      const overlay = nextPanel.querySelector('.locked-overlay');
      if (overlay) overlay.remove();

      const nextEditor = document.getElementById(`editor-${nextIdx}`);
      const nextRun = document.getElementById(`btn-run-${nextIdx}`);
      const nextSub = document.getElementById(`btn-submit-${nextIdx}`);
      if (nextEditor) nextEditor.disabled = false;
      if (nextRun) nextRun.disabled = false;
      if (nextSub) nextSub.disabled = false;
    }

    document.getElementById(`btn-next-${idx}`).style.display = 'inline-flex';
    if (currentQ === idx) document.getElementById('btnNext').disabled = false;
  } else {
    document.getElementById(`btn-finish-${idx}`).style.display = 'inline-flex';
    document.getElementById('btnFinishFooter').style.display = 'inline-flex';
  }
}

(function initTimer() {
  if (ACTIVE_IDX === null) {
    // All questions already done — show a completed "Done in --:--" state
    // (challengeStartTime is unknown on a fresh load of a completed challenge,
    // so we just show the finished style without a meaningful time).
    const timerEl = document.getElementById('timerEl');
    timerEl.classList.remove('hidden', 'urgent');
    timerEl.classList.add('finished');
    document.getElementById('timerLabel').textContent = 'Done in';
    document.getElementById('timerTxt').textContent = '--:--';
    document.getElementById('btnFinishFooter').style.display = 'inline-flex';
    return;
  }

  const idx = ACTIVE_IDX;
  if (hasAttempt[idx]) {
    seedTimer(idx, serverRemaining[idx]);
  } else {
    // Starting a timer is a state change. Keep the GET page read-only and use
    // the existing CSRF-protected POST start request for the initial question.
    gotoQ(idx);
  }
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
      if (!resp.ok) {
        throw new Error(`Timer start failed with HTTP ${resp.status}.`);
      }

      const data = await resp.json();
      fillQuestionContent(idx, data.question);
      seedTimer(idx, data.remaining_seconds);
      if (data.expired) handleExpired(idx);
    } catch (_) {
      // The server is authoritative. Retry the idempotent POST instead of
      // starting a client-only clock that the server cannot validate.
      hasAttempt[idx] = false;
      window.setTimeout(() => {
        if (Q_STATE[idx] === 'active' && !hasAttempt[idx] && activeTimer === null) {
          gotoQ(idx);
        }
      }, 3000);
    }
  }

  // Timer display is managed globally by syncTimerDisplay — no per-panel
  // hiding/showing needed here. Removing that block fixed Issue 3.
  syncTimerDisplay();
}

/**
 * DS-14: the page carries no problem text for a question whose server attempt
 * does not exist yet. The start POST returns it after the server clock is
 * stamped; this builds the same markup the server renders for started
 * questions. textContent only — question text is never parsed as HTML.
 */
function fillQuestionContent(idx, question) {
  const panel = document.getElementById(`qpanel-${idx}`);
  if (!panel || !question || panel.dataset.content === '1') return;

  const el = (tag, cls, text) => {
    const node = document.createElement(tag);
    if (cls) node.className = cls;
    if (text !== undefined) node.textContent = text;
    return node;
  };

  const nameEl = document.getElementById(`problem-name-${idx}`);
  if (nameEl) {
    nameEl.textContent = question.title || '';
    nameEl.hidden = !question.title;
  }
  document.getElementById(`problem-title-${idx}`).textContent = question.problem_description || '';

  const holder = document.getElementById(`tc-holder-${idx}`);
  const cases  = Array.isArray(question.test_cases) ? question.test_cases : [];
  holder.textContent = '';
  if (cases.length > 0) {
    const list = el('div', 'tc-list');
    list.appendChild(el('div', 'tc-list-label', 'Sample Test Cases'));
    cases.forEach(tc => {
      const card = el('div', 'tc-card');
      card.id = `tc-${tc.id}`;
      if (tc.input !== null && tc.input !== undefined) {
        const inRow = el('div', 'tc-row');
        inRow.appendChild(el('span', 'tc-key', 'Input:'));
        inRow.appendChild(el('span', 'tc-val', tc.input));
        card.appendChild(inRow);
      }
      const expRow = el('div', 'tc-row');
      expRow.appendChild(el('span', 'tc-key', 'Expected:'));
      expRow.appendChild(el('span', 'tc-val', tc.expected_output));
      card.appendChild(expRow);

      const gotRow = el('div', 'tc-row');
      gotRow.id = `tc-got-row-${tc.id}`;
      gotRow.style.display = 'none';
      gotRow.appendChild(el('span', 'tc-key', 'Got:'));
      const gotVal = el('span', 'tc-val');
      gotVal.id = `tc-got-${tc.id}`;
      const badge = el('span', 'tc-badge');
      badge.id = `tc-badge-${tc.id}`;
      gotRow.appendChild(gotVal);
      gotRow.appendChild(badge);
      card.appendChild(gotRow);
      list.appendChild(card);
    });
    holder.appendChild(list);
  }

  const editor = document.getElementById(`editor-${idx}`);
  if (editor && editor.value === '') editor.value = question.starter_code || '';

  panel.dataset.content = '1';
}

function dotUpdate(idx) {
  const dot = document.querySelector(`.q-dot[data-index="${idx}"]`);
  if (!dot) return;
  dot.classList.remove('active', 'done', 'failed');
  if (Q_STATE[idx] === 'done')    { dot.classList.add('done');   return; }
  if (Q_STATE[idx] === 'expired') { dot.classList.add('failed'); return; }
  if (Q_STATE[idx] === 'locked')  { dot.classList.add('locked'); return; }
  // active question
  const st = qStates[idx];
  if (idx === currentQ)             dot.classList.add('active');
  else if (st?.status === 'passed') dot.classList.add('done');
  else if (st?.status === 'failed' || st?.status === 'error') dot.classList.add('failed');
}

// Initialise footer nav state
document.getElementById('btnNext').disabled =
  TOTAL <= 1 || Q_STATE[1] === 'locked';

// ── Sync footer Q-number and Prev/Next buttons to the real starting question ─
// When ACTIVE_IDX > 0 (partially-completed challenge) currentQ is already set
// to ACTIVE_IDX above, so the footer must reflect that panel, not Q1.
document.getElementById('footerQ').textContent = currentQ + 1;
document.getElementById('btnPrev').disabled = currentQ === 0;
document.getElementById('btnNext').disabled =
  currentQ >= TOTAL - 1 || Q_STATE[currentQ + 1] === 'locked';

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

    if (data.status === 'expired' || data.expired) {
      handleExpired(idx, data);
      outBox.textContent = data.error || data.message || 'Time limit exceeded. You may proceed to the next question.';
      outBox.classList.add('is-error');
      return;
    }

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
      handleExpired(idx, data);
      return;
    }

    if (data.source_failed) {
      qStates[idx] = { status: 'failed', passed: 0, total: data.tests_total, xp: 0 };
      dotUpdate(idx);
      renderSubmitResults(idx, data);
      document.getElementById(`btn-retry-${idx}`).style.display = 'inline-flex';
      return;
    }

    if (resp.status === 403 || resp.status === 409 || resp.status === 422) {
      showOutputError(idx, data.error || data.message || 'Submission rejected by server.');
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
  if (Q_STATE[idx] === 'expired') return;
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
  scoreEl.textContent   = `${data.tests_passed ?? 0} / ${data.tests_total ?? 0} tests`;
  xpEl.textContent      = (data.xp_earned ?? 0) > 0 ? `+${data.xp_earned} XP` : '';

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

  const instructionErrors = data.instruction_errors || [];
  const stderr = instructionErrors.length
    ? instructionErrors.join('\\n')
    : ((data.results || []).find(r => r.stderr)?.stderr
        // DS-13: hidden cases carry only a generic, server-written message.
        || (data.results || []).find(r => r.is_hidden && !r.passed && r.message)?.message);

  if (stderr) { stderrEl.style.display = 'block'; stderrEl.textContent = stderr; }
  else        { stderrEl.style.display = 'none'; }
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
 * Replace the countdown timer with a green "Done in X min Y sec" display.
 * Called once when the challenge is fully complete.
 * This is the ONLY moment the timer visually "stops" — individual question
 * passes leave the timer visible (neutral '--:--') until the next starts.
 */
function showElapsedTimer() {
  const elapsedMs   = challengeStartTime !== null ? (Date.now() - challengeStartTime) : 0;
  const elapsedSecs = Math.max(0, Math.floor(elapsedMs / 1000));
  const mins = Math.floor(elapsedSecs / 60);
  const secs = elapsedSecs % 60;
  const pad  = n => String(n).padStart(2, '0');

  // "Done in 4m 23s"  or  "Done in 45s" for sub-minute times
  document.getElementById('timerLabel').textContent = 'Done in';
  document.getElementById('timerTxt').textContent   =
    mins > 0 ? `${mins}m ${pad(secs)}s` : `${secs}s`;

  const timerEl = document.getElementById('timerEl');
  timerEl.classList.remove('hidden', 'urgent');
  timerEl.classList.add('finished');   // green style, label becomes visible

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


// Expose functions used by inline onclick/onkeydown handlers.
window.gotoQ = gotoQ;
window.toggleStdin = toggleStdin;
window.runCode = runCode;
window.submitCode = submitCode;
window.resetSubmit = resetSubmit;
window.switchTab = switchTab;
window.showCompletion = showCompletion;
window.handleTab = handleTab;

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
// Dots are rendered by PHP; refresh their JS-driven classes on load.
for (let i = 0; i < TOTAL; i++) dotUpdate(i);
syncTimerDisplay();

// ── bfcache prevention — Issue 1 root cause ──────────────────────────────
// When the user presses Back, the browser may serve this page from bfcache
// (in-memory snapshot), bypassing the server entirely. This means the PHP-
// rendered state — attempt data, Q-dot states, timers — would be stale.
// Forcing a reload on 'pageshow' with e.persisted=true ensures the server
// always re-renders fresh state, fixing "Start Challenge" vs "Continue
// Challenge" and the wrong-panel display on return navigation.
window.addEventListener('pageshow', e => {
  if (e.persisted) window.location.reload();
});
</script>
</body>
</html>
