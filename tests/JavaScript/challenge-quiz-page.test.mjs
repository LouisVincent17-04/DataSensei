import assert from 'node:assert/strict';
import fs from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

// Drives the REAL inline script of the MCQ quiz page (with Blade expressions
// replaced by fixture values) together with the real client library.
const viewPath = process.env.DS_QUIZ_VIEW
  || new URL('../../resources/views/student/challenge-quiz.blade.php', import.meta.url);
const view = fs.readFileSync(viewPath, 'utf8');
const library = fs.readFileSync(new URL('../../public/js/challenge-quiz-client.js', import.meta.url), 'utf8');

const SERVER_START = 1_790_000_000_000;

function renderedScript() {
  const inline = [...view.matchAll(/<script>([\s\S]*?)<\/script>/g)].pop()[1];

  return inline
    .replace(/@json\(route\('challenges\.quiz\.(\w+)'[\s\S]*?\]\)\)/g, (_, name) => JSON.stringify('/' + name))
    .replace(/@json\(array_map[\s\S]*?\)\)\)/, '[]')
    .replace(/\{\{ \(int\) \$serverNowMs \}\}/, String(SERVER_START))
    .replace(/\{\{ \(int\) \$expiresAtMs \}\}/, String(SERVER_START + 600_000))
    .replace(/\{\{ \(int\) \$attempt->time_limit_seconds \}\}/, '600')
    .replace(/\{\{ \(int\) \$attempt->id \}\}/, '7')
    .replace(/\{\{ \$challenge->questions->count\(\) \}\}/, '2')
    .replace(/\{\{[\s\S]*?\}\}/g, '0');
}

function bootPage() {
  const state = { elapsedMs: 0, submitted: 0 };
  const timers = new Map();
  let nextTimer = 1;
  const requests = [];
  const element = () => ({
    textContent: '', className: '', value: '', disabled: false, style: {},
    classList: { add() {}, remove() {} },
    addEventListener() {},
  });
  const elements = {};
  const form = element();

  const context = {
    console,
    Promise,
    JSON,
    Math,
    Number,
    String,
    Object,
    Set,
    Map,
    Array,
    Error,
    Date: { now: () => 1_700_000_000_000 + state.elapsedMs },
    performance: { now: () => 1_000 + state.elapsedMs },
    setTimeout: (fn, ms) => { const id = nextTimer++; timers.set(id, { at: state.elapsedMs + ms, fn }); return id; },
    clearTimeout: id => timers.delete(id),
    setInterval: () => 0, // the test calls updateTimer()/heartbeat() itself
    HTMLFormElement: { prototype: { submit() { state.submitted += 1; } } },
    document: {
      hidden: false,
      querySelector: () => ({ content: 'csrf' }),
      getElementById: id => (id === 'quizForm' ? form : (elements[id] ||= element())),
      addEventListener() {},
    },
    addEventListener() {},
    fetch: async (url, options) => {
      const body = JSON.parse(options.body);
      requests.push({ url, body });
      if (url === '/heartbeat') {
        return { ok: true, status: 200, json: async () => ({
          ok: true,
          status: 'in_progress',
          server_now_ms: SERVER_START + state.elapsedMs,
          expires_at_ms: SERVER_START + 600_000,
          should_submit: false,
        }) };
      }
      return { ok: true, status: 200, json: async () => ({ ok: true }) };
    },
  };
  context.window = context;
  context.globalThis = context;
  vm.createContext(context);
  if (view.includes('challenge-quiz-client.js')) vm.runInContext(library, context);
  vm.runInContext(renderedScript(), context);

  return {
    context, state, requests, elements,
    async advance(ms) {
      const target = state.elapsedMs + ms;
      for (;;) {
        const due = [...timers.entries()].filter(([, t]) => t.at <= target).sort((a, b) => a[1].at - b[1].at)[0];
        if (!due) break;
        timers.delete(due[0]);
        state.elapsedMs = due[1].at;
        due[1].fn();
        await settle();
      }
      state.elapsedMs = target;
      await settle();
    },
  };
}

async function settle() {
  for (let i = 0; i < 20; i += 1) await Promise.resolve();
}

test('quiz page: 600s quiz still shows about 570s immediately after the 30s heartbeat', async () => {
  const page = bootPage();
  await settle();

  await page.advance(30_000);
  assert.equal(page.context.remainingSeconds(), 570);

  await page.context.heartbeat();
  assert.equal(page.context.remainingSeconds(), 570);

  for (const at of [60, 300]) {
    await page.advance(at * 1000 - page.state.elapsedMs);
    await page.context.heartbeat();
    assert.equal(page.context.remainingSeconds(), 600 - at);
  }
});

test('quiz page: heartbeats every 30s never auto-submit before the server deadline', async () => {
  const page = bootPage();
  await settle();

  for (let second = 1; second < 600; second += 1) {
    await page.advance(1_000);
    if (second % 30 === 0) await page.context.heartbeat();
    page.context.updateTimer();
    assert.equal(page.state.submitted, 0, `auto-submitted at ${second}s`);
  }

  await page.advance(1_000);
  page.context.updateTimer();
  assert.equal(page.state.submitted, 1);
});

test('quiz page: answers to two questions inside the debounce window are both autosaved before "Saved" shows', async () => {
  const page = bootPage();
  await settle();

  page.context.autosaveAnswer(101, 1001);
  await page.advance(50);
  page.context.autosaveAnswer(102, 1002);
  await page.advance(1_000);

  const saved = page.requests.filter(r => r.url === '/autosave').map(r => [r.body.question_id, r.body.option_id]).sort();
  assert.deepEqual(saved, [[101, 1001], [102, 1002]]);
  assert.match(page.elements.saveState.textContent, /^Saved\./);
});
