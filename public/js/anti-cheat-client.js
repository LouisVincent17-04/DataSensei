(function (global) {
  'use strict';

  const defaultTransientStatuses = [408, 425, 429, 500, 502, 503, 504];

  function createEventUuid() {
    if (global.crypto && typeof global.crypto.randomUUID === 'function') {
      return global.crypto.randomUUID();
    }

    const bytes = new Uint8Array(16);
    if (global.crypto && typeof global.crypto.getRandomValues === 'function') {
      global.crypto.getRandomValues(bytes);
    } else {
      for (let index = 0; index < bytes.length; index += 1) {
        bytes[index] = Math.floor(Math.random() * 256);
      }
    }

    bytes[6] = (bytes[6] & 0x0f) | 0x40;
    bytes[8] = (bytes[8] & 0x3f) | 0x80;
    const hex = Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');

    return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
  }

  async function responseBody(response) {
    try {
      const text = await response.text();
      if (text === '') return null;

      try {
        return JSON.parse(text);
      } catch (error) {
        return text;
      }
    } catch (error) {
      return null;
    }
  }

  async function postJsonWithRetry(url, payload, options = {}) {
    const fetchImpl = options.fetchImpl
      || (typeof global.fetch === 'function' ? global.fetch.bind(global) : null);
    const maxRetries = Math.max(0, Number(options.maxRetries ?? 1));
    const retryDelayMs = Math.max(0, Number(options.retryDelayMs ?? 250));
    const transientStatuses = new Set(options.transientStatuses || defaultTransientStatuses);
    const wait = options.wait || (delay => new Promise(resolve => global.setTimeout(resolve, delay)));
    const headers = options.headers || {};

    if (typeof fetchImpl !== 'function') {
      return {
        ok: false,
        status: 0,
        attempts: 0,
        data: null,
        error: 'Fetch is not available in this browser.',
      };
    }

    for (let attempt = 0; attempt <= maxRetries; attempt += 1) {
      let response;

      try {
        response = await fetchImpl(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...headers,
          },
          body: JSON.stringify(payload),
          keepalive: true,
        });
      } catch (error) {
        if (attempt < maxRetries) {
          await wait(retryDelayMs);
          continue;
        }

        return {
          ok: false,
          status: 0,
          attempts: attempt + 1,
          data: null,
          error: error instanceof Error ? error.message : String(error),
        };
      }

      const data = await responseBody(response);
      if (response.ok) {
        return {
          ok: true,
          status: response.status,
          attempts: attempt + 1,
          data,
          error: null,
        };
      }

      if (transientStatuses.has(response.status) && attempt < maxRetries) {
        await wait(retryDelayMs);
        continue;
      }

      return {
        ok: false,
        status: response.status,
        attempts: attempt + 1,
        data,
        error: `Anti-cheat endpoint returned HTTP ${response.status}.`,
      };
    }

    return {
      ok: false,
      status: 0,
      attempts: maxRetries + 1,
      data: null,
      error: 'Anti-cheat event could not be delivered.',
    };
  }

  function createFocusLossCoordinator(options = {}) {
    const windowMs = Math.max(0, Number(options.windowMs ?? 2000));
    const now = options.now || (() => Date.now());
    const uuid = options.uuid || createEventUuid;
    const onFocusLoss = options.onFocusLoss;

    if (typeof onFocusLoss !== 'function') {
      throw new TypeError('createFocusLossCoordinator requires an onFocusLoss callback.');
    }

    let lastEventAt = null;
    let lastEventUuid = null;

    return Object.freeze({
      notify(source, details = {}) {
        const occurredAt = Number(now());
        const withinCorrelationWindow = lastEventAt !== null
          && occurredAt >= lastEventAt
          && occurredAt - lastEventAt < windowMs;

        if (withinCorrelationWindow && lastEventUuid) {
          return {
            accepted: false,
            duplicate: true,
            eventUuid: lastEventUuid,
          };
        }

        lastEventAt = occurredAt;
        lastEventUuid = uuid();
        const event = {
          source,
          details,
          occurredAt,
          eventUuid: lastEventUuid,
        };
        onFocusLoss(event);

        return {
          accepted: true,
          duplicate: false,
          eventUuid: lastEventUuid,
        };
      },

      resume() {
        lastEventAt = null;
        lastEventUuid = null;
      },
    });
  }

  const SNAPSHOT_MARKER = 'data-ds-ac-snapshot';
  const FINALIZE_FIELD = '_anti_cheat_finalize';
  const IDENTITY_FIELD = '_anti_cheat_session_id';

  function controlType(control) {
    return String(control.type || '').toLowerCase();
  }

  function isHiddenControl(control) {
    return controlType(control) === 'hidden';
  }

  function isButtonControl(control) {
    const tag = String(control.tagName || '').toLowerCase();
    return tag === 'button' || ['submit', 'button', 'reset', 'image'].includes(controlType(control));
  }

  function formControls(form) {
    return Array.from(form.elements || []);
  }

  /*
   * The name/value pairs a native submission of this form would send now.
   * Mirrors the browser rules that matter here: unnamed and disabled controls
   * are skipped, radios/checkboxes count only when checked, buttons never.
   */
  function successfulEntries(form) {
    const entries = [];

    formControls(form).forEach(control => {
      if (!control.name || control.disabled || isButtonControl(control)) return;
      const type = controlType(control);
      if ((type === 'radio' || type === 'checkbox') && !control.checked) return;
      if (type === 'file') return;

      if (type === 'select-multiple' && control.options) {
        Array.from(control.options).forEach(option => {
          if (option.selected) entries.push([control.name, String(option.value)]);
        });
        return;
      }

      entries.push([control.name, String(control.value ?? '')]);
    });

    return entries;
  }

  function setHiddenField(form, doc, name, value, marker = null) {
    let field = formControls(form).find(control => control.name === name && isHiddenControl(control));

    if (!field) {
      field = doc.createElement('input');
      field.type = 'hidden';
      field.name = name;
      form.appendChild(field);
    }

    field.value = String(value);
    field.disabled = false;
    if (marker && typeof field.setAttribute === 'function') field.setAttribute(marker, '1');

    return field;
  }

  /*
   * Lock the visible controls of a protected form WITHOUT losing anything a
   * later submission needs. Hidden inputs (_token, _method, the protected
   * attempt identity) are never disabled. The current value of every visible
   * answer control is first copied into a hidden input of the same name, and
   * only then is the visible control disabled, so a native form.submit() and
   * new FormData(form) still carry the CSRF token, the identity and the
   * latest answers. Calling it again is harmless.
   */
  function snapshotAndLockForm(form, options = {}) {
    const doc = options.document || global.document;
    const keepEnabled = options.keepEnabled || (() => false);
    const controls = formControls(form);
    const visibleAnswers = [];

    controls.forEach(control => {
      if (isHiddenControl(control) || isButtonControl(control)) return;
      if (!control.name || control.disabled) return;
      visibleAnswers.push(control);
    });

    const snapshot = successfulEntries({ elements: visibleAnswers });

    snapshot.forEach(([name, value]) => {
      const field = doc.createElement('input');
      field.type = 'hidden';
      field.name = name;
      field.value = value;
      if (typeof field.setAttribute === 'function') field.setAttribute(SNAPSHOT_MARKER, '1');
      form.appendChild(field);
    });

    controls.forEach(control => {
      if (isHiddenControl(control) || keepEnabled(control)) return;
      control.disabled = true;
      if (typeof control.setAttribute === 'function') control.setAttribute('aria-disabled', 'true');
    });

    return snapshot;
  }

  /*
   * Finalize a locked attempt: wait (bounded) until the violation event has
   * been delivered so the server records the outcome first, then submit the
   * form with the explicit finalize flag. The server does not depend on the
   * event having arrived: the flag alone already withholds credit.
   */
  async function finalizeLockedAttempt(options = {}) {
    const form = options.form;
    const doc = options.document || global.document;
    const timeoutMs = Math.max(0, Number(options.timeoutMs ?? 3000));
    const wait = options.wait || (delay => new Promise(resolve => global.setTimeout(resolve, delay)));
    const submit = options.submit || (target => global.HTMLFormElement.prototype.submit.call(target));

    if (!form) return { submitted: false, eventSettled: false, entries: [] };

    let eventSettled = false;
    if (options.eventDelivery) {
      const delivery = Promise.resolve(options.eventDelivery)
        .catch(() => null)
        .then(() => { eventSettled = true; });
      await Promise.race([delivery, wait(timeoutMs)]);
    } else {
      eventSettled = true;
    }

    setHiddenField(form, doc, FINALIZE_FIELD, '1');
    if (options.sessionId) setHiddenField(form, doc, IDENTITY_FIELD, options.sessionId);

    if (typeof options.beforeSubmit === 'function') options.beforeSubmit(form);
    const entries = successfulEntries(form);
    submit(form);

    return { submitted: true, eventSettled, entries };
  }

  /*
   * Normalise the authoritative integrity state sent by the server, both when
   * the page is rendered and in every event response.
   */
  function normalizeIntegrityState(state) {
    if (!state || typeof state !== 'object') return null;

    const remaining = state.remaining_allowance;

    return {
      blocked: Boolean(state.blocked),
      reason: typeof state.reason === 'string' && state.reason !== '' ? state.reason : null,
      focusLossCount: Math.max(0, Number(state.focus_loss_count ?? 0) || 0),
      remainingAllowance: remaining === null || remaining === undefined
        ? null
        : Math.max(0, Number(remaining) || 0),
    };
  }

  global.DataSenseiAntiCheatClient = Object.freeze({
    createEventUuid,
    createFocusLossCoordinator,
    finalizeLockedAttempt,
    normalizeIntegrityState,
    postJsonWithRetry,
    setHiddenField,
    snapshotAndLockForm,
    successfulEntries,
  });
})(typeof window !== 'undefined' ? window : globalThis);
