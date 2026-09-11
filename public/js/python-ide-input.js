(function (root) {
    'use strict';

    const MAX_INTERACTIVE_INPUTS = 40;
    const MAX_STDIN_BYTES = 10000;

    function normalizeValue(value) {
        return String(value ?? '').replace(/[\r\n]+/g, ' ');
    }

    function buildStdin(values) {
        if (!Array.isArray(values) || values.length === 0) {
            return '';
        }

        return values.map(normalizeValue).join('\n') + '\n';
    }

    function byteLength(value) {
        const text = String(value ?? '');

        if (typeof TextEncoder !== 'undefined') {
            return new TextEncoder().encode(text).length;
        }

        return unescape(encodeURIComponent(text)).length;
    }

    function canAppend(values, value) {
        return byteLength(buildStdin([...(values || []), value])) <= MAX_STDIN_BYTES;
    }

    function promptLabel(prompt) {
        return typeof prompt === 'string' && prompt.length > 0
            ? prompt
            : 'Program input:';
    }

    function outputDelta(previousOutput, currentOutput) {
        const previous = String(previousOutput ?? '');
        const current = String(currentOutput ?? '');

        if (previous === '') {
            return current;
        }

        if (current.startsWith(previous)) {
            return current.slice(previous.length);
        }

        // A program that uses randomness or the current time can produce a
        // different prefix when replayed. Keep the latest output visible and
        // clearly separate it from the earlier attempt.
        return current === '' ? '' : '\n' + current;
    }

    function answerEcho(currentOutput, value) {
        const output = String(currentOutput ?? '');
        const separator = output !== '' && !/\s$/.test(output) ? ' ' : '';

        return separator + normalizeValue(value) + '\n';
    }

    /**
     * True only for the unmistakable signature of stdin never arriving: answers
     * were sent and the sandbox read none of them.
     *
     * Fails open on purpose. A runner image that predates this report sends no
     * count at all, and a missing count must never be read as "nothing was
     * consumed" - that would stop runs that are working perfectly. A partial
     * count (2 of 3) is also fine: a program whose branches depend on random
     * numbers or the clock can legitimately ask fewer times on replay.
     */
    function progressStalled(consumed, supplied) {
        if (consumed === null || consumed === undefined || consumed === '') {
            return false;
        }

        const read = Number(consumed);
        const sent = Number(supplied);

        if (!Number.isFinite(read) || !Number.isFinite(sent) || sent <= 0) {
            return false;
        }

        return read === 0;
    }

    root.DataSenseiPythonInput = Object.freeze({
        MAX_INTERACTIVE_INPUTS,
        MAX_STDIN_BYTES,
        buildStdin,
        canAppend,
        promptLabel,
        outputDelta,
        answerEcho,
        progressStalled,
    });
}(typeof window !== 'undefined' ? window : globalThis));
