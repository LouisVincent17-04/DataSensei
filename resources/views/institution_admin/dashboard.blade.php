<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Institution Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
      --border:      #1e2f47;
      --border-hover:#2c4168;
      --accent:      #3b82f6;
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

    /* ── SIDEBAR ── */
    .sidebar { width: 220px; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; }
    .sidebar-logo { padding: 20px 20px 16px; border-bottom: 1px solid var(--border); }
    .sidebar-logo span { font-size: 1rem; font-weight: 700; color: var(--text); letter-spacing: -0.02em; }
    .sidebar-logo small { display: block; font-size: 0.7rem; color: var(--muted); margin-top: 2px; font-weight: 500; }
    .sidebar-nav { padding: 12px 10px; flex: 1; display: flex; flex-direction: column; gap: 2px; }
    .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; color: var(--muted); text-decoration: none; transition: background 0.12s, color 0.12s; }
    .nav-item:hover, .nav-item.active { background: var(--surface2); color: var(--text); }
    .nav-item.active { color: var(--accent); }
    .nav-item svg { flex-shrink: 0; }
    .nav-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--dim); padding: 12px 12px 4px; }
    .badge { margin-left: auto; background: rgba(239,68,68,0.15); color: var(--warn); font-size: 0.65rem; font-weight: 700; padding: 2px 7px; border-radius: 20px; }
    .sidebar-footer { padding: 12px 10px; border-top: 1px solid var(--border); }
    .logout-btn { width: 100%; display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; color: var(--muted); background: none; border: none; cursor: pointer; transition: background 0.12s, color 0.12s; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); color: var(--warn); }

    /* ── TOPBAR ── */
    .topbar { height: 64px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; letter-spacing: -0.01em; }
    .topbar-meta { font-size: 0.8rem; color: var(--muted); }

    /* ── CONTENT ── */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 28px; }

    /* ── FLASH ── */
    .flash { padding: 12px 20px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; border-left: 3px solid; }
    .flash-success { background: rgba(16,185,129,0.08); border-color: var(--accent3); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,0.08); border-color: var(--warn); color: var(--warn); }

    /* ── WELCOME ── */
    .welcome-banner { background: var(--surface); border: 1px solid var(--border); border-left: 4px solid var(--accent3); border-radius: var(--radius); padding: 28px 32px; display: flex; align-items: center; justify-content: space-between; gap: 24px; }
    .welcome-text h2 { font-size: 1.5rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 6px; }
    .welcome-text p  { font-size: 0.875rem; color: var(--muted); }

    /* ── INSTITUTION CODE FLIP BOX ── */
    .inst-code-box {
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 16px 20px;
      text-align: center;
      flex-shrink: 0;
      min-width: 180px;
      transition: border-color 0.2s;
    }
    .inst-code-box:hover { border-color: var(--border-hover); }
    .inst-code-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--muted); margin-bottom: 8px; }
    .inst-code-value {
      font-size: 1.6rem;
      font-weight: 700;
      letter-spacing: 0.2em;
      color: var(--accent2);
      font-variant-numeric: tabular-nums;
      font-family: 'Courier New', monospace;
      min-height: 2rem;
      transition: filter 0.3s, opacity 0.3s;
    }
    .inst-code-value.hidden {
      filter: blur(6px);
      opacity: 0.4;
      user-select: none;
    }
    .inst-code-actions { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 10px; }
    .code-toggle-btn {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 0.7rem; font-weight: 600;
      padding: 4px 10px; border-radius: var(--radius-sm);
      background: rgba(139,92,246,0.12); color: var(--accent2);
      border: 1px solid rgba(139,92,246,0.25);
      cursor: pointer; transition: opacity 0.15s;
    }
    .code-toggle-btn:hover { opacity: 0.8; }
    .code-copy-btn {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 0.7rem; font-weight: 600;
      padding: 4px 10px; border-radius: var(--radius-sm);
      background: rgba(59,130,246,0.1); color: var(--accent);
      border: 1px solid rgba(59,130,246,0.2);
      cursor: pointer; transition: opacity 0.15s;
    }
    .code-copy-btn:hover { opacity: 0.8; }
    .inst-code-hint { font-size: 0.7rem; color: var(--dim); margin-top: 8px; }

    /* ── STAT GRID ── */
    .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px; }
    .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px 24px; display: flex; flex-direction: column; gap: 8px; transition: border-color 0.15s; }
    .stat-card:hover { border-color: var(--border-hover); }
    .stat-label { font-size: 0.75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; }
    .stat-value { font-size: 2rem; font-weight: 700; letter-spacing: -0.03em; line-height: 1; }
    .stat-sub   { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    .stat-blue   { color: var(--accent);  }
    .stat-green  { color: var(--accent3); }
    .stat-amber  { color: var(--accent4); }
    .stat-red    { color: var(--warn);    }

    /* ── GRID ROW ── */
    .grid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 900px) { .grid-row { grid-template-columns: 1fr; } }

    /* ── CARD ── */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .card-header { padding: 20px 24px 0; display: flex; justify-content: space-between; align-items: flex-start; }
    .card-title  { font-size: 0.9375rem; font-weight: 600; }
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

    /* ── PILLS ── */
    .pill { display: inline-flex; align-items: center; font-size: 0.7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; }
    .pill-pending  { background: rgba(245,158,11,0.12); color: var(--accent4); border: 1px solid rgba(245,158,11,0.25); }
    .pill-approved { background: rgba(16,185,129,0.12); color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-rejected { background: rgba(239,68,68,0.08);  color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }

    /* ── AVATAR ── */
    .user-av { width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: var(--accent); flex-shrink: 0; }

    /* ── ACTION BUTTONS ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; padding: 5px 12px; border-radius: var(--radius-sm); border: none; cursor: pointer; transition: opacity 0.12s; }
    .btn:hover { opacity: 0.85; }
    .btn-approve { background: rgba(16,185,129,0.15); color: var(--accent3); border: 1px solid rgba(16,185,129,0.3); }
    .btn-reject  { background: rgba(239,68,68,0.08);  color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }
  </style>
</head>
<body>

  {{-- ── SIDEBAR ── --}}
  <aside class="sidebar">
    <div class="sidebar-logo">
      <span>DataSensei</span>
      <small>Institution Admin</small>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-label">Overview</div>
      <a href="{{ route('institution-admin.dashboard') }}" class="nav-item active">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <div class="nav-label">Applications</div>
      <a href="{{ route('institution-admin.applications.index') }}" class="nav-item">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Instructor Applications
        @if($pendingCount > 0)
          <span class="badge">{{ $pendingCount }}</span>
        @endif
      </a>
    </nav>
    <div class="sidebar-footer">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-btn">
          <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
          Log out
        </button>
      </form>
    </div>
  </aside>

  {{-- ── MAIN ── --}}
  <div class="main">
    <div class="topbar">
      <h1>Institution Admin Dashboard</h1>
      <span class="topbar-meta">{{ now()->format('l, F j, Y') }}</span>
    </div>

    <div class="content">

      @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
      @endif

      {{-- WELCOME BANNER --}}
      <div class="welcome-banner">
        <div class="welcome-text">
          <h2>Welcome back, {{ auth()->user()->name }}</h2>
          <p>Managing <strong>{{ $institution->name }}</strong> — review instructor applications and track your team.</p>
        </div>

        {{-- FLIP CODE BOX --}}
        <div class="inst-code-box">
          <div class="inst-code-label">Institution Code</div>
          <div class="inst-code-value hidden" id="inst-code-display">
            {{ $institution->institution_code }}
          </div>
          <div class="inst-code-actions">
            <button class="code-toggle-btn" id="code-toggle-btn" onclick="toggleCode()">
              <svg id="code-eye-icon" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              <span id="code-toggle-label">Show Code</span>
            </button>
            <button class="code-copy-btn" id="code-copy-btn" onclick="copyCode()" title="Copy to clipboard">
              <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/>
              </svg>
              <span id="copy-label">Copy</span>
            </button>
          </div>
          <div class="inst-code-hint">Share with instructors to apply</div>
        </div>
      </div>

      {{-- STAT CARDS --}}
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-label">Total Members</div>
          <div class="stat-value stat-blue">{{ $totalMembers }}</div>
          <div class="stat-sub">Students &amp; instructors</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Instructors</div>
          <div class="stat-value stat-green">{{ $totalInstructors }}</div>
          <div class="stat-sub">Approved &amp; active</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Pending Applications</div>
          <div class="stat-value stat-amber">{{ $pendingCount }}</div>
          <div class="stat-sub">Awaiting your review</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Rejected This Month</div>
          <div class="stat-value stat-red">{{ $rejectedCount }}</div>
          <div class="stat-sub">{{ now()->format('F Y') }}</div>
        </div>
      </div>

      {{-- GRID ROW --}}
      <div class="grid-row">

        {{-- Pending Applications --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Pending Applications</div>
              <div class="card-subtitle">Instructors waiting for your review</div>
            </div>
            <a href="{{ route('institution-admin.applications.index') }}" class="card-link">View all →</a>
          </div>
          <div class="card-body">
            <table class="tbl">
              <thead>
                <tr><th>Instructor</th><th>Applied</th><th>Actions</th></tr>
              </thead>
              <tbody>
                @forelse($pendingApplications as $app)
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                      <div>
                        <div style="font-weight:600;">{{ $app->user->name }}</div>
                        <div style="font-size:0.75rem;color:var(--muted);">{{ $app->user->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td style="color:var(--muted);font-size:0.8rem;">{{ $app->created_at->diffForHumans() }}</td>
                  <td>
                    <div style="display:flex;gap:6px;">
                      <form method="POST" action="{{ route('institution-admin.applications.approve', $app) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-approve">✓ Approve</button>
                      </form>
                      <form method="POST" action="{{ route('institution-admin.applications.reject', $app) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-reject">✕ Reject</button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" style="text-align:center;color:var(--muted);padding:24px;">No pending applications 🎉</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        {{-- Recently Approved --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Recently Approved</div>
              <div class="card-subtitle">Latest instructors added to your institution</div>
            </div>
            <a href="{{ route('institution-admin.applications.index', ['status' => 'approved']) }}" class="card-link">View all →</a>
          </div>
          <div class="card-body">
            <table class="tbl">
              <thead>
                <tr><th>Instructor</th><th>Approved by</th><th>When</th></tr>
              </thead>
              <tbody>
                @forelse($recentApproved as $app)
                <tr>
                  <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                      <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                      <div style="font-weight:600;">{{ $app->user->name }}</div>
                    </div>
                  </td>
                  <td style="font-size:0.8rem;color:var(--muted);">{{ $app->reviewer?->name ?? '—' }}</td>
                  <td style="font-size:0.8rem;color:var(--muted);">{{ $app->reviewed_at?->diffForHumans() ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" style="text-align:center;color:var(--muted);padding:24px;">No approved instructors yet.</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </div>{{-- /grid-row --}}
    </div>{{-- /content --}}
  </div>{{-- /main --}}

  <script>
    const CODE = '{{ $institution->institution_code }}';
    let codeVisible = false;

    function toggleCode() {
      codeVisible = !codeVisible;
      const display = document.getElementById('inst-code-display');
      const label   = document.getElementById('code-toggle-label');
      const icon    = document.getElementById('code-eye-icon');

      if (codeVisible) {
        display.classList.remove('hidden');
        label.textContent = 'Hide Code';
        icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M1 1l22 22"/>';
      } else {
        display.classList.add('hidden');
        label.textContent = 'Show Code';
        icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
      }
    }

    function copyCode() {
      navigator.clipboard.writeText(CODE).then(() => {
        const lbl = document.getElementById('copy-label');
        lbl.textContent = 'Copied!';
        setTimeout(() => lbl.textContent = 'Copy', 2000);
      });
    }
  </script>
</body>
</html>