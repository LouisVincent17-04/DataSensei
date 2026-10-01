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

    .grid{display:grid;gap:16px}
    .card h2{font-size:1rem;font-weight:600;line-height:1.35}
    .muted{color:var(--muted)}
    .dim{color:var(--dim)}

    /* details: plain label and value pairs */
    .facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px 24px;margin:14px 0 0}
    .facts dt{color:var(--muted);font-size:.8125rem;font-weight:500}
    .facts dd{margin:2px 0 0;color:var(--text);font-size:.9375rem;font-weight:500;overflow-wrap:anywhere}
    .instructions{margin-top:10px;color:var(--ds-text-secondary);font-size:.875rem;line-height:1.65;white-space:pre-wrap;overflow-wrap:anywhere}
    .section-note{margin-top:4px;color:var(--muted);font-size:.8125rem;line-height:1.5}
    .state-good{color:var(--ds-success-text)}
    .state-bad{color:var(--ds-danger-text)}

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
      .facts{grid-template-columns:repeat(2,minmax(0,1fr))}
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
    @media(max-width:420px){.facts{grid-template-columns:minmax(0,1fr)}}
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
        @php
          $item = $assignment->libraryItem;
          $fmt = fn ($date) => $date ? $date->format('M d, Y, g:i A') : null;
          $instructions = trim((string) ($assignment->instructions ?: $item?->instructions));
        @endphp
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $assignment->title }}</h1>
            <p class="page-subtitle">{{ $assignment->classRoom->name }}{{ $assignment->classRoom->section ? ', '.$assignment->classRoom->section : '' }}. {{ $item?->type_label === 'MCQ' ? 'Multiple choice' : ($item?->type_label ?? 'Assignment') }}. {{ ucfirst($assignment->status) }}.</p>
          </div>
          <div class="actions">
            @if($assignment->status !== 'archived')
              <a href="{{ route('instructor.assignments.edit', $assignment) }}" class="btn secondary">Edit</a>
            @endif
            @if($assignment->status === 'draft')<form method="POST" action="{{ route('instructor.assignments.publish', $assignment) }}">@csrf @method('PATCH')<button class="btn good" type="submit">Publish</button></form>@endif
            @if($assignment->status === 'published')<form method="POST" action="{{ route('instructor.assignments.close', $assignment) }}">@csrf @method('PATCH')<button class="btn secondary" type="submit">Close</button></form>@endif
            <a href="{{ route('instructor.assignments.index') }}" class="btn secondary">Back</a>
          </div>
        </div>

        @if(session('success')) <div class="alert success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert danger">{{ session('error') }}</div> @endif

        <div class="grid">
          <section class="card card-pad" aria-labelledby="details-title">
            <h2 id="details-title">Assignment details</h2>
            <dl class="facts">
              <div><dt>Class</dt><dd>{{ $assignment->classRoom->name }}</dd></div>
              <div><dt>Status</dt><dd>{{ ucfirst($assignment->status) }}</dd></div>
              <div><dt>Available from</dt><dd>{{ $fmt($assignment->available_at) ?? 'When published' }}</dd></div>
              <div><dt>Due date</dt><dd>{{ $fmt($assignment->due_at) ?? 'No due date' }}</dd></div>
              <div><dt>Questions</dt><dd>{{ $item?->questions->count() ?? 0 }}</dd></div>
              <div><dt>Total points</dt><dd>{{ (int) ($item?->questions->sum('points') ?: $item?->total_points) }}</dd></div>
              <div><dt>Time limit</dt><dd>{{ $item?->time_limit_minutes ? $item->time_limit_minutes.' minutes' : 'None' }}</dd></div>
              <div><dt>Attempts allowed</dt><dd>{{ $assignment->max_attempts ?: 1 }}</dd></div>
              <div><dt>Submitted</dt><dd>{{ $submittedCount }} of {{ $studentCount }} students</dd></div>
              <div><dt>Published</dt><dd>{{ $fmt($assignment->assigned_at) ?? 'Not yet' }}</dd></div>
            </dl>
          </section>

          <section class="card card-pad" aria-labelledby="instructions-title">
            <h2 id="instructions-title">Instructions</h2>
            <p class="instructions">{{ $instructions !== '' ? $instructions : 'No instructions were added.' }}</p>
          </section>

          <section class="card card-pad" aria-labelledby="content-title">
            <h2 id="content-title">Questions and answers</h2>
            <p class="section-note">What students answer, with the correct and expected answers. Students do not see the answers.</p>
            @if($item)
              @include('instructor.assignments._content', ['item' => $item])
            @else
              <p class="muted" style="margin-top:12px">This assignment has no question source.</p>
            @endif
          </section>

          <section class="card card-pad" aria-labelledby="submissions-title">
            <h2 id="submissions-title" style="margin-bottom:12px">Submissions</h2>
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
                          <span class="{{ $submission->status === 'late' ? 'state-bad' : ($submission->status === 'in_progress' ? '' : 'state-good') }}">{{ str_replace('_',' ',ucfirst($submission->status)) }}</span>
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
