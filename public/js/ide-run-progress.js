(function (root) {
    'use strict';

    /*
     * Progress model for one IDE run.
     *
     * The sandbox cannot report how far a Python program has come, so the
     * number is an estimate: fixed shares for the steps the browser does know
     * (saving, sending), then a curve that approaches 95% at a pace taken
     * from how long this file needed last time. It never goes backwards, never
     * reaches 100% before the server has answered, and jumps to 100% when it has.
     */
    const SAVE_SHARE = 8;
    const SEND_SHARE = 15;
    const RUN_CEILING = 95;
    const DEFAULT_ESTIMATE_MS = 1200;

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function createRunProgress(options) {
        const settings = options || {};
        const now = settings.now || (() => Date.now());
        const estimateMs = clamp(Number(settings.estimateMs) || DEFAULT_ESTIMATE_MS, 150, 60000);

        let stage = 'saving';
        let stageStartedAt = now();
        let floor = 0;
        let finished = false;

        function raw() {
            if (finished) return 100;

            const elapsed = Math.max(0, now() - stageStartedAt);

            if (stage === 'saving') {
                return SAVE_SHARE * (1 - Math.exp(-elapsed / 250));
            }

            if (stage === 'sending') {
                return SAVE_SHARE + (SEND_SHARE - SAVE_SHARE) * (1 - Math.exp(-elapsed / 200));
            }

            // Reaches about 63% of the remaining distance after one estimate,
            // 86% after two, and only creeps after that.
            return SEND_SHARE + (RUN_CEILING - SEND_SHARE) * (1 - Math.exp(-elapsed / estimateMs));
        }

        return Object.freeze({
            setStage(next) {
                if (finished || next === stage) return;
                floor = Math.max(floor, raw());
                stage = next;
                stageStartedAt = now();
            },

            finish() {
                finished = true;
            },

            stage() {
                return finished ? 'done' : stage;
            },

            value() {
                floor = clamp(Math.max(floor, raw()), 0, 100);

                // Whole numbers from 1 to 100, and 100 only when finished.
                const shown = Math.round(floor);

                return finished ? 100 : clamp(shown, 1, 99);
            },
        });
    }

    /* Remembers how long each file took, to pace the next run of that file. */
    function createDurationMemory(limit) {
        const max = Math.max(1, Number(limit) || 50);
        const durations = new Map();

        return Object.freeze({
            remember(key, milliseconds) {
                const value = Number(milliseconds);
                if (!Number.isFinite(value) || value <= 0) return;

                const previous = durations.get(key);
                durations.delete(key);
                // Lean towards the newest run; one slow run does not stick forever.
                durations.set(key, previous === undefined ? value : previous * 0.4 + value * 0.6);

                if (durations.size > max) {
                    durations.delete(durations.keys().next().value);
                }
            },

            estimate(key) {
                return durations.has(key) ? durations.get(key) : DEFAULT_ESTIMATE_MS;
            },
        });
    }

    function stageLabel(stage, fileName) {
        const name = fileName || 'your program';

        switch (stage) {
        case 'saving': return 'Saving ' + name;
        case 'sending': return 'Starting the Python sandbox';
        case 'running': return 'Running ' + name;
        case 'resuming': return 'Sending your input';
        case 'done': return 'Finished';
        default: return 'Working';
        }
    }

    function createRunToken() {
        const bytes = new Uint8Array(16);

        if (root.crypto && typeof root.crypto.getRandomValues === 'function') {
            root.crypto.getRandomValues(bytes);
        } else {
            for (let index = 0; index < bytes.length; index++) bytes[index] = Math.floor(Math.random() * 256);
        }

        return Array.from(bytes, (byte) => byte.toString(16).padStart(2, '0')).join('');
    }

    root.DataSenseiRunProgress = Object.freeze({
        DEFAULT_ESTIMATE_MS,
        createRunProgress,
        createDurationMemory,
        createRunToken,
        stageLabel,
    });
}(typeof window !== 'undefined' ? window : globalThis));
