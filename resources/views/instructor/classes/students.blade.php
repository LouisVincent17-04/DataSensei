{{-- resources/views/instructor/classes/students.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $class->name }} — Students · DataSensei</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
      --surface3:    #223149;
      --border:      #1e2f47;
      --border-hover:#2c4168;
      --accent:      #3b82f6;
      --accent-hover:#2563eb;
      --accent2:     #8b5cf6;
      --accent3:     #10b981;
      --accent4:     #f59e0b;
      --warn:        #ef4444;
      --text:        #fafafa;
      --muted:       #7f93b0;
      --dim:         #3d5272;
      --radius:      8px;
      --radius-sm:   6px;
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
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    /* ── Topbar ───────────────────────────────────────── */
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
    .topbar-back {
      display: flex; align-items: center; gap: 8px;
      color: var(--muted); font-size: .875rem; font-weight: 600;
      text-decoration: none; transition: color .15s;
    }
    .topbar-back:hover { color: var(--text); }
    .topbar-title {
      flex: 1;
      display: flex; align-items: center; gap: 10px;
    }
    .topbar-title h1 { font-size: 1.125rem; font-weight: 700; letter-spacing: -0.01em; }
    .topbar-title .class-code-pill {
      font-size: .65rem; font-weight: 700; padding: 3px 9px;
      border-radius: 999px; background: var(--surface3);
      border: 1px solid var(--border); color: var(--muted);
      letter-spacing: .06em;
    }
    .topbar-search {
      display: flex; align-items: center;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 8px 12px; gap: 10px; width: 260px;
      transition: border-color .15s;
    }
    .topbar-search:focus-within { border-color: var(--accent); }
    .topbar-search input {
      background: none; border: none; outline: none;
      color: var(--text); font-size: .875rem; font-family: inherit; width: 100%;
    }
    .topbar-search input::placeholder { color: var(--dim); }
    .topbar-btn {
      width: 36px; height: 36px; background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--muted); transition: all .15s; text-decoration: none;
    }
    .topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }

    /* ── Content ──────────────────────────────────────── */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }

    /* ── Flash ────────────────────────────────────────── */
    .flash {
      padding: 14px 18px; border-radius: var(--radius-sm);
      font-size: .875rem; font-weight: 500; display: flex; align-items: center; gap: 10px;
    }
    .flash-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.25);  color: var(--warn); }

    /* ── Page Header ──────────────────────────────────── */
    .page-header {
      display: flex; align-items: flex-end; justify-content: space-between;
      gap: 16px; flex-wrap: wrap;
    }
    .page-header h2 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
    .page-header p  { font-size: .875rem; color: var(--muted); margin-top: 4px; }

    /* ── Stats Strip ──────────────────────────────────── */
    .stats-strip { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
    .strip-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 18px 20px;
      display: flex; align-items: center; gap: 16px;
    }
    .strip-icon {
      width: 40px; height: 40px; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .strip-info { flex: 1; }
    .strip-value { font-size: 1.5rem; font-weight: 800; line-height: 1; letter-spacing: -0.03em; }
    .strip-label { font-size: .75rem; color: var(--muted); margin-top: 4px; }

    /* ── Toolbar ──────────────────────────────────────── */
    .toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .search-box {
      display: flex; align-items: center; gap: 10px;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 9px 14px;
      transition: border-color .15s; flex: 1; min-width: 220px; max-width: 360px;
    }
    .search-box:focus-within { border-color: var(--accent); }
    .search-box input {
      background: none; border: none; outline: none;
      color: var(--text); font-size: .875rem; font-family: inherit; width: 100%;
    }
    .search-box input::placeholder { color: var(--dim); }

    .filter-tabs { display: flex; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
    .filter-tab {
      padding: 8px 16px; font-size: .8125rem; font-weight: 600;
      cursor: pointer; color: var(--muted); border: none; background: none;
      font-family: inherit; transition: all .15s; text-decoration: none;
      display: flex; align-items: center; gap: 6px;
    }
    .filter-tab.active { background: var(--surface3); color: var(--text); }
    .filter-tab:hover:not(.active) { color: var(--text); }
    .tab-count {
      font-size: .68rem; font-weight: 700; padding: 2px 6px; border-radius: 999px;
      background: var(--surface3);
    }
    .filter-tab.active .tab-count { background: var(--accent); color: #fff; }
    .tab-count-warn { background: rgba(239,68,68,.15); color: var(--warn); }

    /* ── Students Table ───────────────────────────────── */
    .table-wrap {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); overflow: hidden;
    }
    table {
      width: 100%; border-collapse: collapse;
    }
    thead tr {
      border-bottom: 1px solid var(--border);
    }
    thead th {
      padding: 12px 20px;
      font-size: .72rem; font-weight: 800; color: var(--dim);
      letter-spacing: .06em; text-transform: uppercase;
      text-align: left; white-space: nowrap;
    }
    thead th.col-check { width: 44px; padding-right: 0; }
    thead th.col-action { text-align: right; }

    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background .12s;
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: var(--surface2); }

    td {
      padding: 14px 20px;
      font-size: .875rem; vertical-align: middle;
    }
    td.col-check { padding-right: 0; }
    td.col-action { text-align: right; }

    /* Student identity cell */
    .student-cell { display: flex; align-items: center; gap: 12px; }
    .student-avatar {
      width: 36px; height: 36px; border-radius: var(--radius-sm);
      background: var(--surface3); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: .8rem; font-weight: 800; color: var(--text);
      flex-shrink: 0;
    }
    .student-name { font-weight: 600; color: var(--text); }
    .student-email { font-size: .75rem; color: var(--muted); margin-top: 2px; }

    /* Status pill */
    .pill {
      font-size: .68rem; font-weight: 700; padding: 3px 8px;
      border-radius: 999px; letter-spacing: .04em; text-transform: uppercase;
      display: inline-flex; align-items: center; gap: 4px;
    }
    .pill-ok      { background: rgba(16,185,129,.1);  color: var(--accent3); }
    .pill-warning { background: rgba(245,158,11,.12);  color: var(--accent4); }
    .pill-dim     { background: rgba(127,147,176,.08); color: var(--muted); }
    .pill-danger  { background: rgba(239,68,68,.1);   color: var(--warn); }

    /* XP badge */
    .xp-badge {
      font-size: .75rem; font-weight: 700;
      color: var(--accent); background: rgba(59,130,246,.08);
      border: 1px solid rgba(59,130,246,.18);
      padding: 3px 8px; border-radius: 6px;
    }

    /* Joined date */
    .date-text { font-size: .8rem; color: var(--muted); }

    /* Inline action row */
    .action-group { display: flex; align-items: center; gap: 6px; justify-content: flex-end; }

    /* ── Buttons ──────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 7px; padding: 9px 16px; border-radius: var(--radius-sm);
      font-size: .875rem; font-weight: 600; cursor: pointer;
      border: 1px solid transparent; transition: all .15s;
      font-family: inherit; text-decoration: none; white-space: nowrap;
    }
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-accent:hover  { background: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover   { background: var(--border); }
    .btn-success { background: rgba(16,185,129,.1); color: var(--accent3); border-color: rgba(16,185,129,.25); }
    .btn-success:hover { background: rgba(16,185,129,.2); }
    .btn-danger  { background: rgba(239,68,68,.1); color: var(--warn); border-color: rgba(239,68,68,.2); }
    .btn-danger:hover  { background: rgba(239,68,68,.2); }
    .btn-sm  { padding: 6px 12px; font-size: .8rem; }
    .btn-icon { padding: 7px; }

    /* ── Bulk action bar ──────────────────────────────── */
    .bulk-bar {
      display: none;
      align-items: center; gap: 12px;
      background: var(--surface3); border: 1px solid var(--border-hover);
      border-radius: var(--radius-sm); padding: 10px 16px;
      font-size: .875rem; font-weight: 600;
    }
    .bulk-bar.visible { display: flex; }
    .bulk-bar .count { color: var(--accent); }
    .bulk-bar-spacer { flex: 1; }

    /* ── Empty ────────────────────────────────────────── */
    .empty-state {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; text-align: center;
      padding: 64px 32px; gap: 16px;
    }
    .empty-icon { width: 56px; height: 56px; color: var(--dim); }
    .empty-state h3 { font-size: 1.125rem; font-weight: 700; }
    .empty-state p  { font-size: .875rem; color: var(--muted); max-width: 320px; line-height: 1.6; }

    /* ── Pagination ───────────────────────────────────── */
    .pagination { display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap; }
    .pagination a, .pagination span {
      min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
      border-radius: var(--radius-sm); font-size: .8125rem; font-weight: 600;
      border: 1px solid var(--border); background: var(--surface);
      color: var(--muted); text-decoration: none; transition: all .15s;
    }
    .pagination a:hover { color: var(--text); border-color: var(--border-hover); }
    .pagination .active span { background: var(--accent); border-color: var(--accent); color: #fff; }
    .pagination .disabled span { opacity: .4; cursor: default; }

    /* ── Confirm modal ────────────────────────────────── */
    .modal-backdrop {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.6); z-index: 200;
      align-items: center; justify-content: center;
    }
    .modal-backdrop.open { display: flex; }
    .modal {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 28px 28px 24px;
      max-width: 420px; width: 100%; margin: 16px;
      box-shadow: 0 20px 60px rgba(0,0,0,.5);
    }
    .modal h3 { font-size: 1rem; font-weight: 700; margin-bottom: 8px; }
    .modal p  { font-size: .875rem; color: var(--muted); line-height: 1.6; margin-bottom: 20px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

    /* ── Checkbox ─────────────────────────────────────── */
    input[type="checkbox"] {
      width: 16px; height: 16px; cursor: pointer;
      accent-color: var(--accent);
    }

    /* ── Scrollbar ────────────────────────────────────── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @media (max-width: 1024px) {
      .stats-strip { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 960px) {
      .topbar-search { display: none; }
    }
    @media (max-width: 640px) {
      .content { padding: 20px; }
      .topbar { padding: 0 16px; }
      .stats-strip { grid-template-columns: 1fr 1fr; }
      .toolbar { flex-direction: column; align-items: stretch; }
      .filter-tabs { width: 100%; }
      .col-xp, .col-joined, .col-institution { display: none; }
    }
  </style>
</head>
<body>
  @include('partials.instructor-sidebar')

  @php
    $tab = request('tab', 'enrolled'); // enrolled | pending
  @endphp

  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <a href="{{ route('instructor.classes.index') }}" class="topbar-back">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        Classes
      </a>

      <div class="topbar-title">
        <h1>{{ $class->name }}</h1>
        <span class="class-code-pill">{{ $class->class_code }}</span>
        @if($class->section)
          <span class="class-code-pill">{{ $class->section }}</span>
        @endif
      </div>

      <form method="GET" action="{{ route('instructor.classes.students', $class) }}" class="topbar-search">
        <input type="hidden" name="tab" value="{{ $tab }}" />
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" placeholder="Search students…" value="{{ request('search') }}" />
      </form>

      <a href="{{ route('profile') }}" class="topbar-btn" title="Profile">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </a>
    </header>

    <main class="content">
      <!-- Flash Messages -->
      @if (session('success'))
        <div class="flash flash-success">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
          </svg>
          {{ session('success') }}
        </div>
      @endif
      @if (session('error'))
        <div class="flash flash-error">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ session('error') }}
        </div>
      @endif

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h2>Student Roster</h2>
          <p>
            {{ $class->subject_code ? $class->subject_code . ' · ' : '' }}
            {{ $class->term ?? 'No term' }}
            @if($class->academic_year) · {{ $class->academic_year }} @endif
          </p>
        </div>
        {{-- Enrolment info --}}
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
          @if($class->max_students)
            <span style="font-size:.8125rem; color:var(--muted);">
              {{ $enrolledCount }} / {{ $class->max_students }} seats filled
            </span>
          @endif
          @if($class->allow_self_enroll)
            <span class="pill pill-ok">Self-Enrolment On</span>
          @else
            <span class="pill pill-dim">Self-Enrolment Off</span>
          @endif
        </div>
      </div>

      <!-- Stats Strip -->
      <div class="stats-strip">
        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent3)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M12 14c4.418 0 8 2.239 8 5v1H4v-1c0-2.761 3.582-5 8-5z"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $enrolledCount }}</div>
            <div class="strip-label">Enrolled Students</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent4)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="9"/>
              <path d="M12 8v4l3 3"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $pendingCount }}</div>
            <div class="strip-label">Pending Approval</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/>
              <polyline points="13 2 13 9 20 9"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $class->max_students ?? '∞' }}</div>
            <div class="strip-label">Max Capacity</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent2)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $avgXp ?? 0 }}</div>
            <div class="strip-label">Avg. XP</div>
          </div>
        </div>
      </div>

      <!-- Bulk Action Bar (shown when rows are checked) -->
      <div class="bulk-bar" id="bulkBar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9 11l3 3L22 4"/>
          <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
        </svg>
        <span><span class="count" id="bulkCount">0</span> students selected</span>
        <div class="bulk-bar-spacer"></div>
        @if($tab === 'pending')
          <form method="POST" action="{{ route('instructor.classes.students.approve-bulk', $class) }}" id="bulkApproveForm">
            @csrf
            <div id="bulkApproveInputs"></div>
            <button type="submit" class="btn btn-success btn-sm">
              <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M5 13l4 4L19 7"/>
              </svg>
              Approve Selected
            </button>
          </form>
        @endif
        <form method="POST" action="{{ route('instructor.classes.students.remove-bulk', $class) }}" id="bulkRemoveForm"
              onsubmit="return confirmBulkRemove()">
          @csrf @method('DELETE')
          <div id="bulkRemoveInputs"></div>
          <button type="submit" class="btn btn-danger btn-sm">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M17 16l4-4m0 0l-4-4m4 4H7"/>
            </svg>
            Remove Selected
          </button>
        </form>
      </div>

      <!-- Toolbar -->
      <div class="toolbar">
        <form method="GET" action="{{ route('instructor.classes.students', $class) }}"
              style="flex:1; display:flex; gap:12px; flex-wrap:wrap;">
          <input type="hidden" name="tab" value="{{ $tab }}" />
          <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Search by name or email…" value="{{ request('search') }}" />
          </div>
          @if(request('search'))
            <a href="{{ route('instructor.classes.students', ['class' => $class, 'tab' => $tab]) }}"
               class="btn btn-ghost btn-sm">Clear</a>
          @endif
        </form>

        <div class="filter-tabs">
          <a href="{{ route('instructor.classes.students', array_merge(request()->except(['tab','page']), ['class' => $class->id, 'tab' => 'enrolled'])) }}"
             class="filter-tab {{ $tab === 'enrolled' ? 'active' : '' }}">
            Enrolled
            <span class="tab-count">{{ $enrolledCount }}</span>
          </a>
          <a href="{{ route('instructor.classes.students', array_merge(request()->except(['tab','page']), ['class' => $class->id, 'tab' => 'pending'])) }}"
             class="filter-tab {{ $tab === 'pending' ? 'active' : '' }}">
            Pending
            <span class="tab-count {{ $pendingCount > 0 ? 'tab-count-warn' : '' }}">{{ $pendingCount }}</span>
          </a>
        </div>
      </div>

      <!-- Students Table -->
      <div class="table-wrap">
        @if($students->isNotEmpty())
          <table>
            <thead>
              <tr>
                <th class="col-check">
                  <input type="checkbox" id="selectAll" title="Select all" />
                </th>
                <th>Student</th>
                <th class="col-institution">Institution</th>
                <th class="col-xp">XP</th>
                <th class="col-joined">
                  {{ $tab === 'pending' ? 'Requested' : 'Enrolled' }}
                </th>
                <th>Status</th>
                <th class="col-action">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $student)
                <tr>
                  <!-- Checkbox -->
                  <td class="col-check">
                    <input type="checkbox" class="row-check" value="{{ $student->id }}" />
                  </td>

                  <!-- Student Identity -->
                  <td>
                    <div class="student-cell">
                      <div class="student-avatar">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                      </div>
                      <div>
                        <div class="student-name">{{ $student->name }}</div>
                        <div class="student-email">{{ $student->email }}</div>
                      </div>
                    </div>
                  </td>

                  <!-- Institution -->
                  <td class="col-institution">
                    <span class="date-text">
                      {{ $student->institution?->name ?? '—' }}
                    </span>
                  </td>

                  <!-- XP -->
                  <td class="col-xp">
                    <span class="xp-badge">{{ number_format($student->xp) }} XP</span>
                  </td>

                  <!-- Date -->
                  <td class="col-joined">
                    <span class="date-text">
                      {{ $student->pivot->enrolled_at
                           ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y')
                           : '—' }}
                    </span>
                  </td>

                  <!-- Status -->
                  <td>
                    @if($tab === 'pending')
                      <span class="pill pill-warning">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8">
                          <circle cx="4" cy="4" r="4"/>
                        </svg>
                        Pending
                      </span>
                    @else
                      <span class="pill pill-ok">
                        <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8">
                          <circle cx="4" cy="4" r="4"/>
                        </svg>
                        Enrolled
                      </span>
                    @endif
                  </td>

                  <!-- Actions -->
                  <td class="col-action">
                    <div class="action-group">
                      @if($tab === 'pending')
                        {{-- Approve --}}
                        <form method="POST"
                              action="{{ route('instructor.classes.students.approve', ['class' => $class, 'student' => $student]) }}">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm" title="Approve">
                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                              <path d="M5 13l4 4L19 7"/>
                            </svg>
                            Approve
                          </button>
                        </form>
                      @endif

                      {{-- Remove --}}
                      <form method="POST"
                            action="{{ route('instructor.classes.students.remove', ['class' => $class, 'student' => $student]) }}"
                            onsubmit="return confirm('Remove {{ addslashes($student->name) }} from this class?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Remove">
                          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                          </svg>
                          Remove
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>

          {{-- Pagination --}}
          @if($students->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
              <div class="pagination">
                {{ $students->appends(request()->except('page'))->links('pagination::simple-default') }}
              </div>
            </div>
          @endif

        @else
          <div class="empty-state">
            <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1"/>
              <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
            </svg>
            @if(request('search'))
              <h3>No students match "{{ request('search') }}"</h3>
              <p>Try a different name or email address.</p>
              <a href="{{ route('instructor.classes.students', ['class' => $class, 'tab' => $tab]) }}"
                 class="btn btn-ghost">Clear Search</a>
            @elseif($tab === 'pending')
              <h3>No pending requests</h3>
              <p>Students who request to join this class will appear here for your approval.</p>
            @else
              <h3>No students enrolled yet</h3>
              <p>Share the class code <strong>{{ $class->class_code }}</strong> with your students to get started.</p>
            @endif
          </div>
        @endif
      </div>

      @if($students->isNotEmpty())
        <p style="font-size:.75rem; color:var(--dim); text-align:center;">
          Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
        </p>
      @endif

    </main>
  </div>

  <script>
    // ── Select All / Row Checks ──────────────────────────────
    const selectAll   = document.getElementById('selectAll');
    const rowChecks   = document.querySelectorAll('.row-check');
    const bulkBar     = document.getElementById('bulkBar');
    const bulkCount   = document.getElementById('bulkCount');
    const bulkApproveInputs = document.getElementById('bulkApproveInputs');
    const bulkRemoveInputs  = document.getElementById('bulkRemoveInputs');

    function syncBulkBar() {
      const checked = [...rowChecks].filter(c => c.checked);
      bulkCount.textContent = checked.length;

      // Sync hidden inputs for both forms
      if (bulkApproveInputs) {
        bulkApproveInputs.innerHTML = '';
        checked.forEach(c => {
          const inp = document.createElement('input');
          inp.type = 'hidden'; inp.name = 'student_ids[]'; inp.value = c.value;
          bulkApproveInputs.appendChild(inp);
        });
      }
      if (bulkRemoveInputs) {
        bulkRemoveInputs.innerHTML = '';
        checked.forEach(c => {
          const inp = document.createElement('input');
          inp.type = 'hidden'; inp.name = 'student_ids[]'; inp.value = c.value;
          bulkRemoveInputs.appendChild(inp);
        });
      }

      bulkBar.classList.toggle('visible', checked.length > 0);
    }

    selectAll?.addEventListener('change', () => {
      rowChecks.forEach(c => c.checked = selectAll.checked);
      syncBulkBar();
    });

    rowChecks.forEach(c => c.addEventListener('change', () => {
      const allChecked = [...rowChecks].every(r => r.checked);
      const noneChecked = [...rowChecks].every(r => !r.checked);
      selectAll.indeterminate = !allChecked && !noneChecked;
      selectAll.checked = allChecked;
      syncBulkBar();
    }));

    // ── Bulk remove confirm ──────────────────────────────────
    function confirmBulkRemove() {
      const n = document.getElementById('bulkCount').textContent;
      return confirm(`Remove ${n} selected student(s) from this class?`);
    }
  </script>
</body>
</html>