<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assignment Details — DataSensei</title>
<style>
    /* Assignment detail: question preview and submissions. Colours, type and radius come from partials.design-system. */
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
    .actions{display:flex;flex-wrap:wrap;gap:8px}

    /* messages */
    .alert{margin-bottom:16px;padding:12px 16px;border:1px solid var(--ds-accent-border);border-radius:var(--radius-sm);
      background:var(--ds-accent-soft);color:#dbeafe;font-size:.875rem;line-height:1.5}
    .alert strong{font-weight:600}
    .alert.success{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:#d1fae5}
    .alert.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:#fee2e2}

    .card{border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .card-pad{padding:20px}

    /* summary figures: label above value */
    .grid{display:grid;gap:20px}
    .stats{grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;margin-bottom:24px}
    .stat{display:flex;flex-direction:column-reverse;justify-content:flex-end;gap:4px;padding:16px 18px;
      border:1px solid var(--border);border-radius:var(--radius);background:var(--surface)}
    .stat .num{font-size:1.5rem;font-weight:700;line-height:1.2;letter-spacing:-.02em;font-variant-numeric:tabular-nums;overflow-wrap:anywhere}
    .stat .label{color:var(--muted);font-size:.8125rem;font-weight:500}

    .detail-grid{grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);align-items:start}
    .card h2{font-size:1rem;font-weight:600;line-height:1.35}
    .card > p.muted{font-size:.875rem;line-height:1.6}
    .card > p.muted strong{color:var(--text);font-weight:600}
    .muted{color:var(--muted)}
    .dim{color:var(--dim)}

    /* question preview: rows separated by rules */
    .question-card{padding:16px 0;border-top:1px solid var(--border);font-size:.875rem;line-height:1.55}
    .question-card:last-child{padding-bottom:0}
    .question-card > p{color:var(--ds-text-secondary)}
    .question-card strong{color:var(--text);font-weight:600}
    .option-row{display:flex;align-items:flex-start;gap:8px;margin-top:8px;padding:8px 12px;border-radius:var(--radius-sm);
      background:var(--surface3);color:var(--ds-text-secondary);font-size:.875rem;line-height:1.5}
    .option-row span{min-width:0;overflow-wrap:anywhere}

    .badge-pill{display:inline-flex;align-items:center;padding:2px 8px;border:1px solid var(--ds-border-strong);border-radius:var(--radius-xs);
      background:var(--surface2);color:var(--ds-text-secondary);font-size:.75rem;font-weight:600;line-height:1.4;white-space:nowrap}
    .badge-pill.good{border-color:var(--ds-success-border);background:var(--ds-success-soft);color:var(--ds-success-text)}
    .badge-pill.warn{border-color:var(--ds-warning-border);background:var(--ds-warning-soft);color:var(--ds-warning-text)}
    .badge-pill.danger{border-color:var(--ds-danger-border);background:var(--ds-danger-soft);color:var(--ds-danger-text)}

    /* submissions table */
    .table{width:100%;min-width:380px;border-collapse:collapse}
    .table th{padding:10px 12px;border-bottom:1px solid var(--border);background:var(--surface3);color:var(--muted);
      font-size:.75rem;font-weight:600;text-align:left;white-space:nowrap}
    .table td{padding:12px 12px;border-bottom:1px solid var(--border);color:var(--ds-text-secondary);font-size:.875rem;
      line-height:1.45;vertical-align:middle}
    .table tbody tr:last-child td{border-bottom:0}
    .table tbody tr:hover td{background:rgba(255,255,255,.02)}
    .table strong{color:var(--text);font-weight:600}
    .table td .dim{font-size:.8125rem;overflow-wrap:anywhere}
    .card .ds-table-scroll{border:1px solid var(--border);border-radius:var(--radius-sm)}
    .empty{padding:32px 20px;color:var(--muted);font-size:.875rem;line-height:1.5;text-align:center}

    @media(max-width:1100px){
      .stats{grid-template-columns:repeat(2,minmax(0,1fr))}
      .detail-grid{grid-template-columns:minmax(0,1fr)}
    }
    @media(max-width:900px){
      .ds-main{padding:24px 20px 40px}
    }
    @media(max-width:640px){
      .ds-main{padding:20px 16px 32px}
      .card-pad{padding:16px}
      .top-row{align-items:stretch;flex-direction:column}
      .top-row > div:first-child{flex:0 0 auto}
      .top-row .actions > *{flex:1 1 auto}
      .top-row .actions form .btn{width:100%}
    }
    @media(max-width:420px){.stats{grid-template-columns:minmax(0,1fr)}}
    @media(prefers-reduced-motion:reduce){.btn{transition:none}}
  </style>
  @include('partials.admin-inspired-page-style')
    @include('partials.page-head', ['pageTitle' => 'Assignment Details', 'pageDescription' => 'Create assignments, review submissions, and return feedback.'])
</head>
<body class="ds-admin-inspired">
  <div class="ds-shell">
    @include('partials.instructor-sidebar')
    <main class="ds-main">

      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">{{ $assignment->libraryItem->topic_title }}, {{ $assignment->libraryItem->version_name }}, {{ $assignment->libraryItem->type_label }}</p>
          </div>
          <div class="actions">
            <a href="{{ route('instructor.assignments.edit', $assignment) }}" class="btn secondary">Edit</a>
            @if($assignment->status === 'draft')<form method="POST" action="{{ route('instructor.assignments.publish', $assignment) }}">@csrf @method('PATCH')<button class="btn good" type="submit">Publish</button></form>@endif
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

        <div class="grid detail-grid">
          <section class="card card-pad">
            <h2 style="margin-bottom:12px">Question Preview</h2>
            @foreach($assignment->libraryItem->questions as $question)
              <div class="question-card">
                <div class="badge-pill">{{ $question->type_label }}, {{ $question->points }} pt</div>
                <p style="margin-top:8px;line-height:1.6"><strong>{{ $loop->iteration }}. {{ $question->question_text }}</strong></p>
                @if($question->question_type === 'mcq')
                  @foreach($question->options as $option)
                    <div class="option-row">{{ $option->is_correct ? '✓' : '○' }} <span>{{ $option->option_text }}</span></div>
                  @endforeach
                @else
                  <p class="muted" style="margin-top:8px">Accepted: {{ $question->blankAnswers->pluck('answer_text')->join(', ') }}</p>
                @endif
              </div>
            @endforeach
          </section>

          <section class="card card-pad">
            <h2 style="margin-bottom:12px">Submissions</h2>
            <p class="muted" style="margin-bottom:16px">Class: <strong>{{ $assignment->classRoom->name }}</strong><br>Due: {{ $assignment->due_at ? $assignment->due_at->format('M d, Y h:i A') : 'No due date' }}</p>
            @if($assignment->submissions->count())
              <table class="table">
                <thead><tr><th>Student</th><th>Attempt</th><th>Score</th><th>Status</th></tr></thead>
                <tbody>
                  @foreach($assignment->submissions->sortByDesc('submitted_at') as $submission)
                    <tr>
                      <td><strong>{{ $submission->student?->name }}</strong><br><span class="dim">{{ $submission->student?->email }}</span></td>
                      <td>{{ $submission->attempt_no }}</td>
                      <td>
                        {{ $submission->score }}/{{ $submission->total_points }}
                        @if($submission->isHeldForIntegrityReview())
                          <br><span class="dim">Uncredited: {{ (int) $submission->provisional_score }}/{{ $submission->total_points }}</span>
                        @endif
                      </td>
                      <td>
                        @if($submission->isHeldForIntegrityReview())
                          <strong>Held for integrity review</strong>
                          <br><span class="dim">{{ $submission->integrity_reason ?: 'The anti-cheat policy withheld credit for this attempt.' }}</span>
                          <div class="actions" style="margin-top:8px">
                            <form method="POST" action="{{ route('instructor.assignments.submissions.release', [$assignment, $submission]) }}">@csrf @method('PATCH')<button class="btn good" type="submit" onclick="return confirm('Credit the saved score of this attempt?')">Release score</button></form>
                            <form method="POST" action="{{ route('instructor.assignments.submissions.keep-blocked', [$assignment, $submission]) }}">@csrf @method('PATCH')<button class="btn secondary" type="submit" onclick="return confirm('Keep this attempt blocked with no credit?')">Keep blocked</button></form>
                          </div>
                        @else
                          <span class="badge-pill {{ $submission->status === 'late' ? 'danger' : 'good' }}">{{ str_replace('_',' ',ucfirst($submission->status)) }}</span>
                          @if($submission->integrity_status === 'blocked' && $submission->integrity_reviewed_at)
                            <br><span class="dim">Kept blocked after integrity review, no credit.</span>
                          @elseif($submission->integrity_reviewed_at)
                            <br><span class="dim">Released after integrity review.</span>
                          @endif
                        @endif
                      </td>
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
