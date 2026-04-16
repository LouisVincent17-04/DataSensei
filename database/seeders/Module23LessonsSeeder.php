<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module23LessonsSeeder
 * Seeds lessons for Module 23: Data Warehousing.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module23LessonsSeeder
 */
class Module23LessonsSeeder extends Seeder
{
    public function run()
    {
        $module = Module::where('order_index', 23)->firstOrFail();
        Lesson::where('module_id', $module->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.1 — What Is a Data Warehouse?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is a Data Warehouse?</h2>
<p>A <strong>data warehouse</strong> is a centralised, integrated repository of structured data specifically designed for analytical queries, business intelligence (BI), and decision support — not for day-to-day transactional operations. Where an operational database (OLTP system) is optimised to process thousands of small, fast read-write transactions per second, a data warehouse is optimised to scan millions or billions of rows rapidly, aggregate them, and return answers to complex analytical questions. The two systems serve fundamentally different masters: OLTP serves the application; the warehouse serves the analyst and the executive.</p>

<p>The concept was formally articulated by Bill Inmon in 1990, who defined a data warehouse as <em>subject-oriented, integrated, time-variant, and non-volatile</em>. These four properties remain the canonical definition today, and understanding them precisely is the foundation of everything else in this module.</p>

<h3>The Four Core Properties (Inmon)</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Inmon's Four Properties</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">SUBJECT-ORIENTED</span>
  Organised around major business subjects, not applications.
  OLTP:  tables for Orders, Invoices, Shipments, Returns (processes).
  DW:    subjects are Sales, Customers, Products, Inventory (entities).
  Why:   analysts think in business terms, not application terms.

<span style="color:#93c5fd;">INTEGRATED</span>
  Data from disparate source systems is cleaned, standardised,
  and unified before entering the warehouse.
  Example: Source A uses "M/F", Source B uses "Male/Female",
           Source C uses 1/0 — warehouse stores a single standard.
  This integration IS the hard work of data engineering.

<span style="color:#a7f3d0;">TIME-VARIANT</span>
  Every record carries a time dimension — data represents
  a point-in-time snapshot, not just the current state.
  OLTP:  customer address is the CURRENT address (overwritten).
  DW:    every address change is recorded with effective dates.
  This enables historical trend analysis across years or decades.

<span style="color:#fcd34d;">NON-VOLATILE</span>
  Data is loaded in bulk, then READ ONLY.
  No individual row updates or deletes happen after loading.
  OLTP:  UPDATE, DELETE are constant (e.g., cancelling an order).
  DW:    INSERT new data periodically; old data never changes.
  This stability is what makes long-running analytical queries safe.</div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Key Insight</span>
A data warehouse is not a faster database. It is a different
KIND of database — designed for a different workload.
Using one as the other is like using a racing car as a truck.</div>
  </div>
</div>

<h3>OLTP vs OLAP: A Side-by-Side Comparison</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — OLTP vs OLAP</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Property          OLTP (Operational)         OLAP (Analytical / DW)</span>
──────────────────────────────────────────────────────────────────────
Purpose           Run the business            Analyse the business
Query type        Simple, predefined          Complex, ad hoc
Data scope        Current state               Historical (years)
Row count/query   1 – 100                     Millions – billions
Transactions/sec  Thousands                   Low (batch / scheduled)
Schema            Highly normalised (3NF)     Denormalised (star/snowflake)
Users             Clerks, applications        Analysts, executives
Update pattern    Frequent inserts/updates    Bulk loads (ETL), read-only
Storage optimise  Row-oriented                Column-oriented
Index strategy    B-tree on primary keys      Bitmap, columnar indexes
Example systems   MySQL, PostgreSQL, Oracle   Snowflake, Redshift, BigQuery</div>
  </div>
</div>

<h3>The Modern Data Warehouse Ecosystem</h3>
<p>The data warehouse sits at the centre of a broader ecosystem. Source systems (CRMs, ERPs, web logs, IoT sensors) feed data through an <strong>ETL/ELT pipeline</strong> into the warehouse. From there, BI tools (Tableau, Power BI, Looker) and data science notebooks (Jupyter, Databricks) query the warehouse to produce dashboards, reports, and machine learning features. Understanding the warehouse means understanding its role in this complete data flow — not just the storage layer in isolation.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">ARCHITECTURE — The Modern Data Stack</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">Sources           Ingestion          Storage           Consumption</span>
──────────────────────────────────────────────────────────────────────
CRM (Salesforce)  ──┐                ┌──────────────┐  Tableau / Looker
ERP (SAP)         ──┤                │              │  Power BI
Web analytics     ──┼──► ETL/ELT ──►│  DATA        │  Jupyter Notebooks
IoT sensors       ──┤  (Airbyte,    │  WAREHOUSE   │  dbt transformations
Financial systems ──┤   Fivetran,   │  (Snowflake, │  ML feature stores
Mobile app logs   ──┘   Spark)      │  Redshift,   │  Embedded analytics
                                    │  BigQuery)   │
                                    └──────────────┘
                                           │
                                    ┌──────┘
                                    │ Data Marts
                                    │ (subject-specific subsets)
                                    └─► Sales DM, Finance DM, HR DM</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.1 What Is a Data Warehouse?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L23_1', [
                ['q' => 'Which of Inmon\'s four properties explains why historical trend analysis is possible in a data warehouse?', 'opts' => ['Subject-oriented — data is organised around business entities', 'Non-volatile — data is never deleted once loaded', 'Time-variant — every record carries a time dimension capturing point-in-time snapshots', 'Integrated — data from all sources is unified'], 'ans' => 2, 'exp' => 'Time-variance means every data record is associated with a specific point in time. Unlike OLTP systems that overwrite current state, a warehouse retains all historical states. This enables queries like "what was our customer count on January 1st three years ago" — impossible in a purely transactional system.'],
                ['q' => 'An OLTP system stores a customer\'s current address. A data warehouse stores all address changes with effective dates. Which property does this illustrate?', 'opts' => ['Non-volatile', 'Integrated', 'Subject-oriented', 'Time-variant'], 'ans' => 3, 'exp' => 'Storing address changes with effective dates is time-variance in practice. The warehouse captures the full history of changes, not just the current state. This allows analysts to know where a customer lived when they made a purchase two years ago — critical for geographic sales analysis.'],
                ['q' => 'Why is a data warehouse described as "non-volatile"?', 'opts' => ['Its storage media cannot be corrupted by power failures', 'Data is loaded in bulk and then only read — individual rows are never updated or deleted after loading', 'The schema cannot be changed after the warehouse is created', 'Query results are cached and never recomputed'], 'ans' => 1, 'exp' => 'Non-volatile means once data is written to the warehouse, it stays as-is. There are no individual UPDATE or DELETE operations on historical records. New data is periodically added via bulk ETL loads. This stability makes long-running analytical queries safe — the data does not shift while you are querying it.'],
                ['q' => 'A company\'s CRM uses "M/F" for gender, its HR system uses "Male/Female", and its loyalty app uses 1/0. After ETL, the warehouse uses a single standard. Which property does this implement?', 'opts' => ['Time-variant', 'Non-volatile', 'Integrated', 'Subject-oriented'], 'ans' => 2, 'exp' => 'Integration is the process of standardising, reconciling, and unifying data from disparate source systems before it enters the warehouse. Inconsistent codes, formats, units, and naming conventions are resolved so that analysts query a single, consistent version of the truth — not a patchwork of source-system idiosyncrasies.'],
                ['q' => 'Which characteristic most distinguishes an OLAP workload from an OLTP workload?', 'opts' => ['OLAP queries are simpler and run faster than OLTP queries', 'OLAP queries scan millions to billions of rows aggregating historical data; OLTP queries touch a handful of rows for current state operations', 'OLAP systems require more frequent writes than OLTP systems', 'OLAP systems use highly normalised schemas while OLTP uses denormalised ones'], 'ans' => 1, 'exp' => 'OLAP (Online Analytical Processing) queries are complex aggregations over large historical datasets — e.g., "total revenue by region and product for the last 36 months." They scan millions of rows. OLTP (Online Transaction Processing) queries touch a few specific rows — e.g., "fetch this customer\'s current order." The workload difference drives completely different design decisions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.2 — Dimensional Modelling: Star and Snowflake Schemas
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Dimensional Modelling: Star and Snowflake Schemas</h2>
<p><strong>Dimensional modelling</strong> is the dominant design methodology for data warehouses, developed by Ralph Kimball in the 1990s. It organises data into two types of tables: <strong>fact tables</strong> that store measurable business events (sales, clicks, payments, shipments) and <strong>dimension tables</strong> that describe the context of those events (who, what, when, where, why). The resulting structure — typically a star or snowflake shape — is highly readable to business analysts, performs well on analytical queries, and maps naturally to how people think about business metrics.</p>

<h3>Fact Tables: The Measurements</h3>
<p>A fact table sits at the centre of the schema. Each row represents one business event or measurement. Fact tables are typically <em>long and narrow</em>: millions to billions of rows but relatively few columns. Most columns are either foreign keys to dimension tables or numeric measures (amounts, counts, quantities, durations).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Creating a Star Schema for Retail Sales</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ── DIMENSION TABLES (the "points" of the star) ──────────────</span>

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_date (
    date_key        <span style="color:#a7f3d0;">INT PRIMARY KEY</span>,   <span style="color:#6b7280;">-- surrogate key: 20240115</span>
    full_date       <span style="color:#a7f3d0;">DATE</span>,
    day_of_week     <span style="color:#a7f3d0;">VARCHAR(10)</span>,       <span style="color:#6b7280;">-- 'Monday'</span>
    day_of_month    <span style="color:#a7f3d0;">TINYINT</span>,
    week_of_year    <span style="color:#a7f3d0;">TINYINT</span>,
    month_name      <span style="color:#a7f3d0;">VARCHAR(10)</span>,       <span style="color:#6b7280;">-- 'January'</span>
    quarter         <span style="color:#a7f3d0;">CHAR(2)</span>,           <span style="color:#6b7280;">-- 'Q1'</span>
    year            <span style="color:#a7f3d0;">SMALLINT</span>,
    is_weekend      <span style="color:#a7f3d0;">BOOLEAN</span>,
    is_holiday      <span style="color:#a7f3d0;">BOOLEAN</span>,
    fiscal_period   <span style="color:#a7f3d0;">VARCHAR(10)</span>
);

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_customer (
    customer_key    <span style="color:#a7f3d0;">INT PRIMARY KEY</span>,   <span style="color:#6b7280;">-- surrogate key</span>
    customer_id     <span style="color:#a7f3d0;">VARCHAR(20)</span>,       <span style="color:#6b7280;">-- natural/business key</span>
    first_name      <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    last_name       <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    email           <span style="color:#a7f3d0;">VARCHAR(100)</span>,
    city            <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    state           <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    country         <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    age_band        <span style="color:#a7f3d0;">VARCHAR(20)</span>,       <span style="color:#6b7280;">-- '25-34', '35-44' etc.</span>
    loyalty_tier    <span style="color:#a7f3d0;">VARCHAR(20)</span>        <span style="color:#6b7280;">-- 'Bronze','Silver','Gold'</span>
);

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_product (
    product_key     <span style="color:#a7f3d0;">INT PRIMARY KEY</span>,
    product_id      <span style="color:#a7f3d0;">VARCHAR(20)</span>,
    product_name    <span style="color:#a7f3d0;">VARCHAR(100)</span>,
    category        <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    subcategory     <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    brand           <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    unit_cost       <span style="color:#a7f3d0;">DECIMAL(10,2)</span>,
    supplier        <span style="color:#a7f3d0;">VARCHAR(100)</span>
);

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_store (
    store_key       <span style="color:#a7f3d0;">INT PRIMARY KEY</span>,
    store_id        <span style="color:#a7f3d0;">VARCHAR(20)</span>,
    store_name      <span style="color:#a7f3d0;">VARCHAR(100)</span>,
    region          <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    country         <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    store_type      <span style="color:#a7f3d0;">VARCHAR(30)</span>,       <span style="color:#6b7280;">-- 'Flagship','Outlet','Online'</span>
    sq_footage      <span style="color:#a7f3d0;">INT</span>
);

<span style="color:#6b7280;">-- ── FACT TABLE (the centre of the star) ──────────────────────</span>

<span style="color:#c4b5fd;">CREATE TABLE</span> fact_sales (
    sale_key        <span style="color:#a7f3d0;">BIGINT PRIMARY KEY</span>,
    date_key        <span style="color:#a7f3d0;">INT REFERENCES</span> dim_date(date_key),
    customer_key    <span style="color:#a7f3d0;">INT REFERENCES</span> dim_customer(customer_key),
    product_key     <span style="color:#a7f3d0;">INT REFERENCES</span> dim_product(product_key),
    store_key       <span style="color:#a7f3d0;">INT REFERENCES</span> dim_store(store_key),
    <span style="color:#6b7280;">-- Measures (additive facts):</span>
    quantity_sold   <span style="color:#a7f3d0;">INT</span>,
    unit_price      <span style="color:#a7f3d0;">DECIMAL(10,2)</span>,
    discount_amount <span style="color:#a7f3d0;">DECIMAL(10,2)</span>,
    net_revenue     <span style="color:#a7f3d0;">DECIMAL(12,2)</span>,     <span style="color:#6b7280;">-- = quantity * (price - discount)</span>
    cost_of_goods   <span style="color:#a7f3d0;">DECIMAL(12,2)</span>,
    gross_profit    <span style="color:#a7f3d0;">DECIMAL(12,2)</span>      <span style="color:#6b7280;">-- = net_revenue - cost_of_goods</span>
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Star Schema Shape</span>
         dim_date
             |
dim_store ──fact_sales── dim_customer
             |
         dim_product

The fact table is always the centre. Each dimension
connects to it directly — like points on a star.</div>
  </div>
</div>

<h3>Querying the Star Schema</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Analytical Queries on the Star Schema</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Q1: Monthly revenue by product category (last 12 months)</span>
<span style="color:#c4b5fd;">SELECT</span>
    d.year,
    d.month_name,
    p.category,
    <span style="color:#93c5fd;">SUM</span>(f.net_revenue)     <span style="color:#c4b5fd;">AS</span> total_revenue,
    <span style="color:#93c5fd;">SUM</span>(f.quantity_sold)   <span style="color:#c4b5fd;">AS</span> units_sold,
    <span style="color:#93c5fd;">AVG</span>(f.gross_profit)    <span style="color:#c4b5fd;">AS</span> avg_profit_per_sale
<span style="color:#c4b5fd;">FROM</span>   fact_sales f
<span style="color:#c4b5fd;">JOIN</span>   dim_date     d <span style="color:#c4b5fd;">ON</span> f.date_key    = d.date_key
<span style="color:#c4b5fd;">JOIN</span>   dim_product  p <span style="color:#c4b5fd;">ON</span> f.product_key = p.product_key
<span style="color:#c4b5fd;">WHERE</span>  d.full_date >= <span style="color:#93c5fd;">CURRENT_DATE</span> - <span style="color:#a7f3d0;">INTERVAL '12 months'</span>
<span style="color:#c4b5fd;">GROUP BY</span> d.year, d.month_name, p.category
<span style="color:#c4b5fd;">ORDER BY</span> d.year, d.month_name, total_revenue <span style="color:#c4b5fd;">DESC</span>;

<span style="color:#6b7280;">-- Q2: Top 10 customers by revenue in Gold loyalty tier</span>
<span style="color:#c4b5fd;">SELECT</span>
    c.first_name || <span style="color:#a7f3d0;">' '</span> || c.last_name <span style="color:#c4b5fd;">AS</span> customer_name,
    c.country,
    <span style="color:#93c5fd;">SUM</span>(f.net_revenue)   <span style="color:#c4b5fd;">AS</span> lifetime_revenue,
    <span style="color:#93c5fd;">COUNT</span>(*)             <span style="color:#c4b5fd;">AS</span> total_transactions
<span style="color:#c4b5fd;">FROM</span>   fact_sales f
<span style="color:#c4b5fd;">JOIN</span>   dim_customer c <span style="color:#c4b5fd;">ON</span> f.customer_key = c.customer_key
<span style="color:#c4b5fd;">WHERE</span>  c.loyalty_tier = <span style="color:#a7f3d0;">'Gold'</span>
<span style="color:#c4b5fd;">GROUP BY</span> c.customer_key, customer_name, c.country
<span style="color:#c4b5fd;">ORDER BY</span> lifetime_revenue <span style="color:#c4b5fd;">DESC</span>
<span style="color:#c4b5fd;">LIMIT</span>  <span style="color:#fcd34d;">10</span>;</div>
  </div>
</div>

<h3>Star vs Snowflake Schema</h3>
<p>A <strong>snowflake schema</strong> is a normalised extension of the star schema where dimension tables are further broken down into sub-dimension tables. For example, instead of storing <code>category</code> and <code>subcategory</code> directly in <code>dim_product</code>, a snowflake would split them into a separate <code>dim_category</code> table. This reduces data redundancy but adds JOIN complexity. Most modern cloud warehouses favour the star schema because storage is cheap and query simplicity is valuable.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Star vs Snowflake</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Property          Star Schema              Snowflake Schema</span>
──────────────────────────────────────────────────────────────────────
Normalisation     Denormalised (flat dims)  Normalised (sub-dims)
JOIN count        Fewer (dim to fact only)  More (dim → sub-dim → fact)
Query simplicity  High — easy to read       Lower — more joins needed
Storage           Slightly more (redundancy) Less (no redundancy)
Maintenance       Easier — fewer tables     Harder — more tables
BI tool compat    Excellent                 Good (tools may struggle)
Modern preference PREFERRED                 Used when storage is critical</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.2 Dimensional Modelling: Star and Snowflake Schemas',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L23_2', [
                ['q' => 'In a star schema, what is the role of the fact table?', 'opts' => ['It stores descriptive attributes about business entities like customers and products', 'It sits at the centre and stores measurable business events as rows, with foreign keys to dimension tables and numeric measures', 'It contains date and time hierarchies for time-series analysis', 'It acts as a lookup table mapping surrogate keys to natural keys'], 'ans' => 1, 'exp' => 'The fact table is the centre of the star. Each row represents one business event or measurement (e.g., a single sale transaction). It contains foreign keys to all surrounding dimension tables and numeric measures (revenue, quantity, cost). Fact tables are typically very wide in rows (millions+) but narrow in columns.'],
                ['q' => 'Why do data warehouse dimension tables use surrogate keys instead of natural business keys?', 'opts' => ['Natural keys are always integers and too large for foreign keys', 'Surrogate keys isolate the warehouse from source system changes — if a natural key changes in the source, the surrogate key is stable', 'Surrogate keys compress better in columnar storage', 'Natural keys cannot be used in JOIN operations'], 'ans' => 1, 'exp' => 'Surrogate keys are system-generated integers independent of the business. If a customer\'s natural key changes in the source CRM (e.g., after a system migration), the surrogate key remains unchanged, preserving historical JOIN integrity. They also handle the case where multiple source systems have conflicting natural keys.'],
                ['q' => 'What makes "net_revenue" an additive fact in the sales fact table?', 'opts' => ['It can be calculated by multiplying two other columns', 'It can be meaningfully summed across all dimensions — by product, by time, by region — and the result is always valid', 'It is stored as a floating-point number rather than an integer', 'It references a foreign key in a dimension table'], 'ans' => 1, 'exp' => 'An additive fact can be summed across every dimension without producing a meaningless result. Net revenue can be summed by day, by month, by region, by product, by customer — and in any combination. This is the most useful type of fact. Non-additive facts (like ratios or percentages) cannot be summed meaningfully.'],
                ['q' => 'A BI analyst complains that snowflake schema queries are hard to write. What is the main cause?', 'opts' => ['Snowflake schemas do not support GROUP BY operations', 'Normalised sub-dimension tables require additional JOINs — a query must traverse dim_product → dim_subcategory → dim_category instead of reading a single flat dimension', 'Snowflake schemas use row-oriented storage that is slow for aggregations', 'BI tools cannot connect to snowflake schemas natively'], 'ans' => 1, 'exp' => 'The snowflake schema normalises dimensions into sub-tables. To get "category" for a product, you must JOIN dim_product to dim_subcategory to dim_category — three tables instead of one. With complex queries involving multiple dimensions, this JOIN chain multiplies and makes SQL harder to read, write, and optimise.'],
                ['q' => 'The dim_date table has columns for day_of_week, month_name, quarter, is_holiday, fiscal_period. Why pre-calculate all of these?', 'opts' => ['To avoid the computational cost of storing raw dates', 'Because date arithmetic functions vary across SQL dialects — pre-calculating attributes ensures consistent, query-ready values that need no runtime computation', 'Because date dimensions must be denormalised to qualify as a star schema', 'To enable surrogate key generation for time-series data'], 'ans' => 1, 'exp' => 'Pre-computing date attributes in dim_date avoids runtime calculations in every query. Instead of WHERE EXTRACT(QUARTER FROM sale_date) = 1, analysts write WHERE d.quarter = \'Q1\'. This is faster, simpler, and portable across databases. The date dimension is typically pre-populated for 10+ years — a one-time cost for permanent query simplicity.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.3 — ETL vs ELT: Data Integration Pipelines
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>ETL vs ELT: Data Integration Pipelines</h2>
<p>Data does not appear in a warehouse by magic. It must be extracted from source systems, cleaned and transformed into the warehouse schema, and loaded into the target tables. This process — <strong>Extract, Transform, Load (ETL)</strong> — is the operational backbone of every data warehouse. In the modern cloud era, a newer pattern has emerged: <strong>ELT (Extract, Load, Transform)</strong>, where raw data is loaded first and transformed inside the warehouse using its own compute power. Understanding both patterns — their trade-offs, tools, and failure modes — is essential for any data engineer or architect.</p>

<h3>ETL: The Traditional Approach</h3>
<p>In ETL, transformation happens in a separate processing engine <em>before</em> data enters the warehouse. The warehouse only ever sees clean, schema-conformed data. This approach dominated when warehouses were expensive and compute was scarce.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — ETL Pipeline: Extract, Transform, Load</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> pandas <span style="color:#93c5fd;">as</span> pd
<span style="color:#93c5fd;">import</span> numpy  <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> datetime <span style="color:#93c5fd;">import</span> datetime

<span style="color:#6b7280;"># ── EXTRACT ───────────────────────────────────────────────────
# Simulate raw data from an OLTP source (messy, real-world data)</span>
<span style="color:#93c5fd;">raw_sales</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'transaction_id'</span>: [<span style="color:#a7f3d0;">'TXN001'</span>, <span style="color:#a7f3d0;">'TXN002'</span>, <span style="color:#a7f3d0;">'TXN003'</span>, <span style="color:#a7f3d0;">'TXN004'</span>, <span style="color:#a7f3d0;">'TXN005'</span>],
    <span style="color:#a7f3d0;">'sale_date'</span>:       [<span style="color:#a7f3d0;">'2024-01-15'</span>, <span style="color:#a7f3d0;">'15/01/2024'</span>, <span style="color:#a7f3d0;">'2024-01-16'</span>, <span style="color:#a7f3d0;">'Jan 17 2024'</span>, <span style="color:#fca5a5;">None</span>],
    <span style="color:#a7f3d0;">'customer_id'</span>:     [<span style="color:#a7f3d0;">'C001'</span>, <span style="color:#a7f3d0;">'C002'</span>, <span style="color:#a7f3d0;">'C001'</span>, <span style="color:#a7f3d0;">'C003'</span>, <span style="color:#a7f3d0;">'C004'</span>],
    <span style="color:#a7f3d0;">'product_id'</span>:      [<span style="color:#a7f3d0;">'P100'</span>, <span style="color:#a7f3d0;">'P200'</span>, <span style="color:#a7f3d0;">'P100'</span>, <span style="color:#a7f3d0;">'P300'</span>, <span style="color:#a7f3d0;">'P200'</span>],
    <span style="color:#a7f3d0;">'quantity'</span>:        [<span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">1</span>, -<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">1</span>],     <span style="color:#6b7280;"># -1 is a return — valid</span>
    <span style="color:#a7f3d0;">'unit_price'</span>:      [<span style="color:#fcd34d;">29.99</span>, <span style="color:#fcd34d;">149.50</span>, <span style="color:#fcd34d;">29.99</span>, <span style="color:#fca5a5;">None</span>, <span style="color:#fcd34d;">149.50</span>],
    <span style="color:#a7f3d0;">'store_id'</span>:        [<span style="color:#a7f3d0;">'S01'</span>, <span style="color:#a7f3d0;">'s01'</span>, <span style="color:#a7f3d0;">'S02'</span>, <span style="color:#a7f3d0;">'S01'</span>, <span style="color:#a7f3d0;">'S03'</span>],  <span style="color:#6b7280;"># case inconsistency</span>
})

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== RAW (Extracted) Data ==="</span>)
<span style="color:#93c5fd;">print</span>(raw_sales.to_string(index=<span style="color:#fca5a5;">False</span>))

<span style="color:#6b7280;"># ── TRANSFORM ─────────────────────────────────────────────────</span>
<span style="color:#93c5fd;">def</span> <span style="color:#93c5fd;">transform_sales</span>(df):
    df = df.copy()

    <span style="color:#6b7280;"># 1. Standardise date formats</span>
    df[<span style="color:#a7f3d0;">'sale_date'</span>] = pd.to_datetime(df[<span style="color:#a7f3d0;">'sale_date'</span>], errors=<span style="color:#a7f3d0;">'coerce'</span>, dayfirst=<span style="color:#fca5a5;">False</span>)
    df[<span style="color:#a7f3d0;">'sale_date'</span>] = df[<span style="color:#a7f3d0;">'sale_date'</span>].fillna(pd.Timestamp(<span style="color:#a7f3d0;">'2024-01-17'</span>))  <span style="color:#6b7280;"># default</span>

    <span style="color:#6b7280;"># 2. Standardise store_id casing</span>
    df[<span style="color:#a7f3d0;">'store_id'</span>] = df[<span style="color:#a7f3d0;">'store_id'</span>].str.upper()

    <span style="color:#6b7280;"># 3. Handle missing unit_price (lookup from product master)</span>
    price_lookup = {<span style="color:#a7f3d0;">'P100'</span>: <span style="color:#fcd34d;">29.99</span>, <span style="color:#a7f3d0;">'P200'</span>: <span style="color:#fcd34d;">149.50</span>, <span style="color:#a7f3d0;">'P300'</span>: <span style="color:#fcd34d;">89.00</span>}
    df[<span style="color:#a7f3d0;">'unit_price'</span>] = df.apply(
        <span style="color:#93c5fd;">lambda</span> r: price_lookup.get(r[<span style="color:#a7f3d0;">'product_id'</span>], r[<span style="color:#a7f3d0;">'unit_price'</span>])
        <span style="color:#93c5fd;">if</span> pd.isna(r[<span style="color:#a7f3d0;">'unit_price'</span>]) <span style="color:#93c5fd;">else</span> r[<span style="color:#a7f3d0;">'unit_price'</span>], axis=<span style="color:#fcd34d;">1</span>)

    <span style="color:#6b7280;"># 4. Derive measures</span>
    df[<span style="color:#a7f3d0;">'net_revenue'</span>] = (df[<span style="color:#a7f3d0;">'quantity'</span>] * df[<span style="color:#a7f3d0;">'unit_price'</span>]).round(<span style="color:#fcd34d;">2</span>)
    df[<span style="color:#a7f3d0;">'date_key'</span>]    = df[<span style="color:#a7f3d0;">'sale_date'</span>].dt.strftime(<span style="color:#a7f3d0;">'%Y%m%d'</span>).astype(int)

    <span style="color:#6b7280;"># 5. Select and rename for warehouse schema</span>
    <span style="color:#93c5fd;">return</span> df[[<span style="color:#a7f3d0;">'transaction_id'</span>,<span style="color:#a7f3d0;">'date_key'</span>,<span style="color:#a7f3d0;">'customer_id'</span>,
               <span style="color:#a7f3d0;">'product_id'</span>,<span style="color:#a7f3d0;">'store_id'</span>,<span style="color:#a7f3d0;">'quantity'</span>,<span style="color:#a7f3d0;">'unit_price'</span>,<span style="color:#a7f3d0;">'net_revenue'</span>]]

<span style="color:#93c5fd;">clean_sales</span> = transform_sales(raw_sales)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== CLEAN (Transformed) Data — Ready to Load ==="</span>)
<span style="color:#93c5fd;">print</span>(clean_sales.to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== RAW (Extracted) Data ===
 transaction_id   sale_date customer_id product_id  quantity  unit_price store_id
         TXN001  2024-01-15        C001       P100         2       29.99      S01
         TXN002  15/01/2024        C002       P200         1      149.50      s01
         TXN003  2024-01-16        C001       P100        -1       29.99      S02
         TXN004 Jan 17 2024        C003       P300         3         NaN      S01
         TXN005        None        C004       P200         1      149.50      S03

=== CLEAN (Transformed) Data — Ready to Load ===
 transaction_id  date_key customer_id product_id store_id  quantity  unit_price  net_revenue
         TXN001  20240115        C001       P100      S01         2       29.99        59.98
         TXN002  20240115        C002       P200      S01         1      149.50       149.50
         TXN003  20240116        C001       P100      S02        -1       29.99       -29.99
         TXN004  20240117        C003       P300      S01         3       89.00       267.00
         TXN005  20240117        C004       P200      S03         1      149.50       149.50</div>
  </div>
</div>

<h3>ETL vs ELT: When to Use Which</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — ETL vs ELT</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">ETL (Extract → Transform → Load):</span>
  Transform OUTSIDE the warehouse (Python, Spark, SSIS).
  Load only clean data.
  + Warehouse never sees dirty data.
  + Works with any warehouse (no SQL transform needed).
  - Transform infrastructure must scale separately.
  - Harder to debug (error in a separate system).
  Best for: Legacy warehouses, complex non-SQL transforms,
            strict governance (GDPR redaction before load).

<span style="color:#93c5fd;">ELT (Extract → Load → Transform):</span>
  Load raw data first; transform INSIDE the warehouse using SQL.
  Tools: dbt (data build tool), Dataform, warehouse-native SQL.
  + Leverage warehouse compute power (Snowflake, BigQuery).
  + Full raw data always available — can re-transform anytime.
  + SQL transforms are version-controlled, tested, documented.
  - Raw layer contains potentially sensitive data.
  - Requires a capable, scalable warehouse.
  Best for: Cloud-native stacks (Snowflake, BigQuery, Redshift),
            dbt-based transformation layers.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.3 ETL vs ELT: Data Integration Pipelines',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L23_3', [
                ['q' => 'In the ETL pipeline code, the sale_date column contains four different date formats. What pandas function handles this safely?', 'opts' => ['pd.read_csv() with parse_dates=True', 'pd.to_datetime() with errors="coerce" — invalid formats become NaT instead of raising errors', 'datetime.strptime() applied row-by-row', 'df["sale_date"].astype("datetime64")'], 'ans' => 1, 'exp' => 'pd.to_datetime(series, errors="coerce") attempts to parse every value; unparseable values become NaT (Not a Time) rather than crashing the pipeline. This is critical for production ETL — a single bad date should not halt an entire nightly load. The NaT values are then handled with fillna() or flagged for review.'],
                ['q' => 'The raw data has store_id "s01" and "S01" for the same store. What ETL transformation step fixes this?', 'opts' => ['A lookup table JOIN to resolve ambiguous IDs', 'Standardising case with str.upper() or str.lower() as part of the clean/conform step', 'Dropping all rows with inconsistent casing', 'Assigning a surrogate key based on alphabetical order'], 'ans' => 1, 'exp' => 'Case inconsistency is a classic data quality issue. Applying str.upper() standardises all store IDs to a single canonical form before loading. This is part of the "conform" phase of ETL where data from disparate source systems is unified. Without it, "s01" and "S01" would be treated as two different stores in GROUP BY queries.'],
                ['q' => 'In ELT, why is the raw data retained in the warehouse after loading?', 'opts' => ['To allow OLTP queries to run against unprocessed data', 'Because the warehouse has unlimited storage that cannot be freed', 'Raw data preservation allows re-running transformations with new logic anytime — you can fix business logic bugs without re-extracting from the source', 'To satisfy non-volatile requirements that prohibit any data deletion'], 'ans' => 2, 'exp' => 'In ELT, raw data lands in a raw/staging layer and is never overwritten. If a business logic bug is found in a transformation (e.g., wrong revenue calculation), you can re-run the dbt model against the preserved raw data without re-extracting from the source system. This is a major operational advantage over ETL where only clean data is kept.'],
                ['q' => 'What does "incremental loading" mean in ETL and why is it preferred over full loads for large tables?', 'opts' => ['Loading data in small chunks to avoid memory errors', 'Loading only new or changed records since the last ETL run rather than re-loading the entire source table', 'Loading data into a temporary staging table before the final target', 'Running multiple ETL jobs in parallel to increase throughput'], 'ans' => 1, 'exp' => 'Incremental loading uses a high-watermark (last run timestamp or max ID) to extract only records created or modified since the previous load. A fact_sales table with 5 billion rows cannot be reloaded nightly — it would take hours. Incremental loading might process only the 500,000 new rows from yesterday, completing in minutes.'],
                ['q' => 'A company must redact customer PII (names, emails) before data enters the warehouse for GDPR compliance. Should they use ETL or ELT?', 'opts' => ['ELT — transform inside the warehouse for full control', 'ETL — redact PII in the transform engine before loading so sensitive data never enters the warehouse', 'Both are equally suitable — PII is handled at the query layer', 'Neither — GDPR prohibits storing any customer data in a warehouse'], 'ans' => 1, 'exp' => 'ETL is preferred when sensitive data must be redacted before reaching the warehouse. In ELT, raw data (including PII) would first be loaded into the warehouse, requiring strict access controls even on the raw layer. ETL\'s transform-first approach ensures PII is masked or removed before it enters any warehouse storage, simplifying compliance.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.4 — Slowly Changing Dimensions (SCDs)
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Slowly Changing Dimensions (SCDs)</h2>
<p>One of the most nuanced and practically important challenges in data warehousing is handling dimension attributes that change over time. When a customer moves to a new city, when a product is re-categorised, when an employee changes departments — what should happen to the dimension record? The answer is not obvious, and the wrong choice destroys the analytical value of your historical data. <strong>Slowly Changing Dimensions (SCDs)</strong> is the formal framework, developed by Ralph Kimball, for handling these changes systematically. There are six SCD types, but three dominate real-world use.</p>

<h3>SCD Type 1 — Overwrite (No History)</h3>
<p>Simply update the dimension record with the new value. The old value is lost forever. Use Type 1 only when history is genuinely irrelevant — for example, correcting a data entry error, or when a product description changes in a way that makes the old description meaningless.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — SCD Type 1: Overwrite</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Customer C001 moved from 'London' to 'Manchester'
-- SCD Type 1: Simply overwrite city — history is lost</span>

<span style="color:#c4b5fd;">UPDATE</span> dim_customer
<span style="color:#c4b5fd;">SET</span>    city             = <span style="color:#a7f3d0;">'Manchester'</span>,
       last_updated_date = <span style="color:#93c5fd;">CURRENT_DATE</span>
<span style="color:#c4b5fd;">WHERE</span>  customer_id = <span style="color:#a7f3d0;">'C001'</span>;

<span style="color:#6b7280;">-- CONSEQUENCE: all historical fact_sales rows for C001 now
-- show 'Manchester' — even sales made when C001 lived in London.
-- Geographic analysis of past sales is now WRONG.
-- Use Type 1 ONLY when this is acceptable (e.g., fixing typos).</span></div>
  </div>
</div>

<h3>SCD Type 2 — Add New Row (Full History)</h3>
<p>The most important SCD type for analytical accuracy. When an attribute changes, the current row is expired and a new row is inserted with the updated values. This preserves full history — you can see what the dimension looked like at any point in time and join it correctly to facts.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — SCD Type 2: Add New Row with Effective Dating</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- dim_customer with SCD Type 2 columns</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> dim_customer_scd2 (
    customer_key    <span style="color:#a7f3d0;">INT PRIMARY KEY</span>,        <span style="color:#6b7280;">-- surrogate — unique per version</span>
    customer_id     <span style="color:#a7f3d0;">VARCHAR(20)</span>,            <span style="color:#6b7280;">-- natural key — same across versions</span>
    first_name      <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    last_name       <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    city            <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    loyalty_tier    <span style="color:#a7f3d0;">VARCHAR(20)</span>,
    <span style="color:#6b7280;">-- SCD Type 2 control columns:</span>
    effective_date  <span style="color:#a7f3d0;">DATE NOT NULL</span>,          <span style="color:#6b7280;">-- when this version became active</span>
    expiry_date     <span style="color:#a7f3d0;">DATE</span>,                   <span style="color:#6b7280;">-- NULL = currently active</span>
    is_current      <span style="color:#a7f3d0;">BOOLEAN DEFAULT TRUE</span>    <span style="color:#6b7280;">-- flag for easy filtering</span>
);

<span style="color:#6b7280;">-- Initial record: C001 lives in London (active from 2020-01-01)</span>
<span style="color:#c4b5fd;">INSERT INTO</span> dim_customer_scd2
<span style="color:#c4b5fd;">VALUES</span> (<span style="color:#fcd34d;">1001</span>, <span style="color:#a7f3d0;">'C001'</span>, <span style="color:#a7f3d0;">'Alice'</span>, <span style="color:#a7f3d0;">'Smith'</span>, <span style="color:#a7f3d0;">'London'</span>,  <span style="color:#a7f3d0;">'Gold'</span>,
        <span style="color:#a7f3d0;">'2020-01-01'</span>, <span style="color:#fca5a5;">NULL</span>, <span style="color:#fca5a5;">TRUE</span>);

<span style="color:#6b7280;">-- Customer moves to Manchester on 2024-03-01:
-- Step 1: Expire the old record</span>
<span style="color:#c4b5fd;">UPDATE</span> dim_customer_scd2
<span style="color:#c4b5fd;">SET</span>    expiry_date = <span style="color:#a7f3d0;">'2024-02-29'</span>,
       is_current  = <span style="color:#fca5a5;">FALSE</span>
<span style="color:#c4b5fd;">WHERE</span>  customer_id = <span style="color:#a7f3d0;">'C001'</span> <span style="color:#c4b5fd;">AND</span> is_current = <span style="color:#fca5a5;">TRUE</span>;

<span style="color:#6b7280;">-- Step 2: Insert new version with updated city</span>
<span style="color:#c4b5fd;">INSERT INTO</span> dim_customer_scd2
<span style="color:#c4b5fd;">VALUES</span> (<span style="color:#fcd34d;">1002</span>, <span style="color:#a7f3d0;">'C001'</span>, <span style="color:#a7f3d0;">'Alice'</span>, <span style="color:#a7f3d0;">'Smith'</span>, <span style="color:#a7f3d0;">'Manchester'</span>, <span style="color:#a7f3d0;">'Gold'</span>,
        <span style="color:#a7f3d0;">'2024-03-01'</span>, <span style="color:#fca5a5;">NULL</span>, <span style="color:#fca5a5;">TRUE</span>);

<span style="color:#6b7280;">-- Now a fact_sales JOIN using the surrogate key correctly links
-- pre-March 2024 sales to London and post-March sales to Manchester.
-- Historical accuracy is preserved perfectly.</span>

<span style="color:#6b7280;">-- Query: get current address for all customers</span>
<span style="color:#c4b5fd;">SELECT</span> customer_id, first_name, city
<span style="color:#c4b5fd;">FROM</span>   dim_customer_scd2
<span style="color:#c4b5fd;">WHERE</span>  is_current = <span style="color:#fca5a5;">TRUE</span>;</div>
  </div>
</div>

<h3>SCD Type 3 — Add New Column (Limited History)</h3>
<p>Type 3 adds a new column to store the previous value, keeping only one version of history. For example, <code>current_city</code> and <code>previous_city</code>. Simple to implement but can only track one prior value — unsuitable for dimensions that change frequently.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — SCD Types Summary</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Type  Method              History   Complexity  Best For</span>
──────────────────────────────────────────────────────────────────────
  1   Overwrite            None      Low         Data corrections, irrelevant history
  2   New row + expiry     Full      High        Most analytical use cases (RECOMMENDED)
  3   New column           One step  Medium      Rarely changes, compare current/previous
  4   History table        Full      Medium      Separate history table, current table fast
  6   Hybrid (1+2+3)       Full      Very high   Enterprise DWs needing all three views</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.4 Slowly Changing Dimensions (SCDs)',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L23_4', [
                ['q' => 'A company applies SCD Type 1 when a customer changes city. A query for "revenue by city in 2022" runs in 2025. What is wrong?', 'opts' => ['The query will fail with a foreign key error', 'All historical sales will show the 2025 city — geographic analysis of 2022 data will be incorrect because history was overwritten', 'SCD Type 1 prevents GROUP BY city from executing', 'The query will double-count transactions with city changes'], 'ans' => 1, 'exp' => 'SCD Type 1 overwrites the old value. After the update, every fact_sales row for that customer — including those from years ago — will JOIN to the new city. A 2022 revenue-by-city report run in 2025 will attribute old London sales to Manchester. This is why Type 1 is only suitable when historical accuracy of that attribute is not required.'],
                ['q' => 'In SCD Type 2, why does the fact table store the surrogate key rather than the natural customer ID?', 'opts' => ['Surrogate keys are shorter and save storage space', 'Each version of a customer has a different surrogate key — the fact row links to the exact version of the customer dimension that was current at transaction time, preserving historical accuracy', 'Natural keys are not guaranteed to be unique across source systems', 'Surrogate keys enable faster B-tree index lookups than natural keys'], 'ans' => 1, 'exp' => 'The power of SCD Type 2 lies in the surrogate key. Customer C001 may have surrogate key 1001 (London version) and 1002 (Manchester version). A sale in January links to surrogate 1001; a sale in April links to 1002. The natural key C001 is the same for both — but the surrogate key correctly identifies which dimensional version was active at transaction time.'],
                ['q' => 'What is the purpose of the is_current flag in an SCD Type 2 implementation?', 'opts' => ['It marks records that have been loaded in the current ETL run', 'It provides a fast filter to retrieve only the active (current) version of a dimension record without date range comparisons', 'It flags records that have never had any attribute changes', 'It indicates that the surrogate key was generated by the current ETL process'], 'ans' => 1, 'exp' => 'WHERE is_current = TRUE is far simpler and faster than WHERE effective_date <= TODAY AND (expiry_date IS NULL OR expiry_date > TODAY). The is_current boolean flag is a query optimisation and readability aid — it instantly identifies the active version without date arithmetic. It is kept consistent by always setting FALSE on expiry and TRUE on new inserts.'],
                ['q' => 'When should you choose SCD Type 3 over SCD Type 2?', 'opts' => ['When complete history across unlimited changes is required', 'When only the most recent change matters and the dimension attribute changes very infrequently — tracking current vs previous is sufficient', 'When the dimension changes daily and storage is limited', 'When the ETL tool does not support date range filtering'], 'ans' => 1, 'exp' => 'SCD Type 3 stores current_value and previous_value columns. It is appropriate when: (a) you only need to compare current state vs one prior state, and (b) the attribute changes rarely. A classic example: a sales territory reorganisation where you want to report both "under new territory" and "under old territory." If the attribute changes more than once, Type 3 loses the older history.'],
                ['q' => 'A product is re-categorised from "Electronics" to "Smart Home." Historical sales reports must still show "Electronics" for pre-change purchases. Which SCD type is required?', 'opts' => ['SCD Type 1 — overwrite the category field', 'SCD Type 3 — add a previous_category column', 'SCD Type 2 — expire the current record and insert a new version with the new category', 'No SCD needed — use a filter in the query instead'], 'ans' => 2, 'exp' => 'Preserving historical category attribution requires SCD Type 2. The current product row (with "Electronics") is expired with the re-categorisation date. A new row (with "Smart Home") is inserted. Historical fact_sales rows linked to the old surrogate key will JOIN to "Electronics"; new sales link to "Smart Home." Historical reports are automatically correct.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.5 — Data Vault Modelling
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Data Vault Modelling</h2>
<p><strong>Data Vault</strong> is an alternative data warehouse modelling methodology developed by Dan Linstedt in the 1990s and formally published in 2000. Where Kimball's dimensional modelling optimises for query performance and analyst usability, Data Vault optimises for <em>auditability, flexibility, and parallel loading</em>. It is particularly popular in industries with heavy regulatory requirements (banking, insurance, government) where the ability to trace every data point back to its source is a legal necessity, not a nice-to-have.</p>

<h3>The Three Building Blocks</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Data Vault: Hubs, Links, Satellites</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">HUB — "What are the core business entities?"</span>
  Stores the unique business keys for each business concept.
  Columns: hash_key (PK), business_key, load_date, record_source.
  Example: hub_customer stores each unique customer_id exactly once.
  Very stable — never changes once loaded.

<span style="color:#93c5fd;">LINK — "How are entities related?"</span>
  Stores relationships between hubs (many-to-many, transactional).
  Columns: hash_key (PK), hub_A_hash_key (FK), hub_B_hash_key (FK),
           load_date, record_source.
  Example: link_customer_order connects hub_customer to hub_order.
  Also very stable — relationships rarely need to change.

<span style="color:#a7f3d0;">SATELLITE — "What do we know about entities/relationships?"</span>
  Stores descriptive attributes and their full change history.
  Columns: parent_hash_key (FK), load_date (PK together),
           all descriptive attributes, hash_diff, record_source.
  Example: sat_customer_demographics stores name, address, etc.
  Changes over time — new rows added for each change (like SCD2).

<span style="color:#fcd34d;">KEY PRINCIPLE:</span>
  Every table has load_date and record_source columns.
  This provides complete auditability — every data point can be
  traced to which source system, on which date, delivered it.
  No data is ever deleted. The vault is append-only.</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Data Vault Table Definitions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- HUB: Core business entity — unique customers</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> hub_customer (
    customer_hk     <span style="color:#a7f3d0;">CHAR(32) PRIMARY KEY</span>,    <span style="color:#6b7280;">-- MD5 hash of business key</span>
    customer_bk     <span style="color:#a7f3d0;">VARCHAR(20) NOT NULL</span>,    <span style="color:#6b7280;">-- natural business key</span>
    load_date       <span style="color:#a7f3d0;">TIMESTAMP NOT NULL</span>,
    record_source   <span style="color:#a7f3d0;">VARCHAR(100) NOT NULL</span>    <span style="color:#6b7280;">-- e.g. 'CRM_SALESFORCE'</span>
);

<span style="color:#6b7280;">-- HUB: Core business entity — unique orders</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> hub_order (
    order_hk        <span style="color:#a7f3d0;">CHAR(32) PRIMARY KEY</span>,
    order_bk        <span style="color:#a7f3d0;">VARCHAR(20) NOT NULL</span>,
    load_date       <span style="color:#a7f3d0;">TIMESTAMP NOT NULL</span>,
    record_source   <span style="color:#a7f3d0;">VARCHAR(100) NOT NULL</span>
);

<span style="color:#6b7280;">-- LINK: Relationship between customer and order</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> link_customer_order (
    cust_order_hk   <span style="color:#a7f3d0;">CHAR(32) PRIMARY KEY</span>,  <span style="color:#6b7280;">-- hash of both BKs combined</span>
    customer_hk     <span style="color:#a7f3d0;">CHAR(32) REFERENCES</span> hub_customer(customer_hk),
    order_hk        <span style="color:#a7f3d0;">CHAR(32) REFERENCES</span> hub_order(order_hk),
    load_date       <span style="color:#a7f3d0;">TIMESTAMP NOT NULL</span>,
    record_source   <span style="color:#a7f3d0;">VARCHAR(100) NOT NULL</span>
);

<span style="color:#6b7280;">-- SATELLITE: Customer demographic attributes (with full history)</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> sat_customer_demographics (
    customer_hk     <span style="color:#a7f3d0;">CHAR(32) REFERENCES</span> hub_customer(customer_hk),
    load_date       <span style="color:#a7f3d0;">TIMESTAMP NOT NULL</span>,
    <span style="color:#c4b5fd;">PRIMARY KEY</span>     (customer_hk, load_date),   <span style="color:#6b7280;">-- composite PK</span>
    first_name      <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    last_name       <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    city            <span style="color:#a7f3d0;">VARCHAR(50)</span>,
    email           <span style="color:#a7f3d0;">VARCHAR(100)</span>,
    hash_diff       <span style="color:#a7f3d0;">CHAR(32)</span>,              <span style="color:#6b7280;">-- hash of all attributes for change detection</span>
    record_source   <span style="color:#a7f3d0;">VARCHAR(100) NOT NULL</span>
);

<span style="color:#6b7280;">-- SATELLITE: Order business facts</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> sat_order_details (
    order_hk        <span style="color:#a7f3d0;">CHAR(32) REFERENCES</span> hub_order(order_hk),
    load_date       <span style="color:#a7f3d0;">TIMESTAMP NOT NULL</span>,
    <span style="color:#c4b5fd;">PRIMARY KEY</span>     (order_hk, load_date),
    order_date      <span style="color:#a7f3d0;">DATE</span>,
    total_amount    <span style="color:#a7f3d0;">DECIMAL(12,2)</span>,
    status          <span style="color:#a7f3d0;">VARCHAR(20)</span>,
    hash_diff       <span style="color:#a7f3d0;">CHAR(32)</span>,
    record_source   <span style="color:#a7f3d0;">VARCHAR(100) NOT NULL</span>
);</div>
  </div>
</div>

<h3>Data Vault vs Kimball: Choosing the Right Approach</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Kimball (Dimensional) vs Data Vault</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Dimension          Kimball Star Schema         Data Vault</span>
─────────────────────────────────────────────────────────────────────
Primary goal       Query simplicity            Auditability, agility
Best for           Self-service BI             Regulated industries
Schema complexity  Low                         High
Query complexity   Low (few JOINs)             High (many JOINs)
New source add     Difficult (schema changes)  Easy (new satellites)
Audit trail        Limited                     Complete (every load)
Parallel load      Hard                        Easy (hubs/links/sats independent)
Time to first query Fast                       Slower (needs info marts)
Industry fit       Retail, tech, startups      Banking, insurance, govt</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.5 Data Vault Modelling',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L23_5', [
                ['q' => 'What are the three building blocks of a Data Vault model?', 'opts' => ['Facts, Dimensions, and Bridges', 'Hubs (business entities), Links (relationships), and Satellites (descriptive attributes)', 'Raw, Staging, and Presentation layers', 'Ingestion, Transformation, and Serving tables'], 'ans' => 1, 'exp' => 'Data Vault has exactly three table types: Hubs store unique business keys for each core entity (customer, product); Links store relationships between hubs; Satellites store descriptive attributes and their full history attached to hubs or links. Every table includes load_date and record_source for complete auditability.'],
                ['q' => 'Why does the sat_customer_demographics table use a composite primary key of (customer_hk, load_date)?', 'opts' => ['Because customer_hk alone is not unique across satellite tables', 'Because a satellite tracks the full history of changes — the same customer has multiple rows, each valid at a different load_date, so the combination is unique', 'Because load_date alone uniquely identifies each ETL batch', 'Because Data Vault requires all PKs to include a timestamp'], 'ans' => 1, 'exp' => 'Satellites are append-only history tables. When a customer\'s city changes, a new row is inserted (not an update). Multiple rows exist for the same customer_hk with different load_dates. The composite PK (customer_hk, load_date) ensures each version is uniquely identified. This is analogous to SCD Type 2 in dimensional modelling.'],
                ['q' => 'What is the purpose of the hash_diff column in a satellite table?', 'opts' => ['To encrypt sensitive attribute values for GDPR compliance', 'To detect whether any attribute has changed since the last load — if the hash of all attributes matches, no new row is needed', 'To generate the hash key for the hub table', 'To link satellite rows to the corresponding link table'], 'ans' => 1, 'exp' => 'hash_diff is an MD5 or SHA hash of all descriptive attribute values in the satellite row. During an ETL load, the incoming record\'s hash_diff is compared to the latest stored hash_diff for that hub key. If they match, nothing changed — no new row is inserted. If they differ, a new satellite row is appended. This is an efficient change-detection mechanism.'],
                ['q' => 'Why is Data Vault preferred over Kimball star schema in regulated industries like banking?', 'opts' => ['Star schemas cannot handle financial data types', 'Every Data Vault row includes load_date and record_source, providing a complete immutable audit trail — regulators can trace any data point to its origin source and exact load timestamp', 'Data Vault performs better on aggregation queries than star schemas', 'Banking data has too many dimensions for a star schema'], 'ans' => 1, 'exp' => 'Regulatory requirements in banking, insurance, and government often mandate full data lineage — the ability to answer "where did this number come from and when did it enter the system?" Data Vault\'s universal load_date and record_source columns, combined with its append-only nature, provide this audit trail automatically. Star schemas focus on query performance and sacrifice this auditability.'],
                ['q' => 'A new data source (a third-party fraud detection API) needs to add attributes to existing customers. In Data Vault, how is this handled without breaking existing tables?', 'opts' => ['A new column is added to the hub_customer table', 'A new satellite table sat_customer_fraud_signals is created and attached to hub_customer — existing tables remain unchanged', 'A new hub is created for the fraud detection system with its own business key', 'The link table is extended with the new fraud attributes'], 'ans' => 1, 'exp' => 'Data Vault\'s extensibility is one of its key advantages. Adding a new source means creating a new satellite attached to the existing hub. hub_customer remains unchanged; the new sat_customer_fraud_signals table stands independently alongside existing satellites like sat_customer_demographics. No schema changes are needed on existing objects — zero risk of breaking existing queries.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.6 — Columnar Storage and Query Optimisation
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Columnar Storage and Query Optimisation</h2>
<p>Traditional relational databases store data row-by-row: all fields of a single record are written contiguously on disk. This is optimal for OLTP workloads — you fetch or update one record at a time. But analytical queries aggregate millions of records across just a few columns. A row-oriented store must read every column of every row even if the query only needs two of them. <strong>Columnar (column-oriented) storage</strong> stores each column separately — when a query needs only revenue and date, it reads only those two columns from disk, skipping everything else. This fundamental difference is why modern cloud warehouses (Snowflake, BigQuery, Redshift) achieve query performance that would be impossible with traditional row stores.</p>

<h3>Row Storage vs Column Storage</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Row vs Column Storage Layout</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Table: fact_sales (4 rows, 5 columns)</span>
sale_id | date_key | customer_key | product_key | net_revenue
────────┼──────────┼──────────────┼─────────────┼────────────
   1    | 20240101 |     1001     |     501     |   59.98
   2    | 20240101 |     1002     |     502     |  149.50
   3    | 20240102 |     1001     |     503     |   89.00
   4    | 20240103 |     1003     |     501     |  267.00

<span style="color:#fca5a5;">ROW STORE (PostgreSQL, MySQL) — on disk:</span>
[1, 20240101, 1001, 501, 59.98] [2, 20240101, 1002, 502, 149.50]
[3, 20240102, 1001, 503, 89.00] [4, 20240103, 1003, 501, 267.00]
For: SELECT SUM(net_revenue) → must read ALL 5 columns of ALL rows.
I/O: 4 rows × 5 columns = 20 values read.

<span style="color:#a7f3d0;">COLUMN STORE (Snowflake, BigQuery) — on disk:</span>
sale_id:      [1, 2, 3, 4]
date_key:     [20240101, 20240101, 20240102, 20240103]
customer_key: [1001, 1002, 1001, 1003]
product_key:  [501, 502, 503, 501]
net_revenue:  [59.98, 149.50, 89.00, 267.00]
For: SELECT SUM(net_revenue) → reads ONLY the net_revenue column.
I/O: 4 values read. 5× less I/O than row store.
At scale (1 billion rows, 50 columns): query reads 1/50 of the data.</div>
  </div>
</div>

<h3>Why Column Stores Compress So Much Better</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Column Compression Demonstration</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> zlib, json, struct
<span style="color:#93c5fd;">import</span> numpy <span style="color:#93c5fd;">as</span> np

<span style="color:#6b7280;"># Simulate 1,000,000 rows of fact_sales</span>
np.random.seed(<span style="color:#fcd34d;">42</span>)
n = <span style="color:#fcd34d;">1_000_000</span>

<span style="color:#6b7280;"># Columns with realistic cardinalities</span>
store_id     = np.random.choice([<span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">4</span>,<span style="color:#fcd34d;">5</span>], n)         <span style="color:#6b7280;"># low cardinality (5 stores)</span>
product_key  = np.random.randint(<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">1001</span>, n)              <span style="color:#6b7280;"># 1000 products</span>
net_revenue  = np.round(np.random.uniform(<span style="color:#fcd34d;">9.99</span>, <span style="color:#fcd34d;">999.99</span>, n), <span style="color:#fcd34d;">2</span>)

<span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">compressed_size_kb</span>(arr):
    raw_bytes    = arr.astype(np.float32).tobytes()
    compressed   = zlib.compress(raw_bytes, level=<span style="color:#fcd34d;">6</span>)
    <span style="color:#93c5fd;">return</span> len(raw_bytes)//<span style="color:#fcd34d;">1024</span>, len(compressed)//<span style="color:#fcd34d;">1024</span>

raw_s, comp_s = compressed_size_kb(store_id)
raw_p, comp_p = compressed_size_kb(product_key)
raw_r, comp_r = compressed_size_kb(net_revenue)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Column      Raw (KB)  Compressed (KB)  Ratio"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"-"</span> * <span style="color:#fcd34d;">50</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"store_id    {raw_s:>8}  {comp_s:>15}  {raw_s/max(comp_s,1):.1f}x"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"product_key {raw_p:>8}  {comp_p:>15}  {raw_p/max(comp_p,1):.1f}x"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"net_revenue {raw_r:>8}  {comp_r:>15}  {raw_r/max(comp_r,1):.1f}x"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nLow-cardinality columns (store_id) compress best — "</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"only 5 unique values in 1M rows = extreme repetition."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Column      Raw (KB)  Compressed (KB)  Ratio
--------------------------------------------------
store_id        3906              312  12.5x
product_key     3906             2187   1.8x
net_revenue     3906             3798   1.0x

Low-cardinality columns (store_id) compress best —
only 5 unique values in 1M rows = extreme repetition.</div>
  </div>
</div>

<h3>Key Warehouse Query Optimisation Techniques</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">REFERENCE — Warehouse Optimisation Techniques</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">PARTITIONING:</span>
  Split a large table into physical segments by a partition key.
  Typically by date (monthly or yearly partitions).
  Benefit: Queries with WHERE date >= '2024-01-01' scan
           ONLY 2024 partitions — skipping 10 years of history.
  Example: fact_sales partitioned by year(sale_date).

<span style="color:#93c5fd;">CLUSTERING / SORT KEYS:</span>
  Physically sort rows on disk by one or more columns.
  Snowflake: CLUSTER BY (store_id, date_key).
  Benefit: Queries filtering on cluster columns read contiguous
           disk blocks — vastly fewer I/O operations.

<span style="color:#a7f3d0;">QUERY PRUNING / PREDICATE PUSHDOWN:</span>
  The query engine skips data blocks whose min/max range cannot
  satisfy the WHERE clause (zone maps / min-max indexes).
  Example: block containing revenue [50-200] is skipped for
           WHERE net_revenue > 500.

<span style="color:#fcd34d;">MATERIALISED VIEWS / AGGREGATION TABLES:</span>
  Pre-compute common aggregations and store them.
  Query hits the pre-aggregated table instead of scanning billions of rows.
  dbt: use materialized='table' or 'incremental' models.
  Snowflake: CREATE OR REPLACE MATERIALIZED VIEW mv_monthly_sales AS ...</span></div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.6 Columnar Storage and Query Optimisation',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L23_6', [
                ['q' => 'For the query SELECT SUM(net_revenue) FROM fact_sales with 1 billion rows and 50 columns, how much less data does a column store read compared to a row store?', 'opts' => ['2x less', '10x less', '50x less — only 1 of 50 columns is needed', '100x less'], 'ans' => 2, 'exp' => 'A column store reads only the net_revenue column — 1/50th of the total data. A row store reads all 50 columns for every row even though 49 are unused. At 1 billion rows, this is the difference between reading 2GB vs 100GB from disk. This I/O reduction is the primary source of columnar performance gains.'],
                ['q' => 'Why does the store_id column (5 unique values in 1M rows) compress 12.5x better than net_revenue?', 'opts' => ['net_revenue uses more bytes per value', 'Low-cardinality columns have extreme repetition — a column of 5 repeating values compresses with run-length encoding or dictionary encoding to a tiny fraction of raw size', 'Store IDs are shorter strings than revenue values', 'The compression algorithm is tuned specifically for integer IDs'], 'ans' => 1, 'exp' => 'Compression algorithms exploit repetition. With only 5 unique store IDs across 1M rows, a dictionary encoding stores the 5 values once and then a 1-million-entry array of 3-bit codes. High-cardinality columns like net_revenue (near-unique float values) offer almost no repetition to exploit, resulting in minimal compression.'],
                ['q' => 'A query on a 10-year fact_sales table has WHERE sale_date >= \'2024-01-01\'. How does date partitioning improve performance?', 'opts' => ['Partitioning reorders rows within 2024 for faster sorting', 'The query engine skips all partitions for years 2014-2023 entirely — only the 2024 partition is scanned, reducing I/O by up to 90%', 'Date partitioning creates an index on sale_date for B-tree lookups', 'Partitioning prevents the query from running on non-2024 data by access control'], 'ans' => 1, 'exp' => 'Partition pruning means the query engine identifies which partitions can possibly satisfy the WHERE clause and skips all others. With annual partitioning, a 2024-only query skips 9 of 10 partitions — a 90% reduction in data scanned. For daily-partitioned tables with multi-year history, savings can be 99%+.'],
                ['q' => 'What is a materialised view and when should it be used in a data warehouse?', 'opts' => ['A view that can be updated with DML operations like a regular table', 'A pre-computed, physically stored result set of a query — used when the same complex aggregation is queried frequently and the underlying data changes infrequently', 'A view that is automatically partitioned by the warehouse engine', 'A dimension table that is refreshed in real time from the source system'], 'ans' => 1, 'exp' => 'A materialised view stores the query result on disk. Instead of scanning billions of rows for "monthly revenue by region," the warehouse reads the materialised result — orders of magnitude faster. The trade-off is storage cost and staleness: the materialised view must be refreshed when underlying data changes, which adds ETL complexity.'],
                ['q' => 'Zone maps (min/max indexes) in columnar warehouses skip data blocks where min_val > filter_val or max_val < filter_val. What query pattern does this NOT help?', 'opts' => ['WHERE sale_date BETWEEN \'2024-01-01\' AND \'2024-03-31\'', 'WHERE net_revenue > 500', 'WHERE store_id IN (1, 3, 5)', 'WHERE category = \'Electronics\''], 'ans' => 2, 'exp' => 'Zone maps skip a block only if the block\'s [min, max] range is completely outside the filter. For WHERE store_id IN (1, 3, 5), every data block might contain stores 1, 3, and 5 mixed together — so the min/max is always [1,5] and no blocks can be pruned. IN predicates on non-clustered, interleaved data defeat zone map pruning. Clustering the data on store_id first would fix this.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.7 — Cloud Data Warehouses: Snowflake, BigQuery, Redshift
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Cloud Data Warehouses: Snowflake, BigQuery, and Redshift</h2>
<p>The three dominant cloud data warehouses — <strong>Snowflake</strong>, <strong>Google BigQuery</strong>, and <strong>Amazon Redshift</strong> — have fundamentally transformed what it means to run a data warehouse. They eliminated the need to purchase and manage hardware, decoupled storage from compute, and introduced elastic scaling that was impossible with on-premises systems. Understanding their architecture differences, pricing models, and optimal use cases is essential knowledge for any modern data engineer or architect.</p>

<h3>Architecture Philosophies</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">COMPARISON — Snowflake vs BigQuery vs Redshift</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#fcd34d;">Feature              Snowflake            BigQuery             Redshift</span>
────────────────────────────────────────────────────────────────────────────
Provider             Multi-cloud          Google Cloud         AWS
Architecture         Shared disk /        Serverless,          Shared-nothing
                     Separate compute     fully managed        MPP cluster
Storage              Separate (S3/ADLS)   Managed (Colossus)   Separate (S3)
Compute unit         Virtual Warehouse    Slot (100 CPUs)      Node (dc2/ra3)
Compute scaling      Instant (seconds)    Automatic            Minutes
Pricing model        Compute credits      Per TB scanned       Instance hours
                     + storage            + storage            + storage
Idle cost            Near-zero (suspend)  Zero (serverless)    Full node cost
Concurrency          High (multi-cluster) Very high (auto)     Limited (WLM)
SQL dialect          ANSI + extensions    Standard SQL         PostgreSQL-based
Best for             Multi-workload,      Ad-hoc analytics,    AWS-native shops,
                     SaaS, regulated      ML feature stores,   existing Postgres
                     industries           Google ecosystem     users</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Warehouse-Specific Features</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ── SNOWFLAKE FEATURES ──────────────────────────────────────</span>

<span style="color:#6b7280;">-- Snowflake: Semi-structured data (JSON) stored in VARIANT columns</span>
<span style="color:#c4b5fd;">SELECT</span>
    event_data:<span style="color:#a7f3d0;">user_id</span>::<span style="color:#a7f3d0;">STRING</span>           <span style="color:#c4b5fd;">AS</span> user_id,
    event_data:<span style="color:#a7f3d0;">properties</span>:<span style="color:#a7f3d0;">page_name</span>::<span style="color:#a7f3d0;">STRING</span> <span style="color:#c4b5fd;">AS</span> page_name,
    event_data:<span style="color:#a7f3d0;">timestamp</span>::<span style="color:#a7f3d0;">TIMESTAMP</span>        <span style="color:#c4b5fd;">AS</span> event_ts
<span style="color:#c4b5fd;">FROM</span> raw_events
<span style="color:#c4b5fd;">WHERE</span> event_data:<span style="color:#a7f3d0;">event_type</span> = <span style="color:#a7f3d0;">'page_view'</span>;

<span style="color:#6b7280;">-- Snowflake: Time Travel — query data as it existed in the past</span>
<span style="color:#c4b5fd;">SELECT</span> <span style="color:#93c5fd;">COUNT</span>(*) <span style="color:#c4b5fd;">FROM</span> fact_sales
AT (TIMESTAMP => <span style="color:#a7f3d0;">'2024-01-15 08:00:00'</span>::TIMESTAMP);

<span style="color:#6b7280;">-- ── BIGQUERY FEATURES ────────────────────────────────────────</span>

<span style="color:#6b7280;">-- BigQuery: STRUCT and ARRAY nested data types</span>
<span style="color:#c4b5fd;">SELECT</span>
    customer_id,
    address.city,
    address.country,
    item.product_id,
    item.quantity
<span style="color:#c4b5fd;">FROM</span> orders,
<span style="color:#93c5fd;">UNNEST</span>(order_items) <span style="color:#c4b5fd;">AS</span> item;

<span style="color:#6b7280;">-- BigQuery: Partition by ingestion date (auto-partitioned)</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> events_partitioned
<span style="color:#c4b5fd;">PARTITION BY</span> <span style="color:#93c5fd;">DATE</span>(event_timestamp)
<span style="color:#c4b5fd;">CLUSTER BY</span> user_id, event_type
<span style="color:#c4b5fd;">AS SELECT</span> * <span style="color:#c4b5fd;">FROM</span> raw_events;

<span style="color:#6b7280;">-- ── REDSHIFT FEATURES ────────────────────────────────────────</span>

<span style="color:#6b7280;">-- Redshift: Distribution styles for JOIN optimisation</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> fact_sales (
    sale_key     <span style="color:#a7f3d0;">BIGINT</span>,
    customer_key <span style="color:#a7f3d0;">INT</span>,
    net_revenue  <span style="color:#a7f3d0;">DECIMAL(12,2)</span>
) DISTKEY(customer_key)    <span style="color:#6b7280;">-- co-locate with dim_customer</span>
  SORTKEY(date_key);       <span style="color:#6b7280;">-- sort for date range queries</span></div>
  </div>
</div>

<h3>The Separation of Storage and Compute</h3>
<p>All three platforms physically separate storage from compute. Data lives in object storage (S3 for AWS/Snowflake, Google Cloud Storage for BigQuery) while compute nodes are spun up on demand to process queries. This means you pay for storage continuously but pay for compute only when queries run — a massive cost advantage over traditional on-premises warehouses that required expensive hardware to sit idle overnight.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.7 Cloud Data Warehouses: Snowflake, BigQuery, Redshift',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L23_7', [
                ['q' => 'BigQuery charges per TB of data scanned rather than per compute node-hour. What is the implication for query design?', 'opts' => ['Queries should always use SELECT * to avoid recomputing column selections', 'Query designers should minimise bytes scanned by using partitioning, clustering, and selecting only needed columns — every unnecessary column or unpartitioned scan costs money', 'Partitioning is irrelevant since billing is per TB regardless', 'BigQuery caches all query results so repeated queries are free'], 'ans' => 1, 'exp' => 'BigQuery\'s per-TB pricing model makes I/O reduction directly measurable in dollars. A SELECT * on a 10TB table costs the same whether you need 1 column or 50. Using SELECT only needed columns, date partitioning (to skip partitions), and clustering (to allow block pruning) directly reduces the billed TB and therefore the cost.'],
                ['q' => 'What is Snowflake\'s "Time Travel" feature and what problem does it solve?', 'opts' => ['It stores data in multiple time zones simultaneously', 'It allows querying data as it existed at a specific past timestamp — enabling recovery from accidental DML errors without restoring backups', 'It automatically partitions tables by time for query performance', 'It replicates data across geographic regions for disaster recovery'], 'ans' => 1, 'exp' => 'Snowflake Time Travel lets you query historical states of a table: SELECT * FROM my_table AT (TIMESTAMP => \'yesterday\'). If someone accidentally truncates a table or runs a wrong UPDATE, you can recover data by selecting from the table at a time before the error — no backup restore required. Time Travel windows are configurable from 0 to 90 days.'],
                ['q' => 'In Redshift, what is the purpose of the DISTKEY and SORTKEY table properties?', 'opts' => ['DISTKEY encrypts data for security; SORTKEY controls access permissions', 'DISTKEY controls how rows are distributed across compute nodes (to co-locate JOINed tables); SORTKEY physically orders rows on disk to accelerate range-scan queries', 'DISTKEY sets the primary key; SORTKEY sets the secondary index', 'Both properties are deprecated in Redshift RA3 and no longer needed'], 'ans' => 1, 'exp' => 'DISTKEY determines which column is used to distribute rows across Redshift nodes. Distributing fact and dimension tables on the same join key (e.g., customer_key) co-locates related rows on the same node, minimising inter-node data movement during JOINs. SORTKEY physically orders data on disk — queries with ORDER BY or range filters on the sort key read far fewer disk blocks.'],
                ['q' => 'Why does Snowflake\'s idle cost approach zero while an on-premises warehouse has full idle cost?', 'opts' => ['Snowflake uses more efficient processors that consume less power', 'Snowflake Virtual Warehouses can be suspended in seconds — compute billing stops immediately; storage (S3) continues at a fraction of the cost', 'Snowflake pre-negotiates fixed low rates with cloud providers', 'Idle Snowflake warehouses are automatically shared with other customers'], 'ans' => 1, 'exp' => 'On-premises hardware is a sunk cost — it consumes power and rack space whether or not it is processing queries. Snowflake separates compute from storage: when no queries are running, the Virtual Warehouse is suspended (in ~30 seconds) and billing stops. Only object storage (pennies per GB/month) continues. This makes Snowflake dramatically cheaper for bursty analytical workloads.'],
                ['q' => 'A startup uses BigQuery and receives a surprise $15,000 bill because an analyst ran SELECT * FROM a 50TB table. What is the best preventive control?', 'opts' => ['Disable all analyst access to BigQuery until further notice', 'Enable BigQuery custom cost controls and query quotas — set per-query byte limits so expensive queries are rejected before they scan expensive data', 'Switch to Snowflake which does not charge for data scanned', 'Require all queries to be reviewed by a DBA before execution'], 'ans' => 1, 'exp' => 'BigQuery provides custom quotas and cost controls at the project, dataset, and user level. You can set a maximum bytes billed per query — if a query would scan more than the limit, it is rejected before scanning a single byte. Additionally, table previews, partition filters, and dbt models with SELECT column lists prevent accidental full scans.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.8 — Data Marts and the Kimball Bus Architecture
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Data Marts and the Kimball Bus Architecture</h2>
<p>As data warehouses grow to serve multiple business units — Sales, Finance, Marketing, Operations, HR — a single monolithic schema becomes unwieldy. Different teams have different data needs, different access rights, and different query patterns. <strong>Data marts</strong> are subject-specific, smaller repositories derived from the enterprise data warehouse, designed to serve a particular business domain or team. The <strong>Kimball Bus Architecture</strong> provides the framework for integrating multiple data marts into a coherent enterprise warehouse without sacrificing consistency.</p>

<h3>What Is a Data Mart?</h3>
<p>A data mart is a focused subset of a data warehouse tailored to a specific business function. It may contain only the tables, columns, and time ranges relevant to that function. Data marts can be physical (separate databases with ETL-loaded copies) or logical (views or virtual schemas on top of the central warehouse). Modern cloud warehouses make logical data marts trivially easy through schema separation and role-based access control.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Creating Logical Data Marts with Views</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Enterprise DW schema: sales, finance, marketing all in one place

-- ── SALES DATA MART ──────────────────────────────────────────
-- Tailored for the sales team: includes performance metrics
-- for products, stores, regions. PII columns excluded.</span>
<span style="color:#c4b5fd;">CREATE VIEW</span> mart_sales.monthly_product_performance <span style="color:#c4b5fd;">AS</span>
<span style="color:#c4b5fd;">SELECT</span>
    d.year, d.month_name, d.quarter,
    p.category, p.subcategory, p.brand, p.product_name,
    s.region, s.country, s.store_type,
    <span style="color:#93c5fd;">SUM</span>(f.net_revenue)     <span style="color:#c4b5fd;">AS</span> revenue,
    <span style="color:#93c5fd;">SUM</span>(f.quantity_sold)   <span style="color:#c4b5fd;">AS</span> units,
    <span style="color:#93c5fd;">SUM</span>(f.gross_profit)    <span style="color:#c4b5fd;">AS</span> gross_profit,
    <span style="color:#93c5fd;">COUNT</span>(<span style="color:#c4b5fd;">DISTINCT</span> f.customer_key) <span style="color:#c4b5fd;">AS</span> unique_buyers
<span style="color:#c4b5fd;">FROM</span>  fact_sales f
<span style="color:#c4b5fd;">JOIN</span>  dim_date    d <span style="color:#c4b5fd;">ON</span> f.date_key    = d.date_key
<span style="color:#c4b5fd;">JOIN</span>  dim_product p <span style="color:#c4b5fd;">ON</span> f.product_key = p.product_key
<span style="color:#c4b5fd;">JOIN</span>  dim_store   s <span style="color:#c4b5fd;">ON</span> f.store_key   = s.store_key
<span style="color:#c4b5fd;">GROUP BY</span> <span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">4</span>,<span style="color:#fcd34d;">5</span>,<span style="color:#fcd34d;">6</span>,<span style="color:#fcd34d;">7</span>,<span style="color:#fcd34d;">8</span>,<span style="color:#fcd34d;">9</span>,<span style="color:#fcd34d;">10</span>;

<span style="color:#6b7280;">-- ── FINANCE DATA MART ─────────────────────────────────────────
-- Finance team: full P&amp;L figures, fiscal periods, cost data</span>
<span style="color:#c4b5fd;">CREATE VIEW</span> mart_finance.pl_summary <span style="color:#c4b5fd;">AS</span>
<span style="color:#c4b5fd;">SELECT</span>
    d.fiscal_period,
    d.year, d.quarter,
    p.category,
    <span style="color:#93c5fd;">SUM</span>(f.net_revenue)                    <span style="color:#c4b5fd;">AS</span> net_revenue,
    <span style="color:#93c5fd;">SUM</span>(f.cost_of_goods)                  <span style="color:#c4b5fd;">AS</span> cogs,
    <span style="color:#93c5fd;">SUM</span>(f.gross_profit)                   <span style="color:#c4b5fd;">AS</span> gross_profit,
    <span style="color:#93c5fd;">SUM</span>(f.gross_profit) / <span style="color:#93c5fd;">NULLIF</span>(<span style="color:#93c5fd;">SUM</span>(f.net_revenue), <span style="color:#fcd34d;">0</span>) <span style="color:#c4b5fd;">AS</span> gp_margin
<span style="color:#c4b5fd;">FROM</span>  fact_sales f
<span style="color:#c4b5fd;">JOIN</span>  dim_date    d <span style="color:#c4b5fd;">ON</span> f.date_key    = d.date_key
<span style="color:#c4b5fd;">JOIN</span>  dim_product p <span style="color:#c4b5fd;">ON</span> f.product_key = p.product_key
<span style="color:#c4b5fd;">GROUP BY</span> <span style="color:#fcd34d;">1</span>,<span style="color:#fcd34d;">2</span>,<span style="color:#fcd34d;">3</span>,<span style="color:#fcd34d;">4</span>;

<span style="color:#6b7280;">-- Grant mart access selectively — sales team cannot see finance mart</span>
<span style="color:#c4b5fd;">GRANT</span> <span style="color:#c4b5fd;">SELECT</span> <span style="color:#c4b5fd;">ON</span> mart_sales.*   <span style="color:#c4b5fd;">TO</span> ROLE sales_analyst;
<span style="color:#c4b5fd;">GRANT</span> <span style="color:#c4b5fd;">SELECT</span> <span style="color:#c4b5fd;">ON</span> mart_finance.* <span style="color:#c4b5fd;">TO</span> ROLE finance_analyst;</div>
  </div>
</div>

<h3>The Kimball Bus Architecture and Conformed Dimensions</h3>
<p>The <strong>Bus Architecture</strong> solves the integration problem: how can multiple data marts from different business units produce consistent reports when combined? The answer is <strong>conformed dimensions</strong> — dimension tables (like dim_date, dim_customer, dim_product) that have identical meaning and content across every data mart that uses them.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">CONCEPT — Conformed Dimensions: The Integration Glue</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># The "Data Warehouse Bus Matrix" maps which dimensions each fact
# table uses. Shared dimensions are CONFORMED — identical definitions.</span>

<span style="color:#fcd34d;">                dim_date  dim_customer  dim_product  dim_store  dim_employee</span>
fact_sales         ✓           ✓             ✓           ✓
fact_inventory     ✓                         ✓           ✓
fact_web_sessions  ✓           ✓
fact_hr_payroll    ✓                                                  ✓
fact_marketing     ✓           ✓             ✓

<span style="color:#a7f3d0;">CONFORMED DIMENSION RULE:</span>
  dim_date in fact_sales must be IDENTICAL to dim_date in fact_inventory.
  Same surrogate keys, same attributes, same grain.
  Then this cross-mart query is valid and produces consistent results:

  SELECT d.month_name, s.revenue, i.units_on_hand
  FROM   mart_sales.monthly_revenue s
  JOIN   mart_inventory.eom_stock   i USING (date_key, product_key)
  JOIN   dim_date d USING (date_key)

  If dim_date differs between marts → this JOIN produces nonsense.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.8 Data Marts and the Kimball Bus Architecture',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L23_8', [
                ['q' => 'What is the primary purpose of a data mart?', 'opts' => ['To replace the enterprise data warehouse with a smaller, faster system', 'To provide a subject-specific, focused subset of warehouse data tailored to a particular business function or team', 'To store raw, unprocessed data before ETL transformation', 'To create real-time replicas of OLTP systems for analytical use'], 'ans' => 1, 'exp' => 'A data mart serves a specific business domain — Sales, Finance, Marketing, HR. It contains only the tables, columns, aggregation levels, and time ranges relevant to that domain. This reduces complexity for end users, improves query performance (smaller scope), and allows domain-specific access controls.'],
                ['q' => 'What is a "conformed dimension" in the Kimball Bus Architecture?', 'opts' => ['A dimension that is automatically updated by the ETL process', 'A dimension table with identical keys, attributes, and definitions shared across multiple fact tables and data marts — enabling cross-mart queries', 'A dimension that conforms to third normal form for storage efficiency', 'A dimension that is partitioned by date for query performance'], 'ans' => 1, 'exp' => 'Conformed dimensions are the "glue" of the Bus Architecture. dim_date in the sales mart and dim_date in the inventory mart must have identical surrogate keys and attribute definitions. Then the same date_key = 20240115 means the same day in both marts, enabling valid cross-mart JOIN queries like comparing sales revenue to inventory levels by date.'],
                ['q' => 'The mart_finance.pl_summary view uses NULLIF(SUM(net_revenue), 0) in the margin calculation. Why?', 'opts' => ['NULLIF improves query performance for aggregate functions', 'To prevent division by zero — if net_revenue is 0, NULLIF returns NULL rather than causing an arithmetic error, and the ratio becomes NULL instead of crashing', 'NULLIF converts the revenue column from VARCHAR to DECIMAL', 'To filter out months with zero revenue from the result set'], 'ans' => 1, 'exp' => 'Division by zero causes a runtime error in SQL. NULLIF(SUM(net_revenue), 0) returns NULL when the denominator is 0, and NULL/anything = NULL — not an error. The margin column shows NULL for rows with zero revenue rather than crashing. This is a standard defensive pattern for any calculated ratio or percentage.'],
                ['q' => 'A sales analyst reports that "our monthly customer count" is different in the sales mart vs the marketing mart. What is likely the cause?', 'opts' => ['The sales mart has more recent data than the marketing mart', 'The dim_customer table is not conformed — the two marts use different definitions of "customer" or different dimension surrogate keys, producing inconsistent results', 'The sales mart uses a star schema while the marketing mart uses snowflake', 'The two marts are querying different fact tables with different grain'], 'ans' => 1, 'exp' => 'This is the classic symptom of non-conformed dimensions. If sales mart defines "customer" as anyone with a purchase and marketing defines "customer" as anyone who signed up (including non-purchasers), the counts will differ for the same period. Conformed dimensions require a single agreed definition enforced through shared dimension tables with identical keys.'],
                ['q' => 'A company adds a new dim_campaign table used only by fact_marketing. Does this require changes to other data marts?', 'opts' => ['Yes — every data mart must be updated to reference the new dimension', 'No — dim_campaign is a new dimension with no existing references; other fact tables and marts are completely unaffected', 'Yes — the bus matrix must be fully rebuilt to accommodate the new column', 'Only if fact_sales also records campaign attribution'], 'ans' => 1, 'exp' => 'Adding a new dimension to the bus matrix only affects fact tables that reference it. dim_campaign is used by fact_marketing — its creation requires no changes to fact_sales, fact_inventory, or their associated data marts. This independence is a key benefit of the dimensional modelling approach: new data subjects are additive, not disruptive.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.9 — dbt: Data Build Tool for Warehouse Transformations
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>dbt: The Data Build Tool for Warehouse Transformations</h2>
<p><strong>dbt (data build tool)</strong> has become the standard tool for the transformation layer in modern ELT pipelines. Where traditional ETL tools (SSIS, Informatica) use proprietary GUIs and store transformation logic in opaque binary formats, dbt keeps all transformations as plain <strong>SQL SELECT statements</strong> in version-controlled files. dbt compiles those SELECT statements into CREATE TABLE or CREATE VIEW statements and executes them in your warehouse. The result: transformations that are readable, testable, documented, and managed with the same rigour as application code.</p>

<h3>dbt Project Structure</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">STRUCTURE — A dbt Project Layout</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;">my_dbt_project/
├── <span style="color:#c4b5fd;">dbt_project.yml</span>        <span style="color:#6b7280;"># project config: name, version, model paths</span>
├── <span style="color:#93c5fd;">profiles.yml</span>           <span style="color:#6b7280;"># warehouse connection credentials</span>
├── models/
│   ├── staging/           <span style="color:#6b7280;"># 1:1 mapping from source tables, minimal transforms</span>
│   │   ├── stg_orders.sql
│   │   ├── stg_customers.sql
│   │   └── schema.yml     <span style="color:#6b7280;"># source freshness + column documentation</span>
│   ├── intermediate/      <span style="color:#6b7280;"># business logic joins, not exposed to BI tools</span>
│   │   └── int_customer_orders.sql
│   └── marts/             <span style="color:#6b7280;"># final models consumed by BI tools and analysts</span>
│       ├── sales/
│       │   ├── fct_sales.sql
│       │   └── dim_customer.sql
│       └── finance/
│           └── fct_revenue_daily.sql
├── tests/                 <span style="color:#6b7280;"># custom SQL data quality tests</span>
│   └── assert_revenue_positive.sql
├── macros/                <span style="color:#6b7280;"># reusable Jinja SQL snippets</span>
│   └── generate_surrogate_key.sql
└── seeds/                 <span style="color:#6b7280;"># small static CSV files (lookup tables)</span>
    └── country_codes.csv</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL (dbt) — Model Files with Refs and Tests</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- models/staging/stg_orders.sql
-- Staging model: rename columns, cast types, add basic filters</span>
{{
  config(
    materialized = <span style="color:#a7f3d0;">'view'</span>   <span style="color:#6b7280;">-- run as a view — no storage cost</span>
  )
}}

<span style="color:#c4b5fd;">SELECT</span>
    order_id                                         <span style="color:#c4b5fd;">AS</span> order_id,
    customer_id                                      <span style="color:#c4b5fd;">AS</span> customer_id,
    <span style="color:#93c5fd;">CAST</span>(order_date <span style="color:#c4b5fd;">AS</span> <span style="color:#a7f3d0;">DATE</span>)                         <span style="color:#c4b5fd;">AS</span> order_date,
    <span style="color:#93c5fd;">UPPER</span>(<span style="color:#93c5fd;">TRIM</span>(status))                              <span style="color:#c4b5fd;">AS</span> status,
    <span style="color:#93c5fd;">ROUND</span>(<span style="color:#93c5fd;">CAST</span>(total_amount <span style="color:#c4b5fd;">AS</span> <span style="color:#a7f3d0;">DECIMAL(12,2)</span>), <span style="color:#fcd34d;">2</span>)  <span style="color:#c4b5fd;">AS</span> total_amount,
    _loaded_at                                       <span style="color:#c4b5fd;">AS</span> source_loaded_at
<span style="color:#c4b5fd;">FROM</span> {{ source(<span style="color:#a7f3d0;">'raw'</span>, <span style="color:#a7f3d0;">'orders'</span>) }}             <span style="color:#6b7280;">-- reference to raw source table</span>
<span style="color:#c4b5fd;">WHERE</span> order_date >= <span style="color:#a7f3d0;">'2020-01-01'</span>               <span style="color:#6b7280;">-- exclude pre-2020 test data</span>


<span style="color:#6b7280;">-- models/marts/sales/fct_sales.sql
-- Fact model: references staging models, computes metrics</span>
{{
  config(
    materialized = <span style="color:#a7f3d0;">'incremental'</span>,          <span style="color:#6b7280;">-- only load new rows each run</span>
    unique_key   = <span style="color:#a7f3d0;">'sale_key'</span>,
    on_schema_change = <span style="color:#a7f3d0;">'sync_all_columns'</span>
  )
}}

<span style="color:#c4b5fd;">SELECT</span>
    {{ dbt_utils.generate_surrogate_key([<span style="color:#a7f3d0;">'o.order_id'</span>, <span style="color:#a7f3d0;">'oi.line_num'</span>]) }}
                                               <span style="color:#c4b5fd;">AS</span> sale_key,
    d.date_key,
    c.customer_key,
    p.product_key,
    oi.quantity,
    oi.unit_price,
    oi.quantity * oi.unit_price               <span style="color:#c4b5fd;">AS</span> gross_revenue,
    oi.discount_amount,
    oi.quantity * oi.unit_price - oi.discount_amount <span style="color:#c4b5fd;">AS</span> net_revenue
<span style="color:#c4b5fd;">FROM</span>   {{ ref(<span style="color:#a7f3d0;">'stg_order_items'</span>) }} oi      <span style="color:#6b7280;">-- ref() builds the DAG automatically</span>
<span style="color:#c4b5fd;">JOIN</span>   {{ ref(<span style="color:#a7f3d0;">'stg_orders'</span>)      }} o  <span style="color:#c4b5fd;">ON</span> oi.order_id    = o.order_id
<span style="color:#c4b5fd;">JOIN</span>   {{ ref(<span style="color:#a7f3d0;">'dim_customer'</span>)    }} c  <span style="color:#c4b5fd;">ON</span> o.customer_id  = c.customer_id
<span style="color:#c4b5fd;">JOIN</span>   {{ ref(<span style="color:#a7f3d0;">'dim_product'</span>)     }} p  <span style="color:#c4b5fd;">ON</span> oi.product_id  = p.product_id
<span style="color:#c4b5fd;">JOIN</span>   {{ ref(<span style="color:#a7f3d0;">'dim_date'</span>)        }} d  <span style="color:#c4b5fd;">ON</span> o.order_date   = d.full_date
{% <span style="color:#c4b5fd;">if</span> is_incremental() %}
<span style="color:#c4b5fd;">WHERE</span>  o.order_date > (<span style="color:#c4b5fd;">SELECT</span> <span style="color:#93c5fd;">MAX</span>(order_date) <span style="color:#c4b5fd;">FROM</span> {{ this }})
{% endif %}                                    <span style="color:#6b7280;">-- only load new data on incremental runs</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">dbt Commands</span>
dbt run          # compile and execute all models
dbt test         # run data quality tests (not null, unique, ref integrity)
dbt docs generate # auto-generate data documentation site
dbt run --select fct_sales  # run one model and its dependencies</div>
  </div>
</div>

<h3>dbt Tests: Built-in Data Quality</h3>
<p>dbt ships with four built-in generic tests that apply to any column: <code>not_null</code>, <code>unique</code>, <code>accepted_values</code>, and <code>relationships</code> (referential integrity). These are declared in YAML schema files — no custom SQL required. Combined with custom SQL tests in the tests/ folder and third-party packages like dbt-expectations, dbt provides a comprehensive data quality testing framework that runs as part of every deployment.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.9 dbt: The Data Build Tool',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L23_9', [
                ['q' => 'What does the {{ ref(\'stg_orders\') }} function do in a dbt model?', 'opts' => ['It imports the stg_orders Python function into the SQL context', 'It creates a dependency on the stg_orders model and resolves to its compiled table/view name — dbt uses this to build a directed acyclic graph (DAG) of model dependencies and executes them in correct order', 'It creates a physical copy of the stg_orders table in the target schema', 'It runs the stg_orders model inline as a CTE'], 'ans' => 1, 'exp' => 'ref() serves two purposes: it resolves to the correct fully-qualified table name (schema.model_name) in the target environment, and it tells dbt that this model depends on stg_orders. dbt uses all ref() calls to build a DAG and guarantees that stg_orders is built before any model that refs it — automatic dependency management without manual configuration.'],
                ['q' => 'A dbt model is configured with materialized=\'incremental\'. What happens differently compared to materialized=\'table\'?', 'opts' => ['Incremental models run faster because they use views instead of tables', 'Incremental models only process and load new or changed rows since the last run — not the full dataset — using the high-watermark pattern', 'Incremental models cannot be joined to other models', 'Incremental models are run in parallel across multiple compute clusters'], 'ans' => 1, 'exp' => 'An incremental model checks the current max value of a date or ID column in the target table ({{ this }}), then only processes source rows newer than that watermark. A table with 5 billion rows is rebuilt from scratch as a \'table\' model — expensive nightly. As an \'incremental\' model, only yesterday\'s 1 million new rows are processed, completing in minutes.'],
                ['q' => 'What does dbt test do and which four generic tests are built in?', 'opts' => ['It validates SQL syntax only; no built-in tests exist', 'It runs data quality assertions against model outputs — built-in tests: not_null, unique, accepted_values, relationships (referential integrity)', 'It tests query performance by measuring execution time', 'It validates that model names follow naming conventions'], 'ans' => 1, 'exp' => 'dbt test compiles and runs SQL assertions against the data in your warehouse. The four built-in generic tests check: not_null (column has no NULL values), unique (column values are unique), accepted_values (column only contains specified values), and relationships (foreign key exists in the referenced table). Failures are reported so engineers can fix data issues before they reach BI dashboards.'],
                ['q' => 'Why does dbt use plain SQL SELECT statements rather than a proprietary transformation language?', 'opts' => ['SQL SELECT is faster than proprietary transformation tools', 'SQL SELECT is universally understood, version-controllable, testable, and runs natively in the warehouse — no separate compute engine needed; any SQL developer can read and maintain the transformations', 'SQL SELECT statements cannot express complex transformations like joins and aggregations', 'dbt uses a proprietary Python-based language that is compiled to SQL'], 'ans' => 1, 'exp' => 'dbt\'s core philosophy is "transformations as SQL SELECT statements." This means transformations are readable by any SQL developer, stored in Git (version control, code review, CI/CD), tested with standard SQL assertions, and executed by the warehouse itself — leveraging its distributed compute. There is no transformation infrastructure to maintain separately from the warehouse.'],
                ['q' => 'A dbt model fails in production with "column net_revenue cannot be null." Which dbt test would have caught this before deployment?', 'opts' => ['accepted_values: net_revenue: [0, 1]', 'unique: net_revenue', 'not_null: net_revenue', 'relationships: net_revenue to fact_sales.net_revenue'], 'ans' => 2, 'exp' => 'The not_null test asserts that a column contains no NULL values. Configuring not_null on net_revenue in the model\'s schema.yml would run a SELECT COUNT(*) ... WHERE net_revenue IS NULL assertion after every dbt run. If any NULLs exist, the test fails and the deployment pipeline is halted — preventing bad data from reaching downstream BI dashboards.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.10 — Data Quality, Governance, and the Modern Data Stack
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>Data Quality, Governance, and the Modern Data Stack</h2>
<p>A data warehouse is only as valuable as the data inside it. The most sophisticated star schema and fastest columnar storage are worthless if the underlying data is inaccurate, incomplete, stale, or inconsistently defined. <strong>Data quality</strong> — systematically measuring, monitoring, and enforcing the accuracy and completeness of warehouse data — is not a nice-to-have afterthought. It is the engineering discipline that determines whether executives trust the numbers and whether analysts spend their time generating insights or chasing data errors.</p>

<h3>The Six Dimensions of Data Quality</h3>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">FRAMEWORK — Six Dimensions of Data Quality</span>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">COMPLETENESS:</span>   Are all expected records present? Are required fields populated?
                 Test: COUNT(*) matches source; NULLs in NOT NULL columns = 0.

<span style="color:#93c5fd;">ACCURACY:</span>       Does the data correctly reflect reality?
                 Test: Revenue in DW matches GL system; row counts match OLTP.

<span style="color:#a7f3d0;">CONSISTENCY:</span>    Are values consistent across systems and time periods?
                 Test: Total DW sales = total CRM sales = total ERP invoices.

<span style="color:#fcd34d;">TIMELINESS:</span>     Is data available when users expect it?
                 Test: fact_sales.max(date_key) >= yesterday's date by 06:00.

<span style="color:#fca5a5;">UNIQUENESS:</span>     Are there unexpected duplicate records?
                 Test: COUNT(DISTINCT sale_key) = COUNT(*) on fact_sales.

<span style="color:#c4b5fd;">VALIDITY:</span>       Do values conform to domain rules and business constraints?
                 Test: net_revenue >= 0 (no negative sales without a flag);
                       date_key BETWEEN 20000101 AND 20991231.</div>
  </div>
</div>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Automated Data Quality Checks</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#93c5fd;">import</span> pandas <span style="color:#93c5fd;">as</span> pd
<span style="color:#93c5fd;">import</span> numpy  <span style="color:#93c5fd;">as</span> np
<span style="color:#93c5fd;">from</span> datetime <span style="color:#93c5fd;">import</span> date

<span style="color:#c4b5fd;">class</span> <span style="color:#fcd34d;">DataQualityChecker</span>:
    <span style="color:#a7f3d0;">"""Automated DQ checks against a warehouse table (or DataFrame)."""</span>

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">__init__</span>(self, df: pd.DataFrame, table_name: str):
        self.df   = df
        self.name = table_name
        self.results = []

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">check_not_null</span>(self, columns):
        <span style="color:#c4b5fd;">for</span> col <span style="color:#c4b5fd;">in</span> columns:
            null_count = self.df[col].isna().sum()
            status = <span style="color:#a7f3d0;">'PASS'</span> <span style="color:#c4b5fd;">if</span> null_count == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'FAIL'</span>
            self.results.append({<span style="color:#a7f3d0;">'check'</span>: <span style="color:#a7f3d0;">f'not_null:{col}'</span>,
                                  <span style="color:#a7f3d0;">'status'</span>: status, <span style="color:#a7f3d0;">'detail'</span>: null_count})

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">check_unique</span>(self, columns):
        <span style="color:#c4b5fd;">for</span> col <span style="color:#c4b5fd;">in</span> columns:
            dups = self.df[col].duplicated().sum()
            self.results.append({<span style="color:#a7f3d0;">'check'</span>: <span style="color:#a7f3d0;">f'unique:{col}'</span>,
                                  <span style="color:#a7f3d0;">'status'</span>: <span style="color:#a7f3d0;">'PASS'</span> <span style="color:#c4b5fd;">if</span> dups == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'FAIL'</span>,
                                  <span style="color:#a7f3d0;">'detail'</span>: dups})

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">check_row_count</span>(self, expected_min):
        n = len(self.df)
        self.results.append({<span style="color:#a7f3d0;">'check'</span>: <span style="color:#a7f3d0;">'row_count'</span>,
                              <span style="color:#a7f3d0;">'status'</span>: <span style="color:#a7f3d0;">'PASS'</span> <span style="color:#c4b5fd;">if</span> n >= expected_min <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'FAIL'</span>,
                              <span style="color:#a7f3d0;">'detail'</span>: n})

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">check_positive</span>(self, column):
        neg = (self.df[column] < <span style="color:#fcd34d;">0</span>).sum()
        self.results.append({<span style="color:#a7f3d0;">'check'</span>: <span style="color:#a7f3d0;">f'positive:{column}'</span>,
                              <span style="color:#a7f3d0;">'status'</span>: <span style="color:#a7f3d0;">'PASS'</span> <span style="color:#c4b5fd;">if</span> neg == <span style="color:#fcd34d;">0</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'FAIL'</span>,
                              <span style="color:#a7f3d0;">'detail'</span>: neg})

    <span style="color:#c4b5fd;">def</span> <span style="color:#93c5fd;">report</span>(self):
        <span style="color:#c4b5fd;">for</span> r <span style="color:#c4b5fd;">in</span> self.results:
            icon = <span style="color:#a7f3d0;">'✓'</span> <span style="color:#c4b5fd;">if</span> r[<span style="color:#a7f3d0;">'status'</span>] == <span style="color:#a7f3d0;">'PASS'</span> <span style="color:#c4b5fd;">else</span> <span style="color:#a7f3d0;">'✗'</span>
            <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"[{r['status']}] {icon} {r['check']:<30} detail={r['detail']}"</span>)

<span style="color:#6b7280;"># Simulate a fact_sales DataFrame with injected quality issues</span>
fact_sales = pd.DataFrame({
    <span style="color:#a7f3d0;">'sale_key'</span>:    [<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">5</span>],    <span style="color:#6b7280;"># duplicate key 3!</span>
    <span style="color:#a7f3d0;">'date_key'</span>:    [<span style="color:#fcd34d;">20240101</span>, <span style="color:#fcd34d;">20240101</span>, <span style="color:#fcd34d;">20240102</span>, <span style="color:#fcd34d;">20240102</span>, <span style="color:#fca5a5;">None</span>],
    <span style="color:#a7f3d0;">'net_revenue'</span>: [<span style="color:#fcd34d;">59.98</span>, <span style="color:#fcd34d;">149.50</span>, <span style="color:#fcd34d;">89.00</span>, <span style="color:#fcd34d;">267.00</span>, -<span style="color:#fcd34d;">5.00</span>],   <span style="color:#6b7280;"># negative!</span>
})

dq = DataQualityChecker(fact_sales, <span style="color:#a7f3d0;">'fact_sales'</span>)
dq.check_not_null([<span style="color:#a7f3d0;">'sale_key'</span>, <span style="color:#a7f3d0;">'date_key'</span>])
dq.check_unique([<span style="color:#a7f3d0;">'sale_key'</span>])
dq.check_row_count(expected_min=<span style="color:#fcd34d;">4</span>)
dq.check_positive(<span style="color:#a7f3d0;">'net_revenue'</span>)
dq.report()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>[PASS] ✓ not_null:sale_key                detail=0
[FAIL] ✗ not_null:date_key                detail=1
[FAIL] ✗ unique:sale_key                  detail=1
[PASS] ✓ row_count                        detail=5
[FAIL] ✗ positive:net_revenue             detail=1</div>
  </div>
</div>

<h3>Data Governance: People, Process, Policy</h3>
<p>Data governance is the framework of policies, roles, and processes that ensure data assets are managed consistently, securely, and in alignment with business objectives and regulatory requirements. A <strong>Data Catalogue</strong> (Alation, Collibra, dbt Docs) documents what every table and column means — the business definitions, owners, lineage, and quality metrics. A <strong>Data Steward</strong> is the business owner responsible for a domain's data quality. Without governance, even a technically excellent warehouse degrades into an untrustworthy "data swamp" as undocumented tables accumulate and definitions drift.</p>
HTML;

        Lesson::create([
            'module_id'   => $module->id,
            'title'       => '23.10 Data Quality, Governance, and the Modern Data Stack',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L23_10', [
                ['q' => 'The DataQualityChecker finds 1 duplicate in sale_key and 1 negative net_revenue. Which quality dimensions do these violations represent?', 'opts' => ['Timeliness and Completeness', 'Uniqueness (duplicate key) and Validity (negative revenue violates business rule)', 'Accuracy and Consistency', 'Completeness and Accuracy'], 'ans' => 1, 'exp' => 'Duplicate primary keys violate Uniqueness — every fact row should have a unique identifier. Negative net_revenue (without a returns flag) violates Validity — a business rule states net_revenue must be >= 0 for non-return transactions. Each quality dimension maps to a specific type of check that can be automated.'],
                ['q' => 'What is a Data Catalogue and why is it essential for warehouse governance?', 'opts' => ['A list of all database tables sorted alphabetically for easy lookup', 'A searchable repository documenting every table and column with business definitions, ownership, lineage, usage statistics, and quality metrics — preventing the warehouse from becoming an undocumented "data swamp"', 'A backup system that catalogues all ETL job configurations', 'A real-time monitoring dashboard for ETL pipeline health'], 'ans' => 1, 'exp' => 'Without a data catalogue, analysts spend enormous time asking "what does this column mean?" and "who owns this table?" Undocumented tables accumulate; definitions drift; the same metric gets calculated differently by different teams. A catalogue (Alation, Collibra, or dbt Docs) provides a single source of truth for data definitions, preventing the warehouse from degrading into an unusable mess.'],
                ['q' => 'A timeliness check fails: fact_sales.max(date_key) shows yesterday\'s date but the ETL should have loaded today\'s data by 6am. What is the most likely cause?', 'opts' => ['The date dimension does not include today\'s date', 'The ETL pipeline failed, ran late, or the source system had a data delivery delay — today\'s data has not been loaded yet', 'The fact table is using SCD Type 1 which overwrites date_key', 'The columnar storage compression delayed the data load'], 'ans' => 1, 'exp' => 'Timeliness measures whether data is available when promised. max(date_key) < today means no data for today was loaded. Root causes: ETL job failure (check scheduler logs), source system delay (the OLTP system did not export today\'s data yet), or network/connectivity issue. Automated timeliness alerts catch this before analysts start their morning with stale dashboards.'],
                ['q' => 'A company has two teams calculating "monthly active customers" — Sales gets 45,000 and Marketing gets 52,000 for the same month. What governance problem does this illustrate?', 'opts' => ['The fact_sales table has incorrect row counts', 'Lack of a conformed definition — "active customer" is defined differently (e.g., made a purchase vs logged into the app), and without a data catalogue enforcing a single standard, each team uses their own definition', 'The date dimension is not partitioned correctly by month', 'The ETL loaded different data volumes to each team\'s mart'], 'ans' => 1, 'exp' => 'This is the classic "multiple versions of the truth" problem that data governance exists to solve. Without a governed, single definition of "active customer" documented in a data catalogue and enforced through a single certified metric in the warehouse, different teams will compute the same metric differently. The fix: define the metric once, document it, and ensure all teams use the same dbt model or view.'],
                ['q' => 'Which modern data stack component is responsible for orchestrating the execution order of dbt models, ETL jobs, and data quality checks?', 'opts' => ['dbt itself — it manages all orchestration internally', 'A workflow orchestrator such as Apache Airflow, Prefect, or Dagster — dbt handles transformation logic but an orchestrator schedules and coordinates the full pipeline', 'The cloud data warehouse — Snowflake and BigQuery include built-in schedulers', 'The data catalogue — it triggers ETL jobs when documentation is updated'], 'ans' => 1, 'exp' => 'dbt handles transformation logic (SQL models, tests, docs) but does not schedule jobs or coordinate with external systems (Fivetran loads, Airflow DAGs, API pulls). Orchestrators like Airflow, Prefect, or Dagster handle the full DAG of pipeline steps: trigger source extraction → wait for load → run dbt models → run DQ checks → send Slack alert if tests fail. This separation of concerns is standard in the modern data stack.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.11 — FINAL EXAM
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Inmon defines a data warehouse as time-variant. A sales analyst wants to know "what was our customer count by city in Q1 2022." Which system can answer this and which cannot?', 'opts' => ['Only OLTP can answer — it stores current customer addresses', 'Only a data warehouse can answer — it stores address history; the OLTP system overwrites with the current address, making 2022 analysis impossible', 'Both can answer equally accurately', 'Neither can answer without joining to an external archive'], 'ans' => 1, 'exp' => 'Time-variance is what makes this possible. The OLTP system stores the current address — a customer who moved from London to Manchester has only "Manchester." The warehouse (with SCD Type 2) stores both addresses with effective dates, so Q1 2022 sales can be correctly attributed to "London." Without time-variance, historical geographic analysis is fundamentally broken.'],
            ['q' => 'In a star schema, a fact_sales table has 2 billion rows and 6 columns: 4 foreign keys + 2 measures. What is the recommended approach for a query filtering by product_category?', 'opts' => ['Add product_category directly to fact_sales to avoid JOINs', 'JOIN to dim_product to get product_category — denormalising category into the fact table violates dimensional modelling principles and creates update anomalies', 'Use a subquery instead of a JOIN for better performance', 'Create a separate fact_sales_by_category table pre-aggregated'], 'ans' => 1, 'exp' => 'Descriptive attributes like product_category belong in dimension tables, not fact tables. Adding category to fact_sales would require updating 2 billion rows every time a product is re-categorised. The dimension table has only thousands of rows — updates are instant. JOINs between fact and dimension tables are the intended access pattern in dimensional modelling.'],
            ['q' => 'The ETL code uses pd.to_datetime(errors="coerce") on the sale_date column. TXN002 has date "15/01/2024" which pandas cannot parse with the default format. What happens?', 'opts' => ['The entire ETL job crashes with a ValueError', 'TXN002\'s date becomes NaT (Not a Time) — the pipeline continues; the NaT is then handled by the fillna step or flagged for review', 'The date is silently set to 1970-01-01 (Unix epoch)', 'The row is automatically dropped from the output DataFrame'], 'ans' => 1, 'exp' => 'errors="coerce" instructs pd.to_datetime() to return NaT for any value it cannot parse rather than raising an exception. This makes the ETL pipeline resilient to format inconsistencies — a single bad date does not halt an entire nightly load. The NaT can then be filled with a default date or flagged in a data quality report.'],
            ['q' => 'A customer changes city three times in 5 years. Which SCD type correctly preserves all three city versions for historical analysis?', 'opts' => ['SCD Type 1 — overwrite the city field each time', 'SCD Type 3 — add current_city and previous_city columns', 'SCD Type 2 — add a new dimension row for each change with effective/expiry dates', 'SCD Type 6 — store the initial value only'], 'ans' => 2, 'exp' => 'SCD Type 2 inserts a new dimension row for each change. Three city changes create four rows for the same customer: v1 (city A, active 2020-2022), v2 (city B, active 2022-2023), v3 (city C, active 2023-2024), v4 (city D, current). Each fact_sales row links via surrogate key to the version active at transaction time. Type 3 can only store one prior value — two changes are lost.'],
            ['q' => 'Why does the low-cardinality store_id column compress 12.5x better than the continuous net_revenue column in columnar storage?', 'opts' => ['Integer columns always compress better than decimal columns', 'Store_id has only 5 unique values across 1 million rows — extreme repetition that dictionary and run-length encoding exploit; net_revenue has near-unique float values with almost no repetition', 'The compression algorithm is tuned for ID columns specifically', 'net_revenue requires higher precision storage that cannot be compressed'], 'ans' => 1, 'exp' => 'Compression algorithms exploit repetition. With only 5 unique store IDs, a dictionary maps each ID to a 3-bit code — 1 million 3-bit codes compress to a tiny fraction of 1 million 32-bit integers. net_revenue values like 59.98, 149.50, 267.00 are all distinct — there is almost nothing to compress. Low cardinality = high compression.'],
            ['q' => 'A Data Vault hub_customer table has columns: customer_hk, customer_bk, load_date, record_source. What does record_source contain and why is it critical?', 'opts' => ['The surrogate key of the source system dimension', 'The name of the source system that delivered this record (e.g., "CRM_SALESFORCE") — enabling full data lineage and audit trail mandated by regulatory compliance', 'A hash of all customer attributes for change detection', 'The ETL job ID that loaded this row into the vault'], 'ans' => 1, 'exp' => 'record_source identifies which source system delivered each record. In regulated industries, auditors may ask "where did this customer record come from?" Record_source answers this definitively. Combined with load_date, every data point in the vault has a complete provenance trail — critical for GDPR data lineage, financial audit requirements, and debugging data discrepancies between sources.'],
            ['q' => 'BigQuery charges per TB scanned. A query SELECT * FROM fact_sales WHERE year = 2024 scans 10TB. What two techniques would reduce the cost most effectively?', 'opts' => ['Using LIMIT and OFFSET clauses to paginate results', 'Partitioning the table by date (skipping 9 years of partitions) and selecting only needed columns (not SELECT *) — reducing scanned bytes from 10TB to potentially 0.2TB', 'Caching the query result in a temporary table', 'Running the query at off-peak hours when rates are lower'], 'ans' => 1, 'exp' => 'Date partitioning with WHERE year=2024 prunes ~90% of partitions — from 10 years to 1. Column pruning by selecting only needed columns instead of SELECT * reduces scanned bytes by the fraction of needed columns (e.g., 4 of 50 columns = 92% reduction). Combined, a 10TB scan becomes ~0.08TB — a 125x cost reduction and proportional performance improvement.'],
            ['q' => 'In dbt, what is the difference between a model materialised as "view" vs "table"?', 'opts' => ['Views are faster than tables because they skip storage', 'A "view" runs the SQL at query time with no stored data — always fresh but slower for complex queries; a "table" pre-computes and stores results — fast queries but requires a full rebuild on each dbt run', 'Tables support incremental loading; views do not support any materialisation', 'Views are used for fact tables; tables are used for dimension tables'], 'ans' => 1, 'exp' => 'view materialisation creates a virtual table whose SQL is executed at query time — no data is stored, always reflects source data, but complex views can be slow. table materialisation runs the SQL and stores the result physically — queries are fast (reading pre-computed data) but each dbt run fully rebuilds the table. incremental sits between: only new rows are processed, combining table performance with efficiency.'],
            ['q' => 'The mart_finance.pl_summary view uses SUM(gross_profit) / NULLIF(SUM(net_revenue), 0) AS gp_margin. For a row where net_revenue = 0, what value does gp_margin return?', 'opts' => ['0 (zero divided by zero)', 'An arithmetic error that terminates the query', 'NULL — NULLIF returns NULL when net_revenue = 0, and any division by NULL = NULL', 'Infinity (gross_profit / 0 = ∞)'], 'ans' => 2, 'exp' => 'NULLIF(SUM(net_revenue), 0) returns NULL when the argument equals 0. Then gross_profit / NULL = NULL in SQL. This gracefully handles the division by zero case — the margin column shows NULL (meaning "undefined") rather than crashing the query. BI tools typically display NULL as a blank cell or "N/A" in dashboards.'],
            ['q' => 'A company\'s conformed dim_date table has date_key, full_date, month_name, quarter, fiscal_period. The sales mart and marketing mart both use this table. An analyst combines their monthly metrics with JOIN ... USING (date_key). What makes this JOIN valid and consistent?', 'opts' => ['Both tables use the same surrogate key sequence', 'Conformity — both marts use the identical dim_date definition with the same surrogate keys and attribute values, so date_key=20240115 means the same calendar day in both marts', 'The JOIN works because date_key is a primary key in both marts', 'The analyst has SELECT * permission on both mart schemas'], 'ans' => 1, 'exp' => 'Conformed dimensions are the foundational integration mechanism of the Kimball Bus Architecture. Because both marts share the identical dim_date table (same keys, same grain, same attributes), date_key=20240115 maps to January 15, 2024 in both. The JOIN is semantically valid — you are truly joining on the same day. If dim_date were different between marts, the JOIN would produce incorrect or misleading results.'],
            ['q' => 'The DataQualityChecker finds not_null:date_key FAIL with detail=1. What should happen next in a production ETL pipeline?', 'opts' => ['Load the data anyway and fix the NULL in the warehouse later', 'Halt the pipeline load, alert the data engineering team, quarantine the affected row to a DQ exception table, and investigate the source system before proceeding', 'Replace the NULL with the current date and continue loading', 'Delete the row with the NULL date_key and proceed silently'], 'ans' => 1, 'exp' => 'A NULL in a foreign key column (date_key) means the fact row cannot be joined to the date dimension — it is analytically useless and potentially harmful. Production best practice: fail the pipeline or quarantine the bad row to an exceptions table, alert engineers, investigate root cause in the source system, and only load clean data. Silent fixes (substituting today\'s date) introduce incorrect analysis without a traceable audit trail.'],
            ['q' => 'What is the core architectural difference between Snowflake and a traditional on-premises data warehouse like Teradata?', 'opts' => ['Snowflake uses SQL while Teradata uses a proprietary query language', 'Snowflake separates storage (object storage, cheap, persistent) from compute (Virtual Warehouses, elastic, pay-per-use) — on-premises tightly couples them in fixed hardware that must be sized for peak load and paid for continuously', 'Snowflake is row-oriented while Teradata is column-oriented', 'Snowflake cannot handle semi-structured data while Teradata can'], 'ans' => 1, 'exp' => 'On-premises warehouses couple storage and compute in physical servers — you buy enough hardware for peak demand and pay for it 24/7. Snowflake stores data in S3 (cheap object storage) and spins compute nodes on demand in seconds. Virtual Warehouses can be suspended when idle — compute billing stops. This separation enables elastic scaling and near-zero idle costs, fundamentally changing the economics of data warehousing.'],
        ];

        $finalContent  = <<<HTML
<div id="org-lock-screen" style="text-align:center;padding:4rem 2rem;background:var(--surface2);border:1px solid var(--border);border-radius:12px;margin-top:2rem;">
    <div style="font-size:3rem;margin-bottom:1rem;">🔒</div>
    <h3 style="color:var(--text);margin-bottom:0.5rem;">University / Organization Access Only</h3>
    <p style="color:var(--muted);">The Final Module Exam is restricted to enrolled students and verified organization members.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 23: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 23.1 through 23.10 — warehouse fundamentals, dimensional modelling, ETL/ELT pipelines, SCDs, Data Vault, columnar storage, cloud warehouses, data marts, dbt, and data quality and governance. Good luck!</p>
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
            'module_id'   => $module->id,
            'title'       => '23.11 Final Exam: Data Warehousing Mastery',
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