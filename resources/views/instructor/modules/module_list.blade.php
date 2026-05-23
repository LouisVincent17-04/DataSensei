<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Module Library — DataSensei</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg:           #0d1320;
            --surface:      #111c2d;
            --surface2:     #1a2638;
            --surface3:     #223149;
            --border:       #1e2f47;
            --border-hover: #2c4168;
            --accent:       #3b82f6;
            --accent-hover: #2563eb;
            --accent2:      #8b5cf6;
            --accent3:      #10b981;
            --accent4:      #f59e0b;
            --warn:         #ef4444;
            --text:         #fafafa;
            --muted:        #7f93b0;
            --dim:          #3d5272;
            --radius:       8px;
            --radius-sm:    6px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Layout ──────────────────────────────────────────────────────── */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

        .topbar {
            height: 64px;
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 32px;
            gap: 16px;
            flex-shrink: 0;
        }
        .topbar h1 { font-size: 1.125rem; font-weight: 700; flex: 1; letter-spacing: -0.01em; }

        .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 20px; }

        /* ── Page Header ─────────────────────────────────────────────────── */
        .page-header {
            display: flex; align-items: flex-end; justify-content: space-between;
            gap: 16px; flex-wrap: wrap;
        }
        .page-kicker {
            font-size: .6875rem; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: var(--accent); margin-bottom: 6px;
        }
        .page-header h2 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
        .page-header p  { font-size: .875rem; color: var(--muted); margin-top: 4px; max-width: 600px; line-height: 1.6; }
        .header-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

        /* ── Flash ───────────────────────────────────────────────────────── */
        .flash {
            padding: 13px 16px; border-radius: var(--radius-sm);
            font-size: .875rem; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
        }
        .flash-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25); color: var(--accent3); }
        .flash-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.25);  color: var(--warn); }
        #jsError { display: none; }

        /* ── Two-column layout ───────────────────────────────────────────── */
        .layout-grid {
            display: grid;
            grid-template-columns: 288px 1fr;
            gap: 16px;
            align-items: start;
        }

        /* ── Sidebar Panel ───────────────────────────────────────────────── */
        .sidebar-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: visible;
            position: sticky;
            top: 0;
        }
        .sidebar-header {
            padding: 16px 18px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-header h3 { font-size: .9375rem; font-weight: 700; letter-spacing: -0.01em; }
        .sidebar-header p  { font-size: .75rem; color: var(--muted); margin-top: 3px; line-height: 1.5; }
        .sidebar-body { padding: 16px; display: flex; flex-direction: column; gap: 14px; }

        .field { display: flex; flex-direction: column; gap: 6px; }
        .field-label {
            font-size: .6875rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--muted);
        }

        /* ── Custom Class Dropdown ───────────────────────────────────────── */
        /* Replaces native <select> to fix text overflow and enable auto-select */
        .csd-wrap { position: relative; z-index: 100; }

        .csd-trigger {
            width: 100%;
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: var(--radius-sm); cursor: pointer;
            font-family: inherit; font-size: .875rem; color: var(--muted);
            transition: border-color .15s;
            text-align: left;
        }
        .csd-trigger:hover, .csd-wrap.open .csd-trigger { border-color: var(--accent); color: var(--text); }
        .csd-trigger-label { flex: 1; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
        .csd-trigger-selected { color: var(--text); }
        .csd-chevron { flex-shrink: 0; color: var(--muted); transition: transform .2s; }
        .csd-wrap.open .csd-chevron { transform: rotate(180deg); }

        .csd-menu {
            display: none;
            position: absolute; top: calc(100% + 6px); left: 0; right: 0;
            background: var(--surface); border: 1px solid var(--border-hover);
            border-radius: var(--radius-sm);
            box-shadow: 0 12px 32px rgba(0,0,0,.5);
            z-index: 200; overflow: hidden;
        }
        .csd-wrap.open .csd-menu { display: flex; flex-direction: column; }

        .csd-search-wrap {
            padding: 10px;
            border-bottom: 1px solid var(--border);
        }
        .csd-search {
            width: 100%; padding: 7px 10px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: var(--radius-sm); color: var(--text);
            font: inherit; font-size: .8125rem; outline: none;
        }
        .csd-search:focus { border-color: var(--accent); }
        .csd-search::placeholder { color: var(--dim); }

        .csd-list { max-height: 260px; overflow-y: auto; }

        .csd-option {
            padding: 10px 14px; cursor: pointer;
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        .csd-option:last-child { border-bottom: none; }
        .csd-option:hover { background: var(--surface2); }
        .csd-option.selected { background: rgba(59,130,246,.1); }
        .csd-option-name {
            font-size: .875rem; font-weight: 600; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .csd-option-meta {
            font-size: .6875rem; color: var(--muted); margin-top: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .csd-empty { padding: 16px 14px; font-size: .8125rem; color: var(--dim); text-align: center; }
        /* Actual hidden input submitted with the form */

        /* ── Search Input ────────────────────────────────────────────────── */
        .search-box {
            display: flex; align-items: center; gap: 10px;
            background: var(--bg); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 9px 12px;
            transition: border-color .15s;
        }
        .search-box:focus-within { border-color: var(--accent); }
        .search-box input {
            background: none; border: none; outline: none;
            color: var(--text); font-size: .875rem; font-family: inherit; width: 100%;
        }
        .search-box input::placeholder { color: var(--dim); }

        /* ── Stats Cards ─────────────────────────────────────────────────── */
        .stats-grid { display: grid; gap: 8px; }
        .stat-card {
            background: var(--bg); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 12px 14px;
            display: flex; align-items: center; gap: 12px;
        }
        .stat-icon {
            width: 32px; height: 32px; border-radius: var(--radius-sm);
            background: var(--surface2); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .stat-info { flex: 1; min-width: 0; }
        .stat-value { font-size: 1.125rem; font-weight: 800; line-height: 1; letter-spacing: -0.02em; }
        .stat-label { font-size: .6875rem; color: var(--muted); margin-top: 3px; font-weight: 500; }

        /* ── Main Panel ──────────────────────────────────────────────────── */
        .main-panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex; flex-direction: column;
        }
        .main-panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        .main-panel-header h3 { font-size: .9375rem; font-weight: 700; letter-spacing: -0.01em; }
        .main-panel-header p  { font-size: .75rem; color: var(--muted); margin-top: 3px; line-height: 1.5; }

        /* ── Year Filter Tabs (matches classes.blade filter-tabs style) ───── */
        .year-tabs-wrap {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; border-bottom: 1px solid var(--border);
            flex-wrap: wrap;
        }
        .year-tabs-label { font-size: .6875rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .07em; flex-shrink: 0; }
        .year-tabs {
            display: flex; background: var(--surface2); border: 1px solid var(--border);
            border-radius: var(--radius-sm); overflow: hidden;
        }
        .year-tab {
            padding: 7px 14px; font-size: .8125rem; font-weight: 600;
            cursor: pointer; color: var(--muted); border: none; background: none;
            font-family: inherit; transition: all .15s; white-space: nowrap;
        }
        .year-tab.active { background: var(--surface3); color: var(--text); }
        .year-tab:hover:not(.active) { color: var(--text); }

        /* ── Module Grid ─────────────────────────────────────────────────── */
        .modules-body { padding: 20px; flex: 1; overflow-y: auto; }

        .year-section { margin-bottom: 28px; }
        .year-section:last-child { margin-bottom: 0; }

        .year-heading {
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; margin-bottom: 12px;
        }
        .year-heading h4 { font-size: .9375rem; font-weight: 700; letter-spacing: -0.01em; }
        .year-count {
            font-size: .6875rem; font-weight: 700; padding: 2px 8px;
            border-radius: 999px; background: var(--surface3);
            border: 1px solid var(--border); color: var(--muted); white-space: nowrap;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 12px;
        }

        /* ── Module Card ─────────────────────────────────────────────────── */
        .module-card {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 15px;
            transition: border-color .15s, transform .15s, box-shadow .15s;
            position: relative;
        }
        .module-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0,0,0,.25);
        }

        .module-card-top {
            display: flex; align-items: flex-start; gap: 11px; margin-bottom: 10px;
        }
        .module-badge {
            width: 40px; height: 40px; border-radius: var(--radius-sm);
            background: var(--surface2); border: 1px solid var(--border);
            color: var(--accent); display: flex; align-items: center; justify-content: center;
            font-size: .6875rem; font-weight: 800; flex-shrink: 0; letter-spacing: .02em;
        }
        .module-info { flex: 1; min-width: 0; }
        .module-title { font-size: .9375rem; font-weight: 700; line-height: 1.4; letter-spacing: -0.01em; }
        .module-year-pill {
            display: inline-flex; margin-top: 4px;
            color: var(--muted); background: var(--surface2); border: 1px solid var(--border);
            border-radius: 999px; padding: 2px 8px; font-size: .6875rem; font-weight: 600;
        }
        .module-note { font-size: .75rem; color: var(--muted); line-height: 1.5; margin-bottom: 12px; }

        /* ── Version Options ─────────────────────────────────────────────── */
        .version-list { display: flex; flex-direction: column; gap: 8px; }

        .version-option {
            display: grid; grid-template-columns: auto 1fr;
            gap: 10px; align-items: flex-start;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 10px 12px;
            cursor: pointer; transition: border-color .15s, background .15s;
        }
        .version-option:hover   { border-color: var(--border-hover); background: var(--surface3); }
        .version-option.selected { border-color: var(--accent); background: rgba(59,130,246,.1); }
        .version-option input   { margin-top: 2px; accent-color: var(--accent); flex-shrink: 0; }

        .version-name { display: block; font-size: .8125rem; font-weight: 600; line-height: 1.4; }
        .version-code {
            display: inline-block; margin-top: 2px;
            color: var(--accent); font-size: .625rem; font-weight: 700; letter-spacing: .04em;
        }
        .version-desc { margin-top: 5px; color: var(--muted); font-size: .75rem; line-height: 1.5; }
        .version-tags { display: flex; gap: 5px; flex-wrap: wrap; margin-top: 7px; }
        .vtag {
            font-size: .625rem; font-weight: 700; padding: 2px 7px; border-radius: 999px;
            background: var(--bg); border: 1px solid var(--border); color: var(--muted);
            text-transform: uppercase; letter-spacing: .04em;
        }
        .vtag-green { color: var(--accent3); }
        .vtag-amber { color: var(--accent4); }

        /* ── Footer Bar ──────────────────────────────────────────────────── */
        .footer-bar {
            position: sticky; bottom: 0;
            background: rgba(17,28,45,.97);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            padding: 13px 20px;
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; flex-wrap: wrap;
        }
        .selected-summary { font-size: .8125rem; color: var(--muted); }
        .selected-summary strong { color: var(--text); font-weight: 700; }

        /* ── Buttons (aligned with classes.blade) ────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            gap: 7px; padding: 9px 16px; border-radius: var(--radius-sm);
            font-size: .875rem; font-weight: 600; cursor: pointer;
            border: 1px solid transparent; transition: all .15s;
            font-family: inherit; text-decoration: none; white-space: nowrap;
        }
        .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
        .btn-accent:hover:not(:disabled)  { background: var(--accent-hover); border-color: var(--accent-hover); }
        .btn-accent:disabled { opacity: .55; cursor: not-allowed; }
        .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--border); }
        .btn-ghost:hover   { background: var(--border); }
        .btn-sm { padding: 6px 12px; font-size: .8125rem; }

        /* ── Scrollbar ───────────────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

        /* ── Responsive ──────────────────────────────────────────────────── */
        @media (max-width: 1100px) {
            .layout-grid { grid-template-columns: 1fr; }
            .sidebar-panel { position: static; }
        }
        @media (max-width: 640px) {
            .content { padding: 20px; }
            .module-grid { grid-template-columns: 1fr; }
            .footer-bar { flex-direction: column; align-items: stretch; }
            .footer-bar .btn { width: 100%; }
            .year-tabs-wrap { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
    @include('partials.instructor-sidebar')

    <div class="main">
        <header class="topbar">
            <h1>Module Library</h1>
        </header>

        <main class="content">

            {{-- Page Header --}}
            <div class="page-header">
                <div>
                    <div class="page-kicker">DataSensei · Instructor Tools</div>
                    <h2>Module Library</h2>
                    <p>Browse all Data Science modules and assign the right versions to your class. Modules are reading and MCQ-based — no compiler execution.</p>
                </div>
                <div class="header-actions">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="clearAllSelections()">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12"/>
                        </svg>
                        Clear Selection
                    </button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="selectVisibleV1()">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                        </svg>
                        Select All V1 (Visible)
                    </button>
                </div>
            </div>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="flash flash-success">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flash flash-error">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="flash flash-error">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif
            {{-- JS inline validation error (replaces alert()) --}}
            <div id="jsError" class="flash flash-error">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span id="jsErrorText"></span>
            </div>

            <form method="POST" action="{{ route('modules.module-library.assign') }}" id="moduleAssignForm">
                @csrf

                {{-- Hidden input submitted by custom class dropdown --}}
                <input type="hidden" name="class_id" id="class_id_input" />

                <div class="layout-grid">

                    {{-- ── Sidebar ──────────────────────────────────────── --}}
                    <aside class="sidebar-panel">
                        <div class="sidebar-header">
                            <h3>Assign to Class</h3>
                            <p>Choose a class, then pick a module version or leave modules on <em>Do not include</em>.</p>
                        </div>

                        <div class="sidebar-body">

                            {{-- Custom Class Dropdown --}}
                            <div class="field">
                                <label class="field-label">Class</label>

                                {{--
                                    Each option carries data-assigned = JSON array of module_library_item_id values
                                    already assigned to that class. JS reads this on selection to auto-check radios.
                                    Requires the controller to eager-load assignedModules (see ModuleLibraryController).
                                --}}
                                <div class="csd-wrap" id="classDropdown">
                                    <button type="button" class="csd-trigger" id="csdTrigger" aria-haspopup="listbox" aria-expanded="false">
                                        <span class="csd-trigger-label" id="csdLabel">Select a class…</span>
                                        <svg class="csd-chevron" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </button>

                                    <div class="csd-menu" role="listbox" id="csdMenu">
                                        <div class="csd-search-wrap">
                                            <input
                                                type="text"
                                                class="csd-search"
                                                id="csdSearch"
                                                placeholder="Search classes…"
                                                autocomplete="off"
                                            />
                                        </div>
                                        <div class="csd-list" id="csdList">
                                            @forelse ($classes as $class)
                                                <div
                                                    class="csd-option"
                                                    role="option"
                                                    data-value="{{ $class->id }}"
                                                    data-assigned="{{ $class->assignedModules->pluck('module_library_item_id')->toJson() }}"
                                                    data-name="{{ $class->name }}"
                                                    data-label="{{ $class->name }}{{ $class->section ? ' — '.$class->section : '' }} ({{ $class->class_code }})"
                                                >
                                                    <div class="csd-option-name">{{ $class->name }}</div>
                                                    <div class="csd-option-meta">
                                                        @if($class->section){{ $class->section }} · @endif
                                                        {{ $class->subject_code ?? '' }}
                                                        @if($class->subject_code) · @endif
                                                        <span style="font-family: monospace; letter-spacing:.04em;">{{ $class->class_code }}</span>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="csd-empty">No active classes found. Create a class first.</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Module Search --}}
                            <div class="field">
                                <label class="field-label" for="moduleSearch">Search Modules</label>
                                <div class="search-box">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:var(--dim); flex-shrink:0;">
                                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                    <input type="text" id="moduleSearch" placeholder="Filter by module title…" oninput="filterModules()" autocomplete="off" />
                                </div>
                            </div>

                            {{-- Stats --}}
                            <div class="stats-grid">
                                <div class="stat-card">
                                    <div class="stat-icon" style="color:var(--accent)">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                                            <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                                        </svg>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value">{{ $totalModuleTitles }}</div>
                                        <div class="stat-label">Module Titles</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="color:var(--accent2)">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                                        </svg>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value">{{ $totalModuleVersions }}</div>
                                        <div class="stat-label">Available Versions</div>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon" style="color:var(--accent3)">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value" id="selectedCountSide">0</div>
                                        <div class="stat-label">Selected</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    {{-- ── Main Module Panel ────────────────────────────── --}}
                    <section class="main-panel">
                        <div class="main-panel-header">
                            <h3>Available Modules</h3>
                            <p>Each module title may have multiple versions. Modules left on <em>Do not include</em> are skipped.</p>
                        </div>

                        {{-- Year Filter Tabs (segmented, matches classes.blade filter-tabs) --}}
                        <div class="year-tabs-wrap">
                            <span class="year-tabs-label">Year</span>
                            <div class="year-tabs">
                                <button type="button" class="year-tab active" data-year="All"    onclick="setYear('All')">All</button>
                                <button type="button" class="year-tab"        data-year="Year 1" onclick="setYear('Year 1')">Year 1</button>
                                <button type="button" class="year-tab"        data-year="Year 2" onclick="setYear('Year 2')">Year 2</button>
                                <button type="button" class="year-tab"        data-year="Year 3" onclick="setYear('Year 3')">Year 3</button>
                                <button type="button" class="year-tab"        data-year="Year 4" onclick="setYear('Year 4')">Year 4</button>
                            </div>
                        </div>

                        {{-- Modules --}}
                        <div class="modules-body">
                            @foreach ($modulesByYear as $year => $moduleGroups)
                                <div class="year-section" data-section-year="{{ $year }}">
                                    <div class="year-heading">
                                        <h4>{{ $year }}</h4>
                                        <span class="year-count">{{ $moduleGroups->count() }} modules</span>
                                    </div>

                                    <div class="module-grid">
                                        @foreach ($moduleGroups as $moduleNo => $versions)
                                            @php $first = $versions->first(); @endphp

                                            <article
                                                class="module-card"
                                                data-title="{{ strtolower($first->title) }}"
                                                data-year="{{ $first->year_level }}"
                                                data-module-no="{{ $first->module_no }}"
                                            >
                                                <div class="module-card-top">
                                                    <div class="module-badge">M{{ $first->module_no }}</div>
                                                    <div class="module-info">
                                                        <div class="module-title">{{ $first->title }}</div>
                                                        <span class="module-year-pill">{{ $first->year_level }}</span>
                                                    </div>
                                                </div>

                                                <p class="module-note">Select a version to include, or leave as <em>Do not include</em> to skip.</p>

                                                <div class="version-list">
                                                    {{-- Skip option --}}
                                                    <label class="version-option none-option">
                                                        <input type="radio"
                                                               name="selected_modules[{{ $first->module_no }}]"
                                                               value=""
                                                               checked
                                                               onchange="updateCount()">
                                                        <div>
                                                            <span class="version-name">Do not include</span>
                                                            <span class="version-code">SKIP</span>
                                                            <p class="version-desc">Leave this module out of the class assignment.</p>
                                                        </div>
                                                    </label>

                                                    @foreach ($versions as $module)
                                                        <label class="version-option">
                                                            <input type="radio"
                                                                   name="selected_modules[{{ $module->module_no }}]"
                                                                   value="{{ $module->id }}"
                                                                   data-module-id="{{ $module->id }}"
                                                                   onchange="updateCount()">
                                                            <div>
                                                                <span class="version-name">{{ $module->version_name }}</span>
                                                                <span class="version-code">{{ $module->version_code }}</span>
                                                                <p class="version-desc">{{ $module->description }}</p>
                                                                <div class="version-tags">
                                                                    <span class="vtag vtag-green">MCQ based</span>
                                                                    <span class="vtag vtag-amber">Snippets only</span>
                                                                    <span class="vtag">No compiler</span>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer Bar --}}
                        <div class="footer-bar">
                            <div class="selected-summary">
                                <strong id="selectedCount">0</strong> module version(s) selected
                            </div>
                            <button type="submit" class="btn btn-accent" id="submitBtn">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Add to Class
                            </button>
                        </div>
                    </section>

                </div>
            </form>
        </main>
    </div>

    <script>
    /* ── State ─────────────────────────────────────────────────────────────── */
    let activeYear = 'All';

    /* ── Inline error helper ────────────────────────────────────────────────── */
    function showError(msg) {
        const el = document.getElementById('jsError');
        if (msg) {
            document.getElementById('jsErrorText').textContent = msg;
            el.style.display = 'flex';
            el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            el.style.display = 'none';
        }
    }

    /* ── Selection counter & highlight ─────────────────────────────────────── */
    function updateCount() {
        let count = 0;
        document.querySelectorAll('.version-option').forEach(o => o.classList.remove('selected'));

        document.querySelectorAll('input[type="radio"][name^="selected_modules"]:checked').forEach(r => {
            if (r.value !== '') {
                count++;
                r.closest('.version-option').classList.add('selected');
            }
        });

        document.getElementById('selectedCount').textContent     = count;
        document.getElementById('selectedCountSide').textContent = count;
        if (count > 0) showError(null);
    }

    /* ── Year filter ────────────────────────────────────────────────────────── */
    function setYear(year) {
        activeYear = year;
        document.querySelectorAll('.year-tab').forEach(t =>
            t.classList.toggle('active', t.dataset.year === year));
        filterModules();
    }

    function filterModules() {
        const search = document.getElementById('moduleSearch').value.toLowerCase().trim();

        document.querySelectorAll('.module-card').forEach(card => {
            const matchTitle = card.dataset.title.includes(search);
            const matchYear  = activeYear === 'All' || card.dataset.year === activeYear;
            card.style.display = (matchTitle && matchYear) ? '' : 'none';
        });

        document.querySelectorAll('.year-section').forEach(sec => {
            const hasVisible = Array.from(sec.querySelectorAll('.module-card'))
                .some(c => c.style.display !== 'none');
            sec.style.display = hasVisible ? '' : 'none';
        });
    }

    /* ── Bulk helpers ───────────────────────────────────────────────────────── */
    function clearAllSelections() {
        document.querySelectorAll('.none-option input[type="radio"]').forEach(r => r.checked = true);
        updateCount();
    }

    function selectVisibleV1() {
        document.querySelectorAll('.module-card').forEach(card => {
            if (card.style.display === 'none') return;
            const firstReal = card.querySelector('.version-option:not(.none-option) input[type="radio"]');
            if (firstReal) firstReal.checked = true;
        });
        updateCount();
    }

    /* ── Auto-select assigned modules when a class is chosen ────────────────── */
    function applyAssignedModules(assignedIds) {
        // Start from a clean slate
        clearAllSelections();

        if (!assignedIds || assignedIds.length === 0) return;

        const idSet = new Set(assignedIds.map(Number));

        document.querySelectorAll('input[type="radio"][data-module-id]').forEach(radio => {
            if (idSet.has(Number(radio.dataset.moduleId))) {
                radio.checked = true;
            }
        });

        updateCount();
    }

    /* ── Custom class dropdown ──────────────────────────────────────────────── */
    (function () {
        const wrap      = document.getElementById('classDropdown');
        const trigger   = document.getElementById('csdTrigger');
        const menu      = document.getElementById('csdMenu');
        const label     = document.getElementById('csdLabel');
        const searchEl  = document.getElementById('csdSearch');
        const listEl    = document.getElementById('csdList');
        const hiddenIn  = document.getElementById('class_id_input');

        function open() {
            wrap.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
            searchEl.value = '';
            filterOptions('');
            setTimeout(() => searchEl.focus(), 50);
        }

        function close() {
            wrap.classList.remove('open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        function filterOptions(q) {
            const lower = q.toLowerCase();
            let anyVisible = false;
            listEl.querySelectorAll('.csd-option').forEach(opt => {
                const match = opt.dataset.name.toLowerCase().includes(lower);
                opt.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });
            // Show/hide empty message
            let emptyMsg = listEl.querySelector('.csd-empty-dyn');
            if (!anyVisible) {
                if (!emptyMsg) {
                    emptyMsg = document.createElement('div');
                    emptyMsg.className = 'csd-empty csd-empty-dyn';
                    emptyMsg.textContent = 'No classes match your search.';
                    listEl.appendChild(emptyMsg);
                }
            } else if (emptyMsg) {
                emptyMsg.remove();
            }
        }

        function select(opt) {
            const val      = opt.dataset.value;
            const lbl      = opt.dataset.label;
            const assigned = JSON.parse(opt.dataset.assigned || '[]');

            hiddenIn.value = val;
            label.textContent = lbl;
            label.classList.add('csd-trigger-selected');

            // Highlight selected option
            listEl.querySelectorAll('.csd-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');

            close();

            // Auto-select already-assigned modules for this class
            applyAssignedModules(assigned);
        }

        trigger.addEventListener('click', e => {
            e.stopPropagation();
            wrap.classList.contains('open') ? close() : open();
        });

        searchEl.addEventListener('input', () => filterOptions(searchEl.value));

        listEl.addEventListener('click', e => {
            const opt = e.target.closest('.csd-option');
            if (opt) select(opt);
        });

        // Close on outside click
        document.addEventListener('click', e => {
            if (!wrap.contains(e.target)) close();
        });

        // Keyboard nav
        trigger.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
            if (e.key === 'Escape') close();
        });
    })();

    /* ── Form submit validation ─────────────────────────────────────────────── */
    document.getElementById('moduleAssignForm').addEventListener('submit', function (e) {
        const classId  = document.getElementById('class_id_input').value;
        const selected = Array.from(
            document.querySelectorAll('input[type="radio"][name^="selected_modules"]:checked')
        ).filter(r => r.value !== '');

        if (!classId) {
            e.preventDefault();
            showError('Please select a class before submitting.');
            document.getElementById('csdTrigger').focus();
            return;
        }

        if (selected.length === 0) {
            e.preventDefault();
            showError('Please select at least one module version before submitting.');
            return;
        }

        // Disable to prevent double-submit
        const btn = document.getElementById('submitBtn');
        btn.disabled    = true;
        btn.innerHTML   = '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" opacity=".4"/></svg> Assigning…';
    });

    /* ── Init ───────────────────────────────────────────────────────────────── */
    updateCount();
    </script>
</body>
</html>