<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $class->name }} Assessments — DataSensei</title>
  @include('student.assessments._list_styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => $class->name, 'pageDescription' => 'Assessments in '.$class->name.'.'])
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <span class="mobile-title">{{ $class->name }}</span>
  </header>
  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">
        <a class="crumb" href="{{ route('student.assessments.index') }}">&larr; My Classes</a>
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">{{ $class->name }}</h1>
            <p class="page-subtitle">
              @if($class->section){{ $class->section }}. @endif
              Instructor: {{ $class->instructor?->name ?? 'not set' }}.
              @if($class->is_archived) This class is archived. @endif
            </p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert success" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert danger" role="alert">{{ session('error') }}</div>
        @endif

        @php
          $total = collect($groups)->sum(fn ($rows) => count($rows));
          $sections = [
            'available' => ['Available', 'Open now. Start or continue these.', 'Nothing is open right now.'],
            'upcoming' => ['Upcoming', 'Published by your instructor, opening later.', 'Nothing is scheduled to open.'],
            'missing' => ['Missing', 'The due date has passed and nothing was turned in.', 'Nothing is missing.'],
            'late' => ['Late', 'Turned in after the due date.', 'No late submissions.'],
            'submitted' => ['Submitted', 'Turned in on time.', 'Nothing submitted yet.'],
          ];
          $when = fn ($date) => $date ? $date->format('M d, Y, g:i A') : 'No due date';
        @endphp

        @if($total === 0)
          <section class="card">
            <div class="card-head"><h2>No assessments yet</h2></div>
            <p class="card-empty">Your instructor has not given this class any assessments yet. Homework, quizzes, and examinations will appear here when they are published.</p>
          </section>
        @else
          <p class="summary-line">
            {{ count($groups['available']) }} available, {{ count($groups['upcoming']) }} upcoming, {{ count($groups['missing']) }} missing, {{ count($groups['late']) }} late, {{ count($groups['submitted']) }} submitted.
          </p>

          @foreach($sections as $key => [$heading, $note, $empty])
            <section class="card" id="{{ $key }}" aria-labelledby="{{ $key }}-title">
              <div class="card-head">
                <h2 id="{{ $key }}-title">{{ $heading }} ({{ count($groups[$key]) }})</h2>
                <span>{{ $note }}</span>
              </div>

              @if(count($groups[$key]) === 0)
                <p class="card-empty">{{ $empty }}</p>
              @else
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">Assessment</th>
                      <th scope="col">{{ $key === 'upcoming' ? 'Opens' : 'Due' }}</th>
                      <th scope="col">{{ in_array($key, ['late', 'submitted'], true) ? 'Result' : 'Status' }}</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($groups[$key] as $row)
                      @php
                        $assessment = $row['assessment'];
                        $done = $row['done'];
                        $inProgress = $row['in_progress'];
                      @endphp
                      <tr>
                        <td>
                          <strong>{{ $assessment->title }}</strong>
                          @if($assessment->purposeLabel() || ($assessment->topic_title && $assessment->topic_title !== $assessment->title))
                            <span class="sub">{{ collect([$assessment->purposeLabel(), $assessment->topic_title !== $assessment->title ? $assessment->topic_title : null])->filter()->join(', ') }}</span>
                          @endif
                        </td>
                        <td data-label="{{ $key === 'upcoming' ? 'Opens' : 'Due' }}">
                          @if($key === 'upcoming')
                            {{ $when($assessment->available_at) }}
                            @if($assessment->due_at)<span class="sub">Due {{ $when($assessment->due_at) }}</span>@endif
                          @else
                            {{ $when($assessment->due_at) }}
                          @endif
                        </td>
                        <td data-label="{{ in_array($key, ['late', 'submitted'], true) ? 'Result' : 'Status' }}">
                          @switch($key)
                            @case('available')
                              {{ $inProgress ? 'In progress' : 'Not started' }}
                              @break
                            @case('upcoming')
                              Not open yet
                              @break
                            @case('missing')
                              <span class="state-bad">{{ $assessment->status === 'closed' ? 'Missing, closed' : 'Missing' }}</span>
                              @break
                            @case('late')
                              <span class="state-warn">Late</span>, {{ $done->score }} / {{ $done->total_points }}
                              <span class="sub">Turned in {{ $when($done->submitted_at) }}</span>
                              @break
                            @default
                              <span class="state-good">{{ $done->status === 'graded' ? 'Graded' : 'Submitted' }}</span>, {{ $done->score }} / {{ $done->total_points }}
                              <span class="sub">Turned in {{ $when($done->submitted_at) }}</span>
                          @endswitch
                        </td>
                        <td class="action">
                          @if($key === 'upcoming')
                            <span class="sub">Opens later</span>
                          @elseif($done)
                            <a class="btn" href="{{ route('student.assessments.result', [$assessment, $done]) }}">View result</a>
                          @elseif($inProgress && $assessment->status === 'published')
                            <a class="btn primary" href="{{ route('student.assessments.take', [$assessment, $inProgress]) }}">Continue</a>
                          @elseif($assessment->status === 'published' && ! ($key === 'missing' && $assessment->time_limit_minutes))
                            <a class="btn primary" href="{{ route('student.assessments.show', $assessment) }}">{{ $key === 'missing' ? 'Turn in late' : 'Start' }}</a>
                          @elseif($key === 'missing' && $assessment->time_limit_minutes)
                            <span class="sub">Due date passed</span>
                          @else
                            <span class="sub">Closed</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
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
