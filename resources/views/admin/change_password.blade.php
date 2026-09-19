@extends('admin.layout')

@section('title', 'Change Password')
@section('page_title', 'Change Password')
@section('page_subtitle', 'Update the password used to access the DataSensei admin workspace.')

@section('content')
  <section class="split">
    <article class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Security & Passwords</h2>
          <p class="panel-subtitle">Confirm the current password before setting a new one.</p>
        </div>
      </div>
      <div class="panel-body">
        <form method="POST" action="{{ route('profile.password.update') }}">
          @csrf
          @method('PATCH')

          <div class="field">
            <label>Current Password</label>
            <input class="input" type="password" name="current_password" required autocomplete="current-password" placeholder="Enter current password">
          </div>

          <div class="form-grid" style="margin-top:16px">
            <div class="field">
              <label>New Password</label>
              <input class="input" type="password" name="password" required autocomplete="new-password" placeholder="Enter new password">
            </div>
            <div class="field">
              <label>Confirm New Password</label>
              <input class="input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm new password">
            </div>
          </div>

          <div class="action-row" style="justify-content:flex-end;margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
            <a class="btn secondary" href="{{ url()->previous() }}">Back</a>
            <button class="btn" type="submit">Update Password</button>
          </div>
        </form>
      </div>
    </article>

    <aside class="panel">
      <div class="panel-head">
        <div class="panel-heading">
          <h2 class="panel-title">Password Guidance</h2>
          <p class="panel-subtitle">Use a unique password that is difficult to guess.</p>
        </div>
      </div>
      <div class="panel-body">
        <div style="display:flex;flex-direction:column;gap:16px;color:var(--muted);font-size:.86rem;line-height:1.6">
          <p>Use at least eight characters and combine uppercase letters, lowercase letters, numbers, and symbols.</p>
          <p>Avoid reusing a password from another website or sharing the password with another administrator.</p>
          <p>After the update succeeds, continue using the same admin routes and permissions as before.</p>
        </div>
      </div>
    </aside>
  </section>
@endsection
