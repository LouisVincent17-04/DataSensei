<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module3ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 3 — Introduction to Data Science (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Data Science',
            'description'           => 'Tackle advanced data science scenarios — debugging broken pipelines, interpreting model diagnostics, optimizing feature selection, and reasoning through edge cases in statistics, ML evaluation, and time series. Heavy emphasis on code analysis and multi-step problem solving.',
            'time_limit_seconds'    => 1800, // 30 minutes
            'base_xp'               => 1500,
            'order_index'           => 3,
        ]);

        $this->command->info("Seeding 50 advanced-level questions...");

        $qaData = [

            // ── NUMPY — ADVANCED ───────────────────────────────────────────
            [
                'q' => "What does the following NumPy code print?\n\nimport numpy as np\na = np.array([[1,2,3],[4,5,6]])\nb = a[:, 1:]\nprint(b.sum())",
                'opts' => [
                    ['15', false],
                    ['20', false],
                    ['27', true],
                    ['21', false],
                ],
            ],
            [
                'q' => "What is wrong with the following code?\n\nimport numpy as np\na = np.array([1, 2, 3])\nb = np.array([4, 5])\nprint(a + b)",
                'opts' => [
                    ['Nothing — NumPy pads the shorter array with zeros', false],
                    ['It raises a ValueError because the arrays have incompatible shapes', true],
                    ['It returns [5, 7, 3]', false],
                    ['It returns [1, 2, 3, 4, 5]', false],
                ],
            ],
            [
                'q' => "Which NumPy operation efficiently computes the dot product of two large matrices without writing a Python loop?",
                'opts' => [
                    ['np.sum(a * b)', false],
                    ['np.dot(a, b) or a @ b', true],
                    ['np.multiply(a, b).sum(axis=1)', false],
                    ['np.cross(a, b)', false],
                ],
            ],
            [
                'q' => "You have a 2D array of shape (1000, 50). You want the mean of each column. Which is the MOST efficient approach?",
                'opts' => [
                    ['for col in range(50): print(a[:, col].mean())', false],
                    ['np.mean(a, axis=0)', true],
                    ['a.flatten().mean()', false],
                    ['np.average(a)', false],
                ],
            ],

            // ── PANDAS — ADVANCED ──────────────────────────────────────────
            [
                'q' => "What does the following code do?\n\ndf['rank'] = df['score'].rank(method='dense', ascending=False)\n\nAssume df has 5 rows with scores [90, 85, 85, 70, 60].",
                'opts' => [
                    ['Assigns ranks 1–5 with ties getting the same rank and no gap after', true],
                    ['Assigns ranks by average of tied positions', false],
                    ['Sorts the DataFrame by score descending', false],
                    ['Raises an error — pandas rank() requires unique values', false],
                ],
            ],
            [
                'q' => "Identify the bug in this Pandas code:\n\ndf_filtered = df[df['age'] > 30]\ndf_filtered['category'] = 'Senior'\n\nWhat is the likely outcome?",
                'opts' => [
                    ['The original df is correctly updated', false],
                    ['A SettingWithCopyWarning — df_filtered is a view, not a copy', true],
                    ['df_filtered is deleted after the assignment', false],
                    ['The code runs cleanly with no issues', false],
                ],
            ],
            [
                'q' => "What does the following code return?\n\ndf = pd.DataFrame({'A': [1,2,None,4], 'B': [5,None,7,8]})\ndf.fillna(df.mean())",
                'opts' => [
                    ['Replaces NaN with 0 in all columns', false],
                    ['Replaces NaN in each column with that column\'s mean', true],
                    ['Raises an error because df.mean() cannot be passed to fillna()', false],
                    ['Drops rows with any NaN values', false],
                ],
            ],
            [
                'q' => "You call `df.apply(func, axis=1)`. This means the function `func` receives:",
                'opts' => [
                    ['Each column as a Series', false],
                    ['Each row as a Series', true],
                    ['The entire DataFrame at once', false],
                    ['Each scalar value individually', false],
                ],
            ],
            [
                'q' => "What is the difference between `df.loc[0]` and `df.iloc[0]`?",
                'opts' => [
                    ['loc uses label-based indexing; iloc uses integer-position indexing', true],
                    ['They are identical and interchangeable', false],
                    ['loc selects columns; iloc selects rows', false],
                    ['iloc is slower than loc for large DataFrames', false],
                ],
            ],

            // ── STATISTICS — ADVANCED CALCULATION ─────────────────────────
            [
                'q' => "Dataset: [12, 15, 14, 10, 13]. Compute the sample standard deviation.\n\n(s = sqrt(Σ(xi - x̄)² / (n-1)))\n\nMean = 12.8",
                'opts' => [
                    ['1.92', true],
                    ['1.72', false],
                    ['2.16', false],
                    ['1.48', false],
                ],
            ],
            [
                'q' => "A normal distribution has mean = 100 and std = 15. What is the probability that a randomly selected value falls between 85 and 115?\n\n(Use the 68-95-99.7 rule)",
                'opts' => [
                    ['34%', false],
                    ['95%', false],
                    ['68%', true],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => "You run a hypothesis test with α = 0.05 and get a p-value of 0.03. What is the correct conclusion?",
                'opts' => [
                    ['Fail to reject H₀ — there is no significant effect', false],
                    ['Reject H₀ — there is sufficient evidence to support the alternative hypothesis', true],
                    ['Accept H₀ — the effect is definitely real', false],
                    ['The test is inconclusive — run it again', false],
                ],
            ],
            [
                'q' => "Pearson correlation between X and Y is r = 0.90. The coefficient of determination R² is:",
                'opts' => [
                    ['0.90', false],
                    ['0.81', true],
                    ['0.45', false],
                    ['0.95', false],
                ],
            ],
            [
                'q' => "You have two datasets: A = [5,5,5,5,5] and B = [1,3,5,7,9]. Both have the same mean (5). Which statement is true?",
                'opts' => [
                    ['Both have the same variance', false],
                    ['A has higher variance than B', false],
                    ['B has higher variance than A', true],
                    ['Variance cannot be compared when means are equal', false],
                ],
            ],
            [
                'q' => "What does a p-value represent in statistical hypothesis testing?",
                'opts' => [
                    ['The probability that the null hypothesis is true', false],
                    ['The probability of observing results at least as extreme as the data, assuming H₀ is true', true],
                    ['The effect size of the result', false],
                    ['The probability that the alternative hypothesis is false', false],
                ],
            ],

            // ── EDA — ADVANCED ─────────────────────────────────────────────
            [
                'q' => "You detect that 3 features have pairwise correlations above 0.95 with each other. What is the BEST course of action before training a linear model?",
                'opts' => [
                    ['Keep all 3 — more features improve model accuracy', false],
                    ['Remove or combine the highly correlated features to reduce multicollinearity', true],
                    ['Apply one-hot encoding to all 3 features', false],
                    ['Apply K-Means clustering to separate the features', false],
                ],
            ],
            [
                'q' => "What does the following code detect?\n\nQ1 = df['col'].quantile(0.25)\nQ3 = df['col'].quantile(0.75)\nIQR = Q3 - Q1\noutliers = df[(df['col'] < Q1 - 1.5*IQR) | (df['col'] > Q3 + 1.5*IQR)]",
                'opts' => [
                    ['Values below the median', false],
                    ['Values outside the 1.5×IQR fence — Tukey outlier detection', true],
                    ['Values in the top and bottom 5th percentiles', false],
                    ['Values more than 2 standard deviations from the mean', false],
                ],
            ],
            [
                'q' => "A variable has a skewness of +2.3. What transformation would MOST likely normalize its distribution?",
                'opts' => [
                    ['Squaring the values', false],
                    ['Applying a log or square-root transformation', true],
                    ['Multiplying by -1', false],
                    ['One-hot encoding', false],
                ],
            ],
            [
                'q' => "What does a QQ-plot (Quantile-Quantile plot) help you determine?",
                'opts' => [
                    ['The correlation between two variables', false],
                    ['Whether a dataset follows a theoretical distribution, such as normal', true],
                    ['The number of clusters in the data', false],
                    ['Missing value patterns in a DataFrame', false],
                ],
            ],

            // ── FEATURE ENGINEERING — ADVANCED ────────────────────────────
            [
                'q' => "Which regularization technique adds the sum of ABSOLUTE values of coefficients to the loss function, and can shrink some coefficients to exactly zero (feature selection)?",
                'opts' => [
                    ['Ridge (L2 regularization)', false],
                    ['Elastic Net', false],
                    ['Lasso (L1 regularization)', true],
                    ['Dropout', false],
                ],
            ],
            [
                'q' => "You apply PCA and retain components that explain 95% of the total variance. Your original data had 50 features. The result has 8 components. What does this mean?",
                'opts' => [
                    ['You lost 42 features, which will hurt model performance', false],
                    ['8 linear combinations of features capture 95% of the information, reducing dimensionality significantly', true],
                    ['PCA selected 8 of the original features', false],
                    ['The remaining 42 components were all noise and outliers', false],
                ],
            ],
            [
                'q' => "Target encoding replaces a categorical variable's values with the mean of the target for each category. What is the PRIMARY risk of this technique?",
                'opts' => [
                    ['It creates too many binary columns', false],
                    ['It can introduce data leakage if applied before the train/test split', true],
                    ['It cannot be used with tree-based models', false],
                    ['It is slower than one-hot encoding', false],
                ],
            ],

            // ── ML — ADVANCED ──────────────────────────────────────────────
            [
                'q' => "What is the output of the following scikit-learn code?\n\nfrom sklearn.linear_model import LogisticRegression\nfrom sklearn.datasets import make_classification\nX, y = make_classification(n_samples=100, random_state=42)\nmodel = LogisticRegression()\nmodel.fit(X, y)\nprint(type(model.predict(X[:3])))",
                'opts' => [
                    ['<class \'list\'>', false],
                    ['<class \'numpy.ndarray\'>', true],
                    ['<class \'pandas.Series\'>', false],
                    ['<class \'float\'>', false],
                ],
            ],
            [
                'q' => "A Random Forest classifier with 100 trees achieves better performance than a single Decision Tree. Why?",
                'opts' => [
                    ['Each tree sees all features, leading to a larger model', false],
                    ['Averaging predictions from many decorrelated trees reduces variance (bagging principle)', true],
                    ['Random Forest applies boosting to correct previous trees\' errors', false],
                    ['It uses gradient descent to optimize the trees jointly', false],
                ],
            ],
            [
                'q' => "What does the following confusion matrix tell you about the classifier?\n\n             Predicted 0   Predicted 1\nActual 0:       450           50\nActual 1:        30          470\n\nWhat is the False Positive Rate?",
                'opts' => [
                    ['30 / 500 = 6%', false],
                    ['50 / 500 = 10%', true],
                    ['50 / 520 = 9.6%', false],
                    ['30 / 470 = 6.3%', false],
                ],
            ],
            [
                'q' => "You train a gradient boosted model and notice training loss decreases but validation loss increases after iteration 50. What should you do?",
                'opts' => [
                    ['Increase the number of estimators to 500', false],
                    ['Apply early stopping at ~50 iterations to prevent overfitting', true],
                    ['Decrease the learning rate to 0.001', false],
                    ['Add more training data immediately', false],
                ],
            ],
            [
                'q' => "What is the bias-variance tradeoff in machine learning?",
                'opts' => [
                    ['High bias models overfit; high variance models underfit', false],
                    ['High bias models underfit (too simple); high variance models overfit (too complex)', true],
                    ['Both bias and variance should be maximized for best performance', false],
                    ['Bias and variance are independent and do not affect each other', false],
                ],
            ],
            [
                'q' => "When using Grid Search Cross-Validation, what is the risk of selecting hyperparameters based on cross-validation scores computed on the FULL dataset?",
                'opts' => [
                    ['Underfitting because the model sees too much data', false],
                    ['Data leakage — the test set should not influence hyperparameter tuning', true],
                    ['The grid search will be computationally too slow', false],
                    ['Cross-validation results are not reproducible without a random_state', false],
                ],
            ],

            // ── UNSUPERVISED — ADVANCED ────────────────────────────────────
            [
                'q' => "K-Means uses Euclidean distance to assign points to clusters. Which preprocessing step is ESSENTIAL before running K-Means on features with very different scales?",
                'opts' => [
                    ['One-hot encoding categorical features', false],
                    ['Normalizing or standardizing all numeric features', true],
                    ['Removing all rows with missing values', false],
                    ['Applying a log transformation to the target variable', false],
                ],
            ],
            [
                'q' => "DBSCAN differs from K-Means in that it:",
                'opts' => [
                    ['Requires you to specify the number of clusters beforehand', false],
                    ['Can detect arbitrarily shaped clusters and identify noise points without specifying K', true],
                    ['Always produces the same clusters regardless of initialization', false],
                    ['Minimizes within-cluster variance like K-Means', false],
                ],
            ],

            // ── TIME SERIES — ADVANCED ─────────────────────────────────────
            [
                'q' => "You use the Augmented Dickey-Fuller (ADF) test on a time series. The p-value is 0.45. What does this indicate?",
                'opts' => [
                    ['The series is stationary — safe to model with ARIMA', false],
                    ['The series is non-stationary — differencing or transformation is needed', true],
                    ['The series has no seasonality', false],
                    ['The series has a perfect linear trend', false],
                ],
            ],
            [
                'q' => "In ARIMA(p, d, q), what does the 'd' parameter control?",
                'opts' => [
                    ['The number of autoregressive terms', false],
                    ['The order of differencing applied to make the series stationary', true],
                    ['The size of the moving average window', false],
                    ['The seasonal period of the data', false],
                ],
            ],
            [
                'q' => "A time series model trained on 2018–2022 data is evaluated on 2023 data. The test error is much higher than training error. What is the most likely cause?",
                'opts' => [
                    ['The model underfits — use a more complex model', false],
                    ['Distribution shift — patterns in 2023 differ from 2018–2022 (e.g., COVID effects, market change)', true],
                    ['The train/test split was not randomized properly', false],
                    ['The model used too many features', false],
                ],
            ],

            // ── NLP — ADVANCED ─────────────────────────────────────────────
            [
                'q' => "In the following bag-of-words representation, documents are: D1='data science is great', D2='data is data'. What is the TF of 'data' in D2?\n\n(TF = count of term in doc / total words in doc)",
                'opts' => [
                    ['0.25', false],
                    ['0.50', false],
                    ['0.67', true],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "Why do word embeddings (Word2Vec, GloVe) outperform Bag-of-Words for most NLP tasks?",
                'opts' => [
                    ['They are computationally faster to compute', false],
                    ['They represent words as dense vectors that capture semantic relationships and context', true],
                    ['They require no training data', false],
                    ['They always produce smaller vocabulary sizes', false],
                ],
            ],
            [
                'q' => "A sentiment classifier trained on English reviews is applied to Tagalog reviews. Performance drops significantly. This is an example of:",
                'opts' => [
                    ['Overfitting on the English training data', false],
                    ['Domain shift / distribution mismatch between training and inference data', true],
                    ['A bug in the tokenizer', false],
                    ['Class imbalance in the test set', false],
                ],
            ],

            // ── DEBUGGING & CODE ANALYSIS ──────────────────────────────────
            [
                'q' => "Find the bug:\n\nfrom sklearn.preprocessing import StandardScaler\nscaler = StandardScaler()\nX_train_scaled = scaler.fit_transform(X_train)\nX_test_scaled  = scaler.fit_transform(X_test)  # ← line 4",
                'opts' => [
                    ['Line 3 should use transform() not fit_transform()', false],
                    ['Line 4 should use transform(X_test), NOT fit_transform(X_test)', true],
                    ['StandardScaler cannot be applied to test data at all', false],
                    ['fit_transform() and transform() are identical — no bug exists', false],
                ],
            ],
            [
                'q' => "What is the output of this code?\n\nimport pandas as pd\ndf = pd.DataFrame({'x': [1,2,3], 'y': [4,5,6]})\ndf2 = df\ndf2['x'] = 0\nprint(df['x'].sum())",
                'opts' => [
                    ['6', false],
                    ['0', true],
                    ['3', false],
                    ['An error', false],
                ],
            ],
            [
                'q' => "Which line introduces a bug in this pipeline?\n\nfrom sklearn.pipeline import Pipeline\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.linear_model import Ridge\n\npipe = Pipeline([\n    ('scaler', StandardScaler()),\n    ('model', Ridge())\n])\npipe.fit(X_train, y_train)\npredictions = pipe.predict(X_train)  # ← evaluating on training set",
                'opts' => [
                    ['The Pipeline order is wrong — model must come before scaler', false],
                    ['Evaluating on X_train gives overly optimistic results; should evaluate on X_test', true],
                    ['Ridge() cannot be used inside a Pipeline', false],
                    ['StandardScaler() is applied twice when using Pipeline', false],
                ],
            ],

            // ── OPTIMIZATION & PERFORMANCE ─────────────────────────────────
            [
                'q' => "You have a dataset with 5 million rows. Using pandas `iterrows()` to process each row is extremely slow. What is the better approach?",
                'opts' => [
                    ['Use itertuples() instead of iterrows()', false],
                    ['Use vectorized NumPy/Pandas operations (apply, map, or direct column arithmetic)', true],
                    ['Convert the DataFrame to a list and use a for loop', false],
                    ['Run the code in a Jupyter notebook instead of a .py file', false],
                ],
            ],
            [
                'q' => "Which of the following BEST reduces memory usage when loading a large CSV file with Pandas?",
                'opts' => [
                    ['pd.read_csv(file, nrows=100)', false],
                    ['pd.read_csv(file, dtype={\'id\': \'int32\', \'value\': \'float32\'}) or using chunked reading', true],
                    ['pd.read_csv(file, index_col=0)', false],
                    ['Converting the CSV to JSON before loading', false],
                ],
            ],
            [
                'q' => "When training a machine learning model, using `n_jobs=-1` in scikit-learn means:",
                'opts' => [
                    ['The model will run for an unlimited number of iterations', false],
                    ['All available CPU cores will be used for parallel computation', true],
                    ['The model disables regularization', false],
                    ['The random seed is set to -1 for stochasticity', false],
                ],
            ],

            // ── ADVANCED EVALUATION ────────────────────────────────────────
            [
                'q' => "The ROC-AUC score of a binary classifier is 0.50. What does this mean?",
                'opts' => [
                    ['The model is perfect — 50% precision and 50% recall', false],
                    ['The model performs no better than random guessing', true],
                    ['The model achieves 50% accuracy', false],
                    ['The model is slightly better than baseline', false],
                ],
            ],
            [
                'q' => "In a highly imbalanced dataset (99% class 0, 1% class 1), a model predicts class 0 for every input. Its accuracy is 99%. Why is this misleading?",
                'opts' => [
                    ['Accuracy is always a reliable metric', false],
                    ['The model completely fails to detect the minority class — Precision, Recall, and F1 for class 1 are all 0', true],
                    ['The model needs more training epochs', false],
                    ['The dataset should be augmented, not the metric changed', false],
                ],
            ],
            [
                'q' => "Stratified k-fold cross-validation differs from regular k-fold because:",
                'opts' => [
                    ['It is faster due to fewer splits', false],
                    ['Each fold preserves the class distribution proportions of the original dataset', true],
                    ['It shuffles the data before each fold', false],
                    ['It uses weighted loss functions', false],
                ],
            ],
            [
                'q' => "You train a Logistic Regression model and get coefficients [2.5, -1.3, 0.0, 4.1] for 4 features. What does a coefficient of 0.0 imply?",
                'opts' => [
                    ['That feature has the most importance', false],
                    ['That feature has no linear influence on the predicted log-odds', true],
                    ['That feature should be removed because it causes an error', false],
                    ['That the model has converged prematurely', false],
                ],
            ],

            // ── MIXED SCENARIO ─────────────────────────────────────────────
            [
                'q' => "A data scientist computes RMSE = 5.2 for a regression model predicting house prices in millions. What does this number represent?",
                'opts' => [
                    ['The model is wrong 5.2% of the time', false],
                    ['The average prediction error is approximately 5.2 million units (on the target scale)', true],
                    ['The R² score is 1 - 5.2 = -4.2', false],
                    ['The model outperforms a baseline by 5.2 units', false],
                ],
            ],
            [
                'q' => "You apply 10-fold cross-validation and get scores: [0.82, 0.79, 0.91, 0.85, 0.88, 0.77, 0.90, 0.83, 0.86, 0.79]. The high variance in scores suggests:",
                'opts' => [
                    ['The model is well-tuned and stable', false],
                    ['The model is sensitive to which subset of data it trains on — possible instability or insufficient data', true],
                    ['10 folds is too many — reduce to 3', false],
                    ['The model is underfitting consistently', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 3 — Introduction to Data Science (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}