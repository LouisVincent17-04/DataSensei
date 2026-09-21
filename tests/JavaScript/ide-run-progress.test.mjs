import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../public/js/ide-run-progress.js', import.meta.url), 'utf8');

function load() {
    const sandbox = { globalThis: {}, Math, Number, Map, Uint8Array, Array, Object };
    sandbox.globalThis = sandbox;
    vm.runInNewContext(source, sandbox);
    return sandbox.DataSenseiRunProgress;
}

test('progress stays between 1 and 99 until the server answers, then shows 100', () => {
    const api = load();
    let clock = 0;
    const progress = api.createRunProgress({ estimateMs: 1000, now: () => clock });

    assert.equal(progress.value(), 1);
    clock = 400; progress.setStage('sending');
    clock = 600; progress.setStage('running');

    for (const at of [700, 1600, 5000, 60000, 600000]) {
        clock = at;
        const value = progress.value();
        assert.ok(value >= 1 && value <= 99, `value ${value} at ${at} ms`);
    }

    progress.finish();
    assert.equal(progress.value(), 100);
    assert.equal(progress.stage(), 'done');
});

test('progress never moves backwards across stages or repeated reads', () => {
    const api = load();
    let clock = 0;
    const progress = api.createRunProgress({ estimateMs: 800, now: () => clock });
    let last = 0;

    const stages = { 300: 'sending', 450: 'running', 2000: 'resuming', 2100: 'running' };

    for (clock = 0; clock <= 4000; clock += 50) {
        if (stages[clock]) progress.setStage(stages[clock]);
        const value = progress.value();
        assert.ok(value >= last, `went from ${last} to ${value} at ${clock} ms`);
        last = value;
    }

    assert.ok(last > 60, `expected real movement, got ${last}`);
});

test('the pace follows the remembered duration of the file', () => {
    const api = load();
    let clock = 0;
    const quick = api.createRunProgress({ estimateMs: 300, now: () => clock });
    const slow = api.createRunProgress({ estimateMs: 6000, now: () => clock });
    quick.setStage('running');
    slow.setStage('running');
    clock = 600;

    assert.ok(quick.value() > 80);
    assert.ok(slow.value() < 30);
});

test('duration memory blends runs and forgets the oldest file', () => {
    const api = load();
    const memory = api.createDurationMemory(2);

    assert.equal(memory.estimate('a.py'), api.DEFAULT_ESTIMATE_MS);
    memory.remember('a.py', 1000);
    memory.remember('a.py', 2000);
    assert.equal(memory.estimate('a.py'), 1600);

    memory.remember('b.py', 500);
    memory.remember('c.py', 700);
    assert.equal(memory.estimate('a.py'), api.DEFAULT_ESTIMATE_MS);
    memory.remember('d.py', Number.NaN);
    assert.equal(memory.estimate('d.py'), api.DEFAULT_ESTIMATE_MS);
});

test('run tokens match what the server accepts and differ per run', () => {
    const api = load();
    const first = api.createRunToken();
    const second = api.createRunToken();

    assert.match(first, /^[A-Za-z0-9_-]{8,64}$/);
    assert.notEqual(first, second);
});

test('every stage has a plain label', () => {
    const api = load();

    assert.equal(api.stageLabel('running', 'main.py'), 'Running main.py');
    assert.equal(api.stageLabel('resuming', 'main.py'), 'Sending your input');
    assert.equal(api.stageLabel('unknown'), 'Working');
});
