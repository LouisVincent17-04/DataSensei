<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;

class Module2ChallengeSeederIntermediate extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'intermediate')->first();

        if (!$category) {
            $this->command->error("Intermediate category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        $this->command->info("Creating Module 2 — Basics of Statistics (Intermediate)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Basics of Statistics',
            'description'           => 'Work through multi-step statistical problems involving Python code, manual calculations, and analytical reasoning. Covers distributions, hypothesis testing, regression, and data analysis workflows.',
            'time_limit_seconds'    => 1500,
            'base_xp'               => 900,
            'order_index'           => 2,
        ]);

        $this->command->info("Seeding 50 intermediate-level statistics questions...");

        $qaData = [

            // ── DESCRIPTIVE STATS FROM CODE ────────────────────────────────
            [
                'q' => "What does this code compute?\n\ndata = [4, 8, 15, 16, 23, 42]\nresult = sum(data) / len(data)",
                'opts' => [
                    ['The median', false],
                    ['The mode', false],
                    ['The arithmetic mean', true],
                    ['The variance', false],
                ],
            ],
            [
                'q' => "What is the output?\n\nimport statistics\ndata = [3, 1, 4, 1, 5, 9, 2, 6]\nprint(statistics.median(data))",
                'opts' => [
                    ['3', false],
                    ['3.5', true],
                    ['4', false],
                    ['5', false],
                ],
            ],
            [
                'q' => "What does this code calculate?\n\ndata = [2, 4, 4, 4, 5, 5, 7, 9]\nmean = sum(data) / len(data)\nvariance = sum((x - mean)**2 for x in data) / len(data)\nprint(variance)",
                'opts' => [
                    ['Population variance', true],
                    ['Sample variance', false],
                    ['Standard deviation', false],
                    ['Mean absolute deviation', false],
                ],
            ],
            [
                'q' => "What single fix makes this correctly compute SAMPLE variance instead of population variance?\n\ndata = [2, 4, 4, 4, 5, 5, 7, 9]\nmean = sum(data) / len(data)\nvariance = sum((x - mean)**2 for x in data) / len(data)",
                'opts' => [
                    ['Change sum() to max()', false],
                    ['Change len(data) to len(data) - 1', true],
                    ['Change ** 2 to ** 0.5', false],
                    ['Use sorted(data) first', false],
                ],
            ],
            [
                'q' => "What does this code find?\n\ndata = [5, 1, 9, 3, 7]\nresult = max(data) - min(data)\nprint(result)",
                'opts' => [
                    ['Mean', false],
                    ['Variance', false],
                    ['Range', true],
                    ['IQR', false],
                ],
            ],

            // ── PERCENTILES & IQR ─────────────────────────────────────────
            [
                'q' => "Using numpy, what does this return?\n\nimport numpy as np\ndata = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]\nprint(np.percentile(data, 25))",
                'opts' => [
                    ['2.5', false],
                    ['3.25', true],
                    ['4', false],
                    ['2', false],
                ],
            ],
            [
                'q' => "What does this code correctly compute?\n\nimport numpy as np\ndata = [3, 7, 8, 5, 12, 14, 21, 13, 18]\nQ1 = np.percentile(data, 25)\nQ3 = np.percentile(data, 75)\nIQR = Q3 - Q1\nlower = Q1 - 1.5 * IQR\nupper = Q3 + 1.5 * IQR\noutliers = [x for x in data if x < lower or x > upper]",
                'opts' => [
                    ['The variance of the dataset', false],
                    ['Values outside the Tukey fence (potential outliers)', true],
                    ['The standard deviation of the dataset', false],
                    ['The confidence interval of the mean', false],
                ],
            ],
            [
                'q' => 'Dataset: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100].\nQ1 = 27.5, Q3 = 72.5, IQR = 45.\nUsing the 1.5×IQR rule, what is the upper outlier fence?',
                'opts' => [
                    ['100', false],
                    ['117.5', false],
                    ['140', true],
                    ['130', false],
                ],
            ],

            // ── NORMAL DISTRIBUTION & Z-SCORES ────────────────────────────
            [
                'q' => "What is the output of this calculation?\n\nmean = 100\nstd = 15\nx = 130\nz = (x - mean) / std\nprint(round(z, 2))",
                'opts' => [
                    ['1.5', false],
                    ['2.0', true],
                    ['2.5', false],
                    ['3.0', false],
                ],
            ],
            [
                'q' => 'IQ scores are normally distributed with μ = 100 and σ = 15.\nApproximately what percentage of people have an IQ between 85 and 115?',
                'opts' => [
                    ['50%', false],
                    ['68%', true],
                    ['95%', false],
                    ['99.7%', false],
                ],
            ],
            [
                'q' => 'A student scores 75 on a test where μ = 65 and σ = 8.\nWhat is the z-score, and what does it mean?',
                'opts' => [
                    ['z = 1.0 — the student is 1 SD below average', false],
                    ['z = 1.25 — the student is 1.25 SD above average', true],
                    ['z = 0.8 — the student is just below average', false],
                    ['z = 2.5 — the student is an extreme outlier', false],
                ],
            ],
            [
                'q' => "What does scipy.stats.norm.cdf(1.96) approximately return?\n\nfrom scipy import stats\nprint(stats.norm.cdf(1.96))",
                'opts' => [
                    ['0.68', false],
                    ['0.95', false],
                    ['0.975', true],
                    ['0.99', false],
                ],
            ],
            [
                'q' => 'In a standard normal distribution (μ=0, σ=1), what is the probability of getting a z-score ABOVE 1.96?',
                'opts' => [
                    ['5%', false],
                    ['2.5%', true],
                    ['97.5%', false],
                    ['0.5%', false],
                ],
            ],

            // ── BINOMIAL DISTRIBUTION ─────────────────────────────────────
            [
                'q' => 'What conditions define a binomial distribution?',
                'opts' => [
                    ['Continuous data, unlimited trials, variable probability', false],
                    ['Fixed number of trials, two outcomes (success/fail), constant probability, independent trials', true],
                    ['Normal data with known mean and SD', false],
                    ['At least 30 observations with equal weights', false],
                ],
            ],
            [
                'q' => 'A fair coin is flipped 10 times. What is the expected number of heads?',
                'opts' => [
                    ['4', false],
                    ['5', true],
                    ['6', false],
                    ['10', false],
                ],
            ],
            [
                'q' => "What does this code compute?\n\nfrom scipy.stats import binom\nprint(binom.pmf(k=3, n=5, p=0.5))",
                'opts' => [
                    ['The probability of getting at least 3 heads in 5 flips', false],
                    ['The probability of getting exactly 3 heads in 5 fair coin flips', true],
                    ['The mean of a binomial distribution', false],
                    ['The cumulative probability up to 3 successes', false],
                ],
            ],
            [
                'q' => 'For a Binomial(n=20, p=0.3), what is the variance?',
                'opts' => [
                    ['6', false],
                    ['4.2', true],
                    ['0.3', false],
                    ['14', false],
                ],
            ],

            // ── HYPOTHESIS TESTING ────────────────────────────────────────
            [
                'q' => "What does this t-test code test?\n\nfrom scipy import stats\ngroup_a = [78, 82, 79, 85, 80]\ngroup_b = [70, 74, 72, 76, 71]\nt_stat, p_value = stats.ttest_ind(group_a, group_b)\nprint(p_value)",
                'opts' => [
                    ['Whether group_a has higher variance than group_b', false],
                    ['Whether there is a statistically significant difference in means between group_a and group_b', true],
                    ['The correlation between group_a and group_b', false],
                    ['Whether the data is normally distributed', false],
                ],
            ],
            [
                'q' => 'A t-test returns p = 0.04. Using α = 0.05, what is the correct conclusion?',
                'opts' => [
                    ['Fail to reject H₀ — no significant difference', false],
                    ['Reject H₀ — the difference is statistically significant', true],
                    ['Accept H₁ as proven fact', false],
                    ['The test is inconclusive', false],
                ],
            ],
            [
                'q' => 'What is a Type I error in hypothesis testing?',
                'opts' => [
                    ['Failing to reject a false null hypothesis', false],
                    ['Rejecting a true null hypothesis (false positive)', true],
                    ['Using the wrong test statistic', false],
                    ['Having a sample size that is too small', false],
                ],
            ],
            [
                'q' => 'What is a Type II error in hypothesis testing?',
                'opts' => [
                    ['Rejecting a true null hypothesis', false],
                    ['Failing to reject a false null hypothesis (false negative)', true],
                    ['Using a wrong significance level', false],
                    ['Having too many outliers in the data', false],
                ],
            ],
            [
                'q' => 'The significance level α = 0.05 represents the:',
                'opts' => [
                    ['Probability of a Type II error', false],
                    ['Acceptable probability of making a Type I error', true],
                    ['Confidence level of the test', false],
                    ['Effect size of the test', false],
                ],
            ],

            // ── CONFIDENCE INTERVALS ──────────────────────────────────────
            [
                'q' => 'What does a 95% confidence interval mean?',
                'opts' => [
                    ['95% of the data falls within this interval', false],
                    ['There is a 95% probability the true population parameter is in this specific interval', false],
                    ['If we repeated the experiment many times, 95% of such intervals would contain the true parameter', true],
                    ['The sample mean is 95% accurate', false],
                ],
            ],
            [
                'q' => "What is the 95% CI for this sample?\n\nimport numpy as np\nfrom scipy import stats\ndata = [48, 52, 55, 49, 51, 53, 50, 54]\nmean = np.mean(data)\nse = stats.sem(data)\nci = stats.t.interval(0.95, df=len(data)-1, loc=mean, scale=se)\nprint(ci)",
                'opts' => [
                    ['The range [mean - 2σ, mean + 2σ]', false],
                    ['A t-distribution-based 95% confidence interval for the population mean', true],
                    ['The IQR of the dataset', false],
                    ['The standard deviation of the sample', false],
                ],
            ],
            [
                'q' => 'Increasing sample size generally does what to the confidence interval width?',
                'opts' => [
                    ['Makes it wider', false],
                    ['Has no effect', false],
                    ['Makes it narrower', true],
                    ['Makes it asymmetric', false],
                ],
            ],

            // ── CORRELATION & REGRESSION ──────────────────────────────────
            [
                'q' => "What does this output represent?\n\nimport numpy as np\nx = [1, 2, 3, 4, 5]\ny = [2, 4, 5, 4, 5]\ncorr = np.corrcoef(x, y)[0, 1]\nprint(round(corr, 2))",
                'opts' => [
                    ['The slope of the regression line', false],
                    ['The Pearson correlation coefficient between x and y', true],
                    ['The variance of x', false],
                    ['The R-squared value', false],
                ],
            ],
            [
                'q' => "A linear regression gives: ŷ = 3.5x + 10\nIf x = 4, what is the predicted ŷ?",
                'opts' => [
                    ['14', false],
                    ['24', true],
                    ['21', false],
                    ['40', false],
                ],
            ],
            [
                'q' => "What does this code do?\n\nfrom sklearn.linear_model import LinearRegression\nimport numpy as np\nX = np.array([[1],[2],[3],[4],[5]])\ny = [2, 4, 5, 4, 5]\nmodel = LinearRegression().fit(X, y)\nprint(model.coef_, model.intercept_)",
                'opts' => [
                    ['Prints the correlation and variance', false],
                    ['Prints the slope and intercept of the best-fit line', true],
                    ['Prints the residuals of the model', false],
                    ['Tests the null hypothesis about the slope', false],
                ],
            ],
            [
                'q' => 'What are residuals in linear regression?',
                'opts' => [
                    ['The predicted values', false],
                    ['The differences between the actual and predicted values', true],
                    ['The coefficients of the regression line', false],
                    ['The variance of the dependent variable', false],
                ],
            ],

            // ── CHI-SQUARE TEST ───────────────────────────────────────────
            [
                'q' => 'When is a chi-square test used?',
                'opts' => [
                    ['To compare two numerical means', false],
                    ['To test relationships between two categorical variables', true],
                    ['To test if data is normally distributed (only)', false],
                    ['To calculate regression coefficients', false],
                ],
            ],
            [
                'q' => "What does this code test?\n\nfrom scipy.stats import chi2_contingency\nimport numpy as np\ntable = np.array([[50, 30], [20, 40]])\nchi2, p, dof, expected = chi2_contingency(table)\nprint(p)",
                'opts' => [
                    ['Whether two groups have equal variances', false],
                    ['Whether two categorical variables are independent of each other', true],
                    ['Whether the data follows a normal distribution', false],
                    ['The correlation between two variables', false],
                ],
            ],

            // ── CENTRAL LIMIT THEOREM ─────────────────────────────────────
            [
                'q' => 'What does the Central Limit Theorem (CLT) state?',
                'opts' => [
                    ['All data in the real world is normally distributed', false],
                    ['The distribution of sample means approaches a normal distribution as sample size grows, regardless of the population distribution', true],
                    ['The mean of a sample always equals the population mean', false],
                    ['Variance decreases as sample size increases', false],
                ],
            ],
            [
                'q' => 'The CLT is most reliable when the sample size is at least:',
                'opts' => [
                    ['5', false],
                    ['10', false],
                    ['30', true],
                    ['100', false],
                ],
            ],
            [
                'q' => "A population has μ = 50 and σ = 10. You take samples of size n = 25.\nWhat is the standard error of the mean (SEM)?",
                'opts' => [
                    ['10', false],
                    ['5', false],
                    ['2', true],
                    ['0.4', false],
                ],
            ],

            // ── DATA TRANSFORMATION & SKEWNESS ───────────────────────────
            [
                'q' => "What does this code check, and what result indicates normality?\n\nfrom scipy.stats import skew\ndata = [2, 3, 3, 4, 4, 4, 5, 6, 10]\nprint(round(skew(data), 2))",
                'opts' => [
                    ['Kurtosis — a value near 3 indicates normality', false],
                    ['Skewness — a value near 0 indicates approximate symmetry (normality)', true],
                    ['Standard deviation — a value < 1 indicates normality', false],
                    ['Variance — a negative value indicates right skew', false],
                ],
            ],
            [
                'q' => 'A dataset is heavily right-skewed. Applying log transformation typically:',
                'opts' => [
                    ['Increases the skewness further', false],
                    ['Has no effect on distribution shape', false],
                    ['Reduces right skewness and pulls the distribution closer to normal', true],
                    ['Makes the dataset bimodal', false],
                ],
            ],
            [
                'q' => "What is wrong with this code for computing skewness manually?\n\ndata = [1, 2, 3, 4, 5]\nmean = sum(data) / len(data)\nskewness = sum((x - mean)**2 for x in data) / len(data)",
                'opts' => [
                    ['sum() should be max()', false],
                    ['The exponent should be 3, not 2 (skewness uses the third moment)', true],
                    ['mean should be computed using sorted(data)', false],
                    ['len(data) should be len(data) - 1', false],
                ],
            ],

            // ── PRACTICAL / ANALYTICAL PROBLEMS ──────────────────────────
            [
                'q' => "A dataset: [10, 12, 14, 16, 18, 20, 100]\nMean = 27.1, Median = 16\nWhich is the better measure of center, and why?",
                'opts' => [
                    ['Mean, because it uses all data values', false],
                    ['Median, because it is not distorted by the outlier (100)', true],
                    ['Mode, because it is the most common value', false],
                    ['They are equally useful here', false],
                ],
            ],
            [
                'q' => 'You have two datasets:\nA: [50, 50, 50, 50, 50] — SD = 0\nB: [20, 35, 50, 65, 80] — SD = 22.9\nWhich dataset would have a wider 95% confidence interval for the mean?',
                'opts' => [
                    ['Dataset A', false],
                    ['Dataset B', true],
                    ['They would be the same width', false],
                    ['Cannot be determined', false],
                ],
            ],
            [
                'q' => 'You run a hypothesis test and get p = 0.001. A colleague says "the effect is practically significant." Are they correct?',
                'opts' => [
                    ['Yes — a very small p-value always means a large, meaningful effect', false],
                    ['No — statistical significance (p-value) does not guarantee practical significance (effect size)', true],
                    ['Yes — p < 0.05 always means the result is important', false],
                    ['No — p = 0.001 means we should accept the null hypothesis', false],
                ],
            ],
            [
                'q' => "What is wrong with this interpretation?\n\n\"We got p = 0.07 so we accept the null hypothesis — there is definitely no difference between groups.\"",
                'opts' => [
                    ['Nothing — p > 0.05 means we accept the null', false],
                    ['We "fail to reject" H₀, not "accept" it — we simply lack sufficient evidence for a difference', true],
                    ['The p-value of 0.07 is actually significant at α = 0.05', false],
                    ['Accepting H₀ is valid when p > 0.10', false],
                ],
            ],

            // ── MULTI-STEP CALCULATIONS ───────────────────────────────────
            [
                'q' => "Dataset: [8, 12, 10, 14, 6]\nStep 1: Find the mean.\nStep 2: Find each deviation squared.\nStep 3: Sum them and divide by N.\nWhat is the population standard deviation (rounded to 2 decimal places)?",
                'opts' => [
                    ['2.28', false],
                    ['2.83', true],
                    ['3.16', false],
                    ['4.00', false],
                ],
            ],
            [
                'q' => "Two groups:\nGroup A (n=5): mean = 75, SD = 8\nGroup B (n=5): mean = 85, SD = 6\nYou want to test if their means differ significantly.\nWhat is the most appropriate test?",
                'opts' => [
                    ['Chi-square test', false],
                    ['Independent samples t-test', true],
                    ['Paired samples t-test', false],
                    ['ANOVA', false],
                ],
            ],
            [
                'q' => "Before and after a training program, the SAME 6 employees are tested:\nBefore: [60, 65, 70, 55, 72, 68]\nAfter:  [70, 75, 80, 65, 78, 74]\nWhat test is most appropriate?",
                'opts' => [
                    ['Independent samples t-test', false],
                    ['Paired samples t-test', true],
                    ['Chi-square test', false],
                    ['One-sample z-test', false],
                ],
            ],
            [
                'q' => "A regression model gives:\nSlope = 2.3, Intercept = 5.0, R² = 0.76\nPredict y when x = 10.",
                'opts' => [
                    ['23.0', false],
                    ['28.0', true],
                    ['25.3', false],
                    ['30.5', false],
                ],
            ],
            [
                'q' => "You split data into 80% train / 20% test.\nTraining R² = 0.95, Test R² = 0.51\nWhat problem does this suggest?",
                'opts' => [
                    ['Underfitting — the model is too simple for training data', false],
                    ['Overfitting — the model memorized training data but generalizes poorly', true],
                    ['The test set is too large', false],
                    ['R² is the wrong metric for this type of data', false],
                ],
            ],
            [
                'q' => "You compute: Pearson r = 0.92 between ice cream sales and drowning rates.\nWhat is the correct statistical interpretation?",
                'opts' => [
                    ['Ice cream causes drowning — stop selling it in summer', false],
                    ['There is a strong positive correlation, but causation cannot be inferred — a confounding variable (hot weather) likely explains both', true],
                    ['The correlation is too high to be real — the data must be wrong', false],
                    ['A high r always implies a causal mechanism', false],
                ],
            ],
            [
                'q' => 'You need to compare the means of THREE different groups. Using multiple t-tests increases the risk of Type I error. What test should you use instead?',
                'opts' => [
                    ['Chi-square test', false],
                    ['ANOVA (Analysis of Variance)', true],
                    ['Paired t-test', false],
                    ['Pearson correlation', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 2 — Basics of Statistics (Intermediate).");
        $this->command->info("   Challenge ID: {$challenge->id}  |  Category: Intermediate");
    }
}