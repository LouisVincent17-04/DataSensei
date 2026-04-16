<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module23ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Data Warehousing')
                 ->delete();

        $this->command->info("Creating Module 23 — Data Warehousing (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Warehousing',
            'description'           => 'Deep data warehousing problems covering complex dbt model debugging, advanced Snowflake/BigQuery optimisation, Data Vault implementation, window functions in warehousing context, and complex SCD edge cases. Requires reading and correcting production code.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1500,
            'order_index'           => 23,
        ]);

        $this->command->info("Seeding 50 advanced-level Data Warehousing questions...");

        $qaData = [

            // ── DIMENSIONAL MODELLING — ADVANCED ──────────────────────────
            [
                'q' => "A fact table contains a 'discount_percentage' column. An analyst runs:\n\nSELECT product_key, SUM(discount_percentage)\nFROM fact_sales\nGROUP BY product_key;\n\nWhat is conceptually wrong?",
                'opts' => [
                    ['SUM requires a numeric column', false],
                    ['discount_percentage is a non-additive fact — summing percentages across transactions is mathematically meaningless. The correct approach is to compute revenue × discount_rate or use a weighted average', true],
                    ['GROUP BY should include date_key', false],
                    ['Discount should be stored in a dimension, not a fact', false],
                ],
            ],
            [
                'q' => "This query calculates the correct metric for a non-additive ratio fact:\n\nSELECT\n    product_key,\n    SUM(revenue * discount_rate) / NULLIF(SUM(revenue), 0) AS weighted_avg_discount\nFROM fact_sales\nGROUP BY product_key;\n\nWhy is NULLIF(SUM(revenue), 0) used?",
                'opts' => [
                    ['To filter out products with zero revenue', false],
                    ['To prevent division-by-zero errors — if SUM(revenue) is 0, NULLIF returns NULL, making the division return NULL instead of a runtime error', true],
                    ['To convert revenue to a percentage', false],
                    ['NULLIF is required for weighted averages in all databases', false],
                ],
            ],
            [
                'q' => "A factless fact table is used to capture:",
                'opts' => [
                    ['Facts with zero monetary value', false],
                    ['Events that occur without any measurable numeric metric — e.g. student course enrollment, product promotion coverage, or attendance records', true],
                    ['A fact table with no dimension keys', false],
                    ['Aggregated summary facts with no raw detail', false],
                ],
            ],
            [
                'q' => "In a type 4 SCD (mini-dimension), the purpose is to:",
                'opts' => [
                    ['Store only two versions of each attribute', false],
                    ['Separate frequently changing attributes into a standalone \'mini\' dimension table to avoid causing massive SCD Type 2 row explosions in the main dimension', true],
                    ['Archive dimension rows in a separate historical table', false],
                    ['Combine Type 1 and Type 2 behaviour in one table', false],
                ],
            ],
            [
                'q' => "An SCD Type 6 combines which types?",
                'opts' => [
                    ['Types 1 and 2 only', false],
                    ['Types 1, 2, and 3 — maintaining a full history (Type 2 rows), overwriting a current attribute column (Type 1), and adding a previous value column (Type 3)', true],
                    ['Types 2, 4, and 5', false],
                    ['Types 3 and 4 only', false],
                ],
            ],

            // ── ETL/ELT — ADVANCED CODE ────────────────────────────────────
            [
                'q' => "This Snowflake MERGE statement implements an upsert. What does it do?\n\nMERGE INTO dim_customer AS target\nUSING staging_customer AS source\n    ON target.source_id = source.source_id\n    AND target.is_current = TRUE\nWHEN MATCHED AND (\n    target.city <> source.city OR\n    target.email <> source.email\n) THEN UPDATE SET\n    target.eff_end    = CURRENT_DATE - 1,\n    target.is_current = FALSE\nWHEN NOT MATCHED BY TARGET THEN INSERT (\n    source_id, city, email, eff_start, eff_end, is_current\n) VALUES (\n    source.source_id, source.city, source.email,\n    CURRENT_DATE, NULL, TRUE\n);",
                'opts' => [
                    ['Performs SCD Type 1 — overwrites existing records', false],
                    ['Expires the current matching row when city or email has changed AND inserts new source rows that do not yet exist in the target — but does NOT insert the new version of changed rows', true],
                    ['Deletes changed rows and re-inserts them', false],
                    ['Performs a complete SCD Type 2 cycle in one statement', false],
                ],
            ],
            [
                'q' => "What is missing from the MERGE statement above to complete a full SCD Type 2 implementation?",
                'opts' => [
                    ['A DELETE clause for removed customers', false],
                    ['A second INSERT clause (or separate INSERT statement) to add the NEW version of changed rows with the updated city/email, new eff_start = CURRENT_DATE, eff_end = NULL, is_current = TRUE', true],
                    ['An index on source_id', false],
                    ['Nothing — the MERGE is complete', false],
                ],
            ],
            [
                'q' => "A dbt incremental model uses this config:\n\n{{ config(\n    materialized='incremental',\n    unique_key='order_id',\n    incremental_strategy='merge'\n) }}\n\nSELECT * FROM {{ ref('stg_orders') }}\n{% if is_incremental() %}\nWHERE updated_at > (SELECT MAX(updated_at) FROM {{ this }})\n{% endif %}\n\nWhat does `{{ this }}` refer to?",
                'opts' => [
                    ['The source table stg_orders', false],
                    ['The already-existing target table for this model in the warehouse — enabling the incremental filter to check the latest timestamp already loaded', true],
                    ['The dbt project name', false],
                    ['A Jinja variable for the current run timestamp', false],
                ],
            ],
            [
                'q' => "This dbt incremental model will produce incorrect results in a specific scenario. What is it?\n\n{% if is_incremental() %}\nWHERE created_at > (SELECT MAX(created_at) FROM {{ this }})\n{% endif %}",
                'opts' => [
                    ['The model will fail on the first run', false],
                    ['Using created_at instead of updated_at misses UPDATES to existing rows — only new rows are captured. Records updated after their creation date with the same created_at will never be re-processed', true],
                    ['MAX(created_at) returns NULL on an empty table', false],
                    ['There is no issue — created_at and updated_at are interchangeable', false],
                ],
            ],

            // ── DATA VAULT — ADVANCED ─────────────────────────────────────
            [
                'q' => "In Data Vault 2.0, the record source (RSRC) metadata column in every Hub, Link, and Satellite is used for:",
                'opts' => [
                    ['Compressing the record for storage', false],
                    ['Tracking which source system contributed each record — enabling auditability, conflict resolution between sources, and the ability to replay only data from a specific source', true],
                    ['Generating the hash key deterministically', false],
                    ['Indicating whether a record has been quality-checked', false],
                ],
            ],
            [
                'q' => "A Hub_Customer table has:\n  hub_customer_hk (HASH of customer_bk)\n  customer_bk (source business key)\n  load_date\n  record_source\n\nTwo source systems (CRM and ERP) both provide customer IDs but use different formats. How does Data Vault handle this?",
                'opts' => [
                    ['Only one source system can load to a Hub', false],
                    ['Each source system loads its own business key into the same Hub with its own record_source — the Same-As Link (SAL) then resolves which CRM and ERP keys refer to the same real customer', true],
                    ['All business keys are merged into a single composite key', false],
                    ['Source systems must agree on a format before loading', false],
                ],
            ],
            [
                'q' => "What is an 'Effectivity Satellite' in Data Vault?",
                'opts' => [
                    ['A satellite that tracks the historical values of a hub attribute', false],
                    ['A satellite attached to a Link that records the time period during which the relationship between two hubs was active — e.g. when a customer was associated with a specific account', true],
                    ['A satellite used only for current-state queries', false],
                    ['A satellite that stores SCD Type 1 data', false],
                ],
            ],

            // ── COLUMNAR STORAGE & OPTIMISATION — ADVANCED ───────────────
            [
                'q' => "In Snowflake, micro-partitions are automatically created as data is inserted. Each micro-partition contains metadata including min/max values per column. This enables:",
                'opts' => [
                    ['Automatic data encryption per partition', false],
                    ['Micro-partition pruning — queries with WHERE clauses on range conditions can skip entire micro-partitions whose min/max metadata shows they cannot contain relevant rows', true],
                    ['Automatic deduplication within each partition', false],
                    ['Parallel writes across multiple virtual warehouses', false],
                ],
            ],
            [
                'q' => "Snowflake Clustering Keys should be chosen based on:",
                'opts' => [
                    ['Always cluster on the primary key', false],
                    ['The columns most frequently used in WHERE and JOIN clauses on large tables — ideally a low-cardinality column like date that gives good pruning without over-clustering (too many distinct values cause poor pruning)', true],
                    ['The columns with the highest cardinality', false],
                    ['The column with the most NULL values', false],
                ],
            ],
            [
                'q' => "What is the 'spillage to disk' problem in Redshift and how is it fixed?",
                'opts' => [
                    ['When tables grow larger than the disk capacity', false],
                    ['When a query\'s intermediate results exceed available memory — Redshift spills to slower disk storage, dramatically increasing query runtime. Fix: increase node size, reduce query complexity, or use DISTKEY to reduce data movement in JOIN heavy queries', true],
                    ['When VACUUM has not been run for too long', false],
                    ['When too many concurrent queries run simultaneously', false],
                ],
            ],
            [
                'q' => "BigQuery partitioning by INGESTION TIME vs column partitioning — which is better for most analytical workloads and why?",
                'opts' => [
                    ['Ingestion time is always better because it requires no extra columns', false],
                    ['Column partitioning on a business date column (e.g. transaction_date) is usually better — queries filter on business dates, not ingestion timestamps, enabling pruning on the column users actually query', true],
                    ['They are identical in performance', false],
                    ['Ingestion time partitioning prevents data duplication', false],
                ],
            ],

            // ── CLOUD DWH — ADVANCED CODE ─────────────────────────────────
            [
                'q' => "This Snowflake query uses a window function. What does it compute?\n\nSELECT\n    customer_key,\n    order_date,\n    revenue,\n    SUM(revenue) OVER (\n        PARTITION BY customer_key\n        ORDER BY order_date\n        ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW\n    ) AS running_total\nFROM fact_orders;",
                'opts' => [
                    ['Total revenue per customer across all time', false],
                    ['A cumulative (running) sum of revenue per customer ordered by date — each row shows the total revenue for that customer up to and including that order date', true],
                    ['The most recent revenue for each customer', false],
                    ['Revenue ranked by date for each customer', false],
                ],
            ],
            [
                'q' => "What does this BigQuery query compute using a window function?\n\nSELECT\n    product_key,\n    sale_date,\n    revenue,\n    LAG(revenue, 1) OVER (\n        PARTITION BY product_key\n        ORDER BY sale_date\n    ) AS prev_day_revenue,\n    revenue - LAG(revenue, 1) OVER (\n        PARTITION BY product_key\n        ORDER BY sale_date\n    ) AS revenue_change\nFROM fact_daily_sales;",
                'opts' => [
                    ['The 7-day moving average of revenue per product', false],
                    ['For each product, the previous day\'s revenue and the day-over-day change in revenue', true],
                    ['The cumulative revenue change over all time', false],
                    ['The maximum revenue change ever observed', false],
                ],
            ],
            [
                'q' => "This dbt macro has a bug. What is it?\n\n{% macro cents_to_dollars(column_name) %}\n    {{ column_name }} / 100\n{% endmacro %}\n\n-- Used as:\nSELECT {{ cents_to_dollars('revenue_cents') }} AS revenue_dollars",
                'opts' => [
                    ['Macros cannot perform arithmetic', false],
                    ['Integer division truncates in most SQL dialects — 199 / 100 = 1, not 1.99. Should be {{ column_name }} / 100.0 to force float division', true],
                    ['The macro argument should be in double quotes', false],
                    ['Macros cannot reference column names dynamically', false],
                ],
            ],
            [
                'q' => "In Snowflake, the RESULT_SCAN function is used to:",
                'opts' => [
                    ['Scan all result sets from the past 24 hours', false],
                    ['Query the results of a previously executed statement by its query ID — useful for retrieving results without re-running expensive queries', true],
                    ['Scan micro-partition metadata for a table', false],
                    ['Return the execution plan of a query', false],
                ],
            ],

            // ── dbt — ADVANCED ────────────────────────────────────────────
            [
                'q' => "What is a dbt 'generic test' vs a 'singular test'?",
                'opts' => [
                    ['Generic tests run on all models; singular tests run on one model', false],
                    ['Generic tests are parameterised tests defined in YAML applied to columns across many models (e.g. not_null, unique). Singular tests are standalone .sql files that assert a specific condition for a single model', true],
                    ['Generic tests use Python; singular tests use SQL', false],
                    ['They are the same thing with different names', false],
                ],
            ],
            [
                'q' => "What does `dbt compile` do (without running the models)?",
                'opts' => [
                    ['Validates the dbt project YAML configuration files', false],
                    ['Resolves all Jinja templating ({{ ref() }}, {{ config() }}, macros) and generates the final SQL files in the target/ directory without executing them in the warehouse', true],
                    ['Runs only the SQL tests, not the models', false],
                    ['Uploads the dbt project to dbt Cloud', false],
                ],
            ],
            [
                'q' => "In dbt, the `--select` flag with a + modifier:\n  `dbt run --select +fct_revenue`\nmeans:",
                'opts' => [
                    ['Run only fct_revenue and nothing else', false],
                    ['Run fct_revenue AND all of its upstream dependencies (all models that fct_revenue depends on)', true],
                    ['Run fct_revenue AND all downstream models that depend on it', false],
                    ['Run all models except fct_revenue', false],
                ],
            ],
            [
                'q' => "What is a dbt 'exposure' used for?",
                'opts' => [
                    ['Documenting data breach incidents', false],
                    ['Declaring downstream consumers of dbt models (dashboards, ML models, APIs) in the project — enabling lineage visibility from raw source to final business consumption', true],
                    ['Marking sensitive columns for masking', false],
                    ['Configuring model freshness SLAs', false],
                ],
            ],
            [
                'q' => "This dbt incremental model has a subtle performance problem. What is it?\n\n{{ config(materialized='incremental', unique_key='order_id') }}\n\nSELECT *\nFROM {{ ref('stg_orders') }}\n{% if is_incremental() %}\nWHERE updated_at > (SELECT MAX(updated_at) FROM {{ this }})\n{% endif %}",
                'opts' => [
                    ['SELECT * is not allowed in dbt incremental models', false],
                    ['The subquery SELECT MAX(updated_at) FROM {{ this }} runs a full table scan on the target table on every incremental run — on a table with billions of rows this is expensive. Fix: store the watermark in a dedicated metadata table or use dbt\'s built-in incremental predicates', true],
                    ['The is_incremental() macro is deprecated', false],
                    ['unique_key requires a composite key', false],
                ],
            ],

            // ── DATA QUALITY & GOVERNANCE — ADVANCED ─────────────────────
            [
                'q' => "You discover that fact_sales has 0.3% of rows with order_amount < 0. Which action is correct?",
                'opts' => [
                    ['Delete all negative rows immediately', false],
                    ['Investigate the root cause first — negative amounts may be legitimate (refunds, returns, adjustments) and should be modelled explicitly rather than deleted', true],
                    ['Replace negative values with NULL', false],
                    ['Increase the data quality threshold to accept them', false],
                ],
            ],
            [
                'q' => "What is 'schema drift' in a data warehouse context and why is it dangerous?",
                'opts' => [
                    ['When the warehouse runs out of disk space', false],
                    ['When a source system changes its table structure (adds, removes, or renames columns) unexpectedly — this can silently break downstream ETL/ELT pipelines and dbt models, causing data loss or incorrect data without immediate errors', true],
                    ['When queries take longer than expected', false],
                    ['When two schemas have the same table names', false],
                ],
            ],
            [
                'q' => "In dbt, the `on_schema_change` config for incremental models handles schema drift by allowing options like:",
                'opts' => [
                    ['ignore, fail, append_new_columns, sync_all_columns', true],
                    ['overwrite, merge, append, delete', false],
                    ['strict, loose, permissive, auto', false],
                    ['freeze, update, rebuild, skip', false],
                ],
            ],
            [
                'q' => "Row-level security (RLS) in Snowflake is implemented using:",
                'opts' => [
                    ['A WHERE clause added manually to every query', false],
                    ['Row Access Policies — policy functions attached to tables that transparently filter rows based on the querying user\'s role or attributes without requiring application-level changes', true],
                    ['Column masking policies applied to row identifiers', false],
                    ['Virtual Private Database (VPD) equivalent to Oracle', false],
                ],
            ],
            [
                'q' => "Dynamic Data Masking in Snowflake allows you to:",
                'opts' => [
                    ['Encrypt entire tables at rest', false],
                    ['Show full sensitive data to privileged roles while automatically masking it (e.g. showing XXX-XX-1234 instead of a full SSN) for unprivileged roles — at query time, transparently', true],
                    ['Permanently delete sensitive data from the warehouse', false],
                    ['Hash all column values before storage', false],
                ],
            ],

            // ── WINDOW FUNCTIONS & ANALYTICAL SQL ────────────────────────
            [
                'q' => "What is the difference between ROW_NUMBER(), RANK(), and DENSE_RANK() when there are ties?",
                'opts' => [
                    ['They are identical for all inputs', false],
                    ['ROW_NUMBER() assigns unique sequential numbers regardless of ties. RANK() skips ranks after ties (1,1,3). DENSE_RANK() does not skip ranks after ties (1,1,2)', true],
                    ['RANK() and DENSE_RANK() are identical; ROW_NUMBER() differs', false],
                    ['Only ROW_NUMBER() works with ORDER BY', false],
                ],
            ],
            [
                'q' => "This query identifies the top-spending customer per region. What is the output structure?\n\nSELECT *\nFROM (\n    SELECT\n        c.region,\n        c.customer_name,\n        SUM(f.revenue) AS total_spend,\n        ROW_NUMBER() OVER (\n            PARTITION BY c.region\n            ORDER BY SUM(f.revenue) DESC\n        ) AS rn\n    FROM fact_sales f\n    JOIN dim_customer c ON f.customer_key = c.customer_key\n    GROUP BY c.region, c.customer_name\n) ranked\nWHERE rn = 1;",
                'opts' => [
                    ['All customers sorted by total spend descending', false],
                    ['One row per region — the customer with the highest total spend in each region', true],
                    ['All customers with total spend above the regional average', false],
                    ['The top 1 customer globally across all regions', false],
                ],
            ],
            [
                'q' => "This Redshift query is slower than expected on 10 billion rows. What is the primary fix?\n\nSELECT\n    DATE_TRUNC('month', sale_date) AS month,\n    category,\n    SUM(revenue)\nFROM fact_sales\nJOIN dim_product USING (product_key)\nGROUP BY 1, 2;\n\n-- fact_sales DISTKEY = customer_key\n-- dim_product DISTKEY = AUTO",
                'opts' => [
                    ['Add a LIMIT clause', false],
                    ['The DISTKEY mismatch forces a full data redistribution during the JOIN — change fact_sales DISTKEY to product_key to co-locate matching rows with dim_product on the same nodes', true],
                    ['Use ILIKE instead of USING for joins', false],
                    ['Replace SUM with COUNT for performance', false],
                ],
            ],
            [
                'q' => "What is a 'materialised view' in the context of data warehousing, and how does it differ from a standard view?",
                'opts' => [
                    ['A materialised view is identical to a regular view but stored in a different schema', false],
                    ['A materialised view physically stores the pre-computed query results on disk and can be refreshed periodically — unlike a regular view which re-executes the underlying query on every access', true],
                    ['A materialised view cannot contain aggregations', false],
                    ['A materialised view is only available in Snowflake', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 23 — Data Warehousing (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}