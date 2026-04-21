<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Challenge;
use App\Models\ChallengeCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CodingChallengeTitlesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Schema::disableForeignKeyConstraints();
        // DB::table('challenges')->truncate(); // Clear existing challenges to prevent duplicates
        // Schema::enableForeignKeyConstraints();
        // 1. Fetch all 5 categories (Newbie, University, etc.)
        $categories = ChallengeCategory::all();

        if ($categories->isEmpty()) {
            $this->command->error("Please run the ChallengeCategorySeeder first!");
            return;
        }

        // 2. The complete 24-module curriculum with descriptions
        $modules = [
            // YEAR 1
            ['title' => 'Basics of Python Programming', 'desc' => 'Variables, data types, control flow, functions, and file I/O. Your gateway into data science programming using Python.'],
            ['title' => 'Basics of Statistics', 'desc' => 'Descriptive statistics, probability fundamentals, distributions, and hypothesis testing — the backbone of data analysis.'],
            ['title' => 'Introduction to Data Science', 'desc' => 'Overview of the data science lifecycle, tools, roles, and real-world applications across industries.'],
            ['title' => 'Mathematical Analysis I', 'desc' => 'Limits, continuity, differentiation, and integration. The mathematical foundation for machine learning and modeling.'],
            ['title' => 'Methods of Proof', 'desc' => 'Logic, set theory, mathematical induction, and proof techniques essential for rigorous data science reasoning.'],
            ['title' => 'Modeling and Simulation', 'desc' => 'Building computational models, discrete event simulation, Monte Carlo methods, and systems analysis.'],
            
            // YEAR 2
            ['title' => 'Algorithms & Data Structures for Data Scientists', 'desc' => 'Arrays, trees, graphs, sorting, searching, and complexity analysis applied to data-heavy problems.'],
            ['title' => 'Statistical Methods & Experimental Design', 'desc' => 'ANOVA, regression analysis, experimental design principles, sampling strategies, and inferential testing.'],
            ['title' => 'Applied Matrix Analysis', 'desc' => 'Linear algebra for data science — vectors, eigenvalues, SVD, PCA, and matrix decomposition techniques.'],
            ['title' => 'Database Management for Data Science', 'desc' => 'Relational databases, SQL querying, NoSQL systems, data pipelines, and schema design for analytics workloads.'],
            ['title' => 'Introduction to Bayesian Data Analysis', 'desc' => "Bayes' theorem, prior/posterior distributions, MCMC sampling, and probabilistic inference for real-world data."],
            ['title' => 'Introductory Forecasting', 'desc' => 'Time series analysis, trend decomposition, exponential smoothing, ARIMA, and forecast evaluation metrics.'],
            
            // YEAR 3
            ['title' => 'Introduction to Optimization Techniques', 'desc' => 'Gradient descent, convex optimization, linear programming, and metaheuristics for machine learning applications.'],
            ['title' => 'Machine Learning 1: Supervised Learning', 'desc' => 'Linear/logistic regression, decision trees, SVMs, k-NN, ensemble methods, and model evaluation pipelines.'],
            ['title' => 'Data Visualization', 'desc' => 'Principles of visual encoding, interactive charts with matplotlib/seaborn/plotly, dashboards, and storytelling with data.'],
            ['title' => 'Multivariate Analysis', 'desc' => 'PCA, factor analysis, cluster analysis, discriminant analysis, and canonical correlation for high-dimensional data.'],
            ['title' => 'Deep Learning', 'desc' => 'Neural networks, backpropagation, CNNs, RNNs, transformers, and training strategies using PyTorch/TensorFlow.'],
            ['title' => 'Privacy, Ethics & Data Governance', 'desc' => 'Data privacy laws (GDPR, DPA), algorithmic fairness, bias in AI, responsible data use, and governance frameworks.'],
            
            // YEAR 4
            ['title' => 'Introduction to Artificial Intelligence', 'desc' => 'Search algorithms, knowledge representation, planning, natural language processing, and the AI landscape.'],
            ['title' => 'Analysis of Unstructured Data', 'desc' => 'Text mining, NLP, sentiment analysis, image processing, web scraping, and feature extraction from raw data.'],
            ['title' => 'Machine Learning 2: Unsupervised Learning', 'desc' => 'Clustering, dimensionality reduction, anomaly detection, GANs, autoencoders, and self-supervised learning.'],
            ['title' => 'Big Data & Cloud Computing', 'desc' => 'Hadoop, Spark, distributed computing, cloud platforms (AWS/GCP/Azure), streaming data, and scalable pipelines.'],
            ['title' => 'Data Warehousing', 'desc' => 'Star/snowflake schemas, ETL pipelines, OLAP cubes, data marts, and enterprise analytics infrastructure.'],
            ['title' => 'Sequential Decision Making', 'desc' => 'Markov decision processes, reinforcement learning, Q-learning, policy gradients, and real-world decision optimization.'],
        
        ];

        
        // 3. Seed all 24 titles into all 5 categories (120 total challenges)
        $default_time_limit = 1200; // Default 15 minutes for all challenges (can be overridden in specific question seeders if needed)
        $default_xp = 600; // Default XP for all challenges (can be overridden in specific question seeders if needed)
        foreach ($categories as $category) {
            $order_index= 1;
            foreach ($modules as $module) {
                $this->command->info("{$category->id} Seeding challenges for category: {$category->name}");
                $this->command->info(" - Challenge: {$module['title']}");
                Challenge::create([
                    'challenge_category_id' => $category->id,
                    'title' => $module['title'],
                    'description' => $module['desc'],
                    'time_limit_seconds' => $default_time_limit,
                    'base_xp' => $default_xp,
                    'order_index' => $order_index,
                    'is_coding_challenge' => 1, // Mark this as a coding challenge
                ]);
                $order_index++;
            }
            $default_time_limit += 900; // Increase time limit by 1 minute for each category (just as an example)
            $default_xp += 300; // Increase XP by 100 for each category (
        }
    }
}

// I SHOULD ADD A NEW COLUMN TO THE CHALLENGES TABLE CALLED "is_coding_challenge" (tinyInteger) TO DIFFERENTIATE BETWEEN QUIZ CHALLENGES AND CODING CHALLENGES. THEN, IN THE SEEDER FOR THE CODING CHALLENGES (WHICH I HAVEN'T CREATED YET), I CAN SET THIS TO TRUE. THIS WAY, WHEN I FETCH CHALLENGES FOR THE CODING CHALLENGE MAP, I CAN FILTER BY THIS COLUMN.