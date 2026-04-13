<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;
use App\Models\Lesson;

/**
 * Module10LessonsSeeder
 * Seeds lessons for Module 10: Database Management for Data Science.
 * Run AFTER CurriculumSeeder which creates the modules.
 *
 * Usage:  php artisan db:seed --class=Module10LessonsSeeder
 */
class Module10LessonsSeeder extends Seeder
{
    public function run()
    {
        $dbModule = Module::where('order_index', 10)->firstOrFail();
        Lesson::where('module_id', $dbModule->id)->delete();

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.1 — Relational Database Fundamentals
        // ══════════════════════════════════════════════════════════════
        $content1 = <<<'HTML'
<h2>Relational Database Fundamentals</h2>
<p>A <strong>relational database</strong> is a structured collection of data organized into <em>tables</em> (also called <em>relations</em>), where each table represents a real-world entity. Every row in a table is a <strong>record</strong>, and every column is an <strong>attribute</strong>. The relational model was formalized by Edgar F. Codd at IBM in 1970, and it remains the dominant data storage paradigm in enterprise systems, web applications, and data pipelines to this day.</p>

<p>As a data scientist, the vast majority of production data you will work with lives inside a relational database — whether that is PostgreSQL at a startup, MySQL in a legacy system, or a cloud warehouse like BigQuery or Redshift. Knowing how to extract, join, and aggregate this data using SQL is as fundamental as knowing how to use Pandas.</p>

<h3>Core Concepts: Tables, Rows & Columns</h3>
<p>Think of a relational database as a collection of spreadsheets that are <em>linked together by shared keys</em>. Unlike a single flat spreadsheet, a database splits information into logical units to avoid redundancy and ensure consistency — a principle called <strong>normalization</strong>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Table Structure</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- A table called "customers" with 4 columns</span>
<span style="color:#6b7280;">-- Each row = one customer record</span>

<span style="color:#6b7280;">-- customer_id | name            | email                    | signup_date</span>
<span style="color:#6b7280;">-- -----------   ---------------   ------------------------   -----------</span>
<span style="color:#6b7280;">-- 1           | Alice Santos    | alice@example.com        | 2023-01-10</span>
<span style="color:#6b7280;">-- 2           | Bob Reyes       | bob@example.com          | 2023-02-14</span>
<span style="color:#6b7280;">-- 3           | Carmen Diaz     | carmen@example.com       | 2023-03-05</span>

<span style="color:#c4b5fd;">CREATE TABLE</span> customers (
    customer_id   <span style="color:#93c5fd;">INT</span>          <span style="color:#c4b5fd;">PRIMARY KEY</span>,
    name          <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">100</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    email         <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">150</span>) <span style="color:#c4b5fd;">UNIQUE NOT NULL</span>,
    signup_date   <span style="color:#93c5fd;">DATE</span>
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>Table 'customers' created successfully.</div>
  </div>
</div>

<h3>Primary Keys & Foreign Keys</h3>
<p>Every well-designed table has a <strong>primary key</strong> — a column (or combination of columns) that uniquely identifies each row. No two rows can share the same primary key value, and it can never be NULL. A <strong>foreign key</strong> is a column in one table that references the primary key of another — this is how tables are <em>related</em> to each other, hence the name "relational" database.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Primary & Foreign Keys</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- The "orders" table references "customers" via a foreign key</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> orders (
    order_id      <span style="color:#93c5fd;">INT</span>            <span style="color:#c4b5fd;">PRIMARY KEY</span>,
    customer_id   <span style="color:#93c5fd;">INT</span>            <span style="color:#c4b5fd;">NOT NULL</span>,
    order_date    <span style="color:#93c5fd;">DATE</span>           <span style="color:#c4b5fd;">NOT NULL</span>,
    total_amount  <span style="color:#93c5fd;">DECIMAL</span>(<span style="color:#fcd34d;">10</span>,<span style="color:#fcd34d;">2</span>),

    <span style="color:#6b7280;">-- This FOREIGN KEY enforces referential integrity:</span>
    <span style="color:#6b7280;">-- you cannot insert an order for a customer_id that does not exist</span>
    <span style="color:#c4b5fd;">FOREIGN KEY</span> (customer_id) <span style="color:#c4b5fd;">REFERENCES</span> customers(customer_id)
        <span style="color:#c4b5fd;">ON DELETE CASCADE</span>   <span style="color:#6b7280;">-- if the customer is deleted, their orders are too</span>
        <span style="color:#c4b5fd;">ON UPDATE CASCADE</span>   <span style="color:#6b7280;">-- if the customer_id changes, it updates here too</span>
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>Table 'orders' created with referential integrity constraint.</div>
  </div>
</div>

<h3>RDBMS Software: PostgreSQL, MySQL, SQLite</h3>
<p>A <strong>Relational Database Management System (RDBMS)</strong> is the software that runs the database engine. The most common ones in data science are:</p>
<ul style="line-height:2;">
  <li><strong>PostgreSQL</strong> — open-source, extremely powerful, used in most modern data pipelines and analytics stacks. Supports JSON, arrays, and advanced window functions.</li>
  <li><strong>MySQL / MariaDB</strong> — extremely popular in web applications (WordPress, Shopify). Good for transactional data.</li>
  <li><strong>SQLite</strong> — a file-based database with no server required. Perfect for local development, mobile apps, and small datasets. Python's <code>sqlite3</code> module is built-in.</li>
  <li><strong>SQL Server / Oracle</strong> — enterprise systems you'll encounter in legacy corporate environments.</li>
  <li><strong>BigQuery / Redshift / Snowflake</strong> — cloud-native data warehouses built on SQL but optimized for analytical queries over billions of rows.</li>
</ul>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Connect to SQLite</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sqlite3

<span style="color:#6b7280;"># Connect to (or create) a database file in the current directory</span>
conn = sqlite3.<span style="color:#93c5fd;">connect</span>(<span style="color:#a7f3d0;">"data_science.db"</span>)
cursor = conn.<span style="color:#93c5fd;">cursor</span>()

<span style="color:#6b7280;"># Execute a SQL statement</span>
cursor.<span style="color:#93c5fd;">execute</span>(<span style="color:#a7f3d0;">"""
    CREATE TABLE IF NOT EXISTS students (
        student_id  INTEGER PRIMARY KEY AUTOINCREMENT,
        name        TEXT    NOT NULL,
        score       REAL,
        enrolled    TEXT
    )
"""</span>)

conn.<span style="color:#93c5fd;">commit</span>()  <span style="color:#6b7280;"># Save changes</span>
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Database and table created successfully!"</span>)
conn.<span style="color:#93c5fd;">close</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Database and table created successfully!</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.1 Relational Database Fundamentals',
            'order_index' => 1,
            'content'     => $this->appendQuiz($content1, 'L10_1', [
                ['q' => 'What does a PRIMARY KEY guarantee in a relational table?', 'opts' => ['Each row has a non-null, unique identifier', 'The column is automatically indexed by name', 'All values in the column must be integers', 'The column links to another table'], 'ans' => 0, 'exp' => 'A PRIMARY KEY uniquely identifies every row in a table. It cannot be NULL and no two rows can share the same primary key value.'],
                ['q' => 'What is a FOREIGN KEY used for?', 'opts' => ['To encrypt sensitive columns', 'To link a column in one table to the primary key of another table', 'To speed up SELECT queries', 'To prevent duplicate rows in the same table'], 'ans' => 1, 'exp' => 'A FOREIGN KEY establishes a referential relationship between two tables, enforcing that values in one table must exist as primary keys in the referenced table.'],
                ['q' => 'Which Python module provides built-in SQLite support with no installation needed?', 'opts' => ['pandas', 'sqlalchemy', 'sqlite3', 'psycopg2'], 'ans' => 2, 'exp' => 'sqlite3 is part of Python\'s standard library — no pip install required. It lets you create, read, and manage SQLite database files directly from Python.'],
                ['q' => 'What does ON DELETE CASCADE mean on a foreign key constraint?', 'opts' => ['The child row is set to NULL when the parent is deleted', 'Deleting the parent row automatically deletes all related child rows', 'The delete operation is blocked if child rows exist', 'A backup of the deleted row is archived'], 'ans' => 1, 'exp' => 'ON DELETE CASCADE automatically deletes all child rows in the referencing table when the parent row they reference is deleted, maintaining referential integrity.'],
                ['q' => 'Which of the following is a cloud-native analytical data warehouse?', 'opts' => ['SQLite', 'MySQL', 'BigQuery', 'MS Access'], 'ans' => 2, 'exp' => 'BigQuery (Google Cloud), Redshift (AWS), and Snowflake are cloud-native data warehouses designed for analytical queries at petabyte scale. SQLite and MySQL are traditional RDBMS solutions.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.2 — SQL Basics: SELECT, WHERE, ORDER BY, LIMIT
        // ══════════════════════════════════════════════════════════════
        $content2 = <<<'HTML'
<h2>SQL Basics: Querying Data</h2>
<p><strong>SQL (Structured Query Language)</strong> is the universal language for communicating with relational databases. Despite being over 50 years old, it remains the single most important skill for a data scientist working with structured data. Every data extraction task — whether you're pulling last month's sales, filtering user cohorts, or computing average transaction values — starts with a SQL query.</p>

<p>SQL is <em>declarative</em>: you describe <strong>what</strong> you want, not <em>how</em> to get it. The database engine figures out the most efficient execution plan automatically.</p>

<h3>The SELECT Statement</h3>
<p>Every data retrieval query begins with <code>SELECT</code>. You specify which columns you want, then which table to pull from using <code>FROM</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — SELECT Basics</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Select ALL columns from the customers table</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> customers;

<span style="color:#6b7280;">-- Select only specific columns (best practice — avoid SELECT * in production)</span>
<span style="color:#c4b5fd;">SELECT</span> name, email <span style="color:#c4b5fd;">FROM</span> customers;

<span style="color:#6b7280;">-- Use aliases to rename columns in the output</span>
<span style="color:#c4b5fd;">SELECT</span>
    customer_id  <span style="color:#c4b5fd;">AS</span> id,
    name         <span style="color:#c4b5fd;">AS</span> full_name,
    signup_date  <span style="color:#c4b5fd;">AS</span> joined
<span style="color:#c4b5fd;">FROM</span> customers;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result (sample)</span>id | full_name      | joined
---+-----------------+------------
1  | Alice Santos   | 2023-01-10
2  | Bob Reyes      | 2023-02-14
3  | Carmen Diaz    | 2023-03-05</div>
  </div>
</div>

<h3>Filtering with WHERE</h3>
<p>The <code>WHERE</code> clause filters rows based on one or more conditions. Only rows that satisfy the condition are returned. You can combine multiple conditions using <code>AND</code>, <code>OR</code>, and <code>NOT</code>.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — WHERE Clause</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Orders with a total greater than 500</span>
<span style="color:#c4b5fd;">SELECT</span> order_id, total_amount <span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">WHERE</span> total_amount > <span style="color:#fcd34d;">500</span>;

<span style="color:#6b7280;">-- Combine conditions with AND / OR</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">WHERE</span> total_amount > <span style="color:#fcd34d;">100</span> <span style="color:#c4b5fd;">AND</span> order_date >= <span style="color:#a7f3d0;">'2024-01-01'</span>;

<span style="color:#6b7280;">-- BETWEEN is inclusive on both ends</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">WHERE</span> total_amount <span style="color:#c4b5fd;">BETWEEN</span> <span style="color:#fcd34d;">200</span> <span style="color:#c4b5fd;">AND</span> <span style="color:#fcd34d;">800</span>;

<span style="color:#6b7280;">-- IN checks against a list of values</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> customers
<span style="color:#c4b5fd;">WHERE</span> customer_id <span style="color:#c4b5fd;">IN</span> (<span style="color:#fcd34d;">1</span>, <span style="color:#fcd34d;">3</span>, <span style="color:#fcd34d;">7</span>, <span style="color:#fcd34d;">12</span>);

<span style="color:#6b7280;">-- LIKE for pattern matching (% = any number of chars, _ = one char)</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> customers
<span style="color:#c4b5fd;">WHERE</span> email <span style="color:#c4b5fd;">LIKE</span> <span style="color:#a7f3d0;">'%@gmail.com'</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result (LIKE query)</span>customer_id | name         | email
-------------+--------------+-------------------
2            | Bob Reyes    | bob@gmail.com
5            | Elena Cruz   | elena@gmail.com</div>
  </div>
</div>

<h3>Sorting with ORDER BY and Limiting Results</h3>
<p><code>ORDER BY</code> sorts the result set. <code>LIMIT</code> (or <code>TOP</code> in SQL Server) restricts how many rows are returned — critical when working with large tables to avoid pulling millions of rows accidentally.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — ORDER BY & LIMIT</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Top 5 highest-value orders</span>
<span style="color:#c4b5fd;">SELECT</span> order_id, customer_id, total_amount
<span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">ORDER BY</span> total_amount <span style="color:#c4b5fd;">DESC</span>
<span style="color:#c4b5fd;">LIMIT</span> <span style="color:#fcd34d;">5</span>;

<span style="color:#6b7280;">-- Sort by multiple columns: primary sort by date (newest first),</span>
<span style="color:#6b7280;">-- secondary sort alphabetically by customer name</span>
<span style="color:#c4b5fd;">SELECT</span> o.order_id, c.name, o.order_date, o.total_amount
<span style="color:#c4b5fd;">FROM</span> orders o
<span style="color:#c4b5fd;">JOIN</span> customers c <span style="color:#c4b5fd;">ON</span> o.customer_id = c.customer_id
<span style="color:#c4b5fd;">ORDER BY</span> o.order_date <span style="color:#c4b5fd;">DESC</span>, c.name <span style="color:#c4b5fd;">ASC</span>
<span style="color:#c4b5fd;">LIMIT</span> <span style="color:#fcd34d;">10</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>order_id | name          | order_date  | total_amount
----------+---------------+-------------+--------------
1045      | Alice Santos  | 2024-03-20  | 980.00
1032      | Bob Reyes     | 2024-03-18  | 745.50</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.2 SQL Basics: SELECT, WHERE, ORDER BY, LIMIT',
            'order_index' => 2,
            'content'     => $this->appendQuiz($content2, 'L10_2', [
                ['q' => 'Which SQL clause is used to filter rows based on a condition?', 'opts' => ['ORDER BY', 'HAVING', 'WHERE', 'LIMIT'], 'ans' => 2, 'exp' => 'WHERE filters rows before any grouping or aggregation. It is placed after FROM and evaluated row-by-row against each record.'],
                ['q' => 'What does SELECT * FROM orders do?', 'opts' => ['Deletes all rows from orders', 'Returns only the first row', 'Returns all columns and all rows from orders', 'Counts the rows in orders'], 'ans' => 2, 'exp' => 'SELECT * means "select all columns". Combined with FROM orders and no WHERE clause, it retrieves every column and every row in the table.'],
                ['q' => 'Which operator checks if a value falls within a range (inclusive)?', 'opts' => ['IN', 'LIKE', 'BETWEEN', 'EXISTS'], 'ans' => 2, 'exp' => 'BETWEEN x AND y returns rows where the value is >= x and <= y (inclusive on both ends).'],
                ['q' => 'What does ORDER BY total_amount DESC do?', 'opts' => ['Sorts from smallest to largest', 'Sorts from largest to smallest', 'Removes duplicate amounts', 'Groups by amount'], 'ans' => 1, 'exp' => 'DESC means descending — largest values first. ASC (ascending) is the default and returns smallest values first.'],
                ['q' => 'What is the purpose of LIMIT 10 in a SQL query?', 'opts' => ['Filters rows where value is less than 10', 'Restricts the result set to 10 rows maximum', 'Skips the first 10 rows', 'Groups results into sets of 10'], 'ans' => 1, 'exp' => 'LIMIT n caps the number of rows returned to at most n rows. It is critical when exploring large tables to avoid accidentally pulling millions of rows.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.3 — Aggregate Functions & GROUP BY
        // ══════════════════════════════════════════════════════════════
        $content3 = <<<'HTML'
<h2>Aggregate Functions & GROUP BY</h2>
<p>Aggregation is where SQL truly shines for data science. Instead of returning individual rows, aggregate functions <strong>collapse many rows into a single summary value</strong>. Combined with <code>GROUP BY</code>, you can compute metrics per category — per customer, per product, per month, per region — in a single efficient query that a database engine can run on billions of rows.</p>

<h3>The Five Core Aggregate Functions</h3>
<p>These functions are the backbone of every analytics query you will ever write:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Aggregate Functions</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">SELECT</span>
    <span style="color:#93c5fd;">COUNT</span>(*)                   <span style="color:#c4b5fd;">AS</span> total_orders,
    <span style="color:#93c5fd;">SUM</span>(total_amount)          <span style="color:#c4b5fd;">AS</span> total_revenue,
    <span style="color:#93c5fd;">AVG</span>(total_amount)          <span style="color:#c4b5fd;">AS</span> avg_order_value,
    <span style="color:#93c5fd;">MIN</span>(total_amount)          <span style="color:#c4b5fd;">AS</span> smallest_order,
    <span style="color:#93c5fd;">MAX</span>(total_amount)          <span style="color:#c4b5fd;">AS</span> largest_order
<span style="color:#c4b5fd;">FROM</span> orders;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>total_orders | total_revenue | avg_order_value | smallest_order | largest_order
--------------+---------------+-----------------+----------------+--------------
1250          | 187430.50     | 149.94          | 5.00           | 3200.00</div>
  </div>
</div>

<h3>GROUP BY — Per-Category Aggregation</h3>
<p><code>GROUP BY</code> splits rows into groups based on one or more columns, then applies the aggregate function <em>independently to each group</em>. Every column in the SELECT that is not inside an aggregate function <strong>must</strong> appear in the GROUP BY clause.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — GROUP BY</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Total revenue and order count PER CUSTOMER</span>
<span style="color:#c4b5fd;">SELECT</span>
    customer_id,
    <span style="color:#93c5fd;">COUNT</span>(*)         <span style="color:#c4b5fd;">AS</span> num_orders,
    <span style="color:#93c5fd;">SUM</span>(total_amount) <span style="color:#c4b5fd;">AS</span> lifetime_value,
    <span style="color:#93c5fd;">AVG</span>(total_amount) <span style="color:#c4b5fd;">AS</span> avg_spend
<span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">GROUP BY</span> customer_id
<span style="color:#c4b5fd;">ORDER BY</span> lifetime_value <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer_id | num_orders | lifetime_value | avg_spend
-------------+------------+----------------+-----------
3            | 28         | 14250.00       | 508.93
1            | 15         | 9800.50        | 653.37
2            | 10         | 3200.00        | 320.00</div>
  </div>
</div>

<h3>HAVING — Filtering Groups</h3>
<p><code>HAVING</code> is like <code>WHERE</code>, but it filters <em>after</em> grouping has occurred. Use <code>WHERE</code> to filter individual rows <em>before</em> grouping, and <code>HAVING</code> to filter the computed groups <em>after</em>. A classic mistake is using <code>WHERE</code> on an aggregate — that will always cause an error.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — HAVING Clause</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Find customers who placed MORE than 5 orders (HAVING filters groups)</span>
<span style="color:#c4b5fd;">SELECT</span>
    customer_id,
    <span style="color:#93c5fd;">COUNT</span>(*) <span style="color:#c4b5fd;">AS</span> num_orders,
    <span style="color:#93c5fd;">SUM</span>(total_amount) <span style="color:#c4b5fd;">AS</span> total_spent
<span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">WHERE</span> order_date >= <span style="color:#a7f3d0;">'2024-01-01'</span>   <span style="color:#6b7280;">-- WHERE filters rows BEFORE grouping</span>
<span style="color:#c4b5fd;">GROUP BY</span> customer_id
<span style="color:#c4b5fd;">HAVING</span> <span style="color:#93c5fd;">COUNT</span>(*) > <span style="color:#fcd34d;">5</span>              <span style="color:#6b7280;">-- HAVING filters groups AFTER aggregation</span>
<span style="color:#c4b5fd;">ORDER BY</span> total_spent <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer_id | num_orders | total_spent
-------------+------------+-------------
3            | 12         | 6430.00
7            | 8          | 4100.00</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.3 Aggregate Functions & GROUP BY',
            'order_index' => 3,
            'content'     => $this->appendQuiz($content3, 'L10_3', [
                ['q' => 'Which function returns the number of rows in a group?', 'opts' => ['SUM()', 'TOTAL()', 'COUNT()', 'NUM()'], 'ans' => 2, 'exp' => 'COUNT(*) counts all rows including NULLs. COUNT(column_name) counts only non-NULL values in that column.'],
                ['q' => 'What is the key difference between WHERE and HAVING?', 'opts' => ['WHERE is used with JOINs; HAVING is used without', 'WHERE filters rows before grouping; HAVING filters groups after aggregation', 'WHERE works on strings only; HAVING works on numbers', 'They are interchangeable'], 'ans' => 1, 'exp' => 'WHERE is evaluated before GROUP BY and filters individual rows. HAVING is evaluated after GROUP BY and filters the resulting groups. You cannot use aggregate functions in WHERE.'],
                ['q' => 'If you SELECT customer_id, COUNT(*) FROM orders GROUP BY customer_id — what does each row in the result represent?', 'opts' => ['One order record', 'One customer and their total number of orders', 'One product category', 'A summary of all orders combined'], 'ans' => 1, 'exp' => 'GROUP BY customer_id creates one output row per unique customer_id, and COUNT(*) tallies how many rows exist in each group (how many orders that customer placed).'],
                ['q' => 'What error occurs if you write: SELECT customer_id, COUNT(*) FROM orders WHERE COUNT(*) > 5 GROUP BY customer_id?', 'opts' => ['Wrong column count', 'Syntax error: aggregate function in WHERE clause', 'Missing semicolon', 'Cannot use COUNT with GROUP BY'], 'ans' => 1, 'exp' => 'Aggregate functions like COUNT() cannot be used in a WHERE clause. Use HAVING after GROUP BY instead to filter by aggregated values.'],
                ['q' => 'Which aggregate function returns the single largest value in a column?', 'opts' => ['TOP()', 'GREATEST()', 'MAX()', 'UPPER()'], 'ans' => 2, 'exp' => 'MAX(column) returns the highest value in that column across all rows (or within a group when used with GROUP BY). MIN() is the counterpart for the smallest value.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.4 — JOINs: INNER, LEFT, RIGHT, FULL
        // ══════════════════════════════════════════════════════════════
        $content4 = <<<'HTML'
<h2>JOINs: Combining Tables</h2>
<p>In a normalized relational database, related data is split across multiple tables. A <strong>JOIN</strong> combines rows from two or more tables based on a shared column — typically a foreign key relationship. JOINs are arguably the most important SQL concept for data scientists: most real-world analytical queries require pulling data from at least two tables.</p>

<h3>INNER JOIN — Only Matching Rows</h3>
<p>An <code>INNER JOIN</code> returns only rows that have a match in <em>both</em> tables. Rows that exist in one table but not the other are excluded from the result. This is the most common join type.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — INNER JOIN</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Get each order alongside its customer's name and email</span>
<span style="color:#6b7280;">-- Only orders that have a matching customer are returned</span>
<span style="color:#c4b5fd;">SELECT</span>
    o.order_id,
    c.name         <span style="color:#c4b5fd;">AS</span> customer_name,
    c.email,
    o.order_date,
    o.total_amount
<span style="color:#c4b5fd;">FROM</span> orders o
<span style="color:#c4b5fd;">INNER JOIN</span> customers c <span style="color:#c4b5fd;">ON</span> o.customer_id = c.customer_id
<span style="color:#c4b5fd;">ORDER BY</span> o.order_date <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>order_id | customer_name | email               | order_date  | total_amount
----------+---------------+---------------------+-------------+--------------
1045      | Alice Santos  | alice@example.com   | 2024-03-20  | 980.00
1032      | Bob Reyes     | bob@example.com     | 2024-03-18  | 745.50</div>
  </div>
</div>

<h3>LEFT JOIN — All Left Rows + Matching Right</h3>
<p>A <code>LEFT JOIN</code> returns <em>all</em> rows from the left table, and the matching rows from the right table. If there is no match on the right side, the right columns are filled with <code>NULL</code>. This is commonly used to find records that have <em>no</em> related records — for example, customers who have never placed an order.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — LEFT JOIN</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Find all customers, including those with NO orders</span>
<span style="color:#c4b5fd;">SELECT</span>
    c.customer_id,
    c.name,
    <span style="color:#93c5fd;">COUNT</span>(o.order_id) <span style="color:#c4b5fd;">AS</span> num_orders
<span style="color:#c4b5fd;">FROM</span> customers c
<span style="color:#c4b5fd;">LEFT JOIN</span> orders o <span style="color:#c4b5fd;">ON</span> c.customer_id = o.customer_id
<span style="color:#c4b5fd;">GROUP BY</span> c.customer_id, c.name
<span style="color:#c4b5fd;">ORDER BY</span> num_orders;

<span style="color:#6b7280;">-- Classic pattern: find customers who NEVER ordered</span>
<span style="color:#c4b5fd;">SELECT</span> c.customer_id, c.name
<span style="color:#c4b5fd;">FROM</span> customers c
<span style="color:#c4b5fd;">LEFT JOIN</span> orders o <span style="color:#c4b5fd;">ON</span> c.customer_id = o.customer_id
<span style="color:#c4b5fd;">WHERE</span> o.order_id <span style="color:#c4b5fd;">IS NULL</span>;   <span style="color:#6b7280;">-- NULL means no matching order exists</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result (never ordered)</span>customer_id | name
-------------+--------------
4            | David Lim
8            | Fiona Tan</div>
  </div>
</div>

<h3>Joining Three Tables</h3>
<p>Real queries often join three or more tables. You simply chain multiple JOIN clauses. The engine processes them left to right, building an intermediate result set at each step.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Three-Table JOIN</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- customers → orders → order_items → products</span>
<span style="color:#c4b5fd;">SELECT</span>
    c.name               <span style="color:#c4b5fd;">AS</span> customer,
    p.product_name,
    oi.quantity,
    oi.unit_price,
    (oi.quantity * oi.unit_price) <span style="color:#c4b5fd;">AS</span> line_total
<span style="color:#c4b5fd;">FROM</span> customers c
<span style="color:#c4b5fd;">INNER JOIN</span> orders o      <span style="color:#c4b5fd;">ON</span> c.customer_id  = o.customer_id
<span style="color:#c4b5fd;">INNER JOIN</span> order_items oi <span style="color:#c4b5fd;">ON</span> o.order_id      = oi.order_id
<span style="color:#c4b5fd;">INNER JOIN</span> products p     <span style="color:#c4b5fd;">ON</span> oi.product_id   = p.product_id
<span style="color:#c4b5fd;">WHERE</span> c.name = <span style="color:#a7f3d0;">'Alice Santos'</span>
<span style="color:#c4b5fd;">ORDER BY</span> o.order_date <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer      | product_name  | quantity | unit_price | line_total
---------------+---------------+----------+------------+-----------
Alice Santos  | Laptop Pro    | 1        | 890.00     | 890.00
Alice Santos  | USB Hub       | 2        | 45.00      | 90.00</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.4 JOINs: INNER, LEFT, RIGHT, FULL',
            'order_index' => 4,
            'content'     => $this->appendQuiz($content4, 'L10_4', [
                ['q' => 'What does an INNER JOIN return?', 'opts' => ['All rows from the left table only', 'All rows from both tables regardless of match', 'Only rows that have a matching record in both tables', 'All rows from the right table only'], 'ans' => 2, 'exp' => 'INNER JOIN is the most restrictive join — it only returns rows where the JOIN condition is satisfied in both tables. Rows without a match on either side are excluded.'],
                ['q' => 'You LEFT JOIN customers to orders. A customer with no orders appears in the result — what values do the orders columns contain?', 'opts' => ['0', 'Empty string', 'NULL', 'The row is excluded'], 'ans' => 2, 'exp' => 'A LEFT JOIN preserves all rows from the left table. When no matching row exists in the right table, all right-side columns are filled with NULL.'],
                ['q' => 'What is the classic use of LEFT JOIN ... WHERE right_table.id IS NULL?', 'opts' => ['To find duplicate records', 'To find rows in the left table with no match in the right table', 'To get all rows from both tables', 'To replace NULLs with zeros'], 'ans' => 1, 'exp' => 'This pattern identifies records in the left table that have no corresponding record in the right table — e.g., customers who have never ordered, or products with no sales.'],
                ['q' => 'In the query: FROM orders o INNER JOIN customers c ON o.customer_id = c.customer_id — what do "o" and "c" represent?', 'opts' => ['Column names', 'Table aliases (short names for the tables)', 'Database names', 'Schema names'], 'ans' => 1, 'exp' => 'Table aliases (o for orders, c for customers) make queries shorter and easier to read, especially when joining many tables. They are defined immediately after the table name in the FROM/JOIN clause.'],
                ['q' => 'How many JOIN clauses are needed to combine 4 tables?', 'opts' => ['1', '2', '3', '4'], 'ans' => 2, 'exp' => 'To combine n tables you need n-1 JOIN clauses. For 4 tables: FROM table1 JOIN table2 ... JOIN table3 ... JOIN table4 — that is 3 JOINs.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.5 — Subqueries & CTEs
        // ══════════════════════════════════════════════════════════════
        $content5 = <<<'HTML'
<h2>Subqueries & Common Table Expressions (CTEs)</h2>
<p>As your SQL queries grow in complexity, you need tools to break them into readable, reusable steps. <strong>Subqueries</strong> (also called nested queries or inner queries) and <strong>CTEs</strong> (Common Table Expressions, written using the <code>WITH</code> keyword) are the two primary approaches. Every senior data analyst and data engineer uses them constantly.</p>

<h3>Subqueries — Queries Inside Queries</h3>
<p>A subquery is a complete SQL query nested inside another query. It can appear in the <code>WHERE</code>, <code>FROM</code>, or <code>SELECT</code> clause. The inner query runs first, and its result is used by the outer query.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Subquery in WHERE</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Find orders with a total ABOVE the average order value</span>
<span style="color:#c4b5fd;">SELECT</span> order_id, customer_id, total_amount
<span style="color:#c4b5fd;">FROM</span> orders
<span style="color:#c4b5fd;">WHERE</span> total_amount > (
    <span style="color:#c4b5fd;">SELECT</span> <span style="color:#93c5fd;">AVG</span>(total_amount) <span style="color:#c4b5fd;">FROM</span> orders   <span style="color:#6b7280;">-- inner query calculates the average</span>
)
<span style="color:#c4b5fd;">ORDER BY</span> total_amount <span style="color:#c4b5fd;">DESC</span>;

<span style="color:#6b7280;">-- Find customers who have NEVER placed an order using NOT IN</span>
<span style="color:#c4b5fd;">SELECT</span> customer_id, name
<span style="color:#c4b5fd;">FROM</span> customers
<span style="color:#c4b5fd;">WHERE</span> customer_id <span style="color:#c4b5fd;">NOT IN</span> (
    <span style="color:#c4b5fd;">SELECT</span> <span style="color:#c4b5fd;">DISTINCT</span> customer_id <span style="color:#c4b5fd;">FROM</span> orders
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result (above average)</span>order_id | customer_id | total_amount
----------+-------------+--------------
1045      | 1           | 980.00
1032      | 3           | 745.50
1018      | 7           | 620.00</div>
  </div>
</div>

<h3>Derived Tables — Subqueries in FROM</h3>
<p>You can use a subquery in the <code>FROM</code> clause as if it were a table — called a <strong>derived table</strong>. This allows you to filter, aggregate, or transform data first, then query the result. Every derived table must be given an alias.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Derived Table</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- First aggregate orders per customer, then filter by high spenders</span>
<span style="color:#c4b5fd;">SELECT</span> customer_id, total_spent
<span style="color:#c4b5fd;">FROM</span> (
    <span style="color:#c4b5fd;">SELECT</span> customer_id, <span style="color:#93c5fd;">SUM</span>(total_amount) <span style="color:#c4b5fd;">AS</span> total_spent
    <span style="color:#c4b5fd;">FROM</span> orders
    <span style="color:#c4b5fd;">GROUP BY</span> customer_id
) <span style="color:#c4b5fd;">AS</span> customer_totals        <span style="color:#6b7280;">-- derived table MUST have an alias</span>
<span style="color:#c4b5fd;">WHERE</span> total_spent > <span style="color:#fcd34d;">5000</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer_id | total_spent
-------------+-------------
3            | 14250.00
1            | 9800.50</div>
  </div>
</div>

<h3>CTEs with WITH — Readable Multi-Step Queries</h3>
<p>A <strong>CTE (Common Table Expression)</strong> uses the <code>WITH</code> keyword to define a named temporary result set at the <em>top</em> of the query. The CTE is then referenced by name below, just like a real table. CTEs make complex queries dramatically more readable and maintainable — you define each step once and reuse it. They also make it easy to chain multiple transformations step by step, something impossible to do cleanly with nested subqueries.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — CTEs with WITH</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Step 1: Summarize orders per customer</span>
<span style="color:#c4b5fd;">WITH</span> customer_summary <span style="color:#c4b5fd;">AS</span> (
    <span style="color:#c4b5fd;">SELECT</span>
        customer_id,
        <span style="color:#93c5fd;">COUNT</span>(*) <span style="color:#c4b5fd;">AS</span> num_orders,
        <span style="color:#93c5fd;">SUM</span>(total_amount) <span style="color:#c4b5fd;">AS</span> lifetime_value
    <span style="color:#c4b5fd;">FROM</span> orders
    <span style="color:#c4b5fd;">GROUP BY</span> customer_id
),

<span style="color:#6b7280;">-- Step 2: Classify customers into tiers</span>
customer_tiers <span style="color:#c4b5fd;">AS</span> (
    <span style="color:#c4b5fd;">SELECT</span>
        cs.customer_id,
        c.name,
        cs.lifetime_value,
        <span style="color:#c4b5fd;">CASE</span>
            <span style="color:#c4b5fd;">WHEN</span> cs.lifetime_value >= <span style="color:#fcd34d;">10000</span> <span style="color:#c4b5fd;">THEN</span> <span style="color:#a7f3d0;">'Platinum'</span>
            <span style="color:#c4b5fd;">WHEN</span> cs.lifetime_value >= <span style="color:#fcd34d;">5000</span>  <span style="color:#c4b5fd;">THEN</span> <span style="color:#a7f3d0;">'Gold'</span>
            <span style="color:#c4b5fd;">ELSE</span> <span style="color:#a7f3d0;">'Silver'</span>
        <span style="color:#c4b5fd;">END</span> <span style="color:#c4b5fd;">AS</span> tier
    <span style="color:#c4b5fd;">FROM</span> customer_summary cs
    <span style="color:#c4b5fd;">JOIN</span> customers c <span style="color:#c4b5fd;">ON</span> cs.customer_id = c.customer_id
)

<span style="color:#6b7280;">-- Final query uses the CTE results</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> customer_tiers
<span style="color:#c4b5fd;">ORDER BY</span> lifetime_value <span style="color:#c4b5fd;">DESC</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer_id | name          | lifetime_value | tier
-------------+---------------+----------------+---------
3            | Carmen Diaz   | 14250.00       | Platinum
1            | Alice Santos  | 9800.50        | Gold
2            | Bob Reyes     | 3200.00        | Silver</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.5 Subqueries & CTEs',
            'order_index' => 5,
            'content'     => $this->appendQuiz($content5, 'L10_5', [
                ['q' => 'A subquery in the WHERE clause is executed...', 'opts' => ['After the outer query', 'At the same time as the outer query in parallel', 'Before the outer query', 'Only when the result is more than 1 row'], 'ans' => 2, 'exp' => 'The inner (sub)query is evaluated first. Its result is then passed to the outer query as a value or set of values to filter against.'],
                ['q' => 'What keyword is used to define a CTE?', 'opts' => ['DEFINE', 'DECLARE', 'WITH', 'SET'], 'ans' => 2, 'exp' => 'CTEs are introduced with the WITH keyword: WITH cte_name AS (SELECT ...). The CTE can then be referenced by name in the main query below.'],
                ['q' => 'What is required when using a subquery in the FROM clause?', 'opts' => ['It must be wrapped in parentheses and given an alias', 'It must return only one column', 'It must be sorted with ORDER BY', 'It cannot use GROUP BY'], 'ans' => 0, 'exp' => 'Subqueries in the FROM clause are called derived tables. They must be enclosed in parentheses and must be given an alias (e.g., AS customer_totals) or the query will throw a syntax error.'],
                ['q' => 'What is the main advantage of a CTE over a deeply nested subquery?', 'opts' => ['CTEs run faster', 'CTEs can be reused multiple times and make the query much more readable', 'CTEs store data permanently', 'CTEs allow more join types'], 'ans' => 1, 'exp' => 'CTEs improve readability dramatically by naming intermediate steps. A CTE can also be referenced multiple times within the same query, unlike a subquery which would need to be written out again.'],
                ['q' => 'Can you define multiple CTEs in a single WITH clause?', 'opts' => ['No, only one CTE per query', 'Yes, separate them with commas', 'Yes, but only if they do not reference each other', 'Yes, using semicolons between them'], 'ans' => 1, 'exp' => 'Multiple CTEs are separated by commas: WITH cte1 AS (...), cte2 AS (...) SELECT ... . Each subsequent CTE can even reference a previously defined CTE in the same WITH block.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.6 — Window Functions
        // ══════════════════════════════════════════════════════════════
        $content6 = <<<'HTML'
<h2>Window Functions</h2>
<p><strong>Window functions</strong> are one of the most powerful features of modern SQL, and they are absolutely essential for data science. Unlike aggregate functions (which collapse many rows into one), window functions compute a value <em>for each row</em> while still being able to look across a set of related rows — called the "window". The result is added as a new column alongside the original row, without reducing the row count.</p>

<p>Window functions are used for running totals, moving averages, rankings, percentiles, and lead/lag comparisons — things that are either very hard or impossible to do with plain GROUP BY.</p>

<h3>Syntax: OVER() Clause</h3>
<p>Every window function uses the <code>OVER()</code> clause to define the window. <code>PARTITION BY</code> divides rows into groups (like GROUP BY, but without collapsing). <code>ORDER BY</code> inside OVER defines the sequence within each partition.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — ROW_NUMBER, RANK, DENSE_RANK</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Rank customers by total spending within their region</span>
<span style="color:#c4b5fd;">SELECT</span>
    customer_id,
    region,
    lifetime_value,
    <span style="color:#93c5fd;">ROW_NUMBER</span>() <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">PARTITION BY</span> region <span style="color:#c4b5fd;">ORDER BY</span> lifetime_value <span style="color:#c4b5fd;">DESC</span>) <span style="color:#c4b5fd;">AS</span> row_num,
    <span style="color:#93c5fd;">RANK</span>()       <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">PARTITION BY</span> region <span style="color:#c4b5fd;">ORDER BY</span> lifetime_value <span style="color:#c4b5fd;">DESC</span>) <span style="color:#c4b5fd;">AS</span> rank_val,
    <span style="color:#93c5fd;">DENSE_RANK</span>() <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">PARTITION BY</span> region <span style="color:#c4b5fd;">ORDER BY</span> lifetime_value <span style="color:#c4b5fd;">DESC</span>) <span style="color:#c4b5fd;">AS</span> dense_rank_val
<span style="color:#c4b5fd;">FROM</span> customer_summary;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>customer_id | region | lifetime_value | row_num | rank_val | dense_rank_val
-------------+--------+----------------+---------+----------+----------------
3            | Asia   | 14250.00       | 1       | 1        | 1
1            | Asia   | 9800.50        | 2       | 2        | 2
5            | Asia   | 9800.50        | 3       | 2        | 2
2            | Asia   | 3200.00        | 4       | 4        | 3</div>
  </div>
</div>

<h3>Running Totals with SUM() OVER()</h3>
<p>An aggregate function like <code>SUM()</code> becomes a window function when you add <code>OVER(ORDER BY ...)</code>. This computes a <strong>cumulative sum</strong> — each row shows the running total up to and including that row. This is used for tracking cumulative revenue, cumulative users, inventory running balances, and much more.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Running Total & Moving Average</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">SELECT</span>
    order_date,
    daily_revenue,
    <span style="color:#6b7280;">-- Running (cumulative) total of revenue</span>
    <span style="color:#93c5fd;">SUM</span>(daily_revenue) <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">ORDER BY</span> order_date) <span style="color:#c4b5fd;">AS</span> running_total,
    <span style="color:#6b7280;">-- 7-day moving average</span>
    <span style="color:#93c5fd;">AVG</span>(daily_revenue) <span style="color:#c4b5fd;">OVER</span> (
        <span style="color:#c4b5fd;">ORDER BY</span> order_date
        <span style="color:#c4b5fd;">ROWS BETWEEN</span> <span style="color:#fcd34d;">6</span> <span style="color:#c4b5fd;">PRECEDING AND CURRENT ROW</span>
    ) <span style="color:#c4b5fd;">AS</span> moving_avg_7d
<span style="color:#c4b5fd;">FROM</span> daily_sales
<span style="color:#c4b5fd;">ORDER BY</span> order_date;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>order_date  | daily_revenue | running_total | moving_avg_7d
-------------+---------------+---------------+---------------
2024-01-01  | 1200.00       | 1200.00       | 1200.00
2024-01-02  | 980.00        | 2180.00       | 1090.00
2024-01-03  | 1540.00       | 3720.00       | 1240.00</div>
  </div>
</div>

<h3>LAG() and LEAD() — Comparing Adjacent Rows</h3>
<p><code>LAG(col, n)</code> accesses the value from n rows <em>before</em> the current row, while <code>LEAD(col, n)</code> accesses n rows <em>after</em>. These are essential for computing period-over-period changes: revenue growth vs last month, price change vs yesterday, or retention vs the prior cohort.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — LAG & LEAD</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">SELECT</span>
    month,
    monthly_revenue,
    <span style="color:#93c5fd;">LAG</span>(monthly_revenue, <span style="color:#fcd34d;">1</span>) <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">ORDER BY</span> month) <span style="color:#c4b5fd;">AS</span> prev_month_revenue,
    monthly_revenue - <span style="color:#93c5fd;">LAG</span>(monthly_revenue, <span style="color:#fcd34d;">1</span>) <span style="color:#c4b5fd;">OVER</span> (<span style="color:#c4b5fd;">ORDER BY</span> month) <span style="color:#c4b5fd;">AS</span> mom_change
<span style="color:#c4b5fd;">FROM</span> monthly_sales
<span style="color:#c4b5fd;">ORDER BY</span> month;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>month    | monthly_revenue | prev_month_revenue | mom_change
----------+-----------------+--------------------+-----------
2024-01  | 45000.00        | NULL               | NULL
2024-02  | 52000.00        | 45000.00           | 7000.00
2024-03  | 48500.00        | 52000.00           | -3500.00</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.6 Window Functions',
            'order_index' => 6,
            'content'     => $this->appendQuiz($content6, 'L10_6', [
                ['q' => 'What makes a window function different from an aggregate function like GROUP BY?', 'opts' => ['Window functions are faster', 'Window functions compute per-row values without collapsing the result set', 'Window functions only work on numeric columns', 'Window functions require a subquery'], 'ans' => 1, 'exp' => 'Aggregate functions (with GROUP BY) collapse multiple rows into one row per group. Window functions compute values for each row while still being able to reference surrounding rows — the row count is preserved.'],
                ['q' => 'What does PARTITION BY do inside an OVER() clause?', 'opts' => ['Sorts the rows within the window', 'Divides rows into independent groups for the window calculation', 'Limits the number of rows returned', 'Filters rows before the window is applied'], 'ans' => 1, 'exp' => 'PARTITION BY divides the data into independent subsets, and the window function is applied separately within each partition — similar to GROUP BY but without collapsing rows.'],
                ['q' => 'What is the difference between RANK() and DENSE_RANK()?', 'opts' => ['They are identical', 'RANK() skips numbers after a tie; DENSE_RANK() does not skip', 'DENSE_RANK() skips numbers after a tie; RANK() does not', 'RANK() resets per partition; DENSE_RANK() does not'], 'ans' => 1, 'exp' => 'If two rows tie at rank 2, RANK() gives both a rank of 2 and the next row gets rank 4 (skipping 3). DENSE_RANK() also gives both a 2, but the next row gets 3 (no gap).'],
                ['q' => 'Which window function retrieves the value from the PREVIOUS row?', 'opts' => ['LEAD()', 'PREV()', 'SHIFT()', 'LAG()'], 'ans' => 3, 'exp' => 'LAG(col, n) accesses a value from n rows before the current row. LEAD(col, n) does the opposite, accessing n rows ahead. Both are used for period-over-period comparisons.'],
                ['q' => 'What does ROWS BETWEEN 6 PRECEDING AND CURRENT ROW define?', 'opts' => ['A fixed date range of 6 days', 'The window frame: this row and the 6 rows immediately before it', 'Skip the first 6 rows', 'A 6-row lookahead window'], 'ans' => 1, 'exp' => 'ROWS BETWEEN 6 PRECEDING AND CURRENT ROW defines a sliding frame of 7 rows (the current row plus the 6 preceding rows), used for computing 7-day rolling averages or other moving statistics.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.7 — Database Design & Normalization
        // ══════════════════════════════════════════════════════════════
        $content7 = <<<'HTML'
<h2>Database Design & Normalization</h2>
<p>A well-designed database is the foundation of reliable data science. Poor schema design leads to data inconsistencies, redundant storage, update anomalies, and query performance nightmares. <strong>Normalization</strong> is the process of organizing a database to reduce redundancy and improve data integrity, following a set of rules called <strong>Normal Forms (NF)</strong>.</p>

<h3>The Problems That Normalization Solves</h3>
<p>Before normalization, consider a flat table where every order includes the customer's name and email duplicated on every row. This creates three classes of problems:</p>
<ul style="line-height:2.2;">
  <li><strong>Update anomaly:</strong> If Alice changes her email, you must update every single row where she appears — miss one and your data becomes inconsistent.</li>
  <li><strong>Insertion anomaly:</strong> You can't add a customer to the database until they place an order, because the customer data lives in the orders table.</li>
  <li><strong>Deletion anomaly:</strong> If you delete Alice's only order, you lose all her customer information too.</li>
</ul>

<h3>First Normal Form (1NF) — Atomic Values</h3>
<p>A table is in <strong>1NF</strong> if: every column contains atomic (indivisible) values, each column contains only one type of data, and every row is uniquely identifiable.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — 1NF Violation vs. Correct</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ❌ Violates 1NF: phone_numbers stores multiple values in one cell</span>
<span style="color:#6b7280;">-- order_id | customer  | phone_numbers</span>
<span style="color:#6b7280;">-- 1        | Alice     | 09171234567, 09281234567</span>

<span style="color:#6b7280;">-- ✅ Correct 1NF: one value per cell, separate table for multiple phones</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> customer_phones (
    phone_id    <span style="color:#93c5fd;">INT</span>  <span style="color:#c4b5fd;">PRIMARY KEY</span>,
    customer_id <span style="color:#93c5fd;">INT</span>  <span style="color:#c4b5fd;">NOT NULL</span>,
    phone       <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">20</span>) <span style="color:#c4b5fd;">NOT NULL</span>,
    phone_type  <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">10</span>),  <span style="color:#6b7280;">-- 'mobile', 'home', 'work'</span>
    <span style="color:#c4b5fd;">FOREIGN KEY</span> (customer_id) <span style="color:#c4b5fd;">REFERENCES</span> customers(customer_id)
);</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Notes</span>1NF requires each cell to hold a single, indivisible value.
Repeating groups (multiple values in one cell) violate 1NF.</div>
  </div>
</div>

<h3>Second Normal Form (2NF) — No Partial Dependencies</h3>
<p>A table is in <strong>2NF</strong> if it is already in 1NF and every non-key attribute is <em>fully dependent on the entire primary key</em> (not just part of it). This only applies to tables with composite primary keys.</p>

<h3>Third Normal Form (3NF) — No Transitive Dependencies</h3>
<p>A table is in <strong>3NF</strong> if it is in 2NF and every non-key column depends <em>only</em> on the primary key — not on another non-key column. A transitive dependency is when column C depends on column B, which depends on the primary key A (A → B → C is bad; all should be A → B and A → C directly).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — 3NF Fix: Extract Transitive Dependency</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- ❌ Violates 3NF: zip_code → city (transitive dependency)</span>
<span style="color:#6b7280;">-- customer_id | name  | zip_code | city       | country</span>
<span style="color:#6b7280;">-- The city is determined by zip_code, not by customer_id directly</span>

<span style="color:#6b7280;">-- ✅ 3NF fix: extract zip_code → city into its own lookup table</span>
<span style="color:#c4b5fd;">CREATE TABLE</span> zip_codes (
    zip_code  <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">10</span>) <span style="color:#c4b5fd;">PRIMARY KEY</span>,
    city      <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">100</span>),
    country   <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">100</span>)
);

<span style="color:#c4b5fd;">ALTER TABLE</span> customers
    <span style="color:#c4b5fd;">DROP COLUMN</span> city,
    <span style="color:#c4b5fd;">DROP COLUMN</span> country,
    <span style="color:#c4b5fd;">ADD COLUMN</span> zip_code <span style="color:#93c5fd;">VARCHAR</span>(<span style="color:#fcd34d;">10</span>) <span style="color:#c4b5fd;">REFERENCES</span> zip_codes(zip_code);
<span style="color:#6b7280;">-- Now customers.zip_code → zip_codes.city (no transitive dep in customers)</span></div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Notes</span>After the fix, updating a city name requires changing one row
in zip_codes — not thousands of customer rows.</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.7 Database Design & Normalization',
            'order_index' => 7,
            'content'     => $this->appendQuiz($content7, 'L10_7', [
                ['q' => 'What is an update anomaly in an unnormalized database?', 'opts' => ['Inserting a row breaks a constraint', 'Changing a value in one place requires updating many duplicated rows', 'A query returns stale cached data', 'Two users updating the same row simultaneously'], 'ans' => 1, 'exp' => 'An update anomaly occurs when the same piece of data is stored redundantly across many rows, meaning a single logical change requires updating multiple records — creating the risk of inconsistencies.'],
                ['q' => 'What does First Normal Form (1NF) require?', 'opts' => ['All columns must be integers', 'Each cell must contain a single atomic value', 'The table must have a composite primary key', 'No two tables can share a column name'], 'ans' => 1, 'exp' => '1NF requires that each cell holds one indivisible value — no arrays, comma-separated lists, or repeating groups in a single column.'],
                ['q' => 'What is a transitive dependency?', 'opts' => ['A foreign key referencing a non-primary column', 'Column C depends on column B, which depends on the primary key — not on the primary key directly', 'A column that depends on two other columns simultaneously', 'A primary key composed of multiple columns'], 'ans' => 1, 'exp' => 'A transitive dependency is when a non-key column depends on another non-key column (which in turn depends on the PK). 3NF requires eliminating these by moving the dependent data to its own table.'],
                ['q' => 'Which normal form specifically addresses composite primary keys and partial dependencies?', 'opts' => ['1NF', '2NF', '3NF', 'BCNF'], 'ans' => 1, 'exp' => '2NF only matters when a table has a composite primary key. It requires that every non-key column depends on the ENTIRE composite key, not just one part of it.'],
                ['q' => 'After normalizing a database, what is typically required to retrieve combined data?', 'opts' => ['UNION queries', 'Subqueries only', 'JOIN operations', 'Stored procedures'], 'ans' => 2, 'exp' => 'Normalization splits data into multiple tables. To retrieve logically related data back together, you use JOIN operations — which is why understanding JOINs is critical when working with normalized databases.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.8 — Indexing & Query Performance
        // ══════════════════════════════════════════════════════════════
        $content8 = <<<'HTML'
<h2>Indexing & Query Performance</h2>
<p>When a database table has millions or billions of rows, even a simple <code>SELECT ... WHERE</code> query can take minutes if the engine must scan every row. <strong>Indexes</strong> are auxiliary data structures that allow the database engine to locate rows in microseconds instead — similar to the index at the back of a textbook. Understanding when and how to create indexes is one of the most impactful performance skills a data scientist or data engineer can develop.</p>

<h3>How a Full Table Scan Works (Without an Index)</h3>
<p>Without an index, the database performs a <strong>sequential scan</strong>: it reads every single row from disk and checks whether it matches your WHERE condition. For a table with 50 million rows, this means reading potentially 50 million rows even if your query only needs 10 of them.</p>

<h3>How a B-Tree Index Works</h3>
<p>Most database indexes use a <strong>B-Tree (Balanced Tree)</strong> structure. The indexed column values are stored in a sorted tree. When you query <code>WHERE email = 'alice@example.com'</code>, the engine traverses the tree in O(log n) time — for 50 million rows, that is roughly 26 comparisons instead of 50 million. The index entry also stores a pointer to the actual row on disk.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — Creating Indexes</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Single-column index on a frequently searched column</span>
<span style="color:#c4b5fd;">CREATE INDEX</span> idx_customers_email
    <span style="color:#c4b5fd;">ON</span> customers(email);

<span style="color:#6b7280;">-- Composite index on two columns (covers queries that filter on both)</span>
<span style="color:#c4b5fd;">CREATE INDEX</span> idx_orders_customer_date
    <span style="color:#c4b5fd;">ON</span> orders(customer_id, order_date);

<span style="color:#6b7280;">-- Unique index — also enforces uniqueness (like a constraint)</span>
<span style="color:#c4b5fd;">CREATE UNIQUE INDEX</span> idx_customers_email_unique
    <span style="color:#c4b5fd;">ON</span> customers(email);

<span style="color:#6b7280;">-- Drop an index when it is no longer needed</span>
<span style="color:#c4b5fd;">DROP INDEX</span> idx_customers_email;

<span style="color:#6b7280;">-- View all indexes on a table (PostgreSQL)</span>
<span style="color:#c4b5fd;">SELECT</span> indexname, indexdef
<span style="color:#c4b5fd;">FROM</span> pg_indexes
<span style="color:#c4b5fd;">WHERE</span> tablename = <span style="color:#a7f3d0;">'customers'</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Notes</span>PRIMARY KEY and UNIQUE constraints automatically create indexes.
Over-indexing slows down INSERT/UPDATE/DELETE operations.</div>
  </div>
</div>

<h3>EXPLAIN / EXPLAIN ANALYZE — Reading Query Plans</h3>
<p>Before and after adding an index, use <code>EXPLAIN</code> to see how the database engine plans to execute your query. <code>EXPLAIN ANALYZE</code> actually runs the query and shows the real execution time. The key terms to understand are: <strong>Seq Scan</strong> (slow, no index used), <strong>Index Scan</strong> (fast, index used), and <strong>cost</strong> (relative estimate of resources).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">SQL — EXPLAIN ANALYZE (PostgreSQL)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;">-- Before index: sequential scan (slow)</span>
<span style="color:#c4b5fd;">EXPLAIN ANALYZE</span>
<span style="color:#c4b5fd;">SELECT</span> * <span style="color:#c4b5fd;">FROM</span> customers <span style="color:#c4b5fd;">WHERE</span> email = <span style="color:#a7f3d0;">'alice@example.com'</span>;</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Before Index</span>Seq Scan on customers  (cost=0.00..25000.00 rows=1 width=150)
  Filter: ((email)::text = 'alice@example.com')
Execution Time: 842.351 ms

<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">After CREATE INDEX</span>Index Scan using idx_customers_email on customers
  (cost=0.43..8.45 rows=1 width=150)
  Index Cond: ((email)::text = 'alice@example.com')
Execution Time: 0.082 ms</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.8 Indexing & Query Performance',
            'order_index' => 8,
            'content'     => $this->appendQuiz($content8, 'L10_8', [
                ['q' => 'What is a sequential (full table) scan?', 'opts' => ['Reading only the indexed rows', 'Reading every row in the table to find matching records', 'Running the same query twice', 'Ordering rows before filtering'], 'ans' => 1, 'exp' => 'A sequential scan reads every single row in the table and evaluates the WHERE condition on each one. It is the default when no usable index exists and becomes very slow on large tables.'],
                ['q' => 'What data structure do most database indexes use?', 'opts' => ['Hash Map', 'Linked List', 'B-Tree (Balanced Tree)', 'Red-Black Tree'], 'ans' => 2, 'exp' => 'B-Trees are the standard index structure in PostgreSQL, MySQL, and most RDBMS systems. They maintain sorted data and allow O(log n) search, insert, and delete operations.'],
                ['q' => 'What is the downside of creating too many indexes on a table?', 'opts' => ['SELECT queries become slower', 'INSERT, UPDATE, DELETE operations become slower because every index must be updated', 'The database runs out of memory immediately', 'Queries return incorrect results'], 'ans' => 1, 'exp' => 'Every index on a table must be maintained whenever data changes. A table with 10 indexes will have its INSERT/UPDATE/DELETE operations take significantly longer because 10 B-Trees must all be updated.'],
                ['q' => 'What does EXPLAIN ANALYZE do in PostgreSQL?', 'opts' => ['Explains the column definitions of a table', 'Shows the execution plan AND actually runs the query, reporting real execution time', 'Only estimates the execution plan without running the query', 'Rewrites the query for better performance'], 'ans' => 1, 'exp' => 'EXPLAIN shows the estimated plan without executing. EXPLAIN ANALYZE both executes the query AND shows the actual plan with real timing — it is the standard tool for diagnosing slow queries.'],
                ['q' => 'In an EXPLAIN output, which scan type indicates an index is being used?', 'opts' => ['Seq Scan', 'Full Scan', 'Index Scan', 'Row Scan'], 'ans' => 2, 'exp' => '"Index Scan" or "Index Only Scan" in the EXPLAIN output means the database is using an index to locate rows efficiently. "Seq Scan" means it is reading the entire table without an index.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.9 — Python + SQL: SQLAlchemy & Pandas Integration
        // ══════════════════════════════════════════════════════════════
        $content9 = <<<'HTML'
<h2>Python + SQL: SQLAlchemy & Pandas Integration</h2>
<p>In a real data science workflow, you rarely run SQL in a database console and manually copy results. Instead, you query databases directly from Python, load the results into <strong>Pandas DataFrames</strong>, perform analysis, and write results back to the database — all in a single pipeline. This lesson covers the tools that make this seamless: <code>sqlite3</code> (built-in), <code>psycopg2</code> (PostgreSQL), and <code>SQLAlchemy</code> (the standard Python ORM and database abstraction layer).</p>

<h3>Raw SQL with sqlite3 and pandas.read_sql()</h3>
<p>The simplest approach: open a connection, pass a SQL string to <code>pd.read_sql()</code>, and get back a fully-formed DataFrame. No ORM needed for pure analytical queries.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — pandas.read_sql() with SQLite</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> sqlite3
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># Connect to the database</span>
conn = sqlite3.<span style="color:#93c5fd;">connect</span>(<span style="color:#a7f3d0;">"data_science.db"</span>)

<span style="color:#6b7280;"># Load SQL query result directly into a DataFrame</span>
query = <span style="color:#a7f3d0;">"""
    SELECT
        c.name         AS customer,
        COUNT(o.order_id)          AS num_orders,
        SUM(o.total_amount)        AS lifetime_value
    FROM customers c
    LEFT JOIN orders o ON c.customer_id = o.customer_id
    GROUP BY c.customer_id, c.name
    ORDER BY lifetime_value DESC
"""</span>

df = pd.<span style="color:#93c5fd;">read_sql</span>(query, conn)
conn.<span style="color:#93c5fd;">close</span>()

<span style="color:#93c5fd;">print</span>(df.head())
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"\nTop spender: {df.iloc[0]['customer']} — ₱{df.iloc[0]['lifetime_value']:,.2f}"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>     customer  num_orders  lifetime_value
0  Carmen Diaz          28        14250.00
1  Alice Santos         15         9800.50
2    Bob Reyes          10         3200.00

Top spender: Carmen Diaz — ₱14,250.00</div>
  </div>
</div>

<h3>SQLAlchemy — Production-Grade Database Access</h3>
<p><strong>SQLAlchemy</strong> is the standard Python library for database interaction. It provides two layers: the <em>Core</em> (direct SQL with connection management) and the <em>ORM</em> (map Python classes to tables). For data science, we mostly use SQLAlchemy Core via an <strong>engine</strong> — a connection pool that manages connections efficiently and abstracts over different database backends (SQLite, PostgreSQL, MySQL, etc.).</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — SQLAlchemy + pandas</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">from</span> sqlalchemy <span style="color:#c4b5fd;">import</span> create_engine, text
<span style="color:#c4b5fd;">import</span> pandas <span style="color:#c4b5fd;">as</span> pd

<span style="color:#6b7280;"># Connection strings follow the pattern: dialect://user:password@host/dbname</span>
<span style="color:#6b7280;"># SQLite:</span>
engine = <span style="color:#93c5fd;">create_engine</span>(<span style="color:#a7f3d0;">"sqlite:///data_science.db"</span>)

<span style="color:#6b7280;"># PostgreSQL (requires pip install psycopg2):</span>
<span style="color:#6b7280;"># engine = create_engine("postgresql://user:password@localhost:5432/mydb")</span>

<span style="color:#6b7280;"># Use the engine as a context manager — connection is auto-closed</span>
<span style="color:#c4b5fd;">with</span> engine.<span style="color:#93c5fd;">connect</span>() <span style="color:#c4b5fd;">as</span> conn:
    df = pd.<span style="color:#93c5fd;">read_sql</span>(<span style="color:#93c5fd;">text</span>(<span style="color:#a7f3d0;">"SELECT * FROM customers LIMIT 5"</span>), conn)
    <span style="color:#93c5fd;">print</span>(df)

<span style="color:#6b7280;"># Write a DataFrame BACK to the database</span>
results_df = pd.<span style="color:#93c5fd;">DataFrame</span>({
    <span style="color:#a7f3d0;">'model'</span>: [<span style="color:#a7f3d0;">'RandomForest'</span>, <span style="color:#a7f3d0;">'XGBoost'</span>],
    <span style="color:#a7f3d0;">'accuracy'</span>: [<span style="color:#fcd34d;">0.934</span>, <span style="color:#fcd34d;">0.961</span>],
    <span style="color:#a7f3d0;">'run_date'</span>: [<span style="color:#a7f3d0;">'2024-03-01'</span>, <span style="color:#a7f3d0;">'2024-03-01'</span>]
})
results_df.<span style="color:#93c5fd;">to_sql</span>(<span style="color:#a7f3d0;">'model_results'</span>, engine, if_exists=<span style="color:#a7f3d0;">'append'</span>, index=<span style="color:#fca5a5;">False</span>)
<span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Model results written to database!"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>   customer_id          name                  email  signup_date
0            1   Alice Santos    alice@example.com   2023-01-10
1            2     Bob Reyes      bob@example.com   2023-02-14
2            3   Carmen Diaz   carmen@example.com   2023-03-05
Model results written to database!</div>
  </div>
</div>

<h3>Parameterized Queries — Preventing SQL Injection</h3>
<p>Never format user input directly into a SQL string using Python f-strings. This opens your application to <strong>SQL injection attacks</strong> — where malicious input manipulates the query to drop tables, extract passwords, or bypass authentication. Always use <strong>parameterized queries</strong> where the values are passed separately from the SQL string.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Parameterized Queries</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># ❌ DANGEROUS — never do this with user input</span>
user_email = <span style="color:#93c5fd;">input</span>(<span style="color:#a7f3d0;">"Enter email: "</span>)
query = <span style="color:#a7f3d0;">f"SELECT * FROM customers WHERE email = '{user_email}'"</span>
<span style="color:#6b7280;"># If user types: ' OR '1'='1 — ALL rows are returned!  (SQL injection)</span>

<span style="color:#6b7280;"># ✅ SAFE — parameterized queries escape input automatically</span>
<span style="color:#c4b5fd;">import</span> sqlite3
conn = sqlite3.<span style="color:#93c5fd;">connect</span>(<span style="color:#a7f3d0;">"data_science.db"</span>)
cursor = conn.<span style="color:#93c5fd;">cursor</span>()

safe_email = <span style="color:#93c5fd;">input</span>(<span style="color:#a7f3d0;">"Enter email: "</span>)
<span style="color:#6b7280;"># The ? placeholder is replaced safely by the engine — never injected as raw SQL</span>
cursor.<span style="color:#93c5fd;">execute</span>(<span style="color:#a7f3d0;">"SELECT * FROM customers WHERE email = ?"</span>, (safe_email,))
<span style="color:#93c5fd;">print</span>(cursor.<span style="color:#93c5fd;">fetchall</span>())
conn.<span style="color:#93c5fd;">close</span>()</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Result</span>[(1, 'Alice Santos', 'alice@example.com', '2023-01-10')]</div>
  </div>
</div>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.9 Python + SQL: SQLAlchemy & Pandas Integration',
            'order_index' => 9,
            'content'     => $this->appendQuiz($content9, 'L10_9', [
                ['q' => 'Which pandas function loads a SQL query result directly into a DataFrame?', 'opts' => ['pd.from_sql()', 'pd.sql_query()', 'pd.read_sql()', 'pd.load_db()'], 'ans' => 2, 'exp' => 'pd.read_sql(query, connection) executes the SQL query and returns the result as a Pandas DataFrame. The connection can be a raw database connection (sqlite3) or a SQLAlchemy engine.'],
                ['q' => 'What does DataFrame.to_sql() do?', 'opts' => ['Prints the DataFrame as a SQL table', 'Converts the DataFrame to a JSON file', 'Writes the DataFrame as a new table (or appends rows) in the connected database', 'Validates the DataFrame against a SQL schema'], 'ans' => 2, 'exp' => 'df.to_sql(table_name, engine) writes the contents of the DataFrame to the specified table in the connected database. The if_exists parameter controls whether to create a new table, replace it, or append.'],
                ['q' => 'What is a SQL injection attack?', 'opts' => ['Inserting NULL values into a required column', 'Inserting malicious SQL code via user input that manipulates the database query', 'Running too many concurrent queries', 'Accidentally deleting indexes'], 'ans' => 1, 'exp' => 'SQL injection occurs when user input is directly concatenated into a SQL string. Attackers can type SQL code that changes the query\'s logic — bypassing authentication, extracting all data, or even dropping tables.'],
                ['q' => 'What is the correct way to prevent SQL injection in a Python sqlite3 query?', 'opts' => ['Convert the input to uppercase before inserting', 'Use f-strings but wrap the input in quotes', 'Use parameterized queries with ? placeholders and pass values as a tuple', 'Validate the input length is under 50 characters'], 'ans' => 2, 'exp' => 'Parameterized queries pass the value separately from the SQL string. The database engine handles escaping automatically, making SQL injection impossible. Use ? (sqlite3) or :name (SQLAlchemy) placeholders.'],
                ['q' => 'Which SQLAlchemy function creates a database connection pool for your application?', 'opts' => ['sqlalchemy.connect()', 'sqlalchemy.open()', 'create_engine()', 'new_session()'], 'ans' => 2, 'exp' => 'create_engine(connection_string) is the entry point for SQLAlchemy. It creates a connection pool and returns an Engine object, which manages database connections efficiently throughout your application.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.10 — NoSQL Databases & When to Use Them
        // ══════════════════════════════════════════════════════════════
        $content10 = <<<'HTML'
<h2>NoSQL Databases & When to Use Them</h2>
<p><strong>NoSQL</strong> (Not Only SQL) databases are non-relational data stores designed for specific data models and access patterns that relational databases handle poorly. They trade the strict structure and ACID guarantees of SQL for flexibility, horizontal scalability, and speed at extreme data volumes. A modern data scientist must know when to reach for a NoSQL database — and when to stick with SQL.</p>

<h3>Why NoSQL? The Motivation</h3>
<p>The rise of the web introduced data challenges that RDBMS systems were not designed for: social networks with billions of nodes and edges, real-time user behavior streams with millions of events per second, product catalogs with wildly different attributes per item, and sensor networks producing unstructured JSON. Forcing all of this into fixed-schema relational tables is either impractical or catastrophically slow.</p>

<h3>Four Major NoSQL Database Types</h3>
<p>Each type optimizes for a different data model and query pattern:</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — MongoDB (Document Store)</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#6b7280;"># MongoDB stores data as JSON-like documents (no fixed schema required)</span>
<span style="color:#c4b5fd;">from</span> pymongo <span style="color:#c4b5fd;">import</span> MongoClient

client = <span style="color:#93c5fd;">MongoClient</span>(<span style="color:#a7f3d0;">"mongodb://localhost:27017/"</span>)
db = client[<span style="color:#a7f3d0;">"ecommerce"</span>]
products = db[<span style="color:#a7f3d0;">"products"</span>]  <span style="color:#6b7280;"># Collection (like a SQL table)</span>

<span style="color:#6b7280;"># Insert a document — no schema required, each doc can have different fields</span>
products.<span style="color:#93c5fd;">insert_one</span>({
    <span style="color:#a7f3d0;">"name"</span>: <span style="color:#a7f3d0;">"Laptop Pro X"</span>,
    <span style="color:#a7f3d0;">"price"</span>: <span style="color:#fcd34d;">89999.00</span>,
    <span style="color:#a7f3d0;">"specs"</span>: {
        <span style="color:#a7f3d0;">"cpu"</span>: <span style="color:#a7f3d0;">"Apple M3 Pro"</span>,
        <span style="color:#a7f3d0;">"ram_gb"</span>: <span style="color:#fcd34d;">18</span>,
        <span style="color:#a7f3d0;">"storage_gb"</span>: <span style="color:#fcd34d;">512</span>
    },
    <span style="color:#a7f3d0;">"tags"</span>: [<span style="color:#a7f3d0;">"laptop"</span>, <span style="color:#a7f3d0;">"apple"</span>, <span style="color:#a7f3d0;">"pro"</span>],
    <span style="color:#a7f3d0;">"in_stock"</span>: <span style="color:#fca5a5;">True</span>
})

<span style="color:#6b7280;"># Query: find all laptops under ₱100,000 with at least 16GB RAM</span>
results = products.<span style="color:#93c5fd;">find</span>({
    <span style="color:#a7f3d0;">"price"</span>:      {<span style="color:#a7f3d0;">"$lt"</span>: <span style="color:#fcd34d;">100000</span>},
    <span style="color:#a7f3d0;">"specs.ram_gb"</span>: {<span style="color:#a7f3d0;">"$gte"</span>: <span style="color:#fcd34d;">16</span>}
})

<span style="color:#c4b5fd;">for</span> doc <span style="color:#c4b5fd;">in</span> results:
    <span style="color:#93c5fd;">print</span>(doc[<span style="color:#a7f3d0;">"name"</span>], <span style="color:#a7f3d0;">"-"</span>, doc[<span style="color:#a7f3d0;">"specs"</span>][<span style="color:#a7f3d0;">"ram_gb"</span>], <span style="color:#a7f3d0;">"GB RAM"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Laptop Pro X - 18 GB RAM</div>
  </div>
</div>

<h3>Redis — Key-Value Store</h3>
<p><strong>Redis</strong> stores data as key-value pairs entirely in RAM, making read/write operations sub-millisecond. It is used for caching database query results, session storage, leaderboards, real-time counters, and feature stores for machine learning inference.</p>

<div class="code-window" style="background:var(--surface2);border-radius:8px;border:1px solid var(--border);margin-bottom:32px;overflow:hidden;">
  <div style="background:rgba(0,0,0,0.2);padding:8px 16px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--border);">
    <span style="font-size:0.75rem;color:var(--muted);font-family:'JetBrains Mono',monospace;">PYTHON — Redis Key-Value Cache</span>
    <button onclick="launchIDE(this)" style="background:var(--accent);color:#fff;border:none;padding:6px 12px;border-radius:4px;font-size:0.75rem;cursor:pointer;font-weight:600;">Try in Compiler →</button>
  </div>
  <div style="padding:16px;">
    <div class="code-content" style="color:#e5e7eb;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;font-size:0.9rem;"><span style="color:#c4b5fd;">import</span> redis
<span style="color:#c4b5fd;">import</span> json

r = redis.<span style="color:#93c5fd;">Redis</span>(host=<span style="color:#a7f3d0;">'localhost'</span>, port=<span style="color:#fcd34d;">6379</span>, db=<span style="color:#fcd34d;">0</span>)

<span style="color:#6b7280;"># Cache a model's prediction result (expires after 3600 seconds = 1 hour)</span>
cache_key = <span style="color:#a7f3d0;">"prediction:user_1042"</span>
prediction = {<span style="color:#a7f3d0;">"churn_prob"</span>: <span style="color:#fcd34d;">0.87</span>, <span style="color:#a7f3d0;">"segment"</span>: <span style="color:#a7f3d0;">"high_risk"</span>}
r.<span style="color:#93c5fd;">setex</span>(cache_key, <span style="color:#fcd34d;">3600</span>, json.<span style="color:#93c5fd;">dumps</span>(prediction))

<span style="color:#6b7280;"># Retrieve from cache (microseconds vs milliseconds from a DB query)</span>
cached = r.<span style="color:#93c5fd;">get</span>(cache_key)
<span style="color:#c4b5fd;">if</span> cached:
    data = json.<span style="color:#93c5fd;">loads</span>(cached)
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">f"Cache hit: churn prob = {data['churn_prob']}"</span>)
<span style="color:#c4b5fd;">else</span>:
    <span style="color:#93c5fd;">print</span>(<span style="color:#a7f3d0;">"Cache miss — run the model"</span>)</div>
    <div style="color:#9ca3af;font-size:0.85rem;overflow-x:auto;white-space:pre;font-family:'JetBrains Mono',monospace;">
<span style="color:var(--dim);text-transform:uppercase;font-size:0.7rem;letter-spacing:0.05em;display:block;margin-bottom:8px;font-family:'Inter',sans-serif;font-weight:600;">Console Output</span>Cache hit: churn prob = 0.87</div>
  </div>
</div>

<h3>SQL vs. NoSQL: When to Use Which</h3>
<p>The choice between SQL and NoSQL is never about one being "better" — it is about which fits your data model and access patterns. In practice, most production systems use <strong>both</strong>: a relational database for transactional and analytical data, and a NoSQL store for caching, real-time features, or flexible document storage.</p>
<ul style="line-height:2.2;">
  <li><strong>Use SQL (PostgreSQL/MySQL)</strong> when you need complex JOIN queries, strong consistency, data integrity constraints, and your schema is well-defined and stable.</li>
  <li><strong>Use MongoDB</strong> when your data has a variable or nested structure, you need to iterate on your schema quickly, or documents are the natural unit of access.</li>
  <li><strong>Use Redis</strong> when you need sub-millisecond latency for caching, counters, leaderboards, or real-time ML feature serving.</li>
  <li><strong>Use Cassandra / HBase</strong> when you need to write millions of events per second across multiple data centers (time-series, IoT, event logs).</li>
  <li><strong>Use Neo4j</strong> when your data is inherently a graph — fraud detection networks, social connections, recommendation engines.</li>
</ul>
HTML;

        Lesson::create([
            'module_id'   => $dbModule->id,
            'title'       => '10.10 NoSQL Databases & When to Use Them',
            'order_index' => 10,
            'content'     => $this->appendQuiz($content10, 'L10_10', [
                ['q' => 'What is a "document" in a MongoDB document store?', 'opts' => ['A PDF or Word file stored in the database', 'A JSON-like record that can have a flexible, nested structure without a fixed schema', 'A type of SQL table with JSON columns', 'A file of raw SQL statements'], 'ans' => 1, 'exp' => 'MongoDB stores data as BSON (Binary JSON) documents. Each document can have different fields and nested structures — there is no fixed schema enforced at the database level.'],
                ['q' => 'What makes Redis significantly faster than a traditional relational database for reads?', 'opts' => ['Redis uses B-Tree indexes for all keys', 'Redis stores all data in RAM (in-memory), making reads sub-millisecond', 'Redis compresses data more efficiently', 'Redis uses a different CPU architecture'], 'ans' => 1, 'exp' => 'Redis is an in-memory data store — all data lives in RAM. Since RAM access is orders of magnitude faster than disk I/O, Redis delivers sub-millisecond read/write performance.'],
                ['q' => 'When is a graph database like Neo4j the best choice?', 'opts' => ['When storing large binary files', 'When data has complex many-to-many relationships and graph traversal queries are common', 'When you need fixed schema enforcement', 'When you need sub-millisecond key lookups'], 'ans' => 1, 'exp' => 'Graph databases excel when the relationships between entities are as important as the entities themselves — fraud ring detection, social networks, knowledge graphs, and recommendation engines are classic use cases.'],
                ['q' => 'What does the $gte operator mean in a MongoDB query?', 'opts' => ['Greater than explicitly', 'Greater than or equal to', 'Get elements', 'Group then enumerate'], 'ans' => 1, 'exp' => 'MongoDB uses query operators prefixed with $ for comparisons. $gte means "greater than or equal to", $gt means "greater than", $lt means "less than", and $lte means "less than or equal to".'],
                ['q' => 'In a modern production data system, which approach is most common?', 'opts' => ['Use only SQL for everything', 'Use only NoSQL for everything', 'Use SQL for transactional/analytical data and NoSQL for caching/real-time/flexible data', 'Choose one database type and never mix'], 'ans' => 2, 'exp' => 'Modern systems are "polyglot persistent" — they use the best database for each job. PostgreSQL for structured analytics, Redis for caching and real-time features, MongoDB for flexible document storage, etc.'],
            ])
        ]);

        // ══════════════════════════════════════════════════════════════
        // LESSON 10.11 — Final Exam
        // ══════════════════════════════════════════════════════════════
        $allFinalQuestions = [
            ['q' => 'Which SQL keyword ensures that a column cannot contain duplicate values across rows?', 'opts' => ['NOT NULL', 'PRIMARY KEY', 'UNIQUE', 'DISTINCT'], 'ans' => 2, 'exp' => 'UNIQUE constrains a column (or combination of columns) so that no two rows can hold the same value. A PRIMARY KEY also implies uniqueness but additionally prohibits NULL.'],
            ['q' => 'What is the correct SQL clause order for a full aggregation query?', 'opts' => ['SELECT → WHERE → GROUP BY → HAVING → ORDER BY', 'WHERE → SELECT → GROUP BY → ORDER BY → HAVING', 'GROUP BY → SELECT → HAVING → WHERE → ORDER BY', 'SELECT → HAVING → WHERE → GROUP BY → ORDER BY'], 'ans' => 0, 'exp' => 'The standard order is: SELECT ... FROM ... WHERE ... GROUP BY ... HAVING ... ORDER BY ... LIMIT. The database evaluates them in a different internal order (FROM → WHERE → GROUP BY → HAVING → SELECT → ORDER BY → LIMIT) but they are written in this sequence.'],
            ['q' => 'You need to return all products from a table, including those with no matching sale record. Which join do you use?', 'opts' => ['INNER JOIN', 'RIGHT JOIN', 'LEFT JOIN products to sales', 'CROSS JOIN'], 'ans' => 2, 'exp' => 'A LEFT JOIN from products to sales returns all products as the left table, with NULL in the sales columns for any product that has no sale record. INNER JOIN would exclude products with no sales.'],
            ['q' => 'What does NTILE(4) OVER (ORDER BY salary) compute?', 'opts' => ['The 4th highest salary', 'Divides rows into 4 equal-sized buckets (quartiles) and assigns a bucket number to each row', 'The average of every 4th row', 'A rank out of 4'], 'ans' => 1, 'exp' => 'NTILE(n) is a window function that divides the ordered result set into n equal-sized buckets and assigns each row its bucket number (1 through n). NTILE(4) creates salary quartiles.'],
            ['q' => 'Which Normal Form eliminates transitive dependencies?', 'opts' => ['1NF', '2NF', '3NF', '4NF'], 'ans' => 2, 'exp' => '3NF requires that every non-key attribute depends only on the primary key — not on another non-key attribute. This eliminates transitive dependencies (e.g., city depending on zip_code, not on customer_id directly).'],
            ['q' => 'What is the time complexity of looking up a value in a properly indexed B-Tree column?', 'opts' => ['O(n)', 'O(n²)', 'O(log n)', 'O(1)'], 'ans' => 2, 'exp' => 'B-Tree indexes provide O(log n) lookup time. For a table with 1 billion rows, this means roughly 30 comparisons to locate a value — versus potentially 1 billion comparisons for a full table scan.'],
            ['q' => 'Which pandas function reads a SQL query into a DataFrame?', 'opts' => ['pd.DataFrame.from_query()', 'pd.read_sql()', 'pd.load_table()', 'pd.from_database()'], 'ans' => 1, 'exp' => 'pd.read_sql(query, connection) is the standard way to execute a SQL query and load results directly into a Pandas DataFrame. It accepts both raw connection objects and SQLAlchemy engines.'],
            ['q' => 'What is a CTE (Common Table Expression)?', 'opts' => ['A type of index for faster joins', 'A named temporary result set defined at the top of a query using WITH, referenced like a table below', 'A stored procedure that runs nightly', 'A type of subquery in the SELECT clause'], 'ans' => 1, 'exp' => 'CTEs are introduced with WITH cte_name AS (subquery). They act as named temporary views for the duration of the query, making complex multi-step queries far more readable and maintainable.'],
            ['q' => 'What does the LAG() window function return?', 'opts' => ['The maximum value in the partition', 'The value from a preceding row (default: 1 row before the current row)', 'A running total up to the current row', 'The value from the next row ahead'], 'ans' => 1, 'exp' => 'LAG(column, n) retrieves the value from n rows before the current row in the window\'s order. It is used for period-over-period comparisons like month-over-month revenue changes.'],
            ['q' => 'Why should you never build SQL queries using Python f-strings with user input?', 'opts' => ['f-strings are too slow for database operations', 'It creates SQL injection vulnerabilities where malicious input can manipulate the query', 'The database cannot parse f-string output', 'f-strings strip quotes from the values'], 'ans' => 1, 'exp' => 'Concatenating user input directly into SQL strings allows attackers to inject SQL code. Always use parameterized queries (e.g., cursor.execute("... WHERE id = ?", (user_id,))) which safely escape all input.'],
            ['q' => 'Which NoSQL database type stores data as JSON-like documents without a fixed schema?', 'opts' => ['Redis', 'Cassandra', 'MongoDB', 'Neo4j'], 'ans' => 2, 'exp' => 'MongoDB is a document store — it stores BSON (Binary JSON) documents in collections. Each document can have its own structure, making it ideal for semi-structured or evolving data schemas.'],
            ['q' => 'What is referential integrity in a relational database?', 'opts' => ['All column names must be unique across the entire database', 'Foreign key values must reference existing primary key values in the parent table', 'Every table must have an auto-incrementing primary key', 'All rows in a JOIN must produce a result'], 'ans' => 1, 'exp' => 'Referential integrity is enforced by FOREIGN KEY constraints — they ensure that a value in a foreign key column always has a matching primary key in the referenced table. You cannot insert an order for a customer_id that does not exist.'],
        ];

        $finalContent = <<<'HTML'
<div id="org-lock-screen" style="display:block;text-align:center;padding:60px 20px;">
    <h2 style="color:var(--text);margin-bottom:12px;">🔒 Final Exam — Organization Required</h2>
    <p style="color:var(--muted);">The Module 10 Final Exam is only available to students enrolled in an organization.</p>
    <p style="font-size:0.85rem;color:#f59e0b;margin-top:1rem;background:rgba(245,158,11,0.1);padding:10px;border-radius:8px;display:inline-block;">Please contact administration to link your account to an organization.</p>
</div>
<div id="final-exam-content" style="display:none;">
    <h2>Module 10: Final Examination</h2>
    <p>This comprehensive exam covers all topics from Lessons 10.1 through 10.10 — relational fundamentals, SQL querying, aggregation, JOINs, subqueries, CTEs, window functions, normalization, indexing, Python-SQL integration, and NoSQL databases. Good luck!</p>
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
            'module_id'   => $dbModule->id,
            'title'       => '10.11 Final Exam: Database Management Mastery',
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