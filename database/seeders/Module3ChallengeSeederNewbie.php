<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module3ChallengeSeederNewbie extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'newbie')->first();

        if (!$category) {
            $this->command->error("Newbie category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        /*
         * MODULE ORDERING FIX:
         * The map page orders challenges by `id ASC`. If other challenges were
         * seeded before this one, they'd get lower IDs and push Module 3 out of position.
         *
         * Solution: use the `order_index` column to control display order.
         * For now we also delete any existing challenges for this category
         * and re-insert so IDs are clean.
         */

        // Remove existing challenges for this category (cascades to questions/options)
        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 3 — Introduction to Data Science (Newbie)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Data Science',
            'description'           => 'Test your very basic understanding of what Data Science is — no prior experience required! This challenge covers what data science is, why it matters, common tools, and beginner vocabulary every aspiring data scientist should know.',
            'time_limit_seconds'    => 900, // 15 minutes for 50 questions
            'base_xp'               => 500,
            'order_index'           => 3, // Ensure this appears third on the map
        ]);

        $this->command->info("Seeding 50 newbie-friendly questions...");

        $qaData = [

            // ── WHAT IS DATA SCIENCE ──────────────────────────────────────
            [
                'q' => 'What is Data Science?',
                'opts' => [
                    ['A subject about drawing and designing things', false],
                    ['A field that uses data, statistics, and programming to gain insights and make decisions', true],
                    ['A branch of chemistry that studies materials', false],
                    ['A type of social media platform', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of data?',
                'opts' => [
                    ['A painting on a wall', false],
                    ['A temperature reading of 30°C recorded every hour', true],
                    ['A physical book on a shelf', false],
                    ['A song played on the radio', false],
                ],
            ],
            [
                'q' => 'What does a data scientist mainly do?',
                'opts' => [
                    ['Design buildings and bridges', false],
                    ['Collect, analyze, and interpret large amounts of data to help solve problems', true],
                    ['Write news articles and blog posts', false],
                    ['Manage social media accounts', false],
                ],
            ],
            [
                'q' => 'Which of the following is NOT a step in the basic data science process?',
                'opts' => [
                    ['Collecting data', false],
                    ['Cleaning data', false],
                    ['Painting the data', true],
                    ['Analyzing data', false],
                ],
            ],
            [
                'q' => 'Why is data important in today\'s world?',
                'opts' => [
                    ['Data is only important for scientists in laboratories', false],
                    ['Data helps individuals and organizations make better decisions based on facts', true],
                    ['Data is just a collection of random numbers with no meaning', false],
                    ['Data is mainly used to decorate presentations', false],
                ],
            ],

            // ── TYPES OF DATA ─────────────────────────────────────────────
            [
                'q' => 'Which of the following is an example of numerical data?',
                'opts' => [
                    ['Your favorite color', false],
                    ['Your name', false],
                    ['Your age (e.g., 20)', true],
                    ['The name of your school', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of categorical data?',
                'opts' => [
                    ['Weight in kilograms', false],
                    ['Number of siblings', false],
                    ['Eye color (brown, blue, green)', true],
                    ['Body temperature in Celsius', false],
                ],
            ],
            [
                'q' => 'What is "structured data"?',
                'opts' => [
                    ['Data stored in a random order with no pattern', false],
                    ['Data organized into rows and columns, like a spreadsheet', true],
                    ['Data that is always about science topics', false],
                    ['Data that can only be images', false],
                ],
            ],
            [
                'q' => 'Which of the following is an example of unstructured data?',
                'opts' => [
                    ['A table of student grades', false],
                    ['A spreadsheet of sales figures', false],
                    ['A collection of social media posts and photos', true],
                    ['A database of employee salaries', false],
                ],
            ],
            [
                'q' => 'What does "raw data" mean?',
                'opts' => [
                    ['Data that has been analyzed and is ready to present', false],
                    ['Data that has not been processed or cleaned yet', true],
                    ['Data collected only from the internet', false],
                    ['Data stored in a cloud server', false],
                ],
            ],

            // ── BASIC TOOLS ───────────────────────────────────────────────
            [
                'q' => 'Which programming language is most commonly used in data science?',
                'opts' => [
                    ['HTML', false],
                    ['CSS', false],
                    ['Python', true],
                    ['PHP', false],
                ],
            ],
            [
                'q' => 'What is Microsoft Excel mainly used for in data science (at a beginner level)?',
                'opts' => [
                    ['Creating websites', false],
                    ['Storing, organizing, and performing basic analysis on tabular data', true],
                    ['Running machine learning models', false],
                    ['Designing graphics and logos', false],
                ],
            ],
            [
                'q' => 'What is a CSV file?',
                'opts' => [
                    ['A type of image file', false],
                    ['A video format used to store recordings', false],
                    ['A plain text file where values are separated by commas, used to store tabular data', true],
                    ['A programming file used to run applications', false],
                ],
            ],
            [
                'q' => 'What does "Jupyter Notebook" allow data scientists to do?',
                'opts' => [
                    ['Design mobile applications', false],
                    ['Write and run code in a browser, with the ability to see results immediately', true],
                    ['Edit video files frame by frame', false],
                    ['Send emails automatically', false],
                ],
            ],
            [
                'q' => 'Which Python library is primarily used for data manipulation and analysis?',
                'opts' => [
                    ['NumPy', false],
                    ['Matplotlib', false],
                    ['Pandas', true],
                    ['Seaborn', false],
                ],
            ],
            [
                'q' => 'Which Python library is used to create charts and graphs?',
                'opts' => [
                    ['Pandas', false],
                    ['Matplotlib', true],
                    ['Scikit-learn', false],
                    ['Flask', false],
                ],
            ],
            [
                'q' => 'What does NumPy primarily help with in data science?',
                'opts' => [
                    ['Building websites', false],
                    ['Performing fast numerical computations with arrays and matrices', true],
                    ['Connecting to databases', false],
                    ['Sending HTTP requests', false],
                ],
            ],

            // ── BASIC STATISTICS ──────────────────────────────────────────
            [
                'q' => 'What is the mean (average) of the numbers: 10, 20, 30?',
                'opts' => [
                    ['30', false],
                    ['20', true],
                    ['10', false],
                    ['60', false],
                ],
            ],
            [
                'q' => 'What is the median of the dataset: [5, 1, 3, 9, 7]?',
                'opts' => [
                    ['1', false],
                    ['5', true],
                    ['9', false],
                    ['3', false],
                ],
            ],
            [
                'q' => 'What is the mode of the dataset: [4, 4, 2, 7, 4, 9]?',
                'opts' => [
                    ['2', false],
                    ['7', false],
                    ['9', false],
                    ['4', true],
                ],
            ],
            [
                'q' => 'What does "frequency" mean in data analysis?',
                'opts' => [
                    ['How fast a computer processes data', false],
                    ['The number of times a particular value appears in a dataset', true],
                    ['The highest value in a dataset', false],
                    ['The difference between the largest and smallest values', false],
                ],
            ],
            [
                'q' => 'If a dataset has values [2, 4, 6, 8, 10], what is the range?',
                'opts' => [
                    ['5', false],
                    ['8', true],
                    ['6', false],
                    ['30', false],
                ],
            ],

            // ── DATA SCIENCE VOCABULARY ────────────────────────────────────
            [
                'q' => 'What is a "dataset"?',
                'opts' => [
                    ['A single data point or number', false],
                    ['A collection of related data organized for analysis', true],
                    ['A type of computer hardware', false],
                    ['A Python programming command', false],
                ],
            ],
            [
                'q' => 'What does EDA stand for in data science?',
                'opts' => [
                    ['Extreme Data Algorithm', false],
                    ['Electronic Data Application', false],
                    ['Exploratory Data Analysis', true],
                    ['Extracted Data Architecture', false],
                ],
            ],
            [
                'q' => 'What does "missing value" mean in a dataset?',
                'opts' => [
                    ['A value that is too large to display', false],
                    ['A blank or empty cell where data should be recorded but isn\'t', true],
                    ['A number that was calculated incorrectly', false],
                    ['A duplicate entry in the dataset', false],
                ],
            ],
            [
                'q' => 'What is an "outlier" in data science?',
                'opts' => [
                    ['A very common value that appears frequently', false],
                    ['A data point that is significantly different from most other values', true],
                    ['A column name in a spreadsheet', false],
                    ['A type of chart used to display data', false],
                ],
            ],
            [
                'q' => 'In a table (DataFrame), what is a "row"?',
                'opts' => [
                    ['A category label for a column', false],
                    ['A single observation or record in the dataset', true],
                    ['A calculation applied to the data', false],
                    ['A type of chart used in data visualization', false],
                ],
            ],
            [
                'q' => 'In a table (DataFrame), what is a "column"?',
                'opts' => [
                    ['A single record or data entry', false],
                    ['A chart type for visualizing data', false],
                    ['A variable or feature that stores a particular type of information', true],
                    ['A Python function for cleaning data', false],
                ],
            ],

            // ── DATA COLLECTION ───────────────────────────────────────────
            [
                'q' => 'Which of the following is a valid way to collect data?',
                'opts' => [
                    ['Guessing numbers randomly', false],
                    ['Surveying people with a questionnaire', true],
                    ['Skipping the data collection step entirely', false],
                    ['Copying answers from a friend', false],
                ],
            ],
            [
                'q' => 'What is web scraping used for?',
                'opts' => [
                    ['Cleaning data inside a database', false],
                    ['Automatically collecting data from websites', true],
                    ['Designing web pages with HTML', false],
                    ['Visualizing data in charts', false],
                ],
            ],
            [
                'q' => 'What does an API allow a data scientist to do?',
                'opts' => [
                    ['Create graphics and logos for presentations', false],
                    ['Retrieve data from an external service or application programmatically', true],
                    ['Delete unwanted files from a computer', false],
                    ['Speed up the training of machine learning models', false],
                ],
            ],

            // ── BASIC VISUALIZATION ────────────────────────────────────────
            [
                'q' => 'What type of chart is best for showing how values change over time?',
                'opts' => [
                    ['Pie chart', false],
                    ['Bar chart', false],
                    ['Line chart', true],
                    ['Scatter plot', false],
                ],
            ],
            [
                'q' => 'What type of chart is best for comparing quantities across different categories?',
                'opts' => [
                    ['Line chart', false],
                    ['Bar chart', true],
                    ['Histogram', false],
                    ['Box plot', false],
                ],
            ],
            [
                'q' => 'What does a pie chart show?',
                'opts' => [
                    ['How two variables relate to each other', false],
                    ['How data changes over time', false],
                    ['The proportion of each category as a slice of a whole circle', true],
                    ['The distribution of values in a numeric column', false],
                ],
            ],
            [
                'q' => 'A scatter plot is used to:',
                'opts' => [
                    ['Show parts of a whole', false],
                    ['Compare categories side by side', false],
                    ['Show the relationship between two numeric variables', true],
                    ['Display a single category\'s change over time', false],
                ],
            ],

            // ── MACHINE LEARNING BASICS (CONCEPTUAL) ──────────────────────
            [
                'q' => 'What is Machine Learning in simple terms?',
                'opts' => [
                    ['Teaching computers to write poetry', false],
                    ['Training a computer program to learn patterns from data and make predictions', true],
                    ['Physically upgrading a computer\'s hardware components', false],
                    ['Building robots that can walk and talk', false],
                ],
            ],
            [
                'q' => 'What is "supervised learning"?',
                'opts' => [
                    ['A teacher physically watching a student program', false],
                    ['Training a model using data that already has known correct answers (labels)', true],
                    ['Training a model on data with no labels at all', false],
                    ['A method to supervise other data scientists on a team', false],
                ],
            ],
            [
                'q' => 'Which of the following is a classification task?',
                'opts' => [
                    ['Predicting tomorrow\'s temperature in degrees', false],
                    ['Estimating the price of a used car', false],
                    ['Deciding if an email is spam (yes) or not spam (no)', true],
                    ['Forecasting next month\'s total sales amount', false],
                ],
            ],
            [
                'q' => 'What is a "label" in a supervised learning dataset?',
                'opts' => [
                    ['The column names of a DataFrame', false],
                    ['The known correct answer or output the model is trained to predict', true],
                    ['A tag added to Python variables', false],
                    ['A type of chart used in EDA', false],
                ],
            ],
            [
                'q' => 'What is the purpose of splitting data into training and testing sets?',
                'opts' => [
                    ['To make the dataset smaller so it loads faster', false],
                    ['To train the model on one portion and evaluate how well it works on unseen data', true],
                    ['To duplicate the data for backup purposes', false],
                    ['To separate numerical and categorical columns', false],
                ],
            ],

            // ── REAL-WORLD APPLICATIONS ───────────────────────────────────
            [
                'q' => 'Which of the following is a real-world application of data science?',
                'opts' => [
                    ['Recommending movies on Netflix based on what you\'ve watched before', true],
                    ['Printing documents from a printer', false],
                    ['Sending an email to a friend', false],
                    ['Installing a mobile app on a smartphone', false],
                ],
            ],
            [
                'q' => 'How does a bank use data science?',
                'opts' => [
                    ['To design the interior of bank branches', false],
                    ['To detect fraudulent transactions by identifying unusual patterns', true],
                    ['To print customer receipts faster', false],
                    ['To organize physical files in storage rooms', false],
                ],
            ],
            [
                'q' => 'Which industry does NOT commonly use data science?',
                'opts' => [
                    ['Healthcare', false],
                    ['Retail and e-commerce', false],
                    ['Finance and banking', false],
                    ['None — all industries use data science in some way', true],
                ],
            ],

            // ── DATA CLEANING BASICS ───────────────────────────────────────
            [
                'q' => 'Why is data cleaning important in data science?',
                'opts' => [
                    ['It makes the data look more colorful in charts', false],
                    ['Errors, duplicates, and missing values in data can lead to inaccurate results', true],
                    ['It increases the size of the dataset automatically', false],
                    ['It is not important — data is always collected perfectly', false],
                ],
            ],
            [
                'q' => 'What is a "duplicate row" in a dataset?',
                'opts' => [
                    ['A row with all missing values', false],
                    ['A row that appears more than once with the same data', true],
                    ['A row that has been deleted from the dataset', false],
                    ['A row containing only numbers', false],
                ],
            ],
            [
                'q' => 'What is one common way to handle a missing value in a numeric column?',
                'opts' => [
                    ['Delete the entire dataset', false],
                    ['Replace it with the mean or median of that column', true],
                    ['Replace it with a random letter', false],
                    ['Leave it blank and continue', false],
                ],
            ],

            // ── DATA ETHICS & GENERAL ─────────────────────────────────────
            [
                'q' => 'What does "data privacy" mean?',
                'opts' => [
                    ['Keeping data in a private folder on your desktop', false],
                    ['The protection of personal information and the rights of individuals regarding their data', true],
                    ['Making data available to everyone on the internet', false],
                    ['Encrypting all data using Python code', false],
                ],
            ],
            [
                'q' => 'What is "bias" in data science?',
                'opts' => [
                    ['A statistical formula used to compute averages', false],
                    ['A systematic error in data or a model that produces unfair or skewed results', true],
                    ['A type of chart that shows bimodal distributions', false],
                    ['A Python library for machine learning', false],
                ],
            ],
            [
                'q' => 'Which of the following BEST describes what "Big Data" refers to?',
                'opts' => [
                    ['Data stored on a very large hard drive', false],
                    ['Datasets so large and complex that traditional tools cannot process them easily', true],
                    ['Data collected only from government sources', false],
                    ['A Python library for working with large files', false],
                ],
            ],
            [
                'q' => 'What does it mean when a model makes a "prediction"?',
                'opts' => [
                    ['It deletes unnecessary rows from a dataset', false],
                    ['It uses patterns learned from data to estimate an output for new, unseen inputs', true],
                    ['It creates a visualization of the dataset', false],
                    ['It resets all variables in a Python script', false],
                ],
            ],
            [
                'q' => 'What is the correct order of the basic data science workflow?',
                'opts' => [
                    ['Analyze → Collect → Clean → Visualize → Conclude', false],
                    ['Collect → Clean → Analyze → Visualize → Conclude', true],
                    ['Visualize → Collect → Clean → Analyze → Conclude', false],
                    ['Clean → Analyze → Collect → Conclude → Visualize', false],
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

        // $this->command->info("✅ Done! 50 questions seeded for Module 3 — Introduction to Data Science.");
        // $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Newbie");
        // $this->command->newLine();
        // $this->command->comment("NOTE: If Module 3 doesn't appear in the correct position on the map, add an `order_index`");
        // $this->command->comment("column to the `challenges` table and update the map controller query:");
        // $this->command->comment("  ->orderBy('order_index', 'asc')");
        // $this->command->comment("Then set order_index = 3 for this challenge.");
    }
}

/*
 * ─── OPTIONAL MIGRATION (if ordering is still wrong after a fresh seed) ──────
 *
 * If you already have challenges with lower IDs (from the old ChallengeSeeder),
 * add an order_index column so you can control the display order independently:
 *
 *   Schema::table('challenges', function (Blueprint $table) {
 *       $table->integer('order_index')->default(0)->after('base_xp');
 *   });
 *
 * Then in ChallengesController::map(), change:
 *   ->orderBy('id', 'asc')
 * to:
 *   ->orderBy('order_index', 'asc')->orderBy('id', 'asc')
 *
 * And set order_index = 3 on this challenge after seeding:
 *   Challenge::where('title', 'Introduction to Data Science')->update(['order_index' => 3]);
 */