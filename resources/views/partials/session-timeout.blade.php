@once
@php
  $idleTimeoutMinutes = max(1, (int) config('session.idle_timeout', 240));
  $idleWarningMinutes = min(5, max(1, $idleTimeoutMinutes - 1));
@endphp
<style id="datasensei-session-timeout-style">
  html.ds-session-blocked,
  html.ds-session-blocked body{overflow:hidden!important}
  .ds-session-timeout[hidden]{display:none!important}
  .ds-session-timeout{position:fixed!important;inset:0!important;width:100vw!important;height:100vh!important;height:100dvh!important;max-width:none!important;margin:0!important;z-index:2147483000;background:rgba(3,8,18,.86);display:flex;align-items:center;justify-content:center;overflow:auto;padding:16px;isolation:isolate;font-family:var(--ds-font-sans,Inter,Arial,Helvetica,sans-serif)}
  .ds-session-timeout-card{width:min(440px,100%);margin:auto;padding:24px;background:var(--ds-surface,#111c2d);border:1px solid var(--ds-border-strong,#2c4168);border-radius:var(--ds-radius-lg,10px);box-shadow:var(--ds-shadow-lg);color:var(--ds-text,#f8fafc);text-align:left}
  .ds-session-timeout-card h2{margin:0 0 6px;font-size:1.125rem;font-weight:600;line-height:1.35;letter-spacing:-.01em}
  .ds-session-timeout-card p{margin:0;color:var(--ds-text-secondary,#c8d5e8);font-size:.875rem;line-height:1.55}
  .ds-session-timeout-countdown{margin-top:16px;padding:12px 14px;border:1px solid var(--ds-border,#1e2f47);border-radius:var(--ds-radius-sm,6px);background:var(--ds-surface-3,#0f1928);color:var(--ds-text,#f8fafc);font-size:.875rem;font-weight:600;font-variant-numeric:tabular-nums}
  .ds-session-timeout-actions{display:flex;justify-content:flex-end;flex-wrap:wrap;gap:8px;margin-top:20px}
  .ds-session-timeout-actions[hidden]{display:none!important}
  .ds-session-timeout-btn{min-height:38px;padding:0 16px;border-radius:var(--ds-radius-sm,6px);border:1px solid var(--ds-border-strong,#2c4168);background:var(--ds-surface-2,#1a2638);color:var(--ds-text,#f8fafc);font:inherit;font-size:.875rem;font-weight:500;cursor:pointer}
  .ds-session-timeout-btn:hover{background:var(--ds-surface-hover,#1f2d44)}
  .ds-session-timeout-btn.primary{background:var(--ds-accent,#3b82f6);border-color:var(--ds-accent,#3b82f6);color:#fff}
  .ds-session-timeout-btn.primary:hover{background:var(--ds-accent-strong,#2563eb);border-color:var(--ds-accent-strong,#2563eb)}
  .ds-session-timeout-server{margin-top:16px;color:var(--ds-danger-text,#fca5a5)!important}
  .ds-session-timeout.is-ending .ds-session-timeout-countdown{color:var(--ds-warning-text,#fcd34d);border-color:var(--ds-warning-border,rgba(245,158,11,.38));background:var(--ds-warning-soft,rgba(245,158,11,.12))}
  @media(max-width:520px){.ds-session-timeout-actions{flex-direction:column-reverse}.ds-session-timeout-btn{width:100%}}
</style>

<script id="datasensei-session-timeout-script">
(() => {
  const initialize = () => {
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
    let redirecting = false;

    const overlay = document.createElement('div');
    overlay.className = 'ds-session-timeout';
    overlay.id = 'ds-session-timeout-warning';
    overlay.hidden = true;
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-labelledby', 'ds-session-timeout-title');
    overlay.innerHTML = `
      <div class="ds-session-timeout-card">
        <h2 id="ds-session-timeout-title">Still there?</h2>
        <p id="ds-session-timeout-description">For your security, DataSensei signs you out after {{ $idleTimeoutMinutes }} minutes without activity. Choose Stay signed in if you want to continue.</p>
        <div class="ds-session-timeout-countdown" id="ds-session-timeout-countdown" aria-live="polite"></div>
        <p class="ds-session-timeout-server" id="ds-session-timeout-server" hidden></p>
        <div class="ds-session-timeout-actions" id="ds-session-timeout-actions">
          <button type="button" class="ds-session-timeout-btn" id="ds-session-sign-out">Sign out now</button>
          <button type="button" class="ds-session-timeout-btn primary" id="ds-session-stay">Stay signed in</button>
        </div>
      </div>`;
    document.body.appendChild(overlay);

    const title = document.getElementById('ds-session-timeout-title');
    const description = document.getElementById('ds-session-timeout-description');
    const countdown = document.getElementById('ds-session-timeout-countdown');
    const serverMessage = document.getElementById('ds-session-timeout-server');
    const actions = document.getElementById('ds-session-timeout-actions');
    const stayButton = document.getElementById('ds-session-stay');
    const signOutButton = document.getElementById('ds-session-sign-out');

    const readSharedActivity = () => {
      try {
        const stored = Number(window.localStorage.getItem(storageKey) || 0);
        return Number.isFinite(stored) && stored > 0 ? stored : 0;
      } catch (_) {
        return 0;
      }
    };

    const writeSharedActivity = (timestamp) => {
      try {
        window.localStorage.setItem(storageKey, String(timestamp));
      } catch (_) {
        // Session timeout still works when localStorage is unavailable.
      }
    };

    const effectiveLastActivity = () => Math.max(lastActivity, readSharedActivity());

    const clearTimers = () => {
      if (warningTimer) window.clearTimeout(warningTimer);
      if (expiryTimer) window.clearTimeout(expiryTimer);
      if (countdownTimer) window.clearInterval(countdownTimer);
      warningTimer = null;
      expiryTimer = null;
      countdownTimer = null;
    };

    const hideWarning = () => {
      if (ending) return;
      overlay.hidden = true;
      overlay.classList.remove('is-ending');
      overlay.removeAttribute('aria-busy');
      document.documentElement.classList.remove('ds-session-blocked');
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
      if (ending) return;
      overlay.hidden = false;
      document.documentElement.classList.add('ds-session-blocked');
      renderCountdown();
      if (countdownTimer) window.clearInterval(countdownTimer);
      countdownTimer = window.setInterval(renderCountdown, 1000);
      stayButton?.focus();
    };

    const scheduleTimers = () => {
      if (ending) return;
      if (warningTimer) window.clearTimeout(warningTimer);
      if (expiryTimer) window.clearTimeout(expiryTimer);

      const elapsed = Date.now() - effectiveLastActivity();
      const warningDelay = Math.max(0, timeoutMs - warningMs - elapsed);
      const expiryDelay = Math.max(0, timeoutMs - elapsed);

      warningTimer = window.setTimeout(showWarning, warningDelay);
      expiryTimer = window.setTimeout(() => endSession(true), expiryDelay);
    };

    const prepareSessionNavigation = (expired) => {
      window.__dataSenseiSessionEnding = true;
      document.documentElement.classList.add('ds-session-blocked');

      try {
        window.dispatchEvent(new CustomEvent('datasensei:session-ending', {
          detail: { expired: Boolean(expired) },
        }));
      } catch (_) {
        // The navigation flag still suppresses page-specific leave warnings.
      }
    };

    const redirectToLogin = (expired = false, destination = null) => {
      if (redirecting) return;
      redirecting = true;
      ending = true;
      clearTimers();
      prepareSessionNavigation(expired);

      try {
        window.localStorage.setItem(logoutKey, String(Date.now()));
      } catch (_) {}

      const target = typeof destination === 'string' && destination.trim() !== ''
        ? destination
        : (expired ? expiredLoginUrl : loginUrl);
      window.location.replace(target);
    };

    // This listener is registered before page-level dirty-form guards. During
    // a real session exit it prevents those guards from opening a native
    // "Leave site?" dialog. Normal user navigation still receives protection.
    window.addEventListener('beforeunload', event => {
      if (!window.__dataSenseiSessionEnding) return;
      event.stopImmediatePropagation();
    }, { capture: true });

    const requestWithTimeout = async (url, options, timeout = 6000) => {
      const controller = new AbortController();
      const timer = window.setTimeout(() => controller.abort(), timeout);
      try {
        return await fetch(url, { ...options, signal: controller.signal });
      } finally {
        window.clearTimeout(timer);
      }
    };

    const renderEndingState = (expired) => {
      overlay.hidden = false;
      overlay.classList.add('is-ending');
      overlay.setAttribute('aria-busy', 'true');
      document.documentElement.classList.add('ds-session-blocked');
      if (title) title.textContent = expired ? 'Session expired' : 'Signing out';
      if (description) {
        description.textContent = expired
          ? 'Your session ended because there was no activity. You will be taken to the sign-in page.'
          : 'Your session is ending. You will be taken to the sign-in page.';
      }
      if (countdown) countdown.textContent = 'Redirecting to sign in…';
      if (serverMessage) {
        serverMessage.hidden = true;
        serverMessage.textContent = '';
      }
      if (actions) actions.hidden = true;
    };

    async function endSession(expired) {
      if (ending) return;
      ending = true;
      clearTimers();
      renderEndingState(expired);

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
      } catch (_) {
        // A missing or already-expired server session must not trap the user
        // behind the overlay. Continue to the sign-in page in every case.
      }

      redirectToLogin(expired);
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
          redirectToLogin(true);
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
      window.addEventListener(eventName, noteActivity, { passive: true });
    });

    let lastPointerMove = 0;
    window.addEventListener('pointermove', () => {
      const now = Date.now();
      if ((now - lastPointerMove) >= 10000) {
        lastPointerMove = now;
        noteActivity();
      }
    }, { passive: true });

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
      noteActivity();
      sendHeartbeat();
    });
    signOutButton?.addEventListener('click', () => endSession(false));

    window.DataSenseiSession = {
      redirectToLogin,
      end: endSession,
      isEnding: () => ending || Boolean(window.__dataSenseiSessionEnding),
    };

    lastActivity = Math.max(lastActivity, readSharedActivity());
    writeSharedActivity(lastActivity);
    scheduleTimers();
    window.setInterval(sendHeartbeat, 30000);
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, { once: true });
  } else {
    initialize();
  }
})();
</script>
@endonce
