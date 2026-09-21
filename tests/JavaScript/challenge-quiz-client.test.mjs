import assert from 'node:assert/strict';
import test from 'node:test';

await import('../../public/js/challenge-quiz-client.js');

const client = globalThis.DataSenseiChallengeQuizClient;

/* ---------- helpers ---------- */

const SERVER_START = 1_790_000_000_000;

// Simulates the real server: its own clock advances with true elapsed time.
function world({ limitSeconds = 600 } = {}) {
  const state = { elapsedMs: 0 };
  return {
    state,
    advance(ms) { state.elapsedMs += ms; },
    mono: () => 5_000 + state.elapsedMs,          // performance.now()-like
    wall: () => 1_700_000_000_000 + state.elapsedMs, // deliberately skewed client wall clock
    serverNow: () => SERVER_START + state.elapsedMs,
    expiresAt: SERVER_START + limitSeconds * 1000,
    heartbeatBody() {
      return { server_now_ms: this.serverNow(), expires_at_ms: this.expiresAt };
    },
  };
}

function newClock(w) {
  return client.createQuizClock({
    serverNowMs: w.serverNow(),
    expiresAtMs: w.expiresAt,
    monotonicNow: w.mono,
    wallNow: w.wall,
  });
}

function fakeTimers() {
  let now = 0;
  let nextId = 1;
  const timers = new Map();
  return {
    setTimeout(fn, ms) { const id = nextId++; timers.set(id, { at: now + ms, fn }); return id; },
    clearTimeout(id) { timers.delete(id); },
    async advance(ms) {
      const target = now + ms;
      for (;;) {
        const due = [...timers.entries()].filter(([, t]) => t.at <= target).sort((a, b) => a[1].at - b[1].at)[0];
        if (!due) break;
        timers.delete(due[0]);
        now = due[1].at;
        due[1].fn();
        await flushMicrotasks();
      }
      now = target;
      await flushMicrotasks();
    },
    pending: () => timers.size,
  };
}

async function flushMicrotasks() {
  for (let i = 0; i < 10; i += 1) await Promise.resolve();
}

function deferredServer() {
  const requests = [];
  return {
    requests,
    send(payload) {
      return new Promise((resolve, reject) => {
        requests.push({ payload, resolve: () => resolve({ ok: true }), reject });
      });
    },
  };
}

function httpError(status, body = {}) {
  const error = new Error('Request failed.');
  error.status = status;
  error.body = body;
  return error;
}

/* ---------- DS-10: clock ---------- */

test('600 second quiz shows about 570 seconds at t=30s before and immediately after a heartbeat', () => {
  const w = world();
  const clock = newClock(w);
  assert.equal(clock.remainingSeconds(), 600);

  w.advance(30_000);
  assert.equal(clock.remainingSeconds(), 570);

  const ticket = clock.beginSync();
  assert.equal(clock.completeSync(ticket, w.heartbeatBody()), true);
  assert.equal(clock.remainingSeconds(), 570, 'heartbeat must not subtract the elapsed time a second time');
});

test('heartbeats at 30, 60 and 300 seconds never double count elapsed time', () => {
  const w = world();
  const clock = newClock(w);
  let elapsed = 0;

  for (const at of [30, 60, 90, 120, 300]) {
    w.advance((at - elapsed) * 1000);
    elapsed = at;
    assert.equal(clock.remainingSeconds(), 600 - at, `before heartbeat at ${at}s`);
    clock.completeSync(clock.beginSync(), w.heartbeatBody());
    assert.equal(clock.remainingSeconds(), 600 - at, `after heartbeat at ${at}s`);
  }

  w.advance(1_000);
  assert.equal(clock.remainingSeconds(), 299);
});

test('no automatic expiry before the server deadline with a heartbeat every 30 seconds', () => {
  const w = world();
  const clock = newClock(w);

  for (let second = 1; second < 600; second += 1) {
    w.advance(1_000);
    if (second % 30 === 0) clock.completeSync(clock.beginSync(), w.heartbeatBody());
    assert.equal(clock.isExpired(), false, `client expired early at ${second}s`);
    assert.ok(Math.abs(clock.remainingSeconds() - (600 - second)) <= 1);
  }

  w.advance(1_000);
  assert.equal(clock.isExpired(), true);
  assert.equal(clock.remainingSeconds(), 0);
});

test('two seconds of request latency keeps the timer within one second of the server', () => {
  const w = world();
  const clock = newClock(w);

  w.advance(30_000);
  const ticket = clock.beginSync();
  w.advance(1_000);                 // request travels for 1s
  const body = w.heartbeatBody();   // server stamps its time at t=31s
  w.advance(1_000);                 // response travels for 1s
  clock.completeSync(ticket, body);

  assert.equal(w.state.elapsedMs, 32_000);
  assert.equal(clock.remainingSeconds(), 568);
  assert.ok(Math.abs(clock.nowMs() - w.serverNow()) <= 1);
});

test('asymmetric latency error is bounded by half the round trip and never moves the deadline', () => {
  const w = world();
  const clock = newClock(w);

  w.advance(30_000);
  const ticket = clock.beginSync();
  const body = w.heartbeatBody();   // server answered instantly...
  w.advance(2_000);                 // ...but the response took 2s to arrive
  clock.completeSync(ticket, body);

  assert.ok(Math.abs(clock.nowMs() - w.serverNow()) <= 1_000);
  assert.equal(clock.expiresAtMs(), w.expiresAt);
});

test('out-of-order heartbeat responses: an older response cannot replace a newer sync', () => {
  const w = world();
  const clock = newClock(w);

  w.advance(30_000);
  const slowTicket = clock.beginSync();
  const slowBody = w.heartbeatBody();        // stamped at 30s, delivered very late

  w.advance(30_000);
  const fastTicket = clock.beginSync();
  assert.equal(clock.completeSync(fastTicket, w.heartbeatBody()), true);
  assert.equal(clock.remainingSeconds(), 540);

  w.advance(5_000);
  assert.equal(clock.completeSync(slowTicket, slowBody), false, 'stale response must be ignored');
  assert.equal(clock.remainingSeconds(), 535);
});

test('a failed or malformed heartbeat leaves the clock untouched', () => {
  const w = world();
  const clock = newClock(w);
  w.advance(45_000);

  assert.equal(clock.completeSync(clock.beginSync(), {}), false);
  assert.equal(clock.completeSync(clock.beginSync(), null), false);
  assert.equal(clock.remainingSeconds(), 555);
});

test('browser suspension is detected and a re-sync restores server time without extending the deadline', () => {
  const state = { mono: 0, wall: 1_700_000_000_000, server: SERVER_START };
  const expiresAt = SERVER_START + 600_000;
  const clock = client.createQuizClock({
    serverNowMs: state.server,
    expiresAtMs: expiresAt,
    monotonicNow: () => state.mono,
    wallNow: () => state.wall,
  });

  // 10 normal seconds.
  state.mono += 10_000; state.wall += 10_000; state.server += 10_000;
  assert.equal(clock.detectSuspension(), false);
  assert.equal(clock.remainingSeconds(), 590);

  // Device sleeps 120s: the monotonic clock did not tick, real time did.
  state.wall += 120_000; state.server += 120_000;
  assert.equal(clock.detectSuspension(), true);

  clock.completeSync(clock.beginSync(), { server_now_ms: state.server, expires_at_ms: expiresAt });
  assert.equal(clock.remainingSeconds(), 470);
  assert.equal(clock.expiresAtMs(), expiresAt);
  assert.equal(clock.detectSuspension(), false);
});

test('a client wall clock change does not move the countdown', () => {
  const state = { mono: 0, wall: 1_700_000_000_000 };
  const clock = client.createQuizClock({
    serverNowMs: SERVER_START,
    expiresAtMs: SERVER_START + 600_000,
    monotonicNow: () => state.mono,
    wallNow: () => state.wall,
  });

  state.mono += 30_000;
  state.wall -= 3_600_000; // student sets the clock back one hour
  assert.equal(clock.remainingSeconds(), 570);
});

/* ---------- DS-11: autosave queue ---------- */

function newQueue(server, timers, extra = {}) {
  const states = [];
  const queue = client.createAutosaveQueue({
    send: payload => server.send(payload),
    debounceMs: 180,
    retryDelaysMs: [1000, 2000],
    setTimeout: timers.setTimeout,
    clearTimeout: timers.clearTimeout,
    onState: state => states.push(state),
    ...extra,
  });
  return { queue, states };
}

test('rapid edits to question 101 and 102 inside the debounce window both reach the endpoint', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue, states } = newQueue(server, timers);

  queue.set(101, 1001);
  await timers.advance(50);
  queue.set(102, 1002);
  await timers.advance(400);

  assert.deepEqual(
    server.requests.map(r => [r.payload.question_id, r.payload.option_id]).sort(),
    [[101, 1001], [102, 1002]]
  );
  assert.equal(queue.state(), 'saving');
  assert.ok(!states.includes('saved'), 'nothing is acknowledged yet');
});

test('"Saved" appears only after every pending change is acknowledged, in any order', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue, states } = newQueue(server, timers);

  queue.set(101, 1001);
  queue.set(102, 1002);
  await timers.advance(200);
  assert.equal(server.requests.length, 2);

  // Out-of-order: 102 is acknowledged first.
  server.requests[1].resolve();
  await flushMicrotasks();
  assert.equal(queue.state(), 'saving');
  assert.ok(!states.includes('saved'));

  server.requests[0].resolve();
  await flushMicrotasks();
  assert.equal(queue.state(), 'saved');
  assert.deepEqual(states, ['saving', 'saved']);
});

test('a delayed acknowledgement of an older edit does not mark the newer edit as saved', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue, states } = newQueue(server, timers);

  queue.set(101, 1001);
  await timers.advance(200);
  assert.equal(server.requests.length, 1);

  queue.set(101, 1003);            // student changes the same question while the first save is in flight
  await timers.advance(200);
  assert.equal(server.requests.length, 1, 'one request per question at a time');

  server.requests[0].resolve();    // old acknowledgement arrives
  await flushMicrotasks();
  assert.ok(!states.includes('saved'), 'old ack must not report Saved');
  assert.equal(server.requests.length, 2);
  assert.equal(server.requests[1].payload.option_id, 1003);
  assert.ok(server.requests[1].payload.seq > server.requests[0].payload.seq);

  server.requests[1].resolve();
  await flushMicrotasks();
  assert.equal(queue.state(), 'saved');
});

test('repeated edits of one question inside the debounce window send only the latest value', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue } = newQueue(server, timers);

  queue.set(101, 1001);
  await timers.advance(100);
  queue.set(101, 1002);
  await timers.advance(100);
  assert.equal(server.requests.length, 0);
  await timers.advance(100);

  assert.equal(server.requests.length, 1);
  assert.equal(server.requests[0].payload.option_id, 1002);
});

test('a failed save is retried and never reported as saved in between', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue, states } = newQueue(server, timers);

  queue.set(101, 1001);
  queue.set(102, 1002);
  await timers.advance(200);

  server.requests[0].reject(new TypeError('network down'));
  server.requests[1].resolve();
  await flushMicrotasks();
  assert.equal(queue.state(), 'error');
  assert.ok(!states.includes('saved'));
  assert.equal(queue.pendingCount(), 1);

  await timers.advance(1000);      // first retry
  assert.equal(server.requests.length, 3);
  assert.deepEqual([server.requests[2].payload.question_id, server.requests[2].payload.option_id], [101, 1001]);

  server.requests[2].reject(httpError(503));
  await flushMicrotasks();
  await timers.advance(2000);      // second retry with backoff
  assert.equal(server.requests.length, 4);

  server.requests[3].resolve();
  await flushMicrotasks();
  assert.equal(queue.state(), 'saved');
});

test('flush sends pending answers immediately and resolves once everything is acknowledged', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue } = newQueue(server, timers);

  queue.set(101, 1001);
  queue.set(102, 1002);
  let flushed = null;
  queue.flush().then(state => { flushed = state; });
  await flushMicrotasks();

  assert.equal(server.requests.length, 2, 'flush does not wait for the debounce');
  assert.equal(flushed, null);

  server.requests.forEach(r => r.resolve());
  await flushMicrotasks();
  assert.equal(flushed, 'saved');
  await timers.advance(500);
  assert.equal(server.requests.length, 2, 'debounce timers must not resend acknowledged answers');
});

test('a 409 expired response closes the queue, reports the reason and is never shown as saved', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  let closedReason = null;
  const { queue, states } = newQueue(server, timers, { onClosed: reason => { closedReason = reason; } });

  queue.set(101, 1001);
  await timers.advance(200);
  server.requests[0].reject(httpError(409, { ok: false, status: 'expired' }));
  await flushMicrotasks();

  assert.equal(closedReason, 'expired');
  assert.equal(queue.state(), 'closed');
  assert.ok(!states.includes('saved'));
  assert.equal(queue.set(102, 1002), null);
  await timers.advance(5000);
  assert.equal(server.requests.length, 1);
});

test('a rejected edit (422) is dropped and surfaces as an error, not as saved', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue, states } = newQueue(server, timers);

  queue.set(101, 999999);
  await timers.advance(200);
  server.requests[0].reject(httpError(422));
  await flushMicrotasks();

  assert.equal(queue.state(), 'error');
  assert.ok(!states.includes('saved'));
  await timers.advance(10_000);
  assert.equal(server.requests.length, 1, 'permanent rejections are not retried');
});

test('sequence numbers continue from the server-provided base', async () => {
  const server = deferredServer();
  const timers = fakeTimers();
  const { queue } = newQueue(server, timers, { seqBase: 41 });

  assert.equal(queue.set(101, 1001), 42);
  assert.equal(queue.set(102, 1002), 43);
});
