<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Assignments — DataSensei</title>
<style>
    /* Instructor assignments list. Colours, type and radius come from partials.design-system. */
    *{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;background:var(--bg);color:var(--text);font-family:var(--ds-font-sans)}
    a{color:inherit}
    .ds-shell{display:flex;min-height:100vh}
    .ds-main{flex:1;min-width:0;padding:28px 32px 48px}
    .wrap{max-width:1450px;margin:0 auto}

    /* page header */
    .top-row{display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:24px}
    .top-row > div:first-child{min-width:0;flex:1 1 320px}
    .page-subtitle{max-width:72ch;margin-top:4px;color:var(--muted);font-size:.875rem;line-height:1.55}

    /* buttons */
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:38px;padding:0 16px;
      border:1px solid var(--ds-border-strong);border-radius:var(--radius-sm);background:var(--surface2);color:var(--text);
      font:500 .875rem/1.2 var(--ds-font-sans);text-decoration:none;white-space:nowrap;cursor:pointer;
      transition:background .12s ease,border-color .12s ease,color .12s ease}
    .btn:hover{background:var(--ds-surface-hover)}
    .btn.primary{border-color:var(--accent);background:var(--accent);color:#fff}
    .btn.primary:hover{border-color:var(--accent-hover);background:var(--accent-hover)}
    .btn.good{border-color:var(--ds-success-border);background:transparent;color:var(--ds-success-text)}
    .btn.good:hover{background:var(--ds-success-soft)}
    .btn.danger{border-color:var(--ds-danger-border);background:transparent;color:var(--ds-danger-text)}
    .btn.danger:hover{background:var(--ds-danger-soft)}
    .btn.disabled{opacity:.5;cursor:not-allowed;pointer-events:none}
    .actions{display:flex;flex-wrap:wrap;gap:8px}

    /* flash messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.5}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .alert.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    /* summary figures: label above value */
    .grid{display:grid;gap:12px}
    .stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:24px}
    .stat{display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:16px 18px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .stat .num{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums}
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500}

    /* list card */
    .card{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface);overflow:hidden}
    .toolbar{display:grid;grid-template-columns:minmax(0,1fr) 220px 180px auto;gap:12px;align-items:end;
      padding:16px 20px;border-bottom:1px solid var(--border)}
    .field label{display:block;margin-bottom:6px;color:var(--ds-text-secondary);font-size:.8125rem;font-weight:500}
    .input,.select{width:100%;min-height:38px;padding:8px 12px;border:1px solid var(--ds-input-border);border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--text);font:400 .875rem/1.4 var(--ds-font-sans);outline:none;
      transition:border-color .12s ease,box-shadow .12s ease}
    .input::placeholder{color:var(--dim)}
    .input:focus,.select:focus{border-color:var(--accent);box-shadow:var(--ds-focus-ring)}

    .table{width:100%;border-collapse:collapse}
    .card .table{min-width:820px}
    .table th{padding:10px 14px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .table td{padding:12px 14px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
      line-height:1.45;vertical-align:middle}
    .table tbody tr:last-child td{border-bottom:0}
    .table tbody tr:hover td{background:rgba(255,255,255,.02)}
    .table strong{color:var(--text);font-weight:600}
    .table td .muted,.table td .dim{font-size:.8125rem}
    .table .btn{min-height:32px;padding:0 12px;font-size:.8125rem}
    .muted{color:var(--muted)}
    .dim{color:var(--dim)}

    .badge-pill{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .badge-pill.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .badge-pill.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .badge-pill.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}

    .pagination{padding:14px 20px;border-top:1px solid var(--border)}
    .pagination:not(:has(*)){display:none}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1100px){
      .stats{grid-template-columns:repeat(2,minmax(0,1fr))}
      .toolbar{grid-template-columns:minmax(0,1fr) minmax(0,1fr) auto}
      .toolbar > .field:first-child{grid-column:1/-1}
    }
    @media(max-width:900px){
      .ds-main{padding:24px 20px 40px}
    }
    @media(max-width:760px){
      .toolbar{grid-template-columns:minmax(0,1fr)}
      .toolbar .btn{width:100%}
    }
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .toolbar,.pagination{padding-left:16px;padding-right:16px}
      .top-row{align-items:stretch;flex-direction:column}
      .top-row > div:first-child{flex:0 0 auto}
      .top-row > .btn{width:100%}
    }
    @media(max-width:420px){.stats{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn,.input,.select{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Instructor Assignments', 'pageDescription' => 'Create assignments, review submissions, and return feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">Assignments</h1>
            <p class="page-subtitle">Create class assignments, publish them to students, and review submission activity.</p>
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
                    $submitted = $assignment->submitted_students_count ?? 0;
                    $totalStudents = $assignment->classRoom?->students_count ?? 0;
                  @endphp
                  <tr>
                    <td><strong>{{ $assignment->title }}</strong><br><span class="muted">{{ $assignment->libraryItem?->topic_title }}, {{ $assignment->libraryItem?->version_name }}</span></td>
                    <td>{{ $assignment->classRoom?->name }}<br><span class="dim">{{ $assignment->classRoom?->section }}</span></td>
                    <td><span class="badge-pill">{{ $assignment->libraryItem?->type_label }}</span></td>
                    <td>{{ $assignment->due_at ? $assignment->due_at->format('M d, Y h:i A') : 'No due date' }}</td>
                    <td><span class="badge-pill {{ $assignment->status === 'published' ? 'good' : ($assignment->status === 'draft' ? 'warn' : '') }}">{{ ucfirst($assignment->status) }}</span></td>
                    <td>{{ $submitted }}/{{ $totalStudents }}</td>
                    <td>
                      <div class="actions">
                        <a class="btn secondary" href="{{ route('instructor.assignments.show', $assignment) }}">View</a>
                        <a class="btn secondary" href="{{ route('instructor.assignments.edit', $assignment) }}">Edit</a>
                        @if($assignment->status === 'draft')<form method="POST" action="{{ route('instructor.assignments.publish', $assignment) }}">@csrf @method('PATCH')<button class="btn good" type="submit">Publish</button></form>@endif
                        @if(($assignment->submissions_count ?? 0) > 0)
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
