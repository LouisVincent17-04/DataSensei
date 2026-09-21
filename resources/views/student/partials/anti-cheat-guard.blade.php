@php
  $antiCheatSettings = $antiCheatSettings ?? [];
  $assessmentType = 'assignment';
  $classAssignmentId = $classAssignmentId ?? null;
  $assignmentSubmissionId = $assignmentSubmissionId ?? null;
  $assignmentQuestionId = $assignmentQuestionId ?? null;
  $antiCheatSessionId = $antiCheatSessionId ?? null;
  $antiCheatEventContract = \App\Support\AntiCheatEventContract::clientContract();
  // Authoritative state from AntiCheatPolicyService::attemptIntegrityState().
  $antiCheatState = $antiCheatState ?? null;
@endphp

@if(!empty($antiCheatSettings['enabled']))
<style>
  /* Anti-cheat warnings for timed work. The toast joins the global
     notification stack; the lock and fullscreen dialogs sit above every
     other layer (mobile bar 900, drawer 960, page dialogs 1000+). */
  .ds-ac-toast {
    position: relative; inset: auto; z-index: 10000; display: none;
    width: min(390px, 100%); max-width: 390px; margin: 16px 16px 0 auto; padding: 12px 14px;
    border: 1px solid var(--ds-warning-border); border-radius: var(--ds-radius-sm); background: var(--ds-surface);
    box-shadow: var(--ds-shadow-md); color: #fef3c7;
    font: 500 .8125rem/1.5 var(--ds-font-sans); overflow-wrap: anywhere;
  }
  .ds-ac-toast.show { display: block; }
  .ds-ac-toast strong { display: block; margin-bottom: 2px; color: #fff; font-weight: 600; }

  /* Both overlays stay nearly opaque: a locked or paused attempt must not
     remain readable behind the dialog. */
  .ds-ac-lock, .ds-ac-fullscreen {
    position: fixed; inset: 0; padding: 16px; overflow-y: auto;
    display: none; align-items: center; justify-content: center;
    background: rgba(3, 8, 18, .94);
  }
  .ds-ac-lock { z-index: 99998; }
  .ds-ac-fullscreen { z-index: 99997; }
  .ds-ac-lock.show, .ds-ac-fullscreen.show { display: flex; }

  .ds-ac-lock-card, .ds-ac-fullscreen-card {
    width: min(560px, 100%); max-height: calc(100vh - 32px); max-height: calc(100dvh - 32px); overflow-y: auto;
    padding: 20px; display: flex; flex-direction: column; align-items: flex-start;
    border: 1px solid var(--ds-border-strong); border-radius: var(--ds-radius-lg); background: var(--ds-surface);
    box-shadow: var(--ds-shadow-lg); font-family: var(--ds-font-sans); text-align: left;
  }
  .ds-ac-lock-icon { display: block; width: 24px; height: 24px; margin: 0 0 12px; color: var(--ds-danger-text); }
  .ds-ac-lock-title, .ds-ac-fullscreen-title { margin-bottom: 6px; color: var(--ds-text); font-size: 1rem; font-weight: 600; line-height: 1.35; }
  .ds-ac-lock-msg { margin-bottom: 20px; color: var(--ds-danger-text); font-size: .875rem; line-height: 1.55; }
  .ds-ac-fullscreen-msg { margin-bottom: 20px; color: var(--ds-text-muted); font-size: .875rem; line-height: 1.55; }
  .ds-ac-lock-btn, .ds-ac-fullscreen-btn {
    align-self: flex-end; min-height: 38px; padding: 0 16px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1px solid var(--ds-accent); border-radius: var(--ds-radius-sm); background: var(--ds-accent); color: #fff;
    font: 500 .875rem/1.2 var(--ds-font-sans); white-space: nowrap; cursor: pointer;
    transition: background var(--ds-dur-2) ease, border-color var(--ds-dur-2) ease;
  }
  .ds-ac-lock-btn:hover, .ds-ac-fullscreen-btn:hover { border-color: var(--ds-accent-strong); background: var(--ds-accent-strong); }
  @media (max-width: 560px) {
    .ds-ac-lock-btn, .ds-ac-fullscreen-btn { align-self: stretch; }
  }
</style>

<div class="ds-ac-toast" id="ds-ac-toast" data-ds-global-notification role="alert"><strong id="ds-ac-toast-title">Anti-cheat warning</strong><span id="ds-ac-toast-msg"></span></div>
<div class="ds-ac-lock" id="ds-ac-lock"><div class="ds-ac-lock-card"><svg class="ds-ac-lock-icon" viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg><div class="ds-ac-lock-title">Assignment Attempt Locked</div><div class="ds-ac-lock-msg" id="ds-ac-lock-msg">This assignment attempt was locked because a restricted action was detected.</div><div class="ds-ac-lock-msg" style="color:var(--ds-text-muted)">Your saved answers are kept. Submit this attempt so your instructor can review it. It receives no credit unless your instructor releases it.</div><button type="button" class="ds-ac-lock-btn" id="ds-ac-finalize-btn">Submit for review</button></div></div>
<div class="ds-ac-fullscreen" id="ds-ac-fullscreen"><div class="ds-ac-fullscreen-card"><div class="ds-ac-fullscreen-title">Fullscreen Required</div><div class="ds-ac-fullscreen-msg">Your instructor requires fullscreen mode for this assignment. Leaving fullscreen may be logged as a violation.</div><button type="button" class="ds-ac-fullscreen-btn" id="ds-ac-fullscreen-btn">Enter Fullscreen</button></div></div>

<script src="{{ asset('js/anti-cheat-client.js') }}"></script>
<script>
(() => {
  const settings = @json($antiCheatSettings);
  const eventContract = @json($antiCheatEventContract);
  const assessmentType = 'assignment';
  const classAssignmentId = @json($classAssignmentId);
  const assignmentSubmissionId = @json($assignmentSubmissionId);
  const baseAssignmentQuestionId = @json($assignmentQuestionId);
  const sessionKey = @json($antiCheatSessionId);
  const logUrl = @json(route('anti-cheat.events.store'));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());
  const clientTools = window.DataSenseiAntiCheatClient;

  if (!classAssignmentId || !assignmentSubmissionId || !sessionKey) return;
  if (!clientTools) {
    console.error('DataSensei anti-cheat client failed to load.');
    return;
  }
  window.DataSenseiAntiCheat = window.DataSenseiAntiCheat || {};
  window.DataSenseiAntiCheat.sessionId = sessionKey;
  window.DataSenseiAntiCheat.settings = settings;

  const toast = document.getElementById('ds-ac-toast');
  const toastTitle = document.getElementById('ds-ac-toast-title');
  const toastMsg = document.getElementById('ds-ac-toast-msg');
  const lock = document.getElementById('ds-ac-lock');
  const lockMsg = document.getElementById('ds-ac-lock-msg');
  const fullscreenOverlay = document.getElementById('ds-ac-fullscreen');
  const fullscreenBtn = document.getElementById('ds-ac-fullscreen-btn');

  const protectedForm = () => document.getElementById('assignmentForm') || document.querySelector('form[data-protected-assessment="1"]');
  const finalizeBtn = document.getElementById('ds-ac-finalize-btn');
  const initialState = clientTools.normalizeIntegrityState(@json($antiCheatState));

  // The count starts from the server's logical focus-loss count, never from
  // zero, so a reload cannot reset the warnings that were already used.
  let tabSwitchCount = initialState ? initialState.focusLossCount : 0;
  let lockTriggered = false;
  let lastViolationDelivery = null;
  let finalizing = false;
  let lastInternalCopy = null;
  let lastInternalCopyAt = 0;

  function showToast(title, message) {
    if (!settings.show_warnings || !toast) return;
    toastTitle.textContent = title;
    toastMsg.textContent = message;
    toast.classList.add('show');
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toast.classList.remove('show'), 5500);
  }

  function currentAssignmentQuestionId(target = null) {
    const card = target?.closest?.('[data-assignment-question-id]');
    if (card?.dataset?.assignmentQuestionId) return card.dataset.assignmentQuestionId;
    return baseAssignmentQuestionId;
  }

  async function logEvent(eventType, details = {}, eventUuid = null) {
    if (!eventContract.events?.[eventType]) {
      const result = { ok: false, status: 0, error: `Unknown anti-cheat event type: ${eventType}` };
      console.error('Anti-cheat event was not sent:', result);
      return result;
    }

    const { target = null, ...safeDetails } = details;
    const payload = {
      assessment_type: assessmentType,
      event_type: eventType,
      event_uuid: eventUuid || clientTools.createEventUuid(),
      attempt_session_id: sessionKey,
      class_assignment_id: classAssignmentId,
      assignment_submission_id: assignmentSubmissionId,
      assignment_question_id: currentAssignmentQuestionId(target),
      occurred_at: new Date().toISOString(),
      details: {
        ...safeDetails,
        url: window.location.pathname,
        visibility_state: document.visibilityState,
        screen_width: window.screen?.width,
        screen_height: window.screen?.height,
        avail_width: window.screen?.availWidth,
        avail_height: window.screen?.availHeight,
      },
    };
    const result = await clientTools.postJsonWithRetry(logUrl, payload, {
      headers: { 'X-CSRF-TOKEN': csrf },
      maxRetries: 1,
      retryDelayMs: 250,
      transientStatuses: eventContract.transient_http_statuses,
    });

    if (!result.ok) {
      console.error('Anti-cheat event delivery failed:', {
        eventType,
        eventUuid: payload.event_uuid,
        status: result.status,
        attempts: result.attempts,
        error: result.error,
      });
    }

    applyServerState(result);

    return result;
  }

  // Every event response carries the server's decision. The page locks exactly
  // when the server considers the attempt blocked.
  function applyServerState(result) {
    const state = result?.ok ? clientTools.normalizeIntegrityState(result.data?.integrity) : null;
    if (!state) return null;

    tabSwitchCount = state.focusLossCount;
    if (state.blocked) {
      lockAttempt(state.reason || 'This assignment attempt was locked because a restricted action was detected.', null);
    }

    return state;
  }

  function disableAttemptInputs() {
    // Hidden inputs (_token, _method, attempt identity) stay enabled and the
    // latest answers are copied into hidden inputs before the visible controls
    // are disabled, so the locked form can still be submitted (no 419).
    const form = protectedForm();
    if (form) clientTools.snapshotAndLockForm(form, { document });

    document.querySelectorAll('input, textarea, select, button').forEach(el => {
      if (el.type === 'hidden' || el.classList.contains('ds-ac-lock-btn')) return;
      el.disabled = true;
    });
    document.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', e => e.preventDefault());
      a.style.pointerEvents = 'none';
      a.style.opacity = '.55';
    });
  }

  async function finalizeAttempt() {
    const form = protectedForm();
    if (!form || finalizing) return;
    finalizing = true;
    if (finalizeBtn) {
      finalizeBtn.disabled = true;
      finalizeBtn.textContent = 'Submitting...';
    }

    await clientTools.finalizeLockedAttempt({
      form,
      document,
      sessionId: sessionKey,
      eventDelivery: lastViolationDelivery,
      timeoutMs: 3000,
      beforeSubmit: target => target.dispatchEvent(new Event('datasensei:final-submit')),
    });
  }

  function lockAttempt(message, eventType = 'threshold_exceeded') {
    if (lockTriggered) return;
    lockTriggered = true;
    disableAttemptInputs();
    // Push the locked snapshot to the server draft before anything else.
    protectedForm()?.dispatchEvent(new Event('datasensei:flush-autosave'));
    if (eventType) {
      lastViolationDelivery = logEvent(eventType, { message, tab_switch_count: tabSwitchCount });
    }
    if (lockMsg) lockMsg.textContent = message;
    lock?.classList.add('show');

    if (settings.auto_submit_mcq_on_violation) {
      setTimeout(finalizeAttempt, 1200);
    }
  }

  finalizeBtn?.addEventListener('click', finalizeAttempt);

  const focusLossCoordinator = clientTools.createFocusLossCoordinator({
    windowMs: eventContract.focus_correlation_window_ms,
    async onFocusLoss({ source, eventUuid }) {
      tabSwitchCount++;
      const delivery = logEvent(eventContract.focus_loss_event, {
        source_event: source,
        tab_switch_count: tabSwitchCount,
        max_allowed: settings.max_tab_switches,
      }, eventUuid);
      lastViolationDelivery = delivery;
      const result = await delivery;
      if (lockTriggered) return;

      // logEvent() already replaced the local count with the server's
      // deduplicated count when the event was delivered.
      const limit = settings.max_tab_switches ?? 0;
      if (settings.block_on_tab_limit) {
        const remaining = Math.max(0, limit - tabSwitchCount);
        showToast('Focus warning', `Leaving the assignment window is restricted. Focus losses still allowed before this attempt locks: ${remaining}`);
      } else {
        showToast('Focus warning', 'Leaving the assignment window is restricted and was logged.');
      }

      // Offline fallback only: the server could not answer, so the local
      // count decides. Finalizing then holds the attempt for review.
      if (!result.ok && settings.block_on_tab_limit && tabSwitchCount > limit) {
        lockAttempt('You exceeded the allowed tab-switch/focus-loss limit for this assignment attempt.');
      }
    },
  });

  function handleFocusLoss(source) {
    if (settings.allow_tab_switch) return;
    focusLossCoordinator.notify(source);
  }

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') {
      handleFocusLoss('visibility_hidden');
    } else {
      focusLossCoordinator.resume();
    }
  });

  window.addEventListener('blur', () => {
    setTimeout(() => {
      if (!document.hasFocus()) handleFocusLoss('window_blur');
    }, 250);
  });
  window.addEventListener('focus', () => focusLossCoordinator.resume());

  document.addEventListener('copy', e => {
    const selection = String(window.getSelection?.() || '');
    lastInternalCopy = selection;
    lastInternalCopyAt = Date.now();
    logEvent('copy', { length: selection.length, target: e.target });

    if (!settings.allow_copy) {
      e.preventDefault();
      showToast('Copy blocked', 'Copying is disabled for this assignment.');
    }
  }, true);

  document.addEventListener('cut', e => {
    const selection = String(window.getSelection?.() || '');
    lastInternalCopy = selection;
    lastInternalCopyAt = Date.now();
    logEvent('cut', { length: selection.length, target: e.target });

    if (!settings.allow_copy) {
      e.preventDefault();
      showToast('Cut blocked', 'Cut is disabled for this assignment.');
    }
  }, true);

  document.addEventListener('paste', e => {
    const text = e.clipboardData?.getData('text') || '';
    const copiedInsideRecently = lastInternalCopy && text === lastInternalCopy && (Date.now() - lastInternalCopyAt) < 30000;

    logEvent('paste', { length: text.length, internal_copy_match: !!copiedInsideRecently, target: e.target });

    if (!settings.allow_paste) {
      e.preventDefault();
      showToast('Paste blocked', 'Pasting is disabled for this assignment.');
      if (settings.lock_screen_on_violation) lockAttempt('Pasting is not allowed in this assignment attempt.', 'blocked_paste');
      else logEvent('blocked_paste', { reason: 'paste_disabled', target: e.target });
      return;
    }

    if (settings.block_external_paste && !copiedInsideRecently) {
      e.preventDefault();
      showToast('External paste blocked', 'Only text copied from inside this assignment attempt can be pasted.');
      if (settings.lock_screen_on_violation) lockAttempt('External paste was detected and blocked.', 'blocked_paste');
      else logEvent('blocked_paste', { reason: 'external_paste', target: e.target });
    }
  }, true);

  document.addEventListener('contextmenu', e => {
    if (settings.allow_right_click) return;
    e.preventDefault();
    showToast('Right click blocked', 'Right click is disabled for this assignment.');
    logEvent('right_click', { target: e.target });
  }, true);

  document.addEventListener('keydown', e => {
    const key = String(e.key || '').toLowerCase();
    const ctrlOrMeta = e.ctrlKey || e.metaKey;
    const devtools = key === 'f12' || (ctrlOrMeta && e.shiftKey && ['i', 'j', 'c'].includes(key)) || (ctrlOrMeta && key === 'u');
    const pasteShortcut = ctrlOrMeta && key === 'v';
    const copyShortcut = ctrlOrMeta && ['c', 'x'].includes(key);

    if (devtools && !settings.allow_devtools_shortcuts) {
      e.preventDefault();
      showToast('Shortcut blocked', 'Developer/browser-source shortcuts are disabled.');
      if (settings.lock_screen_on_violation) lockAttempt('A restricted developer/browser shortcut was detected.', 'devtools_shortcut');
      else logEvent('devtools_shortcut', { key: e.key });
      return;
    }

    if (pasteShortcut && !settings.allow_paste) {
      e.preventDefault();
      showToast('Paste shortcut blocked', 'Paste shortcuts are disabled for this assignment.');
      // Same policy as a paste from the menu: the server treats blocked_paste
      // as locking when "Lock screen on critical violation" is on.
      if (settings.lock_screen_on_violation) lockAttempt('Pasting is not allowed in this assignment attempt.', 'blocked_paste');
      else logEvent('blocked_paste', { shortcut: true });
    }

    if (copyShortcut && !settings.allow_copy) {
      e.preventDefault();
      showToast('Copy shortcut blocked', 'Copy/cut shortcuts are disabled for this assignment.');
      logEvent('copy_shortcut_blocked', { key: e.key });
    }
  }, true);

  async function requestFullscreenIfNeeded() {
    if (!settings.require_fullscreen) return;
    if (document.fullscreenElement) return;
    fullscreenOverlay?.classList.add('show');
  }

  fullscreenBtn?.addEventListener('click', async () => {
    try {
      await document.documentElement.requestFullscreen();
      fullscreenOverlay?.classList.remove('show');
      logEvent('fullscreen_entered', {});
    } catch (err) {
      showToast('Fullscreen required', 'Your browser refused fullscreen. Please allow fullscreen for this assignment.');
      logEvent('fullscreen_request_failed', { message: err.message });
    }
  });

  document.addEventListener('fullscreenchange', () => {
    if (!settings.require_fullscreen) return;
    if (!document.fullscreenElement) {
      logEvent('fullscreen_exit', {});
      showToast('Fullscreen exited', 'Fullscreen is required for this assignment.');
      if (settings.lock_screen_on_violation) lockAttempt('Fullscreen mode was exited during this protected assignment attempt.', 'fullscreen_exit');
      else requestFullscreenIfNeeded();
    }
  });

  async function detectDualMonitor() {
    if (!settings.detect_dual_monitor) return;

    try {
      if ('getScreenDetails' in window) {
        const details = await window.getScreenDetails();
        const screens = details?.screens?.length || 1;
        if (screens > 1) {
          showToast('Multiple screens detected', 'Your browser reported more than one screen.');
          logEvent('dual_monitor_detected', { screens });
          if (settings.block_dual_monitor) lockAttempt('Multiple screens were detected and blocked by your instructor settings.', 'dual_monitor_detected');
        }
        return;
      }

      if ('isExtended' in window.screen && window.screen.isExtended) {
        showToast('Extended display detected', 'Your browser reported an extended display.');
        logEvent('dual_monitor_detected', { is_extended: true });
        if (settings.block_dual_monitor) lockAttempt('An extended display was detected and blocked by your instructor settings.', 'dual_monitor_detected');
        return;
      }

      logEvent('dual_monitor_check_unavailable', { supported: false });
    } catch (err) {
      logEvent('dual_monitor_check_failed', { message: err.message });
    }
  }

  const identityForm = protectedForm() || document.querySelector('form');
  if (identityForm) clientTools.setHiddenField(identityForm, document, '_anti_cheat_session_id', sessionKey);

  window.DataSenseiAntiCheat.logEvent = logEvent;
  window.DataSenseiAntiCheat.lockAttempt = lockAttempt;

  if (initialState?.blocked) {
    // The server already considers this attempt blocked: show it locked right
    // away instead of an editable form that can only be rejected later.
    lockAttempt(initialState.reason || 'This assignment attempt was locked because a restricted action was detected.', null);
    return;
  }

  requestFullscreenIfNeeded();
  detectDualMonitor();
})();
</script>
@endif
