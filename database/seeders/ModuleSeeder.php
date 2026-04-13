<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $modules = [
            // ════════ YEAR 1 ════════
            [
                'title' => 'Basics of Python Programming',
                'description' => 'Variables, data types, control flow, functions, and file I/O. Your gateway into data science programming using Python.',
                'year_level' => 'Year 1',
                'order_index' => 1,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Basics of Statistics',
                'description' => 'Descriptive statistics, probability fundamentals, distributions, and hypothesis testing — the backbone of data analysis.',
                'year_level' => 'Year 1',
                'order_index' => 2,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Introduction to Data Science',
                'description' => 'Overview of the data science lifecycle, tools, roles, and real-world applications across industries.',
                'year_level' => 'Year 1',
                'order_index' => 3,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Mathematical Analysis I',
                'description' => 'Limits, continuity, differentiation, and integration. The mathematical foundation for machine learning and modeling.',
                'year_level' => 'Year 1',
                'order_index' => 4,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Methods of Proof',
                'description' => 'Logic, set theory, mathematical induction, and proof techniques essential for rigorous data science reasoning.',
                'year_level' => 'Year 1',
                'order_index' => 5,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Modeling and Simulation',
                'description' => 'Building computational models, discrete event simulation, Monte Carlo methods, and systems analysis.',
                'year_level' => 'Year 1',
                'order_index' => 6,
                'is_boss' => true, // Milestone Module
                'xp_reward' => 300,
            ],

            // ════════ YEAR 2 ════════
            [
                'title' => 'Algorithms & Data Structures for Data Scientists',
                'description' => 'Arrays, trees, graphs, sorting, searching, and complexity analysis applied to data-heavy problems.',
                'year_level' => 'Year 2',
                'order_index' => 7,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Statistical Methods & Experimental Design',
                'description' => 'ANOVA, regression analysis, experimental design principles, sampling strategies, and inferential testing.',
                'year_level' => 'Year 2',
                'order_index' => 8,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Applied Matrix Analysis',
                'description' => 'Linear algebra for data science — vectors, eigenvalues, SVD, PCA, and matrix decomposition techniques.',
                'year_level' => 'Year 2',
                'order_index' => 9,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Database Management for Data Science',
                'description' => 'Relational databases, SQL querying, NoSQL systems, data pipelines, and schema design for analytics workloads.',
                'year_level' => 'Year 2',
                'order_index' => 10,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Introduction to Bayesian Data Analysis',
                'description' => "Bayes' theorem, prior/posterior distributions, MCMC sampling, and probabilistic inference for real-world data.",
                'year_level' => 'Year 2',
                'order_index' => 11,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Introductory Forecasting',
                'description' => 'Time series analysis, trend decomposition, exponential smoothing, ARIMA, and forecast evaluation metrics.',
                'year_level' => 'Year 2',
                'order_index' => 12,
                'is_boss' => true, // Milestone Module
                'xp_reward' => 300,
            ],

            // ════════ YEAR 3 ════════
            [
                'title' => 'Introduction to Optimization Techniques',
                'description' => 'Gradient descent, convex optimization, linear programming, and metaheuristics for machine learning applications.',
                'year_level' => 'Year 3',
                'order_index' => 13,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Machine Learning 1: Supervised Learning',
                'description' => 'Linear/logistic regression, decision trees, SVMs, k-NN, ensemble methods, and model evaluation pipelines.',
                'year_level' => 'Year 3',
                'order_index' => 14,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Data Visualization',
                'description' => 'Principles of visual encoding, interactive charts with matplotlib/seaborn/plotly, dashboards, and storytelling with data.',
                'year_level' => 'Year 3',
                'order_index' => 15,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Multivariate Analysis',
                'description' => 'PCA, factor analysis, cluster analysis, discriminant analysis, and canonical correlation for high-dimensional data.',
                'year_level' => 'Year 3',
                'order_index' => 16,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Deep Learning',
                'description' => 'Neural networks, backpropagation, CNNs, RNNs, transformers, and training strategies using PyTorch/TensorFlow.',
                'year_level' => 'Year 3',
                'order_index' => 17,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Privacy, Ethics & Data Governance',
                'description' => 'Data privacy laws (GDPR, DPA), algorithmic fairness, bias in AI, responsible data use, and governance frameworks.',
                'year_level' => 'Year 3',
                'order_index' => 18,
                'is_boss' => true, // Milestone Module
                'xp_reward' => 300,
            ],

            // ════════ YEAR 4 ════════
            [
                'title' => 'Introduction to Artificial Intelligence',
                'description' => 'Search algorithms, knowledge representation, planning, natural language processing, and the AI landscape.',
                'year_level' => 'Year 4',
                'order_index' => 19,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Analysis of Unstructured Data',
                'description' => 'Text mining, NLP, sentiment analysis, image processing, web scraping, and feature extraction from raw data.',
                'year_level' => 'Year 4',
                'order_index' => 20,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Machine Learning 2: Unsupervised Learning',
                'description' => 'Clustering, dimensionality reduction, anomaly detection, GANs, autoencoders, and self-supervised learning.',
                'year_level' => 'Year 4',
                'order_index' => 21,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Big Data & Cloud Computing',
                'description' => 'Hadoop, Spark, distributed computing, cloud platforms (AWS/GCP/Azure), streaming data, and scalable pipelines.',
                'year_level' => 'Year 4',
                'order_index' => 22,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Data Warehousing',
                'description' => 'Star/snowflake schemas, ETL pipelines, OLAP cubes, data marts, and enterprise analytics infrastructure.',
                'year_level' => 'Year 4',
                'order_index' => 23,
                'is_boss' => false,
                'xp_reward' => 100,
            ],
            [
                'title' => 'Sequential Decision Making',
                'description' => 'Markov decision processes, reinforcement learning, Q-learning, policy gradients, and real-world decision optimization.',
                'year_level' => 'Year 4',
                'order_index' => 24,
                'is_boss' => true, // Final Milestone Module
                'xp_reward' => 300,
            ],
        ];

        // Insert into database with timestamps
        foreach ($modules as $module) {
            $module['created_at'] = $now;
            $module['updated_at'] = $now;
            DB::table('modules')->updateOrInsert(
                ['order_index' => $module['order_index']], // Prevent duplicates if run twice
                $module
            );
        }
    }
}