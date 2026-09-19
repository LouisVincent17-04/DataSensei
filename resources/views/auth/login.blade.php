<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DataSensei</title>
<style>
  /* Sign-in and registration. Colours, type and radius come from
     partials.design-system; this block only lays the page out. */
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    /* Page-only names. --surface-2 is also read by the strength meter script. */
    --surface-2: var(--ds-surface-2);
    --line: var(--ds-border);
    --line-2: var(--ds-border-strong);
  }

  html, body {
    height: 100%;
    font-family: var(--ds-font-sans);
    background: var(--ds-bg);
    color: var(--ds-text);
  }

  body { overflow: hidden; }

  /* ── shell ──────────────────────────────────────────── */

  .shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 460px;
    height: 100vh;
    height: 100dvh;
    overflow: hidden;
  }

  /* ── visual side ────────────────────────────────────── */

  .left {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-width: 0;
    padding: 48px 64px;
    overflow-y: auto;
    background: var(--ds-bg);
  }

  .left-inner { width: 100%; max-width: 580px; }

  .brand-row { margin-bottom: 40px; }

  .hero-title {
    max-width: 24ch;
    color: var(--ds-text);
    font-size: 1.625rem;
    font-weight: 700;
    line-height: 1.25;
    letter-spacing: -0.02em;
  }

  .hero-sub {
    max-width: 52ch;
    margin-top: 10px;
    color: var(--ds-text-muted);
    font-size: 0.875rem;
    line-height: 1.6;
  }

  /* ── miniature workspace preview ────────────────────── */

  .preview {
    max-width: 560px;
    margin-top: 32px;
    overflow: hidden;
    border: 1px solid var(--ds-border);
    border-radius: var(--ds-radius-md);
    background: var(--ds-surface);
  }

  .pv-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--ds-border);
  }

  .pv-head h2 {
    min-width: 0;
    overflow: hidden;
    color: var(--ds-text);
    font-size: 0.8125rem;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .pv-mod {
    padding-right: 12px;
    border-right: 1px solid var(--ds-border);
    color: var(--ds-text-muted);
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
  }

  .pv-steps {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    white-space: nowrap;
  }

  .pv-steps small {
    color: var(--ds-text-muted);
    font-size: 0.75rem;
    font-variant-numeric: tabular-nums;
  }

  .pv-bar {
    width: 72px;
    height: 4px;
    overflow: hidden;
    border-radius: 999px;
    background: var(--ds-surface-2);
  }

  .pv-bar span { display: block; width: 58%; height: 100%; border-radius: inherit; background: var(--ds-accent); }

  .pv-thread {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 16px;
    border-bottom: 1px solid var(--ds-border);
  }

  .pv-msg { display: flex; gap: 10px; min-width: 0; transition: opacity 0.35s ease; }
  .pv-msg > div:last-child { min-width: 0; }

  /* Brief dim between loops, so the restart reads as a new answer. */
  .pv-msg.is-resetting { opacity: 0.2; }

  .pv-avatar {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-2);
    color: var(--ds-text-secondary);
    font-size: 0.75rem;
    font-weight: 600;
  }

  .pv-avatar.bot {
    border-color: var(--ds-accent-border);
    background: var(--ds-accent-soft);
    color: var(--ds-accent-text);
  }

  .pv-name {
    margin-bottom: 4px;
    color: var(--ds-text);
    font-size: 0.75rem;
    font-weight: 600;
  }

  .pv-name span { color: var(--ds-text-dim); font-weight: 400; }

  .pv-body {
    max-width: 54ch;
    color: var(--ds-text-secondary);
    font-size: 0.8125rem;
    line-height: 1.55;
  }

  .pv-body + .pv-body { margin-top: 8px; }

  .pv-body code {
    padding: 1px 4px;
    border-radius: var(--ds-radius-xs);
    background: var(--ds-surface-2);
    color: var(--ds-text);
    font-family: var(--ds-font-mono);
    font-size: 0.75rem;
  }

  .pv-snippet {
    margin: 8px 0;
    padding: 8px 12px;
    overflow-x: auto;
    border: 1px solid var(--ds-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-3);
    color: var(--ds-text-secondary);
    font-family: var(--ds-font-mono);
    font-size: 0.75rem;
    line-height: 1.6;
    white-space: pre;
  }

  .pv-snippet .c-str { color: var(--ds-success-text); }
  .pv-snippet .c-fn  { color: var(--ds-accent-text); }

  .pv-msg .pv-table { margin-top: 8px; }

  /* typing indicator + caret */

  .pv-dots {
    display: flex;
    align-items: center;
    gap: 4px;
    height: 20px;
  }

  .pv-dots i {
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: var(--ds-text-muted);
    animation: pvDot 1.3s ease-in-out infinite;
  }

  .pv-dots i:nth-child(2) { animation-delay: 0.18s; }
  .pv-dots i:nth-child(3) { animation-delay: 0.32s; }

  @keyframes pvDot {
    0%, 60%, 100% { opacity: 0.28; }
    30% { opacity: 0.9; }
  }

  .pv-caret {
    display: inline-block;
    width: 6px;
    height: 12px;
    margin-left: 2px;
    vertical-align: -1px;
    background: var(--ds-accent);
    opacity: 0.85;
    animation: pvCaret 1s step-end infinite;
  }

  @keyframes pvCaret {
    0%, 100% { opacity: 0.85; }
    50% { opacity: 0; }
  }

  .pv-reveal {
    opacity: 0;
    transform: translateY(3px);
    transition: opacity 0.35s ease, transform 0.35s ease;
  }

  .pv-reveal.shown { opacity: 1; transform: none; }

  .pv-table {
    width: 100%;
    border-collapse: collapse;
    color: var(--ds-text-secondary);
    font-family: var(--ds-font-mono);
    font-size: 0.75rem;
  }

  .pv-table th {
    padding: 4px 12px 4px 0;
    border-bottom: 1px solid var(--ds-border);
    color: var(--ds-text-muted);
    font-weight: 500;
    text-align: left;
    white-space: nowrap;
  }

  .pv-table td {
    padding: 4px 12px 4px 0;
    border-bottom: 1px solid var(--ds-border);
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
  }

  .pv-table tr:last-child td { border-bottom: none; }

  .pv-checks {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    color: var(--ds-text-secondary);
    font-size: 0.75rem;
  }

  .pv-checks svg { width: 12px; height: 12px; flex-shrink: 0; color: var(--ds-success); }

  .pv-system {
    margin-top: 0;
    padding: 8px 16px;
    border-bottom: 1px solid var(--ds-border);
    background: var(--ds-surface-3);
    font-family: var(--ds-font-mono);
  }

  .pv-composer {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
  }

  .pv-input {
    flex: 1;
    min-width: 0;
    padding: 7px 10px;
    border: 1px solid var(--ds-input-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-3);
    color: var(--ds-text-dim);
    font-size: 0.75rem;
  }

  .pv-send {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--ds-radius-sm);
    background: var(--ds-accent);
    color: #ffffff;
  }

  .pv-send svg { width: 12px; height: 12px; }

  /* ── form side ──────────────────────────────────────── */

  .right {
    display: grid;
    grid-template-rows: auto minmax(0, 1fr);
    justify-items: center;
    min-width: 0;
    padding: 44px 48px;
    overflow-y: auto;
    border-left: 1px solid var(--ds-border);
    background: var(--ds-surface);
  }

  .right #ds-global-notification-stack {
    grid-row: 1;
    align-self: start;
    justify-self: end;
    width: min(420px, 100%);
    max-width: 100%;
    margin: 0 0 16px auto !important;
  }

  .form-card {
    grid-row: 2;
    align-self: center;
    width: 100%;
    max-width: 372px;
    min-width: 0;
  }

  /* ── tabs (underline) ───────────────────────────────── */

  .tabs {
    position: relative;
    display: flex;
    gap: 24px;
    margin-bottom: 28px;
    border-bottom: 1px solid var(--ds-border);
  }

  .tab-btn {
    padding: 4px 0 12px;
    border: none;
    background: none;
    color: var(--ds-text-muted);
    font-family: var(--ds-font-sans);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: color var(--ds-dur-2) ease;
  }

  .tab-btn:hover { color: var(--ds-text); }
  .tab-btn.active { color: var(--ds-text); }

  .tab-btn:focus-visible {
    outline: none;
    color: var(--ds-text);
    border-radius: var(--ds-radius-xs);
    box-shadow: var(--ds-focus-ring);
  }

  .tab-underline {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--ds-accent);
    transition: transform 0.2s var(--ds-ease), width 0.2s var(--ds-ease);
  }

  /* ── alerts ─────────────────────────────────────────── */

  .alert {
    margin-bottom: 16px;
    padding: 12px 16px;
    border: 1px solid var(--ds-accent-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-accent-soft);
    color: #dbeafe;
    font-size: 0.875rem;
    line-height: 1.5;
    overflow-wrap: anywhere;
  }

  .alert-success {
    border-color: var(--ds-success-border);
    background: var(--ds-success-soft);
    color: #d1fae5;
  }

  .alert-danger {
    border-color: var(--ds-danger-border);
    background: var(--ds-danger-soft);
    color: #fee2e2;
  }

  /* ── form ───────────────────────────────────────────── */

  .form-heading {
    margin-bottom: 4px;
    color: var(--ds-text);
    font-size: 1.375rem;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.015em;
  }

  .form-sub {
    margin-bottom: 24px;
    color: var(--ds-text-muted);
    font-size: 0.875rem;
    line-height: 1.55;
  }

  .field { margin-bottom: 16px; }

  .field-top {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 6px;
  }

  .field-label {
    color: var(--ds-text-secondary);
    font-size: 0.8125rem;
    font-weight: 500;
  }

  .field-wrap { position: relative; }

  .field-input {
    width: 100%;
    min-height: 40px;
    padding: 8px 12px;
    border: 1px solid var(--ds-input-border);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-surface-3);
    color: var(--ds-text);
    font-family: var(--ds-font-sans);
    font-size: 0.875rem;
    outline: none;
    transition: border-color var(--ds-dur-2) ease, box-shadow var(--ds-dur-2) ease;
    -webkit-appearance: none;
  }

  .field-input::placeholder { color: var(--ds-text-dim); }
  .field-input:hover { border-color: var(--ds-border-strong); }

  .field-input:focus {
    border-color: var(--ds-accent);
    box-shadow: var(--ds-focus-ring);
  }

  .pwd-toggle {
    position: absolute;
    top: 50%;
    right: 4px;
    min-height: 32px;
    padding: 0 10px;
    border: none;
    border-radius: var(--ds-radius-xs);
    background: none;
    color: var(--ds-text-muted);
    font-family: var(--ds-font-sans);
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transform: translateY(-50%);
    transition: color var(--ds-dur-2) ease;
  }

  .pwd-toggle:hover { color: var(--ds-text); }

  .pwd-toggle:focus-visible {
    outline: none;
    color: var(--ds-text);
    box-shadow: var(--ds-focus-ring);
  }

  .caps-note {
    display: none;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
    color: var(--ds-warning-text);
    font-size: 0.75rem;
  }

  .caps-note.visible { display: flex; }

  .caps-note i {
    width: 6px;
    height: 6px;
    flex-shrink: 0;
    border-radius: 50%;
    background: var(--ds-warning);
  }

  .form-extras {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px 12px;
    margin: 20px 0 24px;
  }

  .check-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 32px;
    color: var(--ds-text-secondary);
    font-size: 0.875rem;
    cursor: pointer;
    user-select: none;
    transition: color var(--ds-dur-2) ease;
  }

  .check-label:hover { color: var(--ds-text); }

  .check-box {
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--ds-border-strong);
    border-radius: var(--ds-radius-xs);
    background: var(--ds-surface-3);
    transition: background var(--ds-dur-2) ease, border-color var(--ds-dur-2) ease;
  }

  .check-label input[type="checkbox"] { position: absolute; width: 0; height: 0; opacity: 0; }

  .check-label input:checked + .check-box {
    border-color: var(--ds-accent);
    background: var(--ds-accent);
  }

  .check-label input:checked + .check-box::after {
    content: '';
    display: block;
    width: 8px;
    height: 4px;
    border-bottom: 2px solid #ffffff;
    border-left: 2px solid #ffffff;
    transform: rotate(-45deg) translateY(-1px);
  }

  .check-label input:focus-visible + .check-box { box-shadow: var(--ds-focus-ring); }

  .forgot-link {
    color: var(--ds-accent-text);
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    white-space: nowrap;
    transition: color var(--ds-dur-2) ease;
  }

  .forgot-link:hover { color: var(--ds-text); text-decoration: underline; }

  .cta-btn {
    width: 100%;
    min-height: 40px;
    padding: 0 16px;
    border: 1px solid var(--ds-accent);
    border-radius: var(--ds-radius-sm);
    background: var(--ds-accent);
    color: #ffffff;
    font-family: var(--ds-font-sans);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background var(--ds-dur-2) ease, border-color var(--ds-dur-2) ease;
  }

  .cta-btn:hover,
  .cta-btn:active { border-color: var(--ds-accent-strong); background: var(--ds-accent-strong); }

  .cta-btn:focus-visible { outline: none; box-shadow: var(--ds-focus-ring); }

  .cta-btn:disabled {
    border-color: var(--ds-border-strong);
    background: var(--ds-surface-2);
    color: var(--ds-text-muted);
    cursor: default;
  }

  .strength-wrap { display: none; margin-top: 8px; }
  .strength-wrap.visible { display: block; }

  .strength-bars { display: flex; gap: 4px; margin-bottom: 6px; }

  .strength-bar {
    flex: 1;
    height: 4px;
    border-radius: 999px;
    background: var(--surface-2);
    transition: background 0.2s ease;
  }

  .strength-label { color: var(--ds-text-muted); font-size: 0.75rem; font-weight: 500; }

  .field-error {
    display: none;
    margin-top: 6px;
    color: var(--ds-danger-text);
    font-size: 0.75rem;
    line-height: 1.45;
  }

  .field.has-error .field-input { border-color: var(--ds-danger); }
  .field.has-error .field-error { display: block; }

  .client-error {
    display: none;
    margin-top: 6px;
    color: var(--ds-danger-text);
    font-size: 0.75rem;
    line-height: 1.45;
  }

  .field.client-invalid .field-input { border-color: var(--ds-danger); }
  .field.client-invalid .client-error { display: block; }

  .form-panel { display: none; }
  .form-panel.active { display: block; }

  .note-text {
    margin-top: 16px;
    color: var(--ds-text-muted);
    font-size: 0.8125rem;
    line-height: 1.6;
  }

  .switch-row {
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--ds-border);
    color: var(--ds-text-muted);
    font-size: 0.875rem;
  }

  .note-text a,
  .link-btn {
    color: var(--ds-accent-text);
    font-weight: 500;
    text-decoration: none;
    transition: color var(--ds-dur-2) ease;
  }

  .link-btn {
    padding: 0;
    border: 0;
    background: transparent;
    font: inherit;
    font-weight: 500;
    cursor: pointer;
    appearance: none;
  }

  .note-text a:hover,
  .link-btn:hover { color: var(--ds-text); text-decoration: underline; }

  /* ── responsive ─────────────────────────────────────── */

  @media (max-width: 1180px) {
    .shell { grid-template-columns: minmax(0, 1fr) 420px; }
    .left { padding: 44px 44px; }
    .right { padding: 40px 40px; }
  }

  /* One column: the page scrolls normally and the form follows the intro. */
  @media (max-width: 980px) {
    html, body { height: auto; min-height: 100%; }
    body { overflow-x: hidden; overflow-y: auto; }

    /* The intro keeps its height and the form fills the rest from the top,
       so an inline hint appearing never shifts the tabs under the pointer. */
    .shell {
      grid-template-columns: minmax(0, 1fr);
      grid-template-rows: auto 1fr;
      height: auto;
      min-height: 100vh;
      overflow: visible;
    }

    .left {
      padding: 36px 40px 32px;
      overflow: visible;
      border-bottom: 1px solid var(--ds-border);
    }

    .brand-row { margin-bottom: 28px; }
    .preview { display: none; }

    .right {
      padding: 32px 40px 48px;
      overflow: visible;
      justify-items: start;
      border-left: none;
    }

    .form-card { max-width: 420px; align-self: start; }
  }

  @media (max-width: 560px) {
    .left { padding: 28px 16px 24px; }
    .right { padding: 28px 16px 40px; }
    .brand-row { margin-bottom: 24px; }
    .hero-title { font-size: 1.375rem; }
  }
</style>
    @include('partials.page-head', ['pageDescription' => 'Sign in to DataSensei to continue your data science coursework.'])
</head>
<body>

@php
    $isRegisterTab =
        old('name') ||
        $errors->has('name') ||
        $errors->has('password_confirmation') ||
        request()->is('register');
@endphp

<div class="shell">
  <div class="left">
    <div class="left-inner">
      <div class="brand-row">
        @include('partials.brand-logo', [
          'variant' => 'auth',
          'size' => 'large',
          'subtext' => 'Data Science Learning Platform',
        ])
      </div>

      <h1 class="hero-title">Data Science, learned by doing.</h1>

      <p class="hero-sub">
        Write code, work with real datasets, complete lessons and assessments,
        and review feedback as you learn.
      </p>

      <div class="preview" aria-hidden="true">
        <div class="pv-head">
          <span class="pv-mod">Module 6</span>
          <h2>Handling missing values with pandas</h2>
          <span class="pv-steps">
            <span class="pv-bar"><span></span></span>
            <small>7 / 12</small>
          </span>
        </div>

        <div class="pv-checks pv-system">
          <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6">
            <path d="M3 8.4 L6.4 11.6 L13 4.8"/>
          </svg>
          Cell ran in 0.42s, 2 of 2 checks passed
        </div>
        <?php date_default_timezone_set('Asia/Manila'); ?>
        <div class="pv-thread">
          <div class="pv-msg">
            <div class="pv-avatar">LT</div>
            <div>
              <div class="pv-name">Louis Tajanlangit <span>{{date('h:i A')}}</span></div>
              <p class="pv-body">My checks pass, but the mean of the column changed after this line:</p>
              <div class="pv-snippet">df[<span class="c-str">"lot_frontage"</span>].<span class="c-fn">fillna</span>(median)</div>
              <p class="pv-body">Did I fill it the wrong way?</p>
            </div>
          </div>

          <div class="pv-msg" id="pv-reply">
            <div class="pv-avatar bot">DS</div>
            <div>
              <div class="pv-name">DataSensei Assistant</div>
              <div class="pv-dots" data-dots hidden><i></i><i></i><i></i></div>
              <p class="pv-body" data-type>No, that shift is expected. You replaced 259 missing values with the median, so the mean moves a little while the median stays where it was:</p>
              <table class="pv-table pv-reveal" data-reveal>
                <thead>
                  <tr><th>lot_frontage</th><th>before</th><th>after</th></tr>
                </thead>
                <tbody>
                  <tr><td>mean</td><td>70.05</td><td>69.86</td></tr>
                  <tr><td>median</td><td>69.00</td><td>69.00</td></tr>
                  <tr><td>missing</td><td>259</td><td>0</td></tr>
                </tbody>
              </table>
              <p class="pv-body" data-type>Filling with <code>mean()</code> instead would pull the column toward the outliers. Step 8 asks you to compare both.</p>
            </div>
          </div>
        </div>

        <div class="pv-composer">
          <span class="pv-input">Ask about this lesson</span>
          <span class="pv-send">
            <svg viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 7h9M7.5 3.2 11.4 7l-3.9 3.8"/>
            </svg>
          </span>
        </div>
      </div>
    </div>
  </div>

  <div class="right auth-page-notification-host">
    <div class="form-card">
      <div class="tabs">
        <button class="tab-btn {{ !$isRegisterTab ? 'active' : '' }}" onclick="switchTab('login')" type="button">Sign in</button>
        <button class="tab-btn {{ $isRegisterTab ? 'active' : '' }}" onclick="switchTab('register')" type="button">Create account</button>
        <span class="tab-underline" id="tab-underline"></span>
      </div>

      @if (session('status') || request()->boolean('expired'))
        <div class="alert alert-success" data-ds-global-notification role="status">
          {{ session('status') ?: 'Your session expired because there was no activity. Please sign in again.' }}
        </div>
      @endif

      @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
      @endif

      @if ($errors->has('general'))
        <div class="alert alert-danger">
          {{ $errors->first('general') }}
        </div>
      @endif

      <div class="alert alert-danger" id="auth-submit-error" role="alert" tabindex="-1" hidden></div>

      <div class="form-panel {{ !$isRegisterTab ? 'active' : '' }}" id="panel-login">
        <form method="POST" action="{{ Route::has('login') ? route('login') : url('/login') }}">
          @csrf

          <h2 class="form-heading">Sign in</h2>
          <p class="form-sub">Use your institution account to continue.</p>

          <div class="field @error('email') has-error @enderror" id="f-email-login">
            <div class="field-top">
              <label class="field-label" for="login-email">Email</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="email"
                name="email"
                id="login-email"
                value="{{ old('email') }}"
                placeholder="you@university.edu"
                required
                autofocus
                autocomplete="username"
                data-validate="email"
              >
            </div>
            <div class="client-error"></div>
            @error('email')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror" id="f-pwd-login">
            <div class="field-top">
              <label class="field-label" for="login-pwd">Password</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="password"
                name="password"
                id="login-pwd"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
                style="padding-right: 56px;"
                data-validate="required"
                data-caps="caps-login"
              >
              <button class="pwd-toggle" onclick="togglePwd('login-pwd', this)" type="button" aria-label="Show password">Show</button>
            </div>
            <div class="caps-note" id="caps-login"><i></i>Caps Lock is on</div>
            <div class="client-error"></div>
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
            @else
              <a href="{{ url('/forgot-password') }}" class="forgot-link">Forgot password?</a>
            @endif
          </div>

          <button type="submit" class="cta-btn" id="login-btn">Sign in</button>
        </form>

        <p class="note-text switch-row">
          Don't have an account? <button class="link-btn" type="button" onclick="switchTab('register')">Create one</button>
        </p>
      </div>

      <div class="form-panel {{ $isRegisterTab ? 'active' : '' }}" id="panel-register">
        <form method="POST" action="{{ Route::has('register') ? route('register') : url('/register') }}">
          @csrf

          <h2 class="form-heading">Create account</h2>
          <p class="form-sub">
            Create your learner account first. You can apply to an institution after signing in.
          </p>

          <div class="field @error('name') has-error @enderror" id="f-name-reg">
            <div class="field-top">
              <label class="field-label" for="reg-name">Full name</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="text"
                name="name"
                id="reg-name"
                value="{{ old('name') }}"
                placeholder="Juan dela Cruz"
                required
                autocomplete="name"
                data-validate="required"
              >
            </div>
            <div class="client-error"></div>
            @error('name')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('email') has-error @enderror" id="f-email-reg">
            <div class="field-top">
              <label class="field-label" for="reg-email">Email</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="email"
                name="email"
                id="reg-email"
                value="{{ old('email') }}"
                placeholder="you@university.edu"
                required
                autocomplete="email"
                data-validate="email"
              >
            </div>
            <div class="client-error"></div>
            @error('email')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <div class="field @error('password') has-error @enderror" id="f-pwd-reg">
            <div class="field-top">
              <label class="field-label" for="reg-pwd">Password</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="password"
                name="password"
                id="reg-pwd"
                placeholder="8+ chars with upper/lowercase, number, symbol"
                required
                autocomplete="new-password"
                style="padding-right: 56px;"
                oninput="checkStrength(this.value)"
                data-caps="caps-reg"
              >
              <button class="pwd-toggle" onclick="togglePwd('reg-pwd', this)" type="button" aria-label="Show password">Show</button>
            </div>
            <div class="caps-note" id="caps-reg"><i></i>Caps Lock is on</div>

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

          <div class="field @error('password_confirmation') has-error @enderror" id="f-pwd-conf-reg">
            <div class="field-top">
              <label class="field-label" for="reg-pwd-conf">Confirm password</label>
            </div>
            <div class="field-wrap">
              <input
                class="field-input"
                type="password"
                name="password_confirmation"
                id="reg-pwd-conf"
                placeholder="Type password again"
                required
                autocomplete="new-password"
                style="padding-right: 56px;"
                data-match="reg-pwd"
                data-caps="caps-conf"
              >
              <button class="pwd-toggle" onclick="togglePwd('reg-pwd-conf', this)" type="button" aria-label="Show password">Show</button>
            </div>
            <div class="caps-note" id="caps-conf"><i></i>Caps Lock is on</div>
            <div class="client-error"></div>
            @error('password_confirmation')
              <div class="field-error" style="display:block;">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="cta-btn" id="reg-btn" style="margin-top: 24px;">Create account</button>
        </form>

        <p class="note-text">
          Create an account only if you are authorized to use DataSensei. Your institution may apply its own terms and privacy policy.
        </p>

        <p class="note-text switch-row">
          Already have an account? <button class="link-btn" type="button" onclick="switchTab('login')">Sign in</button>
        </p>
      </div>
    </div>
  </div>
</div>

<script>
/* ── assistant preview: typing effect ───────────────── */

(function(){
  const reply = document.getElementById('pv-reply');
  if (!reply) return;

  const dots = reply.querySelector('[data-dots]');
  const typed = Array.from(reply.querySelectorAll('[data-type]'));
  const reveals = Array.from(reply.querySelectorAll('[data-reveal]'));

  const showEverything = () => {
    if (dots) dots.hidden = true;
    reveals.forEach(el => el.classList.add('shown'));
  };

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // The preview is hidden on small screens, so there is nothing to animate.
  if (reducedMotion || reply.offsetParent === null) {
    showEverything();
    return;
  }

  // Keep the original markup so the message survives with JavaScript disabled.
  const originals = typed.map(el => {
    const fragment = document.createDocumentFragment();
    while (el.firstChild) fragment.appendChild(el.firstChild);
    el.style.visibility = 'hidden';
    return fragment;
  });

  // If anything ever throws mid-loop, put the finished answer back on screen.
  const restoreAnswer = () => {
    typed.forEach((element, index) => {
      element.textContent = '';
      element.appendChild(originals[index].cloneNode(true));
      element.style.visibility = '';
    });

    showEverything();
  };

  const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

  function typeNodes(target, source, speed){
    const nodes = Array.from(source.childNodes);

    return nodes.reduce((chain, node) => chain.then(() => {
      if (node.nodeType === Node.TEXT_NODE) {
        const text = node.textContent;
        const holder = document.createTextNode('');
        target.appendChild(holder);

        return text.split('').reduce((inner, character) => inner.then(() => {
          holder.textContent += character;
          return wait(character === ' ' ? speed * 0.5 : speed);
        }), Promise.resolve());
      }

      const clone = node.cloneNode(false);
      target.appendChild(clone);
      return typeNodes(clone, node, speed);
    }), Promise.resolve());
  }

  // Type the whole answer once, from empty to finished.
  async function typeAnswer(){
    if (dots) {
      dots.hidden = false;
      await wait(900);
      dots.hidden = true;
    }

    for (let i = 0; i < typed.length; i++) {
      const element = typed[i];
      const holder = document.createElement('span');
      const caret = document.createElement('span');
      caret.className = 'pv-caret';

      element.style.visibility = '';
      element.appendChild(holder);
      element.appendChild(caret);

      await typeNodes(holder, originals[i], 17);
      await wait(260);
      caret.remove();

      if (i === 0) {
        reveals.forEach(el => el.classList.add('shown'));
        await wait(420);
      }
    }
  }

  // Clear the answer back to its starting state for the next pass. The captured
  // fragments are only ever read, never moved, so they stay reusable forever.
  function clearAnswer(){
    typed.forEach(element => {
      element.textContent = '';
      element.style.visibility = 'hidden';
    });

    reveals.forEach(el => el.classList.remove('shown'));
  }

  // Never animate into a background tab; wait until the page is looked at again.
  function whenVisible(){
    if (! document.hidden) return Promise.resolve();

    return new Promise(resolve => {
      const onChange = () => {
        if (document.hidden) return;
        document.removeEventListener('visibilitychange', onChange);
        resolve();
      };

      document.addEventListener('visibilitychange', onChange);
    });
  }

  const HOLD_FINISHED_MS = 3200; // long enough to read the finished answer
  const BLANK_PAUSE_MS = 620;    // beat between clearing and typing again

  async function loop(){
    // eslint-disable-next-line no-constant-condition
    while (true) {
      await whenVisible();

      // Skip a pass entirely while the preview is off-screen (narrow layouts).
      if (reply.offsetParent === null) {
        await wait(1000);
        continue;
      }

      await typeAnswer();
      await wait(HOLD_FINISHED_MS);

      reply.classList.add('is-resetting');
      await wait(320);
      clearAnswer();
      reply.classList.remove('is-resetting');
      await wait(BLANK_PAUSE_MS);
    }
  }

  clearAnswer();
  loop().catch(restoreAnswer);
})();

/* ── tab underline ──────────────────────────────────── */

function moveUnderline(){
  const underline = document.getElementById('tab-underline');
  const active = document.querySelector('.tab-btn.active');

  if (!underline || !active) return;

  underline.style.width = active.offsetWidth + 'px';
  underline.style.transform = `translateX(${active.offsetLeft}px)`;
}

window.addEventListener('resize', moveUnderline, { passive: true });
window.addEventListener('load', moveUnderline);
if (document.fonts && document.fonts.ready) document.fonts.ready.then(moveUnderline);
moveUnderline();

/* ── tabs ───────────────────────────────────────────── */

function switchTab(tab){
  document.querySelectorAll('.tab-btn').forEach((button, index) => {
    button.classList.toggle('active', tab === 'login' ? index === 0 : index === 1);
  });

  document.querySelectorAll('.form-panel').forEach(panel => {
    panel.classList.remove('active');
  });

  document.getElementById(`panel-${tab}`).classList.add('active');

  moveUnderline();

  document.title = tab === 'login'
    ? 'DataSensei — Sign In'
    : 'DataSensei — Create Account';

  const firstInput = document.querySelector(`#panel-${tab} .field-input`);
  if (firstInput) firstInput.focus({ preventScroll: true });
}

/* ── show / hide password ───────────────────────────── */

function togglePwd(id, btn){
  const input = document.getElementById(id);
  const isText = input.type === 'text';

  input.type = isText ? 'password' : 'text';
  btn.textContent = isText ? 'Show' : 'Hide';
  btn.setAttribute('aria-label', isText ? 'Show password' : 'Hide password');
}

/* ── caps lock indicator ────────────────────────────── */

document.querySelectorAll('[data-caps]').forEach(input => {
  const note = document.getElementById(input.dataset.caps);
  if (!note) return;

  const update = event => {
    const on = typeof event.getModifierState === 'function' && event.getModifierState('CapsLock');
    note.classList.toggle('visible', !!on);
  };

  input.addEventListener('keydown', update);
  input.addEventListener('keyup', update);
  input.addEventListener('blur', () => note.classList.remove('visible'));
});

/* ── inline hints (display only — server validation is unchanged) ── */

const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

function setHint(input, message){
  const field = input.closest('.field');
  const box = field && field.querySelector('.client-error');
  if (!field || !box) return;

  if (message) {
    box.textContent = message;
    field.classList.add('client-invalid');
  } else {
    box.textContent = '';
    field.classList.remove('client-invalid');
  }
}

document.querySelectorAll('[data-validate]').forEach(input => {
  input.addEventListener('blur', () => {
    const value = input.value.trim();

    if (!value) {
      setHint(input, 'This field is required.');
      return;
    }

    if (input.dataset.validate === 'email' && !emailPattern.test(value)) {
      setHint(input, 'Enter a valid email address.');
      return;
    }

    setHint(input, '');
  });

  input.addEventListener('input', () => setHint(input, ''));
});

document.querySelectorAll('[data-match]').forEach(input => {
  const source = document.getElementById(input.dataset.match);
  if (!source) return;

  const compare = () => {
    if (!input.value) return setHint(input, '');
    setHint(input, input.value === source.value ? '' : 'Passwords do not match.');
  };

  input.addEventListener('input', compare);
  input.addEventListener('blur', compare);
  source.addEventListener('input', () => { if (input.value) compare(); });
});

/* ── password strength ──────────────────────────────── */

function checkStrength(value){
  const wrap = document.getElementById('strength-wrap');
  const label = document.getElementById('strength-label');
  const bars = [1, 2, 3, 4].map(i => document.getElementById('sb' + i));

  if(!value){
    wrap.classList.remove('visible');
    return;
  }

  wrap.classList.add('visible');

  let score = 0;

  if(value.length >= 8) score++;
  if(/[A-Z]/.test(value)) score++;
  if(/[0-9]/.test(value)) score++;
  if(/[^A-Za-z0-9]/.test(value)) score++;

  const colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6'];
  const labels = ['Weak', 'Fair', 'Good', 'Strong'];

  bars.forEach((bar, index) => {
    bar.style.background = index < score ? colors[score - 1] : 'var(--surface-2)';
  });

  label.textContent = labels[score - 1] || 'Weak';
  label.style.color = colors[score - 1] || colors[0];
}
</script>
<script id="datasensei-auth-session-script">
(() => {
  const forms = [...document.querySelectorAll('.form-panel form')];
  const buttons = forms.map(form => form.querySelector('button[type="submit"]'));
  const error = document.getElementById('auth-submit-error');
  let submitting = false;
  let activeRequest = null;

  const resetSubmitState = () => {
    submitting = false;
    forms.forEach(form => form.removeAttribute('aria-busy'));
    buttons.forEach(button => {
      button.disabled = false;
      button.textContent = button.id === 'reg-btn' ? 'Create account' : 'Sign in';
    });
  };

  const refreshSession = async signal => {
    for (let attempt = 0; attempt < 2; attempt++) {
      const response = await fetch(@json(route('login')), {
        method: 'GET',
        credentials: 'same-origin',
        mode: 'same-origin',
        cache: 'no-store',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        signal,
      });

      // A suspended tab may reach the server before idle logout completes.
      // That response clears the old session; retry once for the new token.
      if (attempt === 0 && (response.status === 401 || response.status === 419)) continue;
      if (!response.ok) throw new Error('Session refresh failed.');

      return response;
    }
  };

  forms.forEach(form => {
    form.addEventListener('submit', async event => {
      if (event.defaultPrevented) return;
      event.preventDefault();
      if (submitting) return;

      submitting = true;
      error.hidden = true;
      form.setAttribute('aria-busy', 'true');
      buttons.forEach(button => { button.disabled = true; });
      const button = form.querySelector('button[type="submit"]');
      button.textContent = button.id === 'reg-btn' ? 'Creating account…' : 'Signing in…';

      const controller = new AbortController();
      activeRequest = controller;
      const timer = window.setTimeout(() => controller.abort(), 10000);

      try {
        const response = await refreshSession(controller.signal);
        if (activeRequest !== controller) return;

        // An existing sign-in in another tab follows the usual dashboard redirect.
        if (response.redirected && !(response.headers.get('Content-Type') || '').includes('application/json')) {
          const destination = new URL(response.url, window.location.href);
          if (destination.origin !== window.location.origin) throw new Error('Invalid redirect.');
          window.location.assign(destination.href);
          return;
        }

        const data = await response.json();
        if (activeRequest !== controller) return;
        if (typeof data.csrf_token !== 'string' || data.csrf_token.length === 0) {
          throw new Error('Missing session token.');
        }

        forms.forEach(authForm => {
          authForm.querySelector('input[name="_token"]').value = data.csrf_token;
        });
        const meta = document.querySelector('meta[name="csrf-token"]');
        if (meta) meta.content = data.csrf_token;

        if (!form.reportValidity()) {
          resetSubmitState();
          return;
        }

        // Keep the normal POST, validation errors, remember-me and role redirects.
        HTMLFormElement.prototype.submit.call(form);
      } catch (_) {
        if (activeRequest !== controller) return;
        resetSubmitState();
        error.textContent = 'We could not submit the form. Please try again.';
        error.hidden = false;
        error.focus();
      } finally {
        window.clearTimeout(timer);
        if (activeRequest === controller) activeRequest = null;
      }
    });
  });

  window.addEventListener('pageshow', event => {
    if (!event.persisted) return;
    activeRequest?.abort();
    activeRequest = null;
    error.hidden = true;
    resetSubmitState();
  });
})();
</script>
</body>
</html>