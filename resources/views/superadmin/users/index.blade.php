<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — User Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    :root {
      --bg:#0d1320; --surface:#111c2d; --surface2:#1a2638; --border:#1e2f47; --border-hover:#2c4168;
      --accent:#3b82f6; --accent-hover:#2563eb; --accent2:#8b5cf6; --accent3:#10b981; --accent4:#f59e0b;
      --warn:#ef4444; --text:#fafafa; --muted:#7f93b0; --dim:#3d5272; --radius:8px; --radius-sm:6px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

    /* TOPBAR */
    .topbar { height: 64px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; flex: 1; letter-spacing: -0.01em; }

    /* CONTENT */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }

    /* FLASH */
    .flash { padding: 12px 20px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; border-left: 3px solid; }
    .flash-success { background: rgba(16,185,129,0.08); border-color: var(--accent3); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,0.08);  border-color: var(--warn);    color: var(--warn); }

    /* TOOLBAR */
    .toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .search-box { display: flex; align-items: center; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 14px; gap: 10px; min-width: 260px; flex: 1; max-width: 400px; transition: border-color 0.15s; }
    .search-box:focus-within { border-color: var(--accent); }
    .search-box input { background: none; border: none; outline: none; color: var(--text); font-size: 0.875rem; font-family: inherit; width: 100%; }
    .search-box input::placeholder { color: var(--dim); }
    select.filter { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 14px; color: var(--text); font-size: 0.875rem; font-family: inherit; cursor: pointer; outline: none; }
    select.filter:focus { border-color: var(--accent); }

    /* BUTTONS */
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 18px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: inherit; text-decoration: none; }
    .btn-primary  { background: var(--accent);  color: #fff; }
    .btn-primary:hover  { background: var(--accent-hover); }
    .btn-ghost    { background: var(--surface);  color: var(--text);    border-color: var(--border); }
    .btn-ghost:hover    { border-color: var(--border-hover); }
    .btn-danger   { background: rgba(239,68,68,0.10); color: var(--warn);    border-color: rgba(239,68,68,0.20); }
    .btn-danger:hover   { background: rgba(239,68,68,0.18); }
    .btn-purple   { background: rgba(139,92,246,0.12); color: #a78bfa; border-color: rgba(139,92,246,0.25); }
    .btn-purple:hover   { background: rgba(139,92,246,0.20); }
    .btn-amber    { background: rgba(245,158,11,0.10); color: var(--accent4); border-color: rgba(245,158,11,0.25); }
    .btn-amber:hover    { background: rgba(245,158,11,0.18); }
    .btn-teal     { background: rgba(16,185,129,0.10); color: var(--accent3); border-color: rgba(16,185,129,0.25); }
    .btn-teal:hover     { background: rgba(16,185,129,0.18); }
    .btn-sm { padding: 5px 12px; font-size: 0.8rem; }

    /* CARD */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .card-header { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
    .card-title { font-size: 0.9375rem; font-weight: 600; }
    .card-subtitle { font-size: 0.8rem; color: var(--muted); margin-top: 3px; }

    /* TABLE */
    .tbl-wrap { overflow-x: auto; }
    .tbl { width: 100%; border-collapse: collapse; }
    .tbl th { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--dim); padding: 10px 16px; border-bottom: 1px solid var(--border); text-align: left; white-space: nowrap; }
    .tbl td { font-size: 0.875rem; color: var(--text); padding: 14px 16px; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tr:hover td { background: var(--surface2); }
    .tbl-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    /* PILLS */
    .pill { display: inline-flex; align-items: center; font-size: 0.7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
    .pill-active      { background: rgba(16,185,129,0.12);  color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-disabled    { background: rgba(239,68,68,0.08);   color: var(--warn);    border: 1px solid rgba(239,68,68,0.20); }
    .pill-student     { background: rgba(59,130,246,0.12);  color: #60a5fa; border: 1px solid rgba(59,130,246,0.25); }
    .pill-admin       { background: rgba(249,115,22,0.12);  color: #fb923c; border: 1px solid rgba(249,115,22,0.25); }
    .pill-super-admin { background: rgba(139,92,246,0.12);  color: #a78bfa; border: 1px solid rgba(139,92,246,0.25); }
    .pill-instructor   { background: rgba(16,185,129,0.12);  color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-institution-admin { background: rgba(245,158,11,0.10); color: var(--accent4); border-color: rgba(245,158,11,0.25); }

    /* AVATAR */
    .av { width: 34px; height: 34px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: inline-flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: var(--accent); flex-shrink: 0; }

    /* PAGINATION */
    .pagination { display: flex; gap: 6px; justify-content: center; padding: 20px; }
    .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 500; border: 1px solid var(--border); color: var(--muted); text-decoration: none; transition: all 0.15s; }
    .pagination a:hover { background: var(--surface2); color: var(--text); }
    .pagination .active span { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* MODAL */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(3px); z-index: 1000; display: none; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.5); }
    .modal-header { padding: 24px 28px 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-title  { font-size: 1.1rem; font-weight: 700; }
    .modal-close  { background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; border-radius: 4px; display: flex; transition: color 0.15s; }
    .modal-close:hover { color: var(--text); }
    .modal-body   { padding: 24px 28px 28px; display: flex; flex-direction: column; gap: 18px; }
    .form-row     { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group   { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 0.8rem; font-weight: 600; color: var(--muted); }
    .form-control { background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 9px 14px; color: var(--text); font-size: 0.875rem; font-family: inherit; outline: none; transition: border-color 0.15s; width: 100%; }
    .form-control:focus { border-color: var(--accent); }
    .form-error   { font-size: 0.75rem; color: var(--warn); margin-top: 2px; }
    .modal-footer { display: flex; gap: 10px; justify-content: flex-end; padding-top: 8px; }

    /* NOTICES */
    .notice { font-size: 0.78rem; color: var(--muted); background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 10px 14px; line-height: 1.5; }
    .notice strong { color: #a78bfa; }
    .notice-warn  { border-color: rgba(245,158,11,0.30); background: rgba(245,158,11,0.06); }
    .notice-warn strong { color: var(--accent4); }
    .notice-teal  { border-color: rgba(16,185,129,0.30); background: rgba(16,185,129,0.06); }
    .notice-teal strong { color: var(--accent3); }
    .notice-danger { border-color: rgba(239,68,68,0.30); background: rgba(239,68,68,0.06); }
    .notice-danger strong { color: var(--warn); }
  </style>
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">
    <div class="topbar">
      <h1>User Management</h1>
    </div>

    <div class="content">

      {{-- Flash --}}
      @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="flash flash-error">{{ session('error') }}</div>
      @endif

      {{-- TOOLBAR --}}
      <form method="GET" action="{{ route('superadmin.users.index') }}">
        <div class="toolbar">
          <div class="search-box">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--dim)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" placeholder="Search name or email…" value="{{ request('search') }}">
          </div>
          <select name="role" class="filter" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="student"     {{ request('role') === 'student'     ? 'selected' : '' }}>Student</option>
            <option value="admin"       {{ request('role') === 'admin'       ? 'selected' : '' }}>Admin</option>
            <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
          </select>
          <select name="status" class="filter" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
          </select>
          <select name="institution_id" class="filter" onchange="this.form.submit()">
            <option value="">All Institutions</option>
            @foreach($institutions as $inst)
              <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
            @endforeach
          </select>
          <button type="submit" class="btn btn-ghost">Filter</button>
          <a href="{{ route('superadmin.users.index') }}" class="btn btn-ghost">Reset</a>
          <button type="button" class="btn btn-primary" onclick="openModal('createModal')" style="margin-left:auto;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Add User
          </button>
        </div>
      </form>

      {{-- TABLE --}}
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">All Users</div>
            <div class="card-subtitle">{{ $totalUsers }} user{{ $totalUsers !== 1 ? 's' : '' }} found</div>
          </div>
        </div>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Institution</th>
                <th>XP</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
              <tr>
                <td>
                  <div style="display:flex;align-items:center;gap:12px;">
                    <div class="av">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div>
                      <div style="font-weight:600;">{{ $user->name }}</div>
                      <div style="font-size:0.75rem;color:var(--muted);">{{ $user->email }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="pill
                    @switch($user->role)
                      @case(1) pill-student @break
                      @case(2) pill-admin @break
                      @case(3) pill-super-admin @break
                      @case(4) pill-instructor @break
                      @case(5) pill-institution-admin @break
                    @endswitch">
                    @switch($user->role)
                      @case(1) Student @break
                      @case(2) Admin @break
                      @case(3) Super Admin @break
                      @case(4) Instructor @break
                      @case(5) Institution Admin @break
                    @endswitch
                  </span>
                </td>
                <td style="color:var(--muted);font-size:0.8rem;">
                  {{ $user->institution?->name ?? '—' }}
                </td>
                <td style="font-weight:600;color:var(--accent4);">
                  {{ number_format($user->xp ?? 0) }}
                </td>
                <td>
                  <span class="pill {{ $user->status === 'active' ? 'pill-active' : 'pill-disabled' }}">
                    {{ $user->status }}
                  </span>
                </td>
                <td style="color:var(--muted);font-size:0.8rem;white-space:nowrap;">
                  {{ $user->created_at->format('M d, Y') }}
                </td>
                <td>
                  <div class="tbl-actions">

                    {{-- Edit (name / email / status only) --}}
                    <button class="btn btn-ghost btn-sm"
                      onclick="openEditModal(
                        {{ $user->id }},
                        '{{ addslashes($user->name) }}',
                        '{{ $user->email }}',
                        {{ $user->role }},
                        '{{ $user->status }}'
                      )">Edit</button>

                    {{-- Toggle Status (cannot self-disable) --}}
                    @if($user->id !== auth()->id())
                      <form method="POST" action="{{ route('superadmin.users.toggleStatus', $user) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                          class="btn btn-sm {{ $user->status === 'active' ? 'btn-danger' : 'btn-ghost' }}"
                          onclick="return confirm('{{ $user->status === 'active' ? 'Disable' : 'Enable' }} this user?')">
                          {{ $user->status === 'active' ? 'Disable' : 'Enable' }}
                        </button>
                      </form>
                    @endif

                    {{-- Promote: students and admins only, not yourself --}}
                    @if($user->role < 3 && $user->id !== auth()->id())
                      <button class="btn btn-purple btn-sm"
                        onclick="openPromoteModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->role }})">
                        Promote
                      </button>
                    @endif

                    {{-- Demote: admins and super admins only, not yourself --}}
                    @if($user->role >= 2 && $user->id !== auth()->id())
                      <button class="btn btn-amber btn-sm"
                        onclick="openDemoteModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->role }})">
                        Demote
                      </button>
                    @endif

                    {{-- Assign Institution Admin: non-super-admins, not yourself --}}
                    @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                      <button class="btn btn-teal btn-sm"
                        onclick="openAssignInstModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->institution_id ?? 'null' }})">
                        Inst. Admin
                      </button>
                    @endif

                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align:center;color:var(--muted);padding:40px;">
                  No users found. Try adjusting your filters.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="pagination">
          {{ $users->links() }}
        </div>
      </div>

    </div>
  </div>

  {{-- ═══════════════ CREATE MODAL ═══════════════ --}}
  <div class="modal-overlay" id="createModal">
    <div class="modal">
      <div class="modal-header">
        <span class="modal-title">Add New User</span>
        <button class="modal-close" onclick="closeModal('createModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('superadmin.users.store') }}">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name *</label>
              <input class="form-control" type="text" name="name" placeholder="Juan dela Cruz" required value="{{ old('name') }}">
              @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>Email Address *</label>
              <input class="form-control" type="email" name="email" placeholder="juan@school.edu" required value="{{ old('email') }}">
              @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Password *</label>
              <input class="form-control" type="password" name="password" placeholder="Min. 6 characters" required>
              @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>Confirm Password *</label>
              <input class="form-control" type="password" name="password_confirmation" placeholder="Repeat password" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Role *</label>
              <select class="form-control" name="role" required>
                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="admin"   {{ old('role') === 'admin'   ? 'selected' : '' }}>Admin</option>
              </select>
              @error('role')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>Status *</label>
              <select class="form-control" name="status" required>
                <option value="active"   {{ old('status','active') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="disabled" {{ old('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
              </select>
            </div>
          </div>
          <div class="notice notice-teal">
            <strong>Note:</strong> New users are not assigned to any institution at creation.
            To appoint someone as an institution admin, use the <strong>Inst. Admin</strong> button on the table after creating them.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">Cancel</button>
            <button type="submit" class="btn btn-primary">Create User</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════════════ EDIT MODAL (name / email / status only) ═══════════════ --}}
  <div class="modal-overlay" id="editModal">
    <div class="modal">
      <div class="modal-header">
        <span class="modal-title">Edit User</span>
        <button class="modal-close" onclick="closeModal('editModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" id="editForm" action="">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label>Full Name *</label>
              <input class="form-control" type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
              <label>Email Address *</label>
              <input class="form-control" type="email" name="email" id="edit_email" required>
            </div>
          </div>
          <div class="form-group">
            <label>Status *</label>
            <select class="form-control" name="status" id="edit_status" required>
              <option value="active">Active</option>
              <option value="disabled">Disabled</option>
            </select>
          </div>
          {{-- Role is passed as a hidden field; role changes use Promote / Demote --}}
          <input type="hidden" name="role" id="edit_role_hidden">
          <div class="notice">
            <strong>Note:</strong> Role changes use the <strong>Promote</strong> / <strong>Demote</strong> buttons.
            To assign or change institution admin rights, use the <strong>Inst. Admin</strong> button.
            This form only updates name, email, and status.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════════════ PROMOTE MODAL ═══════════════ --}}
  <div class="modal-overlay" id="promoteModal">
    <div class="modal" style="max-width:440px;">
      <div class="modal-header">
        <span class="modal-title">Promote User</span>
        <button class="modal-close" onclick="closeModal('promoteModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" id="promoteForm" action="">
        @csrf @method('PATCH')
        <div class="modal-body">
          <p style="font-size:0.875rem;color:var(--muted);line-height:1.6;">
            Promoting <strong id="promote_name" style="color:var(--text);"></strong> will change their system access immediately.
          </p>
          <div class="form-group">
            <label>Promote to *</label>
            <select class="form-control" name="role" id="promote_role" required>
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
          <div class="notice">
            <strong>Heads up:</strong> Promoting to <strong>Super Admin</strong> will detach the user from their current institution.
            To appoint an institution-level admin, use <strong>Inst. Admin</strong> instead.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('promoteModal')">Cancel</button>
            <button type="submit" class="btn btn-purple">Confirm Promotion</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════════════ DEMOTE MODAL ═══════════════ --}}
  <div class="modal-overlay" id="demoteModal">
    <div class="modal" style="max-width:440px;">
      <div class="modal-header">
        <span class="modal-title">Demote User</span>
        <button class="modal-close" onclick="closeModal('demoteModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" id="demoteForm" action="">
        @csrf @method('PATCH')
        <div class="modal-body">
          <p style="font-size:0.875rem;color:var(--muted);line-height:1.6;">
            You are about to demote <strong id="demote_name" style="color:var(--text);"></strong>
            from <strong id="demote_from" style="color:var(--accent4);"></strong>
            to <strong id="demote_to" style="color:var(--accent4);"></strong>.
          </p>
          <div class="notice notice-warn">
            <strong>Warning:</strong> Demoting an <strong>Admin → Student</strong> will also remove them from their institution.
            They will lose all institution-admin rights immediately.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('demoteModal')">Cancel</button>
            <button type="submit" class="btn btn-amber">Confirm Demotion</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ═══════════════ ASSIGN INSTITUTION ADMIN MODAL ═══════════════ --}}
  <div class="modal-overlay" id="assignInstModal">
    <div class="modal" style="max-width:460px;">
      <div class="modal-header">
        <span class="modal-title">Assign Institution Admin</span>
        <button class="modal-close" onclick="closeModal('assignInstModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" id="assignInstForm" action="">
        @csrf @method('PATCH')
        <div class="modal-body">
          <p style="font-size:0.875rem;color:var(--muted);line-height:1.6;">
            Designate <strong id="assign_inst_name" style="color:var(--text);"></strong>
            as the administrator of an institution.
            Their role will be set to <strong style="color:#fb923c;">Admin</strong> and they will be linked to the chosen institution.
          </p>
          <div class="form-group">
            <label>Institution *</label>
            <select class="form-control" name="institution_id" id="assign_inst_select" required>
              <option value="">— Select Institution —</option>
              @foreach($institutions as $inst)
                <option value="{{ $inst->id }}">{{ $inst->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="notice notice-danger">
            <strong>Scope of authority:</strong> As super admin, you can appoint institution admins here.
            However, <strong>adding regular students/users as members</strong> of an institution is the institution admin's responsibility — not yours.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('assignInstModal')">Cancel</button>
            <button type="submit" class="btn btn-teal">Assign as Inst. Admin</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }

    // Close on backdrop click
    document.querySelectorAll('.modal-overlay').forEach(o => {
      o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });

    // Auto-open create modal on validation errors (not edit)
    @if($errors->any() && old('_method') !== 'PUT')
      openModal('createModal');
    @endif

    const roleNumToStr = { 1: 'student', 2: 'admin', 3: 'super_admin' };
    const roleLabel    = { 1: 'Student', 2: 'Admin', 3: 'Super Admin' };

    // ── EDIT MODAL ────────────────────────────────────────────────────────────
    function openEditModal(id, name, email, roleNum, status) {
      const form = document.getElementById('editForm');
      form.action = `/superadmin/users/${id}`;

      document.getElementById('edit_name').value        = name;
      document.getElementById('edit_email').value       = email;
      document.getElementById('edit_status').value      = status;
      document.getElementById('edit_role_hidden').value = roleNumToStr[roleNum] ?? 'student';

      openModal('editModal');
    }

    // ── PROMOTE MODAL ─────────────────────────────────────────────────────────
    function openPromoteModal(id, name, currentRole) {
      const form = document.getElementById('promoteForm');
      form.action = `/superadmin/users/${id}/promote`;

      document.getElementById('promote_name').textContent = name;

      // Pre-select one step up
      const sel = document.getElementById('promote_role');
      sel.value = currentRole === 1 ? 'admin' : 'super_admin';

      openModal('promoteModal');
    }

    // ── DEMOTE MODAL ──────────────────────────────────────────────────────────
    function openDemoteModal(id, name, currentRole) {
      const form = document.getElementById('demoteForm');
      form.action = `/superadmin/users/${id}/demote`;

      const newRole = currentRole - 1;
      document.getElementById('demote_name').textContent = name;
      document.getElementById('demote_from').textContent = roleLabel[currentRole];
      document.getElementById('demote_to').textContent   = roleLabel[newRole];

      openModal('demoteModal');
    }

    // ── ASSIGN INSTITUTION ADMIN MODAL ────────────────────────────────────────
    function openAssignInstModal(id, name, currentInstId) {
      const form = document.getElementById('assignInstForm');
      form.action = `/superadmin/users/${id}/assign-inst-admin`;

      document.getElementById('assign_inst_name').textContent = name;

      // Pre-select their current institution if they have one
      const sel = document.getElementById('assign_inst_select');
      for (let opt of sel.options) {
        opt.selected = currentInstId !== null && String(opt.value) === String(currentInstId);
      }

      openModal('assignInstModal');
    }
  </script>

</body>
</html>