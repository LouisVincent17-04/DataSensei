<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Super Admin Dashboard — DataSensei</title>
<style>
    /* Super admin dashboard. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --accent4: var(--ds-warning);
      --warn:    var(--ds-danger);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--ds-font-sans); background: var(--bg); color: var(--text); min-height: 100vh; display: flex; overflow-x: hidden; }
    .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    /* ── Title bar ── */
    .topbar { min-height: 60px; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 4px 16px;
      background: var(--bg); border-bottom: 1px solid var(--border); flex-shrink: 0; }
    .topbar-meta { color: var(--muted); font-size: .8125rem; }

    /* ── Content ── */
    .content { flex: 1; padding: 28px 32px 48px; display: flex; flex-direction: column; gap: 24px; }

    /* ── Lead-in ── */
    .welcome-banner { display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 16px 24px; }
    .welcome-text { min-width: 0; }
    .welcome-text h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -.01em; }
    .welcome-text p { margin-top: 4px; max-width: 72ch; color: var(--muted); font-size: .875rem; line-height: 1.5; }

    /* ── Flash ── */
    .flash { padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm); font-size: .875rem; line-height: 1.5; }
    .flash-success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: #d1fae5; }
    .flash-error   { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: #fee2e2; }

    /* ── Summary figures ── */
    .stat-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .stat-card { padding: 16px 18px; display: flex; flex-direction: column; gap: 4px;
      background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .stat-label { color: var(--muted); font-size: .8125rem; font-weight: 500; }
    .stat-value { margin-top: 2px; color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .stat-sub { color: var(--muted); font-size: .75rem; line-height: 1.4; }
    .stat-accent, .stat-accent2, .stat-accent3, .stat-accent4 { color: var(--text); }
    .stat-warn { color: var(--ds-danger-text); }

    /* ── Two columns ── */
    .grid-row { display: grid; grid-template-columns: minmax(0, 3fr) minmax(300px, 2fr); gap: 20px; align-items: start; }

    /* ── Card ── */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .card-header { padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; border-bottom: 1px solid var(--border); }
    .card-title { color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; }
    .card-subtitle { margin-top: 2px; color: var(--muted); font-size: .8125rem; line-height: 1.45; }
    .card-body { padding: 0; overflow-x: auto; }
    .card-link { flex-shrink: 0; color: var(--ds-accent-text); font-size: .8125rem; font-weight: 500; text-decoration: none; white-space: nowrap; }
    .card-link:hover { text-decoration: underline; }

    /* ── Table ── */
    .tbl { width: 100%; min-width: 520px; border-collapse: collapse; font-variant-numeric: tabular-nums; }
    .tbl th { padding: 10px 14px; background: var(--surface3); border-bottom: 1px solid var(--border); color: var(--muted);
      font-size: .75rem; font-weight: 600; text-align: left; white-space: nowrap; }
    .tbl td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--ds-text-secondary); font-size: .875rem; vertical-align: middle; }
    .tbl td:first-child { min-width: 200px; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td { background: rgba(255, 255, 255, .02); }

    /* ── Role label (plain text) and status badge ── */
    .pill { color: var(--ds-text-secondary); font-size: .875rem; white-space: nowrap; }
    .pill-active, .pill-disabled { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid; border-radius: var(--radius-xs);
      font-size: .75rem; font-weight: 600; line-height: 1.4; text-transform: capitalize; }
    .pill-active   { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .pill-disabled { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }

    /* ── Initials ── */
    .user-av { width: 32px; height: 32px; flex: 0 0 32px; display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2);
      color: var(--text); font-size: .75rem; font-weight: 600; }

    /* ── Top institutions ── */
    .inst-item { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border); }
    .inst-item:last-child { border-bottom: none; }
    .inst-item > div:first-child { min-width: 0; flex: 1 1 auto; }
    .inst-name { color: var(--text); font-size: .875rem; font-weight: 600; line-height: 1.4; overflow-wrap: anywhere; }
    .inst-meta { margin-top: 4px; display: flex; align-items: center; flex-wrap: wrap; gap: 6px 8px; color: var(--muted); font-size: .8125rem; }
    .inst-count { margin-left: auto; flex-shrink: 0; color: var(--text); font-size: .9375rem; font-weight: 600; font-variant-numeric: tabular-nums; }

    @media (max-width: 1100px) {
      .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .grid-row { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 8px 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .topbar { padding: 8px 16px; }
      .content { padding: 20px 16px 32px; gap: 20px; }
      .card-header { padding: 12px 16px; }
      .inst-item { padding: 12px 16px; }
      .stat-card { padding: 14px 16px; }
    }
    @media (max-width: 420px) {
      .stat-grid { grid-template-columns: minmax(0, 1fr); }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Super Admin Dashboard', 'pageDescription' => 'Platform administration for DataSensei.'])
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">

    {{-- TOPBAR --}}
    <div class="topbar">
      <h1 class="ds-page-title">Super Admin Dashboard</h1>
      <span class="topbar-meta">{{ now()->format('l, F j, Y') }}</span>
    </div>

    <div class="content">

      {{-- Flash --}}
      @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
      @endif

      {{-- WELCOME --}}
      <div class="welcome-banner">
        <div class="welcome-text">
          <h2>Welcome back, {{ auth()->user()->name }}</h2>
          <p>Here's a platform-wide overview. Manage users, institutions, and monitor activity from one place.</p>
        </div>
      </div>

      {{-- STAT CARDS --}}
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total Students</div>
          <div class="stat-value stat-accent">250</div>
          <div class="stat-sub">+11 this month</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Total Admins</div>
          <div class="stat-value stat-accent2">10</div>
          <div class="stat-sub">Platform administrators</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Institutions</div>
          <div class="stat-value stat-accent3">5</div>
          <div class="stat-sub">5 active</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Modules</div>
          <div class="stat-value stat-accent4">30</div>
          <div class="stat-sub">Course modules published</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Disabled Accounts</div>
          <div class="stat-value stat-warn">10</div>
          <div class="stat-sub">Require attention</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">New Registrations</div>
          <div class="stat-value stat-accent">100</div>
          <div class="stat-sub">{{ now()->format('F Y') }}</div>
        </div>
      </div>

      {{-- GRID ROW: Recent Users + Top Institutions --}}
      <div class="grid-row">

        {{-- Recent Users --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Recent Registrations</div>
              <div class="card-subtitle">Last 5 users to join the platform</div>
            </div>
            <a href="{{ route('superadmin.users.index') }}" class="card-link">View all →</a>
          </div>
          <div class="card-body">
            <table class="tbl">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Joined</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div class="user-av">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                      <div>
                        <div style="font-weight:600;color:var(--text);">{{ $user->name }}</div>
                        <div style="font-size:0.75rem;color:var(--muted);overflow-wrap:anywhere;">{{ $user->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                  <span class="pill 
                      @switch($user->role)
                          @case(1) pill-student @break
                          @case(2) pill-admin @break
                          @case(3) pill-super-admin @break
                      @endswitch
                  ">
                      @switch($user->role)
                          @case(1) Student @break
                          @case(2) Admin @break
                          @case(3) Super Admin @break
                      @endswitch
                  </span>
                  </td>
                  <td>
                    <span class="pill {{ $user->status === 'active' ? 'pill-active' : 'pill-disabled' }}">
                      {{ $user->status }}
                    </span>
                  </td>
                  <td style="color:var(--muted);font-size:0.8125rem;white-space:nowrap;">{{ $user->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:32px 20px;">No users yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Top Institutions --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Top Institutions</div>
              <div class="card-subtitle">Ranked by enrolled students</div>
            </div>
            <a href="{{ route('superadmin.institutions.index') }}" class="card-link">View all →</a>
          </div>
          <div class="card-body">
            @forelse($institutions as $inst)
            <div class="inst-item">
              <div>
                <div class="inst-name">{{ $inst->name }}</div>
                <div class="inst-meta">
                  <span>{{ $inst->admin_count }} admin{{ $inst->admin_count !== 1 ? 's' : '' }}</span>
                  <span class="pill {{ $inst->status === 'active' ? 'pill-active' : 'pill-disabled' }}">{{ $inst->status }}</span>
                </div>
              </div>
              <div class="inst-count">{{ number_format($inst->student_count) }}</div>
            </div>
            @empty
              <p style="color:var(--muted);font-size:0.875rem;text-align:center;padding:32px 20px;">No institutions yet.</p>
            @endforelse
          </div>
        </div>

      </div>{{-- /grid-row --}}

    </div>{{-- /content --}}
  </div>{{-- /main --}}

</body>
</html>