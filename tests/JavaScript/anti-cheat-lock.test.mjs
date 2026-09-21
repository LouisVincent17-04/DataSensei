import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const root = new URL('../../', import.meta.url);
const clientSource = readFileSync(new URL('public/js/anti-cheat-client.js', root), 'utf8');
const guardSource = readFileSync(new URL('resources/views/student/partials/anti-cheat-guard.blade.php', root), 'utf8');

await import('../../public/js/anti-cheat-client.js');
const client = globalThis.DataSenseiAntiCheatClient;

/* ---------- minimal DOM ---------- */

class FakeElement {
  constructor(tagName, props = {}) {
    this.tagName = tagName.toUpperCase();
    this.type = tagName === 'input' ? 'text' : (tagName === 'button' ? 'submit' : '');
    this.name = '';
    this.value = '';
    this.checked = false;
    this.disabled = false;
    this.textContent = '';
    this.style = {};
    this.dataset = {};
    this.attributes = {};
    this.listeners = {};
    this.children = [];
    const classes = new Set(props.className ? props.className.split(' ') : []);
    this.classList = {
      add: name => classes.add(name),
      remove: name => classes.delete(name),
      contains: name => classes.has(name),
    };
    Object.assign(this, props);
  }

  setAttribute(name, value) { this.attributes[name] = String(value); }
  getAttribute(name) { return this.attributes[name] ?? null; }
  addEventListener(type, handler) { (this.listeners[type] ||= []).push(handler); }
  dispatchEvent(event) { (this.listeners[event.type] || []).forEach(handler => handler(event)); return true; }
  click() { this.dispatchEvent({ type: 'click' }); }
  closest() { return null; }
}

class FakeForm extends FakeElement {
  constructor() {
    super('form');
    this.elements = [];
    this.nativeSubmissions = [];
  }

  appendChild(element) {
    this.elements.push(element);
    return element;
  }
}

function buildForm() {
  const form = new FakeForm();
  const token = form.appendChild(new FakeElement('input', { type: 'hidden', name: '_token', value: 'csrf-token-value' }));
  const radioWrong = form.appendChild(new FakeElement('input', { type: 'radio', name: 'answers[11]', value: '101' }));
  const radioRight = form.appendChild(new FakeElement('input', { type: 'radio', name: 'answers[11]', value: '102' }));
  const blank = form.appendChild(new FakeElement('input', { type: 'text', name: 'answers[12]', value: '' }));
  const submitButton = form.appendChild(new FakeElement('button', { type: 'submit' }));

  return { form, token, radioWrong, radioRight, blank, submitButton };
}

const toObject = entries => Object.fromEntries(entries);

/* ---------- pure helpers ---------- */

test('locking keeps the CSRF token, the attempt identity and the latest answers submittable', () => {
  const { form, token, radioRight, blank, submitButton } = buildForm();
  const doc = { createElement: tag => new FakeElement(tag) };
  const identity = client.setHiddenField(form, doc, '_anti_cheat_session_id', 'attempt-identity');

  // The learner's latest edits, made after the last autosave.
  radioRight.checked = true;
  blank.value = 'pandas';

  client.snapshotAndLockForm(form, { document: doc });

  assert.equal(token.disabled, false, 'hidden _token must never be disabled');
  assert.equal(identity.disabled, false, 'hidden attempt identity must never be disabled');
  assert.equal(radioRight.disabled, true);
  assert.equal(blank.disabled, true);
  assert.equal(submitButton.disabled, true);

  assert.deepEqual(toObject(client.successfulEntries(form)), {
    _token: 'csrf-token-value',
    _anti_cheat_session_id: 'attempt-identity',
    'answers[11]': '102',
    'answers[12]': 'pandas',
  });

  // Locking twice (event response + local rule) must not duplicate answers.
  client.snapshotAndLockForm(form, { document: doc });
  assert.equal(client.successfulEntries(form).filter(([name]) => name === 'answers[11]').length, 1);
});

test('the old lock (disable every control) is what produced the 419', () => {
  const { form, radioRight } = buildForm();
  radioRight.checked = true;
  form.elements.forEach(element => { element.disabled = true; });

  assert.deepEqual(client.successfulEntries(form), [], 'a fully disabled form submits nothing, not even _token');
});

test('finalization waits for the violation event, then submits with the finalize flag', async () => {
  const { form, radioRight } = buildForm();
  const doc = { createElement: tag => new FakeElement(tag) };
  radioRight.checked = true;
  client.snapshotAndLockForm(form, { document: doc });

  const order = [];
  let deliver;
  const eventDelivery = new Promise(resolve => { deliver = resolve; }).then(() => order.push('event-recorded'));
  const pending = client.finalizeLockedAttempt({
    form,
    document: doc,
    sessionId: 'attempt-identity',
    eventDelivery,
    wait: () => new Promise(() => {}),
    submit: () => order.push('submitted'),
  });

  await Promise.resolve();
  assert.deepEqual(order, [], 'nothing is submitted while the event is still in flight');
  deliver();
  const result = await pending;

  assert.deepEqual(order, ['event-recorded', 'submitted']);
  assert.equal(result.eventSettled, true);
  assert.deepEqual(toObject(result.entries), {
    _token: 'csrf-token-value',
    'answers[11]': '102',
    'answers[12]': '',
    _anti_cheat_finalize: '1',
    _anti_cheat_session_id: 'attempt-identity',
  });
});

test('a blocked or hanging event request cannot postpone finalization beyond the timeout', async () => {
  const { form } = buildForm();
  const doc = { createElement: tag => new FakeElement(tag) };
  const waits = [];
  let submitted = 0;

  const result = await client.finalizeLockedAttempt({
    form,
    document: doc,
    sessionId: 'attempt-identity',
    eventDelivery: new Promise(() => {}),
    timeoutMs: 3000,
    wait: delay => { waits.push(delay); return Promise.resolve(); },
    submit: () => { submitted += 1; },
  });

  assert.deepEqual(waits, [3000]);
  assert.equal(submitted, 1);
  assert.equal(result.eventSettled, false);
  assert.equal(toObject(result.entries)._anti_cheat_finalize, '1', 'the server holds the attempt on this flag alone');
});

test('server integrity state is normalised for the page', () => {
  assert.equal(client.normalizeIntegrityState(null), null);
  assert.deepEqual(
    client.normalizeIntegrityState({ blocked: true, reason: 'Locked.', focus_loss_count: 3, remaining_allowance: 0 }),
    { blocked: true, reason: 'Locked.', focusLossCount: 3, remainingAllowance: 0 }
  );
  assert.deepEqual(
    client.normalizeIntegrityState({ blocked: false, reason: null, focus_loss_count: 1, remaining_allowance: null }),
    { blocked: false, reason: null, focusLossCount: 1, remainingAllowance: null }
  );
});

/* ---------- the real Blade script, executed in a VM ---------- */

function guardScript(config) {
  const scripts = [...guardSource.matchAll(/<script>([\s\S]*?)<\/script>/g)];
  assert.equal(scripts.length, 1, 'exactly one inline guard script');

  const values = {
    '$antiCheatSettings': config.settings,
    '$antiCheatEventContract': config.contract,
    '$classAssignmentId': 7,
    '$assignmentSubmissionId': 9,
    '$assignmentQuestionId': null,
    '$antiCheatSessionId': 'attempt-identity',
    "route('anti-cheat.events.store')": '/anti-cheat/events',
    'csrf_token()': 'csrf-token-value',
    '$antiCheatState': config.state ?? null,
  };

  return scripts[0][1].replace(/@json\(((?:[^()]|\([^()]*\))*)\)/g, (match, expression) => {
    assert.ok(expression in values, `unexpected Blade expression: ${expression}`);
    return JSON.stringify(values[expression]);
  });
}

function bootGuard({ settings = {}, state = null, fetchImpl } = {}) {
  const parts = buildForm();
  const { form } = parts;
  const lock = new FakeElement('div');
  const lockMsg = new FakeElement('div');
  const finalizeBtn = new FakeElement('button', { type: 'button', className: 'ds-ac-lock-btn' });
  const byId = {
    assignmentForm: form,
    'ds-ac-lock': lock,
    'ds-ac-lock-msg': lockMsg,
    'ds-ac-finalize-btn': finalizeBtn,
    'ds-ac-toast': new FakeElement('div'),
    'ds-ac-toast-title': new FakeElement('div'),
    'ds-ac-toast-msg': new FakeElement('div'),
  };
  const documentListeners = {};
  const timers = [];
  const requests = [];

  const document = {
    visibilityState: 'visible',
    fullscreenElement: null,
    documentElement: {},
    hasFocus: () => true,
    getElementById: id => byId[id] ?? null,
    querySelector: selector => {
      if (selector.startsWith('meta')) return { content: 'csrf-token-value' };
      if (selector.startsWith('form')) return form;
      return null;
    },
    querySelectorAll: selector => (selector === 'a' ? [] : [...form.elements, finalizeBtn]),
    createElement: tag => new FakeElement(tag),
    addEventListener: (type, handler) => { (documentListeners[type] ||= []).push(handler); },
  };

  const context = {
    document,
    console: { error() {}, log() {} },
    screen: {},
    location: { pathname: '/student/assignments/7/attempt/9' },
    getSelection: () => '',
    addEventListener() {},
    setTimeout: (fn, delay) => { timers.push({ fn, delay }); return timers.length; },
    clearTimeout() {},
    fetch: (url, options) => {
      const payload = JSON.parse(options.body);
      requests.push(payload);
      return fetchImpl(payload);
    },
    Event: class { constructor(type) { this.type = type; } },
    HTMLFormElement: class {},
  };
  context.HTMLFormElement.prototype.submit = function submit() {
    this.nativeSubmissions.push(client.successfulEntries(this));
  };
  context.window = context;
  vm.createContext(context);
  vm.runInContext(clientSource, context);

  const contract = {
    events: Object.fromEntries([
      'focus_loss', 'threshold_exceeded', 'blocked_paste', 'paste', 'devtools_shortcut', 'right_click',
      'dual_monitor_detected', 'dual_monitor_check_unavailable', 'dual_monitor_check_failed', 'copy', 'cut',
      'fullscreen_exit', 'fullscreen_entered', 'fullscreen_request_failed', 'copy_shortcut_blocked',
    ].map(name => [name, { severity: 'info', classification: 'informational' }])),
    focus_loss_event: 'focus_loss',
    focus_correlation_window_ms: 2000,
    transient_http_statuses: [503],
  };
  const policy = {
    enabled: true, allow_tab_switch: false, max_tab_switches: 2, block_on_tab_limit: true,
    require_fullscreen: false, detect_dual_monitor: false, block_dual_monitor: false,
    allow_copy: true, allow_paste: false, block_external_paste: true, allow_right_click: false,
    allow_devtools_shortcuts: false, show_warnings: true, auto_submit_mcq_on_violation: true,
    lock_screen_on_violation: true, ...settings,
  };

  vm.runInContext(guardScript({ settings: policy, contract, state }), context);

  return {
    ...parts,
    lock,
    lockMsg,
    finalizeBtn,
    requests,
    fire: (type, event = {}) => (documentListeners[type] || []).forEach(handler => handler({
      preventDefault() {}, target: null, ...event,
    })),
    setVisibility: value => { document.visibilityState = value; },
    runTimers: () => timers.splice(0).forEach(timer => timer.fn()),
    settle: async () => { for (let i = 0; i < 20; i += 1) await new Promise(resolve => setImmediate(resolve)); },
  };
}

const okResponse = body => ({ ok: true, status: 200, text: async () => JSON.stringify(body) });

test('auto-submit after a violation sends _token, identity, latest answers and the finalize flag', async () => {
  const page = bootGuard({
    fetchImpl: async () => okResponse({ ok: true, integrity: { blocked: true, reason: 'Locked by server.', focus_loss_count: 0, remaining_allowance: 2 } }),
  });
  page.radioRight.checked = true;
  page.blank.value = 'pandas';

  page.fire('paste', { clipboardData: { getData: () => 'copied elsewhere' } });

  assert.equal(page.lock.classList.contains('show'), true);
  assert.equal(page.token.disabled, false);
  assert.equal(page.blank.disabled, true);
  assert.equal(page.form.nativeSubmissions.length, 0, 'not submitted before the event is recorded');

  await page.settle();
  page.runTimers(); // the 1.2 s auto-submit delay
  await page.settle();

  assert.equal(page.form.nativeSubmissions.length, 1);
  assert.deepEqual(toObject(page.form.nativeSubmissions[0]), {
    _token: 'csrf-token-value',
    _anti_cheat_session_id: 'attempt-identity',
    'answers[11]': '102',
    'answers[12]': 'pandas',
    _anti_cheat_finalize: '1',
  });
  assert.ok(page.requests.some(request => request.event_type === 'blocked_paste'), 'the violation event was posted first');
});

test('a reload of a blocked attempt is locked immediately, keeps its count and offers finalization', async () => {
  const page = bootGuard({
    settings: { auto_submit_mcq_on_violation: false },
    state: { blocked: true, reason: 'Your assignment attempt was locked because it exceeded the allowed tab-switch/focus-loss limit.', focus_loss_count: 3, max_tab_switches: 2, remaining_allowance: 0 },
    fetchImpl: async () => okResponse({ ok: true }),
  });
  page.radioRight.checked = true; // restored from the saved draft by the server

  assert.equal(page.lock.classList.contains('show'), true, 'locked without any new violation');
  assert.match(page.lockMsg.textContent, /focus-loss limit/);
  assert.equal(page.blank.disabled, true);
  assert.equal(page.requests.length, 0, 'restoring the lock does not log another violation');

  page.finalizeBtn.click();
  await page.settle();

  assert.equal(page.form.nativeSubmissions.length, 1);
  const sent = toObject(page.form.nativeSubmissions[0]);
  assert.equal(sent._token, 'csrf-token-value');
  assert.equal(sent._anti_cheat_session_id, 'attempt-identity');
  assert.equal(sent._anti_cheat_finalize, '1');
});

test('the page locks when the server says blocked, using the server focus count', async () => {
  let serverCount = 1; // one focus loss happened before the reload
  const page = bootGuard({
    settings: { auto_submit_mcq_on_violation: false },
    state: { blocked: false, reason: null, focus_loss_count: 1, max_tab_switches: 2, remaining_allowance: 1 },
    fetchImpl: async payload => {
      if (payload.event_type === 'focus_loss') serverCount += 1;
      return okResponse({ ok: true, integrity: {
        blocked: serverCount > 2,
        reason: serverCount > 2 ? 'Locked: focus-loss limit.' : null,
        focus_loss_count: serverCount,
        remaining_allowance: Math.max(0, 2 - serverCount),
      } });
    },
  });

  const loseFocus = async () => {
    page.setVisibility('hidden');
    page.fire('visibilitychange');
    await page.settle();
    page.setVisibility('visible');
    page.fire('visibilitychange'); // resets the client correlation window
  };

  // 2nd logical focus loss (count restored from the server: 1 -> 2): still allowed.
  await loseFocus();
  assert.equal(page.lock.classList.contains('show'), false);
  assert.equal(page.requests.at(-1).details.tab_switch_count, 2, 'counting continues from the server value, not from zero');

  // 3rd: the server reports blocked, the page locks.
  await loseFocus();
  assert.equal(page.lock.classList.contains('show'), true);
  assert.match(page.lockMsg.textContent, /focus-loss limit/);
});
