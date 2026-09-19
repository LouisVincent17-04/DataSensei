<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Classes — DataSensei</title>
<style>
    /* My Classes. Colours, type and radius come from partials.design-system;
       --accent2/3/4 remain because the class initials reference them. */
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

    /* ── Title bar ──────────────────────────────────────── */
    .topbar {
      min-height: 60px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 32px;
      gap: 12px;
      flex-shrink: 0;
    }
    .topbar h1 { flex: 1; min-width: 0; }
    .topbar-search {
      display: flex;
      align-items: center;
      gap: 8px;
      width: 100%;
      max-width: 300px;
      min-height: 38px;
      padding: 0 12px;
      background: var(--surface3);
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      transition: border-color .16s ease, box-shadow .16s ease;
      flex: 0 1 300px;
    }
    .topbar-search:focus-within { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .topbar-search svg { flex: 0 0 auto; color: var(--muted); }
    .topbar-search input {
      flex: 1;
      width: 100%;
      min-height: 36px;
      margin: 0;
      padding: 0;
      background: transparent;
      border: none;
      outline: none;
      box-shadow: none;
      color: var(--text);
      font: 400 .875rem/1.2 var(--ds-font-sans);
      appearance: none;
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

    /* ── Content ────────────────────────────────────────── */
    .content { flex: 1; padding: 28px 32px 48px; display: flex; flex-direction: column; gap: 24px; }

    /* ── Flash ──────────────────────────────────────────── */
    .flash {
      padding: 12px 16px; border: 1px solid transparent; border-radius: var(--radius-sm);
      font-size: .875rem; line-height: 1.5; display: flex; align-items: center; gap: 8px;
    }
    .flash-success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: #d1fae5; }
    .flash-error   { background: var(--ds-danger-soft);  border-color: var(--ds-danger-border);  color: #fee2e2; }

    /* ── Section header ─────────────────────────────────── */
    .page-header {
      display: flex; align-items: flex-end; justify-content: space-between;
      gap: 16px; flex-wrap: wrap;
    }
    .page-header > div { min-width: 0; flex: 1 1 320px; }
    .page-header h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; }
    .page-header p { max-width: 72ch; margin-top: 4px; font-size: .875rem; line-height: 1.5; color: var(--muted); }

    /* ── Toolbar ────────────────────────────────────────── */
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

    /* ── Summary figures ────────────────────────────────── */
    .stats-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .strip-card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius); padding: 16px 18px;
    }
    .strip-info { display: flex; flex-direction: column; gap: 4px; }
    .strip-label { font-size: .8125rem; font-weight: 500; line-height: 1.4; color: var(--muted); }
    .strip-value { font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }

    /* ── Class cards ────────────────────────────────────── */
    .classes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(min(320px, 100%), 1fr));
      gap: 16px;
    }

    .class-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      overflow: visible;
      display: flex; flex-direction: column;
      min-width: 0;
      transition: border-color .16s ease;
    }
    .class-card:hover { border-color: var(--border-hover); }

    .class-card-header {
      padding: 20px 20px 16px;
      display: flex; align-items: flex-start; gap: 12px; flex: 1;
    }
    /* The index-based colour set inline by the template is neutralised:
       colour is reserved for status. */
    .class-initials {
      width: 40px; height: 40px; border-radius: var(--radius-sm);
      background: var(--surface2); border: 1px solid var(--ds-border-strong);
      display: flex; align-items: center; justify-content: center;
      color: var(--text) !important;
      font-size: .8125rem; font-weight: 600; flex-shrink: 0;
    }
    .class-info { flex: 1; min-width: 0; }
    .class-name {
      font-size: .9375rem; font-weight: 600; line-height: 1.35;
      overflow-wrap: anywhere;
      margin-bottom: 2px;
    }
    .class-meta { font-size: .8125rem; color: var(--muted); line-height: 1.5; overflow-wrap: anywhere; }
    .class-code-badge {
      display: inline-flex; align-items: center;
      padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary);
      font: 500 .75rem/1.4 var(--ds-font-mono);
      flex-shrink: 0; white-space: nowrap;
    }

    .class-card-body { padding: 0 20px; flex: 1; }
    .class-desc {
      font-size: .8125rem; color: var(--muted); line-height: 1.55;
      overflow: hidden;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    }

    .class-card-stats {
      display: grid; grid-template-columns: 1fr 1fr;
      margin-top: 16px;
      border-top: 1px solid var(--border);
    }
    .class-stat {
      padding: 12px 20px;
      display: flex; flex-direction: column-reverse; justify-content: flex-end; gap: 2px;
    }
    .class-stat + .class-stat { border-left: 1px solid var(--border); }
    .class-stat-value { font-size: .9375rem; font-weight: 600; line-height: 1.4; font-variant-numeric: tabular-nums; }
    .class-stat-label { font-size: .75rem; color: var(--muted); font-weight: 500; }

    .class-card-footer {
      padding: 12px 20px;
      border-top: 1px solid var(--border);
      display: flex; align-items: center; gap: 8px;
    }

    /* ── Buttons ────────────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 8px; min-height: 38px; padding: 0 16px; border-radius: var(--radius-sm);
      font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer;
      border: 1px solid transparent;
      transition: background .16s ease, border-color .16s ease, color .16s ease;
      text-decoration: none; white-space: nowrap;
    }
    .btn-primary,
    .btn-accent  { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-primary:hover,
    .btn-accent:hover  { background: var(--accent-hover); border-color: var(--accent-hover); }
    .btn-ghost   { background: var(--surface2); color: var(--text); border-color: var(--ds-border-strong); }
    .btn-ghost:hover   { background: var(--ds-surface-hover); }
    .btn-danger  { background: transparent; color: var(--ds-danger-text); border-color: var(--ds-danger-border); }
    .btn-danger:hover  { background: var(--ds-danger-soft); }
    .btn-sm  { min-height: 32px; padding: 0 12px; font-size: .8125rem; }
    .btn-icon { width: 32px; padding: 0; }

    /* ── Status labels ──────────────────────────────────── */
    .pill {
      display: inline-flex; align-items: center;
      padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary);
      font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }
    .pill-ok      { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .pill-warning { background: var(--ds-warning-soft); border-color: var(--ds-warning-border); color: var(--ds-warning-text); }
    .pill-dim     { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--ds-text-secondary); }

    /* ── Empty ──────────────────────────────────────────── */
    .empty-state {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; text-align: center;
      padding: 32px 20px; gap: 8px;
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius);
    }
    .empty-icon { width: 28px; height: 28px; margin-bottom: 4px; color: var(--muted); }
    .empty-state h3 { font-size: .9375rem; font-weight: 600; overflow-wrap: anywhere; }
    .empty-state p  { font-size: .875rem; color: var(--muted); max-width: 360px; line-height: 1.5; }
    .empty-state .btn { margin-top: 8px; }

    /* ── Dropdown Menu ──────────────────────────────────── */
    .dropdown { position: relative; }
    .dropdown-menu {
      position: absolute; right: 0; top: calc(100% + 6px); z-index: 200;
      background: var(--surface); border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm); min-width: 180px; max-width: calc(100vw - 24px);
      padding: 4px;
      box-shadow: var(--ds-shadow-md);
      display: none; flex-direction: column;
    }
    .dropdown.open .dropdown-menu { display: flex; }
    .dropdown-item {
      min-height: 34px; padding: 0 10px; border-radius: var(--radius-xs);
      font: 500 .8125rem/1.2 var(--ds-font-sans);
      color: var(--ds-text-secondary); cursor: pointer; text-decoration: none;
      display: flex; align-items: center; gap: 8px;
      transition: background .12s ease, color .12s ease;
      background: none; border: none; width: 100%; text-align: left;
    }
    .dropdown-item:hover { background: var(--surface2); color: var(--text); }
    .dropdown-item.danger { color: var(--ds-danger-text); }
    .dropdown-item.danger:hover { background: var(--ds-danger-soft); }
    .dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }

    @media (max-width: 960px) {
      .topbar-search { display: none; }
    }
    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 0 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .content { padding: 20px 16px 32px; }
      .topbar { padding: 0 16px; }
      .page-header { flex-direction: column; align-items: flex-start; }
      .page-header > div { flex-basis: auto; }
      .page-header > .btn { width: 100%; }
      .toolbar { flex-direction: column; align-items: stretch; }
      .toolbar .search-box { max-width: none; min-width: 0; }
      .filter-tabs { width: 100%; }
      .filter-tab { flex: 1 1 0; justify-content: center; }
      .class-card-header { padding: 16px 16px 12px; }
      .class-card-body { padding: 0 16px; }
      .class-stat { padding: 12px 16px; }
      .class-card-footer { padding: 12px 16px; }
    }
    @media (max-width: 560px) {
      .stats-strip { grid-template-columns: minmax(0, 1fr); gap: 8px; }
      .strip-card { padding: 12px 16px; }
      .strip-info { flex-direction: row; align-items: baseline; justify-content: space-between; gap: 12px; }
      .strip-value { font-size: 1.25rem; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'My Classes', 'pageDescription' => 'Manage your classes, join codes, and enrolled students.'])
</head>
<body>
  @include('partials.instructor-sidebar')

@php
    use Illuminate\Support\Facades\Auth;

    $instructor = Auth::user();
    $stripes = ['stripe-blue', 'stripe-purple', 'stripe-green', 'stripe-amber'];
    $hasAnyClasses = ((int) $totalActive + (int) $totalArchived) > 0;

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
      <h1 class="ds-page-title">My Classes</h1>

      <form method="GET" action="{{ route('instructor.classes.index') }}" class="topbar-search">
        @if($showArchived)
          <input type="hidden" name="archived" value="1" />
        @endif
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" name="search" placeholder="Search classes…" value="{{ request('search') }}" />
      </form>

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
        @if ($hasAnyClasses)
          <a href="{{ route('instructor.classes.create') }}" class="btn btn-accent">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Create New Class
          </a>
        @endif
      </div>

      {{-- Stats Strip --}}
      <div class="stats-strip">
        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Active Classes</div>
            <div class="strip-value">{{ $totalActive }}</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Archived Classes</div>
            <div class="strip-value">{{ $totalArchived }}</div>
          </div>
        </div>

        <div class="strip-card">
          <div class="strip-info">
            <div class="strip-label">Total Students (this page)</div>
            <div class="strip-value">{{ $studentCounts }}</div>
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
                    @if($class->section) {{ $class->section }}, @endif
                    @if($class->subject_code) {{ $class->subject_code }}, @endif
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
                          onsubmit="return confirm('Permanently delete this class? This cannot be undone.')">
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
