<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module23ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Data Warehousing')
                 ->delete();

        $this->command->info("Creating Module 23 — Data Warehousing (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Warehousing',
            'description'           => 'Multi-step problems combining SQL tracing, dbt model logic, SCD implementation, schema design decisions, and query optimisation reasoning. Requires reading code snippets and choosing the correct design or output.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1000,
            'order_index'           => 23,
        ]);

        $this->command->info("Seeding 50 intermediate-level Data Warehousing questions...");

        $qaData = [

            // ── 23.2 SCHEMA DESIGN — CODE & REASONING ────────────────────
            [
                'q' => "Given this schema:\n  fact_sales (date_key, product_key, customer_key, store_key, revenue, quantity)\n  dim_date (date_key, year, quarter, month, day)\n  dim_product (product_key, product_name, category, brand)\n  dim_customer (customer_key, customer_name, city, country)\n  dim_store (store_key, store_name, region)\n\nWhat schema type is this and what is its name?",
                'opts' => [
                    ['Snowflake Schema — because dimensions are normalised', false],
                    ['Star Schema — fact_sales sits in the centre directly connected to all dimension tables with no sub-dimensions', true],
                    ['Data Vault — hubs and satellites are visible', false],
                    ['Third Normal Form (3NF) — no redundancy', false],
                ],
            ],
            [
                'q' => "What does this SQL query compute?\n\nSELECT\n    d.year,\n    d.quarter,\n    p.category,\n    SUM(f.revenue) AS total_revenue,\n    COUNT(DISTINCT f.customer_key) AS unique_customers\nFROM fact_sales f\nJOIN dim_date d ON f.date_key = d.date_key\nJOIN dim_product p ON f.product_key = p.product_key\nGROUP BY d.year, d.quarter, p.category\nORDER BY d.year, d.quarter, p.category;",
                'opts' => [
                    ['Total revenue and unique customers per individual transaction', false],
                    ['Total revenue and unique customer count grouped by year, quarter, and product category', true],
                    ['Average revenue per product', false],
                    ['The most recent sale per category', false],
                ],
            ],
            [
                'q' => "A fact table has grain 'one row per daily product inventory snapshot'. A data analyst runs:\n\nSELECT product_key, SUM(inventory_quantity)\nFROM fact_inventory\nWHERE date_key BETWEEN 20240101 AND 20240107\nGROUP BY product_key;\n\nWhat is the problem with this query?",
                'opts' => [
                    ['The date range is too large', false],
                    ['inventory_quantity is a semi-additive fact — summing daily snapshots across time gives a meaningless total. The correct approach is to use AVG or query the latest snapshot', true],
                    ['GROUP BY should include date_key', false],
                    ['There is no problem — this query is correct', false],
                ],
            ],
            [
                'q' => "You need to model a scenario where an order can have multiple line items, and each line item has a product. The correct grain for the fact table is:",
                'opts' => [
                    ['One row per order', false],
                    ['One row per order line item — capturing revenue, quantity, and discount at the most granular level', true],
                    ['One row per customer per day', false],
                    ['One row per product per month', false],
                ],
            ],
            [
                'q' => "A bridge table in dimensional modelling is used when:",
                'opts' => [
                    ['A fact table has too many rows', false],
                    ['A fact and a dimension have a many-to-many relationship — e.g. a customer can belong to multiple segments simultaneously', true],
                    ['Two fact tables need to be joined directly', false],
                    ['A dimension table has more than 10 columns', false],
                ],
            ],

            // ── 23.3 ETL — CODE TRACING ───────────────────────────────────
            [
                'q' => "This incremental ETL uses a watermark. What records will be extracted?\n\nLAST_WATERMARK = '2024-01-14 00:00:00'\n\nSELECT *\nFROM source_orders\nWHERE updated_at > '2024-01-14 00:00:00'\nORDER BY updated_at ASC;",
                'opts' => [
                    ['All records updated on or before 2024-01-14', false],
                    ['All records updated AFTER 2024-01-14 00:00:00 — records created or modified since the last successful load', true],
                    ['Only records created on exactly 2024-01-14', false],
                    ['All records in the source table', false],
                ],
            ],
            [
                'q' => "What is the risk in this ETL timestamp watermark approach?\n\nWHERE updated_at > :last_watermark\n\n-- Source DB is in UTC+8, warehouse is in UTC",
                'opts' => [
                    ['The query will run out of memory', false],
                    ['Timezone mismatch — records updated in the source system may have timestamps that appear AFTER the watermark in local time but overlap when converted to UTC, causing records to be missed or double-loaded', true],
                    ['The > operator should be >=', false],
                    ['Watermarks only work with integer keys, not timestamps', false],
                ],
            ],
            [
                'q' => "A full refresh ETL truncates and reloads a dimension table every night. The table has 50 million rows. Which problem does this cause?",
                'opts' => [
                    ['No problem — full refresh is always the safest approach', false],
                    ['The nightly load window may be too long, and during the truncate-reload window the table is unavailable for queries — causing reporting downtime', true],
                    ['Full refresh cannot handle NULL values', false],
                    ['It automatically deletes all foreign keys', false],
                ],
            ],

            // ── 23.4 SCD — IMPLEMENTATION ────────────────────────────────
            [
                'q' => "A customer's record in dim_customer looks like this after an SCD Type 2 update:\n\ncustomer_key | source_id | city      | eff_start  | eff_end    | is_current\n1001         | C-500     | Manila    | 2020-01-01 | 2023-06-14 | FALSE\n1002         | C-500     | Cebu      | 2023-06-15 | NULL       | TRUE\n\nA sales record from 2022-03-10 has customer_key = 1001. What city was the customer in at the time of sale?",
                'opts' => [
                    ['Cebu', false],
                    ['Manila — the sale links to customer_key 1001 which was active during 2022', true],
                    ['Cannot be determined', false],
                    ['NULL — the city was not recorded in 2022', false],
                ],
            ],
            [
                'q' => "To query the CURRENT city for ALL customers in a SCD Type 2 dimension, the correct WHERE clause is:",
                'opts' => [
                    ['WHERE eff_end IS NULL', false],
                    ['WHERE is_current = TRUE', false],
                    ['Either WHERE is_current = TRUE or WHERE eff_end IS NULL — both are valid conventions but should be consistent', true],
                    ['WHERE customer_key = MAX(customer_key)', false],
                ],
            ],
            [
                'q' => "What is the correct SQL to implement an SCD Type 2 'expire old row' step when a customer moves from Manila to Cebu on 2024-03-01?",
                'opts' => [
                    ["UPDATE dim_customer\nSET city = 'Cebu'\nWHERE source_id = 'C-500';", false],
                    ["UPDATE dim_customer\nSET eff_end = '2024-02-29', is_current = FALSE\nWHERE source_id = 'C-500' AND is_current = TRUE;", true],
                    ["DELETE FROM dim_customer\nWHERE source_id = 'C-500';", false],
                    ["INSERT INTO dim_customer (source_id, city, eff_start)\nVALUES ('C-500', 'Cebu', '2024-03-01');", false],
                ],
            ],

            // ── 23.5 DATA VAULT — INTERMEDIATE ───────────────────────────
            [
                'q' => "In Data Vault, a 'Raw Vault' stores:",
                'opts' => [
                    ['Cleaned and business-rule-applied data ready for reporting', false],
                    ['Data loaded directly from source systems with no business rules applied — preserving the original data exactly as received', true],
                    ['Only hub tables', false],
                    ['Aggregated summary tables', false],
                ],
            ],
            [
                'q' => "In Data Vault, a 'Business Vault' contains:",
                'opts' => [
                    ['Raw source data with timestamps', false],
                    ['Derived and business-rule-enriched structures built on top of the Raw Vault — e.g. computed derived satellites, bridge tables, PIT tables', true],
                    ['The same data as the Raw Vault but compressed', false],
                    ['Only satellite tables', false],
                ],
            ],
            [
                'q' => "What is a 'Same-As Link' (SAL) in Data Vault?",
                'opts' => [
                    ['A link that connects two identical hub records from the same source', false],
                    ['A special link that records that two hub records represent the same real-world entity — used for identity resolution / deduplication across source systems', true],
                    ['A link between two satellites', false],
                    ['A reference to a conformed dimension', false],
                ],
            ],

            // ── 23.6 QUERY OPTIMISATION ───────────────────────────────────
            [
                'q' => "This BigQuery query scans a 10TB table but only needs 2 columns from the last 7 days. Which optimisation has the biggest impact?\n\nSELECT order_id, revenue\nFROM `project.dataset.fact_sales`\nWHERE DATE(created_at) >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY);",
                'opts' => [
                    ['Add an index on created_at', false],
                    ['Partition the table by DATE(created_at) — BigQuery will scan only the 7 relevant partition files instead of the full 10TB', true],
                    ['Add a LIMIT 1000 clause', false],
                    ['Convert the query to NoSQL', false],
                ],
            ],
            [
                'q' => "In Snowflake, what is 'query result caching' and when does it NOT help?",
                'opts' => [
                    ['Snowflake caches the first 1,000 rows of every query — it does not help for large result sets', false],
                    ['Snowflake caches exact query results for 24 hours. It does NOT help when the underlying data has changed, when the query text differs even slightly, or when using functions like CURRENT_TIMESTAMP()', true],
                    ['Query caching only applies to SELECT * queries', false],
                    ['Caching is disabled by default in Snowflake', false],
                ],
            ],
            [
                'q' => "A JOIN between fact_sales (2 billion rows) and dim_customer (500,000 rows) in Redshift is slow. The most likely fix is:",
                'opts' => [
                    ['Increase the Redshift node count', false],
                    ['Set the DISTRIBUTION KEY on fact_sales and dim_customer to customer_key — co-locating matching rows on the same node eliminates expensive data redistribution during the JOIN', true],
                    ['Use a FULL OUTER JOIN instead of INNER JOIN', false],
                    ['Index the customer_key column', false],
                ],
            ],
            [
                'q' => "What does VACUUM in Redshift do and when should you run it?",
                'opts' => [
                    ['Deletes all NULL values from tables', false],
                    ['Reclaims space from deleted rows and re-sorts data according to the sort key — should be run after large DELETE or UPDATE operations', true],
                    ['Compresses all columns using the best encoding', false],
                    ['Resets query cache statistics', false],
                ],
            ],

            // ── 23.7 CLOUD DWH — INTERMEDIATE ────────────────────────────
            [
                'q' => "Snowflake's 'zero-copy cloning' feature works by:",
                'opts' => [
                    ['Physically duplicating all data to a new location', false],
                    ['Creating metadata pointers to the original micro-partitions — the clone is instantaneous and uses no additional storage until data diverges between original and clone', true],
                    ['Creating a read-only snapshot of the table', false],
                    ['Compressing the table and making a backup', false],
                ],
            ],
            [
                'q' => "BigQuery ARRAY and STRUCT types are useful for:",
                'opts' => [
                    ['Storing images and binary files', false],
                    ['Representing nested and repeated data (like JSON-style data) natively in BigQuery, avoiding expensive JOINs for hierarchical data', true],
                    ['Encrypting sensitive columns', false],
                    ['Storing time-series data with sub-second precision', false],
                ],
            ],
            [
                'q' => "What is BigQuery's 'Slot' and why does it matter for query performance?",
                'opts' => [
                    ['A slot is a storage partition — more slots means more storage', false],
                    ['A slot is a unit of computational capacity (CPU+memory+I/O) — queries compete for available slots, and on-demand pricing auto-scales while flat-rate pricing allocates a fixed number', true],
                    ['A slot is a table partition in BigQuery', false],
                    ['A slot is the unit used to measure data scanned per query', false],
                ],
            ],

            // ── 23.9 dbt — INTERMEDIATE ───────────────────────────────────
            [
                'q' => "What does this dbt model file produce?\n\n-- models/marts/fct_daily_revenue.sql\n{{ config(materialized='table') }}\n\nSELECT\n    date_key,\n    SUM(revenue)   AS total_revenue,\n    COUNT(*)       AS num_transactions\nFROM {{ ref('stg_sales') }}\nGROUP BY date_key",
                'opts' => [
                    ['A view that recalculates on every query', false],
                    ['A physical table named fct_daily_revenue containing daily aggregated revenue and transaction count, built from the stg_sales model', true],
                    ['An ephemeral CTE used only within other models', false],
                    ['A test that validates revenue is positive', false],
                ],
            ],
            [
                'q' => "In dbt, what is the purpose of 'seeds'?",
                'opts' => [
                    ['To initialise the database with random data for testing', false],
                    ['To load small static CSV files (e.g. country codes, calendar data, mapping tables) directly into the data warehouse as tables', true],
                    ['To trigger incremental model runs', false],
                    ['To define the starting watermark for incremental models', false],
                ],
            ],
            [
                'q' => "What does `dbt test` validate in a project with this schema.yml?\n\nmodels:\n  - name: dim_customer\n    columns:\n      - name: customer_key\n        tests:\n          - unique\n          - not_null",
                'opts' => [
                    ['That dim_customer has no duplicate customer_key values and no NULL customer_key values', true],
                    ['That dim_customer has the correct number of rows', false],
                    ['That all revenue values are positive', false],
                    ['That dim_customer joined successfully with fact_sales', false],
                ],
            ],
            [
                'q' => "What is a dbt 'snapshot' used for?",
                'opts' => [
                    ['Taking a backup of the warehouse every night', false],
                    ['Implementing SCD Type 2 automatically — dbt snapshots track changes in source tables and maintain a history of changes using effective dates and is_current flags', true],
                    ['Creating materialised views for performance', false],
                    ['Scheduling dbt jobs to run at fixed intervals', false],
                ],
            ],
            [
                'q' => "In a dbt project, the recommended folder structure separates models into:",
                'opts' => [
                    ['input, process, output', false],
                    ['staging (raw → clean), intermediate (business logic), marts (final analytical models)', true],
                    ['bronze, silver, gold', false],
                    ['raw, transform, load', false],
                ],
            ],

            // ── 23.10 DATA QUALITY — INTERMEDIATE ────────────────────────
            [
                'q' => "A data quality check finds that 5% of fact_sales rows have a NULL customer_key. This is an example of which data quality dimension failing?",
                'opts' => [
                    ['Timeliness', false],
                    ['Completeness — required foreign key values are missing', true],
                    ['Uniqueness', false],
                    ['Validity', false],
                ],
            ],
            [
                'q' => "A data quality check finds that customer ages in dim_customer range from −5 to 250. Which data quality dimension is violated?",
                'opts' => [
                    ['Completeness', false],
                    ['Uniqueness', false],
                    ['Validity — values fall outside the acceptable domain (0–120)', true],
                    ['Timeliness', false],
                ],
            ],
            [
                'q' => "You run `dbt test` and find the `unique` test on order_id fails with 1,200 duplicate rows. What is the most likely root cause in an ELT pipeline?",
                'opts' => [
                    ['dbt is not installed correctly', false],
                    ['The incremental model ran multiple times without deduplication logic — the same orders were loaded more than once due to missing DISTINCT or ROW_NUMBER() dedup in the staging model', true],
                    ['order_id is not a valid column name', false],
                    ['Unique tests always fail on large tables', false],
                ],
            ],
            [
                'q' => "Data freshness monitoring in dbt is configured using:",
                'opts' => [
                    ['The --freshness flag in dbt run', false],
                    ['The `freshness` property on a source definition — specifying warn_after and error_after thresholds for how old the most recent data can be', true],
                    ['A custom Python script that checks load timestamps', false],
                    ['The `updated_at` test in schema.yml', false],
                ],
            ],
            [
                'q' => "A GDPR 'right to erasure' request arrives for a customer. The customer has SCD Type 2 history spanning 5 rows in dim_customer. What must happen in the warehouse?",
                'opts' => [
                    ['Only delete the is_current = TRUE row', false],
                    ['Delete or anonymise all 5 historical rows AND update any foreign key references in fact tables — data warehouses are typically non-volatile but GDPR compliance overrides this', true],
                    ['Archive all rows to cold storage', false],
                    ['Nothing — data warehouses are exempt from GDPR', false],
                ],
            ],
            [
                'q' => "What is 'data observability' in a modern data stack?",
                'opts' => [
                    ['The ability to view raw data without transformation', false],
                    ['The continuous monitoring of data health — covering freshness, volume, schema changes, distribution shifts, and lineage — to detect and alert on data quality issues automatically', true],
                    ['A feature of BI tools that shows data refresh times', false],
                    ['Audit logging of who accessed what data', false],
                ],
            ],
            [
                'q' => "A column-level lineage tool shows that dim_customer.city derives from:\n  raw.crm.contacts.billing_city (via staging_contacts)\n  raw.erp.accounts.address_city (via staging_accounts)\n\nWhat problem does this reveal?",
                'opts' => [
                    ['The column has two possible sources — this reveals a potential data conflict where city could be overwritten or merged incorrectly, requiring clear business rules on which source is authoritative', true],
                    ['Both sources are identical so no problem exists', false],
                    ['Column lineage only applies to fact tables', false],
                    ['city should be stored in the fact table, not the dimension', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 23 — Data Warehousing (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}