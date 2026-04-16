<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module10ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Safely wipe the existing challenge and its cascading questions/options
        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Database Management for Data Science')
                 ->delete();

        $this->command->info("Creating Module 10 — Database Management for Data Science (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Database Management for Data Science',
            'description'           => 'Test your intermediate database skills. Covers complex SQL joins, database normalization, indexing strategies, ACID properties, and the fundamentals of NoSQL systems.',
            'time_limit_seconds'    => 1800, // 30 mins
            'base_xp'               => 1000,
            'order_index'           => 10,
        ]);

        $this->command->info("Seeding 50 intermediate-level database questions...");

        $qaData = [
            // ─── SQL BASICS & AGGREGATION ───
            ['q' => 'Which SQL clause is used to filter the results of a GROUP BY operation?', 'opts' => [['HAVING', true], ['WHERE', false], ['FILTER', false], ['ORDER BY', false]]],
            ['q' => 'What is the correct order of execution in a SQL query?', 'opts' => [['FROM, WHERE, GROUP BY, HAVING, SELECT, ORDER BY', true], ['SELECT, FROM, WHERE, GROUP BY, HAVING, ORDER BY', false], ['FROM, SELECT, WHERE, GROUP BY, HAVING, ORDER BY', false], ['SELECT, WHERE, FROM, GROUP BY, HAVING, ORDER BY', false]]],
            ['q' => 'Which SQL aggregate function counts the number of non-null values in a column?', 'opts' => [['COUNT(column_name)', true], ['COUNT(*)', false], ['SUM(column_name)', false], ['TOTAL(column_name)', false]]],
            ['q' => 'What does the DISTINCT keyword do in a SELECT statement?', 'opts' => [['Removes duplicate rows from the result set', true], ['Sorts the result set uniquely', false], ['Filters out NULL values', false], ['Returns only the first row', false]]],
            ['q' => 'How do you select all columns from a table named "users"?', 'opts' => [['SELECT * FROM users', true], ['SELECT ALL FROM users', false], ['SELECT column FROM users', false], ['GET * FROM users', false]]],
            ['q' => 'Which keyword is used to sort the result-set in descending order?', 'opts' => [['DESC', true], ['ASC', false], ['DOWN', false], ['SORT DESC', false]]],
            ['q' => 'What does the LIKE operator do in a WHERE clause?', 'opts' => [['Searches for a specified pattern in a column', true], ['Checks for exact string equality', false], ['Compares numerical values', false], ['Filters for NULL values', false]]],
            ['q' => 'In a LIKE clause, what does the "%" wildcard represent?', 'opts' => [['Zero, one, or multiple characters', true], ['A single character', false], ['A numeric digit', false], ['A space character', false]]],
            ['q' => 'How do you find values that fall within a specific range?', 'opts' => [['BETWEEN value1 AND value2', true], ['IN RANGE (value1, value2)', false], ['WITHIN value1 TO value2', false], ['FROM value1 TO value2', false]]],
            ['q' => 'Which SQL function returns the current date and time?', 'opts' => [['CURRENT_TIMESTAMP', true], ['NOW()', true], ['TODAY()', false], ['SYSDATE', false]]], // Note: Handled logic for NOW() depending on dialect, typically both are fine.

            // ─── JOINS & SET OPERATIONS ───
            ['q' => 'Which JOIN returns all rows from the left table, and the matched rows from the right table?', 'opts' => [['LEFT JOIN', true], ['RIGHT JOIN', false], ['INNER JOIN', false], ['FULL OUTER JOIN', false]]],
            ['q' => 'What is the default type of JOIN in SQL if you just type "JOIN"?', 'opts' => [['INNER JOIN', true], ['LEFT JOIN', false], ['OUTER JOIN', false], ['CROSS JOIN', false]]],
            ['q' => 'Which operation combines the result sets of two SELECT statements and removes duplicates?', 'opts' => [['UNION', true], ['UNION ALL', false], ['INTERSECT', false], ['EXCEPT', false]]],
            ['q' => 'How does UNION ALL differ from UNION?', 'opts' => [['UNION ALL keeps duplicate rows', true], ['UNION ALL is slower', false], ['UNION ALL only works on numerical data', false], ['There is no difference', false]]],
            ['q' => 'What does a CROSS JOIN produce?', 'opts' => [['The Cartesian product of both tables', true], ['Only matching rows', false], ['Rows that do not match', false], ['An error if there is no ON clause', false]]],
            ['q' => 'Which set operation returns only the rows that appear in BOTH result sets?', 'opts' => [['INTERSECT', true], ['UNION', false], ['EXCEPT', false], ['MINUS', false]]],
            ['q' => 'If Table A has 5 rows and Table B has 4 rows, how many rows does a CROSS JOIN produce?', 'opts' => [['20', true], ['9', false], ['5', false], ['4', false]]],
            ['q' => 'What happens if a LEFT JOIN finds no match in the right table?', 'opts' => [['The right table columns contain NULL', true], ['The row is excluded from the results', false], ['An error is thrown', false], ['The query fails to execute', false]]],
            ['q' => 'Can you join a table to itself?', 'opts' => [['Yes, using a Self Join', true], ['No, it creates an infinite loop', false], ['Only if it has a foreign key to itself', false], ['Yes, but only using CROSS JOIN', false]]],
            ['q' => 'Which JOIN returns all records when there is a match in either the left or right table?', 'opts' => [['FULL OUTER JOIN', true], ['INNER JOIN', false], ['CROSS JOIN', false], ['LEFT JOIN', false]]],

            // ─── DATABASE DESIGN & NORMALIZATION ───
            ['q' => 'What is a Primary Key?', 'opts' => [['A column (or set of columns) that uniquely identifies each row', true], ['A key used to encrypt the database', false], ['A column that allows duplicate values', false], ['The first column in a table', false]]],
            ['q' => 'What is a Foreign Key?', 'opts' => [['A field that uniquely identifies a row in another table', true], ['A key generated randomly by the database', false], ['A field used for sorting data', false], ['A primary key located in a secondary database', false]]],
            ['q' => 'What is the main goal of database normalization?', 'opts' => [['To reduce data redundancy and improve data integrity', true], ['To increase query speed', false], ['To compress the database file size', false], ['To allow duplicate records', false]]],
            ['q' => 'What is a requirement for First Normal Form (1NF)?', 'opts' => [['All columns must contain atomic (indivisible) values', true], ['All non-key attributes must depend on the primary key', false], ['There must be no transitive dependencies', false], ['The table must have a foreign key', false]]],
            ['q' => 'What defines Second Normal Form (2NF)?', 'opts' => [['It is in 1NF and has no partial dependencies on a composite primary key', true], ['It removes all foreign keys', false], ['It requires all columns to be integers', false], ['It is in 1NF and has no transitive dependencies', false]]],
            ['q' => 'What defines Third Normal Form (3NF)?', 'opts' => [['It is in 2NF and has no transitive functional dependencies', true], ['It is in 2NF and all tables are merged', false], ['It removes all composite keys', false], ['It requires all data to be encrypted', false]]],
            ['q' => 'What is denormalization?', 'opts' => [['Intentionally introducing redundancy to improve read performance', true], ['Breaking tables down into smaller pieces', false], ['Removing all indexes from a table', false], ['Deleting duplicate rows', false]]],
            ['q' => 'In an ER Diagram, what does a diamond shape usually represent?', 'opts' => [['A relationship between entities', true], ['An entity', false], ['An attribute', false], ['A primary key', false]]],
            ['q' => 'What is a composite primary key?', 'opts' => [['A primary key made of two or more columns', true], ['A key that changes over time', false], ['A foreign key mixed with a primary key', false], ['A key containing string and integer data', false]]],
            ['q' => 'What type of relationship is resolved using a junction (or pivot) table?', 'opts' => [['Many-to-Many', true], ['One-to-Many', false], ['One-to-One', false], ['Zero-to-Many', false]]],

            // ─── INDEXING, PERFORMANCE & WINDOW FUNCTIONS ───
            ['q' => 'What is the primary purpose of a database index?', 'opts' => [['To speed up data retrieval operations', true], ['To save disk space', false], ['To enforce foreign key constraints', false], ['To automatically back up data', false]]],
            ['q' => 'What is a potential downside of adding too many indexes to a table?', 'opts' => [['Slower INSERT, UPDATE, and DELETE operations', true], ['Slower SELECT operations', false], ['Data corruption', false], ['Loss of primary keys', false]]],
            ['q' => 'Which data structure is most commonly used for database indexes?', 'opts' => [['B-Tree', true], ['Linked List', false], ['Stack', false], ['Queue', false]]],
            ['q' => 'What is a clustered index?', 'opts' => [['An index that dictates the physical order of data in a table', true], ['An index built on multiple columns', false], ['A virtual index stored in RAM', false], ['An index used only for foreign keys', false]]],
            ['q' => 'How many clustered indexes can a single table have?', 'opts' => [['Exactly one', true], ['Zero or more', false], ['Up to 255', false], ['Depends on the database size', false]]],
            ['q' => 'What is the purpose of the EXPLAIN (or EXPLAIN PLAN) command?', 'opts' => [['To show the execution plan of a query for optimization', true], ['To add comments to a SQL script', false], ['To generate documentation for the database', false], ['To execute a query safely in a sandbox', false]]],
            ['q' => 'Which window function assigns a unique sequential integer to rows within a partition?', 'opts' => [['ROW_NUMBER()', true], ['RANK()', false], ['DENSE_RANK()', false], ['NTILE()', false]]],
            ['q' => 'How does RANK() differ from ROW_NUMBER() when there are ties?', 'opts' => [['RANK() gives tied rows the same rank and skips the next numbers', true], ['RANK() assigns random numbers to ties', false], ['They are exactly the same', false], ['RANK() throws an error on ties', false]]],
            ['q' => 'What is the OVER() clause used for?', 'opts' => [['To define the window (partition and ordering) for a window function', true], ['To end a query', false], ['To override a primary key', false], ['To write data over existing rows', false]]],
            ['q' => 'Which window function accesses data from a previous row in the same result set?', 'opts' => [['LAG()', true], ['LEAD()', false], ['PREV()', false], ['PRIOR()', false]]],

            // ─── ACID & NOSQL DATABASES ───
            ['q' => 'What does the "A" in ACID stand for?', 'opts' => [['Atomicity', true], ['Accuracy', false], ['Availability', false], ['Agility', false]]],
            ['q' => 'What does "Atomicity" guarantee in a database transaction?', 'opts' => [['The entire transaction succeeds or the entire transaction fails (all or nothing)', true], ['The data is always available', false], ['The data is isolated from other users', false], ['The transaction will run incredibly fast', false]]],
            ['q' => 'What does the "C" in ACID stand for?', 'opts' => [['Consistency', true], ['Concurrency', false], ['Concurrency', false], ['Completeness', false]]],
            ['q' => 'What does "Durability" in ACID mean?', 'opts' => [['Once a transaction is committed, it remains saved even in the event of a system failure', true], ['The database will never crash', false], ['Queries will execute consistently fast', false], ['Hardware will not degrade over time', false]]],
            ['q' => 'According to the CAP Theorem, a distributed system can only guarantee two out of which three properties?', 'opts' => [['Consistency, Availability, Partition Tolerance', true], ['Concurrency, Atomicity, Performance', false], ['Consistency, Accuracy, Partition Tolerance', false], ['Caching, Availability, Performance', false]]],
            ['q' => 'MongoDB is an example of which type of NoSQL database?', 'opts' => [['Document Store', true], ['Key-Value Store', false], ['Graph Database', false], ['Column-Family Store', false]]],
            ['q' => 'Redis is best described as which type of database?', 'opts' => [['Key-Value Store', true], ['Relational Database', false], ['Graph Database', false], ['Document Store', false]]],
            ['q' => 'Which type of NoSQL database is highly optimized for traversing relationships between entities?', 'opts' => [['Graph Database (e.g., Neo4j)', true], ['Document Database', false], ['Key-Value Store', false], ['Time-Series Database', false]]],
            ['q' => 'What is a major advantage of NoSQL databases over traditional RDBMS?', 'opts' => [['Flexible schemas and easier horizontal scaling', true], ['Stricter ACID compliance', false], ['Standardized SQL querying', false], ['Built-in complex JOIN capabilities', false]]],
            ['q' => 'In data warehousing, what does ETL stand for?', 'opts' => [['Extract, Transform, Load', true], ['Execute, Transfer, Log', false], ['Evaluate, Translate, Load', false], ['Extract, Test, Leave', false]]],
        ];

        // Shuffle questions to ensure variety
        shuffle($qaData);

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
                'challenge_category_id' => $category->id,
            ]);
            
            // Shuffle options so the correct answer isn't always first
            $options = $data['opts'];
            shuffle($options);

            foreach ($options as $opt) {
                ChallengeOption::create([
                    'challenge_question_id' => $question->id,
                    'option_text'           => $opt[0],
                    'is_correct'            => $opt[1],
                ]);
            }
        }

        $this->command->info("✅ Done! 50 questions seeded for Module 10 — Database Management (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}