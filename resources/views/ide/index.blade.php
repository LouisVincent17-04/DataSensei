<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>IDE — DataSensei</title>
{{-- CodeMirror 5 is bundled locally so the IDE works offline and loads in two requests. --}}
  <link rel="stylesheet" href="{{ asset('vendor/codemirror/codemirror.bundle.css') }}" />
  <script defer src="{{ asset('vendor/codemirror/codemirror.bundle.js') }}"></script>

  <style>
    /* Python IDE. Colours, type and radius come from partials.design-system;
       only the editor's syntax colours (CodeMirror dracula tokens) are its own. */
    :root {
      /* Page-specific names still read by the scripts below. */
      --accent3: var(--ds-success-text);
      --warn: var(--ds-danger-text);
      --warn2: var(--ds-warning-text);
      --tab-h: 40px; --activity-w: 48px; --explorer-w: 248px; --topbar-h: 52px; --terminal-h: 220px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: var(--ds-font-sans); background: var(--bg); color: var(--text); overflow: hidden; }
    .app { display: flex; flex-direction: column; height: 100vh; height: 100dvh; }

    /* ── top bar ─────────────────────────────────────────────── */
    .topbar { min-height: var(--topbar-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 16px; gap: 12px 16px; flex-shrink: 0; user-select: none; }
    .topbar-logo { display: flex; align-items: center; flex-shrink: 0; }
    .topbar-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: .8125rem; color: var(--muted); min-width: 0; overflow: hidden; white-space: nowrap; }
    .topbar-breadcrumb .seg { color: var(--ds-text-secondary); overflow: hidden; text-overflow: ellipsis; }
    .topbar-breadcrumb #breadcrumb-file { color: var(--text); font-family: var(--ds-font-mono); }
    .topbar-breadcrumb .arrow { color: var(--dim); }
    .topbar-actions { margin-left: auto; display: flex; align-items: center; gap: 8px; }
    .topbar-nav-divider { width: 1px; height: 20px; background: var(--border); margin: 0 4px; flex-shrink: 0; }

    .tb-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 32px; padding: 0 12px; border-radius: var(--radius-sm); border: 1px solid var(--ds-border-strong); background: var(--surface2); color: var(--text); font: 500 .8125rem/1.2 var(--ds-font-sans); text-decoration: none; white-space: nowrap; cursor: pointer; transition: background .12s ease, border-color .12s ease, color .12s ease; }
    .tb-btn:hover { background: var(--ds-surface-hover); }
    .tb-btn:disabled { opacity: .55; cursor: not-allowed; }
    .tb-btn.return-btn { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); color: var(--ds-accent-text); }
    .tb-btn.return-btn:hover { color: var(--text); }
    .tb-btn.dashboard-btn { border-color: transparent; background: transparent; color: var(--muted); }
    .tb-btn.dashboard-btn:hover { background: var(--surface2); color: var(--text); }
    .tb-btn.run { border-color: var(--accent); background: var(--accent); color: #fff; }
    .tb-btn.run:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
    .tb-btn.run.is-stopping { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }
    .tb-btn.run.is-stopping:hover { border-color: var(--ds-danger); }
    /* The save confirmation lives inside the Save button, so it never adds a row. */
    .tb-btn.save { min-width: 76px; }
    .tb-btn.run { min-width: 68px; }
    .tb-btn.save.is-saved { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .tb-select { height: 32px; min-width: 188px; padding: 0 8px 0 10px; border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--ds-text-secondary); font: 500 .8125rem/1.2 var(--ds-font-sans); cursor: pointer; transition: border-color .12s ease; }
    .tb-select:hover { border-color: var(--ds-border-strong); color: var(--text); }
    .tb-select:focus { outline: none; border-color: var(--accent); box-shadow: var(--ds-focus-ring); color: var(--text); }
    .tb-select option { background: var(--surface); color: var(--text); }

    /* ── workspace: activity bar, explorer, editor ───────────── */
    .workspace { display: flex; flex: 1; min-height: 0; overflow: hidden; }
    .activity-bar { width: var(--activity-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; align-items: center; padding: 8px 0; gap: 4px; flex-shrink: 0; }
    .ab-btn { width: 36px; height: 36px; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); border: 0; background: transparent; transition: background .12s ease, color .12s ease; position: relative; }
    .ab-btn:hover, .ab-btn.active { color: var(--text); background: var(--surface2); }
    .ab-btn.active::before { content: ''; position: absolute; left: -6px; top: 8px; bottom: 8px; width: 2px; background: var(--accent); border-radius: 2px; }

    /* Explorer (also the drop target for files from the desktop). */
    .explorer { width: var(--explorer-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; overflow: hidden; transition: background .12s ease; }
    .explorer.drag-zone-active { background: var(--ds-accent-soft); outline: 1px dashed var(--accent); outline-offset: -1px; }
    .explorer-header { height: var(--tab-h); padding: 0 6px 0 14px; font-size: .8125rem; font-weight: 600; color: var(--ds-text-secondary); display: flex; align-items: center; justify-content: space-between; gap: 8px; border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .explorer-actions { display: flex; gap: 2px; }
    .exp-action { width: 28px; height: 28px; border-radius: var(--radius-sm); border: 0; background: transparent; color: var(--muted); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background .12s ease, color .12s ease; }
    .exp-action:hover { color: var(--text); background: var(--surface2); }
    .explorer-tree { flex: 1; overflow-y: auto; padding: 6px; }
    .tree-node { user-select: none; }
    .tree-row { display: flex; align-items: center; gap: 6px; min-height: 28px; padding: 3px 6px; border-radius: var(--radius-xs); cursor: pointer; font: 400 .8125rem/1.4 var(--ds-font-mono); color: var(--ds-text-secondary); transition: background .12s ease, color .12s ease; position: relative; border: 1px solid transparent; }
    .tree-row:hover { background: var(--surface2); color: var(--text); }
    .tree-row.active { background: var(--ds-accent-soft); color: var(--text); }
    .tree-row.active .file-icon { color: var(--ds-accent-text); }
    .tree-row.dragging { opacity: .45; background: var(--surface2); }
    .tree-row.drag-over { background: var(--ds-accent-soft) !important; border: 1px dashed var(--accent); color: var(--text); }
    .tree-row .caret { width: 14px; height: 14px; flex-shrink: 0; color: var(--dim); transition: transform .12s ease; }
    .tree-row.open .caret { transform: rotate(90deg); }
    .tree-row .file-icon { width: 14px; height: 14px; flex-shrink: 0; color: var(--muted); }
    .tree-row .folder-icon { color: var(--ds-accent-text); }
    .tree-row .py-icon { color: var(--muted); }
    .tree-row .node-name { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; pointer-events: none; }
    .tree-row .node-actions { display: none; gap: 2px; flex-shrink: 0; }
    .tree-row:hover .node-actions, .tree-row:focus-within .node-actions { display: flex; }
    .node-act-btn { width: 22px; height: 22px; border: 0; background: transparent; color: var(--muted); cursor: pointer; border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center; transition: background .12s ease, color .12s ease; }
    .node-act-btn:hover { color: var(--text); background: var(--ds-surface-hover); }
    .node-act-btn.del:hover { color: var(--ds-danger-text); background: var(--ds-danger-soft); }
    .tree-children { display: none; }
    .tree-children.open { display: block; }

    .editor-area { flex: 1; min-width: 0; display: flex; flex-direction: column; overflow: hidden; background: var(--bg); }
    .tab-bar { height: var(--tab-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: stretch; overflow-x: auto; overflow-y: hidden; flex-shrink: 0; scrollbar-width: thin; }
    .tab { height: 100%; padding: 0 10px 0 14px; display: flex; align-items: center; gap: 8px; font: 400 .8125rem/1 var(--ds-font-mono); color: var(--muted); cursor: pointer; border-right: 1px solid var(--border); border-bottom: 2px solid transparent; white-space: nowrap; transition: background .12s ease, color .12s ease; user-select: none; flex-shrink: 0; }
    .tab:hover { color: var(--text); background: var(--surface2); }
    .tab.active { color: var(--text); background: var(--bg); border-bottom-color: var(--accent); }
    .tab .tab-close { width: 20px; height: 20px; border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center; color: var(--dim); transition: background .12s ease, color .12s ease; }
    .tab .tab-close:hover { color: var(--text); background: var(--ds-surface-hover); }
    .tab.modified .tab-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }
    .editor-wrap { flex: 1; min-height: 0; display: flex; flex-direction: column; overflow: hidden; }
    .editor-pane { flex: 1; min-height: 0; overflow: hidden; position: relative; display: flex; flex-direction: column; }
    .editor-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; gap: 6px; padding: 32px 20px; color: var(--muted); text-align: center; }
    .editor-empty svg { width: 32px; height: 32px; margin-bottom: 6px; }
    .editor-empty h3 { font-size: .9375rem; font-weight: 600; color: var(--ds-text-secondary); }
    .editor-empty p { font-size: .875rem; line-height: 1.5; }
    #cm-host { flex: 1; min-height: 0; display: none; overflow: hidden; }

    /* Editor surfaces follow the page; syntax colours stay with the theme. */
    #cm-host .CodeMirror { height: 100% !important; font-family: var(--ds-font-mono) !important; font-size: 14px !important; line-height: 1.6 !important; background: var(--bg) !important; }
    #cm-host .CodeMirror-gutters { background: var(--bg) !important; border-right: 1px solid var(--border) !important; padding-right: 8px !important; padding-left: 4px !important; }
    #cm-host .CodeMirror-linenumber { color: var(--dim) !important; min-width: 30px !important; padding-left: 0 !important; text-align: right !important; }
    #cm-host .CodeMirror-activeline-background { background: rgba(255, 255, 255, .04); }
    #cm-host .CodeMirror-scrollbar-filler, #cm-host .CodeMirror-gutter-filler { background: var(--bg); }
    #cm-host .CodeMirror-foldgutter-open, #cm-host .CodeMirror-foldgutter-folded { color: var(--dim); }
    #cm-host .CodeMirror-foldmarker { padding: 0 4px; border-radius: var(--radius-xs); background: var(--ds-accent-soft); color: var(--ds-accent-text); font-family: var(--ds-font-mono); text-shadow: none; }
    #cm-host .cm-searching { background: rgba(245, 158, 11, .28); }
    #cm-host .CodeMirror-dialog { padding: 8px 12px; background: var(--surface); color: var(--ds-text-secondary); font: 400 .8125rem/1.5 var(--ds-font-sans); }
    #cm-host .CodeMirror-dialog-top { border-bottom: 1px solid var(--border); }
    #cm-host .CodeMirror-dialog-bottom { border-top: 1px solid var(--border); }
    #cm-host .CodeMirror-dialog input { width: min(20em, 60%); color: var(--text); font-family: var(--ds-font-mono); font-size: .8125rem; }
    #cm-host .CodeMirror-dialog button { min-height: 26px; padding: 0 8px; margin-left: 4px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2); color: var(--text); font: 500 .75rem var(--ds-font-sans); cursor: pointer; }
    /* Autocomplete list (attached to <body> by CodeMirror). */
    .CodeMirror-hints { padding: 4px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface); box-shadow: var(--ds-shadow-md); font: 400 .8125rem/1.5 var(--ds-font-mono); max-width: calc(100vw - 24px); }
    .CodeMirror-hint { padding: 2px 8px; border-radius: var(--radius-xs); color: var(--ds-text-secondary); }
    li.CodeMirror-hint-active { background: var(--ds-accent-strong); color: #fff; }

    /* ── terminal ─────────────────────────────────────────────── */
    .bottom-panel { height: var(--terminal-h); background: var(--surface3); border-top: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; transition: height .16s ease; }
    .bottom-panel.collapsed { height: 34px; }
    .panel-tabs { height: 34px; display: flex; align-items: stretch; border-bottom: 1px solid var(--border); padding: 0 6px 0 12px; gap: 4px; flex-shrink: 0; overflow: hidden; }
    .panel-tab { display: flex; align-items: center; padding: 0 2px; font-size: .8125rem; font-weight: 500; color: var(--muted); border-bottom: 2px solid transparent; cursor: default; }
    .panel-tab.active { color: var(--text); border-bottom-color: var(--accent); }
    .panel-actions { margin-left: auto; display: flex; align-items: center; gap: 2px; }
    .panel-act-btn { width: 28px; height: 28px; border: 0; background: transparent; color: var(--muted); cursor: pointer; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; transition: background .12s ease, color .12s ease; }
    .panel-act-btn:hover { color: var(--text); background: var(--surface2); }
    .terminal-body { flex: 1; min-height: 0; overflow-y: auto; padding: 10px 16px; font-family: var(--ds-font-mono); font-size: .8125rem; line-height: 1.6; color: var(--ds-text-secondary); }
    .term-prompt { color: var(--ds-success-text); }
    .term-output { color: var(--ds-text-secondary); white-space: pre-wrap; word-break: break-all; }
    .term-error  { color: var(--ds-danger-text); white-space: pre-wrap; word-break: break-all; }
    .term-info   { color: var(--muted); }
    .term-success { color: var(--ds-success-text); }
    .term-stream { color: var(--ds-text-secondary); white-space: pre-wrap; overflow-wrap: anywhere; }
    .term-stream-input { color: var(--ds-success-text); font-weight: 500; }
    /* Typed straight into the transcript, like a real console line. */
    .term-input-field { background: transparent; border: 0; outline: none; padding: 0; margin: 0; color: var(--ds-success-text); font-family: inherit; font-size: inherit; font-weight: 500; caret-color: var(--ds-success-text); min-width: 10ch; max-width: 100%; }
    .term-input-caret { color: var(--ds-success-text); animation: termCaret 1.05s step-end infinite; }
    @keyframes termCaret { 0%,100% { opacity: 1; } 50% { opacity: 0; } }
    .resize-handle { height: 4px; background: transparent; cursor: ns-resize; flex-shrink: 0; transition: background .12s ease; }
    .resize-handle:hover { background: var(--accent); }

    /* ── status bar ───────────────────────────────────────────── */
    .statusbar { min-height: 26px; background: var(--surface); border-top: 1px solid var(--border); display: flex; align-items: center; flex-wrap: wrap; padding: 0 12px; gap: 4px 16px; font-size: .75rem; color: var(--muted); font-variant-numeric: tabular-nums; flex-shrink: 0; user-select: none; }
    .statusbar-right { margin-left: auto; display: flex; flex-wrap: wrap; gap: 4px 16px; }
    #status-msg { color: var(--ds-text-secondary); }

    /* ── context menu ─────────────────────────────────────────── */
    .ctx-menu { position: fixed; background: var(--surface); border: 1px solid var(--ds-border-strong); border-radius: var(--radius); padding: 4px; min-width: 168px; max-width: calc(100vw - 24px); z-index: 9999; box-shadow: var(--ds-shadow-md); display: none; }
    .ctx-menu.open { display: block; }
    .ctx-item { display: flex; align-items: center; gap: 8px; min-height: 32px; padding: 0 10px; border-radius: var(--radius-xs); font-size: .8125rem; cursor: pointer; color: var(--ds-text-secondary); transition: background .12s ease, color .12s ease; }
    .ctx-item:hover { background: var(--surface2); color: var(--text); }
    .ctx-item.danger { color: var(--ds-danger-text); }
    .ctx-item.danger:hover { background: var(--ds-danger-soft); color: var(--ds-danger-text); }
    .ctx-sep { height: 1px; background: var(--border); margin: 4px 6px; }

    /* ── dialogs ──────────────────────────────────────────────── */
    .modal-bg { position: fixed; inset: 0; background: var(--ds-overlay); z-index: 8888; display: none; align-items: center; justify-content: center; padding: 16px; overflow-y: auto; }
    .modal-bg.open { display: flex; }
    .modal { background: var(--surface); border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg); padding: 20px; width: min(420px, 100%); max-height: calc(100dvh - 32px); overflow-y: auto; display: flex; flex-direction: column; gap: 16px; box-shadow: var(--ds-shadow-lg); }
    .modal h3 { font-size: 1rem; font-weight: 600; line-height: 1.35; color: var(--text); }
    .modal p.subtitle { font-size: .875rem; line-height: 1.5; color: var(--muted); margin-top: -10px; }
    .modal-input-group { display: flex; flex-direction: column; gap: 6px; }
    .modal-input-group label { font-size: .8125rem; font-weight: 500; color: var(--ds-text-secondary); }
    .modal input, .modal textarea { width: 100%; min-height: 38px; background: var(--surface3); border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-mono); padding: 8px 12px; outline: none; transition: border-color .12s ease, box-shadow .12s ease; }
    .modal input::placeholder, .modal textarea::placeholder { color: var(--dim); }
    .modal textarea { min-height: 120px; resize: vertical; }
    .modal input:focus, .modal textarea:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .modal-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; margin-top: 4px; }
    .modal-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 0 16px; border-radius: var(--radius-sm); font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer; border: 1px solid var(--ds-border-strong); background: var(--surface2); color: var(--text); transition: background .12s ease, border-color .12s ease; }
    .modal-btn:hover { background: var(--ds-surface-hover); }
    .modal-btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .modal-btn.primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

    @keyframes spin { to { transform: rotate(360deg); } }
    .spinner { width: 14px; height: 14px; border: 2px solid var(--ds-border-strong); border-top-color: var(--accent); border-radius: 50%; animation: spin .6s linear infinite; }
    .sr-only { position: absolute !important; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
    /* The IDE fills the whole window. Page notifications float over it instead of
       pushing the editor down and back up again. */
    .app > #ds-global-notification-stack {
      position: fixed !important;
      top: calc(var(--topbar-h) + 10px) !important;
      right: 16px !important;
      bottom: auto !important;
      left: auto !important;
      margin: 0 !important;
      width: min(380px, calc(100vw - 32px));
      z-index: 1200;
    }

    /* ── AI review panel ──────────────────────────────────────── */
    #rb-toggle {
      position: fixed; bottom: 40px; right: 20px; z-index: 1000;
      width: 44px; height: 44px; border-radius: var(--radius);
      background: var(--accent); border: 1px solid var(--accent); cursor: pointer;
      display: flex; align-items: center; justify-content: center;
      transition: background .12s ease, border-color .12s ease;
      color: #fff;
    }
    #rb-toggle:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    #rb-toggle .rb-icon-chat, #rb-toggle .rb-icon-close { transition: opacity .12s ease; }
    #rb-toggle .rb-icon-close { position: absolute; opacity: 0; }
    #rb-toggle.rb-open .rb-icon-chat  { opacity: 0; }
    #rb-toggle.rb-open .rb-icon-close { opacity: 1; }
    /* A review is waiting — hidden once the panel is open. */
    #rb-toggle .rb-dot {
      position: absolute; top: -4px; right: -4px;
      width: 10px; height: 10px; border-radius: 50%;
      background: var(--ds-success); border: 2px solid var(--bg);
      opacity: 0; transition: opacity .16s ease;
    }
    #rb-toggle.rb-has-review .rb-dot { opacity: 1; }
    #rb-toggle.rb-open .rb-dot { opacity: 0 !important; }
    @keyframes rbPulse { 0%,100% { opacity: 1; } 50% { opacity: .35; } }

    #rb-panel {
      position: fixed; bottom: 92px; right: 20px; z-index: 1000;
      width: min(360px, calc(100vw - 24px)); height: min(520px, calc(100dvh - 160px));
      background: var(--surface); border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg);
      display: flex; flex-direction: column; overflow: hidden;
      box-shadow: var(--ds-shadow-lg);
      opacity: 0; pointer-events: none; visibility: hidden;
      transform: translateY(8px);
      transition: opacity .16s ease, transform .16s ease, visibility 0s linear .16s;
    }
    #rb-panel.rb-open { opacity: 1; pointer-events: all; visibility: visible; transform: none; transition: opacity .16s ease, transform .16s ease, visibility 0s; }

    .rb-header {
      min-height: 52px; flex-shrink: 0;
      display: flex; align-items: center; gap: 12px; padding: 8px 16px;
      background: var(--surface); border-bottom: 1px solid var(--border);
      user-select: none;
    }
    .rb-header-title { font-size: .875rem; font-weight: 600; line-height: 1.35; color: var(--text); }
    .rb-header-sub   { font-size: .75rem; line-height: 1.4; color: var(--muted); }
    .rb-status {
      margin-left: auto; display: flex; align-items: center; gap: 6px; flex-shrink: 0;
      font-size: .75rem; color: var(--ds-success-text); white-space: nowrap;
    }
    .rb-status-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--ds-success); }
    .rb-status.rb-busy .rb-status-dot { background: var(--ds-warning); animation: rbPulse 1s ease-in-out infinite; }
    .rb-status.rb-busy span { color: var(--ds-warning-text); }

    #rb-msgs {
      flex: 1; min-height: 0; overflow-y: auto; padding: 12px;
      display: flex; flex-direction: column; gap: 10px;
      scroll-behavior: smooth;
    }
    #rb-msgs::-webkit-scrollbar { width: 6px; }
    #rb-msgs::-webkit-scrollbar-thumb { background: var(--ds-border-strong); border-radius: var(--radius-xs); border: 0; }

    .rb-msg { display: flex; gap: 8px; align-items: flex-start; animation: rbMsgIn .16s ease; }
    @keyframes rbMsgIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
    .rb-msg.rb-user { flex-direction: row-reverse; }
    .rb-avatar {
      width: 28px; height: 28px; border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: .75rem; font-weight: 600; flex-shrink: 0;
    }
    .rb-msg.rb-bot  .rb-avatar { background: var(--ds-accent-soft); border: 1px solid var(--ds-accent-border); color: var(--ds-accent-text); }
    .rb-msg.rb-user .rb-avatar { background: var(--surface2); border: 1px solid var(--ds-border-strong); color: var(--ds-text-secondary); }
    .rb-body { flex: 1; min-width: 0; }
    .rb-msg.rb-user .rb-body { display: flex; flex-direction: column; align-items: flex-end; }
    .rb-bubble {
      display: inline-block; padding: 10px 12px; border-radius: var(--radius-sm);
      font-size: .8125rem; line-height: 1.55; max-width: 100%; word-break: break-word;
    }
    .rb-msg.rb-user .rb-bubble {
      background: var(--ds-accent-soft); border: 1px solid var(--ds-accent-border);
      color: var(--ds-text-secondary); font-family: var(--ds-font-mono); font-size: .75rem;
      white-space: pre-wrap; text-align: left; max-height: 120px; overflow-y: auto;
    }
    .rb-msg.rb-bot .rb-bubble {
      background: var(--surface2); border: 1px solid var(--border);
      color: var(--text); width: 100%;
    }
    /* Formatted review sections */
    .rb-section { font-size: .8125rem; font-weight: 600; color: var(--ds-accent-text); margin: 10px 0 4px; padding-bottom: 4px; border-bottom: 1px solid var(--border); }
    .rb-section:first-child { margin-top: 0; }
    .rb-section.ok   { color: var(--ds-success-text); }
    .rb-section.err  { color: var(--ds-danger-text); }
    .rb-section.warn { color: var(--ds-warning-text); }
    .rb-bullet { display: flex; gap: 8px; font-size: .8125rem; color: var(--ds-text-secondary); line-height: 1.5; padding: 1px 0; }
    .rb-bullet::before { content: '›'; color: var(--ds-accent-text); flex-shrink: 0; font-weight: 600; }
    .rb-code { background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px; margin: 4px 0; font-family: var(--ds-font-mono); font-size: .75rem; color: var(--ds-accent-text); white-space: pre-wrap; word-break: break-word; overflow-x: auto; }
    .rb-line { font-size: .8125rem; color: var(--ds-text-secondary); line-height: 1.55; padding: 1px 0; }
    .rb-line + .rb-line { margin-top: 4px; }
    .rb-line .rb-key { color: var(--text); font-weight: 600; }
    .rb-line .rb-key.ok  { color: var(--ds-success-text); }
    .rb-line .rb-key.err { color: var(--ds-danger-text); }
    /* Typing indicator */
    .rb-typing .rb-bubble { padding: 12px; }
    .rb-dots { display: flex; gap: 4px; align-items: center; height: 13px; }
    .rb-dots span { width: 5px; height: 5px; border-radius: 50%; background: var(--muted); animation: rbDot 1.2s ease-in-out infinite; }
    .rb-dots span:nth-child(2) { animation-delay: .18s; }
    .rb-dots span:nth-child(3) { animation-delay: .4s; }
    .rb-wait { margin-top: 8px; font-size: .75rem; line-height: 1.5; color: var(--muted); }
    @keyframes rbDot { 0%,80%,100% { opacity: .35; } 40% { opacity: 1; } }

    /* Input area */
    .rb-input-area {
      flex-shrink: 0; border-top: 1px solid var(--border);
      background: var(--surface); padding: 12px;
      display: flex; flex-direction: column; gap: 8px;
    }
    .rb-input-label { font-size: .8125rem; font-weight: 500; color: var(--ds-text-secondary); }
    #rb-input {
      width: 100%; min-height: 60px; max-height: 120px; resize: vertical;
      background: var(--surface3); border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm);
      padding: 8px 12px; font: 400 .8125rem/1.5 var(--ds-font-sans);
      color: var(--text); outline: none; transition: border-color .12s ease, box-shadow .12s ease; display: block;
    }
    #rb-input:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    #rb-input::placeholder { color: var(--dim); }
    .rb-input-row { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .rb-clear { min-height: 32px; padding: 0 8px; margin-left: -8px; font: 500 .8125rem/1.2 var(--ds-font-sans); color: var(--muted); background: transparent; border: 0; border-radius: var(--radius-sm); cursor: pointer; transition: background .12s ease, color .12s ease; }
    .rb-clear:hover { color: var(--text); background: var(--surface2); }
    .rb-clear:disabled { opacity: .55; cursor: not-allowed; }
    #rb-send {
      display: inline-flex; align-items: center; gap: 6px;
      min-height: 32px; padding: 0 12px; border-radius: var(--radius-sm); border: 1px solid var(--accent);
      background: var(--accent); color: #fff;
      font: 500 .8125rem/1.2 var(--ds-font-sans);
      cursor: pointer; transition: background .12s ease, border-color .12s ease; flex-shrink: 0;
    }
    #rb-send:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    #rb-send:disabled { opacity: .55; cursor: not-allowed; }

    /* ── touch screens: row actions cannot rely on hover ─────── */
    @media (hover: none) {
      .tree-row { min-height: 36px; }
      .tree-row .node-actions { display: flex; }
      .node-act-btn { width: 28px; height: 28px; }
      .exp-action, .panel-act-btn { width: 32px; height: 32px; }
    }

    /* ── tablets and phones: the page scrolls, panes stack ───── */
    @media (max-width: 900px) {
      html, body { height: auto; overflow: visible; }
      body { overflow-x: hidden; }
      .app { height: auto; min-height: 100vh; min-height: 100dvh; }
      .topbar { flex-wrap: wrap; padding: 8px 12px; }
      .topbar-breadcrumb { flex: 1 1 0; }
      .topbar-actions { width: 100%; margin-left: 0; flex-wrap: wrap; }
      .tb-btn.dashboard-btn { margin-left: auto; }
      .workspace { flex-direction: column; overflow: visible; }
      .activity-bar { width: auto; height: 44px; flex-direction: row; padding: 0 6px; border-right: 0; border-bottom: 1px solid var(--border); }
      .ab-btn.active::before { left: 8px; right: 8px; top: auto; bottom: -4px; width: auto; height: 2px; }
      .explorer { width: 100%; max-height: 38vh; max-height: 38dvh; border-right: 0; border-bottom: 1px solid var(--border); }
      .explorer-tree { min-height: 96px; }
      .editor-area { flex: none; overflow: visible; }
      .editor-wrap { flex: none; overflow: visible; }
      .editor-pane { flex: none; height: 55vh; height: 55dvh; min-height: 320px; }
      .bottom-panel { height: min(360px, 45vh); height: min(360px, 45dvh); }
      .resize-handle { display: none; }
      .statusbar { padding: 4px 12px; }
      #rb-toggle { bottom: 16px; right: 16px; }
      #rb-panel { right: 12px; bottom: 72px; height: min(520px, calc(100dvh - 96px)); }
    }

    @media (max-width: 640px) {
      .tb-select { flex: 1 1 100%; min-width: 0; }
      .topbar-nav-divider { display: none; }
      .editor-pane { min-height: 280px; }
      .terminal-body { padding: 10px 12px; }
      .statusbar-right { margin-left: 0; }
      #rb-panel { left: 12px; width: auto; }
    }

    @media (max-width: 400px) {
      .tb-btn.dashboard-btn { padding: 0 8px; }
      .tb-btn.dashboard-btn svg { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
      .bottom-panel, #rb-panel, .rb-msg { transition: none; animation: none; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'IDE', 'pageDescription' => 'Write, run, and get feedback on Python inside the DataSensei workspace.'])
</head>
<body>

<div class="app">
  <div class="topbar">
    <div class="topbar-logo">
      @include('partials.brand-logo', [
        'variant' => 'topbar',
        'size' => 'compact',
        'showText' => true,
        'href' => route('studentDashboard'),
      ])
    </div>
    <div class="topbar-breadcrumb" aria-label="Current file">
      <span class="seg">Workspace</span>
      <span class="arrow" id="breadcrumb-sep" style="display:none">/</span>
      <span class="seg" id="breadcrumb-file"></span>
    </div>
    <div class="topbar-actions">
      <a href="{{ route('studentDashboard') }}" id="returnToLessonBtn" class="tb-btn return-btn" style="display: none;">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        Return to Lesson
      </a>
      <select class="tb-select" id="pythonSampleSelect" title="Auto-generate sample Python code" aria-label="Generate a predefined Python sample">
        <option value="">Generate a Python sample...</option>
        <option value="simple">Simple Code</option>
        <option value="input">Code with Input</option>
        <option value="list">Code with Array/List</option>
        <option value="matplot">Code with Matplotlib</option>
        <option value="csv_matplot">CSV Data + Matplotlib</option>
      </select>
      <button class="tb-btn save" id="btn-save" onclick="IDE.save()" title="Save (Ctrl+S)">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        <span id="btn-save-label">Save</span>
      </button>
      <button class="tb-btn run" id="btn-run" onclick="IDE.runOrStop()" title="Run (Ctrl+Enter) — click again to stop">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
        Run
      </button>
      @include('student.partials.notification-center', ['variant' => 'topbar'])
      <span class="topbar-nav-divider" aria-hidden="true"></span>
      <a href="{{ route('studentDashboard') }}" class="tb-btn dashboard-btn" title="Return to dashboard" aria-label="Return to dashboard">
        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M19 12H5m7 7-7-7 7-7"/></svg>
        <span>Dashboard</span>
      </a>
    </div>
  </div>

  <span id="save-indicator" class="sr-only" role="status" aria-live="polite"></span>

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
    <h3>Program Input</h3>
    <p class="subtitle" id="input-modal-subtitle">Enter the value requested by your Python program.</p>
    <div class="modal-input-group"><label id="input-modal-label" for="dynamic-single-input">Program input:</label><input type="text" id="dynamic-single-input" maxlength="10000" placeholder="Type your answer and press Enter" autocomplete="off" spellcheck="false" /></div>
    <div class="modal-actions"><button class="modal-btn" onclick="IDE.cancelInput()">Cancel Run</button><button class="modal-btn primary" onclick="IDE.submitInput()">Submit</button></div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════════
     AI REVIEW CHATBOT WIDGET HTML
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
    <div style="flex:1;min-width:0">
      <div class="rb-header-title">AI Code Reviewer</div>
      <div class="rb-header-sub">Auto-reviews on every Run</div>
    </div>
    <div class="rb-status" id="rb-status" role="status" aria-live="polite">
      <div class="rb-status-dot"></div><span>Ready</span>
    </div>
  </div>

  <div id="rb-msgs" aria-live="polite"></div>

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
     REVIEW BOT JS MODULE (global — before IDE IIFE)
═══════════════════════════════════════════════════ --}}
<script src="{{ asset('js/python-ide-input.js') }}"></script>
<script>
// ── ReviewBot ─────────────────────────────────────────────────────────────────
// Laravel endpoint handled by CodeReviewController.
const REVIEW_URL = @json(route('api.code-review'));
const REVIEW_CLIENT_TIMEOUT_MS = {{ (int) config('code_execution.ollama.client_timeout_ms', 14000) }};
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

const ReviewBot = (() => {
  let _open     = false;
  let _busy     = false;
  let _lastCode = '';
  let _lastLang = 'python';
  let _lastRunOutput = '';
  const _typingTimers = {};
  let _activeController = null;
  // A review that outlived the request keeps going on the server; this token
  // lets a newer Run replace it without waiting.
  let _backgroundWait = null;
  let _generation = 0;
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
      '<div class="rb-line" style="margin-top:4px;color:var(--dim)">You can also ask me follow-up questions below.</div>'
    );
  }

  /* ── Called by IDE.run() automatically ── */
  function autoReview(code, lang, filename, runOutput = '') {
    if (!code || !code.trim()) return;
    if (_busy && _backgroundWait) {
      // The earlier review is only waiting on the server. The new run replaces it.
      _backgroundWait.cancel();
    } else if (_busy) {
      _open_panel();
      _addBot('<div class="rb-line" style="color:var(--warn2)">An AI review is already running. Please wait for it to finish before requesting another review.</div>');
      return;
    }

    _lastCode = code;
    _lastLang = lang || 'python';
    _lastRunOutput = runOutput || '';
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

    void _sendReview(code, lang);
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

    if (!_lastCode.trim()) {
      _addBot('<div class="rb-line" style="color:var(--warn2)">Run a Python file first so I have code and output to discuss.</div>');
      return;
    }

    // Block code generation requests on the frontend
    if (_isCodeGenRequest(question)) {
      $input().value = '';
      _addUser(question);
      _addBot(
        '<div class="rb-line" style="color:var(--warn2)">⚠ I\'m a <strong style="color:var(--text)">code reviewer</strong>, not a code generator. ' +
        'I can\'t write or produce code for you — but I can help you understand issues in your existing code, explain concepts, or point you in the right direction.</div>'
      );
      return;
    }

    $input().value = '';
    _addUser(question);

    _history.push({ role: 'user', content: question });
    void _callAI(question);
  }

  function handleKey(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); sendFollowUp(); }
  }

  /* ── Clear ── */
  function clear() {
    if (_busy) return;
    $msgs().innerHTML = '';
    _history.length = 0;
    _addWelcome();
  }

  function _boundedRunOutput(value) {
    const text = String(value || '').trim();
    const limit = 12000;
    if (text.length <= limit) return text;
    return text.slice(0, 2000) + '\n[... earlier terminal output omitted ...]\n' + text.slice(-(limit - 2050));
  }

  function _localFallback(runOutput, isChat = false) {
    const failed = /\bExit code:\s*(?!0\b)-?\d+/i.test(runOutput || '')
      || /\b(?:SyntaxError|IndentationError|NameError|TypeError|ValueError|ImportError|ModuleNotFoundError|ZeroDivisionError|IndexError|KeyError|AttributeError|Exception)\b/i.test(runOutput || '');

    if (failed) {
      return 'Status: Has Issues\nFeedback: Your program stopped with an error. The AI reviewer could not be reached, so here is where to look.\nSteps to Fix:\n- Read the final traceback message.\n- Inspect the reported line and the values used there.\n- Correct the cause and run the program again.';
    }

    return isChat
      ? 'Status: Not Reviewed\nFeedback: The AI reviewer could not be reached from this page. Your last run is still loaded, so you can ask again in a moment.'
      : 'Status: Not Reviewed\nFeedback: Your program ran and reported no execution error. The AI reviewer could not be reached from this page, so it was not reviewed. Nothing is wrong with your run.\nCheck On Your Own:\n- Compare the result with the expected output.\n- Test invalid and boundary inputs.\n- Review conditions, loops, function results, and edge cases.';
  }

  function _formatElapsed(totalSeconds) {
    const seconds = Math.max(0, Math.round(totalSeconds));
    if (seconds < 60) return `${seconds}s`;
    const minutes = Math.floor(seconds / 60);
    return `${minutes}m ${String(seconds % 60).padStart(2, '0')}s`;
  }

  /* ── Wait for a review that continues on the server ── */
  function _cancellableSleep(ms, wait) {
    return new Promise(resolve => {
      const timer = setTimeout(resolve, ms);
      wait.onCancel = () => { clearTimeout(timer); resolve(); };
    });
  }

  async function _awaitBackground(pending, typingId) {
    const wait = { cancelled: false, onCancel: null };
    wait.cancel = () => {
      wait.cancelled = true;
      // Turn the waiting bubble into a short note right away, in its own place.
      if (_typingTimers[typingId]) {
        clearInterval(_typingTimers[typingId]);
        delete _typingTimers[typingId];
      }
      const bubble = document.getElementById(typingId);
      if (bubble) {
        bubble.removeAttribute('id');
        bubble.classList.remove('rb-typing');
        bubble.querySelector('.rb-bubble').innerHTML = '<div class="rb-line" style="color:var(--dim)">This review was replaced by your newer run.</div>';
      }
      if (wait.onCancel) wait.onCancel();
    };
    _backgroundWait = wait;

    const typing = document.getElementById(typingId);
    if (typing) typing.dataset.longWait = '1';

    let failures = 0;
    try {
      while (true) {
        await _cancellableSleep(REVIEW_POLL_MS, wait);
        if (wait.cancelled) {
          const error = new Error('Replaced by a newer run.');
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
          data = await _readJsonResponse(response);
        } catch (networkError) {
          failures++;
          if (failures >= 6) throw networkError;
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
      if (_backgroundWait === wait) _backgroundWait = null;
    }
  }

  /* ── Core: send code for review ── */
  async function _sendReview(code, lang) {
    const generation = ++_generation;
    const typingId = _addTyping();
    _setBusy(true);

    try {
      const form = new FormData();
      form.append('mode',     'review');
      form.append('code',     code);
      form.append('language', lang || 'python');
      form.append('run_output', _boundedRunOutput(_lastRunOutput));

      const { data } = await _postReview(form);
      const msg = data.pending
        ? await _awaitBackground(data, typingId)
        : (data.message || '');
      _removeTyping(typingId);

      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } catch (err) {
      _removeTyping(typingId);
      if (err && err.superseded) return;
      const msg = _localFallback(_lastRunOutput, false);
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } finally {
      if (generation === _generation) _setBusy(false);
    }
  }

  /* ── Core: send follow-up ── */
  async function _callAI(question) {
    const generation = ++_generation;
    const typingId = _addTyping();
    _setBusy(true);
    let liveMessage = null;
    let streamedText = '';
    let pending = null;

    try {
      const form = new FormData();
      form.append('mode',     'chat');
      form.append('code',     _lastCode);
      form.append('language', _lastLang);
      form.append('question', question);
      form.append('run_output', _boundedRunOutput(_lastRunOutput));
      form.append('previous_context', _history.slice(0, -1).slice(-4).map(h => `${h.role.toUpperCase()}: ${h.content}`).join('\n---\n'));
      form.append('stream', '1');

      let completed = false;
      let finalMessage = '';
      const result = await _postReview(form, {
        stream: true,
        onEvent(event, payload) {
          if (event === 'delta' && payload.text) {
            streamedText += payload.text;
            _removeTyping(typingId);

            if (!liveMessage) {
              liveMessage = _addBot(_formatReview(streamedText));
            } else {
              liveMessage.querySelector('.rb-bubble').innerHTML = _formatReview(streamedText);
              _scroll();
            }
          } else if (event === 'done') {
            completed = true;
            finalMessage = payload.message || streamedText;
          } else if (event === 'pending' && payload.status_url) {
            pending = payload;
          } else if (event === 'error') {
            throw new Error(payload.message || 'The AI response stream failed.');
          }
        },
      });

      if (!result.streamed && result.data.pending) pending = result.data;

      const msg = pending
        ? await _awaitBackground(pending, typingId)
        : (result.streamed
          ? (completed ? finalMessage : '')
          : (result.data.message || ''));

      if (!msg) {
        throw new Error('The AI response ended before it was complete.');
      }

      _removeTyping(typingId);
      if (liveMessage) {
        liveMessage.querySelector('.rb-bubble').innerHTML = _formatReview(msg);
      } else {
        liveMessage = _addBot(_formatReview(msg));
      }
      _history.push({ role: 'assistant', content: msg });
    } catch (err) {
      _removeTyping(typingId);
      if (liveMessage) liveMessage.remove();
      if (err && err.superseded) return;
      const msg = _localFallback(_lastRunOutput, true);
      _addBot(_formatReview(msg));
      _history.push({ role: 'assistant', content: msg });
    } finally {
      if (generation === _generation) _setBusy(false);
    }
  }

  async function _postReview(form, { stream = false, onEvent = null } = {}) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), REVIEW_CLIENT_TIMEOUT_MS);
    _activeController = controller;

    try {
      const response = await fetch(REVIEW_URL, {
        method: 'POST',
        headers: {
          'Accept': stream ? 'application/json, text/event-stream' : 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: form,
        signal: controller.signal,
      });
      const contentType = response.headers.get('content-type') || '';

      if (stream && contentType.includes('text/event-stream')) {
        if (!response.ok) {
          throw new Error(`AI request failed with HTTP ${response.status}.`);
        }

        await _readEventStream(response, onEvent || (() => {}));

        return { streamed: true, data: null };
      }

      const data = await _readJsonResponse(response);
      if (!response.ok || !data.ok) {
        throw new Error(data.message || `AI request failed with HTTP ${response.status}.`);
      }

      return { streamed: false, data };
    } catch (error) {
      controller.abort();

      if (error.name === 'AbortError') {
        throw new Error('The AI request timed out. Ollama may be offline or overloaded.');
      }

      throw error;
    } finally {
      clearTimeout(timeoutId);
      if (_activeController === controller) _activeController = null;
    }
  }

  async function _readEventStream(response, onEvent) {
    if (!response.body) {
      throw new Error('This browser could not read the AI response stream.');
    }

    const reader = response.body.getReader();
    const decoder = new TextDecoder();
    let buffer = '';

    const dispatch = (block) => {
      const lines = block.split(/\r?\n/);
      let event = 'message';
      const dataLines = [];

      for (const line of lines) {
        if (line.startsWith('event:')) event = line.slice(6).trim();
        if (line.startsWith('data:')) dataLines.push(line.slice(5).trimStart());
      }

      if (dataLines.length === 0) return;

      let payload;
      try {
        payload = JSON.parse(dataLines.join('\n'));
      } catch (error) {
        throw new Error('The AI response stream contained malformed data.');
      }

      onEvent(event, payload);
    };

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;

      buffer += decoder.decode(value, { stream: true });
      const blocks = buffer.split(/\r?\n\r?\n/);
      buffer = blocks.pop() || '';
      blocks.forEach(dispatch);
    }

    buffer += decoder.decode();
    if (buffer.trim()) dispatch(buffer);
  }

  async function _readJsonResponse(response) {
    const text = await response.text();

    if (!text.trim()) {
      return {};
    }

    try {
      return JSON.parse(text);
    } catch (error) {
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
    return el;
  }

  function _addTyping() {
    const id  = 'rb-typing-' + Date.now();
    const el  = _buildMsg('rb-bot rb-typing', '<div class="rb-dots"><span></span><span></span><span></span></div><div class="rb-wait" hidden></div>');
    el.id     = id;
    $msgs().appendChild(el);
    _scroll();

    // A local model can take a few seconds. Show that it is still working so a
    // slow review never looks like a dead panel.
    const startedAt = Date.now();
    const note = el.querySelector('.rb-wait');
    _typingTimers[id] = setInterval(() => {
      const seconds = Math.round((Date.now() - startedAt) / 1000);
      if (seconds < 4) return;
      note.hidden = false;
      note.textContent = el.dataset.longWait === '1'
        ? `Still reviewing… ${_formatElapsed(seconds)}. This is taking longer than usual, but the review keeps going and will appear here. You can keep coding.`
        : `Still reviewing… ${_formatElapsed(seconds)}`;
    }, 1000);

    return id;
  }

  function _removeTyping(id) {
    if (_typingTimers[id]) {
      clearInterval(_typingTimers[id]);
      delete _typingTimers[id];
    }
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
    $input().setAttribute('aria-busy', on ? 'true' : 'false');
    const clearButton = document.querySelector('.rb-clear');
    if (clearButton) clearButton.disabled = on;
    $send().innerHTML = on
      ? '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> Wait…'
      : '<svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg> Ask';
    const $s = $status();
    $s.className = on ? 'rb-status rb-busy' : 'rb-status';
    $s.innerHTML = on
      ? '<div class="rb-status-dot"></div><span>AI is thinking...</span>'
      : '<div class="rb-status-dot"></div><span>Ready</span>';
  }

  /* ── Review formatter ── */
  function _formatReview(raw) {
    if (!raw || !raw.trim()) return '<div class="rb-line" style="color:var(--dim)">(No response)</div>';
    raw = raw.replace(/```[\s\S]*?```/g, '').replace(/`/g, '');
    // Headings introduce a list; everything else is a labelled sentence and must
    // stay readable prose instead of becoming a shouting section header.
    const HEADING = /^(Issues?|Suggestions?|Warnings?|Notes?|Steps to (?:Fix|Check)|Check On Your Own|Checked|Verified|Summary)\s*:\s*$/i;
    const LABEL   = /^(Status|Feedback|Explanation|Error|Location|Result)\s*:\s*(.*)$/i;
    // Tone comes from the verdict itself, never from a word inside the sentence.
    const VERDICT_OK  = /^(correct|clean|no issues|passed?|ok)\b/i;
    const VERDICT_ERR = /^(has issues|incorrect|failed?|error)\b/i;
    let html = '';
    {
      for (const line of raw.split('\n')) {
        const t = line.trim(); if (!t) continue;
        const label = t.match(LABEL);
        if (HEADING.test(t)) {
          html += `<div class="rb-section">${escH(t.replace(/\s*:\s*$/, ''))}</div>`;
        } else if (label) {
          const key = label[1];
          const value = label[2].trim();
          let tone = '';
          if (/^status$/i.test(key)) {
            tone = VERDICT_OK.test(value) ? ' ok' : VERDICT_ERR.test(value) ? ' err' : '';
          }
          html += `<div class="rb-line"><span class="rb-key${tone}">${escH(key)}:</span> ${escH(value)}</div>`;
        } else if (/^[-•*]\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^[-•*]\s+/, ''))}</div>`;
        } else if (/^\d+\.\s/.test(t)) {
          html += `<div class="rb-bullet">${escH(t.replace(/^\d+\.\s+/, ''))}</div>`;
        } else {
          html += `<div class="rb-line">${escH(t)}</div>`;
        }
      }
    }
    return html || '<div class="rb-line" style="color:var(--dim)">(Empty response)</div>';
  }

  function escH(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  return { toggle, autoReview, sendFollowUp, handleKey, clear };
})();
</script>

<script>
const WORKSPACE_ID = {{ $workspace->id }};
const TREE_DATA = @json($tree);
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const NODES_URL = @json(route('ide.nodes.store'));
const TREE_URL = @json(route('ide.tree'));

const IDE = (() => {
  let openTabs = []; let activeTab = null; let cm = null; let treeData = [...TREE_DATA]; let cmChanging = false;
  let draggedNodeId = null;
  let runInProgress = false;
  let runStopRequested = false;
  let runAbortController = null;

  function byId(id) { return document.getElementById(id); }
  function setText(id, value) { const node = byId(id); if (node) node.textContent = value ?? ''; }
  function setHtml(id, value) { const node = byId(id); if (node) node.innerHTML = value ?? ''; }
  function setDisplay(id, value) { const node = byId(id); if (node) node.style.display = value; }

  function init() {
      renderTree();

      if (!initCM()) {
          return;
      }

      setupResize(); setupKeyboard(); setupPythonSampleSelect(); handleLessonImports();
      setupGlobalDropZone();
      window.addEventListener('beforeunload', (event) => {
          if (!openTabs.some(tab => tab.modified)) return;
          event.preventDefault();
          event.returnValue = '';
      });
  }

  function setupPythonSampleSelect() {
      const select = document.getElementById('pythonSampleSelect');
      if (!select) return;

      select.addEventListener('change', async () => {
          const key = select.value;
          if (!key) return;

          select.disabled = true;
          setStatus('Generating sample…');

          try {
              await insertPythonSample(key);
          } finally {
              select.value = '';
              select.disabled = false;
          }
      });
  }

  // Safe duplicate-name generator: main.py -> main_1.py.
  // Avoids parentheses so generated samples always pass Laravel filename validation.
  function splitFilename(filename) {
      const lastDot = filename.lastIndexOf('.');
      if (lastDot > 0 && lastDot < filename.length - 1) {
          return { base: filename.slice(0, lastDot), ext: filename.slice(lastDot) };
      }
      return { base: filename, ext: '' };
  }

  function sanitizeGeneratedBase(base) {
      return (base || 'file')
          .replace(/[\s()]+/g, '_')
          .replace(/[^A-Za-z0-9_.-]+/g, '_')
          .replace(/_+/g, '_')
          .replace(/^[_.-]+|[_.-]+$/g, '') || 'file';
  }

  function generateSuggestedName(filename, forcedIndex = null) {
      const parts = splitFilename(filename);
      let base = sanitizeGeneratedBase(parts.base);
      let index = forcedIndex;
      const match = base.match(/_(\d+)$/);

      if (match) {
          if (index === null) index = parseInt(match[1], 10) + 1;
          base = base.replace(/_\d+$/, '');
      }

      if (index === null) index = 1;
      return `${base}_${index}${parts.ext}`;
  }

  function getChildrenForParent(parentId) {
      if (parentId === null || parentId === undefined) return treeData;
      const parent = findNode(treeData, parentId);
      return parent && Array.isArray(parent.children) ? parent.children : [];
  }

  function nodeNameExists(name, parentId = null) {
      return getChildrenForParent(parentId).some(n => String(n.name).toLowerCase() === String(name).toLowerCase());
  }

  function nextAvailableName(filename, parentId = null) {
      if (!nodeNameExists(filename, parentId)) return filename;

      for (let i = 1; i <= 100; i++) {
          const candidate = generateSuggestedName(filename, i);
          if (!nodeNameExists(candidate, parentId)) return candidate;
      }

      const parts = splitFilename(filename);
      return `${sanitizeGeneratedBase(parts.base)}_${Date.now()}${parts.ext}`;
  }

  // GLOBAL EXTERNAL DESKTOP DROP ZONE
  function setupGlobalDropZone() {
      const explorerPanel = document.getElementById('explorer-panel');
      explorerPanel.addEventListener('dragover', (e) => { e.preventDefault(); explorerPanel.classList.add('drag-zone-active'); });
      explorerPanel.addEventListener('dragleave', (e) => { e.preventDefault(); if (!explorerPanel.contains(e.relatedTarget)) { explorerPanel.classList.remove('drag-zone-active'); } });
      explorerPanel.addEventListener('drop', (e) => { e.preventDefault(); explorerPanel.classList.remove('drag-zone-active'); handleDrop(e, null); });
  }

  // CORE DRAG AND DROP HANDLER
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
                  await api(NODES_URL, 'POST', { workspace_id: WORKSPACE_ID, parent_id: parentId, type: 'file', name: fileNameToUse, content: e.target.result, language: 'python' });
                  resolve();
              } catch (err) {
                  if (err.collision) {
                      const suggested = nextAvailableName(fileNameToUse, parentId);
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
          await api(`${NODES_URL}/${encodeURIComponent(nodeId)}/move`, 'PATCH', payload);
          await refreshTree(); setStatus('Ready');
      } catch (err) {
          if (err.collision) {
              const node = findNode(treeData, nodeId);
              const suggested = nextAvailableName(node.name, newParentId);
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

  // ANTI-SPAM LESSON IMPORT LOGIC
  async function handleLessonImports() {
    const returnUrl = sessionStorage.getItem('datasensei_return_url');
    const returnBtn = document.getElementById('returnToLessonBtn');
    if (returnUrl && returnBtn) {
        try {
            const parsedReturnUrl = new URL(returnUrl, window.location.origin);
            if (parsedReturnUrl.origin === window.location.origin && ['http:', 'https:'].includes(parsedReturnUrl.protocol)) {
                returnBtn.style.display = 'inline-flex';
                returnBtn.href = parsedReturnUrl.pathname + parsedReturnUrl.search + parsedReturnUrl.hash;
                returnBtn.addEventListener('click', () => sessionStorage.removeItem('datasensei_return_url'));
            } else {
                sessionStorage.removeItem('datasensei_return_url');
            }
        } catch (_) {
            sessionStorage.removeItem('datasensei_return_url');
        }
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
                await api(`${NODES_URL}/${encodeURIComponent(practiceFile.id)}/save`, 'PATCH', { content: pendingCode });
                nodeToOpen = practiceFile; nodeToOpen.content = pendingCode;
            } else {
                const res = await api(NODES_URL, 'POST', { workspace_id: WORKSPACE_ID, parent_id: null, type: 'file', name: 'practice.py', content: pendingCode, language: 'python' });
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

  /* ── editor: Python-aware completion and IDE key bindings ─────────── */

  const PY_KEYWORDS = ['False','None','True','and','as','assert','async','await','break','class','continue','def','del','elif','else','except','finally','for','from','global','if','import','in','is','lambda','match','case','nonlocal','not','or','pass','raise','return','try','while','with','yield'];

  const PY_BUILTINS = ['abs','all','any','bin','bool','bytearray','bytes','callable','chr','classmethod','complex','dict','dir','divmod','enumerate','filter','float','format','frozenset','getattr','hasattr','hash','help','hex','id','input','int','isinstance','issubclass','iter','len','list','map','max','min','next','object','oct','open','ord','pow','print','property','range','repr','reversed','round','set','setattr','slice','sorted','staticmethod','str','sum','super','tuple','type','vars','zip','self','ArithmeticError','AssertionError','AttributeError','Exception','FileNotFoundError','IndexError','KeyError','KeyboardInterrupt','NameError','NotImplementedError','OverflowError','RuntimeError','StopIteration','TypeError','ValueError','ZeroDivisionError'];

  /** Completion pool: language words plus every identifier already in the file. */
  function pythonHint(editor) {
    const cursor = editor.getCursor();
    const token = editor.getTokenAt(cursor);
    const start = /[\w$]/.test(token.string) ? token.start : cursor.ch;
    const typed = editor.getRange({ line: cursor.line, ch: start }, cursor);
    const needle = typed.toLowerCase();

    const fileWords = new Set();
    const identifier = /[A-Za-z_][A-Za-z0-9_]*/g;
    let match;

    while ((match = identifier.exec(editor.getValue())) !== null) {
      fileWords.add(match[0]);
    }

    const list = Array.from(new Set([...PY_KEYWORDS, ...PY_BUILTINS, ...fileWords]))
      .filter(word => word !== typed && word.toLowerCase().startsWith(needle))
      .sort((a, b) => a.length - b.length || a.localeCompare(b))
      .slice(0, 40);

    return { list, from: CodeMirror.Pos(cursor.line, start), to: cursor };
  }

  function duplicateLine(editor) {
    const cursor = editor.getCursor();
    const line = editor.getLine(cursor.line);
    editor.replaceRange('\n' + line, { line: cursor.line, ch: line.length });
    editor.setCursor({ line: cursor.line + 1, ch: cursor.ch });
  }

  function moveLines(editor, direction) {
    const from = editor.getCursor('from');
    const to = editor.getCursor('to');
    const target = direction < 0 ? from.line - 1 : to.line + 1;

    if (target < 0 || target > editor.lastLine()) return;

    const moved = editor.getLine(target);
    const block = [];

    for (let line = from.line; line <= to.line; line++) block.push(editor.getLine(line));

    const rewritten = direction < 0 ? [...block, moved] : [moved, ...block];
    const startLine = Math.min(from.line, target);
    const endLine = Math.max(to.line, target);

    editor.replaceRange(
      rewritten.join('\n'),
      { line: startLine, ch: 0 },
      { line: endLine, ch: editor.getLine(endLine).length }
    );

    editor.setSelection(
      { line: from.line + direction, ch: from.ch },
      { line: to.line + direction, ch: to.ch }
    );
  }

  function gotoLine(editor) {
    const answer = window.prompt('Go to line');
    const line = parseInt(answer, 10);

    if (!Number.isFinite(line) || line < 1) return;

    const target = Math.min(line, editor.lineCount()) - 1;
    editor.setCursor({ line: target, ch: 0 });
    editor.scrollIntoView({ line: target, ch: 0 }, 120);
    editor.focus();
  }

  function pythonEditorKeys() {
    return {
      'Ctrl-/': 'toggleComment', 'Cmd-/': 'toggleComment',
      'Ctrl-Space': 'autocomplete',
      'Ctrl-S': () => save(), 'Cmd-S': () => save(),
      'Ctrl-Enter': () => run(), 'Cmd-Enter': () => run(),
      'Ctrl-F': 'findPersistent', 'Cmd-F': 'findPersistent',
      'Ctrl-H': 'replace', 'Cmd-Alt-F': 'replace',
      'Shift-Ctrl-H': 'replaceAll', 'Shift-Cmd-Alt-F': 'replaceAll',
      'Ctrl-G': 'findNext', 'Shift-Ctrl-G': 'findPrev',
      'Ctrl-L': editor => gotoLine(editor), 'Cmd-L': editor => gotoLine(editor),
      'Ctrl-D': editor => duplicateLine(editor), 'Cmd-D': editor => duplicateLine(editor),
      'Alt-Up': editor => moveLines(editor, -1),
      'Alt-Down': editor => moveLines(editor, 1),
      'Shift-Ctrl-K': 'deleteLine', 'Shift-Cmd-K': 'deleteLine',
      // PyCharm inserts spaces at the caret; only a selection is block-indented.
      'Tab': editor => {
        if (editor.somethingSelected()) { editor.execCommand('indentMore'); return; }
        editor.replaceSelection(' '.repeat(editor.getOption('indentUnit')), 'end');
      },
      'Shift-Tab': editor => editor.execCommand('indentLess'),
      'Esc': () => { if (runInProgress) stopRun(); },
    };
  }

  function initCM() {
    if (typeof window.CodeMirror !== 'function') {
      setStatus('Editor unavailable');
      setHtml('terminal-body', '<div class="term-error">The code editor could not be loaded. Verify the public/vendor/codemirror assets are installed.</div>');
      ['btn-save', 'btn-run', 'pythonSampleSelect'].forEach(id => { const element = byId(id); if (element) element.disabled = true; });
      return false;
    }

    cm = CodeMirror(document.getElementById('cm-host'), {
      mode: 'python', theme: 'dracula', lineNumbers: true, lineWrapping: false, tabSize: 4, indentUnit: 4, smartIndent: true,
      autoCloseBrackets: true, matchBrackets: true, styleActiveLine: true, foldGutter: true, gutters: ['CodeMirror-linenumbers','CodeMirror-foldgutter'],
      extraKeys: pythonEditorKeys(),
      hintOptions: { hint: pythonHint, completeSingle: false },
    });

    // Suggest as you type, the way a desktop IDE does, but never inside a
    // string or comment and never after a single character.
    cm.on('inputRead', (editor, change) => {
      if (change.origin !== '+input' || editor.state.completionActive) return;
      if (!/^[A-Za-z_]$/.test(change.text[0] || '')) return;

      const cursor = editor.getCursor();
      const token = editor.getTokenAt(cursor);

      if (token.type === 'string' || token.type === 'comment') return;
      if (token.string.trim().length < 2) return;

      editor.showHint({ hint: pythonHint, completeSingle: false });
    });

    cm.on('change', () => { if (cmChanging) return; if (activeTab !== null) { const tab = openTabs.find(t => t.id === activeTab); if (tab && !tab.modified) { tab.modified = true; renderTabBar(); } } });
    cm.on('cursorActivity', () => { const cur = cm.getCursor(); setText('status-pos', `Ln ${cur.line + 1}, Col ${cur.ch + 1}`); });
    return true;
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

  function attachCtxMenuListeners() { const root = document.getElementById('tree-root'); if (root.dataset.contextMenuReady === 'true') return; root.dataset.contextMenuReady = 'true'; root.addEventListener('contextmenu', (e) => { const row = e.target.closest('.tree-row'); if (!row) return; e.preventDefault(); const nodeEl = row.closest('.tree-node'); const id = parseInt(nodeEl.dataset.id); const type = nodeEl.dataset.type; const name = row.querySelector('.node-name').textContent; showCtxMenu(e.clientX, e.clientY, id, type, name); }); }
  function openFile(id, name, content) { document.querySelectorAll('.tree-row').forEach(r => r.classList.remove('active')); const nodeEl = document.querySelector(`.tree-node[data-id="${id}"]`); if (nodeEl) nodeEl.querySelector('.tree-row').classList.add('active'); setText('breadcrumb-file', name); setDisplay('breadcrumb-sep', ''); if (!openTabs.find(t => t.id === id)) { openTabs.push({ id, name, content, modified: false }); } activeTab = id; renderTabBar(); loadTabContent(id); updateStatusLang(name); }
  function loadTabContent(id) { const tab = openTabs.find(t => t.id === id); if (!tab || !cm) return; document.getElementById('editor-empty').style.display = 'none'; const cmHost = document.getElementById('cm-host'); cmHost.style.display = 'block'; setTimeout(() => { cm.refresh(); }, 10); cm.focus(); cmChanging = true; cm.setValue(tab.content ?? ''); cmChanging = false; cm.clearHistory(); cm.scrollTo(0, 0); }
  function syncActiveTabContent() { if (activeTab === null) return; const tab = openTabs.find(t => t.id === activeTab); if (tab) tab.content = cm.getValue(); }
  function renderTabBar() { const bar = document.getElementById('tab-bar'); bar.innerHTML = ''; openTabs.forEach(tab => { const el = document.createElement('div'); el.className = 'tab' + (tab.id === activeTab ? ' active' : '') + (tab.modified ? ' modified' : ''); el.innerHTML = `<svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>${escHtml(tab.name)}${tab.modified ? '<span class="tab-dot"></span>' : ''}<span class="tab-close" data-close="${tab.id}"><svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></span>`; el.addEventListener('click', (e) => { if (e.target.closest('[data-close]')) closeTab(tab.id); else switchTab(tab.id); }); bar.appendChild(el); }); }
  function switchTab(id) { syncActiveTabContent(); activeTab = id; renderTabBar(); loadTabContent(id); const tab = openTabs.find(t => t.id === id); if (tab) { setText('breadcrumb-file', tab.name); updateStatusLang(tab.name); } }
  function closeTab(id) { syncActiveTabContent(); const idx = openTabs.findIndex(t => t.id === id); if (idx < 0) return; const closingTab = openTabs[idx]; if (closingTab.modified && !confirm(`Close "${closingTab.name}" without saving your changes?`)) return; openTabs.splice(idx, 1); if (activeTab === id) { if (openTabs.length === 0) { activeTab = null; document.getElementById('editor-empty').style.display = 'flex'; document.getElementById('cm-host').style.display = 'none'; setText('breadcrumb-file', ''); setDisplay('breadcrumb-sep', 'none'); } else { const next = openTabs[Math.min(idx, openTabs.length - 1)]; switchTab(next.id); return; } } renderTabBar(); }
  async function save() { if (activeTab === null) return false; syncActiveTabContent(); const tab = openTabs.find(t => t.id === activeTab); if (!tab) return false; setStatus('Saving…'); try { await api(`${NODES_URL}/${encodeURIComponent(tab.id)}/save`, 'PATCH', { content: tab.content }); tab.modified = false; renderTabBar(); flashSave('Saved'); setStatus('Saved'); return true; } catch(e) { termPrint('error', 'Save failed: ' + e.message); setStatus('Save failed'); return false; } }
  let saveFlashTimer = null;
  function flashSave(msg) {
    // Confirm inside the Save button (fixed width) so the layout never moves.
    const live = byId('save-indicator');
    const button = byId('btn-save');
    const label = byId('btn-save-label');
    if (live) live.textContent = msg;
    if (!button || !label) return;
    clearTimeout(saveFlashTimer);
    button.classList.add('is-saved');
    label.textContent = msg;
    saveFlashTimer = setTimeout(() => {
      button.classList.remove('is-saved');
      label.textContent = 'Save';
      if (live) live.textContent = '';
    }, 1600);
  }

  let currentInputResolve = null;

  // Kept for the legacy modal path; the run loop now reads from the terminal.
  function askUserForInput(promptText) { return new Promise((resolve) => { const modal = document.getElementById('program-input-modal'); const label = document.getElementById('input-modal-label'); const input = document.getElementById('dynamic-single-input'); label.textContent = promptText; input.value = ''; modal.classList.add('open'); setTimeout(() => input.focus(), 50); currentInputResolve = resolve; }); }

  /**
   * Read one value the way a terminal does: the caret sits at the end of the
   * program's own prompt, the learner types inline, and Enter commits it.
   * Resolves with null when the run is stopped (Stop button, Esc or Ctrl+C).
   */
  function termReadLine(stream) {
    return new Promise((resolve) => {
      const field = document.createElement('input');
      field.type = 'text';
      field.className = 'term-input-field';
      field.autocomplete = 'off';
      field.spellcheck = false;
      field.setAttribute('aria-label', 'Program input');
      field.size = 12;

      const caret = document.createElement('span');
      caret.className = 'term-input-caret';
      caret.textContent = '\u2588';

      stream.appendChild(field);
      stream.appendChild(caret);

      const body = document.getElementById('terminal-body');
      body.scrollTop = body.scrollHeight;
      field.focus();

      let settled = false;

      const finish = (value) => {
        if (settled) return;
        settled = true;
        currentInputResolve = null;

        caret.remove();
        const echo = document.createElement('span');
        echo.className = 'term-stream-input';
        echo.textContent = (value === null ? '^C' : value) + '\n';
        stream.replaceChild(echo, field);
        body.scrollTop = body.scrollHeight;

        resolve(value);
      };

      field.addEventListener('input', () => {
        field.size = Math.max(12, field.value.length + 2);
      });

      field.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
          event.preventDefault();
          finish(field.value);
          return;
        }

        if (event.key === 'Escape' || (event.ctrlKey && event.key.toLowerCase() === 'c' && !field.value)) {
          event.preventDefault();
          finish(null);
        }
      });

      // Clicking anywhere in the terminal returns focus to the waiting line.
      body.addEventListener('click', () => { if (!settled) field.focus(); }, { once: false });

      currentInputResolve = finish;
    });
  }
  function submitInput() { const val = document.getElementById('dynamic-single-input').value; document.getElementById('program-input-modal').classList.remove('open'); if (currentInputResolve) { currentInputResolve(val); currentInputResolve = null; } }
  function cancelInput() { document.getElementById('program-input-modal').classList.remove('open'); if (currentInputResolve) { currentInputResolve(null); currentInputResolve = null; } }
  document.getElementById('dynamic-single-input').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); e.stopPropagation(); submitInput(); }
  });

  function createTerminalStream() {
    const body = document.getElementById('terminal-body');
    const stream = document.createElement('div');
    stream.className = 'term-stream';
    body.appendChild(stream);
    body.scrollTop = body.scrollHeight;
    return stream;
  }

  function termStreamWrite(stream, type, text) {
    if (!stream || text === null || text === undefined || text === '') return;
    const span = document.createElement('span');
    span.className = type === 'input' ? 'term-stream-input' : 'term-stream-output';
    span.textContent = String(text);
    stream.appendChild(span);
    const body = document.getElementById('terminal-body');
    body.scrollTop = body.scrollHeight;
  }

  function setRunButton(state) {
    const runBtn = document.getElementById('btn-run');
    if (!runBtn) return;

    const icons = {
      run: '<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg> Run',
      stop: '<svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><rect x="6" y="6" width="12" height="12" rx="1.5"/></svg> Stop',
    };

    runBtn.disabled = false;
    runBtn.classList.toggle('is-stopping', state !== 'run');
    runBtn.innerHTML = state === 'run' ? icons.run : icons.stop;
    runBtn.title = state === 'run' ? 'Run (Ctrl+Enter)' : 'Stop the running program (Esc)';
  }

  // One button, two jobs - the way an IDE does it.
  function runOrStop() {
    if (runInProgress) {
      stopRun();
      return;
    }

    run();
  }

  function stopRun() {
    if (!runInProgress) return;

    runStopRequested = true;

    // Release a line the program is waiting on, then drop the request itself.
    if (currentInputResolve) currentInputResolve(null);
    if (runAbortController) runAbortController.abort();
  }

  async function run() {
    if (runInProgress || activeTab === null || !cm) return;
    runInProgress = true;
    runStopRequested = false;
    setRunButton('stop');

    try {
      if (!await save()) return;
      const tab = openTabs.find(t => t.id === activeTab);
      if (!tab) return;

      const code = tab.content || "";
      const inputBridge = window.DataSenseiPythonInput;
      if (!inputBridge) {
        throw new Error('The Python input helper could not be loaded. Refresh the page and try again.');
      }

      const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal();
      termPrint('prompt', `$ python3 ${tab.name}`); setStatus('Running…');
      const terminalStream = createTerminalStream();
      const inputValues = [];
      let previousOutput = '';
      let res = null;

      while (true) {
        const stdin = inputBridge.buildStdin(inputValues);
        runAbortController = new AbortController();

        try {
          res = await api(
            `${NODES_URL}/${encodeURIComponent(tab.id)}/run`,
            'POST',
            { stdin, content: tab.content },
            { signal: runAbortController.signal }
          );
        } catch (error) {
          if (runStopRequested || error.name === 'AbortError') {
            termPrint('info', 'Run stopped.');
            setStatus('Stopped');
            return;
          }

          throw error;
        } finally {
          runAbortController = null;
        }

        const currentOutput = String(res.output || '');
        termStreamWrite(terminalStream, 'output', inputBridge.outputDelta(previousOutput, currentOutput));
        previousOutput = currentOutput;

        if (!res.input_required) break;

        // The sandbox reports how many answers it actually read. Zero, after we
        // sent some, is the one unambiguous sign that stdin is not reaching the
        // program; anything else (including a runner that reports no count at
        // all) is left alone so a working run is never stopped.
        if (inputBridge.progressStalled(res.inputs_consumed, inputValues.length)) {
          termPrint('error', 'Stopped: the sandbox received none of the values you typed. Rebuild the runner image with "docker build -t datasensei-python-runner:latest docker/python-runner" and try again.');
          setStatus('Input not delivered');
          return;
        }

        if (inputValues.length >= inputBridge.MAX_INTERACTIVE_INPUTS) {
          termPrint('error', `Run stopped after ${inputBridge.MAX_INTERACTIVE_INPUTS} input prompts. Check whether the program is asking for input forever.`);
          setStatus('Too many input prompts');
          return;
        }

        // stdout already carries the prompt the program printed, trailing space
        // and all. Compare both sides without trailing whitespace, or a prompt
        // ending in a space looks unprinted and gets written a second time.
        const promptText = inputBridge.promptLabel(res.input_prompt);
        const comparablePrompt = promptText.replace(/\s+$/, '');
        const comparableOutput = currentOutput.replace(/\s+$/, '');
        if (comparablePrompt !== '' && !comparableOutput.endsWith(comparablePrompt)) {
          termStreamWrite(terminalStream, 'output', promptText);
        }

        setStatus('Waiting for program input…');
        const value = await termReadLine(terminalStream);

        if (value === null) {
          termPrint('info', runStopRequested ? 'Run stopped.' : 'Run canceled.');
          setStatus(runStopRequested ? 'Stopped' : 'Run canceled');
          return;
        }

        if (!inputBridge.canAppend(inputValues, value)) {
          termPrint('error', `Run stopped because program input exceeds the ${inputBridge.MAX_STDIN_BYTES.toLocaleString()}-byte limit.`);
          setStatus('Input is too large');
          return;
        }

        inputValues.push(value);
        setStatus('Running…');
      }

      if (res.error) termPrint('error', res.error);
      if (res.plots && res.plots.length > 0) res.plots.forEach(b64 => termPrintImage(b64));
      if (!terminalStream.textContent && !res.error && (!res.plots || res.plots.length === 0)) termPrint('info', '(No output)');
      termPrint('info', `Exit: ${res.exit_code}  Time: ${res.execution_time_ms}ms`);
      setStatus(res.exit_code === 0 ? 'Finished' : `Error (exit ${res.exit_code})`);

      const reviewOutput = [
        res.output ? 'STDOUT:\n' + res.output : '',
        res.error ? 'STDERR:\n' + res.error : '',
        `Exit code: ${res.exit_code}`,
        `Execution time: ${res.execution_time_ms}ms`,
        res.plots && res.plots.length > 0 ? `Plots generated: ${res.plots.length}` : ''
      ].filter(Boolean).join('\n\n');
      ReviewBot.autoReview(code, 'python', tab.name, reviewOutput);
    } catch(e) { termPrint('error', 'Request failed: ' + e.message); setStatus('Run failed');
    } finally {
      runInProgress = false;
      runStopRequested = false;
      runAbortController = null;
      currentInputResolve = null;
      setRunButton('run');
    }
  }

  const PYTHON_SAMPLES = {
    simple: {
      filename: 'simple_code.py',
      title: 'Simple Code',
      code: String.raw`# Simple Code
# This program shows basic variables, computation, and output.

name = "DataSensei Learner"
age = 22
score = 95

print("Hello,", name)
print("Age:", age)
print("Score:", score)

if score >= 75:
    print("Result: Passed")
else:
    print("Result: Needs improvement")
`
    },
    input: {
      filename: 'input_code.py',
      title: 'Code with Input',
      code: String.raw`# Code with Input
# When you run this, DataSensei will ask for the input values.

name = input("Enter your name: ")
first_score = float(input("Enter first score: "))
second_score = float(input("Enter second score: "))

average = (first_score + second_score) / 2

print("Student:", name)
print("Average:", average)

if average >= 75:
    print("Remarks: Passed")
else:
    print("Remarks: Failed")
`
    },
    list: {
      filename: 'list_code.py',
      title: 'Code with Array/List',
      code: String.raw`# Code with Array/List
# Python uses lists to store multiple values in one variable.

scores = [88, 92, 75, 81, 95]

print("Scores:", scores)
print("Number of scores:", len(scores))
print("Highest score:", max(scores))
print("Lowest score:", min(scores))
print("Average score:", sum(scores) / len(scores))

print("Passed scores:")
for score in scores:
    if score >= 75:
        print(score)
`
    },
    matplot: {
      filename: 'matplotlib_chart.py',
      title: 'Code with Matplotlib',
      code: String.raw`# Code with Matplotlib
# This program creates a simple line chart.

import matplotlib.pyplot as plt

weeks = [1, 2, 3, 4, 5]
study_hours = [2, 3, 5, 4, 6]

plt.plot(weeks, study_hours, marker="o")
plt.title("Weekly Study Hours")
plt.xlabel("Week")
plt.ylabel("Hours")
plt.grid(True)
plt.show()
`
    },
    csv_matplot: {
      filename: 'csv_matplotlib_chart.py',
      title: 'CSV Data + Matplotlib',
      supportFiles: [
        {
          placeholder: 'CSV_FILENAME',
          filename: 'student_scores.csv',
          language: 'csv',
          content: String.raw`Student,Score
Ana,88
Ben,74
Carlo,91
Dina,83
Ella,96
`
        }
      ],
      code: String.raw`# CSV File with Data + Matplotlib
# This program reads a real CSV file from your project folder and visualizes it.

import csv
import matplotlib.pyplot as plt

filename = "__CSV_FILENAME__"

students = []
scores = []

with open(filename, "r", newline="") as file:
    reader = csv.DictReader(file)
    for row in reader:
        students.append(row["Student"])
        scores.append(int(row["Score"]))

print("CSV file:", filename)
print("Students:", students)
print("Scores:", scores)
print("Average score:", sum(scores) / len(scores))

plt.bar(students, scores)
plt.title("Student Scores from CSV")
plt.xlabel("Student")
plt.ylabel("Score")
plt.ylim(0, 100)
plt.show()
`
    }
  };

  function renderSampleCode(sample, supportFileNames = {}) {
    let code = sample.code || '';
    Object.entries(supportFileNames).forEach(([placeholder, filename]) => {
      const token = '__' + placeholder + '__';
      code = code.split(token).join(filename);
    });
    return code;
  }

  async function createSampleSupportFiles(sample, parentId = null) {
    const supportFileNames = {};
    const supportFiles = Array.isArray(sample.supportFiles) ? sample.supportFiles : [];

    for (const support of supportFiles) {
      const placeholder = support.placeholder || 'SUPPORT_FILENAME';
      let candidateName = nextAvailableName(support.filename, parentId);

      for (let attempt = 0; attempt < 5; attempt++) {
        try {
          const res = await api(NODES_URL, 'POST', {
            workspace_id: WORKSPACE_ID,
            parent_id: parentId,
            type: 'file',
            name: candidateName,
            content: support.content || '',
            language: support.language || 'text'
          });

          supportFileNames[placeholder] = res.node.name || candidateName;
          termPrint('info', `Created data file: ${res.node.name || candidateName}`);
          break;
        } catch (err) {
          if (err.collision) {
            candidateName = generateSuggestedName(candidateName);
            continue;
          }

          throw err;
        }
      }

      if (!supportFileNames[placeholder]) {
        throw new Error(`Could not generate a unique filename for ${support.filename}.`);
      }
    }

    return supportFileNames;
  }

  function parentIdOfActiveTab() {
    if (activeTab === null) return null;
    const node = findNode(treeData, activeTab);
    return node ? node.parent_id : null;
  }

  async function insertPythonSample(key) {
    if (!key || !PYTHON_SAMPLES[key] || !cm) return;

    const sample = PYTHON_SAMPLES[key];
    const parentId = parentIdOfActiveTab();
    let candidateName = nextAvailableName(sample.filename, parentId);

    try {
      // A generated sample always becomes its own Python file. This avoids
      // unexpectedly overwriting the currently open file and makes repeated
      // sample generation predictable.
      const supportFileNames = await createSampleSupportFiles(sample, parentId);
      const generatedCode = renderSampleCode(sample, supportFileNames);

      for (let attempt = 0; attempt < 10; attempt++) {
        try {
          const res = await api(NODES_URL, 'POST', {
            workspace_id: WORKSPACE_ID,
            parent_id: parentId,
            type: 'file',
            name: candidateName,
            content: generatedCode,
            language: 'python'
          });

          await refreshTree();
          openFile(res.node.id, res.node.name, res.node.content ?? generatedCode);
          setStatus(`${sample.title} generated`);
          termPrint('info', `${sample.title} generated as ${res.node.name || candidateName}. Press Run when ready.`);
          return;
        } catch (err) {
          if (err.collision) {
            candidateName = generateSuggestedName(candidateName);
            continue;
          }

          throw err;
        }
      }

      throw new Error('Could not generate a unique sample filename. Delete or rename an older sample and try again.');
    } catch (err) {
      termPrint('error', err.message || 'Could not generate the Python sample.');
      setStatus('Sample generation failed');
    }
  }

  let _modalCallback = null; let _modalParentId = null; let _modalType = null; let _renameId = null;
  function promptCreate(type, parentId) { _modalType = type; _modalParentId = parentId; _modalCallback = 'create'; document.getElementById('modal-title').textContent = type === 'file' ? 'New File' : 'New Folder'; document.getElementById('modal-input').value = type === 'file' ? 'untitled.py' : 'new-folder'; document.getElementById('modal-bg').classList.add('open'); setTimeout(() => { const inp = document.getElementById('modal-input'); inp.focus(); inp.select(); }, 50); }
  function promptRename(id, currentName) { _renameId = id; _modalCallback = 'rename'; document.getElementById('modal-title').textContent = 'Rename'; document.getElementById('modal-input').value = currentName; document.getElementById('modal-bg').classList.add('open'); setTimeout(() => { const inp = document.getElementById('modal-input'); inp.focus(); inp.select(); }, 50); }
  function closeModal() { document.getElementById('modal-bg').classList.remove('open'); _modalCallback = null; }
  async function confirmModal() { const val = document.getElementById('modal-input').value.trim(); if (!val) return; const callback = _modalCallback; const type = _modalType; const parentId = _modalParentId; const renameId = _renameId; closeModal(); if (callback === 'create') await createNode(type, parentId, val); else if (callback === 'rename') await renameNode(renameId, val); }
  async function createNode(type, parentId, name) { setStatus('Creating…'); try { const res = await api(NODES_URL, 'POST', { workspace_id: WORKSPACE_ID, parent_id: parentId, type, name, content: type === 'file' ? '' : null, language: 'python' }); await refreshTree(); setStatus('Created'); if (type === 'file') openFile(res.node.id, res.node.name, res.node.content ?? ''); } catch(e) { termPrint('error', e.message); setStatus('Error'); } }
  async function renameNode(id, name) { setStatus('Renaming…'); try { await api(`${NODES_URL}/${encodeURIComponent(id)}/rename`, 'PATCH', { name }); const tab = openTabs.find(t => t.id === id); if (tab) { tab.name = name; renderTabBar(); setText('breadcrumb-file', name); } await refreshTree(); setStatus('Renamed'); } catch(e) { termPrint('error', e.message); setStatus('Error'); } }
  function confirmDelete(id, name) { if (!confirm(`Delete "${name}"? This cannot be undone.`)) return; deleteNode(id); }
  async function deleteNode(id) { setStatus('Deleting…'); try { await api(`${NODES_URL}/${encodeURIComponent(id)}`, 'DELETE'); openTabs = openTabs.filter(t => t.id !== id); if (activeTab === id) { activeTab = openTabs.length ? openTabs[openTabs.length - 1].id : null; if (activeTab) loadTabContent(activeTab); else { document.getElementById('editor-empty').style.display = 'flex'; document.getElementById('cm-host').style.display = 'none'; } } renderTabBar(); await refreshTree(); setStatus('Deleted'); } catch(e) { termPrint('error', e.message); setStatus('Error'); } }
  async function refreshTree() { const res = await api(TREE_URL, 'GET'); treeData = res.tree; renderTree(); }
  function showCtxMenu(x, y, id, type, name) { const menu = document.getElementById('ctx-menu'); menu.innerHTML = ''; const items = type === 'folder' ? [{ label: 'New File', action: () => promptCreate('file', id) }, { label: 'New Folder', action: () => promptCreate('folder', id) }, { sep: true }, { label: 'Rename', action: () => promptRename(id, name) }, { label: 'Delete', action: () => confirmDelete(id, name), danger: true }] : [{ label: 'Open', action: () => { const node = findNode(treeData, id); if (node) openFile(node.id, node.name, node.content ?? ''); } }, { sep: true }, { label: 'Rename', action: () => promptRename(id, name) }, { label: 'Delete', action: () => confirmDelete(id, name), danger: true }]; items.forEach(item => { if (item.sep) { const sep = document.createElement('div'); sep.className = 'ctx-sep'; menu.appendChild(sep); } else { const el = document.createElement('div'); el.className = 'ctx-item' + (item.danger ? ' danger' : ''); el.textContent = item.label; el.addEventListener('click', () => { closeCtxMenu(); item.action(); }); menu.appendChild(el); } }); menu.style.left = x + 'px'; menu.style.top = y + 'px'; menu.classList.add('open'); setTimeout(() => document.addEventListener('click', closeCtxMenu, { once: true }), 10); }
  function closeCtxMenu() { document.getElementById('ctx-menu').classList.remove('open'); }
  function termPrint(type, text) { const body = document.getElementById('terminal-body'); const div = document.createElement('div'); div.className = `term-${type}`; if (type === 'prompt') div.innerHTML = `<span class="term-prompt">›</span> ${escHtml(text)}`; else div.textContent = text; body.appendChild(div); body.scrollTop = body.scrollHeight; const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal(); }
  function termPrintImage(base64) { const body = document.getElementById('terminal-body'); const wrapper = document.createElement('div'); wrapper.style.cssText = 'padding: 8px 0;'; const img = document.createElement('img'); img.src = 'data:image/png;base64,' + base64; img.style.cssText = 'max-width:100%; border-radius:6px; border:1px solid var(--border); display:block;'; wrapper.appendChild(img); body.appendChild(wrapper); body.scrollTop = body.scrollHeight; const panel = document.getElementById('bottom-panel'); if (panel.classList.contains('collapsed')) toggleTerminal(); }
  function clearTerminal() { setHtml('terminal-body', '<div class="term-info">Terminal cleared.</div>'); }
  function toggleTerminal() { const panel = document.getElementById('bottom-panel'); panel.classList.toggle('collapsed'); const icon = document.getElementById('term-toggle-icon'); icon.innerHTML = panel.classList.contains('collapsed') ? '<polyline points="6 15 12 9 18 15"/>' : '<polyline points="6 9 12 15 18 9"/>'; setTimeout(() => { if(cm) cm.refresh(); }, 250); }
  function setupResize() { const handle = document.getElementById('resize-handle'); const panel = document.getElementById('bottom-panel'); let dragging = false; let startY, startH; handle.addEventListener('mousedown', (e) => { dragging = true; startY = e.clientY; startH = panel.offsetHeight; document.body.style.cursor = 'ns-resize'; document.body.style.userSelect = 'none'; }); document.addEventListener('mousemove', (e) => { if (!dragging) return; const diff = startY - e.clientY; const newH = Math.max(34, Math.min(startH + diff, 500)); panel.style.height = newH + 'px'; }); document.addEventListener('mouseup', () => { dragging = false; document.body.style.cursor = ''; document.body.style.userSelect = ''; }); }
  function togglePanel(name) { if (name === 'explorer') { const el = document.getElementById('explorer-panel'); el.style.display = el.style.display === 'none' ? '' : 'none'; } }
  function collapseAll() { document.querySelectorAll('.tree-children').forEach(c => c.classList.remove('open')); document.querySelectorAll('.tree-row').forEach(r => r.classList.remove('open')); }
  function setupKeyboard() { document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeModal(); closeCtxMenu(); cancelInput(); return; } if (e.target.closest('input, textarea, select, [contenteditable="true"]')) return; if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); save(); } if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); run(); } }); document.getElementById('modal-input').addEventListener('keydown', (e) => { if (e.key === 'Enter') confirmModal(); }); }
  function setStatus(msg) { setText('status-msg', msg); }
  function updateStatusLang(name) { const ext = String(name || '').split('.').pop().toLowerCase(); const map = { py: 'Python 3', js: 'JavaScript', ts: 'TypeScript', md: 'Markdown', txt: 'Plain Text' }; setText('status-lang', map[ext] || 'Python 3'); }
  function focusSearch() { if (cm) cm.execCommand('find'); }

  // UPDATED API HELPER TO CATCH COLLISIONS
  async function api(url, method, body, extra = {}) {
      const opts = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } };
      if (method !== 'GET' && body !== undefined) opts.body = JSON.stringify(body);
      if (extra.signal) opts.signal = extra.signal;

      const res = await fetch(url, opts);
      let json = {};

      try {
          json = await res.json();
      } catch (_) {
          json = {};
      }

      if (!res.ok) {
          const validationMessage = json.errors ? Object.values(json.errors).flat()[0] : null;
          const err = new Error(json.error || validationMessage || json.message || 'Request failed');
          err.collision = Boolean(json.collision);
          err.errors = json.errors || {};
          throw err;
      }

      return json;
  }

  function findNode(nodes, id) { for (const n of nodes) { if (n.id === id) return n; if (n.children) { const found = findNode(n.children, id); if (found) return found; } } return null; }
  function escHtml(str) { return (str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

  return { init, save, run, runOrStop, stopRun, submitInput, cancelInput, promptCreate, promptRename, closeModal, confirmModal, clearTerminal, toggleTerminal, togglePanel, collapseAll, focusSearch, insertPythonSample };
})();

window.addEventListener('DOMContentLoaded', IDE.init);
</script>
</body>
</html>
