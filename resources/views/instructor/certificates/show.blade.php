@extends('instructor.layout')

@use('App\Models\CertificateDefinition')
@use('App\Services\Reports\ClassCompletion')
@use('App\Support\Certificates\CertificateLayouts')
@use('App\Support\Glossary')
@use('App\Support\Reports\ReportFormat', 'F')

@section('title', $definition->name)
@section('page_title', $definition->name)
@section('page_subtitle', 'Certificate of Completion for '.trim(($course['title'] ?? '').($course['section'] ? ', '.$course['section'] : '')).'. Check the preview, activate it, and at the end of the semester issue it to the students who completed the class.')

@section('content')
  @include('certificates._styles')

  @php
    $statusTone = ['active' => 'ct-good', 'inactive' => 'ct-muted', 'draft' => 'ct-warn'][$definition->status] ?? '';
    $active = $definition->status === CertificateDefinition::STATUS_ACTIVE;
    $rows = $review['rows'];
    $eligible = collect($rows)->filter(fn ($r) => $r['complete'] && ! $r['holds'])->count();
    $completed = collect($rows)->where('complete', true)->count();
  @endphp

  @push('head')
  <style>
    .ct-review-head { display: flex; flex-wrap: wrap; gap: 8px 18px; align-items: baseline; justify-content: space-between; }
    .ct-preview-wrap { max-width: 820px; }
    .ct-check { width: 16px; height: 16px; accent-color: var(--accent); }
    .ct-table td.ct-num, .ct-table th.ct-num { white-space: nowrap; font-variant-numeric: tabular-nums; }
    .ct-missing { margin: 6px 0 0; padding-left: 18px; color: var(--muted); font-size: .8125rem; }
    .ct-missing li { margin: 2px 0; }
    details.ct-more > summary { color: var(--ds-accent-text); cursor: pointer; font-size: .8125rem; }
    .ct-requirements { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px 18px; margin: 0; }
    @media (max-width: 900px) { .ct-requirements { grid-template-columns: minmax(0, 1fr); } }
    .ct-requirements h3 { margin: 0 0 4px; color: var(--text); font-size: .8125rem; font-weight: 600; }
    .ct-requirements ul { margin: 0; padding-left: 18px; color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
    .ct-issue-bar { display: flex; flex-wrap: wrap; gap: 10px 16px; align-items: center; padding: 12px 18px; border-top: 1px solid var(--border); }
  </style>
  @endpush

  <div class="ct-form">
    <section class="ct-section">
      <h2>Certificate</h2>
      <div class="ct-section-body">
        <dl class="ct-dl">
          <dt>{!! Glossary::help('certificate_status') !!}</dt>
          <dd><span class="{{ $statusTone }}">{{ $definition->statusLabel() }}</span>@if($active && $definition->activated_at) <span class="ct-muted">since {{ $definition->activated_at->format('M j, Y g:i A') }}</span>@endif</dd>
          <dt>Course / class</dt>
          <dd>{{ $course['title'] }}{{ $course['section'] ? ', '.$course['section'] : '' }}{{ $course['semester'] ? ' ('.$course['semester'].')' : '' }}@if($definition->classRoom?->is_archived) <span class="ct-muted">(archived class)</span>@endif</dd>
          <dt>{!! Glossary::help('certificate_requirement') !!}</dt><dd>{{ $rule }}</dd>
          <dt>{!! Glossary::help('certificate_layout') !!}</dt><dd>{{ CertificateLayouts::name($definition->layout_key) }}@unless($layoutEnabled) <span class="ct-warn">(turned off by an administrator)</span>@endunless</dd>
          <dt>Issued so far</dt><dd>{{ $issuedCount }}</dd>
        </dl>

        <div class="ct-actions">
          @if(! $active)
            <form method="POST" action="{{ route('instructor.certificates.activate', $definition) }}">
              @csrf
              @method('PATCH')
              <button class="ct-btn" type="submit" @disabled($problem !== null)>Activate</button>
            </form>
          @else
            <form method="POST" action="{{ route('instructor.certificates.deactivate', $definition) }}" onsubmit="return confirm('Make this certificate inactive? It cannot be issued while inactive; certificates already issued stay valid.');">
              @csrf
              @method('PATCH')
              <button class="ct-btn ct-btn-secondary" type="submit">Make inactive</button>
            </form>
          @endif
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.edit', $definition) }}">Edit</a>
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.preview-pdf', $definition) }}" target="_blank" rel="noopener">Preview PDF</a>
          @if($definition->classRoom)
            <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.gradebook.index', ['class_id' => $definition->class_id]) }}">Class gradebook</a>
          @endif
          @if(! $active && $issuedCount === 0)
            <form method="POST" action="{{ route('instructor.certificates.destroy', $definition) }}" onsubmit="return confirm('Delete this certificate?');">
              @csrf
              @method('DELETE')
              <button class="ct-btn ct-btn-danger" type="submit">Delete</button>
            </form>
          @endif
          <a class="ct-btn ct-btn-secondary" href="{{ route('instructor.certificates.index') }}">All certificates</a>
        </div>
        @if($problem !== null && ! $active)
          <p class="ct-help ct-warn" role="note">{{ $problem }}</p>
        @elseif(! $active)
          <p class="ct-help">Activate the certificate when the preview is right. Activating issues nothing: you issue it to the students you select below.</p>
        @endif
      </div>
    </section>

    <section class="ct-section">
      <h2>Preview with a sample learner</h2>
      <div class="ct-section-body">
        <div class="ct-preview-wrap"><div class="ct-frame">{{ $svg }}</div></div>
        <p class="ct-help">Exactly as students receive it on screen and as a PDF, with their own name, the issue date and a unique certificate ID.</p>
      </div>
    </section>

    <section class="ct-section">
      <h2>Required class work</h2>
      <div class="ct-section-body">
        @if($review['total'] === 0)
          <p class="ct-help ct-warn">This class has no assigned modules, challenges or published assessments yet, so no student can complete it. Assign the class work first.</p>
        @else
          <p class="ct-help">Everything assigned to this class counts: {{ count($review['requirements']['modules']) }} {{ count($review['requirements']['modules']) === 1 ? 'module' : 'modules' }}, {{ count($review['requirements']['activities']) }} {{ count($review['requirements']['activities']) === 1 ? 'activity' : 'activities' }} (challenges given to the class) and {{ count($review['requirements']['assessments']) }} {{ count($review['requirements']['assessments']) === 1 ? 'assessment' : 'assessments' }}. Public challenges and personal practice never count.</p>
          <details class="ct-more">
            <summary>Show the {{ $review['total'] }} required items</summary>
            <div class="ct-requirements" style="margin-top:10px">
              @foreach(ClassCompletion::GROUPS as $group => $label)
                <div>
                  <h3>{{ $label }}</h3>
                  @if($review['requirements'][$group] === [])
                    <p class="ct-help">None assigned.</p>
                  @else
                    <ul>
                      @foreach($review['requirements'][$group] as $item)
                        <li>{{ $item['title'] }} <span class="ct-muted">({{ $item['kind'] }})</span></li>
                      @endforeach
                    </ul>
                  @endif
                </div>
              @endforeach
            </div>
          </details>
        @endif
      </div>
    </section>

    <section class="ct-section" id="issue">
      <h2 class="ct-review-head"><span>Step 4. Review students and issue</span><span class="ct-muted" style="font-weight:400;font-size:.8125rem">{{ count($rows) }} enrolled, {{ $completed }} completed the class, {{ $issuedCount }} issued</span></h2>
      @if($rows === [])
        <div class="ct-empty">No students are enrolled in this class yet.</div>
      @else
        <form method="POST" action="{{ route('instructor.certificates.issue', $definition) }}" id="ct-issue-form" onsubmit="var n = this.querySelectorAll('input[name=&quot;students[]&quot;]:checked').length; if (n === 0) { alert('Select at least one student who completed the class.'); return false; } return confirm('Issue the Certificate of Completion to ' + n + (n === 1 ? ' student' : ' students') + '? Each student is checked again before it is issued.');">
          @csrf
          <div class="ct-table-wrap">
            <table class="ct-table">
              <thead>
                <tr>
                  <th scope="col"><input type="checkbox" class="ct-check" id="ct-all" aria-label="Select every student who completed the class" @disabled(! $active || $eligible === 0)></th>
                  <th scope="col">Student</th>
                  <th scope="col" class="ct-num">Modules</th>
                  <th scope="col" class="ct-num">Activities</th>
                  <th scope="col" class="ct-num">Assessments graded</th>
                  <th scope="col" class="ct-num">Final grade</th>
                  <th scope="col">Completion</th>
                  <th scope="col">Certificate</th>
                </tr>
              </thead>
              <tbody>
                @foreach($rows as $row)
                  @php
                    $student = $row['student'];
                    $canIssue = $active && $row['complete'] && ! $row['holds'];
                    $group = fn ($g) => $row['groups'][$g]['total'] === 0 ? F::NONE : $row['groups'][$g]['done'].' of '.$row['groups'][$g]['total'];
                  @endphp
                  <tr>
                    <td><input type="checkbox" class="ct-check" name="students[]" value="{{ $student->id }}" aria-label="Select {{ $student->name }}" @disabled(! $canIssue) @if($canIssue) data-eligible @endif></td>
                    <td>
                      {{ $student->name }}
                      <div class="ct-help">{{ $student->email }}</div>
                      <a class="ct-link-button" style="font-size:.8125rem" href="{{ route('instructor.gradebook.index', ['class_id' => $definition->class_id, 'student_id' => $student->id]) }}">Assessment history</a>
                    </td>
                    <td class="ct-num">{{ $group('modules') }}</td>
                    <td class="ct-num">{{ $group('activities') }}</td>
                    <td class="ct-num">{{ $group('assessments') }}</td>
                    <td class="ct-num">{{ $row['final_grade'] === null ? F::NONE : F::pct($row['final_grade']) }}</td>
                    <td>
                      @if($row['complete'])
                        <span class="ct-good">Completed</span>
                      @elseif($row['total'] === 0)
                        <span class="ct-muted">Nothing assigned</span>
                      @else
                        <span class="ct-warn">{{ $row['done'] }} of {{ $row['total'] }} done</span>
                        <details class="ct-more">
                          <summary>{{ count($row['missing']) }} not complete</summary>
                          <ul class="ct-missing">
                            @foreach($row['missing'] as $missing)
                              <li>{{ $missing['title'] }} <span>({{ $missing['kind'] }}): {{ $missing['detail'] }}</span></li>
                            @endforeach
                          </ul>
                        </details>
                      @endif
                    </td>
                    <td>
                      @if($row['certificate'])
                        {{ $row['certificate']->certificate_number }}
                        <div class="ct-help">{{ $row['certificate']->issued_at?->format('M j, Y') }}@if($row['certificate']->isRevoked()) <span class="ct-bad">(revoked)</span>@endif</div>
                      @else
                        <span class="ct-muted">Not issued</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="ct-issue-bar">
            <button class="ct-btn" type="submit" @disabled(! $active || $eligible === 0)>Issue certificate to selected students</button>
            <span class="ct-help">
              @if(! $active)
                Activate the certificate to issue it.
              @elseif($eligible === 0)
                No student can receive it right now: a student must complete every required item, and each student receives it once.
              @else
                {{ $eligible }} {{ $eligible === 1 ? 'student completed' : 'students completed' }} the class and can receive it. Only students who completed every required item can be selected; the server checks each one again when you issue.
              @endif
            </span>
          </div>
        </form>
      @endif
    </section>
  </div>
@endsection

@push('scripts')
<script>
  (function () {
    var all = document.getElementById('ct-all');
    if (!all) { return; }
    all.addEventListener('change', function () {
      Array.prototype.forEach.call(document.querySelectorAll('#ct-issue-form input[data-eligible]'), function (box) { box.checked = all.checked; });
    });
  })();
</script>
@endpush
