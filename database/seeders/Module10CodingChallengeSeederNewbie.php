<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 10 — Relational Databases & SQL (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all SQL/DB topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   10.1  Relational Database Fundamentals
 *   10.2  SQL Basics: SELECT, WHERE, ORDER BY, LIMIT
 *   10.3  Aggregate Functions & GROUP BY
 *   10.4  JOINs: INNER, LEFT, RIGHT, FULL
 *   10.5  Subqueries & CTEs
 *   10.6  Window Functions
 *   10.7  Database Design & Normalization
 *   10.8  Indexing & Query Performance
 *   10.10 NoSQL Databases & When to Use Them
 *
 * All tasks run in Python using the built-in sqlite3 module so they
 * execute on the same Python judge as the rest of DataSensei.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module10CodingChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (! $category) {
            $this->command->error('Newbie category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 10 — Relational Databases & SQL (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Relational Databases & SQL',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply relational database and SQL concepts through hands-on Python tasks — query tables with SELECT, WHERE, ORDER BY, and LIMIT, perform aggregations and GROUP BY, write JOINs across multiple tables, use subqueries and CTEs, apply window functions, work with normalized schemas, understand indexing, and simulate NoSQL document stores — all using Python\'s built-in sqlite3 module. Each task sets up a small in-memory database for you; your job is to write the right SQL or Python logic.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 10,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Relational Database Fundamentals (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A **relational database** stores data in tables made up of rows (records) and columns (attributes). Every table should have a **primary key** — a column (or set of columns) that uniquely identifies each row.

The `students` table below holds information about enrolled students:

| student_id | name  | age | major            |
|-----------|-------|-----|------------------|
| 1         | Alice | 20  | Computer Science |
| 2         | Bob   | 22  | Mathematics      |
| 3         | Carol | 21  | Computer Science |
| 4         | Dave  | 23  | Physics          |
| 5         | Eve   | 20  | Mathematics      |
| 6         | Frank | 24  | Computer Science |

Write a SQL query that returns the **total number of rows** in the `students` table.

Expected output:
```
6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (
    student_id INTEGER, name TEXT, age INTEGER, major TEXT
)''')
conn.executemany('INSERT INTO students VALUES (?,?,?,?)', [
    (1,'Alice',20,'Computer Science'),
    (2,'Bob',22,'Mathematics'),
    (3,'Carol',21,'Computer Science'),
    (4,'Dave',23,'Physics'),
    (5,'Eve',20,'Mathematics'),
    (6,'Frank',24,'Computer Science'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
In a relational database, a column like `major` can repeat across many rows. To see which unique values exist, you use `SELECT DISTINCT`.

Using the same `students` table:

| student_id | name  | age | major            |
|-----------|-------|-----|------------------|
| 1         | Alice | 20  | Computer Science |
| 2         | Bob   | 22  | Mathematics      |
| 3         | Carol | 21  | Computer Science |
| 4         | Dave  | 23  | Physics          |
| 5         | Eve   | 20  | Mathematics      |
| 6         | Frank | 24  | Computer Science |

Write a SQL query that returns all **distinct majors**, ordered **alphabetically**.

Expected output:
```
Computer Science
Mathematics
Physics
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (
    student_id INTEGER, name TEXT, age INTEGER, major TEXT
)''')
conn.executemany('INSERT INTO students VALUES (?,?,?,?)', [
    (1,'Alice',20,'Computer Science'),
    (2,'Bob',22,'Mathematics'),
    (3,'Carol',21,'Computer Science'),
    (4,'Dave',23,'Physics'),
    (5,'Eve',20,'Mathematics'),
    (6,'Frank',24,'Computer Science'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
A **primary key** uniquely identifies a row. Looking up a row by its primary key is the most basic and efficient type of query.

Using the same `students` table, read a `student_id` from input and print that student's **name**.

Example:
```
Input:  1
Output: Alice
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (
    student_id INTEGER, name TEXT, age INTEGER, major TEXT
)''')
conn.executemany('INSERT INTO students VALUES (?,?,?,?)', [
    (1,'Alice',20,'Computer Science'),
    (2,'Bob',22,'Mathematics'),
    (3,'Carol',21,'Computer Science'),
    (4,'Dave',23,'Physics'),
    (5,'Eve',20,'Mathematics'),
    (6,'Frank',24,'Computer Science'),
])
conn.commit()

student_id = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [student_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Relational databases support **NULL** to represent missing or unknown values. Detecting NULLs is an important part of data quality in any database.

The `employees` table has some missing salaries:

| id | name  | salary | department |
|----|-------|--------|-----------|
| 1  | Alice | 70000  | IT        |
| 2  | Bob   | NULL   | HR        |
| 3  | Carol | 60000  | IT        |
| 4  | Dave  | NULL   | Sales     |
| 5  | Eve   | 80000  | IT        |

Write a SQL query that counts how many employees have a **NULL salary**.

Expected output:
```
2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, department TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',None,'HR'),
    (3,'Carol',60000,'IT'),
    (4,'Dave',None,'Sales'),
    (5,'Eve',80000,'IT'),
])
conn.commit()

# Write your SQL query below (use IS NULL)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
A **foreign key** is a column in one table that references the primary key of another table. When a foreign key value has no matching primary key in the referenced table, the row is called an **orphan** — a sign of broken referential integrity.

**orders**
| order_id | customer_id | amount |
|---------|------------|--------|
| 1       | 101        | 500    |
| 2       | 102        | 300    |
| 3       | 999        | 200    |
| 4       | 101        | 150    |

**customers**
| id  | name  |
|-----|-------|
| 101 | Alice |
| 102 | Bob   |
| 103 | Carol |

Order 3 has `customer_id = 999`, which does not exist in `customers`.

Write a SQL query that counts the number of **orphan orders** (orders whose `customer_id` does not appear in `customers`).

Expected output:
```
1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    order_id INTEGER, customer_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,101,500),(2,102,300),(3,999,200),(4,101,150),
])
conn.execute('''CREATE TABLE customers (
    id INTEGER, name TEXT
)''')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (101,'Alice'),(102,'Bob'),(103,'Carol'),
])
conn.commit()

# Write your SQL query below (use NOT IN or NOT EXISTS)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: SQL Basics — SELECT, WHERE, ORDER BY, LIMIT (Q6–Q11)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
The `WHERE` clause filters rows based on a condition. Combined with `ORDER BY`, you can return a sorted, filtered result set.

The `products` table:

| id | name      | price | category    |
|----|-----------|-------|-------------|
| 1  | Laptop    | 1200  | Electronics |
| 2  | Phone     | 600   | Electronics |
| 3  | Desk      | 400   | Furniture   |
| 4  | Tablet    | 800   | Electronics |
| 5  | Chair     | 300   | Furniture   |
| 6  | USB Cable | 15    | Electronics |

Write a SQL query that returns the **name** of every product in the `'Electronics'` category, ordered **alphabetically by name**.

Expected output:
```
Laptop
Phone
Tablet
USB Cable
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, name TEXT, price INTEGER, category TEXT
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Laptop',1200,'Electronics'),
    (2,'Phone',600,'Electronics'),
    (3,'Desk',400,'Furniture'),
    (4,'Tablet',800,'Electronics'),
    (5,'Chair',300,'Furniture'),
    (6,'USB Cable',15,'Electronics'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
`ORDER BY` controls the sort order of results. You can sort by multiple columns — the second column breaks ties from the first.

Using the `students` table:

| student_id | name  | age | major            |
|-----------|-------|-----|------------------|
| 1         | Alice | 20  | Computer Science |
| 2         | Bob   | 22  | Mathematics      |
| 3         | Carol | 21  | Computer Science |
| 4         | Dave  | 23  | Physics          |
| 5         | Eve   | 20  | Mathematics      |
| 6         | Frank | 24  | Computer Science |

Write a SQL query that returns each student's `name` and `age`, ordered by `age` **ascending**, then by `name` **ascending** for ties.

Expected output:
```
Alice 20
Eve 20
Carol 21
Bob 22
Dave 23
Frank 24
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (
    student_id INTEGER, name TEXT, age INTEGER, major TEXT
)''')
conn.executemany('INSERT INTO students VALUES (?,?,?,?)', [
    (1,'Alice',20,'Computer Science'),
    (2,'Bob',22,'Mathematics'),
    (3,'Carol',21,'Computer Science'),
    (4,'Dave',23,'Physics'),
    (5,'Eve',20,'Mathematics'),
    (6,'Frank',24,'Computer Science'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
`LIMIT` restricts how many rows are returned. Combined with `ORDER BY DESC`, it's a common pattern for finding the **top-N** results.

Using the `products` table:

| id | name      | price | category    |
|----|-----------|-------|-------------|
| 1  | Laptop    | 1200  | Electronics |
| 2  | Phone     | 600   | Electronics |
| 3  | Desk      | 400   | Furniture   |
| 4  | Tablet    | 800   | Electronics |
| 5  | Chair     | 300   | Furniture   |
| 6  | USB Cable | 15    | Electronics |

Write a SQL query that returns the **name and price** of the **3 most expensive products**, ordered by price **descending**.

Expected output:
```
Laptop 1200
Tablet 800
Phone 600
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, name TEXT, price INTEGER, category TEXT
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Laptop',1200,'Electronics'),
    (2,'Phone',600,'Electronics'),
    (3,'Desk',400,'Furniture'),
    (4,'Tablet',800,'Electronics'),
    (5,'Chair',300,'Furniture'),
    (6,'USB Cable',15,'Electronics'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
The `WHERE` clause supports comparison operators (`>`, `<`, `>=`, `<=`, `=`, `<>`) and `BETWEEN` for range filters.

Using the `products` table, write a SQL query that returns the **name and price** of products whose price is **between 500 and 900 (inclusive)**, ordered by price **ascending**.

Expected output:
```
Phone 600
Tablet 800
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, name TEXT, price INTEGER, category TEXT
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Laptop',1200,'Electronics'),
    (2,'Phone',600,'Electronics'),
    (3,'Desk',400,'Furniture'),
    (4,'Tablet',800,'Electronics'),
    (5,'Chair',300,'Furniture'),
    (6,'USB Cable',15,'Electronics'),
])
conn.commit()

# Write your SQL query below (use BETWEEN or >= and <=)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
You can combine `WHERE` conditions with `AND` to require multiple criteria at once.

Using the `students` table, write a SQL query that returns the **name** of every student older than 21, ordered by name **alphabetically**.

Expected output:
```
Bob
Dave
Frank
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (
    student_id INTEGER, name TEXT, age INTEGER, major TEXT
)''')
conn.executemany('INSERT INTO students VALUES (?,?,?,?)', [
    (1,'Alice',20,'Computer Science'),
    (2,'Bob',22,'Mathematics'),
    (3,'Carol',21,'Computer Science'),
    (4,'Dave',23,'Physics'),
    (5,'Eve',20,'Mathematics'),
    (6,'Frank',24,'Computer Science'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Multiple `WHERE` conditions can be combined with `AND` and `OR`. Use parentheses when mixing both to make precedence explicit.

Using the `products` table, write a SQL query that returns the **name and price** of products that are in the `'Electronics'` category **AND** cost more than 500, ordered by price **descending**.

Expected output:
```
Laptop 1200
Tablet 800
Phone 600
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, name TEXT, price INTEGER, category TEXT
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Laptop',1200,'Electronics'),
    (2,'Phone',600,'Electronics'),
    (3,'Desk',400,'Furniture'),
    (4,'Tablet',800,'Electronics'),
    (5,'Chair',300,'Furniture'),
    (6,'USB Cable',15,'Electronics'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Aggregate Functions & GROUP BY (Q12–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Aggregate functions** compute a single result from a set of rows. The most common are `COUNT`, `SUM`, `AVG`, `MAX`, and `MIN`.

The `orders` table:

| id | customer_id | category    | amount |
|----|------------|-------------|--------|
| 1  | 101        | Electronics | 1200   |
| 2  | 102        | Food        | 50     |
| 3  | 101        | Electronics | 800    |
| 4  | 103        | Furniture   | 600    |
| 5  | 102        | Food        | 30     |
| 6  | 101        | Furniture   | 900    |
| 7  | 104        | Books       | 25     |

Write a SQL query that returns the **total number of orders** and the **total amount** across all orders, space-separated.

Expected output:
```
7 3605
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
`GROUP BY` splits rows into groups based on a column's value, then applies an aggregate function to each group.

Using the same `orders` table, write a SQL query that returns the **total amount per category**, ordered alphabetically by category.

Expected output:
```
Books 25
Electronics 2000
Food 80
Furniture 1500
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
`AVG()` computes the mean of a column within each group when combined with `GROUP BY`.

Using the same `orders` table, write a SQL query that returns the **average amount per category**, ordered alphabetically by category.

Expected output:
```
Books 25.0
Electronics 1000.0
Food 40.0
Furniture 750.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
`MAX()` and `MIN()` return the largest and smallest values in a column.

Using the same `orders` table, write a SQL query that returns the **maximum amount** and the **minimum amount**, space-separated, in a single row.

Expected output:
```
1200 25
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
`HAVING` filters **groups** after `GROUP BY` — it works like `WHERE` but operates on aggregated values.

Using the same `orders` table, write a SQL query that returns each **category** and its **order count**, but only for categories that have **more than one order**. Order the result alphabetically by category.

Expected output:
```
Electronics 2
Food 2
Furniture 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below (use GROUP BY + HAVING COUNT(*) > 1)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
`COUNT(DISTINCT col)` counts the number of **unique** values in a column, ignoring duplicates.

Using the same `orders` table, write a SQL query that returns the **number of distinct customers** who placed at least one order.

Expected output:
```
4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,'Electronics',1200),
    (2,102,'Food',50),
    (3,101,'Electronics',800),
    (4,103,'Furniture',600),
    (5,102,'Food',30),
    (6,101,'Furniture',900),
    (7,104,'Books',25),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: JOINs — INNER, LEFT, RIGHT, FULL (Q18–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
An **INNER JOIN** returns only the rows where the join condition is satisfied in **both** tables. Rows with no match are excluded.

**employees**
| id | name  | salary | dept_id |
|----|-------|--------|---------|
| 1  | Alice | 50000  | 1       |
| 2  | Bob   | 45000  | 2       |
| 3  | Carol | 70000  | 1       |
| 4  | Dave  | 65000  | 3       |
| 5  | Eve   | 55000  | 2       |

**departments**
| dept_id | dept_name |
|---------|----------|
| 1       | IT        |
| 2       | HR        |
| 3       | Sales     |
| 4       | Marketing |

Write a SQL query that joins `employees` with `departments` and returns each employee's **name** and **department name**, ordered alphabetically by employee name.

Expected output:
```
Alice IT
Bob HR
Carol IT
Dave Sales
Eve HR
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,1),(2,'Bob',45000,2),(3,'Carol',70000,1),
    (4,'Dave',65000,3),(5,'Eve',55000,2),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?)', [
    (1,'IT'),(2,'HR'),(3,'Sales'),(4,'Marketing'),
])
conn.commit()

# Write your SQL query below (INNER JOIN employees with departments)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
A **LEFT JOIN** returns **all rows from the left table** and the matching rows from the right table. If there is no match, right-table columns are `NULL`.

Using the same `employees` and `departments` tables, write a SQL query that lists every **department name** and how many employees it has (including departments with **zero employees**), ordered alphabetically by department name.

Expected output:
```
HR 2
IT 2
Marketing 0
Sales 1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,1),(2,'Bob',45000,2),(3,'Carol',70000,1),
    (4,'Dave',65000,3),(5,'Eve',55000,2),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?)', [
    (1,'IT'),(2,'HR'),(3,'Sales'),(4,'Marketing'),
])
conn.commit()

# Write your SQL query below (LEFT JOIN departments → employees, then GROUP BY)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
A classic use of `LEFT JOIN` is finding rows in the left table that have **no match** in the right table — by filtering on `WHERE right_table.key IS NULL`.

Using the same `departments` and `employees` tables, write a SQL query that returns the names of departments that have **no employees**, ordered alphabetically.

Expected output:
```
Marketing
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,1),(2,'Bob',45000,2),(3,'Carol',70000,1),
    (4,'Dave',65000,3),(5,'Eve',55000,2),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?)', [
    (1,'IT'),(2,'HR'),(3,'Sales'),(4,'Marketing'),
])
conn.commit()

# Write your SQL query below (LEFT JOIN + WHERE e.id IS NULL)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
`INNER JOIN` can be combined with `GROUP BY` and aggregate functions to produce grouped summaries across related tables.

Using the same `employees` and `departments` tables, write a SQL query that returns each **department name** and the **total salary** of its employees, ordered alphabetically by department name. (Only include departments that have at least one employee.)

Expected output:
```
HR 100000
IT 120000
Sales 65000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,1),(2,'Bob',45000,2),(3,'Carol',70000,1),
    (4,'Dave',65000,3),(5,'Eve',55000,2),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?)', [
    (1,'IT'),(2,'HR'),(3,'Sales'),(4,'Marketing'),
])
conn.commit()

# Write your SQL query below (INNER JOIN + GROUP BY + SUM)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
You can join **three or more tables** by chaining `JOIN` clauses. Each join links two tables using a shared key.

**customers**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |

**products**
| id | name   |
|----|--------|
| 1  | Laptop |
| 2  | Phone  |
| 3  | Tablet |

**orders**
| id | customer_id | product_id | amount |
|----|------------|-----------|--------|
| 1  | 1          | 1         | 1200   |
| 2  | 3          | 2         | 800    |
| 3  | 1          | 3         | 600    |
| 4  | 2          | 1         | 1200   |

Read an `order_id` from input and print the **customer name**, **product name**, and **amount**, space-separated.

Example:
```
Input:  1
Output: Alice Laptop 1200
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customers (id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),
])
conn.execute('''CREATE TABLE products (id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO products VALUES (?,?)', [
    (1,'Laptop'),(2,'Phone'),(3,'Tablet'),
])
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, product_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,3,2,800),(3,1,3,600),(4,2,1,1200),
])
conn.commit()

order_id = int(input())
# Write your SQL query below (join all three tables)
query = "SELECT ..."
for row in conn.execute(query, [order_id]):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
A **self-join** joins a table to itself. It is useful for finding relationships between rows in the same table, such as pairs of employees in the same department.

Using the `employees` table, write a SQL query that finds all pairs of employees who work in the **same department**, where the first name comes alphabetically before the second. Print `name1 name2` per pair, ordered by `name1`.

Expected output:
```
Alice Carol
Bob Eve
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,1),(2,'Bob',45000,2),(3,'Carol',70000,1),
    (4,'Dave',65000,3),(5,'Eve',55000,2),
])
conn.commit()

# Write your SQL query below (self-join on dept_id with e1.name < e2.name)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
A **FULL OUTER JOIN** returns all rows from both tables, with `NULL` where there is no match. SQLite does not support `FULL OUTER JOIN` natively, but you can simulate it using `LEFT JOIN UNION ALL` with an anti-join.

**customers**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |

**orders**
| id | customer_id | amount |
|----|------------|--------|
| 1  | 1          | 500    |
| 2  | 3          | 300    |
| 3  | 4          | 200    |

`Bob` (id 2) has no orders. Order 3 references `customer_id = 4` which does not exist in `customers`.

Simulate a FULL OUTER JOIN that returns every customer name and every order amount, printing `None` where there is no match. Order the result by customer name (NULLs last).

Expected output:
```
Alice 500
Bob None
Carol 300
None 200
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customers (id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),
])
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,500),(2,3,300),(3,4,200),
])
conn.commit()

# Simulate FULL OUTER JOIN using UNION ALL:
# Part 1: All customers LEFT JOIN orders
# Part 2: Orders with no matching customer (anti-join)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Subqueries & CTEs (Q25–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
A **subquery** is a query nested inside another query. When placed in a `WHERE` clause, it can compute a value that the outer query compares against.

The `employees` table:

| id | name  | salary | dept   |
|----|-------|--------|--------|
| 1  | Alice | 70000  | IT     |
| 2  | Bob   | 45000  | HR     |
| 3  | Carol | 85000  | IT     |
| 4  | Dave  | 55000  | Sales  |
| 5  | Eve   | 60000  | HR     |
| 6  | Frank | 90000  | IT     |

Write a SQL query that returns the **names** of employees whose salary is **above the company-wide average**, ordered alphabetically.

Expected output:
```
Alice
Carol
Frank
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',45000,'HR'),
    (3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),
    (5,'Eve',60000,'HR'),
    (6,'Frank',90000,'IT'),
])
conn.commit()

# Write your SQL query below (use subquery to compute AVG)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
A **subquery in the FROM clause** acts as a temporary table (also called a derived table). The outer query then selects from this derived table.

Using the same `employees` table, write a SQL query that uses a **subquery in FROM** to compute the total salary per department, then selects all departments and their totals. Order by department name alphabetically.

Expected output:
```
HR 105000
IT 245000
Sales 55000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',45000,'HR'),
    (3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),
    (5,'Eve',60000,'HR'),
    (6,'Frank',90000,'IT'),
])
conn.commit()

# Write your SQL query below (subquery in FROM clause)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
A **correlated subquery** references a column from the outer query. It is re-evaluated for every row of the outer query.

Using the same `employees` table, write a SQL query that finds the **highest earner in each department** (the employee whose salary equals the maximum salary in their department). Print `name dept`, ordered alphabetically by name.

Expected output:
```
Dave Sales
Eve HR
Frank IT
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',45000,'HR'),
    (3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),
    (5,'Eve',60000,'HR'),
    (6,'Frank',90000,'IT'),
])
conn.commit()

# Write your SQL query below (correlated subquery referencing outer query's dept)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
A **CTE (Common Table Expression)** uses the `WITH` keyword to define a named temporary result set at the top of a query. It improves readability and can be referenced multiple times.

Using the same `employees` table, write a SQL query that uses a CTE named `high_salary` to select employees earning more than 70000, then returns their **names** ordered alphabetically.

Expected output:
```
Carol
Frank
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',45000,'HR'),
    (3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),
    (5,'Eve',60000,'HR'),
    (6,'Frank',90000,'IT'),
])
conn.commit()

# Write your SQL query below (WITH high_salary AS (...) SELECT ...)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
CTEs can also perform **aggregations**. This is useful when you want to first compute group-level statistics and then query the result.

The `employees` table (with clean averages):

| id | name  | salary | dept  |
|----|-------|--------|-------|
| 1  | Alice | 70000  | IT    |
| 2  | Bob   | 48000  | HR    |
| 3  | Carol | 80000  | IT    |
| 4  | Dave  | 55000  | Sales |
| 5  | Eve   | 52000  | HR    |
| 6  | Frank | 90000  | IT    |

Write a SQL query using a CTE named `dept_avg` that computes the **average salary per department**, then selects all departments and their averages. Order by department name alphabetically.

Expected output:
```
HR 50000.0
IT 80000.0
Sales 55000.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),
    (2,'Bob',48000,'HR'),
    (3,'Carol',80000,'IT'),
    (4,'Dave',55000,'Sales'),
    (5,'Eve',52000,'HR'),
    (6,'Frank',90000,'IT'),
])
conn.commit()

# Write your SQL query below (WITH dept_avg AS (...) SELECT ...)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
A subquery with `IN` lets you filter rows whose column value appears in the result of another query.

**customers**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |
| 4  | Dave  |

**orders**
| id | customer_id | amount |
|----|------------|--------|
| 1  | 1          | 1200   |
| 2  | 2          | 300    |
| 3  | 3          | 800    |
| 4  | 1          | 150    |
| 5  | 4          | 600    |

Write a SQL query that returns the **names** of customers who have placed at least one order with an amount **greater than 500**, ordered alphabetically.

Expected output:
```
Alice
Carol
Dave
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customers (id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,1200),(2,2,300),(3,3,800),(4,1,150),(5,4,600),
])
conn.commit()

# Write your SQL query below (use IN with a subquery)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Window Functions (Q31–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
**Window functions** compute values across a set of rows related to the current row — without collapsing them into a single output row like `GROUP BY` does.

`ROW_NUMBER() OVER (ORDER BY col)` assigns a unique sequential integer to each row based on the specified order.

The `sales` table:

| sale_id | amount | month   |
|---------|--------|---------|
| 1       | 1200   | 2023-01 |
| 2       | 800    | 2023-01 |
| 3       | 1500   | 2023-02 |
| 4       | 600    | 2023-02 |
| 5       | 900    | 2023-03 |
| 6       | 2000   | 2023-03 |

Write a SQL query that returns `sale_id`, `amount`, and a `row_num` column assigned by `ROW_NUMBER()` ordered by `amount` **descending**. Order the output by `row_num`.

Expected output:
```
6 2000 1
3 1500 2
1 1200 3
5 900 4
2 800 5
4 600 6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (ROW_NUMBER() OVER (ORDER BY amount DESC))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
`RANK() OVER (PARTITION BY col ORDER BY col2)` ranks rows **within each partition** (group). Rows with the same value receive the same rank, and the next rank skips accordingly.

Using the same `sales` table, write a SQL query that returns `sale_id`, `month`, `amount`, and the `RANK()` of each sale **within its month** by amount descending. Order by `month` then `rank`.

Expected output:
```
1 2023-01 1200 1
2 2023-01 800 2
3 2023-02 1500 1
4 2023-02 600 2
6 2023-03 2000 1
5 2023-03 900 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (RANK() OVER (PARTITION BY month ORDER BY amount DESC))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
`SUM(col) OVER (ORDER BY col2)` computes a **running (cumulative) total** — each row receives the sum of all rows up to and including itself in the specified order.

Using the same `sales` table, write a SQL query that returns `sale_id`, `amount`, and the **running total** of amount ordered by `sale_id`.

Expected output:
```
1 1200 1200
2 800 2000
3 1500 3500
4 600 4100
5 900 5000
6 2000 7000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (SUM(amount) OVER (ORDER BY sale_id))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
`LAG(col, n)` accesses the value of `col` from the row **n positions before** the current row. When there is no previous row, it returns `NULL`.

Using the same `sales` table, write a SQL query that returns `sale_id`, `amount`, and the **previous row's amount** (`LAG(amount)`) ordered by `sale_id`.

Expected output:
```
1 1200 None
2 800 1200
3 1500 800
4 600 1500
5 900 600
6 2000 900
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (LAG(amount) OVER (ORDER BY sale_id))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
`LEAD(col, n)` accesses the value of `col` from the row **n positions ahead** of the current row. When there is no next row, it returns `NULL`.

Using the same `sales` table, write a SQL query that returns `sale_id`, `amount`, and the **next row's amount** (`LEAD(amount)`) ordered by `sale_id`.

Expected output:
```
1 1200 800
2 800 1500
3 1500 600
4 600 900
5 900 2000
6 2000 None
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (LEAD(amount) OVER (ORDER BY sale_id))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
`AVG(col) OVER (PARTITION BY group_col)` computes the **average within each partition** and attaches it to every row in that partition — without grouping rows together.

Using the same `sales` table, write a SQL query that returns `sale_id`, `amount`, `month`, and the **average amount for that month** as a window function. Order by `sale_id`.

Expected output:
```
1 1200 2023-01 1000.0
2 800 2023-01 1000.0
3 1500 2023-02 1050.0
4 600 2023-02 1050.0
5 900 2023-03 1450.0
6 2000 2023-03 1450.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    sale_id INTEGER, amount INTEGER, month TEXT
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Write your SQL query below (AVG(amount) OVER (PARTITION BY month))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Database Design & Normalization (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
**First Normal Form (1NF)** requires that every column holds **atomic (indivisible) values** — no comma-separated lists, no arrays. A cell like `"Python,SQL"` violates 1NF because it stores multiple values in one field.

The `employees_skills` table stores skills as a single comma-separated string:

| id | name  | skills        |
|----|-------|--------------|
| 1  | Alice | Python,SQL   |
| 2  | Bob   | Java         |
| 3  | Carol | SQL,Excel,R  |
| 4  | Dave  | Python       |

Write Python code that **counts how many rows violate 1NF** (i.e., the `skills` column contains a comma).

Expected output:
```
2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees_skills (
    id INTEGER, name TEXT, skills TEXT
)''')
conn.executemany('INSERT INTO employees_skills VALUES (?,?,?)', [
    (1,'Alice','Python,SQL'),
    (2,'Bob','Java'),
    (3,'Carol','SQL,Excel,R'),
    (4,'Dave','Python'),
])
conn.commit()

# Count rows where the skills field contains a comma (violates 1NF)
count = 0
for row in conn.execute("SELECT skills FROM employees_skills"):
    # Your logic here
    pass
print(count)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
**Second Normal Form (2NF)** requires 1NF and that every non-key attribute depends on the **whole** primary key. A proper 2NF design separates data into related tables linked by foreign keys.

Here, `students`, `courses`, and `enrollments` are in a correctly normalized 2NF schema:

**students**: (student_id, name)  
**courses**: (course_id, course_name, credits)  
**enrollments**: (student_id, course_id)

Read a `student_id` from input and print the **course names** that student is enrolled in, ordered alphabetically.

Example:
```
Input:  1
Output:
CS101
Math
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE students (student_id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO students VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),
])
conn.execute('''CREATE TABLE courses (
    course_id INTEGER, course_name TEXT, credits INTEGER
)''')
conn.executemany('INSERT INTO courses VALUES (?,?,?)', [
    (1,'CS101',4),(2,'Math',3),(3,'Physics',3),(4,'English',2),
])
conn.execute('''CREATE TABLE enrollments (student_id INTEGER, course_id INTEGER)''')
conn.executemany('INSERT INTO enrollments VALUES (?,?)', [
    (1,1),(1,2),(2,2),(2,3),(3,1),(3,4),
])
conn.commit()

student_id = int(input())
# Write your SQL query below (JOIN enrollments with courses, filter by student_id)
query = "SELECT ..."
for row in conn.execute(query, [student_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
**Third Normal Form (3NF)** eliminates **transitive dependencies** — non-key attributes must depend only on the primary key, not on other non-key attributes.

An un-normalized table might store `dept_name` and `dept_location` directly in the `employees` table. In 3NF, these move to a separate `departments` table.

**employees**: (emp_id, name, dept_id)  
**departments**: (dept_id, dept_name, location)

Read an `emp_id` from input and print that employee's **department location**.

Example:
```
Input:  2
Output: Chicago
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    emp_id INTEGER, name TEXT, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',1),(2,'Bob',2),(3,'Carol',1),(4,'Dave',3),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT, location TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?,?)', [
    (1,'IT','New York'),(2,'HR','Chicago'),(3,'Sales','Boston'),
])
conn.commit()

emp_id = int(input())
# Write your SQL query below (JOIN employees with departments)
query = "SELECT ..."
for row in conn.execute(query, [emp_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
A common step toward **2NF** is extracting repeated data into its own table. An un-normalized `order_items` table stores product details with every line item, causing redundancy.

The un-normalized table:

| order_id | product_id | product_name | product_price | quantity |
|---------|-----------|-------------|--------------|---------|
| 1       | 1         | Laptop      | 1200         | 2       |
| 2       | 1         | Laptop      | 1200         | 1       |
| 3       | 2         | Phone       | 600          | 3       |
| 4       | 3         | Tablet      | 800          | 1       |
| 5       | 2         | Phone       | 600          | 2       |

To normalize it, extract the **unique products**. Write a SQL query that returns each distinct `product_id`, `product_name`, and `product_price`, ordered by `product_id`.

Expected output:
```
1 Laptop 1200
2 Phone 600
3 Tablet 800
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE order_items (
    order_id INTEGER, product_id INTEGER, product_name TEXT,
    product_price INTEGER, quantity INTEGER
)''')
conn.executemany('INSERT INTO order_items VALUES (?,?,?,?,?)', [
    (1,1,'Laptop',1200,2),
    (2,1,'Laptop',1200,1),
    (3,2,'Phone',600,3),
    (4,3,'Tablet',800,1),
    (5,2,'Phone',600,2),
])
conn.commit()

# Write your SQL query below (SELECT DISTINCT product_id, product_name, product_price)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
In a **3NF-compliant schema**, every non-key attribute depends directly on the primary key of its table. Querying across such a schema always requires a JOIN.

Using the normalized `employees` and `departments` tables (same as Q39), write a SQL query that returns every employee's **name** and their department **location**, ordered alphabetically by name.

Expected output:
```
Alice New York
Bob Chicago
Carol New York
Dave Boston
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    emp_id INTEGER, name TEXT, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',1),(2,'Bob',2),(3,'Carol',1),(4,'Dave',3),
])
conn.execute('''CREATE TABLE departments (
    dept_id INTEGER, dept_name TEXT, location TEXT
)''')
conn.executemany('INSERT INTO departments VALUES (?,?,?)', [
    (1,'IT','New York'),(2,'HR','Chicago'),(3,'Sales','Boston'),
])
conn.commit()

# Write your SQL query below (JOIN employees with departments, ORDER BY name)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Indexing & Query Performance (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
An **index** on a column allows the database engine to find matching rows without scanning the entire table. Columns used frequently in `WHERE` clauses — especially unique identifiers like `email` — are prime candidates for indexing.

The `users` table has an index on `email`:

| id | name  | email                |
|----|-------|---------------------|
| 1  | Alice | alice@example.com   |
| 2  | Bob   | bob@example.com     |
| 3  | Carol | carol@example.com   |
| 4  | Dave  | dave@example.com    |

Read an email address from input and print the corresponding user **id**.

Example:
```
Input:  bob@example.com
Output: 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE users (id INTEGER, name TEXT, email TEXT)''')
conn.executemany('INSERT INTO users VALUES (?,?,?)', [
    (1,'Alice','alice@example.com'),
    (2,'Bob','bob@example.com'),
    (3,'Carol','carol@example.com'),
    (4,'Dave','dave@example.com'),
])
# In a real database you would run: CREATE INDEX idx_email ON users(email)
conn.commit()

email = input()
# Write your SQL query below (WHERE email = ?)
query = "SELECT ..."
for row in conn.execute(query, [email]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Indexes on **date or timestamp columns** speed up range queries. A query like `WHERE order_date > '2023-02-01'` benefits greatly from an index on `order_date`.

The `orders` table:

| id | customer_id | amount | order_date |
|----|------------|--------|-----------|
| 1  | 1          | 500    | 2023-01-15|
| 2  | 2          | 800    | 2023-02-20|
| 3  | 1          | 300    | 2023-03-10|
| 4  | 3          | 600    | 2023-04-05|
| 5  | 2          | 200    | 2023-02-01|

Read a **cutoff date** from input and print the **count of orders** placed **strictly after** that date.

Example:
```
Input:  2023-02-01
Output: 3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, amount INTEGER, order_date TEXT
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,500,'2023-01-15'),
    (2,2,800,'2023-02-20'),
    (3,1,300,'2023-03-10'),
    (4,3,600,'2023-04-05'),
    (5,2,200,'2023-02-01'),
])
conn.commit()

cutoff = input()
# Write your SQL query below (WHERE order_date > cutoff)
query = "SELECT ..."
for row in conn.execute(query, [cutoff]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
A **composite index** covers two or more columns together (e.g., `(category, price)`). Queries that filter on both columns can use the composite index efficiently.

The `products` table has a composite index on `(category, price)`:

| id | category    | name         | price |
|----|-------------|-------------|-------|
| 1  | Electronics | Laptop       | 1200  |
| 2  | Electronics | Phone        | 600   |
| 3  | Electronics | Tablet       | 800   |
| 4  | Furniture   | Chair        | 300   |
| 5  | Furniture   | Desk         | 600   |
| 6  | Books       | Python Book  | 45    |

Read a **category** from input and print the **names** of products in that category whose price is **less than 700**, ordered alphabetically.

Example:
```
Input:  Electronics
Output: Phone
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, category TEXT, name TEXT, price INTEGER
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Electronics','Laptop',1200),
    (2,'Electronics','Phone',600),
    (3,'Electronics','Tablet',800),
    (4,'Furniture','Chair',300),
    (5,'Furniture','Desk',600),
    (6,'Books','Python Book',45),
])
conn.commit()

category = input()
# Write your SQL query below (WHERE category = ? AND price < 700, ORDER BY name)
query = "SELECT ..."
for row in conn.execute(query, [category]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
A **covering index** contains all the columns a query needs, so the database can answer the query entirely from the index without touching the main table rows.

Using the `products` table, write a SQL query that returns all **distinct categories**, ordered alphabetically. In practice, an index on `category` would make this query a fast index-only scan.

Expected output:
```
Books
Electronics
Furniture
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, category TEXT, name TEXT, price INTEGER
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Electronics','Laptop',1200),
    (2,'Electronics','Phone',600),
    (3,'Electronics','Tablet',800),
    (4,'Furniture','Chair',300),
    (5,'Furniture','Desk',600),
    (6,'Books','Python Book',45),
])
conn.commit()

# Write your SQL query below (SELECT DISTINCT category, ORDER BY category)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
An index on a numeric column like `salary` accelerates **range scans** — queries that filter with `>`, `<`, `BETWEEN`, etc.

The `employees` table has an index on `salary`:

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 50000  |
| 2  | Bob   | 45000  |
| 3  | Carol | 75000  |
| 4  | Dave  | 65000  |
| 5  | Eve   | 55000  |
| 6  | Frank | 80000  |

Read a **salary threshold** from input and print the **names** of employees whose salary is **strictly above** that threshold, ordered alphabetically.

Example:
```
Input:  60000
Output:
Carol
Dave
Frank
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',50000),(2,'Bob',45000),(3,'Carol',75000),
    (4,'Dave',65000),(5,'Eve',55000),(6,'Frank',80000),
])
conn.commit()

threshold = int(input())
# Write your SQL query below (WHERE salary > threshold, ORDER BY name)
query = "SELECT ..."
for row in conn.execute(query, [threshold]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: NoSQL Databases & When to Use Them (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**NoSQL document databases** (like MongoDB) store records as JSON-like documents instead of fixed rows and columns. Each document can have its own structure.

Here, JSON documents are stored as text in a SQLite table (a common simulation technique). Use Python's `json` module to parse them.

The `documents` table:

| id | data                                              |
|----|---------------------------------------------------|
| 1  | `{"name":"Alice","age":30,"city":"New York"}`     |
| 2  | `{"name":"Bob","age":25,"city":"Chicago"}`        |
| 3  | `{"name":"Carol","age":35,"city":"New York"}`     |
| 4  | `{"name":"Dave","age":28,"city":"Boston"}`        |
| 5  | `{"name":"Eve","age":32,"city":"Chicago"}`        |

Write Python code that reads all documents, counts how many belong to each **city**, and prints `city count` pairs ordered alphabetically by city.

Expected output:
```
Boston 1
Chicago 2
New York 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE documents (id INTEGER, data TEXT)''')
conn.executemany('INSERT INTO documents VALUES (?,?)', [
    (1,'{"name":"Alice","age":30,"city":"New York"}'),
    (2,'{"name":"Bob","age":25,"city":"Chicago"}'),
    (3,'{"name":"Carol","age":35,"city":"New York"}'),
    (4,'{"name":"Dave","age":28,"city":"Boston"}'),
    (5,'{"name":"Eve","age":32,"city":"Chicago"}'),
])
conn.commit()

# Read documents, parse JSON, count by city, print sorted
city_counts = {}
for row in conn.execute("SELECT data FROM documents"):
    doc = json.loads(row[0])
    # Your logic here
    pass

for city in sorted(city_counts):
    print(city, city_counts[city])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
A **key-value store** (like Redis) maps unique string keys to values. It supports extremely fast lookups by key — similar to a dictionary in Python.

The `kv_store` table simulates a key-value store:

| key           | value  |
|--------------|--------|
| user:1        | Alice  |
| user:2        | Bob    |
| session:abc   | active |
| config:theme  | dark   |
| user:3        | Carol  |

Read a **key** from input and print the corresponding **value**.

Example:
```
Input:  user:1
Output: Alice
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE kv_store (key TEXT, value TEXT)''')
conn.executemany('INSERT INTO kv_store VALUES (?,?)', [
    ('user:1','Alice'),
    ('user:2','Bob'),
    ('session:abc','active'),
    ('config:theme','dark'),
    ('user:3','Carol'),
])
conn.commit()

key = input()
# Write your SQL query below (WHERE key = ?)
query = "SELECT ..."
for row in conn.execute(query, [key]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Document databases support **flexible querying** on any field within a document, even though documents are not stored in a rigid relational schema.

The `documents` table stores product records as JSON:

| id | data                                                      |
|----|----------------------------------------------------------|
| 1  | `{"type":"electronics","name":"Laptop","price":1200}`    |
| 2  | `{"type":"food","name":"Apple","price":50}`              |
| 3  | `{"type":"electronics","name":"Phone","price":600}`      |
| 4  | `{"type":"furniture","name":"Chair","price":300}`        |
| 5  | `{"type":"electronics","name":"Tablet","price":800}`     |
| 6  | `{"type":"food","name":"Banana","price":50}`             |

Read a **price threshold** from input and print the **name** of every product with a price **strictly above** that threshold, ordered alphabetically.

Example:
```
Input:  500
Output:
Laptop
Phone
Tablet
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE documents (id INTEGER, data TEXT)''')
conn.executemany('INSERT INTO documents VALUES (?,?)', [
    (1,'{"type":"electronics","name":"Laptop","price":1200}'),
    (2,'{"type":"food","name":"Apple","price":50}'),
    (3,'{"type":"electronics","name":"Phone","price":600}'),
    (4,'{"type":"furniture","name":"Chair","price":300}'),
    (5,'{"type":"electronics","name":"Tablet","price":800}'),
    (6,'{"type":"food","name":"Banana","price":50}'),
])
conn.commit()

threshold = int(input())
# Parse each document and filter by price, then print names sorted alphabetically
names = []
for row in conn.execute("SELECT data FROM documents"):
    doc = json.loads(row[0])
    # Your logic here
    pass

for name in sorted(names):
    print(name)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
NoSQL databases often lack built-in `COUNT` and `AVG` SQL functions, so applications compute these aggregations in application code (Python, JavaScript, etc.).

Using the same product `documents` table from Q49, write Python code that:
1. Counts the **total number of documents**
2. Computes the **average price** across all documents

Print the results in the following format:
```
count: 6
average_price: 500.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE documents (id INTEGER, data TEXT)''')
conn.executemany('INSERT INTO documents VALUES (?,?)', [
    (1,'{"type":"electronics","name":"Laptop","price":1200}'),
    (2,'{"type":"food","name":"Apple","price":50}'),
    (3,'{"type":"electronics","name":"Phone","price":600}'),
    (4,'{"type":"furniture","name":"Chair","price":300}'),
    (5,'{"type":"electronics","name":"Tablet","price":800}'),
    (6,'{"type":"food","name":"Banana","price":50}'),
])
conn.commit()

# Parse all documents, compute count and average price
prices = []
for row in conn.execute("SELECT data FROM documents"):
    doc = json.loads(row[0])
    # Your logic here
    pass

# Print count and average
# print(f"count: ...")
# print(f"average_price: ...")
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
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

        // ── Q1: COUNT(*) students ─────────────────────────────────────────
        $seed(1, [
            ['input' => null, 'expected_output' => '6', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '6', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '6', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '6', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: DISTINCT majors ──────────────────────────────────────────
        $seed(2, [
            ['input' => null, 'expected_output' => "Computer Science\nMathematics\nPhysics", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Computer Science\nMathematics\nPhysics", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Computer Science\nMathematics\nPhysics", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Computer Science\nMathematics\nPhysics", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: PK lookup by student_id ──────────────────────────────────
        $seed(3, [
            ['input' => '1', 'expected_output' => 'Alice', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '3', 'expected_output' => 'Carol', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '5', 'expected_output' => 'Eve',   'is_hidden' => true,  'order_index' => 3],
            ['input' => '2', 'expected_output' => 'Bob',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: COUNT NULL salaries ──────────────────────────────────────
        $seed(4, [
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: COUNT orphan orders ──────────────────────────────────────
        $seed(5, [
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: WHERE category = Electronics ORDER BY name ───────────────
        $seed(6, [
            ['input' => null, 'expected_output' => "Laptop\nPhone\nTablet\nUSB Cable", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop\nPhone\nTablet\nUSB Cable", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop\nPhone\nTablet\nUSB Cable", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop\nPhone\nTablet\nUSB Cable", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: ORDER BY age ASC, name ASC ───────────────────────────────
        $seed(7, [
            ['input' => null, 'expected_output' => "Alice 20\nEve 20\nCarol 21\nBob 22\nDave 23\nFrank 24", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 20\nEve 20\nCarol 21\nBob 22\nDave 23\nFrank 24", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 20\nEve 20\nCarol 21\nBob 22\nDave 23\nFrank 24", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 20\nEve 20\nCarol 21\nBob 22\nDave 23\nFrank 24", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Top 3 most expensive products ───────────────────────────
        $seed(8, [
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: WHERE price BETWEEN 500 AND 900 ──────────────────────────
        $seed(9, [
            ['input' => null, 'expected_output' => "Phone 600\nTablet 800", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Phone 600\nTablet 800", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Phone 600\nTablet 800", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Phone 600\nTablet 800", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: WHERE age > 21 ORDER BY name ───────────────────────────
        $seed(10, [
            ['input' => null, 'expected_output' => "Bob\nDave\nFrank", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Bob\nDave\nFrank", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Bob\nDave\nFrank", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Bob\nDave\nFrank", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: WHERE category=Electronics AND price>500 ────────────────
        $seed(11, [
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1200\nTablet 800\nPhone 600", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: COUNT(*) and SUM(amount) ────────────────────────────────
        $seed(12, [
            ['input' => null, 'expected_output' => '7 3605', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '7 3605', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '7 3605', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '7 3605', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: SUM per category ────────────────────────────────────────
        $seed(13, [
            ['input' => null, 'expected_output' => "Books 25\nElectronics 2000\nFood 80\nFurniture 1500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Books 25\nElectronics 2000\nFood 80\nFurniture 1500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Books 25\nElectronics 2000\nFood 80\nFurniture 1500", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Books 25\nElectronics 2000\nFood 80\nFurniture 1500", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: AVG per category ────────────────────────────────────────
        $seed(14, [
            ['input' => null, 'expected_output' => "Books 25.0\nElectronics 1000.0\nFood 40.0\nFurniture 750.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Books 25.0\nElectronics 1000.0\nFood 40.0\nFurniture 750.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Books 25.0\nElectronics 1000.0\nFood 40.0\nFurniture 750.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Books 25.0\nElectronics 1000.0\nFood 40.0\nFurniture 750.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: MAX and MIN amount ──────────────────────────────────────
        $seed(15, [
            ['input' => null, 'expected_output' => '1200 25', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1200 25', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1200 25', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1200 25', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: HAVING COUNT(*) > 1 ────────────────────────────────────
        $seed(16, [
            ['input' => null, 'expected_output' => "Electronics 2\nFood 2\nFurniture 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Electronics 2\nFood 2\nFurniture 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Electronics 2\nFood 2\nFurniture 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Electronics 2\nFood 2\nFurniture 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: COUNT DISTINCT customers ───────────────────────────────
        $seed(17, [
            ['input' => null, 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '4', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '4', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: INNER JOIN employees + departments ──────────────────────
        $seed(18, [
            ['input' => null, 'expected_output' => "Alice IT\nBob HR\nCarol IT\nDave Sales\nEve HR", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice IT\nBob HR\nCarol IT\nDave Sales\nEve HR", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice IT\nBob HR\nCarol IT\nDave Sales\nEve HR", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice IT\nBob HR\nCarol IT\nDave Sales\nEve HR", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: LEFT JOIN all departments with employee count ───────────
        $seed(19, [
            ['input' => null, 'expected_output' => "HR 2\nIT 2\nMarketing 0\nSales 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 2\nIT 2\nMarketing 0\nSales 1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 2\nIT 2\nMarketing 0\nSales 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 2\nIT 2\nMarketing 0\nSales 1", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Departments with no employees ──────────────────────────
        $seed(20, [
            ['input' => null, 'expected_output' => 'Marketing', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'Marketing', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'Marketing', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'Marketing', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: SUM salary per department ──────────────────────────────
        $seed(21, [
            ['input' => null, 'expected_output' => "HR 100000\nIT 120000\nSales 65000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 100000\nIT 120000\nSales 65000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 100000\nIT 120000\nSales 65000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 100000\nIT 120000\nSales 65000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Three-table JOIN by order_id ───────────────────────────
        $seed(22, [
            ['input' => '1', 'expected_output' => 'Alice Laptop 1200', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => 'Carol Phone 800',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => 'Alice Tablet 600',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '4', 'expected_output' => 'Bob Laptop 1200',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Self-join — same-department pairs ───────────────────────
        $seed(23, [
            ['input' => null, 'expected_output' => "Alice Carol\nBob Eve", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice Carol\nBob Eve", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice Carol\nBob Eve", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice Carol\nBob Eve", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: FULL JOIN simulation ────────────────────────────────────
        $seed(24, [
            ['input' => null, 'expected_output' => "Alice 500\nBob None\nCarol 300\nNone 200", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 500\nBob None\nCarol 300\nNone 200", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 500\nBob None\nCarol 300\nNone 200", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 500\nBob None\nCarol 300\nNone 200", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Subquery WHERE above average salary ─────────────────────
        $seed(25, [
            ['input' => null, 'expected_output' => "Alice\nCarol\nFrank", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\nCarol\nFrank", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\nCarol\nFrank", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\nCarol\nFrank", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Subquery in FROM — dept totals ──────────────────────────
        $seed(26, [
            ['input' => null, 'expected_output' => "HR 105000\nIT 245000\nSales 55000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 105000\nIT 245000\nSales 55000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 105000\nIT 245000\nSales 55000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 105000\nIT 245000\nSales 55000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Correlated subquery — highest earner per dept ───────────
        $seed(27, [
            ['input' => null, 'expected_output' => "Dave Sales\nEve HR\nFrank IT", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Dave Sales\nEve HR\nFrank IT", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Dave Sales\nEve HR\nFrank IT", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Dave Sales\nEve HR\nFrank IT", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: CTE high_salary ─────────────────────────────────────────
        $seed(28, [
            ['input' => null, 'expected_output' => "Carol\nFrank", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Carol\nFrank", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Carol\nFrank", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Carol\nFrank", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: CTE dept_avg ────────────────────────────────────────────
        $seed(29, [
            ['input' => null, 'expected_output' => "HR 50000.0\nIT 80000.0\nSales 55000.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 50000.0\nIT 80000.0\nSales 55000.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 50000.0\nIT 80000.0\nSales 55000.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 50000.0\nIT 80000.0\nSales 55000.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: IN subquery — customers with orders > 500 ───────────────
        $seed(30, [
            ['input' => null, 'expected_output' => "Alice\nCarol\nDave", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\nCarol\nDave", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\nCarol\nDave", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\nCarol\nDave", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: ROW_NUMBER by amount DESC ──────────────────────────────
        $seed(31, [
            ['input' => null, 'expected_output' => "6 2000 1\n3 1500 2\n1 1200 3\n5 900 4\n2 800 5\n4 600 6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "6 2000 1\n3 1500 2\n1 1200 3\n5 900 4\n2 800 5\n4 600 6", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "6 2000 1\n3 1500 2\n1 1200 3\n5 900 4\n2 800 5\n4 600 6", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "6 2000 1\n3 1500 2\n1 1200 3\n5 900 4\n2 800 5\n4 600 6", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: RANK PARTITION BY month ────────────────────────────────
        $seed(32, [
            ['input' => null, 'expected_output' => "1 2023-01 1200 1\n2 2023-01 800 2\n3 2023-02 1500 1\n4 2023-02 600 2\n6 2023-03 2000 1\n5 2023-03 900 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 2023-01 1200 1\n2 2023-01 800 2\n3 2023-02 1500 1\n4 2023-02 600 2\n6 2023-03 2000 1\n5 2023-03 900 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 2023-01 1200 1\n2 2023-01 800 2\n3 2023-02 1500 1\n4 2023-02 600 2\n6 2023-03 2000 1\n5 2023-03 900 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 2023-01 1200 1\n2 2023-01 800 2\n3 2023-02 1500 1\n4 2023-02 600 2\n6 2023-03 2000 1\n5 2023-03 900 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: Running SUM ────────────────────────────────────────────
        $seed(33, [
            ['input' => null, 'expected_output' => "1 1200 1200\n2 800 2000\n3 1500 3500\n4 600 4100\n5 900 5000\n6 2000 7000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 1200\n2 800 2000\n3 1500 3500\n4 600 4100\n5 900 5000\n6 2000 7000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 1200\n2 800 2000\n3 1500 3500\n4 600 4100\n5 900 5000\n6 2000 7000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 1200\n2 800 2000\n3 1500 3500\n4 600 4100\n5 900 5000\n6 2000 7000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: LAG previous amount ────────────────────────────────────
        $seed(34, [
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: LEAD next amount ───────────────────────────────────────
        $seed(35, [
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: AVG OVER PARTITION BY month ────────────────────────────
        $seed(36, [
            ['input' => null, 'expected_output' => "1 1200 2023-01 1000.0\n2 800 2023-01 1000.0\n3 1500 2023-02 1050.0\n4 600 2023-02 1050.0\n5 900 2023-03 1450.0\n6 2000 2023-03 1450.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1000.0\n2 800 2023-01 1000.0\n3 1500 2023-02 1050.0\n4 600 2023-02 1050.0\n5 900 2023-03 1450.0\n6 2000 2023-03 1450.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1000.0\n2 800 2023-01 1000.0\n3 1500 2023-02 1050.0\n4 600 2023-02 1050.0\n5 900 2023-03 1450.0\n6 2000 2023-03 1450.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1000.0\n2 800 2023-01 1000.0\n3 1500 2023-02 1050.0\n4 600 2023-02 1050.0\n5 900 2023-03 1450.0\n6 2000 2023-03 1450.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: 1NF violation count ─────────────────────────────────────
        $seed(37, [
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: 2NF — courses for a student ────────────────────────────
        $seed(38, [
            ['input' => '1', 'expected_output' => "CS101\nMath",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "Math\nPhysics",  'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => "CS101\nEnglish", 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "CS101\nMath",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: 3NF — dept location by emp_id ──────────────────────────
        $seed(39, [
            ['input' => '2', 'expected_output' => 'Chicago',  'is_hidden' => false, 'order_index' => 1],
            ['input' => '1', 'expected_output' => 'New York', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => 'New York', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '4', 'expected_output' => 'Boston',   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: DISTINCT products from unnormalized table ───────────────
        $seed(40, [
            ['input' => null, 'expected_output' => "1 Laptop 1200\n2 Phone 600\n3 Tablet 800", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 Laptop 1200\n2 Phone 600\n3 Tablet 800", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 Laptop 1200\n2 Phone 600\n3 Tablet 800", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 Laptop 1200\n2 Phone 600\n3 Tablet 800", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: 3NF JOIN — name + location ─────────────────────────────
        $seed(41, [
            ['input' => null, 'expected_output' => "Alice New York\nBob Chicago\nCarol New York\nDave Boston", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice New York\nBob Chicago\nCarol New York\nDave Boston", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice New York\nBob Chicago\nCarol New York\nDave Boston", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice New York\nBob Chicago\nCarol New York\nDave Boston", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Email index lookup ──────────────────────────────────────
        $seed(42, [
            ['input' => 'bob@example.com',   'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'carol@example.com', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'alice@example.com', 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'dave@example.com',  'expected_output' => '4', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Count orders after cutoff date ─────────────────────────
        $seed(43, [
            ['input' => '2023-02-01', 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2023-01-01', 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2023-03-01', 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '2023-04-01', 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Composite index — products by category and price < 700 ──
        $seed(44, [
            ['input' => 'Electronics', 'expected_output' => 'Phone',            'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Furniture',   'expected_output' => "Chair\nDesk",      'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Books',       'expected_output' => 'Python Book',      'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Furniture',   'expected_output' => "Chair\nDesk",      'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Covering index — DISTINCT categories ────────────────────
        $seed(45, [
            ['input' => null, 'expected_output' => "Books\nElectronics\nFurniture", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Books\nElectronics\nFurniture", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Books\nElectronics\nFurniture", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Books\nElectronics\nFurniture", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Salary range scan above threshold ───────────────────────
        $seed(46, [
            ['input' => '60000', 'expected_output' => "Carol\nDave\nFrank",  'is_hidden' => false, 'order_index' => 1],
            ['input' => '70000', 'expected_output' => "Carol\nFrank",        'is_hidden' => false, 'order_index' => 2],
            ['input' => '50000', 'expected_output' => "Carol\nDave\nEve\nFrank", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '75000', 'expected_output' => 'Frank',               'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: NoSQL — count documents by city ─────────────────────────
        $seed(47, [
            ['input' => null, 'expected_output' => "Boston 1\nChicago 2\nNew York 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Boston 1\nChicago 2\nNew York 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Boston 1\nChicago 2\nNew York 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Boston 1\nChicago 2\nNew York 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Key-value store lookup ──────────────────────────────────
        $seed(48, [
            ['input' => 'user:1',       'expected_output' => 'Alice',  'is_hidden' => false, 'order_index' => 1],
            ['input' => 'config:theme', 'expected_output' => 'dark',   'is_hidden' => false, 'order_index' => 2],
            ['input' => 'user:3',       'expected_output' => 'Carol',  'is_hidden' => true,  'order_index' => 3],
            ['input' => 'session:abc',  'expected_output' => 'active', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Document filter by price threshold ──────────────────────
        $seed(49, [
            ['input' => '500', 'expected_output' => "Laptop\nPhone\nTablet",          'is_hidden' => false, 'order_index' => 1],
            ['input' => '100', 'expected_output' => "Chair\nLaptop\nPhone\nTablet",   'is_hidden' => false, 'order_index' => 2],
            ['input' => '1000','expected_output' => 'Laptop',                         'is_hidden' => true,  'order_index' => 3],
            ['input' => '250', 'expected_output' => "Chair\nLaptop\nPhone\nTablet",   'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Document count + average price ──────────────────────────
        $seed(50, [
            ['input' => null, 'expected_output' => "count: 6\naverage_price: 500.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "count: 6\naverage_price: 500.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "count: 6\naverage_price: 500.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "count: 6\naverage_price: 500.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 10 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}