<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Assignments — DataSensei</title>
<style>
    /* Student assignment list. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent); --accent3: var(--ds-success);
      --warn: var(--ds-danger); --warn2: var(--ds-warning);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
      min-height: 100%;
      font-family: var(--ds-font-sans);
      background: var(--bg);
      color: var(--text);
    }

    a { color: inherit; text-decoration: none; }

    /* ── Layout ── */
    .ds-shell { display: flex; min-height: 100vh; }
    .ds-main  { flex: 1; min-width: 0; padding: 28px 32px 48px; }
    .wrap     { max-width: 1200px; margin: 0 auto; }

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
      width: 38px; height: 38px;
      display: inline-flex; align-items: center; justify-content: center;
      padding: 0;
      background: var(--surface2);
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-sm);
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

    /* ── Drawer backdrop (the drawer itself comes from the shared shell) ── */
    .sidebar-overlay {
      display: none;
      position: fixed; inset: 0;
      background: var(--ds-overlay);
      z-index: 950;
    }
    .sidebar-overlay.open { display: block; }

    /* ── Page header ── */
    .top-row {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 24px;
    }

    .page-subtitle {
      color: var(--muted);
      max-width: 72ch;
      margin-top: 4px;
      font-size: .875rem;
      line-height: 1.55;
    }

    /* ── Card ── */
    .card {
      border: 1px solid var(--border);
      background: var(--surface);
      border-radius: var(--radius);
      overflow: hidden;
    }

    /* ── Filter bar ── */
    .toolbar {
      display: flex;
      align-items: flex-end;
      gap: 12px;
      flex-wrap: wrap;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
    }

    .field { display: flex; flex-direction: column; gap: 6px; }
    .field label {
      color: var(--ds-text-secondary);
      font-size: .8125rem; font-weight: 500;
      line-height: 1.35;
    }

    /* ── Form controls ── */
    .input, .select {
      min-height: 38px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      background: var(--surface3);
      color: var(--text);
      padding: 8px 12px;
      font-family: var(--ds-font-sans); font-size: .875rem;
      outline: none;
      transition: border-color .12s ease, box-shadow .12s ease;
    }
    .select { min-width: 180px; cursor: pointer; }
    .input:focus, .select:focus {
      border-color: var(--accent);
      box-shadow: var(--ds-focus-ring);
    }

    /* ── Buttons ── */
    .btn {
      min-height: 38px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--ds-border-strong);
      background: var(--surface2);
      color: var(--text);
      padding: 0 16px;
      font-family: var(--ds-font-sans); font-size: .875rem; font-weight: 500; line-height: 1.2;
      cursor: pointer;
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      transition: background .12s ease, border-color .12s ease;
      white-space: nowrap;
      text-decoration: none;
    }

    .btn.primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .btn.primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

    .btn.secondary { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
    .btn.secondary:hover { background: var(--ds-surface-hover); }

    .btn.good { background: transparent; border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .btn.good:hover { background: var(--ds-success-soft); }

    /* ── Table ── */
    .table-scroll {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      min-width: 680px;
    }

    .table th, .table td {
      text-align: left;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }

    .table th {
      padding: 10px 14px;
      font-size: .75rem;
      font-weight: 600;
      color: var(--muted);
      background: var(--surface3);
      white-space: nowrap;
    }

    .table td { padding: 12px 14px; font-size: .875rem; color: var(--ds-text-secondary); }
    .table td strong { color: var(--text); display: block; margin-bottom: 2px; font-weight: 600; overflow-wrap: anywhere; }
    .table td .sub { font-size: .8125rem; color: var(--muted); }

    .table tbody tr:last-child td { border-bottom: none; }
    .table tbody tr:hover td { background: rgba(255,255,255,.02); }

    /* ── Status labels ── */
    .pill {
      display: inline-flex; align-items: center; gap: 4px;
      border: 1px solid var(--ds-border-strong);
      background: var(--surface2);
      border-radius: var(--radius-xs);
      padding: 2px 8px;
      font-size: .75rem; font-weight: 600; line-height: 1.4;
      color: var(--ds-text-secondary);
      white-space: nowrap;
      font-variant-numeric: tabular-nums;
    }
    .pill.good   { color: var(--ds-success-text); border-color: var(--ds-success-border); background: var(--ds-success-soft); }
    .pill.warn   { color: var(--ds-warning-text); border-color: var(--ds-warning-border); background: var(--ds-warning-soft); }
    .pill.danger { color: var(--ds-danger-text);  border-color: var(--ds-danger-border);  background: var(--ds-danger-soft); }

    /* ── Alerts ── */
    .alert {
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      font-size: .875rem;
      line-height: 1.55;
      margin-bottom: 16px;
      border: 1px solid var(--ds-accent-border);
      background: var(--ds-accent-soft);
      color: #dbeafe;
    }
    .alert.success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: #d1fae5; }
    .alert.danger  { background: var(--ds-danger-soft);  border-color: var(--ds-danger-border);  color: #fee2e2; }

    /* ── Pagination ── */
    .pagination { padding: 14px 20px; border-top: 1px solid var(--border); }
    .pagination:empty { display: none; }

    /* ── Empty state ── */
    .empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 40px 20px;
      gap: 8px;
    }

    .empty h3 {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text);
    }

    .empty p {
      color: var(--muted);
      font-size: .875rem;
      max-width: 440px;
      line-height: 1.6;
    }

    .empty p strong { color: var(--text); font-weight: 600; }

    /* Enrollment figures under the explanation */
    .empty-stats {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 4px 24px;
      margin-top: 12px;
      background: var(--surface3);
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      text-align: left;
      width: 100%;
      max-width: 360px;
    }

    .empty-stat {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      font-size: .8125rem;
      color: var(--muted);
      padding: 4px 0;
    }

    .empty-stat .val {
      color: var(--text);
      font-weight: 600;
      flex-shrink: 0;
      font-variant-numeric: tabular-nums;
    }

    /* ── Responsive ── */
    @media (max-width: 900px) {
      .mobile-header { display: flex; }
      .ds-main { padding: 24px 20px 40px; }
    }

    @media (min-width: 901px) {
      .sidebar-overlay.open { display: none; }
    }

    @media (max-width: 720px) {
      /* Table becomes a list of rows on small screens */
      /* (the global 640px minimum for wide tables does not apply to the row list) */
      .card table.table { min-width: 0; }
      .table thead   { display: none; }
      .table, .table tbody { display: block; width: 100%; }

      .table tr {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        column-gap: 12px;
        row-gap: 8px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border);
      }
      .table tr:last-child { border-bottom: none; }
      .table tr:hover td  { background: transparent; }

      .table td {
        padding: 0;
        border: none;
        font-size: .875rem;
      }

      /* Assignment name, row 1 */
      .table td:nth-child(1) { grid-column: 1 / -1; grid-row: 1; }
      /* Class, row 2 left; type, row 2 right */
      .table td:nth-child(2) { grid-column: 1; grid-row: 2; align-self: center; font-size: .8125rem; color: var(--muted); }
      .table td:nth-child(3) { grid-column: 2; grid-row: 2; justify-self: end; }
      /* Due, row 3 left; status, row 3 right */
      .table td:nth-child(4) { grid-column: 1; grid-row: 3; align-self: center; font-size: .8125rem; color: var(--muted); }
      .table td:nth-child(5) { grid-column: 2; grid-row: 3; justify-self: end; }
      /* Action, row 4 */
      .table td:nth-child(6) { grid-column: 1 / -1; grid-row: 4; }
      .table td:nth-child(6) .btn { width: 100%; margin-top: 4px; }

      /* Instructor challenge list has five columns: its action fills row 4 */
      .instructor-challenges .table td:nth-child(5) { grid-column: 1 / -1; grid-row: 4; justify-self: stretch; }
      .instructor-challenges .table td:nth-child(5) .btn { width: 100%; margin-top: 4px; }
    }

    @media (max-width: 640px) {
      .ds-main { padding: 20px 16px 32px; }
      .toolbar { padding: 14px 16px; gap: 8px; }
      .toolbar .field { flex: 1 1 180px; }
      .toolbar .select { min-width: 0; width: 100%; }
      .pagination { padding: 14px 16px; }
      .empty { padding: 32px 16px; }
    }

    @media (max-width: 420px) {
      .empty-stats { grid-template-columns: 1fr; max-width: 280px; }
      .toolbar .btn { flex: 1 1 auto; }
    }

    @media (prefers-reduced-motion: reduce) {
      .btn, .input, .select { transition: none; }
    }
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'My Assignments', 'pageDescription' => 'See the assignments your instructor set, submit work, and review feedback.'])
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
                        <span class="sub">{{ $assignment->libraryItem?->topic_title }}@if($assignment->libraryItem?->version_name), {{ $assignment->libraryItem->version_name }}@endif</span>
                      </td>

                      <td>{{ $assignment->classRoom?->name ?? '—' }}</td>

                      <td>
                        <span class="pill">{{ $assignment->libraryItem?->type_label ?? 'Quiz' }}</span>
                      </td>

                      <td>
                        {{ $assignment->due_at
                          ? $assignment->due_at->format('M d, Y, h:i A')
                          : '—' }}
                      </td>

                      <td>
                        @if(!$submission)
                          <span class="pill warn">Not Started</span>
                        @elseif($submission->status === 'in_progress')
                          <span class="pill warn">In Progress</span>
                        @elseif($submission->status === 'late')
                          <span class="pill danger">Late, {{ $submission->score }}/{{ $submission->total_points }}</span>
                        @else
                          <span class="pill good">{{ ucfirst($submission->status) }}, {{ $submission->score }}/{{ $submission->total_points }}</span>
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

        {{-- ── CHALLENGES GIVEN BY THE INSTRUCTOR ── --}}
        <section class="card instructor-challenges" style="margin-top:24px" aria-labelledby="instructor-challenges-title">
          <div class="toolbar" style="align-items:flex-start;flex-direction:column;gap:4px">
            <h2 id="instructor-challenges-title" style="font-size:.9375rem;font-weight:600;color:var(--text);line-height:1.35">Challenges from your instructor</h2>
            <p class="page-subtitle" style="margin-top:0">Quiz and coding challenges your instructor gave to your class. They open on the University Student challenge map.</p>
          </div>

          @if(isset($challengeAssignments) && $challengeAssignments->count())
            <div class="table-scroll" role="region" aria-label="Instructor challenges list">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">Challenge</th>
                    <th scope="col">Class</th>
                    <th scope="col">Type</th>
                    <th scope="col">Due</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($challengeAssignments as $challengeAssignment)
                    @php
                      $givenChallenge = $challengeAssignment->challenge;
                      $isCodingChallenge = (bool) ($givenChallenge?->is_coding_challenge);
                      $takeUrl = $givenChallenge
                        ? ($isCodingChallenge
                            ? route('challenges.coding.quiz', ['slug' => 'university-student', 'challenge' => $givenChallenge->id])
                            : route('challenges.quiz', ['slug' => 'university-student', 'challenge' => $givenChallenge->id]))
                        : null;
                    @endphp
                    <tr>
                      <td>
                        <strong>{{ $challengeAssignment->title ?: ($givenChallenge?->title ?? 'Challenge') }}</strong>
                        @if($challengeAssignment->instructions)
                          <span class="sub">{{ $challengeAssignment->instructions }}</span>
                        @elseif($givenChallenge && $challengeAssignment->title && $challengeAssignment->title !== $givenChallenge->title)
                          <span class="sub">{{ $givenChallenge->title }}</span>
                        @endif
                      </td>
                      <td>{{ $challengeAssignment->class?->name ?? '—' }}</td>
                      <td>{{ $isCodingChallenge ? 'Coding' : 'Quiz' }}</td>
                      <td>
                        {{ $challengeAssignment->due_at
                          ? $challengeAssignment->due_at->format('M d, Y, h:i A')
                          : 'No due date' }}
                      </td>
                      <td>
                        @if($takeUrl)
                          <a class="btn primary" href="{{ $takeUrl }}">{{ $isCodingChallenge ? 'Open coding challenge' : 'Start quiz' }}</a>
                        @else
                          <span class="sub">Not available</span>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="empty" style="padding:28px 20px">
              <h3>No challenges from your instructor right now</h3>
              <p>When your instructor gives a quiz or coding challenge to your class, it will be listed here with its due date.</p>
            </div>
          @endif
        </section>
      </div>
    </main>
  </div>


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
