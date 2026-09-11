import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';


const helperUrl = new URL('../../public/js/python-ide-input.js', import.meta.url);
const source = await readFile(helperUrl, 'utf8');
const context = vm.createContext({ TextEncoder });
vm.runInContext(source, context, { filename: 'python-ide-input.js' });
const input = context.DataSenseiPythonInput;


test('buildStdin preserves multiple answers in order and terminates every line', () => {
    assert.equal(input.buildStdin([]), '');
    assert.equal(input.buildStdin(['Ada', '21', 'Data Science']), 'Ada\n21\nData Science\n');
    assert.equal(input.buildStdin(['', 'second']), '\nsecond\n');
});

test('promptLabel keeps the exact runtime prompt and supplies only an empty fallback', () => {
    assert.equal(input.promptLabel('Enter your name: '), 'Enter your name: ');
    assert.equal(input.promptLabel('Age?'), 'Age?');
    assert.equal(input.promptLabel(''), 'Program input:');
});

test('outputDelta emits only new output when the isolated program is replayed', () => {
    assert.equal(input.outputDelta('', 'Welcome\nName:'), 'Welcome\nName:');
    assert.equal(
        input.outputDelta('Welcome\nName:', 'Welcome\nName:Hello Ada\nAge:'),
        'Hello Ada\nAge:',
    );
    assert.equal(input.outputDelta('same', 'same'), '');
});

test('answerEcho displays the entered value after its prompt', () => {
    assert.equal(input.answerEcho('Enter your name:', 'Ada'), ' Ada\n');
    assert.equal(input.answerEcho('', 'Ada'), 'Ada\n');
});

test('input size validation uses the same 10000-byte sandbox boundary', () => {
    assert.equal(input.canAppend([], 'a'.repeat(9999)), true);
    assert.equal(input.canAppend([], 'a'.repeat(10000)), false);
    assert.equal(input.MAX_STDIN_BYTES, 10000);
    assert.equal(input.MAX_INTERACTIVE_INPUTS, 40);
});

test('progressStalled fires only when the sandbox read none of the answers', () => {
    // Everything was consumed: the run is progressing.
    assert.equal(input.progressStalled(2, 2), false);
    assert.equal(input.progressStalled(3, 2), false);
    // First prompt of a run - nothing has been sent yet.
    assert.equal(input.progressStalled(0, 0), false);
    // Answers sent, none read: this is the infinite-prompt case.
    assert.equal(input.progressStalled(0, 1), true);
    assert.equal(input.progressStalled(0, 3), true);
    // Partial reads are legitimate (a branch may skip an input on replay).
    assert.equal(input.progressStalled(1, 3), false);
    // An older runner image reports no count; that must never block a run.
    assert.equal(input.progressStalled(undefined, 2), false);
    assert.equal(input.progressStalled(null, 2), false);
    assert.equal(input.progressStalled('', 2), false);
});
