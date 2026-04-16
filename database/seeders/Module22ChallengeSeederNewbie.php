<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module22ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 22 — Big Data & Cloud Computing (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Big Data & Cloud Computing',
            'description'           => 'Test your basic understanding of Big Data and Cloud Computing — what they are, why they matter, and the key tools and concepts that power the modern data world. No prior experience required!',
            'time_limit_seconds'    => 900,
            'base_xp'               => 500,
            'order_index'           => 22,
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions on Big Data & Cloud Computing...");

        $qaData = [

            // ── 22.1 WHAT IS BIG DATA? THE 5 Vs ─────────────────────────
            [
                'q' => 'What is "Big Data"?',
                'opts' => [
                    ['Data stored on a very large hard drive', false],
                    ['Datasets so large, fast, or complex that traditional tools cannot process them effectively', true],
                    ['Any dataset with more than 1,000 rows', false],
                    ['Data that is only used by large companies', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT one of the original "3 Vs" of Big Data?',
                'opts' => [
                    ['Volume', false],
                    ['Velocity', false],
                    ['Veracity', true],
                    ['Variety', false],
                ],
            ],
            [
                'q' => 'What does "Volume" mean in the context of Big Data?',
                'opts' => [
                    ['How loud the data is', false],
                    ['The sheer amount or size of data being generated and stored', true],
                    ['How many different types of data exist', false],
                    ['How quickly data is processed', false],
                ],
            ],
            [
                'q' => 'What does "Velocity" mean in the context of Big Data?',
                'opts' => [
                    ['The physical speed of the storage device', false],
                    ['The speed at which data is generated, collected, and processed', true],
                    ['The number of different data formats', false],
                    ['The accuracy of the data', false],
                ],
            ],
            [
                'q' => 'What does "Variety" mean in the context of Big Data?',
                'opts' => [
                    ['The number of servers in a data centre', false],
                    ['The different types and formats of data — structured, semi-structured, and unstructured', true],
                    ['How many copies of data are stored', false],
                    ['The speed at which data is deleted', false],
                ],
            ],
            [
                'q' => 'Social media posts, images, and videos are examples of which type of Big Data?',
                'opts' => [
                    ['Structured data — stored in neat rows and columns', false],
                    ['Unstructured data — no fixed format or schema', true],
                    ['Transactional data — always stored in databases', false],
                    ['Batch data — only processed once a day', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of "high velocity" data?',
                'opts' => [
                    ['Annual census records updated once a year', false],
                    ['Monthly sales reports exported to a spreadsheet', false],
                    ['Stock market prices updating thousands of times per second', true],
                    ['A customer address book updated manually', false],
                ],
            ],

            // ── 22.2 CLOUD COMPUTING: IaaS, PaaS, SaaS ───────────────────
            [
                'q' => 'What is cloud computing?',
                'opts' => [
                    ['Storing data on a floppy disk in the shape of a cloud', false],
                    ['Delivering computing services — servers, storage, databases, software — over the internet on demand', true],
                    ['A type of weather forecasting using computers', false],
                    ['A computer network that only works outdoors', false],
                ],
            ],
            [
                'q' => 'What does "IaaS" stand for?',
                'opts' => [
                    ['Internet as a Service', false],
                    ['Infrastructure as a Service', true],
                    ['Integration as a System', false],
                    ['Input and a Server', false],
                ],
            ],
            [
                'q' => 'What does "PaaS" stand for?',
                'opts' => [
                    ['Platform as a Service', true],
                    ['Processing as a System', false],
                    ['Pipeline and a Server', false],
                    ['Packet as a Service', false],
                ],
            ],
            [
                'q' => 'What does "SaaS" stand for?',
                'opts' => [
                    ['Storage as a System', false],
                    ['Software as a Service', true],
                    ['Server as a Solution', false],
                    ['Scaling as a Service', false],
                ],
            ],
            [
                'q' => 'Google Docs and Gmail are examples of which cloud service model?',
                'opts' => [
                    ['IaaS', false],
                    ['PaaS', false],
                    ['SaaS', true],
                    ['DaaS (Database as a Service)', false],
                ],
            ],
            [
                'q' => 'Which of the following are the three major cloud providers?',
                'opts' => [
                    ['Amazon Web Services, Microsoft Azure, Google Cloud Platform', true],
                    ['Facebook Cloud, Twitter Cloud, LinkedIn Cloud', false],
                    ['IBM, Oracle, SAP only', false],
                    ['Dropbox, iCloud, OneDrive', false],
                ],
            ],
            [
                'q' => 'What is a key advantage of using cloud computing over owning your own servers?',
                'opts' => [
                    ['Cloud computing is always faster than on-premise servers', false],
                    ['You only pay for what you use and can scale resources up or down on demand', true],
                    ['Cloud computing requires no internet connection', false],
                    ['Cloud data is immune to security breaches', false],
                ],
            ],

            // ── 22.3 DISTRIBUTED STORAGE: HDFS & OBJECT STORAGE ──────────
            [
                'q' => 'What does HDFS stand for?',
                'opts' => [
                    ['Hard Drive File System', false],
                    ['Hadoop Distributed File System', true],
                    ['High-Definition File Storage', false],
                    ['Hierarchical Data Format System', false],
                ],
            ],
            [
                'q' => 'HDFS is designed to store very large files by:',
                'opts' => [
                    ['Compressing all files into a single zip archive', false],
                    ['Splitting files into blocks and distributing them across many machines in a cluster', true],
                    ['Storing every file on exactly one dedicated server', false],
                    ['Using only solid-state drives (SSDs)', false],
                ],
            ],
            [
                'q' => 'What is "object storage" in cloud computing?',
                'opts' => [
                    ['Storage designed for 3D objects and CAD files only', false],
                    ['A flat storage system where data is stored as objects (files) with metadata and a unique ID, ideal for unstructured data', true],
                    ['A relational database that stores Python objects', false],
                    ['Storage that requires an object-oriented programming language to access', false],
                ],
            ],
            [
                'q' => 'Amazon S3, Google Cloud Storage, and Azure Blob Storage are examples of:',
                'opts' => [
                    ['Relational databases', false],
                    ['Cloud object storage services', true],
                    ['Real-time streaming platforms', false],
                    ['Machine learning model registries', false],
                ],
            ],
            [
                'q' => 'What is a "columnar storage format" used for in Big Data?',
                'opts' => [
                    ['Storing data in vertical columns in a spreadsheet application', false],
                    ['Storing each column of a table together on disk, making queries that read only a few columns much faster', true],
                    ['A way to encrypt data column by column', false],
                    ['A format for storing images side by side', false],
                ],
            ],

            // ── 22.4 APACHE SPARK ─────────────────────────────────────────
            [
                'q' => 'What is Apache Spark?',
                'opts' => [
                    ['A cloud storage service from Amazon', false],
                    ['An open-source distributed computing engine designed for fast, large-scale data processing', true],
                    ['A relational database management system', false],
                    ['A tool for building websites', false],
                ],
            ],
            [
                'q' => 'Apache Spark is often described as much faster than Hadoop MapReduce because it:',
                'opts' => [
                    ['Uses more servers', false],
                    ['Processes data in-memory rather than writing intermediate results to disk', true],
                    ['Only processes small datasets', false],
                    ['Uses Python instead of Java', false],
                ],
            ],
            [
                'q' => 'What is an RDD in Apache Spark?',
                'opts' => [
                    ['Rapid Data Download', false],
                    ['Resilient Distributed Dataset — the fundamental data structure in Spark representing a distributed collection of objects', true],
                    ['Remote Database Driver', false],
                    ['Relational Data Definition', false],
                ],
            ],
            [
                'q' => 'Which Python library allows you to use Apache Spark from Python?',
                'opts' => [
                    ['pandas', false],
                    ['PySpark', true],
                    ['NumPy', false],
                    ['scikit-learn', false],
                ],
            ],

            // ── 22.5 APACHE KAFKA ─────────────────────────────────────────
            [
                'q' => 'What is Apache Kafka?',
                'opts' => [
                    ['A database for storing structured tabular data', false],
                    ['A distributed event streaming platform used to collect, store, and process real-time data streams', true],
                    ['A machine learning framework for deep learning', false],
                    ['A cloud provider like AWS or Azure', false],
                ],
            ],
            [
                'q' => 'In Kafka, what is a "topic"?',
                'opts' => [
                    ['The subject line of an email message', false],
                    ['A named category or feed to which messages (events) are published and from which consumers read', true],
                    ['A type of Kafka server', false],
                    ['A compressed archive of Kafka data', false],
                ],
            ],
            [
                'q' => 'In Kafka, a "producer" is:',
                'opts' => [
                    ['A system that reads messages from a Kafka topic', false],
                    ['A system that writes (publishes) messages to a Kafka topic', true],
                    ['The main Kafka server that manages all topics', false],
                    ['A monitoring tool for Kafka clusters', false],
                ],
            ],
            [
                'q' => 'In Kafka, a "consumer" is:',
                'opts' => [
                    ['A system that writes messages to a Kafka topic', false],
                    ['A system that reads (subscribes to) messages from a Kafka topic', true],
                    ['The Kafka cluster manager', false],
                    ['A tool for compressing Kafka messages', false],
                ],
            ],

            // ── 22.6 CLOUD DATA WAREHOUSES ────────────────────────────────
            [
                'q' => 'What is a data warehouse?',
                'opts' => [
                    ['A physical building where computer servers are stored', false],
                    ['A centralised repository for structured, historical data used for reporting and analytics', true],
                    ['A tool for real-time stream processing', false],
                    ['A type of NoSQL database', false],
                ],
            ],
            [
                'q' => 'Which of the following are cloud-based data warehouses?',
                'opts' => [
                    ['MySQL, PostgreSQL, SQLite', false],
                    ['Snowflake, Google BigQuery, Amazon Redshift', true],
                    ['Hadoop, Spark, Kafka', false],
                    ['TensorFlow, PyTorch, Keras', false],
                ],
            ],
            [
                'q' => 'What is a key advantage of cloud data warehouses like BigQuery over traditional on-premise warehouses?',
                'opts' => [
                    ['Cloud warehouses do not support SQL queries', false],
                    ['Cloud warehouses scale storage and compute independently and charge per query, eliminating the need to manage physical hardware', true],
                    ['Cloud warehouses only store images and videos', false],
                    ['Traditional warehouses are always faster for large datasets', false],
                ],
            ],

            // ── 22.7 DATA PIPELINES, ETL/ELT & AIRFLOW ───────────────────
            [
                'q' => 'What does ETL stand for in data engineering?',
                'opts' => [
                    ['Extract, Train, Load', false],
                    ['Extract, Transform, Load', true],
                    ['Encode, Transfer, Label', false],
                    ['Export, Test, Launch', false],
                ],
            ],
            [
                'q' => 'In an ETL pipeline, what does the "Transform" step do?',
                'opts' => [
                    ['Copies raw data from a source to a destination unchanged', false],
                    ['Cleans, filters, aggregates, or reformats data before loading it into the destination', true],
                    ['Monitors the pipeline for errors', false],
                    ['Backs up all data to a secondary storage location', false],
                ],
            ],
            [
                'q' => 'What is Apache Airflow?',
                'opts' => [
                    ['A real-time streaming tool similar to Kafka', false],
                    ['A workflow orchestration tool used to schedule, monitor, and manage data pipelines', true],
                    ['A cloud object storage service', false],
                    ['A SQL query engine for data warehouses', false],
                ],
            ],
            [
                'q' => 'In Apache Airflow, what is a "DAG"?',
                'opts' => [
                    ['A Database Access Gateway', false],
                    ['A Directed Acyclic Graph — a collection of tasks with defined dependencies, representing a data pipeline workflow', true],
                    ['A Data Aggregation Group', false],
                    ['A Distributed Application Grid', false],
                ],
            ],

            // ── 22.8 DATA LAKES, LAKEHOUSES & DELTA LAKE ─────────────────
            [
                'q' => 'What is a data lake?',
                'opts' => [
                    ['A large outdoor reservoir used to cool data centre servers', false],
                    ['A centralised storage repository that holds raw data in its native format — structured, semi-structured, and unstructured', true],
                    ['A type of relational database optimised for analytics', false],
                    ['A Kafka topic that stores large messages', false],
                ],
            ],
            [
                'q' => 'What is the main difference between a data lake and a data warehouse?',
                'opts' => [
                    ['Data lakes store only structured data; data warehouses store only unstructured data', false],
                    ['Data lakes store raw, unprocessed data in any format; data warehouses store structured, cleaned data ready for analysis', true],
                    ['Data warehouses are always bigger than data lakes', false],
                    ['Data lakes are only used by small companies', false],
                ],
            ],
            [
                'q' => 'What is Delta Lake?',
                'opts' => [
                    ['A physical lake in the Amazon AWS data centre', false],
                    ['An open-source storage layer that adds ACID transactions and reliability to data lakes', true],
                    ['A type of relational database from Microsoft', false],
                    ['A Kafka connector for stream processing', false],
                ],
            ],
            [
                'q' => 'What is a "data lakehouse"?',
                'opts' => [
                    ['A combination of a data lake and a warehouse — storing raw data like a lake while providing the reliability and query performance of a warehouse', true],
                    ['A lakehouse is a physical data centre built next to a lake for cooling', false],
                    ['A lakehouse is a small data warehouse for startups', false],
                    ['A lakehouse only stores machine learning model artefacts', false],
                ],
            ],

            // ── 22.9 CLOUD SECURITY, GOVERNANCE & COMPLIANCE ─────────────
            [
                'q' => 'What is "data governance" in the context of Big Data?',
                'opts' => [
                    ['The process of deleting old data from cloud storage', false],
                    ['The set of policies, standards, and processes that ensure data is accurate, secure, and used appropriately', true],
                    ['Governing which employees can buy cloud computing subscriptions', false],
                    ['A legal requirement to store data only in one country', false],
                ],
            ],
            [
                'q' => 'What does GDPR stand for?',
                'opts' => [
                    ['General Data Processing Regulation', false],
                    ['General Data Protection Regulation', true],
                    ['Global Database Privacy Rules', false],
                    ['Government Data Privacy Requirements', false],
                ],
            ],
            [
                'q' => 'What is "data encryption" in cloud security?',
                'opts' => [
                    ['Deleting data so it cannot be accessed', false],
                    ['Converting data into a coded format so that only authorised parties can read it', true],
                    ['Compressing data to save storage space', false],
                    ['Backing up data to a secondary location', false],
                ],
            ],
            [
                'q' => 'What is the "principle of least privilege" in cloud security?',
                'opts' => [
                    ['Only the CEO should have access to cloud data', false],
                    ['Users and systems should be granted only the minimum permissions needed to do their job — nothing more', true],
                    ['All employees should have equal access to all data', false],
                    ['Sensitive data should be stored in the cheapest cloud tier', false],
                ],
            ],

            // ── 22.10 MLOps & DEPLOYING ML AT SCALE ──────────────────────
            [
                'q' => 'What does MLOps stand for?',
                'opts' => [
                    ['Machine Language Operations', false],
                    ['Machine Learning Operations — the practice of deploying, monitoring, and maintaining ML models in production', true],
                    ['Multi-Level Optimisation for Servers', false],
                    ['Model Learning Over Processing', false],
                ],
            ],
            [
                'q' => 'What is a "model registry" in MLOps?',
                'opts' => [
                    ['A government list of all AI companies', false],
                    ['A centralised store for tracking trained ML models, their versions, metadata, and deployment status', true],
                    ['A database of training datasets', false],
                    ['A tool for writing machine learning code', false],
                ],
            ],
            [
                'q' => 'What is "model drift" in machine learning production?',
                'opts' => [
                    ['When a model accidentally moves to a different server', false],
                    ['When a deployed model\'s performance degrades over time because the real-world data distribution changes from what it was trained on', true],
                    ['When a model uses too much memory', false],
                    ['When the model\'s code is accidentally overwritten', false],
                ],
            ],
            [
                'q' => 'What is a Docker container in the context of deploying ML models?',
                'opts' => [
                    ['A physical shipping container used to transport servers', false],
                    ['A lightweight, portable package that bundles an application and all its dependencies, ensuring it runs consistently anywhere', true],
                    ['A cloud storage bucket for model files', false],
                    ['A type of database optimised for model predictions', false],
                ],
            ],
            [
                'q' => 'What is Kubernetes used for in a cloud ML deployment?',
                'opts' => [
                    ['Writing Python code for machine learning models', false],
                    ['Orchestrating and scaling containerised applications (like ML model servers) across a cluster of machines', true],
                    ['Storing training data in a data lake', false],
                    ['Building dashboards for data visualisation', false],
                ],
            ],
            [
                'q' => 'What is "CI/CD" in the context of MLOps?',
                'opts' => [
                    ['Cloud Infrastructure and Compute Distribution', false],
                    ['Continuous Integration and Continuous Deployment — automating the testing and deployment of code and models', true],
                    ['Clustered Inference and Centralised Data', false],
                    ['Classification Integration and Custom Deployment', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 22 — Big Data & Cloud Computing (Newbie).");
    }
}