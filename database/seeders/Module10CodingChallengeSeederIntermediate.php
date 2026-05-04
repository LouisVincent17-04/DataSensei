<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 10 — Relational Databases & SQL (Intermediate) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Intermediate tier
 *   2. coding_questions    — 50 questions covering advanced SQL/DB topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   10.1  Advanced Schema Constraints & Keys
 *   10.2  Advanced SELECT: COALESCE, CASE, Date/String Functions
 *   10.3  Conditional Aggregation & Group Filtering
 *   10.4  Complex JOINs: Multi-table, Self-Joins, Inequality Joins
 *   10.5  Correlated Subqueries & Recursive CTEs
 *   10.6  Advanced Window Functions: DENSE_RANK, NTILE, Moving Averages
 *   10.7  Entity-Attribute-Value (EAV) & Dimensional Modeling
 *   10.8  Query Optimization & SARGable Predicates
 *   10.10 Handling JSON within Relational DBs (SQLite JSON1)
 */
class Module10CodingChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────
        // 1. CHALLENGE
        // ─────────────────────────────────────────────────────────────────

        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (! $category) {
            $this->command->error('Intermediate category not found! Run ChallengeCategorySeeder first.');
            return;
        }

        $this->command->info('Creating Module 10 — Relational Databases & SQL (Intermediate) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Relational Databases & SQL',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Elevate your database logic to the intermediate tier. In these Python/SQLite tasks, you will navigate composite keys, pivot data using conditional aggregation, write complex self-joins, leverage correlated subqueries, apply advanced window functions (DENSE_RANK, moving averages), optimize query predicates for indexes, and extract nested JSON data.',
                'time_limit_seconds' => 1500, // Increased for intermediate
                'base_xp'            => 1000, // Higher XP reward
                'order_index'        => 10,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 intermediate coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: Advanced Schema Constraints & Keys (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A **composite primary key** uses two or more columns to uniquely identify a row. 

The `enrollments` table tracks students and the courses they take.
| student_id | course_id | semester |
|------------|-----------|----------|
| 1          | 101       | Fall     |
| 1          | 102       | Fall     |
| 2          | 101       | Spring   |
| 3          | 105       | Fall     |

Write a SQL query that returns the `student_id` of students enrolled in **more than one course**, ordered ascending.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE enrollments (student_id INTEGER, course_id INTEGER, semester TEXT, PRIMARY KEY(student_id, course_id))')
conn.executemany('INSERT INTO enrollments VALUES (?,?,?)', [
    (1,101,'Fall'), (1,102,'Fall'), (2,101,'Spring'), (3,105,'Fall')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q2
            [
                'order_index'         => 2,
                'problem_description' => <<<'MD'
A `UNIQUE` constraint ensures all values in a column or group of columns are distinct. 
The `users` table tracks user emails, but we want to identify any existing duplicates before applying a unique constraint.

Write a SQL query that returns the `email` addresses that appear **more than once**, ordered alphabetically.

Expected output:
admin@test.com

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, email TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [
    (1,'admin@test.com'), (2,'bob@test.com'), (3,'admin@test.com'), (4,'carol@test.com')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q3
            [
                'order_index'         => 3,
                'problem_description' => <<<'MD'
Constraints like `CHECK` enforce domain integrity. 

The `products` table has `price` and `stock`. Identify any rows that violate the logic: `price >= 0` AND `stock >= 0`.
Return the `id` of the violating rows, ordered ascending.

Expected output:
2
4

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, price INTEGER, stock INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1, 100, 5), (2, -10, 10), (3, 50, 0), (4, 20, -5)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Foreign keys often have cascading deletes (`ON DELETE CASCADE`). If a parent is removed, orphans are deleted.
We need to simulate finding orphans across a 3-tier hierarchy: `Universities -> Departments -> Courses`.

Find the `course_name` of any course whose `dept_id` does NOT exist in the `departments` table.

Expected output:
Intro to Magic

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE departments (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO departments VALUES (?,?)', [(1,'Math'),(2,'Science')])
conn.execute('CREATE TABLE courses (id INTEGER, dept_id INTEGER, course_name TEXT)')
conn.executemany('INSERT INTO courses VALUES (?,?,?)', [
    (101, 1, 'Calculus'), (102, 2, 'Physics'), (103, 99, 'Intro to Magic')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Data integrity often requires updating rows to resolve anomalies. Using Python and SQLite, find all employees in the `employees` table with a `salary` mapped as `NULL` and `UPDATE` them to a default of `40000`. 
Then print the sum of all salaries.

Expected output:
150000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    (1, 50000), (2, None), (3, 60000), (4, None)
])
conn.commit()

# Execute an UPDATE query
conn.execute("...")

# Print the sum
for row in conn.execute("SELECT SUM(salary) FROM employees"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 2: Advanced SELECT — COALESCE, CASE, Dates (Q6–Q11)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
`COALESCE()` returns the first non-NULL value in a list. 
The `contacts` table has `mobile_phone`, `home_phone`, and `work_phone`. 

Write a query that returns the user's `name` and their **primary phone** (preferring mobile, then home, then work), ordered by name.

Expected output:
Alice 555-1111
Bob 555-2222
Carol 555-3333

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE contacts (name TEXT, mobile_phone TEXT, home_phone TEXT, work_phone TEXT)')
conn.executemany('INSERT INTO contacts VALUES (?,?,?,?)', [
    ('Alice', '555-1111', None, '555-9999'),
    ('Bob', None, '555-2222', '555-8888'),
    ('Carol', None, None, '555-3333')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
`CASE WHEN` allows if/else logic directly in your queries.
Given an `orders` table, categorize orders by amount: `>= 1000` is 'High', `>= 500` is 'Medium', else 'Low'.

Return `id` and the new `category` column, ordered by `id`.

Expected output:
1 High
2 Low
3 Medium

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?)', [(1,1200), (2,300), (3,500)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
String manipulation via `LIKE` and wildcards.
Find all users in the `users` table whose `username` starts with `'A'` and ends with `'n'`, and is exactly 5 characters long.

Expected output:
Aaron

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (username TEXT)')
conn.executemany('INSERT INTO users VALUES (?)', [('Admin',), ('Aaron',), ('Alan',), ('Adrian',)])
conn.commit()

# In SQLite, '_' matches a single character, '%' matches any sequence.
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q9
            [
                'order_index'         => 9,
                'problem_description' => <<<'MD'
Working with dates. In SQLite, you can format strings or extract parts using `strftime()`.
Find the total `amount` of sales made specifically in the year `2023`.

Expected output:
1500

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (amount INTEGER, sale_date TEXT)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (1000, '2023-05-10'), (500, '2023-11-20'), (800, '2022-12-01')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Advanced sorting: `ORDER BY` allows sorting on calculated columns or `CASE` statements.
Sort the `products` table so that products with `stock = 0` appear at the *very bottom*, and all others are sorted by `price` ascending.

Expected output:
Desk 100
Chair 150
Laptop 1200
Phone 800

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (name TEXT, price INTEGER, stock INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    ('Laptop', 1200, 5), ('Phone', 800, 0), ('Chair', 150, 10), ('Desk', 100, 2)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row[:2])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
Pagination logic is often implemented via `LIMIT` and `OFFSET`. 
Write a query to retrieve the 2nd page of results from the `users` table, assuming a page size of 3, ordered by `id` ascending.

Expected output:
4 Dave
5 Eve
6 Frank

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [
    (1,'Alice'),(2,'Bob'),(3,'Carol'),(4,'Dave'),(5,'Eve'),(6,'Frank'),(7,'Grace')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: Conditional Aggregation & Group Filtering (Q12–Q17)
            // ═══════════════════════════════════════════════════════════════

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
**Conditional Aggregation** pivots rows into columns. 
Use `SUM(CASE WHEN ... THEN amount ELSE 0 END)` to return a single row with three columns: Total `IT` salary, Total `HR` salary, Total `Sales` salary.

Expected output:
160000 95000 65000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (dept TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    ('IT', 70000), ('IT', 90000), ('HR', 45000), ('HR', 50000), ('Sales', 65000)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
Complex `HAVING` clauses filter grouped sets. 
Find the `customer_id` of customers who have made **more than 2 orders** AND whose **average order amount** is strictly greater than 500.

Expected output:
101

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (1,101,600), (2,101,700), (3,101,550),
    (4,102,800), (5,102,900),
    (6,103,400), (7,103,400), (8,103,400)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
Filtering within groupings via `COUNT(CASE...)`.
Return the `department` name and the count of employees earning `> 60000` in that department. Order by department name.

Expected output:
HR 0
IT 2
Sales 1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (dept TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    ('IT', 70000), ('IT', 90000), ('HR', 45000), ('HR', 50000), ('Sales', 65000)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
Grouping by multiple columns. 
Return the `year`, `month`, and `total_amount` of sales grouped by both year and month. Extract year and month using SQLite's string functions (`substr` or `strftime`). Order sequentially.

Expected output:
2023 01 1500
2023 02 800

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (amount INTEGER, sale_date TEXT)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (1000, '2023-01-15'), (500, '2023-01-20'), (800, '2023-02-10')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
Multi-level aggregations. Find the highest average department salary. 
*(Hint: Group by department to get the average, then order by that average descending and limit 1).*

Expected output:
80000.0

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (dept TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    ('IT', 70000), ('IT', 90000), ('HR', 40000), ('Sales', 60000)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
Aggregating distinct values dynamically. 
Find the number of **unique products** purchased per customer. Order alphabetically by customer ID.

Expected output:
1 2
2 1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (customer_id INTEGER, product_id INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?)', [
    (1, 101), (1, 101), (1, 102), (2, 105), (2, 105)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Complex JOINs (Q18–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
Multi-table JOINs with aggregations. 
Join `Customers`, `Orders`, and `OrderItems` to find the **total amount spent by 'Alice'**. (amount = price * qty).

Expected output:
2400

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO customers VALUES (?,?)', [(1,'Alice'),(2,'Bob')])
conn.execute('CREATE TABLE orders (id INTEGER, customer_id INTEGER)')
conn.executemany('INSERT INTO orders VALUES (?,?)', [(10,1), (11,2)])
conn.execute('CREATE TABLE order_items (order_id INTEGER, price INTEGER, qty INTEGER)')
conn.executemany('INSERT INTO order_items VALUES (?,?,?)', [
    (10, 1000, 2), (10, 400, 1), (11, 500, 1)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
Finding missing data using `LEFT JOIN` and `IS NULL`. 
Return the names of employees who have **not** been assigned to any project in the `project_assignments` table. Order alphabetically.

Expected output:
Eve

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [(1,'Dave'),(2,'Eve'),(3,'Frank')])
conn.execute('CREATE TABLE project_assignments (emp_id INTEGER, project_name TEXT)')
conn.executemany('INSERT INTO project_assignments VALUES (?,?)', [(1,'GovX Migration'), (3,'DataSensei UI')])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
**Self-Joins** for hierarchies. 
The `employees` table has a `manager_id` linking to another employee's `id`. 
Return each employee's name and their manager's name (format: `EmpName ManagerName`). Filter out employees who have no manager. Order by employee name.

Expected output:
Bob Alice
Carol Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (id INTEGER, name TEXT, manager_id INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    (1,'Alice',None), (2,'Bob',1), (3,'Carol',1)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
A **CROSS JOIN** generates a Cartesian product. 
Using `products` and `stores`, CROSS JOIN them to return every possible combination of `store_name` and `product_name`, ordered by store then product.

Expected output:
StoreA Apples
StoreA Bananas
StoreB Apples
StoreB Bananas

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE stores (store_name TEXT)')
conn.executemany('INSERT INTO stores VALUES (?)', [('StoreA',), ('StoreB',)])
conn.execute('CREATE TABLE products (product_name TEXT)')
conn.executemany('INSERT INTO products VALUES (?)', [('Apples',), ('Bananas',)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
Non-equi Joins (Inequality Joins). 
Join `events` and `promotions` to find which promotion was active during which event, where `event.date BETWEEN promo.start_date AND promo.end_date`.
Return `event_name` and `promo_name`, ordered by event.

Expected output:
Launch Weekend Sale

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE events (event_name TEXT, date TEXT)')
conn.executemany('INSERT INTO events VALUES (?,?)', [('Launch', '2023-05-15')])
conn.execute('CREATE TABLE promotions (promo_name TEXT, start_date TEXT, end_date TEXT)')
conn.executemany('INSERT INTO promotions VALUES (?,?,?)', [
    ('Weekend Sale', '2023-05-14', '2023-05-16'),
    ('Holiday Sale', '2023-12-01', '2023-12-31')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Join on Multiple Conditions. 
Join `students` to `grades` where BOTH `student_id` matches AND the `semester` is 'Fall'. Return the student name and grade, ordered alphabetically.

Expected output:
Alice A

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE students (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO students VALUES (?,?)', [(1,'Alice'),(2,'Bob')])
conn.execute('CREATE TABLE grades (student_id INTEGER, semester TEXT, grade TEXT)')
conn.executemany('INSERT INTO grades VALUES (?,?,?)', [
    (1,'Fall','A'), (1,'Spring','B'), (2,'Spring','A')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Intersection using INNER JOIN. Find users who are BOTH in the `newsletter_subscribers` table AND the `premium_members` table, utilizing a strict INNER JOIN on `email`. Order by email.

Expected output:
alice@mail.com

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE newsletter (email TEXT)')
conn.executemany('INSERT INTO newsletter VALUES (?)', [('alice@mail.com',),('bob@mail.com',)])
conn.execute('CREATE TABLE premium (email TEXT)')
conn.executemany('INSERT INTO premium VALUES (?)', [('alice@mail.com',),('charlie@mail.com',)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Correlated Subqueries & CTEs (Q25–Q30)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
A **Correlated Subquery** evaluates the inner query relative to the current row of the outer query. 
Find the names of employees whose salary is *greater than the average salary of their specific department*. Order alphabetically.

Expected output:
Frank

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, salary INTEGER, dept TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    ('Alice', 50000, 'IT'), ('Bob', 50000, 'IT'), ('Frank', 80000, 'IT'),
    ('Carol', 40000, 'HR'), ('Dave', 40000, 'HR')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
`EXISTS` is often faster than `IN` for checking subquery intersections. 
Write a query using `WHERE EXISTS` to find all departments in the `departments` table that have at least one employee in the `employees` table. Order alphabetically.

Expected output:
HR
IT

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE departments (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO departments VALUES (?,?)', [(1,'IT'),(2,'HR'),(3,'Marketing')])
conn.execute('CREATE TABLE employees (id INTEGER, dept_id INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [(10,1), (11,2)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
Using Multiple CTEs. Define two CTEs: `top_dept` (departments with total salary > 100k) and `high_earners` (employees > 60k). 
Join them to output the high-earning employee names in top departments. Order by name.

Expected output:
Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, salary INTEGER, dept TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    ('Alice', 80000, 'IT'), ('Bob', 30000, 'IT'), 
    ('Carol', 70000, 'HR')
])
conn.commit()

query = "WITH top_dept AS (...), high_earners AS (...) SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**Recursive CTEs** let you build hierarchical or sequential data. 
Write a recursive CTE to generate the numbers 1 through 5, and select them.

Expected output:
1
2
3
4
5

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')

# Use WITH RECURSIVE
query = "WITH RECURSIVE cnt(x) AS (SELECT 1 UNION ALL SELECT x+1 FROM cnt WHERE x<5) SELECT x FROM cnt;"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
Scalar Subqueries in the SELECT clause. 
For every employee, return their name, their salary, and the **company-wide maximum salary** as a third column. Order by name.

Expected output:
Alice 50000 80000
Bob 80000 80000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [('Alice', 50000), ('Bob', 80000)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
Updates with Subqueries. Update the `customers` table, setting `is_active = 1` for any customer who exists in the `recent_orders` table.
Print the active customers alphabetically.

Expected output:
Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE customers (id INTEGER, name TEXT, is_active INTEGER DEFAULT 0)')
conn.executemany('INSERT INTO customers VALUES (?,?,?)', [(1,'Alice',0), (2,'Bob',0)])
conn.execute('CREATE TABLE recent_orders (customer_id INTEGER)')
conn.executemany('INSERT INTO recent_orders VALUES (?)', [(1,)])
conn.commit()

conn.execute("UPDATE ...")

for row in conn.execute("SELECT name FROM customers WHERE is_active = 1 ORDER BY name"):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 300,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Advanced Window Functions (Q31–Q36)
            // ═══════════════════════════════════════════════════════════════

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
`DENSE_RANK()` vs `RANK()`. `DENSE_RANK` doesn't skip numbers after a tie. 
Return the employee name, salary, and their dense rank grouped/partitioned by `dept`, ordered by `salary` DESC. 
Order final output by `dept` and `drank`.

Expected output:
IT Frank 90000 1
IT Alice 80000 2
IT Bob 80000 2
IT Carol 70000 3

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, salary INTEGER, dept TEXT)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    ('Alice', 80000, 'IT'), ('Bob', 80000, 'IT'), ('Carol', 70000, 'IT'), ('Frank', 90000, 'IT')
])
conn.commit()

query = "SELECT dept, name, salary, DENSE_RANK() OVER (...) AS drank FROM employees ORDER BY dept, drank"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
`NTILE(n)` divides rows into `n` roughly equal buckets.
Divide the 4 products into 2 quartiles/buckets based on `price` DESC. 
Return name and bucket number, ordered by price DESC.

Expected output:
Laptop 1
Phone 1
Tablet 2
Desk 2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (name TEXT, price INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?)', [
    ('Laptop', 1200), ('Phone', 800), ('Tablet', 600), ('Desk', 200)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
**Moving Averages** use window frames.
Compute a moving sum of `amount` using `ROWS BETWEEN 1 PRECEDING AND CURRENT ROW` ordered by `day`.
Output day, amount, and moving sum.

Expected output:
1 10 10
2 20 30
3 30 50
4 40 70

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (day INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [(1,10), (2,20), (3,30), (4,40)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q34
            [
                'order_index'         => 34,
                'problem_description' => <<<'MD'
`FIRST_VALUE()` retrieves the first value in an ordered window.
For each row, show the `name`, `salary`, and the `salary` of the lowest-paid employee in the whole table (ordered ascending).

Expected output:
Alice 50000 40000
Bob 80000 40000
Carol 40000 40000

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?)', [
    ('Alice', 50000), ('Bob', 80000), ('Carol', 40000)
])
conn.commit()

query = "SELECT name, salary, FIRST_VALUE(salary) OVER(ORDER BY salary ASC) FROM employees ORDER BY name"
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q35
            [
                'order_index'         => 35,
                'problem_description' => <<<'MD'
Calculate year-over-year growth. Use `LAG()` to get the previous year's revenue, subtract it from the current year, and output `year` and `growth`. If previous year is null, output `0` using `COALESCE`. Order by year.

Expected output:
2021 0
2022 50
2023 -20

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE revenue (year INTEGER, amt INTEGER)')
conn.executemany('INSERT INTO revenue VALUES (?,?)', [(2021, 100), (2022, 150), (2023, 130)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q36
            [
                'order_index'         => 36,
                'problem_description' => <<<'MD'
Percent of Total within Partition.
Calculate each employee's salary as a percentage of their department's total salary: `(salary * 100.0) / SUM(salary) OVER(...)`.
Output `name`, `dept`, and `pct` (rounded to 1 decimal), ordered alphabetically by name.

Expected output:
Alice IT 60.0
Bob IT 40.0
Carol HR 100.0

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE employees (name TEXT, dept TEXT, salary INTEGER)')
conn.executemany('INSERT INTO employees VALUES (?,?,?)', [
    ('Alice', 'IT', 60000), ('Bob', 'IT', 40000), ('Carol', 'HR', 50000)
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: EAV & Dimensional Modeling (Q37–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
Entity-Attribute-Value (EAV) mapping. 
An `eav` table stores `entity_id`, `attribute`, and `value`. 
Find the `entity_id` of the item that has BOTH `color = blue` AND `size = L`.

Expected output:
2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE eav (entity_id INTEGER, attribute TEXT, value TEXT)')
conn.executemany('INSERT INTO eav VALUES (?,?,?)', [
    (1, 'color', 'red'), (1, 'size', 'L'),
    (2, 'color', 'blue'), (2, 'size', 'L'),
    (3, 'color', 'blue'), (3, 'size', 'S')
])
conn.commit()

# Use self-joins or conditional aggregation (HAVING COUNT)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
Pivoting EAV tables. 
Write a query using `MAX(CASE...)` to pivot the EAV data for `entity_id = 2` into columns: `entity_id, color, size`.

Expected output:
2 blue L

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE eav (entity_id INTEGER, attribute TEXT, value TEXT)')
conn.executemany('INSERT INTO eav VALUES (?,?,?)', [
    (2, 'color', 'blue'), (2, 'size', 'L')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Flattening a Star Schema. 
Join a `fact_sales` table to `dim_store` and `dim_date` to output the `store_name`, `date_string`, and `amount`. Order by `amount` DESC.

Expected output:
NY Store 2023-01-01 500

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE dim_store (store_id INTEGER, store_name TEXT)')
conn.executemany('INSERT INTO dim_store VALUES (?,?)', [(1, 'NY Store')])
conn.execute('CREATE TABLE dim_date (date_id INTEGER, date_string TEXT)')
conn.executemany('INSERT INTO dim_date VALUES (?,?)', [(100, '2023-01-01')])
conn.execute('CREATE TABLE fact_sales (store_id INTEGER, date_id INTEGER, amount INTEGER)')
conn.executemany('INSERT INTO fact_sales VALUES (?,?,?)', [(1, 100, 500)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
Resolving Many-to-Many ties. 
Given `students`, `courses`, and `enrollments`, output a single list of `student_name` and a concatenated string of their courses (using SQLite's `GROUP_CONCAT(course_name, ', ')`). Order by student name.

Expected output:
Alice Math, Science

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE students (id INTEGER, name TEXT)')
conn.executemany('INSERT INTO students VALUES (?,?)', [(1, 'Alice')])
conn.execute('CREATE TABLE courses (id INTEGER, course_name TEXT)')
conn.executemany('INSERT INTO courses VALUES (?,?)', [(10, 'Math'), (11, 'Science')])
conn.execute('CREATE TABLE enrollments (student_id INTEGER, course_id INTEGER)')
conn.executemany('INSERT INTO enrollments VALUES (?,?)', [(1, 10), (1, 11)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
Identifying Functional Dependency anomalies (3NF violation).
The `orders` table stores `customer_id`, `customer_name`, and `customer_city`. 
Write a query to find `customer_id`s that have conflicting (more than 1 distinct) `customer_city` entries across their orders.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (order_id INTEGER, customer_id INTEGER, customer_city TEXT)')
conn.executemany('INSERT INTO orders VALUES (?,?,?)', [
    (101, 1, 'NY'), (102, 1, 'LA'), (103, 2, 'Boston'), (104, 2, 'Boston')
])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 250,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Query Optimization & SARGable Predicates (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
SARGable (Search ARGument ABLE) predicates can use indexes. 
`WHERE SUBSTR(date, 1, 4) = '2023'` is NOT SARGable. 
Rewrite it using string bounds or `LIKE '2023%'` to count sales in 2023.

Expected output:
2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE sales (amount INTEGER, date TEXT)')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (100, '2023-01-05'), (200, '2023-11-20'), (300, '2022-12-31')
])
conn.commit()

# Use LIKE for SARGable prefix matching
query = "SELECT COUNT(*) FROM sales WHERE ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
Avoid math on indexed columns. `WHERE price * 0.9 < 100` ignores indexes on `price`. 
Rewrite the condition mathematically to isolate `price` (`price < 100 / 0.9`). 
Select the names of those products.

Expected output:
Pen

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (name TEXT, price INTEGER)')
conn.executemany('INSERT INTO products VALUES (?,?)', [('Desk', 150), ('Pen', 10)])
conn.commit()

query = "SELECT name FROM products WHERE price < (100 / 0.9)"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
Using a Covering Index. If an index is `(category, status)`, a query filtering on BOTH can use it perfectly.
Select `count(*)` of products where `category = 'Electronics'` AND `status = 'Active'`.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE products (id INTEGER, category TEXT, status TEXT)')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1, 'Electronics', 'Active'), (2, 'Electronics', 'Inactive'), (3, 'Home', 'Active')
])
conn.execute('CREATE INDEX idx_cat_stat ON products(category, status)')
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
Avoiding `SELECT *` in production code. 
Only retrieve the `email` column for active users (`is_active = 1`).

Expected output:
test@mail.com

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (id INTEGER, email TEXT, is_active INTEGER)')
conn.executemany('INSERT INTO users VALUES (?,?,?)', [(1, 'test@mail.com', 1), (2, 'old@mail.com', 0)])
conn.commit()

query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
Boolean vs integer filtering optimization. SQLite stores booleans as `0` and `1`. 
Find the sum of `amount` where `is_paid = 1`.

Expected output:
300

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE invoices (amount INTEGER, is_paid INTEGER)')
conn.executemany('INSERT INTO invoices VALUES (?,?)', [(300, 1), (200, 0)])
conn.commit()

query = "SELECT SUM(amount) FROM invoices WHERE is_paid = 1"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 200,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 10: JSON in Relational Databases (SQLite JSON1) (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
SQLite's `json_extract(column, '$.path')` (or `column ->> '$.path'`) extracts JSON data.
The `users` table has a `profile` JSON column. 
Extract the `$.age` property and return names of users strictly older than 25, ordered alphabetically.

Expected output:
Alice

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE users (name TEXT, profile TEXT)')
conn.executemany('INSERT INTO users VALUES (?,?)', [
    ('Alice', '{"age": 30}'), ('Bob', '{"age": 20}')
])
conn.commit()

query = "SELECT name FROM users WHERE json_extract(profile, '$.age') > 25"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Aggregating over extracted JSON.
Sum the extracted `$.price` from the `items` JSON column across all rows.

Expected output:
150

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE orders (items TEXT)')
conn.executemany('INSERT INTO orders VALUES (?)', [
    ('{"price": 100}',), ('{"price": 50}',)
])
conn.commit()

query = "SELECT SUM(json_extract(items, '$.price')) FROM orders"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
JSON Array Length. Extract the length of a JSON array using `json_array_length()`.
Find the `id` of rows where the `$.tags` array has more than 1 item.

Expected output:
1

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE posts (id INTEGER, metadata TEXT)')
conn.executemany('INSERT INTO posts VALUES (?,?)', [
    (1, '{"tags": ["sql", "db"]}'), (2, '{"tags": ["sql"]}')
])
conn.commit()

query = "SELECT id FROM posts WHERE json_array_length(metadata, '$.tags') > 1"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
Filtering nested JSON objects. Extract `$.address.city` and return the `id` of rows where the city is `'Cebu'`.

Expected output:
2

MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('CREATE TABLE locations (id INTEGER, data TEXT)')
conn.executemany('INSERT INTO locations VALUES (?,?)', [
    (1, '{"address": {"city": "Manila"}}'), 
    (2, '{"address": {"city": "Cebu"}}')
])
conn.commit()

query = "SELECT id FROM locations WHERE json_extract(data, '$.address.city') = 'Cebu'"
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 350,
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

        // Note: For deterministic intermediate queries that don't rely on dynamic input logic,
        // we replicate the static expected output across the cases just like the base seeder template.

        $seed(1, [
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(2, [
            ['input' => null, 'expected_output' => "admin@test.com", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "admin@test.com", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "admin@test.com", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "admin@test.com", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(3, [
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2\n4", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(4, [
            ['input' => null, 'expected_output' => "Intro to Magic", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Intro to Magic", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Intro to Magic", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Intro to Magic", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(5, [
            ['input' => null, 'expected_output' => "150000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "150000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "150000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "150000", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(6, [
            ['input' => null, 'expected_output' => "Alice 555-1111\nBob 555-2222\nCarol 555-3333", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 555-1111\nBob 555-2222\nCarol 555-3333", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 555-1111\nBob 555-2222\nCarol 555-3333", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 555-1111\nBob 555-2222\nCarol 555-3333", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(7, [
            ['input' => null, 'expected_output' => "1 High\n2 Low\n3 Medium", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 High\n2 Low\n3 Medium", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 High\n2 Low\n3 Medium", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 High\n2 Low\n3 Medium", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(8, [
            ['input' => null, 'expected_output' => "Aaron", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Aaron", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Aaron", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Aaron", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(9, [
            ['input' => null, 'expected_output' => "1500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1500", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1500", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(10, [
            ['input' => null, 'expected_output' => "Desk 100\nChair 150\nLaptop 1200\nPhone 800", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Desk 100\nChair 150\nLaptop 1200\nPhone 800", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Desk 100\nChair 150\nLaptop 1200\nPhone 800", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Desk 100\nChair 150\nLaptop 1200\nPhone 800", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(11, [
            ['input' => null, 'expected_output' => "4 Dave\n5 Eve\n6 Frank", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "4 Dave\n5 Eve\n6 Frank", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "4 Dave\n5 Eve\n6 Frank", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "4 Dave\n5 Eve\n6 Frank", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(12, [
            ['input' => null, 'expected_output' => "160000 95000 65000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "160000 95000 65000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "160000 95000 65000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "160000 95000 65000", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(13, [
            ['input' => null, 'expected_output' => "101", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "101", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "101", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "101", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(14, [
            ['input' => null, 'expected_output' => "HR 0\nIT 2\nSales 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR 0\nIT 2\nSales 1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR 0\nIT 2\nSales 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR 0\nIT 2\nSales 1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(15, [
            ['input' => null, 'expected_output' => "2023 01 1500\n2023 02 800", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2023 01 1500\n2023 02 800", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2023 01 1500\n2023 02 800", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2023 01 1500\n2023 02 800", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(16, [
            ['input' => null, 'expected_output' => "80000.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "80000.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "80000.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "80000.0", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(17, [
            ['input' => null, 'expected_output' => "1 2\n2 1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 2\n2 1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 2\n2 1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 2\n2 1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(18, [
            ['input' => null, 'expected_output' => "2400", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2400", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2400", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2400", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(19, [
            ['input' => null, 'expected_output' => "Eve", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Eve", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Eve", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Eve", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(20, [
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Bob Alice\nCarol Alice", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(21, [
            ['input' => null, 'expected_output' => "StoreA Apples\nStoreA Bananas\nStoreB Apples\nStoreB Bananas", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "StoreA Apples\nStoreA Bananas\nStoreB Apples\nStoreB Bananas", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "StoreA Apples\nStoreA Bananas\nStoreB Apples\nStoreB Bananas", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "StoreA Apples\nStoreA Bananas\nStoreB Apples\nStoreB Bananas", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(22, [
            ['input' => null, 'expected_output' => "Launch Weekend Sale", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Launch Weekend Sale", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Launch Weekend Sale", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Launch Weekend Sale", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(23, [
            ['input' => null, 'expected_output' => "Alice A", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice A", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice A", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice A", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(24, [
            ['input' => null, 'expected_output' => "alice@mail.com", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "alice@mail.com", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "alice@mail.com", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "alice@mail.com", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(25, [
            ['input' => null, 'expected_output' => "Frank", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Frank", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Frank", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Frank", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(26, [
            ['input' => null, 'expected_output' => "HR\nIT", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "HR\nIT", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "HR\nIT", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "HR\nIT", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(27, [
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(28, [
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1\n2\n3\n4\n5", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(29, [
            ['input' => null, 'expected_output' => "Alice 50000 80000\nBob 80000 80000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 50000 80000\nBob 80000 80000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 50000 80000\nBob 80000 80000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 50000 80000\nBob 80000 80000", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(30, [
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(31, [
            ['input' => null, 'expected_output' => "IT Frank 90000 1\nIT Alice 80000 2\nIT Bob 80000 2\nIT Carol 70000 3", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "IT Frank 90000 1\nIT Alice 80000 2\nIT Bob 80000 2\nIT Carol 70000 3", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "IT Frank 90000 1\nIT Alice 80000 2\nIT Bob 80000 2\nIT Carol 70000 3", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "IT Frank 90000 1\nIT Alice 80000 2\nIT Bob 80000 2\nIT Carol 70000 3", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(32, [
            ['input' => null, 'expected_output' => "Laptop 1\nPhone 1\nTablet 2\nDesk 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1\nPhone 1\nTablet 2\nDesk 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1\nPhone 1\nTablet 2\nDesk 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1\nPhone 1\nTablet 2\nDesk 2", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(33, [
            ['input' => null, 'expected_output' => "1 10 10\n2 20 30\n3 30 50\n4 40 70", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 10 10\n2 20 30\n3 30 50\n4 40 70", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 10 10\n2 20 30\n3 30 50\n4 40 70", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 10 10\n2 20 30\n3 30 50\n4 40 70", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(34, [
            ['input' => null, 'expected_output' => "Alice 50000 40000\nBob 80000 40000\nCarol 40000 40000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice 50000 40000\nBob 80000 40000\nCarol 40000 40000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice 50000 40000\nBob 80000 40000\nCarol 40000 40000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice 50000 40000\nBob 80000 40000\nCarol 40000 40000", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(35, [
            ['input' => null, 'expected_output' => "2021 0\n2022 50\n2023 -20", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2021 0\n2022 50\n2023 -20", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2021 0\n2022 50\n2023 -20", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2021 0\n2022 50\n2023 -20", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(36, [
            ['input' => null, 'expected_output' => "Alice IT 60.0\nBob IT 40.0\nCarol HR 100.0", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice IT 60.0\nBob IT 40.0\nCarol HR 100.0", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice IT 60.0\nBob IT 40.0\nCarol HR 100.0", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice IT 60.0\nBob IT 40.0\nCarol HR 100.0", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(37, [
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(38, [
            ['input' => null, 'expected_output' => "2 blue L", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2 blue L", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2 blue L", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2 blue L", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(39, [
            ['input' => null, 'expected_output' => "NY Store 2023-01-01 500", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "NY Store 2023-01-01 500", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "NY Store 2023-01-01 500", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "NY Store 2023-01-01 500", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(40, [
            ['input' => null, 'expected_output' => "Alice Math, Science", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice Math, Science", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice Math, Science", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice Math, Science", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(41, [
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(42, [
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(43, [
            ['input' => null, 'expected_output' => "Pen", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Pen", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Pen", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Pen", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(44, [
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(45, [
            ['input' => null, 'expected_output' => "test@mail.com", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "test@mail.com", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "test@mail.com", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "test@mail.com", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(46, [
            ['input' => null, 'expected_output' => "300", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "300", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "300", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "300", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(47, [
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(48, [
            ['input' => null, 'expected_output' => "150", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "150", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "150", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "150", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(49, [
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1", 'is_hidden' => true,  'order_index' => 4],
        ]);
        $seed(50, [
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 10 Coding (Intermediate) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}