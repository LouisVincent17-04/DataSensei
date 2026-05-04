<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — IDE</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

  {{-- CodeMirror 6 via CDN --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/dracula.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/python/python.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/edit/closebrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/edit/matchbrackets.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/comment/comment.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/search/search.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/search/searchcursor.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldcode.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldgutter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/indent-fold.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/fold/foldgutter.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/selection/active-line.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/anyword-hint.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/hint/show-hint.min.css" />

  <style>
    :root {
      --bg: #0d1320; --surface: #111c2d; --surface2: #1a2638; --surface3: #0f1928; --border: #1e2f47;
      --border-hover: #2c4168; --accent: #3b82f6; --accent-hover: #2563eb; --accent3: #10b981;
      --warn: #ef4444; --warn2: #f59e0b; --text: #fafafa; --muted: #7f93b0; --dim: #3d5272;
      --radius: 6px; --tab-h: 38px; --sidebar-w: 52px; --explorer-w: 240px; --topbar-h: 42px; --terminal-h: 220px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); overflow: hidden; -webkit-font-smoothing: antialiased; }
    .app { display: flex; flex-direction: column; height: 100vh; }
    .topbar { height: var(--topbar-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 16px; gap: 12px; flex-shrink: 0; user-select: none; }
    .topbar-logo { font-weight: 700; font-size: 0.875rem; letter-spacing: -0.02em; display: flex; align-items: center; gap: 6px; }
    .topbar-logo span { color: var(--accent); }
    .topbar-sep { width: 1px; height: 18px; background: var(--border); margin: 0 4px; }
    .topbar-breadcrumb { display: flex; align-items: center; gap: 4px; font-size: 0.8rem; color: var(--muted); }
    .topbar-breadcrumb .seg { color: var(--text); }
    .topbar-breadcrumb .arrow { color: var(--dim); }
    .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 6px; }
    .tb-btn { display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: var(--radius); border: 1px solid var(--border); background: transparent; color: var(--muted); font-size: 0.75rem; font-family: inherit; cursor: pointer; transition: all 0.15s; font-weight: 500; }
    .tb-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border-hover); }
    .tb-btn.run { background: var(--accent3); color: #fff; border-color: var(--accent3); font-weight: 600; }
    .tb-btn.run:hover { background: #0ea472; border-color: #0ea472; }
    .tb-btn.run:disabled { opacity: 0.55; cursor: not-allowed; }
    .tb-btn.save { color: var(--accent); border-color: rgba(59,130,246,0.3); }
    .tb-btn.save:hover { background: rgba(59,130,246,0.08); }
    .workspace { display: flex; flex: 1; overflow: hidden; }
    .activity-bar { width: var(--sidebar-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; padding: 8px 0; gap: 2px; flex-shrink: 0; }
    .ab-btn { width: 36px; height: 36px; border-radius: var(--radius); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--dim); border: none; background: transparent; transition: all 0.15s; position: relative; }
    .ab-btn:hover, .ab-btn.active { color: var(--text); background: var(--surface2); }
    .ab-btn.active::before { content: ''; position: absolute; left: -1px; width: 3px; height: 24px; background: var(--accent); border-radius: 0 3px 3px 0; }
    /* EXPLORER (D&D TARGET) */
    .explorer { width: var(--explorer-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; overflow: hidden; transition: background 0.2s; }
    .explorer.drag-zone-active { background: rgba(59,130,246,0.05); border: 1px dashed var(--accent); }
    .explorer-header { padding: 10px 12px 8px; font-size: 0.65rem; font-weight: 700; color: var(--muted); letter-spacing: 0.1em; text-transform: uppercase; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .explorer-actions { display: flex; gap: 2px; }
    .exp-action { width: 22px; height: 22px; border-radius: 4px; border: none; background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.12s; }
    .exp-action:hover { color: var(--text); background: var(--surface2); }
    .explorer-tree { flex: 1; overflow-y: auto; padding: 4px 0; }
    .tree-node { user-select: none; }
    .tree-row { display: flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 4px; cursor: pointer; font-size: 0.8125rem; color: var(--muted); transition: background 0.1s, color 0.1s, opacity 0.2s; position: relative; border: 1px solid transparent; }
    .tree-row:hover { background: var(--surface2); color: var(--text); }
    .tree-row.active { background: rgba(59,130,246,0.1); color: var(--text); }
    .tree-row.active .file-icon { color: var(--accent); }
    .tree-row.dragging { opacity: 0.4; background: var(--surface2); }
    .tree-row.drag-over { background: rgba(59,130,246,0.15) !important; border: 1px dashed var(--accent); color: var(--text); }
    .tree-row .caret { width: 14px; height: 14px; flex-shrink: 0; color: var(--dim); transition: transform 0.15s; }
    .tree-row.open .caret { transform: rotate(90deg); }
    .tree-row .file-icon { width: 14px; height: 14px; flex-shrink: 0; color: var(--muted); }
    .tree-row .folder-icon { color: var(--warn2); }
    .tree-row .py-icon { color: #3b82f6; }
    .tree-row .node-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; pointer-events: none; }
    .tree-row .node-actions { display: none; gap: 2px; }
    .tree-row:hover .node-actions { display: flex; }
    .node-act-btn { width: 18px; height: 18px; border: none; background: transparent; color: var(--muted); cursor: pointer; border-radius: 3px; display: flex; align-items: center; justify-content: center; transition: all 0.1s; }
    .node-act-btn:hover { color: var(--text); background: var(--border); }
    .node-act-btn.del:hover { color: var(--warn); }
    .tree-children { display: none; }
    .tree-children.open { display: block; }
    .editor-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; background: var(--bg); }
    .tab-bar { height: var(--tab-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: flex-end; overflow-x: auto; flex-shrink: 0; }
    .tab { height: calc(var(--tab-h) - 1px); padding: 0 14px; display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: var(--muted); cursor: pointer; border-right: 1px solid var(--border); border-top: 2px solid transparent; white-space: nowrap; transition: all 0.12s; user-select: none; flex-shrink: 0; }
    .tab:hover { color: var(--text); background: var(--surface2); }
    .tab.active { color: var(--text); background: var(--bg); border-top-color: var(--accent); }
    .tab .tab-close { width: 16px; height: 16px; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: var(--dim); transition: all 0.1s; }
    .tab .tab-close:hover { color: var(--text); background: var(--border); }
    .tab.modified .tab-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }
    .editor-wrap { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .editor-pane { flex: 1; overflow: hidden; position: relative; display: flex; flex-direction: column; }
    .editor-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 16px; color: var(--dim); }
    .editor-empty h3 { font-size: 1rem; font-weight: 600; color: var(--muted); }
    .editor-empty p { font-size: 0.8125rem; }
    #cm-host { flex: 1; display: none; overflow: hidden; }
    .CodeMirror { height: 100% !important; font-family: 'JetBrains Mono', monospace !important; font-size: 14px !important; line-height: 1.6 !important; background: var(--bg) !important; }
    .CodeMirror-gutters { background: var(--bg) !important; border-right: 1px solid var(--border) !important; padding-right: 10px !important; padding-left: 5px !important; }
    .CodeMirror-linenumber { color: var(--dim) !important; min-width: 30px !important; padding-left: 0 !important; text-align: right !important; }
    .bottom-panel { height: var(--terminal-h); background: var(--surface3); border-top: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; transition: height 0.2s ease; }
    .bottom-panel.collapsed { height: 34px; }
    .panel-tabs { height: 34px; display: flex; align-items: center; border-bottom: 1px solid var(--border); padding: 0 8px; gap: 4px; flex-shrink: 0; }
    .panel-tab { padding: 3px 10px; font-size: 0.75rem; color: var(--muted); cursor: pointer; border-radius: 4px; border: 1px solid transparent; font-weight: 500; transition: all 0.12s; }
    .panel-tab.active { color: var(--text); background: var(--surface2); border-color: var(--border); }
    .panel-actions { margin-left: auto; display: flex; gap: 4px; }
    .panel-act-btn { width: 22px; height: 22px; border: none; background: transparent; color: var(--muted); cursor: pointer; border-radius: 3px; display: flex; align-items: center; justify-content: center; transition: all 0.1s; }
    .panel-act-btn:hover { color: var(--text); background: var(--surface2); }
    .terminal-body { flex: 1; overflow-y: auto; padding: 10px 14px; font-family: 'JetBrains Mono', monospace; font-size: 12.5px; line-height: 1.6; color: #c5d0e0; }
    .term-prompt { color: var(--accent3); }
    .term-output { color: #c5d0e0; white-space: pre-wrap; word-break: break-all; }
    .term-error  { color: #f87171; white-space: pre-wrap; word-break: break-all; }
    .term-info   { color: var(--muted); font-style: italic; }
    .term-success { color: var(--accent3); }
    .statusbar { height: 22px; background: var(--accent); display: flex; align-items: center; padding: 0 12px; gap: 12px; font-size: 0.7rem; color: rgba(255,255,255,0.85); flex-shrink: 0; user-select: none; }
    .statusbar-right { margin-left: auto; display: flex; gap: 12px; }
    .ctx-menu { position: fixed; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 4px; min-width: 160px; z-index: 9999; box-shadow: 0 8px 32px rgba(0,0,0,0.5); display: none; }
    .ctx-menu.open { display: block; }
    .ctx-item { display: flex; align-items: center; gap: 8px; padding: 6px 10px; border-radius: 4px; font-size: 0.8125rem; cursor: pointer; color: var(--muted); transition: all 0.1s; }
    .ctx-item:hover { background: var(--surface2); color: var(--text); }
    .ctx-item.danger:hover { color: var(--warn); }
    .ctx-sep { height: 1px; background: var(--border); margin: 3px 6px; }
    .modal-bg { position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 8888; display: none; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .modal-bg.open { display: flex; }
    .modal { background: var(--surface); border: 1px solid var(--border); border-radius: 12px; padding: 24px; width: 400px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .modal h3 { font-size: 1.1rem; font-weight: 600; color: var(--text); margin-bottom: 4px; }
    .modal p.subtitle { font-size: 0.85rem; color: var(--muted); margin-top: -12px; margin-bottom: 8px; }
    .modal-input-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
    .modal-input-group label { font-size: 0.8rem; font-weight: 600; color: var(--accent); }
    .modal input { width: 100%; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text); font-size: 0.9rem; font-family: inherit; padding: 10px 12px; outline: none; transition: border-color 0.15s; }
    .modal input:focus { border-color: var(--accent); }
    .modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 8px; }
    .modal-btn { padding: 8px 16px; border-radius: var(--radius); font-size: 0.85rem; font-weight: 600; font-family: inherit; cursor: pointer; border: 1px solid var(--border); background: transparent; color: var(--muted); transition: all 0.15s; }
    .modal-btn:hover { background: var(--surface2); color: var(--text); }
    .modal-btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .modal-btn.primary:hover { background: var(--accent-hover); }
    .resize-handle { height: 4px; background: transparent; cursor: ns-resize; flex-shrink: 0; transition: background 0.15s; }
    .resize-handle:hover { background: var(--accent); }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin 0.6s linear infinite; }
    @keyframes fadeOut { 0%{opacity:1} 80%{opacity:1} 100%{opacity:0} }
    .save-flash { animation: fadeOut 1.8s ease forwards; }

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
    /* pulsing dot — hidden once panel is open */
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
    /* Formatted review sections */
    .rb-section { font-size: 0.68rem; font-weight: 700; color: var(--accent); letter-spacing: 0.06em; text-transform: uppercase; margin: 8px 0 3px; padding-bottom: 3px; border-bottom: 1px solid var(--border); }
    .rb-section:first-child { margin-top: 0; }
    .rb-section.ok   { color: var(--accent3); }
    .rb-section.err  { color: var(--warn); }
    .rb-section.warn { color: var(--warn2); }
    .rb-bullet { display: flex; gap: 6px; font-size: 0.76rem; color: var(--muted); line-height: 1.5; padding: 1px 0; }
    .rb-bullet::before { content: '›'; color: var(--accent); flex-shrink: 0; font-weight: 700; }
    .rb-code { background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius); padding: 7px 9px; margin: 5px 0; font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #93c5fd; white-space: pre-wrap; word-break: break-word; overflow-x: auto; }
    .rb-line { font-size: 0.76rem; color: var(--muted); line-height: 1.55; padding: 1px 0; }
    /* Typing indicator */
    .rb-typing .rb-bubble { padding: 11px 13px; }
    .rb-dots { display: flex; gap: 4px; align-items: center; height: 13px; }
    .rb-dots span { width: 5px; height: 5px; border-radius: 50%; background: var(--muted); animation: rbDot 1.2s ease-in-out infinite; }
    .rb-dots span:nth-child(2) { animation-delay: 0.2s; }
    .rb-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes rbDot { 0%,80%,100%{transform:scale(0.7);opacity:0.4} 40%{transform:scale(1.1);opacity:1} }

    /* Input area */
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
  </style>
</head>
<body>

<div class="app">
  <div class="topbar">
    <div class="topbar-logo">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="2.5"><path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/><path d="M8 12h8M8 16h5"/></svg>
      Data<span>Sensei</span>
    </div>
    <div class="topbar-sep"></div>
    <div class="topbar-breadcrumb" id="breadcrumb">
      <span class="seg">{{ $workspace->name }}</span>
      <span class="arrow" id="breadcrumb-sep" style="display:none"> › </span>
      <span class="seg" id="breadcrumb-file"></span>
    </div>

    <div class="topbar-actions">
      <span id="save-indicator" style="font-size:0.7rem;color:var(--accent3);display:none"></span>
      <a href="#" id="returnToLessonBtn" class="tb-btn" style="display: none; color: var(--accent); border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.1);">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Return to Lesson
      </a>
      <button class="tb-btn save" id="btn-save" onclick="IDE.save()" title="Save (Ctrl+S)">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Save
      </button>
      <button class="tb-btn run" id="btn-run" onclick="IDE.run()" title="Run (Ctrl+Enter)">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        Run
      </button>
      <div class="topbar-sep"></div>
      <a href="{{ route('studentDashboard') }}" class="tb-btn" title="Back to Dashboard">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      </a>
    </div>
  </div>

  <div class="workspace">
    <div class="activity-bar">
      <button class="ab-btn active" id="ab-explorer" title="Explorer" onclick="IDE.togglePanel('explorer')">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
      </button>
      <button class="ab-btn" title="Search" onclick="IDE.focusSearch()">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
      <button class="ab-btn" title="Run & Debug">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><polygon points="5 3 19 12 5 21 5 3"/></svg>
      </button>
    </div>

    <div class="explorer" id="explorer-panel">
      <div class="explorer-header">
        <span>Explorer</span>
        <div class="explorer-actions">
          <button class="exp-action" title="New File" onclick="IDE.promptCreate('file', null)"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg></button>
          <button class="exp-action" title="New Folder" onclick="IDE.promptCreate('folder', null)"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/></svg></button>
          <button class="exp-action" title="Collapse All" onclick="IDE.collapseAll()"><svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></button>
        </div>
      </div>
      <div class="explorer-tree" id="tree-root"></div>
    </div>

    <div class="editor-area">
      <div class="tab-bar" id="tab-bar"></div>
      <div class="editor-wrap">
        <div class="editor-pane" id="editor-pane">
          <div class="editor-empty" id="editor-empty">
            <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--dim)" stroke-width="1"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
            <h3>No file open</h3>
            <p>Create or select a file from the explorer to start coding.</p>
          </div>
          <div id="cm-host"></div>
        </div>
        <div class="resize-handle" id="resize-handle"></div>
        <div class="bottom-panel" id="bottom-panel">
          <div class="panel-tabs"><div class="panel-tab active">Terminal</div>
            <div class="panel-actions">
              <button class="panel-act-btn" title="Clear" onclick="IDE.clearTerminal()"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg></button>
              <button class="panel-act-btn" title="Toggle" onclick="IDE.toggleTerminal()"><svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" id="term-toggle-icon"><polyline points="6 9 12 15 18 9"/></svg></button>
            </div>
          </div>
          <div class="terminal-body" id="terminal-body"><div class="term-info">DataSensei Python IDE — ready.</div></div>
        </div>
      </div>
    </div>
  </div>

  <div class="statusbar"><span id="status-lang">Python 3</span><span id="status-pos">Ln 1, Col 1</span><div class="statusbar-right"><span id="status-msg">Ready</span><span>UTF-8</span><span>LF</span></div></div>
</div>

<div class="ctx-menu" id="ctx-menu"></div>
<div class="modal-bg" id="modal-bg">
  <div class="modal"><h3 id="modal-title">New File</h3><input type="text" id="modal-input" placeholder="filename.py" autocomplete="off" spellcheck="false" /><div class="modal-actions"><button class="modal-btn" onclick="IDE.closeModal()">Cancel</button><button class="modal-btn primary" onclick="IDE.confirmModal()">Create</button></div></div>
</div>

<div class="modal-bg" id="program-input-modal">
  <div class="modal">
    <h3>Input Required</h3>
    <p class="subtitle" id="input-modal-subtitle">Your program is paused and waiting for input.</p>
    <div class="modal-input-group"><label id="input-modal-label">Prompt:</label><input type="text" id="dynamic-single-input" placeholder="Type your answer here..." autocomplete="off" spellcheck="false" /></div>
    <div class="modal-actions"><button class="modal-btn" onclick="IDE.cancelInput()">Cancel Run</button><button class="modal-btn primary" onclick="IDE.submitInput()">Submit</button></div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     🤖 AI REVIEW CHATBOT WIDGET HTML
═══════════════════════════════════════════════════ --}}
<button id="rb-toggle" onclick="ReviewBot.toggle()" title="AI Code Reviewer">
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
        <path stroke-linecap="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
      </svg>
    </div>
    <div style="flex:1;min-width:0">
      <div class="rb-header-title">AI Code Reviewer</div>
      <div class="rb-header-sub">Auto-reviews on every Run</div>
    </div>
    <div class="rb-status" id="rb-status">
      <div class="rb-status-dot"></div><span>Ready</span>
    </div>
  </div>

  <div id="rb-msgs"></div>

  <div class="rb-input-area">
    <div class="rb-input-label">Ask a follow-up</div>
    <textarea id="rb-input" placeholder="e.g. Why is that an issue? How do I fix it?" onkeydown="ReviewBot.handleKey(event)"></textarea>
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
     🤖 REVIEW BOT JS MODULE (global — before IDE IIFE)
═══════════════════════════════════════════════════ --}}
<script>
// ── ReviewBot ─────────────────────────────────────────────────────────────────
// Change REVIEW_URL to match your Laravel route that proxies code_reviewer.py
// Route should accept POST { code, language } and return JSON { ok, message }
const REVIEW_URL = '/api/code-review';

const ReviewBot = (() => {
  let _open     = false;
  let _busy     = false;
  let _lastCode = '';
  let _lastLang = 'python';
  // Accumulate review context for multi-turn follow-ups
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
      '<div class="rb-line">Hi! I\'ll automatically review your Python code every time you hit <strong style="color:var(--text)">Run</strong>.</div>' +
      '<div class="rb-line" style="margin-top:5px;color:var(--dim)">You can also ask me follow-up questions below.</div>'
    );
  }

  /* ── Called by IDE.run() automatically ── */
  function autoReview(code, lang, filename) {
    if (!code || !code.trim()) return;
    _lastCode = code;
    _lastLang = lang || 'python';
    _history.length = 0; // reset context per new run

    _open_panel();
    $toggle().classList.add('rb-has-review');

    // Show user bubble — truncated code preview
    const preview = code.length > 220 ? code.slice(0, 217) + '…' : code;
    _addUser(preview);

    // Bot opening line depends on language
    const langLabel = lang === 'mysql' ? 'MySQL query' : 'Python code';
    _addBot(
      `<div class="rb-line">I see you just ran your <strong style="color:var(--text)">${escH(filename || langLabel)}</strong> — let me review it now...</div>`
    );

    _sendReview(code, lang);
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
        '<div class="rb-line" style="color:var(--warn2)">⚠️ I\'m a <strong style="color:var(--text)">code reviewer</strong>, not a code generator. ' +
        'I can\'t write or produce code for you — but I can help you understand issues in your existing code, explain concepts, or point you in the right direction.</div>'
      );
      return;
    }

    $input().value = '';
    _addUser(question);

    const messages = _history.length
      ? [..._history, { role: 'user', content: question }]
      : [{ role: 'user', content: `Regarding this code:\n\`\`\`\n${_lastCode}\n\`\`\`\n\n${question}` }];

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

  /* ── Core: send code for review ── */
  async function _sendReview(code, lang) {
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'review');
      form.append('code',     code);
      form.append('language', lang || 'python');

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
        // Store in history for follow-ups
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
    const SECTION = /^(Status|Issues?|Fix|Suggestion|Suggestions|Warning|Warnings|Notes?|Summary|Result)s?:/i;
    const segs = raw.split(/(```[a-z]*\n?)/);
    let html = '', inCode = false, codeAcc = '';
    for (const seg of segs) {
      if (/^```/.test(seg)) { if (inCode) { html += `<div class="rb-code">${escH(codeAcc.trimEnd())}</div>`; codeAcc = ''; } inCode = !inCode; continue; }
      if (inCode) { codeAcc += seg; continue; }
      for (const line of seg.split('\n')) {
        const t = line.trim(); if (!t) continue;
        if (SECTION.test(t)) {
          const cls = /correct|clean|good|pass/i.test(t) ? 'rb-section ok' : /error|issue|fail|wrong/i.test(t) ? 'rb-section err' : 'rb-section';
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
const WORKSPACE_ID = {{ $workspace->id }};
const TREE_DATA = @json($tree);
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

const IDE = (() => {
  let openTabs = []; let activeTab = null; let cm = null; let treeData = [...TREE_DATA]; let cmChanging = false;
  let draggedNodeId = null;

  function init() { 
      renderTree(); initCM(); setupResize(); setupKeyboard(); handleLessonImports(); 
      setupGlobalDropZone();
  }

  // 🚀 AUTO-NAME GENERATOR (e.g. main.py -> main (1).py) 🚀
  function generateSuggestedName(filename) {
      const parts = filename.split('.');
      const ext = parts.length > 1 ? '.' + parts.pop() : '';
      let base = parts.join('.');
      const match = base.match(/ \((\d+)\)$/);
      if (match) {
          const num = parseInt(match[1]) + 1;
          base = base.replace(/ \(\d+\)$/, ` (${num})`);
      } else {
          base += ' (1)';
      }
      return base + ext;
  }

  // 🚀 GLOBAL EXTERNAL DESKTOP DROP ZONE 🚀
  function setupGlobalDropZone() {
      const explorerPanel = document.getElementById('explorer-panel');
      explorerPanel.addEventListener('dragover', (e) => { e.preventDefault(); explorerPanel.classList.add('drag-zone-active'); });
      explorerPanel.addEventListener('dragleave', (e) => { e.preventDefault(); if (!explorerPanel.contains(e.relatedTarget)) { explorerPanel.classList.remove('drag-zone-active'); } });
      explorerPanel.addEventListener('drop', (e) => { e.preventDefault(); explorerPanel.classList.remove('drag-zone-active'); handleDrop(e, null); });
  }

  // 🚀 CORE DRAG AND DROP HANDLER 🚀
  async function handleDrop(e, targetParentId) {
      if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
          setStatus('Uploading files...');
          for (let file of e.dataTransfer.files) { await uploadExternalFile(file, targetParentId); }
          await refreshTree(); setStatus('Ready'); return;
      }
      if (draggedNodeId && draggedNodeId !== targetParentId) {
          if (isDescendant(draggedNodeId, targetParentId)) { setStatus('Cannot move a folder into itself.'); return; }
          moveInternalNode(draggedNodeId, targetParentId);
      }
  }

  function uploadExternalFile(file, parentId, overrideName = null) {
      const fileNameToUse = overrideName || file.name;
      return new Promise((resolve) => {
          const reader = new FileReader();
          reader.onload = async (e) => {
              try {
                  await api('/ide/nodes', 'POST', { workspace_id: WORKSPACE_ID, parent_id: parentId, type: 'file', name: fileNameToUse, content: e.target.result, language: 'python' });
                  resolve();
              } catch (err) {
                  if (err.collision) {
                      const suggested = generateSuggestedName(fileNameToUse);
                      const newName = prompt(`A file named "${fileNameToUse}" already exists here.\nEnter a new name or cancel:`, suggested);
                      if (newName && newName.trim() !== '') { await uploadExternalFile(file, parentId, newName.trim()); } else { setStatus('Upload canceled.'); }
                  } else { termPrint('error', `Failed to upload ${file.name}: ${err.message}`); }
                  resolve();
              }
          };
          reader.onerror = () => resolve();
          reader.readAsText(file);
      });
  }

  async function moveInternalNode(nodeId, newParentId, overrideName = null) {
      setStatus('Moving...');
      try {
          const payload = { parent_id: newParentId };
          if (overrideName) payload.new_name = overrideName;
          await api(`/ide/nodes/${nodeId}/move`, 'PATCH', payload);
          await refreshTree(); setStatus('Ready');
      } catch (err) {
          if (err.collision) {
              const node = findNode(treeData, nodeId);
              const suggested = generateSuggestedName(node.name);
              const newName = prompt(`A file named "${node.name}" already exists in that folder.\nEnter a new name or cancel:`, suggested);
              if (newName && newName.trim() !== '') { await moveInternalNode(nodeId, newParentId, newName.trim()); } else { setStatus('Move canceled.'); }
          } else { termPrint('error', 'Move failed: ' + err.message); setStatus('Error'); }
      }
  }

  function isDescendant(nodeId, targetId) {
      if (!targetId) return false;
      if (nodeId === targetId) return true;
      let currentTarget = findNode(treeData, targetId);
      while (currentTarget && currentTarget.parent_id) {
          if (currentTarget.parent_id === nodeId) return true;
          currentTarget = findNode(treeData, currentTarget.parent_id);
      }
      return false;
  }

  // 🚀 ANTI-SPAM LESSON IMPORT LOGIC 🚀
  async function handleLessonImports() {
    const returnUrl = sessionStorage.getItem('datasensei_return_url');
    const returnBtn = document.getElementById('returnToLessonBtn');
    if (returnUrl && returnBtn) {
        returnBtn.style.display = 'inline-flex'; returnBtn.href = returnUrl;
        returnBtn.addEventListener('click', () => sessionStorage.removeItem('datasensei_return_url'));
    }

    const pendingCode = sessionStorage.getItem('datasensei_pending_code');
    if (pendingCode) {
        const findPracticeFile = (nodes) => {
            for (let n of nodes) {
                if (n.type === 'file' && n.name === 'practice.py') return n;
                if (n.children) { let found = findPracticeFile(n.children); if (found) return found; }
            }
            return null;
        };

        let practiceFile = findPracticeFile(treeData);

        try {
            setStatus('Loading practice file...');
            let nodeToOpen;
            if (practiceFile) {
                await api(`/ide/nodes/${practiceFile.id}/save`, 'PATCH', { content: pendingCode });
                nodeToOpen = practiceFile; nodeToOpen.content = pendingCode;
            } else {
                const res = await api('/ide/nodes', 'POST', { workspace_id: WORKSPACE_ID, parent_id: null, type: 'file', name: 'practice.py', content: pendingCode, language: 'python' });
                nodeToOpen = res.node;
            }
            await refreshTree();
            openFile(nodeToOpen.id, 'practice.py', pendingCode);
            cm.setValue(pendingCode);
            const tab = openTabs.find(t => t.id === nodeToOpen.id); if (tab) tab.modified = true;
            renderTabBar();
            sessionStorage.removeItem('datasensei_pending_code');
            setStatus('Ready');
        } catch (e) { console.error("Practice Load Failed", e); setStatus('Error'); }
    }
  }

  function initCM() {
    cm = CodeMirror(document.getElementById('cm-host'), {
      mode: 'python', theme: 'dracula', lineNumbers: true, lineWrapping: false, tabSize: 4, indentUnit: 4, smartIndent: true,
      autoCloseBrackets: true, matchBrackets: true, styleActiveLine: true, foldGutter: true, gutters: ['CodeMirror-linenumbers','CodeMirror-foldgutter'],
      extraKeys: { 'Ctrl-/':'toggleComment', 'Cmd-/':'toggleComment', 'Ctrl-Space':'autocomplete', 'Tab':(cm)=>cm.execCommand('indentMore'), 'Shift-Tab':(cm)=>cm.execCommand('indentLess'), 'Ctrl-S':()=>save(), 'Cmd-S':()=>save(), 'Ctrl-Enter':()=>run(), 'Cmd-Enter':()=>run() },
      hintOptions: { hint: CodeMirror.hint.anyword },
    });
    cm.on('change', () => { if (cmChanging) return; if (activeTab !== null) { const tab = openTabs.find(t => t.id === activeTab); if (tab && !tab.modified) { tab.modified = true; renderTabBar(); } } });
    cm.on('cursorActivity', () => { const cur = cm.getCursor(); document.getElementById('status-pos').textContent = `Ln ${cur.line + 1}, Col ${cur.ch + 1}`; });
  }

  function renderTree() { const root = document.getElementById('tree-root'); root.innerHTML = ''; treeData.forEach(node => root.appendChild(buildTreeNode(node, 0))); attachCtxMenuListeners(); }

  function buildTreeNode(node, depth) {
    const wrap = document.createElement('div'); wrap.className = 'tree-node'; wrap.dataset.id = node.id; wrap.dataset.type = node.type;
    const indent = depth * 14; const row = document.createElement('div'); row.className = 'tree-row'; row.style.paddingLeft = `${8 + indent}px`;
    row.setAttribute('draggable', 'true');
    row.addEventListener('dragstart', (e) => { draggedNodeId = node.id; e.dataTransfer.effectAllowed = 'move'; e.dataTransfer.setData('text/plain', node.id); row.classList.add('dragging'); });
    row.addEventListener('dragend', () => { draggedNodeId = null; row.classList.remove('dragging'); document.querySelectorAll('.drag-over').forEach(el => el.classList.remove('drag-over')); });

    if (node.type === 'folder') {
      row.innerHTML = `<svg class="caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg><svg class="file-icon folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg><span class="node-name">${escHtml(node.name)}</span><div class="node-actions"><button class="node-act-btn" title="New File" data-action="newfile" data-id="${node.id}"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="12" y1="12" x2="12" y2="18"/><line x1="9" y1="15" x2="15" y2="15"/></svg></button><button class="node-act-btn" title="Rename" data-action="rename" data-id="${node.id}"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button><button class="node-act-btn del" title="Delete" data-action="delete" data-id="${node.id}" data-name="${escHtml(node.name)}"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg></button></div>`;
      const children = document.createElement('div'); children.className = 'tree-children'; (node.children || []).forEach(child => children.appendChild(buildTreeNode(child, depth + 1)));
      row.addEventListener('click', (e) => { if (e.target.closest('[data-action]')) return; row.classList.toggle('open'); children.classList.toggle('open'); });
      row.addEventListener('dragover', (e) => { e.preventDefault(); e.stopPropagation(); row.classList.add('drag-over'); });
      row.addEventListener('dragleave', (e) => { e.preventDefault(); e.stopPropagation(); row.classList.remove('drag-over'); });
      row.addEventListener('drop', (e) => { e.preventDefault(); e.stopPropagation(); row.classList.remove('drag-over'); handleDrop(e, node.id); });
      wrap.appendChild(row); wrap.appendChild(children);
    } else {
      row.innerHTML = `<svg class="caret" style="opacity:0" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg><svg class="file-icon py-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span class="node-name">${escHtml(node.name)}</span><div class="node-actions"><button class="node-act-btn" title="Rename" data-action="rename" data-id="${node.id}"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button><button class="node-act-btn del" title="Delete" data-action="delete" data-id="${node.id}" data-name="${escHtml(node.name)}"><svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg></button></div>`;
      row.addEventListener('click', (e) => { if (e.target.closest('[data-action]')) return; openFile(node.id, node.name, node.content ?? ''); });
      wrap.appendChild(row);
    }
    row.querySelectorAll('[data-action]').forEach(btn => btn.addEventListener('click', (e) => { e.stopPropagation(); const action = btn.dataset.action; const id = parseInt(btn.dataset.id); const name = btn.dataset.name; if (action === 'newfile') promptCreate('file', id); else if (action === 'newfolder') promptCreate('folder', id); else if (action === 'rename') promptRename(id, btn.closest('.tree-row').querySelector('.node-name').textContent); else if (action === 'delete') confirmDelete(id, name); }));
    return wrap;
  }

  function attachCtxMenuListeners() { document.getElementById('tree-root').addEventListener('contextmenu', (e) => { const row = e.target.closest('.tree-row'); if (!row) return; e.preventDefault(); const nodeEl = row.closest('.tree-node'); const id = parseInt(nodeEl.dataset.id); const type = nodeEl.dataset.type; const name = row.querySelector('.node-name').textContent; showCtxMenu(e.clientX, e.clientY, id, type, name); }); }
  function openFile(id, name, content) { document.querySelectorAll('.tree-row').forEach(r => r.classList.remove('active')); const nodeEl = document.querySelector(`.tree-node[data-id="${id}"]`); if (nodeEl) nodeEl.querySelector('.tree-row').classList.add('active'); document.getElementById('breadcrumb-file').textContent = name; document.getElementById('breadcrumb-sep').style.display = ''; if (!openTabs.find(t => t.id === id)) { openTabs.push({ id, name, content, modified: false }); } activeTab = id; renderTabBar(); loadTabContent(id); updateStatusLang(name); }
  function loadTabContent(id) { const tab = openTabs.find(t => t.id === id); if (!tab) return; document.getElementById('editor-empty').style.display = 'none'; const cmHost = document.getElementById('cm-host'); cmHost.style.display = 'block'; setTimeout(() => { cm.refresh(); }, 10); cm.focus(); cmChanging = true; cm.setValue(tab.content ?? ''); cmChanging = false; cm.clearHistory(); cm.scrollTo(0, 0); }
  function syncActiveTabContent() { if (activeTab === null) return; const tab = openTabs.find(t => t.id === activeTab); if (tab) tab.content = cm.getValue(); }
  function renderTabBar() { const bar = document.getElementById('tab-bar'); bar.innerHTML = ''; openTabs.forEach(tab => { const el = document.createElement('div'); el.className = 'tab' + (tab.id === activeTab ? ' active' : '') + (tab.modified ? ' modified' : ''); el.innerHTML = `<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>${escHtml(tab.name)}${tab.modified ? '<span class="tab-dot"></span>' : ''}<span class="tab-close" data-close="${tab.id}"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>`; el.addEventListener('click', (e) => { if (e.target.closest('[data-close]')) closeTab(tab.id); else switchTab(tab.id); }); bar.appendChild(el); }); }
  function switchTab(id) { syncActiveTabContent(); activeTab = id; renderTabBar(); loadTabContent(id); const tab = openTabs.find(t => t.id === id); if (tab) { document.getElementById('breadcrumb-file').textContent = tab.name; updateStatusLang(tab.name); } }
  function closeTab(id) { syncActiveTabContent(); const idx = openTabs.findIndex(t => t.id === id); openTabs.splice(idx, 1); if (activeTab === id) { if (openTabs.length === 0) { activeTab = null; document.getElementById('editor-empty').style.display = 'flex'; document.getElementById('cm-host').style.display = 'none'; document.getElementById('breadcrumb-file').textContent = ''; document.getElementById('breadcrumb-sep').style.display = 'none'; } else { const next = openTabs[Math.min(idx, openTabs.length - 1)]; switchTab(next.id); return; } } renderTabBar(); }
  async function save() { if (activeTab === null) return; syncActiveTabContent(); const tab = openTabs.find(t => t.id === activeTab); if (!tab) return; setStatus('Saving…'); try { const res = await api(`/ide/nodes/${tab.id}/save`, 'PATCH', { content: tab.content }); tab.modified = false; renderTabBar(); flashSave('Saved'); setStatus('Saved'); } catch(e) { setStatus('Save failed'); } }
  function flashSave(msg) { const el = document.getElementById('save-indicator'); el.textContent = msg; el.style.display = 'inline'; el.className = 'save-flash'; setTimeout(() => { el.style.display = 'none'; el.className = ''; }, 2000); }

  let currentInputResolve = null;
  function askUserForInput(promptText) { return new Promise((resolve) => { const modal = document.getElementById('program-input-modal'); const label = document.getElementById('input-modal-label'); const input = document.getElementById('dynamic-single-input'); label.textContent = promptText; input.value = ""; modal.classList.add('open'); setTimeout(() => input.focus(), 50); currentInputResolve = resolve; }); }
  function submitInput() { const val = document.getElementById('dynamic-single-input').value; document.getElementById('program-input-modal').classList.remove('open'); if (currentInputResolve) { currentInputResolve(val); currentInputResolve = null; } }
  function cancelInput() { document.getElementById('program-input-modal').classList.remove('open'); if (currentInputResolve) { currentInputResolve(null); currentInputResolve = null; } }
  document.getElementById('dynamic-single-input').addEventListener('keydown', (e) => { if (e.key === 'Enter') submitInput(); });

  async function run() {
    if (activeTab === null) return; await save(); const tab = openTabs.find(t => t.id === activeTab); if (!tab) return;
    const code = tab.content || ""; const inputRegex = /input\((['"]?)(.*?)\1\)/g; let match; let inputsList = [];
    while ((match = inputRegex.exec(code)) !== null) {
        let userResponse = await askUserForInput(match[2] || "Input value:");
        if (userResponse === null) { setStatus('Run canceled'); return; }
        inputsList.push(userResponse);
    }
    const runBtn = document.getElementById('btn-run'); runBtn.disabled = true; runBtn.innerHTML = '<div class="spinner"></div> Running…';
    const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal();
    termPrint('prompt', `$ python3 ${tab.name}`); setStatus('Running…');

    // 🤖 AUTO-TRIGGER CHATBOT REVIEW — fires immediately when Run is clicked
    ReviewBot.autoReview(tab.content || cm.getValue(), 'python', tab.name);

    try {
      const res = await api(`/ide/nodes/${tab.id}/run`, 'POST', { stdin: inputsList.join('\n'), content: tab.content });
      if (res.output) termPrint('output', res.output); if (res.error) termPrint('error', res.error);
      if (res.plots && res.plots.length > 0) res.plots.forEach(b64 => termPrintImage(b64));
      if (!res.output && !res.error && (!res.plots || res.plots.length === 0)) termPrint('info', '(No output)');
      termPrint('info', `Exit: ${res.exit_code}  Time: ${res.execution_time_ms}ms`);
      setStatus(res.exit_code === 0 ? 'Finished' : `Error (exit ${res.exit_code})`);
    } catch(e) { termPrint('error', 'Request failed: ' + e.message); setStatus('Run failed');
    } finally { runBtn.disabled = false; runBtn.innerHTML = `<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg> Run`; }
  }

  let _modalCallback = null; let _modalParentId = null; let _modalType = null; let _renameId = null;
  function promptCreate(type, parentId) { _modalType = type; _modalParentId = parentId; _modalCallback = 'create'; document.getElementById('modal-title').textContent = type === 'file' ? 'New File' : 'New Folder'; document.getElementById('modal-input').value = type === 'file' ? 'untitled.py' : 'new-folder'; document.getElementById('modal-bg').classList.add('open'); setTimeout(() => { const inp = document.getElementById('modal-input'); inp.focus(); inp.select(); }, 50); }
  function promptRename(id, currentName) { _renameId = id; _modalCallback = 'rename'; document.getElementById('modal-title').textContent = 'Rename'; document.getElementById('modal-input').value = currentName; document.getElementById('modal-bg').classList.add('open'); setTimeout(() => { const inp = document.getElementById('modal-input'); inp.focus(); inp.select(); }, 50); }
  function closeModal() { document.getElementById('modal-bg').classList.remove('open'); _modalCallback = null; }
  async function confirmModal() { const val = document.getElementById('modal-input').value.trim(); if (!val) return; const callback = _modalCallback; const type = _modalType; const parentId = _modalParentId; const renameId = _renameId; closeModal(); if (callback === 'create') await createNode(type, parentId, val); else if (callback === 'rename') await renameNode(renameId, val); }
  async function createNode(type, parentId, name) { setStatus('Creating…'); try { const res = await api('/ide/nodes', 'POST', { workspace_id: WORKSPACE_ID, parent_id: parentId, type, name, content: type === 'file' ? '' : null, language: 'python' }); await refreshTree(); setStatus('Created'); if (type === 'file') openFile(res.node.id, res.node.name, res.node.content ?? ''); } catch(e) { termPrint('error', e.message); setStatus('Error'); } }
  async function renameNode(id, name) { setStatus('Renaming…'); try { await api(`/ide/nodes/${id}/rename`, 'PATCH', { name }); const tab = openTabs.find(t => t.id === id); if (tab) { tab.name = name; renderTabBar(); document.getElementById('breadcrumb-file').textContent = name; } await refreshTree(); setStatus('Renamed'); } catch(e) { termPrint('error', e.message); setStatus('Error'); } }
  function confirmDelete(id, name) { if (!confirm(`Delete "${name}"? This cannot be undone.`)) return; deleteNode(id); }
  async function deleteNode(id) { setStatus('Deleting…'); try { await api(`/ide/nodes/${id}`, 'DELETE'); openTabs = openTabs.filter(t => t.id !== id); if (activeTab === id) { activeTab = openTabs.length ? openTabs[openTabs.length - 1].id : null; if (activeTab) loadTabContent(activeTab); else { document.getElementById('editor-empty').style.display = 'flex'; document.getElementById('cm-host').style.display = 'none'; } } renderTabBar(); await refreshTree(); setStatus('Deleted'); } catch(e) { setStatus('Error'); } }
  async function refreshTree() { const res = await api('/ide/tree', 'GET'); treeData = res.tree; renderTree(); }
  function showCtxMenu(x, y, id, type, name) { const menu = document.getElementById('ctx-menu'); menu.innerHTML = ''; const items = type === 'folder' ? [{ label: 'New File', action: () => promptCreate('file', id) }, { label: 'New Folder', action: () => promptCreate('folder', id) }, { sep: true }, { label: 'Rename', action: () => promptRename(id, name) }, { label: 'Delete', action: () => confirmDelete(id, name), danger: true }] : [{ label: 'Open', action: () => { const node = findNode(treeData, id); if (node) openFile(node.id, node.name, node.content ?? ''); } }, { sep: true }, { label: 'Rename', action: () => promptRename(id, name) }, { label: 'Delete', action: () => confirmDelete(id, name), danger: true }]; items.forEach(item => { if (item.sep) { const sep = document.createElement('div'); sep.className = 'ctx-sep'; menu.appendChild(sep); } else { const el = document.createElement('div'); el.className = 'ctx-item' + (item.danger ? ' danger' : ''); el.textContent = item.label; el.addEventListener('click', () => { closeCtxMenu(); item.action(); }); menu.appendChild(el); } }); menu.style.left = x + 'px'; menu.style.top = y + 'px'; menu.classList.add('open'); setTimeout(() => document.addEventListener('click', closeCtxMenu, { once: true }), 10); }
  function closeCtxMenu() { document.getElementById('ctx-menu').classList.remove('open'); }
  function termPrint(type, text) { const body = document.getElementById('terminal-body'); const div = document.createElement('div'); div.className = `term-${type}`; if (type === 'prompt') div.innerHTML = `<span class="term-prompt">›</span> ${escHtml(text)}`; else div.textContent = text; body.appendChild(div); body.scrollTop = body.scrollHeight; const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal(); }
  function termPrintImage(base64) { const body = document.getElementById('terminal-body'); const wrapper = document.createElement('div'); wrapper.style.cssText = 'padding: 8px 0;'; const img = document.createElement('img'); img.src = 'data:image/png;base64,' + base64; img.style.cssText = 'max-width:100%; border-radius:6px; border:1px solid var(--border); display:block;'; wrapper.appendChild(img); body.appendChild(wrapper); body.scrollTop = body.scrollHeight; const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal(); }
  function clearTerminal() { document.getElementById('terminal-body').innerHTML = '<div class="term-info">Terminal cleared.</div>'; }
  function toggleTerminal() { const panel = document.getElementById('bottom-panel'); panel.classList.toggle('collapsed'); const icon = document.getElementById('term-toggle-icon'); icon.innerHTML = panel.classList.contains('collapsed') ? '<polyline points="6 15 12 9 18 15"/>' : '<polyline points="6 9 12 15 18 9"/>'; setTimeout(() => { if(cm) cm.refresh(); }, 250); }
  function setupResize() { const handle = document.getElementById('resize-handle'); const panel = document.getElementById('bottom-panel'); let dragging = false; let startY, startH; handle.addEventListener('mousedown', (e) => { dragging = true; startY = e.clientY; startH = panel.offsetHeight; document.body.style.cursor = 'ns-resize'; document.body.style.userSelect = 'none'; }); document.addEventListener('mousemove', (e) => { if (!dragging) return; const diff = startY - e.clientY; const newH = Math.max(34, Math.min(startH + diff, 500)); panel.style.height = newH + 'px'; }); document.addEventListener('mouseup', () => { dragging = false; document.body.style.cursor = ''; document.body.style.userSelect = ''; }); }
  function togglePanel(name) { if (name === 'explorer') { const el = document.getElementById('explorer-panel'); el.style.display = el.style.display === 'none' ? '' : 'none'; } }
  function collapseAll() { document.querySelectorAll('.tree-children').forEach(c => c.classList.remove('open')); document.querySelectorAll('.tree-row').forEach(r => r.classList.remove('open')); }
  function setupKeyboard() { document.addEventListener('keydown', (e) => { if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); save(); } if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); run(); } if (e.key === 'Escape') { closeModal(); closeCtxMenu(); cancelInput(); } }); document.getElementById('modal-input').addEventListener('keydown', (e) => { if (e.key === 'Enter') confirmModal(); }); }
  function setStatus(msg) { document.getElementById('status-msg').textContent = msg; }
  function updateStatusLang(name) { const ext = name.split('.').pop().toLowerCase(); const map = { py: 'Python 3', js: 'JavaScript', ts: 'TypeScript', md: 'Markdown', txt: 'Plain Text' }; document.getElementById('status-lang').textContent = map[ext] || 'Python 3'; }
  function focusSearch() { if (cm) cm.execCommand('find'); }

  // 🚀 UPDATED API HELPER TO CATCH COLLISIONS 🚀
  async function api(url, method, body) { 
      const opts = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } }; 
      if (method !== 'GET' && body !== undefined) opts.body = JSON.stringify(body); 
      const res = await fetch(url, opts); 
      const json = await res.json(); 
      if (!res.ok) {
          const err = new Error(json.error || json.message || 'Request failed');
          err.collision = json.collision;
          throw err;
      }
      return json; 
  }

  function findNode(nodes, id) { for (const n of nodes) { if (n.id === id) return n; if (n.children) { const found = findNode(n.children, id); if (found) return found; } } return null; }
  function escHtml(str) { return (str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  return { init, save, run, submitInput, cancelInput, promptCreate, promptRename, closeModal, confirmModal, clearTerminal, toggleTerminal, togglePanel, collapseAll, focusSearch };
})();

window.addEventListener('DOMContentLoaded', IDE.init);
</script>
</body>
</html>