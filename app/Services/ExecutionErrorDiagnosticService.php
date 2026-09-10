<?php

namespace App\Services;

use Illuminate\Support\Str;

class ExecutionErrorDiagnosticService
{
    /**
     * Build a fast, deterministic explanation for a failed Python or SQL run.
     * This path never depends on Ollama and intentionally never returns code.
     *
     * @return array{status: string, error: string, location: string, explanation: string, steps: array<int, string>}|null
     */
    public function diagnose(string $language, string $runOutput): ?array
    {
        $output = trim($runOutput);
        if ($output === '') {
            return null;
        }

        return $language === 'sql'
            ? $this->diagnoseSql($output)
            : $this->diagnosePython($output);
    }

    public function fallback(
        string $language,
        string $runOutput,
        bool $isChat = false,
        ?string $outcome = null
    ): string {
        $diagnostic = $this->diagnose($language, $runOutput);
        if ($diagnostic !== null) {
            return $this->format($diagnostic);
        }

        $subject = $language === 'sql' ? 'query' : 'program';
        $reason = $this->reasonFor($outcome, $isChat);

        $lines = [
            'Status: Not Reviewed',
            $isChat
                ? "Feedback: {$reason} Your last run is still loaded, so you can ask again in a moment."
                : "Feedback: Your {$subject} ran and reported no execution error. {$reason} Nothing is wrong with your run.",
        ];

        if (! $isChat) {
            $lines[] = 'Check On Your Own:';
            $lines[] = '- Compare the result with the expected output or requirement.';
            $lines[] = '- Test empty, invalid, and boundary inputs where applicable.';
            $lines[] = $language === 'sql'
                ? '- Confirm that the selected tables, columns, joins, filters, and grouping match the current sandbox schema.'
                : '- Review input handling, conditions, loops, function results, and edge cases.';
        }

        return implode("\n", $lines);
    }

    /**
     * Name the real cause instead of always blaming the response deadline, so a
     * stopped service or a missing model is not reported as a slow review.
     */
    private function reasonFor(?string $outcome, bool $isChat): string
    {
        $action = $isChat ? 'answer this follow-up' : 'review it';

        return match ($outcome) {
            'connection_unavailable' => "The AI reviewer is not running right now, so it could not {$action}.",
            'model_unavailable' => "The AI reviewer model is not installed on this server, so it could not {$action}.",
            'model_load_failure' => "The AI reviewer model could not be loaded on this device, so it could not {$action}.",
            'timeout' => "The AI reviewer took longer than the time limit, so it could not {$action}.",
            'duplicate_fallback' => 'The AI reviewer was still finishing your previous request, so this one was skipped.',
            'capacity_fallback' => "The AI reviewer is handling another request right now, so it could not {$action}.",
            'invalid_review', 'malformed_response', 'malformed_stream', 'response_too_large'
                => "The AI reviewer replied in a format this page could not read, so it could not {$action}.",
            'empty_response', 'empty_processed_response', 'incomplete_response', 'incomplete_stream'
                => "The AI reviewer returned an incomplete answer, so it could not {$action}.",
            default => "The AI reviewer was unavailable, so it could not {$action}.",
        };
    }

    /** @param array{status: string, error: string, location: string, explanation: string, steps: array<int, string>} $diagnostic */
    public function format(array $diagnostic): string
    {
        $lines = [
            'Status: '.$diagnostic['status'],
            'Error: '.$diagnostic['error'],
        ];

        if ($diagnostic['location'] !== '') {
            $lines[] = 'Location: '.$diagnostic['location'];
        }

        $lines[] = 'Explanation: '.$diagnostic['explanation'];
        $lines[] = 'Steps to Fix:';

        foreach ($diagnostic['steps'] as $step) {
            $lines[] = '- '.$step;
        }

        return implode("\n", $lines);
    }

    /** @return array{status: string, error: string, location: string, explanation: string, steps: array<int, string>}|null */
    private function diagnosePython(string $output): ?array
    {
        $exitCode = null;
        if (preg_match('/\bExit code:\s*(-?\d+)/i', $output, $match) === 1) {
            $exitCode = (int) $match[1];
        }

        $definitions = [
            'SyntaxError' => [
                'Python could not understand the structure of a statement.',
                ['Check the reported line and the line immediately before it.', 'Check punctuation, quotation marks, brackets, and statement structure.', 'Correct the structure and run the program again.'],
            ],
            'IndentationError' => [
                'Python found inconsistent or missing indentation in a block.',
                ['Open the reported line and its surrounding block.', 'Make the indentation level consistent and avoid mixing tabs with spaces.', 'Run the program again after aligning the block.'],
            ],
            'TabError' => [
                'Python found an inconsistent mixture of tabs and spaces.',
                ['Inspect indentation near the reported line.', 'Convert the block to one consistent indentation style.', 'Run the program again.'],
            ],
            'NameError' => [
                'The program tried to use a name that was not defined in the current scope.',
                ['Check the spelling and capitalization of the reported name.', 'Confirm that the value is assigned before it is used.', 'Check whether the name belongs to another function or scope.'],
            ],
            'TypeError' => [
                'An operation or function received a value of an incompatible type.',
                ['Identify the operation on the final traceback line.', 'Check the types of every value involved in that operation.', 'Convert or validate the input before repeating the operation.'],
            ],
            'ValueError' => [
                'A function received the correct general type but an unacceptable value.',
                ['Check the value described in the final error message.', 'Validate or normalize the input before using it.', 'Test the corrected input path again.'],
            ],
            'ModuleNotFoundError' => [
                'The requested Python module is unavailable in the execution environment.',
                ['Confirm the module name and spelling.', 'Check whether the sandbox permits and includes that dependency.', 'Use an available built-in or approved dependency when installation is unavailable.'],
            ],
            'ImportError' => [
                'Python found the module but could not import the requested component.',
                ['Check the requested component name.', 'Confirm that it exists in the available module version.', 'Review circular imports if the component belongs to another workspace file.'],
            ],
            'ZeroDivisionError' => [
                'The program attempted to divide by zero.',
                ['Find the denominator used at the reported location.', 'Validate the denominator before division.', 'Decide how the program should handle a zero value.'],
            ],
            'IndexError' => [
                'The program requested a sequence position that does not exist.',
                ['Check the sequence length before accessing a position.', 'Review loop boundaries and zero-based indexing.', 'Test with empty and short sequences.'],
            ],
            'KeyError' => [
                'The program requested a dictionary key that is not present.',
                ['Confirm the key spelling and capitalization.', 'Check that the key exists before reading it.', 'Provide a deliberate missing-key behavior.'],
            ],
            'AttributeError' => [
                'The value does not provide the attribute or method being requested.',
                ['Check the type of the value at the reported location.', 'Verify the attribute or method name.', 'Confirm that the value was initialized as expected.'],
            ],
            'FileNotFoundError' => [
                'The program tried to open a file that is not available at the requested path.',
                ['Check the filename and relative path.', 'Confirm that the file exists inside the permitted workspace.', 'Avoid paths outside the sandbox workspace.'],
            ],
            'TimeoutError' => [
                'The operation did not finish within its allowed execution time.',
                ['Check for infinite loops or unexpectedly large work.', 'Reduce unnecessary repeated processing.', 'Run a smaller input to isolate the slow section.'],
            ],
            'MemoryError' => [
                'The program requested more memory than the execution environment could provide.',
                ['Check for unbounded collections or repeated data copies.', 'Process smaller portions of data.', 'Release or reuse large intermediate values where possible.'],
            ],
        ];

        foreach ($definitions as $error => [$explanation, $steps]) {
            if (preg_match('/\b'.preg_quote($error, '/').'\b/i', $output) === 1) {
                return $this->diagnostic($error, $this->pythonLocation($output), $explanation, $steps);
            }
        }

        if (preg_match('/\b([A-Za-z_][A-Za-z0-9_]*(?:Error|Exception))\b/', $output, $match) === 1) {
            return $this->diagnostic(
                $match[1],
                $this->pythonLocation($output),
                'Python stopped because an exception was raised during execution.',
                ['Read the final traceback message first.', 'Inspect the reported line and the values used there.', 'Correct the cause and rerun with the same input.']
            );
        }

        if ($exitCode !== null && $exitCode !== 0) {
            return $this->diagnostic(
                'Python execution failed',
                $this->pythonLocation($output),
                "The program stopped with exit code {$exitCode}, but no recognized Python exception name was available.",
                ['Read the final lines of the terminal output.', 'Inspect the last operation completed before the program stopped.', 'Correct that failure and run the program again.']
            );
        }

        return null;
    }

    /** @return array{status: string, error: string, location: string, explanation: string, steps: array<int, string>}|null */
    private function diagnoseSql(string $output): ?array
    {
        $definitions = [
            '/\bsyntax error\b|\bnear\s+["\']?.+?["\']?:\s*syntax error\b/i' => [
                'SQL syntax error',
                'The database could not parse part of the SQL statement.',
                ['Check the reported SQL clause and the clause immediately before it.', 'Check keywords, commas, parentheses, quotation marks, and clause order.', 'Run one statement at a time to isolate the invalid section.'],
            ],
            '/\bno such table\b|\btable\s+.+\s+does not exist\b/i' => [
                'Table not found',
                'The query refers to a table that is not present in the current sandbox database.',
                ['Check the table list in the schema sidebar.', 'Check spelling and capitalization.', 'Create or select the intended table before running the query.'],
            ],
            '/\bno such column\b|\bunknown column\b|\bhas no column named\b/i' => [
                'Column not found',
                'The query refers to a column that is not available in the selected table or result scope.',
                ['Inspect the table columns in the schema sidebar.', 'Check aliases, spelling, and capitalization.', 'Confirm that the column belongs to the table used in that clause.'],
            ],
            '/\bambiguous column\b/i' => [
                'Ambiguous column',
                'More than one table provides the referenced column name.',
                ['Identify which table should supply the column.', 'Use the appropriate table name or alias to qualify it.', 'Check other shared column names in the same query.'],
            ],
            '/\bconstraint failed\b|\bforeign key constraint\b|\bunique constraint\b|\bnot null constraint\b/i' => [
                'Database constraint violation',
                'The requested change conflicts with a database integrity rule.',
                ['Identify the named constraint and affected value.', 'Check required fields, duplicate values, and referenced records.', 'Correct the data while preserving the intended integrity rule.'],
            ],
            '/\bmisuse of aggregate\b|\baggregate functions are not allowed\b/i' => [
                'Invalid aggregate usage',
                'An aggregate function is being used in an unsupported clause or grouping context.',
                ['Review the grouping and filtering clauses.', 'Separate row filtering from aggregate filtering.', 'Confirm that every selected non-aggregate column is grouped correctly.'],
            ],
            '/\bdatatype mismatch\b/i' => [
                'Data type mismatch',
                'A value is incompatible with the expected database type.',
                ['Check the destination column type.', 'Validate or convert the supplied value.', 'Run the statement again with a compatible value.'],
            ],
            '/\bdatabase is locked\b/i' => [
                'Sandbox database is busy',
                'Another operation is temporarily holding a database lock.',
                ['Wait briefly for the current operation to finish.', 'Avoid sending the same write operation repeatedly.', 'Retry the query once the sandbox is idle.'],
            ],
            '/^\[Security\]/mi' => [
                'SQL Sandbox security restriction',
                'The statement contains an operation that the learning sandbox intentionally does not permit.',
                ['Read the allowed-statement list in the displayed message.', 'Use only the supported learning operations.', 'Keep database access inside the assigned sandbox.'],
            ],
            '/\bnot supported in the sandbox\b/i' => [
                'Unsupported SQL statement',
                'The SQL statement is outside the operations supported by the learning sandbox.',
                ['Review the supported statement types.', 'Choose the supported operation that matches the learning objective.', 'Run each supported statement separately when troubleshooting.'],
            ],
        ];

        foreach ($definitions as $pattern => [$error, $explanation, $steps]) {
            if (preg_match($pattern, $output) === 1) {
                return $this->diagnostic($error, $this->sqlLocation($output), $explanation, $steps);
            }
        }

        if (preg_match('/\b(?:query|statement|sql)\b.*\b(?:failed|error|invalid)\b|\b(?:failed|error|invalid)\b.*\b(?:query|statement|sql)\b/i', $output) === 1) {
            return $this->diagnostic(
                'SQL execution failed',
                $this->sqlLocation($output),
                'The database rejected the statement, but the returned message did not match a more specific known error.',
                ['Read the complete sandbox message.', 'Run one statement at a time.', 'Compare the statement with the current table and column definitions.']
            );
        }

        return null;
    }

    private function pythonLocation(string $output): string
    {
        preg_match_all('/\bline\s+(\d+)\b/i', $output, $matches);
        $lines = $matches[1] ?? [];

        return $lines === [] ? '' : 'Line '.end($lines);
    }

    private function sqlLocation(string $output): string
    {
        if (preg_match('/\bStatement\s+(\d+)\b/i', $output, $match) === 1) {
            return 'Statement '.$match[1];
        }

        return '';
    }

    /**
     * @param array<int, string> $steps
     * @return array{status: string, error: string, location: string, explanation: string, steps: array<int, string>}
     */
    private function diagnostic(string $error, string $location, string $explanation, array $steps): array
    {
        return [
            'status' => 'Has Issues',
            'error' => Str::limit(trim($error), 100, ''),
            'location' => Str::limit(trim($location), 100, ''),
            'explanation' => Str::limit(trim($explanation), 500, ''),
            'steps' => array_slice(array_values($steps), 0, 4),
        ];
    }
}
