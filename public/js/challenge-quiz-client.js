(function (global) {
  'use strict';

  /*
   * Timer and autosave logic for the standalone MCQ challenge page.
   * Dependency-free so it can be driven from Node tests with fake clocks.
   */

  function defaultMonotonicNow() {
    if (global.performance && typeof global.performance.now === 'function') {
      return global.performance.now();
    }

    return Date.now();
  }

  /**
   * Server-anchored countdown clock.
   *
   * The server time reference and the local reference are stored as ONE pair
   * and are only ever replaced together, so elapsed time can never be counted
   * twice after a heartbeat. Elapsed time is measured on a monotonic clock.
   * The deadline always comes from the server; the client never derives or
   * extends it.
   */
  function createQuizClock(options) {
    const monotonicNow = typeof options.monotonicNow === 'function' ? options.monotonicNow : defaultMonotonicNow;
    const wallNow = typeof options.wallNow === 'function' ? options.wallNow : () => Date.now();
    const suspensionToleranceMs = Number.isFinite(options.suspensionToleranceMs) ? options.suspensionToleranceMs : 2000;

    let expiresAtMs = Number(options.expiresAtMs);
    let anchor = Object.freeze({ serverNowMs: Number(options.serverNowMs), localMs: monotonicNow() });
    let requestCounter = 0;
    let appliedRequestId = 0;
    let lastWallMs = wallNow();
    let lastMonoMs = anchor.localMs;

    function nowMs() {
      return anchor.serverNowMs + Math.max(0, monotonicNow() - anchor.localMs);
    }

    function remainingMs() {
      return Math.max(0, expiresAtMs - nowMs());
    }

    return Object.freeze({
      nowMs,
      remainingMs,
      remainingSeconds() {
        return Math.max(0, Math.floor(remainingMs() / 1000));
      },
      isExpired() {
        return remainingMs() <= 0;
      },
      expiresAtMs() {
        return expiresAtMs;
      },

      /** Call immediately before sending a heartbeat. */
      beginSync() {
        requestCounter += 1;
        return Object.freeze({ id: requestCounter, sentAtLocalMs: monotonicNow() });
      },

      /**
       * Apply a heartbeat response. The server timestamp was taken somewhere
       * between send and receive, so it is anchored at the request midpoint.
       * Responses older than one already applied are ignored.
       */
      completeSync(ticket, data) {
        if (!ticket || !data) return false;
        if (ticket.id <= appliedRequestId) return false;

        const serverNowMs = Number(data.server_now_ms);
        if (!Number.isFinite(serverNowMs) || serverNowMs <= 0) return false;

        const receivedAtLocalMs = monotonicNow();
        const roundTripMs = Math.max(0, receivedAtLocalMs - ticket.sentAtLocalMs);

        appliedRequestId = ticket.id;
        anchor = Object.freeze({
          serverNowMs,
          localMs: ticket.sentAtLocalMs + (roundTripMs / 2),
        });

        const serverExpiresAtMs = Number(data.expires_at_ms);
        if (Number.isFinite(serverExpiresAtMs) && serverExpiresAtMs > 0) {
          expiresAtMs = serverExpiresAtMs;
        }

        lastWallMs = wallNow();
        lastMonoMs = receivedAtLocalMs;

        return true;
      },

      /**
       * Returns true when the wall clock and the monotonic clock disagree
       * about how much time passed since the previous check, which is what a
       * suspended browser or sleeping device looks like. The caller should
       * re-synchronize with the server; the clock never guesses.
       */
      detectSuspension() {
        const wall = wallNow();
        const mono = monotonicNow();
        const drift = Math.abs((wall - lastWallMs) - (mono - lastMonoMs));
        lastWallMs = wall;
        lastMonoMs = mono;

        return drift > suspensionToleranceMs;
      },
    });
  }

  function isTransientStatus(status) {
    return status === undefined
      || status === null
      || status === 0
      || status === 408
      || status === 425
      || status === 429
      || status >= 500;
  }

  /**
   * Per-question autosave queue.
   *
   * Every edited question keeps its own pending entry until the server has
   * acknowledged exactly that edit (matched by sequence number). At most one
   * request per question is in flight; different questions save in parallel.
   * The state is "saved" only when nothing is pending and nothing is in flight.
   */
  function createAutosaveQueue(options) {
    const send = options.send;
    const onState = typeof options.onState === 'function' ? options.onState : () => {};
    const onClosed = typeof options.onClosed === 'function' ? options.onClosed : () => {};
    const debounceMs = Number.isFinite(options.debounceMs) ? options.debounceMs : 180;
    const retryDelaysMs = Array.isArray(options.retryDelaysMs) && options.retryDelaysMs.length
      ? options.retryDelaysMs
      : [1000, 2000, 5000, 10000];
    const setTimer = options.setTimeout || ((fn, ms) => global.setTimeout(fn, ms));
    const clearTimer = options.clearTimeout || (id => global.clearTimeout(id));

    const pending = new Map();   // questionId -> { optionId, seq, dueTimer }
    const inFlight = new Map();  // questionId -> seq
    let seqCounter = Number.isFinite(options.seqBase) ? Math.max(0, Math.floor(options.seqBase)) : 0;
    let retryTimer = null;
    const failures = new Map();  // questionId -> consecutive transient failures
    let rejected = false;
    let closed = false;
    let closedReason = null;
    let lastState = null;
    let idleWaiters = [];

    function isIdle() {
      return pending.size === 0 && inFlight.size === 0;
    }

    function currentState() {
      if (closed) return 'closed';
      if (failures.size > 0) return 'error';
      if (!isIdle()) return 'saving';
      return rejected ? 'error' : 'saved';
    }

    function publish() {
      const state = currentState();
      if (state !== lastState) {
        lastState = state;
        onState(state, { pending: pending.size, inFlight: inFlight.size, reason: closedReason });
      }

      if (isIdle() || closed) {
        const waiters = idleWaiters;
        idleWaiters = [];
        waiters.forEach(resolve => resolve(state));
      }
    }

    function close(reason) {
      closed = true;
      closedReason = reason || null;
      pending.forEach(entry => { if (entry.dueTimer !== null) clearTimer(entry.dueTimer); });
      if (retryTimer !== null) { clearTimer(retryTimer); retryTimer = null; }
      publish();
      onClosed(closedReason);
    }

    function scheduleRetry() {
      if (retryTimer !== null || closed) return;
      const worst = Math.max(1, ...failures.values());
      const delay = retryDelaysMs[Math.min(worst, retryDelaysMs.length) - 1];
      retryTimer = setTimer(() => {
        retryTimer = null;
        pump(true);
      }, delay);
    }

    function dispatch(questionId, entry) {
      if (entry.dueTimer !== null) { clearTimer(entry.dueTimer); entry.dueTimer = null; }
      const sentSeq = entry.seq;
      inFlight.set(questionId, sentSeq);

      let request;
      try {
        request = Promise.resolve(send({ question_id: questionId, option_id: entry.optionId, seq: sentSeq }));
      } catch (error) {
        request = Promise.reject(error);
      }

      request.then(() => {
        inFlight.delete(questionId);
        const latest = pending.get(questionId);
        // Only the acknowledgement of the newest edit clears the entry. An
        // older acknowledgement must never mark a newer edit as saved.
        if (latest && latest.seq === sentSeq) {
          pending.delete(questionId);
        }
        failures.delete(questionId);
        publish();
        pump(false);
      }, error => {
        inFlight.delete(questionId);
        const status = error && error.status;
        const body = (error && error.body) || {};

        if (status === 409) {
          close(body.status || 'finished');
          return;
        }

        if (isTransientStatus(status)) {
          failures.set(questionId, (failures.get(questionId) || 0) + 1);
          publish();
          scheduleRetry();
          return;
        }

        // Permanent rejection of this particular edit (validation, session).
        const latest = pending.get(questionId);
        if (latest && latest.seq === sentSeq) {
          pending.delete(questionId);
        }
        failures.delete(questionId);
        rejected = true;
        publish();
        pump(false);
      });
    }

    function pump(force) {
      if (closed) return;
      pending.forEach((entry, questionId) => {
        if (inFlight.has(questionId)) return;
        if (!force && entry.dueTimer !== null) return;
        // A question that just failed waits for its backoff timer.
        if (!force && failures.has(questionId)) return;
        dispatch(questionId, entry);
      });
      publish();
    }

    return Object.freeze({
      set(questionId, optionId) {
        if (closed) return null;
        const existing = pending.get(questionId);
        if (existing && existing.dueTimer !== null) clearTimer(existing.dueTimer);

        seqCounter += 1;
        rejected = false;
        const entry = { optionId, seq: seqCounter, dueTimer: null };
        // The debounce timer belongs to this question only.
        entry.dueTimer = setTimer(() => {
          entry.dueTimer = null;
          if (pending.get(questionId) === entry) pump(false);
        }, debounceMs);
        pending.set(questionId, entry);
        publish();

        return entry.seq;
      },

      /** Send everything now (tab hidden, page leaving, before submit). */
      flush() {
        if (!closed) {
          if (retryTimer !== null) { clearTimer(retryTimer); retryTimer = null; }
          pump(true);
        }

        return new Promise(resolve => {
          if (isIdle() || closed) {
            resolve(currentState());
            return;
          }
          idleWaiters.push(resolve);
        });
      },

      state: currentState,
      isIdle,
      pendingCount() { return pending.size; },
      inFlightCount() { return inFlight.size; },
      isClosed() { return closed; },
    });
  }

  const api = Object.freeze({ createQuizClock, createAutosaveQueue });

  global.DataSenseiChallengeQuizClient = api;
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = api;
  }
})(typeof window !== 'undefined' ? window : globalThis);
