import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';

const read = relativePath => readFileSync(new URL(`../../${relativePath}`, import.meta.url), 'utf8');

const timeoutPartial = read('resources/views/partials/session-timeout.blade.php');
const ideView = read('resources/views/ide/index.blade.php');
const sqlView = read('resources/views/ide/sql_sandbox.blade.php');
const notifications = read('resources/views/student/partials/notification-center.blade.php');

function renderedTimeoutScript() {
  const match = timeoutPartial.match(/<script id="datasensei-session-timeout-script">([\s\S]*?)<\/script>/);
  assert.ok(match, 'session-timeout script was not found');

  return match[1]
    .replaceAll('{{ $idleTimeoutMinutes }}', '240')
    .replaceAll('{{ $idleWarningMinutes }}', '5')
    .replace(/@json\([^;\n]+\)/g, '"/test"');
}

test('session dialog is created after the document body exists', () => {
  const scriptStart = timeoutPartial.indexOf('<script id="datasensei-session-timeout-script">');
  const headSafeMarkup = timeoutPartial.slice(0, scriptStart);

  assert.equal(/<div\b[^>]*id="ds-session-timeout-warning"/i.test(headSafeMarkup), false);
  assert.match(timeoutPartial, /document\.addEventListener\('DOMContentLoaded', initialize/);
  assert.match(timeoutPartial, /document\.body\.appendChild\(overlay\)/);
});

test('session overlay always covers the complete viewport', () => {
  assert.match(timeoutPartial, /position:fixed!important/);
  assert.match(timeoutPartial, /inset:0!important/);
  assert.match(timeoutPartial, /width:100vw!important/);
  assert.match(timeoutPartial, /height:100dvh!important/);
});

test('automatic expiry cannot be trapped by a failed logout request', () => {
  assert.match(timeoutPartial, /catch \(_\) \{[\s\S]*?Continue to the sign-in page in every case\.[\s\S]*?\}\s*redirectToLogin\(expired\);/);
  assert.doesNotMatch(timeoutPartial, /We could not complete sign-out automatically/);
});

test('session navigation suppresses page-level leave-site dialogs', () => {
  assert.match(timeoutPartial, /window\.__dataSenseiSessionEnding = true/);
  assert.match(timeoutPartial, /datasensei:session-ending/);
  assert.match(timeoutPartial, /event\.stopImmediatePropagation\(\)/);
  assert.match(ideView, /if \(window\.__dataSenseiSessionEnding \|\| window\.DataSenseiSession\?\.isEnding\?\.\(\)\) return;/);
});

test('IDE, SQL Sandbox, and notifications redirect cleanly on expired API sessions', () => {
  for (const source of [ideView, sqlView]) {
    assert.match(source, /response\.status !== 401 && response\.status !== 419/);
    assert.match(source, /window\.DataSenseiSession\?\.redirectToLogin/);
    assert.match(source, /redirectWhenSessionExpired\(response\)/);
  }

  assert.match(sqlView, /'X-Requested-With': 'XMLHttpRequest'/);

  assert.match(notifications, /response\.status === 401 \|\| response\.status === 419/);
  assert.match(notifications, /redirectExpiredSession\(loginUrl\)/);
});

test('rendered session-timeout JavaScript remains syntactically valid', () => {
  assert.doesNotThrow(() => new Function(renderedTimeoutScript()));
});

test('expired-session redirect mounts one dialog and bypasses later unload guards', () => {
  const makeClassList = () => {
    const names = new Set();
    return {
      add: (...values) => values.forEach(value => names.add(value)),
      remove: (...values) => values.forEach(value => names.delete(value)),
      contains: value => names.has(value),
    };
  };

  const makeElement = () => ({
    hidden: false,
    disabled: false,
    textContent: '',
    innerHTML: '',
    className: '',
    classList: makeClassList(),
    attributes: new Map(),
    listeners: new Map(),
    setAttribute(name, value) { this.attributes.set(name, value); },
    removeAttribute(name) { this.attributes.delete(name); },
    addEventListener(name, handler) { this.listeners.set(name, handler); },
    focus() {},
  });

  const ids = new Map([
    ['ds-session-timeout-title', makeElement()],
    ['ds-session-timeout-description', makeElement()],
    ['ds-session-timeout-countdown', makeElement()],
    ['ds-session-timeout-server', makeElement()],
    ['ds-session-timeout-actions', makeElement()],
    ['ds-session-stay', makeElement()],
    ['ds-session-sign-out', makeElement()],
  ]);
  const appended = [];
  const documentListeners = new Map();
  const document = {
    readyState: 'complete',
    hidden: false,
    documentElement: { classList: makeClassList() },
    body: { appendChild: element => appended.push(element) },
    createElement: () => makeElement(),
    getElementById: id => ids.get(id) || null,
    addEventListener: (name, handler) => documentListeners.set(name, handler),
  };

  const windowListeners = new Map();
  const dispatched = [];
  const replacements = [];
  const storage = new Map();
  let timerId = 0;
  const window = {
    localStorage: {
      getItem: key => storage.get(key) ?? null,
      setItem: (key, value) => storage.set(key, value),
    },
    location: { replace: destination => replacements.push(destination) },
    addEventListener(name, handler) {
      const handlers = windowListeners.get(name) || [];
      handlers.push(handler);
      windowListeners.set(name, handlers);
    },
    dispatchEvent(event) { dispatched.push(event); },
    setTimeout: () => ++timerId,
    clearTimeout: () => {},
    setInterval: () => ++timerId,
    clearInterval: () => {},
  };

  class FakeCustomEvent {
    constructor(type, options = {}) {
      this.type = type;
      this.detail = options.detail;
    }
  }

  const execute = new Function('window', 'document', 'fetch', 'AbortController', 'CustomEvent', renderedTimeoutScript());
  execute(window, document, async () => ({ ok: true, status: 204 }), AbortController, FakeCustomEvent);

  assert.equal(appended.length, 1);
  assert.equal(appended[0].id, 'ds-session-timeout-warning');
  assert.equal(typeof window.DataSenseiSession?.redirectToLogin, 'function');

  window.DataSenseiSession.redirectToLogin(true, '/login?expired=1');

  assert.equal(window.__dataSenseiSessionEnding, true);
  assert.deepEqual(replacements, ['/login?expired=1']);
  assert.equal(dispatched.at(-1)?.type, 'datasensei:session-ending');
  assert.equal(dispatched.at(-1)?.detail?.expired, true);

  const beforeUnloadGuard = windowListeners.get('beforeunload')?.[0];
  assert.equal(typeof beforeUnloadGuard, 'function');
  let propagationStopped = false;
  beforeUnloadGuard({ stopImmediatePropagation: () => { propagationStopped = true; } });
  assert.equal(propagationStopped, true);
});
