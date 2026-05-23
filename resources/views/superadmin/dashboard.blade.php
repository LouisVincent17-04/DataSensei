<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Super Admin Dashboard</title>
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
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

    /* ── TOPBAR ── */
    .topbar { height: 64px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; letter-spacing: -0.01em; }
    .topbar-meta { font-size: 0.8rem; color: var(--muted); }

    /* ── CONTENT ── */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 28px; }

    /* ── WELCOME ── */
    .welcome-banner { background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--accent2); border-radius: var(--radius); padding: 28px 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .welcome-text h2 { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 6px; }
    .welcome-text p { font-size: 0.875rem; color: var(--muted); }
    .welcome-pill { background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(59,130,246,0.15)); border: 1px solid rgba(139,92,246,0.3); color: #a78bfa; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; padding: 6px 16px; border-radius: 20px; white-space: nowrap; }

    /* ── FLASH MESSAGE ── */
    .flash { padding: 12px 20px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; border-left: 3px solid; }
    .flash-success { background: rgba(16,185,129,0.08); border-color: var(--accent3); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,0.08); border-color: var(--warn); color: var(--warn); }

    /* ── STAT GRID ── */
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
    .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; display: flex; flex-direction: column; gap: 8px; transition: border-color 0.15s; }
    .stat-card:hover { border-color: var(--border-hover); }
    .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 2rem; font-weight: 700; letter-spacing: -0.03em; line-height: 1; }
    .stat-sub { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    .stat-accent  { color: var(--accent);  }
    .stat-accent2 { color: var(--accent2); }
    .stat-accent3 { color: var(--accent3); }
    .stat-accent4 { color: var(--accent4); }
    .stat-warn    { color: var(--warn);    }

    /* ── GRID ROW ── */
    .grid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 900px) { .grid-row { grid-template-columns: 1fr; } }

    /* ── CARD ── */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .card-header { padding: 20px 24px 0; display: flex; justify-content: space-between; align-items: flex-start; }
    .card-title  { font-size: 0.9375rem; font-weight: 600; color: var(--text); }
    .card-subtitle { font-size: 0.8rem; color: var(--muted); margin-top: 3px; }
    .card-body   { padding: 20px 24px 24px; }
    .card-link   { font-size: 0.8rem; color: var(--accent); text-decoration: none; font-weight: 500; }
    .card-link:hover { text-decoration: underline; }

    /* ── TABLE ── */
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl th { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dim); padding: 8px 12px; border-bottom: 1px solid var(--border); text-align: left; }
    .tbl td { font-size: 0.875rem; color: var(--text); padding: 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tr:hover td { background: var(--surface2); }

    /* ── PILL ── */
    .pill { display: inline-flex; align-items: center; font-size: 0.7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; }
    .pill-active   { background: rgba(16,185,129,0.12);  color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-disabled { background: rgba(239,68,68,0.08);   color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }
    .pill-student {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    .pill-admin {
        background: rgba(249, 115, 22, 0.12);
        color: #ea580c;
        border: 1px solid rgba(249, 115, 22, 0.25);
    }
    .pill-super-admin {
        background: rgba(147, 51, 234, 0.12);
        color: #7c3aed;
        border: 1px solid rgba(147, 51, 234, 0.25);
    }
    /* ── AVATAR ── */
    .user-av { width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: var(--accent); flex-shrink: 0; }

    /* ── TOP INSTITUTIONS ── */
    .inst-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border); }
    .inst-item:last-child { border-bottom: none; }
    .inst-icon { width: 36px; height: 36px; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .inst-name { font-size: 0.875rem; font-weight: 600; color: var(--text); }
    .inst-meta { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    .inst-count { margin-left: auto; font-size: 1.1rem; font-weight: 700; color: var(--accent2); }
  </style>
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">

    {{-- TOPBAR --}}
    <div class="topbar">
      <h1>Super Admin Dashboard</h1>
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
        <span class="welcome-pill">Super Admin</span>
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
                        <div style="font-weight:600;">{{ $user->name }}</div>
                        <div style="font-size:0.75rem;color:var(--muted);">{{ $user->email }}</div>
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
                  <td style="color:var(--muted);font-size:0.8rem;">{{ $user->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">No users yet.</td></tr>
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
              <div class="inst-icon">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="var(--accent2)" stroke-width="2">
                  <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                  <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
              </div>
              <div>
                <div class="inst-name">{{ $inst->name }}</div>
                <div class="inst-meta">
                  {{ $inst->admin_count }} admin{{ $inst->admin_count !== 1 ? 's' : '' }} ·
                  <span class="pill {{ $inst->status === 'active' ? 'pill-active' : 'pill-disabled' }}">{{ $inst->status }}</span>
                </div>
              </div>
              <div class="inst-count">{{ number_format($inst->student_count) }}</div>
            </div>
            @empty
              <p style="color:var(--muted);font-size:0.875rem;text-align:center;padding:24px 0;">No institutions yet.</p>
            @endforelse
          </div>
        </div>

      </div>{{-- /grid-row --}}

    </div>{{-- /content --}}
  </div>{{-- /main --}}

</body>
</html>