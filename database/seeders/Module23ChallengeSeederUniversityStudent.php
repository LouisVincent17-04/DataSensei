<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module23ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Data Warehousing')
                 ->delete();

        $this->command->info("Creating Module 23 — Data Warehousing (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Warehousing',
            'description'           => 'Analytical questions on schema design trade-offs, SCD mechanics, ETL vs ELT decision-making, dimensional modelling choices, and Kimball vs Inmon comparisons. Requires reasoning through design decisions.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 750,
            'order_index'           => 23,
        ]);

        $this->command->info("Seeding 50 university-level Data Warehousing questions...");

        $qaData = [

            // ── 23.1 DATA WAREHOUSE CONCEPTS ──────────────────────────────
            [
                'q' => 'Bill Inmon\'s "top-down" approach to data warehousing builds:',
                'opts' => [
                    ['Data marts first, then integrates them', false],
                    ['A single integrated enterprise-wide data warehouse in 3NF first, from which data marts are derived', true],
                    ['A data lake, then migrates to a warehouse', false],
                    ['Individual OLTP systems first', false],
                ],
            ],
            [
                'q' => 'Which of the following is a key characteristic that distinguishes a data warehouse from an operational database?',
                'opts' => [
                    ['Warehouses only support INSERT operations', false],
                    ['Warehouses are optimised for complex read-heavy analytical queries over large historical datasets; operational DBs are optimised for fast transactional read/write', true],
                    ['Warehouses never use SQL', false],
                    ['Operational databases can only store one year of data', false],
                ],
            ],
            [
                'q' => 'A "grain" in dimensional modelling defines:',
                'opts' => [
                    ['The size of the fact table in gigabytes', false],
                    ['The level of detail (granularity) represented by each row in the fact table — e.g. "one row per individual sales transaction"', true],
                    ['The primary key structure of a dimension table', false],
                    ['The number of dimensions connected to a fact table', false],
                ],
            ],
            [
                'q' => 'Declaring the grain of a fact table should happen:',
                'opts' => [
                    ['After all dimension tables have been designed', false],
                    ['As the first and most critical step in designing any fact table', true],
                    ['Only when performance issues arise', false],
                    ['At the end of the ETL development phase', false],
                ],
            ],
            [
                'q' => 'An "additive fact" is one that can be:',
                'opts' => [
                    ['Added across all dimensions without restriction', true],
                    ['Added across some dimensions but not all', false],
                    ['Never aggregated with SUM', false],
                    ['Stored only in dimension tables', false],
                ],
            ],
            [
                'q' => 'A "semi-additive fact" (e.g. account balance) is one that:',
                'opts' => [
                    ['Can be summed across all dimensions freely', false],
                    ['Can be summed across some dimensions (e.g. across accounts) but not others (e.g. across time — summing daily balances makes no sense)', true],
                    ['Cannot be aggregated in any way', false],
                    ['Is only used in Data Vault models', false],
                ],
            ],
            [
                'q' => 'A "non-additive fact" (e.g. a percentage or ratio) is one that:',
                'opts' => [
                    ['Can be summed across all dimensions', false],
                    ['Cannot meaningfully be summed at all — e.g. adding profit margins across regions produces nonsense', true],
                    ['Can be summed across time but not products', false],
                    ['Is always stored in a dimension table', false],
                ],
            ],

            // ── 23.2 SCHEMA DESIGN ────────────────────────────────────────
            [
                'q' => 'A Star Schema is preferred over a Snowflake Schema for most data warehouse use cases because:',
                'opts' => [
                    ['Star schemas use less storage', false],
                    ['Star schemas require fewer JOINs, making queries simpler and faster for end users and BI tools', true],
                    ['Star schemas support more dimensions', false],
                    ['Snowflake schemas cannot store dates', false],
                ],
            ],
            [
                'q' => 'A Snowflake Schema reduces data redundancy by normalising dimension tables. The tradeoff is:',
                'opts' => [
                    ['More storage is used', false],
                    ['Queries require more JOINs to navigate the normalised hierarchy, increasing query complexity and potentially reducing performance', true],
                    ['It cannot support historical data', false],
                    ['It only works with small datasets', false],
                ],
            ],
            [
                'q' => 'A "degenerate dimension" is:',
                'opts' => [
                    ['A dimension table with very few rows', false],
                    ['A dimensional attribute stored directly in the fact table with no corresponding dimension table — e.g. an order number', true],
                    ['A dimension that has been deprecated', false],
                    ['A dimension containing NULL values', false],
                ],
            ],
            [
                'q' => 'A "junk dimension" is used to:',
                'opts' => [
                    ['Store corrupted or invalid data', false],
                    ['Group low-cardinality flags and indicators (e.g. is_promotional, is_returned) into a single dimension table to avoid cluttering the fact table', true],
                    ['Replace the date dimension', false],
                    ['Store foreign keys from multiple fact tables', false],
                ],
            ],
            [
                'q' => 'A "role-playing dimension" occurs when:',
                'opts' => [
                    ['A dimension table is shared between two schemas', false],
                    ['A single dimension is referenced multiple times in the same fact table serving different roles — e.g. a Date dimension used as Order Date, Ship Date, and Return Date', true],
                    ['A dimension changes its structure over time', false],
                    ['A dimension is only used for reporting, not joins', false],
                ],
            ],

            // ── 23.3 ETL vs ELT ───────────────────────────────────────────
            [
                'q' => 'ETL is typically preferred when:',
                'opts' => [
                    ['The target is a modern cloud data warehouse with unlimited compute', false],
                    ['Sensitive data must be masked or filtered BEFORE it reaches the target system, or when the target system has limited compute power', true],
                    ['The transformation logic is extremely simple', false],
                    ['The source system is also a data warehouse', false],
                ],
            ],
            [
                'q' => 'ELT is typically preferred when:',
                'opts' => [
                    ['The source system cannot export data', false],
                    ['The target is a powerful cloud data warehouse (Snowflake, BigQuery) where transformations can be done cheaply and at scale using SQL', true],
                    ['Only batch processing is required', false],
                    ['The data volume is very small (under 1GB)', false],
                ],
            ],
            [
                'q' => 'CDC (Change Data Capture) in ETL pipelines refers to:',
                'opts' => [
                    ['A method to compress data before loading', false],
                    ['The technique of identifying and capturing only the data that has CHANGED in the source system since the last extraction, rather than re-extracting everything', true],
                    ['A tool for comparing two database schemas', false],
                    ['A data quality validation framework', false],
                ],
            ],
            [
                'q' => 'Incremental loading in an ETL pipeline is preferred over full loading because:',
                'opts' => [
                    ['Incremental loads are simpler to implement', false],
                    ['It is far more efficient — only new or changed records are processed, reducing load time and resource consumption significantly', true],
                    ['Full loads risk overwriting all data', false],
                    ['Incremental loads skip the transform step', false],
                ],
            ],
            [
                'q' => 'The "watermark" technique in incremental ETL uses:',
                'opts' => [
                    ['A timestamp or ID column to track the last successfully loaded record, so only records created/modified after that point are extracted', true],
                    ['A hash of all rows to detect changes', false],
                    ['A binary flag added to every source row', false],
                    ['A separate audit database to track changes', false],
                ],
            ],

            // ── 23.4 SCD ──────────────────────────────────────────────────
            [
                'q' => 'SCD Type 3 tracks changes by:',
                'opts' => [
                    ['Overwriting the current value', false],
                    ['Inserting a new row for each change', false],
                    ['Adding new columns to store a limited number of previous values (e.g. current_city and previous_city)', true],
                    ['Archiving old rows in a separate history table', false],
                ],
            ],
            [
                'q' => 'A customer\'s city changes from "Manila" to "Cebu". Which SCD type should you use if business analysts need to query sales for a customer\'s city AT THE TIME OF EACH SALE (full history)?',
                'opts' => [
                    ['SCD Type 1 — overwrite Manila with Cebu', false],
                    ['SCD Type 2 — insert a new row with Cebu, set effective dates on the old Manila row', true],
                    ['SCD Type 3 — add a previous_city column', false],
                    ['No change needed — city never changes', false],
                ],
            ],
            [
                'q' => 'In SCD Type 2, when a new row is inserted for a changed attribute, the PREVIOUS row typically has its end_date set to:',
                'opts' => [
                    ['NULL', false],
                    ['The day BEFORE the new row\'s start_date (or exactly the new row\'s start_date depending on convention)', true],
                    ['The system\'s install date', false],
                    ['The original row\'s start_date', false],
                ],
            ],
            [
                'q' => 'What is the main disadvantage of SCD Type 2?',
                'opts' => [
                    ['It loses all historical data', false],
                    ['It causes the dimension table to grow very large over time as new rows are added for every change', true],
                    ['It cannot track more than one attribute change', false],
                    ['It requires a special database engine', false],
                ],
            ],

            // ── 23.5 DATA VAULT ───────────────────────────────────────────
            [
                'q' => 'In Data Vault, a LINK table stores:',
                'opts' => [
                    ['Descriptive attributes of a hub', false],
                    ['The business keys of two or more hubs, representing a relationship or transaction between them', true],
                    ['Historical changes of a satellite', false],
                    ['Aggregated metrics for reporting', false],
                ],
            ],
            [
                'q' => 'Data Vault uses hash keys instead of sequential surrogate keys because:',
                'opts' => [
                    ['Hash keys are always shorter', false],
                    ['Hash keys are deterministic and can be computed independently in parallel across different load jobs without coordination, enabling scalable parallel loading', true],
                    ['Sequential keys cause data duplication', false],
                    ['Hash keys improve query performance', false],
                ],
            ],
            [
                'q' => 'A "Point-in-Time" (PIT) table in Data Vault is used to:',
                'opts' => [
                    ['Store the latest version of each satellite record only', false],
                    ['Improve query performance by snapshotting the latest satellite records at regular time intervals, avoiding expensive JOINS across multiple satellites', true],
                    ['Track all changes in hub business keys', false],
                    ['Replace link tables for better performance', false],
                ],
            ],

            // ── 23.6 COLUMNAR STORAGE & QUERY OPTIMISATION ───────────────
            [
                'q' => 'Partition pruning in a data warehouse means:',
                'opts' => [
                    ['Deleting old partitions to save space', false],
                    ['The query engine skips scanning entire partitions that cannot contain relevant data based on the WHERE clause — e.g. skipping all months except the queried month', true],
                    ['Splitting a large table into smaller tables', false],
                    ['Removing duplicate partitions', false],
                ],
            ],
            [
                'q' => 'In Amazon Redshift, the DISTRIBUTION KEY determines:',
                'opts' => [
                    ['How data is sorted within each node', false],
                    ['How table rows are distributed across compute nodes — a well-chosen dist key minimises data movement during JOIN operations', true],
                    ['The compression algorithm applied to each column', false],
                    ['The number of slices per node', false],
                ],
            ],
            [
                'q' => 'In Redshift and similar MPP (Massively Parallel Processing) databases, a "sort key" is used to:',
                'opts' => [
                    ['Sort query results before returning them to the user', false],
                    ['Order how data is physically stored on disk so that range queries on the sort key column can skip large sections of data (zone maps)', true],
                    ['Ensure primary key uniqueness', false],
                    ['Speed up INSERT operations', false],
                ],
            ],
            [
                'q' => 'Run-length encoding (RLE) compression is most effective for columns where:',
                'opts' => [
                    ['Values are all unique integers', false],
                    ['The same value repeats many consecutive times — e.g. status = "ACTIVE" for thousands of rows in a row', true],
                    ['Values are stored as JSON', false],
                    ['Columns contain floating-point numbers', false],
                ],
            ],

            // ── 23.7 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => 'Snowflake\'s "Virtual Warehouses" are:',
                'opts' => [
                    ['Logical containers for schemas and tables', false],
                    ['Independent compute clusters that can be started, stopped, and scaled without affecting the underlying stored data', true],
                    ['Pre-built data models available as a service', false],
                    ['The Snowflake equivalent of a SQL schema', false],
                ],
            ],
            [
                'q' => 'BigQuery uses a serverless architecture meaning:',
                'opts' => [
                    ['No data is stored on servers', false],
                    ['Users do not provision or manage compute infrastructure — Google automatically allocates resources for each query', true],
                    ['It cannot run on Google\'s servers', false],
                    ['There are no query size limits', false],
                ],
            ],
            [
                'q' => 'Snowflake\'s "Time Travel" feature allows users to:',
                'opts' => [
                    ['Query data as it existed at any point in the past (up to 90 days) — enabling recovery from accidental changes or deletions', true],
                    ['Schedule queries to run in the future', false],
                    ['View query execution history from the past 7 days', false],
                    ['Restore deleted user accounts', false],
                ],
            ],
            [
                'q' => 'A key cost-saving strategy in BigQuery is to:',
                'opts' => [
                    ['Always use SELECT * to retrieve all columns', false],
                    ['Select only the columns you need — since BigQuery charges by data scanned, avoiding SELECT * significantly reduces costs on wide tables', true],
                    ['Use smaller virtual machines', false],
                    ['Disable query caching', false],
                ],
            ],

            // ── 23.8 KIMBALL BUS ARCHITECTURE ────────────────────────────
            [
                'q' => 'The Kimball Bus Matrix maps:',
                'opts' => [
                    ['Source systems to target tables', false],
                    ['Business processes (fact tables) on one axis and dimensions on the other — showing which dimensions are shared (conformed) across which fact tables', true],
                    ['ETL jobs to their execution schedules', false],
                    ['Data marts to their respective servers', false],
                ],
            ],
            [
                'q' => 'The main advantage of conformed dimensions in Kimball\'s architecture is:',
                'opts' => [
                    ['They reduce the number of tables in the warehouse', false],
                    ['They enable "drill-across" queries that join facts from different data marts using the shared dimension as the link — ensuring consistent analysis', true],
                    ['They eliminate the need for surrogate keys', false],
                    ['They automatically handle SCD Type 2', false],
                ],
            ],

            // ── 23.9 dbt ─────────────────────────────────────────────────
            [
                'q' => 'In dbt, the `ref()` function is used to:',
                'opts' => [
                    ['Reference an external database connection', false],
                    ['Reference another dbt model by name — dbt uses this to build the dependency DAG and ensure models run in the correct order', true],
                    ['Create a new database table', false],
                    ['Run a test on a model', false],
                ],
            ],
            [
                'q' => 'dbt materialisation types include which of the following?',
                'opts' => [
                    ['view, table, incremental, ephemeral', true],
                    ['staging, intermediate, marts, raw', false],
                    ['bronze, silver, gold, platinum', false],
                    ['full_refresh, partial_refresh, snapshot, seed', false],
                ],
            ],
            [
                'q' => 'When a dbt model is materialised as "incremental", dbt will:',
                'opts' => [
                    ['Always recreate the full table from scratch on every run', false],
                    ['Only process new or changed records since the last run, appending or updating the existing table rather than rebuilding it', true],
                    ['Create a view that is recalculated on every query', false],
                    ['Store results only in memory, not on disk', false],
                ],
            ],
            [
                'q' => 'dbt tests (schema tests) can verify which of the following data quality checks out of the box?',
                'opts' => [
                    ['not_null, unique, accepted_values, relationships', true],
                    ['row_count, column_count, data_type, encoding', false],
                    ['min_value, max_value, average, standard_deviation', false],
                    ['foreign_key, primary_key, index, constraint', false],
                ],
            ],
            [
                'q' => 'In dbt, a "source" is defined to:',
                'opts' => [
                    ['Create a new table in the warehouse', false],
                    ['Declare and document raw source tables in the warehouse so dbt can reference them, test them, and track freshness', true],
                    ['Connect dbt to an external API', false],
                    ['Schedule a dbt job to run hourly', false],
                ],
            ],

            // ── 23.10 DATA QUALITY & GOVERNANCE ──────────────────────────
            [
                'q' => 'The six dimensions of data quality most commonly referenced are:',
                'opts' => [
                    ['Accuracy, Completeness, Consistency, Timeliness, Validity, Uniqueness', true],
                    ['Speed, Volume, Variety, Veracity, Value, Velocity', false],
                    ['Structured, Unstructured, Semi-structured, Linked, Open, Closed', false],
                    ['Raw, Staged, Cleaned, Enriched, Aggregated, Reported', false],
                ],
            ],
            [
                'q' => 'A data steward in a data governance framework is responsible for:',
                'opts' => [
                    ['Writing ETL pipeline code', false],
                    ['Managing the quality, definitions, and appropriate use of data within a specific business domain', true],
                    ['Administering the cloud warehouse infrastructure', false],
                    ['Building BI dashboards for executives', false],
                ],
            ],
            [
                'q' => 'What is the purpose of data lineage tracking in a data warehouse?',
                'opts' => [
                    ['To compress old data and save storage', false],
                    ['To provide full visibility into where data comes from, what transformations it has undergone, and where it flows — essential for debugging, auditing, and compliance', true],
                    ['To automatically fix data quality issues', false],
                    ['To rank data assets by business value', false],
                ],
            ],
            [
                'q' => 'The "Modern Data Stack" typically refers to a combination of:',
                'opts' => [
                    ['Hadoop, Hive, Pig, and Sqoop', false],
                    ['Cloud data warehouse + ELT tools (Fivetran/Airbyte) + dbt + BI tool (Tableau/Looker/Metabase)', true],
                    ['Oracle, Informatica, Cognos, and SSAS', false],
                    ['Kafka, Spark, Cassandra, and HBase', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 23 — Data Warehousing (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}