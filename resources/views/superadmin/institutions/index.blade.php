<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Institution Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:#0d1320; --surface:#111c2d; --surface2:#1a2638; --border:#1e2f47; --border-hover:#2c4168;
      --accent:#3b82f6; --accent-hover:#2563eb; --accent2:#8b5cf6; --accent3:#10b981; --accent4:#f59e0b;
      --warn:#ef4444; --text:#fafafa; --muted:#7f93b0; --dim:#3d5272; --radius:8px; --radius-sm:6px;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; overflow-x: hidden; -webkit-font-smoothing: antialiased; }
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

    .topbar { height: 64px; background: var(--bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; flex: 1; letter-spacing: -0.01em; }
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }

    .flash { padding: 12px 20px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; border-left: 3px solid; }
    .flash-success { background: rgba(16,185,129,0.08); border-color: var(--accent3); color: var(--accent3); }
    .flash-error   { background: rgba(239,68,68,0.08); border-color: var(--warn); color: var(--warn); }

    .toolbar { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .search-box { display: flex; align-items: center; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 14px; gap: 10px; min-width: 260px; flex: 1; max-width: 400px; transition: border-color 0.15s; }
    .search-box:focus-within { border-color: var(--accent); }
    .search-box input { background: none; border: none; outline: none; color: var(--text); font-size: 0.875rem; font-family: inherit; width: 100%; }
    .search-box input::placeholder { color: var(--dim); }
    select.filter { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 14px; color: var(--text); font-size: 0.875rem; font-family: inherit; cursor: pointer; outline: none; }
    select.filter:focus { border-color: var(--accent); }

    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 18px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: inherit; text-decoration: none; }
    .btn-primary  { background: var(--accent); color: #fff; }
    .btn-primary:hover  { background: var(--accent-hover); }
    .btn-ghost    { background: var(--surface); color: var(--text); border-color: var(--border); }
    .btn-ghost:hover    { border-color: var(--border-hover); }
    .btn-danger   { background: rgba(239,68,68,0.1); color: var(--warn); border-color: rgba(239,68,68,0.3); }
    .btn-danger:hover   { background: rgba(239,68,68,0.18); }
    .btn-sm { padding: 5px 12px; font-size: 0.8rem; }

    /* INST GRID */
    .inst-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; }
    .inst-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 22px 24px; display: flex; flex-direction: column; gap: 14px; transition: border-color 0.15s; }
    .inst-card:hover { border-color: var(--border-hover); }
    .inst-card-top { display: flex; align-items: flex-start; gap: 14px; }
    .inst-logo { width: 46px; height: 46px; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
    .inst-logo img { width: 100%; height: 100%; object-fit: cover; }
    .inst-info .inst-name { font-size: 1rem; font-weight: 700; color: var(--text); }
    .inst-info .inst-email { font-size: 0.8rem; color: var(--muted); margin-top: 2px; }
    .inst-stats { display: flex; gap: 16px; }
    .inst-stat { display: flex; flex-direction: column; gap: 2px; }
    .inst-stat-val { font-size: 1.25rem; font-weight: 700; }
    .inst-stat-lbl { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted); }
    .inst-card-footer { display: flex; align-items: center; gap: 8px; padding-top: 12px; border-top: 1px solid var(--border); }
    .inst-meta-item { display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: var(--muted); }

    /* PILLS */
    .pill { display: inline-flex; align-items: center; font-size: 0.7rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; }
    .pill-active   { background: rgba(16,185,129,0.12);  color: var(--accent3); border: 1px solid rgba(16,185,129,0.25); }
    .pill-disabled { background: rgba(239,68,68,0.08);   color: var(--warn);    border: 1px solid rgba(239,68,68,0.2); }

    /* PAGINATION */
    .pagination { display: flex; gap: 6px; justify-content: center; padding: 20px 0; }
    .pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; height: 34px; padding: 0 10px; border-radius: var(--radius-sm); font-size: 0.8rem; font-weight: 500; border: 1px solid var(--border); color: var(--muted); text-decoration: none; transition: all 0.15s; }
    .pagination a:hover { background: var(--surface2); color: var(--text); }
    .pagination .active span { background: var(--accent); color: #fff; border-color: var(--accent); }

    /* MODAL */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(3px); z-index: 1000; display: none; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); width: 100%; max-width: 560px; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 64px rgba(0,0,0,0.5); }
    .modal-header { padding: 24px 28px 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-title { font-size: 1.1rem; font-weight: 700; }
    .modal-close { background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; border-radius: 4px; display: flex; transition: color 0.15s; }
    .modal-close:hover { color: var(--text); }
    .modal-body  { padding: 24px 28px 28px; display: flex; flex-direction: column; gap: 16px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 0.8rem; font-weight: 600; color: var(--muted); }
    .form-control { background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 9px 14px; color: var(--text); font-size: 0.875rem; font-family: inherit; outline: none; transition: border-color 0.15s; width: 100%; }
    .form-control:focus { border-color: var(--accent); }
    textarea.form-control { resize: vertical; min-height: 80px; }
    .form-error { font-size: 0.75rem; color: var(--warn); margin-top: 2px; }
    .modal-footer { display: flex; gap: 10px; justify-content: flex-end; padding-top: 8px; }

    /* DELETE MODAL */
    .delete-modal .modal { max-width: 400px; }
    .delete-icon { width: 52px; height: 52px; border-radius: 50%; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .delete-body { text-align: center; padding: 32px 28px 28px; }
    .delete-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 10px; }
    .delete-desc { font-size: 0.875rem; color: var(--muted); line-height: 1.5; }
    .delete-footer { display: flex; gap: 10px; justify-content: center; padding-top: 20px; }

    /* Empty state */
    .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
    .empty-state svg { opacity: 0.3; margin: 0 auto 16px; display: block; }
    .empty-state h3 { font-size: 1rem; font-weight: 600; color: var(--text); margin-bottom: 6px; }
    .empty-state p { font-size: 0.875rem; }
  </style>
</head>
<body>

  @include('partials.superadmin-sidebar')

  <div class="main">
    <div class="topbar">
      <h1>Institution Management</h1>
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
      <p style="font-size:0.8rem;color:var(--muted);">
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
                  <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="var(--accent2)" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                @endif
              </div>
              <div class="inst-info" style="flex:1;min-width:0;">
                <div class="inst-name">{{ $inst->name }}</div>
                <div class="inst-email">{{ $inst->email }}</div>
                @if($inst->website)
                  <a href="{{ $inst->website }}" target="_blank" style="font-size:0.75rem;color:var(--accent);text-decoration:none;">{{ $inst->website }}</a>
                @endif
              </div>
              <span class="pill {{ $inst->status === 'active' ? 'pill-active' : 'pill-disabled' }}">{{ $inst->status }}</span>
            </div>

            <div class="inst-stats">
              <div class="inst-stat">
                <span class="inst-stat-val" style="color:var(--accent);">{{ number_format($inst->student_count) }}</span>
                <span class="inst-stat-lbl">Students</span>
              </div>
              <div class="inst-stat">
                <span class="inst-stat-val" style="color:var(--accent2);">{{ number_format($inst->admin_count) }}</span>
                <span class="inst-stat-lbl">Admins</span>
              </div>
              <div class="inst-stat" style="margin-left:auto;">
                <span class="inst-stat-val" style="color:var(--muted);font-size:0.875rem;">{{ $inst->created_at->format('M Y') }}</span>
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
              <label>Logo <span style="color:var(--dim)">(optional, max 2MB)</span></label>
              <input class="form-control" type="file" name="logo" accept="image/*" style="padding:6px 14px;">
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
              <label>Replace Logo <span style="color:var(--dim)">(optional)</span></label>
              <input class="form-control" type="file" name="logo" accept="image/*" style="padding:6px 14px;">
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
        <div class="delete-icon">
          <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="var(--warn)" stroke-width="2">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
          </svg>
        </div>
        <div class="delete-title">Delete Institution?</div>
        <div class="delete-desc">
          You are about to permanently delete <strong id="deleteInstName"></strong>.
          <span id="deleteWarning" style="display:none;color:var(--warn);font-weight:600;display:block;margin-top:8px;"></span>
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