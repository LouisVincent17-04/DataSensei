<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module3ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 3 — Introduction to Data Science (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Data Science',
            'description'           => 'Dive slightly deeper into data science concepts — analytical thinking, simple library usage, data wrangling basics, and light statistical reasoning. Designed for university students beginning their data science journey.',
            'time_limit_seconds'    => 1200, // 20 minutes for 50 questions
            'base_xp'               => 750,
            'order_index'           => 3,
        ]);

        $this->command->info("Seeding 50 university-level questions...");

        $qaData = [

            // ── WHAT IS DATA SCIENCE ──────────────────────────────────────
            [
                'q' => 'A data scientist is given 10,000 rows of sales records. Before building any model, what is the FIRST recommended step?',
                'opts' => [
                    ['Train a machine learning model immediately', false],
                    ['Perform Exploratory Data Analysis (EDA) to understand the data', true],
                    ['Delete rows with missing values without inspecting them', false],
                    ['Normalize all columns to a 0–1 range', false],
                ],
            ],
            [
                'q' => 'Which of the following best describes "structured data"?',
                'opts' => [
                    ['Images stored in folders', false],
                    ['Audio recordings from customer calls', false],
                    ['Data organized into rows and columns with fixed types', true],
                    ['Raw HTML from web scraping', false],
                ],
            ],
            [
                'q' => 'In the data science workflow, what does "data wrangling" primarily involve?',
                'opts' => [
                    ['Training predictive models', false],
                    ['Cleaning, transforming, and reshaping raw data into usable form', true],
                    ['Visualizing final results for stakeholders', false],
                    ['Deploying a model to a web server', false],
                ],
            ],
            [
                'q' => 'A dataset has 1,000 rows. After filtering, only 200 rows remain. What percentage of data was retained?',
                'opts' => [
                    ['20%', true],
                    ['80%', false],
                    ['200%', false],
                    ['2%', false],
                ],
            ],
            [
                'q' => 'Which file format is most commonly used to share tabular datasets in data science?',
                'opts' => [
                    ['.docx', false],
                    ['.csv', true],
                    ['.mp4', false],
                    ['.exe', false],
                ],
            ],

            // ── NUMPY ─────────────────────────────────────────────────────
            [
                'q' => "What does the following NumPy code return?\n\nimport numpy as np\na = np.array([1, 2, 3, 4, 5])\nprint(a.mean())",
                'opts' => [
                    ['1', false],
                    ['5', false],
                    ['3.0', true],
                    ['15', false],
                ],
            ],
            [
                'q' => "What is the shape of the array created by `np.zeros((3, 4))`?",
                'opts' => [
                    ['(3,)', false],
                    ['(4, 3)', false],
                    ['(3, 4)', true],
                    ['(12,)', false],
                ],
            ],
            [
                'q' => "What does `np.arange(2, 10, 2)` produce?",
                'opts' => [
                    ['[2, 4, 6, 8, 10]', false],
                    ['[2, 4, 6, 8]', true],
                    ['[0, 2, 4, 6, 8]', false],
                    ['[2, 10, 2]', false],
                ],
            ],
            [
                'q' => "What does the following code output?\n\nimport numpy as np\na = np.array([10, 20, 30])\nprint(a * 2)",
                'opts' => [
                    ['[10, 20, 30, 10, 20, 30]', false],
                    ['[20, 40, 60]', true],
                    ['60', false],
                    ['Error', false],
                ],
            ],
            [
                'q' => 'Which NumPy function returns the index of the maximum value in an array?',
                'opts' => [
                    ['np.max()', false],
                    ['np.argmax()', true],
                    ['np.indexOf()', false],
                    ['np.maxindex()', false],
                ],
            ],

            // ── PANDAS ────────────────────────────────────────────────────
            [
                'q' => "What does `df.shape` return for a DataFrame with 100 rows and 5 columns?",
                'opts' => [
                    ['(5, 100)', false],
                    ['100', false],
                    ['(100, 5)', true],
                    ['500', false],
                ],
            ],
            [
                'q' => "What does `df.head(3)` return?",
                'opts' => [
                    ['The last 3 rows', false],
                    ['The first 3 rows', true],
                    ['3 random rows', false],
                    ['The column names only', false],
                ],
            ],
            [
                'q' => "Which Pandas method shows the count, mean, std, and quartiles for numeric columns?",
                'opts' => [
                    ['df.info()', false],
                    ['df.summary()', false],
                    ['df.describe()', true],
                    ['df.stats()', false],
                ],
            ],
            [
                'q' => "How do you select only the column named 'age' from a DataFrame `df`?",
                'opts' => [
                    ["df.column('age')", false],
                    ["df['age']", true],
                    ["df.select('age')", false],
                    ["df->age", false],
                ],
            ],
            [
                'q' => "What does `df.isnull().sum()` tell you?",
                'opts' => [
                    ['The total number of rows in the DataFrame', false],
                    ['The number of missing values per column', true],
                    ['The sum of all numeric values', false],
                    ['Whether any column has duplicate values', false],
                ],
            ],
            [
                'q' => "A DataFrame has a column 'score' with values [85, 90, None, 78]. What does `df['score'].fillna(0)` do?",
                'opts' => [
                    ['Removes the row with None', false],
                    ['Replaces the None with 0', true],
                    ['Replaces all values with 0', false],
                    ['Returns an error', false],
                ],
            ],
            [
                'q' => "Which Pandas method removes duplicate rows from a DataFrame?",
                'opts' => [
                    ['df.removeDuplicates()', false],
                    ['df.distinct()', false],
                    ['df.drop_duplicates()', true],
                    ['df.unique()', false],
                ],
            ],

            // ── MATPLOTLIB & VISUALIZATION ────────────────────────────────
            [
                'q' => 'Which chart type is best for comparing the distribution of a single numeric variable?',
                'opts' => [
                    ['Pie chart', false],
                    ['Histogram', true],
                    ['Scatter plot', false],
                    ['Bar chart', false],
                ],
            ],
            [
                'q' => 'What is the purpose of a scatter plot in data science?',
                'opts' => [
                    ['To show proportions of categories in a whole', false],
                    ['To display the relationship between two numeric variables', true],
                    ['To compare values across categorical groups', false],
                    ['To show data changing over time', false],
                ],
            ],
            [
                'q' => "What does `plt.xlabel('Age')` do in a Matplotlib plot?",
                'opts' => [
                    ['Sets the title of the chart', false],
                    ['Labels the x-axis with the text "Age"', true],
                    ['Creates a new column named Age', false],
                    ['Filters the DataFrame by the Age column', false],
                ],
            ],
            [
                'q' => "What Seaborn function creates a heatmap of a correlation matrix?",
                'opts' => [
                    ['sns.barplot()', false],
                    ['sns.scatter()', false],
                    ['sns.heatmap()', true],
                    ['sns.matrix()', false],
                ],
            ],

            // ── STATISTICS ────────────────────────────────────────────────
            [
                'q' => 'Given the dataset [4, 8, 6, 8, 10], what is the mode?',
                'opts' => [
                    ['6', false],
                    ['8', true],
                    ['7.2', false],
                    ['10', false],
                ],
            ],
            [
                'q' => 'What is the median of the dataset [3, 7, 1, 9, 5]?',
                'opts' => [
                    ['5', true],
                    ['7', false],
                    ['4', false],
                    ['3', false],
                ],
            ],
            [
                'q' => 'A dataset has mean=50, median=48, and mode=45. What does this suggest about the distribution?',
                'opts' => [
                    ['It is symmetric (normal)', false],
                    ['It is negatively skewed (left-skewed)', false],
                    ['It is positively skewed (right-skewed)', true],
                    ['It has no skew at all', false],
                ],
            ],
            [
                'q' => 'Which measure of spread is most affected by extreme outliers?',
                'opts' => [
                    ['Interquartile Range (IQR)', false],
                    ['Median Absolute Deviation', false],
                    ['Range (max − min)', true],
                    ['Mode', false],
                ],
            ],
            [
                'q' => 'If Q1 = 20 and Q3 = 50, what is the IQR?',
                'opts' => [
                    ['70', false],
                    ['30', true],
                    ['35', false],
                    ['25', false],
                ],
            ],

            // ── FEATURE ENGINEERING ───────────────────────────────────────
            [
                'q' => 'What is "one-hot encoding" used for in data science?',
                'opts' => [
                    ['Scaling numeric features to 0–1', false],
                    ['Converting categorical variables into binary (0/1) columns', true],
                    ['Removing outliers from numeric columns', false],
                    ['Filling missing values with the mean', false],
                ],
            ],
            [
                'q' => 'Which scaling technique transforms features so the minimum value is 0 and maximum is 1?',
                'opts' => [
                    ['Standardization (Z-score)', false],
                    ['Log transformation', false],
                    ['Min-Max Normalization', true],
                    ['Binning', false],
                ],
            ],
            [
                'q' => 'You have a column "City" with values ["Manila", "Cebu", "Davao"]. How many binary columns does one-hot encoding produce?',
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['6', false],
                ],
            ],

            // ── EDA ───────────────────────────────────────────────────────
            [
                'q' => 'What does a correlation coefficient of -0.85 between two variables indicate?',
                'opts' => [
                    ['No relationship', false],
                    ['A weak positive relationship', false],
                    ['A strong negative relationship', true],
                    ['A perfect positive relationship', false],
                ],
            ],
            [
                'q' => 'In EDA, a box plot whisker extending far beyond Q3 most likely indicates:',
                'opts' => [
                    ['The data is normally distributed', false],
                    ['There are potential outliers on the high end', true],
                    ['The IQR is zero', false],
                    ['The mean equals the median', false],
                ],
            ],
            [
                'q' => 'A histogram with a long tail on the right side is described as:',
                'opts' => [
                    ['Left-skewed (negatively skewed)', false],
                    ['Symmetric', false],
                    ['Bimodal', false],
                    ['Right-skewed (positively skewed)', true],
                ],
            ],
            [
                'q' => "What does `df.corr()` in Pandas return?",
                'opts' => [
                    ['The covariance matrix of all columns', false],
                    ['The Pearson correlation matrix between all numeric columns', true],
                    ['The sum of all numeric values', false],
                    ['A plot of correlation heatmap', false],
                ],
            ],

            // ── MACHINE LEARNING BASICS ────────────────────────────────────
            [
                'q' => 'In supervised learning, what does a "label" refer to?',
                'opts' => [
                    ['A column used as input for the model', false],
                    ['The known output/target variable the model is trained to predict', true],
                    ['A category assigned to features after clustering', false],
                    ['A metadata tag in the dataset file', false],
                ],
            ],
            [
                'q' => 'Which of these is an example of a classification problem?',
                'opts' => [
                    ['Predicting the price of a house', false],
                    ['Forecasting tomorrow\'s temperature', false],
                    ['Identifying whether an email is spam or not spam', true],
                    ['Estimating a student\'s final exam score', false],
                ],
            ],
            [
                'q' => 'What is the purpose of splitting data into training and test sets?',
                'opts' => [
                    ['To make datasets smaller and easier to process', false],
                    ['To evaluate how well a model generalizes to unseen data', true],
                    ['To remove outliers from the dataset', false],
                    ['To ensure both sets have the same number of rows', false],
                ],
            ],
            [
                'q' => 'If a model achieves 100% accuracy on training data but 55% on test data, what is the likely problem?',
                'opts' => [
                    ['Underfitting', false],
                    ['Data leakage', false],
                    ['Overfitting', true],
                    ['Class imbalance', false],
                ],
            ],
            [
                'q' => 'Which metric measures the percentage of correctly predicted outcomes in a classification model?',
                'opts' => [
                    ['Precision', false],
                    ['Recall', false],
                    ['F1-Score', false],
                    ['Accuracy', true],
                ],
            ],

            // ── UNSUPERVISED LEARNING ─────────────────────────────────────
            [
                'q' => 'What is the main goal of the K-Means clustering algorithm?',
                'opts' => [
                    ['Predict a numeric output variable', false],
                    ['Group data points into K clusters based on similarity', true],
                    ['Reduce overfitting in a classification model', false],
                    ['Find the correlation between two variables', false],
                ],
            ],
            [
                'q' => 'In K-Means, what does the "K" represent?',
                'opts' => [
                    ['The number of features in the dataset', false],
                    ['The number of rows in the dataset', false],
                    ['The number of clusters to form', true],
                    ['The learning rate of the algorithm', false],
                ],
            ],

            // ── TIME SERIES ───────────────────────────────────────────────
            [
                'q' => 'Which of these datasets is a time series?',
                'opts' => [
                    ['A table of student names and grades', false],
                    ['Daily stock prices recorded over one year', true],
                    ['A collection of product images', false],
                    ['A survey of customer satisfaction scores', false],
                ],
            ],
            [
                'q' => 'What does "seasonality" mean in a time series?',
                'opts' => [
                    ['Random noise present in the data', false],
                    ['A long-term upward or downward movement in data', false],
                    ['Repeating patterns at fixed intervals (e.g. every year, quarter, or week)', true],
                    ['A sudden one-time spike in the data', false],
                ],
            ],

            // ── NLP ───────────────────────────────────────────────────────
            [
                'q' => 'What does "tokenization" mean in Natural Language Processing?',
                'opts' => [
                    ['Converting text into images', false],
                    ['Splitting text into individual words or sentences', true],
                    ['Encrypting text for security', false],
                    ['Translating text to another language', false],
                ],
            ],
            [
                'q' => 'In TF-IDF, a word with a very high IDF score is:',
                'opts' => [
                    ['Very common across all documents', false],
                    ['Rare across documents and therefore more distinctive', true],
                    ['Present in every sentence', false],
                    ['A stop word like "the" or "and"', false],
                ],
            ],
            [
                'q' => 'Sentiment analysis classifies text into which general categories?',
                'opts' => [
                    ['True or False', false],
                    ['Spam or Not Spam', false],
                    ['Positive, Negative, or Neutral', true],
                    ['Structured or Unstructured', false],
                ],
            ],

            // ── DATA TYPES & GENERAL DS CONCEPTS ─────────────────────────
            [
                'q' => 'A dataset has 500 rows and 8 columns. How many total data points (cells) does it contain?',
                'opts' => [
                    ['508', false],
                    ['4000', true],
                    ['500', false],
                    ['64', false],
                ],
            ],
            [
                'q' => 'You have a column with values [10, 20, 20, 30, 200]. The value 200 is likely:',
                'opts' => [
                    ['A normal data point', false],
                    ['A missing value', false],
                    ['An outlier', true],
                    ['A duplicate', false],
                ],
            ],
            [
                'q' => 'Which of the following is the best way to handle a numeric column with 5% missing values?',
                'opts' => [
                    ['Delete the entire column', false],
                    ['Impute with the mean or median of that column', true],
                    ['Replace all values in the column with 0', false],
                    ['Leave them as-is; models handle NaN automatically', false],
                ],
            ],
            [
                'q' => 'What is the difference between a population and a sample in data science?',
                'opts' => [
                    ['There is no difference', false],
                    ['A population is a subset; a sample is the full dataset', false],
                    ['A population is the entire group; a sample is a subset drawn from it', true],
                    ['A sample is always more accurate than a population', false],
                ],
            ],
            [
                'q' => 'Which Python library is most commonly used for machine learning tasks in data science?',
                'opts' => [
                    ['Matplotlib', false],
                    ['Pandas', false],
                    ['scikit-learn', true],
                    ['Seaborn', false],
                ],
            ],
            [
                'q' => 'What does it mean for a model to "generalize well"?',
                'opts' => [
                    ['It achieves 100% accuracy on training data', false],
                    ['It performs well on new, unseen data beyond what it was trained on', true],
                    ['It uses the most complex algorithm available', false],
                    ['It requires no hyperparameter tuning', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 3 — Introduction to Data Science (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}