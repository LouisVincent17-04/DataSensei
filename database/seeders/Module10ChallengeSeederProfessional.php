<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module10ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 10 — Database Management for Data Science (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Database Management for Data Science',
            'description'           => 'Real-world professional-grade database challenges — production pipeline design, large-scale query optimization, schema evolution strategies, distributed system trade-offs, MLOps data infrastructure, edge cases in SQL semantics, and performance engineering at scale.',
            'time_limit_seconds'    => 3600,
            'base_xp'               => 3000,
            'order_index'           => 10,
        ]);

        $this->command->info("Seeding 50 professional-level questions...");

        $qaData = [

            // ── PRODUCTION PIPELINE DESIGN ────────────────────────────────
            [
                'q' => "A data science team ingests 50 million new rows per day into a PostgreSQL fact table. After 6 months the table has 9 billion rows and queries are degrading. What is the FIRST architectural change to implement?",
                'opts' => [
                    ['Add more B-tree indexes to all filter columns', false],
                    ['Implement declarative range partitioning on the date column — queries that filter by date will only scan the relevant partition(s)', true],
                    ['Migrate the entire table to a single CSV file', false],
                    ['Run VACUUM FULL every night', false],
                ],
            ],
            [
                'q' => "You are designing a real-time feature store for ML models that requires:\n- Sub-millisecond point lookups by user_id\n- Batch writes of 100k rows every 5 minutes\n- Occasional full-table scans for retraining\n\nWhich storage architecture best fits ALL three requirements?",
                'opts' => [
                    ['A single PostgreSQL table with no indexes', false],
                    ['A dual-store pattern: Redis for low-latency point lookups + a columnar store (e.g., BigQuery/Redshift) for batch analytics, with a pipeline syncing writes to both', true],
                    ['A single MongoDB collection', false],
                    ['A local SQLite database with WAL mode enabled', false],
                ],
            ],
            [
                'q' => "A production ETL job fails silently — it inserts 0 rows but reports success. The root cause is uncaught exception swallowing. What is the correct production fix in SQLAlchemy?",
                'opts' => [
                    ['Use try/except and print the error', false],
                    ['Wrap inserts in explicit transactions with rollback on exception, assert rowcount > 0 after each batch, and emit structured logs/alerts on anomalies', true],
                    ['Switch from SQLAlchemy to raw psycopg2', false],
                    ['Use if_exists=\'replace\' to reset the table on every run', false],
                ],
            ],
            [
                'q' => "Your batch pipeline reads 200M rows via pd.read_sql and crashes with MemoryError. After switching to chunksize=100000, it still takes 4 hours. What is the next optimization?",
                'opts' => [
                    ['Increase RAM on the server until it no longer crashes', false],
                    ['Push computation to the database — rewrite transformations as SQL (aggregations, filters, window functions) so only the small result set is transferred to Python', true],
                    ['Convert the DataFrame to a numpy array before processing', false],
                    ['Use multiprocessing to read all chunks in parallel without changing the query', false],
                ],
            ],

            // ── QUERY OPTIMIZATION AT SCALE ───────────────────────────────
            [
                'q' => "A query joining three 500M-row tables takes 45 minutes. EXPLAIN shows three Seq Scans and a Hash Join that spills to disk. What is the most impactful fix?",
                'opts' => [
                    ['Increase work_mem so hash joins fit in RAM, add selective indexes on JOIN and WHERE columns, and consider pre-aggregating large tables via materialized views', true],
                    ['Rewrite the query as three separate queries and join in Python pandas', false],
                    ['Add a B-tree index to every column', false],
                    ['Increase max_connections in postgresql.conf', false],
                ],
            ],
            [
                'q' => "You notice that a frequently run analytical query is not using an existing index. EXPLAIN shows the planner\'s row estimate is 100x off from the actual row count. What should you investigate?",
                'opts' => [
                    ['The index is corrupted and must be rebuilt', false],
                    ['Table statistics are stale — run ANALYZE, and consider lowering default_statistics_target for high-cardinality columns to improve planner estimates', true],
                    ['The query uses a subquery which disables index usage', false],
                    ['The table has too many NULL values', false],
                ],
            ],
            [
                'q' => "What is the N+1 query problem in ORM-based data pipelines, and why is it critical to fix in production?\n\n# Example (bad):\nfor customer in session.query(Customer).all():\n    orders = session.query(Order).filter_by(customer_id=customer.id).all()",
                'opts' => [
                    ['Running N+1 queries where 1 query fetches N parent records and then N separate queries fetch child records — scales as O(N) database round trips instead of O(1)', true],
                    ['A query that returns N+1 more rows than expected', false],
                    ['Using N+1 indexes on the same table', false],
                    ['A transaction that commits N+1 times', false],
                ],
            ],
            [
                'q' => "What is query plan caching and when does it cause problems in PostgreSQL (known as 'plan cache poisoning')?",
                'opts' => [
                    ['The database never caches query plans', false],
                    ['PostgreSQL caches the execution plan for prepared statements; if data distribution changes significantly, the cached plan may become suboptimal — fixed by DEALLOCATE or adjusting plan_cache_mode', true],
                    ['Plan caching only applies to views', false],
                    ['Query plans are cached permanently until the database restarts', false],
                ],
            ],

            // ── SCHEMA EVOLUTION & MIGRATIONS ─────────────────────────────
            [
                'q' => "You need to add a NOT NULL column with no default to a 2-billion-row production table with zero downtime. What is the correct strategy?",
                'opts' => [
                    ['Run ALTER TABLE ADD COLUMN col NOT NULL during off-peak hours', false],
                    ['Add the column as nullable first, backfill data in batches, then add the NOT NULL constraint using ADD CONSTRAINT ... NOT VALID to avoid a full table scan lock', true],
                    ['Drop and recreate the table with the new column', false],
                    ['Use a VIEW to simulate the new column without altering the table', false],
                ],
            ],
            [
                'q' => "What is the risk of a long-running DDL statement like ALTER TABLE ADD COLUMN on a busy PostgreSQL production table?",
                'opts' => [
                    ['It has no risk — DDL is always instant in PostgreSQL', false],
                    ['DDL acquires an ACCESS EXCLUSIVE lock that blocks ALL reads and writes on the table — even a fast DDL waits behind slow queries, and slow DDL can halt production traffic', true],
                    ['It only blocks INSERT operations', false],
                    ['It only runs during autovacuum', false],
                ],
            ],
            [
                'q' => "You are renaming a column that is used by 20 production services. What is the safest zero-downtime migration strategy?",
                'opts' => [
                    ['Rename the column and deploy all 20 services simultaneously', false],
                    ['Add the new column, write to both columns in the application, backfill, deploy services to read the new column, then drop the old column in a later migration', true],
                    ['Create a VIEW with the new column name pointing to the old one', false],
                    ['Use a database trigger to alias the old column to the new name', false],
                ],
            ],

            // ── DISTRIBUTED DATABASES & TRADE-OFFS ───────────────────────
            [
                'q' => "In the context of distributed databases and the CAP theorem, if you choose Consistency + Partition Tolerance (CP) over Availability, what does your system do during a network partition?",
                'opts' => [
                    ['It continues to serve reads from stale replicas', false],
                    ['It refuses to serve requests (becomes unavailable) until the partition is resolved, ensuring all nodes remain consistent', true],
                    ['It automatically resolves conflicts using last-write-wins', false],
                    ['It buffers all writes until the partition heals', false],
                ],
            ],
            [
                'q' => "What is the PACELC extension to the CAP theorem and what additional trade-off does it capture?",
                'opts' => [
                    ['It adds the Elasticity and Latency properties to CAP', false],
                    ['Even when there is NO partition (else case), distributed systems must trade off between Latency and Consistency — PACELC captures both partition-time and normal-operation trade-offs', true],
                    ['It replaces CAP for modern cloud databases', false],
                    ['It adds Persistence and Cost to the CAP framework', false],
                ],
            ],
            [
                'q' => "Apache Cassandra is a wide-column distributed database. Which consistency model does it use by default, and how can you adjust it per-query?",
                'opts' => [
                    ['Strong consistency with no ability to adjust', false],
                    ['Tunable consistency — you specify a consistency level per query (ONE, QUORUM, ALL), trading latency for consistency guarantees', true],
                    ['Always eventual consistency with no tuning options', false],
                    ['ACID transactions identical to PostgreSQL', false],
                ],
            ],
            [
                'q' => "What is the difference between read replicas and multi-master replication, and when would you use each?",
                'opts' => [
                    ['They are the same architecture', false],
                    ['Read replicas route reads to secondary nodes (one write master, multiple read replicas — good for read-heavy workloads); multi-master allows writes to multiple nodes simultaneously (higher write throughput but conflict resolution complexity)', true],
                    ['Multi-master is only available in NoSQL databases', false],
                    ['Read replicas are always synchronous; multi-master is always asynchronous', false],
                ],
            ],

            // ── MLOps DATA INFRASTRUCTURE ─────────────────────────────────
            [
                'q' => "A feature pipeline stores ML features in a PostgreSQL table. The ML model was trained on features computed with a timezone bug that has since been fixed. What database practice prevents this silent data quality issue from affecting future models?",
                'opts' => [
                    ['Delete all historical data and start over', false],
                    ['Version the feature computation logic with a pipeline_version column, store point-in-time snapshots for reproducibility, and alert on schema/logic changes', true],
                    ['Add a NOT NULL constraint to the timezone column', false],
                    ['Use a NoSQL database instead', false],
                ],
            ],
            [
                'q' => "What is data lineage and why is it critical in production ML data pipelines?",
                'opts' => [
                    ['A database constraint that prevents NULL values', false],
                    ['Tracking the origin, transformations, and flow of data from source to model — enables debugging model degradation, auditing compliance, and reproducing past predictions', true],
                    ['The order in which tables were created in the database', false],
                    ['The foreign key chain between tables', false],
                ],
            ],
            [
                'q' => "You are designing a training data store for a model that must be retrained weekly. The dataset is 500GB of Parquet files. Which storage strategy is most appropriate?",
                'opts' => [
                    ['Load all 500GB into a single PostgreSQL table for every training run', false],
                    ['Store raw/processed Parquet files in object storage (S3/GCS) versioned by date, use a metadata catalog (e.g., Delta Lake or Apache Iceberg) for time-travel queries and incremental loads', true],
                    ['Convert to CSV and store in a shared network folder', false],
                    ['Use a single Redis instance for in-memory storage', false],
                ],
            ],

            // ── ADVANCED EDGE CASES IN SQL ────────────────────────────────
            [
                'q' => "What does this query return, and why is it a common source of data bugs?\n\nSELECT COUNT(*) FROM orders WHERE discount != 0;",
                'opts' => [
                    ['All orders with no discount', false],
                    ['All orders where discount is non-zero AND non-NULL — rows where discount IS NULL are silently excluded because NULL != 0 evaluates to UNKNOWN, not TRUE', true],
                    ['All rows in the table', false],
                    ['An error — you cannot compare a column to 0', false],
                ],
            ],
            [
                'q' => "What does this query return?\n\nSELECT 1 WHERE NULL = NULL;",
                'opts' => [
                    ['1 (TRUE)', false],
                    ['0 (FALSE)', false],
                    ['No rows — NULL = NULL evaluates to UNKNOWN, not TRUE; use IS NOT DISTINCT FROM for NULL-safe equality', true],
                    ['An error', false],
                ],
            ],
            [
                'q' => "What is the output of this aggregation edge case?\n\nSELECT AVG(score) FROM students WHERE class = 'X';\n\n(class 'X' has no students)",
                'opts' => [
                    ['0', false],
                    ['NULL — aggregate functions return NULL when applied to an empty set, except COUNT(*) which returns 0', true],
                    ['An error', false],
                    ['Infinity', false],
                ],
            ],
            [
                'q' => "What is integer overflow risk in SQL aggregations, and how do you mitigate it?\n\nSELECT SUM(quantity) FROM order_items; -- quantity is INT",
                'opts' => [
                    ['INT columns cannot overflow in SQL', false],
                    ['SUM of billions of INT values can exceed the 32-bit integer max (2,147,483,647) — use SUM(quantity::BIGINT) or define the column as BIGINT from the start', true],
                    ['SUM always returns a FLOAT to avoid overflow', false],
                    ['Overflow only occurs with SMALLINT columns', false],
                ],
            ],
            [
                'q' => "What is the 'Halloween Problem' in SQL UPDATE statements?",
                'opts' => [
                    ['UPDATE statements fail every year on October 31st', false],
                    ['When an UPDATE modifies rows and the query plan reads those same rows again (via an index scan), causing rows to be updated multiple times — requires a buffer/temp copy to prevent', true],
                    ['UPDATE acquires locks that block all reads', false],
                    ['UPDATE returns incorrect row counts on large tables', false],
                ],
            ],

            // ── COLUMNAR STORAGE & OLAP ───────────────────────────────────
            [
                'q' => "Why is columnar storage (e.g., Parquet, Redshift, BigQuery) dramatically faster than row storage for analytical aggregations like SUM(revenue)?",
                'opts' => [
                    ['Columnar databases have larger RAM', false],
                    ['In columnar storage, all values of revenue are stored contiguously — the database reads only that column from disk (I/O reduction), and the homogeneous data compresses better and vectorizes well', true],
                    ['Columnar databases use more indexes', false],
                    ['Columnar storage stores data in sorted order automatically', false],
                ],
            ],
            [
                'q' => "What is predicate pushdown in a columnar query engine (e.g., Spark, Parquet reader)?",
                'opts' => [
                    ['Pushing SELECT columns to the end of the query', false],
                    ['Applying WHERE filters as early as possible (at the storage scan layer) to skip reading row groups that cannot contain matching data — reduces I/O massively', true],
                    ['Optimizing subqueries by moving them to the outer query', false],
                    ['Pushing GROUP BY computation to the client', false],
                ],
            ],
            [
                'q' => "What is Z-ordering (multi-dimensional clustering) in Delta Lake / data lakes, and what problem does it solve?",
                'opts' => [
                    ['Sorting data alphabetically to improve readability', false],
                    ['Co-locating related rows for multiple columns in the same file chunks — allows the reader to skip entire files when filtering on any of those columns, solving the multi-column data skipping problem', true],
                    ['Compressing data using Z-standard compression', false],
                    ['Partitioning data by the last letter of a string column', false],
                ],
            ],

            // ── PERFORMANCE ENGINEERING ───────────────────────────────────
            [
                'q' => "A data warehouse query scans 10TB per run and costs $50 per execution on BigQuery. What is the most cost-effective engineering change?",
                'opts' => [
                    ['Increase the query slot allocation', false],
                    ['Partition the table by date and cluster by the most filtered column — queries that filter by date/cluster will scan only the relevant partitions, potentially reducing scanned data by 99%+', true],
                    ['Convert all columns to STRING type for compression', false],
                    ['Cache all results in Redis', false],
                ],
            ],
            [
                'q' => "What is connection pool exhaustion and how does it manifest in a production data science application under load?",
                'opts' => [
                    ['The database server runs out of disk space', false],
                    ['All pooled connections are in use by concurrent requests — new requests block or timeout waiting for a connection, causing latency spikes and timeouts even if the database itself is healthy', true],
                    ['Too many indexes slow down all connections simultaneously', false],
                    ['The SQLAlchemy engine fails to import', false],
                ],
            ],
            [
                'q' => "What is a write-ahead log (WAL) in PostgreSQL, and how does it contribute to both durability and replication?",
                'opts' => [
                    ['A log of all SELECT queries for auditing', false],
                    ['Changes are written to the WAL (append-only log on disk) BEFORE they are applied to the main data files — ensures crash recovery (durability) and is the basis for streaming replication (replicas replay the WAL)', true],
                    ['A queue of pending INSERT statements waiting to be executed', false],
                    ['A backup of the entire database written daily', false],
                ],
            ],
            [
                'q' => "In a multi-tenant SaaS data platform serving 10,000 clients, what database isolation strategy best balances security, cost, and operational complexity?",
                'opts' => [
                    ['One separate PostgreSQL instance per tenant (10,000 instances)', false],
                    ['Shared database with tenant_id column + row-level security (RLS) policies — enforces isolation at the database layer without physical separation, with schema-per-tenant as a middle option for highly sensitive clients', true],
                    ['One CSV file per tenant on a shared network drive', false],
                    ['A single table with no tenant isolation', false],
                ],
            ],

            // ── ADVANCED PYTHON + SQL PATTERNS ────────────────────────────
            [
                'q' => "What is the correct pattern for bulk-inserting 1 million rows from Python to PostgreSQL with maximum performance?\n\n(A) for row in data: conn.execute(insert, row)  # row by row\n(B) conn.execute(insert, data)  # executemany\n(C) COPY command via psycopg2.copy_expert() or SQLAlchemy bulk_insert_mappings()",
                'opts' => [
                    ['(A) — row by row is most reliable', false],
                    ['(B) — executemany is always fastest', false],
                    ['(C) — COPY / bulk_insert bypasses row-by-row overhead and is 10-100x faster for large inserts', true],
                    ['All three have the same performance', false],
                ],
            ],
            [
                'q' => "What does this SQLAlchemy pattern accomplish, and why is it important for long-running pipelines?\n\nwith engine.connect() as conn:\n    for chunk in pd.read_sql(query, conn, chunksize=50000):\n        result = transform(chunk)\n        result.to_sql('output', conn, if_exists='append', index=False)\n        conn.commit()",
                'opts' => [
                    ['Reads all data into RAM then writes it out', false],
                    ['Processes data in 50,000-row streaming chunks — committing after each chunk so that a crash mid-pipeline results in partial progress (resumable) rather than full rollback', true],
                    ['Creates 50,000 separate tables', false],
                    ['Automatically handles SQL injection', false],
                ],
            ],
            [
                'q' => "What is the purpose of Alembic in a production data science project?",
                'opts' => [
                    ['A Python library for faster pandas operations', false],
                    ['A database migration tool for SQLAlchemy — tracks schema version history, generates migration scripts, and applies them in order to keep database schema in sync with application code across environments', true],
                    ['A connection pooling library for asyncio', false],
                    ['A visualization tool for EXPLAIN plans', false],
                ],
            ],
            [
                'q' => "What is dbt (data build tool) and how does it fit into the modern data stack?",
                'opts' => [
                    ['A Python library to replace pandas for data transformation', false],
                    ['A transformation framework that allows analysts to write modular SQL SELECT statements (models), with dependency management, testing, documentation, and lineage tracking built in — runs transformations inside the data warehouse', true],
                    ['A database administration tool for PostgreSQL only', false],
                    ['A replacement for SQLAlchemy in Python pipelines', false],
                ],
            ],

            // ── ADVANCED NoSQL / MULTI-MODEL ──────────────────────────────
            [
                'q' => "In Apache Kafka used as part of a streaming data pipeline into a database, what is 'exactly-once semantics' and why is it hard to achieve?",
                'opts' => [
                    ['Each message is processed and written to the database exactly once — hard because network failures can cause re-delivery (at-least-once) or message loss (at-most-once), requiring idempotent writes and transactional producers/consumers', true],
                    ['Kafka sends each message to exactly one consumer', false],
                    ['Each database write is committed in exactly one transaction', false],
                    ['Exactly-once is the default and easy to achieve', false],
                ],
            ],
            [
                'q' => "What is a time-series database (e.g., InfluxDB, TimescaleDB) optimized for, and what makes it better than a general-purpose relational DB for sensor data?",
                'opts' => [
                    ['General-purpose OLTP workloads with complex JOINs', false],
                    ['Append-heavy writes and time-range queries on timestamped data — automatic partitioning by time, compressed time-series storage, built-in downsampling/continuous aggregates, and functions like time_bucket()', true],
                    ['Document storage with flexible schemas', false],
                    ['Graph traversal and relationship queries', false],
                ],
            ],
            [
                'q' => "What is a graph database (e.g., Neo4j) best suited for, and why does it outperform relational databases for certain queries?",
                'opts' => [
                    ['Storing large tabular datasets with billions of rows', false],
                    ['Highly connected data with complex relationship traversals (social networks, fraud detection, knowledge graphs) — graph databases store relationships as first-class citizens (edges), avoiding expensive multi-table JOINs', true],
                    ['Time-series sensor data with high write throughput', false],
                    ['Full-text search over document collections', false],
                ],
            ],

            // ── PROFESSIONAL MISC ─────────────────────────────────────────
            [
                'q' => "What is the difference between OLTP and OLAP systems, and why do most production data science teams use separate systems for each?",
                'opts' => [
                    ['OLTP and OLAP are the same; the term used depends on the vendor', false],
                    ['OLTP (Online Transaction Processing) is optimized for short, concurrent read/write transactions; OLAP (Online Analytical Processing) is optimized for complex analytical queries over large historical datasets — running analytics on the OLTP system can degrade production application performance', true],
                    ['OLAP is a subset of OLTP for reporting only', false],
                    ['OLTP uses NoSQL; OLAP uses SQL', false],
                ],
            ],
            [
                'q' => "What is Apache Iceberg and what problem does it solve for large-scale data lakes?",
                'opts' => [
                    ['A compression algorithm for Parquet files', false],
                    ['An open table format that adds ACID transactions, schema evolution, time-travel queries, and efficient partition pruning to data lake file storage — fixing the lack of reliability in raw Hive/Parquet tables', true],
                    ['A managed PostgreSQL service on AWS', false],
                    ['A real-time streaming database', false],
                ],
            ],
            [
                'q' => "What is data vault modeling and when is it preferred over star/snowflake schema in an enterprise data warehouse?",
                'opts' => [
                    ['It is an older term for the star schema', false],
                    ['A modeling methodology that separates Hubs (business keys), Links (relationships), and Satellites (context/history) — preferred when source systems change frequently, auditability of every change is required, and the warehouse must be built incrementally without historical rewrites', true],
                    ['A type of normalization used for OLTP systems', false],
                    ['A schema specifically for storing unstructured JSON data', false],
                ],
            ],
            [
                'q' => "What is the difference between hot, warm, and cold data tiers in a storage strategy, and how does this apply to a data platform?",
                'opts' => [
                    ['They refer to the physical temperature of the server hardware', false],
                    ['Hot (frequently accessed, fast/expensive storage like SSD/in-memory), warm (occasionally accessed, standard SSD or HDD), cold (rarely accessed, cheap archival storage like S3 Glacier) — tiering reduces cost while maintaining acceptable access latency for each usage pattern', true],
                    ['Hot data is unencrypted; cold data is encrypted', false],
                    ['These tiers only apply to NoSQL databases', false],
                ],
            ],
            [
                'q' => "A machine learning pipeline runs a daily batch prediction job that reads from a PostgreSQL features table and writes predictions back. Over time, predictions degrade silently. What database practice detects this earliest?",
                'opts' => [
                    ['Adding more indexes to the features table', false],
                    ['Logging prediction timestamps with feature snapshots, monitoring statistical drift in feature distributions via scheduled SQL queries (mean, stddev, null rates), and alerting when distributions shift beyond a threshold', true],
                    ['Increasing the frequency of VACUUM ANALYZE', false],
                    ['Switching to a NoSQL feature store', false],
                ],
            ],
            [
                'q' => "What is the medallion architecture (Bronze/Silver/Gold layers) in a modern data lakehouse, and what does each layer contain?",
                'opts' => [
                    ['Three different database servers for development, staging, and production', false],
                    ['Bronze: raw ingested data (unchanged); Silver: cleaned, validated, joined data; Gold: aggregated, business-level data ready for analytics and ML — each layer progressively increases data quality and reduces volume', true],
                    ['Three compression levels applied to Parquet files', false],
                    ['Three security access levels for database users', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 10 — Database Management for Data Science (Professional).");
    }
}