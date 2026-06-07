<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DataSensei — Assignment Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{--bg:#0d1320;--surface:#111c2d;--surface2:#1a2638;--surface3:#0f1928;--border:#1e2f47;--border-hover:#2c4168;--accent:#3b82f6;--accent-hover:#2563eb;--accent2:#8b5cf6;--accent3:#10b981;--warn:#ef4444;--warn2:#f59e0b;--text:#fafafa;--muted:#7f93b0;--dim:#3d5272;--radius:14px;--radius-sm:10px;--sidebar-w:270px}*{box-sizing:border-box;margin:0;padding:0}html,body{min-height:100%;font-family:'Inter',system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:radial-gradient(circle at top left,rgba(59,130,246,.12),transparent 34rem),radial-gradient(circle at top right,rgba(139,92,246,.10),transparent 28rem),var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}a{color:inherit}.ds-shell{display:flex;min-height:100vh}.ds-main{flex:1;min-width:0;padding:28px}.wrap{max-width:1450px;margin:0 auto}.top-row{display:flex;justify-content:space-between;align-items:flex-start;gap:20px;margin-bottom:22px}.page-kicker{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-size:.72rem;font-weight:900;text-transform:uppercase;letter-spacing:.09em;margin-bottom:8px}.page-kicker:before{content:"";width:8px;height:8px;background:var(--accent3);border-radius:50%;box-shadow:0 0 18px rgba(16,185,129,.8)}.page-title{font-size:clamp(1.8rem,3vw,2.6rem);font-weight:900;letter-spacing:-.06em;line-height:1.05}.page-subtitle{color:var(--muted);max-width:780px;line-height:1.65;margin-top:10px;font-size:.95rem}.card{border:1px solid var(--border);background:rgba(17,28,45,.92);border-radius:22px;box-shadow:0 18px 55px rgba(0,0,0,.22);backdrop-filter:blur(12px);overflow:hidden}.card-pad{padding:18px}.grid{display:grid;gap:14px}.stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:16px}.stat{border:1px solid var(--border);background:rgba(255,255,255,.035);border-radius:18px;padding:16px}.stat .num{font-size:1.6rem;font-weight:900}.stat .label{color:var(--muted);font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-top:4px}.toolbar{display:grid;grid-template-columns:minmax(220px,1fr) 190px 190px auto;gap:12px;align-items:end;padding:18px;background:linear-gradient(135deg,rgba(255,255,255,.045),rgba(255,255,255,.015)),var(--surface);border-bottom:1px solid var(--border)}.field label{display:block;color:var(--dim);font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.08em;margin-bottom:7px}.input,.select,textarea{width:100%;min-height:42px;border:1px solid var(--border);border-radius:12px;background:var(--surface3);color:var(--text);padding:10px 12px;font:inherit;font-size:.88rem;outline:none}.input:focus,.select:focus,textarea:focus{border-color:rgba(59,130,246,.65);box-shadow:0 0 0 4px rgba(59,130,246,.10)}textarea{min-height:110px;resize:vertical}.btn{min-height:42px;border-radius:12px;border:1px solid transparent;padding:0 16px;font:inherit;font-size:.82rem;font-weight:900;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;transition:.15s;white-space:nowrap}.btn:hover{transform:translateY(-1px)}.btn.primary{background:var(--accent);color:#fff;border-color:var(--accent);box-shadow:0 12px 26px rgba(59,130,246,.24)}.btn.primary:hover{background:var(--accent-hover)}.btn.secondary{background:rgba(255,255,255,.035);color:var(--text);border-color:var(--border)}.btn.danger{background:rgba(239,68,68,.10);color:#fecaca;border-color:rgba(239,68,68,.35)}.btn.good{background:rgba(16,185,129,.12);color:#a7f3d0;border-color:rgba(16,185,129,.35)}.table{width:100%;border-collapse:collapse}.table th,.table td{text-align:left;padding:14px 16px;border-bottom:1px solid var(--border);vertical-align:top}.table th{font-size:.72rem;text-transform:uppercase;letter-spacing:.07em;color:var(--dim);background:rgba(255,255,255,.025)}.table td{font-size:.88rem;color:var(--muted)}.table strong{color:var(--text)}.muted{color:var(--muted)}.dim{color:var(--dim)}.badge-pill{display:inline-flex;align-items:center;gap:7px;border:1px solid var(--border);background:rgba(255,255,255,.04);border-radius:999px;padding:5px 9px;font-size:.72rem;font-weight:900;color:var(--muted);white-space:nowrap}.badge-pill.good{color:#a7f3d0;border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.09)}.badge-pill.warn{color:#fde68a;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.09)}.badge-pill.danger{color:#fecaca;border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.09)}.actions{display:flex;gap:8px;flex-wrap:wrap}.alert{border-radius:16px;padding:14px 16px;font-weight:800;line-height:1.5;margin-bottom:16px;border:1px solid transparent}.alert.success{background:rgba(16,185,129,.10);border-color:rgba(16,185,129,.30);color:#a7f3d0}.alert.danger{background:rgba(239,68,68,.10);border-color:rgba(239,68,68,.30);color:#fecaca}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.span-2{grid-column:span 2}.question-card{border:1px solid var(--border);background:rgba(255,255,255,.025);border-radius:18px;padding:16px;margin-bottom:12px}.option-row{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--border);background:rgba(15,25,40,.75);border-radius:12px;padding:11px;margin-top:8px}.score-big{font-size:3rem;font-weight:900;letter-spacing:-.08em}.pagination{padding:16px}.empty{text-align:center;padding:46px 22px;color:var(--muted)}@media(max-width:900px){.ds-main{padding:18px}.top-row{flex-direction:column}.stats,.form-grid{grid-template-columns:1fr}.span-2{grid-column:span 1}.toolbar{grid-template-columns:1fr}.table{display:block;overflow-x:auto}}
  </style>

</head>
<body>
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <div class="page-kicker">Assignment Details</div>
            <h1 class="page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">{{ $assignment->libraryItem->topic_title }} · {{ $assignment->libraryItem->version_name }} · {{ $assignment->libraryItem->type_label }}</p>
          </div>
          <div class="actions">
            <a href="{{ route('instructor.assignments.edit', $assignment) }}" class="btn secondary">Edit</a>
            @if($assignment->status !== 'published')<form method="POST" action="{{ route('instructor.assignments.publish', $assignment) }}">@csrf @method('PATCH')<button class="btn good" type="submit">Publish</button></form>@endif
            @if($assignment->status === 'published')<form method="POST" action="{{ route('instructor.assignments.close', $assignment) }}">@csrf @method('PATCH')<button class="btn secondary" type="submit">Close</button></form>@endif
            <a href="{{ route('instructor.assignments.index') }}" class="btn secondary">Back</a>
          </div>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif

        <div class="grid stats">
          <div class="stat"><div class="num">{{ $assignment->libraryItem->questions->count() }}</div><div class="label">Questions</div></div>
          <div class="stat"><div class="num">{{ $assignment->libraryItem->total_points }}</div><div class="label">Points</div></div>
          <div class="stat"><div class="num">{{ $submittedCount }}/{{ $studentCount }}</div><div class="label">Submitted</div></div>
          <div class="stat"><div class="num">{{ ucfirst($assignment->status) }}</div><div class="label">Status</div></div>
        </div>

        <div class="grid" style="grid-template-columns:1.15fr .85fr">
          <section class="card card-pad">
            <h2 style="margin-bottom:12px">Question Preview</h2>
            @foreach($assignment->libraryItem->questions as $question)
              <div class="question-card">
                <div class="badge-pill">{{ $question->type_label }} · {{ $question->points }} pt</div>
                <p style="margin-top:10px;line-height:1.6"><strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong></p>
                @if($question->question_type === 'mcq')
                  @foreach($question->options as $option)
                    <div class="option-row">{{ $option->is_correct ? '✅' : '○' }} <span>{{ $option->option_text }}</span></div>
                  @endforeach
                @else
                  <p class="muted" style="margin-top:8px">Accepted: {{ $question->blankAnswers->pluck('answer_text')->join(', ') }}</p>
                @endif
              </div>
            @endforeach
          </section>

          <section class="card card-pad">
            <h2 style="margin-bottom:12px">Submissions</h2>
            <p class="muted" style="margin-bottom:14px">Class: <strong>{{ $assignment->classRoom->name }}</strong><br>Due: {{ $assignment->due_at ? $assignment->due_at->format('M d, Y h:i A') : 'No due date' }}</p>
            @if($assignment->submissions->count())
              <table class="table">
                <thead><tr><th>Student</th><th>Attempt</th><th>Score</th><th>Status</th></tr></thead>
                <tbody>
                  @foreach($assignment->submissions->sortByDesc('submitted_at') as $submission)
                    <tr>
                      <td><strong>{{ $submission->student?->name }}</strong><br><span class="dim">{{ $submission->student?->email }}</span></td>
                      <td>{{ $submission->attempt_no }}</td>
                      <td>{{ $submission->score }}/{{ $submission->total_points }}</td>
                      <td><span class="badge-pill {{ $submission->status === 'late' ? 'danger' : 'good' }}">{{ str_replace('_',' ',ucfirst($submission->status)) }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            @else
              <div class="empty">No submissions yet.</div>
            @endif
          </section>
        </div>
      </div>

    </main>
  </div>
</body>
</html>
