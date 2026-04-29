<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PDOException;

class SqlSandboxController extends Controller
{
    // ─── Constants ────────────────────────────────────────────────────────────

    private const MAX_TABLES = 5;

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
    ];

    /**
     * Only these statement types are allowed as the very first word.
     * Everything else (VACUUM, REINDEX, etc.) is silently blocked.
     */
    private const ALLOWED_FIRST_WORDS = [
        'SELECT', 'INSERT', 'UPDATE', 'DELETE',
        'CREATE', 'DROP', 'ALTER',
        'BEGIN', 'COMMIT', 'ROLLBACK',
        'PRAGMA', 'EXPLAIN',
        'TRUNCATE',
    ];

    // ─── Internal helpers ─────────────────────────────────────────────────────

    /**
     * Returns the absolute path to this user's private SQLite database.
     * Creates the storage directory AND the empty .sqlite file on first use.
     */
    private function getUserDbPath(): string
    {
        $userId = Auth::id();
        $dir    = storage_path('app/sandbox');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . "/user_{$userId}.sqlite";

        // Touch the file so SQLite doesn't throw "does not exist".
        // touch() is a no-op when the file already exists.
        if (! file_exists($path)) {
            touch($path);
        }

        return $path;
    }

    /**
     * Returns (and lazily registers) a named Laravel DB connection that points
     * to the current user's private SQLite file.
     */
    private function getUserConnection(): string
    {
        $userId   = Auth::id();
        $connName = "sandbox_user_{$userId}";

        if (config("database.connections.{$connName}") === null) {
            config([
                "database.connections.{$connName}" => [
                    'driver'                  => 'sqlite',
                    'database'                => $this->getUserDbPath(),
                    'prefix'                  => '',
                    'foreign_key_constraints' => true,
                ],
            ]);
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

        $cols = DB::connection($conn)->select("PRAGMA table_info(\"{$table}\")");

        return array_map(static fn ($c) => [
            'name' => $c->name,
            'type' => $c->type ?: 'TEXT',
            'pk'   => (bool) $c->pk,
        ], $cols);
    }

    /**
     * Creates (touches) the SQLite sandbox file for a specific user.
     * Call this right after a new user is created in AuthController.
     */
    public static function provisionSandbox(int $userId): void
    {
        $dir = storage_path('app/sandbox');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . "/user_{$userId}.sqlite";

        if (! file_exists($path)) {
            touch($path);
        }
    }

    /**
     * Strips -- line comments and /* block comments from a SQL string.
     * Used for security analysis only — SQLite handles comments natively
     * so we also pass the stripped SQL to the DB to keep execution clean.
     */
    private function stripComments(string $sql): string
    {
        // Remove /* ... */ block comments (non-greedy, dotall)
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

        // Remove -- line comments
        $sql = preg_replace('/--[^\n]*/u', '', $sql);

        return trim($sql);
    }

    /**
     * Splits a multi-statement SQL string into individual statements.
     *
     * Splits on semicolons, strips comments from each chunk, and discards
     * anything that is blank after comment removal (e.g. a trailing comment
     * block or a line that was only a -- remark).
     *
     * Returns an array of ['raw' => ..., 'clean' => ...] pairs so we can
     * run security checks on the clean version and execute the clean version.
     */
    private function splitStatements(string $sql): array
    {
        $chunks = explode(';', $sql);
        $result = [];

        foreach ($chunks as $chunk) {
            $clean = $this->stripComments($chunk);

            // Skip blank chunks (whitespace-only or comment-only segments)
            if ($clean === '') {
                continue;
            }

            $result[] = [
                'raw'   => trim($chunk),   // original (kept for reference)
                'clean' => $clean,         // comment-stripped version for security + execution
            ];
        }

        return $result;
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
            $conn       = $this->getUserConnection();
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

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not load tables: ' . $e->getMessage(),
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

            DB::connection($conn)->statement("DROP TABLE IF EXISTS \"{$table}\"");

            return response()->json(['status' => 'success', 'message' => "Table '{$table}' dropped."]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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
        $createTableCount = 0;

        foreach ($statements as $index => $stmt) {
            $sql       = $stmt['clean'];
            $stmtLabel = 'Statement ' . ($index + 1);

            // 3a. Single-keyword blocklist
            foreach (self::FORBIDDEN_KEYWORDS as $keyword) {
                if (stripos($sql, $keyword) !== false) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => "[Security] {$stmtLabel}: The keyword '{$keyword}' is not allowed in the SQL Sandbox.",
                    ], 403);
                }
            }

            // 3b. Compound-pattern blocklist
            foreach (self::FORBIDDEN_PATTERNS as $pattern) {
                if (preg_match($pattern, $sql)) {
                    preg_match($pattern, $sql, $m);
                    $matched = strtoupper($m[0] ?? 'that command');
                    return response()->json([
                        'status'  => 'error',
                        'message' => "[Security] {$stmtLabel}: '{$matched}' is not allowed in the SQL Sandbox. "
                                   . "Stick to CREATE TABLE, SELECT, INSERT, UPDATE, DELETE, ALTER TABLE, and DROP TABLE.",
                    ], 403);
                }
            }

            // 3c. First-word allowlist
            $firstWord = strtoupper(preg_split('/\s+/', ltrim($sql))[0] ?? '');

            if (! in_array($firstWord, self::ALLOWED_FIRST_WORDS, true)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "[Security] {$stmtLabel}: '{$firstWord}' statements are not supported in the SQL Sandbox. "
                               . "Allowed: SELECT, INSERT, UPDATE, DELETE, CREATE TABLE, ALTER TABLE, DROP TABLE.",
                ], 403);
            }

            // 3d. Count CREATE TABLE statements to check the limit later
            if (preg_match('/^\s*CREATE\s+TABLE\b/i', $sql)) {
                $createTableCount++;
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

        // ── 5. CREATE TABLE limit check (existing + new must not exceed MAX) ──
        if ($createTableCount > 0) {
            $existing = count($this->fetchUserTables($conn));
            if ($existing + $createTableCount > self::MAX_TABLES) {
                $allowed = self::MAX_TABLES - $existing;
                return response()->json([
                    'status'  => 'error',
                    'message' => "Sandbox limit: you can only create {$allowed} more table(s) "
                               . "(max " . self::MAX_TABLES . "). Drop a table first.",
                ], 403);
            }
        }

        // ── 6. Execute statements in order ────────────────────────────────────
        $messages       = [];   // success messages collected per statement
        $lastResultSet  = null; // result set of the final SELECT (if any)
        $lastFirstWord  = '';

        try {
            foreach ($statements as $index => $stmt) {
                $sql       = $stmt['clean'];
                $firstWord = strtoupper(preg_split('/\s+/', ltrim($sql))[0] ?? '');
                $isRead    = in_array($firstWord, ['SELECT', 'PRAGMA', 'EXPLAIN'], true);

                if ($isRead) {
                    $results = DB::connection($conn)->select($sql);

                    // Store result — the last SELECT's data is what we return
                    $lastResultSet = $results;
                    $lastFirstWord = $firstWord;

                    $rowCount    = count($results);
                    $messages[]  = "Statement " . ($index + 1) . ": {$rowCount} row(s) returned.";

                } else {
                    DB::connection($conn)->statement($sql);

                    $lastFirstWord = $firstWord;

                    $msg = match ($firstWord) {
                        'CREATE'   => 'Table created successfully.',
                        'DROP'     => 'Table dropped successfully.',
                        'ALTER'    => 'Table altered successfully.',
                        'TRUNCATE' => 'Table truncated successfully.',
                        'INSERT'   => 'Row(s) inserted successfully.',
                        'UPDATE'   => 'Row(s) updated successfully.',
                        'DELETE'   => 'Row(s) deleted successfully.',
                        default    => 'Executed successfully.',
                    };

                    $messages[] = "Statement " . ($index + 1) . ": {$msg}";
                }
            }

        } catch (PDOException $e) {
            $ran = count($messages);
            return response()->json([
                'status'  => 'error',
                'message' => 'SQL Error on statement ' . ($ran + 1) . ': ' . $e->getMessage()
                           . ($ran > 0 ? " ({$ran} statement(s) before this point executed successfully)" : ''),
            ], 422);

        } catch (\Exception $e) {
            $ran = count($messages);
            return response()->json([
                'status'  => 'error',
                'message' => 'Unexpected Error on statement ' . ($ran + 1) . ': ' . $e->getMessage(),
            ], 500);
        }

        // ── 7. Build response ─────────────────────────────────────────────────

        // If the final meaningful statement was a SELECT, return its result set
        if ($lastResultSet !== null) {
            if (empty($lastResultSet)) {
                return response()->json([
                    'status'  => 'success',
                    'message' => count($statements) > 1
                        ? implode("\n", $messages)
                        : 'Query returned 0 rows.',
                    'columns' => [],
                    'rows'    => [],
                ]);
            }

            $firstRow = (array) $lastResultSet[0];
            $columns  = array_keys($firstRow);
            $rows     = array_map(static fn ($r) => array_values((array) $r), $lastResultSet);

            return response()->json([
                'status'  => 'success',
                'columns' => $columns,
                'rows'    => $rows,
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