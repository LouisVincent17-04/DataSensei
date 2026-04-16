<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module22ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 22 — Big Data & Cloud Computing (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Big Data & Cloud Computing',
            'description'           => 'Apply your Big Data and Cloud knowledge to multi-step problems, code tracing, cost calculations, and architectural design decisions reflecting real data engineering scenarios across Spark, Kafka, Airflow, and cloud platforms.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1500,
            'order_index'           => 22,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions on Big Data & Cloud Computing...");

        $qaData = [

            // ── 22.1 BIG DATA & THE 5 Vs ─────────────────────────────────
            [
                'q' => "A company processes 10 TB of log data per day from 5,000 microservices. Each log line is a JSON object with 40 fields, but only 3 fields are queried regularly. Engineers debate between storing logs as:\n\nOption A: Row-oriented JSON files\nOption B: Columnar Parquet files partitioned by service and date\n\nWhich addresses the 'Volume' and 'Variety' Vs most effectively and why?",
                'opts' => [
                    ['Option A — JSON is more flexible for variety', false],
                    ['Option B — Parquet\'s columnar storage skips unused columns (reducing I/O from 40 to 3 columns per query) while partitioning by service/date prunes irrelevant files, handling both volume efficiency and varied service sources', true],
                    ['Both options are equivalent for this workload', false],
                    ['Option A — JSON is faster for 40-field records', false],
                ],
            ],
            [
                'q' => "Calculate the storage cost of a Big Data system:\n• Raw data ingested: 5 TB/day\n• Replication factor: 3\n• Retention: 90 days\n• Compression ratio: 4:1 (after Parquet compression)\n\nTotal compressed and replicated storage after 90 days = ?",
                'opts' => [
                    ['5 TB × 90 = 450 TB', false],
                    ['5 TB / 4 × 3 × 90 = 337.5 TB', true],
                    ['5 TB × 3 × 90 = 1350 TB', false],
                    ['5 TB / 4 × 90 = 112.5 TB (without replication)', false],
                ],
            ],
            [
                'q' => "You receive a dataset with 15% missing values in a critical feature, 8% duplicate rows, and timestamps stored inconsistently (mix of Unix epoch and ISO 8601 strings). Which 'V' does this dataset primarily violate, and what is the correct remediation order?",
                'opts' => [
                    ['Volume — remove rows until the dataset is smaller', false],
                    ['Veracity — the data is untrustworthy. Remediation order: (1) deduplicate, (2) standardise timestamps, (3) impute or flag missing values — deduplication first prevents double-counting in imputation statistics', true],
                    ['Velocity — the data is arriving too slowly', false],
                    ['Variety — too many different field types', false],
                ],
            ],

            // ── 22.2 CLOUD COMPUTING ──────────────────────────────────────
            [
                'q' => "A data science team needs to run a 72-hour model training job on a cluster of 8 GPUs. After training, the cluster sits idle. Compare costs:\n\nOption A: Reserved instances (pre-paid annually) at $0.90/hr each\nOption B: On-demand spot instances at $1.80/hr each (with potential interruption)\nOption C: On-demand standard instances at $3.50/hr each\n\nFor 72 hours × 8 GPUs, which is cheapest and what is its cost?",
                'opts' => [
                    ['Option A: $0.90 × 8 × 72 = $518.40 (cheapest only if used year-round — here the idle time makes reservations wasteful)', false],
                    ['Option B: $1.80 × 8 × 72 = $1,036.80', false],
                    ['Option C: $3.50 × 8 × 72 = $2,016.00', false],
                    ['Option B at $1,036.80 is cheapest for a single 72-hour burst — spot instances offer the best hourly rate for short jobs and the team can handle interruption with checkpointing', true],
                ],
            ],
            [
                'q' => "An organisation stores sensitive patient health data. Regulations require data to never leave the country. They also need burst compute capacity for quarterly model retraining. Which architecture is most appropriate?",
                'opts' => [
                    ['Full public cloud — AWS handles compliance automatically', false],
                    ['Hybrid cloud — sensitive data stays in private on-premise storage meeting data residency requirements; burst compute jobs pull anonymised feature data to a public cloud for training', true],
                    ['Full private cloud — reject public cloud entirely', false],
                    ['Multi-cloud with data replicated across AWS and GCP in multiple regions', false],
                ],
            ],
            [
                'q' => "What does the following AWS CLI command do?\n\naws s3 cp s3://my-data-lake/raw/2024/ s3://my-data-lake/archive/2024/ --recursive --storage-class GLACIER",
                'opts' => [
                    ['Deletes all files from the raw 2024 prefix', false],
                    ['Copies all objects under raw/2024/ recursively to archive/2024/ and changes their storage class to Glacier (cold, infrequent-access storage) — a cost optimisation for old data', true],
                    ['Creates a backup of the S3 bucket to a local machine', false],
                    ['Moves files from raw to archive and deletes the source', false],
                ],
            ],

            // ── 22.3 DISTRIBUTED STORAGE ──────────────────────────────────
            [
                'q' => "An HDFS cluster has 10 data nodes. A 1.28 GB file is written with block size 128 MB and replication factor 3. How many total block replicas exist across the cluster?",
                'opts' => [
                    ['10 replicas (one per node)', false],
                    ['30 replicas — 10 blocks × 3 replicas each', true],
                    ['3 replicas total (one per replica set)', false],
                    ['12 replicas — 10 blocks + 2 extra for fault tolerance', false],
                ],
            ],
            [
                'q' => "What does the following Parquet-reading PySpark code output, and why is it efficient?\n\ndf = spark.read.parquet('s3://bucket/sales/')\nresult = df.filter(df.year == 2024).select('revenue', 'region')\nresult.show()",
                'opts' => [
                    ['It reads all columns from all partitions, then filters and selects in memory', false],
                    ['Spark\'s Catalyst optimizer applies predicate pushdown (skipping non-2024 partitions) and column pruning (reading only revenue and region columns from Parquet) — minimising I/O before data enters the cluster', true],
                    ['The filter runs after show(), making it inefficient', false],
                    ['Parquet files cannot be read directly from S3', false],
                ],
            ],
            [
                'q' => "The difference between Apache Parquet and Apache ORC columnar formats is:\n\nA) Parquet is better supported by Spark and cloud ecosystems; ORC was optimised for Hive and older Hadoop workloads\nB) ORC is row-oriented; Parquet is columnar\nC) Parquet cannot store nested data structures\nD) ORC does not support compression",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.4 APACHE SPARK ─────────────────────────────────────────
            [
                'q' => "Trace through the following PySpark code and determine the output:\n\nfrom pyspark.sql import SparkSession\nspark = SparkSession.builder.getOrCreate()\ndata = [(1, 'A', 100), (2, 'B', 200), (3, 'A', 150), (4, 'B', 300)]\ndf = spark.createDataFrame(data, ['id', 'group', 'value'])\nresult = df.groupBy('group').agg({'value': 'sum'})\nresult.show()",
                'opts' => [
                    ['+-----+----------+\n|group|sum(value)|\n+-----+----------+\n|    A|       250|\n|    B|       500|\n+-----+----------+', true],
                    ['The code raises an error — agg() does not accept dict syntax', false],
                    ['Only one row is returned because groupBy returns a single group', false],
                    ['The output is unsorted with sum = 750 in one row', false],
                ],
            ],
            [
                'q' => "What is a Spark 'shuffle' operation and why is it expensive?\n\ndf1.join(df2, on='customer_id')",
                'opts' => [
                    ['A shuffle randomly samples rows — it is cheap and O(1)', false],
                    ['A shuffle redistributes data across partitions so that rows with the same join key end up on the same executor — this requires network data transfer between all nodes, making it the most expensive operation in Spark', true],
                    ['A shuffle sorts each partition independently with no network I/O', false],
                    ['Shuffles only occur for groupBy, never for joins', false],
                ],
            ],
            [
                'q' => "A Spark job processing 500 GB of data has 200 tasks but only 4 executor cores. What is the correct calculation for the minimum number of 'waves' (rounds of parallel task execution)?",
                'opts' => [
                    ['200 / 4 = 50 waves — each wave runs 4 tasks simultaneously', true],
                    ['200 × 4 = 800 waves', false],
                    ['500 / 4 = 125 waves based on data size', false],
                    ['Only 1 wave — Spark runs all tasks simultaneously', false],
                ],
            ],
            [
                'q' => "What is 'data skew' in Spark and what is its performance impact?\n\ndf.groupBy('country').count()",
                'opts' => [
                    ['Data skew means the data is not sorted — it has no performance impact', false],
                    ['If one country (e.g. USA) has 80% of records, the partition holding USA data takes much longer than others — one executor becomes the bottleneck while others finish quickly, creating a straggler that delays the entire stage', true],
                    ['Data skew occurs when the cluster has too many executor cores', false],
                    ['Skew only affects joins, not groupBy operations', false],
                ],
            ],

            // ── 22.5 APACHE KAFKA ─────────────────────────────────────────
            [
                'q' => "A Kafka topic has 6 partitions and a consumer group has 4 consumers. How are partitions assigned?",
                'opts' => [
                    ['Each consumer gets 1.5 partitions — Kafka splits partitions fractionally', false],
                    ['Kafka assigns partitions round-robin: 2 consumers each handle 2 partitions, and 2 consumers each handle 1 partition (6 partitions across 4 consumers = 2+2+1+1)', true],
                    ['Only 4 partitions are used; 2 are left empty', false],
                    ['All 4 consumers read all 6 partitions simultaneously', false],
                ],
            ],
            [
                'q' => "What is a Kafka 'offset' and why is it important for fault tolerance?",
                'opts' => [
                    ['The offset is the size in bytes of each Kafka message', false],
                    ['The offset is a monotonically increasing integer identifying each message\'s position within a partition — consumers track their offset so that after a crash they can resume reading exactly where they left off, preventing data loss or duplication', true],
                    ['The offset is the latency between producer and consumer in milliseconds', false],
                    ['The offset is the number of partitions minus the number of consumers', false],
                ],
            ],
            [
                'q' => "A Kafka producer sends order events. Orders for the same customer must be processed in order. What configuration ensures this?",
                'opts' => [
                    ['Use a single partition for all customers', false],
                    ['Set the message key to the customer_id — Kafka routes all messages with the same key to the same partition, guaranteeing order within that partition for that customer', true],
                    ['Enable Kafka transactions on the consumer side', false],
                    ['Set replication factor to 1 to avoid reordering', false],
                ],
            ],
            [
                'q' => "What is the difference between Kafka Streams and Apache Flink for stream processing?",
                'opts' => [
                    ['Kafka Streams only supports batch processing; Flink is real-time only', false],
                    ['Kafka Streams is a lightweight library that runs inside your application (no separate cluster needed) and only reads from Kafka; Flink is a full distributed stream processing engine that supports multiple sources and more complex stateful operations', true],
                    ['Flink is written in Python; Kafka Streams is written in Java only', false],
                    ['They are equivalent tools with the same architecture', false],
                ],
            ],

            // ── 22.6 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => "A BigQuery table has 10 TB of data. A query reads the entire table and costs $5/TB. A data engineer adds a partition on the `event_date` column and a user queries only one day's data (approximately 27 GB). Calculate the savings per query.",
                'opts' => [
                    ['No savings — BigQuery always scans the full table', false],
                    ['Unpartitioned: 10 TB × $5 = $50. Partitioned: 0.027 TB × $5 = $0.135. Savings = $49.865 per query — a ~370x cost reduction', true],
                    ['Savings = $5 - $0.027 = $4.973', false],
                    ['Savings = 10 TB - 27 GB = approximately 9.97 TB', false],
                ],
            ],
            [
                'q' => "In Snowflake, what is a 'virtual warehouse' and how does it differ from the storage layer?",
                'opts' => [
                    ['A virtual warehouse is where Snowflake stores compressed data files', false],
                    ['A virtual warehouse is an independent cluster of compute nodes that executes queries — multiple virtual warehouses can run queries simultaneously against the same shared storage without contention', true],
                    ['A virtual warehouse is Snowflake\'s name for a data lake', false],
                    ['A virtual warehouse is a Snowflake account that exists only in a test environment', false],
                ],
            ],
            [
                'q' => "What is 'materialized view' in a cloud data warehouse and when should it be used?",
                'opts' => [
                    ['A view that only shows the most recent 1000 rows', false],
                    ['A pre-computed, cached result of a query stored as a table — used for frequently-run expensive aggregations where staleness (refresh lag) is acceptable, trading storage cost for query speed', true],
                    ['A view that can be modified directly like a table', false],
                    ['A view that materialises only when the user logs in', false],
                ],
            ],

            // ── 22.7 DATA PIPELINES, ETL/ELT & AIRFLOW ───────────────────
            [
                'q' => "The following Airflow DAG definition has a bug. Identify it:\n\nfrom airflow import DAG\nfrom airflow.operators.python import PythonOperator\nfrom datetime import datetime\n\ndag = DAG('my_pipeline', start_date=datetime(2024, 1, 1), schedule_interval='@daily')\n\nextract = PythonOperator(task_id='extract', python_callable=extract_fn, dag=dag)\nload = PythonOperator(task_id='load', python_callable=load_fn, dag=dag)\ntransform = PythonOperator(task_id='transform', python_callable=transform_fn, dag=dag)\n\nextract >> load >> transform",
                'opts' => [
                    ['The DAG is missing a catchup=False parameter', false],
                    ['The task dependency order is wrong — data should be extracted, then transformed, then loaded: extract >> transform >> load', true],
                    ['PythonOperator cannot be used in modern Airflow', false],
                    ['The schedule_interval @daily is not a valid cron expression', false],
                ],
            ],
            [
                'q' => "In a dbt project, what is the difference between a 'model' and a 'test'?",
                'opts' => [
                    ['Models are Python scripts; tests are SQL queries', false],
                    ['A dbt model is a SQL SELECT statement that defines a transformation (materialised as a table or view in the warehouse); a dbt test is a data quality assertion that validates model outputs (e.g. not_null, unique, accepted_values)', true],
                    ['Tests run before models in the dbt build order', false],
                    ['Models and tests are synonyms in dbt terminology', false],
                ],
            ],
            [
                'q' => "What is 'idempotency' in data pipeline design and why is it critical?",
                'opts' => [
                    ['Idempotency means a pipeline runs faster on subsequent executions', false],
                    ['An idempotent pipeline produces the same result regardless of how many times it is run — critical because retries after failures should not cause duplicate records or partial updates in the destination', true],
                    ['Idempotency means a pipeline can process any file format', false],
                    ['An idempotent pipeline uses incremental loading exclusively', false],
                ],
            ],

            // ── 22.8 DATA LAKES, LAKEHOUSES & DELTA LAKE ─────────────────
            [
                'q' => "Delta Lake uses a transaction log (_delta_log/) to provide ACID guarantees. What information is stored in this log?",
                'opts' => [
                    ['The raw data files with ACID metadata embedded', false],
                    ['A sequence of JSON commit files recording every operation (add file, remove file, schema change) — this log is what enables time travel, schema enforcement, and concurrent writer isolation', true],
                    ['Encrypted copies of all Delta table data', false],
                    ['Only DELETE and UPDATE operations — INSERT operations are logged separately', false],
                ],
            ],
            [
                'q' => "What does the following Delta Lake Python command do?\n\nfrom delta.tables import DeltaTable\ndt = DeltaTable.forPath(spark, '/data/customers')\ndt.vacuum(retentionHours=168)",
                'opts' => [
                    ['Deletes all data older than 168 days from the Delta table', false],
                    ['Removes data files no longer referenced by the transaction log that are older than 168 hours (7 days) — reclaims storage used by old versions while keeping 7 days of time travel history', true],
                    ['Compresses all Parquet files into a single file after 168 hours', false],
                    ['Flushes the Delta cache after 168 hours of inactivity', false],
                ],
            ],
            [
                'q' => "A lakehouse architecture combines a data lake and a data warehouse. Which scenario best justifies choosing a lakehouse over separate lake + warehouse systems?",
                'opts' => [
                    ['The team only uses SQL and structured data — a warehouse alone is sufficient', false],
                    ['The team needs to run BI dashboards (SQL on structured data), ML training (raw files in Python), and streaming ingestion — all on the same unified storage layer without copying data between systems', true],
                    ['The team has a very small dataset (under 1 GB)', false],
                    ['The team wants to reduce query performance to save costs', false],
                ],
            ],

            // ── 22.9 CLOUD SECURITY, GOVERNANCE & COMPLIANCE ─────────────
            [
                'q' => "A data engineer accidentally gives all analysts `BigQuery Admin` role instead of `BigQuery Data Viewer`. What is the security risk and correct fix?",
                'opts' => [
                    ['No risk — Admin and Viewer roles are equivalent for read-only analysts', false],
                    ['BigQuery Admin allows deleting datasets, modifying permissions, and creating expensive jobs — violating least privilege. Fix: revoke Admin, grant Data Viewer + Job User roles only', true],
                    ['The fix is to enable MFA for all analyst accounts', false],
                    ['Admin role is required to run SELECT queries in BigQuery', false],
                ],
            ],
            [
                'q' => "Under GDPR, a user requests deletion of all their personal data from your data lake. Your data lake contains raw JSON logs with user_id fields spread across 3 years of Parquet files. What is the main technical challenge?",
                'opts' => [
                    ['Parquet files are read-only — you cannot modify them at all', false],
                    ['Rewriting or replacing 3 years of immutable Parquet files to remove a single user\'s records is expensive — solutions include using Delta Lake\'s DELETE support, pseudonymisation, or crypto-shredding (delete the encryption key for that user\'s data)', true],
                    ['GDPR does not apply to data stored in a data lake', false],
                    ['Simply deleting the user\'s account satisfies the right to erasure for all associated data', false],
                ],
            ],
            [
                'q' => "What is 'data masking' and when is it applied in a cloud data platform?",
                'opts' => [
                    ['Data masking means hiding a dataset from all users', false],
                    ['Data masking replaces sensitive data (e.g. credit card numbers, SSNs) with realistic but fictitious values — applied when sharing data with analysts or test environments where real PII should not be exposed', true],
                    ['Data masking is equivalent to data encryption', false],
                    ['Data masking permanently deletes sensitive columns from a dataset', false],
                ],
            ],

            // ── 22.10 MLOps & DEPLOYING ML AT SCALE ──────────────────────
            [
                'q' => "A deployed ML model returns predictions via a REST API. The team notices p99 latency spikes from 50ms to 800ms during peak hours. What is the most likely cause and first mitigation to try?",
                'opts' => [
                    ['The model weights are corrupted — retrain the model', false],
                    ['The single model server is CPU-bound during peak load — first mitigation: scale horizontally by adding more model server replicas behind a load balancer', true],
                    ['REST APIs are too slow for ML — switch to gRPC', false],
                    ['The feature store is returning incorrect features', false],
                ],
            ],
            [
                'q' => "The following Dockerfile for an ML model has a performance issue. Identify it:\n\nFROM python:3.11\nWORKDIR /app\nCOPY . .\nRUN pip install -r requirements.txt\nRUN python train_model.py\nEXPOSE 8080\nCMD ['python', 'serve.py']",
                'opts' => [
                    ['The WORKDIR is set incorrectly', false],
                    ['Training the model (RUN python train_model.py) inside the Docker build bakes a specific trained model into the image — any retraining requires rebuilding the image. Models should be loaded from external storage (S3/GCS) at runtime, not baked in', true],
                    ['pip install should come after COPY . .', false],
                    ['EXPOSE 8080 is not valid in a Dockerfile', false],
                ],
            ],
            [
                'q' => "What is 'canary deployment' in MLOps and how does it differ from blue/green deployment?",
                'opts' => [
                    ['They are identical strategies with different names', false],
                    ['Canary deployment gradually routes an increasing percentage of traffic to the new model (e.g. 5% → 20% → 100%), allowing early detection of issues with minimal user impact; blue/green switches 100% of traffic at once from the old (blue) to new (green) deployment', true],
                    ['Canary deployment is only used for batch models; blue/green is for real-time APIs', false],
                    ['Blue/green routing is always more expensive than canary routing', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 22 — Big Data & Cloud Computing (Intermediate).");
    }
}