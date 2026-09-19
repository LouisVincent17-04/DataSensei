@include('partials.sidebar-shell', ['navHome' => route('institution-admin.dashboard')])
{{--
    Institution admin sidebar.

    The dashboard and the applications screen each carried their own copy of
    this navigation, which is why the institution admin had no way to reach a
    profile page. One partial now serves every institution-admin screen, and it
    uses the same navigation shell as every other role.

    $pendingCount is optional: pages that already count pending applications
    pass it in, and the badge is hidden everywhere else.
--}}
<aside class="sidebar" id="institution-admin-sidebar">
  <div class="sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Institution Admin',
      'href' => route('institution-admin.dashboard'),
    ])
  </div>

  <nav class="nav-group" aria-label="Institution admin overview">
    <div class="nav-label">Overview</div>

    <a href="{{ route('institution-admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('institution-admin.dashboard') ? 'active' : '' }}"
       @if(request()->routeIs('institution-admin.dashboard')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
  </nav>

  <nav class="nav-group" aria-label="Institution admin applications">
    <div class="nav-label">Applications</div>

    <a href="{{ route('institution-admin.applications.index') }}"
       class="nav-item {{ request()->routeIs('institution-admin.applications.*') ? 'active' : '' }}"
       @if(request()->routeIs('institution-admin.applications.*')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
      Instructor Applications
      @if(($pendingCount ?? 0) > 0)
        <span class="badge">{{ $pendingCount }}</span>
      @endif
    </a>
  </nav>

  <nav class="nav-group" aria-label="Institution admin account">
    <div class="nav-label">Account</div>

    <a href="{{ route('profile') }}"
       class="nav-item {{ request()->routeIs('profile', 'change-password') ? 'active' : '' }}"
       @if(request()->routeIs('profile', 'change-password')) aria-current="page" @endif>
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profile
    </a>
  </nav>

  <div class="sidebar-footer">
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
      @csrf
      <button type="submit" class="logout-btn">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Log out
      </button>
    </form>
  </div>
</aside>
