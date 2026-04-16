<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module14ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 14 — Supervised Learning (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 1: Supervised Learning',
            'description'           => 'Work through supervised learning problems using Python and scikit-learn. Read and trace code, debug common mistakes, calculate metrics manually, and reason through model selection and validation scenarios.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 900,
            'order_index'           => 14,
        ]);

        $this->command->info("Seeding 50 intermediate supervised learning questions...");

        $qaData = [

            // ── READING SKLEARN CODE ───────────────────────────────────────
            [
                'q' => "What does this code do?\n\nfrom sklearn.model_selection import train_test_split\nX_train, X_test, y_train, y_test = train_test_split(\n    X, y, test_size=0.2, random_state=42\n)",
                'opts' => [
                    ['Normalizes X and y before training', false],
                    ['Splits X and y into 80% training and 20% test sets reproducibly', true],
                    ['Creates a 20-fold cross-validation split', false],
                    ['Filters out 20% of outliers from the dataset', false],
                ],
            ],
            [
                'q' => "What is the purpose of `random_state=42` in scikit-learn?\n\ntrain_test_split(X, y, test_size=0.2, random_state=42)",
                'opts' => [
                    ['It sets the train size to 42%', false],
                    ['It ensures the split is reproducible — the same result every time the code runs', true],
                    ['It tells sklearn to use 42 trees in the model', false],
                    ['It shuffles the data 42 times before splitting', false],
                ],
            ],
            [
                'q' => "What does this pipeline do step by step?\n\nfrom sklearn.pipeline import Pipeline\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.linear_model import LogisticRegression\n\npipe = Pipeline([\n    ('scaler', StandardScaler()),\n    ('clf', LogisticRegression())\n])\npipe.fit(X_train, y_train)\npipe.predict(X_test)",
                'opts' => [
                    ['Scales all data globally then classifies', false],
                    ['Fits scaler on training data → transforms training data → trains logistic regression → transforms test data with the SAME scaler → predicts', true],
                    ['Trains logistic regression first, then scales output', false],
                    ['Applies scaling only to the test set', false],
                ],
            ],
            [
                'q' => "What will this code print?\n\nfrom sklearn.linear_model import LinearRegression\nimport numpy as np\n\nX = np.array([[1],[2],[3],[4],[5]])\ny = np.array([2, 4, 6, 8, 10])\n\nmodel = LinearRegression()\nmodel.fit(X, y)\nprint(model.predict([[6]]))",
                'opts' => [
                    ['[10]', false],
                    ['[12]', true],
                    ['[6]', false],
                    ['[11]', false],
                ],
            ],
            [
                'q' => "What does this code compute?\n\nfrom sklearn.metrics import mean_squared_error\nimport numpy as np\n\ny_true = [3, 5, 4]\ny_pred = [2, 5, 6]\nprint(mean_squared_error(y_true, y_pred))",
                'opts' => [
                    ['1.0', false],
                    ['1.67', true],
                    ['2.0', false],
                    ['5.0', false],
                ],
            ],

            // ── CROSS-VALIDATION CODE ─────────────────────────────────────
            [
                'q' => "What does this output represent?\n\nfrom sklearn.model_selection import cross_val_score\nfrom sklearn.tree import DecisionTreeClassifier\n\nmodel = DecisionTreeClassifier()\nscores = cross_val_score(model, X, y, cv=5)\nprint(scores.mean(), scores.std())",
                'opts' => [
                    ['The training accuracy and its variance', false],
                    ['The mean and standard deviation of 5-fold cross-validation accuracy scores', true],
                    ['The F1 score and its variance across 5 trees', false],
                    ['The mean and std of feature importances', false],
                ],
            ],
            [
                'q' => "A model returns these 5-fold CV scores: [0.82, 0.79, 0.85, 0.80, 0.84].\nWhat is the mean CV accuracy?",
                'opts' => [
                    ['0.81', false],
                    ['0.82', true],
                    ['0.83', false],
                    ['0.85', false],
                ],
            ],

            // ── DEBUGGING COMMON ML MISTAKES ──────────────────────────────
            [
                'q' => "What is the data leakage bug in this code?\n\nfrom sklearn.preprocessing import StandardScaler\n\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)  # fit on ALL data\n\nX_train, X_test = X_scaled[:80], X_scaled[80:]",
                'opts' => [
                    ['StandardScaler cannot be used before splitting', false],
                    ['The scaler was fit on the entire dataset (including test data) before splitting — test statistics leaked into the scaler', true],
                    ['X_train and X_test share the same indices', false],
                    ['fit_transform should be transform only', false],
                ],
            ],
            [
                'q' => "Find the bug in this logistic regression evaluation:\n\nmodel.fit(X_train, y_train)\naccuracy = model.score(X_train, y_train)  # line A\nprint(f'Test accuracy: {accuracy}')",
                'opts' => [
                    ['model.fit() is called before model.score()', false],
                    ['Line A evaluates on X_train instead of X_test — this measures training accuracy, not test accuracy', true],
                    ['LogisticRegression does not have a .score() method', false],
                    ['The f-string syntax is invalid in Python', false],
                ],
            ],
            [
                'q' => "What is wrong with this cross-validation setup?\n\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.model_selection import cross_val_score\nfrom sklearn.svm import SVC\n\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)  # applied to all data\nscores = cross_val_score(SVC(), X_scaled, y, cv=5)",
                'opts' => [
                    ['SVC does not support cross_val_score', false],
                    ['The scaler was fit on all data before cross-validation — each fold\'s validation data statistics leak into the scaler', true],
                    ['cv=5 is too small for this dataset', false],
                    ['SVC requires y to be continuous', false],
                ],
            ],
            [
                'q' => "What does this print? Trace through it.\n\nfrom sklearn.tree import DecisionTreeClassifier\n\nmodel = DecisionTreeClassifier(max_depth=None)\nmodel.fit(X_train, y_train)\nprint(model.score(X_train, y_train))\nprint(model.score(X_test, y_test))\n\n# X_train has 200 samples, a complex dataset",
                'opts' => [
                    ['Both scores will be around 70–80%', false],
                    ['Training score ≈ 1.0, test score significantly lower — classic overfitting with unlimited depth', true],
                    ['Both scores will be around 50% — underfitting', false],
                    ['Training score will be lower than test score', false],
                ],
            ],

            // ── DECISION TREE GINI & INFO GAIN ────────────────────────────
            [
                'q' => "A node has 10 samples: 6 class A, 4 class B.\nCompute Gini Impurity:\nGini = 1 - Σ(pᵢ²)\np_A = 0.6, p_B = 0.4",
                'opts' => [
                    ['0.38', false],
                    ['0.48', true],
                    ['0.52', false],
                    ['0.60', false],
                ],
            ],
            [
                'q' => "A pure node (all one class) has Gini Impurity of:\nGini = 1 - (1.0² + 0.0²)",
                'opts' => [
                    ['1.0', false],
                    ['0.5', false],
                    ['0.0', true],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => "Parent node Gini = 0.48 (10 samples).\nAfter a split: Left node has 6 samples, Gini=0.0. Right node has 4 samples, Gini=0.0.\nWhat is the Gini Gain?",
                'opts' => [
                    ['0.24', false],
                    ['0.48', true],
                    ['0.0', false],
                    ['1.0', false],
                ],
            ],

            // ── LOGISTIC REGRESSION MATH ──────────────────────────────────
            [
                'q' => "Logistic regression computes: p = 1 / (1 + e^(-z))\nIf z = 0, what is p?",
                'opts' => [
                    ['0', false],
                    ['0.5', true],
                    ['1', false],
                    ['Undefined', false],
                ],
            ],
            [
                'q' => "If z = 2.0, the sigmoid output is approximately:\np = 1 / (1 + e^(-2)) ≈ 1 / (1 + 0.135)",
                'opts' => [
                    ['0.50', false],
                    ['0.73', false],
                    ['0.88', true],
                    ['0.99', false],
                ],
            ],
            [
                'q' => "A logistic regression model: log-odds = 1.5 + 0.8×(hours_studied)\nFor a student who studied 2 hours:\nlog-odds = 1.5 + 0.8×2 = 3.1\np = 1/(1 + e^(-3.1)) ≈ 0.957\n\nWhat is the predicted class at threshold 0.5?",
                'opts' => [
                    ['Class 0 (Fail)', false],
                    ['Class 1 (Pass)', true],
                    ['Uncertain — output is between 0 and 1', false],
                    ['Class 2 — multi-class output', false],
                ],
            ],

            // ── SVM CODE & KERNELS ────────────────────────────────────────
            [
                'q' => "What does this SVM code do differently from a linear SVM?\n\nfrom sklearn.svm import SVC\nmodel = SVC(kernel='rbf', C=1.0, gamma='scale')\nmodel.fit(X_train, y_train)",
                'opts' => [
                    ['It uses a linear decision boundary', false],
                    ['It uses the Radial Basis Function kernel — mapping data to infinite dimensions to find non-linear decision boundaries', true],
                    ['It applies gradient boosting on top of SVM', false],
                    ['It trains one SVM per class', false],
                ],
            ],
            [
                'q' => "Compare these two SVMs:\nModel A: SVC(C=0.01)\nModel B: SVC(C=1000)\n\nWhich is more likely to overfit on a noisy dataset?",
                'opts' => [
                    ['Model A (C=0.01) — low C increases model complexity', false],
                    ['Model B (C=1000) — high C forces the model to minimize errors even on noisy points, reducing margin and overfitting', true],
                    ['Both will overfit equally', false],
                    ['Neither — SVM never overfits', false],
                ],
            ],

            // ── KNN CODE & DISTANCE ────────────────────────────────────────
            [
                'q' => "What does this code output, and what algorithm does it use?\n\nfrom sklearn.neighbors import KNeighborsClassifier\n\nmodel = KNeighborsClassifier(n_neighbors=3)\nmodel.fit(X_train, y_train)\ny_pred = model.predict(X_test)\nprint(y_pred[:5])",
                'opts' => [
                    ['The 3 nearest training points to each test point', false],
                    ['Class predictions for the first 5 test samples using majority vote from 3 nearest neighbors', true],
                    ['The distance to the 3 nearest neighbors for each test point', false],
                    ['The probabilities of each class for the first 5 test samples', false],
                ],
            ],
            [
                'q' => "Point A = (1, 2), Point B = (4, 6).\nCompute the Euclidean distance:\nd = √((4-1)² + (6-2)²)",
                'opts' => [
                    ['3', false],
                    ['4', false],
                    ['5', true],
                    ['7', false],
                ],
            ],
            [
                'q' => "You run KNN with different K values and get:\nK=1: Train=100%, Test=72%\nK=5: Train=89%, Test=83%\nK=15: Train=81%, Test=80%\nK=50: Train=74%, Test=73%\n\nWhich K is optimal based on this data?",
                'opts' => [
                    ['K=1 — highest training accuracy', false],
                    ['K=5 — best test accuracy while keeping a reasonable train/test gap', true],
                    ['K=50 — most stable across train and test', false],
                    ['K=15 — smallest gap between train and test accuracy', false],
                ],
            ],

            // ── RANDOM FOREST CODE ────────────────────────────────────────
            [
                'q' => "What does this code output?\n\nfrom sklearn.ensemble import RandomForestClassifier\nimport pandas as pd\n\nmodel = RandomForestClassifier(n_estimators=100, random_state=42)\nmodel.fit(X_train, y_train)\n\nimportances = pd.Series(model.feature_importances_, index=feature_names)\nprint(importances.sort_values(ascending=False))",
                'opts' => [
                    ['The accuracy of each of the 100 trees', false],
                    ['Feature importances ranked from most to least important', true],
                    ['The OOB (out-of-bag) score for each feature', false],
                    ['The correlation matrix of features', false],
                ],
            ],
            [
                'q' => "What is the OOB (Out-Of-Bag) score in Random Forest?\n\nmodel = RandomForestClassifier(oob_score=True)\nmodel.fit(X_train, y_train)\nprint(model.oob_score_)",
                'opts' => [
                    ['The accuracy on the full training set', false],
                    ['An internal validation score using samples not included in each tree\'s bootstrap sample — a free cross-validation estimate', true],
                    ['The accuracy on the test set', false],
                    ['The mean Gini impurity across all trees', false],
                ],
            ],

            // ── GRADIENT BOOSTING CODE ────────────────────────────────────
            [
                'q' => "What does the `learning_rate` parameter control in gradient boosting?\n\nfrom sklearn.ensemble import GradientBoostingClassifier\nmodel = GradientBoostingClassifier(\n    n_estimators=100,\n    learning_rate=0.1,\n    max_depth=3\n)",
                'opts' => [
                    ['The speed of the training algorithm\'s optimization steps', false],
                    ['How much each new tree\'s contribution is shrunk — smaller values require more trees but often generalize better', true],
                    ['The learning schedule for the neural network underneath', false],
                    ['The maximum number of features used by each tree', false],
                ],
            ],
            [
                'q' => "Compare these two gradient boosting configurations.\nWhich is more likely to overfit?\n\nModel A: n_estimators=1000, learning_rate=0.5, max_depth=8\nModel B: n_estimators=100, learning_rate=0.05, max_depth=3",
                'opts' => [
                    ['Model B — more trees cause more complexity', false],
                    ['Model A — high n_estimators, high learning rate, and deep trees all increase complexity and overfitting risk', true],
                    ['Both will overfit equally', false],
                    ['Neither — gradient boosting does not overfit', false],
                ],
            ],

            // ── MULTI-STEP METRIC CALCULATIONS ────────────────────────────
            [
                'q' => "Confusion matrix results:\nTP=50, TN=40, FP=10, FN=20\n\nCompute ALL three: Accuracy, Precision, Recall\nAccuracy = (TP+TN)/(TP+TN+FP+FN)\nPrecision = TP/(TP+FP)\nRecall = TP/(TP+FN)",
                'opts' => [
                    ['Acc=0.75, Pre=0.80, Rec=0.71', true],
                    ['Acc=0.80, Pre=0.75, Rec=0.71', false],
                    ['Acc=0.75, Pre=0.71, Rec=0.80', false],
                    ['Acc=0.83, Pre=0.83, Rec=0.75', false],
                ],
            ],
            [
                'q' => "Using Precision=0.80 and Recall=0.71 from above:\nCompute the F1 Score:\nF1 = 2 × (P × R) / (P + R)",
                'opts' => [
                    ['0.73', false],
                    ['0.75', false],
                    ['0.753', true],
                    ['0.80', false],
                ],
            ],
            [
                'q' => "What does AUC-ROC measure?\n\nfrom sklearn.metrics import roc_auc_score\nauc = roc_auc_score(y_test, y_prob)\nprint(auc)",
                'opts' => [
                    ['The accuracy of the model at a 0.5 threshold', false],
                    ['The area under the ROC curve — the model\'s ability to distinguish between classes across all thresholds (1.0 = perfect, 0.5 = random)', true],
                    ['The precision at a specific recall threshold', false],
                    ['The log-loss of the model', false],
                ],
            ],

            // ── HYPERPARAMETER TUNING CODE ────────────────────────────────
            [
                'q' => "What does this code search for?\n\nfrom sklearn.model_selection import GridSearchCV\n\nparam_grid = {\n    'max_depth': [3, 5, 10],\n    'min_samples_split': [2, 5, 10]\n}\n\ngrid = GridSearchCV(DecisionTreeClassifier(), param_grid, cv=5)\ngrid.fit(X_train, y_train)\nprint(grid.best_params_)",
                'opts' => [
                    ['The best random forest configuration', false],
                    ['The best combination of max_depth and min_samples_split using 5-fold CV on 9 total combinations', true],
                    ['The single best feature in the dataset', false],
                    ['The optimal training set size', false],
                ],
            ],
            [
                'q' => "How many models are trained in total in the above GridSearchCV?\n(3 max_depth values × 3 min_samples_split values × 5 folds)",
                'opts' => [
                    ['9', false],
                    ['15', false],
                    ['45', true],
                    ['30', false],
                ],
            ],
            [
                'q' => "What is the difference between GridSearchCV and RandomizedSearchCV?\n\nfrom sklearn.model_selection import RandomizedSearchCV\nrandom = RandomizedSearchCV(\n    estimator, param_dist, n_iter=20, cv=5\n)",
                'opts' => [
                    ['GridSearchCV is faster because it uses random sampling', false],
                    ['RandomizedSearchCV samples n_iter random combinations instead of trying every combination — much faster for large parameter spaces', true],
                    ['RandomizedSearchCV always finds a better result than GridSearchCV', false],
                    ['They produce identical results for the same parameter grid', false],
                ],
            ],

            // ── REGULARIZATION IN CODE ────────────────────────────────────
            [
                'q' => "What does increasing alpha do in Ridge regression?\n\nfrom sklearn.linear_model import Ridge\nmodel_low  = Ridge(alpha=0.01)\nmodel_high = Ridge(alpha=100)",
                'opts' => [
                    ['Higher alpha makes coefficients larger and the model more flexible', false],
                    ['Higher alpha applies stronger regularization — shrinking coefficients more aggressively and reducing overfitting', true],
                    ['Alpha controls the learning rate during training', false],
                    ['Alpha sets the number of iterations for convergence', false],
                ],
            ],
            [
                'q' => "Which model will produce sparse coefficients (some exactly = 0)?\n\nfrom sklearn.linear_model import Lasso, Ridge\nlasso = Lasso(alpha=1.0)\nridge = Ridge(alpha=1.0)",
                'opts' => [
                    ['Ridge — L2 regularization zeros out small coefficients', false],
                    ['Lasso — L1 regularization can set coefficients exactly to zero, performing feature selection', true],
                    ['Both produce the same sparsity', false],
                    ['Neither — regularization never zeros out coefficients', false],
                ],
            ],

            // ── PRACTICAL MULTI-STEP SCENARIOS ────────────────────────────
            [
                'q' => "You are building a medical diagnosis model. Missing a positive case (predicting healthy when sick) is far more dangerous than a false alarm.\nWhich metric should you MAXIMIZE?",
                'opts' => [
                    ['Precision — minimize false alarms', false],
                    ['Recall — minimize false negatives (missed sick patients)', true],
                    ['Accuracy — overall correctness matters most', false],
                    ['Specificity — minimize false positives', false],
                ],
            ],
            [
                'q' => "A spam filter falsely marks important emails as spam. Which metric are you failing on?\nFalse Positive = marking a real email as spam",
                'opts' => [
                    ['Recall — missing actual spam', false],
                    ['Precision — too many legitimate emails predicted as spam', true],
                    ['Accuracy — overall wrong predictions', false],
                    ['AUC — discriminating spam from not-spam', false],
                ],
            ],
            [
                'q' => "Your model has:\nTraining accuracy = 97%\nCV accuracy = 95%\nTest accuracy = 94%\n\nWhat conclusion do you draw?",
                'opts' => [
                    ['Severe overfitting — the model must be simplified', false],
                    ['The model generalizes well — all three scores are close with no major gap', true],
                    ['Underfitting — the test accuracy should equal training accuracy', false],
                    ['Data leakage — the gap between training and test is suspicious', false],
                ],
            ],
            [
                'q' => "You train a Random Forest and a Logistic Regression on the same data:\nRF: Train=96%, Test=85%\nLR: Train=79%, Test=78%\n\nFor production, which model should you prefer and why?",
                'opts' => [
                    ['Random Forest — it has higher test accuracy', true],
                    ['Logistic Regression — it has a smaller train/test gap', false],
                    ['Random Forest — it has higher training accuracy', false],
                    ['Logistic Regression — simpler models always win in production', false],
                ],
            ],
            [
                'q' => "What is the problem with this evaluation strategy?\n\nmodel.fit(X, y)  # trained on ALL data\naccuracy = model.score(X, y)  # evaluated on same data\nprint(f'Model accuracy: {accuracy}')",
                'opts' => [
                    ['model.score() is not a valid method', false],
                    ['The model is evaluated on the same data it was trained on — this is optimistic and does not reflect real-world performance', true],
                    ['You should use cross_val_score instead of fit', false],
                    ['Training on all data is always correct for final deployment', false],
                ],
            ],

            // ── ADVANCED TRACING ──────────────────────────────────────────
            [
                'q' => "What does this code measure and why is it more appropriate than accuracy here?\n\nfrom sklearn.metrics import f1_score\nfrom sklearn.utils import resample\n\n# Dataset: 950 class 0, 50 class 1\ny_pred = [0] * 1000  # always predict majority class\nprint(f1_score(y_test, y_pred, pos_label=1))",
                'opts' => [
                    ['F1 = 0.95 — confirms the model works well on imbalanced data', false],
                    ['F1 = 0.0 — exposes that always predicting class 0 finds no positive cases, unlike accuracy (95%) which was misleading', true],
                    ['F1 = 0.50 — average of precision and recall', false],
                    ['F1 = 1.0 — the model is perfect by majority class definition', false],
                ],
            ],
            [
                'q' => "What is the effect of setting `class_weight='balanced'` in sklearn?\n\nfrom sklearn.linear_model import LogisticRegression\nmodel = LogisticRegression(class_weight='balanced')",
                'opts' => [
                    ['It removes minority class samples from training', false],
                    ['It automatically adjusts weights so minority classes receive proportionally higher importance during training', true],
                    ['It balances the dataset by oversampling the majority class', false],
                    ['It ensures predictions are balanced between all classes', false],
                ],
            ],

            // ── REGRESSION DEEP DIVE ──────────────────────────────────────
            [
                'q' => "You fit a polynomial regression of degree 10 to 15 data points.\nTraining MSE = 0.001, Test MSE = 45.7\nWhat is happening and what is the fix?",
                'opts' => [
                    ['Underfitting — increase polynomial degree further', false],
                    ['Overfitting — the degree-10 polynomial memorizes noise; reduce degree or apply regularization', true],
                    ['The model is correct — training error is always lower than test error', false],
                    ['The test set is too small to evaluate accurately', false],
                ],
            ],
            [
                'q' => "Actual: [10, 20, 30, 40, 50]\nPredicted: [12, 19, 28, 42, 49]\n\nCompute MAE:\nMAE = mean(|10-12|, |20-19|, |30-28|, |40-42|, |50-49|)",
                'opts' => [
                    ['1.2', false],
                    ['1.4', false],
                    ['1.6', true],
                    ['2.0', false],
                ],
            ],
            [
                'q' => "Same predictions above. Compute MSE:\nMSE = mean(2², 1², 2², 2², 1²)",
                'opts' => [
                    ['1.6', false],
                    ['2.0', true],
                    ['2.4', false],
                    ['4.0', false],
                ],
            ],

            // ── LEARNING CURVES ───────────────────────────────────────────
            [
                'q' => "A learning curve shows training and validation accuracy as training size increases.\nYou observe: both curves plateau at ~75% accuracy even with 10,000 samples.\nWhat does this indicate?",
                'opts' => [
                    ['Overfitting — the model needs fewer samples', false],
                    ['High bias (underfitting) — more data won\'t help; the model is too simple for this problem', true],
                    ['The model has converged perfectly and is production-ready', false],
                    ['The validation set is too small to produce reliable curves', false],
                ],
            ],
            [
                'q' => "A learning curve shows: training accuracy = 99%, validation accuracy starts at 60% and slowly rises toward 80% as training size increases.\nWhat does this indicate?",
                'opts' => [
                    ['High bias — the model is underfitting', false],
                    ['High variance — the model overfits small datasets but improves with more data', true],
                    ['The model has converged — add more features to improve', false],
                    ['The validation set is incorrectly labeled', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 14 — Supervised Learning (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}