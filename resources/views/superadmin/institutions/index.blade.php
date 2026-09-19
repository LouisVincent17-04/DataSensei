<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Institution Management — DataSensei</title>
<style>
    /* Institution management. Colours, type and radius come from partials.design-system. */
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
    .btn-ghost { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
    .btn-ghost:hover { background: var(--ds-surface-hover); }
    .btn-danger { background: transparent; border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
    .btn-danger:hover { background: var(--ds-danger-soft); }
    .btn-sm { min-height: 32px; padding: 0 12px; font-size: .8125rem; }

    /* Institution cards */
    .inst-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr)); gap: 16px; }
    .inst-card { min-width: 0; padding: 20px; display: flex; flex-direction: column; gap: 16px;
      background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); transition: border-color .12s ease; }
    .inst-card:hover { border-color: var(--border-hover); }
    .inst-card-top { display: flex; align-items: flex-start; gap: 12px; }
    .inst-card-top > .pill { flex-shrink: 0; }
    .inst-logo { width: 40px; height: 40px; flex: 0 0 40px; display: flex; align-items: center; justify-content: center; overflow: hidden;
      background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--muted); }
    .inst-logo img { width: 100%; height: 100%; object-fit: cover; }
    .inst-logo svg { width: 20px; height: 20px; }
    .inst-info .inst-name { color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; overflow-wrap: anywhere; }
    .inst-info .inst-email { margin-top: 2px; color: var(--muted); font-size: .8125rem; overflow-wrap: anywhere; }
    .inst-info a { display: inline-block; margin-top: 2px; text-decoration: none; overflow-wrap: anywhere; }
    .inst-info a:hover { text-decoration: underline; }
    .inst-stats { display: flex; flex-wrap: wrap; align-items: flex-end; gap: 12px 28px; padding: 12px 14px; background: var(--surface3); border-radius: var(--radius-sm); }
    .inst-stat { display: flex; flex-direction: column; gap: 2px; }
    .inst-stat-val { color: var(--text); font-size: 1.125rem; font-weight: 600; line-height: 1.3; font-variant-numeric: tabular-nums; }
    .inst-stat-lbl { color: var(--muted); font-size: .75rem; }
    .inst-card-footer { margin-top: auto; display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding-top: 16px; border-top: 1px solid var(--border); }
    .inst-meta-item { display: flex; align-items: flex-start; gap: 6px; color: var(--muted); font-size: .8125rem; line-height: 1.45; overflow-wrap: anywhere; }
    .inst-meta-item svg { margin-top: 3px; }

    /* Status badge */
    .pill { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs);
      background: var(--surface2); color: var(--ds-text-secondary); font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; text-transform: capitalize; }
    .pill-active   { background: var(--ds-success-soft); border-color: var(--ds-success-border); color: var(--ds-success-text); }
    .pill-disabled { background: var(--ds-danger-soft); border-color: var(--ds-danger-border); color: var(--ds-danger-text); }

    /* Pagination (vendor/pagination/admin) */
    .pagination { padding: 4px 0 0; }
    .pagination:not(:has(.admin-pagination)) { display: none; }

    /* Dialogs */
    .modal-overlay { position: fixed; inset: 0; z-index: 1300; /* above the mobile bar and drawer */ display: none; align-items: center; justify-content: center;
      padding: 16px; background: var(--ds-overlay); overflow-y: auto; }
    .modal-overlay.open { display: flex; }
    .modal { width: 100%; max-width: 560px; max-height: calc(100vh - 32px); max-height: calc(100dvh - 32px); overflow-y: auto;
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
    .form-group label span { font-weight: 400; }
    .form-control { width: 100%; min-height: 38px; padding: 8px 12px; background: var(--surface3); border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm); color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); outline: none;
      transition: border-color .12s ease, box-shadow .12s ease; }
    .form-control::placeholder { color: var(--dim); }
    .form-control:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    input[type="file"].form-control { padding: 6px 8px; color: var(--ds-text-secondary); font-size: .8125rem; }
    input[type="file"].form-control::file-selector-button { margin-right: 10px; min-height: 26px; padding: 0 10px; border: 1px solid var(--ds-border-strong);
      border-radius: var(--radius-xs); background: var(--surface2); color: var(--text); font: 500 .8125rem/1.2 var(--ds-font-sans); cursor: pointer; }
    textarea.form-control { min-height: 96px; resize: vertical; line-height: 1.55; }
    .form-error { color: var(--ds-danger-text); font-size: .75rem; line-height: 1.4; }
    .modal-footer { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end;
      margin: 4px -20px -20px; padding: 16px 20px; border-top: 1px solid var(--border); }

    /* Delete dialog */
    .delete-modal .modal { max-width: 420px; }
    .delete-body { padding: 20px 20px 0; }
    .delete-title { margin-bottom: 8px; font-size: 1rem; font-weight: 600; line-height: 1.35; }
    .delete-desc { color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; }
    .delete-desc strong { color: var(--text); font-weight: 600; }
    .delete-footer { display: flex; flex-wrap: wrap; gap: 8px; justify-content: flex-end; align-items: center;
      margin: 20px -20px 0; padding: 16px 20px; border-top: 1px solid var(--border); }
    .delete-footer .btn-danger { background: var(--ds-danger); border-color: var(--ds-danger); color: #fff; }
    .delete-footer .btn-danger:hover { background: var(--ds-danger-strong); border-color: var(--ds-danger-strong); }

    /* Empty state */
    .empty-state { padding: 32px 20px; text-align: center; color: var(--muted);
      background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .empty-state svg { width: 28px; height: 28px; margin: 0 auto 12px; display: block; color: var(--dim); }
    .empty-state h3 { margin-bottom: 4px; color: var(--text); font-size: .9375rem; font-weight: 600; }
    .empty-state p { font-size: .875rem; }

    @media (max-width: 900px) {
      .topbar { min-height: 56px; padding: 8px 20px; }
      .content { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .topbar { padding: 8px 16px; }
      .content { padding: 20px 16px 32px; gap: 16px; }
      .search-box { flex: 1 1 100%; max-width: none; }
      select.filter { flex: 1 1 140px; }
      .toolbar > .btn { flex: 1 1 auto; }
      .inst-card { padding: 16px; }
      .form-row { grid-template-columns: minmax(0, 1fr); }
      .modal-header { padding: 14px 16px; }
      .modal-body { padding: 16px; }
      .modal-footer { margin: 4px -16px -16px; padding: 14px 16px; }
      .modal-footer .btn { flex: 1 1 auto; }
      .delete-body { padding: 16px 16px 0; }
      .delete-footer { margin: 16px -16px 0; padding: 14px 16px; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Institution Management', 'pageDescription' => 'Manage the institutions using DataSensei.'])
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">
    <div class="topbar">
      <h1 class="ds-page-title">Institution Management</h1>
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
      <form method="GET" action="{{ route('superadmin.institutions.index') }}">
        <div class="toolbar">
          <div class="search-box">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="var(--dim)" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="search" placeholder="Search name or email…" value="{{ request('search') }}">
          </div>
          <select name="status" class="filter" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="disabled" {{ request('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
          </select>
          <button type="submit" class="btn btn-ghost">Filter</button>
          <a href="{{ route('superadmin.institutions.index') }}" class="btn btn-ghost">Reset</a>
          <button type="button" class="btn btn-primary" onclick="openModal('createModal')" style="margin-left:auto;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
            Add Institution
          </button>
        </div>
      </form>

      {{-- Summary line --}}
      <p style="font-size:0.8125rem;color:var(--muted);">
        {{ $institutions->total() }} institution{{ $institutions->total() !== 1 ? 's' : '' }} found
      </p>

      {{-- INSTITUTION GRID --}}
      @if($institutions->isEmpty())
        <div class="empty-state">
          <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          <h3>No institutions found</h3>
          <p>Add your first institution to get started.</p>
        </div>
      @else
        <div class="inst-grid">
          @foreach($institutions as $inst)
          <div class="inst-card">
            <div class="inst-card-top">
              <div class="inst-logo">
                @if($inst->logo_path)
                  <img src="{{ Storage::url($inst->logo_path) }}" alt="{{ $inst->name }}">
                @else
                  <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                @endif
              </div>
              <div class="inst-info" style="flex:1;min-width:0;">
                <div class="inst-name">{{ $inst->name }}</div>
                <div class="inst-email">{{ $inst->email }}</div>
                @if($inst->website)
                  <a href="{{ $inst->website }}" target="_blank" style="font-size:0.8125rem;color:var(--ds-accent-text);">{{ $inst->website }}</a>
                @endif
              </div>
              <span class="pill {{ $inst->status === 'active' ? 'pill-active' : 'pill-disabled' }}">{{ $inst->status }}</span>
            </div>

            <div class="inst-stats">
              <div class="inst-stat">
                <span class="inst-stat-val">{{ number_format($inst->student_count) }}</span>
                <span class="inst-stat-lbl">Students</span>
              </div>
              <div class="inst-stat">
                <span class="inst-stat-val">{{ number_format($inst->admin_count) }}</span>
                <span class="inst-stat-lbl">Admins</span>
              </div>
              <div class="inst-stat" style="margin-left:auto;">
                <span class="inst-stat-val" style="color:var(--ds-text-secondary);font-size:0.875rem;">{{ $inst->created_at->format('M Y') }}</span>
                <span class="inst-stat-lbl">Joined</span>
              </div>
            </div>

            @if($inst->address)
              <div class="inst-meta-item">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                {{ Str::limit($inst->address, 55) }}
              </div>
            @endif

            <div class="inst-card-footer">
              <button class="btn btn-ghost btn-sm"
                onclick="openEditModal(
                  {{ $inst->id }},
                  '{{ addslashes($inst->name) }}',
                  '{{ addslashes($inst->email) }}',
                  '{{ addslashes($inst->address ?? '') }}',
                  '{{ addslashes($inst->contact_number ?? '') }}',
                  '{{ addslashes($inst->website ?? '') }}',
                  '{{ addslashes($inst->notes ?? '') }}',
                  '{{ $inst->status }}'
                )">
                Edit
              </button>

              {{-- Toggle Status --}}
              <form method="POST" action="{{ route('superadmin.institutions.toggleStatus', $inst) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-sm {{ $inst->status === 'active' ? 'btn-danger' : 'btn-ghost' }}"
                  onclick="return confirm('{{ $inst->status === 'active' ? 'Disable' : 'Enable' }} this institution?')">
                  {{ $inst->status === 'active' ? 'Disable' : 'Enable' }}
                </button>
              </form>

              {{-- Delete --}}
              <button class="btn btn-danger btn-sm" style="margin-left:auto;"
                onclick="openDeleteModal({{ $inst->id }}, '{{ addslashes($inst->name) }}', {{ $inst->student_count }})">
                Delete
              </button>
            </div>
          </div>
          @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination">
          {{ $institutions->links() }}
        </div>
      @endif

    </div>{{-- /content --}}
  </div>{{-- /main --}}

  {{-- ══════════════════════════ CREATE MODAL ══════════════════════════ --}}
  <div class="modal-overlay" id="createModal">
    <div class="modal">
      <div class="modal-header">
        <span class="modal-title">Add Institution</span>
        <button class="modal-close" onclick="closeModal('createModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" action="{{ route('superadmin.institutions.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label>Institution Name *</label>
              <input class="form-control" type="text" name="name" placeholder="University of XYZ" required value="{{ old('name') }}">
              @error('name')<span class="form-error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
              <label>Official Email *</label>
              <input class="form-control" type="email" name="email" placeholder="info@unixyz.edu" required value="{{ old('email') }}">
              @error('email')<span class="form-error">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Contact Number</label>
              <input class="form-control" type="text" name="contact_number" placeholder="+63 2 8123 4567" value="{{ old('contact_number') }}">
            </div>
            <div class="form-group">
              <label>Website</label>
              <input class="form-control" type="url" name="website" placeholder="https://unixyz.edu" value="{{ old('website') }}">
            </div>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input class="form-control" type="text" name="address" placeholder="123 Main St, City, Province" value="{{ old('address') }}">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Status *</label>
              <select class="form-control" name="status" required>
                <option value="active"   {{ old('status','active') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="disabled" {{ old('status') === 'disabled' ? 'selected' : '' }}>Disabled</option>
              </select>
            </div>
            <div class="form-group">
              <label>Logo <span style="color:var(--muted)">(optional, max 2MB)</span></label>
              <input class="form-control" type="file" name="logo" accept="image/*" style="padding:6px 8px;">
              @error('logo')<span class="form-error">{{ $message }}</span>@enderror
            </div>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea class="form-control" name="notes" placeholder="Internal notes about this institution…">{{ old('notes') }}</textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('createModal')">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Institution</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════════════════ EDIT MODAL ════════════════════════════ --}}
  <div class="modal-overlay" id="editModal">
    <div class="modal">
      <div class="modal-header">
        <span class="modal-title">Edit Institution</span>
        <button class="modal-close" onclick="closeModal('editModal')">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <form method="POST" id="editInstForm" action="" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="form-row">
            <div class="form-group">
              <label>Institution Name *</label>
              <input class="form-control" type="text" name="name" id="ei_name" required>
            </div>
            <div class="form-group">
              <label>Official Email *</label>
              <input class="form-control" type="email" name="email" id="ei_email" required>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Contact Number</label>
              <input class="form-control" type="text" name="contact_number" id="ei_contact">
            </div>
            <div class="form-group">
              <label>Website</label>
              <input class="form-control" type="url" name="website" id="ei_website">
            </div>
          </div>
          <div class="form-group">
            <label>Address</label>
            <input class="form-control" type="text" name="address" id="ei_address">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>Status *</label>
              <select class="form-control" name="status" id="ei_status" required>
                <option value="active">Active</option>
                <option value="disabled">Disabled</option>
              </select>
            </div>
            <div class="form-group">
              <label>Replace Logo <span style="color:var(--muted)">(optional)</span></label>
              <input class="form-control" type="file" name="logo" accept="image/*" style="padding:6px 8px;">
            </div>
          </div>
          <div class="form-group">
            <label>Notes</label>
            <textarea class="form-control" name="notes" id="ei_notes"></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-ghost" onclick="closeModal('editModal')">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════════════════ DELETE MODAL ══════════════════════════ --}}
  <div class="modal-overlay delete-modal" id="deleteModal">
    <div class="modal" style="max-width:420px;">
      <div class="delete-body">
        <div class="delete-title">Delete Institution?</div>
        <div class="delete-desc">
          You are about to permanently delete <strong id="deleteInstName"></strong>.
          <span id="deleteWarning" style="display:none;color:var(--ds-danger-text);font-weight:600;display:block;margin-top:8px;"></span>
          This action <strong>cannot be undone</strong>. All associated users will be unlinked.
        </div>
        <div class="delete-footer">
          <button class="btn btn-ghost" onclick="closeModal('deleteModal')">Cancel</button>
          <form method="POST" id="deleteInstForm" action="">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    function openModal(id)  { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    document.querySelectorAll('.modal-overlay').forEach(o => {
      o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
    });

    function openEditModal(id, name, email, address, contact, website, notes, status) {
      document.getElementById('editInstForm').action = `/superadmin/institutions/${id}`;
      document.getElementById('ei_name').value    = name;
      document.getElementById('ei_email').value   = email;
      document.getElementById('ei_address').value = address;
      document.getElementById('ei_contact').value = contact;
      document.getElementById('ei_website').value = website;
      document.getElementById('ei_notes').value   = notes;
      document.getElementById('ei_status').value  = status;
      openModal('editModal');
    }

    function openDeleteModal(id, name, studentCount) {
      document.getElementById('deleteInstForm').action = `/superadmin/institutions/${id}`;
      document.getElementById('deleteInstName').textContent = name;
      const warn = document.getElementById('deleteWarning');
      if (studentCount > 0) {
        warn.textContent = `⚠ This institution has ${studentCount} enrolled student(s).`;
        warn.style.display = 'block';
      } else {
        warn.style.display = 'none';
      }
      openModal('deleteModal');
    }
  </script>

</body>
</html>