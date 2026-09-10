<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — My Submissions</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

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
      --warn:#ef4444;
      --warn2:#f59e0b;
      --text:#fafafa;
      --muted:#7f93b0;
      --dim:#3d5272;
      --radius:14px;
      --radius-sm:10px;
    }

    *, *::before, *::after {
      box-sizing:border-box;
      margin:0;
      padding:0;
    }

    html, body {
      min-height:100%;
      font-family:'Inter', system-ui, -apple-system, sans-serif;
      background:
        radial-gradient(circle at top left, rgba(59,130,246,.12), transparent 34rem),
        radial-gradient(circle at top right, rgba(139,92,246,.10), transparent 28rem),
        var(--bg);
      color:var(--text);
      -webkit-font-smoothing:antialiased;
    }

    a {
      color:inherit;
      text-decoration:none;
    }

    .mobile-header {
      display:none;
      align-items:center;
      justify-content:space-between;
      height:56px;
      padding:0 16px;
      background:var(--surface);
      border-bottom:1px solid var(--border);
      position:sticky;
      top:0;
      z-index:200;
    }

    .hamburger {
      width:38px;
      height:38px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--border);
      border-radius:10px;
      background:rgba(255,255,255,.04);
      color:var(--text);
      cursor:pointer;
    }

    .mobile-title {
      flex:1;
      text-align:center;
      font-size:.86rem;
      font-weight:800;
    }

    .sidebar-overlay {
      display:none;
      position:fixed;
      inset:0;
      background:rgba(0,0,0,.55);
      backdrop-filter:blur(3px);
      z-index:150;
    }

    .sidebar-overlay.open {
      display:block;
    }

    .ds-shell {
      display:flex;
      min-height:100vh;
    }

    .ds-main {
      flex:1;
      min-width:0;
      padding:28px;
    }

    .wrap {
      width:100%;
      max-width:1450px;
      margin:0 auto;
    }

    .top-row {
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap:20px;
      margin-bottom:24px;
    }

    .page-kicker {
      display:inline-flex;
      align-items:center;
      gap:8px;
      color:var(--accent);
      font-size:.72rem;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.09em;
      margin-bottom:8px;
    }

    .page-kicker::before {
      content:"";
      width:8px;
      height:8px;
      border-radius:50%;
      background:var(--accent3);
      box-shadow:0 0 18px rgba(16,185,129,.8);
    }

    .page-title {
      font-size:clamp(1.8rem, 3vw, 2.6rem);
      font-weight:900;
      letter-spacing:-.06em;
      line-height:1.05;
    }

    .page-subtitle {
      max-width:760px;
      margin-top:10px;
      color:var(--muted);
      font-size:.92rem;
      line-height:1.65;
    }

    .header-action {
      min-height:42px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:0 17px;
      border:1px solid var(--border);
      border-radius:12px;
      background:rgba(255,255,255,.04);
      color:var(--text);
      font-size:.82rem;
      font-weight:900;
      white-space:nowrap;
      transition:.15s;
    }

    .header-action:hover {
      transform:translateY(-1px);
      border-color:var(--border-hover);
      background:rgba(255,255,255,.07);
    }

    .stats-grid {
      display:grid;
      grid-template-columns:repeat(5, minmax(0, 1fr));
      gap:14px;
      margin-bottom:20px;
    }

    .stat-card {
      min-width:0;
      padding:18px;
      border:1px solid var(--border);
      border-radius:18px;
      background:rgba(17,28,45,.92);
      box-shadow:0 14px 38px rgba(0,0,0,.16);
    }

    .stat-top {
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:12px;
    }

    .stat-label {
      color:var(--muted);
      font-size:.75rem;
      font-weight:800;
      line-height:1.4;
    }

    .stat-icon {
      width:34px;
      height:34px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid var(--border);
      border-radius:10px;
      background:var(--surface2);
      color:var(--accent);
      flex-shrink:0;
    }

    .stat-value {
      margin-top:15px;
      font-size:1.7rem;
      font-weight:900;
      letter-spacing:-.05em;
    }

    .stat-note {
      margin-top:5px;
      color:var(--dim);
      font-size:.7rem;
      line-height:1.45;
    }

    .card {
      overflow:hidden;
      border:1px solid var(--border);
      border-radius:22px;
      background:rgba(17,28,45,.92);
      box-shadow:0 18px 55px rgba(0,0,0,.22);
      backdrop-filter:blur(12px);
    }

    .toolbar {
      display:grid;
      grid-template-columns:minmax(240px, 1fr) 220px auto auto;
      align-items:end;
      gap:12px;
      padding:17px 18px;
      border-bottom:1px solid var(--border);
      background:
        linear-gradient(135deg, rgba(255,255,255,.045), rgba(255,255,255,.015)),
        var(--surface);
    }

    .field {
      display:flex;
      flex-direction:column;
      gap:6px;
    }

    .field label {
      color:var(--dim);
      font-size:.68rem;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.08em;
    }

    .input,
    .select {
      width:100%;
      min-height:42px;
      border:1px solid var(--border);
      border-radius:12px;
      background:var(--surface3);
      color:var(--text);
      padding:10px 12px;
      font:inherit;
      font-size:.88rem;
      outline:none;
      transition:border-color .15s, box-shadow .15s;
    }

    .input:focus,
    .select:focus {
      border-color:rgba(59,130,246,.65);
      box-shadow:0 0 0 4px rgba(59,130,246,.10);
    }

    .input::placeholder {
      color:var(--dim);
    }

    .btn {
      min-height:42px;
      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      padding:0 17px;
      border:1px solid transparent;
      border-radius:12px;
      font:inherit;
      font-size:.82rem;
      font-weight:900;
      cursor:pointer;
      white-space:nowrap;
      transition:.15s;
    }

    .btn:hover {
      transform:translateY(-1px);
    }

    .btn.primary {
      background:var(--accent);
      border-color:var(--accent);
      color:#fff;
      box-shadow:0 8px 22px rgba(59,130,246,.25);
    }

    .btn.primary:hover {
      background:var(--accent-hover);
    }

    .btn.secondary {
      background:rgba(255,255,255,.04);
      border-color:var(--border);
      color:var(--text);
    }

    .btn.secondary:hover {
      background:rgba(255,255,255,.07);
      border-color:var(--border-hover);
    }

    .table-scroll {
      overflow-x:auto;
      -webkit-overflow-scrolling:touch;
    }

    .table {
      width:100%;
      min-width:980px;
      border-collapse:collapse;
    }

    .table th,
    .table td {
      padding:14px 16px;
      border-bottom:1px solid var(--border);
      text-align:left;
      vertical-align:middle;
    }

    .table th {
      background:rgba(255,255,255,.022);
      color:var(--dim);
      font-size:.69rem;
      font-weight:900;
      text-transform:uppercase;
      letter-spacing:.07em;
      white-space:nowrap;
    }

    .table td {
      color:var(--muted);
      font-size:.84rem;
      line-height:1.5;
    }

    .table tbody tr:last-child td {
      border-bottom:none;
    }

    .table tbody tr:hover td {
      background:rgba(255,255,255,.014);
    }

    .submission-title {
      display:block;
      color:var(--text);
      font-size:.88rem;
      font-weight:800;
      margin-bottom:3px;
    }

    .subtext {
      display:block;
      color:var(--dim);
      font-size:.75rem;
    }

    .attempt-badge,
    .pill {
      display:inline-flex;
      align-items:center;
      gap:6px;
      border:1px solid var(--border);
      border-radius:999px;
      background:rgba(255,255,255,.04);
      padding:5px 9px;
      color:var(--muted);
      font-size:.71rem;
      font-weight:900;
      white-space:nowrap;
    }

    .pill.good {
      color:#a7f3d0;
      border-color:rgba(16,185,129,.35);
      background:rgba(16,185,129,.09);
    }

    .pill.warn {
      color:#fde68a;
      border-color:rgba(245,158,11,.35);
      background:rgba(245,158,11,.09);
    }

    .pill.danger {
      color:#fecaca;
      border-color:rgba(239,68,68,.35);
      background:rgba(239,68,68,.09);
    }

    .pill.info {
      color:#bfdbfe;
      border-color:rgba(59,130,246,.35);
      background:rgba(59,130,246,.09);
    }

    .score {
      color:var(--text);
      font-weight:900;
      font-variant-numeric:tabular-nums;
    }

    .feedback {
      max-width:250px;
      overflow:hidden;
      display:-webkit-box;
      -webkit-line-clamp:2;
      -webkit-box-orient:vertical;
      color:var(--muted);
      font-size:.78rem;
      line-height:1.5;
    }

    .pagination {
      display:flex;
      justify-content:flex-end;
      padding:15px 18px;
      border-top:1px solid var(--border);
    }

    .empty {
      min-height:330px;
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      text-align:center;
      padding:50px 24px;
      gap:12px;
    }

    .empty-icon {
      width:62px;
      height:62px;
      display:flex;
      align-items:center;
      justify-content:center;
      border:1px solid rgba(59,130,246,.24);
      border-radius:18px;
      background:rgba(59,130,246,.10);
      color:var(--accent);
    }

    .empty h3 {
      color:var(--text);
      font-size:1.05rem;
      font-weight:900;
    }

    .empty p {
      max-width:440px;
      color:var(--muted);
      font-size:.87rem;
      line-height:1.65;
    }

    .empty-actions {
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      justify-content:center;
      margin-top:6px;
    }

    .alert {
      margin-bottom:16px;
      padding:13px 16px;
      border:1px solid transparent;
      border-radius:14px;
      font-weight:700;
      line-height:1.5;
    }

    .alert.success {
      background:rgba(16,185,129,.10);
      border-color:rgba(16,185,129,.30);
      color:#a7f3d0;
    }

    .alert.danger {
      background:rgba(239,68,68,.10);
      border-color:rgba(239,68,68,.30);
      color:#fecaca;
    }

    @media (max-width:1200px) {
      .stats-grid {
        grid-template-columns:repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width:900px) {
      .toolbar {
        grid-template-columns:1fr 1fr;
      }

      .top-row {
        flex-direction:column;
      }
    }

    @media (max-width:800px) {
      .mobile-header {
        display:flex;
      }

      .ds-main {
        padding:16px;
      }

      .stats-grid {
        grid-template-columns:1fr 1fr;
      }

      .sidebar {
        display:flex !important;
        position:fixed;
        top:0;
        left:0;
        height:100vh;
        z-index:160;
        transform:translateX(-100%);
        transition:transform .25s cubic-bezier(.4,0,.2,1);
        box-shadow:4px 0 32px rgba(0,0,0,.35);
      }

      .sidebar.is-open {
        transform:translateX(0);
      }
    }

    @media (max-width:640px) {
      .page-title {
        font-size:1.65rem;
      }

      .stats-grid,
      .toolbar {
        grid-template-columns:1fr;
      }

      .table-scroll {
        overflow-x:visible;
      }

      .table,
      .table tbody {
        display:block;
        width:100%;
        min-width:0;
      }

      .table thead {
        display:none;
      }

      .table tr {
        display:block;
        padding:16px;
        border-bottom:1px solid var(--border);
      }

      .table tr:last-child {
        border-bottom:none;
      }

      .table td {
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:16px;
        padding:7px 0;
        border:0;
        text-align:right;
      }

      .table td::before {
        content:attr(data-label);
        color:var(--dim);
        font-size:.69rem;
        font-weight:900;
        text-transform:uppercase;
        letter-spacing:.06em;
        text-align:left;
      }

      .table td:first-child {
        display:block;
        padding-bottom:12px;
        text-align:left;
      }

      .table td:first-child::before {
        display:none;
      }

      .feedback {
        max-width:190px;
        text-align:right;
      }
    }
  </style>
  @include('partials.admin-inspired-page-style')
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
            <div class="page-kicker"></div>
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
              <span class="stat-icon">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
                </svg>
              </span>
            </div>
            <div class="stat-value">{{ $submissionStats['all_attempts'] ?? 0 }}</div>
            <div class="stat-note">Every saved assignment attempt</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Completed</span>
              <span class="stat-icon" style="color:var(--accent3)">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M5 13l4 4L19 7"/>
                </svg>
              </span>
            </div>
            <div class="stat-value">{{ $submissionStats['completed'] ?? 0 }}</div>
            <div class="stat-note">Submitted, late, or graded attempts</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">In Progress</span>
              <span class="stat-icon" style="color:var(--warn2)">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
                </svg>
              </span>
            </div>
            <div class="stat-value">{{ $submissionStats['in_progress'] ?? 0 }}</div>
            <div class="stat-note">Attempts that can still be continued</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Late</span>
              <span class="stat-icon" style="color:var(--warn)">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M12 9v4m0 4h.01"/><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
              </span>
            </div>
            <div class="stat-value">{{ $submissionStats['late'] ?? 0 }}</div>
            <div class="stat-note">Attempts submitted after the due time</div>
          </article>

          <article class="stat-card">
            <div class="stat-top">
              <span class="stat-label">Average Score</span>
              <span class="stat-icon" style="color:var(--accent2)">
                <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M4 19V9m8 10V5m8 14v-7"/><path d="M3 19h18"/>
                </svg>
              </span>
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
                        <span class="subtext">
                          {{ $assignment?->libraryItem?->topic_title ?? 'No topic' }}
                          @if($assignment?->libraryItem?->version_name)
                            · {{ $assignment->libraryItem->version_name }}
                          @endif
                        </span>
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
