@php
  $antiCheatSettings = $antiCheatSettings ?? [];
  $assessmentType = 'assignment';
  $classAssignmentId = $classAssignmentId ?? null;
  $assignmentSubmissionId = $assignmentSubmissionId ?? null;
  $assignmentQuestionId = $assignmentQuestionId ?? null;
@endphp

@if(!empty($antiCheatSettings['enabled']))
<style>
  .ds-ac-toast{position:fixed;right:22px;bottom:22px;z-index:99999;max-width:390px;border:1px solid rgba(245,158,11,.45);background:rgba(17,28,45,.98);color:#fde68a;border-radius:16px;padding:14px 16px;box-shadow:0 18px 55px rgba(0,0,0,.38);font:600 13px/1.45 Inter,system-ui,sans-serif;display:none}.ds-ac-toast.show{display:block}.ds-ac-toast strong{display:block;color:#fff;margin-bottom:3px}.ds-ac-lock{position:fixed;inset:0;z-index:99998;background:rgba(8,15,28,.94);backdrop-filter:blur(5px);display:none;align-items:center;justify-content:center;padding:24px}.ds-ac-lock.show{display:flex}.ds-ac-lock-card{max-width:560px;border:1px solid rgba(239,68,68,.45);background:#111c2d;border-radius:24px;padding:28px;text-align:center;box-shadow:0 22px 75px rgba(0,0,0,.45)}.ds-ac-lock-icon{font-size:2.6rem;margin-bottom:12px}.ds-ac-lock-title{font-size:1.45rem;font-weight:900;color:#fff;margin-bottom:10px}.ds-ac-lock-msg{color:#fecaca;line-height:1.65;margin-bottom:18px}.ds-ac-lock-btn{border:1px solid rgba(59,130,246,.45);background:#3b82f6;color:#fff;border-radius:12px;min-height:42px;padding:0 16px;font-weight:900;cursor:pointer}.ds-ac-fullscreen{position:fixed;inset:0;z-index:99997;background:rgba(8,15,28,.94);display:none;align-items:center;justify-content:center;padding:24px}.ds-ac-fullscreen.show{display:flex}.ds-ac-fullscreen-card{max-width:560px;border:1px solid rgba(59,130,246,.45);background:#111c2d;border-radius:24px;padding:28px;text-align:center}.ds-ac-fullscreen-title{font-size:1.35rem;font-weight:900;color:#fff;margin-bottom:8px}.ds-ac-fullscreen-msg{color:#7f93b0;line-height:1.65;margin-bottom:18px}.ds-ac-fullscreen-btn{border:0;background:#3b82f6;color:white;border-radius:12px;min-height:44px;padding:0 18px;font-weight:900;cursor:pointer}
</style>

<div class="ds-ac-toast" id="ds-ac-toast"><strong id="ds-ac-toast-title">Anti-cheat warning</strong><span id="ds-ac-toast-msg"></span></div>
<div class="ds-ac-lock" id="ds-ac-lock"><div class="ds-ac-lock-card"><div class="ds-ac-lock-icon">🔒</div><div class="ds-ac-lock-title">Assignment Attempt Locked</div><div class="ds-ac-lock-msg" id="ds-ac-lock-msg">This assignment attempt was locked because a restricted action was detected.</div><button type="button" class="ds-ac-lock-btn" onclick="window.location.reload()">Reload Page</button></div></div>
<div class="ds-ac-fullscreen" id="ds-ac-fullscreen"><div class="ds-ac-fullscreen-card"><div class="ds-ac-fullscreen-title">Fullscreen Required</div><div class="ds-ac-fullscreen-msg">Your instructor requires fullscreen mode for this assignment. Leaving fullscreen may be logged as a violation.</div><button type="button" class="ds-ac-fullscreen-btn" id="ds-ac-fullscreen-btn">Enter Fullscreen</button></div></div>

<script>
(() => {
  const settings = @json($antiCheatSettings);
  const assessmentType = 'assignment';
  const classAssignmentId = @json($classAssignmentId);
  const assignmentSubmissionId = @json($assignmentSubmissionId);
  const baseAssignmentQuestionId = @json($assignmentQuestionId);
  const logUrl = @json(route('anti-cheat.events.store'));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token());

  if (!classAssignmentId || !assignmentSubmissionId) return;

  const sessionKey = `ds_ac_assignment_${classAssignmentId}_${assignmentSubmissionId}_${Date.now()}_${Math.random().toString(36).slice(2, 9)}`;
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

  let tabSwitchCount = 0;
  let lockTriggered = false;
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

  async function logEvent(eventType, details = {}) {
    try {
      await fetch(logUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({
          assessment_type: assessmentType,
          event_type: eventType,
          attempt_session_id: sessionKey,
          class_assignment_id: classAssignmentId,
          assignment_submission_id: assignmentSubmissionId,
          assignment_question_id: currentAssignmentQuestionId(details.target || null),
          occurred_at: new Date().toISOString(),
          details: {
            ...details,
            target: undefined,
            url: window.location.pathname,
            visibility_state: document.visibilityState,
            screen_width: window.screen?.width,
            screen_height: window.screen?.height,
            avail_width: window.screen?.availWidth,
            avail_height: window.screen?.availHeight,
          },
        }),
      });
    } catch (err) {
      console.warn('Anti-cheat log failed:', err);
    }
  }

  function disableAttemptInputs() {
    document.querySelectorAll('input, textarea, select, button').forEach(el => {
      if (!el.classList.contains('ds-ac-lock-btn')) el.disabled = true;
    });
    document.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', e => e.preventDefault());
      a.style.pointerEvents = 'none';
      a.style.opacity = '.55';
    });
  }

  function lockAttempt(message, eventType = 'threshold_exceeded') {
    if (lockTriggered) return;
    lockTriggered = true;
    logEvent(eventType, { message, tab_switch_count: tabSwitchCount });
    disableAttemptInputs();
    if (lockMsg) lockMsg.textContent = message;
    lock?.classList.add('show');

    if (settings.auto_submit_mcq_on_violation) {
      const form = document.getElementById('assignmentForm') || document.querySelector('form[data-protected-assessment="1"]');
      if (form) setTimeout(() => form.submit(), 1200);
    }
  }

  function handleTabViolation(kind) {
    if (settings.allow_tab_switch) return;
    tabSwitchCount++;
    logEvent(kind, { tab_switch_count: tabSwitchCount, max_allowed: settings.max_tab_switches });
    const remaining = Math.max(0, (settings.max_tab_switches ?? 0) - tabSwitchCount + 1);
    showToast('Focus warning', `Leaving the assignment window is restricted. Remaining warning(s): ${remaining}`);

    if (settings.block_on_tab_limit && tabSwitchCount > (settings.max_tab_switches ?? 0)) {
      lockAttempt('You exceeded the allowed tab-switch/focus-loss limit for this assignment attempt.');
    }
  }

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') handleTabViolation('visibility_hidden');
  });

  window.addEventListener('blur', () => {
    setTimeout(() => {
      if (!document.hasFocus()) handleTabViolation('window_blur');
    }, 250);
  });

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
      logEvent('blocked_paste', { shortcut: true });
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

  const hiddenInput = document.createElement('input');
  hiddenInput.type = 'hidden';
  hiddenInput.name = '_anti_cheat_session_id';
  hiddenInput.value = sessionKey;
  (document.getElementById('assignmentForm') || document.querySelector('form[data-protected-assessment="1"]') || document.querySelector('form'))?.appendChild(hiddenInput);

  window.DataSenseiAntiCheat.logEvent = logEvent;
  window.DataSenseiAntiCheat.lockAttempt = lockAttempt;

  requestFullscreenIfNeeded();
  detectDualMonitor();
})();
</script>
@endif
