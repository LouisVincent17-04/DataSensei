<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module18ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        // Challenge::where('challenge_category_id', $category->id)->delete();

        $this->command->info("Creating Module 18 — Privacy, Ethics & Data Governance (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Privacy, Ethics & Data Governance',
            'description'           => 'Production-grade privacy engineering, organizational governance failures, real-world legal compliance edge cases, systemic bias in deployed systems, and multi-stakeholder ethical decision-making under uncertainty.',
            'time_limit_seconds'    => 2100,
            'base_xp'               => 1400,
            'order_index'           => 18,
        ]);

        $this->command->info("Seeding Professional ethics & privacy questions...");

        $qaData = [

            // ── PRODUCTION PRIVACY FAILURES ───────────────────────────────
            [
                'q' => "```python\n# Production data pipeline\ndef process_user_events(events_df):\n    # Aggregate click data for analytics\n    aggregated = events_df.groupby('user_id').agg({\n        'page_views': 'sum',\n        'session_duration': 'mean',\n        'purchase_amount': 'sum'\n    }).reset_index()\n    # Write to analytics warehouse\n    aggregated.to_sql('user_analytics', engine, if_exists='append')\n    return aggregated\n```\n\nThis pipeline stores individual-level aggregated data linked to `user_id` in an analytics warehouse accessible to 200 analysts.\n\nThe primary privacy engineering problem is:",
                'opts' => [
                    ['The aggregation uses the wrong functions', false],
                    ['Storing user_id-linked behavioral profiles in a broadly accessible warehouse without purpose limitation or access controls violates data minimization and least-privilege principles', true],
                    ['SQL is not GDPR-compliant', false],
                    ['session_duration should not be averaged', false],
                ],
            ],
            [
                'q' => "```python\n# ML feature engineering pipeline\ndef create_features(df):\n    df['age_group'] = pd.cut(df['age'], bins=[0,25,35,50,65,100])\n    df['income_bracket'] = pd.qcut(df['income'], q=4)\n    df['zip_region'] = df['zip_code'].str[:3]  # first 3 digits\n    df['name_initial'] = df['full_name'].str[0]  # first letter\n    # Drop direct identifiers\n    df = df.drop(columns=['full_name', 'ssn', 'email'])\n    return df\n```\n\nA privacy engineer reviewing this pipeline flags it. The reason is:",
                'opts' => [
                    ['age_group generalization is too aggressive', false],
                    ['The combination of age_group + income_bracket + zip_region + name_initial still constitutes a set of quasi-identifiers that can re-identify individuals, especially in smaller demographic groups', true],
                    ['Dropping SSN is not sufficient for anonymization', false],
                    ['pd.cut and pd.qcut do not preserve data quality', false],
                ],
            ],
            [
                'q' => "```python\n# A/B testing framework stores experiment assignments:\nexperiment_log = {\n    'user_id': user_id,\n    'experiment_id': exp_id,\n    'variant': variant,           # 'control' or 'treatment'\n    'assigned_at': timestamp,\n    'outcome': conversion_event   # True/False\n}\ndb.experiments.insert(experiment_log)\n```\n\nUsers consented to 'improving our service' as the data use purpose.\n\nUsing this data to train a persuasion model that maximizes purchase probability (not merely measuring it) raises which GDPR issue?",
                'opts' => [
                    ['Experiment logs cannot be stored in databases', false],
                    ['Purpose creep — using A/B test data to build a persuasion optimization model is incompatible with the original purpose of "service improvement" without re-consent', true],
                    ['A/B testing is banned under GDPR', false],
                    ['Conversion events are not personal data', false],
                ],
            ],

            // ── SYSTEMIC BIAS IN PRODUCTION ───────────────────────────────
            [
                'q' => "A content recommendation model is deployed and monitored:\n\n  Month 1:  Engagement gap (Group A vs B): +2%\n  Month 3:  Engagement gap: +8%\n  Month 6:  Engagement gap: +19%\n  Month 12: Engagement gap: +34%\n\nThe widening gap is caused by a feedback loop. Explain the mechanism:",
                'opts' => [
                    ['Group B users have lower internet speed, causing slower engagement', false],
                    ['The model learns from engagement data; Group A engages more with certain content; the model recommends more of that content to Group A; Group A engages even more — creating a self-reinforcing cycle that excludes Group B', true],
                    ['The model was retrained monthly, each time introducing new bias', false],
                    ['Group B users opted out of personalization', false],
                ],
            ],
            [
                'q' => "```python\n# Monitoring dashboard alert:\n# Model: credit_risk_v3\n# Date: 2024-06-01\n# Alert: Disparate impact ratio dropped from 0.87 to 0.61\n#        for protected group 'age_65+'\n# Cause: Model retrained on 6 months of data from economic downturn\n#        where elderly customers had higher default rates\n```\n\nThe model was retrained on recent recession data where elderly people disproportionately defaulted.\nThe correct governance response is:",
                'opts' => [
                    ['Deploy the retrained model since it is more accurate on recent data', false],
                    ['Pause deployment, investigate whether the recession-period data represents a temporary anomaly that unfairly penalizes a protected group, and evaluate whether temporal or distributional controls are needed', true],
                    ['Remove age from the feature set only', false],
                    ['Reduce the model\'s confidence threshold for all predictions', false],
                ],
            ],
            [
                'q' => "A hiring AI is deployed globally. In Country A, using gender as a factor is illegal. In Country B, using affirmative action targets based on gender is legally mandated.\n\nThe same model must serve both jurisdictions. The correct architectural approach is:",
                'opts' => [
                    ['Build one global model that ignores gender for everyone', false],
                    ['Deploy jurisdiction-specific model variants with different fairness constraints, governed by a legal requirements registry per region', true],
                    ['Use the stricter law globally to be safe', false],
                    ['Let individual users choose which model applies to them', false],
                ],
            ],

            // ── PRIVACY ENGINEERING AT SCALE ──────────────────────────────
            [
                'q' => "```python\n# Privacy budget management across a data platform\nclass CentralPrivacyLedger:\n    def __init__(self):\n        self.budgets = {}  # {user_id: epsilon_consumed}\n\n    def request_query(self, user_id, query_epsilon, max_epsilon=5.0):\n        consumed = self.budgets.get(user_id, 0)\n        if consumed + query_epsilon > max_epsilon:\n            return None  # Deny query\n        self.budgets[user_id] = consumed + query_epsilon\n        return self._execute_with_noise(query_epsilon)\n```\n\nThis implements a per-user privacy budget. A critical scalability problem in production with millions of users is:",
                'opts' => [
                    ['The dictionary cannot store more than 1000 users', false],
                    ['In-memory budget tracking is not durable — a server restart loses all consumed budgets, allowing users to restart their privacy budget exploitation', true],
                    ['query_epsilon must always equal max_epsilon', false],
                    ['The Laplace noise cannot be computed for large epsilon values', false],
                ],
            ],
            [
                'q' => "A data platform implements 'privacy by default' for all new features.\n\nThis means the engineering team must, for each new data feature:\n\n  □ Classify data sensitivity level\n  □ Document retention period\n  □ Define access control tiers\n  □ Assess re-identification risk\n  □ Obtain privacy sign-off before launch\n\nA feature is shipped without the re-identification risk assessment due to deadline pressure.\n6 months later, it is discovered that combining this feature's data with a public dataset re-identifies 12,000 users.\n\nThis scenario demonstrates:",
                'opts' => [
                    ['An acceptable engineering trade-off under time pressure', false],
                    ['That privacy reviews must be non-negotiable gates, not optional checklists — technical debt in privacy is qualitatively different from functional technical debt because harms are irreversible', true],
                    ['The re-identification risk assessment is not useful in practice', false],
                    ['The public dataset should have been better protected', false],
                ],
            ],
            [
                'q' => "```python\n# Synthetic data generation for sharing\nfrom sdv.tabular import CTGAN\n\nmodel = CTGAN(epochs=300)\nmodel.fit(real_sensitive_data)\nsynthetic_data = model.sample(num_rows=10000)\n\n# Membership inference attack test:\nfrom ml_privacy_meter import attack\nattack_results = attack.membership_inference(\n    target_model=model,\n    synthetic_data=synthetic_data,\n    real_data=real_sensitive_data\n)\nprint(attack_results['auc'])  # 0.73\n```\n\nA membership inference attack AUC of 0.73 against the synthetic data generator means:",
                'opts' => [
                    ['The synthetic data is perfectly private (AUC=0.73 is below 1.0)', false],
                    ['An adversary can determine whether a specific individual\'s data was in the training set with 73% AUC — significantly better than random (0.5), indicating the synthetic data leaks information about the real data', true],
                    ['73% of synthetic rows are identical to real rows', false],
                    ['The CTGAN model has 73% generative accuracy', false],
                ],
            ],

            // ── GOVERNANCE FAILURES & ORGANIZATIONAL DYNAMICS ─────────────
            [
                'q' => "A large organization has:\n  - A data governance policy document (published 3 years ago)\n  - No enforcement mechanism\n  - No data stewards assigned to any domain\n  - 12 different definitions of 'active customer' in use\n  - No data catalog\n\nThis organization has governance-as-paperwork (policies exist but are not operational).\n\nThe highest-priority first action to make governance operational is:",
                'opts' => [
                    ['Update the governance policy document immediately', false],
                    ['Assign accountable data owners per domain and give them authority and resources — governance only works with human accountability tied to consequences', true],
                    ['Buy a data catalog software tool', false],
                    ['Train all staff on the existing policy document', false],
                ],
            ],
            [
                'q' => "A financial institution's data governance committee includes:\n  - CDO (Chief Data Officer)\n  - CTO (Chief Technology Officer)\n  - CCO (Chief Compliance Officer)\n  - Legal counsel\n  - Business unit representatives\n\nA new AI model for loan approval is proposed. It achieves 94% accuracy but a fairness audit shows disparate impact ratio = 0.61 against a protected class.\n\nThe CCO says deploy — accuracy justifies it. Legal counsel says reject — liability risk. Business says deploy — competitive pressure.\n\nThe data-ethically correct resolution process is:",
                'opts' => [
                    ['The highest-ranking person decides', false],
                    ['Conduct a formal DPIA and fairness impact assessment, document the trade-offs transparently, attempt to remediate bias, and if unresolvable, escalate to the board with a clear risk statement — deploy only if all parties accept documented residual risk', true],
                    ['Deploy with a disclaimer in the terms of service', false],
                    ['Use the model for 90 days in a trial and monitor complaints', false],
                ],
            ],
            [
                'q' => "```python\n# Data retention automation\ndef enforce_retention_policy(db_connection, policy):\n    for table, retention_days in policy.items():\n        cutoff = datetime.now() - timedelta(days=retention_days)\n        db_connection.execute(\n            f'DELETE FROM {table} WHERE created_at < %s',\n            (cutoff,)\n        )\n\n# Called nightly in production\nenforce_retention_policy(conn, {\n    'user_sessions':      30,\n    'purchase_history':   365,\n    'medical_records':    2555,  # 7 years\n    'audit_logs':         3650   # 10 years\n})\n```\n\nAn auditor notices that `audit_logs` is subject to automatic deletion after 10 years, but the organization is under an ongoing regulatory investigation requiring all logs to be preserved.\n\nThe production system must:",
                'opts' => [
                    ['Continue automated deletion — policy must be followed', false],
                    ['Implement a legal hold mechanism that suspends automated retention deletion for records under investigation — automated deletion without legal hold checking is a compliance and evidence destruction risk', true],
                    ['Manually delete audit logs before the investigation team accesses them', false],
                    ['Increase the retention period to 100 years globally', false],
                ],
            ],

            // ── ADVANCED FAIRNESS TRADE-OFFS ──────────────────────────────
            [
                'q' => "A medical AI for sepsis detection is evaluated:\n\n  Overall AUC: 0.91\n  White patients:   TPR=0.89, FPR=0.08\n  Black patients:   TPR=0.72, FPR=0.06\n  Hispanic patients: TPR=0.78, FPR=0.07\n\nThe model achieves similar FPR across groups but significantly different TPR.\nIn a life-threatening medical context, which disparity is more ethically severe and why?",
                'opts' => [
                    ['FPR disparity — false alarms waste resources equally across groups', false],
                    ['TPR disparity — missing sepsis (false negatives) means delayed treatment and higher mortality; the model misses 27% more sepsis cases in Black patients compared to White patients', true],
                    ['Both are equally severe in all medical contexts', false],
                    ['Neither is severe since overall AUC=0.91 is excellent', false],
                ],
            ],
            [
                'q' => "Chouldechova's impossibility theorem proves that for a binary classifier, when base rates differ across groups, it is impossible to simultaneously satisfy:\n\n  A. Calibration (equal PPV across groups)\n  B. Equal false positive rates\n  C. Equal false negative rates\n\nFor a recidivism tool where Black defendants have a higher base rate of recidivism in the biased historical data:\n\nInsisting the tool satisfies calibration (A) while achieving equal FPR (B) will necessarily result in:",
                'opts' => [
                    ['Higher FNR for the lower-base-rate group (White defendants miss more true positives)', false],
                    ['Higher FNR for the higher-base-rate group — if calibration holds with different base rates, equalized FPR forces unequal FNR, meaning more actual recidivists from the high-base-rate group are missed', false],
                    ['Higher FNR for the lower-base-rate group — when base rates differ and calibration holds, equalizing FPR mathematically requires a higher FNR for the group with a lower base rate', true],
                    ['The model automatically becomes uncalibrated', false],
                ],
            ],
            [
                'q' => "A team implements equal opportunity fairness (equal TPR) for a credit model.\n\nStakeholder A (regulators): 'This is fair — qualified applicants in both groups are approved equally.'\nStakeholder B (applicants): 'This is unfair — Group B still has a higher FPR, meaning more Group B members are falsely labeled risky.'\nStakeholder C (bank): 'This maximizes business value within legal constraints.'\n\nThis scenario illustrates:",
                'opts' => [
                    ['One stakeholder is correct and the others are wrong', false],
                    ['Fairness is multidimensional — different valid stakeholder perspectives prioritize different metrics; the choice of which fairness criterion to optimize is inherently a value judgment, not a purely technical decision', true],
                    ['Equal opportunity is not a valid fairness metric', false],
                    ['The bank\'s perspective should always be prioritized', false],
                ],
            ],

            // ── RESPONSIBLE AI — PROFESSIONAL ─────────────────────────────
            [
                'q' => "```python\n# Continuous fairness monitoring in production\nclass FairnessMonitor:\n    def __init__(self, threshold=0.05):\n        self.threshold = threshold\n        self.history = []\n\n    def evaluate(self, y_true, y_pred, sensitive_features):\n        from fairlearn.metrics import equalized_odds_difference\n        eod = equalized_odds_difference(\n            y_true, y_pred,\n            sensitive_features=sensitive_features\n        )\n        self.history.append({'timestamp': datetime.now(), 'eod': eod})\n        if eod > self.threshold:\n            self.trigger_alert(eod)\n        return eod\n\n    def trigger_alert(self, eod):\n        # Sends alert to ML platform team\n        pass\n```\n\nAfter 6 months, equalized_odds_difference trends:\n  Month 1: 0.02, Month 3: 0.04, Month 4: 0.06 [ALERT], Month 5: 0.09 [ALERT], Month 6: 0.14 [ALERT]\n\nThe alerts were sent but no action was taken. This governance failure is called:",
                'opts' => [
                    ['Alert fatigue causing the monitoring system to shut down', false],
                    ['Monitoring-without-action — having detection capability without a defined remediation process and ownership is operationally equivalent to not monitoring at all', true],
                    ['The threshold of 0.05 was set too low', false],
                    ['Equalized odds difference cannot be monitored in real time', false],
                ],
            ],
            [
                'q' => "An organization builds an AI ethics framework with three layers:\n\n  Layer 1: Principles (fairness, transparency, accountability, privacy)\n  Layer 2: Policies (DPIA requirements, fairness thresholds, model cards)\n  Layer 3: Practices (code reviews, bias audits, monitoring pipelines)\n\nResearch (e.g., Mittelstadt 2019) shows that principle-based AI ethics frameworks often fail in practice because:",
                'opts' => [
                    ['Principles are too complex for engineers to understand', false],
                    ['Abstract principles without enforcement mechanisms, incentive structures, and organizational accountability create ethics-washing — the appearance of ethical practice without substantive change', true],
                    ['Layer 3 practices are not technically feasible', false],
                    ['DPIA requirements conflict with GDPR', false],
                ],
            ],
            [
                'q' => "A data scientist at a social media company discovers that the recommendation algorithm amplifies politically extreme content because extreme content maximizes engagement (which the model optimizes).\n\nThe company's legal team confirms this is not illegal in their jurisdiction.\nThe product team refuses to change the objective because engagement metrics drive revenue.\n\nFrom a professional ethics standpoint, the data scientist's obligations are:",
                'opts' => [
                    ['Limited to legal compliance — if it\'s not illegal, it\'s not their concern', false],
                    ['To document the finding formally, escalate through all available internal channels (ethics board, leadership), and if systemic public harm continues without remediation, consider external options — professional ethics codes (IEEE, ACM) require harm prevention beyond legal minimums', true],
                    ['To optimize the algorithm better so extreme content is less rewarded', false],
                    ['To immediately quit and publish their findings', false],
                ],
            ],

            // ── EDGE CASES & COMPLEX SCENARIOS ────────────────────────────
            [
                'q' => "A research team wants to use a dataset of medical records (with patient consent for 'medical research') to train a large language model that will be commercialized.\n\nThe original consent was for 'medical research.' Commercialization of a general LLM goes beyond this scope.\n\nUnder GDPR, the correct action before proceeding is:",
                'opts' => [
                    ['Proceed — \'medical research\' is broad enough to cover commercial LLM training', false],
                    ['Obtain fresh explicit consent for the new purpose, or conduct a compatibility assessment that the new use is genuinely compatible with the original research purpose — commercial LLM training likely fails this test', true],
                    ['Anonymize the data and proceed without consent', false],
                    ['Apply for a DPIA waiver from the supervisory authority', false],
                ],
            ],
            [
                'q' => "```python\n# A model trained to predict employee attrition\n# Features include: performance_score, tenure, salary_band,\n#                   department, overtime_hours, sick_days_taken\n\n# Discovered post-deployment:\n# 'sick_days_taken' strongly correlates with disability status\n# in the workforce data (r=0.67)\n```\n\nUsing 'sick_days_taken' as a feature means the model has learned a proxy for disability status, a protected characteristic.\n\nBeyond removing the feature, the comprehensive remediation requires:",
                'opts' => [
                    ['Replacing sick_days_taken with a normalized version', false],
                    ['Auditing all features for proxy correlations with protected attributes, retraining without proxy features, re-evaluating fairness metrics, and conducting a retrospective impact review of decisions made by the original model', true],
                    ['Retraining the model with double the data', false],
                    ['Informing employees that the model uses sick days as a feature', false],
                ],
            ],
            [
                'q' => "An international organization operates data pipelines that process:\n  - EU citizen data (GDPR applies)\n  - California residents' data (CCPA applies)\n  - Philippine residents' data (DPA 2012 applies)\n  - Singapore residents' data (PDPA applies)\n\nThey use a single global data lake.\n\nThe 'lowest common denominator' compliance approach (applying the strictest rule globally) has which key trade-off?",
                'opts' => [
                    ['It is cheaper than jurisdiction-specific compliance', false],
                    ['It simplifies compliance at the cost of potentially over-restricting data uses that are legally permissible in some jurisdictions, impacting legitimate business operations in those regions', true],
                    ['It guarantees full compliance in all jurisdictions', false],
                    ['It is required by international law', false],
                ],
            ],
            [
                'q' => "A data scientist is asked to evaluate whether to deploy a predictive policing model.\n\nAfter analysis, they find:\n  - Precision: 72% (of predicted crime locations, 72% have incidents)\n  - Recall: 41% (model misses 59% of actual incidents)\n  - The model was trained on 10 years of arrest data from a period of documented discriminatory policing\n  - Communities of color are 3.1× more likely to be flagged by the model\n  - Deployment will increase police presence in flagged areas, generating more arrests, which feed back into future training data\n\nThe technically sound and ethically defensible recommendation is:",
                'opts' => [
                    ['Deploy the model — 72% precision is acceptable for law enforcement', false],
                    ['Do not deploy — the combination of biased training data, poor recall, disparate racial impact, and the feedback loop between deployment and future training data creates compounding, self-reinforcing harm that precision alone cannot justify', true],
                    ['Deploy with a disclaimer that the model may be biased', false],
                    ['Retrain on 20 years of data to improve recall before deploying', false],
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

        $this->command->info("✅ Done! Questions seeded for Module 18 — Privacy, Ethics & Data Governance (Professional).");
    }
}