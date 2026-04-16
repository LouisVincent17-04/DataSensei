<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module14ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 14 — Supervised Learning (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Machine Learning 1: Supervised Learning',
            'description'           => 'Solve production-grade supervised learning challenges: ML system design, causal ML, model monitoring at scale, advanced optimization, fairness and reliability engineering, and real-world tradeoffs that practitioners face in industry.',
            'time_limit_seconds'    => 2100,
            'base_xp'               => 1500,
            'order_index'           => 14,
        ]);

        $this->command->info("Seeding 50 professional supervised learning questions...");

        $qaData = [

            // ── ML SYSTEM DESIGN ──────────────────────────────────────────
            [
                'q' => "You are designing a real-time fraud detection system.\nThe model must respond in <50ms per transaction at 100,000 TPS peak load.\nWhich architecture decision is MOST critical for meeting these constraints?",
                'opts' => [
                    ['Use a deep neural network — it achieves the highest AUC', false],
                    ['Use a pre-trained, lightweight model (e.g., gradient boosted trees) served from an in-memory store — complex models may not fit the <50ms latency SLA at scale', true],
                    ['Train a new model for every transaction', false],
                    ['Use k-NN with k=1 — it requires no training time', false],
                ],
            ],
            [
                'q' => "Your ML training pipeline runs nightly. New features are added monthly.\nWhich design principle prevents silent regression in production?\n\n# Consider: data contracts, schema validation, unit tests on transformations",
                'opts' => [
                    ['Retrain more frequently — daily instead of nightly', false],
                    ['Implement data validation (e.g., Great Expectations) at ingestion, schema versioning, and automated regression tests that gate model promotion', true],
                    ['Monitor only the final model accuracy metric in production', false],
                    ['Use a single monolithic pipeline that is harder to change', false],
                ],
            ],
            [
                'q' => "You must serve 12 different country-specific models behind a single prediction API.\nThe models differ in features, preprocessing, and thresholds.\nWhat production pattern should you use?",
                'opts' => [
                    ['Pack all 12 models into one object and switch with if/else at runtime', false],
                    ['Use a model registry (e.g., MLflow) with versioned artifacts per country, a routing layer that selects the model based on request metadata, and separate rollout pipelines per model', true],
                    ['Train a single global model and ignore country-level differences', false],
                    ['Deploy 12 separate microservices — one per model with no shared infrastructure', false],
                ],
            ],
            [
                'q' => "A critical business model is a black box to stakeholders.\nYou are asked to make it explainable without sacrificing performance.\nWhat is the best production-grade approach?",
                'opts' => [
                    ['Replace the model with logistic regression — it is always interpretable', false],
                    ['Deploy a post-hoc explanation layer (SHAP, LIME) alongside the model in the serving infrastructure — compute local explanations on demand without changing the model', true],
                    ['Add feature importance as a global explanation — per-prediction explanations are unnecessary', false],
                    ['Explainability is a research concern and not required in production', false],
                ],
            ],

            // ── ADVANCED MODEL MONITORING ─────────────────────────────────
            [
                'q' => "Your production classifier has stable accuracy for 6 months, then drops 8% in one week.\nYou investigate feature distributions and find P(feature_A) has shifted significantly.\nWhat type of drift is this, and what is the standard response?",
                'opts' => [
                    ['Concept drift — retrain immediately with new data', false],
                    ['Covariate shift (input distribution drift) — investigate why P(X) changed, assess impact on P(Y|X), decide whether retraining on recent data or domain adaptation is needed', true],
                    ['Label drift — check if y labels are being collected correctly', false],
                    ['Model decay — this always happens after 6 months', false],
                ],
            ],
            [
                'q' => "You monitor your model using Population Stability Index (PSI) on prediction scores.\nYou observe PSI = 0.32 between last month and this month.\nWhat does this signal?",
                'opts' => [
                    ['Minor drift — monitor but no action needed (PSI < 0.1 is stable)', false],
                    ['Significant distribution shift (PSI > 0.2) — the model\'s score distribution has changed substantially; investigate root cause and likely retrain', true],
                    ['The model is performing better than before', false],
                    ['PSI = 0.32 is always within acceptable bounds for classifiers', false],
                ],
            ],
            [
                'q' => "What is the difference between monitoring model PERFORMANCE and monitoring model INPUTS in production?\n\n# Performance: accuracy, F1, AUC (requires labels)\n# Inputs: feature distributions, missing rates, schema violations",
                'opts' => [
                    ['They measure the same thing from different angles', false],
                    ['Performance monitoring requires ground truth labels which may arrive with delay — input monitoring can detect drift immediately without labels, serving as an early warning system', true],
                    ['Input monitoring replaces performance monitoring entirely', false],
                    ['Performance monitoring is only possible in batch systems, not streaming', false],
                ],
            ],
            [
                'q' => "You implement shadow mode deployment for a new model.\nWhat does this mean and what are you measuring?",
                'opts' => [
                    ['Training the new model on shadow (synthetic) data before real data', false],
                    ['The new model receives production traffic and makes predictions in parallel with the live model, but its output is NOT served to users — you compare predictions and metrics before promoting it', true],
                    ['Deploying only 1% of traffic to the new model (canary release)', false],
                    ['The model runs in a sandboxed environment with no real data', false],
                ],
            ],

            // ── CAUSAL ML & TREATMENT EFFECTS ────────────────────────────
            [
                'q' => "You train a supervised model to predict who responds to an email promotion.\nThe training data contains users who received AND did not receive the email.\nWhat fundamental limitation exists in using standard supervised ML for treatment effect estimation?",
                'opts' => [
                    ['The model will overfit because treatment is a binary feature', false],
                    ['Standard supervised ML predicts outcomes, not TREATMENT EFFECTS — the counterfactual (what would have happened without the email) is never observed; without causal structure, the model conflates response rate with incremental lift', true],
                    ['Email response data is always imbalanced', false],
                    ['Classification models cannot handle binary treatment variables', false],
                ],
            ],
            [
                'q' => "Uplift modeling (also called incremental response modeling) estimates:\nτ(x) = E[Y(1)|X=x] - E[Y(0)|X=x]\n\nWhat does this quantity represent?",
                'opts' => [
                    ['The probability a user converts without any treatment', false],
                    ['The individual treatment effect — the expected change in outcome for a specific user X=x caused by the treatment, compared to not receiving it', true],
                    ['The model\'s accuracy improvement from adding treatment as a feature', false],
                    ['The correlation between treatment and outcome in the training data', false],
                ],
            ],

            // ── ADVANCED REGULARIZATION & OPTIMIZATION ────────────────────
            [
                'q' => "Your gradient boosting model achieves 94% train accuracy, 91% CV accuracy, 90% test accuracy.\nA team member suggests adding more trees to improve further.\nWhat is the correct professional response?",
                'opts' => [
                    ['Agree — more trees always improve gradient boosting', false],
                    ['The model is already well-generalized (small train-CV-test gap). Adding more trees risks overfitting; focus on feature engineering or data quality instead', true],
                    ['Disagree — switch to a neural network for the last 1% gain', false],
                    ['Run a t-test to determine if the improvement is statistically significant', false],
                ],
            ],
            [
                'q' => "What is the 'double descent' phenomenon in modern ML?\n\n# Observed: as model complexity increases:\n# Phase 1: test error decreases (classical bias-variance tradeoff)\n# Phase 2: test error spikes at interpolation threshold\n# Phase 3: test error decreases again with very large models",
                'opts' => [
                    ['Overfitting occurs twice during training in large models', false],
                    ['Beyond the interpolation threshold (zero training error), further increasing model size can paradoxically improve generalization — classical bias-variance tradeoff breaks down for overparameterized models', true],
                    ['The model\'s test error mirrors training error in a double sine wave pattern', false],
                    ['Double descent only occurs in neural networks, never in tree models', false],
                ],
            ],
            [
                'q' => "You are tuning a logistic regression with SGD. The loss oscillates wildly and does not converge.\nWhat is the most likely cause and fix?\n\nfrom sklearn.linear_model import SGDClassifier\nmodel = SGDClassifier(loss='log_loss', learning_rate='constant', eta0=10.0)",
                'opts' => [
                    ['The dataset has too many features for SGD', false],
                    ['learning_rate=10.0 is far too high — the gradient steps overshoot the minimum; reduce eta0 significantly or use learning_rate=\'optimal\' for automatic scheduling', true],
                    ['SGDClassifier cannot use log_loss', false],
                    ['The data needs to be shuffled before each epoch', false],
                ],
            ],
            [
                'q' => "What is the purpose of learning rate scheduling in iterative model training?\n\nfrom sklearn.linear_model import SGDClassifier\nmodel = SGDClassifier(learning_rate='invscaling', eta0=0.1, power_t=0.5)",
                'opts' => [
                    ['It increases the learning rate over time to find the global minimum faster', false],
                    ['It reduces the learning rate over time — allowing large steps early for rapid convergence and smaller steps later for fine-grained optimization near the minimum', true],
                    ['It sets a different learning rate for each feature', false],
                    ['It selects the best learning rate automatically using cross-validation', false],
                ],
            ],

            // ── PRODUCTION FAIRNESS & RELIABILITY ────────────────────────
            [
                'q' => "Your loan approval classifier has equal overall accuracy (85%) for Group A and Group B.\nHowever, False Positive Rate for Group A = 5%, Group B = 22%.\nWhat fairness problem exists?",
                'opts' => [
                    ['None — equal accuracy means the model is fair', false],
                    ['Equalized odds violation — Group B is incorrectly denied loans at 4x the rate of Group A, even though overall accuracy is equal; this constitutes disparate impact', true],
                    ['The model is biased because Group B\'s data quality is lower', false],
                    ['This is expected when class proportions differ between groups', false],
                ],
            ],
            [
                'q' => "You apply post-processing to enforce Equal Opportunity (equal TPR across groups) by adjusting per-group decision thresholds.\nWhat is the technical tradeoff?",
                'opts' => [
                    ['No tradeoff — fairness constraints always improve overall accuracy', false],
                    ['Enforcing fairness constraints typically reduces overall model accuracy — there is a fundamental Pareto frontier between accuracy and most fairness definitions', true],
                    ['Equal opportunity only improves accuracy for the disadvantaged group', false],
                    ['Per-group threshold adjustment is never used in production systems', false],
                ],
            ],

            // ── ADVANCED FEATURE ENGINEERING ─────────────────────────────
            [
                'q' => "You are building a churn prediction model with a timestamp feature.\nYou engineer: day_of_week, days_since_last_purchase, rolling_7day_transactions.\nWhich is the most dangerous potential issue?\n\n# days_since_last_purchase is computed relative to prediction date",
                'opts' => [
                    ['Rolling windows are computationally expensive', false],
                    ['If days_since_last_purchase or rolling features use FUTURE data relative to the label date, you have temporal leakage — the model sees information it would not have at real prediction time', true],
                    ['Day of week has too many categories for gradient boosting', false],
                    ['Rolling features cause multicollinearity with the timestamp', false],
                ],
            ],
            [
                'q' => "Target encoding replaces a categorical feature value with the mean target value for that category.\nWhat is the critical implementation requirement in cross-validation?\n\nfrom category_encoders import TargetEncoder",
                'opts' => [
                    ['Target encoding must be done on the test set only', false],
                    ['Target encoding must be fit ONLY on the training fold within each CV split — fitting on the full dataset causes target leakage since validation fold labels influence the encoding', true],
                    ['Target encoding is immune to leakage because it uses means, not raw labels', false],
                    ['Cross-validation should not be used with target encoding', false],
                ],
            ],
            [
                'q' => "What is feature interaction and how does it benefit tree models vs. linear models?\n\n# Example: feature_A × feature_B synergy",
                'opts' => [
                    ['Tree models cannot capture interactions; linear models with interaction terms always win', false],
                    ['Tree models learn feature interactions implicitly through splits — they partition on combinations of features automatically. Linear models need explicit interaction terms (A×B) added as features to capture the same effects', true],
                    ['Both model types handle interactions identically', false],
                    ['Feature interactions are only relevant for image data', false],
                ],
            ],

            // ── ADVANCED ENSEMBLE DESIGN ──────────────────────────────────
            [
                'q' => "In stacking, why must the meta-model be trained on OUT-OF-FOLD predictions from base models rather than their in-sample predictions?\n\n# Base models: RF, GBM, SVM\n# Meta-model: Logistic Regression trained on base model outputs",
                'opts' => [
                    ['Out-of-fold predictions are faster to compute', false],
                    ['In-sample base model predictions are overfit — the meta-model would learn to trust base models\' optimistic training scores. OOF predictions reflect realistic generalization performance', true],
                    ['The meta-model cannot process in-sample predictions of the same size', false],
                    ['This requirement only applies when base models are decision trees', false],
                ],
            ],
            [
                'q' => "CatBoost handles categorical features differently from XGBoost.\nWhat is CatBoost's key innovation for categoricals?\n\nfrom catboost import CatBoostClassifier\nmodel = CatBoostClassifier(cat_features=['city', 'product_type'])",
                'opts' => [
                    ['CatBoost one-hot encodes all categorical features automatically', false],
                    ['CatBoost uses Ordered Target Statistics — computing target encoding using only PREVIOUS samples in a random permutation, preventing target leakage during training', true],
                    ['CatBoost converts categoricals to embeddings using a neural network layer', false],
                    ['CatBoost ignores categorical features and uses only numerical ones', false],
                ],
            ],

            // ── STATISTICAL INFERENCE IN ML ───────────────────────────────
            [
                'q' => "You compare two classifiers:\nModel A: CV accuracy = 85.2% ± 1.8%\nModel B: CV accuracy = 86.1% ± 3.4%\n\nA stakeholder says 'Model B is obviously better — 86.1 > 85.2.'\nWhat is the correct statistical response?",
                'opts' => [
                    ['Agree — the higher mean is all that matters', false],
                    ['The difference (0.9%) is within Model B\'s standard deviation (3.4%) — it may not be statistically significant. Use a paired t-test or Wilcoxon signed-rank test on fold scores to determine significance', true],
                    ['Use the model with lower standard deviation always', false],
                    ['Only the test set score should be used for comparison — CV is unreliable', false],
                ],
            ],
            [
                'q' => "What is the 5×2 CV F-test (Dietterich, 1998) used for?\n\n# Compares two ML models by running 5 repetitions of 2-fold CV",
                'opts' => [
                    ['Computing AUC with 5 different random seeds', false],
                    ['A statistically rigorous method for comparing two models\' performance that correctly accounts for the dependence structure of cross-validation results', true],
                    ['Selecting the best of 5 hyperparameter configurations', false],
                    ['Testing if a model\'s accuracy is significantly above 50%', false],
                ],
            ],

            // ── ADVANCED SVM / KERNEL METHODS ─────────────────────────────
            [
                'q' => "What is the 'kernel trick' mathematically, and why does it make non-linear SVM tractable?\n\nK(xᵢ, xⱼ) = φ(xᵢ)ᵀφ(xⱼ)",
                'opts' => [
                    ['It replaces the input features with their PCA projections', false],
                    ['The kernel function computes the inner product in the transformed feature space WITHOUT explicitly computing the transformation φ — making high/infinite-dimensional feature spaces computationally feasible', true],
                    ['It applies feature normalization to speed up distance computations', false],
                    ['It reduces the training data to only support vectors before applying φ', false],
                ],
            ],
            [
                'q' => "You have 50,000 training samples and are comparing:\nA: SVC(kernel='rbf') — exact kernel SVM\nB: Pipeline([RBFSampler(n_components=500), LinearSVC()])\n\nFor production constraints (training time < 10 min), which is preferable?",
                'opts' => [
                    ['A — exact SVM always outperforms the approximation', false],
                    ['B — RBFSampler approximates the RBF kernel mapping at O(n) training cost vs. O(n²–n³) for exact SVM; acceptable for large datasets with tolerable approximation error', true],
                    ['Both have the same training complexity', false],
                    ['Neither — use a neural network for large datasets', false],
                ],
            ],

            // ── PRODUCTION TRAINING PIPELINES ────────────────────────────
            [
                'q' => "You need to retrain a production model daily on a growing dataset (currently 50M rows, growing 200K/day).\nFull retraining takes 8 hours — exceeding the 6-hour window.\nWhat is the best solution?",
                'opts' => [
                    ['Increase compute resources indefinitely to keep full retraining', false],
                    ['Implement incremental/warm-start training: retrain on recent data and fine-tune the existing model, OR use reservoir sampling to maintain a representative fixed-size training window', true],
                    ['Retrain weekly instead of daily', false],
                    ['Reduce the model to logistic regression — it trains in minutes', false],
                ],
            ],
            [
                'q' => "What is 'warm starting' in the context of gradient boosting (XGBoost/LightGBM)?",
                'opts' => [
                    ['Initializing model weights randomly from a warm (high-variance) distribution', false],
                    ['Continuing training from a previously saved checkpoint — adding more trees to an existing model on new data without discarding prior learning', true],
                    ['Pre-warming the CPU/GPU cache before training begins', false],
                    ['Starting training with high learning rate that decreases to zero', false],
                ],
            ],
            [
                'q' => "What does this MLflow tracking code accomplish in a production ML pipeline?\n\nimport mlflow\nwith mlflow.start_run():\n    mlflow.log_params({'n_estimators': 200, 'max_depth': 5})\n    mlflow.log_metric('val_auc', 0.934)\n    mlflow.sklearn.log_model(model, 'model')",
                'opts' => [
                    ['It deploys the model to a REST API endpoint', false],
                    ['It tracks experiment parameters, metrics, and the serialized model artifact for reproducibility, comparison, and model registry promotion', true],
                    ['It calculates feature importance and logs it as a metric', false],
                    ['It automatically retrains the model when val_auc drops below a threshold', false],
                ],
            ],

            // ── ADVANCED EVALUATION EDGE CASES ────────────────────────────
            [
                'q' => "Your binary classifier achieves AUC-ROC = 0.97.\nA colleague is excited, but you notice P(y=1) = 0.001 in production.\nWhat critical additional evaluation do you perform?",
                'opts' => [
                    ['Increase the dataset size to make AUC more reliable', false],
                    ['Evaluate AUC-PR (Precision-Recall AUC) — with 0.1% positive rate, ROC AUC can be misleadingly high even for poor models; PR-AUC reflects true precision at operational recall levels', true],
                    ['AUC-ROC = 0.97 is reliable regardless of class imbalance', false],
                    ['Compute accuracy instead — it is more robust to imbalance', false],
                ],
            ],
            [
                'q' => "Evaluating a regression model:\nTest set RMSE = 42.3\nTraining set RMSE = 1.8\n\nA second model gives:\nTest set RMSE = 38.1\nTraining set RMSE = 35.9\n\nWhich model do you prefer for production and why?",
                'opts' => [
                    ['Model 1 — lower training RMSE proves it learned the data better', false],
                    ['Model 2 — similar train and test RMSE indicates good generalization; Model 1\'s 20x gap between train/test RMSE signals severe overfitting', true],
                    ['Model 1 — test RMSE is only slightly higher than Model 2', false],
                    ['You need more metrics before choosing — RMSE alone is insufficient', false],
                ],
            ],
            [
                'q' => "What is the multiple comparisons problem when benchmarking 20 ML models, and how should you report results?\n\n# All models evaluated on the same test set",
                'opts' => [
                    ['With 20 models, at least one will achieve high accuracy by chance alone — apply Bonferroni/BH correction, use held-out validation sets, and report confidence intervals', true],
                    ['The problem is computational — 20 models take too long to evaluate', false],
                    ['Multiple comparisons only matter in hypothesis testing, not ML benchmarks', false],
                    ['Pick the model with the highest absolute metric — no correction needed', false],
                ],
            ],

            // ── DEEP TECHNICAL REASONING ──────────────────────────────────
            [
                'q' => "Why does gradient boosting with very small learning_rate (e.g., 0.001) and high n_estimators often outperform large learning_rate with few trees?\n\nmodel = GradientBoostingClassifier(learning_rate=0.001, n_estimators=10000)",
                'opts' => [
                    ['Smaller learning rate always produces more accurate individual trees', false],
                    ['Shrinkage regularization: small steps allow each tree to correct errors incrementally without overshooting, while many trees accumulate sufficient total correction — trading computation for generalization', true],
                    ['High n_estimators compensates for poor tree quality at low learning rates', false],
                    ['This configuration prevents convergence and produces random results', false],
                ],
            ],
            [
                'q' => "What is 'Isotonic Regression' and when is it used in ML pipelines?\n\nfrom sklearn.isotonic import IsotonicRegression\nfrom sklearn.calibration import CalibratedClassifierCV\ncal = CalibratedClassifierCV(model, method='isotonic')",
                'opts' => [
                    ['A regression algorithm that enforces monotonic predictions on ordered data', false],
                    ['A non-parametric calibration method that fits a non-decreasing step function to map uncalibrated scores to well-calibrated probabilities — preferred over Platt scaling with large datasets', true],
                    ['A regularization technique that prevents negative coefficients', false],
                    ['A method for handling ordinal categorical targets in regression', false],
                ],
            ],
            [
                'q' => "What is the Vapnik-Chervonenkis (VC) dimension and why does it matter for supervised learning guarantees?",
                'opts' => [
                    ['The maximum depth of a decision tree that can be learned efficiently', false],
                    ['A measure of a model class\'s capacity — the size of the largest dataset it can perfectly classify in all possible labelings; higher VC dim enables more complex functions but requires more data to generalize', true],
                    ['The number of support vectors in an optimal SVM solution', false],
                    ['The minimum sample size required for a 95% confidence test', false],
                ],
            ],
            [
                'q' => "PAC learning guarantees state that with probability ≥ 1-δ, a learned hypothesis has error ≤ ε, requiring:\nn ≥ (1/ε) × (ln|H| + ln(1/δ))\n\nWhat does this tell a practitioner?",
                'opts' => [
                    ['Models always converge given enough iterations', false],
                    ['Sample complexity grows logarithmically with the hypothesis class size and inversely with the desired error tolerance — quantifying how much data is needed for reliable learning guarantees', true],
                    ['You need exponentially more data as accuracy requirements tighten', false],
                    ['PAC bounds are only valid for linear models with VC dim = d', false],
                ],
            ],
            [
                'q' => "You receive this production alert:\n'Model prediction latency p99 = 2.3 seconds, up from 180ms baseline. Throughput dropped 40%.'\n\nYour model is a gradient boosted tree (GBM) serving via REST API.\nWhat is the FIRST investigation step?",
                'opts' => [
                    ['Retrain the model — high latency means the model needs updating', false],
                    ['Check if input feature computation or preprocessing (not the model itself) changed — GBMs predict in microseconds; the bottleneck is almost certainly upstream (feature store latency, serialization, network, or a runaway feature computation)', true],
                    ['Reduce n_estimators to speed up inference', false],
                    ['Switch to a simpler model like logistic regression', false],
                ],
            ],
            [
                'q' => "Your L1-regularized (Lasso) logistic regression for a 500-feature dataset zeroes out 480 features.\nA stakeholder is concerned: 'We\'re losing 96% of our features!'\nWhat is the professional explanation and validation step?",
                'opts' => [
                    ['The model is broken — L1 should not eliminate that many features', false],
                    ['Lasso performs embedded feature selection — the 480 zeroed features had insufficient signal given the regularization strength. Validate by checking if removing them causes meaningful CV performance drop; if not, the sparse model is correct', true],
                    ['Increase alpha to allow more features to be retained', false],
                    ['Lasso should not be used with more than 100 features', false],
                ],
            ],
            [
                'q' => "What does this conformal prediction code provide that standard classification does not?\n\nfrom nonconformist import IcpClassifier, ClassifierNc\nicp = IcpClassifier(ClassifierNc(RandomForestClassifier()))\nicp.fit(X_train, y_train)\nicp.calibrate(X_cal, y_cal)\nprediction_set = icp.predict(X_test, significance=0.1)",
                'opts' => [
                    ['Higher accuracy than standard classification on the same data', false],
                    ['Prediction SETS with a formal coverage guarantee — the true label is in the predicted set with probability ≥ 90% (1 - significance), providing rigorous uncertainty quantification', true],
                    ['Faster inference by parallelizing the random forest prediction', false],
                    ['An ensemble of conformal predictors to reduce variance', false],
                ],
            ],
            [
                'q' => "In a production ML system, you apply three interventions to a struggling model:\n1. Collected 5x more recent training data\n2. Added 10 new engineered features\n3. Switched from Random Forest to XGBoost\n\nTest AUC improved from 0.71 to 0.88. A PM asks 'What caused the improvement?'\nWhat is the correct response?",
                'opts' => [
                    ['XGBoost is always better than Random Forest — that was the fix', false],
                    ['You cannot isolate the cause — changes were applied simultaneously; proper attribution requires ablation studies (testing each change independently) to quantify individual contributions', true],
                    ['More data always explains the majority of improvements', false],
                    ['Feature engineering always contributes more than algorithm choice', false],
                ],
            ],
            [
                'q' => "You are deploying a high-stakes medical diagnostic model.\nRegulators require the model to be 'explainable by design.'\nWhich approach satisfies this while maintaining competitive performance?",
                'opts' => [
                    ['Deploy SHAP post-hoc explanations on top of a black-box model — this satisfies regulators', false],
                    ['Use an inherently interpretable model (GAM, explainable boosting machine, or a regularized linear model) with documented decision logic — post-hoc explanations are insufficient for high-stakes regulated domains', true],
                    ['Deep neural networks with attention maps are always explainable enough', false],
                    ['Any model with >90% accuracy is implicitly acceptable to regulators', false],
                ],
            ],
            [
                'q' => "What is the 'train-serve skew' problem and how do you detect it?\n\n# Training: feature computed from a database batch export\n# Serving: same feature computed from a real-time API call",
                'opts' => [
                    ['A performance difference between GPU training and CPU serving', false],
                    ['Discrepancies between how features are computed during training vs. inference — small logic differences (e.g., NULL handling, rounding, timezone) cause the model to see different distributions at serve time; detected by logging and comparing feature values from both pipelines', true],
                    ['The model\'s accuracy drop that occurs naturally after deployment', false],
                    ['A version mismatch between sklearn and the production environment', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 14 — Supervised Learning (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}