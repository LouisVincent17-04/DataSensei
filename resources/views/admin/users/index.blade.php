@extends('admin.layout')

@section('title', 'User Management')
@section('eyebrow', 'Admin Operations')
@section('page_title', 'User Management')
@section('page_subtitle', 'Manage learners, instructors, and institution admins. Admin and superadmin accounts are intentionally protected from this workspace.')

@section('content')
  <section class="panel">
    <form class="toolbar" method="GET" action="{{ route('admin.users.index') }}">
      <input class="input small" type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email">
      <select class="select small" name="role">
        <option value="">All roles</option>
        @foreach($roleOptions as $value => $label)
          <option value="{{ $value }}" @selected((string)request('role') === (string)$value)>{{ $label }}</option>
        @endforeach
      </select>
      <select class="select small" name="status">
        <option value="">All statuses</option>
        <option value="active" @selected(request('status') === 'active')>Active</option>
        <option value="disabled" @selected(request('status') === 'disabled')>Disabled</option>
      </select>
      <select class="select small" name="institution_id">
        <option value="">All institutions</option>
        @foreach($institutions as $institution)
          <option value="{{ $institution->id }}" @selected((string)request('institution_id') === (string)$institution->id)>{{ $institution->name }}</option>
        @endforeach
      </select>
      <button class="btn" type="submit">Filter</button>
      <a class="btn secondary" href="{{ route('admin.users.index') }}">Reset</a>
    </form>
  </section>

  <section class="panel">
    <h2 class="panel-title">Create Account</h2>
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
      <div class="action-row" style="margin-top:14px"><button class="btn" type="submit">Create Account</button></div>
    </form>
  </section>

  <section class="panel">
    <h2 class="panel-title">Managed Accounts</h2>
    <div class="table-wrap">
      <table>
        <thead><tr><th>User</th><th>Role / State</th><th>Institution</th><th>Activity</th><th>Edit</th><th>Status</th></tr></thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td><strong>{{ $user->name }}</strong><br><span class="dim">{{ $user->email }}</span></td>
              <td>{{ $user->role_name }}<br><span class="badge info">{{ $user->learner_state }}</span></td>
              <td>{{ $user->institution?->name ?? 'None' }}</td>
              <td><span class="dim">Classes:</span> {{ $user->classes_as_student_count }}<br><span class="dim">Submissions:</span> {{ $user->assignment_submissions_count }}<br><span class="dim">Achievements:</span> {{ $user->user_achievements_count }}</td>
              <td>
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
                  <div class="action-row" style="margin-top:10px"><button class="btn small" type="submit">Save</button></div>
                </form>
              </td>
              <td>
                <span class="badge {{ $user->status === 'active' ? 'active' : 'disabled' }}">{{ ucfirst($user->status) }}</span>
                <form method="POST" action="{{ route('admin.users.status', $user) }}" style="margin-top:8px">
                  @csrf
                  @method('PATCH')
                  <button class="btn small {{ $user->status === 'active' ? 'danger' : 'green' }}" type="submit">{{ $user->status === 'active' ? 'Disable' : 'Enable' }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="pagination">{{ $users->links() }}</div>
  </section>
@endsection
