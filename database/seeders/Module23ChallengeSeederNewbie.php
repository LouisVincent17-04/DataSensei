<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module23ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
                 ->where('title', 'Data Warehousing')
                 ->delete();

        $this->command->info("Creating Module 23 — Data Warehousing (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Data Warehousing',
            'description'           => 'Test your understanding of the very basics of data warehousing — what a data warehouse is, why organisations use them, key concepts like dimensions and facts, and common tools. No prior experience required.',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 23,
        ]);

        $this->command->info("Seeding 50 newbie-level Data Warehousing questions...");

        $qaData = [

            // ── 23.1 WHAT IS A DATA WAREHOUSE ─────────────────────────────
            [
                'q' => 'What is a data warehouse?',
                'opts' => [
                    ['A physical storage room for company servers', false],
                    ['A central repository that stores large amounts of structured data from multiple sources for reporting and analysis', true],
                    ['A type of spreadsheet used by accountants', false],
                    ['A cloud service for hosting websites', false],
                ],
            ],
            [
                'q' => 'Who coined the term "data warehouse" and is considered its founding father?',
                'opts' => [
                    ['Edgar Codd', false],
                    ['Bill Inmon', true],
                    ['Ralph Kimball', false],
                    ['James Martin', false],
                ],
            ],
            [
                'q' => 'What is the PRIMARY purpose of a data warehouse?',
                'opts' => [
                    ['To process day-to-day business transactions as fast as possible', false],
                    ['To support business intelligence, analytics, and decision-making by storing historical data', true],
                    ['To replace a company\'s email system', false],
                    ['To store raw log files from web servers', false],
                ],
            ],
            [
                'q' => 'A data warehouse is described as "subject-oriented". This means:',
                'opts' => [
                    ['It only stores data about one topic', false],
                    ['It is organised around key business subjects like sales, customers, and products rather than around application functions', true],
                    ['The data is sorted alphabetically by subject', false],
                    ['It requires a subject-matter expert to operate', false],
                ],
            ],
            [
                'q' => 'A data warehouse is described as "non-volatile". This means:',
                'opts' => [
                    ['It cannot be deleted accidentally', false],
                    ['Once data is loaded into the warehouse, it is not deleted or changed — it is read-only for analysis', true],
                    ['It runs without electricity', false],
                    ['It is stored in a fire-proof location', false],
                ],
            ],
            [
                'q' => 'What does OLAP stand for?',
                'opts' => [
                    ['Online Linear Analysis Processing', false],
                    ['Online Analytical Processing', true],
                    ['Operational Large-scale Analytics Platform', false],
                    ['Object-Linked Application Protocol', false],
                ],
            ],
            [
                'q' => 'What does OLTP stand for?',
                'opts' => [
                    ['Online Transaction Processing', true],
                    ['Object-Level Transfer Protocol', false],
                    ['Operational Logic and Transformation Pipeline', false],
                    ['Online Total Log Processing', false],
                ],
            ],
            [
                'q' => 'What is the key difference between OLTP and OLAP systems?',
                'opts' => [
                    ['OLTP is used for reading only; OLAP is used for writing only', false],
                    ['OLTP handles day-to-day transactions (many small reads/writes); OLAP handles complex analytical queries on large historical datasets', true],
                    ['OLTP is cloud-based; OLAP is on-premise only', false],
                    ['There is no difference — they are the same thing', false],
                ],
            ],

            // ── 23.2 DIMENSIONAL MODELLING ────────────────────────────────
            [
                'q' => 'In dimensional modelling, what is a "fact table"?',
                'opts' => [
                    ['A table that stores reference information like customer names', false],
                    ['The central table in a star schema that stores measurable business events and metrics (e.g. sales amount, quantity)', true],
                    ['A table that stores error logs', false],
                    ['A lookup table for country codes', false],
                ],
            ],
            [
                'q' => 'In dimensional modelling, what is a "dimension table"?',
                'opts' => [
                    ['A table that stores numerical measurements', false],
                    ['A table that provides descriptive context for the facts — e.g. who, what, where, when', true],
                    ['A table that connects two databases', false],
                    ['A table that stores aggregated totals', false],
                ],
            ],
            [
                'q' => 'Which of the following is a typical example of a FACT?',
                'opts' => [
                    ['Customer name', false],
                    ['Product category', false],
                    ['Total sales amount for an order', true],
                    ['Country of a store', false],
                ],
            ],
            [
                'q' => 'Which of the following is a typical example of a DIMENSION?',
                'opts' => [
                    ['Revenue', false],
                    ['Number of units sold', false],
                    ['Date of purchase', true],
                    ['Profit margin', false],
                ],
            ],
            [
                'q' => 'In a Star Schema, the fact table is surrounded by:',
                'opts' => [
                    ['Other fact tables', false],
                    ['Dimension tables directly connected to it', true],
                    ['Staging tables', false],
                    ['Raw CSV files', false],
                ],
            ],
            [
                'q' => 'How does a Snowflake Schema differ from a Star Schema?',
                'opts' => [
                    ['A Snowflake Schema has no fact table', false],
                    ['In a Snowflake Schema, dimension tables are further normalised into sub-dimension tables, making the schema look like a snowflake', true],
                    ['A Snowflake Schema is used only for small datasets', false],
                    ['They are identical — different names for the same thing', false],
                ],
            ],
            [
                'q' => 'A surrogate key in a data warehouse is:',
                'opts' => [
                    ['The original primary key from the source system', false],
                    ['A system-generated integer key used in the warehouse that is independent of the source system\'s key', true],
                    ['A key used to encrypt sensitive data', false],
                    ['A composite key made of two columns', false],
                ],
            ],

            // ── 23.3 ETL vs ELT ───────────────────────────────────────────
            [
                'q' => 'What does ETL stand for?',
                'opts' => [
                    ['Extract, Transfer, Load', false],
                    ['Extract, Transform, Load', true],
                    ['Evaluate, Test, Launch', false],
                    ['Export, Translate, Link', false],
                ],
            ],
            [
                'q' => 'In an ETL pipeline, in what ORDER do the steps happen?',
                'opts' => [
                    ['Load → Transform → Extract', false],
                    ['Transform → Extract → Load', false],
                    ['Extract → Transform → Load', true],
                    ['Extract → Load → Transform', false],
                ],
            ],
            [
                'q' => 'What does the "Extract" step in ETL do?',
                'opts' => [
                    ['Cleans and formats data', false],
                    ['Pulls raw data from source systems such as databases, APIs, and flat files', true],
                    ['Loads data into the final destination', false],
                    ['Compresses data for storage', false],
                ],
            ],
            [
                'q' => 'What does the "Transform" step in ETL do?',
                'opts' => [
                    ['Copies data from one database to another', false],
                    ['Cleans, filters, enriches, and restructures data to match the target schema', true],
                    ['Archives old data', false],
                    ['Connects to source systems', false],
                ],
            ],
            [
                'q' => 'In ELT, what is the key difference from ETL?',
                'opts' => [
                    ['ELT does not require extraction', false],
                    ['In ELT, data is loaded into the target system FIRST, then transformed there — leveraging the warehouse\'s own compute power', true],
                    ['ELT only works with cloud systems', false],
                    ['ELT skips the loading step', false],
                ],
            ],
            [
                'q' => 'Which modern tool is most associated with the ELT approach?',
                'opts' => [
                    ['Microsoft Excel', false],
                    ['dbt (data build tool)', true],
                    ['Apache Kafka', false],
                    ['MySQL Workbench', false],
                ],
            ],

            // ── 23.4 SLOWLY CHANGING DIMENSIONS ──────────────────────────
            [
                'q' => 'What is a Slowly Changing Dimension (SCD)?',
                'opts' => [
                    ['A dimension that is loaded very slowly due to large data volumes', false],
                    ['A dimension whose attribute values change slowly and infrequently over time — e.g. a customer\'s address or marital status', true],
                    ['A dimension table with very few rows', false],
                    ['A dimension that is recalculated every day', false],
                ],
            ],
            [
                'q' => 'In SCD Type 1, when a dimension value changes, you:',
                'opts' => [
                    ['Add a new row to preserve history', false],
                    ['Overwrite the existing value — no history is kept', true],
                    ['Create a new version column', false],
                    ['Archive the old value in a separate table', false],
                ],
            ],
            [
                'q' => 'In SCD Type 2, when a dimension value changes, you:',
                'opts' => [
                    ['Overwrite the old value directly', false],
                    ['Insert a new row with the updated value, preserving the full history of changes', true],
                    ['Delete the old row', false],
                    ['Store only the latest value', false],
                ],
            ],
            [
                'q' => 'SCD Type 2 rows are typically managed using which columns?',
                'opts' => [
                    ['created_at and deleted_at', false],
                    ['effective_start_date, effective_end_date, and an is_current flag', true],
                    ['version_number and checksum', false],
                    ['row_id and batch_id', false],
                ],
            ],

            // ── 23.5 DATA VAULT MODELLING ─────────────────────────────────
            [
                'q' => 'Data Vault modelling was designed primarily for:',
                'opts' => [
                    ['Small databases with fewer than 100 tables', false],
                    ['Enterprise data warehouses that need scalability, auditability, and the ability to handle change over time', true],
                    ['Replacing relational databases entirely', false],
                    ['Real-time streaming data only', false],
                ],
            ],
            [
                'q' => 'The three core components of a Data Vault model are:',
                'opts' => [
                    ['Facts, Dimensions, and Aggregates', false],
                    ['Hubs, Links, and Satellites', true],
                    ['Tables, Views, and Indexes', false],
                    ['Sources, Targets, and Mappings', false],
                ],
            ],
            [
                'q' => 'In Data Vault, a HUB stores:',
                'opts' => [
                    ['The historical changes of descriptive attributes', false],
                    ['Unique business keys for a core business concept (e.g. unique customer IDs)', true],
                    ['The relationships between business concepts', false],
                    ['Raw source data before transformation', false],
                ],
            ],
            [
                'q' => 'In Data Vault, a SATELLITE stores:',
                'opts' => [
                    ['Business keys', false],
                    ['Relationships between hubs', false],
                    ['Descriptive context and historical changes for hubs or links', true],
                    ['Aggregated metrics', false],
                ],
            ],

            // ── 23.6 COLUMNAR STORAGE ─────────────────────────────────────
            [
                'q' => 'In a columnar (column-oriented) database, data is stored:',
                'opts' => [
                    ['Row by row — all fields of one record together', false],
                    ['Column by column — all values of one attribute together', true],
                    ['In JSON format only', false],
                    ['In alphabetical order by column name', false],
                ],
            ],
            [
                'q' => 'Why is columnar storage faster for analytical queries that read only a few columns from a large table?',
                'opts' => [
                    ['It stores less data overall', false],
                    ['Only the relevant columns are read from disk — irrelevant columns are never accessed, dramatically reducing I/O', true],
                    ['It automatically indexes every column', false],
                    ['It compresses all data to zero bytes', false],
                ],
            ],
            [
                'q' => 'Which of the following databases uses columnar storage?',
                'opts' => [
                    ['MySQL', false],
                    ['PostgreSQL (default row store)', false],
                    ['Amazon Redshift', true],
                    ['SQLite', false],
                ],
            ],
            [
                'q' => 'Columnar storage achieves high compression ratios because:',
                'opts' => [
                    ['Columns are stored on separate servers', false],
                    ['Values in the same column tend to be of the same type and similar values, making them highly compressible', true],
                    ['It uses ZIP compression on the entire database', false],
                    ['Duplicate rows are automatically deleted', false],
                ],
            ],

            // ── 23.7 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => 'Which of the following is a cloud-based data warehouse service?',
                'opts' => [
                    ['MySQL', false],
                    ['Microsoft Excel', false],
                    ['Snowflake', true],
                    ['Apache Spark', false],
                ],
            ],
            [
                'q' => 'Google BigQuery is a cloud data warehouse that uses which pricing model for queries?',
                'opts' => [
                    ['A flat monthly subscription regardless of usage', false],
                    ['Pay-per-query based on the amount of data scanned', true],
                    ['A per-row insert fee', false],
                    ['Free for all users always', false],
                ],
            ],
            [
                'q' => 'Amazon Redshift is based on which open-source database?',
                'opts' => [
                    ['MySQL', false],
                    ['SQLite', false],
                    ['PostgreSQL', true],
                    ['MongoDB', false],
                ],
            ],
            [
                'q' => 'Snowflake\'s key architectural innovation is separating:',
                'opts' => [
                    ['Tables from indexes', false],
                    ['Compute (query processing) from storage (data), allowing each to scale independently', true],
                    ['SQL from NoSQL queries', false],
                    ['Cloud from on-premise operations', false],
                ],
            ],

            // ── 23.8 DATA MARTS & KIMBALL ─────────────────────────────────
            [
                'q' => 'What is a data mart?',
                'opts' => [
                    ['A small convenience store for data scientists', false],
                    ['A subset of a data warehouse focused on a specific business area or department, like Sales or Finance', true],
                    ['A staging area for raw data', false],
                    ['A type of NoSQL database', false],
                ],
            ],
            [
                'q' => 'Ralph Kimball\'s bottom-up approach to data warehousing builds:',
                'opts' => [
                    ['A single enterprise-wide warehouse first, then data marts', false],
                    ['Individual departmental data marts first, which are later integrated into an enterprise warehouse', true],
                    ['Only OLTP systems, not OLAP', false],
                    ['Data lakes without any dimensional modelling', false],
                ],
            ],
            [
                'q' => 'The Kimball Bus Architecture uses "conformed dimensions" which are:',
                'opts' => [
                    ['Dimensions that are only used once', false],
                    ['Shared dimension tables that mean exactly the same thing across different data marts, enabling consistent cross-mart analysis', true],
                    ['Dimensions that automatically update themselves', false],
                    ['Encrypted dimension tables for security', false],
                ],
            ],
            [
                'q' => 'In Kimball\'s approach, a "conformed fact" means:',
                'opts' => [
                    ['A fact that has been validated by an auditor', false],
                    ['Facts (metrics) that are defined consistently and can be compared across different data marts', true],
                    ['A fact table with no dimension tables', false],
                    ['A pre-aggregated fact table', false],
                ],
            ],

            // ── 23.9 dbt ─────────────────────────────────────────────────
            [
                'q' => 'What does dbt stand for?',
                'opts' => [
                    ['Data Build Tool', true],
                    ['Database Batch Transfer', false],
                    ['Distributed Batch Transformer', false],
                    ['Data Business Template', false],
                ],
            ],
            [
                'q' => 'dbt is primarily used for which step in the data pipeline?',
                'opts' => [
                    ['Extracting data from source systems', false],
                    ['The Transform step — writing SQL-based transformations inside the data warehouse', true],
                    ['Loading raw files from S3', false],
                    ['Visualising dashboards', false],
                ],
            ],
            [
                'q' => 'In dbt, models are written as:',
                'opts' => [
                    ['Python scripts only', false],
                    ['SELECT SQL queries stored in .sql files', true],
                    ['JSON configuration files', false],
                    ['Excel macros', false],
                ],
            ],
            [
                'q' => 'What does `dbt run` do?',
                'opts' => [
                    ['Extracts data from source systems', false],
                    ['Executes all dbt models — running the SQL transformations and creating/updating tables or views in the warehouse', true],
                    ['Deletes all tables in the warehouse', false],
                    ['Deploys the dbt project to production servers', false],
                ],
            ],

            // ── 23.10 DATA QUALITY & GOVERNANCE ──────────────────────────
            [
                'q' => 'What is data quality in the context of a data warehouse?',
                'opts' => [
                    ['How visually appealing the dashboard is', false],
                    ['The degree to which data is accurate, complete, consistent, timely, and fit for its intended use', true],
                    ['The speed at which data is loaded', false],
                    ['The number of tables in the warehouse', false],
                ],
            ],
            [
                'q' => 'Data governance in a data warehouse refers to:',
                'opts' => [
                    ['The IT team controlling who can write code', false],
                    ['The policies, processes, and standards that manage data availability, usability, integrity, and security', true],
                    ['The hardware that stores the data', false],
                    ['Government regulations for data storage', false],
                ],
            ],
            [
                'q' => 'A data catalog is used to:',
                'opts' => [
                    ['Delete duplicate data automatically', false],
                    ['Provide a searchable inventory of an organisation\'s data assets with metadata, ownership, and lineage information', true],
                    ['Store backup copies of all warehouse tables', false],
                    ['Generate automated reports for management', false],
                ],
            ],
            [
                'q' => 'Data lineage in a data warehouse describes:',
                'opts' => [
                    ['The legal ownership of a dataset', false],
                    ['The origin of data and how it has moved and transformed through the pipeline from source to destination', true],
                    ['The age of the oldest record in the warehouse', false],
                    ['The number of rows added per day', false],
                ],
            ],
            [
                'q' => 'What is a "data lake" and how does it differ from a data warehouse?',
                'opts' => [
                    ['A data lake stores only structured data; a data warehouse stores unstructured data', false],
                    ['A data lake stores raw data in its native format (structured, semi-structured, unstructured); a data warehouse stores cleaned, structured data optimised for querying', true],
                    ['They are the same thing with different names', false],
                    ['A data lake is a physical storage room; a data warehouse is virtual', false],
                ],
            ],
            [
                'q' => 'What is a staging area in a data warehouse pipeline?',
                'opts' => [
                    ['The production database where end users query data', false],
                    ['A temporary holding zone where raw extracted data lands before being transformed and loaded into the warehouse', true],
                    ['The server room where the warehouse is hosted', false],
                    ['The dashboard layer visible to business users', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 23 — Data Warehousing (Newbie).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
    }
}