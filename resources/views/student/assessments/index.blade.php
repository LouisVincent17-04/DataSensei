<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Assessments — DataSensei</title>
  @include('student.assessments._list_styles')
  @include('partials.admin-inspired-page-style')
  @include('partials.page-head', ['pageTitle' => 'Assessments', 'pageDescription' => 'Choose a class to see its homework, quizzes, and examinations.'])
</head>
<body class="ds-admin-inspired">
  <header class="mobile-header" aria-label="Mobile navigation">
    <button class="hamburger" id="js-menu-btn" aria-label="Open menu" aria-expanded="false">
      <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <span class="mobile-title">Assessments</span>
  </header>
  <div class="sidebar-overlay" id="js-overlay" aria-hidden="true"></div>

  <div class="ds-shell">
    @include('partials.sidebar')

    <main class="ds-main">
      <div class="wrap">
        <div class="top-row">
          <div>
            <h1 class="page-title ds-page-title">Assessments</h1>
            <p class="page-subtitle">Your classes. Open a class to see its available, upcoming, submitted, missing and late homework, quizzes, and examinations.</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert success" role="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
          <div class="alert danger" role="alert">{{ session('error') }}</div>
        @endif

        @php
          $describe = function (array $counts): string {
            $labels = ['available' => 'available', 'upcoming' => 'upcoming', 'missing' => 'missing', 'late' => 'late', 'submitted' => 'submitted'];
            $parts = [];
            foreach ($labels as $key => $label) {
              if (($counts[$key] ?? 0) > 0) {
                $parts[] = $counts[$key].' '.$label;
              }
            }
            return $parts === [] ? 'No assessments yet' : implode(', ', $parts);
          };
          $active = $classes->where('is_archived', false);
          $archived = $classes->where('is_archived', true);
        @endphp

        <section class="card" aria-labelledby="my-classes">
          <div class="card-head">
            <h2 id="my-classes">My Classes</h2>
            <span>{{ $active->count() }} {{ $active->count() === 1 ? 'class' : 'classes' }}</span>
          </div>

          @if($active->isEmpty())
            <p class="card-empty">You are not enrolled in any class yet. When your instructor adds you to a class, it appears here with its assessments.</p>
          @else
            <table class="table">
              <thead>
                <tr><th scope="col">Class</th><th scope="col">Instructor</th><th scope="col">Assessments</th><th scope="col"></th></tr>
              </thead>
              <tbody>
                @foreach($active as $class)
                  @php $counts = $summaries[$class->id] ?? []; @endphp
                  <tr>
                    <td>
                      <a class="row-link" href="{{ route('student.assessments.class', $class) }}"><strong>{{ $class->name }}</strong></a>
                      @if($class->section)<span class="sub">{{ $class->section }}</span>@endif
                    </td>
                    <td data-label="Instructor">{{ $class->instructor?->name ?? '—' }}</td>
                    <td data-label="Assessments">
                      <span class="{{ ($counts['missing'] ?? 0) > 0 ? 'state-bad' : '' }}">{{ $describe($counts) }}</span>
                    </td>
                    <td class="action"><a class="btn primary" href="{{ route('student.assessments.class', $class) }}">Open class</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </section>

        @if($archived->isNotEmpty())
          <section class="card" aria-labelledby="past-classes">
            <div class="card-head">
              <h2 id="past-classes">Archived classes</h2>
              <span>Finished classes you can still look back on</span>
            </div>
            <table class="table">
              <thead>
                <tr><th scope="col">Class</th><th scope="col">Instructor</th><th scope="col">Assessments</th><th scope="col"></th></tr>
              </thead>
              <tbody>
                @foreach($archived as $class)
                  <tr>
                    <td>
                      <a class="row-link" href="{{ route('student.assessments.class', $class) }}"><strong>{{ $class->name }}</strong></a>
                      @if($class->section)<span class="sub">{{ $class->section }}</span>@endif
                    </td>
                    <td data-label="Instructor">{{ $class->instructor?->name ?? '—' }}</td>
                    <td data-label="Assessments">{{ $describe($summaries[$class->id] ?? []) }}</td>
                    <td class="action"><a class="btn" href="{{ route('student.assessments.class', $class) }}">Open class</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </section>
        @endif
      </div>
    </main>
  </div>

  @include('student.assessments._menu_script')
</body>
</html>
