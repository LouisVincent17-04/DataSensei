{{-- resources/views/instructor/classes/students.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $class->name }} — Students, DataSensei</title>
<style>
    /* Class roster. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --accent4: var(--ds-warning);
      --warn:    var(--ds-danger);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
    }
    .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }

    /* ── Title bar ────────────────────────────────────── */
    .topbar {
      min-height: 60px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      padding: 0 32px;
      gap: 8px 16px;
      flex-shrink: 0;
    }
    .topbar-back {
      display: flex; align-items: center; gap: 6px; flex-shrink: 0;
      color: var(--muted); font-size: .875rem; font-weight: 500;
      text-decoration: none; transition: color .16s ease;
    }
    .topbar-back:hover { color: var(--text); }
    .topbar-title {
      flex: 1 1 240px; min-width: 0;
      display: flex; align-items: center; flex-wrap: wrap; gap: 4px 10px;
    }
    .topbar-title h1 { min-width: 0; overflow-wrap: anywhere; }
    .topbar-title .class-code-pill {
      display: inline-flex; align-items: center;
      padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary);
      font: 500 .75rem/1.4 var(--ds-font-mono); white-space: nowrap;
    }
    .topbar-title .topbar-meta { color: var(--muted); font-size: .8125rem; white-space: nowrap; }
    .topbar-search {
      display: flex; align-items: center; gap: 8px;
      width: 100%; max-width: 260px; flex: 0 1 260px;
      min-height: 38px; padding: 0 12px;
      background: var(--surface3); border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      transition: border-color .16s ease, box-shadow .16s ease;
    }
    .topbar-search:focus-within { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .topbar-search svg { color: var(--muted); }
    .topbar-search input {
      width: 100%; min-height: 36px; background: none; border: none; outline: none; box-shadow: none;
      color: var(--text); font: 400 .875rem/1.2 var(--ds-font-sans);
    }
    .topbar-search input::placeholder { color: var(--dim); }
    .topbar-btn {
      width: 36px; height: 36px; flex: 0 0 36px;
      display: flex; align-items: center; justify-content: center;
      background: var(--surface2); border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm); color: var(--muted); text-decoration: none;
      transition: background .16s ease, color .16s ease;
    }
    .topbar-btn:hover { color: var(--text); background: var(--ds-surface-hover); }

    /* ── Content ──────────────────────────────────────── */
    .content { flex: 1; padding: 28px 32px 48px; display: flex; flex-direction: column; gap: 24px; }

    /* ── Flash ────────────────────────────────────────── */
    .flash {
      padding: 12px 16px; border: 1px solid transparent; border-radius: var(--radius-sm);
      font-size: .875rem; line-height: 1.5; display: flex; align-items: center; gap: 8px;
    }
    .flash-success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: #d1fae5; }
    .flash-error   { background: var(--ds-danger-soft);  border-color: var(--ds-danger-border);  color: #fee2e2; }

    /* ── Section header ───────────────────────────────── */
    .page-header {
      display: flex; align-items: flex-end; justify-content: space-between;
      gap: 12px 16px; flex-wrap: wrap;
    }
    .page-header > div:first-child { min-width: 0; flex: 1 1 320px; }
    .page-header h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; }
    .page-header p  { max-width: 72ch; font-size: .875rem; line-height: 1.5; color: var(--muted); margin-top: 4px; }
    .header-note { color: var(--muted); font-size: .8125rem; }

    /* ── Summary figures ──────────────────────────────── */
    .stats-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .strip-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 16px 18px;
    }
    .strip-info { display: flex; flex-direction: column; gap: 4px; }
    .strip-label { font-size: .8125rem; font-weight: 500; line-height: 1.4; color: var(--muted); }
    .strip-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }

    /* ── Add student by email ─────────────────────────── */
    .add-student-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px;
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 16px 24px;
      flex-wrap: wrap;
    }
    .add-student-copy { min-width: 0; flex: 1 1 320px; }
    .add-student-copy h3 { font-size: .9375rem; font-weight: 600; line-height: 1.35; margin-bottom: 4px; }
    .add-student-copy p { color: var(--muted); font-size: .875rem; line-height: 1.5; max-width: 72ch; }
    .add-student-form {
      display: flex;
      gap: 8px;
      align-items: flex-end;
      flex-wrap: wrap;
      min-width: 0;
    }
    .add-student-field { display: flex; flex-direction: column; gap: 6px; min-width: 0; flex: 1 1 260px; }
    .add-student-field label { color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; line-height: 1.35; }
    .add-student-field input {
      width: 300px;
      max-width: 100%;
      min-height: 38px;
      padding: 8px 12px;
      background: var(--surface3);
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font: 400 .875rem/1.4 var(--ds-font-sans);
      outline: none;
      transition: border-color .16s ease, box-shadow .16s ease;
    }
    .add-student-field input:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .add-student-field input::placeholder { color: var(--dim); }

    /* ── Toolbar ──────────────────────────────────────── */
    .toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .search-box {
      display: flex; align-items: center; gap: 8px;
      min-height: 38px; padding: 0 12px;
      background: var(--surface3); border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      transition: border-color .16s ease, box-shadow .16s ease;
      flex: 1; min-width: 220px; max-width: 360px;
    }
    .search-box:focus-within { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .search-box svg { color: var(--muted); }
    .search-box input {
      width: 100%; min-height: 36px; background: none; border: none; outline: none; box-shadow: none;
      color: var(--text); font: 400 .875rem/1.2 var(--ds-font-sans);
    }
    .search-box input::placeholder { color: var(--dim); }

    .filter-tabs { display: flex; gap: 4px; }
    .filter-tab {
      min-height: 38px; padding: 0 12px;
      display: inline-flex; align-items: center; gap: 8px;
      border: 1px solid transparent; border-radius: var(--radius-sm); background: none;
      color: var(--muted); font: 500 .875rem/1.2 var(--ds-font-sans); text-decoration: none;
      transition: background .16s ease, color .16s ease, border-color .16s ease;
    }
    .filter-tab:hover:not(.active) { color: var(--text); background: var(--surface2); }
    .filter-tab.active { background: var(--ds-accent-soft); border-color: var(--ds-accent-border); color: var(--ds-accent-text); }
    .tab-count {
      min-width: 20px; padding: 1px 6px; text-align: center;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary);
      font-size: .75rem; font-weight: 600; line-height: 1.4; font-variant-numeric: tabular-nums;
    }
    .filter-tab.active .tab-count { border-color: var(--ds-accent-border); background: transparent; color: var(--ds-accent-text); }
    .tab-count-warn { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

    /* ── Students table ───────────────────────────────── */
    .table-wrap {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow-x: auto;
    }
    table {
      width: 100%; min-width: 760px; border-collapse: collapse;
      font-variant-numeric: tabular-nums;
    }
    thead th {
      padding: 10px 14px;
      background: var(--surface3);
      border-bottom: 1px solid var(--border);
      font-size: .75rem; font-weight: 600; color: var(--muted);
      text-align: left; white-space: nowrap;
    }
    thead th:first-child, td:first-child { padding-left: 20px; }
    thead th:last-child, td:last-child { padding-right: 20px; }
    thead th.col-check { width: 44px; padding-right: 0; }
    thead th.col-action { text-align: right; }

    tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background .12s ease;
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(255, 255, 255, .02); }

    td {
      padding: 12px 14px;
      color: var(--ds-text-secondary);
      font-size: .875rem; vertical-align: middle;
    }
    td.col-check { padding-right: 0; }
    td.col-action { text-align: right; }

    /* Student identity cell */
    .student-cell { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .student-cell > div:last-child { min-width: 0; }
    .student-avatar {
      width: 32px; height: 32px; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--ds-border-strong);
      display: flex; align-items: center; justify-content: center;
      font-size: .8125rem; font-weight: 600; color: var(--text);
      flex-shrink: 0;
    }
    /* The table scrolls inside its box, so names and addresses stay on one line. */
    .student-name { font-weight: 600; color: var(--text); white-space: nowrap; }
    .student-email { font-size: .75rem; color: var(--muted); margin-top: 2px; white-space: nowrap; }

    /* Status labels */
    .pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary);
      font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .pill svg { width: 6px; height: 6px; }
    .pill-ok      { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .pill-warning { background: var(--ds-warning-soft); border-color: var(--ds-warning-border); color: var(--ds-warning-text); }
    .pill-dim     { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--ds-text-secondary); }
    .pill-danger  { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }

    /* XP */
    .xp-badge { color: var(--ds-text-secondary); font-size: .875rem; font-variant-numeric: tabular-nums; white-space: nowrap; }

    /* Dates and secondary cells */
    .date-text { font-size: .8125rem; color: var(--muted); }

    /* Inline action row */
    .action-group { display: flex; align-items: center; gap: 8px; justify-content: flex-end; }

    /* ── Buttons ──────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 8px; min-height: 38px; padding: 0 16px; border-radius: var(--radius-sm);
      font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer;
      border: 1px solid transparent;
      transition: background .16s ease, border-color .16s ease, color .16s ease;
      text-decoration: none; white-space: nowrap;
    }
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-accent:hover  { background: var(--accent-hover); border-color: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--ds-border-strong); }
    .btn-ghost:hover   { background: var(--ds-surface-hover); }
    .btn-success { background: transparent; color: var(--ds-success-text); border-color: var(--ds-success-border); }
    .btn-success:hover { background: var(--ds-success-soft); }
    .btn-danger  { background: transparent; color: var(--ds-danger-text); border-color: var(--ds-danger-border); }
    .btn-danger:hover  { background: var(--ds-danger-soft); }
    .btn-sm  { min-height: 32px; padding: 0 12px; font-size: .8125rem; }
    .btn-icon { width: 32px; padding: 0; }

    /* ── Bulk action bar ──────────────────────────────── */
    .bulk-bar {
      display: none;
      align-items: center; flex-wrap: wrap; gap: 8px 12px;
      background: var(--surface2); border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm); padding: 8px 12px 8px 16px;
      font-size: .875rem; font-weight: 500;
    }
    .bulk-bar.visible { display: flex; }
    .bulk-bar .count { color: var(--ds-accent-text); font-weight: 600; font-variant-numeric: tabular-nums; }
    .bulk-bar-spacer { flex: 1; }

    /* ── Empty ────────────────────────────────────────── */
    .empty-state {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; text-align: center;
      padding: 32px 20px; gap: 8px;
    }
    .empty-icon { width: 28px; height: 28px; margin-bottom: 4px; color: var(--muted); }
    .empty-state h3 { font-size: .9375rem; font-weight: 600; overflow-wrap: anywhere; }
    .empty-state p  { font-size: .875rem; color: var(--muted); max-width: 360px; line-height: 1.5; }
    .empty-state strong { font-family: var(--ds-font-mono); font-weight: 500; color: var(--text); }
    .empty-state .btn { margin-top: 8px; }

    /* ── Checkbox ─────────────────────────────────────── */
    input[type="checkbox"] {
      width: 16px; height: 16px; cursor: pointer;
      accent-color: var(--accent);
    }

    @media (max-width: 960px) {
      .topbar-search { display: none; }
    }
    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 0 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .content { padding: 20px 16px 32px; }
      .topbar { padding: 10px 16px; flex-wrap: nowrap; gap: 12px; }
      .topbar-title { flex: 1 1 auto; }
      .page-header > div:first-child { flex-basis: 100%; }
      .add-student-card { padding: 16px; }
      .add-student-form { width: 100%; }
      .add-student-field input { width: 100%; }
      .add-student-form .btn { flex: 1 1 auto; }
      .toolbar { flex-direction: column; align-items: stretch; }
      .toolbar .search-box { max-width: none; min-width: 0; }
      .filter-tabs { width: 100%; }
      .filter-tab { flex: 1 1 0; justify-content: center; }
      thead th:first-child, td:first-child { padding-left: 16px; }
      thead th:last-child, td:last-child { padding-right: 16px; }
    }
    @media (max-width: 560px) {
      .stats-strip { grid-template-columns: minmax(0, 1fr); gap: 8px; }
      .strip-card { padding: 12px 16px; }
      .strip-info { flex-direction: row; align-items: baseline; justify-content: space-between; gap: 12px; }
      .strip-value { font-size: 1.25rem; }
    }
  </style>
    @include('partials.page-head', ['pageDescription' => 'Manage your classes, join codes, and enrolled students.'])
</head>
<body>
  @include('partials.instructor-sidebar')

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
        <h1 class="ds-page-title">{{ $class->name }}</h1>
        <span class="class-code-pill">{{ $class->class_code }}</span>
        @if($class->section)
          <span class="topbar-meta">{{ $class->section }}</span>
        @endif
      </div>

      <form method="GET" action="{{ route('instructor.classes.students', $class) }}" class="topbar-search">
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

      @error('email')
        <div class="flash flash-error">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
          {{ $message }}
        </div>
      @enderror

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <h2>Student Roster</h2>
          <p>
            {{ $class->subject_code ? $class->subject_code . ', ' : '' }}
            {{ $class->term ?? 'No term' }}
            @if($class->academic_year), {{ $class->academic_year }} @endif
          </p>
        </div>
        {{-- Enrolment info --}}
        <div style="display:flex; flex-direction:column; align-items:flex-start; gap:2px;">
          @if($class->max_students)
            <span class="header-note">
              {{ $enrolledCount }} / {{ $class->max_students }} seats filled
            </span>
          @endif
          <span class="header-note">Instructor-managed enrolment</span>
        </div>
      </div>

      <!-- Stats Strip -->
      <div class="stats-strip">
        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Enrolled Students</div>
            <div class="strip-value">{{ $enrolledCount }}</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Max Capacity</div>
            <div class="strip-value">{{ $class->max_students ?? '∞' }}</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Avg. XP</div>
            <div class="strip-value">{{ $avgXp ?? 0 }}</div>
          </div>
        </div>
      </div>

      <!-- Add Student by Gmail -->
      <section class="add-student-card">
        <div class="add-student-copy">
          <h3>Add Student by Gmail</h3>
          <p>
            This searches the registered users table by email, then adds the matching student account to this class.
            The student must already have a DataSensei student account.
          </p>
        </div>

        <form method="POST" action="{{ route('instructor.classes.students.add-by-email', $class) }}" class="add-student-form">
          @csrf
          <div class="add-student-field">
            <label>Student Gmail / Email</label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="student@gmail.com"
              required
            >
          </div>

          <button type="submit" class="btn btn-accent">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path d="M12 5v14M5 12h14"/>
            </svg>
            Add Student
          </button>
        </form>
      </section>

      <!-- Bulk Action Bar (shown when rows are checked) -->
      <div class="bulk-bar" id="bulkBar">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M9 11l3 3L22 4"/>
          <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
        </svg>
        <span><span class="count" id="bulkCount">0</span> students selected</span>
        <div class="bulk-bar-spacer"></div>
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
          <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Search by name or email…" value="{{ request('search') }}" />
          </div>
          @if(request('search'))
            <a href="{{ route('instructor.classes.students', ['class' => $class, 'tab' => $tab ?? request('tab')]) }}"
               class="btn btn-ghost btn-sm">Clear</a>
          @endif
        </form>

        <div class="filter-tabs">
          <a href="{{ route('instructor.classes.students', array_merge(request()->except('page'), ['class' => $class->id])) }}"
             class="filter-tab active">
            Enrolled
            <span class="tab-count">{{ $enrolledCount }}</span>
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
                  Enrolled
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
                    <span class="pill pill-ok">
                      <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="4"/>
                      </svg>
                      Enrolled
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="col-action">
                    <div class="action-group">
                      {{-- Remove --}}
                      <form method="POST"
                            action="{{ route('instructor.classes.students.remove', ['class' => $class, 'student' => $student]) }}"
                            onsubmit="return confirm('Remove this student from the class?')">
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
              <a href="{{ route('instructor.classes.students', ['class' => $class]) }}"
                 class="btn btn-ghost">Clear Search</a>
            @else
              <h3>No students enrolled yet</h3>
              <p>Share the class code <strong>{{ $class->class_code }}</strong> with your students to get started.</p>
            @endif
          </div>
        @endif
      </div>

      @if($students->isNotEmpty())
        <p style="font-size:.8125rem; color:var(--muted);">
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
    const bulkRemoveInputs  = document.getElementById('bulkRemoveInputs');

    function syncBulkBar() {
      const checked = [...rowChecks].filter(c => c.checked);
      bulkCount.textContent = checked.length;

      // Sync hidden inputs for the bulk removal form.
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
