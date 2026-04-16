<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module23ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Data Warehousing')
                 ->delete();

        $this->command->info("Creating Module 23 — Data Warehousing (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Warehousing',
            'description'           => 'Professional-grade data warehousing problems: production pipeline failures, warehouse cost governance, multi-source data conflicts, real-time warehouse architectures, dbt at scale, compliance engineering, and data mesh design. For working data engineers, warehouse architects, and analytics engineers.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 2000,
            'order_index'           => 23,
        ]);

        $this->command->info("Seeding 50 professional-level Data Warehousing questions...");

        $qaData = [

            // ── PRODUCTION PIPELINE FAILURES ──────────────────────────────
            [
                'q' => "A nightly dbt production run fails at 3AM with:\n'Compilation Error: object 'analytics.stg_orders' does not exist'\n\nThe table existed yesterday. What is the most likely cause and correct response?",
                'opts' => [
                    ['dbt has a bug in this version', false],
                    ['The upstream ELT tool (Fivetran/Airbyte) failed to load the source data, leaving stg_orders empty or dropped. Response: add source freshness checks BEFORE dbt runs, alert on ELT failures, and implement a circuit-breaker that prevents dbt from running if critical sources are stale', true],
                    ['The Snowflake warehouse was suspended', false],
                    ['The dbt project YAML is misconfigured', false],
                ],
            ],
            [
                'q' => "A fact table in production accumulates 5 billion rows/month. Monthly Snowflake compute costs have tripled in 3 months. The most impactful engineering action is:",
                'opts' => [
                    ['Upgrade to a larger Snowflake virtual warehouse', false],
                    ['Audit query patterns and implement aggressive micro-partition pruning — add clustering keys on the most-queried columns (e.g. event_date), review and fix queries that do full table scans, and partition large tables to reduce bytes scanned per query', true],
                    ['Move all data to S3 and query with Athena', false],
                    ['Delete data older than 6 months', false],
                ],
            ],
            [
                'q' => "A financial reporting pipeline loads daily revenue. The business reports a $2.3M discrepancy between the warehouse total and the source ERP system. The investigation checklist should start with:",
                'opts' => [
                    ['Immediately rollback the last 3 months of data', false],
                    ['Row count and SUM reconciliation at each pipeline stage — compare source extract → staging → final mart. Also check for timezone issues, double-loading of incremental batches, NULL exclusions in SUM, and currency conversion bugs', true],
                    ['Check if Snowflake had any downtime', false],
                    ['Re-run the pipeline and see if the number changes', false],
                ],
            ],
            [
                'q' => "An incremental dbt model that uses `unique_key` with the merge strategy runs for 4 hours on Snowflake. The model processes 500M rows. The root cause is:",
                'opts' => [
                    ['Snowflake cannot handle 500M row merges', false],
                    ['The MERGE statement performs a full join between the incoming data and the target table to find matches — on 500M rows this is extremely expensive. Fix: pre-filter the incremental batch to only the relevant date partition before the merge, drastically reducing the rows scanned in the target', true],
                    ['unique_key cannot be a non-integer column', false],
                    ['The dbt version is incompatible with Snowflake', false],
                ],
            ],
            [
                'q' => "A Redshift cluster shows consistently high disk queue depth and high CPU during business hours. EXPLAIN plan shows multiple full table scans on a 2TB fact table. The correct resolution path is:",
                'opts' => [
                    ['Increase the cluster to RA3.16xlarge immediately', false],
                    ['Analyse query patterns: add SORTKEY and DISTKEY aligned to the most common JOIN/WHERE columns, run VACUUM SORT ONLY to apply sorting, use workload management (WLM) queues to separate long analytical queries from short dashboard queries, and consider query result caching', true],
                    ['Move the cluster to a different AWS region', false],
                    ['Disable all concurrent queries to reduce load', false],
                ],
            ],

            // ── WAREHOUSE COST GOVERNANCE ──────────────────────────────────
            [
                'q' => "A Snowflake account bill spikes to 10x normal in one weekend. Query history shows a rogue dbt `--full-refresh` was run on all 300 models. The preventive control is:",
                'opts' => [
                    ['Remove the --full-refresh flag from dbt', false],
                    ['Implement a Snowflake Resource Monitor with a credit quota and auto-suspend policy, restrict --full-refresh in CI/CD pipelines to production deployments only, and require peer review for full-refresh runs on large models', true],
                    ['Set all virtual warehouses to X-Small', false],
                    ['Disable the Snowflake account on weekends', false],
                ],
            ],
            [
                'q' => "BigQuery on-demand pricing charges per bytes scanned. A dashboard query runs 1,000 times/day scanning 500GB each time. The cost-optimal architecture is:",
                'opts' => [
                    ['Cache the query result in the application layer', false],
                    ['Switch to BigQuery flat-rate (slots) pricing for predictable high-volume workloads, materialise the query result as a scheduled query into a smaller summary table that the dashboard reads instead, and add column partitioning to minimise bytes scanned', true],
                    ['Reduce the dashboard refresh rate to once per day', false],
                    ['Migrate to Redshift to avoid per-scan charges', false],
                ],
            ],
            [
                'q' => "In Snowflake, what is the financial risk of setting a virtual warehouse AUTO_SUSPEND to 3600 seconds (1 hour) instead of 60 seconds?",
                'opts' => [
                    ['The warehouse will timeout active queries', false],
                    ['The warehouse continues consuming credits even when idle — at $4/credit for an XL warehouse running 16 credits/hour, a warehouse idle for 1 hour instead of 1 minute wastes ~$60/occurrence. For hundreds of warehouses this becomes significant daily waste', true],
                    ['Queries will queue instead of running immediately', false],
                    ['Snowflake does not support AUTO_SUSPEND values over 300 seconds', false],
                ],
            ],

            // ── MULTI-SOURCE CONFLICTS & DATA INTEGRATION ─────────────────
            [
                'q' => "CRM reports 42,500 active customers. ERP reports 38,200. Finance says 40,100. All three source systems feed the data warehouse. The correct engineering approach is:",
                'opts' => [
                    ['Use the highest number and document it', false],
                    ['Investigate and document the business rules that define an "active customer" per system (last purchase date cutoff, contract status, billing status) — build a single canonical definition agreed with stakeholders, implement it in a gold-layer dbt model, and publish a data dictionary explaining the discrepancy and chosen definition', true],
                    ['Average the three numbers', false],
                    ['Only trust the CRM as it is the system of record', false],
                ],
            ],
            [
                'q' => "Two source systems provide customer records. After loading:\n- CRM has customer_id = 'C-1001' with email = 'john@old.com'\n- ERP has account_id = 'ACC-8823' with email = 'john@new.com'\n\nThey are the same person. In Data Vault, this is resolved using:",
                'opts' => [
                    ['Overwriting the CRM record with the ERP record', false],
                    ['A Same-As Link connecting Hub_Customer (C-1001 hash) and Hub_Account (ACC-8823 hash) — each source\'s data is preserved in its own satellite, and the link asserts the identity match. A Business Vault view can then surface the most current email', true],
                    ['Creating a new combined record in the Hub', false],
                    ['Deleting the record with the older email', false],
                ],
            ],
            [
                'q' => "A real-time streaming pipeline (Kafka → Snowflake) and a nightly batch ETL both write to the same fact table. Users report duplicates. The production solution is:",
                'opts' => [
                    ['Disable the streaming pipeline', false],
                    ['Implement idempotent loading — use a MERGE with a unique_key (event_id) for stream inserts, partition the table to separate streaming writes from batch writes, and reconcile daily with a deduplication model that preferentially uses batch-validated records over stream records', true],
                    ['Add a DISTINCT to every downstream query', false],
                    ['Truncate and reload the fact table daily', false],
                ],
            ],

            // ── REAL-TIME & LAMBDA ARCHITECTURE ──────────────────────────
            [
                'q' => "The Lambda Architecture for a data warehouse combines:",
                'opts' => [
                    ['Python and SQL processing layers', false],
                    ['A batch layer (accurate historical processing), a speed layer (real-time approximate processing for recent data), and a serving layer that merges both — providing both low latency and accuracy', true],
                    ['On-premise and cloud components only', false],
                    ['Structured and unstructured data stores', false],
                ],
            ],
            [
                'q' => "The Kappa Architecture simplifies Lambda by:",
                'opts' => [
                    ['Removing the speed layer', false],
                    ['Eliminating the batch layer — processing ALL data (historical and real-time) through a single streaming pipeline (e.g. Kafka + Flink), reprocessing historical data by replaying the event log when logic changes', true],
                    ['Using only SQL, no streaming frameworks', false],
                    ['Replacing the serving layer with a data lake', false],
                ],
            ],
            [
                'q' => "A warehouse team implements the 'Medallion Architecture' (Bronze → Silver → Gold). What does each layer represent?",
                'opts' => [
                    ['Raw data, cleaned data, aggregated data', false],
                    ['Bronze: raw ingested data as-is. Silver: cleaned, validated, deduplicated, joined data. Gold: business-level aggregated, dimensional models or metrics ready for consumption by BI and ML teams', true],
                    ['Source, staging, and reporting databases', false],
                    ['Development, staging, and production environments', false],
                ],
            ],
            [
                'q' => "Snowflake Dynamic Tables (introduced 2023) differ from materialised views because they:",
                'opts' => [
                    ['Dynamic Tables are read-only; materialised views are writable', false],
                    ['Dynamic Tables automatically track their dependencies and refresh incrementally when upstream data changes, supporting pipeline-like declarative SQL transformations without explicit scheduling — closer to a streaming materialised view', true],
                    ['Dynamic Tables use Python instead of SQL', false],
                    ['Dynamic Tables only work on external stages', false],
                ],
            ],

            // ── dbt AT SCALE ──────────────────────────────────────────────
            [
                'q' => "A dbt project has grown to 800 models and CI/CD runs take 90 minutes, blocking deploys. The correct engineering approach is:",
                'opts' => [
                    ['Increase the CI/CD server size', false],
                    ['Implement dbt state comparison with --select state:modified+ to run only changed models and their downstream dependencies. Use slim CI (clone production state, run only affected models), split models into separate dbt projects by domain, and parallelize model execution', true],
                    ['Remove dbt tests to speed up the run', false],
                    ['Run all models at 3AM to avoid delays', false],
                ],
            ],
            [
                'q' => "What is a dbt 'metric' (dbt Semantic Layer) and how does it differ from a regular dbt model?",
                'opts' => [
                    ['A metric is a dbt model that only returns one number', false],
                    ['A dbt metric is a YAML-defined business metric (e.g. monthly_revenue, active_users) that can be queried dynamically across any dimension — decoupling metric logic from pre-built aggregation tables and enabling consistent self-service querying via the Semantic Layer API', true],
                    ['Metrics are Python-based dbt models', false],
                    ['A metric automatically creates a dashboard in BI tools', false],
                ],
            ],
            [
                'q' => "In a large dbt project, cross-project `dbt-meshes` solve which problem?",
                'opts' => [
                    ['Running multiple dbt versions simultaneously', false],
                    ['Domain-based project separation — teams own their own dbt projects with explicit public interfaces (published models), while downstream teams consume these as dependencies via cross-project ref(), enabling decentralised ownership without monorepo complexity', true],
                    ['Connecting dbt to multiple data warehouses', false],
                    ['Running dbt models in parallel across Kubernetes pods', false],
                ],
            ],
            [
                'q' => "What is dbt's `--defer` flag used for in development?",
                'opts' => [
                    ['Deferring model runs to off-peak hours', false],
                    ['Running a model in development against the PRODUCTION versions of its upstream dependencies (from a production state artifact) — avoiding the need to rebuild the entire upstream chain in a dev environment, saving compute and time', true],
                    ['Delaying failed model retries by N minutes', false],
                    ['Skipping tests during a development run', false],
                ],
            ],

            // ── DATA MESH ARCHITECTURE ────────────────────────────────────
            [
                'q' => "The Data Mesh paradigm shifts data ownership from centralised data teams to:",
                'opts' => [
                    ['The cloud provider managing the infrastructure', false],
                    ['Domain teams who own, build, and operate their own data products — applying product thinking to data, with a federated governance model and a self-serve data infrastructure platform', true],
                    ['Individual data scientists who need the data', false],
                    ['A single global data governance committee', false],
                ],
            ],
            [
                'q' => "In Data Mesh, a 'data product' must satisfy which key qualities?",
                'opts' => [
                    ['It must be written in SQL and deployed to Snowflake', false],
                    ['Discoverable, addressable, trustworthy, self-describing, interoperable, natively accessible, and governed — each data product exposes a defined interface and SLA for consumers', true],
                    ['It must be owned by the central data engineering team', false],
                    ['It must be updated in real-time at all times', false],
                ],
            ],
            [
                'q' => "The main tension in a Data Mesh implementation is between:",
                'opts' => [
                    ['SQL vs NoSQL choice per domain', false],
                    ['Domain autonomy (teams choose their own tools and models) vs interoperability (cross-domain queries need consistent standards, conformed dimensions, and shared data contracts)', true],
                    ['Batch vs streaming processing preferences', false],
                    ['Cloud vs on-premise infrastructure', false],
                ],
            ],

            // ── COMPLIANCE & GOVERNANCE AT SCALE ──────────────────────────
            [
                'q' => "Your warehouse holds PII from EU citizens. GDPR Article 17 'Right to Erasure' requires deletion within 30 days of request. With 5 years of SCD Type 2 history and fact tables referencing those dimension keys, what is the compliant engineering approach?",
                'opts' => [
                    ['Delete only the most recent dimension row', false],
                    ['Implement a pseudonymisation / tokenisation strategy at ingestion — replace PII with a token. On erasure request, delete the token mapping table entry. All historical rows become unidentifiable without re-running the warehouse. Combine with a data purge pipeline for any residual PII in staging and raw layers', true],
                    ['Archive all data to Glacier and mark as deleted', false],
                    ['Data warehouses are exempt from Article 17', false],
                ],
            ],
            [
                'q' => "SOC 2 Type II compliance for a data warehouse requires evidence of:",
                'opts' => [
                    ['Only encrypting data at rest', false],
                    ['Continuous operational controls over a period of time — access control reviews, audit logs of who accessed what data, change management processes, availability monitoring, and incident response records. Type II covers a 6-12 month period, not a point-in-time snapshot (SOC 2 Type I)', true],
                    ['Having a privacy policy publicly available', false],
                    ['Using a SOC 2 certified cloud provider', false],
                ],
            ],
            [
                'q' => "Column masking in Snowflake is applied at which layer?",
                'opts' => [
                    ['At the ETL/ELT ingestion layer before data reaches the warehouse', false],
                    ['At the query layer — the masking policy is attached to the column and applied transparently at query time based on the user\'s role, so the underlying data remains intact but is masked in results for unauthorised roles', true],
                    ['At the storage layer — data is physically masked on disk', false],
                    ['In the BI tool, not in the warehouse itself', false],
                ],
            ],

            // ── ADVANCED SCHEMA & MODELLING AT SCALE ─────────────────────
            [
                'q' => "A retail warehouse has a product dimension with 10 million SKUs. Typical dimensional modelling creates a flat dim_product. At this scale, which approach is better and why?",
                'opts' => [
                    ['Use a Star Schema regardless of size', false],
                    ['Use a Snowflake Schema — normalise product hierarchy (brand → category → sub-category) into separate tables. At 10M SKUs, the fully denormalised dim_product is extremely wide and wastes storage on repeated hierarchy values. Snowflake schema trades JOIN complexity for significant storage and maintenance efficiency', true],
                    ['Store all 10M rows in a JSON column', false],
                    ['Partition dim_product by product_key range', false],
                ],
            ],
            [
                'q' => "An 'outrigger' table in dimensional modelling is a dimension that references another dimension. For example, dim_store references dim_geography. This is considered an anti-pattern because:",
                'opts' => [
                    ['Foreign keys between dimensions are not allowed in SQL', false],
                    ['It effectively creates a Snowflake Schema in a Star Schema — BI tools that expect a pure star schema may have trouble navigating the outrigger, and it adds JOIN complexity. Better to denormalise geography attributes into dim_store or create a separate role-playing dimension', true],
                    ['dim_geography would have too many rows', false],
                    ['Outriggers cause circular foreign key references', false],
                ],
            ],
            [
                'q' => "A 'periodic snapshot fact table' differs from a 'transaction fact table' in that:",
                'opts' => [
                    ['Periodic snapshots are smaller than transaction tables', false],
                    ['A periodic snapshot captures the state of a process at regular intervals (e.g. end-of-day account balance) regardless of activity — rows exist even for periods with no transactions, making it ideal for semi-additive facts over time', true],
                    ['Transaction fact tables cannot contain dates', false],
                    ['Periodic snapshots cannot be partitioned', false],
                ],
            ],
            [
                'q' => "An 'accumulating snapshot fact table' is used to model:",
                'opts' => [
                    ['Daily revenue snapshots', false],
                    ['Business processes with a defined workflow and multiple milestones — e.g. an order that moves through stages (placed → approved → shipped → delivered). Each order has ONE row that is UPDATED as milestones are reached, capturing milestone dates and durations', true],
                    ['Multi-valued dimension relationships', false],
                    ['Slowly changing facts like product prices', false],
                ],
            ],

            // ── REAL-WORLD SYSTEM DESIGN ──────────────────────────────────
            [
                'q' => "You are designing a warehouse for a streaming platform (Netflix-style). Users generate 50 billion events/day. The business needs both real-time dashboards (< 5 min latency) and historical trend analysis (3+ years). The correct architecture is:",
                'opts' => [
                    ['Load all 50B events directly into Snowflake', false],
                    ['Kappa/Lambda hybrid: stream events via Kafka → Flink for real-time aggregations into a serving layer (Redis/DynamoDB) for live dashboards; batch compact Kafka events hourly into Parquet on S3 → load into Snowflake/BigQuery for historical analysis. Use a separate summary table layer for trend reports', true],
                    ['Use a single PostgreSQL database with read replicas', false],
                    ['Process everything with Spark batch jobs overnight', false],
                ],
            ],
            [
                'q' => "A bank needs a data warehouse that maintains a complete, immutable audit trail of every change made to every record, with full source traceability, and must handle schema changes from 15 source systems over 10 years without breaking historical data. The best modelling approach is:",
                'opts' => [
                    ['Star Schema with aggressive denormalisation', false],
                    ['Data Vault 2.0 — the Raw Vault provides insert-only, auditable, source-stamped storage that is immune to schema changes (new satellites can be added without touching existing ones), and the Business Vault provides the business interpretation layer', true],
                    ['Third Normal Form (3NF) enterprise data warehouse', false],
                    ['Delta Lake with ACID transactions', false],
                ],
            ],
            [
                'q' => "You are tasked with migrating a 200TB on-premise Teradata warehouse to Snowflake. The most critical risk to manage during migration is:",
                'opts' => [
                    ['Network bandwidth for the data transfer', false],
                    ['SQL dialect differences — Teradata SQL, functions (TD_NORMALIZE_OVERLAPS, COMPRESS, etc.), BTEQ scripts, and procedural code (stored procedures) require translation. Business logic embedded in Teradata-specific syntax must be validated against Snowflake outputs to ensure identical results before cutover', true],
                    ['Snowflake account provisioning time', false],
                    ['Training the team on the Snowflake UI', false],
                ],
            ],
            [
                'q' => "A data warehouse serves 500 concurrent BI users. Query performance degrades during peak hours (9AM–11AM). The Snowflake multi-cluster warehouse feature solves this by:",
                'opts' => [
                    ['Running queries faster on a single large cluster', false],
                    ['Automatically spinning up additional compute clusters when queuing occurs — distributing concurrent queries across multiple clusters without user-visible latency degradation. Each cluster is independent but shares the same storage, enabling true scale-out concurrency', true],
                    ['Caching all query results during peak hours', false],
                    ['Prioritising long-running queries over short ones', false],
                ],
            ],
            [
                'q' => "What is a 'data contract' in modern data engineering and why is it important for warehouse reliability?",
                'opts' => [
                    ['A legal document signed by data vendors', false],
                    ['A formal, versioned agreement between a data producer (e.g. a microservice) and a data consumer (e.g. the warehouse ETL) specifying the schema, data types, semantics, SLAs, and quality expectations. Breaking changes require explicit versioning — preventing silent schema drift from breaking downstream pipelines', true],
                    ['A dbt test that validates row counts', false],
                    ['A Snowflake GRANT statement for table access', false],
                ],
            ],
            [
                'q' => "An analytics engineer notices that the same business metric 'Monthly Active Users' is calculated differently in 3 separate dbt marts, causing conflicting numbers in different dashboards. The long-term architectural fix is:",
                'opts' => [
                    ['Delete two of the three marts', false],
                    ['Implement a Semantic Layer (dbt Metrics, Cube.dev, or LookML) — define MAU as a single canonical metric with agreed business logic, expose it via a semantic API, and have all BI tools query the metric layer rather than raw SQL, eliminating inconsistent calculations across teams', true],
                    ['Rename the metrics to make them distinct', false],
                    ['Lock down mart table access to one team only', false],
                ],
            ],
            [
                'q' => "What is the 'thin ETL, fat warehouse' philosophy in modern data engineering?",
                'opts' => [
                    ['Use lightweight servers for ETL and large servers for the warehouse', false],
                    ['Extract and load raw data with minimal transformations into a powerful cloud warehouse (thin ETL), then perform all heavy transformations inside the warehouse using SQL/dbt (fat warehouse) — leveraging the warehouse\'s optimised compute rather than a separate ETL server', true],
                    ['Store raw data in a thin format like Parquet', false],
                    ['Keep the ETL pipeline simple and the warehouse complex', false],
                ],
            ],
            [
                'q' => "A data platform team is evaluating whether to use Iceberg tables (on S3) queried by Trino/Athena vs a managed cloud warehouse (Snowflake). The key decision factor favouring Iceberg is:",
                'opts' => [
                    ['Iceberg is always cheaper than Snowflake', false],
                    ['Iceberg provides an open table format with no vendor lock-in — multiple engines (Spark, Trino, Flink, Dremio) can read/write the same tables, ACID transactions are supported, and the organisation owns its data storage. Trade-off: more operational overhead vs Snowflake\'s fully managed simplicity', true],
                    ['Iceberg supports larger table sizes than Snowflake', false],
                    ['Iceberg automatically optimises query performance', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 23 — Data Warehousing (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}