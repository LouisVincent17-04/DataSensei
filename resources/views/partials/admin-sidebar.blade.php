<aside class="admin-sidebar">
  <div class="admin-sidebar-logo">
    @include('partials.brand-logo', [
      'variant' => 'sidebar',
      'size' => 'normal',
      'subtext' => 'Admin Workspace',
      'href' => route('admin.dashboard'),
    ])
  </div>

  <nav class="admin-nav-group">
    <div class="admin-nav-label">Operations</div>
    <a class="admin-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
    <a class="admin-nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a>
    <a class="admin-nav-item {{ request()->routeIs('admin.content.*') ? 'active' : '' }}" href="{{ route('admin.content.index') }}">Content</a>
    <a class="admin-nav-item {{ request()->routeIs('admin.gamification.*') ? 'active' : '' }}" href="{{ route('admin.gamification.index') }}">Gamification</a>
    <a class="admin-nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">Reports</a>
  </nav>

  <nav class="admin-nav-group">
    <div class="admin-nav-label">Quick Access</div>
    <a class="admin-nav-item" href="{{ route('modules.module-library.index') }}">Module Library</a>
    <a class="admin-nav-item" href="{{ route('profile') }}">Profile Settings</a>
  </nav>

  <div class="admin-sidebar-footer">
    <div class="admin-user-card">
      <div class="admin-avatar">{{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'A' }}</div>
      <div>
        <div class="admin-name">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
        <div class="admin-role">Platform Operator</div>
      </div>
    </div>
  </div>
</aside>

<style>
  .admin-sidebar{width:270px;min-height:100vh;height:100vh;position:sticky;top:0;background:linear-gradient(180deg,#101a2c,#0b1220);border-right:1px solid var(--border);display:flex;flex-direction:column;overflow-y:auto;z-index:90}.admin-sidebar-logo{padding:24px;border-bottom:1px solid var(--border)}.admin-nav-group{padding:22px 14px 0}.admin-nav-label{font-size:.72rem;font-weight:900;color:var(--dim);text-transform:uppercase;letter-spacing:.09em;padding:0 12px;margin-bottom:8px}.admin-nav-item{display:flex;align-items:center;gap:10px;color:var(--muted);text-decoration:none;border-radius:12px;padding:10px 12px;margin-bottom:4px;font-weight:800;font-size:.88rem;transition:.16s ease}.admin-nav-item:hover{background:var(--surface2);color:var(--text)}.admin-nav-item.active{background:linear-gradient(90deg,rgba(59,130,246,.22),rgba(139,92,246,.12));color:#fff;border:1px solid rgba(59,130,246,.24)}.admin-sidebar-footer{margin-top:auto;padding:16px;border-top:1px solid var(--border)}.admin-user-card{display:flex;align-items:center;gap:12px;border:1px solid var(--border);border-radius:14px;padding:12px;background:rgba(255,255,255,.025)}.admin-avatar{width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:12px;background:rgba(59,130,246,.14);border:1px solid rgba(59,130,246,.28);font-weight:950;color:#bfdbfe}.admin-name{font-weight:900;color:var(--text);font-size:.88rem}.admin-role{color:var(--muted);font-size:.72rem;margin-top:3px}@media(max-width:760px){.admin-sidebar{display:none}}
</style>
