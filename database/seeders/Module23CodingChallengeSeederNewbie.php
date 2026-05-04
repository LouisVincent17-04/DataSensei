<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ChallengeCategory;
use App\Models\Challenge;

/**
 * Module 23 — Data Warehousing (Newbie) — CODING variant
 *
 * Seeds in one pass:
 *   1. challenges          — one coding challenge for the Newbie tier
 *   2. coding_questions    — 50 questions covering all DW topics
 *   3. test_cases          — 4 cases per question (2 visible + 2 hidden)
 *
 * Topics covered:
 *   - What Is a Data Warehouse?
 *   - Dimensional Modelling: Star and Snowflake Schemas
 *   - ETL vs ELT: Data Integration Pipelines
 *   - Slowly Changing Dimensions (SCDs)
 *   - Data Vault Modelling
 *   - Columnar Storage and Query Optimisation
 *   - Cloud Data Warehouses: Snowflake, BigQuery, Redshift
 *   - Data Marts and the Kimball Bus Architecture
 *   - dbt: The Data Build Tool
 *   - Data Quality, Governance, and the Modern Data Stack
 *
 * All tasks run in Python using the built-in sqlite3 module so they
 * execute on the same Python judge as the rest of DataSensei.
 *
 * Safe to re-run: each section is guarded by an existence check.
 */
class Module23CodingChallengeSeederNewbie extends Seeder
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

        $this->command->info('Creating Module 23 — Data Warehousing (Newbie) [Coding]...');

        $challenge = Challenge::firstOrCreate(
            [
                'challenge_category_id' => $category->id,
                'title'                 => 'Data Warehousing',
                'is_coding_challenge'   => 1,
            ],
            [
                'description'        => 'Apply data warehousing concepts through hands-on Python tasks — query fact and dimension tables, perform star and snowflake schema joins, build basic ETL pipelines, work with slowly changing dimensions, run window functions, and validate data quality using Python\'s built-in sqlite3 module. Each task sets up a small in-memory database for you; your job is to write the right SQL or Python logic.',
                'time_limit_seconds' => 900,
                'base_xp'            => 500,
                'order_index'        => 23,
            ]
        );

        // ─────────────────────────────────────────────────────────────────
        // 2. CODING QUESTIONS (50 total)
        // ─────────────────────────────────────────────────────────────────

        $this->command->info('Seeding 50 coding questions...');

        $questionDefs = [

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 1: What Is a Data Warehouse? (Q1–Q5)
            // ═══════════════════════════════════════════════════════════════

            // Q1
            [
                'order_index'         => 1,
                'problem_description' => <<<'MD'
A data warehouse is a central repository that stores large volumes of structured data for analysis and reporting.

The table `sales_fact` below holds individual sales transactions:

| sale_id | product | region | amount |
|---------|---------|--------|--------|
| 1       | Laptop  | North  | 1200   |
| 2       | Phone   | South  | 800    |
| 3       | Laptop  | East   | 1500   |
| 4       | Tablet  | North  | 600    |
| 5       | Phone   | West   | 900    |

Write a SQL query that returns the **total number of rows** in `sales_fact`.

Expected output:
```
5
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,'Laptop','North',1200),
    (2,'Phone','South',800),
    (3,'Laptop','East',1500),
    (4,'Tablet','North',600),
    (5,'Phone','West',900),
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
Using the same `sales_fact` table:

| sale_id | product | region | amount |
|---------|---------|--------|--------|
| 1       | Laptop  | North  | 1200   |
| 2       | Phone   | South  | 800    |
| 3       | Laptop  | East   | 1500   |
| 4       | Tablet  | North  | 600    |
| 5       | Phone   | West   | 900    |

Write a SQL query that returns the **total revenue** (sum of all `amount` values).

Expected output:
```
5000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,'Laptop','North',1200),
    (2,'Phone','South',800),
    (3,'Laptop','East',1500),
    (4,'Tablet','North',600),
    (5,'Phone','West',900),
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
Using the same `sales_fact` table, write a SQL query that returns the **number of distinct products** sold.

| sale_id | product | region | amount |
|---------|---------|--------|--------|
| 1       | Laptop  | North  | 1200   |
| 2       | Phone   | South  | 800    |
| 3       | Laptop  | East   | 1500   |
| 4       | Tablet  | North  | 600    |
| 5       | Phone   | West   | 900    |

Expected output:
```
3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,'Laptop','North',1200),
    (2,'Phone','South',800),
    (3,'Laptop','East',1500),
    (4,'Tablet','North',600),
    (5,'Phone','West',900),
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

            // Q4
            [
                'order_index'         => 4,
                'problem_description' => <<<'MD'
Using the same `sales_fact` table, write a SQL query that returns the **highest single sale amount**.

Expected output:
```
1500
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,'Laptop','North',1200),
    (2,'Phone','South',800),
    (3,'Laptop','East',1500),
    (4,'Tablet','North',600),
    (5,'Phone','West',900),
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

            // Q5
            [
                'order_index'         => 5,
                'problem_description' => <<<'MD'
Using the same `sales_fact` table, write a SQL query that returns the **average sale amount**.

Expected output:
```
1000.0
```

Hint: SQLite's `AVG()` returns a float.
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,'Laptop','North',1200),
    (2,'Phone','South',800),
    (3,'Laptop','East',1500),
    (4,'Tablet','North',600),
    (5,'Phone','West',900),
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
            // TOPIC 2: Dimensional Modelling — Star & Snowflake (Q6–Q12)
            // ═══════════════════════════════════════════════════════════════

            // Q6
            [
                'order_index'         => 6,
                'problem_description' => <<<'MD'
In a **star schema**, a central fact table links to surrounding dimension tables.

Here `sales_fact` stores foreign keys, and `product_dim` stores product details:

**sales_fact**
| sale_id | product_id | region_id | amount |
|---------|-----------|-----------|--------|
| 1       | 1         | 1         | 1200   |
| 2       | 2         | 2         | 800    |
| 3       | 1         | 3         | 1500   |
| 4       | 3         | 1         | 600    |
| 5       | 2         | 4         | 900    |

**product_dim**
| product_id | product_name | category    |
|-----------|-------------|-------------|
| 1         | Laptop      | Electronics |
| 2         | Phone       | Electronics |
| 3         | Tablet      | Accessories |

Join the two tables and print the **total amount per product name**, ordered alphabetically by product name.

Expected output:
```
Laptop 2700
Phone 1700
Tablet 600
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, category TEXT
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop','Electronics'),(2,'Phone','Electronics'),(3,'Tablet','Accessories'),
])
conn.commit()

# Write your SQL query below (join sales_fact with product_dim)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q7
            [
                'order_index'         => 7,
                'problem_description' => <<<'MD'
Using the star schema from the previous question, a `region_dim` table has also been added:

**region_dim**
| region_id | region_name |
|-----------|------------|
| 1         | North      |
| 2         | South      |
| 3         | East       |
| 4         | West       |

Read a `region_name` from input and print the **total sales amount** for that region.

Example:
```
Input:  North
Output: 1800
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE region_dim (
    region_id INTEGER, region_name TEXT
)''')
conn.executemany('INSERT INTO region_dim VALUES (?,?)', [
    (1,'North'),(2,'South'),(3,'East'),(4,'West'),
])
conn.commit()

region = input()
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [region]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q8
            [
                'order_index'         => 8,
                'problem_description' => <<<'MD'
Using the star schema (same `sales_fact` and `product_dim` as Q6), print the **number of sales transactions per product category**, ordered alphabetically by category.

Expected output:
```
Accessories 1
Electronics 4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, category TEXT
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop','Electronics'),(2,'Phone','Electronics'),(3,'Tablet','Accessories'),
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
Using the same star schema, read a `sale_id` from input and print the **product name** and **amount** for that sale, separated by a space.

Example:
```
Input:  1
Output: Laptop 1200
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, category TEXT
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop','Electronics'),(2,'Phone','Electronics'),(3,'Tablet','Accessories'),
])
conn.commit()

sale_id = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [sale_id]):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q10
            [
                'order_index'         => 10,
                'problem_description' => <<<'MD'
Using both `sales_fact`, `product_dim`, and `region_dim`, print the **total sales amount per region**, ordered alphabetically by region name.

Expected output:
```
East 1500
North 1800
South 800
West 900
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, category TEXT
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop','Electronics'),(2,'Phone','Electronics'),(3,'Tablet','Accessories'),
])
conn.execute('''CREATE TABLE region_dim (
    region_id INTEGER, region_name TEXT
)''')
conn.executemany('INSERT INTO region_dim VALUES (?,?)', [
    (1,'North'),(2,'South'),(3,'East'),(4,'West'),
])
conn.commit()

# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q11
            [
                'order_index'         => 11,
                'problem_description' => <<<'MD'
A **snowflake schema** normalises dimension tables into multiple levels. Here, products link to subcategories, which link to top-level categories:

**product_dim** → **subcategory_dim** → **category_dim**

| product_id | product_name | subcategory_id |
|-----------|-------------|---------------|
| 1         | Laptop      | 1             |
| 2         | Phone       | 2             |
| 3         | Tablet      | 3             |

| subcategory_id | subcategory_name | category_id |
|---------------|-----------------|-------------|
| 1             | Computers       | 1           |
| 2             | Mobile          | 1           |
| 3             | Personal        | 2           |

| category_id | category_name |
|-------------|--------------|
| 1           | Electronics  |
| 2           | Accessories  |

Using the same `sales_fact`, join through all three levels and print the **total amount per top-level category**, ordered alphabetically.

Expected output:
```
Accessories 600
Electronics 4400
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, subcategory_id INTEGER
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop',1),(2,'Phone',2),(3,'Tablet',3),
])
conn.execute('''CREATE TABLE subcategory_dim (
    subcategory_id INTEGER, subcategory_name TEXT, category_id INTEGER
)''')
conn.executemany('INSERT INTO subcategory_dim VALUES (?,?,?)', [
    (1,'Computers',1),(2,'Mobile',1),(3,'Personal',2),
])
conn.execute('''CREATE TABLE category_dim (
    category_id INTEGER, category_name TEXT
)''')
conn.executemany('INSERT INTO category_dim VALUES (?,?)', [
    (1,'Electronics'),(2,'Accessories'),
])
conn.commit()

# Write your SQL query below (join through all three dimension levels)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q12
            [
                'order_index'         => 12,
                'problem_description' => <<<'MD'
Using the snowflake schema from Q11, read a **category name** from input and print the **total sales amount** for that category.

Example:
```
Input:  Electronics
Output: 4400
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product_id INTEGER, region_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?)', [
    (1,1,1,1200),(2,2,2,800),(3,1,3,1500),(4,3,1,600),(5,2,4,900),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, product_name TEXT, subcategory_id INTEGER
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?,?)', [
    (1,'Laptop',1),(2,'Phone',2),(3,'Tablet',3),
])
conn.execute('''CREATE TABLE subcategory_dim (
    subcategory_id INTEGER, subcategory_name TEXT, category_id INTEGER
)''')
conn.executemany('INSERT INTO subcategory_dim VALUES (?,?,?)', [
    (1,'Computers',1),(2,'Mobile',1),(3,'Personal',2),
])
conn.execute('''CREATE TABLE category_dim (
    category_id INTEGER, category_name TEXT
)''')
conn.executemany('INSERT INTO category_dim VALUES (?,?)', [
    (1,'Electronics'),(2,'Accessories'),
])
conn.commit()

category = input()
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [category]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 3: ETL vs ELT — Data Integration Pipelines (Q13–Q18)
            // ═══════════════════════════════════════════════════════════════

            // Q13
            [
                'order_index'         => 13,
                'problem_description' => <<<'MD'
In an ETL pipeline, a common first step is to **filter** raw records for quality before loading.

The `staging` table contains raw sales records with a validity flag:

| id | product       | amount | is_valid |
|----|--------------|--------|----------|
| 1  | valid_laptop | 1200   | 1        |
| 2  | bad_record   | 0      | 0        |
| 3  | valid_phone  | 800    | 1        |
| 4  | valid_tablet | 600    | 1        |
| 5  | bad_record   | 0      | 0        |

Write a SQL query that counts how many records have `is_valid = 1`.

Expected output:
```
3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE staging (
    id INTEGER, product TEXT, amount INTEGER, is_valid INTEGER
)''')
conn.executemany('INSERT INTO staging VALUES (?,?,?,?)', [
    (1,'valid_laptop',1200,1),
    (2,'bad_record',0,0),
    (3,'valid_phone',800,1),
    (4,'valid_tablet',600,1),
    (5,'bad_record',0,0),
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

            // Q14
            [
                'order_index'         => 14,
                'problem_description' => <<<'MD'
During ETL, raw data often arrives with amounts stored as **text** instead of numbers. Casting them allows arithmetic.

The `raw_orders` table stores amounts as TEXT:

| id | amount_text |
|----|------------|
| 1  | 1200       |
| 2  | 800        |
| 3  | 1500       |

Write a SQL query that **casts** each `amount_text` to INTEGER and prints their **total sum**.

Expected output:
```
3500
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE raw_orders (
    id INTEGER, amount_text TEXT
)''')
conn.executemany('INSERT INTO raw_orders VALUES (?,?)', [
    (1,'1200'),(2,'800'),(3,'1500'),
])
conn.commit()

# Write your SQL query below (use CAST)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q15
            [
                'order_index'         => 15,
                'problem_description' => <<<'MD'
In ETL pipelines, `NULL` values must be handled before loading. `COALESCE` replaces `NULL` with a default.

The `orders` table has some missing amounts:

| id | amount |
|----|--------|
| 1  | 100    |
| 2  | NULL   |
| 3  | 200    |
| 4  | NULL   |
| 5  | 50     |

Write a SQL query that prints the **sum of amounts**, treating each `NULL` as `0` using `COALESCE`.

Expected output:
```
350
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO orders VALUES (?,?)', [
    (1,100),(2,None),(3,200),(4,None),(5,50),
])
conn.commit()

# Write your SQL query below (use COALESCE to handle NULLs)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q16
            [
                'order_index'         => 16,
                'problem_description' => <<<'MD'
A common ETL transformation is **standardising text** — for example, converting product names to uppercase before loading into the warehouse.

The `products` table holds raw names in lowercase:

| id | name   |
|----|--------|
| 1  | laptop |
| 2  | phone  |
| 3  | tablet |

Write a SQL query that prints each product name in **uppercase**, one per line, ordered by `id`.

Expected output:
```
LAPTOP
PHONE
TABLET
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (id INTEGER, name TEXT)''')
conn.executemany('INSERT INTO products VALUES (?,?)', [
    (1,'laptop'),(2,'phone'),(3,'tablet'),
])
conn.commit()

# Write your SQL query below (use UPPER)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q17
            [
                'order_index'         => 17,
                'problem_description' => <<<'MD'
ETL pipelines often extract date parts from timestamp columns for use in date dimensions.

The `sales` table stores dates as text in `YYYY-MM-DD` format:

| id | sale_date  |
|----|-----------|
| 1  | 2021-03-15|
| 2  | 2022-07-20|
| 3  | 2023-01-10|

Read a `sale_id` from input and print the **year** extracted from that sale's date.

Example:
```
Input:  1
Output: 2021
```

Hint: use `SUBSTR(sale_date, 1, 4)` or `strftime('%Y', sale_date)`.
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales (id INTEGER, sale_date TEXT)''')
conn.executemany('INSERT INTO sales VALUES (?,?)', [
    (1,'2021-03-15'),(2,'2022-07-20'),(3,'2023-01-10'),
])
conn.commit()

sale_id = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [sale_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q18
            [
                'order_index'         => 18,
                'problem_description' => <<<'MD'
The final **Load** step in an ETL pipeline moves cleaned records from a staging table into a target table.

The `staging` table has the following records (only `is_valid = 1` rows should be loaded):

| id | product       | amount | is_valid |
|----|--------------|--------|----------|
| 1  | valid_laptop | 1200   | 1        |
| 2  | bad_record   | 0      | 0        |
| 3  | valid_phone  | 800    | 1        |
| 4  | valid_tablet | 600    | 1        |

Write Python code that inserts only valid rows from `staging` into the empty `target` table, then prints the **count of rows** in `target`.

Expected output:
```
3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE staging (
    id INTEGER, product TEXT, amount INTEGER, is_valid INTEGER
)''')
conn.executemany('INSERT INTO staging VALUES (?,?,?,?)', [
    (1,'valid_laptop',1200,1),
    (2,'bad_record',0,0),
    (3,'valid_phone',800,1),
    (4,'valid_tablet',600,1),
])
conn.execute('''CREATE TABLE target (
    id INTEGER, product TEXT, amount INTEGER
)''')
conn.commit()

# Write your solution below
# Insert valid rows from staging into target, then print the count in target
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 4: Slowly Changing Dimensions (Q19–Q24)
            // ═══════════════════════════════════════════════════════════════

            // Q19
            [
                'order_index'         => 19,
                'problem_description' => <<<'MD'
A **Slowly Changing Dimension (SCD) Type 2** keeps full history by adding a new row whenever an attribute changes, and marking old rows with `is_current = 0`.

The `customer_scd` table:

| id | customer_key | customer_name | city      | effective_date | expiry_date | is_current |
|----|-------------|--------------|-----------|---------------|------------|-----------|
| 1  | 101         | Alice         | New York  | 2020-01-01    | 2022-06-30 | 0         |
| 2  | 101         | Alice         | Boston    | 2022-07-01    | 9999-12-31 | 1         |
| 3  | 102         | Bob           | Chicago   | 2021-03-01    | 9999-12-31 | 1         |
| 4  | 103         | Carol         | Seattle   | 2019-05-01    | 2021-12-31 | 0         |
| 5  | 103         | Carol         | Portland  | 2022-01-01    | 9999-12-31 | 1         |

Write a SQL query that counts the number of **current** records (`is_current = 1`).

Expected output:
```
3
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
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

            // Q20
            [
                'order_index'         => 20,
                'problem_description' => <<<'MD'
Using the same `customer_scd` table, write a SQL query that lists the **customer names** of all **expired** records (`is_current = 0`), ordered alphabetically.

Expected output:
```
Alice
Carol
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
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

            // Q21
            [
                'order_index'         => 21,
                'problem_description' => <<<'MD'
Using the same `customer_scd` table, read a `customer_key` from input and print the **customer_name** and **city** of the **most recent** version (the current row).

Example:
```
Input:  101
Output: Alice Boston
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
])
conn.commit()

customer_key = int(input())
# Write your SQL query below (filter by customer_key and is_current)
query = "SELECT ..."
for row in conn.execute(query, [customer_key]):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q22
            [
                'order_index'         => 22,
                'problem_description' => <<<'MD'
To look up a customer's data **as of a specific date**, filter for the row where `effective_date <= check_date <= expiry_date`.

Read a `customer_key` and a `check_date` from input (one per line) and print the **customer_name** and **city** that were active on that date.

Example:
```
Input:
101
2021-01-01
Output: Alice New York
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
])
conn.commit()

customer_key = int(input())
check_date = input()
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [customer_key, check_date, check_date]):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q23
            [
                'order_index'         => 23,
                'problem_description' => <<<'MD'
Using the same `customer_scd` table, read a `customer_key` from input and print the **total number of versions** (rows) for that customer.

Example:
```
Input:  101
Output: 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
])
conn.commit()

customer_key = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [customer_key]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q24
            [
                'order_index'         => 24,
                'problem_description' => <<<'MD'
Using the same `customer_scd` table, find all **customer keys that have more than one version** (meaning they changed at least once). Print them in ascending order.

Expected output:
```
101
103
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_scd (
    id INTEGER, customer_key INTEGER, customer_name TEXT,
    city TEXT, effective_date TEXT, expiry_date TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_scd VALUES (?,?,?,?,?,?,?)', [
    (1,101,'Alice','New York','2020-01-01','2022-06-30',0),
    (2,101,'Alice','Boston','2022-07-01','9999-12-31',1),
    (3,102,'Bob','Chicago','2021-03-01','9999-12-31',1),
    (4,103,'Carol','Seattle','2019-05-01','2021-12-31',0),
    (5,103,'Carol','Portland','2022-01-01','9999-12-31',1),
])
conn.commit()

# Write your SQL query below (use GROUP BY + HAVING)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 5: Data Vault Modelling (Q25–Q27)
            // ═══════════════════════════════════════════════════════════════

            // Q25
            [
                'order_index'         => 25,
                'problem_description' => <<<'MD'
A **Data Vault Hub** stores unique business keys and metadata about when each key was first loaded.

The `customer_hub` table:

| hub_id | business_key | load_date  | record_source |
|--------|-------------|-----------|--------------|
| 1      | C001        | 2023-01-01 | CRM         |
| 2      | C002        | 2023-01-02 | CRM         |
| 3      | C003        | 2023-01-03 | ERP         |

Write a SQL query that prints all **business keys**, ordered alphabetically.

Expected output:
```
C001
C002
C003
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_hub (
    hub_id INTEGER, business_key TEXT, load_date TEXT, record_source TEXT
)''')
conn.executemany('INSERT INTO customer_hub VALUES (?,?,?,?)', [
    (1,'C001','2023-01-01','CRM'),
    (2,'C002','2023-01-02','CRM'),
    (3,'C003','2023-01-03','ERP'),
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

            // Q26
            [
                'order_index'         => 26,
                'problem_description' => <<<'MD'
A **Data Vault Link** records relationships between hubs. Here, `customer_order_link` connects customers to their orders.

**customer_hub**
| hub_id | business_key |
|--------|-------------|
| 1      | C001        |
| 2      | C002        |
| 3      | C003        |

**customer_order_link**
| link_id | customer_hub_id | order_id |
|---------|----------------|---------|
| 1       | 1              | 1001    |
| 2       | 1              | 1002    |
| 3       | 2              | 1003    |

Read a **business key** from input and print all `order_id` values linked to that customer, ordered ascending.

Example:
```
Input:  C001
Output:
1001
1002
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_hub (
    hub_id INTEGER, business_key TEXT, load_date TEXT, record_source TEXT
)''')
conn.executemany('INSERT INTO customer_hub VALUES (?,?,?,?)', [
    (1,'C001','2023-01-01','CRM'),
    (2,'C002','2023-01-02','CRM'),
    (3,'C003','2023-01-03','ERP'),
])
conn.execute('''CREATE TABLE customer_order_link (
    link_id INTEGER, customer_hub_id INTEGER, order_id INTEGER, load_date TEXT
)''')
conn.executemany('INSERT INTO customer_order_link VALUES (?,?,?,?)', [
    (1,1,1001,'2023-02-01'),
    (2,1,1002,'2023-02-15'),
    (3,2,1003,'2023-03-01'),
])
conn.commit()

business_key = input()
# Write your SQL query below (join hub + link)
query = "SELECT ..."
for row in conn.execute(query, [business_key]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q27
            [
                'order_index'         => 27,
                'problem_description' => <<<'MD'
A **Data Vault Satellite** stores descriptive attributes for a hub entity. Join the hub with the satellite to retrieve customer details.

**customer_sat**
| sat_id | hub_id | customer_name | email               | is_current |
|--------|--------|--------------|--------------------|-----------:|
| 1      | 1      | Alice         | alice@example.com  | 1         |
| 2      | 2      | Bob           | bob@example.com    | 1         |
| 3      | 3      | Carol         | carol@example.com  | 1         |

Read a **business key** from input and print the **customer name**.

Example:
```
Input:  C002
Output: Bob
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customer_hub (
    hub_id INTEGER, business_key TEXT, load_date TEXT, record_source TEXT
)''')
conn.executemany('INSERT INTO customer_hub VALUES (?,?,?,?)', [
    (1,'C001','2023-01-01','CRM'),
    (2,'C002','2023-01-02','CRM'),
    (3,'C003','2023-01-03','ERP'),
])
conn.execute('''CREATE TABLE customer_sat (
    sat_id INTEGER, hub_id INTEGER, customer_name TEXT,
    email TEXT, is_current INTEGER
)''')
conn.executemany('INSERT INTO customer_sat VALUES (?,?,?,?,?)', [
    (1,1,'Alice','alice@example.com',1),
    (2,2,'Bob','bob@example.com',1),
    (3,3,'Carol','carol@example.com',1),
])
conn.commit()

business_key = input()
# Write your SQL query below (join hub + satellite)
query = "SELECT ..."
for row in conn.execute(query, [business_key]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 6: Columnar Storage and Query Optimisation (Q28–Q31)
            // ═══════════════════════════════════════════════════════════════

            // Q28
            [
                'order_index'         => 28,
                'problem_description' => <<<'MD'
**Column pruning** is a key optimisation: only select the columns you actually need. `SELECT *` forces the engine to read all columns, which is expensive in columnar stores.

The `sales_fact` table has many columns:

| sale_id | product | region | amount | sale_date  | discount |
|---------|---------|--------|--------|-----------|---------|
| 1       | Laptop  | North  | 1200   | 2023-01-15 | 5      |
| 2       | Phone   | South  | 800    | 2023-02-20 | 0      |
| 3       | Laptop  | East   | 1500   | 2023-03-10 | 10     |
| 4       | Tablet  | North  | 600    | 2023-04-05 | 0      |
| 5       | Phone   | West   | 900    | 2023-05-12 | 3      |

Write a query that selects **only** `product` and `amount`, ordered by `sale_id`.

Expected output:
```
Laptop 1200
Phone 800
Laptop 1500
Tablet 600
Phone 900
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT,
    amount INTEGER, sale_date TEXT, discount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?,?)', [
    (1,'Laptop','North',1200,'2023-01-15',5),
    (2,'Phone','South',800,'2023-02-20',0),
    (3,'Laptop','East',1500,'2023-03-10',10),
    (4,'Tablet','North',600,'2023-04-05',0),
    (5,'Phone','West',900,'2023-05-12',3),
])
conn.commit()

# Write your SQL query below (select only product and amount)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q29
            [
                'order_index'         => 29,
                'problem_description' => <<<'MD'
`LIMIT` lets you sample data quickly without scanning the entire table — essential for exploring large warehouses.

Using the same `sales_fact`, write a query that returns the **top 3 sales by amount** (highest first), printing `product` and `amount`.

Expected output:
```
Laptop 1500
Laptop 1200
Phone 900
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT,
    amount INTEGER, sale_date TEXT, discount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?,?)', [
    (1,'Laptop','North',1200,'2023-01-15',5),
    (2,'Phone','South',800,'2023-02-20',0),
    (3,'Laptop','East',1500,'2023-03-10',10),
    (4,'Tablet','North',600,'2023-04-05',0),
    (5,'Phone','West',900,'2023-05-12',3),
])
conn.commit()

# Write your SQL query below (use ORDER BY and LIMIT)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q30
            [
                'order_index'         => 30,
                'problem_description' => <<<'MD'
**Predicate pushdown** means filtering rows as early as possible so the engine processes less data before aggregating.

Using the same `sales_fact`, read a `region` from input and print the **total amount** for that region only.

Example:
```
Input:  North
Output: 1800
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT,
    amount INTEGER, sale_date TEXT, discount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?,?)', [
    (1,'Laptop','North',1200,'2023-01-15',5),
    (2,'Phone','South',800,'2023-02-20',0),
    (3,'Laptop','East',1500,'2023-03-10',10),
    (4,'Tablet','North',600,'2023-04-05',0),
    (5,'Phone','West',900,'2023-05-12',3),
])
conn.commit()

region = input()
# Write your SQL query below (filter by region, then aggregate)
query = "SELECT ..."
for row in conn.execute(query, [region]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q31
            [
                'order_index'         => 31,
                'problem_description' => <<<'MD'
Equality filters on indexed columns are the fastest way to look up a single record in a large warehouse table.

Using the same `sales_fact`, read a `sale_id` from input and print only the **amount** for that sale.

Example:
```
Input:  3
Output: 1500
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT,
    amount INTEGER, sale_date TEXT, discount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?,?)', [
    (1,'Laptop','North',1200,'2023-01-15',5),
    (2,'Phone','South',800,'2023-02-20',0),
    (3,'Laptop','East',1500,'2023-03-10',10),
    (4,'Tablet','North',600,'2023-04-05',0),
    (5,'Phone','West',900,'2023-05-12',3),
])
conn.commit()

sale_id = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [sale_id]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 7: Cloud Data Warehouses (Q32–Q37)
            // ═══════════════════════════════════════════════════════════════

            // Q32
            [
                'order_index'         => 32,
                'problem_description' => <<<'MD'
Cloud warehouses like **Snowflake**, **BigQuery**, and **Redshift** support **partition pruning** — scanning only the partitions matching a filter, which massively reduces I/O.

The `sales_fact` table includes a `sale_year` column that acts as a partition key:

| sale_id | product | region | amount | sale_year |
|---------|---------|--------|--------|----------|
| 1       | Laptop  | North  | 1200   | 2022     |
| 2       | Phone   | South  | 800    | 2022     |
| 3       | Laptop  | East   | 1500   | 2023     |
| 4       | Tablet  | North  | 600    | 2023     |
| 5       | Phone   | West   | 900    | 2022     |
| 6       | Laptop  | South  | 2000   | 2023     |

Read a `sale_year` from input and print the **total sales** for that year.

Example:
```
Input:  2022
Output: 2900
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

sale_year = int(input())
# Write your SQL query below
query = "SELECT ..."
for row in conn.execute(query, [sale_year]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q33
            [
                'order_index'         => 33,
                'problem_description' => <<<'MD'
`ROW_NUMBER()` is a window function supported by all major cloud warehouses. It assigns a unique sequential number to each row within an ordered partition.

Using the same 6-row `sales_fact`, write a query that assigns a row number to each sale ordered by `amount ASC`. Print `sale_id` and `row_number`, ordered by row number.

Expected output:
```
4 1
2 2
5 3
1 4
3 5
6 6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

# Write your SQL query below (use ROW_NUMBER() OVER (ORDER BY amount ASC))
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
`RANK()` is similar to `ROW_NUMBER()` but assigns the same rank to ties, then skips the next rank(s).

Using the same `sales_fact`, write a query that ranks each sale by `amount DESC` using `RANK()`. Print `product`, `amount`, and `rank`, ordered by rank then amount.

Expected output:
```
Laptop 2000 1
Laptop 1500 2
Laptop 1200 3
Phone 900 4
Phone 800 5
Tablet 600 6
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

# Write your SQL query below (use RANK() OVER (ORDER BY amount DESC))
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
A **running total** (cumulative sum) uses `SUM() OVER (ORDER BY ...)` to accumulate values row by row.

Using the same `sales_fact`, write a query that computes the running total of `amount` ordered by `sale_id`. Print `sale_id` and `running_total`.

Expected output:
```
1 1200
2 2000
3 3500
4 4100
5 5000
6 7000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

# Write your SQL query below (use SUM(amount) OVER (ORDER BY sale_id))
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
`LAG()` looks back at the **previous row's value** in an ordered window — useful for period-over-period comparisons.

Using the same `sales_fact`, write a query using `LAG(amount)` ordered by `sale_id` to print `sale_id`, `amount`, and `prev_amount` (the previous row's amount).

The first row has no previous row, so `prev_amount` will be `None`.

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
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

# Write your SQL query below (use LAG(amount) OVER (ORDER BY sale_id))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q37
            [
                'order_index'         => 37,
                'problem_description' => <<<'MD'
`LEAD()` looks **forward** to the next row's value — the mirror image of `LAG()`.

Using the same `sales_fact`, write a query using `LEAD(amount)` ordered by `sale_id` to print `sale_id`, `amount`, and `next_amount` (the next row's amount).

The last row has no next row, so `next_amount` will be `None`.

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
conn.execute('''CREATE TABLE sales_fact (
    sale_id INTEGER, product TEXT, region TEXT, amount INTEGER, sale_year INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?,?,?)', [
    (1,'Laptop','North',1200,2022),
    (2,'Phone','South',800,2022),
    (3,'Laptop','East',1500,2023),
    (4,'Tablet','North',600,2023),
    (5,'Phone','West',900,2022),
    (6,'Laptop','South',2000,2023),
])
conn.commit()

# Write your SQL query below (use LEAD(amount) OVER (ORDER BY sale_id))
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 8: Data Marts and the Kimball Bus Architecture (Q38–Q41)
            // ═══════════════════════════════════════════════════════════════

            // Q38
            [
                'order_index'         => 38,
                'problem_description' => <<<'MD'
A **data mart** is a subject-oriented subset of the warehouse tailored for a specific team. The `monthly_sales_mart` below is a pre-aggregated mart for the Sales team:

| month_year | total_amount | total_orders |
|-----------|-------------|-------------|
| 2023-01   | 45000       | 30          |
| 2023-02   | 52000       | 35          |
| 2023-03   | 61000       | 42          |
| 2023-04   | 47000       | 31          |

Write a SQL query that prints `month_year` and `total_amount` for all months, ordered by `month_year`.

Expected output:
```
2023-01 45000
2023-02 52000
2023-03 61000
2023-04 47000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE monthly_sales_mart (
    month_year TEXT, total_amount INTEGER, total_orders INTEGER
)''')
conn.executemany('INSERT INTO monthly_sales_mart VALUES (?,?,?)', [
    ('2023-01',45000,30),
    ('2023-02',52000,35),
    ('2023-03',61000,42),
    ('2023-04',47000,31),
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

            // Q39
            [
                'order_index'         => 39,
                'problem_description' => <<<'MD'
Using the same `monthly_sales_mart`, write a SQL query that returns the **month with the highest total amount**.

Print `month_year` and `total_amount`.

Expected output:
```
2023-03 61000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE monthly_sales_mart (
    month_year TEXT, total_amount INTEGER, total_orders INTEGER
)''')
conn.executemany('INSERT INTO monthly_sales_mart VALUES (?,?,?)', [
    ('2023-01',45000,30),
    ('2023-02',52000,35),
    ('2023-03',61000,42),
    ('2023-04',47000,31),
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

            // Q40
            [
                'order_index'         => 40,
                'problem_description' => <<<'MD'
The **Kimball Bus Architecture** uses roll-ups to aggregate grain from daily to monthly.

The `daily_sales` table has individual daily totals:

| sale_date  | amount |
|-----------|--------|
| 2023-01-05 | 5000  |
| 2023-01-15 | 8000  |
| 2023-01-20 | 6000  |
| 2023-02-10 | 9000  |
| 2023-02-22 | 7000  |

Write a SQL query that **rolls up** daily totals to monthly totals. Print `month` (first 7 characters of `sale_date`) and `monthly_total`, ordered by month.

Expected output:
```
2023-01 19000
2023-02 16000
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE daily_sales (
    sale_date TEXT, amount INTEGER
)''')
conn.executemany('INSERT INTO daily_sales VALUES (?,?)', [
    ('2023-01-05',5000),
    ('2023-01-15',8000),
    ('2023-01-20',6000),
    ('2023-02-10',9000),
    ('2023-02-22',7000),
])
conn.commit()

# Write your SQL query below (use SUBSTR to extract month, then GROUP BY)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q41
            [
                'order_index'         => 41,
                'problem_description' => <<<'MD'
In a bus architecture, **conformed dimensions** let multiple fact tables share the same dimension. Here, both `sales_fact` and `returns_fact` use the same product categories.

| sales_fact: id | amount |     | returns_fact: id | amount |
|----------------|--------|     |------------------|--------|
| 1              | 5000   |     | 1                | 1000   |
| 2              | 3000   |     | 2                | 500    |
| 3              | 4000   |     |                  |        |

Write Python code that computes **net sales** = total sales − total returns, and prints the result.

Expected output:
```
10500
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (id INTEGER, amount INTEGER)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?)', [
    (1,5000),(2,3000),(3,4000),
])
conn.execute('''CREATE TABLE returns_fact (id INTEGER, amount INTEGER)''')
conn.executemany('INSERT INTO returns_fact VALUES (?,?)', [
    (1,1000),(2,500),
])
conn.commit()

# Write your solution below (compute total sales - total returns)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // ═══════════════════════════════════════════════════════════════
            // TOPIC 9: dbt — The Data Build Tool (Q42–Q46)
            // ═══════════════════════════════════════════════════════════════

            // Q42
            [
                'order_index'         => 42,
                'problem_description' => <<<'MD'
In **dbt**, a **base model** is a thin SELECT layer over a raw source table. Its job is to rename cryptic columns into readable names — nothing else.

The `raw_orders` table has abbreviated column names:

| order_id | cust_id | tot_amt | ord_status |
|---------|--------|--------|-----------|
| 1001    | 201    | 500    | complete  |
| 1002    | 202    | 750    | pending   |
| 1003    | 201    | 300    | complete  |

Write a SQL query that selects all rows, renaming the columns to `id`, `customer_id`, `amount`, and `status`. Print all four values per row, space-separated, ordered by `id`.

Expected output:
```
1001 201 500 complete
1002 202 750 pending
1003 201 300 complete
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE raw_orders (
    order_id INTEGER, cust_id INTEGER, tot_amt INTEGER, ord_status TEXT
)''')
conn.executemany('INSERT INTO raw_orders VALUES (?,?,?,?)', [
    (1001,201,500,'complete'),
    (1002,202,750,'pending'),
    (1003,201,300,'complete'),
])
conn.commit()

# Write your SQL query below (rename columns using AS)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q43
            [
                'order_index'         => 43,
                'problem_description' => <<<'MD'
A **staging model** in dbt goes one step further: it renames columns **and** filters or casts data.

Using the same `raw_orders`, write a query that returns only **completed** orders (`ord_status = 'complete'`) with renamed columns (`id`, `customer_id`, `amount`, `status`), ordered by `id`.

Expected output:
```
1001 201 500 complete
1003 201 300 complete
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE raw_orders (
    order_id INTEGER, cust_id INTEGER, tot_amt INTEGER, ord_status TEXT
)''')
conn.executemany('INSERT INTO raw_orders VALUES (?,?,?,?)', [
    (1001,201,500,'complete'),
    (1002,202,750,'pending'),
    (1003,201,300,'complete'),
])
conn.commit()

# Write your SQL query below (rename + filter for complete orders)
query = "SELECT ..."
for row in conn.execute(query):
    print(*row)
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
            ],

            // Q44
            [
                'order_index'         => 44,
                'problem_description' => <<<'MD'
An **incremental dbt model** only processes rows that are **new or updated** since the last run. The key pattern is: `WHERE updated_at > '{{ last_run_date }}'`.

The `orders` table:

| id | customer_id | amount | updated_at |
|----|------------|--------|-----------|
| 1  | 101        | 500    | 2023-01-10|
| 2  | 102        | 800    | 2023-02-15|
| 3  | 103        | 300    | 2023-01-20|
| 4  | 104        | 600    | 2023-03-05|

Read a **cutoff date** from input and print the `id` of every order updated **after** that date, ordered ascending.

Example:
```
Input:  2023-02-01
Output:
2
4
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (
    id INTEGER, customer_id INTEGER, amount INTEGER, updated_at TEXT
)''')
conn.executemany('INSERT INTO orders VALUES (?,?,?,?)', [
    (1,101,500,'2023-01-10'),
    (2,102,800,'2023-02-15'),
    (3,103,300,'2023-01-20'),
    (4,104,600,'2023-03-05'),
])
conn.commit()

cutoff = input()
# Write your SQL query below (filter updated_at > cutoff, ORDER BY id)
query = "SELECT ..."
for row in conn.execute(query, [cutoff]):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q45
            [
                'order_index'         => 45,
                'problem_description' => <<<'MD'
dbt's **uniqueness test** checks that a column has no duplicate values. You can replicate it with SQL:

```sql
SELECT COUNT(*) - COUNT(DISTINCT col) AS duplicates FROM table
```

The `customers` table:

| id | customer_id | name           |
|----|------------|---------------|
| 1  | 101        | Alice          |
| 2  | 102        | Bob            |
| 3  | 101        | Alice Duplicate|
| 4  | 103        | Carol          |

Write a query that prints the **number of duplicate `customer_id` values** (i.e., total rows minus distinct `customer_id` count).

Expected output:
```
1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE customers (
    id INTEGER, customer_id INTEGER, name TEXT
)''')
conn.executemany('INSERT INTO customers VALUES (?,?,?)', [
    (1,101,'Alice'),
    (2,102,'Bob'),
    (3,101,'Alice Duplicate'),
    (4,103,'Carol'),
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

            // Q46
            [
                'order_index'         => 46,
                'problem_description' => <<<'MD'
dbt's **not_null test** counts rows where a specified column is `NULL`. You can simulate it with:

```sql
SELECT COUNT(*) FROM table WHERE col IS NULL
```

The `orders` table has some missing amounts:

| id | amount |
|----|--------|
| 1  | 500    |
| 2  | NULL   |
| 3  | 300    |
| 4  | NULL   |
| 5  | 200    |

Write a SQL query that counts how many rows have a `NULL` value in the `amount` column.

Expected output:
```
2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE orders (id INTEGER, amount INTEGER)''')
conn.executemany('INSERT INTO orders VALUES (?,?)', [
    (1,500),(2,None),(3,300),(4,None),(5,200),
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
            // TOPIC 10: Data Quality, Governance, and the Modern Data Stack (Q47–Q50)
            // ═══════════════════════════════════════════════════════════════

            // Q47
            [
                'order_index'         => 47,
                'problem_description' => <<<'MD'
A key part of **data governance** is profiling your data to find missing values before they cause downstream issues.

The `staging_sales` table:

| id | product | amount | sale_date  |
|----|---------|--------|-----------|
| 1  | Laptop  | 1200   | 2023-01-01|
| 2  | Phone   | NULL   | 2023-02-01|
| 3  | Laptop  | 1500   | 2023-03-01|
| 4  | NULL    | 600    | 2023-04-01|
| 5  | Phone   | 900    | NULL      |

Write a SQL query that counts the **total number of NULL values across `product`, `amount`, and `sale_date`** combined.

Expected output:
```
3
```

Hint: Sum the individual null counts for each column.
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE staging_sales (
    id INTEGER, product TEXT, amount INTEGER, sale_date TEXT
)''')
conn.executemany('INSERT INTO staging_sales VALUES (?,?,?,?)', [
    (1,'Laptop',1200,'2023-01-01'),
    (2,'Phone',None,'2023-02-01'),
    (3,'Laptop',1500,'2023-03-01'),
    (4,None,600,'2023-04-01'),
    (5,'Phone',900,None),
])
conn.commit()

# Write your SQL query below (sum null counts across three columns)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q48
            [
                'order_index'         => 48,
                'problem_description' => <<<'MD'
Duplicate records are a common data quality problem. They can arise from double-loading or failed deduplication in ETL.

The `products` table has some repeated product names:

| id | product_name | category    |
|----|-------------|-------------|
| 1  | Laptop      | Electronics |
| 2  | Phone       | Electronics |
| 3  | Laptop      | Electronics |
| 4  | Tablet      | Accessories |
| 5  | Phone       | Electronics |

Write a SQL query that lists **product names that appear more than once**, ordered alphabetically.

Expected output:
```
Laptop
Phone
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE products (
    id INTEGER, product_name TEXT, category TEXT
)''')
conn.executemany('INSERT INTO products VALUES (?,?,?)', [
    (1,'Laptop','Electronics'),
    (2,'Phone','Electronics'),
    (3,'Laptop','Electronics'),
    (4,'Tablet','Accessories'),
    (5,'Phone','Electronics'),
])
conn.commit()

# Write your SQL query below (use GROUP BY + HAVING COUNT(*) > 1)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q49
            [
                'order_index'         => 49,
                'problem_description' => <<<'MD'
**Referential integrity** means every foreign key in a fact table must match a valid primary key in the referenced dimension. Rows that don't match are called **orphans**.

**sales_fact**
| id | product_id | amount |
|----|-----------|--------|
| 1  | 1         | 1200   |
| 2  | 2         | 800    |
| 3  | 99        | 1500   |
| 4  | 1         | 600    |

**product_dim**
| product_id | name   |
|-----------|--------|
| 1         | Laptop |
| 2         | Phone  |

Row 3 in `sales_fact` has `product_id = 99`, which does not exist in `product_dim`.

Write a SQL query that counts **orphan rows** in `sales_fact` (rows whose `product_id` is not in `product_dim`).

Expected output:
```
1
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE sales_fact (
    id INTEGER, product_id INTEGER, amount INTEGER
)''')
conn.executemany('INSERT INTO sales_fact VALUES (?,?,?)', [
    (1,1,1200),(2,2,800),(3,99,1500),(4,1,600),
])
conn.execute('''CREATE TABLE product_dim (
    product_id INTEGER, name TEXT
)''')
conn.executemany('INSERT INTO product_dim VALUES (?,?)', [
    (1,'Laptop'),(2,'Phone'),
])
conn.commit()

# Write your SQL query below (use LEFT JOIN or NOT IN / NOT EXISTS)
query = "SELECT ..."
for row in conn.execute(query):
    print(row[0])
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 150,
            ],

            // Q50
            [
                'order_index'         => 50,
                'problem_description' => <<<'MD'
A **data reconciliation check** compares row counts between a source (staging) and a target table to confirm that all records were loaded.

Two tables:
- `staging_table` has 6 rows
- `target_table` has 4 rows (2 failed to load)

Write Python code that queries both tables, prints the row count of each, and then prints the **difference** (staging minus target).

Expected output:
```
staging: 6
target: 4
difference: 2
```
MD,
                'starter_code'        => <<<'PY'
import sqlite3
conn = sqlite3.connect(':memory:')
conn.execute('''CREATE TABLE staging_table (id INTEGER, data TEXT)''')
conn.executemany('INSERT INTO staging_table VALUES (?,?)', [
    (1,'a'),(2,'b'),(3,'c'),(4,'d'),(5,'e'),(6,'f'),
])
conn.execute('''CREATE TABLE target_table (id INTEGER, data TEXT)''')
conn.executemany('INSERT INTO target_table VALUES (?,?)', [
    (1,'a'),(2,'b'),(3,'c'),(4,'d'),
])
conn.commit()

# Write your solution below
# Print:  staging: X
#         target: Y
#         difference: Z
PY,
                'time_limit_seconds'  => 600,
                'base_xp'             => 100,
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

        // ── Q1: COUNT(*) ──────────────────────────────────────────────────
        $seed(1, [
            ['input' => null, 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '5', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '5', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '5', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q2: SUM(amount) ───────────────────────────────────────────────
        $seed(2, [
            ['input' => null, 'expected_output' => '5000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '5000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '5000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '5000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q3: COUNT(DISTINCT product) ───────────────────────────────────
        $seed(3, [
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q4: MAX(amount) ───────────────────────────────────────────────
        $seed(4, [
            ['input' => null, 'expected_output' => '1500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1500', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q5: AVG(amount) ───────────────────────────────────────────────
        $seed(5, [
            ['input' => null, 'expected_output' => '1000.0', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1000.0', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1000.0', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1000.0', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q6: Total per product (star join) ────────────────────────────
        $seed(6, [
            ['input' => null, 'expected_output' => "Laptop 2700\nPhone 1700\nTablet 600", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 2700\nPhone 1700\nTablet 600", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 2700\nPhone 1700\nTablet 600", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 2700\nPhone 1700\nTablet 600", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q7: Total by region (input) ──────────────────────────────────
        $seed(7, [
            ['input' => 'North', 'expected_output' => '1800', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'South', 'expected_output' => '800',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'East',  'expected_output' => '1500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'West',  'expected_output' => '900',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q8: Count per category ────────────────────────────────────────
        $seed(8, [
            ['input' => null, 'expected_output' => "Accessories 1\nElectronics 4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Accessories 1\nElectronics 4", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Accessories 1\nElectronics 4", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Accessories 1\nElectronics 4", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q9: Product + amount for a given sale_id ──────────────────────
        $seed(9, [
            ['input' => '1', 'expected_output' => 'Laptop 1200', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => 'Phone 800',   'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => 'Laptop 1500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '4', 'expected_output' => 'Tablet 600',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q10: Total per region (all regions) ───────────────────────────
        $seed(10, [
            ['input' => null, 'expected_output' => "East 1500\nNorth 1800\nSouth 800\nWest 900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "East 1500\nNorth 1800\nSouth 800\nWest 900", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "East 1500\nNorth 1800\nSouth 800\nWest 900", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "East 1500\nNorth 1800\nSouth 800\nWest 900", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q11: Snowflake — total per top-level category ─────────────────
        $seed(11, [
            ['input' => null, 'expected_output' => "Accessories 600\nElectronics 4400", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Accessories 600\nElectronics 4400", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Accessories 600\nElectronics 4400", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Accessories 600\nElectronics 4400", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q12: Total for a given category (input) ───────────────────────
        $seed(12, [
            ['input' => 'Electronics', 'expected_output' => '4400', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'Accessories', 'expected_output' => '600',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'Electronics', 'expected_output' => '4400', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'Accessories', 'expected_output' => '600',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q13: Count valid staging rows ─────────────────────────────────
        $seed(13, [
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q14: CAST text → int, SUM ─────────────────────────────────────
        $seed(14, [
            ['input' => null, 'expected_output' => '3500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3500', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q15: COALESCE NULL → 0, SUM ──────────────────────────────────
        $seed(15, [
            ['input' => null, 'expected_output' => '350', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '350', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '350', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '350', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q16: UPPER transformation ─────────────────────────────────────
        $seed(16, [
            ['input' => null, 'expected_output' => "LAPTOP\nPHONE\nTABLET", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "LAPTOP\nPHONE\nTABLET", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "LAPTOP\nPHONE\nTABLET", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "LAPTOP\nPHONE\nTABLET", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q17: Extract year from date ───────────────────────────────────
        $seed(17, [
            ['input' => '1', 'expected_output' => '2021', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2', 'expected_output' => '2022', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '3', 'expected_output' => '2023', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '1', 'expected_output' => '2021', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q18: Load valid rows, print count ─────────────────────────────
        $seed(18, [
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q19: Count current SCD records ────────────────────────────────
        $seed(19, [
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q20: List expired customer names ──────────────────────────────
        $seed(20, [
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Alice\nCarol", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q21: Current name + city for a given customer_key ─────────────
        $seed(21, [
            ['input' => '101', 'expected_output' => 'Alice Boston',   'is_hidden' => false, 'order_index' => 1],
            ['input' => '102', 'expected_output' => 'Bob Chicago',     'is_hidden' => false, 'order_index' => 2],
            ['input' => '103', 'expected_output' => 'Carol Portland',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '101', 'expected_output' => 'Alice Boston',    'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q22: Customer as of a check date ──────────────────────────────
        $seed(22, [
            ['input' => "101\n2021-01-01", 'expected_output' => 'Alice New York',  'is_hidden' => false, 'order_index' => 1],
            ['input' => "101\n2023-01-01", 'expected_output' => 'Alice Boston',    'is_hidden' => false, 'order_index' => 2],
            ['input' => "103\n2020-06-01", 'expected_output' => 'Carol Seattle',   'is_hidden' => true,  'order_index' => 3],
            ['input' => "103\n2023-01-01", 'expected_output' => 'Carol Portland',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q23: Total versions for a customer_key ────────────────────────
        $seed(23, [
            ['input' => '101', 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '102', 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '103', 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '101', 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q24: Customer keys with more than one version ──────────────────
        $seed(24, [
            ['input' => null, 'expected_output' => "101\n103", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "101\n103", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "101\n103", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "101\n103", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q25: All hub business keys ────────────────────────────────────
        $seed(25, [
            ['input' => null, 'expected_output' => "C001\nC002\nC003", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "C001\nC002\nC003", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "C001\nC002\nC003", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "C001\nC002\nC003", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q26: Order IDs for a given business key ───────────────────────
        $seed(26, [
            ['input' => 'C001', 'expected_output' => "1001\n1002", 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'C002', 'expected_output' => '1003',       'is_hidden' => false, 'order_index' => 2],
            ['input' => 'C001', 'expected_output' => "1001\n1002", 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'C002', 'expected_output' => '1003',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q27: Customer name from hub + satellite ────────────────────────
        $seed(27, [
            ['input' => 'C001', 'expected_output' => 'Alice', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'C002', 'expected_output' => 'Bob',   'is_hidden' => false, 'order_index' => 2],
            ['input' => 'C003', 'expected_output' => 'Carol', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'C001', 'expected_output' => 'Alice', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q28: Select only product and amount ───────────────────────────
        $seed(28, [
            ['input' => null, 'expected_output' => "Laptop 1200\nPhone 800\nLaptop 1500\nTablet 600\nPhone 900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1200\nPhone 800\nLaptop 1500\nTablet 600\nPhone 900", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1200\nPhone 800\nLaptop 1500\nTablet 600\nPhone 900", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1200\nPhone 800\nLaptop 1500\nTablet 600\nPhone 900", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q29: Top 3 by amount DESC ─────────────────────────────────────
        $seed(29, [
            ['input' => null, 'expected_output' => "Laptop 1500\nLaptop 1200\nPhone 900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 1500\nLaptop 1200\nPhone 900", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 1500\nLaptop 1200\nPhone 900", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 1500\nLaptop 1200\nPhone 900", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q30: Total for a given region (predicate pushdown) ────────────
        $seed(30, [
            ['input' => 'North', 'expected_output' => '1800', 'is_hidden' => false, 'order_index' => 1],
            ['input' => 'South', 'expected_output' => '800',  'is_hidden' => false, 'order_index' => 2],
            ['input' => 'East',  'expected_output' => '1500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => 'West',  'expected_output' => '900',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q31: Amount for a given sale_id ───────────────────────────────
        $seed(31, [
            ['input' => '3', 'expected_output' => '1500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '1', 'expected_output' => '1200', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '4', 'expected_output' => '600',  'is_hidden' => true,  'order_index' => 3],
            ['input' => '5', 'expected_output' => '900',  'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q32: Total sales for a given year ─────────────────────────────
        $seed(32, [
            ['input' => '2022', 'expected_output' => '2900', 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2023', 'expected_output' => '4100', 'is_hidden' => false, 'order_index' => 2],
            ['input' => '2022', 'expected_output' => '2900', 'is_hidden' => true,  'order_index' => 3],
            ['input' => '2023', 'expected_output' => '4100', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q33: ROW_NUMBER ordered by amount ASC ─────────────────────────
        $seed(33, [
            ['input' => null, 'expected_output' => "4 1\n2 2\n5 3\n1 4\n3 5\n6 6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "4 1\n2 2\n5 3\n1 4\n3 5\n6 6", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "4 1\n2 2\n5 3\n1 4\n3 5\n6 6", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "4 1\n2 2\n5 3\n1 4\n3 5\n6 6", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q34: RANK by amount DESC ──────────────────────────────────────
        $seed(34, [
            ['input' => null, 'expected_output' => "Laptop 2000 1\nLaptop 1500 2\nLaptop 1200 3\nPhone 900 4\nPhone 800 5\nTablet 600 6", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop 2000 1\nLaptop 1500 2\nLaptop 1200 3\nPhone 900 4\nPhone 800 5\nTablet 600 6", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop 2000 1\nLaptop 1500 2\nLaptop 1200 3\nPhone 900 4\nPhone 800 5\nTablet 600 6", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop 2000 1\nLaptop 1500 2\nLaptop 1200 3\nPhone 900 4\nPhone 800 5\nTablet 600 6", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q35: Running total ────────────────────────────────────────────
        $seed(35, [
            ['input' => null, 'expected_output' => "1 1200\n2 2000\n3 3500\n4 4100\n5 5000\n6 7000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200\n2 2000\n3 3500\n4 4100\n5 5000\n6 7000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200\n2 2000\n3 3500\n4 4100\n5 5000\n6 7000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200\n2 2000\n3 3500\n4 4100\n5 5000\n6 7000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q36: LAG — previous row amount ────────────────────────────────
        $seed(36, [
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 None\n2 800 1200\n3 1500 800\n4 600 1500\n5 900 600\n6 2000 900", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q37: LEAD — next row amount ───────────────────────────────────
        $seed(37, [
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1 1200 800\n2 800 1500\n3 1500 600\n4 600 900\n5 900 2000\n6 2000 None", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q38: All months and totals from mart ──────────────────────────
        $seed(38, [
            ['input' => null, 'expected_output' => "2023-01 45000\n2023-02 52000\n2023-03 61000\n2023-04 47000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2023-01 45000\n2023-02 52000\n2023-03 61000\n2023-04 47000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2023-01 45000\n2023-02 52000\n2023-03 61000\n2023-04 47000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2023-01 45000\n2023-02 52000\n2023-03 61000\n2023-04 47000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q39: Month with highest total ─────────────────────────────────
        $seed(39, [
            ['input' => null, 'expected_output' => '2023-03 61000', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '2023-03 61000', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '2023-03 61000', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '2023-03 61000', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q40: Roll-up daily → monthly ─────────────────────────────────
        $seed(40, [
            ['input' => null, 'expected_output' => "2023-01 19000\n2023-02 16000", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "2023-01 19000\n2023-02 16000", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "2023-01 19000\n2023-02 16000", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "2023-01 19000\n2023-02 16000", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q41: Net sales (sales - returns) ──────────────────────────────
        $seed(41, [
            ['input' => null, 'expected_output' => '10500', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '10500', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '10500', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '10500', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q42: Base model — rename columns ──────────────────────────────
        $seed(42, [
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1002 202 750 pending\n1003 201 300 complete", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1002 202 750 pending\n1003 201 300 complete", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1002 202 750 pending\n1003 201 300 complete", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1002 202 750 pending\n1003 201 300 complete", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q43: Staging model — rename + filter complete ──────────────────
        $seed(43, [
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1003 201 300 complete", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1003 201 300 complete", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1003 201 300 complete", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "1001 201 500 complete\n1003 201 300 complete", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q44: Incremental logic — filter by updated_at ─────────────────
        $seed(44, [
            ['input' => '2023-01-15', 'expected_output' => "2\n3\n4", 'is_hidden' => false, 'order_index' => 1],
            ['input' => '2023-02-01', 'expected_output' => "2\n4",    'is_hidden' => false, 'order_index' => 2],
            ['input' => '2023-01-01', 'expected_output' => "1\n2\n3\n4", 'is_hidden' => true, 'order_index' => 3],
            ['input' => '2023-03-01', 'expected_output' => '4',       'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q45: Uniqueness test — duplicate count ────────────────────────
        $seed(45, [
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q46: Not-null test — NULL count ───────────────────────────────
        $seed(46, [
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '2', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q47: Total NULLs across three columns ─────────────────────────
        $seed(47, [
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '3', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q48: Duplicate product names ──────────────────────────────────
        $seed(48, [
            ['input' => null, 'expected_output' => "Laptop\nPhone", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "Laptop\nPhone", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "Laptop\nPhone", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "Laptop\nPhone", 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q49: Orphan row count (referential integrity) ─────────────────
        $seed(49, [
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => '1', 'is_hidden' => true,  'order_index' => 4],
        ]);

        // ── Q50: Row count reconciliation ─────────────────────────────────
        $seed(50, [
            ['input' => null, 'expected_output' => "staging: 6\ntarget: 4\ndifference: 2", 'is_hidden' => false, 'order_index' => 1],
            ['input' => null, 'expected_output' => "staging: 6\ntarget: 4\ndifference: 2", 'is_hidden' => false, 'order_index' => 2],
            ['input' => null, 'expected_output' => "staging: 6\ntarget: 4\ndifference: 2", 'is_hidden' => true,  'order_index' => 3],
            ['input' => null, 'expected_output' => "staging: 6\ntarget: 4\ndifference: 2", 'is_hidden' => true,  'order_index' => 4],
        ]);

        $this->command->info('✅ Module 23 Coding (Newbie) seeded — 1 challenge, 50 questions, 200 test cases.');
    }
}