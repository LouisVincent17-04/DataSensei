{{-- ── SIDEBAR PARTIAL ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Data Science Learning Platform',
      'href' => route('studentDashboard'),
    ])
  </div>

  <nav class="nav-group">
    <div class="nav-label">Main</div>

    <a href="{{ route('studentDashboard') }}"
       class="nav-item {{ request()->routeIs('studentDashboard') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>

    <a href="{{ route('modules.index') }}"
       class="nav-item {{ request()->routeIs('modules.index') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Modules
      <span class="badge">8</span>
    </a>

    <a href="{{ route('student.assignments.index') }}"
       class="nav-item {{ request()->routeIs('student.assignments.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
      Assignments
    </a>


    <a href="{{ route('student.achievements.index') }}"
       class="nav-item {{ request()->routeIs('student.achievements.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M8 21h8M12 17v4"/>
        <path d="M7 4h10v4a5 5 0 01-10 0V4z"/>
        <path d="M5 5H3v2a4 4 0 004 4M19 5h2v2a4 4 0 01-4 4"/>
      </svg>
      Achievements
      <span class="badge">XP</span>
    </a>

    <a href="{{ route('student.leaderboard.index') }}"
       class="nav-item {{ request()->routeIs('student.leaderboard.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 19V9m8 10V5m8 14v-7"/>
        <path d="M3 19h18"/>
      </svg>
      Leaderboard
    </a>



    <a href="{{ route('student.advanced-topics.index') }}"
       class="nav-item {{ request()->routeIs('student.advanced-topics.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      Advanced Suggestions
      <span class="badge">Next</span>
    </a>

    <a href="{{ route('ide.index') }}"
       class="nav-item {{ request()->routeIs('ide.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <polyline points="16 18 22 12 16 6"/>
        <polyline points="8 6 2 12 8 18"/>
      </svg>
      Python IDE
    </a>

    <a href="{{ route('student.analytics.index') }}"
       class="nav-item {{ request()->routeIs('student.analytics.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Analytics
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Practice</div>

    <a href="{{ route('challenges') }}"
       class="nav-item {{ request()->routeIs('challenges', 'challenges.map', 'challenges.quiz', 'challenges.quiz.submit') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
      </svg>
      Challenges
      <span class="badge">3</span>
    </a>

  <a href="{{ route('challenges.coding') }}"
    class="nav-item {{ request()->routeIs('challenges.coding', 'challenges.coding.map', 'challenges.coding.quiz') ? 'active' : '' }}">
    <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <rect x="3" y="4" width="18" height="12" rx="2" ry="2"></rect>
      <line x1="2" y1="20" x2="22" y2="20"></line>
    </svg>
    Coding Challenges
    <span class="badge">3</span>
  </a>


  <a href="{{ route('sql-sandbox.index') }}"
     class="nav-item {{ request()->routeIs('sql-sandbox.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
          <path d="M3 5v6c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path>
          <path d="M3 11v6c0 1.66 4.03 3 9 3s9-1.34 9-3v-6"></path>
      </svg>
      SQL Sandbox
  </a>

  <a href="{{ route('student.data-toolkit.index') }}"
     class="nav-item {{ request()->routeIs('student.data-toolkit.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14"/>
        <path d="M4 19h16"/>
        <path d="M8 15v-3"/>
        <path d="M12 15V8"/>
        <path d="M16 15v-5"/>
      </svg>
      Data Toolkit
      <span class="badge">Stats</span>
  </a>

    <a href="{{ route('student.submissions.index') }}"
       class="nav-item {{ request()->routeIs('student.submissions.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      Submissions
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Account</div>

    <a href="{{ route('profile') }}"
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
          @if(auth()->check() && auth()->user()->xp)
            ⚡ {{ number_format(auth()->user()->xp) }} XP
          @else
            Data Science Track
          @endif
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
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
    /* ── CENTRALIZED SIDEBAR CSS ── */
    .sidebar { width: 260px; min-height: 100vh; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 100; }
    .sidebar-logo { padding: 24px; border-bottom: 1px solid var(--border); }

    .nav-group { padding: 24px 16px 0; }
    .nav-label { font-size: 0.75rem; font-weight: 600; color: var(--dim); letter-spacing: 0.05em; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem; font-weight: 500; color: var(--muted); transition: all 0.15s ease; text-decoration: none; margin-bottom: 2px; }
    .nav-item:hover { background: var(--surface2); color: var(--text); }
    .nav-item.active { background: var(--surface2); color: var(--text); border-left: 3px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
    .nav-item .icon { width: 18px; height: 18px; flex-shrink: 0; color: var(--muted); transition: color 0.15s ease; }
    .nav-item:hover .icon { color: var(--text); }
    .nav-item.active .icon { color: var(--accent); }
    .badge { margin-left: auto; background: var(--surface2); border: 1px solid var(--border); color: var(--text); font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }
    
    .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s; }
    .user-card:hover { background: var(--surface2); }
    .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; color: var(--text); flex-shrink: 0; }
    .user-info { overflow: hidden; }
    .user-info .name  { font-size: 0.875rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .user-info .role  { font-size: 0.75rem; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    .logout-form { width: 100%; }
    .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: inherit; cursor: pointer; transition: all 0.15s ease; text-align: left; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: #ef4444; }

    @media (max-width: 700px) { .sidebar { display: none; } }
</style>