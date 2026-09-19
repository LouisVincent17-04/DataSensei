<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instructor Applications — DataSensei</title>
@include('partials.page-heading-style')
  <style>
    /* Instructor applications (institution admin). Colours, type and radius come
       from partials.design-system; the sidebar comes from partials.sidebar-shell. */
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
    .content { flex: 1; display: flex; flex-direction: column; gap: 20px; padding: 28px 32px 48px; }

    /* ── flash ── */
    .flash { padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm); font-size: 0.875rem; line-height: 1.5; }
    .flash-success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
    .flash-error { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }

    /* ── lead-in ── */
    .page-header { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; }
    .page-header > div { min-width: 0; }
    .page-header h2 { font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; overflow-wrap: anywhere; }
    .page-header p { max-width: 72ch; margin-top: 4px; color: var(--muted); font-size: 0.875rem; line-height: 1.5; }

    /* ── status tabs (underline) ── */
    .tab-bar { display: flex; gap: 24px; max-width: 100%; overflow-x: auto; border-bottom: 1px solid var(--border); scrollbar-width: none; }
    .tab-bar::-webkit-scrollbar { display: none; }
    .tab-link {
      flex-shrink: 0;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      min-height: 40px;
      margin-bottom: -1px;
      padding: 0 2px;
      border-bottom: 2px solid transparent;
      color: var(--muted);
      font-size: 0.875rem;
      font-weight: 500;
      text-decoration: none;
      white-space: nowrap;
      transition: color 0.12s ease, border-color 0.12s ease;
    }
    .tab-link:hover { color: var(--text); }
    .tab-link.active { border-bottom-color: var(--accent); color: var(--text); }
    .tab-count {
      display: inline-flex;
      align-items: center;
      padding: 1px 6px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-xs);
      background: var(--surface2);
      color: var(--ds-text-secondary);
      font-size: 0.75rem;
      font-weight: 600;
      line-height: 1.4;
      font-variant-numeric: tabular-nums;
    }
    .tab-count-pending { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }
    /* Approved and rejected counts stay neutral; only the queue that needs action is highlighted. */

    /* ── table card ── */
    .card { min-width: 0; overflow: hidden; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    .tbl { width: 100%; min-width: 720px; border-collapse: collapse; }
    .tbl th { padding: 10px 14px; background: var(--surface3); border-bottom: 1px solid var(--border); color: var(--muted); font-size: 0.75rem; font-weight: 600; text-align: left; white-space: nowrap; }
    .tbl td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--ds-text-secondary); font-size: 0.875rem; vertical-align: middle; }
    .tbl th:first-child, .tbl td:first-child { padding-left: 20px; }
    .tbl th:last-child, .tbl td:last-child { padding-right: 20px; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td { background: rgba(255, 255, 255, 0.02); }
    .tbl .muted-cell { color: var(--muted); font-size: 0.8125rem; }
    .tbl .nowrap { white-space: nowrap; }
    .date-rel { color: var(--muted); font-size: 0.75rem; }

    .person { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .person > div:last-child { min-width: 0; }
    .person-name { color: var(--text); font-weight: 600; line-height: 1.35; }
    .person-email { color: var(--muted); font-size: 0.75rem; overflow-wrap: break-word; }

    .user-av { width: 32px; height: 32px; flex-shrink: 0; display: inline-flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--text); font-size: 0.75rem; font-weight: 600; }

    .code-used { padding: 2px 6px; border: 1px solid var(--border); border-radius: var(--radius-xs); background: var(--surface3); color: var(--ds-text-secondary); font-family: var(--ds-font-mono); font-size: 0.8125rem; white-space: nowrap; }

    /* ── status badges ── */
    .pill { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2); color: var(--ds-text-secondary); font-size: 0.75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; text-transform: capitalize; }
    .pill-pending { border-color: var(--ds-warning-border); background: var(--ds-warning-soft); color: var(--ds-warning-text); }
    .pill-approved { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: var(--ds-success-text); }
    .pill-rejected { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: var(--ds-danger-text); }

    /* ── row actions ── */
    .row-actions { display: flex; flex-wrap: nowrap; gap: 8px; }
    .btn { min-height: 32px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 12px; border: 1px solid transparent; border-radius: var(--radius-sm); background: transparent; font-size: 0.8125rem; font-weight: 500; line-height: 1.2; white-space: nowrap; cursor: pointer; transition: background 0.12s ease, border-color 0.12s ease; }
    .btn-approve { border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .btn-approve:hover { background: var(--ds-success-soft); }
    .btn-reject { border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
    .btn-reject:hover { background: var(--ds-danger-soft); }

    /* ── empty state ── */
    .tbl td.empty-td { padding: 0; }
    .tbl tbody tr:hover td.empty-td { background: transparent; }
    .empty-state { padding: 32px 20px; text-align: center; }
    .empty-icon { display: inline-flex; margin-bottom: 8px; color: var(--dim); }
    .empty-title { margin-bottom: 4px; color: var(--text); font-size: 0.875rem; font-weight: 600; }
    .empty-sub { color: var(--muted); font-size: 0.875rem; line-height: 1.5; }

    /* ── pagination ── */
    .pagination-wrap { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 20px; border-top: 1px solid var(--border); }
    .pagination-info { color: var(--muted); font-size: 0.8125rem; }
    .pagination-links { display: flex; flex-wrap: wrap; gap: 6px; }
    .pagination-links a, .pagination-links span {
      min-width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0 8px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--ds-text-secondary);
      font-size: 0.8125rem;
      font-weight: 500;
      text-decoration: none;
      transition: background 0.12s ease, color 0.12s ease;
    }
    .pagination-links a:hover { background: var(--ds-surface-hover); color: var(--text); }
    .pagination-links span:not([aria-current]) { opacity: 0.45; }
    .pagination-links span[aria-current] { border-color: var(--accent); background: var(--accent); color: #fff; }

    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 8px 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .topbar { padding: 8px 16px; }
      .content { padding: 20px 16px 32px; }
      .tab-bar { gap: 16px; }
      .pagination-wrap { align-items: flex-start; flex-direction: column; padding: 12px 16px; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Instructor Applications', 'pageDescription' => 'Review instructor applications for your institution.'])
</head>
<body>

  {{-- ── SIDEBAR ── --}}
  @include('partials.institution-admin-sidebar', ['pendingCount' => $counts['pending'] ?? 0])

  {{-- ── MAIN ── --}}
  <div class="main">
    <div class="topbar">
      <h1 class="ds-page-title">Instructor Applications</h1>
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
          <span class="tab-count">
            {{ array_sum($counts) }}
          </span>
        </a>
      </div>

      {{-- APPLICATIONS TABLE --}}
      <div class="card">
        <div class="table-scroll">
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
                <div class="person">
                  <div class="user-av">{{ strtoupper(substr($app->user->name, 0, 1)) }}</div>
                  <div>
                    <div class="person-name">{{ $app->user->name }}</div>
                    <div class="person-email">{{ $app->user->email }}</div>
                  </div>
                </div>
              </td>

              {{-- Applied --}}
              <td class="muted-cell nowrap">
                {{ $app->created_at->format('M d, Y') }}<br>
                <span class="date-rel">{{ $app->created_at->diffForHumans() }}</span>
              </td>

              {{-- Code Used --}}
              <td>
                <code class="code-used">
                  {{ $app->entered_code }}
                </code>
              </td>

              {{-- Status --}}
              <td>
                <span class="pill pill-{{ $app->status }}">{{ $app->status }}</span>
              </td>

              {{-- Reviewed By / At (hidden on pending-only tab) --}}
              @if($status !== 'pending')
              <td class="muted-cell">
                {{ $app->reviewer?->name ?? '—' }}
              </td>
              <td class="muted-cell nowrap">
                {{ $app->reviewed_at?->format('M d, Y') ?? '—' }}
              </td>
              @endif

              {{-- Actions --}}
              @if($status === 'pending' || $status === 'all')
              <td>
                @if($app->isPending())
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
                @else
                  <span class="muted-cell">—</span>
                @endif
              </td>
              @endif
            </tr>
            @empty
            <tr>
              <td colspan="8" class="empty-td">
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
        </div>

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