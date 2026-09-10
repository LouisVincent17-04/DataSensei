<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>DataSensei</title>
@include('partials.brand-head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    /* --- original palette, untouched --- */
    --bg:          #080c14;
    --panel:       #0d1320;
    --surface:     #111827;
    --surface-2:   #1a2232;
    --border:      rgba(255,255,255,0.07);
    --border-active: rgba(99,179,237,0.5);
    --accent:      #63b3ed;
    --green:       #68d391;
    --amber:       #f6ad55;
    --text-1:      #f0f4ff;
    --text-2:      #8a99b3;
    --text-3:      #4a5568;
    --danger:      #fc8181;
    --success:     #68d391;

    --sunken:      #060a11;
    --accent-lo:   #3182ce;
    --accent-dk:   #2c5282;
    --line:        rgba(255,255,255,0.07);
    --line-2:      rgba(255,255,255,0.11);

    --radius:      6px;
    --radius-sm:   4px;

    --font-body:   'Instrument Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
    --font-mono:   'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
  }

  html, body {
    height: 100%;
    font-family: var(--font-body);
    background: var(--bg);
    color: var(--text-1);
    -webkit-font-smoothing: antialiased;
  }

  body { overflow: hidden; }

  ::selection { background: rgba(99,179,237,0.25); }

  /* ── shell ──────────────────────────────────────────── */

  .shell {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 460px;
    height: 100vh;
    overflow: hidden;
  }

  /* ── left ───────────────────────────────────────────── */

  .left {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 48px 64px;
    overflow-y: auto;
  }

  .left-inner { width: 100%; max-width: 620px; }

  .brand-row { margin-bottom: 44px; }

  .hero-title {
    font-size: clamp(26px, 2.5vw, 34px);
    font-weight: 600;
    line-height: 1.15;
    letter-spacing: -0.022em;
    color: var(--text-1);
  }

  .hero-sub {
    margin-top: 12px;
    font-size: 14.5px;
    line-height: 1.65;
    color: var(--text-2);
    max-width: 50ch;
  }

  /* ── miniature workspace preview ────────────────────── */

  .preview {
    margin-top: 32px;
    max-width: 560px;
    border: 1px solid var(--line-2);
    border-radius: var(--radius);
    background: var(--panel);
    overflow: hidden;
  }

  .pv-head {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 14px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
  }

  .pv-head h2 {
    font-size: 13px;
    font-weight: 600;
    letter-spacing: -0.008em;
    color: var(--text-1);
  }

  .pv-mod {
    font-family: var(--font-mono);
    font-size: 10.5px;
    color: var(--text-3);
    padding-right: 12px;
    border-right: 1px solid var(--line-2);
    white-space: nowrap;
  }

  .pv-steps {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 9px;
    white-space: nowrap;
  }

  .pv-steps small {
    font-size: 11px;
    color: var(--text-2);
    font-variant-numeric: tabular-nums;
  }

  .pv-bar {
    width: 74px;
    height: 3px;
    background: var(--surface-2);
    border-radius: 2px;
    overflow: hidden;
  }

  .pv-bar span { display: block; height: 100%; width: 58%; background: var(--accent); }

  .pv-thread {
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 15px 14px;
    border-bottom: 1px solid var(--border);
  }

  .pv-msg { display: flex; gap: 10px; }

  .pv-avatar {
    width: 24px;
    height: 24px;
    border-radius: var(--radius-sm);
    background: var(--surface-2);
    border: 1px solid var(--line-2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9.5px;
    font-weight: 600;
    color: var(--text-2);
    flex-shrink: 0;
  }

  .pv-avatar.bot {
    background: rgba(99,179,237,0.12);
    border-color: rgba(99,179,237,0.32);
    color: var(--accent);
  }

  .pv-name {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-1);
    margin-bottom: 4px;
  }

  .pv-name span { font-weight: 400; color: var(--text-3); }

  .pv-body {
    font-size: 12.5px;
    line-height: 1.6;
    color: var(--text-2);
    max-width: 54ch;
  }

  .pv-body + .pv-body { margin-top: 8px; }

  .pv-body code {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-1);
    background: var(--surface-2);
    padding: 1px 5px;
    border-radius: 3px;
  }

  .pv-snippet {
    margin: 8px 0;
    padding: 9px 11px;
    background: var(--sunken);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    font-family: var(--font-mono);
    font-size: 11px;
    line-height: 1.7;
    color: var(--text-2);
    white-space: pre;
    overflow-x: auto;
  }

  .pv-snippet .c-str { color: var(--green); }
  .pv-snippet .c-fn  { color: #b9c6de; }

  .pv-msg .pv-table { margin-top: 9px; }

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
    background: var(--text-3);
    animation: pvDot 1.3s ease-in-out infinite;
  }

  .pv-dots i:nth-child(2) { animation-delay: 0.16s; }
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
    background: var(--accent);
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
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-2);
  }

  .pv-table th {
    text-align: left;
    font-weight: 500;
    color: var(--text-3);
    padding: 4px 12px 5px 0;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
  }

  .pv-table td {
    padding: 4px 12px 4px 0;
    border-bottom: 1px solid rgba(255,255,255,0.04);
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
  }

  .pv-table tr:last-child td { border-bottom: none; }

  .pv-checks {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-top: 11px;
    font-size: 11.5px;
    color: var(--text-2);
  }

  .pv-checks svg { width: 12px; height: 12px; color: var(--green); flex-shrink: 0; }

  .pv-system {
    margin-top: 0;
    padding: 9px 14px;
    border-bottom: 1px solid var(--border);
    background: rgba(104,211,145,0.045);
    font-family: var(--font-mono);
    font-size: 10.5px;
  }

  .pv-composer {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 14px;
    background: var(--surface);
  }

  .pv-input {
    flex: 1;
    padding: 7px 10px;
    border: 1px solid var(--line-2);
    border-radius: var(--radius-sm);
    background: var(--sunken);
    font-size: 12px;
    color: var(--text-3);
  }

  .pv-send {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: var(--radius-sm);
    background: var(--accent);
    color: #06121f;
    flex-shrink: 0;
  }

  .pv-send svg { width: 12px; height: 12px; }

  /* ── right ──────────────────────────────────────────── */

  .right {
    background: var(--panel);
    border-left: 1px solid var(--line-2);
    display: grid;
    grid-template-rows: auto minmax(0, 1fr);
    justify-items: center;
    padding: 44px 48px;
    overflow-y: auto;
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
  }

  /* ── tabs ───────────────────────────────────────────── */

  .tabs {
    position: relative;
    display: flex;
    gap: 26px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 30px;
  }

  .tab-btn {
    padding: 0 0 12px;
    border: none;
    background: none;
    color: var(--text-3);
    font-family: var(--font-body);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.18s ease;
  }

  .tab-btn:hover { color: var(--text-2); }
  .tab-btn.active { color: var(--text-1); }

  .tab-btn:focus-visible {
    outline: none;
    color: var(--text-1);
    box-shadow: 0 0 0 3px rgba(99,179,237,0.18);
    border-radius: 2px;
  }

  .tab-underline {
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--accent);
    transition: transform 0.24s cubic-bezier(0.4, 0, 0.2, 1), width 0.24s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ── alerts ─────────────────────────────────────────── */

  .alert {
    border-radius: var(--radius-sm);
    padding: 11px 13px;
    font-size: 12.5px;
    line-height: 1.55;
    margin-bottom: 18px;
    border-left: 2px solid;
  }

  .alert-success {
    background: rgba(104,211,145,0.07);
    border-color: var(--success);
    color: #a5e5bf;
  }

  .alert-danger {
    background: rgba(252,129,129,0.07);
    border-color: var(--danger);
    color: #f5b0b0;
  }

  /* ── form ───────────────────────────────────────────── */

  .form-heading {
    font-size: 21px;
    font-weight: 600;
    letter-spacing: -0.018em;
    color: var(--text-1);
    margin-bottom: 6px;
  }

  .form-sub {
    font-size: 13.5px;
    color: var(--text-2);
    margin-bottom: 26px;
    line-height: 1.55;
  }

  .field { margin-bottom: 17px; }

  .field-top {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 7px;
  }

  .field-label {
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-2);
  }

  .field-wrap { position: relative; }

  .field-input {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--line-2);
    border-radius: var(--radius-sm);
    padding: 11px 13px;
    font-family: var(--font-body);
    font-size: 14px;
    color: var(--text-1);
    outline: none;
    transition: border-color 0.16s ease, box-shadow 0.16s ease;
    -webkit-appearance: none;
  }

  .field-input::placeholder { color: var(--text-3); font-size: 13.5px; }

  .field-input:hover { border-color: rgba(255,255,255,0.16); }

  .field-input:focus {
    border-color: var(--border-active);
    box-shadow: 0 0 0 3px rgba(99,179,237,0.10);
  }

  .pwd-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-3);
    font-family: var(--font-body);
    font-size: 11.5px;
    font-weight: 500;
    letter-spacing: 0.02em;
    padding: 4px 5px;
    border-radius: 3px;
    transition: color 0.16s ease;
  }

  .pwd-toggle:hover { color: var(--text-1); }

  .pwd-toggle:focus-visible {
    outline: none;
    color: var(--text-1);
    box-shadow: 0 0 0 2px rgba(99,179,237,0.3);
  }

  .caps-note {
    display: none;
    align-items: center;
    gap: 6px;
    margin-top: 7px;
    font-size: 11.5px;
    color: var(--amber);
  }

  .caps-note.visible { display: flex; }

  .caps-note i {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: var(--amber);
    flex-shrink: 0;
  }

  .form-extras {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 20px 0 22px;
  }

  .check-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 13px;
    color: var(--text-2);
    user-select: none;
    transition: color 0.16s ease;
  }

  .check-label:hover { color: var(--text-1); }

  .check-box {
    width: 16px;
    height: 16px;
    border: 1px solid var(--line-2);
    border-radius: 3px;
    background: var(--surface);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.16s ease, border-color 0.16s ease;
    flex-shrink: 0;
  }

  .check-label input[type="checkbox"] { position: absolute; opacity: 0; width: 0; height: 0; }

  .check-label input:checked + .check-box {
    background: var(--accent);
    border-color: var(--accent);
  }

  .check-label input:checked + .check-box::after {
    content: '';
    display: block;
    width: 8px;
    height: 4px;
    border-left: 2px solid #06121f;
    border-bottom: 2px solid #06121f;
    transform: rotate(-45deg) translateY(-1px);
  }

  .check-label input:focus-visible + .check-box {
    box-shadow: 0 0 0 3px rgba(99,179,237,0.18);
  }

  .forgot-link {
    font-size: 13px;
    color: var(--text-2);
    text-decoration: none;
    white-space: nowrap;
    transition: color 0.16s ease;
  }

  .forgot-link:hover { color: var(--accent); }

  .cta-btn {
    width: 100%;
    padding: 12px;
    background: var(--accent);
    border: 1px solid var(--accent);
    border-radius: var(--radius-sm);
    font-family: var(--font-body);
    font-size: 14px;
    font-weight: 600;
    color: #06121f;
    cursor: pointer;
    transition: background 0.16s ease, border-color 0.16s ease;
  }

  .cta-btn:hover { background: #7cc1f0; border-color: #7cc1f0; }
  .cta-btn:active { background: var(--accent-lo); border-color: var(--accent-lo); }

  .cta-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(99,179,237,0.25); }

  .cta-btn:disabled {
    background: var(--surface-2);
    border-color: var(--line-2);
    color: var(--text-3);
    cursor: default;
  }

  .strength-wrap { margin-top: 10px; display: none; }
  .strength-wrap.visible { display: block; }

  .strength-bars { display: flex; gap: 4px; margin-bottom: 6px; }

  .strength-bar {
    flex: 1;
    height: 3px;
    border-radius: 2px;
    background: var(--surface-2);
    transition: background 0.25s ease;
  }

  .strength-label { font-size: 11.5px; font-weight: 500; color: var(--text-3); }

  .field-error {
    font-size: 12px;
    color: var(--danger);
    margin-top: 6px;
    display: none;
  }

  .field.has-error .field-input { border-color: rgba(252,129,129,0.55); }
  .field.has-error .field-error { display: block; }

  .client-error {
    font-size: 12px;
    color: var(--danger);
    margin-top: 6px;
    display: none;
  }

  .field.client-invalid .field-input { border-color: rgba(252,129,129,0.55); }
  .field.client-invalid .client-error { display: block; }

  .form-panel { display: none; }
  .form-panel.active { display: block; }

  .note-text {
    font-size: 12.5px;
    color: var(--text-3);
    line-height: 1.6;
    margin-top: 18px;
  }

  .switch-row {
    margin-top: 20px;
    padding-top: 18px;
    border-top: 1px solid var(--border);
    font-size: 13px;
    color: var(--text-2);
  }

  .note-text a,
  .link-btn {
    color: var(--text-1);
    text-decoration: none;
    border-bottom: 1px solid var(--border-active);
    padding-bottom: 1px;
    transition: color 0.16s ease, border-color 0.16s ease;
  }

  .link-btn {
    appearance: none;
    border-top: 0; border-left: 0; border-right: 0;
    padding: 0 0 1px;
    background: transparent;
    font: inherit;
    cursor: pointer;
  }

  .note-text a:hover,
  .link-btn:hover { color: var(--accent); border-color: var(--accent); }

  /* ── responsive ─────────────────────────────────────── */

  @media (max-width: 1180px) {
    .shell { grid-template-columns: minmax(0, 1fr) 420px; }
    .left { padding: 44px 44px; }
    .right { padding: 40px 38px; }
  }

  @media (max-width: 980px) {
    body { overflow-y: auto; }

    .shell {
      grid-template-columns: 1fr;
      height: auto;
      min-height: 100vh;
    }

    .left {
      padding: 40px 40px 36px;
      border-bottom: 1px solid var(--line-2);
    }

    .brand-row { margin-bottom: 32px; }
    .preview { display: none; }

    .right {
      border-left: none;
      padding: 36px 40px 48px;
      justify-items: start;
    }

    .form-card { max-width: 420px; }
  }

  @media (max-width: 560px) {
    .left { padding: 30px 22px 28px; }
    .right { padding: 30px 22px 40px; }
    .form-extras { flex-wrap: wrap; gap: 10px; }
  }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
  }
</style>
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
          Cell ran in 0.42s &middot; 2 of 2 checks passed
        </div>

        <div class="pv-thread">
          <div class="pv-msg">
            <div class="pv-avatar">JC</div>
            <div>
              <div class="pv-name">Juan <span>&middot; 2:14 PM</span></div>
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
                style="padding-right: 58px;"
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
                style="padding-right: 58px;"
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
                style="padding-right: 58px;"
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

          <button type="submit" class="cta-btn" id="reg-btn" style="margin-top: 22px;">Create account</button>
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
    el.dataset.pending = 'true';
    el.style.visibility = 'hidden';
    return fragment;
  });

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

  async function run(){
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
      delete element.dataset.pending;

      if (i === 0) {
        reveals.forEach(el => el.classList.add('shown'));
        await wait(420);
      }
    }
  }

  run().catch(showEverything);
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

/* ── submit state ───────────────────────────────────── */

document.querySelectorAll('.form-panel form').forEach(form => {
  form.addEventListener('submit', () => {
    const button = form.querySelector('button[type="submit"]');
    if (!button) return;

    button.disabled = true;
    button.textContent = button.id === 'reg-btn' ? 'Creating account…' : 'Signing in…';
  });
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

  const colors = ['#fc8181', '#f6ad55', '#68d391', '#63b3ed'];
  const labels = ['Weak', 'Fair', 'Good', 'Strong'];

  bars.forEach((bar, index) => {
    bar.style.background = index < score ? colors[score - 1] : 'var(--surface-2)';
  });

  label.textContent = labels[score - 1] || 'Weak';
  label.style.color = colors[score - 1] || colors[0];
}
</script>
</body>
</html>