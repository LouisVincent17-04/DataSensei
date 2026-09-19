<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Platform Analytics — DataSensei</title>
<style>
    /* Platform analytics, including the x-superadmin.analytics-table and
       x-superadmin.group-card components. Colours, type and radius come from
       partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --green:   var(--ds-success);
      --orange:  var(--ds-warning);
      --red:     var(--ds-danger);
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:var(--ds-font-sans);background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden}
    .main{flex:1;min-width:0;display:flex;flex-direction:column}

    /* Title bar */
    .topbar{min-height:60px;padding:0 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px 16px;
      background:var(--bg);border-bottom:1px solid var(--border);position:sticky;top:var(--ds-sticky-top,0px);z-index:10}
    .topbar .range-label{color:var(--muted);font-size:.8125rem;font-variant-numeric:tabular-nums}
    .content{padding:28px 32px 48px;display:flex;flex-direction:column;gap:32px}

    /* Intro and section links */
    .intro{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px 24px;margin-bottom:-8px}
    .intro p{flex:1 1 360px;min-width:0;max-width:72ch;color:var(--muted);font-size:.875rem;line-height:1.55}
    .tabs{display:flex;gap:6px;flex-wrap:wrap}
    .tab{display:inline-flex;align-items:center;min-height:32px;padding:0 12px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500;text-decoration:none;
      transition:background .12s ease,color .12s ease}
    .tab:hover{background:var(--ds-surface-hover);color:var(--text)}
    section[id]{scroll-margin-top:calc(var(--ds-sticky-top,0px) + 76px)}

    /* Date range */
    .filter-card{margin-top:-8px;padding:16px 20px;display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;
      background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .field{display:flex;flex-direction:column;gap:6px;min-width:0}
    .field label{color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500;line-height:1.35}
    .input{min-height:38px;padding:8px 12px;background:var(--surface3);border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;color-scheme:dark;transition:border-color .12s ease,box-shadow .12s ease}
    .input:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    /* Buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn-primary{background:var(--accent);border-color:var(--accent);color:#fff}
    .btn-primary:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .btn-small{min-height:32px;padding:0 12px;font-size:.8125rem;flex-shrink:0}

    /* Summary figures */
    .stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
    .stat{padding:16px 18px;display:flex;flex-direction:column;gap:4px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius)}
    .stat-label{color:var(--muted);font-size:.8125rem;font-weight:500}
    .stat-value{margin-top:2px;color:var(--text);font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat-sub{color:var(--muted);font-size:.75rem;line-height:1.4}
    /* Tones are decorative per-figure colours from the service; figures read in one colour. */
    .tone-blue,.tone-purple,.tone-green,.tone-orange,.tone-red{color:var(--text)}

    /* Sections */
    .section{display:flex;flex-direction:column;gap:16px}
    .section-head{display:flex;align-items:flex-end;justify-content:space-between;flex-wrap:wrap;gap:12px 16px}
    .section-head > div{flex:1 1 320px}
    .section-title{color:var(--text);font-size:1.0625rem;font-weight:600;line-height:1.35;letter-spacing:-.01em}
    .section-sub{margin-top:2px;max-width:72ch;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px;align-items:start}
    .grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;align-items:start}

    /* Cards and tables */
    .card{min-width:0;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}
    .card-header{padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
    .card-header > div{min-width:0}
    .card-title{color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.35}
    .card-sub{margin-top:2px;color:var(--muted);font-size:.8125rem;line-height:1.45}
    .card-body{padding:16px 20px;display:grid;gap:12px}
    .tbl-wrap{overflow-x:auto}
    .tbl{width:100%;border-collapse:collapse;font-variant-numeric:tabular-nums}
    .tbl-wrap > table.tbl{min-width:560px}
    .tbl th{padding:10px 14px;background:var(--surface3);border-bottom:1px solid var(--border);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .tbl td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.45;vertical-align:middle}
    .tbl tr:last-child td{border-bottom:none}
    .tbl tbody tr:hover td{background:rgba(255,255,255,.02)}
    .tbl td.empty{padding:32px 20px}

    /* Badges: status, severity, and the "Review" tag on tables that need attention */
    .pill{display:inline-flex;align-items:center;flex-shrink:0;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap;text-transform:capitalize}
    .pill-green{background:var(--ds-success-soft);border-color:var(--ds-success-border);color:var(--ds-success-text)}
    .pill-red{background:var(--ds-danger-soft);border-color:var(--ds-danger-border);color:var(--ds-danger-text)}
    .pill-orange{background:var(--ds-warning-soft);border-color:var(--ds-warning-border);color:var(--ds-warning-text)}
    .pill-blue,.pill-purple{background:var(--ds-accent-soft);border-color:var(--ds-accent-border);color:var(--ds-accent-text)}

    /* Horizontal bars */
    .bar-list{display:flex;flex-direction:column;gap:12px}
    .bar-row{display:grid;grid-template-columns:minmax(0,140px) minmax(0,1fr) 56px;gap:12px;align-items:center}
    .bar-label{color:var(--ds-text-secondary);font-size:.8125rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
    .bar-shell{height:6px;border-radius:999px;background:var(--surface2);overflow:hidden}
    .bar-fill{height:100%;min-width:3px;border-radius:999px;background:var(--accent)}
    .bar-value{color:var(--text);font-size:.8125rem;font-weight:600;text-align:right;font-variant-numeric:tabular-nums}

    /* Insights */
    .insight-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .insight{padding:16px 18px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);
      --insight-dot:var(--ds-accent)}
    .insight.green{--insight-dot:var(--ds-success)}
    .insight.orange{--insight-dot:var(--ds-warning)}
    .insight.red{--insight-dot:var(--ds-danger)}
    .insight.purple{--insight-dot:var(--ds-accent)}
    .insight h3{display:flex;align-items:baseline;gap:8px;margin-bottom:6px;color:var(--text);font-size:.9375rem;font-weight:600;line-height:1.35}
    .insight h3::before{content:"";width:8px;height:8px;flex:0 0 8px;border-radius:50%;background:var(--insight-dot);transform:translateY(-1px)}
    .insight p{color:var(--ds-text-secondary);font-size:.8125rem;line-height:1.55}

    .empty{padding:24px 12px;text-align:center;color:var(--muted);font-size:.875rem}
    .nowrap{white-space:nowrap}
    .muted{color:var(--muted);white-space:nowrap}
    .strong{color:var(--text);font-weight:600}

    /* Table pairs get the full width until there is room for two readable tables. */
    @media(max-width:1440px){
      .grid-2:has(.tbl-wrap){grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:1100px){
      .grid-2{grid-template-columns:minmax(0,1fr)}
      .stat-grid,.grid-3{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:900px){
      .topbar{min-height:56px;padding:8px 20px}
      .content{padding:24px 20px 40px;gap:28px}
    }
    @media(max-width:640px){
      .topbar{padding:8px 16px;position:static}
      section[id]{scroll-margin-top:calc(var(--ds-sticky-top,0px) + 12px)}
      .tbl td.empty{text-align:left}
      .content{padding:20px 16px 32px;gap:24px}
      .grid-3,.insight-grid{grid-template-columns:minmax(0,1fr)}
      .filter-card{padding:16px}
      .filter-card .field{flex:1 1 130px}
      .filter-card .input{width:100%}
      .filter-card .btn{flex:1 1 auto}
      .card-header{padding:12px 16px}
      .card-body{padding:16px}
      .stat{padding:14px 16px}
      .bar-row{grid-template-columns:minmax(0,1fr) auto;gap:6px 12px}
      .bar-row .bar-shell{grid-column:1 / -1;grid-row:2}
    }
    @media(max-width:420px){
      .stat-grid{grid-template-columns:minmax(0,1fr)}
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Platform Analytics', 'pageDescription' => 'Platform-wide usage and performance figures.'])
</head>
<body>
@include('partials.superadmin-sidebar')

<div class="main">
  <div class="topbar">
    <h1 class="ds-page-title">Platform Analytics</h1>
    <span class="range-label">{{ $range['from_date'] }} → {{ $range['to_date'] }}</span>
  </div>

  <main class="content">
    <section class="intro">
      <p>Monitor users, institutions, instructors, learning activity, assessment quality, anti-cheat events, content usage, engagement, and risk signals across the entire platform.</p>
      <nav class="tabs" aria-label="Sections">
        <a href="#overview" class="tab">Overview</a>
        <a href="#students" class="tab">Students</a>
        <a href="#learning" class="tab">Learning</a>
        <a href="#institutions" class="tab">Institutions</a>
        <a href="#integrity" class="tab">Integrity</a>
      </nav>
    </section>

    <form method="GET" action="{{ route('superadmin.analytics.index') }}" class="filter-card">
      <div class="field">
        <label>From</label>
        <input class="input" type="date" name="from" value="{{ $range['from_date'] }}">
      </div>
      <div class="field">
        <label>To</label>
        <input class="input" type="date" name="to" value="{{ $range['to_date'] }}">
      </div>
      <button class="btn btn-primary" type="submit">Apply Range</button>
      <a class="btn" href="{{ route('superadmin.analytics.index') }}">Last 30 Days</a>
      <a class="btn" href="{{ route('superadmin.analytics.export', ['section' => 'overview', 'from' => $range['from_date'], 'to' => $range['to_date']]) }}">Export Overview</a>
    </form>

    <section id="overview" class="section">
      <div class="section-head">
        <div>
          <div class="section-title">Executive Overview</div>
          <div class="section-sub">Real counts from your DataSensei tables. Missing optional tables safely show as zero instead of crashing.</div>
        </div>
      </div>
      <div class="stat-grid">
        @foreach($summary as $stat)
          <div class="stat">
            <div class="stat-label">{{ $stat['label'] }}</div>
            <div class="stat-value tone-{{ $stat['tone'] ?? 'blue' }}">{{ number_format((int) $stat['value']) }}</div>
            <div class="stat-sub">{{ $stat['sub'] }}</div>
          </div>
        @endforeach
      </div>
    </section>

    <section class="section">
      <div class="section-head">
        <div>
          <div class="section-title">Smart Insights</div>
          <div class="section-sub">Quick interpretation of the selected analytics range.</div>
        </div>
      </div>
      <div class="insight-grid">
        @foreach($insights as $insight)
          <div class="insight {{ $insight['tone'] }}">
            <h3>{{ $insight['title'] }}</h3>
            <p>{{ $insight['body'] }}</p>
          </div>
        @endforeach
      </div>
    </section>

    <section class="section">
      <div class="grid-2">
        <div class="card">
          <div class="card-header"><div><div class="card-title">Role Distribution</div><div class="card-sub">Accounts by platform role.</div></div></div>
          <div class="card-body">
            @php $maxRole = max(1, collect($roleDistribution)->max('total') ?? 1); @endphp
            @forelse($roleDistribution as $row)
              <div class="bar-row">
                <div class="bar-label">{{ $row['label'] }}</div>
                <div class="bar-shell"><div class="bar-fill" style="width: {{ max(3, ($row['total'] / $maxRole) * 100) }}%"></div></div>
                <div class="bar-value">{{ number_format($row['total']) }}</div>
              </div>
            @empty
              <div class="empty">No role data yet.</div>
            @endforelse
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div><div class="card-title">Daily Learning Activity</div><div class="card-sub">MCQ attempts, coding submissions, assignment submissions, and anti-cheat logs.</div></div></div>
          <div class="card-body">
            @php $maxActivity = max(1, collect($activityTrend)->max('total') ?? 1); @endphp
            <div class="bar-list">
              @forelse(array_slice($activityTrend, -14) as $row)
                <div class="bar-row">
                  <div class="bar-label">{{ \Carbon\Carbon::parse($row['date'])->format('M d') }}</div>
                  <div class="bar-shell"><div class="bar-fill" style="width: {{ max(3, ($row['total'] / $maxActivity) * 100) }}%"></div></div>
                  <div class="bar-value">{{ number_format($row['total']) }}</div>
                </div>
              @empty
                <div class="empty">No activity in this range.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="students" class="section">
      <div class="section-head">
        <div><div class="section-title">Student Analytics</div><div class="section-sub">XP, challenge performance, coding performance, assignment performance, and at-risk signals.</div></div>
        <a class="btn btn-small" href="{{ route('superadmin.analytics.export', ['section' => 'students', 'from' => $range['from_date'], 'to' => $range['to_date']]) }}">Export Students</a>
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Top XP Students" subtitle="Highest gamification progress." :rows="$students['topXp']" :columns="['name'=>'Student','email'=>'Email','xp'=>'XP','streak'=>'Streak','last_activity'=>'Last Activity']" />
        <x-superadmin.analytics-table title="Top MCQ Performers" subtitle="Ranked by average MCQ challenge score." :rows="$students['topMcq']" :columns="['name'=>'Student','attempts_count'=>'Attempts','avg_score'=>'Avg Score','total_xp'=>'XP','avg_time_seconds'=>'Avg Time']" />
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Top Coding Performers" subtitle="Based on coding submissions and test case success." :rows="$students['topCoding']" :columns="['name'=>'Student','submissions_count'=>'Submissions','passed_count'=>'Passed','avg_test_score'=>'Avg Tests','total_xp'=>'XP']" />
        <x-superadmin.analytics-table title="At-Risk Students" subtitle="Low XP, inactivity, or low assignment average." :rows="$students['atRisk']" :columns="['name'=>'Student','email'=>'Email','xp'=>'XP','assignment_avg'=>'Assignment Avg','risk_reasons'=>'Reasons']" danger />
      </div>
    </section>

    <section id="institutions" class="section">
      <div class="section-head">
        <div><div class="section-title">Instructor and Institution Analytics</div><div class="section-sub">Super Admin can monitor which institutions and instructors are actually using the platform.</div></div>
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Most Active Instructors" subtitle="Classes, students, assignments, and submissions connected to instructors." :rows="$instructors['topInstructors']" :columns="['name'=>'Instructor','email'=>'Email','classes_count'=>'Classes','students_count'=>'Students','assignments_count'=>'Assignments','submissions_count'=>'Submissions']" />
        <x-superadmin.analytics-table title="Top Institutions" subtitle="Ranked by student and class activity." :rows="$institutions['topInstitutions']" :columns="['name'=>'Institution','status'=>'Status','users_count'=>'Users','classes_count'=>'Classes','students_count'=>'Students','assignments_count'=>'Assignments']" />
      </div>
    </section>

    <section id="learning" class="section">
      <div class="section-head">
        <div><div class="section-title">Learning Analytics</div><div class="section-sub">Most attempted content, hardest content, coding challenge quality, and assignment performance.</div></div>
        <a class="btn btn-small" href="{{ route('superadmin.analytics.export', ['section' => 'learning', 'from' => $range['from_date'], 'to' => $range['to_date']]) }}">Export Learning</a>
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Most Attempted MCQ Challenges" subtitle="High-traffic challenge content." :rows="$learning['mostAttemptedMcq']" :columns="['title'=>'Challenge','level'=>'Level','attempts_count'=>'Attempts','avg_score'=>'Avg Score','avg_time_seconds'=>'Avg Time']" />
        <x-superadmin.analytics-table title="Hardest MCQ Challenges" subtitle="Lowest average score in the selected range." :rows="$learning['hardestMcq']" :columns="['title'=>'Challenge','level'=>'Level','attempts_count'=>'Attempts','avg_score'=>'Avg Score']" danger />
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Coding Challenge Performance" subtitle="Submissions and test pass rates by challenge." :rows="$learning['codingPerformance']" :columns="['title'=>'Challenge','level'=>'Level','submissions_count'=>'Submissions','passed_count'=>'Passed','avg_test_score'=>'Avg Tests']" />
        <x-superadmin.analytics-table title="Hardest Coding Questions" subtitle="Lowest average test-case performance." :rows="$learning['hardestCoding']" :columns="['challenge_title'=>'Challenge','problem_preview'=>'Question Preview','submissions_count'=>'Submissions','passed_count'=>'Passed','avg_test_score'=>'Avg Tests']" danger />
      </div>
      <div class="grid-2">
        <x-superadmin.analytics-table title="Assignment Performance" subtitle="Class-assigned academic work." :rows="$learning['assignmentPerformance']" :columns="['title'=>'Assignment','class_name'=>'Class','status'=>'Status','submissions_count'=>'Submissions','late_count'=>'Late','avg_score'=>'Avg Score']" />
        <x-superadmin.analytics-table title="Challenge Category Breakdown" subtitle="Your five difficulty levels as the assessment ladder." :rows="$learning['categoryBreakdown']" :columns="['level'=>'Level','mcq_count'=>'MCQ','coding_count'=>'Coding']" />
      </div>
    </section>

    <section class="section">
      <div class="section-head">
        <div><div class="section-title">Content Analytics</div><div class="section-sub">Library usage and class assignment status.</div></div>
      </div>
      <div class="grid-3">
        <x-superadmin.group-card title="Modules by Year Level" :rows="$content['moduleLibraryByYear']" />
        <x-superadmin.group-card title="Assignment Library Types" :rows="$content['assignmentLibraryTypes']" />
        <x-superadmin.group-card title="Class Assignment Status" :rows="$content['classAssignmentStatus']" />
      </div>
    </section>

    <section id="integrity" class="section">
      <div class="section-head">
        <div><div class="section-title">Integrity and Anti-Cheat Analytics</div><div class="section-sub">Instructor assignment anti-cheat logs summarized for Super Admin oversight.</div></div>
        <a class="btn btn-small" href="{{ route('superadmin.analytics.export', ['section' => 'anticheat', 'from' => $range['from_date'], 'to' => $range['to_date']]) }}">Export Events</a>
      </div>
      <div class="grid-2">
        <x-superadmin.group-card title="Event Types" :rows="$antiCheat['eventTypes']" />
        <x-superadmin.group-card title="Severity" :rows="$antiCheat['severity']" />
      </div>
      <x-superadmin.analytics-table title="Recent Anti-Cheat Events" subtitle="Latest assignment integrity events in the selected range." :rows="$antiCheat['recentEvents']" :columns="['student_name'=>'Student','student_email'=>'Email','assignment_title'=>'Assignment','event_type'=>'Event','severity'=>'Severity','occurred_at'=>'Occurred']" danger />
    </section>
  </main>
</div>
</body>
</html>
