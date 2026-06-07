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
      
      --success-bg:   rgba(16, 185, 129, 0.1);
      --success-text: #10b981;
      --success-border: rgba(16, 185, 129, 0.2);
      --error-bg:     rgba(239, 68, 68, 0.1);
      --error-text:   #ef4444;
      --error-border: rgba(239, 68, 68, 0.2);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* Standardized Layout */
    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg);
      color: var(--text);
      margin: 0;
      -webkit-font-smoothing: antialiased;
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; }

    /* ── NAMESPACED MAIN AREA ── */
    .page-challenges-main { 
      flex: 1; display: flex; flex-direction: column; min-width: 0; position: relative;
      background-image: radial-gradient(circle at 50% 0%, var(--surface2) 0%, var(--bg) 60%);
    }

    /* ── HERO ── */
    .page-challenges-hero { text-align: center; padding: 60px 20px 40px; max-width: 700px; margin: 0 auto; }
    .page-challenges-hero-title { font-size: 2.5rem; font-weight: 700; color: var(--text); letter-spacing: -0.03em; margin-bottom: 16px; }
    .page-challenges-hero-title span { color: var(--accent); }
    .page-challenges-hero-subtitle { font-size: 1.05rem; color: var(--muted); line-height: 1.6; }

    .page-challenges-institution-note {
      max-width: 760px;
      margin: 0 auto 24px;
      padding: 0 20px;
      color: var(--muted);
      font-size: .875rem;
      line-height: 1.6;
      text-align: center;
    }
    .page-challenges-institution-note strong {
      color: var(--accent);
    }

    /* ── ALERTS ── */
    .page-challenges-alert-container { max-width: 700px; margin: 0 auto 20px; width: 100%; padding: 0 20px; }
    .page-challenges-alert { padding: 12px 16px; border-radius: var(--radius-sm); font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 10px; }
    .page-challenges-alert-success { background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); }
    .page-challenges-alert-error   { background: var(--error-bg);   color: var(--error-text);   border: 1px solid var(--error-border); }

    /* ── GRID ── */
    .page-challenges-grid { max-width: 1100px; margin: 0 auto 100px; padding: 0 32px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; align-items: stretch; }
    .page-challenges-grid > :nth-child(4) { grid-column: 1 / span 1; transform: translateX(50%); }
    .page-challenges-grid > :nth-child(5) { grid-column: 2 / span 1; transform: translateX(50%); }

    /* ── CARD ── */
    .page-challenges-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 32px 24px; text-decoration: none; display: flex; flex-direction: column; transition: border-color 0.3s, box-shadow 0.3s, transform 0.3s; position: relative; overflow: visible; z-index: 1; }
    .page-challenges-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: radial-gradient(circle at top right, rgba(59,130,246,0.08), transparent 60%); opacity: 0; transition: opacity 0.3s; pointer-events: none; border-radius: inherit; }
    .page-challenges-card:hover { border-color: var(--accent); box-shadow: 0 12px 30px rgba(0,0,0,0.4), 0 0 20px var(--accent-glow); z-index: 10; }
    .page-challenges-card:hover::before { opacity: 1; }
    
    .page-challenges-grid > :nth-child(1):hover,
    .page-challenges-grid > :nth-child(2):hover,
    .page-challenges-grid > :nth-child(3):hover { transform: translateY(-6px); }
    .page-challenges-grid > :nth-child(4):hover,
    .page-challenges-grid > :nth-child(5):hover { transform: translateX(50%) translateY(-6px); }

    .page-challenges-card-icon { width: 48px; height: 48px; border-radius: 10px; background: var(--surface2); border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; color: var(--accent); margin-bottom: 20px; transition: all 0.3s; }
    .page-challenges-card:hover .page-challenges-card-icon { background: rgba(59,130,246,0.12); border-color: var(--accent); transform: scale(1.1); }
    .page-challenges-card-title { font-size: 1.125rem; font-weight: 700; color: var(--text); margin-bottom: 6px; }
    .page-challenges-card-audience { font-size: 0.75rem; font-weight: 600; color: var(--accent); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 12px; display: block; }
    .page-challenges-card-desc { font-size: 0.875rem; color: var(--muted); line-height: 1.6; flex: 1; margin-bottom: 24px; }
    .page-challenges-card-action { display: flex; align-items: center; gap: 8px; color: var(--accent); font-size: 0.875rem; font-weight: 600; margin-top: auto; transition: gap 0.2s; }
    .page-challenges-card:hover .page-challenges-card-action { gap: 12px; }

    /* ── LOCKED STATE ── */
    .page-challenges-card.is-locked { cursor: pointer; }
    .page-challenges-card.is-locked .page-challenges-card-icon { color: var(--dim); }
    .page-challenges-card.is-locked .page-challenges-card-title { color: var(--muted); }
    .page-challenges-card.is-locked .page-challenges-card-audience { color: var(--dim); }
    .page-challenges-card.is-locked .page-challenges-card-desc { color: var(--dim); }
    .page-challenges-card.is-locked .page-challenges-card-action { color: var(--dim); }
    
    .page-challenges-lock-overlay { position: absolute; top: 20px; right: 20px; color: var(--dim); transition: color 0.2s; }
    .page-challenges-card.is-locked:hover .page-challenges-lock-overlay { color: var(--text); }
    .page-challenges-card.is-bonus { border-color: rgba(245,158,11,0.55); box-shadow: 0 0 26px rgba(245,158,11,0.08); }
    .page-challenges-card-badge { position:absolute; top:16px; right:16px; padding:5px 9px; border-radius:999px; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.32); color:#fbbf24; font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.05em; }
    .page-challenges-lock-reason { display:block; margin-top: 10px; color: var(--dim); font-size: .76rem; line-height: 1.45; }

    /* ── MODAL ── */
    .page-challenges-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 9999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px); opacity: 0; pointer-events: none; transition: opacity 0.2s; }
    .page-challenges-modal-overlay.is-active { opacity: 1; pointer-events: auto; }
    .page-challenges-modal-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); width: 100%; max-width: 420px; padding: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); transform: translateY(20px) scale(0.95); transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
    .page-challenges-modal-overlay.is-active .page-challenges-modal-card { transform: translateY(0) scale(1); }
    
    .page-challenges-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .page-challenges-modal-title { font-size: 1.25rem; font-weight: 700; color: var(--text); }
    .page-challenges-modal-close { background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; transition: color 0.2s; display: flex; align-items: center; border-radius: 4px; }
    .page-challenges-modal-close:hover { color: var(--text); background: var(--surface2); }
    
    .page-challenges-modal-desc { font-size: 0.875rem; color: var(--muted); line-height: 1.5; margin-bottom: 20px; }
    .page-challenges-form-group { margin-bottom: 20px; }
    .page-challenges-form-label { display: block; font-size: 0.8125rem; font-weight: 600; color: var(--dim); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .page-challenges-form-input { width: 100%; background: var(--bg); border: 1px solid var(--border); color: var(--text); padding: 12px 14px; border-radius: var(--radius-sm); font-family: inherit; font-size: 0.9375rem; outline: none; transition: border-color 0.2s; }
    .page-challenges-form-input:focus { border-color: var(--accent); }
    .page-challenges-form-input::placeholder { color: var(--dim); }
    
    .page-challenges-modal-submit { width: 100%; background: var(--accent); color: #fff; border: none; padding: 12px; border-radius: var(--radius-sm); font-size: 0.9375rem; font-weight: 600; cursor: pointer; transition: background 0.2s; font-family: inherit; }
    .page-challenges-modal-submit:hover { background: var(--accent-hover); }

    @media (max-width: 1100px) {
      .page-challenges-grid { grid-template-columns: repeat(2, 1fr); }
      .page-challenges-grid > :nth-child(4), .page-challenges-grid > :nth-child(5) { grid-column: auto; transform: none; }
      .page-challenges-grid > :nth-child(4):hover, .page-challenges-grid > :nth-child(5):hover { transform: translateY(-6px); }
    }
    @media (max-width: 768px) {
      .page-challenges-grid { grid-template-columns: 1fr; padding: 0 16px; }
      .page-challenges-hero { padding: 48px 20px 32px; }
      .page-challenges-hero-title { font-size: 1.75rem; }
    }
  </style>
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-challenges-main">

    <div class="page-challenges-hero">
      <h1 class="page-challenges-hero-title">Select Your <span>Challenge Path</span></h1>
      <p class="page-challenges-hero-subtitle">Choose your starting point based on your current experience level to receive a personalized curriculum map.</p>
    </div>

    <div class="page-challenges-institution-note">
      Paths now unlock through progression. Complete the previous difficulty to move forward; exceptional speed and accuracy can unlock the next-next difficulty early.
    </div>

    <div class="page-challenges-alert-container">
      @if(session('success'))
        <div class="page-challenges-alert page-challenges-alert-success">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          {{ session('success') }}
        </div>
      @endif
      @if($errors->any())
        <div class="page-challenges-alert page-challenges-alert-error">
          <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          {{ $errors->first() }}
        </div>
      @endif

      @include('student.partials.exceptional-unlock-notifications', ['exceptionalNotifications' => $exceptionalNotifications ?? []])
    </div>

    <div class="page-challenges-grid">

      @foreach($categories as $cat)
        @php
          $lockInfo = $pathLocks[$cat->slug] ?? ['unlocked' => true, 'reason' => 'Available.', 'bonus_unlocked' => false, 'unlock_type' => 'starter'];
          $isLocked = !($lockInfo['unlocked'] ?? false);
          $isBonusUnlocked = (bool) ($lockInfo['bonus_unlocked'] ?? false);
        @endphp

        @if($isLocked)
          <div class="page-challenges-card is-locked">
            <div class="page-challenges-lock-overlay">
              <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div class="page-challenges-card-icon" style="color: var(--dim);">
              {!! $cat->icon_svg !!}
            </div>
            <h2 class="page-challenges-card-title" style="color: var(--muted);">{{ $cat->name }}</h2>
            <span class="page-challenges-card-audience" style="color: var(--dim);">{{ $cat->target_audience }}</span>
            <p class="page-challenges-card-desc" style="color: var(--dim);">
              {{ $cat->description }}
              <span class="page-challenges-lock-reason">{{ $lockInfo['reason'] ?? 'Complete the required previous path to unlock this.' }}</span>
            </p>
            <div class="page-challenges-card-action" style="color: var(--dim);">
              Locked
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
          </div>
        @else
          <a href="{{ route('challenges.map', $cat->slug) }}" class="page-challenges-card {{ $isBonusUnlocked ? 'is-bonus' : '' }}">
            @if($isBonusUnlocked)
              <div class="page-challenges-card-badge">Exceptional Unlock</div>
            @endif
            <div class="page-challenges-card-icon">
              {!! $cat->icon_svg !!}
            </div>
            <h2 class="page-challenges-card-title">{{ $cat->name }}</h2>
            <span class="page-challenges-card-audience">{{ $cat->target_audience }}</span>
            <p class="page-challenges-card-desc">
              {{ $cat->description }}
              @if($isBonusUnlocked)
                <span class="page-challenges-lock-reason" style="color:#fbbf24;">{{ $lockInfo['reason'] }}</span>
              @endif
            </p>
            <div class="page-challenges-card-action">
              {{ $isBonusUnlocked ? 'Open Early-Unlocked Path' : 'Select Path' }}
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path fill="none" stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </div>
          </a>
        @endif
      @endforeach

    </div>

  </div>
</div>

</body>
</html>