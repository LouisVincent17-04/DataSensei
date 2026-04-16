<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>DataSensei — Security & Passwords</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script>
    window.USER_ORG_ID = @json(auth()->check() ? auth()->user()->organization_id : null);
  </script>
  <style>
    :root {
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6; 
      --accent-hover: #2563eb;
      --warn2:        #f59e0b; 
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      --radius:       8px;
      --radius-sm:    6px;
      --topbar-h:     64px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }

    /* Standard Layout Wrapper */
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── NAMESPACED PROFILE COMPONENTS ── */
    .page-profile-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; min-width: 0; }

    /* Topbar */
    .page-profile-topbar { height: var(--topbar-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; z-index: 10; }
    .page-profile-topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; }
    .page-profile-topbar-btn { width: 36px; height: 36px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); transition: all 0.15s; position: relative; }
    .page-profile-topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }
    .page-profile-notif-dot { position: absolute; top: -2px; right: -2px; width: 8px; height: 8px; background: var(--accent); border-radius: 50%; border: 2px solid var(--bg); }

    /* Content Area */
    .page-profile-content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }
    .page-profile-content-inner { max-width: 1100px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; gap: 24px; }

    /* Profile Header Card */
    .page-profile-header-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); display: flex; flex-direction: column; }
    .page-profile-info-section { padding: 32px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
    .page-profile-identity { display: flex; align-items: center; gap: 24px; }
    
    .page-profile-avatar-large { width: 90px; height: 90px; border-radius: 12px; background: var(--surface2); border: 1px solid var(--border-hover); display: flex; align-items: center; justify-content: center; font-size: 2.25rem; font-weight: 700; color: var(--accent); position: relative; flex-shrink: 0; }
    .page-profile-text h2 { font-size: 1.5rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .page-profile-text p { font-size: 0.875rem; color: var(--muted); }
    
    .page-profile-badges { display: flex; gap: 8px; margin-top: 10px; }
    .page-profile-badge { font-size: 0.65rem; font-weight: 600; padding: 4px 8px; border-radius: 4px; border: 1px solid; letter-spacing: 0.05em; text-transform: uppercase; }
    .page-profile-badge-ds { color: var(--accent); border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.1); }
    .page-profile-badge-rank { color: var(--warn2); border-color: rgba(245,158,11,0.3); background: rgba(245,158,11,0.1); }

    .page-profile-stats-row { display: flex; gap: 32px; padding-left: 24px; border-left: 1px solid var(--border); }
    .page-profile-stat { display: flex; flex-direction: column; align-items: flex-start; }
    .page-profile-stat .val { font-size: 1.375rem; font-weight: 700; color: var(--text); line-height: 1; }
    .page-profile-stat .lbl { font-size: 0.7rem; font-weight: 500; color: var(--dim); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 6px; }

    /* Tabs */
    .page-profile-tabs { display: flex; gap: 32px; padding: 0 32px; background: var(--surface); border-radius: 0 0 var(--radius) var(--radius); }
    .page-profile-tab { padding: 16px 0; font-size: 0.875rem; font-weight: 500; color: var(--muted); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.15s; text-decoration: none; }
    .page-profile-tab:hover { color: var(--text); }
    .page-profile-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

    /* Grid & Cards */
    .page-profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    
    .page-profile-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); display: flex; flex-direction: column; }
    .page-profile-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .page-profile-card-title { font-weight: 600; font-size: 1rem; color: var(--text); }
    .page-profile-card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
    .page-profile-card-body { padding: 24px; flex: 1; }

    /* Forms */
    .page-profile-form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
    .page-profile-form-group:last-child { margin-bottom: 0; }
    .page-profile-form-group label { font-size: 0.8125rem; font-weight: 500; color: var(--muted); }
    .page-profile-form-group input[type="password"] { background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius-sm); color: var(--text); font-size: 0.875rem; padding: 10px 14px; font-family: inherit; outline: none; transition: border-color 0.15s; width: 100%; }
    .page-profile-form-group input[type="password"]:focus { border-color: var(--accent); }

    .page-profile-form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px; padding-top: 24px; border-top: 1px solid var(--border); }
    .page-profile-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: inherit; }
    .page-profile-btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
    .page-profile-btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }

    /* Lists */
    .page-profile-req-list { list-style: none; display: flex; flex-direction: column; gap: 12px; margin-top: 12px; }
    .page-profile-req-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.8125rem; color: var(--muted); line-height: 1.5; }
    .page-profile-req-list li svg { color: var(--accent); flex-shrink: 0; margin-top: 2px; }

    @media (max-width: 900px) {
      .page-profile-grid { grid-template-columns: 1fr; }
      .page-profile-info-section { flex-direction: column; align-items: flex-start; gap: 32px; }
      .page-profile-stats-row { padding-left: 0; border-left: none; width: 100%; justify-content: space-between; }
    }
    @media (max-width: 700px) {
      .page-profile-identity { flex-direction: column; align-items: center; text-align: center; width: 100%; }
      .page-profile-badges { justify-content: center; }
      .page-profile-info-section { padding: 24px; }
      .page-profile-tabs { padding: 0 16px; gap: 20px; }
    }
  </style>
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-profile-main">

    <header class="page-profile-topbar">
      <h1>Account Settings</h1>
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
          
          <div style="display:flex; flex-direction:column; gap:24px;">
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

          <div style="display:flex; flex-direction:column; gap:24px;">
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