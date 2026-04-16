<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module18ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 18 — Privacy, Ethics & Data Governance (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Privacy, Ethics & Data Governance',
            'description'           => 'Analyze code-level privacy implementations, debug biased pipelines, interpret fairness trade-offs quantitatively, and reason through complex governance and legal edge cases.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1100,
            'order_index'           => 18,
        ]);

        $this->command->info("Seeding Advanced ethics & privacy questions...");

        $qaData = [

            // ── DIFFERENTIAL PRIVACY — ADVANCED CODE ──────────────────────
            [
                'q' => "```python\nimport numpy as np\n\ndef private_mean(data, epsilon, lower, upper):\n    n = len(data)\n    clipped = np.clip(data, lower, upper)\n    true_mean = np.mean(clipped)\n    sensitivity = (upper - lower) / n\n    noise = np.random.laplace(0, sensitivity / epsilon)\n    return true_mean + noise\n\nresult = private_mean(ages, epsilon=1.0, lower=18, upper=90)\n```\n\nWhy is the data clipped to [lower, upper] before computing the mean?",
                'opts' => [
                    ['To remove outliers for better model accuracy', false],
                    ['To bound the sensitivity — without clipping, a single extreme value could change the mean drastically, requiring much larger noise', true],
                    ['To normalize the data to [0, 1]', false],
                    ['To satisfy GDPR storage limitations', false],
                ],
            ],
            [
                'q' => "```python\nclass PrivacyBudgetTracker:\n    def __init__(self, total_epsilon):\n        self.remaining = total_epsilon\n\n    def consume(self, epsilon):\n        if epsilon > self.remaining:\n            raise PrivacyBudgetExhausted()\n        self.remaining -= epsilon\n        return epsilon\n\ntracker = PrivacyBudgetTracker(total_epsilon=1.0)\ntracker.consume(0.3)  # Query 1\ntracker.consume(0.3)  # Query 2\ntracker.consume(0.3)  # Query 3\ntracker.consume(0.3)  # Query 4 — what happens?\n```\n\nQuery 4 attempts to consume 0.3 but only 0.1 remains. The correct behavior is:",
                'opts' => [
                    ['Allow the query with the remaining 0.1 budget', false],
                    ['Raise a PrivacyBudgetExhausted exception — the privacy guarantee would be violated if allowed', true],
                    ['Reset the budget automatically', false],
                    ['Add more noise to compensate for the overflow', false],
                ],
            ],
            [
                'q' => "```python\nfrom diffprivlib.models import GaussianNB\n\nclf = GaussianNB(epsilon=0.5, bounds=([0]*n_features, [1]*n_features))\nclf.fit(X_train, y_train)\n```\n\nThe `bounds` parameter in diffprivlib's GaussianNB is required because:",
                'opts' => [
                    ['GaussianNB does not support unbounded features', false],
                    ['Differential privacy mechanisms require bounded sensitivity — the bounds define the range within which data values are clipped to compute sensitivity', true],
                    ['It is needed to normalize the training data automatically', false],
                    ['Bounds control how many trees are built in the model', false],
                ],
            ],
            [
                'q' => "The Gaussian mechanism for differential privacy satisfies (ε, δ)-DP instead of pure ε-DP.\n\nThe δ parameter represents:",
                'opts' => [
                    ['The amount of Gaussian noise added', false],
                    ['A small probability that the privacy guarantee fails — (ε, δ)-DP allows a δ chance of stronger information leakage', true],
                    ['The sensitivity of the query', false],
                    ['The number of queries allowed', false],
                ],
            ],

            // ── FAIRNESS — ADVANCED CODE & ANALYSIS ───────────────────────
            [
                'q' => "```python\nfrom fairlearn.metrics import demographic_parity_difference\nfrom fairlearn.reductions import ExponentiatedGradient, DemographicParity\n\nconstraint = DemographicParity()\nmitigator = ExponentiatedGradient(estimator=LogisticRegression(),\n                                   constraints=constraint)\nmitigator.fit(X_train, y_train, sensitive_features=A_train)\ny_pred = mitigator.predict(X_test)\n\ndpd = demographic_parity_difference(y_test, y_pred,\n                                    sensitive_features=A_test)\nprint(dpd)  # 0.023\n```\n\nA demographic_parity_difference of 0.023 means:",
                'opts' => [
                    ['The model has 2.3% overall error', false],
                    ['The selection rate differs by 2.3 percentage points between the most and least favored groups', true],
                    ['The model was trained for 0.023 epochs', false],
                    ['23% of predictions are incorrect', false],
                ],
            ],
            [
                'q' => "```python\nfrom fairlearn.metrics import (\n    equalized_odds_difference,\n    false_positive_rate,\n    false_negative_rate\n)\n\nmetrics = {\n    'fpr': false_positive_rate,\n    'fnr': false_negative_rate\n}\n\nresults = MetricFrame(metrics=metrics,\n                      y_true=y_test,\n                      y_pred=y_pred,\n                      sensitive_features=A_test)\nprint(results.by_group)\n# Group A: fpr=0.05, fnr=0.10\n# Group B: fpr=0.18, fnr=0.25\n```\n\nThe equalized_odds_difference will be:",
                'opts' => [
                    ['0 — both groups have non-zero FPR and FNR', false],
                    ['max(|0.18-0.05|, |0.25-0.10|) = max(0.13, 0.15) = 0.15', true],
                    ['(0.05+0.10)/2 = 0.075', false],
                    ['Cannot be computed from FPR and FNR alone', false],
                ],
            ],
            [
                'q' => "```python\n# Threshold optimizer for equal opportunity\nfrom fairlearn.postprocessing import ThresholdOptimizer\n\noptimizer = ThresholdOptimizer(\n    estimator=base_model,\n    constraints='equalized_odds',\n    objective='balanced_accuracy_score'\n)\noptimizer.fit(X_train, y_train, sensitive_features=A_train)\n```\n\nThis approach applies DIFFERENT decision thresholds per demographic group.\nA criticism of this approach from a legal perspective in some jurisdictions is:",
                'opts' => [
                    ['It makes the model less accurate', false],
                    ['Explicitly using different thresholds per protected group may itself constitute illegal disparate treatment under anti-discrimination law', true],
                    ['It requires too much computation', false],
                    ['ThresholdOptimizer cannot handle binary classification', false],
                ],
            ],
            [
                'q' => "You compute the following for a recidivism prediction model:\n\n  White defendants:  FPR = 0.10,  FNR = 0.28\n  Black defendants:  FPR = 0.25,  FNR = 0.12\n\nThis pattern (higher FPR for one group, higher FNR for the other) indicates:",
                'opts' => [
                    ['The model is perfectly calibrated', false],
                    ['The model over-predicts recidivism for Black defendants (more false alarms) and under-predicts for White defendants — a well-documented pattern in criminal justice AI', true],
                    ['The model satisfies equalized odds', false],
                    ['The training data was too small', false],
                ],
            ],

            // ── PRIVACY-ENHANCING TECH — ADVANCED ────────────────────────
            [
                'q' => "```python\n# Federated learning aggregation (FedAvg)\ndef federated_average(client_weights, client_sizes):\n    total = sum(client_sizes)\n    averaged = []\n    for layer_idx in range(len(client_weights[0])):\n        layer_avg = sum(\n            client_weights[i][layer_idx] * client_sizes[i] / total\n            for i in range(len(client_weights))\n        )\n        averaged.append(layer_avg)\n    return averaged\n```\n\nFedAvg weights each client's contribution by their dataset size. A privacy concern is:",
                'opts' => [
                    ['Large clients are penalized for having more data', false],
                    ['A large malicious client can disproportionately influence the global model (data poisoning attack)', true],
                    ['Small clients cannot participate in federated learning', false],
                    ['The aggregation reveals raw training data to the server', false],
                ],
            ],
            [
                'q' => "```python\n# Secure Aggregation pseudocode:\n# 1. Each client i generates random masks: rᵢⱼ for each pair (i,j)\n# 2. Client i sends: gradient_i + Σⱼ rᵢⱼ - Σⱼ rⱼᵢ\n# 3. Server sums all masked gradients — masks cancel out\n# Result: server learns only the SUM of gradients, not individual ones\n```\n\nSecure Aggregation protects against:",
                'opts' => [
                    ['The server inspecting individual clients\' gradient updates to reconstruct their private data', true],
                    ['Malicious clients sending corrupted gradients', false],
                    ['Model accuracy degradation from noise', false],
                    ['Data poisoning from large clients', false],
                ],
            ],
            [
                'q' => "```python\nimport tenseal as ts\n\ncontext = ts.context(ts.SCHEME_TYPE.CKKS,\n                     poly_modulus_degree=8192,\n                     coeff_mod_bit_sizes=[60, 40, 40, 60])\ncontext.generate_galois_keys()\n\nenc_data = ts.ckks_vector(context, plaintext_data)\nenc_result = enc_data.dot(enc_weights) + enc_bias\n```\n\nThis code performs inference using CKKS homomorphic encryption. The key property is:",
                'opts' => [
                    ['The model weights are hidden from the server', false],
                    ['The inference computation runs on encrypted data — the server never sees the plaintext input', true],
                    ['The result is automatically differentially private', false],
                    ['It encrypts only the output, not the computation', false],
                ],
            ],

            // ── DATA GOVERNANCE — ADVANCED ────────────────────────────────
            [
                'q' => "```python\n# Data lineage tracking with Apache Atlas (pseudocode)\nprocess = Process(\n    name='customer_aggregation',\n    inputs=[dataset_raw_customers],\n    outputs=[dataset_aggregated_customers],\n    user='analyst_01',\n    timestamp=datetime.now()\n)\natlas_client.create_entity(process)\n```\n\nThis lineage record answers which governance question most directly?",
                'opts' => [
                    ['Is this dataset accurate and complete?', false],
                    ['Where did this derived dataset come from, who created it, and when?', true],
                    ['Who is allowed to access this dataset?', false],
                    ['When will this dataset be deleted?', false],
                ],
            ],
            [
                'q' => "```python\n# Great Expectations data quality check\nimport great_expectations as ge\n\ndf_ge = ge.from_pandas(df)\nresults = df_ge.expect_column_values_to_not_be_null('customer_id')\nresults2 = df_ge.expect_column_values_to_be_between(\n    'age', min_value=18, max_value=120)\nresults3 = df_ge.expect_column_values_to_be_unique('customer_id')\n```\n\nThese three checks together validate which data quality dimensions?",
                'opts' => [
                    ['Timeliness, accuracy, and consistency', false],
                    ['Completeness (no nulls), validity (age range), and uniqueness (no duplicate IDs)', true],
                    ['Accuracy, lineage, and retention', false],
                    ['Availability, security, and compliance', false],
                ],
            ],
            [
                'q' => "A data mesh architecture distributes data ownership to domain teams instead of centralizing it.\n\nThe primary data governance challenge this introduces is:",
                'opts' => [
                    ['Domain teams cannot store data locally', false],
                    ['Maintaining consistent standards, definitions, and quality across independently owned domains without a central enforcer', true],
                    ['Data mesh increases storage costs exponentially', false],
                    ['Domain teams cannot use SQL', false],
                ],
            ],
            [
                'q' => "```sql\n-- Role-based access control implementation\nGRANT SELECT ON schema.customer_pii TO role_analyst;\nREVOKE SELECT ON schema.customer_pii FROM role_analyst;\nGRANT SELECT ON schema.customer_anonymized TO role_analyst;\n```\n\nThis sequence implements which data governance principle?",
                'opts' => [
                    ['Data retention management', false],
                    ['Least-privilege access — analysts can only view anonymized data, not PII', true],
                    ['Data lineage tracking', false],
                    ['K-anonymity enforcement at the database level', false],
                ],
            ],

            // ── RESPONSIBLE AI — ADVANCED ──────────────────────────────────
            [
                'q' => "```python\nimport shap\n\nexplainer = shap.Explainer(model, X_train)\nshap_values = explainer(X_test)\n\n# A specific prediction for individual i:\nprint(shap_values[i].base_values)  # 0.35 (base rate)\nprint(shap_values[i].values)\n# feature 'income':    +0.22\n# feature 'zip_code': -0.18\n# feature 'age':      +0.05\n# Prediction:          0.44\n```\n\nThe feature 'zip_code' has SHAP value −0.18 for this individual. In a credit approval model, this likely indicates:",
                'opts' => [
                    ['The model rewarded the applicant for their ZIP code', false],
                    ['The applicant\'s ZIP code decreased their approval probability by 0.18 — potentially a proxy for race/ethnicity, raising a redlining concern', true],
                    ['The model cannot use geographic data', false],
                    ['ZIP code is the least important feature', false],
                ],
            ],
            [
                'q' => "```python\nfrom alibi.explainers import AnchorTabular\n\nexplainer = AnchorTabular(predict_fn, feature_names)\nexplainer.fit(X_train)\n\nexplanation = explainer.explain(X_test[0])\nprint(explanation.anchor)\n# Output: ['income > 50000', 'credit_score > 700']\nprint(explanation.precision)  # 0.95\n```\n\nAn anchor explanation with precision=0.95 means:",
                'opts' => [
                    ['The model is 95% accurate on the test set', false],
                    ['For 95% of inputs where these anchor rules hold, the model gives the same prediction', true],
                    ['95% of features are covered by the anchor', false],
                    ['The anchor rules apply to 95% of the training data', false],
                ],
            ],
            [
                'q' => "Model cards (Mitchell et al. 2019) include an 'intended use' section that specifies:\n\n  Primary uses: ___\n  Out-of-scope uses: ___\n\nA sentiment analysis model trained on product reviews lists 'clinical depression detection' as out-of-scope.\n\nDeploying it for clinical depression screening would be problematic because:",
                'opts' => [
                    ['It would run too slowly', false],
                    ['The training distribution (product reviews) differs drastically from clinical text — performance is unknown and potentially harmful in a high-stakes medical context', true],
                    ['Sentiment models cannot handle medical terminology', false],
                    ['The model card is not legally binding', false],
                ],
            ],

            // ── ALGORITHMIC BIAS — ADVANCED ───────────────────────────────
            [
                'q' => "```python\nfrom aif360.datasets import BinaryLabelDataset\nfrom aif360.metrics import BinaryLabelDatasetMetric\n\nmetric = BinaryLabelDatasetMetric(\n    dataset,\n    unprivileged_groups=[{'race': 0}],\n    privileged_groups=[{'race': 1}]\n)\n\nprint(metric.disparate_impact())  # 0.58\nprint(metric.mean_difference())  # -0.21\n```\n\nA disparate impact of 0.58 means:",
                'opts' => [
                    ['58% of predictions are correct', false],
                    ['The unprivileged group receives the positive outcome only 58% as often as the privileged group — well below the 0.8 threshold', true],
                    ['The dataset has 58% minority group representation', false],
                    ['The model satisfies demographic parity at 58%', false],
                ],
            ],
            [
                'q' => "```python\nfrom aif360.algorithms.preprocessing import Reweighing\n\nRW = Reweighing(unprivileged_groups=[{'gender': 0}],\n                privileged_groups=[{'gender': 1}])\nRW.fit(train_dataset)\ntransformed = RW.transform(train_dataset)\n\n# After reweighing:\nprint(transformed.instance_weights[:5])\n# [1.43, 1.43, 0.71, 0.71, 1.43]\n```\n\nWeights > 1 are assigned to instances from the unprivileged group. This technique:",
                'opts' => [
                    ['Removes the unprivileged group from training', false],
                    ['Increases the influence of underrepresented/disadvantaged group samples during training to reduce bias', true],
                    ['Adds synthetic samples for the unprivileged group', false],
                    ['Applies post-hoc threshold adjustment', false],
                ],
            ],
            [
                'q' => "```python\nfrom aif360.algorithms.inprocessing import AdversarialDebiasing\nimport tensorflow as tf\n\ndebiased_model = AdversarialDebiasing(\n    privileged_groups=[{'sex': 1}],\n    unprivileged_groups=[{'sex': 0}],\n    scope_name='debiased_classifier',\n    sess=tf.compat.v1.Session()\n)\ndebiased_model.fit(dataset_train)\n```\n\nAdversarial debiasing trains two networks simultaneously:\n  1. A classifier that predicts the target label\n  2. An adversary that tries to predict the protected attribute from the classifier's output\n\nThe classifier is trained to:",
                'opts' => [
                    ['Maximize the adversary\'s ability to predict the protected attribute', false],
                    ['Simultaneously maximize prediction accuracy AND minimize the adversary\'s ability to predict the protected attribute', true],
                    ['Only minimize the adversary loss', false],
                    ['Encode the protected attribute into the prediction', false],
                ],
            ],

            // ── LEGAL & COMPLIANCE — ADVANCED ─────────────────────────────
            [
                'q' => "An EU company uses an automated AI system to make fully automated credit decisions with no human review.\n\nUnder GDPR Article 22, individuals have the right to:",
                'opts' => [
                    ['Have the AI explain itself in technical detail', false],
                    ['Not be subject to solely automated decisions that significantly affect them, and to request human review', true],
                    ['Receive a copy of the AI model weights', false],
                    ['Opt out of credit scoring entirely', false],
                ],
            ],
            [
                'q' => "A GDPR 'legitimate interest' legal basis for processing requires a three-part test:\n  1. Purpose test: is there a legitimate interest?\n  2. Necessity test: is processing necessary for that interest?\n  3. Balancing test: does the individual's interest override the legitimate interest?\n\nA company wants to use customer data for direct marketing under 'legitimate interest'.\nThe balancing test is most likely to FAIL if:",
                'opts' => [
                    ['The marketing is for a product the customer already bought', false],
                    ['The customer has previously opted out of marketing communications, indicating their interest clearly overrides the company\'s', true],
                    ['The company has a privacy policy on its website', false],
                    ['The data was collected with consent for a different purpose', false],
                ],
            ],
            [
                'q' => "Under the EU AI Act, 'unacceptable risk' AI systems are BANNED outright.\n\nWhich of the following falls into the unacceptable risk category?",
                'opts' => [
                    ['AI used in CV screening for job applications', false],
                    ['AI-powered chatbot for customer service', false],
                    ['Real-time biometric surveillance of people in public spaces by law enforcement (with limited exceptions)', true],
                    ['AI that recommends movies on a streaming platform', false],
                ],
            ],

            // ── ETHICAL PRACTICE — ADVANCED ───────────────────────────────
            [
                'q' => "```python\n# Evaluating model fairness BEFORE deployment\nfrom fairlearn.metrics import MetricFrame\nimport pandas as pd\n\nmetric_frame = MetricFrame(\n    metrics={'accuracy': accuracy_score,\n             'selection_rate': selection_rate,\n             'false_negative_rate': false_negative_rate},\n    y_true=y_test,\n    y_pred=y_pred,\n    sensitive_features=protected_attr\n)\n\nprint(metric_frame.by_group)\nprint(metric_frame.difference())  # max - min per metric\n```\n\nThe `difference()` method returns the gap between the best and worst performing group per metric. A deployment gate might require:",
                'opts' => [
                    ['difference() == 0 for all metrics', false],
                    ['difference() to be below an organization-defined threshold (e.g., < 0.05) for all fairness-critical metrics', true],
                    ['difference() to be maximized to show diversity', false],
                    ['difference() to equal the overall accuracy', false],
                ],
            ],
            [
                'q' => "A data scientist discovers that their company's production model exhibits significant gender bias. They raise it with management, who instructs them to keep it quiet for competitive reasons.\n\nEthically, the data scientist should:",
                'opts' => [
                    ['Follow management\'s instructions — company loyalty is paramount', false],
                    ['Immediately seek escalation paths: ethics board, legal/compliance team, or if systemic harm is occurring, relevant regulatory authorities — professional codes of ethics prioritize public harm prevention', true],
                    ['Fix the bias secretly without informing anyone', false],
                    ['Do nothing — the model will be retrained eventually', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 18 — Privacy, Ethics & Data Governance (Advanced).");
    }
}