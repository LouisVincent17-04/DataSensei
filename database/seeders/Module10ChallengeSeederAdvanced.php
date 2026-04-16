<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module10ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 10 — Database Management for Data Science (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Database Management for Data Science',
            'description'           => 'Tackle advanced database challenges: debug broken SQL queries, analyse EXPLAIN plans, optimize indexes, design complex schemas, work with advanced window functions, and build robust Python-SQL pipelines with error handling.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 10,
        ]);

        $this->command->info("Seeding 50 advanced-level questions...");

        $qaData = [

            // ── DEBUGGING SQL ─────────────────────────────────────────────
            [
                'q' => "This query is supposed to return the top 3 customers by revenue but gives wrong results. What is the bug?\n\nSELECT customer_id, SUM(amount) AS revenue\nFROM orders\nGROUP BY customer_id\nORDER BY revenue\nLIMIT 3;",
                'opts' => [
                    ['SUM should be COUNT', false],
                    ['ORDER BY defaults to ASC — it returns the 3 LOWEST revenue customers, not the highest. Fix: ORDER BY revenue DESC', true],
                    ['LIMIT must come before ORDER BY', false],
                    ['GROUP BY and ORDER BY cannot be used together', false],
                ],
            ],
            [
                'q' => "What is wrong with this query?\n\nSELECT dept, name, MAX(salary)\nFROM employees\nGROUP BY dept;",
                'opts' => [
                    ['MAX() cannot be used with GROUP BY', false],
                    ['name is not in GROUP BY and not in an aggregate function — this violates SQL grouping rules and will error in strict databases', true],
                    ['dept should be in the WHERE clause', false],
                    ['Nothing is wrong — it returns the highest-paid employee per department', false],
                ],
            ],
            [
                'q' => "This LEFT JOIN query returns fewer rows than expected. What is likely wrong?\n\nSELECT c.name, o.total\nFROM customers c\nLEFT JOIN orders o ON c.id = o.customer_id\nWHERE o.total > 100;",
                'opts' => [
                    ['LEFT JOIN is the wrong join type', false],
                    ['The WHERE o.total > 100 filter turns the LEFT JOIN into an INNER JOIN — customers with no orders (NULL total) are excluded. Fix: move the condition to ON or use WHERE o.total > 100 OR o.total IS NULL', true],
                    ['The ON clause is incorrect', false],
                    ['customers and orders cannot be joined this way', false],
                ],
            ],
            [
                'q' => "What does this query actually return, and why is it a common mistake?\n\nSELECT * FROM orders\nWHERE customer_id != NULL;",
                'opts' => [
                    ['All rows where customer_id is not NULL', false],
                    ['No rows — comparisons with NULL using != (or =) always evaluate to UNKNOWN, not TRUE. The correct syntax is: WHERE customer_id IS NOT NULL', true],
                    ['All rows in the table', false],
                    ['A syntax error', false],
                ],
            ],
            [
                'q' => "What is the bug in this window function?\n\nSELECT name, salary,\n  RANK() OVER (PARTITION BY dept) AS rnk\nFROM employees;",
                'opts' => [
                    ['RANK() cannot be used with PARTITION BY', false],
                    ['RANK() requires an ORDER BY clause inside OVER() to define the ranking order — without it, the result is undefined', true],
                    ['PARTITION BY dept is incorrect syntax', false],
                    ['There is no bug', false],
                ],
            ],

            // ── EXPLAIN PLANS & PERFORMANCE ───────────────────────────────
            [
                'q' => "An EXPLAIN ANALYZE output shows: Seq Scan on orders (cost=0..8543 rows=250000) (actual time=0.1..312ms)\n\nAn index exists on orders(customer_id). Why might the planner still choose a Seq Scan?",
                'opts' => [
                    ['PostgreSQL does not support indexes on foreign keys', false],
                    ['The query lacks a WHERE clause on customer_id, or the estimated rows returned are so many that a full scan is cheaper than random index lookups', true],
                    ['The index is corrupted', false],
                    ['Seq Scan is always used for tables over 1000 rows', false],
                ],
            ],
            [
                'q' => "EXPLAIN shows a Hash Join. When does a Hash Join outperform a Nested Loop Join?",
                'opts' => [
                    ['Always — Hash Join is always faster', false],
                    ['When joining large tables where one fits in memory as a hash table — avoids O(n×m) row-by-row comparison', true],
                    ['When joining tables on a non-indexed column with very few rows', false],
                    ['Hash Join only works on string columns', false],
                ],
            ],
            [
                'q' => "After adding an index, EXPLAIN still shows a Seq Scan. Which action is most likely to fix this?",
                'opts' => [
                    ['Restart the database server', false],
                    ['Run ANALYZE on the table so the query planner has up-to-date statistics about column cardinality', true],
                    ['Drop and recreate the table', false],
                    ['Increase the LIMIT clause value', false],
                ],
            ],
            [
                'q' => "What does this EXPLAIN ANALYZE node indicate?\n\n'Bitmap Heap Scan on users'\n'  Recheck Cond: (age > 30)'\n'  -> Bitmap Index Scan on idx_users_age'",
                'opts' => [
                    ['The index was not used', false],
                    ['PostgreSQL first built a bitmap of matching row locations using the index, then fetched those rows from the heap — a two-phase index scan used when many rows match', true],
                    ['The query has a syntax error', false],
                    ['The table was scanned sequentially twice', false],
                ],
            ],

            // ── ADVANCED WINDOW FUNCTIONS ─────────────────────────────────
            [
                'q' => "What does this query detect in a time-series dataset?\n\nSELECT *,\n  revenue - LAG(revenue) OVER (PARTITION BY product_id ORDER BY sale_date) AS day_over_day_change\nFROM daily_sales;",
                'opts' => [
                    ['The cumulative revenue for each product', false],
                    ['The daily change in revenue compared to the previous day, per product', true],
                    ['The maximum revenue for each product per day', false],
                    ['The rank of each day\'s revenue', false],
                ],
            ],
            [
                'q' => "What does this session gap detection query identify?\n\nSELECT user_id, event_time,\n  SUM(is_new_session) OVER (PARTITION BY user_id ORDER BY event_time) AS session_id\nFROM (\n  SELECT *,\n    CASE WHEN event_time - LAG(event_time) OVER\n      (PARTITION BY user_id ORDER BY event_time) > INTERVAL '30 minutes'\n    THEN 1 ELSE 0 END AS is_new_session\n  FROM events\n) t;",
                'opts' => [
                    ['The total number of events per user', false],
                    ['A session ID for each event, incrementing whenever a 30-minute gap is detected between events for the same user', true],
                    ['All events that occurred exactly 30 minutes apart', false],
                    ['The last event in each user\'s session', false],
                ],
            ],
            [
                'q' => "What is the purpose of PERCENT_RANK() OVER (ORDER BY salary)?\n\nFor the highest salary in a 10-person table, what does PERCENT_RANK() return?",
                'opts' => [
                    ['100', false],
                    ['1.0 — the relative rank as a fraction from 0 to 1, where 1 is the top', true],
                    ['10', false],
                    ['0 — highest rank always returns 0', false],
                ],
            ],

            // ── ADVANCED INDEXING ─────────────────────────────────────────
            [
                'q' => "What is a GIN index in PostgreSQL and when would you use it?",
                'opts' => [
                    ['A standard B-tree index with compression', false],
                    ['A Generalized Inverted Index — used for indexing composite values like arrays, JSONB fields, or full-text search tsvectors', true],
                    ['An index specifically for foreign key columns', false],
                    ['A clustered index that reorders table rows', false],
                ],
            ],
            [
                'q' => "You need to search a products table for rows where the JSONB column `attributes` contains a specific key-value pair. Which index type should you create?",
                'opts' => [
                    ['B-tree index on the attributes column', false],
                    ['GIN index on the attributes column', true],
                    ['Partial index filtering on attributes', false],
                    ['No index — JSONB cannot be indexed', false],
                ],
            ],
            [
                'q' => "Index-Only Scans in PostgreSQL are possible when:\n\n(What condition must be met?)",
                'opts' => [
                    ['The table has fewer than 1000 rows', false],
                    ['All columns needed by the query are included in the index (covering index) and the visibility map shows pages are all-visible', true],
                    ['The table has no NULLs', false],
                    ['The query uses ORDER BY on the indexed column', false],
                ],
            ],
            [
                'q' => "What is index selectivity, and why does it matter for query performance?",
                'opts' => [
                    ['The number of indexes on a table — more is always better', false],
                    ['The fraction of distinct values in a column — high selectivity (many distinct values) makes an index much more useful for filtering', true],
                    ['Whether the index is clustered or non-clustered', false],
                    ['The physical size of the index file on disk', false],
                ],
            ],

            // ── ADVANCED PYTHON + SQL ─────────────────────────────────────
            [
                'q' => "What does this SQLAlchemy code do, and what problem does it solve?\n\nfrom sqlalchemy.orm import Session\nwith Session(engine) as session:\n    try:\n        session.execute(insert_stmt_1)\n        session.execute(insert_stmt_2)\n        session.commit()\n    except Exception:\n        session.rollback()\n        raise",
                'opts' => [
                    ['It runs both inserts and ignores all errors', false],
                    ['It wraps both inserts in a transaction — if either fails, both are rolled back, ensuring atomicity', true],
                    ['It creates two separate connections for each insert', false],
                    ['It retries the inserts up to 3 times on failure', false],
                ],
            ],
            [
                'q' => "What is the purpose of connection pooling in SQLAlchemy?\n\nengine = create_engine(url, pool_size=10, max_overflow=5)",
                'opts' => [
                    ['To limit the number of tables a connection can query', false],
                    ['To maintain a pool of reusable database connections, avoiding the overhead of opening and closing a new connection for every query', true],
                    ['To replicate data to multiple databases simultaneously', false],
                    ['To encrypt connections to the database', false],
                ],
            ],
            [
                'q' => "What does this pandas pipeline accomplish?\n\ndf = pd.read_sql(\n  'SELECT user_id, action, ts FROM events',\n  engine,\n  parse_dates=['ts']\n)\ndaily = (\n  df.set_index('ts')\n    .groupby('user_id')\n    .resample('D')['action']\n    .count()\n    .reset_index()\n)\ndaily.to_sql('daily_action_counts', engine, if_exists='replace', index=False)",
                'opts' => [
                    ['Deletes all events older than one day', false],
                    ['Reads raw event data, resamples it to daily action counts per user, and writes the result back to a summary table', true],
                    ['Reads events and exports them to a CSV file', false],
                    ['Creates a new events table with daily timestamps', false],
                ],
            ],
            [
                'q' => "Why should you use pd.read_sql with chunksize when querying very large tables?",
                'opts' => [
                    ['To make the query run faster on the database side', false],
                    ['To avoid loading the entire result set into RAM at once — process data in manageable batches', true],
                    ['Because pandas cannot handle more than 1 million rows', false],
                    ['To automatically create database indexes', false],
                ],
            ],

            // ── ADVANCED NoSQL ────────────────────────────────────────────
            [
                'q' => "What is sharding in a distributed database, and what problem does it solve?",
                'opts' => [
                    ['Compressing data to reduce disk usage', false],
                    ['Horizontally partitioning data across multiple nodes so that each node holds a subset of the data — scales write throughput and storage beyond a single machine', true],
                    ['Replicating data to multiple nodes for read performance', false],
                    ['Splitting a table into normalized sub-tables', false],
                ],
            ],
            [
                'q' => "In MongoDB, what is the difference between find() and aggregate()?\n\n(A) db.orders.find({ status: 'completed' })\n(B) db.orders.aggregate([ { \$match: { status: 'completed' } }, { \$group: { _id: '\$customer_id', total: { \$sum: '\$amount' } } } ])",
                'opts' => [
                    ['find() is for reads; aggregate() is for writes', false],
                    ['find() retrieves documents matching a simple filter; aggregate() executes a multi-stage pipeline for complex transformations, grouping, and computation', true],
                    ['They are interchangeable', false],
                    ['aggregate() is slower and should be avoided', false],
                ],
            ],
            [
                'q' => "What is the write concern in MongoDB, and why does it matter for data integrity?",
                'opts' => [
                    ['The maximum number of documents you can write per second', false],
                    ['A setting that controls how many replica set members must acknowledge a write before it is considered successful — higher write concern = stronger durability guarantee', true],
                    ['The schema validation rule applied on insert', false],
                    ['The index used to speed up write operations', false],
                ],
            ],
            [
                'q' => "What is the BSON format used by MongoDB?",
                'opts' => [
                    ['A compressed version of CSV', false],
                    ['Binary-encoded JSON — extends JSON with additional types like Date, ObjectId, and binary data', true],
                    ['A proprietary schema format similar to XML', false],
                    ['A columnar storage format', false],
                ],
            ],

            // ── ADVANCED SCHEMA & DESIGN ──────────────────────────────────
            [
                'q' => "What is Slowly Changing Dimension (SCD) Type 2 in data warehousing?",
                'opts' => [
                    ['Overwriting the old value with the new value (no history kept)', false],
                    ['Creating a new row for each change with effective_from and effective_to dates, preserving full history', true],
                    ['Adding a new column for each version of the attribute', false],
                    ['Deleting outdated dimension rows', false],
                ],
            ],
            [
                'q' => "What is a fact table in a dimensional data warehouse model?",
                'opts' => [
                    ['A table containing descriptive attributes about business entities (customers, products)', false],
                    ['A table storing quantitative measurements and metrics (sales amount, quantity) along with foreign keys to dimension tables', true],
                    ['A lookup table for valid values', false],
                    ['A backup of the operational database', false],
                ],
            ],
            [
                'q' => "What is a database partition and what are its two main types?",
                'opts' => [
                    ['A database backup strategy with full and incremental types', false],
                    ['Dividing a large table into smaller physical segments — horizontal partitioning (by rows, e.g., by date range) and vertical partitioning (by columns)', true],
                    ['Splitting a single SQL query into two separate queries', false],
                    ['A method to replicate data across two servers', false],
                ],
            ],

            // ── CONCURRENCY & LOCKING ─────────────────────────────────────
            [
                'q' => "What is a deadlock in a database, and how does it typically occur?",
                'opts' => [
                    ['A query that runs for more than 60 seconds', false],
                    ['Two or more transactions each hold a lock and wait for the other to release it — creating a circular dependency that neither can break', true],
                    ['A situation where too many indexes slow down writes', false],
                    ['A failed connection to the database server', false],
                ],
            ],
            [
                'q' => "Which PostgreSQL isolation level prevents phantom reads?",
                'opts' => [
                    ['READ COMMITTED', false],
                    ['REPEATABLE READ', false],
                    ['SERIALIZABLE', true],
                    ['READ UNCOMMITTED', false],
                ],
            ],
            [
                'q' => "What is optimistic locking and when is it preferred over pessimistic locking?",
                'opts' => [
                    ['Optimistic locking locks rows immediately on read; preferred for high-contention scenarios', false],
                    ['Optimistic locking assumes conflicts are rare and validates at commit time (using a version column) — preferred for low-contention, read-heavy workloads to maximize concurrency', true],
                    ['Optimistic locking never rolls back transactions', false],
                    ['Optimistic locking is only available in NoSQL databases', false],
                ],
            ],

            // ── ADVANCED AGGREGATIONS ─────────────────────────────────────
            [
                'q' => "What does the FILTER clause do in PostgreSQL aggregation?\n\nSELECT\n  COUNT(*) FILTER (WHERE status = 'completed') AS completed,\n  COUNT(*) FILTER (WHERE status = 'failed') AS failed\nFROM jobs;",
                'opts' => [
                    ['It is equivalent to two separate SELECT queries with WHERE clauses', false],
                    ['It applies a conditional filter to an aggregate function — counting only rows matching the condition, all in a single pass over the table', true],
                    ['It removes rows from the table permanently', false],
                    ['It is the same as HAVING', false],
                ],
            ],
            [
                'q' => "What does GROUPING SETS ((dept), (location), ()) compute?",
                'opts' => [
                    ['Only a grand total', false],
                    ['Aggregates for each dept, each location, and a grand total — all in a single query pass', true],
                    ['A three-way join between three tables', false],
                    ['Groups by all combinations of dept and location', false],
                ],
            ],
            [
                'q' => "What is the difference between ROLLUP and CUBE in SQL?",
                'opts' => [
                    ['They are the same', false],
                    ['ROLLUP generates a hierarchy of subtotals (useful for year/month/day roll-ups); CUBE generates ALL possible combinations of grouping sets', true],
                    ['CUBE is a 3D version of ROLLUP for three dimensions', false],
                    ['ROLLUP removes duplicates; CUBE keeps them', false],
                ],
            ],

            // ── FULL-TEXT SEARCH ──────────────────────────────────────────
            [
                'q' => "In PostgreSQL, what does the @@ operator do?\n\nSELECT * FROM articles\nWHERE to_tsvector('english', body) @@ to_tsquery('data & science');",
                'opts' => [
                    ['Performs a LIKE wildcard search', false],
                    ['Tests whether a tsvector (full-text document) matches a tsquery (search query) — returns TRUE if the document contains both "data" and "science"', true],
                    ['Computes the string distance between two texts', false],
                    ['Concatenates two strings', false],
                ],
            ],
            [
                'q' => "What is ts_rank() used for in PostgreSQL full-text search?",
                'opts' => [
                    ['Counting the number of matching documents', false],
                    ['Scoring how well a document matches a query — useful for ordering search results by relevance', true],
                    ['Tokenizing a text string into words', false],
                    ['Creating a full-text index on a column', false],
                ],
            ],

            // ── ADVANCED MISC ─────────────────────────────────────────────
            [
                'q' => "What is logical replication in PostgreSQL and how does it differ from physical replication?",
                'opts' => [
                    ['They are identical mechanisms', false],
                    ['Logical replication streams changes at the row/table level (SQL DML operations) and can replicate to different PostgreSQL versions or selective tables; physical replication copies the entire disk block stream', true],
                    ['Logical replication is only used for backups', false],
                    ['Physical replication is slower and deprecated', false],
                ],
            ],
            [
                'q' => "What is the purpose of pg_stat_statements in PostgreSQL?",
                'opts' => [
                    ['To store all historical data changes', false],
                    ['To track execution statistics (call count, total time, mean time) for all SQL queries — invaluable for identifying slow queries in production', true],
                    ['To list all active database connections', false],
                    ['To store the results of EXPLAIN ANALYZE permanently', false],
                ],
            ],
            [
                'q' => "What does VACUUM FULL do in PostgreSQL, and what is its major downside?",
                'opts' => [
                    ['It is the same as regular VACUUM but faster', false],
                    ['It fully rewrites the table to reclaim all dead space but acquires an exclusive lock — blocking all reads and writes on the table during the operation', true],
                    ['It removes all NULL values from the table', false],
                    ['It rebuilds all indexes without locking the table', false],
                ],
            ],
            [
                'q' => "What is a lateral join (LATERAL) in SQL and when is it useful?\n\nSELECT c.name, top_order.amount\nFROM customers c\nJOIN LATERAL (\n  SELECT amount FROM orders\n  WHERE customer_id = c.id\n  ORDER BY amount DESC LIMIT 1\n) AS top_order ON TRUE;",
                'opts' => [
                    ['A join that uses inequality conditions', false],
                    ['A join where the right-side subquery can reference columns from the left-side table — useful for per-row subqueries like "top N per group"', true],
                    ['A join that always returns all rows from both tables', false],
                    ['A join that only works with window functions', false],
                ],
            ],
            [
                'q' => "What is the difference between a stored procedure and a function in PostgreSQL?",
                'opts' => [
                    ['They are identical', false],
                    ['A stored procedure can manage transactions (COMMIT/ROLLBACK inside it) and is called with CALL; a function returns a value, is used inside SQL expressions, and cannot manage transactions', true],
                    ['Functions are faster; stored procedures are for DDL only', false],
                    ['Stored procedures can only be written in PL/pgSQL; functions can use any language', false],
                ],
            ],
            [
                'q' => "What is a foreign data wrapper (FDW) in PostgreSQL?",
                'opts' => [
                    ['A mechanism to enforce foreign key constraints', false],
                    ['An extension that allows PostgreSQL to query external data sources (other databases, CSV files, APIs) as if they were local tables', true],
                    ['A type of index on foreign key columns', false],
                    ['A tool to migrate data from MySQL to PostgreSQL', false],
                ],
            ],
            [
                'q' => "In the context of Python data pipelines, what does SQLAlchemy's Core differ from its ORM?\n\n(A) Core: conn.execute(text('SELECT * FROM users WHERE id=:id'), {'id': 1})\n(B) ORM: session.query(User).filter(User.id == 1).first()",
                'opts' => [
                    ['There is no difference', false],
                    ['Core provides SQL expression language close to raw SQL (faster, less overhead); ORM maps Python objects to tables (more abstraction, easier to write complex models)', true],
                    ['Core is deprecated in modern SQLAlchemy', false],
                    ['ORM is only for INSERT/UPDATE; Core is only for SELECT', false],
                ],
            ],
        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'          => $challenge->id,
                'question_text'         => $data['q'],
                'challenge_category_id' => $category->id,
            ]);

            foreach ($data['opts'] as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 50 questions seeded for Module 10 — Database Management for Data Science (Advanced).");
    }
}