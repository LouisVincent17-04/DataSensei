{{-- resources/views/partials/instructor-sidebar.blade.php --}}
<aside class="sidebar">
  @php
    use Illuminate\Support\Facades\Route;

    $safeRoute = function ($name, $fallback = '#', $params = []) {
      return Route::has($name) ? route($name, $params) : $fallback;
    };
  @endphp

  <div class="sidebar-logo">
    <div class="wordmark">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="2.5">
        <path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/>
        <path d="M8 12h8M8 16h5"/>
      </svg>
      Data<span>Sensei</span>
    </div>
    <div class="tagline">Instructor Workspace</div>
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
      <span class="badge">Live</span>
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Assignments</div>

    <a href="{{ $safeRoute('modules.module_list', '/modules/module-library') }}"
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

    <a href="{{ $safeRoute('instructor.assignments.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.assignments.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
      Assignments
    </a>

    <a href="{{ $safeRoute('instructor.submissions.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.submissions.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/>
      </svg>
      Submissions
      <span class="badge">Review</span>
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Analytics</div>

    <a href="{{ $safeRoute('instructor.analytics.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.analytics.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Class Analytics
    </a>

    <a href="{{ $safeRoute('instructor.mastery.index', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.mastery.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 3l7 4v5c0 5-3.5 8-7 9-3.5-1-7-4-7-9V7l7-4z"/>
      </svg>
      ILO Mastery
    </a>

    <a href="{{ $safeRoute('instructor.students.risk', '#') }}"
       class="nav-item {{ request()->routeIs('instructor.risk.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 9v4m0 4h.01"/>
        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      </svg>
      At-Risk Alerts
      <span class="badge badge-warn">!</span>
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

<style>
  .sidebar {
    width: 270px;
    min-height: 100vh;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
    z-index: 100;
  }

  .sidebar-logo {
    padding: 24px;
    border-bottom: 1px solid var(--border);
  }

  .sidebar-logo .wordmark {
    font-weight: 800;
    font-size: 1.25rem;
    letter-spacing: -0.025em;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .sidebar-logo .wordmark span {
    color: var(--accent);
  }

  .sidebar-logo .tagline {
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 4px;
    font-weight: 600;
  }

  .nav-group {
    padding: 22px 16px 0;
  }

  .nav-label {
    font-size: 0.72rem;
    font-weight: 800;
    color: var(--dim);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 0 12px;
    margin-bottom: 8px;
  }

  .nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 12px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--muted);
    transition: all 0.15s ease;
    text-decoration: none;
    margin-bottom: 2px;
  }

  .nav-item:hover {
    background: var(--surface2);
    color: var(--text);
  }

  .nav-item.active {
    background: var(--surface2);
    color: var(--text);
    border-left: 3px solid var(--accent);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
  }

  .nav-item .icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    color: var(--muted);
    transition: color 0.15s ease;
  }

  .nav-item:hover .icon {
    color: var(--text);
  }

  .nav-item.active .icon {
    color: var(--accent);
  }

  .badge {
    margin-left: auto;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 12px;
  }

  .badge-warn {
    color: #ef4444;
    border-color: rgba(239,68,68,0.3);
    background: rgba(239,68,68,0.08);
  }

  .sidebar-footer {
    margin-top: auto;
    padding: 16px;
    border-top: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .user-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background 0.15s;
  }

  .user-card:hover {
    background: var(--surface2);
  }

  .avatar {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-sm);
    background: var(--surface2);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.875rem;
    color: var(--text);
    flex-shrink: 0;
  }

  .user-info {
    overflow: hidden;
  }

  .user-info .name {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .user-info .role {
    font-size: 0.75rem;
    color: var(--muted);
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .logout-form {
    width: 100%;
  }

  .logout-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 8px 12px;
    border-radius: var(--radius-sm);
    background: transparent;
    border: 1px solid var(--border);
    color: var(--muted);
    font-size: 0.875rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    transition: all 0.15s ease;
    text-align: left;
  }

  .logout-btn:hover {
    background: rgba(239,68,68,0.08);
    border-color: rgba(239,68,68,0.3);
    color: #ef4444;
  }

  @media (max-width: 700px) {
    .sidebar {
      display: none;
    }
  }
</style>
