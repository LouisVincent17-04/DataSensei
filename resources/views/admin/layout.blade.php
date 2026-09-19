<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Workspace') — DataSensei</title>
<style>
    /* Admin workspace. Colours, type, radius and spacing come from the shared
       design system (partials.design-system); this block only lays out the
       admin screens and names their components. */
    :root {
      --accent2:#3b82f6;
      --accent3:#10b981;
      --accent4:#f59e0b;
      --warn:#ef4444;
    }

    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    html, body { min-height:100%; }

    body {
      font-family:var(--ds-font-sans, 'Inter', Arial, Helvetica, sans-serif);
      background:var(--bg);
      color:var(--text);
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    a { color:inherit; text-decoration:none; }
    button, input, select, textarea { font:inherit; }

    .admin-shell { display:flex; min-height:100vh; }
    .main { flex:1; min-width:0; display:flex; flex-direction:column; }

    /* ── Title bar ── */
    .topbar {
      position:sticky;
      top:0;
      z-index:50;
      min-height:60px;
      flex-shrink:0;
      display:flex;
      align-items:center;
      gap:12px;
      padding:0 32px;
      background:var(--bg);
      border-bottom:1px solid var(--border);
    }

    .topbar h1 { flex:1; min-width:0; color:var(--text); }

    .topbar-btn {
      width:36px;
      height:36px;
      flex:0 0 36px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-sm);
      background:var(--surface);
      color:var(--muted);
      cursor:pointer;
      transition:color .12s ease, border-color .12s ease, background .12s ease;
    }

    .topbar-btn:hover { color:var(--text); background:var(--surface2); }
    .mobile-menu-btn { display:none; }

    .content { flex:1; padding:28px 32px 48px; }
    .wrap { width:100%; max-width:1440px; margin:0 auto; }

    /* Page description under the title bar: plain text, no box. */
    .page-intro { margin:0 0 24px; }
    .page-intro .eyebrow { display:none; }
    .page-intro p { max-width:80ch; color:var(--muted); font-size:.875rem; line-height:1.55; }

    /* ── Alerts ── */
    .notice {
      margin-bottom:16px;
      padding:12px 16px;
      border:1px solid var(--ds-success-border);
      border-radius:var(--radius-sm);
      background:var(--ds-success-soft);
      color:#d1fae5;
      font-size:.875rem;
      line-height:1.55;
    }
    .notice.error { border-color:var(--ds-danger-border); background:var(--ds-danger-soft); color:#fee2e2; }
    .notice ul { margin:8px 0 0 18px; }

    /* ── Dashboard lead-in: plain text and actions, no banner ── */
    .welcome-banner {
      margin-bottom:24px;
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:16px;
    }
    .welcome-text { min-width:0; flex:1 1 360px; }
    .welcome-text h2 { margin-bottom:4px; color:var(--text); font-size:1rem; font-weight:600; line-height:1.35; }
    .welcome-text p { max-width:72ch; color:var(--muted); font-size:.875rem; line-height:1.55; }
    .welcome-cta { display:flex; flex-wrap:wrap; gap:8px; margin-top:0; }

    /* ── Buttons ── */
    .btn {
      min-height:var(--ds-control-h);
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:0 16px;
      border:1px solid var(--accent);
      border-radius:var(--radius-sm);
      background:var(--accent);
      color:#fff;
      font-size:.875rem;
      font-weight:500;
      line-height:1.2;
      cursor:pointer;
      white-space:nowrap;
      transition:background .12s ease, border-color .12s ease, color .12s ease;
    }

    .btn:hover { background:var(--accent-hover); border-color:var(--accent-hover); }
    .btn.secondary { border-color:var(--ds-border-strong); background:var(--surface2); color:var(--text); }
    .btn.secondary:hover { background:var(--ds-surface-hover); }
    .btn.danger { border-color:var(--ds-danger-border); background:transparent; color:var(--ds-danger-text); }
    .btn.danger:hover { border-color:var(--ds-danger); background:var(--ds-danger-soft); }
    .btn.green { border-color:var(--ds-success-border); background:transparent; color:var(--ds-success-text); }
    .btn.green:hover { border-color:var(--ds-success); background:var(--ds-success-soft); }
    .btn.small { min-height:var(--ds-control-h-sm); padding:0 12px; font-size:.8125rem; }
    .btn:disabled, .btn[aria-disabled="true"] { opacity:.55; }
    .action-row { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

    /* ── Summary figures ── */
    .grid { display:grid; gap:16px; }
    .grid.cards, .stats-row { grid-template-columns:repeat(4,minmax(0,1fr)); margin-bottom:24px; }

    .stat {
      min-width:0;
      padding:16px 20px;
      display:flex;
      flex-direction:column;
      gap:6px;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--surface);
    }

    .stat-header { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .stat .label { color:var(--muted); font-size:.8125rem; font-weight:500; line-height:1.35; }
    .stat-icon { display:none; }
    .stat .value { color:var(--text); font-size:1.625rem; font-weight:700; letter-spacing:-.02em; line-height:1.15; font-variant-numeric:tabular-nums; }
    .stat .sub { color:var(--muted); font-size:.75rem; line-height:1.45; }
    .stat-bar { display:none; }

    /* ── Panels ── */
    .split, .grid-2 { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:24px; }

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
      padding:16px 20px;
      display:flex;
      align-items:center;
      justify-content:space-between;
      flex-wrap:wrap;
      gap:12px;
      border-bottom:1px solid var(--border);
    }

    .panel-heading { min-width:0; flex:1 1 240px; }
    .panel-title, .card-title { color:var(--text); font-size:.9375rem; font-weight:600; line-height:1.35; }
    .panel-subtitle, .card-subtitle { margin-top:2px; color:var(--muted); font-size:.8125rem; line-height:1.5; }
    .panel-body, .card-body { flex:1; padding:20px; }

    /* ── Forms ── */
    .toolbar { display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap; padding:16px 20px; }
    .toolbar.compact { padding:0; }
    .toolbar .field { flex:1 1 180px; }
    .toolbar .input.small, .toolbar .select.small { width:auto; min-width:170px; flex:1 1 170px; }
    .form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .form-grid.three { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .field { min-width:0; }
    .field label { display:block; margin-bottom:6px; color:var(--ds-text-secondary); font-size:.8125rem; font-weight:500; line-height:1.35; }

    .input, .select, .textarea {
      width:100%;
      min-height:var(--ds-control-h);
      padding:8px 12px;
      border:1px solid var(--ds-input-border);
      border-radius:var(--radius-sm);
      outline:0;
      background:var(--surface3);
      color:var(--text);
      font-size:.875rem;
      transition:border-color .12s ease, box-shadow .12s ease;
    }
    .textarea { min-height:96px; resize:vertical; line-height:1.55; }
    .input::placeholder, .textarea::placeholder { color:var(--dim); }
    .input:focus, .select:focus, .textarea:focus { border-color:var(--accent); box-shadow:var(--ds-focus-ring); }

    /* ── Tables ── */
    .table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; border-collapse:collapse; }
    th, td { padding:12px 16px; border-bottom:1px solid var(--border); text-align:left; vertical-align:middle; }
    th { padding-top:10px; padding-bottom:10px; color:var(--muted); background:var(--surface3); font-size:.75rem; font-weight:600; white-space:nowrap; }
    td { color:var(--ds-text-secondary); font-size:.875rem; line-height:1.5; }
    td strong { color:var(--text); font-weight:600; }
    tbody tr:last-child td { border-bottom:0; }
    tbody tr:hover td { background:rgba(255,255,255,.02); }
    .compact-table { min-width:0 !important; }
    .dim { color:var(--dim); }
    .empty-cell { padding:32px 20px; text-align:center; color:var(--muted); }

    .badge {
      display:inline-flex;
      align-items:center;
      gap:4px;
      padding:2px 8px;
      border:1px solid var(--ds-border-strong);
      border-radius:var(--radius-xs);
      background:var(--surface2);
      color:var(--ds-text-secondary);
      font-size:.75rem;
      font-weight:600;
      line-height:1.4;
      white-space:nowrap;
    }
    .badge.info { color:var(--ds-accent-text); border-color:var(--ds-accent-border); background:var(--ds-accent-soft); }
    .badge.active { color:var(--ds-success-text); border-color:var(--ds-success-border); background:var(--ds-success-soft); }
    .badge.disabled { color:var(--ds-danger-text); border-color:var(--ds-danger-border); background:var(--ds-danger-soft); }
    /* Counts and notes beside a panel title read as plain text, not as a status. */
    .panel-head .badge.info, .panel-header .badge.info { padding:0; border:0; background:none; color:var(--muted); font-size:.8125rem; font-weight:500; }

    .pagination { padding:12px 16px; border-top:1px solid var(--border); }

    /* ── Collapsible management editors ── */
    .section-anchor { scroll-margin-top:84px; }
    .management-list { display:grid; gap:8px; padding:16px; }
    .management-item {
      overflow:hidden;
      border:1px solid var(--border);
      border-radius:var(--radius);
      background:var(--surface3);
      transition:border-color .12s ease, background .12s ease;
    }
    .management-item:hover { border-color:var(--border-hover); }
    .management-item[open] { border-color:var(--ds-accent-border); background:var(--surface); }
    .management-item > summary {
      display:grid;
      grid-template-columns:minmax(200px,1.5fr) minmax(160px,1fr) minmax(110px,.55fr) auto;
      align-items:center;
      gap:16px;
      padding:14px 16px;
      list-style:none;
      cursor:pointer;
      user-select:none;
    }
    .management-item > summary::-webkit-details-marker { display:none; }
    .management-item[open] > summary { border-bottom:1px solid var(--border); }
    .management-main { min-width:0; }
    .management-main strong { display:block; overflow:hidden; color:var(--text); font-size:.875rem; font-weight:600; text-overflow:ellipsis; white-space:nowrap; }
    .management-main span, .management-meta { display:block; margin-top:2px; color:var(--muted); font-size:.75rem; line-height:1.45; overflow-wrap:anywhere; }
    .management-metric { color:var(--muted); font-size:.8125rem; line-height:1.45; }
    .management-metric strong { display:block; color:var(--text); font-size:.875rem; font-weight:600; }
    .management-toggle {
      display:inline-flex;
      align-items:center;
      gap:8px;
      justify-self:end;
      color:var(--ds-accent-text);
      font-size:.8125rem;
      font-weight:500;
      white-space:nowrap;
    }
    .management-toggle::after { content:'+'; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; border:1px solid var(--ds-border-strong); border-radius:var(--radius-xs); background:var(--surface2); color:var(--text); font-size:1rem; line-height:1; }
    .management-item[open] .management-toggle::after { content:'−'; }
    .management-editor { padding:16px; background:var(--surface); }
    .management-editor .form-grid { gap:16px; }
    .management-editor .textarea { min-height:78px; }
    .management-editor .action-row { margin-top:16px; justify-content:flex-end; }
    .summary-status { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .color-preview { width:10px; height:10px; border-radius:50%; border:1px solid rgba(255,255,255,.22); background:var(--swatch,#3b82f6); }

    /* ── Dashboard figures ── */
    .chart-bars {
      height:180px;
      display:grid;
      grid-template-columns:repeat(14,minmax(8px,1fr));
      align-items:end;
      gap:8px;
      padding:12px 12px 32px;
      border:1px solid var(--border);
      border-radius:var(--radius-sm);
      background:var(--surface3);
    }
    .chart-bars .bar { min-height:6px; position:relative; border-radius:3px 3px 0 0; background:var(--accent); }
    .chart-bars .bar span { position:absolute; left:50%; bottom:-24px; transform:translateX(-50%); color:var(--dim); font-size:.6875rem; white-space:nowrap; }
    /* Every other date, so neighbouring labels never collide. */
    .chart-bars .bar:nth-child(even) span { display:none; }

    .health { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; padding:20px; }
    .health-card { padding:14px 16px; border:1px solid var(--border); border-left-width:3px; border-radius:var(--radius-sm); background:var(--surface3); }
    .health-card .label { color:var(--muted); font-size:.8125rem; font-weight:500; }
    .health-card .value { margin-top:4px; color:var(--text); font-size:1rem; font-weight:600; overflow-wrap:anywhere; }
    .health-card .note { margin-top:6px; color:var(--muted); font-size:.75rem; line-height:1.45; }
    .health-card.ok { border-left-color:var(--accent3); }
    .health-card.warning { border-left-color:var(--accent4); }
    .health-card.danger { border-left-color:var(--warn); }

    .quick-actions { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; padding:20px; }
    .quick-action { padding:14px 16px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--surface3); transition:border-color .12s ease, background .12s ease; }
    .quick-action:hover { border-color:var(--border-hover); background:var(--surface2); }
    .quick-action strong { display:block; color:var(--text); font-size:.875rem; font-weight:600; }
    .quick-action span { display:block; margin-top:4px; color:var(--muted); font-size:.8125rem; line-height:1.5; }

    .sidebar-overlay { display:none; position:fixed; inset:0; z-index:950; background:var(--ds-overlay); }
    .sidebar-overlay.open { display:block; }

    @media (max-width:1200px) {
      .grid.cards, .stats-row { grid-template-columns:repeat(2,minmax(0,1fr)); }
      .health, .quick-actions { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:1000px) {
      .split, .grid-2 { grid-template-columns:1fr; gap:0; }
      .form-grid.three { grid-template-columns:repeat(2,minmax(0,1fr)); }
    }

    @media (max-width:900px) {
      .topbar { min-height:56px; padding:0 16px; gap:8px; }
      .mobile-menu-btn { display:flex; }
      .content { padding:20px 16px 40px; }
    }

    @media (max-width:640px) {
      .grid.cards, .stats-row, .health, .quick-actions, .form-grid, .form-grid.three { grid-template-columns:1fr; }
      /* Wide fields set grid-column inline for the desktop grid. */
      .form-grid > * { grid-column:auto !important; }
      .welcome-cta, .welcome-cta .btn { width:100%; }
      .toolbar { align-items:stretch; padding:16px; }
      .toolbar .field, .toolbar .input.small, .toolbar .select.small, .toolbar .btn { width:100%; flex:1 1 100%; }
      .panel-head, .panel-header, .card-header { align-items:flex-start; padding:14px 16px; }
      .panel-body, .card-body { padding:16px; }
      .health, .quick-actions { padding:16px; }
      .chart-bars { gap:4px; padding-left:8px; padding-right:8px; }
      .chart-bars .bar span { display:none; }
      .management-list { padding:8px; }
      .management-item > summary { grid-template-columns:1fr auto; gap:8px; }
      .management-item > summary .management-metric,
      .management-item > summary .summary-status { grid-column:1 / -1; }
      .management-toggle { grid-column:2; grid-row:1; }
    }
  </style>

  @stack('head')
    @include('partials.page-head', ['pageDescription' => 'Administration tools for DataSensei content and accounts.'])
</head>
<body>
  <div class="sidebar-overlay" id="admin-sidebar-overlay" aria-hidden="true"></div>

  <div class="admin-shell">
    {{-- Superadmins reach some of these pages (the module library) from their own
         sidebar, so the chrome follows the account rather than the URL. --}}
    @include('partials.role-sidebar')

    <div class="main">
      <header class="topbar">
        <button class="topbar-btn mobile-menu-btn" id="admin-menu-button" type="button" aria-label="Open admin navigation" aria-expanded="false">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>

        <h1 class="ds-page-title">@yield('page_title', 'Overview')</h1>

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
      // Only the admin sidebar carries the id; other roles render their own.
      const sidebar = document.getElementById('admin-sidebar')
        || document.getElementById('institution-admin-sidebar')
        || document.querySelector('.admin-shell .sidebar');
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
          if (window.innerWidth <= 900) closeSidebar();
        });
      });

      window.addEventListener('resize', function () {
        if (window.innerWidth > 900) closeSidebar();
      });
    });
  </script>

  @stack('scripts')
</body>
</html>
