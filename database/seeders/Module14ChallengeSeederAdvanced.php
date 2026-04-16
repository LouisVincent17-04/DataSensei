<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module14ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 14 — Supervised Learning (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 1: Supervised Learning',
            'description'           => 'Tackle advanced supervised learning challenges: debug subtle code issues, reason through model internals, optimize training pipelines, interpret diagnostic plots, and evaluate real-world tradeoffs in regression, classification, and ensemble methods.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1100,
            'order_index'           => 14,
        ]);

        $this->command->info("Seeding 50 advanced supervised learning questions...");

        $qaData = [

            // ── DEBUGGING ADVANCED PIPELINES ──────────────────────────────
            [
                'q' => "Find all bugs in this pipeline:\n\nfrom sklearn.pipeline import Pipeline\nfrom sklearn.preprocessing import StandardScaler\nfrom sklearn.svm import SVC\nfrom sklearn.model_selection import cross_val_score\n\npipe = Pipeline([('scaler', StandardScaler()), ('svc', SVC())])\n\nscaler = StandardScaler()\nX_scaled = scaler.fit_transform(X)  # Bug: applied before CV\n\nscores = cross_val_score(pipe, X_scaled, y, cv=5)",
                'opts' => [
                    ['The pipeline steps are in the wrong order', false],
                    ['X_scaled was computed by fitting on all data first — this leaks test fold statistics into every CV fold, even though the pipeline would re-scale correctly on its own', true],
                    ['SVC cannot be the last step in a pipeline', false],
                    ['cross_val_score does not support pipelines', false],
                ],
            ],
            [
                'q' => "What is the subtle bug and its consequence?\n\nfrom sklearn.model_selection import train_test_split\nfrom sklearn.preprocessing import LabelEncoder\n\nle = LabelEncoder()\ny_encoded = le.fit_transform(y)\n\nX_train, X_test, y_train, y_test = train_test_split(\n    X, y_encoded, test_size=0.2, random_state=42\n)\n# Model trained... then on new production data:\ny_new_encoded = le.transform(y_new)  # y_new has a class not in training",
                'opts' => [
                    ['LabelEncoder should be applied after splitting', false],
                    ['If y_new contains a class unseen during le.fit(), transform() raises ValueError — the encoder was never taught that class', true],
                    ['LabelEncoder cannot be used with train_test_split', false],
                    ['y_encoded should be float, not int', false],
                ],
            ],
            [
                'q' => "What goes wrong when you run this code?\n\nfrom sklearn.model_selection import GridSearchCV\nfrom sklearn.ensemble import RandomForestClassifier\n\nparam_grid = {'n_estimators': [100, 500], 'max_depth': [5, 10]}\ngrid = GridSearchCV(RandomForestClassifier(), param_grid, cv=5, scoring='accuracy')\ngrid.fit(X_train, y_train)\n\n# Developer evaluates on test set:\nprint(grid.score(X_test, y_test))  # Is this valid?",
                'opts' => [
                    ['GridSearchCV.score() raises an error on unseen data', false],
                    ['This is valid — grid.score() correctly uses the best estimator refitted on full X_train to predict X_test', true],
                    ['The test set was used during GridSearchCV, so this result is biased', false],
                    ['You must call grid.best_estimator_.predict() instead', false],
                ],
            ],
            [
                'q' => "Why does this custom CV loop produce an optimistic bias?\n\nbest_acc = 0\nbest_model = None\nfor depth in [1, 3, 5, 7, 10]:\n    model = DecisionTreeClassifier(max_depth=depth)\n    model.fit(X_train, y_train)\n    acc = model.score(X_test, y_test)  # Bug here\n    if acc > best_acc:\n        best_acc = acc\n        best_model = model\n\nprint(f'Best test accuracy: {best_acc}')",
                'opts' => [
                    ['The loop should use cross_val_score instead of fit/score', false],
                    ['Hyperparameters are being tuned using the test set — the final reported accuracy is not an unbiased estimate of true generalization performance', true],
                    ['DecisionTreeClassifier cannot be instantiated in a loop', false],
                    ['The bias comes from not shuffling the data first', false],
                ],
            ],

            // ── ADVANCED GRADIENT BOOSTING ────────────────────────────────
            [
                'q' => "What does this XGBoost early stopping configuration do?\n\nimport xgboost as xgb\nmodel = xgb.XGBClassifier(\n    n_estimators=1000,\n    learning_rate=0.01,\n    early_stopping_rounds=50\n)\nmodel.fit(\n    X_train, y_train,\n    eval_set=[(X_val, y_val)],\n    verbose=False\n)",
                'opts' => [
                    ['Trains exactly 1000 trees and reports the best of them', false],
                    ['Trains up to 1000 trees but stops automatically if validation loss does not improve for 50 consecutive rounds — preventing overfitting', true],
                    ['Stops training when training loss drops below 0.01', false],
                    ['Uses 50% of training data as a hold-out for early stopping', false],
                ],
            ],
            [
                'q' => "In gradient boosting, what does `subsample=0.8` do?\n\nfrom sklearn.ensemble import GradientBoostingClassifier\nmodel = GradientBoostingClassifier(\n    n_estimators=200,\n    subsample=0.8,\n    max_depth=4\n)",
                'opts' => [
                    ['Each tree uses only 80% of the features', false],
                    ['Each tree is trained on a random 80% of training samples (without replacement) — introduces stochasticity that reduces variance', true],
                    ['Training stops after 80% of trees are built', false],
                    ['Only 80% of the data is loaded into memory', false],
                ],
            ],
            [
                'q' => "LightGBM uses 'leaf-wise' tree growth vs. XGBoost's 'level-wise' growth.\nWhich statement is correct?",
                'opts' => [
                    ['Level-wise grows deeper trees faster than leaf-wise', false],
                    ['Leaf-wise grows the leaf with the maximum loss reduction at each step — faster convergence but higher overfitting risk on small datasets', true],
                    ['Both strategies produce identical trees given the same parameters', false],
                    ['Leaf-wise is only valid for regression, not classification', false],
                ],
            ],

            // ── ADVANCED REGULARIZATION & LOSS ───────────────────────────
            [
                'q' => "What is the effect of Elastic Net regularization?\n\nfrom sklearn.linear_model import ElasticNet\nmodel = ElasticNet(alpha=1.0, l1_ratio=0.5)",
                'opts' => [
                    ['It applies only L2 regularization with a 50% penalty', false],
                    ['It combines L1 and L2 penalties — l1_ratio=0.5 means equal mix — getting sparse coefficients (L1) AND stability (L2)', true],
                    ['It applies L1 then L2 sequentially in two passes', false],
                    ['It is only valid for binary classification, not regression', false],
                ],
            ],
            [
                'q' => "You train logistic regression with these two loss curves:\nModel A: training loss steadily decreasing, validation loss diverging upward after epoch 20\nModel B: both losses decreasing and converging\n\nWhat do these indicate respectively?",
                'opts' => [
                    ['A: underfitting, B: overfitting', false],
                    ['A: overfitting (validation loss diverges), B: healthy training (both converge)', true],
                    ['A: correct training, B: underfitting', false],
                    ['A and B are both overfitting at different rates', false],
                ],
            ],
            [
                'q' => "What does Huber loss do differently from MSE for regression?\n\nfrom sklearn.linear_model import HuberRegressor\nmodel = HuberRegressor(epsilon=1.35)",
                'opts' => [
                    ['It penalizes all errors quadratically like MSE but with a threshold', false],
                    ['It behaves like MSE for small errors and like MAE (linear) for large errors — robust to outliers', true],
                    ['It is identical to MSE when there are no outliers', false],
                    ['It minimizes the maximum error across all samples', false],
                ],
            ],

            // ── ADVANCED EVALUATION & METRICS ────────────────────────────
            [
                'q' => "Interpret this ROC curve scenario:\nModel A: AUC = 0.96\nModel B: AUC = 0.73\nModel C: AUC = 0.50\n\nThe task is fraud detection with 0.1% fraud rate.\nWhich model should you choose and what additional check matters?",
                'opts' => [
                    ['Model A — highest AUC always wins', false],
                    ['Model A for AUC, but also check Precision-Recall AUC and performance at operationally relevant thresholds — with 0.1% fraud, PR-AUC is more informative', true],
                    ['Model B — AUC of 0.73 is safer for imbalanced data', false],
                    ['Model C — at 0.50 AUC it avoids false positives entirely', false],
                ],
            ],
            [
                'q' => "What is the Precision-Recall curve and when is it preferred over the ROC curve?",
                'opts' => [
                    ['It is always preferred — ROC is obsolete', false],
                    ['It plots precision vs. recall at every threshold — preferred when the positive class is rare (highly imbalanced datasets) because it focuses on positive class performance', true],
                    ['It is used only for multi-class problems', false],
                    ['It gives the same information as ROC when AUC > 0.8', false],
                ],
            ],
            [
                'q' => "You lower a logistic regression classifier's decision threshold from 0.5 to 0.2.\nWhat are the DIRECT effects on Precision and Recall?",
                'opts' => [
                    ['Both precision and recall increase', false],
                    ['Recall increases (fewer missed positives) but precision decreases (more false alarms)', true],
                    ['Precision increases but recall decreases', false],
                    ['Neither changes — threshold only affects the classification output format', false],
                ],
            ],
            [
                'q' => "Cohen's Kappa score is used as an evaluation metric because:\n\nfrom sklearn.metrics import cohen_kappa_score\nkappa = cohen_kappa_score(y_true, y_pred)",
                'opts' => [
                    ['It measures accuracy without requiring balanced classes', false],
                    ['It measures classification agreement beyond what would be expected by chance — more meaningful than raw accuracy for imbalanced problems', true],
                    ['It replaces F1 score for multi-class classification', false],
                    ['It measures the correlation between predicted probabilities and true labels', false],
                ],
            ],

            // ── SVM INTERNALS ─────────────────────────────────────────────
            [
                'q' => "What does this code reveal about the fitted SVM?\n\nfrom sklearn.svm import SVC\nmodel = SVC(kernel='rbf', C=1.0)\nmodel.fit(X_train, y_train)\nprint(len(model.support_vectors_))\nprint(model.support_vectors_.shape)",
                'opts' => [
                    ['The number of features and their importance rankings', false],
                    ['The number of support vectors and their feature values — fewer support vectors often means a better-regularized, more generalizable model', true],
                    ['The kernel matrix dimensions', false],
                    ['The decision function values for each training point', false],
                ],
            ],
            [
                'q' => "When gamma is set very high in an RBF SVM:\n`SVC(kernel='rbf', gamma=100)`\nWhat is the effect on the decision boundary?",
                'opts' => [
                    ['The boundary becomes linear — high gamma simplifies the model', false],
                    ['The influence of each training point becomes very local — the boundary wraps tightly around individual training points, likely overfitting', true],
                    ['High gamma forces the model to ignore outliers', false],
                    ['Gamma only affects the training speed, not the boundary shape', false],
                ],
            ],

            // ── FEATURE ENGINEERING & SELECTION ──────────────────────────
            [
                'q' => "What does this feature selection code do?\n\nfrom sklearn.feature_selection import SelectKBest, f_classif\n\nselector = SelectKBest(score_func=f_classif, k=10)\nX_new = selector.fit_transform(X_train, y_train)\n\n# Then apply to test:\nX_test_new = selector.transform(X_test)",
                'opts' => [
                    ['Selects the 10 features with the highest correlation to each other', false],
                    ['Selects the 10 features most statistically associated with the target label using ANOVA F-scores, fit only on training data', true],
                    ['Creates 10 new polynomial features from the original set', false],
                    ['Reduces dimensionality using PCA to 10 components', false],
                ],
            ],
            [
                'q' => "What is Recursive Feature Elimination (RFE) doing here?\n\nfrom sklearn.feature_selection import RFE\nfrom sklearn.linear_model import LogisticRegression\n\nestimator = LogisticRegression()\nselector = RFE(estimator, n_features_to_select=5)\nselector.fit(X_train, y_train)",
                'opts' => [
                    ['It adds features one at a time until 5 are selected', false],
                    ['It recursively trains the model, removes the least important feature each iteration, and repeats until 5 features remain', true],
                    ['It selects the 5 features with highest variance', false],
                    ['It generates 5 synthetic features from the original dataset', false],
                ],
            ],
            [
                'q' => "Why must feature selection be done INSIDE a cross-validation loop rather than before it?\n\n# Wrong:\nselector = SelectKBest(k=10).fit(X, y)\nX_selected = selector.transform(X)\nscores = cross_val_score(model, X_selected, y, cv=5)",
                'opts' => [
                    ['It slows down cross-validation unnecessarily', false],
                    ['Fitting the selector on all X (including val folds) leaks information about which features are important into every fold — inflating performance estimates', true],
                    ['SelectKBest cannot be used with cross_val_score', false],
                    ['Feature selection is always a pre-processing step done before any CV', false],
                ],
            ],

            // ── ADVANCED DECISION TREE / ENSEMBLE INTERNALS ───────────────
            [
                'q' => "What does the `min_samples_leaf` parameter control in Decision Trees?\n\nDecisionTreeClassifier(min_samples_leaf=10)",
                'opts' => [
                    ['The minimum number of samples required to make a split at any node', false],
                    ['The minimum number of training samples that must remain in a leaf after a split — prevents very specific, noisy leaf rules', true],
                    ['The minimum tree depth allowed', false],
                    ['The minimum impurity decrease required for a split', false],
                ],
            ],
            [
                'q' => "What is 'stacking' (stacked generalization) in ensemble learning?\n\nfrom sklearn.ensemble import StackingClassifier\nbase_models = [('rf', RandomForestClassifier()), ('svm', SVC(probability=True))]\nmeta_model = LogisticRegression()\nstacker = StackingClassifier(estimators=base_models, final_estimator=meta_model)",
                'opts' => [
                    ['Training multiple models and averaging their outputs equally', false],
                    ['Training base models on the data, then training a meta-model on their out-of-fold predictions to learn how to best combine them', true],
                    ['Training models sequentially where each corrects the previous', false],
                    ['Randomly selecting which model makes each prediction at inference time', false],
                ],
            ],
            [
                'q' => "In a Voting Classifier with `voting='soft'`:\n\nfrom sklearn.ensemble import VotingClassifier\nvc = VotingClassifier(\n    estimators=[('lr', lr), ('rf', rf), ('svm', svm)],\n    voting='soft'\n)\n\nHow does it make the final prediction?",
                'opts' => [
                    ['Each model votes for a class and the majority wins', false],
                    ['It averages the predicted class probabilities from all models and picks the class with the highest mean probability', true],
                    ['It uses the model with the highest individual accuracy', false],
                    ['It assigns random weights to each model\'s prediction', false],
                ],
            ],

            // ── CALIBRATION & PROBABILITY OUTPUT ─────────────────────────
            [
                'q' => "What does probability calibration address?\n\nfrom sklearn.calibration import CalibratedClassifierCV\ncal_model = CalibratedClassifierCV(SVC(), method='isotonic', cv=5)\ncal_model.fit(X_train, y_train)",
                'opts' => [
                    ['It speeds up the SVC by approximating the kernel matrix', false],
                    ['It adjusts the model\'s output probabilities so they reflect true likelihood — e.g., if model says 0.7, about 70% of such cases should actually be positive', true],
                    ['It balances class weights for imbalanced datasets', false],
                    ['It forces the SVC to output probabilities between 0 and 1', false],
                ],
            ],
            [
                'q' => "A Random Forest trained on imbalanced data consistently outputs:\nP(class=1) between 0.05 and 0.15, even for true positives.\nWhat is the most likely cause?",
                'opts' => [
                    ['The forest has too few trees', false],
                    ['The minority class (class=1) is underrepresented — the model learned a strong prior toward class 0, requiring either class rebalancing, threshold adjustment, or calibration', true],
                    ['Random Forests cannot output probabilities', false],
                    ['The learning rate is too high', false],
                ],
            ],

            // ── ADVANCED CROSS-VALIDATION STRATEGIES ─────────────────────
            [
                'q' => "When should you use Stratified K-Fold instead of regular K-Fold?\n\nfrom sklearn.model_selection import StratifiedKFold\nskf = StratifiedKFold(n_splits=5)\nfor train_idx, val_idx in skf.split(X, y):",
                'opts' => [
                    ['When the dataset has more than 10,000 samples', false],
                    ['When class proportions are imbalanced — StratifiedKFold ensures each fold maintains the same class ratio as the full dataset', true],
                    ['When using regression, not classification', false],
                    ['When you want completely random fold assignment without any structure', false],
                ],
            ],
            [
                'q' => "What is Group K-Fold and when is it essential?\n\nfrom sklearn.model_selection import GroupKFold\ngkf = GroupKFold(n_splits=5)\nfor train_idx, val_idx in gkf.split(X, y, groups=patient_ids):",
                'opts' => [
                    ['A CV method that groups similar features together during splitting', false],
                    ['It ensures all samples from the same group (e.g., same patient) stay in the same fold — prevents data leakage when samples are not independent', true],
                    ['It groups numerical and categorical features into separate folds', false],
                    ['It is identical to Stratified K-Fold for classification tasks', false],
                ],
            ],
            [
                'q' => "Nested cross-validation separates which two concerns?\n\n# Inner CV: hyperparameter tuning\n# Outer CV: model evaluation\nfor train_idx, test_idx in outer_cv.split(X, y):\n    grid = GridSearchCV(model, param_grid, cv=inner_cv)\n    grid.fit(X[train_idx], y[train_idx])\n    score = grid.score(X[test_idx], y[test_idx])",
                'opts' => [
                    ['Feature selection and model training', false],
                    ['Hyperparameter selection (inner loop) from unbiased model evaluation (outer loop) — prevents optimistic bias from tuning on the same data used for evaluation', true],
                    ['Training set creation from test set evaluation', false],
                    ['Regularization from optimization', false],
                ],
            ],

            // ── ADVANCED MATH & DERIVATIONS ───────────────────────────────
            [
                'q' => "The logistic regression log-loss (binary cross-entropy) is:\nL = -[y×log(p) + (1-y)×log(1-p)]\n\nIf y=1 and p=0.9, what is the loss?",
                'opts' => [
                    ['0.0', false],
                    ['~0.105', true],
                    ['~0.9', false],
                    ['~1.0', false],
                ],
            ],
            [
                'q' => "If y=1 and p=0.01 (confident wrong prediction), what happens to the log-loss?\nL = -log(0.01)",
                'opts' => [
                    ['Loss ≈ 0 — the model is nearly correct', false],
                    ['Loss ≈ 4.6 — the model is severely penalized for a confident wrong prediction', true],
                    ['Loss = 1 — constant for all wrong predictions', false],
                    ['Loss is undefined for p=0.01', false],
                ],
            ],
            [
                'q' => "The gradient descent update rule for linear regression is:\nw = w - α × ∇L\nwhere ∇L = (1/n) × Xᵀ(Xw - y)\n\nWith α=0.1, current weight w=3.0, gradient=0.5:\nWhat is the updated weight?",
                'opts' => [
                    ['3.05', false],
                    ['2.95', true],
                    ['3.5', false],
                    ['2.5', false],
                ],
            ],
            [
                'q' => "What is the closed-form solution for linear regression (Normal Equation), and when does it fail?\nw = (XᵀX)⁻¹Xᵀy",
                'opts' => [
                    ['It always works but is slower than gradient descent', false],
                    ['It gives exact weights in one step but fails when XᵀX is singular (features are perfectly collinear) or when n_features >> n_samples making inversion computationally expensive', true],
                    ['It requires iterative convergence like gradient descent', false],
                    ['It only works for logistic regression', false],
                ],
            ],

            // ── REAL-WORLD ML SCENARIOS ───────────────────────────────────
            [
                'q' => "You deploy a classifier that achieves 93% accuracy on your test set.\nAfter 3 months in production, accuracy drops to 71%.\nWhat is the most likely cause?",
                'opts' => [
                    ['The model\'s code was corrupted during deployment', false],
                    ['Distribution shift / concept drift — the statistical properties of production data have changed from the training distribution', true],
                    ['The test set was too small to be reliable', false],
                    ['The model needs to be retrained with more trees', false],
                ],
            ],
            [
                'q' => "You want to interpret WHY a Random Forest predicted class 1 for a specific user.\nWhich technique is designed for this?\n\nfrom shap import TreeExplainer\nexplainer = TreeExplainer(model)\nshap_values = explainer.shap_values(X_test[0])",
                'opts' => [
                    ['Global feature importance from feature_importances_', false],
                    ['SHAP (SHapley Additive exPlanations) — it provides per-prediction explanations of each feature\'s contribution to that specific output', true],
                    ['Permutation importance on the test set', false],
                    ['Confusion matrix analysis', false],
                ],
            ],
            [
                'q' => "What is the difference between model accuracy and model fairness?\n\nModel achieves 92% overall accuracy.\nGroup A accuracy: 95%\nGroup B accuracy: 68%",
                'opts' => [
                    ['Both measure the same thing from different perspectives', false],
                    ['High overall accuracy can mask severe performance disparities between demographic groups — 92% overall hides that Group B is served poorly', true],
                    ['Model fairness only matters for classification, not regression', false],
                    ['Group-level accuracy differences are always due to dataset size differences', false],
                ],
            ],

            // ── ADVANCED IMBALANCED DATA ──────────────────────────────────
            [
                'q' => "What does SMOTE do to handle class imbalance?\n\nfrom imblearn.over_sampling import SMOTE\nsm = SMOTE(random_state=42)\nX_res, y_res = sm.fit_resample(X_train, y_train)",
                'opts' => [
                    ['It removes majority class samples until classes are balanced', false],
                    ['It creates synthetic minority class samples by interpolating between existing minority class neighbors', true],
                    ['It duplicates existing minority class samples randomly', false],
                    ['It applies class weights to the loss function instead of resampling', false],
                ],
            ],
            [
                'q' => "When should SMOTE be applied relative to cross-validation?\n\n# Option A: SMOTE before CV split\n# Option B: SMOTE inside each CV fold on training data only",
                'opts' => [
                    ['Option A — SMOTE before CV is faster and produces the same result', false],
                    ['Option B — SMOTE inside each fold prevents synthetic samples from appearing in validation sets, avoiding optimistic bias', true],
                    ['Both options are equivalent — SMOTE does not cause leakage', false],
                    ['SMOTE should never be used with cross-validation', false],
                ],
            ],

            // ── PERFORMANCE & SCALING ─────────────────────────────────────
            [
                'q' => "Your Random Forest with 1000 trees takes 45 minutes to train.\nWhich hyperparameter change has the GREATEST impact on training speed without necessarily hurting accuracy much?",
                'opts' => [
                    ['Reduce max_depth from None to 5', false],
                    ['Set n_jobs=-1 to use all CPU cores for parallel tree training', true],
                    ['Reduce n_estimators from 1000 to 999', false],
                    ['Enable warm_start=True on the same forest', false],
                ],
            ],
            [
                'q' => "An SVM with RBF kernel on 500,000 samples is extremely slow.\nThe most effective solution is:\n\nfrom sklearn.svm import LinearSVC\nfrom sklearn.kernel_approximation import RBFSampler",
                'opts' => [
                    ['Increase C to speed up convergence', false],
                    ['Use LinearSVC or approximate the RBF kernel (e.g., RBFSampler + LinearSVC) — SVM training is O(n²) to O(n³), making exact RBF SVM infeasible on large datasets', true],
                    ['Reduce the number of support vectors manually', false],
                    ['Switch from kernel="rbf" to kernel="linear" — they produce identical results', false],
                ],
            ],
            [
                'q' => "What is the computational complexity of fitting KNN (k-nearest neighbors) at PREDICTION time for 1 query point against n training samples with d features?",
                'opts' => [
                    ['O(1) — the model is precomputed during training', false],
                    ['O(n × d) — must compute distances to all n training points across d dimensions', true],
                    ['O(k × d) — only k neighbors need to be checked', false],
                    ['O(log n) — KNN uses a sorted tree structure by default', false],
                ],
            ],
            [
                'q' => "KD-Trees and Ball Trees speed up KNN by:\n\nfrom sklearn.neighbors import KNeighborsClassifier\nmodel = KNeighborsClassifier(n_neighbors=5, algorithm='ball_tree')",
                'opts' => [
                    ['Reducing the number of features through PCA before prediction', false],
                    ['Partitioning the training data spatially so that large subsets of points can be pruned without computing distances, reducing average query time', true],
                    ['Caching the most frequent predictions from training', false],
                    ['Approximating distances using hash functions', false],
                ],
            ],
            [
                'q' => "You have 10 million training samples and 500 features.\nRanking these algorithms from fastest to slowest to train:\nA=LinearSVC, B=SVC(kernel='rbf'), C=RandomForest(n_jobs=-1), D=KNN(n_neighbors=5)",
                'opts' => [
                    ['D < A < C < B', false],
                    ['A < C < D < B', false],
                    ['A fastest, then C, then D (training=trivial, but inference slow), B slowest (O(n²–n³))', true],
                    ['All algorithms scale identically with n', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 14 — Supervised Learning (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}