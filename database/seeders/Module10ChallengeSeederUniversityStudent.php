<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module10ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 10 — Database Management for Data Science (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Database Management for Data Science',
            'description'           => 'Deepen your SQL knowledge — trace query results, understand JOIN behaviour, work with GROUP BY and HAVING, and begin applying subqueries and normalization rules analytically.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 10,
        ]);

        $this->command->info("Seeding 50 university-level questions...");

        $qaData = [

            // ── SQL BASICS: TRACING QUERIES ───────────────────────────────
            [
                'q' => "Given this table called employees:\n\nid | name    | dept    | salary\n1  | Alice   | HR      | 50000\n2  | Bob     | IT      | 70000\n3  | Carol   | HR      | 55000\n4  | Dave    | IT      | 80000\n\nWhat does this query return?\nSELECT name FROM employees WHERE salary > 60000;",
                'opts' => [
                    ['Alice, Bob, Carol, Dave', false],
                    ['Bob, Dave', true],
                    ['Alice, Carol', false],
                    ['Bob only', false],
                ],
            ],
            [
                'q' => "Using the same employees table above, what does this return?\nSELECT COUNT(*) FROM employees WHERE dept = 'HR';",
                'opts' => [
                    ['1', false],
                    ['2', true],
                    ['4', false],
                    ['0', false],
                ],
            ],
            [
                'q' => "What is the output of this query on the employees table?\nSELECT dept, AVG(salary) FROM employees GROUP BY dept;",
                'opts' => [
                    ['One row per employee with their salary', false],
                    ['Two rows: HR with avg 52500, IT with avg 75000', true],
                    ['One row with the overall average salary', false],
                    ['An error because salary is not in GROUP BY', false],
                ],
            ],
            [
                'q' => "What does the HAVING clause do that WHERE cannot?",
                'opts' => [
                    ['Filters individual rows before grouping', false],
                    ['Filters groups AFTER a GROUP BY aggregation has been applied', true],
                    ['Sorts the result set', false],
                    ['Joins two tables together', false],
                ],
            ],
            [
                'q' => "Given the employees table, what does this query return?\nSELECT dept, COUNT(*) AS cnt\nFROM employees\nGROUP BY dept\nHAVING COUNT(*) > 1;",
                'opts' => [
                    ['All four departments', false],
                    ['Only HR and IT (both have 2 employees)', true],
                    ['Nothing — HAVING needs WHERE first', false],
                    ['Only the department with the most employees', false],
                ],
            ],
            [
                'q' => "What is the correct order of SQL clauses in a SELECT statement?",
                'opts' => [
                    ['SELECT → WHERE → FROM → GROUP BY → HAVING → ORDER BY', false],
                    ['SELECT → FROM → WHERE → GROUP BY → HAVING → ORDER BY', true],
                    ['FROM → GROUP BY → WHERE → SELECT → HAVING → ORDER BY', false],
                    ['FROM → SELECT → WHERE → ORDER BY → GROUP BY → HAVING', false],
                ],
            ],

            // ── JOINs: TRACING & ANALYSIS ─────────────────────────────────
            [
                'q' => "Table orders: order_id | customer_id | amount\n1 | 101 | 200\n2 | 102 | 150\n3 | 103 | 300\n\nTable customers: customer_id | name\n101 | Ana\n102 | Ben\n\nHow many rows does INNER JOIN on customer_id return?",
                'opts' => [
                    ['3', false],
                    ['2', true],
                    ['5', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "Using the same tables above, how many rows does a LEFT JOIN (orders LEFT JOIN customers) return?",
                'opts' => [
                    ['2', false],
                    ['3', true],
                    ['5', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "In a LEFT JOIN, what value appears in the customer name column for order_id = 3 (customer_id 103, who is not in the customers table)?",
                'opts' => [
                    ['0', false],
                    ['"unknown"', false],
                    ['NULL', true],
                    ['An error is thrown', false],
                ],
            ],
            [
                'q' => "What is a self-join?",
                'opts' => [
                    ['A JOIN between a table and itself using aliases', true],
                    ['A JOIN that does not need ON clause', false],
                    ['A JOIN where all rows match', false],
                    ['A JOIN between two identical tables in different databases', false],
                ],
            ],
            [
                'q' => "A CROSS JOIN between a table of 4 rows and a table of 3 rows produces how many rows?",
                'opts' => [
                    ['7', false],
                    ['4', false],
                    ['12', true],
                    ['1', false],
                ],
            ],

            // ── SUBQUERIES ────────────────────────────────────────────────
            [
                'q' => "What does this query do?\nSELECT name FROM employees\nWHERE salary > (SELECT AVG(salary) FROM employees);",
                'opts' => [
                    ['Returns all employees', false],
                    ['Returns employees whose salary is above the company average', true],
                    ['Returns the average salary', false],
                    ['Returns only the employee with the highest salary', false],
                ],
            ],
            [
                'q' => "Where can a subquery be placed in a SQL statement?",
                'opts' => [
                    ['Only inside the WHERE clause', false],
                    ['In the WHERE, FROM, or SELECT clause', true],
                    ['Only inside the FROM clause', false],
                    ['Only inside the HAVING clause', false],
                ],
            ],
            [
                'q' => "What is a correlated subquery?",
                'opts' => [
                    ['A subquery that runs only once and passes its result to the outer query', false],
                    ['A subquery that references a column from the outer query and is re-evaluated for each outer row', true],
                    ['A subquery written after FROM', false],
                    ['A subquery that uses GROUP BY', false],
                ],
            ],

            // ── CTEs ──────────────────────────────────────────────────────
            [
                'q' => "Which of the following is a correct CTE syntax?\n\n(A)\nWITH top_earners AS (\n  SELECT * FROM employees WHERE salary > 60000\n)\nSELECT * FROM top_earners;\n\n(B)\nCTE top_earners AS (\n  SELECT * FROM employees WHERE salary > 60000\n)\nSELECT * FROM top_earners;",
                'opts' => [
                    ['Only (B) is correct', false],
                    ['Only (A) is correct', true],
                    ['Both are correct', false],
                    ['Neither is correct', false],
                ],
            ],
            [
                'q' => "What is the main advantage of a CTE over a subquery?",
                'opts' => [
                    ['CTEs always run faster than subqueries', false],
                    ['CTEs make complex queries more readable and can be referenced multiple times in the same query', true],
                    ['CTEs permanently store the result as a table', false],
                    ['CTEs are the only way to use recursive queries', false],
                ],
            ],
            [
                'q' => "A recursive CTE is used for:",
                'opts' => [
                    ['Aggregating data across multiple tables', false],
                    ['Traversing hierarchical or tree-structured data (e.g., org charts, categories)', true],
                    ['Running a query on a schedule', false],
                    ['Creating temporary indexes', false],
                ],
            ],

            // ── WINDOW FUNCTIONS ──────────────────────────────────────────
            [
                'q' => "What is the output of ROW_NUMBER() OVER (PARTITION BY dept ORDER BY salary DESC) for the employees table?\n\nAlice | HR | 50000\nCarol | HR | 55000\nBob   | IT | 70000\nDave  | IT | 80000\n\nWhat row number does Alice get within HR?",
                'opts' => [
                    ['1', false],
                    ['2', true],
                    ['3', false],
                    ['4', false],
                ],
            ],
            [
                'q' => "What is the difference between RANK() and DENSE_RANK()?",
                'opts' => [
                    ['They are identical', false],
                    ['RANK() skips numbers after a tie (1,1,3); DENSE_RANK() does not skip (1,1,2)', true],
                    ['DENSE_RANK() skips numbers; RANK() does not', false],
                    ['RANK() only works on strings; DENSE_RANK() works on numbers', false],
                ],
            ],
            [
                'q' => "LAG(salary, 1) OVER (ORDER BY id) returns:",
                'opts' => [
                    ['The salary of the next row', false],
                    ['The salary of the previous row', true],
                    ['The average of the previous and current salary', false],
                    ['NULL for all rows', false],
                ],
            ],
            [
                'q' => "What does SUM(salary) OVER (PARTITION BY dept) compute?",
                'opts' => [
                    ['The salary for each individual employee', false],
                    ['A running total of all salaries', false],
                    ['The total salary for the department of each row, repeated for every row in that department', true],
                    ['The maximum salary in the entire table', false],
                ],
            ],

            // ── NORMALIZATION ─────────────────────────────────────────────
            [
                'q' => "A table stores: order_id, customer_name, customer_email, product_name, product_price.\nCustomer email repeats for every order. This violates which normal form?",
                'opts' => [
                    ['First Normal Form (1NF)', false],
                    ['Second Normal Form (2NF) — non-key attributes depend on only part of the composite key', false],
                    ['Both 2NF and 3NF — transitive dependencies exist; customer data should be in its own table', true],
                    ['No normal form is violated', false],
                ],
            ],
            [
                'q' => "Second Normal Form (2NF) requires that:",
                'opts' => [
                    ['No column stores multiple values', false],
                    ['The table is in 1NF AND every non-key attribute is fully dependent on the entire primary key (no partial dependencies)', true],
                    ['No two rows have the same values', false],
                    ['All data is in a single table', false],
                ],
            ],
            [
                'q' => "Third Normal Form (3NF) eliminates:",
                'opts' => [
                    ['Partial dependencies', false],
                    ['Transitive dependencies (non-key column depends on another non-key column)', true],
                    ['Multi-valued columns', false],
                    ['All NULL values', false],
                ],
            ],
            [
                'q' => "Denormalization is the process of:",
                'opts' => [
                    ['Normalizing a database to 4NF', false],
                    ['Intentionally introducing redundancy to improve read query performance', true],
                    ['Removing all indexes from a database', false],
                    ['Deleting duplicate rows', false],
                ],
            ],

            // ── INDEXING ──────────────────────────────────────────────────
            [
                'q' => "Which type of index enforces that all values in a column are unique?",
                'opts' => [
                    ['Composite index', false],
                    ['Full-text index', false],
                    ['Unique index', true],
                    ['Clustered index', false],
                ],
            ],
            [
                'q' => "A clustered index determines:",
                'opts' => [
                    ['Which column is the primary key', false],
                    ['The physical order in which rows are stored on disk', true],
                    ['How many indexes a table can have', false],
                    ['The maximum number of rows in a table', false],
                ],
            ],
            [
                'q' => "How many clustered indexes can a single table have?",
                'opts' => [
                    ['Unlimited', false],
                    ['Only one', true],
                    ['One per column', false],
                    ['Two', false],
                ],
            ],
            [
                'q' => "A composite index on columns (last_name, first_name) is MOST useful for which query?",
                'opts' => [
                    ['SELECT * FROM users WHERE first_name = \'Ana\';', false],
                    ['SELECT * FROM users WHERE last_name = \'Smith\';', true],
                    ['SELECT * FROM users WHERE age = 30;', false],
                    ['SELECT * FROM users ORDER BY email;', false],
                ],
            ],

            // ── PYTHON + SQL ──────────────────────────────────────────────
            [
                'q' => "What does the following pandas code do?\n\nimport pandas as pd\nfrom sqlalchemy import create_engine\nengine = create_engine('sqlite:///mydb.db')\ndf = pd.read_sql('SELECT * FROM sales', engine)",
                'opts' => [
                    ['Creates a new table called sales', false],
                    ['Reads the entire sales table from mydb.db into a pandas DataFrame', true],
                    ['Deletes all rows from the sales table', false],
                    ['Creates a SQLite database and immediately closes it', false],
                ],
            ],
            [
                'q' => "Which pandas method writes a DataFrame to a SQL table?",
                'opts' => [
                    ['df.to_csv()', false],
                    ['df.to_sql()', true],
                    ['df.to_excel()', false],
                    ['df.push_sql()', false],
                ],
            ],
            [
                'q' => "What does if_exists='replace' do in df.to_sql('table', engine, if_exists='replace')?",
                'opts' => [
                    ['Adds new rows to the existing table', false],
                    ['Drops the existing table and creates a new one with the DataFrame data', true],
                    ['Raises an error if the table already exists', false],
                    ['Updates existing rows that match the index', false],
                ],
            ],
            [
                'q' => "What is the role of SQLAlchemy's connection string (e.g., 'postgresql://user:pass@localhost/dbname')?",
                'opts' => [
                    ['It encrypts the database', false],
                    ['It specifies the database type, credentials, host, and database name needed to connect', true],
                    ['It runs a SQL query immediately on import', false],
                    ['It defines the table schema', false],
                ],
            ],

            // ── NoSQL ─────────────────────────────────────────────────────
            [
                'q' => "In MongoDB, data is stored as:",
                'opts' => [
                    ['Rows in a table', false],
                    ['JSON-like documents in collections', true],
                    ['Key-value pairs only', false],
                    ['Nodes and edges', false],
                ],
            ],
            [
                'q' => "Which SQL concept is most similar to a MongoDB 'collection'?",
                'opts' => [
                    ['A column', false],
                    ['A row', false],
                    ['A table', true],
                    ['An index', false],
                ],
            ],
            [
                'q' => "Redis is a popular example of which type of NoSQL database?",
                'opts' => [
                    ['Document store', false],
                    ['Graph database', false],
                    ['Key-value store', true],
                    ['Column-family store', false],
                ],
            ],
            [
                'q' => "When is a NoSQL database generally PREFERRED over a relational database?",
                'opts' => [
                    ['When data has complex, fixed relationships and requires ACID transactions', false],
                    ['When data is unstructured, rapidly changing in schema, or needs massive horizontal scaling', true],
                    ['When you need to run complex multi-table SQL JOINs', false],
                    ['When storage space is extremely limited', false],
                ],
            ],

            // ── MISC ANALYTICAL ───────────────────────────────────────────
            [
                'q' => "What is the difference between WHERE and HAVING in SQL?",
                'opts' => [
                    ['WHERE filters after GROUP BY; HAVING filters before GROUP BY', false],
                    ['WHERE filters individual rows before grouping; HAVING filters groups after aggregation', true],
                    ['They are interchangeable', false],
                    ['WHERE works on numeric columns only; HAVING works on all columns', false],
                ],
            ],
            [
                'q' => "Which SQL statement is used to change the structure of an existing table (e.g., add a column)?",
                'opts' => [
                    ['UPDATE TABLE', false],
                    ['MODIFY TABLE', false],
                    ['ALTER TABLE', true],
                    ['CHANGE TABLE', false],
                ],
            ],
            [
                'q' => "What does the COALESCE(col, 0) function return?",
                'opts' => [
                    ['Always returns 0', false],
                    ['Returns col if col is not NULL, otherwise returns 0', true],
                    ['Returns the maximum of col and 0', false],
                    ['Converts col to a string', false],
                ],
            ],
            [
                'q' => "What does EXPLAIN (or EXPLAIN ANALYZE) do in SQL?",
                'opts' => [
                    ['Explains the meaning of each column in plain text', false],
                    ['Shows the query execution plan, including how the database intends to execute the query and estimated costs', true],
                    ['Adds a comment to the query', false],
                    ['Converts the SQL to Python code', false],
                ],
            ],
            [
                'q' => "Which isolation level prevents dirty reads but allows non-repeatable reads?",
                'opts' => [
                    ['SERIALIZABLE', false],
                    ['READ UNCOMMITTED', false],
                    ['READ COMMITTED', true],
                    ['REPEATABLE READ', false],
                ],
            ],
            [
                'q' => "What does ACID stand for in database transactions?",
                'opts' => [
                    ['Availability, Consistency, Integrity, Durability', false],
                    ['Atomicity, Consistency, Isolation, Durability', true],
                    ['Atomicity, Concurrency, Integrity, Distribution', false],
                    ['Access, Control, Index, Data', false],
                ],
            ],
            [
                'q' => "The VIEW in SQL is:",
                'opts' => [
                    ['A physical copy of a table stored on disk', false],
                    ['A saved SELECT query that behaves like a virtual table', true],
                    ['An index on a specific column', false],
                    ['A stored procedure', false],
                ],
            ],
            [
                'q' => "What is the purpose of the UNION operator in SQL?",
                'opts' => [
                    ['Joins two tables on a common column', false],
                    ['Combines the result sets of two SELECT queries, removing duplicates', true],
                    ['Multiplies the rows of two tables', false],
                    ['Creates a new table from two existing tables', false],
                ],
            ],
            [
                'q' => "How does UNION ALL differ from UNION?",
                'opts' => [
                    ['UNION ALL removes duplicates; UNION keeps them', false],
                    ['UNION ALL keeps all rows including duplicates; UNION removes duplicates', true],
                    ['They are exactly the same', false],
                    ['UNION ALL only works with numeric columns', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 10 — Database Management for Data Science (University Student).");
    }
}