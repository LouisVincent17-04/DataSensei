<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DataSensei — Select Your Path</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    :root {
      --bg:           #0d1320;
      --surface:      #111c2d;
      --surface2:     #1a2638;
      --border:       #1e2f47;
      --border-hover: #2c4168;
      --accent:       #3b82f6;
      --accent-hover: #2563eb;
      --accent-glow:  rgba(59, 130, 246, 0.15);
      --text:         #fafafa;
      --muted:        #7f93b0;
      --dim:          #3d5272;
      --radius:       12px;
      --radius-sm:    6px;
      --sidebar-w:    260px;
      
      /* Alert Colors */
      --success-bg:   rgba(16, 185, 129, 0.1);
      --success-text: #10b981;
      --success-border: rgba(16, 185, 129, 0.2);
      --error-bg:     rgba(239, 68, 68, 0.1);
      --error-text:   #ef4444;
      --error-border: rgba(239, 68, 68, 0.2);
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

    /* ── SIDEBAR ── */
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
    .avatar { width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.875rem; color: var(--text); flex-shrink: 0; }
    .user-info .name  { font-size: 0.875rem; font-weight: 600; color: var(--text); }
    .user-info .role  { font-size: 0.75rem; color: var(--muted); margin-top: 2px; }
    
    .logout-form { width: 100%; }
    .logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 8px 12px; border-radius: var(--radius-sm); background: transparent; border: 1px solid var(--border); color: var(--muted); font-size: 0.875rem; font-weight: 500; font-family: inherit; cursor: pointer; transition: all 0.15s ease; text-align: left; }
    .logout-btn:hover { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.3); color: #ef4444; }

    /* ── MAIN ── */
    .challenge-main {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      position: relative;
      background-image: radial-gradient(circle at 50% 0%, var(--surface2) 0%, var(--bg) 60%);
    }

    /* ── HEADER ── */
    .challenge-hero {
      text-align: center;
      padding: 60px 20px 40px;
      max-width: 700px;
      margin: 0 auto;
    }

    .challenge-hero__title {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--text);
      letter-spacing: -0.03em;
      margin-bottom: 16px;
    }

    .challenge-hero__title span { color: var(--accent); }
    .challenge-hero__subtitle { font-size: 1.05rem; color: var(--muted); line-height: 1.6; }

    /* ── ALERTS ── */
    .alert-container {
      max-width: 700px;
      margin: 0 auto 20px;
      width: 100%;
      padding: 0 20px;
    }
    .alert {
      padding: 12px 16px;
      border-radius: var(--radius-sm);
      font-size: 0.875rem;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); }
    .alert-error   { background: var(--error-bg);   color: var(--error-text);   border: 1px solid var(--error-border); }

    /* ── GRID ── */
    .challenge-grid {
      max-width: 1100px;
      margin: 0 auto 100px;
      padding: 0 32px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
      align-items: stretch;
    }

    /* Position cards 4 & 5 centered under the top row */
    .challenge-grid > :nth-child(4) { grid-column: 1 / span 1; transform: translateX(50%); }
    .challenge-grid > :nth-child(5) { grid-column: 2 / span 1; transform: translateX(50%); }

    /* ── CARD BASE ── */
    .challenge-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 32px 24px;
      text-decoration: none;
      display: flex;
      flex-direction: column;
      /* No transform here — each group gets its own hover rule below */
      transition: border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                  box-shadow   0.3s cubic-bezier(0.4, 0, 0.2, 1),
                  transform    0.3s cubic-bezier(0.4, 0, 0.2, 1),
                  opacity      0.3s ease;
      position: relative;
      overflow: visible;
      z-index: 1;
    }

    .challenge-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      background: radial-gradient(circle at top right, rgba(59, 130, 246, 0.08), transparent 60%);
      opacity: 0;
      transition: opacity 0.3s ease;
      pointer-events: none;
      border-radius: inherit;
    }

    /* Shared hover styles — NO transform here */
    .challenge-card:hover {
      border-color: var(--accent);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4), 0 0 20px var(--accent-glow);
      z-index: 10;
    }

    .challenge-card:hover::before { opacity: 1; }

    /* Cards 1–3: simple lift */
    .challenge-grid > :nth-child(1):hover,
    .challenge-grid > :nth-child(2):hover,
    .challenge-grid > :nth-child(3):hover {
      transform: translateY(-6px);
    }

    /* Cards 4 & 5: lift WHILE keeping the X centering offset */
    .challenge-grid > :nth-child(4):hover,
    .challenge-grid > :nth-child(5):hover {
      transform: translateX(50%) translateY(-6px);
    }

    /* ── ICON ── */
    .challenge-card__icon {
      width: 56px;
      height: 56px;
      background: var(--surface2);
      border: 1px solid var(--border);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 24px;
      transition: all 0.3s ease;
      color: var(--accent);
    }

    .challenge-card:hover .challenge-card__icon {
      background: var(--accent);
      border-color: var(--accent);
      transform: scale(1.05);
      color: #fff;
    }

    /* ── CARD CONTENT ── */
    .challenge-card__title    { font-size: 1.25rem; font-weight: 700; color: var(--text); margin-bottom: 8px; letter-spacing: -0.01em; }
    .challenge-card__audience { font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; display: block; }
    .challenge-card__desc     { font-size: 0.875rem; color: var(--muted); line-height: 1.6; margin-bottom: 24px; flex: 1; }
    .challenge-card__action   { display: flex; align-items: center; gap: 8px; font-size: 0.875rem; font-weight: 600; color: var(--text); margin-top: auto; transition: gap 0.2s, color 0.2s; }
    .challenge-card__action svg { transition: transform 0.2s; flex-shrink: 0; }
    .challenge-card:hover .challenge-card__action     { color: var(--accent); gap: 12px; }
    .challenge-card:hover .challenge-card__action svg { transform: translateX(4px); }

    /* ── LOCKED CARD ── */
    .challenge-card.locked {
      opacity: 0.75;
      cursor: pointer;
      background: rgba(17, 28, 45, 0.5);
    }

    /* Locked cards get a subtle lift — override the full -6px with -2px */
    .challenge-grid > :nth-child(1).locked:hover,
    .challenge-grid > :nth-child(2).locked:hover,
    .challenge-grid > :nth-child(3).locked:hover {
      transform: translateY(-2px);
    }
    .challenge-grid > :nth-child(4).locked:hover,
    .challenge-grid > :nth-child(5).locked:hover {
      transform: translateX(50%) translateY(-2px);
    }

    .challenge-card.locked:hover {
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
      opacity: 1;
    }
    .challenge-card.locked:hover .challenge-card__icon {
      background: var(--surface2);
      border-color: var(--accent);
      transform: none;
      color: var(--accent);
    }

    .lock-overlay {
      position: absolute;
      top: 20px;
      right: 20px;
      color: var(--dim);
      transition: color 0.2s ease;
    }
    .challenge-card.locked:hover .lock-overlay { color: var(--text); }

    /* ── MODAL ── */
    .modal-overlay {
      position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 9999;
      display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);
      opacity: 0; pointer-events: none; transition: opacity 0.2s ease;
    }
    .modal-overlay.active { opacity: 1; pointer-events: auto; }
    .modal-card {
      background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius);
      width: 100%; max-width: 420px; padding: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.5);
      transform: translateY(20px) scale(0.95); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .modal-overlay.active .modal-card { transform: translateY(0) scale(1); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .modal-title  { font-size: 1.25rem; font-weight: 700; color: var(--text); }
    .modal-close  { background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; transition: color 0.2s; display: flex; align-items: center; justify-content: center; border-radius: 4px; }
    .modal-close:hover { color: var(--text); background: var(--surface2); }
    .modal-desc   { font-size: 0.875rem; color: var(--muted); line-height: 1.5; margin-bottom: 20px; }
    .form-group   { margin-bottom: 20px; }
    .form-label   { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--dim); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .form-input   { width: 100%; background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 12px 14px; border-radius: var(--radius-sm); font-family: inherit; font-size: 0.9375rem; outline: none; transition: border-color 0.2s; }
    .form-input:focus { border-color: var(--accent); }
    .form-input::placeholder { color: var(--dim); }
    .modal-submit { width: 100%; background: var(--accent); color: #fff; border: none; padding: 12px; border-radius: var(--radius-sm); font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
    .modal-submit:hover { background: var(--accent-hover); }

    /* ── RESPONSIVE ── */
    @media (max-width: 1100px) {
      .challenge-grid { grid-template-columns: repeat(2, 1fr); }
      /* Reset X centering at smaller viewports */
      .challenge-grid > :nth-child(4),
      .challenge-grid > :nth-child(5) { grid-column: auto; transform: none; }
      /* And restore normal hover lifts */
      .challenge-grid > :nth-child(4):hover,
      .challenge-grid > :nth-child(5):hover { transform: translateY(-6px); }
      .challenge-grid > :nth-child(4).locked:hover,
      .challenge-grid > :nth-child(5).locked:hover { transform: translateY(-2px); }
    }

    @media (max-width: 768px) {
      .sidebar { display: none; }
      .challenge-grid { grid-template-columns: 1fr; padding: 0 16px; }
      .challenge-hero { padding: 48px 20px 32px; }
      .challenge-hero__title { font-size: 1.75rem; }
    }
  </style>
</head>
<body>

  @include('partials.sidebar')

  <div class="challenge-main">

    <div class="challenge-hero">
      <h1 class="challenge-hero__title">Select Your <span>Challenge Path</span></h1>
      <p class="challenge-hero__subtitle">Choose your starting point based on your current experience level to receive a personalized curriculum map.</p>
    </div>

    <div class="alert-container">
      @if(session('success'))
        <div class="alert alert-success">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @error('invite_code')
        <div class="alert alert-error">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ $message }}
        </div>
      @enderror
    </div>

    <div class="challenge-grid">

      @foreach($categories as $cat)
        @php
          $isLocked = ($cat->slug === 'university-student' && !$hasUniversity);
        @endphp

        @if($isLocked)
          <div class="challenge-card locked" onclick="openInviteModal()">

            <div class="lock-overlay">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
            </div>

            <div class="challenge-card__icon" style="color: var(--dim);">
              {!! $cat->icon_svg !!}
            </div>

            <h2 class="challenge-card__title" style="color: var(--muted);">{{ $cat->name }}</h2>
            <span class="challenge-card__audience" style="color: var(--dim);">{{ $cat->target_audience }}</span>
            <p class="challenge-card__desc" style="color: var(--dim);">{{ $cat->description }}</p>

            <div class="challenge-card__action" style="color: var(--dim);">
              Unlock this path
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
              </svg>
            </div>

          </div>
        @else
          <a href="{{ route('challenges.map', $cat->slug) }}" class="challenge-card">
            <div class="challenge-card__icon">
              {!! $cat->icon_svg !!}
            </div>
            <h2 class="challenge-card__title">{{ $cat->name }}</h2>
            <span class="challenge-card__audience">{{ $cat->target_audience }}</span>
            <p class="challenge-card__desc">{{ $cat->description }}</p>
            <div class="challenge-card__action">
              Select Path
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path fill="none" stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
              </svg>
            </div>
          </a>
        @endif
      @endforeach

    </div>

  </div>

  <div class="modal-overlay" id="inviteModal" onclick="closeOnBackdrop(event)">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title">Join Organization</h3>
        <button class="modal-close" onclick="closeInviteModal()">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <p class="modal-desc">This path requires an active university or organization affiliation. Enter the invite code provided by your instructor.</p>
      
      <form action="{{ route('challenges.enroll') }}" method="POST">
        @csrf
        <div class="form-group">
          <label class="form-label" for="invite_code">Invite Code</label>
          <input type="text" id="invite_code" name="invite_code" class="form-input" placeholder="e.g. UCLM-DS-2026" required autocomplete="off">
        </div>
        <button type="submit" class="modal-submit">Verify & Enroll</button>
      </form>
    </div>
  </div>

  <script>
    function openInviteModal() {
      document.getElementById('inviteModal').classList.add('active');
      setTimeout(() => { document.getElementById('invite_code').focus(); }, 100);
    }
    function closeInviteModal() {
      document.getElementById('inviteModal').classList.remove('active');
    }
    function closeOnBackdrop(event) {
      if (event.target.id === 'inviteModal') closeInviteModal();
    }

    @if($errors->has('invite_code'))
      openInviteModal();
    @endif
  </script>

</body>
</html>