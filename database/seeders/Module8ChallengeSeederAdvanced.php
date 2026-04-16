<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module8ChallengeSeederAdvanced extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'advanced')->first();

        if (!$category) {
            $this->command->error("Advanced category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', 'Statistical Methods & Experimental Design')
            ->delete();

        $this->command->info("Creating Module 8 — Statistical Methods & Experimental Design (Advanced)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Statistical Methods & Experimental Design',
            'description'           => 'Tackle advanced statistical challenges involving Python/R-like code snippets, debugging flawed analyses, multi-step derivations, and sophisticated experimental design decisions.',
            'time_limit_seconds'    => 1800,
            'base_xp'               => 2000,
            'order_index'           => 8,
        ]);

        $this->command->info("Seeding 50 advanced statistics questions...");

        $qaData = [

            // ── CODE TRACING — DESCRIPTIVE STATS ──────────────────────────
            [
                'q' => 'Trace the output of this Python code:

import statistics
data = [4, 8, 6, 5, 3, 2, 8, 9, 2, 5]
print(round(statistics.variance(data), 2))

Note: statistics.variance() uses sample variance (n-1).',
                'opts' => [
                    ['4.80', false],
                    ['5.21', false],
                    ['5.78', true],
                    ['6.00', false],
                ],
            ],
            [
                'q' => 'What does this code compute?

import numpy as np
data = np.array([10, 20, 30, 40, 50])
result = np.std(data)
print(round(result, 2))

Note: np.std() uses population standard deviation by default.',
                'opts' => [
                    ['14.14', true],
                    ['15.81', false],
                    ['10.00', false],
                    ['12.25', false],
                ],
            ],
            [
                'q' => 'Identify the BUG in this z-score computation:

def z_score(x, mean, std):
    return (x + mean) / std

z = z_score(85, 75, 10)',
                'opts' => [
                    ['std should be variance', false],
                    ['The formula uses + instead of −', true],
                    ['mean and x are in wrong order', false],
                    ['There is no bug', false],
                ],
            ],
            [
                'q' => 'What is the output?

data = [2, 4, 4, 4, 5, 5, 7, 9]
mean = sum(data) / len(data)
variance = sum((x - mean)**2 for x in data) / len(data)
print(variance)',
                'opts' => [
                    ['4.0', true],
                    ['4.57', false],
                    ['2.0', false],
                    ['16.0', false],
                ],
            ],
            [
                'q' => 'This code attempts to detect outliers using IQR. What is wrong?

import numpy as np
data = [1, 2, 2, 3, 4, 100]
Q1 = np.percentile(data, 25)
Q3 = np.percentile(data, 75)
IQR = Q1 - Q3  # BUG HERE
lower = Q1 - 1.5 * IQR
upper = Q3 + 1.5 * IQR
outliers = [x for x in data if x < lower or x > upper]',
                'opts' => [
                    ['percentile values are wrong', false],
                    ['IQR should be Q3 − Q1, not Q1 − Q3', true],
                    ['The list comprehension is incorrect', false],
                    ['1.5 should be 2.0', false],
                ],
            ],

            // ── CODE TRACING — PROBABILITY & DISTRIBUTIONS ────────────────
            [
                'q' => 'Trace this code and determine what probability is being computed:

from scipy.stats import binom
p = binom.pmf(k=3, n=10, p=0.5)
print(round(p, 4))',
                'opts' => [
                    ['P(X = 3) for Binomial(n=10, p=0.5) ≈ 0.1172', true],
                    ['P(X ≤ 3) for Binomial(n=10, p=0.5) ≈ 0.1719', false],
                    ['P(X ≥ 3) for Binomial(n=10, p=0.5) ≈ 0.9453', false],
                    ['The mean of the distribution', false],
                ],
            ],
            [
                'q' => 'What does this compute and what is the approximate result?

from scipy.stats import norm
result = norm.cdf(1.96)
print(round(result, 4))',
                'opts' => [
                    ['P(Z ≤ 1.96) ≈ 0.9750', true],
                    ['P(Z ≤ 1.96) ≈ 0.0250', false],
                    ['P(Z = 1.96) ≈ 0.0584', false],
                    ['P(Z ≥ 1.96) ≈ 0.9750', false],
                ],
            ],
            [
                'q' => 'Identify the bug in this code that computes P(A and B) assuming independence:

P_A = 0.4
P_B = 0.3
P_A_and_B = P_A + P_B  # Bug: should this be * or + ?',
                'opts' => [
                    ['No bug — addition is correct', false],
                    ['Should use multiplication: P_A * P_B', true],
                    ['Should use P_A − P_B', false],
                    ['Should use P_A / P_B', false],
                ],
            ],
            [
                'q' => 'What statistical concept is this code simulating?

import numpy as np
np.random.seed(42)
sample_means = [np.mean(np.random.normal(0, 1, 30)) for _ in range(10000)]
print(np.std(sample_means))',
                'opts' => [
                    ['Population variance', false],
                    ['The Standard Error of the Mean (CLT in action)', true],
                    ['The skewness of the normal distribution', false],
                    ['The population standard deviation', false],
                ],
            ],
            [
                'q' => 'What is wrong with this confidence interval calculation?

import numpy as np
from scipy import stats

data = [10, 12, 14, 16, 18]
n = len(data)
mean = np.mean(data)
sem = np.std(data) / np.sqrt(n)  # Uses population std
ci = stats.norm.interval(0.95, loc=mean, scale=sem)',
                'opts' => [
                    ['norm.interval should be t.interval for small samples', false],
                    ['np.std(data) uses population std; for a sample, use np.std(data, ddof=1). Also, t-distribution is more appropriate for small n.', true],
                    ['The confidence level should be 0.975, not 0.95', false],
                    ['There is no bug', false],
                ],
            ],

            // ── HYPOTHESIS TESTING — CODE & DEBUGGING ────────────────────
            [
                'q' => 'This code runs a one-sample t-test. What conclusion is drawn given the output?

from scipy import stats
data = [102, 98, 105, 110, 97, 103, 99, 108]
t_stat, p_value = stats.ttest_1samp(data, popmean=100)
print(f"t={t_stat:.3f}, p={p_value:.4f}")
# Output: t=1.789, p=0.1159',
                'opts' => [
                    ['Reject H₀ at α=0.05; the mean is significantly different from 100', false],
                    ['Fail to reject H₀ at α=0.05; insufficient evidence the mean differs from 100', true],
                    ['The test is invalid because sample size is too small', false],
                    ['p-value of 0.1159 means 11.59% of data is outliers', false],
                ],
            ],
            [
                'q' => 'Identify the error in this two-sample t-test setup:

from scipy import stats
group_A = [23, 25, 28, 22, 27]
group_B = [30, 35, 33, 31, 29]

# Researcher says groups are paired (same subjects before/after)
t, p = stats.ttest_ind(group_A, group_B)  # Bug?',
                'opts' => [
                    ['No bug — ttest_ind is always correct', false],
                    ['ttest_ind tests independent samples; for paired data, use ttest_rel', true],
                    ['The arrays have different values', false],
                    ['The significance level is missing', false],
                ],
            ],
            [
                'q' => 'What does this code compute?

from scipy import stats
obs = [20, 30, 50]
exp = [33.3, 33.3, 33.4]
chi2, p = stats.chisquare(f_obs=obs, f_exp=exp)
print(p)',
                'opts' => [
                    ['p-value for independence test', false],
                    ['p-value for a goodness-of-fit test against uniform expected frequencies', true],
                    ['The F-statistic for ANOVA', false],
                    ['Pearson correlation coefficient', false],
                ],
            ],
            [
                'q' => 'Trace the ANOVA code and determine what df_between should be:

from scipy import stats
group1 = [5, 6, 7]
group2 = [8, 9, 10]
group3 = [3, 4, 5]
group4 = [11, 12, 13]

f, p = stats.f_oneway(group1, group2, group3, group4)
# df_between = number of groups - 1
df_between = ???',
                'opts' => [
                    ['3', true],
                    ['4', false],
                    ['8', false],
                    ['11', false],
                ],
            ],
            [
                'q' => 'What is the critical flaw in this hypothesis test?

# Testing if coin is fair
flips = 55  # heads in 100 flips
p_value = 2 * (1 - 0.9332)  # Using z-table manually, z ≈ 1.00
alpha = 0.05
if p_value < alpha:
    print("Reject H0")
else:
    print("Fail to reject H0")
# z = (55 - 50) / sqrt(100 * 0.5 * 0.5) = 1.00 → p ≈ 0.1336',
                'opts' => [
                    ['The formula for z is wrong', false],
                    ['The two-sided p-value should be 2 × P(Z > 1.00) ≈ 0.3174, not 0.1336. The z-table value used is P(Z < 1.00), not P(Z > 1.00)', true],
                    ['α should be 0.01', false],
                    ['There is no flaw', false],
                ],
            ],

            // ── REGRESSION — CODE & ANALYSIS ──────────────────────────────
            [
                'q' => 'What does this code compute and what does the output tell you?

import numpy as np
x = np.array([1, 2, 3, 4, 5])
y = np.array([2, 4, 5, 4, 5])
correlation = np.corrcoef(x, y)[0, 1]
r_squared = correlation ** 2
print(round(r_squared, 4))',
                'opts' => [
                    ['The slope of the regression line', false],
                    ['R² — the proportion of variance in y explained by x', true],
                    ['The p-value of the regression', false],
                    ['The standard error of the residuals', false],
                ],
            ],
            [
                'q' => 'Identify the issue in this multiple regression interpretation:

# Model: y = 5 + 3*x1 + 2*x2
# Researcher says: "x1 has a stronger effect because its raw coefficient (3) > x2 (2)"',
                'opts' => [
                    ['The interpretation is correct', false],
                    ['Raw coefficients cannot be compared directly if x1 and x2 are on different scales; use standardized coefficients', true],
                    ['The intercept should be 0', false],
                    ['ANOVA F-test is needed first', false],
                ],
            ],
            [
                'q' => 'This code fits a regression and prints residuals. What does a residual plot with a FUNNEL shape indicate?

import numpy as np
from sklearn.linear_model import LinearRegression
import matplotlib.pyplot as plt

X = np.array([[1],[2],[3],[4],[5]])
y = np.array([1, 4, 9, 16, 25])
model = LinearRegression().fit(X, y)
residuals = y - model.predict(X)
# plt.scatter(model.predict(X), residuals)',
                'opts' => [
                    ['The model is overfitted', false],
                    ['Heteroscedasticity — residual variance is not constant', true],
                    ['Autocorrelation in the residuals', false],
                    ['The model is underfitted', false],
                ],
            ],
            [
                'q' => 'What is the problem with this regression model evaluation?

from sklearn.linear_model import LinearRegression
from sklearn.metrics import r2_score
import numpy as np

# Degree-15 polynomial fit on 20 data points
# Training R² = 0.9998
# Test R² = 0.12',
                'opts' => [
                    ['The model underfits the training data', false],
                    ['The model is overfitted — high training R² but very low test R²', true],
                    ['r2_score is the wrong metric', false],
                    ['Linear regression cannot handle polynomials', false],
                ],
            ],

            // ── EXPERIMENTAL DESIGN — ADVANCED ───────────────────────────
            [
                'q' => 'A researcher runs 20 hypothesis tests simultaneously (α = 0.05 each). What is the approximate probability of getting AT LEAST ONE false positive by chance?

P(at least one Type I error) = 1 − (1 − α)^k',
                'opts' => [
                    ['5%', false],
                    ['~64%', true],
                    ['~10%', false],
                    ['~100%', false],
                ],
            ],
            [
                'q' => 'The BONFERRONI correction addresses multiple comparisons by:',
                'opts' => [
                    ['Multiplying the sample size by the number of tests', false],
                    ['Dividing α by the number of comparisons (αᵢ = α / k)', true],
                    ['Using a chi-square test instead of t-tests', false],
                    ['Increasing effect size', false],
                ],
            ],
            [
                'q' => 'In a CROSSOVER experimental design, each subject receives:',
                'opts' => [
                    ['Only one treatment', false],
                    ['All treatments in sequence, acting as their own control', true],
                    ['A placebo only', false],
                    ['Treatments assigned by block', false],
                ],
            ],
            [
                'q' => 'What is the PURPOSE of a LATIN SQUARE design?',
                'opts' => [
                    ['To test three or more independent variables simultaneously', false],
                    ['To control for two blocking factors while testing one treatment factor', true],
                    ['To randomize subjects into three groups', false],
                    ['To reduce effect size', false],
                ],
            ],
            [
                'q' => 'Hawthorne Effect in a study refers to:',
                'opts' => [
                    ['The placebo effect', false],
                    ['Participants changing their behavior because they know they are being observed', true],
                    ['Confounding by a third variable', false],
                    ['Regression to the mean', false],
                ],
            ],

            // ── ADVANCED INFERENCE ────────────────────────────────────────
            [
                'q' => 'The LIKELIHOOD RATIO TEST compares two nested models by:',
                'opts' => [
                    ['Comparing their R² values', false],
                    ['Computing −2 × ln(L₀ / L₁) and comparing to a chi-square distribution', true],
                    ['Using a t-test on their coefficients', false],
                    ['Subtracting RMSE values', false],
                ],
            ],
            [
                'q' => 'AIC (Akaike Information Criterion) is used for:',
                'opts' => [
                    ['Hypothesis testing between two groups', false],
                    ['Model selection — lower AIC indicates a better balance of fit and complexity', true],
                    ['Computing confidence intervals', false],
                    ['Detecting multicollinearity', false],
                ],
            ],
            [
                'q' => 'In Bayesian inference, the POSTERIOR distribution is proportional to:',
                'opts' => [
                    ['Prior × Evidence', false],
                    ['Likelihood × Prior', true],
                    ['Prior / Likelihood', false],
                    ['Evidence / Prior', false],
                ],
            ],
            [
                'q' => 'Bootstrap resampling is used to:',
                'opts' => [
                    ['Increase the original sample size by duplicating data', false],
                    ['Estimate the sampling distribution of a statistic by resampling with replacement', true],
                    ['Remove outliers from the data', false],
                    ['Standardize all variables to z-scores', false],
                ],
            ],

            // ── ADVANCED ANOVA & REGRESSION ───────────────────────────────
            [
                'q' => 'In a TWO-WAY ANOVA with factors A and B, the interaction term A×B is significant. What does this mean for interpreting main effects?',
                'opts' => [
                    ['Main effects should be the primary interpretation', false],
                    ['Main effects alone are misleading; the interaction must be examined first', true],
                    ['The study should be repeated', false],
                    ['Factor A causes Factor B', false],
                ],
            ],
            [
                'q' => 'A researcher uses ANCOVA. What does ANCOVA do that standard ANOVA does not?',
                'opts' => [
                    ['Tests more than one dependent variable', false],
                    ['Controls for a continuous covariate to reduce error variance and adjust group means', true],
                    ['Uses non-parametric ranks', false],
                    ['Tests for interaction effects only', false],
                ],
            ],
            [
                'q' => 'In MANOVA, compared to running separate ANOVAs, the advantage is:',
                'opts' => [
                    ['MANOVA is simpler to compute', false],
                    ['MANOVA controls for Type I error inflation across multiple dependent variables', true],
                    ['MANOVA requires smaller samples', false],
                    ['MANOVA eliminates the need for post-hoc tests', false],
                ],
            ],
            [
                'q' => 'Ridge regression differs from OLS regression in that it:',
                'opts' => [
                    ['Removes outliers automatically', false],
                    ['Adds an L2 penalty term to shrink coefficients, addressing multicollinearity', true],
                    ['Uses ranks instead of raw values', false],
                    ['Maximizes R²', false],
                ],
            ],

            // ── ADVANCED NON-PARAMETRIC & DIAGNOSTICS ────────────────────
            [
                'q' => 'The Shapiro-Wilk test is used to assess:',
                'opts' => [
                    ['Homogeneity of variances', false],
                    ['Whether a sample comes from a normally distributed population', true],
                    ['Independence of observations', false],
                    ['The significance of a correlation', false],
                ],
            ],
            [
                'q' => 'Levene\'s test is used to check:',
                'opts' => [
                    ['Normality of residuals', false],
                    ['Equality of variances across groups (homoscedasticity)', true],
                    ['Whether the sample mean equals the population mean', false],
                    ['The strength of correlation', false],
                ],
            ],
            [
                'q' => 'What does a Q-Q plot that shows points deviating from the diagonal line at the tails indicate?',
                'opts' => [
                    ['The data is perfectly normal', false],
                    ['The data has heavier or lighter tails than a normal distribution', true],
                    ['The model has high R²', false],
                    ['The sample is biased', false],
                ],
            ],
            [
                'q' => 'Friedman\'s test is the non-parametric equivalent of:',
                'opts' => [
                    ['Independent samples ANOVA', false],
                    ['Repeated-measures ANOVA', true],
                    ['Two-sample t-test', false],
                    ['Chi-square test', false],
                ],
            ],

            // ── POWER & SAMPLE SIZE — ADVANCED ───────────────────────────
            [
                'q' => 'A researcher wants to detect a medium effect (d = 0.5) with power = 0.80 and α = 0.05 (two-tailed). Using standard power tables, the required sample size per group for an independent t-test is approximately:',
                'opts' => [
                    ['26', false],
                    ['64', true],
                    ['128', false],
                    ['30', false],
                ],
            ],
            [
                'q' => 'If you run a study with only n=10 and the true effect is small (d=0.2), what issue is most likely?',
                'opts' => [
                    ['Very high power', false],
                    ['Extremely low power — high probability of Type II error', true],
                    ['Inflated Type I error rate', false],
                    ['Overfitting', false],
                ],
            ],
            [
                'q' => 'A priori power analysis is conducted:',
                'opts' => [
                    ['After data collection to check if power was adequate', false],
                    ['Before data collection to determine the required sample size', true],
                    ['During data analysis to adjust p-values', false],
                    ['Only when sample size is very large', false],
                ],
            ],
            [
                'q' => 'Sensitivity power analysis is used to determine:',
                'opts' => [
                    ['The minimum sample size required', false],
                    ['The minimum detectable effect size given n, α, and power', true],
                    ['Whether to use parametric or non-parametric tests', false],
                    ['The correct significance level', false],
                ],
            ],

            // ── ADVANCED DEBUGGING & EDGE CASES ──────────────────────────
            [
                'q' => 'A researcher concludes "since p = 0.049 < 0.05, the effect is practically important." What is wrong with this reasoning?',
                'opts' => [
                    ['Nothing — p < 0.05 always means practical importance', false],
                    ['Statistical significance (p-value) does not imply practical significance; effect size must also be reported', true],
                    ['The threshold should be 0.01 not 0.05', false],
                    ['p-values cannot be 0.049', false],
                ],
            ],
            [
                'q' => 'p-HACKING refers to:',
                'opts' => [
                    ['A method of increasing statistical power', false],
                    ['Manipulating analyses or data collection until p < 0.05 is achieved, inflating Type I error', true],
                    ['Using too large a sample size', false],
                    ['Applying Bonferroni correction', false],
                ],
            ],
            [
                'q' => 'What is REGRESSION TO THE MEAN and when is it a concern?',
                'opts' => [
                    ['A type of model overfitting in regression', false],
                    ['Extreme values on first measurement tend to be closer to the mean on second measurement — a concern when pre-selecting extreme scorers', true],
                    ['The tendency for R² to increase with more predictors', false],
                    ['A bias introduced by stratified sampling', false],
                ],
            ],
            [
                'q' => 'The ECOLOGICAL FALLACY occurs when:',
                'opts' => [
                    ['Animal data is applied to humans', false],
                    ['Inferences about individuals are made from group-level statistical data', true],
                    ['An experiment is conducted outdoors', false],
                    ['The control group is removed from analysis', false],
                ],
            ],
            [
                'q' => 'SIMPSON\'S PARADOX describes a situation where:',
                'opts' => [
                    ['A trend appears in grouped data but disappears or reverses when data is combined', true],
                    ['A trend disappears due to a low sample size', false],
                    ['Two variables have equal correlation to a third', false],
                    ['A dataset has two modes', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 8 — Statistical Methods & Experimental Design (Advanced).");
    }
}