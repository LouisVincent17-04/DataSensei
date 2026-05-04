<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 10 — Relational Databases & SQL (Professional) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Professional tier
 *   2. coding_questions    — 50 questions covering elite SQL/DB topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   10.11 Gaps & Islands, Sessionization
 *   10.12 Recursive CTEs (Graph Traversal, BOM, String Splitting)
 *   10.13 Advanced JSON Manipulation (json_each, json_group_array)
 *   10.14 Complex Window Frames & Time-Series Analytics
 *   10.15 Pivoting, Unpivoting & Matrix Operations
 *   10.16 Relational Division & Set Theory
 *   10.17 Slowly Changing Dimensions (SCD2) & Event Sourcing
 *   10.18 OLAP Simulations (Rollup, Pareto, Retention)
 *   10.19 Query Optimization & Advanced Deduplication
 *   10.20 Concurrency, State Machines, & Constraints
 */
class Module10CodingChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (! $category) {
            $this->command->error('Professional category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 10 — Relational Databases & SQL (Professional) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Relational Databases & SQL',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Master elite database concepts. Solve Gaps & Islands problems, traverse hierarchies and graphs using Recursive CTEs, dynamically pivot matrices, unnest JSON arrays, implement Relational Division, analyze cohorts and Pareto distributions, reconstruct event-sourced states, and optimize complex queries using Python and SQLite.',
                'time_limit_seconds' => 1800,
                'base_xp'            => 1500,
                'order_index'        => 10,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 professional coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Gaps & Islands / Sequence Analytics (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
**Gaps & Islands (The Islands Problem)**: Group consecutive sequences.
Given a `logins` table with continuous and broken sequences of `day_id`, find the `start_day` and `end_day` of each continuous streak of logins. Order by `start_day`.

Expected output:
1 3
6 7
10 10

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE logins (day_id INTEGER)')
conn.executemany('INSERT INTO logins VALUES (?)', [(1,),(2,),(3,),(6,),(7,),(10,)])
conn.commit()

# Use ROW_NUMBER() to create a grouping identifier
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
**Gaps & Islands (The Gaps Problem)**: Identify missing records in a sequence.
The `invoices` table has an `id` column. Find the ranges of missing IDs between the minimum and maximum ID. Output `gap_start` and `gap_end`.

Expected output:
3 4
8 9

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE invoices (id INTEGER)')
conn.executemany('INSERT INTO invoices VALUES (?)', [(1,),(2,),(5,),(6,),(7,),(10,)])
conn.commit()

# Use LEAD() to find the next ID and identify gaps > 1
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
**Sessionization**: Group events into sessions if they occur within a specific time threshold (e.g., 60 seconds).
The `events` table tracks `event_time` in seconds. Output the `session_id` (using a running sum of gap flags), `min(event_time)`, and `max(event_time)`. Order by session_id.

Expected output:
1 10 30
2 150 160
3 300 300

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE events (event_time INTEGER)')
conn.executemany('INSERT INTO events VALUES (?)', [(10,),(20,),(30,),(150,),(160,),(300,)])
conn.commit()

# Use LAG to find gaps > 60s, then SUM() OVER to create session IDs
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Peak Concurrency**: Find the time when the most active users were online simultaneously.
`sessions` table has `start_time` and `end_time`. Generate a timeline of +1 (start) and -1 (end) events, then calculate the running sum of active users. Output the maximum concurrent users.

Expected output:
3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sessions (start_time INTEGER, end_time INTEGER)')
conn.executemany('INSERT INTO sessions VALUES (?,?)', [(1, 5), (2, 6), (4, 8), (7, 10)])
conn.commit()

# Simulate timeline via UNION ALL, then use SUM() OVER
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Max Streak**: Find the length of the longest consecutive sequence of 'Win's in the `matches` table.
Output the max streak length.

Expected output:
3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE matches (id INTEGER, result TEXT)')
conn.executemany('INSERT INTO matches VALUES (?,?)', [
    (1,'Win'),(2,'Loss'),(3,'Win'),(4,'Win'),(5,'Win'),(6,'Loss'),(7,'Win')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Recursive CTEs & Graphs (Q6–Q10)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
**Tree Depth**: Calculate the maximum depth of an employee management hierarchy. The CEO (`manager_id IS NULL`) is at depth 1.
Use a `WITH RECURSIVE` CTE. Output the maximum depth.

Expected output:
3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp (id INTEGER, manager_id INTEGER)')
conn.executemany('INSERT INTO emp VALUES (?,?)', [(1,None), (2,1), (3,1), (4,2), (5,2)])
conn.commit()

query = "WITH RECURSIVE hierarchy AS (...) SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
**Graph Traversal (Shortest Path)**: Given directed `edges` (from_node, to_node), find the shortest distance (number of edges) from node 'A' to node 'D' using a recursive CTE.

Expected output:
2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE edges (from_node TEXT, to_node TEXT)')
conn.executemany('INSERT INTO edges VALUES (?,?)', [
    ('A','B'), ('B','C'), ('C','D'), ('A','X'), ('X','D')
])
conn.commit()

query = "WITH RECURSIVE paths AS (...) SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
**Bill of Materials (BOM)**: Calculate the total items required to build part `100`.
Part 100 requires 2 of part 101 and 1 of part 102. Part 101 requires 3 of part 103. 
Output the total sum of *leaf* components (in this case, 102 and 103). (2 * 3) + 1 = 7.

Expected output:
7

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE bom (part INTEGER, sub_part INTEGER, qty INTEGER)')
conn.executemany('INSERT INTO bom VALUES (?,?,?)', [
    (100, 101, 2), (100, 102, 1), (101, 103, 3)
])
conn.commit()

query = "WITH RECURSIVE explode AS (...) SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Date Series Generation**: Generate a sequence of dates between '2023-01-01' and '2023-01-03' using a recursive CTE, and output them.

Expected output:
2023-01-01
2023-01-02
2023-01-03

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')

query = "WITH RECURSIVE dates(d) AS (...) SELECT d FROM dates"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
**String Splitting**: SQLite lacks a built-in `SPLIT()` function. Use a recursive CTE to split the string `'A,B,C'` into individual rows.

Expected output:
A
B
C

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')

query = "WITH RECURSIVE split(word, str) AS (...) SELECT word FROM split"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Advanced JSON & Arrays (SQLite json1) (Q11–Q15)
            // ═══════════════════════════════════════════════════════════════

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
**Unnesting JSON Arrays**: The `orders` table has an `items` column containing a JSON array of product IDs: `[10, 20]`. 
Use `json_each()` to unnest the array and return a row for each product ID. Output `order_id` and `product_id`.

Expected output:
1 10
1 20
2 30

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (order_id INTEGER, items TEXT)')
conn.executemany('INSERT INTO orders VALUES (?,?)', [(1, '[10,20]'), (2, '[30]')])
conn.commit()

query = "SELECT order_id, json_each.value FROM orders, json_each(orders.items)"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**JSON Array Aggregation**: Aggregate rows back into a JSON array using `json_group_array()`.
Group the `tags` by `post_id` and output `post_id` and the JSON array string.

Expected output:
1 ["sql","db"]
2 ["python"]

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE post_tags (post_id INTEGER, tag TEXT)')
conn.executemany('INSERT INTO post_tags VALUES (?,?)', [(1,'sql'), (1,'db'), (2,'python')])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Updating JSON Dynamically**: Use `json_set()` to update the `$.status` property of a JSON object in the `jobs` table to `'completed'`.
Return the updated JSON strings.

Expected output:
{"id":1,"status":"completed"}
{"id":2,"status":"completed"}

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE jobs (data TEXT)')
conn.executemany('INSERT INTO jobs VALUES (?)', [('{"id":1,"status":"pending"}',), ('{"id":2,"status":"failed"}',)])
conn.commit()

query = "SELECT json_set(data, '$.status', 'completed') FROM jobs"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
**Filtering by JSON Array Membership**: Find `user_id`s where the `$.roles` JSON array contains the role `'admin'`.
*(Hint: unnest with json_each and filter).*

Expected output:
2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (user_id INTEGER, data TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [(1, '{"roles":["user"]}'), (2, '{"roles":["user","admin"]}')])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
**Deep JSON Extraction & Math**: The `sensors` table stores JSON arrays of readings. Unnest the arrays, extract the `$.temp` value from each object, and calculate the overall average temperature.

Expected output:
25.0

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sensors (readings TEXT)')
conn.executemany('INSERT INTO sensors VALUES (?)', [('[{"temp":20},{"temp":30}]',), ('[{"temp":25}]',)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Complex Window Frames & Analytics (Q16–Q20)
            // ═══════════════════════════════════════════════════════════════

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
**Running Total with Constraints**: Calculate a running sum of `amount`. If the running sum exceeds 100, clamp it to 100 for that row and all subsequent rows in the output display. (Use a `CASE` wrapped around the window function). Order by `id`. Output `id` and `clamped_sum`.

Expected output:
1 40
2 90
3 100
4 100

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE trans (id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO trans VALUES (?,?)', [(1,40), (2,50), (3,30), (4,10)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
**Median via NTILE**: Calculate the median salary. For an even number of records, it's the average of the two middle values. Use `NTILE()` or `ROW_NUMBER()` to identify the exact middle row(s).

Expected output:
60000.0

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE salaries (salary INTEGER)')
conn.executemany('INSERT INTO salaries VALUES (?)', [(40000,), (50000,), (70000,), (90000,)])
conn.commit()

query = "WITH ranked AS (...) SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
**The Mode (Most Frequent)**: Find the most common `age` in the `users` table. If tied, return the lower age. Use aggregation and `ORDER BY count DESC, age ASC LIMIT 1`.

Expected output:
25

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (age INTEGER)')
conn.executemany('INSERT INTO users VALUES (?)', [(20,), (25,), (25,), (30,), (30,)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
**Weighted Moving Average**: Calculate a 2-day weighted moving average: `(Today * 2 + Yesterday * 1) / 3`. Use `LAG()` to get yesterday's value. If there's no yesterday, use `Today` as the full value. Output `day` and `wma`.

Expected output:
1 10
2 16
3 26

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE metrics (day INTEGER, val INTEGER)')
conn.executemany('INSERT INTO metrics VALUES (?,?)', [(1,10), (2,20), (3,30)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Nth Value Simulation**: SQLite lacks `NTH_VALUE`. Find the **second highest** salary in the `employees` table without using `LIMIT 1 OFFSET 1` at the root query. (Use `DENSE_RANK` in a subquery).

Expected output:
80000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp (salary INTEGER)')
conn.executemany('INSERT INTO emp VALUES (?)', [(90000,), (80000,), (80000,), (70000,)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 450,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Pivoting, Unpivoting & Matrix Logic (Q21–Q25)
            // ═══════════════════════════════════════════════════════════════

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
**Dynamic Pivot (Group Concat)**: Pivot key-value attributes into a single string format per entity.
Output `entity_id` and a string like `color:red, size:L`. Use `GROUP_CONCAT` and order the pairs alphabetically.

Expected output:
1 color:red, size:L
2 color:blue

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE eav (entity_id INTEGER, attr TEXT, val TEXT)')
conn.executemany('INSERT INTO eav VALUES (?,?,?)', [
    (1, 'color', 'red'), (1, 'size', 'L'), (2, 'color', 'blue')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
**Unpivoting Columns to Rows**: Convert a wide table with `id, q1, q2` into a long table with `id, quarter, amount` using `UNION ALL`. Order by `id`, then `quarter`.

Expected output:
1 q1 100
1 q2 150
2 q1 200
2 q2 250

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (id INTEGER, q1 INTEGER, q2 INTEGER)')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [(1, 100, 150), (2, 200, 250)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
**Relational Division (Bought ALL)**: Find the `user_id` of users who purchased *every* product listed in the `products` table.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (product_id INTEGER)')
conn.executemany('INSERT INTO products VALUES (?)', [(10,), (20,)])
conn.execute('CREATE TABLE purchases (user_id INTEGER, product_id INTEGER)')
conn.executemany('INSERT INTO purchases VALUES (?,?)', [(1, 10), (1, 20), (2, 10)])
conn.commit()

# Count distinct products bought vs total products
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
**Matrix Multiplication**: Multiply two matrices A and B stored as `(row, col, value)`. Output the dot product for row 1 of A and col 1 of B. 
A: row 1 is `[2, 3]`. B: col 1 is `[4, 5]`. Product = `(2*4) + (3*5) = 23`.

Expected output:
23

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE a (row INTEGER, col INTEGER, val INTEGER)')
conn.executemany('INSERT INTO a VALUES (?,?,?)', [(1,1,2), (1,2,3)])
conn.execute('CREATE TABLE b (row INTEGER, col INTEGER, val INTEGER)')
conn.executemany('INSERT INTO b VALUES (?,?,?)', [(1,1,4), (2,1,5)])
conn.commit()

query = "SELECT SUM(a.val * b.val) FROM a JOIN b ON a.col = b.row WHERE a.row=1 AND b.col=1"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
**Jaccard Similarity**: Calculate the intersection over union of purchases between User 1 and User 2. User 1 bought `[A, B]`. User 2 bought `[B, C]`. Intersection = `[B]` (1). Union = `[A, B, C]` (3). Result = `1.0 / 3.0 = 0.333`. (Format to 3 decimals using `ROUND`).

Expected output:
0.333

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE purchases (user_id INTEGER, item TEXT)')
conn.executemany('INSERT INTO purchases VALUES (?,?)', [(1,'A'), (1,'B'), (2,'B'), (2,'C')])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Slowly Changing Dimensions & Event Sourcing (Q26–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**SCD Type 2 (Active Record)**: In an SCD2 table `emp_history`, find the `department` of `emp_id = 1` as of the date `'2023-06-01'`. Active records have `start_date <= date` and `end_date > date` (or `end_date IS NULL`).

Expected output:
HR

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp_history (emp_id INTEGER, department TEXT, start_date TEXT, end_date TEXT)')
conn.executemany('INSERT INTO emp_history VALUES (?,?,?,?)', [
    (1, 'IT', '2023-01-01', '2023-05-01'), 
    (1, 'HR', '2023-05-01', None)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
**Event Sourcing (Rebuild State)**: An append-only `events` table records balance changes. Rebuild the current balance for `account_id = 1` by summing all transactions.

Expected output:
150

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE events (account_id INTEGER, change INTEGER)')
conn.executemany('INSERT INTO events VALUES (?,?)', [(1, 100), (1, 200), (1, -150)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**Detecting Overlapping Ranges**: Identify if any records in the `subscriptions` table overlap in time for the same `user_id`. An overlap exists if `startA < endB` and `endA > startB`. Output `1` if an overlap exists, else `0`.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE subs (user_id INTEGER, start_date TEXT, end_date TEXT)')
conn.executemany('INSERT INTO subs VALUES (?,?,?)', [
    (1, '2023-01-01', '2023-03-01'), (1, '2023-02-01', '2023-04-01')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
**Detect Changed Attributes**: Compare the current incoming data against the most recent SCD2 record. If the `salary` differs, output the `emp_id`.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE current_emp (emp_id INTEGER, salary INTEGER)')
conn.executemany('INSERT INTO current_emp VALUES (?,?)', [(1, 60000), (2, 50000)])
conn.execute('CREATE TABLE emp_scd2 (emp_id INTEGER, salary INTEGER, is_active INTEGER)')
conn.executemany('INSERT INTO emp_scd2 VALUES (?,?,?)', [(1, 50000, 1), (2, 50000, 1)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**SCD2 Close Record Strategy**: Update the `emp_scd2` table. Set `is_active = 0` for `emp_id = 1`. Then select the `is_active` status for `emp_id = 1`.

Expected output:
0

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp_scd2 (emp_id INTEGER, is_active INTEGER)')
conn.executemany('INSERT INTO emp_scd2 VALUES (?,?)', [(1, 1), (2, 1)])
conn.commit()

conn.execute("UPDATE emp_scd2 SET is_active = 0 WHERE emp_id = 1")
for row in conn.execute("SELECT is_active FROM emp_scd2 WHERE emp_id = 1"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Advanced Aggregations & OLAP (Q31–Q35)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**ROLLUP Simulation**: SQLite lacks `GROUP BY ROLLUP`. Simulate it using `UNION ALL`. Output the sum per department, followed by a 'Total' row (where dept is 'Total'). Order by amount ASC.

Expected output:
HR 40
IT 60
Total 100

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (dept TEXT, amount INTEGER)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [('IT', 60), ('HR', 40)])
conn.commit()

query = "SELECT dept, SUM(amount) FROM sales GROUP BY dept UNION ALL SELECT 'Total', SUM(amount) FROM sales ORDER BY 2 ASC"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
**Pareto Principle (80/20 Rule)**: Find customers contributing to the top 80% of revenue. Compute running sum / total sum. Output the `customer_id`s whose running sum is `<= 0.8 * total_sum` (or the first one that crosses it, simplifying here to just the highest spender who accounts for > 50%).

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE rev (customer_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO rev VALUES (?,?)', [(1, 800), (2, 100), (3, 100)])
conn.commit()

query = "WITH ranked AS (SELECT customer_id, amount, SUM(amount) OVER (ORDER BY amount DESC) as running_total, (SELECT SUM(amount) FROM rev) as total FROM rev) SELECT customer_id FROM ranked WHERE running_total - amount < 0.8 * total"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Cohort Retention**: `users` joined in Jan. `logins` show activity. Find the percentage of Jan users who logged in during Feb (Month 2). Output as integer percentage `(count / total * 100)`.

Expected output:
50

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, join_month TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [(1, 'Jan'), (2, 'Jan')])
conn.execute('CREATE TABLE logins (id INTEGER, login_month TEXT)')
conn.executemany('INSERT INTO logins VALUES (?,?)', [(1, 'Feb')])
conn.commit()

query = "SELECT (COUNT(DISTINCT l.id) * 100) / (SELECT COUNT(*) FROM users) FROM logins l"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
**Running Product**: Calculate the running product of `val` using logarithmic addition: `EXP(SUM(LN(val)))`. In SQLite, you can simulate this via a recursive CTE or self-join if math functions are disabled. Output the final product of all rows.

Expected output:
24

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE numbers (val INTEGER)')
conn.executemany('INSERT INTO numbers VALUES (?)', [(2,), (3,), (4,)])
conn.commit()

# Simplest approach in SQLite: Cross join or CTE to multiply
query = "WITH RECURSIVE prod(p, idx) AS (SELECT 1, 1 UNION ALL SELECT p * (SELECT val FROM numbers LIMIT 1 OFFSET idx-1), idx + 1 FROM prod WHERE idx <= (SELECT COUNT(*) FROM numbers)) SELECT p FROM prod ORDER BY idx DESC LIMIT 1"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
**Percentile Rank Simulation**: Map `amount` to percentiles (0.0 to 1.0) using `PERCENT_RANK()` logic: `(rank - 1) / (total_rows - 1)`. Find the percentile of the amount `50` in `[10, 50, 100]`.

Expected output:
0.5

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE vals (amount INTEGER)')
conn.executemany('INSERT INTO vals VALUES (?)', [(10,), (50,), (100,)])
conn.commit()

query = "SELECT (RANK() OVER(ORDER BY amount) - 1.0) / (COUNT(*) OVER() - 1.0) FROM vals WHERE amount = 50"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Query Optimization & Anti-Patterns (Q36–Q40)
            // ═══════════════════════════════════════════════════════════════

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
**Deduplication (Keeping Lowest ID)**: The `users` table has duplicate `email`s. Use a `DELETE` query to remove duplicates, keeping only the row with the lowest `id`. Then output the remaining IDs ordered.

Expected output:
1
3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, email TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [(1, 'a@a.com'), (2, 'a@a.com'), (3, 'b@b.com')])
conn.commit()

conn.execute("DELETE FROM users WHERE id NOT IN (SELECT MIN(id) FROM users GROUP BY email)")
for row in conn.execute("SELECT id FROM users ORDER BY id"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**NOT IN Null Trap**: `WHERE id NOT IN (SELECT ref_id)` fails if `ref_id` contains `NULL`. Rewrite it using an `Anti-Join` (`LEFT JOIN ... WHERE right.id IS NULL`) to safely find `users` not in `blacklist`. Output name.

Expected output:
Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [(1, 'Alice'), (2, 'Bob')])
conn.execute('CREATE TABLE blacklist (ref_id INTEGER)')
conn.executemany('INSERT INTO blacklist VALUES (?)', [(2,), (None,)])
conn.commit()

query = "SELECT u.name FROM users u LEFT JOIN blacklist b ON u.id = b.ref_id WHERE b.ref_id IS NULL"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Greatest-N-Per-Group**: Find the highest paid employee in *each* department. Output `dept`, `name`, `salary`. Order by `dept`.

Expected output:
HR Carol 60000
IT Bob 80000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp (name TEXT, dept TEXT, salary INTEGER)')
conn.executemany('INSERT INTO emp VALUES (?,?,?)', [
    ('Alice', 'IT', 50000), ('Bob', 'IT', 80000), ('Carol', 'HR', 60000)
])
conn.commit()

query = "SELECT dept, name, salary FROM (SELECT *, ROW_NUMBER() OVER(PARTITION BY dept ORDER BY salary DESC) as rn FROM emp) WHERE rn=1 ORDER BY dept"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Range Encompassment**: Find `range_id`s in `ranges` that *fully encompass* the period `'2023-01-10'` to `'2023-01-20'`. Output `range_id`.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE ranges (range_id INTEGER, start_dt TEXT, end_dt TEXT)')
conn.executemany('INSERT INTO ranges VALUES (?,?,?)', [
    (1, '2023-01-01', '2023-01-31'), (2, '2023-01-15', '2023-01-25')
])
conn.commit()

query = "SELECT range_id FROM ranges WHERE start_dt <= '2023-01-10' AND end_dt >= '2023-01-20'"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
**Mutually Exclusive Records**: Find IDs that exist in table A OR table B, but **not both** (Symmetric Difference). Order by ID.

Expected output:
1
3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE a (id INTEGER)')
conn.executemany('INSERT INTO a VALUES (?)', [(1,), (2,)])
conn.execute('CREATE TABLE b (id INTEGER)')
conn.executemany('INSERT INTO b VALUES (?)', [(2,), (3,)])
conn.commit()

query = "SELECT id FROM a WHERE id NOT IN (SELECT id FROM b) UNION ALL SELECT id FROM b WHERE id NOT IN (SELECT id FROM a) ORDER BY id"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: Specialized Math, Geospatial & Domain Logic (Q41–Q45)
            // ═══════════════════════════════════════════════════════════════

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
**Linear Interpolation**: A value is missing for `day = 2`. Interpolate it using the average of `day 1` (10) and `day 3` (30). Output the interpolated value.

Expected output:
20

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE metrics (day INTEGER, val INTEGER)')
conn.executemany('INSERT INTO metrics VALUES (?,?)', [(1, 10), (3, 30)])
conn.commit()

query = "SELECT AVG(val) FROM metrics WHERE day IN (1, 3)"
for row in conn.execute(query):
    print(int(row[0]))
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
**Bounding Box Spatial Filter**: Find locations `(x, y)` within the box `x BETWEEN 0 AND 10` AND `y BETWEEN 0 AND 10`. Output `name`.

Expected output:
Park

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE locations (name TEXT, x INTEGER, y INTEGER)')
conn.executemany('INSERT INTO locations VALUES (?,?,?)', [('Park', 5, 5), ('Mall', 15, 5)])
conn.commit()

query = "SELECT name FROM locations WHERE x BETWEEN 0 AND 10 AND y BETWEEN 0 AND 10"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
**Inverse Relationships**: The `follows` table has `(follower, followed)`. Find users who follow each other mutually. Output pairs ordered by the lower ID first.

Expected output:
1 2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE follows (f1 INTEGER, f2 INTEGER)')
conn.executemany('INSERT INTO follows VALUES (?,?)', [(1, 2), (2, 1), (3, 1)])
conn.commit()

query = "SELECT a.f1, a.f2 FROM follows a JOIN follows b ON a.f1 = b.f2 AND a.f2 = b.f1 WHERE a.f1 < a.f2"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
**String Extraction**: Extract the domain name from an `email` column using `SUBSTR` and `INSTR`. Output the domain.

Expected output:
example.com

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (email TEXT)')
conn.executemany('INSERT INTO users VALUES (?)', [('alice@example.com',)])
conn.commit()

query = "SELECT SUBSTR(email, INSTR(email, '@') + 1) FROM users"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
**Exact Age Calculation**: Calculate exact age in years given a `dob` '2000-05-15' and today '2023-04-10'. (Should be 22, not 23, since May hasn't happened). Use SQLite date math formatting.

Expected output:
22

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')

# SQLite math trick: cast strftime('%Y%m%d') to int, subtract, divide by 10000
query = "SELECT (CAST(strftime('%Y%m%d', '2023-04-10') AS INTEGER) - CAST(strftime('%Y%m%d', '2000-05-15') AS INTEGER)) / 10000"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: Triggers, Constraints & Concurrency (Q46–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
**Soft Deletes**: Only query users where `deleted_at IS NULL`. Output remaining active names.

Expected output:
Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (name TEXT, deleted_at TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [('Alice', None), ('Bob', '2023-01-01')])
conn.commit()

query = "SELECT name FROM users WHERE deleted_at IS NULL"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**Optimistic Concurrency Control**: Execute an `UPDATE` on `balance` ONLY IF `version = 1`. Then increment `version` by 1. Output the new version and balance.

Expected output:
2 150

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE accounts (id INTEGER, balance INTEGER, version INTEGER)')
conn.executemany('INSERT INTO accounts VALUES (?,?,?)', [(1, 100, 1)])
conn.commit()

conn.execute("UPDATE accounts SET balance = 150, version = version + 1 WHERE id = 1 AND version = 1")
for row in conn.execute("SELECT version, balance FROM accounts"):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
**Simulate Trigger/Audit Log**: Perform an `UPDATE` on salary, and then insert a log string `'salary updated'` into the `audit` table. Output the audit log message.

Expected output:
salary updated

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE emp (salary INTEGER)')
conn.execute('CREATE TABLE audit (msg TEXT)')
conn.executemany('INSERT INTO emp VALUES (?)', [(50000,)])
conn.commit()

conn.execute("UPDATE emp SET salary = 60000")
conn.execute("INSERT INTO audit VALUES ('salary updated')")

for row in conn.execute("SELECT msg FROM audit"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**State Machine Valid Transitions**: Update `status` to `'Shipped'` ONLY IF current `status` is `'Processing'`. Output final status.

Expected output:
Shipped

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, status TEXT)')
conn.executemany('INSERT INTO orders VALUES (?,?)', [(1, 'Processing')])
conn.commit()

conn.execute("UPDATE orders SET status = 'Shipped' WHERE id = 1 AND status = 'Processing'")
for row in conn.execute("SELECT status FROM orders"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
**Strict Constraints (Check simulation)**: Prevent withdrawal if balance drops below 0. Simulate by doing `UPDATE ... WHERE balance - 200 >= 0`. Output the balance to verify it didn't drop below 0 if starting balance is 100.

Expected output:
100

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE accounts (balance INTEGER)')
conn.executemany('INSERT INTO accounts VALUES (?)', [(100,)])
conn.commit()

conn.execute("UPDATE accounts SET balance = balance - 200 WHERE balance - 200 >= 0")
for row in conn.execute("SELECT balance FROM accounts"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 400,
            ],
        ];

        // Insert questions; collect IDs
        $questionIds = [];

        foreach ($questionDefs as $def) {
            $row = DB::table('coding_questions')->where([
                'challenge_id' => $challenge->id,
                'order_index'  => $def['order_index'],
            ])->first();

            if (! $row) {
                $id = DB::table('coding_questions')->insertGetId(array_merge(
                    ['challenge_id' => $challenge->id, 'language' => 'python'],
                    $def,
                    ['created_at' => now(), 'updated_at' => now()]
                ));
            } else {
                $id = $row->id;
            }

            $questionIds[$def['order_index']] = $id;
        }

        // ─────────────────────────────────────────────────────────────────
        // 3. TEST CASES (4 per question: 2 visible, 2 hidden)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding test cases...');

        $now = now()->toDateTimeString();

        $seed = function (int $ord, array $cases) use ($questionIds, $now): void {
            $qid = $questionIds[$ord] ?? null;
            if (! $qid) return;

            if (DB::table('test_cases')->where('coding_question_id', $qid)->exists()) {
                $this->command->warn("  test_cases for Q{$ord} already exist — skipping.");
                return;
            }

            $rows = array_map(fn ($c) => array_merge(
                ['coding_question_id' => $qid, 'created_at' => $now, 'updated_at' => $now],
                $c
            ), $cases);

            DB::table('test_cases')->insert($rows);
        };

        // Standardized deterministic SQL tests. Hardcoded expected string matching.
        $staticExpected = [
            1 => "1 3\n6 7\n10 10",
            2 => "3 4\n8 9",
            3 => "1 10 30\n2 150 160\n3 300 300",
            4 => "3",
            5 => "3",
            6 => "3",
            7 => "2",
            8 => "7",
            9 => "2023-01-01\n2023-01-02\n2023-01-03",
            10 => "A\nB\nC",
            11 => "1 10\n1 20\n2 30",
            12 => "1 [\"sql\",\"db\"]\n2 [\"python\"]",
            13 => "{\"id\":1,\"status\":\"completed\"}\n{\"id\":2,\"status\":\"completed\"}",
            14 => "2",
            15 => "25.0",
            16 => "1 40\n2 90\n3 100\n4 100",
            17 => "60000.0",
            18 => "25",
            19 => "1 10\n2 16\n3 26",
            20 => "80000",
            21 => "1 color:red, size:L\n2 color:blue",
            22 => "1 q1 100\n1 q2 150\n2 q1 200\n2 q2 250",
            23 => "1",
            24 => "23",
            25 => "0.333",
            26 => "HR",
            27 => "150",
            28 => "1",
            29 => "1",
            30 => "0",
            31 => "HR 40\nIT 60\nTotal 100",
            32 => "1",
            33 => "50",
            34 => "24",
            35 => "0.5",
            36 => "1\n3",
            37 => "Alice",
            38 => "HR Carol 60000\nIT Bob 80000",
            39 => "1",
            40 => "1\n3",
            41 => "20",
            42 => "Park",
            43 => "1 2",
            44 => "example.com",
            45 => "22",
            46 => "Alice",
            47 => "2 150",
            48 => "salary updated",
            49 => "Shipped",
            50 => "100"
        ];

        foreach ($staticExpected as $qNum => $expectedOut) {
            $seed($qNum, [
                ['input' => null, 'expected_output' => $expectedOut, 'is_hidden' => false, 'order_index' => 1],
                ['input' => null, 'expected_output' => $expectedOut, 'is_hidden' => false, 'order_index' => 2],
                ['input' => null, 'expected_output' => $expectedOut, 'is_hidden' => true,  'order_index' => 3],
                ['input' => null, 'expected_output' => $expectedOut, 'is_hidden' => true,  'order_index' => 4],
            ]);
        }

        $this->command->info('✅ Module 10 Coding (Professional) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}