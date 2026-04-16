<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module18ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 18 — Privacy, Ethics & Data Governance (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Privacy, Ethics & Data Governance',
            'description'           => 'Work through multi-step privacy calculations, fairness metric computations, bias mitigation logic, and governance scenario analysis. Some code snippets included.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 900,
            'order_index'           => 18,
        ]);

        $this->command->info("Seeding Intermediate ethics & privacy questions...");

        $qaData = [

            // ── K-ANONYMITY CALCULATIONS ──────────────────────────────────
            [
                'q' => "A dataset has these records with quasi-identifiers (Age, ZIP, Gender):\n\n  Row 1: 28, 90210, M\n  Row 2: 28, 90210, M\n  Row 3: 35, 10001, F\n  Row 4: 35, 10001, F\n  Row 5: 41, 30301, M\n\nWhat is the k-anonymity value of this dataset?",
                'opts' => [
                    ['k = 1 (Row 5 is unique)', true],
                    ['k = 2', false],
                    ['k = 3', false],
                    ['k = 5', false],
                ],
            ],
            [
                'q' => "To achieve k=2 anonymity for the dataset above, which transformation is most appropriate for Row 5 (Age=41, ZIP=30301, Gender=M)?",
                'opts' => [
                    ['Delete Row 5 entirely', false],
                    ['Generalize Age to a range (e.g., 40-49) and ZIP to a prefix (e.g., 303**) to merge with another similar record', true],
                    ['Replace Age with 0 and ZIP with NULL', false],
                    ['Add a duplicate of Row 5', false],
                ],
            ],
            [
                'q' => "A dataset achieves k=5 anonymity but all 5 members of one equivalence class have the same sensitive attribute value (e.g., all have Disease=Cancer).\n\nThis dataset is vulnerable to which attack?",
                'opts' => [
                    ['Re-identification via external database', false],
                    ['Homogeneity attack — knowing someone is in the group reveals their sensitive attribute', true],
                    ['Skewness attack from unbalanced classes', false],
                    ['Differential privacy budget exhaustion', false],
                ],
            ],
            [
                'q' => "T-closeness requires that the distribution of a sensitive attribute in each equivalence class is close to its distribution in the whole table.\n\nIf the overall Disease distribution is: 60% healthy, 30% diabetes, 10% cancer — a t-close group must have a distribution within t=0.2 of this.\n\nA group with 50% healthy, 40% diabetes, 10% cancer satisfies t-closeness because:",
                'opts' => [
                    ['All three values are non-zero', false],
                    ['The maximum difference in any category (40%-30%=10%) is within 0.2 total variation distance', true],
                    ['The group has more than 2 members', false],
                    ['T-closeness only checks the sensitive attribute once', false],
                ],
            ],

            // ── DIFFERENTIAL PRIVACY CALCULATIONS ────────────────────────
            [
                'q' => "The Laplace mechanism adds noise drawn from Laplace(0, Δf/ε) to a query result.\n\nFor a query with sensitivity Δf = 10 and privacy budget ε = 0.5:\n\nNoise scale = ?",
                'opts' => [
                    ['5', false],
                    ['20', true],
                    ['0.05', false],
                    ['10', false],
                ],
            ],
            [
                'q' => "With ε = 2 and Δf = 6, the Laplace noise scale is 3.\nThe true query result is 150. The noisy output is 150 + Lap(0, 3).\n\nIf the noise drawn is +4.2, the reported value is 154.2.\n\nIncreasing ε from 2 to 10 (same Δf=6) changes the noise scale to:",
                'opts' => [
                    ['0.6', true],
                    ['60', false],
                    ['16', false],
                    ['3', false],
                ],
            ],
            [
                'q' => "Sequential composition in differential privacy states:\nIf you run k queries with budgets ε₁, ε₂, ..., εₖ on the SAME dataset, the total privacy cost is:\n\n  ε_total = ε₁ + ε₂ + ... + εₖ\n\nIf a data analyst runs 5 queries each with ε = 0.1, the total budget consumed is:",
                'opts' => [
                    ['0.1', false],
                    ['0.5', true],
                    ['0.02', false],
                    ['1.0', false],
                ],
            ],
            [
                'q' => "Parallel composition states: if queries are run on DISJOINT subsets of the data, the total budget is:\n\n  ε_total = max(ε₁, ε₂, ..., εₖ)\n\nThree queries on disjoint subsets with budgets ε = 0.3, 0.5, 0.4.\nTotal privacy budget consumed:",
                'opts' => [
                    ['1.2', false],
                    ['0.3', false],
                    ['0.5', true],
                    ['0.4', false],
                ],
            ],

            // ── FAIRNESS METRIC CALCULATIONS ──────────────────────────────
            [
                'q' => "A hiring model results:\n  Group A: 200 applicants, 160 selected → selection rate = 80%\n  Group B: 200 applicants, 100 selected → selection rate = 50%\n\n80% rule: Group B rate / Group A rate = 50/80 = 0.625\n\nConclusion:",
                'opts' => [
                    ['No disparate impact — ratio is above 0.8', false],
                    ['Disparate impact against Group B — ratio 0.625 < 0.8', true],
                    ['The model is perfectly fair', false],
                    ['Disparate impact against Group A', false],
                ],
            ],
            [
                'q' => "Confusion matrices for a fraud detection model:\n\n  Group A: TP=90, FP=10, FN=10, TN=390\n  Group B: TP=60, FP=40, FN=40, TN=360\n\nTrue Positive Rate (TPR) = TP / (TP + FN)\n\nTPR for Group A = ?\nTPR for Group B = ?",
                'opts' => [
                    ['Group A: 0.90, Group B: 0.60', true],
                    ['Group A: 0.75, Group B: 0.50', false],
                    ['Group A: 0.90, Group B: 0.50', false],
                    ['Group A: 0.80, Group B: 0.60', false],
                ],
            ],
            [
                'q' => "Using the same confusion matrices:\n  Group A: FPR = FP/(FP+TN) = 10/400 = 0.025\n  Group B: FPR = 40/400 = 0.100\n\nThe model violates equalized odds because:",
                'opts' => [
                    ['Both TPR and FPR are equal across groups', false],
                    ['TPR differs (0.90 vs 0.60) AND FPR differs (0.025 vs 0.10) across groups', true],
                    ['Only FPR differs, not TPR', false],
                    ['The model is calibrated so equalized odds is satisfied', false],
                ],
            ],
            [
                'q' => "A model predicts loan default probability. Among those predicted to have 70% default risk:\n  Group A: 72% actually defaulted\n  Group B: 68% actually defaulted\n\nThis model is approximately:",
                'opts' => [
                    ['Calibrated — predicted probabilities match actual outcomes similarly across groups', true],
                    ['Biased against Group A', false],
                    ['Violating demographic parity', false],
                    ['Violating equalized odds', false],
                ],
            ],

            // ── BIAS MITIGATION STRATEGIES ────────────────────────────────
            [
                'q' => "Pre-processing bias mitigation techniques modify the training data BEFORE model training. Which of the following is a pre-processing technique?",
                'opts' => [
                    ['Adversarial debiasing during training', false],
                    ['Reweighting training samples so underrepresented groups have higher influence', true],
                    ['Post-hoc threshold adjustment per demographic group', false],
                    ['Adding a fairness penalty term to the loss function', false],
                ],
            ],
            [
                'q' => "In-processing bias mitigation modifies the learning algorithm itself. An example is:",
                'opts' => [
                    ['Oversampling the minority class before training', false],
                    ['Adding a fairness regularization term to the loss: L_total = L_task + λ·L_fairness', true],
                    ['Calibrating model scores after training separately per group', false],
                    ['Removing protected attributes from the feature set', false],
                ],
            ],
            [
                'q' => "Post-processing bias mitigation adjusts model outputs AFTER training. A common approach is:\n\nFor a binary classifier, set group-specific decision thresholds:\n  Group A threshold: 0.6\n  Group B threshold: 0.4\n\nThis is called:",
                'opts' => [
                    ['Calibration', false],
                    ['Disparate threshold adjustment to equalize TPR or FPR across groups', true],
                    ['Adversarial training', false],
                    ['Data augmentation', false],
                ],
            ],
            [
                'q' => "Removing a protected attribute (e.g., gender) from the feature set does NOT always eliminate bias because:",
                'opts' => [
                    ['The model will not train without gender', false],
                    ['Proxy variables (e.g., name, ZIP code, shopping patterns) can still encode gender implicitly', true],
                    ['Gender is always the most predictive feature', false],
                    ['The model must see all features to produce predictions', false],
                ],
            ],

            // ── GDPR & LEGAL FRAMEWORK — INTERMEDIATE ─────────────────────
            [
                'q' => "A healthcare app collects location data to find nearby hospitals. Under GDPR purpose limitation, using that same location data to target users with pharmaceutical ads requires:",
                'opts' => [
                    ['Nothing — data already collected can be reused', false],
                    ['A new, separate consent from the user or another valid legal basis for this new purpose', true],
                    ['Only a privacy policy update', false],
                    ['Notifying the supervisory authority', false],
                ],
            ],
            [
                'q' => "A company wants to transfer EU citizens\' data to a server in a country without an EU adequacy decision.\n\nUnder GDPR, they may still do so by using:",
                'opts' => [
                    ['Any commercial contract between the two companies', false],
                    ['Standard Contractual Clauses (SCCs) approved by the European Commission or Binding Corporate Rules', true],
                    ['A simple user consent checkbox', false],
                    ['A bilateral trade agreement between the countries', false],
                ],
            ],
            [
                'q' => "A DPIA (Data Protection Impact Assessment) concludes that risks remain HIGH even after mitigation measures.\n\nThe next required step under GDPR is:",
                'opts' => [
                    ['Proceed with processing since a DPIA was conducted', false],
                    ['Consult the supervisory authority before starting the processing', true],
                    ['Cancel the project permanently', false],
                    ['Ask users to opt out', false],
                ],
            ],

            // ── PRIVACY-ENHANCING TECH — INTERMEDIATE ─────────────────────
            [
                'q' => "```python\nimport numpy as np\n\ndef laplace_mechanism(true_value, sensitivity, epsilon):\n    scale = sensitivity / epsilon\n    noise = np.random.laplace(0, scale)\n    return true_value + noise\n\nresult = laplace_mechanism(true_value=500, sensitivity=1, epsilon=0.1)\n```\n\nThe noise scale for this query is:",
                'opts' => [
                    ['0.1', false],
                    ['10', true],
                    ['500', false],
                    ['1', false],
                ],
            ],
            [
                'q' => "```python\nresult1 = laplace_mechanism(500, sensitivity=1, epsilon=0.1)\nresult2 = laplace_mechanism(500, sensitivity=1, epsilon=10)\n```\n\nWhich result has smaller expected absolute noise?",
                'opts' => [
                    ['result1 (ε=0.1) — smaller ε means less noise', false],
                    ['result2 (ε=10) — larger ε means less noise (scale = 1/10 = 0.1)', true],
                    ['Both have identical expected noise', false],
                    ['Neither adds noise since sensitivity=1', false],
                ],
            ],
            [
                'q' => "In a federated learning system with 1000 clients:\n  - Each client trains locally for 5 epochs\n  - Only model gradients (not raw data) are shared\n\nAn attacker intercepts a client's gradient update. They can potentially:",
                'opts' => [
                    ['Reconstruct the exact raw training data using gradient inversion attacks', true],
                    ['Do nothing — gradients contain no information about the data', false],
                    ['Only learn the model architecture', false],
                    ['Access the central server\'s data', false],
                ],
            ],
            [
                'q' => "To defend against gradient inversion attacks in federated learning, which technique adds the strongest privacy guarantee?",
                'opts' => [
                    ['Using HTTPS for gradient transmission', false],
                    ['Applying differential privacy (adding calibrated noise to gradients before sharing)', true],
                    ['Compressing gradients before sending', false],
                    ['Only sharing gradients with the server admin', false],
                ],
            ],

            // ── DATA GOVERNANCE — INTERMEDIATE ────────────────────────────
            [
                'q' => "A company has three departments each defining \"revenue\" differently:\n  - Sales: revenue = invoiced amount\n  - Finance: revenue = collected cash\n  - Analytics: revenue = recognized revenue\n\nThis problem is solved by establishing:",
                'opts' => [
                    ['A single database for all departments', false],
                    ['A business glossary with agreed, authoritative definitions for each key metric', true],
                    ['Separate reporting tools per department', false],
                    ['Weekly reconciliation meetings only', false],
                ],
            ],
            [
                'q' => "Role-Based Access Control (RBAC) assigns permissions based on a user's role. Under the principle of least privilege:\n\nA junior data analyst should have:",
                'opts' => [
                    ['Full access to all production databases', false],
                    ['Read-only access to only the datasets required for their specific tasks', true],
                    ['Write access to the data warehouse', false],
                    ['Admin access to add new users', false],
                ],
            ],
            [
                'q' => "A data audit trail logs:\n  - Who accessed data\n  - What they did (read/write/delete)\n  - When each action occurred\n\nThis audit trail is essential for which two governance goals?",
                'opts' => [
                    ['Data compression and archiving', false],
                    ['Accountability (tracing responsibility) and compliance verification (proving regulatory adherence)', true],
                    ['Model training and validation', false],
                    ['Data quality improvement and feature engineering', false],
                ],
            ],
            [
                'q' => "A data quality framework measures six dimensions. Which combination correctly pairs a dimension with its definition?",
                'opts' => [
                    ['Completeness = data has no duplicates; Accuracy = data is up-to-date', false],
                    ['Completeness = required data is present; Accuracy = data correctly represents real-world values', true],
                    ['Timeliness = data has no nulls; Consistency = data uses the same format everywhere', false],
                    ['Uniqueness = data is accurate; Validity = data is complete', false],
                ],
            ],

            // ── RESPONSIBLE AI — INTERMEDIATE ─────────────────────────────
            [
                'q' => "```python\nimport shap\nexplainer = shap.TreeExplainer(model)\nshap_values = explainer.shap_values(X_test)\nshap.summary_plot(shap_values, X_test)\n```\n\nA SHAP summary plot shows feature 'age' has both large positive and large negative SHAP values.\nThis means:",
                'opts' => [
                    ['Age has no effect on the model predictions', false],
                    ['Age has a strong but directionally varying effect — for some individuals it increases predictions, for others it decreases them', true],
                    ['The model is biased against older people', false],
                    ['Age should be removed from the model', false],
                ],
            ],
            [
                'q' => "A counterfactual explanation states: 'Your loan was denied. If your income were ₱5,000 higher, it would have been approved.'\n\nCounterfactual explanations are valuable for users because:",
                'opts' => [
                    ['They reveal the model\'s internal weights', false],
                    ['They provide actionable, understandable guidance on what the individual can change to get a different outcome', true],
                    ['They explain all model predictions globally', false],
                    ['They guarantee the model is fair', false],
                ],
            ],
            [
                'q' => "Under the EU AI Act, providers of high-risk AI systems must:\n\n  1. Register the system in an EU database\n  2. Conduct conformity assessments\n  3. Maintain technical documentation\n  4. Implement human oversight mechanisms\n\nA medical diagnosis AI refusing to provide any explanation for its decision violates which requirement?",
                'opts' => [
                    ['EU database registration', false],
                    ['Human oversight mechanisms — decisions must be reviewable and explainable to clinicians', true],
                    ['Technical documentation archiving', false],
                    ['Conformity assessment timing', false],
                ],
            ],

            // ── ETHICAL PRACTICE — INTERMEDIATE ───────────────────────────
            [
                'q' => "A data scientist is asked to build a model that predicts which neighborhoods to target for increased police patrols, trained on historical arrest data.\n\nThe core ethical problem with this approach is:",
                'opts' => [
                    ['The dataset is too small', false],
                    ['Historical arrest data reflects existing policing bias — the model will amplify and automate that bias, creating a feedback loop', true],
                    ['Regression models cannot handle geographic data', false],
                    ['Police data is too noisy for machine learning', false],
                ],
            ],
            [
                'q' => "The concept of 'informed consent' for data collection requires that individuals understand:\n\n  A. What data is being collected\n  B. Why it is being collected\n  C. How it will be used\n  D. Who it will be shared with\n  E. How long it will be retained\n\nA cookie consent banner that says only 'We use cookies' satisfies:",
                'opts' => [
                    ['All five requirements (A–E)', false],
                    ['None of the requirements — it is too vague to be informed consent', true],
                    ['Requirements A and B only', false],
                    ['Requirements C and D only', false],
                ],
            ],
            [
                'q' => "An organization conducts an annual 'algorithmic impact assessment' for all deployed AI models. This assessment should include:",
                'opts' => [
                    ['Only model accuracy metrics on the current test set', false],
                    ['Fairness audits across demographic groups, review of data lineage, assessment of real-world outcomes, and stakeholder feedback', true],
                    ['Only a legal compliance checklist', false],
                    ['Performance benchmarks compared to competitor models', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 18 — Privacy, Ethics & Data Governance (Intermediate).");
    }
}