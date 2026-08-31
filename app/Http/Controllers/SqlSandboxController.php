<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;

class SqlSandboxController extends Controller
{
    // ─── Constants ────────────────────────────────────────────────────────────

    private const MAX_TABLES = 5;
    private const MAX_RESULT_ROWS = 500;
    private const MAX_DATABASE_BYTES = 10 * 1024 * 1024;

    /**
     * Single-keyword blocklist.
     * These are blocked regardless of what follows them.
     */
    private const FORBIDDEN_KEYWORDS = [
        'ATTACH',
        'DETACH',
        'GRANT',
        'REVOKE',
        'LOCK',
        'UNLOCK',
        'LOAD_FILE',
        'LOAD_EXTENSION',
        'RECURSIVE',
        'INTO OUTFILE',
        'INTO DUMPFILE',
    ];

    /**
     * Compound-pattern blocklist (checked via regex).
     *
     * These block dangerous two-word commands that beginners should never use.
     * Even though SQLite doesn't support most of them natively, we block them
     * explicitly so the sandbox stays safe if the DB driver ever changes.
     *
     * Allowed CREATE variants : CREATE TABLE, CREATE INDEX, CREATE VIEW
     * Allowed DROP variants   : DROP TABLE, DROP INDEX, DROP VIEW
     */
    private const FORBIDDEN_PATTERNS = [
        '/\bCREATE\s+(DATABASE|SCHEMA|USER|ROLE|LOGIN|SERVER|TRIGGER|PROCEDURE|FUNCTION|EVENT)\b/i',
        '/\bDROP\s+(DATABASE|SCHEMA|USER|ROLE|LOGIN|SERVER|TRIGGER|PROCEDURE|FUNCTION|EVENT)\b/i',
        '/\bALTER\s+(DATABASE|SCHEMA|USER|ROLE|SYSTEM|SERVER)\b/i',
        '/\bRENAME\s+DATABASE\b/i',
        '/\bSHOW\s+(DATABASES|USERS|GRANTS)\b/i',
        '/\bEXEC\b/i',
        '/\bEXECUTE\b/i',
        '/\bxp_\w+/i',           // SQL Server extended procs
        '/\bsp_\w+/i',           // SQL Server system procs
        '/\bINFORMATION_SCHEMA\b/i',
        '/\b(?:RANDOMBLOB|ZEROBLOB|PRINTF)\s*\(/i',
    ];

    /**
     * Only these statement types are allowed as the very first word.
     * Everything else (VACUUM, REINDEX, etc.) is silently blocked.
     */
    private const ALLOWED_FIRST_WORDS = [
        'SELECT', 'INSERT', 'UPDATE', 'DELETE',
        'CREATE', 'DROP', 'ALTER',
        'PRAGMA', 'EXPLAIN',
    ];

    // ─── Internal helpers ─────────────────────────────────────────────────────

    /**
     * Returns the absolute path to this user's private SQLite database.
     * Write requests may create the directory and database on first use;
     * read-only requests never create filesystem state.
     */
    private function getUserDbPath(bool $create = true): string
    {
        $userId = Auth::id();
        $dir    = storage_path('app/sandbox');

        if ($create) {
            if (! is_dir($dir) && ! mkdir($dir, 0750, true) && ! is_dir($dir)) {
                throw new \RuntimeException('Could not create the SQL sandbox directory.');
            }

            @chmod($dir, 0750);
        }

        $path = $dir . "/user_{$userId}.sqlite";

        if ($create && ! file_exists($path)) {
            if (! touch($path)) {
                throw new \RuntimeException('Could not create the SQL sandbox database.');
            }
        }

        if ($create) {
            @chmod($path, 0600);
        }

        return $path;
    }

    /**
     * Returns (and lazily registers) a named Laravel DB connection that points
     * to the current user's private SQLite file.
     */
    private function getUserConnection(bool $writable = true): string
    {
        $userId   = Auth::id();
        $connName = "sandbox_user_{$userId}";
        $databasePath = $this->getUserDbPath($writable);

        if (! $writable && ! is_file($databasePath)) {
            throw new \RuntimeException('The SQL sandbox database does not exist.');
        }

        if (config("database.connections.{$connName}") === null) {
            config([
                "database.connections.{$connName}" => [
                    'driver'                  => 'sqlite',
                    'database'                => $databasePath,
                    'prefix'                  => '',
                    'foreign_key_constraints' => true,
                ],
            ]);

            $connection = DB::connection($connName);
            $pageSizeRow = $connection->selectOne('PRAGMA page_size');
            $pageSize = max(512, (int) ($pageSizeRow->page_size ?? 4096));
            $maxPages = max(1, (int) floor(self::MAX_DATABASE_BYTES / $pageSize));

            $connection->statement('PRAGMA foreign_keys = ON');
            $connection->statement('PRAGMA trusted_schema = OFF');
            $connection->statement('PRAGMA busy_timeout = 2000');
            if ($writable) {
                $connection->statement("PRAGMA max_page_count = {$maxPages}");
            }
        }

        return $connName;
    }

    /**
     * Returns the list of user-created table names (excludes SQLite internals).
     */
    private function fetchUserTables(string $conn): array
    {
        $rows = DB::connection($conn)->select(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%' ORDER BY name"
        );

        return array_map(static fn ($r) => $r->name, $rows);
    }

    /**
     * Returns column metadata for a given table.
     */
    private function fetchTableColumns(string $conn, string $table): array
    {
        $tables = $this->fetchUserTables($conn);
        if (! in_array($table, $tables, true)) {
            return [];
        }

        $identifier = $this->quoteIdentifier($table);
        $cols = DB::connection($conn)->select("PRAGMA table_info({$identifier})");

        return array_map(static fn ($c) => [
            'name' => $c->name,
            'type' => $c->type ?: 'TEXT',
            'pk'   => (bool) $c->pk,
        ], $cols);
    }

    /**
     * Split on semicolons and remove comments only when they are outside SQL
     * strings or quoted identifiers. A plain explode() corrupted valid values
     * such as INSERT INTO notes VALUES ('first; second').
     */
    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $quote = null;
        $lineComment = false;
        $blockComment = false;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];
            $next = $index + 1 < $length ? $sql[$index + 1] : '';

            if ($lineComment) {
                if ($char === "\n") {
                    $lineComment = false;
                    $buffer .= "\n";
                }
                continue;
            }

            if ($blockComment) {
                if ($char === '*' && $next === '/') {
                    $blockComment = false;
                    $buffer .= ' ';
                    $index++;
                }
                continue;
            }

            if ($quote !== null) {
                $buffer .= $char;
                $closing = $quote === '[' ? ']' : $quote;

                if ($char === $closing) {
                    if ($quote !== '[' && $next === $closing) {
                        $buffer .= $next;
                        $index++;
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }

            if ($char === '-' && $next === '-') {
                $lineComment = true;
                $index++;
                continue;
            }

            if ($char === '/' && $next === '*') {
                $blockComment = true;
                $index++;
                continue;
            }

            if (in_array($char, ["'", '"', '`', '['], true)) {
                $quote = $char;
                $buffer .= $char;
                continue;
            }

            if ($char === ';') {
                $clean = trim($buffer);
                if ($clean !== '') {
                    $statements[] = ['clean' => $clean];
                }
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        $clean = trim($buffer);
        if ($clean !== '') {
            $statements[] = ['clean' => $clean];
        }

        return $statements;
    }

    /** Replace quoted content with spaces before keyword and shape checks. */
    private function sqlForSecurityChecks(string $sql): string
    {
        $result = '';
        $quote = null;
        $length = strlen($sql);

        for ($index = 0; $index < $length; $index++) {
            $char = $sql[$index];
            $next = $index + 1 < $length ? $sql[$index + 1] : '';

            if ($quote !== null) {
                $closing = $quote === '[' ? ']' : $quote;
                $result .= ctype_space($char) ? $char : ' ';

                if ($char === $closing) {
                    if ($quote !== '[' && $next === $closing) {
                        $result .= ' ';
                        $index++;
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }

            if (in_array($char, ["'", '"', '`', '['], true)) {
                $quote = $char;
                $result .= ' ';
                continue;
            }

            $result .= $char;
        }

        return $result;
    }

    private function supportedStatementKind(string $sql): ?string
    {
        $analysis = ltrim($this->sqlForSecurityChecks($sql));

        if (preg_match('/^SELECT\b/i', $analysis) === 1) {
            return 'read';
        }

        if (preg_match('/^(?:INSERT\s+INTO|UPDATE\b|DELETE\s+FROM)\b/i', $analysis) === 1) {
            return 'write';
        }

        if (preg_match('/^CREATE\s+(?:TABLE|(?:UNIQUE\s+)?INDEX|VIEW)\b/i', $analysis) === 1) {
            return 'write';
        }

        if (preg_match('/^DROP\s+(?:TABLE|INDEX|VIEW)\b/i', $analysis) === 1) {
            return 'write';
        }

        if (preg_match('/^ALTER\s+TABLE\b/i', $analysis) === 1) {
            return 'write';
        }

        if (preg_match('/^PRAGMA\s+(?:table_x?info|index_info|index_list|foreign_key_list)\s*\(/i', $analysis) === 1) {
            return 'read';
        }

        if (preg_match('/^EXPLAIN(?:\s+QUERY\s+PLAN)?\s+SELECT\b/i', $analysis) === 1) {
            return 'read';
        }

        return null;
    }

    private function selectLimited(string $connection, string $sql): array
    {
        $statement = DB::connection($connection)->getPdo()->query($sql);
        $rows = [];
        $columns = [];

        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            if ($columns === []) {
                $columns = array_keys($row);
            }

            if (count($rows) >= self::MAX_RESULT_ROWS) {
                return compact('columns', 'rows') + ['truncated' => true];
            }

            $rows[] = array_values($row);
        }

        if ($columns === []) {
            for ($index = 0; $index < $statement->columnCount(); $index++) {
                $metadata = $statement->getColumnMeta($index);
                $columns[] = (string) ($metadata['name'] ?? "column_{$index}");
            }
        }

        return compact('columns', 'rows') + ['truncated' => false];
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }

    private function publicSqlError(PDOException $exception): string
    {
        $message = (string) ($exception->errorInfo[2] ?? $exception->getMessage());
        $message = preg_replace(
            '#(?:[A-Za-z]:[\\\\/]|/)[^\s\'"<>]*\.sqlite\b#i',
            '[sandbox database]',
            $message
        ) ?? 'Invalid SQL statement.';
        $message = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $message)
            ?? 'Invalid SQL statement.';

        return mb_substr(trim($message), 0, 500) ?: 'Invalid SQL statement.';
    }

    // ─── Route handlers ───────────────────────────────────────────────────────

    /**
     * GET /sql-sandbox
     */
    public function index()
    {
        // Retrieve any code or return URLs passed from the lesson
        $pendingCode = session('pending_sql_code', '');
        $returnUrl = session('datasensei_return_url', '');

        return view('ide.sql_sandbox', compact('pendingCode', 'returnUrl'));
    }

    /**
     * GET /sql-sandbox/tables
     */
    public function tables(): JsonResponse
    {
        try {
            if (! is_file($this->getUserDbPath(false))) {
                return response()->json([
                    'status' => 'success',
                    'tables' => [],
                    'count' => 0,
                    'limit' => self::MAX_TABLES,
                ]);
            }

            $conn       = $this->getUserConnection(false);
            $tableNames = $this->fetchUserTables($conn);

            $tables = [];
            foreach ($tableNames as $name) {
                $tables[] = [
                    'name'    => $name,
                    'columns' => $this->fetchTableColumns($conn, $name),
                ];
            }

            return response()->json([
                'status' => 'success',
                'tables' => $tables,
                'count'  => count($tableNames),
                'limit'  => self::MAX_TABLES,
            ]);

        } catch (\Throwable $exception) {
            Log::warning('SQL sandbox table listing failed.', [
                'user_id' => Auth::id(),
                'exception' => $exception::class,
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not load your sandbox tables.',
            ], 500);
        }
    }

    /**
     * DELETE /sql-sandbox/tables/{table}
     */
    public function dropTable(string $table): JsonResponse
    {
        try {
            $conn   = $this->getUserConnection();
            $tables = $this->fetchUserTables($conn);

            if (! in_array($table, $tables, true)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Table '{$table}' does not exist in your sandbox.",
                ], 404);
            }

            $identifier = $this->quoteIdentifier($table);
            DB::connection($conn)->statement("DROP TABLE IF EXISTS {$identifier}");

            return response()->json(['status' => 'success', 'message' => "Table '{$table}' dropped."]);

        } catch (\Throwable $exception) {
            Log::warning('SQL sandbox table deletion failed.', [
                'user_id' => Auth::id(),
                'exception' => $exception::class,
            ]);

            return response()->json(['status' => 'error', 'message' => 'Could not drop the selected table.'], 500);
        }
    }

    /**
     * POST /sql-sandbox/execute
     *
     * Supports multiple statements separated by semicolons.
     * Comments (-- and /* *\/) are stripped before security checks and execution.
     *
     * Execution behaviour:
     *  - Statements run in order; the first error stops the batch.
     *  - If the final statement is a SELECT/PRAGMA/EXPLAIN, its result set is returned.
     *  - Otherwise a plain success message summarising all executed statements is returned.
     */
    public function execute(Request $request): JsonResponse
    {
        // ── 1. Validate raw input ─────────────────────────────────────────────
        $request->validate([
            'query' => ['required', 'string', 'max:5000'],
        ]);

        $rawInput = trim($request->input('query'));

        // ── 2. Split into individual statements (comments stripped per chunk) ─
        $statements = $this->splitStatements($rawInput);

        if (empty($statements)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No executable SQL found. Make sure you have at least one statement.',
            ], 422);
        }

        // ── 3. Security pass — validate every statement BEFORE executing any ─
        //    This prevents partial execution of a batch that contains a bad statement.
        foreach ($statements as $index => $stmt) {
            $sql       = $stmt['clean'];
            $stmtLabel = 'Statement ' . ($index + 1);
            $analysis  = $this->sqlForSecurityChecks($sql);

            // 3a. Single-keyword blocklist
            foreach (self::FORBIDDEN_KEYWORDS as $keyword) {
                if (stripos($analysis, $keyword) !== false) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "[Security] {$stmtLabel}: The keyword '{$keyword}' is not allowed in the SQL Sandbox.",
                    ], 403);
                }
            }

            // 3b. Compound-pattern blocklist
            foreach (self::FORBIDDEN_PATTERNS as $pattern) {
                if (preg_match($pattern, $analysis)) {
                    preg_match($pattern, $analysis, $m);
                    $matched = strtoupper($m[0] ?? 'that command');
                    return response()->json([
                        'status'  => 'error',
                        'message' => "[Security] {$stmtLabel}: '{$matched}' is not allowed in the SQL Sandbox. "
                                   . "Stick to CREATE TABLE, SELECT, INSERT, UPDATE, DELETE, ALTER TABLE, and DROP TABLE.",
                    ], 403);
                }
            }

            // 3c. First-word allowlist and exact supported statement shapes.
            $firstWord = strtoupper(preg_split('/\s+/', ltrim($analysis))[0] ?? '');

            if (! in_array($firstWord, self::ALLOWED_FIRST_WORDS, true) || $this->supportedStatementKind($sql) === null) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "[Security] {$stmtLabel}: This SQL statement is not supported in the sandbox. "
                               . "Use SELECT, INSERT INTO, UPDATE, DELETE FROM, CREATE TABLE/INDEX/VIEW, ALTER TABLE, DROP TABLE/INDEX/VIEW, or a read-only schema PRAGMA.",
                    ], 403);
            }

        }

        // ── 4. Get user connection (auto-creates file if missing) ─────────────
        try {
            $conn = $this->getUserConnection();
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not initialise your sandbox database.',
            ], 500);
        }

        // ── 5. Execute the validated batch atomically ─────────────────────────
        $messages = [];
        $lastResultSet = null;
        $currentStatement = 1;
        $connection = DB::connection($conn);

        try {
            $connection->beginTransaction();

            foreach ($statements as $index => $stmt) {
                $currentStatement = $index + 1;
                $sql = $stmt['clean'];
                $analysis = ltrim($this->sqlForSecurityChecks($sql));
                $firstWord = strtoupper(preg_split('/\s+/', $analysis)[0] ?? '');
                $isRead = $this->supportedStatementKind($sql) === 'read';

                if ($isRead) {
                    $lastResultSet = $this->selectLimited($conn, $sql);
                    $rowCount = count($lastResultSet['rows']);
                    $suffix = $lastResultSet['truncated'] ? '+' : '';
                    $messages[] = "Statement {$currentStatement}: {$rowCount}{$suffix} row(s) returned.";
                    continue;
                }

                $connection->statement($sql);
                $lastResultSet = null;

                $message = match ($firstWord) {
                    'CREATE' => 'Database object created successfully.',
                    'DROP' => 'Database object dropped successfully.',
                    'ALTER' => 'Table altered successfully.',
                    'INSERT' => 'Row(s) inserted successfully.',
                    'UPDATE' => 'Row(s) updated successfully.',
                    'DELETE' => 'Row(s) deleted successfully.',
                    default => 'Executed successfully.',
                };

                $messages[] = "Statement {$currentStatement}: {$message}";
            }

            // Check the actual post-batch schema while the write lock is held.
            // This handles IF NOT EXISTS correctly and closes the parallel
            // CREATE TABLE race that a pre-execution count cannot prevent.
            if (count($this->fetchUserTables($conn)) > self::MAX_TABLES) {
                $connection->rollBack();

                return response()->json([
                    'status' => 'error',
                    'message' => 'Sandbox limit: a maximum of '.self::MAX_TABLES.' tables is allowed. No changes from this batch were saved.',
                ], 403);
            }

            $connection->commit();
        } catch (PDOException $exception) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            return response()->json([
                'status' => 'error',
                'message' => "SQL Error on statement {$currentStatement}: {$this->publicSqlError($exception)} No changes from this batch were saved.",
            ], 422);
        } catch (\Throwable $exception) {
            if ($connection->transactionLevel() > 0) {
                $connection->rollBack();
            }

            Log::error('Unexpected SQL sandbox execution failure.', [
                'user_id' => Auth::id(),
                'statement_number' => $currentStatement,
                'exception' => $exception::class,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => "The sandbox could not execute statement {$currentStatement}. No changes from this batch were saved.",
            ], 500);
        }

        // ── 6. Build response ─────────────────────────────────────────────────

        // If the final meaningful statement was a SELECT, return its result set
        if ($lastResultSet !== null) {
            return response()->json([
                'status'  => 'success',
                'message' => $lastResultSet['truncated']
                    ? 'Showing the first '.self::MAX_RESULT_ROWS.' rows. Refine your query to see a smaller result set.'
                    : (count($statements) > 1 ? implode("\n", $messages) : null),
                'columns' => $lastResultSet['columns'],
                'rows'    => $lastResultSet['rows'],
                'truncated' => $lastResultSet['truncated'],
            ]);
        }

        // All statements were writes — return the combined message summary
        $summary = count($messages) === 1
            ? $messages[0]                            // single statement: keep it terse
            : count($messages) . " statement(s) executed:\n" . implode("\n", $messages);

        return response()->json([
            'status'  => 'success',
            'message' => $summary,
            'columns' => [],
            'rows'    => [],
        ]);
    }
}
