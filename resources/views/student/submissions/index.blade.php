<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Submissions — DataSensei</title>
<style>
    /* Student submission history. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --warn: var(--ds-danger);
      --warn2: var(--ds-warning);
    }

    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      min-height: 100%;
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    /* ── Mobile bar (this page keeps its own menu button, #js-menu-btn;
          it matches the shared mobile bar) ── */
    .mobile-header {
      display: none;
      align-items: center;
      gap: 10px;
      height: 52px;
      padding: 0 12px;
      padding-left: max(12px, env(safe-area-inset-left));
      padding-right: max(64px, env(safe-area-inset-right));
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 900;
    }

    .mobile-header > [aria-hidden="true"] { display: none; }

    .hamburger {
      flex: 0 0 38px;
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      cursor: pointer;
    }

    .hamburger:hover { background: var(--ds-surface-hover); }

    .mobile-title {
      min-width: 0;
      flex: 1 1 auto;
      overflow: hidden;
      font-size: .9375rem;
      font-weight: 600;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* Drawer backdrop (the drawer itself comes from the shared shell) */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: var(--ds-overlay);
      z-index: 950;
    }

    .sidebar-overlay.open {
      display: block;
    }

    .ds-shell {
      display: flex;
      min-height: 100vh;
    }

    .ds-main {
      flex: 1;
      min-width: 0;
      padding: 28px 32px 48px;
    }

    .wrap {
      width: 100%;
      max-width: 1450px;
      margin: 0 auto;
    }

    /* ── Page header ── */
    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 24px;
    }

    .top-row > div:first-child { min-width: 0; flex: 1 1 320px; }

    .page-subtitle {
      max-width: 72ch;
      margin-top: 4px;
      color: var(--muted);
      font-size: .875rem;
      line-height: 1.55;
    }

    .header-action {
      min-height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 0 16px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      font-size: .875rem;
      font-weight: 500;
      line-height: 1.2;
      white-space: nowrap;
      transition: background .12s ease;
    }

    .header-action:hover {
      background: var(--ds-surface-hover);
    }

    /* ── Summary figures ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(5, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 24px;
    }

    .stat-card {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding: 16px 18px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .stat-label {
      color: var(--muted);
      font-size: .8125rem;
      font-weight: 500;
      line-height: 1.4;
    }

    .stat-value {
      margin-top: 2px;
      font-size: 1.5rem;
      font-weight: 700;
      line-height: 1.2;
      letter-spacing: -.02em;
      font-variant-numeric: tabular-nums;
    }

    .stat-note {
      color: var(--muted);
      font-size: .75rem;
      line-height: 1.45;
    }

    /* ── History panel ── */
    .card {
      overflow: hidden;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .toolbar {
      display: grid;
      grid-template-columns: minmax(240px, 1fr) 220px auto auto;
      align-items: end;
      gap: 12px;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
    }

    .field {
      min-width: 0;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .field label {
      color: var(--ds-text-secondary);
      font-size: .8125rem;
      font-weight: 500;
      line-height: 1.35;
    }

    .input,
    .select {
      width: 100%;
      min-height: 38px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      color: var(--text);
      padding: 8px 12px;
      font-family: var(--ds-font-sans);
      font-size: .875rem;
      outline: none;
      transition: border-color .12s ease, box-shadow .12s ease;
    }

    .input:focus,
    .select:focus {
      border-color: var(--accent);
      box-shadow: var(--ds-focus-ring);
    }

    .input::placeholder {
      color: var(--dim);
    }

    .btn {
      min-height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 0 16px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
      background: var(--surface2);
      color: var(--text);
      font-family: var(--ds-font-sans);
      font-size: .875rem;
      font-weight: 500;
      line-height: 1.2;
      cursor: pointer;
      white-space: nowrap;
      transition: background .12s ease, border-color .12s ease;
    }

    .btn.primary {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
    }

    .btn.primary:hover {
      background: var(--accent-hover);
      border-color: var(--accent-hover);
    }

    .btn.secondary {
      background: var(--surface2);
      border-color: var(--ds-border-strong);
      color: var(--text);
    }

    .btn.secondary:hover {
      background: var(--ds-surface-hover);
    }

    .table-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      width: 100%;
      min-width: 980px;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      border-bottom: 1px solid var(--border);
      text-align: left;
      vertical-align: middle;
    }

    .table th {
      padding: 10px 14px;
      background: var(--surface3);
      color: var(--muted);
      font-size: .75rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .table td {
      padding: 12px 14px;
      color: var(--ds-text-secondary);
      font-size: .875rem;
      line-height: 1.5;
    }

    .table tbody tr:last-child td {
      border-bottom: none;
    }

    .table tbody tr:hover td {
      background: rgba(255,255,255,.02);
    }

    .submission-title {
      display: block;
      color: var(--text);
      font-size: .875rem;
      font-weight: 600;
      margin-bottom: 2px;
      overflow-wrap: anywhere;
    }

    .subtext {
      display: block;
      color: var(--muted);
      font-size: .8125rem;
    }

    /* "Attempt #n" is plain text; statuses are compact labels */
    .attempt-badge {
      color: var(--ds-text-secondary);
      font-size: .875rem;
      white-space: nowrap;
      font-variant-numeric: tabular-nums;
    }

    .pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-xs);
      background: var(--surface2);
      padding: 2px 8px;
      color: var(--ds-text-secondary);
      font-size: .75rem;
      font-weight: 600;
      line-height: 1.4;
      white-space: nowrap;
    }

    .pill.good {
      color: var(--ds-success-text);
      border-color: var(--ds-success-border);
      background: var(--ds-success-soft);
    }

    .pill.warn {
      color: var(--ds-warning-text);
      border-color: var(--ds-warning-border);
      background: var(--ds-warning-soft);
    }

    .pill.danger {
      color: var(--ds-danger-text);
      border-color: var(--ds-danger-border);
      background: var(--ds-danger-soft);
    }

    .pill.info {
      color: var(--ds-accent-text);
      border-color: var(--ds-accent-border);
      background: var(--ds-accent-soft);
    }

    .score {
      color: var(--text);
      font-weight: 600;
      font-variant-numeric: tabular-nums;
    }

    .feedback {
      max-width: 250px;
      overflow: hidden;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      color: var(--muted);
      font-size: .8125rem;
      line-height: 1.5;
    }

    .pagination {
      padding: 14px 20px;
      border-top: 1px solid var(--border);
    }

    .pagination:empty { display: none; }

    /* ── Empty state ── */
    .empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: 40px 20px;
      gap: 8px;
    }

    .empty-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--muted);
    }

    .empty-icon svg { width: 28px; height: 28px; }

    .empty h3 {
      color: var(--text);
      font-size: 1rem;
      font-weight: 600;
    }

    .empty p {
      max-width: 440px;
      color: var(--muted);
      font-size: .875rem;
      line-height: 1.6;
    }

    .empty-actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      justify-content: center;
      margin-top: 8px;
    }

    .alert {
      margin-bottom: 16px;
      padding: 12px 16px;
      border: 1px solid var(--ds-accent-border);
      border-radius: var(--radius-sm);
      background: var(--ds-accent-soft);
      color: #dbeafe;
      font-size: .875rem;
      line-height: 1.55;
    }

    .alert.success {
      background: var(--ds-success-soft);
      border-color: var(--ds-success-border);
      color: #d1fae5;
    }

    .alert.danger {
      background: var(--ds-danger-soft);
      border-color: var(--ds-danger-border);
      color: #fee2e2;
    }

    @media (max-width: 1280px) {
      .stats-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 900px) {
      .mobile-header {
        display: flex;
      }

      .ds-main {
        padding: 24px 20px 40px;
      }

      .stats-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .toolbar {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (min-width: 901px) {
      .sidebar-overlay.open { display: none; }
    }

    @media (max-width: 640px) {
      .ds-main {
        padding: 20px 16px 32px;
      }

      .top-row .header-action {
        width: 100%;
      }

      .toolbar {
        grid-template-columns: 1fr;
        padding: 14px 16px;
      }

      .toolbar > span[aria-hidden="true"] { display: none; }

      /* The table becomes a list of labelled rows. The global 640px minimum
         for wide tables does not apply here. */
      .card table.table,
      .table tbody {
        display: block;
        width: 100%;
        min-width: 0;
      }

      .table thead {
        display: none;
      }

      .table tr {
        display: block;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
      }

      .table tr:last-child {
        border-bottom: none;
      }

      .table tbody tr:hover td {
        background: transparent;
      }

      /* label on the left, value (and its sub-line) stacked on the right */
      .table td {
        display: flow-root;
        padding: 6px 0;
        border: 0;
        text-align: right;
      }

      .table td::before {
        content: attr(data-label);
        float: left;
        margin-right: 16px;
        color: var(--muted);
        font-size: .8125rem;
        font-weight: 500;
        line-height: 1.6;
        text-align: left;
      }

      .table td:first-child {
        display: block;
        padding-bottom: 10px;
        text-align: left;
      }

      .table td:first-child::before {
        display: none;
      }

      .table td:last-child .btn {
        min-height: 32px;
        padding: 0 12px;
        font-size: .8125rem;
      }

      .feedback {
        max-width: 200px;
        margin-left: auto;
        text-align: right;
      }

      .pagination { padding: 14px 16px; }
    }

    @media (max-width: 420px) {
      .stats-grid {
        grid-template-columns: minmax(0, 1fr);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .btn, .header-action, .input, .select { transition: none; }
    }
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'My Submissions', 'pageDescription' => 'Every submission you have made, with its result and feedback.'])
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header">
    <button class="hamburger" id="js-menu-btn" type="button" aria-label="Open navigation" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>
    <span class="mobile-title">My Submissions</span>
    <div style="width:38px" aria-hidden="true"></div>
  </header>

  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">My Submissions</h1>
            <p class="page-subtitle">
              Review your assignment attempts, scores, status, and instructor feedback.
            </p>
          </div>

          <a href="{{ route('student.assignments.index') }}" class="header-action">
            View Assignments
          </a>
        </div>

        @if(session('success'))
          <div class="alert success" role="alert">{{ session('success') }}</div>
        @endif

        @if(session('error'))
          <div class="alert danger" role="alert">{{ session('error') }}</div>
        @endif

        <section class="stats-grid" aria-label="Submission summary">
          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">All Attempts</span>
            </div>
            <div class="stat-value">{{ $submissionStats['all_attempts'] ?? 0 }}</div>
            <div class="stat-note">Every saved assignment attempt</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Completed</span>
            </div>
            <div class="stat-value">{{ $submissionStats['completed'] ?? 0 }}</div>
            <div class="stat-note">Submitted, late, or graded attempts</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">In Progress</span>
            </div>
            <div class="stat-value">{{ $submissionStats['in_progress'] ?? 0 }}</div>
            <div class="stat-note">Attempts that can still be continued</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Late</span>
            </div>
            <div class="stat-value">{{ $submissionStats['late'] ?? 0 }}</div>
            <div class="stat-note">Attempts submitted after the due time</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Average Score</span>
            </div>
            <div class="stat-value">{{ $submissionStats['average_percentage'] ?? 0 }}%</div>
            <div class="stat-note">Combined completed-attempt score</div>
          </article>
        </section>

        <section class="card">
          <form class="toolbar" method="GET" action="{{ route('student.submissions.index') }}">
            <div class="field">
              <label for="submission-search">Search</label>
              <input
                id="submission-search"
                class="input"
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Assignment, class, topic, or code..."
              >
            </div>

            <div class="field">
              <label for="submission-status">Status</label>
              <select id="submission-status" class="select" name="status">
                <option value="">All Attempts</option>
                <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>In Progress</option>
                <option value="graded" @selected(request('status') === 'graded')>Graded</option>
                <option value="late" @selected(request('status') === 'late')>Late</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
              </select>
            </div>

            <button class="btn primary" type="submit">Apply Filters</button>

            @if(request()->filled('search') || request()->filled('status'))
              <a class="btn secondary" href="{{ route('student.submissions.index') }}">Clear</a>
            @else
              <span aria-hidden="true"></span>
            @endif
          </form>

          @if($submissions->count())
            <div class="table-scroll" role="region" aria-label="Submission history">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Assignment</th>
                    <th scope="col">Class</th>
                    <th scope="col">Attempt</th>
                    <th scope="col">Activity Time</th>
                    <th scope="col">Status</th>
                    <th scope="col">Score</th>
                    <th scope="col">Feedback</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($submissions as $submission)
                    @php
                      $assignment = $submission->classAssignment;
                      $statusClass = match($submission->status) {
                        'graded' => 'good',
                        'late' => 'danger',
                        'submitted' => 'info',
                        default => 'warn',
                      };
                      $statusLabel = match($submission->status) {
                        'in_progress' => 'In Progress',
                        'submitted' => 'Submitted',
                        'late' => 'Late',
                        'graded' => 'Graded',
                        default => ucfirst(str_replace('_', ' ', (string) $submission->status)),
                      };
                    @endphp
                    <tr>
                      <td data-label="Assignment">
                        <span class="submission-title">{{ $assignment?->title ?? 'Deleted Assignment' }}</span>
                        <span class="subtext">{{ $assignment?->libraryItem?->topic_title ?? 'No topic' }}@if($assignment?->libraryItem?->version_name), {{ $assignment->libraryItem->version_name }}@endif</span>
                      </td>

                      <td data-label="Class">
                        {{ $assignment?->classRoom?->name ?? '—' }}
                      </td>

                      <td data-label="Attempt">
                        <span class="attempt-badge">Attempt #{{ $submission->attempt_no }}</span>
                      </td>

                      <td data-label="Activity Time">
                        @if($submission->submitted_at)
                          {{ $submission->submitted_at->format('M d, Y') }}
                          <span class="subtext">{{ $submission->submitted_at->format('h:i A') }}</span>
                        @elseif($submission->started_at)
                          Started {{ $submission->started_at->format('M d, Y') }}
                          <span class="subtext">{{ $submission->started_at->format('h:i A') }}</span>
                        @else
                          —
                        @endif
                      </td>

                      <td data-label="Status">
                        <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
                      </td>

                      <td data-label="Score">
                        @if($submission->status === 'in_progress')
                          <span class="subtext">Not scored yet</span>
                        @else
                          <span class="score">{{ $submission->score }}/{{ $submission->total_points }}</span>
                          <span class="subtext">{{ $submission->percentage }}%</span>
                        @endif
                      </td>

                      <td data-label="Feedback">
                        <span class="feedback">
                          {{ $submission->feedback ?: 'No instructor feedback yet.' }}
                        </span>
                      </td>

                      <td data-label="Action">
                        @if($assignment && $submission->status === 'in_progress')
                          <a class="btn primary" href="{{ route('student.assignments.take', [$assignment, $submission]) }}">
                            Continue
                          </a>
                        @elseif($assignment)
                          <a class="btn secondary" href="{{ route('student.submissions.show', $submission) }}">
                            View Result
                          </a>
                        @else
                          <span class="subtext">Unavailable</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="pagination">{{ $submissions->links() }}</div>
          @else
            <div class="empty">
              <div class="empty-icon" aria-hidden="true">
                <svg width="29" height="29" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
                </svg>
              </div>

              <h3>No submissions found</h3>

              <p>
                @if(request()->filled('search') || request()->filled('status'))
                  No saved attempts match the selected filters. Clear the filters or search using a different term.
                @else
                  Assignment attempts will appear here after you start or submit work from the Assignments page.
                @endif
              </p>

              <div class="empty-actions">
                @if(request()->filled('search') || request()->filled('status'))
                  <a class="btn secondary" href="{{ route('student.submissions.index') }}">Clear Filters</a>
                @endif
                <a class="btn primary" href="{{ route('student.assignments.index') }}">View Assignments</a>
              </div>
            </div>
          @endif
        </section>
      </div>
    </main>
  </div>

  <script>
    (function () {
      const button = document.getElementById('js-menu-btn');
      const overlay = document.getElementById('js-overlay');
      const sidebar = document.querySelector('.sidebar');

      function openMenu() {
        if (!sidebar) return;
        sidebar.classList.add('is-open');
        overlay?.classList.add('open');
        overlay?.removeAttribute('aria-hidden');
        button?.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      }

      function closeMenu() {
        if (!sidebar) return;
        sidebar.classList.remove('is-open');
        overlay?.classList.remove('open');
        overlay?.setAttribute('aria-hidden', 'true');
        button?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }

      button?.addEventListener('click', openMenu);
      overlay?.addEventListener('click', closeMenu);

      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeMenu();
      });

      sidebar?.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
          if (window.innerWidth <= 800) closeMenu();
        });
      });
    })();
  </script>
</body>
</html>
