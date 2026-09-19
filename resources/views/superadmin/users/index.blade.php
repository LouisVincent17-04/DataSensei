<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management — DataSensei</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    /* User management. Colours, type and radius come from partials.design-system. */
    :root {
      --accent2: var(--ds-accent);
      --accent3: var(--ds-success);
      --accent4: var(--ds-warning);
      --warn:    var(--ds-danger);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--ds-font-sans); background: var(--bg); color: var(--text); min-height: 100vh; display: flex; overflow-x: hidden; }
    .main { flex: 1; min-width: 0; display: flex; flex-direction: column; }

    /* Title bar */
    .topbar { min-height: 60px; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 4px 16px;
      background: var(--bg); border-bottom: 1px solid var(--border); flex-shrink: 0; }

    /* Content */
    .content { flex: 1; padding: 28px 32px 48px; display: flex; flex-direction: column; gap: 20px; }

    /* Flash */
    .flash { padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm); font-size: .875rem; line-height: 1.5; }
    .flash-success { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: #d1fae5; }
    .flash-error   { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: #fee2e2; }

    /* Filters */
    .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .search-box { flex: 1 1 220px; min-width: 0; max-width: 360px; min-height: 38px; padding: 0 12px; display: flex; align-items: center; gap: 8px;
      background: var(--surface3); border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm);
      transition: border-color .12s ease, box-shadow .12s ease; }
    .search-box:focus-within { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .search-box input { width: 100%; min-width: 0; padding: 8px 0; background: none; border: none; outline: none; box-shadow: none;
      color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); }
    .search-box input:focus-visible { box-shadow: none; }
    .search-box input::placeholder { color: var(--dim); }
    select.filter { min-height: 38px; max-width: 100%; padding: 8px 12px; background: var(--surface3); border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); outline: none;
      transition: border-color .12s ease, box-shadow .12s ease; }
    select.filter:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }

    /* Buttons */
    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 38px; padding: 0 16px;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2); color: var(--text);
      font: 500 .875rem/1.2 var(--ds-font-sans); text-decoration: none; white-space: nowrap; cursor: pointer;
      transition: background .12s ease, border-color .12s ease, color .12s ease; }
    .btn:hover { background: var(--ds-surface-hover); }
    .btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    .btn-ghost, .btn-secondary { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
    .btn-ghost:hover, .btn-secondary:hover { background: var(--ds-surface-hover); }
    .btn-danger { background: transparent; border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
    .btn-danger:hover { background: var(--ds-danger-soft); }
    .btn-sm { min-height: 32px; padding: 0 12px; font-size: .8125rem; }

    /* Card */
    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }
    .card-header { padding: 14px 20px; display: flex; justify-content: space-between; align-items: center; gap: 16px; border-bottom: 1px solid var(--border); }
    .card-title { font-size: .9375rem; font-weight: 600; line-height: 1.35; }
    .card-subtitle { margin-top: 2px; color: var(--muted); font-size: .8125rem; }

    /* Table */
    .tbl-wrap { overflow-x: auto; }
    .tbl { width: 100%; border-collapse: collapse; font-variant-numeric: tabular-nums; }
    .tbl-wrap > table.tbl { min-width: 960px; }
    .tbl th { padding: 10px 14px; background: var(--surface3); border-bottom: 1px solid var(--border); color: var(--muted);
      font-size: .75rem; font-weight: 600; text-align: left; white-space: nowrap; }
    .tbl td { padding: 12px 14px; border-bottom: 1px solid var(--border); color: var(--ds-text-secondary); font-size: .875rem; vertical-align: middle; }
    .tbl td:first-child { min-width: 240px; }
    .tbl tr:last-child td { border-bottom: none; }
    .tbl tbody tr:hover td { background: rgba(255, 255, 255, .02); }
    .tbl-actions { display: flex; gap: 6px; flex-wrap: wrap; min-width: 220px; }

    /* Role label (plain text) and status badge */
    .pill { color: var(--ds-text-secondary); font-size: .875rem; white-space: nowrap; }
    .pill-active, .pill-disabled { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid; border-radius: var(--radius-xs);
      font-size: .75rem; font-weight: 600; line-height: 1.4; text-transform: capitalize; }
    .pill-active   { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .pill-disabled { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }

    /* Initials */
    .av { width: 32px; height: 32px; flex: 0 0 32px; display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); background: var(--surface2);
      color: var(--text); font-size: .75rem; font-weight: 600; }

    /* Pagination (vendor/pagination/admin) */
    .pagination { padding: 14px 20px; border-top: 1px solid var(--border); }
    .pagination:not(:has(.admin-pagination)) { display: none; }

    /* Dialogs */
    .modal-overlay { position: fixed; inset: 0; z-index: 1300; /* above the mobile bar and drawer */ display: none; align-items: center; justify-content: center;
      padding: 16px; background: var(--ds-overlay); overflow-y: auto; }
    .modal-overlay.open { display: flex; }
    .modal { width: 100%; max-width: 520px; max-height: calc(100vh - 32px); max-height: calc(100dvh - 32px); overflow-y: auto;
      background: var(--surface); border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg); box-shadow: var(--ds-shadow-lg); }
    .modal-header { padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; border-bottom: 1px solid var(--border); }
    .modal-title { font-size: 1rem; font-weight: 600; line-height: 1.35; }
    .modal-close { width: 32px; height: 32px; flex: 0 0 32px; display: inline-flex; align-items: center; justify-content: center; margin-right: -6px;
      padding: 0; background: none; border: 1px solid transparent; border-radius: var(--radius-sm); color: var(--muted); cursor: pointer;
      transition: background .12s ease, color .12s ease; }
    .modal-close:hover { background: var(--surface2); color: var(--text); }
    .modal-close svg { width: 18px; height: 18px; }
    .modal-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }
    .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
    .form-group label { color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; line-height: 1.35; }
    .form-control { width: 100%; min-height: 38px; padding: 8px 12px; background: var(--surface3); border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); outline: none;
      transition: border-color .12s ease, box-shadow .12s ease; }
    .form-control::placeholder { color: var(--dim); }
    .form-control:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .form-error { color: var(--ds-danger-text); font-size: .75rem; line-height: 1.4; }
    .modal-footer { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end;
      margin: 4px -20px -20px; padding: 16px 20px; border-top: 1px solid var(--border); }

    /* Notes inside dialogs */
    .notice { padding: 12px 16px; background: var(--surface3); border: 1px solid var(--border); border-radius: var(--radius-sm);
      color: var(--ds-text-secondary); font-size: .8125rem; line-height: 1.55; }
    .notice strong { color: var(--text); font-weight: 600; }
    .notice-teal { background: var(--ds-accent-soft); border-color: var(--ds-accent-border); color: #dbeafe; }
    .notice-warn { background: var(--ds-warning-soft); border-color: var(--ds-warning-border); color: #fef3c7; }
    .notice-danger { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: #fee2e2; }
    .notice-teal strong, .notice-warn strong, .notice-danger strong { color: #fff; }

    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 8px 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .topbar { padding: 8px 16px; }
      .content { padding: 20px 16px 32px; gap: 16px; }
      .card-header, .pagination { padding-left: 16px; padding-right: 16px; }
      .search-box { flex: 1 1 100%; max-width: none; }
      select.filter { flex: 1 1 140px; }
      .toolbar > .btn { flex: 1 1 auto; }
      .form-row { grid-template-columns: minmax(0, 1fr); }
      .modal-header { padding: 14px 16px; }
      .modal-body { padding: 16px; }
      .modal-footer { margin: 4px -16px -16px; padding: 14px 16px; }
      .modal-footer .btn { flex: 1 1 auto; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'User Management', 'pageDescription' => 'Manage DataSensei accounts, roles, and access.'])
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">
    <div class="topbar">
      <h1 class="ds-page-title">User Management</h1>
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
            <option value="student"           {{ request('role') === 'student'           ? 'selected' : '' }}>Student</option>
            <option value="instructor"        {{ request('role') === 'instructor'        ? 'selected' : '' }}>Instructor</option>
            <option value="institution_admin" {{ request('role') === 'institution_admin' ? 'selected' : '' }}>Institution Admin</option>
            <option value="admin"             {{ request('role') === 'admin'             ? 'selected' : '' }}>Admin</option>
            <option value="super_admin"       {{ request('role') === 'super_admin'       ? 'selected' : '' }}>Super Admin</option>
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
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div class="av">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    <div>
                      <div style="font-weight:600;color:var(--text);">{{ $user->name }}</div>
                      <div style="font-size:0.75rem;color:var(--muted);overflow-wrap:anywhere;">{{ $user->email }}</div>
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
                <td style="color:var(--muted);font-size:0.8125rem;">
                  {{ $user->institution?->name ?? '—' }}
                </td>
                <td style="color:var(--text);">
                  {{ number_format($user->xp ?? 0) }}
                </td>
                <td>
                  <span class="pill {{ $user->status === 'active' ? 'pill-active' : 'pill-disabled' }}">
                    {{ $user->status }}
                  </span>
                </td>
                <td style="color:var(--muted);font-size:0.8125rem;white-space:nowrap;">
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
                      <button class="btn btn-secondary btn-sm"
                        onclick="openPromoteModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->role }})">
                        Promote
                      </button>
                    @endif

                    {{-- Demote: admins and super admins only, not yourself --}}
                    @if($user->role >= 2 && $user->id !== auth()->id())
                      <button class="btn btn-secondary btn-sm"
                        onclick="openDemoteModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->role }})">
                        Demote
                      </button>
                    @endif

                    {{-- Assign Institution Admin: non-super-admins, not yourself --}}
                    @if(!$user->isSuperAdmin() && $user->id !== auth()->id())
                      <button class="btn btn-secondary btn-sm"
                        onclick="openAssignInstModal({{ $user->id }}, '{{ addslashes($user->name) }}', {{ $user->institution_id ?? 'null' }})">
                        Inst. Admin
                      </button>
                    @endif

                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align:center;color:var(--muted);padding:32px 20px;">
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
              <input class="form-control" type="password" name="password" placeholder="8+ chars, upper/lower, number, symbol" required>
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
          <p style="font-size:0.875rem;color:var(--ds-text-secondary);line-height:1.55;">
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
            <button type="submit" class="btn btn-primary">Confirm Promotion</button>
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
          <p style="font-size:0.875rem;color:var(--ds-text-secondary);line-height:1.55;">
            You are about to demote <strong id="demote_name" style="color:var(--text);"></strong>
            from <strong id="demote_from" style="color:var(--text);"></strong>
            to <strong id="demote_to" style="color:var(--text);"></strong>.
          </p>
          <div class="notice notice-warn">
            <strong>Warning:</strong> Demoting an <strong>Admin → Student</strong> will also remove them from their institution.
            They will lose all institution-admin rights immediately.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('demoteModal')">Cancel</button>
            <button type="submit" class="btn btn-primary">Confirm Demotion</button>
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
          <p style="font-size:0.875rem;color:var(--ds-text-secondary);line-height:1.55;">
            Designate <strong id="assign_inst_name" style="color:var(--text);"></strong>
            as the administrator of an institution.
            Their role will be set to <strong style="color:var(--text);">Admin</strong> and they will be linked to the chosen institution.
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
            <button type="submit" class="btn btn-primary">Assign as Inst. Admin</button>
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
