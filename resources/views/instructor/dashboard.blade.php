<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Instructor Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <style>
    :root {
      --bg:          #0d1320;
      --surface:     #111c2d;
      --surface2:    #1a2638;
      --surface3:    #223149;
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

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; }

    .topbar {
      height: 64px;
      background: var(--bg);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      padding: 0 32px;
      gap: 16px;
      flex-shrink: 0;
    }

    .topbar h1 {
      font-size: 1.125rem;
      font-weight: 700;
      color: var(--text);
      flex: 1;
      letter-spacing: -0.01em;
    }

    .topbar-search {
      display: flex;
      align-items: center;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 8px 12px;
      gap: 10px;
      width: 280px;
      transition: border-color 0.15s;
    }

    .topbar-search:focus-within { border-color: var(--accent); }

    .topbar-search input {
      background: none;
      border: none;
      outline: none;
      color: var(--text);
      font-size: 0.875rem;
      font-family: inherit;
      width: 100%;
    }

    .topbar-search input::placeholder { color: var(--dim); }

    .topbar-btn {
      width: 36px;
      height: 36px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      color: var(--muted);
      transition: all 0.15s;
      position: relative;
    }

    .topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }

    .notif-dot {
      position: absolute;
      top: -2px;
      right: -2px;
      width: 8px;
      height: 8px;
      background: var(--accent4);
      border-radius: 50%;
      border: 2px solid var(--bg);
    }

    .content {
      flex: 1;
      overflow-y: auto;
      padding: 32px;
      display: flex;
      flex-direction: column;
      gap: 24px;
    }

    .welcome-banner {
      background:
        radial-gradient(circle at top right, rgba(59,130,246,0.18), transparent 30%),
        var(--surface);
      border: 1px solid var(--border);
      border-left: 4px solid var(--accent);
      border-radius: var(--radius);
      padding: 28px 32px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }

    .welcome-text h2 {
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--text);
      line-height: 1.2;
      margin-bottom: 8px;
      letter-spacing: -0.02em;
    }

    .welcome-text p {
      font-size: 0.875rem;
      color: var(--muted);
      max-width: 620px;
      line-height: 1.6;
    }

    .welcome-cta { display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap; }

    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 9px 16px;
      border-radius: var(--radius-sm);
      font-size: 0.875rem;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.15s;
      font-family: inherit;
      text-decoration: none;
      white-space: nowrap;
    }

    .btn-primary { background: var(--text); color: var(--bg); }
    .btn-primary:hover { background: #e4e4e7; }
    .btn-ghost { background: var(--surface2); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover { background: var(--border); }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 16px;
    }

    .stat-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 18px;
      display: flex;
      flex-direction: column;
      gap: 14px;
      transition: border-color 0.2s, transform 0.2s;
      min-height: 140px;
    }

    .stat-card:hover { border-color: var(--border-hover); transform: translateY(-1px); }

    .stat-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }

    .stat-title {
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--muted);
      line-height: 1.4;
    }

    .stat-icon {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--border);
      background: var(--surface2);
      color: var(--text);
      flex-shrink: 0;
    }

    .stat-main { display: flex; align-items: baseline; gap: 8px; }

    .stat-value {
      font-size: 1.65rem;
      font-weight: 800;
      color: var(--text);
      line-height: 1;
      letter-spacing: -0.03em;
      font-variant-numeric: tabular-nums;
    }

    .stat-note {
      font-size: 0.72rem;
      color: var(--dim);
      line-height: 1.4;
    }

    .stat-bar {
      height: 4px;
      background: var(--surface2);
      border-radius: 4px;
      overflow: hidden;
      margin-top: auto;
    }

    .stat-bar-fill { height: 100%; border-radius: 4px; transition: width 1s ease; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .grid-main { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .card-header {
      padding: 20px 24px;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .card-title { font-weight: 700; font-size: 1rem; color: var(--text); }
    .card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 4px; line-height: 1.5; }
    .card-body { padding: 24px; flex: 1; }

    .link-sm {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--muted);
      cursor: pointer;
      text-decoration: none;
      transition: color 0.15s;
      white-space: nowrap;
    }

    .link-sm:hover { color: var(--text); }

    .class-list, .activity-list, .risk-list, .assignment-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .class-item {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 16px;
      padding: 16px;
      border: 1px solid var(--border);
      background: var(--bg);
      border-radius: var(--radius-sm);
      transition: border-color 0.15s;
    }

    .class-item:hover { border-color: var(--border-hover); }

    .class-name {
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 4px;
    }

    .class-meta {
      font-size: 0.75rem;
      color: var(--muted);
      line-height: 1.5;
    }

    .class-score {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 5px;
    }

    .class-score strong {
      font-size: 1rem;
      color: var(--text);
      font-variant-numeric: tabular-nums;
    }

    .pill {
      font-size: 0.68rem;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 999px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      width: fit-content;
    }

    .pill-ok { background: rgba(16,185,129,0.1); color: var(--accent3); }
    .pill-warning { background: rgba(245,158,11,0.1); color: var(--accent4); }
    .pill-danger { background: rgba(239,68,68,0.1); color: var(--warn); }
    .pill-info { background: rgba(59,130,246,0.1); color: var(--accent); }
    .pill-purple { background: rgba(139,92,246,0.1); color: var(--accent2); }

    .risk-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
    }

    .risk-avatar {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-sm);
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.72rem;
      font-weight: 800;
      flex-shrink: 0;
    }

    .risk-info { flex: 1; min-width: 0; }
    .risk-name { font-size: 0.875rem; color: var(--text); font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .risk-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; line-height: 1.4; }

    .assignment-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px;
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
    }

    .assignment-icon {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-sm);
      background: var(--surface2);
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--accent);
      flex-shrink: 0;
    }

    .assignment-info { flex: 1; min-width: 0; }
    .assignment-title { font-size: 0.875rem; color: var(--text); font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .assignment-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; line-height: 1.4; }

    .activity-item {
      display: flex;
      gap: 16px;
      position: relative;
    }

    .activity-item:not(:last-child)::after {
      content: '';
      position: absolute;
      top: 24px;
      left: 7px;
      bottom: -20px;
      width: 1px;
      background: var(--border);
    }

    .activity-dot {
      width: 15px;
      height: 15px;
      border-radius: 50%;
      background: var(--surface);
      border: 3px solid var(--border);
      margin-top: 3px;
      flex-shrink: 0;
      position: relative;
      z-index: 1;
    }

    .activity-dot.completed { border-color: var(--accent3); }
    .activity-dot.action { border-color: var(--accent); }
    .activity-dot.warning { border-color: var(--accent4); }
    .activity-dot.danger { border-color: var(--warn); }

    .activity-content { flex: 1; }
    .activity-title { font-size: 0.875rem; color: var(--text); line-height: 1.5; }
    .activity-title strong { font-weight: 700; }
    .activity-time { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }

    .chart-wrap { padding: 4px 0 0; overflow-x: auto; }
    .chart-svg { width: 100%; min-width: 520px; height: 170px; display: block; }

    .chart-legend {
      display: flex;
      gap: 20px;
      margin-top: 20px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--muted);
    }

    .legend-dot { width: 10px; height: 10px; border-radius: 2px; }

    .heatmap {
      display: grid;
      grid-template-columns: 100px repeat(7, 1fr);
      gap: 8px;
      align-items: center;
      min-width: 520px;
    }

    .heatmap-wrap { overflow-x: auto; }

    .heat-label {
      font-size: 0.72rem;
      color: var(--muted);
      font-weight: 600;
    }

    .heat-cell {
      height: 26px;
      border-radius: 5px;
      background: var(--surface2);
      border: 1px solid var(--border);
    }

    .heat-1 { background: rgba(59,130,246,0.16); }
    .heat-2 { background: rgba(59,130,246,0.28); }
    .heat-3 { background: rgba(16,185,129,0.32); }
    .heat-4 { background: rgba(245,158,11,0.38); }
    .heat-5 { background: rgba(239,68,68,0.34); }

    .empty-note {
      color: var(--muted);
      font-size: 0.875rem;
      line-height: 1.6;
      padding: 12px;
      border: 1px dashed var(--border);
      border-radius: var(--radius-sm);
      background: rgba(255,255,255,0.015);
    }

    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--surface2); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--dim); }

    @media (max-width: 1300px) {
      .stats-row { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 1000px) {
      .grid-2, .grid-main { grid-template-columns: 1fr; }
      .topbar-search { display: none; }
    }

    @media (max-width: 760px) {
      .content { padding: 20px; }
      .stats-row { grid-template-columns: 1fr; }
      .welcome-banner { flex-direction: column; align-items: flex-start; }
      .topbar { padding: 0 20px; }
      .class-item { grid-template-columns: 1fr; }
      .class-score { align-items: flex-start; }
    }
  </style>
</head>

<body>
  @include('partials.instructor-sidebar')

  @php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\Route;

    $instructor = Auth::user();

    $routeOrUrl = function ($routeName, $fallback = '#') {
        return Route::has($routeName) ? route($routeName) : $fallback;
    };

    $hasTable = function ($table) {
        try {
            return Schema::hasTable($table);
        } catch (\Throwable $e) {
            return false;
        }
    };

    $activeClasses = 0;
    $totalStudents = 0;
    $assignedChallenges = 0;
    $averageScore = 0;
    $masteryRate = 0;
    $atRiskCount = 0;
    $classes = collect();

    try {
        if ($hasTable('classes')) {
            $classQuery = DB::table('classes')->where('instructor_id', $instructor->id);

            if (Schema::hasColumn('classes', 'is_archived')) {
                $classQuery->where(function ($query) {
                    $query->where('is_archived', false)->orWhereNull('is_archived');
                });
            }

            $activeClasses = (clone $classQuery)->count();
            $classes = (clone $classQuery)->limit(4)->get();
        }

        if ($hasTable('class_student') && $hasTable('classes')) {
            $totalStudents = DB::table('class_student')
                ->join('classes', 'classes.id', '=', 'class_student.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->distinct('class_student.student_id')
                ->count('class_student.student_id');
        }

        if ($hasTable('class_challenge_assignments') && $hasTable('classes')) {
            $assignedChallenges = DB::table('class_challenge_assignments')
                ->join('classes', 'classes.id', '=', 'class_challenge_assignments.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->count();
        }

        if ($hasTable('challenge_submissions') && $hasTable('classes')) {
            $averageScore = (int) round(DB::table('challenge_submissions')
                ->join('classes', 'classes.id', '=', 'challenge_submissions.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->avg('challenge_submissions.score') ?? 0);
        }

        if ($hasTable('student_ilo_mastery') && $hasTable('classes')) {
            $totalMasteryRows = DB::table('student_ilo_mastery')
                ->join('classes', 'classes.id', '=', 'student_ilo_mastery.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->count();

            $satisfiedMasteryRows = DB::table('student_ilo_mastery')
                ->join('classes', 'classes.id', '=', 'student_ilo_mastery.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->where('student_ilo_mastery.gate_satisfied', true)
                ->count();

            $masteryRate = $totalMasteryRows > 0 ? (int) round(($satisfiedMasteryRows / $totalMasteryRows) * 100) : 0;
        }

        if ($hasTable('student_cluster_assignments') && $hasTable('classes')) {
            $atRiskCount = DB::table('student_cluster_assignments')
                ->join('classes', 'classes.id', '=', 'student_cluster_assignments.class_id')
                ->where('classes.instructor_id', $instructor->id)
                ->whereIn('student_cluster_assignments.cluster_label', ['at_risk', 'disengaged'])
                ->count();
        }
    } catch (\Throwable $e) {
        $activeClasses = 0;
        $totalStudents = 0;
        $assignedChallenges = 0;
        $averageScore = 0;
        $masteryRate = 0;
        $atRiskCount = 0;
        $classes = collect();
    }

    $displayClasses = $classes->count() ? $classes : collect([
        (object) ['name' => 'Python for Data Science', 'section' => 'BSIT 3A', 'term' => 'Current Term'],
        (object) ['name' => 'SQL Analytics', 'section' => 'BSCS 2B', 'term' => 'Current Term'],
        (object) ['name' => 'Machine Learning Basics', 'section' => 'BSIT 4A', 'term' => 'Current Term'],
    ]);
  @endphp

  <div class="main">
    <header class="topbar">
      <h1>Instructor Dashboard</h1>

      <div class="topbar-search">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="8"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" placeholder="Search classes, students, reports..." />
      </div>

      <div class="topbar-btn" title="Notifications">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="notif-dot"></span>
      </div>

      <a href="{{ $routeOrUrl('profile', '/profile') }}" class="topbar-btn" title="Profile">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </a>
    </header>

    <main class="content">
      <section class="welcome-banner">
        <div class="welcome-text">
          <h2>Welcome back, {{ $instructor->name ?? 'Instructor' }}!</h2>
          <p>
            Monitor class performance, assign learning materials, review submissions, and quickly identify students who may need support.
          </p>

          <div class="welcome-cta">
            <a href="{{ $routeOrUrl('instructor.classes.create', '#') }}" class="btn btn-primary">
              Create Class
            </a>
            <a href="{{ $routeOrUrl('instructor.classes.index', '#') }}" class="btn btn-ghost">
              Manage Classes
            </a>
            <a href="{{ $routeOrUrl('instructor.analytics.index', '#') }}" class="btn btn-ghost">
              View Analytics
            </a>
          </div>
        </div>
      </section>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Active Classes</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m8-6a4 4 0 11-8 0 4 4 0 018 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $activeClasses }}</span>
          </div>
          <div class="stat-note">Currently assigned to you</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min($activeClasses * 20, 100) }}%;background:var(--accent)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Total Students</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12 14c4.418 0 8 2.239 8 5v1H4v-1c0-2.761 3.582-5 8-5z"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $totalStudents }}</span>
          </div>
          <div class="stat-note">Across your classes</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min($totalStudents, 100) }}%;background:var(--accent3)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Challenges Assigned</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $assignedChallenges }}</span>
          </div>
          <div class="stat-note">Active graded/practice tasks</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min($assignedChallenges * 8, 100) }}%;background:var(--accent2)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">Average Class Score</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4"/>
                <circle cx="12" cy="12" r="9"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $averageScore }}%</span>
          </div>
          <div class="stat-note">Weighted score estimate</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $averageScore }}%;background:var(--accent4)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">ILO Mastery Rate</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $masteryRate }}%</span>
          </div>
          <div class="stat-note">Students clearing mastery gates</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $masteryRate }}%;background:var(--accent3)"></div></div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <span class="stat-title">At-Risk Students</span>
            <div class="stat-icon">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M12 9v4m0 4h.01"/>
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              </svg>
            </div>
          </div>
          <div class="stat-main">
            <span class="stat-value">{{ $atRiskCount }}</span>
          </div>
          <div class="stat-note">Needs review or intervention</div>
          <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ min($atRiskCount * 15, 100) }}%;background:var(--warn)"></div></div>
        </div>
      </div>

      <div class="grid-main">
        <div style="display:flex; flex-direction:column; gap:24px;">
          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Class Performance Overview</div>
                <div class="card-subtitle">Comparative view of average scores, engagement, and mastery.</div>
              </div>
              <a href="{{ $routeOrUrl('instructor.analytics.index', '#') }}" class="link-sm">View Report →</a>
            </div>

            <div class="card-body">
              <div class="chart-wrap">
                <svg class="chart-svg" viewBox="0 0 620 170" preserveAspectRatio="none">
                  <line x1="0" y1="135" x2="620" y2="135" stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="100" x2="620" y2="100" stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="65" x2="620" y2="65" stroke="#1e2f47" stroke-width="1"/>
                  <line x1="0" y1="30" x2="620" y2="30" stroke="#1e2f47" stroke-width="1"/>

                  <rect x="45" y="58" width="32" height="77" rx="4" fill="#3b82f6"/>
                  <rect x="83" y="40" width="32" height="95" rx="4" fill="#10b981"/>
                  <rect x="121" y="72" width="32" height="63" rx="4" fill="#8b5cf6"/>

                  <rect x="215" y="82" width="32" height="53" rx="4" fill="#3b82f6"/>
                  <rect x="253" y="64" width="32" height="71" rx="4" fill="#10b981"/>
                  <rect x="291" y="95" width="32" height="40" rx="4" fill="#8b5cf6"/>

                  <rect x="385" y="48" width="32" height="87" rx="4" fill="#3b82f6"/>
                  <rect x="423" y="35" width="32" height="100" rx="4" fill="#10b981"/>
                  <rect x="461" y="62" width="32" height="73" rx="4" fill="#8b5cf6"/>

                  <text x="99" y="158" fill="#7f93b0" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Class A</text>
                  <text x="269" y="158" fill="#7f93b0" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Class B</text>
                  <text x="439" y="158" fill="#7f93b0" font-size="11" font-family="Inter,sans-serif" text-anchor="middle">Class C</text>
                </svg>
              </div>

              <div class="chart-legend">
                <div class="legend-item"><div class="legend-dot" style="background:var(--accent)"></div>Average Score</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--accent3)"></div>Engagement</div>
                <div class="legend-item"><div class="legend-dot" style="background:var(--accent2)"></div>ILO Mastery</div>
              </div>
            </div>
          </div>

          <div class="grid-2">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Active Classes</div>
                  <div class="card-subtitle">Quick access to your current class sections.</div>
                </div>
                <a href="{{ $routeOrUrl('instructor.classes.index', '#') }}" class="link-sm">All Classes →</a>
              </div>

              <div class="card-body">
                <div class="class-list">
                  @forelse ($displayClasses as $index => $class)
                    <div class="class-item">
                      <div>
                        <div class="class-name">{{ $class->name ?? 'Untitled Class' }}</div>
                        <div class="class-meta">
                          {{ $class->section ?? 'No Section' }} · {{ $class->term ?? 'Current Term' }}
                        </div>
                      </div>

                      <div class="class-score">
                        <strong>{{ [88, 76, 92, 81][$index % 4] }}%</strong>
                        <span class="pill {{ $index === 1 ? 'pill-warning' : 'pill-ok' }}">
                          {{ $index === 1 ? 'Needs Review' : 'On Track' }}
                        </span>
                      </div>
                    </div>
                  @empty
                    <div class="empty-note">
                      No classes found yet. Create a class to begin assigning modules and challenges.
                    </div>
                  @endforelse
                </div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Assigned Work</div>
                  <div class="card-subtitle">Recently assigned modules and challenges.</div>
                </div>
                <a href="{{ $routeOrUrl('instructor.assignments.index', '#') }}" class="link-sm">Manage →</a>
              </div>

              <div class="card-body">
                <div class="assignment-list">
                  <div class="assignment-item">
                    <div class="assignment-icon">
                      <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                      </svg>
                    </div>
                    <div class="assignment-info">
                      <div class="assignment-title">Data Cleaning with Pandas</div>
                      <div class="assignment-meta">Module · Due Friday · Required</div>
                    </div>
                    <span class="pill pill-info">Module</span>
                  </div>

                  <div class="assignment-item">
                    <div class="assignment-icon">
                      <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                      </svg>
                    </div>
                    <div class="assignment-info">
                      <div class="assignment-title">EDA on Housing Dataset</div>
                      <div class="assignment-meta">Challenge · 3 attempts · AI allowed</div>
                    </div>
                    <span class="pill pill-purple">Challenge</span>
                  </div>

                  <div class="assignment-item">
                    <div class="assignment-icon">
                      <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4"/>
                        <circle cx="12" cy="12" r="9"/>
                      </svg>
                    </div>
                    <div class="assignment-info">
                      <div class="assignment-title">SQL Aggregations Quiz</div>
                      <div class="assignment-meta">Practice · Unlimited attempts</div>
                    </div>
                    <span class="pill pill-ok">Practice</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Student Engagement Heatmap</div>
                <div class="card-subtitle">Activity intensity per class for the current week.</div>
              </div>
            </div>

            <div class="card-body">
              <div class="heatmap-wrap">
                <div class="heatmap">
                  <div class="heat-label"></div>
                  <div class="heat-label">Mon</div>
                  <div class="heat-label">Tue</div>
                  <div class="heat-label">Wed</div>
                  <div class="heat-label">Thu</div>
                  <div class="heat-label">Fri</div>
                  <div class="heat-label">Sat</div>
                  <div class="heat-label">Sun</div>

                  <div class="heat-label">Class A</div>
                  <div class="heat-cell heat-2"></div><div class="heat-cell heat-3"></div><div class="heat-cell heat-4"></div><div class="heat-cell heat-3"></div><div class="heat-cell heat-2"></div><div class="heat-cell heat-1"></div><div class="heat-cell"></div>

                  <div class="heat-label">Class B</div>
                  <div class="heat-cell heat-1"></div><div class="heat-cell heat-2"></div><div class="heat-cell heat-2"></div><div class="heat-cell heat-1"></div><div class="heat-cell heat-3"></div><div class="heat-cell"></div><div class="heat-cell"></div>

                  <div class="heat-label">Class C</div>
                  <div class="heat-cell heat-3"></div><div class="heat-cell heat-4"></div><div class="heat-cell heat-5"></div><div class="heat-cell heat-4"></div><div class="heat-cell heat-3"></div><div class="heat-cell heat-2"></div><div class="heat-cell heat-1"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:24px;">
          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">At-Risk Alerts</div>
                <div class="card-subtitle">Students flagged by mastery, pass rate, or inactivity.</div>
              </div>
              <a href="{{ $routeOrUrl('instructor.students.risk', '#') }}" class="link-sm">Review →</a>
            </div>

            <div class="card-body">
              <div class="risk-list">
                <div class="risk-item">
                  <div class="risk-avatar">JR</div>
                  <div class="risk-info">
                    <div class="risk-name">Juan R.</div>
                    <div class="risk-meta">Low ILO mastery · 2 failed gates</div>
                  </div>
                  <span class="pill pill-danger">At Risk</span>
                </div>

                <div class="risk-item">
                  <div class="risk-avatar">AM</div>
                  <div class="risk-info">
                    <div class="risk-name">Ana M.</div>
                    <div class="risk-meta">No activity in 7 days</div>
                  </div>
                  <span class="pill pill-warning">Disengaged</span>
                </div>

                <div class="risk-item">
                  <div class="risk-avatar">KC</div>
                  <div class="risk-info">
                    <div class="risk-name">Karl C.</div>
                    <div class="risk-meta">Missing 2 submissions</div>
                  </div>
                  <span class="pill pill-warning">Follow Up</span>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Recent Instructor Activity</div>
                <div class="card-subtitle">Latest teaching and assessment events.</div>
              </div>
            </div>

            <div class="card-body">
              <div class="activity-list">
                <div class="activity-item">
                  <div class="activity-dot completed"></div>
                  <div class="activity-content">
                    <div class="activity-title">Published <strong>SQL Aggregations Quiz</strong> to BSCS 2B</div>
                    <div class="activity-time">1 hour ago</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-dot action"></div>
                  <div class="activity-content">
                    <div class="activity-title">Reviewed <strong>18 submissions</strong> from Python for Data Science</div>
                    <div class="activity-time">3 hours ago</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-dot warning"></div>
                  <div class="activity-content">
                    <div class="activity-title">At-risk alert generated for <strong>3 students</strong></div>
                    <div class="activity-time">Yesterday</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-dot completed"></div>
                  <div class="activity-content">
                    <div class="activity-title">Assigned <strong>Data Cleaning with Pandas</strong> module</div>
                    <div class="activity-time">2 days ago</div>
                  </div>
                </div>

                <div class="activity-item">
                  <div class="activity-dot danger"></div>
                  <div class="activity-content">
                    <div class="activity-title">Late deadline reminder sent to <strong>Class B</strong></div>
                    <div class="activity-time">3 days ago</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Quick Actions</div>
                <div class="card-subtitle">Common teaching tasks.</div>
              </div>
            </div>

            <div class="card-body" style="display:flex; flex-direction:column; gap:12px;">
              <a href="{{ $routeOrUrl('instructor.classes.create', '#') }}" class="btn btn-ghost">Create New Class</a>
              <a href="{{ $routeOrUrl('instructor.modules.index', '#') }}" class="btn btn-ghost">Assign Module</a>
              <a href="{{ $routeOrUrl('instructor.challenges.index', '#') }}" class="btn btn-ghost">Assign Challenge</a>
              <a href="{{ $routeOrUrl('instructor.reports.export', '#') }}" class="btn btn-ghost">Export Reports</a>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.stat-bar-fill').forEach(el => {
        const target = el.style.width;
        el.style.width = '0%';
        setTimeout(() => { el.style.width = target; }, 150);
      });
    });
  </script>
</body>
</html>
