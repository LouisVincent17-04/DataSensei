<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Platform Analytics</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:#0d1320; --surface:#111c2d; --surface2:#1a2638; --surface3:#22314a;
      --border:#1e2f47; --border-hover:#2c4168; --accent:#3b82f6; --accent2:#8b5cf6;
      --green:#10b981; --orange:#f59e0b; --red:#ef4444; --text:#fafafa;
      --muted:#8ba0bc; --dim:#48607f; --radius:12px; --radius-sm:8px;
    }
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden;-webkit-font-smoothing:antialiased}
    .main{flex:1;min-width:0;display:flex;flex-direction:column}
    .topbar{height:64px;background:var(--bg);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 32px;gap:16px;position:sticky;top:0;z-index:10}
    .topbar h1{font-size:1.125rem;font-weight:700;letter-spacing:-.02em;flex:1}
    .topbar .range-label{font-size:.78rem;color:var(--muted)}
    .content{padding:32px;display:flex;flex-direction:column;gap:24px;overflow-y:auto}
    .hero{background:linear-gradient(135deg,rgba(59,130,246,.14),rgba(139,92,246,.12));border:1px solid rgba(139,92,246,.35);border-radius:var(--radius);padding:26px 28px;display:flex;justify-content:space-between;gap:24px;align-items:flex-start}
    .hero h2{font-size:1.5rem;letter-spacing:-.03em;margin-bottom:6px}
    .hero p{color:var(--muted);font-size:.9rem;line-height:1.55;max-width:840px}
    .filter-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;display:flex;align-items:end;gap:12px;flex-wrap:wrap}
    .field{display:flex;flex-direction:column;gap:6px}.field label{font-size:.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;font-weight:700}
    .input{background:var(--surface2);border:1px solid var(--border);color:var(--text);border-radius:var(--radius-sm);padding:9px 12px;font-family:inherit;font-size:.86rem;outline:none}.input:focus{border-color:var(--accent)}
    .btn{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:9px 13px;font-size:.84rem;font-weight:700;font-family:inherit;cursor:pointer;text-decoration:none;color:var(--text);background:var(--surface2);transition:.15s}.btn:hover{border-color:var(--border-hover);background:var(--surface3)}
    .btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));border-color:transparent;color:white}.btn-primary:hover{filter:brightness(1.08)}
    .btn-small{padding:6px 10px;font-size:.76rem}
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:16px}.stat{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:18px 20px;min-height:122px;display:flex;flex-direction:column;gap:8px}.stat:hover{border-color:var(--border-hover)}
    .stat-label{font-size:.72rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--muted)}.stat-value{font-size:2rem;font-weight:800;line-height:1;letter-spacing:-.04em}.stat-sub{font-size:.76rem;color:var(--muted);line-height:1.4}.tone-blue{color:var(--accent)}.tone-purple{color:var(--accent2)}.tone-green{color:var(--green)}.tone-orange{color:var(--orange)}.tone-red{color:var(--red)}
    .section{display:flex;flex-direction:column;gap:14px}.section-head{display:flex;align-items:center;justify-content:space-between;gap:14px}.section-title{font-size:1rem;font-weight:800;letter-spacing:-.02em}.section-sub{color:var(--muted);font-size:.82rem;margin-top:3px;line-height:1.45}
    .grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}@media(max-width:1100px){.grid-2,.grid-3{grid-template-columns:1fr}.hero{flex-direction:column}}
    .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden}.card-header{padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:16px}.card-title{font-size:.95rem;font-weight:800}.card-sub{font-size:.78rem;color:var(--muted);margin-top:4px;line-height:1.45}.card-body{padding:18px 20px}.tbl-wrap{overflow-x:auto}.tbl{width:100%;border-collapse:collapse}.tbl th{font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;color:var(--dim);text-align:left;padding:9px 11px;border-bottom:1px solid var(--border);white-space:nowrap}.tbl td{font-size:.83rem;color:var(--text);padding:11px;border-bottom:1px solid var(--border);vertical-align:top}.tbl tr:last-child td{border-bottom:none}.tbl tr:hover td{background:rgba(255,255,255,.02)}
    .pill{display:inline-flex;align-items:center;border-radius:999px;padding:3px 9px;border:1px solid var(--border);font-size:.68rem;font-weight:800;text-transform:uppercase;letter-spacing:.04em;white-space:nowrap}.pill-green{background:rgba(16,185,129,.1);color:var(--green);border-color:rgba(16,185,129,.25)}.pill-red{background:rgba(239,68,68,.1);color:var(--red);border-color:rgba(239,68,68,.25)}.pill-orange{background:rgba(245,158,11,.1);color:var(--orange);border-color:rgba(245,158,11,.25)}.pill-blue{background:rgba(59,130,246,.1);color:#60a5fa;border-color:rgba(59,130,246,.25)}.pill-purple{background:rgba(139,92,246,.1);color:#a78bfa;border-color:rgba(139,92,246,.25)}
    .bar-list{display:flex;flex-direction:column;gap:12px}.bar-row{display:grid;grid-template-columns:150px 1fr 70px;gap:12px;align-items:center}.bar-label{font-size:.8rem;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.bar-shell{height:9px;border-radius:999px;background:var(--surface2);overflow:hidden;border:1px solid var(--border)}.bar-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,var(--accent),var(--accent2));min-width:3px}.bar-value{text-align:right;font-size:.78rem;font-weight:800;color:var(--text)}
    .insight-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:14px}.insight{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:16px;border-left:4px solid var(--accent)}.insight.green{border-left-color:var(--green)}.insight.orange{border-left-color:var(--orange)}.insight.red{border-left-color:var(--red)}.insight.purple{border-left-color:var(--accent2)}.insight h3{font-size:.9rem;margin-bottom:6px}.insight p{font-size:.8rem;color:var(--muted);line-height:1.55}.empty{padding:24px;text-align:center;color:var(--muted);font-size:.85rem}.nowrap{white-space:nowrap}.muted{color:var(--muted)}.strong{font-weight:800}.tabs{display:flex;gap:8px;flex-wrap:wrap}.tab{padding:7px 10px;border:1px solid var(--border);border-radius:999px;color:var(--muted);text-decoration:none;font-size:.75rem;font-weight:800;background:var(--surface)}.tab:hover{color:var(--text);border-color:var(--border-hover)}
  </style>
</head>
<body>
@include('partials.superadmin-sidebar')

<div class="main">
  <div class="topbar">
    <h1>Platform Analytics</h1>
    <span class="range-label">{{ $range['from_date'] }} → {{ $range['to_date'] }}</span>
  </div>

  <main class="content">
    <section class="hero">
      <div>
        <h2>DataSensei Platform Intelligence</h2>
        <p>Monitor users, institutions, instructors, learning activity, assessment quality, anti-cheat events, content usage, engagement, and risk signals across the entire platform.</p>
      </div>
      <div class="tabs">
        <a href="#overview" class="tab">Overview</a>
        <a href="#students" class="tab">Students</a>
        <a href="#learning" class="tab">Learning</a>
        <a href="#institutions" class="tab">Institutions</a>
        <a href="#integrity" class="tab">Integrity</a>
      </div>
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
