<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Gradebook — DataSensei</title>
  @include('student.assessments._list_styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'My Gradebook', 'pageDescription' => 'Your own grades, results and instructor feedback in each class.'])
  <style>
    /* My Gradebook (DataSensei Updates 12). Plain text states, no badges. */
    .gb-filter { display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
    .gb-filter label { display: block; margin-bottom: 6px; color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; }
    .gb-filter select { min-width: 260px; min-height: 34px; padding: 0 10px; border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); color-scheme: var(--ds-color-scheme, dark); }
    .gb-feedback { display: block; margin-top: 6px; padding: 8px 10px; border-left: 2px solid var(--ds-accent-border); background: var(--ds-accent-soft); color: var(--text); font-size: .8125rem; line-height: 1.5; white-space: pre-line; overflow-wrap: anywhere; }
    .gb-num { font-variant-numeric: tabular-nums; white-space: nowrap; }
    .gb-scroll { overflow-x: auto; }
    .gb-table td:first-child { min-width: 170px; word-break: normal; overflow-wrap: break-word; }
    .gb-table tfoot td { border-top: 1px solid var(--ds-border-strong, var(--border)); color: var(--text); vertical-align: top; }
    .gb-note { color: var(--muted); font-size: .8125rem; line-height: 1.5; white-space: normal; }
    .gb-link { color: var(--ds-accent-text, var(--accent)); text-decoration: none; }
    .gb-link:hover { text-decoration: underline; }
    .gb-details summary { color: var(--ds-accent-text, var(--accent)); cursor: pointer; font-size: .8125rem; white-space: nowrap; }
    .gb-completion { padding: 14px 18px 16px; border-top: 1px solid var(--border); font-size: .875rem; color: var(--ds-text-secondary); }
    .gb-completion h3 { margin: 0 0 4px; color: var(--text); font-size: .875rem; font-weight: 600; }
    .gb-completion p { margin: 0 0 6px; line-height: 1.55; }
    .gb-missing { margin: 6px 0 0; padding-left: 18px; color: var(--muted); font-size: .8125rem; line-height: 1.55; }
    .state-muted { color: var(--muted); }
    @media (max-width: 720px) {
      .gb-scroll { overflow-x: visible; }
      table.gb-table, table.gb-table.ds-table--wide { min-width: 0; }
      .gb-table tfoot { display: block; width: 100%; }
      .gb-table tfoot td { border-top: none; }
      .gb-table tfoot tr { border-top: 1px solid var(--ds-border-strong, var(--border)); }
      .gb-table tfoot td[data-label="Type"] { display: none; }
      .gb-table td:first-child { min-width: 0; }
    }
  </style>
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <span class="mobile-title">My Gradebook</span>
  </header>
  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">My Gradebook</h1>
            <p class="page-subtitle">Your own results for the assessments your instructors assigned in each class: score, percentage, status, due date, attempts and feedback, with your overall class grade and how much of the class's required work you have completed. Only you can see this page; practice and public challenges are not part of your class grade.</p>
          </div>
        </div>

        @if($classes->isEmpty())
          <section class="card">
            <div class="card-head"><h2>No classes yet</h2></div>
            <p class="card-empty">You are not enrolled in a class. When an instructor adds you to a class, your grades for its assessments appear here.</p>
          </section>
        @else
          <form method="GET" action="{{ route('student.gradebook.index') }}" class="gb-filter">
            <div>
              <label for="gb-class">Class</label>
              <select id="gb-class" name="class_id" onchange="this.form.submit()">
                <option value="">All my classes</option>
                @foreach($classes as $option)
                  <option value="{{ $option->id }}" @selected($selected && $selected->id === $option->id)>{{ $option->name }}{{ $option->section ? ', '.$option->section : '' }}{{ $option->is_archived ? ' (archived)' : '' }}</option>
                @endforeach
              </select>
            </div>
            <noscript><button class="btn" type="submit">Show</button></noscript>
          </form>

          @php
            $day = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('M j, Y') : '—';
            $pct = fn ($value) => $value === null ? '—' : rtrim(rtrim(number_format($value, 1), '0'), '.').'%';
          @endphp

          @foreach($books as $book)
            @php $class = $book['class']; $overall = $book['overall']; $completion = $book['completion']; @endphp
            <section class="card" aria-labelledby="gb-class-{{ $class->id }}">
              <div class="card-head">
                <h2 id="gb-class-{{ $class->id }}">{{ trim(($class->subject_code ? $class->subject_code.' ' : '').$class->name) }}{{ $class->section ? ', '.$class->section : '' }}</h2>
                <span>Instructor: {{ $class->instructor?->name ?? 'not set' }}{{ $class->term ? '. '.$class->term : '' }}</span>
              </div>

              @if($book['rows'] === [])
                <p class="card-empty">Your instructor has not published any assessments in this class yet.</p>
              @else
                <div class="gb-scroll">
                <table class="table gb-table">
                  <thead>
                    <tr>
                      <th scope="col">Assessment</th>
                      <th scope="col">Type</th>
                      <th scope="col">Score</th>
                      <th scope="col">Percentage</th>
                      <th scope="col">Status</th>
                      <th scope="col">Due date</th>
                      <th scope="col">Attempts</th>
                      <th scope="col">Submitted</th>
                      <th scope="col">Feedback</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($book['rows'] as $row)
                      @php $assessment = $row['assessment']; @endphp
                      <tr>
                        <td>
                          @if($row['submission_id'])
                            <a class="gb-link" href="{{ route('student.assessments.result', [$assessment->id, $row['submission_id']]) }}"><strong>{{ $assessment->title }}</strong></a>
                          @else
                            <strong>{{ $assessment->title }}</strong>
                          @endif
                        </td>
                        <td data-label="Type">{{ $assessment->purposeLabel() ?: 'Assessment' }}</td>
                        <td data-label="Score" class="gb-num">{{ $row['score_text'] }}</td>
                        <td data-label="Percentage" class="gb-num">
                          @if($row['percent'] !== null)
                            {{ $pct($row['percent']) }}
                            <span class="sub"><span class="{{ $row['passed'] ? 'state-good' : 'state-bad' }}">{{ $row['passed'] ? 'Passed' : 'Not passed' }}</span> (pass mark {{ $row['pass_mark'] }}%)</span>
                          @else
                            —
                          @endif
                        </td>
                        <td data-label="Status">
                          <span class="{{ $row['tone'] ? 'state-'.$row['tone'] : '' }}">{{ $row['state_label'] }}</span>
                          @if($row['not_credited'])<span class="sub">Kept blocked after integrity review; no credit.</span>@endif
                        </td>
                        <td data-label="Due date" class="gb-num">{{ $assessment->due_at ? $day($assessment->due_at) : 'No due date' }}</td>
                        <td data-label="Attempts" class="gb-num">{{ $row['attempts'] }}{{ $row['max_attempts'] ? ' of '.$row['max_attempts'] : '' }}@if($row['attempts'] > 1 && $row['state'] === 'graded')<span class="sub">Best graded counts</span>@endif</td>
                        <td data-label="Submitted" class="gb-num">{{ $day($row['submitted_at']) }}</td>
                        <td data-label="Feedback">
                          @if($row['feedback'] || $row['answer_feedback'] > 0)
                            <details class="gb-details">
                              <summary>View feedback</summary>
                              @if($row['feedback'])<span class="gb-feedback">{{ $row['feedback'] }}</span>@endif
                              @if($row['answer_feedback'] > 0)
                                <span class="sub">{{ $row['answer_feedback'] }} {{ $row['answer_feedback'] === 1 ? 'comment' : 'comments' }} on your answers. <a class="gb-link" href="{{ route('student.assessments.result', [$assessment->id, $row['submission_id']]) }}">Open the result</a></span>
                              @endif
                            </details>
                          @else
                            —
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                    <tr>
                      <td><strong>{!! \App\Support\Glossary::help('final_grade', 'Overall grade') !!}</strong></td>
                      <td data-label="Type">—</td>
                      <td data-label="Score" class="gb-num">{{ $overall['earned'] !== null ? $overall['earned'].' / '.$overall['possible'] : '—' }}</td>
                      <td data-label="Percentage" class="gb-num"><strong>{{ $pct($overall['percent']) }}</strong></td>
                      <td data-label="Status"><span class="{{ $overall['final'] ? 'state-good' : '' }}">{{ $overall['status'] }}</span></td>
                      <td colspan="4" class="gb-note">{{ $overall['graded'] }} of {{ $overall['total'] }} {{ $overall['total'] === 1 ? 'assessment' : 'assessments' }} graded. The overall grade is the average of your graded assessments, each counting equally; practice and public challenges are not part of it.</td>
                    </tr>
                  </tfoot>
                </table>
                </div>
              @endif

              @if($completion !== null)
                <div class="gb-completion">
                  <h3>{!! \App\Support\Glossary::help('class_completion', 'Class completion') !!}:
                    @if($completion['total'] === 0)
                      <span class="state-muted">nothing assigned yet</span>
                    @elseif($completion['complete'])
                      <span class="state-good">all required work completed</span>
                    @else
                      <span>{{ $completion['done'] }} of {{ $completion['total'] }} required items completed</span>
                    @endif
                  </h3>
                  @if($completion['total'] > 0)
                    <p>
                      Modules {{ $completion['groups']['modules']['done'] }} of {{ $completion['groups']['modules']['total'] }},
                      activities {{ $completion['groups']['activities']['done'] }} of {{ $completion['groups']['activities']['total'] }},
                      assessments graded {{ $completion['groups']['assessments']['done'] }} of {{ $completion['groups']['assessments']['total'] }}.
                      @if($book['certificate'])
                        Certificate of Completion issued on {{ $book['certificate']->issued_at?->format('M j, Y') }}: <a class="gb-link" href="{{ route('student.certificates.show', $book['certificate']->id) }}">view it</a>.
                      @elseif($completion['complete'])
                        Your instructor issues the Certificate of Completion at the end of the semester.
                      @endif
                    </p>
                    @if($completion['missing'] !== [])
                      <details class="gb-details">
                        <summary>Show what is left ({{ count($completion['missing']) }})</summary>
                        <ul class="gb-missing">
                          @foreach($completion['missing'] as $missing)
                            <li>{{ $missing['title'] }} ({{ $missing['kind'] }}): {{ $missing['detail'] }}</li>
                          @endforeach
                        </ul>
                      </details>
                    @endif
                  @endif
                </div>
              @endif
            </section>
          @endforeach
        @endif
      </div>
    </main>
  </div>

  @include('student.assessments._menu_script')
</body>
</html>
