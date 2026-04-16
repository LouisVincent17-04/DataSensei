<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module22ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 22 — Big Data & Cloud Computing (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Big Data & Cloud Computing',
            'description'           => 'Debug complex Spark jobs, optimise Kafka pipelines, design fault-tolerant cloud architectures, and reason about subtle production failures across the full modern data stack. Questions involve real code, cost modelling, and system-level trade-offs.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 22,
        ]);

        $this->command->info("Seeding 50 advanced-level questions on Big Data & Cloud Computing...");

        $qaData = [

            // ── 22.1 BIG DATA & THE 5 Vs ─────────────────────────────────
            [
                'q' => "A data platform ingests IoT sensor readings from 200,000 devices. Each device sends 10 readings/second. Each reading is 256 bytes.\n\nCalculate:\n1. Events per second across all devices\n2. Throughput in MB/second\n3. Daily data volume in TB (uncompressed)",
                'opts' => [
                    ['2M events/sec; 512 MB/sec; ~44 TB/day', true],
                    ['200K events/sec; 51.2 MB/sec; ~4.4 TB/day', false],
                    ['2M events/sec; 256 MB/sec; ~22 TB/day', false],
                    ['200K events/sec; 256 MB/sec; ~22 TB/day', false],
                ],
            ],
            [
                'q' => "The following PySpark code reads a 10 TB dataset but runs out of driver memory. Identify the bug:\n\ndf = spark.read.parquet('s3://bucket/data/')\nall_data = df.collect()  # Bug\nresult = process(all_data)",
                'opts' => [
                    ['spark.read.parquet cannot read from S3 directly', false],
                    ['df.collect() pulls ALL data from all executors into the driver\'s memory — 10 TB will crash the driver. The fix is to keep data as a distributed DataFrame and use Spark transformations/actions rather than collecting to the driver', true],
                    ['The parquet path is missing the file extension', false],
                    ['process() cannot accept a list as input in PySpark', false],
                ],
            ],
            [
                'q' => "A streaming pipeline must process 1 million events/second with end-to-end latency under 500ms. The current Kafka → Spark Structured Streaming → Delta Lake pipeline achieves 2-second latency. What is the most impactful tuning step?\n\nA) Increase Kafka retention period\nB) Reduce the Spark streaming trigger interval from 1 minute to 100ms and increase the number of Kafka partitions to match Spark parallelism\nC) Switch from Delta Lake to CSV for faster writes\nD) Reduce the number of Kafka brokers",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.2 CLOUD COMPUTING ──────────────────────────────────────
            [
                'q' => "The following Terraform code provisions an S3 bucket. Identify the security misconfiguration:\n\nresource 'aws_s3_bucket' 'data_lake' {\n  bucket = 'company-data-lake'\n}\n\nresource 'aws_s3_bucket_acl' 'data_lake_acl' {\n  bucket = aws_s3_bucket.data_lake.id\n  acl    = 'public-read'\n}",
                'opts' => [
                    ['The bucket name is too long', false],
                    ['acl = \"public-read\" makes the data lake publicly readable on the internet — any person or bot can list and download all files. This should be \"private\" with explicit IAM policies for authorised access only', true],
                    ['Terraform cannot provision S3 buckets — use AWS CLI instead', false],
                    ['The bucket is missing a region declaration', false],
                ],
            ],
            [
                'q' => "A data engineering team uses Spot (preemptible) instances for Spark jobs. A 6-hour Spark job is interrupted after 5 hours when AWS reclaims the spot instance. Which Spark feature minimises repeated work on restart?",
                'opts' => [
                    ['Spark lazy evaluation — transformations are cheap to recompute', false],
                    ['Spark checkpointing — periodically saving RDD/DataFrame state to S3 so that a restarted job can resume from the last checkpoint rather than reprocessing all 5 hours of work', true],
                    ['Spark shuffle files — automatically preserved across driver restarts', false],
                    ['Increasing spark.executor.instances to prevent interruption', false],
                ],
            ],
            [
                'q' => "A company runs 50 different data workloads on AWS. Some are latency-sensitive (real-time APIs), others are batch jobs that can tolerate delays. Which AWS cost optimisation strategy is most appropriate?\n\nA) Reserved instances for everything at the lowest hourly rate\nB) Spot instances for batch jobs (tolerant of interruption), reserved instances for steady-state services, on-demand for variable real-time workloads\nC) On-demand instances for everything to maintain maximum flexibility\nD) Use only Lambda functions to eliminate all server costs",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.3 DISTRIBUTED STORAGE ──────────────────────────────────
            [
                'q' => "A Spark job reads a 100 GB Parquet dataset partitioned by date (365 daily partitions). The query filters on a single week (7 partitions ≈ 1.9 GB) but still reads all 100 GB. What is the most likely cause?",
                'opts' => [
                    ['Spark cannot read partitioned Parquet files from S3', false],
                    ['The filter column (date) does not match the physical partition column in the directory structure — partition pruning requires the filter to reference the exact partition directory name (e.g. date=2024-01-01); if the column name differs or the data is not partitioned on that column, Spark reads all files', true],
                    ['Parquet always reads full files; pruning only works with ORC', false],
                    ['The Spark version does not support predicate pushdown', false],
                ],
            ],
            [
                'q' => "What does the following PySpark operation do and when should it be avoided?\n\ndf_cached = df.cache()\ndf_cached.count()  # Trigger materialisation\n# ... multiple subsequent operations on df_cached",
                'opts' => [
                    ['cache() stores the DataFrame in serialised form on disk — always slower than recomputing', false],
                    ['cache() persists the DataFrame in executor memory (at MEMORY_AND_DISK storage level) — appropriate when the same DataFrame is used in multiple downstream operations to avoid recomputing from source; should be avoided if the DataFrame is used only once or exceeds available memory', true],
                    ['cache() is equivalent to persist(StorageLevel.DISK_ONLY)', false],
                    ['cache() prevents Spark from garbage-collecting the DataFrame', false],
                ],
            ],
            [
                'q' => "A data lake accumulates millions of small Parquet files (each 1-5 MB) due to hourly micro-batch writes. Query performance degrades severely. What is this problem called and how is it solved?\n\nA) The 'shuffle bottleneck' — solved by increasing Spark partitions\nB) The 'small files problem' — solved by periodically running a compaction job (e.g. OPTIMIZE in Delta Lake) to merge small files into larger ones (128-256 MB), reducing the number of file open/close operations per query\nC) The 'hot partition problem' — solved by adding more partitions\nD) HDFS NameNode overload — solved by adding NameNode replicas",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.4 APACHE SPARK ─────────────────────────────────────────
            [
                'q' => "The following Spark job runs 10x slower than expected. Identify the root cause from the Spark UI observation: Stage 4 has 200 tasks, 197 finish in 30 seconds but 3 tasks take 8 minutes.\n\ndf.groupBy('city').agg(F.count('*').alias('cnt')).orderBy('cnt', ascending=False)",
                'opts' => [
                    ['Stage 4 is slow because orderBy requires a full sort', false],
                    ['Data skew — 3 cities (e.g. New York, London, Tokyo) have vastly more records than the other 197 cities, causing 3 tasks to process disproportionately large partitions. Fix: salting (add a random prefix to the key to spread the load) or using repartition()', true],
                    ['The cluster does not have enough memory for Stage 4', false],
                    ['count(\'*\') is not supported in Spark 3.x', false],
                ],
            ],
            [
                'q' => "What is the difference between repartition() and coalesce() in Spark, and when should each be used?\n\ndf.repartition(200)   # Option A\ndf.coalesce(10)       # Option B",
                'opts' => [
                    ['They are equivalent — both change the number of partitions with a full shuffle', false],
                    ['repartition() performs a full shuffle and can increase OR decrease partition count (produces balanced partitions); coalesce() reduces partitions without a full shuffle by merging partitions locally (faster but may produce uneven partitions) — use repartition() before joins/writes, coalesce() before writing a small result to disk', true],
                    ['repartition() only works for increasing partitions; coalesce() only works for reducing', false],
                    ['coalesce() triggers a shuffle; repartition() avoids it', false],
                ],
            ],
            [
                'q' => "The following PySpark join produces a Cartesian product warning and runs for hours. What is wrong?\n\ndf_large = spark.read.parquet('s3://orders/')      # 500M rows\ndf_small = spark.read.parquet('s3://customers/')   # 10K rows\n\nresult = df_large.join(df_small)",
                'opts' => [
                    ['The join is missing an ON condition — without specifying a join key, Spark performs a cross join (Cartesian product) of 500M × 10K = 5 trillion row pairs. Fix: add the join key: df_large.join(df_small, on=\'customer_id\')', true],
                    ['The join direction is reversed — always join small to large', false],
                    ['Parquet files cannot be joined in Spark', false],
                    ['The join produces a warning because df_small is not cached', false],
                ],
            ],
            [
                'q' => "Broadcast join in Spark avoids a shuffle. The following hint uses it:\n\nfrom pyspark.sql import functions as F\nresult = df_large.join(F.broadcast(df_small), on='customer_id')\n\nWhat is the size threshold (default spark.sql.autoBroadcastJoinThreshold) for automatic broadcast, and why does manual broadcast help when a table is just above this threshold?",
                'opts' => [
                    ['10 MB default — manual broadcast forces it even for tables up to 8 GB, eliminating shuffle at the cost of driver memory', true],
                    ['1 GB default — tables larger than 1 GB cannot be broadcast regardless', false],
                    ['There is no default threshold — all tables are broadcast unless disabled', false],
                    ['128 MB default — manual broadcast is never more efficient than the automatic threshold', false],
                ],
            ],

            // ── 22.5 APACHE KAFKA ─────────────────────────────────────────
            [
                'q' => "A Kafka producer sends financial transactions. The following configuration is applied:\n\nacks=all\nretries=3\nenable.idempotence=true\n\nWhat guarantee does this configuration provide and what is the trade-off?",
                'opts' => [
                    ['It guarantees at-most-once delivery with maximum throughput', false],
                    ['acks=all ensures the message is committed to all in-sync replicas before acknowledgement (durability); enable.idempotence prevents duplicate messages on retry (exactly-once produce semantics). Trade-off: higher latency vs. higher durability/correctness', true],
                    ['It guarantees at-least-once delivery with no retry overhead', false],
                    ['retries=3 means each message is sent to 3 different brokers', false],
                ],
            ],
            [
                'q' => "A Kafka consumer group has 3 consumers reading a topic with 3 partitions. Consumer 2 dies. What happens to its partitions, and how long does the rebalance take by default?",
                'opts' => [
                    ['The partitions are lost permanently until consumer 2 restarts', false],
                    ['Kafka triggers a group rebalance: the remaining 2 consumers are reassigned consumer 2\'s partition(s). During the rebalance (triggered after session.timeout.ms, default 10 seconds) no consumer in the group can read — this is called "stop the world" rebalancing', true],
                    ['The remaining 2 consumers automatically pick up the orphaned partition with zero downtime', false],
                    ['The broker redistributes messages from the dead consumer\'s partition to the other partitions', false],
                ],
            ],
            [
                'q' => "Kafka Connect is used to stream data from a PostgreSQL database into a Kafka topic. The following configuration is applied:\n\n'connector.class': 'io.debezium.connector.postgresql.PostgresConnector'\n'plugin.name': 'pgoutput'\n'slot.name': 'debezium_slot'\n\nWhat is the data capture pattern being used and what does it capture?",
                'opts' => [
                    ['Full table scan every minute — captures the full table state periodically', false],
                    ['Change Data Capture (CDC) via PostgreSQL logical replication — Debezium reads the PostgreSQL Write-Ahead Log (WAL) and emits row-level INSERT, UPDATE, and DELETE events to Kafka in real time, with no polling overhead', true],
                    ['Batch export using pg_dump every hour', false],
                    ['Query-based incremental capture using an updated_at timestamp column', false],
                ],
            ],

            // ── 22.6 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => "The following BigQuery query runs daily and costs $25/run (scans 5 TB). The table is queried 30 times/day by different analysts, always filtering on the same `event_date` column. The table is not partitioned. What is the monthly cost and what architectural fix reduces it by ~90%?\n\nSELECT user_id, event_type, revenue\nFROM `project.dataset.events`\nWHERE DATE(event_timestamp) = '2024-01-15'",
                'opts' => [
                    ['Monthly cost = $25 × 30 × 30 = $22,500. Fix: partition the table on event_date and cluster on event_type — queries filter to a single day (~16 GB), reducing cost to ~$0.08/query and monthly cost to ~$72', true],
                    ['Monthly cost = $25 × 30 = $750. Fix: use SELECT * to cache all columns', false],
                    ['Monthly cost = $25/day. BigQuery automatically partitions tables over time', false],
                    ['Monthly cost = $22,500. Fix: switch to Redshift which has no per-query costs', false],
                ],
            ],
            [
                'q' => "Snowflake's 'zero-copy cloning' creates a clone of a table instantly without duplicating data. How does it work internally and what is a practical MLOps use case?",
                'opts' => [
                    ['It compresses the table to 0 bytes — data is reconstructed from metadata on query', false],
                    ['Zero-copy cloning creates a new table that shares the same underlying micropartition files as the original — only newly written data diverges and incurs additional storage cost. Use case: create a production data clone for model training without risking writes to production', true],
                    ['It creates a pointer to the original table with no independent existence', false],
                    ['It copies the table schema but not the data — data must be inserted separately', false],
                ],
            ],
            [
                'q' => "A data warehouse query takes 4 minutes. EXPLAIN ANALYZE shows 80% of execution time is spent on a hash join between a 500M-row fact table and a 50M-row slowly changing dimension (SCD) table. What is the most effective optimisation?",
                'opts' => [
                    ['Add an index on the join key — warehouses use columnar indexes automatically', false],
                    ['Materialise a pre-joined version of the fact + dimension as a materialised view, refreshed nightly — dashboards query the materialised view (pre-joined, pre-aggregated) rather than running the expensive join at query time', true],
                    ['Increase the virtual warehouse size to 4XL for more memory', false],
                    ['Partition the fact table by the join key', false],
                ],
            ],

            // ── 22.7 DATA PIPELINES, ETL/ELT & AIRFLOW ───────────────────
            [
                'q' => "The following Airflow task uses the wrong operator for a PySpark job running on an EMR cluster:\n\nfrom airflow.operators.python import PythonOperator\n\ndef run_spark_job():\n    import subprocess\n    subprocess.run(['spark-submit', '--master', 'yarn', 'job.py'])\n\ntask = PythonOperator(task_id='spark_job', python_callable=run_spark_job)",
                'opts' => [
                    ['PythonOperator is the correct way to run Spark jobs', false],
                    ['Running spark-submit as a Python subprocess blocks the Airflow worker for the full job duration, consuming a worker slot. The correct approach is SparkSubmitOperator or EmrAddStepsOperator which submit the job asynchronously and release the worker while the cluster runs the job', true],
                    ['subprocess.run is not available in Python 3', false],
                    ['The master should be set to local, not yarn', false],
                ],
            ],
            [
                'q' => "What is 'watermarking' in Apache Spark Structured Streaming and why is it needed for stateful aggregations?",
                'opts' => [
                    ['Watermarking adds metadata to each Spark streaming batch for auditing', false],
                    ['Watermarking defines a threshold for how late data can arrive — events older than the watermark are dropped and the state for that time window is finalised. Without watermarking, stateful operations must keep state forever for potentially late-arriving events, causing unbounded memory growth', true],
                    ['Watermarking is used to encrypt streaming data at rest', false],
                    ['A watermark is the maximum number of partitions in a streaming job', false],
                ],
            ],
            [
                'q' => "A data pipeline runs daily. The source system sometimes delivers duplicate records for the same event_id. The destination is a Delta Lake table. What is the correct upsert pattern to handle this idempotently?\n\nA) Always APPEND — duplicates can be deduplicated later\nB) MERGE (UPSERT) on event_id: if the event_id already exists update the row, otherwise insert — Delta Lake MERGE ensures idempotent writes regardless of duplicate source records\nC) DELETE all records for today then re-INSERT the full day's data\nD) Use INSERT IGNORE to skip duplicate event_ids",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.8 DATA LAKES, LAKEHOUSES & DELTA LAKE ─────────────────
            [
                'q' => "A data lake with 500 TB of raw data has no data catalogue. A new analyst asks: 'Where is last year's customer purchase data?' What is the fundamental problem and professional solution?",
                'opts' => [
                    ['The problem is insufficient storage — add more nodes', false],
                    ['Without a data catalogue (e.g. AWS Glue Data Catalog, Apache Atlas), datasets have no discoverable metadata, schema, owner, or lineage. Solution: implement automated metadata crawling, enforce naming conventions, and require schema-on-write for new datasets', true],
                    ['The analyst should use SHOW TABLES in SQL to list all datasets', false],
                    ['The problem is that data lakes cannot store purchase data — use a warehouse instead', false],
                ],
            ],
            [
                'q' => "What does the following Delta Lake command do and what problem does it solve?\n\nspark.sql('OPTIMIZE delta.`/data/events` ZORDER BY (user_id, event_date)')",
                'opts' => [
                    ['Compresses all Delta files to ZIP format', false],
                    ['OPTIMIZE compacts small files into larger ones. ZORDER BY co-locates related data (rows with the same user_id and event_date) in the same files — queries filtering on user_id or event_date skip more files via data-skipping statistics, dramatically reducing I/O for selective queries', true],
                    ['ZORDER sorts the Delta table in ascending order by user_id and event_date', false],
                    ['OPTIMIZE rebuilds the Delta transaction log from scratch', false],
                ],
            ],
            [
                'q' => "Apache Iceberg and Delta Lake are both lakehouse table formats. A key advantage of Iceberg over Delta Lake for multi-engine environments is:\n\nA) Iceberg supports only Spark; Delta Lake supports all engines\nB) Iceberg has an open specification with native support across Spark, Flink, Trino, Presto, Hive, and Snowflake — Delta Lake historically had stronger Spark integration but weaker cross-engine support outside Databricks\nC) Delta Lake does not support ACID transactions; Iceberg does\nD) Iceberg stores data in row format; Delta Lake uses columnar Parquet",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.9 CLOUD SECURITY, GOVERNANCE & COMPLIANCE ─────────────
            [
                'q' => "A GCP Cloud Storage bucket accidentally has 'allUsers' (public internet) read access granted. The bucket contains 10 TB of customer PII. The security team detects this 3 days later. List the correct immediate response actions in order.",
                'opts' => [
                    ['Delete the bucket → file an insurance claim → notify regulators', false],
                    ['(1) Immediately revoke the allUsers IAM binding, (2) enable bucket-level access audit logging to determine what was accessed, (3) assess breach scope, (4) notify affected customers and regulators per GDPR/CCPA timelines, (5) conduct root-cause analysis and implement preventative controls (e.g. org policy to deny public access)', true],
                    ['Rotate all API keys → rebuild the bucket from backup → restore access', false],
                    ['Do nothing until a formal penetration test confirms the exposure', false],
                ],
            ],
            [
                'q' => "Row-level security (RLS) in a cloud data warehouse ensures that:\n\nA) Each row is encrypted with a unique key\nB) Different users querying the same table see only the rows they are authorised to access — e.g. a regional sales manager sees only their region's rows from the global sales table\nC) Rows can only be read, never updated or deleted\nD) Each row is stored in a separate physical file",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "What is a 'VPC endpoint' for S3 in AWS and why is it important for data security?\n\nA) A VPC endpoint allows S3 traffic to remain within the AWS network (not traverse the public internet), preventing data exfiltration risk and reducing egress costs\nB) A VPC endpoint makes S3 buckets publicly accessible within a VPC\nC) A VPC endpoint is a way to increase S3 read/write throughput\nD) A VPC endpoint is an S3 feature that validates file checksums",
                'opts' => [
                    ['A', true],
                    ['B', false],
                    ['C', false],
                    ['D', false],
                ],
            ],

            // ── 22.10 MLOps & DEPLOYING ML AT SCALE ──────────────────────
            [
                'q' => "The following FastAPI ML serving code has a critical performance bug for high-traffic inference:\n\nfrom fastapi import FastAPI\nimport pickle\napp = FastAPI()\n\n@app.post('/predict')\ndef predict(features: dict):\n    with open('model.pkl', 'rb') as f:\n        model = pickle.load(f)   # Bug\n    return {'prediction': model.predict([list(features.values())])[0]}",
                'opts' => [
                    ['pickle.load is deprecated in Python 3.11', false],
                    ['Loading the model from disk on every request adds ~100ms of I/O overhead per call. The model should be loaded once at startup (module level or lifespan event) and reused across all requests', true],
                    ['FastAPI cannot serve pickle models — use ONNX instead', false],
                    ['The model should be reloaded every 10 requests to stay fresh', false],
                ],
            ],
            [
                'q' => "What is 'shadow mode' deployment in MLOps and how does it differ from A/B testing?",
                'opts' => [
                    ['Shadow mode and A/B testing are identical strategies', false],
                    ['In shadow mode, the new model receives a copy of all production traffic and generates predictions, but its outputs are NEVER shown to users — predictions are logged for offline evaluation only. A/B testing routes real users to either model and measures live outcomes. Shadow mode is safer for high-stakes systems', true],
                    ['Shadow mode shows predictions only to internal users; A/B testing is for external users', false],
                    ['Shadow mode is only used for recommendation systems, not classifiers', false],
                ],
            ],
            [
                'q' => "A recommendation model is retrained weekly in a Kubeflow pipeline. After a retraining run, the offline metrics (AUC, NDCG) improve but online A/B test metrics (CTR, revenue) decline. What is the most likely cause?\n\nA) The model is overfitting to the training data\nB) Offline metric improvement does not guarantee online improvement — this often indicates a training-serving skew (feature computation differs between offline training and online serving) or optimising for the wrong objective. Root cause analysis: compare offline/online feature distributions and audit the serving feature pipeline\nC) The A/B test is too short to reach statistical significance\nD) Kubeflow incorrectly serialised the model weights",
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => "A Kubernetes-deployed ML model API experiences CPU throttling. The pod's resource configuration is:\n\nresources:\n  requests:\n    cpu: '500m'\n    memory: '2Gi'\n  limits:\n    cpu: '500m'\n    memory: '4Gi'\n\nWhat is wrong with this configuration and what is the fix?",
                'opts' => [
                    ['Memory limits should equal requests', false],
                    ['Setting cpu limits = cpu requests (both 500m) causes CPU throttling — even if the node has spare CPU, the pod is throttled at 500m. Fix: set cpu limit higher than requests (e.g. limit: 2000m) to allow bursting during inference spikes, or remove the cpu limit entirely', true],
                    ['500m means 500 megahertz, which is too slow for ML inference', false],
                    ['The pod needs a GPU resource request, not a CPU request', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 22 — Big Data & Cloud Computing (Advanced).");
    }
}