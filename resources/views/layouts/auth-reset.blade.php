<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'Password Reset' }} — DataSensei</title>
<style>
    /* Password reset (forgot, verify, new password). Colours, type and radius
       come from partials.design-system; this block only lays the page out. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body { min-height: 100%; }
    body {
      background: var(--ds-bg);
      color: var(--ds-text);
      font-family: var(--ds-font-sans);
    }

    .shell { display: grid; grid-template-columns: minmax(0, 1fr) 460px; min-height: 100vh; }

    /* ── visual side ─────────────────────────────────────── */
    .left {
      min-width: 0;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 48px 64px;
      background: var(--ds-bg);
    }
    .left-hero { max-width: 560px; margin: auto 0; padding: 40px 0; }
    .hero-title {
      margin-bottom: 12px;
      color: var(--ds-text);
      font-size: 1.625rem;
      font-weight: 700;
      line-height: 1.25;
      letter-spacing: -0.02em;
    }
    .hero-title em { color: inherit; font-style: normal; }
    .hero-copy { max-width: 52ch; color: var(--ds-text-muted); font-size: 0.875rem; line-height: 1.6; }
    .security-list { display: grid; gap: 12px; margin-top: 28px; }
    .security-item { display: flex; align-items: center; gap: 12px; color: var(--ds-text-secondary); font-size: 0.875rem; line-height: 1.45; }
    .security-icon {
      width: 28px;
      height: 28px;
      flex: 0 0 28px;
      display: grid;
      place-items: center;
      border: 1px solid var(--ds-border-strong);
      border-radius: var(--ds-radius-sm);
      background: var(--ds-surface-2);
      color: var(--ds-text-muted);
      font-size: 0.75rem;
      font-weight: 600;
      font-variant-numeric: tabular-nums;
    }
    .left-foot { color: var(--ds-text-dim); font-size: 0.75rem; }

    /* ── form side ───────────────────────────────────────── */
    .right {
      min-width: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 40px 48px;
      border-left: 1px solid var(--ds-border);
      background: var(--ds-surface);
    }
    .card { width: 100%; max-width: 372px; min-width: 0; }

    .step-row { display: flex; gap: 6px; margin-bottom: 28px; }
    .step { height: 4px; flex: 1; overflow: hidden; border-radius: 999px; background: var(--ds-surface-2); }
    .step.active, .step.done { background: var(--ds-accent); }

    .heading { margin-bottom: 6px; color: var(--ds-text); font-size: 1.375rem; font-weight: 700; line-height: 1.3; letter-spacing: -0.015em; }
    .subheading { margin-bottom: 24px; color: var(--ds-text-muted); font-size: 0.875rem; line-height: 1.6; overflow-wrap: anywhere; }
    .subheading strong { color: var(--ds-text-secondary); font-weight: 600; }

    .field { margin-bottom: 16px; }
    .field-label { display: block; margin-bottom: 6px; color: var(--ds-text-secondary); font-size: 0.8125rem; font-weight: 500; }
    .field-wrap { position: relative; }
    .field-input {
      width: 100%;
      min-height: 40px;
      padding: 8px 12px;
      border: 1px solid var(--ds-input-border);
      border-radius: var(--ds-radius-sm);
      background: var(--ds-surface-3);
      color: var(--ds-text);
      font: 400 0.875rem/1.4 var(--ds-font-sans);
      outline: none;
      transition: border-color var(--ds-dur-2) ease, box-shadow var(--ds-dur-2) ease;
    }
    .field-input:hover { border-color: var(--ds-border-strong); }
    .field-input:focus { border-color: var(--ds-accent); box-shadow: var(--ds-focus-ring); }
    .field-input::placeholder { color: var(--ds-text-dim); }
    /* The one-time code reads as a code: monospaced digits, centred. */
    .otp-input { min-height: 52px; text-align: center; font-family: var(--ds-font-mono); font-size: 1.5rem; font-weight: 600; font-variant-numeric: tabular-nums; }
    .password-input { padding-right: 64px; }
    .toggle {
      position: absolute;
      top: 50%;
      right: 4px;
      min-height: 32px;
      padding: 0 10px;
      border: 0;
      border-radius: var(--ds-radius-xs);
      background: transparent;
      color: var(--ds-text-muted);
      font: 500 0.75rem/1 var(--ds-font-sans);
      cursor: pointer;
      transform: translateY(-50%);
      transition: color var(--ds-dur-2) ease;
    }
    .toggle:hover { color: var(--ds-text); }
    .field-error { margin-top: 6px; color: var(--ds-danger-text); font-size: 0.75rem; line-height: 1.45; }

    .alert { margin-bottom: 16px; padding: 12px 16px; border: 1px solid var(--ds-accent-border); border-radius: var(--ds-radius-sm); background: var(--ds-accent-soft); color: #dbeafe; font-size: 0.875rem; line-height: 1.5; overflow-wrap: anywhere; }
    .alert-success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
    .alert-danger { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }
    .alert-info { border-color: var(--ds-accent-border); background: var(--ds-accent-soft); color: #dbeafe; }

    .button {
      width: 100%;
      min-height: 40px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 0 16px;
      border: 1px solid var(--ds-accent);
      border-radius: var(--ds-radius-sm);
      background: var(--ds-accent);
      color: #ffffff;
      font: 500 0.875rem/1.2 var(--ds-font-sans);
      text-decoration: none;
      cursor: pointer;
      transition: background var(--ds-dur-2) ease, border-color var(--ds-dur-2) ease, color var(--ds-dur-2) ease;
    }
    .button:hover { border-color: var(--ds-accent-strong); background: var(--ds-accent-strong); }
    .button:disabled { cursor: not-allowed; opacity: 0.55; }
    .button-secondary { border-color: var(--ds-border-strong); background: var(--ds-surface-2); color: var(--ds-text); }
    .button-secondary:hover { border-color: var(--ds-border-strong); background: var(--ds-surface-hover); }
    .spinner { display: none; width: 15px; height: 15px; border: 2px solid rgba(255, 255, 255, 0.35); border-top-color: #ffffff; border-radius: 50%; animation: spin 0.75s linear infinite; }
    .is-loading .spinner { display: block; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .helper { margin-top: 6px; color: var(--ds-text-muted); font-size: 0.75rem; line-height: 1.55; }
    .actions { display: grid; gap: 8px; margin-top: 8px; }
    .link-row { margin-top: 20px; color: var(--ds-text-muted); font-size: 0.875rem; text-align: center; }
    .link { color: var(--ds-accent-text); font-weight: 500; text-decoration: none; }
    .link:hover { color: var(--ds-text); text-decoration: underline; }
    .resend-row { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 4px 12px; margin: 16px 0; color: var(--ds-text-muted); font-size: 0.8125rem; }
    .resend-button { min-height: 32px; padding: 0; border: 0; background: transparent; color: var(--ds-accent-text); font: 500 0.8125rem/1.2 var(--ds-font-sans); cursor: pointer; }
    .resend-button:hover:not(:disabled) { color: var(--ds-text); text-decoration: underline; }
    .resend-button:disabled { color: var(--ds-text-dim); cursor: not-allowed; }
    .password-rules { margin: 4px 0 20px; padding: 12px 14px; border-radius: var(--ds-radius-sm); background: var(--ds-surface-3); }
    .password-rules strong { display: block; margin-bottom: 4px; color: var(--ds-text); font-size: 0.8125rem; font-weight: 600; }
    .password-rules p { color: var(--ds-text-muted); font-size: 0.8125rem; line-height: 1.55; }

    @media (max-width: 1180px) {
      .shell { grid-template-columns: minmax(0, 1fr) 420px; }
      .left { padding: 44px; }
      .right { padding: 40px; }
    }
    @media (max-width: 900px) {
      .shell { grid-template-columns: minmax(0, 1fr); }
      .left { display: none; }
      .right { min-height: 100vh; padding: 40px 20px; border-left: 0; }
      .card { max-width: 420px; }
    }
    @media (max-width: 480px) {
      .right { align-items: flex-start; padding: 32px 16px; }
    }
  </style>
    @include('partials.page-head')
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
      <h1 class="hero-title">Return to learning<br>with a <em>verified</em><br>password reset</h1>
      <p class="hero-copy">DataSensei uses a short-lived, one-time email code to verify your identity before allowing a password change.</p>
      <div class="security-list">
        <div class="security-item"><span class="security-icon">01</span><span>{{ \App\Support\PasswordOtpConfiguration::length() }}-digit code sent only to the registered email</span></div>
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
