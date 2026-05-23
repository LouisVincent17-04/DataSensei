{{-- ── SUPERADMIN SIDEBAR PARTIAL ── --}}
<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="wordmark">
      <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="var(--accent)" stroke-width="2.5">
        <path d="M4 7v10c0 2 1.5 3 3.5 3h9c2 0 3.5-1 3.5-3V7c0-2-1.5-3-3.5-3h-9C5.5 4 4 5 4 7z"/>
        <path d="M8 12h8M8 16h5"/>
      </svg>
      Data<span>Sensei</span>
    </div>
    <div class="tagline">
      <span class="superadmin-badge">Super Admin</span>
    </div>
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
      @php $pendingUsers = \App\Models\User::where('status','active')->where('role','!=','superadmin')->count(); @endphp
      @if($pendingUsers > 0)
        <span class="badge">{{ $pendingUsers }}</span>
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

    {{-- Placeholder links – wire up when those controllers are ready --}}
    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      Modules
    </a>

    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
      </svg>
      Analytics
    </a>

    <a href="#" class="nav-item">
      <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>
      </svg>
      Settings
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

<style>
  /* Reuse all student sidebar base styles + overrides */
  .sidebar { width: 260px; min-height: 100vh; background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 100; }
  .sidebar-logo { padding: 24px; border-bottom: 1px solid var(--border); }
  .sidebar-logo .wordmark { font-weight: 700; font-size: 1.25rem; letter-spacing: -0.025em; color: var(--text); display: flex; align-items: center; gap: 8px; }
  .sidebar-logo .wordmark span { color: var(--accent); }
  .sidebar-logo .tagline { margin-top: 6px; }
  .superadmin-badge { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(59,130,246,0.15)); border: 1px solid rgba(139,92,246,0.35); color: #a78bfa; padding: 3px 10px; border-radius: 20px; }

  .nav-group { padding: 24px 16px 0; }
  .nav-label { font-size: 0.75rem; font-weight: 600; color: var(--dim); letter-spacing: 0.05em; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; }
  .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem; font-weight: 500; color: var(--muted); transition: all 0.15s ease; text-decoration: none; margin-bottom: 2px; }
  .nav-item:hover { background: var(--surface2); color: var(--text); }
  .nav-item.active { background: var(--surface2); color: var(--text); border-left: 3px solid var(--accent2); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
  .nav-item .icon { width: 18px; height: 18px; flex-shrink: 0; color: var(--muted); transition: color 0.15s ease; }
  .nav-item:hover .icon { color: var(--text); }
  .nav-item.active .icon { color: var(--accent2); }
  .badge { margin-left: auto; background: var(--surface2); border: 1px solid var(--border); color: var(--text); font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }

  .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }
  .user-card { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s; }
  .user-card:hover { background: var(--surface2); }
  .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; color: var(--text); flex-shrink: 0; }
  .avatar-super { background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(59,130,246,0.25)); border-color: rgba(139,92,246,0.4); color: #a78bfa; }
  .user-info .name  { font-size: 0.875rem; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .user-info .role  { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
  .logout-form { width: 100%; }
  .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: inherit; cursor: pointer; transition: all 0.15s ease; text-align: left; }
  .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: #ef4444; }

  @media (max-width: 700px) { .sidebar { display: none; } }
</style>