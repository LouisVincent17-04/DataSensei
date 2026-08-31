<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — My Assignments</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    /* ── TOKENS ── */
    :root {
      --bg:#0d1320; --surface:#111c2d; --surface2:#1a2638; --surface3:#0f1928;
      --border:#1e2f47; --border-hover:#2c4168;
      --accent:#3b82f6; --accent-hover:#2563eb;
      --accent2:#8b5cf6; --accent3:#10b981;
      --warn:#ef4444; --warn2:#f59e0b;
      --text:#fafafa; --muted:#7f93b0; --dim:#3d5272;
      --radius:14px; --radius-sm:10px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      min-height: 100%;
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background:
        radial-gradient(circle at top left,  rgba(59,130,246,.12), transparent 34rem),
        radial-gradient(circle at top right, rgba(139,92,246,.10), transparent 28rem),
        var(--bg);
      color: var(--text);
      -webkit-font-smoothing: antialiased;
    }

    a { color: inherit; text-decoration: none; }

    /* ── LAYOUT ── */
    .ds-shell { display: flex; min-height: 100vh; }
    .ds-main  { flex: 1; min-width: 0; padding: 28px; }
    .wrap     { max-width: 1200px; margin: 0 auto; }

    /* ── MOBILE HEADER ── */
    .mobile-header {
      display: none;
      align-items: center;
      justify-content: space-between;
      padding: 0 16px;
      height: 56px;
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 200;
      gap: 12px;
    }

    .hamburger {
      flex-shrink: 0;
      width: 38px; height: 38px;
      display: flex; align-items: center; justify-content: center;
      background: rgba(255,255,255,.04);
      border: 1px solid var(--border);
      border-radius: 10px;
      color: var(--text);
      cursor: pointer;
    }

    .mobile-title {
      font-size: .85rem;
      font-weight: 800;
      letter-spacing: -.02em;
      flex: 1;
      text-align: center;
    }

    /* ── SIDEBAR OVERLAY ── */
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(0,0,0,.55);
      backdrop-filter: blur(3px);
      z-index: 150;
    }
    .sidebar-overlay.open { display: block; }

    /* ── PAGE HEADER ── */
    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
      margin-bottom: 24px;
    }

    .page-kicker {
      display: inline-flex; align-items: center; gap: 8px;
      color: var(--accent);
      font-size: .72rem; font-weight: 900;
      text-transform: uppercase; letter-spacing: .09em;
      margin-bottom: 8px;
    }
    .page-kicker::before {
      content: ""; width: 8px; height: 8px;
      background: var(--accent3); border-radius: 50%;
      box-shadow: 0 0 18px rgba(16,185,129,.8);
    }

    .page-title {
      font-size: clamp(1.8rem, 3vw, 2.6rem);
      font-weight: 900;
      letter-spacing: -.06em;
      line-height: 1.05;
    }

    .page-subtitle {
      color: var(--muted);
      max-width: 680px;
      line-height: 1.65;
      margin-top: 10px;
      font-size: .92rem;
    }

    /* ── CARD ── */
    .card {
      border: 1px solid var(--border);
      background: rgba(17,28,45,.92);
      border-radius: 22px;
      box-shadow: 0 18px 55px rgba(0,0,0,.22);
      backdrop-filter: blur(12px);
      overflow: hidden;
    }

    /* ── TOOLBAR (filter bar) ── */
    .toolbar {
      display: flex;
      align-items: flex-end;
      gap: 12px;
      flex-wrap: wrap;
      padding: 16px 18px;
      background: linear-gradient(135deg, rgba(255,255,255,.045), rgba(255,255,255,.015)), var(--surface);
      border-bottom: 1px solid var(--border);
    }

    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label {
      color: var(--dim);
      font-size: .68rem; font-weight: 900;
      text-transform: uppercase; letter-spacing: .08em;
    }

    /* ── FORM CONTROLS ── */
    .input, .select {
      min-height: 42px;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: var(--surface3);
      color: var(--text);
      padding: 10px 12px;
      font: inherit; font-size: .88rem;
      outline: none;
      transition: border-color .15s, box-shadow .15s;
    }
    .select { min-width: 180px; cursor: pointer; }
    .input:focus, .select:focus {
      border-color: rgba(59,130,246,.65);
      box-shadow: 0 0 0 4px rgba(59,130,246,.10);
    }

    /* ── BUTTONS ── */
    .btn {
      min-height: 42px;
      border-radius: 12px;
      border: 1px solid transparent;
      padding: 0 18px;
      font: inherit; font-size: .82rem; font-weight: 900;
      cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      transition: transform .15s, background .15s;
      white-space: nowrap;
      text-decoration: none;
    }
    .btn:hover { transform: translateY(-1px); }

    .btn.primary {
      background: var(--accent); color: #fff;
      border-color: var(--accent);
      box-shadow: 0 8px 22px rgba(59,130,246,.28);
    }
    .btn.primary:hover { background: var(--accent-hover); }

    .btn.secondary {
      background: rgba(255,255,255,.04);
      color: var(--text);
      border-color: var(--border);
    }
    .btn.secondary:hover { background: rgba(255,255,255,.07); }

    .btn.good {
      background: rgba(16,185,129,.12);
      color: #a7f3d0;
      border-color: rgba(16,185,129,.35);
    }

    /* ── TABLE ── */
    .table-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      min-width: 640px;
    }

    .table th, .table td {
      text-align: left;
      padding: 13px 16px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }

    .table th {
      font-size: .7rem;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: var(--dim);
      background: rgba(255,255,255,.022);
      white-space: nowrap;
    }

    .table td { font-size: .87rem; color: var(--muted); }
    .table td strong { color: var(--text); display: block; margin-bottom: 2px; }
    .table td .sub { font-size: .78rem; color: var(--dim); }

    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td { background: rgba(255,255,255,.014); }

    /* ── BADGES ── */
    .pill {
      display: inline-flex; align-items: center; gap: 6px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,.04);
      border-radius: 999px;
      padding: 4px 10px;
      font-size: .72rem; font-weight: 900;
      color: var(--muted);
      white-space: nowrap;
    }
    .pill.good   { color:#a7f3d0; border-color:rgba(16,185,129,.35);  background:rgba(16,185,129,.09); }
    .pill.warn   { color:#fde68a; border-color:rgba(245,158,11,.35);  background:rgba(245,158,11,.09); }
    .pill.danger { color:#fecaca; border-color:rgba(239,68,68,.35);   background:rgba(239,68,68,.09);  }

    /* ── ALERTS ── */
    .alert {
      border-radius: 14px;
      padding: 13px 16px;
      font-weight: 700;
      line-height: 1.5;
      margin-bottom: 16px;
      border: 1px solid transparent;
    }
    .alert.success { background:rgba(16,185,129,.10); border-color:rgba(16,185,129,.30); color:#a7f3d0; }
    .alert.danger  { background:rgba(239,68,68,.10);  border-color:rgba(239,68,68,.30);  color:#fecaca; }

    /* ── PAGINATION ── */
    .pagination { padding: 14px 18px; display: flex; justify-content: flex-end; }

    /* ──────────────────────────────────────────────
       EMPTY STATE  (was the broken part — now fixed)
       ────────────────────────────────────────────── */
    .empty {
      /* Use flex column so every child stacks vertically */
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 52px 24px 44px;
      gap: 12px;
    }

    .empty-icon {
      width: 56px; height: 56px;
      background: rgba(59,130,246,.10);
      border: 1px solid rgba(59,130,246,.22);
      border-radius: 16px;
      display: flex; align-items: center; justify-content: center;
      color: var(--accent);
      margin-bottom: 4px;
    }

    .empty h3 {
      font-size: 1rem;
      font-weight: 800;
      color: var(--text);
    }

    .empty p {
      color: var(--muted);
      font-size: .88rem;
      max-width: 400px;
      line-height: 1.65;
    }

    /* Stats panel inside empty state — block-level, not inline */
    .empty-stats {
      display: grid;                    /* block-level grid, NOT inline-grid */
      grid-template-columns: 1fr 1fr;
      gap: 6px 20px;
      margin-top: 8px;
      border: 1px solid var(--border);
      background: rgba(255,255,255,.025);
      border-radius: 14px;
      padding: 14px 20px;
      text-align: left;
      width: 100%;
      max-width: 340px;
    }

    .empty-stat {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-size: .78rem;
      color: var(--dim);
      padding: 3px 0;
    }

    .empty-stat .val {
      color: var(--text);
      font-weight: 700;
      font-size: .82rem;
      flex-shrink: 0;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 800px) {
      .mobile-header { display: flex; }
      .ds-main { padding: 16px; }
      .page-title { font-size: 1.55rem; }
      .page-subtitle { font-size: .87rem; }

      /* Table → card list on small screens */
      .table-scroll { overflow-x: visible; }
      .table         { min-width: unset; }
      .table thead   { display: none; }
      .table, .table tbody { display: block; width: 100%; }

      .table tr {
        display: grid;
        grid-template-columns: 1fr auto;
        grid-template-rows: auto auto auto auto;
        column-gap: 12px;
        row-gap: 6px;
        padding: 16px;
        border-bottom: 1px solid var(--border);
      }
      .table tr:last-child { border-bottom: none; }
      .table tr:hover td  { background: transparent; }

      .table td {
        padding: 0;
        border: none;
        font-size: .85rem;
      }

      /* Assignment name — full width, row 1 */
      .table td:nth-child(1) { grid-column: 1 / -1; grid-row: 1; }

      /* Class name — row 2 col 1 */
      .table td:nth-child(2) { grid-column: 1; grid-row: 2; font-size: .77rem; color: var(--dim); }

      /* Type badge — row 2 col 2 */
      .table td:nth-child(3) { grid-column: 2; grid-row: 2; justify-self: end; }

      /* Due date — row 3 col 1 */
      .table td:nth-child(4) { grid-column: 1; grid-row: 3; font-size: .77rem; color: var(--dim); }

      /* Status — row 3 col 2 */
      .table td:nth-child(5) { grid-column: 2; grid-row: 3; justify-self: end; }

      /* Action button — full width, row 4 */
      .table td:nth-child(6) { grid-column: 1 / -1; grid-row: 4; }
      .table td:nth-child(6) .btn { width: 100%; margin-top: 4px; }

      .empty-stats { grid-template-columns: 1fr; max-width: 280px; }
      .toolbar .select { min-width: unset; width: 100%; }
      .toolbar { gap: 10px; }
      .toolbar .btn { flex: 0 0 auto; }

      .pagination { justify-content: center; }
      .card { border-radius: 16px; }
    }

    @media (max-width: 480px) {
      .ds-main { padding: 12px; }
      .card { border-radius: 14px; }
      .empty { padding: 40px 16px 36px; }
    }
  </style>
  @include('partials.admin-inspired-page-style')
</head>
<body class="ds-admin-inspired">

  {{-- ── MOBILE TOPBAR ── --}}
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>
    <span class="mobile-title">My Assignments</span>
    <div style="width:38px" aria-hidden="true"></div>
  </header>

  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">

        {{-- ── PAGE HEADER ── --}}
        <div class="top-row">
          <div>
            <div class="page-kicker">Assignments</div>
            <h1 class="page-title ds-page-title">My Assignments</h1>
            <p class="page-subtitle">View activities from your classes, continue unfinished work, and open completed results.</p>
          </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
          <div class="alert success" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert danger" role="alert">{{ session('error') }}</div>
        @endif

        {{-- ── MAIN CARD ── --}}
        <section class="card">

          {{-- Filter toolbar --}}
          <form class="toolbar" method="GET" action="{{ route('student.assignments.index') }}">
            <div class="field">
              <label for="status-filter">Status</label>
              <select id="status-filter" class="select" name="status">
                <option value="">All Statuses</option>
                <option value="pending"   @selected(request('status') === 'pending')>Pending</option>
                <option value="submitted" @selected(request('status') === 'submitted')>Submitted</option>
              </select>
            </div>
            <button class="btn secondary" type="submit">Filter</button>
          </form>

          @if($assignments->count())

            {{-- Scrollable table wrapper --}}
            <div class="table-scroll" role="region" aria-label="Assignments list">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Assignment</th>
                    <th scope="col">Class</th>
                    <th scope="col">Type</th>
                    <th scope="col">Due</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($assignments as $assignment)
                    @php $submission = $assignment->submissions->first(); @endphp
                    <tr>
                      <td>
                        <strong>{{ $assignment->title }}</strong>
                        <span class="sub">
                          {{ $assignment->libraryItem?->topic_title }}
                          @if($assignment->libraryItem?->version_name)
                            · {{ $assignment->libraryItem->version_name }}
                          @endif
                        </span>
                      </td>

                      <td>{{ $assignment->classRoom?->name ?? '—' }}</td>

                      <td>
                        <span class="pill">{{ $assignment->libraryItem?->type_label ?? 'Quiz' }}</span>
                      </td>

                      <td>
                        {{ $assignment->due_at
                          ? $assignment->due_at->format('M d, Y · h:i A')
                          : '—' }}
                      </td>

                      <td>
                        @if(!$submission)
                          <span class="pill warn">Not Started</span>
                        @elseif($submission->status === 'in_progress')
                          <span class="pill warn">In Progress</span>
                        @elseif($submission->status === 'late')
                          <span class="pill danger">Late · {{ $submission->score }}/{{ $submission->total_points }}</span>
                        @else
                          <span class="pill good">{{ ucfirst($submission->status) }} · {{ $submission->score }}/{{ $submission->total_points }}</span>
                        @endif
                      </td>

                      <td>
                        @if($submission && $submission->status === 'in_progress')
                          <a class="btn primary" href="{{ route('student.assignments.take', [$assignment, $submission]) }}">Continue</a>
                        @elseif($submission && $submission->status !== 'in_progress')
                          <a class="btn secondary" href="{{ route('student.assignments.result', [$assignment, $submission]) }}">View Result</a>
                        @else
                          <a class="btn primary" href="{{ route('student.assignments.show', $assignment) }}">Start</a>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="pagination">{{ $assignments->links() }}</div>

          @else
            {{-- ── EMPTY STATE (fixed layout) ── --}}
            <div class="empty">
<!-- 
              <div class="empty-icon" aria-hidden="true">
                <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
              </div> -->

              <h3>No assignments available yet</h3>

              <p>
                @if(($studentAssignmentStats['enrolled_classes'] ?? 0) === 0)
                  You're not enrolled in any class. Ask your instructor to add your account to their class.
                @elseif(($studentAssignmentStats['all_class_assignments'] ?? 0) === 0)
                  Your class doesn't have any assignments yet. Check back later.
                @elseif(($studentAssignmentStats['draft'] ?? 0) > 0 && ($studentAssignmentStats['published_visible'] ?? 0) === 0)
                  Your instructor has saved assignments as <strong>Draft</strong>. Ask them to publish when ready.
                @elseif(($studentAssignmentStats['future'] ?? 0) > 0 && ($studentAssignmentStats['published_visible'] ?? 0) === 0)
                  Assignments are scheduled but their availability window hasn't opened yet.
                @else
                  No assignments match your current filter. Try selecting <em>All Statuses</em>.
                @endif
              </p>

              {{-- Stats grid — block-level, stacks correctly below the text --}}
              <div class="empty-stats" role="list" aria-label="Enrollment summary">
                <div class="empty-stat" role="listitem">
                  <span>Enrolled classes</span>
                  <span class="val">{{ $studentAssignmentStats['enrolled_classes'] ?? 0 }}</span>
                </div>
                <div class="empty-stat" role="listitem">
                  <span>Total assignments</span>
                  <span class="val">{{ $studentAssignmentStats['all_class_assignments'] ?? 0 }}</span>
                </div>
                <div class="empty-stat" role="listitem">
                  <span>Visible now</span>
                  <span class="val">{{ $studentAssignmentStats['published_visible'] ?? 0 }}</span>
                </div>
                <div class="empty-stat" role="listitem">
                  <span>Drafts</span>
                  <span class="val">{{ $studentAssignmentStats['draft'] ?? 0 }}</span>
                </div>
                <div class="empty-stat" role="listitem">
                  <span>Scheduled</span>
                  <span class="val">{{ $studentAssignmentStats['future'] ?? 0 }}</span>
                </div>
              </div>

            </div>
          @endif

        </section>
      </div>
    </main>
  </div>

  {{-- ── SIDEBAR MOBILE BEHAVIOR OVERRIDE ──
       The sidebar partial hides itself via display:none at ≤700px.
       We override that here to use a slide-in drawer instead. --}}
  <style>
    @media (max-width: 800px) {
      .sidebar {
        display: flex !important;     /* un-hide */
        position: fixed;
        top: 0; left: 0;
        height: 100vh;
        z-index: 160;
        transform: translateX(-100%);
        transition: transform .25s cubic-bezier(.4,0,.2,1);
        box-shadow: 4px 0 32px rgba(0,0,0,.35);
      }
      .sidebar.is-open {
        transform: translateX(0);
      }
    }
  </style>

  <script>
    (function () {
      const btn     = document.getElementById('js-menu-btn');
      const overlay = document.getElementById('js-overlay');
      const sidebar = document.querySelector('.sidebar');

      function open() {
        sidebar.classList.add('is-open');
        overlay.classList.add('open');
        overlay.removeAttribute('aria-hidden');
        btn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      }

      function close() {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('open');
        overlay.setAttribute('aria-hidden', 'true');
        btn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }

      btn?.addEventListener('click', open);
      overlay?.addEventListener('click', close);

      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') close();
      });

      /* Close drawer when a nav link is tapped on mobile */
      sidebar?.querySelectorAll('.nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
          if (window.innerWidth <= 800) close();
        });
      });
    })();
  </script>

</body>
</html>
