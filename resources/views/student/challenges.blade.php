<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Challenges — DataSensei</title>
<style>
    /* Challenge path list. Colours, type and radius come from partials.design-system;
       coding-challenges.blade.php uses the same rules. */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      margin: 0;
      background: var(--bg);
      color: var(--text);
      font-family: var(--ds-font-sans);
    }
    .page-layout-wrapper { display: flex; min-height: 100vh; }
    .page-challenges-main { flex: 1; min-width: 0; padding: 28px 32px 48px; }

    /* ── Page header ── */
    .page-challenges-header { margin-bottom: 24px; }
    .page-challenges-hero-subtitle { max-width: 72ch; margin-top: 4px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .page-challenges-institution-note { max-width: 72ch; margin-top: 6px; color: var(--muted); font-size: .8125rem; line-height: 1.55; }

    /* ── Alerts (the shared notification script usually moves these into its stack) ── */
    .page-challenges-alert-container { display: grid; gap: 8px; }
    .page-challenges-alert-container:has(> *) { margin-bottom: 20px; }
    .page-challenges-alert {
      display: flex; align-items: flex-start; gap: 8px;
      padding: 12px 16px; border: 1px solid; border-radius: var(--radius-sm);
      font-size: .875rem; line-height: 1.5;
    }
    .page-challenges-alert svg { width: 16px; height: 16px; flex: 0 0 16px; margin-top: 2px; }
    .page-challenges-alert-success { border-color: var(--ds-success-border); background: var(--ds-success-soft); color: #d1fae5; }
    .page-challenges-alert-error   { border-color: var(--ds-danger-border); background: var(--ds-danger-soft); color: #fee2e2; }

    /* ── Path grid ── */
    .page-challenges-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; align-items: stretch; }

    /* ── Path card ── */
    .page-challenges-card {
      position: relative; display: flex; flex-direction: column; min-width: 0;
      padding: 20px; border: 1px solid var(--border); border-radius: var(--radius);
      background: var(--surface); color: inherit; text-decoration: none;
      transition: background .12s ease, border-color .12s ease;
    }
    a.page-challenges-card:hover { border-color: var(--border-hover); background: var(--surface2); }

    .page-challenges-card-title { color: var(--text); font-size: .9375rem; font-weight: 600; line-height: 1.35; overflow-wrap: anywhere; }
    .page-challenges-card.is-bonus .page-challenges-card-title { padding-right: 150px; }
    .page-challenges-card-audience { display: block; margin-top: 2px; color: var(--muted); font-size: .8125rem; font-weight: 500; line-height: 1.45; }
    .page-challenges-card-desc { flex: 1; margin: 12px 0 16px; color: var(--ds-text-secondary); font-size: .875rem; line-height: 1.55; }
    .page-challenges-card-action {
      display: inline-flex; align-items: center; gap: 6px; margin-top: auto;
      color: var(--ds-accent-text); font-size: .875rem; font-weight: 500; line-height: 1.3;
      transition: color .12s ease;
    }
    .page-challenges-card-action svg { width: 16px; height: 16px; }
    a.page-challenges-card:hover .page-challenges-card-action { color: var(--text); }

    /* ── Locked and early-unlocked paths ── */
    .page-challenges-card.is-locked { cursor: default; }
    .page-challenges-card.is-locked .page-challenges-card-title { color: var(--muted); }
    .page-challenges-card.is-locked .page-challenges-card-audience { color: var(--dim); }
    .page-challenges-card.is-locked .page-challenges-card-desc { color: var(--muted); }
    .page-challenges-card.is-locked .page-challenges-card-action { color: var(--muted); }
    .page-challenges-card.is-locked .page-challenges-card-action svg { width: 14px; height: 14px; }

    .page-challenges-lock-reason {
      display: block; margin-top: 12px; padding: 8px 10px; border-radius: var(--radius-sm);
      background: var(--surface3); color: var(--muted); font-size: .8125rem; line-height: 1.5;
    }

    .page-challenges-card.is-bonus { border-color: var(--ds-warning-border); }
    a.page-challenges-card.is-bonus:hover { border-color: var(--ds-warning); }
    .page-challenges-card.is-bonus .page-challenges-lock-reason { background: var(--ds-warning-soft); color: var(--ds-warning-text); }
    .page-challenges-card-badge {
      position: absolute; top: 19px; right: 20px;
      display: inline-flex; align-items: center; padding: 2px 8px;
      border: 1px solid var(--ds-warning-border); border-radius: var(--radius-xs);
      background: var(--ds-warning-soft); color: var(--ds-warning-text);
      font-size: .75rem; font-weight: 600; line-height: 1.4; white-space: nowrap;
    }

    /* ── Dialog ── */
    .page-challenges-modal-overlay {
      position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center;
      padding: 16px; overflow-y: auto; background: var(--ds-overlay);
      opacity: 0; pointer-events: none; transition: opacity .16s ease;
    }
    .page-challenges-modal-overlay.is-active { opacity: 1; pointer-events: auto; }
    .page-challenges-modal-card {
      width: min(420px, 100%); max-height: calc(100vh - 32px); max-height: calc(100dvh - 32px); overflow-y: auto;
      padding: 20px; border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg);
      background: var(--surface); box-shadow: var(--ds-shadow-lg);
      transform: translateY(8px); transition: transform .16s ease;
    }
    .page-challenges-modal-overlay.is-active .page-challenges-modal-card { transform: none; }
    .page-challenges-modal-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 8px; }
    .page-challenges-modal-title { color: var(--text); font-size: 1rem; font-weight: 600; }
    .page-challenges-modal-close {
      width: 32px; height: 32px; flex: 0 0 32px; display: inline-flex; align-items: center; justify-content: center;
      border: 1px solid transparent; border-radius: var(--radius-sm); background: transparent; color: var(--muted); cursor: pointer;
      transition: background .12s ease, color .12s ease;
    }
    .page-challenges-modal-close:hover { background: var(--surface2); color: var(--text); }
    .page-challenges-modal-desc { margin-bottom: 20px; color: var(--muted); font-size: .875rem; line-height: 1.55; }
    .page-challenges-form-group { margin-bottom: 16px; }
    .page-challenges-form-label { display: block; margin-bottom: 6px; color: var(--ds-text-secondary); font-size: .8125rem; font-weight: 500; }
    .page-challenges-form-input {
      width: 100%; min-height: 38px; padding: 8px 12px;
      border: 1px solid var(--ds-input-border); border-radius: var(--radius-sm); background: var(--surface3);
      color: var(--text); font: 400 .875rem/1.4 var(--ds-font-sans); outline: none;
      transition: border-color .12s ease, box-shadow .12s ease;
    }
    .page-challenges-form-input:focus { border-color: var(--accent); box-shadow: var(--ds-focus-ring); }
    .page-challenges-form-input::placeholder { color: var(--dim); }
    .page-challenges-modal-submit {
      width: 100%; min-height: 38px; padding: 0 16px;
      border: 1px solid var(--accent); border-radius: var(--radius-sm); background: var(--accent); color: #fff;
      font: 500 .875rem/1.2 var(--ds-font-sans); cursor: pointer; transition: background .12s ease, border-color .12s ease;
    }
    .page-challenges-modal-submit:hover { border-color: var(--accent-hover); background: var(--accent-hover); }

    @media (max-width: 1100px) {
      .page-challenges-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 900px) {
      .page-challenges-main { padding: 24px 20px 40px; }
    }
    @media (max-width: 640px) {
      .page-challenges-main { padding: 20px 16px 32px; }
      .page-challenges-header { margin-bottom: 20px; }
      .page-challenges-grid { grid-template-columns: minmax(0, 1fr); gap: 12px; }
      .page-challenges-card { padding: 16px; }
      .page-challenges-card-badge { top: 15px; right: 16px; }
    }
  </style>
    @include('partials.page-head', ['pageTitle' => 'Select Your Path', 'pageDescription' => 'Practise data science challenges and track your mastery.'])
</head>
<body>

<div class="page-layout-wrapper">
  @include('partials.sidebar')

  <div class="page-challenges-main">

    <header class="page-challenges-header">
      <h1 class="ds-page-title">Challenges</h1>
      <p class="page-challenges-hero-subtitle">Choose a path that matches your current experience level.</p>
      <p class="page-challenges-institution-note">
        Paths now unlock through progression. Complete the previous difficulty to move forward; exceptional speed and accuracy can unlock the next-next difficulty early.
      </p>
    </header>

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
            <h2 class="page-challenges-card-title">{{ $cat->name }}</h2>
            <span class="page-challenges-card-audience">{{ $cat->target_audience }}</span>
            <p class="page-challenges-card-desc">
              {{ $cat->description }}
              <span class="page-challenges-lock-reason">{{ $lockInfo['reason'] ?? 'Complete the required previous path to unlock this.' }}</span>
            </p>
            <div class="page-challenges-card-action">
              Locked
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
          </div>
        @else
          <a href="{{ route('challenges.map', $cat->slug) }}" class="page-challenges-card {{ $isBonusUnlocked ? 'is-bonus' : '' }}">
            @if($isBonusUnlocked)
              <div class="page-challenges-card-badge">Exceptional Unlock</div>
            @endif
            <h2 class="page-challenges-card-title">{{ $cat->name }}</h2>
            <span class="page-challenges-card-audience">{{ $cat->target_audience }}</span>
            <p class="page-challenges-card-desc">
              {{ $cat->description }}
              @if($isBonusUnlocked)
                <span class="page-challenges-lock-reason">{{ $lockInfo['reason'] }}</span>
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
