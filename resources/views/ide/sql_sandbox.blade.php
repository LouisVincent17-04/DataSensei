<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SQL Sandbox — DataSensei</title>
    @include('partials.brand-head')
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
        .topbar-logo .ds-brand-logo__accent { color: var(--accent); }
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
        .topbar-nav-divider { width: 1px; height: 20px; background: var(--border); margin: 0 2px 0 6px; flex-shrink: 0; }
        .tb-btn.dashboard-btn { color: var(--muted); border-color: transparent; background: transparent; white-space: nowrap; }
        .tb-btn.dashboard-btn:hover { color: var(--text); border-color: var(--border-hover); background: var(--surface2); }
        .tb-btn.run { background: var(--accent3); color: #fff; border-color: var(--accent3); font-weight: 600; }
        .tb-btn.run:hover { background: #0ea472; }
        .tb-btn.run:disabled { opacity: 0.6; cursor: not-allowed; }
        .sql-sample-select {
            height: 30px;
            min-width: 190px;
            border: 1px solid var(--border);
            background: var(--surface3);
            color: var(--muted);
            border-radius: var(--radius);
            padding: 0 10px;
            font-family: inherit;
            font-size: 0.75rem;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }
        .sql-sample-select:hover,
        .sql-sample-select:focus { color: var(--text); border-color: var(--border-hover); background: var(--surface2); }
        .sql-sample-select option { background: var(--surface); color: var(--text); }
        
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

        .drop-toast {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100dvh !important;
            max-width: none !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 20px !important;
            border: 0 !important;
            background: transparent !important;
            overflow: hidden;
            z-index: 2147483647;
        }
        .drop-toast[open] {
            display: grid !important;
            place-items: center !important;
        }
        .drop-toast::backdrop {
            background: rgba(3, 8, 18, 0.72);
            backdrop-filter: blur(3px);
        }
        .drop-dialog {
            width: min(420px, calc(100vw - 40px));
            margin: auto;
            background: var(--surface);
            border: 1px solid var(--border-hover);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 18px 55px rgba(0,0,0,0.6);
            animation: dropDialogIn 0.16s ease-out;
        }
        @keyframes dropDialogIn {
            from { opacity: 0; transform: translateY(8px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .drop-toast-title { color: var(--warn); font-weight: 700; font-size: 0.95rem; margin-bottom: 6px; }
        .drop-toast-desc { font-size: 0.8rem; color: var(--muted); margin-bottom: 16px; line-height: 1.55; }

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
            @include('partials.brand-logo', [
                'variant' => 'topbar',
                'size' => 'compact',
                'showText' => true,
                'href' => route('studentDashboard'),
            ])
        </div>
        <div class="topbar-sep"></div>
        <span class="topbar-title">SQL Sandbox</span>
        
        <div style="margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <a href="{{ route('studentDashboard') }}" id="returnToLessonBtn" class="tb-btn" style="display: none; color: var(--accent); border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.1);">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Return to Lesson
            </a>
            <button class="tb-btn" onclick="insertSnippet('select')">SELECT</button>
            <button class="tb-btn" onclick="insertSnippet('create')">CREATE</button>
            @include('student.partials.notification-center', ['variant' => 'topbar'])
            <span class="topbar-nav-divider" aria-hidden="true"></span>
            <a href="{{ route('studentDashboard') }}" class="tb-btn dashboard-btn" title="Return to dashboard" aria-label="Return to dashboard">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M19 12H5m7 7-7-7 7-7"/></svg>
                <span>Dashboard</span>
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
                    <select class="sql-sample-select" id="sqlSampleSelect" title="Auto-generate sample SQL" onchange="insertSqlSample(this.value); this.value='';">
                        <option value="">Generate SQL sample...</option>
                        <option value="simple_select">Simple SELECT</option>
                        <option value="create_insert">CREATE + INSERT + SELECT</option>
                        <option value="where_order">WHERE + ORDER BY</option>
                        <option value="join_group">JOIN + GROUP BY</option>
                        <option value="safe_update">Safe UPDATE</option>
                    </select>
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

<dialog class="drop-toast" id="drop-toast" aria-labelledby="drop-title" aria-describedby="drop-desc">
    <div class="drop-dialog">
        <div class="drop-toast-title" id="drop-title">Delete Table?</div>
        <div class="drop-toast-desc" id="drop-desc"></div>
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button class="tb-btn" id="drop-cancel-btn" type="button" onclick="cancelDrop()">Cancel</button>
            <button class="tb-btn" id="drop-confirm-btn" type="button" style="background:var(--warn); color:white; border:none;" onclick="confirmDrop()">Delete</button>
        </div>
    </div>
</dialog>

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
// Calls POST /api/code-review with { code, language: 'sqlite' }
// Returns JSON { ok, message }
const REVIEW_URL = @json(route('api.code-review'));
const REVIEW_CLIENT_TIMEOUT_MS = {{ (int) config('code_execution.ollama.client_timeout_ms', 14000) }};
const EXPIRED_LOGIN_URL = @json(route('login', ['expired' => 1]));
const REVIEW_POLL_MS = {{ max(1000, (int) config('code_execution.ollama.background_poll_interval_ms', 2000)) }};

// Load the AI reviewer model while the page is open so the first review after
// a pause does not wait for the model to load. Background request: it does not
// count as activity for the idle sign-out.
(() => {
  const warmUrl = @json(route('api.code-review.warm'));
  let lastWarm = 0;
  const warm = () => {
    if (document.visibilityState !== 'visible' || Date.now() - lastWarm < 60000) return;
    lastWarm = Date.now();
    fetch(warmUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-DataSensei-Background': '1',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
    }).catch(() => {});
  };
  warm();
  setInterval(warm, 4 * 60 * 1000);
  document.addEventListener('visibilitychange', warm);
})();

function redirectExpiredSession(destination = null) {
  if (window.DataSenseiSession?.redirectToLogin) {
    window.DataSenseiSession.redirectToLogin(true, destination);
    return;
  }

  window.__dataSenseiSessionEnding = true;
  try {
    window.dispatchEvent(new CustomEvent('datasensei:session-ending', {
      detail: { expired: true },
    }));
  } catch (_) {}
  window.location.replace(destination || EXPIRED_LOGIN_URL);
}

async function redirectWhenSessionExpired(response) {
  if (response.status !== 401 && response.status !== 419) return false;

  let destination = null;
  try {
    const payload = await response.clone().json();
    destination = payload?.login_url || null;
  } catch (_) {}

  redirectExpiredSession(destination);
  return true;
}

const ReviewBot = (() => {
  let _open     = false;
  let _busy     = false;
  let _lastCode = '';
  let _lastLang = 'sqlite';
  let _lastRunOutput = '';
  let _activeController = null;
  // A review that outlived the request keeps going on the server.
  let _backgroundWait = null;
  let _generation = 0;
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
  function autoReview(sql, runOutput = '') {
    if (!sql || !sql.trim()) return;
    if (_backgroundWait) _backgroundWait.cancel();
    _lastCode = sql;
    _lastLang = 'sqlite';
    _lastRunOutput = runOutput || '';
    _history.length = 0; // reset context per new run

    _open_panel();
    $toggle().classList.add('rb-has-review');

    // Show user bubble — truncated SQL preview
    const preview = sql.length > 220 ? sql.slice(0, 217) + '…' : sql;
    _addUser(preview);

    _addBot(
      '<div class="rb-line">I see you just ran a <strong style="color:var(--text)">SQL query</strong> — let me review it now...</div>'
    );

    _sendReview(sql, 'sqlite');
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

  function _boundedRunOutput(output) {
    const value = String(output || '');
    const limit = 12000;
    if (value.length <= limit) return value;
    return `${value.slice(0, 7000)}\n\n[output shortened]\n\n${value.slice(-4500)}`;
  }

  function _localFallback(runOutput, isChat = false) {
    const output = String(runOutput || '');
    const hasError = /syntax error|no such (?:table|column)|constraint failed|ambiguous column|query failed|\[security\]|unsupported/i.test(output);
    if (hasError) {
      return 'Status: Has Issues\nFeedback: The SQL Sandbox reported an execution error. The detailed reviewer was unavailable, but the original database error remains valid.\nSteps to Fix:\n- Read the reported error and identify the affected statement.\n- Check the table name, column names, clause order, and database constraints.\n- Correct the underlying cause and run the query again.';
    }
    return isChat
      ? 'Status: Review Limited\nFeedback: The detailed reviewer was unavailable within the response deadline. Review the current query and result, then ask a narrower question or try again.'
      : 'Status: Review Limited\nFeedback: The query completed, but the detailed reviewer was unavailable within the response deadline. Check that the result matches the intended rows and columns.';
  }

  /* ── Wait for a review that continues on the server ── */
  function _formatElapsed(totalSeconds) {
    const seconds = Math.max(0, Math.round(totalSeconds));
    if (seconds < 60) return `${seconds}s`;
    return `${Math.floor(seconds / 60)}m ${String(seconds % 60).padStart(2, '0')}s`;
  }

  async function _awaitBackground(pending, typingId) {
    const startedAt = Date.now();
    const wait = { cancelled: false, onCancel: null };
    const bubble = document.getElementById(typingId);
    const note = document.createElement('div');
    note.className = 'rb-line';
    note.style.cssText = 'color:var(--dim);margin-top:6px;font-size:.72rem';
    if (bubble) bubble.querySelector('.rb-bubble').appendChild(note);
    const tick = setInterval(() => {
      note.textContent = `Still reviewing… ${_formatElapsed((Date.now() - startedAt) / 1000)}. This is taking longer than usual, but the review keeps going and will appear here.`;
    }, 1000);

    wait.cancel = () => {
      wait.cancelled = true;
      clearInterval(tick);
      const current = document.getElementById(typingId);
      if (current) {
        current.removeAttribute('id');
        current.classList.remove('rb-typing');
        current.querySelector('.rb-bubble').innerHTML = '<div class="rb-line" style="color:var(--dim)">This review was replaced by your newer query.</div>';
      }
      if (wait.onCancel) wait.onCancel();
    };
    _backgroundWait = wait;

    let failures = 0;
    try {
      while (true) {
        await new Promise(resolve => {
          const timer = setTimeout(resolve, REVIEW_POLL_MS);
          wait.onCancel = () => { clearTimeout(timer); resolve(); };
        });
        if (wait.cancelled) {
          const error = new Error('Replaced by a newer query.');
          error.superseded = true;
          throw error;
        }

        let response;
        let data = {};
        try {
          response = await fetch(pending.status_url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-DataSensei-Background': '1' },
            cache: 'no-store',
          });
          if (await redirectWhenSessionExpired(response)) {
            const error = new Error('Your session expired. Redirecting to sign in.');
            error.sessionExpired = true;
            throw error;
          }
          data = await _readJsonResponse(response);
        } catch (error) {
          if (error?.sessionExpired) throw error;
          failures++;
          if (failures >= 6) throw error;
          continue;
        }

        if (response.status === 404 || data.status === 'missing') {
          throw new Error(data.message || 'The background review is no longer available.');
        }
        if (!response.ok) {
          failures++;
          if (failures >= 6) throw new Error(`Review status failed with HTTP ${response.status}.`);
          continue;
        }

        failures = 0;
        if (data.status === 'done') return data.message || '';
      }
    } finally {
      clearInterval(tick);
      if (_backgroundWait === wait) _backgroundWait = null;
    }
  }

  /* ── Core: send SQL for review ── */
  async function _sendReview(code, lang) {
    const generation = ++_generation;
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'review');
      form.append('code',     code);
      form.append('language', lang || 'sqlite');
      form.append('run_output', _boundedRunOutput(_lastRunOutput));

      const res  = await _postReview(form);
      const data = await _readJsonResponse(res);

      if (!res.ok || !data.ok) {
        throw new Error(data.message || `Review request failed with HTTP ${res.status}.`);
      }

      const msg  = data.pending
        ? await _awaitBackground(data, typingId)
        : (data.message || '');
      _removeTyping(typingId);
      const html = _formatReview(msg);
      _addBot(html);
      _history.push({ role: 'assistant', content: msg });
    } catch (err) {
      _removeTyping(typingId);
      if (err?.sessionExpired || err?.superseded) return;
      const msg = _localFallback(_lastRunOutput, false);
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } finally {
      if (generation === _generation) _setBusy(false);
    }
  }

  /* ── Core: send follow-up ── */
  async function _callAI(messages) {
    const generation = ++_generation;
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'chat');
      form.append('code',     _lastCode);
      form.append('language', _lastLang);
      form.append('question', messages[messages.length - 1].content);
      form.append('run_output', _boundedRunOutput(_lastRunOutput));
      form.append('previous_context', _history.map(h => h.content).slice(-4).join('\n---\n'));
      form.append('stream', '1');

      let finalMessage = '';
      let pending = null;
      const res = await _postReview(form, async response => {
        await _readEventStream(response, event => {
          if (event.event === 'done') finalMessage = event.data.message || '';
          if (event.event === 'pending' && event.data.status_url) pending = event.data;
        });
      });

      if (pending) finalMessage = await _awaitBackground(pending, typingId);
      _removeTyping(typingId);

      if (!res.ok || !finalMessage) throw new Error(`Follow-up request failed with HTTP ${res.status}.`);

      const msg  = finalMessage;
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } catch (err) {
      _removeTyping(typingId);
      if (err?.sessionExpired || err?.superseded) return;
      const msg = _localFallback(_lastRunOutput, true);
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } finally {
      if (generation === _generation) _setBusy(false);
    }
  }

  async function _postReview(form, streamHandler = null) {
    if (_activeController) _activeController.abort();
    const controller = new AbortController();
    _activeController = controller;
    const timer = setTimeout(() => controller.abort(), REVIEW_CLIENT_TIMEOUT_MS);

    try {
      const response = await fetch(REVIEW_URL, {
        method: 'POST',
        headers: {
          'Accept': streamHandler ? 'application/json, text/event-stream' : 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: form,
        signal: controller.signal,
      });
      if (await redirectWhenSessionExpired(response)) {
        const error = new Error('Your session expired. Redirecting to sign in.');
        error.sessionExpired = true;
        throw error;
      }
      if (streamHandler) await streamHandler(response);
      return response;
    } finally {
      clearTimeout(timer);
      if (_activeController === controller) _activeController = null;
    }
  }

  async function _readEventStream(response, onEvent) {
    if (!response.ok || !response.body) {
      const data = await _readJsonResponse(response);
      throw new Error(data.message || `Request failed with HTTP ${response.status}.`);
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';
    while (true) {
      const { value, done } = await reader.read();
      buffer += decoder.decode(value || new Uint8Array(), { stream: !done });
      const chunks = buffer.split('\n\n');
      buffer = chunks.pop() || '';
      for (const chunk of chunks) {
        let event = 'message';
        const dataLines = [];
        for (const line of chunk.split('\n')) {
          if (line.startsWith('event:')) event = line.slice(6).trim();
          if (line.startsWith('data:')) dataLines.push(line.slice(5).trim());
        }
        if (!dataLines.length) continue;
        let data;
        try { data = JSON.parse(dataLines.join('\n')); } catch (_) { continue; }
        onEvent({ event, data });
      }
      if (done) break;
    }
  }

  async function _readJsonResponse(response) {
    const text = await response.text();

    if (!text.trim()) return {};

    try {
      return JSON.parse(text);
    } catch (_) {
      throw new Error(`Server returned a non-JSON response (HTTP ${response.status}).`);
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
    const proseOnly = raw.replace(/```[\s\S]*?```/g, '').replace(/`/g, '');
    let html = '';
    for (const line of proseOnly.split('\n')) {
        const t = line.trim(); if (!t) continue;
        if (SECTION.test(t)) {
          const cls = /correct|clean|good|pass|ok/i.test(t) ? 'rb-section ok' : /error|issue|fail|wrong/i.test(t) ? 'rb-section err' : 'rb-section';
          html += `<div class="${cls}">${escH(t)}</div>`;
        } else if (/^[-•*]\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^[-•*]\s+/, ''))}</div>`;
        } else if (/^\d+\.\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^\d+\.\s+/, ''))}</div>`;
        } else {
          html += `<div class="rb-line">${escH(t)}</div>`;
        }
    }
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

    // Named routes remain correct when the application is hosted below a
    // subdirectory or its public URL changes.
    const EXECUTE_URL = @json(route('sql-sandbox.execute'));
    const TABLES_URL  = @json(route('sql-sandbox.tables'));

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
    function sameOriginUrl(value) {
        if (!value) return null;
        try {
            const parsed = new URL(value, window.location.origin);
            return parsed.origin === window.location.origin && ['http:', 'https:'].includes(parsed.protocol)
                ? parsed.href
                : null;
        } catch (_) {
            return null;
        }
    }

    const sessionReturnUrl = sameOriginUrl(@json($returnUrl ?? ''));
    if (sessionReturnUrl) {
        sessionStorage.setItem('datasensei_return_url', sessionReturnUrl);
    }

    const returnUrl = sameOriginUrl(sessionStorage.getItem('datasensei_return_url'));
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

    // ── Sample SQL generator ────────────────────────────────────────────────

    const SQL_SAMPLES = {
        simple_select: `-- Simple SELECT
-- Run this after creating a table, or change the table name to your own table.
SELECT * FROM students;`,

        create_insert: `-- CREATE + INSERT + SELECT
-- This creates a small table, inserts sample data, then displays the records.

CREATE TABLE students (
  id INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  program TEXT NOT NULL,
  score INTEGER NOT NULL
);

INSERT INTO students (id, name, program, score) VALUES
  (1, 'Ana', 'BSIT', 88),
  (2, 'Ben', 'BSCS', 74),
  (3, 'Carlo', 'BSIT', 91),
  (4, 'Dina', 'BSCS', 83);

SELECT * FROM students;`,

        where_order: `-- WHERE + ORDER BY
-- Shows only passing students and sorts them by highest score.

SELECT
  name,
  program,
  score
FROM students
WHERE score >= 75
ORDER BY score DESC;`,

        join_group: `-- JOIN + GROUP BY
-- Creates two related tables and summarizes average scores per program.

CREATE TABLE programs (
  id INTEGER PRIMARY KEY,
  program_code TEXT NOT NULL,
  program_name TEXT NOT NULL
);

CREATE TABLE student_scores (
  id INTEGER PRIMARY KEY,
  student_name TEXT NOT NULL,
  program_id INTEGER NOT NULL,
  score INTEGER NOT NULL
);

INSERT INTO programs (id, program_code, program_name) VALUES
  (1, 'BSIT', 'Bachelor of Science in Information Technology'),
  (2, 'BSCS', 'Bachelor of Science in Computer Science');

INSERT INTO student_scores (id, student_name, program_id, score) VALUES
  (1, 'Ana', 1, 88),
  (2, 'Ben', 2, 74),
  (3, 'Carlo', 1, 91),
  (4, 'Dina', 2, 83);

SELECT
  p.program_code,
  COUNT(s.id) AS total_students,
  ROUND(AVG(s.score), 2) AS average_score
FROM programs p
JOIN student_scores s ON s.program_id = p.id
GROUP BY p.program_code
ORDER BY average_score DESC;`,

        safe_update: `-- Safe UPDATE
-- Always use WHERE when updating records.

UPDATE students
SET score = 90
WHERE id = 2;

SELECT * FROM students
WHERE id = 2;`
    };

    window.insertSqlSample = key => {
        if (!key || !SQL_SAMPLES[key]) return;
        const current = $query.value.trim();
        if (current && !confirm('Replace the current SQL editor content with this sample?')) {
            return;
        }
        $query.value = SQL_SAMPLES[key];
        $query.focus();
        updateLines();
    };

    // ── Snippet helpers ───────────────────────────────────────────────────

    function sqlIdentifier(name) {
        const identifier = String(name ?? '');
        return /^[A-Za-z_][A-Za-z0-9_]*$/.test(identifier)
            ? identifier
            : `"${identifier.replace(/"/g, '""')}"`;
    }

    window.insertSnippet = async k => {
        if (k === 'select') {
            try {
                // Always read the current sandbox schema when SELECT is clicked.
                // This keeps the generated query correct after CREATE/DROP operations.
                const tableState = await fetchCurrentTableState();

                if (tableState.tables.length === 0) {
                    $query.value = '';
                    updateLines();
                    showInfo('No tables found. Create a table first using the CREATE function.');
                    return;
                }

                // The tables endpoint is ordered by the database. With no explicit
                // selected-table concept in this UI, use the first real table returned.
                const tableName = String(tableState.tables[0]?.name ?? '');
                if (!tableName) {
                    showResult(false, 'Could not retrieve a valid table name from the SQL Sandbox.');
                    return;
                }

                $query.value = `SELECT * FROM ${sqlIdentifier(tableName)} LIMIT 10;`;
                $query.focus();
                updateLines();
                return;
            } catch (error) {
                console.error('[SQL Sandbox] SELECT table lookup failed:', error);
                showResult(false, error?.message || 'Could not retrieve the available tables. Please refresh the schema and try again.');
                return;
            }
        }

        if (k === 'create') {
            $query.value = 'CREATE TABLE students (\n  id INTEGER PRIMARY KEY,\n  name TEXT,\n  grade INTEGER\n);';
            $query.focus();
            updateLines();
        }
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

    function showInfo(message) {
        $idle.style.display = 'none';
        $tblScroll.classList.remove('show');
        $resultMsg.style.display = 'block';
        $resultMsg.style.color = 'var(--muted)';
        $resultMsg.textContent = message;
        $badge.style.display = 'none';
        $rowCount.textContent = '';
    }

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
        const head = document.getElementById('result-head');
        const body = document.getElementById('result-body');
        const headRow = document.createElement('tr');

        cols.forEach(column => {
            const cell = document.createElement('th');
            cell.textContent = String(column);
            headRow.appendChild(cell);
        });

        head.replaceChildren(headRow);
        body.replaceChildren(...rows.map(row => {
            const tableRow = document.createElement('tr');
            row.forEach(value => {
                const cell = document.createElement('td');
                cell.textContent = value === null ? 'NULL' : String(value);
                tableRow.appendChild(cell);
            });
            return tableRow;
        }));
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

        if (await redirectWhenSessionExpired(res)) {
            return {
                ok: false,
                status: res.status,
                data: { status: 'error', message: 'Your session expired. Redirecting to sign in.' },
            };
        }

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

    async function fetchCurrentTableState() {
        const { ok, data } = await apiFetch(TABLES_URL, { cache: 'no-store' });

        if (!ok || data.status !== 'success') {
            throw new Error(data.message || 'Could not retrieve the available tables from the SQL Sandbox.');
        }

        return {
            ...data,
            tables: Array.isArray(data.tables) ? data.tables : [],
        };
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

        try {
            const { data } = await apiFetch(EXECUTE_URL, {
                method: 'POST',
                body:   JSON.stringify({ query: q }),
            });

            let reviewOutput = '';
            if (data.status === 'success') {
                const cols = data.columns && data.columns.length > 0 ? data.columns : null;
                showResult(true, cols ?? data.message, data.rows ?? []);
                reviewOutput = cols
                    ? `Columns: ${cols.join(', ')}\nRows returned: ${(data.rows || []).length}\nPreview: ${JSON.stringify((data.rows || []).slice(0, 5))}`
                    : (data.message || 'SQL executed successfully.');
                // Refresh sidebar schema in case CREATE/DROP/ALTER ran
                loadTables();
            } else {
                showResult(false, data.message || 'Query failed.');
                reviewOutput = data.message || 'Query failed.';
            }

            // Review after execution so the chatbot sees the actual SQL result/error.
            ReviewBot.autoReview(q, reviewOutput);

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
            const data = await fetchCurrentTableState();

            const fill = document.getElementById('quota-fill');
            if (fill) {
                fill.style.width = `${(data.count / data.limit) * 100}%`;
                fill.classList.toggle('full', data.count >= data.limit);
            }
            document.getElementById('quota-text').textContent = `${data.count}/${data.limit}`;

            if (data.tables.length === 0) {
                $tableList.innerHTML = '<div class="empty-state">No tables yet</div>';
                return;
            }

            $tableList.replaceChildren(...data.tables.map(table => {
                const item = document.createElement('div');
                item.className = 'tbl-item';

                const header = document.createElement('div');
                header.className = 'tbl-header';
                header.addEventListener('click', () => item.classList.toggle('open'));
                header.innerHTML = '<svg class="tbl-chevron" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>';

                const name = document.createElement('span');
                name.className = 'tbl-name';
                name.textContent = String(table.name);
                header.appendChild(name);

                const actions = document.createElement('div');
                actions.className = 'tbl-actions';
                const drop = document.createElement('button');
                drop.type = 'button';
                drop.className = 'tbl-btn';
                drop.title = 'Delete Table';
                drop.setAttribute('aria-label', `Delete table ${table.name}`);
                drop.innerHTML = '<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>';
                drop.addEventListener('click', event => {
                    event.stopPropagation();
                    showDropToast(String(table.name));
                });
                actions.appendChild(drop);
                header.appendChild(actions);

                const columns = document.createElement('div');
                columns.className = 'col-list';
                for (const column of table.columns) {
                    const row = document.createElement('div');
                    row.className = 'col-row';
                    const columnName = document.createElement('span');
                    columnName.className = 'col-name';
                    columnName.textContent = String(column.name);
                    const type = document.createElement('span');
                    type.className = 'col-type';
                    type.textContent = String(column.type);
                    row.append(columnName, type);
                    columns.appendChild(row);
                }

                item.append(header, columns);
                return item;
            }));
        } catch (e) {
            console.warn('[SQL Sandbox] loadTables failed:', e);
        }
    };

    // ── Drop Table Toast ──────────────────────────────────────────────────
    let tableToDrop = null;

    window.showDropToast = name => {
        tableToDrop = name;
        document.getElementById('drop-desc').textContent = `Are you sure you want to delete '${name}'? This action cannot be undone.`;

        const modal = document.getElementById('drop-toast');
        if (typeof modal.showModal === 'function') {
            if (!modal.open) modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }

        requestAnimationFrame(() => document.getElementById('drop-cancel-btn')?.focus());
    };

    window.cancelDrop = () => {
        const modal = document.getElementById('drop-toast');
        if (typeof modal.close === 'function' && modal.open) {
            modal.close();
        } else {
            modal.removeAttribute('open');
        }

        tableToDrop = null;
        const confirmButton = document.getElementById('drop-confirm-btn');
        if (confirmButton) {
            confirmButton.disabled = false;
            confirmButton.textContent = 'Delete';
        }
    };

    window.confirmDrop = async () => {
        if (!tableToDrop) return;

        const confirmButton = document.getElementById('drop-confirm-btn');
        if (confirmButton) {
            confirmButton.disabled = true;
            confirmButton.textContent = 'Deleting…';
        }

        try {
            const { ok, data } = await apiFetch(`${TABLES_URL}/${encodeURIComponent(tableToDrop)}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            if (!ok) {
                showResult(false, data.message || 'Could not delete the selected table.');
            } else {
                await loadTables();
            }
        } catch (e) {
            console.error('[SQL Sandbox] confirmDrop failed:', e);
            showResult(false, 'Could not delete the selected table. Please try again.');
        } finally {
            cancelDrop();
        }
    };

    const dropModal = document.getElementById('drop-toast');

    dropModal.addEventListener('click', event => {
        if (event.target === dropModal) cancelDrop();
    });

    dropModal.addEventListener('cancel', event => {
        event.preventDefault();
        cancelDrop();
    });

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
