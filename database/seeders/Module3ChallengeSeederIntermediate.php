<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module3ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 3 — Introduction to Data Science (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Data Science',
            'description'           => 'Apply multi-step reasoning across the full data science workflow — data wrangling, statistical analysis, feature engineering, and model evaluation. Questions include code tracing, calculation-based problems, and conceptual analysis.',
            'time_limit_seconds'    => 1500, // 25 minutes
            'base_xp'               => 1000,
            'order_index'           => 3,
        ]);

        $this->command->info("Seeding 50 intermediate-level questions...");

        $qaData = [

            // ── NUMPY — INTERMEDIATE ──────────────────────────────────────
            [
                'q' => "What does the following code output?\n\nimport numpy as np\na = np.array([[1, 2], [3, 4]])\nprint(a.sum(axis=0))",
                'opts' => [
                    ['[4, 6]', true],
                    ['[3, 7]', false],
                    ['10', false],
                    ['[[1, 2], [3, 4]]', false],
                ],
            ],
            [
                'q' => "What is the output of the following?\n\nimport numpy as np\na = np.array([1, 2, 3, 4, 5])\nprint(a[a > 3])",
                'opts' => [
                    ['[1, 2]', false],
                    ['[3, 4, 5]', false],
                    ['[4, 5]', true],
                    ['True, True', false],
                ],
            ],
            [
                'q' => "Given `a = np.array([2, 4, 6, 8])`, what does `np.std(a)` return (rounded to 2 decimal places)?",
                'opts' => [
                    ['2.24', true],
                    ['5.0', false],
                    ['4.0', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "What does `np.dot(np.array([1,2,3]), np.array([4,5,6]))` return?",
                'opts' => [
                    ['[4, 10, 18]', false],
                    ['32', true],
                    ['12', false],
                    ['[5, 7, 9]', false],
                ],
            ],
            [
                'q' => "What is the result of `np.array([1,2,3]).reshape(3,1).shape`?",
                'opts' => [
                    ['(1, 3)', false],
                    ['(3,)', false],
                    ['(3, 1)', true],
                    ['(1,)', false],
                ],
            ],

            // ── PANDAS — INTERMEDIATE ──────────────────────────────────────
            [
                'q' => "Given a DataFrame `df` with a 'salary' column, what does the following return?\n\ndf[df['salary'] > 50000]['salary'].mean()",
                'opts' => [
                    ['The mean salary of all employees', false],
                    ['The mean salary of employees earning more than 50,000', true],
                    ['The count of employees earning above 50,000', false],
                    ['An error — chained indexing is not allowed', false],
                ],
            ],
            [
                'q' => "What does `df.groupby('department')['salary'].mean()` return?",
                'opts' => [
                    ['The total salary by department', false],
                    ['The average salary grouped by each department', true],
                    ['The number of employees in each department', false],
                    ['The highest salary in each department', false],
                ],
            ],
            [
                'q' => "Which Pandas method merges two DataFrames `df1` and `df2` on a common column 'id', keeping only rows that exist in BOTH?",
                'opts' => [
                    ["pd.merge(df1, df2, on='id', how='left')", false],
                    ["pd.merge(df1, df2, on='id', how='outer')", false],
                    ["pd.merge(df1, df2, on='id', how='inner')", true],
                    ["pd.concat([df1, df2])", false],
                ],
            ],
            [
                'q' => "A DataFrame has a 'date' column as strings. After running `df['date'] = pd.to_datetime(df['date'])`, what can you now do that you couldn't before?",
                'opts' => [
                    ['Sort rows alphabetically', false],
                    ['Extract year, month, day and perform time-based filtering', true],
                    ['Multiply the date column by 2', false],
                    ['Nothing changes — to_datetime is optional', false],
                ],
            ],
            [
                'q' => "What does `df.pivot_table(values='sales', index='region', columns='month', aggfunc='sum')` produce?",
                'opts' => [
                    ['A Series of total sales per region', false],
                    ['A 2D table showing total sales for each region-month combination', true],
                    ['A line chart of monthly sales', false],
                    ['A filtered DataFrame with sales above the monthly mean', false],
                ],
            ],
            [
                'q' => "Which Pandas method applies a function to every element in a column?\n\ndf['price'] = df['price']._____(lambda x: x * 1.12)",
                'opts' => [
                    ['apply()', true],
                    ['transform()', false],
                    ['map_col()', false],
                    ['iterate()', false],
                ],
            ],

            // ── STATISTICS — CALCULATION ───────────────────────────────────
            [
                'q' => "A dataset is: [10, 20, 20, 40, 10]. What is the variance?\n\n(Variance = average of squared deviations from the mean)",
                'opts' => [
                    ['100', false],
                    ['120', true],
                    ['96', false],
                    ['80', false],
                ],
            ],
            [
                'q' => "If mean = 70 and standard deviation = 10, what Z-score corresponds to a value of 85?\n\n(Z = (X - mean) / std)",
                'opts' => [
                    ['1.0', false],
                    ['1.5', true],
                    ['2.0', false],
                    ['0.5', false],
                ],
            ],
            [
                'q' => "In a normal distribution, approximately what percentage of data falls within 2 standard deviations of the mean?",
                'opts' => [
                    ['68%', false],
                    ['90%', false],
                    ['95%', true],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => "Given Q1 = 25 and Q3 = 75, any value above _____ is considered an outlier using the IQR method (1.5 × IQR rule).",
                'opts' => [
                    ['100', false],
                    ['125', false],
                    ['150', true],
                    ['75', false],
                ],
            ],
            [
                'q' => "A dataset has values: [5, 5, 5, 5, 100]. Which measure best represents the 'typical' value?",
                'opts' => [
                    ['Mean', false],
                    ['Standard Deviation', false],
                    ['Median or Mode', true],
                    ['Variance', false],
                ],
            ],
            [
                'q' => "You compute the correlation between height and weight: r = 0.82. What does this tell you?\n\nChoose the most accurate interpretation.",
                'opts' => [
                    ['Height causes weight to increase', false],
                    ['There is a strong positive linear relationship between height and weight', true],
                    ['Weight explains 82% of height variation', false],
                    ['The relationship is weak and unreliable', false],
                ],
            ],

            // ── EDA & VISUALIZATION — INTERMEDIATE ────────────────────────
            [
                'q' => "You have a column with 10,000 values where 9,500 are 0 and 500 are various numbers. This is an example of:",
                'opts' => [
                    ['Normal distribution', false],
                    ['Multimodal distribution', false],
                    ['Class imbalance / zero-inflated distribution', true],
                    ['Uniform distribution', false],
                ],
            ],
            [
                'q' => "In a box plot, the line inside the box represents the:",
                'opts' => [
                    ['Mean', false],
                    ['Mode', false],
                    ['Median', true],
                    ['Standard Deviation', false],
                ],
            ],
            [
                'q' => "A correlation heatmap shows that feature A and feature B have a correlation of 0.97. What problem might this cause in a linear regression model?",
                'opts' => [
                    ['Underfitting', false],
                    ['Multicollinearity', true],
                    ['Overfitting due to too few features', false],
                    ['Data leakage', false],
                ],
            ],
            [
                'q' => "What does `df['col'].value_counts(normalize=True)` return?",
                'opts' => [
                    ['The count of each unique value', false],
                    ['The proportion (relative frequency) of each unique value', true],
                    ['The cumulative sum of values', false],
                    ['Values sorted in descending order by index', false],
                ],
            ],
            [
                'q' => "You are analyzing sales data and plot a time series. You notice the same spike every December. This is called:",
                'opts' => [
                    ['Trend', false],
                    ['Noise', false],
                    ['Stationarity', false],
                    ['Seasonality', true],
                ],
            ],

            // ── FEATURE ENGINEERING — INTERMEDIATE ────────────────────────
            [
                'q' => "You have a 'salary' column ranging from 20,000 to 200,000. After Min-Max normalization, what range will the values fall in?",
                'opts' => [
                    ['-1 to 1', false],
                    ['0 to 1', true],
                    ['0 to 100', false],
                    ['The original range is preserved', false],
                ],
            ],
            [
                'q' => "What is the Z-score normalized value of x = 90 when mean = 80 and std = 5?",
                'opts' => [
                    ['0.5', false],
                    ['1.0', false],
                    ['2.0', true],
                    ['10.0', false],
                ],
            ],
            [
                'q' => "A 'Color' column has values: ['Red', 'Blue', 'Green', 'Red']. After one-hot encoding, how many new columns are created?",
                'opts' => [
                    ['1', false],
                    ['2', false],
                    ['3', true],
                    ['4', false],
                ],
            ],
            [
                'q' => "Which of these is a valid reason to apply a log transformation to a feature?",
                'opts' => [
                    ['The feature has many zero values', false],
                    ['The feature is already normally distributed', false],
                    ['The feature is highly right-skewed (long right tail)', true],
                    ['The feature is a categorical variable', false],
                ],
            ],

            // ── MACHINE LEARNING — INTERMEDIATE ────────────────────────────
            [
                'q' => "A classifier predicts spam email. True Positives = 80, False Positives = 20, True Negatives = 90, False Negatives = 10.\n\nWhat is the Precision?\n(Precision = TP / (TP + FP))",
                'opts' => [
                    ['0.80', true],
                    ['0.89', false],
                    ['0.90', false],
                    ['0.85', false],
                ],
            ],
            [
                'q' => "Using the same values (TP=80, FN=10), what is the Recall?\n(Recall = TP / (TP + FN))",
                'opts' => [
                    ['0.80', false],
                    ['0.89', true],
                    ['0.90', false],
                    ['0.72', false],
                ],
            ],
            [
                'q' => "Which of the following best describes K-fold cross-validation?",
                'opts' => [
                    ['Training K different models on the same dataset', false],
                    ['Splitting data into K equal parts, using each part once as the test set while training on the rest', true],
                    ['Randomly shuffling data K times before training', false],
                    ['Using K features to train the model', false],
                ],
            ],
            [
                'q' => "What is the effect of increasing the K value in K-Nearest Neighbors (KNN)?",
                'opts' => [
                    ['The model becomes more complex and prone to overfitting', false],
                    ['The model becomes smoother and may underfit', true],
                    ['Predictions become faster', false],
                    ['The training set grows larger', false],
                ],
            ],
            [
                'q' => "A linear regression model has the equation: y = 3x + 5. If x = 10, what does the model predict for y?",
                'opts' => [
                    ['30', false],
                    ['35', true],
                    ['50', false],
                    ['15', false],
                ],
            ],
            [
                'q' => "What does the R² (coefficient of determination) value measure in a regression model?",
                'opts' => [
                    ['How many features were used to train the model', false],
                    ['The proportion of variance in the target variable explained by the model', true],
                    ['The average prediction error', false],
                    ['The correlation between two features', false],
                ],
            ],

            // ── UNSUPERVISED LEARNING — INTERMEDIATE ───────────────────────
            [
                'q' => "In PCA (Principal Component Analysis), the first principal component:",
                'opts' => [
                    ['Captures the least variance in the data', false],
                    ['Captures the most variance in the data', true],
                    ['Is always the original first column of the dataset', false],
                    ['Represents the mean of all features', false],
                ],
            ],
            [
                'q' => "You run K-Means with K=3 on a dataset. After convergence, you want to know if 3 clusters was the right choice. Which method helps evaluate this?",
                'opts' => [
                    ['Confusion Matrix', false],
                    ['R² Score', false],
                    ['The Elbow Method (plotting inertia vs. K)', true],
                    ['F1-Score', false],
                ],
            ],

            // ── TIME SERIES — INTERMEDIATE ─────────────────────────────────
            [
                'q' => "A time series of monthly revenue shows an upward trend AND spikes every December. Decomposing this series gives you which three components?",
                'opts' => [
                    ['Mean, Variance, Mode', false],
                    ['Trend, Seasonality, Residual', true],
                    ['Signal, Noise, Outlier', false],
                    ['Autocorrelation, Lag, Stationarity', false],
                ],
            ],
            [
                'q' => "What does a rolling 7-day average of daily sales data achieve?",
                'opts' => [
                    ['Highlights individual daily spikes more clearly', false],
                    ['Smooths out short-term fluctuations to reveal underlying trends', true],
                    ['Removes seasonal patterns from the data', false],
                    ['Converts the data from weekly to monthly frequency', false],
                ],
            ],
            [
                'q' => "A stationary time series is one that:",
                'opts' => [
                    ['Has an increasing trend over time', false],
                    ['Shows repeating seasonal patterns', false],
                    ['Has constant mean and variance over time', true],
                    ['Only contains positive values', false],
                ],
            ],

            // ── NLP — INTERMEDIATE ─────────────────────────────────────────
            [
                'q' => "In NLP, 'stop words' are removed during preprocessing because:",
                'opts' => [
                    ['They cause syntax errors in the code', false],
                    ['They are misspelled words that confuse the model', false],
                    ['They are common words (like "the", "is") that carry little meaningful information', true],
                    ['They are numbers that should be converted to text', false],
                ],
            ],
            [
                'q' => "TF-IDF stands for Term Frequency-Inverse Document Frequency. A word that appears in EVERY document will have a TF-IDF score that is:",
                'opts' => [
                    ['Very high — frequent words are important', false],
                    ['Very low — IDF penalizes words appearing in all documents', true],
                    ['Exactly 1.0', false],
                    ['Equal to its term frequency', false],
                ],
            ],
            [
                'q' => "Which NLP technique converts words with similar meaning to a common base form? (e.g., 'running' → 'run', 'better' → 'good')",
                'opts' => [
                    ['Tokenization', false],
                    ['Stemming', false],
                    ['Lemmatization', true],
                    ['Vectorization', false],
                ],
            ],

            // ── DATA PIPELINES & WORKFLOW ──────────────────────────────────
            [
                'q' => "In scikit-learn, what is the purpose of a `Pipeline` object?",
                'opts' => [
                    ['To download datasets from the internet', false],
                    ['To chain preprocessing steps and a model into a single, reproducible workflow', true],
                    ['To visualize model performance in a flowchart', false],
                    ['To parallelize model training across multiple CPUs', false],
                ],
            ],
            [
                'q' => "What is 'data leakage' in machine learning?",
                'opts' => [
                    ['A memory error when loading large datasets', false],
                    ['When information from the test set influences training, leading to falsely optimistic results', true],
                    ['Missing values leaking into other columns during imputation', false],
                    ['When a model is too simple and underfits the data', false],
                ],
            ],
            [
                'q' => "Which scikit-learn function splits a dataset into training and test sets?\n\nfrom sklearn.model_selection import _____",
                'opts' => [
                    ['split_data()', false],
                    ['train_test_split()', true],
                    ['divide_dataset()', false],
                    ['cross_val_split()', false],
                ],
            ],
            [
                'q' => "You apply `StandardScaler` to your training data and want to scale the test data. Which is correct?",
                'opts' => [
                    ['Fit and transform the test data separately', false],
                    ['Only transform the test data using the scaler fitted on training data', true],
                    ['Apply MinMaxScaler to test data instead', false],
                    ['Do not scale the test data at all', false],
                ],
            ],

            // ── MIXED MULTI-STEP ───────────────────────────────────────────
            [
                'q' => "You have a dataset with 500 rows. You use an 80/20 train-test split. You then apply 5-fold cross-validation on the training set. How many rows does each validation fold contain?",
                'opts' => [
                    ['80', true],
                    ['100', false],
                    ['400', false],
                    ['20', false],
                ],
            ],
            [
                'q' => "A model has Precision = 0.75 and Recall = 0.80. What is the F1-Score?\n\n(F1 = 2 × Precision × Recall / (Precision + Recall))",
                'opts' => [
                    ['0.775', true],
                    ['0.80', false],
                    ['0.70', false],
                    ['0.725', false],
                ],
            ],
            [
                'q' => "After training a Decision Tree on a dataset, you notice training accuracy = 99% but test accuracy = 62%. What is the BEST next step?",
                'opts' => [
                    ['Add more features to the training data', false],
                    ['Reduce tree depth (max_depth) to prevent overfitting', true],
                    ['Increase the learning rate', false],
                    ['Remove the test set and retrain on all data', false],
                ],
            ],
            [
                'q' => "You compute the mean squared error (MSE) for two models: Model A MSE = 450, Model B MSE = 230. Which model performs better?",
                'opts' => [
                    ['Model A — higher MSE means better coverage', false],
                    ['Model B — lower MSE means predictions are closer to actual values', true],
                    ['They are equivalent — MSE does not measure model quality', false],
                    ['Cannot determine without R² score', false],
                ],
            ],
            [
                'q' => "What is the purpose of the `random_state` parameter in scikit-learn functions like `train_test_split`?",
                'opts' => [
                    ['To shuffle the dataset randomly each time', false],
                    ['To ensure reproducibility — the same split is produced every run', true],
                    ['To set the number of training iterations', false],
                    ['To control the size of the test set', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 3 — Introduction to Data Science (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}