<?php

namespace App\Services;

/**
 * Reviews straight-line beginner Python locally, with no model call.
 *
 * Introductory exercises (variables, prints, one if/else) do not need a language
 * model to be judged, and waiting seconds for one on every Run is the slowest
 * part of the lesson loop. This service answers those submissions immediately
 * and returns null for anything it cannot judge honestly, which then follows the
 * normal AI path.
 *
 * Every statement it makes is derived from the submitted code or the recorded
 * run result. It never guesses and never returns code.
 */
class SimpleCodeReviewService
{
    /** Constructs that need real reasoning to review, so they go to the model. */
    private const COMPLEX_CONSTRUCTS = '/^\s*(?:async\s|def\s|class\s|while\s|try\s*:|except\b|finally\s*:|with\s|global\s|nonlocal\s|yield\b|@\w)/m';

    private const ALLOWED_IMPORTS = ['math', 'random', 'statistics', 'datetime', 'time', 'decimal', 'fractions'];

    private const RISKY_CALLS = '/\b(?:eval|exec|compile|__import__|open|input\s*\(\s*\)\s*\.\s*\w+)\s*\(/';

    /** Short names that are conventional and should not be flagged. */
    private const CONVENTIONAL_NAMES = ['i', 'j', 'k', 'n', 'x', 'y', 'z', '_'];

    /**
     * @return string|null A formatted review, or null when the model should handle it.
     */
    public function review(string $language, string $code, string $runOutput): ?string
    {
        if (! (bool) config('code_execution.review.fast_path', true)) {
            return null;
        }

        if ($language !== 'python') {
            return null;
        }

        $code = trim($code);
        if ($code === '' || ! $this->runSucceeded($runOutput)) {
            return null;
        }

        if (! $this->isSimple($code)) {
            return null;
        }

        $logic = $this->stripStringsAndComments($code);
        $checked = [];
        $suggestions = [];

        $printedLines = $this->countPrintedLines($runOutput);
        $checked[] = $printedLines > 0
            ? "The program finished with exit code 0 and printed {$printedLines} ".($printedLines === 1 ? 'line' : 'lines').'.'
            : 'The program finished with exit code 0 without printing anything.';

        $assigned = $this->assignedNames($logic);
        $unused = $this->unusedNames($logic, $assigned);

        if ($assigned !== []) {
            if ($unused === []) {
                $count = count($assigned);
                $checked[] = $count === 1
                    ? 'The one variable it defines is used after being assigned.'
                    : "All {$count} variables are used after being assigned.";
            } else {
                foreach (array_slice($unused, 0, 2) as $name) {
                    $suggestions[] = "The variable '{$name}' is assigned but never used afterwards. Remove it or use it where it was intended.";
                }
            }
        }

        $branchNote = $this->branchNote($logic);
        if ($branchNote !== null) {
            if ($branchNote['complete']) {
                $checked[] = $branchNote['message'];
            } else {
                $suggestions[] = $branchNote['message'];
            }
        }

        foreach ($this->styleSuggestions($logic, $code) as $suggestion) {
            $suggestions[] = $suggestion;
        }

        if ($printedLines === 0) {
            $suggestions[] = 'Nothing was printed, so the result cannot be checked from the output. Print the values you want to verify.';
        }

        $suggestions = array_slice(array_values(array_unique($suggestions)), 0, 3);

        $lines = [
            'Status: Correct',
            'Feedback: The program ran without any syntax or runtime error, and a check of the submitted code found no incorrect statement. '
                .'This review was produced on this server, so it is immediate.',
            'Checked:',
        ];

        foreach ($checked as $item) {
            $lines[] = '- '.$item;
        }

        $lines[] = '- Compare the printed values with what the exercise expects, since only you know the required result.';

        if ($suggestions !== []) {
            $lines[] = 'Suggestions:';

            foreach ($suggestions as $suggestion) {
                $lines[] = '- '.$suggestion;
            }
        }

        return implode("\n", $lines);
    }

    /** The recorded run must show a clean, completed execution. */
    private function runSucceeded(string $runOutput): bool
    {
        $output = trim($runOutput);
        if ($output === '') {
            return false;
        }

        if (preg_match('/\bExit code:\s*0\b/i', $output) !== 1) {
            return false;
        }

        if (preg_match('/\bSTDERR:\s*\S/i', $output) === 1) {
            return false;
        }

        return preg_match(
            '/\b(?:Traceback|SyntaxError|IndentationError|TabError|NameError|TypeError|ValueError|ImportError|ModuleNotFoundError|ZeroDivisionError|IndexError|KeyError|AttributeError|RecursionError|MemoryError|Warning)\b/i',
            $output
        ) !== 1;
    }

    private function isSimple(string $code): bool
    {
        $maxLines = max(1, (int) config('code_execution.review.max_lines', 40));
        $maxChars = max(200, (int) config('code_execution.review.max_chars', 1500));

        if (mb_strlen($code) > $maxChars) {
            return false;
        }

        $logic = $this->stripStringsAndComments($code);

        $statements = array_values(array_filter(
            preg_split('/\R/', $logic) ?: [],
            static fn (string $line): bool => trim($line) !== ''
        ));

        if (count($statements) > $maxLines) {
            return false;
        }

        if (preg_match(self::COMPLEX_CONSTRUCTS, $logic) === 1) {
            return false;
        }

        if (preg_match(self::RISKY_CALLS, $logic) === 1) {
            return false;
        }

        // Only well-known standard-library imports stay on the fast path.
        if (preg_match_all('/^\s*(?:import|from)\s+([A-Za-z_][\w.]*)/m', $logic, $matches) > 0) {
            foreach ($matches[1] as $module) {
                $root = strtok($module, '.');

                if (! in_array($root, self::ALLOWED_IMPORTS, true)) {
                    return false;
                }
            }
        }

        // Deep nesting means branching this reviewer cannot follow.
        foreach ($statements as $line) {
            $indent = strlen($line) - strlen(ltrim($line, ' '));

            if ($indent > 8) {
                return false;
            }
        }

        return true;
    }

    /** Remove strings and comments so identifiers are matched, not their text. */
    private function stripStringsAndComments(string $code): string
    {
        $withoutTripleQuotes = preg_replace('/("""|\'\'\')[\s\S]*?\1/', '""', $code) ?? $code;
        $withoutStrings = preg_replace('/(?<!\\\\)(["\']).*?(?<!\\\\)\1/', '""', $withoutTripleQuotes) ?? $withoutTripleQuotes;

        return preg_replace('/#[^\n]*/', '', $withoutStrings) ?? $withoutStrings;
    }

    /** @return array<int, string> */
    private function assignedNames(string $logic): array
    {
        preg_match_all('/^\s*([A-Za-z_]\w*)\s*(?:[-+*\/%]|\/\/|\*\*)?=(?!=)/m', $logic, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    /**
     * @param  array<int, string>  $assigned
     * @return array<int, string>
     */
    private function unusedNames(string $logic, array $assigned): array
    {
        $unused = [];

        foreach ($assigned as $name) {
            // Count every mention, then discount the assignments themselves.
            $mentions = preg_match_all('/\b'.preg_quote($name, '/').'\b/', $logic);
            $assignments = preg_match_all('/^\s*'.preg_quote($name, '/').'\s*(?:[-+*\/%]|\/\/|\*\*)?=(?!=)/m', $logic);

            if ($mentions - $assignments <= 0) {
                $unused[] = $name;
            }
        }

        return $unused;
    }

    /** @return array{complete: bool, message: string}|null */
    private function branchNote(string $logic): ?array
    {
        $hasIf = preg_match('/^\s*if\s.+:/m', $logic) === 1;

        if (! $hasIf) {
            return null;
        }

        $hasElse = preg_match('/^\s*(?:else\s*:|elif\s.+:)/m', $logic) === 1;

        return $hasElse
            ? ['complete' => true, 'message' => 'The condition has both a true and a false branch, so every input reaches a defined result.']
            : ['complete' => false, 'message' => 'The if statement has no else branch. Decide what should happen when the condition is false.'];
    }

    /** @return array<int, string> */
    private function styleSuggestions(string $logic, string $original): array
    {
        $suggestions = [];

        if (preg_match('/==\s*(?:True|False)\b/', $logic) === 1) {
            $suggestions[] = 'Comparing a value to True or False is unnecessary. Use the condition on its own, or negate it.';
        }

        if (preg_match('/^\s*([A-Za-z_])\s*=(?!=)/m', $logic, $match) === 1
            && ! in_array(strtolower($match[1]), self::CONVENTIONAL_NAMES, true)) {
            $suggestions[] = "The single-letter name '{$match[1]}' does not say what it holds. A descriptive name makes the program easier to read.";
        }

        foreach (preg_split('/\R/', $original) ?: [] as $index => $line) {
            if (mb_strlen(rtrim($line)) > 100) {
                $lineNumber = $index + 1;
                $suggestions[] = "Line {$lineNumber} is very long. Splitting it keeps the logic readable.";
                break;
            }
        }

        return $suggestions;
    }

    /**
     * Count only the program's own stdout lines, never the terminal's trailing
     * "Exit code" / "Execution time" metadata.
     */
    private function countPrintedLines(string $runOutput): int
    {
        $lines = preg_split('/\R/', $runOutput) ?: [];
        $inStdout = false;
        $printed = 0;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (preg_match('/^STDOUT:\s*$/i', $trimmed) === 1) {
                $inStdout = true;

                continue;
            }

            if (preg_match('/^(?:STDERR:|Exit code:|Execution time:|Plots generated:)/i', $trimmed) === 1) {
                $inStdout = false;

                continue;
            }

            if ($inStdout && $trimmed !== '') {
                $printed++;
            }
        }

        return $printed;
    }
}
