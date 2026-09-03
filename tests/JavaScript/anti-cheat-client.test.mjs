import assert from 'node:assert/strict';
import test from 'node:test';

await import('../../public/js/anti-cheat-client.js');

const client = globalThis.DataSenseiAntiCheatClient;

function response(status, body = {}) {
  return {
    ok: status >= 200 && status < 300,
    status,
    text: async () => JSON.stringify(body),
  };
}

test('visibilitychange and blur become one logical focus loss', () => {
  let now = 1000;
  const events = [];
  const uuids = [
    '11111111-1111-4111-8111-111111111111',
    '22222222-2222-4222-8222-222222222222',
  ];
  const coordinator = client.createFocusLossCoordinator({
    windowMs: 2000,
    now: () => now,
    uuid: () => uuids.shift(),
    onFocusLoss: event => events.push(event),
  });

  const hidden = coordinator.notify('visibility_hidden');
  now = 1250;
  const blur = coordinator.notify('window_blur');

  assert.equal(hidden.accepted, true);
  assert.equal(blur.accepted, false);
  assert.equal(blur.duplicate, true);
  assert.equal(blur.eventUuid, hidden.eventUuid);
  assert.equal(events.length, 1);

  coordinator.resume();
  now = 1500;
  const nextRealFocusLoss = coordinator.notify('visibility_hidden');

  assert.equal(nextRealFocusLoss.accepted, true);
  assert.equal(events.length, 2);
  assert.notEqual(events[0].eventUuid, events[1].eventUuid);
});

test('transient HTTP failures retry once with the same payload', async () => {
  const requests = [];
  const fetchImpl = async (url, options) => {
    requests.push({ url, options });
    return requests.length === 1
      ? response(503, { message: 'temporarily unavailable' })
      : response(200, { ok: true });
  };
  const payload = {
    event_type: 'fullscreen_entered',
    event_uuid: '33333333-3333-4333-8333-333333333333',
  };

  const result = await client.postJsonWithRetry('/anti-cheat/events', payload, {
    fetchImpl,
    maxRetries: 1,
    retryDelayMs: 0,
    wait: async () => {},
  });

  assert.equal(result.ok, true);
  assert.equal(result.attempts, 2);
  assert.equal(requests.length, 2);
  assert.equal(requests[0].options.body, requests[1].options.body);
});

test('validation failures are reported and are not retried', async () => {
  let requestCount = 0;
  const result = await client.postJsonWithRetry('/anti-cheat/events', {}, {
    fetchImpl: async () => {
      requestCount += 1;
      return response(422, { message: 'invalid event' });
    },
    maxRetries: 1,
    retryDelayMs: 0,
    wait: async () => {},
  });

  assert.equal(result.ok, false);
  assert.equal(result.status, 422);
  assert.equal(result.attempts, 1);
  assert.equal(requestCount, 1);
});

test('a transient network error is retried only within the configured bound', async () => {
  let requestCount = 0;
  const result = await client.postJsonWithRetry('/anti-cheat/events', {}, {
    fetchImpl: async () => {
      requestCount += 1;
      if (requestCount === 1) throw new TypeError('network unavailable');
      return response(200, { ok: true });
    },
    maxRetries: 1,
    retryDelayMs: 0,
    wait: async () => {},
  });

  assert.equal(result.ok, true);
  assert.equal(result.attempts, 2);
  assert.equal(requestCount, 2);
});
