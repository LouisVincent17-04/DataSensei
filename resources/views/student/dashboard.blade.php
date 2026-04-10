<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Student Dashboard</title>
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
      -moz-osx-font-smoothing: grayscale;
    }

    /* ── SIDEBAR ── */
    .sidebar {
      width: 260px;
      min-height: 100vh;
      background: var(--surface);
      border-right: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      flex-shrink: 0;
      position: sticky;
      top: 0;
      height: 100vh;
      overflow-y: auto;
    }

    .sidebar-logo {
      padding: 24px;
      border-bottom: 1px solid var(--border);
    }

    .sidebar-logo .wordmark {
      font-weight: 700;
      font-size: 1.25rem;
      letter-spacing: -0.025em;
      color: var(--text);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .sidebar-logo .wordmark span { color: var(--accent); }

    .sidebar-logo .tagline {
      font-size: 0.75rem;
      color: var(--muted);
      margin-top: 4px;
      font-weight: 500;
    }

    .nav-group { padding: 24px 16px 0; }

    .nav-label {
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--dim);
      letter-spacing: 0.05em;
      text-transform: uppercase;
      padding: 0 12px;
      margin-bottom: 8px;
    }

    .nav-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px 12px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--muted);
      transition: all 0.15s ease;
      text-decoration: none;
      margin-bottom: 2px;
    }

    .nav-item:hover { background: var(--surface2); color: var(--text); }

    .nav-item.active {
      background: var(--surface2);
      color: var(--text);
      border-left: 3px solid var(--accent);
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    .nav-item .icon {
      width: 18px; height: 18px;
      flex-shrink: 0;
      color: var(--muted);
      transition: color 0.15s ease;
    }

    .nav-item:hover .icon { color: var(--text); }
    .nav-item.active .icon { color: var(--accent); }

    .badge {
      margin-left: auto;
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--text);
      font-size: 0.7rem;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: 12px;
    }

    /* ── SIDEBAR FOOTER ── */
    .sidebar-footer {
      margin-top: auto;
      padding: 16px;
      border-top: 1px solid var(--border);
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .user-card {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 8px;
      border-radius: var(--radius-sm);
      cursor: pointer;
      transition: background 0.15s;
    }

    .user-card:hover { background: var(--surface2); }

    .avatar {
      width: 36px; height: 36px;
      border-radius: var(--radius-sm);
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--text);
      flex-shrink: 0;
    }

    .user-info .name  { font-size: 0.875rem; font-weight: 600; color: var(--text); }
    .user-info .role  { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }

    /* Logout */
    .logout-form { width: 100%; }

    .logout-btn {
      display: flex;
      align-items: center;
      gap: 10px;
      width: 100%;
      padding: 8px 12px;
      border-radius: var(--radius-sm);
      background: transparent;
      border: 1px solid var(--border);
      color: var(--muted);
      font-size: 0.875rem;
      font-weight: 500;
      font-family: 'Inter', sans-serif;
      cursor: pointer;
      transition: all 0.15s ease;
      text-align: left;
    }

    .logout-btn:hover {
      background: rgba(239,68,68,0.08);
      border-color: rgba(239,68,68,0.3);
      color: var(--warn);
    }

    /* ── MAIN ── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

    /* ── TOPBAR ── */
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

    .topbar h1 {
      font-size: 1.125rem;
      font-weight: 600;
      color: var(--text);
      flex: 1;
      letter-spacing: -0.01em;
    }

    .topbar-search {
      display: flex;
      align-items: center;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 8px 12px;
      gap: 10px;
      width: 260px;
      transition: border-color 0.15s;
    }

    .topbar-search:focus-within { border-color: var(--accent); }

    .topbar-search input {
      background: none; border: none; outline: none;
      color: var(--text); font-size: 0.875rem; font-family: inherit; width: 100%;
    }

    .topbar-search input::placeholder { color: var(--dim); }

    .topbar-btn {
      width: 36px; height: 36px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; color: var(--muted);
      transition: all 0.15s; position: relative;
    }

    .topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }

    .notif-dot {
      position: absolute; top: -2px; right: -2px;
      width: 8px; height: 8px;
      background: var(--accent);
      border-radius: 50%;
      border: 2px solid var(--bg);
    }

    /* ── CONTENT ── */
    .content {
      flex: 1; overflow-y: auto;
      padding: 32px;
      display: flex; flex-direction: column; gap: 24px;
    }

    /* ── WELCOME BANNER ── */
    .welcome-banner {
      background: var(--surface);
      border: 1px solid var(--border);
      border-left: 4px solid var(--accent);
      border-radius: var(--radius);
      padding: 28px 32px;
      display: flex; align-items: center;
      justify-content: space-between; gap: 24px;
    }

    .welcome-text h2 {
      font-size: 1.5rem; font-weight: 700;
      color: var(--text); line-height: 1.2;
      margin-bottom: 8px; letter-spacing: -0.02em;
    }

    .welcome-text p {
      font-size: 0.875rem; color: var(--muted);
      max-width: 500px; line-height: 1.5;
    }

    .welcome-cta { display: flex; gap: 12px; margin-top: 20px; }

    .btn {
      display: inline-flex; align-items: center; justify-content: center;
      gap: 8px; padding: 8px 16px;
      border-radius: var(--radius-sm);
      font-size: 0.875rem; font-weight: 500;
      cursor: pointer; border: 1px solid transparent;
      transition: all 0.15s; font-family: inherit;
    }

    .btn-primary  { background: var(--text); color: var(--bg); }
    .btn-primary:hover { background: #e4e4e7; }
    .btn-ghost    { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover  { background: var(--border); }



    /* ── STAT CARDS ── */
    .stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
    }

    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 20px;
      display: flex; flex-direction: column; gap: 16px;
      transition: border-color 0.2s;
    }

    .stat-card:hover { border-color: var(--border-hover); }

    .stat-header { display: flex; align-items: center; justify-content: space-between; }

    .stat-title { font-size: 0.875rem; font-weight: 500; color: var(--muted); }

    .stat-icon {
      width: 32px; height: 32px;
      border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      border: 1px solid var(--border);
      background: var(--surface2);
      color: var(--text);
    }

    .stat-main { display: flex; align-items: baseline; gap: 8px; }

    .stat-value {
      font-size: 1.75rem; font-weight: 600;
      color: var(--text); line-height: 1;
      letter-spacing: -0.02em;
      font-variant-numeric: tabular-nums;
    }



    .stat-bar {
      height: 4px; background: var(--surface2);
      border-radius: 4px; overflow: hidden; margin-top: auto;
    }

    .stat-bar-fill { height: 100%; border-radius: 4px; transition: width 1s ease; }

    /* ── GRIDS ── */
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .grid-3 { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }

    /* ── CARD ── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      display: flex; flex-direction: column;
    }

    .card-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; justify-content: space-between;
    }

    .card-title  { font-weight: 600; font-size: 1rem; color: var(--text); }
    .card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
    .card-body   { padding: 24px; flex: 1; }

    .link-sm {
      font-size: 0.875rem; font-weight: 500;
      color: var(--muted); cursor: pointer; text-decoration: none;
      transition: color 0.15s;
    }
    .link-sm:hover { color: var(--text); }

    /* ── MODULES ── */
    .module-list { display: flex; flex-direction: column; gap: 20px; }
    .module-item { display: flex; align-items: center; gap: 16px; }

    .module-icon {
      width: 40px; height: 40px;
      border-radius: var(--radius-sm);
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0; font-size: 1rem;
    }

    .module-info { flex: 1; min-width: 0; }

    .module-name {
      font-size: 0.875rem; font-weight: 500; color: var(--text);
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .module-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }

    .module-progress {
      display: flex; flex-direction: column;
      align-items: flex-end; gap: 6px; min-width: 80px;
    }

    .module-pct {
      font-size: 0.875rem; font-weight: 600;
      color: var(--text); font-variant-numeric: tabular-nums;
    }

    .prog-bar {
      width: 80px; height: 6px;
      background: var(--surface2); border-radius: 4px; overflow: hidden;
    }

    .prog-fill { height: 100%; border-radius: 4px; transition: width 1.2s ease; }

    /* ── ACTIVITY ── */
    .activity-list { display: flex; flex-direction: column; gap: 20px; }

    .activity-item {
      display: flex; gap: 16px; position: relative;
    }

    .activity-item:not(:last-child)::after {
      content: ''; position: absolute;
      top: 24px; left: 7px; bottom: -20px;
      width: 1px; background: var(--border);
    }

    .activity-dot {
      width: 15px; height: 15px; border-radius: 50%;
      background: var(--surface);
      border: 3px solid var(--border);
      margin-top: 3px; flex-shrink: 0;
      position: relative; z-index: 1;
    }

    .activity-dot.completed { border-color: var(--accent3); }
    .activity-dot.action   { border-color: var(--accent); }
    .activity-dot.warning  { border-color: var(--accent4); }
    .activity-dot.purple   { border-color: var(--accent2); }

    .activity-content { flex: 1; }
    .activity-title { font-size: 0.875rem; color: var(--text); line-height: 1.5; }
    .activity-title strong { font-weight: 600; }
    .activity-time { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }

    /* ── CHALLENGES ── */
    .challenge-list { display: flex; flex-direction: column; gap: 12px; }

    .challenge-card {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 16px;
      display: flex; align-items: center; gap: 16px;
      cursor: pointer; transition: border-color 0.15s;
    }

    .challenge-card:hover { border-color: var(--border-hover); }

    .challenge-diff {
      padding: 4px 8px; border-radius: 4px;
      font-size: 0.6875rem; font-weight: 600;
      letter-spacing: 0.05em; flex-shrink: 0;
    }

    .diff-easy   { background: rgba(16,185,129,0.1); color: var(--accent3); }
    .diff-medium { background: rgba(245,158,11,0.1); color: var(--accent4); }
    .diff-hard   { background: rgba(239,68,68,0.1);  color: var(--warn); }

    .challenge-info { flex: 1; }
    .challenge-name { font-size: 0.875rem; font-weight: 500; color: var(--text); }
    .challenge-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
    .challenge-pts  { font-weight: 600; font-size: 0.875rem; color: var(--text); }

    /* ── CHART ── */
    .chart-wrap { padding: 4px 0 0; }
    .chart-svg  { width: 100%; height: 160px; display: block; }

    .chart-legend { display: flex; gap: 20px; margin-top: 20px; justify-content: center; }

    .legend-item {
      display: flex; align-items: center; gap: 8px;
      font-size: 0.75rem; font-weight: 500; color: var(--muted);
    }

    .legend-dot { width: 10px; height: 10px; border-radius: 2px; }

    /* ── LEADERBOARD ── */
    .lb-list { display: flex; flex-direction: column; gap: 4px; }

    .lb-item {
      display: flex; align-items: center; gap: 16px;
      padding: 10px 12px; border-radius: var(--radius-sm);
      transition: background 0.15s;
    }

    .lb-item:hover { background: var(--surface2); }

    .lb-item.you {
      background: var(--surface2);
      border: 1px solid var(--border);
    }

    .lb-rank {
      font-weight: 600; font-size: 0.875rem;
      width: 20px; text-align: center;
      flex-shrink: 0; color: var(--muted);
      font-variant-numeric: tabular-nums;
    }

    .lb-rank.gold   { color: #f59e0b; }
    .lb-rank.silver { color: #a1a1aa; }
    .lb-rank.bronze { color: #cd7c3a; }

    .lb-avatar {
      width: 30px; height: 30px;
      border-radius: var(--radius-sm);
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex; align-items: center; justify-content: center;
      font-size: 0.7rem; font-weight: 600; flex-shrink: 0;
    }

    .lb-name {
      flex: 1; font-size: 0.875rem;
      font-weight: 500; color: var(--text);
      display: flex; align-items: center; gap: 8px;
    }

    .lb-you-tag {
      font-size: 0.65rem;
      background: var(--accent); color: #fff;
      border-radius: 4px; padding: 2px 6px; font-weight: 600;
    }

    .lb-pts {
      font-weight: 500; font-size: 0.875rem;
      color: var(--muted); font-variant-numeric: tabular-nums;
    }

    /* ── DEADLINES ── */
    .deadline-list { display: flex; flex-direction: column; gap: 16px; }
    .deadline-item { display: flex; align-items: center; gap: 16px; }

    .deadline-date {
      text-align: center; width: 48px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 8px 0; flex-shrink: 0;
    }

    .deadline-day {
      font-weight: 600; font-size: 1rem;
      color: var(--text); line-height: 1;
      font-variant-numeric: tabular-nums;
    }

    .deadline-month {
      font-size: 0.6875rem; font-weight: 500;
      color: var(--muted); text-transform: uppercase; margin-top: 4px;
    }

    .deadline-info { flex: 1; }
    .deadline-title { font-size: 0.875rem; font-weight: 500; color: var(--text); }
    .deadline-sub   { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }

    .deadline-pill {
      font-size: 0.6875rem; font-weight: 600;
      padding: 3px 8px; border-radius: 4px; flex-shrink: 0;
    }

    .pill-urgent { background: rgba(239,68,68,0.1);  color: var(--warn); }
    .pill-soon   { background: rgba(245,158,11,0.1); color: var(--accent4); }
    .pill-ok     { background: rgba(16,185,129,0.1); color: var(--accent3); }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) { .stats-row { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 900px)  { .grid-2, .grid-3 { grid-template-columns: 1fr; } }
    @media (max-width: 700px)  {
      .sidebar { display: none; }
      .stats-row { grid-template-columns: 1fr; }
      .welcome-banner { flex-direction: column; align-items: flex-start; }
    }
  </style>
</head>
<body>

  <!-- ── SIDEBAR ── -->
  @include('partials.sidebar')

  <!-- ── MAIN ── -->
  <div class="main">

    <header class="topbar">
      <h1>Overview</h1>
      <div class="topbar-search">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search resources, modules..." />
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

      <!-- WELCOME BANNER -->
      <section class="welcome-banner">
        <div class="welcome-text">
          @auth
            <h2>Welcome back, {{ Auth::user()->name }}!</h2>
          @else
            <h2>Welcome back, User!</h2>
          @endauth
          <p>You're making steady progress on your data science curriculum. Review your open challenges or jump back into the active module.</p>
          <div class="welcome-cta">
            <button class="btn btn-primary">Resume Module</button>
            <button class="btn btn-ghost" onclick="window.location.href='/ide'">Open IDE Workspace</button>
          </div>
        </div>

      </section>

      <!-- STAT CARDS -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Modules Completed</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">5</span>

          </div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:62%;background:var(--accent)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Average Score</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">87%</span>

          </div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:87%;background:var(--accent3)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Challenges Solved</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">142</span>

          </div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:71%;background:var(--accent2)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Total Study Time</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">38h</span>

          </div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:55%;background:var(--accent4)"></div></div>
        </div>
      </div>

      <!-- MODULES + ACTIVITY -->
      <div class="grid-2">

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Learning Modules</div>
              <div class="card-subtitle">Your progress across all 8 modules</div>
            </div>
            <a href="#" class="link-sm">View All →</a>
          </div>
          <div class="card-body">
            <div class="module-list">
              <div class="module-item">
                <div class="module-info">
                  <div class="module-name">No Active Module</div>
                  <div class="module-meta">Go To Modules</div>
                </div>
              </div>
            </div>
          </div>
{{-- 
          <div class="card-body">
            <div class="module-list">
              <div class="module-item">
                <div class="module-info">
                  <div class="module-name">No Active Module</div>
                  <div class="module-meta">Go To Modules</div>
                </div>
                <div class="module-progress">
                  <div class="module-pct">100%</div>
                  <div class="prog-bar"><div class="prog-fill" style="width:100%;background:var(--accent3)"></div></div>
                </div>
              </div>
            </div>
          </div> --}}
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Recent Activity</div>
              <div class="card-subtitle">Your latest learning events</div>
            </div>
          </div>
          <div class="card-body">
            <div class="activity-list">
              <div class="activity-item">
                <div class="activity-dot completed"></div>
                <div class="activity-content">
                  <div class="activity-title">Completed <strong>Data Cleaning with Pandas</strong> — scored 94%</div>
                  <div class="activity-time">2 hours ago</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-dot action"></div>
                <div class="activity-content">
                  <div class="activity-title">Ran Python notebook in the <strong>Online IDE</strong> — no errors</div>
                  <div class="activity-time">3 hours ago</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-dot warning"></div>
                <div class="activity-content">
                  <div class="activity-title">Attempted challenge: <strong>EDA on Housing Dataset</strong></div>
                  <div class="activity-time">Yesterday, 8:45 PM</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-dot purple"></div>
                <div class="activity-content">
                  <div class="activity-title">Received AI feedback on <strong>K-Means Clustering</strong> submission</div>
                  <div class="activity-time">Yesterday, 6:12 PM</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-dot completed"></div>
                <div class="activity-content">
                  <div class="activity-title">Completed quiz: <strong>Descriptive Statistics Basics</strong> — 10/10</div>
                  <div class="activity-time">2 days ago</div>
                </div>
              </div>
              <div class="activity-item">
                <div class="activity-dot"></div>
                <div class="activity-content">
                  <div class="activity-title">Enrolled in <strong>Automated Grading Module</strong></div>
                  <div class="activity-time">3 days ago</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- CHART + CHALLENGES | LEADERBOARD + DEADLINES -->
      <div class="grid-3">

        <div style="display:flex;flex-direction:column;gap:24px;">

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Weekly Activity</div>
                <div class="card-subtitle">Challenges solved &amp; study time</div>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-wrap">
                <svg class="chart-svg" viewBox="0 0 560 160" preserveAspectRatio="none">
                  <defs>
                    <linearGradient id="g1" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%"   stop-color="#3b82f6" stop-opacity="0.15"/>
                      <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                    </linearGradient>
                    <linearGradient id="g2" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%"   stop-color="#8b5cf6" stop-opacity="0.12"/>
                      <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0"/>
                    </linearGradient>
                  </defs>
                  <line x1="0" y1="120" x2="560" y2="120" stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="90"  x2="560" y2="90"  stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="60"  x2="560" y2="60"  stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="30"  x2="560" y2="30"  stroke="#1e2f47" stroke-width="1"/>
                  <path d="M20,100 L100,70 L180,85 L260,35 L340,55 L420,28 L500,45 L540,40 L540,120 L20,120 Z" fill="url(#g1)"/>
                  <path d="M20,110 L100,95 L180,100 L260,70 L340,82 L420,62 L500,72 L540,68 L540,120 L20,120 Z" fill="url(#g2)"/>
                  <polyline points="20,100 100,70 180,85 260,35 340,55 420,28 500,45 540,40" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linejoin="round"/>
                  <polyline points="20,110 100,95 180,100 260,70 340,82 420,62 500,72 540,68" fill="none" stroke="#8b5cf6" stroke-width="2" stroke-linejoin="round" stroke-dasharray="5,3"/>
                  <circle cx="260" cy="35" r="4" fill="#3b82f6"/>
                  <circle cx="420" cy="28" r="4" fill="#3b82f6"/>
                  <text x="20"  y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Mon</text>
                  <text x="100" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Tue</text>
                  <text x="180" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Wed</text>
                  <text x="260" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Thu</text>
                  <text x="340" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Fri</text>
                  <text x="420" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Sat</text>
                  <text x="500" y="148" fill="#3d5272" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Sun</text>
                </svg>
              </div>
              <div class="chart-legend">
                <div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div>Challenges</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--accent2)"></div>Study Time</div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Open Challenges</div>
                <div class="card-subtitle">Pending for you right now</div>
              </div>
              <a href="#" class="link-sm">All →</a>
            </div>
            <div class="card-body">
              <div class="challenge-list">
                <div class="challenge-card">
                  <span class="challenge-diff diff-easy">EASY</span>
                  <div class="challenge-info">
                    <div class="challenge-name">Basic EDA on Titanic Dataset</div>
                    <div class="challenge-meta">pandas · seaborn · ~45 min</div>
                  </div>
                  <span class="challenge-pts">+50 pts</span>
                </div>
                <div class="challenge-card">
                  <span class="challenge-diff diff-medium">MEDIUM</span>
                  <div class="challenge-info">
                    <div class="challenge-name">Build a Linear Regression Model</div>
                    <div class="challenge-meta">scikit-learn · ~90 min</div>
                  </div>
                  <span class="challenge-pts">+120 pts</span>
                </div>
                <div class="challenge-card">
                  <span class="challenge-diff diff-hard">HARD</span>
                  <div class="challenge-info">
                    <div class="challenge-name">Implement K-Means from Scratch</div>
                    <div class="challenge-meta">numpy · matplotlib · ~2 hr</div>
                  </div>
                  <span class="challenge-pts">+250 pts</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div style="display:flex;flex-direction:column;gap:24px;">

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Leaderboard</div>
                <div class="card-subtitle">Top students this month</div>
              </div>
            </div>
            <div class="card-body">
              <div class="lb-list">
                <div class="lb-item">
                  <span class="lb-rank gold">1</span>
                  <div class="lb-avatar">MR</div>
                  <span class="lb-name">Maria R.</span>
                  <span class="lb-pts">3,842 pts</span>
                </div>
                <div class="lb-item">
                  <span class="lb-rank silver">2</span>
                  <div class="lb-avatar">JC</div>
                  <span class="lb-name">Juan C.</span>
                  <span class="lb-pts">3,601 pts</span>
                </div>
                <div class="lb-item">
                  <span class="lb-rank bronze">3</span>
                  <div class="lb-avatar">AC</div>
                  <span class="lb-name">Ana C.</span>
                  <span class="lb-pts">3,210 pts</span>
                </div>
                <div class="lb-item you">
                  <span class="lb-rank">7</span>
                  <div class="lb-avatar">LS</div>
                  <span class="lb-name">
                    @if (auth()->check())
                      {{ auth()->user()->name}}.
                    
                    @else
                      User
                      
                    @endif 
                  <span class="lb-you-tag">You</span></span>
                  <span class="lb-pts">2,485 pts</span>
                </div>
                <div class="lb-item">
                  <span class="lb-rank">8</span>
                  <div class="lb-avatar">KD</div>
                  <span class="lb-name">Karl D.</span>
                  <span class="lb-pts">2,310 pts</span>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Upcoming Deadlines</div>
                <div class="card-subtitle">Don't miss these submissions</div>
              </div>
            </div>
            <div class="card-body">
              <div class="deadline-list">
                <div class="deadline-item">
                  <div class="deadline-date">
                    <div class="deadline-day">09</div>
                    <div class="deadline-month">Apr</div>
                  </div>
                  <div class="deadline-info">
                    <div class="deadline-title">Data Cleaning Assignment</div>
                    <div class="deadline-sub">Module 1 · Submit notebook</div>
                  </div>
                  <span class="deadline-pill pill-urgent">Tomorrow</span>
                </div>
                <div class="deadline-item">
                  <div class="deadline-date">
                    <div class="deadline-day">14</div>
                    <div class="deadline-month">Apr</div>
                  </div>
                  <div class="deadline-info">
                    <div class="deadline-title">EDA Mini-Project</div>
                    <div class="deadline-sub">Module 2 · Kaggle dataset</div>
                  </div>
                  <span class="deadline-pill pill-soon">6 days</span>
                </div>
                <div class="deadline-item">
                  <div class="deadline-date">
                    <div class="deadline-day">22</div>
                    <div class="deadline-month">Apr</div>
                  </div>
                  <div class="deadline-info">
                    <div class="deadline-title">Supervised Learning Quiz</div>
                    <div class="deadline-sub">Module 3 · Auto-graded</div>
                  </div>
                  <span class="deadline-pill pill-ok">14 days</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.stat-bar-fill, .prog-fill').forEach(el => {
        const target = el.style.width;
        el.style.width = '0%';
        setTimeout(() => { el.style.width = target; }, 150);
      });
    });
  </script>

</body>
</html>