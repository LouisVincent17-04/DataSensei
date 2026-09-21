import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { dirname, resolve } from 'node:path';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const blade = readFileSync(resolve(root, 'resources/views/student/coding-challenge-quiz.blade.php'), 'utf8');

function loadFill(document) {
  const start = blade.indexOf('function fillQuestionContent(idx, question)');
  const end = blade.indexOf('function dotUpdate(idx)');
  assert.ok(start > 0 && end > start, 'fillQuestionContent must exist in the quiz view');
  return new Function('document', `${blade.slice(start, end)}; return fillQuestionContent;`)(document);
}

function fakeDocument(ids) {
  const make = (tag) => ({
    tag, id: '', className: '', style: {}, dataset: {}, children: [], value: '',
    _text: '',
    set textContent(v) { this._text = String(v); this.children = []; },
    get textContent() { return this._text + this.children.map(c => c.textContent).join(''); },
    set innerHTML(_) { throw new Error('innerHTML must not be used for question content'); },
    appendChild(child) { this.children.push(child); return child; },
  });
  const byId = Object.fromEntries(ids.map(id => [id, Object.assign(make('div'), { id })]));
  const all = [];
  return {
    byId, all,
    createElement: (tag) => { const n = make(tag); all.push(n); return n; },
    getElementById: (id) => byId[id] ?? all.find(n => n.id === id) ?? null,
  };
}

test('DS-14: the quiz view never inlines question content for JavaScript', () => {
  assert.doesNotMatch(blade, /\$question->problem_description/);
  assert.doesNotMatch(blade, /\$question->starter_code/);
  assert.doesNotMatch(blade, /\$question->visibleTestCases/);
  assert.doesNotMatch(blade, /@json\(\s*\$challenge/);
});

test('DS-14: start response content is rendered as text with the server-side ids', () => {
  const doc = fakeDocument(['qpanel-0', 'problem-title-0', 'tc-holder-0', 'editor-0']);
  const fill = loadFill(doc);

  fill(0, {
    problem_description: '<img src=x onerror=alert(1)> Add two numbers',
    starter_code: '# start here',
    test_cases: [{ id: 7, input: '1 2', expected_output: '3' }, { id: 8, input: null, expected_output: '<b>5</b>' }],
  });

  assert.equal(doc.byId['problem-title-0'].textContent, '<img src=x onerror=alert(1)> Add two numbers');
  assert.equal(doc.byId['editor-0'].value, '# start here');
  assert.equal(doc.byId['qpanel-0'].dataset.content, '1');
  for (const id of ['tc-7', 'tc-got-row-7', 'tc-got-7', 'tc-badge-7', 'tc-8']) {
    assert.ok(doc.getElementById(id), `${id} must exist so submit results can be painted`);
  }
  assert.equal(doc.getElementById('tc-got-row-7').style.display, 'none');
  const card8 = doc.getElementById('tc-8');
  assert.ok(!card8.textContent.includes('Input:'), 'a null input renders no Input row');
  assert.ok(card8.textContent.includes('<b>5</b>'));
});

test('DS-14: a resumed panel or typed code is never overwritten', () => {
  const doc = fakeDocument(['qpanel-0', 'problem-title-0', 'tc-holder-0', 'editor-0']);
  const fill = loadFill(doc);
  doc.byId['editor-0'].value = 'my code';
  fill(0, { problem_description: 'A', starter_code: '# s', test_cases: [] });
  assert.equal(doc.byId['editor-0'].value, 'my code');

  fill(0, { problem_description: 'B', starter_code: '# s', test_cases: [] });
  assert.equal(doc.byId['problem-title-0'].textContent, 'A', 'content is filled once');
});
