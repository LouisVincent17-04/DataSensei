<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Workspace') — DataSensei</title>
  @include('partials.brand-head')
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --bg:#0d1320;
      --surface:#111c2d;
      --surface2:#1a2638;
      --surface3:#0f1928;
      --border:#1e2f47;
      --border-hover:#2c4168;
      --accent:#3b82f6;
      --accent-hover:#2563eb;
      --accent2:#8b5cf6;
      --accent3:#10b981;
      --accent4:#f59e0b;
      --warn:#ef4444;
      --text:#fafafa;
      --muted:#7f93b0;
      --dim:#3d5272;
      --radius:8px;
      --radius-sm:6px;
    }

    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    html, body { min-height:100%; }

    body {
      font-family:'Inter',sans-serif;
      background:var(--bg);
      color:var(--text);
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    a { color:inherit; text-decoration:none; }
    button, input, select, textarea { font:inherit; }

    .admin-shell { display:flex; min-height:100vh; }
    .main { flex:1; min-width:0; display:flex; flex-direction:column; overflow:hidden; }

    /* Student-panel top navigation language */
    .topbar {
      height:64px;
      flex-shrink:0;
      display:flex;
      align-items:center;
      gap:16px;
      padding:0 32px;
      background:var(--bg);
      border-bottom:1px solid var(--border);
    }

    .topbar h1 {
      flex:1;
      color:var(--text);
      font-size:1.125rem;
      font-weight:600;
      letter-spacing:-.01em;
    }

    .topbar-search {
      width:260px;
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 12px;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      background:var(--surface);
      transition:border-color .15s ease;
    }

    .topbar-search:focus-within { border-color:var(--accent); }
    .topbar-search input { width:100%; border:0; outline:0; background:none; color:var(--text); font-size:.875rem; }
    .topbar-search input::placeholder { color:var(--dim); }

    .topbar-btn {
      width:36px;
      height:36px;
      flex:0 0 36px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      background:var(--surface);
      color:var(--muted);
      cursor:pointer;
      position:relative;
      transition:all .15s ease;
    }

    .topbar-btn:hover { color:var(--text); border-color:var(--border-hover); }
    .notif-dot { position:absolute; top:-2px; right:-2px; width:8px; height:8px; border:2px solid var(--bg); border-radius:50%; background:var(--accent); }
    .mobile-menu-btn { display:none; }

    .content {
      flex:1;
      overflow-y:auto;
      padding:32px;
    }

    .wrap { width:100%; max-width:none; margin:0 auto; }

    .page-intro {
      margin-bottom:24px;
      padding:20px 24px;
      border:1px solid var(--border);
      border-left:4px solid var(--accent);
      border-radius:var(--radius);
      background:var(--surface);
    }

    .page-intro .eyebrow {
      display:block;
      margin-bottom:6px;
      color:var(--accent);
      font-size:.7rem;
      font-weight:600;
      letter-spacing:.08em;
      text-transform:uppercase;
    }

    .page-intro p { max-width:850px; color:var(--muted); font-size:.875rem; line-height:1.55; }

    /* Alerts */
    .notice {
      margin-bottom:16px;
      padding:12px 14px;
      border:1px solid rgba(16,185,129,.30);
      border-radius:var(--radius);
      background:rgba(16,185,129,.08);
      color:#a7f3d0;
      font-size:.875rem;
      line-height:1.55;
    }
    .notice.error { border-color:rgba(239,68,68,.30); background:rgba(239,68,68,.08); color:#fecaca; }
    .notice ul { margin:8px 0 0 18px; }

    /* Student dashboard-style welcome banner */
    .welcome-banner {
      margin-bottom:24px;
      padding:28px 32px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:24px;
      border:1px solid var(--border);
      border-left:4px solid var(--accent);
      border-radius:var(--radius);
      background:var(--surface);
    }

    .welcome-text h2 { margin-bottom:8px; color:var(--text); font-size:1.5rem; font-weight:700; letter-spacing:-.02em; line-height:1.2; }
    .welcome-text p { max-width:650px; color:var(--muted); font-size:.875rem; line-height:1.5; }
    .welcome-cta { display:flex; flex-wrap:wrap; gap:12px; margin-top:20px; }

    /* Buttons use the same compact treatment as the student panel */
    .btn {
      min-height:36px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:8px 16px;
      border:1px solid transparent;
      border-radius:var(--radius-sm);
      background:var(--text);
      color:var(--bg);
      font-size:.875rem;
      font-weight:500;
      cursor:pointer;
      white-space:nowrap;
      transition:all .15s ease;
      box-shadow:none;
    }

    .btn:hover { background:#e4e4e7; }
    .btn.secondary { border-color:var(--border); background:var(--surface2); color:var(--text); }
    .btn.secondary:hover { background:var(--border); }
    .btn.danger { border-color:rgba(239,68,68,.30); background:rgba(239,68,68,.08); color:#fecaca; }
    .btn.danger:hover { border-color:rgba(239,68,68,.45); background:rgba(239,68,68,.14); }
    .btn.green { border-color:rgba(16,185,129,.30); background:rgba(16,185,129,.08); color:#a7f3d0; }
    .btn.green:hover { border-color:rgba(16,185,129,.45); background:rgba(16,185,129,.14); }
    .btn.small { min-height:32px; padding:6px 12px; font-size:.75rem; }
    .action-row { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }

    /* Summary cards */
    .grid { display:grid; gap:16px; }
    .grid.cards, .stats-row { grid-template-columns:repeat(4,minmax(0,1fr)); margin-bottom:24px; }

    .stat {
      min-width:0;
      min-height:126px;
      padding:20px;
      display:flex;
      flex-direction:column;
      gap:14px;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--surface);
      transition:border-color .2s ease;
    }

    .stat:hover { border-color:var(--border-hover); }
    .stat-header { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .stat .label { color:var(--muted); font-size:.875rem; font-weight:500; line-height:1.35; }
    .stat-icon { width:32px; height:32px; display:flex; align-items:center; justify-content:center; flex:0 0 32px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--accent); }
    .stat .value { color:var(--text); font-size:1.75rem; font-weight:600; letter-spacing:-.02em; line-height:1; font-variant-numeric:tabular-nums; }
    .stat .sub { margin-top:auto; color:var(--muted); font-size:.75rem; line-height:1.4; }
    .stat-bar { height:4px; overflow:hidden; border-radius:4px; background:var(--surface2); }
    .stat-bar span { display:block; width:100%; height:100%; border-radius:4px; background:var(--accent); }
    .tone-green .stat-bar span, .tone-green .stat-icon { color:var(--accent3); }
    .tone-green .stat-bar span { background:var(--accent3); }
    .tone-purple .stat-bar span, .tone-purple .stat-icon { color:var(--accent2); }
    .tone-purple .stat-bar span { background:var(--accent2); }
    .tone-orange .stat-bar span, .tone-orange .stat-icon { color:var(--accent4); }
    .tone-orange .stat-bar span { background:var(--accent4); }
    .tone-red .stat-bar span, .tone-red .stat-icon { color:var(--warn); }
    .tone-red .stat-bar span { background:var(--warn); }

    /* Cards/panels */
    .split, .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:24px; }

    .panel, .card {
      min-width:0;
      margin-bottom:24px;
      overflow:hidden;
      display:flex;
      flex-direction:column;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--surface);
    }

    .panel-head, .panel-header, .card-header {
      min-height:72px;
      padding:20px 24px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      border-bottom:1px solid var(--border);
      background:var(--surface);
    }

    .panel-heading { min-width:0; }
    .panel-title, .card-title { color:var(--text); font-size:1rem; font-weight:600; line-height:1.35; }
    .panel-subtitle, .card-subtitle { margin-top:4px; color:var(--muted); font-size:.75rem; line-height:1.5; }
    .panel-body, .card-body { flex:1; padding:24px; }

    /* Forms */
    .toolbar { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; padding:20px 24px; }
    .toolbar.compact { padding:0; }
    .toolbar .field { flex:1 1 180px; }
    .toolbar .input.small, .toolbar .select.small { width:auto; min-width:170px; flex:1 1 170px; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .form-grid.three { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .field { min-width:0; }
    .field label { display:block; margin-bottom:7px; color:var(--dim); font-size:.68rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; }

    .input, .select, .textarea {
      width:100%;
      min-height:42px;
      padding:10px 12px;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      outline:0;
      background:var(--bg);
      color:var(--text);
      font-size:.875rem;
      transition:border-color .15s ease, box-shadow .15s ease;
    }
    .textarea { min-height:90px; resize:vertical; line-height:1.55; }
    .input::placeholder, .textarea::placeholder { color:var(--dim); }
    .input:focus, .select:focus, .textarea:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,130,246,.10); }

    /* Tables */
    .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:14px 16px; border-bottom:1px solid var(--border); text-align:left; vertical-align:middle; }
    th { color:var(--dim); background:rgba(255,255,255,.018); font-size:.7rem; font-weight:600; letter-spacing:.06em; text-transform:uppercase; white-space:nowrap; }
    td { color:var(--muted); font-size:.84rem; line-height:1.55; }
    td strong { color:var(--text); font-weight:600; }
    tbody tr:last-child td { border-bottom:0; }
    tbody tr:hover { background:rgba(255,255,255,.018); }
    .compact-table { min-width:0 !important; }
    .dim { color:var(--dim); }
    .empty-cell { padding:40px 20px; text-align:center; color:var(--muted); }

    .badge {
      display:inline-flex;
      align-items:center;
      gap:6px;
      padding:4px 8px;
      border:1px solid var(--border);
      border-radius:999px;
      background:var(--surface2);
      color:var(--text);
      font-size:.7rem;
      font-weight:600;
      white-space:nowrap;
    }
    .badge.info { color:#bfdbfe; border-color:rgba(59,130,246,.32); background:rgba(59,130,246,.08); }
    .badge.active { color:#a7f3d0; border-color:rgba(16,185,129,.32); background:rgba(16,185,129,.08); }
    .badge.disabled { color:#fecaca; border-color:rgba(239,68,68,.32); background:rgba(239,68,68,.08); }

    .pagination { padding:14px 18px; border-top:1px solid var(--border); }

    .admin-pagination {
      width:100%;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
    }
    .admin-pagination__summary { color:var(--muted); font-size:.75rem; line-height:1.5; }
    .admin-pagination__summary strong { color:var(--text); font-weight:600; }
    .admin-pagination__links { display:flex; align-items:center; flex-wrap:wrap; gap:6px; }
    .admin-page-link {
      width:32px;
      height:32px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      background:var(--surface2);
      color:var(--muted);
      font-size:.78rem;
      font-weight:600;
      line-height:1;
      transition:all .15s ease;
    }
    a.admin-page-link:hover { border-color:var(--border-hover); background:var(--border); color:var(--text); }
    .admin-page-link.is-current { border-color:rgba(59,130,246,.55); background:rgba(59,130,246,.14); color:#bfdbfe; }
    .admin-page-link.is-disabled { opacity:.42; cursor:not-allowed; }

    /* Compact, collapsible management editors */
    .section-anchor { scroll-margin-top:84px; }
    .management-list { display:grid; gap:10px; padding:16px; }
    .management-item {
      overflow:hidden;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--surface3);
      transition:border-color .15s ease, background .15s ease;
    }
    .management-item:hover { border-color:var(--border-hover); }
    .management-item[open] { border-color:rgba(59,130,246,.40); background:var(--surface); }
    .management-item > summary {
      min-height:74px;
      display:grid;
      grid-template-columns:minmax(220px,1.5fr) minmax(170px,1fr) minmax(110px,.55fr) auto;
      align-items:center;
      gap:18px;
      padding:14px 16px;
      list-style:none;
      cursor:pointer;
      user-select:none;
    }
    .management-item > summary::-webkit-details-marker { display:none; }
    .management-item[open] > summary { border-bottom:1px solid var(--border); }
    .management-main { min-width:0; }
    .management-main strong { display:block; overflow:hidden; color:var(--text); font-size:.875rem; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
    .management-main span, .management-meta { display:block; margin-top:4px; color:var(--dim); font-size:.72rem; line-height:1.45; overflow-wrap:anywhere; }
    .management-metric { color:var(--muted); font-size:.78rem; line-height:1.45; }
    .management-metric strong { display:block; color:var(--text); font-size:.84rem; font-weight:600; }
    .management-toggle {
      display:inline-flex;
      align-items:center;
      gap:8px;
      justify-self:end;
      color:var(--accent);
      font-size:.75rem;
      font-weight:600;
      white-space:nowrap;
    }
    .management-toggle::after { content:'+'; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface2); color:var(--text); font-size:1rem; line-height:1; }
    .management-item[open] .management-toggle::after { content:'−'; }
    .management-editor { padding:18px; background:rgba(13,19,32,.38); }
    .management-editor .form-grid { gap:14px; }
    .management-editor .textarea { min-height:78px; }
    .management-editor .action-row { margin-top:14px; justify-content:flex-end; }
    .summary-status { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .color-preview { width:10px; height:10px; border-radius:50%; border:1px solid rgba(255,255,255,.22); background:var(--swatch,#3b82f6); }

    /* Dashboard-specific supporting visuals */
    .chart-bars {
      height:180px;
      display:grid;
      grid-template-columns:repeat(14,minmax(8px,1fr));
      align-items:end;
      gap:8px;
      padding:12px 12px 30px;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--bg);
    }
    .chart-bars .bar { min-height:8px; position:relative; border-radius:6px 6px 2px 2px; background:linear-gradient(180deg,var(--accent),rgba(59,130,246,.30)); }
    .chart-bars .bar span { position:absolute; left:50%; bottom:-23px; transform:translateX(-50%); color:var(--dim); font-size:.62rem; white-space:nowrap; }

    .health { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; padding:24px; }
    .health-card { padding:18px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface2); }
    .health-card .label { color:var(--muted); font-size:.78rem; font-weight:500; }
    .health-card .value { margin-top:10px; color:var(--text); font-size:1.1rem; font-weight:600; overflow-wrap:anywhere; }
    .health-card .note { margin-top:6px; color:var(--dim); font-size:.72rem; line-height:1.45; }
    .health-card.ok { border-left:3px solid var(--accent3); }
    .health-card.warning { border-left:3px solid var(--accent4); }
    .health-card.danger { border-left:3px solid var(--warn); }

    .quick-actions { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; padding:24px; }
    .quick-action { padding:16px; border:1px solid var(--border); border-radius:var(--radius); background:var(--surface2); transition:all .15s ease; }
    .quick-action:hover { border-color:var(--border-hover); background:var(--border); }
    .quick-action strong { display:block; color:var(--text); font-size:.875rem; font-weight:600; }
    .quick-action span { display:block; margin-top:6px; color:var(--muted); font-size:.75rem; line-height:1.5; }

    .sidebar-overlay { display:none; position:fixed; inset:0; z-index:150; background:rgba(0,0,0,.55); backdrop-filter:blur(3px); }
    .sidebar-overlay.open { display:block; }

    ::-webkit-scrollbar { width:6px; height:6px; }
    ::-webkit-scrollbar-track { background:transparent; }
    ::-webkit-scrollbar-thumb { border-radius:4px; background:var(--surface2); }
    ::-webkit-scrollbar-thumb:hover { background:var(--dim); }

    @media (max-width:1200px) {
      .grid.cards, .stats-row { grid-template-columns:repeat(3,minmax(0,1fr)); }
      .health, .quick-actions { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:1000px) {
      .grid.cards, .stats-row { grid-template-columns:repeat(2,minmax(0,1fr)); }
      .split, .grid-2 { grid-template-columns:1fr; }
      .form-grid.three { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:700px) {
      .topbar { height:56px; padding:0 16px; gap:10px; }
      .topbar h1 { font-size:1rem; }
      .topbar-search { display:none; }
      .mobile-menu-btn { display:flex; }
      .content { padding:18px; }
      .grid.cards, .stats-row, .health, .quick-actions, .form-grid, .form-grid.three { grid-template-columns:1fr; }
      .welcome-banner { padding:22px; flex-direction:column; align-items:flex-start; }
      .welcome-text h2 { font-size:1.3rem; }
      .welcome-cta, .welcome-cta .btn { width:100%; }
      .toolbar { align-items:stretch; }
      .toolbar .field, .toolbar .input.small, .toolbar .select.small, .toolbar .btn { width:100%; flex:1 1 100%; }
      .panel-head, .panel-header, .card-header { align-items:flex-start; padding:18px; }
      .panel-body, .card-body { padding:18px; }
      .chart-bars { gap:4px; }
      .admin-pagination { align-items:flex-start; flex-direction:column; }
      .admin-pagination__links { width:100%; }
      .management-list { padding:10px; }
      .management-item > summary { grid-template-columns:1fr auto; gap:10px; }
      .management-item > summary .management-metric,
      .management-item > summary .summary-status { grid-column:1 / -1; }
      .management-toggle { grid-column:2; grid-row:1; }
      .management-editor { padding:14px; }
    }
  </style>

  @stack('head')
</head>
<body>
  <div class="sidebar-overlay" id="admin-sidebar-overlay" aria-hidden="true"></div>

  <div class="admin-shell">
    @include('partials.admin-sidebar')

    <div class="main">
      <header class="topbar">
        <button class="topbar-btn mobile-menu-btn" id="admin-menu-button" type="button" aria-label="Open admin navigation" aria-expanded="false">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>

        <h1>@yield('page_title', 'Overview')</h1>

        <div class="topbar-search">
          <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input type="text" placeholder="Search admin workspace..." aria-label="Search admin workspace">
        </div>

        <div class="topbar-btn" aria-label="Notifications">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
          <span class="notif-dot"></span>
        </div>

        <a class="topbar-btn" href="{{ route('profile') }}" aria-label="Open profile">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </a>
      </header>

      <main class="content">
        <div class="wrap">
          @if(session('success'))
            <div class="notice" role="alert">{{ session('success') }}</div>
          @endif

          @if(session('error'))
            <div class="notice error" role="alert">{{ session('error') }}</div>
          @endif

          @if($errors->any())
            <div class="notice error" role="alert">
              <strong>Fix the following:</strong>
              <ul>
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @unless(request()->routeIs('admin.dashboard'))
            <section class="page-intro">
              <span class="eyebrow">@yield('eyebrow', 'Admin Workspace')</span>
              <p>@yield('page_subtitle', 'Manage day-to-day DataSensei platform operations.')</p>
            </section>
          @endunless

          @yield('content')
        </div>
      </main>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const menuButton = document.getElementById('admin-menu-button');
      const sidebar = document.getElementById('admin-sidebar');
      const overlay = document.getElementById('admin-sidebar-overlay');

      if (!menuButton || !sidebar || !overlay) return;

      const closeSidebar = function () {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        menuButton.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      };

      const openSidebar = function () {
        sidebar.classList.add('is-open');
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden', 'false');
        menuButton.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      };

      menuButton.addEventListener('click', function () {
        sidebar.classList.contains('is-open') ? closeSidebar() : openSidebar();
      });

      overlay.addEventListener('click', closeSidebar);

      sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
          if (window.innerWidth <= 700) closeSidebar();
        });
      });

      window.addEventListener('resize', function () {
        if (window.innerWidth > 700) closeSidebar();
      });
    });
  </script>

  @stack('scripts')
</body>
</html>
