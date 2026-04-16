<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module2ChallengeSeederProfessional extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'professional')->first();

        if (!$category) {
            $this->command->error("Professional category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 2 — Basics of Statistics (Professional)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Statistics',
            'description'           => 'Solve real-world statistical challenges as encountered in industry: causal inference, production-grade A/B testing, Bayesian workflows, survival analysis, robust estimation, and statistical system design.',
            'time_limit_seconds'    => 2100,
            'base_xp'               => 1500,
            'order_index'           => 2,
        ]);

        $this->command->info("Seeding 50 professional-level statistics questions...");

        $qaData = [

            // ── CAUSAL INFERENCE ──────────────────────────────────────────
            [
                'q' => "Your company runs an A/B test. The treatment group saw the feature by default, but some users in the control group actively sought it out and enabled it.\nThis violates which A/B testing assumption?",
                'opts' => [
                    ['SUTVA — Stable Unit Treatment Value Assumption (no spillover between units)', false],
                    ['Exclusion restriction — treatment assignment must affect outcome ONLY through the treatment itself; self-selection contaminates the control group', true],
                    ['Randomization — the groups were not randomly assigned', false],
                    ['Sample size sufficiency — the test should have been larger', false],
                ],
            ],
            [
                'q' => "You observe a strong correlation between feature usage and retention.\nYou want to estimate the causal effect. Which method is most appropriate given you cannot run a true RCT?",
                'opts' => [
                    ['Run a Pearson correlation with p < 0.05 to confirm causality', false],
                    ['Use an Instrumental Variable (IV) or Regression Discontinuity (RDD) design to isolate exogenous variation', true],
                    ['Compute the difference in means and report it as a causal estimate', false],
                    ['Build a regression model and interpret coefficients as causal effects', false],
                ],
            ],
            [
                'q' => "In Difference-in-Differences (DiD) causal estimation, the key identifying assumption is:\n\nTreatment group: trend before treatment observed\nControl group: parallel trend observed",
                'opts' => [
                    ['The treatment and control groups had identical pre-treatment means', false],
                    ['In the absence of treatment, the two groups would have followed parallel trends', true],
                    ['The treatment was assigned randomly to all units', false],
                    ['Post-treatment outcomes are identical across groups', false],
                ],
            ],
            [
                'q' => "What is a confounding variable, and why is it dangerous in observational studies?",
                'opts' => [
                    ['A variable that is perfectly collinear with the predictor — it causes numerical instability', false],
                    ['A variable causally related to both the treatment and the outcome — it creates spurious associations and biases causal estimates', true],
                    ['A variable with very high variance that dominates the regression', false],
                    ['An outcome variable measured with error', false],
                ],
            ],

            // ── PRODUCTION A/B TESTING ────────────────────────────────────
            [
                'q' => "Your A/B test platform automatically stops tests when p < 0.05 is first detected (\"peeking\").\nWhat statistical problem does this cause?",
                'opts' => [
                    ['It reduces statistical power significantly', false],
                    ['It inflates the Type I error rate — repeatedly testing until significance is found guarantees false positives over time', true],
                    ['It makes the test underpowered by cutting it short', false],
                    ['It violates the assumption of equal sample sizes', false],
                ],
            ],
            [
                'q' => "Sequential testing methods like the Sequential Probability Ratio Test (SPRT) or always-valid p-values solve the peeking problem by:",
                'opts' => [
                    ['Disabling continuous monitoring entirely', false],
                    ['Providing error guarantees that remain valid regardless of when you stop the test', true],
                    ['Doubling the sample size to compensate for multiple looks', false],
                    ['Using Bonferroni correction on every look', false],
                ],
            ],
            [
                'q' => "An A/B test on a checkout page shows:\nControl: n=5000, conversion=8.0%\nTreatment: n=5000, conversion=8.4%\np = 0.08, power = 0.45\n\nA PM says 'p > 0.05 so ship the control.' What is the correct concern?",
                'opts' => [
                    ['p = 0.08 is close enough to 0.05 to ship the treatment', false],
                    ['Power = 0.45 means the test was severely underpowered — the test was unable to reliably detect the true effect; more data is needed', true],
                    ['The control should be shipped since conversion is lower', false],
                    ['p > 0.05 definitively proves there is no difference', false],
                ],
            ],
            [
                'q' => "You are running 50 simultaneous A/B experiments on your platform.\nWhich multiple testing correction is preferred over Bonferroni in this context, and why?",
                'opts' => [
                    ['Bonferroni — because it is the most conservative and eliminates all false positives', false],
                    ['Benjamini-Hochberg (BH) FDR control — it is less conservative, maintains higher power, and controls the expected proportion of false discoveries rather than eliminating all of them', true],
                    ['No correction is needed — each test is independent', false],
                    ['Bonferroni adjusted by √n to account for sample size', false],
                ],
            ],

            // ── BAYESIAN A/B TESTING ──────────────────────────────────────
            [
                'q' => "In Bayesian A/B testing with Beta priors:\nP(conversion) ~ Beta(α, β)\nAfter observing 40 conversions and 160 non-conversions, what are the posterior parameters?",
                'opts' => [
                    ['Beta(40, 160)', false],
                    ['Beta(α + 40, β + 160)', true],
                    ['Beta(α × 40, β × 160)', false],
                    ['Beta(40/200, 160/200)', false],
                ],
            ],
            [
                'q' => "A Bayesian A/B test reports: P(B > A) = 0.94.\nWhat is the correct business interpretation?",
                'opts' => [
                    ['B is 94% better than A in absolute terms', false],
                    ['Given the data and prior, there is a 94% probability that variant B has a higher true conversion rate than A', true],
                    ['The p-value equivalent is 0.06, so the result is not significant at α = 0.05', false],
                    ['94% of users preferred variant B', false],
                ],
            ],

            // ── SURVIVAL ANALYSIS ─────────────────────────────────────────
            [
                'q' => "What does the Kaplan-Meier estimator measure?\n\nfrom lifelines import KaplanMeierFitter\nkmf = KaplanMeierFitter()\nkmf.fit(durations=T, event_observed=E)\nkmf.plot_survival_function()",
                'opts' => [
                    ['The probability that a subject is still alive (event has not occurred) at each time point', true],
                    ['The hazard rate at each time point', false],
                    ['The expected time until the event for the average subject', false],
                    ['The cumulative distribution function of event times', false],
                ],
            ],
            [
                'q' => 'What is "censoring" in survival analysis?',
                'opts' => [
                    ['Removing extreme outliers from the time-to-event data', false],
                    ['When a subject leaves the study or the study ends before the event is observed — their exact event time is unknown but bounded', true],
                    ['When survival time is transformed using log scaling', false],
                    ['When two groups have non-parallel survival curves', false],
                ],
            ],
            [
                'q' => 'The Cox Proportional Hazards model assumes:',
                'opts' => [
                    ['The baseline hazard function is parametrically specified as exponential', false],
                    ['The hazard ratio between two subjects is constant over time (proportional hazards)', true],
                    ['All subjects must have the same baseline hazard', false],
                    ['Survival times are normally distributed', false],
                ],
            ],

            // ── ROBUST ESTIMATION ─────────────────────────────────────────
            [
                'q' => "You have a dataset of customer revenue with extreme right-skew and heavy outliers.\nWhich estimator is most ROBUST for measuring central tendency in a production metric?",
                'opts' => [
                    ['Arithmetic mean — it uses all the data', false],
                    ['Trimmed mean or median — they are resistant to extreme outliers and better represent the typical customer', true],
                    ['Maximum likelihood estimator of a normal distribution', false],
                    ['Mode — the most common revenue value', false],
                ],
            ],
            [
                'q' => "In production monitoring, why is the median a better SLA metric than the mean for API latency?",
                'opts' => [
                    ['The median is always smaller than the mean', false],
                    ['The mean can be severely inflated by rare but extreme latency spikes (timeouts, cold starts), masking the experience of typical users', true],
                    ['The median requires less computation on streaming data', false],
                    ['The mean is only valid for normally distributed data', false],
                ],
            ],
            [
                'q' => "You are reporting p95 latency. A deployment causes p95 to jump from 120ms to 280ms, but p50 (median) is unchanged at 45ms.\nWhat is the most likely explanation?",
                'opts' => [
                    ['The deployment improved performance for most users', false],
                    ['A small percentage of requests became significantly slower (tail latency regression), while typical requests are unaffected', true],
                    ['The monitoring tool has a bug — p95 and p50 cannot diverge', false],
                    ['The sample size for p95 is too small to be reliable', false],
                ],
            ],

            // ── STATISTICAL SYSTEM DESIGN ─────────────────────────────────
            [
                'q' => "You are designing an experimentation platform. A team wants to run A/B tests where the unit of randomization is the user session rather than the user_id.\nWhat problem does this introduce?",
                'opts' => [
                    ['Session-level randomization is always more statistically efficient', false],
                    ['A single user can be in both control and treatment in different sessions — violating unit independence and inflating variance (network effects / carryover)', true],
                    ['Session-level randomization requires a larger total sample', false],
                    ['It prevents the use of any parametric test', false],
                ],
            ],
            [
                'q' => "CUPED (Controlled-experiment Using Pre-Experiment Data) reduces variance in A/B tests by:",
                'opts' => [
                    ['Removing users who did not engage with the feature', false],
                    ['Using pre-experiment covariates to reduce within-group variance of the metric, increasing statistical power without increasing sample size', true],
                    ['Running the test for a longer period to collect more data', false],
                    ['Splitting the sample into more granular strata', false],
                ],
            ],
            [
                'q' => "What is the Novelty Effect in A/B testing and how do you mitigate it?",
                'opts' => [
                    ['Users are more likely to click new features simply because they are new — artificially inflating early results; mitigate by running tests long enough to observe stable behavior', true],
                    ['New features always perform worse initially due to user unfamiliarity', false],
                    ['The novelty effect only applies to UI changes, not algorithm changes', false],
                    ['Running longer tests always eliminates novelty effects', false],
                ],
            ],

            // ── HIERARCHICAL / MULTILEVEL MODELS ─────────────────────────
            [
                'q' => "You are modeling student test scores. Students are nested within classrooms, which are nested within schools.\nWhy is OLS regression inappropriate here?",
                'opts' => [
                    ['OLS cannot handle more than 2 predictors', false],
                    ['Students within the same classroom are not independent — violating OLS\'s independence assumption; a hierarchical mixed-effects model is required', true],
                    ['OLS requires a normally distributed dependent variable with no exceptions', false],
                    ['OLS is perfectly valid; nesting can be handled with dummy variables', false],
                ],
            ],
            [
                'q' => 'In a mixed-effects model, "random effects" are used to:',
                'opts' => [
                    ['Handle heteroscedasticity in residuals', false],
                    ['Account for variability that comes from grouping structure (e.g., different baselines per cluster), which the fixed effects cannot capture', true],
                    ['Remove outliers from the regression', false],
                    ['Replace interaction terms when data is sparse', false],
                ],
            ],

            // ── BAYESIAN INFERENCE DEPTH ──────────────────────────────────
            [
                'q' => "MCMC (Markov Chain Monte Carlo) methods like Metropolis-Hastings are used in Bayesian statistics because:",
                'opts' => [
                    ['They are faster than analytical solutions', false],
                    ['The posterior distribution is often analytically intractable; MCMC samples from it by constructing a Markov chain with the posterior as its stationary distribution', true],
                    ['They guarantee globally optimal parameter estimates', false],
                    ['They convert Bayesian problems into frequentist ones', false],
                ],
            ],
            [
                'q' => "In a Bayesian model, you choose a very informative prior that strongly contradicts the data.\nWhat effect will this have on the posterior with a moderate-sized dataset?",
                'opts' => [
                    ['The posterior will always match the likelihood (data wins)', false],
                    ['The posterior will be a compromise between the prior and the likelihood, but may be biased toward the prior with small-to-moderate data', true],
                    ['The posterior will exactly equal the prior', false],
                    ['The model will fail to converge', false],
                ],
            ],

            // ── DIMENSIONALITY & MULTIVARIATE STATS ──────────────────────
            [
                'q' => "PCA (Principal Component Analysis) is a dimensionality reduction technique that:\n\nfrom sklearn.decomposition import PCA\npca = PCA(n_components=2)\nX_reduced = pca.fit_transform(X_scaled)",
                'opts' => [
                    ['Selects the two features with the highest variance', false],
                    ['Finds orthogonal linear combinations of features (principal components) that maximize explained variance', true],
                    ['Clusters data into 2 groups using unsupervised learning', false],
                    ['Reduces dimensionality by removing correlated features entirely', false],
                ],
            ],
            [
                'q' => "What is the curse of dimensionality and its statistical consequence?",
                'opts' => [
                    ['Models with many features always overfit — solved by removing features randomly', false],
                    ['As dimensionality grows, data becomes increasingly sparse, distances become less meaningful, and more data is needed exponentially to maintain the same statistical power', true],
                    ['High-dimensional data always violates normality assumptions', false],
                    ['Correlation matrices become singular only for datasets with more features than observations', false],
                ],
            ],

            // ── REAL-WORLD METRIC DESIGN ──────────────────────────────────
            [
                'q' => "Your team defines success for an A/B test as: 'DAU increases by 2%'.\nA statistician warns this is a 'ratio metric.' Why is this problematic?",
                'opts' => [
                    ['Percentages cannot be used as A/B test metrics', false],
                    ['Ratio metrics (numerator/denominator) have higher variance and can be biased if the denominator changes between variants; delta method or bootstrapping is needed for correct standard errors', true],
                    ['DAU is a lagging indicator and cannot be tested in real time', false],
                    ['Ratio metrics always require log transformation before testing', false],
                ],
            ],
            [
                'q' => "You observe that your experiment's primary metric (revenue/user) improved significantly, but a guardrail metric (customer support tickets) also increased significantly.\nWhat is the correct product decision?",
                'opts' => [
                    ['Ship the feature — revenue improvement outweighs support cost', false],
                    ['Do not ship without investigation — a guardrail violation indicates the change may be causing harm that offsets the revenue gain', true],
                    ['Run a longer test to see if the guardrail metric normalizes', false],
                    ['Ignore guardrail metrics unless specified by legal', false],
                ],
            ],

            // ── ADVANCED DISTRIBUTIONS & EDGE CASES ──────────────────────
            [
                'q' => "You are modeling the number of server failures per day. The distribution is:\n- Events are rare and independent\n- Rate is constant over time\n\nWhich distribution is most appropriate?",
                'opts' => [
                    ['Normal distribution', false],
                    ['Binomial distribution', false],
                    ['Poisson distribution', true],
                    ['Uniform distribution', false],
                ],
            ],
            [
                'q' => "A Poisson process has λ = 3 events per hour.\nWhat is the probability of observing exactly 5 events in one hour?\nFormula: P(X=k) = (λᵏ × e⁻λ) / k!",
                'opts' => [
                    ['~0.050', false],
                    ['~0.101', true],
                    ['~0.168', false],
                    ['~0.200', false],
                ],
            ],
            [
                'q' => "You are modeling time between customer purchases. Times are always positive and right-skewed.\nWhich distribution family is most appropriate as a starting point?",
                'opts' => [
                    ['Normal — symmetric and well-understood', false],
                    ['Gamma or Weibull — they model positive, right-skewed continuous data and have flexible shape parameters', true],
                    ['Binomial — because purchase is a binary event', false],
                    ['Uniform — no information about the shape is available', false],
                ],
            ],

            // ── PRODUCTION MONITORING & DRIFT ─────────────────────────────
            [
                'q' => "Your model's predictions are drifting over time. You use the Kolmogorov-Smirnov (KS) test to monitor:\n\nfrom scipy.stats import ks_2samp\nstat, p = ks_2samp(training_scores, production_scores)\nprint(p)",
                'opts' => [
                    ['Whether the model\'s accuracy is declining', false],
                    ['Whether the distribution of prediction scores has shifted significantly between training and production', true],
                    ['Whether features have become correlated over time', false],
                    ['Whether the model is overfitting on new data', false],
                ],
            ],
            [
                'q' => "Population Stability Index (PSI) is used in production ML to:\n\nPSI = Σ (Actual% - Expected%) × ln(Actual% / Expected%)",
                'opts' => [
                    ['Measure model accuracy on new data', false],
                    ['Quantify how much the distribution of a variable has shifted between two time periods (training vs. production)', true],
                    ['Compute the KL divergence of predictions vs. actuals', false],
                    ['Track the stability of model coefficients over time', false],
                ],
            ],
            [
                'q' => "PSI < 0.1 means:\nPSI 0.1–0.2 means:\nPSI > 0.2 means:",
                'opts' => [
                    ['Stable / Minor shift / Significant shift — no action needed / monitor / retrain required', true],
                    ['Significant shift / Stable / Minor shift', false],
                    ['Overfit / Underfit / Well-fit', false],
                    ['High accuracy / Medium accuracy / Low accuracy', false],
                ],
            ],

            // ── STATISTICAL ETHICS & REPRODUCIBILITY ─────────────────────
            [
                'q' => "What is p-hacking, and why does it undermine scientific validity?",
                'opts' => [
                    ['Setting α = 0.01 instead of 0.05 to be more rigorous', false],
                    ['Selectively reporting analyses, outcomes, or subgroups until p < 0.05 is found — inflating the false positive rate and producing non-reproducible findings', true],
                    ['Using p-values for binary decisions instead of effect sizes', false],
                    ['Running underpowered studies that fail to find real effects', false],
                ],
            ],
            [
                'q' => "What is the Replication Crisis in science, and what practices address it?",
                'opts' => [
                    ['Many published findings fail to replicate in independent studies — addressed by pre-registration, larger samples, open data, and reporting effect sizes with confidence intervals rather than just p-values', true],
                    ['Old statistical methods are being replaced by modern machine learning', false],
                    ['Statistical software produces inconsistent results across platforms', false],
                    ['Too many papers are being published without peer review', false],
                ],
            ],
            [
                'q' => "You are presenting an A/B test result: p = 0.049, lift = +0.3%, 95% CI = [+0.01%, +0.59%].\nA rigorous interpretation to give leadership is:",
                'opts' => [
                    ['The test is significant — we proved the feature improves conversion by 0.3%', false],
                    ['The result is statistically significant but the effect is small and the CI is wide; practical significance and business impact should be evaluated alongside the statistical evidence before shipping', true],
                    ['Ship immediately — any p < 0.05 result is worth implementing', false],
                    ['The CI includes near-zero values so we should reject the result', false],
                ],
            ],

            // ── ADVANCED CALCULATION GAUNTLETS ───────────────────────────
            [
                'q' => "Two independent samples:\nGroup A: n=100, x̄=72, s=10\nGroup B: n=100, x̄=68, s=12\n\nCompute the pooled standard error:\nSE = √(s₁²/n₁ + s₂²/n₂)\n\nWhat is SE?",
                'opts' => [
                    ['1.28', false],
                    ['1.56', true],
                    ['2.20', false],
                    ['11.00', false],
                ],
            ],
            [
                'q' => "Using the above (x̄_A - x̄_B = 4, SE = 1.56), compute the t-statistic:",
                'opts' => [
                    ['1.92', false],
                    ['2.26', false],
                    ['2.56', true],
                    ['3.00', false],
                ],
            ],
            [
                'q' => "A logistic regression model outputs: log-odds = 0.5 + 1.2 × (feature).\nFor feature = 1, what is the predicted probability?\nFormula: p = 1 / (1 + e^(-log_odds))\n(Use: e^1.7 ≈ 5.474)",
                'opts' => [
                    ['0.65', false],
                    ['0.72', false],
                    ['0.85', true],
                    ['0.92', false],
                ],
            ],
            [
                'q' => "You train a model with features X1 and X2. Both are standardized (mean=0, SD=1).\nCoefficients: β1 = 3.2, β2 = 1.1.\n\nA team member says 'X1 is 3× more important than X2 because 3.2/1.1 ≈ 3'.\nIs this interpretation valid, and why?",
                'opts' => [
                    ['Yes — standardized coefficients directly represent variable importance', true],
                    ['No — coefficients are on different scales and cannot be compared', false],
                    ['No — only tree-based feature importance is valid for this comparison', false],
                    ['Yes, but only for linear models with independent features', false],
                ],
            ],
            [
                'q' => "A Poisson regression models count data. The coefficient for predictor X is β = 0.3.\nWhat is the multiplicative effect on the expected count for a 1-unit increase in X?",
                'opts' => [
                    ['0.3 (additive increase)', false],
                    ['1.35 (e^0.3 ≈ 1.35 — a 35% increase in expected count)', true],
                    ['0.74 (e^-0.3)', false],
                    ['0.3% increase', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 2 — Basics of Statistics (Professional).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Professional");
    }
}