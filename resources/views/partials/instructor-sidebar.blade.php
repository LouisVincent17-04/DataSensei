@include('partials.sidebar-shell', ['navHome' => \Illuminate\Support\Facades\Route::has('instructor.dashboard') ? route('instructor.dashboard') : url('/instructor/dashboard')])
{{-- resources/views/partials/instructor-sidebar.blade.php --}}
<aside class="sidebar">
  @php
    use Illuminate\Support\Facades\Route;

    $safeRoute = function ($name, $fallback = '#', $params = []) {
      return Route::has($name) ? route($name, $params) : $fallback;
    };
  @endphp

  <div class="sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Instructor Workspace',
      'href' => $safeRoute('instructor.dashboard', '/instructor/dashboard'),
    ])
  </div>

  <nav class="nav-group">
    <div class="nav-label">Teaching</div>

    <a href="{{ $safeRoute('instructor.dashboard', '/instructor/dashboard') }}"
       class="nav-item {{ request()->is('instructor/dashboard') || request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>

    <a href="{{ $safeRoute('instructor.classes.index', '/classes') }}"
       class="nav-item {{ request()->routeIs('instructor.classes.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1"/>
        <path d="M12 12a4 4 0 100-8 4 4 0 000 8z"/>
      </svg>
      Classes
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Assignments</div>

    <a href="{{ $safeRoute('modules.module-library.index', '/modules/module-library') }}"
       class="nav-item {{ request()->routeIs('modules.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Module Library
    </a>

    <a href="{{ $safeRoute('instructor.challenges.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.challenges.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      Challenge Pool
    </a>

    <a href="{{ $safeRoute('instructor.challenge-builder.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.challenge-builder.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M8 9l-4 3 4 3M16 9l4 3-4 3M13 6l-2 12"/>
      </svg>
      Challenge Builder
    </a>

    <a href="{{ $safeRoute('instructor.class-challenges.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.class-challenges.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 13l2 2 4-4"/>
      </svg>
      Class Challenges
    </a>
    <a href="{{ $safeRoute('instructor.assessments.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.assessments.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 4h16v16H4z"/><path d="M8 9h8M8 13h8M8 17h5"/>
      </svg>
      Assessments
    </a>

    <a href="{{ $safeRoute('instructor.assignments.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.assignments.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
      Assignments
    </a>




    <a href="{{ $safeRoute('instructor.tos.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.tos.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 5h16M4 12h16M4 19h16"/>
      </svg>
      Table of Specifications
    </a>


    <a href="{{ $safeRoute('instructor.anti-cheat.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.anti-cheat.*') && ! request()->routeIs('instructor.anti-cheat.events') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
        <path d="M9 12l2 2 4-5"/>
      </svg>
      Anti-Cheat
    </a>



    <a href="{{ $safeRoute('instructor.anti-cheat.events', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.anti-cheat.events') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 9v4m0 4h.01"/>
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      </svg>
      Anti-Cheat Events
    </a>


    <a href="{{ $safeRoute('instructor.submissions.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.submissions.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
      </svg>
      Submissions
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Analytics</div>

    <a href="{{ $safeRoute('instructor.model-development.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.model-development.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14"/>
        <path d="M8 15l3-3 2 2 3-4"/><path d="M4 19h16"/>
      </svg>
      Student Model Development
    </a>

    <a href="{{ $safeRoute('instructor.analytics.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.analytics.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Class Analytics
    </a>

    <a href="{{ $safeRoute('instructor.competencies.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.competencies.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/>
        <path d="M2 21h20"/>
      </svg>
      Skills Competency Matrix
    </a>

    <a href="{{ $safeRoute('instructor.mastery.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.mastery.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
      </svg>
      ILO Mastery
    </a>

    <a href="{{ $safeRoute('instructor.risk.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.risk.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 9v4m0 4h.01"/>
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      </svg>
      At-Risk Alerts
    </a>

    <a href="{{ $safeRoute('instructor.reports.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.reports.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 10v6m0 0l-3-3m3 3l3-3"/>
        <path d="M4 4h16v16H4z"/>
      </svg>
      Reports & Exports
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Account</div>

    <a href="{{ $safeRoute('profile', '/profile') }}"
       class="nav-item {{ request()->routeIs('profile', 'change-password') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      Profile
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">
        @if(auth()->check() && auth()->user()->name)
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        @else
          ?
        @endif
      </div>

      <div class="user-info">
        <div class="name">
          {{ auth()->check() ? auth()->user()->name : 'Guest' }}
        </div>
        <div class="role">
          Instructor
        </div>
      </div>
    </div>

    <form method="POST" action="{{ $safeRoute('logout', '/logout') }}" class="logout-form">
      @csrf
      <button type="submit" class="logout-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sign Out
      </button>
    </form>
  </div>
</aside>
