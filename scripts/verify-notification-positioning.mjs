import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const read = relativePath => fs.readFileSync(path.join(projectRoot, relativePath), 'utf8');

const walk = (directory, suffix) => {
  const matches = [];

  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const target = path.join(directory, entry.name);
    if (entry.isDirectory()) matches.push(...walk(target, suffix));
    else if (entry.name.endsWith(suffix)) matches.push(target);
  }

  return matches;
};

const uiPolish = read('resources/views/partials/ui-polish.blade.php');
const notificationCss = uiPolish.match(/<style id="datasensei-global-notifications-style">([\s\S]*?)<\/style>/)?.[1] ?? '';
const notificationScript = uiPolish.match(/<script id="datasensei-global-notifications-script">([\s\S]*?)<\/script>/)?.[1] ?? '';

assert(notificationCss, 'Shared global notification CSS is missing.');
assert(notificationScript, 'Shared global notification JavaScript is missing.');
const stackRule = notificationCss.match(/#ds-global-notification-stack\s*\{([^}]*)\}/i)?.[1] ?? '';
assert(stackRule, 'The shared notification stack rule is missing.');
assert.match(stackRule, /position:\s*relative\s*!important/i);
assert.match(stackRule, /inset:\s*auto\s*!important/i);
assert.doesNotMatch(stackRule, /position:\s*(?:fixed|absolute|sticky)/i, 'The global stack is attached to a viewport or positioned ancestor.');
assert.doesNotMatch(stackRule, /(?:^|;)\s*(?:top|right|bottom|left)\s*:/i, 'The global stack has edge coordinates instead of page-flow spacing.');
assert.match(stackRule, /z-index:\s*10000/i);
assert.match(stackRule, /width:\s*min\(420px,\s*calc\(100%\s*-\s*32px\)\)/i);
assert.match(stackRule, /margin:\s*16px[^;]*16px\s+auto\s*!important/i);
assert.match(notificationCss, /flex-direction:\s*column/i);
assert.match(notificationCss, /align-items:\s*flex-end/i);
assert.match(notificationCss, /overflow:\s*visible/i);
assert.match(notificationCss, /#ds-global-notification-stack\[hidden\][\s\S]*?display:\s*none\s*!important/i);
assert.match(notificationCss, /@media\s*\(max-width:\s*560px\)[\s\S]*?width:\s*calc\(100%\s*-\s*24px\)/i);
assert.match(notificationCss, /@media\s*\(max-width:\s*560px\)[\s\S]*?margin:\s*12px[^;]*12px\s+auto\s*!important/i);
assert.doesNotMatch(notificationCss, /100vw/i, 'Notification sizing must use the page area, not the viewport.');
assert.match(notificationCss, />\s*\[data-ds-global-notification\][\s\S]*?position:\s*relative\s*!important/i);
assert.match(notificationCss, />\s*\[data-ds-global-notification\][\s\S]*?bottom:\s*auto\s*!important/i);
assert.match(notificationCss, />\s*\[data-ds-global-notification\][\s\S]*?left:\s*auto\s*!important/i);

for (const key of ['success', 'error', 'warning', 'info', 'status', 'message']) {
  assert(uiPolish.includes(`'${key}' =>`), `Standard server flash key "${key}" is not registered.`);
}

for (const behavior of [
  'pageHostSelector',
  'canHostPageNotifications',
  'navigationBoundary.insertAdjacentElement',
  "stack.setAttribute('data-ds-page-flow-notifications'",
  "pageHost.setAttribute('data-ds-notification-page-host'",
  'syncStackVisibility',
  'MutationObserver',
  'api.host',
  'api.mount',
  'api.show',
  'api.dismiss',
]) {
  assert(notificationScript.includes(behavior), `Shared notification behavior "${behavior}" is missing.`);
}
assert.doesNotMatch(notificationScript, /showPopover|hidePopover|popover-open|setAttribute\(['"]popover/i);
assert.doesNotMatch(notificationScript, /updateTopOffset|addEventListener\(['"]scroll/i);
assert.doesNotMatch(notificationScript, /document\.body\.appendChild\(stack\)/i);
assert.match(notificationScript, /const\s+navigationSelector\s*=/i);
assert.match(notificationScript, /navigationBoundary\.insertAdjacentElement\(['"]afterend['"],\s*stack\)/i);
assert.match(notificationScript, /else\s+insertionHost\.prepend\(stack\)/i);

const syntaxCheck = notificationScript
  .replace('@json($dsGlobalFlashNotifications)', '[]')
  .replace('@json($dsGlobalValidationMessages)', '[]');
new Function(syntaxCheck);

const antiCheat = read('resources/views/student/partials/anti-cheat-guard.blade.php');
const antiCheatToastCss = antiCheat.match(/\.ds-ac-toast\s*\{([^}]*)\}/)?.[1] ?? '';
assert.match(antiCheatToastCss, /position:\s*relative/i);
assert.match(antiCheatToastCss, /inset:\s*auto/i);
assert.match(antiCheatToastCss, /margin:\s*16px\s+16px\s+0\s+auto/i);
assert.doesNotMatch(antiCheatToastCss, /position:\s*(?:fixed|absolute|sticky)/i);
assert.doesNotMatch(antiCheatToastCss, /(?:^|;)\s*(?:top|right|bottom|left)\s*:/i);
assert.match(antiCheat, /id="ds-ac-toast"[^>]*data-ds-global-notification[^>]*role="alert"/i);

const explicitRuntimeSources = [
  'resources/views/student/partials/exceptional-unlock-notifications.blade.php',
  'resources/views/student/challenges-map.blade.php',
  'resources/views/student/coding-challenges-map.blade.php',
  'resources/views/auth/login.blade.php',
  'resources/views/ide/index.blade.php',
];

for (const sourcePath of explicitRuntimeSources) {
  assert(read(sourcePath).includes('data-ds-global-notification'), `${sourcePath} is not registered with the global stack.`);
}

const ideView = read('resources/views/ide/index.blade.php');
assert.match(
  ideView,
  /<\/div>\s*<span\s+id="save-indicator"[^>]*data-ds-global-notification[^>]*><\/span>\s*<div\s+class="workspace">/i,
  'The IDE save notification must be a page-level sibling after the topbar, not a topbar child.'
);

const adminContentManager = read('public/js/admin-content-manager.js');
assert(!adminContentManager.includes('window.alert('), 'A native browser alert remains in the admin content manager.');
assert.match(adminContentManager, /DataSenseiNotifications\.show\(message,\s*\{\s*type:\s*'warning',\s*duration:\s*0\s*\}\)/);
new Function(adminContentManager);

const viewRoot = path.join(projectRoot, 'resources/views');
const viewFiles = walk(viewRoot, '.blade.php');
const views = new Map(viewFiles.map(file => [
  path.relative(viewRoot, file).replace(/\.blade\.php$/, '').split(path.sep).join('.'),
  fs.readFileSync(file, 'utf8'),
]));
const transientPositionConflicts = [];
const contextualNotificationSelector = /(?:ds-notification-(?:center|panel|badge|trigger)|drop-toast|attempt-notice)/i;

for (const [viewName, source] of views) {
  const styleBlocks = Array.from(source.matchAll(/<style[^>]*>([\s\S]*?)<\/style>/gi), match => match[1]);

  for (const styles of styleBlocks) {
    for (const rule of styles.matchAll(/([^{}]+)\{([^{}]*)\}/g)) {
      const selector = rule[1].trim();
      const declarations = rule[2];
      if (!/(?:toast|snackbar|flash|alert|notice|notification)/i.test(selector)) continue;
      if (contextualNotificationSelector.test(selector)) continue;
      if (/position\s*:\s*(?:fixed|absolute|sticky)/i.test(declarations)) {
        transientPositionConflicts.push(`${viewName}: ${selector.replace(/\s+/g, ' ')}`);
      }
    }
  }
}

assert.deepEqual(
  transientPositionConflicts,
  [],
  `Transient notification rules still use fixed/absolute/sticky positioning: ${transientPositionConflicts.join(', ')}`
);
const dependencies = new Map();

for (const [name, source] of views) {
  const linkedViews = [];
  for (const pattern of [
    /@include(?:If|When|Unless|First)?\(\s*['"]([^'"]+)['"]/g,
    /@extends\(\s*['"]([^'"]+)['"]/g,
  ]) {
    for (const match of source.matchAll(pattern)) linkedViews.push(match[1]);
  }
  dependencies.set(name, linkedViews);
}

const reaches = (start, target, seen = new Set()) => {
  if (start === target) return true;
  if (seen.has(start)) return false;
  seen.add(start);
  return (dependencies.get(start) ?? []).some(next => reaches(next, target, new Set(seen)));
};

const controllerRoot = path.join(projectRoot, 'app/Http/Controllers');
const controllerViews = new Set();
for (const file of walk(controllerRoot, '.php')) {
  const source = fs.readFileSync(file, 'utf8');
  for (const match of source.matchAll(/(?:return\s+)?view\(\s*['"]([^'"]+)['"]/g)) controllerViews.add(match[1]);
  for (const match of source.matchAll(/->view\(\s*['"]([^'"]+)['"]/g)) controllerViews.add(match[1]);
}

const coveredApplicationViews = [...controllerViews].filter(view => views.has(view) && reaches(view, 'partials.ui-polish'));
const uncoveredApplicationViews = [...controllerViews]
  .filter(view => views.has(view) && !reaches(view, 'partials.ui-polish'))
  .sort();
assert.deepEqual(
  uncoveredApplicationViews,
  ['student.model-development.report'],
  `Unexpected controller-rendered views without the shared system: ${uncoveredApplicationViews.join(', ')}`
);
const globalMessagePattern = /session\(\s*['"](?:success|error|warning|info|status|message)['"]|\$errors->any\(\)|\$errors->has\(\s*['"](?:general|rate_limit|otp)['"]/;
const messageViews = [...views.entries()]
  .filter(([, source]) => globalMessagePattern.test(source))
  .map(([name]) => name);
const uncoveredMessageViews = messageViews.filter(messageView => {
  if (reaches(messageView, 'partials.ui-polish')) return false;
  return !coveredApplicationViews.some(applicationView => reaches(applicationView, messageView));
});

assert.deepEqual(uncoveredMessageViews, [], `Global-message views without the shared system: ${uncoveredMessageViews.join(', ')}`);

const phpFiles = walk(path.join(projectRoot, 'app'), '.php');
const flashedKeys = new Set();
for (const file of phpFiles) {
  const source = fs.readFileSync(file, 'utf8');
  for (const match of source.matchAll(/->with\(\s*['"](success|error|warning|info|status|message)['"]/g)) flashedKeys.add(match[1]);
}

for (const key of flashedKeys) {
  assert(uiPolish.includes(`'${key}' =>`), `Controller flash key "${key}" is not handled globally.`);
}

const sqlSandbox = read('resources/views/ide/sql_sandbox.blade.php');
assert.match(sqlSandbox, /<dialog\s+class="drop-toast"/i, 'The centered drop-table UI must remain a confirmation dialog, not a toast notification.');

console.log([
  'PASS: page-flow top-right notification audit',
  `- ${coveredApplicationViews.length}/${[...controllerViews].filter(view => views.has(view)).length} controller-rendered views covered (one print-only report excluded)`,
  `- ${messageViews.length} flash/validation templates covered`,
  `- ${flashedKeys.size} currently emitted server flash types handled; all 6 standard keys registered`,
  '- runtime warnings, anti-cheat, exceptional unlocks, login expiry, and IDE save flashes registered',
  '- no transient notification rule remains fixed, absolute, sticky, or Popover/top-layer based',
  '- page-host selection, navbar separation, natural document scrolling, responsive stacking, and syntax checks passed',
].join('\n'));
