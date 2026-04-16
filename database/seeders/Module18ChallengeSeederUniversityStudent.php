<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module18ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 18 — Privacy, Ethics & Data Governance (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Privacy, Ethics & Data Governance',
            'description'           => 'Apply your understanding of data ethics and privacy frameworks analytically. Expect scenario-based reasoning, legal framework comparisons, bias identification, and fairness metric interpretation.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 700,
            'order_index'           => 18,
        ]);

        $this->command->info("Seeding University Student ethics & privacy questions...");

        $qaData = [

            // ── WHY ETHICS & PRIVACY MATTER ───────────────────────────────
            [
                'q' => 'A company uses a customer\'s purchase history to automatically increase their insurance premiums without disclosure. This violates which two principles simultaneously?',
                'opts' => [
                    ['Data minimization and encryption', false],
                    ['Transparency and purpose limitation', true],
                    ['Data accuracy and availability', false],
                    ['Anonymization and pseudonymization', false],
                ],
            ],
            [
                'q' => 'Purpose limitation under GDPR means that data collected for one purpose:',
                'opts' => [
                    ['Can be freely reused for any other purpose as long as it is within the same company', false],
                    ['Cannot be used for a new, incompatible purpose without further consent or legal basis', true],
                    ['Must be deleted after that purpose is fulfilled regardless of any other use', false],
                    ['Can be shared with third parties only once', false],
                ],
            ],
            [
                'q' => 'Which of the following illustrates a tension between data utility and privacy?',
                'opts' => [
                    ['Adding more columns to a dataset', false],
                    ['Heavily anonymizing a medical dataset makes it less useful for research but better protects patient identity', true],
                    ['Using a larger training set improves model accuracy', false],
                    ['Publishing a dataset with no sensitive columns', false],
                ],
            ],
            [
                'q' => 'The "right of access" under GDPR gives individuals the right to:',
                'opts' => [
                    ['Access any company\'s internal database', false],
                    ['Request and receive a copy of the personal data a company holds about them', true],
                    ['Access all data collected by any government agency', false],
                    ['Correct only financial records', false],
                ],
            ],

            // ── PERSONAL DATA & SENSITIVITY ───────────────────────────────
            [
                'q' => 'Under GDPR, which category of data requires EXPLICIT consent and extra protection?',
                'opts' => [
                    ['Name and email address', false],
                    ['Age and country of residence', false],
                    ['Health data, biometric data, and political opinions', true],
                    ['Purchase preferences and browsing history', false],
                ],
            ],
            [
                'q' => 'A dataset contains: Name, ZIP code, Date of Birth, Gender.\nEven without direct identifiers like SSN, this combination can often re-identify individuals. This risk is called:',
                'opts' => [
                    ['Data minimization failure', false],
                    ['Re-identification attack using quasi-identifiers', true],
                    ['Data leakage from the model', false],
                    ['K-anonymity enforcement', false],
                ],
            ],
            [
                'q' => 'A dataset achieves k-anonymity with k=3. This means:',
                'opts' => [
                    ['Each row appears exactly 3 times', false],
                    ['Each individual is indistinguishable from at least 2 other individuals based on quasi-identifiers', true],
                    ['The dataset has at most 3 unique values per column', false],
                    ['At most 3 people can access the dataset', false],
                ],
            ],
            [
                'q' => 'You have a dataset where ZIP=90210, Age=28, Gender=Male appears only once. This record fails k-anonymity because:',
                'opts' => [
                    ['The ZIP code is too long', false],
                    ['The unique combination of quasi-identifiers allows that individual to be singled out (k=1)', true],
                    ['Age is a sensitive attribute', false],
                    ['The record has missing values', false],
                ],
            ],
            [
                'q' => 'L-diversity extends k-anonymity by requiring that each equivalence class also has:',
                'opts' => [
                    ['At least l rows', false],
                    ['At least l well-represented values of the sensitive attribute', true],
                    ['At most l quasi-identifiers', false],
                    ['Exactly l distinct ZIP codes', false],
                ],
            ],

            // ── GDPR FRAMEWORK ───────────────────────────────────────────
            [
                'q' => 'Under GDPR, which role is legally responsible for determining the purposes and means of processing personal data?',
                'opts' => [
                    ['Data Processor', false],
                    ['Data Controller', true],
                    ['Data Subject', false],
                    ['Data Protection Officer', false],
                ],
            ],
            [
                'q' => 'A cloud company (AWS, Azure) stores and processes data on behalf of a client. Under GDPR, the cloud company acts as a:',
                'opts' => [
                    ['Data Controller', false],
                    ['Data Processor', true],
                    ['Data Subject', false],
                    ['Supervisory Authority', false],
                ],
            ],
            [
                'q' => 'GDPR requires organizations to report a data breach to the supervisory authority within:',
                'opts' => [
                    ['7 days', false],
                    ['30 days', false],
                    ['72 hours', true],
                    ['6 months', false],
                ],
            ],
            [
                'q' => 'A Data Protection Impact Assessment (DPIA) under GDPR is required when:',
                'opts' => [
                    ['Any new database is created', false],
                    ['Processing is likely to result in high risk to individuals\' rights and freedoms', true],
                    ['More than 100 rows of data are collected', false],
                    ['The company employs more than 50 people', false],
                ],
            ],
            [
                'q' => 'GDPR\'s "storage limitation" principle states that personal data should be:',
                'opts' => [
                    ['Kept indefinitely for future business analysis', false],
                    ['Kept only as long as necessary for the specified purpose, then deleted or anonymized', true],
                    ['Stored on EU servers only', false],
                    ['Limited to 1 GB per individual', false],
                ],
            ],

            // ── GLOBAL PRIVACY LAWS ────────────────────────────────────────
            [
                'q' => 'How does the California Consumer Privacy Act (CCPA) differ from GDPR in its approach to consent?',
                'opts' => [
                    ['CCPA requires opt-in consent before any data collection; GDPR allows opt-out', false],
                    ['CCPA primarily uses an opt-out model (consumers can say "do not sell my data"); GDPR requires opt-in consent for many uses', true],
                    ['Both laws are identical in their consent requirements', false],
                    ['CCPA applies to non-profit organizations while GDPR does not', false],
                ],
            ],
            [
                'q' => 'The Philippines\' Data Privacy Act of 2012 is enforced by:',
                'opts' => [
                    ['The Department of Information and Communications Technology', false],
                    ['The National Privacy Commission (NPC)', true],
                    ['The Supreme Court', false],
                    ['The Department of Justice', false],
                ],
            ],
            [
                'q' => 'Under both GDPR and CCPA, individuals have the right to request deletion of their personal data. One key DIFFERENCE is:',
                'opts' => [
                    ['GDPR applies globally while CCPA applies only to companies with revenue > $25M or similar thresholds', true],
                    ['CCPA applies globally while GDPR applies only in Europe', false],
                    ['Neither law allows data deletion requests from individuals', false],
                    ['Both laws are identical in scope and applicability', false],
                ],
            ],

            // ── ALGORITHMIC BIAS — ANALYTICAL ─────────────────────────────
            [
                'q' => 'A loan approval model has:\n  - Overall approval rate: 70%\n  - Approval rate for Group A: 80%\n  - Approval rate for Group B: 45%\n\nThis disparity is an indicator of:',
                'opts' => [
                    ['Good model calibration', false],
                    ['Potential disparate impact — Group B is disproportionately denied loans', true],
                    ['Overfitting to Group A', false],
                    ['Data leakage from Group B records', false],
                ],
            ],
            [
                'q' => 'The "80% rule" (or four-fifths rule) for disparate impact states that a selection rate for a protected group is problematic if it is less than:',
                'opts' => [
                    ['50% of the majority group\'s rate', false],
                    ['80% of the highest group\'s selection rate', true],
                    ['80% accuracy for the protected group', false],
                    ['4/5 of the total dataset size', false],
                ],
            ],
            [
                'q' => 'Group A has a selection rate of 60% and Group B has 40%. Applying the 80% rule:\n\n  Ratio = 40% / 60% = 0.667\n\nThis ratio is:',
                'opts' => [
                    ['Above 0.8, so no disparate impact is indicated', false],
                    ['Below 0.8, indicating potential disparate impact against Group B', true],
                    ['Equal to 0.8, so the threshold is exactly met', false],
                    ['The 80% rule cannot be applied here', false],
                ],
            ],
            [
                'q' => 'Historical bias in training data occurs when:',
                'opts' => [
                    ['The training data is too old (collected before 2000)', false],
                    ['The training data reflects past societal inequalities that the model then learns and perpetuates', true],
                    ['The training data has too many features', false],
                    ['The model is trained for too many epochs on historical data', false],
                ],
            ],

            // ── FAIRNESS FRAMEWORKS ───────────────────────────────────────
            [
                'q' => 'You have these model results:\n  Group A: True Positive Rate (TPR) = 0.85\n  Group B: True Positive Rate (TPR) = 0.60\n\nThis model violates which fairness criterion?',
                'opts' => [
                    ['Demographic parity', false],
                    ['Equal opportunity (equal TPR across groups)', true],
                    ['Calibration', false],
                    ['Predictive parity', false],
                ],
            ],
            [
                'q' => '"Equalized odds" requires that across groups, a model has equal:',
                'opts' => [
                    ['Overall accuracy', false],
                    ['Both True Positive Rate AND False Positive Rate', true],
                    ['Precision and recall averaged', false],
                    ['Number of training samples', false],
                ],
            ],
            [
                'q' => 'It has been mathematically proven that several fairness metrics (demographic parity, equalized odds, and calibration) CANNOT all be satisfied simultaneously except in trivial cases. This is known as:',
                'opts' => [
                    ['The fairness paradox', false],
                    ['The impossibility theorem of fairness', true],
                    ['The accuracy-fairness trade-off lemma', false],
                    ['The bias-variance trade-off', false],
                ],
            ],
            [
                'q' => '"Calibration" as a fairness property means:',
                'opts' => [
                    ['The model accuracy is the same for all groups', false],
                    ['A predicted probability of X% means the event occurs X% of the time, equally across groups', true],
                    ['The model\'s precision is equal across groups', false],
                    ['The confusion matrix is symmetric', false],
                ],
            ],

            // ── PRIVACY-ENHANCING TECHNOLOGIES ───────────────────────────
            [
                'q' => 'Differential privacy adds noise calibrated to the "sensitivity" of a query. The privacy budget ε (epsilon) controls:',
                'opts' => [
                    ['The amount of data collected', false],
                    ['The privacy-accuracy trade-off — smaller ε means more noise and stronger privacy', true],
                    ['The number of queries allowed per user', false],
                    ['The encryption key length', false],
                ],
            ],
            [
                'q' => 'A smaller ε in differential privacy means:',
                'opts' => [
                    ['Less privacy protection and more accurate results', false],
                    ['Stronger privacy protection but less accurate query results', true],
                    ['Faster computation', false],
                    ['The data is fully anonymized', false],
                ],
            ],
            [
                'q' => 'In federated learning, "model poisoning" refers to:',
                'opts' => [
                    ['Sending corrupted gradients from a malicious client to manipulate the global model', true],
                    ['Accidentally deleting the global model weights', false],
                    ['Using too much memory on the central server', false],
                    ['Sending the full dataset instead of gradients', false],
                ],
            ],
            [
                'q' => 'Homomorphic encryption allows:',
                'opts' => [
                    ['Data to be compressed before sending', false],
                    ['Computations to be performed on encrypted data without decrypting it first', true],
                    ['Two parties to share encryption keys safely', false],
                    ['A dataset to be anonymized automatically', false],
                ],
            ],

            // ── DATA GOVERNANCE ───────────────────────────────────────────
            [
                'q' => 'A data governance framework typically includes which three core components?',
                'opts' => [
                    ['Data collection, data storage, data visualization', false],
                    ['People (roles & responsibilities), Processes (policies & procedures), and Technology (tools & systems)', true],
                    ['Data scientists, data engineers, and data analysts', false],
                    ['Input data, model, and output data', false],
                ],
            ],
            [
                'q' => 'A "master data management" (MDM) system ensures:',
                'opts' => [
                    ['Only the master server stores data', false],
                    ['A single, consistent, and authoritative version of key business entities (e.g., customer, product) across an organization', true],
                    ['Only senior managers can access data', false],
                    ['Data is compressed to master format', false],
                ],
            ],
            [
                'q' => 'Which of these is a sign of POOR data governance?',
                'opts' => [
                    ['Multiple teams have conflicting definitions of "active customer" with no agreed standard', true],
                    ['A data catalog documents all datasets with ownership and lineage', false],
                    ['Data access is controlled by role-based permissions', false],
                    ['A data retention policy specifies how long each data type is kept', false],
                ],
            ],
            [
                'q' => 'A "data retention policy" specifies:',
                'opts' => [
                    ['How much data is backed up each day', false],
                    ['How long different categories of data should be kept before deletion or archival', true],
                    ['The maximum size of a single database table', false],
                    ['Who can retain access to data after leaving the company', false],
                ],
            ],

            // ── RESPONSIBLE AI ────────────────────────────────────────────
            [
                'q' => 'A "model card" for an AI system typically documents:',
                'opts' => [
                    ['The programming language used', false],
                    ['Intended use, performance metrics across demographics, limitations, and ethical considerations', true],
                    ['The names of all engineers who built the model', false],
                    ['The server specifications the model runs on', false],
                ],
            ],
            [
                'q' => 'SHAP (SHapley Additive exPlanations) values explain a model\'s prediction by:',
                'opts' => [
                    ['Ranking features by their overall correlation with the target', false],
                    ['Attributing each feature a contribution value representing its impact on a specific prediction', true],
                    ['Generating counterfactual examples', false],
                    ['Testing model performance on held-out data', false],
                ],
            ],
            [
                'q' => 'A LIME (Local Interpretable Model-agnostic Explanations) explanation is "local" because:',
                'opts' => [
                    ['It only works for locally-trained models', false],
                    ['It explains one individual prediction by approximating the model with a simple interpretable model around that specific data point', true],
                    ['It explains the overall global model behavior', false],
                    ['It only works with data from local servers', false],
                ],
            ],
            [
                'q' => 'The EU AI Act classifies AI systems into risk categories. A credit scoring AI would fall under:',
                'opts' => [
                    ['Minimal risk', false],
                    ['Limited risk', false],
                    ['High risk — it affects individuals\' access to financial services', true],
                    ['Unacceptable risk (banned)', false],
                ],
            ],

            // ── BUILDING AN ETHICAL PRACTICE ──────────────────────────────
            [
                'q' => 'An "ethics review board" or "AI ethics committee" in an organization is responsible for:',
                'opts' => [
                    ['Writing all the company\'s code', false],
                    ['Reviewing data projects and AI deployments for ethical risks and compliance before and during deployment', true],
                    ['Approving all financial transactions', false],
                    ['Managing the company\'s social media presence', false],
                ],
            ],
            [
                'q' => '"Datasheets for Datasets" (a framework proposed by Gebru et al.) recommend that dataset creators document:',
                'opts' => [
                    ['Only the dataset\'s column names and data types', false],
                    ['Motivation, composition, collection process, recommended uses, and known limitations of the dataset', true],
                    ['Only the dataset size in rows and columns', false],
                    ['The names of all data scientists who worked on it', false],
                ],
            ],
            [
                'q' => 'Which practice helps ensure ongoing fairness of a deployed model over time?',
                'opts' => [
                    ['Training the model once and never retraining', false],
                    ['Continuous monitoring of model outputs across demographic groups and retraining when performance drifts', true],
                    ['Increasing model complexity to improve accuracy', false],
                    ['Removing demographic information from user profiles', false],
                ],
            ],
            [
                'q' => 'A whistleblower data scientist reports that their company\'s model is causing harm to minority groups. This action aligns with:',
                'opts' => [
                    ['A breach of company confidentiality always', false],
                    ['Professional ethical responsibility — harm prevention takes precedence over organizational loyalty when public interest is at risk', true],
                    ['An illegal act in all jurisdictions', false],
                    ['A violation of GDPR', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 18 — Privacy, Ethics & Data Governance (University Student).");
    }
}