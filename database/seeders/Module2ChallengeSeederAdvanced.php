<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module2ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 2 — Basics of Statistics (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Statistics',
            'description'           => 'Tackle advanced statistical reasoning through code debugging, deep conceptual analysis, and edge-case problems. Covers Bayesian inference, ANOVA, MLE, bootstrapping, and model diagnostics.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 1100,
            'order_index'           => 2,
        ]);

        $this->command->info("Seeding 50 advanced-level statistics questions...");

        $qaData = [

            // ── DEBUGGING DESCRIPTIVE STATS ───────────────────────────────
            [
                'q' => "Find the bug:\n\ndata = [4, 8, 15, 16, 23, 42]\nmean = sum(data) / len(data) - 1\nprint(mean)",
                'opts' => [
                    ['sum() should be product()', false],
                    ['`- 1` is incorrectly subtracted from the mean, not used for sample correction', true],
                    ['len(data) should be len(data) + 1', false],
                    ['data should be sorted first', false],
                ],
            ],
            [
                'q' => "What is wrong here for computing the SAMPLE standard deviation?\n\nimport math\ndata = [2, 4, 4, 4, 5, 5, 7, 9]\nmean = sum(data) / len(data)\nvariance = sum((x - mean)**2 for x in data) / len(data)\nstd_dev = math.sqrt(variance)",
                'opts' => [
                    ['math.sqrt should be math.pow', false],
                    ['The divisor should be len(data) - 1 for sample standard deviation (Bessel\'s correction)', true],
                    ['The mean formula is wrong', false],
                    ['The exponent should be 3 not 2', false],
                ],
            ],
            [
                'q' => "What will this print, and why?\n\nimport statistics\ndata = [1, 1, 2, 3, 3]\nprint(statistics.multimode(data))",
                'opts' => [
                    ['[1] — only the first mode is returned', false],
                    ['[1, 3] — both modes are returned since multimode() returns all modes', true],
                    ['3 — the last mode alphabetically', false],
                    ['An error — multimode() does not exist', false],
                ],
            ],
            [
                'q' => "This code is meant to detect outliers. What is the logical bug?\n\nimport numpy as np\ndata = np.array([10, 12, 11, 13, 100, 11, 12])\nmean = np.mean(data)\nstd = np.std(data)\noutliers = data[abs(data - mean) > 2]\nprint(outliers)",
                'opts' => [
                    ['np.std should be np.var', false],
                    ['The threshold should be `2 * std`, not just `2` — currently filtering by raw value, not standard deviations', true],
                    ['abs() is not valid for numpy arrays', false],
                    ['data should be sorted before filtering', false],
                ],
            ],

            // ── MAXIMUM LIKELIHOOD ESTIMATION ────────────────────────────
            [
                'q' => 'What does Maximum Likelihood Estimation (MLE) do?',
                'opts' => [
                    ['Minimizes the sum of squared residuals', false],
                    ['Finds the parameter values that maximize the probability of observing the given data', true],
                    ['Finds the parameter values that minimize the bias of an estimator', false],
                    ['Maximizes the R² of a regression model', false],
                ],
            ],
            [
                'q' => "For a normal distribution, the MLE estimate of the mean μ is:\n\nX = [3, 5, 7, 9, 11]\nWhat is the MLE estimate of μ?",
                'opts' => [
                    ['5', false],
                    ['7', true],
                    ['9', false],
                    ['6', false],
                ],
            ],
            [
                'q' => 'The MLE of variance for a normal distribution divides by N (not N-1). This makes the MLE variance estimator:',
                'opts' => [
                    ['Unbiased', false],
                    ['Biased (it systematically underestimates the true population variance)', true],
                    ['Consistent but not efficient', false],
                    ['Invalid for large samples', false],
                ],
            ],

            // ── BAYESIAN STATISTICS ───────────────────────────────────────
            [
                'q' => "Bayes' Theorem: P(A|B) = P(B|A) × P(A) / P(B)\n\nA disease affects 1% of the population. A test is 99% sensitive (P(positive|disease)=0.99) and 95% specific (P(negative|no disease)=0.95).\nGiven a positive test, what is approximately the probability you actually have the disease?",
                'opts' => [
                    ['99%', false],
                    ['95%', false],
                    ['50%', false],
                    ['~17%', true],
                ],
            ],
            [
                'q' => 'In Bayesian statistics, what is the "prior"?',
                'opts' => [
                    ['The probability calculated after seeing the data', false],
                    ['Your belief about a parameter BEFORE observing any data', true],
                    ['The likelihood of the observed data given the parameters', false],
                    ['The normalizing constant in Bayes\' theorem', false],
                ],
            ],
            [
                'q' => 'After updating the prior with observed data, the result is called the:',
                'opts' => [
                    ['Likelihood', false],
                    ['Prior predictive distribution', false],
                    ['Posterior distribution', true],
                    ['Marginal distribution', false],
                ],
            ],
            [
                'q' => 'Which statement best contrasts Frequentist vs Bayesian inference?',
                'opts' => [
                    ['Frequentist uses prior knowledge; Bayesian does not', false],
                    ['Frequentist treats parameters as fixed unknowns; Bayesian treats parameters as distributions with uncertainty', true],
                    ['Bayesian methods are always less accurate than frequentist methods', false],
                    ['Frequentist methods always require larger sample sizes', false],
                ],
            ],

            // ── ANOVA ─────────────────────────────────────────────────────
            [
                'q' => 'One-Way ANOVA tests whether:',
                'opts' => [
                    ['Two specific groups differ significantly', false],
                    ['The means of THREE or more groups are all equal', true],
                    ['The variances of groups are equal', false],
                    ['Data is normally distributed', false],
                ],
            ],
            [
                'q' => "What does this ANOVA code test?\n\nfrom scipy.stats import f_oneway\ng1 = [20, 22, 19, 24, 21]\ng2 = [28, 30, 27, 29, 31]\ng3 = [35, 33, 36, 34, 37]\nF, p = f_oneway(g1, g2, g3)\nprint(F, p)",
                'opts' => [
                    ['Whether all three groups have the same variance', false],
                    ['Whether the means of g1, g2, and g3 are significantly different from each other', true],
                    ['Whether the data in each group is normally distributed', false],
                    ['The pairwise differences between all group pairs', false],
                ],
            ],
            [
                'q' => 'ANOVA produces a significant result (p < 0.05). What does this tell you?',
                'opts' => [
                    ['All group means are different from each other', false],
                    ['At least one group mean is significantly different — but not which one(s)', true],
                    ['Every pair of groups is significantly different', false],
                    ['The result proves causation between the groups', false],
                ],
            ],
            [
                'q' => 'After a significant ANOVA result, which test is used to find WHICH specific groups differ?',
                'opts' => [
                    ['Another ANOVA on each pair', false],
                    ['A post-hoc test such as Tukey\'s HSD', true],
                    ['A chi-square test', false],
                    ['A paired t-test on each pair (no correction needed)', false],
                ],
            ],

            // ── BOOTSTRAPPING ─────────────────────────────────────────────
            [
                'q' => 'What is bootstrapping in statistics?',
                'opts' => [
                    ['Training a neural network from scratch', false],
                    ['Resampling with replacement from the observed data to estimate the distribution of a statistic', true],
                    ['Removing outliers before running a regression', false],
                    ['Splitting data into training and test sets repeatedly', false],
                ],
            ],
            [
                'q' => "What does this bootstrapping code estimate?\n\nimport numpy as np\ndata = [3, 7, 5, 9, 6, 4, 8, 5, 7, 6]\nboot_means = []\nfor _ in range(10000):\n    sample = np.random.choice(data, size=len(data), replace=True)\n    boot_means.append(np.mean(sample))\nprint(np.percentile(boot_means, [2.5, 97.5]))",
                'opts' => [
                    ['The population variance', false],
                    ['A bootstrap 95% confidence interval for the mean', true],
                    ['The standard deviation of the data', false],
                    ['The probability distribution of the data', false],
                ],
            ],
            [
                'q' => 'What is the key advantage of bootstrapping over traditional parametric methods?',
                'opts' => [
                    ['It is always faster to compute', false],
                    ['It does not require assumptions about the population distribution', true],
                    ['It always produces narrower confidence intervals', false],
                    ['It eliminates sampling error entirely', false],
                ],
            ],

            // ── MODEL DIAGNOSTICS & REGRESSION DEPTH ─────────────────────
            [
                'q' => "What assumption is violated here?\n\nimport numpy as np\nimport matplotlib.pyplot as plt\nresiduals = [1, 2, 3, 4, 5, 6, 7, 8]  # residuals from regression\nplt.plot(residuals)\nplt.show()\n# The plot shows a clear upward trend in residuals",
                'opts' => [
                    ['Normality of residuals', false],
                    ['Homoscedasticity (equal variance) — residuals should show no pattern', true],
                    ['Independence of observations', false],
                    ['Linearity between X and y — the plot proves non-linearity', false],
                ],
            ],
            [
                'q' => "What does this code compute and what does a value near 2 indicate?\n\nfrom statsmodels.stats.stattools import durbin_watson\nresiduals = model.resid\ndw = durbin_watson(residuals)\nprint(dw)",
                'opts' => [
                    ['Heteroscedasticity — value near 2 means heteroscedastic residuals', false],
                    ['Autocorrelation — a DW value near 2 indicates no autocorrelation in residuals', true],
                    ['Normality of residuals — near 2 means normal', false],
                    ['Multicollinearity — near 2 means low VIF', false],
                ],
            ],
            [
                'q' => "A regression model has VIF = 18 for one predictor. What does this mean?\n\n(VIF = Variance Inflation Factor)",
                'opts' => [
                    ['The predictor is very important and should be kept', false],
                    ['The predictor is highly correlated with other predictors — severe multicollinearity', true],
                    ['The predictor has low variance and should be dropped', false],
                    ['VIF > 10 is always acceptable', false],
                ],
            ],
            [
                'q' => "Which regression assumption does this code check?\n\nimport scipy.stats as stats\nstats.probplot(residuals, dist='norm', plot=plt)\nplt.show()",
                'opts' => [
                    ['Linearity of the relationship between X and y', false],
                    ['Normality of residuals using a Q-Q plot', true],
                    ['Homoscedasticity of residuals', false],
                    ['Independence of predictors', false],
                ],
            ],

            // ── LOGISTIC REGRESSION ───────────────────────────────────────
            [
                'q' => 'What is the output of logistic regression?',
                'opts' => [
                    ['A continuous numerical value', false],
                    ['A probability between 0 and 1 (passed through a sigmoid function)', true],
                    ['A category label directly without any probability', false],
                    ['The coefficients of a linear function only', false],
                ],
            ],
            [
                'q' => "What does the coefficient β in logistic regression represent?\n\nlog(p / (1-p)) = β₀ + β₁x₁",
                'opts' => [
                    ['The change in probability for a unit increase in x', false],
                    ['The change in LOG-ODDS for a one-unit increase in x₁', true],
                    ['The slope of the decision boundary', false],
                    ['The percentage change in the target variable', false],
                ],
            ],
            [
                'q' => 'The log-loss (binary cross-entropy) loss function penalizes:',
                'opts' => [
                    ['Large coefficient values to prevent overfitting', false],
                    ['Confident wrong predictions more severely than uncertain wrong ones', true],
                    ['Predictions that are too close to 0.5', false],
                    ['Models with more than 10 features', false],
                ],
            ],

            // ── MULTIPLE HYPOTHESIS TESTING ───────────────────────────────
            [
                'q' => 'You run 20 independent hypothesis tests at α = 0.05. How many are expected to be false positives (Type I errors) just by chance?',
                'opts' => [
                    ['0', false],
                    ['1', true],
                    ['5', false],
                    ['20', false],
                ],
            ],
            [
                'q' => 'What does the Bonferroni correction do?',
                'opts' => [
                    ['Increases statistical power by pooling tests', false],
                    ['Divides the significance level α by the number of tests to control family-wise error rate', true],
                    ['Adjusts p-values upward to make them more significant', false],
                    ['Replaces t-tests with z-tests when n > 30', false],
                ],
            ],
            [
                'q' => "You run 10 tests. Bonferroni-corrected α = 0.05 / 10 = 0.005.\nA test returns p = 0.008. What is your conclusion?",
                'opts' => [
                    ['Reject H₀ — p < 0.05', false],
                    ['Fail to reject H₀ — p > Bonferroni threshold of 0.005', true],
                    ['The result is borderline significant', false],
                    ['Accept H₁ as proven', false],
                ],
            ],
            [
                'q' => 'The Benjamini-Hochberg (BH) procedure is preferred over Bonferroni when you want to:',
                'opts' => [
                    ['Eliminate all Type I errors', false],
                    ['Control the False Discovery Rate (FDR) rather than family-wise error — less conservative, more power', true],
                    ['Increase the significance level for all tests', false],
                    ['Run tests only on normally distributed data', false],
                ],
            ],

            // ── POWER ANALYSIS ────────────────────────────────────────────
            [
                'q' => 'Statistical power is defined as:',
                'opts' => [
                    ['The probability of making a Type I error', false],
                    ['1 - β, the probability of correctly rejecting a false null hypothesis', true],
                    ['The confidence level of a test', false],
                    ['The effect size divided by the standard error', false],
                ],
            ],
            [
                'q' => 'To increase the statistical power of a test, you can:',
                'opts' => [
                    ['Decrease the sample size', false],
                    ['Use a more stringent α (e.g., 0.01 instead of 0.05)', false],
                    ['Increase the sample size or the effect size', true],
                    ['Use a one-tailed test always (regardless of hypothesis)', false],
                ],
            ],
            [
                'q' => "What does this code compute?\n\nfrom statsmodels.stats.power import TTestIndPower\nanalysis = TTestIndPower()\nresult = analysis.solve_power(effect_size=0.5, alpha=0.05, power=0.8)\nprint(result)",
                'opts' => [
                    ['The p-value for a t-test with these parameters', false],
                    ['The required sample size per group to achieve 80% power', true],
                    ['The Type II error rate of the test', false],
                    ['The confidence interval width', false],
                ],
            ],

            // ── COVARIANCE & CORRELATION DEPTH ───────────────────────────
            [
                'q' => "What is the relationship between covariance and Pearson correlation?\n\nGiven: Cov(X,Y), σ_X, σ_Y",
                'opts' => [
                    ['r = Cov(X,Y) × σ_X × σ_Y', false],
                    ['r = Cov(X,Y) / (σ_X × σ_Y)', true],
                    ['r = σ_X × σ_Y / Cov(X,Y)', false],
                    ['r = Cov(X,Y) + σ_X + σ_Y', false],
                ],
            ],
            [
                'q' => "What bug exists in this covariance matrix code?\n\nimport numpy as np\nX = [1, 2, 3, 4, 5]\nY = [2, 4, 5, 4, 5]\ncov = np.cov(X, Y)\nprint(cov[0][0])  # Developer expects correlation of X with Y",
                'opts' => [
                    ['np.cov should be np.corrcoef — cov[0][0] gives the variance of X, not the correlation between X and Y', true],
                    ['cov[0][0] is the correct index for correlation between X and Y', false],
                    ['X and Y must have the same variance for this to work', false],
                    ['np.cov requires sorted arrays', false],
                ],
            ],

            // ── TIME SERIES BASICS ────────────────────────────────────────
            [
                'q' => 'What does "stationarity" mean in a time series?',
                'opts' => [
                    ['The data has no missing values', false],
                    ['The statistical properties (mean, variance) remain constant over time', true],
                    ['The data increases at a constant rate', false],
                    ['The autocorrelation is exactly 0 at all lags', false],
                ],
            ],
            [
                'q' => "What does the Augmented Dickey-Fuller (ADF) test check?\n\nfrom statsmodels.tsa.stattools import adfuller\nresult = adfuller(time_series_data)\nprint(result[1])  # p-value",
                'opts' => [
                    ['Whether the time series has seasonal patterns', false],
                    ['Whether the time series is stationary (null hypothesis: has a unit root / is non-stationary)', true],
                    ['The autocorrelation at lag 1', false],
                    ['Whether residuals are normally distributed', false],
                ],
            ],

            // ── EDGE CASES & DEEP REASONING ───────────────────────────────
            [
                'q' => "You compute Pearson r = 0.05 (p = 0.01) on a dataset of n = 10,000.\nWhat is the correct interpretation?",
                'opts' => [
                    ['Strong evidence of a strong relationship — p < 0.05 proves importance', false],
                    ['Statistically significant but practically negligible — large samples make tiny effects significant', true],
                    ['The correlation is not significant because r is near 0', false],
                    ['p = 0.01 means the correlation is 1% accurate', false],
                ],
            ],
            [
                'q' => "Jensen's Inequality states that for a convex function f:\nE[f(X)] ≥ f(E[X])\n\nThis explains why, for log-normal data:",
                'opts' => [
                    ['The mean equals the median', false],
                    ['The geometric mean is always less than or equal to the arithmetic mean', true],
                    ['The mode is always greater than the mean', false],
                    ['Variance is always greater than standard deviation', false],
                ],
            ],
            [
                'q' => "What is the problem with this simulation?\n\nimport numpy as np\nnp.random.seed(0)\nresults = []\nfor _ in range(1000):\n    sample = np.random.normal(0, 1, 5)  # n=5\n    results.append(np.mean(sample))\nprint(np.std(results))  # Developer expects this to be σ/√n = 1/√5 ≈ 0.447",
                'opts' => [
                    ['The code is correct — np.std(results) will equal exactly 0.447', false],
                    ['Nothing is wrong — this is a valid Monte Carlo simulation of the standard error, and the result should be close to 0.447 (with some Monte Carlo noise)', true],
                    ['np.random.seed() invalidates the simulation', false],
                    ['n=5 is too small for CLT to apply — the result will always be wrong', false],
                ],
            ],
            [
                'q' => "A/B testing: Group A (n=1000, conversion=0.10) vs Group B (n=1000, conversion=0.12).\nYou get p = 0.04. A stakeholder says 'ship it — it's a 20% improvement!'\nWhat statistical concern should you raise?",
                'opts' => [
                    ['The p-value is too low to be valid', false],
                    ['The practical effect size is small (2 percentage points), and you should also check for multiple testing if this was one of many A/B tests run simultaneously', true],
                    ['N=1000 per group is always insufficient for A/B testing', false],
                    ['Conversion rates cannot be compared with a z-test or t-test', false],
                ],
            ],
            [
                'q' => "What is Simpson's Paradox?",
                'opts' => [
                    ['When adding more data always reverses a trend', false],
                    ['When a trend appears in several groups of data but disappears or reverses when the groups are combined', true],
                    ['When two correlated variables have opposite regression slopes', false],
                    ['When the mean and median are equal despite skewed data', false],
                ],
            ],

            // ── INFORMATION THEORY BASICS ─────────────────────────────────
            [
                'q' => 'Shannon entropy H(X) = -Σ p(x) log₂ p(x). A fair coin has entropy of:',
                'opts' => [
                    ['0 bits', false],
                    ['0.5 bits', false],
                    ['1 bit', true],
                    ['2 bits', false],
                ],
            ],
            [
                'q' => 'KL Divergence D_KL(P||Q) measures:',
                'opts' => [
                    ['The symmetric distance between two distributions', false],
                    ['How much information is lost when using Q to approximate P (non-symmetric)', true],
                    ['The entropy of the combined distribution P + Q', false],
                    ['The variance between two datasets', false],
                ],
            ],
            [
                'q' => "A decision tree uses information gain to split nodes.\nInformation gain = Entropy(parent) - weighted avg Entropy(children).\nA split that perfectly separates classes produces child entropy of:",
                'opts' => [
                    ['1.0', false],
                    ['0.5', false],
                    ['0', true],
                    ['Negative', false],
                ],
            ],

            // ── RESAMPLING & CROSS-VALIDATION ─────────────────────────────
            [
                'q' => "What does k-fold cross-validation help detect?\n\nfrom sklearn.model_selection import cross_val_score\nscores = cross_val_score(model, X, y, cv=5)\nprint(scores.mean(), scores.std())",
                'opts' => [
                    ['Whether the data is normally distributed', false],
                    ['How well the model generalizes to unseen data and whether it overfits', true],
                    ['The optimal number of features to use', false],
                    ['The statistical significance of the model coefficients', false],
                ],
            ],
            [
                'q' => 'What is the bias-variance tradeoff?',
                'opts' => [
                    ['Models with low bias always have low variance', false],
                    ['Increasing model complexity tends to decrease bias but increase variance; the goal is to minimize total error', true],
                    ['High variance models are always better than high bias models', false],
                    ['Bias only matters in classification, not regression', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 2 — Basics of Statistics (Advanced).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Advanced");
    }
}