<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — Instructor Assignments</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#1e2f47;--border-hover:#2c4168;--accent:#3b82f6;--accent-hover:#2563eb;--accent2:#8b5cf6;--accent3:#10b981;--warn:#ef4444;--warn2:#f59e0b;--text:#fafafa;--muted:#7f93b0;--dim:#3d5272;--radius:14px;--radius-sm:10px;--sidebar-w:270px}*{box-sizing:border-box;margin:0;padding:0}html,body{min-height:100%;font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.12),transparent 34rem),radial-gradient(circle at top right,rgba(139,92,246,.10),transparent 28rem),var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}a{color:inherit}.ds-shell{display:flex;min-height:100vh}.ds-main{flex:1;min-width:0;padding:28px}.wrap{max-width:1450px;margin:0 auto}.top-row{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:22px}.page-kicker{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px}.page-kicker:before{content:"";width:8px;height:8px;background:var(--accent3);border-radius:50%;box-shadow:0 0 18px rgba(16,185,129,.8)}.page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;letter-spacing:-.06em;line-height:1.05}.page-subtitle{color:var(--muted);max-width:780px;line-height:1.65;margin-top:10px;font-size:.95rem}.card{border:1px solid var(--border);background:rgba(17,28,45,.92);border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.22);backdrop-filter:blur(12px);overflow:hidden}.card-pad{padding:18px}.grid{display:grid;gap:14px}.stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:16px}.stat{border:1px solid var(--border);background:rgba(255,255,255,.035);border-radius:18px;padding:16px}.stat .num{font-size:1.6rem;font-weight:900}.stat .label{color:var(--muted);font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}.toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 190px 190px auto;gap:12px;align-items:end;padding:18px;background:linear-gradient(135deg,rgba(255,255,255,.045),rgba(255,255,255,.015)),var(--surface);border-bottom:1px solid var(--border)}.field label{display:block;color:var(--dim);font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px}.input,.select,textarea{width:100%;min-height:42px;border:1px solid var(--border);border-radius:12px;background:var(--surface3);color:var(--text);padding:10px 12px;font:inherit;font-size:.88rem;outline:none}.input:focus,.select:focus,textarea:focus{border-color:rgba(59,130,246,.65);box-shadow:0 0 0 4px rgba(59,130,246,.10)}textarea{min-height:110px;resize:vertical}.btn{min-height:42px;border-radius:12px;border:1px solid transparent;padding:0 16px;font:inherit;font-size:.82rem;font-weight:900;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.15s;white-space:nowrap}.btn:hover{transform:translateY(-1px)}.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 12px 26px rgba(59,130,246,.24)}.btn.primary:hover{background:var(--accent-hover)}.btn.secondary{background:rgba(255,255,255,.035);color:var(--text);border-color:var(--border)}.btn.danger{background:rgba(239,68,68,.10);color:#fecaca;border-color:rgba(239,68,68,.35)}.btn.good{background:rgba(16,185,129,.12);color:#a7f3d0;border-color:rgba(16,185,129,.35)}.btn.disabled{opacity:.52;cursor:not-allowed;pointer-events:none}.btn.disabled:hover{transform:none}.table{width:100%;border-collapse:collapse}.table th,.table td{text-align:left;padding:14px 16px;border-bottom:1px solid var(--border);vertical-align:top}.table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:var(--dim);background:rgba(255,255,255,.025)}.table td{font-size:.88rem;color:var(--muted)}.table strong{color:var(--text)}.muted{color:var(--muted)}.dim{color:var(--dim)}.badge-pill{display:inline-flex;align-items:center;gap:7px;border:1px solid var(--border);background:rgba(255,255,255,.04);border-radius:999px;padding:5px 9px;font-size:.72rem;font-weight:900;color:var(--muted);white-space:nowrap}.badge-pill.good{color:#a7f3d0;border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.09)}.badge-pill.warn{color:#fde68a;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.09)}.badge-pill.danger{color:#fecaca;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.09)}.actions{display:flex;gap:8px;flex-wrap:wrap}.alert{border-radius:16px;padding:14px 16px;font-weight:800;line-height:1.5;margin-bottom:16px;border:1px solid transparent}.alert.success{background:rgba(16,185,129,.10);border-color:rgba(16,185,129,.30);color:#a7f3d0}.alert.danger{background:rgba(239,68,68,.10);border-color:rgba(239,68,68,.30);color:#fecaca}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}.question-card{border:1px solid var(--border);background:rgba(255,255,255,.025);border-radius:18px;padding:16px;margin-bottom:12px}.option-row{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--border);background:rgba(15,25,40,.75);border-radius:12px;padding:11px;margin-top:8px}.score-big{font-size:3rem;font-weight:900;letter-spacing:-.08em}.pagination{padding:16px}.empty{text-align:center;padding:46px 22px;color:var(--muted)}@media(max-width:900px){.ds-main{padding:18px}.top-row{flex-direction:column}.stats,.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}.toolbar{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
  </style>

</head>
<body>
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <div class="page-kicker">Instructor Assignment Center</div>
            <h1 class="page-title">Assignments</h1>
            <p class="page-subtitle">Assign seeded MCQ and fill-in-the-blank versions of each DataSensei topic to your classes.</p>
          </div>
          <a href="{{ route('instructor.assignments.create') }}" class="btn primary">+ Create Assignment</a>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert danger">{{ session('error') }}</div> @endif

        <div class="grid stats">
          <div class="stat"><div class="num">{{ $stats['total'] }}</div><div class="label">Total</div></div>
          <div class="stat"><div class="num">{{ $stats['published'] }}</div><div class="label">Published</div></div>
          <div class="stat"><div class="num">{{ $stats['draft'] }}</div><div class="label">Drafts</div></div>
          <div class="stat"><div class="num">{{ $stats['closed'] }}</div><div class="label">Closed</div></div>
        </div>

        <section class="card">
          <form class="toolbar" method="GET" action="{{ route('instructor.assignments.index') }}">
            <div class="field"><label>Search</label><input class="input" name="search" value="{{ request('search') }}" placeholder="Search assignment, topic, version..."></div>
            <div class="field"><label>Class</label><select class="select" name="class_id"><option value="">All classes</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }} {{ $class->section ? '— '.$class->section : '' }}</option>@endforeach</select></div>
            <div class="field"><label>Status</label><select class="select" name="status"><option value="">All status</option>@foreach(['draft','published','closed','archived'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div>
            <button class="btn secondary" type="submit">Filter</button>
          </form>

          @if($assignments->count())
            <table class="table">
              <thead><tr><th>Assignment</th><th>Class</th><th>Type</th><th>Due</th><th>Status</th><th>Submissions</th><th>Actions</th></tr></thead>
              <tbody>
                @foreach($assignments as $assignment)
                  @php
                    $submitted = $assignment->submissions->whereIn('status', ['submitted','late','graded'])->pluck('student_id')->unique()->count();
                    $totalStudents = $assignment->classRoom?->students()->count() ?? 0;
                  @endphp
                  <tr>
                    <td><strong>{{ $assignment->title }}</strong><br><span class="muted">{{ $assignment->libraryItem?->topic_title }} · {{ $assignment->libraryItem?->version_name }}</span></td>
                    <td>{{ $assignment->classRoom?->name }}<br><span class="dim">{{ $assignment->classRoom?->section }}</span></td>
                    <td><span class="badge-pill">{{ $assignment->libraryItem?->type_label }}</span></td>
                    <td>{{ $assignment->due_at ? $assignment->due_at->format('M d, Y h:i A') : 'No due date' }}</td>
                    <td><span class="badge-pill {{ $assignment->status === 'published' ? 'good' : ($assignment->status === 'draft' ? 'warn' : '') }}">{{ ucfirst($assignment->status) }}</span></td>
                    <td>{{ $submitted }}/{{ $totalStudents }}</td>
                    <td>
                      <div class="actions">
                        <a class="btn secondary" href="{{ route('instructor.assignments.show', $assignment) }}">View</a>
                        <a class="btn secondary" href="{{ route('instructor.assignments.edit', $assignment) }}">Edit</a>
                        @if($assignment->status !== 'published')<form method="POST" action="{{ route('instructor.assignments.publish', $assignment) }}">@csrf @method('PATCH')<button class="btn good" type="submit">Publish</button></form>@endif
                        @if(($assignment->submissions_count ?? $assignment->submissions->count()) > 0)
                          <button class="btn danger disabled" type="button" title="Cannot delete because this assignment already has student submission records.">Delete Locked</button>
                        @else
                          <form method="POST" action="{{ route('instructor.assignments.destroy', $assignment) }}" onsubmit="return confirm('Delete this assigned assignment? This is only allowed when there are no student submissions yet.');">@csrf @method('DELETE')<button class="btn danger" type="submit">Delete</button></form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
            <div class="pagination">{{ $assignments->links() }}</div>
          @else
            <div class="empty">No assignments yet. Create one from your seeded assignment library.</div>
          @endif
        </section>
      </div>

    </main>
  </div>
</body>
</html>
