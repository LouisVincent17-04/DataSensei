@include('partials.sidebar-shell', ['navHome' => route('admin.dashboard')])
<aside class="sidebar" id="admin-sidebar">
  <div class="sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Admin Workspace',
      'href' => route('admin.dashboard'),
    ])
  </div>

  <nav class="nav-group" aria-label="Admin main navigation">
    <div class="nav-label">Main</div>

    <a href="{{ route('admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>
  </nav>

  <nav class="nav-group" aria-label="Admin management navigation">
    <div class="nav-label">Management</div>

    <a href="{{ route('admin.users.index') }}"
       class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
       @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>
      Users
    </a>

    <a href="{{ route('admin.content.index') }}"
       class="nav-item {{ request()->routeIs('admin.content.*') ? 'active' : '' }}"
       @if(request()->routeIs('admin.content.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Content
    </a>

    <a href="{{ route('admin.gamification.index') }}"
       class="nav-item {{ request()->routeIs('admin.gamification.*') ? 'active' : '' }}"
       @if(request()->routeIs('admin.gamification.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M8 21h8M12 17v4"/><path d="M7 4h10v4a5 5 0 01-10 0V4z"/><path d="M5 5H3v2a4 4 0 004 4M19 5h2v2a4 4 0 01-4 4"/>
      </svg>
      Gamification
    </a>

    <a href="{{ route('admin.reports.index') }}"
       class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
       @if(request()->routeIs('admin.reports.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 17v-6m4 6V7m4 10v-3"/><path d="M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
      </svg>
      Reports
    </a>
  </nav>

  <nav class="nav-group" aria-label="Admin content navigation">
    <div class="nav-label">Platform Content</div>

    <a class="nav-item {{ request()->routeIs('admin.module-library.*') ? 'active' : '' }}" href="{{ route('admin.module-library.index') }}"
       @if(request()->routeIs('admin.module-library.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
      </svg>
      Learning Modules
    </a>

    <a class="nav-item {{ request()->routeIs('admin.challenges.*') ? 'active' : '' }}" href="{{ route('admin.challenges.index') }}"
       @if(request()->routeIs('admin.challenges.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="9"/><path d="M9.8 9a2.3 2.3 0 114.2 1.3c-.8.8-2 1.1-2 2.7M12 17h.01"/>
      </svg>
      MCQ Challenges
    </a>

    <a class="nav-item {{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}" href="{{ route('admin.assessments.index') }}"
       @if(request()->routeIs('admin.assessments.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12l2 2 4-4M9 18h6"/>
      </svg>
      Assessments
    </a>
  </nav>

  <nav class="nav-group" aria-label="Admin account navigation">
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
      <div class="avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
      <div class="user-info">
        <div class="name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
        <div class="role">Platform Operator</div>
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
