<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data SciFy — Authentication</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;1,9..144,300&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #080c14;
    --panel: #0d1320;
    --surface: #111827;
    --surface-2: #1a2232;
    --border: rgba(255,255,255,0.07);
    --border-active: rgba(99,179,237,0.5);
    --accent: #63b3ed;
    --accent-dim: rgba(99,179,237,0.12);
    --accent-glow: rgba(99,179,237,0.2);
    --green: #68d391;
    --green-dim: rgba(104,211,145,0.12);
    --amber: #f6ad55;
    --amber-dim: rgba(246,173,85,0.1);
    --text-1: #f0f4ff;
    --text-2: #8a99b3;
    --text-3: #4a5568;
    --danger: #fc8181;
    --danger-dim: rgba(252,129,129,0.1);
    --radius: 12px;
    --radius-sm: 7px;
    --font-display: 'Fraunces', Georgia, serif;
    --font-body: 'DM Sans', system-ui, sans-serif;
  }

  html, body {
    height: 100%;
    font-family: var(--font-body);
    background: var(--bg);
    color: var(--text-1);
    overflow: hidden;
  }

  /* ── CANVAS BACKGROUND ── */
  #bg-canvas {
    position: fixed;
    inset: 0;
    z-index: 0;
    pointer-events: none;
  }

  /* ── LAYOUT ── */
  .shell {
    position: relative;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr 480px;
    height: 100vh;
    overflow: hidden;
  }

  /* ── LEFT PANEL ── */
  .left {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 48px 56px;
    position: relative;
    overflow: hidden;
  }

  .left-logo {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .logo-mark {
    width: 36px;
    height: 36px;
    background: var(--accent);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .logo-mark svg { width: 20px; height: 20px; }

  .logo-name {
    font-family: var(--font-display);
    font-size: 20px;
    font-weight: 600;
    color: var(--text-1);
    letter-spacing: -0.3px;
  }

  .left-hero {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 32px 0;
  }

  .hero-eyebrow {
    font-size: 11px;
    font-weight: 500;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .hero-eyebrow::before {
    content: '';
    display: block;
    width: 20px;
    height: 1px;
    background: var(--accent);
  }

  .hero-title {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 54px);
    font-weight: 300;
    line-height: 1.1;
    color: var(--text-1);
    letter-spacing: -1.5px;
    margin-bottom: 20px;
  }

  .hero-title em {
    font-style: italic;
    color: var(--accent);
  }

  .hero-sub {
    font-size: 15px;
    line-height: 1.65;
    color: var(--text-2);
    max-width: 420px;
    margin-bottom: 40px;
    font-weight: 300;
  }

  /* Floating stats */
  .stats-row {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
  }

  .stat-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    background: var(--surface);
    border: 0.5px solid var(--border);
    border-radius: 40px;
    font-size: 13px;
    color: var(--text-2);
    transition: border-color 0.3s;
  }

  .stat-pill:hover { border-color: rgba(255,255,255,0.15); }

  .stat-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--green);
    box-shadow: 0 0 6px var(--green);
    animation: pulse 2s infinite;
  }

  .stat-dot.amber { background: var(--amber); box-shadow: 0 0 6px var(--amber); }
  .stat-dot.blue { background: var(--accent); box-shadow: 0 0 6px var(--accent); }

  @keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }

  /* Token visualization strip */
  .token-strip {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    margin-bottom: 32px;
  }

  .token {
    padding: 4px 10px;
    border-radius: 5px;
    font-size: 11.5px;
    font-family: 'Courier New', monospace;
    font-weight: 400;
    letter-spacing: 0.2px;
    opacity: 0;
    animation: tokenIn 0.4s ease forwards;
  }

  .token.t-keyword { background: rgba(99,179,237,0.15); color: #90cdf4; border: 0.5px solid rgba(99,179,237,0.3); }
  .token.t-string  { background: rgba(104,211,145,0.12); color: #9ae6b4; border: 0.5px solid rgba(104,211,145,0.25); }
  .token.t-num     { background: rgba(246,173,85,0.12);  color: #fbd38d; border: 0.5px solid rgba(246,173,85,0.25); }
  .token.t-op      { background: rgba(255,255,255,0.06); color: #a0aec0; border: 0.5px solid rgba(255,255,255,0.1); }
  .token.t-func    { background: rgba(183,148,246,0.12); color: #d6bcfa; border: 0.5px solid rgba(183,148,246,0.25); }

  @keyframes tokenIn {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* bottom quote */
  .left-bottom {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .left-bottom blockquote {
    font-size: 13px;
    font-weight: 300;
    font-style: italic;
    color: var(--text-3);
    line-height: 1.6;
    border-left: 1.5px solid var(--text-3);
    padding-left: 12px;
  }

  /* ── RIGHT PANEL (form) ── */
  .right {
    background: var(--panel);
    border-left: 0.5px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 48px;
    position: relative;
    overflow-y: auto;
  }

  .form-card {
    width: 100%;
    max-width: 360px;
  }

  /* Tabs */
  .tabs {
    display: flex;
    gap: 0;
    background: var(--surface);
    border-radius: 10px;
    padding: 4px;
    margin-bottom: 32px;
  }

  .tab-btn {
    flex: 1;
    padding: 9px 0;
    border: none;
    background: transparent;
    color: var(--text-2);
    font-family: var(--font-body);
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    border-radius: 7px;
    transition: all 0.2s;
    letter-spacing: 0.1px;
  }

  .tab-btn.active {
    background: var(--surface-2);
    color: var(--text-1);
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
  }

  /* Form header */
  .form-heading {
    font-family: var(--font-display);
    font-size: 28px;
    font-weight: 600;
    letter-spacing: -0.8px;
    color: var(--text-1);
    margin-bottom: 6px;
    line-height: 1.15;
  }

  .form-sub {
    font-size: 13.5px;
    color: var(--text-2);
    margin-bottom: 28px;
    font-weight: 300;
    line-height: 1.5;
  }

  /* Field */
  .field { margin-bottom: 14px; }

  .field-label {
    display: block;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-2);
    margin-bottom: 6px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
  }

  .field-wrap {
    position: relative;
  }

  .field-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: var(--text-3);
    pointer-events: none;
    transition: color 0.2s;
  }

  .field-input {
    width: 100%;
    background: var(--surface);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 11px 12px 11px 38px;
    font-family: var(--font-body);
    font-size: 14px;
    color: var(--text-1);
    outline: none;
    transition: border-color 0.2s, background 0.2s;
    -webkit-appearance: none;
  }

  .field-input::placeholder { color: var(--text-3); font-size: 13.5px; }

  .field-input:focus {
    border-color: var(--border-active);
    background: var(--surface-2);
  }

  .field-input:focus ~ .field-icon,
  .field-wrap:focus-within .field-icon { color: var(--accent); }

  /* password toggle */
  .pwd-toggle {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-3);
    padding: 2px;
    display: flex;
    align-items: center;
    transition: color 0.2s;
  }
  .pwd-toggle:hover { color: var(--text-2); }

  /* Remember + forgot */
  .form-extras {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    margin-top: 2px;
  }

  .check-label {
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    font-size: 13px;
    color: var(--text-2);
    user-select: none;
  }

  .check-box {
    width: 15px;
    height: 15px;
    border: 0.5px solid var(--text-3);
    border-radius: 4px;
    background: var(--surface);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s;
    flex-shrink: 0;
  }

  .check-label input[type="checkbox"] { display: none; }
  .check-label input:checked + .check-box {
    background: var(--accent);
    border-color: var(--accent);
  }
  .check-label input:checked + .check-box::after {
    content: '';
    display: block;
    width: 8px;
    height: 5px;
    border-left: 1.5px solid #fff;
    border-bottom: 1.5px solid #fff;
    transform: rotate(-45deg) translateY(-1px);
  }

  .forgot-link {
    font-size: 13px;
    color: var(--accent);
    text-decoration: none;
    opacity: 0.8;
    transition: opacity 0.2s;
  }
  .forgot-link:hover { opacity: 1; }

  /* CTA button */
  .cta-btn {
    width: 100%;
    padding: 13px;
    background: var(--accent);
    border: none;
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 14.5px;
    font-weight: 500;
    color: #04111f;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.1px;
  }

  .cta-btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0);
    transition: background 0.2s;
  }

  .cta-btn:hover::after { background: rgba(255,255,255,0.08); }
  .cta-btn:active { transform: scale(0.985); }

  .cta-btn.loading { pointer-events: none; opacity: 0.7; }

  /* Divider */
  .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0;
  }
  .divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 0.5px;
    background: var(--border);
  }
  .divider span {
    font-size: 12px;
    color: var(--text-3);
    white-space: nowrap;
  }

  /* Social buttons */
  .social-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  .social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 12px;
    background: var(--surface);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 500;
    color: var(--text-2);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
  }
  .social-btn:hover { border-color: rgba(255,255,255,0.14); color: var(--text-1); background: var(--surface-2); }
  .social-btn svg { width: 16px; height: 16px; flex-shrink: 0; }

  /* Strength indicator */
  .strength-wrap {
    margin-top: 7px;
    display: none;
  }
  .strength-wrap.visible { display: block; }
  .strength-bars {
    display: flex;
    gap: 3px;
    margin-bottom: 4px;
  }
  .strength-bar {
    flex: 1;
    height: 3px;
    border-radius: 2px;
    background: var(--surface-2);
    transition: background 0.3s;
  }
  .strength-bar.active { }
  .strength-label { font-size: 11px; color: var(--text-3); }

  /* Error state */
  .field-error {
    font-size: 11.5px;
    color: var(--danger);
    margin-top: 5px;
    display: none;
  }
  .field.has-error .field-input { border-color: rgba(252,129,129,0.5); }
  .field.has-error .field-error { display: block; }

  /* Panels */
  .form-panel { display: none; }
  .form-panel.active { display: block; }

  /* Role selector (register) */
  .role-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 14px;
  }

  .role-card {
    padding: 12px;
    background: var(--surface);
    border: 0.5px solid var(--border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
  }
  .role-card:hover { border-color: rgba(255,255,255,0.14); }
  .role-card.selected { border-color: var(--border-active); background: var(--accent-dim); }
  .role-card input { display: none; }

  .role-icon { font-size: 20px; display: block; margin-bottom: 4px; }
  .role-name { font-size: 12px; font-weight: 500; color: var(--text-2); }
  .role-card.selected .role-name { color: var(--accent); }

  /* Terms */
  .terms-text {
    font-size: 11.5px;
    color: var(--text-3);
    line-height: 1.5;
    margin-top: 16px;
    text-align: center;
  }
  .terms-text a { color: var(--accent); text-decoration: none; opacity: 0.85; }
  .terms-text a:hover { opacity: 1; }

  /* Responsive */
  @media (max-width: 900px) {
    .shell { grid-template-columns: 1fr; }
    .left { display: none; }
    .right { border-left: none; }
  }

  /* Animate in */
  .form-card { animation: slideUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both; }
  @keyframes slideUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .left-hero { animation: fadeIn 0.7s ease 0.1s both; }
  @keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
  }
</style>
</head>
<body>

@php
    // Logic to determine which tab should be active on page load
    $isRegisterTab = old('name') || $errors->has('name') || request()->is('register');
@endphp

<canvas id="bg-canvas"></canvas>

<div class="shell">

  <div class="left">
    <div class="left-logo">
      <div class="logo-mark">
        <svg viewBox="0 0 20 20" fill="none">
          <rect x="2" y="10" width="3" height="8" rx="1" fill="#04111f"/>
          <rect x="7" y="6"  width="3" height="12" rx="1" fill="#04111f"/>
          <rect x="12" y="2" width="3" height="16" rx="1" fill="#04111f"/>
          <circle cx="16.5" cy="5.5" r="2" fill="#04111f" opacity="0.6"/>
          <path d="M3.5 10 L8.5 7 L13.5 3" stroke="#04111f" stroke-width="1.2" stroke-dasharray="2 1.5" opacity="0.5"/>
        </svg>
      </div>
      <span class="logo-name">Data SciFy</span>
    </div>

    <div class="left-hero">
      <div class="hero-eyebrow">AI-Powered Learning</div>

      <h1 class="hero-title">
        Master data science<br>with an <em>intelligent</em><br>feedback engine
      </h1>

      <p class="hero-sub">
        Write code, get AI-driven insights in real time, and learn data science concepts through structured, adaptive curriculum.
      </p>

      <div class="token-strip" id="token-strip"></div>

      <div class="stats-row">
        <div class="stat-pill"><span class="stat-dot"></span>Live compiler</div>
        <div class="stat-pill"><span class="stat-dot blue"></span>NLP feedback</div>
        <div class="stat-pill"><span class="stat-dot amber"></span>Adaptive paths</div>
      </div>
    </div>

    <div class="left-bottom">
      <blockquote>
        "The goal is to turn data into information,<br>and information into insight." — Carly Fiorina
      </blockquote>
    </div>
  </div>

  <div class="right">
    <div class="form-card">

      <div class="tabs">
        <button class="tab-btn {{ !$isRegisterTab ? 'active' : '' }}" onclick="switchTab('login')" type="button">Sign in</button>
        <button class="tab-btn {{ $isRegisterTab ? 'active' : '' }}" onclick="switchTab('register')" type="button">Create account</button>
      </div>

      <div class="form-panel {{ !$isRegisterTab ? 'active' : '' }}" id="panel-login">
        <form method="POST" action="{{ route('login') ?? url('/login') }}">
          @csrf
          <h2 class="form-heading">Welcome back</h2>
          <p class="form-sub">Continue your data science journey.</p>

          <div class="field @error('email') has-error @enderror" id="f-email-login">
            <label class="field-label" for="login-email">Email</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <rect x="1.5" y="3.5" width="13" height="9" rx="1.5"/>
                <path d="M1.5 5.5 L8 9.5 L14.5 5.5"/>
              </svg>
              <input class="field-input" type="email" name="email" id="login-email" value="{{ old('email') }}" placeholder="you@university.edu" required autofocus autocomplete="username">
            </div>
            @error('email')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror" id="f-pwd-login">
            <label class="field-label" for="login-pwd">Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <rect x="2.5" y="7" width="11" height="7.5" rx="1.5"/>
                <path d="M5 7V5a3 3 0 016 0v2"/>
              </svg>
              <input class="field-input" type="password" name="password" id="login-pwd" placeholder="••••••••" required autocomplete="current-password" style="padding-right: 40px;">
              <button class="pwd-toggle" onclick="togglePwd('login-pwd', this)" type="button" aria-label="Toggle password">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.3" id="eye-login">
                  <ellipse cx="7.5" cy="7.5" rx="5.5" ry="3.5"/>
                  <circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
              </button>
            </div>
            @error('password')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-extras">
            <label class="check-label">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <span class="check-box"></span>
              Remember me
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
            @endif
          </div>

          <button type="submit" class="cta-btn" id="login-btn">Sign in to Data SciFy</button>
        </form>

        <div class="divider"><span>or continue with</span></div>

        <div class="social-row">
          <a href="{{ url('/auth/google') }}" class="social-btn">
            <svg viewBox="0 0 16 16" fill="none">
              <path d="M15.54 8.18c0-.57-.05-1.12-.14-1.64H8v3.1h4.23a3.62 3.62 0 01-1.57 2.37v1.97h2.54c1.49-1.37 2.34-3.39 2.34-5.8z" fill="#4285F4"/>
              <path d="M8 16c2.13 0 3.91-.7 5.21-1.9l-2.54-1.97c-.71.47-1.61.75-2.67.75-2.05 0-3.79-1.39-4.41-3.25H1.06v2.03A7.98 7.98 0 008 16z" fill="#34A853"/>
              <path d="M3.59 9.63A4.83 4.83 0 013.34 8c0-.57.1-1.12.25-1.63V4.34H1.06A7.98 7.98 0 000 8c0 1.29.31 2.5.86 3.57l2.73-1.94z" fill="#FBBC05"/>
              <path d="M8 3.18c1.15 0 2.19.4 3 1.17l2.25-2.25A7.96 7.96 0 008 0 7.98 7.98 0 001.06 4.34L3.59 6.37C4.21 4.57 5.95 3.18 8 3.18z" fill="#EA4335"/>
            </svg>
            Google
          </a>
          <a href="{{ url('/auth/github') }}" class="social-btn">
            <svg viewBox="0 0 16 16" fill="currentColor">
              <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"/>
            </svg>
            GitHub
          </a>
        </div>

        <p class="terms-text" style="margin-top:18px;">
          Don't have an account? <a href="#" onclick="switchTab('register'); return false;">Create one free</a>
        </p>
      </div>

      <div class="form-panel {{ $isRegisterTab ? 'active' : '' }}" id="panel-register">
        <form method="POST" action="{{ route('register') ?? url('/register') }}">
          @csrf
          <h2 class="form-heading">Start learning</h2>
          <p class="form-sub">Join thousands of data science learners.</p>

          <div class="role-grid">
            <label class="role-card {{ old('role', 'student') == 'student' ? 'selected' : '' }}">
              <input type="radio" name="role" value="student" {{ old('role', 'student') == 'student' ? 'checked' : '' }}>
              <span class="role-name">Student</span>
            </label>
            <label class="role-card {{ old('role') == 'educator' ? 'selected' : '' }}">
              <input type="radio" name="role" value="educator" {{ old('role') == 'educator' ? 'checked' : '' }}>
              <span class="role-name">Educator</span>
            </label>
          </div>

          <div class="field @error('name') has-error @enderror" id="f-name-reg">
            <label class="field-label" for="reg-name">Full name</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <circle cx="8" cy="5" r="3"/>
                <path d="M1.5 14.5c0-3.31 2.91-6 6.5-6s6.5 2.69 6.5 6"/>
              </svg>
              <input class="field-input" type="text" name="name" id="reg-name" value="{{ old('name') }}" placeholder="Juan dela Cruz" required autocomplete="name">
            </div>
            @error('name')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('email') has-error @enderror" id="f-email-reg">
            <label class="field-label" for="reg-email">Email</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <rect x="1.5" y="3.5" width="13" height="9" rx="1.5"/>
                <path d="M1.5 5.5 L8 9.5 L14.5 5.5"/>
              </svg>
              <input class="field-input" type="email" name="email" id="reg-email" value="{{ old('email') }}" placeholder="you@university.edu" required autocomplete="email">
            </div>
            @error('email')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror" id="f-pwd-reg">
            <label class="field-label" for="reg-pwd">Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <rect x="2.5" y="7" width="11" height="7.5" rx="1.5"/>
                <path d="M5 7V5a3 3 0 016 0v2"/>
              </svg>
              <input class="field-input" type="password" name="password" id="reg-pwd" placeholder="Min. 8 characters" required autocomplete="new-password" style="padding-right: 40px;" oninput="checkStrength(this.value)">
              <button class="pwd-toggle" onclick="togglePwd('reg-pwd', this)" type="button">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.3">
                  <ellipse cx="7.5" cy="7.5" rx="5.5" ry="3.5"/>
                  <circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
              </button>
            </div>
            <div class="strength-wrap" id="strength-wrap">
              <div class="strength-bars">
                <div class="strength-bar" id="sb1"></div>
                <div class="strength-bar" id="sb2"></div>
                <div class="strength-bar" id="sb3"></div>
                <div class="strength-bar" id="sb4"></div>
              </div>
              <span class="strength-label" id="strength-label">Weak</span>
            </div>
            @error('password')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field" id="f-pwd-conf-reg">
            <label class="field-label" for="reg-pwd-conf">Confirm Password</label>
            <div class="field-wrap">
              <svg class="field-icon" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4">
                <rect x="2.5" y="7" width="11" height="7.5" rx="1.5"/>
                <path d="M5 7V5a3 3 0 016 0v2"/>
              </svg>
              <input class="field-input" type="password" name="password_confirmation" id="reg-pwd-conf" placeholder="Type password again" required autocomplete="new-password" style="padding-right: 40px;">
              <button class="pwd-toggle" onclick="togglePwd('reg-pwd-conf', this)" type="button">
                <svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.3">
                  <ellipse cx="7.5" cy="7.5" rx="5.5" ry="3.5"/>
                  <circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
              </button>
            </div>
          </div>

          <button type="submit" class="cta-btn" id="reg-btn" style="margin-top: 4px;">Create account</button>
        </form>

        <p class="terms-text">
          By creating an account you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
        </p>

        <p class="terms-text" style="margin-top:10px;">
          Already have an account? <a href="#" onclick="switchTab('login'); return false;">Sign in</a>
        </p>
      </div>

    </div>
  </div>
</div>

<script>
/* ── CANVAS PARTICLE BACKGROUND ── */
(function(){
  const canvas = document.getElementById('bg-canvas');
  const ctx = canvas.getContext('2d');
  let W, H, particles = [];

  function resize(){
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
  }

  function createParticles(){
    particles = [];
    const N = Math.floor((W * H) / 18000);
    for(let i=0; i<N; i++){
      particles.push({
        x: Math.random() * W,
        y: Math.random() * H,
        r: Math.random() * 1.2 + 0.3,
        vx: (Math.random() - 0.5) * 0.15,
        vy: (Math.random() - 0.5) * 0.15,
        alpha: Math.random() * 0.4 + 0.1
      });
    }
  }

  function draw(){
    ctx.clearRect(0, 0, W, H);

    // Draw connections
    for(let i=0; i<particles.length; i++){
      for(let j=i+1; j<particles.length; j++){
        const p = particles[i], q = particles[j];
        const dx = p.x-q.x, dy = p.y-q.y;
        const d = Math.sqrt(dx*dx+dy*dy);
        if(d < 100){
          ctx.beginPath();
          ctx.moveTo(p.x, p.y);
          ctx.lineTo(q.x, q.y);
          ctx.strokeStyle = `rgba(99,179,237,${(1 - d/100) * 0.06})`;
          ctx.lineWidth = 0.5;
          ctx.stroke();
        }
      }
    }

    // Draw dots
    particles.forEach(p => {
      ctx.beginPath();
      ctx.arc(p.x, p.y, p.r, 0, Math.PI*2);
      ctx.fillStyle = `rgba(99,179,237,${p.alpha})`;
      ctx.fill();

      p.x += p.vx; p.y += p.vy;
      if(p.x < 0 || p.x > W) p.vx *= -1;
      if(p.y < 0 || p.y > H) p.vy *= -1;
    });

    requestAnimationFrame(draw);
  }

  window.addEventListener('resize', ()=>{ resize(); createParticles(); });
  resize(); createParticles(); draw();
})();

/* ── TOKEN STRIP ANIMATION ── */
const tokens = [
  { text: 'import', cls: 't-keyword' },
  { text: 'pandas', cls: 't-func' },
  { text: 'as', cls: 't-keyword' },
  { text: 'pd', cls: 't-op' },
  { text: 'df', cls: 't-op' },
  { text: '=', cls: 't-op' },
  { text: 'pd.read_csv', cls: 't-func' },
  { text: '("data.csv")', cls: 't-string' },
  { text: 'df.shape', cls: 't-func' },
  { text: '(', cls: 't-op' },
  { text: '1458', cls: 't-num' },
  { text: ',', cls: 't-op' },
  { text: '81', cls: 't-num' },
  { text: ')', cls: 't-op' },
  { text: 'accuracy', cls: 't-keyword' },
  { text: ':', cls: 't-op' },
  { text: '0.943', cls: 't-num' },
  { text: 'fit(', cls: 't-func' },
  { text: 'X_train', cls: 't-op' },
  { text: ')', cls: 't-op' },
];

function renderTokens(){
  const strip = document.getElementById('token-strip');
  strip.innerHTML = '';
  tokens.forEach((t, i) => {
    const el = document.createElement('span');
    el.className = `token ${t.cls}`;
    el.textContent = t.text;
    el.style.animationDelay = `${i * 0.06}s`;
    strip.appendChild(el);
  });
}
renderTokens();

/* ── TAB SWITCHER ── */
function switchTab(tab){
  document.querySelectorAll('.tab-btn').forEach((b, i) => {
    b.classList.toggle('active', (tab === 'login') ? i===0 : i===1);
  });
  document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
  document.getElementById(`panel-${tab}`).classList.add('active');

  // Update heading context
  if(tab === 'login'){
    document.title = 'Data SciFy — Sign In';
  } else {
    document.title = 'Data SciFy — Create Account';
  }
}

/* ── ROLE CARDS ── */
document.querySelectorAll('.role-card').forEach(card => {
  card.addEventListener('click', () => {
    // Sync the underlying radio button status to UI changes
    document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    card.querySelector('input').checked = true;
  });
});

/* ── PASSWORD TOGGLE ── */
function togglePwd(id, btn){
  const inp = document.getElementById(id);
  const isText = inp.type === 'text';
  inp.type = isText ? 'password' : 'text';
  btn.innerHTML = isText
    ? `<svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.3"><ellipse cx="7.5" cy="7.5" rx="5.5" ry="3.5"/><circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/></svg>`
    : `<svg width="15" height="15" viewBox="0 0 15 15" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M1.5 1.5l12 12M6.2 6.4a1.5 1.5 0 002.1 2.1M3 5C1.8 6 1.2 7 1.2 7.5S3.5 11 7.5 11M12 10c1.2-1 1.8-2 1.8-2.5S11 4 7.5 4"/></svg>`;
}

/* ── PASSWORD STRENGTH ── */
function checkStrength(val){
  const wrap = document.getElementById('strength-wrap');
  const label = document.getElementById('strength-label');
  const bars = [1,2,3,4].map(i => document.getElementById('sb'+i));

  if(!val){ wrap.classList.remove('visible'); return; }
  wrap.classList.add('visible');

  let score = 0;
  if(val.length >= 8) score++;
  if(/[A-Z]/.test(val)) score++;
  if(/[0-9]/.test(val)) score++;
  if(/[^A-Za-z0-9]/.test(val)) score++;

  const colors = ['#fc8181','#f6ad55','#68d391','#63b3ed'];
  const labels = ['Weak','Fair','Good','Strong'];

  bars.forEach((b, i) => {
    b.style.background = i < score ? colors[score-1] : 'var(--surface-2)';
  });
  label.textContent = labels[score-1] || 'Weak';
  label.style.color = colors[score-1] || colors[0];
}
</script>
</body>
</html>