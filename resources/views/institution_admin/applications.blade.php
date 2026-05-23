<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Instructor Applications</title>
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
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }

    /* ── FLASH ── */
    .flash { padding: 12px 20px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; border-left: 3px solid; }
    .flash-success { background: rgba(16,185,129,0.08); border-color: var(--accent3); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,0.08); border-color: var(--warn); color: var(--warn); }

    /* ── PAGE HEADER ── */
    .page-header { display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; }
    .page-header h2 { font-size: 1.25rem; font-weight: 700; letter-spacing: -0.02em; }
    .page-header p { font-size: 0.85rem; color: var(--muted); margin-top: 3px; }

    /* ── TAB BAR ── */
    .tab-bar { display: flex; gap: 4px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 4px; width: fit-content; }
    .tab-link {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 7px 16px; border-radius: var(--radius-sm);
      font-size: 0.8125rem; font-weight: 600; text-decoration: none;
      color: var(--muted); transition: background 0.15s, color 0.15s;
    }
    .tab-link:hover { color: var(--text); background: var(--surface2); }
    .tab-link.active { background: var(--surface2); color: var(--text); }
    .tab-count {
      font-size: 0.65rem; font-weight: 700; padding: 2px 7px;
      border-radius: 20px; line-height: 1;
    }
    .tab-link.active .tab-count-pending  { background: rgba(245,158,11,0.2);  color: var(--accent4); }
    .tab-link.active .tab-count-approved { background: rgba(16,185,129,0.2);  color: var(--accent3); }
    .tab-link.active .tab-count-rejected { background: rgba(239,68,68,0.15);  color: var(--warn); }
    .tab-count-pending  { background: rgba(245,158,11,0.1);  color: var(--accent4); }
    .tab-count-approved { background: rgba(16,185,129,0.1);  color: var(--accent3); }
    .tab-count-rejected { background: rgba(239,68,68,0.08);  color: var(--warn); }

    /* ── CARD ── */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }

    /* ── TABLE ── */
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl th { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dim); padding: 10px 16px; border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap; }
    .tbl td { font-size: 0.875rem; color: var(--text); padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tr:hover td { background: var(--surface2); }

    /* ── AVATAR ── */
    .user-av { width: 34px; height: 34px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; color: var(--accent); flex-shrink: 0; }

    /* ── PILLS ── */
    .pill { display: inline-flex; align-items: center; font-size: 0.7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; }
    .pill-pending  { background: rgba(245,158,11,0.12); color: var(--accent4); border: 1px solid rgba(245,158,11,0.25); }
    .pill-approved { background: rgba(16,185,129,0.12); color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-rejected { background: rgba(239,68,68,0.08);  color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }

    /* ── BUTTONS ── */
    .btn { display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; padding: 5px 12px; border-radius: var(--radius-sm); border: none; cursor: pointer; transition: opacity 0.12s; white-space: nowrap; }
    .btn:hover { opacity: 0.8; }
    .btn-approve { background: rgba(16,185,129,0.15); color: var(--accent3); border: 1px solid rgba(16,185,129,0.3); }
    .btn-reject  { background: rgba(239,68,68,0.08);  color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }

    /* ── EMPTY STATE ── */
    .empty-state { padding: 60px 24px; text-align: center; }
    .empty-icon { width: 48px; height: 48px; background: var(--surface2); border: 1px solid var(--border); border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px; }
    .empty-title { font-size: 0.9375rem; font-weight: 600; margin-bottom: 6px; }
    .empty-sub { font-size: 0.8rem; color: var(--muted); }

    /* ── PAGINATION ── */
    .pagination-wrap { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .pagination-info { font-size: 0.8rem; color: var(--muted); }
    .pagination-links { display: flex; gap: 4px; }
    .pagination-links a, .pagination-links span {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 32px; height: 32px; padding: 0 8px;
      border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 500;
      text-decoration: none; color: var(--muted);
      border: 1px solid var(--border); background: var(--surface);
      transition: border-color 0.15s, color 0.15s;
    }
    .pagination-links a:hover { border-color: var(--border-hover); color: var(--text); }
    .pagination-links span[aria-current] { background: var(--accent); color: #fff; border-color: var(--accent); }
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
      <a href="{{ route('institution-admin.dashboard') }}" class="nav-item">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <div class="nav-label">Applications</div>
      <a href="{{ route('institution-admin.applications.index') }}" class="nav-item active">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Instructor Applications
        @if($counts['pending'] > 0)
          <span class="badge">{{ $counts['pending'] }}</span>
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
      <h1>Instructor Applications</h1>
      <span class="topbar-meta">{{ now()->format('l, F j, Y') }}</span>
    </div>

    <div class="content">

      @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
      @endif

      {{-- PAGE HEADER --}}
      <div class="page-header">
        <div>
          <h2>{{ $institution->name }}</h2>
          <p>Review and manage instructor applications for your institution.</p>
        </div>
      </div>

      {{-- TAB BAR --}}
      <div class="tab-bar">
        <a href="{{ route('institution-admin.applications.index', ['status' => 'pending']) }}"
           class="tab-link {{ $status === 'pending' ? 'active' : '' }}">
          Pending
          <span class="tab-count tab-count-pending">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('institution-admin.applications.index', ['status' => 'approved']) }}"
           class="tab-link {{ $status === 'approved' ? 'active' : '' }}">
          Approved
          <span class="tab-count tab-count-approved">{{ $counts['approved'] }}</span>
        </a>
        <a href="{{ route('institution-admin.applications.index', ['status' => 'rejected']) }}"
           class="tab-link {{ $status === 'rejected' ? 'active' : '' }}">
          Rejected
          <span class="tab-count tab-count-rejected">{{ $counts['rejected'] }}</span>
        </a>
        <a href="{{ route('institution-admin.applications.index', ['status' => 'all']) }}"
           class="tab-link {{ $status === 'all' ? 'active' : '' }}">
          All
          <span class="tab-count" style="background:rgba(127,147,176,0.12);color:var(--muted);">
            {{ array_sum($counts) }}
          </span>
        </a>
      </div>

      {{-- APPLICATIONS TABLE --}}
      <div class="card">
        <table class="tbl">
          <thead>
            <tr>
              <th>Instructor</th>
              <th>Applied</th>
              <th>Code Used</th>
              <th>Status</th>
              @if($status !== 'pending') <th>Reviewed By</th> <th>Reviewed At</th> @endif
              @if($status === 'pending' || $status === 'all') <th>Actions</th> @endif
            </tr>
          </thead>
          <tbody>
            @forelse($applications as $app)
            <tr>
              {{-- Instructor --}}
              <td>
                <div style="display:flex;align-items:center;gap:10px;">
                  <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                  <div>
                    <div style="font-weight:600;line-height:1.3;">{{ $app->user->name }}</div>
                    <div style="font-size:0.75rem;color:var(--muted);">{{ $app->user->email }}</div>
                  </div>
                </div>
              </td>

              {{-- Applied --}}
              <td style="color:var(--muted);font-size:0.8rem;white-space:nowrap;">
                {{ $app->created_at->format('M d, Y') }}<br>
                <span style="font-size:0.72rem;color:var(--dim);">{{ $app->created_at->diffForHumans() }}</span>
              </td>

              {{-- Code Used --}}
              <td>
                <code style="font-size:0.8rem;background:var(--surface2);border:1px solid var(--border);padding:2px 8px;border-radius:4px;letter-spacing:0.1em;color:var(--muted);">
                  {{ $app->entered_code }}
                </code>
              </td>

              {{-- Status --}}
              <td>
                <span class="pill pill-{{ $app->status }}">{{ $app->status }}</span>
              </td>

              {{-- Reviewed By / At (hidden on pending-only tab) --}}
              @if($status !== 'pending')
              <td style="font-size:0.8rem;color:var(--muted);">
                {{ $app->reviewer?->name ?? '—' }}
              </td>
              <td style="font-size:0.8rem;color:var(--muted);white-space:nowrap;">
                {{ $app->reviewed_at?->format('M d, Y') ?? '—' }}
              </td>
              @endif

              {{-- Actions --}}
              @if($status === 'pending' || $status === 'all')
              <td>
                @if($app->isPending())
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
                @else
                  <span style="font-size:0.75rem;color:var(--dim);">—</span>
                @endif
              </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="8">
                <div class="empty-state">
                  <div class="empty-icon">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--dim)" stroke-width="1.5">
                      <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                      <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                  </div>
                  <div class="empty-title">No {{ $status === 'all' ? '' : $status }} applications</div>
                  <div class="empty-sub">
                    @if($status === 'pending')
                      All caught up — no instructors are waiting for review.
                    @elseif($status === 'approved')
                      No instructors have been approved yet.
                    @elseif($status === 'rejected')
                      No applications have been rejected.
                    @else
                      No applications have been submitted yet.
                    @endif
                  </div>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>

        {{-- PAGINATION --}}
        @if($applications->hasPages())
        <div class="pagination-wrap">
          <div class="pagination-info">
            Showing {{ $applications->firstItem() }}–{{ $applications->lastItem() }}
            of {{ $applications->total() }} applications
          </div>
          <div class="pagination-links">
            {{-- Previous --}}
            @if($applications->onFirstPage())
              <span>‹</span>
            @else
              <a href="{{ $applications->previousPageUrl() }}">‹</a>
            @endif

            {{-- Page numbers --}}
            @foreach($applications->getUrlRange(1, $applications->lastPage()) as $page => $url)
              @if($page == $applications->currentPage())
                <span aria-current="page">{{ $page }}</span>
              @else
                <a href="{{ $url }}">{{ $page }}</a>
              @endif
            @endforeach

            {{-- Next --}}
            @if($applications->hasMorePages())
              <a href="{{ $applications->nextPageUrl() }}">›</a>
            @else
              <span>›</span>
            @endif
          </div>
        </div>
        @endif

      </div>{{-- /card --}}
    </div>{{-- /content --}}
  </div>{{-- /main --}}

</body>
</html>