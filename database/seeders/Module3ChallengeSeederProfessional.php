<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module3ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 3 — Introduction to Data Science (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Introduction to Data Science',
            'description'           => 'Real-world data science scenarios at professional level — production pipeline design, advanced statistical inference, model deployment edge cases, time series forecasting, NLP at scale, and performance engineering. Expect ambiguous problems, trade-off reasoning, and code requiring deep expertise to debug or optimize.',
            'time_limit_seconds'    => 2400, // 40 minutes
            'base_xp'               => 2000,
            'order_index'           => 3,
        ]);

        $this->command->info("Seeding 50 professional-level questions...");

        $qaData = [

            // ── PRODUCTION PIPELINES & ARCHITECTURE ───────────────────────
            [
                'q' => "You are building a production ML pipeline for a bank's fraud detection system. The dataset has 99.8% non-fraud cases. Which approach BEST addresses class imbalance while maintaining production reliability?",
                'opts' => [
                    ['Oversample the minority class using SMOTE in preprocessing, and optimize the classifier threshold using precision-recall AUC on a held-out validation set', true],
                    ['Collect more data until classes are perfectly balanced before training', false],
                    ['Use accuracy as the primary metric since it reflects real-world distribution', false],
                    ['Train a model on the fraud class only using one-class classification exclusively', false],
                ],
            ],
            [
                'q' => "A data scientist fits a preprocessing scaler and model on training data. In production, the scaler parameters (mean, std) are stored and reused for inference. If the feature distribution shifts significantly over 6 months, what should happen?",
                'opts' => [
                    ['Nothing — scalers are stable once fitted', false],
                    ['The model should be retrained and the scaler refitted on recent data; implement data drift monitoring to trigger retraining automatically', true],
                    ['Only retrain the model — the scaler can stay fixed', false],
                    ['Apply a new scaler to each inference request without storing it', false],
                ],
            ],
            [
                'q' => "You are designing a feature store for a real-time recommendation system serving 10M requests/day. Which trade-off is MOST critical to address?",
                'opts' => [
                    ['Whether to use Python or Java for the feature computation layer', false],
                    ['Balancing feature freshness (how up-to-date features are) vs. latency (how fast features are retrieved for inference)', true],
                    ['Whether to store features as floats or doubles', false],
                    ['Choosing between logistic regression and neural networks for the model', false],
                ],
            ],
            [
                'q' => "A model trained on historical customer data is deployed. After 3 months, complaints about poor predictions arise. Monitoring shows input feature distributions have shifted. This is called:",
                'opts' => [
                    ['Model overfitting', false],
                    ['Concept drift / data drift — the statistical properties of inputs have changed', true],
                    ['Regularization failure', false],
                    ['Training set contamination', false],
                ],
            ],
            [
                'q' => "Which of the following strategies BEST prevents target leakage in a time series prediction problem?",
                'opts' => [
                    ['Random train/test split across all time periods', false],
                    ['Temporal split — train on data before a cutoff date, test on data after', true],
                    ['Stratified k-fold cross-validation', false],
                    ['Using the full dataset for training and validation simultaneously', false],
                ],
            ],

            // ── ADVANCED STATISTICS ────────────────────────────────────────
            [
                'q' => "You run an A/B test with 50,000 users per group. The p-value is 0.032 (α=0.05) and the absolute conversion rate improvement is 0.1% (from 10.0% to 10.1%). What is the correct business interpretation?",
                'opts' => [
                    ['The result is statistically significant AND practically significant — deploy immediately', false],
                    ['The result is statistically significant but may lack practical significance; evaluate the cost of the change vs. 0.1% lift', true],
                    ['The result is not significant because 0.1% is too small to matter', false],
                    ['The p-value is invalid because both groups have the same sample size', false],
                ],
            ],
            [
                'q' => "In Bayesian inference, what does the posterior distribution represent?",
                'opts' => [
                    ['The probability distribution of the data given model parameters', false],
                    ['Our updated belief about model parameters after observing the data, combining prior belief and likelihood', true],
                    ['The maximum likelihood estimate of model parameters', false],
                    ['The marginal distribution of the target variable', false],
                ],
            ],
            [
                'q' => "You are comparing 5 machine learning models on the same test set and report the best model's accuracy. Why is this problematic?",
                'opts' => [
                    ['5 models is too few to draw conclusions', false],
                    ['The test set has been used for model selection, inflating reported performance — you need a hold-out evaluation set separate from the comparison process', true],
                    ['Accuracy is always the wrong metric for model comparison', false],
                    ['The models must be compared on training data only', false],
                ],
            ],
            [
                'q' => "A linear regression model produces residuals that show a clear fan-shaped pattern (variance increases with fitted values). What statistical violation does this indicate?",
                'opts' => [
                    ['Multicollinearity among features', false],
                    ['Heteroscedasticity — the assumption of constant variance in residuals is violated', true],
                    ['Serial autocorrelation in the residuals', false],
                    ['Non-normality of the target variable', false],
                ],
            ],
            [
                'q' => "You need to perform a statistical test comparing the mean response time across 4 different server configurations. Which test is appropriate?",
                'opts' => [
                    ['Paired t-test', false],
                    ['Chi-squared test', false],
                    ['One-way ANOVA, followed by post-hoc tests if significant', true],
                    ['Z-test for proportions', false],
                ],
            ],

            // ── ADVANCED ML THEORY ─────────────────────────────────────────
            [
                'q' => "What is the fundamental difference between bagging (e.g., Random Forest) and boosting (e.g., XGBoost)?",
                'opts' => [
                    ['Bagging uses deep trees; boosting uses shallow stumps exclusively', false],
                    ['Bagging trains models in parallel on random subsets to reduce variance; boosting trains models sequentially where each model corrects the previous one\'s errors to reduce bias', true],
                    ['Bagging reduces bias; boosting reduces variance', false],
                    ['Both are identical — they differ only in the base learner used', false],
                ],
            ],
            [
                'q' => "An XGBoost model with max_depth=10 and 1000 trees overfits the training data. Which combination of hyperparameter changes is MOST effective?",
                'opts' => [
                    ['Increase max_depth to 15 and add more trees', false],
                    ['Reduce max_depth (e.g., 4–6), reduce n_estimators, add subsampling and colsample_bytree regularization', true],
                    ['Switch to a linear kernel SVM', false],
                    ['Remove all regularization parameters and rely on early stopping alone', false],
                ],
            ],
            [
                'q' => "A neural network for tabular data (income prediction) achieves 82% accuracy but a simple Gradient Boosted Tree achieves 85%. What is the most likely reason?",
                'opts' => [
                    ['Neural networks cannot handle tabular data', false],
                    ['Tabular data often has complex non-linearities better captured by tree ensembles; neural networks require more data and tuning to surpass GBTs on structured data', true],
                    ['The neural network needs more layers to compete', false],
                    ['Both models are equivalent — the difference is within noise range', false],
                ],
            ],
            [
                'q' => "What does the following code compute?\n\nfrom sklearn.inspection import permutation_importance\nresult = permutation_importance(model, X_test, y_test, n_repeats=10)\nprint(result.importances_mean)",
                'opts' => [
                    ['The model\'s feature coefficients from a linear model', false],
                    ['The mean decrease in model performance when each feature is randomly shuffled — measuring feature importance', true],
                    ['The cross-validation accuracy for each feature individually', false],
                    ['The standard deviation of each feature in X_test', false],
                ],
            ],
            [
                'q' => "You are calibrating a classifier's probability outputs. The model assigns 80% confidence to 1,000 predictions, but only 60% of them are actually positive. What does this tell you?",
                'opts' => [
                    ['The model is well-calibrated — 80% is close enough to 60%', false],
                    ['The model is overconfident — its predicted probabilities are systematically too high; apply Platt scaling or isotonic regression', true],
                    ['The model is underconfident — increase the decision threshold', false],
                    ['The problem is class imbalance, not calibration', false],
                ],
            ],

            // ── NUMPY & PANDAS — PROFESSIONAL ─────────────────────────────
            [
                'q' => "What does the following code do and what is its output?\n\nimport numpy as np\na = np.random.seed(42)\nb = np.random.randn(5)\nprint((b > 0).sum())",
                'opts' => [
                    ['Prints 5 — all values from randn are positive', false],
                    ['Prints the count of values greater than 0 (2 or 3, depending on seed — with seed 42 it\'s 3)', true],
                    ['Prints the sum of all positive values', false],
                    ['Raises an error — randn returns floats, not booleans', false],
                ],
            ],
            [
                'q' => "You have a Pandas DataFrame with 10 million rows. The operation `df.apply(func, axis=1)` takes 8 minutes. What is the MOST effective optimization?",
                'opts' => [
                    ['Use df.applymap() instead of apply()', false],
                    ['Rewrite the function using vectorized NumPy operations directly on the DataFrame columns, avoiding row-wise Python loops entirely', true],
                    ['Increase the system RAM', false],
                    ['Split the DataFrame into chunks and apply in parallel using multiprocessing', false],
                ],
            ],
            [
                'q' => "What is the output of this code?\n\nimport pandas as pd\ndf = pd.DataFrame({'A': [1,2,3], 'B': [4,5,6]})\nresult = df.eval('C = A * B + A')\nprint(result['C'].tolist())",
                'opts' => [
                    ['[4, 10, 18]', false],
                    ['[5, 12, 21]', true],
                    ['[5, 10, 21]', false],
                    ['Error — eval() cannot create new columns', false],
                ],
            ],
            [
                'q' => "You need to join two DataFrames: `orders` (10M rows) and `products` (500 rows) on 'product_id'. Which approach is fastest in Pandas?",
                'opts' => [
                    ['pd.merge(orders, products, on=\'product_id\', how=\'left\')', true],
                    ['Iterating over each order row and manually looking up the product', false],
                    ['pd.concat([orders, products], ignore_index=True)', false],
                    ['Using SQL via a SQLite in-memory database', false],
                ],
            ],

            // ── TIME SERIES — PROFESSIONAL ─────────────────────────────────
            [
                'q' => "You have daily sales data with strong weekly seasonality and a rising trend. The Ljung-Box test on your ARIMA residuals shows p < 0.05. What does this tell you?",
                'opts' => [
                    ['The residuals are white noise — the model is well-specified', false],
                    ['Significant autocorrelation remains in the residuals — the model is under-specified and needs revision (e.g., add seasonal terms via SARIMA)', true],
                    ['The trend component has been fully captured', false],
                    ['The p-value is irrelevant for time series model diagnostics', false],
                ],
            ],
            [
                'q' => "Facebook Prophet is used to forecast weekly website traffic. After fitting, the model's trend component shows a changepoint mid-2022. How should you interpret this?",
                'opts' => [
                    ['The model encountered a bug in mid-2022', false],
                    ['Prophet detected a structural break where the trend rate changed — investigate what event caused it (e.g., product launch, algorithm change)', true],
                    ['The changepoint means data before mid-2022 should be deleted', false],
                    ['Changepoints only occur in seasonal, not trend, components', false],
                ],
            ],
            [
                'q' => "Walk-forward validation in time series forecasting means:",
                'opts' => [
                    ['Shuffling time series data before splitting for cross-validation', false],
                    ['Iteratively expanding the training window while evaluating on a fixed future horizon — simulating real-world incremental forecasting', true],
                    ['Using the last 20% of data as a validation set only once', false],
                    ['Predicting every data point using the previous step only (lag-1)', false],
                ],
            ],

            // ── NLP — PROFESSIONAL ─────────────────────────────────────────
            [
                'q' => "You fine-tune a BERT model on a domain-specific sentiment corpus (medical reviews). After training, the model is 98% accurate on the medical test set but only 62% on general reviews. What is the correct diagnosis?",
                'opts' => [
                    ['Underfitting — the model is not complex enough', false],
                    ['Catastrophic forgetting / over-specialization — fine-tuning eroded BERT\'s general language knowledge; consider lower learning rate or adapter layers', true],
                    ['The model should be trained from scratch on medical data', false],
                    ['BERT is not suitable for sentiment analysis', false],
                ],
            ],
            [
                'q' => "In a production NLP system processing 1M user reviews per day, which approach is MOST scalable for batch inference?",
                'opts' => [
                    ['Load the model fresh for each individual request', false],
                    ['Batch reviews into groups (e.g., 64 or 128), preload the model into memory, and run batch inference using optimized frameworks like ONNX or TorchScript', true],
                    ['Use a CPU-only server for cost savings — GPUs are unnecessary', false],
                    ['Tokenize each review individually and send separate API calls', false],
                ],
            ],
            [
                'q' => "TF-IDF fails to capture semantic similarity between synonyms (e.g., 'car' and 'automobile' are treated as completely unrelated). Which technique solves this?",
                'opts' => [
                    ['Increasing the vocabulary size', false],
                    ['Dense word embeddings (Word2Vec, GloVe, fastText) or contextual embeddings (BERT) that place semantically similar words close in vector space', true],
                    ['Using bigrams instead of unigrams', false],
                    ['Applying L2 normalization to TF-IDF vectors', false],
                ],
            ],

            // ── DATA ENGINEERING & SCALE ───────────────────────────────────
            [
                'q' => "You are loading a 50GB CSV file into memory using `pd.read_csv()`. The server has 32GB RAM. The script crashes with MemoryError. What is the correct solution?",
                'opts' => [
                    ['Buy a server with 64GB RAM', false],
                    ['Use chunked reading with `pd.read_csv(chunksize=100000)` and process each chunk, or use Dask / PySpark for distributed processing', true],
                    ['Compress the CSV to ZIP before loading', false],
                    ['Convert the CSV to JSON — JSON files load faster', false],
                ],
            ],
            [
                'q' => "Which scenario demonstrates correct usage of Apache Spark vs. Pandas?",
                'opts' => [
                    ['Use Spark for exploratory analysis on a 100MB file; use Pandas for 100TB pipelines', false],
                    ['Use Pandas for in-memory analysis on datasets that fit in RAM; use Spark for distributed processing of datasets exceeding RAM capacity across a cluster', true],
                    ['Spark and Pandas are interchangeable — choose based on personal preference', false],
                    ['Use Spark only when the dataset has more than 1,000 columns', false],
                ],
            ],
            [
                'q' => "A batch ETL pipeline runs nightly and joins 3 large tables. Sometimes the join produces duplicate rows due to 1:N relationships not being handled. What is the CORRECT fix?",
                'opts' => [
                    ['Add a LIMIT clause to cap the output', false],
                    ['Diagnose the join cardinality, apply appropriate aggregation or deduplication (e.g., GROUP BY or ROW_NUMBER with partitioning), and add data quality tests', true],
                    ['Switch from inner join to outer join', false],
                    ['Truncate the table before loading to clear old duplicates', false],
                ],
            ],

            // ── MODEL MONITORING & MLOps ───────────────────────────────────
            [
                'q' => "In an MLOps platform, which metric should trigger an automatic model retraining alert?",
                'opts' => [
                    ['Model training time exceeds 1 hour', false],
                    ['Population Stability Index (PSI) of input features exceeds 0.25, indicating significant distribution shift from training baseline', true],
                    ['Prediction latency increases by 10ms', false],
                    ['The model\'s R² on training data drops below 0.99', false],
                ],
            ],
            [
                'q' => "A/B testing a new recommendation model in production: Group A (old model) serves 500K users, Group B (new model) serves 500K users. The metric is click-through rate (CTR). After 1 week, p=0.002 for CTR improvement. What additional analysis is necessary before deploying?",
                'opts' => [
                    ['No further analysis needed — p < 0.05 means deploy immediately', false],
                    ['Check for network effects (users in different groups may influence each other), novelty effects (users clicking due to newness, not value), and segment performance across user cohorts', true],
                    ['Run the test for another year to be certain', false],
                    ['Reduce the test to 100K users per group to lower computational costs', false],
                ],
            ],

            // ── EDGE CASES & PROFESSIONAL JUDGMENT ────────────────────────
            [
                'q' => "You train a churn prediction model. The model achieves high accuracy but business stakeholders report the model is flagging the wrong customers. Inspection shows the model uses 'last_login_days' as the top feature, but recently this field was reset to 0 for all users during a database migration. What is this an example of?",
                'opts' => [
                    ['Model underfitting', false],
                    ['Silent data corruption — a non-breaking schema change corrupted a critical feature, causing silent model degradation', true],
                    ['Concept drift from user behavior change', false],
                    ['The model is working correctly — 0 days since login is valid', false],
                ],
            ],
            [
                'q' => "What does the following scikit-learn code output when `y_test` is perfectly predicted?\n\nfrom sklearn.metrics import log_loss\nprint(log_loss([0,1,0,1], [0.0, 1.0, 0.0, 1.0]))",
                'opts' => [
                    ['1.0', false],
                    ['0.0', true],
                    ['Infinity — log(0) is undefined', false],
                    ['0.5', false],
                ],
            ],
            [
                'q' => "In a dataset, you find that 'age' has been recorded as 999 for approximately 2% of records. What is the MOST appropriate professional approach?",
                'opts' => [
                    ['Remove all rows where age = 999', false],
                    ['Identify 999 as a sentinel/placeholder value for missing data, treat as NaN, and apply appropriate imputation based on other features (e.g., median, KNN imputation, or model-based imputation)', true],
                    ['Replace 999 with 99 since it is likely a typo', false],
                    ['Clip all age values to [0, 120] and proceed without investigation', false],
                ],
            ],
            [
                'q' => "You deploy a scikit-learn model using pickle. Six months later, the team upgrades scikit-learn from 1.2 to 1.4. The model fails to load. What is the root cause and best practice?",
                'opts' => [
                    ['Pickle files expire after 6 months due to compression', false],
                    ['scikit-learn pickle files are not guaranteed cross-version compatible — best practice is to store the model alongside its exact dependency versions and use ONNX or model registries with versioning', true],
                    ['The server disk is corrupted — re-download pickle', false],
                    ['Upgrade to Python 3.12 to fix pickle compatibility', false],
                ],
            ],

            // ── PROFESSIONAL NUMPY/PANDAS EDGE CASES ──────────────────────
            [
                'q' => "Why does the following code produce unexpected results?\n\nimport numpy as np\na = np.array([0.1, 0.2, 0.3])\nprint(a.sum() == 0.6)",
                'opts' => [
                    ['NumPy has a bug in sum()', false],
                    ['Floating-point representation in binary causes rounding errors — use np.isclose(a.sum(), 0.6) for comparisons', true],
                    ['The array must be converted to float64 first', false],
                    ['Python integer overflow causes this', false],
                ],
            ],
            [
                'q' => "What is the key problem with this production inference code?\n\ndef predict(features):\n    model = joblib.load('model.pkl')  # loads model on every call\n    return model.predict([features])",
                'opts' => [
                    ['joblib.load() is not thread-safe', false],
                    ['Loading the model on every inference call adds disk I/O latency per request — the model should be loaded once at application startup and reused', true],
                    ['model.predict() expects a DataFrame, not a list', false],
                    ['The function does not handle model drift automatically', false],
                ],
            ],
            [
                'q' => "You perform feature selection using a Random Forest's `feature_importances_`. A feature has importance = 0.32 (highest). What does this mean, and what is a known limitation of this metric?",
                'opts' => [
                    ['The feature causes 32% of predictions to change; no limitations', false],
                    ['32% of splits in the forest used this feature for reduction in impurity; but this metric can be biased toward high-cardinality or continuous features', true],
                    ['The feature has a 32% correlation with the target variable', false],
                    ['The feature should be removed since high importance causes overfitting', false],
                ],
            ],

            // ── COMPREHENSIVE PROFESSIONAL SCENARIOS ──────────────────────
            [
                'q' => "A data scientist reports their model achieves 94% accuracy on a medical diagnosis task. A colleague asks about sensitivity (recall for the positive class). Sensitivity is only 42%. How should this model be assessed for clinical deployment?",
                'opts' => [
                    ['94% accuracy is excellent — deploy immediately', false],
                    ['The model should NOT be deployed — it misses 58% of actual positive cases, which in a medical context could be life-threatening; optimize threshold or retrain with recall-focused loss', true],
                    ['Increase training data and the sensitivity will naturally improve', false],
                    ['Use accuracy for deployment decisions — recall is a secondary metric', false],
                ],
            ],
            [
                'q' => "What is SHAP (SHapley Additive exPlanations) used for in professional data science?",
                'opts' => [
                    ['A fast algorithm for training tree ensembles', false],
                    ['Explaining individual model predictions by attributing contribution of each feature to the output — crucial for model transparency and regulatory compliance (e.g., GDPR right to explanation)', true],
                    ['A regularization technique to prevent overfitting', false],
                    ['A data augmentation strategy for imbalanced classes', false],
                ],
            ],
            [
                'q' => "You are asked to reduce the inference latency of a large scikit-learn Gradient Boosted classifier from 200ms to under 20ms for a real-time API. Which approach is MOST effective?",
                'opts' => [
                    ['Use a faster programming language like C++ to call the model', false],
                    ['Export the model to ONNX format and serve with ONNX Runtime, or distill the GBM into a smaller model (e.g., fewer trees, lower depth) while monitoring accuracy degradation', true],
                    ['Add more CPUs to the server', false],
                    ['Increase the batch size for each API request', false],
                ],
            ],
            [
                'q' => "In a recommendation system, you notice users tend to only receive recommendations for items already popular (popularity bias). Which technique BEST corrects this?",
                'opts' => [
                    ['Increase the training dataset size', false],
                    ['Apply inverse propensity scoring or add diversity regularization to the objective function, penalizing recommendations of already-popular items', true],
                    ['Switch from collaborative filtering to content-based filtering only', false],
                    ['Remove all popular items from the recommendation pool', false],
                ],
            ],
            [
                'q' => "A professional data scientist is asked to build a fair lending model that predicts loan defaults. After training, the model shows significantly higher false positive rates for one demographic group. What is the MOST responsible course of action?",
                'opts' => [
                    ['Deploy the model since it achieves good overall accuracy', false],
                    ['Audit the model using fairness metrics (equal opportunity, equalized odds), investigate the source of bias (historical data, proxy features), apply fairness-aware algorithms or constraints, and involve legal/ethics review before deployment', true],
                    ['Remove the demographic column and retrain — the problem is solved', false],
                    ['Increase the dataset size until fairness metrics improve naturally', false],
                ],
            ],
            [
                'q' => "You have trained a time series forecasting model for inventory management. The model performs well on average but catastrophically underestimates demand spikes (e.g., holiday seasons). Which approach BEST addresses this?",
                'opts' => [
                    ['Switch to a simpler model that underfits less', false],
                    ['Add external regressors for known events (holidays, promotions), use quantile regression or probabilistic forecasting to model prediction intervals, and implement manual override rules for known high-demand periods', true],
                    ['Remove holiday data from the training set as outliers', false],
                    ['Apply log transformation to flatten the demand spikes', false],
                ],
            ],
            [
                'q' => "What does it mean when a data scientist says a model is 'well-calibrated', and why does it matter in professional practice?",
                'opts' => [
                    ['The model achieves high accuracy on all data splits', false],
                    ['The model\'s predicted probabilities closely match empirical frequencies — e.g., events predicted at 80% confidence actually occur ~80% of the time; critical for decision-making in medicine, finance, and insurance where probabilities drive actions', true],
                    ['The model\'s training and validation losses are equal', false],
                    ['All hyperparameters have been manually optimized', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 3 — Introduction to Data Science (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}