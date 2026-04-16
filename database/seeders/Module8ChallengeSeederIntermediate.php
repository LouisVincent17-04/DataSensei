<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module8ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', 'Statistical Methods & Experimental Design')
            ->delete();

        $this->command->info("Creating Module 8 — Statistical Methods & Experimental Design (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Statistical Methods & Experimental Design',
            'description'           => 'Solve multi-step statistical problems involving distributions, hypothesis testing, regression, and experimental design. Questions include calculations, interpretation of formulas, and code-like computation traces.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 1500,
            'order_index'           => 8,
        ]);

        $this->command->info("Seeding 50 intermediate statistics questions...");

        $qaData = [

            // ── DESCRIPTIVE STATS — MULTI-STEP ───────────────────────────
            [
                'q' => 'Given the data: 4, 8, 6, 5, 3, 2, 8, 9, 2, 5

Step 1: Find the mean.
Step 2: Find the deviations from the mean.
Step 3: Find the POPULATION variance.

What is the population variance? (Round to 2 decimal places)',
                'opts' => [
                    ['4.80', false],
                    ['5.20', true],
                    ['6.00', false],
                    ['4.00', false],
                ],
            ],
            [
                'q' => 'A sample has values: 10, 14, 11, 13, 12.
The sample mean is 12.
Compute the SAMPLE variance (divide by n−1).

s² = ?',
                'opts' => [
                    ['2.0', false],
                    ['2.5', true],
                    ['3.0', false],
                    ['1.5', false],
                ],
            ],
            [
                'q' => 'Which of the following correctly computes the z-score for x = 85, given mean μ = 75 and σ = 5?

z = (x − μ) / σ',
                'opts' => [
                    ['z = 1.5', false],
                    ['z = 2.5', false],
                    ['z = 2.0', true],
                    ['z = 0.5', false],
                ],
            ],
            [
                'q' => 'A dataset is right-skewed. Which relationship is expected?

A) Mean > Median > Mode
B) Mean < Median < Mode
C) Mean = Median = Mode
D) Mode > Mean > Median',
                'opts' => [
                    ['B', false],
                    ['C', false],
                    ['D', false],
                    ['A', true],
                ],
            ],
            [
                'q' => 'The Empirical Rule states that for a normal distribution, ~99.7% of data lies within:',
                'opts' => [
                    ['1 standard deviation of the mean', false],
                    ['2 standard deviations of the mean', false],
                    ['3 standard deviations of the mean', true],
                    ['4 standard deviations of the mean', false],
                ],
            ],

            // ── PROBABILITY — MULTI-STEP ─────────────────────────────────
            [
                'q' => 'A disease affects 1% of a population. A test is 95% sensitive (detects disease when present) and 90% specific (correctly identifies healthy people). If a person tests POSITIVE, what is the approximate probability they actually have the disease? (Use Bayes\' Theorem)

P(Disease | Positive) ≈ ?',
                'opts' => [
                    ['~50%', false],
                    ['~8.7%', true],
                    ['~95%', false],
                    ['~1%', false],
                ],
            ],
            [
                'q' => 'A factory produces items where 2% are defective. A quality inspector randomly picks 5 items. Using Binomial(n=5, p=0.02), what is the probability that NONE are defective?

P(X=0) = (1 − 0.02)^5 ≈ ?',
                'opts' => [
                    ['0.87', false],
                    ['0.98', false],
                    ['0.90', true],
                    ['0.75', false],
                ],
            ],
            [
                'q' => 'For a Poisson distribution with λ = 3, what is the expected value AND variance?',
                'opts' => [
                    ['Mean = 3, Variance = 9', false],
                    ['Mean = 3, Variance = 3', true],
                    ['Mean = 9, Variance = 3', false],
                    ['Mean = 1.73, Variance = 3', false],
                ],
            ],
            [
                'q' => 'In the Central Limit Theorem, as the sample size n increases, the sampling distribution of the sample mean:',
                'opts' => [
                    ['Becomes more skewed', false],
                    ['Approaches a normal distribution regardless of the population shape', true],
                    ['Spreads wider', false],
                    ['Equals the population distribution exactly', false],
                ],
            ],
            [
                'q' => 'The standard error of the mean (SEM) is computed as:

SEM = σ / √n

If σ = 20 and n = 100, what is the SEM?',
                'opts' => [
                    ['0.2', false],
                    ['2', true],
                    ['20', false],
                    ['200', false],
                ],
            ],

            // ── CONFIDENCE INTERVALS ──────────────────────────────────────
            [
                'q' => 'A 95% confidence interval for a population mean is interpreted as:',
                'opts' => [
                    ['There is a 95% chance the true mean is in this specific interval', false],
                    ['If we repeated the study many times, 95% of such intervals would contain the true mean', true],
                    ['95% of the data falls in this range', false],
                    ['The sample mean is 95% accurate', false],
                ],
            ],
            [
                'q' => 'A sample of n=36 has mean x̄ = 50 and σ = 12. Compute the 95% CI.
(Use z* = 1.96, SEM = σ/√n)

CI = x̄ ± z* × SEM',
                'opts' => [
                    ['50 ± 3.92', true],
                    ['50 ± 1.96', false],
                    ['50 ± 5.88', false],
                    ['50 ± 2.00', false],
                ],
            ],
            [
                'q' => 'Which of the following would make a confidence interval NARROWER?',
                'opts' => [
                    ['Increasing confidence level from 95% to 99%', false],
                    ['Decreasing sample size', false],
                    ['Increasing sample size', true],
                    ['Increasing population standard deviation', false],
                ],
            ],
            [
                'q' => 'A 99% CI is compared to a 95% CI for the same data. The 99% CI is:',
                'opts' => [
                    ['Narrower', false],
                    ['The same width', false],
                    ['Wider', true],
                    ['Centered at a different point', false],
                ],
            ],

            // ── HYPOTHESIS TESTING — CALCULATIONS ────────────────────────
            [
                'q' => 'You are testing H₀: μ = 100 vs H₁: μ ≠ 100.
Sample: n = 49, x̄ = 106, σ = 14.

Compute the z-test statistic.

z = (x̄ − μ₀) / (σ / √n)',
                'opts' => [
                    ['z = 2', false],
                    ['z = 4', false],
                    ['z = 3', true],
                    ['z = 1', false],
                ],
            ],
            [
                'q' => 'If z = 2.5 and the critical value at α = 0.05 (two-tailed) is ±1.96, what is your conclusion?',
                'opts' => [
                    ['Fail to reject H₀', false],
                    ['Reject H₀ — significant result', true],
                    ['Accept H₀ unconditionally', false],
                    ['Cannot determine without more data', false],
                ],
            ],
            [
                'q' => 'For a one-sample t-test with n = 16, the degrees of freedom are:',
                'opts' => [
                    ['16', false],
                    ['15', true],
                    ['17', false],
                    ['8', false],
                ],
            ],
            [
                'q' => 'A paired t-test is conducted on before/after data with n = 20 pairs. The mean difference is d̄ = 4.5, and the standard error of the difference is SE = 1.5.

Compute t = d̄ / SE',
                'opts' => [
                    ['t = 1.5', false],
                    ['t = 4.5', false],
                    ['t = 6.0', false],
                    ['t = 3.0', true],
                ],
            ],

            // ── ANOVA ─────────────────────────────────────────────────────
            [
                'q' => 'In a one-way ANOVA, the F-statistic is computed as:',
                'opts' => [
                    ['Within-group variance / Between-group variance', false],
                    ['Between-group variance (MSB) / Within-group variance (MSW)', true],
                    ['Total variance / Sample size', false],
                    ['Mean difference / Standard error', false],
                ],
            ],
            [
                'q' => 'In ANOVA, a large F-statistic suggests:',
                'opts' => [
                    ['The group means are all the same', false],
                    ['There is more variation within groups than between groups', false],
                    ['At least one group mean differs significantly from the others', true],
                    ['The null hypothesis is definitely true', false],
                ],
            ],
            [
                'q' => 'After a significant ANOVA result, a POST-HOC test (e.g., Tukey\'s HSD) is performed to:',
                'opts' => [
                    ['Re-run the ANOVA with different data', false],
                    ['Identify WHICH specific group means are different from each other', true],
                    ['Increase the power of the test', false],
                    ['Compute the overall F-statistic', false],
                ],
            ],
            [
                'q' => 'A one-way ANOVA is conducted on 4 groups each of size 10 (N=40). What are the degrees of freedom for the BETWEEN groups?',
                'opts' => [
                    ['39', false],
                    ['36', false],
                    ['4', false],
                    ['3', true],
                ],
            ],

            // ── CHI-SQUARE CALCULATIONS ───────────────────────────────────
            [
                'q' => 'In a Chi-square goodness-of-fit test, observed frequencies are: 30, 20, 50. Expected are: 33.3, 33.3, 33.3.

Which formula gives the test statistic?

χ² = Σ [(O − E)² / E]

Compute χ² approximately.',
                'opts' => [
                    ['χ² ≈ 3.0', false],
                    ['χ² ≈ 12.06', true],
                    ['χ² ≈ 6.5', false],
                    ['χ² ≈ 1.5', false],
                ],
            ],
            [
                'q' => 'A 2×2 contingency table has the following values:
  Observed: [[30, 20], [10, 40]]
  Expected: [[20, 30], [20, 30]]

For the cell (30, Expected=20), what is that cell\'s χ² contribution?',
                'opts' => [
                    ['10', false],
                    ['5', true],
                    ['2', false],
                    ['100', false],
                ],
            ],
            [
                'q' => 'In a Chi-square test of independence for a 3×4 table, the degrees of freedom are:',
                'opts' => [
                    ['12', false],
                    ['6', true],
                    ['11', false],
                    ['7', false],
                ],
            ],
            [
                'q' => 'Yates\' continuity correction is applied to Chi-square tests when:',
                'opts' => [
                    ['The sample is very large', false],
                    ['Expected cell frequency is small (especially in 2×2 tables)', true],
                    ['The data is normally distributed', false],
                    ['There are more than 5 categories', false],
                ],
            ],

            // ── REGRESSION — CALCULATIONS ─────────────────────────────────
            [
                'q' => 'Given: Σx = 30, Σy = 50, Σxy = 170, Σx² = 100, n = 10.

The slope b = (nΣxy − ΣxΣy) / (nΣx² − (Σx)²)

Compute b.',
                'opts' => [
                    ['b = 0.5', false],
                    ['b = 2.0', true],
                    ['b = 1.5', false],
                    ['b = 0.25', false],
                ],
            ],
            [
                'q' => 'In a regression model, the RESIDUAL is defined as:',
                'opts' => [
                    ['Predicted y minus actual y', false],
                    ['Actual y minus predicted ŷ', true],
                    ['The slope times x', false],
                    ['The intercept value', false],
                ],
            ],
            [
                'q' => 'Heteroscedasticity in regression means:',
                'opts' => [
                    ['Residuals have constant variance', false],
                    ['The model is perfectly linear', false],
                    ['The variance of residuals changes across levels of the predictor', true],
                    ['There is perfect multicollinearity', false],
                ],
            ],
            [
                'q' => 'In a regression model with R² = 0.64, how much variance in y is UNEXPLAINED?',
                'opts' => [
                    ['64%', false],
                    ['36%', true],
                    ['0.64%', false],
                    ['0.36%', false],
                ],
            ],

            // ── EXPERIMENTAL DESIGN — ANALYTICAL ─────────────────────────
            [
                'q' => 'A RANDOMIZED CONTROLLED TRIAL (RCT) is considered the gold standard because:',
                'opts' => [
                    ['It always uses a large sample', false],
                    ['Randomization minimizes confounding and allows causal inference', true],
                    ['It never requires a control group', false],
                    ['It does not require statistical analysis', false],
                ],
            ],
            [
                'q' => 'In a FACTORIAL design, 2 factors each with 2 levels creates:',
                'opts' => [
                    ['2 treatment conditions', false],
                    ['4 treatment conditions', true],
                    ['6 treatment conditions', false],
                    ['8 treatment conditions', false],
                ],
            ],
            [
                'q' => 'An INTERACTION EFFECT in a two-way ANOVA means:',
                'opts' => [
                    ['Both main effects are significant', false],
                    ['The effect of one factor depends on the level of the other factor', true],
                    ['Neither main effect is significant', false],
                    ['The factors are independent of each other', false],
                ],
            ],
            [
                'q' => 'BLOCKING in experimental design is used to:',
                'opts' => [
                    ['Increase the effect size artificially', false],
                    ['Control for a known source of variability that is not the primary focus', true],
                    ['Select a biased sample', false],
                    ['Reduce sample size', false],
                ],
            ],

            // ── EFFECT SIZE & POWER — CALCULATIONS ───────────────────────
            [
                'q' => 'Cohen\'s d is computed as:

d = (μ₁ − μ₂) / σ_pooled

If μ₁ = 75, μ₂ = 65, and σ_pooled = 10, what is d?',
                'opts' => [
                    ['d = 0.5', false],
                    ['d = 2.0', false],
                    ['d = 1.0', true],
                    ['d = 0.1', false],
                ],
            ],
            [
                'q' => 'According to Cohen\'s conventions, d = 0.2 is considered:',
                'opts' => [
                    ['Large effect', false],
                    ['Medium effect', false],
                    ['Small effect', true],
                    ['Negligible effect', false],
                ],
            ],
            [
                'q' => 'Increasing power from 0.80 to 0.90 (while keeping α fixed) generally requires:',
                'opts' => [
                    ['Fewer participants', false],
                    ['A larger sample size', true],
                    ['A higher α level', false],
                    ['Using a one-tailed test only', false],
                ],
            ],
            [
                'q' => 'The relationship between α (Type I error), β (Type II error), and power is:',
                'opts' => [
                    ['Power = 1 − α', false],
                    ['Power = α − β', false],
                    ['Power = 1 − β', true],
                    ['Power = α + β', false],
                ],
            ],

            // ── MODEL DIAGNOSTICS ─────────────────────────────────────────
            [
                'q' => 'Which of the following plots is used to check if regression residuals are normally distributed?',
                'opts' => [
                    ['Scatter plot of x vs y', false],
                    ['Q-Q (quantile-quantile) plot of residuals', true],
                    ['Bar chart of fitted values', false],
                    ['Histogram of x values', false],
                ],
            ],
            [
                'q' => 'The Durbin-Watson test is used to detect:',
                'opts' => [
                    ['Multicollinearity', false],
                    ['Heteroscedasticity', false],
                    ['Autocorrelation in residuals', true],
                    ['Non-linearity', false],
                ],
            ],
            [
                'q' => 'Cook\'s distance in regression is used to identify:',
                'opts' => [
                    ['Heteroscedasticity', false],
                    ['Influential data points (outliers with leverage)', true],
                    ['The best-fit slope', false],
                    ['Multicollinearity among predictors', false],
                ],
            ],
            [
                'q' => 'The Variance Inflation Factor (VIF) measures:',
                'opts' => [
                    ['Heteroscedasticity of residuals', false],
                    ['The degree of multicollinearity among predictors', true],
                    ['Effect size in ANOVA', false],
                    ['Whether the model is overfitted', false],
                ],
            ],

            // ── NON-PARAMETRIC METHODS ────────────────────────────────────
            [
                'q' => 'The Wilcoxon Signed-Rank test is used as a non-parametric alternative to:',
                'opts' => [
                    ['ANOVA', false],
                    ['Chi-square test', false],
                    ['Paired t-test', true],
                    ['Simple linear regression', false],
                ],
            ],
            [
                'q' => 'The Kruskal-Wallis test is used when:',
                'opts' => [
                    ['You have exactly two groups and normal data', false],
                    ['You need to compare three or more groups and normality cannot be assumed', true],
                    ['You are testing for correlation between two variables', false],
                    ['Your data is a 2×2 contingency table', false],
                ],
            ],
            [
                'q' => 'Spearman\'s rank correlation coefficient (rₛ) is preferred over Pearson\'s r when:',
                'opts' => [
                    ['Both variables are normally distributed', false],
                    ['The relationship is linear', false],
                    ['The data is ordinal or has significant outliers', true],
                    ['The sample size is very large', false],
                ],
            ],
            [
                'q' => 'In a Mann-Whitney U test, what are you comparing?',
                'opts' => [
                    ['The variances of two groups', false],
                    ['The RANK distributions of two independent groups', true],
                    ['The correlation between two variables', false],
                    ['The proportions in a contingency table', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 8 — Statistical Methods & Experimental Design (Intermediate).");
    }
}