<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module22ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 22 — Big Data & Cloud Computing (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Big Data & Cloud Computing',
            'description'           => 'Professional-grade Big Data and Cloud challenges grounded in petabyte-scale system design, cost optimisation at enterprise scale, regulatory compliance edge cases, multi-engine data architecture, and production incident response across the full modern data stack.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 3000,
            'order_index'           => 22,
        ]);

        $this->command->info("Seeding 50 professional-level questions on Big Data & Cloud Computing...");

        $qaData = [

            // ── 22.1 BIG DATA & THE 5 Vs ─────────────────────────────────
            [
                'q' => "A global e-commerce platform processes:\n• 2 billion daily events (clickstream, purchases, returns)\n• 150+ event schema versions in production simultaneously\n• 40% late-arriving events (up to 72 hours delayed)\n• Events from 85 countries with different time zones and currencies\n\nWhich of the 5 Vs is MOST technically challenging to address, and what is the professional architectural response?",
                'opts' => [
                    ['Volume — solved by adding more Kafka partitions', false],
                    ['Variety (150+ schema versions) — solved by implementing a Schema Registry (e.g. Confluent Schema Registry with Avro/Protobuf) for schema evolution with backwards/forwards compatibility, combined with Velocity (40% late data) addressed via event-time watermarking with a 72-hour watermark in Flink/Spark Structured Streaming', true],
                    ['Veracity — all events should be rejected until they conform to one schema', false],
                    ['Value — the business has not defined KPIs yet', false],
                ],
            ],
            [
                'q' => "A data team must design a system to answer: 'How many unique users visited any page in the last 30 minutes?' across 10 million events/minute. Exact counting requires storing all user IDs.\n\nCalculate the memory required for exact unique counting vs. HyperLogLog (HLL) at 2% error:\n• Exact: 10M events/min × 30 min × 8 bytes/UUID = ?\n• HLL: ~12 KB per counter",
                'opts' => [
                    ['Exact: 2.4 GB; HLL: 12 KB — HLL provides a ~200,000x memory reduction at 2% error, making real-time cardinality estimation feasible at scale', true],
                    ['Exact: 240 MB; HLL: 12 KB', false],
                    ['Exact: 24 GB; HLL: 1.2 MB', false],
                    ['Exact: 2.4 TB; HLL cannot be used for sliding windows', false],
                ],
            ],
            [
                'q' => "A fintech company must retain raw transaction data for 7 years (regulatory requirement) but only queries data older than 2 years for audits. Design the most cost-effective tiered storage strategy on AWS S3.\n\nA) Hot (S3 Standard) for all 7 years\nB) Hot (S3 Standard) for 0-90 days → Warm (S3 Standard-IA) for 90 days-2 years → Cold (S3 Glacier) for 2-7 years, using S3 Lifecycle policies for automatic transitions\nC) All data in S3 Glacier Instant Retrieval from day 1\nD) Delete data after 2 years to comply with GDPR right to erasure",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.2 CLOUD COMPUTING ──────────────────────────────────────
            [
                'q' => "A company's AWS bill for data processing is $180K/month. Analysis shows:\n• EMR Spark clusters: $120K (run 24/7, 60% idle at night)\n• RDS Aurora: $30K (steady 24/7 OLTP)\n• EC2 on-demand for ML training: $30K (used only 40 hrs/month)\n\nWhat is the optimal cost reduction strategy for each component?",
                'opts' => [
                    ['Migrate all workloads to Google Cloud for a 30% discount', false],
                    ['EMR: use auto-scaling + spot instances for batch jobs (shut down at night) → estimated $48K; RDS Aurora: reserved instance 1-year → estimated $19.5K; EC2 ML: spot instances for training → estimated $10.5K. Total: ~$78K/month (57% reduction)', true],
                    ['Replace EMR with a serverless Lambda function to eliminate the cluster cost entirely', false],
                    ['Increase RDS instance size to reduce query time and lower EMR processing hours', false],
                ],
            ],
            [
                'q' => "A multi-cloud architecture runs data workloads across AWS and GCP. The team incurs $45K/month in cross-cloud data transfer costs (egress). What architectural pattern eliminates most of this cost?\n\nA) Replicate all data to both clouds and run workloads locally on each cloud\nB) Adopt a data mesh architecture with each domain owning its data in its preferred cloud, exposing only aggregated API results (not raw data) across clouds — minimises raw data transfer\nC) Use a single cloud provider for all workloads (cloud consolidation)\nD) Compress all data before transfer to reduce bytes",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A production Spark EMR cluster fails with: 'Container killed on request. Exit code is 137.' The Spark UI shows executors being killed repeatedly during the shuffle phase of a 500 GB join.\n\nWhat is exit code 137, what causes it in this context, and what is the fix?",
                'opts' => [
                    ['Exit 137 = disk full. Fix: add more EBS storage to executor nodes', false],
                    ['Exit 137 = OOM kill (Out of Memory). The shuffle is materialising 500 GB of join data in executor memory. Fix: increase executor memory (spark.executor.memory), enable off-heap memory (spark.memory.offHeap), use sort-merge join with disk spill instead of hash join, or pre-aggregate/filter data before the join', true],
                    ['Exit 137 = network timeout between driver and executors', false],
                    ['Exit 137 = Python interpreter crash in a UDF', false],
                ],
            ],

            // ── 22.3 DISTRIBUTED STORAGE ──────────────────────────────────
            [
                'q' => "A petabyte-scale data lake uses Apache Iceberg. A CDC pipeline appends 50 million rows/day to an events table. After 6 months:\n• Table has 9 billion rows\n• 180,000 Parquet data files\n• Metadata JSON has grown to 8 GB\n• Query planning takes 45 seconds (just reading metadata)\n\nWhat is the root cause and the correct resolution?",
                'opts' => [
                    ['9 billion rows is too many for Iceberg — migrate to Delta Lake', false],
                    ['The small file explosion (180K files) combined with unbounded metadata growth is the root cause. Resolution: (1) run Iceberg table compaction (rewriteDataFiles) to merge small files, (2) expire old snapshots to truncate metadata, (3) rewrite position delete files, (4) switch CDC to micro-batch with larger batch sizes to reduce daily file count from thousands to dozens', true],
                    ['Query planning slowness is a BigQuery limitation — Iceberg metadata is always fast', false],
                    ['The metadata size is normal for a 9B row table — add more metadata nodes', false],
                ],
            ],
            [
                'q' => "The Z-ordering optimisation in Delta Lake achieves data skipping by co-locating correlated data. If a Delta table has:\n• 1000 data files\n• A query filters on user_id = 'U12345'\n• Without Z-ORDER: 1000 files scanned\n• After ZORDER BY (user_id): 8 files scanned\n\nCalculate the data skipping ratio and explain why perfect 1-file skipping is impossible.",
                'opts' => [
                    ['Skipping ratio = 992/1000 = 99.2%. Perfect 1-file skipping is impossible because Z-ORDER uses a space-filling curve (Hilbert curve) that maps multi-dimensional data to 1D, inherently placing multiple users in the same file — it optimises locality but cannot isolate a single user_id to one file without partitioning', true],
                    ['Skipping ratio = 8/1000 = 0.8% — Z-ORDER is ineffective', false],
                    ['Perfect 1-file skipping is achievable with a high enough n_bins parameter', false],
                    ['The 8 files scanned means Z-ORDER increased I/O by 8x', false],
                ],
            ],
            [
                'q' => "A data engineering team evaluates three lakehouse table formats for a new platform:\n• Delta Lake (Databricks)\n• Apache Iceberg (open standard)\n• Apache Hudi (Uber)\n\nTheir requirements:\n1. Multi-engine reads: Spark, Trino, Flink, and Snowflake\n2. Row-level deletes at scale (GDPR erasure)\n3. Time travel for 90-day data audits\n4. Open spec (no vendor lock-in)\n\nWhich format best satisfies ALL requirements?",
                'opts' => [
                    ['Delta Lake — best for Spark workloads and Databricks ecosystem', false],
                    ['Apache Iceberg — open specification natively supported by all listed engines; supports row-level deletes via position/equality delete files; time travel via snapshot history; no vendor dependency', true],
                    ['Apache Hudi — best known for upsert performance at Uber scale', false],
                    ['All three are equivalent for these requirements', false],
                ],
            ],

            // ── 22.4 APACHE SPARK ─────────────────────────────────────────
            [
                'q' => "A Spark Structured Streaming job processes 10M events/hour. The team observes that every 6 hours, processing latency spikes from 2 seconds to 4 minutes for one micro-batch. Investigation shows the spike corresponds exactly to a Delta Lake OPTIMIZE job running on the source table.\n\nWhat is the root cause and fix?",
                'opts' => [
                    ['Delta Lake OPTIMIZE conflicts with Spark Streaming reads — use Parquet instead', false],
                    ['OPTIMIZE rewrites and commits thousands of new files in one transaction — when the streaming job reads this commit, it must process the metadata for thousands of file additions/removals in a single batch, overwhelming the driver. Fix: run OPTIMIZE with a narrower ZORDER scope, schedule it during low-traffic periods, and use rate limiting (maxFilesPerTrigger) in the streaming reader', true],
                    ['The spike is unrelated to OPTIMIZE — it is a GC pause in the JVM', false],
                    ['OPTIMIZE should never be run on tables read by streaming jobs', false],
                ],
            ],
            [
                'q' => "The following Spark UDF causes dramatic performance degradation on a 500M-row dataset:\n\nfrom pyspark.sql import functions as F\nfrom pyspark.sql.types import DoubleType\n\n@F.udf(returnType=DoubleType())\ndef complex_calc(x, y, z):\n    import numpy as np\n    return float(np.sqrt(x**2 + y**2 + z**2))\n\ndf.withColumn('magnitude', complex_calc('x', 'y', 'z'))\n\nWhat are the TWO main performance issues and how are both fixed?",
                'opts' => [
                    ['Python UDFs serialize/deserialize each row via Python-JVM pickle (row-by-row overhead), and numpy is imported inside the UDF (re-imported for every row). Fix: (1) replace with native Spark SQL functions (F.sqrt(F.pow(x,2)+F.pow(y,2)+F.pow(z,2))) to eliminate Python overhead, or (2) use Pandas UDFs (@F.pandas_udf) which process vectorised batches and move the import outside the function', true],
                    ['The UDF is missing error handling — add try/except', false],
                    ['DoubleType should be FloatType for numpy compatibility', false],
                    ['numpy is not available in Spark executors — use math.sqrt instead', false],
                ],
            ],
            [
                'q' => "A Spark job running on a cluster with 20 executors (each 4 cores, 16 GB RAM) processes a 2 TB dataset. The job runs for 3 hours. Spark UI shows:\n• Stage 1: 95% time, 80 tasks, 25 MB average task size\n• Executor CPU utilisation: 12%\n\nDiagnose and fix the performance problem using these two observations.",
                'opts' => [
                    ['12% CPU utilisation means the cluster is undersized — double the number of executors', false],
                    ['80 tasks across 80 cores (20×4) means only 1 task wave — each task processes 25 MB (too small per task) while the cluster is massively underutilised. Fix: increase partitions to 800-1600 (repartition(1600) or coalesce(800)) so tasks are smaller and all 80 cores stay busy with multiple waves of meaningful work', true],
                    ['The problem is memory — increase spark.executor.memory to 32 GB', false],
                    ['Stage 1 taking 95% of time is expected — all work concentrates in one stage', false],
                ],
            ],

            // ── 22.5 APACHE KAFKA ─────────────────────────────────────────
            [
                'q' => "A Kafka-based payment processing system must guarantee exactly-once end-to-end semantics (producer → Kafka → consumer → database). What combination of Kafka features achieves this?\n\nA) Producer idempotence (enable.idempotence=true) + Kafka transactions (transactional.id) + consumer reading only committed offsets (isolation.level=read_committed) + idempotent consumer writes to the database (upsert on payment_id)\nB) acks=all + retries=MAX_INT only\nC) A single Kafka partition for all payments\nD) Consumer auto-commit with enable.auto.commit=true",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A Kafka cluster with 10 brokers, 100 topics, and 5000 total partitions suddenly experiences a broker failure. The cluster takes 45 minutes to recover (leader elections for all orphaned partitions). What configuration change reduces recovery time?\n\nA) Increase replication factor from 2 to 5\nB) Enable unclean leader election for faster failover\nC) Deploy KRaft mode (Kafka without ZooKeeper) and reduce partition count per topic — ZooKeeper-based leader election is O(n_partitions) and a bottleneck; KRaft eliminates ZooKeeper overhead and leader election scales better\nD) Reduce the number of topics to fewer than 10",
                'opts' => [
                    ['A', false],
                    ['B', false],
                    ['C', true],
                    ['D', false],
                ],
            ],
            [
                'q' => "A Kafka Streams application maintains a state store (changelog topic) of running account balances. After a rolling restart, the application takes 30 minutes to restore state before processing resumes. What is the professional solution to reduce state restoration time?\n\nA) Use a global state store instead of a local state store\nB) Configure standby replicas (num.standby.replicas=1) — Kafka Streams maintains a warm copy of state on a standby task; on failover, the standby takes over with minimal catchup rather than replaying the full changelog from the beginning\nC) Reduce the changelog topic retention period\nD) Store state in an external Redis instead of Kafka Streams state stores",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.6 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => "A Snowflake account has 500 concurrent analysts running ad-hoc queries against a single X-Large virtual warehouse (32 credits/hour). The warehouse is queued 70% of the time. Monthly cost at $3/credit running 16 hrs/day:\n\n32 credits/hr × 16 hrs × 30 days × $3 = $46,080/month\n\nThe team considers splitting into 5 Medium warehouses (4 credits/hour each). Calculate the new monthly cost if each warehouse runs 16 hrs/day, and explain the architectural benefit.",
                'opts' => [
                    ['5 × 4 credits/hr × 16 hrs × 30 days × $3 = $28,800/month (37.5% saving). Benefit: workload isolation — each team/department gets a dedicated warehouse so a heavy query from one team cannot queue another team; additionally, each warehouse auto-suspends when idle, reducing actual cost further', true],
                    ['5 × 32 credits/hr = more expensive than 1 X-Large', false],
                    ['Medium warehouses cannot handle analyst workloads — only X-Large is appropriate', false],
                    ['Cost is identical — Snowflake charges per query, not per warehouse', false],
                ],
            ],
            [
                'q' => "A BigQuery dataset grows 1 TB/day. After 2 years it has 730 TB. Storage costs $0.02/GB/month. Most queries only access the last 90 days.\n\nBigQuery's long-term storage pricing reduces cost by 50% for data not modified in 90 days. Calculate:\n1. Total storage in 2 years\n2. Monthly storage cost without long-term pricing\n3. Monthly storage cost with long-term pricing (90+ day data at $0.01/GB)",
                'opts' => [
                    ['Total: 730 TB. Without LTS: 730,000 GB × $0.02 = $14,600/month. With LTS: (90 TB × $0.02) + (640 TB × $0.01) = $1,800 + $6,400 = $8,200/month (44% saving)', true],
                    ['Total: 730 TB. Without LTS: $7,300/month. With LTS: $3,650/month', false],
                    ['Total: 365 TB (BigQuery deduplicates). Monthly cost: $3,650/month', false],
                    ['BigQuery charges per query, not per storage volume', false],
                ],
            ],
            [
                'q' => "A data warehouse table suffers from 'hot partition' syndrome — 80% of queries in the past month filtered on a single date (2024-01-15, a major sales event). The table is partitioned by date and clustered by product_category.\n\nWhat advanced Snowflake/BigQuery feature addresses this and how does it work?",
                'opts' => [
                    ['Add an index on the 2024-01-15 partition — indexes fix hot partition issues', false],
                    ['Automatic Clustering (Snowflake) / Automatic Re-clustering — the warehouse monitors micro-partition overlap statistics and automatically re-clusters data in the background when clustering depth degrades due to heavy write patterns, maintaining efficient data skipping without manual CLUSTER BY re-runs', true],
                    ['Create a materialised view of only the 2024-01-15 data', false],
                    ['Partition the table more finely — by hour instead of day', false],
                ],
            ],

            // ── 22.7 DATA PIPELINES, ETL/ELT & AIRFLOW ───────────────────
            [
                'q' => "An Airflow DAG runs 500 tasks daily. The team observes that the Airflow scheduler is slow to queue tasks and the webserver is unresponsive. Diagnosis shows:\n• Airflow metadata database (PostgreSQL) has 200 GB of task instance history\n• scheduler_heartbeat_sec=5 with 500 tasks parsing 1000 DAG files every 5 seconds\n\nWhat are the two root causes and their professional fixes?",
                'opts' => [
                    ['The DAG is too complex — reduce to 10 tasks', false],
                    ['Root cause 1: Metadata DB bloat — fix with regular DAG run cleanup (airflow db clean --keep-last-runs=30) and PostgreSQL VACUUM/ANALYZE. Root cause 2: DAG parsing overhead — fix by using DAG serialisation (store_serialized_dags=True) to parse DAGs once and cache in the DB rather than re-parsing all 1000 files every 5 seconds', true],
                    ['Airflow should be replaced with Prefect for large task counts', false],
                    ['PostgreSQL should be replaced with SQLite for lighter metadata storage', false],
                ],
            ],
            [
                'q' => "A dbt project has 800 models. Running `dbt run` takes 2.5 hours. The team runs it 4 times daily. What combination of dbt features reduces the full run time?\n\nA) dbt incremental models (only process new/changed records since last run) + model tagging (run only tagged subsets, e.g. `dbt run --select tag:hourly`) + dbt defer (in CI, only run models changed since the last production run using `dbt run --defer --select state:modified+`)\nB) Increase the dbt threads from 4 to 8 only\nC) Replace dbt with direct SQL scripts\nD) Run all 800 models in a single transaction",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "The Great Expectations library is integrated into a data pipeline. A suite of expectations fails with:\n\n'expect_column_values_to_not_be_null' FAILED: column='user_id', unexpected_count=15000, unexpected_percent=3.2%\n\nThe pipeline continues and loads 15,000 NULL user_id records into the production warehouse. What pipeline design principle was violated and how should it be fixed?",
                'opts' => [
                    ['The pipeline should ignore data quality checks for performance', false],
                    ['The validation action was set to "warn" instead of "fail" — data quality gates must be blocking (raise an exception / stop the pipeline) for critical constraints like non-null primary keys. Fix: configure the checkpoint action to throw a validation error on failure, preventing bad data from reaching the warehouse', true],
                    ['Great Expectations cannot check for null values', false],
                    ['3.2% null rate is within acceptable bounds and should be allowed', false],
                ],
            ],

            // ── 22.8 DATA LAKES, LAKEHOUSES & DELTA LAKE ─────────────────
            [
                'q' => "A data mesh architecture assigns data ownership to domain teams. The Orders domain publishes a 'canonical orders' data product consumed by 15 downstream teams. The Orders team changes the schema — dropping a column that 12 of the 15 teams use.\n\nWhat governance mechanism prevents this breaking change?",
                'opts' => [
                    ['Domain teams should not communicate — each team maintains its own copy', false],
                    ['A data contract (formalised schema agreement between producer and consumers) enforced at the data platform level — schema changes must be backwards compatible or require consumer approval; the Schema Registry or data catalogue enforces contract versions and alerts consumers before breaking changes deploy', true],
                    ['The Orders team should simply restore the column after complaints', false],
                    ['A centralised data governance team must approve all schema changes in a weekly meeting', false],
                ],
            ],
            [
                'q' => "A Delta Lake table receives concurrent writes from 3 Spark jobs and 2 structured streaming jobs simultaneously. One streaming job fails with:\n\n'ConcurrentAppendException: Files were added to the root of the table by a concurrent update'\n\nWhat does this exception mean and how does Delta Lake's optimistic concurrency control resolve it?",
                'opts' => [
                    ['Delta Lake does not support concurrent writes — use a single writer', false],
                    ['Delta Lake uses optimistic concurrency: each writer reads the current table version, performs its operation, and attempts to commit. If another writer committed in between (changing files that conflict), the transaction fails with this exception. The writer must retry. For append-only workloads, Delta Lake can often auto-resolve conflicts (concurrent appends to non-overlapping partitions succeed); the fix is to partition the table so concurrent writers target different partitions', true],
                    ['The exception means two files have identical names — rename the output files', false],
                    ['ConcurrentAppendException only occurs in Delta Lake version < 2.0', false],
                ],
            ],
            [
                'q' => "A healthcare data lakehouse must satisfy:\n1. PHI (Protected Health Information) data isolated to authorised roles\n2. Audit log of every query on PHI data\n3. HIPAA compliance requiring 6-year data retention\n4. Sub-second query latency for clinical dashboards on 50 TB of structured data\n\nWhich combination of technologies satisfies ALL four requirements?",
                'opts' => [
                    ['Delta Lake on S3 + Athena for all requirements', false],
                    ['Snowflake with row-level security (RLS) policies + column-level masking for PHI isolation; Snowflake Query History + AWS CloudTrail for audit; S3 Lifecycle to Glacier for 6-year retention of raw exports; Snowflake search optimisation + result caching + clustering for sub-second clinical dashboards', true],
                    ['BigQuery alone handles all four requirements without additional tools', false],
                    ['On-premise Oracle database — cloud is not HIPAA compliant', false],
                ],
            ],

            // ── 22.9 CLOUD SECURITY, GOVERNANCE & COMPLIANCE ─────────────
            [
                'q' => "A security audit finds that an ML training job running on AWS SageMaker has been assigned a role with AdministratorAccess permissions. The job only needs to read from one S3 bucket and write to another. What is the blast radius of this misconfiguration and the correct fix?",
                'opts' => [
                    ['No risk — SageMaker jobs run in isolated containers', false],
                    ['Blast radius: a compromised training job or malicious model code could access any AWS resource (EC2, RDS, other S3 buckets, IAM, billing) in the account. Fix: create a least-privilege SageMaker execution role with only s3:GetObject on the input bucket ARN and s3:PutObject on the output bucket ARN — nothing more', true],
                    ['AdministratorAccess is required for SageMaker to function correctly', false],
                    ['The risk is low because SageMaker is a managed service with built-in security', false],
                ],
            ],
            [
                'q' => "A company processes EU citizen data in AWS us-east-1 (USA). Under GDPR Article 44, this constitutes a cross-border data transfer. The company uses Standard Contractual Clauses (SCCs). After the Schrems II ruling, what additional technical measure is required?\n\nA) Simply having SCCs is sufficient — no technical measures needed post-Schrems II\nB) Supplementary technical measures: end-to-end encryption where the data exporter holds the keys (not the cloud provider), pseudonymisation, and data minimisation — ensuring the US cloud provider cannot access plaintext EU personal data even under government compulsion\nC) Migrate all EU data to a European cloud region — SCCs are invalidated for US regions\nD) Obtain explicit consent from all EU citizens for US data processing",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A data platform team implements column-level encryption for a PII field (ssn) in Snowflake:\n\nCREATE OR REPLACE MASKING POLICY ssn_mask AS (val STRING) RETURNS STRING ->\n  CASE WHEN CURRENT_ROLE() IN ('ANALYST') THEN '***-**-' || RIGHT(val, 4)\n       WHEN CURRENT_ROLE() IN ('COMPLIANCE') THEN val\n       ELSE '***-**-****'\n  END;\n\nALTER TABLE customers MODIFY COLUMN ssn SET MASKING POLICY ssn_mask;\n\nAn analyst queries: SELECT ssn FROM customers WHERE ssn = '123-45-6789'. What does the analyst see and can they filter by real SSN?",
                'opts' => [
                    ['The analyst sees the masked value but the WHERE filter works on the real data — the filter successfully returns the row for SSN \'123-45-6789\' but displays \'***-**-6789\'', true],
                    ['The analyst sees nothing — the WHERE clause also masks the filter value, returning 0 rows', false],
                    ['The masking policy blocks the query entirely — a permission error is raised', false],
                    ['The analyst sees \'***-**-****\' because they have no role matching the policy', false],
                ],
            ],

            // ── 22.10 MLOps & DEPLOYING ML AT SCALE ──────────────────────
            [
                'q' => "A production ML recommendation model serves 50,000 requests/second. The team needs to deploy a new model version with a different input feature schema (3 new features added, 1 removed). A naive replace causes 2 minutes of downtime. Design a zero-downtime schema migration deployment strategy.",
                'opts' => [
                    ['Take the service down during off-peak hours for migration', false],
                    ['Phase 1: Deploy a model adapter layer that handles both old and new schemas (backwards compatible wrapper). Phase 2: Gradually migrate clients to send new schema via canary (5% → 25% → 100%). Phase 3: Update the feature store and serving pipeline to produce new schema for all traffic. Phase 4: Remove the adapter. This ensures zero downtime with rollback capability at each phase', true],
                    ['Simply update the model in place — modern load balancers handle schema differences transparently', false],
                    ['Use blue/green deployment: switch 100% of traffic at once to avoid schema confusion', false],
                ],
            ],
            [
                'q' => "A fraud detection model trained on 2023 data is deployed in production. In March 2024, the fraud team notices precision drops from 0.88 to 0.61 despite stable recall. Investigation shows that fraudsters have adopted new techniques not present in 2023 training data.\n\nThis is:\nA) Model decay (gradual performance degradation due to data drift)\nB) Concept drift (the underlying relationship between features and fraud has changed — new fraud patterns)\nC) Training-serving skew\nD) Label noise from the annotation team\n\nWhat is the correct monitoring and retraining strategy?",
                'opts' => [
                    ['A', false],
                    ['B — concept drift. Strategy: implement a continuous learning pipeline that retrains weekly on a rolling 3-month window (balancing recent fraud patterns with sufficient volume); use online learning for fast adaptation; monitor precision/recall on a daily labelled sample via analyst feedback; deploy new model versions via shadow mode to validate before switching', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A data science platform team must build a fully automated ML pipeline on AWS that:\n1. Triggers on new S3 data arrival\n2. Validates data quality (Great Expectations)\n3. Trains a model on SageMaker\n4. Evaluates against a holdout set\n5. Registers the model in MLflow if metrics pass thresholds\n6. Deploys to a SageMaker endpoint with blue/green traffic shift\n7. Monitors endpoint for data drift\n\nWhat AWS-native orchestration service best coordinates this workflow, and what monitoring service handles step 7?",
                'opts' => [
                    ['AWS Lambda for orchestration; CloudWatch for drift monitoring', false],
                    ['Amazon SageMaker Pipelines (natively integrates all SageMaker steps with conditional logic, model registry, and endpoint deployment); SageMaker Model Monitor for step 7 (continuously compares inference input/output distributions against training baselines, alerting on data drift and model quality degradation)', true],
                    ['Apache Airflow on MWAA for orchestration; S3 event notifications for monitoring', false],
                    ['AWS Step Functions; Amazon Rekognition for drift detection', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 22 — Big Data & Cloud Computing (Professional).");
    }
}