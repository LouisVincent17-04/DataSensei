<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module10ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 10 — Database Management for Data Science (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Database Management for Data Science',
            'description'           => 'Test your knowledge of the very basics of databases — what they are, how tables work, and simple SQL commands like SELECT, WHERE, and INSERT. No prior database experience assumed!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 10,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── RELATIONAL DATABASE FUNDAMENTALS ──────────────────────────
            [
                'q' => 'What is a database?',
                'opts' => [
                    ['A type of programming language', false],
                    ['An organized collection of structured data stored electronically', true],
                    ['A single spreadsheet file', false],
                    ['A web browser plugin', false],
                ],
            ],
            [
                'q' => 'In a relational database, data is organized into:',
                'opts' => [
                    ['Files and folders', false],
                    ['Nodes and edges', false],
                    ['Tables with rows and columns', true],
                    ['Stacks and queues', false],
                ],
            ],
            [
                'q' => 'What do we call a single horizontal entry in a database table?',
                'opts' => [
                    ['Column', false],
                    ['Field', false],
                    ['Row (or record)', true],
                    ['Key', false],
                ],
            ],
            [
                'q' => 'What do we call a single vertical category in a database table (e.g., "Age", "Name")?',
                'opts' => [
                    ['Row', false],
                    ['Column (or field)', true],
                    ['Record', false],
                    ['Index', false],
                ],
            ],
            [
                'q' => 'A Primary Key in a database table is used to:',
                'opts' => [
                    ['Sort the table alphabetically', false],
                    ['Uniquely identify each row in the table', true],
                    ['Link the table to an external file', false],
                    ['Encrypt the table data', false],
                ],
            ],
            [
                'q' => 'A Foreign Key in a database table is used to:',
                'opts' => [
                    ['Uniquely identify a row in the same table', false],
                    ['Reference the primary key of another table, linking two tables together', true],
                    ['Lock a row from being edited', false],
                    ['Store images in the database', false],
                ],
            ],
            [
                'q' => 'Which of the following is a popular relational database system used in data science?',
                'opts' => [
                    ['Microsoft Word', false],
                    ['PostgreSQL', true],
                    ['Adobe Photoshop', false],
                    ['Google Chrome', false],
                ],
            ],
            [
                'q' => 'What does SQL stand for?',
                'opts' => [
                    ['Simple Question Language', false],
                    ['System Query Logic', false],
                    ['Structured Query Language', true],
                    ['Standard Question Library', false],
                ],
            ],

            // ── SQL BASICS: SELECT, WHERE, ORDER BY, LIMIT ────────────────
            [
                'q' => 'Which SQL keyword is used to retrieve data from a table?',
                'opts' => [
                    ['GET', false],
                    ['FETCH', false],
                    ['SELECT', true],
                    ['RETRIEVE', false],
                ],
            ],
            [
                'q' => 'What does the * symbol mean in the query: SELECT * FROM students;',
                'opts' => [
                    ['Select only the first row', false],
                    ['Select all columns', true],
                    ['Select only numeric columns', false],
                    ['Multiply all values', false],
                ],
            ],
            [
                'q' => 'Which clause do you use to filter rows in a SQL query?',
                'opts' => [
                    ['HAVING', false],
                    ['FILTER', false],
                    ['WHERE', true],
                    ['LIMIT', false],
                ],
            ],
            [
                'q' => 'What will this query return?\n\nSELECT name FROM students WHERE age = 20;',
                'opts' => [
                    ['All columns for students aged 20', false],
                    ['Only the name column for students aged 20', true],
                    ['All students sorted by age', false],
                    ['The number 20', false],
                ],
            ],
            [
                'q' => 'Which SQL clause sorts the results of a query?',
                'opts' => [
                    ['SORT BY', false],
                    ['ORDER BY', true],
                    ['ARRANGE BY', false],
                    ['GROUP BY', false],
                ],
            ],
            [
                'q' => 'What does ORDER BY age DESC do?',
                'opts' => [
                    ['Sorts rows by age from smallest to largest', false],
                    ['Sorts rows by age from largest to smallest', true],
                    ['Deletes rows where age is null', false],
                    ['Groups rows by age', false],
                ],
            ],
            [
                'q' => 'What does the LIMIT clause do in a SQL query?',
                'opts' => [
                    ['Limits which columns are returned', false],
                    ['Restricts the number of rows returned', true],
                    ['Limits the size of the database', false],
                    ['Prevents duplicate rows', false],
                ],
            ],
            [
                'q' => 'What will SELECT * FROM products LIMIT 5; return?',
                'opts' => [
                    ['All rows in the products table', false],
                    ['Only the first 5 rows from the products table', true],
                    ['The last 5 rows from the products table', false],
                    ['5 random rows', false],
                ],
            ],

            // ── AGGREGATE FUNCTIONS & GROUP BY ────────────────────────────
            [
                'q' => 'Which SQL function counts the number of rows in a result?',
                'opts' => [
                    ['SUM()', false],
                    ['TOTAL()', false],
                    ['COUNT()', true],
                    ['NUM()', false],
                ],
            ],
            [
                'q' => 'Which SQL function returns the total sum of a numeric column?',
                'opts' => [
                    ['COUNT()', false],
                    ['AVG()', false],
                    ['SUM()', true],
                    ['MAX()', false],
                ],
            ],
            [
                'q' => 'What does AVG(salary) do in a SQL query?',
                'opts' => [
                    ['Returns the highest salary', false],
                    ['Returns the average value of the salary column', true],
                    ['Returns the total number of salaries', false],
                    ['Deletes all salary rows', false],
                ],
            ],
            [
                'q' => 'What does GROUP BY do in SQL?',
                'opts' => [
                    ['Sorts the results alphabetically', false],
                    ['Groups rows that share the same value in a column so aggregate functions can be applied to each group', true],
                    ['Removes duplicate rows', false],
                    ['Filters rows before returning them', false],
                ],
            ],
            [
                'q' => 'Which aggregate function returns the largest value in a column?',
                'opts' => [
                    ['TOP()', false],
                    ['LARGEST()', false],
                    ['MAX()', true],
                    ['UPPER()', false],
                ],
            ],
            [
                'q' => 'Which aggregate function returns the smallest value in a column?',
                'opts' => [
                    ['BOTTOM()', false],
                    ['MIN()', true],
                    ['LOWER()', false],
                    ['FIRST()', false],
                ],
            ],

            // ── JOINs ─────────────────────────────────────────────────────
            [
                'q' => 'What does a SQL JOIN do?',
                'opts' => [
                    ['Adds two numbers together', false],
                    ['Combines rows from two or more tables based on a related column', true],
                    ['Deletes matching rows from two tables', false],
                    ['Sorts two tables together', false],
                ],
            ],
            [
                'q' => 'An INNER JOIN returns:',
                'opts' => [
                    ['All rows from the left table only', false],
                    ['All rows from both tables regardless of a match', false],
                    ['Only rows that have a matching value in both tables', true],
                    ['Rows that do not match in either table', false],
                ],
            ],
            [
                'q' => 'A LEFT JOIN returns:',
                'opts' => [
                    ['Only matching rows from both tables', false],
                    ['All rows from the left table, and matched rows from the right table (NULLs where no match)', true],
                    ['Only rows from the right table', false],
                    ['Only rows with no match', false],
                ],
            ],
            [
                'q' => 'If table A has 3 rows and table B has 4 rows, and you do an INNER JOIN with 2 matches, how many rows are in the result?',
                'opts' => [
                    ['7', false],
                    ['12', false],
                    ['2', true],
                    ['3', false],
                ],
            ],
            [
                'q' => 'Which JOIN returns ALL rows from BOTH tables, filling in NULL where there is no match?',
                'opts' => [
                    ['INNER JOIN', false],
                    ['LEFT JOIN', false],
                    ['FULL OUTER JOIN', true],
                    ['CROSS JOIN', false],
                ],
            ],

            // ── SUBQUERIES & CTEs ─────────────────────────────────────────
            [
                'q' => 'What is a subquery in SQL?',
                'opts' => [
                    ['A query written inside another query', true],
                    ['A query that runs automatically every hour', false],
                    ['A stored procedure', false],
                    ['A type of JOIN', false],
                ],
            ],
            [
                'q' => 'What does CTE stand for in SQL?',
                'opts' => [
                    ['Column Table Expression', false],
                    ['Common Table Expression', true],
                    ['Conditional Table Entry', false],
                    ['Computed Table Engine', false],
                ],
            ],
            [
                'q' => 'A CTE is defined using which keyword?',
                'opts' => [
                    ['DEFINE', false],
                    ['CREATE', false],
                    ['WITH', true],
                    ['LET', false],
                ],
            ],

            // ── WINDOW FUNCTIONS ──────────────────────────────────────────
            [
                'q' => 'What makes window functions different from regular aggregate functions in SQL?',
                'opts' => [
                    ['They delete rows while computing', false],
                    ['They perform calculations across rows related to the current row without collapsing them into a single result', true],
                    ['They only work on string columns', false],
                    ['They always require a GROUP BY clause', false],
                ],
            ],
            [
                'q' => 'Which clause is required to define the window in a window function?',
                'opts' => [
                    ['GROUP BY', false],
                    ['PARTITION BY / OVER', true],
                    ['HAVING', false],
                    ['ORDER BY alone', false],
                ],
            ],
            [
                'q' => 'The ROW_NUMBER() window function assigns:',
                'opts' => [
                    ['A random number to each row', false],
                    ['A unique sequential number to each row within a partition', true],
                    ['The rank based on a score column', false],
                    ['The total count of rows', false],
                ],
            ],

            // ── DATABASE DESIGN & NORMALIZATION ──────────────────────────
            [
                'q' => 'What is database normalization?',
                'opts' => [
                    ['Making a database faster by adding indexes', false],
                    ['The process of organizing a database to reduce data redundancy and improve data integrity', true],
                    ['Converting a database to NoSQL format', false],
                    ['Encrypting a database', false],
                ],
            ],
            [
                'q' => 'First Normal Form (1NF) requires that:',
                'opts' => [
                    ['Each table has at least two columns', false],
                    ['Each column contains atomic (indivisible) values and each row is unique', true],
                    ['All columns depend on the foreign key', false],
                    ['All data is stored in one table', false],
                ],
            ],
            [
                'q' => 'What problem does normalization mainly solve?',
                'opts' => [
                    ['Slow SELECT queries', false],
                    ['Data redundancy and update anomalies', true],
                    ['Missing primary keys', false],
                    ['Large file sizes on disk', false],
                ],
            ],

            // ── INDEXING & QUERY PERFORMANCE ─────────────────────────────
            [
                'q' => 'What is a database index?',
                'opts' => [
                    ['A list of all table names in the database', false],
                    ['A data structure that speeds up the retrieval of rows from a table', true],
                    ['A backup copy of the database', false],
                    ['A constraint that prevents duplicate values', false],
                ],
            ],
            [
                'q' => 'Adding an index to a column speeds up:',
                'opts' => [
                    ['INSERT operations', false],
                    ['SELECT queries that search or filter on that column', true],
                    ['DELETE of all rows', false],
                    ['Database backups', false],
                ],
            ],
            [
                'q' => 'A downside of adding too many indexes to a table is:',
                'opts' => [
                    ['SELECT queries become slower', false],
                    ['INSERT and UPDATE operations become slower because each index must be updated', true],
                    ['The primary key stops working', false],
                    ['The table becomes read-only', false],
                ],
            ],

            // ── PYTHON + SQL ──────────────────────────────────────────────
            [
                'q' => 'Which Python library is commonly used to connect Python code to a SQL database?',
                'opts' => [
                    ['matplotlib', false],
                    ['numpy', false],
                    ['SQLAlchemy', true],
                    ['scikit-learn', false],
                ],
            ],
            [
                'q' => 'Which pandas function reads a SQL query result directly into a DataFrame?',
                'opts' => [
                    ['pd.read_csv()', false],
                    ['pd.read_sql()', true],
                    ['pd.read_table()', false],
                    ['pd.from_sql()', false],
                ],
            ],
            [
                'q' => 'In SQLAlchemy, what does create_engine() do?',
                'opts' => [
                    ['Creates a new database table', false],
                    ['Establishes a connection to the database', true],
                    ['Runs a SELECT query automatically', false],
                    ['Exports a DataFrame to CSV', false],
                ],
            ],

            // ── NoSQL ─────────────────────────────────────────────────────
            [
                'q' => 'What does NoSQL mean?',
                'opts' => [
                    ['No structured query language at all', false],
                    ['Not only SQL — a category of databases that don\'t use the traditional relational table model', true],
                    ['A database with no tables and no data', false],
                    ['A database that cannot be queried', false],
                ],
            ],
            [
                'q' => 'MongoDB is an example of which type of NoSQL database?',
                'opts' => [
                    ['Graph database', false],
                    ['Column-family database', false],
                    ['Document database', true],
                    ['Key-value store', false],
                ],
            ],
            [
                'q' => 'Which of the following is a key advantage of NoSQL databases over relational databases?',
                'opts' => [
                    ['They always run faster for every use case', false],
                    ['They can store flexible, unstructured or semi-structured data and scale horizontally more easily', true],
                    ['They require no storage space', false],
                    ['They support SQL queries natively', false],
                ],
            ],

            // ── BASIC DML ─────────────────────────────────────────────────
            [
                'q' => 'Which SQL command is used to add a new row to a table?',
                'opts' => [
                    ['ADD', false],
                    ['UPDATE', false],
                    ['INSERT INTO', true],
                    ['CREATE', false],
                ],
            ],
            [
                'q' => 'Which SQL command modifies existing data in a table?',
                'opts' => [
                    ['INSERT', false],
                    ['MODIFY', false],
                    ['UPDATE', true],
                    ['CHANGE', false],
                ],
            ],
            [
                'q' => 'Which SQL command removes rows from a table?',
                'opts' => [
                    ['REMOVE', false],
                    ['DROP', false],
                    ['DELETE', true],
                    ['CLEAR', false],
                ],
            ],
            [
                'q' => 'What is the difference between DELETE and DROP in SQL?',
                'opts' => [
                    ['They do the same thing', false],
                    ['DELETE removes rows from a table; DROP removes the entire table structure', true],
                    ['DROP removes rows; DELETE removes the table', false],
                    ['Neither actually removes data permanently', false],
                ],
            ],
            [
                'q' => 'Which SQL command creates a new table in a database?',
                'opts' => [
                    ['MAKE TABLE', false],
                    ['NEW TABLE', false],
                    ['CREATE TABLE', true],
                    ['BUILD TABLE', false],
                ],
            ],
            [
                'q' => 'The DISTINCT keyword in SQL is used to:',
                'opts' => [
                    ['Sort results in alphabetical order', false],
                    ['Return only unique (non-duplicate) values', true],
                    ['Filter rows by a condition', false],
                    ['Join two tables', false],
                ],
            ],
            [
                'q' => 'What does NULL represent in a database?',
                'opts' => [
                    ['The number zero', false],
                    ['An empty string ""', false],
                    ['A missing or unknown value', true],
                    ['The text "NULL"', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 10 — Database Management for Data Science (Newbie).");
    }
}