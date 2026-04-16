<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module8ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', 'Statistical Methods & Experimental Design')
            ->delete();

        $this->command->info("Creating Module 8 — Statistical Methods & Experimental Design (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Statistical Methods & Experimental Design',
            'description'           => 'Professional-grade statistical challenges: real-world study design flaws, production code audits, edge cases in inference, performance trade-offs, and nuanced interpretation of complex analyses.',
            'time_limit_seconds'    => 2400,
            'base_xp'               => 3000,
            'order_index'           => 8,
        ]);

        $this->command->info("Seeding 50 professional-level statistics questions...");

        $qaData = [

            // ── REAL-WORLD STUDY DESIGN CRITIQUE ──────────────────────────
            [
                'q' => 'A pharmaceutical company runs a clinical trial: 500 patients are enrolled, but only 300 complete the study. The final analysis uses only completers. What is the PRIMARY statistical concern?

A) Sample size is too small
B) Survivorship / attrition bias — dropouts may differ systematically from completers, violating the intent-to-treat principle
C) The study lacks a control group
D) The placebo effect was not accounted for',
                'opts' => [
                    ['A', false],
                    ['B', true],
                    ['C', false],
                    ['D', false],
                ],
            ],
            [
                'q' => 'An online A/B test runs for 3 days. The analyst checks results every hour and stops when p < 0.05 is reached on day 2. What is the core statistical problem?',
                'opts' => [
                    ['The sample was too large', false],
                    ['Peeking / optional stopping inflates the Type I error rate far above the nominal α = 0.05', true],
                    ['Day 2 results are always unreliable', false],
                    ['A/B tests require paired t-tests', false],
                ],
            ],
            [
                'q' => 'A company measures employee productivity before and after a new training program. Productivity improves. The analyst concludes the training caused the improvement. What threats to internal validity exist?

Select the BEST answer.',
                'opts' => [
                    ['None — pre-post designs are always valid', false],
                    ['History effects (external events), maturation, and regression to the mean — all can explain improvement without a control group', true],
                    ['Only the placebo effect is a concern', false],
                    ['The test is invalid because productivity is subjective', false],
                ],
            ],
            [
                'q' => 'A survey is conducted by calling landline phones during business hours to measure public opinion on remote work. Which sampling bias is MOST severe?',
                'opts' => [
                    ['Voluntary response bias', false],
                    ['Coverage bias — employed, remote-working individuals are systematically excluded', true],
                    ['Response bias from question wording', false],
                    ['Clustering bias', false],
                ],
            ],
            [
                'q' => 'A hospital compares mortality rates: Hospital A (40%) vs Hospital B (20%). They conclude Hospital B is better. However, Hospital A treats 80% severe cases and Hospital B treats 80% mild cases. This is an example of:',
                'opts' => [
                    ['Regression to the mean', false],
                    ['Type II error', false],
                    ['Simpson\'s Paradox / confounding by severity — case-mix adjustment is required', true],
                    ['Measurement error', false],
                ],
            ],

            // ── PRODUCTION CODE AUDIT ──────────────────────────────────────
            [
                'q' => 'Review this production A/B test analysis code:

from scipy import stats
import numpy as np

control = np.random.binomial(1, 0.10, 10000)   # 10% baseline conversion
treatment = np.random.binomial(1, 0.11, 10000)  # 11% treatment conversion

t, p = stats.ttest_ind(control, treatment)
if p < 0.05:
    print("Deploy treatment")

What is the most critical issue with using ttest_ind here?',
                'opts' => [
                    ['sample size is too small', false],
                    ['Binary conversion data should use a proportions z-test or chi-square test, not ttest_ind which assumes continuous normally-distributed data', true],
                    ['np.random.binomial is not appropriate for A/B tests', false],
                    ['The significance level should be 0.01 for business decisions', false],
                ],
            ],
            [
                'q' => 'This pipeline computes rolling p-values in a live experiment:

results = []
for day in range(1, 31):
    daily_data_control.append(new_control_observations)
    daily_data_treatment.append(new_treatment_observations)
    _, p = stats.ttest_ind(daily_data_control, daily_data_treatment)
    if p < 0.05:
        results.append("significant")
        break

What is the false positive rate after 30 days at α=0.05 if there is truly no effect?',
                'opts' => [
                    ['Exactly 5%', false],
                    ['Approximately 0%', false],
                    ['Far above 5% — potentially 40-60% due to repeated significance testing without correction', true],
                    ['Exactly 30 × 5% = 150% (impossible, so 100%)', false],
                ],
            ],
            [
                'q' => 'Audit this multi-variable regression pipeline:

from sklearn.linear_model import LinearRegression
import numpy as np

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2)

# Feature selection on ALL data before split
top_features = select_k_best(X, y, k=10)
X_train_selected = X_train[:, top_features]
X_test_selected = X_test[:, top_features]

model = LinearRegression().fit(X_train_selected, y_train)
print(model.score(X_test_selected, y_test))

What is the data leakage issue?',
                'opts' => [
                    ['test_size=0.2 is too small', false],
                    ['Feature selection was performed on the FULL dataset before splitting, so test set information leaked into feature selection, causing optimistically biased evaluation', true],
                    ['LinearRegression cannot handle multiple features', false],
                    ['k=10 features is too many', false],
                ],
            ],
            [
                'q' => 'A data scientist runs this code on a highly imbalanced dataset (98% class 0, 2% class 1):

from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score

model = LogisticRegression().fit(X_train, y_train)
print(f"Accuracy: {accuracy_score(y_test, model.predict(X_test)):.2%}")
# Output: Accuracy: 97.8%
# Scientist: "Great model!"

What is the fundamental evaluation mistake?',
                'opts' => [
                    ['Logistic regression is the wrong model type', false],
                    ['Accuracy is misleading on imbalanced data; a model predicting all class 0 would score ~98%. Precision, recall, F1, and AUC-ROC should be used', true],
                    ['The test set is too large', false],
                    ['The model should use accuracy_score from statsmodels, not sklearn', false],
                ],
            ],
            [
                'q' => 'Identify ALL issues in this bootstrap confidence interval implementation:

import numpy as np

data = [4, 6, 8, 5, 7, 9, 3]
bootstrap_means = []
for _ in range(100):  # Only 100 iterations
    sample = np.random.choice(data, size=len(data), replace=False)  # replace=False
    bootstrap_means.append(np.mean(sample))

ci_lower = np.percentile(bootstrap_means, 2.5)
ci_upper = np.percentile(bootstrap_means, 97.5)',
                'opts' => [
                    ['Only one issue: replace=False should be replace=True', false],
                    ['Two issues: (1) replace=False prevents proper bootstrap resampling — must be True; (2) 100 iterations is far too few — typically 1,000–10,000 are needed for stable estimates', true],
                    ['No issues', false],
                    ['percentile(2.5) and percentile(97.5) are wrong thresholds', false],
                ],
            ],

            // ── EDGE CASES IN INFERENCE ────────────────────────────────────
            [
                'q' => 'A study has n = 50,000 subjects. The two-sample t-test gives p = 0.0001, and Cohen\'s d = 0.04. How should this result be communicated professionally?',
                'opts' => [
                    ['The result is highly significant and practically important', false],
                    ['Statistically significant but practically trivial — with n=50,000, tiny effects reach significance. d=0.04 is negligible. Effect size and clinical/practical significance must be reported alongside p-value', true],
                    ['The result should be ignored since n is too large', false],
                    ['A non-parametric test should be used instead', false],
                ],
            ],
            [
                'q' => 'You conduct a meta-analysis across 10 studies on the same intervention. Most show p > 0.05 but all have positive effect directions with similar d ≈ 0.3. What is the most statistically rigorous conclusion?',
                'opts' => [
                    ['No effect exists because most studies were not significant', false],
                    ['A pooled analysis via meta-analysis can detect a consistent moderate effect even when individual studies are underpowered — this is a Type II error issue in individual studies, not lack of effect', true],
                    ['The studies are all flawed and should be discarded', false],
                    ['Conduct another study and ignore prior results', false],
                ],
            ],
            [
                'q' => 'A Bayesian analysis produces a 95% credible interval for μ of [2.1, 4.8]. A frequentist 95% CI gives [1.9, 5.1]. Which interpretation is CORRECT for each?',
                'opts' => [
                    ['Both mean: 95% chance the true μ is in the interval', false],
                    ['Bayesian CI: P(2.1 ≤ μ ≤ 4.8 | data) = 0.95 — a probability statement about μ. Frequentist CI: in repeated experiments, 95% of such intervals will contain the true μ — μ is fixed, not random', true],
                    ['Frequentist CI is always wider than Bayesian CI', false],
                    ['They are identical in interpretation', false],
                ],
            ],
            [
                'q' => 'You apply the Bonferroni correction to 100 tests at α = 0.05. The corrected threshold becomes α* = 0.0005. A colleague argues this is too conservative. What is the alternative and its trade-off?',
                'opts' => [
                    ['Use α = 0.05 with no correction', false],
                    ['Benjamini-Hochberg FDR correction — controls the expected proportion of false discoveries rather than the family-wise error rate, making it less conservative but allowing some false positives at a controlled rate', true],
                    ['Double the original α', false],
                    ['Use Bayesian testing which needs no correction', false],
                ],
            ],
            [
                'q' => 'A randomized experiment shows the treatment is effective (p = 0.01). However, the treatment assignment was not truly random — participants self-selected. What is the correct statistical/methodological conclusion?',
                'opts' => [
                    ['The causal conclusion stands because p < 0.05', false],
                    ['Self-selection is a form of confounding that breaks the causal chain — the observed association is valid but causal inference is not warranted. This is an observational study', true],
                    ['The study must be rejected entirely', false],
                    ['Intent-to-treat analysis fixes this problem', false],
                ],
            ],

            // ── ADVANCED EXPERIMENTAL DESIGN ──────────────────────────────
            [
                'q' => 'In a SEQUENTIAL ANALYSIS framework (e.g., using alpha-spending functions), what problem is being solved compared to fixed-sample designs?',
                'opts' => [
                    ['It removes the need for a null hypothesis', false],
                    ['It allows planned interim looks with controlled Type I error, unlike ad-hoc peeking which inflates false positives', true],
                    ['It eliminates the need for a control group', false],
                    ['It reduces the required sample size to zero', false],
                ],
            ],
            [
                'q' => 'An experiment uses a STEPPED-WEDGE design. What is its defining characteristic?',
                'opts' => [
                    ['All groups receive treatment simultaneously', false],
                    ['Groups cross over from control to treatment at different randomized time points — useful when withholding treatment from some permanently is unethical', true],
                    ['The treatment is applied in increasing doses', false],
                    ['Multiple outcome variables are measured at each step', false],
                ],
            ],
            [
                'q' => 'In a WITHIN-SUBJECTS design, the key statistical advantage over a between-subjects design is:',
                'opts' => [
                    ['Larger sample sizes are possible', false],
                    ['Individual differences are partitioned out, reducing error variance and increasing statistical power for the same n', true],
                    ['Confounding is completely eliminated', false],
                    ['No p-values are needed', false],
                ],
            ],
            [
                'q' => 'You are designing a cluster-randomized trial (schools randomized, not students). What adjustment is REQUIRED in your analysis that would NOT be needed in individual randomization?',
                'opts' => [
                    ['Use a one-sample t-test', false],
                    ['Account for intra-cluster correlation (ICC) — students in the same school are correlated, inflating false positives if treated as independent. Use multilevel modeling or GEE', true],
                    ['Increase significance level to 0.10', false],
                    ['Apply Bonferroni correction for number of clusters', false],
                ],
            ],
            [
                'q' => 'PROPENSITY SCORE MATCHING is used in observational studies to:',
                'opts' => [
                    ['Test whether two groups have different means', false],
                    ['Balance observed confounders between treatment and control groups, approximating randomization to support causal inference', true],
                    ['Compute the marginal likelihood of the model', false],
                    ['Replace missing data with estimated values', false],
                ],
            ],

            // ── ADVANCED REGRESSION & MODELING ───────────────────────────
            [
                'q' => 'In a logistic regression model, the coefficient for predictor x₁ is β = 0.693. What does this represent?',
                'opts' => [
                    ['The probability increases by 0.693 for each unit increase in x₁', false],
                    ['The log-odds of the outcome increase by 0.693 per unit increase in x₁, corresponding to an odds ratio of e^0.693 ≈ 2.0', true],
                    ['The probability doubles for each unit increase in x₁', false],
                    ['The R² increases by 0.693', false],
                ],
            ],
            [
                'q' => 'You fit a mixed-effects model: lmer(y ~ time + treatment + (1|subject)). What does the (1|subject) term represent?',
                'opts' => [
                    ['A fixed effect for each subject', false],
                    ['A random intercept per subject — accounts for baseline differences between subjects (repeated measures correlation)', true],
                    ['An interaction between time and subject', false],
                    ['A polynomial term for subject ID', false],
                ],
            ],
            [
                'q' => 'In survival analysis, the COX PROPORTIONAL HAZARDS model assumes:',
                'opts' => [
                    ['Survival times are normally distributed', false],
                    ['The hazard ratio between groups is constant over time (proportional hazards assumption)', true],
                    ['All subjects experience the event', false],
                    ['Censoring never occurs', false],
                ],
            ],
            [
                'q' => 'LASSO regression (L1 regularization) differs from Ridge (L2) in that:',
                'opts' => [
                    ['LASSO always produces larger coefficients', false],
                    ['LASSO can shrink coefficients exactly to zero, performing automatic feature selection; Ridge only shrinks toward zero', true],
                    ['Ridge produces sparse solutions', false],
                    ['LASSO maximizes R²', false],
                ],
            ],
            [
                'q' => 'Elastic Net regression combines L1 and L2 penalties. It is preferred over pure LASSO when:',
                'opts' => [
                    ['The dataset is small', false],
                    ['There are groups of highly correlated predictors — LASSO arbitrarily picks one; Elastic Net can include the whole group', true],
                    ['All predictors are uncorrelated', false],
                    ['The outcome is binary', false],
                ],
            ],

            // ── PERFORMANCE & SCALABILITY CONSIDERATIONS ──────────────────
            [
                'q' => 'You need to run pairwise t-tests across 500 features in a dataset. What is the computational and statistical concern?',
                'opts' => [
                    ['Only computational: O(n²) comparisons are slow', false],
                    ['Both: O(n²) = 124,750 tests create massive multiple comparisons inflation (FDR control essential) AND is computationally expensive — vectorized operations or dimension reduction (PCA) should be considered first', true],
                    ['Only statistical: p-values cannot be computed for more than 100 features', false],
                    ['No concern — modern computers handle this trivially', false],
                ],
            ],
            [
                'q' => 'For a streaming data pipeline that must update statistical summaries (mean, variance) online without storing all data, which algorithm is appropriate?',
                'opts' => [
                    ['Collect all data then compute batch statistics', false],
                    ['Welford\'s online algorithm — computes running mean and variance in O(1) per update with O(1) memory, numerically stable', true],
                    ['Re-read the full dataset on each update', false],
                    ['Use only the last 10 observations', false],
                ],
            ],
            [
                'q' => 'You are computing bootstrap confidence intervals for a complex statistic on a dataset with n = 1,000,000. Standard bootstrap with B = 10,000 resamples is too slow. What is the best solution?',
                'opts' => [
                    ['Reduce B to 10 resamples', false],
                    ['Use the bag of little bootstraps (BLB) — split data into subsets, bootstrap within subsets, aggregate — provides statistical guarantees with drastically reduced computation', true],
                    ['Use only 1,000 observations', false],
                    ['Switch to a parametric CI assuming normality without checking', false],
                ],
            ],
            [
                'q' => 'In a large-scale A/B test platform, NETWORK EFFECTS (or interference) threaten validity when:',
                'opts' => [
                    ['The sample is too large', false],
                    ['Users in control and treatment groups interact with each other (e.g., social networks), causing treatment to spill over to controls and biasing estimated effects', true],
                    ['The experiment runs for too long', false],
                    ['Multiple metrics are measured simultaneously', false],
                ],
            ],
            [
                'q' => 'For a high-dimensional dataset (p >> n), ordinary least squares regression:',
                'opts' => [
                    ['Works well because more features improve fit', false],
                    ['Is ill-posed — the system is underdetermined, producing infinite solutions or catastrophic overfitting. Regularization (LASSO, Ridge) or dimensionality reduction is required', true],
                    ['Automatically selects the most important features', false],
                    ['Produces exactly the same result as Ridge regression', false],
                ],
            ],

            // ── PROFESSIONAL INTERPRETATION & REPORTING ───────────────────
            [
                'q' => 'A clinical study reports: "The new drug reduced systolic blood pressure by 2 mmHg (95% CI: [0.5, 3.5], p = 0.008)." A colleague says "the effect is significant so the drug should be prescribed." What is the professional statistical counterargument?',
                'opts' => [
                    ['The p-value should be 0.05 not 0.008', false],
                    ['A 2 mmHg reduction is statistically significant but may be clinically meaningless. The confidence interval includes values as small as 0.5 mmHg. Clinical significance, side effect profiles, and cost must be weighed alongside statistical significance', true],
                    ['Confidence intervals are unnecessary since p < 0.05', false],
                    ['The study must be replicated with larger α', false],
                ],
            ],
            [
                'q' => 'A report states: "We found no significant difference (p = 0.32), therefore the two treatments are equivalent." What is wrong with this conclusion?',
                'opts' => [
                    ['Nothing — p > 0.05 proves the null hypothesis', false],
                    ['Absence of evidence is not evidence of absence. Failing to reject H₀ does not prove equivalence — the study may have been underpowered. Equivalence testing (TOST procedure) with pre-specified margins is required', true],
                    ['p = 0.32 means there is a 32% chance of an effect', false],
                    ['Equivalence can only be claimed when p > 0.50', false],
                ],
            ],
            [
                'q' => 'You are reviewing a published paper that reports 47 outcomes but only highlights the 3 that reached p < 0.05. What is the methodological problem?',
                'opts' => [
                    ['3 significant outcomes out of 47 is a very high success rate', false],
                    ['Outcome reporting bias / selective reporting — with 47 tests at α=0.05, ~2-3 false positives are expected by chance. Pre-registration and reporting all outcomes is required', true],
                    ['The paper should have used α = 0.001', false],
                    ['No problem — researchers always report the most important findings', false],
                ],
            ],
            [
                'q' => 'The REPLICATION CRISIS in science is largely attributed to which combination of factors?',
                'opts' => [
                    ['Using ANOVA instead of regression', false],
                    ['Underpowered studies, p-hacking, publication bias toward positive results, HARKing (Hypothesizing After Results are Known), and lack of pre-registration', true],
                    ['Using frequentist instead of Bayesian statistics exclusively', false],
                    ['Too many replications being conducted', false],
                ],
            ],
            [
                'q' => 'HARKING (Hypothesizing After Results are Known) is problematic because:',
                'opts' => [
                    ['It makes papers shorter', false],
                    ['It presents post-hoc hypotheses as a priori predictions, inflating Type I error and creating an illusion of confirmatory evidence where only exploratory analysis occurred', true],
                    ['It is a violation of copyright', false],
                    ['It reduces statistical power', false],
                ],
            ],

            // ── ADVANCED BAYESIAN & MODERN METHODS ───────────────────────
            [
                'q' => 'A Bayesian model has a DIFFUSE (non-informative) prior. In large samples, the posterior distribution will:',
                'opts' => [
                    ['Always differ significantly from the frequentist result', false],
                    ['Converge toward the frequentist MLE result — the likelihood dominates and the prior has negligible influence', true],
                    ['Be identical to the prior distribution', false],
                    ['Have higher variance than with an informative prior', false],
                ],
            ],
            [
                'q' => 'In Bayesian hypothesis testing, the BAYES FACTOR (BF₁₀) = 15 means:',
                'opts' => [
                    ['The null hypothesis is 15 times more likely', false],
                    ['The data are 15 times more likely under H₁ than under H₀ — considered strong evidence for H₁', true],
                    ['There is a 15% probability that H₁ is true', false],
                    ['The p-value is 1/15', false],
                ],
            ],
            [
                'q' => 'MCMC (Markov Chain Monte Carlo) is used in Bayesian statistics because:',
                'opts' => [
                    ['It is faster than analytical solutions in all cases', false],
                    ['Many posterior distributions cannot be computed analytically; MCMC generates samples from the posterior numerically even for complex, high-dimensional models', true],
                    ['It eliminates the need for a prior distribution', false],
                    ['It converts Bayesian inference into frequentist inference', false],
                ],
            ],
            [
                'q' => 'A DIRECTED ACYCLIC GRAPH (DAG) in causal inference is used to:',
                'opts' => [
                    ['Visualize p-value distributions', false],
                    ['Encode causal assumptions, identify confounders, mediators, and colliders, and determine the minimal adjustment set required for unbiased causal effect estimation', true],
                    ['Run ANOVA post-hoc tests', false],
                    ['Compute partial correlations only', false],
                ],
            ],
            [
                'q' => 'Adjusting for a COLLIDER variable in regression:',
                'opts' => [
                    ['Always reduces confounding bias', false],
                    ['INTRODUCES bias — collider adjustment opens a spurious path between variables that were otherwise unrelated (collider stratification bias)', true],
                    ['Is always required for valid causal inference', false],
                    ['Has no effect on the regression estimates', false],
                ],
            ],
            [
                'q' => 'In a DIFFERENCE-IN-DIFFERENCES (DiD) design, the key identifying assumption is:',
                'opts' => [
                    ['The treatment group is randomly selected', false],
                    ['Parallel trends — in the absence of treatment, the treatment and control groups would have followed the same trend over time', true],
                    ['The control group never changes over time', false],
                    ['Treatment and control are always measured at the same time', false],
                ],
            ],
            [
                'q' => 'INSTRUMENTAL VARIABLES (IV) estimation is used when:',
                'opts' => [
                    ['All confounders are measured and can be adjusted for', false],
                    ['There is unmeasured confounding — an instrument affects treatment but affects outcome ONLY through treatment, allowing causal effect estimation despite unobserved confounders', true],
                    ['The outcome variable is binary', false],
                    ['The sample size is too small for regression', false],
                ],
            ],
            [
                'q' => 'REGRESSION DISCONTINUITY DESIGN (RDD) exploits:',
                'opts' => [
                    ['Random assignment to treatment and control groups', false],
                    ['A known threshold or cutoff for treatment assignment — units just above and below the cutoff are assumed comparable, enabling local causal inference', true],
                    ['Repeated measurements of the same subjects', false],
                    ['Instrumental variables to adjust for selection bias', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 8 — Statistical Methods & Experimental Design (Professional).");
    }
}