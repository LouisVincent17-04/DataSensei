@extends('admin.layout')

@section('title', 'User Management')
@section('eyebrow', 'Admin Operations')
@section('page_title', 'User Management')
@section('page_subtitle', 'Manage learners, instructors, and institution admins. Admin and superadmin accounts are intentionally protected from this workspace.')

@section('content')
  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Find Managed Accounts</h2>
        <p class="panel-subtitle">Search by identity and narrow the account list by role, status, or institution.</p>
      </div>
    </div>
    <form class="toolbar" method="GET" action="{{ route('admin.users.index') }}">
      <div class="field">
        <label for="admin-user-search">Search</label>
        <input id="admin-user-search" class="input small" type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email">
      </div>
      <div class="field">
        <label for="admin-user-role">Role</label>
        <select id="admin-user-role" class="select small" name="role">
          <option value="">All roles</option>
          @foreach($roleOptions as $value => $label)
            <option value="{{ $value }}" @selected((string)request('role') === (string)$value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label for="admin-user-status">Status</label>
        <select id="admin-user-status" class="select small" name="status">
          <option value="">All statuses</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="disabled" @selected(request('status') === 'disabled')>Disabled</option>
        </select>
      </div>
      <div class="field">
        <label for="admin-user-institution">Institution</label>
        <select id="admin-user-institution" class="select small" name="institution_id">
          <option value="">All institutions</option>
          @foreach($institutions as $institution)
            <option value="{{ $institution->id }}" @selected((string)request('institution_id') === (string)$institution->id)>{{ $institution->name }}</option>
          @endforeach
        </select>
      </div>
      <button class="btn" type="submit">Filter</button>
      <a class="btn secondary" href="{{ route('admin.users.index') }}">Reset</a>
    </form>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Create Account</h2>
        <p class="panel-subtitle">Create a managed account and assign its access role, institution, and initial status.</p>
      </div>
      <span class="badge info">Protected roles excluded</span>
    </div>
    <div class="panel-body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="form-grid three">
          <div class="field"><label>Name</label><input class="input" name="name" value="{{ old('name') }}" required></div>
          <div class="field"><label>Email</label><input class="input" type="email" name="email" value="{{ old('email') }}" required></div>
          <div class="field"><label>Role</label><select class="select" name="role" required>@foreach($roleOptions as $value => $label)<option value="{{ $value }}" @selected((string)old('role') === (string)$value)>{{ $label }}</option>@endforeach</select></div>
          <div class="field"><label>Status</label><select class="select" name="status"><option value="active">Active</option><option value="disabled">Disabled</option></select></div>
          <div class="field"><label>Institution</label><select class="select" name="institution_id"><option value="">None</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}">{{ $institution->name }}</option>@endforeach</select></div>
          <div class="field"><label>Password</label><input class="input" type="password" name="password" required></div>
          <div class="field"><label>Confirm Password</label><input class="input" type="password" name="password_confirmation" required></div>
        </div>
        <div class="action-row" style="margin-top:16px"><button class="btn" type="submit">Create Account</button></div>
      </form>
    </div>
  </section>

  <section class="panel">
    <div class="panel-head">
      <div class="panel-heading">
        <h2 class="panel-title">Managed Accounts</h2>
        <p class="panel-subtitle">Review account activity and update permitted user details without leaving the page.</p>
      </div>
      <span class="badge info">{{ number_format($users->total()) }} accounts</span>
    </div>
    <div class="table-wrap">
      <table style="min-width:1220px">
        <thead><tr><th>User</th><th>Role / State</th><th>Institution</th><th>Activity</th><th>Edit</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td data-label="User"><strong>{{ $user->name }}</strong><br><span class="dim">{{ $user->email }}</span></td>
              <td data-label="Role / State">{{ $user->role_name }}<br><span class="badge info">{{ $user->learner_state }}</span></td>
              <td data-label="Institution">{{ $user->institution?->name ?? 'None' }}</td>
              <td data-label="Activity"><span class="dim">Classes:</span> {{ $user->classes_as_student_count }}<br><span class="dim">Submissions:</span> {{ $user->assignment_submissions_count }}<br><span class="dim">Achievements:</span> {{ $user->user_achievements_count }}</td>
              <td class="form-cell" data-label="Edit Account">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                  @csrf
                  @method('PUT')
                  <div class="form-grid">
                    <div class="field"><label>Name</label><input class="input" name="name" value="{{ $user->name }}" required></div>
                    <div class="field"><label>Email</label><input class="input" type="email" name="email" value="{{ $user->email }}" required></div>
                    <div class="field"><label>Role</label><select class="select" name="role">@foreach($roleOptions as $value => $label)<option value="{{ $value }}" @selected((int)$user->role === (int)$value)>{{ $label }}</option>@endforeach</select></div>
                    <div class="field"><label>Status</label><select class="select" name="status"><option value="active" @selected($user->status === 'active')>Active</option><option value="disabled" @selected($user->status === 'disabled')>Disabled</option></select></div>
                    <div class="field"><label>Institution</label><select class="select" name="institution_id"><option value="">None</option>@foreach($institutions as $institution)<option value="{{ $institution->id }}" @selected((int)$user->institution_id === (int)$institution->id)>{{ $institution->name }}</option>@endforeach</select></div>
                    <div class="field"><label>New Password</label><input class="input" type="password" name="password" placeholder="Leave blank"></div>
                    <div class="field"><label>Confirm Password</label><input class="input" type="password" name="password_confirmation" placeholder="Leave blank"></div>
                  </div>
                  <div class="action-row" style="margin-top:12px"><button class="btn small" type="submit">Save Changes</button></div>
                </form>
              </td>
              <td data-label="Status">
                <span class="badge {{ $user->status === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($user->status) }}</span>
                <form method="POST" action="{{ route('admin.users.status', $user) }}" style="margin-top:10px">
                  @csrf
                  @method('PATCH')
                  <button class="btn small {{ $user->status === 'active' ? 'danger' : 'green' }}" type="submit">{{ $user->status === 'active' ? 'Disable' : 'Enable' }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td class="empty-cell" colspan="6">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $users->links('vendor.pagination.admin') }}</div>
  </section>
@endsection
