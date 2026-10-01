@extends('instructor.layout')

@use('App\Models\Assessment')
@use('App\Support\Reports\ReportFormat', 'F')

@section('title', 'Gradebook')
@section('page_title', 'Gradebook')
@section('page_subtitle', 'What are the grades of all students in my class? Every published assessment of the class, each student\'s final grade and their completion of the required class work. Only your own classes are shown; practice and public challenges are not part of the grade.')

@section('content')
  @include('reports.partials.styles')

  @php
    $pct = fn ($value) => $value === null ? F::NONE : F::pct($value);
    $when = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('M d, Y, g:i A') : 'No due date';
    $day = fn ($date) => $date ? \Illuminate\Support\Carbon::parse($date)->format('M d, Y') : 'no due date';
  @endphp

  @push('head')
  <style>
    /* Gradebook grid (DataSensei Updates 12): headers wrap so every column fits. */
    .gb-grid th { white-space: normal; min-width: 120px; vertical-align: bottom; }
    .gb-grid th:first-child { min-width: 180px; }
    .gb-grid td { white-space: nowrap; }
    .gb-grid td:first-child { white-space: normal; }
    .gb-grid tfoot td { border-top: 1px solid var(--ds-border-strong); color: var(--text); font-weight: 600; }
    .gb-detail td:nth-child(2), .gb-detail td:nth-child(3), .gb-detail td:nth-child(4), .gb-detail td:nth-child(6) { white-space: nowrap; }
    .gb-facts { display: grid; grid-template-columns: minmax(150px, 220px) minmax(0, 1fr); gap: 8px 16px; margin: 0; font-size: .875rem; }
    .gb-facts dt { color: var(--muted); }
    .gb-facts dd { margin: 0; color: var(--text); }
    .gb-missing { margin: 6px 0 0; padding-left: 18px; color: var(--muted); font-size: .8125rem; line-height: 1.55; }
  </style>
  @endpush

  <div class="rp">
    <form method="GET" action="{{ route('instructor.gradebook.index') }}" class="rp-filters">
      <div class="rp-field rp-field-wide">
        <label for="gb-class">Class</label>
        <select id="gb-class" class="rp-input" name="class_id" onchange="this.form.submit()">
          <option value="">Choose a class</option>
          @foreach($classes as $option)
            <option value="{{ $option->id }}" @selected($class && $class->id === $option->id)>{{ $option->name }}{{ $option->section ? ', '.$option->section : '' }}{{ $option->is_archived ? ' (archived)' : '' }}</option>
          @endforeach
        </select>
      </div>
      @if($class && $book)
        <div class="rp-field rp-field-wide">
          <label for="gb-assessment">Assessment details</label>
          <select id="gb-assessment" class="rp-input" name="assessment_id" onchange="this.form.submit()">
            <option value="">All assessments (grid)</option>
            @foreach($book['assessments'] as $option)
              <option value="{{ $option->id }}" @selected($assessment && $assessment->id === $option->id)>{{ $option->title }}</option>
            @endforeach
          </select>
        </div>
      @endif
      <div class="rp-filter-actions"><button type="submit" class="rp-btn">Open</button></div>
    </form>

    @if($class !== null && ($record ?? null) !== null)
      @php
        $student = $record['student'];
        $overall = $record['overall'];
        $done = $record['completion'];
      @endphp
      <section class="rp-panel">
        <div class="rp-panel-head">
          <h3 class="rp-panel-title">{{ $student->name }}</h3>
          <span class="rp-count">{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}{{ $class->term ? ', '.$class->term : '' }}</span>
        </div>
        <p class="rp-note"><a class="rp-back" href="{{ route('instructor.gradebook.index', ['class_id' => $class->id]) }}">← Back to the {{ $class->name }} grid</a></p>
        <div class="rp-panel-body">
          <dl class="gb-facts">
            <dt>{!! \App\Support\Glossary::help('final_grade') !!}</dt>
            <dd><strong>{{ $pct($overall['percent']) }}</strong>{{ $overall['earned'] !== null ? ' ('.$overall['earned'].' / '.$overall['possible'].' points)' : '' }}, {{ $overall['graded'] }} of {{ $overall['total'] }} assessments graded, {{ strtolower($overall['status']) }}</dd>
            <dt>{!! \App\Support\Glossary::help('class_completion') !!}</dt>
            <dd>
              @if($done === null || $done['total'] === 0)
                Nothing assigned to the class yet.
              @else
                @if($done['complete'])<span class="rp-good">All required work completed</span>@else{{ $done['done'] }} of {{ $done['total'] }} required items completed @endif
                (modules {{ $done['groups']['modules']['done'] }} of {{ $done['groups']['modules']['total'] }}, activities {{ $done['groups']['activities']['done'] }} of {{ $done['groups']['activities']['total'] }}, assessments graded {{ $done['groups']['assessments']['done'] }} of {{ $done['groups']['assessments']['total'] }})
                @if($done['missing'] !== [])
                  <ul class="gb-missing">
                    @foreach($done['missing'] as $missing)
                      <li>{{ $missing['title'] }} ({{ $missing['kind'] }}): {{ $missing['detail'] }}</li>
                    @endforeach
                  </ul>
                @endif
              @endif
            </dd>
            <dt>Certificate of Completion</dt>
            <dd>
              @forelse($record['certificates'] as $held)
                {{ $held->certificate_number }}, issued {{ $held->issued_at?->format('M d, Y') }}@if($held->isRevoked()) <span class="rp-bad">(revoked)</span>@endif<br>
              @empty
                Not issued.
              @endforelse
            </dd>
          </dl>
        </div>
        @if($record['rows'] === [])
          <div class="rp-panel-body">This class has no published assessments yet.</div>
        @else
          <div class="rp-table-wrap">
            <table class="rp-table gb-detail">
              <thead>
                <tr>
                  <th scope="col">Assessment</th>
                  <th scope="col">Type</th>
                  <th scope="col">Due</th>
                  <th scope="col">Counted result</th>
                  <th scope="col">Status</th>
                  <th scope="col">Every attempt</th>
                  <th scope="col">Feedback</th>
                </tr>
              </thead>
              <tbody>
                @foreach($record['rows'] as $row)
                  @php $item = $row['assessment']; @endphp
                  <tr>
                    <td>{{ $item->title }}</td>
                    <td>{{ Assessment::PURPOSES[$item->purpose] ?? 'Assessment' }}</td>
                    <td>{{ $day($item->due_at) }}</td>
                    <td>{{ $row['score_text'] }}@if($row['percent'] !== null)<div class="rp-meta">{{ $pct($row['percent']) }}{{ $row['passed'] ? ', passed' : ', not passed' }}</div>@endif</td>
                    <td><span class="{{ $row['tone'] && $row['state'] !== 'graded' ? 'rp-'.$row['tone'] : '' }}">{{ $row['state_label'] }}</span></td>
                    <td>
                      @forelse($record['history'][$item->id] ?? [] as $attempt)
                        <div>
                          @if(in_array($attempt->status, ['submitted', 'late', 'graded'], true))<a class="rp-link" href="{{ route('instructor.assessments.submissions.show', [$item->id, $attempt->id]) }}">Attempt {{ $attempt->attempt_no }}</a>@else Attempt {{ $attempt->attempt_no }}@endif: {{ $attempt->state_label }}{{ $attempt->percent !== null ? ', '.$pct($attempt->percent) : '' }}{{ $attempt->submitted_at ? ', '.$when($attempt->submitted_at) : '' }}
                        </div>
                      @empty
                        {{ F::NONE }}
                      @endforelse
                    </td>
                    <td>
                      @if($row['feedback'])<div style="white-space:pre-line;max-width:40ch">{{ $row['feedback'] }}</div>@endif
                      @if($row['answer_feedback'] > 0)<div class="rp-meta">{{ $row['answer_feedback'] }} {{ $row['answer_feedback'] === 1 ? 'comment' : 'comments' }} on answers</div>@endif
                      @if(! $row['feedback'] && $row['answer_feedback'] === 0){{ F::NONE }}@endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    @elseif($class === null)
      <div class="rp-panel"><div class="rp-panel-body">
        @if($classes->isEmpty())
          You have no classes yet. Create a class and enrol students, then come back here.
        @else
          Choose one of your {{ $classes->count() }} classes to see its gradebook.
        @endif
      </div></div>
    @elseif($assessment === null)
      @php
        $assessments = $book['assessments'];
        $students = $book['students'];
      @endphp
      <section class="rp-panel">
        <div class="rp-panel-head">
          <h3 class="rp-panel-title">{{ $class->name }}{{ $class->section ? ', '.$class->section : '' }}</h3>
          <span class="rp-count">{{ $students->count() }} {{ $students->count() === 1 ? 'student' : 'students' }}, {{ $assessments->count() }} {{ $assessments->count() === 1 ? 'assessment' : 'assessments' }}</span>
        </div>
        <p class="rp-note">Each cell shows the counted attempt (the best graded attempt), its percentage and its state. The final grade is the average of a student's graded assessments, each counting equally. Completion counts every module, activity and assessment assigned to the class ({{ $completion['total'] }} {{ $completion['total'] === 1 ? 'item' : 'items' }}); an assessment counts once it is graded. Pass marks use each assessment's passing score, or {{ F::PASS_PERCENT }}% when none is set. Choose an assessment above for its feedback, or a student for their whole record.</p>
        <p class="rp-note">
          @if($certificate)
            Certificate of Completion: <a class="rp-link" href="{{ route('instructor.certificates.show', $certificate) }}">{{ $certificate->name }}</a> ({{ $certificate->statusLabel() }}). At the end of the semester, issue it there to the students who completed the class.
          @else
            At the end of the semester you can issue a Certificate of Completion to the students who completed the class: <a class="rp-link" href="{{ route('instructor.certificates.create', ['class_id' => $class->id]) }}">create one for this class</a>.
          @endif
        </p>
        @if($students->isEmpty())
          <div class="rp-panel-body">No students are enrolled in this class yet.</div>
        @elseif($assessments->isEmpty())
          <div class="rp-panel-body">This class has no published assessments yet.</div>
        @else
          <div class="rp-table-wrap">
            <table class="rp-table gb-grid">
              <thead>
                <tr>
                  <th scope="col">Student</th>
                  @foreach($assessments as $item)
                    <th scope="col"><a class="rp-link" href="{{ route('instructor.gradebook.index', ['class_id' => $class->id, 'assessment_id' => $item->id]) }}">{{ $item->title }}</a><div class="rp-meta">{{ Assessment::PURPOSES[$item->purpose] ?? 'Assessment' }}, due {{ $day($item->due_at) }}</div></th>
                  @endforeach
                  <th scope="col">{!! \App\Support\Glossary::help('final_grade') !!}</th>
                  <th scope="col">{!! \App\Support\Glossary::help('class_completion', 'Completion') !!}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $studentId => $student)
                  <tr>
                    <td><a class="rp-link" href="{{ route('instructor.gradebook.index', ['class_id' => $class->id, 'student_id' => $studentId]) }}">{{ $student->name }}</a><div class="rp-meta">{{ $student->email }}</div></td>
                    @foreach($assessments as $item)
                      @php $cell = $book['cells'][$studentId][$item->id]; @endphp
                      <td>
                        @if($cell['state'] === 'graded')
                          <span class="rp-{{ $cell['tone'] }}">{{ $pct($cell['percent']) }}</span>
                          <div class="rp-meta">{{ $cell['score_text'] }}{{ $cell['late'] ? ', late' : '' }}{{ $cell['feedback'] || $cell['answer_feedback'] > 0 ? ', feedback given' : '' }}</div>
                        @else
                          <span class="{{ $cell['tone'] ? 'rp-'.$cell['tone'] : '' }}">{{ $cell['state_label'] }}</span>
                        @endif
                      </td>
                    @endforeach
                    <td><strong>{{ $pct($book['averages'][$studentId]) }}</strong></td>
                    <td>
                      @php $done = $completion['students'][$studentId] ?? null; @endphp
                      @if($done === null || $done['total'] === 0)
                        {{ F::NONE }}
                      @elseif($done['complete'])
                        <span class="rp-good">Completed</span>
                      @else
                        {{ $done['done'] }} of {{ $done['total'] }}
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
              <tfoot>
                <tr>
                  <td>Average / Total</td>
                  @foreach($assessments as $item)
                    <td>{{ $pct($book['assessmentAverages'][$item->id]) }}</td>
                  @endforeach
                  <td>{{ $pct($book['classAverage']) }}</td>
                  <td>{{ collect($completion['students'])->where('complete', true)->count() }} of {{ $students->count() }} completed</td>
                </tr>
              </tfoot>
            </table>
          </div>
        @endif
      </section>
    @else
      @php $students = $book['students']; @endphp
      <section class="rp-panel">
        <div class="rp-panel-head">
          <h3 class="rp-panel-title">{{ $assessment->title }}</h3>
          <span class="rp-count">{{ Assessment::PURPOSES[$assessment->purpose] ?? 'Assessment' }}, due {{ $when($assessment->due_at) }}, pass mark {{ $assessment->passing_score_percent ?: F::PASS_PERCENT }}%</span>
        </div>
        <p class="rp-note"><a class="rp-back" href="{{ route('instructor.gradebook.index', ['class_id' => $class->id]) }}">← Back to the {{ $class->name }} grid</a></p>
        @if($students->isEmpty())
          <div class="rp-panel-body">No students are enrolled in this class yet.</div>
        @else
          <div class="rp-table-wrap">
            <table class="rp-table gb-detail">
              <thead>
                <tr>
                  <th scope="col">Student</th>
                  <th scope="col">Score</th>
                  <th scope="col">Percentage</th>
                  <th scope="col">Result</th>
                  <th scope="col">State</th>
                  <th scope="col">Attempts</th>
                  <th scope="col">Feedback</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $studentId => $student)
                  @php $cell = $book['cells'][$studentId][$assessment->id]; @endphp
                  <tr>
                    <td>{{ $student->name }}<div class="rp-meta">{{ $student->email }}</div></td>
                    <td>{{ $cell['score_text'] }}</td>
                    <td>{{ $pct($cell['percent']) }}</td>
                    <td>
                      @if($cell['passed'] === null)
                        {{ F::NONE }}
                      @else
                        <span class="rp-{{ $cell['passed'] ? 'good' : 'bad' }}">{{ $cell['passed'] ? 'Passed' : 'Not passed' }}</span>
                      @endif
                    </td>
                    <td>
                      <span class="{{ $cell['tone'] && $cell['state'] !== 'graded' ? 'rp-'.$cell['tone'] : '' }}">{{ $cell['state_label'] }}</span>
                      @if($cell['not_credited'])<div class="rp-meta">Kept blocked after integrity review</div>@endif
                      @if($cell['submitted_at'])<div class="rp-meta">Turned in {{ $when($cell['submitted_at']) }}</div>@endif
                    </td>
                    <td>{{ $cell['attempts'] }}{{ $cell['max_attempts'] ? ' of '.$cell['max_attempts'] : '' }}</td>
                    <td>
                      @if($cell['feedback'])<div style="white-space:pre-line;max-width:48ch">{{ $cell['feedback'] }}</div>@endif
                      @if($cell['answer_feedback'] > 0)<div class="rp-meta">{{ $cell['answer_feedback'] }} {{ $cell['answer_feedback'] === 1 ? 'comment' : 'comments' }} on answers</div>@endif
                      @if(! $cell['feedback'] && $cell['answer_feedback'] === 0){{ F::NONE }}@endif
                    </td>
                    <td>
                      @if($cell['submission_id'])
                        <a class="rp-link" href="{{ route('instructor.assessments.submissions.show', [$assessment->id, $cell['submission_id']]) }}">{{ in_array($cell['state'], ['awaiting_grade', 'held'], true) ? 'Review' : 'Open' }}</a>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </section>
    @endif
  </div>
@endsection
