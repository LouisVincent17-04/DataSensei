<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modules — DataSensei</title>
<style>
    /* Module catalogue. Colours, type and radius come from
       partials.design-system; status colours are the shared tokens. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --accent4: var(--ds-warning);
      --warn: var(--ds-danger);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: var(--ds-font-sans);
    }

    .page-layout-wrapper { display: flex; min-height: 100vh; }

    .page-modules-main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    /* ── title bar ─────────────────────────────────────────────── */
    .page-modules-topbar {
      min-height: 60px;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 0 32px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      flex-shrink: 0;
    }

    .page-modules-topbar h1 { flex: 1 1 auto; min-width: 0; }

    .page-modules-search {
      width: min(320px, 38vw);
      min-height: 38px;
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 0 6px 0 12px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      color: var(--dim);
      transition: border-color .12s ease, box-shadow .12s ease;
    }

    .page-modules-search:focus-within { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }

    .page-modules-search input {
      width: 100%;
      min-height: 0 !important;
      height: 36px !important;
      padding: 0 !important;
      border: 0 !important;
      border-radius: 0 !important;
      outline: none;
      background: transparent !important;
      box-shadow: none !important;
      color: var(--text);
      font-family: inherit;
      font-size: .875rem;
    }

    .page-modules-search input::placeholder { color: var(--dim); }

    .page-modules-search-clear {
      width: 26px;
      height: 26px;
      flex: 0 0 26px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 0;
      border-radius: var(--radius-xs);
      background: transparent;
      color: var(--muted);
      font-size: 1rem;
      cursor: pointer;
    }

    .page-modules-search-clear:hover { background: var(--surface2); color: var(--text); }
    .page-modules-search-clear[hidden] { display: none; }

    .page-modules-topbar-btn {
      width: 36px;
      height: 36px;
      flex: 0 0 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--ds-text-secondary);
      transition: background-color .12s ease, color .12s ease;
    }

    .page-modules-topbar-btn:hover { background: var(--ds-surface-hover); color: var(--text); }

    .page-modules-content {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 24px;
      padding: 28px 32px 48px;
    }

    /* ── lead-in and summary ───────────────────────────────────── */
    .page-modules-header {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px 24px;
    }

    .page-modules-header-text { min-width: 0; flex: 1 1 320px; }
    .page-modules-header-text h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; color: var(--text); }
    .page-modules-header-text p { max-width: 72ch; margin-top: 4px; color: var(--muted); font-size: .875rem; line-height: 1.5; }

    .page-modules-summary { display: flex; align-items: stretch; gap: 20px; flex-shrink: 0; }
    .page-modules-stat { display: flex; flex-direction: column-reverse; gap: 2px; }
    .page-modules-stat .val {
      color: var(--text);
      font-size: 1.5rem;
      font-weight: 700;
      line-height: 1.2;
      letter-spacing: -.02em;
      font-variant-numeric: tabular-nums;
    }
    .page-modules-stat .lbl { color: var(--muted); font-size: .8125rem; font-weight: 500; }
    .page-modules-divider { width: 1px; background: var(--border); }

    /* ── filters ───────────────────────────────────────────────── */
    .page-modules-filter-row { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }

    .page-modules-filter-tab {
      min-height: 32px;
      padding: 0 12px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: transparent;
      color: var(--muted);
      font-family: inherit;
      font-size: .8125rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color .12s ease, border-color .12s ease, color .12s ease;
    }

    .page-modules-filter-tab:hover { background: var(--surface2); color: var(--text); }

    .page-modules-filter-tab.active {
      border-color: var(--ds-accent-border);
      background: var(--ds-accent-soft);
      color: var(--ds-accent-text);
    }

    .page-modules-filter-spacer { flex: 1; }
    .page-modules-search-status { color: var(--muted); font-size: .8125rem; font-variant-numeric: tabular-nums; }
    .page-modules-unlock-note { color: var(--muted); font-size: .8125rem; }
    .page-modules-search-status:not(:empty) + .page-modules-unlock-note { padding-left: 8px; border-left: 1px solid var(--border); }

    .page-modules-empty-search {
      padding: 32px 20px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
      color: var(--muted);
      font-size: .875rem;
      text-align: center;
    }

    /* ── flash after finishing a module ────────────────────────── */
    .page-modules-alert {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px 16px;
      border: 1px solid var(--ds-success-border);
      border-radius: var(--radius-sm);
      background: var(--ds-success-soft);
      color: var(--ds-success-text);
      font-size: .875rem;
      line-height: 1.5;
    }

    .page-modules-alert svg { flex: 0 0 auto; }

    /* ── year groups and module cards ──────────────────────────── */
    .page-modules-year-group { display: flex; flex-direction: column; gap: 12px; }

    /* The filter script hides cards and groups with the hidden attribute. */
    .page-modules-year-group[hidden],
    .page-modules-card[hidden] { display: none; }

    .page-modules-year-label {
      display: flex;
      align-items: center;
      gap: 12px;
      color: var(--text);
      font-size: 1rem;
      font-weight: 600;
      line-height: 1.35;
    }

    .page-modules-year-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    .page-modules-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }

    .page-modules-card {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      overflow: hidden;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
      transition: border-color .12s ease, background-color .12s ease;
    }

    .page-modules-card.unlocked:hover,
    .page-modules-card.completed:hover { border-color: var(--border-hover); }

    .page-modules-card.locked { background: var(--surface3); }

    /* Status is carried by the badge; the old colour stripe is not shown. */
    .page-modules-stripe { display: none; }

    .page-modules-card-body { flex: 1; display: flex; flex-direction: column; gap: 8px; padding: 20px; }
    .page-modules-card-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 22px; margin-bottom: 4px; }

    .page-modules-badge {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-xs);
      background: var(--surface2);
      color: var(--ds-text-secondary);
      font-size: .75rem;
      font-weight: 600;
      line-height: 1.4;
      white-space: nowrap;
    }

    .page-modules-badge.is-completed { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .page-modules-badge.is-inprogress { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); color: var(--ds-accent-text); }
    .page-modules-badge.is-unlocked { border-color: var(--ds-accent-border); background: transparent; color: var(--ds-accent-text); }
    .page-modules-badge.is-locked { border-color: var(--border); background: transparent; color: var(--muted); }

    .page-modules-title { padding-right: 20px; color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.4; overflow-wrap: break-word; }
    .page-modules-card.locked .page-modules-title { color: var(--ds-text-secondary); }
    .page-modules-desc { flex: 1; color: var(--muted); font-size: .8125rem; line-height: 1.55; overflow-wrap: break-word; }

    .page-modules-meta-row { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; margin-top: 4px; }
    .page-modules-meta-chip { color: var(--muted); font-size: .75rem; font-variant-numeric: tabular-nums; }
    .page-modules-meta-chip:not(:last-child)::after { content: ","; }

    .page-modules-card-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      padding: 14px 20px;
      border-top: 1px solid var(--border);
    }

    .page-modules-prog-wrap { flex: 1; min-width: 0; }
    .page-modules-prog-label { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 6px; }
    .page-modules-prog-text { color: var(--muted); font-size: .75rem; }
    .page-modules-prog-pct { color: var(--ds-text-secondary); font-size: .75rem; font-weight: 600; font-variant-numeric: tabular-nums; }
    .page-modules-prog-bar { height: 6px; overflow: hidden; border-radius: 999px; background: var(--surface2); }
    .page-modules-prog-fill { height: 100%; border-radius: inherit; transition: width .3s ease; }

    .page-modules-btn {
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      min-height: 32px;
      padding: 0 12px;
      border: 1px solid transparent;
      border-radius: var(--radius-sm);
      font-family: inherit;
      font-size: .8125rem;
      font-weight: 500;
      line-height: 1.2;
      text-decoration: none;
      white-space: nowrap;
      cursor: pointer;
      transition: background-color .12s ease, border-color .12s ease;
    }

    .page-modules-btn-start { border-color: var(--accent); background: var(--accent); color: #fff; }
    .page-modules-btn-start:hover { border-color: var(--accent-hover); background: var(--accent-hover); }
    .page-modules-btn-continue { border-color: var(--ds-border-strong); background: var(--surface2); color: var(--text); }
    .page-modules-btn-continue:hover { background: var(--ds-surface-hover); }
    .page-modules-btn-review { border-color: var(--ds-success-border); background: transparent; color: var(--ds-success-text); }
    .page-modules-btn-review:hover { background: var(--ds-success-soft); }
    .page-modules-btn-locked { border-color: var(--border); background: transparent; color: var(--muted); cursor: not-allowed; }

    /* Status mark in the card corner: a lock for locked modules, a check for completed ones. */
    .page-modules-status-overlay { position: absolute; top: 20px; right: 18px; display: flex; align-items: center; justify-content: center; }
    .page-modules-status-overlay.locked { color: var(--muted); }
    .page-modules-status-overlay.completed { color: var(--ds-success-text); }

    @media (max-width: 1200px) {
      .page-modules-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (max-width: 900px) {
      .page-modules-topbar { min-height: 56px; padding: 0 20px; }
      .page-modules-content { padding: 24px 20px 40px; }
    }

    @media (max-width: 700px) {
      .page-modules-topbar { flex-wrap: wrap; padding-top: 10px; padding-bottom: 10px; }
      .page-modules-topbar h1 { flex: 1 1 calc(100% - 96px); }
      .page-modules-search { order: 2; width: 100%; }
    }

    @media (max-width: 640px) {
      .page-modules-topbar { padding-left: 16px; padding-right: 16px; }
      .page-modules-content { padding: 20px 16px 32px; }
      .page-modules-grid { grid-template-columns: minmax(0, 1fr); }
      .page-modules-summary { width: 100%; justify-content: space-between; }
      .page-modules-filter-spacer { display: none; }
      .page-modules-search-status:not(:empty) + .page-modules-unlock-note { padding-left: 0; border-left: 0; }
      .page-modules-card-body { padding: 16px; }
      .page-modules-card-footer { padding: 12px 16px; }
      .page-modules-status-overlay { top: 16px; right: 14px; }
    }

    @media (prefers-reduced-motion: reduce) {
      .page-modules-prog-fill { transition: none; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Modules', 'pageDescription' => 'Work through DataSensei lessons and modules at your own pace.'])
</head>
<body>

  @php
    $unlockedIds = $unlockedModuleIds ?? [];
    $completedIds = $completedModuleIds ?? [];
    
    $completedLessonIds = Auth::check() ? Auth::user()->completedLessons->pluck('id')->toArray() : [];
    
    $totalModules = $modules->count();
    $completedModulesCount = count($completedIds);
    $unlockedCount = count($unlockedIds) > 0 ? count($unlockedIds) : 1; 
    
    $overallProgress = $totalModules > 0 ? round(($completedModulesCount / $totalModules) * 100) : 0;
    
    $yearLabels = [
        'Year 1' => 'First Year — Foundations',
        'Year 2' => 'Second Year — Core Methods',
        'Year 3' => 'Third Year — Advanced Analytics',
        'Year 4' => 'Fourth Year — Specialization',
    ];
  @endphp

  <div class="page-layout-wrapper">
    @include('partials.sidebar')

    <div class="page-modules-main">

      <header class="page-modules-topbar">
        <h1 class="ds-page-title">Modules</h1>
        <div class="page-modules-search">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="search" placeholder="Search by title or description" id="searchInput" autocomplete="off" aria-label="Search modules" />
          <button class="page-modules-search-clear" id="clearModuleSearch" type="button" aria-label="Clear module search" hidden>×</button>
        </div>
        <a class="page-modules-topbar-btn" href="{{ route('student.notifications.index') }}" aria-label="Open notifications">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </a>
        <a class="page-modules-topbar-btn" href="{{ route('profile') }}" aria-label="Open profile">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </a>
      </header>

      <main class="page-modules-content">

        @if(session('success'))
          <div class="page-modules-alert" role="status">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
          </div>
        @endif

        <div class="page-modules-header">
          <div class="page-modules-header-text">
            <h2>Course Modules</h2>
            <p>BS Data Science curriculum — complete modules sequentially to unlock advanced topics.</p>
          </div>
          <div class="page-modules-summary">
            <div class="page-modules-stat">
              <div class="val">{{ $unlockedCount }}</div>
              <div class="lbl">Unlocked</div>
            </div>
            <div class="page-modules-divider"></div>
            <div class="page-modules-stat">
              <div class="val">{{ $totalModules }}</div>
              <div class="lbl">Total</div>
            </div>
            <div class="page-modules-divider"></div>
            <div class="page-modules-stat">
              <div class="val">{{ $overallProgress }}%</div>
              <div class="lbl">Complete</div>
            </div>
          </div>
        </div>

        <div class="page-modules-filter-row">
          <button type="button" class="page-modules-filter-tab active" onclick="filterModules('all', this)">All</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('unlocked', this)">Unlocked</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('locked', this)">Locked</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('year1', this)">Year 1</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('year2', this)">Year 2</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('year3', this)">Year 3</button>
          <button type="button" class="page-modules-filter-tab" onclick="filterModules('year4', this)">Year 4</button>
          <div class="page-modules-filter-spacer"></div>
          <span class="page-modules-search-status" id="moduleSearchStatus" role="status" aria-live="polite"></span>
          <div class="page-modules-unlock-note">
            Complete prerequisites to unlock modules
          </div>
        </div>

        @foreach($modules->groupBy('year_level') as $year => $yearModules)
          @php
            $yearDataAttr = strtolower(str_replace(' ', '', $year));
          @endphp

          <div class="page-modules-year-group" data-year="{{ $yearDataAttr }}">
            <div class="page-modules-year-label">{{ $yearLabels[$year] ?? $year }}</div>
            <div class="page-modules-grid">

              @foreach($yearModules as $module)
                @php
                  // Progress Bar Math (Lesson Level)
                  $tLessons = $module->lessons->count();
                  $cLessons = $module->lessons->whereIn('id', $completedLessonIds)->count();
                  $progressPct = $tLessons > 0 ? round(($cLessons / $tLessons) * 100) : 0;
                  $isInProgress = ($progressPct > 0 && $progressPct < 100);
                  
                  // EXACT STATUS FROM DATABASE (Module Level)
                  $isCompleted = in_array($module->id, $completedIds);
                  $isUnlocked = ($module->order_index == 1) || in_array($module->id, $unlockedIds);
                  
                  if ($isCompleted) {
                      $statusDataAttr = 'completed';
                  } elseif ($isUnlocked) {
                      $statusDataAttr = 'unlocked';
                  } else {
                      $statusDataAttr = 'locked';
                  }
                @endphp

                <div class="page-modules-card {{ $statusDataAttr }}" data-status="{{ $statusDataAttr }}" data-year="{{ $yearDataAttr }}">
                  
                  <div class="page-modules-stripe" style="background: var({{ $isCompleted ? '--accent3' : ($isUnlocked ? '--accent' : '--dim') }})"></div>
                  
                  @if($isCompleted)
                    <div class="page-modules-status-overlay completed" title="Module Completed">
                      <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                  @elseif(!$isUnlocked)
                    <div class="page-modules-status-overlay locked">
                      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                  @endif

                  <div class="page-modules-card-body">
                    <div class="page-modules-card-top">
                      @if($isCompleted)
                        <span class="page-modules-badge is-completed">Completed</span>
                      @elseif($isInProgress)
                        <span class="page-modules-badge is-inprogress">In Progress</span>
                      @elseif($isUnlocked)
                        <span class="page-modules-badge is-unlocked">Unlocked</span>
                      @else
                        <span class="page-modules-badge is-locked">Locked</span>
                      @endif
                    </div>

                    <div class="page-modules-title">{{ $module->title }}</div>
                    <div class="page-modules-desc">{{ Str::limit($module->description, 90) }}</div>
                    
                    <div class="page-modules-meta-row">
                      <span class="page-modules-meta-chip">{{ $tLessons }} lessons</span>
                      <span class="page-modules-meta-chip">~{{ max(1, round($tLessons / 2)) }} hrs</span>
                    </div>
                  </div>

                  <div class="page-modules-card-footer">
                    <div class="page-modules-prog-wrap">
                      <div class="page-modules-prog-label">
                        <span class="page-modules-prog-text">Progress</span>
                        <span class="page-modules-prog-pct">{{ $isUnlocked ? $progressPct.'%' : '—' }}</span>
                      </div>
                      <div class="page-modules-prog-bar">
                        @php
                          $barColor = '--accent';
                          if($isCompleted) $barColor = '--accent3';
                          if(!$isUnlocked) $barColor = '--dim';
                        @endphp
                        <div class="page-modules-prog-fill" style="width:{{ $progressPct }}%; background:var({{ $barColor }})"></div>
                      </div>
                    </div>

                    @if(!$isUnlocked)
                      <button class="page-modules-btn page-modules-btn-locked" disabled>
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg> Locked
                      </button>
                    @elseif($isCompleted)
                      <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="page-modules-btn page-modules-btn-review">Review</a>
                    @elseif($isInProgress)
                      <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="page-modules-btn page-modules-btn-continue">Continue →</a>
                    @else
                      <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="page-modules-btn page-modules-btn-start">Start →</a>
                    @endif

                  </div>
                </div>
              @endforeach

            </div>
          </div>
        @endforeach

        <div class="page-modules-empty-search" id="moduleSearchEmpty" hidden>
          No modules match the current search and filter.
        </div>

      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.page-modules-prog-fill').forEach(el => {
        const target = el.style.width;
        el.style.width = '0%';
        setTimeout(() => { el.style.width = target; }, 200);
      });
    });

    let activeModuleFilter = 'all';

    function filterModules(type, btn) {
      activeModuleFilter = type;
      document.querySelectorAll('.page-modules-filter-tab').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');

      applyModuleFilters();
    }

    function applyModuleFilters() {
      const query = document.getElementById('searchInput').value.trim().toLowerCase();
      const cards = [...document.querySelectorAll('.page-modules-card')];
      const groups = document.querySelectorAll('.page-modules-year-group');
      let visibleCount = 0;

      cards.forEach(card => {
        const title = card.querySelector('.page-modules-title')?.textContent.toLowerCase() || '';
        const description = card.querySelector('.page-modules-desc')?.textContent.toLowerCase() || '';
        const matchesSearch = !query || title.includes(query) || description.includes(query);
        let matchesFilter = activeModuleFilter === 'all';

        if (activeModuleFilter === 'unlocked') {
          matchesFilter = card.dataset.status === 'unlocked' || card.dataset.status === 'completed';
        } else if (activeModuleFilter === 'locked') {
          matchesFilter = card.dataset.status === 'locked';
        } else if (activeModuleFilter.startsWith('year')) {
          matchesFilter = card.dataset.year === activeModuleFilter;
        }

        card.hidden = !(matchesSearch && matchesFilter);
        if (!card.hidden) visibleCount += 1;
      });

      groups.forEach(group => {
        group.hidden = ![...group.querySelectorAll('.page-modules-card')].some(card => !card.hidden);
      });

      document.getElementById('moduleSearchEmpty').hidden = visibleCount !== 0;
      document.getElementById('moduleSearchStatus').textContent = `${visibleCount} module${visibleCount === 1 ? '' : 's'} shown`;
      document.getElementById('clearModuleSearch').hidden = query === '';
    }

    document.getElementById('searchInput').addEventListener('input', applyModuleFilters);
    document.getElementById('clearModuleSearch').addEventListener('click', function () {
      const search = document.getElementById('searchInput');
      search.value = '';
      search.focus();
      applyModuleFilters();
    });

    applyModuleFilters();
  </script>

</body>
</html>
