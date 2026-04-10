{{-- ── SIDEBAR PARTIAL ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="wordmark">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="2.5"><path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/><path d="M8 12h8M8 16h5"/></svg>
      Data<span>SciFy</span>
    </div>
    <div class="tagline">AI-Powered Learning Platform</div>
  </div>

  <nav class="nav-group">
    <div class="nav-label">Main</div>
    <a href="{{ route('studentDashboard') }}" class="nav-item {{ request()->routeIs('studentDashboard') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="{{ route('showModules') }}" class="nav-item {{ request()->routeIs('showModules') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
      Modules
      <span class="badge">8</span>
    </a>
    <a href="{{ route('ide.index') }}" class="nav-item {{ request()->routeIs('ide.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
      IDE &amp; Compiler
    </a>
    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
      Analytics
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Practice</div>
    <a href="{{ route('challenges') }}" class="nav-item {{ request()->routeIs('challenges') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
      Challenges
      <span class="badge">3</span>
    </a>
    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      Sandbox
    </a>
    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      Submissions
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Account</div>
    <a href="{{ route('profile') }}" class="nav-item {{ (request()->routeIs('profile') || 
    request()->routeIs('change-password')) ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
      Profile
    </a>

  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar">
        @if (auth()->check() && auth()->user()->name)
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        @else
          ?
        @endif
      </div>
      <div class="user-info">
        <div class="name">
          @if (auth()->check())
            {{ auth()->user()->name }}
          @else
            User
          @endif
        </div>
        <div class="role">Data Science Track</div>
      </div>
    </div>

    {{-- Laravel logout form --}}
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
      @csrf
      <button type="submit" class="logout-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        Sign Out
      </button>
    </form>
  </div>
</aside>