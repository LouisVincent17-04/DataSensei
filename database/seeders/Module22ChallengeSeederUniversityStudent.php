<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module22ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 22 — Big Data & Cloud Computing (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Big Data & Cloud Computing',
            'description'           => 'Move beyond definitions — compare architectures, evaluate design trade-offs, trace through pipeline logic, and interpret the behaviour of distributed systems across the modern data stack.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 22,
        ]);

        $this->command->info("Seeding 50 university-level questions on Big Data & Cloud Computing...");

        $qaData = [

            // ── 22.1 WHAT IS BIG DATA? THE 5 Vs ─────────────────────────
            [
                'q' => 'A hospital system collects patient records, MRI images, real-time heart monitor readings, and doctor\'s text notes. Which of the "5 Vs" is primarily illustrated by having these four very different data formats?',
                'opts' => [
                    ['Volume — because there is a lot of medical data', false],
                    ['Variety — structured records, unstructured images, streaming signals, and free text represent different data types', true],
                    ['Velocity — because heart monitors send data fast', false],
                    ['Veracity — because medical data must be accurate', false],
                ],
            ],
            [
                'q' => 'The "Veracity" V of Big Data refers to:',
                'opts' => [
                    ['The speed at which data arrives', false],
                    ['The trustworthiness, quality, and accuracy of data — including noise, biases, and inconsistencies', true],
                    ['The monetary value derived from data insights', false],
                    ['The variety of file formats in a data lake', false],
                ],
            ],
            [
                'q' => 'The "Value" V of Big Data is considered the most important by some practitioners because:',
                'opts' => [
                    ['Value measures how much storage space the data occupies', false],
                    ['Having large, fast, and varied data means nothing if the business cannot extract actionable insights and tangible benefit from it', true],
                    ['Value determines the velocity at which data is processed', false],
                    ['Value is a measure of data encryption strength', false],
                ],
            ],
            [
                'q' => 'A ride-sharing app processes 50,000 GPS location pings per second from active drivers. Which V is most prominently challenged here?',
                'opts' => [
                    ['Variety — different GPS devices send data differently', false],
                    ['Velocity — the system must ingest and act on 50,000 events per second in real time', true],
                    ['Volume — because GPS files are large', false],
                    ['Veracity — GPS signals are sometimes inaccurate', false],
                ],
            ],
            [
                'q' => 'Traditional relational databases struggle with Big Data primarily because they:',
                'opts' => [
                    ['Cannot store numbers or strings', false],
                    ['Were designed for structured data on a single machine — they cannot horizontally scale across hundreds of commodity servers to handle petabyte-scale data', true],
                    ['Do not support SQL queries', false],
                    ['Are incompatible with Python', false],
                ],
            ],

            // ── 22.2 CLOUD COMPUTING: IaaS, PaaS, SaaS ───────────────────
            [
                'q' => 'A startup rents virtual machines from AWS EC2, installs their own operating system, databases, and custom software. Which cloud model are they using?',
                'opts' => [
                    ['SaaS — they are using ready-made software', false],
                    ['PaaS — the platform manages the OS for them', false],
                    ['IaaS — they rent raw compute infrastructure and manage everything above the hardware themselves', true],
                    ['FaaS — serverless function execution', false],
                ],
            ],
            [
                'q' => 'Google App Engine and AWS Elastic Beanstalk are examples of which cloud model?',
                'opts' => [
                    ['IaaS — because they run on real machines', false],
                    ['PaaS — the provider manages the runtime, OS patching, and scaling; the developer only deploys code', true],
                    ['SaaS — because developers use them through a browser', false],
                    ['DaaS — because they host databases', false],
                ],
            ],
            [
                'q' => 'What is the difference between a "public cloud" and a "private cloud"?',
                'opts' => [
                    ['Public clouds store public data; private clouds store private data', false],
                    ['A public cloud is shared infrastructure operated by a provider (e.g. AWS) and available to any customer; a private cloud is dedicated infrastructure operated for a single organisation', true],
                    ['Public clouds are free; private clouds are paid', false],
                    ['Private clouds are always on-premise; public clouds are always off-premise', false],
                ],
            ],
            [
                'q' => 'What is a "hybrid cloud" architecture?',
                'opts' => [
                    ['A cloud that uses two different programming languages', false],
                    ['A combination of on-premise/private cloud and public cloud, connected to share data and workloads', true],
                    ['A cloud service that is half IaaS and half SaaS', false],
                    ['A cloud that processes half of data in real time and half in batch', false],
                ],
            ],
            [
                'q' => 'Cloud elasticity allows a system to automatically scale resources up during peak demand and scale down during quiet periods. For a data science team, what is the primary financial benefit?',
                'opts' => [
                    ['The team never needs to write efficient code because resources are unlimited', false],
                    ['They avoid paying for idle capacity — paying for large training jobs only when running them, then releasing the resources', true],
                    ['Elasticity guarantees zero latency for all queries', false],
                    ['Elasticity automatically optimises machine learning models', false],
                ],
            ],

            // ── 22.3 DISTRIBUTED STORAGE: HDFS & COLUMNAR FORMATS ─────────
            [
                'q' => 'HDFS stores files in blocks (default 128 MB each). A 500 MB file is split into how many full blocks?',
                'opts' => [
                    ['3 full blocks (384 MB) with one partial block (116 MB) — 4 blocks total', true],
                    ['5 blocks of exactly 100 MB each', false],
                    ['1 block — HDFS stores small files together', false],
                    ['500 blocks of 1 MB each', false],
                ],
            ],
            [
                'q' => 'HDFS uses a replication factor (default = 3). A 128 MB block with replication factor 3 actually consumes how much total storage across the cluster?',
                'opts' => [
                    ['128 MB — replication is done virtually', false],
                    ['384 MB — 3 physical copies stored on 3 different data nodes', true],
                    ['256 MB — one primary and one backup copy', false],
                    ['64 MB — HDFS compresses during replication', false],
                ],
            ],
            [
                'q' => 'Apache Parquet is a popular columnar file format in Big Data. A query only needs 2 out of 50 columns. Why is Parquet faster than a row-based format (e.g. CSV) for this query?',
                'opts' => [
                    ['Parquet files are always smaller than CSV files', false],
                    ['Parquet stores each column separately on disk — only the 2 needed columns are read, skipping the other 48; CSV must read entire rows containing all 50 columns', true],
                    ['Parquet is faster because it uses Python instead of Java', false],
                    ['Parquet automatically caches queries in RAM', false],
                ],
            ],
            [
                'q' => 'What is "schema-on-read" (used in data lakes) vs. "schema-on-write" (used in data warehouses)?',
                'opts' => [
                    ['Schema-on-read enforces data structure when reading; schema-on-write enforces it when writing — warehouses validate structure at ingest time, lakes apply schema flexibly at query time', true],
                    ['Schema-on-read means no schema is ever applied', false],
                    ['Schema-on-write means data is always stored as JSON', false],
                    ['They are equivalent terms for the same concept', false],
                ],
            ],

            // ── 22.4 APACHE SPARK ─────────────────────────────────────────
            [
                'q' => 'In Apache Spark, what is the difference between a "transformation" and an "action"?',
                'opts' => [
                    ['Transformations write data to disk; actions keep data in memory', false],
                    ['Transformations are lazy — they define a computation plan without executing it; actions trigger the actual execution and return results', true],
                    ['Transformations use Python; actions use Scala', false],
                    ['There is no difference — both immediately execute', false],
                ],
            ],
            [
                'q' => 'Spark\'s lazy evaluation strategy means:',
                'opts' => [
                    ['Spark is slower than traditional databases', false],
                    ['Spark builds an execution plan (DAG of transformations) and only executes when an action is called — allowing it to optimise the full plan before running', true],
                    ['Spark evaluates data row by row rather than in bulk', false],
                    ['Spark delays writing results until the end of the month', false],
                ],
            ],
            [
                'q' => 'What is a Spark DataFrame compared to an RDD?',
                'opts' => [
                    ['DataFrames are unstructured; RDDs are always structured', false],
                    ['DataFrames are higher-level abstractions with named columns and schema, optimised by the Catalyst query optimiser — RDDs are lower-level distributed collections without schema', true],
                    ['RDDs support SQL queries; DataFrames do not', false],
                    ['DataFrames only work in Java; RDDs work in Python', false],
                ],
            ],
            [
                'q' => 'Spark Structured Streaming allows:',
                'opts' => [
                    ['Processing streaming data using the same DataFrame/SQL API as batch processing', true],
                    ['Streaming only works with Kafka as the data source', false],
                    ['Structured streaming replaces Kafka in all architectures', false],
                    ['Only JSON data can be processed in structured streaming', false],
                ],
            ],
            [
                'q' => 'What is Spark\'s Catalyst Optimizer?',
                'opts' => [
                    ['A chemical process used to speed up server hardware', false],
                    ['A query optimisation engine that transforms and optimises logical query plans into efficient physical execution plans automatically', true],
                    ['A tool for converting Python code to Scala automatically', false],
                    ['A monitoring dashboard for Spark jobs', false],
                ],
            ],

            // ── 22.5 APACHE KAFKA ─────────────────────────────────────────
            [
                'q' => 'Kafka topics are divided into "partitions." What is the purpose of partitions?',
                'opts' => [
                    ['Partitions separate secure and non-secure messages', false],
                    ['Partitions allow a topic to be distributed across multiple brokers, enabling parallel reads and writes for higher throughput', true],
                    ['Partitions compress messages to reduce storage', false],
                    ['Partitions are used only for archiving old messages', false],
                ],
            ],
            [
                'q' => 'What is a Kafka "broker"?',
                'opts' => [
                    ['A financial intermediary that charges fees per Kafka message', false],
                    ['A Kafka server that stores topic partitions and serves producers and consumers', true],
                    ['A Kafka monitoring agent running on each client machine', false],
                    ['A security layer that encrypts Kafka messages', false],
                ],
            ],
            [
                'q' => 'Kafka retains messages for a configurable retention period (e.g. 7 days) even after consumers read them. What does this enable?',
                'opts' => [
                    ['Consumers can only read each message once', false],
                    ['Multiple independent consumer groups can read the same topic at their own pace, and consumers can replay historical messages by resetting their offset', true],
                    ['Producers must wait for all consumers to confirm receipt before sending the next message', false],
                    ['Kafka automatically deletes processed messages to save space', false],
                ],
            ],
            [
                'q' => 'What is a "consumer group" in Kafka?',
                'opts' => [
                    ['A group of Kafka brokers that share the processing load', false],
                    ['A set of consumers that jointly consume a topic — each partition is assigned to exactly one consumer in the group, enabling parallel consumption', true],
                    ['A security group that controls who can read a Kafka topic', false],
                    ['A Kafka plugin that groups messages by their content', false],
                ],
            ],

            // ── 22.6 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => 'What is the difference between OLTP and OLAP databases?',
                'opts' => [
                    ['OLTP handles large analytical queries over historical data; OLAP handles many small real-time transactions', false],
                    ['OLTP (Online Transaction Processing) handles many small real-time read/write operations; OLAP (Online Analytical Processing) handles complex analytical queries over large historical datasets', true],
                    ['OLTP stores unstructured data; OLAP stores structured data only', false],
                    ['They are the same type of database with different names', false],
                ],
            ],
            [
                'q' => 'Snowflake\'s "separation of storage and compute" architecture means:',
                'opts' => [
                    ['Snowflake stores compute results but not raw data', false],
                    ['Storage (data) and compute (query execution) are scaled independently — you can run a large query with more compute without changing how much data you store', true],
                    ['Snowflake uses separate physical buildings for storage and servers', false],
                    ['Queries in Snowflake are always run on separate machines from where data is stored', false],
                ],
            ],
            [
                'q' => 'Google BigQuery is described as "serverless." What does this mean for the data analyst?',
                'opts' => [
                    ['BigQuery has no servers — it runs on the analyst\'s laptop', false],
                    ['The analyst does not need to provision, manage, or scale servers — they simply run SQL queries and BigQuery handles infrastructure automatically', true],
                    ['BigQuery can only run queries of fewer than 10 seconds', false],
                    ['Serverless means BigQuery is always free to use', false],
                ],
            ],

            // ── 22.7 DATA PIPELINES, ETL/ELT & AIRFLOW ───────────────────
            [
                'q' => 'ELT (Extract, Load, Transform) differs from ETL by:',
                'opts' => [
                    ['ELT skips the transformation step entirely', false],
                    ['In ELT, raw data is loaded directly into the destination (e.g. a data warehouse) first, then transformed using the warehouse\'s own compute power — enabled by powerful cloud warehouses', true],
                    ['ELT only works with real-time data', false],
                    ['ELT extracts data twice for redundancy', false],
                ],
            ],
            [
                'q' => 'An Airflow DAG has tasks A → B → C where → means "must complete before." If Task B fails, what happens to Task C?',
                'opts' => [
                    ['Task C runs immediately regardless of B\'s status', false],
                    ['Task C is skipped or stays in a waiting state — Airflow respects task dependencies and will not run downstream tasks after an upstream failure (unless configured to)', true],
                    ['The entire DAG is automatically restarted from Task A', false],
                    ['Airflow runs Task C in a separate DAG run', false],
                ],
            ],
            [
                'q' => 'In Airflow, what is a "schedule interval"?',
                'opts' => [
                    ['The time limit for each individual task to complete', false],
                    ['How often the DAG is triggered to run — e.g. daily, hourly, or a custom cron expression', true],
                    ['The number of tasks that run in parallel', false],
                    ['The interval between Airflow version upgrades', false],
                ],
            ],
            [
                'q' => 'dbt (data build tool) is used in the modern data stack primarily for:',
                'opts' => [
                    ['Ingesting raw data from source systems into a data lake', false],
                    ['Writing and managing SQL transformations inside a data warehouse, with version control, testing, and documentation built in', true],
                    ['Orchestrating pipeline schedules like Airflow', false],
                    ['Streaming data in real time like Kafka', false],
                ],
            ],

            // ── 22.8 DATA LAKES, LAKEHOUSES & DELTA LAKE ─────────────────
            [
                'q' => 'A common problem with data lakes is becoming a "data swamp." What causes this?',
                'opts' => [
                    ['Data lakes store too much clean, structured data', false],
                    ['Without governance, cataloguing, and metadata management, data lakes fill with poorly organised, undocumented raw files that are difficult to find and trust', true],
                    ['Data lakes automatically delete files after 30 days', false],
                    ['Data swamps occur when the lake uses too many columnar file formats', false],
                ],
            ],
            [
                'q' => 'Delta Lake provides ACID transactions to data lakes. In this context, what does "ACID" stand for?',
                'opts' => [
                    ['Atomicity, Consistency, Isolation, Durability', true],
                    ['Automated, Compressed, Indexed, Distributed', false],
                    ['Access, Control, Identity, Directory', false],
                    ['Aggregated, Columnar, Integrated, Durable', false],
                ],
            ],
            [
                'q' => 'Delta Lake\'s "time travel" feature allows you to:',
                'opts' => [
                    ['Schedule pipelines to run at different times in different time zones', false],
                    ['Query a previous version of a table by specifying a version number or timestamp — useful for auditing and recovering from accidental changes', true],
                    ['Stream data back in time to reprocess historical events', false],
                    ['Preview future data using predictive analytics', false],
                ],
            ],

            // ── 22.9 CLOUD SECURITY, GOVERNANCE & COMPLIANCE ─────────────
            [
                'q' => 'In cloud security, what is "IAM"?',
                'opts' => [
                    ['Internet Access Management — controlling which websites employees can visit', false],
                    ['Identity and Access Management — controlling who (users, services) can access which cloud resources and what actions they can perform', true],
                    ['Integrated Application Monitoring — tracking application performance', false],
                    ['Infrastructure Automation Manager — a tool for provisioning servers', false],
                ],
            ],
            [
                'q' => 'What is the difference between "authentication" and "authorisation" in cloud security?',
                'opts' => [
                    ['They are the same concept — both verify identity', false],
                    ['Authentication verifies WHO you are (e.g. username/password); authorisation determines WHAT you are allowed to do (e.g. read vs. write permissions)', true],
                    ['Authentication is only for humans; authorisation is only for machines', false],
                    ['Authorisation happens before authentication in all cloud systems', false],
                ],
            ],
            [
                'q' => 'Data "lineage" in data governance tracks:',
                'opts' => [
                    ['The ancestral origins of the company that owns the data', false],
                    ['Where data came from, how it was transformed, and where it flows — providing traceability and auditability across a data pipeline', true],
                    ['The physical cables connecting data centre servers', false],
                    ['The order in which employees were granted data access', false],
                ],
            ],

            // ── 22.10 MLOps & DEPLOYING ML AT SCALE ──────────────────────
            [
                'q' => 'What is the difference between "batch inference" and "real-time inference" in ML deployment?',
                'opts' => [
                    ['Batch inference is less accurate than real-time inference', false],
                    ['Batch inference runs predictions on a large dataset periodically (e.g. nightly); real-time inference serves individual predictions on demand with low latency (e.g. per API request)', true],
                    ['Real-time inference only works for image classification', false],
                    ['Batch inference uses GPU; real-time inference uses CPU only', false],
                ],
            ],
            [
                'q' => 'A/B testing in ML deployment refers to:',
                'opts' => [
                    ['Testing two different Python versions for compatibility', false],
                    ['Routing a fraction of live traffic to a new model version (B) while the current model (A) handles the rest, comparing performance on real users before full rollout', true],
                    ['Running the same model twice to check for consistency', false],
                    ['A testing framework for writing unit tests for ML code', false],
                ],
            ],
            [
                'q' => 'What is "feature store" in the MLOps ecosystem?',
                'opts' => [
                    ['An online marketplace for purchasing ML datasets', false],
                    ['A centralised repository for storing, sharing, and serving ML features — ensuring consistency between training and inference time feature computation', true],
                    ['A data warehouse specifically for storing model weights', false],
                    ['A tool for automatically selecting the best features for a model', false],
                ],
            ],
            [
                'q' => 'MLflow is used by data scientists primarily for:',
                'opts' => [
                    ['Deploying models to Kubernetes clusters', false],
                    ['Tracking experiments — logging parameters, metrics, and artefacts for each ML training run to compare and reproduce results', true],
                    ['Building data pipelines with Apache Spark', false],
                    ['Streaming model predictions to a Kafka topic', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 22 — Big Data & Cloud Computing (University Student).");
    }
}