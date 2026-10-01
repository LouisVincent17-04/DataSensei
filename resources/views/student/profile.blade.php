<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>My Profile — DataSensei</title>

<style>
    /* Profile (all roles). Colours, type and radius come from
       partials.design-system; the sidebar comes from partials.sidebar-shell. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { margin: 0; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .page-layout-wrapper { display: flex; min-height: 100vh; }
    .page-profile-main { flex: 1; min-width: 0; display: flex; flex-direction: column; position: relative; overflow: hidden; }

    /* ── title bar ── */
    .page-profile-topbar { min-height: 60px; flex-shrink: 0; display: flex; align-items: center; gap: 16px; padding: 0 32px; background: var(--bg); border-bottom: 1px solid var(--border); }
    .page-profile-topbar h1 { flex: 1; min-width: 0; }

    /* ── content ── */
    .page-profile-content { flex: 1; display: flex; flex-direction: column; gap: 24px; padding: 28px 32px 48px; }
    .page-profile-content-inner { width: 100%; max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

    /* ── account summary: a plain header, not a banner ── */
    .page-profile-header-card { display: flex; flex-direction: column; gap: 24px; }

    /* Section tabs (underline) */
    .page-profile-tabs { display: flex; gap: 24px; overflow-x: auto; border-bottom: 1px solid var(--border); scrollbar-width: none; }
    .page-profile-tabs::-webkit-scrollbar { display: none; }
    .page-profile-tab { flex-shrink: 0; display: inline-flex; align-items: center; min-height: 40px; margin-bottom: -1px; padding: 0 2px; border-bottom: 2px solid transparent; color: var(--muted); font-size: 0.875rem; font-weight: 500; text-decoration: none; white-space: nowrap; cursor: pointer; transition: color 0.12s ease, border-color 0.12s ease; }
    .page-profile-tab:hover { color: var(--text); }
    .page-profile-tab.active { border-bottom-color: var(--accent); color: var(--text); }

    /* ── cards ── */
    .page-profile-grid { display: grid; grid-template-columns: minmax(0, 2fr) minmax(0, 1fr); gap: 20px; align-items: start; }
    .page-profile-grid-single { display: grid; grid-template-columns: minmax(0, 1fr); gap: 20px; }
    .page-profile-card { min-width: 0; display: flex; flex-direction: column; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .page-profile-card-header { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px 16px; padding: 14px 20px; border-bottom: 1px solid var(--border); }
    .page-profile-card-header > div { min-width: 0; }
    .page-profile-card-title { color: var(--text); font-size: 0.9375rem; font-weight: 600; line-height: 1.35; }
    .page-profile-card-subtitle { margin-top: 2px; color: var(--muted); font-size: 0.8125rem; line-height: 1.5; }
    .page-profile-card-body { flex: 1; padding: 20px; }

    /* ── forms ── */
    .page-profile-form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .page-profile-form-group:last-child { margin-bottom: 0; }
    .page-profile-form-group label { color: var(--ds-text-secondary); font-size: 0.8125rem; font-weight: 500; }
    .page-profile-form-group input[type="text"],
    .page-profile-form-group input[type="email"],
    .page-profile-form-group input[type="password"],
    .page-profile-form-group textarea,
    .page-profile-form-group select {
      width: 100%;
      min-height: 38px;
      padding: 8px 12px;
      background: var(--surface3);
      border: 1px solid var(--ds-input-border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font: 400 0.875rem/1.4 var(--ds-font-sans);
      outline: none;
      transition: border-color 0.12s ease, box-shadow 0.12s ease;
    }
    .page-profile-form-group input::placeholder,
    .page-profile-form-group textarea::placeholder { color: var(--dim); }
    .page-profile-form-group input:focus,
    .page-profile-form-group textarea:focus,
    .page-profile-form-group select:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .page-profile-form-group input[readonly] { color: var(--muted); cursor: not-allowed; }
    .page-profile-form-group input[readonly]:focus { border-color: var(--ds-input-border); box-shadow: none; }
    .page-profile-form-group textarea { min-height: 96px; resize: vertical; line-height: 1.55; }
    .page-profile-help-text { color: var(--muted); font-size: 0.75rem; line-height: 1.5; }
    .page-profile-field-error { color: var(--ds-danger-text); font-size: 0.75rem; line-height: 1.45; }

    .page-profile-form-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }

    /* ── buttons ── */
    .page-profile-btn { min-height: 38px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 16px; border: 1px solid transparent; border-radius: var(--radius-sm); font: 500 0.875rem/1.2 var(--ds-font-sans); text-decoration: none; white-space: nowrap; cursor: pointer; transition: background 0.12s ease, border-color 0.12s ease, color 0.12s ease; }
    .page-profile-btn-primary { background: var(--accent); border-color: var(--accent); color: #fff; }
    .page-profile-btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    .page-profile-btn-ghost { background: var(--surface2); border-color: var(--ds-border-strong); color: var(--text); }
    .page-profile-btn-ghost:hover { background: var(--ds-surface-hover); }
    .page-profile-btn-danger { width: 100%; background: transparent; border-color: var(--ds-danger-border); color: var(--ds-danger-text); }
    .page-profile-btn-danger:hover { background: var(--ds-danger-soft); }

    .page-profile-danger-note { margin-bottom: 16px; color: var(--muted); font-size: 0.875rem; line-height: 1.6; }

    /* ── messages ── */
    .page-profile-alert { margin-bottom: 16px; padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm); font-size: 0.875rem; line-height: 1.5; }
    .page-profile-alert-success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
    .page-profile-alert-danger { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }


    .page-profile-rank-empty { color: var(--muted); font-size: 0.875rem; line-height: 1.6; }

    /* ── account summary (DataSensei Updates 9): plain details, no badges or tiles ── */
    .pf-summary { padding: 18px 20px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); }
    .pf-name { margin: 0; color: var(--text); font-size: 1.125rem; font-weight: 600; line-height: 1.35; overflow-wrap: anywhere; }
    .pf-email { margin: 2px 0 0; color: var(--muted); font-size: 0.875rem; overflow-wrap: anywhere; }
    .pf-facts { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px 20px; margin: 16px 0 0; }
    .pf-facts dt { color: var(--muted); font-size: 0.8125rem; font-weight: 500; }
    .pf-facts dd { margin: 2px 0 0; color: var(--text); font-size: 0.9375rem; font-weight: 500; overflow-wrap: anywhere; }
    .pf-note { margin: 14px 0 0; color: var(--ds-text-secondary); font-size: 0.875rem; }
    .pf-status { margin: 4px 0 0; color: var(--ds-text-secondary); font-size: 0.875rem; }
    .pf-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .pf-table th { padding: 8px 12px; background: var(--surface3); color: var(--muted); font-size: 0.75rem; font-weight: 600; text-align: left; }
    .pf-table td { padding: 9px 12px; border-top: 1px solid var(--border); color: var(--ds-text-secondary); }
    .pf-table tr.is-current td { color: var(--text); font-weight: 600; }
    @media (max-width: 900px) { .pf-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 420px) { .pf-facts { grid-template-columns: minmax(0, 1fr); } }

    @media (max-width: 900px) {
      .page-profile-topbar { min-height: 56px; padding: 0 20px; }
      .page-profile-content { padding: 24px 20px 40px; }
      .page-profile-grid { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 640px) {
      .page-profile-topbar { padding: 0 16px; }
      .page-profile-content { padding: 20px 16px 32px; }
      .page-profile-tabs { gap: 20px; }
      .page-profile-card-header { padding: 12px 16px; }
      .page-profile-card-body { padding: 16px; }
      .page-profile-form-actions .page-profile-btn { flex: 1 1 auto; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'My Profile', 'pageDescription' => 'Your DataSensei account details and learning summary.'])
</head>

<body>
@php
  $currentUser = $user ?? auth()->user();
  $tab = $activeTab ?? request('tab', 'general');

  if (! in_array($tab, ['general', 'institution', 'security'], true)) {
      $tab = 'general';
  }

  $roleLabel = 'Student';

  // Ranks, XP and the streak only mean something for learner accounts. Staff
  // accounts see their account summary in the same place instead.
  $isLearner = (int) $currentUser->role === \App\Models\User::ROLE_USER;

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


  $currentRank = $currentRank ?? null;
  $nextRank = $nextRank ?? null;
  $ranks = $ranks ?? collect();
  $rankProgressPercent = isset($rankProgressPercent) ? max(0, min(100, (float) $rankProgressPercent)) : 0;
  $currentRankPosition = $currentRankPosition ?? 1;
  $totalRanks = $totalRanks ?? max(1, $ranks->count());
  $currentRankTier = (int) ($currentRank->rank_id ?? 1);
  $rankTierClass = 'rank-tier-' . max(1, min(8, $currentRankTier));
  $rankName = $currentRank->rank_name ?? 'Unranked';
  $rankExpRequired = (int) ($currentRank->exp_required ?? 0);
  $rankXpToNext = $rankXpToNext ?? 0;
@endphp

<div class="page-layout-wrapper">
  @include('partials.role-sidebar')

  <div class="page-profile-main">
    <header class="page-profile-topbar">
      <h1 class="ds-page-title">My Profile</h1>
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
        <section class="pf-summary" aria-label="Account summary">
          <h2 class="pf-name">{{ $currentUser->name }}</h2>
          <p class="pf-email">{{ $currentUser->email }}</p>
          <dl class="pf-facts">
            <div><dt>Role</dt><dd>{{ $roleLabel }}</dd></div>
            <div><dt>Institution</dt><dd>{{ $currentUser->institution?->name ?? 'None' }}</dd></div>
            @if ($isLearner)
              <div><dt>Total XP</dt><dd>{{ number_format($currentUser->xp ?? 0) }}</dd></div>
              <div><dt>Rank</dt><dd>{{ $rankName }}</dd></div>
              <div><dt>Day streak</dt><dd>{{ $currentUser->streak ?? 0 }}</dd></div>
            @endif
            <div><dt>Member since</dt><dd>{{ optional($currentUser->created_at)->format('M Y') ?? 'Not recorded' }}</dd></div>
            <div><dt>Account status</dt><dd>{{ ucfirst($currentUser->status ?? 'active') }}</dd></div>
          </dl>
          @if ($isLearner)
            <p class="pf-note">
              @if ($nextRank)
                {{ number_format($rankXpToNext) }} more XP to reach the {{ $nextRank->rank_name }} rank.
              @else
                You have reached the highest rank.
              @endif
            </p>
          @endif
        </section>

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
                      placeholder="{{ $isLearner ? 'Write a short bio about your learning goals...' : 'Write a short bio your colleagues will see...' }}"
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

            @if ($isLearner)
            <div class="page-profile-card page-profile-rank-path-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Rank Progression</div>
                  <div class="page-profile-card-subtitle">Your total XP determines the highest rank you currently qualify for.</div>
                </div>
              </div>

              <div class="page-profile-card-body">
                @if ($ranks->isNotEmpty())
                  <table class="pf-table">
                    <thead><tr><th>Rank</th><th>XP required</th><th>Status</th></tr></thead>
                    <tbody>
                      @foreach ($ranks as $rank)
                        @php
                          $isUnlockedRank = (int) ($currentUser->xp ?? 0) >= (int) $rank->exp_required;
                          $isCurrentRank = $currentRank && (int) $currentRank->rank_id === (int) $rank->rank_id;
                        @endphp
                        <tr class="{{ $isCurrentRank ? 'is-current' : '' }}">
                          <td>{{ $rank->rank_name }}</td>
                          <td>{{ number_format($rank->exp_required) }}</td>
                          <td>{{ $isCurrentRank ? 'Your current rank' : ($isUnlockedRank ? 'Reached' : 'Not yet') }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                @else
                  <p class="page-profile-rank-empty">
                    No ranks found yet. Run the ranks migration first, then reload the profile page.
                  </p>
                @endif
              </div>
            </div>
            @endif

          </div>
        @endif

        @if ($tab === 'institution')
          <div class="page-profile-grid-single">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Institution</div>
                  <div class="page-profile-card-subtitle">
                    @if ($canApplyAsInstructor)
                      Apply as Instructor using an institution code.
                    @else
                      The institution this account belongs to.
                    @endif
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
                    <label>Institution</label>
                    {{-- This field used to print the raw institution_id, which
                         told the account holder nothing. --}}
                    <input type="text" value="{{ $currentUser->institution?->name ?? 'Unknown Institution' }}" readonly>
                  </div>

                  <p class="pf-status">Status: connected.</p>

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

                  <p class="pf-status">Status: waiting for approval.</p>

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

                  <p class="pf-status">Status: approved.</p>

                @else
                  @if ($rejectedApplication)
                    <div class="page-profile-alert page-profile-alert-danger">
                      Your previous instructor application was rejected. You may apply again if your institution admin gave you the correct code.
                    </div>
                  @endif

                  @if ($canApplyAsInstructor)
                    <p class="page-profile-danger-note">
                      Choose your institution and enter the institution code provided by your institution admin or school representative.
                      This is only available to regular student accounts with no institution yet. Once approved, your account role will become Instructor under the selected institution.
                    </p>
                  @else
                    <p class="page-profile-danger-note">
                      This account is not linked to an institution. A {{ strtolower($roleLabel) }} account works across DataSensei without one.
                    </p>
                  @endif

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

                    <div class="page-profile-help-text">
                      Use at least {{ config('password_otp.password_min_length', 8) }} characters with uppercase and lowercase letters, a number, and a symbol.
                    </div>
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
                  <div class="page-profile-card-title">Deactivate Account</div>
                  <div class="page-profile-card-subtitle">Disable access while preserving your academic records.</div>
                </div>
              </div>

              <div class="page-profile-card-body">
                <p class="page-profile-danger-note">
                  Deactivation signs you out and blocks future login. Your grades, submissions, progress, and class history are kept for academic record integrity. An administrator can restore access when appropriate.
                </p>

                <form action="{{ route('profile.delete') }}" method="POST" onsubmit="return confirm('Deactivate your account and sign out now? Your academic history will be preserved.');">
                  @csrf
                  @method('DELETE')

                  <div class="page-profile-form-group">
                    <label>Confirm Password</label>
                    <input
                      type="password"
                      name="delete_password"
                      placeholder="Enter password to deactivate account"
                      required
                    >

                    @error('delete_password')
                      <div class="page-profile-field-error">{{ $message }}</div>
                    @enderror
                  </div>

                  <button type="submit" class="page-profile-btn page-profile-btn-danger">
                    Deactivate Account
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
