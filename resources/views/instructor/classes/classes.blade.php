<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Classes — DataSensei</title>
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

    /* ── Topbar ─────────────────────────────────────────── */
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
    .topbar-search {
      display: flex; align-items: center;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-sm); padding: 8px 12px; gap: 10px; width: 280px;
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

    /* ── Content ────────────────────────────────────────── */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }

    /* ── Flash ──────────────────────────────────────────── */
    .flash {
      padding: 14px 18px; border-radius: var(--radius-sm);
      font-size: .875rem; font-weight: 500; display: flex; align-items: center; gap: 10px;
    }
    .flash-success { background: rgba(16,185,129,.1); border: 1px solid rgba(16,185,129,.25); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.25);  color: var(--warn); }

    /* ── Page Header ────────────────────────────────────── */
    .page-header {
      display: flex; align-items: flex-end; justify-content: space-between;
      gap: 16px; flex-wrap: wrap;
    }
    .page-header h2 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; }
    .page-header p { font-size: .875rem; color: var(--muted); margin-top: 4px; }

    /* ── Toolbar ────────────────────────────────────────── */
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

    /* ── Stats Strip ────────────────────────────────────── */
    .stats-strip { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
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

    /* ── Classes Grid ───────────────────────────────────── */
    .classes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 16px;
    }

    .class-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: visible;
      display: flex; flex-direction: column;
      transition: border-color .2s, transform .2s, box-shadow .2s;
      position: relative;
    }
    .class-card:hover {
      border-color: var(--border-hover);
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0,0,0,.3);
    }

    /* Colour stripe on left based on index */
    .class-card::before {
      content: '';
      position: absolute;
      left: 0; top: 0; bottom: 0;
      width: 3px;
      border-radius: 3px 0 0 3px;
    }
    .stripe-blue::before   { background: var(--accent); }
    .stripe-purple::before { background: var(--accent2); }
    .stripe-green::before  { background: var(--accent3); }
    .stripe-amber::before  { background: var(--accent4); }

    .class-card-header {
      padding: 20px 20px 14px 24px;
      display: flex; align-items: flex-start; gap: 12px; flex: 1;
      border-radius: var(--radius) var(--radius) 0 0;
      overflow: hidden;
    }
    .class-initials {
      width: 44px; height: 44px; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: .8rem; font-weight: 800; flex-shrink: 0;
      letter-spacing: .03em;
    }
    .class-info { flex: 1; min-width: 0; }
    .class-name {
      font-size: .975rem; font-weight: 700;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      margin-bottom: 3px;
    }
    .class-meta { font-size: .75rem; color: var(--muted); line-height: 1.6; }
    .class-code-badge {
      font-size: .65rem; font-weight: 700; padding: 3px 8px;
      border-radius: 999px; background: var(--surface3);
      border: 1px solid var(--border); color: var(--muted);
      letter-spacing: .06em; font-family: 'JetBrains Mono', monospace;
      flex-shrink: 0; white-space: nowrap;
    }

    .class-card-body { padding: 0 20px 0 24px; flex: 1; }
    .class-desc {
      font-size: .8125rem; color: var(--muted); line-height: 1.6;
      overflow: hidden;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }

    .class-card-stats {
      display: grid; grid-template-columns: 1fr 1fr;
      gap: 1px; margin-top: 16px;
      background: var(--border);
      border-top: 1px solid var(--border);
    }
    .class-stat {
      padding: 12px 20px; background: var(--surface);
      display: flex; flex-direction: column; gap: 2px;
    }
    .class-stat:first-child { padding-left: 24px; }
    .class-stat-value { font-size: .95rem; font-weight: 700; }
    .class-stat-label { font-size: .68rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }

    .class-card-footer {
      padding: 12px 20px 12px 24px;
      border-top: 1px solid var(--border);
      display: flex; align-items: center; gap: 8px;
      border-radius: 0 0 var(--radius) var(--radius);
      background: var(--surface);
    }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 7px; padding: 9px 16px; border-radius: var(--radius-sm);
      font-size: .875rem; font-weight: 600; cursor: pointer;
      border: 1px solid transparent; transition: all .15s;
      font-family: inherit; text-decoration: none; white-space: nowrap;
    }
    .btn-primary { background: var(--text); color: var(--bg); }
    .btn-primary:hover { background: #e4e4e7; }
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-accent:hover  { background: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover   { background: var(--border); }
    .btn-danger  { background: rgba(239,68,68,.1); color: var(--warn); border-color: rgba(239,68,68,.2); }
    .btn-danger:hover  { background: rgba(239,68,68,.2); }
    .btn-sm  { padding: 6px 12px; font-size: .8rem; }
    .btn-icon { padding: 7px; }

    /* ── Pill ───────────────────────────────────────────── */
    .pill {
      font-size: .68rem; font-weight: 700; padding: 3px 8px;
      border-radius: 999px; letter-spacing: .04em; text-transform: uppercase;
    }
    .pill-ok      { background: rgba(16,185,129,.1);  color: var(--accent3); }
    .pill-warning { background: rgba(245,158,11,.1);  color: var(--accent4); }
    .pill-dim     { background: rgba(127,147,176,.08); color: var(--muted); }

    /* ── Empty ──────────────────────────────────────────── */
    .empty-state {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; text-align: center;
      padding: 64px 32px; gap: 16px;
      background: var(--surface); border: 1px dashed var(--border);
      border-radius: var(--radius);
    }
    .empty-icon { width: 64px; height: 64px; color: var(--dim); }
    .empty-state h3 { font-size: 1.125rem; font-weight: 700; }
    .empty-state p  { font-size: .875rem; color: var(--muted); max-width: 340px; line-height: 1.6; }

    /* ── Pagination ─────────────────────────────────────── */
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

    /* ── Dropdown Menu ──────────────────────────────────── */
    .dropdown { position: relative; }
    .dropdown-menu {
      position: absolute; right: 0; top: calc(100% + 6px); z-index: 200;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-sm); min-width: 180px;
      box-shadow: 0 8px 24px rgba(0,0,0,.4);
      display: none; flex-direction: column; overflow: hidden;
    }
    .dropdown.open .dropdown-menu { display: flex; }
    .dropdown-item {
      padding: 10px 14px; font-size: .8125rem; font-weight: 500;
      color: var(--muted); cursor: pointer; text-decoration: none;
      display: flex; align-items: center; gap: 8px;
      transition: background .12s, color .12s;
      background: none; border: none; font-family: inherit; width: 100%; text-align: left;
    }
    .dropdown-item:hover { background: var(--surface2); color: var(--text); }
    .dropdown-item.danger { color: var(--warn); }
    .dropdown-item.danger:hover { background: rgba(239,68,68,.08); }
    .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @media (max-width: 960px) {
      .stats-strip { grid-template-columns: 1fr 1fr; }
      .topbar-search { display: none; }
    }
    @media (max-width: 640px) {
      .content { padding: 20px; }
      .topbar { padding: 0 20px; }
      .page-header { flex-direction: column; align-items: flex-start; }
      .stats-strip { grid-template-columns: 1fr; }
      .classes-grid { grid-template-columns: 1fr; }
      .toolbar { flex-direction: column; align-items: stretch; }
      .filter-tabs { width: 100%; }
    }
  </style>
</head>
<body>
  @include('partials.instructor-sidebar')

@php
    use Illuminate\Support\Facades\Auth;

    $instructor = Auth::user();
    $stripes = ['stripe-blue', 'stripe-purple', 'stripe-green', 'stripe-amber'];

    if (!function_exists('classInitials')) {
        function classInitials(string $name): string {
            $words = array_filter(explode(' ', $name));

            if (count($words) === 1) {
                return strtoupper(substr(array_values($words)[0], 0, 2));
            }

            return strtoupper(substr(array_values($words)[0], 0, 1) . substr(end($words), 0, 1));
        }
    }
@endphp

  <div class="main">
    <header class="topbar">
      <h1>My Classes</h1>

      <form method="GET" action="{{ route('instructor.classes.index') }}" class="topbar-search">
        @if($showArchived)
          <input type="hidden" name="archived" value="1" />
        @endif
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" placeholder="Search classes…" value="{{ request('search') }}" />
      </form>

      <a href="{{ route('instructor.classes.create') }}" class="btn btn-accent" style="gap:8px;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        New Class
      </a>

      <a href="{{ route('profile') }}" class="topbar-btn" title="Profile">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </a>
    </header>

    <main class="content">
      {{-- Flash Messages --}}
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

      {{-- Page Header --}}
      <div class="page-header">
        <div>
          <h2>{{ $showArchived ? 'Archived Classes' : 'Active Classes' }}</h2>
          <p>Manage your class sections, enrolments, and assignments.</p>
        </div>
        <a href="{{ route('instructor.classes.create') }}" class="btn btn-accent">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          Create New Class
        </a>
      </div>

      {{-- Stats Strip --}}
      <div class="stats-strip">
        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
              <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $totalActive }}</div>
            <div class="strip-label">Active Classes</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent4)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $totalArchived }}</div>
            <div class="strip-label">Archived Classes</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-icon" style="color:var(--accent3)">
            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M12 14c4.418 0 8 2.239 8 5v1H4v-1c0-2.761 3.582-5 8-5z"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <div class="strip-info">
            <div class="strip-value">{{ $studentCounts }}</div>
            <div class="strip-label">Total Students (this page)</div>
          </div>
        </div>
      </div>

      {{-- Toolbar --}}
      <div class="toolbar">
        <form method="GET" action="{{ route('instructor.classes.index') }}" style="flex:1;display:flex;gap:12px;flex-wrap:wrap;">
          @if($showArchived)
            <input type="hidden" name="archived" value="1"/>
          @endif
          <div class="search-box">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="search" placeholder="Search by name, section, or code…" value="{{ request('search') }}" />
          </div>
          @if(request('search'))
            <a href="{{ route('instructor.classes.index', $showArchived ? ['archived'=>1] : []) }}" class="btn btn-ghost btn-sm">
              Clear
            </a>
          @endif
        </form>

        <div class="filter-tabs">
          <a href="{{ route('instructor.classes.index', request()->except(['archived','page'])) }}"
             class="filter-tab {{ !$showArchived ? 'active' : '' }}">
            Active
            <span class="tab-count">{{ $totalActive }}</span>
          </a>
          <a href="{{ route('instructor.classes.index', array_merge(request()->except('page'), ['archived'=>1])) }}"
             class="filter-tab {{ $showArchived ? 'active' : '' }}">
            Archived
            <span class="tab-count">{{ $totalArchived }}</span>
          </a>
        </div>
      </div>

      {{-- Classes Grid --}}
      @if ($classes->isNotEmpty())
        <div class="classes-grid">
          @foreach ($classes as $index => $class)
            @php $stripe = $stripes[$index % 4]; @endphp
            <div class="class-card {{ $stripe }}">
              <div class="class-card-header">
                <div class="class-initials" style="color:var({{ $stripe === 'stripe-blue' ? '--accent' : ($stripe === 'stripe-purple' ? '--accent2' : ($stripe === 'stripe-green' ? '--accent3' : '--accent4')) }})">
                  {{ classInitials($class->name) }}
                </div>
                <div class="class-info">
                  <div class="class-name" title="{{ $class->name }}">{{ $class->name }}</div>
                  <div class="class-meta">
                    @if($class->section) {{ $class->section }} · @endif
                    @if($class->subject_code) {{ $class->subject_code }} · @endif
                    {{ $class->term ?? 'No term set' }}
                  </div>
                </div>
                <span class="class-code-badge">{{ $class->class_code }}</span>
              </div>

              @if($class->description)
                <div class="class-card-body">
                  <p class="class-desc">{{ $class->description }}</p>
                </div>
              @endif

              <div class="class-card-stats">
                <div class="class-stat">
                  {{-- FIX: students_count comes from withCount('students') in the controller --}}
                  <span class="class-stat-value">{{ $class->students_count }}</span>
                  <span class="class-stat-label">Students</span>
                </div>
                <div class="class-stat">
                  <span class="class-stat-value">
                    @if($class->is_archived)
                      <span class="pill pill-dim">Archived</span>
                    @else
                      <span class="pill pill-ok">Active</span>
                    @endif
                  </span>
                  <span class="class-stat-label">Status</span>
                </div>
              </div>

              <div class="class-card-footer">
                {{-- View Students --}}
                <a href="{{ route('instructor.classes.students', $class) }}"
                   class="btn btn-accent btn-sm" style="flex:1; justify-content:center;">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1"/>
                    <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
                  </svg>
                  Students
                </a>

                {{-- Edit --}}
                <a href="{{ route('instructor.classes.edit', $class) }}"
                   class="btn btn-ghost btn-sm" title="Edit class">
                  <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                  </svg>
                  Edit
                </a>

                {{-- More: Archive/Restore + Delete --}}
                <div class="dropdown">
                  <button class="btn btn-ghost btn-sm btn-icon dropdown-toggle" title="More options" type="button">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                    </svg>
                  </button>
                  <div class="dropdown-menu">
                    @if (!$class->is_archived)
                      <form method="POST" action="{{ route('instructor.classes.archive', $class) }}">
                        @csrf @method('PATCH')
                        <button class="dropdown-item" type="submit">
                          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                          </svg>
                          Archive
                        </button>
                      </form>
                    @else
                      <form method="POST" action="{{ route('instructor.classes.restore', $class) }}">
                        @csrf @method('PATCH')
                        <button class="dropdown-item" type="submit">
                          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                          </svg>
                          Restore
                        </button>
                      </form>
                    @endif
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('instructor.classes.destroy', $class) }}"
                          onsubmit="return confirm('Permanently delete {{ addslashes($class->name) }}? This cannot be undone.')">
                      @csrf @method('DELETE')
                      <button class="dropdown-item danger" type="submit">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if ($classes->hasPages())
          <div class="pagination">
            {{ $classes->links('pagination::simple-default') }}
          </div>
        @endif

      @else
        <div class="empty-state">
          <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
          </svg>
          @if (request('search'))
            <h3>No classes match "{{ request('search') }}"</h3>
            <p>Try a different search term or clear the filter.</p>
            <a href="{{ route('instructor.classes.index') }}" class="btn btn-ghost">Clear Search</a>
          @elseif ($showArchived)
            <h3>No archived classes</h3>
            <p>Classes you archive will appear here. You can restore them at any time.</p>
          @else
            <h3>No classes yet</h3>
            <p>Create your first class to get started — students can join with your unique class code.</p>
            <a href="{{ route('instructor.classes.create') }}" class="btn btn-accent">
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
              </svg>
              Create First Class
            </a>
          @endif
        </div>
      @endif
    </main>
    {{-- Dropdown toggle: JS-driven so Archive/Restore form submits don't lose focus --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
          btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var dropdown = btn.closest('.dropdown');
            var isOpen = dropdown.classList.contains('open');
            // Close all open dropdowns first
            document.querySelectorAll('.dropdown.open').forEach(function (d) { d.classList.remove('open'); });
            if (!isOpen) dropdown.classList.add('open');
          });
        });
        // Click outside closes all
        document.addEventListener('click', function () {
          document.querySelectorAll('.dropdown.open').forEach(function (d) { d.classList.remove('open'); });
        });
      });
    </script>
  </body>
</html>