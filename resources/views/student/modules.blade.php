<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Modules</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
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

    /* ── SIDEBAR STYLES (Truncated for brevity, matches your existing) ── */
    .sidebar { width: 260px; min-height: 100vh; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
    .sidebar-logo { padding: 24px; border-bottom: 1px solid var(--border); }
    .sidebar-logo .wordmark { font-weight: 700; font-size: 1.25rem; letter-spacing: -0.025em; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .sidebar-logo .wordmark span { color: var(--accent); }
    .sidebar-logo .tagline { font-size: 0.75rem; color: var(--muted); margin-top: 4px; font-weight: 500; }
    .nav-group { padding: 24px 16px 0; }
    .nav-label { font-size: 0.75rem; font-weight: 600; color: var(--dim); letter-spacing: 0.05em; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem; font-weight: 500; color: var(--muted); transition: all 0.15s ease; text-decoration: none; margin-bottom: 2px; }
    .nav-item:hover { background: var(--surface2); color: var(--text); }
    .nav-item.active { background: var(--surface2); color: var(--text); border-left: 3px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
    .nav-item .icon { width: 18px; height: 18px; flex-shrink: 0; color: var(--muted); transition: color 0.15s ease; }
    .nav-item:hover .icon { color: var(--text); }
    .nav-item.active .icon { color: var(--accent); }
    .badge { margin-left: auto; background: var(--surface2); border: 1px solid var(--border); color: var(--text); font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
    .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s; }
    .user-card:hover { background: var(--surface2); }
    .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; color: var(--text); flex-shrink: 0; }
    .user-info .name  { font-size: 0.875rem; font-weight: 600; color: var(--text); }
    .user-info .role  { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    .logout-form { width: 100%; }
    .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s ease; text-align: left; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: var(--warn); }

    /* ── MAIN AREA ── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
    .topbar { height: 64px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; letter-spacing: -0.01em; }
    .topbar-search { display: flex; align-items: center; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 12px; gap: 10px; width: 260px; transition: border-color 0.15s; }
    .topbar-search:focus-within { border-color: var(--accent); }
    .topbar-search input { background: none; border: none; outline: none; color: var(--text); font-size: 0.875rem; font-family: inherit; width: 100%; }
    .topbar-search input::placeholder { color: var(--dim); }
    .topbar-btn { width: 36px; height: 36px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); transition: all 0.15s; position: relative; }
    .topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }
    .notif-dot { position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; border: 2px solid var(--bg); }
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 28px; }
    
    /* ── HEADERS & STATS ── */
    .page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; }
    .page-header-text h2 { font-size: 1.375rem; font-weight: 700; color: var(--text); letter-spacing: -0.02em; }
    .page-header-text p { font-size: 0.875rem; color: var(--muted); margin-top: 6px; line-height: 1.5; }
    .progress-summary { display: flex; align-items: center; gap: 20px; flex-shrink: 0; }
    .summary-stat { text-align: right; }
    .summary-stat .val { font-size: 1.5rem; font-weight: 700; color: var(--text); letter-spacing: -0.02em; font-variant-numeric: tabular-nums; line-height: 1; }
    .summary-stat .lbl { font-size: 0.7rem; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 4px; }
    .summary-divider { width: 1px; height: 36px; background: var(--border); }

    /* ── FILTER TABS ── */
    .filter-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .filter-tab { padding: 6px 14px; border-radius: var(--radius-sm); font-size: 0.8125rem; font-weight: 500; cursor: pointer; border: 1px solid var(--border); background: transparent; color: var(--muted); font-family: inherit; transition: all 0.15s; }
    .filter-tab:hover { background: var(--surface2); color: var(--text); }
    .filter-tab.active { background: var(--accent); border-color: var(--accent); color: #fff; }
    .filter-spacer { flex: 1; }
    .unlock-note { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: var(--muted); background: var(--surface); border: 1px solid var(--border); padding: 6px 12px; border-radius: var(--radius-sm); }

    /* ── GRID & CARDS ── */
    .year-group { display: flex; flex-direction: column; gap: 16px; }
    .year-label { display: flex; align-items: center; gap: 12px; font-size: 0.75rem; font-weight: 600; color: var(--dim); text-transform: uppercase; letter-spacing: 0.08em; }
    .year-label::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .module-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .module-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); display: flex; flex-direction: column; overflow: hidden; transition: border-color 0.2s, transform 0.15s; position: relative; }
    .module-card.unlocked:hover { border-color: var(--border-hover); transform: translateY(-2px); }
    .module-card.locked { opacity: 0.55; }
    .module-card.locked:hover { opacity: 0.7; }
    .module-stripe { height: 3px; width: 100%; }
    .module-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; gap: 12px; }
    .module-card-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
    
    /* Status Badges */
    .module-status-badge { display: flex; align-items: center; gap: 5px; padding: 3px 8px; border-radius: 4px; font-size: 0.6875rem; font-weight: 600; letter-spacing: 0.04em; }
    .badge-unlocked { background: rgba(16,185,129,0.12); color: var(--accent3); }
    .badge-locked { background: rgba(127,147,176,0.1); color: var(--muted); }
    .badge-inprogress { background: rgba(59,130,246,0.12); color: var(--accent); }
    
    .module-title { font-size: 0.9375rem; font-weight: 600; color: var(--text); line-height: 1.35; }
    .module-locked .module-title { color: var(--muted); }
    .module-desc { font-size: 0.8125rem; color: var(--muted); line-height: 1.55; flex: 1; }
    .module-meta-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .meta-chip { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: var(--muted); }
    .module-card-footer { padding: 14px 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    
    /* Progress Bar */
    .prog-wrap { flex: 1; }
    .prog-label { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .prog-label-text { font-size: 0.75rem; color: var(--muted); }
    .prog-label-pct { font-size: 0.75rem; font-weight: 600; color: var(--text); font-variant-numeric: tabular-nums; }
    .prog-bar { height: 5px; background: var(--surface2); border-radius: 4px; overflow: hidden; }
    .prog-fill { height: 100%; border-radius: 4px; transition: width 1.2s ease; }
    
    /* Buttons */
    .module-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: var(--radius-sm); font-size: 0.8125rem; font-weight: 500; cursor: pointer; border: 1px solid transparent; font-family: inherit; transition: all 0.15s; white-space: nowrap; flex-shrink: 0; text-decoration: none;}
    .btn-start { background: var(--accent); color: #fff; }
    .btn-start:hover { background: var(--accent-hover); }
    .btn-continue { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-continue:hover { background: var(--border); }
    .btn-locked { background: transparent; color: var(--dim); border-color: var(--border); cursor: not-allowed; }
    .btn-review { background: rgba(16,185,129,0.1); color: var(--accent3); border-color: rgba(16,185,129,0.2); }
    .btn-review:hover { background: rgba(16,185,129,0.18); }
    .lock-overlay { position: absolute; top: 14px; right: 14px; color: var(--dim); }

    /* Scrollbars */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @media (max-width: 1200px) { .module-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 900px)  { .module-grid { grid-template-columns: 1fr; } }
    @media (max-width: 700px)  { .sidebar { display: none; } }
  </style>
</head>
<body>

  @php
    // Get completed lessons from auth user
    $completedLessonIds = Auth::check() ? Auth::user()->completedLessons->pluck('id')->toArray() : [];
    
    // Overall Stats tracking
    $totalModules = $modules->count();
    $unlockedCount = 0;
    $completedModulesCount = 0;
    
    // Calculate global stats before rendering
    $prevCompleted = true; // First module is always unlocked
    foreach($modules as $m) {
        $tLessons = $m->lessons->count();
        $cLessons = $m->lessons->whereIn('id', $completedLessonIds)->count();
        $pct = $tLessons > 0 ? round(($cLessons / $tLessons) * 100) : 0;
        
        if ($prevCompleted) { $unlockedCount++; }
        if ($pct === 100 && $tLessons > 0) { $completedModulesCount++; }
        
        // Update condition for the *next* module in the loop
        $prevCompleted = ($pct === 100 && $tLessons > 0);
    }
    
    $overallProgress = $totalModules > 0 ? round(($completedModulesCount / $totalModules) * 100) : 0;
    
    // UI Labels mapping
    $yearLabels = [
        'Year 1' => 'First Year — Foundations',
        'Year 2' => 'Second Year — Core Methods',
        'Year 3' => 'Third Year — Advanced Analytics',
        'Year 4' => 'Fourth Year — Specialization',
    ];
    
    // Reset tracker for the actual UI rendering loop below
    $globalUnlockTracker = true; 
  @endphp

  @include('partials.sidebar')

  <div class="main">

    <header class="topbar">
      <h1>Modules</h1>
      <div class="topbar-search">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search modules..." id="searchInput" />
      </div>
      <div class="topbar-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span class="notif-dot"></span>
      </div>
      <div class="topbar-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      </div>
    </header>

    <main class="content">

      <div class="page-header">
        <div class="page-header-text">
          <h2>Course Modules</h2>
          <p>BS Data Science curriculum — complete modules sequentially to unlock advanced topics.</p>
        </div>
        <div class="progress-summary">
          <div class="summary-stat">
            <div class="val">{{ $unlockedCount }}</div>
            <div class="lbl">Unlocked</div>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-stat">
            <div class="val">{{ $totalModules }}</div>
            <div class="lbl">Total</div>
          </div>
          <div class="summary-divider"></div>
          <div class="summary-stat">
            <div class="val">{{ $overallProgress }}%</div>
            <div class="lbl">Complete</div>
          </div>
        </div>
      </div>

      <div class="filter-row">
        <button class="filter-tab active" onclick="filterModules('all', this)">All</button>
        <button class="filter-tab" onclick="filterModules('unlocked', this)">Unlocked</button>
        <button class="filter-tab" onclick="filterModules('locked', this)">Locked</button>
        <button class="filter-tab" onclick="filterModules('year1', this)">Year 1</button>
        <button class="filter-tab" onclick="filterModules('year2', this)">Year 2</button>
        <button class="filter-tab" onclick="filterModules('year3', this)">Year 3</button>
        <button class="filter-tab" onclick="filterModules('year4', this)">Year 4</button>
        <div class="filter-spacer"></div>
        <div class="unlock-note">
          <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Complete prerequisites to unlock modules
        </div>
      </div>

      @foreach($modules->groupBy('year_level') as $year => $yearModules)
        @php
          // Converts "Year 1" into "year1" for the JS filter
          $yearDataAttr = strtolower(str_replace(' ', '', $year));
        @endphp

        <div class="year-group" data-year="{{ $yearDataAttr }}">
          <div class="year-label">{{ $yearLabels[$year] ?? $year }}</div>
          <div class="module-grid">

            @foreach($yearModules as $module)
              @php
                // Individual Module Progress Math
                $tLessons = $module->lessons->count();
                $cLessons = $module->lessons->whereIn('id', $completedLessonIds)->count();
                $progressPct = $tLessons > 0 ? round(($cLessons / $tLessons) * 100) : 0;
                
                $isCompleted = ($progressPct === 100 && $tLessons > 0);
                $isInProgress = ($progressPct > 0 && $progressPct < 100);
                
                // Is this module unlocked?
                $isUnlocked = $globalUnlockTracker;
                $statusDataAttr = $isUnlocked ? 'unlocked' : 'locked';

                // Update tracker for the NEXT iteration
                $globalUnlockTracker = $isCompleted;
              @endphp

              <div class="module-card {{ $statusDataAttr }}" data-status="{{ $statusDataAttr }}" data-year="{{ $yearDataAttr }}">
                
                <div class="module-stripe" style="background: var({{ $isUnlocked ? '--accent3' : '--dim' }})"></div>
                
                @if(!$isUnlocked)
                  <div class="lock-overlay">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  </div>
                @endif

                <div class="module-card-body">
                  <div class="module-card-top">
                    @if($isCompleted)
                      <span class="module-status-badge badge-unlocked">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg> Completed
                      </span>
                    @elseif($isInProgress)
                      <span class="module-status-badge badge-inprogress">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg> In Progress
                      </span>
                    @elseif($isUnlocked)
                      <span class="module-status-badge badge-inprogress" style="background: rgba(245,158,11,0.12); color: var(--accent4);">
                        <svg width="8" height="8" viewBox="0 0 8 8" fill="currentColor"><circle cx="4" cy="4" r="4"/></svg> Unlocked
                      </span>
                    @else
                      <span class="module-status-badge badge-locked">Locked</span>
                    @endif
                  </div>

                  <div class="module-title">{{ $module->title }}</div>
                  <div class="module-desc">{{ Str::limit($module->description, 90) }}</div>
                  
                  <div class="module-meta-row">
                    <span class="meta-chip">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"/></svg>
                      {{ $tLessons }} lessons
                    </span>
                    <span class="meta-chip">
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                      ~{{ max(1, round($tLessons / 2)) }} hrs
                    </span>
                  </div>
                </div>

                <div class="module-card-footer">
                  <div class="prog-wrap">
                    <div class="prog-label">
                      <span class="prog-label-text">Progress</span>
                      <span class="prog-label-pct">{{ $isUnlocked ? $progressPct.'%' : '—' }}</span>
                    </div>
                    <div class="prog-bar">
                      @php
                        // Determine progress bar color
                        $barColor = '--accent';
                        if($isCompleted) $barColor = '--accent3';
                        if(!$isUnlocked) $barColor = '--dim';
                      @endphp
                      <div class="prog-fill" style="width:{{ $progressPct }}%; background:var({{ $barColor }})"></div>
                    </div>
                  </div>

                  @if(!$isUnlocked)
                    <button class="module-btn btn-locked" disabled>
                      <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg> Locked
                    </button>
                  @elseif($isCompleted)
                    <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="module-btn btn-review">Review</a>
                  @elseif($isInProgress)
                    <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="module-btn btn-continue">Continue →</a>
                  @else
                    <a href="{{ route('lesson.show', ['module' => $module->id]) }}" class="module-btn btn-start">Start →</a>
                  @endif

                </div>
              </div>
            @endforeach

          </div>
        </div>
      @endforeach

    </main>
  </div>

  <script>
    // Animate progress bars on load
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.prog-fill').forEach(el => {
        const target = el.style.width;
        el.style.width = '0%';
        setTimeout(() => { el.style.width = target; }, 200);
      });
    });

    // JS Filtering Engine
    function filterModules(type, btn) {
      document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');

      const cards  = document.querySelectorAll('.module-card');
      const groups = document.querySelectorAll('.year-group');

      cards.forEach(card => {
        let show = false;
        if (type === 'all')      show = true;
        else if (type === 'unlocked') show = card.dataset.status === 'unlocked';
        else if (type === 'locked')   show = card.dataset.status === 'locked';
        else show = card.dataset.year === type;
        card.style.display = show ? '' : 'none';
      });

      groups.forEach(group => {
        const visible = [...group.querySelectorAll('.module-card')].some(c => c.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
      });
    }

    // JS Search Engine
    document.getElementById('searchInput').addEventListener('input', function () {
      const q = this.value.toLowerCase();
      const groups = document.querySelectorAll('.year-group');

      document.querySelectorAll('.module-card').forEach(card => {
        const title = card.querySelector('.module-title')?.textContent.toLowerCase() || '';
        const desc  = card.querySelector('.module-desc')?.textContent.toLowerCase() || '';
        card.style.display = (title.includes(q) || desc.includes(q)) ? '' : 'none';
      });

      groups.forEach(group => {
        const visible = [...group.querySelectorAll('.module-card')].some(c => c.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
      });
    });
  </script>

</body>
</html>