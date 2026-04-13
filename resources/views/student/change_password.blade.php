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
      /* DataSensei Original Core Palette */
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      
      /* Gamification Accents */
      --accent:       #3b82f6; 
      --accent-hover: #2563eb;
      --accent2:      #8b5cf6; 
      --accent3:      #10b981; 
      --warn:         #ef4444; 
      --warn2:        #f59e0b; 
      
      /* Typography */
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      
      /* Geometry */
      --radius:       8px;
      --radius-sm:    6px;
      --topbar-h:     64px;
      --sidebar-w:    260px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* ── SIDEBAR STYLES ── */
    .sidebar { width: var(--sidebar-w); background: var(--surface); border-right: 1px solid var(--border); display: flex; flex-direction: column; flex-shrink: 0; height: 100vh; position: sticky; top: 0; }
    .sidebar-logo { padding: 24px; border-bottom: 1px solid var(--border); }
    .sidebar-logo .wordmark { font-weight: 700; font-size: 1.25rem; color: var(--text); display: flex; align-items: center; gap: 8px; }
    .sidebar-logo .wordmark span { color: var(--accent); }
    .sidebar-logo .tagline { font-size: 0.75rem; color: var(--muted); margin-top: 4px; font-weight: 500; }
    .nav-group { padding: 24px 16px 0; }
    .nav-label { font-size: 0.75rem; font-weight: 600; color: var(--dim); letter-spacing: 0.05em; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; }
    .nav-item { display: flex; align-items: center; gap: 12px; padding: 8px 12px; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem; font-weight: 500; color: var(--muted); text-decoration: none; margin-bottom: 2px; transition: all 0.15s; }
    .nav-item:hover { background: var(--surface2); color: var(--text); }
    .nav-item.active { background: var(--surface2); color: var(--text); border-left: 3px solid var(--accent); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
    .nav-item .icon { width: 18px; height: 18px; color: inherit; }
    .sidebar-footer { margin-top: auto; padding: 16px; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: 8px; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: var(--radius-sm); cursor: pointer; transition: background 0.15s; }
    .user-card:hover { background: var(--surface2); }
    .user-card .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; }
    .user-card .name { font-size: 0.875rem; font-weight: 600; }
    .user-card .role { font-size: 0.75rem; color: var(--muted); }
    
    .logout-form { width: 100%; }
    .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.15s ease; text-align: left; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: var(--warn); }
    
    .badge { margin-left: auto; background: var(--surface2); border: 1px solid var(--border); color: var(--text); font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 12px; }

    /* ── MAIN APP AREA ── */
    .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; position: relative; }

    /* ── TOPBAR ── */
    .topbar { height: var(--topbar-h); background: var(--surface); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; gap: 16px; flex-shrink: 0; z-index: 10; }
    .topbar h1 { font-size: 1.125rem; font-weight: 600; color: var(--text); flex: 1; }
    
    .topbar-btn { width: 36px; height: 36px; background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--muted); transition: all 0.15s; position: relative; }
    .topbar-btn:hover { color: var(--text); border-color: var(--border-hover); }

    /* ── CONTENT AREA ── */
    .content { flex: 1; overflow-y: auto; padding: 32px; display: flex; flex-direction: column; gap: 24px; }
    .content-inner { max-width: 1100px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; gap: 24px; }

    /* ── PROFILE HEADER CARD ── */
    .profile-header-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      display: flex;
      flex-direction: column;
    }

    .profile-info-section {
      padding: 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--border);
    }

    .profile-identity {
      display: flex;
      align-items: center;
      gap: 24px;
    }

    .profile-avatar-large {
      width: 90px; height: 90px;
      border-radius: 12px;
      background: var(--surface2);
      border: 1px solid var(--border-hover);
      display: flex; align-items: center; justify-content: center;
      font-size: 2.25rem; font-weight: 700; color: var(--accent);
      position: relative;
      flex-shrink: 0;
    }

    .profile-text h2 { font-size: 1.5rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .profile-text p { font-size: 0.875rem; color: var(--muted); }
    
    .profile-badges { display: flex; gap: 8px; margin-top: 10px; }
    .profile-badge { font-size: 0.65rem; font-weight: 600; padding: 4px 8px; border-radius: 4px; border: 1px solid; letter-spacing: 0.05em; text-transform: uppercase; }
    .badge-ds { color: var(--accent); border-color: rgba(59,130,246,0.3); background: rgba(59,130,246,0.1); }
    .badge-rank { color: var(--warn2); border-color: rgba(245,158,11,0.3); background: rgba(245,158,11,0.1); }

    .profile-stats-row { display: flex; gap: 32px; padding-left: 24px; border-left: 1px solid var(--border); }
    .p-stat { display: flex; flex-direction: column; align-items: flex-start; }
    .p-stat .val { font-size: 1.375rem; font-weight: 700; color: var(--text); line-height: 1; }
    .p-stat .lbl { font-size: 0.7rem; font-weight: 500; color: var(--dim); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 6px; }

    /* ── TABS ── */
    .profile-tabs {
      display: flex;
      gap: 32px;
      padding: 0 32px;
      background: var(--surface);
      border-radius: 0 0 var(--radius) var(--radius);
    }
    .p-tab {
      padding: 16px 0;
      font-size: 0.875rem;
      font-weight: 500;
      color: var(--muted);
      cursor: pointer;
      border-bottom: 2px solid transparent;
      transition: all 0.15s;
      text-decoration: none;
    }
    .p-tab:hover { color: var(--text); }
    .p-tab.active { color: var(--accent); border-bottom-color: var(--accent); }

    /* ── PROFILE LAYOUT GRID ── */
    .profile-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 24px;
    }

    /* ── CARDS ── */
    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      display: flex; flex-direction: column;
    }
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .card-title { font-weight: 600; font-size: 1rem; color: var(--text); }
    .card-subtitle { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
    .card-body { padding: 24px; flex: 1; }

    /* ── FORMS ── */
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
    .form-group:last-child { margin-bottom: 0; }
    
    label { font-size: 0.8125rem; font-weight: 500; color: var(--muted); }
    
    input[type="password"] {
      background: var(--bg);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      color: var(--text);
      font-size: 0.875rem;
      padding: 10px 14px;
      font-family: inherit;
      outline: none;
      transition: border-color 0.15s;
      width: 100%;
    }
    input[type="password"]:focus { border-color: var(--accent); }

    .form-actions {
      display: flex; justify-content: flex-end; gap: 12px;
      margin-top: 12px; padding-top: 24px; border-top: 1px solid var(--border);
    }

    .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 8px 16px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; cursor: pointer; border: 1px solid transparent; transition: all 0.15s; font-family: inherit; }
    .btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
    .btn-primary:hover { background: var(--accent-hover); border-color: var(--accent-hover); }
    .btn-ghost { background: transparent; color: var(--text); border-color: var(--border); }
    .btn-ghost:hover { background: var(--surface2); }

    /* ── LIST STYLES ── */
    .req-list { list-style: none; display: flex; flex-direction: column; gap: 12px; margin-top: 12px; }
    .req-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 0.8125rem; color: var(--muted); line-height: 1.5; }
    .req-list li svg { color: var(--accent); flex-shrink: 0; margin-top: 2px; }

    /* ── RESPONSIVE ── */
    @media (max-width: 900px) {
      .profile-grid { grid-template-columns: 1fr; }
      .profile-info-section { flex-direction: column; align-items: flex-start; gap: 32px; }
      .profile-stats-row { padding-left: 0; border-left: none; width: 100%; justify-content: space-between; }
    }
    @media (max-width: 700px) {
      .sidebar { display: none; }
      .profile-identity { flex-direction: column; align-items: center; text-align: center; width: 100%; }
      .profile-badges { justify-content: center; }
      .profile-info-section { padding: 24px; }
      .profile-tabs { padding: 0 16px; gap: 20px; }
    }
  </style>
</head>
<body>

  @include('partials.sidebar')

  <div class="main">

    <header class="topbar">
      <h1>Account Settings</h1>
      
      <div class="topbar-btn" title="Notifications">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span class="notif-dot"></span>
      </div>
    </header>

    <main class="content">
      <div class="content-inner">

        <div class="profile-header-card">
          <div class="profile-info-section">
            
            <div class="profile-identity">
              <div class="profile-avatar-large">
                @if (auth()->check() && auth()->user()->name)
                  {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @else
                  L
                @endif
              </div>
              
              <div class="profile-text">
                <h2>
                  @if (auth()->check())
                    {{ auth()->user()->name }}
                  @else
                    Louis Santos
                  @endif
                </h2>
                <p>student@datasensei.ph</p>
                <div class="profile-badges">
                  <span class="profile-badge badge-ds">Data Science Track</span>
                  <span class="profile-badge badge-rank">Top 10% Overall</span>
                </div>
              </div>
            </div>

            <div class="profile-stats-row">
              <div class="p-stat">
                <div class="val">2,485</div>
                <div class="lbl">Total XP</div>
              </div>
              <div class="p-stat">
                <div class="val">142</div>
                <div class="lbl">Solved</div>
              </div>
              <div class="p-stat">
                <div class="val">12</div>
                <div class="lbl">Day Streak</div>
              </div>
            </div>

          </div>

          <div class="profile-tabs">
            <a href="{{ route('profile') }}" class="p-tab">General Details</a>
            <div class="p-tab active">Security & Passwords</div>
          </div>
        </div>

        <div class="profile-grid">
          
          <div style="display:flex; flex-direction:column; gap:24px;">
            
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Change Password</div>
                  <div class="card-subtitle">Ensure your account is using a long, random password to stay secure.</div>
                </div>
              </div>
              <div class="card-body">
                <form action="#" method="POST">
                  @csrf
                  <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Enter your current password" required />
                  </div>

                  <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Create a new password" required />
                  </div>

                  <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="password_confirmation" placeholder="Re-type your new password" required />
                  </div>

                  <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                  </div>
                </form>
              </div>
            </div>

          </div>

          <div style="display:flex; flex-direction:column; gap:24px;">
            
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Password Requirements</div>
                  <div class="card-subtitle">Follow these rules for a strong password.</div>
                </div>
              </div>
              <div class="card-body">
                <ul class="req-list">
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

        </div> </div>
    </main>
  </div>

</body>
</html>