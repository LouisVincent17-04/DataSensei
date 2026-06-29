<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Password Reset' }} | DataSensei</title>
  @include('partials.brand-head')
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,600;1,9..144,300&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --bg: #080c14;
      --panel: #0d1320;
      --surface: #111827;
      --surface-2: #1a2232;
      --border: rgba(255,255,255,.08);
      --border-active: rgba(99,179,237,.55);
      --accent: #63b3ed;
      --green: #68d391;
      --amber: #f6ad55;
      --danger: #fc8181;
      --text-1: #f0f4ff;
      --text-2: #8a99b3;
      --text-3: #53627b;
      --radius: 12px;
      --radius-sm: 7px;
      --font-display: 'Fraunces', Georgia, serif;
      --font-body: 'DM Sans', system-ui, sans-serif;
    }
    html, body { min-height: 100%; }
    body {
      font-family: var(--font-body);
      color: var(--text-1);
      background:
        radial-gradient(circle at 12% 16%, rgba(99,179,237,.11), transparent 28%),
        radial-gradient(circle at 78% 82%, rgba(104,211,145,.07), transparent 26%),
        var(--bg);
    }
    .shell { display: grid; grid-template-columns: minmax(0,1fr) 500px; min-height: 100vh; }
    .left {
      padding: 46px 56px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      border-right: 1px solid var(--border);
      min-width: 0;
    }
    .left-hero { max-width: 620px; margin: auto 0; padding: 40px 0; }
    .eyebrow {
      display: flex; align-items: center; gap: 8px;
      color: var(--accent); font-size: 11px; font-weight: 600;
      letter-spacing: 2px; text-transform: uppercase; margin-bottom: 20px;
    }
    .eyebrow::before { content: ''; width: 20px; height: 1px; background: var(--accent); }
    .hero-title {
      font-family: var(--font-display); font-weight: 300;
      font-size: clamp(38px,4vw,56px); line-height: 1.08;
      letter-spacing: -1.5px; margin-bottom: 20px;
    }
    .hero-title em { color: var(--accent); font-style: italic; }
    .hero-copy { color: var(--text-2); max-width: 470px; line-height: 1.7; font-weight: 300; }
    .security-list { display: grid; gap: 12px; margin-top: 34px; }
    .security-item { display: flex; align-items: center; gap: 11px; color: var(--text-2); font-size: 13px; }
    .security-icon {
      width: 28px; height: 28px; display: grid; place-items: center;
      border: 1px solid rgba(99,179,237,.22); border-radius: 8px;
      background: rgba(99,179,237,.06); color: var(--accent);
    }
    .left-foot { color: var(--text-3); font-size: 12px; }
    .right { padding: 40px; display: flex; align-items: center; justify-content: center; background: rgba(13,19,32,.72); }
    .card { width: 100%; max-width: 420px; }
    .step-row { display: flex; gap: 8px; margin-bottom: 28px; }
    .step { height: 3px; flex: 1; background: var(--surface-2); border-radius: 999px; overflow: hidden; }
    .step.active, .step.done { background: var(--accent); }
    .heading { font-family: var(--font-display); font-size: 31px; font-weight: 600; letter-spacing: -.6px; margin-bottom: 8px; }
    .subheading { color: var(--text-2); font-size: 14px; line-height: 1.65; margin-bottom: 26px; }
    .field { margin-bottom: 18px; }
    .field-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-2); margin-bottom: 8px; }
    .field-wrap { position: relative; }
    .field-input {
      width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm);
      background: var(--surface); color: var(--text-1); padding: 13px 14px;
      font: inherit; font-size: 14px; outline: none; transition: .18s ease;
    }
    .field-input:focus { border-color: var(--border-active); box-shadow: 0 0 0 3px rgba(99,179,237,.08); }
    .field-input::placeholder { color: #46536a; }
    .otp-input { text-align: center; font-size: 28px; letter-spacing: .5em; font-weight: 600; padding-left: calc(14px + .5em); }
    .password-input { padding-right: 46px; }
    .toggle {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      border: 0; background: transparent; color: var(--text-2); cursor: pointer; padding: 6px;
    }
    .field-error { margin-top: 7px; font-size: 12px; color: var(--danger); line-height: 1.45; }
    .alert { border-radius: var(--radius-sm); padding: 12px 14px; margin-bottom: 18px; font-size: 13px; line-height: 1.5; }
    .alert-success { color: #b9f6d4; background: rgba(104,211,145,.08); border: 1px solid rgba(104,211,145,.25); }
    .alert-danger { color: #ffd1d1; background: rgba(252,129,129,.08); border: 1px solid rgba(252,129,129,.25); }
    .alert-info { color: #c9e8ff; background: rgba(99,179,237,.08); border: 1px solid rgba(99,179,237,.24); }
    .button {
      width: 100%; min-height: 46px; border: 0; border-radius: var(--radius-sm);
      background: var(--accent); color: #07101d; font: inherit; font-weight: 700;
      cursor: pointer; transition: transform .18s ease, opacity .18s ease, box-shadow .18s ease;
      display: inline-flex; align-items: center; justify-content: center; gap: 9px;
      text-decoration: none;
    }
    .button:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(99,179,237,.16); }
    .button:disabled { cursor: not-allowed; opacity: .58; transform: none; box-shadow: none; }
    .button-secondary { background: transparent; color: var(--text-2); border: 1px solid var(--border); }
    .button-secondary:hover { color: var(--text-1); border-color: rgba(255,255,255,.16); box-shadow: none; }
    .spinner { display: none; width: 15px; height: 15px; border: 2px solid rgba(7,16,29,.28); border-top-color: #07101d; border-radius: 50%; animation: spin .75s linear infinite; }
    .is-loading .spinner { display: block; }
    @keyframes spin { to { transform: rotate(360deg); } }
    .helper { color: var(--text-3); font-size: 12px; line-height: 1.55; margin-top: 10px; }
    .actions { display: grid; gap: 10px; margin-top: 8px; }
    .link-row { margin-top: 20px; text-align: center; font-size: 13px; color: var(--text-2); }
    .link { color: var(--accent); text-decoration: none; font-weight: 600; }
    .link:hover { text-decoration: underline; }
    .resend-row { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin: 14px 0 18px; font-size: 12px; color: var(--text-2); }
    .resend-button { border: 0; background: transparent; color: var(--accent); font: inherit; font-weight: 600; cursor: pointer; }
    .resend-button:disabled { color: var(--text-3); cursor: not-allowed; }
    .password-rules { margin: 12px 0 20px; padding: 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); background: rgba(17,24,39,.72); }
    .password-rules strong { display: block; font-size: 12px; margin-bottom: 7px; }
    .password-rules p { color: var(--text-2); font-size: 12px; line-height: 1.55; }
    @media (max-width: 900px) {
      .shell { grid-template-columns: 1fr; }
      .left { display: none; }
      .right { min-height: 100vh; padding: 28px 20px; }
      .card { max-width: 460px; }
    }
    @media (max-width: 480px) {
      .right { padding: 24px 16px; align-items: flex-start; }
      .card { margin-top: 28px; }
      .heading { font-size: 28px; }
    }
  </style>
</head>
<body>
<div class="shell">
  <section class="left" aria-hidden="true">
    <div>
      @include('partials.brand-logo', [
        'variant' => 'auth',
        'size' => 'large',
        'subtext' => 'Data Science Learning Platform',
        'href' => route('login'),
      ])
    </div>

    <div class="left-hero">
      <div class="eyebrow">Secure account recovery</div>
      <h1 class="hero-title">Return to learning<br>with a <em>verified</em><br>password reset</h1>
      <p class="hero-copy">DataSensei uses a short-lived, one-time email code to verify your identity before allowing a password change.</p>
      <div class="security-list">
        <div class="security-item"><span class="security-icon">01</span><span>Six-digit code sent only to the registered email</span></div>
        <div class="security-item"><span class="security-icon">02</span><span>Code expires automatically after five minutes</span></div>
        <div class="security-item"><span class="security-icon">03</span><span>Previous and used codes are immediately invalidated</span></div>
      </div>
    </div>

    <div class="left-foot">DataSensei account security</div>
  </section>

  <main class="right">
    <div class="card">
      <div class="step-row" aria-label="Password reset progress">
        <span class="step {{ ($step ?? 1) >= 1 ? 'active' : '' }}"></span>
        <span class="step {{ ($step ?? 1) >= 2 ? 'active' : '' }}"></span>
        <span class="step {{ ($step ?? 1) >= 3 ? 'active' : '' }}"></span>
      </div>

      @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
      @endif
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if ($errors->has('rate_limit'))
        <div class="alert alert-danger">{{ $errors->first('rate_limit') }}</div>
      @endif

      @yield('content')
    </div>
  </main>
</div>
<script>
  document.querySelectorAll('form[data-loading-form]').forEach((form) => {
    form.addEventListener('submit', () => {
      const button = form.querySelector('[data-loading-button]');
      if (!button) return;
      button.disabled = true;
      button.classList.add('is-loading');
      const text = button.querySelector('[data-button-text]');
      if (text && button.dataset.loadingText) text.textContent = button.dataset.loadingText;
    });
  });

  document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
      const input = document.getElementById(button.dataset.passwordToggle);
      if (!input) return;
      const reveal = input.type === 'password';
      input.type = reveal ? 'text' : 'password';
      button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
      button.textContent = reveal ? 'Hide' : 'Show';
    });
  });
</script>
@stack('scripts')
</body>
</html>
