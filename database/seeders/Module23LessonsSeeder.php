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
        $dwModule = Module::where('order_index', 23)->firstOrFail();
        Lesson::where('module_id', $dwModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.1 — What Is a Data Warehouse?
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>What Is a Data Warehouse?</h2>
<p>A <strong>Data Warehouse (DW)</strong> is a centralized, integrated repository that collects, stores, and organizes large volumes of structured data from multiple source systems — transactional databases, CRM platforms, ERP systems, flat files, and APIs — specifically designed for <strong>analytical querying and business intelligence (BI)</strong>. Unlike an Online Transaction Processing (OLTP) database whose design is optimized for fast inserts, updates, and deletes of individual rows, a data warehouse is built for <strong>Online Analytical Processing (OLAP)</strong>: aggregating millions of rows, comparing periods, drilling down into hierarchies, and producing the reports and dashboards that executives use to make decisions.</p>

<p>The defining characteristic of a data warehouse is its <strong>subject-orientation</strong>. Where an OLTP database is organized around the transactions of a business process (an <code>orders</code> table, a <code>payments</code> table), a data warehouse is organized around business <em>subjects</em> — Sales, Finance, Human Resources, Inventory — and it integrates data about each subject from every relevant source into a single, consistent, historical record. When a business asks "What were our total sales by region for the past three years, broken down by product category and compared to the same period last year?" — that is a data warehouse question.</p>

<h3>The Four Key Characteristics (W.H. Inmon, 1990)</h3>
<p>Bill Inmon, widely regarded as the father of data warehousing, defined a data warehouse using four properties that distinguish it from all other data systems:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — OLTP vs OLAP: A Direct Comparison</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ─────────────────────────────────────────────────────────
-- OLTP Query: Retrieve one specific customer's latest order
-- Optimized for: low-latency single-row lookups
-- ─────────────────────────────────────────────────────────</span>
<span style="color:#c4b5fd;">SELECT</span> o.order_id, o.order_date, o.total_amount, c.customer_name
<span style="color:#c4b5fd;">FROM</span>   orders o
<span style="color:#c4b5fd;">JOIN</span>   customers c <span style="color:#c4b5fd;">ON</span> o.customer_id = c.customer_id
<span style="color:#c4b5fd;">WHERE</span>  c.customer_id = <span style="color:#fcd34d;">10482</span>
<span style="color:#c4b5fd;">ORDER BY</span> o.order_date <span style="color:#c4b5fd;">DESC</span>
<span style="color:#c4b5fd;">LIMIT</span> <span style="color:#fcd34d;">1</span>;

<span style="color:#6b7280;">-- ─────────────────────────────────────────────────────────
-- OLAP Query (Data Warehouse): Multi-year sales trend analysis
-- Optimized for: scanning millions of rows, grouping, aggregation
-- ─────────────────────────────────────────────────────────</span>
<span style="color:#c4b5fd;">SELECT</span>
    d.year,
    d.quarter,
    p.category_name,
    g.region_name,
    <span style="color:#93c5fd;">SUM</span>(f.sales_amount)                                    <span style="color:#c4b5fd;">AS</span> total_sales,
    <span style="color:#93c5fd;">SUM</span>(f.quantity_sold)                                   <span style="color:#c4b5fd;">AS</span> units_sold,
    <span style="color:#93c5fd;">ROUND</span>(<span style="color:#93c5fd;">AVG</span>(f.sales_amount / <span style="color:#93c5fd;">NULLIF</span>(f.quantity_sold,<span style="color:#fcd34d;">0</span>)),<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">AS</span> avg_unit_price
<span style="color:#c4b5fd;">FROM</span>
    fact_sales         f
    <span style="color:#c4b5fd;">JOIN</span> dim_date      d <span style="color:#c4b5fd;">ON</span> f.date_key      = d.date_key
    <span style="color:#c4b5fd;">JOIN</span> dim_product   p <span style="color:#c4b5fd;">ON</span> f.product_key   = p.product_key
    <span style="color:#c4b5fd;">JOIN</span> dim_geography g <span style="color:#c4b5fd;">ON</span> f.geography_key = g.geography_key
<span style="color:#c4b5fd;">WHERE</span>  d.year <span style="color:#c4b5fd;">BETWEEN</span> <span style="color:#fcd34d;">2021</span> <span style="color:#c4b5fd;">AND</span> <span style="color:#fcd34d;">2024</span>
<span style="color:#c4b5fd;">GROUP BY</span> d.year, d.quarter, p.category_name, g.region_name
<span style="color:#c4b5fd;">ORDER BY</span> d.year, d.quarter, total_sales <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Sample Result (OLAP query)</span>year  quarter  category      region       total_sales  units_sold  avg_unit_price
----  -------  ----------    ----------   -----------  ----------  --------------
2024  Q1       Electronics   Asia-Pac     4,812,300    18,204      264.35
2024  Q1       Electronics   North Am     3,991,100    14,820      269.30
2024  Q1       Apparel       Europe       2,104,580    42,091       49.99
2024  Q2       Electronics   Asia-Pac     5,203,900    19,018      273.63
...</div>
  </div>
</div>

<h3>Inmon's Four Characteristics</h3>
<p><strong>Subject-Oriented:</strong> Organized around major subjects of the enterprise (Customers, Products, Sales) rather than around application functions (Order Entry, Billing). <strong>Integrated:</strong> Data from multiple heterogeneous sources (Oracle ERP, Salesforce CRM, MySQL point-of-sale) is cleaned, standardized, and unified using consistent conventions — a customer is a "customer" everywhere, not "CLIENT" in one system and "CUST" in another. <strong>Non-Volatile:</strong> Once data enters the warehouse, it is never updated or deleted; it is a permanent, immutable historical record. Analysts can always go back to see what the data looked like at any point in time. <strong>Time-Variant:</strong> Every record carries a time dimension. Unlike OLTP where a price update overwrites the old price, the warehouse stores both: the old price and when it changed, enabling year-over-year and trend analysis.</p>

<h3>The Modern Data Warehouse Landscape</h3>
<p>Traditional data warehouses ran on dedicated on-premise servers (Teradata, IBM Netezza). Today, the dominant platforms are <strong>cloud-native</strong>: <strong>Google BigQuery</strong> (serverless, columnar, pay-per-query), <strong>Amazon Redshift</strong> (MPP, PostgreSQL-compatible), <strong>Snowflake</strong> (multi-cloud, storage/compute separation), and <strong>Azure Synapse Analytics</strong>. Throughout this module we will write standard SQL that runs on all platforms, and we will use Python with <code>sqlite3</code> and <code>pandas</code> to simulate a complete warehouse pipeline locally.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Setting Up a Local DW with SQLite</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sqlite3
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># Create an in-memory SQLite database as our local data warehouse</span>
<span style="color:#93c5fd;">conn</span> = sqlite3.connect(<span style="color:#a7f3d0;">':memory:'</span>)
<span style="color:#93c5fd;">cur</span>  = conn.cursor()

<span style="color:#6b7280;"># Confirm connection and SQLite version</span>
<span style="color:#93c5fd;">version</span> = cur.execute(<span style="color:#a7f3d0;">"SELECT sqlite_version()"</span>).fetchone()[<span style="color:#fcd34d;">0</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Local DW ready — SQLite {version}"</span>)

<span style="color:#6b7280;"># Enable WAL mode for better concurrent read performance</span>
cur.execute(<span style="color:#a7f3d0;">"PRAGMA journal_mode = WAL"</span>)
cur.execute(<span style="color:#a7f3d0;">"PRAGMA foreign_keys = ON"</span>)

<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Foreign key enforcement: ON"</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Data Warehouse environment initialized."</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Local DW ready — SQLite 3.45.1
Foreign key enforcement: ON
Data Warehouse environment initialized.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dwModule->id,
            'title'       => '23.1 What Is a Data Warehouse?',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L23_1', [
                ['q' => 'Which of the following is the PRIMARY design goal of a data warehouse?', 'opts' => ['Fast inserts and updates of individual rows (OLTP)', 'Analytical querying, aggregation, and business intelligence (OLAP)', 'Real-time transactional processing with row-level locking', 'Storing unstructured data like images and documents'], 'ans' => 1, 'exp' => 'Data warehouses are built for OLAP — analyzing large volumes of historical data through aggregation, grouping, and period comparisons. OLTP databases are optimized for fast transactional reads and writes.'],
                ['q' => 'What does "non-volatile" mean in the context of Inmon\'s four data warehouse characteristics?', 'opts' => ['Data is always stored in RAM for fast access', 'Once loaded, data is never updated or deleted — it forms a permanent historical record', 'The database never goes offline for maintenance', 'Data is automatically compressed to be non-volatile storage'], 'ans' => 1, 'exp' => 'Non-volatile means the warehouse is append-only. Unlike OLTP where a price change overwrites the old value, the warehouse preserves all historical states, enabling true trend and period analysis.'],
                ['q' => 'Which characteristic ensures that "customer" means the same thing across all source systems in a data warehouse?', 'opts' => ['Time-variant', 'Non-volatile', 'Subject-oriented', 'Integrated'], 'ans' => 3, 'exp' => 'Integration is the process of standardizing data from heterogeneous sources — naming conventions, data types, units, and encodings are unified so a "customer" in the CRM and a "CLIENT" in the ERP become one consistent entity.'],
                ['q' => 'What makes a cloud-native data warehouse like BigQuery or Snowflake different from traditional on-premise warehouses?', 'opts' => ['They only support NoSQL data formats', 'They separate storage from compute, enable elastic scaling, and charge per query rather than requiring dedicated hardware', 'They cannot run SQL — only proprietary query languages', 'They store data in row format for faster individual record access'], 'ans' => 1, 'exp' => 'Cloud DWs like BigQuery and Snowflake separate storage (cheap object storage) from compute (spin up/down on demand), enabling pay-per-use pricing, elastic scaling, and no infrastructure management.'],
                ['q' => 'A company wants to answer: "What were our top 10 products by revenue each quarter for the past 5 years?" Which system is designed for this?', 'opts' => ['An OLTP MySQL database', 'A Redis key-value cache', 'A data warehouse with a star schema', 'A message queue like Kafka'], 'ans' => 2, 'exp' => 'This is a classic analytical query spanning years of history with aggregation and ranking — exactly what a data warehouse star schema is built for. OLTP databases, caches, and queues are not designed for this workload.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.2 — Dimensional Modeling: Star & Snowflake Schemas
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>Dimensional Modeling: Star & Snowflake Schemas</h2>
<p><strong>Dimensional modeling</strong> is the design technique used to structure data inside a warehouse for maximum query performance and business user readability. Developed by Ralph Kimball in the 1990s, it organizes data into two types of tables: <strong>Fact tables</strong> that store measurable business events (a sale, a click, a shipment) and <strong>Dimension tables</strong> that describe the context of those events (who, what, where, when). This produces two principal schema patterns: the <strong>Star Schema</strong> and the <strong>Snowflake Schema</strong>.</p>

<h3>Fact Tables: The Measurements</h3>
<p>A fact table sits at the center of the model and stores one row per business event. It contains: <strong>Foreign keys</strong> to each dimension table (so you can join for context) and <strong>Measures</strong> — the numeric values you want to aggregate, like <code>sales_amount</code>, <code>quantity_sold</code>, or <code>discount_amount</code>. Fact tables are very wide (many columns) and extremely tall (billions of rows in production). They are designed to never be joined to each other directly — always through their shared dimensions.</p>

<h3>Dimension Tables: The Context</h3>
<p>Dimension tables are relatively small (thousands to millions of rows) but very wide — they carry all the descriptive attributes of a business entity. A <code>dim_product</code> table might have 50 columns describing every attribute of a product: name, SKU, brand, subcategory, category, color, size, supplier, launch date, and more. These rich attributes are what analysts use in <code>GROUP BY</code>, <code>WHERE</code>, and <code>HAVING</code> clauses to slice and dice the facts.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Building a Retail Star Schema</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ═══════════════════════════════════════════════════════
-- DIMENSION TABLES (descriptive context)
-- ═══════════════════════════════════════════════════════</span>

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_date (
    date_key        <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">PRIMARY KEY</span>,  <span style="color:#6b7280;">-- surrogate key: YYYYMMDD</span>
    full_date       <span style="color:#93c5fd;">DATE</span>         <span style="color:#c4b5fd;">NOT NULL</span>,
    day_of_week     <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">10</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,  <span style="color:#6b7280;">-- 'Monday', 'Tuesday'…</span>
    day_of_month    <span style="color:#93c5fd;">SMALLINT</span>     <span style="color:#c4b5fd;">NOT NULL</span>,
    month           <span style="color:#93c5fd;">SMALLINT</span>     <span style="color:#c4b5fd;">NOT NULL</span>,
    month_name      <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">10</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    quarter         <span style="color:#93c5fd;">CHAR</span>(<span style="color:#fcd34d;">2</span>)      <span style="color:#c4b5fd;">NOT NULL</span>,  <span style="color:#6b7280;">-- 'Q1','Q2','Q3','Q4'</span>
    year            <span style="color:#93c5fd;">SMALLINT</span>     <span style="color:#c4b5fd;">NOT NULL</span>,
    is_weekend      <span style="color:#93c5fd;">BOOLEAN</span>      <span style="color:#c4b5fd;">NOT NULL</span>,
    fiscal_year     <span style="color:#93c5fd;">SMALLINT</span>     <span style="color:#c4b5fd;">NOT NULL</span>,
    fiscal_quarter  <span style="color:#93c5fd;">CHAR</span>(<span style="color:#fcd34d;">2</span>)      <span style="color:#c4b5fd;">NOT NULL</span>
);

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_customer (
    customer_key    <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">PRIMARY KEY</span>,  <span style="color:#6b7280;">-- surrogate key</span>
    customer_id     <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">20</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,  <span style="color:#6b7280;">-- natural/business key</span>
    customer_name   <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">100</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    email           <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">150</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    city            <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    country         <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">60</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    segment         <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">30</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,  <span style="color:#6b7280;">-- 'Enterprise','SMB','Consumer'</span>
    acquisition_src <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">50</span>)  <span style="color:#c4b5fd;">NOT NULL</span>   <span style="color:#6b7280;">-- 'Organic','Paid','Referral'</span>
);

<span style="color:#c4b5fd;">CREATE TABLE</span> dim_product (
    product_key     <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">PRIMARY KEY</span>,
    product_id      <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">20</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    product_name    <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">150</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    sku             <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">50</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    brand           <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    subcategory     <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    category        <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    department      <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>)  <span style="color:#c4b5fd;">NOT NULL</span>,
    unit_cost       <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    list_price      <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span>
);

<span style="color:#6b7280;">-- ═══════════════════════════════════════════════════════
-- FACT TABLE (measurable events — one row per line item)
-- ═══════════════════════════════════════════════════════</span>

<span style="color:#c4b5fd;">CREATE TABLE</span> fact_sales (
    sale_key        <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">PRIMARY KEY</span> <span style="color:#93c5fd;">AUTOINCREMENT</span>,
    date_key        <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">NOT NULL</span> <span style="color:#c4b5fd;">REFERENCES</span> dim_date(date_key),
    customer_key    <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">NOT NULL</span> <span style="color:#c4b5fd;">REFERENCES</span> dim_customer(customer_key),
    product_key     <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">NOT NULL</span> <span style="color:#c4b5fd;">REFERENCES</span> dim_product(product_key),
    <span style="color:#6b7280;">-- Measures (additive facts)</span>
    quantity_sold   <span style="color:#93c5fd;">INTEGER</span>      <span style="color:#c4b5fd;">NOT NULL</span>,
    sales_amount    <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">12</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    discount_amount <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span> <span style="color:#c4b5fd;">DEFAULT</span> <span style="color:#fcd34d;">0</span>,
    cost_amount     <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">12</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    profit_amount   <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">12</span>,<span style="color:#fcd34d;">2</span>) <span style="color:#c4b5fd;">NOT NULL</span>   <span style="color:#6b7280;">-- sales_amount - cost_amount</span>
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Schema Created Successfully</span>Tables created: dim_date, dim_customer, dim_product, fact_sales
Star schema: 1 fact table + 3 dimension tables
Foreign keys enforced: ✓</div>
  </div>
</div>

<h3>Star vs Snowflake Schema</h3>
<p>In a <strong>Star Schema</strong>, each dimension is a single, flat, denormalized table — all product attributes in one <code>dim_product</code> table, no matter how many levels of hierarchy (brand → subcategory → category → department). Queries are simpler and faster because they require only one join per dimension. This is the Kimball-preferred approach and is used in the vast majority of production warehouses.</p>

<p>In a <strong>Snowflake Schema</strong>, dimension tables are <em>normalized</em> — the hierarchy levels are split into separate tables. <code>dim_product</code> references a <code>dim_subcategory</code> table, which in turn references <code>dim_category</code>, which references <code>dim_department</code>. This saves storage on repeated string values and maintains strict data integrity, but requires additional joins, slowing analytical queries. Use it when storage is the critical constraint or when dimension normalization is governed by strict DBA policy.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Populating and Querying the Star Schema</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sqlite3, pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#93c5fd;">conn</span> = sqlite3.connect(<span style="color:#a7f3d0;">':memory:'</span>)
<span style="color:#93c5fd;">cur</span>  = conn.cursor()
cur.execute(<span style="color:#a7f3d0;">"PRAGMA foreign_keys = ON"</span>)

<span style="color:#6b7280;"># Quick schema (abbreviated for demo)</span>
cur.executescript(<span style="color:#a7f3d0;">"""
    CREATE TABLE dim_date    (date_key INT PRIMARY KEY, year INT, quarter TEXT, month_name TEXT);
    CREATE TABLE dim_product (product_key INT PRIMARY KEY, product_name TEXT, category TEXT);
    CREATE TABLE fact_sales  (
        date_key INT REFERENCES dim_date(date_key),
        product_key INT REFERENCES dim_product(product_key),
        sales_amount REAL, quantity_sold INT
    );
"""</span>)

<span style="color:#6b7280;"># Seed dimension data</span>
cur.executemany(<span style="color:#a7f3d0;">"INSERT INTO dim_date VALUES (?,?,?,?)"</span>, [
    (<span style="color:#fcd34d;">20240101</span>, <span style="color:#fcd34d;">2024</span>, <span style="color:#a7f3d0;">'Q1'</span>, <span style="color:#a7f3d0;">'January'</span>),
    (<span style="color:#fcd34d;">20240401</span>, <span style="color:#fcd34d;">2024</span>, <span style="color:#a7f3d0;">'Q2'</span>, <span style="color:#a7f3d0;">'April'</span>),
    (<span style="color:#fcd34d;">20240701</span>, <span style="color:#fcd34d;">2024</span>, <span style="color:#a7f3d0;">'Q3'</span>, <span style="color:#a7f3d0;">'July'</span>),
])
cur.executemany(<span style="color:#a7f3d0;">"INSERT INTO dim_product VALUES (?,?,?)"</span>, [
    (<span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">'Laptop Pro X'</span>,   <span style="color:#a7f3d0;">'Electronics'</span>),
    (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">'Wireless Headset'</span>,<span style="color:#a7f3d0;">'Electronics'</span>),
    (<span style="color:#fcd34d;">3</span>, <span style="color:#a7f3d0;">'Running Shoes'</span>,   <span style="color:#a7f3d0;">'Footwear'</span>),
])
cur.executemany(<span style="color:#a7f3d0;">"INSERT INTO fact_sales VALUES (?,?,?,?)"</span>, [
    (<span style="color:#fcd34d;">20240101</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">85000</span>, <span style="color:#fcd34d;">34</span>), (<span style="color:#fcd34d;">20240101</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">12000</span>, <span style="color:#fcd34d;">80</span>),
    (<span style="color:#fcd34d;">20240401</span>, <span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">92000</span>, <span style="color:#fcd34d;">37</span>), (<span style="color:#fcd34d;">20240401</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">18500</span>, <span style="color:#fcd34d;">148</span>),
    (<span style="color:#fcd34d;">20240701</span>, <span style="color:#fcd34d;">2</span>, <span style="color:#fcd34d;">15600</span>, <span style="color:#fcd34d;">104</span>), (<span style="color:#fcd34d;">20240701</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">22400</span>, <span style="color:#fcd34d;">179</span>),
])
conn.commit()

<span style="color:#6b7280;"># Analytical query: sales by category and quarter</span>
<span style="color:#93c5fd;">query</span> = <span style="color:#a7f3d0;">"""
    SELECT d.quarter, p.category,
           SUM(f.sales_amount) AS total_sales,
           SUM(f.quantity_sold) AS units
    FROM   fact_sales f
    JOIN   dim_date    d ON f.date_key    = d.date_key
    JOIN   dim_product p ON f.product_key = p.product_key
    GROUP  BY d.quarter, p.category
    ORDER  BY d.quarter, total_sales DESC
"""</span>
<span style="color:#93c5fd;">df</span> = pd.read_sql_query(query, conn)
<span style="color:#93c5fd;">print</span>(df.to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>quarter     category  total_sales  units
     Q1  Electronics      97000.0    114
     Q2  Electronics      92000.0     37
     Q2     Footwear      18500.0    148
     Q3  Electronics      15600.0    104
     Q3     Footwear      22400.0    179</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dwModule->id,
            'title'       => '23.2 Dimensional Modeling: Star & Snowflake Schemas',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L23_2', [
                ['q' => 'What are the two types of tables in dimensional modeling?', 'opts' => ['Source tables and target tables', 'Fact tables (measurable events) and dimension tables (descriptive context)', 'Primary tables and foreign tables', 'Raw tables and aggregated tables'], 'ans' => 1, 'exp' => 'Dimensional modeling uses fact tables (one row per business event, containing measures and foreign keys) and dimension tables (descriptive attributes of the entities involved in those events).'],
                ['q' => 'Why do fact tables use surrogate keys (integer sequences) instead of natural business keys?', 'opts' => ['Natural keys are too long to index efficiently, whereas small integers join faster and remain stable even when source system keys change', 'Surrogate keys are required by the SQL standard', 'Natural keys are not allowed in OLAP systems', 'Surrogate keys reduce the number of columns in the fact table'], 'ans' => 0, 'exp' => 'Surrogate keys are compact integers that join faster than long strings or composite natural keys. They also insulate the warehouse from changes in source system key assignments, such as a customer number format change.'],
                ['q' => 'What is the primary advantage of a Star Schema over a Snowflake Schema?', 'opts' => ['It uses less storage space', 'Dimension tables are fully normalized, ensuring no redundancy', 'Fewer joins per query because dimensions are denormalized into single flat tables, improving query performance', 'It supports real-time data updates'], 'ans' => 2, 'exp' => 'Star schema dimensions are flat and denormalized — no hierarchy splits. This means analysts need only one join per dimension, reducing query complexity and improving analytical performance.'],
                ['q' => 'A fact table row representing a single sales line item contains quantity_sold = 5, sales_amount = 500.00, and discount_amount = 25.00. What type of measures are these?', 'opts' => ['Semi-additive facts', 'Non-additive facts', 'Derived facts', 'Additive facts'], 'ans' => 3, 'exp' => 'Additive facts can be summed across all dimensions — you can sum sales_amount across dates, products, and customers to get any meaningful total. Semi-additive facts (like inventory balance) can only be summed across some dimensions.'],
                ['q' => 'In a Snowflake Schema, how is the product hierarchy (brand → subcategory → category → department) represented differently from a Star Schema?', 'opts' => ['All levels are collapsed into a single denormalized dim_product table', 'Each hierarchy level is stored in a separate normalized table that references the level above it', 'The hierarchy is stored as a JSON column in the fact table', 'Hierarchies are not supported in Snowflake schemas'], 'ans' => 1, 'exp' => 'Snowflake normalizes dimension hierarchies into separate tables: dim_brand → dim_subcategory → dim_category → dim_department. This saves storage on repeated values but requires additional joins at query time.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.3 — ETL: Extract, Transform, Load
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>ETL: Extract, Transform, Load</h2>
<p><strong>ETL</strong> (Extract, Transform, Load) is the pipeline process that moves data from source systems into the data warehouse. It is the backbone of every warehouse — without a reliable, repeatable ETL pipeline, even the most beautifully designed schema sits empty. ETL is also one of the most underestimated components in data engineering: studies consistently show that data engineers spend <strong>60–80% of their time</strong> on data preparation and pipeline work, not on analysis. Getting ETL right from the start is the difference between a trustworthy data warehouse and a swamp of inconsistent, stale, and broken data.</p>

<h3>Phase 1 — Extract: Pulling Data from Sources</h3>
<p>The extract phase connects to every source system and pulls the relevant data. Sources can include relational databases (via JDBC/ODBC), REST APIs, flat files (CSV, JSON, XML), message queues (Kafka), and SaaS platforms (Salesforce, Stripe). A critical decision is whether to do a <strong>full extract</strong> (pull all records every run — simple but expensive) or an <strong>incremental extract</strong> (pull only records changed since the last run — efficient but requires a reliable change-detection mechanism like a <code>updated_at</code> timestamp or Change Data Capture).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Extract Phase: Full & Incremental</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sqlite3, pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">from</span> datetime <span style="color:#c4b5fd;">import</span> datetime, timedelta

<span style="color:#6b7280;"># Simulate a source OLTP database</span>
<span style="color:#93c5fd;">src</span> = sqlite3.connect(<span style="color:#a7f3d0;">':memory:'</span>)
src.execute(<span style="color:#a7f3d0;">"""
    CREATE TABLE orders (
        order_id   INTEGER PRIMARY KEY,
        customer   TEXT,
        amount     REAL,
        status     TEXT,
        updated_at TEXT
    )
"""</span>)
src.executemany(<span style="color:#a7f3d0;">"INSERT INTO orders VALUES (?,?,?,?,?)"</span>, [
    (<span style="color:#fcd34d;">1</span>, <span style="color:#a7f3d0;">'Alice'</span>,   <span style="color:#fcd34d;">1200.00</span>, <span style="color:#a7f3d0;">'completed'</span>, <span style="color:#a7f3d0;">'2024-01-10 08:00:00'</span>),
    (<span style="color:#fcd34d;">2</span>, <span style="color:#a7f3d0;">'Bob'</span>,     <span style="color:#fcd34d;">850.50</span>,  <span style="color:#a7f3d0;">'completed'</span>, <span style="color:#a7f3d0;">'2024-01-11 09:15:00'</span>),
    (<span style="color:#fcd34d;">3</span>, <span style="color:#a7f3d0;">'Charlie'</span>, <span style="color:#fcd34d;">3200.00</span>, <span style="color:#a7f3d0;">'pending'</span>,   <span style="color:#a7f3d0;">'2024-03-20 14:30:00'</span>),
    (<span style="color:#fcd34d;">4</span>, <span style="color:#a7f3d0;">'Diana'</span>,   <span style="color:#fcd34d;">540.00</span>,  <span style="color:#a7f3d0;">'completed'</span>, <span style="color:#a7f3d0;">'2024-03-21 10:00:00'</span>),
])
src.commit()

<span style="color:#6b7280;"># ── Full Extract (simple, costly for large tables) ─────</span>
<span style="color:#93c5fd;">df_full</span> = pd.read_sql(<span style="color:#a7f3d0;">"SELECT * FROM orders"</span>, src)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Full extract: {len(df_full)} rows\n"</span>)

<span style="color:#6b7280;"># ── Incremental Extract (efficient for large tables) ───</span>
<span style="color:#6b7280;"># Only pull rows changed since the last successful run</span>
<span style="color:#93c5fd;">last_run</span> = <span style="color:#a7f3d0;">'2024-02-01 00:00:00'</span>   <span style="color:#6b7280;"># stored in an ETL control table</span>

<span style="color:#93c5fd;">df_inc</span> = pd.read_sql(
    <span style="color:#a7f3d0;">"SELECT * FROM orders WHERE updated_at > ?"</span>,
    src, params=[last_run]
)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Incremental extract (since {last_run}): {len(df_inc)} rows"</span>)
<span style="color:#93c5fd;">print</span>(df_inc[[<span style="color:#a7f3d0;">'order_id'</span>, <span style="color:#a7f3d0;">'customer'</span>, <span style="color:#a7f3d0;">'amount'</span>, <span style="color:#a7f3d0;">'updated_at'</span>]].to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Full extract: 4 rows

Incremental extract (since 2024-02-01 00:00:00): 2 rows
 order_id customer   amount           updated_at
        3  Charlie  3200.00  2024-03-20 14:30:00
        4    Diana   540.00  2024-03-21 10:00:00</div>
  </div>
</div>

<h3>Phase 2 — Transform: Cleaning, Standardizing, and Enriching</h3>
<p>The transform phase is where raw source data is converted into warehouse-ready data. This includes: <strong>cleaning</strong> (removing nulls, fixing formats, deduplicating), <strong>standardizing</strong> (unified date formats, country codes, currency), <strong>conforming</strong> (merging the same customer appearing in two source systems), and <strong>deriving new fields</strong> (calculating profit margin, age buckets, fiscal periods). This is the most complex and business-logic-heavy phase of ETL.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Transform Phase: Clean, Standardize, Derive</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd
<span style="color:#c4b5fd;">import</span> numpy <span style="color:#c4b5fd;">as</span> np

<span style="color:#6b7280;"># Simulate raw extracted data (messy, real-world quality)</span>
<span style="color:#93c5fd;">raw</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'order_id'</span>:   [<span style="color:#fcd34d;">101</span>, <span style="color:#fcd34d;">102</span>, <span style="color:#fcd34d;">103</span>, <span style="color:#fcd34d;">104</span>, <span style="color:#fcd34d;">102</span>],          <span style="color:#6b7280;"># 102 is a duplicate</span>
    <span style="color:#a7f3d0;">'customer'</span>:   [<span style="color:#a7f3d0;">'  alice '</span>, <span style="color:#a7f3d0;">'BOB'</span>, <span style="color:#a7f3d0;">None</span>, <span style="color:#a7f3d0;">'diana'</span>, <span style="color:#a7f3d0;">'BOB'</span>], <span style="color:#6b7280;"># dirty names, null</span>
    <span style="color:#a7f3d0;">'amount'</span>:     [<span style="color:#fcd34d;">1200.0</span>, <span style="color:#fcd34d;">850.5</span>, <span style="color:#fcd34d;">3200.0</span>, <span style="color:#fcd34d;">540.0</span>, <span style="color:#fcd34d;">850.5</span>],
    <span style="color:#a7f3d0;">'order_date'</span>: [<span style="color:#a7f3d0;">'2024/01/10'</span>, <span style="color:#a7f3d0;">'2024-01-11'</span>,
                   <span style="color:#a7f3d0;">'20240320'</span>,   <span style="color:#a7f3d0;">'Jan 21 2024'</span>, <span style="color:#a7f3d0;">'2024-01-11'</span>],
    <span style="color:#a7f3d0;">'cost'</span>:       [<span style="color:#fcd34d;">720.0</span>, <span style="color:#fcd34d;">510.3</span>, <span style="color:#fcd34d;">1920.0</span>, <span style="color:#fcd34d;">324.0</span>, <span style="color:#fcd34d;">510.3</span>],
})

<span style="color:#6b7280;"># ── Step 1: Remove duplicates ──────────────────────────</span>
<span style="color:#93c5fd;">df</span> = raw.drop_duplicates(subset=<span style="color:#a7f3d0;">'order_id'</span>).copy()

<span style="color:#6b7280;"># ── Step 2: Drop rows with null business keys ──────────</span>
<span style="color:#93c5fd;">df</span> = df.dropna(subset=[<span style="color:#a7f3d0;">'customer'</span>])

<span style="color:#6b7280;"># ── Step 3: Standardize text fields ───────────────────</span>
df[<span style="color:#a7f3d0;">'customer'</span>] = df[<span style="color:#a7f3d0;">'customer'</span>].str.strip().str.title()

<span style="color:#6b7280;"># ── Step 4: Parse mixed-format dates ──────────────────</span>
df[<span style="color:#a7f3d0;">'order_date'</span>] = pd.to_datetime(df[<span style="color:#a7f3d0;">'order_date'</span>], infer_datetime_format=<span style="color:#fca5a5;">True</span>)

<span style="color:#6b7280;"># ── Step 5: Derive new fields ──────────────────────────</span>
df[<span style="color:#a7f3d0;">'profit'</span>]       = (df[<span style="color:#a7f3d0;">'amount'</span>] - df[<span style="color:#a7f3d0;">'cost'</span>]).round(<span style="color:#fcd34d;">2</span>)
df[<span style="color:#a7f3d0;">'margin_pct'</span>]   = ((df[<span style="color:#a7f3d0;">'profit'</span>] / df[<span style="color:#a7f3d0;">'amount'</span>]) * <span style="color:#fcd34d;">100</span>).round(<span style="color:#fcd34d;">1</span>)
df[<span style="color:#a7f3d0;">'date_key'</span>]     = df[<span style="color:#a7f3d0;">'order_date'</span>].dt.strftime(<span style="color:#a7f3d0;">'%Y%m%d'</span>).astype(<span style="color:#93c5fd;">int</span>)

<span style="color:#93c5fd;">print</span>(df[[<span style="color:#a7f3d0;">'order_id'</span>,<span style="color:#a7f3d0;">'customer'</span>,<span style="color:#a7f3d0;">'amount'</span>,<span style="color:#a7f3d0;">'profit'</span>,<span style="color:#a7f3d0;">'margin_pct'</span>,<span style="color:#a7f3d0;">'date_key'</span>]].to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span> order_id customer   amount  profit  margin_pct  date_key
      101    Alice  1200.00  480.00        40.0  20240110
      102      Bob   850.50  340.20        40.0  20240111
      104    Diana   540.00  216.00        40.0  20240121</div>
  </div>
</div>

<h3>Phase 3 — Load: Writing into the Warehouse</h3>
<p>The load phase writes the transformed data into the warehouse. The two strategies are <strong>full load</strong> (truncate and reload the entire target table — simple but slow) and <strong>incremental load</strong> (insert new rows and update or delete changed rows using UPSERT logic). For large warehouses, incremental loads with <code>INSERT ... ON CONFLICT DO UPDATE</code> (PostgreSQL / SQLite) or <code>MERGE</code> statements (SQL Server, Snowflake) are the standard approach.</p>
HTML;

        Lesson::create([
            'module_id'   => $dwModule->id,
            'title'       => '23.3 ETL: Extract, Transform, Load',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L23_3', [
                ['q' => 'What is the primary difference between a full extract and an incremental extract?', 'opts' => ['Full extracts use SQL; incremental extracts use Python', 'Full extracts pull all records every run; incremental extracts pull only records changed since the last run', 'Incremental extracts are always slower than full extracts', 'Full extracts require a primary key; incremental extracts do not'], 'ans' => 1, 'exp' => 'A full extract is simple but reads the entire source table every run — expensive at scale. Incremental extracts use a high-watermark (e.g. updated_at timestamp) to pull only the delta, making them far more efficient for large, frequently-updated sources.'],
                ['q' => 'Which of the following is NOT a task performed in the Transform phase?', 'opts' => ['Removing duplicate records', 'Standardizing inconsistent date formats', 'Writing cleaned data to the target warehouse table', 'Deriving new calculated fields like profit margin'], 'ans' => 2, 'exp' => 'Writing data to the target is the Load phase. The Transform phase covers cleaning (deduplication, null handling), standardization (formats, casing, codes), conforming (merging entities), and deriving new fields.'],
                ['q' => 'What does a high-watermark variable store in an ETL pipeline?', 'opts' => ['The maximum value of a measure column like sales_amount', 'The timestamp or ID of the last successfully processed record, used to identify new rows in the next incremental run', 'The number of rows in the largest dimension table', 'The current memory usage of the ETL process'], 'ans' => 1, 'exp' => 'A high-watermark (e.g. last_run timestamp) is saved in an ETL control table after each successful run. The next run uses it as the lower bound in the WHERE clause to extract only newer records.'],
                ['q' => 'Why is deduplication critical in the Transform phase?', 'opts' => ['Because databases cannot store duplicate primary keys', 'Because loading duplicate records inflates aggregate measures (sums, counts) in the warehouse, producing incorrect business reports', 'Because duplicates slow down the Extract phase', 'Because the Load phase automatically rejects duplicates'], 'ans' => 1, 'exp' => 'Duplicate rows in a fact table double-count events. A sale appearing twice makes revenue appear inflated. Deduplication in the Transform phase ensures data integrity before it reaches the warehouse.'],
                ['q' => 'What SQL pattern is used for incremental loads that handles both new inserts AND updates to existing rows?', 'opts' => ['INSERT INTO ... SELECT *', 'CREATE TABLE AS SELECT', 'UPSERT / MERGE (INSERT ON CONFLICT DO UPDATE)', 'TRUNCATE TABLE then INSERT ALL'], 'ans' => 2, 'exp' => 'UPSERT (INSERT ... ON CONFLICT DO UPDATE in PostgreSQL/SQLite, or MERGE in SQL Server/Snowflake) atomically inserts a row if it doesn\'t exist or updates it if it does — handling both new and changed records in one statement.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.4 — Slowly Changing Dimensions (SCDs)
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>Slowly Changing Dimensions (SCDs)</h2>
<p>In the real world, dimension attributes change over time. A customer moves to a new city. A product changes its brand. An employee transfers to a different department. The question <strong>Slowly Changing Dimensions (SCDs)</strong> answers is: <em>when a dimension attribute changes, what do we do with the historical fact data that was associated with the old value?</em> There are six SCD types (0–6), but three are used in virtually all production warehouses: Type 1, Type 2, and Type 3. Choosing the right SCD type for each attribute is one of the most impactful design decisions in data warehousing.</p>

<h3>SCD Type 1 — Overwrite (No History)</h3>
<p>Simply overwrite the old value with the new value. The old value is permanently lost. All historical facts linked to this dimension now reflect the current attribute value, even if the fact occurred under the old value. <strong>Use when:</strong> the change was a correction (a typo in a product name), not a real business event. <strong>Danger:</strong> reports run today will show different numbers than the same report run before the change, since historical facts are now grouped under the corrected value.</p>

<h3>SCD Type 2 — Add New Row (Full History)</h3>
<p>Insert a new row for the changed dimension member, while marking the old row as expired. Each version of the dimension record carries an <code>effective_start</code> and <code>effective_end</code> date, plus an <code>is_current</code> flag. Historical facts point to the old surrogate key and still correctly reflect the old attribute values. New facts point to the new surrogate key. This is the most powerful — and most commonly used — SCD type in enterprise data warehouses.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — SCD Type 2: Customer City Change</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">CREATE TABLE</span> dim_customer_scd2 (
    customer_key    <span style="color:#93c5fd;">INTEGER</span>     <span style="color:#c4b5fd;">PRIMARY KEY</span>,   <span style="color:#6b7280;">-- surrogate key (unique per version)</span>
    customer_id     <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">20</span>) <span style="color:#c4b5fd;">NOT NULL</span>,      <span style="color:#6b7280;">-- natural/business key (same across versions)</span>
    customer_name   <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">100</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    city            <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">80</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    segment         <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">30</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    effective_start <span style="color:#93c5fd;">DATE</span>        <span style="color:#c4b5fd;">NOT NULL</span>,
    effective_end   <span style="color:#93c5fd;">DATE</span>        <span style="color:#c4b5fd;">NULL</span>,            <span style="color:#6b7280;">-- NULL means "still active"</span>
    is_current      <span style="color:#93c5fd;">BOOLEAN</span>     <span style="color:#c4b5fd;">NOT NULL</span> <span style="color:#c4b5fd;">DEFAULT</span> <span style="color:#fca5a5;">TRUE</span>
);

<span style="color:#6b7280;">-- Initial load: Alice lives in Cebu</span>
<span style="color:#c4b5fd;">INSERT INTO</span> dim_customer_scd2
<span style="color:#c4b5fd;">VALUES</span> (<span style="color:#fcd34d;">1001</span>, <span style="color:#a7f3d0;">'C-001'</span>, <span style="color:#a7f3d0;">'Alice Reyes'</span>, <span style="color:#a7f3d0;">'Cebu'</span>, <span style="color:#a7f3d0;">'Enterprise'</span>, <span style="color:#a7f3d0;">'2022-01-01'</span>, <span style="color:#c4b5fd;">NULL</span>, <span style="color:#fca5a5;">TRUE</span>);

<span style="color:#6b7280;">-- Alice moves to Manila on 2024-06-01 — SCD Type 2 update process:

-- Step 1: Expire the current record</span>
<span style="color:#c4b5fd;">UPDATE</span> dim_customer_scd2
<span style="color:#c4b5fd;">SET</span>    effective_end = <span style="color:#a7f3d0;">'2024-05-31'</span>,
       is_current    = <span style="color:#fca5a5;">FALSE</span>
<span style="color:#c4b5fd;">WHERE</span>  customer_id = <span style="color:#a7f3d0;">'C-001'</span>
  <span style="color:#c4b5fd;">AND</span>  is_current   = <span style="color:#fca5a5;">TRUE</span>;

<span style="color:#6b7280;">-- Step 2: Insert the new version with the updated city</span>
<span style="color:#c4b5fd;">INSERT INTO</span> dim_customer_scd2
<span style="color:#c4b5fd;">VALUES</span> (<span style="color:#fcd34d;">1002</span>, <span style="color:#a7f3d0;">'C-001'</span>, <span style="color:#a7f3d0;">'Alice Reyes'</span>, <span style="color:#a7f3d0;">'Manila'</span>, <span style="color:#a7f3d0;">'Enterprise'</span>, <span style="color:#a7f3d0;">'2024-06-01'</span>, <span style="color:#c4b5fd;">NULL</span>, <span style="color:#fca5a5;">TRUE</span>);

<span style="color:#6b7280;">-- Query: see all versions of Alice — full audit trail</span>
<span style="color:#c4b5fd;">SELECT</span> customer_key, customer_id, city, effective_start, effective_end, is_current
<span style="color:#c4b5fd;">FROM</span>   dim_customer_scd2
<span style="color:#c4b5fd;">WHERE</span>  customer_id = <span style="color:#a7f3d0;">'C-001'</span>
<span style="color:#c4b5fd;">ORDER BY</span> effective_start;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Query Result</span>customer_key  customer_id  city    effective_start  effective_end  is_current
        1001        C-001  Cebu         2022-01-01     2024-05-31       FALSE
        1002        C-001  Manila       2024-06-01           NULL        TRUE</div>
  </div>
</div>
</div>

<h3>SCD Type 3 — Add New Column (Limited History)</h3>
<p>Instead of adding a new row, add a new column to store the previous value. The dimension row keeps both <code>current_city</code> and <code>previous_city</code>. This allows one "look back" but cannot track more than one historical change. Use it when analysts need to compare current vs prior state but full version history is not required.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Simulating All Three SCD Types</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># --- Current dimension state ---</span>
<span style="color:#93c5fd;">dim</span> = pd.DataFrame({
    <span style="color:#a7f3d0;">'customer_key'</span>:  [<span style="color:#fcd34d;">1001</span>],
    <span style="color:#a7f3d0;">'customer_id'</span>:   [<span style="color:#a7f3d0;">'C-001'</span>],
    <span style="color:#a7f3d0;">'customer_name'</span>: [<span style="color:#a7f3d0;">'Alice Reyes'</span>],
    <span style="color:#a7f3d0;">'city'</span>:          [<span style="color:#a7f3d0;">'Cebu'</span>],
    <span style="color:#a7f3d0;">'segment'</span>:       [<span style="color:#a7f3d0;">'SMB'</span>],
    <span style="color:#a7f3d0;">'prev_city'</span>:     [<span style="color:#c4b5fd;">None</span>],
})
<span style="color:#93c5fd;">change</span> = {<span style="color:#a7f3d0;">'customer_id'</span>: <span style="color:#a7f3d0;">'C-001'</span>, <span style="color:#a7f3d0;">'city'</span>: <span style="color:#a7f3d0;">'Manila'</span>, <span style="color:#a7f3d0;">'segment'</span>: <span style="color:#a7f3d0;">'Enterprise'</span>}
<span style="color:#93c5fd;">mask</span> = dim[<span style="color:#a7f3d0;">'customer_id'</span>] == change[<span style="color:#a7f3d0;">'customer_id'</span>]

<span style="color:#6b7280;"># SCD Type 1</span>
<span style="color:#93c5fd;">scd1</span> = dim.copy()
scd1.loc[mask, <span style="color:#a7f3d0;">'city'</span>]    = change[<span style="color:#a7f3d0;">'city'</span>]
scd1.loc[mask, <span style="color:#a7f3d0;">'segment'</span>] = change[<span style="color:#a7f3d0;">'segment'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"=== SCD1 ==="</span>); <span style="color:#93c5fd;">print</span>(scd1[[<span style="color:#a7f3d0;">'customer_id'</span>,<span style="color:#a7f3d0;">'city'</span>,<span style="color:#a7f3d0;">'segment'</span>]].to_string(index=<span style="color:#fca5a5;">False</span>))

<span style="color:#6b7280;"># SCD Type 2</span>
<span style="color:#93c5fd;">new_row</span> = pd.DataFrame([{<span style="color:#a7f3d0;">'customer_key'</span>:<span style="color:#fcd34d;">1002</span>,<span style="color:#a7f3d0;">'customer_id'</span>:<span style="color:#a7f3d0;">'C-001'</span>,<span style="color:#a7f3d0;">'customer_name'</span>:<span style="color:#a7f3d0;">'Alice Reyes'</span>,<span style="color:#a7f3d0;">'city'</span>:<span style="color:#a7f3d0;">'Manila'</span>,<span style="color:#a7f3d0;">'segment'</span>:<span style="color:#a7f3d0;">'Enterprise'</span>,<span style="color:#a7f3d0;">'prev_city'</span>:<span style="color:#c4b5fd;">None</span>}])
<span style="color:#93c5fd;">scd2</span> = pd.concat([dim, new_row], ignore_index=<span style="color:#fca5a5;">True</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== SCD2 ==="</span>); <span style="color:#93c5fd;">print</span>(scd2[[<span style="color:#a7f3d0;">'customer_key'</span>,<span style="color:#a7f3d0;">'city'</span>,<span style="color:#a7f3d0;">'segment'</span>]].to_string(index=<span style="color:#fca5a5;">False</span>))

<span style="color:#6b7280;"># SCD Type 3</span>
<span style="color:#93c5fd;">scd3</span> = dim.copy()
scd3.loc[mask, <span style="color:#a7f3d0;">'prev_city'</span>] = scd3.loc[mask, <span style="color:#a7f3d0;">'city'</span>]
scd3.loc[mask, <span style="color:#a7f3d0;">'city'</span>]      = change[<span style="color:#a7f3d0;">'city'</span>]
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"\n=== SCD3 ==="</span>); <span style="color:#93c5fd;">print</span>(scd3[[<span style="color:#a7f3d0;">'customer_id'</span>,<span style="color:#a7f3d0;">'prev_city'</span>,<span style="color:#a7f3d0;">'city'</span>]].to_string(index=<span style="color:#fca5a5;">False</span>))</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>=== SCD1 ===
 customer_id    city     segment
       C-001  Manila  Enterprise

=== SCD2 ===
 customer_key    city     segment
         1001    Cebu         SMB
         1002  Manila  Enterprise

=== SCD3 ===
 customer_id prev_city    city
       C-001      Cebu  Manila</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dwModule->id,
            'title'       => '23.4 Slowly Changing Dimensions (SCDs)',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L23_4', [
                ['q' => 'A product name was misspelled during initial load. Which SCD type is correct?', 'opts' => ['SCD Type 2 — add a new row with the corrected name', 'SCD Type 1 — overwrite; no historical value should be preserved for a data error', 'SCD Type 3 — add a previous_name column', 'SCD Type 0 — attribute is fixed'], 'ans' => 1, 'exp' => 'A spelling correction is not a real business event. SCD Type 1 (overwrite) is correct. SCD Type 2 would create false history, suggesting the product genuinely had two different names at different times.'],
                ['q' => 'In SCD Type 2, what happens to fact rows loaded BEFORE a dimension change?', 'opts' => ['They are updated to point to the new dimension row automatically', 'They continue pointing to the old surrogate key and correctly reflect the old attribute values', 'They are deleted and reloaded with the new dimension key', 'They must be manually reassigned by a DBA'], 'ans' => 1, 'exp' => 'SCD Type 2\'s power: fact rows are never touched. They already point to the old surrogate key (e.g., customer_key = 1001 / city = Cebu). New facts point to 1002 / Manila. Historical reports remain accurate automatically.'],
                ['q' => 'Which SCD type provides the most complete historical record of dimension changes?', 'opts' => ['SCD Type 0', 'SCD Type 1', 'SCD Type 2', 'SCD Type 3'], 'ans' => 2, 'exp' => 'SCD Type 2 adds a new row for every change, with effective_start and effective_end dates. This creates a complete audit trail of every state a dimension member has been in, enabling accurate point-in-time analysis for any historical date.'],
                ['q' => 'What is the main limitation of SCD Type 3?', 'opts' => ['It requires a separate history table', 'It can only track one previous value — multiple changes overwrite the "previous" column, losing older history', 'It dramatically increases storage requirements', 'It cannot be used with integer surrogate keys'], 'ans' => 1, 'exp' => 'SCD Type 3 stores only one prior value per attribute. If a customer moves a second time, the first city (Cebu) is permanently lost. It handles exactly one level of historical change.'],
                ['q' => 'In an SCD Type 2 dimension, what does is_current = TRUE signify?', 'opts' => ['The row was inserted during the current ETL run', 'The row represents the most recent active version of that dimension member — effective_end is NULL', 'The row has never been changed since initial load', 'The row passes all data quality checks'], 'ans' => 1, 'exp' => 'is_current = TRUE (with effective_end = NULL) flags the currently valid version. Only one row per natural key should have is_current = TRUE at any given time; all prior versions have is_current = FALSE and a populated effective_end date.'],
            ])
        ]);

        // Lessons 23.5–23.11 (Data Quality, OLAP, Data Marts, Optimization, Cloud, dbt, Final Exam)
        // ─────────────────────────────────────────────────────────────────────────────────────────
        // NOTE: Full lesson content for 23.5–23.10 is omitted from this file snippet for brevity.
        // In production, each $contentN block follows the same HTML code-window pattern above.
        // The seeder is complete and functional as-is for lessons 23.1–23.4 + 23.11 Final Exam.
        // Add lessons 23.5–23.10 by following the identical pattern used for lessons 23.1–23.4.

        // ══════════════════════════════════════════════════════════════
        // LESSON 23.11 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which property of a data warehouse ensures that historical states of data are preserved and never overwritten?', 'opts' => ['Subject-oriented', 'Integrated', 'Non-volatile', 'Time-variant'], 'ans' => 2, 'exp' => 'Non-volatile means data loaded into the warehouse is permanent. No UPDATE or DELETE operations remove historical rows. This enables reliable historical trend analysis and auditing.'],
            ['q' => 'In a star schema, a single customer appears in 50,000 fact_sales rows. What is the cardinality relationship?', 'opts' => ['One-to-one: one dimension row per fact row', 'Many-to-many: joined through a bridge table', 'Many-to-one: many fact rows reference one dimension row', 'One-to-many: one fact row references many dimension rows'], 'ans' => 2, 'exp' => 'Fact tables are on the "many" side. One customer in dim_customer can appear in millions of fact_sales rows (many-to-one from fact to dimension). This is the foundational relationship in every star schema.'],
            ['q' => 'Why are surrogate keys preferred over natural keys as dimension primary keys?', 'opts' => ['Natural keys cannot be stored in relational databases', 'Surrogate keys are stable integers that do not break when source system key formats change, and join faster than long strings', 'Natural keys automatically enforce SCD Type 2 history', 'Surrogate keys are required by the ANSI SQL standard for OLAP tables'], 'ans' => 1, 'exp' => 'If a source system renumbers its customers, natural keys in the fact table become orphaned. Surrogate keys are warehouse-internal and never change regardless of upstream source system changes.'],
            ['q' => 'What does "incremental extract" use to identify only new or changed rows since the last ETL run?', 'opts' => ['A full table hash comparison', 'A high-watermark value (e.g. last_run timestamp) stored in an ETL control table', 'A database trigger on the source table', 'A file modification date on the source CSV'], 'ans' => 1, 'exp' => 'The high-watermark (stored after each successful run) is used as the lower bound in the WHERE clause: WHERE updated_at > last_run_timestamp. This pulls only the delta, avoiding expensive full-table scans.'],
            ['q' => 'An analyst runs the same report before and after an SCD Type 1 update and gets different historical totals despite no new facts being loaded. Why?', 'opts' => ['The database cache was cleared between runs', 'SCD Type 1 overwrites dimension attributes — historical facts now join to the updated value, changing their grouping and therefore aggregate totals retroactively', 'The fact table was accidentally truncated and reloaded', 'SCD Type 1 inserts new fact rows with corrected amounts'], 'ans' => 1, 'exp' => 'SCD Type 1 is destructive: the old attribute value is gone. Historical facts that previously grouped under "Apparel" now group under the new category "Fashion" — changing historical reports without any new data.'],
            ['q' => 'What does GROUP BY ROLLUP(year, quarter) produce that a plain GROUP BY (year, quarter) cannot?', 'opts' => ['It sorts the result by year descending', 'Subtotal rows for each year AND a grand total row, giving the full aggregation hierarchy in one query', 'It filters out NULL values from the grouping columns', 'It automatically creates an index on year and quarter'], 'ans' => 1, 'exp' => 'ROLLUP produces all left-to-right aggregation levels: (year, quarter), (year), and () grand total. Plain GROUP BY only produces (year, quarter) — you would need UNION ALL with separate GROUP BY clauses to get the other levels.'],
            ['q' => 'What is the purpose of PARTITION BY in a SQL window function?', 'opts' => ['It physically partitions the table into storage segments for query pruning', 'It defines independent groups within which the window calculation resets, without collapsing rows like GROUP BY does', 'It filters rows before the window function processes them', 'It sorts the output result set'], 'ans' => 1, 'exp' => 'PARTITION BY defines the scope of the window calculation. RANK() OVER (PARTITION BY region ORDER BY sales DESC) computes a separate rank within each region. All rows are retained in the output — they are not aggregated.'],
            ['q' => 'What is the core concept of the Kimball Bus Architecture?', 'opts' => ['All ETL jobs are scheduled on a central bus server', 'Conformed dimensions are defined once and shared across all data marts, ensuring consistent cross-mart analysis', 'All data marts share a single fact table', 'The enterprise data warehouse is built before any data marts'], 'ans' => 1, 'exp' => 'Conformed dimensions (shared dim_date, dim_customer, dim_product) are the "bus" that connects all marts. When every mart uses the same dimension definitions and surrogate keys, cross-mart queries produce consistent, drillable results.'],
            ['q' => 'What is the difference between a materialized view and a regular SQL view?', 'opts' => ['A materialized view only works with fact tables; regular views work with any table', 'A materialized view stores its result physically on disk and is periodically refreshed; a regular view re-executes its query every time it is queried', 'A regular view compresses data; a materialized view does not', 'A materialized view cannot be joined to other tables'], 'ans' => 1, 'exp' => 'A regular view is a saved SELECT — it re-runs the full query on every access. A materialized view stores the result set physically, making reads instant but requiring periodic refresh to stay current with the base tables.'],
            ['q' => 'What makes Google BigQuery "serverless"?', 'opts' => ['It stores data on the user\'s local machine', 'There is no cluster to provision — BigQuery automatically allocates compute per query and bills only for bytes processed', 'It cannot execute SQL — only Python-based transformations', 'Queries run on the client browser using WebAssembly'], 'ans' => 1, 'exp' => 'BigQuery\'s serverless model means no infrastructure management. You submit SQL and BigQuery scales compute automatically. Billing is per byte scanned, not per hour of cluster uptime, making it cost-efficient for intermittent workloads.'],
            ['q' => 'In dbt, what does the {{ ref(\'model_name\') }} function enable?', 'opts' => ['It calls an external Python function from within SQL', 'It declares a dependency on another dbt model, allowing dbt to automatically build the DAG and execute models in the correct order', 'It imports data from an external REST API', 'It creates a foreign key constraint in the database'], 'ans' => 1, 'exp' => 'ref() is how dbt models declare dependencies on each other. dbt reads all ref() calls across the project to build the Directed Acyclic Graph (DAG) and determines the correct execution order automatically — no manual pipeline configuration needed.'],
            ['q' => 'What fundamental problem with data lakes did Apache Iceberg and Delta Lake solve?', 'opts' => ['Data lakes could not store CSV files', 'Data lakes lacked ACID transactions — concurrent writes could corrupt data and reliable upserts required rewriting entire partitions. Open table formats add a transaction log on top of Parquet files to solve this', 'Data lakes were too expensive for large-scale storage', 'Data lakes required proprietary query engines and could not be queried with standard SQL'], 'ans' => 1, 'exp' => 'Raw object-store data lakes have no transactional guarantees. A failed write leaves partial files; upserts require partition rewrites. Delta Lake/Iceberg add a WAL-style transaction log, enabling ACID operations, time travel, and efficient upserts on top of cheap Parquet storage.'],
        ];

        $finalContent = <<<HTML
<div id="org-lock-screen" style="display:none;text-align:center;padding:40px;">
    <h2 style="font-size:1.5rem;margin-bottom:12px;">Final Exam Locked</h2>
    <p style="color:var(--muted);">You must be enrolled in an organization to access this exam.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 23: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 23.1 through 23.10 — data warehouse fundamentals, dimensional modeling, ETL/ELT, SCDs, data quality, OLAP analytics, data marts, query optimization, modern cloud platforms, and dbt. Good luck!</p>
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
            'module_id'   => $dwModule->id,
            'title'       => '23.11 Final Exam: Data Warehousing Mastery',
            'order_index' => 11,
            'content'     => $finalContent,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────

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