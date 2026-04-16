<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module14ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 14 — Supervised Learning (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 1: Supervised Learning',
            'description'           => 'Apply your understanding of supervised learning through analytical problems, metric calculations, algorithm comparisons, and conceptual tracing. Covers train/test splits, evaluation metrics, regression, classification, and model selection.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 700,
            'order_index'           => 14,
        ]);

        $this->command->info("Seeding 50 university-level supervised learning questions...");

        $qaData = [

            // ── TRAIN/TEST SPLIT & CROSS-VALIDATION ───────────────────────
            [
                'q' => 'A dataset has 1,000 samples. You use an 80/20 train/test split. How many samples are in the test set?',
                'opts' => [
                    ['100', false],
                    ['200', true],
                    ['800', false],
                    ['20', false],
                ],
            ],
            [
                'q' => 'What is the purpose of a validation set (distinct from the test set)?',
                'opts' => [
                    ['To replace the training set when data is limited', false],
                    ['To tune hyperparameters during model development without touching the held-out test set', true],
                    ['To store the raw unprocessed data', false],
                    ['To measure final model performance after all decisions are made', false],
                ],
            ],
            [
                'q' => 'In k-fold cross-validation with k=5, how many times is the model trained?',
                'opts' => [
                    ['1', false],
                    ['5', true],
                    ['10', false],
                    ['20', false],
                ],
            ],
            [
                'q' => 'What is the main advantage of k-fold cross-validation over a single train/test split?',
                'opts' => [
                    ['It always produces a higher accuracy score', false],
                    ['Every sample gets to be in the validation set exactly once, giving a more reliable performance estimate', true],
                    ['It eliminates the need for labeled data', false],
                    ['It trains the model k times faster', false],
                ],
            ],
            [
                'q' => 'You train a model and find: Training accuracy = 99%, Test accuracy = 61%. What is the most likely problem?',
                'opts' => [
                    ['Underfitting — the model is too simple', false],
                    ['Overfitting — the model memorized training data and fails to generalize', true],
                    ['Data leakage — the test set was too small', false],
                    ['The model has too few hyperparameters', false],
                ],
            ],
            [
                'q' => 'You train a model and find: Training accuracy = 58%, Test accuracy = 57%. What is the most likely problem?',
                'opts' => [
                    ['Overfitting — the model is too complex', false],
                    ['Underfitting — the model is too simple to learn the patterns in the data', true],
                    ['Data leakage — test data was used during training', false],
                    ['The learning rate is too high', false],
                ],
            ],

            // ── LINEAR REGRESSION CALCULATIONS ───────────────────────────
            [
                'q' => 'A linear regression model has the equation: ŷ = 3x + 7.\nIf x = 5, what is the predicted value of ŷ?',
                'opts' => [
                    ['15', false],
                    ['22', true],
                    ['35', false],
                    ['12', false],
                ],
            ],
            [
                'q' => 'What does the Mean Squared Error (MSE) measure in regression?\nMSE = (1/n) × Σ(yᵢ - ŷᵢ)²',
                'opts' => [
                    ['The percentage of correct predictions', false],
                    ['The average of the squared differences between actual and predicted values', true],
                    ['The total number of incorrect predictions', false],
                    ['The correlation between features and labels', false],
                ],
            ],
            [
                'q' => 'Actual values: [3, 5, 4], Predicted values: [2, 5, 6].\nWhat is the MSE?\nMSE = mean of [(3-2)², (5-5)², (4-6)²]',
                'opts' => [
                    ['1.0', false],
                    ['1.67', true],
                    ['2.0', false],
                    ['5.0', false],
                ],
            ],
            [
                'q' => 'What does R² (R-squared) measure in linear regression?',
                'opts' => [
                    ['The slope of the regression line', false],
                    ['The proportion of variance in the target variable explained by the features', true],
                    ['The number of correct predictions divided by total predictions', false],
                    ['The error between individual predictions', false],
                ],
            ],
            [
                'q' => 'An R² value of 0 means:',
                'opts' => [
                    ['The model perfectly predicts all values', false],
                    ['The model explains none of the variance — it performs no better than simply predicting the mean', true],
                    ['The model predicts the wrong class for every input', false],
                    ['The model has no features', false],
                ],
            ],
            [
                'q' => 'What is the difference between MSE and RMSE?',
                'opts' => [
                    ['MSE is used for classification; RMSE is for regression', false],
                    ['RMSE is the square root of MSE, bringing it back to the same units as the target variable', true],
                    ['MSE penalizes all errors equally; RMSE penalizes large errors more', false],
                    ['There is no meaningful difference between them', false],
                ],
            ],

            // ── LOGISTIC REGRESSION & CLASSIFICATION METRICS ──────────────
            [
                'q' => 'A logistic regression model outputs 0.72. With a decision threshold of 0.5, what is the predicted class?',
                'opts' => [
                    ['Class 0 (Negative)', false],
                    ['Class 1 (Positive)', true],
                    ['Uncertain — more data needed', false],
                    ['0.72', false],
                ],
            ],
            [
                'q' => 'A binary classifier results in:\nTP=80, TN=70, FP=10, FN=20\nWhat is the accuracy?',
                'opts' => [
                    ['75%', false],
                    ['83.3%', true],
                    ['88.9%', false],
                    ['90%', false],
                ],
            ],
            [
                'q' => 'Using the same confusion matrix (TP=80, TN=70, FP=10, FN=20):\nWhat is Precision?\nPrecision = TP / (TP + FP)',
                'opts' => [
                    ['0.78', false],
                    ['0.80', false],
                    ['0.89', true],
                    ['0.92', false],
                ],
            ],
            [
                'q' => 'Using the same confusion matrix (TP=80, TN=70, FP=10, FN=20):\nWhat is Recall (Sensitivity)?\nRecall = TP / (TP + FN)',
                'opts' => [
                    ['0.67', false],
                    ['0.80', true],
                    ['0.89', false],
                    ['0.75', false],
                ],
            ],
            [
                'q' => 'What is the F1 Score and when is it preferred over accuracy?\nF1 = 2 × (Precision × Recall) / (Precision + Recall)',
                'opts' => [
                    ['The geometric mean of precision and recall; preferred for balanced datasets', false],
                    ['The harmonic mean of precision and recall; preferred when classes are imbalanced', true],
                    ['The arithmetic mean of precision and recall; always the best metric', false],
                    ['A score that measures only false positives', false],
                ],
            ],
            [
                'q' => 'Precision = 0.89, Recall = 0.80.\nWhat is the F1 Score (rounded to 2 decimals)?',
                'opts' => [
                    ['0.83', false],
                    ['0.84', true],
                    ['0.85', false],
                    ['0.89', false],
                ],
            ],

            // ── DECISION TREES DEPTH ──────────────────────────────────────
            [
                'q' => 'What does "tree depth" control in a decision tree?',
                'opts' => [
                    ['The number of features used at each split', false],
                    ['How many levels of questions the tree can ask — deeper trees can model more complex patterns but risk overfitting', true],
                    ['The number of training samples the tree uses', false],
                    ['The threshold value for each split', false],
                ],
            ],
            [
                'q' => 'A decision tree with unlimited depth is trained on 200 samples and achieves 100% training accuracy but 65% test accuracy. The best fix is:',
                'opts' => [
                    ['Increase the tree depth further', false],
                    ['Limit the max_depth hyperparameter to reduce overfitting', true],
                    ['Remove all features from the dataset', false],
                    ['Switch from classification to regression', false],
                ],
            ],
            [
                'q' => 'What criterion does a decision tree use to decide which feature to split on at each node?',
                'opts' => [
                    ['It always picks the feature with the most unique values', false],
                    ['It picks the feature that results in the purest child nodes — measured by Gini impurity or Information Gain (entropy)', true],
                    ['It picks features in alphabetical order', false],
                    ['It randomly selects a feature at each node', false],
                ],
            ],
            [
                'q' => 'Gini Impurity of a node is 0. What does this mean?',
                'opts' => [
                    ['The node is maximally impure — all classes are equally represented', false],
                    ['The node is perfectly pure — all samples belong to the same class', true],
                    ['The tree has only one feature', false],
                    ['The split was made randomly', false],
                ],
            ],

            // ── RANDOM FORESTS ────────────────────────────────────────────
            [
                'q' => 'What technique does Random Forest use to create diverse trees?',
                'opts' => [
                    ['It trains all trees on the exact same training data', false],
                    ['Each tree is trained on a random bootstrap sample and uses a random subset of features at each split', true],
                    ['Each tree is assigned a different learning rate', false],
                    ['Each tree is initialized with different random weights', false],
                ],
            ],
            [
                'q' => 'How does a Random Forest make its final classification prediction?',
                'opts' => [
                    ['It uses the first tree\'s prediction', false],
                    ['It takes the average of all tree outputs', false],
                    ['Each tree votes and the majority class wins', true],
                    ['It uses the tree with the highest individual accuracy', false],
                ],
            ],
            [
                'q' => 'What is "feature importance" in a Random Forest?',
                'opts' => [
                    ['The number of times a feature appears in the dataset', false],
                    ['A score that indicates how much each feature contributed to reducing impurity across all trees', true],
                    ['The correlation between a feature and the target label', false],
                    ['Whether a feature is numerical or categorical', false],
                ],
            ],

            // ── SUPPORT VECTOR MACHINES ───────────────────────────────────
            [
                'q' => 'What does a Support Vector Machine (SVM) try to find?',
                'opts' => [
                    ['The line that passes through the most data points', false],
                    ['The hyperplane that maximizes the margin between classes', true],
                    ['The cluster centers of all data groups', false],
                    ['The feature with the highest variance', false],
                ],
            ],
            [
                'q' => 'What are "support vectors" in SVM?',
                'opts' => [
                    ['All training data points used to build the model', false],
                    ['The data points closest to the decision boundary that define the margin', true],
                    ['The predicted class labels for each data point', false],
                    ['The hyperparameters of the model', false],
                ],
            ],
            [
                'q' => 'The "kernel trick" in SVM is used to:',
                'opts' => [
                    ['Speed up training on large datasets', false],
                    ['Map data to a higher-dimensional space so that a linear boundary can separate non-linearly separable classes', true],
                    ['Reduce the number of support vectors needed', false],
                    ['Convert regression into a classification problem', false],
                ],
            ],
            [
                'q' => 'In SVM, the regularization parameter C controls:',
                'opts' => [
                    ['The learning rate of the algorithm', false],
                    ['The trade-off between maximizing the margin and allowing misclassifications — high C = fewer errors but smaller margin', true],
                    ['The number of support vectors used', false],
                    ['The degree of the polynomial kernel', false],
                ],
            ],

            // ── K-NEAREST NEIGHBORS DEPTH ─────────────────────────────────
            [
                'q' => 'A KNN model with K=1 on a training set achieves 100% accuracy. Why is this expected?',
                'opts' => [
                    ['The model is very well regularized', false],
                    ['Each point\'s nearest neighbor on the training set is itself — so it always predicts correctly (but likely overfits)', true],
                    ['K=1 means only one feature is used', false],
                    ['The training data is perfectly separable', false],
                ],
            ],
            [
                'q' => 'As K increases in KNN, the decision boundary becomes:',
                'opts' => [
                    ['More irregular and jagged (more complex)', false],
                    ['Smoother and simpler — higher K reduces sensitivity to individual points', true],
                    ['Unchanged — K does not affect the boundary shape', false],
                    ['Always linear regardless of K', false],
                ],
            ],
            [
                'q' => 'Why does KNN struggle with high-dimensional data?',
                'opts' => [
                    ['It cannot handle more than 10 features', false],
                    ['In high dimensions, all points become approximately equidistant — making "nearest neighbor" meaningless (curse of dimensionality)', true],
                    ['KNN does not support feature scaling', false],
                    ['The algorithm cannot process numerical features', false],
                ],
            ],
            [
                'q' => 'Why is feature scaling (e.g., normalization) important for KNN?',
                'opts' => [
                    ['KNN requires all features to be binary', false],
                    ['KNN uses distance to find neighbors — features with larger ranges will dominate the distance calculation without scaling', true],
                    ['Scaling reduces training time significantly', false],
                    ['KNN cannot process unscaled data at all', false],
                ],
            ],

            // ── HYPERPARAMETER TUNING ─────────────────────────────────────
            [
                'q' => 'What is Grid Search?',
                'opts' => [
                    ['A visualization tool that displays model parameters on a grid', false],
                    ['An exhaustive search over a predefined set of hyperparameter combinations to find the best-performing configuration', true],
                    ['A search algorithm that finds the closest data points', false],
                    ['A method to remove unnecessary features from a model', false],
                ],
            ],
            [
                'q' => 'What is the risk of tuning hyperparameters directly using the test set?',
                'opts' => [
                    ['The model will train slower', false],
                    ['The test set effectively becomes part of the training process — the final evaluation is no longer unbiased', true],
                    ['The model will overfit the training data', false],
                    ['Hyperparameters cannot be tuned at all in this case', false],
                ],
            ],

            // ── GRADIENT BOOSTING & ENSEMBLE METHODS ─────────────────────
            [
                'q' => 'What is the key difference between Bagging (e.g., Random Forest) and Boosting?',
                'opts' => [
                    ['Bagging uses regression; boosting uses classification', false],
                    ['Bagging trains trees in parallel independently; boosting trains trees sequentially, each correcting the errors of the previous one', true],
                    ['Bagging requires more data than boosting', false],
                    ['Boosting always produces fewer trees than bagging', false],
                ],
            ],
            [
                'q' => 'In gradient boosting, each new tree is trained to:',
                'opts' => [
                    ['Replace the previous tree with a better one', false],
                    ['Fit the residual errors (mistakes) of all previous trees combined', true],
                    ['Predict the training labels from scratch', false],
                    ['Select a new set of features not used by previous trees', false],
                ],
            ],
            [
                'q' => 'Which of the following is a popular gradient boosting library known for its speed and performance?',
                'opts' => [
                    ['Matplotlib', false],
                    ['XGBoost', true],
                    ['NumPy', false],
                    ['SciPy', false],
                ],
            ],

            // ── BIAS-VARIANCE TRADEOFF ────────────────────────────────────
            [
                'q' => 'In the bias-variance tradeoff, what is "bias"?',
                'opts' => [
                    ['The model\'s error due to being too sensitive to noise in training data', false],
                    ['Error from incorrect assumptions in the model — a high-bias model underfits', true],
                    ['The difference between training and test accuracy', false],
                    ['The number of features the model ignores', false],
                ],
            ],
            [
                'q' => 'In the bias-variance tradeoff, what is "variance"?',
                'opts' => [
                    ['Error from the model being too simple to capture patterns', false],
                    ['Error from the model being too sensitive to fluctuations in training data — a high-variance model overfits', true],
                    ['The spread of the training labels', false],
                    ['The difference between precision and recall', false],
                ],
            ],
            [
                'q' => 'A simple model (e.g., linear regression on complex data) tends to have:',
                'opts' => [
                    ['High bias, low variance', true],
                    ['Low bias, high variance', false],
                    ['High bias, high variance', false],
                    ['Low bias, low variance', false],
                ],
            ],
            [
                'q' => 'A very complex model (e.g., a very deep decision tree) tends to have:',
                'opts' => [
                    ['High bias, low variance', false],
                    ['Low bias, high variance', true],
                    ['High bias, high variance', false],
                    ['Low bias, low variance', false],
                ],
            ],

            // ── REGULARIZATION ────────────────────────────────────────────
            [
                'q' => 'What does regularization do in machine learning?',
                'opts' => [
                    ['It speeds up the training process', false],
                    ['It adds a penalty to the model\'s loss function to discourage overly complex models and reduce overfitting', true],
                    ['It removes features that are correlated with each other', false],
                    ['It normalizes the input features to have mean 0', false],
                ],
            ],
            [
                'q' => 'L1 regularization (Lasso) tends to produce:',
                'opts' => [
                    ['Small but non-zero coefficients for all features', false],
                    ['Sparse models where some coefficients become exactly zero (feature selection)', true],
                    ['Models with maximum complexity', false],
                    ['Models identical to those from Ridge regression', false],
                ],
            ],
            [
                'q' => 'L2 regularization (Ridge) tends to produce:',
                'opts' => [
                    ['Models where many coefficients become exactly zero', false],
                    ['Models with small but non-zero coefficients — it shrinks all coefficients but rarely sets them to zero', true],
                    ['Models with higher variance than unregularized models', false],
                    ['The same result as L1 regularization in all cases', false],
                ],
            ],

            // ── EVALUATION METRICS FOR REGRESSION ────────────────────────
            [
                'q' => 'What does MAE (Mean Absolute Error) measure?\nMAE = (1/n) × Σ|yᵢ - ŷᵢ|',
                'opts' => [
                    ['The average of squared prediction errors', false],
                    ['The average absolute difference between actual and predicted values', true],
                    ['The proportion of variance explained by the model', false],
                    ['The maximum prediction error across all samples', false],
                ],
            ],
            [
                'q' => 'Compared to MSE, MAE is:',
                'opts' => [
                    ['More sensitive to large outlier errors', false],
                    ['Less sensitive to large outlier errors — it does not square the differences', true],
                    ['Identical to MSE when errors are normally distributed', false],
                    ['Always a larger value than MSE', false],
                ],
            ],

            // ── PIPELINE & PRACTICAL ──────────────────────────────────────
            [
                'q' => 'What is a machine learning pipeline?',
                'opts' => [
                    ['A visualization of model performance over time', false],
                    ['A sequence of data processing and modeling steps chained together so they can be applied consistently', true],
                    ['A type of ensemble method that combines pipelines', false],
                    ['A network architecture used in deep learning', false],
                ],
            ],
            [
                'q' => 'Why should data preprocessing (e.g., scaling, imputation) be fit ONLY on the training set and then applied to the test set?',
                'opts' => [
                    ['Because it is faster to fit on a smaller dataset', false],
                    ['To prevent data leakage — using test set statistics during preprocessing lets test data influence the model\'s training process', true],
                    ['Because the test set always has missing values', false],
                    ['Because scaling is not needed on the test set', false],
                ],
            ],
            [
                'q' => 'You have a dataset where 95% of labels are class 0 and 5% are class 1. A model that always predicts class 0 achieves 95% accuracy. This shows that:',
                'opts' => [
                    ['Accuracy is always a reliable metric', false],
                    ['Accuracy is misleading for imbalanced datasets — metrics like F1 or AUC-ROC are more informative', true],
                    ['The model is performing very well', false],
                    ['The dataset should be discarded', false],
                ],
            ],

        ];

        foreach ($qaData as $data) {
            $question = ChallengeQuestion::create([
                'challenge_id'  => $challenge->id,
                'question_text' => $data['q'],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 14 — Supervised Learning (University Student).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: University Student");
    }
}