<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — My Profile</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

  <script>
    window.USER_ORG_ID = @json(auth()->check() ? auth()->user()->organization_id : null);
  </script>

  <style>
    :root {
      --bg:#0d1320; --surface:#111c2d; --surface2:#1a2638; --border:#1e2f47;
      --border-hover:#2c4168; --accent:#3b82f6; --accent-hover:#2563eb;
      --warn:#ef4444; --warn2:#f59e0b; --success:#22c55e;
      --text:#fafafa; --muted:#7f93b0; --dim:#3d5272;
      --radius:8px; --radius-sm:6px; --topbar-h:64px;
    }

    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);margin:0;-webkit-font-smoothing:antialiased}
    .page-layout-wrapper{display:flex;min-height:100vh}
    .page-profile-main{flex:1;display:flex;flex-direction:column;overflow:hidden;position:relative;min-width:0}
    .page-profile-topbar{height:var(--topbar-h);background:var(--surface);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 32px;gap:16px;flex-shrink:0;z-index:10}
    .page-profile-topbar h1{font-size:1.125rem;font-weight:600;color:var(--text);flex:1}
    .page-profile-topbar-btn{width:36px;height:36px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--muted);transition:all .15s;position:relative}
    .page-profile-topbar-btn:hover{color:var(--text);border-color:var(--border-hover)}
    .page-profile-notif-dot{position:absolute;top:-2px;right:-2px;width:8px;height:8px;background:var(--accent);border-radius:50%;border:2px solid var(--bg)}
    .page-profile-content{flex:1;overflow-y:auto;padding:32px;display:flex;flex-direction:column;gap:24px}
    .page-profile-content-inner{max-width:1100px;margin:0 auto;width:100%;display:flex;flex-direction:column;gap:24px}
    .page-profile-header-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);display:flex;flex-direction:column}
    .page-profile-info-section{padding:32px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border)}
    .page-profile-identity{display:flex;align-items:center;gap:24px}
    .page-profile-avatar-large{width:90px;height:90px;border-radius:12px;background:var(--surface2);border:1px solid var(--border-hover);display:flex;align-items:center;justify-content:center;font-size:2.25rem;font-weight:700;color:var(--accent);position:relative;flex-shrink:0}
    .page-profile-avatar-upload{position:absolute;bottom:-8px;right:-8px;width:30px;height:30px;border-radius:50%;background:var(--surface);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--muted);cursor:pointer;transition:all .15s}
    .page-profile-avatar-upload:hover{background:var(--accent);border-color:var(--accent);color:#fff}
    .page-profile-text h2{font-size:1.5rem;font-weight:700;color:var(--text);margin-bottom:4px}
    .page-profile-text p{font-size:.875rem;color:var(--muted)}
    .page-profile-badges{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}
    .page-profile-badge{font-size:.65rem;font-weight:600;padding:4px 8px;border-radius:4px;border:1px solid;letter-spacing:.05em;text-transform:uppercase}
    .page-profile-badge-ds{color:var(--accent);border-color:rgba(59,130,246,.3);background:rgba(59,130,246,.1)}
    .page-profile-badge-rank{color:var(--warn2);border-color:rgba(245,158,11,.3);background:rgba(245,158,11,.1)}
    .page-profile-stats-row{display:flex;gap:32px;padding-left:24px;border-left:1px solid var(--border)}
    .page-profile-stat{display:flex;flex-direction:column;align-items:flex-start}
    .page-profile-stat .val{font-size:1.375rem;font-weight:700;color:var(--text);line-height:1}
    .page-profile-stat .lbl{font-size:.7rem;font-weight:500;color:var(--dim);text-transform:uppercase;letter-spacing:.05em;margin-top:6px}
    .page-profile-tabs{display:flex;gap:32px;padding:0 32px;background:var(--surface);border-radius:0 0 var(--radius) var(--radius)}
    .page-profile-tab{padding:16px 0;font-size:.875rem;font-weight:500;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;transition:all .15s;text-decoration:none}
    .page-profile-tab:hover{color:var(--text)}
    .page-profile-tab.active{color:var(--accent);border-bottom-color:var(--accent)}
    .page-profile-grid{display:grid;grid-template-columns:2fr 1fr;gap:24px}
    .page-profile-grid-single{display:grid;grid-template-columns:1fr;gap:24px}
    .page-profile-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);display:flex;flex-direction:column}
    .page-profile-card-header{padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;gap:16px}
    .page-profile-card-title{font-weight:600;font-size:1rem;color:var(--text)}
    .page-profile-card-subtitle{font-size:.75rem;color:var(--muted);margin-top:4px;line-height:1.5}
    .page-profile-card-body{padding:24px;flex:1}
    .page-profile-form-group{display:flex;flex-direction:column;gap:8px;margin-bottom:20px}
    .page-profile-form-group:last-child{margin-bottom:0}
    .page-profile-form-group label{font-size:.8125rem;font-weight:500;color:var(--muted)}
    .page-profile-form-group input[type="text"],.page-profile-form-group input[type="email"],.page-profile-form-group input[type="password"],.page-profile-form-group textarea,.page-profile-form-group select{background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text);font-size:.875rem;padding:10px 14px;font-family:inherit;outline:none;transition:border-color .15s;width:100%}
    .page-profile-form-group input:focus,.page-profile-form-group textarea:focus,.page-profile-form-group select:focus{border-color:var(--accent)}
    .page-profile-form-group input[readonly]{color:var(--muted);cursor:not-allowed}
    .page-profile-form-group textarea{resize:vertical;min-height:100px}
    .page-profile-help-text{font-size:.75rem;color:var(--muted);line-height:1.5}
    .page-profile-form-actions{display:flex;justify-content:flex-end;gap:12px;margin-top:12px;padding-top:24px;border-top:1px solid var(--border)}
    .page-profile-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:8px 16px;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;cursor:pointer;border:1px solid transparent;transition:all .15s;font-family:inherit;text-decoration:none}
    .page-profile-btn-primary{background:var(--accent);color:#fff;border-color:var(--accent)}
    .page-profile-btn-primary:hover{background:var(--accent-hover);border-color:var(--accent-hover)}
    .page-profile-btn-ghost{background:transparent;color:var(--text);border-color:var(--border)}
    .page-profile-btn-ghost:hover{background:var(--surface2)}
    .page-profile-btn-danger{width:100%;border:1px solid var(--warn);color:var(--warn);background:rgba(239,68,68,.1)}
    .page-profile-btn-danger:hover{background:rgba(239,68,68,.2)}
    .page-profile-danger-note{font-size:.8125rem;color:var(--muted);margin-bottom:16px;line-height:1.6}
    .page-profile-alert{padding:12px 14px;border-radius:var(--radius-sm);font-size:.8125rem;line-height:1.5;margin-bottom:18px;border:1px solid}
    .page-profile-alert-success{background:rgba(34,197,94,.1);color:var(--success);border-color:rgba(34,197,94,.35)}
    .page-profile-alert-danger{background:rgba(239,68,68,.1);color:var(--warn);border-color:rgba(239,68,68,.35)}
    .page-profile-field-error{color:var(--warn);font-size:.75rem;line-height:1.4;margin-top:2px}
    .page-profile-status-pill{display:inline-flex;align-items:center;width:fit-content;padding:5px 10px;border-radius:999px;font-size:.72rem;font-weight:600;letter-spacing:.03em;text-transform:uppercase;border:1px solid var(--border);color:var(--muted);background:var(--bg)}
    .page-profile-status-pending{color:var(--warn2);border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.1)}
    .page-profile-status-approved{color:var(--success);border-color:rgba(34,197,94,.35);background:rgba(34,197,94,.1)}
    .page-profile-status-rejected{color:var(--warn);border-color:rgba(239,68,68,.35);background:rgba(239,68,68,.1)}
    ::-webkit-scrollbar{width:6px;height:6px}
    ::-webkit-scrollbar-track{background:transparent}
    ::-webkit-scrollbar-thumb{background:var(--surface2);border-radius:4px}
    ::-webkit-scrollbar-thumb:hover{background:var(--dim)}
    @media(max-width:900px){.page-profile-grid{grid-template-columns:1fr}.page-profile-info-section{flex-direction:column;align-items:flex-start;gap:32px}.page-profile-stats-row{padding-left:0;border-left:none;width:100%;justify-content:space-between}}
    @media(max-width:700px){.page-profile-content{padding:20px}.page-profile-identity{flex-direction:column;align-items:center;text-align:center;width:100%}.page-profile-badges{justify-content:center}.page-profile-info-section{padding:24px}.page-profile-tabs{padding:0 16px;gap:20px;overflow-x:auto}.page-profile-card-header{align-items:flex-start;flex-direction:column}.page-profile-form-actions{flex-direction:column}.page-profile-btn{width:100%}}
  </style>
</head>

<body>
@php
  $currentUser = $user ?? auth()->user();
  $tab = $activeTab ?? request('tab', 'general');

  if (! in_array($tab, ['general', 'institution', 'security'], true)) {
      $tab = 'general';
  }

  $roleLabel = 'Student';

  if ($currentUser->role == \App\Models\User::ROLE_SUPERADMIN) {
      $roleLabel = 'Super Admin';
  } elseif ($currentUser->role == \App\Models\User::ROLE_ADMIN) {
      $roleLabel = 'Admin';
  } elseif ($currentUser->role == \App\Models\User::ROLE_INSTITUTION_ADMIN) {
      $roleLabel = 'Institution Admin';
  } elseif ($currentUser->role == \App\Models\User::ROLE_INSTRUCTOR) {
      $roleLabel = 'Instructor';
  }

  $canApplyAsInstructor =
      $currentUser->role == \App\Models\User::ROLE_USER &&
      $currentUser->institution_id === null;

  $pendingApplication =
      isset($instructorApplication) &&
      $instructorApplication &&
      $instructorApplication->status === 'pending';

  $approvedApplication =
      isset($instructorApplication) &&
      $instructorApplication &&
      $instructorApplication->status === 'approved';

  $rejectedApplication =
      isset($instructorApplication) &&
      $instructorApplication &&
      $instructorApplication->status === 'rejected';
@endphp

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-profile-main">
    <header class="page-profile-topbar">
      <h1>Account Settings</h1>

      <div class="page-profile-topbar-btn" title="Notifications">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span class="page-profile-notif-dot"></span>
      </div>
    </header>

    <main class="page-profile-content">
      <div class="page-profile-content-inner">

        @if (session('success'))
          <div class="page-profile-alert page-profile-alert-success">
            {{ session('success') }}
          </div>
        @endif

        @if ($errors->has('general'))
          <div class="page-profile-alert page-profile-alert-danger">
            {{ $errors->first('general') }}
          </div>
        @endif

        <div class="page-profile-header-card">
          <div class="page-profile-info-section">
            <div class="page-profile-identity">
              <div class="page-profile-avatar-large">
                {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}

                <div class="page-profile-avatar-upload" title="Change Avatar">
                  <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                  </svg>
                </div>
              </div>

              <div class="page-profile-text">
                <h2>{{ $currentUser->name }}</h2>
                <p>{{ $currentUser->email }}</p>

                <div class="page-profile-badges">
                  <span class="page-profile-badge page-profile-badge-ds">{{ $roleLabel }}</span>
                  <span class="page-profile-badge page-profile-badge-rank">
                    {{ $currentUser->institution_id ? 'Institution Connected' : 'No Institution Yet' }}
                  </span>
                </div>
              </div>
            </div>

            <div class="page-profile-stats-row">
              <div class="page-profile-stat">
                <div class="val">{{ number_format($currentUser->xp ?? 0) }}</div>
                <div class="lbl">Total XP</div>
              </div>

              <div class="page-profile-stat">
                <div class="val">{{ $currentUser->streak ?? 0 }}</div>
                <div class="lbl">Day Streak</div>
              </div>

              <div class="page-profile-stat">
                <div class="val">{{ $currentUser->status ?? 'active' }}</div>
                <div class="lbl">Status</div>
              </div>
            </div>
          </div>

          <div class="page-profile-tabs">
            <a href="{{ route('profile', ['tab' => 'general']) }}"
               class="page-profile-tab {{ $tab === 'general' ? 'active' : '' }}">
              General Details
            </a>

            <a href="{{ route('profile', ['tab' => 'institution']) }}"
               class="page-profile-tab {{ $tab === 'institution' ? 'active' : '' }}">
              Institution
            </a>

            <a href="{{ route('profile', ['tab' => 'security']) }}"
               class="page-profile-tab {{ $tab === 'security' ? 'active' : '' }}">
              Security & Password
            </a>
          </div>
        </div>

        @if ($tab === 'general')
          <div class="page-profile-grid-single">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Personal Information</div>
                  <div class="page-profile-card-subtitle">Update your basic profile details.</div>
                </div>
              </div>

              <div class="page-profile-card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                  @csrf
                  @method('PATCH')

                  <div class="page-profile-form-group">
                    <label>Full Name</label>
                    <input
                      type="text"
                      name="name"
                      value="{{ old('name', $currentUser->name) }}"
                      placeholder="Enter full name"
                      required
                    />

                    @error('name')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="page-profile-form-group">
                    <label>Email Address</label>
                    <input
                      type="email"
                      value="{{ $currentUser->email }}"
                      readonly
                    />
                    <small class="page-profile-help-text">
                      Email changes are disabled for now to protect account identity.
                    </small>
                  </div>

                  <div class="page-profile-form-group">
                    <label>Bio</label>
                    <textarea
                      name="bio"
                      placeholder="Write a short bio about your learning goals..."
                    >{{ old('bio', $currentUser->bio) }}</textarea>

                    @error('bio')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="page-profile-form-actions">
                    <a href="{{ route('profile', ['tab' => 'general']) }}" class="page-profile-btn page-profile-btn-ghost">
                      Cancel
                    </a>

                    <button type="submit" class="page-profile-btn page-profile-btn-primary">
                      Save Changes
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif

        @if ($tab === 'institution')
          <div class="page-profile-grid-single">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Institution</div>
                  <div class="page-profile-card-subtitle">
                    Apply as Instructor using an institution code.
                  </div>
                </div>
              </div>

              <div class="page-profile-card-body">
                @if ($errors->has('institution_application'))
                  <div class="page-profile-alert page-profile-alert-danger">
                    {{ $errors->first('institution_application') }}
                  </div>
                @endif

                @if ($currentUser->institution_id)
                  <p class="page-profile-danger-note">
                    You are already connected to an institution.
                  </p>

                  <div class="page-profile-form-group">
                    <label>Current Role</label>
                    <input type="text" value="{{ $roleLabel }}" readonly>
                  </div>

                  <div class="page-profile-form-group">
                    <label>Institution ID</label>
                    <input type="text" value="{{ $currentUser->institution_id }}" readonly>
                  </div>

                  <span class="page-profile-status-pill page-profile-status-approved">
                    Connected
                  </span>

                @elseif ($pendingApplication)
                  <p class="page-profile-danger-note">
                    Your instructor application is currently pending. Please wait for your institution admin to approve it.
                  </p>

                  <div class="page-profile-form-group">
                    <label>Applying For</label>
                    <input type="text" value="Instructor" readonly>
                  </div>

                  <div class="page-profile-form-group">
                    <label>Institution</label>
                    <input type="text" value="{{ $instructorApplication->institution->name ?? 'Unknown Institution' }}" readonly>
                  </div>

                  <div class="page-profile-form-group">
                    <label>Status</label>
                    <span class="page-profile-status-pill page-profile-status-pending">
                      Pending Approval
                    </span>
                  </div>

                @elseif ($approvedApplication)
                  <p class="page-profile-danger-note">
                    Your instructor application was approved.
                  </p>

                  <div class="page-profile-form-group">
                    <label>Approved Role</label>
                    <input type="text" value="Instructor" readonly>
                  </div>

                  <div class="page-profile-form-group">
                    <label>Institution</label>
                    <input type="text" value="{{ $instructorApplication->institution->name ?? 'Unknown Institution' }}" readonly>
                  </div>

                  <span class="page-profile-status-pill page-profile-status-approved">
                    Approved
                  </span>

                @else
                  @if ($rejectedApplication)
                    <div class="page-profile-alert page-profile-alert-danger">
                      Your previous instructor application was rejected. You may apply again if your institution admin gave you the correct code.
                    </div>
                  @endif

                  <p class="page-profile-danger-note">
                    Choose your institution and enter the institution code provided by your institution admin or school representative.
                    This is only available to regular student accounts with no institution yet. Once approved, your account role will become Instructor under the selected institution.
                  </p>

                  @if ($canApplyAsInstructor)
                    <form action="{{ route('profile.institution.apply') }}" method="POST">
                      @csrf

                      <div class="page-profile-form-group">
                        <label>Applying For</label>
                        <input type="text" value="Instructor" readonly>
                      </div>

                      <div class="page-profile-form-group">
                        <label>Institution</label>
                        <select name="institution_id" required>
                          <option value="">Select institution</option>

                          @foreach ($institutions as $institution)
                            <option
                              value="{{ $institution->id }}"
                              {{ old('institution_id') == $institution->id ? 'selected' : '' }}
                            >
                              {{ $institution->name }}
                            </option>
                          @endforeach
                        </select>

                        @error('institution_id')
                          <div class="page-profile-field-error">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="page-profile-form-group">
                        <label>Institution Code</label>
                        <input
                          type="text"
                          name="institution_code"
                          value="{{ old('institution_code') }}"
                          placeholder="Example: QEREM5"
                          maxlength="6"
                          required
                          style="text-transform: uppercase;"
                        >

                        <small class="page-profile-help-text">
                          Ask your institution admin or school representative for the code.
                        </small>

                        @error('institution_code')
                          <div class="page-profile-field-error">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="page-profile-form-actions">
                        <button type="submit" class="page-profile-btn page-profile-btn-primary">
                          Apply as Instructor
                        </button>
                      </div>
                    </form>
                  @else
                    <div class="page-profile-alert page-profile-alert-danger">
                      This application is only available to regular student accounts with no institution yet.
                    </div>
                  @endif
                @endif
              </div>
            </div>
          </div>
        @endif

        @if ($tab === 'security')
          <div class="page-profile-grid">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Security & Password</div>
                  <div class="page-profile-card-subtitle">Update your account password.</div>
                </div>
              </div>

              <div class="page-profile-card-body">
                <form action="{{ route('profile.password.update') }}" method="POST">
                  @csrf
                  @method('PATCH')

                  <div class="page-profile-form-group">
                    <label>Current Password</label>
                    <input
                      type="password"
                      name="current_password"
                      placeholder="Enter current password"
                      required
                    >

                    @error('current_password')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="page-profile-form-group">
                    <label>New Password</label>
                    <input
                      type="password"
                      name="password"
                      placeholder="Enter new password"
                      required
                    >

                    @error('password')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="page-profile-form-group">
                    <label>Confirm New Password</label>
                    <input
                      type="password"
                      name="password_confirmation"
                      placeholder="Confirm new password"
                      required
                    >
                  </div>

                  <div class="page-profile-form-actions">
                    <button type="submit" class="page-profile-btn page-profile-btn-primary">
                      Update Password
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Danger Zone</div>
                  <div class="page-profile-card-subtitle">Permanent account actions.</div>
                </div>
              </div>

              <div class="page-profile-card-body">
                <p class="page-profile-danger-note">
                  If you delete your account, all your progress, IDE saves, and code submissions will be permanently lost.
                </p>

                <form action="{{ route('profile.delete') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                  @csrf
                  @method('DELETE')

                  <div class="page-profile-form-group">
                    <label>Confirm Password</label>
                    <input
                      type="password"
                      name="delete_password"
                      placeholder="Enter password to delete account"
                      required
                    >

                    @error('delete_password')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <button type="submit" class="page-profile-btn page-profile-btn-danger">
                    Delete Account
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endif

      </div>
    </main>
  </div>
</div>

</body>
</html>
