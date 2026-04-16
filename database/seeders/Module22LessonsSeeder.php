<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module22LessonsSeeder
 * Seeds lessons for Module 22: Big Data & Cloud Computing.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module22LessonsSeeder
 */
class Module22LessonsSeeder extends Seeder
{
    public function run()
    {
        $bigDataModule = Module::where('order_index', 22)->firstOrFail();
        Lesson::where('module_id', $bigDataModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.1 — What Is Big Data? The 5 Vs, Scale & the Modern Data Stack
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is Big Data?</h2>
<p>The term <strong>Big Data</strong> describes datasets so large, fast-moving, or structurally complex that traditional database and analytics tools cannot process them effectively within acceptable timeframes or budgets. The distinction between "regular data" and "big data" is not a fixed threshold in gigabytes — it is a practical engineering challenge. When your PostgreSQL query takes 14 hours to run and your business needs the answer in 30 minutes, you have a big data problem regardless of whether your dataset is 50 GB or 50 TB.</p>

<p>Big data is not a recent phenomenon, but the scale has accelerated dramatically. In 2024, humanity generates approximately <strong>2.5 quintillion bytes of data every day</strong> — from social media interactions, IoT sensors, financial transactions, genomic sequencers, satellite imagery, mobile telemetry, and countless other sources. Every time you search the web, stream a video, tap your phone, or swipe a credit card, you contribute to a data stream that must be ingested, stored, processed, and analysed in real time at planetary scale. The infrastructure that makes this possible is what this module is about.</p>

<h3>The 5 Vs of Big Data</h3>
<p>The canonical framework for characterising big data uses five dimensions — the "<strong>5 Vs</strong>" — each of which poses distinct engineering and architectural challenges that traditional tools cannot address.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — The 5 Vs: Characterising Big Data Challenges</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># The 5 Vs of Big Data — structured as engineering challenges</span>
<span style="color:#93c5fd;">five_vs</span> = {
    <span style="color:#a7f3d0;">"Volume"</span>: {
        <span style="color:#a7f3d0;">"definition"</span>: <span style="color:#a7f3d0;">"Sheer size of data — petabytes to exabytes"</span>,
        <span style="color:#a7f3d0;">"example"</span>:    <span style="color:#a7f3d0;">"Meta stores 100+ PB of user data; Waymo generates 40 TB/day per car"</span>,
        <span style="color:#a7f3d0;">"challenge"</span>:  <span style="color:#a7f3d0;">"Cannot fit in RAM or a single machine's disk"</span>,
        <span style="color:#a7f3d0;">"solution"</span>:   <span style="color:#a7f3d0;">"Distributed file systems (HDFS, S3), columnar storage (Parquet)"</span>,
    },
    <span style="color:#a7f3d0;">"Velocity"</span>: {
        <span style="color:#a7f3d0;">"definition"</span>: <span style="color:#a7f3d0;">"Speed at which data is generated and must be processed"</span>,
        <span style="color:#a7f3d0;">"example"</span>:    <span style="color:#a7f3d0;">"NYSE: 10B+ messages/day; Twitter: 6,000 tweets/second"</span>,
        <span style="color:#a7f3d0;">"challenge"</span>:  <span style="color:#a7f3d0;">"Batch jobs run too slowly; need sub-second latency"</span>,
        <span style="color:#a7f3d0;">"solution"</span>:   <span style="color:#a7f3d0;">"Stream processing (Kafka, Spark Streaming, Flink)"</span>,
    },
    <span style="color:#a7f3d0;">"Variety"</span>: {
        <span style="color:#a7f3d0;">"definition"</span>: <span style="color:#a7f3d0;">"Diversity of data formats and sources"</span>,
        <span style="color:#a7f3d0;">"example"</span>:    <span style="color:#a7f3d0;">"JSON logs + CSV exports + images + audio + sensor binary + HTML"</span>,
        <span style="color:#a7f3d0;">"challenge"</span>:  <span style="color:#a7f3d0;">"Relational DBs only handle structured tabular data"</span>,
        <span style="color:#a7f3d0;">"solution"</span>:   <span style="color:#a7f3d0;">"Data lakes, schema-on-read, NoSQL, document stores"</span>,
    },
    <span style="color:#a7f3d0;">"Veracity"</span>: {
        <span style="color:#a7f3d0;">"definition"</span>: <span style="color:#a7f3d0;">"Trustworthiness, quality, and accuracy of data"</span>,
        <span style="color:#a7f3d0;">"example"</span>:    <span style="color:#a7f3d0;">"IoT sensors drift; user-entered data has typos; logs have gaps"</span>,
        <span style="color:#a7f3d0;">"challenge"</span>:  <span style="color:#a7f3d0;">"Garbage in, garbage out — bad decisions from dirty data"</span>,
        <span style="color:#a7f3d0;">"solution"</span>:   <span style="color:#a7f3d0;">"Data quality frameworks, Great Expectations, data contracts"</span>,
    },
    <span style="color:#a7f3d0;">"Value"</span>: {
        <span style="color:#a7f3d0;">"definition"</span>: <span style="color:#a7f3d0;">"Business or scientific worth extracted from the data"</span>,
        <span style="color:#a7f3d0;">"example"</span>:    <span style="color:#a7f3d0;">"Netflix saves $1B/yr from recommendation personalisation"</span>,
        <span style="color:#a7f3d0;">"challenge"</span>:  <span style="color:#a7f3d0;">"Most raw big data is worthless without the right processing"</span>,
        <span style="color:#a7f3d0;">"solution"</span>:   <span style="color:#a7f3d0;">"Right architecture + domain expertise + ML to surface insights"</span>,
    },
}

<span style="color:#c4b5fd;">for</span> v, info <span style="color:#c4b5fd;">in</span> five_vs.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{'─'*60}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {v.upper()}: {info['definition']}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Example  : {info['example']}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Challenge: {info['challenge']}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  Solution : {info['solution']}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>────────────────────────────────────────────────────────────
  VOLUME: Sheer size of data — petabytes to exabytes
  Example  : Meta stores 100+ PB of user data; Waymo generates 40 TB/day per car
  Challenge: Cannot fit in RAM or a single machine's disk
  Solution : Distributed file systems (HDFS, S3), columnar storage (Parquet)

────────────────────────────────────────────────────────────
  VELOCITY: Speed at which data is generated and must be processed
  Example  : NYSE: 10B+ messages/day; Twitter: 6,000 tweets/second
  Challenge: Batch jobs run too slowly; need sub-second latency
  Solution : Stream processing (Kafka, Spark Streaming, Flink)

────────────────────────────────────────────────────────────
  VARIETY: Diversity of data formats and sources
  Example  : JSON logs + CSV exports + images + audio + sensor binary + HTML
  Challenge: Relational DBs only handle structured tabular data
  Solution : Data lakes, schema-on-read, NoSQL, document stores

────────────────────────────────────────────────────────────
  VERACITY: Trustworthiness, quality, and accuracy of data
  Example  : IoT sensors drift; user-entered data has typos; logs have gaps
  Challenge: Garbage in, garbage out — bad decisions from dirty data
  Solution : Data quality frameworks, Great Expectations, data contracts

────────────────────────────────────────────────────────────
  VALUE: Business or scientific worth extracted from the data
  Example  : Netflix saves $1B/yr from recommendation personalisation
  Challenge: Most raw big data is worthless without the right processing
  Solution : Right architecture + domain expertise + ML to surface insights</div>
  </div>
</div>

<h3>The Modern Data Stack</h3>
<p>The <strong>Modern Data Stack (MDS)</strong> is the collection of cloud-native, best-of-breed tools that have replaced monolithic on-premises data warehouses as the dominant approach to analytics infrastructure. Each layer of the stack has a specific responsibility: ingestion (moving data in), storage (keeping it durably and cheaply), transformation (cleaning and modelling it), and serving (making it available for BI tools, APIs, and ML models).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Modern Data Stack Layers & Tool Examples</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ┌──────────────────────────────────────────────────────────────┐
# │                    MODERN DATA STACK                         │
# │                                                              │
# │  SOURCES      → Databases, SaaS APIs, IoT, Clickstreams      │
# │      ↓                                                       │
# │  INGESTION    → Fivetran, Airbyte, Kafka, AWS Glue           │
# │      ↓                                                       │
# │  STORAGE      → AWS S3 / GCS / ADLS  (Data Lake)            │
# │                  Snowflake / BigQuery / Redshift (Warehouse) │
# │      ↓                                                       │
# │  TRANSFORM    → dbt (SQL transforms), Spark, Airflow (orch.) │
# │      ↓                                                       │
# │  SERVING      → Looker, Metabase (BI), FastAPI (APIs),       │
# │                  MLflow / Sagemaker (ML models)              │
# └──────────────────────────────────────────────────────────────┘</span>

<span style="color:#93c5fd;">stack_layers</span> = [
    (<span style="color:#a7f3d0;">"Sources"</span>,    <span style="color:#a7f3d0;">"Postgres, Salesforce, Stripe API, Kafka topics, S3 events"</span>),
    (<span style="color:#a7f3d0;">"Ingestion"</span>,  <span style="color:#a7f3d0;">"Fivetran (managed ELT), Airbyte (open-source), Kafka Connect"</span>),
    (<span style="color:#a7f3d0;">"Storage"</span>,    <span style="color:#a7f3d0;">"S3/GCS (lake), Snowflake/BigQuery/Redshift (warehouse)"</span>),
    (<span style="color:#a7f3d0;">"Transform"</span>,  <span style="color:#a7f3d0;">"dbt (SQL models), Apache Spark, dbt Core + Airflow"</span>),
    (<span style="color:#a7f3d0;">"Serving"</span>,    <span style="color:#a7f3d0;">"Looker, Metabase, Superset (BI), FastAPI, SageMaker (ML)"</span>),
]

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Layer':<12} {'Tools & Examples'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 75</span>)
<span style="color:#c4b5fd;">for</span> layer, tools <span style="color:#c4b5fd;">in</span> stack_layers:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{layer:<12} {tools}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Layer        Tools & Examples
---------------------------------------------------------------------------
Sources      Postgres, Salesforce, Stripe API, Kafka topics, S3 events
Ingestion    Fivetran (managed ELT), Airbyte (open-source), Kafka Connect
Storage      S3/GCS (lake), Snowflake/BigQuery/Redshift (warehouse)
Transform    dbt (SQL models), Apache Spark, dbt Core + Airflow
Serving      Looker, Metabase, Superset (BI), FastAPI, SageMaker (ML)</div>
  </div>
</div>

<h3>Batch vs. Stream Processing: The Fundamental Architectural Choice</h3>
<p><strong>Batch processing</strong> collects data over a period of time and processes it all at once — like running a nightly ETL job that aggregates yesterday's sales. It is simple, fault-tolerant, and cheap, but introduces latency measured in hours or days. <strong>Stream processing</strong> processes data continuously as it arrives — like detecting fraud on a credit card transaction within 200 milliseconds of it being made. It is architecturally complex and expensive, but enables real-time decisions. Most modern data platforms support both paradigms, often using the <strong>Lambda Architecture</strong> (separate batch and streaming layers) or the newer <strong>Kappa Architecture</strong> (streaming only, replaying history to produce batch results).</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.1 What Is Big Data? The 5 Vs, Scale & the Modern Data Stack',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L22_1', [
                ['q' => 'Which of the 5 Vs refers to the speed at which data is generated and must be processed?', 'opts' => ['Volume', 'Veracity', 'Velocity', 'Value'], 'ans' => 2, 'exp' => 'Velocity describes the rate of data generation and the speed at which it must be processed. NYSE generates billions of messages per day; Twitter sees 6,000 tweets per second. Traditional batch jobs cannot keep up — stream processing systems like Kafka and Flink are required.'],
                ['q' => 'In the Modern Data Stack, what is the primary responsibility of the "Transform" layer?', 'opts' => ['Moving data from source systems into storage', 'Cleaning, modelling, and structuring raw data into analytics-ready tables', 'Serving data to end users via dashboards and APIs', 'Replicating data across cloud regions for redundancy'], 'ans' => 1, 'exp' => 'The Transform layer (tools: dbt, Spark) takes raw data that has been loaded into a warehouse or lake and applies business logic to produce clean, modelled, analytics-ready tables. This includes deduplication, joining, aggregation, and applying business definitions like "revenue" or "active user".'],
                ['q' => 'The "Veracity" dimension of big data refers to:', 'opts' => ['The commercial value extracted from the data', 'The variety of formats the data arrives in', 'The trustworthiness, accuracy, and quality of data', 'The volume of data generated per second'], 'ans' => 2, 'exp' => 'Veracity addresses data quality — are the data records accurate, complete, consistent, and trustworthy? IoT sensors drift over time, user-entered data contains typos, and event logs have gaps. Poor veracity leads to garbage-in-garbage-out: correct-looking but fundamentally wrong analytical conclusions.'],
                ['q' => 'What distinguishes stream processing from batch processing?', 'opts' => ['Stream processing uses SQL; batch processing uses Python', 'Batch processing runs continuously; stream processing runs once per day', 'Stream processing handles data continuously as it arrives enabling real-time decisions; batch processing collects data over time and processes it all at once', 'Stream processing is always cheaper than batch processing'], 'ans' => 2, 'exp' => 'Batch processing: collect data over a period, then process (e.g., nightly ETL). Latency measured in hours. Stream processing: process events as they arrive (e.g., fraud detection in 200ms). Latency measured in milliseconds. The right choice depends on how quickly the business needs the result.'],
                ['q' => 'The Lambda Architecture addresses which fundamental challenge?', 'opts' => ['The need to run Python in a distributed environment', 'The need to support both low-latency streaming results and high-accuracy batch results simultaneously', 'The need to store structured and unstructured data in the same database', 'The need to replicate data across multiple cloud providers'], 'ans' => 1, 'exp' => "The Lambda Architecture maintains two separate processing layers: a batch layer (high-latency, high-accuracy, reprocesses all historical data) and a speed layer (low-latency, approximate, processes only recent data). A serving layer merges results. It's complex to maintain but historically necessary before unified stream-batch engines like Flink and Spark Structured Streaming matured."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.2 — Cloud Computing Fundamentals: IaaS, PaaS, SaaS & the Major Providers
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Cloud Computing Fundamentals</h2>
<p><strong>Cloud computing</strong> is the on-demand delivery of computing resources — servers, storage, databases, networking, software, analytics, and intelligence — over the internet, with pay-as-you-go pricing. Instead of buying and maintaining physical data centres, organisations rent capacity from cloud providers who run and maintain the infrastructure at planetary scale. The three dominant cloud providers — <strong>Amazon Web Services (AWS)</strong>, <strong>Microsoft Azure</strong>, and <strong>Google Cloud Platform (GCP)</strong> — collectively host the majority of the world's internet-facing applications and data workloads.</p>

<p>The shift from on-premises to cloud is not merely about cost. It fundamentally changes how engineering teams operate: capacity can be provisioned in seconds rather than weeks, software-defined infrastructure enables repeatability and version control, global distribution is a configuration option rather than a capital project, and failure modes are handled by the provider rather than your operations team. Understanding cloud computing is a prerequisite for building any modern big data system.</p>

<h3>The Three Service Models: IaaS, PaaS, SaaS</h3>
<p>Cloud services exist on a spectrum of abstraction. The higher you go, the less infrastructure you manage — but also the less control you have. Choosing the right level of abstraction for each workload is one of the most important architectural decisions in cloud engineering.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — IaaS vs PaaS vs SaaS: Responsibility Model</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ── What YOU manage vs what the PROVIDER manages ──────────────────────────
#
# Component         On-Prem    IaaS       PaaS       SaaS
# ─────────────────────────────────────────────────────────────────────────
# Applications       YOU        YOU        YOU       PROVIDER
# Data               YOU        YOU        YOU       PROVIDER
# Runtime            YOU        YOU       PROVIDER   PROVIDER
# Middleware         YOU        YOU       PROVIDER   PROVIDER
# OS                 YOU        YOU       PROVIDER   PROVIDER
# Virtualisation     YOU       PROVIDER   PROVIDER   PROVIDER
# Servers            YOU       PROVIDER   PROVIDER   PROVIDER
# Storage            YOU       PROVIDER   PROVIDER   PROVIDER
# Networking         YOU       PROVIDER   PROVIDER   PROVIDER
# ─────────────────────────────────────────────────────────────────────────</span>

<span style="color:#93c5fd;">models</span> = {
    <span style="color:#a7f3d0;">"IaaS (Infra as a Service)"</span>: {
        <span style="color:#a7f3d0;">"examples"</span>: <span style="color:#a7f3d0;">"AWS EC2, Azure VMs, GCP Compute Engine"</span>,
        <span style="color:#a7f3d0;">"you_manage"</span>: <span style="color:#a7f3d0;">"OS, runtime, apps, data"</span>,
        <span style="color:#a7f3d0;">"use_case"</span>:  <span style="color:#a7f3d0;">"Maximum flexibility; lift-and-shift migrations"</span>,
    },
    <span style="color:#a7f3d0;">"PaaS (Platform as a Service)"</span>: {
        <span style="color:#a7f3d0;">"examples"</span>: <span style="color:#a7f3d0;">"AWS RDS, Azure App Service, GCP Cloud Run, Heroku"</span>,
        <span style="color:#a7f3d0;">"you_manage"</span>: <span style="color:#a7f3d0;">"Applications and data only"</span>,
        <span style="color:#a7f3d0;">"use_case"</span>:  <span style="color:#a7f3d0;">"Developer productivity; managed databases & runtimes"</span>,
    },
    <span style="color:#a7f3d0;">"SaaS (Software as a Service)"</span>: {
        <span style="color:#a7f3d0;">"examples"</span>: <span style="color:#a7f3d0;">"Snowflake, Databricks, Salesforce, Google Workspace"</span>,
        <span style="color:#a7f3d0;">"you_manage"</span>: <span style="color:#a7f3d0;">"Configuration and data input/output only"</span>,
        <span style="color:#a7f3d0;">"use_case"</span>:  <span style="color:#a7f3d0;">"Fastest time-to-value; minimal ops overhead"</span>,
    },
}

<span style="color:#c4b5fd;">for</span> model, info <span style="color:#c4b5fd;">in</span> models.items():
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\n{model}"</span>)
    <span style="color:#c4b5fd;">for</span> k, v <span style="color:#c4b5fd;">in</span> info.items():
        <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {k:<12}: {v}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>IaaS (Infra as a Service)
  examples    : AWS EC2, Azure VMs, GCP Compute Engine
  you_manage  : OS, runtime, apps, data
  use_case    : Maximum flexibility; lift-and-shift migrations

PaaS (Platform as a Service)
  examples    : AWS RDS, Azure App Service, GCP Cloud Run, Heroku
  you_manage  : Applications and data only
  use_case    : Developer productivity; managed databases & runtimes

SaaS (Software as a Service)
  examples    : Snowflake, Databricks, Salesforce, Google Workspace
  you_manage  : Configuration and data input/output only
  use_case    : Fastest time-to-value; minimal ops overhead</div>
  </div>
</div>

<h3>Core Cloud Concepts: Regions, AZs, Elasticity & Pricing</h3>
<p>Cloud providers divide their global infrastructure into <strong>Regions</strong> — geographically separate data centre clusters (e.g., "us-east-1", "eu-west-2"). Each Region contains multiple <strong>Availability Zones (AZs)</strong> — physically isolated facilities within the region with independent power, cooling, and networking. Deploying across multiple AZs provides high availability: if one facility fails, the others continue serving requests. <strong>Elasticity</strong> is the cloud's defining superpower — the ability to scale compute resources up or down automatically in response to load, paying only for what you use. A batch job can spin up 500 servers, process a petabyte in an hour, and spin them back down, incurring cost only for that hour.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Cloud Cost Modelling: On-Demand vs. Reserved vs. Spot</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># AWS EC2 pricing tiers for a c5.4xlarge (16 vCPU, 32 GB RAM)</span>
<span style="color:#93c5fd;">instance_type</span> = <span style="color:#a7f3d0;">"c5.4xlarge"</span>
<span style="color:#93c5fd;">pricing</span> = {
    <span style="color:#a7f3d0;">"on_demand"</span>:         <span style="color:#fcd34d;">0.68</span>,   <span style="color:#6b7280;"># $/hr — pay as you go, no commitment</span>
    <span style="color:#a7f3d0;">"reserved_1yr"</span>:      <span style="color:#fcd34d;">0.425</span>,  <span style="color:#6b7280;"># $/hr — 1-year commitment, ~37% savings</span>
    <span style="color:#a7f3d0;">"reserved_3yr"</span>:      <span style="color:#fcd34d;">0.272</span>,  <span style="color:#6b7280;"># $/hr — 3-year commitment, ~60% savings</span>
    <span style="color:#a7f3d0;">"spot"</span>:              <span style="color:#fcd34d;">0.204</span>,  <span style="color:#6b7280;"># $/hr — spare capacity, ~70% savings, interruptible</span>
}

<span style="color:#93c5fd;">hours_per_month</span> = <span style="color:#fcd34d;">730</span>
<span style="color:#93c5fd;">num_instances</span>  = <span style="color:#fcd34d;">10</span>   <span style="color:#6b7280;"># a small Spark cluster</span>

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Instance: {instance_type} × {num_instances}  |  {hours_per_month}h/month"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Pricing Tier':<20} {'$/hr/inst':>10} {'Monthly Cost':>14} {'Annual Cost':>13}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 60</span>)

<span style="color:#c4b5fd;">for</span> tier, rate <span style="color:#c4b5fd;">in</span> pricing.items():
    <span style="color:#93c5fd;">monthly</span> = rate * num_instances * hours_per_month
    <span style="color:#93c5fd;">annual</span>  = monthly * <span style="color:#fcd34d;">12</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{tier:<20} {rate:>10.3f} {monthly:>14,.2f} {annual:>13,.2f}"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nStrategy: use Reserved for baseline load, Spot for burst batch jobs"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Instance: c5.4xlarge × 10  |  730h/month
Pricing Tier          $/hr/inst    Monthly Cost   Annual Cost
------------------------------------------------------------
on_demand                 0.680       4,964.00      59,568.00
reserved_1yr              0.425       3,102.50      37,230.00
reserved_3yr              0.272       1,985.60      23,827.20
spot                      0.204       1,489.20      17,870.40

Strategy: use Reserved for baseline load, Spot for burst batch jobs</div>
  </div>
</div>

<h3>Infrastructure as Code: Reproducible Cloud Environments</h3>
<p><strong>Infrastructure as Code (IaC)</strong> treats cloud infrastructure — servers, networks, databases, security groups — as software: defined in code, version-controlled in Git, and applied automatically. IaC eliminates "snowflake servers" (unique, manually configured machines that cannot be reproduced), enables disaster recovery, and makes environments consistent across development, staging, and production. The dominant IaC tools are <strong>Terraform</strong> (cloud-agnostic, declarative HCL) and <strong>AWS CloudFormation</strong>. Modern teams also use <strong>Pulumi</strong> (write IaC in Python/TypeScript) for more programmatic control.</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.2 Cloud Computing Fundamentals: IaaS, PaaS, SaaS & the Major Providers',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L22_2', [
                ['q' => 'In the IaaS model, which components does the customer manage?', 'opts' => ['Nothing — the provider manages everything', 'Only the application code', 'The operating system, runtime, middleware, applications, and data', 'Only the physical hardware'], 'ans' => 2, 'exp' => 'IaaS provides virtualised compute, storage, and networking — the provider manages the physical hardware and hypervisor. You manage everything above: OS, runtime, middleware, applications, and data. This gives maximum flexibility but requires the most operational expertise.'],
                ['q' => 'What is the purpose of cloud Availability Zones (AZs)?', 'opts' => ['To reduce data transfer costs between regions', 'To provide physically isolated facilities within a region so that a failure in one AZ does not affect others, enabling high availability', 'To separate development from production workloads', 'To enforce data sovereignty regulations automatically'], 'ans' => 1, 'exp' => 'AZs are distinct physical facilities within a region with independent power, cooling, and networking. If one AZ experiences a failure (power outage, fire, networking issue), workloads deployed across multiple AZs continue running uninterrupted. Multi-AZ deployment is the standard approach for production-critical workloads.'],
                ['q' => 'AWS EC2 Spot Instances offer ~70% cost savings over On-Demand because:', 'opts' => ['They use older, less powerful hardware', 'They can be interrupted by AWS with 2 minutes warning when demand for spare capacity rises', 'They require a 3-year upfront commitment', 'They only run during off-peak hours automatically'], 'ans' => 1, 'exp' => 'Spot Instances use excess EC2 capacity. AWS can reclaim them with a 2-minute notification when that capacity is needed elsewhere. This interruptibility risk is compensated by up to 90% cost reduction. Spot Instances are ideal for fault-tolerant batch workloads like Spark jobs, ML training, and rendering — where work can be checkpointed and resumed.'],
                ['q' => 'Infrastructure as Code (IaC) solves which primary operational problem?', 'opts' => ['Slow query performance in data warehouses', 'Manually configured "snowflake servers" that cannot be reproduced, audited, or version-controlled', 'High network egress costs between cloud regions', 'Slow data ingestion from source systems'], 'ans' => 1, 'exp' => 'IaC defines infrastructure in code (Terraform HCL, CloudFormation YAML, Pulumi Python), enabling it to be version-controlled in Git, reviewed via pull requests, applied consistently across environments, and destroyed and recreated reliably. Manually configured servers are unique, fragile, and irreproducible — a disaster recovery nightmare.'],
                ['q' => 'Which cloud service model provides the highest developer productivity with the least operational overhead?', 'opts' => ['IaaS (Infrastructure as a Service)', 'On-premises hardware', 'SaaS (Software as a Service)', 'Bare-metal dedicated servers'], 'ans' => 2, 'exp' => "SaaS provides a fully managed application where you manage only configuration and data. Snowflake is SaaS: you don't manage servers, OS, query engines, or storage — you write SQL and pay for compute used. This dramatically reduces time-to-value and operational burden, though at the cost of flexibility and potentially higher per-unit cost at very large scale."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.3 — Distributed Storage: HDFS, Object Storage & Columnar Formats
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Distributed Storage: HDFS, Object Storage & Columnar Formats</h2>
<p>At the heart of every big data system is a storage layer designed to hold more data than any single machine can store, to survive hardware failures automatically, and to provide data to distributed compute engines at high throughput. Understanding distributed storage — how it works, why it is architected the way it is, and how the choice of file format dramatically impacts performance — is fundamental to building efficient big data pipelines.</p>

<h3>The Hadoop Distributed File System (HDFS)</h3>
<p>HDFS was the storage foundation of the original Hadoop ecosystem and the first practical distributed file system for big data workloads. Its design philosophy, embodied in the 2003 Google File System paper that inspired it, rests on one key insight: <em>hardware failures are the norm, not the exception</em>. When you operate thousands of commodity machines, several will fail every week. HDFS handles this by splitting every file into 128 MB blocks and storing three copies (replicas) of each block across different machines and racks. If a DataNode dies, the NameNode (master) detects the missing replicas and instructs other nodes to re-replicate them automatically.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">BASH — HDFS Core Commands</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># HDFS uses the same command structure as Unix, prefixed with hdfs dfs</span>

<span style="color:#6b7280;"># List the root of HDFS</span>
<span style="color:#93c5fd;">hdfs dfs -ls</span> /

<span style="color:#6b7280;"># Create a directory</span>
<span style="color:#93c5fd;">hdfs dfs -mkdir</span> -p /user/datasensei/sales

<span style="color:#6b7280;"># Upload a local file to HDFS</span>
<span style="color:#93c5fd;">hdfs dfs -put</span> ./sales_2024.csv /user/datasensei/sales/

<span style="color:#6b7280;"># Check replication factor and block locations</span>
<span style="color:#93c5fd;">hdfs fsck</span> /user/datasensei/sales/sales_2024.csv -files -blocks -locations

<span style="color:#6b7280;"># Check disk usage of a directory</span>
<span style="color:#93c5fd;">hdfs dfs -du</span> -s -h /user/datasensei/sales/

<span style="color:#6b7280;"># Change replication factor (default=3)</span>
<span style="color:#93c5fd;">hdfs dfs -setrep</span> -w 2 /user/datasensei/sales/sales_2024.csv</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Found 1 items
drwxr-xr-x  - datasensei supergroup  0 2024-03-15 /user

/user/datasensei/sales/sales_2024.csv: HEALTHY
 Total size: 2.1 GB
 Total dirs: 0
 Total files: 1
 Total blocks (validated): 17 (avg. block size 126.6 MB)
 Block locations: [DataNode01:rack1, DataNode04:rack2, DataNode07:rack3]

2.1 G  /user/datasensei/sales/
Replication 2 set: /user/datasensei/sales/sales_2024.csv</div>
  </div>
</div>

<h3>Object Storage: AWS S3, GCS & Azure Blob Storage</h3>
<p>Modern cloud-native big data architectures have largely replaced HDFS with <strong>object storage</strong> — services like AWS S3, Google Cloud Storage (GCS), and Azure Blob Storage. Object storage decouples compute from storage: your Spark cluster reads data directly from S3 buckets and is shut down when processing is done, rather than keeping expensive HDFS machines running continuously. Object storage is cheaper (pennies per GB-month), infinitely scalable without upfront provisioning, highly durable (11 nines — 99.999999999% durability), and accessed via HTTP APIs rather than a file system driver.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Interacting with AWS S3 using boto3</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> boto3
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">from</span> io <span style="color:#c4b5fd;">import</span> BytesIO

<span style="color:#6b7280;"># Create an S3 client (credentials from env vars or IAM role)</span>
<span style="color:#93c5fd;">s3</span> = boto3.client(<span style="color:#a7f3d0;">'s3'</span>, region_name=<span style="color:#a7f3d0;">'us-east-1'</span>)
<span style="color:#93c5fd;">BUCKET</span> = <span style="color:#a7f3d0;">'my-data-lake-prod'</span>

<span style="color:#6b7280;"># ── List objects with a prefix (like a directory listing) ─────────────</span>
<span style="color:#93c5fd;">response</span> = s3.list_objects_v2(
    Bucket=<span style="color:#93c5fd;">BUCKET</span>,
    Prefix=<span style="color:#a7f3d0;">'raw/sales/year=2024/month=03/'</span>
)
<span style="color:#c4b5fd;">for</span> obj <span style="color:#c4b5fd;">in</span> response.get(<span style="color:#a7f3d0;">'Contents'</span>, []):
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"  {obj['Key']:<60}  {obj['Size']/1e6:.1f} MB"</span>)

<span style="color:#6b7280;"># ── Read Parquet directly from S3 into a pandas DataFrame ─────────────</span>
<span style="color:#93c5fd;">obj</span>    = s3.get_object(Bucket=<span style="color:#93c5fd;">BUCKET</span>, Key=<span style="color:#a7f3d0;">'raw/sales/year=2024/month=03/sales_01.parquet'</span>)
<span style="color:#93c5fd;">df</span>     = pd.read_parquet(BytesIO(obj[<span style="color:#a7f3d0;">'Body'</span>].read()))
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nLoaded {len(df):,} rows × {len(df.columns)} columns from S3"</span>)
<span style="color:#93c5fd;">print</span>(df.dtypes)

<span style="color:#6b7280;"># ── Upload a processed DataFrame back to S3 as Parquet ────────────────</span>
<span style="color:#93c5fd;">buf</span>    = BytesIO()
<span style="color:#93c5fd;">df</span>.to_parquet(buf, engine=<span style="color:#a7f3d0;">'pyarrow'</span>, compression=<span style="color:#a7f3d0;">'snappy'</span>, index=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">buf</span>.seek(<span style="color:#fcd34d;">0</span>)
<span style="color:#93c5fd;">s3</span>.put_object(Bucket=<span style="color:#93c5fd;">BUCKET</span>, Key=<span style="color:#a7f3d0;">'processed/sales_march_2024.parquet'</span>, Body=buf)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>  raw/sales/year=2024/month=03/sales_01.parquet           127.3 MB
  raw/sales/year=2024/month=03/sales_02.parquet           134.8 MB
  raw/sales/year=2024/month=03/sales_03.parquet           118.5 MB

Loaded 4,182,341 rows × 12 columns from S3
order_id          int64
customer_id       int64
product_sku      object
quantity          int32
unit_price       float64
transaction_ts    datetime64[ns]</div>
  </div>
</div>

<h3>Columnar File Formats: Parquet & ORC</h3>
<p><strong>Parquet</strong> and <strong>ORC</strong> are columnar storage formats — the dominant choice for big data analytics workloads. Unlike row-based formats (CSV, JSON) that store all fields of a record together, columnar formats store each column's values contiguously. This matters enormously for analytical queries: a query computing the average of a single column across 1 billion rows needs to read only that column's data, not every field of every record. Combined with encoding and compression applied per column (integers compress better than mixed-type rows), columnar formats routinely deliver 10–100× lower storage costs and query times compared to CSV.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — CSV vs Parquet: File Size & Read Speed Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>
<span style="color:#c4b5fd;">import</span> time, os

<span style="color:#6b7280;"># Generate 5 million rows of synthetic sales data</span>
<span style="color:#93c5fd;">np.random.seed</span>(<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">N</span> = <span style="color:#fcd34d;">5_000_000</span>
<span style="color:#93c5fd;">df</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'order_id'</span>:    np.arange(<span style="color:#fcd34d;">1</span>, N+<span style="color:#fcd34d;">1</span>, dtype=np.int32),
    <span style="color:#a7f3d0;">'customer_id'</span>: np.random.randint(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">100_000</span>, N, dtype=np.int32),
    <span style="color:#a7f3d0;">'quantity'</span>:    np.random.randint(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">50</span>, N, dtype=np.int8),
    <span style="color:#a7f3d0;">'unit_price'</span>:  np.random.uniform(<span style="color:#fcd34d;">5.0</span>, <span style="color:#fcd34d;">500.0</span>, N).round(<span style="color:#fcd34d;">2</span>),
    <span style="color:#a7f3d0;">'region'</span>:      np.random.choice([<span style="color:#a7f3d0;">'APAC'</span>,<span style="color:#a7f3d0;">'EMEA'</span>,<span style="color:#a7f3d0;">'AMER'</span>], N),
})

<span style="color:#6b7280;"># Write to both formats</span>
<span style="color:#93c5fd;">df</span>.to_csv(<span style="color:#a7f3d0;">'sales.csv'</span>, index=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">df</span>.to_parquet(<span style="color:#a7f3d0;">'sales.parquet'</span>, engine=<span style="color:#a7f3d0;">'pyarrow'</span>, compression=<span style="color:#a7f3d0;">'snappy'</span>)

<span style="color:#93c5fd;">csv_mb</span>     = os.path.getsize(<span style="color:#a7f3d0;">'sales.csv'</span>)     / <span style="color:#fcd34d;">1e6</span>
<span style="color:#93c5fd;">parquet_mb</span> = os.path.getsize(<span style="color:#a7f3d0;">'sales.parquet'</span>) / <span style="color:#fcd34d;">1e6</span>

<span style="color:#6b7280;"># Benchmark: compute average unit_price by region</span>
<span style="color:#93c5fd;">t0</span> = time.perf_counter()
<span style="color:#93c5fd;">pd</span>.read_csv(<span style="color:#a7f3d0;">'sales.csv'</span>, usecols=[<span style="color:#a7f3d0;">'unit_price'</span>,<span style="color:#a7f3d0;">'region'</span>]).groupby(<span style="color:#a7f3d0;">'region'</span>)[<span style="color:#a7f3d0;">'unit_price'</span>].mean()
<span style="color:#93c5fd;">csv_sec</span> = time.perf_counter() - t0

<span style="color:#93c5fd;">t0</span> = time.perf_counter()
<span style="color:#93c5fd;">pd</span>.read_parquet(<span style="color:#a7f3d0;">'sales.parquet'</span>, columns=[<span style="color:#a7f3d0;">'unit_price'</span>,<span style="color:#a7f3d0;">'region'</span>]).groupby(<span style="color:#a7f3d0;">'region'</span>)[<span style="color:#a7f3d0;">'unit_price'</span>].mean()
<span style="color:#93c5fd;">parquet_sec</span> = time.perf_counter() - t0

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Format':<12} {'Size (MB)':>10} {'Query Time':>12} {'Compression Ratio':>20}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 58</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'CSV':<12} {csv_mb:>10.1f} {csv_sec:>10.2f}s {'1.0x':>20}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Parquet':<12} {parquet_mb:>10.1f} {parquet_sec:>10.2f}s {csv_mb/parquet_mb:>19.1f}x"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Format         Size (MB)   Query Time   Compression Ratio
----------------------------------------------------------
CSV              482.3      8.41s                  1.0x
Parquet           47.8      0.31s                 10.1x</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.3 Distributed Storage: HDFS, Object Storage & Columnar Formats',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L22_3', [
                ['q' => 'How does HDFS handle hardware failures automatically?', 'opts' => ['It uses RAID arrays on each DataNode', 'It stores 3 replicas of each 128 MB block across different machines and racks; the NameNode detects missing replicas and triggers re-replication', 'It checkpoints data to a backup HDFS cluster every hour', 'It uses erasure coding exclusively to reconstruct lost data'], 'ans' => 1, 'exp' => "HDFS splits files into 128 MB blocks (configurable) and stores 3 replicas by default, placed across different DataNodes on different racks. The NameNode continuously monitors DataNode heartbeats. When a node fails, the NameNode identifies under-replicated blocks and instructs surviving DataNodes to create new replicas, restoring the 3-replica guarantee automatically."],
                ['q' => 'The primary advantage of cloud object storage (S3/GCS) over HDFS for big data workloads is:', 'opts' => ['Object storage has lower latency for random byte-range reads', 'Object storage decouples compute from storage — clusters can be shut down when not processing, eliminating the cost of always-on HDFS machines', 'Object storage supports POSIX file system operations natively', 'Object storage is faster for write-heavy streaming workloads'], 'ans' => 1, 'exp' => 'HDFS couples storage and compute — you must keep HDFS DataNodes running to store data. Object storage is independent: data sits in S3/GCS permanently at low cost, and you spin up a Spark cluster only when you need to process it, then shut it down. This "transient cluster" pattern dramatically reduces costs for intermittent batch workloads.'],
                ['q' => 'Columnar storage formats like Parquet outperform row-based formats (CSV) for analytical queries because:', 'opts' => ['They compress data with stronger encryption', 'Analytical queries typically read only a few columns — columnar storage allows reading only the required columns without scanning every field of every record', 'They eliminate the need for indexing', 'They convert data to integers before storing'], 'ans' => 1, 'exp' => "SQL analytics: SELECT AVG(price) FROM sales — only needs the 'price' column. Row-based (CSV): must read every field of every row to find 'price'. Columnar (Parquet): reads only the 'price' column's contiguous bytes. On 100-column tables with billion-row queries, this is a 100× I/O reduction. Plus, homogeneous data per column compresses far better than mixed-type rows."],
                ['q' => 'What does the Snappy compression option in Parquet provide?', 'opts' => ['Maximum compression ratio at the cost of very slow read speed', 'A balance of fast compression/decompression speed with moderate compression ratio — optimised for read-heavy query performance', 'Lossless encryption of column data', 'Compatibility with all versions of Python pandas'], 'ans' => 1, 'exp' => 'Snappy prioritises speed over ratio — it decompresses very quickly (important for query latency) while still achieving 2-5× compression. Alternatives: GZIP achieves higher ratios but is much slower to decompress (bad for interactive queries). Zstandard (ZSTD) is newer and often the best choice — better ratio than Snappy with comparable speed.'],
                ['q' => 'In HDFS, what is the role of the NameNode?', 'opts' => ['It stores the actual data blocks for the most popular files', 'It is the master node that stores filesystem metadata — the namespace, directory tree, file-to-block mappings, and block locations — but not actual data', 'It handles client authentication and authorisation', 'It runs the YARN resource manager for job scheduling'], 'ans' => 1, 'exp' => "The NameNode is the single master that holds ALL filesystem metadata in RAM for fast access: what files exist, how they split into blocks, and which DataNodes hold each block. DataNodes store the actual block data. NameNode failure was historically HDFS's single point of failure — modern deployments use NameNode HA (High Availability) with a standby NameNode."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.4 — Apache Spark: Distributed Computing at Scale
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Apache Spark: Distributed Computing at Scale</h2>
<p><strong>Apache Spark</strong> is the dominant general-purpose distributed data processing engine in the big data ecosystem. Originally developed at UC Berkeley's AMPLab in 2009, Spark was designed to overcome Hadoop MapReduce's fundamental limitation: MapReduce writes intermediate results to disk between every processing step, making iterative algorithms (like machine learning) prohibitively slow. Spark keeps intermediate data in memory (RAM) across a cluster, enabling iterative workloads to run 10–100× faster than MapReduce. Today, Spark is the foundation of data processing at every major technology company — Databricks (the company behind Spark) is valued at over $40 billion.</p>

<p>Spark provides a unified engine for batch processing, SQL analytics, streaming, machine learning (MLlib), and graph computation (GraphX) — all through a single API available in Python (PySpark), Scala, Java, R, and SQL. This "write once, run at any scale" promise is what makes Spark so valuable: the same code that you test locally on your laptop can be submitted to a 1,000-node cluster to process petabytes.</p>

<h3>Spark's Core Abstraction: RDDs, DataFrames & Datasets</h3>
<p>Spark's fundamental data abstraction is the <strong>Resilient Distributed Dataset (RDD)</strong> — an immutable, partitioned collection of records distributed across a cluster, with lineage information to reconstruct any partition from its sources if a node fails. Modern Spark development uses the higher-level <strong>DataFrame API</strong> (introduced in Spark 1.3), which adds schema information, enables the Catalyst query optimiser, and supports the same SQL operations data engineers already know. The DataFrame API is almost always preferred over raw RDDs.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PySpark: DataFrame Operations & Spark SQL</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> pyspark.sql <span style="color:#c4b5fd;">import</span> SparkSession
<span style="color:#c4b5fd;">from</span> pyspark.sql <span style="color:#c4b5fd;">import</span> functions <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">F</span>
<span style="color:#c4b5fd;">from</span> pyspark.sql.types <span style="color:#c4b5fd;">import</span> StructType, StructField, IntegerType, DoubleType, StringType

<span style="color:#6b7280;"># Create a SparkSession — the entry point to all Spark functionality</span>
<span style="color:#93c5fd;">spark</span> = SparkSession.builder \
    .appName(<span style="color:#a7f3d0;">"SalesAnalysis"</span>) \
    .config(<span style="color:#a7f3d0;">"spark.executor.memory"</span>, <span style="color:#a7f3d0;">"4g"</span>) \
    .config(<span style="color:#a7f3d0;">"spark.executor.cores"</span>, <span style="color:#a7f3d0;">"4"</span>) \
    .getOrCreate()

<span style="color:#6b7280;"># Define schema explicitly (faster than inferSchema=True on large files)</span>
<span style="color:#93c5fd;">schema</span> = StructType([
    StructField(<span style="color:#a7f3d0;">"order_id"</span>,    IntegerType(), nullable=<span style="color:#fca5a5;">False</span>),
    StructField(<span style="color:#a7f3d0;">"customer_id"</span>, IntegerType(), nullable=<span style="color:#fca5a5;">False</span>),
    StructField(<span style="color:#a7f3d0;">"product_sku"</span>, StringType(),  nullable=<span style="color:#fca5a5;">True</span>),
    StructField(<span style="color:#a7f3d0;">"quantity"</span>,    IntegerType(), nullable=<span style="color:#fca5a5;">True</span>),
    StructField(<span style="color:#a7f3d0;">"unit_price"</span>,  DoubleType(),  nullable=<span style="color:#fca5a5;">True</span>),
    StructField(<span style="color:#a7f3d0;">"region"</span>,      StringType(),  nullable=<span style="color:#fca5a5;">True</span>),
])

<span style="color:#6b7280;"># Read Parquet from S3 — Spark reads in parallel across all partitions</span>
<span style="color:#93c5fd;">df</span> = spark.read.schema(schema).parquet(<span style="color:#a7f3d0;">"s3://my-data-lake/raw/sales/year=2024/"</span>)

<span style="color:#6b7280;"># DataFrame transformations — lazily evaluated until an action is called</span>
<span style="color:#93c5fd;">result</span> = (
    df
    .withColumn(<span style="color:#a7f3d0;">"revenue"</span>, F.col(<span style="color:#a7f3d0;">"quantity"</span>) * F.col(<span style="color:#a7f3d0;">"unit_price"</span>))
    .filter(F.col(<span style="color:#a7f3d0;">"revenue"</span>) &gt; <span style="color:#fcd34d;">100</span>)
    .groupBy(<span style="color:#a7f3d0;">"region"</span>)
    .agg(
        F.count(<span style="color:#a7f3d0;">"order_id"</span>).alias(<span style="color:#a7f3d0;">"order_count"</span>),
        F.round(F.sum(<span style="color:#a7f3d0;">"revenue"</span>), <span style="color:#fcd34d;">2</span>).alias(<span style="color:#a7f3d0;">"total_revenue"</span>),
        F.round(F.avg(<span style="color:#a7f3d0;">"revenue"</span>), <span style="color:#fcd34d;">2</span>).alias(<span style="color:#a7f3d0;">"avg_order_value"</span>),
    )
    .orderBy(F.desc(<span style="color:#a7f3d0;">"total_revenue"</span>))
)

result.show()   <span style="color:#6b7280;"># ACTION — triggers execution of the entire DAG</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Spark processed {df.count():,} rows across {df.rdd.getNumPartitions()} partitions"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>+------+-----------+-------------+---------------+
|region|order_count|total_revenue|avg_order_value|
+------+-----------+-------------+---------------+
|  AMER|  1,847,203|  421,834,211|         228.37|
|  EMEA|  1,324,891|  298,103,447|         225.02|
|  APAC|  1,009,547|  231,872,094|         229.68|
+------+-----------+-------------+---------------+

Spark processed 4,181,641 rows across 200 partitions</div>
  </div>
</div>

<h3>Lazy Evaluation & the DAG Execution Model</h3>
<p>Spark uses <strong>lazy evaluation</strong>: transformation operations (filter, select, groupBy, join) build up a logical execution plan — a <strong>Directed Acyclic Graph (DAG)</strong> — without executing anything. Only when an <strong>action</strong> is called (show, count, write, collect) does Spark actually execute the DAG. The Catalyst optimiser analyses the DAG and reorders, combines, and replaces operations to produce the most efficient physical execution plan — sometimes dramatically different from what you wrote. This is why DataFrame API is faster than raw RDDs: the optimiser cannot see inside arbitrary Python functions in RDDs, but it fully understands SQL-style operations.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Spark SQL, Partitioning & Writing Optimised Output</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Register DataFrame as a temporary SQL view</span>
<span style="color:#93c5fd;">df</span>.createOrReplaceTempView(<span style="color:#a7f3d0;">"sales"</span>)

<span style="color:#6b7280;"># Run pure SQL against it — identical performance to DataFrame API</span>
<span style="color:#93c5fd;">top_customers</span> = spark.sql(<span style="color:#a7f3d0;">"""
    SELECT
        customer_id,
        COUNT(*)          AS total_orders,
        ROUND(SUM(quantity * unit_price), 2) AS lifetime_value,
        MIN(region)       AS primary_region
    FROM  sales
    WHERE unit_price > 0
    GROUP BY customer_id
    HAVING COUNT(*) >= 5
    ORDER BY lifetime_value DESC
    LIMIT 20
"""</span>)
top_customers.show(<span style="color:#fcd34d;">5</span>)

<span style="color:#6b7280;"># Write results as partitioned Parquet — Hive-style partitioning for pruning</span>
<span style="color:#93c5fd;">result</span>.repartition(<span style="color:#fcd34d;">1</span>) \
      .write \
      .mode(<span style="color:#a7f3d0;">"overwrite"</span>) \
      .partitionBy(<span style="color:#a7f3d0;">"region"</span>) \
      .parquet(<span style="color:#a7f3d0;">"s3://my-data-lake/processed/sales_summary/"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Wrote partitioned Parquet to S3:"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  processed/sales_summary/region=AMER/part-00000.parquet"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  processed/sales_summary/region=EMEA/part-00000.parquet"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"  processed/sales_summary/region=APAC/part-00000.parquet"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Future queries filtered by region= will scan only 1 partition folder"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>+-----------+------------+--------------+--------------+
|customer_id|total_orders|lifetime_value|primary_region|
+-----------+------------+--------------+--------------+
|      94821|          87|     204,319.44|          AMER|
|      17483|          71|     188,032.11|          EMEA|
|      52917|          65|     176,448.90|          APAC|
|      83641|          61|     162,917.55|          AMER|
|      29054|          58|     155,841.22|          EMEA|
+-----------+------------+--------------+--------------+

Wrote partitioned Parquet to S3:
  processed/sales_summary/region=AMER/part-00000.parquet
  processed/sales_summary/region=EMEA/part-00000.parquet
  processed/sales_summary/region=APAC/part-00000.parquet
Future queries filtered by region= will scan only 1 partition folder</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.4 Apache Spark: Distributed Computing at Scale',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L22_4', [
                ['q' => 'Why is Apache Spark significantly faster than Hadoop MapReduce for iterative algorithms?', 'opts' => ['Spark uses a faster programming language', 'Spark keeps intermediate data in memory (RAM) across operations whereas MapReduce writes intermediate results to disk between every step', 'Spark uses GPU acceleration by default', 'Spark automatically partitions data more efficiently than MapReduce'], 'ans' => 1, 'exp' => 'MapReduce: every Map→Reduce step writes output to HDFS, then the next step reads it back — massive disk I/O overhead. Spark: intermediate results stay in executor RAM across multiple transformations. For ML algorithms that iterate many times over the same dataset, this makes Spark 10–100× faster than MapReduce.'],
                ['q' => 'In Spark\'s lazy evaluation model, what triggers actual execution of the computation DAG?', 'opts' => ['Any transformation like .filter() or .groupBy()', 'Only actions like .show(), .count(), .collect(), or .write()', 'The .cache() call on a DataFrame', 'Creating the SparkSession'], 'ans' => 1, 'exp' => 'Transformations (filter, select, groupBy, join, withColumn) are lazy — they build a logical plan but execute nothing. Actions (show, count, collect, write, take) trigger the Catalyst optimiser to produce a physical plan and execute it across the cluster. This allows the optimiser to see the full computation graph and apply optimisations before any data is moved.'],
                ['q' => 'What is the Catalyst optimiser in Spark?', 'opts' => ['A chemical processing library for scientific data', 'The query optimisation engine that analyses the DataFrame operation DAG and rewrites it into the most efficient physical execution plan', 'The memory management component that handles garbage collection', 'The scheduler that assigns tasks to executor nodes'], 'ans' => 1, 'exp' => 'Catalyst is Spark SQL\'s query optimiser. It takes the logical plan (what you wrote), applies rule-based and cost-based optimisations (predicate pushdown, projection pruning, join reordering, broadcast joins for small tables), and produces an optimised physical plan. This is why DataFrame API outperforms raw RDDs — Catalyst cannot optimise opaque Python lambda functions.'],
                ['q' => 'Hive-style partitioning (partitionBy("region")) when writing Parquet to S3 benefits future queries because:', 'opts' => ['It sorts rows within each file for faster scans', 'Queries filtered by "region" only need to scan the matching partition folder, skipping all other files entirely — called partition pruning', 'It automatically compresses each partition with a different codec', 'It enables Spark to use GPU acceleration for that column'], 'ans' => 1, 'exp' => 'With partitionBy("region"), data is stored in directories like region=AMER/, region=EMEA/. When a query has WHERE region = \'AMER\', Spark\'s planner detects this and reads ONLY the region=AMER/ directory. If AMER is 1/3 of the data, you just reduced I/O by 67% — free performance from storage layout.'],
                ['q' => 'When should you explicitly define a schema in Spark rather than using inferSchema=True?', 'opts' => ['Never — inferSchema is always more accurate', 'Always for production pipelines: it is much faster (no full file scan needed), guarantees correct types, catches schema drift early, and prevents costly type mismatches downstream', 'Only when reading from databases, not files', 'Only when the file has more than 100 columns'], 'ans' => 1, 'exp' => 'inferSchema=True forces Spark to read the entire file once just to guess types — expensive on large files. Explicit schemas: (1) skip the inference scan entirely, (2) enforce expected types (catching corrupt data early), (3) document what the data looks like, and (4) prevent silent type promotion bugs where a column is read as String when it should be Integer.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.5 — Apache Kafka: Real-Time Data Streaming & Event Architecture
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Apache Kafka: Real-Time Data Streaming & Event Architecture</h2>
<p><strong>Apache Kafka</strong> is a distributed event streaming platform used by over 80% of Fortune 100 companies. Originally built at LinkedIn in 2011 to handle 1 trillion messages per day, Kafka has become the central nervous system of modern data architectures — a high-throughput, fault-tolerant, persistent message bus that connects every data-producing system (databases, microservices, IoT sensors, clickstreams) to every data-consuming system (analytics engines, ML models, data warehouses, operational dashboards) through a unified streaming backbone.</p>

<p>The paradigm shift Kafka enables is from <em>point-to-point integrations</em> to an <em>event-driven architecture</em>. Without Kafka, a system with N producers and M consumers requires N×M direct integrations — unmaintainable at scale. With Kafka, every producer publishes events to Kafka and every consumer subscribes independently — just N + M integrations, with Kafka decoupling them completely.</p>

<h3>Core Kafka Concepts: Topics, Partitions, Producers & Consumers</h3>
<p>A <strong>topic</strong> is a named stream of events — like a database table, but append-only and time-ordered. Topics are divided into <strong>partitions</strong> — ordered, immutable sequences of records stored on different Kafka brokers, enabling horizontal scaling and parallelism. <strong>Producers</strong> write events to topics; <strong>consumers</strong> read from topics. Kafka retains events for a configurable period (default 7 days) — consumers can re-read from any point in history, enabling replayability: if a downstream system crashes, it can simply restart from where it left off.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Kafka Producer & Consumer with kafka-python</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> kafka <span style="color:#c4b5fd;">import</span> KafkaProducer, KafkaConsumer
<span style="color:#c4b5fd;">import</span> json, time, random, datetime

<span style="color:#6b7280;"># ── PRODUCER: publish transaction events ──────────────────────────────</span>
<span style="color:#93c5fd;">producer</span> = KafkaProducer(
    bootstrap_servers=[<span style="color:#a7f3d0;">'kafka-broker-1:9092'</span>, <span style="color:#a7f3d0;">'kafka-broker-2:9092'</span>],
    value_serializer=<span style="color:#c4b5fd;">lambda</span> v: json.dumps(v).encode(<span style="color:#a7f3d0;">'utf-8'</span>),
    key_serializer=<span style="color:#c4b5fd;">lambda</span> k: k.encode(<span style="color:#a7f3d0;">'utf-8'</span>),
    acks=<span style="color:#a7f3d0;">'all'</span>,           <span style="color:#6b7280;"># wait for all replicas — strongest durability guarantee</span>
    retries=<span style="color:#fcd34d;">3</span>,
)

<span style="color:#c4b5fd;">for</span> i <span style="color:#c4b5fd;">in</span> <span style="color:#93c5fd;">range</span>(<span style="color:#fcd34d;">5</span>):
    <span style="color:#93c5fd;">event</span> = {
        <span style="color:#a7f3d0;">"event_id"</span>:   f<span style="color:#a7f3d0;">"txn_{i+1:05d}"</span>,
        <span style="color:#a7f3d0;">"customer_id"</span>: random.randint(<span style="color:#fcd34d;">1000</span>, <span style="color:#fcd34d;">9999</span>),
        <span style="color:#a7f3d0;">"amount"</span>:      <span style="color:#93c5fd;">round</span>(random.uniform(<span style="color:#fcd34d;">10</span>, <span style="color:#fcd34d;">2000</span>), <span style="color:#fcd34d;">2</span>),
        <span style="color:#a7f3d0;">"currency"</span>:    random.choice([<span style="color:#a7f3d0;">"USD"</span>, <span style="color:#a7f3d0;">"EUR"</span>, <span style="color:#a7f3d0;">"GBP"</span>]),
        <span style="color:#a7f3d0;">"timestamp"</span>:   datetime.datetime.utcnow().isoformat(),
    }
    <span style="color:#6b7280;"># Partition by customer_id so all events per customer go to same partition</span>
    <span style="color:#93c5fd;">future</span> = producer.send(<span style="color:#a7f3d0;">"transactions"</span>, key=<span style="color:#93c5fd;">str</span>(event[<span style="color:#a7f3d0;">"customer_id"</span>]), value=event)
    <span style="color:#93c5fd;">meta</span>   = future.get(timeout=<span style="color:#fcd34d;">10</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Sent {event['event_id']} → topic={meta.topic} partition={meta.partition} offset={meta.offset}"</span>)

<span style="color:#93c5fd;">producer</span>.flush()

<span style="color:#6b7280;"># ── CONSUMER: read and process events ────────────────────────────────</span>
<span style="color:#93c5fd;">consumer</span> = KafkaConsumer(
    <span style="color:#a7f3d0;">"transactions"</span>,
    bootstrap_servers=[<span style="color:#a7f3d0;">'kafka-broker-1:9092'</span>],
    group_id=<span style="color:#a7f3d0;">"fraud-detection-service"</span>,   <span style="color:#6b7280;"># consumer group for parallel processing</span>
    value_deserializer=<span style="color:#c4b5fd;">lambda</span> m: json.loads(m.decode(<span style="color:#a7f3d0;">'utf-8'</span>)),
    auto_offset_reset=<span style="color:#a7f3d0;">'latest'</span>,
)

<span style="color:#c4b5fd;">for</span> msg <span style="color:#c4b5fd;">in</span> consumer:
    <span style="color:#93c5fd;">event</span> = msg.value
    <span style="color:#93c5fd;">flag</span>  = <span style="color:#a7f3d0;">"🚨 FRAUD SUSPECT"</span> <span style="color:#c4b5fd;">if</span> event[<span style="color:#a7f3d0;">"amount"</span>] &gt; <span style="color:#fcd34d;">1500</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"✓ OK"</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"[offset={msg.offset}] {event['event_id']}  ${event['amount']:<8.2f}  {flag}"</span>)
    <span style="color:#c4b5fd;">break</span>  <span style="color:#6b7280;"># in production, this loops indefinitely</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Sent txn_00001 → topic=transactions partition=2 offset=104812
Sent txn_00002 → topic=transactions partition=7 offset=97341
Sent txn_00003 → topic=transactions partition=2 offset=104813
Sent txn_00004 → topic=transactions partition=1 offset=88923
Sent txn_00005 → topic=transactions partition=5 offset=112044

[offset=104812] txn_00001  $1847.32   🚨 FRAUD SUSPECT</div>
  </div>
</div>

<h3>Consumer Groups & Partition Assignment: The Scaling Mechanism</h3>
<p>Kafka's scalability secret is the <strong>consumer group</strong>. Multiple consumer instances sharing the same <code>group_id</code> form a consumer group. Kafka automatically distributes topic partitions across group members — each partition is consumed by exactly one member at a time. If a topic has 12 partitions and you run 4 consumer instances in the same group, each instance gets 3 partitions, processing in parallel. Add 8 more instances → each gets 1 partition. Remove instances → Kafka rebalances and redistributes partitions to the remaining members. This is the mechanism that lets a single Kafka consumer group scale to process millions of events per second.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">BASH — Kafka Admin: Creating Topics & Inspecting Consumer Lag</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># Create a topic with 12 partitions and replication factor 3</span>
<span style="color:#93c5fd;">kafka-topics.sh</span> --create \
  --bootstrap-server kafka-broker-1:9092 \
  --topic transactions \
  --partitions 12 \
  --replication-factor 3 \
  --config retention.ms=604800000   <span style="color:#6b7280;"># 7 day retention</span>

<span style="color:#6b7280;"># Describe the topic (shows partition leaders and ISR)</span>
<span style="color:#93c5fd;">kafka-topics.sh</span> --describe \
  --bootstrap-server kafka-broker-1:9092 \
  --topic transactions

<span style="color:#6b7280;"># Check consumer group lag — how far behind is the consumer?</span>
<span style="color:#93c5fd;">kafka-consumer-groups.sh</span> --bootstrap-server kafka-broker-1:9092 \
  --describe \
  --group fraud-detection-service</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Topic: transactions  Partitions: 12  Replication-Factor: 3  Configs: retention.ms=604800000
  Partition: 0   Leader: broker-2   Replicas: 2,0,1   Isr: 2,0,1
  Partition: 1   Leader: broker-0   Replicas: 0,1,2   Isr: 0,1,2
  ...

GROUP                    TOPIC         PARTITION  CURRENT-OFFSET  LOG-END-OFFSET  LAG
fraud-detection-service  transactions  0          104812          104851          39
fraud-detection-service  transactions  1          88923           88950           27
fraud-detection-service  transactions  2          104813          104889          76
# LAG = how many unprocessed messages remain in each partition</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.5 Apache Kafka: Real-Time Data Streaming & Event Architecture',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L22_5', [
                ['q' => 'What is a Kafka "consumer group" and why is it important?', 'opts' => ['A group of Kafka brokers that replicate data together', 'A set of consumer instances sharing the same group_id that collectively process a topic — Kafka distributes partitions among members enabling parallel consumption and horizontal scaling', 'A security group defining which consumers can access a topic', 'A group of topics that are consumed together by a single consumer'], 'ans' => 1, 'exp' => 'Consumer groups enable horizontal scaling: N partitions across M group members means each member processes N/M partitions in parallel. Add more consumers → more parallelism (up to the number of partitions). Kafka tracks each group\'s offset independently, so multiple groups can read the same topic at different rates for different purposes (e.g., fraud detection AND analytics both reading the same transactions topic).'],
                ['q' => 'Kafka\'s "consumer lag" metric measures:', 'opts' => ['Network latency between producer and broker', 'CPU usage of the consumer application', 'The number of unprocessed messages in a partition (log-end-offset minus current-offset)', 'The time taken to serialize a message'], 'ans' => 2, 'exp' => 'Consumer lag = LOG-END-OFFSET − CURRENT-OFFSET for each partition. It tells you how far behind a consumer group is relative to the latest produced messages. High lag means the consumer is not keeping up with the producer — you need to either optimise consumer processing or add more consumer instances. Lag monitoring is critical for SLA compliance in real-time systems.'],
                ['q' => 'Setting acks="all" when producing to Kafka means:', 'opts' => ['The producer does not wait for any acknowledgement, maximising throughput', 'The leader broker acknowledges only when all in-sync replicas (ISR) have written the message — strongest durability guarantee but higher latency', 'Only one broker acknowledges the write', 'The message is written to all brokers in the cluster simultaneously'], 'ans' => 1, 'exp' => 'acks=0: fire and forget — no wait, highest throughput, potential data loss. acks=1: leader acknowledges — one replica guaranteed, potential loss if leader fails before replication. acks=all (or -1): all in-sync replicas acknowledge — no data loss even if the leader fails immediately after. Choose based on your durability vs. latency trade-off.'],
                ['q' => 'Why does Kafka partition messages by a key (e.g., customer_id)?', 'opts' => ['To compress messages of the same key together', 'To guarantee that all messages with the same key go to the same partition, preserving order for that key — essential for use cases like tracking all events per customer chronologically', 'To enable encryption on a per-customer basis', 'To load balance across brokers more effectively than round-robin'], 'ans' => 1, 'exp' => 'Kafka guarantees message order WITHIN a partition, not across partitions. By routing all events for a given customer_id to the same partition, you guarantee that consumer processes all transactions for that customer in the exact order they occurred — critical for balance calculations, fraud detection, and state machine processing.'],
                ['q' => 'What does Kafka\'s message retention policy enable that traditional message queues do not?', 'opts' => ['Higher throughput by deleting messages faster', 'Multiple independent consumer groups to read the same events, and the ability to replay historical events from any point in time — enabling reprocessing, backfill, and new consumer onboarding', 'Automatic schema evolution for message payloads', 'Zero-copy network I/O for reduced latency'], 'ans' => 1, 'exp' => 'Traditional queues delete messages once consumed. Kafka retains messages for a configurable period (7 days by default). This enables: (1) multiple consumer groups reading the same topic independently, (2) replaying events after a consumer bug is fixed, (3) onboarding new consumers to process historical data, and (4) disaster recovery by replaying from a known-good offset.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.6 — Cloud Data Warehouses: Snowflake, BigQuery & Redshift
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Cloud Data Warehouses: Snowflake, BigQuery & Redshift</h2>
<p>A <strong>cloud data warehouse</strong> is a managed, scalable analytical database that separates compute from storage, charges per query or per second of compute used, and is designed exclusively for <strong>OLAP</strong> (Online Analytical Processing) workloads — large-scale aggregations, complex multi-table joins, and full-column scans across billions of rows. Unlike OLTP databases (PostgreSQL, MySQL) designed for row-level inserts and updates, data warehouses are engineered to answer business intelligence questions like "What was our revenue by product category in Q3, segmented by geography, compared to the same period last year?"</p>

<p>The three dominant cloud data warehouses — <strong>Snowflake</strong>, <strong>Google BigQuery</strong>, and <strong>Amazon Redshift</strong> — have each taken different architectural approaches, but all three share the same design principle: store data in columnar format across massively distributed clusters, enabling any SQL query to be parallelised across hundreds or thousands of compute nodes simultaneously.</p>

<h3>Snowflake: The Multi-Cluster, Multi-Cloud Warehouse</h3>
<p>Snowflake's defining architectural innovation is the complete separation of storage, compute, and services into three independent layers. Storage is held in S3/GCS/Azure Blob as compressed columnar micro-partitions. Compute is provided by <strong>Virtual Warehouses</strong> — independent clusters of compute nodes that can be started and stopped in seconds, scaled up or down instantly, and run multiple workloads concurrently without contention. The services layer handles query optimisation, metadata, security, and result caching. Because compute and storage are fully decoupled, your BI team and your data engineering team can run simultaneous heavy queries without affecting each other.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Snowflake: Loading Data, Time Travel & Query Optimisation</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Create a virtual warehouse (compute cluster)</span>
<span style="color:#c4b5fd;">CREATE OR REPLACE</span> WAREHOUSE analytics_wh
    WAREHOUSE_SIZE = <span style="color:#a7f3d0;">'MEDIUM'</span>         <span style="color:#6b7280;">-- 4 nodes</span>
    AUTO_SUSPEND   = <span style="color:#fcd34d;">60</span>               <span style="color:#6b7280;">-- suspend after 60s idle (save cost)</span>
    AUTO_RESUME    = <span style="color:#fca5a5;">TRUE</span>             <span style="color:#6b7280;">-- auto-wake on next query</span>
    MAX_CLUSTER_COUNT = <span style="color:#fcd34d;">3</span>;            <span style="color:#6b7280;">-- scale to 3 clusters under load</span>

<span style="color:#6b7280;">-- Load data from S3 using an external stage</span>
<span style="color:#c4b5fd;">CREATE OR REPLACE</span> STAGE s3_sales_stage
    URL = <span style="color:#a7f3d0;">'s3://my-data-lake/raw/sales/'</span>
    CREDENTIALS = (AWS_ROLE = <span style="color:#a7f3d0;">'arn:aws:iam::123456789:role/snowflake-role'</span>)
    FILE_FORMAT = (TYPE = <span style="color:#a7f3d0;">'PARQUET'</span>);

<span style="color:#c4b5fd;">COPY INTO</span> sales_raw
    <span style="color:#c4b5fd;">FROM</span> @s3_sales_stage
    PATTERN = <span style="color:#a7f3d0;">'.*year=2024.*\.parquet'</span>
    ON_ERROR = <span style="color:#a7f3d0;">'CONTINUE'</span>;

<span style="color:#6b7280;">-- Analytical query with window functions</span>
<span style="color:#c4b5fd;">SELECT</span>
    region,
    DATE_TRUNC(<span style="color:#a7f3d0;">'month'</span>, transaction_ts)                           <span style="color:#c4b5fd;">AS</span> month,
    SUM(quantity * unit_price)                                   <span style="color:#c4b5fd;">AS</span> revenue,
    LAG(SUM(quantity * unit_price), <span style="color:#fcd34d;">1</span>) OVER (
        PARTITION BY region ORDER BY DATE_TRUNC(<span style="color:#a7f3d0;">'month'</span>, transaction_ts)
    )                                                            <span style="color:#c4b5fd;">AS</span> prev_month_revenue,
    ROUND(<span style="color:#fcd34d;">100.0</span> * (SUM(quantity * unit_price) -
        LAG(SUM(quantity * unit_price), <span style="color:#fcd34d;">1</span>) OVER (
            PARTITION BY region ORDER BY DATE_TRUNC(<span style="color:#a7f3d0;">'month'</span>, transaction_ts))) /
        NULLIF(LAG(SUM(quantity * unit_price), <span style="color:#fcd34d;">1</span>) OVER (
            PARTITION BY region ORDER BY DATE_TRUNC(<span style="color:#a7f3d0;">'month'</span>, transaction_ts)), <span style="color:#fcd34d;">0</span>), <span style="color:#fcd34d;">2</span>
    )                                                            <span style="color:#c4b5fd;">AS</span> mom_growth_pct
<span style="color:#c4b5fd;">FROM</span>  sales_raw
<span style="color:#c4b5fd;">WHERE</span> transaction_ts &gt;= <span style="color:#a7f3d0;">'2024-01-01'</span>
<span style="color:#c4b5fd;">GROUP BY</span> <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>
<span style="color:#c4b5fd;">ORDER BY</span> region, month;

<span style="color:#6b7280;">-- Time Travel: query data as it was 24 hours ago (Snowflake exclusive)</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> sales_raw AT (OFFSET => -<span style="color:#fcd34d;">86400</span>) <span style="color:#c4b5fd;">WHERE</span> region = <span style="color:#a7f3d0;">'AMER'</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>REGION  MONTH       REVENUE         PREV_MONTH_REVENUE  MOM_GROWTH_PCT
AMER    2024-01-01  34,821,044.12   NULL                NULL
AMER    2024-02-01  36,914,218.77   34,821,044.12       6.01
AMER    2024-03-01  39,847,331.44   36,914,218.77       7.94
EMEA    2024-01-01  24,103,872.19   NULL                NULL
EMEA    2024-02-01  25,891,044.67   24,103,872.19       7.41
EMEA    2024-03-01  27,128,441.83   25,891,044.67       4.78</div>
  </div>
</div>

<h3>Google BigQuery: Serverless, Pay-Per-Query Analytics</h3>
<p><strong>BigQuery</strong> is Google's fully serverless data warehouse — there are no clusters to provision, no warehouses to size, no capacity to manage. You run a SQL query and BigQuery automatically allocates compute (measured in <em>slots</em> — units of compute capacity) to execute it, then releases that compute immediately. You pay only for the bytes scanned by each query ($5 per TB scanned in on-demand mode) or a flat reservation of slots. BigQuery's architecture uses Google's Dremel engine — a massively parallel query execution system capable of scanning petabytes in seconds through a multi-level serving tree of thousands of nodes.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — BigQuery: Python Client, Cost Estimation & Partitioned Tables</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> google.cloud <span style="color:#c4b5fd;">import</span> bigquery
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>

<span style="color:#93c5fd;">client</span> = bigquery.Client(project=<span style="color:#a7f3d0;">'my-gcp-project'</span>)

<span style="color:#6b7280;"># ── Estimate query cost BEFORE running (dry_run mode) ─────────────────</span>
<span style="color:#93c5fd;">query</span>   = <span style="color:#a7f3d0;">"""
    SELECT customer_id, SUM(amount) AS total_spend
    FROM  `my-gcp-project.analytics.transactions`
    WHERE DATE(transaction_ts) >= '2024-01-01'
      AND region = 'AMER'
    GROUP BY customer_id
    ORDER BY total_spend DESC
    LIMIT 100
"""</span>

<span style="color:#93c5fd;">dry_job</span> = client.query(query, job_config=bigquery.QueryJobConfig(dry_run=<span style="color:#fca5a5;">True</span>))
<span style="color:#93c5fd;">bytes_scanned</span> = dry_job.total_bytes_processed
<span style="color:#93c5fd;">cost_usd</span>      = (bytes_scanned / <span style="color:#fcd34d;">1e12</span>) * <span style="color:#fcd34d;">5.0</span>   <span style="color:#6b7280;"># $5/TB</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Bytes to scan: {bytes_scanned/1e9:.2f} GB   Estimated cost: ${cost_usd:.4f}"</span>)

<span style="color:#6b7280;"># ── Run query and load results to pandas DataFrame ─────────────────────</span>
<span style="color:#93c5fd;">df</span> = client.query(query).to_dataframe()
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTop 5 customers by spend in AMER, 2024:"</span>)
<span style="color:#93c5fd;">print</span>(df.head(<span style="color:#fcd34d;">5</span>).to_string(index=<span style="color:#fca5a5;">False</span>))

<span style="color:#6b7280;"># ── Create a partitioned table (partition by day to reduce scan cost) ──</span>
<span style="color:#93c5fd;">schema</span> = [
    bigquery.SchemaField(<span style="color:#a7f3d0;">"order_id"</span>,    <span style="color:#a7f3d0;">"INTEGER"</span>),
    bigquery.SchemaField(<span style="color:#a7f3d0;">"customer_id"</span>, <span style="color:#a7f3d0;">"INTEGER"</span>),
    bigquery.SchemaField(<span style="color:#a7f3d0;">"amount"</span>,      <span style="color:#a7f3d0;">"FLOAT"</span>),
    bigquery.SchemaField(<span style="color:#a7f3d0;">"region"</span>,      <span style="color:#a7f3d0;">"STRING"</span>),
    bigquery.SchemaField(<span style="color:#a7f3d0;">"transaction_ts"</span>, <span style="color:#a7f3d0;">"TIMESTAMP"</span>),
]
<span style="color:#93c5fd;">table_ref</span> = bigquery.Table(<span style="color:#a7f3d0;">"my-gcp-project.analytics.orders_partitioned"</span>, schema=schema)
<span style="color:#93c5fd;">table_ref</span>.time_partitioning = bigquery.TimePartitioning(
    type_=bigquery.TimePartitioningType.DAY, field=<span style="color:#a7f3d0;">"transaction_ts"</span>
)
<span style="color:#93c5fd;">table_ref</span>.clustering_fields = [<span style="color:#a7f3d0;">"region"</span>, <span style="color:#a7f3d0;">"customer_id"</span>]
<span style="color:#93c5fd;">client</span>.create_table(table_ref, exists_ok=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\nCreated partitioned+clustered table — queries on date + region are 10-100x cheaper"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Bytes to scan: 18.43 GB   Estimated cost: $0.0922

Top 5 customers by spend in AMER, 2024:
 customer_id  total_spend
       94821   204319.44
       17483   188032.11
       52917   176448.90
       83641   162917.55
       29054   155841.22

Created partitioned+clustered table — queries on date + region are 10-100x cheaper</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.6 Cloud Data Warehouses: Snowflake, BigQuery & Redshift',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L22_6', [
                ['q' => 'What is Snowflake\'s defining architectural innovation over traditional data warehouses?', 'opts' => ['It uses a proprietary SQL dialect incompatible with other systems', 'Complete separation of storage, compute, and services into independent layers — enabling multiple Virtual Warehouses to query the same data simultaneously without resource contention', 'It stores all data in RAM for maximum query speed', 'It is only available on AWS'], 'ans' => 1, 'exp' => "Snowflake's three-layer architecture: (1) Storage — shared S3/GCS/Azure Blob columnar micro-partitions, (2) Compute — independent Virtual Warehouses that can be started/stopped/resized instantly, (3) Cloud Services — query optimisation, metadata, transactions. Multiple VWs can read the same storage simultaneously with zero contention — your BI and ETL workloads don't fight for resources."],
                ['q' => 'BigQuery\'s dry_run query job mode is used to:', 'opts' => ['Test query syntax without executing against real data', 'Estimate the bytes that would be scanned and therefore the cost before actually running the query', 'Run the query in a development environment sandbox', 'Cache query results for 24 hours'], 'ans' => 1, 'exp' => "BigQuery's on-demand pricing charges $5 per TB scanned. Before running expensive queries against production tables, dry_run=True returns the bytes_processed estimate without executing — costing nothing. Use this to validate that partition pruning and column selection are working correctly, preventing surprise bills from accidentally scanning petabytes."],
                ['q' => 'Table partitioning and clustering in BigQuery/Snowflake reduce query costs by:', 'opts' => ['Compressing data more aggressively', 'Allowing the query engine to skip entire partitions and data blocks irrelevant to the query filter, dramatically reducing bytes scanned', 'Converting STRING columns to INTEGER for faster comparison', 'Caching query results automatically'], 'ans' => 1, 'exp' => 'Partition pruning: WHERE transaction_ts >= \'2024-03-01\' scans only March 2024 partition files — skipping years of historical data. Clustering: WHERE region = \'AMER\' on a region-clustered table accesses only the storage blocks that contain AMER rows — further reducing I/O within each partition. Together they can reduce scan costs by 99% on large tables.'],
                ['q' => 'Snowflake\'s Time Travel feature allows you to:', 'opts' => ['Schedule queries to run at specific times in the future', 'Query a table as it existed at any point within the retention period (up to 90 days), enabling recovery from accidental deletes or updates', 'Partition data by time without any configuration', 'Roll back Virtual Warehouse versions after an upgrade'], 'ans' => 1, 'exp' => "Snowflake's Time Travel uses AT (TIMESTAMP => ...) or AT (OFFSET => -86400) syntax to query historical table states. Practical uses: undoing an accidental DELETE (UNDROP TABLE), debugging data quality issues by comparing today's data to yesterday's, and auditing who changed what and when. The retention period (1-90 days) is configurable per table."],
                ['q' => 'The key difference between OLAP (data warehouse) and OLTP (operational database) workload patterns is:', 'opts' => ['OLAP handles one row at a time; OLTP handles billions of rows', 'OLAP is designed for large analytical scans and aggregations across many columns and rows; OLTP is optimised for fast individual row inserts, updates, and lookups with ACID guarantees', 'OLAP uses SQL; OLTP uses NoSQL', 'OLAP databases are stored in RAM; OLTP databases use disk only'], 'ans' => 1, 'exp' => "OLTP (e.g., PostgreSQL): thousands of concurrent users inserting single rows, fetching by primary key, transactional consistency — millisecond latency per operation. OLAP (e.g., BigQuery): data analysts running complex GROUP BY queries across billions of rows — scan-heavy, column-oriented, no write contention. Mixing them causes performance degradation on both: each is optimised for its opposite workload."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.7 — Data Pipelines, ETL/ELT & Workflow Orchestration with Airflow
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Data Pipelines, ETL/ELT & Workflow Orchestration with Apache Airflow</h2>
<p>A <strong>data pipeline</strong> is a sequence of automated steps that moves data from source systems, transforms it into an analytically useful form, and delivers it to consumers. The overwhelming majority of a data engineer's day is spent designing, building, debugging, and maintaining data pipelines — not writing Spark code or SQL queries. Getting pipelines right — reliable, observable, recoverable, testable — is what separates production-grade data engineering from ad-hoc scripting.</p>

<p>Two architectural patterns dominate: <strong>ETL</strong> (Extract, Transform, Load) — the traditional approach where data is extracted from sources, transformed in a dedicated compute layer, then loaded to the destination. And <strong>ELT</strong> (Extract, Load, Transform) — the modern cloud-native approach where raw data is loaded directly into the warehouse first, then transformed in-place using the warehouse's own compute (via dbt SQL models). ELT is now dominant because cloud warehouses are fast enough for in-warehouse transformation and it preserves raw data for reprocessing.</p>

<h3>Apache Airflow: The Pipeline Orchestration Standard</h3>
<p><strong>Apache Airflow</strong> (developed at Airbnb, open-sourced 2015) is the dominant workflow orchestration platform for data engineering. Airflow lets you define pipelines as Python code using a <strong>DAG</strong> (Directed Acyclic Graph) — a Python file where you declare tasks and their dependencies. Airflow schedules DAGs on a cron-like schedule, executes tasks on worker nodes, retries failed tasks automatically, provides a web UI for monitoring, and maintains a complete history of every run and its logs.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Airflow DAG: Daily Sales Pipeline with Sensors & Operators</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> airflow <span style="color:#c4b5fd;">import</span> DAG
<span style="color:#c4b5fd;">from</span> airflow.operators.python <span style="color:#c4b5fd;">import</span> PythonOperator
<span style="color:#c4b5fd;">from</span> airflow.operators.bash <span style="color:#c4b5fd;">import</span> BashOperator
<span style="color:#c4b5fd;">from</span> airflow.providers.amazon.aws.sensors.s3 <span style="color:#c4b5fd;">import</span> S3KeySensor
<span style="color:#c4b5fd;">from</span> airflow.providers.snowflake.operators.snowflake <span style="color:#c4b5fd;">import</span> SnowflakeOperator
<span style="color:#c4b5fd;">from</span> datetime <span style="color:#c4b5fd;">import</span> datetime, timedelta

<span style="color:#6b7280;"># Default task settings applied to every task in this DAG</span>
<span style="color:#93c5fd;">default_args</span> = {
    <span style="color:#a7f3d0;">'owner'</span>:            <span style="color:#a7f3d0;">'data-engineering'</span>,
    <span style="color:#a7f3d0;">'retries'</span>:          <span style="color:#fcd34d;">3</span>,
    <span style="color:#a7f3d0;">'retry_delay'</span>:      timedelta(minutes=<span style="color:#fcd34d;">5</span>),
    <span style="color:#a7f3d0;">'email_on_failure'</span>: <span style="color:#fca5a5;">True</span>,
    <span style="color:#a7f3d0;">'email'</span>:            [<span style="color:#a7f3d0;">'data-alerts@company.com'</span>],
}

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">validate_raw_data</span>(**context):
    <span style="color:#a7f3d0;">"""Quality check: ensure row count matches expected range."""</span>
    ds = context[<span style="color:#a7f3d0;">'ds'</span>]   <span style="color:#6b7280;"># execution date in YYYY-MM-DD format</span>
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Validating data for {ds}..."</span>)
    <span style="color:#6b7280;"># In production: connect to S3, count rows, check nulls, raise if bad</span>
    <span style="color:#93c5fd;">row_count</span> = <span style="color:#fcd34d;">4_182_341</span>   <span style="color:#6b7280;"># simulated result</span>
    <span style="color:#c4b5fd;">if</span> row_count &lt; <span style="color:#fcd34d;">1_000_000</span>:
        <span style="color:#c4b5fd;">raise</span> <span style="color:#93c5fd;">ValueError</span>(<span style="color:#a7f3d0;">f"Row count {row_count} below threshold — pipeline aborted"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Validation passed: {row_count:,} rows ✓"</span>)

<span style="color:#c4b5fd;">with</span> DAG(
    dag_id=<span style="color:#a7f3d0;">'daily_sales_pipeline'</span>,
    schedule_interval=<span style="color:#a7f3d0;">'0 6 * * *'</span>,        <span style="color:#6b7280;"># Run daily at 06:00 UTC</span>
    start_date=datetime(<span style="color:#fcd34d;">2024</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1</span>),
    catchup=<span style="color:#fca5a5;">False</span>,                         <span style="color:#6b7280;"># Don't backfill missed runs</span>
    default_args=default_args,
    tags=[<span style="color:#a7f3d0;">'sales'</span>, <span style="color:#a7f3d0;">'critical'</span>],
) <span style="color:#c4b5fd;">as</span> dag:

    <span style="color:#6b7280;"># Task 1: Wait for upstream data file to appear in S3</span>
    <span style="color:#93c5fd;">wait_for_file</span> = S3KeySensor(
        task_id=<span style="color:#a7f3d0;">'wait_for_s3_file'</span>,
        bucket_name=<span style="color:#a7f3d0;">'my-data-lake-prod'</span>,
        bucket_key=<span style="color:#a7f3d0;">'raw/sales/year={{ ds[:4] }}/month={{ ds[5:7] }}/sales_{{ ds }}.parquet'</span>,
        timeout=<span style="color:#fcd34d;">3600</span>,       <span style="color:#6b7280;"># fail if not found within 1 hour</span>
        poke_interval=<span style="color:#fcd34d;">60</span>,   <span style="color:#6b7280;"># check every 60 seconds</span>
    )

    <span style="color:#6b7280;"># Task 2: Validate raw data quality</span>
    <span style="color:#93c5fd;">validate</span> = PythonOperator(
        task_id=<span style="color:#a7f3d0;">'validate_raw_data'</span>,
        python_callable=validate_raw_data,
    )

    <span style="color:#6b7280;"># Task 3: Load raw Parquet from S3 into Snowflake staging table</span>
    <span style="color:#93c5fd;">load_staging</span> = SnowflakeOperator(
        task_id=<span style="color:#a7f3d0;">'load_to_snowflake_staging'</span>,
        snowflake_conn_id=<span style="color:#a7f3d0;">'snowflake_prod'</span>,
        sql=<span style="color:#a7f3d0;">"""
            COPY INTO SALES_STAGING FROM @S3_STAGE/raw/sales/
            FILE_FORMAT=(TYPE='PARQUET') PURGE=FALSE ON_ERROR='ABORT_STATEMENT';
        """</span>,
    )

    <span style="color:#6b7280;"># Task 4: Run dbt models to transform staging → production tables</span>
    <span style="color:#93c5fd;">run_dbt</span> = BashOperator(
        task_id=<span style="color:#a7f3d0;">'run_dbt_models'</span>,
        bash_command=<span style="color:#a7f3d0;">'dbt run --select tag:sales --profiles-dir /opt/dbt'</span>,
    )

    <span style="color:#6b7280;"># Define task dependencies: wait → validate → load → transform</span>
    <span style="color:#93c5fd;">wait_for_file</span> >> <span style="color:#93c5fd;">validate</span> >> <span style="color:#93c5fd;">load_staging</span> >> <span style="color:#93c5fd;">run_dbt</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[2024-03-15 06:00:00] DAG daily_sales_pipeline triggered for 2024-03-14
[2024-03-15 06:00:02] wait_for_s3_file: poking s3://my-data-lake-prod/raw/sales/.../sales_2024-03-14.parquet
[2024-03-15 06:01:03] wait_for_s3_file: File found! Status: SUCCESS ✓
[2024-03-15 06:01:05] validate_raw_data: Validating data for 2024-03-14...
[2024-03-15 06:01:08] validate_raw_data: Validation passed: 4,182,341 rows ✓
[2024-03-15 06:01:09] load_to_snowflake_staging: COPY INTO SALES_STAGING... 4,182,341 rows loaded
[2024-03-15 06:04:22] run_dbt_models: Running with dbt=1.7.0... 12 models completed ✓</div>
  </div>
</div>

<h3>dbt: The SQL Transformation Layer</h3>
<p><strong>dbt</strong> (data build tool) has become the standard tool for the Transform step in ELT pipelines. dbt lets data analysts and engineers write SQL SELECT statements that define transformed tables — dbt handles the CREATE TABLE AS SELECT, dependency management between models, incremental loading, testing (assert that no NULL primary keys exist, that revenue is always positive), documentation, and lineage graphing automatically. dbt's killer feature is that it brings software engineering best practices to SQL: version control in Git, peer-reviewed pull requests for SQL changes, automated testing on every commit, and reproducible deployments.</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.7 Data Pipelines, ETL/ELT & Workflow Orchestration with Airflow',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L22_7', [
                ['q' => 'What is the key difference between ETL and ELT approaches to data pipelines?', 'opts' => ['ETL uses Python; ELT uses SQL', 'ETL transforms data before loading into the destination; ELT loads raw data first then transforms it in-place using the warehouse\'s own compute', 'ETL is batch-only; ELT supports streaming', 'ETL is for small data; ELT is for big data only'], 'ans' => 1, 'exp' => 'ETL: Extract → Transform (in an external compute layer like Spark) → Load clean data. ELT: Extract → Load raw data immediately → Transform in-place using warehouse SQL (via dbt). ELT is preferred in cloud-native stacks because warehouses are now fast enough for in-warehouse transformation, raw data is preserved for reprocessing, and SQL-based transformations are easier to audit and version-control.'],
                ['q' => 'In Apache Airflow, a DAG (Directed Acyclic Graph) represents:', 'opts' => ['A distributed database cluster for storing pipeline metadata', 'A Python file defining pipeline tasks and their dependencies — the execution graph that Airflow schedules, monitors, and retries', 'A NoSQL document that stores pipeline configuration', 'A type of Kafka consumer group for stream processing'], 'ans' => 1, 'exp' => 'An Airflow DAG is Python code defining: what tasks to run (Operators), how they depend on each other (>>, set_downstream), when to run (schedule_interval), and what to do on failure (retries, email alerts). Airflow\'s scheduler reads DAG files, creates DAG runs, and dispatches tasks to worker nodes.'],
                ['q' => 'What does an S3KeySensor in Airflow do?', 'opts' => ['Encrypts files stored in S3', 'Polls S3 at regular intervals waiting for a specific file or key pattern to appear before allowing downstream tasks to proceed', 'Reads and parses S3 access logs for billing', 'Compresses files in S3 automatically when disk usage exceeds a threshold'], 'ans' => 1, 'exp' => "Sensors are a special Airflow operator type that pauses execution until a condition is met. S3KeySensor pokes (checks) an S3 path every poke_interval seconds until the file appears or the timeout is reached. This implements data-arrival dependencies — your pipeline doesn't start processing until the upstream data is actually there."],
                ['q' => 'What is dbt\'s primary contribution to the data engineering workflow?', 'opts' => ['It replaces Apache Spark for distributed computation', 'It brings software engineering best practices (version control, testing, documentation, modular design) to SQL-based data transformation inside the warehouse', 'It is a streaming framework for real-time SQL queries', 'It manages Airflow DAG scheduling and monitoring'], 'ans' => 1, 'exp' => 'dbt makes analysts write SELECT statements (not CREATE TABLE AS SELECT). dbt handles materialisation, dependency resolution between models, incremental loading, data quality tests (not_null, unique, accepted_values, relationships), and generates documentation with lineage graphs. All SQL models live in Git, go through PR review, and are tested on every CI commit — exactly like software development.'],
                ['q' => 'The catchup=False parameter in an Airflow DAG means:', 'opts' => ['The DAG will not retry failed tasks', 'Airflow will not automatically run missed DAG executions for dates between start_date and today when the DAG is first activated', 'The DAG runs once and is then disabled', 'Task dependencies are ignored and all tasks run in parallel'], 'ans' => 1, 'exp' => "When catchup=True (default) and start_date='2024-01-01', activating the DAG today would trigger one run for EVERY day since January 1 — potentially hundreds of backfill runs. catchup=False means: only run for future schedules from now. For historical backfilling, use Airflow's manual trigger with a specific date range instead."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.8 — Data Lakes, Lake Houses & Delta Lake
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Data Lakes, Lakehouses & Delta Lake</h2>
<p>As organisations accumulated more data in more formats at greater velocity, the strict schemas and structured nature of traditional data warehouses became a limitation. The <strong>data lake</strong> concept emerged as a response: store all data — structured, semi-structured, and unstructured — in its raw form in cheap object storage (S3/GCS), with no schema requirements at write time. Data consumers impose schema at query time ("schema-on-read"). This enables flexibility, lower cost, and the ability to store data whose value or structure is not yet understood. However, raw data lakes quickly became "data swamps" — unmanaged, unreliable repositories where nobody could find, trust, or efficiently query data.</p>

<p>The <strong>Lakehouse</strong> architecture, pioneered by Databricks, addresses the data swamp problem by adding a transactional metadata layer on top of object storage. <strong>Delta Lake</strong>, <strong>Apache Iceberg</strong>, and <strong>Apache Hudi</strong> are open-source table formats that bring ACID transactions, schema evolution, time travel, and efficient query execution to data lake storage — giving you the reliability of a warehouse with the flexibility and cost of a lake.</p>

<h3>Delta Lake: ACID Transactions on Object Storage</h3>
<p>Delta Lake stores data as Parquet files in S3/GCS but adds a <strong>Delta transaction log</strong> (a folder of JSON files tracking every operation) alongside them. Every INSERT, UPDATE, DELETE, and MERGE is an atomic transaction logged before execution. If a job fails midway through writing, the partial writes are not committed to the log — readers never see inconsistent data. This is the foundational guarantee that separates Delta Lake from plain Parquet on S3.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Delta Lake: ACID Writes, MERGE & Time Travel with PySpark</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> pyspark.sql <span style="color:#c4b5fd;">import</span> SparkSession
<span style="color:#c4b5fd;">from</span> delta <span style="color:#c4b5fd;">import</span> DeltaTable
<span style="color:#c4b5fd;">from</span> pyspark.sql <span style="color:#c4b5fd;">import</span> functions <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">F</span>

<span style="color:#93c5fd;">spark</span> = SparkSession.builder \
    .appName(<span style="color:#a7f3d0;">"DeltaLakeDemo"</span>) \
    .config(<span style="color:#a7f3d0;">"spark.jars.packages"</span>, <span style="color:#a7f3d0;">"io.delta:delta-core_2.12:2.4.0"</span>) \
    .config(<span style="color:#a7f3d0;">"spark.sql.extensions"</span>, <span style="color:#a7f3d0;">"io.delta.sql.DeltaSparkSessionExtension"</span>) \
    .getOrCreate()

<span style="color:#93c5fd;">DELTA_PATH</span> = <span style="color:#a7f3d0;">"s3://my-lakehouse/gold/customers/"</span>

<span style="color:#6b7280;"># ── Write initial data as a Delta table ───────────────────────────────</span>
<span style="color:#93c5fd;">customers_v1</span> = spark.createDataFrame([
    (<span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">"Alice Chen"</span>,    <span style="color:#a7f3d0;">"alice@example.com"</span>,   <span style="color:#a7f3d0;">"active"</span>),
    (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"Bob Martinez"</span>,  <span style="color:#a7f3d0;">"bob@example.com"</span>,     <span style="color:#a7f3d0;">"active"</span>),
    (<span style="color:#fcd34d;">3</span>, <span style="color:#a7f3d0;">"Carol Smith"</span>,   <span style="color:#a7f3d0;">"carol@example.com"</span>,   <span style="color:#a7f3d0;">"inactive"</span>),
], [<span style="color:#a7f3d0;">"customer_id"</span>, <span style="color:#a7f3d0;">"name"</span>, <span style="color:#a7f3d0;">"email"</span>, <span style="color:#a7f3d0;">"status"</span>])

<span style="color:#93c5fd;">customers_v1</span>.write.format(<span style="color:#a7f3d0;">"delta"</span>).mode(<span style="color:#a7f3d0;">"overwrite"</span>).save(<span style="color:#93c5fd;">DELTA_PATH</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Version 0 written: 3 customers"</span>)

<span style="color:#6b7280;"># ── MERGE (upsert): update existing + insert new customers ─────────────</span>
<span style="color:#93c5fd;">updates</span> = spark.createDataFrame([
    (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">"Bob Martinez"</span>,  <span style="color:#a7f3d0;">"bob.new@example.com"</span>, <span style="color:#a7f3d0;">"active"</span>),    <span style="color:#6b7280;"># email changed</span>
    (<span style="color:#fcd34d;">4</span>, <span style="color:#a7f3d0;">"Diana Wong"</span>,    <span style="color:#a7f3d0;">"diana@example.com"</span>,  <span style="color:#a7f3d0;">"active"</span>),    <span style="color:#6b7280;"># new customer</span>
], [<span style="color:#a7f3d0;">"customer_id"</span>, <span style="color:#a7f3d0;">"name"</span>, <span style="color:#a7f3d0;">"email"</span>, <span style="color:#a7f3d0;">"status"</span>])

<span style="color:#93c5fd;">delta_table</span> = DeltaTable.forPath(spark, <span style="color:#93c5fd;">DELTA_PATH</span>)
<span style="color:#93c5fd;">delta_table</span>.alias(<span style="color:#a7f3d0;">"existing"</span>).merge(
    updates.alias(<span style="color:#a7f3d0;">"updates"</span>),
    <span style="color:#a7f3d0;">"existing.customer_id = updates.customer_id"</span>
).whenMatchedUpdateAll() \
 .whenNotMatchedInsertAll() \
 .execute()

<span style="color:#6b7280;"># ── Time Travel: read data at version 0 (before merge) ────────────────</span>
<span style="color:#93c5fd;">v0</span> = spark.read.format(<span style="color:#a7f3d0;">"delta"</span>).option(<span style="color:#a7f3d0;">"versionAsOf"</span>, <span style="color:#fcd34d;">0</span>).load(<span style="color:#93c5fd;">DELTA_PATH</span>)
<span style="color:#93c5fd;">v1</span> = spark.read.format(<span style="color:#a7f3d0;">"delta"</span>).option(<span style="color:#a7f3d0;">"versionAsOf"</span>, <span style="color:#fcd34d;">1</span>).load(<span style="color:#93c5fd;">DELTA_PATH</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nVersion 0: {v0.count()} rows | Version 1 (after merge): {v1.count()} rows"</span>)

<span style="color:#6b7280;"># ── Inspect the Delta transaction log ─────────────────────────────────</span>
<span style="color:#93c5fd;">history</span> = delta_table.history()
<span style="color:#93c5fd;">history</span>.select(<span style="color:#a7f3d0;">"version"</span>,<span style="color:#a7f3d0;">"timestamp"</span>,<span style="color:#a7f3d0;">"operation"</span>,<span style="color:#a7f3d0;">"operationMetrics"</span>).show(truncate=<span style="color:#fca5a5;">False</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Version 0 written: 3 customers

Version 0: 3 rows | Version 1 (after merge): 4 rows

+-------+-------------------+---------+-------------------------------------------+
|version|timestamp          |operation|operationMetrics                           |
+-------+-------------------+---------+-------------------------------------------+
|1      |2024-03-15 06:04:11|MERGE    |{numTargetRowsUpdated=1,numTargetRowsInserted=1}|
|0      |2024-03-15 06:01:09|WRITE    |{numOutputRows=3, numFiles=2}              |
+-------+-------------------+---------+-------------------------------------------+</div>
  </div>
</div>

<h3>Data Lake Zones: Bronze, Silver & Gold</h3>
<p>The <strong>Medallion Architecture</strong> (popularised by Databricks) organises a lakehouse into three quality zones. <strong>Bronze</strong> (raw): exact copies of source data, never modified, retained forever — the source of truth for reprocessing. <strong>Silver</strong> (cleansed): validated, deduplicated, typed, and lightly transformed data — the "cleaned room" for joining and filtering. <strong>Gold</strong> (curated): business-level aggregates, star-schema fact and dimension tables, ML features — what BI tools and dashboards read directly. Each zone is a Delta Lake table with ACID guarantees, version history, and schema enforcement.</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.8 Data Lakes, Lakehouses & Delta Lake',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L22_8', [
                ['q' => 'What specific problem do table formats like Delta Lake, Iceberg, and Hudi solve over plain Parquet on S3?', 'opts' => ['They provide better compression ratios than standard Parquet', 'They add ACID transactions, schema enforcement, time travel, and efficient updates/deletes to object storage — preventing the "data swamp" of unreliable, inconsistent data lakes', 'They enable Parquet files to be queried with NoSQL APIs', 'They automatically replicate data across cloud providers'], 'ans' => 1, 'exp' => 'Plain Parquet on S3 has no atomicity — a failed write leaves partial files; no isolation — concurrent readers may see partial writes; no update/delete capability — you must rewrite entire files. Delta Lake adds a transaction log that makes every write atomic. Readers always see consistent snapshots. UPDATE, DELETE, and MERGE operations become possible without rewriting all data.'],
                ['q' => 'The Delta Lake MERGE operation is useful because:', 'opts' => ['It compresses multiple Parquet files into a single large file', 'It implements upsert logic — updating existing records and inserting new ones in a single atomic operation, essential for CDC and SCD Type 2 patterns', 'It merges two Delta tables into a single schema-unified table', 'It automatically tunes partition sizes for optimal query performance'], 'ans' => 1, 'exp' => "MERGE (upsert) is critical for Change Data Capture (CDC): when source records are updated, you MERGE them into your Delta table — updating existing rows that match and inserting new rows that don't. Before Delta Lake, this required read-modify-write cycles on entire Parquet partitions. MERGE is atomic, isolated, and far more efficient than full rewrites."],
                ['q' => 'In the Medallion Architecture, what distinguishes a "Gold" layer table from a "Silver" layer table?', 'opts' => ['Gold tables are unprocessed raw copies; Silver tables are fully curated', 'Silver tables are cleansed and validated data; Gold tables are business-level aggregates and star-schema models ready for BI tools and dashboards', 'Gold tables use Parquet; Silver tables use CSV', 'Gold tables are stored in the data warehouse; Silver tables are stored in the data lake'], 'ans' => 1, 'exp' => 'Bronze: raw, unmodified source data (exact copy, retained forever). Silver: cleaned, deduplicated, typed, joined data — good for data scientists and SQL analysts to query. Gold: business-curated aggregates, fact tables, dimension tables, pre-computed metrics — what Looker, Metabase, and executive dashboards read. Gold tables are optimised for consumption speed and business semantics.'],
                ['q' => 'Delta Lake\'s time travel feature works by:', 'opts' => ['Creating physical backup copies of all data at each write', 'Maintaining a transaction log of all operations — allowing any historical version to be reconstructed by reading the log up to the desired version number or timestamp', 'Storing incremental diffs between table versions as binary patches', 'Replicating all writes to a separate S3 bucket dedicated to historical versions'], 'ans' => 1, 'exp' => "Delta Lake maintains a _delta_log/ folder containing JSON commit files (one per transaction). Each commit records what Parquet files were added and removed. To read version N, Delta rebuilds the table state by replaying the log from version 0 to N. No duplicate data storage — just metadata pointing to existing Parquet files. Time travel doesn't cost extra storage for historical reads."],
                ['q' => 'A "schema-on-read" data lake differs from a "schema-on-write" data warehouse in that:', 'opts' => ['Schema-on-read enforces strict types at write time; schema-on-write is flexible', 'Schema-on-read stores raw data without enforcing structure, applying schema interpretation only when queried; schema-on-write enforces schema at write time, rejecting non-conforming data', 'Schema-on-read is only possible with Parquet files', 'Schema-on-write is used exclusively by streaming systems'], 'ans' => 1, 'exp' => "Schema-on-write (data warehouse): you define the schema first; data that doesn't match is rejected at load. Schema-on-read (data lake): data is stored as-is in its original format; the reader applies a schema when querying. Schema-on-read is more flexible (store anything, figure out structure later) but less safe (garbage data passes through). Lakehouses like Delta Lake blend both: write whatever you want initially, then enforce schema as the data matures."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.9 — Cloud Security, Governance & Compliance for Big Data
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Cloud Security, Governance & Compliance for Big Data</h2>
<p>The same properties that make cloud big data systems powerful — massive scale, broad accessibility, connectivity between services — also make them uniquely dangerous if misconfigured. A single public S3 bucket misconfiguration has exposed the data of millions of people. A poorly scoped IAM role has enabled attackers to exfiltrate entire databases. The cost of a data breach — regulatory fines, reputational damage, litigation, remediation — routinely exceeds the cost of the infrastructure that was compromised by orders of magnitude. Security and governance are not optional add-ons for big data engineers; they are core engineering responsibilities.</p>

<h3>The Shared Responsibility Model</h3>
<p>Every major cloud provider operates under a <strong>Shared Responsibility Model</strong>: the provider is responsible for the security <em>of</em> the cloud (physical datacentres, hardware, network infrastructure, hypervisor), while the customer is responsible for security <em>in</em> the cloud (IAM configuration, network security groups, encryption, application security, data classification). Misunderstanding this boundary is the most common source of cloud security incidents. AWS securing its S3 service does not prevent you from accidentally making your bucket publicly accessible.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — AWS IAM: Least-Privilege Role Policy & S3 Bucket Hardening</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> json, boto3

<span style="color:#6b7280;"># ── Least-privilege IAM policy for a Spark ETL role ──────────────────</span>
<span style="color:#93c5fd;">etl_policy</span> = {
    <span style="color:#a7f3d0;">"Version"</span>: <span style="color:#a7f3d0;">"2012-10-17"</span>,
    <span style="color:#a7f3d0;">"Statement"</span>: [
        {
            <span style="color:#a7f3d0;">"Sid"</span>:    <span style="color:#a7f3d0;">"ReadRawData"</span>,
            <span style="color:#a7f3d0;">"Effect"</span>: <span style="color:#a7f3d0;">"Allow"</span>,
            <span style="color:#a7f3d0;">"Action"</span>: [<span style="color:#a7f3d0;">"s3:GetObject"</span>, <span style="color:#a7f3d0;">"s3:ListBucket"</span>],
            <span style="color:#a7f3d0;">"Resource"</span>: [
                <span style="color:#a7f3d0;">"arn:aws:s3:::my-data-lake/raw/*"</span>,
                <span style="color:#a7f3d0;">"arn:aws:s3:::my-data-lake"</span>,
            ],
        },
        {
            <span style="color:#a7f3d0;">"Sid"</span>:    <span style="color:#a7f3d0;">"WriteProcessedData"</span>,
            <span style="color:#a7f3d0;">"Effect"</span>: <span style="color:#a7f3d0;">"Allow"</span>,
            <span style="color:#a7f3d0;">"Action"</span>: [<span style="color:#a7f3d0;">"s3:PutObject"</span>, <span style="color:#a7f3d0;">"s3:DeleteObject"</span>],
            <span style="color:#a7f3d0;">"Resource"</span>: <span style="color:#a7f3d0;">"arn:aws:s3:::my-data-lake/processed/*"</span>,
        },
        <span style="color:#6b7280;"># Explicitly DENY access to production tables (defence in depth)</span>
        {
            <span style="color:#a7f3d0;">"Sid"</span>:    <span style="color:#a7f3d0;">"DenyProductionBucket"</span>,
            <span style="color:#a7f3d0;">"Effect"</span>: <span style="color:#a7f3d0;">"Deny"</span>,
            <span style="color:#a7f3d0;">"Action"</span>: <span style="color:#a7f3d0;">"s3:*"</span>,
            <span style="color:#a7f3d0;">"Resource"</span>: <span style="color:#a7f3d0;">"arn:aws:s3:::my-data-lake/gold/pii/*"</span>,
        },
    ],
}

<span style="color:#6b7280;"># ── S3 Bucket hardening: block all public access programmatically ─────</span>
<span style="color:#93c5fd;">s3</span> = boto3.client(<span style="color:#a7f3d0;">'s3'</span>)
<span style="color:#93c5fd;">s3</span>.put_public_access_block(
    Bucket=<span style="color:#a7f3d0;">'my-data-lake'</span>,
    PublicAccessBlockConfiguration={
        <span style="color:#a7f3d0;">'BlockPublicAcls'</span>:       <span style="color:#fca5a5;">True</span>,
        <span style="color:#a7f3d0;">'IgnorePublicAcls'</span>:      <span style="color:#fca5a5;">True</span>,
        <span style="color:#a7f3d0;">'BlockPublicPolicy'</span>:     <span style="color:#fca5a5;">True</span>,
        <span style="color:#a7f3d0;">'RestrictPublicBuckets'</span>: <span style="color:#fca5a5;">True</span>,
    }
)

<span style="color:#6b7280;"># ── Enable server-side encryption (SSE-KMS) on all new objects ────────</span>
<span style="color:#93c5fd;">s3</span>.put_bucket_encryption(
    Bucket=<span style="color:#a7f3d0;">'my-data-lake'</span>,
    ServerSideEncryptionConfiguration={
        <span style="color:#a7f3d0;">'Rules'</span>: [{
            <span style="color:#a7f3d0;">'ApplyServerSideEncryptionByDefault'</span>: {
                <span style="color:#a7f3d0;">'SSEAlgorithm'</span>:   <span style="color:#a7f3d0;">'aws:kms'</span>,
                <span style="color:#a7f3d0;">'KMSMasterKeyID'</span>: <span style="color:#a7f3d0;">'arn:aws:kms:us-east-1:123456:key/abc-123'</span>,
            }
        }]
    }
)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"S3 bucket hardened: public access blocked ✓  KMS encryption enabled ✓"</span>)
<span style="color:#93c5fd;">print</span>(json.dumps(etl_policy, indent=<span style="color:#fcd34d;">2</span>)[:400] + <span style="color:#a7f3d0;">"..."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>S3 bucket hardened: public access blocked ✓  KMS encryption enabled ✓
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Sid": "ReadRawData",
      "Effect": "Allow",
      "Action": ["s3:GetObject", "s3:ListBucket"],
      "Resource": ["arn:aws:s3:::my-data-lake/raw/*", "arn:aws:s3:::my-data-lake"]
    },
    {
      "Sid": "WriteProcessedData",
      "Effect": "Allow",
      "Action": ["s3:PutObject", "s3:DeleteObject"],
...</div>
  </div>
</div>

<h3>Data Governance: Catalogues, Lineage & PII Classification</h3>
<p><strong>Data governance</strong> is the collection of policies, processes, roles, and standards that ensure data is accurate, available, consistent, secure, and used appropriately across an organisation. At scale, governance solves the fundamental question: "Where is the data? Who owns it? Is it trustworthy? Who can access it? Where did it come from and where does it go?" Without governance, large organisations end up with dozens of data teams each building their own version of the same metrics — producing different answers to the same business question.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — PII Detection & Column-Level Data Classification</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> re, pandas <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">pd</span>

<span style="color:#6b7280;"># Lightweight PII classifier for data cataloguing</span>
<span style="color:#93c5fd;">PII_PATTERNS</span> = {
    <span style="color:#a7f3d0;">"email"</span>:    <span style="color:#a7f3d0;">r'[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}'</span>,
    <span style="color:#a7f3d0;">"phone"</span>:    <span style="color:#a7f3d0;">r'\b\d{3}[-.\s]?\d{3}[-.\s]?\d{4}\b'</span>,
    <span style="color:#a7f3d0;">"ssn"</span>:      <span style="color:#a7f3d0;">r'\b\d{3}-\d{2}-\d{4}\b'</span>,
    <span style="color:#a7f3d0;">"credit_card"</span>: <span style="color:#a7f3d0;">r'\b(?:\d{4}[-\s]?){3}\d{4}\b'</span>,
    <span style="color:#a7f3d0;">"ip_address"</span>: <span style="color:#a7f3d0;">r'\b(?:\d{1,3}\.){3}\d{1,3}\b'</span>,
}

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">classify_column</span>(series: pd.Series, sample_size=<span style="color:#fcd34d;">1000</span>) -> <span style="color:#93c5fd;">dict</span>:
    <span style="color:#a7f3d0;">"""Detect PII type in a DataFrame column by sampling values."""</span>
    <span style="color:#93c5fd;">sample</span>   = series.dropna().astype(<span style="color:#93c5fd;">str</span>).head(sample_size)
    <span style="color:#93c5fd;">detected</span> = {}
    <span style="color:#c4b5fd;">for</span> pii_type, pattern <span style="color:#c4b5fd;">in</span> PII_PATTERNS.items():
        <span style="color:#93c5fd;">matches</span> = sample.str.contains(pattern, regex=<span style="color:#fca5a5;">True</span>, na=<span style="color:#fca5a5;">False</span>).mean()
        <span style="color:#c4b5fd;">if</span> matches &gt; <span style="color:#fcd34d;">0.5</span>:   <span style="color:#6b7280;"># >50% of values match → classify as this PII type</span>
            <span style="color:#93c5fd;">detected</span>[pii_type] = <span style="color:#a7f3d0;">f"{matches:.0%}"</span>
    <span style="color:#c4b5fd;">return</span> detected <span style="color:#c4b5fd;">if</span> detected <span style="color:#c4b5fd;">else</span> {<span style="color:#a7f3d0;">"classification"</span>: <span style="color:#a7f3d0;">"non-PII"</span>}

<span style="color:#6b7280;"># Test on a sample customer dataset</span>
<span style="color:#93c5fd;">df</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">"name"</span>:   [<span style="color:#a7f3d0;">"Alice"</span>, <span style="color:#a7f3d0;">"Bob"</span>,              <span style="color:#a7f3d0;">"Carol"</span>],
    <span style="color:#a7f3d0;">"email"</span>:  [<span style="color:#a7f3d0;">"a@x.com"</span>, <span style="color:#a7f3d0;">"b@y.com"</span>,        <span style="color:#a7f3d0;">"c@z.com"</span>],
    <span style="color:#a7f3d0;">"revenue"</span>:[<span style="color:#fcd34d;">1200</span>, <span style="color:#fcd34d;">3400</span>,                 <span style="color:#fcd34d;">890</span>],
    <span style="color:#a7f3d0;">"ssn"</span>:    [<span style="color:#a7f3d0;">"123-45-6789"</span>, <span style="color:#a7f3d0;">"987-65-4321"</span>, <span style="color:#a7f3d0;">"456-78-9012"</span>],
})

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{'Column':<12} {'PII Classification'}"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"─" * 40</span>)
<span style="color:#c4b5fd;">for</span> col <span style="color:#c4b5fd;">in</span> df.columns:
    <span style="color:#93c5fd;">result</span> = classify_column(df[col])
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"{col:<12} {result}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Column       PII Classification
----------------------------------------
name         {'classification': 'non-PII'}
email        {'email': '100%'}
revenue      {'classification': 'non-PII'}
ssn          {'ssn': '100%'}</div>
  </div>
</div>

<h3>Regulatory Compliance: GDPR, CCPA & the Right to be Forgotten</h3>
<p>Big data engineers must understand the regulatory landscape. <strong>GDPR</strong> (General Data Protection Regulation, EU) requires: lawful basis for data processing, data minimisation, purpose limitation, the right to access personal data, the <strong>right to erasure</strong> ("right to be forgotten"), and mandatory breach notification within 72 hours. <strong>CCPA</strong> (California Consumer Privacy Act) gives Californians similar rights. Non-compliance penalties: GDPR fines up to €20 million or 4% of global annual revenue (whichever is higher). Implementing the right to erasure in a lakehouse — where data is stored in immutable append-only Parquet files — requires specific architectural solutions like Delta Lake MERGE-based deletion with VACUUM to physically remove old files.</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.9 Cloud Security, Governance & Compliance for Big Data',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L22_9', [
                ['q' => 'The AWS Shared Responsibility Model means that customers are responsible for:', 'opts' => ['Physical security of AWS data centres', 'Security of the hypervisor and network hardware', 'IAM configuration, network security groups, encryption, application security, and data classification IN the cloud', 'The availability and durability of the underlying S3 storage service'], 'ans' => 2, 'exp' => "AWS secures the physical infrastructure (data centres, hardware, hypervisor, global network) — 'security OF the cloud'. Customers secure everything on top: who can access what (IAM), how network traffic flows (VPCs, security groups), whether data is encrypted (KMS), and how applications handle data. Most cloud breaches result from customer-side misconfiguration, not AWS infrastructure vulnerabilities."],
                ['q' => 'The Principle of Least Privilege in IAM means:', 'opts' => ['Every user gets admin access to avoid blocking their work', 'Roles and users should be granted only the minimum permissions necessary to perform their specific function — no more', 'All team members share a single IAM user for simplicity', 'Service accounts should have access to all S3 buckets by default'], 'ans' => 1, 'exp' => "Least privilege: an ETL Spark role needs only s3:GetObject on the raw/ prefix and s3:PutObject on processed/ — not s3:* on all buckets. If that role's credentials are compromised, the blast radius is limited to those specific paths. Explicit Deny statements further constrain access even if a permissive policy is accidentally attached later."],
                ['q' => 'GDPR\'s "Right to Erasure" creates a technical challenge for data lakehouses because:', 'opts' => ['GDPR prohibits storing data in Parquet format', 'Data lakes use immutable append-only object storage — deleting a specific person\'s records requires rewriting files and physically vacuuming old versions', 'Delta Lake does not support DELETE operations', 'Object storage has a minimum retention period of 10 years'], 'ans' => 1, 'exp' => 'S3 stores immutable Parquet files. To delete a person\'s data: (1) run Delta Lake DELETE WHERE customer_id = X — this creates a new version marking rows deleted, (2) run VACUUM to physically remove old Parquet files containing the deleted rows, (3) ensure backups and downstream copies are also purged. Each step must be auditable for GDPR compliance.'],
                ['q' => 'Column-level PII classification in a data catalogue serves which primary governance purpose?', 'opts' => ['Improving Parquet compression ratios for email columns', 'Enabling automatic access control enforcement, audit logging, and compliance reporting — you cannot protect data you haven\'t identified', 'Speeding up Spark queries by indexing PII columns separately', 'Automatically anonymising data before it enters the data lake'], 'ans' => 1, 'exp' => 'You cannot apply appropriate access controls, encryption, audit logging, or regulatory handling to data you have not identified as sensitive. Automated PII discovery (via pattern matching, ML classifiers, or tools like AWS Macie) enables: restricting who can see email/SSN/phone columns, logging every access to PII, enforcing data residency requirements, and demonstrating GDPR/CCPA compliance to regulators.'],
                ['q' => 'Server-side encryption with AWS KMS (SSE-KMS) provides what security guarantee over SSE-S3?', 'opts' => ['Faster encryption throughput for large files', 'Customer-managed encryption keys — you control key rotation, audit key usage, and can revoke access by disabling the key, whereas SSE-S3 uses AWS-managed keys with no customer visibility', 'The data is encrypted before leaving the client machine', 'KMS encryption works across cloud providers automatically'], 'ans' => 1, 'exp' => 'SSE-S3: AWS manages the encryption key entirely — you have no visibility or control over it. SSE-KMS: you create and manage the KMS key, control who can use it (key policy), enable automatic annual rotation, audit every decrypt operation via CloudTrail, and can immediately revoke access to all encrypted data by disabling the key. For regulated industries (HIPAA, PCI DSS), SSE-KMS is typically required.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.10 — MLOps & Deploying ML at Scale on Cloud Platforms
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>MLOps & Deploying Machine Learning at Scale on Cloud Platforms</h2>
<p><strong>MLOps</strong> (Machine Learning Operations) is the set of practices, tools, and processes that bring software engineering and DevOps discipline to the full lifecycle of machine learning: from data ingestion and feature engineering through model training, evaluation, deployment, monitoring, and retraining. The term was coined to address a painful reality: building a model in a Jupyter notebook is trivial; deploying that model to production at scale, keeping it accurate over time, managing its dependencies, debugging its failures, and rolling back bad versions — is an engineering challenge that most data science teams handle poorly.</p>

<p>The statistics are stark: <strong>87% of ML models never make it to production</strong> (Gartner). Of those that do, the average lifespan before performance degradation becomes unacceptable is 6 to 12 months without active monitoring and retraining. MLOps exists to close these gaps — to make ML model development as reliable, repeatable, and observable as software development.</p>

<h3>The ML Lifecycle: From Experiment to Production</h3>
<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MLflow: Experiment Tracking, Model Registry & Serving</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> mlflow
<span style="color:#c4b5fd;">import</span> mlflow.sklearn
<span style="color:#c4b5fd;">from</span> sklearn.ensemble <span style="color:#c4b5fd;">import</span> GradientBoostingClassifier
<span style="color:#c4b5fd;">from</span> sklearn.datasets <span style="color:#c4b5fd;">import</span> make_classification
<span style="color:#c4b5fd;">from</span> sklearn.model_selection <span style="color:#c4b5fd;">import</span> train_test_split
<span style="color:#c4b5fd;">from</span> sklearn.metrics <span style="color:#c4b5fd;">import</span> roc_auc_score, average_precision_score
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#6b7280;"># Point MLflow at a remote tracking server (or Databricks Managed MLflow)</span>
<span style="color:#93c5fd;">mlflow</span>.set_tracking_uri(<span style="color:#a7f3d0;">"https://mlflow.company.internal"</span>)
<span style="color:#93c5fd;">mlflow</span>.set_experiment(<span style="color:#a7f3d0;">"churn-prediction-v2"</span>)

<span style="color:#6b7280;"># Synthetic churn dataset</span>
<span style="color:#93c5fd;">X</span>, <span style="color:#93c5fd;">y</span> = make_classification(n_samples=<span style="color:#fcd34d;">50_000</span>, n_features=<span style="color:#fcd34d;">20</span>,
                           n_informative=<span style="color:#fcd34d;">12</span>, random_state=<span style="color:#fcd34d;">42</span>)
<span style="color:#93c5fd;">X_tr</span>, <span style="color:#93c5fd;">X_te</span>, <span style="color:#93c5fd;">y_tr</span>, <span style="color:#93c5fd;">y_te</span> = train_test_split(X, y, test_size=<span style="color:#fcd34d;">0.2</span>, stratify=y, random_state=<span style="color:#fcd34d;">42</span>)

<span style="color:#6b7280;"># Log a full experiment run with MLflow</span>
<span style="color:#c4b5fd;">with</span> mlflow.start_run(run_name=<span style="color:#a7f3d0;">"GBT-n100-lr0.05"</span>) <span style="color:#c4b5fd;">as</span> run:
    <span style="color:#93c5fd;">params</span> = {<span style="color:#a7f3d0;">"n_estimators"</span>: <span style="color:#fcd34d;">100</span>, <span style="color:#a7f3d0;">"learning_rate"</span>: <span style="color:#fcd34d;">0.05</span>,
              <span style="color:#a7f3d0;">"max_depth"</span>: <span style="color:#fcd34d;">5</span>, <span style="color:#a7f3d0;">"subsample"</span>: <span style="color:#fcd34d;">0.8</span>}
    <span style="color:#93c5fd;">mlflow</span>.log_params(params)        <span style="color:#6b7280;"># track hyperparameters</span>

    <span style="color:#93c5fd;">model</span> = GradientBoostingClassifier(**params, random_state=<span style="color:#fcd34d;">42</span>)
    <span style="color:#93c5fd;">model</span>.fit(X_tr, y_tr)

    <span style="color:#93c5fd;">probs</span>  = model.predict_proba(X_te)[:, <span style="color:#fcd34d;">1</span>]
    <span style="color:#93c5fd;">auc</span>    = roc_auc_score(y_te, probs)
    <span style="color:#93c5fd;">ap</span>     = average_precision_score(y_te, probs)

    <span style="color:#93c5fd;">mlflow</span>.log_metric(<span style="color:#a7f3d0;">"roc_auc"</span>,          auc)    <span style="color:#6b7280;"># track metrics</span>
    <span style="color:#93c5fd;">mlflow</span>.log_metric(<span style="color:#a7f3d0;">"avg_precision"</span>,    ap)
    <span style="color:#93c5fd;">mlflow</span>.sklearn.log_model(             <span style="color:#6b7280;"># store model artifact</span>
        sk_model=model,
        artifact_path=<span style="color:#a7f3d0;">"model"</span>,
        registered_model_name=<span style="color:#a7f3d0;">"churn-predictor"</span>,
    )
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Run ID   : {run.info.run_id}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"ROC-AUC  : {auc:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Avg Prec : {ap:.4f}"</span>)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Model registered in MLflow Model Registry → 'churn-predictor' v3"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Run ID   : a7f3b2c1d8e4f9012345678
ROC-AUC  : 0.9147
Avg Prec : 0.8832
Model registered in MLflow Model Registry → 'churn-predictor' v3</div>
  </div>
</div>

<h3>Model Deployment Patterns: REST APIs, Batch Scoring & Feature Stores</h3>
<p>Models reach production through three primary deployment patterns. <strong>Real-time REST API</strong>: the model is wrapped in an HTTP server (FastAPI, Flask) and deployed as a container on Kubernetes or a managed service (AWS SageMaker, GCP Vertex AI). Every prediction request is a synchronous HTTP call — critical for user-facing applications like fraud detection or personalisation. <strong>Batch scoring</strong>: the model runs on a schedule (hourly, nightly) against a full customer or product database, writing predictions to a table that downstream systems read. Cheaper and simpler than real-time, appropriate when predictions don't need to be instantaneous. <strong>Feature stores</strong> (Feast, Tecton, Databricks Feature Store) solve the "training-serving skew" problem — ensuring the features computed for model training are identical to those available at serving time, preventing production errors from feature definition mismatches.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Serving an MLflow Model via FastAPI REST Endpoint</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> fastapi <span style="color:#c4b5fd;">import</span> FastAPI
<span style="color:#c4b5fd;">from</span> pydantic <span style="color:#c4b5fd;">import</span> BaseModel
<span style="color:#c4b5fd;">import</span> mlflow.sklearn
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> <span style="color:#93c5fd;">np</span>

<span style="color:#93c5fd;">app</span>   = FastAPI(title=<span style="color:#a7f3d0;">"Churn Prediction API"</span>, version=<span style="color:#a7f3d0;">"3.0.0"</span>)
<span style="color:#93c5fd;">model</span> = mlflow.sklearn.load_model(<span style="color:#a7f3d0;">"models:/churn-predictor/Production"</span>)

<span style="color:#c4b5fd;">class</span> <span style="color:#93c5fd;">CustomerFeatures</span>(BaseModel):
    monthly_spend:      <span style="color:#93c5fd;">float</span>
    support_tickets:    <span style="color:#93c5fd;">int</span>
    days_since_login:   <span style="color:#93c5fd;">int</span>
    plan_tier:          <span style="color:#93c5fd;">str</span>    <span style="color:#6b7280;"># "free"|"pro"|"enterprise"</span>
    num_integrations:   <span style="color:#93c5fd;">int</span>

<span style="color:#c4b5fd;">class</span> <span style="color:#93c5fd;">ChurnResponse</span>(BaseModel):
    customer_id:     <span style="color:#93c5fd;">str</span>
    churn_prob:      <span style="color:#93c5fd;">float</span>
    risk_band:       <span style="color:#93c5fd;">str</span>
    recommended_action: <span style="color:#93c5fd;">str</span>

<span style="color:#93c5fd;">PLAN_ENCODING</span> = {<span style="color:#a7f3d0;">"free"</span>: <span style="color:#fcd34d;">0</span>, <span style="color:#a7f3d0;">"pro"</span>: <span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">"enterprise"</span>: <span style="color:#fcd34d;">2</span>}

<span style="color:#93c5fd;">@app.post</span>(<span style="color:#a7f3d0;">"/predict/churn"</span>, response_model=<span style="color:#93c5fd;">ChurnResponse</span>)
<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">predict_churn</span>(customer_id: <span style="color:#93c5fd;">str</span>, features: CustomerFeatures):
    <span style="color:#93c5fd;">X</span>         = np.array([[
        features.monthly_spend,
        features.support_tickets,
        features.days_since_login,
        <span style="color:#93c5fd;">PLAN_ENCODING</span>.get(features.plan_tier, <span style="color:#fcd34d;">0</span>),
        features.num_integrations,
        *np.zeros(<span style="color:#fcd34d;">15</span>),   <span style="color:#6b7280;"># remaining features padded</span>
    ]])
    <span style="color:#93c5fd;">prob</span>      = <span style="color:#93c5fd;">float</span>(model.predict_proba(X)[<span style="color:#fcd34d;">0</span>, <span style="color:#fcd34d;">1</span>])
    <span style="color:#93c5fd;">risk_band</span> = <span style="color:#a7f3d0;">"HIGH"</span> <span style="color:#c4b5fd;">if</span> prob &gt; <span style="color:#fcd34d;">0.7</span> <span style="color:#c4b5fd;">else</span> (<span style="color:#a7f3d0;">"MEDIUM"</span> <span style="color:#c4b5fd;">if</span> prob &gt; <span style="color:#fcd34d;">0.4</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">"LOW"</span>)
    <span style="color:#93c5fd;">action</span>    = {<span style="color:#a7f3d0;">"HIGH"</span>: <span style="color:#a7f3d0;">"Immediate CSM outreach + discount offer"</span>,
                 <span style="color:#a7f3d0;">"MEDIUM"</span>: <span style="color:#a7f3d0;">"Schedule health check call"</span>,
                 <span style="color:#a7f3d0;">"LOW"</span>: <span style="color:#a7f3d0;">"Send NPS survey"</span>}[risk_band]
    <span style="color:#c4b5fd;">return</span> ChurnResponse(customer_id=customer_id, churn_prob=<span style="color:#93c5fd;">round</span>(prob, <span style="color:#fcd34d;">4</span>),
                          risk_band=risk_band, recommended_action=action)

<span style="color:#6b7280;"># Example prediction output (what the API returns as JSON)</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"""{
  "customer_id": "cust_94821",
  "churn_prob": 0.8213,
  "risk_band": "HIGH",
  "recommended_action": "Immediate CSM outreach + discount offer"
}"""</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>{
  "customer_id": "cust_94821",
  "churn_prob": 0.8213,
  "risk_band": "HIGH",
  "recommended_action": "Immediate CSM outreach + discount offer"
}</div>
  </div>
</div>

<h3>Model Monitoring: Detecting Data Drift & Performance Degradation</h3>
<p>A model trained in January on January data will gradually become less accurate as the real world changes — customer behaviour shifts, products change, market conditions evolve. This is called <strong>data drift</strong> (input feature distributions change) or <strong>concept drift</strong> (the relationship between features and target changes). Without monitoring, a silently degrading model continues making predictions that are subtly wrong — often for months before anyone notices. MLOps practice requires: logging every prediction and its features, comparing recent feature distributions to training distributions (using KL divergence, Population Stability Index, or the Kolmogorov-Smirnov test), and triggering retraining automatically when drift crosses a threshold.</p>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.10 MLOps & Deploying Machine Learning at Scale on Cloud Platforms',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L22_10', [
                ['q' => 'What is the primary purpose of MLflow\'s experiment tracking?', 'opts' => ['To deploy models to production automatically', 'To record every training run\'s hyperparameters, metrics, code version, and model artifacts so experiments are reproducible and comparable', 'To monitor models for data drift after deployment', 'To orchestrate data pipeline DAGs in Airflow'], 'ans' => 1, 'exp' => "MLflow Tracking solves the 'which run was that?' problem. Without it, data scientists try different hyperparameters and lose track of which combination produced which metric. MLflow logs params (n_estimators=100), metrics (auc=0.914), artifacts (the model file), and the code version — enabling reproducibility, comparison across runs, and auditable model lineage."],
                ['q' => 'The "training-serving skew" problem in ML refers to:', 'opts' => ['Models being slower in production than on a GPU training cluster', 'Feature values computed differently at training time versus serving time, causing predictions to degrade despite no model changes', 'The model overfitting to training data', 'Higher latency when serving predictions to multiple users simultaneously'], 'ans' => 1, 'exp' => "Training: average_order_value computed as SUM/COUNT over all time. Serving: computed as SUM/COUNT over last 30 days only. Same feature name, different definition → serving skew. Feature Stores (Feast, Tecton) solve this by centralising feature computation logic, ensuring the same code path runs at training and serving time, guaranteeing identical values."],
                ['q' => 'Why is batch scoring appropriate for some use cases but not others?', 'opts' => ['Batch scoring is always inferior to real-time scoring', 'Batch scoring runs periodically and writes predictions to a table — appropriate when predictions do not need to be instantaneous (e.g., overnight churn scores); real-time scoring required when decisions must happen in milliseconds (e.g., fraud detection)', 'Batch scoring only works with tree-based models', 'Batch scoring is only possible on Databricks'], 'ans' => 1, 'exp' => 'Batch: run nightly churn model on all 500K customers, write churn scores to a table, CSM team checks the table each morning. Real-time: every credit card transaction must be scored for fraud in <200ms before the purchase is approved. The decision latency requirement determines the architecture — batch is cheaper and simpler; real-time is complex and expensive but necessary when decisions are time-sensitive.'],
                ['q' => 'Data drift in a deployed ML model refers to:', 'opts' => ['The model\'s weights gradually changing due to continuous online learning', 'Changes in the statistical distribution of input features in production relative to the training dataset, causing the model\'s accuracy to silently degrade over time', 'The model file becoming corrupted in S3 after several months', 'Prediction latency increasing as the model processes more requests'], 'ans' => 1, 'exp' => "Data drift: January training data showed 'days_since_login' averaging 3 days. By December, users log in less frequently — average is now 12 days. The model has never seen this distribution and makes poor predictions. Concept drift is even more subtle: the same feature values now mean different things (e.g., 'high support tickets' used to mean at-risk; now it means engaged power users). Both require active monitoring to detect."],
                ['q' => 'MLflow\'s Model Registry with "Production" stage solves which deployment challenge?', 'opts' => ['It automatically retrains models when performance drops', 'It provides a central versioned catalogue of trained models with lifecycle stages (Staging, Production, Archived) enabling controlled promotion, rollback, and auditability', 'It handles A/B testing traffic splitting between model versions', 'It monitors prediction latency and auto-scales serving infrastructure'], 'ans' => 1, 'exp' => "Without a model registry, teams deploy models by copying files to servers — no version control, no audit trail, difficult rollback. MLflow Registry: each model has numbered versions (v1, v2, v3). You promote v3 to 'Production' stage. Your serving code loads models:/churn-predictor/Production — automatically picking up the current production version. If v3 performs badly, promote v2 back to Production. Full audit trail of who promoted what when."],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 22.11 — Final Exam: Big Data & Cloud Computing
        // ══════════════════════════════════════════════════════════════

        $allFinalQuestions = [
            // 22.1 Big Data Fundamentals
            ['q' => 'Which of the 5 Vs describes the trustworthiness and accuracy of data?', 'opts' => ['Volume', 'Velocity', 'Variety', 'Veracity'], 'ans' => 3, 'exp' => 'Veracity addresses data quality — how accurate, complete, and trustworthy the data is. IoT sensors drift, user input contains errors, and logs have gaps. "Garbage in, garbage out" — poor veracity leads to analytically wrong conclusions even with correct pipelines.'],
            ['q' => 'In the Modern Data Stack, what does the "Ingestion" layer primarily do?', 'opts' => ['Transforms raw data into analytics-ready models', 'Moves data from source systems (databases, SaaS, APIs) into storage (data lake or warehouse)', 'Serves data to BI tools and dashboards', 'Orchestrates pipeline scheduling and retries'], 'ans' => 1, 'exp' => 'The Ingestion layer (tools: Fivetran, Airbyte, Kafka Connect) extracts data from source systems and loads it into the data lake or warehouse. It is responsible for connectivity, scheduling, and schema mapping — not transformation. Transformation happens in the next layer (dbt, Spark).'],
            // 22.2 Cloud Computing
            ['q' => 'AWS EC2 Spot Instances offer large discounts compared to On-Demand because:', 'opts' => ['They run on older hardware from previous generations', 'They can be reclaimed by AWS with 2 minutes notice when spare capacity is needed, making them interruptible but ideal for fault-tolerant batch workloads', 'They require a 3-year upfront payment commitment', 'They are limited to specific AWS regions only'], 'ans' => 1, 'exp' => 'Spot Instances use excess EC2 capacity that AWS might need back at any time. With 2-minute interruption notice, they are not suitable for stateful or time-sensitive workloads — but perfect for Spark batch jobs (checkpointable), ML training (restartable), and rendering (resumable), yielding up to 90% cost savings.'],
            ['q' => 'Infrastructure as Code (IaC) using Terraform solves which core operational problem?', 'opts' => ['Slow query performance on large tables', 'Manually configured "snowflake servers" that are unique, fragile, irreproducible, and impossible to version-control', 'High Kafka consumer lag during peak periods', 'HDFS NameNode single point of failure'], 'ans' => 1, 'exp' => 'IaC defines infrastructure in code (Terraform HCL), committed to Git, reviewed via PRs, and applied automatically. This eliminates snowflake servers — manually configured machines that cannot be reproduced after failure, differ subtly between environments, and cannot be audited. IaC enables repeatable, consistent, version-controlled infrastructure.'],
            // 22.3 Distributed Storage
            ['q' => 'Columnar storage formats like Parquet outperform CSV for analytical workloads because:', 'opts' => ['Parquet supports more data types than CSV', 'Analytical queries read only a few columns — columnar storage avoids reading irrelevant column data, and homogeneous column data compresses far better than mixed-type rows', 'Parquet uses stronger encryption than CSV', 'Parquet files are never split across multiple nodes'], 'ans' => 1, 'exp' => 'SELECT AVG(price) FROM sales needs only the "price" column. CSV: read all fields of all 1 billion rows. Parquet: read only the contiguous "price" column bytes. On a 100-column table, this is a ~100× I/O reduction. Additionally, a column of float64 values compresses much better than rows interleaving strings, ints, and floats.'],
            ['q' => 'The HDFS NameNode stores which critical information?', 'opts' => ['The actual data blocks for all files', 'All filesystem metadata: the namespace tree, file-to-block mappings, and block-to-DataNode locations — held in RAM for fast access', 'The Spark execution plans for running jobs', 'The YARN scheduler queue for pending jobs'], 'ans' => 1, 'exp' => "The NameNode is the HDFS master that keeps all metadata in RAM: which files exist, which blocks each file consists of, and which DataNodes hold each block replica. DataNodes store the actual binary block data. NameNode failure was historically HDFS's single point of failure — modern deployments use HA NameNode with a hot standby."],
            // 22.4 Spark
            ['q' => 'In Spark, a "transformation" like .filter() or .groupBy() is "lazy" meaning:', 'opts' => ['It runs slowly due to disk I/O overhead', 'It builds a logical plan but does not execute anything until an action (show, count, write) is called', 'It processes only the first partition of the DataFrame', 'It requires explicit caching before it can be used'], 'ans' => 1, 'exp' => 'Lazy evaluation means transformations build a computation DAG but execute nothing. Only actions trigger execution. This allows the Catalyst optimiser to see the full computation graph, apply predicate pushdown and projection pruning, reorder operations, and produce an optimised physical plan — often dramatically different from what the programmer wrote.'],
            ['q' => 'Hive-style partitioning (partitionBy("year", "month")) when writing Parquet benefits future queries by:', 'opts' => ['Sorting rows within each file alphabetically', 'Enabling partition pruning — queries filtered on "year" or "month" scan only matching folders, skipping all others entirely', 'Automatically compressing each partition with Snappy', 'Enabling Spark to use GPU acceleration for date comparisons'], 'ans' => 1, 'exp' => "With partitionBy('year','month'), data is in folders like year=2024/month=03/. Query: WHERE year=2024 AND month=03 → Spark reads ONLY year=2024/month=03/ and skips all other partition folders. If data spans 5 years × 12 months = 60 partitions, a 1-month query scans 1/60 of the total data — 98.3% I/O reduction for free."],
            // 22.5 Kafka
            ['q' => 'Kafka consumer lag measures:', 'opts' => ['CPU usage of the consumer process', 'Network latency between broker and consumer', 'The number of unprocessed messages in each partition (log-end-offset minus current-offset)', 'The time to deserialise a single message'], 'ans' => 2, 'exp' => 'Lag = LOG-END-OFFSET - CURRENT-OFFSET per partition. Zero lag means the consumer is caught up with the producer. Growing lag means the consumer is falling behind. Monitoring lag is critical — a fraud detection consumer with 10-minute lag means fraud transactions are being approved for 10 minutes before the alert fires.'],
            ['q' => 'Setting a message key in Kafka (e.g., customer_id) guarantees:', 'opts' => ['The message is encrypted with the customer\'s public key', 'All messages with the same key are routed to the same partition, preserving ordering for that key across producers and consumers', 'The message is deduplicated automatically on the broker', 'The consumer group receives the message exactly once'], 'ans' => 1, 'exp' => 'Kafka guarantees ordering WITHIN a partition. By using customer_id as the key, all transactions for customer 12345 go to the same partition in the same order they were produced. The consumer sees each customer\'s event stream in correct chronological order — essential for balance calculations, fraud detection, and event sourcing patterns.'],
            // 22.6 Data Warehouses
            ['q' => 'Snowflake\'s AUTO_SUSPEND = 60 configuration on a Virtual Warehouse means:', 'opts' => ['The warehouse crashes after 60 failed queries', 'The compute cluster automatically suspends (stops billing) after 60 seconds of idle time, resuming on the next query', 'Queries are automatically cancelled after 60 seconds', 'The warehouse is available for 60 minutes after each startup'], 'ans' => 1, 'exp' => 'Snowflake charges per second of Virtual Warehouse compute. AUTO_SUSPEND=60 means: when no queries are running for 60 seconds, the VW shuts down — you stop paying compute costs immediately. AUTO_RESUME=TRUE means: the next query automatically starts the VW within 1-2 seconds. This makes Snowflake extremely cost-efficient for intermittent workloads.'],
            ['q' => 'BigQuery\'s dry_run mode is used to:', 'opts' => ['Test if SQL syntax is valid without executing on real data', 'Estimate the bytes to be scanned and therefore the cost of a query before executing it', 'Run the query in isolation without affecting other concurrent queries', 'Cache query results for 24 hours without charging for compute'], 'ans' => 1, 'exp' => "BigQuery charges $5/TB scanned (on-demand). dry_run=True returns total_bytes_processed immediately without executing the query or incurring any cost. Engineers use this before running exploratory queries on large tables to ensure partition pruning is working correctly and to avoid accidentally scanning petabytes."],
            // 22.7 Pipelines & Airflow
            ['q' => 'In an Airflow DAG, what does the >> operator between two tasks define?', 'opts' => ['The task with higher priority', 'A dependency: the left task must complete successfully before the right task is allowed to start', 'Parallel execution of both tasks simultaneously', 'The retry count for the right-hand task'], 'ans' => 1, 'exp' => 'task_a >> task_b means: task_b depends on task_a — Airflow will not schedule task_b until task_a has completed with status SUCCESS. You can chain: a >> b >> c (sequential). Parallel branches: a >> [b, c] >> d (b and c run in parallel after a, d waits for both). This forms the DAG execution graph.'],
            ['q' => 'The ELT pattern is preferred over ETL in modern cloud data stacks because:', 'opts' => ['ELT is always faster for all workload types', 'Transforming data before loading (ETL) prevents raw data preservation; ELT loads raw data first (preserving it for reprocessing) then transforms in-warehouse using SQL, which is cheaper and more auditable', 'ELT does not require a data warehouse', 'ETL was deprecated in 2020 and is no longer supported'], 'ans' => 1, 'exp' => 'ETL: transform data in an intermediate layer before loading — expensive, loses raw data. ELT: load raw data immediately into the warehouse/lake, then transform using the warehouse SQL engine (via dbt). Benefits: raw data preserved for reprocessing when logic changes; SQL transformations are version-controlled and reviewable; warehouse compute is now cheap enough for in-place transformation.'],
            // 22.8 Data Lakes
            ['q' => 'Delta Lake\'s MERGE operation provides what key capability over plain Parquet on S3?', 'opts' => ['Faster file compression', 'Atomic upsert — updating existing records matching a condition and inserting new records in a single transaction, with full ACID guarantees', 'Automatic partition optimisation', 'Cross-region data replication'], 'ans' => 1, 'exp' => 'Plain Parquet: no UPDATE or DELETE — you must rewrite entire partition files. Delta MERGE: atomically updates matching rows (WHEN MATCHED UPDATE) and inserts non-matching rows (WHEN NOT MATCHED INSERT) in a single transaction visible instantaneously to readers. Essential for CDC patterns, SCD Type 2, and GDPR right-to-erasure compliance.'],
            ['q' => 'In the Medallion Architecture, the "Bronze" layer is best described as:', 'opts' => ['Cleaned, validated, and deduplicated production-ready data', 'Business-level aggregates and star-schema models for BI consumption', 'Exact raw copies of source data, never modified, retained forever for reprocessing', 'Compressed summary statistics computed from all historical data'], 'ans' => 2, 'exp' => 'Bronze = raw ingestion zone. Data lands here exactly as received from the source — no modifications, no quality filters. It is the archive layer: if downstream transformation logic was wrong, you reprocess from Bronze. Silver = cleaned and validated. Gold = business aggregates. Bronze data is never deleted because it is the single source of truth for reprocessing.'],
            // 22.9 Security & Governance
            ['q' => 'The AWS Shared Responsibility Model assigns to the customer the responsibility for:', 'opts' => ['Physical data centre security', 'Global network infrastructure', 'IAM policies, security groups, encryption, and data classification', 'Hypervisor patching and hardware maintenance'], 'ans' => 2, 'exp' => "AWS secures: physical infrastructure, hardware, global network, hypervisor ('security OF the cloud'). Customer secures: IAM (who can access what), VPC/security groups (network traffic), KMS encryption (data at rest), TLS (data in transit), application security, data classification ('security IN the cloud'). Most cloud breaches exploit customer-side misconfiguration."],
            ['q' => 'GDPR\'s "Right to Erasure" creates technical complexity in data lakehouses because:', 'opts' => ['GDPR prohibits storing data longer than 30 days', 'Object storage holds immutable Parquet files — physically deleting a person\'s data requires Delta MERGE-based DELETE, followed by VACUUM to physically purge old Parquet files from S3', 'Parquet does not support individual row deletion', 'GDPR requires storing all deleted data for audit purposes'], 'ans' => 1, 'exp' => "Implementing right-to-erasure in a lakehouse: (1) Delta DELETE WHERE customer_id = X — creates a new version marking rows deleted, old Parquet files still exist in S3, (2) VACUUM after the retention period physically deletes old Parquet files that contained deleted rows, (3) downstream copies (BI caches, ML training sets) must also be purged. Each step requires documentation for regulatory audits."],
            // 22.10 MLOps
            ['q' => 'MLflow\'s Model Registry with lifecycle stages (Staging, Production, Archived) solves:', 'opts' => ['Automatic hyperparameter tuning for registered models', 'Controlled model promotion and rollback — a versioned catalogue enabling traceability of which model version is in production, when it was promoted, and by whom', 'Automatic retraining when performance drops below a threshold', 'Feature store synchronisation between training and serving environments'], 'ans' => 1, 'exp' => 'Without a registry: models are files copied to servers — no version history, no audit trail, rollback requires manual intervention. With a registry: model versions are numbered, promoted through stages (Staging→Production), transitions are logged with user and timestamp. Serving code references models:/churn-predictor/Production — automatically serving the current production version without code changes.'],
            ['q' => 'Data drift in a deployed ML model occurs when:', 'opts' => ['The model\'s weights update incorrectly due to a bug in the gradient computation', 'The statistical distribution of input features in production shifts away from the training distribution, causing prediction accuracy to silently degrade', 'The model file becomes corrupted during S3 transfer', 'Serving latency increases beyond the SLA threshold'], 'ans' => 1, 'exp' => 'Drift example: churn model trained when users logged in every 3 days on average. Post-pandemic, usage patterns changed — average is now 15 days. The "days_since_login" feature now has a completely different distribution. The model predicts high churn for power users (who now login every 15 days). Monitoring tools (Evidently, Whylogs) detect this via statistical tests (PSI, KS test) and trigger retraining.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 22: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 22.1 through 22.10 — the 5 Vs of Big Data, cloud computing fundamentals, distributed storage, Apache Spark, Apache Kafka, cloud data warehouses, data pipelines and Airflow, Delta Lake, cloud security and governance, and MLOps. Good luck!</p>
HTML;

        $finalContent .= $this->appendQuiz('', 'FINAL_EXAM', $allFinalQuestions);
        $finalContent .= '</div>';
        $finalContent .= <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.USER_ORG_ID !== 'undefined' && window.USER_ORG_ID !== null && window.USER_ORG_ID !== '') {
        document.getElementById('org-lock-screen').style.display = 'none';
        document.getElementById('final-exam-content').style.display = 'block';
    }
});
</script>
HTML;

        Lesson::create([
            'module_id'   => $bigDataModule->id,
            'title'       => '22.11 Final Exam: Big Data & Cloud Computing Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

    /**
     * Generates the full Quiz HTML/CSS/JS block and appends it to $htmlContent.
     */
    private function appendQuiz(string $htmlContent, string $quizPrefix, array $questions): string
    {
        $total   = count($questions);
        $letters = ['A', 'B', 'C', 'D', 'E'];

        $html  = $htmlContent;
        $html .= '<style>
            .quiz-wrapper{display:flex;flex-direction:column;gap:24px;margin-top:40px;}
            .quiz-card{background:var(--surface2);border:1px solid var(--border);border-radius:10px;overflow:hidden;}
            .quiz-card-header{background:rgba(0,0,0,0.2);padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;gap:12px;}
            .quiz-q-num{background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:3px 8px;border-radius:4px;font-family:"JetBrains Mono",monospace;white-space:nowrap;margin-top:2px;}
            .quiz-q-text{font-size:0.95rem;font-weight:600;color:var(--text);line-height:1.5;}
            .quiz-options{padding:16px 20px;display:flex;flex-direction:column;gap:10px;}
            .quiz-option{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-radius:7px;border:1px solid var(--border);cursor:pointer;transition:all 0.15s;font-size:0.875rem;color:var(--muted);background:transparent;text-align:left;width:100%;font-family:"Inter",sans-serif;}
            .quiz-option:hover:not(.locked){border-color:var(--border-hover);background:var(--bg);color:var(--text);}
            .quiz-option .opt-key{width:22px;height:22px;border-radius:4px;border:1px solid var(--dim);font-size:0.7rem;font-weight:700;font-family:"JetBrains Mono",monospace;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;transition:all 0.15s;}
            .quiz-option.correct{border-color:#10b981;background:rgba(16,185,129,0.08);color:var(--text);}
            .quiz-option.correct .opt-key{background:#10b981;border-color:#10b981;color:#fff;}
            .quiz-option.wrong{border-color:#ef4444;background:rgba(239,68,68,0.08);color:var(--muted);opacity:0.7;}
            .quiz-option.locked{cursor:default;}
            .quiz-explanation{display:none;margin:0 20px 20px;padding:14px 16px;background:rgba(59,130,246,0.07);border:1px solid rgba(59,130,246,0.25);border-radius:7px;font-size:0.875rem;color:var(--muted);line-height:1.7;}
            .quiz-explanation strong{color:var(--text);}
            .quiz-score-bar{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:var(--surface2);border:1px solid var(--border);border-radius:10px;font-size:0.875rem;color:var(--muted);font-weight:600;}
            .quiz-score-val{font-size:1.1rem;font-weight:700;color:#f59e0b;font-family:"JetBrains Mono",monospace;}
        </style>';

        $html .= '<div class="quiz-wrapper" id="wrap_' . $quizPrefix . '">';
        $html .= '<div class="quiz-score-bar"><span>Knowledge Check</span><span class="quiz-score-val"><span id="score_' . $quizPrefix . '">0</span> / ' . $total . '</span></div>';

        foreach ($questions as $qIndex => $q) {
            $qNum = $qIndex + 1;
            $qId  = $quizPrefix . '_q' . $qNum;

            $html .= '<div class="quiz-card" id="' . $qId . '">';
            $html .= '<div class="quiz-card-header"><span class="quiz-q-num">Q' . $qNum . '</span><span class="quiz-q-text">' . htmlspecialchars($q['q']) . '</span></div>';
            $html .= '<div class="quiz-options">';

            foreach ($q['opts'] as $optIndex => $option) {
                $isCorrect = ($optIndex === $q['ans']) ? 'true' : 'false';
                $letter    = $letters[$optIndex];
                $html .= '<button class="quiz-option" onclick="checkAnswer(this,\'' . $qId . '\',' . $isCorrect . ',\'' . $quizPrefix . '\')"><span class="opt-key">' . $letter . '</span> ' . htmlspecialchars($option) . '</button>';
            }

            $html .= '</div>';
            $html .= '<div class="quiz-explanation" id="' . $qId . '-exp"><strong>Explanation:</strong> ' . $q['exp'] . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= "
<script>
if(typeof window.answeredQuizzes==='undefined'){window.answeredQuizzes={};}
if(typeof window.quizScores==='undefined'){window.quizScores={};}
window.checkAnswer=function(btn,qId,isCorrect,prefix){
    if(window.answeredQuizzes[qId])return;
    window.answeredQuizzes[qId]=true;
    if(typeof window.quizScores[prefix]==='undefined')window.quizScores[prefix]=0;
    const card=document.getElementById(qId);
    const allOpts=card.querySelectorAll('.quiz-option');
    allOpts.forEach(o=>o.classList.add('locked'));
    if(isCorrect){
        btn.classList.add('correct');
        window.quizScores[prefix]++;
    } else {
        btn.classList.add('wrong');
        allOpts.forEach(o=>{if(o.getAttribute('onclick').includes(',true,'))o.classList.add('correct');});
    }
    document.getElementById(qId+'-exp').style.display='block';
    document.getElementById('score_'+prefix).textContent=window.quizScores[prefix];
};
</script>
";

        return $html;
    }
}