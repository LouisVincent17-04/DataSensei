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

  global.DataSenseiAntiCheatClient = Object.freeze({
    createEventUuid,
    createFocusLossCoordinator,
    postJsonWithRetry,
  });
})(typeof window !== 'undefined' ? window : globalThis);
