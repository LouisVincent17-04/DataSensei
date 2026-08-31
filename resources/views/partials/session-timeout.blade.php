@once
@php
  $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 240));
  $idleWarningMinutes = min(5, max(1, $idleTimeoutMinutes - 1));
@endphp
<style id="datasensei-session-timeout-style">
  .ds-session-timeout[hidden]{display:none!important}
  .ds-session-timeout{position:fixed;inset:0;z-index:100000;background:rgba(3,8,18,.76);backdrop-filter:blur(6px);display:flex;align-items:center;justify-content:center;padding:20px;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
  .ds-session-timeout-card{width:min(440px,100%);margin:auto;background:#111c2d;border:1px solid #2a3d59;border-radius:8px;box-shadow:0 24px 70px rgba(0,0,0,.48);padding:24px;color:#f8fafc;text-align:center}
  .ds-session-timeout-card h2{margin:0 0 8px;font-size:1.25rem;line-height:1.25;font-weight:750;letter-spacing:-.02em}
  .ds-session-timeout-card p{margin:0;color:#a8b7ca;font-size:.9rem;line-height:1.55}
  .ds-session-timeout-countdown{margin-top:16px;padding:12px 14px;border:1px solid #2a3d59;border-radius:9px;background:#0d1727;color:#dbeafe;font-size:.86rem;font-weight:700}
  .ds-session-timeout-actions{display:flex;justify-content:center;gap:10px;margin-top:20px}
  .ds-session-timeout-btn{min-height:40px;padding:0 15px;border-radius:8px;border:1px solid #334a69;background:#17243a;color:#dbe7f5;font:inherit;font-size:.84rem;font-weight:700;cursor:pointer}
  .ds-session-timeout-btn:hover{background:#1d2e49;border-color:#49698f}
  .ds-session-timeout-btn.primary{background:#2563eb;border-color:#2563eb;color:#fff}
  .ds-session-timeout-btn.primary:hover{background:#1d4ed8;border-color:#1d4ed8}
  .ds-session-timeout-server{margin-top:14px;color:#fca5a5!important}
  @media(max-width:520px){.ds-session-timeout-actions{flex-direction:column-reverse}.ds-session-timeout-btn{width:100%}}
</style>

<div class="ds-session-timeout" id="ds-session-timeout-warning" hidden role="dialog" aria-modal="true" aria-labelledby="ds-session-timeout-title">
  <div class="ds-session-timeout-card">
    <h2 id="ds-session-timeout-title">Still there?</h2>
    <p>For your security, DataSensei signs you out after {{ $idleTimeoutMinutes }} minutes without activity. Choose Stay signed in if you want to continue.</p>
    <div class="ds-session-timeout-countdown" id="ds-session-timeout-countdown" aria-live="polite"></div>
    <p class="ds-session-timeout-server" id="ds-session-timeout-server" hidden></p>
    <div class="ds-session-timeout-actions">
      <button type="button" class="ds-session-timeout-btn" id="ds-session-sign-out">Sign out now</button>
      <button type="button" class="ds-session-timeout-btn primary" id="ds-session-stay">Stay signed in</button>
    </div>
  </div>
</div>

<script id="datasensei-session-timeout-script">
(() => {
  if (window.__dataSenseiSessionTimeoutInitialized) return;
  window.__dataSenseiSessionTimeoutInitialized = true;

  const timeoutMs = {{ $idleTimeoutMinutes }} * 60 * 1000;
  const warningMs = {{ $idleWarningMinutes }} * 60 * 1000;
  const heartbeatEveryMs = 60 * 1000;
  const storageKey = @json('datasensei:last-user-activity:'.auth()->id());
  const logoutKey = @json('datasensei:session-ended:'.auth()->id());
  const activityUrl = @json(route('session.activity'));
  const logoutUrl = @json(route('logout'));
  const loginUrl = @json(route('login'));
  const expiredLoginUrl = @json(route('login', ['expired' => 1]));
  const csrf = @json(csrf_token());

  let lastActivity = Date.now();
  let lastStoredAt = 0;
  let lastHeartbeatAt = 0;
  let activityDirty = true;
  let warningTimer = null;
  let expiryTimer = null;
  let countdownTimer = null;
  let ending = false;

  const overlay = document.getElementById('ds-session-timeout-warning');
  const countdown = document.getElementById('ds-session-timeout-countdown');
  const serverMessage = document.getElementById('ds-session-timeout-server');
  const stayButton = document.getElementById('ds-session-stay');
  const signOutButton = document.getElementById('ds-session-sign-out');

  // The shared partial may be rendered from inside a sidebar. Moving the
  // dialog to <body> keeps its fixed overlay centered on the full viewport.
  if (overlay && overlay.parentElement !== document.body) {
    document.body.appendChild(overlay);
  }

  const readSharedActivity = () => {
    const stored = Number(window.localStorage.getItem(storageKey) || 0);
    return Number.isFinite(stored) && stored > 0 ? stored : 0;
  };

  const writeSharedActivity = (timestamp) => {
    try {
      window.localStorage.setItem(storageKey, String(timestamp));
    } catch (_) {
      // Session timeout still works when localStorage is unavailable.
    }
  };

  const effectiveLastActivity = () => Math.max(lastActivity, readSharedActivity());

  const hideWarning = () => {
    if (overlay) overlay.hidden = true;
    if (serverMessage) {
      serverMessage.hidden = true;
      serverMessage.textContent = '';
    }
    if (countdownTimer) window.clearInterval(countdownTimer);
    countdownTimer = null;
  };

  const renderCountdown = () => {
    if (!countdown) return;
    const remaining = Math.max(0, timeoutMs - (Date.now() - effectiveLastActivity()));
    const totalSeconds = Math.ceil(remaining / 1000);
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    countdown.textContent = `Automatic sign-out in ${minutes}:${String(seconds).padStart(2, '0')}`;
  };

  const showWarning = () => {
    if (ending || !overlay) return;
    overlay.hidden = false;
    renderCountdown();
    countdownTimer = window.setInterval(renderCountdown, 1000);
    stayButton?.focus();
  };

  const scheduleTimers = () => {
    if (warningTimer) window.clearTimeout(warningTimer);
    if (expiryTimer) window.clearTimeout(expiryTimer);

    const elapsed = Date.now() - effectiveLastActivity();
    const warningDelay = Math.max(0, timeoutMs - warningMs - elapsed);
    const expiryDelay = Math.max(0, timeoutMs - elapsed);

    warningTimer = window.setTimeout(showWarning, warningDelay);
    expiryTimer = window.setTimeout(() => endSession(true), expiryDelay);
  };

  const redirectToLogin = (expired = false) => {
    try {
      window.localStorage.setItem(logoutKey, String(Date.now()));
    } catch (_) {}
    window.location.replace(expired ? expiredLoginUrl : loginUrl);
  };

  const requestWithTimeout = async (url, options, timeout = 6000) => {
    const controller = new AbortController();
    const timer = window.setTimeout(() => controller.abort(), timeout);
    try {
      return await fetch(url, {...options, signal: controller.signal});
    } finally {
      window.clearTimeout(timer);
    }
  };

  async function endSession(expired) {
    if (ending) return;
    ending = true;
    if (warningTimer) window.clearTimeout(warningTimer);
    if (expiryTimer) window.clearTimeout(expiryTimer);
    if (countdownTimer) window.clearInterval(countdownTimer);

    if (overlay) overlay.hidden = false;
    if (countdown) countdown.textContent = expired ? 'Signing you out…' : 'Signing out…';
    if (stayButton) stayButton.disabled = true;
    if (signOutButton) signOutButton.disabled = true;

    try {
      await requestWithTimeout(logoutUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
        },
      });
      redirectToLogin(expired);
    } catch (_) {
      ending = false;
      if (countdown) countdown.textContent = 'We could not complete sign-out automatically.';
      if (serverMessage) {
        serverMessage.hidden = false;
        serverMessage.textContent = 'Go to the sign-in page, then sign in again when DataSensei is available.';
      }
      if (stayButton) {
        stayButton.disabled = false;
        stayButton.textContent = 'Go to sign in';
        stayButton.onclick = () => redirectToLogin(expired);
      }
      if (signOutButton) signOutButton.hidden = true;
    }
  }

  const sendHeartbeat = async () => {
    if (ending || !activityDirty || document.hidden) return;
    const now = Date.now();
    if ((now - lastHeartbeatAt) < heartbeatEveryMs) return;

    lastHeartbeatAt = now;
    try {
      const response = await requestWithTimeout(activityUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrf,
        },
      });

      if (response.status === 401 || response.status === 419) {
        redirectToLogin();
        return;
      }

      if (response.ok) activityDirty = false;
    } catch (_) {
      // A later heartbeat or normal navigation will retry.
    }
  };

  const noteActivity = () => {
    if (ending) return;
    const now = Date.now();
    lastActivity = now;
    activityDirty = true;
    hideWarning();
    scheduleTimers();

    if ((now - lastStoredAt) >= 5000) {
      lastStoredAt = now;
      writeSharedActivity(now);
    }
  };

  const activityEvents = ['pointerdown', 'keydown', 'touchstart', 'scroll'];
  activityEvents.forEach(eventName => {
    window.addEventListener(eventName, noteActivity, {passive: true});
  });

  let lastPointerMove = 0;
  window.addEventListener('pointermove', () => {
    const now = Date.now();
    if ((now - lastPointerMove) >= 10000) {
      lastPointerMove = now;
      noteActivity();
    }
  }, {passive: true});

  window.addEventListener('storage', event => {
    if (event.key === storageKey) {
      const shared = Number(event.newValue || 0);
      if (Number.isFinite(shared) && shared > lastActivity) {
        lastActivity = shared;
        hideWarning();
        scheduleTimers();
      }
    }
    if (event.key === logoutKey) redirectToLogin(false);
  });

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
      const shared = readSharedActivity();
      if (shared > lastActivity) lastActivity = shared;
      scheduleTimers();
      sendHeartbeat();
    }
  });

  stayButton?.addEventListener('click', () => {
    stayButton.textContent = 'Stay signed in';
    noteActivity();
    sendHeartbeat();
  });
  signOutButton?.addEventListener('click', () => endSession(false));

  lastActivity = Math.max(lastActivity, readSharedActivity());
  writeSharedActivity(lastActivity);
  scheduleTimers();
  window.setInterval(sendHeartbeat, 30000);
})();
</script>
@endonce
