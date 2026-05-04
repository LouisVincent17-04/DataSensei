<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SQL Sandbox — DataSensei</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />
    <style>
        /* ── Variables Matched to index.blade.php ─────────────────────────── */
        :root {
            --bg:           #0d1320; 
            --surface:      #111c2d; 
            --surface2:     #1a2638; 
            --surface3:     #0f1928; 
            --border:       #1e2f47;
            --border-hover: #2c4168; 
            --accent:       #3b82f6; 
            --accent-hover: #2563eb; 
            --accent3:      #10b981;
            --warn:         #ef4444; 
            --warn2:        #f59e0b; 
            --text:         #fafafa; 
            --muted:        #7f93b0; 
            --dim:          #3d5272;
            --radius:       6px;
            --topbar-h:     42px;
            --mono:         'JetBrains Mono', monospace;
            --sidebar-w:    260px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            line-height: 1.5;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .shell { display: flex; flex-direction: column; height: 100vh; }
        
        .topbar { 
            height: var(--topbar-h); 
            background: var(--surface); 
            border-bottom: 1px solid var(--border); 
            display: flex; 
            align-items: center; 
            padding: 0 16px; 
            gap: 12px; 
            flex-shrink: 0; 
        }

        .topbar-logo { font-weight: 700; font-size: 0.875rem; letter-spacing: -0.02em; display: flex; align-items: center; gap: 6px; }
        .topbar-logo span { color: var(--accent); }
        .topbar-sep { width: 1px; height: 18px; background: var(--border); margin: 0 4px; }
        
        .workspace { display: flex; flex: 1; overflow: hidden; }

        /* ══ SIDEBAR ══════════════════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex; 
            flex-direction: column;
        }

        .sidebar-head {
            padding: 10px 12px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .sidebar-title { font-size: 0.65rem; font-weight: 700; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; }

        .quota { display: flex; align-items: center; gap: 6px; font-size: 0.7rem; color: var(--muted); }
        .quota-bar { width: 40px; height: 4px; background: var(--border); border-radius: 99px; overflow: hidden; }
        .quota-fill { height: 100%; background: var(--accent); transition: width .3s; }
        .quota-fill.full { background: var(--warn); }

        .table-list { flex: 1; overflow-y: auto; padding: 4px 0; }
        .empty-state { padding: 24px 12px; text-align: center; color: var(--dim); font-size: 0.8rem; }
        .empty-state .icon { font-size: 24px; display: block; margin-bottom: 8px; opacity: 0.5; }

        .tbl-item { border-bottom: 1px solid rgba(30, 47, 71, 0.5); }
        .tbl-header { display: flex; align-items: center; padding: 6px 12px; cursor: pointer; gap: 6px; transition: background .1s; }
        .tbl-header:hover { background: var(--surface2); }
        .tbl-chevron { width: 12px; height: 12px; color: var(--dim); transition: transform .15s; }
        .tbl-item.open .tbl-chevron { transform: rotate(90deg); }
        .tbl-name { flex: 1; font-family: var(--mono); font-size: 12px; color: var(--muted); overflow: hidden; text-overflow: ellipsis; }
        .tbl-item.open .tbl-name { color: var(--text); }

        .tbl-actions { display: flex; gap: 2px; opacity: 0; }
        .tbl-header:hover .tbl-actions { opacity: 1; }
        .tbl-btn { width: 20px; height: 20px; border: none; background: transparent; color: var(--muted); border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .tbl-btn:hover { background: var(--border); color: var(--text); }

        .col-list { display: none; padding: 4px 0 8px 28px; background: var(--surface3); }
        .tbl-item.open .col-list { display: block; }
        .col-row { display: flex; align-items: center; padding: 2px 0; gap: 6px; font-size: 11px; font-family: var(--mono); }
        .col-name { color: var(--muted); }
        .col-type { color: var(--accent); opacity: 0.8; font-size: 9px; text-transform: uppercase; }

        /* ══ MAIN ═════════════════════════════════════════════════════════ */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: var(--bg); }

        .editor-section {
            padding: 12px 16px 0;
            border-bottom: 1px solid var(--border);
            background: var(--surface3);
            flex-shrink: 0;
        }

        .editor-box {
            position: relative;
            border: 1px solid var(--border);
            border-radius: var(--radius) var(--radius) 0 0;
            background: var(--bg);
            overflow: visible;
        }
        .editor-box:focus-within { border-color: var(--accent); }
        
        .line-nums {
            position: absolute;
            left: 0;
            top: 0;
            width: 34px;
            height: 100%;
            background: var(--surface);
            border-right: 1px solid var(--border);
            padding-top: 10px;
            text-align: right;
            pointer-events: none;
            overflow: hidden;
            border-radius: var(--radius) 0 0 0;
        }
        .line-nums span { display: block; padding-right: 8px; font-size: 11px; font-family: var(--mono); color: var(--dim); line-height: 20px; }

        #query {
            resize: none;
            width: 100%;
            min-height: 120px;
            padding: 10px 10px 10px 44px;
            background: transparent;
            border: none;
            outline: none;
            font-size: 13px;
            font-family: var(--mono);
            color: var(--text);
            line-height: 20px;
            display: block;
        }

        .editor-resize-handle {
            width: 100%;
            height: 6px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-top: none;
            border-radius: 0 0 var(--radius) var(--radius);
            cursor: ns-resize;
            display: flex;
            align-items: center;
            justify-content: center;
            user-select: none;
            transition: background 0.15s;
        }
        .editor-resize-handle:hover,
        .editor-resize-handle.dragging { background: var(--border-hover); }
        .editor-resize-handle::before {
            content: '···';
            font-size: 10px;
            letter-spacing: 3px;
            color: var(--dim);
            line-height: 1;
        }

        .tb-btn { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: var(--radius); border: 1px solid var(--border); background: transparent; color: var(--muted); font-size: 0.75rem; cursor: pointer; transition: all 0.15s; font-weight: 500; }
        .tb-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border-hover); }
        .tb-btn.run { background: var(--accent3); color: #fff; border-color: var(--accent3); font-weight: 600; }
        .tb-btn.run:hover { background: #0ea472; }
        .tb-btn.run:disabled { opacity: 0.6; cursor: not-allowed; }
        
        .shortcut { font-size: 10px; color: var(--dim); margin-left: auto; }
        kbd { background: var(--surface2); border: 1px solid var(--border); padding: 1px 4px; border-radius: 3px; }

        .results-section { flex: 1; display: flex; flex-direction: column; overflow: hidden; padding: 12px 16px; gap: 8px; }
        .results-label { font-size: 0.65rem; font-weight: 700; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; }
        .result-box { flex: 1; border: 1px solid var(--border); border-radius: var(--radius); background: var(--surface3); overflow: hidden; display: flex; flex-direction: column; }
        
        .idle { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--dim); gap: 8px; }
        .idle svg { opacity: 0.2; }

        .tbl-scroll { flex: 1; overflow: auto; display: none; }
        .tbl-scroll.show { display: block; }
        .result-table { width: 100%; border-collapse: collapse; font-family: var(--mono); font-size: 12px; }
        .result-table thead { position: sticky; top: 0; background: var(--surface); z-index: 1; }
        .result-table th { padding: 8px 12px; text-align: left; color: var(--muted); font-size: 10px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
        .result-table td { padding: 6px 12px; border-bottom: 1px solid var(--border); color: var(--text); white-space: nowrap; }
        .result-table tr:hover { background: var(--surface2); }

        .status-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; display: none; }
        .status-badge.success { display: inline-block; background: rgba(16, 185, 129, 0.1); color: var(--accent3); }
        .status-badge.error { display: inline-block; background: rgba(239, 68, 68, 0.1); color: var(--warn); }

        .spinner { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; display: none; }
        .spinner.show { display: block; }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .refresh-btn { width: 22px; height: 22px; border: none; background: transparent; color: var(--muted); border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: color .15s, background .15s; }
        .refresh-btn:hover { background: var(--border); color: var(--text); }
        .refresh-btn.spinning svg { animation: spin 0.6s linear infinite; }

        .drop-toast { position: fixed; bottom: 20px; right: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; z-index: 99; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none; }
        .drop-toast.show { display: block; }
        .drop-toast-title { color: var(--warn); font-weight: 700; font-size: 0.85rem; margin-bottom: 4px; }
        .drop-toast-desc { font-size: 0.75rem; color: var(--muted); margin-bottom: 12px; }

        /* ═══════════════════════════════════════════════════
           🤖 AI REVIEW CHATBOT WIDGET
        ═══════════════════════════════════════════════════ */
        #rb-toggle {
          position: fixed; bottom: 24px; right: 24px; z-index: 1000;
          width: 48px; height: 48px; border-radius: 50%;
          background: var(--accent); border: none; cursor: pointer;
          display: flex; align-items: center; justify-content: center;
          box-shadow: 0 4px 18px rgba(59,130,246,0.4);
          transition: background 0.15s, transform 0.15s, box-shadow 0.15s;
          color: #fff;
        }
        #rb-toggle:hover { background: var(--accent-hover); transform: scale(1.08); box-shadow: 0 6px 24px rgba(59,130,246,0.5); }
        #rb-toggle:active { transform: scale(0.95); }
        #rb-toggle .rb-icon-chat, #rb-toggle .rb-icon-close { transition: opacity 0.15s, transform 0.15s; }
        #rb-toggle .rb-icon-close { position: absolute; opacity: 0; transform: rotate(-90deg) scale(0.7); }
        #rb-toggle.rb-open .rb-icon-chat  { opacity: 0; transform: rotate(90deg) scale(0.7); }
        #rb-toggle.rb-open .rb-icon-close { opacity: 1; transform: rotate(0deg) scale(1); }
        #rb-toggle .rb-dot {
          position: absolute; top: 7px; right: 7px;
          width: 8px; height: 8px; border-radius: 50%;
          background: var(--accent3); border: 2px solid var(--bg);
          opacity: 0; transition: opacity 0.3s;
        }
        #rb-toggle.rb-has-review .rb-dot { opacity: 1; animation: rbPulse 2s ease-in-out infinite; }
        #rb-toggle.rb-open .rb-dot { opacity: 0 !important; animation: none; }
        @keyframes rbPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.3)} }

        #rb-panel {
          position: fixed; bottom: 82px; right: 24px; z-index: 1000;
          width: 360px; height: 510px;
          background: var(--surface); border: 1px solid var(--border); border-radius: 12px;
          display: flex; flex-direction: column; overflow: hidden;
          box-shadow: 0 10px 40px rgba(0,0,0,0.55);
          opacity: 0; pointer-events: none;
          transform: translateY(14px) scale(0.97);
          transform-origin: bottom right;
          transition: opacity 0.2s ease, transform 0.2s ease;
        }
        #rb-panel.rb-open { opacity: 1; pointer-events: all; transform: translateY(0) scale(1); }

        .rb-header {
          height: 46px; flex-shrink: 0;
          display: flex; align-items: center; gap: 9px; padding: 0 12px;
          background: var(--surface3); border-bottom: 1px solid var(--border);
          user-select: none;
        }
        .rb-header-icon {
          width: 26px; height: 26px; border-radius: 7px;
          background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.22);
          display: flex; align-items: center; justify-content: center;
          color: var(--accent); flex-shrink: 0;
        }
        .rb-header-title { font-size: 0.8rem; font-weight: 600; color: var(--text); letter-spacing: -0.01em; }
        .rb-header-sub   { font-size: 0.67rem; color: var(--muted); }
        .rb-status {
          margin-left: auto; display: flex; align-items: center; gap: 5px;
          font-size: 0.67rem; color: var(--accent3);
        }
        .rb-status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent3); }
        .rb-status.rb-busy .rb-status-dot { background: var(--warn2); animation: rbPulse 0.7s ease-in-out infinite; }
        .rb-status.rb-busy span { color: var(--warn2); }

        #rb-msgs {
          flex: 1; overflow-y: auto; padding: 12px 10px;
          display: flex; flex-direction: column; gap: 10px;
          scroll-behavior: smooth;
        }
        #rb-msgs::-webkit-scrollbar { width: 3px; }
        #rb-msgs::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        .rb-msg { display: flex; gap: 7px; align-items: flex-start; animation: rbMsgIn 0.18s ease; }
        @keyframes rbMsgIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }
        .rb-msg.rb-user { flex-direction: row-reverse; }
        .rb-avatar {
          width: 24px; height: 24px; border-radius: 7px;
          display: flex; align-items: center; justify-content: center;
          font-size: 0.6rem; font-weight: 700; flex-shrink: 0;
        }
        .rb-msg.rb-bot  .rb-avatar { background: rgba(59,130,246,0.13); border: 1px solid rgba(59,130,246,0.22); color: var(--accent); }
        .rb-msg.rb-user .rb-avatar { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.2); color: var(--accent3); }
        .rb-body { flex: 1; min-width: 0; }
        .rb-msg.rb-user .rb-body { display: flex; flex-direction: column; align-items: flex-end; }
        .rb-bubble {
          display: inline-block; padding: 8px 11px; border-radius: 9px;
          font-size: 0.78rem; line-height: 1.55; max-width: 100%; word-break: break-word;
        }
        .rb-msg.rb-user .rb-bubble {
          background: rgba(59,130,246,0.1); border: 1px solid rgba(59,130,246,0.18);
          color: var(--muted); font-family: 'JetBrains Mono', monospace; font-size: 0.68rem;
          white-space: pre-wrap; text-align: left; max-height: 120px; overflow-y: auto;
        }
        .rb-msg.rb-bot .rb-bubble {
          background: var(--surface2); border: 1px solid var(--border);
          color: var(--text); width: 100%;
        }
        .rb-section { font-size: 0.68rem; font-weight: 700; color: var(--accent); letter-spacing: 0.06em; text-transform: uppercase; margin: 8px 0 3px; padding-bottom: 3px; border-bottom: 1px solid var(--border); }
        .rb-section:first-child { margin-top: 0; }
        .rb-section.ok   { color: var(--accent3); }
        .rb-section.err  { color: var(--warn); }
        .rb-section.warn { color: var(--warn2); }
        .rb-bullet { display: flex; gap: 6px; font-size: 0.76rem; color: var(--muted); line-height: 1.5; padding: 1px 0; }
        .rb-bullet::before { content: '›'; color: var(--accent); flex-shrink: 0; font-weight: 700; }
        .rb-code { background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius); padding: 7px 9px; margin: 5px 0; font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #93c5fd; white-space: pre-wrap; word-break: break-word; overflow-x: auto; }
        .rb-line { font-size: 0.76rem; color: var(--muted); line-height: 1.55; padding: 1px 0; }
        .rb-typing .rb-bubble { padding: 11px 13px; }
        .rb-dots { display: flex; gap: 4px; align-items: center; height: 13px; }
        .rb-dots span { width: 5px; height: 5px; border-radius: 50%; background: var(--muted); animation: rbDot 1.2s ease-in-out infinite; }
        .rb-dots span:nth-child(2) { animation-delay: 0.2s; }
        .rb-dots span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes rbDot { 0%,80%,100%{transform:scale(0.7);opacity:0.4} 40%{transform:scale(1.1);opacity:1} }

        .rb-input-area {
          flex-shrink: 0; border-top: 1px solid var(--border);
          background: var(--surface3); padding: 8px 10px;
          display: flex; flex-direction: column; gap: 6px;
        }
        .rb-input-label { font-size: 0.62rem; font-weight: 700; color: var(--dim); letter-spacing: 0.06em; text-transform: uppercase; }
        #rb-input {
          width: 100%; min-height: 50px; max-height: 110px; resize: vertical;
          background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius);
          padding: 8px 10px; font-family: 'Inter', sans-serif; font-size: 0.76rem;
          color: var(--text); line-height: 1.5; outline: none; transition: border-color 0.15s; display: block;
        }
        #rb-input:focus { border-color: rgba(59,130,246,0.45); }
        #rb-input::placeholder { color: var(--dim); }
        .rb-input-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .rb-clear { font-size: 0.68rem; color: var(--dim); background: none; border: none; cursor: pointer; font-family: inherit; padding: 3px; border-radius: 3px; transition: color 0.15s; }
        .rb-clear:hover { color: var(--muted); }
        #rb-send {
          display: flex; align-items: center; gap: 5px;
          padding: 6px 12px; border-radius: var(--radius); border: none;
          background: var(--accent); color: #fff;
          font-family: 'Inter', sans-serif; font-size: 0.72rem; font-weight: 600;
          cursor: pointer; transition: background 0.15s, transform 0.12s; flex-shrink: 0;
        }
        #rb-send:hover { background: var(--accent-hover); }
        #rb-send:active { transform: scale(0.95); }
        #rb-send:disabled { background: var(--dim); cursor: not-allowed; transform: none; }

        .run-bar { display: flex; align-items: center; gap: 8px; padding: 8px 0 10px; }
    </style>
</head>
<body>

<div class="shell">
    <div class="topbar">
        <div class="topbar-logo">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="2.5"><path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/><path d="M8 12h8M8 16h5"/></svg>
            Data<span>Sensei</span>
        </div>
        <div class="topbar-sep"></div>
        <span class="topbar-title">SQL Sandbox</span>
        
        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <a href="#" id="returnToLessonBtn" class="tb-btn" style="display: none; color: var(--accent); border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.1);">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Return to Lesson
            </a>
            <button class="tb-btn" onclick="insertSnippet('select')">SELECT</button>
            <button class="tb-btn" onclick="insertSnippet('create')">CREATE</button>
            <a href="{{ route('studentDashboard') }}" class="tb-btn" title="Back to Dashboard">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            </a>
        </div>
    </div>

    <div class="workspace">
        <aside class="sidebar">
            <div class="sidebar-head">
                <span class="sidebar-title">Database Explorer</span>
                <div class="quota" style="gap:8px;">
                    <div class="quota-bar"><div class="quota-fill" id="quota-fill"></div></div>
                    <span id="quota-text">0/5</span>
                    <button class="refresh-btn" id="refresh-btn" onclick="refreshSchema()" title="Refresh schema">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="table-list" id="table-list"></div>
        </aside>

        <main class="main">
            <div class="editor-section">
                <div class="editor-box">
                    <div class="line-nums" id="line-nums"><span>1</span></div>
                    <textarea id="query" spellcheck="false" placeholder="-- Enter SQL..."></textarea>
                </div>
                {{-- Full-width drag handle replaces the native lower-right resize grip --}}
                <div class="editor-resize-handle" id="editor-resize-handle"></div>
                <div class="run-bar">
                    <button class="tb-btn run" id="run-btn" onclick="runQuery()">
                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M11.596 8.697l-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z"/></svg>
                        Run Query
                    </button>
                    <button class="tb-btn" onclick="clearAll()">Clear</button>
                    <div class="spinner" id="spinner"></div>
                    <span id="status-badge" class="status-badge"></span>
                    <span class="shortcut"><kbd>Ctrl</kbd>+<kbd>Enter</kbd></span>
                </div>
            </div>

            <div class="results-section">
                <div class="results-header" style="display:flex; justify-content: space-between;">
                    <span class="results-label">Query Results</span>
                    <span id="row-count" style="font-size: 10px; color: var(--dim);"></span>
                </div>
                <div class="result-box">
                    <div class="idle" id="idle">
                        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/><path d="M8 12h8M8 16h5"/></svg>
                        <p>No results to display</p>
                    </div>
                    <div id="result-msg" style="padding: 20px; display: none; font-family: var(--mono); font-size: 13px;"></div>
                    <div class="tbl-scroll" id="tbl-scroll">
                        <table class="result-table">
                            <thead id="result-head"></thead>
                            <tbody id="result-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="drop-toast" id="drop-toast">
    <div class="drop-toast-title">Delete Table?</div>
    <div class="drop-toast-desc" id="drop-desc"></div>
    <div style="display:flex; gap:8px; justify-content: flex-end;">
        <button class="tb-btn" onclick="cancelDrop()">Cancel</button>
        <button class="tb-btn" style="background:var(--warn); color:white; border:none;" onclick="confirmDrop()">Delete</button>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     🤖 AI REVIEW CHATBOT WIDGET HTML
═══════════════════════════════════════════════════ --}}
<button id="rb-toggle" onclick="ReviewBot.toggle()" title="AI SQL Reviewer">
  <div class="rb-dot"></div>
  <svg class="rb-icon-chat" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3-3-3z"/>
  </svg>
  <svg class="rb-icon-close" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
  </svg>
</button>

<div id="rb-panel">
  <div class="rb-header">
    <div class="rb-header-icon">
      <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/>
      </svg>
    </div>
    <div style="flex:1;min-width:0">
      <div class="rb-header-title">AI SQL Reviewer</div>
      <div class="rb-header-sub">Auto-reviews on every Run Query</div>
    </div>
    <div class="rb-status" id="rb-status">
      <div class="rb-status-dot"></div><span>Ready</span>
    </div>
  </div>

  <div id="rb-msgs"></div>

  <div class="rb-input-area">
    <div class="rb-input-label">Ask a follow-up</div>
    <textarea id="rb-input" placeholder="e.g. Why is this query slow? How do I optimize it?" onkeydown="ReviewBot.handleKey(event)"></textarea>
    <div class="rb-input-row">
      <button class="rb-clear" onclick="ReviewBot.clear()">Clear chat</button>
      <button id="rb-send" onclick="ReviewBot.sendFollowUp()">
        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        Ask
      </button>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     🤖 REVIEW BOT JS MODULE
═══════════════════════════════════════════════════ --}}
<script>
// ── ReviewBot (SQL Sandbox) ───────────────────────────────────────────────────
// Calls POST /api/code-review with { code, language: 'mysql' }
// Returns JSON { ok, message }
const REVIEW_URL = '/api/code-review';

const ReviewBot = (() => {
  let _open     = false;
  let _busy     = false;
  let _lastCode = '';
  let _lastLang = 'mysql';
  const _history = [];

  const $toggle = () => document.getElementById('rb-toggle');
  const $panel  = () => document.getElementById('rb-panel');
  const $msgs   = () => document.getElementById('rb-msgs');
  const $status = () => document.getElementById('rb-status');
  const $send   = () => document.getElementById('rb-send');
  const $input  = () => document.getElementById('rb-input');

  /* ── Open / close ── */
  function toggle() {
    _open = !_open;
    $panel().classList.toggle('rb-open', _open);
    $toggle().classList.toggle('rb-open', _open);
    if (_open && $msgs().children.length === 0) _addWelcome();
    if (_open) setTimeout(() => $input().focus(), 220);
  }

  function _open_panel() {
    if (_open) return;
    _open = true;
    $panel().classList.add('rb-open');
    $toggle().classList.add('rb-open');
  }

  /* ── Welcome message ── */
  function _addWelcome() {
    _addBot(
      '<div class="rb-line">Hi! I\'ll automatically review your SQL query every time you hit <strong style="color:var(--text)">Run Query</strong>.</div>' +
      '<div class="rb-line" style="margin-top:5px;color:var(--dim)">You can also ask me follow-up questions about your query below.</div>'
    );
  }

  /* ── Called by runQuery() automatically ── */
  function autoReview(sql) {
    if (!sql || !sql.trim()) return;
    _lastCode = sql;
    _lastLang = 'mysql';
    _history.length = 0; // reset context per new run

    _open_panel();
    $toggle().classList.add('rb-has-review');

    // Show user bubble — truncated SQL preview
    const preview = sql.length > 220 ? sql.slice(0, 217) + '…' : sql;
    _addUser(preview);

    _addBot(
      '<div class="rb-line">I see you just ran a <strong style="color:var(--text)">SQL query</strong> — let me review it now...</div>'
    );

    _sendReview(sql, 'mysql');
  }

  /* ── Code generation detection ── */
  const CODE_GEN_RE = /\b(generate|write\s+(?:me\s+)?(?:a|the|this|some)?|create|give\s+me|produce|make\s+me|implement|build)\b.{0,40}\b(code|function|class|script|program|solution|example|snippet|query|sql)\b/i;
  function _isCodeGenRequest(text) {
    return CODE_GEN_RE.test(text);
  }

  /* ── Manual follow-up from input ── */
  function sendFollowUp() {
    const question = $input().value.trim();
    if (!question || _busy) return;

    // Block code generation requests on the frontend
    if (_isCodeGenRequest(question)) {
      $input().value = '';
      _addUser(question);
      _addBot(
        '<div class="rb-line" style="color:var(--warn2)">⚠️ I\'m an <strong style="color:var(--text)">SQL reviewer</strong>, not a query generator. ' +
        'I can\'t write or produce SQL for you — but I can help you understand issues in your existing query, explain concepts, or point you in the right direction.</div>'
      );
      return;
    }

    $input().value = '';
    _addUser(question);

    const messages = _history.length
      ? [..._history, { role: 'user', content: question }]
      : [{ role: 'user', content: `Regarding this SQL query:\n\`\`\`sql\n${_lastCode}\n\`\`\`\n\n${question}` }];

    _callAI(messages);
  }

  function handleKey(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); sendFollowUp(); }
  }

  /* ── Clear ── */
  function clear() {
    $msgs().innerHTML = '';
    _history.length = 0;
    _addWelcome();
  }

  /* ── Core: send SQL for review ── */
  async function _sendReview(code, lang) {
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'review');
      form.append('code',     code);
      form.append('language', lang || 'mysql');

      const res  = await fetch(REVIEW_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: form,
      });
      const data = await res.json();
      _removeTyping(typingId);

      if (data.ok || data.message) {
        const msg   = data.message || data.review || '';
        const html  = _formatReview(msg);
        _addBot(html);
        _history.push({ role: 'assistant', content: msg });
      } else {
        _addBot(`<div class="rb-line" style="color:var(--warn)">${escH(data.error || 'Review failed. Check your backend route.')}</div>`);
      }
    } catch (err) {
      _removeTyping(typingId);
      _addBot('<div class="rb-line" style="color:var(--warn)">Could not reach the review endpoint. Make sure <code style="font-family:JetBrains Mono,monospace;font-size:0.68rem">/api/code-review</code> is defined in your Laravel routes.</div>');
    } finally {
      _setBusy(false);
    }
  }

  /* ── Core: send follow-up ── */
  async function _callAI(messages) {
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'chat');
      form.append('code',     _lastCode);
      form.append('language', _lastLang);
      form.append('question', messages[messages.length - 1].content);

      const res  = await fetch(REVIEW_URL, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: form,
      });
      const data = await res.json();
      _removeTyping(typingId);

      const msg  = data.message || data.review || data.error || 'No response.';
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } catch (err) {
      _removeTyping(typingId);
      _addBot('<div class="rb-line" style="color:var(--warn)">Request failed.</div>');
    } finally {
      _setBusy(false);
    }
  }

  /* ── DOM helpers ── */
  function _addUser(text) {
    const el = _buildMsg('rb-user', escH(text));
    $msgs().appendChild(el);
    _scroll();
  }

  function _addBot(html) {
    const el = _buildMsg('rb-bot', html);
    $msgs().appendChild(el);
    _scroll();
  }

  function _addTyping() {
    const id  = 'rb-typing-' + Date.now();
    const el  = _buildMsg('rb-bot rb-typing', '<div class="rb-dots"><span></span><span></span><span></span></div>');
    el.id     = id;
    $msgs().appendChild(el);
    _scroll();
    return id;
  }

  function _removeTyping(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
  }

  function _buildMsg(cls, contentHtml) {
    const w = document.createElement('div');
    w.className = 'rb-msg ' + cls;
    const label = cls.includes('rb-user') ? 'ME' : 'AI';
    w.innerHTML = `<div class="rb-avatar">${label}</div><div class="rb-body"><div class="rb-bubble">${contentHtml}</div></div>`;
    return w;
  }

  function _scroll() { requestAnimationFrame(() => { const m = $msgs(); m.scrollTop = m.scrollHeight; }); }

  function _setBusy(on) {
    _busy = on;
    $send().disabled = on;
    $send().innerHTML = on
      ? '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Wait…'
      : '<svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Ask';
    const $s = $status();
    $s.className = on ? 'rb-status rb-busy' : 'rb-status';
    $s.innerHTML = on
      ? '<div class="rb-status-dot"></div><span>Reviewing…</span>'
      : '<div class="rb-status-dot"></div><span>Ready</span>';
  }

  /* ── Review formatter ── */
  function _formatReview(raw) {
    if (!raw || !raw.trim()) return '<div class="rb-line" style="color:var(--dim)">(No response)</div>';
    const SECTION = /^(Status|Issues?|Fix|Suggestion|Suggestions|Warning|Warnings|Notes?|Summary|Result|Performance|Optimization)s?:/i;
    const segs = raw.split(/(```[a-z]*\n?)/);
    let html = '', inCode = false, codeAcc = '';
    for (const seg of segs) {
      if (/^```/.test(seg)) { if (inCode) { html += `<div class="rb-code">${escH(codeAcc.trimEnd())}</div>`; codeAcc = ''; } inCode = !inCode; continue; }
      if (inCode) { codeAcc += seg; continue; }
      for (const line of seg.split('\n')) {
        const t = line.trim(); if (!t) continue;
        if (SECTION.test(t)) {
          const cls = /correct|clean|good|pass|ok/i.test(t) ? 'rb-section ok' : /error|issue|fail|wrong/i.test(t) ? 'rb-section err' : 'rb-section';
          html += `<div class="${cls}">${escH(t)}</div>`;
        } else if (/^[-•*]\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^[-•*]\s+/, ''))}</div>`;
        } else if (/^\d+\.\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^\d+\.\s+/, ''))}</div>`;
        } else {
          const lineHtml = escH(t).replace(/`([^`]+)`/g, '<code style="font-family:JetBrains Mono,monospace;font-size:0.68rem;background:var(--surface3);padding:1px 4px;border-radius:3px;color:#93c5fd">$1</code>');
          html += `<div class="rb-line">${lineHtml}</div>`;
        }
      }
    }
    if (inCode && codeAcc.trim()) html += `<div class="rb-code">${escH(codeAcc.trimEnd())}</div>`;
    return html || '<div class="rb-line" style="color:var(--dim)">(Empty response)</div>';
  }

  function escH(s) {
    return (s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  return { toggle, autoReview, sendFollowUp, handleKey, clear };
})();
</script>

<script>
(() => {
    // ── Guard: CSRF token must exist ──────────────────────────────────────
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        console.error('[SQL Sandbox] CSRF meta tag missing — requests will fail.');
    }
    const CSRF = csrfMeta ? csrfMeta.content : '';

    // ── BASE URL: use the named route to avoid subdirectory mismatches ────
    // Previously `'{{ url("/sql-sandbox") }}'` could resolve incorrectly
    // if APP_URL isn't set or the app is served from a subdirectory.
    // route() is always authoritative in Laravel.
    const BASE = '/sql-sandbox';

    // UI References
    const $query     = document.getElementById('query');
    const $lineNums  = document.getElementById('line-nums');
    const $runBtn    = document.getElementById('run-btn');
    const $spinner   = document.getElementById('spinner');
    const $idle      = document.getElementById('idle');
    const $resultMsg = document.getElementById('result-msg');
    const $tblScroll = document.getElementById('tbl-scroll');
    const $badge     = document.getElementById('status-badge');
    const $rowCount  = document.getElementById('row-count');
    const $tableList  = document.getElementById('table-list');
    const $refreshBtn = document.getElementById('refresh-btn');

    // ── Session Integration ───────────────────────────────────────────────

    // 1. Handle "Return to Lesson" Logic
    const sessionReturnUrl = @json($returnUrl ?? '');
    if (sessionReturnUrl) {
        sessionStorage.setItem('datasensei_return_url', sessionReturnUrl);
    }

    const returnUrl = sessionStorage.getItem('datasensei_return_url');
    const returnBtn = document.getElementById('returnToLessonBtn');
    if (returnUrl && returnBtn) {
        returnBtn.style.display = 'inline-flex';
        returnBtn.href = returnUrl;
        returnBtn.addEventListener('click', () => sessionStorage.removeItem('datasensei_return_url'));
    }

    // 2. Handle Incoming SQL Code from Lesson
    const serverCode   = @json($pendingCode ?? '');
    const clientCode   = sessionStorage.getItem('datasensei_pending_sql_code') || '';
    const incomingCode = serverCode || clientCode;
    if (incomingCode) {
        $query.value = incomingCode;
        updateLines();
        sessionStorage.removeItem('datasensei_pending_sql_code');
    }

    // ── Snippet helpers ───────────────────────────────────────────────────

    window.insertSnippet = k => {
        const s = { 
            select: 'SELECT * FROM table_name LIMIT 10;',
            create: 'CREATE TABLE students (\n  id INTEGER PRIMARY KEY,\n  name TEXT,\n  grade INTEGER\n);'
        };
        $query.value = s[k] || '';
        $query.focus();
        updateLines();
    };

    // ── Line numbers ──────────────────────────────────────────────────────

    function updateLines() {
        const n = $query.value.split('\n').length;
        $lineNums.innerHTML = Array.from({length: n}, (_, i) => `<span>${i + 1}</span>`).join('');
    }
    $query.addEventListener('input', updateLines);
    $query.addEventListener('scroll', () => { $lineNums.scrollTop = $query.scrollTop; });

    // ── Full-width bottom resize handle ───────────────────────────────────
    (function initResize() {
        const handle = document.getElementById('editor-resize-handle');
        if (!handle) return;

        let startY    = 0;
        let startH    = 0;
        const MIN_H   = 80;
        const MAX_H   = 600;

        handle.addEventListener('mousedown', onMouseDown);
        handle.addEventListener('touchstart', onTouchStart, { passive: false });

        function onMouseDown(e) {
            e.preventDefault();
            startDrag(e.clientY);
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        function onTouchStart(e) {
            e.preventDefault();
            startDrag(e.touches[0].clientY);
            document.addEventListener('touchmove', onTouchMove, { passive: false });
            document.addEventListener('touchend', onTouchEnd);
        }

        function startDrag(y) {
            startY = y;
            startH = $query.getBoundingClientRect().height;
            handle.classList.add('dragging');
            document.body.style.cursor    = 'ns-resize';
            document.body.style.userSelect = 'none';
        }

        function onMouseMove(e) { applyHeight(e.clientY); }
        function onTouchMove(e) { e.preventDefault(); applyHeight(e.touches[0].clientY); }

        function applyHeight(y) {
            const newH = Math.min(MAX_H, Math.max(MIN_H, startH + (y - startY)));
            $query.style.height = newH + 'px';
        }

        function onMouseUp()  { endDrag(); document.removeEventListener('mousemove', onMouseMove); document.removeEventListener('mouseup', onMouseUp); }
        function onTouchEnd() { endDrag(); document.removeEventListener('touchmove', onTouchMove); document.removeEventListener('touchend', onTouchEnd); }

        function endDrag() {
            handle.classList.remove('dragging');
            document.body.style.cursor    = '';
            document.body.style.userSelect = '';
        }
    })();

    // ── Result helpers ────────────────────────────────────────────────────

    function showResult(isSuccess, messageOrCols, rows) {
        $idle.style.display = 'none';

        if (isSuccess && Array.isArray(messageOrCols) && messageOrCols.length > 0) {
            showTable(messageOrCols, rows);
            $badge.className  = 'status-badge success';
            $badge.textContent = 'SUCCESS';
        } else if (isSuccess) {
            $tblScroll.classList.remove('show');
            $resultMsg.style.display = 'block';
            $resultMsg.style.color   = 'var(--accent3)';
            $resultMsg.textContent   = messageOrCols || 'Executed successfully.';
            $badge.className  = 'status-badge success';
            $badge.textContent = 'SUCCESS';
            loadTables();
        } else {
            $tblScroll.classList.remove('show');
            $resultMsg.style.display = 'block';
            $resultMsg.style.color   = 'var(--warn)';
            $resultMsg.textContent   = messageOrCols || 'An unknown error occurred.';
            $badge.className  = 'status-badge error';
            $badge.textContent = 'ERROR';
        }
        $badge.style.display = '';
    }

    function showTable(cols, rows) {
        $resultMsg.style.display = 'none';
        $tblScroll.classList.add('show');
        document.getElementById('result-head').innerHTML =
            `<tr>${cols.map(c => `<th>${c}</th>`).join('')}</tr>`;
        document.getElementById('result-body').innerHTML =
            rows.map(r => `<tr>${r.map(v => `<td>${v ?? 'NULL'}</td>`).join('')}</tr>`).join('');
        $rowCount.textContent = `${rows.length} row(s)`;
    }

    // ── Shared fetch wrapper ──────────────────────────────────────────────
    async function apiFetch(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
        };

        const mergedOptions = {
            ...defaults,
            ...options,
            headers: { ...defaults.headers, ...(options.headers || {}) },
        };

        const res = await fetch(url, mergedOptions);

        const contentType = res.headers.get('content-type') || '';
        const isJson      = contentType.includes('application/json');

        if (!isJson) {
            let message;
            if (res.status === 419) {
                message = '419 — Session expired. Please refresh the page and try again.';
            } else if (res.status === 401) {
                message = '401 — Unauthenticated. Please log in again.';
            } else if (res.status === 403) {
                message = '403 — Forbidden. You do not have permission to do that.';
            } else if (res.redirected || res.status === 302) {
                message = 'Session expired or redirected to login — please refresh the page.';
            } else {
                message = `HTTP ${res.status} — Unexpected response from server.`;
            }
            return { ok: false, status: res.status, data: { status: 'error', message } };
        }

        const data = await res.json();
        return { ok: res.ok, status: res.status, data };
    }

    // ── Refresh Schema ────────────────────────────────────────────────────
    window.refreshSchema = async () => {
        $refreshBtn.classList.add('spinning');
        try {
            await loadTables();
        } finally {
            $refreshBtn.classList.remove('spinning');
        }
    };

    // ── Run Query ─────────────────────────────────────────────────────────
    window.runQuery = async () => {
        const q = $query.value.trim();
        if (!q) return;

        $runBtn.disabled = true;
        $spinner.classList.add('show');
        $badge.style.display = 'none';

        // 🤖 Trigger AI review on every Run Query
        ReviewBot.autoReview(q);

        try {
            const { data } = await apiFetch(BASE + '/execute', {
                method: 'POST',
                body:   JSON.stringify({ query: q }),
            });

            if (data.status === 'success') {
                const cols = data.columns && data.columns.length > 0 ? data.columns : null;
                showResult(true, cols ?? data.message, data.rows ?? []);
                // Refresh sidebar schema in case CREATE/DROP/ALTER ran
                loadTables();
            } else {
                showResult(false, data.message || 'Query failed.');
            }

        } catch (err) {
            // Only true network failures (offline, DNS, CORS) land here now.
            console.error('[SQL Sandbox] runQuery network error:', err);
            showResult(false, 'Network error — could not reach the server. Check your connection and try again.');
        } finally {
            $runBtn.disabled = false;
            $spinner.classList.remove('show');
        }
    };

    // ── Table Sidebar ─────────────────────────────────────────────────────
    window.loadTables = async () => {
        try {
            const { data } = await apiFetch(BASE + '/tables');
            if (data.status !== 'success') return;

            const fill = document.getElementById('quota-fill');
            if (fill) {
                fill.style.width = `${(data.count / data.limit) * 100}%`;
                fill.classList.toggle('full', data.count >= data.limit);
            }
            document.getElementById('quota-text').textContent = `${data.count}/${data.limit}`;

            if (data.tables.length === 0) {
                $tableList.innerHTML = '<div class="empty-state"><span class="icon">🗄️</span>No tables yet</div>';
                return;
            }

            $tableList.innerHTML = data.tables.map(t => `
                <div class="tbl-item">
                    <div class="tbl-header" onclick="this.parentElement.classList.toggle('open')">
                        <svg class="tbl-chevron" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
                        <span class="tbl-name">${t.name}</span>
                        <div class="tbl-actions">
                             <button class="tbl-btn" title="Delete Table" onclick="event.stopPropagation(); showDropToast('${t.name}')">
                                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                             </button>
                        </div>
                    </div>
                    <div class="col-list">
                        ${t.columns.map(c => `<div class="col-row"><span class="col-name">${c.name}</span><span class="col-type">${c.type}</span></div>`).join('')}
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.warn('[SQL Sandbox] loadTables failed:', e);
        }
    };

    // ── Drop Table Toast ──────────────────────────────────────────────────
    let tableToDrop = null;

    window.showDropToast = name => {
        tableToDrop = name;
        document.getElementById('drop-desc').textContent = `Are you sure you want to delete '${name}'?`;
        document.getElementById('drop-toast').classList.add('show');
    };

    window.cancelDrop = () => {
        document.getElementById('drop-toast').classList.remove('show');
        tableToDrop = null;
    };

    window.confirmDrop = async () => {
        if (!tableToDrop) return;
        try {
            await apiFetch(`${BASE}/tables/${tableToDrop}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            loadTables();
        } catch (e) {
            console.error('[SQL Sandbox] confirmDrop failed:', e);
        } finally {
            cancelDrop();
        }
    };

    // ── Clear ─────────────────────────────────────────────────────────────
    window.clearAll = () => {
        $query.value = '';
        updateLines();
        $idle.style.display      = 'flex';
        $tblScroll.classList.remove('show');
        $resultMsg.style.display = 'none';
        $badge.style.display     = 'none';
        $rowCount.textContent    = '';
    };

    // ── Keyboard shortcuts ────────────────────────────────────────────────
    $query.addEventListener('keydown', e => {
        if (e.key === 'Tab') {
            e.preventDefault();
            const s = $query.selectionStart;
            $query.value = $query.value.slice(0, s) + '  ' + $query.value.slice($query.selectionEnd);
            $query.selectionStart = $query.selectionEnd = s + 2;
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') runQuery();
    });

    // ── Init ──────────────────────────────────────────────────────────────
    loadTables();
})();
</script>
</body>
</html>