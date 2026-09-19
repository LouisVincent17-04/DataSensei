<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Institution Admin Dashboard — DataSensei</title>
@include('partials.page-heading-style')
  <style>
    /* Institution admin dashboard. Colours, type and radius come from
       partials.design-system; the sidebar comes from partials.sidebar-shell. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { min-height: 100vh; display: flex; overflow-x: hidden; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    /* ── title bar ── */
    .topbar {
      min-height: 60px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 4px 16px;
      padding: 10px 32px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
    }
    .topbar h1 { flex: 1 1 auto; min-width: 0; }
    .topbar-meta { color: var(--muted); font-size: 0.8125rem; white-space: nowrap; }

    /* ── content ── */
    .content { flex: 1; display: flex; flex-direction: column; gap: 24px; padding: 28px 32px 48px; }

    /* ── flash ── */
    .flash { padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm); font-size: 0.875rem; line-height: 1.5; }
    .flash-success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
    .flash-error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }

    /* ── lead-in with the institution code ── */
    .welcome-banner { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px 24px; }
    .welcome-text { flex: 1 1 320px; min-width: 0; }
    .welcome-text h2 { margin: 0; font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; }
    .welcome-text p { margin-top: 4px; max-width: 72ch; color: var(--muted); font-size: 0.875rem; line-height: 1.5; }
    .welcome-text p strong { color: var(--ds-text-secondary); font-weight: 600; }

    .inst-code-box {
      flex: 0 0 auto;
      min-width: 240px;
      padding: 14px 16px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
    }
    .inst-code-label { margin-bottom: 4px; color: var(--muted); font-size: 0.8125rem; font-weight: 500; }
    .inst-code-value {
      min-height: 1.75rem;
      color: var(--text);
      font-family: var(--ds-font-mono);
      font-size: 1.25rem;
      font-weight: 600;
      line-height: 1.4;
      font-variant-numeric: tabular-nums;
      overflow-wrap: anywhere;
      transition: filter 0.2s ease, opacity 0.2s ease;
    }
    /* Hidden until the admin chooses to show it. */
    .inst-code-value.hidden { filter: blur(6px); opacity: 0.4; user-select: none; }
    .inst-code-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-top: 10px; }
    .code-toggle-btn,
    .code-copy-btn {
      min-height: 32px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 0 12px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      font-size: 0.8125rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.12s ease;
    }
    .code-toggle-btn:hover,
    .code-copy-btn:hover { background: var(--ds-surface-hover); }
    .inst-code-hint { margin-top: 8px; color: var(--muted); font-size: 0.75rem; }

    /* ── summary figures ── */
    .stat-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
    .stat-card { display: flex; flex-direction: column; gap: 4px; padding: 16px 18px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .stat-label { color: var(--muted); font-size: 0.8125rem; font-weight: 500; }
    .stat-value { margin-top: 2px; font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
    .stat-sub { color: var(--muted); font-size: 0.75rem; line-height: 1.4; }
    /* Colour only where the figure is a status. */
    .stat-blue, .stat-green, .stat-red { color: var(--text); }
    .stat-amber { color: var(--ds-warning-text); }

    /* ── two lists ── */
    .grid-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; align-items: start; }

    .card { min-width: 0; overflow: hidden; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .card-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 16px; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .card-header > div { min-width: 0; flex: 1 1 auto; }
    .card-title { font-size: 0.9375rem; font-weight: 600; line-height: 1.35; }
    .card-subtitle { margin-top: 2px; color: var(--muted); font-size: 0.8125rem; line-height: 1.45; }
    .card-link { flex-shrink: 0; color: var(--ds-accent-text); font-size: 0.8125rem; font-weight: 500; text-decoration: none; white-space: nowrap; }
    .card-link:hover { text-decoration: underline; }
    .card-body { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    /* ── tables ── */
    .tbl { width: 100%; min-width: 440px; border-collapse: collapse; }
    .tbl th { padding: 10px 14px; background: var(--surface3); border-bottom: 1px solid var(--border); color: var(--muted); font-size: 0.75rem; font-weight: 600; text-align: left; white-space: nowrap; }
    .tbl td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--ds-text-secondary); font-size: 0.875rem; vertical-align: middle; }
    .tbl th:first-child, .tbl td:first-child { padding-left: 20px; }
    .tbl th:last-child, .tbl td:last-child { padding-right: 20px; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td { background: rgba(255, 255, 255, 0.02); }
    .tbl .muted-cell { color: var(--muted); font-size: 0.8125rem; white-space: nowrap; }
    .tbl .empty-cell { padding: 32px 20px; color: var(--muted); text-align: center; white-space: normal; }

    .person { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .person > div:last-child { min-width: 0; }
    .person-name { color: var(--text); font-weight: 600; line-height: 1.35; }
    .person-email { color: var(--muted); font-size: 0.75rem; overflow-wrap: break-word; }

    .user-av { width: 32px; height: 32px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--text); font-size: 0.75rem; font-weight: 600; }

    /* ── row actions ── */
    .row-actions { display: flex; flex-wrap: nowrap; gap: 8px; }
    .btn { min-height: 32px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 12px; border: 1px solid transparent; border-radius: var(--radius-sm); background: transparent; font-size: 0.8125rem; font-weight: 500; line-height: 1.2; white-space: nowrap; cursor: pointer; transition: background 0.12s ease, border-color 0.12s ease; }
    .btn-approve { border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .btn-approve:hover { background: var(--ds-success-soft); }
    .btn-reject { border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
    .btn-reject:hover { background: var(--ds-danger-soft); }

    /* The two lists sit side by side only when their tables fit. */
    @media (max-width: 1440px) {
      .grid-row { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 1100px) {
      .stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 8px 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .topbar { padding: 8px 16px; }
      .content { padding: 20px 16px 32px; gap: 20px; }
      .welcome-banner { align-items: stretch; flex-direction: column; }
      .welcome-text { flex: 0 0 auto; }
      .inst-code-box { min-width: 0; width: 100%; }
      .card-header { padding: 12px 16px; }
      .tbl th:first-child, .tbl td:first-child { padding-left: 16px; }
      .tbl th:last-child, .tbl td:last-child { padding-right: 16px; }
    }
    @media (max-width: 480px) {
      .stat-grid { grid-template-columns: minmax(0, 1fr); }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Institution Admin Dashboard', 'pageDescription' => 'Manage the instructors and classes in your institution.'])
</head>
<body>

  {{-- ── SIDEBAR ── --}}
  @include('partials.institution-admin-sidebar', ['pendingCount' => $pendingCount ?? 0])

  {{-- ── MAIN ── --}}
  <div class="main">
    <div class="topbar">
      <h1 class="ds-page-title">Institution Admin Dashboard</h1>
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
            <button type="button" class="code-toggle-btn" id="code-toggle-btn" onclick="toggleCode()">
              <svg id="code-eye-icon" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              <span id="code-toggle-label">Show Code</span>
            </button>
            <button type="button" class="code-copy-btn" id="code-copy-btn" onclick="copyCode()" title="Copy to clipboard">
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
                    <div class="person">
                      <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                      <div>
                        <div class="person-name">{{ $app->user->name }}</div>
                        <div class="person-email">{{ $app->user->email }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="muted-cell">{{ $app->created_at->diffForHumans() }}</td>
                  <td>
                    <div class="row-actions">
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
                  <td colspan="3" class="empty-cell">No applications are waiting for review.</td>
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
                    <div class="person">
                      <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                      <div class="person-name">{{ $app->user->name }}</div>
                    </div>
                  </td>
                  <td class="muted-cell">{{ $app->reviewer?->name ?? '—' }}</td>
                  <td class="muted-cell">{{ $app->reviewed_at?->diffForHumans() ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" class="empty-cell">No approved instructors yet.</td>
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
