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
            --accent3:      #10b981; /* Green for Run */
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

        /* ── Reset & Base ─────────────────────────────────────────────────── */
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

        /* ── Layout ────────────────────────────────────────────────────────── */
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

        /* Editor Section */
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

        .run-bar { display: flex; align-items: center; gap: 8px; padding: 8px 0 10px; }
        .tb-btn { display: flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: var(--radius); border: 1px solid var(--border); background: transparent; color: var(--muted); font-size: 0.75rem; cursor: pointer; transition: all 0.15s; font-weight: 500; }
        .tb-btn:hover { background: var(--surface2); color: var(--text); border-color: var(--border-hover); }
        .tb-btn.run { background: var(--accent3); color: #fff; border-color: var(--accent3); font-weight: 600; }
        .tb-btn.run:hover { background: #0ea472; }
        .tb-btn.run:disabled { opacity: 0.6; cursor: not-allowed; }
        
        .shortcut { font-size: 10px; color: var(--dim); margin-left: auto; }
        kbd { background: var(--surface2); border: 1px solid var(--border); padding: 1px 4px; border-radius: 3px; }

        /* Results Section */
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
        
        /* Toast */
        .refresh-btn { width: 22px; height: 22px; border: none; background: transparent; color: var(--muted); border-radius: 4px; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: color .15s, background .15s; }
        .refresh-btn:hover { background: var(--border); color: var(--text); }
        .refresh-btn.spinning svg { animation: spin 0.6s linear infinite; }

        .drop-toast { position: fixed; bottom: 20px; right: 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 16px; z-index: 99; box-shadow: 0 10px 25px rgba(0,0,0,0.5); display: none; }
        .drop-toast.show { display: block; }
        .drop-toast-title { color: var(--warn); font-weight: 700; font-size: 0.85rem; margin-bottom: 4px; }
        .drop-toast-desc { font-size: 0.75rem; color: var(--muted); margin-bottom: 12px; }
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
    //
    // Centralises the "is the response actually JSON?" check so every fetch
    // call benefits from the same defence against HTML redirects (419, 302).
    // Returns { ok, status, data } — never throws on HTTP errors.
    //
    async function apiFetch(url, options = {}) {
        const defaults = {
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept':       'application/json',
            },
        };

        // Merge caller headers on top of defaults
        const mergedOptions = {
            ...defaults,
            ...options,
            headers: { ...defaults.headers, ...(options.headers || {}) },
        };

        const res = await fetch(url, mergedOptions);

        const contentType = res.headers.get('content-type') || '';
        const isJson      = contentType.includes('application/json');

        // Non-JSON response — translate the HTTP status into a readable message
        if (!isJson) {
            let message;
            if (res.status === 419) {
                message = '419 — Session expired. Please refresh the page and try again.';
            } else if (res.status === 401) {
                message = '401 — Unauthenticated. Please log in again.';
            } else if (res.status === 403) {
                message = '403 — Forbidden. You do not have permission to do that.';
            } else if (res.redirected || res.status === 302) {
                // fetch() follows redirects transparently; if we land on a
                // non-JSON page it almost always means a login redirect.
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
                // DELETE has no body so drop Content-Type to avoid Laravel
                // treating it as a malformed request
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