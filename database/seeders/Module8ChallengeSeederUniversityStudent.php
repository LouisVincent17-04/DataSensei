<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChallengeCategory;
use App\Models\Challenge;
use App\Models\ChallengeQuestion;
use App\Models\ChallengeOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Module8ChallengeSeederUniversityStudent extends Seeder
{
    public function run(): void
    {
        $category = ChallengeCategory::where('slug', 'university-student')->first();

        if (!$category) {
            $this->command->error("University Student category not found! Run ChallengeCategorySeeder first.");
            return;
        }

        Challenge::where('challenge_category_id', $category->id)
            ->where('title', 'Statistical Methods & Experimental Design')
            ->delete();

        $this->command->info("Creating Module 8 — Statistical Methods & Experimental Design (University Student)...");

        $challenge = Challenge::create([
            'challenge_category_id' => $category->id,
            'title'                 => 'Statistical Methods & Experimental Design',
            'description'           => 'Apply statistical reasoning to analyze data sets, interpret distributions, and evaluate experimental setups. Expect multi-step calculations and analytical thinking.',
            'time_limit_seconds'    => 1200,
            'base_xp'               => 1000,
            'order_index'           => 8,
        ]);

        $this->command->info("Seeding 50 university-level statistics questions...");

        $qaData = [

            // ── DESCRIPTIVE STATISTICS ───────────────────────────────────
            [
                'q' => 'Given the data set: 12, 15, 14, 10, 18, 16, 14, 13 — what is the mean? (Round to 2 decimal places)',
                'opts' => [
                    ['14.00', false],
                    ['14.25', true],
                    ['13.75', false],
                    ['15.00', false],
                ],
            ],
            [
                'q' => 'What is the median of: 7, 3, 9, 1, 5, 11, 4?',
                'opts' => [
                    ['4', false],
                    ['5', true],
                    ['7', false],
                    ['9', false],
                ],
            ],
            [
                'q' => 'A data set has values: 20, 22, 19, 25, 20, 18, 22, 20. What is the mode?',
                'opts' => [
                    ['22', false],
                    ['19', false],
                    ['20', true],
                    ['18', false],
                ],
            ],
            [
                'q' => 'Which measure of central tendency should be used when the data set contains extreme outliers?',
                'opts' => [
                    ['Mean', false],
                    ['Mode', false],
                    ['Median', true],
                    ['Range', false],
                ],
            ],
            [
                'q' => 'For the data: 5, 8, 8, 9, 10, 12 — what is the interquartile range (IQR)?
(Q1 = 8, Q3 = 10)',
                'opts' => [
                    ['4', false],
                    ['2', true],
                    ['7', false],
                    ['5', false],
                ],
            ],
            [
                'q' => 'The variance of a data set measures:',
                'opts' => [
                    ['The most frequent value', false],
                    ['The average squared deviation from the mean', true],
                    ['The difference between the largest and smallest values', false],
                    ['The sum of all values', false],
                ],
            ],
            [
                'q' => 'If the variance of a data set is 25, what is the standard deviation?',
                'opts' => [
                    ['25', false],
                    ['625', false],
                    ['5', true],
                    ['12.5', false],
                ],
            ],
            [
                'q' => 'A distribution where the tail extends to the RIGHT is called:',
                'opts' => [
                    ['Negatively skewed', false],
                    ['Positively skewed', true],
                    ['Symmetric', false],
                    ['Bimodal', false],
                ],
            ],

            // ── PROBABILITY ──────────────────────────────────────────────
            [
                'q' => 'Two dice are rolled. What is the probability that the sum equals 7?',
                'opts' => [
                    ['5/36', false],
                    ['6/36', true],
                    ['7/36', false],
                    ['1/6', false],
                ],
            ],
            [
                'q' => 'A card is drawn from a standard 52-card deck. What is the probability it is a KING or a HEART?',
                'opts' => [
                    ['16/52', true],
                    ['17/52', false],
                    ['4/52', false],
                    ['13/52', false],
                ],
            ],
            [
                'q' => 'Two events are MUTUALLY EXCLUSIVE if:',
                'opts' => [
                    ['They can both happen at the same time', false],
                    ['They cannot both happen at the same time', true],
                    ['They are independent of each other', false],
                    ['Their probabilities sum to more than 1', false],
                ],
            ],
            [
                'q' => 'If P(A) = 0.4 and P(B) = 0.3 and A and B are INDEPENDENT, what is P(A and B)?',
                'opts' => [
                    ['0.7', false],
                    ['0.1', false],
                    ['0.12', true],
                    ['0.58', false],
                ],
            ],
            [
                'q' => 'What is the complement of event A with P(A) = 0.65?',
                'opts' => [
                    ['0.65', false],
                    ['1.65', false],
                    ['0.35', true],
                    ['0.5', false],
                ],
            ],
            [
                'q' => 'Bayes\' Theorem is used to:',
                'opts' => [
                    ['Calculate the mean of a distribution', false],
                    ['Update probabilities given new evidence', true],
                    ['Compute the standard deviation', false],
                    ['Find the p-value of a test', false],
                ],
            ],
            [
                'q' => 'A bag has 5 red and 3 blue balls. You draw one, do NOT replace it, then draw another. What is P(both red)?',
                'opts' => [
                    ['25/64', false],
                    ['5/14', true],
                    ['10/64', false],
                    ['1/2', false],
                ],
            ],
            [
                'q' => 'In a normal distribution, approximately what percentage of data falls within ONE standard deviation of the mean?',
                'opts' => [
                    ['50%', false],
                    ['68%', true],
                    ['95%', false],
                    ['99.7%', false],
                ],
            ],

            // ── PROBABILITY DISTRIBUTIONS ─────────────────────────────────
            [
                'q' => 'The binomial distribution is used when:',
                'opts' => [
                    ['Events have more than two outcomes', false],
                    ['There are exactly two outcomes per trial and a fixed number of trials', true],
                    ['The variable is continuous', false],
                    ['The probability changes per trial', false],
                ],
            ],
            [
                'q' => 'In a Binomial distribution B(n=10, p=0.5), what is the expected value (mean)?',
                'opts' => [
                    ['2', false],
                    ['5', true],
                    ['0.5', false],
                    ['10', false],
                ],
            ],
            [
                'q' => 'The standard normal distribution has a mean of ___ and standard deviation of ___.',
                'opts' => [
                    ['1 and 0', false],
                    ['0 and 1', true],
                    ['0 and 0', false],
                    ['1 and 1', false],
                ],
            ],
            [
                'q' => 'A z-score of 2.0 means the data point is:',
                'opts' => [
                    ['2 units below the mean', false],
                    ['2 standard deviations above the mean', true],
                    ['Equal to the mean', false],
                    ['In the 2nd percentile', false],
                ],
            ],

            // ── SAMPLING & SURVEY DESIGN ─────────────────────────────────
            [
                'q' => 'Which sampling method ensures every individual has an equal probability of selection?',
                'opts' => [
                    ['Convenience sampling', false],
                    ['Purposive sampling', false],
                    ['Simple random sampling', true],
                    ['Quota sampling', false],
                ],
            ],
            [
                'q' => 'STRATIFIED sampling divides the population into:',
                'opts' => [
                    ['Random clusters', false],
                    ['Subgroups (strata) based on shared characteristics, then samples from each', true],
                    ['Only one large group', false],
                    ['Groups by location', false],
                ],
            ],
            [
                'q' => 'SAMPLING BIAS occurs when:',
                'opts' => [
                    ['The sample accurately represents the population', false],
                    ['The sample is too large', false],
                    ['Certain members of the population are systematically over- or under-represented', true],
                    ['The survey has too many questions', false],
                ],
            ],
            [
                'q' => 'As sample size increases, the standard error of the mean generally:',
                'opts' => [
                    ['Increases', false],
                    ['Stays the same', false],
                    ['Decreases', true],
                    ['Doubles', false],
                ],
            ],

            // ── HYPOTHESIS TESTING ───────────────────────────────────────
            [
                'q' => 'A Type I Error occurs when you:',
                'opts' => [
                    ['Fail to reject a false null hypothesis', false],
                    ['Reject a TRUE null hypothesis', true],
                    ['Accept the alternative hypothesis correctly', false],
                    ['Use the wrong test statistic', false],
                ],
            ],
            [
                'q' => 'A Type II Error occurs when you:',
                'opts' => [
                    ['Reject a true null hypothesis', false],
                    ['Fail to reject a FALSE null hypothesis', true],
                    ['Use a sample size that is too large', false],
                    ['Compute the wrong mean', false],
                ],
            ],
            [
                'q' => 'The significance level α = 0.05 represents:',
                'opts' => [
                    ['5% probability of a Type II error', false],
                    ['The probability of rejecting a true null hypothesis (Type I error rate)', true],
                    ['95% confidence that the null is false', false],
                    ['The power of the test', false],
                ],
            ],
            [
                'q' => 'A ONE-TAILED test is used when:',
                'opts' => [
                    ['We are testing if the mean is different in either direction', false],
                    ['We are testing a specific directional hypothesis (greater than or less than)', true],
                    ['We have two samples to compare', false],
                    ['The p-value is greater than 0.05', false],
                ],
            ],

            // ── ONE-SAMPLE & TWO-SAMPLE TESTS ─────────────────────────────
            [
                'q' => 'A one-sample t-test is used to:',
                'opts' => [
                    ['Compare means of two groups', false],
                    ['Test if a sample mean equals a known population mean', true],
                    ['Measure correlation between two variables', false],
                    ['Determine sample size', false],
                ],
            ],
            [
                'q' => 'An INDEPENDENT samples t-test requires:',
                'opts' => [
                    ['The same subjects measured twice', false],
                    ['Two separate, unrelated groups', true],
                    ['A population with known variance', false],
                    ['Three or more groups', false],
                ],
            ],
            [
                'q' => 'A PAIRED t-test is appropriate when:',
                'opts' => [
                    ['Two completely different groups are compared', false],
                    ['The same subjects are measured before and after a treatment', true],
                    ['The data is categorical', false],
                    ['There are more than two groups', false],
                ],
            ],
            [
                'q' => 'Which test is used to compare means across THREE or more groups?',
                'opts' => [
                    ['t-test', false],
                    ['Chi-square test', false],
                    ['ANOVA', true],
                    ['Correlation', false],
                ],
            ],

            // ── CHI-SQUARE & NON-PARAMETRIC ───────────────────────────────
            [
                'q' => 'A Chi-square test of independence tests whether:',
                'opts' => [
                    ['Two means are equal', false],
                    ['Two categorical variables are associated', true],
                    ['The data follows a normal distribution', false],
                    ['Variance is equal across groups', false],
                ],
            ],
            [
                'q' => 'Non-parametric tests are used when:',
                'opts' => [
                    ['The data is normally distributed', false],
                    ['The assumptions of parametric tests are not met', true],
                    ['The sample is very large', false],
                    ['There are exactly two groups', false],
                ],
            ],
            [
                'q' => 'The Mann-Whitney U test is a non-parametric alternative to:',
                'opts' => [
                    ['Paired t-test', false],
                    ['ANOVA', false],
                    ['Independent samples t-test', true],
                    ['Chi-square test', false],
                ],
            ],
            [
                'q' => 'Expected frequency in a Chi-square test is calculated as:',
                'opts' => [
                    ['Observed / Total', false],
                    ['(Row total × Column total) / Grand total', true],
                    ['Observed − Expected', false],
                    ['Standard deviation / Mean', false],
                ],
            ],

            // ── CORRELATION & REGRESSION ─────────────────────────────────
            [
                'q' => 'Pearson\'s r = -0.85 indicates:',
                'opts' => [
                    ['Weak positive correlation', false],
                    ['No correlation', false],
                    ['Strong negative correlation', true],
                    ['Perfect positive correlation', false],
                ],
            ],
            [
                'q' => 'In simple linear regression, the equation ŷ = a + bx represents:',
                'opts' => [
                    ['A probability formula', false],
                    ['A straight-line model predicting y from x', true],
                    ['A chi-square statistic', false],
                    ['A binomial model', false],
                ],
            ],
            [
                'q' => 'The coefficient of determination R² = 0.81 means:',
                'opts' => [
                    ['The correlation is 0.81', false],
                    ['81% of the variance in y is explained by x', true],
                    ['19% of data points are outliers', false],
                    ['The slope is 0.81', false],
                ],
            ],
            [
                'q' => 'In a regression line ŷ = 3 + 2x, if x = 5, what is ŷ?',
                'opts' => [
                    ['10', false],
                    ['13', true],
                    ['11', false],
                    ['15', false],
                ],
            ],

            // ── EXPERIMENTAL DESIGN ──────────────────────────────────────
            [
                'q' => 'RANDOMIZATION in an experiment is used to:',
                'opts' => [
                    ['Increase the sample size', false],
                    ['Reduce bias by distributing confounding variables evenly', true],
                    ['Guarantee a significant result', false],
                    ['Lower the standard deviation', false],
                ],
            ],
            [
                'q' => 'A CONFOUNDING variable is one that:',
                'opts' => [
                    ['Is deliberately manipulated by the researcher', false],
                    ['Affects the dependent variable and is related to the independent variable', true],
                    ['Has no effect on the experiment', false],
                    ['Is always controlled in a well-designed experiment', false],
                ],
            ],
            [
                'q' => 'DOUBLE-BLIND studies are designed to:',
                'opts' => [
                    ['Ensure neither the participants nor the researchers know who received the treatment', true],
                    ['Allow participants to know their group assignment', false],
                    ['Use two different dependent variables', false],
                    ['Test two independent variables simultaneously', false],
                ],
            ],
            [
                'q' => 'REPLICATION in experimental design means:',
                'opts' => [
                    ['Repeating the entire experiment multiple times or having multiple subjects per group', true],
                    ['Using a single test subject', false],
                    ['Removing outliers from data', false],
                    ['Only testing one variable', false],
                ],
            ],

            // ── EFFECT SIZE & POWER ───────────────────────────────────────
            [
                'q' => 'Effect size measures:',
                'opts' => [
                    ['Whether the result is statistically significant', false],
                    ['The practical magnitude of an observed effect', true],
                    ['The probability of a Type I error', false],
                    ['The number of groups in an experiment', false],
                ],
            ],
            [
                'q' => 'Cohen\'s d is used to measure effect size for:',
                'opts' => [
                    ['Categorical data', false],
                    ['The difference between two means', true],
                    ['Correlation coefficients', false],
                    ['Chi-square tests', false],
                ],
            ],
            [
                'q' => 'Statistical POWER is defined as:',
                'opts' => [
                    ['The probability of a Type I error', false],
                    ['The probability of correctly rejecting a false null hypothesis', true],
                    ['The size of the sample', false],
                    ['The significance level α', false],
                ],
            ],
            [
                'q' => 'To increase the power of a statistical test, you can:',
                'opts' => [
                    ['Decrease the sample size', false],
                    ['Increase the significance level α only', false],
                    ['Increase the sample size', true],
                    ['Use a two-tailed test instead of a one-tailed test', false],
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

        $this->command->info("✅ Done! 50 questions seeded for Module 8 — Statistical Methods & Experimental Design (University Student).");
    }
}