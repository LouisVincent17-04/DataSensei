<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 10 — Relational Databases & SQL (University Student) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the University Student tier
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
 * Tier 2 — problems require multi-step reasoning, compound conditions,
 * multi-table joins, correlated subqueries, recursive CTEs, advanced
 * window functions, normalization analysis, and JSON document processing.
 *
 * All tasks run in Python using the built-in sqlite3 module.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module10CodingChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'university_student')->first();

        if (! $category) {
            $this->command->error('University Student category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 10 — Relational Databases & SQL (University Student) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Relational Databases & SQL',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Tackle multi-step relational database challenges using Python\'s sqlite3 module. Problems span compound WHERE clauses, multi-table JOIN chains, conditional aggregations, correlated subqueries, chained CTEs, recursive CTEs, advanced window functions (DENSE_RANK, NTILE, FIRST_VALUE, moving averages), normalization analysis, index strategy scenarios, and JSON document aggregation pipelines. Each task sets up a complete in-memory schema — your job is to write the SQL or Python logic that produces the exact required output.',
                'time_limit_seconds' => 1200,
                'base_xp'            => 750,
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
An `accounts` table stores banking customers, and a `transactions` table records every credit and debit. A negative **balance** (total credits minus total debits) signals an overdrawn account.

**accounts**
| id | holder  |
|----|---------|
| 1  | Alice   |
| 2  | Bob     |
| 3  | Carol   |
| 4  | Dave    |

**transactions**
| id | account_id | type   | amount |
|----|-----------|--------|--------|
| 1  | 1         | credit | 500    |
| 2  | 1         | debit  | 300    |
| 3  | 2         | credit | 200    |
| 4  | 2         | debit  | 400    |
| 5  | 3         | credit | 600    |
| 6  | 3         | debit  | 100    |
| 7  | 4         | credit | 150    |
| 8  | 4         | debit  | 200    |

Write a SQL query that returns the **id** of every account whose balance (SUM of credits − SUM of debits) is **negative**, ordered ascending.

Expected output:
```
2
4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE accounts (id INTEGER, holder TEXT)')
conn.executemany('INSERT INTO accounts VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('''CREATE TABLE transactions (
    id INTEGER, account_id INTEGER, type TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO transactions VALUES (?,?,?,?)', [
    (1,1,'credit',500),(2,1,'debit',300),
    (3,2,'credit',200),(4,2,'debit',400),
    (5,3,'credit',600),(6,3,'debit',100),
    (7,4,'credit',150),(8,4,'debit',200),
])
conn.commit()

# Hint: SUM(CASE WHEN type='credit' THEN amount ELSE -amount END) < 0
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
In a project-tracking system, an `employees` table and an `emp_projects` junction table link employees to their projects. Employees assigned to **more than one project** may create scheduling conflicts.

**employees**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |
| 4  | Dave  |
| 5  | Eve   |

**emp_projects**
| emp_id | project_id |
|--------|-----------|
| 1      | 1         |
| 1      | 2         |
| 2      | 1         |
| 3      | 2         |
| 3      | 3         |
| 4      | 1         |
| 5      | 3         |
| 5      | 1         |
| 5      | 2         |

Write a SQL query that returns the **names** of employees assigned to **more than one project**, ordered alphabetically.

Expected output:
```
Alice
Carol
Eve
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),(5,'Eve'),
])
conn.execute('CREATE TABLE emp_projects (emp_id INTEGER, project_id INTEGER)')
conn.executemany('INSERT INTO emp_projects VALUES (?,?)', [
    (1,1),(1,2),(2,1),(3,2),(3,3),(4,1),(5,3),(5,1),(5,2),
])
conn.commit()

# Hint: JOIN employees → emp_projects, GROUP BY, HAVING COUNT > 1
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
A **composite primary key** uses two or more columns together to uniquely identify a row. When the uniqueness constraint is not enforced, duplicate composite-key entries can appear, signalling a data integrity problem.

The `orders` table below was loaded without enforcing a `(customer_id, product_id)` uniqueness constraint, resulting in duplicate entries:

| id | customer_id | product_id | amount |
|----|------------|-----------|--------|
| 1  | 1          | 101       | 500    |
| 2  | 1          | 101       | 500    |
| 3  | 2          | 102       | 300    |
| 4  | 2          | 103       | 200    |
| 5  | 3          | 101       | 600    |
| 6  | 1          | 102       | 400    |
| 7  | 2          | 102       | 300    |

Write a SQL query that finds all `(customer_id, product_id)` pairs that appear **more than once**, ordered by `customer_id` then `product_id`.

Expected output:
```
1 101
2 102
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, product_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,101,500),(2,1,101,500),(3,2,102,300),
    (4,2,103,200),(5,3,101,600),(6,1,102,400),(7,2,102,300),
])
conn.commit()

# Hint: GROUP BY customer_id, product_id HAVING COUNT(*) > 1
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
**Referential integrity** requires that every foreign key value has a matching primary key in its referenced table. When orders reference both a `customers` and a `products` table, a row is invalid if **either** foreign key is broken.

**orders** (references both customers.id and products.id)
| id | customer_id | product_id |
|----|------------|-----------|
| 1  | 1          | 101       |
| 2  | 2          | 999       |
| 3  | 99         | 101       |
| 4  | 3          | 102       |

**customers**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |

**products**
| id  | name   |
|-----|--------|
| 101 | Laptop |
| 102 | Phone  |

Write a SQL query that returns the **id** of every order where the `customer_id` is missing from `customers` **OR** the `product_id` is missing from `products`, ordered ascending.

Expected output:
```
2
3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER, product_id INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,101),(2,2,999),(3,99,101),(4,3,102),
])
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),
])
conn.execute('CREATE TABLE products (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO products VALUES (?,?)', [
    (101,'Laptop'),(102,'Phone'),
])
conn.commit()

# Hint: LEFT JOIN both dimension tables; filter rows where either join produced NULL
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
**Cascading deletes** mean that removing a parent record removes all child records that depend on it. Before running such a delete, it is useful to know how many downstream orders will be affected.

**categories**
| id | name        |
|----|-------------|
| 1  | Electronics |
| 2  | Furniture   |

**products** (each belongs to a category)
| id | name   | category_id |
|----|--------|------------|
| 1  | Laptop | 1          |
| 2  | Phone  | 1          |
| 3  | Desk   | 2          |
| 4  | Chair  | 2          |
| 5  | Tablet | 1          |

**orders** (each references a product)
| id | product_id | amount |
|----|-----------|--------|
| 1  | 1         | 500    |
| 2  | 2         | 300    |
| 3  | 3         | 400    |
| 4  | 1         | 600    |
| 5  | 5         | 800    |

Read a **category id** from input and print the **number of orders** that would be deleted (orders whose product belongs to that category).

Example:
```
Input:  1
Output: 4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE categories (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO categories VALUES (?,?)', [
    (1,'Electronics'),(2,'Furniture'),
])
conn.execute('CREATE TABLE products (id INTEGER, name TEXT, category_id INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1,'Laptop',1),(2,'Phone',1),(3,'Desk',2),(4,'Chair',2),(5,'Tablet',1),
])
conn.execute('CREATE TABLE orders (id INTEGER, product_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,500),(2,2,300),(3,3,400),(4,1,600),(5,5,800),
])
conn.commit()

category_id = int(input())
# Hint: JOIN orders → products, filter by category_id, COUNT
query = "SELECT ..."
for row in conn.execute(query, [category_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: SQL Basics — SELECT, WHERE, ORDER BY, LIMIT (Q6–Q11)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
A `CASE` expression lets you define conditional logic directly inside a `SELECT`. It is commonly used to create **salary bands** or other derived labels.

The `employees` table:

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 48000  |
| 2  | Bob   | 65000  |
| 3  | Carol | 82000  |
| 4  | Dave  | 55000  |
| 5  | Eve   | 71000  |
| 6  | Frank | 40000  |

Use the following bands:
- salary < 50000 → `'Low'`
- 50000 ≤ salary ≤ 70000 → `'Medium'`
- salary > 70000 → `'High'`

Write a SQL query that returns each employee's **name** and their **band**, ordered alphabetically by name.

Expected output:
```
Alice Low
Bob Medium
Carol High
Dave Medium
Eve High
Frank Low
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',48000),(2,'Bob',65000),(3,'Carol',82000),
    (4,'Dave',55000),(5,'Eve',71000),(6,'Frank',40000),
])
conn.commit()

# Hint: CASE WHEN salary < 50000 THEN 'Low' WHEN salary <= 70000 THEN 'Medium' ELSE 'High' END
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
`LIKE` with a trailing `%` wildcard matches any string that **starts with** a given prefix — and this prefix-search pattern can take advantage of a B-tree index.

The `products` table:

| id | name         | price |
|----|-------------|-------|
| 1  | Laptop       | 1200  |
| 2  | ProBook      | 800   |
| 3  | ProDisplay   | 1500  |
| 4  | Phone        | 600   |
| 5  | Monitor Pro  | 900   |
| 6  | Keyboard     | 150   |

Read a **prefix** from input and print the **names** of all products whose name starts with that prefix, ordered alphabetically.

Example:
```
Input:  Pro
Output:
ProBook
ProDisplay
```

Note: The match is **case-sensitive** in SQLite.
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, name TEXT, price INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1,'Laptop',1200),(2,'ProBook',800),(3,'ProDisplay',1500),
    (4,'Phone',600),(5,'Monitor Pro',900),(6,'Keyboard',150),
])
conn.commit()

prefix = input()
# Hint: WHERE name LIKE ? using prefix + '%'
query = "SELECT ..."
for row in conn.execute(query, [prefix + '%']):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
The `IN` clause filters rows whose column value matches **any value in a list**. When the list is produced by a subquery, it becomes a powerful way to combine information from two tables without an explicit JOIN.

**customers**
| id | name  |
|----|-------|
| 1  | Alice |
| 2  | Bob   |
| 3  | Carol |
| 4  | Dave  |

**orders**
| id | customer_id | category    | amount |
|----|------------|-------------|--------|
| 1  | 1          | Electronics | 500    |
| 2  | 2          | Food        | 50     |
| 3  | 3          | Electronics | 800    |
| 4  | 4          | Furniture   | 300    |
| 5  | 1          | Food        | 30     |
| 6  | 2          | Electronics | 600    |

Read a **category** from input and print the **names** of customers who placed at least one order in that category, with no duplicates, ordered alphabetically.

Example:
```
Input:  Electronics
Output:
Alice
Bob
Carol
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,'Electronics',500),(2,2,'Food',50),(3,3,'Electronics',800),
    (4,4,'Furniture',300),(5,1,'Food',30),(6,2,'Electronics',600),
])
conn.commit()

category = input()
# Hint: WHERE id IN (SELECT DISTINCT customer_id FROM orders WHERE category = ?)
query = "SELECT ..."
for row in conn.execute(query, [category]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
**Pagination** is the technique of returning a fixed-size "page" of results using `LIMIT` and `OFFSET`. Given a page number (1-based) and a page size of **3**, compute `OFFSET = (page - 1) * 3`.

The `products` table (sorted by price descending):

| id | name        | price |
|----|------------|-------|
| 1  | Laptop      | 1200  |
| 2  | Tablet      | 800   |
| 3  | Phone       | 600   |
| 4  | Monitor     | 950   |
| 5  | Keyboard    | 150   |
| 6  | Mouse       | 80    |
| 7  | Headphones  | 250   |
| 8  | Webcam      | 120   |
| 9  | USB Hub     | 45    |

Read a **page number** from input and print the **names** of the products on that page (3 per page, ordered by price descending).

Example:
```
Input:  2
Output:
Phone
Headphones
Keyboard
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, name TEXT, price INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1,'Laptop',1200),(2,'Tablet',800),(3,'Phone',600),(4,'Monitor',950),
    (5,'Keyboard',150),(6,'Mouse',80),(7,'Headphones',250),
    (8,'Webcam',120),(9,'USB Hub',45),
])
conn.commit()

page = int(input())
page_size = 3
offset = (page - 1) * page_size
# Write your SQL query below (ORDER BY price DESC, LIMIT 3, OFFSET ?)
query = "SELECT ..."
for row in conn.execute(query, [offset]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
`COALESCE(col, default)` replaces `NULL` with a fallback value. This allows NULL-safe comparisons — for example, treating a missing salary as 0 before applying a threshold filter.

The `employees` table has some NULL salaries:

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 70000  |
| 2  | Bob   | NULL   |
| 3  | Carol | 85000  |
| 4  | Dave  | NULL   |
| 5  | Eve   | 55000  |

Read a **threshold** from input and print the **names** of employees whose `COALESCE(salary, 0)` is **strictly greater than** the threshold, ordered alphabetically.

Example:
```
Input:  -1
Output:
Alice
Bob
Carol
Dave
Eve
```
(Because COALESCE(NULL, 0) = 0, which is > -1)
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',70000),(2,'Bob',None),(3,'Carol',85000),
    (4,'Dave',None),(5,'Eve',55000),
])
conn.commit()

threshold = int(input())
# Hint: WHERE COALESCE(salary, 0) > ? ORDER BY name
query = "SELECT ..."
for row in conn.execute(query, [threshold]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
SQL's string functions let you manipulate and extract parts of text values. SQLite provides `SUBSTR` and `INSTR` to extract a **domain** from an email address: `SUBSTR(email, INSTR(email, '@') + 1)`.

The `employees` table:

| id | name  | email               |
|----|-------|---------------------|
| 1  | Alice | alice@gmail.com     |
| 2  | Bob   | bob@company.org     |
| 3  | Carol | carol@gmail.com     |
| 4  | Dave  | dave@uni.edu        |
| 5  | Eve   | eve@company.org     |

Write a SQL query that counts how many employees belong to each **email domain**, ordered by count **descending**, then by domain **ascending** for ties.

Expected output:
```
company.org 2
gmail.com 2
uni.edu 1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, email TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice','alice@gmail.com'),
    (2,'Bob','bob@company.org'),
    (3,'Carol','carol@gmail.com'),
    (4,'Dave','dave@uni.edu'),
    (5,'Eve','eve@company.org'),
])
conn.commit()

# Hint: SUBSTR(email, INSTR(email,'@')+1) AS domain, GROUP BY domain
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Aggregate Functions & GROUP BY (Q12–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Aggregations can be **stacked**: first group by customer to get per-customer totals, then find the maximum total across all customers.

The `orders` table:

| id | customer_id | category    | amount |
|----|------------|-------------|--------|
| 1  | 1          | Electronics | 1200   |
| 2  | 1          | Food        | 50     |
| 3  | 2          | Electronics | 800    |
| 4  | 2          | Electronics | 600    |
| 5  | 3          | Furniture   | 400    |
| 6  | 3          | Food        | 30     |
| 7  | 1          | Electronics | 900    |

Write a SQL query that returns the **customer_id** and **total amount** of the customer who spent the **most overall**, as a single row.

Expected output:
```
1 2150
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,'Electronics',1200),(2,1,'Food',50),(3,2,'Electronics',800),
    (4,2,'Electronics',600),(5,3,'Furniture',400),(6,3,'Food',30),
    (7,1,'Electronics',900),
])
conn.commit()

# Hint: GROUP BY customer_id, ORDER BY SUM(amount) DESC, LIMIT 1
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
**Conditional aggregation** uses `CASE` inside aggregate functions to compute multiple metrics in a single `GROUP BY` pass. For example, `SUM(CASE WHEN is_returned=0 THEN amount ELSE 0 END)` computes net (non-returned) sales.

The `sales` table:

| id | product | month | amount | is_returned |
|----|---------|-------|--------|------------|
| 1  | Laptop  | Jan   | 1200   | 0          |
| 2  | Phone   | Jan   | 800    | 1          |
| 3  | Laptop  | Feb   | 1500   | 0          |
| 4  | Phone   | Feb   | 600    | 0          |
| 5  | Laptop  | Jan   | 900    | 1          |
| 6  | Tablet  | Feb   | 700    | 0          |

Write a SQL query that returns each **product**, its **net sales** (sum of non-returned amounts), and its **return count**, ordered alphabetically by product.

Expected output:
```
Laptop 2700 1
Phone 600 1
Tablet 700 0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (
    id INTEGER, product TEXT, month TEXT, amount INTEGER, is_returned INTEGER
)''')
conn.executemany('INSERT INTO sales VALUES (?,?,?,?,?)', [
    (1,'Laptop','Jan',1200,0),(2,'Phone','Jan',800,1),
    (3,'Laptop','Feb',1500,0),(4,'Phone','Feb',600,0),
    (5,'Laptop','Jan',900,1),(6,'Tablet','Feb',700,0),
])
conn.commit()

# Hint: SUM(CASE WHEN is_returned=0 THEN amount ELSE 0 END) and SUM(is_returned)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
`HAVING` filters groups after aggregation. A useful pattern is `HAVING MAX(col) - MIN(col) > threshold`, which finds groups with a **wide spread** of values.

The `employees` table:

| id | name  | salary | dept  |
|----|-------|--------|-------|
| 1  | Alice | 50000  | IT    |
| 2  | Bob   | 75000  | IT    |
| 3  | Carol | 60000  | HR    |
| 4  | Dave  | 65000  | HR    |
| 5  | Eve   | 90000  | IT    |
| 6  | Frank | 40000  | Sales |
| 7  | Grace | 42000  | Sales |

Write a SQL query that returns the **department names** where the difference between the maximum and minimum salary is **greater than 20000**, ordered alphabetically.

Expected output:
```
IT
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,'IT'),(2,'Bob',75000,'IT'),(3,'Carol',60000,'HR'),
    (4,'Dave',65000,'HR'),(5,'Eve',90000,'IT'),(6,'Frank',40000,'Sales'),
    (7,'Grace',42000,'Sales'),
])
conn.commit()

# Hint: GROUP BY dept HAVING MAX(salary) - MIN(salary) > 20000
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Finding the product with the **highest average monthly sales** requires first computing per-product averages, then selecting the top result.

The `sales_monthly` table:

| product | month | total |
|---------|-------|-------|
| Laptop  | Jan   | 2700  |
| Laptop  | Feb   | 1500  |
| Phone   | Jan   | 800   |
| Phone   | Feb   | 600   |
| Tablet  | Feb   | 700   |

Write a SQL query that returns the **product name** and its **average monthly total**, for the product with the **highest average**. Print as `product avg`.

Expected output:
```
Laptop 2100.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales_monthly (product TEXT, month TEXT, total INTEGER)')
conn.executemany('INSERT INTO sales_monthly VALUES (?,?,?)', [
    ('Laptop','Jan',2700),('Laptop','Feb',1500),
    ('Phone','Jan',800),('Phone','Feb',600),
    ('Tablet','Feb',700),
])
conn.commit()

# Hint: GROUP BY product, ORDER BY AVG(total) DESC, LIMIT 1
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Category-level **percentage share** of total revenue is a key business metric. You can compute it with a subquery for the grand total:

```sql
ROUND(SUM(amount) * 100.0 / (SELECT SUM(amount) FROM orders), 1)
```

The `orders` table:

| id | category    | amount |
|----|-------------|--------|
| 1  | Electronics | 1000   |
| 2  | Electronics | 2000   |
| 3  | Furniture   | 1500   |
| 4  | Food        | 500    |

Write a SQL query that returns each **category** and its **percentage of total revenue** (rounded to 1 decimal place), ordered alphabetically by category.

Expected output:
```
Electronics 60.0
Food 10.0
Furniture 30.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, category TEXT, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,'Electronics',1000),(2,'Electronics',2000),
    (3,'Furniture',1500),(4,'Food',500),
])
conn.commit()

# Hint: ROUND(SUM(amount)*100.0/(SELECT SUM(amount) FROM orders), 1)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
`GROUP BY` accepts **multiple columns**, producing one row for each unique combination. This is useful for cross-tab style reporting.

Using the same `orders` table from Q12:

| id | customer_id | category    | amount |
|----|------------|-------------|--------|
| 1  | 1          | Electronics | 1200   |
| 2  | 1          | Food        | 50     |
| 3  | 2          | Electronics | 800    |
| 4  | 2          | Electronics | 600    |
| 5  | 3          | Furniture   | 400    |
| 6  | 3          | Food        | 30     |
| 7  | 1          | Electronics | 900    |

Write a SQL query that returns the **total amount per customer per category**, ordered by `customer_id` ascending, then `category` ascending.

Expected output:
```
1 Electronics 2100
1 Food 50
2 Electronics 1400
3 Food 30
3 Furniture 400
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, category TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,'Electronics',1200),(2,1,'Food',50),(3,2,'Electronics',800),
    (4,2,'Electronics',600),(5,3,'Furniture',400),(6,3,'Food',30),
    (7,1,'Electronics',900),
])
conn.commit()

# Hint: GROUP BY customer_id, category ORDER BY customer_id, category
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: JOINs — INNER, LEFT, RIGHT, FULL (Q18–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
A **four-table JOIN chain** connects students → enrollments → courses → professors, letting you answer questions that span the entire schema.

**students**: (id, name)  
**courses**: (id, name, prof_id)  
**professors**: (id, name)  
**enrollments**: (student_id, course_id)

Data:
- Students: Alice(1), Bob(2), Carol(3)
- Courses: CS101(1, prof 1), Math(2, prof 2), Physics(3, prof 2)
- Professors: Dr. Smith(1), Dr. Jones(2)
- Enrollments: Alice→CS101,Math; Bob→Math,Physics; Carol→CS101

Read a **student name** from input and print the **distinct professor names** who teach that student's courses, ordered alphabetically.

Example:
```
Input:  Alice
Output:
Dr. Jones
Dr. Smith
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE students (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO students VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),
])
conn.execute('CREATE TABLE courses (id INTEGER, name TEXT, prof_id INTEGER)')
conn.executemany('INSERT INTO courses VALUES (?,?,?)', [
    (1,'CS101',1),(2,'Math',2),(3,'Physics',2),
])
conn.execute('CREATE TABLE professors (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO professors VALUES (?,?)', [
    (1,'Dr. Smith'),(2,'Dr. Jones'),
])
conn.execute('CREATE TABLE enrollments (student_id INTEGER, course_id INTEGER)')
conn.executemany('INSERT INTO enrollments VALUES (?,?)', [
    (1,1),(1,2),(2,2),(2,3),(3,1),
])
conn.commit()

student_name = input()
# Hint: JOIN students → enrollments → courses → professors, DISTINCT, ORDER BY prof name
query = "SELECT ..."
for row in conn.execute(query, [student_name]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Combining `LEFT JOIN` with `GROUP BY` and `HAVING` lets you find customers who have spent above a threshold — while naturally including customers with zero orders (total = 0).

**customers**: (id, name)  
**orders**: (id, customer_id, amount)

customers: Alice(1), Bob(2), Carol(3), Dave(4)  
orders: (1,1,800), (2,1,400), (3,2,300), (4,3,1200), (5,3,100)

Dave has no orders → total = 0.

Write a SQL query that returns the **name** and **total spend** of customers whose total is **greater than 1000**, ordered by name.

Expected output:
```
Alice 1200
Carol 1300
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,800),(2,1,400),(3,2,300),(4,3,1200),(5,3,100),
])
conn.commit()

# Hint: LEFT JOIN, COALESCE(SUM,0), HAVING SUM > 1000
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Joining `orders` back to `products` and grouping by product name lets you find products that attracted **multiple distinct customers** — a sign of broad market appeal.

The `orders` table:

| id | customer_id | product | amount |
|----|------------|---------|--------|
| 1  | 1          | Laptop  | 1200   |
| 2  | 2          | Laptop  | 1200   |
| 3  | 3          | Phone   | 800    |
| 4  | 1          | Tablet  | 600    |
| 5  | 2          | Phone   | 800    |
| 6  | 3          | Laptop  | 1200   |

Write a SQL query that returns each **product** and the number of **distinct customers** who bought it, for products bought by **more than one** distinct customer. Order by product name.

Expected output:
```
Laptop 3
Phone 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, product TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,1,'Laptop',1200),(2,2,'Laptop',1200),(3,3,'Phone',800),
    (4,1,'Tablet',600),(5,2,'Phone',800),(6,3,'Laptop',1200),
])
conn.commit()

# Hint: GROUP BY product, HAVING COUNT(DISTINCT customer_id) > 1
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
A **self-join** joins a table to itself using two different aliases. It is the standard way to query hierarchical data like a manager-employee relationship stored in a single table.

The `employees` table has a `manager_id` that references `id` in the same table (`NULL` means no manager):

| id | name  | manager_id |
|----|-------|-----------|
| 1  | Alice | NULL      |
| 2  | Bob   | 1         |
| 3  | Carol | 1         |
| 4  | Dave  | 2         |
| 5  | Eve   | 3         |

Write a SQL query that returns each employee's **name** and their **manager's name**, for employees who **have** a manager. Order by employee name.

Expected output:
```
Bob Alice
Carol Alice
Dave Bob
Eve Carol
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, manager_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',None),(2,'Bob',1),(3,'Carol',1),
    (4,'Dave',2),(5,'Eve',3),
])
conn.commit()

# Hint: JOIN employees e ON e.manager_id = m.id (self-join), ORDER BY e.name
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
The **anti-join** pattern (`LEFT JOIN … WHERE right.key IS NULL` or `NOT EXISTS`) finds rows in one table with **no matching row** in another. It is ideal for finding products never ordered.

**products**: (id, name)  
**orders**: (id, product_id, amount)

products: Laptop(1), Phone(2), Tablet(3), Monitor(4), Keyboard(5)  
orders: (1,1,1200), (2,3,600), (3,1,1200), (4,2,800)

Write a SQL query that returns the **names** of products that have **never** been ordered, ordered alphabetically.

Expected output:
```
Keyboard
Monitor
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO products VALUES (?,?)', [
    (1,'Laptop'),(2,'Phone'),(3,'Tablet'),(4,'Monitor'),(5,'Keyboard'),
])
conn.execute('CREATE TABLE orders (id INTEGER, product_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,1200),(2,3,600),(3,1,1200),(4,2,800),
])
conn.commit()

# Hint: LEFT JOIN orders, WHERE orders.id IS NULL  (or NOT EXISTS)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
A **non-equi join** uses inequality operators (`<`, `>`, `BETWEEN`, `ABS(a-b) < n`) in the join condition. It is useful for finding pairs of rows that satisfy a range relationship.

The `employees` table:

| id | name  | salary | dept       |
|----|-------|--------|------------|
| 1  | Alice | 50000  | IT         |
| 2  | Bob   | 52000  | HR         |
| 3  | Carol | 55000  | Sales      |
| 4  | Dave  | 50500  | Marketing  |
| 5  | Eve   | 54000  | IT         |

Find all pairs of employees from **different departments** whose salaries differ by **less than 2000** (i.e., `ABS(e1.salary - e2.salary) < 2000`). Print `name1 name2` where `name1 < name2` alphabetically, ordered by `name1` then `name2`.

Expected output:
```
Alice Dave
Bob Dave
Carol Eve
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',50000,'IT'),(2,'Bob',52000,'HR'),
    (3,'Carol',55000,'Sales'),(4,'Dave',50500,'Marketing'),
    (5,'Eve',54000,'IT'),
])
conn.commit()

# Hint: self-join e1, e2 WHERE e1.dept != e2.dept AND ABS(e1.salary-e2.salary)<2000
#        AND e1.name < e2.name   ORDER BY e1.name, e2.name
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
Multiple `LEFT JOIN`s on the same base table build a **full summary** row per customer, even for those with no orders.

**customers**: (id, name)  
**orders**: (id, customer_id, amount)

customers: Alice(1), Bob(2), Carol(3), Dave(4)  
orders: (1,1,500),(2,1,300),(3,2,800),(4,3,200),(5,3,400),(6,3,100)

Write a SQL query that returns every customer's **name**, **order count**, and **total spend** (use 0 for customers with no orders). Order by name.

Expected output:
```
Alice 2 800
Bob 1 800
Carol 3 700
Dave 0 0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,500),(2,1,300),(3,2,800),(4,3,200),(5,3,400),(6,3,100),
])
conn.commit()

# Hint: LEFT JOIN, COUNT(o.id), COALESCE(SUM(o.amount),0), GROUP BY c.id, ORDER BY c.name
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Subqueries & CTEs (Q25–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
A **scalar subquery in SELECT** is a subquery that returns a single value and is evaluated for every row. It lets you compare each row against a global statistic such as the company-wide average salary.

The `employees` table:

| id | name  | salary | dept  |
|----|-------|--------|-------|
| 1  | Alice | 70000  | IT    |
| 2  | Bob   | 45000  | HR    |
| 3  | Carol | 85000  | IT    |
| 4  | Dave  | 55000  | Sales |
| 5  | Eve   | 60000  | HR    |
| 6  | Frank | 90000  | IT    |

Write a SQL query that returns each employee's **name**, **salary**, and the difference `salary - avg_salary` (a positive value means above average). Order by name.

Expected output:
```
Alice 70000 2500.0
Bob 45000 -22500.0
Carol 85000 17500.0
Dave 55000 -12500.0
Eve 60000 -7500.0
Frank 90000 22500.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),(2,'Bob',45000,'HR'),(3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),(5,'Eve',60000,'HR'),(6,'Frank',90000,'IT'),
])
conn.commit()

# Hint: SELECT name, salary, salary - (SELECT AVG(salary) FROM employees)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
**Multiple CTEs** can be chained in a single `WITH` clause — each one building on the last. A common pattern is: CTE1 computes department averages, CTE2 computes the overall average, and the main query filters.

Using the `employees` table below, write a query with **two CTEs**:
1. `dept_avg` — average salary per department
2. `overall_avg` — overall average salary (single-row scalar)

Then return each department whose average exceeds the overall average. Order by department.

Employees:
| id | name  | salary | dept  |
|----|-------|--------|-------|
| 1  | Alice | 80000  | IT    |
| 2  | Bob   | 45000  | HR    |
| 3  | Carol | 90000  | IT    |
| 4  | Dave  | 55000  | Sales |
| 5  | Eve   | 60000  | HR    |
| 6  | Frank | 82000  | IT    |

Expected output:
```
IT 84000.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',80000,'IT'),(2,'Bob',45000,'HR'),(3,'Carol',90000,'IT'),
    (4,'Dave',55000,'Sales'),(5,'Eve',60000,'HR'),(6,'Frank',82000,'IT'),
])
conn.commit()

# Hint:
# WITH dept_avg AS (SELECT dept, AVG(salary) avg FROM employees GROUP BY dept),
#      overall_avg AS (SELECT AVG(salary) avg FROM employees)
# SELECT dept, avg FROM dept_avg, overall_avg WHERE dept_avg.avg > overall_avg.avg
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
A **recursive CTE** can traverse a hierarchy stored in a single table. The pattern is:

```sql
WITH RECURSIVE cte(col) AS (
    -- Anchor: base case
    SELECT ... WHERE manager_id = ?
    UNION ALL
    -- Recursive step: expand one level
    SELECT e.id FROM employees e JOIN cte ON e.manager_id = cte.id
)
SELECT id FROM cte ORDER BY id
```

The `employees` table:

| id | name  | manager_id |
|----|-------|-----------|
| 1  | Alice | NULL      |
| 2  | Bob   | 1         |
| 3  | Carol | 1         |
| 4  | Dave  | 2         |
| 5  | Eve   | 3         |
| 6  | Frank | 4         |

Read a **manager id** from input and print the **ids** of all direct and indirect reports, ordered ascending.

Example:
```
Input:  1
Output:
2
3
4
5
6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, manager_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',None),(2,'Bob',1),(3,'Carol',1),
    (4,'Dave',2),(5,'Eve',3),(6,'Frank',4),
])
conn.commit()

manager_id = int(input())
# Write your recursive CTE below
query = "SELECT ..."
for row in conn.execute(query, [manager_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
`EXISTS` returns `TRUE` if a correlated subquery returns at least one row. It is more expressive than `IN` when you need to check for the **existence** of a related row that meets a condition.

**customers**: (id, name)  
**orders**: (id, customer_id, amount)

customers: Alice(1), Bob(2), Carol(3), Dave(4)  
orders: (1,1,300),(2,1,800),(3,2,200),(4,2,400),(5,3,600),(6,4,100)

Write a SQL query using `EXISTS` that returns the **names** of customers who have placed at least one order with amount **strictly greater than 500**, ordered alphabetically.

Expected output:
```
Alice
Carol
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),
])
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,1,300),(2,1,800),(3,2,200),(4,2,400),(5,3,600),(6,4,100),
])
conn.commit()

# Hint: WHERE EXISTS (SELECT 1 FROM orders WHERE customer_id = c.id AND amount > 500)
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
Combining a **CTE with a window function**, then filtering the CTE result, is a clean pattern for finding the **top-N rows per group**.

The `employees` table:

| id | name  | salary | dept  |
|----|-------|--------|-------|
| 1  | Alice | 70000  | IT    |
| 2  | Bob   | 45000  | HR    |
| 3  | Carol | 85000  | IT    |
| 4  | Dave  | 55000  | Sales |
| 5  | Eve   | 60000  | HR    |
| 6  | Frank | 90000  | IT    |
| 7  | Grace | 48000  | HR    |

Write a query that uses a CTE with `RANK() OVER (PARTITION BY dept ORDER BY salary DESC)` to find the **top 2 earners per department**. Print `name dept rank`, ordered by dept then rank.

Expected output:
```
Eve HR 1
Grace HR 2
Frank IT 1
Carol IT 2
Dave Sales 1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept TEXT
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,'IT'),(2,'Bob',45000,'HR'),(3,'Carol',85000,'IT'),
    (4,'Dave',55000,'Sales'),(5,'Eve',60000,'HR'),(6,'Frank',90000,'IT'),
    (7,'Grace',48000,'HR'),
])
conn.commit()

# Hint: WITH ranked AS (SELECT ..., RANK() OVER (...) rk ...) SELECT ... WHERE rk <= 2
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
A **subquery inside HAVING** computes an aggregate in the inner query that is then compared against the outer group's aggregate. This is how you find groups whose aggregate **exceeds the average aggregate** across all groups.

The `orders` table:

| id | product | amount |
|----|---------|--------|
| 1  | Laptop  | 1200   |
| 2  | Phone   | 800    |
| 3  | Laptop  | 1500   |
| 4  => Tablet  | 600    |
| 5  | Phone   | 900    |
| 6  | Laptop  | 600    |
| 7  | Tablet  | 400    |

Write a SQL query that returns the **product** and its **total sales**, for products whose total sales exceed the **average product total** across all products. Order by product.

Expected output:
```
Laptop 3300
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, product TEXT, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,'Laptop',1200),(2,'Phone',800),(3,'Laptop',1500),
    (4,'Tablet',600),(5,'Phone',900),(6,'Laptop',600),(7,'Tablet',400),
])
conn.commit()

# Hint: GROUP BY product HAVING SUM(amount) > (SELECT AVG(total) FROM (...) sub)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Window Functions (Q31–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
`DENSE_RANK()` assigns ranks without gaps — tied rows share the same rank, and the next rank immediately follows. This differs from `RANK()`, which skips numbers after ties.

The `employees` table:

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 70000  |
| 2  | Bob   | 70000  |
| 3  | Carol | 85000  |
| 4  | Dave  | 55000  |
| 5  | Eve   | 60000  |
| 6  | Frank | 90000  |

Write a SQL query that returns each employee's **name**, **salary**, and **dense rank** by salary descending. Order by dense rank ascending, then name ascending for ties.

Expected output:
```
Frank 90000 1
Carol 85000 2
Alice 70000 3
Bob 70000 3
Eve 60000 4
Dave 55000 5
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',70000),(2,'Bob',70000),(3,'Carol',85000),
    (4,'Dave',55000),(5,'Eve',60000),(6,'Frank',90000),
])
conn.commit()

# Hint: DENSE_RANK() OVER (ORDER BY salary DESC)
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
`NTILE(n)` divides the sorted result set into **n equal-sized buckets** and assigns each row a bucket number. It is commonly used to create quartiles (NTILE(4)) or deciles (NTILE(10)).

The `employees` table (8 rows):

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 50000  |
| 2  | Bob   | 70000  |
| 3  | Carol | 85000  |
| 4  | Dave  | 45000  |
| 5  | Eve   | 60000  |
| 6  | Frank | 90000  |
| 7  | Grace | 55000  |
| 8  | Hank  | 75000  |

Write a SQL query using `NTILE(4) OVER (ORDER BY salary ASC)` that returns each employee's **name**, **salary**, and **quartile** (1 = lowest, 4 = highest). Order by salary ascending.

Expected output:
```
Dave 45000 1
Alice 50000 1
Grace 55000 2
Eve 60000 2
Bob 70000 3
Hank 75000 3
Carol 85000 4
Frank 90000 4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',50000),(2,'Bob',70000),(3,'Carol',85000),(4,'Dave',45000),
    (5,'Eve',60000),(6,'Frank',90000),(7,'Grace',55000),(8,'Hank',75000),
])
conn.commit()

# Hint: NTILE(4) OVER (ORDER BY salary ASC)
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
`FIRST_VALUE` and `LAST_VALUE` return the first and last values of a window partition. For `LAST_VALUE` to be correct across an entire partition, you must specify `ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING`.

The `sales` table:

| sale_id | amount | month   |
|---------|--------|---------|
| 1       | 1200   | 2023-01 |
| 2       | 800    | 2023-01 |
| 3       | 1500   | 2023-02 |
| 4       | 600    | 2023-02 |
| 5       | 900    | 2023-03 |
| 6       | 2000   | 2023-03 |

Write a SQL query that returns `sale_id`, `amount`, `month`, the **first amount** in that month (by sale_id), and the **last amount** in that month. Order by `sale_id`.

Expected output:
```
1 1200 2023-01 1200 800
2 800 2023-01 1200 800
3 1500 2023-02 1500 600
4 600 2023-02 1500 600
5 900 2023-03 900 2000
6 2000 2023-03 900 2000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (sale_id INTEGER, amount INTEGER, month TEXT)')
conn.executemany('INSERT INTO sales VALUES (?,?,?)', [
    (1,1200,'2023-01'),(2,800,'2023-01'),
    (3,1500,'2023-02'),(4,600,'2023-02'),
    (5,900,'2023-03'),(6,2000,'2023-03'),
])
conn.commit()

# Hint:
# FIRST_VALUE(amount) OVER (PARTITION BY month ORDER BY sale_id)
# LAST_VALUE(amount)  OVER (PARTITION BY month ORDER BY sale_id
#                           ROWS BETWEEN UNBOUNDED PRECEDING AND UNBOUNDED FOLLOWING)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
A **moving average** smooths out short-term fluctuations. The frame `ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING` averages the current row with its immediate neighbours.

Using the same `sales` table (ordered by `sale_id`):

| sale_id | amount |
|---------|--------|
| 1       | 1200   |
| 2       | 800    |
| 3       | 1500   |
| 4       | 600    |
| 5       | 900    |
| 6       | 2000   |

Write a SQL query that returns `sale_id`, `amount`, and the **3-row moving average** of amount (rounded to 1 decimal place). Order by `sale_id`.

Expected output:
```
1 1200 1000.0
2 800 1166.7
3 1500 966.7
4 600 1000.0
5 900 1166.7
6 2000 1450.0
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (sale_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (1,1200),(2,800),(3,1500),(4,600),(5,900),(6,2000),
])
conn.commit()

# Hint: AVG(amount) OVER (ORDER BY sale_id ROWS BETWEEN 1 PRECEDING AND 1 FOLLOWING)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0], row[1], round(row[2], 1))
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
`SUM(col) OVER ()` (no `PARTITION BY`) computes the **grand total** across all rows and attaches it to every row. Dividing each row's value by this grand total gives the **percentage share** as a window function — no `GROUP BY` needed.

Using the same `sales` table (6 rows, total = 7000):

| sale_id | amount |
|---------|--------|
| 1       | 1200   |
| 2       | 800    |
| 3       | 1500   |
| 4       | 600    |
| 5       | 900    |
| 6       | 2000   |

Write a SQL query that returns `sale_id`, `amount`, and the percentage share `ROUND(amount * 100.0 / SUM(amount) OVER(), 1)`. Order by `sale_id`.

Expected output:
```
1 1200 17.1
2 800 11.4
3 1500 21.4
4 600 8.6
5 900 12.9
6 2000 28.6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (sale_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (1,1200),(2,800),(3,1500),(4,600),(5,900),(6,2000),
])
conn.commit()

# Hint: ROUND(amount * 100.0 / SUM(amount) OVER(), 1) AS pct
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
`ROW_NUMBER() OVER (PARTITION BY key ORDER BY id)` can **deduplicate** a table by keeping only the first occurrence of each key. Wrap it in a CTE and filter `WHERE rn = 1`.

The `products` table has duplicate names inserted at different IDs:

| id | name    | price |
|----|---------|-------|
| 1  | Laptop  | 1200  |
| 2  | Phone   | 800   |
| 3  | Laptop  | 1150  |
| 4  | Tablet  | 600   |
| 5  | Phone   | 750   |
| 6  | Monitor | 900   |

Write a query using `ROW_NUMBER() OVER (PARTITION BY name ORDER BY id)` that keeps only the **first-inserted** row per name and returns `name` and `price`. Order by name.

Expected output:
```
Laptop 1200
Monitor 900
Phone 800
Tablet 600
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, name TEXT, price INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1,'Laptop',1200),(2,'Phone',800),(3,'Laptop',1150),
    (4,'Tablet',600),(5,'Phone',750),(6,'Monitor',900),
])
conn.commit()

# Hint: WITH deduped AS (SELECT ..., ROW_NUMBER() OVER (PARTITION BY name ORDER BY id) rn ...)
#        SELECT name, price FROM deduped WHERE rn = 1 ORDER BY name
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Database Design & Normalization (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
In **BCNF (Boyce-Codd Normal Form)**, every determinant must be a candidate key. A common BCNF violation occurs when a non-key attribute (e.g., `room_capacity`) is determined by another non-key attribute (`room_id`), causing **update anomalies**.

The `course_offerings` table violates BCNF because `room_id → room_capacity`:

| id  | course_id | room_id | room_capacity |
|-----|----------|---------|--------------|
| 1   | 101      | R1      | 30           |
| 2   | 102      | R2      | 50           |
| 3   | 101      | R2      | 50           |
| 4   | 103      | R1      | 30           |
| 5   | 102      | R3      | 40           |

If room R2's capacity changes, every row that references R2 must be updated manually — a classic update anomaly.

Read a **room_id** from input and print the **number of rows** that would require an update if that room's capacity changed.

Example:
```
Input:  R2
Output: 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE course_offerings (
    id INTEGER, course_id INTEGER, room_id TEXT, room_capacity INTEGER
)''')
conn.executemany('INSERT INTO course_offerings VALUES (?,?,?,?)', [
    (1,101,'R1',30),(2,102,'R2',50),(3,101,'R2',50),
    (4,103,'R1',30),(5,102,'R3',40),
])
conn.commit()

room_id = input()
# Write your SQL query below (count rows where room_id = ?)
query = "SELECT ..."
for row in conn.execute(query, [room_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
A **2NF violation** (partial dependency) causes data inconsistency: the same `product_id` can appear in multiple order rows with different `product_name` values, because the name depends only on `product_id`, not the full composite key `(order_id, product_id)`.

The `order_products` table:

| order_id | product_id | product_name | quantity |
|---------|-----------|-------------|---------|
| 1       | 101       | Laptop Pro   | 2       |
| 2       | 101       | Laptop Pro   | 1       |
| 3       | 102       | Phone X      | 3       |
| 4       | 102       | PhoneX       | 2       |
| 5       | 103       | Tablet       | 1       |

Product 102 appears under two different names — an inconsistency caused by the 2NF violation.

Write a SQL query that returns the **product_id** values where the same `product_id` is used with **more than one distinct `product_name`**, ordered ascending.

Expected output:
```
102
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE order_products (
    order_id INTEGER, product_id INTEGER, product_name TEXT, quantity INTEGER
)''')
conn.executemany('INSERT INTO order_products VALUES (?,?,?,?)', [
    (1,101,'Laptop Pro',2),(2,101,'Laptop Pro',1),(3,102,'Phone X',3),
    (4,102,'PhoneX',2),(5,103,'Tablet',1),
])
conn.commit()

# Hint: GROUP BY product_id HAVING COUNT(DISTINCT product_name) > 1
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
After normalizing a schema to **3NF**, queries that previously lived in one table must now JOIN across multiple tables — but the schema is free from update anomalies and redundancy.

A previously denormalized schema has been split into:

**employees**: (emp_id, name, dept_id)  
**departments**: (dept_id, dept_name)  
**projects**: (project_id, project_name)  
**emp_project_hours**: (emp_id, project_id, hours)

Write a SQL query that computes the **total hours worked per department**, by joining all four tables. Order by department name.

Data:
- Employees: Alice(1,IT), Bob(2,HR), Carol(3,IT), Dave(4,Sales)
- Departments: IT(1), HR(2), Sales(3)
- emp_project_hours: (1,1,10),(1,2,15),(2,1,20),(3,2,8),(3,3,12),(4,1,5),(4,2,10)

Expected output:
```
HR 20
IT 45
Sales 15
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (emp_id INTEGER, name TEXT, dept_id INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',1),(2,'Bob',2),(3,'Carol',1),(4,'Dave',3),
])
conn.execute('CREATE TABLE departments (dept_id INTEGER, dept_name TEXT)')
conn.executemany('INSERT INTO departments VALUES (?,?)', [
    (1,'IT'),(2,'HR'),(3,'Sales'),
])
conn.execute('CREATE TABLE projects (project_id INTEGER, project_name TEXT)')
conn.executemany('INSERT INTO projects VALUES (?,?)', [
    (1,'Alpha'),(2,'Beta'),(3,'Gamma'),
])
conn.execute('CREATE TABLE emp_project_hours (emp_id INTEGER, project_id INTEGER, hours INTEGER)')
conn.executemany('INSERT INTO emp_project_hours VALUES (?,?,?)', [
    (1,1,10),(1,2,15),(2,1,20),(3,2,8),(3,3,12),(4,1,5),(4,2,10),
])
conn.commit()

# Hint: JOIN emp → emp_project_hours → departments, GROUP BY dept_name
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
A first step in **normalizing** a denormalized flat table is counting **how many distinct entities** exist for each concept — this tells you how many rows each normalized table will have.

The denormalized `school` table stores student, course, instructor, and grade data in one flat structure:

| student_id | student_name | course_id | course_name | instructor_name | grade |
|-----------|-------------|----------|------------|----------------|-------|
| 1         | Alice        | 101      | CS101      | Dr. Smith      | A     |
| 2         | Bob          | 101      | CS101      | Dr. Smith      | B     |
| 1         | Alice        | 102      | Math       | Dr. Jones      | B     |
| 3         | Carol        | 102      | Math       | Dr. Jones      | A     |
| 2         | Bob          | 103      | Physics    | Dr. Jones      | C     |

Write Python code that queries this table and prints the number of **distinct courses** and **distinct instructors**.

Expected output:
```
courses: 3
instructors: 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE school (
    student_id INTEGER, student_name TEXT, course_id INTEGER,
    course_name TEXT, instructor_name TEXT, grade TEXT
)''')
conn.executemany('INSERT INTO school VALUES (?,?,?,?,?,?)', [
    (1,'Alice',101,'CS101','Dr. Smith','A'),
    (2,'Bob',101,'CS101','Dr. Smith','B'),
    (1,'Alice',102,'Math','Dr. Jones','B'),
    (3,'Carol',102,'Math','Dr. Jones','A'),
    (2,'Bob',103,'Physics','Dr. Jones','C'),
])
conn.commit()

# Query distinct counts for courses and instructors
courses = conn.execute("SELECT COUNT(DISTINCT course_id) FROM school").fetchone()[0]
instructors = # Your query here
print(f"courses: {courses}")
print(f"instructors: {instructors}")
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
A **surrogate key** is a system-generated unique identifier. `ROW_NUMBER() OVER (ORDER BY name)` can act as a surrogate key assignment when the source data has no natural id column.

The `products_raw` table has no id:

| name    | category    | price |
|---------|-------------|-------|
| Laptop  | Electronics | 1200  |
| Phone   | Electronics | 600   |
| Desk    | Furniture   | 400   |
| Chair   | Furniture   | 300   |
| Book    | Literature  | 25    |

Write a SQL query that assigns a surrogate key to each product using `ROW_NUMBER() OVER (ORDER BY name)` and returns `row_num name category price`, ordered by name.

Expected output:
```
1 Book Literature 25
2 Chair Furniture 300
3 Desk Furniture 400
4 Laptop Electronics 1200
5 Phone Electronics 600
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products_raw (name TEXT, category TEXT, price INTEGER)')
conn.executemany('INSERT INTO products_raw VALUES (?,?,?)', [
    ('Laptop','Electronics',1200),('Phone','Electronics',600),
    ('Desk','Furniture',400),('Chair','Furniture',300),
    ('Book','Literature',25),
])
conn.commit()

# Hint: SELECT ROW_NUMBER() OVER (ORDER BY name), name, category, price FROM products_raw ORDER BY name
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Indexing & Query Performance (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
A **composite index** on `(category, price)` allows the database to efficiently answer queries that filter on both columns. The query reads only the matching portion of the index rather than scanning the whole table.

The `products` table:

| id | category    | name         | price |
|----|-------------|-------------|-------|
| 1  | Electronics | Laptop       | 1200  |
| 2  | Electronics | Phone        | 600   |
| 3  | Electronics | Tablet       | 800   |
| 4  | Furniture   | Chair        | 300   |
| 5  | Furniture   | Desk         | 600   |
| 6  | Books       | Python Book  | 45    |
| 7  | Electronics | Keyboard     | 150   |
| 8  | Furniture   | Lamp         | 80    |

Read a **category** and a **maximum price** (on two separate input lines) and print the **names** of matching products, ordered alphabetically.

Example:
```
Input:
Electronics
700
Output:
Keyboard
Phone
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, category TEXT, name TEXT, price INTEGER
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Electronics','Laptop',1200),(2,'Electronics','Phone',600),
    (3,'Electronics','Tablet',800),(4,'Furniture','Chair',300),
    (5,'Furniture','Desk',600),(6,'Books','Python Book',45),
    (7,'Electronics','Keyboard',150),(8,'Furniture','Lamp',80),
])
conn.commit()

category = input()
max_price = int(input())
# Hint: WHERE category = ? AND price < ? ORDER BY name
query = "SELECT ..."
for row in conn.execute(query, [category, max_price]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
`LIKE 'prefix%'` can use a B-tree index because the prefix is fixed. However, `LIKE '%suffix'` cannot — it requires a full scan. Understanding this distinction helps you write index-friendly queries.

Using the same `products` table from Q42, read a **prefix** from input and print the **names** of products whose name **starts with** that prefix, ordered alphabetically.

Example:
```
Input:  P
Output:
Phone
Python Book
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, category TEXT, name TEXT, price INTEGER
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Electronics','Laptop',1200),(2,'Electronics','Phone',600),
    (3,'Electronics','Tablet',800),(4,'Furniture','Chair',300),
    (5,'Furniture','Desk',600),(6,'Books','Python Book',45),
    (7,'Electronics','Keyboard',150),(8,'Furniture','Lamp',80),
])
conn.commit()

prefix = input()
# Hint: WHERE name LIKE prefix + '%'  ORDER BY name
query = "SELECT ..."
for row in conn.execute(query, [prefix + '%']):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
An **index on a foreign key** column dramatically accelerates `JOIN` and `WHERE` filtering on that column. Without an index, the database must scan the entire child table.

The `employees` table has a foreign key `dept_id`, which — in a real system — would be indexed:

| id | name  | salary | dept_id |
|----|-------|--------|---------|
| 1  | Alice | 70000  | 1       |
| 2  | Bob   | 45000  | 2       |
| 3  | Carol | 85000  | 1       |
| 4  | Dave  | 55000  | 3       |
| 5  | Eve   | 60000  | 2       |
| 6  | Frank | 90000  | 1       |

Read a **dept_id** from input and print each employee's **name** and **salary** in that department, ordered by salary **descending**.

Example:
```
Input:  1
Output:
Frank 90000
Carol 85000
Alice 70000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE employees (
    id INTEGER, name TEXT, salary INTEGER, dept_id INTEGER
)''')
conn.executemany('INSERT INTO employees VALUES (?,?,?,?)', [
    (1,'Alice',70000,1),(2,'Bob',45000,2),(3,'Carol',85000,1),
    (4,'Dave',55000,3),(5,'Eve',60000,2),(6,'Frank',90000,1),
])
conn.commit()

dept_id = int(input())
# Hint: WHERE dept_id = ? ORDER BY salary DESC
query = "SELECT ..."
for row in conn.execute(query, [dept_id]):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
A **covering index** contains all the columns a query needs, so the database never touches the main table rows — only the index pages. A query `SELECT name FROM products WHERE category = ?` can be answered entirely from an index on `(category, name)`.

Using the same `products` table from Q42, read a **category** from input and print all **product names** in that category, ordered alphabetically.

Example:
```
Input:  Furniture
Output:
Chair
Desk
Lamp
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, category TEXT, name TEXT, price INTEGER
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?,?)', [
    (1,'Electronics','Laptop',1200),(2,'Electronics','Phone',600),
    (3,'Electronics','Tablet',800),(4,'Furniture','Chair',300),
    (5,'Furniture','Desk',600),(6,'Books','Python Book',45),
    (7,'Electronics','Keyboard',150),(8,'Furniture','Lamp',80),
])
conn.commit()

category = input()
# Hint: WHERE category = ? ORDER BY name
query = "SELECT ..."
for row in conn.execute(query, [category]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
An index on a numeric column like `salary` makes `ORDER BY salary DESC LIMIT N` extremely fast — the database just reads the first N entries of the index in reverse order without scanning the table.

The `employees` table:

| id | name  | salary |
|----|-------|--------|
| 1  | Alice | 70000  |
| 2  | Bob   | 45000  |
| 3  | Carol | 85000  |
| 4  | Dave  | 55000  |
| 5  | Eve   | 60000  |
| 6  | Frank | 90000  |

Read **N** from input and print the **Nth highest salary** (1-based).

Example:
```
Input:  3
Output: 70000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',70000),(2,'Bob',45000),(3,'Carol',85000),
    (4,'Dave',55000),(5,'Eve',60000),(6,'Frank',90000),
])
conn.commit()

n = int(input())
# Hint: ORDER BY salary DESC LIMIT 1 OFFSET n-1
query = "SELECT ..."
for row in conn.execute(query, [n - 1]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: NoSQL Databases & When to Use Them (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
**NoSQL document stores** can contain **nested objects and arrays** inside a single document. Processing such nested data in application code is a fundamental NoSQL skill.

The `documents` table stores user order summaries as JSON. Each document has a `"orders"` array of items with `"product"` and `"qty"` fields:

```json
{"user":"Alice","orders":[{"product":"Laptop","qty":1},{"product":"Phone","qty":2}]}
```

Parse each document and compute the **total quantity ordered** per user, ordered alphabetically by user name.

Expected output:
```
Alice 3
Bob 3
Carol 4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE documents (id INTEGER, data TEXT)')
conn.executemany('INSERT INTO documents VALUES (?,?)', [
    (1, '{"user":"Alice","orders":[{"product":"Laptop","qty":1},{"product":"Phone","qty":2}]}'),
    (2, '{"user":"Bob","orders":[{"product":"Tablet","qty":3}]}'),
    (3, '{"user":"Carol","orders":[{"product":"Laptop","qty":2},{"product":"Tablet","qty":1},{"product":"Phone","qty":1}]}'),
])
conn.commit()

user_totals = {}
for row in conn.execute("SELECT data FROM documents"):
    doc = json.loads(row[0])
    # Your logic here — sum qty across all orders for this user
    pass

for user in sorted(user_totals):
    print(user, user_totals[user])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
In a document database, an **update operation** selectively modifies documents matching a filter — similar to `UPDATE … WHERE` in SQL. The number of modified documents is an important metric.

The `products_docs` table stores product JSON documents. Each document has a `"category"` and an `"on_sale"` boolean.

Write Python code that counts how many **electronics** products currently have `"on_sale": false` — these are the documents a bulk-update would modify.

Expected output:
```
2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products_docs (id INTEGER, data TEXT)')
conn.executemany('INSERT INTO products_docs VALUES (?,?)', [
    (1, '{"name":"Laptop","category":"electronics","price":1200,"on_sale":false}'),
    (2, '{"name":"Phone","category":"electronics","price":600,"on_sale":true}'),
    (3, '{"name":"Chair","category":"furniture","price":300,"on_sale":false}'),
    (4, '{"name":"Tablet","category":"electronics","price":800,"on_sale":false}'),
])
conn.commit()

count = 0
for row in conn.execute("SELECT data FROM products_docs"):
    doc = json.loads(row[0])
    # Count electronics documents where on_sale is False
    pass

print(count)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
Document databases provide **schema flexibility** — individual documents may have different fields. This is useful for optional attributes like a product rating that only some products have been given.

The `documents` table:

| id | data |
|----|------|
| 1  | `{"name":"Laptop","price":1200,"rating":4.5}` |
| 2  | `{"name":"Phone","price":600}` |
| 3  | `{"name":"Tablet","price":800,"rating":3.8}` |
| 4  | `{"name":"Monitor","price":900}` |
| 5  | `{"name":"Keyboard","price":150,"rating":4.2}` |

Write Python code that prints the **names** of products that have a `"rating"` field **and** whose rating is **strictly greater than 4.0**, ordered alphabetically.

Expected output:
```
Keyboard
Laptop
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE documents (id INTEGER, data TEXT)')
conn.executemany('INSERT INTO documents VALUES (?,?)', [
    (1, '{"name":"Laptop","price":1200,"rating":4.5}'),
    (2, '{"name":"Phone","price":600}'),
    (3, '{"name":"Tablet","price":800,"rating":3.8}'),
    (4, '{"name":"Monitor","price":900}'),
    (5, '{"name":"Keyboard","price":150,"rating":4.2}'),
])
conn.commit()

names = []
for row in conn.execute("SELECT data FROM documents"):
    doc = json.loads(row[0])
    # Check for 'rating' key and filter by value
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
A **NoSQL aggregation pipeline** processes documents in stages: filter → group → count. This is equivalent to a SQL `WHERE … GROUP BY … COUNT(*)` query, but executed in application code when the database has no native aggregation support.

The `products_docs` table:

| id | data |
|----|------|
| 1  | `{"name":"Laptop","category":"electronics","on_sale":false}` |
| 2  | `{"name":"Phone","category":"electronics","on_sale":true}` |
| 3  | `{"name":"Chair","category":"furniture","on_sale":false}` |
| 4  | `{"name":"Tablet","category":"electronics","on_sale":true}` |
| 5  | `{"name":"Monitor","category":"electronics","on_sale":false}` |
| 6  | `{"name":"Keyboard","category":"electronics","on_sale":true}` |

Write Python code that:
1. **Filters** to electronics documents only
2. **Groups** by `on_sale` value
3. **Counts** documents in each group

Print `on_sale_value count` pairs, ordered alphabetically by the string value of `on_sale` (`false` before `true`).

Expected output:
```
false 2
true 3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3, json
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products_docs (id INTEGER, data TEXT)')
conn.executemany('INSERT INTO products_docs VALUES (?,?)', [
    (1, '{"name":"Laptop","category":"electronics","on_sale":false}'),
    (2, '{"name":"Phone","category":"electronics","on_sale":true}'),
    (3, '{"name":"Chair","category":"furniture","on_sale":false}'),
    (4, '{"name":"Tablet","category":"electronics","on_sale":true}'),
    (5, '{"name":"Monitor","category":"electronics","on_sale":false}'),
    (6, '{"name":"Keyboard","category":"electronics","on_sale":true}'),
])
conn.commit()

counts = {}  # on_sale_value (str) → count
for row in conn.execute("SELECT data FROM products_docs"):
    doc = json.loads(row[0])
    # Step 1: filter electronics
    # Step 2: group by on_sale (convert bool to 'true'/'false')
    pass

for key in sorted(counts):
    print(key, counts[key])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
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

        // ── Q1: Negative balance accounts ────────────────────────────────
        $seed(1, [
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: Employees on more than one project ────────────────────────
        $seed(2, [
            ['input' => null, 'expected_output' => "Alice\nCarol\nEve", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\nCarol\nEve", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\nCarol\nEve", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\nCarol\nEve", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: Duplicate composite-key pairs ─────────────────────────────
        $seed(3, [
            ['input' => null, 'expected_output' => "1 101\n2 102", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 101\n2 102", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 101\n2 102", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 101\n2 102", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: Referential integrity violations ──────────────────────────
        $seed(4, [
            ['input' => null, 'expected_output' => "2\n3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2\n3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2\n3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2\n3", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: Cascade delete order count ───────────────────────────────
        $seed(5, [
            ['input' => '1', 'expected_output' => '4', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '1', 'expected_output' => '4', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '2', 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: CASE salary band ──────────────────────────────────────────
        $seed(6, [
            ['input' => null, 'expected_output' => "Alice Low\nBob Medium\nCarol High\nDave Medium\nEve High\nFrank Low", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice Low\nBob Medium\nCarol High\nDave Medium\nEve High\nFrank Low", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice Low\nBob Medium\nCarol High\nDave Medium\nEve High\nFrank Low", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice Low\nBob Medium\nCarol High\nDave Medium\nEve High\nFrank Low", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: LIKE prefix ───────────────────────────────────────────────
        $seed(7, [
            ['input' => 'Pro',      'expected_output' => "ProBook\nProDisplay",     'is_hidden' => false, 'order_index' => 1],
            ['input' => 'La',       'expected_output' => 'Laptop',                  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Key',      'expected_output' => 'Keyboard',                'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Monitor',  'expected_output' => 'Monitor Pro',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: IN subquery by category ───────────────────────────────────
        $seed(8, [
            ['input' => 'Electronics', 'expected_output' => "Alice\nBob\nCarol", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Food',        'expected_output' => "Alice\nBob",        'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Furniture',   'expected_output' => 'Dave',              'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Electronics', 'expected_output' => "Alice\nBob\nCarol", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Pagination (page size 3) ──────────────────────────────────
        $seed(9, [
            ['input' => '1', 'expected_output' => "Laptop\nMonitor\nTablet",    'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "Phone\nHeadphones\nKeyboard",'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => "Webcam\nMouse\nUSB Hub",     'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "Laptop\nMonitor\nTablet",    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: COALESCE threshold ───────────────────────────────────────
        $seed(10, [
            ['input' => '50000',  'expected_output' => "Alice\nCarol\nEve",           'is_hidden' => false, 'order_index' => 1],
            ['input' => '-1',     'expected_output' => "Alice\nBob\nCarol\nDave\nEve",'is_hidden' => false, 'order_index' => 2],
            ['input' => '80000',  'expected_output' => 'Carol',                        'is_hidden' => true,  'order_index' => 3],
            ['input' => '0',      'expected_output' => "Alice\nCarol\nEve",            'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Email domain count ───────────────────────────────────────
        $seed(11, [
            ['input' => null, 'expected_output' => "company.org 2\ngmail.com 2\nuni.edu 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "company.org 2\ngmail.com 2\nuni.edu 1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "company.org 2\ngmail.com 2\nuni.edu 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "company.org 2\ngmail.com 2\nuni.edu 1", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Highest total customer ───────────────────────────────────
        $seed(12, [
            ['input' => null, 'expected_output' => '1 2150', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1 2150', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1 2150', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1 2150', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Conditional aggregation (net sales + return count) ───────
        $seed(13, [
            ['input' => null, 'expected_output' => "Laptop 2700 1\nPhone 600 1\nTablet 700 0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 2700 1\nPhone 600 1\nTablet 700 0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 2700 1\nPhone 600 1\nTablet 700 0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 2700 1\nPhone 600 1\nTablet 700 0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: HAVING max-min > 20000 ──────────────────────────────────
        $seed(14, [
            ['input' => null, 'expected_output' => 'IT', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'IT', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'IT', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'IT', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: Highest average monthly sales ───────────────────────────
        $seed(15, [
            ['input' => null, 'expected_output' => 'Laptop 2100.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'Laptop 2100.0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'Laptop 2100.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'Laptop 2100.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: Percentage share ─────────────────────────────────────────
        $seed(16, [
            ['input' => null, 'expected_output' => "Electronics 60.0\nFood 10.0\nFurniture 30.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Electronics 60.0\nFood 10.0\nFurniture 30.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Electronics 60.0\nFood 10.0\nFurniture 30.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Electronics 60.0\nFood 10.0\nFurniture 30.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: GROUP BY customer + category ────────────────────────────
        $seed(17, [
            ['input' => null, 'expected_output' => "1 Electronics 2100\n1 Food 50\n2 Electronics 1400\n3 Food 30\n3 Furniture 400", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 Electronics 2100\n1 Food 50\n2 Electronics 1400\n3 Food 30\n3 Furniture 400", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 Electronics 2100\n1 Food 50\n2 Electronics 1400\n3 Food 30\n3 Furniture 400", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 Electronics 2100\n1 Food 50\n2 Electronics 1400\n3 Food 30\n3 Furniture 400", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: 4-table JOIN (student → professors) ─────────────────────
        $seed(18, [
            ['input' => 'Alice', 'expected_output' => "Dr. Jones\nDr. Smith", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Bob',   'expected_output' => 'Dr. Jones',            'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Carol', 'expected_output' => 'Dr. Smith',            'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Alice', 'expected_output' => "Dr. Jones\nDr. Smith", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: LEFT JOIN top spenders ───────────────────────────────────
        $seed(19, [
            ['input' => null, 'expected_output' => "Alice 1200\nCarol 1300", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 1200\nCarol 1300", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 1200\nCarol 1300", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 1200\nCarol 1300", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: Products with multiple distinct customers ────────────────
        $seed(20, [
            ['input' => null, 'expected_output' => "Laptop 3\nPhone 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 3\nPhone 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 3\nPhone 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 3\nPhone 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Self-join manager hierarchy ─────────────────────────────
        $seed(21, [
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice\nDave Bob\nEve Carol", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice\nDave Bob\nEve Carol", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice\nDave Bob\nEve Carol", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice\nDave Bob\nEve Carol", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Anti-join never-ordered products ─────────────────────────
        $seed(22, [
            ['input' => null, 'expected_output' => "Keyboard\nMonitor", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Keyboard\nMonitor", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Keyboard\nMonitor", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Keyboard\nMonitor", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Non-equi join salary diff < 2000 different dept ─────────
        $seed(23, [
            ['input' => null, 'expected_output' => "Alice Dave\nBob Dave\nCarol Eve", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice Dave\nBob Dave\nCarol Eve", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice Dave\nBob Dave\nCarol Eve", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice Dave\nBob Dave\nCarol Eve", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Full customer summary ────────────────────────────────────
        $seed(24, [
            ['input' => null, 'expected_output' => "Alice 2 800\nBob 1 800\nCarol 3 700\nDave 0 0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 2 800\nBob 1 800\nCarol 3 700\nDave 0 0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 2 800\nBob 1 800\nCarol 3 700\nDave 0 0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 2 800\nBob 1 800\nCarol 3 700\nDave 0 0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: Scalar subquery diff from average ────────────────────────
        $seed(25, [
            ['input' => null, 'expected_output' => "Alice 70000 2500.0\nBob 45000 -22500.0\nCarol 85000 17500.0\nDave 55000 -12500.0\nEve 60000 -7500.0\nFrank 90000 22500.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 70000 2500.0\nBob 45000 -22500.0\nCarol 85000 17500.0\nDave 55000 -12500.0\nEve 60000 -7500.0\nFrank 90000 22500.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 70000 2500.0\nBob 45000 -22500.0\nCarol 85000 17500.0\nDave 55000 -12500.0\nEve 60000 -7500.0\nFrank 90000 22500.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 70000 2500.0\nBob 45000 -22500.0\nCarol 85000 17500.0\nDave 55000 -12500.0\nEve 60000 -7500.0\nFrank 90000 22500.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Chained CTEs dept above overall avg ──────────────────────
        $seed(26, [
            ['input' => null, 'expected_output' => 'IT 84000.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'IT 84000.0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'IT 84000.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'IT 84000.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Recursive CTE — all reports ─────────────────────────────
        $seed(27, [
            ['input' => '1', 'expected_output' => "2\n3\n4\n5\n6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "4\n6",          'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => '5',              'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "2\n3\n4\n5\n6", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: EXISTS — customers with order > 500 ─────────────────────
        $seed(28, [
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: CTE + window top-2 per dept ─────────────────────────────
        $seed(29, [
            ['input' => null, 'expected_output' => "Eve HR 1\nGrace HR 2\nFrank IT 1\nCarol IT 2\nDave Sales 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Eve HR 1\nGrace HR 2\nFrank IT 1\nCarol IT 2\nDave Sales 1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Eve HR 1\nGrace HR 2\nFrank IT 1\nCarol IT 2\nDave Sales 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Eve HR 1\nGrace HR 2\nFrank IT 1\nCarol IT 2\nDave Sales 1", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Subquery in HAVING above avg product total ───────────────
        $seed(30, [
            ['input' => null, 'expected_output' => 'Laptop 3300', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => 'Laptop 3300', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => 'Laptop 3300', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => 'Laptop 3300', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: DENSE_RANK by salary ─────────────────────────────────────
        $seed(31, [
            ['input' => null, 'expected_output' => "Frank 90000 1\nCarol 85000 2\nAlice 70000 3\nBob 70000 3\nEve 60000 4\nDave 55000 5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Frank 90000 1\nCarol 85000 2\nAlice 70000 3\nBob 70000 3\nEve 60000 4\nDave 55000 5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Frank 90000 1\nCarol 85000 2\nAlice 70000 3\nBob 70000 3\nEve 60000 4\nDave 55000 5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Frank 90000 1\nCarol 85000 2\nAlice 70000 3\nBob 70000 3\nEve 60000 4\nDave 55000 5", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: NTILE(4) salary quartiles ───────────────────────────────
        $seed(32, [
            ['input' => null, 'expected_output' => "Dave 45000 1\nAlice 50000 1\nGrace 55000 2\nEve 60000 2\nBob 70000 3\nHank 75000 3\nCarol 85000 4\nFrank 90000 4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Dave 45000 1\nAlice 50000 1\nGrace 55000 2\nEve 60000 2\nBob 70000 3\nHank 75000 3\nCarol 85000 4\nFrank 90000 4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Dave 45000 1\nAlice 50000 1\nGrace 55000 2\nEve 60000 2\nBob 70000 3\nHank 75000 3\nCarol 85000 4\nFrank 90000 4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Dave 45000 1\nAlice 50000 1\nGrace 55000 2\nEve 60000 2\nBob 70000 3\nHank 75000 3\nCarol 85000 4\nFrank 90000 4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: FIRST_VALUE and LAST_VALUE per month ─────────────────────
        $seed(33, [
            ['input' => null, 'expected_output' => "1 1200 2023-01 1200 800\n2 800 2023-01 1200 800\n3 1500 2023-02 1500 600\n4 600 2023-02 1500 600\n5 900 2023-03 900 2000\n6 2000 2023-03 900 2000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1200 800\n2 800 2023-01 1200 800\n3 1500 2023-02 1500 600\n4 600 2023-02 1500 600\n5 900 2023-03 900 2000\n6 2000 2023-03 900 2000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1200 800\n2 800 2023-01 1200 800\n3 1500 2023-02 1500 600\n4 600 2023-02 1500 600\n5 900 2023-03 900 2000\n6 2000 2023-03 900 2000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 2023-01 1200 800\n2 800 2023-01 1200 800\n3 1500 2023-02 1500 600\n4 600 2023-02 1500 600\n5 900 2023-03 900 2000\n6 2000 2023-03 900 2000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: 3-row moving average ─────────────────────────────────────
        $seed(34, [
            ['input' => null, 'expected_output' => "1 1200 1000.0\n2 800 1166.7\n3 1500 966.7\n4 600 1000.0\n5 900 1166.7\n6 2000 1450.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 1000.0\n2 800 1166.7\n3 1500 966.7\n4 600 1000.0\n5 900 1166.7\n6 2000 1450.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 1000.0\n2 800 1166.7\n3 1500 966.7\n4 600 1000.0\n5 900 1166.7\n6 2000 1450.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 1000.0\n2 800 1166.7\n3 1500 966.7\n4 600 1000.0\n5 900 1166.7\n6 2000 1450.0", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Percent of grand total (window) ──────────────────────────
        $seed(35, [
            ['input' => null, 'expected_output' => "1 1200 17.1\n2 800 11.4\n3 1500 21.4\n4 600 8.6\n5 900 12.9\n6 2000 28.6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 17.1\n2 800 11.4\n3 1500 21.4\n4 600 8.6\n5 900 12.9\n6 2000 28.6", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 17.1\n2 800 11.4\n3 1500 21.4\n4 600 8.6\n5 900 12.9\n6 2000 28.6", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 17.1\n2 800 11.4\n3 1500 21.4\n4 600 8.6\n5 900 12.9\n6 2000 28.6", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: ROW_NUMBER deduplication ────────────────────────────────
        $seed(36, [
            ['input' => null, 'expected_output' => "Laptop 1200\nMonitor 900\nPhone 800\nTablet 600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1200\nMonitor 900\nPhone 800\nTablet 600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1200\nMonitor 900\nPhone 800\nTablet 600", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1200\nMonitor 900\nPhone 800\nTablet 600", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: BCNF update anomaly row count ───────────────────────────
        $seed(37, [
            ['input' => 'R2', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'R1', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'R3', 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'R2', 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: 2NF violation — inconsistent product names ───────────────
        $seed(38, [
            ['input' => null, 'expected_output' => '102', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '102', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '102', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '102', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: 3NF — total hours per department ────────────────────────
        $seed(39, [
            ['input' => null, 'expected_output' => "HR 20\nIT 45\nSales 15", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 20\nIT 45\nSales 15", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 20\nIT 45\nSales 15", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 20\nIT 45\nSales 15", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Count distinct entities from denormalized table ──────────
        $seed(40, [
            ['input' => null, 'expected_output' => "courses: 3\ninstructors: 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "courses: 3\ninstructors: 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "courses: 3\ninstructors: 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "courses: 3\ninstructors: 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: ROW_NUMBER surrogate key ────────────────────────────────
        $seed(41, [
            ['input' => null, 'expected_output' => "1 Book Literature 25\n2 Chair Furniture 300\n3 Desk Furniture 400\n4 Laptop Electronics 1200\n5 Phone Electronics 600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 Book Literature 25\n2 Chair Furniture 300\n3 Desk Furniture 400\n4 Laptop Electronics 1200\n5 Phone Electronics 600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 Book Literature 25\n2 Chair Furniture 300\n3 Desk Furniture 400\n4 Laptop Electronics 1200\n5 Phone Electronics 600", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 Book Literature 25\n2 Chair Furniture 300\n3 Desk Furniture 400\n4 Laptop Electronics 1200\n5 Phone Electronics 600", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Composite index — category + price filter ────────────────
        $seed(42, [
            ['input' => "Electronics\n700",  'expected_output' => "Keyboard\nPhone",   'is_hidden' => false, 'order_index' => 1],
            ['input' => "Furniture\n400",    'expected_output' => "Chair\nLamp",        'is_hidden' => false, 'order_index' => 2],
            ['input' => "Electronics\n1300", 'expected_output' => "Keyboard\nPhone\nTablet", 'is_hidden' => true, 'order_index' => 3],
            ['input' => "Books\n100",        'expected_output' => 'Python Book',        'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: LIKE prefix index-friendly search ────────────────────────
        $seed(43, [
            ['input' => 'P',       'expected_output' => "Phone\nPython Book", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'La',      'expected_output' => 'Laptop',             'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Pro',     'expected_output' => '',                   'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Ta',      'expected_output' => 'Tablet',             'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: FK index — employees by dept ────────────────────────────
        $seed(44, [
            ['input' => '1', 'expected_output' => "Frank 90000\nCarol 85000\nAlice 70000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => "Eve 60000\nBob 45000",                  'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => 'Dave 55000',                             'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => "Frank 90000\nCarol 85000\nAlice 70000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Covering index — products by category ────────────────────
        $seed(45, [
            ['input' => 'Furniture',   'expected_output' => "Chair\nDesk\nLamp",           'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Electronics', 'expected_output' => "Keyboard\nLaptop\nPhone\nTablet", 'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Books',       'expected_output' => 'Python Book',                 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Furniture',   'expected_output' => "Chair\nDesk\nLamp",           'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Nth highest salary ───────────────────────────────────────
        $seed(46, [
            ['input' => '1', 'expected_output' => '90000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => '85000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => '70000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '4', 'expected_output' => '60000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: NoSQL sum nested arrays ──────────────────────────────────
        $seed(47, [
            ['input' => null, 'expected_output' => "Alice 3\nBob 3\nCarol 4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 3\nBob 3\nCarol 4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 3\nBob 3\nCarol 4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 3\nBob 3\nCarol 4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: NoSQL count based on multiple filters ─────────────────────
        $seed(48, [
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: NoSQL filter on optional fields ──────────────────────────
        $seed(49, [
            ['input' => null, 'expected_output' => "Keyboard\nLaptop", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Keyboard\nLaptop", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Keyboard\nLaptop", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Keyboard\nLaptop", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: NoSQL aggregation pipeline ───────────────────────────────
        $seed(50, [
            ['input' => null, 'expected_output' => "false 2\ntrue 3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "false 2\ntrue 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "false 2\ntrue 3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "false 2\ntrue 3", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 10 Coding (University Student) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}