<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>Security & Passwords — DataSensei</title>
<script>
    window.USER_ORG_ID = @json(auth()->check() ? auth()->user()->organization_id : null);
  </script>
  <style>
    /* Account settings: password change. Colours, type and radius come from
       partials.design-system; the sidebar comes from partials.sidebar-shell. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { margin: 0; background: var(--bg); color: var(--text); font-family: var(--ds-font-sans); }
    .page-layout-wrapper { display: flex; min-height: 100vh; }
    .page-profile-main { flex: 1; min-width: 0; display: flex; flex-direction: column; position: relative; overflow: hidden; }

    /* ── title bar ── */
    .page-profile-topbar { min-height: 60px; flex-shrink: 0; display: flex; align-items: center; gap: 16px; padding: 0 32px; background: var(--bg); border-bottom: 1px solid var(--border); }
    .page-profile-topbar h1 { flex: 1; min-width: 0; }
    .page-profile-topbar-btn { position: relative; width: 36px; height: 36px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--muted); cursor: pointer; transition: background 0.12s ease, color 0.12s ease; }
    .page-profile-topbar-btn:hover { background: var(--ds-surface-hover); color: var(--text); }
    .page-profile-notif-dot { position: absolute; top: -3px; right: -3px; width: 8px; height: 8px; border: 2px solid var(--bg); border-radius: 50%; background: var(--accent); box-sizing: content-box; }

    /* ── content ── */
    .page-profile-content { flex: 1; display: flex; flex-direction: column; gap: 24px; padding: 28px 32px 48px; }
    .page-profile-content-inner { width: 100%; max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px; }

    /* ── account summary: a plain header, not a banner ── */
    .page-profile-header-card { display: flex; flex-direction: column; gap: 24px; }
    .page-profile-info-section { display: flex; flex-wrap: wrap; gap: 12px; }
    .page-profile-identity { flex: 1 1 100%; min-width: 0; display: flex; align-items: center; gap: 16px; margin-bottom: 8px; }
    .page-profile-avatar-large { width: 56px; height: 56px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; background: var(--surface2); border: 1px solid var(--ds-border-strong); border-radius: var(--radius-sm); color: var(--text); font-size: 1.375rem; font-weight: 600; }
    .page-profile-text { min-width: 0; }
    .page-profile-text h2 { margin-bottom: 2px; color: var(--text); font-size: 1.125rem; font-weight: 600; line-height: 1.35; letter-spacing: -0.01em; overflow-wrap: anywhere; }
    .page-profile-text p { color: var(--muted); font-size: 0.875rem; line-height: 1.5; overflow-wrap: anywhere; }

    .page-profile-badges { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; margin-top: 8px; }
    .page-profile-badge { display: inline-flex; align-items: center; padding: 2px 8px; border: 1px solid var(--ds-border-strong); border-radius: var(--radius-xs); background: var(--surface2); color: var(--ds-text-secondary); font-size: 0.75rem; font-weight: 600; line-height: 1.4; white-space: nowrap; }
    /* The role is plain text, not a capsule. */
    .page-profile-badge-ds { padding: 0; border: 0; background: none; color: var(--muted); font-size: 0.8125rem; font-weight: 500; }

    /* Summary tiles */
    .page-profile-stats-row { flex: 2 1 420px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
    .page-profile-stat { min-width: 0; display: flex; flex-direction: column; gap: 4px; padding: 16px 18px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
    .page-profile-stat .lbl { order: -1; color: var(--muted); font-size: 0.8125rem; font-weight: 500; }
    .page-profile-stat .val { margin-top: 2px; color: var(--text); font-size: 1.5rem; font-weight: 700; line-height: 1.2; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; overflow-wrap: break-word; }

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

    /* ── requirement list ── */
    .page-profile-req-list { display: flex; flex-direction: column; gap: 10px; list-style: none; }
    .page-profile-req-list li { display: flex; align-items: flex-start; gap: 8px; color: var(--ds-text-secondary); font-size: 0.875rem; line-height: 1.5; }
    .page-profile-req-list li svg { flex-shrink: 0; margin-top: 3px; color: var(--ds-success-text); }

    @media (max-width: 900px) {
      .page-profile-topbar { min-height: 56px; padding: 0 20px; }
      .page-profile-content { padding: 24px 20px 40px; }
      .page-profile-grid { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width: 640px) {
      .page-profile-topbar { padding: 0 16px; }
      .page-profile-content { padding: 20px 16px 32px; }
      .page-profile-avatar-large { width: 48px; height: 48px; font-size: 1.25rem; }
      .page-profile-tabs { gap: 20px; }
      .page-profile-card-header { padding: 12px 16px; }
      .page-profile-card-body { padding: 16px; }
      .page-profile-form-actions .page-profile-btn { flex: 1 1 auto; }
    }
    /* Phones: the summary figures become rows of one panel instead of tall tiles. */
    @media (max-width: 560px) {
      .page-profile-stats-row { grid-template-columns: minmax(0, 1fr); gap: 0; overflow: hidden; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); }
      .page-profile-stat { flex-direction: row; align-items: baseline; justify-content: space-between; gap: 12px; padding: 12px 16px; background: none; border: 0; border-top: 1px solid var(--border); border-radius: 0; }
      .page-profile-stat:first-child { border-top: 0; }
      .page-profile-stat .val { margin: 0; font-size: 1.125rem; text-align: right; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Security & Passwords', 'pageDescription' => 'Update the password for your DataSensei account.'])
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-profile-main">

    <header class="page-profile-topbar">
      <h1 class="ds-page-title">Account Settings</h1>
      <div class="page-profile-topbar-btn" title="Notifications">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span class="page-profile-notif-dot"></span>
      </div>
    </header>

    <main class="page-profile-content">
      <div class="page-profile-content-inner">

        <div class="page-profile-header-card">
          <div class="page-profile-info-section">
            
            <div class="page-profile-identity">
              <div class="page-profile-avatar-large">
                @if (auth()->check() && auth()->user()->name)
                  {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @else
                  L
                @endif
              </div>
              
              <div class="page-profile-text">
                <h2>
                  @if (auth()->check())
                    {{ auth()->user()->name }}
                  @else
                    Louis Santos
                  @endif
                </h2>
                <p>student@datasensei.ph</p>
                <div class="page-profile-badges">
                  <span class="page-profile-badge page-profile-badge-ds">Data Science Track</span>
                  <span class="page-profile-badge page-profile-badge-rank">Top 10% Overall</span>
                </div>
              </div>
            </div>

            <div class="page-profile-stats-row">
              <div class="page-profile-stat">
                <div class="val">2,485</div>
                <div class="lbl">Total XP</div>
              </div>
              <div class="page-profile-stat">
                <div class="val">142</div>
                <div class="lbl">Solved</div>
              </div>
              <div class="page-profile-stat">
                <div class="val">12</div>
                <div class="lbl">Day Streak</div>
              </div>
            </div>

          </div>

          <div class="page-profile-tabs">
            <a href="{{ route('profile') }}" class="page-profile-tab">General Details</a>
            <div class="page-profile-tab active">Security & Passwords</div>
          </div>
        </div>

        <div class="page-profile-grid">
          
          <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Change Password</div>
                  <div class="page-profile-card-subtitle">Ensure your account is using a long, random password to stay secure.</div>
                </div>
              </div>
              <div class="page-profile-card-body">
                <form action="#" method="POST">
                  @csrf
                  <div class="page-profile-form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter your current password" required />
                  </div>

                  <div class="page-profile-form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Create a new password" required />
                  </div>

                  <div class="page-profile-form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Re-type your new password" required />
                  </div>

                  <div class="page-profile-form-actions">
                    <button type="submit" class="page-profile-btn page-profile-btn-primary">Update Password</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="page-profile-card">
              <div class="page-profile-card-header">
                <div>
                  <div class="page-profile-card-title">Password Requirements</div>
                  <div class="page-profile-card-subtitle">Follow these rules for a strong password.</div>
                </div>
              </div>
              <div class="page-profile-card-body">
                <ul class="page-profile-req-list">
                  <li>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Must be at least 8 characters long.
                  </li>
                  <li>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Must contain at least one uppercase letter.
                  </li>
                  <li>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Must contain at least one number (0-9).
                  </li>
                  <li>
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Must contain at least one special character (!@#$%^&*).
                  </li>
                </ul>
              </div>
            </div>
          </div>

        </div> 
      </div>
    </main>
  </div>
</div>

</body>
</html>