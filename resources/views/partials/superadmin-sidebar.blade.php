@include('partials.sidebar-shell', ['navHome' => route('superadmin.dashboard')])
{{-- ── SUPERADMIN SIDEBAR PARTIAL ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Super Admin',
      'href' => route('superadmin.dashboard'),
    ])
  </div>

  <nav class="nav-group">
    <div class="nav-label">Overview</div>

    <a href="{{ route('superadmin.dashboard') }}"
       class="nav-item {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <rect x="3" y="3" width="7" height="7" rx="1"/>
        <rect x="14" y="3" width="7" height="7" rx="1"/>
        <rect x="3" y="14" width="7" height="7" rx="1"/>
        <rect x="14" y="14" width="7" height="7" rx="1"/>
      </svg>
      Dashboard
    </a>
  </nav>

  <nav class="nav-group">
    <div class="nav-label">Management</div>

    <a href="{{ route('superadmin.users.index') }}"
       class="nav-item {{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
      </svg>
      Users
      @php
        // role is a tinyInteger column. Comparing it to the string 'superadmin'
        // excluded nobody, so this badge counted every active account.
        $manageableUsers = \App\Models\User::where('status', 'active')
            ->where('role', '!=', \App\Models\User::ROLE_SUPERADMIN)
            ->count();
      @endphp
      @if($manageableUsers > 0)
        <span class="badge" title="Active accounts you can manage">{{ $manageableUsers }}</span>
      @endif
    </a>

    <a href="{{ route('superadmin.institutions.index') }}"
       class="nav-item {{ request()->routeIs('superadmin.institutions.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
        <polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Institutions
    </a>

  </nav>

  <nav class="nav-group">
    <div class="nav-label">Platform</div>

    {{-- Platform-level governance links. Analytics is now fully wired. --}}
    <a href="{{ route('admin.module-library.index') }}"
       class="nav-item {{ request()->routeIs('admin.module-library.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Modules
    </a>

    <a href="{{ route('superadmin.analytics.index') }}"
       class="nav-item {{ request()->routeIs('superadmin.analytics.*') ? 'active' : '' }}">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Platform Analytics
    </a>

  </nav>

  <nav class="nav-group">
    <div class="nav-label">Account</div>

    <a href="{{ route('profile') }}"
       class="nav-item {{ request()->routeIs('profile', 'change-password') ? 'active' : '' }}"
       @if(request()->routeIs('profile', 'change-password')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
      Profile
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-card">
      <div class="avatar avatar-super">
        @if(auth()->check() && auth()->user()->name)
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        @else
          S
        @endif
      </div>
      <div class="user-info">
        <div class="name">{{ auth()->check() ? auth()->user()->name : 'Super Admin' }}</div>
        <div class="role">Super Administrator</div>
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
